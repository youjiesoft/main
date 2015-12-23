<?php
/**
 * @Title: MobilePushCoreAction
 * @Package APIcloud推送类
 * @Description: APIcloud推送类
 * @author liminggang
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d˾
 * @copyright Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d˾
 * @date 2014-2-08
 * @version V1.0
 */
class MobilePushCoreAction {
	//定义静态变量，存储APIcolud固定信息
	//apiid
	private $AppID = "";//'A6964876883288';
	//apikey
	private $AppKey ="";// '1B724738-454E-858A-A740-1F1E4B3DC0FB';
	//apipath
	private $AppPath ="https://p.apicloud.com/api/push/message/";
	private $headerInfo=array();
	private $timeOut = 10;
	
	public function __construct($appid,$appkey){
		//构造函数内赋值内容
		$this->AppID = $appid;
		$this->AppKey = $appkey;
		$this->APICloud();
	}
	function APICloud(){
		$this->headerInfo = array(
				'X-APICloud-AppId:'.$this->AppID,
				'X-APICloud-AppKey:'.$this->getSHAKey()
		);
	}
	function getMilliSecond(){
		list($s1, $s2) = explode(' ', microtime());
		return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
	}
	
	function getSHAKey(){
		$time = $this->getMilliSecond();
		return sha1($this->AppID .'UZ' . $this->AppKey . 'UZ'.$time).'.'.$time;
	}
	
	function push($data){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt ($ch, CURLOPT_URL, $this->AppPath);//设置链接
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $this->headerInfo);//设置HTTP头
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );//POST数据
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
}