<?php
/**
 * @Title: MobileOpenExtendSmsModel
 * @Package 核心公共代码类
 * @Description: 第三方扩展类，针对微信功能
 * @author liminggang
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d˾
 * @copyright Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d˾
 * @date 2014-2-08
 * @version V1.0
 */
class MobileOpenExtendSmsModel extends MobileOpenExtendBaseModel {
	//具体的通道类型
	private $Channel='';
	//短信Class
	private  $SmsClass="";
	//具体的通道对象
	private $SmsObj='';
	//流量控制,S为单位的
	private  $flowControl="60";
	//短信用户名
	private  $SmsAccount="";
	//短信密码
	private  $SmsPassword="";	
	//加密方式
	private  $SmsPswdModel="";	
	//短信使用模板 
	private  $SmsFsCodeMould="";
	public function __construct($channel,$SmsFsCodeMould){
		parent::__construct();
		$this->Channel=$channel;
		//短信使用模板
		$this->SmsFsCodeMould = $SmsFsCodeMould ? $SmsFsCodeMould : "1278843"; 
		//引用Sms的配置文件
		//$SmsList为获取的配置文件数组
		$SmsConfList = require DConfig_PATH."/MobileConf/SmsConf.inc.php";
		//获取配置详情
		$SmsConfInfo=$SmsConfList[$channel];
		if($SmsConfInfo){
			//短信Class名
			$this->SmsClass=isset($SmsConfInfo['SmsClass']) ? $SmsConfInfo['SmsClass'] : $this->SmsClass;			
			//流量控制值
			$this->flowControl=isset($SmsConfInfo['SmsFlowControl']) ? $SmsConfInfo['SmsFlowControl'] : $this->flowControl;
			//短信密码值
			$this->SmsAccount=isset($SmsConfInfo['SmsAccount']) ? $SmsConfInfo['SmsAccount'] : $this->SmsAccount;
			//短信密码值
			$this->SmsPassword=isset($SmsConfInfo['SmsPassword']) ? $SmsConfInfo['SmsPassword'] : $this->SmsPassword;
			//短信密码模式
			$this->SmsPswdModel=isset($SmsConfInfo['SmsPswdModel']) ? $SmsConfInfo['SmsPswdModel'] : $this->SmsPswdModel;
			switch($this->SmsPswdModel){
				case 'MD5':
					$this->SmsPassword=md5($this->SmsPassword);
					break;
				default:
					break;
			}
			//异常捕获
			try{
				if($SmsConfInfo){
					import($SmsConfInfo['SmsPath']);					
					$this->SmsObj= new $this->SmsClass($this->SmsAccount,$this->SmsPassword);
				}
				else{
					throw new AppSmsException('对应的短信渠道不存在，请检查');
				}
			}
			catch(Exception $e){
				
			}
		}
		//引入三方包		
	}
	/*
	 * 提供短信发送频次控制
	* 控制在多久之类不能重复请求发送
	* param phone int flowcontrol  int
	* return boolean  true可以发送，false不能发送
	*/
	private static function  flowControl($phone,$flowControl){
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
		if($dphList<5 &&  $hphList<3 && $stime>=$flowControl){
			return true;
// 			//调用短信接口
// 			import('@.ORG.SmsHttp');
// 			$smsHttp= new SmsHttp;
// 			//短信操作
// 			$smsNotic=$smsHttp->getMessage($phone,$content,$httpType=1);
// 			//回写状态正常时
// 			$data=$result;
// 			$code='1000';
// 			$msg=$smsNotic."验证码为：".$verifcode;
// 			//添加号码发送短信记录
// 			$phdate['phone']=$phone;
// 			$phdate['createtime']=time();
// 			$phresult=$this->CURD('MisSystemPhoneHistery','add',$phdate);
		}else{
			throw new AppSmsException('数据流时间间隔控制');
			//return false;
// 			$data=array();
// 			$code='1006';
// 			if($dphList>=5){
// 				$msg='1006:当天验证码获取超过5条';
// 			}elseif($hphList>=3){
// 				$msg='1006:一个小时内验证码获取超过3条';
// 			}elseif($stime<60){
// 				$msg='1006:上次获取验证码时间不足60s';
// 			}		
		}
	}		
	 /*
   * 提供短信
   * $phone 电话号码，多个以,号分隔;在函数内做循环；如果可以，思考提供高速模式
   * $content 发送内容
   * $httpType 请求类型  1为发送短信,2为获取上行短信,3为获取报告,4为查询余额
   */
  private function getMessage($phone,$content,$httpType=1){
  	  	
  //	try {
  	  //流量控制验证
  	  $result=self::flowControl($phone,$this->flowControl);
  	
  	  //发送短信
  	  $test = $this->SmsFsCodeMould;//使用默认模板
  	  $result=self::getMessageObj($phone,$content,$httpType,$test);//1278843 短信模板编号
  	  //往短信历史记录来处理，考虑phone，号分隔情况
  	   $result=self::insertMessageHistory($phone); 	  
  	  return $result;
  	//}
  	//catch(Exception $e){
  	///	var_dump($e->getMessage());
  	//} 

  }
  /*
   * 提供短信
  * $phone 电话号码，多个以,号分隔
  * $content 发送内容
  * $httpType 请求类型  1为发送短信,2为获取上行短信,3为获取报告,4为查询余额
  */
  private function getMessageObj($phone,$content,$httpType,$test){
  		$result=$this->SmsObj->getMessage($phone,$content,$httpType,$this->SmsFsCodeMould);
  		$p = xml_parser_create();
  		xml_parse_into_struct($p, $result, $vals, $index);
  		xml_parser_free($p);
  		$ret = '';
  		foreach($vals as $k=>$v){
  			$ret[$v['tag']] = $v;
  		}
		if($result==''){
			throw new AppSmsException('返回值为null , phone:'.$phone.';content:'.$content);
		}
  }
  /*
   * 提供短信写入成功记录
  * $phone 电话号码
  */
  private function insertMessageHistory($phone){
  		$phdate['phone']=$phone;
  		$phdate['createtime']=time();
  		$phresult=$this->CURD('MisSystemPhoneHistery','add',$phdate); 
  		if($phresult===false){
  			throw new AppSmsException('写入历时记录失败');
  		} 	
  }
  
  public function getMessageInfo($phone,$content,$httpType=1){
  	$this->getMessage($phone,$content,$httpType);
  } 
}