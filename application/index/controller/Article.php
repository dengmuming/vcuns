<?php
namespace app\index\controller;
use think\Db;
/**
 * 前台文章控制器
 */
class Article extends Common{
	// 文章内容
	public function article(){
		$a_id = input('a_id');
		$where = ['a_id'=>$a_id];
		$model = model('Article');
		// 浏览量增加1
		$model->addClick($where,'a_click');
		
		// 按指定id查询一篇文章数据(带分类)
		$articleOne = $model->getArticleOne($where);
		
		// 上一篇文章
		$where['a_id'] = ['<',$a_id];
		$articleBefore = $model->getArticleOne($where);
		if (!$articleBefore) {
			$this->assign('articleBefore',$articleBefore);
		}
		$this->assign('articleBefore',$articleBefore);
		// 下一篇文章
		$where['a_id'] = ['>',$a_id];
		$articleAfter = $model->getArticleOne($where);
		if (!$articleAfter) {
			$this->assign('articleAfter',$articleAfter);
		}
		$this->assign('articleAfter',$articleAfter);

		// 关键词分割
		$articleOne['a_keyword'] = explode(",",$articleOne['a_keyword']);
		$this->assign('articleOne',$articleOne);
		
		// 按浏览数查询前五篇文章
		$articleHot = $model->getArticleHot();
		$this->assign('articleHot',$articleHot);
		
		// 特别推荐 按指定字段查询三篇文章
		$recommend["article.a_id"]=['in','6,7,8'];
		$articleRecommend = $model->getArticleHot($recommend);
		$this->assign('articleRecommend',$articleRecommend);
		
		// 获得分类
		$category = $model->getTree();
		$this->assign('category',$category);
		
		// 回显导航标记
		$this->assign('nav','atircle');
		return $this->fetch();
	}
	public function addLike(){
		$a_id = input('a_id');
		$where = ['a_id'=>$a_id];
		$model = model('Article');
		// 浏览量增加1
		$model->addClick($where,'a_like');
		$this->success('点赞成功');
		return $this->redirect('article', $where);
	}
}

?>