<?php
/**
 * @Title: OAHelperAction
 * @Package OA助的请求数据
 * @Description: OA助手，请求类，取数据接口
 * @author eagle
 * @company 重庆特米洛科技有限公司˾
 * @copyright 重庆特米洛科技有限公司˾
 * @date 2014-2-08
 * @version V1.0
 */
class OAHelperAction extends Action {
	private $userid=NULL;//当前登录用户
	private $account=NULL;
	private $password=NULL;

	public function __construct(){
		if((!empty($_REQUEST['account'])) && (!empty($_REQUEST['password']))){
			$map['account']=$_REQUEST['account'];
			$map['password']=$_REQUEST['password'];
			$map['status']=array('EQ',1);
			//echo '{"success":"b","u":"'.$_REQUEST['account'].'","p":"'.$_REQUEST['password'].'","aa":"123"}';
		 // die;
			$this->userid=M('User')->where($map)->getfield('id');
			//此处指定为OA助手登录效果
			$_REQUEST['fromOA']=2;
		}else{
			//echo '{"success":"a","u":"'.$_REQUEST['account'].'","p":"'.$_REQUEST['password'].'","aa":"456"}';
			//die;
			echo '[{"error":"参数传递有误！"}]';
		}
	}

	//此方法用来处理返回数据类型，为JSON  还是  array
	public  function getReturnData($returnData,$returnType){
		if($returnType=='json'){
			if($returnData){
				//echo '[{"error":"没有找到数据"}]';
				echo json_encode($returnData);
			}else{
				echo '[{"error":"没有找到数据"}]';
			}
		}else if($returnType=='arr'){
			if($returnData){
				if(is_array($returnData)){
					$returnData=$returnData;
				}else{
					$returnData=(array)$returnData;
				}
				return $returnData;
			}
		}
	}
	public  function getAccessUrl(){
		$accesstype=$_REQUEST['AccessType'];
		if(!empty($accesstype)){
			if(!empty($_REQUEST['urldata'])){
				$_urlData  ="Index/index?data=".$_REQUEST['urldata'];//针对某些带参数钻取的数据
			}else{
				$_urlData  ="Index/index";
			}
			switch ($accesstype){
				//这个是邮件首页
				case 1:
					$url="Index/index";
					break;
					//这个是邮件首页
				case 2:
					// 分号分隔，第一部分为链接用逗号分隔参数，第二部分为打开navtab的标题,这里是在通过model名唯一的方法查询node表以获得它的中文名称，第三部分为打开navtab的REL
					$url="Index/index?data=MisMessage,index;MisMessage;MisMessage";
					break;
					//这个是共享文档首页
				case 3:
					$url="Index/index?data=MisFileManager,index,pid,5,cid,5,share,share;MisFileManager;MisFileManager";
					break;
				case 4:
					//这个是公告中心
					$url="MisSystemPanelMethod/systemNoticeslookup";
					break;
				case 5:
					//这个是知识库
					$url="MisSystemNoticeMethod/knowledge";
					break;
				case 6:
					//这个是专家问答
					$url="MisSystemNoticeMethod/expertquestion";
					break;
				case 7:
					//通讯录打开
					$url="Index/index?data=MisSystemDepartment,organization,pid,5,cid,5,share,share;MisSystemDepartment;MisSystemDepartment;Dialog";
					break;
				case 8:
					//这个是具体邮件点击
					$url=$_urlData;
					break;
				case 9:
					//这个是待办事项点击
					$url=$_urlData;
					break;
				case 10:
					//这个是通知公告点击,特殊处理拼装成适合U方法解析的格式
					$noticeArr=array();
					$noticeArr=explode(',',$_REQUEST['urldata']);
					$url=$noticeArr[0]."/".	$noticeArr[1].'?'.$noticeArr[2].'='.$noticeArr[3]."&".$noticeArr[4].'='.$noticeArr[5];
					break;
				case 11:
					//短信息打开
					$url="Index/index?data=MisMessagePhone,add;MisMessagePhone;MisMessagePhone;Dialog";
					break;
			}
		}else{
			$url="Index/index";
		}
		$signstatus=A("Public")->checkLogin();
		//redirect(U($url));
		//$url = @iconv('UTF-8', 'GBK', urldecode($url));
// 		print_r($url);
// 		exit;
		redirect(U($url),0.01,'Please waiting  3S,page jumping...');
	}
	//取当前在线人员信息
	public function getOnlineUser(){
		//file_put_contents('a1.log',var_export($_REQUEST,1),FILE_APPEND);
		$returnData=A("Common")->lookupRefreshOnLine('1');
	}
	/**
	 * @Title: getWillWorks
	 * @Description: todo(OA助手获取我的待办任务)
	 * @author 杨东
	 * @date 2013-3-15 下午5:02:51
	 * @throws
	 */
	public function getWillWorks($returnType='json'){
		$model = D('MisWorkMonitoring');
		$map['dostatus'] = 0;
		$map['_string'] = 'FIND_IN_SET(  '.$this->userid.', curNodeUser )';
		$result = $model->where($map)->select();
		$returnData=array();
		$num=0;//ruturnData数组的顺序
		foreach( $result as $k=>$v ){
			if(!in_array($v['tablename'], array_keys($moduleNameList))){
					$moduleNameList[$v['tablename']]=1;
					$model = D($v['tablename']);
					$map = array();
					$action = A($v['tablename']);
					if (method_exists ( $action, '_filter' )) {
						$action->_filter ( $map );
					}
					$map['status'] = 1;
					$map['_string'] = 'FIND_IN_SET(  '.$this->userid.', curAuditUser )';
					$count = $model->where($map)->count('id');
					if ($count) {
						$returnData[$num]['name']=getFieldBy($v['tablename'], 'name', 'title', 'node');
						$returnData[$num]['title']=getFieldBy($v['tablename'], 'name', 'title', 'node');
						$returnData[$num]['count']=$count;
						$returnData[$num]['urldata']=$v['tablename'].",index,default,2".";".$v['tablename'].";".$v['tablename'];
						$num++;
					}
			}
		}
		return $this->getReturnData($returnData,$returnType);		
	}

	/**
	 * @Title: getWillWorksList
	 * @Description: todo(手机助手获取我的待办任务)
	 * @author 杨希
	 * @date 2013-3-15 下午5:02:51
	 * @throws
	 */
	public function getWillWorksList($returnType='json'){
		$MWMModel = D('MisWorkMonitoring');
		$user     = M('user');
		$map['dostatus'] = 0;
		$map['_string'] = 'FIND_IN_SET(  '.$this->userid.', curNodeUser )';
		$result = $MWMModel->where($map)->field('orderno,tablename,tableid,createid,createtime')->select();
		$returnData=array();
        foreach($result as $key=>$val){ 
        	$tablename=$MWMModel->getTableName($val['tablename']);
        	//获取待办事务名称
        	$returnData[$key]['title']     =getFieldBy($val['tablename'], 'name', 'title', 'node'); 
        	//获取待办事务名称
        	$returnData[$key]['orderno']   =$val['orderno'];
        	//获取对应表名
        	$returnData[$key]['tablename'] =$val['tablename'];  
        	//获取对应表ID
        	$returnData[$key]['tableid'] =$val['tableid'];        	      	     	
        	$userinfo=$user->where("id")->field('name,dept_id,duty_id')->find();
        	//获取制单人名
        	$returnData[$key]['username']  =$userinfo['name'];
        	//获取制单人部门
        	$returnData[$key]['userdept']  =getFieldBy($userinfo['dept_id'], 'id','name', 'mis_system_department');
        	//获取制单人职级
        	$returnData[$key]['userduty']  =getFieldBy($userinfo['duty_id'], 'id','name', 'duty');
        	//获取制单时间
        	$returnData[$key]['createtime'] =$val['createtime'];
        }
        return $this->getReturnData($returnData,$returnType);
	}	
	/**
	 * @Title: getWillWorksDetail
	 * @Description: todo(手机助手获取我的待办任务详情)
	 * @author 杨希
	 * @date 2014-4-17 下午5:02:51
	 * @throws
	 */	
	public function getWillWorksDetail($returnType='json'){
		file_put_contents('a.log',var_export($_REQUEST,1),FILE_APPEND);

		//$_GET['model']="MisPrincipalInput";//MisSalesContractmas
		//$_GET['tableid']=13;

		$modelName	= $_REQUEST['model'];
		$tableid	= $_REQUEST['tableid'];	
		$model 		= D('SystemConfigDetail');
		
		//从动态配置获取到数据信息
		$result=$model->GetDynamicconfData($modelName,$tableid);
		//初始化返回值
		$returnData=array();
		//message信息拼装
		$message="";
		foreach($result as $key => $val){
			//初始化一个字符串容器
			$tmpstring="";
            foreach($val as $subkey => $subval){
			  $tmpstring=$subkey.'：</b>'.$subval;
            }
			$message.='<b class="t-gre1">'.$tmpstring.'<br/>';
		}
        //将值赋予返回数据
		$returnData['message']=$message;
		//声明相关附件表
		$modelMAR = M('MisAttachedRecord');
		//获取附件信息
		$num=0;
		$map = array();
		$map["status"]  =1;
		$map["tableid"] =$tableid;
		$map["tablename"] =$modelName;
		$attarry=$modelMAR->field("upname,attached")->where($map)->select();
		if($attarry){
			$returnData['attach']=array();
			foreach($attarry as $attkey => $attval){
				$returnData['attach'][$num]['attachname']=$attval['upname'];
				//注意这里的路径，必须是__APP__."/MisSystemAnnouncement/misFileManageDownload/：对应模型名称，不能是__URL__."/misFileManageDownload/；将取不到数据
				$returnData['attach'][$num]['attachurl']=__APP__."/MisSystemAnnouncement/misFileManageDownload/path/".base64_encode($attval['attached'])."/rename/".$attval['upname'];
				$num++;
			}
		}else{
			//你存在数据是赋予空值
			$returnData['attach']=null;
		}		
        return $this->getReturnData($returnData,$returnType);
	}
	/**
	 * @Title: backprocess
	 * @Description: todo(手机助手打回待办事务)
	 * @author 杨希
	 * @date 2014-4-17 下午5:02:51
	 * @throws
	 */
	public function backprocess(){
		C('TOKEN_ON',false);
// 		$_POST['tableid']=10;
// 		$_POST['tablename']='MisPrincipalInput';
 		$_SESSION[C('USER_AUTH_KEY')]=$this->userid;
		$_POST['id']=$_REQUEST['tableid'];
		unset($_REQUEST['tableid']);
		$modelname=$_REQUEST['tablename'];
		//初始化打回必备POST值
		$_POST['backprocess']="流程回退";
		$_POST['doinfo']=$_REQUEST['doinfo'];
		$m = M();
		$m->startTrans();
		$model= A( $modelname );
		//$_REQUEST['fromOA'] = 1;
		$model->backprocess();
		if(C('PHONE')==="TRUE"){
		    $m->commit();
		    echo '{"success":"1"}';
		    exit;
		}
	}
	/**
	 * @Title: auditProcess
	 * @Description: todo(手机助手审核待办事务)
	 * @author 杨希
	 * @date 2014-4-17 下午5:02:51
	 * @throws
	 */
	public function auditProcess(){
 		C('TOKEN_ON',false);
// 		$_POST['tableid']=15;
// 		$_POST['tablename']='MisPrincipalInput';
 		$_SESSION[C('USER_AUTH_KEY')]=$this->userid;
        //初始化审核验证SESSION数据
		$_SESSION[strtolower($_POST['tablename'])."_index"] == 1;
		$_POST['id']=$_REQUEST['tableid'];
		unset($_REQUEST['tableid']);
		$modelname=$_REQUEST['tablename'];
		//初始化打回必备POST值
		$_POST['auditprocess']="流程审核";
		$_POST['endprocess']="流程结束";
		$_POST['doinfo']=$_REQUEST['doinfo'];
		$m = M();
		$m->startTrans();	
		$model= A( $modelname );
		//$_REQUEST['fromOA'] = 1;
		$model->auditProcess();
		if(C('PHONE')==="TRUE"){
		    $m->commit();
		    echo '{"success":"1"}';
		    exit;
		}
	}

	//取文档信息
	public function getNewFile($returnType='json'){
		$returnData=A("MisFileManager")->lookupgetNewestFile(10,$this->userid);
		return $this->getReturnData($returnData,$returnType);	
	}
	/**
	 * @Title: getMessages
	 * @Description: todo(获取新邮件列表)
	 * @param string $returnType  返回值类型，默认为json
	 * @author yangxi
	 * @date 2014-4-3 
	 * @throws
	 */
	public function getMessages($returnType='json'){
		//file_put_contents('a.log',var_export($_REQUEST,1),FILE_APPEND);
		$returnData = A("MisMessageInbox")->getMessages(10,0,$this->userid);
		//返回了mis_message_user表的id(邮件指向)，createid发件人,createtime发件时间，messageType(邮件类型),mis_message表的title（主题）
		//声明相关附件表
		$modelMAR = M('MisAttachedRecord');
		//现在重新拼装带URL的返回值数据
		foreach($returnData as $key => $val){
			$messageType = "inboxself";
			if($val['messageType'] == 1){
				$messageType = "inboxsystem";
			}
			$returnData[$key]['urldata']="MisMessage,index,id,".$returnData[$key]['id'].",messageType,".$messageType.";MisMessage;MisMessage";
		}
		
		return $this->getReturnData($returnData,$returnType);			
	}

	/**
	 * @Title: getMessageDetail
	 * @Description: todo(邮件信息读取，REQUEST传入mis_message_user的id)
	 * @param string $returnType  返回值类型，默认为json
	 * @author yangxi
	 * @date 2014-4-3 
	 * @throws
	 */
	public function getMessageDetail($returnType='json'){
		//对当前用户查看记录做标记
		$MMUModel=M("mis_message_user");
		$map['id']=$_REQUEST['id'];//mis_message_user的ID
		//改变状态为已读
		$readStatus=$MMUModel->where($map)->setField("readedStatus",1);
		//查看邮件详细信息
		$returnData = A("MisMessageInbox")->getMessagesDetail($_REQUEST['id']);
		//返回了mis_message_user表的id(邮件指向)，createid发件人,createtime发件时间，messageType(邮件类型),mis_message表的title（主题）
		return $this->getReturnData($returnData,$returnType);		
		
	}
	
	/**
	 * @Title: replyMessage
	 * @Description: todo(邮件回复 传入回复邮件id 和内容 返回成功)
	 * @param replyType 回复类型sender(发件人) all(发件人，收件人（除了自己），抄送人)
	 * @author libo
	 * @date 2014-4-3 下午5:22:31
	 * @throws
	 */
	public function replyMessage($replyType='sender'){
		file_put_contents('a.log',var_export($_REQUEST,1),FILE_APPEND);
		if(!$_REQUEST['id'] || !$_REQUEST['title'] || !$_REQUEST['content']){
			echo '{"error":"回复邮件要素不全！"}';
			exit;
		}
		//传递mis_message_user的id
		$mis_message_user_id=$_REQUEST['id'];
		//将mis_message_user的id转化成mis_message的id
		$id = getFieldBy($mis_message_user_id,'id','sendid','mis_message_user');
		//传递标题
		$title=$_REQUEST['title'];

		//传递反馈的内容
		$content=$_REQUEST['content'];
	
		$MisMessageModel=M("MisMessage");
		$list=$MisMessageModel->where("id=".$id)->find();
		if($list['createid']==0){
			echo '{"error":"系统邮件，请勿回复！"}';
			exit;
		}
		if($replyType=='sender'){
			$recipient=$list['createid'];
			//查询收件人的中文名
			$recipientname= getFieldBy($list['createid'],'id','name','user');
			//这里专门组装收件人id数组
			$senderList=array(0=>$list['createid']);
		}else if($replyType=='all'){
             //待处理
		}else{
			echo '{"error":"回复范围有误！"}';
			exit;
		}
		//组装要插入数据
		$data=array();
		$data['title']=$title;//主题
		$data['recipient']=$recipient; //收件人
		$data['recipientname']=$recipientname; //收件人中文名
		$data['content']=$content;
		$data['commit']=1;
		$data['createid']=$this->userid; //当前登录用户

		$data['createtime']=time();
		$data['replyid']=$id;//回复邮件ID
		$data['isreply']=1;//表示回复
        //调用了内核create函数，需要避过表单令牌控制（因为没有前端表单）

		C('TOKEN_ON',false);		
		if (false === $MisMessageModel->create ($data)) {
               echo '{"error":"1邮件信息数据获取有误"}';
               exit;
		}
		$result=$MisMessageModel->add();

		if($result){
			echo '{"error":"数据有误"}';
			exit;
		}else{
			//插入成功 mis_message_user
			$MisMessageUserModel=D("MisMessageUser");
			foreach ($senderList as $k=>$v){
				$udata['recipient']=$v; //收件人
				$udata['sendid']=$result; //关联邮件ID
				$udata['commit']=1;
				$udata['createid']=$this->userid; //当前登录用户
				$udata['createtime']=time();
				if (false === $MisMessageUserModel->create ($udata)) {
					echo '{"error":"邮件用户数据获取有误"}';
					exit;
				}
				$ure=$MisMessageUserModel->add();
				if($ure === false){
					echo '{"error":"数据有误"}';
					exit;
				}
			}
			echo '{"sucess":"成功"}';
			exit;
		}
	
	}
	//取最新通知公告
	/**
	 * @Title: SystemNotices
	 * @Description: todo(系统公告)
	 * 并且排除已读的数据
	 * @author 杨东
	 * @date 2013-3-15 下午5:45:07
	 * @throws
	 */
	public function getNewNotices($returnType='json'){
		//获取当前登录人部门id 角色id
		$userModel=M("user");
		$userList=$userModel->where("id=".$this->userid)->find();
		$snmodel = M('MisSystemAnnouncement');
		$snumodel = M('MisSystemAnnouncementUser');
		$map['status']=array("gt",-1);
		$map['commit'] = array("eq",1);
		$time=time();
		$map['endtime']=array(array('eq',0),array('gt',$time),'or');
		$map['starttime']=array('lt',$time);
		if( $this->userid!=1){//不是管理员 只能看到在范围内的公告
			$map['_string']="( (scopetype=2 and ( (find_in_set('".$userList['dept_id']."',deptid) or find_in_set('".$userList['id']."',personid)) or createid=".$this->userid."  ) ) or (scopetype=3))";
		}
		//是否有审核开关
		if(getCommonSettingSkey("SYSTEM_ANNOUNCEMENT")){
			
		}
		$sql="SELECT
		`mis_system_announcement`.`id` as id,
		`mis_system_announcement`.`type` as `type`,
		`mis_system_announcement`.`title` as title,
		`user`.`name` as username,
		`mis_system_announcement`.`createtime` as createtime
		FROM `mis_system_announcement`
		LEFT JOIN `user`
		ON `mis_system_announcement`.createid=`user`.id
		WHERE (`mis_system_announcement`.`status` >  - 1)
		AND (`mis_system_announcement`.`commit` = 1)
		AND ((`mis_system_announcement`.`endtime` = 0)
		OR (`mis_system_announcement`.`endtime` > ".time()."))
		AND (`mis_system_announcement`.`starttime` < ".time().")
		ORDER BY `mis_system_announcement`.sendtime DESC
		LIMIT 0,10";
		//此处获取返回数据
		$returnData = $snmodel->query($sql);
		//现在重新拼装带URL的返回值数据
		foreach($returnData as $key => $val){
			$maps['userid']=$this->userid;
			$maps['announceid']=$val[id];
			$maps['status']=1;
			$num=$snumodel->where($maps)->count();
			if($num>0){
				$returnData[$key]['status']="1";
			}else{
				$returnData[$key]['status']="0";
			}						
			$returnData[$key]['urldata']="MisSystemPanelMethod,systemNoticesview,id,".$returnData[$key]['id'].",type,".$returnData[$key]['type'];
		}
		return $this->getReturnData($returnData,$returnType);
	}
	
	/**
	 * @Title: getNoticeDetail
	 * @Description: todo(获得系统公告详细信息，返回的是json)
	 * @param unknown_type $id  公告id
	 * @author xiafengqin
	 * @date 2014-4-3 下午3:45:52
	 * @throws
	 */
	public function getNoticeDetail(){
		//获取系统公告ID
		$id=$_REQUEST['id'];
		//判断当前用户是否已查看 未查看则添加记录
		$mSAUmodel=M("mis_system_announcement_user");
		$readStatus=$mSAUmodel->where("userid=".$this->userid." and announceid=".$id)->getField("status");
		$readData=array();
		$readData['userid']=$this->userid;
		$readData['announceid']=$id;
		$readData['status']=1;
		if($readStatus == NULL){//新增
			$mSAUmodel->data($readData)->add();
		}else if($readStatus == 0){//状态为未读时
			$mSAUmodel->where("userid=".$this->userid." and announceid=".$_REQUEST['id'])->setField ( 'status', 1 );
		}
		
		$MisSystemAnnouncementModel = D('MisSystemAnnouncement');
		$map = array();
		$map['status'] = 1;
		$map['commit'] = 1;
		$map['id'] = $id;
		$returnData = $MisSystemAnnouncementModel->where( $map )->field('id,title,createid,createtime,content')->find();
		if($returnData){
			//声明相关附件表
			$modelMAR = M('MisAttachedRecord');
			//获取附件信息
			$num=0;
			$map = array();
			$map["status"]  =1;
			$map["orderid"] =$returnData['id'];
			$map["type"] =78;
			$attarry=$modelMAR->field("upname,attached")->where($map)->select();
			if($attarry){
				$returnData['attach']=array();
				foreach($attarry as $attkey => $attval){
					$returnData['attach'][$num]['attachname']=$attval['upname'];
					//注意这里的路径，必须是__APP__."/MisSystemAnnouncement/misFileManageDownload/：对应模型名称，不能是__URL__."/misFileManageDownload/；将取不到数据
					$returnData['attach'][$num]['attachurl']=__APP__."/MisSystemAnnouncement/misFileManageDownload/path/".base64_encode($attval['attached'])."/rename/".$attval['upname'];
					$num++;
				}
			}else{
				//你存在数据是赋予空值
				$returnData['attach']=null;
			}		
		}
		//将id转化成人名，将时间戳转换成日期格式,将部门取出
		$returnData['username'] = getFieldBy($returnData['createid'],'id','name','user');
		$deptid = getFieldBy($returnData['createid'],'id','dept_id','user');
		$returnData['deptname'] = getFieldBy($deptid,'id','name','mis_system_department');
		$returnData['createtime'] = transTime($returnData['createtime']);
		$returnData['content'] = strip_tags($returnData['content']);
		unset($returnData['createid']);
		if($returnData){
			//echo '[{"error":"没有找到数据"}]';
			echo json_encode($returnData);
		}else{
			echo '{"error":"没有找到数据"}';
		}
	}
	//取联系人信息
	public function getPersonInfo(){
		$deptname = $_REQUEST['deptname'];
		$searchkey = $_REQUEST['searchkey'];
		$personid = $_REQUEST['personid'];
		if(!empty($searchkey)){
			$returnData = A("MisHrBasicEmployee")->getMessages($personid,$searchkey);
			if($returnData){
				echo json_encode($returnData);
			}else{
				echo '[{"error":"没有找到数据"}]';
			}
				
		}else{
			$returnData = A("MisHrBasicEmployee")->getMessages($personid);
			echo json_encode($returnData);
		}
	}
	//获取，部门列表
	public function getDepartment($returnType='json'){
		$getPerson = $_REQUEST['getPerson'];
		$deptid =  $_REQUEST['id'];
		$phone =  $_REQUEST['phone'];
		if($getPerson){
			$returnData = A("MisSystemDepartment")->getMessages($deptid);
			if(!$phone){
				//都是一级的所以是显示成文件夹图标
				foreach($returnData as $k=>$v){
					$returnData[$k]['icon']="./images/group.png";
					$returnData[$k]['name']=$returnData[$k]['name']."-".$returnData[$k]['phone'];
				}
			}
		}else{
			$returnData = A("MisSystemDepartment")->getMessages();
		}
		header("Content-type: text/html; charset=utf-8");
		return $this->getReturnData($returnData,$returnType);
	}
	//获取，部门列表，手机显示
	public function getDepartmentPhone($returnType='json'){
		$returnData = A("MisSystemDepartment")->getPhoneMessages();
		header("Content-type: text/html; charset=utf-8");
		return $this->getReturnData($returnData,$returnType);
	}
	//获取，部门列表，手机显示mis_sales_project
	public function getProjectPhone(){
		$returnData = A("MisSalesProject")->getPhoneMessages();
		header("Content-type: text/html; charset=utf-8");

		if($returnData){

			echo json_encode($returnData);
				
		}else{

			echo '[{"error":"没有找到数据"}]';
		}

	}
	
	//获取，公司列表，手机中显示
	public function getPhoneCompanyMessages(){
		$returnData = A("MisSystemDepartment")->getPhoneCompanyMessages();
		header("Content-type: text/html; charset=utf-8");

		if($returnData){

			echo json_encode($returnData);
				
		}else{

			echo '[{"error":"没有找到数据"}]';
		}

	}
	/**
	 * 对array进行json编码
	 *
	 * @param mixed $array
	 * @return 返回 array 值的 JSON 形式
	 * 可以写成类方式调用如下： $json = new json (); $data = $json->json_encode ( $value );
	 */
	function json_encode(&$array) {
		$this->do_urlencode ( $array );
		$str = json_encode ( $array );
		$str = urldecode ( $str );
		return $str;
	}

	/**
	 * 递归遍历var,对var进行url编码
	 *
	 * @param mixed $var
	 */
	private function do_urlencode(&$var) {
		if (is_array ( $var )) {
			// 数组,继续遍历
			foreach ( $var as $key => &$value ) {
				$this->do_urlencode ( $value );
			}
		} else {
			// 非数组,进行url编码
			$var = urlencode ( $var );
		}
	}
	//获取，邮件，待办，公告信息
	public function getEmailAndWorks(){
// 		$returnDataEmail = A("MisMessageInbox")->getMessages(10,0,$this->userid);
// 		//返回了mis_message_user表的id(邮件指向)，createid发件人,createtime发件时间，messageType(邮件类型),mis_message表的title（主题）
// 		//现在重新拼装带URL的返回值数据
// 		foreach($returnDataEmail as $key => $val){
// 			if($val['messageType'] == 1){
// 				$messageType = "inboxsystem";
// 			}
// 			$returnDataEmail[$key]['urldata']="MisMessage,index,id,".$val['id'].",messageType,".$messageType.";MisMessage;MisMessage";
// 		}
// 		$returnDataWorks=A("MisSystemPanelMethod")->setMethod('getWillWorks');
		$returnDataEmail=$this->getMessages("arr");
		$returnDataWorks=$this->getWillWorks("arr");
		$returnDataNotices=$this->getNewNotices("arr");
		$newArray = array();
		$newArray['email'] = $returnDataEmail;
		$newArray['works'] = $returnDataWorks;
		$newArray['notices'] = $returnDataNotices;
		if($newArray){
			echo json_encode($newArray);
		}else{
			echo '[{"error":"没有找到数据"}]';
		}
	}
	//获取，邮件，待办，公告信息，手机使用
	public function getEmailAndWorksPhone(){
		//file_put_contents('a.log',var_export($_REQUEST,1),FILE_APPEND);
		$returnDataEmail=$this->getMessages("arr");
		$returnDataWorks=$this->getWillWorks("arr");
		$returnDataNotices=$this->getNewNotices("arr");
		
		//把通知公告信息过滤，读过的删除了
		$noticesNumber=0;
		foreach($returnDataNotices as $k=>$v){
			if($v['status']<1){
				$noticesNumber++;
			}	
		}		
		$newArray = array();
		$newArray['email'] = count($returnDataEmail);
		$newArray['works'] =  count($returnDataWorks);
		$newArray['notices'] =  $noticesNumber;
		if($newArray){
			echo json_encode($newArray);
		}else{
			echo '[{"error":"没有找到数据"}]';
		}
	}
	
	public  function setPsw(){
		//存在$this->userid
		if($this->userid){
			$_POST['id']=$this->userid;
			$_POST['password']=$_POST['newpassword'];
			A("user")->resetPwd();
			echo '[{sucess:成功}]';
		}else{
			echo '[{"error":"非法操作！"}]';
		}
	}

	public function updateApp(){
		//RPC_DIR：文件路径
		//updateFileUrl： 最终的地址是http://.....
		//const RPC_DIR = __APP__
		//$siteUrl = $_REQUEST['siteurl'];
		$siteUrl = "http://192.168.0.101/Public";
		$version = $_REQUEST['gp_ver'];
		$platform = $_REQUEST['gp_platform'];
		//file_put_contents('a.log',var_export($_REQUEST,1),FILE_APPEND);
		if(empty($version) || !isset($platform)) return;
		//$filename = str_replace('rpc', '', RPC_DIR).'./widget.xml';
		$filename = PUBLIC_PATH.'Uploads/phoneApp/widget.xml';
		if(file_exists($filename)) {
			$c = file_get_contents($filename);
			$xml = simplexml_load_string($c);
			foreach($xml as $k => $v) {
				$arr[$k] = strval($v);
			}
			//preg_match('/<version>(.*?)</version>/', $c, $matches);

			$newver = $arr['version'];

			// preg_match('/<iphone_filename>(.*?)</iphone_filename>/', $c, $matches);
			$iphone_filename =  $arr['iphone_filename'];

			preg_match('/<android_filename>(.*?)</android_filename>/', $c, $matches);
			$android_filename = $arr['android_filename'];

			if($newver > $version) {
				if($platform == '0') { //iphone
					$fileurl = $iphone_filename;
				} elseif($platform == '1') { //android
					$fileurl = $android_filename;
				}

				$filesize = filesize($filename);
				if(empty($filesize)) exit;

				/*
				 if(empty($filesize)) exit;
				exit(iconv($mcharset,'utf-8','<?xml version="1.0" encoding="utf-8" ?><results><updateFileName>房掌柜</updateFileName><updateFileUrl>'.$fileurl.'</updateFileUrl><fileSize>'.$filesize.'</fileSize><version>'.$newver.'</version></results>'));
				}else exit('1');
				*/
				// echo '{"result":"1","name":"12121","size":"","url":"1121212","version":"12121"}';
				//exit;
				echo "<?xml version='1.0' encoding='utf-8' ?>
						<results>
						<updateFileName>phoneApp</updateFileName>
						<updateFileUrl>".$siteUrl."/Uploads/phoneApp/".$fileurl."</updateFileUrl>
								<fileSize>".$filesize."</fileSize>
										<version>".$newver."</version>
												</results>";
			}
			exit;
		}
	}
	
	////////////////////////////手机接口

	/**
	 * @Title: searchPerson
	 * @Description: todo(手机人员搜索接口)
	 * @author yangl
	 * @date 2014-4-4  上午 11:50
	 * @throws
	 */
	public function searchPerson(){
		//file_put_contents('a.log',var_export($_REQUEST,1),FILE_APPEND);
		//post得到要查找的项目id
		$data  = $_REQUEST['searchKey'];
		$MHPPIModel = D("MisHrPersonnelPersonInfo");
		//根据输入的内容查询对应的信息
		$map['name|pinyin|phone|shortNumber'] = array("like","%".$data."%");
		$map['_string'] = 'status = 1';
		$returnData = $MHPPIModel->where($map)->field('name,phone,shortNumber,officeNumber,email')->select();
		//print_r($MHPPIModel->getLastSql());
		if($returnData){
			echo json_encode($returnData);
		}else{
			echo '[{"error":"没有找到数据"}]';
		}
	}
	
	/**
	 * @Title: getProjectUser 
	 * @Description: todo(手机获得项目人员信息，返回的是json) 
	 * @param unknown_type $projectid  项目ID
	 * @author xiafengqin 
	 * @date 2014-4-3 下午3:14:30 
	 * @throws
	 */
	public function getProjectUser(){
		//post得到要查找的项目id
		$projectid  = $_REQUEST['id'];
		//项目人员主表信息信息
		$MisSalesProjectUserModel = D('MisSalesProjectUser');
		//项目人员子表信息
		$MisSalesProjectUserOutinModel = D('MisSalesProjectUserOutin');
		//user视图
		$userview = D('MisHrPersonnelPersonInfoView');
		$map = array();
		//是否审核通过
		$map['auditstatus'] = 1;
		//是够审核
		$map['isaudit'] = 1;
		//不要调出
		$map['isoutin'] = array('NEQ',2);
		//是否被替换(更换角色)
		$map['aboutof'] = array('NEQ',-1);
		$map['status'] = 1;
		$map['projectid'] = $projectid;
		$projectuserid = $MisSalesProjectUserOutinModel->where($map)->field('projectuserid,id,personid')->select();
		foreach ($projectuserid as $key=>$val) {
			unset($map);
			$map['hrid'] = $val['personid'];
			$map['status'] = 1;
			$vo = $userview->where($map)->field('name,phone,officeNumber,shortNumber,email')->find();
			$voList[] = $vo;
		}
		//return json_encode($voList);
		if($voList){
			echo json_encode($voList);
		}else{
			echo '[{"error":"没有找到数据"}]';
		}
	}

}