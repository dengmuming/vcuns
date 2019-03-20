<?php
namespace app\admin\controller;
use think\Request;

/**
 * Class Category 分类控制器
 * 
 */
class Category extends Common{
	/**
	 * 分类首页/增加		返回视图
	 * @param object 	$obj  		获得Query对象
	 * @param array		$news  		查询的所有文章信息
	 * @return string
	 */
	public function index(Request $request){
		$category = model('category')->getTree();
		$this->assign('category', $category);
		// 统计分类总数
		$count = count(model('category')->all());
		$this->assign('count', $count);
		return $this->fetch();
	}
	// 分类添加
	public function add(Request $request){
		if ($request->isPost()) {
			// 提交的分类更新数据
			$data = $request->post();
			if ($data['c_name'] == NULL) {
				$this->error('分类名称不能为空', 'index');
			}
			if (model('category')->isUpdate(false)->allowField(true)->save($data)) {
				$this->success('增加分类成功', 'index');
			}
			$this->error('增加分类失败', 'index');
		}
		return $this->fetch();
	}
	// 分类删除
	public function delete(Request $request){
		if ($request->isGet()) {
			$c_id = input('c_id');
			$model = model('category');
			$result = $model->remove($c_id);
			if ($result === FALSE) {
				$this->error($model->getError());
			}
			$this->success('OK');
		}
	}
	// 分类编辑
	public function edit(Request $request){
		$model = model('category');
		if ($request->isGet()) {
			$c_id = input('c_id');
			// 获得编辑分类名
			$result = $model->get($c_id);
			$this->assign('result', $result);
			// 获得分类
			$category = $model->getTree();
			$this->assign('category', $category);
			return $this->fetch();
		}
		// 接收更新的数据
		if ($request->isPost()) {
			// 存储更新数据
			$data = input();
			// 判断更新结果
			if ($model->isUpdate(true)->allowField(true)->save($data,['c_id'=>$data['c_id']])) {
				$this->success('编辑成功', 'index');
			}
			$this->error('编辑失败', 'index');
		}
	}
}