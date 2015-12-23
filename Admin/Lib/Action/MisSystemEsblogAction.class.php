<?php
class MisSystemEsblogAction extends Action {
	function main(){
		
		$name=$this->getActionName();
		$model=D($name);
		$sign=json_decode(html_entity_decode($_POST['returndata']),true);
		$data['serverurl']=$_POST['serverurl'];
		$data['serverjob']=$_POST['serverjob'];
		$data['dbsource']=$_POST['dbsource'];
		$data['proname']=$_POST['proname'];
		$data['param']=$_POST['param'];
		$data['returndata']=$_POST['returndata'];
		$data['createtime']=time();
		$data['createid']=$_SESSION[C('USER_AUTH_KEY')];
		//$model->startTrans();
		$result=$model->add($data);
		if($result!==false){
			$model->commit();
		}
		if($sign['data']=='success'){
			echo   "success";
		}else{
			echo   "error";
		}
	}
}
?>