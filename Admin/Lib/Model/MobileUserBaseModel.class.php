<?php
/**
 * @Title: MobileUserBaseModel
 * @Package 用户接口类
 * @Description: 请求类，取数据接口
 * @author liminggang
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d˾
 * @copyright Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d˾
 * @date 2014-2-08
 * @version V1.0
 */
class MobileUserBaseModel extends MobileBaseModel {
	/**
	 * @Title: login
	 * @Description: todo(用户登陆方法)
	 * @param 手机号 $phone 手机号码
	 * @param 密码 $pwd
	 * @param 类型 $apptype Driver：司机端，Owner：货主端
	 * @param 返回类型 $returnType 返回类型，【json,arr】
	 * @return 返回响应结果。
	 * @author liminggang
	 * @date 2016-1-23 下午2:53:32
	 * @throws
	 */
	public function login($phone,$pwd,$apptype,$returnType='json'){
		if(!empty($phone) && !empty($pwd) && !empty($apptype)){
			$userinfo=$this->checkLogin($phone,md5($pwd),$apptype);
			//登陆成功
			$data=array();
			if($userinfo){
				$data['userid']=$userinfo['id'];
				$data['phone']=$userinfo['phone'];
				$data['password']=$userinfo['pwd'];
				$data['shenhezhuangtai'] = $userinfo['shenhezhuangtai'];
				$data['zhenshixingming'] = $userinfo['zhenshixingming'];
				$data['leixing'] = $userinfo['leixing'];
				$data['wuliugongsi'] = $userinfo['wuliugongsi']?$userinfo['wuliugongsi']:0;
				$data['apptype'] = $apptype;
				if($apptype == Driver){//如果为司机端则多返回 司机类型 和物流公司名称
					$data['leixing'] = $userinfo['leixing'];
					$data['wuliugongsi'] = $userinfo['wuliugongsi'];
				}
				$data['logintime']=time();
				//获取token
				$token=$this->setToken($data);
				$data['token']=$token;
				return $data;
			}
		}else{
			throw new AppNullParamException();
		}
	}
	
	/**
	 * @Title: checkLogin
	 * @Description: todo(验证用户名和密码是否正确)
	 * @param 手机号 $phone 手机号码
	 * @param 密码 $pwd 密码
	 * @param 返回类型 $returnType 返回类型，【json,arr】
	 * @return 返回响应结果。 1000:用户验证成功，1003:用户验证不符
	 * @author liminggang
	 * @date 2016-1-23 下午2:53:32
	 * @throws
	 */
	 public function checkLogin($phone,$pwd,$apptype){
		if($phone && $pwd && $apptype){
			//核验用户名与密码
			$map=array();
			$map['phone']=$phone;
			$map['pwd']=$pwd;
			//注册的是什么端口 Driver司机端  Owner 货主端
			$modelname = $this->getModelName($apptype);
			$data=D($modelname)->where($map)->find();
			if($data){
				return $data;
			}else{
				//抛出1008异常，用户验证不符
				throw new AppNullParamException("账号失效，请重新登陆！",100001);
			}
		}else{
			//抛出异常，1002缺少必要参数
			throw new AppNullParamException();
		}
	}
	
	/**
	 * @Title: register
	 * @Description: todo(注册或修改账号信息)
	 * @param 手机号 $phone 手机号码
	 * @param 验证码 $verifycode 验证码
	 * @param 密码 $pwd 密码
	 * @param 类型 $apptype Driver：司机端，Owner：货主端
	 * @param 返回类型 $returnType 返回类型，【json,arr】
	 * @return 返回响应结果。 1000:用户验证成功，1003:非法访问
	 * @author liminggang
	 * @date 2016-1-23 下午2:53:32
	 * @throws
	 */
	public function register($phone,$verifycode,$pwd,$apptype,$zhenshixingming,$returnType='json'){
	 	if(!empty($phone) && !empty($verifycode) && !empty($pwd) && !empty($apptype)){
	 		$this->transaction_model=M();
	 		$this->transaction_model->startTrans();
		 	//核验用户名与密码
			$map=array();
			$map['phone']=$phone;
			$map['verifycode']=$verifycode;
			//判断使用的是那个注册表
			$modelname = $this->getModelName($apptype);
			$userInfo=D($modelname)->where($map)->find();
			if($userInfo){
				//修改类型
				$sign='save';
				//如果存在，则将状态改为1
				$map['id']=$userInfo['id'];	
				$data['status']=1;
				$data['pwd']=md5($pwd);
				$data['zhenshixingming'] = $zhenshixingming;
				$data['shenhezhuangtai']=0;
			 	$result=$this->CURD($modelname,$sign,$data,$map);
			}else{
				//新增类型
				$sign='add';
				$data['phone'] = $phone;
				$data['pwd'] = md5($pwd);
				$data['shenhezhuangtai']=0;
				$data['zhenshixingming'] = $zhenshixingming;
			 	$result=$this->CURD($modelname,$sign,$data);
		 	}
		 	if($result===false){
		 		$this->transaction_model->rollback();
		 		//抛出异常，1004用户注册失败
		 		throw new AppUserException("用户注册失败");
		 	}else{
		 		$this->transaction_model->commit();
		 		return true;
		 	}
	 	}else{
	 		//抛出异常，1002缺少必要参数
	 		throw new AppNullParamException();
	 	}
	 }
	 /**
	  * @Title: getVerificationCode
	  * @Description: todo(获取验证码)
	  * @param 手机号 $phone 手机号码
	  * @param 类型 $type 类型： 1为注册 2为修改
	  * @param 类型 $apptype Driver：司机端，Owner：货主端
	  * @param 返回类型 $returnType 返回类型，【json,arr】
	  * @return 返回响应结果。 1000:用户验证成功,1006:获取验证码失败,1007:非法访问,未检查到手机
	  * @author liminggang
	  * @date 2016-1-23 下午2:53:32
	  * @throws
	  */
	 public function getVerificationCode($phone,$type,$apptype,$returnType='json'){
	 	//判断是否有手机号码传输过来
	 	if(!empty($phone) && is_numeric($phone)){
	 		//随机生成一个验证码
	 		$verifycode=rand(1000,9999);
	 		//发送短信内容拼装
	 		//$content=$verifycode."(您获取的手机验证码),有效期为24小时，感谢您注册货巴士物流。如非本人操作，请忽略此短信。";
	 		$content=$verifycode;//云片推送验证码格式 只需要传入验证码
	 		//获取模型名称
	 		$modelname = $this->getModelName($apptype);
	 		//验证手机号码和注册类型。判断当前手机是否已经存在
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
	 			$obj =new MobileOpenExtendSmsModel('skyyunpian');
	 			$obj->getMessageInfo($phone,$content,$httpType=1);
	 		}else{
 				//抛出异常，1005获取验证码失败
 				throw new AppEmailException("获取验证码失败");
	 		}
	 	}else{
	 		//抛出异常，1002缺少必要参数
	 		throw new AppNullParamException();
	 	}
	 }
	
	 /**
	  * @Title: checkVerificationCode
	  * @Description: todo(验证验证码)
	  * @param 手机号 $phone 手机号码
	  * @param 验证码 $verifycode 验证码
	  * @param 类型 $apptype Driver：司机端，Owner：货主端
	  * @param 返回类型 $returnType 返回类型，【json,arr】
	  * @return 返回响应结果。 1000:用户验证成功,1006:获取验证码失败,1007:非法访问,未检查到手机
	  * @author liminggang
	  * @date 2016-1-23 下午2:53:32
	  * @throws
	  */
	public function checkVerificationCode($phone,$verifycode,$apptype,$returnType='json'){
		//获取手机号码和验证码
	 	if(empty($phone) && empty($verifycode)){
	 		//抛出异常，1002缺少必要参数
	 		throw new AppNullParamException();
	 	}
	 	/*
	 	 * 判断用户注册的是什么类型
	 	 * Driver 司机端注册
	 	 * Owner 货主端注册
	 	 */
	 	$modelname = $this->getModelName($apptype);
	 	//核验用户名与验证码
	 	$map=array();
	 	$map['phone']=$phone;
	 	$map['verifycode']=$verifycode;
	 	$data=D($modelname)->where($map)->find();
	 	//为用户添加一个账户我的钱包
	 	$MisAutoNidModel=D('MisAutoNid');
	 	$map = array();
	 	if($apptype == "Owner"){
	 		$map['yonghuleixing']=1;
	 		$map['huozhu']=$data['id'];
	 		$dataadd['yonghuleixing']=1;
	 		$dataadd['huozhu']=$data['id'];
	 		$dataadd['yue']=0;
	 	}elseif($apptype == "Driver"){
	 		$siji=$_REQUEST['uid'];
	 		$map['yonghuleixing']=2;
	 		$map['siji']=$siji;
	 		$dataadd['yonghuleixing']=2;
	 		$dataadd['siji']=$data['id'];
	 		$dataadd['yue']=0;
	 	}
	 	//自动生成单号
	 	$scnmodel = D('SystemConfigNumber');
	 	//生成运单号
	 	$ordernoInfo = $scnmodel->getOrderno("mis_auto_tnhwy","MisAutoNid");
	 	if(!$ordernoInfo['orderno']){
	 		throw new AppNullParamException("充值单号生成失败。");
	 	}
	 	$dataadd['orderno'] = $ordernoInfo['orderno'];
	 	$list=$MisAutoNidModel->where($map)->find();
	 	if(empty($list)){
	 		$this->transaction_model=M();//实例化事物模型
	 		$this->transaction_model->startTrans();//开启事物
	 		//没有数据添加数据
	 		$result=$this->CURD('MisAutoNid', 'add', $dataadd);
	 		if($result!==false){
	 			$this->transaction_model->commit();//事物提交
	 			
	 		}else{
	 			$this->transaction_model->rollback();//事物回滚
	 			throw new AppUserException("我的钱包生成失败");
	 		}
	 	}
	 	if($data){
	 		 return $data;
	 	}else{
	 		//抛出异常，1004验证码错误
	 		throw new AppUserException("验证码错误");
	 	}
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
	 private function checkPhoneExist($phone,$type,$modelname){
	 	if($phone){
	 		$map=array();
	 		$map['phone']=$phone;
	 		$result=D($modelname)->where($map)->find();
	 		if($result){
	 			//新用户注册
	 			switch($type){
	 				case 1://新用户注册
	 					//状态已经为1时
	 					if($result['status']){
	 						throw new AppUserException("用户已经存在");
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
	 * @Title: setPassword
	 * @Description: todo(修改密码)
	 * @param 验证对象【手机号码】 $phone
	 * @param 旧密码  $oldpwd
	 * @param 新密码 $newpwd
	 * @param 验证模型对象 $apptype Driver：司机端，Owner：货主端
	 * @return 返回响应结果 boolean  false或者true
	 * @author liminggang
	 * @date 2016-1-23 下午2:53:32
	 * @throws
	 */
	public function setPassword($phone,$oldpwd,$newpwd,$apptype){
		//获取手机号码和验证码
		if(empty($phone) && empty($oldpwd) && empty($newpwd) && empty($apptype)){
			//抛出异常，1002缺少必要参数
			throw new AppNullParamException();
		}
		//根据不同端口。修改不同类型账号的密码
		$modelname = $this->getModelName($apptype);
		$map = array();
		$map['phone'] = $phone;
		$map['pwd'] = md5($oldpwd);
		$model = D($modelname);
		//旧密码验证
		$id = $model->where($map)->getField("id");
		if(!$id){
			throw new AppUserException("旧密码不正确");
		}
		$data = array();
		$where['id'] = $id;
		$data['pwd'] = md5($newpwd);
		$result = $this->CURD($modelname, 'save', $data,$where);
		if($result){
			return true;
		}else{
			throw new AppUserException("密码修改失败！");
		}
	}
	public function getRenZhenUser($userid,$apptype){
		if(!empty($userid) && !empty($apptype)){
			//核验用户名与密码
			$map=array();
			$map['id']=$userid;
			//注册的是什么端口 Driver司机端  Owner 货主端
			$modelname = $this->getModelName($apptype);
			$data=D($modelname)->where($map)->find();
			if($data){
				return $data;
			}else{
				throw new AppNullParamException("用户失效，请重新登陆",100001);
			}
		}else{
			throw new AppNullParamException();
		}
	}
	/**
	 * @Title: getUserInfo
	 * @Description: todo(获取登陆人信息)
	 * @param 数据id值 $id
	 * @param 验证模型对象 $apptype Driver：司机端，Owner：货主端
	 * @return 返回响应结果 boolean  false或者true
	 * @author liminggang
	 * @date 2016-1-23 下午2:53:32
	 * @throws
	 */
	public function getUserInfo($id,$tablename,$fieldname,$apptype){
		if(!empty($id) && !empty($apptype)){
			//核验用户名与密码
			$map=array();
			$map['id']=$id;
			//注册的是什么端口 Driver司机端  Owner 货主端
			$modelname = $this->getModelName($apptype);
			$data=D($modelname)->where($map)->find();
			if($data){
				//查询头像信息
				$mis_attached_recordDao = M("mis_attached_record");
				$where = array();
				$where['tablename'] = $tablename;
				$where['tableid'] = $id;
				$where['fieldname']=$fieldname;
				$attached= $mis_attached_recordDao->where($where)->field("attached")->find();
				$data['attached'] = $attached['attached'];
				
				//两个不同端获取的Url地址不一样 司机获取 身份证和驾驶证URL地址 而 货主只获取身份证
				if($apptype=="Driver"){
					$where['fieldname']="jiashizheng";//驾驶证URL地址
					$attached= $mis_attached_recordDao->where($where)->field("attached")->find();
					$data['jiashizheng'] = $attached['attached'];
						
					$where['fieldname']="xingshizheng";//行驶证
					$attached= $mis_attached_recordDao->where($where)->field("attached")->find();
					$data['xingshizheng'] = $attached['attached'];
				}
				$where['fieldname']="shenfenzhengzhaopian";//身份证正面
				$attached= $mis_attached_recordDao->where($where)->field("attached")->find();
				$data['shenfenzhengzhaopian'] = $attached['attached'];
				
				$where['fieldname']="shouchishenfenzhengz";//身份证背面
				$attached= $mis_attached_recordDao->where($where)->field("attached")->find();
				$data['shouchishenfenzhengz'] = $attached['attached'];
			}
			return $data;
		}else{
			throw new AppNullParamException();
		}
	}
	
	public function saveNichen($id,$nechen,$apptype){
		if(!empty($id) && !empty($nechen) && !empty($apptype)){
			//获取修改的模型对象
			$modelname = $this->getModelName($apptype);
			$data = array();
			$data['nechen'] = $nechen;
			$option['id'] = $id;
			$phresult=$this->CURD($modelname,'save',$data,$option);
			if($phresult == false){
				throw new AppServerException();
			}
			return $phresult;
		}else {
			throw new AppNullParamException();
		}
	}
	
	/**
	 * @Title: getModelName
	 * @Description: todo(根据条件获取不同的模型名称)
	 * @param APP类型 $apptype Driver：司机端，Owner：货主端
	 * @return 返回不同的模型名称
	 * @author liminggang
	 * @date 2016-1-23 下午2:53:32
	 * @throws
	 */
	 public function getModelName($apptype){
		//定义一个空变量
		$modelname = "";
		if($apptype){
			if($apptype=="Driver"){//判断使用的是那个注册表
				$modelname = "MisAutoZzx";
			}else if($apptype=="Owner"){
				$modelname = "MisAutoEes";
			}
		}
		return $modelname;
	}

}