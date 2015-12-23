<?php
//Version 1.0
class CommonModel extends Model {

	// 获取当前用户的ID
	public function getMemberId() {
		return isset($_SESSION[C('USER_AUTH_KEY')])?$_SESSION[C('USER_AUTH_KEY')]:0;
	}
	//获取当前公司id
    public function getCompanyID(){
    	return isset($_SESSION['companyid'])?$_SESSION['companyid']:0;
    }
    //获取当前登录人部门
    //获取当前公司id
    public function getDeptID(){
    	return isset($_SESSION['user_dep_id'])?$_SESSION['user_dep_id']:0;
    }
    //获取当前登录人职级
    public function getDutyID(){
    	return isset($_SESSION['user_duty_id'])?$_SESSION['user_duty_id']:0;
    }
    
    //单位转换
    public function unitExchange($qty=0,$baseunit,$subunit,$type=1){
    	
    	//mis_system_unit_exchange里的$baseunit，$subunit，转换数量$qty,转换类型$type 1为存储 2为读取
    	$result=unitExchange($qty,$baseunit,$subunit,$type=1);
    	return $result;
    }
	// 日期格式转成，字符串格式   eagle
	public function dateToTimeString($dateValue) {
		//有分秒格式的要把 “&nbsp” 删除
		if($dateValue){
			$dateValue=str_replace("&nbsp;"," ",$dateValue);
			if(trim($dateValue)) {
				return strtotime($dateValue);
			}
		}
		return false;
	}
	/**
	 * @Title: setDefaultVal 
	 * @Description: todo(将满足某些部分，空值需要直接转换为其他内容) 
	 * @param unknown_type $str  
	 * @author quqiang 
	 * @date 2014-12-5 上午04:37:01 
	 * @throws
	 */
	public function setDefaultVal($str){
		if( $str===''){
			$str = '无';
		}
		return $str;
	}
	/**
	 * @Title: setnull
	 * @Description: todo(将空值转换为null作用于数字型字段)
	 * @param unknown_type $str
	 * @author quqiang
	 * @date 2014-12-5 上午04:37:01
	 * @throws
	 */
	public function setnull($str){
		if( $str===''){
			$str = null;
		}
		return $str;
	}
	/**
	 * @Title: getActionName
	 * @Description: todo(获取模型名称)
	 * @param 
	 * @author xiayq
	 * @date 2015-5-4 上午12:00:01
	 * @throws
	 */
	
	public  function getActionName(){
		return $this->name;
	}
	/**
	 +----------------------------------------------------------
	 * 去掉数字中的“，”号
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param array $val 数字值
	 +----------------------------------------------------------
	 * @return boolen
	 +----------------------------------------------------------
	 */
	public function numberToReplace($tempval){
		if ($tempval ) {
			$val= str_replace(",","",$tempval);
			return floatval($val);
		}
		return false;
	}
	public function arrayToserialize($array){
		if($array){
			$arr = serialize($array);
			return $arr;
		}
		return false;
	}
	/**
	 * @Title: implodFeld
	 * @Description: todo(用逗号分隔数据)
	 * @param array $feld
	 * @return boolean
	 * @author liminggang
	 * @date 2013-2-2 上午10:51:33
	 * @throws
	 */
	public function implodFeld($feld) {
		if(isset($feld)) {
			return implode(',',$feld);
		}
		return "";
	}

	/**
	 +----------------------------------------------------------
	 * 根据条件禁用表数据
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param array $options 条件
	 +----------------------------------------------------------
	 * @return boolen
	 +----------------------------------------------------------
	 */
	public function forbid($options,$field='status'){

		if(FALSE === $this->where($options)->setField($field,0)){
			$this->error =  L('_OPERATION_WRONG_');
			return false;
		}else {
			return true;
		}
	}

	/**
	 +----------------------------------------------------------
	 * 根据条件批准表数据
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param array $options 条件
	 +----------------------------------------------------------
	 * @return boolen
	 +----------------------------------------------------------
	 */

	public function checkPass($options,$field='status'){
		if(FALSE === $this->where($options)->setField($field,1)){
			$this->error =  L('_OPERATION_WRONG_');
			return false;
		}else {
			return true;
		}
	}


	/**
	 +----------------------------------------------------------
	 * 根据条件恢复表数据
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param array $options 条件
	 +----------------------------------------------------------
	 * @return boolen
	 +----------------------------------------------------------
	 */
	public function resume($options,$field='status'){
		if(FALSE === $this->where($options)->setField($field,1)){
			$this->error =  L('_OPERATION_WRONG_');
			return false;
		} else {
			return true;
		}
	}

	/**
	 +----------------------------------------------------------
	 * 根据条件恢复表数据
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param array $options 条件
	 +----------------------------------------------------------
	 * @return boolen
	 +----------------------------------------------------------
	 */
	public function recycle($options,$field='status'){
		if(FALSE === $this->where($options)->setField($field,0)){
			$this->error =  L('_OPERATION_WRONG_');
			return false;
		}else {
			return true;
		}
	}

	public function recommend($options,$field='is_recommend'){
		if(FALSE === $this->where($options)->setField($field,1)){
			$this->error =  L('_OPERATION_WRONG_');
			return false;
		}else {
			return true;
		}
	}

	public function unrecommend($options,$field='is_recommend'){
		if(FALSE === $this->where($options)->setField($field,0)){
			$this->error =  L('_OPERATION_WRONG_');
			return false;
		}else {
			return true;
		}
	}
	/*
	 * 文件重写
	* @access public
	+----------------------------------------------------------
	* @author mashihe
	+----------------------------------------------------------
	* @throws FcsException
	+----------------------------------------------------------
	*/
	/*
	 public  function writeover($filename,$data,$safe = false,$method='wb'){
	$safe && $data = "<?php \n".$data."\n?>";
	$handle = fopen($filename,$method);
	fwrite($handle,$data);
	fclose($handle);
	}
	*/
	public  function writeover($filename,$data,$safe = false,$method='wb'){
		$safe && $data = "<?php \n".$data."\n?>";
		// $handle = fopen($filename,$method);
		// fwrite($handle,$data);
		// fclose($handle);
	return	file_put_contents($filename,$data);
	}
	/*
	 * 参数过滤导出
	* @access public
	+----------------------------------------------------------
	* @author mashihe
	+----------------------------------------------------------
	* @throws FcsException
	+----------------------------------------------------------
	*/
	public  function pw_var_export($input,$t = null)
	{
		$output = '';
		if (is_array($input))
		{
			$output .= "array(\r\n";
			foreach ($input as $key => $value)
			{
				$output .= $t."\t".$this->pw_var_export($key,$t."\t").' => '.  $this->pw_var_export($value,$t."\t");
				$output .= ",\r\n";
			}
			$output .= $t.')';
		} elseif (is_string($input)) {
			$output .= "'".str_replace(array("\\","'"),array("\\\\","\'"),$input)."'";
		} elseif (is_int($input) || is_double($input)) {
			$output .= "'".(string)$input."'";
		} elseif (is_bool($input)) {
			$output .= $input ? 'true' : 'false';
		} else {
			$output .= 'NULL';
		}

		return $output;
	}
	/*
	 * by:mashihe
	* 获取所有的数据库表
	*/
	public function gettables(){
		return $this->db->getTables();
	}
	/*
	 * by:mashihe
	* 获取数据表字段
	*
	*/
	public function getfields($table=''){
		return $this->db->getFields($table);
	}
	/*
	 * by:mashihe
	* 获取数据表字段及注释
	*
	*/
	public function getcomment($table=''){
		$aryfields=array();
		$sql="show full fields from $table";
		$list=$this->db->query($sql);
		foreach($list as $k=>$v){
			$aryfields[$v['Field']]=$v;
		}
		return $aryfields;
	}
	public function make_dir($path) {
		$path = str_replace('\\', '/', $path);
		if (is_dir($path)) return true;
		$arr = explode('/', $path);
		$pathNew = '';
		//以数组中的值为文件夹名建立文件夹
		foreach ($arr as $dir) {
			$dir = trim($dir);
			if ($dir == '.' || $dir == '..' || empty($dir)) continue;
			$dir .= '/';
			if (!is_dir($pathNew.$dir)) mkdir($pathNew.$dir, 0777);
			$pathNew .= $dir;
		}
		return is_dir($path);
	}
	/*
	 * 验证：开始日期是否早于结束日期
	* @author yangxi
	+----------------------------------------------------------
	* @access public
	+----------------------------------------------------------
	* @param
	* $date1     2012-01-01格式   日期段1
	* $date2     2012-01-01格式   日期段2
	* $type      int            类型，区分日期段1为开始时间还是结束时间，默认0为结束时间，1为开始时间
	+----------------------------------------------------------
	* @return string
	+----------------------------------------------------------
	*/
	public function  dataCompare($date1,$date2,$type="0"){
		if($type=="0"){
			$enddate=$date1;
			$startdate=$date2;

		}else{
			$stratdate=$date1;
			$enddate=$date2;		
		}
		if($startdate && $enddate){
			$startdate=strtotime($startdate);
			$enddate=strtotime($enddate);
			if($startdate>$enddate){
				return false;
			}
			else{
				return true;
			}
		}
		else{
			return false;
		}
	}	
	/*
	 * 日期间距
	 * @author yangxi
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param  
	 * $startdate   2012-01-01格式   开始日期 
	 * $enddate     2012-01-01格式   结束日期
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	*/
	public function  daysCalculate($startdate,$enddate){
		if($startdate && $enddate){
			$enddate=strtotime($enddate);
			$startdate=strtotime($startdate);
			$days = round(abs($enddate-$startdate)/ (3600 * 24));
		}
		else{
			$days=0;
		}
		return $days;
	}
	
	/*
	* 将数组的value转成string
	* @author quqiang
	+----------------------------------------------------------
	* @access public
	+----------------------------------------------------------
	* @param
	* $filed     数组
	+----------------------------------------------------------
	* @return string
	+----------------------------------------------------------
	*/
	public function arrayToString($filed){
		foreach ($filed as $k=>$v){
			$temp[]=$v;
		}
		if(isset($temp)) {
			return implode(',',$temp);
		}
		return "";
	} 
	/*
	 * @author  wangcheng
	* @date:2012-12-114 21:48:40
	* @describe:分类的扩展属性[可通用]
	* @param $map 查询条件 exp:map=array(
			"catalogid"=>0;//类型 [可扩展1;2;3分别代表不同的l类型];
			"linkid"   =>1024;//当前类型下所属分类id;
	);
	* @return:返回由<p> 标签组合的html 数组
	*/
	function get_expand_property($map,$data=array()){
		//print_r($data);
		unset($map['tableid']);
		$fieldTypeModel = M("mis_typeform_field_type");
		$typeList = $fieldTypeModel->where("status=1")->getField('id,code');
		$fieldModel = M("mis_typeform_field");
		$map['status']=1;
		$fieldList =$fieldModel->where($map)->order("pos asc")->select();
		//print_R($fieldModel->getLastSql());
		$option=array();
		foreach($fieldList AS $k=>$field) {
			//定义检查条件数组
			$checkreg=array();
			if($field['checkreg']){
				$arrcheckreg=explode(";",$field['checkreg']);
				foreach($arrcheckreg as $k2=>$v2){
					$checkreg[]=$typeList[$v2];
				}
			}
				
			//通过传递的数组参数获取当前值或默认值
			$field['defaultval']= isset($data[$field['id']]['content']) ? $data[$field['id']]['content']:$field['defaultval'];
			$option[]= $this->expand_property_html($typeList[$field['fieldtypeid']], $field['name'], $field['options'], $field['defaultval'], $field['tips'] ,$data[$field['id']]['id'],$field['id'], $field['ismust'],$checkreg);
		}
		return $option;
	}
	
	/*
	 * @author  wangcheng
	* @date:2012-12-114 21:48:40
	* @describe:由get_expand_property调用，用于组合html
	* @param string $type  类型 exp:text,select  参数较多请参看具体内容
	* @return:返回由<p> 标签组合的html 数组
	*/
	function expand_property_html($type = '', $title = '', $options = '' ,$default = '', $msg = '' ,$tableid = '',$extendid = '',$must = '',$checkreg='') {
		//print_R($default);
		//是否必填
		$must = $must ? 'required ' : '';
		//没有默认值时换成当前时间
		$default=$default ? $default:'';//date('Y-m-d',time());
		//对对象进行验证
		foreach($checkreg as $key => $val ){
			switch($val){
				case 'date':
					//日期类型追加
					$dataExchange='<a href="javascript:;" class="inputDateButton">选择</a>';
					break;
	
				case 'readonly':
					//只读类型追加
					$readonly='readonly';
					break;
	
				case 'editor':
					//行数显示
					$rows='4';
					break;
			}
		}
	
		//定义提示信息
		$msg = $msg ? '<span class="info">&nbsp;'.$msg.'</span>':'';
		//根据类型返回字段类型
		switch ($type) {
			case 'text':
				//文本框
				$option .= '<div class="tml-form-row"><label>'.$title.'：</label>
						    <input type="hidden" name="expand_property_tableid[]" value="'.$tableid.'" />
						    <input type="hidden" name="expand_property_extendid[]" value="'.$extendid.'" />
						    <input type="text" class="'.$must.$checkreg.'" name="expand_property_content['.$extendid.']" value="'.$default.'" '.$readonly.' />'.$dataExchange.$msg.'</div>';
				break;
	
			case 'hidden':
				//隐藏表单
				$option .= '<input type="hidden" name="expand_property_tableid[]" value="'.$tableid.'" />
						    <input type="hidden" name="expand_property_extendid[]" value="'.$tableid.'" />
						    <input type="hidden" name="expand_property_content['.$extendid.']" value="'.$default.'" />';
				break;
	
			case 'file':
				//上传表单
				$option .= '<div class="tml-form-row"><label>'.$title.'：</label>
						    <input type="hidden" name="expand_property_tableid[]" value="'.$tableid.'" />
						    <input type="hidden" name="expand_property_extendid[]" value="'.$extendid.'" />
						    <input type="file" class="'.$must.$checkreg.'" name="expand_property_content['.$extendid.']" value="'.$default.'" />'.$msg.'</div>';;
				break;
	
			case 'select':
				//下拉菜单
				$option .= '<div class="tml-form-row"><label>'.$title.'：</label>
						    <input type="hidden" name="expand_property_tableid[]" value="'.$tableid.'" />
						    <input type="hidden" name="expand_property_extendid[]" value="'.$extendid.'" />
						    <select class="combox '.$must.$checkreg.'" name="expand_property_content['.$extendid.']">';
				$arr = explode(";", $options);
				$option .='<option value="">--选择--</option>';
				foreach($arr AS $k=>$val) {
					$select= ($default==$val) ? 'selected':'';
					$option .= '<option '.$select.' value="'.$val.'">'.$val.'</option>';
				}
				$option .= '</select>'.$msg.'</div>';
				break;
	
			case 'checkbox':
				//复选框
				$option .= '<div class="tml-form-row"><label>'.$title.'：</label>
						    <input type="hidden" name="expand_property_tableid[]" value="'.$tableid.'" />
						    <input type="hidden" name="expand_property_extendid[]" value="'.$extendid.'" />';
				$optionsArr = explode(";", $options);
				$defaultArr = explode(";", $default);
				foreach($optionsArr AS $k=>$v) {
					$checked= (in_array($v,$defaultArr,true)) ? 'checked':'';
					$option .= $v.'<input '.$checked.' name="expand_property_content['.$extendid.'][]" type="checkbox" value="'.$v.'" />&nbsp&nbsp&nbsp';
				}
				$option .= $msg.'</div>';
				break;
	
			case 'radio':
				//单选按钮
				$option .= '<div class="tml-form-row"><label>'.$title.'：</label>
						    <input type="hidden" name="expand_property_tableid[]" value="'.$tableid.'" />
						    <input type="hidden" name="expand_property_extendid[]" value="'.$extendid.'" />';
				$arr = explode(";", $options);
				foreach($arr AS $k=>$v) {
					$checked= ($default==$v) ? 'checked':'';
					$option .= $v.'<input '.$checked.' type="radio" name="expand_property_content['.$extendid.']" value="'.$v.'"/>&nbsp&nbsp&nbsp';
				}
				$option .= $msg.'</div>';
				break;
			case 'textarea':
				//文本区域
				$option .= '<div class="tml-form-row"><label>'.$title.'：</label>
						    <input type="hidden" name="expand_property_tableid[]" value="'.$tableid.'" />
						    <input type="hidden" name="expand_property_extendid[]" value="'.$extendid.'" />
						    <textarea name="expand_property_content['.$extendid.']" class="'.$must.$checkreg.'" cols="60" rows="'.$rows.'">'.$default.'</textarea>'.$msg.'</div>';
				break;
			case 'pass':
				//密码表单
				$option .= '<div class="tml-form-row"><label>'.$title.'：</label>
						    <input type="hidden" name="expand_property_tableid[]" value="'.$tableid.'" />
						    <input type="hidden" name="expand_property_extendid[]" value="'.$extendid.'" />
						    <input type="password" class="'.$must.$checkreg.'" name="expand_property_content['.$extendid.']" value="'.$default.'" />'.$msg.'</div>';
				break;
		}
		return $option;
	}
	/**
	 +----------------------------------------------------------
	 * 字段值延迟增长
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param string $field  字段名
	 * @param integer $step  增长值
	 * @param integer $lazyTime  延时时间(s)
	 +----------------------------------------------------------
	 * @return boolean
	 +----------------------------------------------------------
	 */
	public function setLazyInc($field,$step=1,$lazyTime=0) {
		$condition   =  $this->options['where'];
		if(empty($condition)) { // 没有条件不做任何更新
			return false;
		}
		if($lazyTime>0) {// 延迟写入
			$guid =  md5($this->name.'_'.$field.'_'.serialize($condition));
			$step = $this->lazyWrite($guid,$step,$lazyTime);
			//print_r($step);
			if(false === $step ) return true; // 等待下次写入
		}
		return $this->setField($field,array('exp',$field.'+'.$step));
	}
	/**
	 +----------------------------------------------------------
	 * 延时更新检查 返回false表示需要延时
	 * 否则返回实际写入的数值
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param string $guid  写入标识
	 * @param integer $step  写入步进值
	 * @param integer $lazyTime  延时时间(s)
	 +----------------------------------------------------------
	 * @return false|integer
	 +----------------------------------------------------------
	 */
	protected function lazyWrite($guid,$step,$lazyTime) {
		$this->options=null;
		$cache= Cache::getInstance('',array("temp"=>TEMP_PATH.'lazy'));//缓存文件放在runtime/temp/lazy 目录下，可以自定义。
		if(false !== ($value = $cache->get($guid))) { // 存在缓存写入数据
			if(time()>$cache->get($guid.'_time')+$lazyTime) {
				// 延时更新时间到了，删除缓存数据 并实际写入数据库
				$cache->rm($guid);
				$cache->rm($guid.'_time');
				return $value+$step;
			}else{
				// 追加数据到缓存
				$cache->set($guid,$value+$step);
				return false;
			}
		}else{ // 没有缓存数据
	
			$cache->set($guid,$step);
			// 计时开始
			$cache->set($guid.'_time',time());
			return false;
		}
	}
	
	/**
	 +----------------------------------------------------------
	 * 字段值延迟增长
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param string $typename 类型名称
	 +----------------------------------------------------------
	 * @return string  $filename  文件名称
	 +----------------------------------------------------------
	 */	
	protected function getDynamicFileName($typename,$filename){
		switch ($typename){
			case "form":
				$filename="form.inc.php";
				break;
			case "data":
				$filename="data.inc.php";
				break;
			case "list":
				$filename="list.inc.php";
				break;
			case "toolbar":
				$filename="toolbar.extension.inc.php";
				break;
			case "toolbarExtend":
				$filename="toolbar.extensionExtend.inc.php";
				break;
			case "view":
				$filename=$filename.".inc.php";
				break;				
			default:
				$filename="list.inc.php";
				break;
		}

		return $filename;
	}
	public function _dataRoamResumeSql($targetTable,$data,$where,$operation='update',$type=false){
		return self::dataRoamResumeSql($targetTable,$data,$where,$operation='update',$type=false);
	}
	/**
	 +----------------------------------------------------------
	 * 数据漫游时的目标数据存在时的SQL语句  dataRoamResumeSql
	 +----------------------------------------------------------
	 * @access dataRoam
	 +----------------------------------------------------------
	 * @param mix data  写入标识
	 +----------------------------------------------------------
	 * @return string  sql
	 +----------------------------------------------------------
	 */
	static private  function dataRoamResumeSql($targetTable,$data,$where,$operation='update',$type=false){
		$sql="Select ";
		$field="";
		//如果为删除，强制显示全部全部字段
		if($operation=='delete'){
			$type=true;
		}
		//如果$type为ture，执行全表数据存储
		if($type){
			$fullFieldsSql='SHOW FULL COLUMNS FROM '.$targetTable;
			$data=M($targetTable)->query($fullFieldsSql);
			foreach($data as $key => $val){
				$field.="`".$val['Field']."`,";
			}
		}else{
			foreach($data as $key => $val){
				$field.=$val.",";
			}
		}
		$sql.=substr($field,0,-1)." from ".$targetTable.$where;
		$result=M($targetTable)->query($sql);
		//     	print_r(M($targetTable)->getLastSql());
		//     	print_r($result);
		//这是带where查询\
		$sqlArr=array();
		if($result){
			foreach($result as $backupKey=>$backupVal){
				switch($operation){
					case 'update':
						$sqlResume="Update ".$targetTable." SET ";
						$fieldVal="";
						foreach($backupVal as $subkey=>$subval){
							$fieldVal.=" `".$subkey."`='".$subval."',";
						}
						$sqlResume.=substr($fieldVal,0,-1).$where;
						break;
					case 'delete':
						$sqlResume="INSERT INTO ".$targetTable." ( ";
	
						$sort=1;//初始化计数器
						$field="";//初始化字段名
						$fieldVal="";//初始化字段值
						foreach($backupVal as $subkey=>$subval){
							//第一个值不要，号
							if($sort==1){
								$field.=" `".$subkey."`";
								$fieldVal.=" '".$subval."'";
							}else{
								$field.=",`".$subkey."`";
								$fieldVal.=",'".$subval."'";
							}
							$sort++;
						}
						$sqlResume.=$field.' ) VALUES ( '.$fieldVal." ) ";
						break;
				}
				$sqlArr[]=$sqlResume;
			}
			
			//     	print_r($sqlArr);
			//      	print_r(base64_encode(serialize($sqlArr)));
			//     	exit;
			//序列换返回值
			return array('sqlresume'=>base64_encode(serialize($sqlArr)),'dataresume'=>base64_encode(serialize($result)));
		}
	
	}
	/**
	 * @Title: getAvgSql
	 * @Description: todo(这里用一句话描述这个方法的作用) 
	 * @param unknown_type 表对象名称
	 * @param unknown_type 当前数据id
	 * @param unknown_type 组装高级sql
	 * @return string  
	 * @author 谢友志 
	 * @date 2015-9-9 下午4:40:51 
	 * @throws
	 */
	public function getAvgSql($tablename,$pkey,$sql){
// 		$tablename="mis_auto_mnkrf";
// 		$pkey=1;
		//初始化结果
		$result="";
		if($tablename){
			//查询数据
			$list=M($tablename)->where("id={$pkey} and status=1")->find();
			$tablelist=array();
			if($sql){
				$ieval="";
			//$sql="SELECT   * FROM  mis_auto_mnkrf  LEFT JOIN `mis_auto_bklne` ON mis_auto_mnkrf.projectid = mis_auto_bklne.id WHERE mis_auto_mnkrf.status = 1 AND mis_auto_bklne.id = mis_auto_mnkrf.projectid ";
				$whereCount = preg_match('/(\bwhere\b)/i', $sql);
				if($whereCount==1){
					$ret = preg_split('/(\bwhere\b)/i', $sql, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE );
					if(strtolower($ret[1]) == 'where' && $ret[2]){
						//循环替换主表数据
						foreach ($list as $key=>$val){
							if(strpos($ret[2], $tablename.".".$key)!== false){
								if(!$val){
									$ieval=0;
								}else{
									if(is_string($val)){
										$ieval="'".$val."'";
									}
									if(is_numeric($val)){
										$ieval=$eval;
									}
								}
								//替换前正则匹配where后
								$ret[2] = str_replace($tablename.".".$key, $val, $ret[2]);
							}
						}
					}
					//强制组装当前数据id 至where 最前端
					$resql=" ".$tablename.".id={$pkey} and ".$ret[2];
					$ret[2]=$resql;
					$sql = join($ret);
				}
				logs("我是最终返回sql---".$sql,"avgsql");
				//执行sql 返回结果
				$result=M($tablename)->query($sql);
			}
		}
		return $result;
	}
}
?>