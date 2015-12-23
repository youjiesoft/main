<?php
/*
 * 收件箱功能
 *mis_hrmessage
 * */
class InboxAction extends CommonAction {

	public function _filter( &$map ){
		$map['status'] = 1;
		$map['recipient'] = $_SESSION [C ( 'USER_AUTH_KEY' )];
		$messageType= $_REQUEST['messageType'];
		if($messageType){
			$map['messageType']=1; //显示系统消息
		}
	}
	
	public function delete(){ 
		
		//删除指定记录
		$model = D ('UserMessage');
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				//标记原表，为删除状态
				$list=$model->where ( $condition )->setField ( 'status', - 1 );
				$volist = $model->where ( $condition )->select();
				foreach ($volist as $k => $v) {
					$rmodel = D('Recycle');
					unset($_POST['id']);
					$_POST['title'] = $v['title'];
					$_POST['sendid'] = $v['sendid'];
					$_POST['senderid'] = $v['createid'];
					$_POST['createid'] = $v['createid'];
					$_POST['recipient'] = $v['recipient'];
					$_POST['content'] = $v['content'];
					$_POST['objid'] = $id;
					$_POST['objtype'] = 1; //确定是从哪个箱里删除的
					//保存当前数据对象
					$list2=$rmodel->add($_POST);
					//print_r($rmodel->getLastSql());
				}
				if ($list!==false) {
					$this->success ('删除成功！' );
				} else {
					$this->error ('删除失败！');
				}
			} else {
				$this->error ( '非法操作' );
			}
		}
	}
	/*
	 * 查看收件箱详情
	 *
	 * */
	public function view(){
		$name="UserMessage";
		$model = D ($name);
		$pk = $model->getPk ();
		$id = $_REQUEST [$pk];
		$condition = array ($pk => array ('eq', $id ) );
		$list=$model->where ( $condition )->find();
		$this->assign("vo",$list);
		//获取附件信息
		$attinfo =$this->getAttachedRecordList($list['sendid'],true,true,$subid=0, $tablename='',$isassign=false);
		$this->assign("attach",$attinfo);
		$this->display();
	}
}
?>