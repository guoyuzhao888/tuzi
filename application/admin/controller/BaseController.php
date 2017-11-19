<?php
namespace app\admin\controller;
use think\Controller;
use think\Session;
class BaseController extends Controller{

	/*
	*用于验证是否登录
	*/
	public function _initialize(){
		if(!Session::has('admin_id')){
			$this->redirect('/admin_login');
		}
	}
}