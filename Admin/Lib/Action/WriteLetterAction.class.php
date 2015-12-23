<?php
class WriteLetterAction extends CommonAction{
	public function index(){
		//查询所有的部门
		$this->searchuser();
		$this->display();
	}
	
	public function commitviews(){
		$model = D ('Message');
		//标记为发送记录
		$_POST['commit']=1;  
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		//保存当前数据对象
		$list=$model->add ();
		//print_r($model->getLastSql());exit;
		$user = explode(',', $_POST['recipient']);  
		//echo $list;exit;
		foreach ($user as $k => $v) {
			$_POST['recipient'] = $v;
			$_POST['sendid'] = $list;
			$model22=D('UserMessage');
			if (false === $model22->create ()) {
				$this->error ( $model22->getError () );
			}
			$list1=$model22->add ();
			//print_r($model22->getLastSql());exit;
		}
		// end
		if ($list && $list1){
			$aid = $_POST['aid'];
			if ($aid) {
				$model4 = D('mis_attached_record');
				$c['id'] = array('in',$aid);
				$model4->where($c)->setField('orderid',$list);
			}
			$this->success ( L('_SUCCESS_'));
		}else{
			$this->error ( L('_ERROR_'));
		}
	}
	//暂时没有用
	/*
public function sendMessage(){
		$model = D ('Message');
		//标记为发送记录
		$_POST['commit']=1;  
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		
		//保存当前数据对象
		$list=$model->add ();
		//把用户分离出来
		$user = explode(',', $_POST['recipient']);  
		//echo $list;exit;
		foreach ($user as $k => $v) {
			$_POST['recipient'] = $v;
			$_POST['sendid'] = $list;
			$model22=D('UserMessage');
			if (false === $model22->create ()) {
				$this->error ( $model22->getError () );
			}
			$list1=$model22->add ();
			//print_r($model22->getLastSql());exit;
		}
		// end
		if ($list && $list1){
			$aid = $_POST['aid'];
			if ($aid) {
				$model4 = D('mis_attached_record');
				$c['id'] = array('in',$aid);
				$model4->where($c)->setField('orderid',$list);
			}
			$this->success ( L('_SUCCESS_'));
		}else{
			$this->error ( L('_ERROR_'));
		}

	}
*/
	public function searchuser(){
		//查询所有的部门
		$mode1=D("user");
		$mode2=D("mis_system_department");
		$list2=$mode2->where("status = 1")->getField("id,name");
		//print_r($mode2->getLastSql());
		$list=$mode1->select();
		//print_r($mode1->getLastSql());
		$contact=array();
		foreach ($list2 as $id=>$name){
			foreach($list as $key=>$val){
				if ($val['dept_id'] == $id) {
					$contact[$name][]=$val;
				}
			}
		}
		$this->assign("contact",$contact);
		
	}
	
	//保存到草稿箱时用这个方法
	public function insert(){
		$model = D ('Message');
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		//保存当前数据对象
		$list=$model->add ();
		if ($list!==false) {
			$aid = $_REQUEST['aid'];
			if ($aid) {
				$model2 = D('mis_attached_record');
				$c['id'] = array('in',$aid);
				$model2->where($c)->setField('orderid',$list);
			}
			$this->success ( L('_SUCCESS_') ,'',$list);
		} else {
			$this->error ( L('_ERROR_') );
		}
	}
	
	//编写消息方法
	public function writeForm(){
		//查询所有的部门,显示在选人列表
		$this->searchuser();
		$this->display();
	}
}

?>