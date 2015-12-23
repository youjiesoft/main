<?php
//Version 1.0
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
class PublicAction extends Action {
	//登录模式
	private $loginType='standardLogin';
	//事务处理
	public  $transaction_model='';
	var $locale = 'SC';
	var $charset = 'UTF8';

	//单例模式入口
	function _initialize() {
		$this->transaction_model=M();
		$this->transaction_model->startTrans();
	}
	// 检查用户是否登录
	protected function checkUser() {
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
			$this->assign("jumpUrl",__APP__.'Public/login/');
			$this->error('用户没有登录或已超时','','','301');
		}
	}


	// 用户登录页面
	public function login() {
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
			//查看公司登陆LOGO
			$CommonSettingModel=M('common_setting'); //配置表
			$picList=$CommonSettingModel->where(" skey='companyico'")->find(); //图片
			$nameList=$CommonSettingModel->where(" skey='companyname'")->find();//名称
			$enameList=$CommonSettingModel->where(" skey='companyename'")->find();//英文名称
			$lastList=$CommonSettingModel->where(" skey='companylast'")->find();//@tml.com
			$this->assign("picList",$picList);
			$this->assign("nameList",$nameList);
			$this->assign("enameList",$enameList);
			$this->assign("lastList",$lastList);
			$this->assign('verificationcode',C("VERIFICATION_CODE"));
			$this->display("Public:login");
		}else{
			$this->assign("isLogin",1);
			$this->redirect('Index/index');
		}
	}
	public function login_dialog() {
		$this->assign('verificationcode',C("VERIFICATION_CODE"));
		$this->display();
	}
	
	

	public function index() {
		//查询当前登录用户公司ID
		$userModel=D('User');
		$companyId=$userModel->where(" status=1 and id=".$_SESSION[C('USER_AUTH_KEY')])->find();
		//获取顶部LOGO 图片
		$MisSystemCompanyModel = M("MisSystemCompany");
		$MisSystemCompanyPicture=$MisSystemCompanyModel->where(" status=1 and id=".$companyId['companyid'])->field('id,picture')->find();
		$this->assign("logopicture",$MisSystemCompanyPicture['logopicture']);
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
			//查看公司登陆LOGO
			$CommonSettingModel=M('common_setting'); //配置表
			$picList=$CommonSettingModel->where(" skey='companyico'")->find(); //图片
			$nameList=$CommonSettingModel->where(" skey='companyname'")->find();//名称
			$enameList=$CommonSettingModel->where(" skey='companyename'")->find();//英文名称
			$this->assign("picList",$picList);
			$this->assign("nameList",$nameList);
			$this->assign("enameList",$enameList);
			$this->assign('verificationcode',C("VERIFICATION_CODE"));
			$this->display('login');
		}else{
			$this->assign("isLogin",1);
			$this->redirect('Index/index');
		}
	}
	// 用户登出
	public function logout()
	{
		//dirct yuansl 2014 06 05
		//读取名人录数据
		//此方法必须放在销毁session之前
		$this->getsolidotinfor();
		$User = M('User');
		$data = array();
		//$data['last_login_time'] ="";
		$data['isonline'] = 0;
		$data['sessionid'] = "";
		$data['leavetime'] = time();
		$data['id']	= $_SESSION[C('USER_AUTH_KEY')];
		$User->save($data);
		$User->commit();
		unset($_SESSION);
		Cookie::delete("userinfo");
		Cookie::clear();
		session_destroy();
		$this->assign("jumpUrl",__URL__.'/login/');
		$this->success('注销成功！');
	}
	//oa助手登录配制
	public  function  helperSignin(){
		$account = $_REQUEST['account'];
		$password = $_REQUEST['password'];
		//你要获取account跟password的code，并做好数据处理
		$_POST['account']=$account;
		$_POST['password']=$password;
		if(!empty($_REQUEST['fromOA'])){
			$this->loginType='helperLogin';
		}
		//echo '{"success":"1","u":"'.$account.'","p":"'.$password.'","aa":"12312"}';
		//dump($_REQUEST);
		//die;
		$this->signin();
	}

	// 登录检测
	public function signin() {
		//查找名人名言
		$this->getsolidotinfor();
		//echo $this->loginType;
		//die;
		if(empty($_POST['account'])) {
			$this->assign("jumpUrl",__URL__.'/login/');
			$this->error('帐号错误！');
		}elseif (empty($_POST['password'])){
			$this->assign("jumpUrl",__URL__.'/login/');
			$this->error('密码必须！');
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
		//生成认证条件
		$map =   array();
		$map["status"]	= 1;
		$map['account']	= $_POST['account'];
		$map['loginnumstatus']	= 0;

		import ( '@.ORG.RBAC' );
		// 认证方法,获取当前用户的信息
		$authInfo = RBAC::authenticate($map);
		if(false == $authInfo) {
			unset($map['account']);
			$map['zhname']	= $_POST['account'];
			$authInfo = RBAC::authenticate($map);
		}
		//dirct yuansl 2014 06 05
		//读取名人录数据
		$this->getsolidotinfor();
		//使用用户名、密码和状态的方式进行认证
		if(false == $authInfo) {
			$this->assign("jumpUrl",__URL__.'/login/');
			if($this->loginType!='helperLogin'){
				$this->error('帐号不存在或已禁用！');
			}else{
				echo '{"error":"100"}';
				exit;
			}
		}else {
			$time = time();
			// 判断是否存在密码错误登录
			$User = M('User');
			if(!$authInfo['login_error_count']){
				$User->where('id='.$authInfo['id'])->setField('last_login_time',$time);
				$User->commit();
			}
			$login_error_count = $authInfo['login_error_count'];
			if($authInfo['last_login_time']){
				$lasttimecount = $time - $authInfo['last_login_time'];
				if($lasttimecount < 86400){
					$login_error_count = $login_error_count+1;
				} else {
					$login_error_count = 1;
				}
			}
			if($this->loginType=='helperLogin'){
				$pwd = $_POST['password'];
				// echo '{"success":"1"}';
			 //  die;
			}else{
				$pwd =  md5($_POST['password']);
			}
			if($authInfo['password'] != $pwd ){
				$this->assign("jumpUrl",__URL__.'/login/');
				if($authInfo['account']!='admin') {
					$User->where('id='.$authInfo['id'])->setField('login_error_count',$login_error_count);
					$User->where('id='.$authInfo['id'])->setField('last_login_time',time());
					$User->commit();
					$error_count = 5;// 错误次数
					$login_error_count = $error_count-$login_error_count;
					unset($_SESSION);
					if ($login_error_count == 0) {
						$User->where('id='.$authInfo['id'])->setField('loginnumstatus',1);
						$User->commit();
						if($this->loginType!='helperLogin'){
							$this->error('密码错误！【今日密码已被锁定，请24小时后再试或者请联系管理员】');
						}else{
							echo '{"error":"101"}';
							exit;
						}
					} else {
						if($this->loginType!='helperLogin'){
							$this->error('密码错误！【今日再输错'.$login_error_count.'次，密码将被锁定】');
						}else{
							echo '{"error":"102"}';
							exit;
						}
					}
				} else {
					if($this->loginType!='helperLogin'){
						$this->error('密码错误！');
					}else{
						echo '{"error":"103"}';
						exit;
					}
				}
			}
           $this->setSession($authInfo);//设置session
           $this->setUserInfoCookie($authInfo);//设置UserInfo的cookie
         //  $this->setBBSCookie($authInfo);//设置BBS的cookie
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
				$User->save($data);
			}
			//更新online信息
			$online_model = M("user_online");
			$online_model->where("userid=".$authInfo['id'])->delete();

			// 缓存访问权限
			RBAC::saveAccessList();
			if(isset($_SESSION[C('ADMIN_AUTH_KEY')])){
				$re = $this->check_upgrade(false);
			}
			//dirct yuansl 2014 06 05
			//读取名人录数据
			$this->getsolidotinfor();
			//如果是从OA客户端登录的，返回真
			if($this->loginType=='helperLogin'){
				if($_REQUEST['fromOA']==2){
					//$this->success('登录成功！');
					redirect(U('Index/index'),0.01,'Please waiting  3S,page jumping...');
					//OAHelperAction::getAccessUrl();
				}else{
					echo '{"success":"1"}';
					exit;
				}
			}else{
				$this->success('登录成功！');
			}
		}
	}
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
    //对session值做设置
    //
	public function setSession($authInfo){
		
		$modeldep=M("mis_system_department");
		$all_dept_list=$modeldep->where("status=1")->select();		
		$userdeptdutymodel = M("user_dept_duty");
		$usermap=array();
		$usermap['status'] = 1;
		$usermap['userid'] = $authInfo["id"];
		$curtment_user_deptlist = $userdeptdutymodel->where($usermap)->getField("id,deptid",true);
		$curtment_user_deptlist_and_child = array();
		foreach($curtment_user_deptlist as $k=>$v){
			$deplist2 =array_unique(array_filter (explode(",",$this->getAlldept($all_dept_list,$v))));
			$curtment_user_deptlist_and_child=array_merge($curtment_user_deptlist_and_child,$deplist2);
		}
		$modeluser=M("user");
		$mapuser = array();
		$mapuser['status'] = 1;
		$mapuser['deptid']=array("in",$curtment_user_deptlist_and_child);
		//获取用户底层部门
		$alluserdept=$userdeptdutymodel->where($mapuser)->getField("userid",true);
		
		
	    //获取用户自身部门	
		$mapuser=array();
		$mapuser['status'] = 1;
		$mapuser['deptid']=array("in",$curtment_user_deptlist);
		$user_dep_all_self=$userdeptdutymodel->where($mapuser)->getField("userid",true);

		
		//获取用户组
		$model=D("role_user");
		$list=$model->where("user_id='".$authInfo['id']."'")->select();
		$aryrole=array();
		foreach($list as $val){
			$aryrole[]=$val['role_id'];
		}

        //统一设置SESSION值
		$_SESSION[C('USER_AUTH_KEY')]	= $authInfo['id'];
		$_SESSION['companyid']	= $authInfo['companyid'];
		$_SESSION['email']     = $authInfo['email'];
		$_SESSION['user_employeid']	=$authInfo['employeid'];//用户绑定员工ID
		$_SESSION['user_dep_id']	=$authInfo['dept_id'];
		$_SESSION['user_dep_all_child']=array_unique($alluserdept);
		$_SESSION['user_dep_all_self']=array_unique($user_dep_all_self);
		$_SESSION['role']	=implode(",", $aryrole);
		$_SESSION['loginUserName']		=$authInfo['name'];
		if($authInfo['account']=='admin') {
			$_SESSION[C('ADMIN_AUTH_KEY')]	=true;
		}
	}
	
	public function setUserInfoCookie($authInfo){
		$cook_userinfo['pwd'] =$authInfo['password'];
		$cook_userinfo['user']=$authInfo['account'];
		$cook_userinfo['logintime'] =$authInfo['last_login_time'];
		if($_POST["remember"]){
			Cookie::set("remember",1,365*24*86400);
		}
		Cookie::set("userinfo",$cook_userinfo,C('COOKIE_EXPIRE'));
	}
	
	public function setBBSCookie($authInfo){
		import('@.ORG.UcenterBbs', '', $ext='.php');
		$objUcenterBbs = new UcenterBbs();
		$bbsuserinfo = $objUcenterBbs->login($authInfo['account'],$authInfo['id'],$authInfo['password']);
		if(!$bbsuserinfo){
			$bbsuserinfo = $objUcenterBbs->register($authInfo['account'],$authInfo['id'],5,$authInfo['password'],$authInfo['email'], $timestamp = 0);
		}
		Cookie::set("bbsuserinfo",$bbsuserinfo,C('COOKIE_EXPIRE'));	
	}

	// 登录检查
	public function checkLogin() {
		$account = $_REQUEST["account"];
		$pwd     = $_REQUEST["password"];
		$userinfo = Cookie::get("userinfo");
		if( ($account && $pwd) || ($userinfo["user"] &&  $userinfo['pwd'])){
			if( $account && $pwd ){
				$map=array();
				$map['account']	= $account;
				$map['password']= $pwd;
			}else{
				$map=array();
				$map['account']	= $userinfo['user'];
				$map['password']= $userinfo['pwd'];
			}

			$map["status"]	=array('gt',0);
			$authInfo =RBAC::authenticate($map);
			if($authInfo){
				//cookie验证重新赋值
				if(ACTION_NAME!="getAllScheduleList"){
					$this->setBBSCookie($authInfo);
					$this->setUserInfoCookie($authInfo);
				}
				if( !isset($_SESSION[C('USER_AUTH_KEY')]) ){
	                $this->setSession($authInfo);
					//更新user_online表
					$online_model = M("user_online");
					$onlinedata=array();
					$onlinedata["modify_time"]=time();
					$onlinedata["session_id"]= session_id();
					$online_model->where("userid=".$authInfo['id'])->save($onlinedata);
				}
				// 缓存访问权限
				RBAC::saveAccessList();
				$same_time_login = intval( getCommonSettingSkey("SAME_TIME_LOGIN") );
				if( ACTION_NAME!="getAllScheduleList" ){
					$modeluseronline = D("UserOnline");
					$aMap = array();
					$aMap['userid'] = $authInfo["id"];
					$info = $modeluseronline->where($aMap)->find();
					$time = time();
					$session_id = session_id();
					if ($info) {
						//存在，则检查session_id
						if($same_time_login==0){
							if ($info['session_id'] == $session_id) {
								//修改时间
								$modeluseronline->where($aMap)->setField('modify_time', $time);
							} else {
								$this->assign("jumpUrl",__APP__.'/Public/login/');
								unset($_SESSION);
								Cookie::delete("userinfo");
								Cookie::delete("bbsuserinfo");
								Cookie::clear();
								session_destroy();
								$this->error("已在其他地方登陆,被迫下线",'','','301');
								exit;
							}
						}else{
							$modeluseronline->where($aMap)->setField('modify_time', $time);
						}

					} else {
						//如果不存在userid，插入
						$data = array();
						$data = array(
								'userid'      => $authInfo["id"],
								'session_id'  => $session_id,
								'modify_time' => $time,
								'createid'    => $authInfo["id"],
								'createtime'  => $time,
						);
						$modeluseronline->add($data);
					}
				}
				return true;
			}
		}else{
			//提示请求时返回json数据
			if( ACTION_NAME=="getAllScheduleList" ){
				$rehtml["html"]= 0;
				$rehtml['date'] = "";
				$rehtml['datalist'] = 0;
				echo json_encode($rehtml);
				exit;
			}
		}
		unset($_SESSION);
		Cookie::delete("userinfo");
		Cookie::delete("bbsuserinfo");
		Cookie::clear();
		session_destroy();
		return false;
	}

	public function profile() {
		$this->checkUser();
		$User	 =	 M("User");
		$vo	=	$User->getById($_SESSION[C('USER_AUTH_KEY')]);
		$this->assign('vo',$vo);
		$this->display();
	}
	
	//获取验证码
	public function verify()
	{
		$type	 =	 isset($_GET['type'])?$_GET['type']:'gif';
		import("@.ORG.Image");
		ob_clean();
		Image::buildImageVerify(4,1,$type,$width=45);
	}

	public function getCreateInfo(){
		$kwd = $_REQUEST['q'];
		$tab = $_REQUEST['tab'];
		$field = $_REQUEST['field'];
		$model = D($tab);
		$map[$field] = array('like',"%".$kwd."%");
		$list = $model->where($map)->getField($field.',id');
		if(!empty ($list)){
			echo json_encode(array_keys($list));
		}
		else{
			echo "搜索无数据";
		}
		exit;
	}
	public function serialnumber(){
		$model=D("SerialNumber");
		$system=array();
		$system=$model->CheckFile();
		$this->assign("crypt", $system);
		if( $_SESSION[C('ADMIN_AUTH_KEY')] ){
			$this->display();
		}else{
			echo "您禁止访问此页面,谢谢";
			exit;
		}
	}
	/**
	 *
	 *清除缓存
	 */
	function clear_cache(){
		$obj_dir = new Dir;
		if($_REQUEST['accesslist'] == 1){
			$directory =  DConfig_PATH."/AccessList";
			$obj_dir->del($directory);
		}
		if($_REQUEST['runtime'] == 1){
			$obj_dir->delDir(RUNTIME_PATH);
		}
		$this->success('删除缓存成功！');
	}


	/**
	 *
	 *系统升级检查
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
	 *
	 *系统升级
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
	
	/**
	 *
	 *系统核心升级
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
			print_r($updatefilelist);

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
		$userinfo = $userModel->where("id = ".$_SESSION [C ( 'USER_AUTH_KEY' )])->find();
		if($userinfo['solidpath']){
			$SolidotList_config_data['solidpath'] = $userinfo['solidpath'];
		}
		$this->assign('Solidot',$SolidotList_config_data);
	}
	
}
?>