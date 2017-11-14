<?php
namespace app\common\Model;

use think\Model;
class AdminModel extends Model
{
	protected $table = "admin";

	//查询管理员
	public function find_admin($condition){
		$res = $this -> where($condition) -> find();
		return $res;
	}
}