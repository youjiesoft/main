<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(selectlist配置文件) 
 * @author libo 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-7-28 下午2:20:04 
 * @version V1.0
 */
class FormListDataModel extends CommonModel{
	/**
	 * (non-PHPdoc)
	 * @Description: SetRules(生成表单版本数据文件)
	 * @param $data   array 配置文件相关参数信息
	 * @param $modelname  对应model名称
	 * @param $filename 对应的文件名称
	 * @author yangxi  2014-12-12
	 */
	public function SetRules($data=array(),$modelname,$filename){
		$filepath=  $this->GetFile($modelname,$filename);
		$str ='';
		$str="/**";
		$str.="\r\n * @Title: FormListData";
		$str.="\r\n * @Description: todo(数据版本文件)";
		$str.="\r\n * @company 重庆特米洛科技有限公司";
		$str.="\r\n * @copyright 本文件归属于重庆特米洛科技有限公司";
		$str.="\r\n * @date ".date('Y-m-d H:i:s');
		$str.="\r\n * @version V1.0";
		$str.="\r\n*/\n";
		$pathinfo = pathinfo($filepath);
		if (!is_dir( $pathinfo['dirname'] )) {
			mkdir($pathinfo['dirname'], 0777);
		}
		$this->writeover($filepath,$str."return ".$this->pw_var_export($data).";\n",true);
	}
	
	/**
	 * (non-PHPdoc)
	 * @Description: GetFile(获取配置文件list.inc信息，指向性的获取某个字段)
	 * @param $keyVal sting  字段主键
	 * @param $modelname  sting  模型名称
	 * @author yangxi  2014-12-12
	 */
    public function GetRules($modelname,$filename) {
		$aryRule = array();
		if (file_exists($this->GetFile($modelname,$filename))) {
			$aryRule = require $this->GetFile($modelname,$filename);
		}
		return $aryRule;
	}
	/**
	 * 获取文件地址
	 * @param 模型名称 $modelname
	 * @param 文件名称 $filename
	 * @return string
	 * @author liminggang
	 */
	public  function GetFile($modelname,$filename){
		$filepath=DConfig_PATH."/FormListData/".$modelname."/".$filename.".php";
		return  $filepath;
	}
}

?>