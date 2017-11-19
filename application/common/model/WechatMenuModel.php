<?php
namespace app\common\Model;
use think\Model;
use traits\model\SoftDelete;
class WechatMenuModel extends Model
{
	use SoftDelete;
    protected $deleteTime = 'delete_time';
	protected $table = "wechat_menu";

	//查询菜单
	public function find_wechat_menu($condition){
		$res = $this -> where($condition) -> find();
		return $res;
	}

	//保存微信菜单
	public function save_wechat_menu($data){
		$res = $this -> save($data);
		return $res;
	}

	//查找微信菜单
	public function select_menus($condition){
		$res = $this -> where($condition) -> select();
		return $res;
	}

	//统计个数
	public function count_menu($condition){
		$res = $this -> where($condition) -> count();
		return $res;
	}

	//删除菜单
	public function del_menu($condition){
		$res = $this -> where($condition) -> delete();
		return $res;
	}

	//更新菜单
	public function update_menu($condition,$data){
		$res = $this -> where($condition) -> update($data);
		return $res;
	}
}