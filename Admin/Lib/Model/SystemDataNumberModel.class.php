<?php
//Version 1.0
// 系统配置
class SystemDataNumberModel extends CommonModel {
	protected $autoCheckFields = false;
	public $firstDetail='';
	public function SetRules($data=array()){
		$filename=  $this->GetFile();
		$this->writeover($filename,"\$aryRule = ".$this->pw_var_export($data).";\n",true);
	}

	public function GetRules($keyVal = '') {
		$value = '';
		if (file_exists($this->GetFile())) {
			require $this->GetFile();
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

	public function GetRulesNO($tablename="",$max){
		$rule=$RulesNO=$temp=$num='';
		if(file_exists($this->GetFile())){
			require $this->GetFile();
			foreach ($aryRule as $key=>$val){
				if ($val['status']) {
					if($key==$tablename){
						$rule=$val['rule'];
						$num=$val['num'];
						$att=preg_split("/(?=[a-z])/", $rule);
						$RulesNO.=$att[0];
						if(strpos($rule,'year')){
							$temp.="Y";
						}
						if(strpos($rule,'moth')){
							$temp.="m";
						}
						if(strpos($rule,'day')){
							$temp.="d";
						}
						if(strpos($rule,'num')){
							if(isset($max)){
								$temp.=sprintf("%0".$num."d", $max);
							}else{
								$temp.=sprintf("%0".$num."d", D($tablename)->max('id')+1);
							}
						}
						$RulesNO.=date($temp);
						return $RulesNO;
					}
				}
			}
		}
	}

	/**
	 * @Title: GetRulesNumber
	 * @Description: todo(单表存多种数据类型，自定义，数据编码，)
	 * @author eagle
	 * @date 2013-11-04
	 * @throws
	 */
	public function GetRulesNumber($tablename="",$code="",$condition="",$sub="",$prefix=""){
		$rule=$RulesNO=$temp=$num='';
		if(file_exists($this->GetFile())){
			require $this->GetFile();
			foreach ($aryRule as $key=>$val){
				if ($val['status']) {
					if($key==$tablename){
						$rule=$val['rule'];
						$num=$val['num'];
						$att=preg_split("/(?=[a-z])/", $rule); //大写字母，不被分离
						if(empty($prefix)){
							$RulesNO.=$att[0];
						}else{
							$RulesNO.=$prefix;
						}
						if(strpos($rule,'year')){
							$temp.="Y";
						}
						if(strpos($rule,'moth')){
							$temp.="m";
						}
						if(strpos($rule,'day')){
							$temp.="d";
						}
						if(empty($sub)){ 												//如果算的是，子记录尾数相加，不执行些代码段
							if(strpos($rule,'num')){
								if(!empty($code)){
									$model = D($tablename);
									$maxCode = $model->where($condition)->max($code);		//查询数据库中的最大的值
									$findSymbol = stripos($maxCode,'-');					//说明是有特别符的
									if($findSymbol){
										$strArray = explode("-",$maxCode);
										$maxCode = $strArray[0];
									}
									$endNumber= substr($maxCode, -$num); 				//从尾部开始截取数字，长度同数据字段指定
									$temp.=sprintf("%0".$num."d",$endNumber+1); 		//关键点，数据库最大id值
								}else{
									$temp.=sprintf("%0".$num."d", D($tablename)->max('id')+1); 	//关键点，数据库最大id值
								}
							}
						}
						if(!empty($sub)){
							$subMap['code']=array('like','%'.$sub.'%');
							$model = D($tablename);
							$maxCode = $model->where($subMap)->max($code);		//查询数据库中的最大的值
							//echo $maxCode;
							$endNumber= substr($maxCode, -2);
							$subNum = substr($sub,-$num);						//只要数字
							$temp.=sprintf($subNum."-"."%02d", $endNumber+1); 	//关键点，数据库最大id值
						}
						//dump($temp);
						//dump($RulesNO);
						$RulesNO.=date($temp);
						//dump($RulesNO);
						return $RulesNO;
					}
				}
			}
		}
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

	public  function GetFile(){
		return  DConfig_PATH."/System/list.inc.php";
	}
}