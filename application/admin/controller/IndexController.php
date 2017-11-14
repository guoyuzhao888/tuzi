<?php
namespace app\admin\controller;
use app\admin\controller\BaseController;

class IndexController extends BaseController{

	//显示主页面
	public function index(){
		return view();
	}

	//home
	public function home(){
		echo 'home';
	}
}
