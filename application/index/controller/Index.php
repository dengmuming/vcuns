<?php
namespace app\index\controller;
use think\Db;
use think\Request;
/**
 * 前台首页控制器
 */
class Index extends Common{
	// 首页内容
	public function index(Request $request){
		$model = model('Article');
		// 初始化查询条件名
		$where = [];

		// 按创建时间查询所有文章数据(带分类) 模型层默认分页为10
		$article = $model->getArticleCatePage($where);
		$this->assign('article',$article);

		// 热门排行 按浏览数查询前五篇文章
		$order = 'a_click';
		$articleHot = $model->getArticleHot($where,$order);
		$this->assign('articleHot',$articleHot);
		
		// 幻灯片 按指定字段查询五篇文章
		$view["a_id"]=['in','1,2,3,4,5'];
		$articleView = $model->getArticleHot($view);
		$this->assign('articleView',$articleView);

		// 特别推荐 按指定字段查询三篇文章
		$recommend["a_id"]=['in','6,7,8'];
		$articleRecommend = $model->getArticleHot($recommend);
		$this->assign('articleRecommend',$articleRecommend);

		// 获得分类
		$category = $model->getTree();
		$this->assign('category',$category);
   
		// 回显导航标记
		$this->assign('nav','index');
		return $this->fetch('index');
	}
}

?>