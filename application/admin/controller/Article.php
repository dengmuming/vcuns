<?php
namespace app\admin\controller;
use think\Request;
use think\Db;
/**
 * Class Article 后台文章控制器
 * 
 */
class Article extends Common{
	/**
	 * 后台文章首页/列表		返回视图
	 * @param object 	$obj  		获得Query对象
	 * @param array		$news  		查询的所有文章信息
	 * @return string
	 */
	public function index(){
		$model = model('article');
		// 统计文章总数
		$count = count(model('article')->all());
		$this->assign('count', $count);
		// 查询文章数据(带分类)
		$article = Db::view('article','a_id,a_title,a_desc,a_content,a_author,a_time,a_click,a_like,a_comment,c_id,a_img,a_thumber,a_water,a_keyword')
		->view('category','c_name','category.c_id=article.c_id')
		->select();
		$this->assign('article', $article);
		return $this->fetch();
	}
	// 文章删除
	public function delete(Request $request){
		if ($request->isGet()) {
			// 接收数据
			$a_id = input('a_id');
			// 组装查询条件
			$where = ['a_id'=>$a_id];
			// 获取模型
			$model = model('article');

			// 获取封面图片的绝对地址
			$imgLoad = $_SERVER['DOCUMENT_ROOT']."/".$model->get($where)->a_img;
			// 获取缩略图的绝对地址
			$thumberLoad = $_SERVER['DOCUMENT_ROOT']."/".$model->get($where)->a_thumber;

			// 获取富文本编辑器上传的图片
			$contentStr = $model->get($where)->a_content;
			// 匹配img标签的正则表达式
			$preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';
			// 匹配所有的img
			preg_match_all($preg, $contentStr, $contentImg);

			// 判断删除文章结果
			if ($model->deleteBywhere($where)) {
				// 删除本地封面图片
				unlink($imgLoad);
				// 删除本地缩略图
				unlink($thumberLoad);
				// 删除富文本编辑器上传的图片
				foreach ($contentImg['1'] as $value) {
					// 删除每一个富文本编辑器图片
					unlink($_SERVER['DOCUMENT_ROOT'].$value);
				}
				$this->success('成功删除一篇文章','index');
			}
		}
		$this->error('删除失败');
	}
	// 文章增加
	public function add(Request $request){
		$model = model('article');
		if ($request->isPost()) {
			$data = input();
			// 实现商品图片上传
			$this->uploadArticleThumb($data);
			// 文章时间
			$data['a_time'] = time();
			if ($model->insert($data)) {
				$this->success('增加成功','index');
			}
			$this->error('增加失败', 'index');
		}
		// 获得文章分类
		$category = model('category')->getTree();
		$this->assign('category', $category);
		return $this->fetch();
	}
	// 文章封面图片上传
	protected function uploadArticleThumb(&$data){
		// 1.获取file类对象
		$file = request()->file('a_img');
		if (!$file) {
			// 没有上传文件
			$this->error('文件必须上传');
		}
		// 2.使用file对象调用move方法实现文件的上传
		$upload_base = config('upload_base');
		$info = $file->validate(['ext'=>'jpg,png'])->move($upload_base);
		if (!$info) {
			// 文件上传有错误
			$this->error($file->getError());
		}
		// 3.提取上传之后保存的文件地址
		$a_img = $upload_base.'/'.$info->getSaveName();
		// 4.更换地址中的\为/
		$data['a_img'] = str_replace('\\', '/', $a_img);
		// 在PHP代码使用图片时当使用相对地址格式顶头不要有/
		// 当在浏览器使用文件地址时 /使用表示为域名 如果省略 会按照当前地址来请求
		// 根据上次的图片生成缩略图
		// 打开图片 获取对象
		$img = \think\Image::open($data['a_img']);
		// 计算缩略图保存地址
		// 保存地址与原图存储在同一个目录文件名称 并在原图基础删增加thumber_的前缀
		$data['a_thumber'] = $upload_base.'/'.date('Ymd').'/thumber_'.$info->getFileName();
		// 生成缩略图
		$img->thumb(200,200)->save($data['a_thumber']);
	}
	// 文章编辑
	public function edit(Request $request){
		$model = model('article');
		if ($request->isGet()) {
			$a_id = input('a_id');
			// 获得文章信息
			$article = $model->get($a_id);
			$this->assign('article', $article);
			// 获得分类
			$category = model('category')->getTree();
			$this->assign('category', $category);
			return $this->fetch();
		}
		if ($request->isPost()) {
			$data = input();
			// 重新上传封面图片则删除原有图片
			if (request()->file('a_img')) {
				// 获取原图片和原缩略图绝对地址
				$imgLoad = $model->get($data['a_id'])->a_img;
				$thumberLoad = $model->get($data['a_id'])->a_thumber;
				// 实现新图片上传
				$this->uploadArticleThumb($data);
				// 删除原图片
				if ($imgLoad) {
					unlink($_SERVER['DOCUMENT_ROOT']."/".$imgLoad);
				}
				// 删除原缩略图
				if ($thumberLoad) {
					unlink($_SERVER['DOCUMENT_ROOT']."/".$thumberLoad);
				}
			}

			// 获取更新前内容
			$contentStrOld = $model->get($data['a_id'])->a_content;
			// 获取更新后内容
			$contentStrNew = $data['a_content'];
			// 匹配img标签的正则表达式
			$preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';
			// 匹配所有的更新前内容img
			preg_match_all($preg, $contentStrOld, $allImgOld);
			// 匹配所有的更新后内容img
			preg_match_all($preg, $contentStrNew, $allImgNew);
			// 新内容有图片时删除没用的图片
			if ($allImgNew['1'] == TRUE) {
				for ($i=0; $i < count($allImgOld['1']); $i++) { 
					if (!in_array($allImgOld['1'][$i], $allImgNew['1'])) {
						unlink($_SERVER['DOCUMENT_ROOT'].$allImgOld['1'][$i]);
					}
				}
			}else{
				// 新内容没有图片时删除本地图片
				foreach ($allImgOld['1'] as $value) {
					// 删除每一个富文本编辑器图片
					unlink($_SERVER['DOCUMENT_ROOT'].$value);
				}
			}
			// 判断更新结果
			if ($model->isUpdate(true)->allowField(true)->save($data,['a_id'=>$data['a_id']])) {
				$this->success('成功', 'index');
			}elseif ($contentStrNew != $contentStrOld) {
				$this->success('成功', 'index');
			}
		}
		$this->error('编辑失败', 'index');
	}
}