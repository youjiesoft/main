<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2012 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id: Model.class.php 2815 2012-03-13 07:08:56Z liu21st $

/**
 +------------------------------------------------------------------------------
 * ThinkPHP Model模型类
 * 实现了ORM和ActiveRecords模式
 +------------------------------------------------------------------------------
 * @category   Think
 * @package  Think
 * @subpackage  Core
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id: Model.class.php 2815 2012-03-13 07:08:56Z liu21st $
 +------------------------------------------------------------------------------
 */
class Model {
    // 操作状态
    const MODEL_INSERT      =   1;      //  插入模型数据
    const MODEL_UPDATE    =   2;      //  更新模型数据
    const MODEL_BOTH      =   3;      //  包含上面两种方式
    const MUST_VALIDATE         =   1;// 必须验证
    const EXISTS_VAILIDATE      =   0;// 表单存在字段则验证
    const VALUE_VAILIDATE       =   2;// 表单值不为空则验证
    // 当前使用的扩展模型
    private $_extModel =  null;
    // 当前数据库操作对象
    protected $db = null;
    // 主键名称
    protected $pk  = 'id';
    // 数据表前缀
    protected $tablePrefix  =   '';
    // 模型名称
    protected $name = '';
    // 数据库名称
    protected $dbName  = '';
    // 数据表名（不包含表前缀）
    protected $tableName = '';
    // 实际数据表名（包含表前缀）
    protected $trueTableName ='';
    // 最近错误信息
    protected $error = '';
    // 字段信息
    protected $fields = array();
    // 数据信息
    protected $data =   array();
    // 查询表达式参数
    protected $options  =   array();
    protected $_validate       = array();  // 自动验证定义
    protected $_auto           = array();  // 自动完成定义
    protected $_map           = array();  // 字段映射定义
    // 是否自动检测数据表字段信息
    protected $autoCheckFields   =   true;
    // 是否批处理验证
    protected $patchValidate   =  false;
    protected $tabletype=0;
    /*
     * 1、字段权限过滤，主要是为表单页面字段不可查看用
     */
    protected $_filter = array();
    protected $_autoFuncCheck = array('unitExchange','setnull');//没有_POST值不执行
    /**
     +----------------------------------------------------------
     * 架构函数
     * 取得DB类的实例对象 字段检查
     +----------------------------------------------------------
     * @param string $name 模型名称
     * @param string $tablePrefix 表前缀
     * @param mixed $connection 数据库连接信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     */
    public function __construct($name='',$tablePrefix='',$connection='',$tabletype=0) {
        // 模型初始化
        $this->_initialize();
        // 获取模型名称
        $this->tabletype = $tabletype;
        if(!empty($name)) {
            if(strpos($name,'.')) { // 支持 数据库名.模型名的 定义
                list($this->dbName,$this->name) = explode('.',$name);
            }else{
                $this->name   =  $name;
            }
        }elseif(empty($this->name)){
            $this->name =   $this->getModelName();
        }
        // 设置表前缀
        if(is_null($tablePrefix)) {// 前缀为Null表示没有前缀
            $this->tablePrefix = '';
        }elseif('' != $tablePrefix) {
            $this->tablePrefix = $tablePrefix;
        }else{
            $this->tablePrefix = $this->tablePrefix?$this->tablePrefix:C('DB_PREFIX');
        }
        // 数据库初始化操作
        // 获取数据库操作对象
        // 当前模型有独立的数据库连接信息
        $this->db(0,empty($this->connection)?$connection:$this->connection);
    }

    /**
     +----------------------------------------------------------
     * 自动检测数据表信息
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    protected function _checkTableInfo() {
        // 如果不是Model类 自动记录数据表信息
        // 只在第一次执行记录
        if(empty($this->fields)) {
            // 如果数据表字段没有定义则自动获取
            if(C('DB_FIELDS_CACHE')) {
                $db   =  $this->dbName?$this->dbName:C('DB_NAME');
                $this->fields = F('_fields/'.$db.'.'.$this->name);
                if(!$this->fields)   $this->flush();
            }else{
                // 每次都会读取数据表信息
                $this->flush();
            }
        }
    }

    /**
     +----------------------------------------------------------
     * 获取字段信息并缓存
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function flush() {
        // 缓存不存在则查询数据表信息
        $this->db->setModel($this->name);
        $fields =   $this->db->getFields($this->getTableName());
        if(!$fields) { // 无法获取字段信息
            return false;
        }
        $this->fields   =   array_keys($fields);
        $this->fields['_autoinc'] = false;
        foreach ($fields as $key=>$val){
            // 记录字段类型
            $type[$key]    =   $val['type'];
            if($val['primary']) {
                $this->fields['_pk'] = $key;
                if($val['autoinc']) $this->fields['_autoinc']   =   true;
            }
        }
        // 记录字段类型信息
        if(C('DB_FIELDTYPE_CHECK'))   $this->fields['_type'] =  $type;

        // 2008-3-7 增加缓存开关控制
        if(C('DB_FIELDS_CACHE')){
            // 永久缓存数据表信息
            $db   =  $this->dbName?$this->dbName:C('DB_NAME');
            F('_fields/'.$db.'.'.$this->name,$this->fields);
        }
    }

    /**
     +----------------------------------------------------------
     * 动态切换扩展模型
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $type 模型类型名称
     * @param mixed $vars 要传入扩展模型的属性变量
     +----------------------------------------------------------
     * @return Model
     +----------------------------------------------------------
     */
    public function switchModel($type,$vars=array()) {
        $class = ucwords(strtolower($type)).'Model';
        if(!class_exists($class))
            throw_exception($class.L('_MODEL_NOT_EXIST_'));
        // 实例化扩展模型
        $this->_extModel   = new $class($this->name);
        if(!empty($vars)) {
            // 传入当前模型的属性到扩展模型
            foreach ($vars as $var)
                $this->_extModel->setProperty($var,$this->$var);
        }
        return $this->_extModel;
    }

    /**
     +----------------------------------------------------------
     * 设置数据对象的值
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $name 名称
     * @param mixed $value 值
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function __set($name,$value) {
        // 设置数据对象属性
        $this->data[$name]  =   $value;
    }

    /**
     +----------------------------------------------------------
     * 获取数据对象的值
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $name 名称
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function __get($name) {
        return isset($this->data[$name])?$this->data[$name]:null;
    }

    /**
     +----------------------------------------------------------
     * 检测数据对象的值
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $name 名称
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    public function __isset($name) {
        return isset($this->data[$name]);
    }

    /**
     +----------------------------------------------------------
     * 销毁数据对象的值
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $name 名称
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function __unset($name) {
        unset($this->data[$name]);
    }

    /**
     +----------------------------------------------------------
     * 利用__call方法实现一些特殊的Model方法
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $method 方法名称
     * @param array $args 调用参数
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function __call($method,$args) {
        if(in_array(strtolower($method),array('table','where','order','limit','page','alias','having','group','lock','distinct'),true)) {
            // 连贯操作的实现
            //start extend special privileges by qchlian
            if(strtolower($method)=="where"){
                if( is_array($args[0] )){
                    if($args[0]['extendwhere']){
                        $extendwhere= $args[0]['extendwhere'];
                        unset($args[0]['extendwhere']);
                        if(count($args[0])>0){
                            $this->options["extendwhere"] = " and ".$extendwhere;
                        }else{
                            $args[0]= $extendwhere;
                        }
                    }
                }
            }
            //end extend special privileges by qchlian
            $this->options[strtolower($method)] =   $args[0];
            return $this;
        }elseif(in_array(strtolower($method),array('count','sum','min','max','avg'),true)){
            // 统计查询的实现
            $field =  isset($args[0])?$args[0]:'*';
            return $this->getField(strtoupper($method).'('.$field.') AS tp_'.$method);
        }elseif(strtolower(substr($method,0,5))=='getby') {
            // 根据某个字段获取记录
            $field   =   parse_name(substr($method,5));
            $where[$field] =  $args[0];
            return $this->where($where)->find();
        }elseif(strtolower(substr($method,0,10))=='getfieldby') {
            // 根据某个字段获取记录的某个值
            $name   =   parse_name(substr($method,10));
            $where[$name] =$args[0];
            return $this->where($where)->getField($args[1]);
        }else{
            throw_exception(__CLASS__.':'.$method.L('_METHOD_NOT_EXIST_'));
            return;
        }
    }
    // 回调方法 初始化模型
    protected function _initialize() {}

    /**
     +----------------------------------------------------------
     * 对保存到数据库的数据进行处理
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param mixed $data 要操作的数据
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
     protected function _facade($data) {
        // 检查非数据字段
        if(!empty($this->fields)) {
            foreach ($data as $key=>$val){
                if(!in_array($key,$this->fields,true)){
                    unset($data[$key]);
                }elseif(C('DB_FIELDTYPE_CHECK') && is_scalar($val)) {
                    // 字段类型检查
                    $this->_parseType($data,$key);
                }
            }
        }
        $this->_before_write($data);
        return $data;
     }

    // 写入数据前的回调方法 包括新增和更新
    protected function _before_write(&$data) {}

    /**
     +----------------------------------------------------------
     * 新增数据
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $data 数据
     * @param array $options 表达式
     * @param boolean $replace 是否replace
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function add($data='',$options=array(),$replace=false) {
        if(empty($data)) {
            // 没有传递数据，获取当前数据对象的值
            if(!empty($this->data)) {
                $data    =   $this->data;
                // 重置数据
                $this->data = array();
            }else{
                $this->error = L('_DATA_TYPE_INVALID_');
                return false;
            }
        }
        // 分析表达式
        $options =  $this->_parseOptions($options);
        // 数据处理
        $data = $this->_facade($data);
        if(false === $this->_before_insert($data,$options)) {
            return false;
        }
        // 写入数据到数据库
        $result = $this->db->insert($data,$options,$replace);
        if(false !== $result ) {
            $insertId   =   $this->getLastInsID();
            if($insertId) {
                // 自增主键返回插入ID
                $data[$this->getPk()]  = $insertId;
                $bool = $this->_after_insert($data,$options);
                if(!$bool){
                	return $bool;
                }
//                 //数据漫游,新增自动插入
//                 $this->dataRoam($this->name,$insertId,1);
                return $insertId;
            }
        }
        return $result;
    }
    // 插入数据前的回调方法
    protected function _before_insert(&$data,$options) {}
    // 插入成功后的回调方法
    protected function _after_insert($data,$options) {
    	 $tabname = $this->getTableName();
    	 $bjbool = true;
    	 if($tabname=="mis_auto_kimpu"){
    	 	//定义data变量，存储post数据
    	 	$data ['projectid'] = $data[$this->getPk()];
    	 	//根据编号获取业务类型的ID
    	 	$MisSystemFlowTypeModel = D("MisSystemFlowType");
    	 	$typeid = $MisSystemFlowTypeModel->where("outlinelevel = 1 and orderno = '".$data['typeid']."'")->getField("id");
    	 	//第一步、进行类型和阶段数据复制
    	 	$where = array();
    	 	$where['id'] = $typeid;
    	 	$where['parentid'] = $typeid;
    	 	$where['_logic'] = 'or';
    	 	//实例化业务类型表
    	 	$flowTypeDate= $MisSystemFlowTypeModel->order("sort,id asc")->where($where)->select();
    	 	$bool = true;
    	 	foreach($flowTypeDate as $k=>$v){
    	 		//注入一个项目ID
    	 		$flowTypeDate[$k]['projectid'] = $data[$this->getPk()];
    	 		if($v['outlinelevel'] == 2 && $bool){
    	 			$flowTypeDate[$k]['complete'] = 2;//默认第一个阶段是进行中。
    	 			$bool = false;
    	 		}
    	 	}
    	 	//实例化项目模型表
    	 	$MisProjectFlowTypeModel = D("MisProjectFlowType");
    	 	//插入项目类型表数据
    	 	$bjbool = $MisProjectFlowTypeModel->addAll($flowTypeDate);
    	 	if(!$bjbool){
    	 		return $bjbool;
    	 	}
    	 	// 获取项目类型
    	 	$MisSystemFlowFormModel = D ( "MisSystemFlowForm" );
    	 	$bjbool = $MisSystemFlowFormModel->intoProject ( $data );
    	 	if(!$bjbool){
    	 		return $bjbool;
    	 	}
    	 }
    	 return $bjbool;
    }

    public function addAll($dataList,$options=array(),$replace=false){
        if(empty($dataList)) {
            $this->error = L('_DATA_TYPE_INVALID_');
            return false;
        }
        // 分析表达式
        $options =  $this->_parseOptions($options);
        // 数据处理
        foreach ($dataList as $key=>$data){
            $dataList[$key] = $this->_facade($data);
        }
        // 写入数据到数据库
        $result = $this->db->insertAll($dataList,$options,$replace);
        if(false !== $result ) {
            $insertId   =   $this->getLastInsID();
            if($insertId) {
                return $insertId;
            }
        }
        return $result;
    }

    /**
     +----------------------------------------------------------
     * 通过Select方式添加记录
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $fields 要插入的数据表字段名
     * @param string $table 要插入的数据表名
     * @param array $options 表达式
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    public function selectAdd($fields='',$table='',$options=array()) {
        // 分析表达式
        $options =  $this->_parseOptions($options);
        // 写入数据到数据库
        if(false === $result = $this->db->selectInsert($fields?$fields:$options['field'],$table?$table:$this->getTableName(),$options)){
            // 数据库插入操作失败
            $this->error = L('_OPERATION_WRONG_');
            return false;
        }else {
            // 插入成功
            return $result;
        }
    }

    /**
     +----------------------------------------------------------
     * 保存数据
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $data 数据
     * @param array $options 表达式
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    public function save($data='',$options=array()) {
        if(empty($data)) {
            // 没有传递数据，获取当前数据对象的值
            if(!empty($this->data)) {
                $data    =   $this->data;
                // 重置数据
                $this->data = array();
            }else{
                $this->error = L('_DATA_TYPE_INVALID_');
                return false;
            }
        }
        // 数据处理
        $data = $this->_facade($data);
        // 分析表达式
        $options =  $this->_parseOptions($options);
        if(false === $this->_before_update($data,$options)) {
            return false;
        }
        if(!isset($options['where']) ) {
            // 如果存在主键数据 则自动作为更新条件
            if(isset($data[$this->getPk()])) {
                $pk   =  $this->getPk();
                $where[$pk]   =  $data[$pk];
                $options['where']  =  $where;
                $pkValue = $data[$pk];
                unset($data[$pk]);
            }else{
                // 如果没有任何更新条件则不执行
                $this->error = L('_OPERATION_WRONG_');
                return false;
            }
        }

        $result = $this->db->update($data,$options);
        if(false !== $result) {
            if(isset($pkValue)) $data[$pk]   =  $pkValue;
            $this->_after_update($data,$options);

            $funNum = 2; //默认为修改操作
            if(ACTION_NAME == "delete"){
            	$funNum = 5;  //如果当前操作方法为删除，则执行3.
            }
            //数据漫游,新增自动插入          
           // $this->dataRoam($this->name,$pkValue,$funNum);
        }
        return $result;
    }
    // 更新数据前的回调方法
    protected function _before_update(&$data,$options) {}
    // 更新成功后的回调方法
    protected function _after_update($data,$options) {}

    /**
     +----------------------------------------------------------
     * 删除数据
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $options 表达式
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function delete($options=array()) {
        if(empty($options) && empty($this->options['where'])) {
            // 如果删除条件为空 则删除当前数据对象所对应的记录
            if(!empty($this->data) && isset($this->data[$this->getPk()]))
                return $this->delete($this->data[$this->getPk()]);
            else
                return false;
        }
        if(is_numeric($options)  || is_string($options)) {
            // 根据主键删除记录
            $pk   =  $this->getPk();
            if(strpos($options,',')) {
                $where[$pk]   =  array('IN', $options);
            }else{
                $where[$pk]   =  $options;
                $pkValue = $options;
            }
            $options =  array();
            $options['where'] =  $where;
        }
        // 分析表达式
        $options =  $this->_parseOptions($options);
        $result=    $this->db->delete($options);
        if(false !== $result) {
            $data = array();
            if(isset($pkValue)) $data[$pk]   =  $pkValue;
          // print_R($options);
            $this->_after_delete($data,$options);
            //数据漫游,新增自动插入
             //$this->dataRoam($this->name,$options,3);

        }
        // 返回删除记录个数
        return $result;
    }
    // 删除成功后的回调方法
    protected function _after_delete($data,$options) {}

    /**
     +----------------------------------------------------------
     * 查询数据集
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $options 表达式参数
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function select($options=array()) {
        if(is_string($options) || is_numeric($options)) {
            // 根据主键查询
            $pk   =  $this->getPk();
            if(strpos($options,',')) {
                $where[$pk] =  array('IN',$options);
            }else{
                $where[$pk]   =  $options;
            }
            $options =  array();
            $options['where'] =  $where;
        }elseif(false === $options){ // 用于子查询 不查询只返回SQL
            $options =  array();
            // 分析表达式
            $options =  $this->_parseOptions($options);
            return  '( '.$this->db->buildSelectSql($options).' )';
        }
        // 分析表达式
        $options =  $this->_parseOptions($options);
        $resultSet = $this->db->select($options,debug_backtrace());
        if(false === $resultSet) {
            return false;
        }
        if(empty($resultSet)) { // 查询结果为空
            return null;
        }
        $this->_after_select($resultSet,$options);
        return $resultSet;
    }
    // 查询成功后的回调方法
    protected function _after_select(&$resultSet,$options) {}

    /**
     +----------------------------------------------------------
     * 生成查询SQL 可用于子查询
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $options 表达式参数
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public function buildSql($options=array()) {
        // 分析表达式
        $options =  $this->_parseOptions($options);
        return  '( '.$this->db->buildSelectSql($options).' )';
    }

    /**
     +----------------------------------------------------------
     * 分析表达式
     +----------------------------------------------------------
     * @access proteced
     +----------------------------------------------------------
     * @param array $options 表达式参数
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     */
    protected function _parseOptions($options=array()) {
        if(is_array($options))
            $options =  array_merge($this->options,$options);
        // 查询过后清空sql表达式组装 避免影响下次查询
        $this->options  =   array();
        if(!isset($options['table']))
            // 自动获取表名
            $options['table'] =$this->getTableName();
        if(!empty($options['alias'])) {
            $options['table']   .= ' '.$options['alias'];
        }
        // 记录操作的模型名称
        $options['model'] =  $this->name;
        // 字段类型验证
        if(C('DB_FIELDTYPE_CHECK')) {
            if(isset($options['where']) && is_array($options['where'])) {
                // 对数组查询条件进行字段类型检查
                foreach ($options['where'] as $key=>$val){
                    $key1 = str_replace("-",".",$key);
                    unset($options['where'][$key]); //删掉原有的键值
                    $key=$key1;
		    $options['where'][$key] = $val; //赋值
                    if(in_array($key,$this->fields,true) && is_scalar($val)){
                       $this->_parseType($options['where'],$key);
                    }
                }
                
            }
        }
        // 表达式过滤
        $this->_options_filter($options);
        $options=$this->_options_extend_filter($options);
        return $options;
    }
    
    protected function _options_extend_filter($options){
        if(is_string($options['where']) && $options['where'] && strpos($options['where'],"1=1")){
            return $options;
        }
//         if(!isset($_SESSION[C('ADMIN_AUTH_KEY')])){
//             $re=$this->db->query("SELECT type,objectid,exacttable,tablename,conditions FROM `mis_authorize_special` where category=1 and status=1");
//             foreach($re as $k=>$v){
//                 //start determine whether an exact match table name
//                 if($v["exacttable"]){
//                     $true = ($v['tablename']==$options['table'])? 1:0;
//                 }else{
//                     $true = ($v['tablename']==$options['table'])? 1:0;
//                     if(!$true){
//                         $exites= explode($v['tablename']." ",$options['table']);
//                         $true = count($exites)>1 ? 1:0;
//                     }
//                 }
//                 //end determine whether an exact match table name
//                 if( $true ) {
//                     $check=false;
//                     //start determine current user whether in the special privileges by filter user
//                     if($v['type']==0){
//                         $useridarr=explode(",",$v['objectid']);
//                         if(in_array($_SESSION[C('USER_AUTH_KEY')],$useridarr)){
//                             $check=true;
//                         }
//                     }else{
//                         $re2=$this->db->query("SELECT user_id FROM `rolegroup_user` where rolegroup_id in(".$v['objectid'].")");
//                         foreach($re2 as $k2=>$v2){
//                             if($v2['user_id']==$_SESSION[C('USER_AUTH_KEY')]){
//                                 $check=true;
//                                 break;
//                             }
//                         }
//                     }
//                     //end determine current user whether in the special privileges by filter user
//                     if($check){//start do the filter
//                         if(is_array($options['where']) && $options['where']){
//                             $options['extendwhere'].=" and ".$v['conditions']; 
//                         }else if(is_string($options['where']) && $options['where']){
//                             $options['where'].=" and ".$v['conditions'];
//                         }else{
//                             $options['where']=$v['conditions'];
//                         }
//                     }
//                 }
//             }
//         }
        return $options;
    }
    
    // 表达式过滤回调方法
    protected function _options_filter(&$options) {}

    /**
     +----------------------------------------------------------
     * 数据类型检测
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param mixed $data 数据
     * @param string $key 字段名
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    protected function _parseType(&$data,$key) {
        $fieldType = strtolower($this->fields['_type'][$key]);
        if(false === strpos($fieldType,'bigint') && false !== strpos($fieldType,'int')) {
            $data[$key]   =  intval($data[$key]);       
        }elseif(false !== strpos($fieldType,'float') || false !== strpos($fieldType,'double')){
            $data[$key]   =  floatval($data[$key]);      	
        }elseif(false !== strpos($fieldType,'bool')){
            $data[$key]   =  (bool)$data[$key];
        }
    }

    /**
     +----------------------------------------------------------
     * 查询数据
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $options 表达式参数
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function find($options=array()) {
        if(is_numeric($options) || is_string($options)) {
            $where[$this->getPk()] =$options;
            $options = array();
            $options['where'] = $where;
        }
        // 总是查找一条记录
        $options['limit'] = 1;
        // 分析表达式
        $options =  $this->_parseOptions($options);
        $resultSet = $this->db->select($options,debug_backtrace());
        if(false === $resultSet) {
            return false;
        }
        if(empty($resultSet)) {// 查询结果为空
            return null;
        }
        $this->data = $resultSet[0];
        $this->_after_find($this->data,$options);
        return $this->data;
    }
	// 查询成功的回调方法
    protected function _after_find(&$result,$options) {
		$this->fieldUnset($result); 	
    }
	/**
	 * @Title: fieldUnset
	 * @Description: todo(这里用一句话描述这个方法的作用) 
	 * @param 查询后的结构数组 $result  
	 * @author 黎明刚
	 * @date 2015年2月4日 下午5:53:10 
	 * @throws
	 */
    protected function fieldUnset(&$result){
    	if(is_array($this->_filter) && count($this->_filter)){
    		foreach($this->_filter as $key=>$val){
    			if($key == "orderno" || $key == "id"){
    				continue;
    			}
    			if($val[1] == 'filterReadSetEmpty'){
    				//不可查看字段，及需要排除查询出来的结果集
    				unset($result[$key]);
    			}
    		}
    		//$this->_filter = array();
    	}
    }

    /**
     +----------------------------------------------------------
     * 处理字段映射
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $data 当前数据
     * @param integer $type 类型 0 写入 1 读取
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     */
    public function parseFieldsMap($data,$type=1) {
        // 检查字段映射
        if(!empty($this->_map)) {
            foreach ($this->_map as $key=>$val){
                if($type==1) { // 读取
                    if(isset($data[$val])) {
                        $data[$key] =   $data[$val];
                        unset($data[$val]);
                    }
                }else{
                    if(isset($data[$key])) {
                        $data[$val] =   $data[$key];
                        unset($data[$key]);
                    }
                }
            }
        }
        return $data;
    }

    /**
     +----------------------------------------------------------
     * 设置记录的某个字段值
     * 支持使用数据库字段和方法
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string|array $field  字段名
     * @param string $value  字段值
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    public function setField($field,$value='') {
        if(is_array($field)) {
            $data = $field;
        }else{
            $data[$field]   =  $value;
        }
        return $this->save($data);
    }

    /**
     +----------------------------------------------------------
     * 字段值增长
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $field  字段名
     * @param integer $step  增长值
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    public function setInc($field,$step=1) {
        return $this->setField($field,array('exp',$field.'+'.$step));
    }

    /**
     +----------------------------------------------------------
     * 字段值减少
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $field  字段名
     * @param integer $step  减少值
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    public function setDec($field,$step=1) {
        return $this->setField($field,array('exp',$field.'-'.$step));
    }

    /**
     +----------------------------------------------------------
     * 获取一条记录的某个字段值
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $field  字段名
     * @param string $spea  字段数据间隔符号 NULL返回数组
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function getField($field,$sepa=null) {
        $options['field']       =   $field;
        $options                =   $this->_parseOptions($options);
        $field                  =   trim($field);
        if(strpos($field,',')) { // 多字段
            $options['limit']   =   is_numeric($sepa)?$sepa:'';
            $resultSet          =   $this->db->select($options,debug_backtrace());
            if(!empty($resultSet)) {
                $_field         =   explode(',', $field);
                $field          =   array_keys($resultSet[0]);
                $key            =   array_shift($field);
                $key2           =   array_shift($field);
                $cols           =   array();
                $count          =   count($_field);
                foreach ($resultSet as $result){
                    $name   =  $result[$key];
                    if(2==$count) {
                        $cols[$name]   =  $result[$key2];
                    }else{
                        $cols[$name]   =  is_string($sepa)?implode($sepa,array_slice($result,1)):$result;
                    }
                }
                return $cols;
            }
        }else{   // 查找一条记录
            // 返回数据个数
            if(true !== $sepa) {// 当sepa指定为true的时候 返回所有数据
                $options['limit']   =   is_numeric($sepa)?$sepa:1;
            }
            $result = $this->db->select($options,debug_backtrace());
            if(!empty($result)) {
                if(true !== $sepa && 1==$options['limit']) return reset($result[0]);
                foreach ($result as $val){
                    $array[]    =   $val[$field];
                }
                return $array;
            }
        }
        return null;
    }

    /**
     +----------------------------------------------------------
     * 创建数据对象 但不保存到数据库
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $data 创建数据
     * @param string $type 状态
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
     public function create($data='',$type='') {
        // 如果没有传值默认取POST数据
        if(empty($data)) {
            $data    =   $_POST;
        }elseif(is_object($data)){
            $data   =   get_object_vars($data);
        }
        // 验证数据
        if(empty($data) || !is_array($data)) {
            $this->error = L('_DATA_TYPE_INVALID_');
            return false;
        }

        // 检查字段映射
        $data = $this->parseFieldsMap($data,0);

        // 状态
        $type = $type?$type:(!empty($data[$this->getPk()])?self::MODEL_UPDATE:self::MODEL_INSERT);

        // 检测提交字段的合法性
        if(isset($this->options['field'])) { // $this->field('field1,field2...')->create()
            $fields =   $this->options['field'];
            unset($this->options['field']);
        }elseif($type == self::MODEL_INSERT && isset($this->insertFields)) {
            $fields =   $this->insertFields;
        }elseif($type == self::MODEL_UPDATE && isset($this->updateFields)) {
            $fields =   $this->updateFields;
        }
        
        if(isset($fields)) {
            if(is_string($fields)) {
                $fields =   explode(',',$fields);
            }
            // 判断令牌验证字段
            if(C('TOKEN_ON'))   $fields[] = C('TOKEN_NAME');
            foreach ($data as $key=>$val){
                if(!in_array($key,$fields)) {
                    unset($data[$key]);
                }
            }
        }
        // 数据自动验证
        if(!$this->autoValidation($data,$type)) return false;

        // 表单令牌验证
        if(C('TOKEN_ON') && !$this->autoCheckToken($data)) {
            $this->error = L('_TOKEN_ERROR_');
            return false;
        }
        // 验证完成生成数据对象
        if($this->autoCheckFields) { // 开启字段检测 则过滤非法字段数据
            $vo   =  array();
            foreach ($this->fields as $key=>$name){
                if(substr($key,0,1)=='_') continue;
                $val = isset($data[$name])?$data[$name]:null;
                //保证赋值有效
                if(!is_null($val)){
                    $vo[$name] = (MAGIC_QUOTES_GPC && is_string($val))?   stripslashes($val)  :  $val;
                }
            }
        }else{
            $vo   =  $data;
        }
        // 创建完成对数据进行自动处理
        $this->autoOperation($vo,$type);
        
        logs("----------1---------".arr2string($vo), MODULE_NAME.'_list_data_'.date('Y-m-d-H' , time()) ,'',__CLASS__,__FUNCTION__,__METHOD__);
        logs(ACTION_NAME."----------1---------".arr2string($this->_filter), MODULE_NAME.'_filter_list_data_'.date('Y-m-d-H' , time()) ,'',__CLASS__,__FUNCTION__,__METHOD__);
        // 数据字段添加条件过滤,(加入了新增和修改页面模板控制，所有过滤掉新增和启动流程底层控制) add by nbmxkj at 20150128 1943
        if(ACTION_NAME == 'insert' || ACTION_NAME == 'startprocess' || ACTION_NAME == 'lookupUpdateProcess'){
        	$this->_filter = array();
        }else{
        	if(!empty($this->_filter)) {
        		foreach ($this->_filter as $field=>$filter){
        			if($this->getPk() == $field){
        				//排除主键过滤
        				continue;
        			}
        			if(is_array($filter)){
        				if($filter[0]=='filterWritSetEmpty' || $filter[1] == 'filterReadSetEmpty'){
        					unset($vo[$field]);
        				}
        			}
        		}
        		$this->_filter = array();
        	}
        }
        logs(ACTION_NAME."----------2---------".arr2string($this->_filter), MODULE_NAME.'_filter_list_data_'.date('Y-m-d-H' , time()) ,'',__CLASS__,__FUNCTION__,__METHOD__);
        logs("----------2---------".arr2string($vo), MODULE_NAME.'_list_data_'.date('Y-m-d-H' , time()) ,'',__CLASS__,__FUNCTION__,__METHOD__);
        // 赋值当前数据对象
        $this->data =   $vo;
        // 返回创建的数据以供其他调用
        return $vo;
     }

    // 自动表单令牌验证
    // TODO  ajax无刷新多次提交暂不能满足
    public function autoCheckToken($data) {
        if(C('TOKEN_ON')){
            $name   = C('TOKEN_NAME');
            if(!isset($data[$name]) || !isset($_SESSION[$name])) { // 令牌数据无效
                return false;
            }

            // 令牌验证
            list($key,$value)  =  explode('_',$data[$name]);
            if( !isset($_SESSION[$name][$key]) )  return true;            
            if($value && $_SESSION[$name][$key] === $value) { // 防止重复提交
                unset($_SESSION[$name][$key]); // 验证完成销毁session
                return true;
            }
            // 开启TOKEN重置
            if(C('TOKEN_RESET')) unset($_SESSION[$name][$key]);
            return false;
        }
        return true;
    }

    /**
     +----------------------------------------------------------
     * 使用正则验证数据
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $value  要验证的数据
     * @param string $rule 验证规则
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    public function regex($value,$rule) {
        $validate = array(
            'require'=> '/.+/',
            'email' => '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
            'url' => '/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/',
            'currency' => '/^\d+(\.\d+)?$/',
            'number' => '/^\d+$/',
            'zip' => '/^[1-9]\d{5}$/',
            'integer' => '/^[-\+]?\d+$/',
            'double' => '/^[-\+]?\d+(\.\d+)?$/',
            'english' => '/^[A-Za-z]+$/',
        );
        // 检查是否有内置的正则表达式
        if(isset($validate[strtolower($rule)]))
            $rule   =   $validate[strtolower($rule)];
        return preg_match($rule,$value)===1;
    }

    /**
     +----------------------------------------------------------
     * 自动表单处理
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $data 创建数据
     * @param string $type 创建类型
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    private function autoOperation(&$data,$type) {
        // 自动填充
        //print_r($_POST);
        //print_r($data);
        if(!empty($this->_auto)) {
            foreach ($this->_auto as $auto){
            	//定义特殊函数在值恒等于空时，不执行任何操作
//             	foreach($this->_autoFuncCheck as $key=>$val){
//             		$sign=false;
//             		if($auto[1]==$val){
//             			if(!$_POST[$auto[0]])continue;
//             			echo 123;
//             		}
//             	}
            	if(in_array($auto[1],$this->_autoFuncCheck)){
            		if($data[$auto[0]]===null){
            			continue;
            		}
            	}
                // 填充因子定义格式
                // array('field','填充内容','填充条件','附加规则',[额外参数])
                if(empty($auto[2])) $auto[2] = self::MODEL_INSERT; // 默认为新增的时候自动填充
                if( $type == $auto[2] || $auto[2] == self::MODEL_BOTH) {
                    switch($auto[3]) {
                        case 'function':    //  使用函数进行填充 字段的值作为参数
                        case 'callback': // 使用回调方法
                            $args = isset($auto[4])?(array)$auto[4]:array();
                            if(isset($data[$auto[0]])) {
                                array_unshift($args,$data[$auto[0]]);
                            }
                            //by qchlian 解决回调函数传参bug
                            if($args && $auto[4]){
                                foreach($args as $k1=>$v1){ 
                                    eval("\$v1 = \"$v1\";");
                                   $args[$k1]=$v1;
                                }
                            }//end by qchlian
                            
                            if('function'==$auto[3]) {
                                $data[$auto[0]]  = call_user_func_array($auto[1], $args);
                            }else{
                                $data[$auto[0]]  =  call_user_func_array(array(&$this,$auto[1]), $args);
                            }
                            break;
                        case 'field':    // 用其它字段的值进行填充
                            $data[$auto[0]] = $data[$auto[1]];
                            break;
                        case 'string':
                        default: // 默认作为字符串填充
                            $data[$auto[0]] = $auto[1];
                    }
                    if(false === $data[$auto[0]] )   unset($data[$auto[0]]);
                }
            }
        }
        //print_r($data);
        return $data;
    }

    /**
     +----------------------------------------------------------
     * 自动表单验证
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param array $data 创建数据
     * @param string $type 创建类型
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    protected function autoValidation($data,$type) {
        // 属性验证
        if(!empty($this->_validate)) { // 如果设置了数据自动验证则进行数据验证
            if($this->patchValidate) { // 重置验证错误信息
                $this->error = array();
            }
            foreach($this->_validate as $key=>$val) {
                // 验证因子定义格式
                // array(field,rule,message,condition,type,when,params)
                // 判断是否需要执行验证
                if(empty($val[5]) || $val[5]== self::MODEL_BOTH || $val[5]== $type ) {
                    if(0==strpos($val[2],'{%') && strpos($val[2],'}'))
                        // 支持提示信息的多语言 使用 {%语言定义} 方式
                        $val[2]  =  L(substr($val[2],2,-1));
                    $val[3]  =  isset($val[3])?$val[3]:self::EXISTS_VAILIDATE;
                    $val[4]  =  isset($val[4])?$val[4]:'regex';
                    // 判断验证条件
                    switch($val[3]) {
                        case self::MUST_VALIDATE:   // 必须验证 不管表单是否有设置该字段
                            if(false === $this->_validationField($data,$val)) 
                                return false;
                            break;
                        case self::VALUE_VAILIDATE:    // 值不为空的时候才验证
                            if('' != trim($data[$val[0]]))
                                if(false === $this->_validationField($data,$val)) 
                                    return false;
                            break;
                        default:    // 默认表单存在该字段就验证
                            if(isset($data[$val[0]]))
                                if(false === $this->_validationField($data,$val)) 
                                    return false;
                    }
                }
            }
            // 批量验证的时候最后返回错误
            if(!empty($this->error)) return false;
        }
        return true;
    }

    /**
     +----------------------------------------------------------
     * 验证表单字段 支持批量验证
     * 如果批量验证返回错误的数组信息
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param array $data 创建数据
     * @param array $val 验证因子
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    protected function _validationField($data,$val) {
        if(false === $this->_validationFieldItem($data,$val)){
            if($this->patchValidate) {
                $this->error[$val[0]]  =  $val[2];
            }else{
                $this->error    =   $val[2];
                return false;
            }
        }
        return ;
    }

    /**
     +----------------------------------------------------------
     * 根据验证因子验证字段
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param array $data 创建数据
     * @param array $val 验证因子
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    protected function _validationFieldItem($data,$val) {
        switch($val[4]) {
            case 'function':// 使用函数进行验证
            case 'callback':// 调用方法进行验证
                $args = isset($val[6])?(array)$val[6]:array();
                array_unshift($args,$data[$val[0]]);
                 //by qchlian 解决回调函数传参bug
                if($args){
                    foreach($args as $k1=>$v1){ 
                        eval("\$v1 = \"$v1\";");
                       $args[$k1]=$v1;
                    }
                }//end by qchlian
                if('function'==$val[4]) {
                    return call_user_func_array($val[1], $args);
                }else{
                    return call_user_func_array(array(&$this, $val[1]), $args);
                }
            case 'confirm': // 验证两个字段是否相同
                return $data[$val[0]] == $data[$val[1]];
            case 'unique': // 验证某个值是否唯一
                if(is_string($val[0]) && strpos($val[0],','))
                    $val[0]  =  explode(',',$val[0]);
                $map = array();
                if(is_array($val[0])) {
                    // 支持多个字段验证
                    foreach ($val[0] as $field){
                        if($field=="status"){
                            $map[$field]   = array("gt",-1);
                        }else{
                            $map[$field]   =  $data[$field];
                        }
                    }
                }else{
                    $map[$val[0]] = $data[$val[0]];
                }
                if(!empty($data[$this->getPk()])) { // 完善编辑的时候验证唯一
                    $map[$this->getPk()] = array('neq',$data[$this->getPk()]);
                }
                if($this->field($this->getPk())->where($map)->find())   return false;
                return true;
            default:  // 检查附加规则
                return $this->check($data[$val[0]],$val[1],$val[4]);
        }
    }

    /**
     +----------------------------------------------------------
     * 验证数据 支持 in between equal length regex expire ip_allow ip_deny
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $value 验证数据
     * @param mixed $rule 验证表达式
     * @param string $type 验证方式 默认为正则验证
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    public function check($value,$rule,$type='regex'){
        switch(strtolower($type)) {
            case 'in': // 验证是否在某个指定范围之内 逗号分隔字符串或者数组
                $range   = is_array($rule)?$rule:explode(',',$rule);
                return in_array($value ,$range);
            case 'between': // 验证是否在某个范围
                list($min,$max)   =  explode(',',$rule);
                return $value>=$min && $value<=$max;
            case 'equal': // 验证是否等于某个值
                return $value == $rule;
            case 'length': // 验证长度
                $length  =  mb_strlen($value,'utf-8'); // 当前数据长度
                if(strpos($rule,',')) { // 长度区间
                    list($min,$max)   =  explode(',',$rule);
                    return $length >= $min && $length <= $max;
                }else{// 指定长度
                    return $length == $rule;
                }
            case 'expire':
                list($start,$end)   =  explode(',',$rule);
                if(!is_numeric($start)) $start   =  strtotime($start);
                if(!is_numeric($end)) $end   =  strtotime($end);
                return $_SERVER['REQUEST_TIME'] >= $start && $_SERVER['REQUEST_TIME'] <= $end;
            case 'ip_allow': // IP 操作许可验证
                return in_array(get_client_ip(),explode(',',$rule));
            case 'ip_deny': // IP 操作禁止验证
                return !in_array(get_client_ip(),explode(',',$rule));
            case 'regex':
            default:    // 默认使用正则验证 可以使用验证类中定义的验证名称
                // 检查附加规则
                return $this->regex($value,$rule);
        }
    }

    /**
     +----------------------------------------------------------
     * SQL查询
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $sql  SQL指令
     * @param boolean $parse  是否需要解析SQL
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function query($sql,$parse=false) {
        $sql  =   $this->parseSql($sql,$parse);
        return $this->db->query($sql);
    }

    /**
     +----------------------------------------------------------
     * 执行SQL语句
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $sql  SQL指令
     * @param boolean $parse  是否需要解析SQL
     +----------------------------------------------------------
     * @return false | integer
     +----------------------------------------------------------
     */
    public function execute($sql,$parse=false) {
        $sql  =   $this->parseSql($sql,$parse);
        return $this->db->execute($sql);
    }

    /**
     +----------------------------------------------------------
     * 解析SQL语句
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $sql  SQL指令
     * @param boolean $parse  是否需要解析SQL
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    protected function parseSql($sql,$parse) {
        // 分析表达式
        if($parse) {
            $options =  $this->_parseOptions();
            $sql  =   $this->db->parseSql($sql,$options);
        }else{
            if(strpos($sql,'__TABLE__'))
                $sql    =   str_replace('__TABLE__',$this->getTableName(),$sql);
        }
        $this->db->setModel($this->name);
        return $sql;
    }

    /**
     +----------------------------------------------------------
     * 切换当前的数据库连接
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param integer $linkNum  连接序号
     * @param mixed $config  数据库连接信息
     * @param array $params  模型参数
     +----------------------------------------------------------
     * @return Model
     +----------------------------------------------------------
     */
    public function db($linkNum,$config='',$params=array()){
        static $_db = array();
        if(!isset($_db[$linkNum])) {
            // 创建一个新的实例
            if(!empty($config) && is_string($config) && false === strpos($config,'/')) { // 支持读取配置参数
                $config  =  C($config);
            }
            $_db[$linkNum]            =    Db::getInstance($config);
        }elseif(NULL === $config){
            $_db[$linkNum]->close(); // 关闭数据库连接
            unset($_db[$linkNum]);
            return ;
        }
        if(!empty($params)) {
            if(is_string($params))    parse_str($params,$params);
            foreach ($params as $name=>$value){
                $this->setProperty($name,$value);
            }
        }
        // 切换数据库连接
        $this->db   =    $_db[$linkNum];
        $this->_after_db();
        // 字段检测
        if(!empty($this->name) && $this->autoCheckFields)    $this->_checkTableInfo();
        return $this;
    }
    // 数据库切换后回调方法
    protected function _after_db() {}

    /**
     +----------------------------------------------------------
     * 得到当前的数据对象名称
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public function getModelName() {
        if(empty($this->name))
            $this->name =   substr(get_class($this),0,-5);
        return $this->name;
    }

    /**
     +----------------------------------------------------------
     * 得到完整的数据表名
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public function getTableName() {
        if(empty($this->trueTableName)) {
            $tableName  = !empty($this->tablePrefix) ? $this->tablePrefix : '';
            if(!empty($this->tableName)) {
                $tableName .= $this->tableName;
            }else{
                $tableName .= parse_name($this->name,$this->tabletype);
            }
            $this->trueTableName    =   strtolower($tableName);
        }
        return (!empty($this->dbName)?$this->dbName.'.':'').$this->trueTableName;
    }

    /**
     +----------------------------------------------------------
     * 启动事务
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function startTrans() {
        $this->commit();
        $this->db->startTrans();
        return ;
    }

    /**
     +----------------------------------------------------------
     * 提交事务
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    public function commit() {
        return $this->db->commit();
    }

    /**
     +----------------------------------------------------------
     * 事务回滚
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    public function rollback() {
        return $this->db->rollback();
    }

    /**
     +----------------------------------------------------------
     * 返回模型的错误信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public function getError(){
        return $this->error;
    }

    /**
     +----------------------------------------------------------
     * 返回数据库的错误信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public function getDbError() {
        return $this->db->getError();
    }

    /**
     +----------------------------------------------------------
     * 返回最后插入的ID
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public function getLastInsID() {
        return $this->db->getLastInsID();
    }

    /**
     +----------------------------------------------------------
     * 返回最后执行的sql语句
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public function getLastSql() {
        return $this->db->getLastSql($this->name);
    }
    // 鉴于getLastSql比较常用 增加_sql 别名
    public function _sql(){
        return $this->getLastSql();
    }

    /**
     +----------------------------------------------------------
     * 获取主键名称
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public function getPk() {
        return isset($this->fields['_pk'])?$this->fields['_pk']:$this->pk;
    }

    /**
     +----------------------------------------------------------
     * 获取数据表字段信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     */
    public function getDbFields(){
        if($this->fields) {
            $fields   =  $this->fields;
            unset($fields['_autoinc'],$fields['_pk'],$fields['_type']);
            return $fields;
        }
        return false;
    }

    /**
     +----------------------------------------------------------
     * 设置数据对象值
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $data 数据
     +----------------------------------------------------------
     * @return Model
     +----------------------------------------------------------
     */
    public function data($data){
        if(is_object($data)){
            $data   =   get_object_vars($data);
        }elseif(is_string($data)){
            parse_str($data,$data);
        }elseif(!is_array($data)){
            throw_exception(L('_DATA_TYPE_INVALID_'));
        }
        $this->data = $data;
        return $this;
    }

    /**
     +----------------------------------------------------------
     * 查询SQL组装 join
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $join
     +----------------------------------------------------------
     * @return Model
     +----------------------------------------------------------
     */
    public function join($join) {
        if(is_array($join)) {
            $this->options['join'] =  $join;
        }elseif(!empty($join)) {
            $this->options['join'][]  =   $join;
        }
        return $this;
    }

    /**
     +----------------------------------------------------------
     * 查询SQL组装 union
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $union
     * @param boolean $all
     +----------------------------------------------------------
     * @return Model
     +----------------------------------------------------------
     */
    public function union($union,$all=false) {
        if(empty($union)) return $this;
        if($all) {
            $this->options['union']['_all']  =   true;
        }
        if(is_object($union)) {
            $union   =  get_object_vars($union);
        }
        // 转换union表达式
        if(is_string($union) ) {
            $options =  $union;
        }elseif(is_array($union)){
            if(isset($union[0])) {
                $this->options['union']  =  array_merge($this->options['union'],$union);
                return $this;
            }else{
                $options =  $union;
            }
        }else{
            throw_exception(L('_DATA_TYPE_INVALID_'));
        }
        $this->options['union'][]  =   $options;
        return $this;
    }

    /**
     +----------------------------------------------------------
     * 查询缓存
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $key
     * @param integer $expire
     * @param string $type
     +----------------------------------------------------------
     * @return Model
     +----------------------------------------------------------
     */
    public function cache($key=true,$expire='',$type=''){
        $this->options['cache']  =  array('key'=>$key,'expire'=>$expire,'type'=>$type);
        return $this;
    }

    /**
     +----------------------------------------------------------
     * 指定查询字段 支持字段排除
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $field
     * @param boolean $except 是否排除
     +----------------------------------------------------------
     * @return Model
     +----------------------------------------------------------
     */
    public function field($field,$except=false){
        if(true === $field) {// 获取全部字段
            $fields   =  $this->getDbFields();
            $field =  $fields?$fields:'*';
        }elseif($except) {// 字段排除
            if(is_string($field)) {
                $field =  explode(',',$field);
            }
            $fields   =  $this->getDbFields();
            $field =  $fields?array_diff($fields,$field):$field;
        }
        $this->options['field']   =   $field;
        return $this;
    }

    /**
     +----------------------------------------------------------
     * 设置模型的属性值
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $name 名称
     * @param mixed $value 值
     +----------------------------------------------------------
     * @return Model
     +----------------------------------------------------------
     */
    public function setProperty($name,$value) {
        if(property_exists($this,$name))
            $this->$name = $value;
        return $this;
    }

//     /**
//      * @Title: dataRoam
//      * @Description: todo(数据漫游通用方法，新增时、修改时、删除时进行数据漫游)
//      * @param 当前执行数据的模型 $modelname
//      * @param 执行模型的数据 $pkValue
//      * @param 执行的何种操作 $sourcetype
//      * @param bool $do
//      * @param bool $debug 是否调试模型
//      * @param bool $dataromaDebug  漫游调试
//      * @author yangxi
//      * @date 2015-1-15 上午11:13:47
//      * @throws
//      */
//     public function dataRoam($modelname,$pkValue,$sourcetype,$do=1,$debug,$dataromaDebug) {
//     	//如果为调试模式跳过数据漫游.数据漫游调试模式直接到数据漫游内部
//     	$_REQUEST["debug"]?$debug=$_REQUEST["debug"]:$debug=false;
//     	$_REQUEST["dataromaDebug"]?$dataromaDebug=$_REQUEST["dataromaDebug"]:$dataromaDebug=false;
//     	if($dataromaDebug===true){
//     		$debug=false;
//     	}
//     	if($debug===false){
//     		//数据漫游,新增自动插入
//     		$msdrModel=D('MisSystemDataRoaming');
//     		$msdrModel->dataRoam($modelname,$pkValue,$sourcetype,$do,$dataromaDebug);
//     	}
//     }    
}