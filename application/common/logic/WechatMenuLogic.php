<?php
namespace app\common\logic;
use think\Model;
use think\Session;
use think\captcha\Captcha;
use app\common\model\WechatMenuModel;
class WechatMenuLogic extends Model
{
	private static $wechat_menu_model;
	//实例化
	public function __construct(){
		parent::__construct();
		self::$wechat_menu_model = new WechatMenuModel;
	}

	//处理添加菜单
	public function handel_menu($data){
		if(!isset($data['is_one_menu'])){
			unset($data['wechat_menu_type']);
		}else{
			unset($data['is_one_menu']);
		}
		$res = self::$wechat_menu_model -> save_wechat_menu($data);
		return $res;
	}

	//获取菜单
	public function get_one_menu(){
		$condition = [
			'wechat_menu_fid'	=> 0,
			'wechat_menu_type'	=> 0,
		];
		$res = self::$wechat_menu_model -> select_menus($condition);
		if($res){
			foreach ($res as $key => $value) {
				$res[$key]['two_menu_num'] = $this -> count_menu($value['wechat_menu_id']);
			}
		}
		return $res;
	}

	//统计菜单
	public function count_menu($wechat_menu_fid){
		$condition = [
			'wechat_menu_fid'	=> $wechat_menu_fid
		];
		$res = self::$wechat_menu_model -> count_menu($condition);
		return $res;
	}

	//查询所有菜单
	public function select_menus(){
		$new_menus = [];
		$one_menu_condition['wechat_menu_fid'] = 0;
		$one_menu = self::$wechat_menu_model -> select_menus($one_menu_condition);
		foreach ($one_menu as $key => $value) {
			array_push($new_menus,$value);
			$two_menu_condition['wechat_menu_fid'] = $value['wechat_menu_id'];
			$two_menu = self::$wechat_menu_model -> select_menus($two_menu_condition);
			if($two_menu){
				foreach ($two_menu as $key1 => $value1) {
					array_push($new_menus,$value1);
				}
			}
		}
		return $new_menus;
	}

	/**删除菜单
	 * 看看是否为一级菜单
	 * ---YES
	 * 		查找是否有子菜单
	 * ---NO
	 * 		直接删除
	 * @DateTime  2017-11-19
	 */
	public function del_menu($data){
		$condition['wechat_menu_id'] = $data['wechat_menu_id'];
		if($data['wechat_menu_fid'] == 0 && $data['wechat_menu_type'] == 0){
			$fid_condition['wechat_menu_fid'] = $data['wechat_menu_id'];
			$wechat_menu = self::$wechat_menu_model -> find_wechat_menu($fid_condition);
			if($wechat_menu){
				return 2;//该菜单下有子菜单
			}
		}
		$del_menu = self::$wechat_menu_model -> del_menu($condition);
		if($del_menu){
			return 1;
		}
		return 0;
	}

	/**
	 * 更改菜单状态
	 */
	public function edit_status($data){
		$condition['wechat_menu_id'] = $data['wechat_menu_id'];
		$menu_data['status'] = $data['status'];
		$res = self::$wechat_menu_model -> update_menu($condition,$menu_data);
		return $res;
	}
}
