<?php
namespace app\admin\model;
use think\Model;
/**
 * 文章模型
 */
class Article extends Model{
	/**
	 * 删除指定文章
	 * @param  array $where 提交的删除条件
	 * @return $this
	 */
	public function deleteBywhere($where){
		return $this->where($where)->delete();
	}
	// 获得所有格式化数据
	public function getTree(){
		// 获取所有分类信息
		$list = $this->all();
		// 调用公共函数对已有的数据进行格式化
		$data = get_tree($list);
		return $data;
	}
	// 统计分类文章数
	public function getArticleCate(){
		$result = Db::view('category','c_id')
			->view('article','c_id');
	}
	// 增加文章
	public function insert($data){
		$result = $this->allowField(true)->save($data);
		if ($result === FALSE) {
			return FALSE;
		}
		return TRUE;
	}
}