<?php
if (!function_exists('get_tree')) {
	/**
	 * 对数据格式化
	 * @param  [type]  $data 被格式化的数据
	 * @param  integer $id   要查找的分类id 0表示查询所有分类
	 * @param  integer $lev  标注分类的层次
	 * @return [type]        [description]
	 */
	function get_tree($data,$id=0,$lev=0){
		// 保存最终的数据
		static $list = [];
		foreach ($data as $value) {
			if ($value['c_pid'] == $id) {
				// 标识分类的层次
				$value['lev'] = $lev;
				$list[] = $value;
				get_tree($data,$value['c_id'],$lev+1);
			}
		}
		return $list;
	}
}