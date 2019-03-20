<?php
namespace app\index\model;
use think\Model;
use think\Db;
/**
 * 文章模型
 */
class Article extends Model{
	// 按时间 并查询所有文章(带分类名)
	public function getArticleCate($where=null){
		// 按创建时间查询文章数据(带分类)
		return Db::view('article','*')
			->view('category','c_name','category.c_id=article.c_id')
			->where($where)
			->order("a_time desc")
			->select();
	}
	// 按时间 并查询所有文章(带分类名) 分页默认为10
	public function getArticleCatePage($where=null,$order='a_time',$page=10,$if=false,$config=[]){
 		//查询数据
   		$list = Db::view('article','*')
   			->view('category','c_name','category.c_id=article.c_id')
   			->where($where)
   			->order("$order desc")
   			->paginate($page,$if,$config)
   			->each(function($item, $key){
		        // 获取id
		        $id = $item["a_id"]; 
		        // 提取文章内容三张图片
				// 匹配img标签的正则表达式
				$preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';
				// 获取文章内容
				preg_match_all($preg, $item['a_content'], $img);
				// 判断文章内容照片是否超过三张
				// 初始化存储照片路径的变量
				$contentImg = []; 
				if (count($img['1'])>=3) {
					foreach ($img['1'] as $i => $value) {
						$contentImg[] = $value;
					}
				}
		        //给数据集追加字段contentImg并赋值
		        $item['contentImg'] = $contentImg;
		        return $item;
		});
   		return $list;
		// 按创建时间查询文章数据(带分类)
		// return Db::view('article','*')
		// 	->view('category','c_name','category.c_id=article.c_id')
		// 	->where($where)
		// 	->order("a_time desc")
		// 	->paginate($page);
	}
	// 按浏览 并查询五篇文章(带分类名)
	public function getArticleHot($where=null,$order='a_time',$start=0,$number=5){
		return Db::view('article','*')
			->view('category','c_name','category.c_id=article.c_id')
			->where($where)
			->order("$order desc")
			->limit($start,$number)
			->select();
	}
	// 查询一篇文章(带分类名)
	public function getArticleOne($where){
		return Db::view('article','*')
			->view('category','c_name','category.c_id=article.c_id')
			->where($where)
			->find();
	}
	// 指定字段增加1,如浏览量
	public function addClick($where,$data,$number=1){
		return Db::name('article')->where($where)->setInc($data,$number);
	}
	// 获得所有格式化后数据
	public function getTree(){
		// 获取所有分类信息
		$list = Db::name('category')->select();
		// 调用公共函数对已有的数据进行格式化
		$data = get_tree($list);
		return $data;
	}
}