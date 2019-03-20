<?php
namespace app\index\controller;
use think\Db;
use think\Request;
/**
 * 前台版块控制器
 */
class Section extends Common{
	// 版块内容
	public function section(Request $request){
		// 初始化查询条件
		$where = [];
		$model = model('Article');
		if ($request->isGet()) {
			$c_id = input('c_id');
			$c_pid = Db::name('category')->where('c_id',$c_id)->field('c_pid')->find();
			if ($c_pid['c_pid'] == 0) {
				$nav = $c_id;
				$c_ids = Db::name('category')->where('c_pid',$c_id)->field('c_id')->select();
				foreach ($c_ids as $key => $value) {
					$where[$key] = $value['c_id'];
				}
				// 数组第一个元素追加顶级分类id
				array_unshift($where, $c_id);
				// 按','分割字符串
				$where = implode(',', $where);
				// 组装查询条件
				$where = [
					'article.c_id'=>['in',$where]
				];
			}else{
				$where['article.c_id'] = $c_id;
				$nav = Db::name('category')->where('c_id',$c_pid['c_pid'])->field('c_id')->find();
				$nav = $nav['c_id'];
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
		$recommend["article.a_id"]=['in','6,7,8'];
		$articleRecommend = $model->getArticleHot($recommend);
		$this->assign('articleRecommend',$articleRecommend);
		
		// 获得分类
		$category = $model->getTree();
		$this->assign('category',$category);

		// 回显导航标记
		$this->assign('nav',$nav);
		return $this->fetch('section/section');
	}
}