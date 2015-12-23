<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(自定义标签控制器) 
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-6-1 下午2:05:47 
 * @version V1.0
 */
class TagsAction extends CommonAction {
	/**
	 * (non-PHPdoc)
	 * 首页列表展示
	 * @see CommonAction::index()
	 */
	public function index(){
		$this->assign('tags', tags());
		$this->display();
	}
	/**
	 * (non-PHPdoc)
	 * 修改方法
	 * @see CommonAction::update()
	 */
	public function update(){
		unset($_POST['__hash__']);
		if(tags($_POST) > 0){
			$this->success(L('_SUCCESS_'));
		}else{
			$this->error(L('_ERROR_'));
		}
	}
}
?>