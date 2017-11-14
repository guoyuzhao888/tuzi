<?php
namespace app\admin\controller;
use think\Controller;
use app\common\service\LoginService;
class LoginController extends Controller{
	private static $login_service;
	//用于实例化模型类
	protected function _initialize(){
		self::$login_service = new LoginService;
	}
	/**
	 * 显示登陆页面
	 */
	public function login(){
		//判断是否为表单传过来的数据
		if (request()->isPost()) {
			$data = input();
			$res = self::$login_service -> check_login($data);//验证登陆
			return json($res);
		}
		return view();
	}
}