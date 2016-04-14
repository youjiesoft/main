<?php
/**
 * @Title: MobileUserBaseAction
 * @Package 用户接口类
 * @Description: 请求类，取数据接口
 * @author liminggang
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d˾
 * @copyright Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d˾
 * @date 2014-2-08
 * @version V1.0
 */
class MobileUserBaseAction extends MobileBaseAction {
	/**
	 * @Title: login
	 * @Description: todo(用户登陆方法)
	 * @param 返回类型 $returnType 返回类型，【json,arr】
	 * @return 返回响应结果。
	 * @author liminggang
	 * @date 2016-1-23 下午2:53:32
	 * @throws
	 */
	public function login($returnType='json'){
		if(!empty($_REQUEST['phone']) && !empty($_REQUEST['pwd']) && !empty($_REQUEST['apptype'])){
			$userinfo=$this->checkLogin('arr',$_REQUEST['apptype']);
			//登陆成功
			$data=array();
			if($userinfo){
				$data['userid']=$userinfo['id'];
				$data['phone']=$userinfo['phone'];
				$data['password']=$userinfo['pwd'];
				$data['logintime']=time();
				//获取token
				$token=$this->setToken($data);
				$data['token']=$token;
				$code='1000';
		 		$msg='1000:登录成功';
			}else{
				$data=array();
				$code='1002';
				$msg='1002:密码错误';
			}
		}else{
			$data=array();
			$code='1003';
			$msg='1003:非法访问';			
		}
		return $this->getReturnData($data,$returnType,$code,$msg);	
	}
	
	/**
	 * @Title: checkLogin
	 * @Description: todo(验证用户名和密码是否正确)
	 * @param 返回类型 $returnType 返回类型，【json,arr】
	 * @return 返回响应结果。 1000:用户验证成功，1003:用户验证不符
	 * @author liminggang
	 * @date 2016-1-23 下午2:53:32
	 * @throws
	 */
	public function checkLogin($returnType='json',$apptype){
		//核验用户名与密码
		$map=array();
		$map['phone']=$_REQUEST['phone'];
		$map['pwd']=$_REQUEST['pwd'];
		if($apptype = 'Driver'){//注册的是什么端口 Driver司机端  Owner 货主端
			$result=D('MisAutoZzx')->where($map)->find();
		}else if($apptype = 'Owner'){
			$result=D('MisAutoEes')->where($map)->find();
		}
		if($result){
		 	//检查情况下直接返回
		 	if($returnType=='check'){
		 		return;
		 	}else{
            		$data=$result;
            		$code='1000';
           			$msg='用户验证成功';
		 	} 	
		 }else{
		 			$data=array();
		 			$code='1003';
		 			$msg='1003:用户验证不符'; 	
		 }
		 return $this->getReturnData($data,$returnType,$code,$msg);
	}
	
	/**
	 * @Title: register
	 * @Description: todo(注册或修改账号信息)
	 * @param 返回类型 $returnType 返回类型，【json,arr】
	 * @return 返回响应结果。 1000:用户验证成功，1003:非法访问
	 * @author liminggang
	 * @date 2016-1-23 下午2:53:32
	 * @throws
	 */
	public function register($returnType='json'){
		/*
		 * 此方法必须传入3个参数，
		 * 1：手机号码
		 * 2：验证码
		 * 3：登陆密码
		 */
	 	if(!empty($_REQUEST['phone']) && !empty($_REQUEST['verifycode']) && !empty($_REQUEST['pwd']) && !empty($_REQUEST['apptype'])){
		 	//核验用户名与密码
			$map=array();
			$map['phone']=$_REQUEST['phone'];
			$map['verifycode']=$_REQUEST['verifycode'];
			
			if($_REQUEST['apptype']=="Driver"){//判断使用的是那个注册表
				$modelname = "MisAutoZzx";
			}else if($_REQUEST['apptype']=="Owner"){
				$modelname = "MisAutoEes";
			}else{
				$data=array();
				$code='1003';
				$msg='1003:非法访问';
				return $this->getReturnData($data,$returnType,$code,$msg);
			}
			
			$userInfo=D($modelname)->where($map)->find();
			//echo D('MisAutoCsb')->getLastSql();
			if($userInfo){
				//修改类型
				$sign='save';
				//如果存在，则将状态改为1
				$map['id']=$userInfo['id'];	
				$data['status']=1;
				$data['pwd']=$_REQUEST['pwd'];
			 	$result=$this->CURD($modelname,'save',$data,$map);
			}else{
				//新增类型
				$sign='add';
				$data['phone']=$_REQUEST['phone'];
				$data['pwd']=$_REQUEST['pwd'];		
			 	$result=$this->CURD($modelname,'add',$data);
		 	}
		 	if($result===false){
		 		$data=array();
		 		$code='1001';
		 		$msg='1001:用户注册失败';
		 		
		 	}else{
		 		//token需要id,account,password三个数据源来做数据处理
		 		switch($sign){
		 			case 'add':
		 				//只需要一个userid重新返回
		 				$returnData['userid']=$result;
		 				$returnData['phone']=$_REQUEST['phone'];
		 				$returnData['password']=$_REQUEST['pwd'];
		 				$returnData['logintime']=time();
		 				break;
	 				case 'save':
	 					//这里需要根据userinfo的数据获取id,account,password
	 					$returnData['userid']=$userInfo['id'];
	 					$returnData['phone']=$userInfo['phone'];
	 					$returnData['password']=$_REQUEST['pwd'];
	 					$returnData['logintime']=time();
	 					break;
		 		}
		 		//lookupInsertUser($_REQUEST['phone'],$_REQUEST['phone'],$_REQUEST['phone'],$_REQUEST['pwd'],3,true,true,1);
		 		//获取token
		 		$token=$this->setToken($returnData);
		 		$data['token']=$token;
		 		$code='1000';
		 		$msg='1000:登录成功';
		 	}
	 	}else{
	 		$data=array();
	 		$code='1003';
	 		$msg='1003:非法访问';	 		
	 	}
	 	return $this->getReturnData($data,$returnType,$code,$msg);	 	
	 }
	 /**
	  * @Title: getVerificationCode
	  * @Description: todo(获取验证码)
	  * @param 返回类型 $returnType 返回类型，【json,arr】
	  * @return 返回响应结果。 1000:用户验证成功,1006:获取验证码失败,1007:非法访问,未检查到手机
	  * @author liminggang
	  * @date 2016-1-23 下午2:53:32
	  * @throws
	  */
	 public function getVerificationCode($returnType='json'){
	 	if(!empty($_REQUEST['phone']) && is_numeric($_REQUEST['phone']) && !empty($_REQUEST['apptype'])){
	 		$verifycode=rand(1000,9999);
	 		//echo $verifycode;
	 		$phone=$_REQUEST['phone'];//手机号码
	 		$type=$_REQUEST['type']?$_REQUEST['type']:1;//类型
	 		if($_REQUEST['apptype']=="Driver"){//判断使用的是那个注册表
	 			$modelname = "MisAutoZzx";
	 		}else if($_REQUEST['apptype']=="Owner"){
	 			$modelname = "MisAutoEes";
	 		}
	 		$content=$verifycode."(您获取的手机验证码),有效期为24小时，感谢您注册货巴士物流。如非本人操作，请忽略此短信。";
	 		$userid=$this->checkPhoneExist($phone,$type,$modelname);
	 		if($userid){
	 			//存在手机号，回写验证码到数据库
	 			$map=array();
	 			$map['id']=$userid;
	 			$data['verifycode']=$verifycode;
	 			$result=$this->CURD($modelname,'save',$data,$map);
	 		}else{
	 			//不存在手机号，插入到数据库
	 			$data['account']=$phone;
	 			$data['phone']=$phone;
	 			$data['verifycode']=$verifycode;
	 			$data['createtime']=time();
	 			$data['status']=0;
	 			$result=$this->CURD($modelname,'add',$data);
	 		}
	 		if($result){
	 			//查询短信发送一个手机号短信间隔最短60s 一个小时不超过3条 ，一天不超过5条
	 			$phoneHisteryMpdel=D('MisSystemPhoneHistery');
	 			//首先查询是否发送了5条数据
	 			$dstarttime=strtotime(date('Y-m-d',time()));
	 			$dendtime=strtotime(date('Y-m-d',strtotime('+1 day')));
	 			$dphMap['createtime']=array(array('egt',$dstarttime),array('lt',$dendtime)) ;
	 			$dphMap['phone']=$phone;
	 			$dphList=$phoneHisteryMpdel->where($dphMap)->count();
	 			$phListInfo=$phoneHisteryMpdel->where($dphMap)->order('id desc ')->find();
	 			//查询一个小时内发送的数据
	 			$hendtime=$phListInfo['createtime'];
	 			$hstarttime=strtotime(date('Y-m-d H:i:s',strtotime('-1 hour',$hendtime)));
	 			$hphMap['createtime']=array(array('egt',$hstarttime),array('lt',$hendtime)) ;
	 			$hphMap['phone']=$phone;
	 			$hphList=$phoneHisteryMpdel->where($hphMap)->count();
	 			$stime=time()-$hendtime;
	 			if($dphList<5 &&  $hphList<3 && $stime>=60){
	 				//调用短信接口
	 				import('@.ORG.SmsHttp');
	 				$smsHttp= new SmsHttp;
	 				//短信操作
	 				$smsNotic=$smsHttp->getMessage($phone,$content,$httpType=1);
	 				//回写状态正常时
	 				$data["phone"]=$_REQUEST['phone'];
	 				$code='1000';
	 				$msg=$smsNotic."验证码为：".$verifycode;
	 				//添加号码发送短信记录
	 				$phdate['phone']=$phone;
	 				$phdate['createtime']=time();
	 				$phresult=$this->CURD('MisSystemPhoneHistery','add',$phdate);
	 			}else{
	 				$data=array();
	 				$code='1006';
	 				if($dphList>=5){
	 					$msg='1006:当天验证码获取超过5条';
	 				}elseif($hphList>=3){
	 					$msg='1006:一个小时内验证码获取超过3条';
	 				}elseif($stime<60){
	 					$msg='1006:上次获取验证码时间不足60s';
	 				}
	 			}
	 		}else{
	 			$data=array();
	 			$code='1006';
	 			$msg='1006:获取验证码失败';
	 		}
	 	}else{
	 		$data=array();
	 		$code='1007';
	 		$msg='1007:非法访问：未有手机号';
	 	}
	 	return $this->getReturnData($data,$returnType,$code,$msg);
	 }
	
	public function checkVerificationCode($returnType='json'){
	 	if(empty($_REQUEST['phone']) && empty($_REQUEST['verifycode'])){
	 		$data=array();
	 		$code='1003';
	 		$msg='1003:非法访问';
	 		return $this->getReturnData($data,$returnType,$code,$msg);
	 	}
	 	if($_REQUEST['apptype']=="Driver"){//判断使用的是那个注册表
	 		$modelname = "MisAutoZzx";
	 	}else if($_REQUEST['apptype']=="Owner"){
	 		$modelname = "MisAutoEes";
	 	}
	 	//核验用户名与密码
	 	$map=array();
	 	$map['phone']=$_REQUEST['phone'];
	 	$map['verifycode']=$_REQUEST['verifycode'];
	 	$result=D($modelname)->where($map)->find();
	 	if($result){
	 		$data=$result;
	 		$code='1000';
	 		$msg='验证成功';
	 	}else{
	 		$data=array();
	 		$code='1005';
	 		$msg='1005:验证不符';
	 	}
	 	return $this->getReturnData($data,$returnType,$code,$msg);
	 }
	 /**
	  * @Title: checkPhoneExist
	  * @Description: todo(获取验证码)
	  * @param 验证对象【手机号码】 $phone
	  * @param 验证类型 $type 1：注册，2：找回密码
	  * @return 返回响应结果 boolean  false或者true
	  * @author liminggang
	  * @date 2016-1-23 下午2:53:32
	  * @throws
	  */
	 private function checkPhoneExist($phone,$type){
	 	if($phone){
	 		$map=array();
	 		$map['phone']=$phone;
	 		$result=D('MisAutoCsb')->where($map)->find();
	 		if($result){
	 			//新用户注册
	 			switch($type){
	 				case 1://新用户注册
	 					//状态已经为1时
	 					if($result['status']){
	 						return $this->getReturnData($data,'json','1010',"用户已经存在");
	 						exit;
	 					}else{
	 						//状态为0时
	 						return $result['id'];
	 					}
	 					break;
	 				case 2://找回密码
	 					return $result['id'];
	 					break;
	 			}
	 		}else{
	 			return false;
	 		}
	 	}else{
	 		return false;
	 	}
	}
	/**
	 * lookupInsertUser  添加特殊用户
	 * @param 用户名 $account
	 * @param $name
	 * @param $zhname
	 * @param 权限组:  $roleid    
	 * @param $isCreatePassword:是否生成密码 （true是 false否）
	 * @param $createDefaultPassword 是否生成默认密码  当没有密码的时候生成123456默认密码
	 * @param $type   1新增 2修改 3删除
	 * xiayuqin
	 */
	function lookupInsertUser($account,$name,$zhname,$password,$roleid,$isCreatePassword=true,$createDefaultPassword=false,$type=1,$status=1,$rand=null){
		$userModel=M('user');
		$roleUserModel=M('rolegroup_user');
		if(empty($account)){
			$account=$_REQUEST['orderno'];
		}
		if(empty($name)){
			$name=$_REQUEST['name'];
		}
		if(empty($zhname)){
			$zhname=$_REQUEST['name'];
		}
		if(empty($password)){
			$password=$_REQUEST['pwd'];
		}
		//货主管理插入数据
		if($type==1){
			$xmtData['orderno']=$account;
			$xmtData['name']=$account;
			$xmtData['pwd']=$password;
			$userlist=$this->CURD('MisAutoXmt','add',$xmtData);
		}elseif($type==2){
			$xmtData['orderno']=$account;
			$xmtData['name']=$account;
			$xmtData['pwd']=$password;
			$xmtmap['orderno']=$account;
			$xmtmap['name']=$account;
			$xmtmap['pwd']=$password;
			$userlist=$this->CURD('MisAutoXmt','save',$xmtData,$xmtmap);
		}
		if($userlist==false){
			$data=array();
			$code='1001';
			$msg='1001:货主保存失败';
			return $this->getReturnData($data,$returnType,$code,$msg);
		}
		if($isCreatePassword){
			if($password){
				$password=md5($password);
			}else{
				if($createDefaultPassword){
					$password=md5('123456');
				}
			}
		}
		if($type==1){
			//新增
			$userData['account']=$account;
			$userData['name']=$name;
			$userData['zhname']=$zhname;
			$userData['password']=$password;
			$userData['verify']=$rand;
			$userData['updatetime']=time();
			if($status==1){
				$userData['status']=1;
			}else{
				$userData['status']=0;
			}
			$userlist=$this->CURD('User','add',$userData);
			//$userlist=$userModel->add($userData);
			if($userlist==false){
				// 				$this->error('用户保存失败');
				$data=array();
				$code='1001';
				$msg='1001:用户保存失败';
				return $this->getReturnData($data,$returnType,$code,$msg);
			}else{
				//添加用户组
				$roleData['rolegroup_id']=$roleid;
				$roleData['user_id']=$userlist;
				$roleList=$this->CURD('RolegroupUser','add',$roleData);
				//$roleList=$roleUserModel->add($roleData);
				if($roleList==false){
					//$this->error('用户组保存失败');
					$data=array();
					$code='1001';
					$msg='1001:用户组保存失败';
					return $this->getReturnData($data,$returnType,$code,$msg);
				}else{
					//添加基础组
					$baseroleMap['rolegroup_id']=1;
					$baseroleMap['user_id']=$userlist;
					$roleList=$this->CURD('RolegroupUser','add',$baseroleMap);
					// 					$baseroleList=$roleUserModel->add($baseroleMap);
					if($baseroleList==false){
						//$this->error('用户基础组保存失败');
						$data=array();
						$code='1001';
						$msg='1001:用户基础组保存失败';
						return $this->getReturnData($data,$returnType,$code,$msg);
					}
				}
			}
		}else if($type==2){
			//新增
			$usermap['account']=$account;
			$userData['name']=$name;
			$userData['zhname']=$zhname;
			$userData['password']=$password;
			$userData['verify']=$rand;
			$userData['updatetime']=time();
			if($status==1){
				$userData['status']=1;
			}else{
				$userData['status']=0;
			}
			//$userlist=$userModel->where($usermap)->save($userData);
			//dump($userModel->getlastsql());
			$userlist=$this->CURD('User','save',$userData,$usermap);
				
			//$this->CURD('User','save',$userData,$usermap);
			//$userlist=$userModel->add($userData);
			if($userlist==false){
				// 				$this->error('用户保存失败');
				$data=array();
				$code='1001';
				$msg='1001:用户保存失败';
				return $this->getReturnData($data,$returnType,$code,$msg);
			}
		}
		return $userlist;
	}
}