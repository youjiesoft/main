<?php
/*
 * 发件箱功能
 *
 * */
class MisMessageOutboxAction extends CommonAction {

	public function _filter( &$map=array()){
		$map['status'] = 1;
		$map['commit'] = 1;
		$map['createid'] = $_SESSION [C ( 'USER_AUTH_KEY' )];
		$map['recipient'] = -$_SESSION [C ( 'USER_AUTH_KEY' )];
		
		$searchby =  $_POST["searchby"];
		$keyword=$this->escapeChar($_POST["keyword"]);
		$searchtype = $_POST['searchtype'];
		$this->assign('keyword',$keyword);
		$this->assign('searchby',$searchby);
		$this->assign('searchtype',$searchtype);
		if($keyword){
			$messageodel = D('MisMessage');
			if($searchby == 'title'){
				//标题
				$mMap['title'] = ($searchtype==2) ? array('like',"%".$keyword."%"):$keyword;
				$list1 = $messageodel->where($mMap)->getField('id',true);
				$map['sendid'] = array('in',$list1);
			}elseif($searchby == 'createid'){
				//发件人
				/* $userModel = D('User');
				 $uMap['id'] = $list2['createid'];
				$userList =  $userModel->where('id')->find(); */
				$userModel = D('User');
				$cMap['name'] = ($searchtype==2) ? array('like',"%".$keyword."%"):$keyword;
				$list2 = $userModel->where($cMap)->getField('id',true);
				$map['createid'] = array('in',$list2);
			} elseif($searchby == 'recipientname'){
				//收件人
				$rMap['recipientname'] = ($searchtype==2) ? array('like',"%".$keyword."%"):$keyword;
				$list3 = $messageodel->where($rMap)->getField('id',true);
				$map['sendid'] = array('in',$list3);
			}else{
				$map[$searchby] = ($searchtype==2) ? array('like',"%".$keyword."%"):$keyword;
			}
		}
		$searchby=array(
				array("id" =>"title","val"=>"主题"),
				array("id" =>"createid","val"=>"发件人"),
				array("id" =>"recipientname","val"=>"收件人"),
		);
		$searchtype=array(array("id" =>"2","val"=>"模糊查找"),
				array("id" =>"1","val"=>"精确查找"));
		$this->assign("searchbylist",$searchby);
		$this->assign("searchtypelist",$searchtype);
	}
	
	public function delete(){
		//删除指定记录
		$name="MisMessageUser";
		$model = D ($name);
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			$condition = array ($pk => array ('in', explode ( ',', $id ) ));
			if ($_REQUEST['deleter']){
				$list = $model->where( $condition )->delete();
			}else{
				$list=$model->where ( $condition )->setField ( 'status', - 1 );
			}
			if ($list!==false) {
	
				$this->success ('删除成功！' );
			}
			else{
				$this->error ('删除失败！');
			}
		}
	}
	
	/*
	 * 查看发件箱详情
	 *
	 * */
	
	public function readMessage(){
		$mailInbox = A("MisMessageInbox"); // 实例化UserAction控制器对象
		$mailInbox->readMessage($_REQUEST['id'], false); // 调用User模块的importUser操作方法
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
		$map['status'] = 1;
		$map['commit'] = 1;
		$map['createid'] = $_SESSION [C ( 'USER_AUTH_KEY' )];
		$map['recipient'] = -$_SESSION [C ( 'USER_AUTH_KEY' )];
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
	*检索用户是否读取了邮件
	*eagle
	**/
	public function showReadedMesaage(){
		$idGroup = $_REQUEST['idGroup'];
		$messageID = $_REQUEST['messageID'];
		$idGroupArray = explode(',', $idGroup); 
		$modelMMU = D('MisMessageUser');
		$c['recipient '] = array('in',$idGroupArray);
		$c['sendid'] = $messageID;
		$list=$modelMMU->where($c)->select();
		/*蒋雄    获取员工的照片*/
		$userModel = D('User');
		$map = array();
		$map['id'] = array('in', $idGroupArray);
		$employeidarr = $userModel->where($map)->getField('id,employeid');
		$misHrPersonnelPersonInfo=D("mis_hr_personnel_person_info");
		$map = array();
		$map['id'] = array('in', $employeidarr);
		$picturearr = $misHrPersonnelPersonInfo->where($map)->getField('id,picture');
		$this->assign("picturearr",$picturearr);
		$employeidarr = array_flip($employeidarr);
		$user_pic = array();
		foreach ($employeidarr as $key => $val) {
			$user_pic[$val] = $picturearr[$key];
		}
		foreach ($list as $key => $val) {
			$list[$key]['pic'] = $user_pic[$val['recipient']];
		}
		$this->assign('list',$list);
		/*结束*/
		$this->display();
	}
	/**
	 * @Title: returnmessageoutbox 
	 * @Description: todo(撤回未查看的邮件)   
	 * @author libo 
	 * @date 2014-4-15 上午9:21:07 
	 * @throws
	 */
	public function returnmessageoutbox(){
		$mUserModel=D("MisMessageUser");
		$sendid=$mUserModel->where("id=".$_REQUEST['id'])->getField("sendid");
		$list=$mUserModel->where("sendid=".$sendid)->select();
		$readArr=array();
		foreach ($list as $k=>$v){
			if(strpos($v['recipient'], "-") !== false){
				unset($v);
			}
			//组合全部收件人的阅读状态
			$readArr[]=$v['readedStatus'];
		}
		if(in_array(1, $readArr)){//如果有人阅读
			$this->error("撤回失败，已读邮件不能撤回");
		}else{
			foreach ($list as $k=>$v){
				//邮件撤回
				$mUserModel->where("id=".$v['id'])->setField("returnmessage",0);
			}
			$this->success("撤回成功");
		}
	}
}
?>