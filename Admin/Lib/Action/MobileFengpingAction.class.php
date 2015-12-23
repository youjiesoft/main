<?php
/**
 * @Title: MobileFengpingAction
 * @Package 风平物流接口类
 * @Description: 请求类，取数据接口
 * @author eagle
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d˾
 * @copyright Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d˾
 * @date 2014-2-08
 * @version V1.0
 */
class MobileFengpingAction extends MobileBaseAction {
	//保存类实例的静态成员变量	
	private $userid=NULL;//当前登录用户ID
	private $username=NULL;//当前登录用户账号
	private $token=NULL;//当前令牌
	private $transaction_model=NULL;//事务模型
    private $notoken=array('register','login','getVerificationCode','checkVerificationCode','getHuoyuanList','getHuoyuanDetail','setPassword','resetPassword','getSearchHot','getCarType','yxtest','smsTest','getShare','getVersion','serverLink','registerExtend','checkUserExtend','findPassword','passwordReset','lookupInsertUser','getHelp','getVerifyCode','registerExtendOne','checkAcount','checkVerify');
	
	public function __construct(){
		//适配转换
		if(isset($_REQUEST['username']))$_REQUEST['account']=$_REQUEST['username'];
		if(isset($_REQUEST['password']))$_REQUEST['pwd']=$_REQUEST['password'];
		//是否注册方法标示位
		$register=false;
		foreach($_REQUEST['_URL_'] as $key => $val ){
			if(in_array($val,$this->notoken)){
				$register=true;
			}
		}
		//直接跳出构造函数            
		if($register){ return ;}
// 		$this->userid = 11;
// 		$_SESSION [C ( 'USER_AUTH_KEY' )] = $this->userid;
		//初步界定token是一个包含用户名，密码，用户ID的数组，经过base64加密后的字符串
		try{
			if($_REQUEST['token']){			
				//对token做解析
				//1反向转换2base64解密3反序列化
				$token=unserialize(base64_decode(strrev($_REQUEST['token'])));
				//print_r($token);
				//这里做了一下转换
				$_REQUEST['userid']=$token['userid'];
				$_REQUEST['account']=$token['account'];
				$_REQUEST['pwd']=$token['password'];
				try{
					if((!empty($_REQUEST['account'])) && (!empty($_REQUEST['pwd']))){
						$this->checkLogin('check');
					}else{
						throw new Exception('Division by zero.');
					}
				}catch(Exception $e){
						$data=array();
			 			$code='1003';
			 			$msg='1003:令牌错误'; 	
			 			return $this->getReturnData($data,'json',$code,$msg);
				}
			}else{			
					throw new Exception('Division by zero.');
			}
		}catch(Exception $e){
			$data=array();
			$code='1003';
			$msg='1003:令牌不存在';
			return $this->getReturnData($data,'json',$code,$msg);
		}
	}

	
	
	
	public function login($returnType='json'){
		if(!empty($_REQUEST['account']) && !empty($_REQUEST['pwd'])){
			$userinfo=$this->checkLogin('arr');
			//登陆成功
			$data=array();
			if($userinfo){
				$data['userid']=$userinfo['id'];
				$data['account']=$userinfo['account'];
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

	
	public function checkLogin($returnType='json'){
		//核验用户名与密码
		$map=array();
		$map['account']=$_REQUEST['account'];
		$map['pwd']=$_REQUEST['pwd'];
	    $result=D('MisAutoCsb')->where($map)->find();
	    //$result=$this->getMisSystemDataview('fpappsjView',$map);
	    //echo D('MisAutoCsb')->getLastSql();
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

	/*
	 * register
	 * 用户注册
	 */
	 public function register($returnType='json'){
	 	if(!empty($_REQUEST['account']) && !empty($_REQUEST['verifcode']) && !empty($_REQUEST['pwd'])){
		 	//核验用户名与密码
			$map=array();
			$map['account']=$_REQUEST['account'];
			$map['verifcode']=$_REQUEST['verifcode'];
			$userInfo=D('MisAutoCsb')->where($map)->find();
			//echo D('MisAutoCsb')->getLastSql();
			if($userInfo){
				//修改类型
				$sign='save';
				//如果存在，则将状态改为1
				$map['id']=$userInfo['id'];	
				$data['status']=1;
				$data['pwd']=$_REQUEST['pwd'];
			 	$result=$this->CURD('MisAutoCsb','save',$data,$map);
			}else{
				//新增类型
				$sign='add';
				$data['account']=$_REQUEST['account'];
				$data['phone']=$_REQUEST['account'];
				$data['pwd']=$_REQUEST['pwd'];		
			 	$result=$this->CURD('MisAutoCsb','add',$data);
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
		 				$returnData['account']=$_REQUEST['account'];
		 				$returnData['password']=$_REQUEST['pwd'];
		 				$returnData['logintime']=time();
		 				break;
	 				case 'save':
	 					//这里需要根据userinfo的数据获取id,account,password
	 					$returnData['userid']=$userInfo['id'];
	 					$returnData['account']=$userInfo['account'];
	 					$returnData['password']=$_REQUEST['pwd'];
	 					$returnData['logintime']=time();
	 					break;
		 		}		 				
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
	 
	 private function  checkPhoneExist($phone,$type){
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
	 }
	 /**
	 getVerificationCode 获取司机验证码
	 */
	 public function getVerificationCode($returnType='json'){
	 	if(!empty($_REQUEST['phone']) && is_numeric($_REQUEST['phone'])){
		 	$verifcode=rand(1000,9999);
		 	//echo $verifcode;
		 	$phone=$_REQUEST['phone'];//手机号码
		 	$type=$_REQUEST['type'];//类型
		 	$content=$verifcode."(您获取的手机验证码),有效期为24小时，感谢您注册风平物流。如非本人操作，请忽略此短信。";
		 	$userid=$this->checkPhoneExist($phone,$type);
		 	if($userid){
		 		//存在手机号，回写验证码到数据库
		 		$map=array();
		 		$map['id']=$userid;
		 		$data['verifcode']=$verifcode;
		 		$result=$this->CURD('MisAutoCsb','save',$data,$map);
		 	}else{
		 		//不存在手机号，插入到数据库
		 		$data['account']=$phone;
		 		$data['phone']=$phone;
		 		$data['verifcode']=$verifcode;
		 		$data['createtime']=time();
		 		$data['status']=0;
		 		$result=$this->CURD('MisAutoCsb','add',$data);
		 	}
		 	//echo 123;
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
					$data=$result;
					$code='1000';
					$msg=$smsNotic."验证码为：".$verifcode;
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
	 	if(empty($_REQUEST['phone']) && empty($_REQUEST['verifcode'])){
	 		$data=array();
	 		$code='1003';
	 		$msg='1003:非法访问';
	 		return $this->getReturnData($data,$returnType,$code,$msg);
	 	}
	 	//核验用户名与密码
	 	$map=array();
	 	$map['phone']=$_REQUEST['phone'];
	 	$map['verifcode']=$_REQUEST['verifcode'];
	 	$result=D('MisAutoCsb')->where($map)->find();
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
	 public function setPassword($returnType='json'){
	 	if(empty($_REQUEST['account'])  && empty($_REQUEST['pwd'])){
	 		$data=array();
	 		$code='1003';
	 		$msg='1003:非法访问';
	 		return $this->getReturnData($data,$returnType,$code,$msg);
	 	}
	 	//核验用户名与密码
	 	$map=array();
	 	$map['account']=$_REQUEST['account'];
	 	$data['pwd']=$_REQUEST['pwd'];
	 	$result=$this->CURD('MisAutoCsb','save',$data,$map);
	 	if($result){
	 		$data=$result;
	 		$code='1000';
	 		$msg='修改成功';
	 	}else{
	 		$data=array();
	 		$code='1006';
	 		$msg='1006:修改失败';
	 	}
	 	return $this->getReturnData($data,$returnType,$code,$msg);
	 }
	 
	public function resetPassword($returnType='json'){
		if(empty($_REQUEST['account']) && empty($_REQUEST['verifcode']) && empty($_REQUEST['pwd'])){
			$data=array();
			$code='1003';
			$msg='1003:非法访问';
			return $this->getReturnData($data,$returnType,$code,$msg);
		}
		//核验用户名与密码
		$map=array();
		$map['account']=$_REQUEST['account'];
		//$map['userid']=$_REQUEST['userid'];
		//忘记密码
		$map['verifcode']=$_REQUEST['verifcode'];
			$data['pwd']=$_REQUEST['pwd'];	
			$data['status']=1;
		 	$result=$this->CURD('MisAutoCsb','save',$data,$map);
		if($result){
			$data=$result;
			$code='1000';
			$msg='修改成功';
		}else{
			$data=array();
			$code='1006';
			$msg='1006:修改失败';
		}
		return $this->getReturnData($data,$returnType,$code,$msg);
	}
	
	public function getDriverInfo(){
		$modelname="MisAutoPca";
		//核验是否存在id
		$map=array();
		$map['userid']=$_REQUEST['userid'];
		$_REQUEST['id']=D($modelname)->where($map)->getField('id');
		$this->getFormInfo($modelname);	
	}
	//实名认证设置
	public  function setDriverInfo($returnType='json'){
		$modelname="MisAutoPca";
		//核验是否存在id
		$map=array();
		$map['userid']=$_REQUEST['userid'];
		$id=D($modelname)->where($map)->getField('id');
		//获取数据源
		$data['jiashiyuan']=$_REQUEST['jiashiyuan'];
		$data['shoujihaoma']=$_REQUEST['shoujihaoma'];
		$data['qitalianxidianhua']=$_REQUEST['qitalianxidianhua'];	
		$data['rzzt']=1;
		//下面是插入
 		//$data['upload20']=$_REQUEST['upload20'];
 		//$data['upload21']=$_REQUEST['upload21'];
 		//$data['upload23']=$_REQUEST['upload23'];		
 		$data['sfzzm']='sfzzm';
 		$data['sfzbm']='sfzbm';
 		$data['jsz']='jsz';	
		if($id){			
			$map['id']=$id;
			$result=$this->CURD($modelname,'save',$data,$map);
		}else{
			$data['userid']=$_REQUEST['userid'];
			//新增时要更新单号规则
			$scnModel=D("SystemConfigNumber");
			$ordernoInfo=$scnModel->getOrderno('mis_auto_bxook','MisAutoPca');
			//如果编码规则存在，强制转换，排除数据漫游设置；否则以数据漫游设置为准
			if($ordernoInfo['status']){
				$data['orderno']=$ordernoInfo['orderno'];
			}
			$result=$this->CURD($modelname,'add',$data);
		}
		if($result){
			$data=$result;
			$code='1000';
			$msg='1000:成功';
		}else{
			$data=array();
			$code='1001';
			$msg='1001:失败';
		}
		return $this->getReturnData($data,$returnType,$code,$msg);
	}

	public function getCarInfo(){
		$modelname="MisAutoQnl";
		//核验是否存在id
		$map=array();
		$map['userid']=$_REQUEST['userid'];
		$_REQUEST['id']=D($modelname)->where($map)->getField('id');
		$this->getFormInfo($modelname);
	}
	//实名认证设置
	public  function setCarInfo($returnType='json'){
		$modelname="MisAutoQnl";
		//核验是否存在id
		$map=array();
		$map['userid']=$_REQUEST['userid'];
		$id=D($modelname)->where($map)->getField('id');
		//获取数据源
		$data['cheliangpaizhao']=$_REQUEST['cheliangpaizhao'];
		$data['chexing']=$_REQUEST['chexing'];
		$data['chechang']=$_REQUEST['chechang'];
		$data['zaizhong']=$_REQUEST['zaizhong'];
		$data['rzzt']=1;
		//下面是插入
// 		$data['Upload8']=$_REQUEST['Upload8'];
// 		$data['Upload9']=$_REQUEST['Upload9'];
		$data['xsz']='zzjgdm';
		$data['yyz']='yyzz';
		$data['wgzp']='ygzp';
		if($id){			
			$map['id']=$id;
			$result=$this->CURD($modelname,'save',$data,$map);
		}else{
			$data['userid']=$_REQUEST['userid'];
			//新增时要更新单号规则
			$scnModel=D("SystemConfigNumber");
			$ordernoInfo=$scnModel->getOrderno('mis_auto_undcx','MisAutoQnl');
			//如果编码规则存在，强制转换，排除数据漫游设置；否则以数据漫游设置为准
			if($ordernoInfo['status']){
				$data['orderno']=$ordernoInfo['orderno'];
			}
			$result=$this->CURD($modelname,'add',$data);
		}
		if($result){
			$data=$result;
			$code='1000';
			$msg='1000:成功';
		}else{
			$data=array();
			$code='1001';
			$msg='1001:失败';
		}
		return $this->getReturnData($data,$returnType,$code,$msg);
	}
	
	
	
	public function getCompanyInfo(){
		$modelname="MisAutoBab";
		//核验是否存在id
		$map=array();
		$map['userid']=$_REQUEST['userid'];
		$_REQUEST['id']=D($modelname)->where($map)->getField('id');
		$this->getFormInfo($modelname);	
	}
	//实名认证设置
	public  function setCompanyInfo($returnType='json'){
	
		$modelname="MisAutoBab";
		//核验是否存在id
		$map=array();
		$map['userid']=$_REQUEST['userid'];
		$id=D($modelname)->where($map)->getField('id');
		//获取数据源
		$data['name']=$_REQUEST['name'];
		$data['lianxiren']=$_REQUEST['lianxiren'];
		$data['lianxidianhua']=$_REQUEST['lianxidianhua'];
		$data['dizhi']=$_REQUEST['dizhi'];
		$data['rzzt']=1;
		//下面是插入
// 		$data['upload7']=$_REQUEST['upload7'];
// 		$data['upload8']=$_REQUEST['upload8'];
		$data['zzjgdm']='zzjgdm';
		$data['yyzz']='yyzz';		
		if($id){			
			$map['id']=$id;
			$result=$this->CURD($modelname,'save',$data,$map);
		}else{
			$data['userid']=$_REQUEST['userid'];
			//新增时要更新单号规则
			$scnModel=D("SystemConfigNumber");
			$ordernoInfo=$scnModel->getOrderno('mis_auto_eihgb','MisAutoBab');
			//如果编码规则存在，强制转换，排除数据漫游设置；否则以数据漫游设置为准
			if($ordernoInfo['status']){
				$data['orderno']=$ordernoInfo['orderno'];
			}
			$result=$this->CURD($modelname,'add',$data);
		}
		if($result){
			$data=$result;
			$code='1000';
			$msg='1000:成功';
		}else{
			$data=array();
			$code='1001';
			$msg='1001:失败';
		}
		return $this->getReturnData($data,$returnType,$code,$msg);
	}
	//意见反馈设置
	public  function setFeedback($returnType='json'){
	
		$modelname="MisAutoGkm";
		//获取数据源
		$data['zhaiyao']=$_REQUEST['zhaiyao'];
		$data['miaoshu']=$_REQUEST['miaoshu'];
		//下面是插入
		// 		$data['upload7']=$_REQUEST['upload7'];
		// 		$data['upload8']=$_REQUEST['upload8'];
		$data['fujianyi']='fujianyi';
		$data['fujianer']='fujianer';
		//一直是新增
		$data['userid']=$_REQUEST['userid'];
		//新增时要更新单号规则
		$scnModel=D("SystemConfigNumber");
		$ordernoInfo=$scnModel->getOrderno('mis_auto_rvcpr','MisAutoGkm');
		//如果编码规则存在，强制转换，排除数据漫游设置；否则以数据漫游设置为准
		if($ordernoInfo['status']){
			$data['orderno']=$ordernoInfo['orderno'];
		}
		$result=$this->CURD($modelname,'add',$data);
		
		if($result){
			$data=$result;
			$code='1000';
			$msg='1000:成功';
		}else{
			$data=array();
			$code='1001';
			$msg='1001:失败';
		}
		return $this->getReturnData($data,$returnType,$code,$msg);
	}	
	
	public function uploadAttachment($returnType='json'){
		//做上传分流,1个人认证，2司机认证，3车辆认证,99意见反馈
		$type=$_REQUEST['type'];
		//上传时，对应数据库表字段
		$field=$_REQUEST['field'];
		//当前上传对应的表单ID值
		$id=$_REQUEST['id']?$_REQUEST['id']:0;
		//司机用户id
		$userid=$_REQUEST['userid'];
		//获取文件流所有内容
		$pImg=$_FILES['upname'];
		$extendpath = pathinfo($pImg['name']);
		//获取文件后缀名
		$suffix=$extendpath['extension'];
		//对象拼装
		$map=array();		
		switch($type){
			case 1:
				//个人认证
				$modelname='MisAutoPca';
				$tablename='mis_auto_bxook';
				if($id){
					$map['id']=$id;					
				}else{					
					$map['userid']=$userid;
				}				
				break;
			case 2:
				//运输商管理
				$modelname='MisAutoBab';
				$tablename='mis_auto_eihgb';
				if($id){
					$map['id']=$id;					
				}else{
					$map['userid']=$userid;					
				}
				break;
			case 3:
				//车辆管理
				$modelname='MisAutoQnl';
				$tablename='mis_auto_undcx';
				if($id){
					$map['id']=$id;					
				}else{					
					$map['userid']=$userid;
				}	
				break;
			case 99:
				//意见反馈
				$modelname='MisAutoGkm';
				$tablename='mis_auto_rvcpr';
				$map['id']=$id;
				break;
		}
		//核验是否存在表单id
		$tableid=D($modelname)->where($map)->getfield('id');
		//开启事务
		$this->transaction_model=M();
		$this->transaction_model->startTrans();
		//第一步：如果不存在，插入关联数据，并返回表单id值
		if(empty($tableid)){
			$data=array();
			$data['userid']=$_REQUEST['userid'];
			$data['rzzt']=0;
			//新增时要更新单号规则
			$scnModel=D("SystemConfigNumber");
			$ordernoInfo=$scnModel->getOrderno($tablename,$modelname);
			//如果编码规则存在，强制转换，排除数据漫游设置；否则以数据漫游设置为准
			if($ordernoInfo['status']){
				$data['orderno']=$ordernoInfo['orderno'];
			}
			$insertid=D($modelname)->add($data);
			$tableid = $insertid;
		}
		if($tableid){
			//第二步：添加附件文档
			//查询是否已经有上传附件，有则替换，无则新增
			$map=array();
			$map['tablename']=$modelname;
			$map['tableid']=$tableid;
			$map['fieldname']= $field;//字段名称
			$uploadid=D('MisAttachedRecord')->where($map)->getfield('id');
			if(empty($uploadid)){			
				//新增附件信息
				$data=array();
				$data['tablename'] = $modelname;
				$data['tableid']=$tableid;
				//确定路径
				$path=$modelname.'/'.date("Y").'/'.date("m").'/'.date("d").'/'.time().".".$suffix;//绝对路径
				$url=C("SERVER_ADDRESS")."/Public/Uploads/".$path;//服务器存储路径
				$pathinfo=UPLOAD_PATH.$path;//上传路径
				$data['attached']= $path;//路径
				$data['fieldname']= $field;//字段名称
				$data['upname']=$extendpath['basename'];//文件命名为上传文件名
				$data['createtime'] = time();
				$data['orderid'] = $_REQUEST['userid'];
				$insertid=D('MisAttachedRecord')->add($data);
				$uploadid=$insertid;
			}else{
				//修改附件信息
				$data=array();
				$data['tablename'] = $modelname;
				$data['tableid']=$tableid;
				//确定路径
				$path=$modelname.'/'.date("Y").'/'.date("m").'/'.date("d").'/'.time().".".$suffix;//绝对路径
				$url=C("SERVER_ADDRESS")."/Public/Uploads/".$path;//服务器存储路径
				$pathinfo=UPLOAD_PATH.$path;//上传路径
				$data['attached']= $path;//路径
				$data['fieldname']= $field;//字段名称
				$data['upname']=$extendpath['basename'];//文件命名为上传文件名
				$data['createtime'] = time();
				$data['orderid'] = $_REQUEST['userid'];
				$condition['id']=$uploadid;
				$insertid=D('MisAttachedRecord')->where($condition)->save($data);				
			}	
			if($insertid===false){
				$this->transaction_model->rollback();
				return $this->getReturnData($data,'json','1012',"数据关联失败");
			}else{
				$ymap['id']=$tableid;
				$ydata[$field]=1;
				D($modelname)->where($ymap)->save($ydata);
				
				
				//第三步：数据流存储
				$filepath = pathinfo($pathinfo);
				if( !file_exists($filepath['dirname']) ) $this->createFolders($filepath['dirname']); //判断目标文件夹是否存在
				$receiveFile = $pathinfo;
				
				$result = $this->receiveStreamFile($receiveFile);
				//开始插入数据
				if($result){
					$this->transaction_model->commit();
					$data=array();
					$data['id']=$uploadid;
					$data['tableid']=$tableid;
					$data['url']=$url;
					$code='1000';
					$msg='1000:成功';
				}else{
					$this->transaction_model->rollback();
					$data=array();
					$code='1001';
					$msg='1001:失败';
				}
				return $this->getReturnData($data,$returnType,$code,$msg);
			}
		}else{
			$this->transaction_model->rollback();
			return $this->getReturnData(array(),'json','1013',"数据关联失败");
		}
	}
	
	//upload
	/*
	 * 定制化需求，在这里面直接调用事务处理
	 */
	public function uploadAttachment11($returnType='json'){
		//做上传分流,1个人认证，2司机认证，3车辆认证
		$type=$_REQUEST['type'];
		$field=$_REQUEST['field'];
		$id=$_REQUEST['id']?$_REQUEST['id']:0;
		$upname=$_REQUEST['upname'];
		//$upname='张三的故事.jpg';
		$extendpath = pathinfo($upname);
		//获取文件后缀名
		$suffix=$extendpath['extension'];
		switch($type){
			case 1:
				//个人认证
				$modelname='MisAutoPca';
				$tablename='mis_auto_bxook';
				$tableid=$id;
				break;
			case 2:
				//运输商管理
				$modelname='MisAutoBab';
				$tablename='mis_auto_eihgb';
				$tableid=$id;
				break;
			case 3:
				//车辆管理
				$modelname='MisAutoQnl';
				$tablename='mis_auto_undcx';
				$tableid=$id;
				break;
		}
		//核验是否存在
		//核验用户名与密码
		$map=array();
		$map['id']=$id;
		$result=D($modelname)->where($map)->find();	
		//开启事务
		$this->transaction_model=M();
		$this->transaction_model->startTrans();
		//第一步：如果不存在，插入关联数据
		if(empty($result)){
			$data=array();
			$data['userid']=$_REQUEST['userid'];
			$insertid=D($modelname)->add($data);
			$tableid = $insertid;
		}
		if($tableid){
			//第二步：添加附件文档
			//保存附件信息
			$data=array();
			//$data['type']=$type;
			//$data['orderid']=$insertid;
			$data['tablename'] = $tablename;
			$data['tableid']=$tableid;
			//确定路径
			$path=$modelname.'/'.date("Y").'/'.date("m").'/'.date("d").'/'.time().".".$suffix;//绝对路径
			$pathinfo=UPLOAD_PATH.$path;
			$data['attached']= $path;//路径
			$data['fieldname']= $field;//字段名称
			$data['upname']=$extendpath['basename'];//文件命名为上传文件名
			$data['createtime'] = time();
			$data['orderid'] = $_REQUEST['userid'];
			$insertid=D('MisAttachedRecord')->add($data);
			
			if($insertid===false){
				$this->transaction_model->rollback();
				return $this->getReturnData($data,'json','1012',"数据关联失败");
			}
		}else{
			$this->transaction_model->rollback();
			return $this->getReturnData($data,'json','1013',"数据关联失败");
		}		
		//第三步：数据流存储
		$filepath = pathinfo($path);
		if( !file_exists($filepath['dirname']) ) $this->createFolders($filepath['dirname']); //判断目标文件夹是否存在
		$receiveFile = $pathinfo;
		
		print_r($receiveFile);
		
		
		$result = $this->receiveStreamFile($receiveFile);
		//开始插入数据
		if($result){
			$this->transaction_model->commit();
			$data=array();
			$data['id']=$insertid;
			$data['tableid']=$tableid;
			$data['url']=$pathinfo;
			$code='1000';
			$msg='1000:成功';
		}else{
			$this->transaction_model->rollback();
			$data=array();
			$code='1001';
			$msg='1001:失败';
		}
		return $this->getReturnData($data,$returnType,$code,$msg);
	}
	//添加定位设置信息
	public  function getMapTraceSeting($returnType='json'){
		if($_REQUEST['userid']=='17'){
			$data['istrace']=false;
			$data['time']=600;
			$code='1000';
			$msg='1000:有所连接';
			return $this->getReturnData($data,$returnType,$code,$msg);
		}
		//这里查询一共有多少张状态为运输中或已完成的运单
		$map=array();
		$map['userid']=$_REQUEST['userid'];
		$map['_string']='ordertype=2 or ordertype=3';
		$idArray=D('MisAutoQec')->where($map)->getField('id');
		if($idArray){
			$data=array();
			$data['istrace']=true;
			$data['time']=600;
			$code='1000';
			$msg='1000:成功';			
			
		}else{
			$data['istrace']=false;
			$data['time']=600;
			$code='1000';
			$msg='1000:没运输中单据';
		}
		return $this->getReturnData($data,$returnType,$code,$msg);
	}	
	
	//添加路径定位记录
	public  function setMapTrace($returnType='json'){
		if($_REQUEST['userid']=='17'){
			$data['istrace']=false;
			$data['time']=600;
			$code='1000';
			$msg='1000:有所连接';
			return $this->getReturnData($data,$returnType,$code,$msg);
		}		
		//定义返回数据库
		$traceTime=array('time'=>600);
		//这里查询一共有多少张状态为运输中或已完成的运单
		$map=array();
		$map['userid']=$_REQUEST['userid'];
		$map['_string']='ordertype=2 or ordertype=3';
		$idArray=D('MisAutoQec')->where($map)->getField('id',true);	
		//echo 	D('MisAutoQec')->getLastSql();
		if($idArray){
			//$sql="";
		    foreach($idArray as $key => $val){
			
// 			$data['account']=$_REQUEST['account'];
// 			$data['latitude']=$_REQUEST['latitude'];
// 			$data['longitude']=$_REQUEST['longitude'];			
// 			$data['nation']=$_REQUEST['nation'];
// 			$data['province']=$_REQUEST['province'];
// 			$data['city']=$_REQUEST['city'];
// 			$data['district']=$_REQUEST['district'];
// 			$data['street']=$_REQUEST['street'];
// 			$data['streetNo']=$_REQUEST['streetNo'];
// 			$data['town']=$_REQUEST['town'];
// 			$data['village']=$_REQUEST['village'];			
// 			$data['orderno']=$val;
// 			$data['createtime']=time();
			
			

			$sql=" INSERT INTO mis_system_map_trace (`account`,`latitude`,`longitude`,`nation`,`province`,`city`,`district`,`street`,`streetNo`,`town`,`village`,`orderno`,`createtime`) VALUES ('".
							  $_REQUEST['account']."','".
							  $_REQUEST['latitude']."','".
							  $_REQUEST['longitude']."','".
							  $_REQUEST['nation']."','".
							  $_REQUEST['province']."','".
							  $_REQUEST['city']."','".
					          $_REQUEST['district']."','".
					          $_REQUEST['street']."','".
					          $_REQUEST['streetNo']."','".
					          $_REQUEST['town']."','".
					          $_REQUEST['village']."','".
					          $val."','".
					          time()."')";
				$result=$this->CURD("MisSystemMapTrace",'execute',$sql);
                if($result===false){
                	break;
                }
		    }
		  //  $sql1=mysql_real_escape_string($sql);
		    //echo $sql;
		    //$result=$this->CURD("MisSystemMapTrace",'add',$data);
		    if($result){
		    	$data['istrace']=true;
		    	$data['time']=$traceTime;
		    	$code='1000';
		    	$msg='1000:成功';
		    }else{
		    	$data['istrace']=true;
		    	$data['time']=$traceTime;
		    	$code='1000';
		    	$msg='1000:记录失败';
		    }
		}else{
			$data['istrace']=true;
		    $data['time']=$traceTime;
			$code='1000';
			$msg='1000:没运输中单据';
		}
		return $this->getReturnData($data,$returnType,$code,$msg);
	}
	
	//参数传当前单据id
	public function setYundanStart(){		
		$this->setYundanStatus(1);
	}
	
	//参数传当前单据id
	public function setYundanDoing(){
		$this->setYundanStatus(2);
	}
	
	//参数传当前单据id
	public function setYundanFinish(){
		$this->setYundanStatus(3);
	}
	
	private  function setYundanStatus($ordertype,$returnType='json'){
		//如果存在，则将状态改为1
		$map['id']=$_REQUEST['id'];
		$data['ordertype']=$ordertype;
		$result=$this->CURD('MisAutoQec','save',$data,$map);
		if($result){
			$data=array();
			$code='1000';
			$msg='1000:成功';
		}else{
			$data=array();
			$code='1001';
			$msg='1001:失败';
		}
		return $this->getReturnData($data,$returnType,$code,$msg);
	}
	//搜索热门关键词
	public  function getSearchHot($returnType='json'){
		$data=array('全国','重庆','北京','上海','大卡车','普卡','大货车','宁波','无锡','杭州','二桥','板车','三桥','发动机','整车','轮胎','普货','武汉','南京');
		return $this->getReturnData($data,'json','1000',"获取数据成功");
	}
	
	
	//添加搜索历史记录
	public  function setSearchLog(){
		$logData=json_decode($_REQUEST['searchlog'],true);
		//M('mis_auto_ormeo_sub_guanzhusousuo')	
		//查询历史记录里最高的一个
		$map=array();
		$map['userid']=$_REQUEST['userid'];
		$lasttime=D("MisAutoOrmeoSubGuanzhusousuo")->where($map)->order('id desc')->getField('createtime');
		foreach($logData as $key =>$val){
			$data['userid']=$_REQUEST['userid'];
			$data['keyword']=$val['keywork'];
			$data['createtime']=$val['date'];
			if($lasttime>$val['date']){ continue;}
			$result=$this->CURD('MisAutoOrmeoSubGuanzhusousuo','add',$data);
		}
		if ($result){
			return $this->getReturnData($data,'json','1000',"获取数据成功");
		}else{
			//return $this->getReturnData($data,'json','1000',"获取数据失败");
			
		}
	}	
	
	//用户基本信息
	public  function getUserInfo($returnType='json'){
		$map=array();
		$map['mis_auto_mnkrf.id']=$_REQUEST['userid'];
		$result=$this->getMisSystemDataview('fpappsjView',$map,$returnType);
		if($returnType='arr'){
			return $result;
		}
	}

	
	public function yxtest(){
		$a=$this->getUserInfo('json');	
		print_r($a);	
	}
	
	public function getHuoyuanList(){
		$map=array();
		$map['huowushuliang']=array('gt',0);
		$map['ordertype']=array('eq',2);
		$fields='';
		if($_REQUEST['keyword']=='全国'){
			$fields='';
		}else{
			$fields='orderno,appshifadian,appmudedi,shifadian,mudedi,huowumingcheng';
		}
		$this->getFormList('MisAutoTie','id,orderno,appshifadian,appmudedi,chufashijian,daodashijian,huowumingcheng,huowushuliang,huowudanwei,huowuxiangqing',$fields,$map);
	}
	
	public function getHuoyuanDetail(){
		$this->getFormInfo('MisAutoTie');
	}
	
	public function getYundanList(){
		$map=array();
		if($_REQUEST['ordertype']==3){
			$map['ordertype']=array('egt',3);
		}else{
			$map['ordertype']=$_REQUEST['ordertype'];
		}
		$map['userid']=$_REQUEST['userid'];
		$this->getFormList('MisAutoQec','id,orderno,appshifadian,appmudedi,chufashijian,daodashijian,huoyuandanhao,huowumingcheng,huowushuliang,huowudanwei','orderno,appshifadian,appmudedi,huowumingcheng',$map);
	}
	
	public function getYundanDetail(){
		$this->getFormInfo('MisAutoQec');
	}	
	/*
	 * 该接口是定制化开发
	 */
	public function setYundan($returnType='json'){

		//第一步获取货源单相关信息
		$map=array();
		$map['id']=$_REQUEST['id'];
		$huoyuanInfo=D("MisAutoTie")->where($map)->find();
		if($huoyuanInfo){
			$data=array();
			//第二步生成运单
			$map=array();
			$map['huoyuandanhao']=$huoyuanInfo['id'];
			$map['userid']=$_REQUEST['userid'];
			$map['ordertype']=array('lt',3);
			$yundanInfo=D("MisAutoQec")->where($map)->find();
			//首先防攻击，多少秒内不能重复提交
			if($yundanInfo){
				return $this->getReturnData($yundanInfo,'json','1008',"已经接单，请勿重复接单！");
			}
			//新增时要更新单号规则
			$scnModel=D("SystemConfigNumber");
			$ordernoInfo=$scnModel->getOrderno('mis_auto_jvvzx','MisAutoQec');
			//如果编码规则存在，强制转换，排除数据漫游设置；否则以数据漫游设置为准
			if($ordernoInfo['status']){
				$data['orderno']=$ordernoInfo['orderno'];
			}
			$data['huoyuandanhao']=$huoyuanInfo['id'];
			$data['appshifadian']=$huoyuanInfo['appshifadian'];
			$data['appmudedi']=$huoyuanInfo['appmudedi'];
			$data['shifadian']=$huoyuanInfo['shifadian'];
			$data['mudedi']=$huoyuanInfo['mudedi'];
			$data['chufashijian']=$huoyuanInfo['chufashijian'];
			$data['daodashijian']=$huoyuanInfo['daodashijian'];
			$data['shouhuoren']=$huoyuanInfo['shouhuoren'];
			$data['lianxidianhua']=$huoyuanInfo['lianxidianhua'];	
			$data['huowumingcheng']=$huoyuanInfo['huowumingcheng'];
			$data['huowushuliang']=0;
			$data['huowudanwei']=$huoyuanInfo['huowudanwei'];
			$data['beizhu']=$huoyuanInfo['huowuxiangqing'];
			$data['chechang']=$huoyuanInfo['chechang'];
			$data['zaizhong']=$huoyuanInfo['zaizhong'];
			$data['chexing']=$huoyuanInfo['chexing'];
			$data['huowuzhonglei']=$huoyuanInfo['huowuzhonglei'];
			$data['ordertype']=1;

			//关联信息
			$data['userid']=$_REQUEST['userid'];
			//关联表单-首先查询用户视图
			$userInfo=$this->getUserInfo('arr');
			//1、司机信息
			$data['jiashiyuan']=$userInfo['list']['sjid'];			
			//2、运输商信息
			$data['yunshugongsi']=$userInfo['list']['yssid'];		
			//3、车辆信息
			$data['cheliangbianhao']=$userInfo['list']['clid'];					
			$data['createtime']=time();
			$result=$this->CURD('MisAutoQec','add',$data);
			if($result){
				$data=$result;
				$code='1000';
				$msg='1000:成功';
			}else{
				$data=array();
				$code='1001';
				$msg='1001:失败';
			}
			return $this->getReturnData($data,$returnType,$code,$msg);
		}else{
			$data=array();
			$code='1003';
			$msg='1003:非法访问';
			return $this->getReturnData($data,$returnType,$code,$msg);			
		}
	}
	
	public function getCarType(){
	
		$data=$this->getBasicData('cx');
		return $this->getReturnData($data,'json','1000',"获取数据成功");
	}
	
	public function getAboutUs(){	
		$data="http://120.25.152.7/phonenew/view/about_us.html";
		return $this->getReturnData($data,'json','1000',"获取数据成功");
	}

	public function getHelp(){
		$data="http://www.fpwuliu.com/help.html";
		return $this->getReturnData($data,'json','1000',"获取数据成功");
	}
	
	public function getShare($returnType='json'){
		$channel=$_REQUEST['channel']?$_REQUEST['channel']:0;
		if($channel){
			switch($channel){
				case 'h5':
					$QRCODE="";//二维码
					$downloadUrl="";
					$title="物流e+";
					$content="风平物流，想运就运";
					break;
				case 'ios':
					$QRCODE="http://120.25.152.7/soft/ios.png";//二维码
					$downloadUrl="https://fir.im/fpwl";
					$title="物流e+";
					$content="风平物流，想运就运";
					break;
				case 'ad':
					$QRCODE="http://120.25.152.7/soft/android.png";//二维码
					$downloadUrl="http://120.25.152.7/soft/Logistics.apk";
					$title="物流e+";
					$content="风平物流，想运就运";
					break;
			}
			return $this->getShareBase($channel,$QRCODE,$downloadUrl,$title,$content);
		}else{
			$data=array();
			$code='1003';
			$msg='1003:非法操作';
			return $this->getReturnData($data,$returnType,$code,$msg);		
		}
	}	
	public function getVersion($returnType='json'){
		$channel=$_REQUEST['channel']?$_REQUEST['channel']:0;
		$versions=$_REQUEST['version']?$_REQUEST['version']:0;
		if($channel){
			switch($channel){
				case 'h5':
					$version="1.0";//版本号
					$build="1";//更新构建号
					$downloadUrl="";
					if($versions>=$build){
						$data=array();
						$code='2000';
						$msg='2000:版本已是最新';
						return $this->getReturnData($data,$returnType,$code,$msg);
					}
					break;
				case 'ios':
					$version="1.0.9";//版本号
					$build="12";//更新构建号
					$downloadUrl="itms-services://?action=download-manifest&url=https%3A%2F%2Ffir.im%2Fapi%2Fv2%2Fapp%2Finstall%2F55b829b9692d394986000012%3Ftoken%3D156cc640129711e5a8f927bf3e20d7bb439e8367";
					if($versions>=$build){
						$data=array();
						$code='2000';
						$msg='2000:版本已是最新';
						return $this->getReturnData($data,$returnType,$code,$msg);
					}
					break;
				case 'ad':
					$version="1.1.6";//版本号
					$build="8";//更新构建号
					$downloadUrl="http://120.25.152.7/soft/Logistics.apk";
					if($versions>=$build){
						$data=array();
						$code='2000';
						$msg='2000:版本已是最新';
						return $this->getReturnData($data,$returnType,$code,$msg);
					}
					break;
			}
			return $this->getVersionBase($channel,$version,$build,$downloadUrl);
		}else{
			$data=array();
			$code='1003';
			$msg='1003:非法操作';
			return $this->getReturnData($data,$returnType,$code,$msg);
		}		
	}
	
	/*
	 * 外部用户注册registerExtend
	*/
	
	public function registerExtend(){
		$username=$_REQUEST['username'];
		$pwd=$_REQUEST['pwd'];
		if(!empty($username) && !empty($pwd)){
			//新增
			$userData['account']=$username;
			$userData['status']=1;
			$userlist=$this->CURD('User','select','',$userData);
			if(!empty($userlist)){
				header("Content-type:text/html;charset=utf-8");
				echo '<h1>此邮箱已经注册，3秒后自动跳转</h1><script>setTimeout(function(){window.location.href="http://www.fpwuliu.com/register.html"},3000);</script>';
					
			}else{
				//用户名截取转asicc加密
// 				$userStr=str_split($username,1);
// 				$useArr='';
// 				foreach ($userStr as $k=>$v){
// 					$useArr.=str_pad(ord($v),4,"0",STR_PAD_LEFT);
// 				}
				$useArr=bin2hex($username);
				$arr=array($useArr,sha1($username));
				$list=base64_encode(serialize($arr));
				
				//查询用户是否激活  未激活修改  不存在新增
				$userData['account']=$username;
				$userData['status']=0;
				$uList=$this->CURD('User','select','',$userData);
				if(empty($uList)){
					//保存 后台用户
					$this->lookupInsertUser($username, $username, $username, $pwd, 134,true,false,1,0);
						
				}else{
					//保存 后台用户
					$this->lookupInsertUser($username, $username, $username, $pwd, 134,true,false,2,0);
						
				}
				//邮件信息
				$attachments=array($username);
				$subject="恭喜您注册成功【风平物流电商平台】";
				$checkUrl='http://manage.fpwuliu.com/Admin/index.php?s=/MobileFengping/checkUserExtend/username/'.$list;
				$body="您注册的用户名：".$username.";密码：".$pwd.";请单击链接完成验证：".$checkUrl;
				$result=$this->SendEmail($subject, $body, $attachments);
				//跳转页面
				if($result){
					header("Content-type:text/html;charset=utf-8");
					echo '<h1>注册成功，用户名密码已发送您邮箱，请激活</h1><script>setTimeout(function(){window.location.href="'.U('Public/login').'"},3000);</script>';
				}else{
					header("Content-type:text/html;charset=utf-8");
					echo '<h1>邮件发送失败，3秒后自动跳转</h1><script>setTimeout(function(){window.location.href="'.U('Public/login').'"},3000);</script>';
				}
				//redirect (U('Public/login'));
				//echo json_encode($result);
			}
		}
	}
	
	/*
	 * 外部用户验证 checkUserExtend
	*/

	public function checkUserExtend(){
		$username=$_REQUEST['username'];
		$userArr=unserialize(base64_decode($username));
		$email=$userArr[0];
		$miemail=$userArr[1];
// 		$aemail=str_split($email,4);
// 		foreach ($aemail as $k1=>$v2){
// 			$name.=chr(intval($v2));
// 		}
		$name=pack("H*",$email);
		if(sha1($name)==$miemail){
			$uData['status']=1;
			$map['account']=$name;
			$list=D();
			$userlist=$this->CURD('User','save',$uData,$map);
			if($userlist!==false){
				header("Content-type:text/html;charset=utf-8");
				echo '<h1>邮箱激活成功，3秒后自动跳转，或点击<a href="'.U('Public/login').'">登录</a></h1><script>setTimeout(function(){window.location.href="http://manage.fpwuliu.com/Admin"},3000);</script>';
					
			}else{
				header("Content-type:text/html;charset=utf-8");
				echo '<h1>邮箱激活失败，3秒后自动跳转</h1><script>setTimeout(function(){window.location.href="http://manage.fpwuliu.com/Admin"},3000);</script>';
				
			}
			
		}else{
			header("Content-type:text/html;charset=utf-8");
			echo '<h1>链接错误，3秒后自动跳转</h1><script>setTimeout(function(){window.location.href="http://www.fpwuliu.com/Admin"),3000);</script>';
			
		}
	
	}
	/**
	 * getVerifyCode获取验证码
	 */
	
	function getVerifyCode(){
		$username=$_GET['username'];
		$pwd=$_GET['pwd'];
		if(!empty($username) && !empty($pwd)){
			//验证码
			$randStr = str_shuffle('abcdefghijklmnopqrstuvwxyz1234567890');
			$rand = substr($randStr,0,5);
			//新增
			$userData['account']=$username;
			$userData['status']=1;
			$userlist=$this->CURD('User','select','',$userData);
			//查询当前时间与上次保存时间是否相差3分钟 3分钟之内不发送邮件
			$time=$userlist['updatetime']-time();
			if(!empty($userlist)){
				echo $_GET['jsoncallback'] . "(".json_encode('此邮箱已经注册').")";	
			}else{
				if(!empty($userlist['updatetime'])&& $time<60*3){
				//	log($time,'xyq222');
					echo $_GET['jsoncallback'] . "(".json_encode('请3分钟后重试').")";
				}else{
					//查询用户是否激活  未激活修改  不存在新增
					$userData1['account']=$username;
					$userData1['status']=0;
					$uList=$this->CURD('User','select','',$userData1);
					//log($uList,'xyq111');
					if(empty($uList)){
						//新增 后台用户
						$this->lookupInsertUser($username, $username, $username, $pwd, 134,true,false,1,0,$rand);
							
					}else{
						//修改 后台用户
						$this->lookupInsertUser($username, $username, $username, $pwd, 134,true,false,2,0,$rand);
							
					}
					//邮件信息
					$attachments=array($username);
					$subject="您正在注册【风平物流电商平台】";
					$body='<p>尊敬的用户，您好：</p><p><br/></p><p style="margin-top: 5px; text-indent: 2em;">您用该邮箱申请的注册验证码是：'.$rand.'，如果非本人操作,请忽略。</p><p style="text-indent: 2em;">感谢您的关注与厚爱。</p><p style="text-align: right;">&nbsp;风平物流运营团队</p><p style="text-align: right;">'.date('Y年m月d日',time()).'<br/></p>';
					
					//$body="尊敬的".$username."，您好：您注册的邮件验证码是".$rand."，如果非本人操作,请忽略。";
					$result=$this->SendEmail($subject, $body, $attachments);
					//跳转页面
					if($result){
						//echo $_GET['jsoncallback'] . "(".json_encode('邮件已发送').")";
						//echo json_encode('邮件已发送');
					}else{
						echo $_GET['jsoncallback'] . "(".json_encode('邮件发送失败').")";
							
						//echo json_encode('邮件发送失败');
					}
			//redirect (U('Public/login'));
			//echo json_encode($result);
				}
			}
			
		}
	}
	
	function test(){
		
		
	}
	
	/*
	 * 外部用户验证码注册registerExtendOne
	*/
	
	public function registerExtendOne(){
		$username=$_REQUEST['username'];
		$pwd=$_REQUEST['pwd'];
		$verify=$_REQUEST['verifyw'];
		if(!empty($username) && !empty($pwd)){
			//新增
			$userData['account']=$username;
			$userData['status']=1;
			$userlist=$this->CURD('User','select','',$userData);
			if(!empty($userlist)){
				header("Content-type:text/html;charset=utf-8");
				echo '<h1>此邮箱已经注册，3秒后自动跳转</h1><script>setTimeout(function(){window.location.href="http://www.fpwuliu.com/register.html"},3000);</script>';
					
			}else{
				$useArr=bin2hex($username);
				$arr=array($useArr,sha1($username));
				$list=base64_encode(serialize($arr));
	
				//查询用户是否激活  未激活修改  不存在新增
				$userMap['account']=$username;
				$userData1['status']=1;
				$userMap['status']=0;
 				$uList=$this->CURD('User','select','',$userMap);
				//不存在新增
				
					//存在激活
				if($uList['verify']==$verify){
					//保存 后台用户
					//$listInfo=D('User')->where($userMap)->save($userData1);
					$listInfo=$this->CURD('User','save',$userData1,$userMap);
					//$strsql=D('User')->getLastSql();
					//跳转页面
					if($listInfo!=false){
						header("Content-type:text/html;charset=utf-8");
						echo '<h1>注册成功，3秒后自动跳转</h1><script>setTimeout(function(){window.location.href="'.U('Public/login').'"},3000);</script>';
					}else{
						header("Content-type:text/html;charset=utf-8");
						echo '<h1>注册失败，3秒后自动跳转,</h1><script>setTimeout(function(){window.location.href="http://www.fpwuliu.com/register.html"},3000);</script>';
					}
				}else{
					header("Content-type:text/html;charset=utf-8");
					echo '<h1>验证码不正确，3秒后自动跳转</h1><script>setTimeout(function(){window.location.href="http://www.fpwuliu.com/register.html"},3000);</script>';
				
				}
				
			}
		}
	} 
	
	/**
	 * checkAcount输入邮箱时验证用户是否注册
	 */
	function checkAcount(){
		$username=$_GET['username'];
		if(!empty($username)){
			$userData['account']=$username;
			$userData['status']=1;
			$userlist=$this->CURD('User','select','',$userData);
			if(!empty($userlist)){
				echo $_GET['jsoncallback'] . "(".json_encode('此邮箱已经注册').")";	
			}else{
				echo $_GET['jsoncallback'] . "(".json_encode(1).")";	
			}
		}
	}
	/**
	checkVerify 验证码校验
	*/
	function checkVerify(){
		$username=$_GET['username'];
		$verifyw=$_GET['verifyw'];
		if(!empty($username)){
			$userData['account']=$username;
			$userData['status']=0;
			$userlist=$this->CURD('User','select','',$userData);
			if($userlist['verify']!=$verifyw){
				echo $_GET['jsoncallback'] . "(".json_encode('验证码不正确').")";	
			}else{
				echo $_GET['jsoncallback'] . "(".json_encode(1).")";	
			}
		}
	}
	/**
	 * findPassword找回密码
	 */
	public function findPassword(){
		// 检查传入的用户名存在性
		$obj = M('user');
		$username=$_REQUEST['username'];
		$map['account'] = $username;
		$count = $obj->where($map)->count();
		if($count != 1){
			header("Content-type:text/html;charset=utf-8");
			echo '<h1>没有找到该用户 请注册,3秒后自动跳转</h1><script>setTimeout(function(){window.location.href="http://www.fpwuliu.com/register.html"},3000);</script>';
			
		}else{

			// 取得4位随机数
			$randChar =md5( rand_string(4) );
			// 找回用户名
			$username=$_REQUEST['username'];
			// 加密结果
			$retStr =str_replace('=', '' , base64_encode($randChar.'|'.$username) );
			// 有效时间， 暂时不做
			
			// 检查数据
			$checkMap['username'] = $username;
			$recodeObj = M('mis_system_forgetpwd');
			$recodeObj->where($checkMap)->delete();
			
			unset($recodeData);
			$recodeData['createtime'] = time();
			$recodeData['username'] = $username;
			$recodeData['key'] = $randChar;
			$recodeData['keys'] = $retStr;
			
			$recodeObj->data($recodeData)->add();
			
			$attachments=array($username);
			$subject="您正在找回密码【风平物流电商平台】";
			$checkUrl='http://www.fpwuliu.com/pwdreset.php?key='.$retStr;
			$body="请单击链接修改密码：".$checkUrl;
			$result=$this->SendEmail($subject, $body, $attachments);
			//跳转页面
			if($result){
				header("Content-type:text/html;charset=utf-8");
				echo '<h1>请在邮箱点击链接修改密码,3秒后自动跳转</h1><script>setTimeout(function(){window.location.href="'.U('Public/login').'"},3000);</script>';
					
				//echo '请在邮箱点击链接修改密码';
			}else{
				header("Content-type:text/html;charset=utf-8");
				echo '<h1>邮件发送失败,3秒后自动跳转</h1><script>setTimeout(function(){window.location.href="'.U('Public/login').'"},3000);</script>';
					
			}
		}
		
		
		//redirect (U('Public/login'));
		
	}
	
	/**
	 * passwordReset重置密码
	 */
	public function passwordReset(){
		$username=$_REQUEST['username'];
		$password=md5($_REQUEST['password']);
		$uData['password']=$password;
		$map['account']=$username;
		$userlist=$this->CURD('User','save',$uData,$map);
		if($userlist==false){
			$data=array();
			$code='1001';
			$msg='1001:密码修改失败';
			return $this->getReturnData($data,$returnType,$code,$msg);
		
		}else{
			$data=$userlist;
			$code='1000';
			$msg='密码修改成功';
		}
		if($userlist!=false){
			header("Content-type:text/html;charset=utf-8");
			echo '<h1>密码修改成功，3秒后自动跳转，或点击<a href="'.U('Public/login').'">登录</a></h1><script>setTimeout(function(){window.location.href="'.U('Public/login').'"},3000);</script>';
				
			//echo '密码修改成功';
		}else{
			header("Content-type:text/html;charset=utf-8");
			echo '<h1>密码修改失败，3秒后自动跳转</h1><script>setTimeout(function(){window.location.href="'.U('Public/login').'"},3000);</script>';
				
		}
		//echo json_encode($userlist);
		redirect (U('Public/login'));
	}
	/**
	 * lookupInsertUser  添加特殊用户
	 * @param 权限组:  $roleid    $isCreatePassword:是否生成密码 （true是 false否）
	 * 		$createDefaultPassword 是否生成默认密码  当没有密码的时候生成123456默认密码
	 * 		$type   1新增 2修改 3删除
	 *
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
// 			$this->error('货主保存失败');
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