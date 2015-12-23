<?php
/**
 * @Title: MobileFengpingAction
 * @Package 重庆创客荟接口类
 * @Description: 请求类，取数据接口
 * @author eagle
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d˾
 * @copyright Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d˾
 * @date 2014-2-08
 * @version V1.0
 */
class MobileChuangkeAction extends MobileBaseAction {
	//保存类实例的静态成员变量	
	private $userid=NULL;//当前登录用户ID
	private $username=NULL;//当前登录用户账号
	private $token=NULL;//当前令牌
	private $transaction_model=NULL;//事务模型
    private $notoken=array('register','login','getHangyeList','getGongnengList','getPriceList','getFangshi','getVerificationCode','getSpPriceList','getSpGongnengList','getSpProfessionList','checkVerificationCode','getHuoyuanList','getHuoyuanDetail','resetPassword','getSearchHot','getCarType','yxtest','smsTest','getShare','getVersion','serverLink','registerExtend','checkUserExtend','findPassword','passwordReset','lookupInsertUser','getHelp','getVerifyCode','registerExtendOne','checkAcount','checkVerify','getZixunList','getZixunDetail','getHuodongList','getHuodongDetail','getXiangmuList','getXiangmuDetail','getDaoshiDetail','getDaoshiList','getFuwuList','getFuwuDetail','getZhenceList','getZhenceDetail','getShangchengList','getShangchengDetail','getKongjianList','getKongjianDetail','getQuyuList','getProfessionList','getFormListAll'); 
	
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
	
	public function getShare($returnType='json'){
		$channel=$_REQUEST['channel']?$_REQUEST['channel']:0;
		if($channel){
			switch($channel){
				case 'h5':
					$QRCODE="";//二维码
					$downloadUrl="";
					$title="创客荟";
					$content="风平物流，想运就运";
					break;
				case 'ios':
					$QRCODE="http://120.25.152.7/soft/ios.png";//二维码
					$downloadUrl="https://fir.im/fpwl";
					$title="创客荟";
					$content="风平物流，想运就运";
					break;
				case 'ad':
					$QRCODE="http://120.25.152.7/soft/android.png";//二维码
					$downloadUrl="http://120.25.152.7/soft/Logistics.apk";
					$title="创客荟";
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
				$subject="恭喜您注册成功【重庆创客荟平台】";
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
					$subject="您正在注册【重庆创客荟平台】";
					$body='<p>尊敬的用户，您好：</p><p><br/></p><p style="margin-top: 5px; text-indent: 2em;">您用该邮箱申请的注册验证码是：'.$rand.'，如果非本人操作,请忽略。</p><p style="text-indent: 2em;">感谢您的关注与厚爱。</p><p style="text-align: right;">&nbsp;重庆创客荟</p><p style="text-align: right;">'.date('Y年m月d日',time()).'<br/></p>';
					
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
			$subject="您正在找回密码【重庆创客荟平台】";
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
	
	
	/**
	 * (non-PHPdoc)创客资讯
	 * @see MobileBaseAction::getZixunList()
	 */
	public function getZixunList(){
		$this->getFormList('MisAutoJwx','id,name,narong','name,narong');
	}
	public function getZixunDetail(){
		$Detailmap['id']=$_REQUEST['id'];
		$Detaillist=$this->CURD('MisAutoJwx','select',null,$Detailmap);
		$Detaildata['yueduliang']=$Detaillist['yueduliang']+1;
		$listInfo=$this->CURD('MisAutoJwx','save',$Detaildata,$Detailmap);
		$this->getFormInfo('MisAutoJwx');
	}
	
	/**
	 * getHuodongList 创客活动
	 */
	public function getHuodongList(){
		$this->getFormList('MisAutoRji','id,name,naronganpai','name,naronganpai');
	}
	public function getHuodongDetail(){
		$Detailmap['id']=$_REQUEST['id'];
		$Detaillist=$this->CURD('MisAutoRji','select',null,$Detailmap);
		$Detaildata['yueduliang']=$Detaillist['yueduliang']+1;
		$listInfo=$this->CURD('MisAutoRji','save',$Detaildata,$Detailmap);
		$this->getFormInfo('MisAutoRji');
		
	}
	
	/**
	 * getXiangmuList  创客项目
	 */
	public function getXiangmuList(){
		$this->getFormList('MisAutoDni','id,name,biaoqian,jieshao','name,jieshao,biaoqian');
	}
	public function getXiangmuDetail(){
		$Detailmap['id']=$_REQUEST['id'];
		$Detaillist=$this->CURD('MisAutoDni','select',null,$Detailmap);
		$Detaildata['yueduliang']=$Detaillist['yueduliang']+1;
		$listInfo=$this->CURD('MisAutoDni','save',$Detaildata,$Detailmap);
		$this->getFormInfo('MisAutoDni');
	}
	
	/**
	 * getDaoshiList 创客导师
	 */
	public function getDaoshiList(){
		$this->getFormList('MisAutoWtv','leixing','leixing,suoshuxingye');
	}
	public function getDaoshiDetail(){
		$this->getFormInfo('MisAutoWtv');
	}
	public function getProfessionList(){
		$this->getFormListAll('MisAutoCwh','id,name','id');
	}
	
	/**
	 * getFuwuList创客服务
	 */
	public function getFuwuList(){
		$this->getFormList('MisAutoCxj','id,name','name');
	}
	public function getFuwuDetail(){
		$this->getFormInfo('MisAutoCxj');
	}
	
	/**
	 * getZhenceList政策资讯
	 */
	public function getZhenceList(){
		$this->getFormList('MisAutoBex','id,name,narong,createtime','name,narong');
	}
	public function getZhenceDetail(){
		//添加阅读量
		$Detailmap['id']=$_REQUEST['id'];
		$Detaillist=$this->CURD('MisAutoBex','select',null,$Detailmap);
		$Detaildata['yueduliang']=$Detaillist['yueduliang']+1;
		$listInfo=$this->CURD('MisAutoBex','save',$Detaildata,$Detailmap);
		$this->getFormInfo('MisAutoBex');
	}
	
	/**
	 * getShangchengList创客商城
	 */
	public function getShangchengList(){
		$this->getFormList('MisAutoLjc','id,name,jiage,zhuangtai','name,jiage,zhuangtai');
	}
	public function getShangchengDetail(){
		$this->getFormInfo('MisAutoLjc');
	}
	//商城行业档案
	public function getSpProfessionList(){
		$xingyeList=D('MisAutoVqy')->select();
		$gongnengList=D('MisAutoBgs')->select();
		$priceList=D('MisAutoNnq')->select();
		$data['xingye']=$xingyeList;
		$data['gongneng']=$gongnengList;
		$data['price']=$priceList;
		return $this->getReturnData($data,'json','1000',"获取数据成功");
		//$this->getFormListAll('MisAutoVqy','id,name','id');
	}
	/*
	 * 查询行业数据
	 */
	public function getHangyeList(){
		$xingyeList=D('MisAutoVqy')->field("id,name")->select();
		return $this->getReturnData($xingyeList,'json','1000',"获取数据成功");
	}
	/*
	 * 查询功能数据
	 */
	public function getGongnengList(){
		$gongnengList=D('MisAutoBgs')->field("id,name")->select();
		return $this->getReturnData($gongnengList,'json','1000',"获取数据成功");
	}
	/*
	 * 查询价格数据
	 */
	public function getPriceList(){
		$priceList=D('MisAutoNnq')->field("id,name")->select();
		return $this->getReturnData($priceList,'json','1000',"获取数据成功");
	}
	/*
	 * 查询方式
	 */
	public function getFangshi(){
		$select_config = require DConfig_PATH."/System/selectlist.inc.php";
		$fangshilist = $select_config["zuOrshou"]["zuOrshou"];
		return $this->getReturnData($fangshilist,'json','1000',"获取数据成功");
	}
	
	//商城功能档案
	public function getSpGongnengList(){
		$this->getFormListAll('MisAutoBgs','id,name','id');
	}
	//商品价格区间
	public function getSpPriceList(){
		$this->getFormListAll('MisAutoNnq','id,name','id');
	}
	
	
	/**
	 * getKongjianList创客空间
	 */
	public function getKongjianList(){
		$this->getFormList('MisAutoQds','id,name,zujin,mianji,suoshudiqu,beizhu,dizhi','name,zujin,mianji,suoshudiqu,beizhu,dizhi');
	}
	public function getKongjianDetail(){
		$this->getFormInfo('MisAutoQds');
	}
	//区域列表
	public function getQuyuList(){
		$condition['parentid']=array('neq',0);
		$this->getFormListAll('MisAutoMji','id,name,parentid','id',$condition);
	}
	//用户资料
	public function getUserDetail(){
		$this->getFormInfo('MisAutoRor');
	}
	
	/**
	 * getMyShangpinList我的商品
	 */
	public function getMyShangpinList(){
		$this->getFormList('MisAutoLriMyView','id,name,jiage,zhuangtai,userid','name,jiage,zhuangtai','','MisAutoLjc');
	}
	//预约购买
	public function setMisAutoLjcStatus(){
		$id=$_REQUEST['id'];
		$userid=$_REQUEST['userid'];
		$shuliang=$_REQUEST['shuliang'];
		$data['userid']=$userid;
		$data['ordertype']=2;
		$data['shangpin']=$id;
		$data['createtime']=time();
		$result=$this->CURD('MisAutoLri','add',$data);
		if($result){
			$map['id']=$id;
			$result1=$this->CURD('MisAutoLjc','select',$data,$map);
			if($result1){
				$data2['shuliang']=$result1['shuliang']-$shuliang;
				$result2=$this->CURD('MisAutoLjc','select',$data2,$map);
				if($result2){
					$data=array();
					$code='1000';
					$msg='1000:成功';
				}else{
					$data=array();
					$code='1001';
					$msg='1001:失败';
				}
			}else{
				$data=array();
				$code='1001';
				$msg='1001:失败';
			}
		}else{
			$data=array();
			$code='1001';
			$msg='1001:失败';
		}
		return $this->getReturnData($data,$returnType,$code,$msg);
		//$this->getFormInfo('MisAutoRor');
	}
	
	
	
}