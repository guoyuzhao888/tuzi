<?php
namespace app\admin\controller;
use app\admin\controller\BaseController;
use app\base\service\WechatMenuService;
class WechatMenuController extends BaseController{

	private static $wechat_menu_service;
	//用于实例化模型类
	public function _initialize(){
		self::$wechat_menu_service = new WechatMenuService;
	}
	//显示菜单列表
	public function menu_index(){
		
		return view();
	}

	//请求菜单数据
	public function select_menus(){
		$res = self::$wechat_menu_service -> select_menus();
		return $res;
	}

	//判断一级菜单是否达到上限
	public function count_one_menu(){
		$res = self::$wechat_menu_service -> count_menu(0,3);
		return json($res);
	}

	//判断二级菜单是否达到上限
	public function count_two_menu(){
		$wechat_menu_id = input("wechat_menu_id");
		$res = self::$wechat_menu_service -> count_menu($wechat_menu_id,5);
		return json($res);
	}

	//判断所有菜单是否达到上限
	public function count_all_menu(){
		$res = self::$wechat_menu_service -> count_menu($wechat_menu_id,5);
	}

	//添加一级菜单
	public function add_one_menu(){
		return view();
	}
	//添加二级菜单
	public function add_two_menu(){
		$res = self::$wechat_menu_service -> get_one_menu();
		$this -> assign('data',$res);
		return view();
	}

	//保存菜单
	public function save_menu(){
		$data = input();
		$res = self::$wechat_menu_service -> add_menu($data);
		return json($res);
	}

	//是否有满足条件的一级菜单
	public function is_has_one_menu(){
		$res = self::$wechat_menu_service -> get_one_menu();
		return json($res);
	}

	//删除菜单
	public function del_menu(){
		$data = input();
		$res = self::$wechat_menu_service -> del_menu($data);
		return json($res);
	}

	//更改菜单状态
	public function edit_status(){
		$data = input();
		$res = self::$wechat_menu_service -> edit_status($data);
		return json($res);
	}

	//生成菜单
	public function generate_menu(){
		$res = self::$wechat_menu_service -> generate_menu();
		return json($res);
	}
}
