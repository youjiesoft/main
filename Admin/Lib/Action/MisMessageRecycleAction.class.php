<?php
/*
 * 回收站
 */
class MisMessageRecycleAction extends CommonAction{
	
	public function _filter(&$map=array()){
		$_REQUEST ['_sort'] = 'desc'; 
		$map['status'] = -1;
		//$map['recipient'] = $_SESSION [C ( 'USER_AUTH_KEY' )];
		$map['_string'] = "`recipient` = " .$_SESSION [C ( 'USER_AUTH_KEY' )]. " or `createid` = ".$_SESSION [C ( 'USER_AUTH_KEY' )];
		
	}
	
	// eagle  删除， 当是草稿箱里面的，就要把MisMessage中的相关记录也删除
	public function delete(){
		//删除指定记录
		$name="MisMessageUser";
		$model = D ($name);
		$model2 = M ("mis_message");
		if (! empty ( $model )) {		
			$pk = $model->getPk ();													//读取主键
			$id = $_REQUEST [$pk];													//主键
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) ); 	//条件
				$list=$model->where ( $condition )->select();  						//读取选择的删除记录的ID
				foreach($list as $k=>$v ){
					if($v['commit']==0){
						$list2=$model2->delete($v['sendid']);	
						if(!$list2) {
							$this->error ( '非法操作' );
						}
					}
					$list=$model->delete($v['id']);
					if(!$list) {
						$this->error ( '非法操作' );
					}
				}
				$this->success ('删除成功！' );
			} else {
				$this->error ( '非法操作' );
			}
		}
	}
	
	/*
	 * 查看回收站详情，这里调用其它控制器中的方法
	 * eagle
	 * */
	public function readMessage(){
		$mailInbox = A("MisMessageInbox"); // 实例化UserAction控制器对象
		$mailInbox->readMessage($_REQUEST['id']); // 调用User模块的importUser操作方法
	}
	public function lookupreadmessage(){
		$mOutboxmodel=D("MisMessageUser");
		$sendid=$mOutboxmodel->where("id=".$_REQUEST['id'])->getField("sendid");
		$meaageModel=D("MisMessage");
		$list=$meaageModel->where("id=".$sendid)->find();
		$this->assign('default',$list);
		$this->assign('id',$_REQUEST['id']);
		//上一条ID
		$map['id']=array('lt',$_REQUEST['id']);
		$map['status'] = -1;
		$map['_string'] = "`recipient` = " .$_SESSION [C ( 'USER_AUTH_KEY' )]. " or `createid` = ".$_SESSION [C ( 'USER_AUTH_KEY' )];
		$updataid=$mOutboxmodel->where($map)->order('id desc')->getField('id');
		//下一条ID
		$map['id']=array('gt',$_REQUEST['id']);
		$downdataid=$mOutboxmodel->where($map)->getField('id');
		$this->assign("updataid",$updataid);
		$this->assign("downdataid",$downdataid);
		//获取附件信息
		$this->getAttachedRecordList($list['id'],true,true,array(' in', array('MisMessageDrafts','MisMessage','MisMessageInbox')));
		$this->display("lookupmessagelist");
	}
	/*
	 * 还原数据
	 * eagle
	 * */
	public function rollback(){
		//删除指定记录
		$name="MisMessageUser";
		$model = D ($name);
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				$list = $model->where ($condition)->setField ( 'status', 1 );
				if ($list!==false) {
					$this->success ('还原成功！' );
				} else {
					$this->error ('还原失败！');
				}
			} else {
				$this->error ( '非法操作' );
			}
		}
	}
}
?>