<?php
namespace app\common\service;

use think\Model;
use app\common\logic\WechatMenuLogic;
class WechatMenuService extends Model
{
	private static $wechat_menu_logic;
	//实例化
	public function __construct(){
		parent::__construct();
		self::$wechat_menu_logic = new WechatMenuLogic;
	}

	//添加菜单
	public function add_menu($data){
		$res = self::$wechat_menu_logic -> handel_menu($data);
		if($res){
			$result = [
				'code'		=> 1,
				'msg'		=> "添加菜单成功"
			];
		}else{
			$result = [
				'code'		=> 0,
				'msg'		=> "添加菜单失败"
			];
		}
		return $result;
	}
	//获取所有菜单
	public function select_menus(){
		$res = self::$wechat_menu_logic -> select_menus();
		$data['data']	= $res;
		$data['count']	= count($res);
		$data['code'] 	= 0;
		$data['msg']	= "";
		return json($data);
	}
	//获取一级菜单
	public function get_one_menu(){
		$res = self::$wechat_menu_logic -> get_one_menu();
		if($res){
			$result = [
				'code'		=> 1,
				'msg'		=> "ok",
				'data'		=> $res
			];
		}else{
			$result = [
				'code'		=> 0,
				'msg'		=> "请添加一级菜单",
			];
		}
		return $result;
	}
	//统计正常菜单的个数
	public function count_menu($wechat_menu_id,$menu_num){
		$res = self::$wechat_menu_logic -> count_menu($wechat_menu_id);
		if($res < $menu_num){
			$result = [
				'code'		=> 1,
				'msg'		=> "ok"
			];
		}else{
			$result = [
				'code'		=> 0,
				'msg'		=> "菜单个数已达上限"
			];
		}
		return $result;
	}

	//删除菜单
	public function del_menu($data){
		$res = self::$wechat_menu_logic -> del_menu($data);
		switch ($res) {
			case 1:
				$result = [
					'code'		=> 1,
					'msg'		=> "删除成功"
				];
				break;
			case 2:
				$result = [
					'code'		=> 2,
					'msg'		=> "该菜单下有子菜单"
				];
				break;
			default:
				$result = [
					'code'		=> 0,
					'msg'		=> "删除失败"
				];
				break;
		}
		return $result;
	}

	//更改菜单状态
	public function edit_status($data){
		$res = self::$wechat_menu_logic -> edit_status($data);
		return $res;
	}
}