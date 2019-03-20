<?php
namespace app\admin\model;
use think\Model;
/**
 * 分类模型
 */
class Category extends Model{
	// 获得所有格式化后数据
	public function getTree(){
		// 获取所有分类信息
		$list = $this->all();
		// 调用公共函数对已有的数据进行格式化
		$data = get_tree($list);
		return $data;
	}
	// 删除分类
	public function remove($c_id){
		if (db('category')->where('c_pid',$c_id)->find()) {
			$this->error = '存在子分类不能删除';
			return FALSE;
		}
		if (db('article')->where('c_id',$c_id)->find()) {
			$this->error = '分类存在文章不能删除';
			return FALSE;
		}
		return db('category')->delete($c_id);
	}
}