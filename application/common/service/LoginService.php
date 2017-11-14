<?php
namespace app\common\service;

use think\Model;
use app\common\logic\LoginLogic;
class LoginService extends Model
{
	private static $login_logic;
	//实例化
	public function __construct(){
		parent::__construct();
		self::$login_logic = new LoginLogic;
	}

	//登陆验证接口
	public function check_login($data){
		$handle_result = self::$login_logic -> handle_login($data);
		return $handle_result;
	}
}