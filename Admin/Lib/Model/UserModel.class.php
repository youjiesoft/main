<?php
/**
 * @Title: file_name
 * @Package package_name
 * @Description: todo(后台用户模型)
 * @author liminggang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-11-26 上午10:27:14
 * @version V1.0
 */
class UserModel extends CommonModel {

	public $_validate	=	array(
			array('account','/^[a-z]\w{1,50}$/i','用户名必须是字母，且2位以上'),
			array('password','require','密码必须'),
			array('name','require','昵称必须'),
			//array('repassword','require','确认密码必须'),
			array('account,status','','英文帐号已经存在',self::MUST_VALIDATE,'unique',self::MODEL_BOTH),
			array('zhname,status','','中文帐号已经存在',self::MUST_VALIDATE,'unique',self::MODEL_BOTH),
			array('account,employeid,status','','帐号与员工已绑定',self::MUST_VALIDATE,'unique',self::MODEL_BOTH),
	);

	public $_auto		=	array(
			array('password','pwdHash',self::MODEL_BOTH,'callback'),
			array('last_login_time','time',self::MODEL_INSERT,'function'),
			array('work_date','time',self::MODEL_INSERT,'function'),
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
		    array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),

	);
	/**
	 * @Title: getUserPic
	 * @Description: 根据 后台用户ID，获取用户头像  如果不存在头像，讲默认调取系统头像。
	 * @param int $userid 后台用户ID
	 * @return 返回后台用户头像路径
	 * @author 黎明刚
	 * @date 2014年10月16日 上午10:48:06
	 * @throws
	*/
	public function getUserPic($userid){
		//系统默认男头像
		$man = 	__ROOT__."/Public/Images/xyimages/organization/user_male.jpg";
		//系统默认女头像。
		$woman = __ROOT__."/Public/Images/xyimages/organization/user_female.jpg";

		if($userid){
			$eployeid = $this->where('id ='.$userid)->getField("employeid");
			//实例化人事表信息
			$modelemploye = M("mis_hr_personnel_person_info");
			$pict = $modelemploye->field("sex,picture")->where("id=".$eployeid)->find();
				
			$pic = $pict['picture']?is_file($pict['picture'])?$pict['picture']:($pict['sex']==1?$man:$woman):($pict['sex']==1?$man:$woman);
				
		}else{
			$pic = $man;
		}
		return $pic;
	}

	protected function returnIspur($tempval){
		if($tempval){
			return 1;
		}else{
			return 0;
		}
	}
	protected function pwdHash() {
		if(isset($_POST['password'])) {
			return pwdHash($_POST['password']);
		}else{
			return false;
		}
	}
	public function setApp($data=array()){
		$field=$this->getFile();
		$list = require $field;
		$list[]=$data;
		$this->writeover($field,"return ".$this->pw_var_export($list).";\n",true);
	}
	/**
	 * 获取文件
	 * @return 文件路径
	 * */
	public function getFile(){
		return DConfig_PATH . "/System/appconfig.inc.php";
	}
	/**
	 * @Title: checkFindPassword
	 * @Description: todo(核验是否正确)
	 * @author yangxi
	 * @date 2014-5-7 上午11:36:03
	 * @throws
	 */
	public function checkFindPassword($account,$name,$val){
		$UserModel=D('User');
		$list=array();
		switch ($name){
			case 'account':
				$UserList=$UserModel->where(" status=1 and account ='".$val."' or  zhname='".$val."'")->find();
				if($UserList){
					//查询设置问题
					if($UserList['questionpwd']){
						$list['questionpwd']=unserialize($UserList['questionpwd']);
						$list['eamil']="验证提示.将往".$UserList['email']."发送重置密码，请查收!";
					}else{
						$list['questionpwd'][]=array(
								'questionpwd'=>'请输入您的中文用户名？',
								'answerpwd'=>$UserList['name'],
								'ischeck'=>-2,
								'eamil'=>"验证提示.将往".$UserList['email']."发送重置密码，请查收!",
						);
					}
				}else{
					$list['name']="错误提示.该用户名不存在!";
					$list['color']="red";
				}
				echo json_encode($list);
				break;
			case 'eamil':
				//查询该用户是否设置邮箱
				$UserList=$UserModel->where(" status=1 and account ='".$account."' or  zhname='".$account."'")->getField("email");
				if($UserList){
					$list['name']="验证提示.将往".$UserList."发送重置密码，请查收!";
					$list['color']="green";
				}else{
					$list['name']="错误提示.邮箱未设置，请联系管理员!";
					$list['color']="red";
				}
				echo json_encode($list);
				break;
			case 'mobile':
				//查询该用户是否设置邮箱
				$UserList=$UserModel->where(" status=1 and account ='".$account."' or  zhname='".$account."'")->getField("mobile");
				if($UserList){
					$list['name']="验证提示.将往".$UserList."发送重置密码，请查收!";
					$list['color']="green";
				}else{
					$list['name']="错误提示.手机未设置，请联系管理员!";
					$list['color']="red";
				}
				echo json_encode($list);
				break;
			case 'verify':
				if( C("VERIFICATION_CODE") && $_SESSION['verify'] != md5($val)) {
					$list['name']="错误提示.验证码不正确!";
					$list['color']="red";
					echo json_encode($list);
				}else{
					echo 2;
				}
				break;
			default:
				if($name=='-2'){
					//默认问题 真实姓名
					$UserList=$UserModel->where(" status=1 and name='".$val."' and account ='".$account."' or  zhname='".$account."'")->find();
					if(!$UserList){
						$list['name']="错误提示.验证问题回答错误!";
						$list['color']="red";
						echo json_encode($list);
					} else{
						echo 2;
					}
				}else{
					//回答问题
					$UserList=$UserModel->where(" status=1 and account ='".$account."' or  zhname='".$account."'")->find();
					$questionpwdList=unserialize($UserList['questionpwd']);
					if($val!=$questionpwdList[$name]['answerpwd']){
						$list['name']="错误提示.验证问题回答错误!";
						$list['color']="red";
						echo json_encode($list);
					}else{
						echo 2;
					}
				}
				break;
		}

	}

	/**
	 * @Title: saveLogoutUserTime
	 * @Description: todo(注销登陆账号时，修改后台用户信息) 此方法在PublicAction中调用
	 * @return bool  true , false
	 * @author liminggang
	 * @date 2014-8-28 下午5:06:01
	 * @throws
	 */
	public function saveLogoutUserTime(){
		$data = array();
		$data['isonline'] = 0;
		$data['sessionid'] = "";
		$data['leavetime'] = time();
		$data['id']	= $_SESSION[C('USER_AUTH_KEY')];
		$result=$this->save($data);
		return $result;
	}
	/**
	 * @Title: getGroupModuleTree
	 * @Description: todo(构造属性结果 by)
	 * @param unknown_type $group_id
	 * @param unknown_type $nodedata
	 * @param unknown_type $pid
	 * @param unknown_type $alreadyaccess
	 * @param unknown_type $parendchecked
	 * $param 数RUL跳转的方法 $func
	 * @return multitype:
	 * @author laicaixia
	 * @date 2013-5-31 下午6:28:54
	 * @throws
	 */
	public function getUserGroupModuleTree($group_id,$nodedata,$pid,$alreadyaccess,&$parendchecked=false, $func = 'roleAccess'){
		$returnarr=array();
		foreach($nodedata as $k => $v) {
			if($v['group_id'] !=$group_id) continue;
			if( $pid ){
				if($pid!=$v['pid'])continue;
			}
			$newv=array();
			$newv['id']=$v['id'];
			$newv['title']=$v['title']; //光标提示信息
			$newv['name']=missubstr($v['title'],12); //结点名字，太多会被截取
			$newv['open']=false;
			if($v['type'] == 3 ){
				if($v['pid']==$pid){
					$newv['pId']=$pid;
					unset($nodedata[$k]);
					$newv['url']="__URL__/" . $func . "/jump/1/id/".$v['id']."/md/".$v['name']."/userid/".$_GET['userid'];
					$newv['type']='post';
					$newv['target']='ajax';
					if ($func == 'roleAccess') {
						$newv['rel']="userroleaccessright";
					} else {
						$newv['rel'] = $func . "userroleaccessright";
					}
					$newv['type']=3;
					if(in_array($v['id'],$alreadyaccess)){
						$newv['checked']=true;
						if(!$parendchecked) $parendchecked = $v['id'];
						$newv['open']=true;
					}
					array_push($returnarr,$newv);
				}
			} else if($v['type'] == 1 ){
				$newv['pId']=100000+$group_id;
				unset($nodedata[$k]);
				$newv['type']=1;
				$s1_2= $this->getUserGroupModuleTree($group_id,$nodedata,$v['id'],$alreadyaccess,$parendchecked, $func);
				if(in_array($v['id'],$alreadyaccess)){
					$newv['checked']=true;
					if(!$parendchecked) $parendchecked = $v['id'];
					$newv['open']=true;
				}
				array_push($returnarr,$newv);
				if($s1_2){
					$returnarr=array_merge($returnarr,$s1_2);
				}
			} else if($v['type'] == 2 ){
				if($v['pid']==$pid){
					$newv['pId']=$pid;
					unset($nodedata[$k]);
					$newv['type']=2;
					$s2_2=$this->getUserGroupModuleTree($group_id,$nodedata,$v['id'],$alreadyaccess,$parendchecked, $func);
					if(in_array($v['id'],$alreadyaccess)){
						$newv['checked']=true;
						if(!$parendchecked) $parendchecked = $v['id'];
						$newv['open']=true;
					}
					array_push($returnarr,$newv);
					if($s2_2){
						$returnarr=array_merge($returnarr,$s2_2);
					}
				}
			}
		}
		return $returnarr;
	}
	/**
	 *
	 * @Title: getNodeIdRole
	 * @Description: todo(查询用户该节点权限)
	 * @author renling
	 * @date 2014-9-4 下午4:12:13
	 * @throws
	 */
	public function getNodeIdRole($id,$role_roleidlist){
		$roleModel = D( "Role" );
		$map = array();
		$map['status'] = 1;
		$map['nodepid'] =$id;
		$module_rolelist = $roleModel->where($map)->select();//获取对应模块下的所以角色组
		$nodecategory=M("nodecategory");
		// <--筛选最大权限的roleid 开始 -->
		$isrolelist = array();
		foreach ($module_rolelist as $k=>$v ) {
			if(in_array($v['id'],$role_roleidlist)){
				$isrolelist[] = $v['id'];
			}
		}
		$map= array();
		$map['id'] = array('in', $isrolelist);
		$alreadyrolelist = $roleModel->where($map)->getField("id,plevels,nodeid");
		$rolelist = array();
		$role_roleidlist = array();
		foreach ($alreadyrolelist as $key => $val){
			if ($rolelist[$val['nodeid']]) {
				if ($rolelist[$val['nodeid']]['plevels'] >= $val['plevels']) {
					$rolelist[$val['nodeid']] = $val;
					$role_roleidlist[$val['nodeid']] = $val['id'];
				}
			} else {
				$rolelist[$val['nodeid']] = $val;
				$role_roleidlist[$val['nodeid']] = $val['id'];
			}
		}
		// <--筛选最大权限的roleid 结束 -->
		$data=array();
		foreach($module_rolelist as $k=>$v ){
			if(in_array($v['id'],$role_roleidlist)){
				$v["already_role"]=1;
			}else{
				$v["already_role"]=0;
			}
			if( isset($data[$v["nodeidcategory"]]) ){
				if( $v["already_role"] ) $data[$v["nodeidcategory"]]["already_role_count"]+=1;
				$data[$v["nodeidcategory"]]["count"]+=1;
				$data[$v["nodeidcategory"]]["child"][$v["nodeid"]][]=$v;
			}else{
				$title = $nodecategory->where("id=".$v["nodeidcategory"])->getField("name");
				if( !$title ) continue;
				$data[$v["nodeidcategory"]]=array();
				$data[$v["nodeidcategory"]]["title"]=$title;
				$data[$v["nodeidcategory"]]["count"]=1;
				$data[$v["nodeidcategory"]]["already_role_count"]=0;
				if( $v["already_role"] ) $data[$v["nodeidcategory"]]["already_role_count"]=1;
				$data[$v["nodeidcategory"]]["child"][$v["nodeid"]]=array();
				$data[$v["nodeidcategory"]]["child"][$v["nodeid"]][]=$v;
			}
		}
		return $data;
	}
	public function getUserCurrentMsg(){




	}
	/**
	 *
	 * @Title: getAccessfilter
	 * @Description: todo(查询数据权限)
	 * @author renling
	 * @date 2014-9-4 下午4:12:13
	 * @throws
	 */
	public function getAccessfilter($qx_name,$map,$tableView){
		$acitonname=ACTION_NAME;
		if($acitonname=="autoPanelShow"){
			$acitonname="index";
		}
		if($tableView){
			if( $_SESSION[strtolower($qx_name.'_'.$acitonname)]!=1 ){
				if( $_SESSION[strtolower($qx_name.'_'.$acitonname)]==2 ){
					////判断公司权限
					if($_SESSION['companyid']){
						$map[$tableView."companyid"]=$_SESSION['companyid'];
					} else {
						$map[$tableView."companyid"] = -1;
					}
				}else if($_SESSION[strtolower($qx_name.'_'.$acitonname)]==3){//判断部门权限
					if($_SESSION['user_dep_id']) {
						$map[$tableView."departmentid"]=$_SESSION['user_dep_id'];
					} else {
						$map[$tableView."departmentid"]=-1;
					}
				}else if($_SESSION[strtolower($qx_name.'_'.$acitonname)]==4){//判断个人权限
					$map[$tableView."createid"]=$_SESSION[C('USER_AUTH_KEY')];
					$map[$tableView."companyid"]=$_SESSION['companyid'];
				}
			}
		}else{
			if( $_SESSION[strtolower($qx_name.'_'.$acitonname)]!=1 ){
				if( $_SESSION[strtolower($qx_name.'_'.$acitonname)]==2 ){
					////判断公司权限
					if($_SESSION['companyid']){
						$map["companyid"]=$_SESSION['companyid'];
					} else {
						$map["companyid"] = -1;
					}
				}else if($_SESSION[strtolower($qx_name.'_'.$acitonname)]==3){//判断部门权限
					if($_SESSION['user_dep_id']) {
						$map["departmentid"]=$_SESSION['user_dep_id'];
					} else {
						$map["departmentid"]=-1;
					}
				}else if($_SESSION[strtolower($qx_name.'_'.$acitonname)]==4){//判断个人权限
					$map["createid"]=$_SESSION[C('USER_AUTH_KEY')];
					$map["companyid"]=$_SESSION['companyid'];
				}
			}
		}
		return $map;
	}
	public  function getConfNotAuth($type){
		require  DConfig_PATH.'/System/notauth.php';
		if($type){
			//传送不用授权的方法
			return $notauth['NOT_AUTH_ACTION'];
		}else{
			//传送不用授权的模块
			return $notauth['NOT_AUTH_MODUL'];
		}
	}
	/**
	 * 
	 * @Title: setSelectUser
	 * @Description: todo(新版选组组件保存数据) 
	 * @param unknown $modelname
	 * @param unknown $tableid
	 * @param unknown $fieldname  
	 * @author renling 
	 * @date 2015年3月21日 下午7:33:32 
	 * @throws
	 */
	public function setSelectUser($modelname,$tableid,$fieldname){
		$MisSystemSelectuserDao=M("mis_system_selectuser");
		$fieldList=$_POST[$fieldname];
		 if($fieldList){
		 	foreach ($fieldList as $key=>$val){
		 		$date=array();
		 		//获取选择用户 recipientname recipient  groupname groupid
		 		$date['fieldname']=$val;
		 		$date['tableid']=$tableid;
		 		$date['modelname']=$modelname;
		 		if($_POST['recipientname'][$val]){
			 		$recipientname=implode(',', $_POST['recipientname'][$val]);
			 		$recipient=implode(',', $_POST['recipient'][$val]);
			 		$date['typeval']=$recipient;
			 		$date['typename']=$recipientname;
			 		$date['typeid']=1;//人
			 		if($_POST['valid'][$val][1]){
			 			$date['updateid']=$_SESSION[C('USER_AUTH_KEY')];
			 			$date['updatetime']=time();
			 			$date['id']=$_POST['valid'][$val][1];
			 			$result=$MisSystemSelectuserDao->save($date); 
			 		}else{
			 			$date['createid']=$_SESSION[C('USER_AUTH_KEY')];
			 			$date['createtime']=time();
			 			$result=$MisSystemSelectuserDao->add($date); 
			 		}
			 		
		 		}else if($_POST['valid'][$val][1]){
		 			//存在组数据  请求删除
		 			$result=$MisSystemSelectuserDao->where("id=".$_POST['valid'][$val][1])->delete();
		 		}
		 		if($_POST['groupname'][$val]){
		 			unset($date['id']);
			 		$groupname=implode(',', $_POST['groupname'][$val]);
			 		$groupid=implode(',', $_POST['groupid'][$val]);
			 		$date['typeval']=$groupid;
			 		$date['typename']=$groupname;
			 		$date['typeid']=2;//组
		 			if($_POST['valid'][$val][2]){
			 			$date['updateid']=$_SESSION[C('USER_AUTH_KEY')];
			 			$date['updatetime']=time();
			 			$date['id']=$_POST['valid'][$val][2];
			 			$result=$MisSystemSelectuserDao->save($date); 
			 		}else{
			 			$date['createid']=$_SESSION[C('USER_AUTH_KEY')];
			 			$date['createtime']=time();
			 			$result=$MisSystemSelectuserDao->add($date); 
			 		}
		 		}else if($_POST['valid'][$val][2]){
		 			//存在组数据  请求删除
		 			$result=$MisSystemSelectuserDao->where("id=".$_POST['valid'][$val][2])->delete();
		 		}
		 	}
		 }
		 return $result;
	}
	/**
	 * 
	 * @Title: getShowSelectUser
	 * @Description: todo(获取显示人员) 
	 * @param unknown $modelname
	 * @param unknown $tableid  
	 * @author renling 
	 * @date 2015年3月23日 下午4:41:55 
	 * @throws
	 */
	public function  getShowSelectUser($modelname,$tableid){
		//查询当前模型
		$MisSystemSelectuserModel=M("mis_system_selectuser");
		$Map['status']=1;
		$Map['tableid']=$tableid;
		$Map['modelname']=$modelname;
		$MisSystemSelectuserList=$MisSystemSelectuserModel->where($Map)->select();
		$list=array();
		foreach ($MisSystemSelectuserList as $key=>$val){
			if($val['typeval']){
				$listArr=array();
				if($val['typeid']==1){
					$listArr=array(
						'valid'=>$val['id'],
						'inputname'=>'recipientname',
						'inputval'=>'recipient',
						'typename'=>explode(',', $val['typename']),
						'typeval'=>explode(',',$val['typeval']),
						'typeid'=>1,
					);
				}else if($val['typeid']==2){
					$listArr=array(
							'valid'=>$val['id'],
							'inputname'=>'groupname',
							'inputval'=>'groupid',
							'typename'=>explode(',', $val['typename']),
							'typeval'=>explode(',',$val['typeval']),
							'typeid'=>2,
					);
				}
				$list[$val['fieldname']][]=$listArr;
			}
		}
		return $list;
	}
	/**
	 * 
	 * @Title: getSelectUserList
	 * @Description: todo(组成真实使用的用户id) 
	 * @param unknown $modelname
	 * @param unknown $tableid  
	 * @author renling 
	 * @date 2015年3月25日 下午2:22:29 
	 * @throws
	 */
	public function getSelectUserList($modelname,$tableid){
		//查询当前模型
		$MisSystemSelectuserModel=M("mis_system_selectuser");
		//组成真实用户id 实时获取
		$rolegroup_userModel = M('rolegroup_user');
		$rolegroupModel = D('Rolegroup');
		//$rolegroupList = $rolegroupModel->where("status=1")->order("id asc")->field('id,name,pid')->select();//所有的组
		$rolegroup_userList = $rolegroup_userModel->field("rolegroup_id,user_id")->order('rolegroup_id ASC')->select();
		//查询当前数据中选择的用户组
		$Map['status']=1;
		$Map['tableid']=$tableid;
		$Map['modelname']=$modelname;
		$MisSystemSelectuserList=$MisSystemSelectuserModel->where($Map)->select();
		foreach ($MisSystemSelectuserList as $k => $v) {
			if($v['typeid']==1){
				$userArr=explode(',', $v['typeval']);
				foreach ($userArr as $userkey=>$userval){
					$rolegroupList[$v['fieldname']][$userval]=$userval;
				}
			}
			if($v['typeid']==2){
				$roleList=explode(',',$v['typeval']);
				foreach ($roleList as $rk=>$rv){
					foreach ($rolegroup_userList as $k2 => $v2) {
						if($rv == $v2["rolegroup_id"]){
								$rolegroupList[$v['fieldname']][$v2["user_id"]] = $v2["user_id"];
						}
					}
				}
			} 
		}
		  return $rolegroupList;
		
	}
}
?>