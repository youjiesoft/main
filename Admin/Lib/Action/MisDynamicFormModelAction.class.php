<?php
/**
 * @Title: MisDynamicFormModelAction
 * @Package package_name
 * @Description: todo(生成Model文件)
 * @author quqiang
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2014-10-14 下午08:00:16
 * @version V1.0
 */
class MisDynamicFormModelAction extends MisDynamicFormCtrollAction{

	/**
	 * @Title: createModel
	 * @Description: todo(生成Model代码)
	 * @param array $fieldData 组件属性列表
	 * @param string $modelname 控制器名称
	 * @param boolean $add 是否覆盖现有文件，默认为false:需要覆盖
	 * @param boolean $isaudit 是否带审批流，默认为false:不带审批流
	 * @author quqiang
	 * @date 2014-8-21 上午10:11:34
	 * @throws
	 */
	protected  function createModel($fieldData , $modelname ,$add=false ,$isaudit=false){
		logs('C:'.__CLASS__.' L:'.__LINE__."model生成 modelname：$modelname");
		// 1.获取到当前Action的组件配置文件信息
/*		$model = D('Autoform');
		$dir = '/autoformconfig/';
		$path = $dir.$this->nodeName.'.php';
		$model->setPath($path);
*/
		// 所有节点的字段组件配置信息 从物理文件中获取内容  已废弃
		// $allControllConfig = $model->getAllControllConfig();
		// 从数据库中获取节点信息
		$formid = getFieldBy($this->nodeName, "actionname", "id", "mis_dynamic_form_manage");
		$formfieldSql="SELECT mas.tablename , sub.* FROM mis_dynamic_database_sub AS sub LEFT JOIN mis_dynamic_database_mas AS mas ".
				"ON sub.masid = mas.id ".
				"WHERE sub.formid={$formid}";
		$misDynamicDatabaseSubObj = M('mis_dynamic_database_sub');
		$fieldData = $misDynamicDatabaseSubObj->query($formfieldSql);
		logs('C:'.__CLASS__.' L:'.__LINE__.' model生成数据查询sql：'.$misDynamicDatabaseSubObj->getLastSql());
		$fieldData = $this->getAllControllConfig($formid);
		logs('C:'.__CLASS__.' L:'.__LINE__.' model生成数据：'.$this->pw_var_export($temp),date('Y-m-d' , time()).'_data.log');
		
		$publicProperty = $this->publicProperty;
		$primaryTableName  = $this->getTrueTableName();
			
		/**
		 *		表数组，基础为二维.
		 * 	0=>主表信息
		 * 	1=>所有从表的信息
		 */
		$tablenameArr = array();
		// 按真实表名分离数组
		foreach ($fieldData as $key => $value) {
			if($primaryTableName == $value['tablename'] ){
				// 分离主表
				$tablenameArr[0][$value['tablename']][] = $value;
			}else{
				// 分离从表
				$tablenameArr[1][$value['tablename']][] = $value;
			}
		}
		// 生成主表的model信息
		foreach ($tablenameArr[0] as $key => $value) {
			// 当前真实表名与Model名
			logs('多表 主表生成时节点：'.$this->nodeName);
			logs('C:'.__CLASS__.' L:'.__LINE__."多表 主表生成时节点：{$this->nodeName}");
			$modelname=createRealModelName($this->nodeName); //ucfirst($key);
			logs('C:'.__CLASS__.' L:'.__LINE__."多表 主表model名：$modelname");
			$truetablename=strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $key), "_"));
			logs('C:'.__CLASS__.' L:'.__LINE__."多表 主表 真实表名：$truetablename");
			$this->createOrderModel($value , $modelname ,$truetablename, $add , $isaudit);
		}
			
		//从表MODEL生成
		foreach ($tablenameArr[1] as $key => $value) {
			// 当前真实表名与Model名
			if($key != ':0'){
				$modelname=createRealModelName($key); //ucfirst($key);
				logs('C:'.__CLASS__.' L:'.__LINE__."多表 子表model名：$modelname");
				$truetablename=strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $key), "_"));
				logs('C:'.__CLASS__.' L:'.__LINE__."多表 子表 真实表名：$truetablename");
				$this->createOrderModel($value , $modelname ,$truetablename, $add , $isaudit , true);
			}
		}
		// 生成数据表格的model
		// 获取当前表单中的数据表格
		$sql="SELECT 
  CONCAT(m.tablename, '_sub_', p.fieldname) AS datatablename,
  IF(
    p.title <> '' 
    OR p.title <> NULL,
    p.title,
    p.fieldname
  ) AS title,
  d.*
FROM
  `mis_dynamic_form_propery` AS p 
  LEFT JOIN mis_dynamic_database_mas AS m 
    ON p.formid = m.formid 
   LEFT JOIN 
   `mis_dynamic_form_datatable` AS d
   ON 
   d.`propertyid`=p.id
WHERE p.formid ={$formid}
  AND p.category = 'datatable' 
  AND m.`isprimary` = 1 
		";
		
		$obj = M();
		$datatableData = $obj->query($sql);
		foreach ($datatableData as $k=>$v){
			$temp[$v['datatablename']][]=$v;
		}
		foreach ($temp as $k=>$v){
			$modelname=createRealModelName($k); //ucfirst($key);
			$this->createDatatableModel($v,$modelname,$k,false ,true , true);
		}
	}

	/**
	 * @Title: createView
	 * @Description: todo(生成查询视图代码)
	 * @param string $actionName	Action名称
	 * @author renling
	 * @date 2014-10-?
	 * @throws
	 * @modify
	 * 	屈强@2014-10-08 16:21	视图查询字段添加主表的ID
	 *
	 */
	protected function createView($actionName){

		// 通过action名称称获取所有表信息
		if(!$this->nodeName){
			$this->error('Action名称丢失！！');
		}
		$mis_dynamic_form_manageObj= M('mis_dynamic_form_manage');
		$forminfo= $mis_dynamic_form_manageObj->where("`actionname`='$this->nodeName'")->find();
		if(!is_array($forminfo)){
			$this->error('没有获取到表单记录信息');
		}
		// 得到字段记录。
		$MisDynamicDatabaseMasViewModel = D("MisDynamicDatabaseMasView");
		$allTableFiledArr = $MisDynamicDatabaseMasViewModel->where('mis_dynamic_database_mas.status=1 and  mis_dynamic_database_sub.status and  mis_dynamic_database_mas.formid='.$forminfo['id'])->select();
		// 将字段信息按所属表分组
		$tablelist=array();
		$newAllTableFieldArr=array();
		foreach ($allTableFiledArr as $key=>$val){
			if($val['category']!="datatable"){
				$tablearr=array(
						'tablename'=>$val['tablename'],
						'tabletitle'=>$val['tabletitle'],
						'isprimary'=>$val['isprimary'],
						'ischoise'=>$val['isprimary'],
				);
				$tablelist[$val['masid']][]=$val;
			}

		}
		logs("=============endview ===".__LINE__."-----》》》》==========".$this->nodeName);
		//$dir = ROOT . "/Dynamicconf/autoformconfig/MisAutoAnameList.inc.php";
		//$tablelist=require $dir;
		$modelPath =  LIB_PATH."Model/".$this->nodeName."ViewModel.class.php";
		logs("=============endview ===".__LINE__."-----》》》》==========".$this->nodeName);
		$listArr="";
		$primaryname="";
		foreach ($tablelist as $stkey=>$stval){
			if(getFieldBy($stkey,"id","isprimary","mis_dynamic_database_mas")){
				$listArr.="\t'".getFieldBy($stkey,"id","tablename","mis_dynamic_database_mas")."'=>array('id',";
				foreach ($stval as $sctkey=>$sctval){
					$listArr.="'".$sctval['field']."',";
				}
				/**
				 * 系统默认字段
				 */
				foreach ($this -> systemUserField as $bkey=>$bval){
					if($bval!=id){
						$listArr.="'".$bval."',";
					}
				}
				$listArr.="'_type'=>'LEFT'),\r\n";
				$primaryname=$sctval['tablename'];
			}
		}
		logs($tablelist,'view','',__CLASS__, __FUNCTION__ , __METHOD__);
		foreach ($tablelist as $tkey=>$tval){
			if(!getFieldBy($tkey,"id","isprimary","mis_dynamic_database_mas")){
				$listArr.="\t'".getFieldBy($tkey,"id","tablename","mis_dynamic_database_mas")."'=>array(";
				foreach ($tval as $ctkey=>$ctval){
					if(trim($ctval['field'])){
						$listArr.="'".$ctval['field']."',";
					}
				}
				$listArr.="'_on'=>'".$primaryname.".id=".getFieldBy($tkey,"id","tablename","mis_dynamic_database_mas").".masid'),\r\n";
			}
		}
		$phpcode.="<?php\r\n/**";
		$phpcode.="\r\n * @Title: ".$this->nodeName."ModelView";
		$phpcode.="\r\n * @Package package_name";
		$phpcode.="\r\n * @Description: todo(动态表单_自动生成-".$this->nodeTitle.")";
		$phpcode.="\r\n * @author ".$_SESSION['loginUserName'];
		$phpcode.="\r\n * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d";
		$phpcode.="\r\n * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d";
		$phpcode.="\r\n * @date ".date('Y-m-d H:i:s');
		$phpcode.="\r\n * @version V1.0";
		$phpcode.="\r\n*/";
		$phpcode.="\r\nclass ";
		$phpcode.= $this->nodeName."ViewModel extends ViewModel {\r\n\t";
		$phpcode .=<<<EOF
		
	function __construct(){
		parent::__construct();
		\$arr = getModelFilterByNodeSetting();
		if(is_array(\$arr)){
			\$this->_filter = \$arr;
		}
	}
	// 字段权限过滤
	protected \$_filter = array();
		
EOF;
		$phpcode.="public \$viewFields = array(\r\n".$listArr.");";
		$phpcode.="\r\n}\r\n?>";
		if(!is_dir(dirname($modelPath))) mk_dir(dirname($modelPath),0777);
		if( false === file_put_contents( $modelPath , $phpcode )){
			$this->error ("视图文件生成失败!");
		}
	}

	/**
	 * @Title: createOrderModel
	 * @Description: todo(生成Model代码)
	 * @param array $fieldData 组件属性列表
	 * @param string $modelname 控制器名称
	 * @param string $truetablename	真实表名
	 * @param boolean $add 是否覆盖现有文件，默认为false:需要覆盖
	 * @param boolean $isaudit 是否带审批流，默认为false:不带审批流
	 * @author quqiang
	 * @date 2014-9-30 下午5:20:34
	 * @throws
	 */
	private function createOrderModel($fieldData , $modelname  ,$truetablename ,$add=false ,$isaudit=false , $ischildren=false){
		$modelPath =  LIB_PATH."Model/".$modelname."Model.class.php";
		$isExist = (file_exists($modelPath));
		if( $isExist && $add){
			$this->error($modelPath."文件已存在!");
		}
		$autohtml1=$autohtml="";
		// 附加model处理函数
		$dateOprateCode='';
		
		$re= M($truetablename)->query('SHOW COLUMNS FROM `'.$truetablename."`");
		if($re) {
			foreach ($re as $key => $val) {
				$columns[$val['Field']] =$val['Field'];
			}
		}
		$autohtml1="\n\tpublic \$_auto =array(";
		$a=false;
		if(isset($columns["createid"])){
			$autohtml1.="\n\t\tarray(\"createid\",\"getMemberId\",self::MODEL_INSERT,\"callback\"),";
			$a=true;
		}
		if(isset($columns["updateid"])){
			$autohtml1.="\n\t\tarray(\"updateid\",\"getMemberId\",self::MODEL_UPDATE,\"callback\"),";
			$a=true;
		}
		if(isset($columns["createtime"])){
			$autohtml1.="\n\t\tarray(\"createtime\",\"time\",self::MODEL_INSERT,\"function\"),";
			$a=true;
		}
		if(isset($columns["updatetime"])){
			$autohtml1.="\n\t\tarray(\"updatetime\",\"time\",self::MODEL_UPDATE,\"function\"),";
			$a=true;
		}
		if(isset($columns["companyid"])){
			$autohtml1.="\n\t\tarray(\"companyid\",\"getCompanyID\",self::MODEL_INSERT,\"callback\"),";
			$a=true;
		}
		if(isset($columns["departmentid"])){
			$autohtml1.="\n\t\tarray(\"departmentid\",\"getDeptID\",self::MODEL_INSERT,\"callback\"),";
			$a=true;
		}
		if(isset($columns["sysdutyid"])){
			$autohtml1.="\n\t\tarray('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),";
			$a=true;
		}
		if($isaudit && isset($columns["informpersonid"])){
			//带审批流，存在知会人
			$autohtml1.="\n\t\tarray('informpersonid','implodFeld',self::MODEL_BOTH,'callback'),";
			$a=true;
		}
		$autohtml1.="\n\t\tarray('allnode','getActionName',self::MODEL_INSERT,'callback'),";
		$a=true;

		$phpcodeExtend.="<?php\r\n/**";
		$phpcodeExtend.="\r\n * @Title: {$modelname}Model";
		$phpcodeExtend.="\r\n * @Package package_name";
		$phpcodeExtend.="\r\n * @Description: todo(动态表单_自动生成-Model-扩展Modedl)";
		$phpcodeExtend.="\r\n * @author ".$_SESSION['loginUserName'];
		$phpcodeExtend.="\r\n * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d";
		$phpcodeExtend.="\r\n * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d";
		$phpcodeExtend.="\r\n * @date ".date('Y-m-d H:i:s');
		$phpcodeExtend.="\r\n * @version V1.0";
		$phpcodeExtend.="\r\n*/";
		$phpcodeExtend.="\r\nclass ";
		$extendModelName = $modelname.'ExtendModel';
		$phpcodeExtend.= $extendModelName." extends CommonModel {\r\n\t";
		$phpcodeExtend.="\n\t}";


		$phpcode.="<?php\r\n/**";
		$phpcode.="\r\n * @Title: {$modelname}Model";
		$phpcode.="\r\n * @Package package_name";
		$phpcode.="\r\n * @Description: todo(动态表单_自动生成-".$this->nodeTitle.")";
		$phpcode.="\r\n * @author ".$_SESSION['loginUserName'];
		$phpcode.="\r\n * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d";
		$phpcode.="\r\n * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d";
		$phpcode.="\r\n * @date ".date('Y-m-d H:i:s');
		$phpcode.="\r\n * @version V1.0";
		$phpcode.="\r\n*/";
		$phpcode.="\r\nclass ";
		$phpcode.= $modelname."Model extends {$extendModelName} {\r\n\t";
		$phpcode.="protected \$trueTableName = '".$truetablename."';";
		/*
		function __construct(){
		parent::__construct();
		\$arr = getModelFilterByNodeSetting();
		if(is_array(\$arr)){
			\$this->_filter = \$arr;
		}
	}
		*/
		$phpcode .=<<<EOF
		
	// 字段权限过滤
	protected \$_filter = array();
	
EOF;
		$hasvalidate=false;
		$validate="";
		$i=1;
		$j=count($fieldData);
		$iscreateOrderNo=false;
		$validate.="\n\tpublic \$_validate=array(\r";//start filter
		foreach($fieldData as $k=>$v){
			$property = $this->getProperty($v['catalog']);
			if($ischildren==false && $iscreateOrderNo==false){
				// orderno 全局验证唯一。
				$validate.="\n\t\tarray('orderno,status','','单号已经存在',self::MUST_VALIDATE,'unique',self::MODEL_BOTH),";
				$validate.="\n\t\tarray('orderno','require','单号必须'),";
				$iscreateOrderNo = true;
			}
			
			if( $v["unique"] ){
				$validate.="\n\t\tarray('".$v['fields']."','','".$v['title']."已经存在',self::MUST_VALIDATE,'unique',self::MODEL_BOTH),";
			}
			if($v['catalog'] == 'date'){
// 				$autohtml1 .= "\n\t\tarray('{$v['fields']}','strtotime',self::MODEL_BOTH,'function'),";
				
				if($this->checkTimeFormat($v['formatphp'])){
					$dateAutoStr ="\n\t\tarray('{$v['fields']}','strtotime',self::MODEL_BOTH,'function'),";
				}else{
					$dateAutoStr ="\n\t\tarray('{$v['fields']}','mktime_{$v['fields']}',self::MODEL_BOTH,'callback'),";
					$dateOprateCode.= $this->dateTimeOpraete($v['fields'],$v['formatphp']);
				}
				$autohtml1 .= $dateAutoStr;
			}
			
			if($v['catalog'] == 'checkbox'){
				$autohtml1 .= "\n\t\tarray('{$v['fields']}','arrayToString',self::MODEL_BOTH,'callback'),";
			}
			if($v['catalog'] == 'text' && $v[$property['unitls']['name']] && $v[$property['unitl']['name']]){
				
			 	// unitls:存储单,  unitl:显示单位
				$autohtml1 .= "\n\t\tarray('{$v['fields']}','unitExchange',self::MODEL_BOTH,'callback',array('{$v[$property['unitl']['name']]}','{$v[$property['unitls']['name']]}',1)),";
			}
			
			// 字段类型为int的空值转为null存储
			if(strtolower($v['tabletype']) == 'int'||strtolower($v['tabletype']) == 'decimal' || strtolower($v['tabletype']) == 'date'){
				$autohtml1 .= "\n\t\tarray('{$v['fields']}','setnull',self::MODEL_BOTH,'callback'),";
			}			
			$i++;
		}
		
		$validate.="\r\n\t);";
		$autohtml1.="\n\t);";
		$autohtml=$autohtml1;

		$phpcode.=$autohtml.$validate.$dateOprateCode;
		$phpcode.="\r\n}\r\n?>";
		logs("Model文件生成! ".$modelPath);
		if(!is_dir(dirname($modelPath))) mk_dir(dirname($modelPath),0777);
		if( false === file_put_contents( $modelPath , $phpcode )){
			$this->error ("Model文件生成失败! ".$modelPath);
		}
		logs("扩展Model文件生成! ".$modelPath);
		$phpcodeExtendPath = LIB_PATH."Model/".$extendModelName.".class.php";
		if(!file_exists($phpcodeExtendPath)){
			if( false === file_put_contents( $phpcodeExtendPath , $phpcodeExtend )){
				$this->error ("扩展Model文件生成失败! ".$phpcodeExtendPath);
			}
		}
	}

	/**
	 * @Title: createOrderModel
	 * @Description: todo(生成数据表格的Model文件)
	 * @param array $fieldData 组件属性列表
	 * @param string $modelname 控制器名称
	 * @param string $truetablename	真实表名
	 * @param boolean $add 是否覆盖现有文件，默认为false:需要覆盖
	 * @param boolean $isaudit 是否带审批流，默认为false:不带审批流
	 * @author quqiang
	 * @date 2014-12-11 下午02:24:37
	 * @throws
	 */
	protected function createDatatableModel($fieldData , $modelname  ,$truetablename ,$add=false ,$isaudit=false , $ischildren=false){
		$modelPath =  LIB_PATH."Model/".$modelname."Model.class.php";
		$isExist = (file_exists($modelPath));
		if( $isExist && $add){
			$this->error($modelPath."文件已存在!");
		}
		$autohtml1=$autohtml="";
		$re= M($truetablename)->query('SHOW COLUMNS FROM `'.$truetablename."`");
		if($re) {
			foreach ($re as $key => $val) {
				$columns[$val['Field']] =$val['Field'];
			}
		}
		$autohtml1="\n\tpublic \$_auto =array(";
		$a=false;
		if(isset($columns["createid"])){
			$autohtml1.="\n\t\tarray(\"createid\",\"getMemberId\",self::MODEL_INSERT,\"callback\"),";
			$a=true;
		}
		if(isset($columns["updateid"])){
			$autohtml1.="\n\t\tarray(\"updateid\",\"getMemberId\",self::MODEL_UPDATE,\"callback\"),";
			$a=true;
		}
		if(isset($columns["createtime"])){
			$autohtml1.="\n\t\tarray(\"createtime\",\"time\",self::MODEL_INSERT,\"function\"),";
			$a=true;
		}
		if(isset($columns["updatetime"])){
			$autohtml1.="\n\t\tarray(\"updatetime\",\"time\",self::MODEL_UPDATE,\"function\"),";
			$a=true;
		}
		if(isset($columns["companyid"])){
			$autohtml1.="\n\t\tarray(\"companyid\",\"getCompanyID\",self::MODEL_INSERT,\"callback\"),";
			$a=true;
		}
		if(isset($columns["departmentid"])){
			$autohtml1.="\n\t\tarray(\"departmentid\",\"getDeptID\",self::MODEL_INSERT,\"callback\"),";
			$a=true;
		}
		if(isset($columns["sysdutyid"])){
			$autohtml1.="\n\t\tarray('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),";
			$a=true;
		}
		if($isaudit && isset($columns["informpersonid"])){
			//带审批流，存在知会人
			$autohtml1.="\n\t\tarray('informpersonid','implodFeld',self::MODEL_BOTH,'callback'),";
			$a=true;
		}

		$phpcodeExtend.="<?php\r\n/**";
		$phpcodeExtend.="\r\n * @Title: {$modelname}Model";
		$phpcodeExtend.="\r\n * @Package package_name";
		$phpcodeExtend.="\r\n * @Description: todo(动态表单_自动生成-数据表格-Model-扩展Modedl)";
		$phpcodeExtend.="\r\n * @author ".$_SESSION['loginUserName'];
		$phpcodeExtend.="\r\n * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d";
		$phpcodeExtend.="\r\n * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d";
		$phpcodeExtend.="\r\n * @date ".date('Y-m-d H:i:s');
		$phpcodeExtend.="\r\n * @version V1.0";
		$phpcodeExtend.="\r\n*/";
		$phpcodeExtend.="\r\nclass ";
		$extendModelName = $modelname.'ExtendModel';
		$phpcodeExtend.= $extendModelName." extends CommonModel {\r\n\t";
		$phpcodeExtend.="\n\t}";


		$phpcode.="<?php\r\n/**";
		$phpcode.="\r\n * @Title: {$modelname}Model";
		$phpcode.="\r\n * @Package package_name";
		$phpcode.="\r\n * @Description: todo(动态表单_自动生成-数据表格-".$this->nodeTitle.")";
		$phpcode.="\r\n * @author ".$_SESSION['loginUserName'];
		$phpcode.="\r\n * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d";
		$phpcode.="\r\n * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d";
		$phpcode.="\r\n * @date ".date('Y-m-d H:i:s');
		$phpcode.="\r\n * @version V1.0";
		$phpcode.="\r\n*/";
		$phpcode.="\r\nclass ";
		$phpcode.= $modelname."Model extends CommonModel {\r\n\t";
		$phpcode.="protected \$trueTableName = '".$truetablename."';";
		$hasvalidate=false;
		$validate="";
		// 附加model处理函数
		$dateOprateCode='';
		$i=1;
		$j=count($fieldData);
		$iscreateOrderNo=false;
		$validate.="\n\tpublic \$_validate=array(\r";//start filter
		foreach($fieldData as $k=>$v){
			if($ischildren==false && $iscreateOrderNo==false){
				// orderno 全局验证唯一。
				$validate.="\n\t\tarray('orderno,status','','单号已经存在',self::EXISTS_VAILIDATE,'unique',self::MODEL_BOTH),";
				$validate.="\n\t\tarray('orderno','require','单号必须'),";
				$iscreateOrderNo = true;
			}
			if($v['category'] == 'text' && $v['unitls'] && $v['unit']){
				// unitls:存储单,  unitl:显示单位
				$autohtml1 .= "\n\t\tarray('{$v['fieldname']}','unitExchange',self::MODEL_BOTH,'callback',array('{$v['unit']}','{$v['unitls']}',1)),";
			}			
			if( $v["unique"] ){
				$validate.="\n\t\tarray('".$v['fieldname']."','','".$v['title']."已经存在',self::EXISTS_VAILIDATE,'unique',self::MODEL_BOTH),";
			}
			if($v['category'] == 'date'){
				//$autohtml1 .= "\n\t\tarray('{$v['fieldname']}','strtotime',self::MODEL_BOTH,'function'),";
				$config = unserialize(base64_decode($v['config']));
				if($this->checkTimeFormat($config['parame']['dateformat'][1])){
					$dateAutoStr ="\n\t\tarray('{$v['fieldname']}','strtotime',self::MODEL_BOTH,'function'),";
				}else{
					$dateAutoStr ="\n\t\tarray('{$v['fieldname']}','mktime_{$v['fieldname']}',self::MODEL_BOTH,'callback'),";
					$dateOprateCode.= $this->dateTimeOpraete($v['fieldname'],$config['parame']['dateformat'][1]);
				}
				$autohtml1 .= $dateAutoStr;
				
				
			}
			// 字段类型为int的空值转为null存储
			if($v['fieldtype'] == 'INT'||$v['fieldtype'] == 'DECIMAL' || $v['fieldtype']='DATE' ){
				$autohtml1 .= "\n\t\tarray('{$v['fieldname']}','setnull',self::MODEL_BOTH,'callback'),";
			}
			
// 			if($v['catalog'] == 'checkbox'){
// 				$autohtml1 .= "\n\t\tarray('{$v['fieldname']}','arrayToString',self::MODEL_BOTH,'callback'),";
// 			}

			$i++;
		}
		
		$validate.="\r\n\t);";
		$autohtml1.="\n\t);";
		$autohtml=$autohtml1;

		$phpcode.=$autohtml.$validate.$dateOprateCode;
		$phpcode.="\r\n}\r\n?>";
		
		logs("数据表格Model文件生成! ".$modelPath);
		logs("数据表格Model文件生成! --- ".$phpcode);
		if(!is_dir(dirname($modelPath))) mk_dir(dirname($modelPath),0777);
		if( false === file_put_contents( $modelPath , $phpcode )){
			$this->error ("Model文件生成失败! ".$modelPath);
		}
		//生成list配置文件		
		$this->createDatatableList($fieldData, $modelname, $truetablename);
	}
	/**
	 * 日期组件非标准格式下的处理代码生成
	 * @Title: dateTimeOpraete
	 * @Description: todo(这里用一句话描述这个方法的作用) 
	 * @param unknown $field
	 * @param unknown $fmt  
	 * @author quqiang 
	 * @date 2016年1月13日 下午3:51:52 
	 * @throws
	 */
	function dateTimeOpraete($field,$fmt){
		$code = <<<EOF
		
	/**
	 * 非标准时间格式数据处理为时间戳
	 * @Title: mktime_{$field}
	 * @Description: todo(非标准时间格式数据处理为时间戳) 
	 * @return 时间戳|空值  
	 * @author quqiang 
	 * @date 2016年1月13日 下午3:33:53 
	 * @throws
	 */
	function mktime_{$field}(\$curval){
		if(!\$curval){
			return \$curval;
		}
		\$fmt = '{$fmt}';
		// 所有格式参数
		\$fmtAll=array('Y','m','d','H','i','s');
		// 将显示格式处理为通用格式
		\$fmtOprate = str_replace(array(' ',':'),array('-','-'),\$fmt);
		// 将显示数据处理为通用格式数据
		\$valOprate = str_replace(array(' ',':'),array('-','-'),\$curval);
		
		\$curfmt = explode('-',\$fmtOprate);
		\$curvalOprate = explode('-',\$valOprate);
		\$ret = array();
		foreach (\$curvalOprate as \$k=>\$v){
			\$ret[\$curfmt[\$k]]=\$v;
		}
		list(\$Y,\$m,\$d,\$H,\$i,\$s) = explode('-',date('Y-m-d-H-i-s',time()));
		\$standTime = array('Y'=>\$Y,'m'=>\$m,'d'=>\$d,'H'=>\$H,'i'=>\$i,'s'=>\$s);
		\$mergeArr = array_merge(\$standTime,\$ret);
		\$valus = array_values(\$mergeArr);
		list(\$Y,\$m,\$d,\$H,\$i,\$s)=\$valus;
		\$time = mktime(\$H,\$i,\$s,\$m,\$d,\$Y);
		return \$time;
	}
EOF;
	 return $code;
	}
	/**
	 * 检查格式是否为标准格式
	 * @Title: checkTimeFormat
	 * @Description: todo(这里用一句话描述这个方法的作用) 
	 * @param string $fmt
	 * @return boolean  
	 * @author quqiang 
	 * @date 2016年1月13日 下午4:02:28 
	 * @throws
	 */
	function checkTimeFormat($fmt){
		$allownFormat=array('Y-m-d H:i:s','Y-m-d','H:i:s');
		if(in_array($fmt , $allownFormat))
			return true;
		else
			return false;
	}
	/**
	 * 
	 * @Title: createDatatableList
	 * @Description: todo(生成数据表格list文件) 
	 * @param array $fieldData	数据表格的字段信息
	 * @param string $modelname	模型名称
	 * @param string $truetablename  真实表名
	 * @author renling 
	 * @date 2015年1月12日 下午3:24:31 
	 * @throws
	 */
	protected  function createDatatableList($fieldData, $modelname, $truetablename){
		$k1=0;
		$shownumber=5;
		$shownumberCount=1;
		foreach ($fieldData as $fkey=>$fval){
			$fval['config']=unserialize(base64_decode($fval['config']));
			$fval['tablename']=$truetablename;
			$this->getDtTagsListincData(&$detailList ,$fval,$notshowcontroll,$fval['fieldname'], $shownumber , &$shownumberCount);
			$k1++;
		}
		$k1 = count($detailList);
		$detailList['id']=array(
				'name' => 'id',
				'showname' => 'ID',
				'shows' => '1',
				'widths' => '',
				'sorts' => count($detailList),
				'models' => '',
				'status' => '1',
				'sortname' => 'id',
				'sortnum' => 0,
				'searchField' => $truetablename.'.id',
				'conditions' => '',
				'type' => 'text',
				'issearch' => '1',
				'isallsearch' => '1',
				'searchsortnum' => '0'
		);
		$model = D('Autoform');
		$dir = '/Models/';
		$listincpath = $dir.$this->nodeName.'/'.$modelname.'.inc.php';
		$model->setPath($listincpath);
		$model->SetListinc($detailList , $modelname.'.inc 配置文件',false);
	}
	/**
	 * 
	 * @Title: getTagsListincData
	 * @Description: todo(生成内嵌表格detailList) 
	 * @param unknown $detailList
	 * @param unknown $v1
	 * @param unknown $notshowcontroll
	 * @param unknown $k1
	 * @param unknown $shownumber
	 * @param unknown $shownumberCount  
	 * @author renling 
	 * @date 2015年1月12日 下午5:35:33 
	 * @throws
	 */
	private function getDtTagsListincData(&$detailList ,$v1, $notshowcontroll ,$k1, $shownumber , &$shownumberCount){
		$str = htmlspecialchars($string);
 		// 是否添加到搜索列表
		$searchlist[$v1[$controllProperty['fields']['name']]]["field"]=$v1['fieldname'];
		$searchlist[$v1[$controllProperty['fields']['name']]]["shows"]=$v1['fieldtitle'];
		$detailList[$k1]['name']=$v1['fieldname'];
		$detailList[$k1]['showname']=$v1['fieldtitle'];
		$detailList[$k1]['shows']=1;
		$detailList[$k1]['widths']=$this -> controlConfig[$v1['category']]['listwidth'];
		$detailList[$k1]['sorts']=0;
		$detailList[$k1]['models']='';
		$detailList[$k1]['sortname']=$v1['fieldtitle'];
		$detailList[$k1]['sortnum']=$v1['fieldsort']?$v1['fieldsort']:$k1+1;
		$detailList[$k1]['iscount']=$v1['iscount'];
		// 添加新属性@20141124 1027
		$detailList[$k1]['fieldtype']=$v1['fieldtype'];  // 字段数据类型
		$detailList[$k1]['fieldcategory']=$v1['category']; // 字段显示用组件类型
	
		$tableName = $v1['tablename'];
	
		if($v1['category'] == 'date'){
			$detailList[$k1]['type'] = 'time';
		}else{
			$detailList[$k1]['type'] = $v1['category']?$v1['category']:"";
		}
		
		
		/*  表单导入配置*/
		//必填
		$detailList[$k1]['required']=$v1['config']['required'];
		//检查类型
		$detailList[$k1]['validate']=$v1['config']['checktype'];
		//唯一
		$detailList[$k1]['unique']=0;
		//是否转换
		$detailList[$k1]['transform']=0;
		
		
		switch ($v1['category']){
			case 'text':
				$unit=$v1['config']['untils'];
				$unitls=$v1['config']['baseuntils'];
				if($unit && $unitls){
					$fun=$fund=array();
					$fun[0][0]="unitExchange";
					$detailList[$k1]['func']=$fun;
					$fd=array("###");
					array_push($fd, $unitls);// 存储单位
					array_push($fd, $unit);// 显示单位
					array_push($fd, 3);// 转换类型
					$fund[0][0]=$fd; // 值
					$detailList[$k1]['funcdata']=$fund;
					//是否转换
					$detailList[$k1]['transform']=1;
					//反转函数
					$unfun=$fun;
					$detailList[$k1]['unfunc']=$unfun;
					$unfd=array("###");
					array_push($unfd, $unitls);// 存储单位
					array_push($unfd, $unit);// 显示单位
					array_push($unfd, 1);// 转换类型
					$unfund[0][0]=$unfd;
					$detailList[$k1]['unfuncdata']=$unfund;
				}
				break;
			case 'date':
				$fun=$fund=array();
				$fun[0][0]="transTime";
				$detailList[$k1]['func']=$fun;
				$fd=array("###");
				$fund[0][0]=$fd;
				$detailList[$k1]['funcdata']=$fund;
				//是否转换
				$detailList[$k1]['transform']=1;
				//反转函数
				$unfun[0][0]="untransTime";
				$detailList[$k1]['unfunc']=$unfun;
				$unfund[0][0]=$fd;
				$detailList[$k1]['unfuncdata']=$unfund;
				break;
			case 'select':
				if($v1['config']['datasouce']==1){
					//选择数据表
					$fun=$fund=array();
					$fun[0][0]="getFieldBy";
					$detailList[$k1]['func']=$fun;
					$fd=array("###",$v1['config']['datasouceparame'][2],$v1['config']['datasouceparame'][1],$v1['config']['datasouceparame'][0]);
					$fund[0][0]=$fd;
					$detailList[$k1]['funcdata']=$fund;
					$detailList[$k1]['table'] = $v1['config']['datasouceparame'][0]?$v1['config']['datasouceparame'][0]:"";
					$detailList[$k1]['field'] = $v1['config']['datasouceparame'][2]?$v1['config']['datasouceparame'][2]:"";
					//是否转换
					$detailList[$k1]['transform']=1;
					//反转函数
					$unfun[0][0]="ungetFieldBy";
					$detailList[$k1]['unfunc']=$unfun;
					$unfund[0][0]=$fd;
					$detailList[$k1]['unfuncdata']=$unfund;
				}else{
					$fun=$fund=array();
					$fun[0][0]="getSelectlistValue";
					$detailList[$k1]['func']=$fun;
					$fd=array("###",$v1['config']['datasouceparame'][0]);
					$fund[0][0]=$fd;
					$detailList[$k1]['funcdata']=$fund;
					//是否转换
					$detailList[$k1]['transform']=1;
					//反转函数
					$unfun[0][0]="ungetSelectlistValue";
					$detailList[$k1]['unfunc']=$unfun;
					$unfund[0][0]=$fd;
					$detailList[$k1]['unfuncdata']=$unfund;
				}
				break;
			case 'lookup':
				//查询当前查找带回配置
				$model=D("LookupObj");
				$selectVo=$model->GetLookupDetail($v1['config']['parame'][0]);
				$fun=$fund=array();
				$fun[0][0]="getFieldBy";
				$detailList[$k1]['func']=$fun;
				// id : $v1[$controllProperty['lookuporgval']['name']]
				$fd=array("###",$selectVo['val'],$selectVo['filed'],$selectVo['mode']);
				$fund[0][0]=$fd;
				$detailList[$k1]['funcdata']=$fund;
				$detailList[$k1]['table'] = $selectVo['mode']; 
				$detailList[$k1]['field'] = $selectVo['val'];
				//是否转换
				$detailList[$k1]['transform']=1;
				//反转函数
				$unfun[0][0]="ungetFieldBy";
				$detailList[$k1]['unfunc']=$unfun;
				$unfund[0][0]=$fd;
				$detailList[$k1]['unfuncdata']=$unfund;
				break;
		}
		$detailList[$k1]['shows']=1;
	}
	
}