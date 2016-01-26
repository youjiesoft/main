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
class CheckForObjModel extends CommonModel{
	protected $trueTableName = "mis_system_checkforobj";
	
	public $_auto =array(
			array("createid","getMemberId",self::MODEL_INSERT,"callback"),
			array("updateid","getMemberId",self::MODEL_UPDATE,"callback"),
			array("createtime","time",self::MODEL_INSERT,"function"),
			array("updatetime","time",self::MODEL_UPDATE,"function"),
	);
	
	protected $autoCheckFields = false;
	public function SetRules($data=array()){
		$filename=  $this->GetFile();
		$this->writeover($filename,"return ".$this->pw_var_export($data).";\n",true);
	}

	public function GetRules($keyVal = '') {
		$value = '';
		if (file_exists($this->GetFile())) {
			$selectlist = require $this->GetFile();
			foreach ($selectlist as $key => $val) {
				if ($key) {
					if ($key==$keyVal) {
						$value = $val;
					}
				}

			}
		}
		return $value;
	}
	public function checkRename($name=''){
		$value='';
		if(file_exists($this->getFile())){
			$selectlist = require $this->GetFile();
			foreach($selectlist as $k=>$v){
				if($v['title']==$name){
					$value = $name;
				}
			}
		}
		return $value;
	}
	public  function GetFile(){
		return  DConfig_PATH."/System/checkforobj.inc.php";
	}
}

?>