<?php
/**
 * @Title: MisMessageInboxAction
 * @Package package_name
 * @Description: todo(收件箱功能)
 * @author liminggang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2012-12-20 上午10:16:55
 * @version V1.0
 */
class MisMessageInboxAction extends CommonAction {

	public function _filter( &$map ){
		$map['status'] = 1;
		$map['commit'] = 1;
		$map['returnmessage']=1;//邮件未撤回
		if($_REQUEST['ids']) $map['id'] = $_REQUEST['ids'];
		$map['recipient'] = $_SESSION [C ( 'USER_AUTH_KEY' )];
		$messageType= $_REQUEST['messageType'] ? $_REQUEST['messageType']:0;
		$important= $_REQUEST['important'];
		$interemail = $_REQUEST['interemail'];
		$this->assign('messageType',$messageType);
		$this->assign('important',$important);
		if($messageType == 1 ){
			$map['messageType']=1; //显示系统消息
		} else {
			$map['messageType']=0; //显示站内信
		}
		if($important){
			$map['important']=1;	//显示重要邮件
		}
		//获取当前登录人系统邮件条数
		$MisMessageUserModel = D("MisMessageUser");
		$msg= $MisMessageUserModel->getUserCurrentMsg(1);
		if($msg["newmsg"]>0){
		    $this->assign("newmsg",$msg["newmsg"]);
		}
		
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
	
	public function _after_list(&$vo){
		if(!$_REQUEST['fullmodel']){//默认读取第一封邮件
			$this->readMessage($vo[0]['id']);
		}
		$messageidary = array();
		foreach ($vo as $key=>$value){
			$messageidary[] = $value['sendid'];
		}
		$modelMAR = D('MisAttachedRecord');
		$map = array();
		$map["status"]  =1;
		$map["orderid"] =array('in',$messageidary);
		$map["type"] =64;
		$isattachment = $modelMAR->where($map)->getField('orderid',true);
		foreach ($vo as $ke=>$val){
			if (in_array($val['sendid'],$isattachment)) {
				$vo[$ke]['isattr'] = true;
			}
		}
		$msgModel = D('MisMessage');
		$map = array();
		$map["status"]  =1;
		$map["id"] =array('in',$messageidary);
		$result = $msgModel->where($map)->getField('id,istranspond,isreply');
		foreach ($vo as $key=>$val){
			foreach ($result as $k=>$v){
				if ($val['sendid'] === $v['id']){
					$vo[$key]['istranspond']=$v['istranspond'];
					$vo[$key]['isreply']=$v['isreply'];
				}
			}
		}
		$this->assign('list', $vo);
	}
	/*
	 *重写CommonAction中的_before_index，用于处理系统消息和收件箱页面
	 */
	public function _before_index(){
// 		dump($_REQUEST);
		if($_REQUEST['searchtype']){//返回的检索条件
			$_POST['keyword']=$_REQUEST['keyword'];
			$_POST['searchby']=$_REQUEST['searchby'];
			$_POST['searchtype']=$_REQUEST['searchtype'];
		}
		if ($_GET['messageType']) {
			//系统消息
			if($_REQUEST['fullmodel']){//完整模式
				$this->assign('rel_id_name','system_message_full');
				$messageTypeUrl = "/messageType/".$_GET['messageType']."/fullmodel/".$_REQUEST['fullmodel'];
			}else{
				$this->assign('rel_id_name','system_message');
				$messageTypeUrl = "/messageType/".$_GET['messageType'];
			}
			$this->assign('messageTypeUrl',$messageTypeUrl);
			$this->assign("messageType",$_GET['messageType']);
		}else if($_REQUEST['important']){
			//重要邮件箱
			if($_REQUEST['fullmodel']){//完整模式
				$this->assign('rel_id_name','important_message_full');
				$messageTypeUrl ="/fullmodel/".$_REQUEST['fullmodel']."/important/1";
			}else{
				$this->assign('rel_id_name','important_message');
				$messageTypeUrl ="";
			}
			$this->assign('messageTypeUrl',$messageTypeUrl);
		}else{
			//收件箱
			if($_REQUEST['fullmodel']){//完整模式
				$this->assign('rel_id_name','is_message_full');
				$messageTypeUrl ="/fullmodel/".$_REQUEST['fullmodel'];
			}else{
				$this->assign('rel_id_name','is_message');
				$messageTypeUrl ="";
			}
			$this->assign('messageTypeUrl',$messageTypeUrl);
			$this->assign('msgtype', 1);
		}
		//判断是否是重要邮件
		$_REQUEST['important'] = $_REQUEST['important'] ? $_REQUEST['important'] : 0;
		$this->assign("important",$_REQUEST['important']);
	}
	public function index() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		$fullmodel=$_REQUEST['fullmodel'];
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name = $this->getActionName();
		if (! empty ( $name )) {
			$qx_name=$name;
			if(substr($name, -4)=="View"){
				$qx_name = substr($name,0, -4);
			}
			//验证浏览及权限
			if( !isset($_SESSION['a']) ){
				$map=D('User')->getAccessfilter($qx_name,$map);
			}
			$this->_list ( $name, $map );
		}
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($name);
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		//扩展工具栏操作
		$toolbarextension = $scdmodel->getDetail($name,true,'toolbar');
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		if( intval($_POST['dwzloadhtml']) ){
			$this->display("dwzloadindex");exit;
		}
		//首页收件箱调用方法，为ajax调用
		if ($_GET['type'] == "ajaxcall") {
			$this->display ("ajax_index");exit;
		}
		if($_REQUEST['jump'] == "jump"){
			$this->display('indexview');exit;
		}
		if($_REQUEST['fullmodel']){
			$this->assign("fullmodel",$_REQUEST['fullmodel']);
			if($_GET['messageType']){
				$this->assign("messageType",$_GET['messageType']);
			}
			$this->display("indexList");
			exit;
		}
		$this->display ();
		return;
	}
	/**
	 * @Title: lable 
	 * @Description:标记为已读邮件
	 * @author laicaixia 
	 * @date 2013-7-26 下午3:13:01 
	 * @throws 
	*/  
	public function lable(){
		$model = D('MisMessageUser');
		$readedStatus = 1;
		if($_REQUEST['readedStatus']){
			$readedStatus = 0;
		}
		if(strpos($_REQUEST ["id"], ',')){
			$map['id']= array('in',$_REQUEST ["id"]);
		} else {
			$map['id']= $_REQUEST ["id"];
		}
		$result = $model->where($map)->setField('readedStatus',$readedStatus);
		$this->success ( L('_SUCCESS_') );
	}
	/**
	 * @Title: lable 
	 * @Description:标记为重要邮件
	 * @author laicaixia 
	 * @date 2013-7-26 下午3:13:01 
	 * @throws 
	*/  
	public function important(){
		$model1 = D('MisMessageUser');
		$map = array();
		$map['id']= array('in',$_REQUEST ["id"]);
		$important = 1;
		if($_REQUEST['importants']){
			$important = 0;
		}
		// 判断是否批量
		if(strpos($_REQUEST ["id"], ',')){
			$map['id']= array('in',$_REQUEST ["id"]);
		} else {
			$map['id']= $_REQUEST ["id"];
		}
		$model1->where($map)->setField('important',$important);
		$this->success ( L('_SUCCESS_') );
	}
	
	public function lookupemailcount(){
		//获取当前登录人系统邮件条数
		$MisMessageUserModel = D("MisMessageUser");
		$msg= $MisMessageUserModel->getUserCurrentMsg(2);
		echo json_encode($msg);
	}
	
	public function readMessage($id,$isdefault=true){
		$name="MisMessageUser";
		$model = D($name);
		if ($_REQUEST ["id"]) {
			$id = $_REQUEST ["id"];
			$isdefault=false;
		}
		$map['id']= $id;
		 
		
		//标记为已读
		$result = $model->where($map)->setField('readedStatus',1);
			
		//数据库事务，当没有回显时，会提示出错
		 if($result) {
                $this->transaction_model->commit();
         }else{
               	$this->transaction_model->rollback();
         }
		
		//联合查询条件
		$condition['mis_message_user.id'] = $id;
		$list = $model->table('mis_message_user as mis_message_user')->where($condition)->join('mis_message as mis_message ON mis_message_user.sendid = mis_message.id')->field('
				mis_message_user.id as muid,
				mis_message.createtime as createtime,
				mis_message.createid as createid,
				mis_message.title as title,
				mis_message.id as id,
				mis_message.recipientname as recipientname,
				mis_message.copytopeoplename as copytopeoplename,
				mis_message.istranspond as istranspond,
				mis_message.isreply as isreply,
				mis_message.content as content
				')->find();
		$this->assign("default",$list);
		//获取附件信息
		$this->getAttachedRecordList($list['id'],true,true,array(' in', array('MisMessageDrafts','MisMessage','MisMessageInbox')));
		$this->assign('a_id', $id);
		
		if(!$isdefault) {
			if($_REQUEST['fullmodel']){
				$this->display('lookupmessagelist');
			}else{
				$this->display();
			}
		}
	}
	
	/**
	 * 回复邮件
	 **/
	public function replyMessage(){
		//调出用户例表
		 $modelMM = A('MisMessage');
		 $modelMM->searchuser();
		 
		 $id = $_REQUEST['id'];
		 $name="MisMessageUser";
		 $model = D($name);
		 //联合查询条件
		 $condition['a.id'] = $id;
		 $list = $model->table('mis_message_user as a')->where($condition)->join('mis_message as b ON a.sendid = b.id')->find();
		 //获取原始邮件信息 并组装到div中 传入页面
		 $list['createname']=getFieldBy($list['createid'], 'id', 'name', 'user');//
		 $list['createtime']=transTime($list['createtime'],'Y-m-d h:i');
		 $message="<div style='background:#e0ecf9;'>
		 <p>--------------原始邮件----------------</p>
		 <p><b>主题</b>：{$list['title']}</p>
		 <p><b>发件人</b>：{$list['createname']}</p>
		 <p><b>发件时间</b>：{$list['createtime']}</p>
         <p><b>收件人</b>：{$list['recipientname']}</p>
		 </div><br/>";
		 //回复
		 if (!$_REQUEST['transpond']) {
		 	$list['content'] = $message. $list['content'].'<br/>';
		 }else {
		 	$list['title']="转发：".$list['title'];
		 	//获取附件信息
			$this->getAttachedRecordList($list['id'],true,true,array(' in', array('MisMessageDrafts','MisMessage','MisMessageInbox')));
		 }
		 $this->assign("vo",$list);
		 //转发
		 $this->assign('transpond',$_REQUEST['transpond']);
		 $this->assign('isreply',$_REQUEST['isreply']);
		 $this->assign('outbox',$_REQUEST['outbox']);
		 $this->display();
	 }
	 
	/**
	 * 跨模块插入别外一个数据
	 **/
	public function insert(){
		if ($_POST['recipient'] || $_POST['copytopeopleid']){
			if ($_POST['recipient']) {
				$_POST['recipient'] = implode(',', $_POST['recipient']);
				$_POST['recipientname'] = implode(',', $_POST['recipientname']);
			}
			if ($_POST['copytopeopleid']) {
				$_POST['copytopeopleid'] = implode(',',$_POST['copytopeopleid']);
				$_POST['copytopeoplename'] = implode(',',$_POST['copytopeoplename']);
			}
		}else {
			$this->error ( '请添加收件人！' );
		}
		if ($_POST['transpond']){
			$_POST['istranspond'] = 1;
		}
		if ($_POST['isreply']){
			$_POST['isreply'] = 1;
		}
		if($_POST['quickreply']){
			$_POST['content']=$_POST['content']."<br/>".$_POST['originalcontent'];
		}
		$model = D ('MisMessage');
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
// 		dump($model->create ());die;
		//保存当前数据对象
		$list=$model->add ();
		if ($list!==false) {
			$mrdmodel = D('MisRuntimeData');
			$mrdmodel->setRuntimeCache($_POST,'MisMessageInbox','add');
			$module2=A('MisMessageInbox');
			if (method_exists($module2,"_after_insert")) {
				call_user_func(array(&$module2,"_after_insert"),$list);
			}
			$this->success ( L('_SUCCESS_') ,'',$list);
			exit;
		} else {
			$this->error ( L('_ERROR_') );
		}
	}
	
	//保存到草稿箱时用这个方法
	public function _after_insert($insertid){
		if ($insertid) {
			$this->swf_upload($insertid,64);
		}
		$replyuploadaddached = $_POST['replyuploadaddached'];
		$attModel = D("MisAttachedRecord");
		foreach ($replyuploadaddached as $key => $value) {
			$data=array();
			$data['type']=64;
			$data['orderid']=$insertid;
			$data['tablename'] = $this->getActionName();
			$data['tableid']=$insertid;
			$data['attached']= $value;
			$data['upname']=$_POST['replyuploadname'][$key];
			$data['createtime'] = time();
			$data['createid'] = $_SESSION[C('USER_AUTH_KEY')]?$_SESSION[C('USER_AUTH_KEY')]:0;
			$rel=$attModel->add($data);
		}
		 //如果传过来了， 最后一次存入的id
		$modelMMU = D('MisMessageUser');
		 if( $_POST['commit']){
			$user = explode(',', $_POST['recipient']);
			$copyuser = explode(',', $_POST['copytopeopleid']);
			$user = array_merge($user, $copyuser);
			//去重复
			$user = array_unique($user);
			//把当前发件人的ID也存入数组
			$user[] = -$_SESSION [C ( 'USER_AUTH_KEY' )];
			//$user[] = 0;
			foreach ($user as $k => $v) {
				$_POST['recipient'] = $v;
				$_POST['sendid'] = $insertid;
				//用create对应一下
				if (false === $modelMMU->create ()) {
					$this->error ( $modelMMU->getError () );
				}
				$list1=$modelMMU->add ();
			}
		} else {
			//$user = explode(',', $_POST['recipient']);  
			//把当前发件人的ID也存入数组
			$user[] = -$_SESSION [C ( 'USER_AUTH_KEY' )];
			//$user[] = 0;
			foreach ($user as $k => $v) {
				$_POST['recipient'] = $v;
				$_POST['sendid'] = $insertid;
				//用create对应一下
				if (false === $modelMMU->create ()) {
					$this->error ( $modelMMU->getError () );
				}
				$list1=$modelMMU->add ();
			}
		}
		// end
		if($insertid || $list1){
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
	/**
	 *
	 * @Title: lookupmanage
	 * @Description: todo(用ztree形式查询出所有部门员工信息。)
	 * @author liminggang
	 * @throws
	 */
	public function lookupmanage(){
		$this->assign('ulId', $_REQUEST['ulId']);
		$model= M('mis_system_department');
		$deptlist = $model->where("status=1")->order("id asc")->select();
		$param['rel']="positiveBox";
		$param['url']="__URL__/lookupmanage/jump/1/deptid/#id#/parentid/#parentid#/ulId/".$_REQUEST['ulId'];
		$typeTree = $this->getTree($deptlist,$param);
		$this->assign('tree',$typeTree);
		$map = array();
		$searchby = str_replace("-", ".", $_POST["searchby"]);
		$keyword=$this->escapeChar($_POST["keyword"]);
		$searchtype = $_POST['searchtype'];
		if($_POST["keyword"]){
			$map[$searchby] = ($searchtype==2)  ? array('like','%'.$keyword.'%'):$keyword;
			$this->assign('keyword',$keyword);
			$searchby = str_replace(".", "-", $_POST["searchby"]);
			$this->assign('searchby',$searchby);
			$this->assign('searchtype',$searchtype);
		}
		$searchby=array(
			array("id" =>"user-name","val"=>"按员工姓名"),
			array("id" =>"orderno","val"=>"按员工编号"),
		);
		$searchtype=array(array("id" =>"2","val"=>"模糊查找"),
				array("id" =>"1","val"=>"精确查找"));
		$this->assign("searchbylist",$searchby);
		$this->assign("searchtypelist",$searchtype);
		$map['positivestatus']=1;
		$deptid		= $_REQUEST['deptid'];
		$parentid	= $_REQUEST['parentid'];
		if ($deptid && $parentid) {
			$deptlist =array_unique(array_filter (explode(",",$this->downAllChildren($deptlist,$deptid))));
			$map['user.dept_id'] = array(' in ',$deptlist);
		}
	 
		$this->_list('MisHrPersonnelPersonInfoView',$map);
		$this->assign('deptid',$deptid);
		$this->assign('parentid',$parentid);
		if ($_REQUEST['jump']) {
			$this->display('lookupmanagelist');
		} else {
			$this->display("lookupmanage");
		}
	}
	/**
	 *
	 * @Title: refurbishCurrentMsg
	 * @Description: todo(刷新首页上面的消息数量)
	 * @author jiangx
	 * @throws
	 */
	public function lookuprefurbishCurrentMsg(){
		//获取当前登录人系统邮件条数
		$MisMessageUserModel = D("MisMessageUser");
		$msg= $MisMessageUserModel->getUserCurrentMsg(2);
		$this->assign('msgcount', $msg);
		$this->display();
	}
	/**
	 * @Title: lookupreadmessage 
	 * @Description: todo(邮件的直接查看，收件箱右边显示的内容)   
	 * @author xiafengqin 
	 * @date 2013-8-28 上午10:53:12 
	 * @throws
	 */
	public function lookupreadmessage(){
		if($_REQUEST['fullmodel']){
			$this->assign("fullmodel",$_REQUEST['fullmodel']);
			if($_REQUEST['messageType']){
				$this->assign("messageType",$_REQUEST['messageType']);
			}
			$this->assign("pageNum",$_REQUEST['pageNum']);
			$this->assign("keyword",$_REQUEST['keyword']);
			$this->assign("searchby",$_REQUEST['searchby']);
			$this->assign("searchtype",$_REQUEST['searchtype']);
		}
		$this->assign("important",$_REQUEST['important']);
		$this->assign("msgtype",$_REQUEST['msgtype']);
		$userModel = D ('MisMessageUser');
		$id = $_REQUEST ["id"];
		// 上一条数据ID
		$map['id'] = array("lt",$id);
		$map['status'] = 1;
		$map['commit'] = 1;
		$map['returnmessage']=1;//邮件未撤回
		$map['recipient'] = $_SESSION [C ( 'USER_AUTH_KEY' )];
		$messageType = $_REQUEST['messageType'];
		$important = $_REQUEST['important'];
		if($messageType){
			$this->assign('msgtype', 0);
			$this->assign('messageType', 1);
			$map['messageType']=1; //显示系统消息
		} else {
			$this->assign('msgtype', 1);
			$this->assign('messageType', 0);
			$map['messageType']=0; //不显示系统消息
		}
		if($important){
			$this->assign('msgtype', 1);//表示是否可以回复
			$this->assign('important', 1);
			$map['important']=1;	//显示重要邮件
		}
		$updataid = $userModel->where($map)->order('id desc')->getField('id');
		$this->assign("updataid",$updataid);
		// 下一条数据ID
		$map['id'] = array("gt",$id);
		$downdataid = $userModel->where($map)->getField('id');
		$this->assign('downdataid',$downdataid);
		$this->readMessage($id, false);
	}
	/**
	 * @Title: getMessages 
	 * @Description: todo(查指定条数的已读或者未读邮件) 
	 * @param int    $number 读取指定条数的数据
	 * @param boolen $isRead  已读或未读邮件0表示未读，1表示已读
	 * @param int    $userid  当前登录人的id
	 * @author xiafengqin 
	 * @date 2014-2-14 下午3:21:44 
	 * @throws
	 */
	public function getMessages($number=10,$isRead=0,$userid=0){
		$MisMessageInboxModel = D("MisMessageInbox");
		return $MisMessageInboxModel->getMessages($number=10,$isRead=0,$userid=0);
	}
	/**
	 * @Title: getMessagesDetail
	 * @Description: todo(查指定邮件详细内容)
	 * @param int    $id 读取指定邮件：mis_message_user的序号
	 * @author yangxi
	 * @date 2014-4-6
	 * @throws
	 */
	public function getMessagesDetail($id=0){	
		if(!$id){
		  die("error:非法传值！");	
		}else{
			$MisMessageInboxModel = D("MisMessageInbox");
			return $MisMessageInboxModel->getMessagesDetail($id);
		}
	}
	
}
?>