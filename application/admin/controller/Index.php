<?php
namespace app\admin\controller;

/**
 * Class Index 后台首页控制器
 * 
 */
class Index extends Common{
	/**
	 * 后台文章首页		返回视图
	 * @param object 	$obj  		获得Query对象
	 * @param array		$news  		查询的所有文章信息
	 * @return string
	 */
	public function index(){
		return view();
		//return $this->fetch();
	}
	public function welcome(){
		return view();
	}
}
