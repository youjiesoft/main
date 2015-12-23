<?php
/**
 * @Title: MisMessageAction
 * @Package package_name
 * @Description: todo(站内信)
 * @author liminggang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2012-12-20 上午10:16:55
 * @version V1.0
 */
class MisMessageAction extends CommonAction{
	public function index(){
		//$_REQUEST['messageType'] = "inboxself";
		if(!$_REQUEST['messageType']){
			//查询所有的部门
			$this->searchuser();
		}
		$this->display();
	}

	public function _before_index(){
		//数据库对象
		$modelMMU = D('MisMessageUser');
		$map['status'] = 1;
		$map['commit'] = 1;
		$map['returnmessage']=1;//邮件未撤回
		$map['readedStatus'] = 0;
		$map['recipient'] = $_SESSION [C ( 'USER_AUTH_KEY' )];
		//取出系统消息，数量
		$map['messageType']=1; //显示系统消息
		$countSystemMessage = $modelMMU->where ( $map )->count ( '*' );
		$this->assign("countSystemMessage",$countSystemMessage);

		//取出收件箱内消息，数理
		$map['messageType']=0; //不显示系统消息
		$countInboxMessage = $modelMMU->where ( $map )->count ( '*' );
		$this->assign("countInboxMessage",$countInboxMessage);
		
		//取出重要邮件，数理
		$map['important']=1; //不显示系统消息
		$importantInboxMessage = $modelMMU->where ( $map )->count ( '*' );
		$this->assign("importantInboxMessage",$importantInboxMessage);
		//把当前人的邮箱服务器传到前台。
		$configEmailModel = D('MisSystemEmail');
		$configMap = array();
		$configMap['status'] = array('GT',0);
		$configMap['userid'] = $_SESSION [C ( 'USER_AUTH_KEY' )];
		$configMap['defaultemail'] = 1;
		$url = $configEmailModel->where($configMap)->getField('server',true);
		$this->assign('url',$url[0]);//只会有一个邮箱地址，
	}

	public function searchuser(){
		$mode1=D("User");
		$map = array();
		$map['status'] = array('GT',0);
		//是管理员的不显示出来
		$map['name'] = array('NEQ','管理员');
		$list=$mode1->field("id,name,dept_id,email")->where($map)->order('sort ASC')->select();
		$returnarr = array();
		$dptmodel = D("MisSystemDepartment");//部门表
		$deptlist = $dptmodel->where("status=1")->order("id asc")->field('id,name,parentid')->select();
		foreach($deptlist as $k=>$v){
			$newv=array();
			$newv['id'] = -$v['id'];
			$newv['pId'] = -$v['parentid'] ? -$v['parentid']:0;
			$newv['title'] = $v['name']; //光标提示信息
			$newv['name'] = missubstr($v['name'],18,true); //结点名字，太多会被截取,针对于现在的ZTREE，宽度不能超过18个字符。
			if($v['parentid'] == 0){
				$newv['open'] = $v['open'] ? false : true;
			}
			$istrue = false;
			$userarr = array();
			$usernamearr = array();
			$emailarr = array();//邮箱
			// 构造用户
			foreach ($list as $k2 => $v2) {
				if($v2['dept_id'] == $v['id']){
					$newv2 = array();
					$userarr[] = $v2['id'];//将用户的名字和id分别存在数组中
					$usernamearr[] = $v2['name'];
					$emailarr[] = $v2['email'];
					$newv2['email'] = $v2['email'];
					$newv2['id'] = $v2['id'];
					$newv2['pId'] = -$v['id'];
					$newv2['title'] = $v2['name']; //光标提示信息
					$newv2['name'] = missubstr($v2['name'],20,true); //结点名字，太多会被截取,针对于现在的ZTREE，宽度不能超过18个字符。
					$newv2['icon'] = "__PUBLIC__/Images/icon/group.png";
					$newv2['open'] = true;
					array_push($returnarr,$newv2);
				}
			}
			$newv["userid"] = implode(",",$userarr);
			$newv["email"] = implode(",",$emailarr);
			$newv["username"] = implode(",",$usernamearr);//把取到名字和id转换成字符串，在传到前台。
			array_push($returnarr,$newv);
		}
		$this->assign('usertree',json_encode($returnarr));
		//用户组的树
		$rolegroup = array();
	 	$rolegroupModel = D('Rolegroup');
		$rolegroupList = $rolegroupModel->where("status=1")->order("id asc")->field('id,name,pid')->select();//所有的组
		$rolegroup_userModel = M('rolegroup_user');
		$rolegroup_userList = $rolegroup_userModel->field("rolegroup_id,user_id")->order('rolegroup_id ASC')->select();
		foreach ($rolegroupList as $k => $v) {
			foreach ($rolegroup_userList as $k2 => $v2) {
				if($v["id"] == $v2["rolegroup_id"]){
					$rolegroupList[$k]["useridarr"][] = $v2["user_id"];
				}
			}
		}
		foreach($rolegroupList as $ke=>$va){
			$newRole=array();
			$newRole['id'] = -$va['id'];
			$newRole['pId'] = 0;
			$newRole['title'] = $va['name']; //光标提示信息
			$newRole['name'] = missubstr($va['name'],18,true); //结点名字，太多会被截取,针对于现在的ZTREE，宽度不能超过18个字符。
			$newRole['open'] = false;
			$istrue = false;
			$userarr = array();
			$usernamearr = array();
			$emailarr = array();
			foreach ($list as $k2 => $v2) {
				if(in_array($v2['id'],$va["useridarr"])){
					$istrue = true;
					$newv2 = array();
					$userarr[] = $v2['id'];
					$usernamearr[] = $v2['name'];
					$emailarr[] = $v2['email'];
					$newv2['email'] = $v2['email'];
					$newv2['id'] = $v2['id'];
					$newv2['pId'] = -$va['id'];
					$newv2['title'] = $v2['name']; //光标提示信息
					$newv2['name'] = missubstr($v2['name'],18,true); //结点名字，太多会被截取,针对于现在的ZTREE，宽度不能超过18个字符。
					$newv2['icon'] = "__PUBLIC__/Images/icon/group.png";
					$newv2['open'] = false;
					array_push($rolegroup,$newv2);
				}
			}
			if($istrue){
				$newRole["userid"] = implode(",",$userarr);
				$newRole["email"] = implode(",",$emailarr);
				$newRole["username"] = implode(",",$usernamearr);
				array_push($rolegroup,$newRole);
			}
		}
		$this->assign('rolegrouptree',json_encode($rolegroup));
		//角色的树
		$ProcessRole = array();
		$ProcessRoleModel = D('ProcessRole');
		$ProcessRoleList = $ProcessRoleModel->where("status=1")->order("sort asc")->field('id,name,deptid,userid')->select();//所有的角色组
		
		foreach($ProcessRoleList as $ke=>$va){
			$newRole=array();
			$newRole['id'] = -$va['id'];
			$newRole['pId'] = 0;
			$newRole['title'] = $va['name']; //光标提示信息
			$newRole['name'] = missubstr($va['name'],18,true); //结点名字，太多会被截取,针对于现在的ZTREE，宽度不能超过18个字符。
			$newRole['open'] = false;
			$istrue = false;
			$deptid = explode(",",$va['deptid']);
			$userid = explode(",",$va['userid']);
			$userarr = array();
			$usernamearr = array();
			$emailarr = array();
			foreach ($list as $k2 => $v2) {
				$istrue2 = false;
				if(in_array($v2['id'],$userid)){
					$istrue2 = true;
				}
				if($istrue2){
					$istrue = true;
					$newv2 = array();
					$userarr[] = $v2['id'];
					$usernamearr[] = $v2['name'];
					$emailarr[] = $v2['email'];
					$newv2['email'] = $v2['email'];
					$newv2['id'] = $v2['id'];
					$newv2['pId'] = -$va['id'];
					$newv2['title'] = $v2['name']; //光标提示信息
					$newv2['name'] = missubstr($v2['name'],18,true); //结点名字，太多会被截取,针对于现在的ZTREE，宽度不能超过18个字符。
					$newv2['icon'] = "__PUBLIC__/Images/icon/group.png";
					$newv2['open'] = false;
					array_push($ProcessRole,$newv2);
				}
			}
			if($istrue){
				$newRole["userid"] = implode(",",$userarr);
				$newRole["email"] = implode(",",$emailarr);
				$newRole["username"] = implode(",",$usernamearr);
				array_push($ProcessRole,$newRole);
			}
		}
		$this->assign('ProcessRoletree',json_encode($ProcessRole));
	}

	//只是个测试的，可以删除
	public function testPushMessage(){
		$userID = array('1','2'); //接收人ID数组
		$messageTitle = "eagle"; //系统信息标题
		$messageContent = "eagle push message to system."; //
		$result = $this->pushMessage($userID,$messageTitle,$messageContent);
		if($result){
			$this->transaction_model->commit();//事务提交
			echo "推送成功";
		}
		else{
			$this->transaction_model->rollback();
			echo "推送失败";
		}


	}

	
	public function _before_insert(){
		/* if(!$_POST['sendmessage']){ //区分组织机构添加 */
		if ($_POST['recipient'] || $_POST['copytopeopleid']){
			if ($_POST['recipient']) {
				if(is_array($_POST['recipient'])){
				    $_POST['recipient'] = implode(',', $_POST['recipient']);				   
				}
				if(is_array($_POST['recipientname'])){
				    $_POST['recipientname'] = implode(',', $_POST['recipientname']);
				}
			}
			if ($_POST['copytopeopleid']) {
				$_POST['copytopeopleid'] = implode(',',$_POST['copytopeopleid']);
				$_POST['copytopeoplename'] = implode(',',$_POST['copytopeoplename']);
			}
		}else {
			$this->error ( '请添加收件人！' );
		}
		/* } */
	}
	//保存到草稿箱时用这个方法
	public function _after_insert($insertid){
		$modelMMU = D('MisMessageUser');
		if ($insertid) {
			$this->swf_upload($insertid,64);
		}
		//提交类型，是放入草稿是发件箱
		if( $_POST['commit']){
			$user = explode(',', $_POST['recipient']);
			$copyuser = explode(',', $_POST['copytopeopleid']);
			$user = array_merge($user, $copyuser);
			//去重复
			$user = array_unique($user);
			//把当前发件人的ID也存入数组 ,注意：前面的 ”-“ 号是有作用的,标记是放在发件箱里的数据。
			$user[] = -$_SESSION [C ( 'USER_AUTH_KEY' )];
			//$user[] = 0;
			$usermodel =M("user");
			foreach ($user as $k => $v) {
				$_POST['recipient'] = $v;
				$_POST['sendid'] = $insertid;
				//用create对应一下
				if (false === $modelMMU->create ()) {
					$this->error ( $modelMMU->getError () );
				}
				$list1=$modelMMU->add ();
				$usermodel->where("id=".$v)->setField("newmsg",1);
			}
		} else {
			//$user = explode(',', $_POST['recipient']);
			//把当前发件人的ID也存入数组   注意：前面的 ”-“ 号是有作用的,标记是放在发件箱里的数据。
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

	//编写消息方法
	public function writeForm(){
		//查询所有的部门,显示在选人列表
// 		$this->searchuser();
		$this->display();
	}

	//test
	public function _after_update($insertid){

		dump($_POST);
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
		$map['working']=1;
		$map['user.status']=1;
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
	 * @Title: messageconfig 
	 * @Description: todo(外部邮件的邮箱配置)   
	 * @author xiafengqin 
	 * @date 2013-8-14 下午5:22:10 
	 * @throws
	 */
	public function messageconfig(){
		$model = D ('misSystemEmail');
		$map = array();
		$map['userid'] = $_SESSION[C('USER_AUTH_KEY')];
		$map['status'] = 1;
		$list = $model->where($map)->select();
		$this->assign('list',$list);
		$this->display();
	}
	/**
	 * @Title: insertmessageconfig 
	 * @Description: todo(对于邮箱信息配置的新增，修改的作用)   
	 * @author xiafengqin 
	 * @date 2013-8-19 下午3:48:31 
	 * @throws
	 */
	public function insertmessageconfig(){
		if ($_POST['id']) {
			$ary = explode('@',$_POST['email']);
			$_POST['address'] = $ary[0];
			$_POST['server'] = $ary[1];
			
			$model = D ( 'misSystemEmail' );
			if (false === $model->create ()) {
				$this->error ( $model->getError () );
			}
			if ($_POST['defaultemail'] == 1){
				$defaultemail = array();
				$defaultemail['defaultemail'] = 0;
				$result = $model->where('userid='.$_POST['userid'])->save($defaultemail);
			}
			// 更新数据
			$list=$model->save ();
			$userModel = D('User');
			//把user表里面的email改了
			$isEmail = $userModel->where('id='.$_POST['userid'])->setField('email',$_POST['email']);
			if (false !== $list && $isEmail !== false) {
				$this->success ( L('_SUCCESS_'));
			} else {
				$this->error ( L('_ERROR_') );
			}
		}else{
			$scnmodel = D('SystemConfigNumber');
			$_POST['code'] = $scnmodel->GetRulesNO('mis_system_email');
			$_POST['userid'] = $_SESSION[C('USER_AUTH_KEY')];
			$_POST['name'] = getFieldBy($_POST['userid'],id,name,'user');
			$ary = explode('@',$_POST['email']);
			$_POST['address'] = $ary[0];
			$_POST['server'] = $ary[1];
			
			$name = 'misSystemEmail';
			$model = D ($name);
			if (false === $model->create ()) {
				$this->error ( $model->getError () );
			}
			//保存当前数据对象
			$defaultemail = array();
			$defaultemail['defaultemail'] = 0;
			$result = $model->where('userid='.$_POST['userid'])->save($defaultemail);
			$list=$model->add ();
			$userModel = D('User');
			$isEmail = $userModel->where('id='.$_POST['userid'])->setField('email',$_POST['email']);
			if ($list!==false && $isEmail !== false) {
				$mrdmodel = D('MisRuntimeData');
				$mrdmodel->setRuntimeCache($_POST,$name,'add');
				$module2=A($name);
				if (method_exists($module2,"_after_insertmessageconfig")) {
					call_user_func(array(&$module2,"_after_insertmessageconfig"),$list);
				}
				$this->success ( L('_SUCCESS_') ,'',$list);
				exit;
			} else {
				$this->error ( L('_ERROR_') );
			}
		}
	}
	/**
	 * @Title: lookupeditmessageconfig 
	 * @Description: todo(当页面点击修改，就将id异步到此方法中)   
	 * @author xiafengqin 
	 * @date 2013-8-19 下午3:50:52 
	 * @throws
	 */
	public function lookupeditmessageconfig(){
		$map = array();
		$map['id'] = $_POST['id'];
		$map['status'] = 1;
		$model = D ('misSystemEmail');
		$result = $model->where($map)->find();
		echo json_encode($result);
	}
	/**
	 * @Title: lookupdeletemessageconfig 
	 * @Description: todo(当页面点击删除，就将id异步到此方法中，将status的值改为-1)   
	 * @author xiafengqin 
	 * @date 2013-8-19 下午5:12:18 
	 * @throws
	 */
	public function lookupdeletemessageconfig(){
		$model = D ('misSystemEmail');
		if (! empty ( $model )) {
			$id = $_REQUEST ['id'];
			$list=$model->where ( 'id='.$id )->setField ( 'status', - 1 );
			if ($list!==false) {
				$this->success( L('_SUCCESS_'));
			} else {
				$this->error( L('_ERROR_'));
			}
		}
	}
	/**
	 * @Title: insertInterEmail
	 * @Description: todo(发送外部邮件，需授权)
	 * @author xiafengqin
	 * @date 2013-8-28 上午10:54:06
	 * @throws
	 */
	public function insertInterEmail(){
		$email = $_POST['email'];
		if (!$email) {
			$this->error ( '邮件地址不能为空！' );
		}
		if (!$_POST['content']) {
			$this->error ( '邮件内容不能为空！' );
		}
		if ($_POST['messageTypeNmae']) {
			$configEmailModel = D('MisSystemEmail');
			$map = array();
			$map['status'] = 1;
			$map['defaultemail'] = 1;
			$map['userid'] = $_SESSION[C('USER_AUTH_KEY')];
			$vo = $configEmailModel->where($map)->find();
		}
		$attachmentlistnew = array();
		foreach ($_POST['swf_upload_save_name'] as $key => $val) {
			$path = pathinfo($val);
			$oldpath = UPLOAD_PATH_TEMP . $_POST['swf_upload_save_name'][$key];
			$newpath = UPLOAD_PATH_TEMP . $path['dirname'] . '/' . $_POST['swf_upload_source_name'][$key];
			if(file_exists($oldpath)){
				$isrename = rename($oldpath,$newpath);
				if ($isrename) {
					$attachmentlistnew[] = $newpath;
				}
			}
		}
		if ($vo) {
			$result = $this->SendEmail($_POST['title'], $_POST['content'], $_POST['email'], $attachmentlistnew, $vo, $_POST['messageTypeNmae']);
		}else {
			$this->error( "请正确的配置邮箱，且有一个默认的邮箱！");
		}
		if($result){
			if ($_POST['recipient']) {
				if(is_array($_POST['recipient'])){
					$_POST['recipient'] = implode(',', $_POST['recipient']);
				}
				if(is_array($_POST['recipientname'])){
					$_POST['recipientname'] = implode(',', $_POST['recipientname']);
				}
			}
			if ($_POST['copytopeopleid']) {
				$_POST['copytopeopleid'] = implode(',',$_POST['copytopeopleid']);
				$_POST['copytopeoplename'] = implode(',',$_POST['copytopeoplename']);
			}
			$this->insert();
			$this->success( "发送成功！");
		} else {
			$this->error( "发送失败，请联系管理员!");
		}
	}
}

?>