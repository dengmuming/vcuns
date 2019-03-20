<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;
/**
 * 后台登录控制器
 * 
 */
class Login extends Controller{
	/**
	 * 生成验证码图片
	 * @param array 	$config 	验证码配置
	 * @param object 	$obj  		获得Captcha对象
	 */
	public function captcha(){
		$config = [
			'length'=>4,
			'codeSet'=>'1234567890',
			'fontSize'=>26
		];
		$obj = new \think\captcha\Captcha($config);
		return $obj->entry();
	}
	/**
	 * 用户登录操作
	 * @param array 	$config 	验证码配置
	 * @param object 	$obj  		获得Captcha对象
	 */
	public function index(Request $request){
		if ($request->isGet()) {
			return $this->fetch();
		}
		// 获取提交的用户数据
		$data = input();
		// 验证码校对
		$obj = new \think\captcha\Captcha();
		if (!$obj->check($data['captcha'])) {
			$this->error('验证码不正确');
		}
		$model = model('Admin');
		$result = $model->login($data);
		if ($result === FALSE) {
			$this->error($model->getError());
		}
		$this->success('完成登陆', 'index/index');
	}
	/**
	 * 用户退出操作
	 * 
	 */
	public function logout(){
		cookie('admin_info', NULL);
		$this->success('退出成功', 'login/index');
	}
}
