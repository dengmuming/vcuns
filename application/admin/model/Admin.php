<?php
namespace app\admin\model;
use think\Model;
/**
 * 后台模型
 */
class Admin extends Model{
	// 登陆方法
	public function login($data){
		// 保存用户账户密码
		$where = [
			'u_name'=>$data['username'],
			'u_pass'=>$data['password']
		];
		// 查询用户和密码是否正确
		$user = Admin::get($where);
		if (!$user) {
			$this->error='用户名不存在或密码错误';
			return FALSE;
		}
		// 保存用户登录状态
		$expire = 0;  // cookie有效时间
		if (isset($data['remember'])) {
			$expire = 3600*24*3;
		}
		cookie('admin_info',$user->toArray(),$expire);
	}
}