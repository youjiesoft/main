<?php

class MisHrEvaluationCentreAction extends CommonAction {
	public function _filter(&$map){
		$map['status'] =1;
	}
	public function _before_edit(){
		$model=M("mis_hr_evaluation_target");
		$list =$model->field("id,name")->select();
		$this->assign("targetidlist",$list);
		$model=M("mis_hr_evaluation_content");
		$list =$model->field("id,name")->select();
		$this->assign("contentidlist",$list);
	}
	public function _before_add(){
		$model=M("mis_hr_evaluation_target");
		$list =$model->field("id,name")->select();
		$this->assign("targetidlist",$list);
		$model=M("mis_hr_evaluation_content");
		$list =$model->field("id,name")->select();
		$this->assign("contentidlist",$list);
	}
}
?>