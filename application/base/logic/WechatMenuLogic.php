<?php
namespace app\base\logic;
use think\Model;
use think\Session;
use think\captcha\Captcha;
use app\base\model\WechatMenuModel;
use EasyWeChat\Foundation\Application;
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
	/**
	 * 生成菜单
	 * 查找一级菜单
	 * 查找二级菜单
	 * 拼装数组
	 * 生成菜单
	 */
	public function generate_menu(){
		$one_menus = $this -> one_menu();
		$two_menus = $this -> two_menu();
		$new_menus = $this -> generate_menu_data($one_menus,$two_menus);
		$this -> generate_wechat_menu($new_menus);
	}

	//生成菜单
	private function generate_wechat_menu($data){
		dump($data);
		$menu = app()->menu;
		$buttons = [
		    [
		        "type" => "click",
		        "name" => "今日歌曲",
		        "key"  => "V1001_TODAY_MUSIC"
		    ],
		    [
		        "name"       => "菜单",
		        "sub_button" => [
		            [
		                "type" => "view",
		                "name" => "搜索",
		                "url"  => "http://www.soso.com/"
		            ],
		            [
		                "type" => "view",
		                "name" => "视频",
		                "url"  => "http://v.qq.com/"
		            ],
		            [
		                "type" => "click",
		                "name" => "赞一下我们",
		                "key" => "V1001_GOOD"
		            ],
		        ],
		    ],
		];
		$res = $menu -> add($buttons);
		dump($res);die;
	}

	//获取所有一级菜单
	private function one_menu(){
		$condition = [
			'status'			=> 1,
			'wechat_menu_fid'	=> 0,
		];
		$one_menus = self::$wechat_menu_model -> select_menus($condition);
		return $one_menus;
	}
	//获取所有二级菜单
	private function two_menu(){
		$condition = [
			'status'			=> 1,
			'wechat_menu_fid'	=> [
				'<>',0
			],
		];
		$two_menus = self::$wechat_menu_model -> select_menus($condition);
		return $two_menus;
	}

	//拼装菜单数据
	private function generate_menu_data($one_menus,$two_menus){
		$new_one_menus 	= [];
		$new_two_menus 	= [];
		$new_menus		= [];
		foreach ($one_menus as $one_menu => $one_menu_value) {
			$one_menu_data = $this -> handle_menu_data($one_menu_value);
			$new_one_menus = $one_menu_data['data'];
			if($one_menu_data['code'] == 0){
				array_push($new_menus,$new_one_menus);
				continue;
			}
			foreach ($two_menus as $two_menu => $two_menu_value) {
				if($two_menu_value['wechat_menu_fid'] == $one_menu_value['wechat_menu_id']){
					$two_menu_data = $this -> handle_menu_data($two_menu_value);
					array_push($new_two_menus,$two_menu_data['data']);
				}
			}
			array_push($new_one_menus['sub_button'],$new_two_menus);
			array_push($new_menus, $new_one_menus);
		}
		return $new_menus;
	}

	//过滤数据
	private function handle_menu_data($data){
		$result = [];
		if($data['wechat_menu_type'] == 1){
			$result['data'] = [
				'name'	=> $data['wechat_menu_name'],
				'type'	=> "click",
				'key'	=> $data['wechat_menu_key']
			];
			$result['code'] = 0;
		}elseif($data['wechat_menu_type'] == 2){
			$result['data'] = [
				'name'	=> $data['wechat_menu_name'],
				'type'	=> "view",
				'url'	=> $data['wechat_menu_url']
			];
			$result['code'] = 0;
		}else{
			$result['data'] = [
				'name'			=> $data['wechat_menu_name'],
				'sub_button'	=> []
			];
			$result['code'] = 1;
		}
		return $result;
	}
	
}
