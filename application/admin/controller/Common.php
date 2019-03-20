<?php
namespace app\admin\controller;
use think\Controller;
/**
 * 后台公共控制器
 * 
 */
class Common extends Controller{
	/**
	 * 后台文章首页		返回视图
	 * @param object 	$obj  		获得Query对象
	 * @param array		$news  		查询的所有文章信息
	 * @return string
	 */
	public function __construct(){
		// 执行父类构造方法
		parent::__construct();
		// 判断用户是否登陆
		if (!cookie('admin_info')) {
			$this->error('需要先登陆', 'login/index');
		}
		// token令牌的检查
		if (config('is_check_token')) {
			// 当为get请求需要生产token令牌 所以不检查
			if (request()->isPost()) {
				// 获取session中的token值
				$session_token = session('__token__');
				// 获取表单所提交的token值
				$token = input('__token__');
				if (!$session_token || !$token || $session_token != $token) {
					$this->error('令牌错误');
				}
				// 说明token值正确
				// 销毁session中token值 token值只能使用一次
				session('__token__', null);
			}
		}
	}
}
