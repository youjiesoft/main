<?php
/**
 * @Title: file_name
 * @Package package_name
 * @Description: todo(评估指标具体内容)
 * @author liminggang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-3-27 上午11:14:31
 * @version V1.0
 */
class MisHrEvaluationContentAction extends CommonAction {
	/**
	 * @Title: _filter 
	 * @Description: todo(构造检索条件) 
	 * @param unknown_type $map  
	 * @author xiafengqin 
	 * @date 2013-6-1 下午3:27:20 
	 * @throws
	 */
	public function _filter(&$map){
		$map['status'] =1;
	}
	/**
	 * @Title: _before_edit 
	 * @Description: todo(打开修改页面前置函数)   
	 * @author xiafengqin 
	 * @date 2013-6-1 下午3:27:37 
	 * @throws
	 */
	public function _before_edit(){
		$model=M("mis_hr_evaluation_target");
		$list =$model->field("id,name")->select();
		$this->assign("targetlist",$list);
	}
	/**
	 * @Title: _before_add 
	 * @Description: todo(打开新增页面前置函数)   
	 * @author xiafengqin 
	 * @date 2013-6-1 下午3:27:56 
	 * @throws
	 */
	public function _before_add(){
		$model=M("mis_hr_evaluation_target");
		$list =$model->field("id,name")->select();
		$this->assign("targetlist",$list);
	}
}
?>