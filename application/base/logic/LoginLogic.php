<?php
namespace app\base\logic;
use think\Model;
use think\Session;
use think\captcha\Captcha;
use app\base\model\AdminModel;
class LoginLogic extends Model
{
	private static $admin_model;
	//实例化
	public function __construct(){
		parent::__construct();
		self::$admin_model = new AdminModel;
	}
	/**
	 * 登陆验证业务逻辑
	 * 验证码验证
	 * 查找管理员
	 * 保存session
	 * @DateTime  2017-11-14
	 */
	public function handle_login($data){
		$verify = $this -> verify_code($data['captcha']);//验证码输入是否正确
		if(!$verify){
			return -1;
		}
		$condition = [//拼装条件
			'user_name'	=> $data['user_name'],
			'password'	=> $data['password'],
		];
		$admin = self::$admin_model -> find_admin($condition);//查找此用户
		if($admin){
			$this -> save_session($admin['user_name'],$admin['admin_id']);//保存session
			return 1;
		}
		return 0;
	}

	/**
	 *用来保存session
	 */
	public function save_session($admin_name,$admin_id){
		Session::set('admin_name',$admin_name);
		Session::set('admin_id',$admin_id);
		return;
	}
	/**
	 * 验证码检测
	 * @param  string $code [验证码]
	 * @return [boolean]       [成功:true,失败:false]
	 */
	private function verify_code($code='')
	{
	    $captcha = new Captcha();
	    return $captcha->check($code);
	}
}
