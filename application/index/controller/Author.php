<?php
namespace app\index\controller;
use think\Db;
use think\Request;
/**
 * 前台作者控制器
 */
class Author extends Common{
	// 版块内容
	public function author(Request $request){
		$model = model('Article');
		if ($request->isGet()) {
			$data = input();
			// 将接收的作者名称存到条件中
			if (isset($data['a_author'])) {
				$where['article.a_author'] = $data['a_author'];
			}
		}
		// 按创建时间查询所有文章数据(带分类) 模型层默认分页为10
		$article = $model->getArticleCatePage($where);
		$this->assign('article',$article);

		// 按浏览数查询前五篇文章
		$order = 'a_click';
		$articleHot = $model->getArticleHot($where,$order);
		$this->assign('articleHot',$articleHot);
		
		// 特别推荐 按指定字段查询三篇文章
		$recommend["article.a_id"]=['in','60,61,62'];
		$articleRecommend = $model->getArticleHot($recommend);
		$this->assign('articleRecommend',$articleRecommend);
		
		// 获得分类
		$category = $model->getTree();
		$this->assign('category',$category);

		// 回显导航标记
		$this->assign('nav','article');
		return $this->fetch('section/section');
	}
}