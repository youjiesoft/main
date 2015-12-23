<?php
//Version 1.0
// 系统配置
class AutoformModel extends CommonModel {
	protected $autoCheckFields = false;
	private $path;
	public function createJSCode($code ,$path, $desc ){
		if($desc){
			$str="/**";
			$str.="\r\n * @Title: Config";
			$str.="\r\n * @Package package_name";
			$str.="\r\n * @Description: todo(动态表单_组件配置文件-".$desc.")";
			$str.="\r\n * @author ".$_SESSION['loginUserName'];
			$str.="\r\n * @company 重庆特米洛科技有限公司";
			$str.="\r\n * @copyright 本文件归属于重庆特米洛科技有限公司";
			$str.="\r\n * @date ".date('Y-m-d H:i:s');
			$str.="\r\n * @version V1.0";
			$str.="\r\n*/\n";
		}
		$code = $str.$code;
		if(!is_writeable($path)){
			chmod($path,0755);
		}
		
		if( false === file_put_contents( $path , $code )){
			logs('行【'.__LINE__.'】类【'.__CLASS__.'】 生成JS失败'.$path);
		}
		
	}
	/**
	 * 生成扩展JS
	 * @param string $code	代码内容
	 * @param string $path	生成路径
	 * @param string $desc	代码描述
	 */
	public function createExtendJSCode($code ,$path, $desc ){
		if($desc){
			$str="/**";
			$str.="\r\n * @Title: Config";
			$str.="\r\n * @Package package_name";
			$str.="\r\n * @Description: todo(动态表单_组件配置文件-".$desc.")";
			$str.="\r\n * @author ".$_SESSION['loginUserName'];
			$str.="\r\n * @company 重庆特米洛科技有限公司";
			$str.="\r\n * @copyright 本文件归属于重庆特米洛科技有限公司";
			$str.="\r\n * @date ".date('Y-m-d H:i:s');
			$str.="\r\n * @version V1.0";
			$str.="\r\n*/\n";
		}
		$code = $str.$code;
		if(!is_writeable($path)){
			chmod($path,0755);
		}
		if(!file_exists($path)){
			if( false === file_put_contents( $path , $code )){
				logs('行【'.__LINE__.'】类【'.__CLASS__.'】 生成JS扩展失败'.$path);
			}
		}
		
	
	}
	public function SetRules($data=array(),$desc=''){
		$filename=  $this->GetFile();
		$str ='';
		if($desc){
			$str="/**";
			$str.="\r\n * @Title: Config";
			$str.="\r\n * @Package package_name";
			$str.="\r\n * @Description: todo(动态表单_组件配置文件-".$desc.")";
			$str.="\r\n * @author ".$_SESSION['loginUserName'];
			$str.="\r\n * @company 重庆特米洛科技有限公司";
			$str.="\r\n * @copyright 本文件归属于重庆特米洛科技有限公司";
			$str.="\r\n * @date ".date('Y-m-d H:i:s');
			$str.="\r\n * @version V1.0";
			$str.="\r\n*/\n";
		}
		/*
		 if(PHP_OS != 'WINNT'){
			$filename=str_replace(DConfig_PATH, './Dynamicconf', $filename);
			}
			*/
		$pathinfo = pathinfo($filename);
		if (!is_dir( $pathinfo['dirname'] )) {
			$this->make_dir( $pathinfo['dirname'],0777);
		}
		logs('开始生成生成默认的配置文件'.$filename);
		$this->writeover($filename,$str."return ".$this->pw_var_export($data).";\n",true);
	}
	public function SetAnameRules($date=array(),$aname){
		$filename=  $this->GetFile();
		$str ='';
		if($desc){
			$str="/**";
			$str.="\r\n * @Title: Config";
			$str.="\r\n * @Package package_name";
			$str.="\r\n * @Description: todo(动态表单_组件配置文件-".$desc.")";
			$str.="\r\n * @author ".$_SESSION['loginUserName'];
			$str.="\r\n * @company 重庆特米洛科技有限公司";
			$str.="\r\n * @copyright 本文件归属于重庆特米洛科技有限公司";
			$str.="\r\n * @date ".date('Y-m-d H:i:s');
			$str.="\r\n * @version V1.0";
			$str.="\r\n*/\n";
		}
		$data=require $filename;
		if(is_array($data)){
			$data[$aname]=$date;
		}else{
			unset($data);
			$data[$aname]=$date;
		}
		/*if(PHP_OS == 'WINNT'){
			$savePath=$filename;
			}else{
			$savePath=str_replace(DConfig_PATH, './Dynamicconf', $filename);
			}*/
		$this->writeover($savePath,$str."return ".$this->pw_var_export($data).";\n",true);
	}
	public function Setnoaccessmode($date=array(),$aname){
		$filename=DConfig_PATH.'/System/noaccessmode.php';
		if(PHP_OS != 'WINNT'){
			$filename=str_replace(DConfig_PATH, './Dynamicconf', $filename);
		}
		$pathinfo = pathinfo($filename);
		if (!is_dir( $pathinfo['dirname'] )) {
			$this->make_dir( $pathinfo['dirname'],0777);
		}
		$str ='';
		if($desc){
			$str="/**";
			$str.="\r\n * @Title: Config";
			$str.="\r\n * @Package package_name";
			$str.="\r\n * @Description: todo(动态表单_无需授权文件-".$desc.")";
			$str.="\r\n * @author ".$_SESSION['loginUserName'];
			$str.="\r\n * @company 重庆特米洛科技有限公司";
			$str.="\r\n * @copyright 本文件归属于重庆特米洛科技有限公司";
			$str.="\r\n * @date ".date('Y-m-d H:i:s');
			$str.="\r\n * @version V1.0";
			$str.="\r\n*/\n";
		}
		$data=require $filename;
		if(is_array($data)){
			if($date){
				$data[$aname]=$date;
			}else{
				unset($data[$aname]);
			}
		}else{
			unset($data);
			$data[$aname]=$date;
		}
		//logs('元数据:'.$this->pw_var_export($data));
		$this->writeover($filename,$str."return ".$this->pw_var_export($data).";\n",true);
	}
	/**
	 * 生成权限配置文件
	 * @param array		$data	数据源
	 * @param string	$desc	说明
	 * @param boolean	$isExtends	是否开启扩展功能,默认为true 开启
	 */
	public function SetListinc($data=array(),$desc='' ,$isExtends = true){
		$filename=  $this->GetFile();
		$str ='';
		if($desc){
			$str="/**";
			$str.="\r\n * @Title: Config";
			$str.="\r\n * @Package package_name";
			$str.="\r\n * @Description: todo(动态表单_配置文件-".$desc.")";
			$str.="\r\n * @author ".$_SESSION['loginUserName'];
			$str.="\r\n * @company 重庆特米洛科技有限公司";
			$str.="\r\n * @copyright 本文件归属于重庆特米洛科技有限公司";
			$str.="\r\n * @date ".date('Y-m-d H:i:s');
			$str.="\r\n * @version V1.0";
			$str.="\r\n*/\n";
		}
		/*if(PHP_OS != 'WINNT'){
			$filename=str_replace(DConfig_PATH, './Dynamicconf', $filename);
			}*/
		$pathinfo = pathinfo($filename);
		if (!is_dir( $pathinfo['dirname'] )) {
			$this->make_dir( $pathinfo['dirname'],0777);
		}
		logs('开始生成listinc文件系统:'.PHP_OS.'路径:'.$filename);
		if($isExtends){
			$this->writeover($filename,$str."\$original = ".$this->pw_var_export($data).";\n\n\$extedsList = require 'listExtend.inc.php';\nreturn array_merge(\$original , \$extedsList);",true);
		}else{
			$this->writeover($filename,$str."return ".$this->pw_var_export($data).";",true);
		}
	}
	
	/**
	 * 生成权限配置文件-手动修改扩展
	 * @param array		$data	数据源
	 * @param string	$desc	说明
	 */
	public function SetListExtendinc($data=array(),$desc=''){
		$filename=  $this->GetFile();
		$str ='';
		if($desc){
			$str="/**";
			$str.="\r\n * @Title: Config";
			$str.="\r\n * @Package package_name";
			$str.="\r\n * @Description: todo(动态表单_配置文件-".$desc.")";
			$str.="\r\n * @author ".$_SESSION['loginUserName'];
			$str.="\r\n * @company 重庆特米洛科技有限公司";
			$str.="\r\n * @copyright 本文件归属于重庆特米洛科技有限公司";
			$str.="\r\n * @date ".date('Y-m-d H:i:s');
			$str.="\r\n * @version V1.0";
			$str.="\r\n*/\n";
		}
		/*if(PHP_OS != 'WINNT'){
		 $filename=str_replace(DConfig_PATH, './Dynamicconf', $filename);
		}*/
		$pathinfo = pathinfo($filename);
		if (!is_dir( $pathinfo['dirname'] )) {
			$this->make_dir( $pathinfo['dirname'],0777);
		}
		logs('kuozhanadas:'.PHP_OS.'路径:'.$filename);
		if(!file_exists($filename)){
			$this->writeover($filename,$str."return ".$this->pw_var_export($data).";\n",true);
		}
		
	}
	
	

	/**
	 * 生成权限配置文件
	 * @param array		$data	数据源
	 * @param string	$desc	说明
	 */
	public function SetToolBar($data=array(),$desc=''){
		$filename=  $this->GetFile();
		$str ='';
		if($desc){
			$str="/**";
			$str.="\r\n * @Title: Config";
			$str.="\r\n * @Package package_name";
			$str.="\r\n * @Description: todo(动态表单_配置文件-".$desc.")";
			$str.="\r\n * @author ".$_SESSION['loginUserName'];
			$str.="\r\n * @company 重庆特米洛科技有限公司";
			$str.="\r\n * @copyright 本文件归属于重庆特米洛科技有限公司";
			$str.="\r\n * @date ".date('Y-m-d H:i:s');
			$str.="\r\n * @version V1.0";
			$str.="\r\n*/\n";
		}
		/*if(PHP_OS != 'WINNT'){
			$filename=str_replace(DConfig_PATH, './Dynamicconf', $filename);
			}*/
		$pathinfo = pathinfo($filename);
		if (!is_dir( $pathinfo['dirname'] )) {
			$this->make_dir( $pathinfo['dirname'],0777);
		}
		/*if(PHP_OS != 'WINNT'){
			$filename=str_replace('/Dynamicconf', '', $filename);
			}*/
		logs('开始生成toolbar主文件 系统:'.PHP_OS.'路径:'.$filename);
		$this->writeover($filename,$str."\$original = ".$this->pw_var_export($data).";\n\$extedsTool = require 'toolbar.extensionExtend.inc.php';\nreturn array_merge(\$original , \$extedsTool);",true);
	}

	/**
	 * 生成权限配置文件
	 * @param array		$data	数据源
	 * @param string	$desc	说明
	 */
	public function SetToolBarExtend($data=array(),$desc=''){
		$filename=  $this->GetFile();
		$str ='';
		if($desc){
			$str="/**";
			$str.="\r\n * @Title: ExtendConfig";
			$str.="\r\n * @Package package_name";
			$str.="\r\n * @Description: todo(动态表单_配置文件-".$desc.")";
			$str.="\r\n * @author ".$_SESSION['loginUserName'];
			$str.="\r\n * @company 重庆特米洛科技有限公司";
			$str.="\r\n * @copyright 本文件归属于重庆特米洛科技有限公司";
			$str.="\r\n * @date ".date('Y-m-d H:i:s');
			$str.="\r\n * @version V1.0";
			$str.="\r\n*/\n";
		}

		/*if(PHP_OS != 'WINNT'){
			$filename=str_replace(DConfig_PATH, './Dynamicconf', $filename);
			}*/
		$pathinfo = pathinfo($filename);
		if (!is_dir( $pathinfo['dirname'] )) {
			$this->make_dir( $pathinfo['dirname'],0777);
		}

		logs('开始生成toolbar扩展文件 系统:'.PHP_OS.'路径:'.$filename);
		if(!file_exists($filename)){
			$this->writeover($filename,$str."\nreturn ".$this->pw_var_export($data).";\n",true);
		}
	}

	public function GetRules($keyVal = '') {
		$value = '';
		if (file_exists($this->GetFile())) {
			$aryRule = require $this->GetFile();
			foreach ($aryRule as $key => $val) {
				if ($key) {
					if ($key==$keyVal) {
						$value = $val;
					}
				}
			}
		}
		return $value;
	}

	/**
	 * @Title: getAllNode
	 * @Description: todo(获取所有节点信息)
	 * @author quqiang
	 * @date 2014-10-9 上午10:43:18
	 * @throws
	 */
	public function getAllNode(){
		$value = '';
		if (file_exists($this->GetFile())) {
			$aryRule = require $this->GetFile();
			$value = $aryRule;
		}
		return $value;
	}

	/**
	 * @Title: getAllChild
	 * @Description: todo(获取所有节点信息)
	 * @author quqiang
	 * @param boolean $useconfig 是否使用物理文件。true:是。false:从数据库中查
	 * @date 2014-10-9 上午10:43:18
	 * @throws
	 */
	public function getAllControllConfig($useconfig=true){
		if($useconfig){
			$value = array();
			if (file_exists($this->GetFile())) {
				$aryRule = require $this->GetFile();
				//$value = $aryRule;
				foreach($aryRule as $k=>$v){
					$value = array_merge($value, $v);
				}
			}
		}else{
			// 从数据库中获取当前表单的所有节点配置信息
		}
		return $value;
	}
	/**
	 * @Title: getOrderNode
	 * @Description: todo(获取指定节点信息)
	 * @param string $node 节点名称
	 * @author quqiang
	 * @date 2014-10-9 上午10:43:18
	 * @throws	空数据
	 */
	public function getOrderNode($node='index'){
		$value = '';
		if($node){
			$aryRule = $this->getAllNode();
			if ($aryRule) {
				$value = $aryRule[$node];
			}
		}
		return $value;
	}

	/**
	 * @Title: saveOrderNode
	 * @Description: todo(保存指定节点信息)
	 * @param string $node 节点名称
	 * @param array	 $data	需要保存的数据
	 * @author quqiang
	 * @date 2014-10-9 上午10:57:55
	 * @throws
	 */
	public function saveOrderNode($node='index' , $data){
		/**
		 * 保存操作步骤：
		 * 1.得到原始数据，
		 * 2.覆盖指定节点信息数据
		 * 3.写入新数据
		 */
		$originalData = $this->getAllNode();
		$originalData[$node] = $data;
		$this->SetRules($originalData);
	}

	public function GetWritable($tablename=""){
		if(file_exists($this->GetFile())){
			require $this->GetFile();
			foreach ($aryRule as $key=>$val){
				if($key==$tablename){
					$writable=$val['writable'];
					if ($val['status']!='1') {
						$writable='1';//单号规则禁用时，可写状态
					}
				}
			}
		}
		return $writable;
	}


	/**
	 * 设置保存路径
	 * @param string $path	数据路径
	 */
	public function setPath($path){
		if($path){
			$this->path=$path;
		}
	}
	/**
	 * 获取路径
	 */
	public  function GetFile(){
		if(!$this->path){
			return  "./Dynamicconf/System/list.inc.php";
			/*
			 if(PHP_OS == 'WINNT'){
				return  "./Dynamicconf/System/list.inc.php";
				}else{
				return  "./Dynamicconf/System/list.inc.php";
				}*/
		}else{
			// 检查传入路径是否有/
			$in='/';
			$out = strpos($this->path , $in);
			$path= "./Dynamicconf".$this->path;
			if($out===false || $out >0){
				$path= "./Dynamicconf/".$this->path;
			}
			return  $path;
				
			/*if(PHP_OS == 'WINNT'){
				return  DConfig_PATH.$this->path;
				}else{
				//return  $this->path; // 20141209 linux系统兼容
				return  DConfig_PATH.$this->path;
				//return  APP_PATH.APP_NAME.'/Dynamicconf'.$this->path;
				}*/
			//DConfig_PATH
			//return  $this->path;
		}
	}
}
?>