<?php
//Version 1.0
// 系统配置
class SystemConfigListModel extends CommonModel {
	protected $autoCheckFields = false;
	public function setValue($data=array()){
		$filename = $this->GetFile();
		$this->writeover($filename,'$configlist = '.$this->pw_var_export($data).";",true);
	}

	public function GetValue($keyVal=''){
		$value = '';
		if(file_exists($this->GetFile())){
		    require $this->GetFile();
		    foreach ($configlist as $key=>$val){
			if($key==$keyVal){
			    $value=$val;
			}
		    }
		}
		return $value;
	}

	public function GetAllValue(){
	    $value = '';
	    if(file_exists($this->GetFile())){
		require $this->GetFile();
		$value = $configlist;
	    }
	    return $value;
	}
	public function GetFile(){
		return  DConfig_PATH."/System/configlist.inc.php";
	}
    
    public function getOrderretraceview() {
    	$fileUrl = DConfig_PATH . "/System/configorderretrace.inc.php";
    	if (file_exists($fileUrl)) {
    		require $fileUrl;
    		return $view;
    	} else {
    		return '';
    	}
    }
    
    public function getOrderretracearr() {
    	$fileUrl = DConfig_PATH . "/System/configorderretrace.inc.php";
    	if (file_exists($fileUrl)) {
    		require $fileUrl;
    		return $arr;
    	} else {
    		return '';
    	}
    }
    /**
     * @Title: getNormArray 
     * @Description: todo(获取中文对应数组)   
     * @author 杨东 
     * @date 2013-5-10 下午4:16:27 
     * @throws
     */
    public function getNormArray(){
    	$fileUrl = DConfig_PATH . "/System/FixedConfig.inc.php";
    	if (file_exists($fileUrl)) {
    		require $fileUrl;
    		return $normArray;
    	} else {
    		return '';
    	}
    }
    /**
     * @Title: getCheckOutMethod
     * @Description: todo(模块操作方法获取排除无需授权节点方法)
     * @author liminggang
     * @date 2013-5-10 下午4:16:27
     * @throws
     */
    public function getCheckOutMethod(){
    	$fileUrl = DConfig_PATH . "/System/checkOutMethod.inc.php";
    	if (file_exists($fileUrl)) {
    		require $fileUrl;
    		return $notarr;
    	} else {
    		return '';
    	}
    }
    /**
     * @Title: getProjectMyWork 
     * @Description: todo(项目管理我的工作配置文件读取方法) 
     * @return unknown|string  
     * @author liminggang 
     * @date 2014-1-13 上午10:10:54 
     * @throws
     */
    public function getProjectMyWork(){
    	$fileUrl = DConfig_PATH . "/System/projectMyWork.inc.php";
    	if (file_exists($fileUrl)) {
    		require $fileUrl;
    		return $mdarr;
    	} else {
    		return '';
    	}
    }
}
?>