<?php
namespace app\index\controller;
use think\Db;
use think\Request;
/**
 * 前台相册控制器
 */
class Photo extends Common{
	// 相册内容
	public function photo(){
		$model = model('Article');
		// $config = [
	 	// 	'type'      => 'page\Page',
	 	// 	'var_page'  => 'page',
		// 	'newstyle'  => true
		// ];
		// 按浏览查询所有文章数据(带分类) 模型层默认分页为10
		$article = $model->getArticleCatePage('','a_time',20);
		$this->assign('article',$article);
		$this->assign('nav','photo');
		// 获得分类
		$category = $model->getTree();
		$this->assign('category',$category);
		return $this->fetch();
	}
}