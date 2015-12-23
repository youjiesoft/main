<?php
/**
 * @Title: file_name
 * @Package package_name
 * @Description: todo(后台用户模块)
 * @author liminggang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2012-12-22 下午3:21:33
 * @version V1.0
 */
class UserAction extends AuthorizeCoreAction {
	function _filter(&$map){
		if ($_SESSION["a"] != 1){
			$map['status']=array("gt",-1);
		}
	}
	public function index() {
		//构成树形菜单
		$model = D('MisSystemDepartment');
		$typeTree=$model->getDeptZtree('','true',"userindexjbsxBox",'',"/frame/1");
		$this->assign('typeTree',$typeTree);
		$MisSystemCompanyModel=D("MisSystemCompany");
		$MisSystemCompanyid=$MisSystemCompanyModel->getCompanyOne();
		//由于重写了_before_index方法需要重新将搜索模板获取功能重写一遍
		$name = $this->getActionName();
		$scdmodel = D('SystemConfigDetail');
		$searchinc = $scdmodel->getDetail($name,false,'search');
		$detailList = $scdmodel->getDetail($name);
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		//配置检索
		$map = $this->_search();
		$map['companyid']=$_REQUEST['companyid']?$_REQUEST['companyid']:$MisSystemCompanyid;
		$this->assign("MisSystemCompanyid",getFieldBy($map['companyid'], "companyid", "id", "mis_system_department","iscompany",1));
		$map['_string'] = "user.id<>'' or user.id<>0";
		$map['user_dept_duty.status']=1;
		$map['dstatus']=1;
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$dept_id = $_REQUEST['deptid'];
		if ($dept_id) {
			if($_REQUEST['companyid']){
				$deptmap=array();
				$deptmap['companyid']=$_REQUEST['companyid'];
			}
			$deptmap["status"]=1;
			$deptlist=$model->where($deptmap)->select();
			$deptlist =array_unique(array_filter (explode(",",$this->findAlldept($deptlist,$dept_id))));
			$map['deptid'] = array(' in ',$deptlist);
			$this->assign("deptid",$dept_id);
		}
		//验证浏览及权限
		if( !isset($_SESSION['a']) ){
			$map=D('User')->getAccessfilter($qx_name,$map);
		}
		$this->_list ( "MisHrPersonnelUserDeptView", $map );
		if ($_REQUEST['frame']) {
			$this->display('indexlist');
		} else {
			$this->display();
		}
	}
// 	//获取部门
// 	public function _before_add(){
// 		//获取公司、部门、岗位三级联动
// 		$MisSystemDepartmentModel=D('MisSystemDepartment');
// 		$deptComList=$MisSystemDepartmentModel->getDeptCombox();
// 		$this->assign("deptanComList",$deptComList);
// 	}
	
	/**
	 * @Title: _before_insert 
	 * @Description: todo(插入数据之前，进行手机验证判断)   
	 * @author liminggang 
	 * @date 2013-11-26 上午9:45:22 
	 * @throws
	 */
	public function _before_insert() {
		$_POST['account'] = $_POST['account2'];
		$_POST['zhname'] = $_POST['name'];
		//查询该员工是否已存在用户ｉｄ
		if(getFieldBy($_POST['employeid'], "employeid", "id", "user")){
			$this->error("已有该员工的账号信息,请勿重复添加！");
		}
		//赋予默认初始密码
		$_POST['password'] = C('USER_DEFAULT_PASSWORD');
		//获取辅助角色
		$attachrole=$_POST['attachrole'];
		//主角色
		$role_id = $_POST['role_id'];
		//根据人事ID获取岗位ID
		$employeid=$_POST['employeid'];
		$worktype = getFieldBy($employeid,'id','worktype','mis_hr_personnel_person_info');
		//根据岗位ID获取相对应的角色ID
		$rolegroupid = getFieldBy($worktype,'id','rolegroup_id','mis_hr_job_info');
		//判断主角色是否相同，相同则不需要加入
		if($rolegroupid && $role_id && $role_id != $rolegroupid){
			if(!$attachrole){
				$attachrole = array();
				array_push($attachrole,$rolegroupid);
			}
		}
		if($attachrole){
			array_push($attachrole,$role_id);
			$_POST['attachrole']=implode(",",array_unique($attachrole));
		}else{
			$_POST['attachrole']=$role_id;
		}
	}
	

	// 插入数据后
	public function _after_insert($id) {
		//反写userid到人事表 by xyz 2015-10-22
		$personinfoModel = M("mis_hr_personnel_person_info");
		$pimap['id'] = $_POST['employeid'];
		$pidata['userid'] = $id;
		$pret = $personinfoModel->where($pimap)->save($pidata);
		if(false === $pret){
			$this->error("反写userid到人事表失败");
		}
		//
		
		if(getFieldBy($_POST['employeid'], "employeid", "id", "user")){
			if(C('SWITCH_SENIOR_ROLE_GROUP')=='On'){
				// 插入用户初始化高级权限组
				$this->addRolegroup($id,C("USER_SENIOR_ROLE_GROUP"));
			}
			$UserDeptDutyModel=D("UserDeptDuty");
			$RolegroupUserModel=D('RolegroupUser');
			//获取辅助角色
			$role_id=explode(",", $_POST['attachrole']);
			$UserDeptDutyMap['employeid']=$_POST['employeid'];
			$UserDeptDutyMap['status']=1;
			$result=$UserDeptDutyModel->where($UserDeptDutyMap)->setField('userid',$id );
			foreach ($UserDeptDutyList as $key=>$val){
				$RolegroupDate=array();
				$RolegroupDate['user_id']=$id;
				$RolegroupDate['rolegroup_id']=getFieldBy($val['worktype'], "id", "rolegroup_id", "mis_hr_job_info");
				$RolegroupUserResult=$RolegroupUserModel->add($RolegroupDate);
				if(!$RolegroupUserResult){
					$this->error("权限组插入错误！");
				}
			}
			//查询当前主角色是否已入角色表
			$map=array();
			$map['user_id']	=$id;	
			$map['rolegroup_id']	=$_POST['role_id'];
			$aresult=$RolegroupUserModel->where($map)->find();
			if(!$aresult){
				$roledate=array();
				$roledate['user_id']=$id;
				$roledate['rolegroup_id']=$_POST['role_id'];
				$RolegroupUserResult=$RolegroupUserModel->add($roledate);
				if(!$RolegroupUserResult){
					$this->error("主角色插入错误！");
				}
			}
		}
		if(!$result){
			//$this->error("用户部门职级关系创建失败");
		}
		//name字段拼音生成
		$this->getPinyin('user','name');
	}

	/**
	 * @Title: lookrolegroup 
	 * @Description: todo(获取角色，部门，职级内容方法)   
	 * @author liminggang 
	 * @date 2013-11-26 上午9:34:13 
	 * @throws
	 */
	public function lookrolegroup(){
		//查询获取角色
		$obj=$_REQUEST['obj'];
		$stepType=$_REQUEST['stepType'];
		$this->assign('obj',$obj);
		$objname=$_REQUEST['objname'];
		$this->assign('objname',$objname);
		$this->assign('stepType',$stepType);
		$map = array();
		$searchby = $_POST["searchby"];
		$keyword= $_POST["keyword"];
		if($keyword){
			$map[$searchby] = array('like','%'.$keyword.'%');
			$this->assign('keyword',$keyword);
			$this->assign('searchby',$searchby);
		}
		$searchby=array(
				array("id" =>"name","val"=>"按角色名称"),
			);
		$this->assign("searchbylist",$searchby);
		
		$this->_list("rolegroup", $map);
		$this->display();
	}
	
	/**
	 * @yangxi
	 * 20120906
	 * remark:将用户加入到对应的基础权限组
	 * @parameter  string  $userId    用户ID
	 * @parameter  array   $roleGroup 权限组
	 */
	protected function addRole($userId,$roleGroup="") {
		if($roleGroup!=""){
			foreach($roleGroup as $key => $value){
				//新增用户自动加入相应权限组
				$RoleUser = M("role_user");
				$RoleUser->user_id	=	$userId;
				// 默认加入高级权限组
				$RoleUser->role_id	=	$value;
				$result=$RoleUser->add();
				if(!$result){
					$this->error("基础权限授予失败");
				}
			}
		}
	}
	/*
	 * 修改之前
	*/
	public function _before_edit(){
		$id=$_REQUEST['id'];
		//查询获取角色
		$RolegroupDao = M("rolegroup");
		$rolegrouplist=$RolegroupDao->where("status = 1")->field("id,name")->select();
		$this->assign("rolegrouplist",$rolegrouplist);
// 		//获取公司、部门、岗位三级联动
// 		$MisSystemDepartmentModel=D('MisSystemDepartment');
// 		$deptComList=$MisSystemDepartmentModel->getDeptCombox();
// 		$this->assign("deptanComList",$deptComList);
	}
	
	public function _after_edit($vo){
		if($vo){
			//将用户，部门，职级关联表
			$UserDeptDutyModel = M("user_dept_duty");
			//查询关系表中的数据
			$map = array();
			$map['userid'] = $vo['id'];
			$map['deptid'] = $vo['dept_id'];
			$map['dutyid'] = $vo['duty_id'];
			$map['typeid'] = 1;
			$deptdutyid=$UserDeptDutyModel->where($map)->getField('id');
			$this->assign('deptdutyid',$deptdutyid);
			if(!$vo['attachrole'] && !$vo['role_id']){
				//以前的老数据，则需要手动到rolegroup_user 取出角色
				$RolegroupUserDao = M("rolegroup_user");
				$usergrouplist=$RolegroupUserDao->where('user_id = '.$vo['id'])->select();
				foreach ($usergrouplist as $key=>$val){
					if($key == 0){
						$vo['role_id'] = $val['rolegroup_id'];
					}else{
						$attachrole[] = $val['rolegroup_id'];
						$this->assign('attachrole',$attachrole);
					}
				}
			}else{
				//获取辅助角色
				$attachrole = array_filter(array_diff(explode(',',$vo['attachrole']),array($vo['role_id'])));
				$this->assign('attachrole',$attachrole);
			}
		}
		$_REQUEST['employeeid']=$vo["employeid"];
		$this->lookupuserdept();
	}
	
	/**
	 * @Title: _before_update
	 * @Description: todo(修改之前进行验证)
	 * @author liminggang
	 * @date 2013-7-10 下午6:39:30
	 * @throws
	 */
	public function _before_update(){

		$_POST['account'] = $_POST['account2'];
		$_POST['zhname'] = $_POST['name'];
		//查询获取角色
		$RolegroupModel = M("rolegroup_user");
		//获得用户ID
		$userid = $_POST['id'];
		//获取新的
		$attachrole=$_POST['attachrole'];
		//获取辅助角色
		$old_attachrole = explode(",", $_POST['old_attachrole']);  //1,2,3
		//$add_role 为新增角色，$del_role为移除角色
		$add_role = $del_role = array();
		if($attachrole){
			foreach($attachrole as $key=>$val){
				if(!in_array($val, $old_attachrole)){
					$add_role[] = $val;
				}
			}
			// 插入用户初始化高级权限组
			if($add_role){
				$this->addRolegroup($userid,$add_role);
			}
		}
		if($old_attachrole){
			foreach($old_attachrole as $k=>$v){
				if(!in_array($v, $attachrole)){
					$del_role[] = $v;
				}
			}
		}
		if($del_role){
			$map = array();
			//修改角色
			$map['rolegroup_id'] = array(' in ',$del_role);
			$map['user_id'] = $userid;
			$result=$RolegroupModel->where($map)->delete();
			if(!$result){
				$this->error("删除角色失败");
			}
		}
		//主角色
		$_POST['role_id'] = $_POST['attachrole']['0'];
		//获取辅助角色
		$_POST['attachrole']=implode(",",$attachrole);
		//获取主部门
		$dept_id = $_POST['dept_id'];
		//获取主职级
		$duty_id = $_POST['duty_id'];
		//用户部门职级表关联ID
		$deptdutyid=$_POST['deptdutyid'];
		
		//将用户，部门，职级关联表
		/* $UserDeptDutyModel = M("user_dept_duty");
		//查询关系表中的数据
		if(!$deptdutyid){
			$this->error("用户部门职级管理表ID获取失败");
		}
		$data = array();
 		$data = array(
 				'id'=>$deptdutyid,
 				'deptid'=>$dept_id,
 				'dutyid'=>$duty_id,
 				'updateid'=>$_SESSION[C('USER_AUTH_KEY')],
 				'updatetime'=>time(),
 			);
 		$result=$UserDeptDutyModel->save($data);
 		if(!$result){
 			$this->error("用户部门职级关系创建失败");
 		} */
	}

	/**
	 * @Title: _after_update
	 * @Description: todo(修改之后进行验证)
	 * @author liminggang
	 * @date 2013-7-10 下午6:39:30
	 * @throws
	 */
	public function _after_update() {
		$this->getPinyin('user','name',$_REQUEST['id']);
	}
	/**
	 * @Title: lookuproleView 
	 * @Description: todo(查看个人权限)   
	 * @author liminggang 
	 * @date 2013-11-26 上午9:47:38 
	 * @throws
	 */
	public function lookuproleView(){
		$nodeModel = D("Node");
		$groupModel = D("Group");
		$accessModel = D("Access");
		$roleuserModel = D("role_user");
		$userid = $_REQUEST['userid'];
		$this->assign('userid',$userid);
		$roleModel = D("Role");
		// <-- 组授权获取 获取role_id  开始 -->
		//获取用户 组授权 权限 id
		$rolegroup_userModel = D("RolegroupUser");
		$rolegroupidlist = $rolegroup_userModel->where('user_id = ' . $_REQUEST['userid'])->getField('rolegroup_id', true);
		//获取权限组id
		$role_rolegroupModel = D("RoleRolegroup");
		$map = array();
		$map['rolegroup_id'] = array('in', $rolegroupidlist);
		$role_roleidlist = $role_rolegroupModel->where($map)->getField('role_id', true);
		// <-- 组授权获取 获取role_id  结束 -->
		//一般授权获取权限 id
		$alreadyrole = $roleuserModel->where('user_id = ' . $_REQUEST['userid'])->getField("role_id",true);
		foreach ($alreadyrole as $val) {
			$role_roleidlist[] = $val;
		}
		//获取nodeid 即页面左边选中节点id
		$map = array();
		$map["role_id"] = array('in', $role_roleidlist);
		$alreadynode = $accessModel->where($map)->getField("node_id",true);
		$noderolelist = array_unique($alreadynode);
		// <-- 组织右边数据 开始 -->
		if( $_GET['id'] ){
			//查询该模块下 用户权限
			$UserModel=D('User');
			$data=$UserModel->getNodeIdRole($_GET['id'],$role_roleidlist);
			$vo = $nodeModel->find($_GET['id']);
			$this->assign("vo",$vo);
			$this->assign("list",$data);
			$this->assign("nodeid",$_GET["id"]);
			$this->assign("userid",$_GET['userid']);
			if($_GET['jump']){
				$this->display("userroleAccessRight");
				exit;
			}
		}
		//<-- 组织右边数据 结束 -->
		//<-- 组织页面左边树数据 开始 -->
		$list = $groupModel->where("status=1")->order("sorts asc")->select();
		$map = array();
		$map['status'] = 1;
		$map['level'] =array("neq",4);
		$map['pid'] =array("gt",0);
		$nodedata = $nodeModel->where($map)->order("sort asc")->select();
		$start_groupid=100000;
		//获取最顶级公司
		
		$returnarr[]=array('id'=>0,'pId'=>-1,'title'=>getFieldBy('0', 'parentid', 'name', 'mis_system_company'),'name'=>getFieldBy('0', 'parentid', 'name', 'mis_system_company'),'open'=>true,'nocheck'=>'true');
		foreach($list as $k =>$v){
			$parendchecked=false;
			$newv['id']=$v['id']+100000;
			$newv['pId']=0;
			$newv['title']=$v['name']; //光标提示信息
			$newv['name']=missubstr($v['name'],20,true); //结点名字，太多会被截取
			$newv['open']=$v['open'] ? $v['open']:'false';
			$newv['checked']="false";
			$newv['type']=0;
			$returnarr1= D('User')->getUserGroupModuleTree($v['id'],$nodedata,0,$noderolelist,$parendchecked, 'lookuproleView');
			if($parendchecked) $newv['checked']="true";
			array_push($returnarr,$newv);
			$returnarr=array_merge($returnarr,$returnarr1);
		}
		$valid= $_REQUEST['selectednodeid'] ? $_REQUEST['selectednodeid']:0;
		$this->assign("valid",$valid);
		$this->assign("userroleaccesstree",json_encode($returnarr));
		//<-- 组织页面左边树数据 结束 -->
		$this->display();
	}

	/**
	 * @Title: resetPwd
	 * @Description: todo(重置密码)
	 * @author yangxi
	 * @date 2013-7-10 上午12:40:52
	 * @throws
	 */
	public function resetPwd() {
		$id  = $_POST['id'];
		$password = $_POST['repassword'];
		if(!$password) {
			$this->error('密码不能为空！');
		}
		$User = M('User');
		$data=array(
				'id'=>$id,
				'password'=>md5($password),
		);
		$User->data($data);
		$result=$User->save();
		if(false !== $result) {
			$this->success("密码修改为$password");
		}else {
			$this->error('重置密码失败！');
		}
	}
	/**
	 * @Title: deptDuty
	 * @Description: todo(查看部门职级)
	 * @author liminggang
	 * @date 2013-12-3 上午9:36:41
	 * @throws
	 */
	public function deptDuty(){
		$UserDeptDutyDao = M("user_dept_duty");
		//获取用户ID
		$userid=$_REQUEST['userid'];
		$this->assign('userid',$userid);
		
		$deptlist = array();
		$method="deptDuty";
		$rel = "userDeptDuty_index";
		$url="/userid/".$userid;
		$ztree=$this->getTree($deptlist,$parm,$returnArr);
		$ztree = D('MisSystemDepartment')->getDeptZtree('','true',$rel,$method,$url);
		$this->assign('ztree',$ztree);
		unset($map);
		if($_REQUEST['deptid']){
			if($_REQUEST['companyid']){
				$deptmap=array();
				$deptmap['companyid']=$_REQUEST['companyid'];
				$this->assign('companyid',$_REQUEST['companyid']);
			}
			$deptmap["status"]=1;
			$deptlist=D('MisSystemDepartment')->where($deptmap)->select();
			$deptid=$_REQUEST['deptid'];
			$deptlist =array_unique(array_filter (explode(",",$this->findAlldept($deptlist,$deptid))));
			$map['deptid'] = array(' in ',$deptlist);
		}
		$map['user_dept_duty.userid'] = $userid;
		$this->_list("MisHrPersonnelUserDeptView", $map);
	
		if($_REQUEST['jump']){
			$this->display("deptDuty_right");
		}else{
			$this->display();
		}
	}
 
	/**
	 * @Title: lookupProcessRole 
	 * @Description: todo(查询流程角色)   
	 * @author liminggang 
	 * @date 2013-12-9 下午1:58:38 
	 * @throws
	 */
	public function lookupProcessRole(){
		$userid=$_REQUEST['userid'];
		$this->assign('userid',$userid);
		if(!$userid){
			$this->error("未获取到后台用户ID");
		}
		$ProcessRoleDao = M("process_role");
		//查询当前用户的流程角色
		$map['status'] = 1;
		$list=$ProcessRoleDao->where($map)->select();
		foreach($list as $k=>$v){
			if(!in_array($userid, explode(",", $v['userid']))){
				unset($list[$k]);
			}
		}
		$this->assign('list',$list);
		$this->display();
	}
	
// 	/**
// 	 * @Title: lookupSendMsg 
// 	 * @Description: todo(手机验证 暂保留此方法)   
// 	 * @author liminggang 
// 	 * @date 2013-12-3 上午9:29:13 
// 	 * @throws
// 	 */
// 	public function lookupSendMsg(){
// 		//随机生成6为数
// 		$rand=rand(100000,999999);
// 		//将这个随机数在cookie中保存20分钟，过期为失效处理
// 		Cookie::set ( 'random', $rand,1200);
// 		//第二，发送手机验证信息内容
// 		$content="您在特米洛企业信息化管理平台,后台用户手机验证码为".$rand;
// 		//获取手机号码
// 		$mobile=$_POST['mobile'];
// 		//验证手机号码是否有重复的。
// 		$UserDao=M('user');
// 		$count=$UserDao->where('mobile = '.$mobile)->count("*");
// 		if($count){
// 			echo false;
// 			exit;
// 		}
// 		$result=$this->SendTelmMsg($content, $mobile);
// 		if($result){
// 			echo true;
// 		}else{
// 			echo false;
// 		}
// 	}
	
	/**
	 * @Title: lookupUpdateUserRowAccess 
	 * @Description: todo(修改个人列权限方法  暂无用处 页面保留便后期使用)   
	 * @author liminggang 
	 * @date 2013-12-11 下午1:59:29 
	 * @throws
	 */ 
// 	public function lookupUpdateUserRowAccess(){
// 		//获得用户ID
// 		$userid=$_POST['userid'];
// 		//获得模型
// 		$modelName=$_POST['md'];
// 		$model = D('SystemConfigDetail');
// 		$issub = $_POST['issub'];
// 		if( $issub){
// 			$detailList = $model->getSubDetail($modelName,false);
// 		}else{
// 			$detailList = $model->getDetail($modelName,false);
// 		}
// 		if ($detailList) {
// 			$allowarr=$_POST['allow'];
// 			foreach( $detailList as $k=>$v ){
// 				$arr = $v["row_access"]?$v["row_access"]:array();
// 				if($allowarr[$k]==1){
// 					if(isset($arr["allow"])){
// 						if($userid){
// 							if( $arr["allow"]["userid"]!="" && isset($arr["allow"]["userid"])){
// 								$arr1 = explode(",",$arr["allow"]["userid"]);
// 								$arr2 = explode(",",$userid);
// 								$arr3 = array_merge($arr1,$arr2);
// 								$arr["allow"]["userid"] = implode(",",array_unique($arr3));
// 							}else{
// 								$arr["allow"]["userid"]= $userid;
// 							}
// 						}
// 					}else{
//  						$arr["allow"] = array();
// 						if($userid)$arr["allow"]['userid'] = $userid;
// 					}
// 					if(isset($arr["deny"])){
// 						$arr4 = array();
// 						if($arr["deny"]["userid"]!="" && isset($arr["deny"]["userid"])){
// 							$arr4 = explode(",",$arr["deny"]["userid"]);
// 							if($arr4 && in_array($userid,$arr4)){
// 								$denyarr=array_diff($arr4,array($userid));
// 								if($denyarr){
// 									$arr["deny"]["userid"] = implode(",",$denyarr);
// 								}else{
// 									unset($arr['deny']);
// 								}
// 							}
// 						}
// 					}
// 				}else{
// 					if(isset($arr["deny"])){
// 						if($userid){
// 							if($arr["deny"]["userid"]!="" && isset($arr["deny"]["userid"])){
// 								$arr1 = explode(",",$arr["deny"]["userid"]);
// 								$arr2 = explode(",",$userid);
// 								$arr3 = array_merge($arr1,$arr2);
// 								$arr["deny"]["userid"] = implode(",",array_unique($arr3));
// 							}else{
// 								$arr["deny"]["userid"]= $userid;
// 							}
// 						}
// 					}else{
// 						$arr["deny"] = array();
// 						if($userid)$arr["deny"]["userid"] = $userid;
// 					}
// 					if(isset($arr["allow"])){
// 						$arr4 = array();
// 						if($arr["allow"]["userid"] && isset($arr["allow"]["userid"])){
// 							$arr4 = explode(",",$arr["allow"]["userid"]);
// 							if($arr4 && in_array($userid,$arr4)){
// 								$allowrr=array_diff($arr4,array($userid));
// 								if($allowrr){
// 									$arr["allow"]["userid"] = implode(",",$allowrr);
// 								}else{
// 									unset($arr["allow"]);
// 								}
// 							}
// 						}
// 					}
// 				}
// 				$detailList[$k]["row_access"]=$arr;
// 			}
// // 			print_r($detailList);
// // 			$this->error("xxx");
// 			if( $issub){
// 				$model->setSubDetail($modelName, $detailList);
// 			}else{
// 				$model->setDetail($modelName, $detailList);
// 			}
// 			//删除缓存文件
// 			$p= C("DATA_CACHE_PATH")."Dynamicconf/Models/".$modelName;
		
// 			if(file_exists($p)){
// 				$FileUtil = new FileUtil();
// 				$boolean=$FileUtil->unlinkDir($p);
// 				if(!$boolean){
// 					$this->error("更新缓存文件失败");
// 				}
// 			}
// 			$this->success(L('_SUCCESS_'));exit;
// 		}else{
// 			$this->error("模块不存在!");
// 		}
// 	}
	/**
	 * @Title: lookupgetloginnumstatus 
	 * @Description: todo(禁用与否的操作)   
	 * @author xiafengqin 
	 * @date 2014-4-2 上午10:11:53 
	 * @throws
	 */
	public function lookupgetloginnumstatus(){
		$model = D('user');
		$id = $_REQUEST['id'];
		$loginnumstatus = $_REQUEST['loginnumstatus'];
		if ($loginnumstatus == 0){
			$result = $model->where('id='.$id)->setField('loginnumstatus',1);
		}else{
			$result = $model->where('id='.$id)->setField('loginnumstatus',0);
		}
		if ($result) {
			$this->success(L('_SUCCESS_'));
		}else{
			$this->error('操作失败');
		}
	}
	/**
	 * 
	 * @Title: lookupuserdept
	 * @Description: todo(动态获取员工多组织)   
	 * @author renling 
	 * @date 2014-10-27 下午6:23:50 
	 * @throws
	 */
	public function lookupuserdept(){
		$employeeid=$_REQUEST['employeeid'];
		//获取公司
		$UserDeptDutyModel=D("UserDeptDuty");
		$UserDeptDutyList=$UserDeptDutyModel->where(" typeid=1 and  status=1 and employeid=".$employeeid)->select();
		$this->assign("UserDeptDutyList",$UserDeptDutyList);
		if($_REQUEST['etype']==1){
			$this->display();
		}else if($_REQUEST['etype']==2){
			$userid=getFieldBy($employeeid, "employeid", "id", "user");
			echo $userid;
		}
	}
	/**
	 * 
	 * @Title: resetlogin
	 * @Description: todo(解除锁定)   
	 * @author renling 
	 * @date 2015年7月30日 下午2:21:41 
	 * @throws
	 */
	public function resetlogin(){
		$userid=$_REQUEST['id'];
		$date=array();
		$date['loginnumstatus']=0;
		$date['login_error_count']=0;
		if($userid){
			$result=D('User')->where("id=".$userid)->save($date);
			if($result){
				$this->success("解除成功！");
			}else{
				$this->error("数据异常！");
				
			}
		}
	}
}
?>