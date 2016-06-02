<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: 首页index核心控制器 
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-8-28 下午3:39:23 
 * @version V1.0
 * +-------------------------------------------
 * 此类作用，作用域，以及运用，如果非必须，一律不准放入此类中。 必须做到延伸扩展，禁止条件判断扩展
 * 
 * 此类只做一下操作。
 * 
 * 1、登陆验证       登陆验证分为2中。1、浏览器版，2、移动设备版。
 * 2、注销操作
 * 3、升级验证。
 * 4、缓存清理
 * 5、
 * +-------------------------------------------
 */
import ( '@.ORG.RBAC' );
import ( '@.ORG.Browse' );
class PublicAction extends PublicExtendAction {
	
	//全局变量，验证登陆方式，(登陆方式有2中，1、浏览器登陆，2、移动设备登陆。 )
	private $loginType='standardLogin';  //helperLogin
	//全局变量，存入空的模型对象
	public  $transaction_model='';   
	
	function _initialize() {
		$this->transaction_model=M();
	}

	public function auditDiv(){
		$this->display();
	}

	
	public function backDiv(){
		$this->display();
	}
	
// 	public function _empty() {
// 		header('HTTP/1.1 404 Not Found');
// 		$this->display('Public:404');
// 	}
	
	// 检查用户是否登录
	protected function checkUser() {
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
			$this->assign("jumpUrl",__APP__.'Public/login/');
			$this->error('用户没有登录或已超时1','','','301');
		}
	}
	
	// 修改资料
	public function change() {
		$this->checkUser();
		$User	 =	 D("User");
		if(!$User->create()) {
			$this->error($User->getError());
		}
		$result	=	$User->save();
		if(false !== $result) {
			$this->success('资料修改成功！');
		}else{
			$this->error('资料修改失败!');
		}
	}
	/**
	 * @Title: login 
	 * @Description: todo(首页登陆模板输出方法)   
	 * @author liminggang 
	 * @date 2014-8-28 下午4:14:36 
	 * @throws
	 */
	public function login() {
		$this->index();
		//$this->display();
	}
	
	/**
	 * @Title: login_dialog 
	 * @Description: todo(session过期后，弹出dialog登陆窗口)   
	 * @author liminggang 
	 * @date 2014-8-28 下午4:18:53 
	 * @throws
	 */
	public function login_dialog() {
		//登陆时，判断是否需要输入验证码。
		$this->assign('verificationcode',C("VERIFICATION_CODE"));
		$this->display();
	}

	/**
	 * @Title: login
	 * @Description: todo(首页登陆模板输出方法)
	 * @author liminggang
	 * @date 2014-8-28 下午4:14:36
	 * @throws
	 */
	public function regirster() {
		$this->index();
		//$this->display();
	}
	
	
	
	
	
	public function index() {
		$boolean = Cookie::get("userinfo");
		if(!$_SESSION[C('USER_AUTH_KEY')] && !$boolean) {
			//查看公司登陆LOGO
			//$CommonSettingModel=M('common_setting'); //配置表
			//$picList=$CommonSettingModel->where(" skey='companyico'")->find(); //图片
			//$nameList=$CommonSettingModel->where(" skey='companyname'")->find();//名称
			//$enameList=$CommonSettingModel->where(" skey='companyename'")->find();//英文名称
			//$this->assign("picList",$picList);
			//$this->assign("nameList",$nameList);
			//$this->assign("enameList",$enameList);
			$this->assign('verificationcode',C("VERIFICATION_CODE"));
			$this->display('login');
		}else{
			$this->assign("isLogin",1);
			$this->redirect('Index/index');
		}
	}
	/**
	 * @Title: logout 
	 * @Description: todo(登出方法)   
	 * @author liminggang 
	 * @date 2014-8-28 下午4:27:54 
	 * @throws
	 */
	public function logout(){
		/*
		 * 读取名人录数据   dirct yuansl 2014 06 05
		 * 此方法必须放在销毁session之前
		 */
		//$this->getsolidotinfor();
		//在登出后，修改登陆用户最后登出时间 begin
		$User = D('User');
		$result=$User->saveLogoutUserTime();
		//更新online信息,类型为删除
		$this->setUserOnline($authInfo="logout",$type="delete");
// 		if(!$result){
// 			$this->error("注销时清除用户信息失败");
// 		}
		//end
		//清除session
		session_unset();
		//销毁cookie
		Cookie::delete("userinfo");
		Cookie::clear();
		session_destroy();//释放当前内存
		$this->display("login");
// 		$this->success('注销成功！');
	}
	
	/**
	 * @Title: getAlldept
	 * @Description: todo(递归方法，获取所有部门信息)
	 * @param unknown_type $deptlist
	 * @param unknown_type $deptid
	 * @param unknown_type $hasparentsid
	 * @return string
	 * @author liminggang
	 * @date 2014-8-28 下午5:40:10
	 * @throws
	 */
	function getAlldept($deptlist,$deptid,$hasparentsid=true){
		if($deptid){
			$arr="";
			if( $hasparentsid ) $arr =",".$deptid;
			foreach ($deptlist as $k=>$v){
				if($v['parentid']==$deptid){
					unset($deptlist[$k]);
					$arr.=",".$v['id'].$this->getAlldept($deptlist,$v['id']);
				}
			}
			return $arr;
		}
	}
	/**
	 * @Title: helperSignin 
	 * @Description: todo(oa助手登录配制 (移动设备版登陆))    
	 * @author liminggang 
	 * @date 2014-8-28 下午4:45:14 
	 * @throws
	 */
	public  function  helperSignin(){
		//你要获取account跟password的code，并做好数据处理
		$_POST['account']=$_REQUEST['account'];
		$_POST['password']=$_REQUEST['password'];
		if(!empty($_REQUEST['fromOA'])){
			$this->loginType='helperLogin';
		}
		return $this->signin();
	}
	/**
	 * @Title: checkPwd 
	 * @Description: todo(验证密码是否正确) 
	 * @param unknown_type $authInfo  
	 * @author liminggang 
	 * @date 2014-8-28 下午6:15:24 
	 * @throws
	 */
	private function checkPwd($authInfo){
		$time = time();
		// 判断是否存在密码错误登录
		$User = M('User');
		if(!$authInfo['login_error_count']){
  			//修改当前登录用户最后登录时间
			$User->where('id='.$authInfo['id'])->setField('last_login_time',$time);
			$User->commit();
		}
		//获取登陆系统错误次数
		$login_error_count = $authInfo['login_error_count'];
		//计算当天密码错误次数保留
		if($authInfo['last_login_time']){
			$lasttimecount = $time - $authInfo['last_login_time'];
			if($lasttimecount < 86400){
				if(intval($authInfo['login_error_count'])!=5){
					$login_error_count = $login_error_count+1;
				}
			} else {
				$login_error_count = 1;
			}
		}
		//登陆方式为移动设备
		if($this->loginType=='helperLogin'){
			//本身登陆密码就是md加密方式
			$pwd = $_POST['password'];
		}else if($this->loginType=='checkLogin'){
			//本身登陆密码就是md加密方式
			$pwd = $_POST['password'];			
		}else{
			//浏览器版登陆当时，密码进行md5加密
			$pwd =  md5($_POST['password']);
		}
		//验证登陆密码是否正确。
		if($authInfo['password'] != $pwd ){		
			$data = array();
			$data['login_error_count'] = $login_error_count;
			$data['last_login_time'] = time();
			$error_count = 5;// 错误次数
			$login_errorsum_count = $error_count-intval($login_error_count);
			if($login_errorsum_count == 0 && $authInfo['account']!='admin'){
				$data['loginnumstatus'] = 1;
			}
			$userResult=$User->where("id=".$authInfo['id'])->save($data);
			$User->commit();
			//session_unset();
			unset($data);
// 			if(!$userResult){
// 				$this->error("修改用户登陆信息失败");
// 			}
			//判断当前用户不为超级管理员
			if($authInfo['account']!='admin') {
				if ($login_errorsum_count == 0) {
					if($this->loginType!='helperLogin'){
						if($this->loginType=='checkLogin'){
							$this->assign("jumpUrl",__APP__.'/Public/login/');
							$this->error('密码错误！【今日密码已被锁定，请24小时后再试或者请联系管理员】','','','301');
							exit;
						}elseif($this->loginType=="dialog"){
							$result  =  array();
							$result['status']  =  0;
							$result['statusCode']  =  0;
							$result['message']  =  "密码错误！【今日密码已被锁定，请24小时后再试或者请联系管理员】";
							echo json_encode($result);
							exit;
						}else{
							$this->assign("info","密码错误！【今日密码已被锁定，请24小时后再试或者请联系管理员】");
                            $this->display("login");exit;
						}
					}else{
						return array('status'=>0,'msg'=>"error:101密码错误！");
					}
				} else {
					if($this->loginType!='helperLogin'){
						if($this->loginType=='checkLogin'){
							$this->assign("jumpUrl",__APP__.'/Public/login/');
							$this->error('密码错误！【今日再输错'.$login_errorsum_count.'次，密码将被锁定】','','','301');
							exit;
						}elseif($this->loginType=="dialog"){
							$result  =  array();
							$result['status']  =  0;
							$result['statusCode']  =  0;
							$result['message']  =  "密码错误！【今日再输错".$login_errorsum_count."次，密码将被锁定】";
							echo json_encode($result);
							exit;
						}else{
							$this->assign("info","密码错误！【今日再输错".$login_errorsum_count."次，密码将被锁定】");
                            $this->display("login");exit;
						}
					}else{
						return array('status'=>0,'msg'=>"error:102密码错误！");
						exit;
					}
				}
			} else {
				if($this->loginType!='helperLogin'){					
					if($this->loginType=='checkLogin'){
					$this->assign("jumpUrl",__APP__.'/Public/login/');
					$this->error("密码输入错误！",'','','301');
					exit;
					}elseif($this->loginType=="dialog"){
							$result  =  array();
							$result['status']  =  0;
							$result['statusCode']  =  0;
							$result['message']  =  "密码输入错误！";
							echo json_encode($result);
							exit;
					}else{
                        $this->assign("info","密码输入错误！");
						$this->display("login");exit;
					}
				}else{
					return array('status'=>0,'msg'=>"error:103密码错误！");
					exit;
				}
			}
		}
	}
	/**
	 * @Title: getAuthInfo
	 * @Description: todo(在signin方法中调用，此方法作用为，获取当前用的部门，部门及子部门。存入session中)
	 * @param array 当前登录用户数据 $authInfo
	 * @author liminggang
	 * @date 2014-8-28 下午6:50:41
	 * @throws
	 */	
	private function getAuthInfo(){		
		//生成认证条件
		$map =   array();
		$map["status"]	= 1;
		//$map['loginnumstatus']	= 0;  //如果一个账号登陆失败N次，将锁定此账号  loginnumstatus则为1
		$where = array();
		$where['account']	= $_POST['account'];
		$where['zhname']  = $_POST['account'];
		$where['_logic'] = 'or';
		$map['_complex'] = $where;
		$where = array();
		$authInfo = RBAC::authenticate($map);
		if($_POST['account']!='admin'){
			$UserDeptDutymap=array();
			$UserDeptDutymap['userid']=$authInfo['id'];//当前用户id
			$UserDeptDutymap['typeid']=1;//获取主岗信息
			$UserDeptDutymap['status']=1;//获取状态为1
			//$UserDeptDutymap['companyid']=$_POST['companyid'];//选择登陆公司
			$companyid = RBAC::authenticate($UserDeptDutymap,'UserDeptDuty');
			// 			if(!$companyid){
			// 				$this->assign("jumpUrl",__URL__.'/login/');
			// 				$this->error('您还没有该公司权限,请重新选择公司登陆！');
			// 			}
            //获取公司
			$authInfo['companyid']=$companyid['companyid'];
			//获取部门
			$authInfo['dept_id']=$companyid['deptid'];
			//获取岗位
			$authInfo['sysworktype']=$companyid['worktype'];
			//获取职级
			$authInfo['sysdutyid']=$companyid['dutyid'];
		}	
		return 	$authInfo;
	}
	/**
	 * @Title: setSession 
	 * @Description: todo(在signin方法中调用，此方法作用为，获取当前用的部门，部门及子部门。存入session中) 
	 * @param array 当前登录用户数据 $authInfo  
	 * @author liminggang 
	 * @date 2014-8-28 下午6:50:41 
	 * @throws
	 */
	private function setSession($authInfo){
		$model=D("role_user");
		$aryrole=$model->where("user_id='".$authInfo['id']."'")->getField("role_id",true);
		//统一设置SESSION值
		$_SESSION[C('USER_AUTH_KEY')]	= $authInfo['id'];
		$_SESSION['user_usertype']	= $authInfo['usertype'];  //1、后台用户，2专家，3金融联络站
		$_SESSION['companyid']	= $authInfo['companyid'];//公司信息
		$_SESSION['email']     = $authInfo['email'];
		$_SESSION['user_employeid']	=$authInfo['employeid'];//用户绑定员工ID
		$_SESSION['user_dep_id']	=$authInfo['dept_id'];//员工部门
		$_SESSION['user_duty_id']	=$authInfo['sysdutyid'];//员工职级
		$_SESSION['user_shangji_id']=$authInfo['shangji'];//用户上级
		$_SESSION['user_anquandengji_id']=$authInfo['anquandengji'];//用户安全等级
		$_SESSION['user_job_id']=$authInfo['sysworktype'];//用户岗位
		$_SESSION['user_txsj']=$authInfo['txsj'];//调休时间
// 		$_SESSION['user_dep_all_child']=array_unique($alluserdept);
// 		$_SESSION['user_dep_all_self']=array_unique($user_dep_all_self); 
		$_SESSION['role']	=implode(",", $aryrole);
		$_SESSION['loginUserName']	=$authInfo['name'];
		$userDao = M("user");
//		$adminArr = $userDao->where("status = 1")->getField("id,account");
		$adminArr=array("admin");
		if(in_array($authInfo['account'],$adminArr)) {
			$_SESSION[C('ADMIN_AUTH_KEY')]	=true;
		}
	}
	
	/**
	 * @Title: setUserOnline
	 * @Description: todo(设置用户在线信息)
	 * @param 登录用户数据 $authInfo
	 * @param 登录用户数据 $type  1 insert, 2 update, 3 delete
	 * @author liminggang
	 * @date 2014-8-28 下午6:52:20
	 * @throws
	 */
	private function setUserOnline($authInfo,$type){
		if($authInfo['id']){
			$online_model = M("user_online");
            switch($type){
            	case "insert":
            		if(C('SAME_TIME_LOADING')){
            			//这里不判断是否重复登录
            			$map=array();
            			$map['userid']=$authInfo['id'];
            			//$map['session_id']=session_id();
            			$result=$online_model->where($map)->count('userid');
            			//如果不存在记录，则插入
            			if($result==='0'){
            				//插入user_online表
            				$onlinedata=array();
            				$onlinedata["modify_time"]=time();
            				$onlinedata["session_id"]= session_id();
            				$onlinedata["userid"]=$authInfo['id'];
            				$onlinedata["nexttime"]=0;
            				$online_model->add($onlinedata);
            			}else{
            				//更新user_online表
							$onlinedata=array();
							$onlinedata["modify_time"]=time();
							$onlinedata["session_id"]= session_id();
							$onlinedata["nexttime"]=0;
							$online_model->where("userid=".$authInfo['id'])->save($onlinedata);	
            			}            			
            		}else{
            			//这里判断是否重复登录
            			$map=array();
            			$map['userid']=$authInfo['id'];
            			//$map['session_id']=session_id();
            			$result=$online_model->where($map)->count('userid');           						
            			//如果不存在记录，则插入	
	            		if($result==='0'){
							//插入user_online表
							$onlinedata=array();
							$onlinedata["modify_time"]=time();
							$onlinedata["session_id"]= session_id();
							$onlinedata["userid"]=$authInfo['id'];
							$onlinedata["nexttime"]=0;
							$online_model->add($onlinedata);
	            		}else{
	            			session_unset();
	            			$this->assign("jumpUrl",__APP__.'/Public/login/');
							//$this->assign("jumpUrl",__URL__.'/login/');
							$this->error('该帐号不能同时操作！');
	            		}
            	    }
				break;
				case "update":
					//更新user_online表
					$onlinedata=array();
					$onlinedata["modify_time"]=time();
					$onlinedata["session_id"]= session_id();
					$online_model->where("userid=".$authInfo['id'])->save($onlinedata);					
				break;
            	case "delete":
            		//先删除过去记录，保持记录唯一
            		$online_model->where("userid=".$_SESSION[C('USER_AUTH_KEY')])->delete();            		
            	break;
            }
		}
	}
	/**
	 * @Title: setUserInfoCookie 
	 * @Description: todo(在signin方法中调用,像Cookie中存入用户密码及登录时间) 
	 * @param 登录用户数据 $authInfo  
	 * @author liminggang 
	 * @date 2014-8-28 下午6:52:20 
	 * @throws
	 */
	private function setUserInfoCookie($authInfo){
		$cook_userinfo['pwd'] =$authInfo['password'];
		$cook_userinfo['user']=$authInfo['account'];
		$cook_userinfo['logintime'] =$authInfo['last_login_time'];
		$cook_expire = C('COOKIE_EXPIRE');
		if($_POST["remember"]){
			Cookie::set("remember",1,365*24*86400);
			$cook_expire = 365*24*86400;
		}
		Cookie::set("userinfo",$cook_userinfo,C('COOKIE_EXPIRE'));
	}
	/**
	 * @Title: setBBSCookie 
	 * @Description: todo(在signin方法中调用,设置BBS的Cookie) 
	 * @param unknown_type $authInfo  
	 * @author liminggang 
	 * @date 2014-8-28 下午6:53:36 
	 * @throws
	 */
	private function setBBSCookie($authInfo){
		import('@.ORG.UcenterBbs', '', $ext='.php');
		$objUcenterBbs = new UcenterBbs();
		$bbsuserinfo = $objUcenterBbs->login($authInfo['account'],$authInfo['id'],$authInfo['password']);
		if(!$bbsuserinfo){
			$bbsuserinfo = $objUcenterBbs->register($authInfo['account'],$authInfo['id'],5,$authInfo['password'],$authInfo['email'], $timestamp = 0);
		}
		Cookie::set("bbsuserinfo",$bbsuserinfo,C('COOKIE_EXPIRE'));
	}
	
	/**
	 * @Title: signin 
	 * @Description: todo(浏览器版登陆方法)   
	 * @author liminggang 
	 * @date 2014-8-28 下午4:34:10 
	 * @throws
	 */
	public function signin() {
		//获取产品模块授权培
		$model=D('SerialNumber');
		$system=array();
		$system=$model->CheckFile();
		if(count($system)>0){//验证序列号文件是否存在，基本校验。
			redirect(U("Public/serialnumber?register=".$_POST['account']));
		}				
		//读取名人录数据
		//$this->getsolidotinfor();
		if($_POST["login_type"]=="dialog"){
			$this->loginType = "dialog";
		}
		if(empty($_POST['account'])) {
			$this->assign("jumpUrl",__URL__.'/login/');
		}elseif (empty($_POST['password'])){
			$this->assign("jumpUrl",__URL__.'/login/');
		}elseif ( C("VERIFICATION_CODE") && empty($_POST['verify'])){
			if($this->loginType!='helperLogin'){
				$this->assign("jumpUrl",__URL__.'/login/');
				$this->error('验证码必须！');
			}
		}elseif( C("VERIFICATION_CODE") && $_SESSION['verify'] != md5($_POST['verify'])) {
			if($this->loginType!='helperLogin'){
				$this->assign("jumpUrl",__URL__.'/login/');
				$this->error('验证码错误！');
			}
		}

		//获取authinfo信息
		$authInfo=$this->getAuthInfo();
		//使用用户名、密码和状态的方式进行认证
		if(false == $authInfo) {
			$this->assign("jumpUrl",__URL__.'/login/');
			if($this->loginType!='helperLogin'){
					$this->assign("info","帐号不存在或已禁用！");
					$this->display('login');
			}else{
				return array('status'=>0,'msg'=>"error:帐号不存在或已禁用！");
				exit;
			}
		}else {
			//--------------此部分为验证密码-------------------//
			$checkPwd=$this->checkPwd($authInfo);
			if($checkPwd){return $checkPwd;}
			//-----------以下部分已被分成方法-------------//
			$this->setSession($authInfo);//设置session
			$this->setUserInfoCookie($authInfo);//设置UserInfo的cookie
			//$this->setBBSCookie($authInfo);//设置BBS的cookie
			$time=time();
			//保存登录信息
			$ip		=get_client_ip();
			$data = array();
			$data['last_login_time']=$time;
			$data['logintime']=$time;
			$data['isonline']=1;
			$data['sessionid'] = session_id();
			$data['id']	= $authInfo['id'];
			$data['login_count']	=array('exp','login_count+1');
			$data['login_error_count'] = 0;
			$data['newmsg'] = 1;
			$data['newmsgtype'] = 1;
			$data['last_login_ip']	=$ip;
			if(!$bindacount){
				$User = M('User');
				$User->save($data);
				$User->commit();
			}

			//新增online信息,类型为新增
			$this->setUserOnline($authInfo,$type="insert");
			// 缓存访问权限
			 RBAC::saveAccessList();
			 //写入浏览及权限
			Browse::saveBrowseList();
			if(isset($_SESSION[C('ADMIN_AUTH_KEY')])){
				$re = $this->check_upgrade(false);
			}

			//如果是从OA客户端登录的，返回真
			if($this->loginType=='helperLogin'){
				if($_REQUEST['fromOA']==2){
					//$this->success('登录成功！');
					redirect(U('Index/index'),0.01,'页面跳转中。。。');
				}else{
					return array('status'=>1,'msg'=>"success:登录成功！");
					exit;
				}
			}else if($this->loginType!='checkLogin'){
					redirect(U('Index/index'));
			}else{
                   $this->success('登录成功！');
			}
		}
	}
	// 登录检查
	public function checkLogin() {
		//Cookie::delete("userinfo");
//  		session_unset();
// 		session_destroy();
// 		exit;
		$userinfo = Cookie::get("userinfo");
		$userinfo["user"]?$_POST["account"]=$userinfo["user"]:$_POST["account"]=$_REQUEST["account"];
		$userinfo['pwd']?$_POST["password"]=$userinfo['pwd']:$_POST["password"]=$_REQUEST["password"];	
		//$_POST["password"]="567";
		if($_POST["account"] && $_POST["password"]){
			//先判读cookie里存的userinfo,再判断通过REQUEST传递过来的数据
			if($_SESSION[C('USER_AUTH_KEY')]){
				//不管安全问题只管性能，如果存在，直接判断有就绕开判断，以后可以用握手协议来记录安全
// 				$map=array();
// 				$map['account']	= $userinfo['user'];
// 				$map['password']= $userinfo['pwd'];
// 				$map["status"]	=array('gt',0);
// 				$authInfo =RBAC::authenticate($map);
// 				if($authInfo){
// 					return true;
// 				}
                //延长cookie超时时间
				Cookie::set("userinfo",Cookie::get("userinfo"),C('COOKIE_EXPIRE'));
				//验证完成后清理
				unset($_POST["account"]);
				unset($_POST["password"]);
				return true;
			}else{
				$authInfo=$this->getAuthInfo();
				
				if($authInfo){
					//--------------此部分为验证密码-------------------//
					$this->loginType="checkLogin";//核验类型为：检验登录
					$this->checkPwd($authInfo);
					$this->setSession($authInfo);//重新设置session
					$this->setUserInfoCookie($authInfo);//设置UserInfo的cookie
					//验证完成后清理
					unset($_POST["account"]);
					unset($_POST["password"]);
					return true;
				}else{
					//更新online信息,类型为删除
					$this->setUserOnline($authInfo="logout",$type="delete");
					// 		session_unset();
					// 		session_destroy();
					//销毁cookie
					Cookie::delete("userinfo");
					Cookie::clear();
					return false;
				}
			}			
		}else{
				//更新online信息,类型为删除
				$this->setUserOnline($authInfo="logout",$type="delete");
		// 		session_unset();
		// 		session_destroy();
				//销毁cookie
				Cookie::delete("userinfo");
				Cookie::clear();
				return false;
		}
	}
 
	/**
	 * @Title: verify 
	 * @Description: todo(获取登录验证码)   
	 * @author liminggang 
	 * @date 2014-8-28 下午6:37:18 
	 * @throws
	 */
	public function verify(){
		$type	 =	 isset($_GET['type'])?$_GET['type']:'gif';
		import("@.ORG.Image");
		ob_clean();
		Image::buildImageVerify(4,1,$type,$width=45);
	}
 
	/**
	 * @Title: serialnumber 
	 * @Description: todo(授权码方法，在CommonAction和SerialNumberAction中有用到)   
	 * @author liminggang 
	 * @date 2014-8-28 下午6:39:23 
	 * @throws
	 */
	public function serialnumber(){
		$model=D("SerialNumber");
		$system=array();
		$system=$model->CheckFile();
		$this->assign("crypt", $system);
		if( $_REQUEST['register']=='register'){
			$this->display();
		}else{
			echo "未授权，您禁止访问此页面,请用注册账号重新授权，谢谢！";
			exit;
		}
	}
	/**
	 * @Title: clear_cache 
	 * @Description: todo(清空缓存)   
	 * @author liminggang 
	 * @date 2014-8-28 下午6:40:24 
	 * @throws
	 */
	function clear_cache(){
		$obj_dir = new Dir;
		if($_REQUEST['runtime'] == 1){
			$directory =  DConfig_PATH."/AccessList";
			$obj_dir->del($directory);
		}
		if($_REQUEST['runtime'] == 3){
			$directory = RUNTIME_PATH."/Data/_fields";
			$obj_dir->del($directory);
		}
		//清除整个目录，必须放在最下面
		if($_REQUEST['runtime'] == 2){
			$obj_dir->delDir(RUNTIME_PATH);
		}
		$this->success('删除缓存成功！');
	}
	/**
	 * @Title: check_upgrade 
	 * @Description: todo(检查是否可以升级) 
	 * @param unknown_type $bytpl
	 * @return number  
	 * @author liminggang 
	 * @date 2014-8-28 下午6:42:44 
	 * @throws
	 */
	public function check_upgrade( $bytpl=true ){
		import('@.ORG.class_xml', '', $ext='.php');
		import('@.ORG.function_filesock', '', $ext='.php');
		$commonmodel = M("CommonSetting");
		$version =$commonmodel->where("skey='version'")->getField("svalue");
		$release =$commonmodel->where("skey='release'")->getField("svalue");
			
		$upgradefile = C("ONLINE_UPGRADE").$version."/".$release."/upgrade.xml";

		$response = xml2array( dfsockopen($upgradefile) );
		if(isset($response['cross']) || isset($response['patch'])) {
			Cookie::set("needupgrade",1,86400);
			$commonmodel->where("skey='upgrade'")->setField("svalue",serialize($response));
			$commonmodel->commit();
			$re=1;
		} else {
			$commonmodel->where("skey='upgrade'")->setField("svalue","");
			$commonmodel->commit();
			$re=0;
		}
		if( $bytpl ){
			echo $re;
			exit;
		}else{
			return $re;
		}
	}

	/**
	 * @Title: upgrade 
	 * @Description: todo(检查可以升级的版本)   
	 * @author liminggang 
	 * @date 2014-8-28 下午6:43:06 
	 * @throws
	 */
	public function upgrade(){
		if(!$_GET['checking']){
			$this->assign("msg","正在检查新的版本");
			$this->assign("linkurl",__APP__.'/Public/upgrade/checking/1');
			$this->assign("check",1);
			$this->display("cross_step1");
			exit;
		}
			
		$commonsettingmodel = M("CommonSetting");
		$respon =$commonsettingmodel->where("skey='upgrade'")->getField("svalue");
		if( $respon=="" ){
			$this->assign("msg","暂无更新");
			$this->assign("check",0);
			$this->display("cross_step1");
			exit;
		}
			
		$version =$commonsettingmodel->where("skey='version'")->getField("svalue");
		$release =$commonsettingmodel->where("skey='release'")->getField("svalue");
			
			
		$relist = unserialize($respon);
		$this->assign("upgrade",$relist);
		$relist['cross']['now_version']=$version;
		$relist['cross']['now_release']=$release;
		$_SESSION["upgrade_cross"]=$relist['cross'];
		$re = $commonsettingmodel->query("SELECT VERSION() AS version");
		$dbversion = $re[0]['version'];
			
		//判断是否升级mysql 或者php
		$locale='SC';
		$charset='UTF8';
		$i=0;
		foreach($relist as $type => $upgrade) {
			$unupgrade = 0;
			if(version_compare($upgrade['phpversion'], PHP_VERSION) > 0 || version_compare($upgrade['mysqlversion'], $dbversion) > 0) {
				$unupgrade = 1;
			}

			$linkurl = __APP__.'/Public/'.$type.'/version/'.$upgrade['latestversion'].'/locale/'.$locale.'/charset/'.$charset.'/release/'.$upgrade['latestrelease'];

			if($unupgrade) {
				$upgraderow[$i][0] ='TML'.$upgrade['latestversion'].'_'.$locale.'_'.$charset.$lang['version'].' [Release '.$upgrade['latestrelease'].']';
				$upgraderow[$i][1] ='要求配置达到 php v'.PHP_VERSION.'MYSQL v'.$dbversion;
			} else {
					
				$upgraderow[$i][0] = 'TML'.$upgrade['latestversion'].'_'.$locale.'_'.$charset.'版本 [Release '.$upgrade['latestrelease'].']';
				$upgraderow[$i][1] = '<a href="'.$linkurl.'"  onclick="upgradecross(this);return false;" title="系统升级" class="upgradebtn" >自动升级</a> | <a href="'.$upgrade['official'].'" target="_blank">手动下载</a>';
			}
			$i++;
		}
		$this->assign("upgraderow",$upgraderow);
		$this->display();
	}
	

	public function redpacksend(){
		$this->dispaly();
		
	}

	public function redpacksave(){
		$this->dispaly();
	
	}
	/**
	 * @Title: cross 
	 * @Description: todo(升级处理方法)   
	 * @author liminggang 
	 * @date 2014-8-28 下午6:43:23 
	 * @throws
	 */
	public function cross(){
		$step = intval($_REQUEST['step']);
		$step = $step ? $step : 1;
			
		$version = trim($_GET['version']);
		$release = trim($_GET['release']);
		$locale = trim($_GET['locale']);
		$charset = trim($_GET['charset']);
		$upgradeinfo = $upgrade_step = array();
		$upgradeinfo = $_SESSION["upgrade_cross"];
			
		import('@.ORG.function_filesock', '', $ext='.php');
		import('@.ORG.tml_upgrade', '', $ext='.php');
		$tml_upgrade = new tml_upgrade();
			
		if($step != 5) {
			$updatefilelist = $tml_upgrade->fetch_updatefile_list($upgradeinfo);
			$updatemd5filelist = $updatefilelist['md5'];
			$updatefilelist = $updatefilelist['file'];
			//$theurl = 'upgrade&operation='.$operation.'&version='.$version.'&locale='.$locale.'&charset='.$charset.'&release='.$release;
			$theurl = __APP__.'/Public/cross/version/'.$version.'/locale/'.$locale.'/charset/'.$charset.'/release/'.$release;
			if(empty($updatefilelist)) {
				$this->error("暂时没有更新程序");
			}
		}
			
		if($step == 1) {
			$this->assign("url",__URL__."/cross/version/".$upgradeinfo['latestversion']."/locale/SC/charset/UTF8/release/".$upgradeinfo['latestrelease']."/step/2");
			$this->assign("uploadfolder",'./upgrade/TML'.$upgradeinfo['latestversion'].' Release['.$upgradeinfo['latestrelease'].']');
			$this->assign("updatefilelist",$updatefilelist);
			$this->display();
		}
		elseif($step == 2) {
			$fileseq = intval($_GET['fileseq']);
			$fileseq = $fileseq ? $fileseq : 1;
			$position = intval($_GET['position']);
			$position = $position ? $position : 0;
			$offset = 100 * 1024;
			if($fileseq > count($updatefilelist)) {
				if($upgradeinfo['isupdatedb']) {
					$tml_upgrade->download_file($upgradeinfo, 'install/data/install.sql');
					$tml_upgrade->download_file($upgradeinfo, 'install/data/install_data.sql');
					//$tml_upgrade->download_file($upgradeinfo, 'update.php', 'script');
				}
				$linkurl = $theurl.'/step/3';
				$percent = "200%";
				$file =  "已下载所有更新文件";
			} else {
				$downloadstatus = $tml_upgrade->download_file($upgradeinfo, $updatefilelist[$fileseq-1], 'upload', $updatemd5filelist[$fileseq-1], $position, $offset);
				if($downloadstatus == 1) {
					$linkurl = $theurl.'/step/2/fileseq/'.$fileseq.'/position/'.($position+$offset);
					$percent = sprintf("%2d", 100 * $fileseq/count($updatefilelist)).'%';
					$file =  $updatefilelist[$fileseq-1];
				} elseif($downloadstatus == 2) {
					$linkurl = $theurl.'/step/2/fileseq/'.($fileseq+1);
					$percent = sprintf("%2d", 100 * $fileseq/count($updatefilelist)).'%';
					$file =  $updatefilelist[$fileseq-1];
				} else {

				}
			}
			$this->assign("file", $file);
			$this->assign("percent", $percent);
			$this->assign("stepover", 1);
			$this->assign("linkurl", $linkurl);
			$this->display("cross_step2");
		}
		elseif($step == 3) {

			list($modifylist, $showlist, $ignorelist) = $tml_upgrade->compare_basefile($upgradeinfo, $updatefilelist);
			$data=array();
			$i=0;

			foreach($updatefilelist as $v) {
				if(isset($ignorelist[$v])) {
					continue;
				}elseif(isset($modifylist[$v])) {
					$data[$i]['file']=$v;
					$data[$i]['local_file_status']="差异 <em class=\"unknown\">&nbsp;</em>";

				} elseif(isset($showlist[$v])) {
					$data[$i]['file']=$v;
					$data[$i]['local_file_status']="正常 <em class=\"fixed\">&nbsp;</em>";

				} else {
					$data[$i]['file']=$v;
					$data[$i]['local_file_status']="新增 <em class=\"unknown\">&nbsp;</em>";
				}
				$i++;
			}

			if(empty($modifylist) && empty($showlist) && empty($ignorelist) && empty($data)) {
				$this->error("不存在校验文件或无更新文件，无需升级");
			}

			$theurl = __APP__.'/Public/cross/version/'.$version.'/locale/'.$locale.'/charset/'.$charset.'/release/'.$release;
			$linkurl = $theurl.'/step/4';
			$this->assign("linkurl", $linkurl);
			$this->assign("uploadfile_folder",'./upgrade/TML'.$upgradeinfo['latestversion'].' Release['.$upgradeinfo['latestrelease'].']');
			$this->assign("uploadfile_back_folder",'./upgrade/TML'.$upgradeinfo['latestversion'].' Release['.$upgradeinfo['latestrelease'].']/back');
			$this->assign("volist", $data);
			$this->display("cross_step3");
		}
		else if($step==4){
			$confirm = $_GET['confirm'];
			$this->assign("theurl",$theurl);
			if(!$confirm) {
				if($upgradeinfo['isupdatedb']) {
					$checkupdatefilelist = array( 'install/data/install.sql','install/data/install_data.sql');
					$checkupdatefilelist = array_merge($checkupdatefilelist, $updatefilelist);
				} else {
					$checkupdatefilelist = $updatefilelist;
				}
					
				if($tml_upgrade->check_folder_perm($checkupdatefilelist)) {
					$confirm = 'file';
				} else {
					$linkurl = $theurl.'/step/4';
					$msg="发现您的目录及文件无修改权限，修改文件权限为可读可写后重试";
					$this->assign("linkurl",$linkurl);
					$this->assign("msg",$msg);
					$this->assign("refresh",0);
					$this->display("cross_step4");
					exit;
				}
			}

			if(!$_GET['startupgrade']) {
				if(!$_GET['backfile']) {
					$linkurl = $theurl.'/step/4/confirm/file/backfile/1';
					$this->assign("linkurl",$linkurl);
					$msg="正在备份原始文件...";
					$this->assign("msg",$msg);
					$this->assign("refresh",1);
					$this->display("cross_step4");
					exit;
				}
				foreach($updatefilelist as $updatefile) {
					$destfile = APP_PATH.$updatefile;
					$backfile = APP_PATH.'./upgrade/TML'.$upgradeinfo['now_version'].' Release['.$upgradeinfo['now_release'].']/back/'.$updatefile;
					if(is_file($destfile)) {
						if(!$tml_upgrade->copy_file($destfile, $backfile, 'file')) {
							$linkurl = $theurl.'/step/4/confirm/file/backfile/1';
							$this->assign("linkurl",$linkurl);
							$msg="备份原始文件出错...";
							$this->assign("msg",$msg);
							$this->assign("refresh",0);
							$this->display("cross_step4");
							exit;
						}
					}
				}
					
				$linkurl = $theurl.'/step/4/confirm/file/backfile/1/startupgrade/1';
				$this->assign("linkurl",$linkurl);
				$msg="备份完成，正在进行升级...";
				$this->assign("msg",$msg);
				$this->assign("refresh",1);
				$this->display("cross_step4");
				exit;
			}

			if(!$_GET['fileupgrade']) {
				foreach($updatefilelist as $updatefile) {
					$srcfile = APP_PATH.'./upgrade/TML'.$version.' Release['.$release.']/'.$updatefile;
					$destfile = APP_PATH.$updatefile;

					if(!$tml_upgrade->copy_file($srcfile, $destfile, $confirm)) {
						$linkurl = $theurl.'/step/4/confirm/file/backfile/1/startupgrade/1';
						$this->assign("linkurl",$linkurl);
						$msg="复制文件 ".$updatefile." 出错，请检测原始文件[".$srcfile."]是否存在，重新复制 ";
						$this->assign("msg",$msg);
						$this->assign("refresh",0);
						$this->display("cross_step4");
						exit;
					}
				}
				if($upgradeinfo['isupdatedb']) {
					$dbupdatefilearr = array( 'install/data/install.sql','install/data/install_data.sql');
					foreach($dbupdatefilearr as $dbupdatefile) {
						//$srcfile = DISCUZ_ROOT.'./data/update/Discuz! X'.$version.' Release['.$release.']/'.$dbupdatefile;
						$srcfile = APP_PATH.'./upgrade/TML'.$version.' Release['.$release.']/'.$dbupdatefile;
						$destfile = APP_PATH.$dbupdatefile;
						if(!$tml_upgrade->copy_file($srcfile, $destfile, $confirm)) {
							$linkurl = $theurl.'/step/4/confirm/file/backfile/1/startupgrade/1';
							$this->assign("linkurl",$linkurl);
							$msg="复制文件 ".$dbupdatefile." 出错，请检测原始文件是否存在，重新复制 ";
							$this->assign("msg",$msg);
							$this->assign("refresh",0);
							$this->display("cross_step4");
							exit;
						}
					}
				}
				if( $upgradeinfo['isupdatedb'] ){
					$linkurl = $theurl.'/step/4/confirm/file/backfile/1/startupgrade/1/fileupgrade/1/dodabase/1';
					$this->assign("linkurl",$linkurl);
					$msg="文件升级成功，即将进入更新数据库";
					$this->assign("msg",$msg);
					$this->assign("refresh",1);
					$this->display("cross_step4");
					exit;
				}
			}
			if($_GET['dodabase']){
				$m = A("UpgradeService");
				$respon = $m->doUpgrade();
				if( !$respon["status"] ){
					$linkurl = $theurl.'/step/4/confirm/file/backfile/1/startupgrade/1/fileupgrade/1/dodabase/1';
					$this->assign("linkurl",$linkurl);
					$msg="数据库更新失败";
					$this->assign("msg",$respon["msg"]);
					$this->assign("refresh",0);
					$this->display("cross_step4");
					exit;
				}
			}
			$linkurl = $theurl.'/step/5';
			$this->assign("linkurl",$linkurl);
			$msg="系统升级成功";
			$this->assign("msg",$msg);
			$this->assign("refresh",1);
			$this->assign("step",5);
			$this->display("cross_step4");
		}
		elseif($step==5){
			//$file = __APP__.'./upgrade/TML'.$upgradeinfo['latestversion'].' Release['.$upgradeinfo['latestrelease'].']/updatelist.tmp';
			// @unlink($file);
			@unlink(APP_PATH.'./Lib/Action/UpgradeServiceAction.class');
			$commonmodel = M("CommonSetting");

			$commonmodel->where("skey='upgrade'")->setField("svalue","");
			$commonmodel->where("skey='release'")->setField("svalue",$upgradeinfo['latestrelease']);
			$commonmodel->where("skey='version'")->setField("svalue",$upgradeinfo['latestversion']);
			$commonmodel->commit();
			$old_update_dir = './upgrade/TML'.$upgradeinfo['latestversion'].' Release['.$upgradeinfo['latestrelease'].']/';
			$new_update_dir = './Backup/updated_TML'.$upgradeinfo['latestversion'].'_Release['.$upgradeinfo['latestrelease'].'/';
			$old_back_dir = './upgrade/TML'.$upgradeinfo['now_version'].' Release['.$upgradeinfo['now_release'].']/';
			$new_back_dir = './upgrade/backed_TML'.$upgradeinfo['now_version'].' Release['.$upgradeinfo['now_release'].']/';
			$tml_upgrade->copy_dir(APP_PATH.$old_update_dir, APP_PATH.$new_update_dir);
			$tml_upgrade->copy_dir(APP_PATH.$old_back_dir, APP_PATH.$new_back_dir);
			$tml_upgrade->rmdirs(APP_PATH.$old_update_dir);
			$tml_upgrade->rmdirs(APP_PATH.$old_back_dir);
			Cookie::delete("needupgrade");
			$this->display("cross_step5");
		}
	}
 	
	/**
	 * @Title: getsolidotinfor
	 * @Description: todo(首页名人名言数据)
	 * @author yuansl
	 * @date 2014-6-6 下午3:51:02
	 * @throws
	 */
	private function getsolidotinfor(){
		$SolidotList_config = require DConfig_PATH."/System/solidotListConfig.inc.php";
		$SolidotList_config_count = count($SolidotList_config);
		$SolidotList_config_position = mt_rand(0,$SolidotList_config_count-1);
		$SolidotList_config_data = $SolidotList_config[$SolidotList_config_position];
		//获取配置图片 如果有
		$userModel = D("User");
		$userinfo = $userModel->where("id = ".$_SESSION [C ( 'USER_AUTH_KEY' )])->field("solidpath")->find();
		if($userinfo['solidpath']){
			$SolidotList_config_data['solidpath'] = $userinfo['solidpath'];
		}
		$this->assign('Solidot',$SolidotList_config_data);
	}
	
	
}
?>