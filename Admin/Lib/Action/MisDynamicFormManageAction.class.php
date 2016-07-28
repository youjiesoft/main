<?php
/**
 * @Title: file_name
 * @Package package_name
 * @Description: todo(动态表单配置控制器)
 * @author liminggang
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2013-6-1 上午9:45:04
 * @version V1.0
 */

/**
 * 文件分离。
 * 分离为	1 模板生成
 * 			2 action生成
 * 			3 model生成
 *
 */
class MisDynamicFormManageAction extends MisDynamicFormTemplateAction {

	/**
	 * 系统字段的详细信息
	 * @var array
	 */
	private $systemUserFieldInfo;

	/**
	 * 数据库关键词
	 * @var array
	 */
	private $dbsystemfield;
	
	/**
	 * 系统字段组件化的配件，用于系统字段组件的属性设置。
	 * @var unknown
	 */
	private $system_field_config ;
	
	/**
	 * 建模操作方式
	 * @var array
	 */
	private $operate;
	/**
	 * 允许的模板生成类型
	 * @var array
	 */
	private $typEnum;
	/**
	 * 审批支持的模板
	 * @var array
	 */
	private $auditTpl;
	private $rels;
	/**
	 * 建表公共字段 在control中配置
	 * @var array
	 */
	private $systefield;
	
	/**
	 * 做数据替换时的来源数据
	 * @var unknown
	 */
	private $replaceSouceArr;
	
	/**
	 * 是否为复用表
	 * @var boolean
	 */
	private $isforeignfield;
	/**
	 * 来源表的formid
	 * @var int
	 */
	private $souceformid;
	/**
	 * 来源表的action名称
	 * @var string
	 */
	private $souceaction;
	/****************************/
	function __construct() {
		parent::__construct();
		$this->operate=array(
				'a'=>'add',	// 新增
				'b'=>'edit', // 修改
				'c'=>'setindextpl', // 首页布局页面
		);
		$this->rels=array(
				'basisarchivestpl'=>array('basisarchivestpl','basisarchivesaudittpl'),
				'commontpl'=>array('audittpl','noaudittpl'),
				'zuhetpl'=>array('zuhetpl'),
				'zczuhetpl'=>array('zczuhetpl'),// 主从
		);
		$this->auditTpl = array(
				'basisarchivesaudittpl'	=>	true,
				'audittpl'		=>	true,
		);
		$this->typEnum=array(
				'commontpl'=>'commontpl', // 普通 模板
				'basisarchivestpl'=>'basisarchivestpl', // 基础档案模板
				'audittpl'=>'audittpl', // 审批流模板
				'noaudittpl'=>'noaudittpl', // 不带审批流模板
				'zuhetpl'=>'zuhetpl', // 表单组合
				'zczuhetpl'=>'zczuhetpl',// 主从
		);
		// $dir = ROOT . '/Conf/';
		$dir = CONF_PATH;
		//$np='DynamicForm/';
		$np='';
		$controlls =require $dir . $np.'controlls.php';
		require $dir .$np. 'property.php';
		$privateProperty = array();
		
		foreach ($controlls as $key=>$val){
			$controlls[$key] = array_merge($NBM_TEMPLATE , $val );
		}
		$this -> controlConfig = $controlls;
		

		$this->tpltypeArr = $NBM_TPL_TYPE;
		$this->systefield = $SYSTEM_FIELD;
		$this->system_field_config = $NBM_SYSTEM_FIELD_CONFIG;
		//  数据库关键词
		$this->dbsystemfield = $NBM_DBSYSTEMFIELD;
		
		foreach ($controlls as $key => $val) {
			if ($val['property']) {
				//				$privateProperty[$key] =$val['property']; // 将公有属性合并到每个组件上
				$privateProperty[$key] = array_merge($NBM_COMMON_PROPERTY, $val['property']);
			}else{
				$privateProperty[$key] = $NBM_COMMON_PROPERTY;
			}
			
			
			if ($val['show']) {
				$temp = '';
				$temp['iscreate'] = $val['iscreate'];
				//是否生成到数据库中字段
				$temp['isline'] = $val['isline'];
				// 一个标签是否占一行
				$temp['title'] = $val['title'];
				$title = $val['title'];
				//javascript:$(this).parent().parent().remove();
				// onclick="delControll(this)"
				$delTag = htmlspecialchars('<a class="nnbm_delete_plain_ctl" ></a>');
				$curTagHtml = htmlspecialchars($val['html']);
				//$isline = "isline=\"" . ($val['isline'] ? 1 : 0) . "\"";
				// 响应式布局的组件容器样式。
				$isline= "colcls=\"col_{$val['property']['titlepercent']['default']}_{$val['property']['contentpercent']['default']} form_group_lay\"";
				
				// 当前组件html结构
				eval("\$curTagHtml = \"$curTagHtml\";");
				$temp['html'] = htmlspecialchars_decode($curTagHtml);
				$this -> controls[] = $temp;
			}
		}

		/* look up */
		if (!is_array($this -> lookupConfig)) {
			
			/**
			 * 修改开始  
			 */
			$lookupMode = D('LookupObj');
			$confFile = $lookupMode->GetConflistFile();
			$conflist = array();
			if(file_exists($confFile)){
				$conflist = require $confFile;
			}			
			$lookupTemp = array();
			foreach($conflist as $k=>$v){
				$path = $lookupMode->GetDetailsPath();
				if(file_exists($path.'/'.$k.'.php')){
					$tempconf = require $path.'/'.$k.'.php';
					if($tempconf['status']){
						$lookupTemp[$k] = $tempconf;
					}
					
				}				
			}
			unset($temp);
			foreach ($lookupTemp as $k => $v) {
				if ($v['status']) {
					$t['title'] = $v['title'];
					$t['fields'] = $v['fields'];
					$t['fieldbackchina'] = implode(',',$v['fields_china']);
					$t['url'] = $v['url'];
					$t['mode'] = $v['mode'];
					$t['checkformodel']=$v['checkformodel'];
					$t['filed']=$v['filed'];
					$t['filed1']=$v['filed1'];
					$t['val']=$v['val'];
					$t['condition']=$v['condition'];
					$t['checkforfields'] = $v['checkforfields'];
					$t['viewname'] = $v['viewname'];
					$t['viewtype'] = $v['viewtype'];
					$t['dialogwidth'] = $v['dialogwidth'];					
					$t['dialogheight'] = $v['dialogheight'];
					$temp["{$k}"] = $t;
				}
			}
			if (count($temp)) {
				$this -> lookupConfig = $temp;
			}
			//dump($temp);
			/**
			 * 修改终止
			 */
// 			$lookupMode = D('LookupObj');
// 			$lookupPath = $lookupMode -> GetFile();
// 			if (file_exists($lookupPath)) {
// 				$lookupTemp = require_once ($lookupPath);
// 				unset($temp);
// 				foreach ($lookupTemp as $k => $v) {
// 					if ($v['status']) {
// 						$t['title'] = $v['title'];
// 						$t['fields'] = $v['fields'];
// 						$t['url'] = $v['url'];
// 						$t['mode'] = $v['mode'];
// 						$t['checkformodel']=$v['checkformodel'];
// 						$t['filed']=$v['filed'];
// 						$t['filed1']=$v['filed1'];
// 						$t['val']=$v['val'];
// 						$t['condition']=$v['condition'];
// 						$t['checkforfields'] = $v['checkforfields'];
// 						$t['viewname'] = $v['viewname'];
// 						$t['viewtype'] = $v['viewtype'];
// 						$temp["{$k}"] = $t;
// 					}
// 				}
// 				if (count($temp)) {
// 					$this -> lookupConfig = $temp;
// 				}
// 			}
		}
		/*  look up end */
		/* checkfor  */
		if (!is_array($this -> checkforConfig)) {
			$checkfoMode = D('CheckForObj');
			$checkforPath = $checkfoMode -> GetFile();
			if (file_exists($checkforPath)) {
				$checkforTemp =
				require_once ($checkforPath);
				unset($temp);
				foreach ($checkforTemp as $k => $v) {
					if ($v['status']) {
						$t['title']=$v['title'];
						$t['model']=$v['model'];
						$t['query_table']=$v['query_table'];
						$t['show_fields']=$v['show_fields'];
						$t['hidden_field']=$v['hidden_field'];
						$t['filter_condition']=$v['filter_condition'];
						$t['sort_condition']=$v['sort_condition'];
						$t['fields']=$v['fields'];
						$temp["{$k}"] = $t;
					}
				}
				if (count($temp)) {
					$this -> checkforConfig = $temp;
				}
			}
		}
		/*  checkfor end */
		$this -> publicProperty = $NBM_COMMON_PROPERTY;
		$this -> privateProperty = $privateProperty;
		$this -> controlDefaultVal = $NBM_DEFAUL_PROPERTYVAL;
		$this -> confRelField = $NBM_REL_FILED;
		/**
		 * 审批流使用字段
		 */
		$this->auditField = $NBM_AUDITFELD;
		/**
		 * 默认字段
		 */
		$this->defaultField = $NBM_DEFAULTFIELD;
		/**
		 * 基础档案字段
		 */
		$this->baseArchivesField=$NBM_BASEARCHIVESFIELD;

		$reservedField = array();
		foreach ($this->defaultField as $k => $v) {
			$reservedField[] = $k;
		}
		foreach ($this->auditField as $k => $v) {
			$reservedField[] = $k;
		}
		foreach ($this->baseArchivesField as $k => $v) {
			$reservedField[] = $k;
		}
		$reservedField[] = 'id';
		$this->systemUserFieldInfo = array_merge($this->defaultField , $this->auditField , $this->baseArchivesField);
		//array_multisort($this->systemUserFieldInfo , SORT_ASC , SORT_STRING);
		//array_sort($this->systemUserFieldInfo, 'canuse');
		sortArray($this->systemUserFieldInfo, 'canuse', 'desc', "string");
		$this -> systemUserField = $reservedField;
		$this->assign('systemUserFieldInfo' , $this->systemUserFieldInfo);
		$this -> assign('reserved', json_encode($reservedField));
		$this -> assign('dbsystemfield', json_encode($this->dbsystemfield));
// 		//2015-1-22 19:13 lookupconfig添加中文带回字段
// 		$lookupConfig1 = $this -> lookupConfig;
// 		foreach($lookupConfig1 as $k=>$v){
// 			if($v['fields_china']) $lookupConfig1[$k]['filedbackchina']=implode(',',$v['fields_china']);
// 		}
// 		$this -> assign('lookupConfig', json_encode($lookupConfig1));
// 		dump($this -> lookupConfig);
		$this -> assign('lookupConfig', json_encode($this -> lookupConfig));
		$this -> assign('checkforConfig', json_encode($this -> checkforConfig));
		$this -> assign('tpltypeArr', $this->tpltypeArr);

		$this -> nodeName = ucfirst($_REQUEST['nodename']);
		//读取列名称数据(按照规则，应该在index方法里面)
		if($this->nodeName){
			$scdmodel = D('SystemConfigDetail');
			$detailList = $scdmodel->getDetail($this->nodeName);
			$this->datalist=$detailList;
		}
		$this -> nodeTitle = $_POST['nodetitle'];
		$this -> tableName = $_POST['tablename'];
		$this -> tableName = strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $this -> nodeName), "_"));
		$this -> isaudit = $_POST['isaudit'] ? true : false;
		$this -> inserttable = $_POST['inserttable'] ? true : false;
		$this->curnode = $_POST['curnode']?$_POST['curnode']:'index';
		$this->souceformid = intval($_POST['ischoise']);
		//$this->souceaction = $_POST['choiseaction'];
		
		$this->isrecord = $_POST['isrecord']  ? true : false ;
		if($this->souceformid){
			$this->isforeignfield = true ;
		}else{
			$this->isforeignfield = false ;
		}
		$this->assign('curnode' , $this->curnode);
	}


	/**
	 * @Title: lookupdefaultfield
	 * @Description: 系统默认字段
	 * @param string $tablename
	 * @author 黎明刚
	 * @date 2014年11月28日 下午4:31:53
	 * @throws
	 */
	private function lookupdefaultfield($tablename=''){
		$list=array();
		$list['auditField']=array(
				'ptmptid' => array (
						'filed' => 'ptmptid',
						'tablename' => $tablename,
						'type' => 'int',
						'length' => '10',
						'category' => '系统自带',
						'title' => '固定流程ID',
						'weight' => '系统自带',
						'sort' => '系统自带',
						'isshow' => '系统自带'
						),
				'flowid' => array (
						'filed' => 'flowid',
						'tablename' => $tablename,
						'type' => 'int',
						'length' => '10',
						'category' => '系统自带',
						'title' => '自定义流程ID',
						'weight' => '系统自带',
						'sort' => '系统自带',
						'isshow' => '系统自带'
						),
				'ostatus' => array (
						'filed' => 'ostatus',
						'tablename' => $tablename,
						'type' => 'varchar',
						'length' => '100',
						'category' => '系统自带',
						'title' => '当前审核节点',
						'weight' => '系统自带',
						'sort' => '系统自带',
						'isshow' => '系统自带'
						),
				'auditState' => array (
						'filed' => 'auditState',
						'tablename' => $tablename,
						'type' => 'tinyint',
						'default'=>'0',
						'length' => '1',
						'category' => '系统自带',
						'title' => '当前审核状态',
						'weight' => '系统自带',
						'sort' => '系统自带',
						'isshow' => '系统自带'
						),
				'curAuditUser' => array (
						'filed' => 'curAuditUser',
						'tablename' => $tablename,
						'type' => 'varchar',
						'length' => '300',
						'category' => '系统自带',
						'title' => '当前可审核人清单',
						'weight' => '系统自带',
						'sort' => '系统自带',
						'isshow' => '系统自带'
						),
				'curNodeUser' => array (
						'filed' => 'curNodeUser',
						'tablename' => $tablename,
						'type' => 'varchar',
						'length' => '300',
						'category' => '系统自带',
						'title' => '当前待审核人清单',
						'weight' => '系统自带',
						'sort' => '系统自带',
						'isshow' => '系统自带'
						),
				'alreadyAuditUser' => array (
						'filed' => 'alreadyAuditUser',
						'tablename' => $tablename,
						'type' => 'varchar',
						'length' => '500',
						'category' => '系统自带',
						'title' => '当前已审核人',
						'weight' => '系统自带',
						'sort' => '系统自带',
						'isshow' => '系统自带'
						),
				'alreadyauditnode' => array (
						'filed' => 'alreadyauditnode',
						'tablename' => $tablename,
						'type' => 'varchar',
						'length' => '200',
						'category' => '系统自带',
						'title' => '当前并行时已审核节点',
						'weight' => '系统自带',
						'sort' => '系统自带',
						'isshow' => '系统自带'
						),
				'auditUser' => array (
						'filed' => 'auditUser',
						'tablename' => $tablename,
						'type' => 'varchar',
						'length' => '500',
						'category' => '系统自带',
						'title' => '所有审核人清单',
						'weight' => '系统自带',
						'sort' => '系统自带',
						'isshow' => '系统自带'
						),
				'allnode' => array (
						'filed' => 'allnode',
						'tablename' => $tablename,
						'type' => 'varchar',
						'length' => '100',
						'category' => '系统自带',
						'title' => '所有流程节点',
						'weight' => '系统自带',
						'sort' => '系统自带',
						'isshow' => '系统自带'
						),
				'informpersonid' => array (
						'filed' => 'informpersonid',
						'tablename' => $tablename,
						'type' => 'varchar',
						'length' => '100',
						'category' => '系统自带',
						'title' => '节点知道会人',
						'weight' => '系统自带',
						'sort' => '系统自带',
						'isshow' => '系统自带'
						),
						);
						$list['defaultField']=array(
				'orderno' => array (
						'filed' => 'orderno',
						'tablename' => $tablename,
						'type' => 'varchar',
						'length' => '100',
						'category' => '系统自带',
						'title' => '编号',
						'weight' => '系统自带',
						'sort' => '系统自带',
						'isshow' => '系统自带'
						),
				'status' => array (
						'filed' => 'status',
						'tablename' => $tablename,
						'type' => 'tinyint',
						'length' => '1',
						'category' => '系统自带',
						'title' => '状态',
						'weight' => '系统自带',
						'sort' => '系统自带',
						'isshow' => '系统自带'
						),
				'companyid' => array (
						'filed' => 'companyid',
						'tablename' => $tablename,
						'type' => 'int',
						'length' => '10',
						'category' => '系统自带',
						'title' => '公司ID',
						'weight' => '系统自带',
						'sort' => '系统自带',
						'isshow' => '系统自带'
						),
				'createid' => array (
						'filed' => 'createid',
						'tablename' => $tablename,
						'type' => 'int',
						'length' => '10',
						'category' => '系统自带',
						'title' => '创建人ID',
						'weight' => '系统自带',
						'sort' => '系统自带',
						'isshow' => '系统自带'
						),
				'createtime' => array (
						'filed' => 'createtime',
						'tablename' => $tablename,
						'type' => 'int',
						'length' => '11',
						'category' => '系统自带',
						'title' => '创建时间',
						'weight' => '系统自带',
						'sort' => '系统自带',
						'isshow' => '系统自带'
						),
				'updateid' => array (
						'filed' => 'updateid',
						'tablename' => $tablename,
						'type' => 'int',
						'length' => '11',
						'category' => '系统自带',
						'title' => '修改人ID',
						'weight' => '系统自带',
						'sort' => '系统自带',
						'isshow' => '系统自带'
						),
				'updatetime' => array (
						'filed' => 'updatetime',
						'tablename' => $tablename,
						'type' => 'int',
						'length' => '11',
						'category' => '系统自带',
						'title' => '修改时间',
						'weight' => '系统自带',
						'sort' => '系统自带',
						'isshow' => '系统自带'
						),
				'operateid' => array (
						'filed' => 'operateid',
						'tablename' => $tablename,
						'type' => 'int',
						'length' => '10',
						'category' => '系统自带',
						'title' => '是否确认',
						'weight' => '系统自带',
						'sort' => '系统自带',
						'isshow' => '系统自带'
						),
						);
						$this->assign("defaultlist",$list);
	}
	public function index(){
		$name=$this->getActionName();
		//生成左侧树结构
		if($_REQUEST['jump']!=1){
			$MisDynamicFormManageModel=D('MisDynamicFormManage');
			$typeTree=$MisDynamicFormManageModel->getAnametree("MisDynamicFormManageview");
			$this->assign("typeTree",json_encode($typeTree));
			$this->assign("uilid",$typeTree[0]['id']);
		}
		//获取系统默认字段
		$this->lookupdefaultfield();
		$aname="";
		$id = 0;
		if($_REQUEST['aname']){
			$aname=$_REQUEST['aname'];
			$id = $_REQUEST['id'];
		}else{
			$aname=$typeTree[0]['ename'];
			$id = $typeTree[0]['id'];
		}
		// 表单的属性
		$forvodata = M('mis_dynamic_form_manage')->where('id='.$id)->find();

		$this->assign("formvodata",$forvodata);
		$this->assign("isaudit",getFieldBy($aname, "actionname", "isaudit", "mis_dynamic_form_manage"));
		$this->assign("aname",$aname);
		$this->getComAndCatypeList();
		if($_REQUEST['id']){
			$id=$_REQUEST['id'];
		}else{
			$id=$typeTree[0]['id'];
		}
		$this->assign("id",$id);
		$_REQUEST['formid']=$id;
		$anameList=$this->getAnameListTodate();
		$this->assign("anameVo",$anameList);
		//查询模板信息
		$MisDynamicFormTemplateModel=D("MisDynamicFormTemplate");
		$MisDynamicFormTemplateList=$MisDynamicFormTemplateModel->where("formid={$id}")->select();
		$this->assign("MisDynamicFormTemplateList",$MisDynamicFormTemplateList);
		//begin
		$scdmodel = D('SystemConfigDetail');
		//扩展工具栏操作
		$toolbarextension = $scdmodel->getDetail($name,false,'toolbar');
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		//end
		//查看该节点是否已有四级节点
		$NodeModel=D('Node');
		$nodeid=getFieldBy($aname,'name','id','node');
		$NodeList=$NodeModel->where("status=1 and  level=4 and pid=".$nodeid)->getField("id,name");
		$this->assign('nodelist',$NodeList);
		//获取模板索引号
		$formtype = getFieldBy($aname,'name','formtype','node');
		$this->assign('formtype',$formtype);
		if( intval($_POST['dwzloadhtml']) ){
			$this->display("dwzloadindex");exit;
		}
		if($_REQUEST['jump']){
			$this->display("indexview");
		}else{
			$this->display();
		}
	}
	/**
	 * @Title: tagcategorycontroll
	 * @Description: todo(checkfor组件信息管理)
	 * @author quqiang
	 * @date 2014-8-28 下午17:14:52
	 * @throws
	 */
	function tagcategorycontroll() {
		$arr = array();

		$dir = ROOT . '/Conf/';
		$controlls =
		require $dir . 'controlls.php';
		foreach ($controlls as $key => $value) {
			//if ($value['iscreate']) {
			$arr[$key] = $value['title'];
			//}
		}

		echo json_encode($arr);
	}
	/**************************************************************************************
	 * 				业务控制
	 **************************************************************************************/
	/**
	 * 主生成函数
	 * @Title: craeteCode
	 * @Description: todo(代码生成的函数，用于控制走向。)
	 * @param array $controllProperty        组件属性数据
	 * @param String $actionName        	控制器名称
	 * @param String $formType        	表单类型，基础档案、表单，审批表单
	 * @param int $serviceType 业务类型【是否审批】
	 * @author quqiang
	 * @date 2015年2月11日 下午1:54:44
	 * @throws Exception
	 */
	protected function createCode(array $controllProperty, String $actionName, String $formType, int $serviceType) {
		logs('业务类型：'.$serviceType);
		/* 验证数据合法性 */
		if (! is_array ( $controllProperty )) {
			throw new NullPointExcetion ( '组件属性数据为空' );
		}
		if (empty ( $actionName )) {
			throw new NullDataExcetion ( '控制器名称为空！' );
		}
		if (empty ( $formType )) {
			throw new NullPointExcetion ('表单类型未选择，请在表单字段信息维护界面选择表单类型。');
		}
		$formTypeData = explode ( '#', $formType );
		if (empty ( $formTypeData[0] )) {
			throw new NullPointExcetion ("表单类型 {$formTypeData[0]} 为空！");
		}
		if (empty ( $this->typEnum [$formTypeData[0]] )) {
			throw new NullDataExcetion ( "表单类型 {$formTypeData[0]} 未知！" );
		}
		$serviceType = $this->auditTpl[$formTypeData[0]];
		// 根据表单类型重置审批状态
		$this->isaudit = $serviceType;
		/* 验证数据合法性 end */
	
		/* 具体生成代码调用 */
		// 生成数据库
		$this->craeteDatabaseAppend ( $controllProperty );
		// 生成组件配置文件
// 		$this->createAutoFormConfig ( $controllProperty, $this->curnode );
		// 组件修改
		self::modifyFormManager ( $controllProperty );
		// 更新配置文件
		$this->modifyConfig ( $controllProperty ['visibility'], $actionName, $serviceType );
		// 生成Model 文件
		$this->createModel ( $controllProperty, $actionName, false, $serviceType );
		// 生成视图
		$this->createView ();
		switch ($formTypeData[0]){
			case 'noaudittpl':
				// 普通表单
			case 'audittpl':
				// 普通表单 - 审批表单
				// 生成Action 文件
				$this->createAction ( $controllProperty, $actionName, false, $serviceType, false );
				// 生成模板
				$this->createTemplate ( $controllProperty ['visibility'], $actionName, $serviceType );
				$this->createJsText();
				break;
			case 'basisarchivestpl':
				// 基础档案
				$this->createBaseArchivesCode($formTypeData);
				$this->createJsText($this->nodeName , 1);
				break;
			case 'basisarchivesaudittpl':
				// 基础档案-审批
					break;
			default:
				// 普通表单
				// 生成Action 文件
				$this->createAction ( $controllProperty, $actionName, false, $serviceType, false );
				// 生成模板
				$this->createTemplate ( $controllProperty ['visibility'], $actionName, $serviceType );
				$this->createJsText($this->nodeName , 1);
				break;
		}
		
		/* 具体生成代码调用 end */
		
		
	}
	
	/**
	 * 建模改版
	 *	@date 20141105 1622
	 * 说明：
	 * 	1.建模填写完成后 ，选择模板生成方式【普通模板、基础档案、扩展余留】。
	 * 	2.建模完成后到index页面布局设置页面。普通模板在这一步允许选择是否为审批模板。基础档案 直接到下一步。
	 *  3.拖动布局add edit 页面。
	 *  4.普通模板反复生成节点模板。
	 *
	 */

	/**
	 *
	 * @Title: add
	 * @Description: todo(新增-建模开始页面，设置字段及模板生成方式)
	 * @author renling
	 * @date 2014-9-25 上午11:44:52
	 * @throws
	 */
	public  function add(){
		if($_REQUEST['type']==1){
			$transeModel = new Model();
			$transeModel->startTrans();
			try{
				//动态建模，保存数据字典
				$primaryname = $this->autoInsert();
				$transeModel->commit();
				$this->success("操作成功", '', array('type' => 1, 'nodename' => $this->nodeName , 'tablename'=>$primaryname,'tpltype'=>$this->tpltype));
				
			}catch (Exception $e){
				$transeModel->rollback();
				$this->error( $e->__toString() );
			}
			
		}else{
			/*此方法为查询子表信息*/
			//$this->getTableNameList();
			//$this->getNodeList();
			//获取数据字段类型，字段组件类型
			$this->getComAndCatypeList();
			$this->display();
		}
	}
	/**
	 * 
	 * @Title: getTableNameList
	 * @Description: todo(读取复用表信息)   
	 * @author renling 
	 * @date 2014年12月22日 下午6:08:48 
	 * @throws
	 */
	public function getTableNameList(){
		$map['status']=1;
		if($_POST['subType']!=2){
			$map['isprimary']=1;
		}
		//读取复用表信息
		$MisDynamicDatabaseMasModel=D("MisDynamicDatabaseMas");
		$MisDynamicDatabaseMasList=$MisDynamicDatabaseMasModel->where($map)->getField("tablename,tabletitle");
		$MisDynamicDatabaseMasAllList=array_unique($MisDynamicDatabaseMasList);
		if($_POST['subType']){
			echo json_encode($MisDynamicDatabaseMasAllList);
		}else{
			$this->assign("MisDynamicDatabaseMasList",$MisDynamicDatabaseMasAllList);
		}
	}
	/**
	 * @Title: setindextpl
	 * @Description: todo(设置首页模板生成方式，如果 上一步选择的是基础档案就直接下一步。如树在左侧还是右侧。)
	 * @author quqiang
	 * @date 2014-11-5 下午04:30:22
	 * @throws
	 */
	public function setindextpl(){
		//读取配置文件   (不采用配置文件方式，已经优化为数据库保存。)
		//$anameList = require ROOT . '/Dynamicconf/autoformconfig/MisAutoAnameList.inc.php';
		// 得到模板生成方式。
		if($_POST && $_GET['type']=='setindextpl'){
			//修改生成配置文件数据源字段
			$tablename = $_POST['tablename']; // 表名
			$nodename = $_POST['nodename']; // action名
// 			foreach ($anameList[$nodename]['datebase'][$tablename]['list'] as $akey=>$aval){
// 				if($aval['filed']==$_POST['datasouce']){
// 					$anameList[$nodename]['datebase'][$tablename]['list'][$akey]['isdatasouce']=1;
// 				}
// 			}
// 			$this->createAutoAnameConfig($anameList[$nodename],$nodename);
			$previewtype = $_POST['nbm_tpl']; // 模板布局方式 。
			$tpltype = $_POST['nbm_tpl_type']; // 模板生成类型
			//重新赋值是否是审批流模板
			$this->isaudit= $tpltype == 'audittpl'?true:false;
			// 获取到当前action 在表单记录表中的数据，并将审批状态改为当前状态。
			$MisDynamicFormManageModel=D('MisDynamicFormManage');
			// $data = $MisDynamicFormManageModel->where("`actionname`='{$nodename}'")->field('id')->find(); // 最安全的修改
			$saveData['isaudit'] = $this->isaudit;
			// $MisDynamicFormManageModel->where("`id`={$data['id']} and `actionname`='{$nodename}'")->save($saveData); // 最安全的修改
			$MisDynamicFormManageModel->where("`actionname`='{$nodename}'")->save($saveData);
			// 改node表中的属性
			// isprocess
			$nodeModel = D('Node');
			$nodeSaveData['isprocess'] = $this->isaudit;
			// 当前修改方式为不可靠的修改。
			$nodeModel->where("`name`='{$nodename}'")->save($nodeSaveData);
			$this->success("操作成功", '', array('type' => 1, 'nodename' => $nodename, 'tablename'=>$tablename,'tpltype'=>$tpltype , 'isaudit'=>$this->isaudit , 'previewtype'=>$previewtype));
		}else{
			$MisDynamicFormManageModel=D("MisDynamicFormManage");
			$mdmtMap=array();
			$mdmtMap['status']=1;
			$tpltype=$_GET['tpltype'];
			$this->tpltype = $this->typEnum[$_GET['tpltype']];
			if($this->tpltype){
				$this->assign('tpltype', $this->tpltype);
			}else{
				$this->assign('tpltype', 'commontpl');
			}
			if($this->tpltype=="zczuhetpl"){
				//获取已绑定关系有类型筛选的主表
				$MisAutoBindList=$MisAutoBindModel->where("status=1 and typeid=0")->getField("id,bindaname");
				$mdmMap=array();
				if($MisAutoBindList){
					$mdmMap['actionname']=array("not in",implode(',',$MisAutoBindList));
				}
				$mdmMap['_string']="tpl='noaudittpl#ltrl' or tpl='noaudittpl#lcrt' or tpl='noaudittpl#list'";
				$mdmMap['status']=1;
				//主从表查询类型
				$zcMisDynamicFormManageList=$MisDynamicFormManageModel->where($mdmMap)->select();
				$this->assign("zcMisDynamicFormManageList",$zcMisDynamicFormManageList);
			}
			$tablename = $_GET['tablename'];  //获取表名
			$nodename = $_GET['nodename'];	//获取node节点名称
			$this->assign('tablename',$tablename);
			$this->assign('nodename',$nodename);
			$this->display();
		}
	}
	/**
	 *
	 * @Title: getComAndCatypeList
	 * @Description: todo(获取字段及主键类型)
	 * @author renling
	 * @date 2014-9-28 下午5:49:42
	 * @throws
	 */
	private function getComAndCatypeList(){
		//查询所有主表信息
		$MisDynamicDatabaseMasDao=M("mis_dynamic_database_mas");
		$masMap=array();
		$masMap['isprimary']=1;
		$masMap['status']=1;
		$MisDynamicDatabaseMasList=$MisDynamicDatabaseMasDao->where($masMap)->group("tablename")->getField("tablename,tabletitle");
		$this->assign("MisDynamicDatabaseMasList",$MisDynamicDatabaseMasList);
		//取得字段类型
		$fieldtype=explode('#',$this->publicProperty['tabletype']['data']);
		$fieldtypeList=array();
		foreach ($fieldtype as $fkey=>$fval){
			$newfieldtype=explode('|',$fval);
			$fieldtypeList[strtolower($newfieldtype[0])]=array(
					'key'=> strtolower($newfieldtype[0]),
					'val'=> strtolower($newfieldtype[1]),
					'len'=> strtolower($newfieldtype[2]),
					'cate'=> strtolower($newfieldtype[3]),
			);
		}
		//取得组件类型
		$controlls=$this->controlConfig;
		foreach ($controlls as $key=>$val){
			//判断是否需要创建数据库
			if($val['iscreate']==1){
				$comboxOptionList[$key]=array(
						'title'=>$val['title'],
						'name'=>$key,
						'weight'=>$val['weight'],
				);
			}
		}
		$this->assign('fieldtypeList',$fieldtypeList);
		$this->assign('comboxOptionList',$comboxOptionList);
	}
	function lookuptable(){
		//查询该表在数据库中的字段
		$table=$_REQUEST['tableename'];
		//表记录
		$MisDynamicDatabaseMasDao=M("mis_dynamic_database_mas");
		//获取表记录id
		$masMap=array();
		//$masMap['_string']=" ischoise IS NULL";
		$masMap['status']=1;
		$masMap['tablename']=$table;
		$MisDynamicDatabaseMasVo=$MisDynamicDatabaseMasDao->where($masMap)->find();
		//print_r($MisDynamicDatabaseMasDao->getLastSql());
		$masid=$MisDynamicDatabaseMasVo['id'];
		$formid=getFieldBy($table, "tablename", "formid", "mis_dynamic_database_mas");
		if($masid){
			//表字段记录表
			$MisDynamicDatabaseSubModel=D("MisDynamicDatabaseSub");
			//查询字段记录表
			$map=array();
			$map['masid']=$masid;
			$map['status']=1;
			//查询字段
			$MisDynamicDatabaseSubList=$MisDynamicDatabaseSubModel->where($map)->select();
			$this->assign("fieldVo",$MisDynamicDatabaseSubList);
			//查询是否带审批流
			$isaudit=getFieldBy($formid,'id','isaudit','mis_dynamic_form_manage');
			$this->assign("isaudit",$isaudit);
		}
		//查询组件类型
		$this->getComAndCatypeList();
		$this->display();
	}
	public function getAnameListTodate(){
		//表记录mas表
		$MisDynamicDatabaseMasDao=M("mis_dynamic_database_mas");
		//表记录sub表
		$MisDynamicDatabaseSubDao=M("mis_dynamic_database_sub");
		//字段最终结果集
		$anameList=array();
		$masMap=array();
		$masMap['status']=1;
		$masMap['formid']=$_REQUEST['formid'];
		$this->assign("formid",$masMap['formid']);
		//查询表信息
		$MisDynamicDatabaseMasList=$MisDynamicDatabaseMasDao->where($masMap)->select();

		//循环查询表字段信息
		foreach ($MisDynamicDatabaseMasList as $mdmkey=>$mdmval){
			$subMap=array();
			$subMap['status']=1;
			$subMap['masid']=$mdmval['id'];
			$MisDynamicDatabaseSubList=$MisDynamicDatabaseSubDao->where($subMap)->select();
			//组装表信息
			$anameList[$mdmkey]=$mdmval;
			//循环表字段信息
			foreach ($MisDynamicDatabaseSubList as $subkey=>$subval){
				$anameList[$mdmkey]['list'][]=$subval;
			}
		}
		return $anameList;

	}
	function edit(){
		/*
		 $this->nodeName=$MisDynamicFormManageVo['actionname'];
		 $html=$this->createContrlHtml('');
		 $this->assign("html",$html);
		 $this->display("MisDynamicFormManage:autoformindex");
		 */
		$id = $_GET['id'];
		$model = D('MisDynamicFormManage');
		//修改当前表字段
		if($_REQUEST['type']==1){
			$this->getNodeList();
			//获取当前节点所在父节点
			$pnodeid=getFieldBy($_REQUEST['aname'], "name", "pid , showmenu","node");
			$nodeinfo = M('node')->where(array('name'=>$_REQUEST['aname']))->field('id , pid,group_id , showmenu')->find();
			$this->assign("nodeinfo",$nodeinfo);
			$this->assign("pnodeid",$pnodeid);
			//得到当前actionname
			$MisDynamicFormManageVo=$model->where("status=1 and  actionname='".$_REQUEST['aname']."'")->find();
			$MisDynamicFormManageVo['tpl'] = explode('#', $MisDynamicFormManageVo['tpl']);
			//模板表
			$MisDynamicFormTemplateDao=M("mis_dynamic_form_template");
			//组件属性表
			$MisDynamicFormProperyDao=M("mis_dynamic_form_propery");
			//表记录
			$MisDynamicDatabaseMasDao=M("mis_dynamic_database_mas");
			//表字段记录
			$MisDynamicDatabaseSubDao=M("mis_dynamic_database_sub");
			
			$anameList=$this->getAnameListTodate();
			// 获取当前表单用到的字段。在系统字段列表中会使用。
			$propertyObj = M('mis_dynamic_form_propery');
			$propertyMap['formid']=$_REQUEST['formid'];
			$propertyFieldList  = $propertyObj->where($propertyMap)->field('fieldname,title')->select();
			$propertyField = array();
			$propertyFieldTitle=array();
			foreach ($propertyFieldList as $K=>$v){
				array_push($propertyField,$v['fieldname']);
				$propertyFieldTitle[$v['fieldname']]=$v['title'];
			}
			$this->assign("propertyFieldTitle",$propertyFieldTitle);
			$this->assign("propertyfieldlist",$propertyField);
			$this->assign("anameListvo",$anameList);
			//查询模板信息
			$ftMap=array();
			$ftMap['status']=1;
			$ftMap['formid']=$_REQUEST['formid'];
			//查询当前表单所有模板信息
			$tempList=$MisDynamicFormTemplateDao->where($ftMap)->getField("tplname,id");
			$tempkeyList=array_keys($tempList);
			if($_REQUEST['tempids']){
				$tempids=$_REQUEST['tempids'];
			}else{
				$tempids=$tempkeyList[0];
			}
			//模板id
			$templateid=$tempList[$tempids];
			//查询当前模板下所有字段
			$MisDynamicFormProperyList=$MisDynamicFormProperyDao->where("tplid={$templateid}")->getField("fieldname,id");
			$this->assign("tempids",$tempids);
			$this->assign("MisDynamicFormProperyList",$MisDynamicFormProperyList);
			$this->assign("tempkeyList",$tempkeyList);
			$MisDynamicFormManageModel=M('mis_dynamic_form_manage');
			$actiontitleVo=$MisDynamicFormManageModel->where(" status=1 and actionname='".$_REQUEST['aname']."'")->field("actiontitle")->find();
			$this->assign("anametitle",$actiontitleVo['actiontitle']);
			$this->assign("acname",$_REQUEST['aname']);
			$this->assign('curnode',$this->curnode);
			//获取当前数据源字段
			$this->assign("datasoure",getFieldBy($_REQUEST['formid'],"formid", "field", "mis_dynamic_database_sub","isdatasouce",1));
			$this->assign("vo",$MisDynamicFormManageVo);
			// 获取到当前的方案key
			$curtpltypekey='commontpl';
			foreach($this->rels as $k=>$v){
				if(in_array($MisDynamicFormManageVo['tpl'][0],$v )){
					$curtpltypekey = $k;
				}
			}
			//当前表单类型为组合类型
			if($curtpltypekey=="zuhetpl"||$curtpltypekey=="zczuhetpl"){
				//获取当前绑定表单
				$misAutoBindDao=M('mis_auto_bind');
				$bindMap['inbindaname']=$_REQUEST['aname'];
				$bindMap['status']=1;
				$misAutoBindVoList=$misAutoBindDao->where($bindMap)->select();
				$actiontitle=getFieldBy($misAutoBindVoList[0]['bindaname'],"actionname","actiontitle","mis_dynamic_form_manage");
				$this->assign("actitontitle",$actiontitle);
				//获取可配置数据源
				$datasoucelist=$this->getDateSoure($misAutoBindVoList[0]['bindaname']);
				$this->assign("bindresult",$datasoucelist['field']);
				$datasoucelist=$this->getDateSoure($misAutoBindVoList[0]['bindaname']);
				$this->assign('datasoucelist',$datasoucelist);
				$this->assign("misAutoBindVoList",$misAutoBindVoList);
			}
			//查询当前主表是否是复用主表
		   $tablename=getFieldBy($_REQUEST['formid'], "formid", "tablename", "mis_dynamic_database_mas","isprimary",1);
		   $ischoise=getFieldBy($tablename, "tablename", "id", "mis_dynamic_database_mas","ischoise",1);
		   if($ischoise){
		   	//查询复用主表字段信息
		   	$tablename=getFieldBy($_REQUEST['formid'], "formid", "tablename", "mis_dynamic_database_mas","isprimary",1);
		   	//获取当前主表信息 $ischoise=getFieldBy($tablename, "tablename", "id", "mis_dynamic_database_mas","ischoise",1);
		   	$masterVo=$MisDynamicDatabaseMasDao->where("tablename='".$tablename."'")->find();
		   	//查询该表字段信息
		   	$subList=$MisDynamicDatabaseSubDao->where("masid=".$masterVo['id'])->select();
		   	//复用表 查询表结构信息
		   	$DynamicconfModel=D("Dynamicconf");
		   	foreach ($subList as $subkey=>$subval){
				foreach ($anameList[0]['list'] as $ankey=>$anval){
					if($subval['field']==$anval['field']){
						unset($subList[$subkey]);
					} 
				}		   		
		   	}
		   	foreach ($subList as $newkey=>$newval){
		   		unset($newval['id']);
		   		unset($newval['masid']);
		   		unset($newval['formid']);
		   	}
		   	$this->assign("tablelist",$subList);
		   }
			//查询当前复用信息
  		  	//$choiseformid=getFieldBy($_REQUEST['formid'], "id", "choiseformid", "mis_dynamic_form_manage");
  		  	$managerModel=M('mis_dynamic_form_manage');
  		  	$managerModelMap['id'] = $_REQUEST['formid'];
  		  	$managerData = $managerModel->where($managerModelMap)->find();
  		  	
	  	 	$this->assign("choiseformid",$managerData['choiseformid']);
	  	 	$this->assign("choisevo",$managerData);
			$this->assign("bindaname",$misAutoBindVoList[0]['bindaname']);
			$this->assign("tplkey",$curtpltypekey);
			$this->assign('reservedField',$this->systemUserField);
			$this->getComAndCatypeList();
			$this->display("MisDynamicFormManage:editbase");



		}else if($_REQUEST['type']==2){
			$transeModel = new Model();
			$transeModel->startTrans();
			try{
				//动态建模，修改
				$resultReturn = $this->autoUpdate();
				$resultReturn['isrecord'] = $this->isrecord;
				$transeModel->commit();
				$this->success("操作成功asdasdasdasdasdasdasd", '', $resultReturn);
			
			}catch (Exception $e){
				$transeModel->rollback();
				$this->error( $e->__toString() );
			}
			
			
		}else{
			try{
			//修改模板信息
			$filedModel = M('MisDynamicFormField');
			$formInfo = $model->where('id='.$id)->find();
			$this->nodeName = $formInfo['actionname'];
			$this->nodeTitle = $formInfo['actiontitle'];
			$this->isrecord = $formInfo['isrecord'];
			$this->assign('action',$this->nodeName);
			// 现有组件
			$formid = getFieldBy($this->nodeName, "actionname", "id", "mis_dynamic_form_manage");
			if(!$formid || empty($formid)){
				$msg = "不能通用action名称{$this->nodeName}获取表单信息！{$formid}";
				throw new NullDataExcetion($msg);
			}
			$DBConfigData = $this->getfieldCategory($formid);
			$data = $DBConfigData[$this->curnode];
			// 生成html
			$html = self::createContrlHtml($data);
			$this->assign('html',$html);
			$this->assign('vo',$formInfo);
			$this->assign('reservedField',$this->systemUserField);
			$this->assign('NBM_COMMON_PROPERTY',$this->publicProperty);
			$this->assign('NBM_COMMON_PROPERTY_JSON',json_encode($this->publicProperty));
			$this->assign('NBM_PRIVATE_PROPERTY_JSON',json_encode($this->privateProperty));
			$this->assign('controlls',$this->controls);
			$this->getNodeList();
			$this->display('MisDynamicFormManage:autoformindex');
			}catch (Exception $e){
				echo $e->__toString();
			}
		}
	}
	/**
	 * 
	 * @Title: createDataTable
	 * @Description: todo(生成之前生成失败的表) 
	 * @param int $formid  表单编号
	 * @author quqiang 
	 * @date 2015年1月7日 上午11:26:11 
	 * @throws
	 */
	function createDataTable($formid){
		if(!$formid){
			$msg = "表单ID传入为空！";
			throw new NullDataExcetion($msg);
		}
		$tablename=M('mis_dynamic_database_mas')->where('formid='.$formid)->order("isprimary desc")->select();
		//获取主表名称
		$primaryname="";
		$MisDynamicDatabaseSubDao=D("mis_dynamic_database_sub");
		 foreach ($tablename as $key=>$val){
		 	//判断主表是否存在
		 	if($val['isprimary']==1){
		 		$primaryname=$val['tablename'];
		 		if(!$this-> validateTable($val['tablename'])){
		 			//不存在主表  查询主表字段
		 			$MisDynamicDatabaseSubList=$MisDynamicDatabaseSubDao->where("masid=".$val['id'])->select();
		 			//创建主表
		 			$ceatetable_html_s .= "\r\nCREATE TABLE IF NOT EXISTS `".$val['tablename']."` \r\n(`id` int(11) NOT NULL AUTO_INCREMENT  COMMENT 'ID' ,";
		 			$ceatetable_html_e="\r\n\t PRIMARY KEY (`id`)";
		 			$unique="";
		 			foreach ($MisDynamicDatabaseSubList as $mskey=>$msval){
		 				if($msval['unique']==1){
		 					$unique="UNIQUE";
		 				}
		 				if($msval['type']=="date"){
		 					$msval['type']="int";
		 				}
		 				//循环创建字段
		 				$ceatetable_html.="\r\n\t`".$msval['field']."` ".$msval['type']."(".$msval['length'].")  DEFAULT NULL ".$unique." COMMENT '".$msval['title']."', ";
		 			}
		 			foreach ($this->defaultField as $key=>$val){
		 				$ceatetable_html .="\r\n\t`".$key."` {$val['type']}({$val['length']}) ".($val['default']!=''?"DEFAULT '{$val['default']}'":"")."{$val['isnull']} {$val['desc']} ,";
		 			}
		 			foreach ($this->auditField as $key=>$val){
		 				$ceatetable_html .="\r\n\t`".$key."` {$val['type']}({$val['length']}) ".($val['default']!=''?"DEFAULT '{$val['default']}'":"")."{$val['isnull']} {$val['desc']} ,";
		 			}
		 			// 开启基础档案字段生成。
		 			//if($this->tpltype =='basisarchivestpl'){
		 				foreach ($this->baseArchivesField as $key=>$val){
		 					$ceatetable_html .="\r\n\t`".$key."` {$val['type']}({$val['length']}) ".($val['default']!=''?"DEFAULT '{$val['default']}'":"")."{$val['isnull']} {$val['desc']} ,";
		 				}
		 			//}
		 			$createtable_str=$ceatetable_html_s.$ceatetable_html.$ceatetable_html_e."\r\n)ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='".$val['tabletitle']."'";
		 		$ret = $MisDynamicDatabaseSubDao->query($createtable_str);
		 		logs('C:'.__CLASS__.' L:'.__LINE__."1420需要jieguo=====：{$this->pw_var_export($ret)}结果运行1420sql====：".$createtable_str);
		 			$resultcere="";
		 			$resultcere=$MisDynamicDatabaseSubDao->getDbError();
		 			if(strlen($resultcere)>5){
		 				$msg = "主表字段设置错误,请查证后再提交!".$MisDynamicDatabaseSubDao->getDBError().' '.$MisDynamicDatabaseSubDao->getLastSql();
		 				throw new NullDataExcetion($msg);
		 			}
		 		}
		 	}else{
		 		//不存在该表
		 		if($primaryname){
		 			if(!$this-> validateTable($val['tablename'])){
		 				//不存在主表  查询主表字段
		 				$MisDynamicDatabaseSubList=$MisDynamicDatabaseSubDao->where("masid=".$val['id'])->select();
		 				$ceatechild_html_s="CREATE TABLE IF NOT EXISTS `".$val['tablename']."` (";
						$ceatechild_html_s.="`id` int(11) NOT NULL AUTO_INCREMENT  COMMENT 'ID'";
						$ceatechild_html_e=",PRIMARY KEY (`id`)";
						$ceatechild_html_e.=",`masid` int(11) COMMENT '关联动态表单数据ID',";
						$ceatechild_html='';
		 				$unique="";
		 				foreach ($MisDynamicDatabaseSubList as $mskey=>$msval){
		 					if($msval['unique']==1){
		 						$unique="UNIQUE";
		 					}
		 					if($msval['type']=="date"){
		 						$msval['type']="int";
		 					}
		 					//循环创建字段
		 					$ceatechild_html.="\r\n\t`".$msval['field']."` ".$msval['type']."(".$msval['length'].")  DEFAULT NULL ".$unique." COMMENT '".$msval['title']."', ";
		 				}
		 				$ceatechild_html.="KEY `delete_{$val['tablename']}` (`masid`),";
		 				$ceatechild_html.="CONSTRAINT `delete_{$val['tablename']}` FOREIGN KEY (`masid`) REFERENCES `{$primaryname}` (`id`) ON DELETE CASCADE";
		 				// 外键删除功能 end
		 				$sqlStr = $ceatechild_html_s.$ceatechild_html_e.$ceatechild_html.")ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='动态表单:{$primaryname} 内嵌表 ".$primaryname."'";
		 				//创建子表
		 				$childresult=$MisDynamicDatabaseSubDao->query($sqlStr);
		 				$errorStr = $MisDynamicDatabaseSubDao->getDbError();
		 				
		 				if(strlen($resultcere)>5){
		 					$msg = "从表字段设置错误,请查证后再提交!".$MisDynamicDatabaseSubDao->getDBError().' '.$MisDynamicDatabaseSubDao->getLastSql();
		 					throw new NullDataExcetion($msg);
		 				}
		 			}
		 		}else{
		 				$msg = "数据异常，该表单不存在主表!";
		 				throw new NullDataExcetion($msg);
		 		}
		 	}
		 }
	}
	private function autoUpdate(){
		$formid=$_POST['formid'];//动态表单记录id
		$this->tpltype = $this->typEnum[$_POST['nbm_tpl_type']];
		if(!$this->tpltype){
			$msg = "模板生成方式为空！";
			throw new NullDataExcetion($msg);
		}
		$this->isaudit=$this->tpltype == 'audittpl'?true:false;
		$this->nodeName=$_POST['actionname'];
		$this->nodeTitle=$_POST['acitontitle'];
		$this->curnode =$_POST['curnode'];
		//ALTER TABLE testtb CHANGE NAME newname CHAR;
		//修改表单名称
		$MisDynamicFormManageModel=D('MisDynamicFormManage');
		$MisDynamicFormManageModel->where("actionname='".$_POST['actionname']."'")->setField("actiontitle", $_POST['acitontitle']);
		$NodeModel=D("Node");
		$NodeModel->where("name='".$_POST['actionname']."'")->setField("title", $_POST['acitontitle']);
		//创建主表储存表
		$AutoPrimaryMasModel=M('mis_auto_primary_mas');
		//创建字表储存表
		$AutoPrimarySubModel=M('mis_auto_primary_sub');
		//创建组件属性表模型
		$MisDynamicFormProperyDao=M("mis_dynamic_form_propery");
		//模板表
		$MisDynamicFormTemplateDao=M("mis_dynamic_form_template");
		//表记录mas表
		$MisDynamicDatabaseMasDao=M("mis_dynamic_database_mas");
		//表记录sub表
		$MisDynamicDatabaseSubDao=M("mis_dynamic_database_sub");
		//主子表视图
		$MisDynamicDatabaseMasViewModel=D("MisDynamicDatabaseMasView");
		if($_POST['insertnode']){
			$this->addNode(getFieldBy($_POST['actionname'], "actionname", "id", "mis_dynamic_form_manage"));
		}
		/**
		 * 数据分发提醒（修改、删除字段时引用该字段的情况）---开始---xyz
		 */
		//修改字段名称 以及添加字段
		$fieldname=$_POST['fieldname'];//字段英文名称集合
		$tablename=$_POST['tablename'];//表名（英文自动申城）
		$delfieldList=$_POST['deleteField']; //删除字段
		$newdellist = array();
		//删除字段
		foreach($delfieldList as $k=>$v){
			$delf = explode(",",$v);
			foreach($delf as $k1=>$v1){
				if($v1){
					$newdellist[$k]['old'][$k1]= getFieldBy($v1,"id","field","mis_dynamic_database_sub");
					$newdellist[$k]['new'][$k1]= '';
				}
			}
		}
		//修改字段
		foreach($tablename as $tkey=>$tval){
			foreach ($fieldname[$tkey]  as $key=>$val){
				if($_POST['ename'][$tkey]){ 
					$sql="";
					if($_POST['oldfieldname'][$tkey][$key]&&$_POST['oldfieldname'][$tkey][$key]&&$_POST['oldfieldname'][$tkey][$key]!=$val){
						//获取修改字段
// 						$updatefieldsid[$tkey][$key] =  $_POST['fieldid'][$tkey][$key];
// 						$updatefieldsold[$tkey][$key] = $_POST['oldfieldname'][$tkey][$key];
// 						$updatefieldsnew[$tkey][$key] = $val;
						$newdellist[$tkey]['old'][$key] = $_POST['oldfieldname'][$tkey][$key];
						$newdellist[$tkey]['new'][$key] = $val;
					}
				}
			}
		}
		
		$newupdatefield = array();
		foreach ($_POST['tableprimary'] as $yk=>$yv){
			$this->dtupdatewarn($formid,'',1,$newdellist[$yk]);
// 			$updatefield1 = is_array($newdellist[$yk])?$newdellist[$yk]:array();			
// 			$updatefield2 = is_array($updatefieldsold[$yk])?$updatefieldsold[$yk]:array();
// 			$newupdatefield[$yk] = array_filter(array_merge($updatefield1,$updatefield2));
		}
		
		/**
		 * 数据分发提醒（修改、删除字段时引用该字段的情况）---结束---xyz
		 */
		$primaryname="";
		$fieldnameList=array();
		$arr1=array(); //修改字段
		$arr2=""; //需要移除当前节点中的字段【作用范围为当前节点】
		$arr3=''; //所有需要删除的字段，所有节点一起删除【作用范围为当前action】
		$alist=array();//需要插入数据库的记录信息（新增）
		$listArr=array();//新增表数据集合
		//当前模板id
		$tplid=getFieldBy($this->curnode, "tplname", "id", "mis_dynamic_form_template","formid",$formid);
		//查询当前表复用表
		$choiseformid=getFieldBy($this->nodeName, "actionname", "choiseformid", "mis_dynamic_form_manage");
		
		//以下是移除字段操作数据库begin
		foreach ($tablename as $tkey=>$tval){
			if($delfieldList[$tkey]){
				$delfieldStr=array();
				//循环删除字段
				$delfieldVo=explode(",",$delfieldList[$tkey]);
				//查询该表是否是复用表
				$subMap=array();
				$subMap['tablename']=$_POST['ename'][$tkey];
				$subMap['ischoise']=1;
				$subMap['status']=1;
				$subMap['formid']=array("neq",$formid);
				$MisDynamicDatabaseChoiseMasList=$MisDynamicDatabaseMasViewModel->where($subMap)->select();
				foreach ($delfieldVo as $dfkey=>$dfval){
					$asql="";
					if($dfval){
						$delfieldname=getFieldBy($dfval, "id", "field", "mis_dynamic_database_sub");
						$delfieldStr[]=$dfval;
						//循环查找该字段的组件属性id
						if ($MisDynamicDatabaseChoiseMasList){
							foreach ($MisDynamicDatabaseChoiseMasList as $chosekey=>$choseval){
								if($choseval['field']==$delfieldname){
									$delfieldStr[]=$choseval['subid'];
								}
							}
						}
						//删除该字段
						$asql="ALTER TABLE ".$_POST['ename'][$tkey]." DROP ".$delfieldname;
						$areulst=$MisDynamicFormManageModel->query($asql);
					}
				}
				if($delfieldStr){
					//删除组件属性表
					$proMap=array();
					$proMap['ids']=array("in",array_values($delfieldStr));
					logs('1297====删除组件属性表'.$MisDynamicFormProperyDao->getLastsql(),'propertydeleterecode');
					$MisDynamicFormProperyResult=$MisDynamicFormProperyDao->where($proMap)->delete();
					if(false === $MisDynamicFormProperyResult){
						$msg = "删除组件属性失败！".$MisDynamicFormProperyDao->getDBError().' '.$MisDynamicFormProperyDao->getLastSql();
						throw new NullDataExcetion($msg);
					}
				}
				//删除sub表记录
				$subMap=array();
				$subMap['id']=array("in",array_values($delfieldStr));
				$MisDynamicDatabaseSubReuslt=$MisDynamicDatabaseSubDao->where($subMap)->delete();
				logs('1308====删除字段信息'.$MisDynamicDatabaseSubDao->getLastsql(),'propertydeleterecode');
				if(false === $MisDynamicDatabaseSubReuslt){
					$msg = "删除字段信息失败！".$MisDynamicDatabaseSubDao->getDBError().' '.$MisDynamicDatabaseSubDao->getLastSql();
					throw new NullDataExcetion($msg);
				}
				
			}
			//以上是移除字段操作数据库begin
			$ceatechild_html='';
			if($_POST['tableprimary'][$tkey]){
				$isprimary=1;
				$primaryname=$_POST['tableprimary'][$tkey];//获取主表表名
				if($_POST['ischoise']!=1){
					$date=array();
					//获取字段列表(序列化)
					$date['filedlist']=serialize($this->gettableArr($tkey));
					$date['tabletitle']=$tval;
					$primaryResult=$AutoPrimaryMasModel->where("tablename='".$primaryname."'")->save($date);
					if(false === $primaryResult){
						$msg = "主表生成失败！【可能原因有：字段重名、字段名过长，请检查！】".$AutoPrimaryMasModel->getDBError().' '.$AutoPrimaryMasModel->getLastSql();
						throw new NullDataExcetion($msg);
					}
				}
			}else{
				$isprimary="";
			} 
			foreach ($fieldname[$tkey]  as $key=>$val){
				//查询该表是否是复用表
				$subMap=array();
				$subMap['tablename']=$_POST['ename'][$tkey];
				$subMap['ischoise']=1;
				$subMap['status']=1;
				$subMap['formid']=array("neq",$formid);
				$MisDynamicDatabaseChoiseMasList=$MisDynamicDatabaseMasDao->where($subMap)->select();
				$unique="";
				if($_POST['fieldunique'][$tkey][$key]==1){
					$unique="UNIQUE";
				}
				//以下为新增字段
				$subDate=array();
				$subDate['field']=$val;
				$subDate['unique']=$_POST['fieldunique'][$tkey][$key];
				$subDate['masid']=$_POST['masid'][$tkey];
				$subDate['isshow']=$_POST['fieldshow'][$tkey][$key];
				$subDate['desc']=$_POST['fielddesc'][$tkey][$key];
				$subDate['tablename']=$_POST['ename'][$tkey];
				$subDate['type']=$_POST['fieldtype'][$tkey][$key];
				$subDate['length']=$_POST['fieldlength'][$tkey][$key];
				$subDate['category']=$_POST['fieldcategory'][$tkey][$key];
				$subDate['title']=$_POST['fieldtitle'][$tkey][$key];
				$subDate['weight']=$_POST['fieldweight'][$tkey][$key];
				$subDate['sort']=$key;
				$subDate['formid']=$formid;
				$subDate['isdatasouce']=$_POST['datasouce']==$val?'1':'0';
				//当前字段名称等于数据源字段名称
				if($_POST['datasouce']==$val){
					//修改数据源字段
					$isdatasouceResult=$MisDynamicDatabaseSubDao->where("id=".$_POST['fieldid'][$tkey][$key])->setField("isdatasouce",1);
					if(false === $isdatasouceResult){
						$msg = "修改数据源失败！".$MisDynamicDatabaseSubDao->getDBError().' '.$MisDynamicDatabaseSubDao->getLastSql();
						throw new NullDataExcetion($msg);
					}
				}
				//添加入propety表
				$prodate=array();
				$prodate['fieldname']=$val;
				// lookup时默认生成org值
				if($_POST['fieldcategory'][$tkey][$key]=='lookup'|| $_POST['fieldcategory'][$tkey][$key]=='lookupsuper'){
					$prodate['lookupgrouporg']='orgDBC'.$_POST['fieldid'][$tkey][$key];
				}
				// 下拉框返写 nbmxkj@20150817
				if($_POST['fieldcategory'][$tkey][$key]=='select'){
					$prodate['dropbackkey']=$_POST['fieldname'][$tkey][$key];;
				}
				// 20141213 @nbmxkj 属性中添加字段所属表信息
				$prodate['dbname']=$_POST['ename'][$tkey];
				$prodate['tablelength']=$_POST['fieldlength'][$tkey][$key];
				$prodate['fieldtype']=$_POST['fieldtype'][$tkey][$key];
				$prodate['category']=$_POST['fieldcategory'][$tkey][$key];
				$prodate['sort']=$key;
				$prodate['title']=$_POST['fieldtitle'][$tkey][$key];
				if($_POST['ename'][$tkey]){ //修改原有表信息
					$sql="";
					if($_POST['oldfieldname'][$tkey][$key]&&($_POST['oldfieldname'][$tkey][$key]&&($_POST['oldfieldname'][$tkey][$key]!=$val)||($_POST['oldfieldtype'][$tkey][$key]!=$_POST['fieldtype'][$tkey][$key])||($_POST['fieldlength'][$tkey][$key]!=$_POST['oldfieldlength'][$tkey][$key])||($_POST['fieldtitle'][$tkey][$key]!=$_POST['oldfieldtitle'][$tkey][$key])||($_POST['fieldshow'][$tkey][$key]==1&&intval($_POST['fieldshow'][$tkey][$key])!=$_POST['oldfieldshow'][$tkey][$key])||($_POST['oldfieldcategory'][$tkey][$key]!=$_POST['fieldcategory'][$tkey][$key])||($_POST['oldfielddesc'][$tkey][$key]!=$_POST['fielddesc'][$tkey][$key])||($_POST['oldfieldunique'][$tkey][$key]!=intval($_POST['fieldunique'][$tkey][$key])))){
						//修改字段名称及字段类型 长度
						if($_POST['ischoise']!=1){
							if($_POST['fieldtype'][$tkey][$key] == 'date'){
								$_POST['fieldtype'][$tkey][$key]= 'int';
							}
							//原为唯一字段
							if($_POST['oldfieldunique'][$tkey][$key]==1&&$_POST['oldfieldunique'][$tkey][$key]!=$_POST['fieldunique'][$tkey][$key]){
								//去掉唯一索引
								$unsql="ALTER TABLE ".$_POST['ename'][$tkey]." DROP INDEX ".$val;
								$unique="";
							}	
							if($_POST['fieldtype'][$tkey][$key]=="longtext"){
								$sql="ALTER TABLE ".$_POST['ename'][$tkey]." CHANGE ".$_POST['oldfieldname'][$tkey][$key]." ".$val." ".$_POST['fieldtype'][$tkey][$key].$unique."  COMMENT '{$_POST['fieldtitle'][$tkey][$key]}'";
							}else{
								$sql="ALTER TABLE ".$_POST['ename'][$tkey]." CHANGE ".$_POST['oldfieldname'][$tkey][$key]." ".$val." ".$_POST['fieldtype'][$tkey][$key]."(".$_POST['fieldlength'][$tkey][$key].")  ".$unique."  COMMENT '{$_POST['fieldtitle'][$tkey][$key]}'";
							}
							//修改字段信息
							$MisDynamicDatabaseSubResult=$MisDynamicDatabaseSubDao->where("id=".$_POST['fieldid'][$tkey][$key])->save($subDate);
							//查询当前表单复用表
							if($MisDynamicDatabaseChoiseMasList){
								foreach ($MisDynamicDatabaseChoiseMasList as $chokey=>$choval){
									$choDate=$subDate;
									$choDate['masid']=$choval['id'];
									$choDate['tablename']=$choval['tablename'];
									$choDate['formid']=$choval['formid'];
									unset($choDate['title']);
									$MisDynamicDatabaseAppendSubResult=$MisDynamicDatabaseSubDao->where("masid=".$choval['id']." and formid=".$choval['formid']." and field='".$_POST['oldfieldname'][$tkey][$key]."'")->save($choDate);
								}
							}
							logs('C:'.__CLASS__.' L:'.__LINE__."建模[修改页面][修改表单物理表字段记录信息]【{$this->nodeName}】DB操作 sql[{$MisDynamicDatabaseSubDao->getLastSql()}]");
							if(false === $MisDynamicDatabaseSubResult){
								$msg = "更新表字段信息失败！".$MisDynamicDatabaseSubDao->getDBError().' '.$MisDynamicDatabaseSubDao->getLastSql();
								throw new NullDataExcetion($msg);
							}
							
							//修改字段属性表
							unset($prodate['sort']);
							//去掉是否显示属性
							unset($prodate['isshow']);
							$MisDynamicFormProperyResult=$MisDynamicFormProperyDao->where("tplid={$tplid}  and  formid={$formid} and ids=".$_POST['fieldid'][$tkey][$key])->save($prodate);
							if($MisDynamicDatabaseChoiseMasList){
								foreach ($MisDynamicDatabaseChoiseMasList as $chokey=>$choval){
									$choprodate=$prodate;
									$choprodate['tplid']=getFieldBy($choval['formid'], "formid", "id", "mis_dynamic_form_template");
									$choprodate['formid']=$choval['formid'];
									unset($choprodate['title']);
									$MisDynamicDatabaseAppendSubResult=$MisDynamicFormProperyDao->where("formid={$choval['formid']} and tplid={$choprodate['tplid']} and fieldname='{$_POST['oldfieldname'][$tkey][$key]}'")->save($prodate);
								}
							}
							logs('C:'.__CLASS__.' L:'.__LINE__."建模[修改页面][修改组件记录]【{$this->nodeName}】DB操作 sql==========[{$MisDynamicFormProperyDao->getLastSql()}]");
							if(false === $MisDynamicFormProperyResult){
								$msg = "更新组件信息失败！".$MisDynamicFormProperyDao->getDBError().' '.$MisDynamicFormProperyDao->getLastSql();
								throw new NullDataExcetion($msg);
							}
							
							
						}
					}
					if(!$_POST['fieldshow'][$tkey][$key]){
						//如果不存在赋值为0
						$_POST['fieldshow'][$tkey][$key]=0;
					}
					//原显示 现为不显示
					if($_POST['oldfieldshow'][$tkey][$key]==1&&$_POST['oldfieldshow'][$tkey][$key]!=$_POST['fieldshow'][$tkey][$key]){
						//删除组件属性表中 当前模板勾掉字段
						$MisDynamicFormProperyResult=$MisDynamicFormProperyDao->where("tplid={$tplid} and  formid={$formid}  and  ids=".$_POST['fieldid'][$tkey][$key])->delete();
						logs('原显示 现为不显示'.$MisDynamicFormProperyDao->getLastsql(),'propertydeleterecode');
						
						if(false === $MisDynamicFormProperyResult){
							$msg = "删除组件失败！".$MisDynamicFormProperyDao->getDBError().' '.$MisDynamicFormProperyDao->getLastSql();
							throw new NullDataExcetion($msg);
						}
						
					}
					logs('C:'.__CLASS__.' L:'.__LINE__."建模[修改页面][组件记录中追加字段判断错误检查用数据源]【{$this->nodeName}】[{$this->pw_var_export($_POST)}]");
					
					//原不显示 现为显示
					if(isset($_POST['oldfieldshow'][$tkey][$key])&&$_POST['oldfieldshow'][$tkey][$key]==0&&$_POST['oldfieldshow'][$tkey][$key]!=$_POST['fieldshow'][$tkey][$key]){
						//取得最大sort
						$propertybjMap['formid']= $formid;
						$addshowmaxsort = $MisDynamicFormProperyDao->where($propertybjMap)->max('sort');
						// lookup时默认生成org值
						if($_POST['fieldcategory'][$tkey][$key]=='lookup'|| $_POST['fieldcategory'][$tkey][$key]=='lookupsuper'){
							$prodate['lookupgrouporg']='orgDBC'.$_POST['fieldid'][$tkey][$key];
						}
						// 下拉框返写 nbmxkj@20150817
						if($_POST['fieldcategory'][$tkey][$key]=='select'){
							$prodate['dropbackkey']=$_POST['fieldname'][$tkey][$key];
						}
						//添加入propety表
						$prodate['formid']=$formid;
						$prodate['category']=$_POST['fieldcategory'][$tkey][$key];
						$prodate['ids']=$_POST['fieldid'][$tkey][$key];
						$prodate['tplid']=$tplid;
						$prodate['sort']=$addshowmaxsort+1;
						$prodate['titlepercent']=$this->privateProperty[$_POST['fieldcategory'][$tkey][$key]]['titlepercent']['default'];
						$prodate['contentpercent']=$this->privateProperty[$_POST['fieldcategory'][$tkey][$key]]['contentpercent']['default'];
						if($choiseformid){
							$isforeignfield=getFieldBy($val,"fieldname", "id", "mis_dynamic_form_propery", "formid",$choiseformid);
							if(!$isforeignfield){
								$msg = "修改组件属性失败,复用主表中【{$_POST['fieldtitle'][$tkey][$key]}】状态为不显示需改为显示后再执行此操作！！";
								throw new NullDataExcetion($msg);
							}
							$prodate['isforeignfield']=$isforeignfield;
						}
						$MisDynamicFormProperyResult=$MisDynamicFormProperyDao->add($prodate);
						logs('C:'.__CLASS__.' L:'.__LINE__."建模[修改页面][组件记录中追加字段]【{$this->nodeName}】DB操作 sql[{$MisDynamicFormProperyDao->getLastSql()}]");

						if(false === $MisDynamicFormProperyResult){
							$msg = "修改组件属性失败！".$MisDynamicFormProperyDao->getDBError().' '.$MisDynamicFormProperyDao->getLastSql();
							throw new NullDataExcetion($msg);
						}
					}
					if(!$_POST['oldfieldname'][$tkey][$key]){
						//不存在该字段 新增该字段
						if($_POST['ischoise']!=1){
							if($_POST['fieldtype'][$tkey][$key]=='date'){
								$_POST['fieldtype'][$tkey][$key] = 'int';
							}
							if($_POST['fieldtype'][$tkey][$key]=="longtext"){
								$sql=" ALTER TABLE ".$_POST['ename'][$tkey]." ADD  COLUMN ".$val." ".$_POST['fieldtype'][$tkey][$key].$unique." COMMENT '{$_POST['fieldtitle'][$tkey][$key]}'";
							}else{
								$sql=" ALTER TABLE ".$_POST['ename'][$tkey]." ADD  COLUMN ".$val." ".$_POST['fieldtype'][$tkey][$key]."(".$_POST['fieldlength'][$tkey][$key].")  ".$unique." COMMENT '{$_POST['fieldtitle'][$tkey][$key]}'";
							}
						}
						$MisDynamicDatabaseSubResult=$MisDynamicDatabaseSubDao->add($subDate); 
						//查询当前表单复用表
						if($MisDynamicDatabaseChoiseMasList){
							foreach ($MisDynamicDatabaseChoiseMasList as $chokey=>$choval){
								$subDate['masid']=$choval['id'];
								$subDate['tablename']=$choval['tablename'];
								$subDate['formid']=$choval['formid'];
								$subDate['souceid']=$MisDynamicDatabaseSubResult;
								$MisDynamicDatabaseAppendSubResult=$MisDynamicDatabaseSubDao->add($subDate);
							}
						}

						if(false === $MisDynamicDatabaseSubResult){
							$msg = "修改组件属性失败！".$MisDynamicDatabaseSubDao->getDBError().' '.$MisDynamicDatabaseSubDao->getLastSql();
							throw new NullDataExcetion($msg);
						}
						
						
						if($_POST['fieldshow'][$tkey][$key]==1){
							//取得最大sort
							$propertybjMap['formid']= $formid;
							$addmaxsort = $MisDynamicFormProperyDao->where($propertybjMap)->max('sort');
							// lookup时默认生成org值
							if($_POST['fieldcategory'][$tkey][$key]=='lookup'|| $_POST['fieldcategory'][$tkey][$key]=='lookupsuper'){
								$prodate['lookupgrouporg']='orgDBC'.$MisDynamicDatabaseSubResult;
							}
							//添加入propety表
							$prodate['formid']=$formid;
							$prodate['ids']=$MisDynamicDatabaseSubResult;
							$prodate['sort']=$addmaxsort+1;
							$prodate['tplid']=$tplid;
							$prodate['titlepercent']=$this->privateProperty[$_POST['fieldcategory'][$tkey][$key]]['titlepercent']['default'];
							$prodate['contentpercent']=$this->privateProperty[$_POST['fieldcategory'][$tkey][$key]]['contentpercent']['default'];
							$MisDynamicFormProperyResult=$MisDynamicFormProperyDao->add($prodate);
							//此处为添加一个字段不添加到组件记录表
// 							if($MisDynamicDatabaseChoiseMasList){
// 								foreach ($MisDynamicDatabaseChoiseMasList as $chokey=>$choval){
// 									$prodate['tplid']=getFieldBy($choval['formid'], "formid", "id", "mis_dynamic_form_template");
// 									$prodate['isforeignfield']=$MisDynamicFormProperyResult;
// 									$prodate['formid']=$choval['formid'];
// 									$MisDynamicDatabaseSubResult=$MisDynamicFormProperyDao->add($prodate);
// 								}
// 							}
							
							if(false === $MisDynamicFormProperyResult){
								$msg = "添加组件属性失败！".$MisDynamicFormProperyDao->getDBError().' '.$MisDynamicFormProperyDao->getLastSql();
								throw new NullDataExcetion($msg);
							}
							
							
						}
					}
					if($_POST['ischoise']!=1){
						$result=$MisDynamicFormManageModel->query($sql);
					}
				}else{
					if(in_array($val,array_values($fieldnameList))){
							$msg = "'".$tval."'表字段'".$val."'构建重复,请查证后再提交!";
							throw new NullDataExcetion($msg);
					}else{
						$listArr[]=$subDate;
						$fieldnameList[]=$cval;
						if($_POST['fieldtype'][$tkey][$key]=="longtext"){
							$ceatechild_html.="\r\n\t`".$val."` ".$_POST['fieldtype'][$tkey][$key]." DEFAULT NULL ".$unique." COMMENT '".$_POST['fieldtitle'][$tkey][$key]."', ";
						}else{
							$ceatechild_html.="\r\n\t`".$val."` ".$_POST['fieldtype'][$tkey][$key]."(".$_POST['fieldlength'][$tkey][$key].")  DEFAULT NULL ".$unique." COMMENT '".$_POST['fieldtitle'][$tkey][$key]."', ";
						}
					}
				}
			}
			$newarr2=array(
			$_POST['tempname']=>$arr2,
			);
			if($_POST['ename'][$tkey]){//已有表名
				$tablename=$_POST['ename'][$tkey];
			}else{//新建表名
				$tablename=$this->looktablename(1, $primaryname);
			}
			if(!$_POST['ename'][$tkey]){
				foreach ($this->defaultField as $dk=>$dv){
					$ceatetable_html .="\r\n\t`".$dk."` {$dv['type']}({$dv['length']}) ".($dv['default']!=''?"DEFAULT '{$dv['default']}'":"")."{$dv['isnull']} {$dv['desc']} ,";
				}
				//获取子表名称
				$curTableName=$tablename;
				$ceatechild_html_s="CREATE TABLE IF NOT EXISTS `".$curTableName."` (";
				$ceatechild_html_s.="`id` int(11) NOT NULL AUTO_INCREMENT  COMMENT 'ID'";
				$ceatechild_html_e=",PRIMARY KEY (`id`)";
				$ceatechild_html_e.=",`masid` int(11) COMMENT '关联动态表单数据ID',";
				$ceatechild_html.="KEY `delete_{$curTableName}` (`masid`),";
				$ceatechild_html.="CONSTRAINT `delete_{$curTableName}` FOREIGN KEY (`masid`) REFERENCES `{$primaryname}` (`id`) ON DELETE CASCADE";
				// 外键删除功能 end
				$sqlStr = $ceatechild_html_s.$ceatechild_html_e.$ceatechild_html.")comment='动态表单:{$primaryname} 内嵌表 ".$primaryname."'";
				logs('C:'.__CLASS__.' L:'.__LINE__."创建子表=====【{$this->nodeName}】DB操作 sql[{$sqlStr}]");
				//创建子表
				$childresult=$MisDynamicFormManageModel->query($sqlStr);
				$errorStr = $MisDynamicFormManageModel->getDbError();
				//添加子表数据
				$date=array();
				$date['tablename']=$curTableName;//从表名称
				//主表名称
				$date['primaryname']=$primaryname;
				//获取字段列表(序列化)
				$date['filedlist']=serialize($this->gettableArr($tkey,$creatableList));
				$childResult=$AutoPrimarySubModel->add($date);
				
				if(false === $childResult){
					$msg = "从表存储信息失败！".$AutoPrimarySubModel->getDBError().' '.$AutoPrimarySubModel->getLastSql();
					throw new NullDataExcetion($msg);
				}
				
				
				/**以下为新增表*/
				$masData=array();
				$masData['tablename']=$curTableName;
				$masData['tabletitle']=$_POST['tablename'][$tkey];
				$masData['formid']=$_POST['formid'];
				$alist[$tkey]=$masData;
				$alist[$tkey]['list']=$listArr;
				//调用插入数据表
				$this->insertDatebase($alist,$formid,$this->curnode);
				/**新增表end*/
			}
			if($_POST['masid'][$tkey]){
				//修改表title
				$tableMas=array();
				$tableMas['tabletitle']=$tval;
				$tableMas['id']=$_POST['masid'][$tkey];
				$tableMas['modelname']=$this->nodeName;
				$MisDynamicDatabaseMasResult=$MisDynamicDatabaseMasDao->save($tableMas);
				if(false === $MisDynamicDatabaseMasResult){
					$msg = "修改主表标题错误！".$MisDynamicDatabaseMasDao->getDBError().' '.$MisDynamicDatabaseMasDao->getLastSql();
					throw new NullDataExcetion($msg);
				}
				
			}
		}
		$this->modifyOriginalConfig($arr1,$newarr2,$arr3);
		$alist=array();
		$alist=array(
				'datebase'=>$this->gettableArr('',$creatableList,1,$_POST['datasouce']),
		);
		if($_POST['datasouce']){
			//修改数据源
			 $MisDynamicDatabaseSubDao->where("field='".$_POST['datasouce']."'  and formid=".$formid)->setField("isdatasouce",1);
		}else{
			//查询原单据数据源
			$olddtmap=array();
			$olddtmap['formid']=$formid;
			$olddtmap['isdatasouce']=1;
			$MisDynamicDatabaseSubDao->where($olddtmap)->setField("isdatasouce",0);
		}
		//$this -> success("操作成功", '', array('type' => 1, 'nodename' => $this -> nodeName ,'tablename'=>$this->tableName,'tpltype'=>$this->tpltype));
		// 获取到当前action 在表单记录表中的数据，并将审批状态改为当前状态。
		// $data = $MisDynamicFormManageModel->where("`actionname`='{$nodename}'")->field('id')->find(); // 最安全的修改
		$saveData['isaudit'] = $this->isaudit;
		$saveData['isreminds'] = $_POST['isreminds']?$_POST['isreminds']:0;
		// $MisDynamicFormManageModel->where("`id`={$data['id']} and `actionname`='{$nodename}'")->save($saveData); // 最安全的修改
		$MisDynamicFormManageModel->where("`actionname`='{$this -> nodeName}'")->save($saveData);
		// 改node表中的属性
		// isprocess
		$nodeModelObj = D('Node');
		$nodeSaveData['isprocess'] = $this->isaudit;
		/*
		 * 处理系统字段组件化问题。
		 * 系统字段的组件固定为文本框组件。
		 * 不入字段记录表，组件记录直接写入组件属性表中。
		 *  */
		$MisDynamicFormProperyModel= M('MisDynamicFormPropery');
		// 处理系统字段组件化问题。
		$systemFieldData = $_POST['system'];
		logs('C:'.__CLASS__.' L:'.__LINE__.'修改时：处理系统字段组件化问题：源数组。'.$this->pw_var_export($systemFieldData),date('Y-m-d' , time()).'_data.log');
		$validateFieldCount=0;
		if($systemFieldData){
			$propertyObj = M('mis_dynamic_form_propery');
			$propertyMap['formid']= $formid;
			$maxsort = $propertyObj->where($propertyMap)->max('sort');
			$search=array(
					'#id#','#name#'
			);
			$replace=array(
					$formid,$fieldname
			);
			foreach ($systemFieldData['fieldname'] as $k=>$v){
				if($systemFieldData['fieldshow'][$k] == 1){
					$maxsort++;
						$proerytyCheckIsValidate['formid']=$formid;
						$proerytyCheckIsValidate['fieldname']=$systemFieldData['fieldname'][$k];
						$validateFieldCount = $MisDynamicFormProperyModel->where($proerytyCheckIsValidate)->find();
						 
						//$sql="SELECT COUNT(*) as ordercount FROM `mis_dynamic_form_propery` WHERE formid={$formid} AND fieldname='{$systemFieldData['fieldname'][$k]}'";
						//$checkorderno  = $MisDynamicFormProperyModel->query($sql);
						logs('C:'.__CLASS__.' L:'.__LINE__.'修改时：检查字段是否存在'.$systemFieldData['fieldname'][$k].'问题：属性记录表插入记录：'.$MisDynamicFormProperyModel->getLastSql());
						logs('C:'.__CLASS__.' L:'.__LINE__.'修改时：检查字段是否存在数据'.$this->pw_var_export($validateFieldCount));
						if(!$validateFieldCount){
							unset($propdata);
							$propdata['ids']=0;
							$propdata['category'] = $systemFieldData['fieldcategory'][$k];
							$propdata['sort']=$maxsort;
							$propdata['fieldname'] = $systemFieldData['fieldname'][$k];
							$propdata['title'] = $systemFieldData['fieldtitle'][$k];
							$propdata['createid'] = $_SESSION[C('USER_AUTH_KEY')][$k];
							$propdata['createtime']=time();
							$propdata['formid']=$formid;
							$propdata['tplid'] = $tplid;
							$propdata['titlepercent']=1;
							$propdata['contentpercent'] = 3;
							if( is_array( $this->system_field_config[$systemFieldData['fieldname'][$k]] ) ){
								//替换lookuporg
								$template = str_replace ( $search, $replace, $this->system_field_config[$systemFieldData['fieldname'][$k]] );
								$propdata =array_merge($propdata , $template);
							}
							$propdata['dbname'] = $this->getTrueTableName();
							logs('C:'.__CLASS__.' L:'.__LINE__.'修改时：处理系统字段组件化'.$systemFieldData['fieldshow'][$k].'问题：被构建的DB表结构数组。'.$this->pw_var_export($propdata),date('Y-m-d' , time()).'_data.log');
							$MisDynamicFormProperyResult = $MisDynamicFormProperyModel->add($propdata);
							
							if(false === $MisDynamicFormProperyResult){
								$msg = "处理系统字段组件化：插入失败！".$MisDynamicFormProperyModel->getDBError().' '.$MisDynamicFormProperyModel->getLastSql();
								throw new NullDataExcetion($msg);
							}
							logs('C:'.__CLASS__.' L:'.__LINE__.'修改时：处理系统字段组件化'.$systemFieldData['fieldshow'][$k].'问题：属性记录表插入记录：'.$MisDynamicFormProperyModel->getLastSql());

						}
				}
			}
		}
		
		// 在做操作前先检查物理表是否存在,不存时就先创建表
		$validateTable = $this-> validateTable( $this->getTrueTableName());
		if(!$validateTable){
			$this->createDataTable($formid);
		}
		
		
		// 当前修改方式为不可靠的修改。
		$nodeModelResult = $nodeModelObj->where("`name`='{$this -> nodeName}'")->save($nodeSaveData);
		
		if(false === $nodeModelResult){
			$msg = "节点名称修改失败！".$nodeModelObj->getDBError().' '.$nodeModelObj->getLastSql();
			throw new NullDataExcetion($msg);
		}
		
		return  array(
						'type' => 1,
						'nodename' => $this -> nodeName ,
						'isaudit'=>$this->isaudit,
						'tpltype'=>$_POST['nbm_tpl_type'],
						'previewtype'=>$_POST['nbm_tpl'],
						'tablename'=>$this->tableName ,
						'curnode'=>$this->curnode);
	}
	/**
	 * @Title: insertAutoForm
	 * @Description: todo(数据添加控制器)
	 * @author quqiang
	 * @date 2014-8-18 下午4:52:52
	 * @throws
	 */
function editAutoForm() {
		if (! $_POST) {
			$id = $_GET ['id'];
			$model = D ( 'MisDynamicFormManage' );
			$filedModel = M ( 'MisDynamicFormField' );
			$formInfo = $model->where ( 'id=' . $id )->find ();
			$this->nodeName = $formInfo ['actionname'];
			$this->nodeTitle = $formInfo ['actiontitle'];
			$this->assign ( 'action', $this->nodeName );
			// 现有组件
			$data = $filedModel->where ( 'formid=' . $id )->select ();
			
			// 生成html
			$html = self::createContrlHtml ( $data );
			$this->assign ( 'html', $html );
			$this->assign ( 'vo', $formInfo );
			$this->assign ( 'reservedField', $this->systemUserField );
			$this->assign ( 'NBM_COMMON_PROPERTY', $this->publicProperty );
			$this->assign ( 'NBM_COMMON_PROPERTY_JSON', json_encode ( $this->publicProperty ) );
			$this->assign ( 'NBM_PRIVATE_PROPERTY_JSON', json_encode ( $this->privateProperty ) );
			$this->assign ( 'controlls', $this->controls );
			$this->getNodeList ();
			$this->display ( 'MisDynamicFormManage:autoformindex' );
		} else {
			
			// //////////////////////////////////////////////
			// 通用表单改版 //
			// //////////////////////////////////////////////
			// 此处新增mis_dynamic_form_indatatable
			$mis_dynamic_form_indatatable = M ( "mis_dynamic_form_indatatable" );
			$misdynamicform = M ( 'mis_dynamic_form_manage' );
			// 重新设置当前action的真实表名
			$this->tableName = $this->getTrueTableName ();
			
			$nodeModel = D ( 'Node' );
			// 当前修改方式为不可靠的修改。
			$this->isaudit = $nodeModel->where ( "`name`='{$this->nodeName}'" )->getField ( "isprocess" );
			$controlProperty = $this->getParame ();
			
			$formData = $misdynamicform->where ( "`actionname`='{$this->nodeName}'" )->find ();
			$this->formtype = $formData ['tpl'];
			$this->isrecord = $formData['isrecord'];
			//$formTypeData = explode ( '#', $formData ['tpl'] );
			// 开始生成代码
			if(!$this->isrecord){
				try {
					$this->createCode ($controlProperty , $this->nodeName, $formData ['tpl'], $this->isaudit );
				} catch ( Exception $e ) {
					$this->error ( $e->getMessage());
				}
			}
			
			$this->success ( '修改成功', '', array (
					'type' => 1,
					'nodename' => $this->nodeName,
					'isaudit' => $this->isaudit,
					'tablename' => $this->tableName 
			) );
			
			
		}
	}
	/**
	 * @Title: lookupSetAccess
	 * @Description: todo(查看授权)
	 * @author renling
	 * @date 2014-8-18 下午4:52:52
	 * @throws
	 */
	public function lookupSetAccess(){
		//获取当前模型名称
		$actionname=$_REQUEST['actionname'];
		//获取当前模型标题
		$actiontitle=$_REQUEST['actiontitle'];
		//頁面加載方法
		if(!$_POST){
			//获取当前模型的所有public 方法
			$class=$actionname."Action";
			$Extendclass=$actionname."ExtendAction";
			//开启反射
			$my_object = new ReflectionClass($class);
			$class_methods = $my_object->getMethods(ReflectionMethod::IS_PUBLIC);
			$class_methods=obj2arr($class_methods);
			//查询既定方法
			$path = DConfig_PATH.'/System/notauth.php';
			require  $path;
			//读取无需授权方法
			$noaccessList=require DConfig_PATH.'/System/noaccessmode.php';
			foreach($class_methods as $key => $val){
				if($val['class']!=$class){
					if($val['class']!=$Extendclass){
						unset($class_methods[$key]);
					}
				}
				if( substr($val['name'],0,1) == "_" ||  substr($val['name'],0,6) == "lookup" || substr($val['name'],0,6) == "combox" ) {
					unset($class_methods[$key]);
				}
				//排除配置文件中无需授权的方法
				if(in_array($val['name'],explode(',',D('User')->getConfNotAuth(1)))){
					unset($class_methods[$key]);
				}
				if(in_array($val['name'], array_keys($notauth['DEFAULT_AUTHORIZE']))){
					unset($class_methods[$key]);
				}
			}
			//加入既定的方法 DEFAULT_AUTHORIZE
			foreach ($notauth['DEFAULT_AUTHORIZE'] as  $dkey=>$dval){
				$class_methods[]=array(
						'name'=>$dval['name'],
						'title'=>$dval['title'],
						'class'=>$class,
						'category'=>$dval['category']
				);
			}
			//查看该节点是否已有四级节点
			$NodeModel=D('Node');
			$nodeid=getFieldBy($actionname,'name','id','node');
			$NodeList=$NodeModel->where("status=1 and  level=4 and pid=".$nodeid)->getField("name,category");
			$NodeTitleList=$NodeModel->where("status=1 and  level=4 and pid=".$nodeid)->getField("name,title");
			$this->assign('nodelist',$NodeList);
			$this->assign("NodeTitleList",$NodeTitleList);
			$this->assign("noaccessList",$noaccessList[$actionname]);
			$this->assign("actiontitle",$actiontitle);
			$this->assign("actionname",$actionname);
			$this->assign("class_methods",$class_methods);
			$this->display();
		}else{
			//添加入權限  组合数组
			$name=$_POST['name'];
			$class_methods=array();
			$notaccessList=array();
			foreach ($name as $key=>$val){
				if($_POST['iscreate'][$key]){
					$class_methods[]=array(
							'name'=>$val,
							'title'=>$_POST['title'][$key],
							'class'=>$actionname,
							'category'=>$_POST['category'][$key],
					);
				}else{
					$notaccessList[$val]=array(
							'name'=>$val,
							'title'=>$_POST['title'][$key],
							'class'=>$actionname,
							'category'=>$_POST['category'][$key],
					);
				}
			}
			$AutoformModel=D("Autoform");
			$AutoformModel->Setnoaccessmode($notaccessList,$actionname);
			if($class_methods)
			{
				$this->addNodeAccess($class_methods);
			}
			$this->success("添加成功!");
		}
	}
	/**
	 * 加入四级节点
	 */
	private function addNodeAccess($class_methods){
		// 		$class=$this -> nodeName."Action";
		// 		$Extendclass=$this -> nodeName."ExtendAction";
		// 		//开启反射
		// 		$my_object = new ReflectionClass($class);
		// 		$class_methods = $my_object->getMethods(ReflectionMethod::IS_PUBLIC);
		// 		$class_methods=$this->obj2arr($class_methods);
		// 		//查询既定方法
		// 		require  ROOT . '/Conf/notauth.php';
		// 		foreach($class_methods as $key => $val){
		// 			if($val['class']!=$class){
		// 				if($val['class']!=$Extendclass){
		// 					unset($class_methods[$key]);
		// 				}
		// 			}
		// 			if( substr($val['name'],0,1) == "_" ||  substr($val['name'],0,6) == "lookup" || substr($val['name'],0,6) == "combox" ) {
		// 				unset($class_methods[$key]);
		// 			}
		// 			//排除配置文件中无需授权的方法
		// 			if(in_array($val['name'],explode(',',D('User')->getConfNotAuth(1)))){
		// 				unset($class_methods[$key]);
		// 			}
		// 			if(in_array($val['name'], array_keys($notauth['DEFAULT_AUTHORIZE']))){
		// 				unset($class_methods[$key]);
		// 			}
		// 		}
		// 		//加入既定的方法 DEFAULT_AUTHORIZE
		// 		foreach ($notauth['DEFAULT_AUTHORIZE'] as  $dkey=>$dval){
		// 			$class_methods[]=array(
		// 					'name'=>$dval['name'],
		// 					'title'=>$dval['title'],
		// 					'class'=>$class,
		// 			);
		// 		}


		//查询该节点所有四级节点
		$nodeModel=D("Node");
		$pid=getFieldBy($_POST['actionname'],'name','id','node');
		$group_id=getFieldBy($pid,"id","group_id","node");
		$nodeMap=array();
		$nodeMap['status']=1;
		$nodeMap['pid']=$pid;
		$nodeMap['level']=4;
		$nodeList=$nodeModel->where($nodeMap)->getField("id,name");
		$nodeModel=D('Node');
		$roleModel=D('Role');
		$accessModel=D('Access');
		//根据pid获取模块节点信息
		$vo = $nodeModel->find($pid);
		//根据模块的pid获取面板信息或者空面板信息
		$vo2 = $nodeModel->find($vo['pid']);
		//插入节点表 $cnode[]=array('name'=>'delete','title'=>'删除','type'=>4,'pid'=>$list,'group_id'=>$groupId,
		//'showmenu'=>1,'icon'=>'-.png',remark=>'自动生成:删除','level'=>4,'category'=>2);
		foreach ($class_methods as $ckey=>$cval){
			if(!in_array($cval['name'],array_values($nodeList))){
				$nodedata=array();
				$nodedata['name']=$cval['name'];
				$nodedata['title']=$cval['title'];
				$nodedata['type']=4;
				$nodedata['pid']=$pid;
				$nodedata['group_id']=$group_id;
				$nodedata['showmenu']=1;
				$nodedata['icon']="-.png";
				$nodedata['remark']="自动生成:".$cval['title'];
				$nodedata['level']=4;
				$nodedata['category']=$cval['category'];
				$result=$nodeModel->add($nodedata);
				if(!$result){
					$this->error("节点插入失败,请联系管理员！");
				}
				//插入access表
				$j=0;
				$role_list = array();
				for($s=1;$s<=5;$s++){
					if($s==1){
						$t="所有";
					}else if($s==2){
						$t="公司";
					}else if($s==3){
						$t="部门";
					}else if($s==5){
						$t="禁用";
					}else{ $t="个人";
					}
					$data=array();
					$data["name"] = $_POST['actiontitle']."_".$t."_".$cval['title'];
					$data["nodetitle"] = $cval['title'];
					$data["nodeidcategory"] = $cval['category'];
					$data["plevels"] = $s;
					$data["nodepid"] = $pid;
					$data["nodeid"] = $result;
					$data["createtime"] = time();
					$data["createid"] = 1;
					$data["sort"] = $j;
					$data["status"] = 1;
					$j++;
					$list = $roleModel->add($data);
					if($list){
						$data["id"] = $list;
						$role_list[] = $data;
					}else{
						$this->error("插入role节点失败，请联系管理员");
					}
				}
				foreach($role_list as $k=>$v){
					//定义4个参数，存放上级id  $pid_type0 节点admin的ID，$pid_type1面板id ，$pid_type2模块ID或者空模块ID ，$pid_type3模块ID
					$pid_type0=$pid_type1=$pid_type2=$pid_type3="";
					if($vo2['type']==2){//kong mokuai
						$vo3 = $nodeModel->find($vo2['pid']);//获取面板
						$pid_type3 = $vo['id'];//模块id
						$pid_type2 = $vo2['id'];//控模块id
						$pid_type1 = $vo3['id'];//面板模块id
						$pid_type0 = $vo3['pid'];//项目id
					}else{//mianban
						$pid_type2 = $vo['id'];//模块id
						$pid_type1 = $vo2['id'];//面板模块id
						$pid_type0 = $vo2['pid'];//项目id
					}
					if($pid_type0!="") $idlist[$v["id"]][] = $pid_type0;
					if($pid_type1!="") $idlist[$v["id"]][] = $pid_type1;
					if($pid_type2!="") $idlist[$v["id"]][] = $pid_type2;
					if($pid_type3!="") $idlist[$v["id"]][] = $pid_type3;
					$data=array();
					$data["role_id"] = $v["id"];
					$data["node_id"] = $v["nodeid"];
					$data["level"]   = 4;
					$data["type"]    = 4;
					$data["pid"]     = $v["nodepid"];
					$data["plevels"] = $v["plevels"];
					$result = $accessModel->add($data);
					if ($result) {
						if ( $idlist ) {
							$result = $roleModel->setGroupActions($v["id"],$idlist[$v["id"]], true);
							if ($result === false) {
								$this->error ( L('_ERROR_') );
							}
						}
					} else {
						$this->error ( L('_ERROR_') );
					}
				}
			}
		}
	}
	
	/**
	 * 生成JS
	 * @Title: createJsText
	 * @Description: todo(这里用一句话描述这个方法的作用) 
	 * @param string $nodeNames		action名称
	 * @param string $tplType		当前表单的模板类型
	 * @author quqiang 
	 * @date 2015年2月6日 下午4:07:13 
	 * @throws
	 */
	private function createJsText($nodeNames='' , $tplType=''){
		// 生成JS功能。
		// 将JS 拆分为两块来进行生成。
		// 1. 函数
		// 2. 动态绑定事件
		// 它们全是字段名称做为Key值 形式存在。方便 时时追加新功能代码。
		// //////////// mdify by nbmxkj@20150208 1948 //////////////////////
		logs ( '开始生成JS', 'js' );
		// 1.获取到当前Action的组件配置文件信息
		$nodeName = $nodeNames ? $nodeNames : $this->nodeName;
		$rands = time().microtime();
		logs ( "开始生成JS nodeNames-{$nodeNames} : node -" . $this->nodeName, 'js' );
		// $model = D('Autoform');
		// $dir = '/autoformconfig/';
		// $path = $dir.$nodeName.'.php';
		// $model->setPath($path);
		// 获取所有节点信息
		$allNodeconfig = $this->getOrderNode ( $this->curnode );
		// 生成两个文件 add edit
		
		$addjsstr = "";
		$editjsstr = "";
		$viewjsstr="";
		
		$addFileName = TMPL_PATH . C ( 'DEFAULT_THEME' ) . "/" . $nodeName . "/add.js";
		$editFileName = TMPL_PATH . C ( 'DEFAULT_THEME' ) . "/" . $nodeName . "/edit.js";
		$viewFildNmae=TMPL_PATH . C ( 'DEFAULT_THEME' ) . "/" . $nodeName . "/view.js";
		
		$addExtendFileName = TMPL_PATH . C ( 'DEFAULT_THEME' ) . "/" . $nodeName . "/addExtend.js";
		$editExtendFileName = TMPL_PATH . C ( 'DEFAULT_THEME' ) . "/" . $nodeName . "/editExtend.js";
		$viewExtendFileName=TMPL_PATH . C ( 'DEFAULT_THEME' ) . "/" . $nodeName . "/viewExtend.js";
		
		
		$add_file_str = "";
		$edit_file_str = "";
		if (file_exists ( $addFileName )) {
			$add_file_str = file_get_contents ( $addFileName );
		}
		if (file_exists ( $editFileName )) {
			$edit_file_str = file_get_contents ( $editFileName );
		}
		$formid = getFieldBy ( $nodeName, "actionname", "id", "mis_dynamic_form_manage" );
		// 查询该表单所有组件
		$MisDynamicFormProperyDao = M ( "mis_dynamic_form_propery" );
		$MisDynamicFormProperyList = $MisDynamicFormProperyDao->where ( "status=1 and formid=" . $formid )->getField ( "fieldname,category" );
		// 查询该表单配置的隐藏显示字段
		$MisDynamicControllRecordDao = M ( "mis_dynamic_controll_record" );
		$str = "";
		$loadStr = "";
		$viewloadStr="";
		$box = "var box = navTab.getCurrentPanel();";
		// 对话框式新增修改
		$box = $this->formtype == 'basisarchivestpl#ltrl' ? "var box = $.pdialog.getCurrent();" : $box;
		$box .="\r\n\tvar formname='{$nodeName}';";
		$box .="\r\n\tbox = $('form[id^=\"'+formname+'\"]',box)?$('form[id^=\"'+formname+'\"]',box):box;";
		
		if ($tplType) {
			$addjsstr = $editjsstr=$viewloadStr = <<<EOF
$(function(){
	initTableWNEW();
    {$box}
EOF;
		} else {
			$addjsstr = $editjsstr=$viewloadStr = <<<EOF
$(function(){
	initTableWNEW();
    {$box}
EOF;
		}
		$idForKeyArr = array ();
		foreach ( $allNodeconfig as $k => $v ) {
			$property = $this->getProperty ( $v ['catalog'] );
			$idForKeyArr [$v [$property ['id'] ['name']]] = $v;
		}
		// 新增页面JS构成
		$fieldArr = array ();
		// 修改页面JS构成
		$fieldEditArr = array ();
		foreach ( $allNodeconfig as $k => $v ) {
			$property = $this->getProperty ( $v ['catalog'] );
			
			
			// /////////////////////////////////////////////////////////////////////////////////////////
			// 文本框-固定显示-附加条件
			// /////////////////////////////////////////////////////////////////////////////////////////
// 			if ($v [$property ['catalog'] ['name']] == 'text'){
// 				echo '<pre>';
// 				print_r($v);
// 				echo '</pre>';
// 			}
			if ($v [$property ['catalog'] ['name']] == 'text' && $v [$property ['additionalconditions'] ['name']]) {
				unset ( $fieldname );
				$fieldname = $v [$property ['fields'] ['name']];
				try {
					$conditions = $v [$property ['conditions'] ['name']];
					$table = $v [$property ['subimporttableobj'] ['name']];
					$showField = $v [$property ['subimporttablefieldobj'] ['name']];
					$isEditShow = $v [$property ['subimporttablefield2obj'] ['name']];
					// 附加条件
					$textAppendCondtionConfigStr = $v [$property ['additionalconditions'] ['name']];
					if ($table && $showField) {
						$conditionsOperated = "";
						if ($conditions) {
							$conditionsOperated .= $conditions;
						}
						$textAppendCondtionConfigArr = appendConditionConfigResolve ( $textAppendCondtionConfigStr );
						if ($conditions && $textAppendCondtionConfigStr) {
							$configResilvedArr = getAppendCondition ( $vo, $textAppendCondtionConfigArr );
							$conditionsOperated .= " and " . parameReplace ( "&#39;", "'", getAppendCondition ( $vo, $textAppendCondtionConfigArr ) );
						} else if (! $conditions && $textAppendCondtionConfigStr) {
							$conditionsOperated .= " " . parameReplace ( "&#39;", "'", getAppendCondition ( $vo, $textAppendCondtionConfigArr ) );
						} else {
							// $conditionsOperated .="'";
						}
			
						$appendCondtionSouce = unserialize ( base64_decode ( $textAppendCondtionConfigStr ) );
						$formControllConditionCellction = unserialize ( $appendCondtionSouce ['proexp'] );
						$systemDefaultFieldConditionCellction = unserialize ( $appendCondtionSouce ['sysexp'] );
			
						// 获取真实的字段名称。
						$formControllConditionArr = array ();
						foreach ( $formControllConditionCellction as $idforkey => $idforVal ) {
							$id = $idforkey;
							$idforVal = $idforVal == '-1' ? $idForKeyArr [$idforkey] [$property ['fields'] ['name']] : $idforVal;
							$idforkey = $idForKeyArr [$idforkey] [$property ['fields'] ['name']];
							$formControllConditionArr [] = array (
									$idforkey => $idforVal
							);
							$curEventTriggeredObj = $idforkey;
							switch ($idForKeyArr [$id] ['catalog']) {
								case 'text' :
								case 'lookup' :
								case 'select' :
										
									$eventBindCode = <<<EOF
									
	$('[name="{$curEventTriggeredObj}"]',box).on('change',function(){
		$('input[name="{$fieldname}"]',box).trigger('staticshowoprate{$fieldname}');
	});
EOF;
									if ($isEditShow) {
										$fieldEditArr [$idforVal] ['textappendcondition' . $fieldname] [] = $fieldArr [$idforVal] ['textappendcondition' . $fieldname] [] = $eventBindCode;
									} else {
										$fieldArr [$idforVal] ['textappendcondition' . $fieldname] [] = $eventBindCode;
									}
									break;
								default :
									$eventBindCode = <<<EOF
			
											$('[name="{$curEventTriggeredObj}"]',box).on('click',function(){
											$('input[name="{$fieldname}"]',box).trigger('staticshowoprate{$fieldname}');
										});
EOF;
									if ($isEditShow) {
										$fieldEditArr [$idforVal] ['textappendcondition' . $fieldname] [] = $fieldArr [$idforVal] ['textappendcondition' . $fieldname] [] = $eventBindCode;
									} else {
										$fieldArr [$idforVal] ['textappendcondition' . $fieldname] [] = $eventBindCode;
									}
									break;
							}
							// if($idForKeyArr[$idforkey][$property['fields']['name']])
						}
			
						if (is_array ( $formControllConditionArr )) {
							$parameJSON = json_encode ( $formControllConditionArr );
						} else {
							$parameJSON = "''";
						}
						$eventBindCode = <<<EOF
			
	$('input[name="{$fieldname}"]' , box).on('staticshowoprate{$fieldname}',function(){
		try{
			var ac={$parameJSON};
			var config = {'table':'{$table}','field':'{$showField}'};
			var obj = $(this);
			ac = typeof(ac) == 'string' ? $.parseJSON(ac) : ac;
			ac = $.json2arr(ac);
			var paramSouce = "{$conditionsOperated}";
			var param='';
			if(isNullorEmpty(paramSouce)){
				$.each(ac , function(key , val){
					var vals = $('[name="'+$.key(val)+'"]' , box).val();
					
					if( isNullorEmpty(vals) ){
						var conditionKey = $.value(val);
						var regParam = new RegExp('(\\\s|=)'+conditionKey+'=\'(.*?)\'');
						param = paramSouce.replace(regParam , function(item , item1){
							var regParamVal = new RegExp("\'.*?\'",'g');
							var str =  item.replace(regParamVal,"'"+vals+"'");
							return str;
						});
					}
				});
			}
			if('object'==typeof(config) && param ){
				$.ajax({
					url:TP_APP+'/Common/lookupAjaxAppendCondtionData',
					data:{'table':config.table,'field':config.field,'condition':param,'action':'{$nodeName}','key':'{$fieldname}'},
							dataType:'json',
							type:'post',
							success:function(msg){
								console.log(msg[config.field]);
								if(msg){
									obj.val(msg[config.field]).change();
								}
							}
					});
			}
		}catch(e){
			console.log(e||e.message);
		}
	 });
EOF;
			
						if ($isEditShow) {
							$fieldEditArr [$idforVal] ['textappendcondition' . $fieldname] [] = $fieldArr [$idforVal] ['textappendcondition' . $fieldname] [] = $eventBindCode;
							$fieldEditArr [$idforVal] ['textappendcondition' . $fieldname] [] = $fieldArr [$idforVal] ['textappendcondition' . $fieldname] [] = <<<EOF
			
	// 自动触发
	$('[name="{$fieldname}"]',box).trigger('staticshowoprate{$fieldname}');
EOF;
						} else {
							$fieldArr [$idforVal] ['textappendcondition' . $fieldname] [] = $eventBindCode;
							$fieldArr [$idforVal] ['textappendcondition' . $fieldname] [] = <<<EOF
			
	// 自动触发
	$('[name="{$fieldname}"]',box).trigger('staticshowoprate{$fieldname}');
EOF;
						}
					}
				} catch ( Exception $e ) {
					// exception throws
					$this->error ( $e->__toString () );
				}
			}
			// /////////////////////////////////////////////////////////////////////////////////////////
			// 文本框-固定显示-附加条件 end
			// /////////////////////////////////////////////////////////////////////////////////////////
			
			// /////////////////////////////////////////////////////////////////////////////////////////
			// 组件动态隐藏
			// /////////////////////////////////////////////////////////////////////////////////////////
			
			
			$MisDynamicControllRecordList = $MisDynamicControllRecordDao->where ( "status=1 and formid=" . $formid." and properyid=".$v[$property['id']['name']])->select ();
				if($MisDynamicControllRecordList){
				//if ($kk == "tagevent" && ! empty ( $vv )) {
					$hiddenStr = "";
					$showStr = "";
					//当前组件为lookup时赋初值
					if($v[$property['catalog']['name']]=="lookup"){
						$v[$property['tagevent']['name']]="change";
					}
					$functionName = $nodeName . "_" . $k . "_" . $v[$property['tagevent']['name']];
					//定义事件类型
					$vv=$v[$property['tagevent']['name']];
					// 绑定触发事件，所有组件在使用动态隐藏时都需要，lookup本身不能设置，所以会设置默认事件。不可更改。
					if($vv){
						// 获取该字段类型
						//$fieldcateorg = $MisDynamicFormProperyList [$k]; 
						// 获取组件类型
						$fieldcateorg=$v[$property['catalog']['name']];
						
						if ($fieldcateorg == "select") {
							if ($v[$property['treedtable']['name']]) {
								$loadStr .= "\r\n\t\$(\"[name='" . $k . "']\" , box).on('change' ,function(){";
								$loadStr .= "\r\n\t\t{$functionName}(this);";
								$loadStr .= "\r\n\t});";
								$loadStr .= "\r\n\t\$(\"[name='" . $k . "']\" , box)." . $vv . "()";
							} else {
								$loadStr .= "\r\n\t\$(\"select[name='" . $k . "']\" , box).on('".$vv."' ,function(){";
								$loadStr .= "\r\n\t\t{$functionName}(this);";
								$loadStr .= "\r\n\t});";
								
								$loadStr .= "\r\n\t\$(\"select[name='" . $k . "']\" , box)." . $vv . "()";
							}
						} else if ($cateorg == "checkbox") {
							$loadStr .= "\r\n\t\$(\"input[name='" . $k . "[]']\" , box).on('".$vv."' ,function(){";
							$loadStr .= "\r\n\t\t{$functionName}(this);";
							$loadStr .= "\r\n\t});";
							$loadStr .= "\r\n\t\$(\"input[name='" . $k . "[]']\" , box)." . $vv . "()";
						} else {
							$loadStr .= "\r\n\t\$(\"input[name='" . $k . "']\" , box).on('".$vv."' ,function(){";
							$loadStr .= "\r\n\t\t{$functionName}(this);";
							$loadStr .= "\r\n\t});";
							$loadStr .= "\r\n\t\$(\"input[name='" . $k . "']\" , box)." . $vv . "();";
						}
						// 循环查找是否有该字段的显示与隐藏
						$hiddenList = array ();
						$strjs = "\r\n\tvar val=$(obj).val();";
						$str = "";
						$viewstrjs="\r\n\tvar val=$('.field_".$k."',box).attr('original');";
						$viewstr="";
						$isparseint=false;
						foreach ( $MisDynamicControllRecordList as $key => $val ) {
	// 						if (getFieldBy ( $val ['properyid'], "id", "fieldname", "mis_dynamic_form_propery" ) == $k) {
							if($val['properyid'] == $v[$property['id']['name']]){
								// 拆分需隐藏字段
								$hiddenList = explode ( ",", $val ['resultval'] );
								$showList = explode ( ",", $val ['resultshowval'] );
								//替换字符
								$roleinexp=getSelectByName ( "misdrules", $val ['roleinexp'] );
								$newroleinexp=str_replace (array ('&quot;','&#39;','&lt;','&gt;'), array ('"', "'",'<','>'),$roleinexp);
								if(strpos($newroleinexp,'<')===false&&strpos($newroleinexp,'>')===false&&$isparseint===false){
									//字符串类型
								}else{
									$isparseint=true;
								}
								$str .= "\r\n\tif(val" . $newroleinexp. "'" .$val ['typeval']. "'){";
								$viewstr.="\r\n\tif(val" . $newroleinexp. "'" . $val ['typeval'] . "'){";
								// 组装隐藏代码
								$hidenControlArr='';
								foreach ( $hiddenList as $hkey => $hval ) {
									if ($hval) {
										//$str .= "\r\n\t\t\$(\".field_" . $hval ."\", box).hide();";
										$hidenControlArr[]=".field_" . $hval;
									}
								}
								$str .= join(',',$hidenControlArr) ? "\r\n\t\tvar hidObj = $(\"". join(',',$hidenControlArr) ."\", box);" : "";
								$str .="\r\n\t\tif(typeof(hidObj) != 'undefined'){";
								$str .="\r\n\t\t\thidObj.hide();";
								// 必填的特殊处理代码调用@nbxkj by 20160115 1721
								$str .="\r\n\t\t\tsetReq(hidObj.find(':input'));";
								$str .="\r\n\t\t\thidObj.find(':input').attr('disabled',true);";
								$viewstr.=join(',',$hidenControlArr) ? "\r\n\t\tvar hidObj = $(\"". join(',',$hidenControlArr) ."\", box).hide();" : "";
	// 							$str .="\r\n\t\t\t\$.each(hidObj , function(){";
	// 							$str .="\r\n\t\t\t\tvar obj = $(this).find(':input');";
	// 							$str .="\r\n\t\t\t\tobj.attr('disabled',true);";
	// 							$str .="\r\n\t\t\t});";
								$str .="\r\n\t\t}";
								unset($hidenControlArr);
								// 组装显示代码
								foreach ( $showList as $skey => $sval ) {
									if ($sval) {
										//$str .= "\r\n\t\t\$(\".field_" . $sval . "\", box).show();";
										$hidenControlArr[]=".field_" . $sval;
									}
								}
								$str .= join(',',$hidenControlArr) ? "\r\n\t\tvar showObj =$(\"". join(',',$hidenControlArr) ."\", box);" : "";
								$viewstr.= join(',',$hidenControlArr) ? "\r\n\t\tvar showObj =$(\"". join(',',$hidenControlArr) ."\", box).show();" : "";
								$str .="\r\n\t\tif(typeof(showObj) != 'undefined'){";
								$str .="\r\n\t\t\tshowObj.show();";
								// 必填的特殊处理代码调用@nbxkj by 20160115 1721
								$str .="\r\n\t\t\tsetReq(showObj.find(':input'));";
								$str .="\r\n\t\t\tsetClsReq(showObj.find(':input'));";
								
								$str .="\r\n\t\t\tshowObj.find(':input').attr('disabled',false);";
	// 							$str .="\r\n\t\t\t\$.each(showObj , function(){";
	// 							$str .="\r\n\t\t\t\tvar obj = $(this).find(':input');";
	// 							$str .="\r\n\t\t\t\tobj.attr('disabled',false);";
	// 							$str .="\r\n\t\t\t});";
								$str .="\r\n\t\t}";
								
								
								// 组装窗体加载代码
									
								$str .= "\r\n\t}";
								$viewstr.= "\r\n\t}";
							}
						}
						// if(empty($add_file_str)||!preg_match("/$functionName/",$add_file_str))
						// {
						if($isparseint){
							$strjs.="\r\n\tval=parseInt(val);";
							$viewstrjs.="\r\n\tval=parseInt(val);";
						} 
						$addAndEditJsArr [$k] = "\nfunction " . $functionName . "(obj){\r      {$box}" . $strjs.$str . "\r\n};";
						$viewJsArr [$k] = "$viewstrjs$viewstr";
						// $addjsstr .= "\nfunction ".$functionName."(obj){".$str."\r\n};";
						// }
							
						// if(empty($edit_file_str)||!preg_match("/$functionName/",$edit_file_str))
						// {
						// $editjsstr .= "\nfunction ".$functionName."(obj){".$str."\r\n};";
						// }
					}
				}
				
			// /////////////////////////////////////////////////////////////////////////////////////////
			// 组件动态隐藏	end
			// /////////////////////////////////////////////////////////////////////////////////////////

			// /////////////////////////////////////////////////////////////////////////////////////////
			// lookup附加条件
			// /////////////////////////////////////////////////////////////////////////////////////////
// 			if ($v [$property ['catalog'] ['name']] == 'lookup' && $v [$property ['additionalconditions'] ['name']]) {
// 				unset ( $fieldname );
// 				$fieldname = $v [$property ['fields'] ['name']];
// 				try {
// 					$appendCondtionSouce = unserialize ( base64_decode ( $v ['additionalconditions'] ) );
// 					$formControllConditionCellction = unserialize ( $appendCondtionSouce ['proexp'] );
// 					$systemDefaultFieldConditionCellction = unserialize ( $appendCondtionSouce ['sysexp'] );
						
// 					// 获取真实的字段名称。
// 					$formControllConditionArr = array ();
// 					foreach ( $formControllConditionCellction as $idforkey => $idforVal ) {
// 						$id = $idforkey;
// 						$idforVal = $idforVal == '-1' ? $idForKeyArr [$idforkey] [$property ['fields'] ['name']] : $idforVal;
// 						$idforkey = $idForKeyArr [$idforkey] [$property ['fields'] ['name']];
// 						$formControllConditionArr [] = array (
// 								$idforkey => $idforVal
// 						);
// 						$curEventTriggeredObj = $idforkey;
// 						switch ($idForKeyArr [$id] ['catalog']) {
// 							case 'text' :
// 							case 'lookup' :
// 							case 'select' :
// 								$fieldEditArr [$idforVal] ['appendcondition'] [] = $fieldArr [$idforVal] ['appendcondition'] [] = <<<EOF
			
// 													$('[name="{$curEventTriggeredObj}"]',box).on('change',function(){
// 													$('.field_{$fieldname} .icon-plus',box).trigger('appendcondition');
// 													// 重置条件后将上次的结果清空
// 		$('.field_{$fieldname} .icon-trash',box).trigger('click');
// 										});
// EOF;
// 																break;
// 							default :
// 								$fieldEditArr [$idforVal] ['appendcondition'] [] = $fieldArr [$idforVal] ['appendcondition'] [] = <<<EOF
			
// 									$('[name="{$curEventTriggeredObj}"]',box).on('click',function(){
// 		$('.field_{$fieldname} .icon-plus',box).trigger('appendcondition');
// 					});
// EOF;
// 												break;
// 						}
// 						// if($idForKeyArr[$idforkey][$property['fields']['name']])
// 					}
// 					$parameJSON = json_encode ( $formControllConditionArr );
// 					$fieldEditArr [$idforVal] ['appendcondition'] [] = $fieldArr [$idforVal] ['appendcondition'] [] = <<<EOF
			
// 							$('.field_{$fieldname} .icon-plus',box).bind('appendcondition',function(){
// 							try{
// 			var appendConditionFromControll='{$parameJSON}';
// 			appendConditionFromControll = typeof(appendConditionFromControll) == 'string' ? $.parseJSON(appendConditionFromControll) : appendConditionFromControll;
// 			appendConditionFromControll = $.json2arr(appendConditionFromControll);
// 			var param = $(this).attr('param');
// 			$.each(appendConditionFromControll , function(key , val){
// 				var vals = $('[name="'+$.key(val)+'"]' , box).val();
// 				if(vals != ''){
// 					var conditionKey = $.value(val);
// 					// 动态正则过滤条件
// 					eval("var regParam = /(\\\\s|=)" + conditionKey + "=(\'(.*?)\')/;");
// 					param = param.replace(regParam , function(item){
// 						return item.replace(/\'(.*?)\'/g,'\''+vals+'\'')
// 					});
// 				}
// 			});
// 			$(this).attr('param' , param);
// 		}catch(e){
// 			console.log(e||e.message);
// 		}
// 	 });
// EOF;
// 				} catch ( Exception $e ) {
// 					// exception throws
// 				}
// 			}
			// /////////////////////////////////////////////////////////////////////////////////////////
			// lookup附加条件	end
			// /////////////////////////////////////////////////////////////////////////////////////////
		
			// /////////////////////////////////////////////////////////////////////////////////////////
			// 数据表格中的lookup附加条件
			// /////////////////////////////////////////////////////////////////////////////////////////
			if ($v [$property ['catalog'] ['name']] == 'datatable') {
				unset ( $fieldname );
				$fieldname = $v [$property ['fields'] ['name']];
			
				$filedTag = $property ['fieldlist'] ['name']; // 获取配置信息中的标识名称
			
				$innerTableFieldInfoObj = json_decode ( htmlspecialchars_decode ( $v [$filedTag] ), true ); // 得到字段信息json对象数据
			
				if (is_array ( $innerTableFieldInfoObj )) {
			
					foreach ( $innerTableFieldInfoObj as $key => $val ) {
						if ($val ['fieldshowtype'] == 'lookup') {
							$config = $val ['fieldshowtypeconfig'];
							$config = $config ? unserialize ( base64_decode ( $config ) ) : array ();
							if ($config ['dateconditions']) {
			
								try {
									$appendCondtionSouce = unserialize ( base64_decode ( $config ['dateconditions'] ) );
									$formControllConditionCellction = unserialize ( $appendCondtionSouce ['proexp'] );
									$systemDefaultFieldConditionCellction = unserialize ( $appendCondtionSouce ['sysexp'] );
									$dataTableEventControll = $val ['fieldname'];
									// 获取真实的字段名称。
									$formControllConditionArr = array ();
									foreach ( $formControllConditionCellction as $idforkey => $idforVal ) {
										$id = $idforkey;
										$idforVal = $idforVal == '-1' ? $idForKeyArr [$idforkey] [$property ['fields'] ['name']] : $idforVal;
										$idforkey = $idForKeyArr [$idforkey] [$property ['fields'] ['name']];
										$formControllConditionArr [] = array (
												$idforkey => $idforVal
										);
										$curEventTriggeredObj = $idforkey;
										switch ($idForKeyArr [$id] ['catalog']) {
											case 'text' :
											case 'lookup' :
											case 'select' :
												$fieldEditArr [$idforVal] ['appendcondition'] [] = $fieldArr [$idforVal] ['appendcondition'] [] = <<<EOF
			
	$('[name="{$curEventTriggeredObj}"]',box).on('change',function(){
		$('.field_datatable4',box).trigger('appendcondition{$dataTableEventControll}');
	});
EOF;
												break;
											default :
														$fieldEditArr [$idforVal] ['appendcondition'] [] = $fieldArr [$idforVal] ['appendcondition'] [] = <<<EOF
															
														$('[name="{$curEventTriggeredObj}"]',box).on('click',function(){
														$('.field_{$fieldname} .icon-plus',box).trigger('appendcondition{$dataTableEventControll}');
														});
EOF;
														break;
										}
										// if($idForKeyArr[$idforkey][$property['fields']['name']])
									}
									$parameJSON = json_encode ( $formControllConditionArr );
									$fieldEditArr [$idforVal] ['appendcondition' . $fieldname] [] = $fieldArr [$idforVal] ['appendcondition' . $fieldname] [] = <<<EOF
		
	$('.field_{$fieldname}',box).bind('appendcondition{$dataTableEventControll}',function(){
		try{
			var obj = $(this).find('table>thead').find('th[template_key="lookup"][template_controll="{$dataTableEventControll}"]');
			var appendConditionFromControll='{$parameJSON}';
			appendConditionFromControll = typeof(appendConditionFromControll) == 'string' ? $.parseJSON(appendConditionFromControll) : appendConditionFromControll;
			appendConditionFromControll = $.json2arr(appendConditionFromControll);
			var param = $(obj).attr('template_data');
			param = htmldecode(param);
			if(isNullorEmpty(param)){
				$.each(appendConditionFromControll , function(key , val){
					var vals = $('[name="'+$.key(val)+'"]' , box).val();
					if( isNullorEmpty(vals) ){
						var conditionKey = $.value(val);
						var regParam = new RegExp('(\\\s|=)'+conditionKey+'=\'(.*?)\'');
						param = param.replace(regParam , function(item , item1){
							var regParamVal = new RegExp("\'.*?\'",'g');
							var str =  item.replace(regParamVal,'&#39;'+vals+'&#39;');
							return str;
						});
					}
				});
				$(obj).attr('template_data' , param);
			}
		}catch(e){
			console.log(e||e.message);
									}
									});
EOF;
								} catch ( Exception $e ) {
								}
							}
						}
					}
				}
			}
			// /////////////////////////////////////////////////////////////////////////////////////////
			// 数据表格中的lookup附加条件	end
			// /////////////////////////////////////////////////////////////////////////////////////////
		
			
			// /////////////////////////////////////////////////////////////////////////////////////////
			// 文本框-组件计算公式
			// /////////////////////////////////////////////////////////////////////////////////////////
			if ($v [$property ['calculate'] ['name']]) {
				// 当前字段名称
				unset($fieldname);
				/**
				 * 计算公式生成步骤：
				 * 	按公司计算后的值写入组件为目标生成公式
				 * 		1.查看当前是否有计算公式。
				 * 		2.根据当前的propertyid在公式表中查询出公式详细。
				 * 		3.解析出公式所需要的组件信息。
				 * 		4.生成具体js执行公式
				 */
				// 查询所有js需要变量
				$calculateModel = M ( 'mis_dynamic_calculate' );
				$propertyid = $v [$property ['id'] ['name']];
				$validdigit = $v [$property ['validdigit'] ['name']] ? $v [$property ['validdigit'] ['name']] : 0;
				$fieldname = $v [$property ['fields'] ['name']];
				$map ['propertyid'] = $propertyid;
				// 获取当前字段的谋算公式详细信息
				$calculateList = $calculateModel->where ( $map )->find ();
				// 组件ID格式的计算公式
				$turnformula = $calculateList ['turnformula'];
				// 构成公式组件的ID列表
				$subassembly = $calculateList ['subassembly'];
				// 公式类型
				$type = $calculateList ['type']; // 1:文本公式 2:两个日期差 3日期加 4 文本拼接 
				
				/*		解析公式，将id替换为具体的字段名		*/
				//根据propertyid列表查询组件名称
				if ($type == 3) {
					// 查询用到的组件名称
					$numbermap ['id'] = $subassembly;
					$properyList = $MisDynamicFormProperyDao->where ( $numbermap )->find ();
					$numbername = $properyList ['fieldname'];
				}
				$jingdu = $calculateList ['jingdu'] ? $calculateList ['jingdu'] : 's'; // 日期精度
				$number = $calculateList ['number']; // 日期加的值
				$subassemblyArr = explode ( ',', $subassembly );
				// 替换id
				$fieldnameArr = array ();
				$FormulArr = array ();
				foreach ( $subassemblyArr as $sk => $sval ) {
					$propertymap ['id'] = $sval;
					$properyList = $MisDynamicFormProperyDao->where ( $propertymap )->find ();
					$name = $properyList ['fieldname'];
					// $inputString.=$inputString?',input[name="'.$name.'"]':'input[name="'.$name.'"]';
					$FormulArr [$sval] = $name;
					$fieldnameArr [] = $name;
				}
				$fieldnameArrString = json_encode ( $fieldnameArr );
				$turnFormulArr = $FormulArr;
				//$turnformulaString = preg_replace_callback ( '/(#.*?#)/', 'lookupTurnformulaCallBackFunc', $turnformula );
				$turnformulaString = preg_replace_callback('/(#.*?#)/',function($v) use ($turnFormulArr){
					$v[0] = str_replace('#', '', $v[0]);
					return '#'.$turnFormulArr[$v[0]].'#';
				} , $turnformula);
				foreach ( $subassemblyArr as $subk => $subval ) {
					$subpropertymap ['id'] = $subval;
					$subproperyList = $MisDynamicFormProperyDao->where ( $subpropertymap )->find ();
					unset($subname);
					$subname = $subproperyList ['fieldname'];
					$inputString = 'input[name="' . $subname . '"]';
					if ($type == 1) {
						$fieldEditArr [$subname] ['addcalc'.$fieldname] [] = $fieldArr [$subname] ['addcalc'.$fieldname] [] = <<<EOF
		
	$('{$inputString}',box).change(function(){
	    $('input[name="{$fieldname}"]',box).trigger('addcalc{$fieldname}');
	 });
	 $('input[name="{$fieldname}"]',box).on('addcalc{$fieldname}',function(){
	 	var c ='{$turnformulaString}';
	 	var c_r=c;
	 	var ret_c=$(this).val();
	 	$.each({$fieldnameArrString}, function(i,v){
		 	var val =  $('input[name="'+v+'"]',box).val();
		 	if(isNullorEmpty(val)){
		 		ret_c = c_r =c_r.replaceAll('#'+v+'#',val);
			}
		});
		var ret ='';
		var a = c.match(/\#/g);
			try{
			var decimal ={$validdigit};
			if(a.length==2){
				if($(this).val()){
					apamount = $(this).val();
				}else{
					apamount = ret_c;
				}
				apamount = comboxMathematicalOperation(ret_c, decimal);	
				
			}else{
				apamount = comboxMathematicalOperation(ret_c, decimal);
			}
			$(this).val(apamount).change();
			}catch(e){
				console.log(e||e.message);
			}
		
	});
EOF;
					$fieldEditArr [$subname] ['load'.$fieldname] [] = $fieldArr [$subname] ['load'.$fieldname] [] = <<<EOF
						
	$('[name="{$fieldname}"]',box).trigger('addcalc{$fieldname}');
EOF;
					} elseif ($type == 2) {
						$JiequArr=explode('#',$turnformulaString);
						if(!empty($JiequArr[4])){
							//时间减过后的计算处理
							$JiequStringfuhao=substr($JiequArr[4],0,1);
							$JiequStringzhi=substr($JiequArr[4],1);

							$fieldEditArr [$subname] ['blur'.$fieldname] [] = $fieldArr [$subname] ['blur'.$fieldname] [] = <<<EOF
							
							
	$('{$inputString}',box).on('blur',function(){
		$('input[name="{$fieldname}"]').trigger('adddatecalc{$fieldname}');
	});
	 $('input[name="{$fieldname}"]',box).on('adddatecalc{$fieldname}',function(){
		try{
			 var d1 = $('input[name="{$fieldnameArr[0]}"]',box).val();
			 var d2 = $('input[name="{$fieldnameArr[1]}"]',box).val();
			 var dat = new Date().DateDiff('{$jingdu}',d1,d2);
			 var ret=dat{$JiequStringfuhao}{$JiequStringzhi};				
							
			 $(this).val(ret).change();
		}catch(e){
			console.log(e||e.message);
		}
	});
EOF;
						}else{
							//正常时间减
						$fieldEditArr [$subname] ['blur'.$fieldname] [] = $fieldArr [$subname] ['blur'.$fieldname] [] = <<<EOF
		
		
	$('{$inputString}',box).on('blur',function(){
		$('input[name="{$fieldname}"]').trigger('adddatecalc{$fieldname}');
	});
	 $('input[name="{$fieldname}"]',box).on('adddatecalc{$fieldname}',function(){
		try{
			 var d1 = $('input[name="{$fieldnameArr[0]}"]',box).val();
			 var d2 = $('input[name="{$fieldnameArr[1]}"]',box).val();
			 var ret = new Date().DateDiff('{$jingdu}',d1,d2);
			 		
			 
			 $(this).val(ret).change();
		}catch(e){
			console.log(e||e.message);
		}
	});
EOF;
						}
						
					} elseif ($type == 3) {
						$fieldEditArr [$subname] ['blur'.$fieldname] [] = $fieldArr [$subname] ['blur'.$fieldname] [] = <<<EOF
		
					$('{$inputString}',box).on('blur',function(){
	    $('input[name="{$fieldname}"]').trigger('addgetdatecalc{$fieldname}');
	 });
	 $('input[name="{$fieldname}"]',box).on('addgetdatecalc{$fieldname}',function(){
		try{
			 var d1 = $('input[name="{$numbername}"]',box).val();
			 var fmtjs="{$allNodeconfig[$numbername][$property ['formatjs']['name']]}";
			  d1 = toDate(d1,fmtjs);
			  if(!d1) return ;
			 var num={$number};
			 var ret = new Date().DateAdd('{$jingdu}',num,d1);
			 var fmtStr = $(this).attr('format');
			 var finalyFMT='yyyy-MM-dd HH:mm';
			 if(fmtStr){
				 fmt= eval('('+fmtStr+')');
				 if(fmt['dateFmt']){
					 finalyFMT = fmt['dateFmt'];
				 }
			 }
			ret= ret.Format(finalyFMT);
			 $(this).val(ret).blur();
		}catch(e){
			 console.log(e||e.message);
		}
	});
EOF;
				$fieldEditArr [$subname] ['load'.$fieldname] [] = $fieldArr [$subname] ['load'.$fieldname] [] = <<<EOF
		
	 $('[name="{$fieldname}"]',box).trigger('addgetdatecalc{$fieldname}');
EOF;
					}elseif($type==4){
						$fieldEditArr [$subname] ['addjoin'.$fieldname] [] = $fieldArr [$subname] ['addjoin'.$fieldname] [] = <<<EOF
						
	$('{$inputString}',box).change(function(){
	    $('input[name="{$fieldname}"]',box).trigger('addjoin{$fieldname}');
	 });
	 $('input[name="{$fieldname}"]',box).on('addjoin{$fieldname}',function(){
	 	var c ='{$turnformulaString}';
	 	var c_r=c;
	 	var ret_c=$(this).val();
	 	$.each({$fieldnameArrString}, function(i,v){
		 	var val =  $('input[name="'+v+'"]',box).val();
		 	if(isNullorEmpty(val)){
		 		ret_c = c_r =c_r.replaceAll('#'+v+'#',val);
			}
		});
		var ret ='';
		var a = ret_c.match(/\#/g);
			try{
			if(a){
					apamount = '';
						
			}else{
				apamount = ret_c;
			}
	 		if(apamount){
	 			$(this).val(apamount).change();
			}
			
			}catch(e){
				console.log(e||e.message);
			}
						
	});
EOF;
					$fieldEditArr [$subname] ['load'.$fieldname] [] = $fieldArr [$subname] ['load'.$fieldname] [] = <<<EOF
						
											$('[name="{$fieldname}"]',box).trigger('addjoin{$fieldname}');
EOF;
					}
				}
			}
			// /////////////////////////////////////////////////////////////////////////////////////////
			// 文本框-组件计算公式 end
			// /////////////////////////////////////////////////////////////////////////////////////////
		}
		
		
		foreach ( $fieldArr as $fieldKey => $fieldVal ) {
			foreach ( $fieldVal as $fKey => $fVal ) {
				foreach ( $fVal as $f2Key => $f2Val ) {
					$addjsstr .= <<<EOF
				{$f2Val}
EOF;
				}
		}
		}
		foreach ($fieldEditArr as $fieldKey=>$fieldVal){
			foreach ($fieldVal as $fKey=>$fVal){
				foreach ($fVal as $f2Key=>$f2Val){
					$editjsstr .=<<<EOF
			{$f2Val}
EOF;
				}
			}
		}
		
		$editjsstr .= "\n" . $loadStr;
		$addjsstr .= "\n" . $loadStr;
		$viewjsstr.="\n".$viewloadStr;
		$addjsstr .= <<<EOF
		
});
EOF;
		$editjsstr .= <<<EOF
		
});
EOF;
		// var_dump($addAndEditJsArr);
		foreach ( $addAndEditJsArr as $aeKey => $aeVal ) {
			$addjsstr .= <<<EOF
		
{$aeVal}
EOF;
			$editjsstr .= <<<EOF
		
{$aeVal}
EOF;
		}
		 		
		foreach ($viewJsArr as $vjKey=>$vjVal){
			$viewjsstr .= <<<EOF
			
			{$vjVal}
EOF;
		}
		$viewjsstr.="\n});";
		$autoformObj = D ( 'Autoform' );
		$autoformObj->createJSCode ( $viewjsstr, $viewFildNmae, '生成添加页面专用JS' );
		$autoformObj->createJSCode ( $addjsstr, $addFileName, '生成添加页面专用JS' );
		$autoformObj->createJSCode ( $editjsstr, $editFileName, '生成修改页面专用JS' );
		
		$autoformObj->createExtendJSCode ( '', $viewExtendFileName, '查看页面JS扩展' );
		$autoformObj->createExtendJSCode('' , $addExtendFileName , '新增页面JS扩展');
		$autoformObj->createExtendJSCode('' , $editExtendFileName , '修改页面JS扩展');
		
		
		/*
		 * //if(!empty($addjsstr)){
		 * $addFile=fopen($addFileName,'w');
		 * fwrite($addFile,$funcDesc.$addjsstr);
		 * fclose($addFile);
		 * //}
		 *
		 * //if(!empty($editjsstr)){
		 *
		 * $editFile=fopen($editFileName,'w');
		 * fwrite($editFile,$funcDesc.$editjsstr);
		 * fclose($editFile);
		 * //}
		 *
		 */
	}

	/* 私有函数区域 */

	/**
	 * 设置组件上lookup属性的默认值
	 * @param array $curTagProperty	当前组件属性列表
	 * @param string $k	当前属性
	 * @param array $temp	属性最终保存集合
	 * @author quqiang
	 * @date 2014-10-10 15:07
	 */
	private function getlookupdefault($curTagProperty , $k , $temp){
		/**
		 * 默认数据
		 * @param array $detaultdata
		 */
	 // 从用户指定lookup数据源中获取第一项数据的内容
		$detaultdata =$this->lookupConfig[key(call_user_func(array($this,$curTagProperty[$k][dataAdd]) , false))];
		foreach (explode(";", $curTagProperty[$k][rel]) as $key=>$val){
			UNSET($temprel);
			$temprel=explode(":",$val);
			$temp[$temprel[0]]=$detaultdata[$temprel[1]];
		}
		return $temp;
	}
	/**
	 * 设置组件上selectlist属性的默认值
	 * @param array $curTagProperty	当前组件属性列表
	 * @param string $k	当前属性
	 * @param array $temp	属性最终保存集合
	 * @author quqiang
	 * @date 2014-10-10 15:09
	 */
	private function getselectlistdefault($curTagProperty , $k , $temp){
	 // 从用户指定selectlist数据源中获取第一项数据的内容
		$detaultdata =key(call_user_func(array($this,$curTagProperty[$k][dataAdd]) , false));
		$temp[$k] = $detaultdata;
		return $temp;
	}

	/* 私有函数区域 end*/

	/**
	 * @Title: modifyOriginalConfig
	 * @Description: todo(批量更新页面已有组件文件)
	 * @param array $arr1 需要修改的字段集合【作用范围为当前action】
	 * @param array $arr2 需要移除当前节点中的字段【作用范围为当前节点】
	 * @param array $arr3 所有需要删除的字段，所有节点一起删除【作用范围为当前action】
	 * @author quqiang
	 * @date 2014-10-10 16:17
	 * @throws
	 * @examp
	 * 		$arr1=array(
	 * 			[0]=>array(
	 * 				'oldfieldname' => 'f1',
	 * 				'filed' => 'f1',
	 *				'tablename' => 'mis_auto_mrugj',
	 *				'type' => 'varchar',
	 *				'length' => '20',
	 *				'category' => 'text',
	 *				'title' => '字段1',
	 *				'weight' => '1',
	 *				'sort' => '1',
	 * 			),
	 * 			[1]=>array(),
	 * 			......
	 * 			oldfieldname // 当有有这个值时 表示的是对现有字段的修改。没有时就是 新增字段
	 * 			//......
	 * 		)
	 * 		$arr2 =array(
	 * 			'节点名'=>'字段1', '字段2',.....
	 * 		)
	 * 		$arr3=>array(
	 * 			'字段1','字段2'......
	 * 		)
	 */
	public function modifyOriginalConfig($arr1  , $arr2 , $arr3){
		return;
		logs('C:'.__CLASS__.' L:'.__LINE__.'需要修改的字段集合'.$this->pw_var_export($arr1),date('Y-m-d' , time()).'_data.log');
		logs('C:'.__CLASS__.' L:'.__LINE__.'需要移除当前节点中的字段'.$this->pw_var_export($arr2),date('Y-m-d' , time()).'_data.log');
		logs('C:'.__CLASS__.' L:'.__LINE__.'所有需要删除的字段'.$this->pw_var_export($arr3),date('Y-m-d' , time()).'_data.log');

		$privateProperty = $this->privateProperty;
		/**
		 * 步骤为：
		 * 1.获取到当前Action的组件配置文件信息
		 * 2。删除字段操作，取出所有节点信息，名称将其中的字段名相同的全删除
		 * 	2.1 选删除记录组合成标准格式 array('field1'=>array(),'field2'=>array());
		 * 3。从需要修改的字段集合中分离出新增的修改，并做相应操作
		 * 	3.1 新增字段
		 * 		3.1.1	生成默认属性
		 * 		3.1.2 追加到所有节点下
		 * 	3.2 修改现有字段		【啊啊， 原有属性丢失了啊。。。。。】
		 * 		3.2.1	改的是普通属性
		 * 		3.2.2	改了组件类型，需要重新生成组件默认属性。
		 * 	//新增字段，所有节点下都添加，需要生成默认组件属性
		 * 	4。需要移除当前节点中的字段
		 * 		4.1	得到当前节点,并移除字段
		 */

		// 1.获取到当前Action的组件配置文件信息
		$model = D('Autoform');
		$dir = '/autoformconfig/';
		$path = $dir.$this->nodeName.'.php';
		$model->setPath($path);

		// 获取所有节点信息
		$allNodeconfig = $model->getAllNode();
		if($allNodeconfig[$this->curnode]){
		}else{
			$allNodeconfig[$this->curnode] = array();
		}



		//		if(!$allNodeconfig[$this->curnode]){
		//			$allNodeconfig[$this->curnode] = array();
		//		}
		// 所有组件信息
		/// $allControllConfig = $model->getAllControllConfig();
		$allControllConfig = $this->getAllControllConfig();
		// 2 删除数组格式转换
		$delFieldArr = array();
		$arr3arr =  explode(',',	$arr3);
		array_pop($arr3arr);
		foreach ($arr3arr as $key => $value) {
			$delFieldArr[$value] =array();
		}
		// 3.从集合中分离出修改与新增
		// 依据为 看数据有无 oldfieldname 键的值
		// 修改字段集合
		$modifyFieldArr=array();
		// 新增字段集合
		$addFieldArr = array();
			
		foreach ($arr1 as $key => $value) {
			if($value['oldfieldname']){
				// 修改字段数据集合
				// 后续需要做修改的哪一项的处理
				$modifyFieldArr[] = $value;
			} else{
				// 新增字段数据集合
				$addFieldArr[$value['filed']] = $value;
			}
		}
		// 3.1.1	新增字段生成默认属性
		if(count($addFieldArr)){
			$addFieldArr = $this->createControllDefaultValue($addFieldArr);
		}
		foreach($allNodeconfig as $key=>$val){

			//2.1 去除数据库中已删除的节点
			$allNodeconfig[$key] =  array_diff_key($val,$delFieldArr);
			// 3.1.2 追加到所有节点下
			$allNodeconfig[$key] = array_merge($allNodeconfig[$key] , $addFieldArr);

			// 4.1得到当前节点 并移除该节点下的字段
			$newlist=explode(',',$arr2[$key]);
			array_pop($newlist);
			foreach ($newlist as $nkey => $nval) {
				unset($val[$nval]);
			}
			$allNodeconfig[$key]=$val;
		}
			
		// 3.2 修改现有字段
		// 分为两大类：1 改字段名，2不 改字段名
		// 改字段名 需要对所有节点中的key 值进行替换
		if(count($modifyFieldArr)){
			foreach ($modifyFieldArr as $key => $value) {
				// 先检查有没有改字段名称,就是检查字段属性是否存在，进一步表示是否需要取出现有值做局部修改
				$checkfieldname = $allControllConfig[$value['filed']];
				if(!$checkfieldname){
					// 旧字段名数据, 如果数据依然为空表示 需要在当前节点中添加这个字段信息
					$tempControllCondif = $allControllConfig[$value['oldfieldname']];
					if($tempControllCondif){
						// 表示改了字段名,那么 需要到每个节点下去更新数据
						foreach($allNodeconfig as $k=>$v){
							// 如果当前节点下不存在旧字段名，表示不在当前节点显示
							// 存在的话 就将原始数据先存到所有节点数据中.并更新所有节点信息
							if($v[$value['oldfieldname']]){

								$tempControllCondif[$privateProperty[$value['category']]['fields']['name']] = $value['filed'];
								$v[$value['filed']]=$tempControllCondif = $tempControllCondif;
								unset($v[$value['oldfieldname']]);
								$allNodeconfig[$k] = $v;
								$allControllConfig[$value['oldfieldname']] = $v;
							}else{
								if($this->curnode == $k){
									$curNodeConfig = $this->createControllDefaultValue(array($value));
									$v[$value['filed']] = reset($curNodeConfig);
									$allNodeconfig[$k] = $v;
								}
							}
						}
					}else{
						// 写入新的字段信息，需要调用自动补全属性。再写入当前节点下
						foreach($allNodeconfig as $k=>$v){

							// 得到当前节点
							if($this->curnode == $k){
								$curNodeConfig = $this->createControllDefaultValue(array($value));
								$v[$value['filed']] = reset($curNodeConfig);
								$allNodeconfig[$k] = $v;
							}
						}
					}

					// 字段名修改完成 end
				}else{
					// 先将用户需要的字段写入
					foreach($allNodeconfig as $k=>$v){
						if($this->curnode == $k ){
							if($v[$value['filed']]){
								$v[$value['filed']] = $checkfieldname;
								$allNodeconfig[$k] = $v;
							}else{
								if($checkfieldname){
									$allNodeconfig[$k][$value['filed']] = $checkfieldname ;
								}
							}
						}
					}
					// 做属性局部修改，就是可能在某一节点中有这个字段信息。这里是做的类型字段属性修改.
					// 原始数据 上做用户的数据修改
					// 修改组件类型。得到默认组件数据
					// 当前组件的生成默认属性。 不一定会用上,但需要先准备好。
					// 原始类型
					$ischanegCategory = false;
					$originalCategory='';
					if($value['category'] != $checkfieldname['catalog']){
						$originalCategory = $checkfieldname['catalog'];

						$checkfieldname['catalog'] = $value['category'];
						$curControlDefaultConfig = $this->createSingleControllDefaultValue($value);
						$ischanegCategory = true;
					}

					// 改的是非 字段 与 组件类型的数据
					// 将用户改的值先改到取出的通用属性中, 前面已经处理过了 组件类型与 字段 就需要移除
					$tempCurUserSetData = $value;

					unset($tempCurUserSetData['oldfieldname']);
					unset($tempCurUserSetData['category']);

					$tempRel = $this->confRelField;
					// 前是用户数据的key，后是 配置中的key
					$tempRel = array_flip($tempRel);

					foreach($tempRel as $k=>$v){
						if($value[$tempRel[$v]]){
							// 有用户修改的数据就保存到临时集合中去
							$checkfieldname[$privateProperty[$value['category']][$k]['name']] = $value[$tempRel[$v]];
						}
					}
					// 组件改了 就需要合并,并清除原有组件的属性
					if($curControlDefaultConfig && $originalCategory){
						foreach($this->controlConfig[$originalCategory]['property'] as $k=>$v){
							unset($checkfieldname[$v['name']]);
						}
						$checkfieldname = array_merge($checkfieldname , reset($curControlDefaultConfig));
					}
					foreach($allNodeconfig as $k=>$v){
						if($v[$value['filed']]){
							$v[$value['filed']] = $checkfieldname;
							$allNodeconfig[$k] = $v;
						}
						/*
						 // 改了类型
						 if($ischanegCategory){
						 if($v[$value['filed']]){
						 $v[$value['filed']] = $checkfieldname;
						 $allNodeconfig[$k] = $v;
						 }
						 }else{
						 // 得到当前节点
						 echo '+++'.$this->curnode.'___'.$k;
						 if($this->curnode == $k  && $v[$value['filed']] ){
						 //&& $v[$value['filed']]
						 echo 'ccc';
						 $v[$value['filed']] = $checkfieldname;
						 $allNodeconfig[$k] = $v;
						 }
						 }
						 */
					}
				}

			}
		}else {}
		//echo '我是到了最后写入文件';
		$model->SetRules($allNodeconfig);
	}

	/**
	 * @Title: createSingleControllDefaultValue
	 * @Description: todo(生成单个组件的默认属性)
	 * @param array $arr 简单组件配置文件内容
	 * @author quqiang
	 * @date 2014-10-10 下午17:24:52
	 * @throws
	 */
	function createSingleControllDefaultValue($arr){
		$data = array();

		//$curTagProperty = $this -> privateProperty[$arr['category']];

		$curTagProperty = $this->controlConfig[$arr['category']]['property'];


		$index = 0;
		unset($temp);
		foreach ($curTagProperty as $k => $v) {
			if ($this -> confRelField[$k]) {
				$temp[$v['name']] = $arr[$this -> confRelField[$k]];
			} else {
				// var_dump($this->controlDefaultVal);
				$curDefaultVal = $this -> controlDefaultVal[$arr['category']];

				$tempItem = $curDefaultVal[$k];
				switch ($k) {
					case 'lookupchoice':
						$temp = call_user_func(array($this , 'getlookupdefault') , $curTagProperty, $k, $temp);
						break;
					case 'showoption':
						$temp = call_user_func(array($this , 'getselectlistdefault') , $curTagProperty, $k, $temp);
					default:
						break;
				}

				if ($tempItem) {
					eval("\$tempItem = \"$tempItem\";");
				} else {
					$tempItem = '';
				}
				if(!$temp[$v['name']]){
					$temp[$v['name']] = $tempItem;
				}
			}
		}
		$data[$arr['filed']]=$temp;
		return $data;
	}

	/**
	 * @Title: createControllDefaultValue
	 * @Description: todo(生成组件的默认属性)
	 * @param array $arr 简单组件配置文件内容
	 * @author quqiang
	 * @date 2014-10-10 下午17:24:52
	 * @throws
	 */
	function createControllDefaultValue($arr){
		$data = array();
		foreach ($arr as $key => $value) {
			$curTagProperty = $this -> privateProperty[$value['category']];
			unset($temp);
			foreach ($curTagProperty as $k => $v) {
				if ($this -> confRelField[$k]) {
					$temp[$v['name']] = $value[$this -> confRelField[$k]];
				} else {
					// var_dump($this->controlDefaultVal);
					$curDefaultVal = $this -> controlDefaultVal[$value['category']];

					$tempItem = $curDefaultVal[$k];
					switch ($k) {
						case 'lookupchoice':
							$temp = call_user_func(array($this , 'getlookupdefault') , $curTagProperty, $k, $temp);
							break;
						case 'showoption':
							$temp = call_user_func(array($this , 'getselectlistdefault') , $curTagProperty, $k, $temp);
						default:
							break;
					}

					if ($tempItem) {
						eval("\$tempItem = \"$tempItem\";");
					} else {
						$tempItem = '';
					}
					if(!$temp[$v['name']]){
						$temp[$v['name']] = $tempItem;
					}
				}
			}
			$data[$key]=$temp;
			$index++;
		}
		return $data;
	}

	/**
	 * @Title: quickedit
	 * @Description: todo(将建建模代码生成为表单属性代码)
	 * @author quqiang
	 * @date 2014-11-10 下午02:47:03
	 * @throws
	 */
	function quickedit() {
		// 先将模板生成方案数据保存下来
		// 模板生成方式
		$tpltype = $_POST['tpltype'];
		// 模板显示布局方式
		$previewtype = $_POST['previewtype'];
		$dbtpltype = $tpltype.'#'.$previewtype;
		//$this->nodeName = 'MisAutoZnx';
		$model = D('MisDynamicFormManage');
		$formInfo = $model -> where("status = 1 and actionname='" . $this -> nodeName . "'") -> find();
		$transeModel = new Model();
		$transeModel->startTrans();
		try{
			if(!$formInfo){
				$msg = "数据异常！参数缺失，请重新打开本页面。";
				throw new NullDataExcetion($msg);
			}
	
			if($dbtpltype){
				$data['tpl']=$dbtpltype;
				$ret = $model->where('id='.$formInfo['id'])->save($data);
				if(false === $ret){
					$msg = "模板类型修改失败。".$model->getDBError()." ".$model->getLastSql();
					throw new NullDataExcetion($msg);
				}
			}else{
				$msg = "模板类型不存!无法修改!";
				throw new NullDataExcetion($msg);
			}
			$transeModel->commit();
		}catch (Exception $e){
			$transeModel->rollback();
			$this->error( $e->__toString() );
		}
		
		$this -> assign('vo', $formInfo);
		logs('C:'.__CLASS__.' L:'.__LINE__.'当前Action名：'.$this->nodeName.'当前节点：'.$this->curnode.' 当前action ID:'.getFieldBy($this->nodeName, "actionname", "id", "mis_dynamic_form_manage"));
		// 从数据库中得到当前表单节点下的组件数据
		$DBConfigData = $this->getfieldCategory(getFieldBy($this->nodeName, "actionname", "id", "mis_dynamic_form_manage"));
		$data = $DBConfigData[$this->curnode];
		/*
		 var_dump($this->nodeName);
		 var_dump($this->curnode);
		 exit;
		 */
		// 生成页面组件
		//echo 'quickedit curnode:';
		$html = self::createContrlHtml($data);
		
		$this -> assign('html', $html);
		$this -> assign('reservedField', $this -> systemUserField);
		$this -> assign('NBM_COMMON_PROPERTY', $this -> publicProperty);
		$this -> assign("action",$this->nodeName);
		$this -> assign('NBM_COMMON_PROPERTY_JSON', json_encode($this -> publicProperty));
		$this -> assign('NBM_PRIVATE_PROPERTY_JSON', json_encode($this -> privateProperty));
		$this -> assign('controlls', $this -> controls);
		$this -> getNodeList();
		$this -> display('MisDynamicFormManage:autoformindex');

	}

	/**
	 * @Title: datatablecontroll
	 * @Description: todo(内嵌表字段管理器)
	 * @author quqiang
	 * @date 2014-8-28 下午17:14:52
	 * @throws
	 */
	function datatablecontroll(){
		if(!$_POST){			
			$id = $_GET['id']; // 被改变值的属性
			$name = $_GET['name']; // 当前操作组件的唯一标识
			$check = $_GET['check']; // 当前操作组件的唯一标识
			$container = $_GET['container']; // 动态表单的操作区域
			$this->nodeName=$_GET['action'];
			$formid = $_GET['formid'];
			$propertyid = $_GET['propertyid'];
			if(!$formid){
				$this->error('组件信息错误');
			}
			//getFieldBy($misDaddId, "formid", "id", "mis_dynamic_form_template","tplname",$tplname)
			$tablename = getFieldBy($formid, "formid", "tablename", "mis_dynamic_database_mas","isprimary",1);
			if(!$tablename){
				$this->error('组件信息错误,表名获取失败');
			}
			$this->assign('tablename',$tablename);
			$this->assign('id',$id);
			$this->assign('fieldname',$name);
			$this->assign('formid',$formid);
			$this->assign('propertyid',$propertyid);
			
			$this->assign('check',$check);
			$this->assign('container',$container);
			$this->assign('tagIndetity',$_GET['tagIndetity']); // 属性查找标识
			$configPath = $this->getAutoFormConfig();
			if(is_array($configPath)){
				$tem = $configPath[$name][$id];
				$obj = json_decode(htmlspecialchars_decode($tem));
				foreach ($obj as $k=>$v){
					foreach ($v as $k1=>$v1){
						$filed[$k1]=$v1;
					}
					$data1[]=$filed;
				}
				
				$this->assign('tabledata',$data1);
			}
			//获取内嵌表数据
			$datatablemodel = M("mis_dynamic_form_datatable");
			$map['formid'] = $formid;
			$map['propertyid'] = $propertyid;
			$rs = $datatablemodel->where($map)->select();
			$rs = json_encode($rs);
			$this->assign('rs',$rs);
			$this->display('MisDynamicFormManage:field');
		}else{	
			// 内嵌表字段信息
			$arr=array();
			$field=array();
			foreach($_POST['items'] as $key=>$val){
				$field[$val['fieldname']]=$val;
			}
			$arr[] = $field;
			$model = M("mis_dynamic_form_datatable");			
			//内嵌表名
			$intablename = $_POST['tablename'].'_sub_'.$_POST['zujianname'];
			$tabletrue = $_POST['tablename'];
			//判断内嵌表是否存在
			$istabletrue = $this->validateTable($intablename);
			$emptymodel = M();
				//dump($_POST);
			//内嵌表不存在就创建一个内嵌表，如果已存在，则把字段添加、修改进内嵌表	
			if(empty($istabletrue)){
				$sql = "CREATE TABLE IF NOT EXISTS `".$intablename."` (";
				$sql .= "`id` int(11) NOT NULL AUTO_INCREMENT  COMMENT 'ID',";
				$sql .= "PRIMARY KEY (`id`),";
				$sql .=	"`masid` int(11) COMMENT '关联动态表单数据ID',";
				foreach ($_POST['items'] as $k1=>$v1){
					$fieldtype = strtolower($v1['fieldtype']);
					if($fieldtype == 'date'){
						$fieldtype = 'INT';
					}
					$sql .= "`".$v1["fieldname"]."` ".$fieldtype."(".$v1["fieldlength"].")  NULL COMMENT '".$v1["fieldtitle"]."',";
				}
				$sql .= "KEY `delete_Action_datatable8` (`masid`),";
				$sql .= "CONSTRAINT `".$intablename."`";
				$sql .= "FOREIGN KEY (`masid`) REFERENCES `".$tabletrue."` (`id`) ON DELETE CASCADE";
				$sql .= ")ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='动态表单:".$tabletrue." 内嵌表  ".$intablename."'";
				$emptymodel->execute($sql);
			}else{
				foreach ($_POST['items'] as $k2=>$v2){
						$fieldtype = strtolower($v2['fieldtype']);
						if($fieldtype == 'date'){
							$fieldtype = 'INT';
						}
					if($v2['fieldid']){
						//continue;
						$oldlist = $model->where("id=".$v2['fieldid'])->find();
						$oldname = $oldlist['fieldname'];
						$sql = "ALTER TABLE `".$intablename."` CHANGE `".$oldname."` `".$v2['fieldname']."` ".$fieldtype."(".$v2["fieldlength"].") DEFAULT NULL COMMENT '".$v2["fieldtitle"]."'";
						$emptymodel->execute($sql);
					}else{
						$sql = "ALTER TABLE `".$intablename."` ADD  `".$v2['fieldname']."` ".$fieldtype."(".$v2["fieldlength"].") DEFAULT NULL COMMENT '".$v2["fieldtitle"]."'";
						$emptymodel->execute($sql);
						//echo $emptymodel->getlastsql();
					}
				}
			}
			//查询当前表单下是否有复用子表
			$MisDynamicFormManageModel=D("MisDynamicFormManage");
			$dfMap=array();
			$dfMap['choiseformid']=$_POST['formid'];
			$MisDynamicFormManageList=$MisDynamicFormManageModel->where($dfMap)->getField("id,actionname");
			//入数据库
			$data['formid'] = (int)$_POST['formid'];
			$data['propertyid'] = (int)$_POST['propertyid'];
			
			//查询该formid propertyid 下的 已有字段
			$fieldarr = $model->where($data)->select();
			foreach ($fieldarr as $k=>$v){
				$fieldlist[] = $v['fieldname'];
				//按id值作为key列出数据库所有数据纪录（用于修改数据时比较字段是否修改）
				$oldall[$v['id']] = $v;
			}
			//查询原纪录最大id值（用于新增数据时id手动添加）
			$maxid = $model->order("id desc")->getField("id");
			
			$updata = array();
			$insertdata = array();
			foreach($_POST['items'] as $k=>$v){
				$data['fieldname'] = $v['fieldname'];
				$data['fieldtitle'] = $v['fieldtitle'];
				$data['fieldtype'] = $v['fieldtype'];
				$data['fieldlength'] = $v['fieldlength'];
				$data['category'] = $v['fieldshowtype'];
				$temp = unserialize(base64_decode($v['fieldshowtypeconfig']));
				$data['unit'] = $temp['baseuntils'];//基本单位
				$data['unitls'] = $temp['untils'];//显示单位
				$data['config'] = $v['fieldshowtypeconfig'];
				$data['isshow'] = (int)$v['fieldshow'];
				$data['isedit'] = (int)$v['isedit'];
				$data['iscount'] = $v['fieldcount']?(int)$v['fieldcount']:0;
				$data['countassignment'] = $data['iscount']?$v['countassignment']:'';
				$data['fieldsort'] = $v['fieldsort'];
				$data['colwidth'] = $v['colwidth'];
				$data['stats_num'] = $v['stats_num'];
				$data['tablename'] = $intablename;
				//向数据库加数据，fieldname必须存在
				if($data['fieldname']){					
					$fieldid = (int)$v['fieldid'];					
					//区别构建新增、修改数据（根据是否有fieldid）					
					if($fieldid){//修改数据						
						$data['id'] = $fieldid;
						$updata[] = $data;									
					}else{//新增数据
						//检查是否字段重复
						if(in_array($data['fieldname'],$fieldlist)) $this->error('字段'.$data['fieldname'].'名称重复，请重新设置字段名称');
						//手动构建记录id，用于便利统一执行sql语句
						$maxid += 1;
						$data['id'] = $maxid;
						$insertdata[] = $data;				
						foreach($arr as $key=>$val){
							if($v['fieldname']==$val[$v['fieldname']]['fieldname']){
								$arr[$key][$v['fieldname']]['fieldid']=$maxid;
							}
						}						
					}
				}
			}
			//复用表数据（根据修改表单的组件id 查询mis_dynamic_form_propery表单中isforeignfield组件id，能够获取复用表单组件信息）
			$reusetableinfo = array();
			$reusemap['isforeignfield'] = $_POST['propertyid'];
			$propertyModel = M("mis_dynamic_form_propery");
			$reusetableinfo = $propertyModel->where($reusemap)->select();
			
			//记录表公用数据
			$createtime = time();
			$updatetime = time();
			$createid = $_SESSION[C('USER_AUTH_KEY')];
			$updateid = $_SESSION[C('USER_AUTH_KEY')];			
			$formid=$_POST['formid'];
			$propertyid = (int)$_POST['propertyid'];
			$modelname =getFieldBy($formid,'id','actionname','mis_dynamic_form_manage');
			$tablename = $intablename;
			
		//----新增开始---			
			if($insertdata){
				//主表数据新增
				$newadddata = array();
				foreach($insertdata as $ik=>$iv){
					$newadddata[]= "('{$iv[id]}','{$iv[fieldname]}','{$iv[fieldtitle]}','{$iv[fieldtype]}','{$iv[fieldlength]}','{$iv[category]}','{$iv[unit]}','{$iv[unitls]}','{$iv[config]}','{$iv[formid]}','{$iv[propertyid]}','{$iv[isshow]}','{$iv[isedit]}','{$iv[iscount]}','{$iv[fieldsort]}','{$iv[tablename]}')";
				}
				$insertsql = "INSERT INTO mis_dynamic_form_datatable ";
				$insertsql .= " (id,fieldname,fieldtitle,fieldtype,fieldlength,category,unit,unitls,config,formid,propertyid,isshow,isedit,iscount,fieldsort,tablename) ";
				$insertsql .= " VALUES ";
				$insertsql .= implode(",",$newadddata);
				$model->execute($insertsql);
				unset($iv);
				unset($newadddata);
				//复用表新增 记录表记录
				
				
				$newadddata = array();
				foreach($reusetableinfo as $fk=>$fv){
					foreach($insertdata as $ik=>$iv){
						//记录表记录数据
						$recodesqldata[] = "('{$modelname}','{$fv[id]}','{$tablename}','1','','{$$iv[fieldname]}','','{$iv[fieldtype]}','','{$iv[fieldlength]}','','{$iv[fieldcategory]}','','{$iv[fieldtitle]}','{$createtime}','{$createid}')";
					}
					
				}
				
				
			}
		//----新增结束---
			
		//----修改开始----
			//修改用的数据重组，只取更改过的字段用于更改，并将新老数据做纪录
			
			$recodedata = array();//用于纪录表的数据
			$newupdata = array();//用于更新的数据
			foreach ($updata as $upk=>$upv){
				$recode = array(); //临时数据
				foreach($upv as $dk=>$dv){
					if($oldall[$upv['id']][$dk] != $dv){
						$recode['old'][$dk] = $oldall[$upv['id']];
						$recode['new'][$dk] = $dv;
					}
				}
				if(count($recode['new'])>0){
					
					$recode['old']['id'] = $upv['id'];
					$recode['new']['id'] = $upv['id'];										
					$recodedata[$upv['id']] = $recode;
					$newupdata[$upv['id']] = $recode['new'];					
				}
				unset($recode);
			}
			if($newupdata){
				//内嵌表复用字段
				$fidkey = array_keys($newupdata); 
				$fmap['sourceid'] = array("in",$fidkey);
				$flist = $model->where($fmap)->select();
				//本身数据修改
				foreach($newupdata as $uk=>$uv){
					//修改数据
					$model->save($uv);						
				}
				//做记录数据
				$operate = 2;
				foreach($flist as $fuk=>$fuv){
					foreach($recodedata as $key=>$val){
						if($fuv['sourceid'] == $val['new']['id']){
							$new = $val['new'];
							$old = $fuv;
							if($new['fieldname']||$new['fieldtype']||$new['fieldlength']||$new['fieldcategory']||$new['fieldtitle']){
								$recodesqldata[] = "('{$modelname}','{$formid}','{$tablename}','{$operate}','{$old[fieldname]}','{$new[fieldname]}','{$old[fieldtype]}','{$new[fieldtype]}','{$old[fieldlength]}','{$new[fieldlength]}','{$old[fieldcategory]}','{$new[fieldcategory]}','{$old[fieldtitle]}','{$new[fieldtitle]}','{$new[updatetime]}','{$new[updateid]}')";
							}
						}						
					}
				}
			}
		//记录表添加数据
			//sql拼装
			$recodesql = "INSERT INTO mis_dynamic_form_record (modelname,formid,tablename,operate,oldfieldname,fieldname,oldfieldtype,fieldtype,oldfieldlength,fieldlength,oldfieldcategory,fieldcategory,oldfieldtitle,fieldtitle,updatetime,updateid) VALUES ";
			$recodesql .= implode(',',$recodesqldata);
			//sql执行
			$model->execute($recodesql);
			//<-----做记录---完成-->
			//复用表修改
			
			//记录表记录
			
		//----修改结束----
		
		//----删除开始----
			//行删除操作
			$updatawarndata = array();
			$drop = $_POST['dropline'];
			if(count($drop)>0){
				foreach($drop as $v){						
					$clum = getFieldBy($v, 'id', 'fieldname', 'mis_dynamic_form_datatable');
					$updatawarndata['old'][] = $clum;
					unset($arr[0][$clum]);
					$delsql = "alter table $intablename drop column $clum  ";
					M()->query($delsql);
				}
				$mapid['id'] = array("in",$drop); //主表删除
// 				$mapid['sourceid'] = array("in",$drop);//复用表删除
// 				$mapid['_logic'] = 'or';
// 				$mapdel['_complex'] = $mapid;
				$model->where($mapid)->delete();
				$operate = 3;
				
			}
			//---
			$this-> dtupdatewarn($_POST['formid'],$_POST['propertyid'],$type='2',$updatawarndata);
		//----删除结束----
		try{
			//-----新增同步复用表-----
			if($insertdata){
				$this->modifyDatatableteByForeignField($insertdata,$formid ,$propertyid, 1);
			}
			
			//----修改同步复用表-----
			if($newupdata){
				$this->modifyDatatableteByForeignField($newupdata, $formid ,$propertyid, 2);
			}
			
			//----删除同步复用表-----
			if($drop){
				$this->modifyDatatableteByForeignField($drop, $formid ,$propertyid, 3);
			}
			
		}catch (Exception $e){
			echo $e->__toString();
		}
			//查询该表全字段与传过来的数据进行比对
			$tableInfo=D("Dynamicconf")->getTableInfo($intablename);
			foreach ($tableInfo as $tkey=>$tval){
				if($tval['COLUMN_NAME']){
					//循环页面传过来的项
					foreach ($_POST['items'] as $ikey=>$ival){
						if($tval['COLUMN_NAME']==$ival['fieldname']){
							unset($_POST['items'][$ikey]);
						}
					}
				}
			} 
			//清空数据后 保存字段一致性 添加入内嵌表结构
			if($_POST['items']){
				foreach ($_POST['items'] as $akey=>$aval){
					$addsql = "ALTER TABLE `".$intablename."` ADD  `".$aval['fieldname']."` ".$aval["fieldtype"]."(".$aval["fieldlength"].") DEFAULT NULL COMMENT '".$aval["fieldtitle"]."'";
					M()->execute($addsql);
				}
			}
			//print_r(unserialize(base64_decode($_POST['items'][4]['fieldshowtypeconfig'])));exit;
			// 数组结构从数组改为json串 @nbmxkj 20141228 1935 
			$this->success('操作成功','',json_encode(reset($arr)));
		}
		
	}

	/**
	 * @Title: relationcontroll 
	 * @Description: todo(关联组件设置 , 如果组件没有设置事件，则该属性无效。)
	 * @author quqiang 
	 * @date 2014-12-22 下午1:06:26 
	 * @throws
	 */
	function relationcontroll(){
		$MisDynamicControllRecordDao=M("mis_dynamic_controll_record");
		if(!$_POST){
			$id = $_GET['id']; // 被改变值的属性
			$name = $_GET['name']; // 当前操作组件的唯一标识
			$check = $_GET['check']; // 当前操作组件的唯一标识
			$container = $_GET['container']; // 动态表单的操作区域
			//当前组件id
			$propertyid=$_REQUEST['propertyid'];
			$this->nodeName=$_GET['action'];
			$this->curnode = $_GET['curnode'];
			$controllName = $_GET['name'];
			$data = $this->getOrderNode();
			$confVo=$data[$controllName];
			$sList=array();
			$skey="";
			$skey="";
			//组件类型为查找带回
			 if($data[$controllName]['catalog']=="lookup"){
			 	//实例化模型
			 	$sList=D($confVo['model'])->getField($data[$controllName]['lookuporgval'].','.$data[$controllName]['lookuporg']);
			 	logs(D($confVo['model'])->getlastsql(),'ContorllLookup');
			 }else{
			 	if($confVo['showoption']){
			 		//选择select配置文件
			 		$selectList = require ROOT . '/Dynamicconf/System/selectlist.inc.php';
			 		//取得配置的数据表
			 		$sList=$selectList[$confVo['showoption']][$confVo['showoption']];
			 	}else{
			 		$skey="";
			 		$ekey="";
			 		// 用户指定的表数据来源
			 		if($confVo['subimporttableobj']){
			 			//非树形展示
			 			$MisSaleClientTypeDao=M($confVo['subimporttableobj']);
			 			$skey=$confVo['subimporttablefield2obj'];
			 			$ekey=$confVo['subimporttablefieldobj'];
			 		}else{
			 			// 树形展示
			 			$MisSaleClientTypeDao=M($confVo['treedtable']);
			 			$skey=$confVo['treevaluefield'];
			 			$ekey=$confVo['treeshowfield'];
			 		}
			 		$sList=$MisSaleClientTypeDao->where("status=1")->field($skey.",".$ekey."")->select();
			 	}
			 }
			$this->assign("proplist",$proplist);
			$this->assign("curnode",$this->curnode);
			$this->assign("formid",$_REQUEST['formid']);
			$this->assign("skey",$skey);
			$this->assign("ekey",$ekey);
			$this->assign("sList",$sList);
			$this->assign('id',$id);
			$this->assign('check',$check);
			//字段英文标示
			$this->assign("fieldname",$controllName);
			$this->assign("propertyid",$propertyid);
			$this->assign('container',$container);
			$this->assign('tagIndetity',$_GET['tagIndetity']); // 属性查找标识
			$configPath = $this->getAutoFormConfig();
			if(is_array($configPath)){
				//			var_dump($configPath);
				//			if(file_exists($configPath)){
				//				$data = require $this->getAutoFormConfig();
					//				//$data = $aryRule;
					//			}
					$tem = $configPath[$name][$id];
					$obj = json_decode(htmlspecialchars_decode($tem));
					foreach ($obj[0] as $k=>$v){
						foreach ($v as $k1=>$v1){
							$filed[$k1]=$v1;
						}
						$data1[]=$filed;
					}
					$this->assign('tabledata',$data1);
				}
				//查找该组件是否设置隐藏与显示
				$MisDynamicControllRecordMap=array();
				$MisDynamicControllRecordMap['status']=1;
				$MisDynamicControllRecordMap['formid']=$_REQUEST['formid'];
				$MisDynamicControllRecordMap['properyid']=$propertyid;
				$MisDynamicControllRecordMap['status']=1;
				$MisDynamicControllRecordList=$MisDynamicControllRecordDao->where($MisDynamicControllRecordMap)->select();
				$this->assign("MisDynamicControllRecordList",$MisDynamicControllRecordList);
				$this->display('MisDynamicFormManage:relationcontroll');
			}else{
				$typeval=$_POST['typeval'];//对应值
				 //进行增加
				 foreach ($typeval as $key=>$val){
					 $MisDynamicControllRecordData=array();
					 $MisDynamicControllRecordData['properyid']=$_POST['properyid'];
					 $MisDynamicControllRecordData['formid']=$_POST['formid'];
					 $MisDynamicControllRecordData['typeval']=$val;
					 $MisDynamicControllRecordData['resultval']=$_POST['resultval'][$key];
					 $MisDynamicControllRecordData['roleinexp']=$_POST['roleinexp'][$key];
					 $MisDynamicControllRecordData['resultvalshow']=$_POST['resultvalshow'][$key];
					 $MisDynamicControllRecordData['resultshowval']=$_POST['resultshowval'][$key];
					 $MisDynamicControllRecordData['resultshowvalrules']=$_POST['resultshowvalrules'][$key];
					 if($_POST['reids'][$key]){
					 	$MisDynamicControllRecordData['id']=$_POST['reids'][$key];
					 	$result=$MisDynamicControllRecordDao->save($MisDynamicControllRecordData);
					 	logs($MisDynamicControllRecordDao->getlastsql(),'controllRecordUpdate');
					 }else{
					 	$result=$MisDynamicControllRecordDao->add($MisDynamicControllRecordData);
					 	logs($MisDynamicControllRecordDao->getlastsql(),'controllRecordAdd');
					 }
					 if(!$result){
					 	$this->error("添加数据失败！");
					 }
				 }
				 $MisDynamicControllRecordDao->commit();
				 $this->success("添加成功！");
			}
	}
	/**
	 * @Title: lookupconfigcontroll
	 * @Description: todo(lookup配置文件信息管理)
	 * @author quqiang
	 * @date 2014-8-28 下午17:14:52
	 * @throws
	 */
	function lookupconfigcontroll(){
		if(!$_POST){
			$category = $_GET['category']; // 组件类型
			$check = $_GET['check']; // 组件唯一标识
			$container = $_GET['container']; // 容器ID
			$tagIndetity = $_GET['tagIndetity']; // 属性查找标识
			$rellist = $_GET['rellist']; // 对应关系列表
			$val = $_GET['val']; // 当前选中的lookupConfig 键
			// 取得选中的LookupConfig 值.
			$choiceLookupConfigVal = $this->lookupConfig[$val];

			// 获取被关联的属性
			$allRelPropertyKey=array();
			$relKeyandKey=array(); // 得到key与key的对应信息
			if($rellist){
				$relArr = explode(';',$rellist);
				foreach ($relArr as $k=>$v){
					$curRel = explode(':', $v);
					if($curRel[0]){
						$relKeyandKey[$curRel[0]]=$curRel[1];
						$allRelPropertyKey[] = array_merge($this->privateProperty[$category][$curRel[0]] , array('key'=>$curRel[0]));
					}
				}
			}
			$this->assign('category',$category);
			$this->assign('check',$check);
			$this->assign('container',$container);
			$this->assign('tagIndetity',$tagIndetity);
			$this->assign('val',$val);
			$this->assign('property',$allRelPropertyKey);
			$this->assign('relKeyandKey',$relKeyandKey);
			$this->assign('choiceLookupConfigVal',$choiceLookupConfigVal);
			$this->display('MisDynamicFormManage:lookupconfig');
		}else{
			$data = $this->getParame();
			$temp_fileds = unserialize(base64_decode($_POST['nbm_fileds']));
			if(empty($temp_fileds)){
				$this->error('操作失败');
			}else{
				$data = array();
				foreach ($temp_fileds as $k=>$v){
					$data[$k]=$_POST[$k];
				}
				$this->success('操作成功','',json_encode($data));
			}
		}
	}

	/**
	 * @Title: checkforconfigcontroll
	 * @Description: todo(checkfor配置文件信息管理)
	 * @author quqiang
	 * @date 2014-8-28 下午17:14:52
	 * @throws
	 */
	function checkforconfigcontroll(){
		if(!$_POST){
			$category = $_GET['category']; // 组件类型
			$check = $_GET['check']; // 组件唯一标识
			$container = $_GET['container']; // 容器ID
			$tagIndetity = $_GET['tagIndetity']; // 属性查找标识
			$rellist = $_GET['rellist']; // 对应关系列表
			$val = $_GET['val']; // 当前选中的lookupConfig 键
			// 取得选中的LookupConfig 值.
			$choiceLookupConfigVal = $this->checkforConfig[$val];

			// 获取被关联的属性
			$allRelPropertyKey=array();
			$relKeyandKey=array(); // 得到key与key的对应信息
			if($rellist){
				$relArr = explode(';',$rellist);
				foreach ($relArr as $k=>$v){
					$curRel = explode(':', $v);
					if($curRel[0]){
						$relKeyandKey[$curRel[0]]=$curRel[1];
						$allRelPropertyKey[] = array_merge($this->privateProperty[$category][$curRel[0]] , array('key'=>$curRel[0]));
					}
				}
			}
			$this->assign('category',$category);
			$this->assign('check',$check);
			$this->assign('container',$container);
			$this->assign('tagIndetity',$tagIndetity);
			$this->assign('val',$val);
			$this->assign('property',$allRelPropertyKey);
			$this->assign('relKeyandKey',$relKeyandKey);
			$this->assign('choiceLookupConfigVal',$choiceLookupConfigVal);
			$this->display('MisDynamicFormManage:checkforconfig');
		}else{
			$data = $this->getParame();
			$temp_fileds = unserialize(base64_decode($_POST['nbm_fileds']));
			if(empty($temp_fileds)){
				$this->error('操作失败');
			}else{
				$data = array();
				foreach ($temp_fileds as $k=>$v){
					$data[$k]=$_POST[$k];
				}
				$this->success('操作成功','',json_encode($data));
			}
		}
	}

	/**
	 * @Title: checkavailabletablecontroll
	 * @Description: todo(获取可用主表信息)
	 * @author quqiang
	 * @date 2014-9-28 下午12:05:52
	 * @throws
	 */
	function checkavailabletablecontroll() {

		// MisAutoAnameList.inc.php
		// 目前处理的所有表中字段全唯一
		$allData = $this->getDataBaseConf();
		$curDataSetConf = $allData['cur'];
		$arr=array();
		foreach ($curDataSetConf['datebase'] as $key => $value) {
			// 加上主表选取后不可用状态
			if($value['ischoice'] == 1 && $value['isprimay'] == 1){
				$temp['nextEnd']=0;
			}else{
				$temp['nextEnd']=1;
			}
			$temp['name']=$key;
			$temp['title'] = $value['tabletitle'];
			$arr[]=$temp;
		}
		echo json_encode($arr);
	}
	/*****************************************************************************************
	 *					 业务控制 end
	 *****************************************************************************************/



	/*****************************************************************************************
	 * 		函数开始
	 ******************************************************************************************/

	/**
	 * (non-PHPdoc)
	 * @see CommonAction::insert()
	 * 插入数据库实际表、保存配置文件
	 */
	private function autoInsert(){
		$this->tpltype = $this->typEnum[$_POST['nbm_tpl_type']];
		if(!$this->tpltype){
			$msg = "模板生成方式为空！";
			logs($msg , '' ,ROOT . '/Dynamicconf/createFormLog');
			throw new NullDataExcetion($msg);
			//$this->error('模板生成方式为空');
		}
		//到这里。审批流是flase
		//$this->isaudit=$this->tpltype == 'audittpl'?true:false;  //此处不能获取到是否为审批流
		
		// 需要插入到字段记录表中的数据
		$tableFiledList=array();
		/// 原代码被暂时移除了，需要时 加入进来就行。
		$this->nodeTitle=$_POST['acitontitle'];
		//实例化model
		$MisDynamicFormManageModel=D('MisDynamicFormManage');
		// 实例化字段存储对象
		$MisDynamicFormFieldModel = D('MisDynamicFormField');
		//创建主表储存表
		$AutoPrimaryMasModel=M('mis_auto_primary_mas');
		//创建字表储存表
		$AutoPrimarySubModel=M('mis_auto_primary_sub');
		//获取表名
		$tablename=$_POST['tablename'];
		//获取字段名称
		$fieldname=$_POST['fieldname'];
		/////  取表数据 ///////
		// 取得 主表标识
		$isprimary  = $_POST['tableprimary'];
		//默认主表名称
		$primaryname=$this->looktablename();
		
		// 是否为复用表
		$isMultiplexForm = $_POST['parimarytablename'] ? true : false;
		// 复用主表名
		$multiplexSouceForm = $_POST['parimarytablename'];
		
		// 定义表结构数据
		$primaryArr = array();
		$childArr = array();
		$creatableList=array();
		$fieldnameList=array();
		$primaryKey="";
		//创建主表
		$ceatetable_html_s .= "\r\nCREATE TABLE IF NOT EXISTS `".$primaryname."` \r\n(`id` int(11) NOT NULL AUTO_INCREMENT  COMMENT 'ID' ,";
		$ceatetable_html_e="\r\n\t PRIMARY KEY (`id`)";
		if($isMultiplexForm){
			$primaryname=$_POST['parimarytablename'];
			$creatableList[0]=array(
				'isprimary'=>1,
				'ischoise'=>1,
				'tablename'=>$primaryname,
			);
		}else{
			//主表创建表结构
			$this->createPrimaryTable($tablename,$isprimary,$fieldname,$primaryname,$creatableList);
		}
			//创建子表表结构
		$this->createSubTable($tablename,$isprimary,$fieldname,$primaryname,$creatableList);		//获取字段列表
		////// 创建表结构结束 //////
		//创建子表
		$data=array();
		//生成action
		$this->nodeName=$this->getPrimaryAName();
		//插入数据至动态列表
		$MisDynamicFormManageModel=D('MisDynamicFormManage');
		$MisDynamicFormManagedate=array();
		$MisDynamicFormManagedate['createid']=$_SESSION[C('USER_AUTH_KEY')];
		$MisDynamicFormManagedate['createtime']=time();
		$MisDynamicFormManagedate['actionname']=$this->nodeName;
		$MisDynamicFormManagedate['actiontitle']=$_POST['acitontitle'];
		$MisDynamicFormManagedate['isaudit']=$this->isaudit;
		$MisDynamicFormManagedate['isrecord'] = $this->isrecord ; // 只记录无代码 by nbmxkj@20150703 1752
		$MisDynamicFormManagedate['isreminds']=$_POST['isreminds']?'1':0;
		if($isMultiplexForm){
			$dfMap=array();
			$dfMap['tablename']=$_POST['parimarytablename'];
			$MisDynamicFormManageVo=M("mis_dynamic_database_mas")->where($dfMap)->find();
			//查询主表
			$MisDynamicFormManagedate['choiseformid']=$MisDynamicFormManageVo['formid'];
			$MisDynamicFormManagedate['choiseaction']=$MisDynamicFormManageVo['modelname'];
		}
		$MisDynamicFormManageresult = $MisDynamicFormManageModel->add($MisDynamicFormManagedate);
		if(!$MisDynamicFormManageresult){
			$msg = "表单主记录生成失败！".$MisDynamicFormManageModel->getLastSql();
			logs($msg , '' ,ROOT . '/Dynamicconf/createFormLog');
			throw new NullDataExcetion($msg);
		}
		if($_POST['insertnode']){
			$this->addNode($MisDynamicFormManageresult);
		}
		//组成action 配置格式
		unset($alist);
		$alist=array();
		$alist=array(
				'datebase'=>$this->gettableArr('',$creatableList,1),
		);
		//插入到相应表中
		$this->insertDatebase($alist['datebase'],$MisDynamicFormManageresult);
		// 复用复制数据
		if($isMultiplexForm){
			$dfMap=array();
			$dfMap['tablename']=$multiplexSouceForm;
			$formSouce=M("mis_dynamic_database_mas")->where($dfMap)->find();
			// 复制记录
			$this->copyMasData($formSouce['formid'],$MisDynamicFormManageresult);
			$this->copySubData($formSouce['formid'],$MisDynamicFormManageresult);
			$this->copyPropertyData($formSouce['formid'],$MisDynamicFormManageresult);
			$this->copyAppendProperty($formSouce['formid'],$MisDynamicFormManageresult);
		} 
		return $primaryname;
	}
	
	/**
	 * 复制附加的属性表
	 * @Title: copyAppendProperty
	 * @Description: todo(复制附加的属性表) 
	 * @param int		$souceFormID		来源表单ID
	 * @param int		$curFormID  		当前表单ID
	 * @author quqiang 
	 * @date 2015年4月14日 下午1:42:40 
	 * @throws
	 */
	function copyAppendProperty($souceFormID , $curFormID){
		// mis_dynamic_controll_record		关联条件附加记录
		// mis_dynamic_form_datatable		数据表格附加记录
		// mis_dynamic_calculate				计算关联附加记录
		//	mis_dynamic_form_indatatable	生单设置
		
		if( empty($souceFormID) ||  empty($curFormID) ){
			$msg = '表单属性复制失败，来源表单编号或当前表单编号未知'.$souceFormID.'___'.$curFormID;
			throw new NullDataExcetion($msg);
		}
		/*
		 *	定义对象
		 */
		// 表单表
		$obj = M();
		$formErrorShowName ='';
		// 取出来源与当前表的 property表 字段对应关系
		$sql=<<<EOF
		
SELECT 
(SELECT id FROM `mis_dynamic_form_propery` WHERE ids = souce.xid) AS xpid,
(SELECT id FROM `mis_dynamic_form_propery` WHERE ids = souce.yid) AS ypid FROM (
SELECT  s1.id AS xid , s2.id AS yid FROM 
`mis_dynamic_database_sub`  AS s1
RIGHT JOIN 
mis_dynamic_database_sub AS s2
ON s1.field=s2.field
WHERE 
s1.masid=(SELECT id FROM `mis_dynamic_database_mas` WHERE formid={$souceFormID} AND isprimary=1) 
AND s2.masid=(SELECT id FROM `mis_dynamic_database_mas` WHERE formid={$curFormID} AND isprimary=1)
) AS souce 
EOF;
		
		// 来源表的主表  与 当前表  的sub表属性对应关系
		$relationData = $obj->query($sql);
		foreach ($relationData as $k=>$v){
		$temp[$v['xpid']] = $v['ypid'];
		}
		$relationData = $temp;
		/***********************************************************************************************************************************/
		/*			 mis_dynamic_calculate				计算关联附加记录																									*/
		/***********************************************************************************************************************************/
		$formErrorShowName='计算关联附加记录';
		$mis_dynamic_calculate_copytSql = <<<EOF
		
		SELECT * FROM mis_dynamic_calculate WHERE formid={$souceFormID}
EOF;
		$mis_dynamic_calculate_copytData= $obj->query($mis_dynamic_calculate_copytSql);
		if(is_array($mis_dynamic_calculate_copytData)){
			//
			$mis_dynamic_calculate_insert_sql='insert into mis_dynamic_calculate';
			// 拼接后的sql数组
			$mis_dynamic_calculate_insert_data='';
			// 插入字段数组
			$mis_dynamic_calculate_insert_field=array('formid', 'propertyid' ,'subassembly' , 'formula' ,'turnformula' , 'ststus' , 'correspondfield' , 'type' , 'jingdu' , 'number','souceid');
			
			foreach ($mis_dynamic_calculate_copytData as $k =>$v){
				$tempPropid = $v['propertyid'];
				$dealProptyId = $relationData[$tempPropid];
				// 复制后的替换
					// 替换所有使用组件id subassembly
				$subassemblyID = $v['subassembly'];
				$subassemblyIDArr = explode(',', $subassemblyID);
				foreach ($subassemblyIDArr as $key=>$val){
					$dealSubassemblyIDArr[] = $relationData[$val];
				}
				$dealSubassemblyID = join(',', $dealSubassemblyIDArr);
					// 替换转化成id的公式 turnformula
				 $v['turnformula'] = preg_replace_callback('/#(\d*)#/',function($mat) use ($relationData){
					if($mat[1]){
						return '#'.$relationData[$mat[1]].'#';
					}
				} , $v['turnformula']);
					// 替换字母对应id correspondfield
			 	$v['correspondfield'] = preg_replace_callback('/"(\d*)"/',function($mat) use ($relationData){
			 		if($mat[1]){
			 			return '"'.$relationData[$mat[1]].'"';
			 		}
			 	} , $v['correspondfield']);
			 	
				if( $dealProptyId && $dealSubassemblyID ){
					$mis_dynamic_calculate_insert_data[]="('{$curFormID}' , '{$dealProptyId}','$dealSubassemblyID' , '{$v['formula']}' , '{$v['turnformula']}' ,'{$v['ststus']}' , '{$v['correspondfield']}' , '{$v['type']}' ,'{$v['jingdu']}' , '{$v['number']}' , {$v['id']})";
				}
				
			}
			
			if( is_array($mis_dynamic_calculate_insert_field) && is_array($mis_dynamic_calculate_insert_data) ){
				$mis_dynamic_calculate_insert_sql .= "(" . join(',' , $mis_dynamic_calculate_insert_field) . ") values" . join(',', $mis_dynamic_calculate_insert_data);
			}else{
				$mis_dynamic_calculate_insert_sql='';
			}
			
			
			if( $mis_dynamic_calculate_insert_sql ){
				$ret = $obj->query($mis_dynamic_calculate_insert_sql);
				if( false === $ret ){
					$msg = "复制{$formErrorShowName}表失败！".$obj->getLastSql();
					throw new NullDataExcetion( $msg );
				}
			}
			
		}
		/***********************************************************************************************************************************/
		/*			 mis_dynamic_controll_record		关联条件附加记录																								*/
		/***********************************************************************************************************************************/
		$formErrorShowName='关联条件附加记录';
		$mis_dynamic_controll_record_copytSql = <<<EOF
		SELECT * FROM mis_dynamic_controll_record WHERE formid={$souceFormID}
EOF;
		$mis_dynamic_controll_record_copytData= $obj->query($mis_dynamic_controll_record_copytSql);
		if(is_array($mis_dynamic_controll_record_copytData)){
			//定义开如结构
			$mis_dynamic_controll_record_insert_sql='insert into mis_dynamic_controll_record';
			// 拼接后的sql数组
			$mis_dynamic_controll_record_insert_data='';
			// 插入字段数组
			$mis_dynamic_controll_record_insert_field=array('properyid', 'formid' ,'typeval' , 'resultval' ,'createid' , 'createtime' , 'updateid' , 'updatetime' , 'roleinexp' , 'resultvalshow', 'status' ,'resultshowval' , 'resultshowvalrules' , 'souceid');
			
			foreach ($mis_dynamic_controll_record_copytData as $k =>$v){
				$tempPropid = $v['properyid'];
				$dealProptyId = $relationData[$tempPropid];
				if($dealProptyId){
					$mis_dynamic_controll_record_insert_data[]="('{$dealProptyId}' ,'{$curFormID}' ,'{$v['typeval']}' , '{$v['resultval']}' , '{$v['createid']}', '{$v['createtime']}', '{$v['updateid']}' , '{$v['updatetime']}' , '{$v['roleinexp']}', '{$v['resultvalshow']}' , '{$v['status']}' ,'{$v['resultshowval']}' , '{$v['resultshowvalrules']}' , {$v['id']})";
				}
			}
			
			if( is_array($mis_dynamic_controll_record_insert_field) && is_array($mis_dynamic_controll_record_insert_data) ){
				$mis_dynamic_controll_record_insert_sql .= "(" . join(',' , $mis_dynamic_controll_record_insert_field) . ") values" . join(',', $mis_dynamic_controll_record_insert_data);
			}else{
				$mis_dynamic_controll_record_insert_sql='';
			}
			if($mis_dynamic_controll_record_insert_sql){
				$ret = $obj->query($mis_dynamic_controll_record_insert_sql);
				if( false === $ret ){
					$msg = "复制{$formErrorShowName}表失败！".$obj->getLastSql();
					throw new NullDataExcetion( $msg );
				}
			}
		}
		/***********************************************************************************************************************************/
		/*			 mis_dynamic_form_datatable		数据表格附加记录																								*/
		/***********************************************************************************************************************************/
		$formErrorShowName='数据表格附加记录';
		$mis_dynamic_form_datatable_copytSql = <<<EOF
		SELECT * FROM mis_dynamic_form_datatable WHERE formid={$souceFormID}
EOF;
		
		$mis_dynamic_form_datatable_copytData= $obj->query($mis_dynamic_form_datatable_copytSql);
		if(is_array($mis_dynamic_form_datatable_copytData)){		
			//echo $obj->getLastSql();
			//定义开如结构
			$mis_dynamic_form_datatable_insert_sql='insert into mis_dynamic_form_datatable';
			// 拼接后的sql数组
			$mis_dynamic_form_datatable_insert_data='';
			// 插入字段数组
			$mis_dynamic_form_datatable_insert_field=array('fieldname', 'fieldtitle' ,'fieldtype' , 'fieldlength' ,'category' , 'unit' , 'unitls' , 'config' , 'formid' , 'propertyid', 'isshow' ,'isedit' , 'iscount' , 'tablename', 'colwidth', 'fieldsort', 'formula', 'calculate', 'validdigit','correspondfield', 'subassembly','sourceid');
			$sourceData = array();
			foreach ($mis_dynamic_form_datatable_copytData as $k =>$v){
				$tempPropid = $v['propertyid'];
				$dealProptyId = $relationData[$tempPropid];
				//拼装一个原数据的字段、id对应关系数组 后面要用
				$sourceData[$v['fieldname']]=$v['id'];		
				if( $dealProptyId){																																																		
					$mis_dynamic_form_datatable_insert_data[]="('{$v[fieldname]}','{$v[fieldtitle]}','{$v[fieldtype]}','{$v[fieldlength]}','{$v[category]}','{$v[unit]}','{$v[unitls]}','{$v[config]}','{$curFormID}','{$dealProptyId}','{$v[isshow]}','{$v[isedit]}','{$v[iscount]}','{$v[tablename]}','{$v[colwidth]}','{$v[fieldsort]}','{$v[formula]}','{$v[calculate]}','{$v[validdigit]}','{$v[correspondfield]}','{$v[subassembly]}','{$v[id]}')";
					//$mis_dynamic_form_datatable_insert_data[]="('{$dealProptyId}' ,'{$curFormID}' ,'{$v['typeval']}' , '{$v['resultval']}' , '{$v['createid']}', '{$v['createtime']}', '{$v['updateid']}' , '{$v['updatetime']}' , '{$v['roleinexp']}', '{$v['resultvalshow']}' , '{$v['status']}' ,'{$v['resultshowval']}' , '{$v['resultshowvalrules']}' , '{$v['id']}')";
				}
			}
			if( is_array($mis_dynamic_form_datatable_insert_field) && is_array($mis_dynamic_form_datatable_insert_data) ){
				$mis_dynamic_form_datatable_insert_sql .= "(" . join(',' , $mis_dynamic_form_datatable_insert_field) . ") values" . join(',', $mis_dynamic_form_datatable_insert_data);
			}else{
				$mis_dynamic_form_datatable_insert_sql='';
			}
			if($mis_dynamic_form_datatable_insert_sql){
				$ret = $obj->query($mis_dynamic_form_datatable_insert_sql);
				if( false === $ret ){
					$msg = "复制{$formErrorShowName}表失败3333！".$obj->getDBError().$obj->getLastSql();
					throw new NullDataExcetion( $msg );
				}else{
					$datatableselectsql = "SELECT * FROM mis_dynamic_form_datatable WHERE formid={$curFormID}";
					$newdatatablelist = $obj->query($datatableselectsql);
					$datatableRelationData = array();
					//利用上面拼装好的fieldname 和id关系数组，找到原表id和复用表id的对应关系
					foreach($newdatatablelist as $dk=>$dv){
						$datatableRelationData[$sourceData[$dv['fieldname']]] = $dv["id"];						
					}
					//遍历复用表数据，对引用了原表id的字段更新为复用表的对应id
					foreach($newdatatablelist as $dk1=>$dv1){
						$jsoncorrespondfield='';
						//已知的有引用id关系的字段为correspondfield，将里面的id进行替换
						if($dv1['correspondfield']){
							$correspondfield = json_decode($dv1['correspondfield'],true);
							foreach($correspondfield as $key=>$val){
								$correspondfield[$key]=$datatableRelationData[$val];
							}
							$jsoncorrespondfield = json_encode($correspondfield);
							$datatableupdatesql = "UPDATE mis_dynamic_form_datatable SET `correspondfield`='{$jsoncorrespondfield}' WHERE id={$dv1['id']}";
							$rest = $obj->query($datatableupdatesql);
							if($rest===false){
								$msg = "复制{$formErrorShowName}表失败！".$obj->getDBError().$obj->getLastSql();
								throw new NullDataExcetion( $msg );
							}
						}
					}
					
				}
			}
		}
		/***********************************************************************************************************************************/
		/*			 mis_dynamic_form_indatatable		生单设置																											*/
		/***********************************************************************************************************************************/
		$formErrorShowName='生单设置';
		$mis_dynamic_form_indatatable_ids = array_keys($relationData);
		if( is_array($mis_dynamic_form_indatatable_ids) ){
			$ids = join(',', $mis_dynamic_form_indatatable_ids);
			$mis_dynamic_form_indatatable_copytSql = <<<EOF
			SELECT * FROM mis_dynamic_form_indatatable WHERE proid in ({$ids})
EOF;
			$mis_dynamic_form_indatatable_copytData= $obj->query($mis_dynamic_form_indatatable_copytSql);
			if( is_array( $mis_dynamic_form_indatatable_copytData ) ){
				//定义开如结构
				$mis_dynamic_form_indatatable_insert_sql='insert into mis_dynamic_form_indatatable';
				// 拼接后的sql数组
				$mis_dynamic_form_indatatable_insert_data='';
				// 插入字段数组
				$mis_dynamic_form_indatatable_insert_field=array('dataname', 'datafieldname' ,'datainname' , 'datainfieldname' ,'datalookuporg' , 'datalookcom' , 'proid' , 'status', 'souceid');
				
				foreach ($mis_dynamic_form_indatatable_copytData as $k =>$v){
					$tempPropid = $v['properyid'];
					$dealProptyId = $relationData[$tempPropid];
					if ($dealProptyId){
						$mis_dynamic_form_indatatable_insert_data[]="('{$v['dataname']}' ,'{$v['datafieldname']}' ,'{$v['datainname']}' , '{$v['datainfieldname']}' , '{$v['datalookuporg']}', '{$v['datalookcom']}', {$dealProptyId} , {$v['status']} , {$v['id']})";
					}
				}
				
				if( is_array($mis_dynamic_form_indatatable_insert_field) && is_array($mis_dynamic_form_indatatable_insert_data) ){
					$mis_dynamic_form_indatatable_insert_sql .= "(" . join(',' , $mis_dynamic_form_indatatable_insert_field) . ") values" . join(',', $mis_dynamic_form_indatatable_insert_data);
				}else{
					$mis_dynamic_form_indatatable_insert_sql='';
				}
				
				if($mis_dynamic_form_indatatable_insert_sql){
					$ret = $obj->query($mis_dynamic_form_indatatable_insert_sql);
					if( false === $ret ){
						$msg = "复制{$formErrorShowName}表失败！".$obj->getLastSql();
						throw new NullDataExcetion( $msg );
					}
				}
			}
		}
	}
	/**
	 * 复制表单复用主表组件配置信息。
	 * @Title: copyPropertyData
	 * @Description: todo(从主表中复制属于主的组件到当前新表单中) 
	 * @param int 	$souceFormID	来源表单ID  
	 * @param int 	$curFormID 		当前表单ID 
	 * @author quqiang 
	 * @date 2015年4月13日 上午11:46:46 
	 * @throws
	 */
	function copyPropertyData($souceFormID , $curFormID){
		if( empty($souceFormID) ||  empty($curFormID) ){
			$msg = '表单属性复制失败，来源表单编号或当前表单编号未知'.$souceFormID.'___'.$curFormID;
			throw new NullDataExcetion($msg);
		}
		/**
		 * 步骤：
		 * 1.获取来源表的主表  与 当前表  的sub表属性对应关系
		 * 2.取得来源表的 主表property记录
		 */
		
		/*
		 *	定义对象 
		 */
		// 表单表
		$misDynamicDatabaseMasObj = M('mis_dynamic_database_mas');
		$obj = M();
		
		$tplid = $this->selectOrInsertTplInfo($curFormID);
		// 取出来源与当前表的 sub 字段对应关系
		$sql=<<<EOF
		
SELECT  s1.id , s1.`masid` , s1.`field` , s2.id AS sid , s2.`masid` AS smasid , s2.`field` AS sfield FROM 
`mis_dynamic_database_sub`  AS s1
RIGHT JOIN 
mis_dynamic_database_sub AS s2
ON s1.field=s2.field
WHERE 
s1.masid=(SELECT id FROM `mis_dynamic_database_mas` WHERE formid={$souceFormID} AND isprimary=1) 
AND s2.masid=(SELECT id FROM `mis_dynamic_database_mas` WHERE formid={$curFormID} AND isprimary=1)
EOF;
		
		// 来源表的主表  与 当前表  的sub表属性对应关系
		$relationData = $obj->query($sql);
		foreach ($relationData as $k=>$v){
			$temp[$v['id']] = $v['sid'];
		}
		$relationData = $temp;
		// 获取出来源表中的可用property记录
		$propertySql = <<<EOF

SELECT * FROM `mis_dynamic_form_propery` 
WHERE ids IN(
SELECT id FROM `mis_dynamic_database_sub` 
WHERE masid=(SELECT id FROM `mis_dynamic_database_mas` WHERE formid={$souceFormID} AND isprimary=1) 
) OR ids = 0 AND formid = {$souceFormID}
EOF;
		
		$soucePropertyData = $obj->query($propertySql);
		
		$propertySql='insert into mis_dynamic_form_propery';
		$propertyInsetField='';
		$propertyInsetData='';
		$categorylist=$this->getAllProperty();
		foreach( $soucePropertyData as $key => $val ){
			$tempProperty=array();
			$arrayKeys = $val[$categorylist['fields']['dbfield']];
			foreach ($categorylist as $k1=>$v1){
				if($k1 == 'id' || $v1['type'] =='hr' ){
						continue;
				}
				if($key==0 && $v1['dbfield']){
					$propertyInsetField[$v1['dbfield']] = '`'.$v1['dbfield'].'`';
				}
				if( $v1['dbfield'] ){
					// 替换掉现的fromid与ids值
					switch( $v1['dbfield'] ){
						case 'ids':
							if($val[$v1['dbfield']] == 0){
								$tempIds = 0;
							}else{
								$tempIds = $relationData[$val[$v1['dbfield']]];
								if(!$tempIds){
									$msg = '复制sub字段记录后，对应关系错误！'.$val[$v1['dbfield']].'__'.$v1['dbfield'].'__'.arr2string($relationData);
									throw new NullDataExcetion( $msg );
								}
							}
							$tempProperty[$v1['dbfield']]= "'".$tempIds."'";
							break;
						case 'isforeignfield':
							$tempProperty[$v1['dbfield']]="'".$val['id']."'";
							break;
						default:
							$tempProperty[$v1['dbfield']]="'".$val[$v1['dbfield']]."'";
							break;
					}
				}
			}
			if(is_array($tempProperty)){
				$tempProperty['formid']=$curFormID;
				$tempProperty['tplid']=$tplid;
				$tempProperty['souceid']=$val['id'];
				$propertyInsetData[$arrayKeys] ="(". join(',', $tempProperty).")";
			}
		}
		if( is_array($propertyInsetField) && is_array($propertyInsetData) ){
			$propertyInsetField['formid'] = 'formid';
			$propertyInsetField['tplid'] = 'tplid';
			$propertyInsetField['souceid'] = 'souceid';
			$propertySql .= "(" . join(',' , $propertyInsetField) . ") values" . join(',', $propertyInsetData);
		}else{
			$propertySql='';
		}
		
		
		if(!$propertySql){
			$msg = '复制组件属性表时来源数据解析错误！';
			throw new NullDataExcetion( $msg );
		}
		logs($propertySql);
		$ret = $obj->query($propertySql);
		if( false === $ret ){
			$msg = '复制组件属性表失败！'.$obj->getDBError().$obj->getLastSql();
			throw new NullDataExcetion( $msg );
		}
		
// 		// 取出来源与当前表的 property表 字段对应关系
// 		$prosql=<<<EOF
		
// SELECT
// (SELECT id FROM `mis_dynamic_form_propery` WHERE ids = souce.xid) AS xpid,
// (SELECT id FROM `mis_dynamic_form_propery` WHERE ids = souce.yid) AS ypid FROM (
// SELECT  s1.id AS xid , s2.id AS yid FROM
// `mis_dynamic_database_sub`  AS s1
// RIGHT JOIN
// mis_dynamic_database_sub AS s2
// ON s1.field=s2.field
// WHERE
// s1.masid=(SELECT id FROM `mis_dynamic_database_mas` WHERE formid={$souceFormID} AND isprimary=1)
// AND s2.masid=(SELECT id FROM `mis_dynamic_database_mas` WHERE formid={$curFormID} AND isprimary=1)
// ) AS souce
// EOF;
		
// 		// 来源表的主表  与 当前表  的sub表属性对应关系
// 		$relationProDataSouce = $obj->query($prosql);
// 		unset($protemp);
// 		unset($relationProData);
// 		foreach ($relationProDataSouce as $k=>$v){
// 			if($v['ypid'] && $v['xpid'] ){
// 				$protemp[$v['ypid']] =$v['xpid'];
// 			}
// 		}
// 		$relationProData = $protemp;
		
// 		$proupdatesql = "INSERT INTO `mis_dynamic_form_propery`(`id`,`isforeignfield`) VALUES ";
// 		unset($proupdateArr);
// 		foreach ($relationProData as $id => $isforeignfield) {
// 			if($id && $isforeignfield){
// 				$proupdateArr[] = "({$id},{$isforeignfield})";
// 			}
// 		}
// 		if(is_array($proupdateArr)){
// 			$proupdatesql .= join(',', $proupdateArr);
// 			$proupdatesql .=" ON DUPLICATE  KEY UPDATE `isforeignfield`=VALUES(`isforeignfield`)";
// 		}else{
// 			$proupdatesql = '';
// 		}
		
// 		if(!$proupdatesql){
// 			$msg = '修正复制组件属性表中来源时来源数据解析错误！';
// 			throw new NullDataExcetion( $msg );
// 		}
		
// 		$proupDataRet = $obj->query($proupdatesql);
// 		if(false === $proupDataRet){
// 			$msg = "修正复制组件属性表中来源时失败！！{$proupdatesql}";
// 			throw new NullDataExcetion( $msg );
// 		}
// 		$retSql =  $obj->getLastSql();
// 		throw new NullDataExcetion('我是出错 ， 出来'.$retSql);
	}
	/**
	 * 复制mas表记录
	 * @Title: copyMasData
	 * @Description: todo(这里用一句话描述这个方法的作用) 
	 * @param int 	$souceFormID	来源表单ID  
	 * @param int 	$curFormID 		当前表单ID 
	 * @throws NullDataExcetion  
	 * @author quqiang 
	 * @date 2015年4月13日 下午4:26:58 
	 * @throws
	 */
	function copyMasData($souceFormID , $curFormID){
		if( empty($souceFormID) ||  empty($curFormID) ){
			$msg = '复制失败，来源表单编号或当前表单编号未知'.$souceFormID.'___'.$curFormID;
			throw new NullDataExcetion($msg);
		}
		/**
		 * 操作步骤：
		 * 1.取出主表mas记录，插入新记录
		 */
		/*
		 *	定义对象
		 */
		// 表单表
		$misDynamicDatabaseMasObj = M('mis_dynamic_database_mas');
		$map['isprimary']=1;
		$map['formid'] = $souceFormID;
		$data = $misDynamicDatabaseMasObj->where($map)->find();
		$souceid = $data['id'];
		unset($data['id']);
		$data['ischoise']=1;
		$data['formid']=$curFormID;
		$data['modelname']=$this->nodeName;
		$data['souceid'] = $souceid;
		$ret = $misDynamicDatabaseMasObj->add($data);
		if(false === $ret){
			$msg = '复制mas表内容失败!'.$misDynamicDatabaseMasObj->getLastSql();
			throw new NullCreateOprateException($msg);
		}
	}
	/**
	 * 复制sub表记录
	 * @Title: copySubData
	 * @Description: todo(这里用一句话描述这个方法的作用) 
	 * @param unknown $souceFormID
	 * @param unknown $curFormID
	 * @throws NullDataExcetion  
	 * @author quqiang 
	 * @date 2015年4月13日 下午4:27:02 
	 * @throws
	 */
	function copySubData($souceFormID , $curFormID){
		if( empty($souceFormID) ||  empty($curFormID) ){
			$msg = '复制失败，来源表单编号或当前表单编号未知'.$souceFormID.'___'.$curFormID;
			throw new NullDataExcetion($msg);
		}
		/*
		 *	定义对象
		 */
		// 表记录
		$misDynamicDatabaseMasObj = M('mis_dynamic_database_mas');
		// 字段记录
		$misDynamicDatabaseSubObj = M('mis_dynamic_database_sub');
		/*
		 *  取和来源表的表记录及字段记录
		 */
			//		取得来源表表记录
		$map['isprimary']=1;
		$map['formid'] = $souceFormID;
		$masData = $misDynamicDatabaseMasObj->where($map)->find();
			//		取得来源表字段记录
		$souceSubMap['masid'] = $masData['id'];
		$souceSubData = $misDynamicDatabaseSubObj->where($souceSubMap)->select();
		/*
		 * 取得目标表单 的表 记录 
		 */
		$orderFormMap['isprimary'] = 1;
		$orderFormMap['formid'] = $curFormID;
		$orderMasData = $misDynamicDatabaseMasObj->where($orderFormMap)->find();
		
		// 复制数据开始
		// ,`randnum`
		$orderSql='insert into mis_dynamic_database_sub(`masid`,`rowid`,`souceid`,`field`,`title`,`type`,`length`,`category`,`weight`,`sort`,`isshow`,`isdatasouce`,`status`,`formid`,`modelname`,`ischoise`) values';
		foreach ($souceSubData as $key => $val){
			$val['weight'] = $val['weight'] ? 1: 0;
			$val['sort'] = $val['sort']=='' ? 0: $val['sort'];
			$val['isshow'] = $val['isshow'] ? 1: 0;
			$val['isdatasouce'] = $val['isdatasouce'] ? 1: 0;
			$val['status'] = $val['status'] ? 1: 0;
			unset($str);
			// ,{$val['rowid']}
			$str ="({$orderMasData['id']}, '{$val['rowid']}' ,'{$val['id']}', '{$val['field']}', '{$val['title']}' ".
					", '{$val['type']}' , '{$val['length']}', '{$val['category']}',".
					" {$val['weight']}, {$val['sort']}, {$val['isshow']}, {$val['isdatasouce']} ,".
					" {$val['status']} , {$curFormID} , '{$this->nodeName}',1)";

			$orderSqlArr[] = $str;
		}
		if(is_array($orderSqlArr)){
			$orderSql .= join(',', $orderSqlArr);
		}else {
			$orderSql = '';
		}
		if(!$orderSql){
			$msg = '复制sub表时来源数据解析错误！';
			throw new NullDataExcetion( $msg );
		}
		
		$ret = $misDynamicDatabaseSubObj->query($orderSql);
		if( false === $ret ){
			$msg = '复制sub表失败！'.$misDynamicDatabaseSubObj->getDbError().$misDynamicDatabaseSubObj->getLastSql();
			throw new NullDataExcetion( $msg );
		}
		
	}
	
	
	
	
	
	
	/**
	 * @Title: insertDatebase
	 * @Description: todo(插入到相应到相应的数据记录表中)
	 * @param unknown_type $alist anamalist 返回数组
	 * @param unknown_type $misDaddId   misd记录表新增id
	 * @author quqiang
	 * @date 2014-12-10 上午10:39:39
	 * @throws
	 */
	private function insertDatebase($alist,$misDaddId,$tplname='index'){
		logs('C:'.__CLASS__.' L:'.__LINE__.'插入到相应到相应的数据记录表中：模板名称 。'.$tplname);
		logs('C:'.__CLASS__.' L:'.__LINE__.'插入到相应到相应的数据记录表中：misd记录表新增id。'.$misDaddId);
		logs('C:'.__CLASS__.' L:'.__LINE__.'插入到相应到相应的数据记录表中：源数组。'.$this->pw_var_export($alist),date('Y-m-d' , time()).'_data.log');
		//表名称记录
		$MisDynamicDatabaseMasModel=D("MisDynamicDatabaseMas");
		//表字段记录
		$MisDynamicDatabaseSubModel=D("MisDynamicDatabaseSub");
		//字段主键记录
		$MisDynamicFormProperyModel=D("MisDynamicFormPropery");
		//模型模板
		$MisDynamicFormTemplateModel=D("MisDynamicFormTemplate");
		if(!getFieldBy($misDaddId, "formid", "id", "mis_dynamic_form_template","tplname",$tplname)){
			$tempDate=array();
			$tempDate['tplname']="index";
			$tempDate['tpltitle']="初始模板";
			$tempDate['formid']=$misDaddId;
			$MisDynamicFormTemplateRet = $MisDynamicFormTemplateModel->add($tempDate);
// 			$MisDynamicFormTemplateModel->commit();
			if(!$MisDynamicFormTemplateRet){
				$msg = "模板记录存储信息失败！".$MisDynamicFormTemplateModel->getLastSql();
				logs($msg , '' ,ROOT . '/Dynamicconf/createFormLog');
				throw new NullDataExcetion($msg);
			}
			
		}
		if($tplname){
			$tplData['id']=getFieldBy($misDaddId, "formid", "id", "mis_dynamic_form_template","tplname",$tplname);
		}else{
			$tplData=$MisDynamicFormTemplateModel->where(array('tplname'=>'index','formid'=>$misDaddId))->find();
		}
		$tplid = $tplData['id']?$tplData['id']:null;
		$filedData=array();
		
		
		/*
		 * 处理系统字段组件化问题。
		 * 系统字段的组件固定为文本框组件。
		 * 不入字段记录表，组件记录直接写入组件属性表中。
		 *  */
		// 处理系统字段组件化问题。
		$systemFieldInsertIndex=0;
		$systemFieldData = $_POST['system'];
		if($systemFieldData){
			foreach ($systemFieldData['fieldname'] as $k=>$v){
				if($systemFieldData['fieldshow'][$k] == 1){
					$systemFieldInsertIndex++;
				}
			}
		}
		foreach ($alist as $k1=>$v1){
			if(!$v1['ischoise']){
				//表信息
				$masDate=array();
				//添加表主要信息
				$masDate['tablename']=$v1['tablename'];
				$masDate['tabletitle']=$v1['tabletitle'];
				$masDate['isprimary']=$v1['isprimay'];
				$masDate['ischoise']=$v1['ischoise'];
				$masDate['formid']=$misDaddId;
				$masDate['modelname']=$this->nodeName;
				$MisDynamicDatabaseMasResult=$MisDynamicDatabaseMasModel->add($masDate);
				//$MisDynamicDatabaseMasModel->commit();
				if(!$MisDynamicDatabaseMasResult){
					$msg = "主表记录内容写入信息失败！".$MisDynamicDatabaseMasModel->getLastSql();
					logs($msg , '' ,ROOT . '/Dynamicconf/createFormLog');
					throw new NullDataExcetion($msg);
				}
			foreach ($v1['list'] as $k3=>$v3){
				$aci++;
				$subdata=array();
				$subdata['desc']=$v3['desc'];
				$subdata['rowid']=$v3['field'];
				$subdata['field']=$v3['field'];
				$subdata['title']=$v3['title'];
				$subdata['type']=$v3['type'];
				$subdata['masid']=$MisDynamicDatabaseMasResult;
				$subdata['ischoise']=$v1['ischoise'];
				$subdata['length']=$v3['length'];
				$subdata['weight']=$v3['weight'];
				$subdata['sort']=$v3['sort'];
				$subdata['formid']=$misDaddId;
				$subdata['category'] = $v3['category'];
				$subdata['isdatasouce']=$v3['isdatasouce'];
				$subdata['unique']=$v3['unique'];
				unset($subdata['id']);
				$subResult=$MisDynamicDatabaseSubModel->add($subdata);
				if(!$subResult){
					$msg = "表字段记录内容写入信息失败！".$MisDynamicDatabaseSubModel->getLastSql();
					logs($msg , '' ,ROOT . '/Dynamicconf/createFormLog');
					throw new NullDataExcetion($msg);
				}
				//字段显示时插入proptey
				if($v3['isshow']==1){
					$systemFieldInsertIndex ++;
					$controlsList=$this->privateProperty;
					//字段信息
					$propdata=array();
					unset($propdata);
					$propdata['ids']=$subResult;
					$propdata['titlepercent']=$controlsList[$v3['category']]['titlepercent']['default'];
					$propdata['contentpercent']=$controlsList[$v3['category']]['contentpercent']['default'];
					$propdata['category'] = $v3['category'];
					$propdata['sort']=$systemFieldInsertIndex;//$v3['sort']; // 新增时有系统字段时直接按最大sort值插入。
					$propdata['fieldname'] = $v3['field'];
					$propdata['title'] = $v3['title'];
					$propdata['createid'] = $_SESSION[C('USER_AUTH_KEY')];
					$propdata['createtime']=time();
					$propdata['formid']=$misDaddId;
					$propdata['tplid'] = $tplid;
					$propdata['isforeignfield'] = $v3['isforeignfield'];
					if( is_array( $this->system_field_config[$v3['field']] ) ){
						$propdata =array_merge($propdata , $this->system_field_config[$v3['field']]);
					}
					
					//$propdata['dbname'] = $v1['tablename'];
					logs(arr2string($v3) , 'dataaddprop');
					// lookup时默认生成org值
					$propdata['lookupgrouporg']='';
					if( $v3['category'] == 'lookup' || $v3['category'] == 'lookupsuper' ){
						$propdata['lookupgrouporg']='orgDBA'.$subResult;
					}
					// 20141213 @nbmxkj 属性中添加字段所属表信息
					$propdata['dbname'] = $v1['tablename'];
					$propdata['tablelength'] = $v3['length'];
					$propdata['fieldtype'] = $v3['type'];

					$MisDynamicFormProperyResult = $MisDynamicFormProperyModel->add($propdata);
					logs('C:'.__CLASS__.' L:'.__LINE__.'插入属性表记录：'.$MisDynamicFormProperyModel->getLastSql());
					// $MisDynamicFormProperyResult=$MisDynamicFormProperyModel->commit();
					if(!$MisDynamicFormProperyResult){
						$msg = "表单组件默认属性写入信息失败！".$MisDynamicFormProperyModel->getLastSql();
						logs($msg , '' ,ROOT . '/Dynamicconf/createFormLog');
						throw new NullDataExcetion($msg);
					}
				}
				//$MisDynamicFormFieldModel->add($data);
				//$MisDynamicFormFieldModel->commit();
				//logs('C:'.__CLASS__.' L:'.__LINE__.$MisDynamicFormFieldModel->getLastSql());
			}
		}
	}
		/*
		 * 处理系统字段组件化问题。
		 * 系统字段的组件固定为文本框组件。
		 * 不入字段记录表，组件记录直接写入组件属性表中。
		 *  */
		// 处理系统字段组件化问题。
		$systemFieldInsertIndex=0;
		logs('C:'.__CLASS__.' L:'.__LINE__.'处理系统字段组件化问题：源数组。'.$this->pw_var_export($systemFieldData),date('Y-m-d' , time()).'_data.log');
		// 条件中去除以下代码，因为加上后新增时不会追加系统字段。 modify by nbmxkj@20150610 1837
		// &&$alist[$systemFieldInsertIndex]['ischoise']
		$s = $this->isforeignfield ? 1 : 0;
		$ss = $alist[$systemFieldInsertIndex]['ischoise'] ? 1 : 0;
		logs(   $s , 'coverrform' , '' , __CLASS__ , __FUNCTION__ , __METHOD__ );
		logs(   $ss , 'coverrform' , '' , __CLASS__ , __FUNCTION__ , __METHOD__ );
		logs($alist , 'coverrform' , '' , __CLASS__ , __FUNCTION__ , __METHOD__ );
		// 
		$checkSystemFieldUse = true;
		if($this->isforeignfield){
			$checkSystemFieldUse = false;
		}
		
		if($systemFieldData && $checkSystemFieldUse ){
			foreach ($systemFieldData['fieldname'] as $k=>$v){
				if( $systemFieldData['fieldshow'][$k] == 1 ){
					unset($propdata);
					$propdata['ids']=0;
					$propdata['category'] = $systemFieldData['fieldcategory'][$k];
					$propdata['sort']=$systemFieldInsertIndex;
					$propdata['fieldname'] = $systemFieldData['fieldname'][$k];
					$propdata['title'] = $systemFieldData['fieldtitle'][$k];
					$propdata['createid'] = $_SESSION[C('USER_AUTH_KEY')][$k];
					$propdata['createtime']=time();
					$propdata['formid']=$misDaddId;
					$propdata['tplid'] = $tplid;
					$propdata['titlepercent'] = 1; //标题占比
					$propdata['contentpercent'] = 3; //内容占比
					$propdata['dbname'] = $this->getTrueTableName();
					logs('C:'.__CLASS__.' L:'.__LINE__.'处理系统字段组件化问题：被构建的DB表结构数组。'.$this->pw_var_export($propdata),date('Y-m-d' , time()).'_data.log');
					unset($MisDynamicFormProperyResult);
					$MisDynamicFormProperyResult = $MisDynamicFormProperyModel->add($propdata);
					// $MisDynamicFormProperyResult=$MisDynamicFormProperyModel->commit();
					logs('C:'.__CLASS__.' L:'.__LINE__.'处理系统字段组件化问题：属性记录表插入记录：'.$MisDynamicFormProperyModel->getLastSql());
					if(!$MisDynamicFormProperyResult){
						$msg = "表单组件默认属性写入信息失败！".$MisDynamicFormProperyModel->getLastSql();
						logs($msg , '' ,ROOT . '/Dynamicconf/createFormLog');
						throw new NullDataExcetion($msg);
					}
					
					$systemFieldInsertIndex++;
				}
			}
		}
	}
	/**
	 *
	 * @Title: getPrimaryAName
	 * @Description: todo(生成aciton名称 并检测action文件是否重名)
	 * @return string
	 * @author renling
	 * @date 2014-9-26 下午3:28:40
	 * @throws
	 */
	public function getPrimaryAName(){
		$str =$this->genRandomString(3);
		$Actionname="MisAuto".ucfirst($str);
		if(getFieldBy($Actionname, "actionname", "id", "mis_dynamic_form_manage")){
			return $this->getPrimaryAName();
		}else{
			$path=LIB_PATH."Action/".$Actionname."Action.class.php";
			if(file_exists($path)){
				return $this->getPrimaryAName();
			}else{
				return $Actionname;
			}
		}
	}
	/**
	 *
	 * @Title: looktablename
	 * @Description: todo(查询当前数据库中所有表名)
	 * @author renling
	 * @date 2014-9-25 上午10:56:23
	 * @throws
	 */
	public function looktablename($type,$name){
		$MisDynamicFormManageModel=D("MisDynamicFormManage");
		if($type){
			$str =$this->genRandomString(3);
			$tablename=$name."_".$str."sub";
		}else{
			$str =$this->genRandomString(5);
			$tablename="mis_auto_".$str;
		}
		//根据表名称查询是否重复
		$sql="SELECT	TABLES.TABLE_NAME FROM information_schema.TABLES WHERE TABLES.TABLE_SCHEMA = '".C('DB_NAME')."' and TABLES.TABLE_NAME='".$tablename."'";      $resultList=$MisDynamicFormManageModel->query($sql);
		$result=$MisDynamicFormManageModel->query($sql);
		if($result){
			$this->looktablename($type,$name);
		}
		return $tablename;
	}





	/**************************			函数			*********************************/




	/**
	 * @Title: craeteDatabaseAppend
	 * @Description: todo(生成数据表,添加时 直接生成表，非添加时当字段信息中ids项为空时做字段追加操作。)
	 * @param array $filedData	字段信息
	 * @author quqiang
	 * @date 2014-9-11 上午9:51:09
	 * @throws
	 */
	private function craeteDatabaseAppend($filedData , $isadd=false){
		// 主表
		$mainTable = '';
		// 字段创建语句
		$createtable_str='';
		// 子表创建字段信息.[组件需要的表]
		$suffTableData=array();
		// 从表
		$childData = array();
		// 子表字段信息
		$sql = $this->getUserSetFiledsArr($filedData);
		$childData = $sql['child'];
		$suffTableData= $sql['suff'];
		foreach ($childData	as $key => $value) {
			$modeTemp = M($key);
			$createtable_str = $value?'ALTER TABLE `'.$key.'` ADD '.join(', ADD', $value):'';
		}

		if($createtable_str){
			$tmodel=M();
			$res2 = $tmodel->query($createtable_str);
			if(strlen($tmodel->getDbError())){
				$this->error('字段追加失败。'.$tmodel->getDbError());
			}
		}
		// 将追加字段信息写回字段信息记录表 屈强@20141008 16:23
		$this->createChildTable($suffTableData);
	}



	/**
	 * @Title: getUserSetFiledsArr
	 * @Description: todo(获取到当前可用的字段生成语句数组)
	 * @param unknown_type $fieldData
	 * @author quqiang
	 * @date 2014-9-11 上午10:00:28
	 * @throws
	 */
	private function getUserSetFiledsArr($fieldData){
		$controllProperty = $this->controlConfig;
		$privateProperty = $this->privateProperty;
		$publicProperty = $this->publicProperty;
		$sqlArr=array();
		$congbiaoArr=array();
		/**
		 * 数据表格的字段生成记录
		 */
		$datatableSql=array();
		
		foreach($fieldData['conf'] as $k=>$v ){
			$property = $this->getProperty($controllProperty['catalog']);
			$iscreateDB = $controllProperty[$v[$publicProperty['catalog']['name']]]['iscreate'];
			if($iscreateDB && !$v[$publicProperty['ids']['name']]){
				switch (strtolower($v[$publicProperty['catalog']['name']])){
					case "text":
						if( !$v[$publicProperty['tabletype']['name']] ){
							$v[$publicProperty['tabletype']['name']]="varchar";
						}
						if( !$v[$publicProperty['length']['name']] ){
							$v[$publicProperty['length']['name']]="100";
						}
						break;
					case "date":
						if( !$v[$publicProperty['tabletype']['name']] ){
							$v[$publicProperty['tabletype']['name']]="int";
						}
						if( !$v[$publicProperty['length']['name']] ){
							$v[$publicProperty['length']['name']]="11";
						}
						break;
					case "textarea":
						$v[$publicProperty['tabletype']['name']]="text";
						$v[$publicProperty['length']['name']]="500";
						break;
					case "checkbox":
						if( !$v[$publicProperty['tabletype']['name']] ){
							$v[$publicProperty['tabletype']['name']]="text";
						}
						if( !$v[$publicProperty['length']['name']] ){
							$v[$publicProperty['length']['name']]="500";
						}
						break;
					case "select":
						if($v['linktable']){
							if( !$v[$publicProperty['tabletype']['name']] )
							$v[$publicProperty['tabletype']['name']]="int";
							if( !$v[$publicProperty['length']['name']] )
							$v[$publicProperty['length']['name']]="11";
						}else{
							if( !$v[$publicProperty['tabletype']['name']] )
							$v[$publicProperty['tabletype']['name']]="varchar";
							if( !$v[$publicProperty['length']['name']] )
							$v[$publicProperty['length']['name']]="100";
						}
						break;
					case "radio":
						if($v['linktable']){
							if( !$v[$publicProperty['tabletype']['name']] )
							$v[$publicProperty['tabletype']['name']]="int";
							if( !$v[$publicProperty['length']['name']] )
							$v[$publicProperty['length']['name']]="11";
						}else{
							if( !$v[$publicProperty['tabletype']['name']] )
							$v[$publicProperty['tabletype']['name']]="varchar";
							if( !$v[$publicProperty['length']['name']] )
							$v[$publicProperty['length']['name']]="100";
						}
						break;
				}

				// defaultval
				$ceatetable_html="`".$v[$publicProperty['fields']['name']]."` ".$v[$publicProperty['tabletype']['name']]."(".$v[$publicProperty['length']['name']].")";
				$ceatetable_html.=" DEFAULT NULL COMMENT '".$v[$publicProperty['title']['name']]."'";
				$congbiaoArr[$v[$publicProperty['tablename']['name']]][]=$ceatetable_html;
			}
		}
		foreach($fieldData['all'] as $k=>$v ){
			$property = $this->getProperty($controllProperty['catalog']);
			$iscreateDB = $controllProperty[$v[$publicProperty['catalog']['name']]]['iscreate'];
			if($iscreateDB && !$v[$publicProperty['ids']['name']]){
				switch (strtolower($v[$publicProperty['catalog']['name']])){
					case "text":
						if( !$v[$publicProperty['tabletype']['name']] ){
							$v[$publicProperty['tabletype']['name']]="varchar";
						}
						if( !$v[$publicProperty['length']['name']] ){
							$v[$publicProperty['length']['name']]="100";
						}
						break;
					case "date":
						if( !$v[$publicProperty['tabletype']['name']] ){
							$v[$publicProperty['tabletype']['name']]="int";
						}
						if( !$v[$publicProperty['length']['name']] ){
							$v[$publicProperty['length']['name']]="11";
						}
						break;
					case "textarea":
						$v[$publicProperty['tabletype']['name']]="text";
						$v[$publicProperty['length']['name']]="500";
						break;
					case "checkbox":
						if( !$v[$publicProperty['tabletype']['name']] ){
							$v[$publicProperty['tabletype']['name']]="text";
						}
						if( !$v[$publicProperty['length']['name']] ){
							$v[$publicProperty['length']['name']]="500";
						}
						break;
					case "select":
						if($v['linktable']){
							if( !$v[$publicProperty['tabletype']['name']] )
							$v[$publicProperty['tabletype']['name']]="int";
							if( !$v[$publicProperty['length']['name']] )
							$v[$publicProperty['length']['name']]="11";
						}else{
							if( !$v[$publicProperty['tabletype']['name']] )
							$v[$publicProperty['tabletype']['name']]="varchar";
							if( !$v[$publicProperty['length']['name']] )
							$v[$publicProperty['length']['name']]="100";
						}
						break;
					case "radio":
						if($v['linktable']){
							if( !$v[$publicProperty['tabletype']['name']] )
							$v[$publicProperty['tabletype']['name']]="int";
							if( !$v[$publicProperty['length']['name']] )
							$v[$publicProperty['length']['name']]="11";
						}else{
							if( !$v[$publicProperty['tabletype']['name']] )
							$v[$publicProperty['tabletype']['name']]="varchar";
							if( !$v[$publicProperty['length']['name']] )
							$v[$publicProperty['length']['name']]="100";
						}
						break;
				}

				// defaultval
				$ceatetable_html="`".$v[$publicProperty['fields']['name']]."` ".$v[$publicProperty['tabletype']['name']]."(".$v[$publicProperty['length']['name']].")";
				$ceatetable_html.=" DEFAULT NULL COMMENT '".$v[$publicProperty['title']['name']]."'";
				$sqlArr[]=$ceatetable_html;
			}

			/* 特殊的内嵌数据表处理 */
			if($v[$publicProperty['catalog']['name']]=="datatable"){
				// 取得从名后缀名
				$suff = $v[$publicProperty['fields']['name']];
				// 获取从表字段信息
				$jsonStr = $v[$privateProperty['datatable']['fieldlist']['name']];
				$obj = json_decode(htmlspecialchars_decode($jsonStr));
				$tebs['name']=$suff;
				$tebsAll['name']=$suff;
				foreach ($obj as $k=>$v){
					
					$fieldname=$v->fieldname;
					$fieldtype=$v->fieldtype?$v->fieldtype:'varchar';
					$v->fieldtype = strtolower($v->fieldtype);
					
					$fieldtype= $v->fieldtype ? ( $v->fieldtype =='date'?'int':$v->fieldtype ):'varchar';

					$fieldlength=$v->fieldlength?$v->fieldlength:10;
					$fieldntitle = $v->fieldtitle?$v->fieldtitle:'用户添加时未填写名称';
					$sql="`{$fieldname}` {$fieldtype}({$fieldlength}) DEFAULT NULL COMMENT '动态表单内嵌表单字段-{$suff}-{$fieldntitle}'";
					if(!$v->fieldid){
						$tebs['field'][]=$sql;
					}
					$tebsAll['field'][]=$sql;
					
				}
			}
			/***/

			if($tebs){
				$inserTables[]=$tebs;
				unset($tebs);
			}
			if($tebsAll){
				$inserAllTables[]=$tebsAll;
				unset($tebsAll);
			}
			
		}
		logs('C:'.__CLASS__.' L:'.__LINE__.'内嵌表列表sql创建数组:'.$this->pw_var_export($inserTables),date('Y-m-d' , time()).'_data.log');
		$datatableSql['change']= $inserTables;
		$datatableSql['all'] = $inserAllTables;
		return array('main'=>$sqlArr,'suff'=>$datatableSql , 'child'=>$congbiaoArr);
	}

	/**
	 * 生成内嵌子表
	 * @param array $data 子表结构数据
	 */
	private function createChildTable($data){
		logs('C:'.__CLASS__.' L:'.__LINE__.'子表结构数据数据源：'.$this->pw_var_export($data),date('Y-m-d' , time()).'_data.log');
		
		/*
		 * 数据表格的生成
		 * $data['all']	这是所有字段列表当移库或 或表不存在时使用，用以重新生成子表
		 * $data['change'] 这是用户新添加的字段属性，用以完成用户添加字段功能 
		*/
		$curtabnamePrefix=$this->tableName.'_sub_';
		$tables=array();
		$sqlStr = '';
		foreach ($data['change'] as $k=>$v){
			$curTableName = $curtabnamePrefix.$v['name'];
			// charset utf8; 设置编码
			$ceatetable_html_s="CREATE TABLE IF NOT EXISTS `".$curTableName."` (";
			$ceatetable_html_s.="`id` int(11) NOT NULL AUTO_INCREMENT  COMMENT 'ID'";
			$ceatetable_html_e=",PRIMARY KEY (`id`)";
			$ceatetable_html_e.=",`masid` int(11) COMMENT '关联动态表单数据ID'";
			$ceatetable_html='';
			if($data['all'][$k]['field']){
				$ceatetable_html = ','.join(',', $data['all'][$k]['field']);
			}
			// 外键删除功能
			$ceatetable_html.=",KEY `delete_{$curTableName}_{$v['name']}` (`masid`),";
			$ceatetable_html.="CONSTRAINT `delete_{$curTableName}` FOREIGN KEY (`masid`) REFERENCES `{$this->tableName}` (`id`) ON DELETE CASCADE";
			// 外键删除功能 end
			$sqlStr = $ceatetable_html_s.$ceatetable_html_e.$ceatetable_html.")ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='动态表单:{$this->tableName} 内嵌表 ".$curTableName."'";
			// 如果当前子表已存在，就做字段追加，否则就做表生成
			logs('C:'.__CLASS__.' L:'.__LINE__.'内嵌表 建表 sql:'.$sqlStr);
			$tmodel=M();
			if($this->validateTable($curTableName)){
				$createtable_str = $v['field']?'ALTER	TABLE `'.$curTableName.'` ADD '.join(', ADD', $v['field']):'';
				if($createtable_str){
					$res2 = $tmodel->query($createtable_str);
				}
			}else {
				$res2 = $tmodel->query($sqlStr);
			}
		}
	}

	/**
	 * 验证表是否存在
	 * @param string $tablename	表名
	 * @return int $num 0:不存在。大于1：表存在数量
	 */
	private function validateTable($tablename){
		$tmodel=M();
		$sql="SELECT COUNT(*) as counts FROM information_schema.TABLES WHERE table_schema ='".C('DB_NAME')."' AND table_name='{$tablename}'";
		$ret = $tmodel->query($sql);
		return $ret[0]['counts'];
	}

	/**
	 * @Title: modifyConfig
	 * @Description: todo(更新字段配置文件[MisAutoAnameList]和单号规则配置文件[list.inc][toolbar])
	 * @param array $fieldData	组件属性列表
	 * @param string $nodename	当前action名称
	 * @param boolean $isaudit	是否审核
	 * @param boolean $special 	是否为特殊
	 * @author quqiang
	 * @date 2014-8-22 上午09:38:49
	 * @throws
	 */
	private function modifyConfig($fieldData,$nodename , $isaudit , $special = false){
		logs('C:'.__CLASS__.' L:'.__LINE__.'更新字段配置文件[MisAutoAnameList]和单号规则配置文件[list.inc][toolbar]');
		//$formid = getFieldBy($nodename, "actionname", "id", "mis_dynamic_form_manage");
		// 1.获取到当前Action的组件配置文件信息
		// 获取所有节点信息
		//$allNodeconfig = $model->getAllControllConfig();
		/*$fieldDataTemp = $this->getfield\Category($formid);
		$fieldData = array();
		foreach($fieldDataTemp as $k=>$v){
			$fieldData = array_merge($fieldData, $v);
		}*/
		
		$fieldData = $this->getAllControllConfig();
		$nodename = ucfirst($nodename);
		$controllPropertyList = $this->controlConfig;
		$publicProperty = $this->publicProperty;
		$notshowcontroll = array();
		foreach ($controllPropertyList as $key=>$val){
			if(!$val['isconfig']){
				$notshowcontroll[] = $key;
			}
		}
		// 不允许用户设为可显示的组件
		// 'upload', 
		//$notshowcontroll = array('datatable','areainfo','fieldset','textarea');
		/**
		 * @modify quqiang
		 * @date 2014-10-8 下午06:00:07
		 * 从表字段配置方式改变,嵌套数据获取
		 *
		 */
		$primaryTableName  = $this->getTrueTableName();

		/**
		 * 步骤：
		 * 1。先源数据 $filedData['visibility'] 中的项按表名['tablename']分组。
		 * 2。区分出主从表，主表的文件名用当前action名命名，从表以直接表名命名。
		 * 3。遍历分组后的数据，依次生成Model文件。
		 *
		 */
			
		/**
		 *		表数组，基础为二维.
		 * 	0=>主表信息
		 * 	1=>所有从表的信息
		 */
		$tablenameArr = array();
		// 按真实表名分离数组
		foreach ($fieldData as $key => $value) {
			if($primaryTableName == $value[$publicProperty['tablename']['name']] ){
				// 分离主表
				$tablenameArr[0][$value[$publicProperty['tablename']['name']]][] = $value;
			}else{
				// 分离从表
				$tablenameArr[1][$value[$publicProperty['tablename']['name']]][] = $value;
			}
		}
		$controllPropertyList = $this->controlConfig;

		/**	 modify @20141008 1803 end **/

		/*********************************
		 *  生成权限、DB字段配置文件
		 *********************************/
		logs('C:'.__CLASS__.' L:'.__LINE__.'被分离后的主从表数据源：'.$this->pw_var_export($tablenameArr),date('Y-m-d' , time()).'_data.log');

		//$model = D('SystemConfigDetail');
		$detailList=$searchlist=$toolbar=array();
		//主表配置添加id项，是否影响子表等其他配置文件不知
		$detailList['id']=array(
			'name' => 'id',
			'showname' => 'ID',
			'shows' => '0',
			'widths' => '',
			'sorts' => count($detailList),
			'models' => '',
			'status' => '1',
			'sortname' => 'id',
			'sortnum' => count($detailList),
			'searchField' => $this->tableName.'.id',
			'conditions' => '',
			'type' => 'text',
			'issearch' => '1',
			'message'=>'1',
			'isallsearch' => '1',
			'searchsortnum' => '0'
			);
		$except=array("createid","updateid","createtime","updatetime","ptmptid","auditUser","alreadyAuditUser","ostatus");
		/**********************************************
		 *	开始处理基础档案的toolbar生成
		 **********************************************/
		$misdynamicform = M('mis_dynamic_form_manage');
		$formData = $misdynamicform->where("`actionname`='{$this->nodeName}'")->find();
		$temp = explode('#', $formData['tpl']);
		/**********************************************************
		 * 生成基础档案
		 *********************************************************/
		 logs('C:'.__CLASS__.' L:'.__LINE__.'开始生成基础档案');
		if($temp[0] == 'basisarchivestpl' || $temp[0] == 'basisarchivesaudittpl'){
			if($temp[0] == 'basisarchivestpl'){
				switch ($temp[1]){
					case 'ltrc':
						$toolbar = $this->getToolBar($isaudit,2);
						break;
					case 'ltrl':
						$toolbar = $this->getToolBar($isaudit,3);
						break;
				}
			}
		}else{
			$toolbar = $this->getToolBar($isaudit);
		}
		$controllProperty = $this->getProperty($category);
		$k1=0;
		$shownumber=8;
		$shownumberCount=1;
		
		$isneedcreateOrderNoConfig=true;
		/**
		 * 主表的配置生成
		 */
		foreach($tablenameArr[0] as $key=>$val){
			foreach ($val as $k => $v1) {
				$iscreateDB = $controllPropertyList[$v1[$publicProperty['catalog']['name']]]['iscreate'];
				$iscreateConfifg = $controllPropertyList[$v1[$publicProperty['catalog']['name']]]['isconfig'];
				
				if($v1[$publicProperty['fields']['name']] == 'orderno'){
					$isneedcreateOrderNoConfig = false;
				}
				if($iscreateDB){
					$this->getTagsListincData($detailList , $v1 , $notshowcontroll , $v1[$publicProperty['fields']['name']] , $shownumber , $shownumberCount,$k1);
					$k1++;
				}
			}
		}

		$k1 = count($detailList);


		/**
		 * 从表的配置生成
		 */
	foreach ($tablenameArr[1] as $key => $val) {
			// 每个子表
			foreach ($val as $k => $v1) {
				$iscreateDB = $controllPropertyList[$v1[$publicProperty['catalog']['name']]]['iscreate'];
				$iscreateConfifg = $controllPropertyList[$v1[$publicProperty['catalog']['name']]]['isconfig'];
				if($iscreateDB){
					$this->getChildrenTagsListincData($detailList , $v1 , $notshowcontroll , $v1[$publicProperty['fields']['name']], $shownumber , $shownumberCount,$k1);
					$k1++;
				}
			}
		}
		
		//array_unshift ($detailList,array('name' => 'id','showname' => '编号','shows' => '1','widths' => '','sorts' => '0','models' => '','sortname' => 'id','sortnum' => '1'));
		if($isneedcreateOrderNoConfig && !$detailList['orderno']){
			$detailList['orderno']=array(
			'name' => 'orderno',
			'showname' => '编号',
			'shows' => '1',
			'widths' => '',
			'sorts' => count($detailList),
			'models' => '',
			'sortname' => 'orderno',
			'sortnum' => count($detailList),
			'searchField' => $this->tableName.'.orderno',
			'conditions' => '',
			'type' => 'text',
			'issearch' => '1',
			'isallsearch' => '1',
			'searchsortnum' => '0',
			'transform'=>'0',
			'unique'=>'0',
			'validate'=>'0',
			'required'=>'0'
			);
		}
		if($special && !$detailList['name'] ){
			$detailList['name']=array(
					'name' => 'name',
					'showname' => '名称',
					'shows' => '1',
					'widths' => '',
					'sorts' => count($detailList),
					'models' => '',
					'sortname' => 'name',
					'sortnum' => count($detailList),
					'searchField' => $this->tableName.'.name',
					'conditions' => '',
					'type' => 'text',
					'issearch' => '1',
					'isallsearch' => '1',
					'searchsortnum' => '0',
					'transform'=>'0',
					'unique'=>'0',
					'validate'=>'0',
					'required'=>'0'
					);
		}
		if( $this->isaudit){
			$detailList['auditState']=array(
					'name' => 'auditState',
					'showname' => '审核状态',
					'shows' => '1',
					'rules'=>'1',
					'widths' => '',
					'sorts' => '0',
					'models' => '',
					'sortname' => 'auditState',
					'func' => array('0' => array('0' => 'getAuditState',),),
					'funcdata' => array('0' => array('0' => array('0' => '###','1' => '#id#','2' => '#ptmptid#',),),),
					'sortnum' =>(count($detailList)+1),'searchField'=>$v1['tablename'].".auditState",
					'transform'=>'0',
					'unique'=>'0',
					'validate'=>'0',
					'required'=>'0'
					);
			//array_push($detailList, array('name' => 'auditState','showname' => '审核状态','shows' => '1','widths' => '','sorts' => '0','models' => '','sortname' => 'auditState','func' => array('0' => array('0' => 'getAuditState',),),'funcdata' => array('0' => array('0' => array('0' => '###','1' => '#id#','2' => '#ptmptid#',),),),'sortnum' =>(count($detailList)+1)));
		}else{
			$detailList['action']=array(
					'name' => 'action',
					'showname' => '操作',
					'shows' => '0',
					'widths' => '',
					'sorts' => '0',
					'models' => '',
					'sortname' => 'status',
					'func' => array('0' => array('0' => 'showStatus',),),
					'funcdata' => array('0' => array('0' => array('0' => '#status#','1' => '#id#',),),),
					'sortnum' => (count($detailList)+1),
					'transform'=>'0',
					'unique'=>'0',
					'validate'=>'0',
					'required'=>'0'
					);
			//array_push ($detailList,array('name' => 'action','showname' => '操作','shows' => '1','widths' => '','sorts' => '0','models' => '','sortname' => 'status','func' => array('0' => array('0' => 'showStatus',),),'funcdata' => array('0' => array('0' => array('0' => '#status#','1' => '#id#',),),),'sortnum' => (count($detailList)+1)));
		}
		$detailList['operateid']=array(
				'name' => 'operateid',
				'showname' => '是否确认',
				'rules'=>'1',
				'shows' => '0',
				'widths' => '',
				'sorts' => '0',
				'models' => '',
				'func' => array('0' => array('0' => 'getSelectByName',),),
				'funcdata' => array('0' => array('0' => array('0' => 'operateidVal','1' => '###',),),),
				'sortname' => 'operateid',
				'sortnum' => (count($detailList)+1),
				'searchField'=>$v1['tablename'].".operateid",
				'transform'=>'0',
				'unique'=>'0',
				'validate'=>'0',
				'required'=>'0'
				);
		$detailList['companyid']=array(
				'name' => 'companyid', 
				'showname' => '公司',
				'shows' => '0',
				'widths' => '',
				'sorts' => '0',
				'models' => 'MisSystemCompany',
				'func' => array('0' => array('0' => 'getFieldBy',),),
				'funcdata' => array('0' => array('0' => array('0' => '###','1' => 'id','2' => 'name','3' => 'MisSystemCompany',),),),
				'sortname' => 'companyid',
				'sortnum' => '9',
				'issearch' => '1',
				'table' => 'mis_system_company',
				'sortnum' => (count($detailList)+1),
				'searchField'=>$v1['tablename'].".companyid",
				'transform'=>'1',
				'unique'=>'0',
				'validate'=>'0',
				'required'=>'0',
				'unfunc' => array('0' => array('0' => 'ungetFieldBy',),),
				'unfuncdata' => array('0' => array('0' => array('0' => '###','1' => 'id','2' => 'name','3' => 'MisSystemCompany',),),)
				);
		$detailList['projectid']=array(
				'name' => 'projectid',
				'showname' => '项目ID',
				'rules'=>'0',
				'shows' => '0',
				'widths' => '',
				'sorts' => '0',
				'models' => '',
				'sortname' => 'projectid','sortnum' => (count($detailList)+1),
				'searchField'=>$v1['tablename'].".projectid",
				'transform'=>'0',
				'unique'=>'0',
				'validate'=>'0',
				'required'=>'0'
				);
		//$detailList['projectworkid']=array('name' => 'projectworkid','showname' => '任务ID','rules'=>'0','shows' => '0','widths' => '','sorts' => '0','models' => '','sortname' => 'projectid','sortnum' => (count($detailList)+1),'searchField'=>$v1['tablename'].".projectworkid");
		$detailList['createid']=array(
				'name' => 'createid', 
				'rules' => '1',
				'showname' => '创建人',
				'shows' => '0',
				'widths' => '',
				'sorts' => '0',
				'models' => 'User',
				'func' => array('0' => array('0' => 'getFieldBy',),),
				'funcdata' => array('0' => array('0' => array('0' => '###','1' => 'id','2' => 'name','3' => 'User',),),),
				'sortname' => 'createid','issearch' => '1','table' => 'user',
				'sortnum' => (count($detailList)+1),'searchField'=>$v1['tablename'].".createid",
				'transform'=>'1',
				'unique'=>'0',
				'validate'=>'0',
				'required'=>'0',
				'unfunc' => array('0' => array('0' => 'ungetFieldBy',),),
				'unfuncdata' => array('0' => array('0' => array('0' => '###','1' => 'id','2' => 'name','3' => 'User',),),)
				);
		
		$detailList['createtime']= array(
				'name' => 'createtime',
				'showname' => '创建时间',
				'shows' => '1',
				'widths' => '',
				'sorts' => '0',
				'models' => '',
				'sortname' => 'createtime',
				'sortnum' => (count($detailList)+1),
				'fieldtype' => 'date',
				'fieldcategory' => 'date',
				'searchField' => $v1['tablename'].".createtime",
				'conditions' => '',
				'type' => 'time',
				'issearch' => '0',
				'isallsearch' => '0',
				'searchsortnum' => '1',
				'func' => array(
						'0' => array(
								'0' => 'transtime',
						),
				),
				'funcdata' => array(
						'0' => array(
								'0' => array(
										'0' => '###',
								),
						),
				),
				'helpvalue' => '',
				'transform'=>'1',
				'unique'=>'0',
				'validate'=>'0',
				'required'=>'0',
				'unfunc' => array('0' => array('0' => 'untranstime',),),
				'unfuncdata' => array(
						'0' => array(
								'0' => array(
										'0' => '###',
								),
						),
					),
				
			);
		
		
		/*	原版修改
		 $model->setDetail($nodename, $detailList);
		 //if( count($searchlist) ) $model->setDetail($nodename, $searchlist,"searchby");
		 if( count($toolbar) ) $model->setDetail($nodename, $toolbar,"toolbar");
		 */

		// 新版 带扩展的修改
		$model = D('Autoform');
		$dir = '/Models/';
		$listincpath = $dir.$this->nodeName.'/list.inc.php';
// 				var_dump($listincpath);
		$model->setPath($listincpath);
		$model->SetListinc($detailList , 'list.inc 配置文件');
// 		生成 list.inc扩展文件
		$listextendpath = $dir.$this->nodeName.'/listExtend.inc.php';
		$model->setPath($listextendpath);
		$model->SetListExtendinc(array() , 'list.inc配置文件--扩展部分');
		
		if( count($toolbar) ){
			// 生成权限配置文件
			$toolbarpath = $dir.$this->nodeName.'/toolbar.extension.inc.php';
			$model->setPath($toolbarpath);
			$model->SetToolBar($toolbar , '操作权限配置文件');
			// 生成 权限扩展文件
			$toolbarextendpath = $dir.$this->nodeName.'/toolbar.extensionExtend.inc.php';
			$model->setPath($toolbarextendpath);
			$model->SetToolBarExtend(array() , '操作权限配置文件--扩展部分');
		}

		/*****************************************************
		 * 	更新单号规则数据。
		 *****************************************************/
		if(!$special){
		    unset($aryRule);
			$modelNumber=D('SystemConfigNumber');
			$id = $this->tableName;
			$vo = $modelNumber->GetRules($id);
			if(!$vo){
				$aryRule= array(
								'rulename'=>'动态表单自动生成：'.$this->nodeTitle,
								// 'rule'=>substr(ucfirst($this->nodeName), 0,1).'yearmothdaynum',
								// 'year'=>date('Y',time()),
								// 'moth'=>date('m',time()),
								// 'day'=>date('d',time()),
								// 'num'=>5,
								// 'writable'=>0,
								// 'status'=>1,
								// 'isprocess'=>1,
								'modelname'=>ucfirst($this->nodeName),
								'table' =>  $this->tableName
				);
			}else{
					$aryRule = array(
									'rulename'=>'动态表单自动生成：'.$this->nodeTitle,
									// 'rule'=>substr(ucfirst($this->nodeName), 0,1).'yearmothdaynum',
									// 'year'=>date('Y',time()),
									// 'moth'=>date('m',time()),
									// 'day'=>date('d',time()),
									// 'num'=>5,
									// 'writable'=>0,
									// 'status'=>1,
									// 'isprocess'=>1,
									'modelname'=>ucfirst($this->nodeName),
									'table' =>  $this->tableName
					);
			}
			$modelNumber->SetRules($aryRule);
		}
		$_REQUEST['model']=$this->nodeName;
		logs('C:'.__CLASS__.' L:'.__LINE__.'生成内嵌表格forminc');
		//生成内嵌表格forminc
		$DynamicconfAction=A("Dynamicconf");
		$DynamicconfAction->setDynamiccof(true);
		logs('C:'.__CLASS__.' L:'.__LINE__.'生成内嵌表格forminc end  ');
	}

	/**
	 *  获取主表的字段配置信息
	 * @param array $detailList	最终结果数组
	 * @param array $v1	要处理的数据
	 * @param array $notshowcontroll	禁止用户勾取的列表
	 * @param int $k1	当前键值
	 * @param int $shownumber	最大列表显示数
	 * @param int $shownumberCount 已显示个数
	 * @param int $sort 排序数
	 */
	private function getTagsListincData(&$detailList ,$v1, $notshowcontroll ,$k1, $shownumber , &$shownumberCount,$sort){
		$str = htmlspecialchars($string);
		$controllProperty = $this->getProperty($v1['catalog']);
		if($v1[$controllProperty['searchlist']['name']]){ // 是否添加到搜索列表
			$searchlist[$v1[$controllProperty['fields']['name']]]["field"]=$v1[$controllProperty['searchlist']['name']];
			$searchlist[$v1[$controllProperty['fields']['name']]]["shows"]=$v1[$controllProperty['allsearchlist']['name']];
		}
		$detailList[$k1]['name']=$v1[$controllProperty['fields']['name']];
		$detailList[$k1]['showname']=$v1[$controllProperty['title']['name']];
		$detailList[$k1]['widths']=$this -> controlConfig[$v1['catalog']]['listwidth'];
		$detailList[$k1]['sorts']=0;
		$detailList[$k1]['models']='';
		$detailList[$k1]['sortname']=$v1[$controllProperty['fields']['name']];
		$detailList[$k1]['sortnum']=$sort;
		//字段控制
		$detailList[$k1]['shows']=1;
		$detailList[$k1]['status']=1;
		$detailList[$k1]['rules']=1;
		$detailList[$k1]['message']=1;
		$detailList[$k1]['isexport']=1;
		
		//添加新属性  链接跳转  2015-4-1 xiayuqin
		$detailList[$k1]['models']=$v1[$controllProperty['models']['name']];
		$detailList[$k1]['methods']=$v1[$controllProperty['methods']['name']];
		$detailList[$k1]['relation']=$v1[$controllProperty['relation']['name']];
		//添加新属性  链接跳转 end
		
		// 添加新属性@20141124 1027
		$detailList[$k1]['fieldtype']=$v1[$controllProperty['tabletype']['name']];  // 字段数据类型
		$detailList[$k1]['fieldcategory']=$v1[$controllProperty['catalog']['name']]; // 字段显示用组件类型

		$tableName = $v1['tablename'];
		$detailList[$k1]['searchField'] = $v1[$controllProperty['fields']['name']]?$tableName.'.'.$v1[$controllProperty['fields']['name']]:"";
		$detailList[$k1]['conditions'] = $v1[$controllProperty['conditions']['name']]?$v1[$controllProperty['conditions']['name']]:"";

		if($v1[$controllProperty['catalog']['name']] == 'date'){
			$detailList[$k1]['type'] = 'time';
		}else if($v1[$controllProperty['catalog']['name']]=="hiddens"||$v1[$controllProperty['catalog']['name']]=="lookup"||$v1[$controllProperty['catalog']['name']]=="lookupsuper"||$v1[$controllProperty['catalog']['name']]=="checkbox"||$v1[$controllProperty['catalog']['name']]=="radio"){
			$detailList[$k1]['type'] = 'text';
		}else{
			$detailList[$k1]['type'] = $v1[$controllProperty['catalog']['name']]?$v1[$controllProperty['catalog']['name']]:"";
		}
		/*list.inc必填   表单导入配置*/
		//检查类型
		$detailList[$k1]['validate']=$v1[$controllProperty['checkfunc']['name']];
		//唯一
		if('orderno'==$v1[$controllProperty['fields']['name']]){
			$detailList[$k1]['unique']=1;
		}else{
			$detailList[$k1]['unique']=0;
		}
		
		//必填
		$detailList[$k1]['required']=$v1[$controllProperty['requiredfield']['name']];
		$detailList[$k1]['transform']=0;
		switch ($v1[$controllProperty['catalog']['name']]){
			case 'text':
				$unit=$v1[$controllProperty['unitl']['name']];
				$unitls=$v1[$controllProperty['unitls']['name']];
				if($unit && $unitls){
					$fun=$fund=array();
					$fun[0][0]="unitExchange";
					$detailList[$k1]['func']=$fun;
					$fd=array("###");
						array_push($fd, $unit);// 存储单位
						array_push($fd, $unitls);// 显示单位
						array_push($fd, 3);// 转换类型
					$fund[0][0]=$fd; // 值
					
					$detailList[$k1]['funcdata']=$fund;
					//是否转换
					$detailList[$k1]['transform']=1;
					//反转函数
					$detailList[$k1]['unfunc']=$fun;
					$unfd=array("###");
					array_push($unfd, $unit);// 存储单位
					array_push($unfd, $unitls);// 显示单位
					array_push($unfd, 1);// 转换类型
					$unfund[0][0]=$unfd;
					$detailList[$k1]['unfuncdata']=$unfund;
				}
				//搜索配置
				$detailList[$k1]['issearch'] = $v1[$controllProperty['searchlist']['name']]?$v1[$controllProperty['searchlist']['name']]:1; //局部检索
				$detailList[$k1]['isallsearch'] = $v1[$controllProperty['allsearchlist']['name']]?$v1[$controllProperty['allsearchlist']['name']]:1;//全局检索
				$detailList[$k1]['searchsortnum'] = $sort;
				$detailList[$k1]['type']='text';
			break;
			case 'areainfo':
				//搜索配置
				$detailList[$k1]['issearch'] = $v1[$controllProperty['searchlist']['name']]?$v1[$controllProperty['searchlist']['name']]:1; //局部检索
				$detailList[$k1]['isallsearch'] = $v1[$controllProperty['allsearchlist']['name']]?$v1[$controllProperty['allsearchlist']['name']]:1;//全局检索
				$detailList[$k1]['searchsortnum'] = $sort;
				$detailList[$k1]['type']='text';
				break;
			case 'textarea':
					$isrichbox=$v1[$controllProperty['isrichbox']['name']];
					if($isrichbox){
						$fun=$fund=array();
						$fun[0][0]="richtext2str";
						$detailList[$k1]['func']=$fun;
						$fd=array("###");
						$fund[0][0]=$fd; // 值
						$detailList[$k1]['funcdata']=$fund;
						//是否转换
						$detailList[$k1]['transform']=1;
						//反转函数
						$unfun[0][0]="unrichtext2str";
						$detailList[$k1]['unfunc']=$unfun;
						$unfund[0][0]=$fd;
						$detailList[$k1]['unfuncdata']=$unfund;
					}
					//搜索配置
					$detailList[$k1]['issearch'] = $v1[$controllProperty['searchlist']['name']]?$v1[$controllProperty['searchlist']['name']]:0; //局部检索
					$detailList[$k1]['isallsearch'] = $v1[$controllProperty['allsearchlist']['name']]?$v1[$controllProperty['allsearchlist']['name']]:0;//全局检索
					$detailList[$k1]['searchsortnum'] = $sort;
					$detailList[$k1]['type']='textarea';
				break;
			case 'password':
					$fun=$fund=array();
					$fun[0][0]="passwordChange";
					$detailList[$k1]['func']=$fun;
					$fd=array("###");
					$fund[0][0]=$fd; // 值
					$detailList[$k1]['funcdata']=$fund;
					//是否转换
					$detailList[$k1]['transform']=0;
					//搜索配置
					$detailList[$k1]['issearch'] = $v1[$controllProperty['searchlist']['name']]?$v1[$controllProperty['searchlist']['name']]:0; //局部检索
					$detailList[$k1]['isallsearch'] = $v1[$controllProperty['allsearchlist']['name']]?$v1[$controllProperty['allsearchlist']['name']]:0;//全局检索
					$detailList[$k1]['searchsortnum'] = $sort;
					$detailList[$k1]['type']='text';
				break;		
			case 'date':
				$fun=$fund=array();
				$fun[0][0]="transTime";
				$detailList[$k1]['func']=$fun;
				$format = $v1[$controllProperty['format']['name']]?$v1[$controllProperty['format']['name']]:$controllProperty['format']['default'];
				$format2 = explode("@",$format);				
				$fd=array("###",$format2[1]);
				$fund[0][0]=$fd;
				$detailList[$k1]['funcdata']=$fund;
				//搜索配置
				$detailList[$k1]['issearch'] = $v1[$controllProperty['searchlist']['name']]?$v1[$controllProperty['searchlist']['name']]:1; //局部检索
				$detailList[$k1]['isallsearch'] = $v1[$controllProperty['allsearchlist']['name']]?$v1[$controllProperty['allsearchlist']['name']]:0;//全局检索
				$detailList[$k1]['searchsortnum'] = $sort;
				$detailList[$k1]['type']='time';
				//是否转换
				$detailList[$k1]['transform']=1;
				//反转函数
				$unfun[0][0]="untransTime";
				$detailList[$k1]['unfunc']=$unfun;
				$unfund[0][0]=$fd;
				$detailList[$k1]['unfuncdata']=$unfund;
				break;
			case 'select':
			case 'radio':
				if($v1[$controllProperty['subimporttableobj']['name']]){
					$fun=$fund=array();
					$fun[0][0]="getFieldBy";
					$detailList[$k1]['func']=$fun;
					$fd=array("###",$v1[$controllProperty['subimporttablefield2obj']['name']],$v1[$controllProperty['subimporttablefieldobj']['name']],$v1[$controllProperty['subimporttableobj']['name']]);
					$fund[0][0]=$fd;
					$detailList[$k1]['funcdata']=$fund;
					$detailList[$k1]['table'] = $v1[$controllProperty['subimporttableobj']['name']]?$v1[$controllProperty['subimporttableobj']['name']]:"";
					$detailList[$k1]['field'] = $v1[$controllProperty['subimporttablefield2obj']['name']]?$v1[$controllProperty['subimporttablefield2obj']['name']]:"";
					//是否转换
					$detailList[$k1]['transform']=1;
					//反转函数
					$unfun[0][0]="ungetFieldBy";
					$detailList[$k1]['unfunc']=$unfun;
					$unfund[0][0]=$fd;
					$detailList[$k1]['unfuncdata']=$unfund;
				}else if($v1[$controllProperty['treedtable']['name']]){
					$fun=$fund=array();
					$fun[0][0]="getFieldBy";
					$detailList[$k1]['func']=$fun;
					$fd=array("###",$v1[$controllProperty['treevaluefield']['name']],$v1[$controllProperty['treeshowfield']['name']],$v1[$controllProperty['treedtable']['name']]);
					$fund[0][0]=$fd;
					$detailList[$k1]['funcdata']=$fund;
					$detailList[$k1]['table'] = $v1[$controllProperty['treedtable']['name']]?$v1[$controllProperty['treedtable']['name']]:"";
					$detailList[$k1]['field'] = $v1[$controllProperty['treevaluefield']['name']]?$v1[$controllProperty['treevaluefield']['name']]:"";
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
					$fds=implode(",",explode(";",$v1[$controllProperty['showoption']['name']]));
					$fd=array("###",$fds);
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
				//搜索配置
				$detailList[$k1]['issearch'] = $v1[$controllProperty['searchlist']['name']]?$v1[$controllProperty['searchlist']['name']]:1; //局部检索
				$detailList[$k1]['isallsearch'] = $v1[$controllProperty['allsearchlist']['name']]?$v1[$controllProperty['allsearchlist']['name']]:0;//全局检索
				$detailList[$k1]['searchsortnum'] = $sort;
				if( ! empty($v1["showoption"])){
					$detailList[$k1]['type'] = 'select|'.$v1["showoption"];
				}elseif( ! empty($v1["subimporttableobj"]) || $v1[$controllProperty['treedtable']['name']]){
					$detailList[$k1]['type'] = 'group';
				}else{
					$detailList[$k1]['type'] = 'select';
				}
				break;
			case 'checkbox':
				if($v1[$controllProperty['subimporttableobj']['name']]){
					$fun=$fund=array();
					$fun[0][0]="excelTplidTonameAppend";
					$detailList[$k1]['func']=$fun;
					$fd=array("###",$v1[$controllProperty['subimporttablefield2obj']['name']],$v1[$controllProperty['subimporttablefieldobj']['name']],$v1[$controllProperty['subimporttableobj']['name']]);
					$fund[0][0]=$fd;
					$detailList[$k1]['funcdata']=$fund;
					$detailList[$k1]['table'] = $v1[$controllProperty['subimporttableobj']['name']]?$v1[$controllProperty['subimporttableobj']['name']]:"";
					$detailList[$k1]['field'] = $v1[$controllProperty['subimporttablefield2obj']['name']]?$v1[$controllProperty['subimporttablefield2obj']['name']]:"";
					//是否转换
					$detailList[$k1]['transform']=1;
					//反转函数
					$unfun[0][0]="unexcelTplidTonameAppend";
					$detailList[$k1]['unfunc']=$unfun;
					$unfund[0][0]=$fd;
					$detailList[$k1]['unfuncdata']=$unfund;
				}else{
					$fun=$fund=array();
					$fun[0][0]="getSelectlistValue";
					$detailList[$k1]['func']=$fun;
					$fds=implode(",",explode(";",$v1[$controllProperty['showoption']['name']]));
					//print_r($v1);
					$fd=array("###",$fds,"checkbox");
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
				//搜索配置
				$detailList[$k1]['issearch'] = $v1[$controllProperty['searchlist']['name']]?$v1[$controllProperty['searchlist']['name']]:0; //局部检索
				$detailList[$k1]['isallsearch'] = $v1[$controllProperty['allsearchlist']['name']]?$v1[$controllProperty['allsearchlist']['name']]:0;//全局检索
				$detailList[$k1]['searchsortnum'] = $sort;
				$detailList[$k1]['type']='checkbox';
				if( ! empty($v1["showoption"])){
					$detailList[$k1]['type'] = 'checkbox|'.$v1["showoption"];
				}
				break;
			case 'lookup':
			case 'lookupsuper':
				//搜索表名  考虑视图情况查显示字段对应表（只适用于无连接的sql语句，带left join 等的不适用）
				$lookupobj = D("LookupObj");
				$lookuplist = $lookupobj->GetLookupDetail($v1[$controllProperty['lookupchoice']['name']]);
				if($lookuplist['viewname']){
					$viewmodel = D("MisSystemDataviewMasView");
					$vmap['mis_system_dataview_sub.status'] = 1;
					$vmap['name'] = $lookuplist['viewname'];
					$vmap['otherfield'] = $lookuplist['filed'];
					//$vmap['_string'] = "isshow is not null and isshow !='' and isshow !='0'";
					$vlist = $viewmodel->where($vmap)->find();
					$vlistsql = strtolower($vlist['replacesql']);
					if(strpos($vlistsql,' join ')>0){
						$relationtable = D($v1[$controllProperty['model']['name']])->getTableName();
					}else{
						$relationtable = $vlist['tablename'];
					}
				}else{
					$relationtable = D($v1[$controllProperty['model']['name']])->getTableName(); //xyz-2015-07-15
				}
				
				$fun=$fund=array();
				$fun[0][0]="getFieldBy";
				$detailList[$k1]['func']=$fun;
				// id : $v1[$controllProperty['lookuporgval']['name']] 
				//没有配置显示值或带回值时默认id name  update renl 2014-12-31 11:36
				$lookuporgval=$v1[$controllProperty['lookuporgval']['name']]?$v1[$controllProperty['lookuporgval']['name']]:"id";
				$lookuporg=$v1[$controllProperty['lookuporg']['name']]?$v1[$controllProperty['lookuporg']['name']]:"name";
				
				//$fd=array("###",$lookuporgval,$lookuporg,$v1[$controllProperty['model']['name']]);
				$fd=array("###",$lookuporgval,$lookuporg,$relationtable); //xyz-2015-07-15
				$fund[0][0]=$fd;
				$detailList[$k1]['funcdata']=$fund;
				//$detailList[$k1]['table'] = $v1[$controllProperty['model']['name']]?$v1[$controllProperty['model']['name']]:"";
				//获取表名而不是模型名称 //xyz-2015-07-15
				$detailList[$k1]['table'] = $v1[$controllProperty['model']['name']]?$relationtable:"";
				//$detailList[$k1]['field'] = $v1[$controllProperty['lookuporg']['name']]?$v1[$controllProperty['lookuporg']['name']]:"id";	
				//field应该为存储字段而不是现实字段  //xyz-2015-07-15
				$detailList[$k1]['field'] = $v1[$controllProperty['lookuporgval']['name']]?$v1[$controllProperty['lookuporgval']['name']]:"id";
				
				//搜索配置
				$detailList[$k1]['issearch'] = $v1[$controllProperty['searchlist']['name']]?$v1[$controllProperty['searchlist']['name']]:1; //局部检索
				$detailList[$k1]['isallsearch'] = $v1[$controllProperty['allsearchlist']['name']]?$v1[$controllProperty['allsearchlist']['name']]:1;//全局检索
				$detailList[$k1]['searchsortnum'] = $sort;
				//$detailList[$k1]['type']='text'; xyz-2015-07-15
				$detailList[$k1]['type']='db|'.$v1[$controllProperty['lookuporg']['name']];//xyz-2015-07-15
				//是否转换
				$detailList[$k1]['transform']=1;
				//反转函数
				$unfun[0][0]="ungetFieldBy";
				$detailList[$k1]['unfunc']=$unfun;
				$unfund[0][0]=$fd;
				$detailList[$k1]['unfuncdata']=$unfund;
				break;
		}
		if(in_array($v1[$controllProperty['catalog']['name']], $notshowcontroll)){
			// 数据1对多类型 设置为不显示，用户不可设置为修改
			$detailList[$k1]['shows']=0;
			$detailList[$k1]['ischosice']=1;
		}else{
			if($shownumberCount >0 &&  $shownumberCount <= $shownumber){
				$detailList[$k1]['shows']=1;
				$shownumberCount ++;
			}else{
				$detailList[$k1]['shows']=0;
				$shownumberCount =0;
			}
		}
		//return $detailList;
	}

	/**
	 *  获取从表的字段配置信息
	 * @param array $detailList	最终结果数组
	 * @param array $v1	要处理的数据
	 * @param array $notshowcontroll	禁止用户勾取的列表
	 * @param int $k1	当前键值
	 * @param int $shownumber	最大列表显示数
	 * @param int $shownumberCount 已显示个数
	 * @param int $sort 排序数
	 */
	private function getChildrenTagsListincData(&$detailList ,$v1, $notshowcontroll ,$k1, $shownumber , &$shownumberCount,$sort){
		$controllProperty = $this->getProperty($v1['catalog']);
		if($v1[$controllProperty['searchlist']['name']]){ // 是否添加到搜索列表
			$searchlist[$v1[$controllProperty['fields']['name']]]["field"]=$v1[$controllProperty['searchlist']['name']];
			$searchlist[$v1[$controllProperty['fields']['name']]]["shows"]=$v1[$controllProperty['allsearchlist']['name']];
		}
		$detailList[$k1]['name']=$v1[$controllProperty['fields']['name']];
		$detailList[$k1]['showname']=$v1[$controllProperty['title']['name']];
		$detailList[$k1]['widths']=$this -> controlConfig[$v1['catalog']]['listwidth'];
		$detailList[$k1]['sorts']=0;
		$detailList[$k1]['models']='';
		$detailList[$k1]['sortname']=$v1[$controllProperty['fields']['name']];
		$detailList[$k1]['sortnum']=$sort+1;
		//字段控制
		$detailList[$k1]['shows']=1;
		$detailList[$k1]['status']=1;
		$detailList[$k1]['rules']=1;
		$detailList[$k1]['message']=1;
		$detailList[$k1]['isexport']=1;

		//添加新属性  链接跳转  2015-4-1 xiayuqin
		$detailList[$k1]['models']=$v1[$controllProperty['models']['name']];
		$detailList[$k1]['methods']=$v1[$controllProperty['methods']['name']];
		$detailList[$k1]['relation']=$v1[$controllProperty['relation']['name']];
		//添加新属性  链接跳转 end
		
		// 添加新属性@20141124 1027
		$detailList[$k1]['fieldtype']=$v1[$controllProperty['tabletype']['name']];  // 字段数据类型
		$detailList[$k1]['fieldcategory']=$v1[$controllProperty['catalog']['name']]; // 字段显示用组件类型

		$tableName = $v1['tablename'];
		$detailList[$k1]['searchField'] = $v1[$controllProperty['fields']['name']]?$tableName.'.'.$v1[$controllProperty['fields']['name']]:"";
		$detailList[$k1]['conditions'] = $v1[$controllProperty['conditions']['name']]?$v1[$controllProperty['conditions']['name']]:"";

		if($v1[$controllProperty['catalog']['name']] == 'date'){
			$detailList[$k1]['type'] = 'time';
		}else{
			$detailList[$k1]['type'] = $v1[$controllProperty['catalog']['name']]?$v1[$controllProperty['catalog']['name']]:"";
		}
		/*list.inc必填   表单导入配置*/
		//检查类型
		$detailList[$k1]['validate']=$v1[$controllProperty['checkfunc']['name']];
		//唯一
		if('orderno'==$v1[$controllProperty['fields']['name']]){
			$detailList[$k1]['unique']=1;
		}else{
			$detailList[$k1]['unique']=0;
		}
		
		//必填
		$detailList[$k1]['required']=$v1[$controllProperty['requiredfield']['name']];
		$detailList[$k1]['transform']=0;
		switch ($v1[$controllProperty['catalog']['name']]){
			/*case 'text':
				$unit=$v1[$controllProperty['unitl']['name']];
				$unitls=$v1[$controllProperty['unitls']['name']];
				if($unit && $unitls){
					$fun=$fund=array();
					$fun[0][0]="unitExchange";
					$detailList[$k1]['func']=$fun;
					$fd=array("###");
					array_push($fd, $unit);// 存储单位
					array_push($fd, $unitls);// 显示单位
					array_push($fd, 3);// 转换类型
					$fund[0][0]=$fd; // 值
			
					$detailList[$k1]['funcdata']=$fund;
					
					//搜索配置
					$detailList[$k1]['issearch'] = $v1[$controllProperty['searchlist']['name']]?$v1[$controllProperty['searchlist']['name']]:1; //局部检索
					$detailList[$k1]['isallsearch'] = $v1[$controllProperty['allsearchlist']['name']]?$v1[$controllProperty['allsearchlist']['name']]:1;//全局检索
					$detailList[$k1]['searchsortnum'] = $sort;
					$detailList[$k1]['type']='text';
				}
				break;		*/	
			case 'date':
				$fun=$fund=array();
				$fun[0][0]="transTime";
				$detailList[$k1]['func']=$fun;
				$fd=array("###");
				$fund[0][0]=$fd;
				$detailList[$k1]['funcdata']=$fund;
				//搜索配置
				$detailList[$k1]['issearch'] = $v1[$controllProperty['searchlist']['name']]?$v1[$controllProperty['searchlist']['name']]:1; //局部检索
				$detailList[$k1]['isallsearch'] = $v1[$controllProperty['allsearchlist']['name']]?$v1[$controllProperty['allsearchlist']['name']]:0;//全局检索
				$detailList[$k1]['searchsortnum'] = $sort;
				$detailList[$k1]['type']='time';
				//是否转换
				$detailList[$k1]['transform']=1;
				//反转函数
				$unfun[0][0]="untransTime";
				$detailList[$k1]['unfunc']=$unfun;
				$unfund[0][0]=$fd;
				$detailList[$k1]['unfuncdata']=$unfund;
				
				break;
			case 'text':
				if($v1[$controllProperty['checkfortable']['name']]){
					// 文本框的checkfor 设置
					$fun=$fund=array();
					$fun[0][0]="getFieldBy";
					$fun[0][1]="getFieldBy";
					$detailList[$k1]['func']=$fun;
					$fd=array("#id#",'masid',$v1[$controllProperty['fields']['name']],$tableName);
					$fd1=array("###",$v1[$controllProperty['checkforbindd']['name']],$v1[$controllProperty['checkforshow']['name']],$v1[$controllProperty['checkfortable']['name']]);
					$fund[0][0]=$fd;
					$fund[0][1]=$fd1;
					$detailList[$k1]['funcdata']=$fund;
					$detailList[$k1]['table'] = $v1[$controllProperty['checkfortable']['name']]?$v1[$controllProperty['checkfortable']['name']]:"";
					$detailList[$k1]['field'] = $v1[$controllProperty['checkforshow']['name']]?$v1[$controllProperty['checkforshow']['name']]:"";
					//是否转换
					$detailList[$k1]['transform']=1;
					//反转函数
					$unfun[0][0]="ungetFieldBy";
					$detailList[$k1]['unfunc']=$unfun;
					$unfund[0][0]=$fd1;
					$detailList[$k1]['unfuncdata']=$unfund;
				}else{
					// 文本框的checkfor 设置
					$fun=$fund=array();
					$fun[0][0]="getFieldBy";
					$detailList[$k1]['func']=$fun;
					$fd=array("#id#",'masid',$v1[$controllProperty['fields']['name']],$tableName);
					$fund[0][0]=$fd;
					$detailList[$k1]['funcdata']=$fund;
					$detailList[$k1]['table'] = $v1[$controllProperty['checkfortable']['name']]?$v1[$controllProperty['checkfortable']['name']]:"";
					$detailList[$k1]['field'] = $v1[$controllProperty['checkforshow']['name']]?$v1[$controllProperty['checkforshow']['name']]:"";
					//是否转换
					$detailList[$k1]['transform']=1;
					//反转函数
					$unfun[0][0]="ungetFieldBy";
					$detailList[$k1]['unfunc']=$unfun;
					$unfund[0][0]=$fd;
					$detailList[$k1]['unfuncdata']=$unfund;
				}
				break;
			case 'textarea':
				$isrichbox=$v1[$controllProperty['isrichbox']['name']];
				if($isrichbox){
					$fun=$fund=array();
					$fun[0][0]="richtext2str";
					$detailList[$k1]['func']=$fun;
					$fd=array("###");
					$fund[0][0]=$fd; // 值
					$detailList[$k1]['funcdata']=$fund;
					
					//是否转换
					$detailList[$k1]['transform']=1;
					//反转函数
					$unfun[0][0]="unrichtext2str";
					$detailList[$k1]['unfunc']=$unfun;
					$unfund[0][0]=$fd;
					$detailList[$k1]['unfuncdata']=$unfund;
				}
				//搜索配置
				$detailList[$k1]['issearch'] = $v1[$controllProperty['searchlist']['name']]?$v1[$controllProperty['searchlist']['name']]:0; //局部检索
				$detailList[$k1]['isallsearch'] = $v1[$controllProperty['allsearchlist']['name']]?$v1[$controllProperty['allsearchlist']['name']]:0;//全局检索
				$detailList[$k1]['searchsortnum'] = $sort;
				$detailList[$k1]['type']='textarea';
				
				break;
				case 'password':
				
					$fun=$fund=array();
					$fun[0][0]="getFieldBy";
					$fun[0][1]="passwordChange";
					$detailList[$k1]['func']=$fun;
					$fd=array("#id#",'masid',$v1[$controllProperty['fields']['name']],$tableName);
					$fd1=array('###');
					$fund[0][0]=$fd;
					$fund[0][1]=$fd1;
					$detailList[$k1]['funcdata']=$fund;
					//是否转换
					$detailList[$k1]['transform']=0;
					//反转函数
					
					break;
			case 'select':
			case 'radio':
				if($v1[$controllProperty['subimporttableobj']['name']]){
					$fun=$fund=array();
					$fun[0][0]="getFieldBy";
					$fun[0][1]="getFieldBy";
					$detailList[$k1]['func']=$fun;
					$fd=array("#id#",'masid',$v1[$controllProperty['fields']['name']],$tableName);
					$fd1=array("###",$v1[$controllProperty['subimporttablefield2obj']['name']],$v1[$controllProperty['subimporttablefieldobj']['name']],$v1[$controllProperty['subimporttableobj']['name']]);
					$fund[0][0]=$fd;
					$fund[0][1]=$fd1;
					$detailList[$k1]['funcdata']=$fund;
					$detailList[$k1]['table'] = $v1[$controllProperty['subimporttableobj']['name']]?$v1[$controllProperty['subimporttableobj']['name']]:"";
					$detailList[$k1]['field'] = $v1[$controllProperty['subimporttablefield2obj']['name']]?$v1[$controllProperty['subimporttablefield2obj']['name']]:"";
					//是否转换
					$detailList[$k1]['transform']=1;
					//反转函数
					$unfun[0][0]="ungetFieldBy";
					$detailList[$k1]['unfunc']=$unfun;
					$unfund[0][0]=$fd1;
					$detailList[$k1]['unfuncdata']=$unfund;
				}else{
					$fun=$fund=array();
					$fun[0][0]="getFieldBy";
					$fun[0][1]="getSelectlistValue";
					$detailList[$k1]['func']=$fun;
					$fds=implode(",",explode(";",$v1[$controllProperty['showoption']['name']]));
					$fd=array("#id#",'masid',$v1[$controllProperty['fields']['name']],$tableName);
					$fd1=array("###",$fds);
					$fund[0][0]=$fd;
					$fund[0][1]=$fd1;
					$detailList[$k1]['funcdata']=$fund;
					//是否转换
					$detailList[$k1]['transform']=1;
					//反转函数
					$unfun[0][0]="ungetSelectlistValue";
					$detailList[$k1]['unfunc']=$unfun;
					$unfund[0][0]=$fd1;
					$detailList[$k1]['unfuncdata']=$unfund;
				}
				//搜索配置
				$detailList[$k1]['issearch'] = $v1[$controllProperty['searchlist']['name']]?$v1[$controllProperty['searchlist']['name']]:1; //局部检索
				$detailList[$k1]['isallsearch'] = $v1[$controllProperty['allsearchlist']['name']]?$v1[$controllProperty['allsearchlist']['name']]:0;//全局检索
				$detailList[$k1]['searchsortnum'] = $sort;
				if( ! empty($v1["showoption"])){
					$detailList[$k1]['type'] = 'select|'.$v1["showoption"];
				}elseif( ! empty($v1["subimporttableobj"])){
					$detailList[$k1]['type'] = 'group';
				}else{
					$detailList[$k1]['type'] = 'select';
				}
			case 'checkbox':
				if($v1[$controllProperty['subimporttableobj']['name']]){
					$fun=$fund=array();
					$fun[0][0]="getFieldBy";
					$fun[0][1]="excelTplidTonameAppend";
					$detailList[$k1]['func']=$fun;
					$fd=array("#id#",'masid',$v1[$controllProperty['fields']['name']],$tableName);
					$fd1=array("###",$v1[$controllProperty['subimporttablefield2obj']['name']],$v1[$controllProperty['subimporttablefieldobj']['name']],$v1[$controllProperty['subimporttableobj']['name']]);
					$fund[0][0]=$fd;
					$fund[0][1]=$fd1;
					$detailList[$k1]['funcdata']=$fund;
					$detailList[$k1]['table'] = $v1[$controllProperty['subimporttableobj']['name']]?$v1[$controllProperty['subimporttableobj']['name']]:"";
					$detailList[$k1]['field'] = $v1[$controllProperty['subimporttablefield2obj']['name']]?$v1[$controllProperty['subimporttablefield2obj']['name']]:"";
					$detailList[$k1]['funcdata']=$fund;
					//是否转换
					$detailList[$k1]['transform']=1;
					//反转函数
					$unfun[0][0]="unexcelTplidTonameAppend";
					$detailList[$k1]['unfunc']=$unfun;
					$unfund[0][0]=$fd1;
					$detailList[$k1]['unfuncdata']=$unfund;
				}else{
					$fun=$fund=array();
					$fun[0][0]="getFieldBy";
					$fun[0][1]="getSelectlistValue";
					$detailList[$k1]['func']=$fun;
					$fds=implode(",",explode(";",$v1[$controllProperty['showoption']['name']]));
					$fd=array("#id#",'masid',$v1[$controllProperty['fields']['name']],$tableName);
					$fd1=array("###",$fds,"checkbox");
					$fund[0][0]=$fd;
					$fund[0][1]=$fd1;
					$detailList[$k1]['funcdata']=$fund;
					//是否转换
					$detailList[$k1]['transform']=1;
					//反转函数
					$unfun[0][0]="ungetSelectlistValue";
					$detailList[$k1]['unfunc']=$unfun;
					$unfund[0][0]=$fd1;
					$detailList[$k1]['unfuncdata']=$unfund;
				}
				//搜索配置
				$detailList[$k1]['issearch'] = $v1[$controllProperty['searchlist']['name']]?$v1[$controllProperty['searchlist']['name']]:0; //局部检索
				$detailList[$k1]['isallsearch'] = $v1[$controllProperty['allsearchlist']['name']]?$v1[$controllProperty['allsearchlist']['name']]:0;//全局检索
				$detailList[$k1]['searchsortnum'] = $sort;
				$detailList[$k1]['type']='checkbox';
				break;
			case 'lookup':
			case 'lookupsuper':
				/***************** modify by nbmxkj at 20150127 1805**************/
                /**********************         子表lookup配置          **************/
                $fun = $fund = array();
                $fun[0][0] = "getFieldBy";
                $fun[0][1] = "getFieldBy";
                $detailList[$k1]['func'] = $fun;

                // id : $v1[$controllProperty['lookuporgval']['name']]
                //没有配置显示值或带回值时默认id name  update renl 2014-12-31 11:36
                $lookuporgval = $v1[$controllProperty['lookuporgval']['name']] ? $v1[$controllProperty['lookuporgval']['name']] : "id";
                $lookuporg = $v1[$controllProperty['lookuporg']['name']] ? $v1[$controllProperty['lookuporg']['name']] : "name";
               
                $fd = array("#id#", 'masid', $v1[$controllProperty['fields']['name']], $tableName);
                $fd1= array("###", $lookuporgval, $lookuporg, $v1[$controllProperty['model']['name']]);
                
                $fund[0][0] = $fd;
                $fund[0][1] = $fd1;
                $detailList[$k1]['funcdata'] = $fund;
                 $detailList[$k1]['table'] = $v1[$controllProperty['model']['name']] ? $v1[$controllProperty['model']['name']] : "";
                $detailList[$k1]['field'] = $v1[$controllProperty['lookuporg']['name']] ? $v1[$controllProperty['lookuporg']['name']] : "id";
                //搜索配置
                $detailList[$k1]['issearch'] = $v1[$controllProperty['searchlist']['name']] ? $v1[$controllProperty['searchlist']['name']] : 0;
                //局部检索
                $detailList[$k1]['isallsearch'] = $v1[$controllProperty['allsearchlist']['name']] ? $v1[$controllProperty['allsearchlist']['name']] : 0;
                //全局检索
                $detailList[$k1]['searchsortnum'] = $sort;
                $detailList[$k1]['type'] = 'group2';
                
                //是否转换
                $detailList[$k1]['transform']=1;
                //反转函数
                $unfun[0][0]="ungetFieldBy";
                $detailList[$k1]['unfunc']=$unfun;
                $unfund[0][0]=$fd1;
                $detailList[$k1]['unfuncdata']=$unfund;
                /***************** modify by nbmxkj at 20150127 1805 end **************/
				break;
		}
		if(in_array($v1[$controllProperty['catalog']['name']], $notshowcontroll)){
			// 数据1对多类型 设置为不显示，用户不可设置为修改
			$detailList[$k1]['shows']=0;
			$detailList[$k1]['ischosice']=1;
		}else{
			if($shownumberCount >0 &&  $shownumberCount <= $shownumber){
				$detailList[$k1]['shows']=1;
				$shownumberCount ++;
			}else{
				$detailList[$k1]['shows']=0;
				$shownumberCount =0;
			}
		}
		
		//return $detailList;
	}
	/**
	 * 生成组件详细（编辑页面使用）
	 * @param array $data 数据库数据
	 */
	private function createContrlHtml($configfield){
		// 获取所有节点
		//$selectlist = $this->getAutoFormConfig($this->curnode);
		/*if(file_exists($configPath)){
			//	$allControlConfig = require $configPath;
			$selectlist = require $this->getAutoFormConfig($this->curnode);
			//			$selectlist = $aryRule;
			}*/
		// 读取物理文件 方法备份
		//$selectlist = $this->getAutoFormConfig($this->curnode);
		$html='';
		$data = $this->controlConfig;
		$propConfig = $this->publicProperty;
		foreach ($configfield as $k=>$v){
			$hidden =  $this->createHidden($k, $v);
			$title = $v['title'];
			$checkorder = "checkorder=\"{$v['fields']}\"";
			$curTagHtml = htmlspecialchars($data[$v['catalog']]['html']); // 当前组件html结构
			// onclick="delControll(this)"  原版的组件删除事件 @nbmxkj 2015 0407 15
			$isforeignfield = $v[$propConfig['isforeignfield']['name']];
			$isforeignfieldProperty = '';
			$copycontrollcls = '';
			if($isforeignfield){
				$isforeignfieldProperty = 'isforeignfield="1"';
				$copycontrollcls = ' icon-copy copy_style';
			}
			$delTag='<a  class="nnbm_delete_plain_ctl icon-remove" '.$isforeignfieldProperty.' style="display: none;"></a>';
			//eval("\$curTagHtml = \"$curTagHtml\";");
			$statusHtml='';
			// 不可编辑
			$islock = $v[$data[$v['catalog']]['property']['islock']['name']];
			// 不可显示
			$isshow = $v['isshow'];
			// 必填
			$isrequired = $v[$data[$v['catalog']]['property']['requiredfield']['name']];
			
			$statusClsTag = '';
			if($isrequired){
			    // 必填
			    $statusHtml .='<span class="icon-asterisk" style="color:red" title="必填"></span>';
// 			   $statusHtml .=' <span class="icon-stack">
//                       <i class="icon-camera"></i>
//                       <i class="icon-ban-circle icon-stack-base text-error"></i>
//                     </span>';
			    $statusClsTag[]='required';
			}
			if(!$islock){
				// 不可编辑
				$statusHtml .='<span class="icon-lock" title="不可编辑"></span>';
				$statusClsTag[]='locked';
			}
			if(!$isshow){
				// 不显示
				$statusHtml .='<span class="icon-eye-close" title="不显示"></span>';
				$statusClsTag[]='removed';
			}
			
			eval("\$curTagHtml = \"$curTagHtml\";");
			$titlePercent = $data[$v['catalog']]['property']['titlepercent']['name'];
			$contentPercent = $data[$v['catalog']]['property']['contentpercent']['name'];
			$controllCLS = 'col_'.$v[$titlePercent].'_'.$v[$contentPercent].' form_group_lay';
			// onclick="setProp(this)"  原版的属性编辑事件 @nbmxkj 2015 0407 15
			$html .= '<li class="nbmmove '.$controllCLS.' '.join(' ', $statusClsTag).'" data="'.$v['catalog'].'"  checkorder="'.$v['fields'].'" names="'.$v['fields'].'">'.htmlspecialchars_decode($curTagHtml).'</li>';
			$i++;
		}
		return $html;
	}

	/**
	 * @Title: createHidden
	 * @Description: todo(生成属性隐藏域)
	 * @param string $category 组件类型
	 * @param array $data 属性数据
	 * @return string
	 * @author quqiang
	 * @date 2014-8-21 上午11:32:40
	 * @throws
	 */
	function createHidden($category , $data){
		$hidden = '';
		// 公用发展
		/* 已合并公有属性到私有属性中 foreach ($this->publicProperty as $k=>$v){
			$hidden .= "<input type=\"hidden\" name=\"".$v['name']."[{$category}]\" value=\"".htmlspecialchars($data[$v['name']])."\" />";
			}*/
		// 私有属性
		foreach ($this->privateProperty[$data['catalog']] as $k=>$v){
			$hidden .= "<input type=\"hidden\" name=\"".$v['name']."[{$category}]\" value=\"".htmlspecialchars($data[$v['name']])."\" />";
		}
		return $hidden;
	}

	/**
	 * 写入公用信息到数据库
	 * @param array $filedData 组件属性列表
	 */
	private function insertFormManager($filedData){
		$data = array();
		$data['actionname'] = $this->nodeName;
		$data['actiontitle'] = $this->nodeTitle;
		$data['isaudit'] = $_POST['isaudit'];
		$data['inserttable'] = $_POST['inserttable'];

		$model = D('MisDynamicFormManage');

		if(C('TOKEN_NAME')) $data[C('TOKEN_NAME')]= $_POST[C('TOKEN_NAME')];

		/* 自带唯一验证
		 if(false == $model->create($data)){
		 $this->error($model->getError());
		 }
		 $ret = $model->add();
		 */

		$count = $model->where("status=1 and  actionname='{$this->nodeName}'")->count();
		if($count){
			$this->error('当即前Action名称已存在 ');
		}
		$data = $model->create($data);
		// 添加表单信息
		$ret = $model->add($data);
		if(!$ret){
			$this->error($model->getDbError().' '.$model->getError());
		}
		if($_POST['insertnode']){
			$this->addNode($ret);
		}
		//添加表单组件信息
		$filedModel = M('MisDynamicFormField');
		foreach ($filedData['public'] as $key=>$val){
			$val['formid'] = $ret;
			$filedModel->add($val);
		}

		$this->success('添加成功');
	}

	/**
	 * 写入公用信息到数据库
	 * @param array $filedData
	 */
	private function modifyFormManager($filedData){
		//logs('C:'.__CLASS__.' L:'.__LINE__.'public 数组内容：'.$this->pw_var_export($filedData),date('Y-m-d' , time()).'_data.log');
		$data = array();
		$data['actionname'] = $this->nodeName;
		$data['actiontitle'] = $this->nodeTitle;
		$data['isaudit'] = $this->isaudit;
		$data['inserttable'] = $this->inserttable;

		$model = D('MisDynamicFormManage');

		//if(C('TOKEN_NAME')) $data[C('TOKEN_NAME')]= $_POST[C('TOKEN_NAME')];
		/* 自带唯一验证
		 if(false == $model->create($data)){
		 $this->error($model->getError());
		 }
		 $ret = $model->add();
		 */

		$count = $model->where("status=1 and actionname='{$this->nodeName}' and id<>".$_POST['dynamicformid'])->count();
		if($count){
			$this->error('当即前Action名称已存在 ');
		}
		C('TOKEN_ON',false);
		$data = $model->create($data);
		// 添加表单信息
		$ret = $model->where('id='.$_POST['dynamicformid'])->save($data);
		logs('C:'.__CLASS__.' L:'.__LINE__."修改manager表：{$ret}".$model->getLastSql());
		
		if(!$ret){
			$this->error($model->getDbError().' '.$model->getError());
		}
		C('TOKEN_ON',true);
		if($_POST['insertnode']){
			$this->addNode($ret);
		}
		//添加表单组件信息
		$filedModel = M('MisDynamicFormPropery'); 
		foreach ($filedData['public'] as $key=>$val){
			$val = $this->propertyReplace($val, $val['category']);
			if($val['category']=="datatable"){
				//构建表名
				$val['datatablemodel']=createRealModelName($this->tableName."_sub_".$val['fieldname']);
				$val['datetablename']=$this->tableName."_sub_".$val['fieldname'];
			}
			if($val[$this->publicProperty['id']['dbfield']]){
				$filedModel->save($val);
				//修改复用表信息
				$MisDynamicDatabaseMasDao=M("mis_dynamic_database_mas");
				$subMap=array();
				$subMap['tablename']=$this->tableName;
				$subMap['ischoise']=1;
				$subMap['status']=1;
				$subMap['formid']=array("neq",$_POST['formid']);
				$MisDynamicDatabaseChoiseMasList=$MisDynamicDatabaseMasDao->where($subMap)->select();
				if($MisDynamicDatabaseChoiseMasList){
					foreach ($MisDynamicDatabaseChoiseMasList as $chokey=>$choval){
						$val['tplid']=getFieldBy($choval['formid'], "formid", "id", "mis_dynamic_form_template");
						$val['formid']=$choval['formid'];
						unset($val['id']);
						unset($val['ids']);
						unset($val['isforeignfield']);
						unset($val['isshow']);
						$MisDynamicDatabaseSubResult=$filedModel->where("formid={$choval['formid']} and tplid={$val['tplid']} and fieldname='{$val['fieldname']}'")->save($val); 
						//$result=$subModel->where("formid={$choval['formid']} and field='{$val['fieldname']}'")->setField("title",$val['title']);
					}
				}
				logs('C:'.__CLASS__.' L:'.__LINE__.'修改组件属性记录：'.$filedModel->getLastSql());
			}else{
				//unset($val[$this->publicProperty['ids']['dbfield']]);
				$val['formid']= $_POST['dynamicformid'];
				$rt = $filedModel->add($val);
			}
		}
		//$this->success('添加成功');
	}
	/**
	 * @Title: createAutoFormConfig
	 * @Description: todo(生成属性配置文件，默认需要生成所有组件)
	 * @param array $data 组件属性列表
	 * @param string $node 所属节点名称
	 * @author quqiang
	 * @date 2014-8-19 上午10:35:06
	 * @throws
	 * @modify 屈强@20141009 1137	组件配置信息文件合并、保存结构修改，【去除所有节点信息，仅保存当前节点信息】
	 */
	public function createAutoFormConfig($data ='' , $node='index'){
		/*
		 原操作 备份
		 $dir = '/autoformconfig/';
		 $defaultPath = $dir.''.$this->nodeName.'.php';
		 $model = D('Autoform');
		 $model->setPath($defaultPath);
		 $model->SetRules($data['all'],$this->nodeName.' 所有节点信息文件'); //存入所有节点信息
		 if($node){ // 存入指定节点的信息
		 $model = D('Autoform');
		 $nodePath = $dir.$this->nodeName.'_'.$node.'.php';
		 $model->setPath($nodePath);
		 $model->SetRules($data['visibility'],$this->nodeName.' 下节点：'.$node.' 可用组件配置文件'); //存入可见节点
		 }
		 */
		/**
		 * 合并配置文件的数据操作
		 * 1。确认当前节点名，
		 * 2。获取当前节点的数据
		 * 3。写入记录
		 * */
		$model = D('Autoform');
		$dir = '/autoformconfig/';
		$defaultPath = $dir.$this->nodeName.'.php';
		$model->setPath($defaultPath);
		//logs('C:'.__CLASS__.' L:'.__LINE__.'我是属性:::  '.$this->pw_var_export($data['all']));
		/**
		 * 从数据库中将所有当前表单的组件信息取出来写入配置文件中
		 */
		logs('C:'.__CLASS__.' L:'.__LINE__.'从数据库中将所有当前表单的组件信息取出来写入配置文件中.'.$defaultPath);
		$DBConfigData = $this->getfieldCategory(getFieldBy($this->nodeName, "actionname", "id", "mis_dynamic_form_manage"));
		$model->SetRules($DBConfigData,'从数据库中将所有当前表单的组件信息取出来写入配置文件中'); //存入所有节点信息
		// 更新字段信息配置文件
		// MisAutoAnameList.inc.php
		// 目前处理的所有表中字段全唯一
		$allData = $this->getDataBaseConf();

		$curData = $allData['cur']; // 当前Action的数据设置
		$allDataSetConf = $allData['data']; // 配置文件中的所有数据

		$curTPLData = $curData['temp']; // 当前模板信息
		$curDataSetConf = $curData['datebase']; // 当前Action下的数据库信息

		$path = $allData['path'];
		/* 得到当前需要更新到表配置中的新加字段 */
		$curModeifyField = $data['conf'];

		// 构建字段数据
		foreach($curModeifyField as $k=>$v){
			$curDataSetConf[$v['tablename']]['list'][] = array(
					'filed' => $v['fields'],
					'tablename' => $v['tablename'],
					'type' => $v['tabletype'],
					'length' =>$v['length'],
					'category' => $v['catalog'],
					'title' => $v['title'],
					'weight' => $this->controlConfig[$v['catalog']]['weight'],
					'sort' => '0',
					'isshow' => '1',
			);
		}
		$curTPLData[$this->curnode] = array(
				'tempname' => $this->curnode,
				'temptitle'	=>	$this->curnodetitle
		);
		/*unset($temp);
		 foreach ($curDataSetConf['datebase'] as $key => $value) {
		 $temp[]=$value;
		 }*/
		// 将新增字段后的配置信息组成
		//$curDataSetConf['datebase'] = $temp;
		$allDataSetConf[$this->nodeName]['datebase']=$curDataSetConf;
		$allDataSetConf[$this->nodeName]['temp']=$curTPLData;
		$model -> setPath($path);
		$model -> SetRules($allDataSetConf,'表、字段信息配置文件');

	}



	/**
	 * @Title: getParame
	 * @Description: todo(公共函数，获取页面POST参数并按组件封装数组
	 * 		'all':'返回所有数据，包括不显示的、不生成字段的',
	 * 		'public':'只含公用属性，用以更新数据',
	 * 		'visibility':'所有属性为可见的，用以生成模板',
	 * 		‘conf’:'需要更新字段配置表，并生成到相应表中的字段信息'
	 * )
	 * @return array|四项
	 * @author quqiang
	 * @date 2014-8-18 下午4:57:12
	 * @throws
	 */
	function getParame() {
		$temp = array();
		// 临时缓存
		$temp1 = array();
		// 临时缓存
		$publicArr = array();
		// 公共属性，更新到字段信息表中的数据
		$visibilityArr = array();
		//可见组件的属性，用以生成页面
		$allArr = array();
		// 所有组件属性。
		$fieldsArr = $_POST['fields'];
		// 得到需要更新配置文件 的字段
		$confArr = array();

		// 得到组件的唯一标识
		foreach ($fieldsArr as $k => $v) {
			/**
			 * 获取公用属性
			 */
			$controlTempArr = array();
			foreach ($this->publicProperty as $kye => $val) {
				//				if($val['identity']){
				//					$temp[$v][$val['name']] = $_POST[$val['name']][$k];
				//				}else{
				//					$temp[$v][$val['name']] = $_POST[$val['name']][$v];
				//				}
				//
				if ($val['identity']) {
					$t = $_POST[$val['name']][$k];
				} else {
					$t = $_POST[$val['name']][$v];
				}
				$controlTempArr[$v][$val['name']] = $t;
				if ($val['dbfield']) {
					$publicArr[$v][$val['dbfield']] = $t;
				}
			}
			/**
			 * 获取所有组件属性和可显示组件属性
			 * 1：先取得组件类型
			 * 2：从POST中取出该组件的属性
			 * 3：分离出不生成字段的，
			 */
			//			unset($temp);
			//			unset($controlTempArr);
			unset($t);
			unset($visib);
			foreach ($this->privateProperty[$controlTempArr[$v][$this->publicProperty['catalog']['name']]] as $key => $val) {
				/* 得到具体属性的值 */
				// 得到所有组件
				if ($val['identity']) {
					if ($val['name']) {
						$t[$val['name']] = $_POST[$val['name']][$k];
					}
				} else {
					if ($val['name']) {
						$t[$val['name']] = $_POST[$val['name']][$v];
					}
				}
				if ($val['dbfield']) {
					$publicArr[$v][$val['dbfield']] = $t[$val['name']];
				}
					
			}
			$allArr[$v] = $t;
			// 可显示的组件
			if (1){ //$t['isshow']) {
				$visibilityArr[$v] = $t;
			}

			if($t['ids']=='' && $t['tablename'] && $this->controlConfig[$t['catalog']]['iscreate']){
				$confArr[$v] = $t;
			}
		}

		$a = array('all' => $allArr, 'public' => $publicArr, 'visibility' => $visibilityArr , 'conf'=>$confArr);
		return $a;

	}






	/**
	 * @Title: addNode
	 * @Description: todo(生成数据库节点)
	 * @param unknown_type $dynamicid
	 * @author quqiang
	 * @date 2014-8-18 下午4:53:54
	 * @throws
	 */
	private function addNode($dynamicid){
		if(!$_POST['dynamicformid']){
			$_POST['dynamicformid'] = $dynamicid;
		}
		$nodemodel= D("Node");
		$nodename=ucfirst($this->nodeName);
		//向数据库插入节点
		$groupId = $nodemodel->where("id=".$_POST['parentnodename'])->getField("group_id");
		$data=array();
		$data["name"]=$nodename;
		$data["title"]=$this->nodeTitle;
		$data["type"]=3;
		$data["showmenu"]=1;
		$data["level"]=3;
		$data["pid"]=$_POST["parentnodename"];
		$data["group_id"]=$groupId;
		$data["showmenu"]=$_POST['showmenu']?1:0;
		$data['icon']='-.png';
		$data['isdynamic'] =1;
		$data['isprojectwork'] =1;
		$data['isprocess'] = $this->isaudit;
		$data['remark']='自动生成:'.$nodename;
		//调用配置文件selectlist.inc.php和 property.php 查出模板类型的索引号 列入node节点表入库字段  formtype
		$list =require D('Selectlist')->getFile();
		require  CONF_PATH.'property.php';
		$formtypearr = $NBM_TPL_TYPE;
		foreach($list['AutotypEnum']['AutotypEnum'] as $k=>$v){
			if($formtypearr[$_POST['nbm_tpl_type']][0]['title'] == $v){
				$data['formtype'] = $k;
			}			
		}
		
		$nodemodel = D("Node");
		if($_POST['modeltype']=='add'){
			$row = $nodemodel->where('name="'.$nodename.'" and level=3')->field('id')->find();
			if($row){
				if($_POST['modeltype']=='update'){
					$data['pid']=$_POST['parentnodename'];
					$nodemodel->where('id='.$row['id'])->save($data);
				}else{
					//$this->error ( '节点已存在，请修改' );
					$msg = "节点插入失败！ {$nodename}-{$this->nodeTitle}节点已存在，请修改！";
					logs($msg , '' ,ROOT . '/Dynamicconf/createFormLog');
					throw new NullDataExcetion($msg);
				}
			}
			if(C('TOKEN_NAME')) $data[C('TOKEN_NAME')]= $_POST[C('TOKEN_NAME')];
			if (false === $nodemodel->create ($data)) {
// 				$this->error ( $nodemodel->getError () );
				$msg = $nodemodel->getError ();
				logs($msg , '' ,ROOT . '/Dynamicconf/createFormLog');
				throw new NullDataExcetion($msg);
			}
			$list=$nodemodel->data($data)->add();
			logs($nodemodel->getLastSql() , '' ,ROOT . '/Dynamicconf/createFormLog');
		}else{
			if($_POST['nodeid']){
				unset($data);
				$data['showmenu'] = $_POST['showmenu']?1:0;
				//				$data['isprocess'] = $this->isaudit;

				$list=$nodemodel->where('id='.$_POST['nodeid'])->save($data);
				if ( !$list ){
					//$this->error ('修改失败!');
					$msg = "修改失败!".$nodemodel->getLastSql();
					logs($msg , '' ,ROOT . '/Dynamicconf/createFormLog');
					throw new NullDataExcetion($msg);
				}
			}
		}
	}





	/******************************************  为页面ajax获取数据准备  ****************************************************/
	/**
	 * @Title: getNodeList
	 * @Description: todo(获取节点列表)
	 * @author quqiang
	 * @date 2014-8-18 下午4:53:11
	 * @throws
	 */
	function getNodeList(){
		// 		$nodeModel= D("Node");
		// 		$map["type"]=1;
		// 		$map['level']=2;
		// 		$map["status"]=1;
		// 		$list = $nodeModel->where($map)->select();
		// 		foreach( $list as $k=> $v ){
		// 			$map['type']=2;
		// 			$map['level']=3;
		// 			$map['pid']=$v['id'];
		// 			$list2 = $nodeModel->where($map)->getField("id,title");
		// 			if($list2){
		// 				$list[$k]["children"]=$list2;
		// 			}
		// 		}
		// 		$map2["type"]=3;
		// 		$map2['level']=3;
		// 		$map2["status"]=1;
		// 		$nodemodellist = $nodeModel->where($map2)->select();
		// 		$this->assign("nodemodellist", $nodemodellist);
		// 		$this->assign("nodelist", $list);
		//查询部门
		//$MisSystemDepartmentModel=D('MisSystemDepartment');
		//$DeptList=$MisSystemDepartmentModel->where(" status=1")->getField('id,name');
		//$this->assign("DeptList",$DeptList);
		//所属组信息
		// 		$groupDAO=M('group');
		// 		$grouplist=$groupDAO->where('status = 1')->field('id,name')->select();
		// 		$this->assign('grouplist',$grouplist);

		//$model = D('MisSystemRecursion');
		//$model->__construct();
		// ,'conditions'=>'`level` NOT IN(1,4) AND STATUS = 1'
		//$data = $model->modelShow('Node',array('key'=>'id','pkey'=>'pid','fields'=>'id,title','conditions'=>'`status` = 1 and `level` < 3'),0,1);
		//$this->assign("nodedatalist", $data);
	}


	public function lookupcdw(){
		//所属节点信息
		$nodeDAO=M('node');
		$map['group_id']=$_POST['group_id'];
		//$map["type"]=1;
		$map['level']=2;
		$map["status"]=1;
		$nodelist = $nodeDAO->where($map)->field("id,name,title")->select();
		echo json_encode($nodelist);
	}
	/**
	 * @Title: getOptions
	 * @Description: 获取 selelist 枚举数据
	 * @author quqiang
	 * @date 2014-8-20 下午05:51:53
	 * @throws
	 */
	function getOptions($iscur=true){
		$model=D('Selectlist');

		if(file_exists($model->GetFile())){
			$selectlist = require $model->GetFile();
		}
		$temp = array();
		foreach ($selectlist as $k=>$v){
			$temp[$k] =$k.'('.$v['name'].')';
		}
		if($iscur){
			echo json_encode($temp);
		}else{
			return $temp;
		}
	}
	/**
	 * @Title: getTables
	 * @Description: todo(获取当前所有数据表信息)
	 * @author quqiang
	 * @date 2014-8-18 下午4:54:35
	 * @throws
	 */
	public function getTables($iscur=true){
		$model3=M();
		$tables = $model3->query("SELECT `TABLE_NAME`,`TABLE_COMMENT` FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '".C('DB_NAME')."' ");//limit 1
		$tables2=array();
		foreach($tables as $k=>$v){
			$k=$v['TABLE_NAME'];
			$v=$v['TABLE_COMMENT'];
			if($v==""){
				$tables2[$k]=$k;
			}else{
				$v=explode("; InnoDB",$v);
				$tables2[$k]=$v[0];
				if( count(explode("InnoDB",$v[0])) >1 ){
					$tables2[$k]=$k;
				}
			}
		}
		if($iscur){
			echo json_encode($tables2);
		}else{
			return $tables2;
		}
	}

	/**
	 * @Title: comboxgetTableField
	 * @Description: todo(获取指定表下的字段信息)
	 * @param string $t	传表名。当该值为空时为ajax获取字段，post 参数为table，输出json数据。
	 * @return multitype:array|json
	 * @author quqiang
	 * @date 2014-8-18 下午4:54:54
	 * @throws
	 */
	function comboxgetTableField( $t='' ){
		$model=M();
		if( $t=="" ){
			$table = $this->escapeStr($_POST['table']);
		}else{
			$table = $t;
		}
		//$arr=array(array("","请选择映射对象字段"));
		$arr=array();
		if ($table!='') {
			$columns = $model->query("show columns from ".$table);
			$model2=M("INFORMATION_SCHEMA.COLUMNS","","",1);
			$columnstitle = $model2->where("table_name = '".$table."' AND TABLE_SCHEMA = '".C('DB_NAME')."'")->getField("COLUMN_NAME,COLUMN_COMMENT");
			foreach($columns as $k=>$v){
				$title=$v['Field'];
				if( $columnstitle[$v['Field']] ) $title=$columnstitle[$v['Field']];
				$arr[$v['Field']] = $title;
			}
		}
		//file_put_contents('a.txt',json_encode($arr));
		if( $t=="" ){
			echo json_encode($arr);
		}else{
			return $arr;
		}
	}

	/**
	 * @Title: addprocess
	 * @Description: todo(流程管理)   
	 * @author 黎明刚
	 * @date 2015年1月27日 上午10:22:31 
	 * @throws
	 */
	public function addprocess(){
		//实例化流程模型
		$ProcessManageModel = D("ProcessManage");
		//获取模型名称
		$modelname=$_REQUEST['nodename'];
		$this->assign("modelname",$modelname);
		// 获取 所有流程 view 查看页面
		$pInfoModel = D("ProcessInfo");
		$pInfoMap['status'] = 1;
		//默认则查询第一个节点的所有流程
		$pInfoMap['nodename'] = $modelname;
		//查看多个流程简要信息
		$pInfoList = $pInfoModel->where($pInfoMap)->order('id desc')->find();
		if($pInfoList){
			//存在流程
			$data = array();
			$data = json_decode($pInfoList['flowtrack'],true);
			unset($_SESSION["flowsdata"]);
			//为session存储内容，方便读取
			$_SESSION["flowsdata"] = $data;
			//输出组件需要的json内容
			$jsondata = $ProcessManageModel->setDataJson($data);
			$this->assign("data",$jsondata);
		}else{
			//不存在流程
			$data = array();
			$data[] = array("ids"=>1,"processname"=>"开始","flag"=>2,"key"=>0,"level"=>1,"choose"=>0,'prcsid'=>1,'modelname'=>$pInfoMap['nodename']);
			unset($_SESSION["flowsdata"]);
			//为session存储内容，方便读取
			$_SESSION["flowsdata"] = $data;
			//输出组件需要的json内容
			$jsondata = $ProcessManageModel->setDataJson($data);
			$this->assign("data",$jsondata);
		}
		$this->assign("pInfoVo",$pInfoList);
		$this->display();
	}
	/**
	 *
	 * @Title: insertprocess
	 * @Description: todo(新增流程走向及走向绑定模板)
	 * @author renling
	 * @date 2014-10-15 下午4:44:54
	 * @throws
	 */
	public function insertprocess(){
		//新增pinfoid
		$nodename=$_POST['nodename'];
		$ProcessInfoModel=D('ProcessInfo');
		$infodate=array();
		$infodate['nodename']=$nodename;
		$infodate['rules']=str_replace("&#39;", "'", html_entity_decode($_POST['rules']));
		$infodate['name']=$_POST['name'];
		$infodate['rulesinfo']=$_POST['rulesinfo'];
		$infodate['showrules']=str_replace("&#39;", "'", html_entity_decode($_POST['showrules']));
		if($_POST['pInfoId']){
			$infoResult=$_POST['pInfoId'];
			$infodate['id']=$_POST['pInfoId'];
			$ProcessInfoModel->save($infodate);
		}else{
			
			$infoResult=$ProcessInfoModel->add($infodate);
		}
		$tname=$_POST['tname'];
		$ProcessRelationModel=D('ProcessRelation');
		//批次信息表
		$MisSystemUserBactchModel = D("MisSystemUserBactch");
		$MisAutoProcessModel=M("mis_auto_process");
		//先删除原有的流程批次信息，在进行新增
		$map=array();
		$map['pinfoid']=$infoResult;
		$map['tablename']="process_info";
		$relaids = $ProcessRelationModel->where($map)->getField("id,pinfoid");
		if($relaids){
			$map = array();
			$map['id'] = array(' in ',array_keys($relaids));
			$resultrela=$ProcessRelationModel->where($map)->delete();
			$map = array();
			$map['tablename'] = 'process_relation';
			$map['tableid'] = array(' in ',array_keys($relaids));
			$resultbactch=$MisSystemUserBactchModel->where($map)->delete();
			$resultap = $MisAutoProcessModel->where("nodename='".$nodename."'  and pinfoid=".$infoResult)->delete();
			if(!$resultrela || !$resultbactch||!$resultap){
				$this->error("数据绑定失败，请联系管理员");
			}
		}
		foreach ($tname as $key=>$val){
			$data=array();
			$data['pinfoid']=$infoResult;
			$data['tablename']="process_info";
			$data['sort']=$key;
			$data['name']=$val;
			$data['createtime']=time();
			$data['createid']=$_SESSION[C('USER_AUTH_KEY')];
			$result=$ProcessRelationModel->add($data);
			if(!$result){
				$this->error("数据提交错误！");
			}
			//添加动态表单关联表
			$mProcessData=array();
			$mProcessData['pinfoid']=$infoResult;
			$mProcessData['relationid']=$result;
			$mProcessData['nodename']=$nodename;
			$mProcessData['tempname']=$_POST['tempname'][$key];
			$mProcessResult=$MisAutoProcessModel->add($mProcessData);
			if(!$result){
				$this->error("动态表单数据错误！");
			}
			//获取当前流程节点的批次，规则
			$userobjidStr=explode(";",$_POST['userobjidStr'][$key]);
			$userobjStr=explode(";",$_POST['userobjStr'][$key]);
			$userobjname = explode(";",$_POST['userobjStrname'][$key]);
			$bactchStr=explode(";",$_POST['bactchStr'][$key]);

			//替换html标签字符
			$fields = str_replace("&#39;", "'", html_entity_decode($_POST['ruleStr'][$key]));
			$ruleStr = explode(";",$fields);
			$rulenameStr = explode(";",str_replace("&#39;", "'", html_entity_decode($_POST['rulename'][$key])));
			$rulesinfoStr = explode(";",$_POST['rulesinfoStr'][$key]);

			$dataList = array();
			foreach($userobjidStr as $bactchkey=>$bactchval){
				$dataList[] = array(
						'tablename'=>"process_relation",
						'tableid'=>$result,
						'userobjid'=>$bactchval,
						'userobj'=>$userobjStr[$bactchkey],
						'userobjname'=>$userobjname[$bactchkey],
						'rule'=>$ruleStr[$bactchkey],
						'rulename'=>$rulenameStr[$bactchkey],
						'rulesinfo'=>$rulesinfoStr[$bactchkey],
						'sort'=> $bactchStr[$bactchkey],
						'createtime'=> time(),
						'createid'=>$_SESSION[C('USER_AUTH_KEY')],
				);
			}
			$bactchResult=$MisSystemUserBactchModel->addAll($dataList);
			if(!$bactchResult){
				$this->error("流程节点批次报错失败,请联系管理员");
			}
		}
		$this->success("配置成功！");
	}
	function   lookupchangeaname(){
		$list=$this->getDateSoure($_REQUEST['val']);
		echo json_encode($list);
		//$this->assign("MisSaleClientTypeList",$MisSaleClientTypeList);
	}
	/**
	 * 获取可用的下拉框，用于设置下拉返写
	 * @Title: getDropbackinfo
	 * @Description: todo()   
	 * @author quqiang 
	 * @date 2015年8月19日 下午3:04:31 
	 * @throws
	 */
	public function getDropbackinfo(){
		$nodename = $_POST['nodename'];
		$model = M('mis_dynamic_form_propery');
		$map['modelname']=$nodename;
		$map['category'] = "select";
		$map['_string']="dropbackkey is not null";
		$data = $model->where($map)->select();
		echo $model->getLastSql();
		echo json_decode($data);
		// 构建sql语句
		$sql ="select fieldname , title from mis_dynamic_form_propery where ";
		//$arr[$v['danweidaima']] = $title;
		
	}
	/***************************************************************************
	 * 函数结束
	 ***************************************************************************/



	/**
	 * @Title: getControllDataSouce
	 * @Description: todo(取得指定组件项的数据源配置情况)
	 * @param $string $key	组件的字段名称。   默认为空，支持url请求
	 * @author quqiang
	 * @date 2014-11-24 下午04:19:35
	 * @throws
	 */
	public function getControllDataSouce($key=''){
		$findField='';
		if(!$key){
			// 非类中直接调用时 从请求中获取fileds提交值
			$findField = $_REQUEST['filed'];
		}else{
			$findField = $key;
		}

		$obj = D('Autoform');
		$dir = '/autoformconfig/';
		$path = $dir.$this->nodeName.'.php';
		$obj->setPath($path);
		// 获取当前节点的字段组件配置信息
		$data = $obj->getOrderNode();
		if(!$data[$findField]){
			$this->error('数据源为空，请在配置好数据源后再来配置该显示字段与值字段');
			//echo '空数据';
		}else{
			$cate = $data[$findField]['catalog'];
			$prpoerty = $this->getProperty($cate);
			// 用户指定了枚举数据
			if($data[$findField][$prpoerty['showoption']['name']]){
				$this->error('枚举数据显示与值字段为固定，不可修改');
			}elseif ($data[$findField][$prpoerty['subimporttableobj']['name']] || $data[$findField][$prpoerty['treedtable']['name']]){
				// 取得指定表对应的list.inc字段列表。
				/*	这里只能获取到表名，但获取不到 action名就意味着获取不到list.inc文件的内容。
				 $SystemConfigDetailObj = D('SystemConfigDetail');
				 $data = $SystemConfigDetailObj->getDetail($findField);
				 // 可用字段缓存
				 $filedArr='';
				 foreach ($data as $key=>$val){
				 // 过滤掉不用的字段列表
				 if(!in_array($val['name'], array('action'))){
				 $filedArr[]=array('key'=>$val['name'] , 'val'=>$val['showname']);
				 }
				 }
				 */

				// 目前就是直接获取表中的字段。
				$orderTableName = $data[$findField][$prpoerty['subimporttableobj']['name']]?$data[$findField][$prpoerty['subimporttableobj']['name']]:($data[$findField][$prpoerty['treedtable']['name']]?$data[$findField][$prpoerty['treedtable']['name']]:'');
				// SELECT COLUMN_NAME , COLUMN_COMMENT FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='system1test' AND TABLE_NAME='ztest'
				$data = self::comboxgetTableField($orderTableName);
				// 得到系统使用字段复本并移除可以使用的字段
				$systemfield = $this->systemUserField;
				//var_dump($systemfield);
				/**
				 *  @todo 没有过滤系统默认字段。
				 */

				$filedArr = '';
				foreach ($data as $k=>$v){
					if(!in_array($k, $systemfield)){
						$filedArr[]=array('key'=>$k,'val'=>$v);
					}
				}
				unset($systemfield);
				$this->success('请向设置值与显示内容' , '' , json_encode($filedArr));
			}else {
				$this->error('数据源为空，请在配置好数据源后再来配置该显示字段与值字段');
			}
		}
	}
	/**
	 * @Title: createBaseArchivesCode
	 * @Description: todo(生成基础档案代码)
	 * @param array		$temp	模板生成方案数据
	 * @author quqiang
	 * @date 2014-11-25 下午09:55:44
	 * @throws
	 */
	private function createBaseArchivesCode($temp){

		$jsPath=$this->nodeName;
		$addjs = $this->getJS ( $jsPath );
		$addjs .= $this->getJS ( $jsPath, 'addExtend' );
		$editjs = $this->getJS ( $jsPath, 'edit' );
		$editjs .= $this->getJS ( $jsPath, 'editExtend' );
		
		
		
		// 获取当前节点的字段组件配置信息
		$curnodeData = $this->getOrderNode('index');
		 logs('C:'.__CLASS__.' L:'.__LINE__.'生成基础档案'.$this->pw_var_export($temp));
		 
		switch($temp[0]){
			case 'basisarchivestpl':
				switch($temp[1]){
					case 'ltrc':
						// 基础档案 左侧列表，右侧ajax刷新编辑页面
						// 生成action
						$this->createBaseArchives($this->nodeName, false , false);
						// 生成新增模板
						//$baseAddHtml = $this->createBaseArchivesAddTemplate($curnodeData , $this->nodeName, '' , false);
						$baseAddHtml = $this->getPage('archiver_ajax_add', $curnodeData, 1);
						$file = TMPL_PATH.C('DEFAULT_THEME')."/".$this->nodeName."/add.html";
						if(!is_dir(dirname($file))) mk_dir(dirname($file),0777);
						if( false === file_put_contents( $file , $baseAddHtml )){
							$this->error ( "基础档案add.html生成失败");
						}
						$baseIndexHtml = $this->getPage('archiver_ajax_index', $curnodeData, 1);
						$file = TMPL_PATH.C('DEFAULT_THEME')."/".$this->nodeName."/index.html";
						if(!is_dir(dirname($file))) mk_dir(dirname($file),0777);
						if( false === file_put_contents( $file , $baseIndexHtml )){
							$this->error ( "基础档案index.html生成失败");
						}
						$baseIndexViewHtml = $this->getPage('archiver_ajax_indexview', $curnodeData, 1);
						$file = TMPL_PATH.C('DEFAULT_THEME')."/".$this->nodeName."/indexview.html";
						if(!is_dir(dirname($file))) mk_dir(dirname($file),0777);
						if( false === file_put_contents( $file , $baseIndexViewHtml )){
							$this->error ( "基础档案indexview.html生成失败");
						}
						// 生成Model
						$this->createModel('','');
						// 生成list文件
						$this->modifyConfig('',$this->nodeTitle , false,true);
						break;
					case 'ltrl':
						// 基础档案 左侧树右侧列表
						// 生成action
						$this->createBaseArchivesLsit($this->nodeName, false , false);
						// add
						//$baseAddHtml = $this->createBaseArchivesListAddTemplate($curnodeData , $this->nodeName, '' , false);
						$baseAddHtml = $this->getPage('archivers_add', $curnodeData, 0);
						$file = TMPL_PATH.C('DEFAULT_THEME')."/".$this->nodeName."/add.html";
						if(!is_dir(dirname($file))) mk_dir(dirname($file),0777);
						if( false === file_put_contents( $file , $addjs.$baseAddHtml )){
							$this->error ( "基础档案add.html生成失败");
						}
						//edit
// 						$baseEditHtml = $this->createBaseArchivesListEditTemplate($curnodeData , $this->nodeName, '' , false);
						$baseEditHtml = $this->getPage('archivers_edit', $curnodeData, 1);
						$file = TMPL_PATH.C('DEFAULT_THEME')."/".$this->nodeName."/edit.html";
						if(!is_dir(dirname($file))) mk_dir(dirname($file),0777);
						if( false === file_put_contents( $file , $editjs.$baseEditHtml )){
							$this->error ( "基础档案edit.html生成失败");
						}
						// index.html
// 						$baseIndexHtml = $this->createBaseArchivesIndexTemplate($this->nodeName, $this->nodeTitle,false);
						$baseIndexHtml = $this->getPage('archivers_index', $curnodeData, 1);
						$file = TMPL_PATH.C('DEFAULT_THEME')."/".$this->nodeName."/index.html";
						if(!is_dir(dirname($file))) mk_dir(dirname($file),0777);
						if( false === file_put_contents( $file , $baseIndexHtml )){
							$this->error ( "基础档案index.html生成失败");
						}
						// indexView.html
// 						$baseIndexViewHtml = $this->createBaseArchivesListIndexView($this->nodeName);
						$baseIndexViewHtml = $this->getPage('archivers_index_view', $curnodeData, 1);
						$file = TMPL_PATH.C('DEFAULT_THEME')."/".$this->nodeName."/indexview.html";
						if(!is_dir(dirname($file))) mk_dir(dirname($file),0777);
						if( false === file_put_contents( $file , $baseIndexViewHtml )){
							$this->error ( "基础档案indexview.html生成失败");
						}
						// 生成Model
						//$this->createModel('','');
						// 生成list文件
						//$this->modifyConfig('',$this->nodeTitle , false,true);
						break;
				}
				break;
			case 'basisarchivesaudittpl':
				// 基础档案 有审批
				break;
		}
		$updatefile = TMPL_PATH.C('DEFAULT_THEME')."/".$this->nodeName."/misSystemDataUpdate.html";
		if(is_file($updatefile)){
			unlink($updatefile);
		}
				
	}
	/**
	 * @Title: getControllDataSouce
	 * @Description: todo(取得指定组件项的属性配置)
	 * @author 王昭侠
	 * @date 2014-11-24 下午04:19:35
	 * @throws
	 */
	/**
	 * @Title: editProperty
	 * @Description: todo(内嵌表格的组件属性设置之取得指定组件项的属性配置)
	 * @author 谢友志
	 * @date 2014-11-29 下午12:17:41
	 * @throws
	 */
	public function editProperty(){
		//$selectlist  select组件绑定表头字段的select组件 读取所有的表头select组件字段
		$propertymodel = M("mis_dynamic_form_propery");
		$selectlist = $propertymodel->where("formid=".$_REQUEST['formid']." and status=1 and category='select'")->getField('id,fieldname,title');
		if($selectlist){
			$this->assign("selectlist",$selectlist);
		}
		$this->assign('obj_name',$_REQUEST["obj"]);
		$this->assign('obj_type',$_REQUEST["type"]);
		//通用数据 检查类型
		$checktypearr = array(
			''=>'无需验证',
			'eamil'=>'邮箱',
			'url'=>'网址',
			'number'=>'整数',
			'double'=>'浮点数',
			'lettersonly'=>'字母',
		);
		$this->assign('checktypearr',$checktypearr);
		//修改需传的值  判断$editvalue存在不 存在表示修改
		$editvalue = $_REQUEST['editvalue'];
		if($editvalue){
			$temp = unserialize(base64_decode($editvalue));
			if(is_array($temp)){
				
				if(substr($temp['parame'][1],0,3)=="org"){
					$temp['parame'][1] = substr($temp['parame'][1],3);
				}
				if($temp['type']=="select" && $temp['datasouceparame'][0]){
					//读取字段信息
					$DynamicconfModel=D("Dynamicconf");
					$mytablelist=$DynamicconfModel->getTableInfo($temp['datasouceparame'][0]);
					$this->assign("tablelist",$mytablelist);
				}
				if(substr($temp['parame'][1],0,3)=="org"){
					$temp['parame'][1] = substr($temp['parame'][1],3);
				}		 
				$this->assign('editvalue',$temp);
			}
		}
		/*
		 * 新增页面
		 */
		//日期选择器
		//选择动态建模用的日期格式
		$controlls =require CONF_PATH.'controlls.php';
		$dateformartdata = explode('#',$controlls['date']['property']['format']['data']);
		foreach($dateformartdata as $key=>$val){
			$v = preg_split("/\||@/", $val);
			$datej_p[$v[0]] = $v;			//js-php日期格式对应
			$dateformatlist[$v[0]]=$v[2];	//页面数据用js-中文 日期格式对应
		}
//		print_r($dateformartdata);
		$model = D("Selectlist");
// 		$selectlist = require $model->GetFile();
// 		$dateformatlist = $selectlist['dateformatchoice']['dateformatchoice'];
		$this->assign('dateformatlist',$dateformatlist);
		//文本框单位
		$baseuntils = getBaseUnit();
		$this->assign('baseuntils',$baseuntils);
 		foreach($baseuntils as $k=>$v){
 			$unils[$v['danweidaima']] = getSubUnit($v['danweidaima']);
 		}
 		$this->assign("unils",json_encode($unils));
 		$this->assign('unitls',$unils);
		//$this->assign('untils',$this->untils);
		//根据传入的type确定给模板赋值
		if($_REQUEST['type']=='lookup'){
			$list = $this->lookuoconfig(false);
			$this->assign('lookuplist',$list);
		}elseif($_REQUEST['type']=='select'){
			//配置表数据
			$newselectinclist = $this->getOptions(false);
			$this->assign('newselectinclist',$newselectinclist);
			// 数据表数据
			$dblist = $this->getTables(false); 
			$this->assign('newwdblist',$dblist);
		}
		//将保存的值json返回
		if($_POST["addtype"]){ 
			$data = $_POST;
			$newdata = array();
			$category = $data['category']?$data['category']:'';
			$newdata['required'] = $_POST['dateneed'];//.' '.$checktypearr[$_POST['checktype']];//通用 是否必填 检查类型
			$newdata['checktype'] = $_POST['checktype'];
			switch ($category){
				case 'text':		//文本框
					$newdata['type'] = 'text';
					$newdata['parame'][0]=$_POST['lookuporgfield']?'org'.$_POST['lookuporgfield'].'.'.$_POST['lookupbringfield']:'';
					$newdata['untils'] = $_POST['fieldunit']?$_POST['fieldunit']:'';//单位英文
					$newdata['untilschar'] = $_POST['untilschar']?$_POST['untilschar']:'';//单位中文
					$newdata['baseuntils'] = $_POST['basefieldunit']?$_POST['basefieldunit']:'';
					$newdata['baseuntilschar'] = $_POST['baseuntilschar']?$_POST['baseuntilschar']:'';
					break;
				case 'lookup':		//lookup
					$newdata['type'] = 'lookup';
					$newdata['parame'][0] = $data['lookupname']?$data['lookupname']:'';
					$newdata['parame'][1] = $_POST['lookuporgname']?'org'.$_POST['lookuporgname']:''; // 设置lookup的lookupgroup 值
					$newdata['dateconditions']=$_POST['dateconditions']?$_POST['dateconditions']:'';//lookup 附加条件
					$newdata['ltol'][0]=$data['lookuporgfield'];
					$newdata['ltol'][1]=$data['orgvalue'];
					$newdata['ltol'][2]=$data['orgtext'];
					break;
				case 'date':		//日期格式
					$newdata['type'] = 'date';
					$data['dateformatlist'] = $data['dateformatlist']?$data['dateformatlist']:'yyyy-MM-dd';
					$newdata['parame'][0] = $data['dateformatlist']?$data['dateformatlist']:'';
					$newdata['parame']['org']=$_POST['lookuporgfield']?'org'.$_POST['lookuporgfield'].'.'.$_POST['lookupbringfield']:'';
					$newdata['parame']['dateformat'] = trim($data['dateformatlist'])?$datej_p[$data['dateformatlist']]:'';
					//print_r($newdata['parame']);
					break;
				case 'select':	//下拉框
					$newdata['type'] = 'select';
					//下拉框两种类型 select-配置表 dbtable-数据表
					// dbtable包含有 dbtablename-映射表 showfield-绑定显示字段 orgfield-绑定值字段
					//select包含有 select_table-映射表
					$newdata['parame']['org']=$_POST['lookuporgfield']?'org'.$_POST['lookuporgfield'].'.'.$_POST['lookupbringfield']:'';
					if($data['orgtype']=='0'){
						$newdata['datasouce'] = 0;
						$newdata['datasouceparame'][0] = $data['selecttablename']?$data['selecttablename']:'';
						$newdata['selectassignment'] = $data['selecetassignment'];
					}elseif($data['orgtype']=='1'){
						$newdata['datasouce'] = 1;
						$newdata['datasouceparame'] = array(
								'0'=>$data['dbtablename']?$data['dbtablename']:'',
								'1'=>$data['showfield']?$data['showfield']:'',
								'2'=>$data['orgfield']?$data['orgfield']:'',
								'treeparentfield'=>$data['treeparentfield']?$data['treeparentfield']:'',//树形父级
								'isnextend'=>$data['isnextend']?$data['isnextend']:'',//是否顶级
								'mulit'=>$data['mulit']?$data['mulit']:'',//是否多选
								'treeheight'=>$data['treeheight']?$data['treeheight']:'',//树形展示高度
								'treewidth'=>$data['treewidth']?$data['treewidth']:'',//树形展示宽度
						);
					}
					break;
				default:
					break;
			} 
			$newdata = str_replace('=', '', base64_encode(serialize($newdata)));
			$this->success("添加成功",'',$newdata);
		}
		// list
		// tagble
		// lokupobj

		$this->display();
	}
	/**
	 * @Title: lookupFieldselct
	 * @Description: todo(内嵌表格的组件属性设置之文本框选择lookup联动)
	 * @author 谢友志
	 * @date 2014-11-30 下午5:21:22
	 * @throws
	 */
	function lookupFieldselct(){
		$lookupval = $_POST['lookupval'];
		if($lookupval){
		//解析查找带回数据 并获取查找带回的字段数组
		$lookupvalue = unserialize(base64_decode($lookupval));
		$model=D('LookupObj');
// 		if(file_exists($model->GetFile())){
// 			$selectlist = require $model->GetFile();
// 		}
// 		$fields = $selectlist[$lookupvalue['parame'][0]]['fields'];
// 		$selectmodel = $selectlist[$lookupvalue['parame'][0]]['mode'];
		$key = $lookupvalue['parame'][0];
		$filearr = $model->GetLookupDetail($key);
		
		$fields=$filearr['fields'];
		 $selectmodel = $filearr['mode'];
		//如果是系统视图
		if($filearr['viewname']){		
		   $viewmodel = $filearr['viewname'];
		}else{
		   $viewmodel ="";
		}
		$fieldarr = $this->getDeatilshowname($selectmodel,$fields,$viewmodel);			
		}else($fieldarr=array());
		//将带回字段返回
		exit(json_encode($fieldarr));
	}
	/**
	 * @Title: getDeatilshowname
	 * @Description: todo(lookup带回字段（英文）和其中文名称一起组合成数组) 
	 * @param string $model 模型名称
	 * @param string $name 带回字段
	 * @return multitype:unknown   
	 * @author 谢友志 
	 * @date 2015-1-6 下午1:58:00 
	 * @throws
	 */
	private function getDeatilshowname($model,$name,$viewModel){
		$name=explode(",",$name);
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($model,false,$viewModel);
		require CONF_PATH. 'property.php';
		$syslist = array_merge($NBM_DEFAULTFIELD,$NBM_AUDITFELD,$NBM_BASEARCHIVESFIELD);
		foreach($syslist as $key=>$val){
			$syslist[$key]['name']=$key;
			$syslist[$key]['showname'] = $val['title'];
		}
		$detailList = array_merge($syslist,$detailList);
		$shownamelist=array();
		foreach ($detailList as $dlist=>$dval){
			foreach ($name as $key=>$val){
				if($dval['name']==$val){
					$shownamelist[$val]=$dval['showname'];
				}
			}
		}
		return $shownamelist;
	}
	/**
	 *
	 * @Title: lookupFieldDate
	 * @Description: todo(移除)
	 * @param unknown $fieldName 查询字段名称
	 * @param unknown $fieldTable   查询表名
	 * @param unknown $type   1为ajax请求 无数据为移除字段返回
	 * @author renling
	 * @date 2014年12月2日 上午11:01:36
	 * @throws
	 */
	public function lookupFieldDate($fieldName,$fieldTable){
		//查询字段
		if(!$fieldName)$fieldName=$_REQUEST['fieldName'];
		//查询表
		if(!$fieldTable)$fieldName=$_REQUEST['fieldTable'];
		$model=D($fieldTable);
		//查询此字段数据 排除空数据
		$list=$model->where("status=1 and {$fieldName}<>''")->field($fieldName)->select();
		if($_POST['type']){
			//ajax 请求
			echo json_encode($list);
		}else{
			//修改移除时返回数据
			return $list;
		}
	}
	/**
	 *
	 * @Title: lookupexittitle
	 * @Description: todo(查询表单名称是否重名)
	 * @author renling
	 * @date 2014年12月4日 下午10:49:56
	 * @throws
	 */
	public function lookupexittitle(){
		$title=$_POST['title'];
		$nodeModel=D('Node');
		//查询节点表是否有该title
		$nodeMap=array();
		$nodeMap['status']=1;
		$nodeMap['title']=array('eq',$title);
		$nodeMap['type']=3;
		$nodeVo=$nodeModel->where($nodeMap)->find();
		if($nodeVo){
			echo json_encode($nodeVo);
		}else{
			echo "";
		}
	}
	/**
	 * @Title: ajaxeditporperty 
	 * @Description: todo(ajax动态保存组件属性)   
	 * @author quqiang 
	 * @date 2014-12-16 下午04:13:06 
	 * @throws
	 */
	public function ajaxeditporperty(){
		$error=array(
				'code'=>'1',
				'msg'=>'操作成功'
		);
		$data = $this->getParame();
		unset($temp);
		$temp = reset($data['public']);
		// 如果数据中ID项没有值，表示需要生成到数据。
		// 得到组件配置信息中的iscreate项值为1表示需要生成DB字段。
		// todo 异常，表名为空时提示用户字段创建失败需要删除当前组件后重新拖入。
		// 新加字段时需要往  sub表，属性表中加记录
		logs('C:'.__CLASS__.' L:'.__LINE__.'拖动页面时时保存数据源：'.$this->pw_var_export($temp),date('Y-m-d' , time()).'_data.log');
		$transeModel = new Model();
		$transeModel->startTrans();
		try{
			if(is_array($temp)){
				if($temp['id']){
					// 现有组件属性修改
					$obj = M('mis_dynamic_form_propery');
					$proresult=$obj->save($temp);
					if($proresult==false){
						$msg = "修改属性失败！".$obj->getlastSql();
						throw new NullDataExcetion($msg);
					}
					$this->modifyPropertyDataByForeignField($temp,$temp['id'],$_POST['formid']);
					logs('C:'.__CLASS__.' L:'.__LINE__.'拖动页面时时保存:对现有组件的修改：'.$obj->getLastSql());
					$subModel=M("mis_dynamic_database_sub");
					$subresult=$subModel->where("id={$temp['ids']}")->setField("title",$temp['title']);
					if($subresult==false){
						$msg = "修改sub标题失败！".$subModel->getlastSql();
						throw new NullDataExcetion($msg);
					}
					$temp['id']=$temp['ids'];
					$this->modifySubByForeignField(array($temp),$_POST['formid']);
				}else{
					$MisDynamicDatabaseMasDao=M("mis_dynamic_database_mas");
					$MisDynamicDatabaseSubDao=M("mis_dynamic_database_sub");
					$curnode = $_POST['curnode'];
					$formid = $_POST['formid'];
					// 新增 组件 记录
					// 取出组件 属性文件
					$iscreate = $this->controlConfig[$temp['category']]['iscreate'];
					
					// 查看有不有表名 dbname
					$tablename = $temp['dbname'];
					if($iscreate && (!$tablename || !$formid || !$curnode)){
						$msg = "表名为空，已移除组件 {$temp[$this->privateProperty[$temp['category']]['fields']['dbfield']]}，请重新添加。";
						if(true === $this->isforeignfield){
							$msg = "复用表单时没有属于自己的数据表，或表名为空，已移除组件 {$temp[$this->privateProperty[$temp['category']]['fields']['dbfield']]}";
						}
						$error=array(
								'code'=>'0',
								'msg'=>$msg,
								'field'=>$temp[$this->privateProperty[$temp['category']]['fields']['dbfield']],
								'type'=>$this->isforeignfield
						);
						echo json_encode($error);
						exit;
					}
					// 添加到表字段记录中
					// 得到表id
					$tableid = getFieldBy($tablename, 'tablename', 'id', 'mis_dynamic_database_mas','formid',$formid);
					$tablesubData=array(
							'masid'=>$tableid,
							'field'=>$temp['fieldname'],
							'title'=>$temp['title'],
							'type'=>strtolower($temp['fieldtype']),
							'length'=>$temp['tablelength'],
							'category'=>$temp['category'],
							'weight'=>$temp['sort'],
							'sort'=>$temp['sort'],
							'isshow'=>1,
							'islock'=>1,
							'isdatasouce'=>0,
							'status'=>1,
							'formid'=>$formid,
							'titlepercent'=>$temp['titlepercent'],
							'contentpercent'=>$temp['contentpercent'],
					);
					$subid = 0;
					if($iscreate){
						//查询该组件 字段记录表是否已有记录
						$misDynamicDatabaseSubObj=M('mis_dynamic_database_sub');
						$subMap=array();
						$subMap['formid']=$formid;
						$subMap['status']=1;
						$subMap['category']=$temp['category'];
						$subMap['field']=$temp['fieldname'];
						$randStr = rand_string(3,0);
						$subVo=$misDynamicDatabaseSubObj->where($subMap)->find();
						if($subVo){//已存在该字段
							$error=array(
									'code'=>'0',
									'msg'=>"已存在[ {$temp['fieldname']} ]字段记录,请更换名称。"
							);
							echo json_encode($error);
							exit;
						}
						// 将字段 信息记入字段记录表中
						$subid = $misDynamicDatabaseSubObj->add($tablesubData);
						$addsubData[0]=$tablesubData;
						$this->modifySubByForeignField($addsubData, $formid,$subid);
						//插入当前数据库表字段
						$temp['fieldtype']=strtolower($temp['fieldtype'])=="date"?"int":strtolower($temp['fieldtype']);
						$sql=" ALTER TABLE ".$tablename." ADD  COLUMN ".$temp['fieldname']." ".strtolower($temp['fieldtype'])."(".$temp['tablelength'].")   COMMENT '{$temp['title']}'";
						$misDynamicDatabaseSubObj->query($sql);
						logs('C:'.__CLASS__.' L:'.__LINE__.'拖动页面时时保存:对表进行字段追加：'.$misDynamicDatabaseSubObj->getLastSql());
						// 添加组件信息记录
						$temp['ids']=$subid;
					}
					$temp['formid']=$formid;
					$temp['tplid'] = getFieldBy($curnode, 'tplname', 'id', 'mis_dynamic_form_template','formid',$formid);
					if($temp['category']=="datatable"){ //内嵌表格添加表名 模型名称
						//构建表名
						$temp['datatablemodel']=createRealModelName($tablename."_sub_".$temp['fieldname']);
						$temp['datetablename']=$tablename."_sub_".$temp['fieldname'];
					}
					$misDynamicFormProperyOPbj = M('mis_dynamic_form_propery');
					
					$temp = $this->propertyReplace($temp, $temp['category']);
					
					$propertyid = $misDynamicFormProperyOPbj->add($temp);
					logs('C:'.__CLASS__.' L:'.__LINE__.'拖动页面时时保存:直接入属性库的组件 ：'.$misDynamicFormProperyOPbj->getLastSql());
				}
			}
			$transeModel->commit();
			$error=array(
					'code'=>'1',
					'msg'=>'添加成功。',
					'id'=>$propertyid,
					'ids'=>$subid
			);
			echo json_encode($error);
		}catch (Exception $e){
			$transeModel->rollback();
			$error=array(
					'code'=>'0',
					'msg'=>'操作失败！'.$e->__toString(),
			);
			echo json_encode($error);
		}
	}
	/**
	 * 
	 * @Title: createCoverForm
	 * @Description: todo(生成套表模式的主表代码) 
	 * @param string $formName	主表的表单Action名  
	 * @author renling 
	 * @date 2014年12月18日 上午12:02:41 
	 * @throws
	 */
	public function createCoverForm($formName){
		if(!$formName){
			$this->error('请传入套表的主表Action');
		}
		// 获取主表的formid
		$formManagObj = M('MisDynamicFormManage');
		$formGetFormIdMap['actionname']= $formName;
		$formGetFormIdMap['status'] = 1;
		// 	formId 表单编号
		$formInfoData = $formManagObj->where($formGetFormIdMap)->getField('id,actiontitle');
		// $formid = getFieldBy($formName, "actionname", "id", "mis_dynamic_form_manage",'status',1);
		if(!is_array($formInfoData)){
			logs('行：['.__LINE__.']文件:['.__CLASS__.']套表生成-获取表单信息失败。'.$formManagObj->getLastSql());
			$this->error('表单信息获取失败！请查证');
		}
		$formid = key($formInfoData);
		$actionTitle = reset($formInfoData);
		// 获取表单的组件数据
		$formControllData = $this->getfieldCategory($formid);
		//生成套表新增页面
		$coverFormViewHtml = $this->createCoverFormAddandEdit($formControllData['index'] , $formName , $actionTitle,0);
		$coverFromPath = TMPL_PATH.C('DEFAULT_THEME')."/".$formName."/add.html";
		if(!is_dir(dirname($coverFromPath))) mk_dir(dirname($coverFromPath),0777);
		if( false === file_put_contents( $coverFromPath , $coverFormViewHtml )){
			$this->error ( "add.html生成失败");
		}
		//生成套表修改页面
		$coverFormViewHtml = $this->createCoverFormAddandEdit($formControllData['index'] , $formName , $actionTitle,1);
		$coverFromPath = TMPL_PATH.C('DEFAULT_THEME')."/".$formName."/edit.html";
		if(!is_dir(dirname($coverFromPath))) mk_dir(dirname($coverFromPath),0777);
		if( false === file_put_contents( $coverFromPath , $coverFormViewHtml )){
			$this->error ( "edit.html生成失败");
		}
		
		//生成套表查看页面
		$coverFormViewHtml = $this->createCoverFormView($formControllData['index'] , $formName , $actionTitle);
		$coverFromPath = TMPL_PATH.C('DEFAULT_THEME')."/".$formName."/view.html";
		if(!is_dir(dirname($coverFromPath))) mk_dir(dirname($coverFromPath),0755);
		if( false === file_put_contents( $coverFromPath , $coverFormViewHtml )){
			$this->error ( "view.html生成失败");
		}
		
		
		//contenthtml.html
		$flowHtml ="";
		$infoPersonHtml = "";
		$buttonHtml ="\r\n\t\t{:W('ShowAction')}";
		if($this->isaudit){
			$flowHtml ="\r\n\t\t<div class=\"showFormFlow\">{:W('ShowFormFlowView',\$vo)}</div>";
			$infoPersonHtml = "\r\n\t\t{:W('ShowNotifyView',\$vo)}";
		}
		$dwzContentHtml = $this->getDynamicContentHtml($formControllData['index'],true,true,-1,$flowHtml,$infoPersonHtml,$buttonHtml,2);
		$file = TMPL_PATH.C('DEFAULT_THEME')."/".$formName."/contenthtml.html";
		if(!is_dir(dirname($file))) mk_dir(dirname($file),0777);
		if( false === file_put_contents( $file , $dwzContentHtml )){
			$this->error ( "contenthtml.html生成失败 ");
		}
		
		$this->creteCoverFormExtendAction($formName);
// 		$str="<!--";
// 		$str.="\r\n * @Title: 套表修改模板";
// 		$str.="\r\n * @Package package_name";
// 		$str.="\r\n * @Description: todo(动态表单_套表修改模板)";
// 		$str.="\r\n * @author ".$_SESSION['loginUserName'];
// 		$str.="\r\n * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d";
// 		$str.="\r\n * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d";
// 		$str.="\r\n * @date ".date('Y-m-d H:i:s');
// 		$str.="\r\n * @version V1.0";
// 		$str.="\r\n -->";
// 		if(!is_writeable($coverFromPath)){
// 			chmod($coverFromPath,0755);
// 		}
// 		$ret = file_put_contents($coverFromPath, $str.$coverFormViewHtml) ;
// 		if(!$ret){
// 			logs('套表viwew页面生成失败===目录不存在请重新生成目录');
// 			$this->error("目录不存在请重新生成目录：{$coverFromPath}");
// 		}
		
		$this->success('套表关系建立完成');
	}
	//当前Action名字 $this->nodeName
	//主表名$this->tableName
	public  function lookupdelRec(){
		$id=$_POST['id'];
		$MisDynamicControllRecordDao=M("mis_dynamic_controll_record");
		$result=$MisDynamicControllRecordDao->where("id=".$id)->delete();
		$MisDynamicControllRecordDao->commit();
		echo $result;
	}
	/**
	 * @Title: delzujian
	 * @Description: todo(这里用一句话描述这个方法的作用)
	 * @author 谢友志
	 * @date 2014-12-24 下午5:37:39
	 * @throws
	 */
	public function delzujian(){
		$data = $_POST;
		//查询该字段是否已存在真实表中
		$DynamicconfModel=D("Dynamicconf");
		$result=$DynamicconfModel->getTableInfo($data['tablename'],$data['zujianname']);
		$subtable = $data['tablename'].'_sub_'.$data['zujianname'];
		if($data['catalog']=="datatable"){
			//删除真实表
			M()->query("drop table ".$subtable);
			//删除mis_dynamic_form_datatable表中包含该组件记录
			$datamodel = M("mis_dynamic_form_datatable");
			$datamodel->where("propertyid=".$data['id'])->delete();
			//删除内嵌表model物理文件 
			$subtablearr = explode("_",$subtable);
			$filename0 = '';
			foreach($subtablearr as $k=>$v){
				$filename0 .= ucfirst($v);
			}
			$filename .=$filename0."Model.class.php";
			$path = dirname(dirname(__FILE__))."/Model";
			$file = $path.'/'.$filename;
			if(is_file($file)){
				unlink($file);
			}
			//删除配置文件
			$dirname = getFieldBy(getFieldBy($data['id'],'id','formid','mis_dynamic_form_propery'),'id','actionname','mis_dynamic_form_manage');
			$detailfile = DConfig_PATH."/Models/".$dirname."/".$filename0.".inc.php";
			unlink($detailfile);
		}
		//删除mis_dynamic_form_propery表中该组件信息
		$model=M('mis_dynamic_form_propery');
		$map=array();
		$map['_string']="id={$data['id']}";
		$model->where($map)->delete();
		M()->commit();
		
	}
	/**
	 *
	 * @Title: datatabledatasouceset
	 * @Description: todo(内嵌表格配置页面)
	 * @author renling
	 * @date 2015年1月8日 下午4:49:04
	 * @throws
	 */
	public function datatabledatasouceset(){
		//读取lookup配置文件
		$model=D('LookupObj');
		$selectlist = $model->GetLookupDetails();
		$MisDynamicFormProperyDao=M("mis_dynamic_form_propery");
		//强制子表显示id项
		$unshiftid['id'] = array(
				'title' => 'ID',
				'category' => 'text',
				'length' => '10',
		);
		foreach($selectlist as $key=>$val){
			if($val['dt']){
				$dt = $val['dt'];
				foreach($dt as $dk=>$dv){
					if(!$dv['list']['id']){
						$selectlist[$key]['dt'][$dk]['list'] = array_merge($unshiftid,$dt[$dk]['list']) ;
					}
				}
			}
		}
// 		if(file_exists($model->GetFile())){
// 			$selectlist = require $model->GetFile();
// 		}
		$jsonlist=array();
		if($_REQUEST['dtstep']==1){//Ajax请求获取内嵌表格列表
			//获取选择的lookup
			$lookupval=$_REQUEST['lookupval'];
			//查找带回单个list			
//			$lookuplist=array_keys($selectlist[$lookupval]['dt']);
// 			if($lookuplist[0]){
// 				$jsonlist['dtname']=$lookuplist[0];
// 				$jsonlist['dttitle']=$selectlist[$lookupval]['dt'][$lookuplist[0]]['title'];
// 			}
			//单个该多个 --开始---2015-09-19 by xyz
			$lookuplist=$selectlist[$lookupval]['dt'];
			foreach($lookuplist as $lk=>$lv){
				$jsonlist[$lk]['dtname'] = $lk;
				$jsonlist[$lk]['dttitle'] = $lv['title'];
			}
			//单个该多个 --结束--- 2015-09-19 by xyz
			echo json_encode($jsonlist);
		}else if($_REQUEST['dtstep']==2){//Ajax获取lookup内嵌表格带回字段
			//获取lookup配置项
			$lookupval=$_REQUEST['lookupval'];
			//获取选择的内嵌表格
			$lookupdt=$_REQUEST['lookupdt'];
			//获取内嵌表格数据
			$jsonlist=$selectlist[$lookupval]['dt'][$lookupdt];
			echo json_encode($jsonlist);
		}else if($_REQUEST['dtstep']==3){//请求保存数据
			$fieldname=$_POST['fieldname'];
			$items=$_POST['items'];
			$dtname=$_POST['dtname'];
			$fdlist=array();
			foreach ($fieldname as $fkey=>$fval){
				if($items[$fkey]){
					$fdlist[$fval]=$items[$fkey];
				}
			}
			$data=array();
			$data[$dtname]['list']=$fdlist;
			$lookupcom=explode(',', $_POST['lookupcom']);
			$lookupcomnew=array();
			$lookupcomname=array();
			foreach ($lookupcom as $lkey=>$lval){
				if($lval){
					$lookupcomnew[]=$lval;
				}
			}
			$data[$dtname]['lookupcom']=implode(',', $lookupcomnew);
			$data[$dtname]['lookupval']=$_POST['lookupobj'];
			//修改当前组件id的fieldrelation
			$fieldrelation=base64_encode(serialize($data));
			//查询lookupgrouporg
			$proMap=array();
			$formid=$_REQUEST['formid'];
			$proMap['formid']=$formid;
			$proMap['category']="lookup";
			$proMap['lookupchoice']=$_POST['lookupobj'];
			$MisDynamicFormProperyVo=$MisDynamicFormProperyDao->where($proMap)->find();
			$MisDynamicFormProperyDao->where("id=".$_POST['propertyid'])->setField("fieldrelation",$fieldrelation);
			$MisDynamicFormIndatatableDao=M("mis_dynamic_form_indatatable");
			//删除原有配置
			$MisDynamicFormIndatatableDao->where("proid=".$_POST['propertyid'])->delete();
			$lookupcomnameArr=implode(',', $lookupcomnew);
			$MisDynamicDatabaseMasDao=M("mis_dynamic_database_mas");
			$masMap=array();
			$masMap['formid']=$formid;
			$masMap['isprimary']=1;
			$MisDynamicDatabaseMasVo=$MisDynamicDatabaseMasDao->where($masMap)->find();
			//查询当前主表名称
			$formtablename=$MisDynamicDatabaseMasVo['tablename'];
			$check=$_POST['check'];
			foreach ($fdlist as $fdkey=>$fdval){
				$data=array();
				$data['dataname']=$formtablename."_sub_".$check;
				$data['datafieldname']=$fdkey;
				$data['datainname']=$dtname;
				$data['datainfieldname']=$fdval;
				$data['datalookuporg']=$MisDynamicFormProperyVo['lookupgrouporg'];
				$data['datalookcom']=$lookupcomnameArr;
				$data['proid']=$_POST['propertyid'];
				$fidresult=$MisDynamicFormIndatatableDao->add($data);
				$MisDynamicFormIndatatableDao->commit();
				if($fidresult==false){
					$this->error("数据错误!!");
				}
			}
			$resutl=$MisDynamicFormProperyDao->commit();
			$jsondata=array();
			$jsondata['fieldrelation']=$fieldrelation;
			$jsondata['org']=$MisDynamicFormProperyVo['lookupgrouporg'];
			if($resutl!==false){
				$this->success("操作成功！",'',json_encode($jsondata));
			}else{
				$this->error("操作失败！");
			}
				
		}else{
			$misDynamicFormDatatableDao=M("mis_dynamic_form_datatable");
			//查询当前内嵌表格字段信息
			$propertyid=$_REQUEST['propertyid'];//组件id
			//获取当前内嵌表是否已配置
			$misDynamicFormDatatableVo=$MisDynamicFormProperyDao->where("id=".$propertyid)->find();
			$datalist=unserialize(base64_decode($misDynamicFormDatatableVo['fieldrelation']));
			//获取当前内嵌表格名称
			$dataname=array_keys($datalist);
			$lookupval=$datalist[$dataname[0]]['lookupval'];
// 			//获取当前lookup设置的内嵌表格
// 			$lookuplist=array_keys($selectlist[$lookupval]['dt']);
// 			if($lookuplist[0]){
// 				$dtlist[]=array(
// 						'dtname'=>$lookuplist[0],
// 						'dttitle'=>$selectlist[$lookupval]['dt'][$lookuplist[0]]['title'],
// 				);
// 			}
			//单个该多个 --开始---2015-09-19 by xyz
			$lookuplist=$selectlist[$lookupval]['dt'];
			foreach($lookuplist as $lk=>$lv){
				$dtlist[$lk]['dtname'] = $lk;
				$dtlist[$lk]['dttitle'] = $lv['title'];
			}
			//单个该多个 --结束--- 2015-09-19 by xyz
			//查询此内嵌表格字段
			$dtfieldlist=$selectlist[$lookupval]['dt'][$dataname[0]];
			$this->assign("dtfieldlist",$dtfieldlist);
			$this->assign("datalist",$datalist);
			$this->assign("dtlist",$dtlist);
			$this->assign("lookupval",$lookupval);
			$this->assign("dtlist",$dtlist);
			$this->assign("dtname",$dataname[0]);
			$this->assign("propertyid",$_REQUEST['propertyid']);
			$misDynamicFormDatatableList=$misDynamicFormDatatableDao->where("propertyid=".$propertyid)->getField("fieldname,fieldtitle");
			$this->assign("misDynamicFormDatatableList",$misDynamicFormDatatableList);
			$formid=$_REQUEST['formid'];
			//查询lookup信息
			$list = $this->lookuoconfig(false);
			
			//查询当前表单包含的lookup
			$sql="SELECT   lookupchoice  FROM  `mis_dynamic_form_propery`  WHERE formid =".$formid."   AND ( `category` = 'lookup'   OR `category` = 'lookupsuper'  )   ";
			$lookuplist=$misDynamicFormDatatableDao->query($sql);
			$newlist=array();
			foreach ($lookuplist as $lkey=>$lval){
				if($list[$lval['lookupchoice']]&&(getFieldBy($lval['lookupchoice'], "id", "proid", "mis_system_lookupobj")||getFieldBy($lval['lookupchoice'], "id", "dtinfo", "mis_system_lookupobj"))){
					$newlist[$lval['lookupchoice']]=$list[$lval['lookupchoice']];
				}
			}
			//logs(var_dump($newlist),"sdt");
			//查询当前内嵌表格
			$this->assign("propertyid",$propertyid);
			$this->assign("formid",$formid);
			$this->assign('lookuplist',$newlist);
			$this->assign("check",$_REQUEST['check']);
			$this->display();
		}
	}
	/**
	 * @Title: getBaseUnit
	 * @Description: todo(获取基础单位)   
	 * @author quqiang 
	 * @date 2015年1月9日 下午3:30:22 
	 * @throws
	 */
	public function getBaseUnit(){
		$data = getBaseUnit();
		
		$arr=array();
		if ($data) {
			foreach($data as $k=>$v){
				$title=$v['danweimingchen'];
				$arr[$v['danweidaima']] = $title;
			}
		}
		echo json_encode($arr);
	}
	/**
	 * @Title: getSubUnit
	 * @Description: todo(获取基础单位 允许的转换单位)   
	 * @author quqiang 
	 * @date 2015年1月9日 下午3:30:43 
	 * @throws
	 */
	public function getSubUnit(){
		$param = $_REQUEST['table'];
		$data = getSubUnit($param);
		$arr=array();
		if ($data) {
			foreach($data as $k=>$v){
				$title=$v['danweimingchen'];
				$arr[$v['danweidaima']] = $title;
			}
		}
		echo json_encode($arr);
	}
	/**
	 * 
	 * @Title: additionalconditions
	 * @Description: todo(设置附加条件)   
	 * @author renling 
	 * @date 2015年1月14日 下午3:43:06 
	 * @throws
	 */
	public function additionalconditions(){
		
		if(!$_POST){
			$property = $this->privateProperty['lookup']['additionalconditions'];
			// lookup key值的存储字段名
			$lookupchoice = $this->privateProperty['lookup']['lookupchoice']['dbfield'];
			
			//查询当前表单组件
			$formid=$_REQUEST['formid'];
			$MisDynamicFormProperyDao=M("mis_dynamic_form_propery");
			//获得当前表单所有组件
			
			// 构造查询条件 ， 允许组件类型列表，没有就不管
			$controllMap['formid'] = $formid;
			if( !empty( $property['allowcontroll'] ) ){
				$controllMap[$this->publicProperty['catalog']['dbfield']] =array ( 'in' , $property['allowcontroll'] );
			}
			$selfField = $_GET['name'];
			// 构建不可使用的字段过滤条件
			if( !empty( $property['notallowfiled'] ) ){
				if(!$selfField){
					$selfField = '';
				}
				// 转换当前字段的标识。
				$notallowfiled = str_replace('#self#', $selfField, $property['notallowfiled']);
				$controllMap[$this->publicProperty['fields']['dbfield']] =array ( 'not in' , $notallowfiled );
			}
			// 构造查询条件 end
			$MisDynamicFormProperyList=$MisDynamicFormProperyDao->where($controllMap)->field("id,fieldname,title")->select();
			// 获取当前llokup组件的lookup key 并通过key 获取到lookup的带回字段。 
			$propertyID= $_GET['propertyid'];
			// lookup 配置文件内容
			$lookupConfig = '';
			if( $propertyID && $lookupchoice ){
				$filedCallBackList[]=$lookupchoice;
				$filedCallBackList[]='additionalconditions';
				
				$propertyModel = M('mis_dynamic_form_propery');
				$findLookupKeyMap['id'] = $propertyID;
				$data = $propertyModel->where($findLookupKeyMap)->field(join(',', $filedCallBackList))->find();
				// 读取lookup配置文件
				if($data['lookupchoice']){
						$lookupGetDataObj = D('LookupObj');
						$lookupData = $lookupGetDataObj -> GetLookupDetail($data['lookupchoice']);
						if(empty($lookupData)){
							logs('lookup【'.$data['lookupchoice'].'】 不存在配置文件。');
						}else{
							$showField = $lookupData['fields']; // 带回字段
							$chinasess = $lookupData['fields_china']; // 带回字段
							$showFieldArr  = explode(',', $showField);
							foreach ($showFieldArr as $key=>$val){
								$title = $chinasess[$val] ? $chinasess[$val] : $val ;
								$lookupConfig[$val] = $val.'【'.$title.'】';
							}
						}
				}
			}
			$additional = $data['additionalconditions'] ? unserialize(base64_decode($data['additionalconditions'])) : array();
		
		
		$additional['proexp'] = $additional['proexp'] ? unserialize($additional['proexp']) : '';
		$additional['sysexp'] = $additional['sysexp'] ? unserialize($additional['sysexp']) : '';
		
			$this->assign('additional',$additional);
			$this->assign('lookupConfig',$lookupConfig);
			//查询系统字段
			$this->assign("MisDynamicFormProperyList",$MisDynamicFormProperyList);
			$this->assign("SystemFieldList",$this->systefield);
			$this->display();
		}else{
			/**
			 * 两种操作方式：
			 * 		1.lookup对应字段重复定义了 关联时 只保留最后一个有效项
			 * 		2.lookup对应字段重复定义了 关联时 只取最后一个对应项不对应关联，其它的默认为当前字段名。
			 */
			// 对应项重复时的控制阀 
			$relationType = 2;
			
			// 对应关联项数据
			$relationFiled = $_POST['relation'];
			// 被对应项数据
			$proexpField=$_POST['proexp'];
			switch ($relationType){
				case 1:
					// 重复项记录数组， 记录的是所有对应项 存在值的 页面 key 数组。是否重复要看key 的数量，
					$repeatArr = array();
					foreach ( $relationFiled  as $key => $val ){
						if($val){
							$repeatArr[$val][]	=	$key;
						}
					}
					/* 1.lookup对应字段重复定义了 关联时 只保留最后一个有效项 */
					// 去除 关联字段的 重复项
// 					$relationFiled = array_flip($relationFiled);
// 					unset($relationFiled['']);
// 					$relationFiled = array_flip($relationFiled);
					// 去除 关联字段的 重复项 end
					/*	********************************************* */
					$proexpArr = array();
					$relationArr=array();
					// 遍历当前页面组件字段，重组映射。当未设置映射时，映射值为本身，有重复映射时只取最后一个映射配置。 
					foreach ($proexpField as $k=>$v){
						// 处理重复映射
						if( $relationFiled && $relationFiled[$k] ) {
							$repeatKey = $repeatArr[$relationFiled[$k]] ;
							
							if( $k == $repeatKey[ count($repeatKey)-1 ] ) {
								$proexpArr[$v] = $relationFiled[ $k ];
							}
// else{
// 								$proexpArr[$v] = -1;
// 							}
						} else {
							// 不存在映射选择
							$proexpArr[$v] = -1;
						}
					}
					break;
				case 2:
					// 重复项记录数组， 记录的是所有对应项 存在值的 页面 key 数组。是否重复要看key 的数量，
					$repeatArr = array();
					foreach ( $relationFiled  as $key => $val ){
						if($val){
							$repeatArr[$val][]	=	$key;
						}
					}
					/* 2.lookup对应字段重复定义了 关联时 只取最后一个对应项不对应关联，其它的默认为当前字段名。 */
					$proexpArr = array();
					$relationArr=array();
					// 遍历当前页面组件字段，重组映射。当未设置映射时，映射值为本身，有重复映射时最后一个为映射配置，其它项为 映射本身值。
					foreach ($proexpField as $k=>$v){
						// 处理重复映射
						if( $relationFiled && $relationFiled[$k] ) {
							$repeatKey = $repeatArr[$relationFiled[$k]] ;
								
							if( $k == $repeatKey[ count($repeatKey)-1 ] ) {
								$proexpArr[$v] = $relationFiled[ $k ];
							} else {
								// 重复映射时 非最后一个映射本身值。
								$proexpArr[$v] = -1;
							}
						} else {
							// 不存在映射选择
							$proexpArr[$v] = -1;
						}
					}
					break;
			}
			
			$sysexpArr = '';
			if( $_POST['sysexp'] ){
				foreach ( $_POST['sysexp'] as $k => $v){
					$sysexpArr[$v] = $v;
				}
			}
			
		//print_r(pwdHash($_POST['proexp']));exit;
		    $proexp = serialize($proexpArr) ;//implode(',', $_POST['proexp']);
		    $sysexp= is_array($sysexpArr) ? serialize($sysexpArr) : '' ;
		    $data['proexp'] = $proexp;
		    $data['sysexp'] = $sysexp;
			$this->success("操作成功!",'',base64_encode(serialize($data)));
		}
	}

	/**
	 * 文本框固定显示值附加条件设置管理
	 * @Title: textadditionalconditions
	 * @Description: todo(文本框固定显示值附加条件设置管理)   
	 * @author quqiang 
	 * @date 2015年4月27日 下午2:26:03 
	 * @throws
	 */
	public function textadditionalconditions(){
		try{
			if(!$_POST){
				$property = $this->privateProperty['text']['additionalconditions'];
				// 固定显示-来源表
				$soucetable = $this->privateProperty['text']['subimporttableobj']['dbfield'];
				// 固定显示-来源表中的字段
				$souceshowfield = $this->privateProperty['text']['subimporttablefieldobj']['dbfield'];
				// 固定显示-是否时时获取数据
				$souceisreflush = $this->privateProperty['text']['subimporttablefield2obj']['dbfield'];
				// 固定显示-过滤条件
				$soucecondition = $this->privateProperty['text']['conditions']['dbfield'];
				// 当前属性ID
				$propertyID= $_GET['propertyid'];
				if( empty($propertyID) || !$propertyID ){
					$msg = '当前属性未知!';
					throw new NullDataExcetion($msg);
				}
				if(empty($soucetable) || empty($souceshowfield)){
					$msg = '设置固定显示文本附加条件前，需要先保存来源信息数据！！';
					throw new NullDataExcetion($msg);
				}
				//查询当前表单组件
				$formid=$_REQUEST['formid'];
				$MisDynamicFormProperyDao=M("mis_dynamic_form_propery");
				//获得当前表单所有组件
					
				// 构造查询条件 ， 允许组件类型列表，没有就不管
				$controllMap['formid'] = $formid;
				if( !empty( $property['allowcontroll'] ) ){
					$controllMap[$this->publicProperty['catalog']['dbfield']] =array ( 'in' , $property['allowcontroll'] );
				}
				$selfField = $_GET['name'];
				// 构建不可使用的字段过滤条件
				if( !empty( $property['notallowfiled'] ) ){
					if(!$selfField){
						$selfField = '';
					}
					// 转换当前字段的标识。
					$notallowfiled = str_replace('#self#', $selfField, $property['notallowfiled']);
					$controllMap[$this->publicProperty['fields']['dbfield']] =array ( 'not in' , $notallowfiled );
				}
				// 构造查询条件 end
				$MisDynamicFormProperyList=$MisDynamicFormProperyDao->where($controllMap)->field("id,fieldname,title")->select();
				// 获取当前llokup组件的lookup key 并通过key 获取到lookup的带回字段。
				
				// 现有文本附加条件设置内容
				$filedCallBackList[]=$property['dbfield'];
				$filedCallBackList[]=$soucetable;
				$filedCallBackList[]=$souceshowfield;
				$filedCallBackList[]=$souceisreflush;
				$filedCallBackList[]=$soucecondition;
				
				$propertyModel = M('mis_dynamic_form_propery');
				$findAppendConditionMap['id'] = $propertyID;
				$data = $propertyModel->where($findAppendConditionMap)->field(join(',', $filedCallBackList))->find();
				//来源表来源字段必须有值才能进行设置
				foreach ($data as $key => $val){
				    if($key=='subimporttableobj'||$key=='subimporttablefieldobj'){
						if(empty($val)){
							$msg = '设置固定显示文本附加条件前，需要先保存来源信息数据！！';
							throw new NullDataExcetion($msg);
						}
					}
				}
				
				$tableinfo = $this->comboxgetTableField($data[$soucetable]);
				
				$additional = $data['additionalconditions'] ? unserialize(base64_decode($data['additionalconditions'])) : array();

				$additional['proexp'] = $additional['proexp'] ? unserialize($additional['proexp']) : '';
				$additional['sysexp'] = $additional['sysexp'] ? unserialize($additional['sysexp']) : '';
		
				$this->assign('additional',$additional);
				$this->assign('lookupConfig',$tableinfo);
				//查询系统字段
				$this->assign("MisDynamicFormProperyList",$MisDynamicFormProperyList);
				$this->assign("SystemFieldList",$this->systefield);
				$this->display();
			}else{
				/**
				 * 两种操作方式：
				 * 		1.lookup对应字段重复定义了 关联时 只保留最后一个有效项
				 * 		2.lookup对应字段重复定义了 关联时 只取最后一个对应项不对应关联，其它的默认为当前字段名。
				 */
				// 对应项重复时的控制阀
				$relationType = 2;
					
				// 对应关联项数据
				$relationFiled = $_POST['relation'];
				// 被对应项数据
				$proexpField=$_POST['proexp'];
				switch ($relationType){
					case 1:
						// 重复项记录数组， 记录的是所有对应项 存在值的 页面 key 数组。是否重复要看key 的数量，
						$repeatArr = array();
						foreach ( $relationFiled  as $key => $val ){
							if($val){
								$repeatArr[$val][]	=	$key;
							}
						}
						/* 1.lookup对应字段重复定义了 关联时 只保留最后一个有效项 */
						// 去除 关联字段的 重复项
						// 					$relationFiled = array_flip($relationFiled);
						// 					unset($relationFiled['']);
						// 					$relationFiled = array_flip($relationFiled);
						// 去除 关联字段的 重复项 end
						/*	********************************************* */
						$proexpArr = array();
						$relationArr=array();
						// 遍历当前页面组件字段，重组映射。当未设置映射时，映射值为本身，有重复映射时只取最后一个映射配置。
						foreach ($proexpField as $k=>$v){
							// 处理重复映射
							if( $relationFiled && $relationFiled[$k] ) {
								$repeatKey = $repeatArr[$relationFiled[$k]] ;
									
								if( $k == $repeatKey[ count($repeatKey)-1 ] ) {
									$proexpArr[$v] = $relationFiled[ $k ];
								}
								// else{
								// 								$proexpArr[$v] = -1;
								// 							}
							} else {
								// 不存在映射选择
								$proexpArr[$v] = -1;
							}
						}
						break;
					case 2:
						// 重复项记录数组， 记录的是所有对应项 存在值的 页面 key 数组。是否重复要看key 的数量，
						$repeatArr = array();
						foreach ( $relationFiled  as $key => $val ){
							if($val){
								$repeatArr[$val][]	=	$key;
							}
						}
						/* 2.lookup对应字段重复定义了 关联时 只取最后一个对应项不对应关联，其它的默认为当前字段名。 */
						$proexpArr = array();
						$relationArr=array();
						// 遍历当前页面组件字段，重组映射。当未设置映射时，映射值为本身，有重复映射时最后一个为映射配置，其它项为 映射本身值。
						foreach ($proexpField as $k=>$v){
							// 处理重复映射
							if( $relationFiled && $relationFiled[$k] ) {
								$repeatKey = $repeatArr[$relationFiled[$k]] ;
		
								if( $k == $repeatKey[ count($repeatKey)-1 ] ) {
									$proexpArr[$v] = $relationFiled[ $k ];
								} else {
									// 重复映射时 非最后一个映射本身值。
									$proexpArr[$v] = -1;
								}
							} else {
								// 不存在映射选择
								$proexpArr[$v] = -1;
							}
						}
						break;
				}
					
				$sysexpArr = '';
				if( $_POST['sysexp'] ){
					foreach ( $_POST['sysexp'] as $k => $v){
						$sysexpArr[$v] = $v;
					}
				}
					
				//print_r(pwdHash($_POST['proexp']));exit;
				$proexp = serialize($proexpArr) ;//implode(',', $_POST['proexp']);
				$sysexp= is_array($sysexpArr) ? serialize($sysexpArr) : '' ;
				$data['proexp'] = $proexp;
				$data['sysexp'] = $sysexp;
				$this->success("操作成功!",'',base64_encode(serialize($data)));
			}
		}catch (Exception $e){
			echo $e->getMessage();
			echo ('<pre>'.$e->__toString().'</pre>');
		}
	}
	
	/**
	 * 自定义组件内容维护
	 * @Title: componentContentEdit
	 * @Description: todo(这里用一句话描述这个方法的作用)   
	 * @author quqiang 
	 * @date 2016年1月9日 下午2:30:39 
	 * @throws
	 */
	function componentContentEdit(){
		try {
			if(!$_POST){
				$propertyID= $_GET['propertyid'];
				if( empty($propertyID) || !$propertyID ){
					$msg = '当前属性未知!';
					throw new NullDataExcetion($msg);
				}
				$propertyModel = M('mis_dynamic_form_propery');
				$findAppendConditionMap['id'] = $propertyID;
				$data = $propertyModel->where($findAppendConditionMap)->field('component')->find();
				$this->assign('content',$data['component']);
				$this->display();
			}else{
				$content = $_POST['component'];
				$this->success("操作成功!",'',$content);
			}
		} catch (Exception $e) {
			echo $e->getMessage();
			echo ('<pre>'.$e->__toString().'</pre>');
		}
	}
	
	/**
	 * 
	 * @Title: additionaldateconditions
	 * @Description: todo(数据表格设置附加条件)   
	 * @author 任岭 
	 * @date 2015年3月24日 上午6:26:32 
	 * @throws
	 */
	function additionaldateconditions(){
		if(!$_POST['issave']){
			$property = $this->privateProperty['lookup']['additionalconditions'];
			// lookup key值的存储字段名
			$lookupchoice = $this->privateProperty['lookup']['lookupchoice']['dbfield'];
			//查询当前表单组件
			$formid=$_REQUEST['formid'];
			$MisDynamicFormProperyDao=M("mis_dynamic_form_propery");
			//获得当前表单所有组件
				
			// 构造查询条件 ， 允许组件类型列表，没有就不管
			$controllMap['formid'] = $formid;
			if( !empty( $property['allowcontroll'] ) ){
				$controllMap[$this->publicProperty['catalog']['dbfield']] =array ( 'in' , $property['allowcontroll'] );
			}
			$selfField = $_GET['name'];
			// 构建不可使用的字段过滤条件
			if( !empty( $property['notallowfiled'] ) ){
				if(!$selfField){
					$selfField = '';
				}
				// 转换当前字段的标识。
				$notallowfiled = str_replace('#self#', $selfField, $property['notallowfiled']);
				$controllMap[$this->publicProperty['fields']['dbfield']] =array ( 'not in' , $notallowfiled );
			}
			// 构造查询条件 end
			$MisDynamicFormProperyList=$MisDynamicFormProperyDao->where($controllMap)->field("id,fieldname,title")->select();
			// 获取当前llokup组件的lookup key 并通过key 获取到lookup的带回字段。
			//$propertyID= $_GET['propertyid'];
			// lookup 配置文件内容
			$lookupConfig = '';
			if($_REQUEST['lokkupchoice'] ){
				$filedCallBackList[]=$lookupchoice;
				$filedCallBackList[]='additionalconditions';
			
				$propertyModel = M('mis_dynamic_form_propery');
				$findLookupKeyMap['id'] = $propertyID;
				//$data = $propertyModel->where($findLookupKeyMap)->field(join(',', $filedCallBackList))->find();
				$data['lookupchoice']=$_REQUEST['lokkupchoice'];
				// 读取lookup配置文件
				if($data['lookupchoice']){
					$lookupGetDataObj = D('LookupObj');
					$lookupData = $lookupGetDataObj -> GetLookupDetail($data['lookupchoice']);
					if(empty($lookupData)){
						logs('lookup【'.$data['lookupchoice'].'】 不存在配置文件。');
					}else{
						$showField = $lookupData['fields']; // 带回字段
						$chinasess = $lookupData['fields_china']; // 带回字段
						$showFieldArr  = explode(',', $showField);
						foreach ($showFieldArr as $key=>$val){
							$title = $chinasess[$val] ? $chinasess[$val] : $val ;
							$lookupConfig[$val] = $val.'【'.$title.'】';
						}
					}
				}
			}
			$additional = $_POST['dateconditions'] ? unserialize(base64_decode($_POST['dateconditions'])) : array();
			
			
			$additional['proexp'] = $additional['proexp'] ? unserialize($additional['proexp']) : '';
			$additional['sysexp'] = $additional['sysexp'] ? unserialize($additional['sysexp']) : '';
			
			$this->assign('additional',$additional);
			$this->assign('lookupConfig',$lookupConfig);
			//查询系统字段
			$this->assign("MisDynamicFormProperyList",$MisDynamicFormProperyList);
			$this->assign("SystemFieldList",$this->systefield);
			$this->display();
		}else{
			//保存值
			/**
			 * 两种操作方式：
			 * 		1.lookup对应字段重复定义了 关联时 只保留最后一个有效项
			 * 		2.lookup对应字段重复定义了 关联时 只取最后一个对应项不对应关联，其它的默认为当前字段名。
			 */
			// 对应项重复时的控制阀
			$relationType = 2;
				
			// 对应关联项数据
			$relationFiled = $_POST['relation'];
			// 被对应项数据
			$proexpField=$_POST['proexp'];
			switch ($relationType){
				case 1:
					// 重复项记录数组， 记录的是所有对应项 存在值的 页面 key 数组。是否重复要看key 的数量，
					$repeatArr = array();
					foreach ( $relationFiled  as $key => $val ){
						if($val){
							$repeatArr[$val][]	=	$key;
						}
					}
					/* 1.lookup对应字段重复定义了 关联时 只保留最后一个有效项 */
					// 去除 关联字段的 重复项
					// 					$relationFiled = array_flip($relationFiled);
					// 					unset($relationFiled['']);
					// 					$relationFiled = array_flip($relationFiled);
					// 去除 关联字段的 重复项 end
					/*	********************************************* */
					$proexpArr = array();
					$relationArr=array();
					// 遍历当前页面组件字段，重组映射。当未设置映射时，映射值为本身，有重复映射时只取最后一个映射配置。
					foreach ($proexpField as $k=>$v){
						// 处理重复映射
						if( $relationFiled && $relationFiled[$k] ) {
							$repeatKey = $repeatArr[$relationFiled[$k]] ;
								
							if( $k == $repeatKey[ count($repeatKey)-1 ] ) {
								$proexpArr[$v] = $relationFiled[ $k ];
							}
							// else{
							// 								$proexpArr[$v] = -1;
							// 							}
						} else {
							// 不存在映射选择
							$proexpArr[$v] = -1;
						}
					}
					break;
				case 2:
					// 重复项记录数组， 记录的是所有对应项 存在值的 页面 key 数组。是否重复要看key 的数量，
					$repeatArr = array();
					foreach ( $relationFiled  as $key => $val ){
						if($val){
							$repeatArr[$val][]	=	$key;
						}
					}
					/* 2.lookup对应字段重复定义了 关联时 只取最后一个对应项不对应关联，其它的默认为当前字段名。 */
					$proexpArr = array();
					$relationArr=array();
					// 遍历当前页面组件字段，重组映射。当未设置映射时，映射值为本身，有重复映射时最后一个为映射配置，其它项为 映射本身值。
					foreach ($proexpField as $k=>$v){
						// 处理重复映射
						if( $relationFiled && $relationFiled[$k] ) {
							$repeatKey = $repeatArr[$relationFiled[$k]] ;
			
							if( $k == $repeatKey[ count($repeatKey)-1 ] ) {
								$proexpArr[$v] = $relationFiled[ $k ];
							} else {
								// 重复映射时 非最后一个映射本身值。
								$proexpArr[$v] = -1;
							}
						} else {
							// 不存在映射选择
							$proexpArr[$v] = -1;
						}
					}
					break;
			}
				
			$sysexpArr = '';
			if( $_POST['sysexp'] ){
				foreach ( $_POST['sysexp'] as $k => $v){
					$sysexpArr[$v] = $v;
				}
			}
// 			$nbmArr[]=$proexpArr;
// 			$nbmArr[]=$sysexpArr;
// 			print_r($nbmArr);
			//print_r(pwdHash($_POST['proexp']));exit;
			$proexp = serialize($proexpArr) ;//implode(',', $_POST['proexp']);
			$sysexp= is_array($sysexpArr) ? serialize($sysexpArr) : '' ;
			unset($data);
			$data['proexp'] = $proexp;
			$data['sysexp'] = $sysexp;
			$this->success("操作成功!",'',base64_encode(serialize($data)));
		}
	}
	/**
	 * @Title: calculatecontroll
	 * @Description: 组件计算设置   
	 * @author xiayq 
	 * @date 2015年1月14日 下午3:30:22 
	 * @throws
	 */
	function calculatecontroll(){
		//type 1:文本公式 2:两个日期差 3日期加 4 文本拼接 
		try {
		$controll=$_REQUEST['controll'];
		$conveneStatus=$_REQUEST['conveneStatus'];
		if($controll==1 && $conveneStatus==null){
			//查询是否存在
			$misDynamicCalculateModel=M('mis_dynamic_calculate');
			$formid=$_REQUEST['formid'];
			$propertyid=$_REQUEST['propertyid'];
			$type=$_REQUEST['type'];
			$jingdu=$_REQUEST['jingdu'];
			$tedapid=$_REQUEST['pid'];
			//去除空格
			if($type==4){
				$formula=$_REQUEST['formula'];
			}else{
				$formula=str_replace(' ','',$_REQUEST['formula']);
			}
			
			if($type==2){
				$pid=$tedapid['date'];
			}else{
				$pid=$tedapid['text'];
			}
			$map['formid']=$formid;
			$map['propertyid']=$propertyid;
			$listInfo=$misDynamicCalculateModel->where($map)->select();
			global $pidArr;
			$pidArr=$pid;
			//不存在添加
			if($listInfo==null){
				$data['formid']=$formid;
				$data['propertyid']=$propertyid;
				$data['formula']=$formula;
				$data['type']=$type;
				$data['jingdu']=$jingdu;
				$data['correspondfield']=json_encode($pid);
				$turnformula = preg_replace_callback('/([A-Z])+/','lookupControllCallBackFunc',$formula);
				//取出使用的组件
				preg_match_all('/([A-Z])+/i', $formula, $matches);
// 				$subArr = str_split($formula); // 旧代码。只满足公式中出现一位长度的字母情况
				$subArr=$matches[0];				
				$subly='';
				foreach ($subArr as $sk=>$sv){
					
					$is_preg=preg_match('/([A-Z])+$/',$sv);
					if($is_preg==1){
						$is_inArray=array_key_exists($sv, $pid);
						if($is_inArray==false){
							$this->error ( "公式中有不存在的字母");
						}
						$subVal=str_replace($sv,$pid[$sv],$sv);
						$subly.=$subly?','.$subVal:$subVal;
					}
				}
				
				//去重
				$subassemblyArr=array_unique(explode(',', $subly));
				$subassembly=implode(',', $subassemblyArr);
				$data['turnformula']=$turnformula;
				$data['subassembly']=$subassembly;
				$list=$misDynamicCalculateModel->add($data);
				if($list){
					$this->success(L('_SUCCESS_'),'',$turnformula);
				}else{
					$this->error ( L('_ERROR_') );
				}
			}else{
				$map['formid']=$formid;
				$map['propertyid']=$propertyid;
				$data['formula']=$formula;
				$data['type']=$type;
				$data['jingdu']=$jingdu;
				$data['correspondfield']=json_encode($pid);
				$turnformula = preg_replace_callback('/([A-Z])+/','lookupControllCallBackFunc',$formula);
				//取出使用的组件
				preg_match_all('/([A-Z])+/i', $formula, $matches);
// 				$subArr = str_split($formula); // 旧代码。只满足公式中出现一位长度的字母情况
				$subArr=$matches[0];
				$subly='';
				foreach ($subArr as $sk=>$sv){
					$is_preg=preg_match('/([A-Z])+$/',$sv);
					if($is_preg==1){
						$is_inArray=array_key_exists($sv, $pid);
						if($is_inArray==false){
							$this->error ( "公式中有不存在的字母");
						}
						$subVal=str_replace($sv,$pid[$sv],$sv);
						$subly.=$subly?','.$subVal:$subVal;
					}
				}
				//去重
				$subassemblyArr=array_unique(explode(',', $subly));
				$subassembly=implode(',', $subassemblyArr);
				$data['turnformula']=$turnformula;
				$data['subassembly']=$subassembly;
				$list=$misDynamicCalculateModel->where($map)->save($data);
				if($list){
					$this->success(L('_SUCCESS_'),'',$turnformula);
				}else{
					$this->error ( L('_ERROR_') );
				}
			}
		}elseif($conveneStatus==2){
			//删除公式
			$misDynamicCalculateModel=M('mis_dynamic_calculate');
			$map['formid']=$_REQUEST['formid'];
			$map['propertyid']=$_REQUEST['propertyid'];
			//查询模板id
			$list=$misDynamicCalculateModel->where($map)->delete();
			if($list){
				$this->success(L('_SUCCESS_'),'','');
			}else{
				$this->error ( L('_ERROR_') );
			}
		}else{
			$properyModel=M('mis_dynamic_form_propery');
			$tplModel=M('mis_dynamic_form_template');
			$misDynamicCalculateModel=M('mis_dynamic_calculate');
			$formid=$_REQUEST['formid'];
			$propertyid=$_REQUEST['propertyid'];
			//查询模板id
			$tplMap['formid'] = $formid;
			$tplid=$tplModel->where($tplMap)->find();
			if(!is_array($tplid)){
				$msg = '数据获取失败！'.$tplModel->getDBError().'_'.$tplModel->getLastSql();
				throw new NullDataExcetion($msg);
			}
			//查询text组件
			$map['formid']=$formid;
			$map['tplid']=$tplid['id'];
			$map['category']="text";
			$map['fieldname']=array('neq','orderno');
			$map['id']=array('neq',$propertyid);
			//查询所有text组件将对应A-Z
			$textList=$properyModel->where($map)->select();
			$iarr=65;
			foreach ($textList as $k=>$val){
				$voList[]=$val;
				if($iarr-65>25){
					$nextiarr=$iarr-65;
					$pert=floor($nextiarr/25);
					$fuiarr=chr(65+($pert-1));
					$voList[$k]['ASCII']=$fuiarr.chr($iarr-26*$pert);
				}else{
					$voList[$k]['ASCII']=chr($iarr);
				}
				$iarr=$iarr+1;
			}
			//查询编辑过的公式
			$calculateList=$misDynamicCalculateModel->where(array('formid'=>$formid,'propertyid'=>$propertyid))->find();
			$this->assign('calculateList',$calculateList);
			$this->assign('voList',$voList);
			//查询可用字母
			$calarray='';
			foreach ($voList as $vk=>$vs){
				if(count($voList)==1){
					$calarray.="{\"".$vs['ASCII']."\":1}";
				}elseif($vs != end($voList)) {
					// 不是最后一项
					$calarray.=$calarray?"\"".$vs['ASCII']."\":1,":"{\"".$vs['ASCII']."\":1,";
				} else {
					// 最后一项
					$calarray.="\"".$vs['ASCII']."\":1}";
				}
			}
			$this->assign('calarray',$calarray);
			
			//查询date组件
			$datemap['formid']=$formid;
			$datemap['tplid']=$tplid['id'];
			$datemap['category']="date";
			$datemap['id']=array('neq',$propertyid);
			//查询所有text组件将对应A-Z
			$dateList=$properyModel->where($datemap)->select();
			$dateiarr=65;
			foreach ($dateList as $k=>$val){
				$datevoList[]=$val;
				$datevoList[$k]['ASCII']=chr($dateiarr);
				$dateiarr=$dateiarr+1;
			}
			$this->assign('datevoList',$datevoList);
			//查询可用字母
			$datecalarray='';
			foreach ($datevoList as $vk=>$vs){
				if(count($datevoList)==1){
					$datecalarray.="{\"".$vs['ASCII']."\":1}";
				}elseif($vs != end($datevoList)) {
					// 不是最后一项
					$datecalarray.=$datecalarray?"\"".$vs['ASCII']."\":1,":"{\"".$vs['ASCII']."\":1,";
				} else {
					// 最后一项
					$datecalarray.="\"".$vs['ASCII']."\":1}";
				}
			}
			$this->assign('datecalarray',$datecalarray);
			$zuhecalarray=json_encode(array('0'=>$calarray,'1'=>$datecalarray));
			$this->assign('zuhecalarray',$zuhecalarray);
			$this->assign('formid',$formid);
			$this->assign('propertyid',$propertyid);
			$this->display();
		}
		}catch (Exception $e){
			echo ('<pre>'.$e->__toString().'</pre>');
		}
	}
/**
	 * @Title: calculatedatatable
	 * @Description: 内嵌表格计算设置   
	 * @author xiayq 
	 * @date 2015年1月19日 下午1:30:22 
	 * @throws
	 */
	function calculatedatatable(){
		$datatablemodel = M("mis_dynamic_form_datatable");
		$calstatus=$_REQUEST['calstatus'];
		if($calstatus==2){
			$formid=$_REQUEST['formid'];
			$propertyid=$_REQUEST['propertyid'];
			$formula=$_REQUEST['formula'];
			$validdigit=$_REQUEST['validdigit'];
			$type=$_REQUEST['type'];
			$jindu=$_REQUEST['jindu'];
			//去除空格
			$formula=str_replace(' ','',$_REQUEST['formula']);
			$pid=$_REQUEST['pid'];
			global $pidArr1;
			$pidArr1=$pid;
			foreach ($formula as $key=>$val){
				$map['id']=$key;
				$data['formula']=$val;
				$data['correspondfield']=json_encode($pid);
				$turnformula = preg_replace_callback('/([A-Z])+/','lookupControllSubCallBackFunc',$val);
				//取出使用的组件
				$subArr = str_split($val);
				$subly='';
				foreach ($subArr as $sk=>$sv){
					$is_preg=preg_match('/([A-Z])+/',$sv);
					if($is_preg==1){
						$is_inArray=array_key_exists($sv, $pid);
						if($is_inArray==false){
							$this->error ( "公式中有不存在的字母");
						}
						$subVal=str_replace($sv,$pid[$sv],$sv);
						$subly.=$subly?','.$subVal:$subVal;
					}
				}
				//去重
				$subassemblyArr=array_unique(explode(',', $subly));
				$subassembly=implode(',', $subassemblyArr);
				$data['calculate']=$turnformula;
				$data['subassembly']=$subassembly;
				$data['validdigit']=$validdigit[$key];
				$data['type'] = $type[$key];
				$data['jindu'] = $jindu[$key]?$jindu[$key]:null;
				$list=$datatablemodel->where($map)->save($data);
				//dump($datatablemodel->getlastsql());
				$fdata['formula']=$val;
				$fdata['correspondfield']=json_encode($pid);
				$fdata['calculate'] = $val;
				$fdata['subassembly'] = $subassemblyArr;
				$fdata['validdigit'] = $validdigit[$key];
				$fdata['type'] = $type[$key];
				$fdata['jindu'] = $jindu[$key]?$jindu[$key]:null;
				
				require CONF_PATH."property.php";
				if($DATATABLE_CONTROL['correspondfield']['edit']){
					$data['id'] = $key;
					$this->modifyDatatableteByCalculate($fdata,$formid,$propertyid,$key);
				}
			}
			if($list){
				$this->success(L('_SUCCESS_'));
			}else{
				$this->error ( L('_ERROR_') );
			}
		}else{
			$formid = $_GET['formid'];
			$propertyid = $_GET['propertyid'];
			$this->assign('formid',$formid);
			$this->assign('propertyid',$propertyid);
			//获取内嵌表数据
			$map['formid'] = $formid;
			$map['propertyid'] = $propertyid;
			$map['category'] = array('in','text,date');
			$rs = $datatablemodel->where($map)->select();
			$iarr=65;
			foreach ($rs as $k=>$val){
				$rs[$k]['ASCII']=chr($iarr);
				$iarr=$iarr+1;
			}
			//查询可用字母
			$calarray='';
			foreach ($rs as $vk=>$vs){
				if(count($rs)==1){
					$calarray.="{\"".$vs['ASCII']."\":1}";
				}elseif($vs != end($rs)) {
					// 不是最后一项
					$calarray.=$calarray?"\"".$vs['ASCII']."\":1,":"{\"".$vs['ASCII']."\":1,";
				} else {
					// 最后一项
					$calarray.="\"".$vs['ASCII']."\":1}";
				}
			}
			$this->assign('calarray',$calarray);
			$this->assign('rs',$rs);
			$this->display();
		}
		
	}
	
	/**
	 * calculatedate日期计算
	 */
	
	function calculatedate(){
		$controll=$_REQUEST['controll'];
		$conveneStatus=$_REQUEST['conveneStatus'];
		if($controll==1 && $conveneStatus==null){
			//查询是否存在
			$misDynamicCalculateModel=M('mis_dynamic_calculate');
			$formid=$_REQUEST['formid'];
			$propertyid=$_REQUEST['propertyid'];
			$type=3;
			$jingdu=$_REQUEST['jingdu'];
			$number=$_REQUEST['number'];
			$formula=$_REQUEST['formula'];
			$map['formid']=$formid;
			$map['propertyid']=$propertyid;
			$listInfo=$misDynamicCalculateModel->where($map)->select();
			//不存在添加
			if($listInfo==null){
				$data['formid']=$formid;
				$data['propertyid']=$propertyid;
				$data['formula']=$formula;
				$data['type']=$type;
				$data['number']=$number;
				$data['jingdu']=$jingdu;
				$data['subassembly']=$formula;
				$list=$misDynamicCalculateModel->add($data);
				if($list){
					$this->success(L('_SUCCESS_'),'',$formula);
				}else{
					$this->error ( L('_ERROR_') );
				}
			}else{
				$data['formid']=$formid;
				$data['propertyid']=$propertyid;
				$data['formula']=$formula;
				$data['type']=$type;
				$data['number']=$number;
				$data['jingdu']=$jingdu;
				$data['subassembly']=$formula;
				$list=$misDynamicCalculateModel->where($map)->save($data);
				if($list){
					$this->success(L('_SUCCESS_'),'',$formula);
				}else{
					$this->error ( L('_ERROR_') );
				}
			}
		}elseif($conveneStatus==2){
			//删除公式
			$misDynamicCalculateModel=M('mis_dynamic_calculate');
			$map['formid']=$_REQUEST['formid'];
			$map['propertyid']=$_REQUEST['propertyid'];
			//查询模板id
			$list=$misDynamicCalculateModel->where($map)->delete();
			if($list){
				$this->success(L('_SUCCESS_'),'','');
			}else{
				$this->error ( L('_ERROR_') );
			}
		}else{
			$properyModel=M('mis_dynamic_form_propery');
			$tplModel=M('mis_dynamic_form_template');
			$misDynamicCalculateModel=M('mis_dynamic_calculate');
			$formid=$_REQUEST['formid'];
			$propertyid=$_REQUEST['propertyid'];
			//查询模板id
			$tplid=$tplModel->where(array('formid'=>$formid))->field('id')->find();
			//查询date组件
			$map['formid']=$formid;
			$map['tplid']=$tplid['id'];
			$map['category']="date";
			$map['id']=array('neq',$propertyid);
			//查询所有date组件
			$voList=$properyModel->where($map)->select();
			//查询编辑过的公式
			$calculateList=$misDynamicCalculateModel->where(array('formid'=>$formid,'propertyid'=>$propertyid))->find();
			$this->assign('calculateList',$calculateList);
			
			$this->assign('voList',$voList);
			$this->assign('formid',$formid);
			$this->assign('propertyid',$propertyid);
			$this->display();
		}
	}
	/**
	 * 
	 * @Title: removeFieldCache
	 * @Description: todo(清除字段缓存) 
	 * @param int $formid  表单编号
	 * @author quqiang 
	 * @date 2015年1月23日 下午7:15:17 
	 * @throws
	 */
    private function removeFieldCache($formid){
		// 字段缓存目录
		defined('DATA_FIELDS_PATH') or define('DATA_FIELDS_PATH' , DATA_PATH.'_fields');
    	if(!$formid){
    		logs('清除字段缓存失败，formid 传入值为空');
    		return false;
    	}
        // 根据formid获取到当前表单使用到的所有数据库表列表
        $sql= <<<EOF
        SELECT actionname AS tablename FROM `mis_dynamic_form_manage` WHERE id=$formid
			UNION
		SELECT tablename FROM `mis_dynamic_database_mas` WHERE formid=$formid
			UNION
		SELECT tablename FROM `mis_dynamic_form_datatable` WHERE formid=$formid AND tablename IS NOT NULL
EOF;
        $model = M();
        $data = $model->query($sql);
        
        foreach ($data as $k=>$v){
        	$filePath = DATA_FIELDS_PATH.'/'.C('DB_NAME').'.'.createRealModelName($v['tablename']).'.php';
        	if(file_exists($filePath)){
        		unlink($filePath);
        		logs($filePath.' 字段缓存已删除');
        	}else{
        		logs($filePath.' 不存在字段缓存');
        	}
        }
    }

  /**
     * 根据组件的复用关系，整体更新需要与主表同步的数据【property表记录】
     * @Title: modifyPropertyDataByForeignField
     * @Description: todo(根据组件的复用关系，整体更新需要与主表同步的数据) 
     * @param array	$data			主表的单条组件属性记录
     * @param int		$propertyid	主表当前需要修改的组件属性记录ID  
     * @param int 		$formid		当前表单ID
     * @author quqiang 
     * @date 2015年4月15日 下午2:50:00 
     * @throws
     */
private function modifyPropertyDataByForeignField($data, $propertyid, $formid) {
		$formErrorShowName = '整体组件属性';
		/**
		 * 步骤：
		 * 1.取到配置文件
		 * 2.根据不同数据的category取出需要的组件配置记录
		 * 3.根据配置文件中的同步控制阀将来源数据中不需要同步的项unset掉
		 * 4.更新记录，条件为 isforeignfield 值等于传入的组件属性记录ID && $data[$key]
		 * 5.更新sub表记录
		 */
		if (is_array ( $data ) && $propertyid) {
			$obj = M ();
			$sql = "update `mis_dynamic_form_propery` set ";
			// 要修改property数据的集合
			$propertyDataArr = '';
			// 要修改sub数据的集合
			$subDataArr = '';
			$subIdArr='';
			$where = '';
			$categoryList = $this->getProperty ( $data ['category'] );
			if (is_array ( $categoryList )) {
				$propertyDataArr = $this->accordingConfigFilter(array($data));
				
				if ($data [$categoryList ['id'] ['dbfield']] && $data [$categoryList ['fields'] ['dbfield']]) {
					$where = " where `{$categoryList['isforeignfield']['dbfield']}`='{$data[$categoryList['id']['dbfield']]}' and `{$categoryList['fields']['dbfield']}`='{$data[$categoryList['fields']['dbfield']]}'";
				}
// 				$subIdArr [] = "{$data[$categoryList['ids']['dbfield']]}";
// 				$subDataArr[]="`title`='{$data[$categoryList['title']['dbfield']]}'";
				// 执行修改操作
				if (is_array ( $propertyDataArr ) && $where) {
					$filedListSql = '';
					foreach ($propertyDataArr as $fkey=>$fval){
						$filedListSql[] = "`{$fkey}`='{$fval}'";
					}
					if(is_array($filedListSql)){
						$propertyModyfySql = $sql . join(',' , $filedListSql) . $where;
					}
					if(!$propertyModyfySql){
						$msg = "复用表属性修改命令构建失败！";
						throw new NullDataExcetion($msg);
					}
					
					$propModifyResult = $obj->query ( $propertyModyfySql );
					if (false === $propModifyResult) {
						$msg = "{$formErrorShowName}更新失败！" . $obj->getDBError ().'  '.$obj->getLastSql();
						throw new NullDataExcetion ( $msg );
					} 
//					else {
// 						// 更新sub表标题.
// 						$formErrorShowName = "sub表标题";
// 						// 1 取得当前表为来源表时的所有复用表对应记录
// 						$subNeedModifySql = <<<EOF
//     					SELECT id FROM `mis_dynamic_database_sub`
// 						WHERE masid IN (
// 						SELECT s2.id FROM
// 						mis_dynamic_database_mas AS s1
// 						LEFT JOIN
// 						mis_dynamic_database_mas AS s2
// 						ON s1.tablename = s2.tablename
// 						WHERE s1.formid = {$formid}
// 						AND s2.formid != s1.formid
// 						)
// 						AND `field` ='{$data [$categoryList ['fields'] ['dbfield']]}'
// EOF;
// 						// 解析出需要更新的记录ID值
// 						$subNeedModifyData = $obj->query ( $subNeedModifySql );
// 						if (is_array ( $subNeedModifyData )) {
// 							$subNeedModifyIds = '';
// 							foreach ( $subNeedModifyData as $key => $val ) {
// 								$subNeedModifyIds [] = $val ['id'];
// 							}
// 							if (is_array ( $subNeedModifyIds ) && is_array ( $subDataArr )) {
// 								$where = join ( ',', $subIdArr );
// 								$sql = "update `mis_dynamic_database_sub` set " . join ( ',', $subDataArr ) . " where id in({$where}) or souceid in({$where})";
// 								$subNeedModifyResult = $obj->query ( $sql );
// 								if (false === $subNeedModifyResult) {
// 									$msg = "{$formErrorShowName}更新失败！" . $obj->getLastSql ();
// 									throw new NullDataExcetion ( $msg );
// 								}
// 							}
// 						}
// 					}
				}
			}
		}
	}
    /**
     * 根据组件的复用关系，整体更新需要与主表同步的数据【sub表记录】
     * @Title: modifySubByForeignField
     * @Description: todo(这里用一句话描述这个方法的作用) 
     * @param array 	$data		sub表中的所有记录
     * @param int 		$formid  	当前表单的formid
     * @author quqiang 
     * @date 2015年4月15日 下午3:14:08 
     * @throws
     */
    private function modifySubByForeignField($data , $formid,$soureid=0){ 
    	// mis_dynamic_controll_record		关联条件附加记录
    	// mis_dynamic_form_datatable		数据表格附加记录
    	// mis_dynamic_calculate				计算关联附加记录
    	//	mis_dynamic_form_indatatable	生单设置
    	if( is_array( $data ) && $formid){
    		$souceData='';
    		$needAddSubData='';
    		foreach ($data as $key=>$val){
    			if(!$val['id']){
//     				$msg = "来源数据中未能找到field字段！" ;
//     				throw new NullDataExcetion ( $msg );
    				$needAddSubData[]=$val;
    			}else{
    				$souceData[$val['id']] = $val;
    			}
    		}
    		$obj = M();
    		// 检查表单是否需要同步其它的sub记录
    		// 得到需要同步的sub表记录
    		$needModifyDataSql = "
SELECT psub.id,psub.masid,psub.field,psub.title , csub.id AS csub_id,csub.masid AS csub_masid,csub.field AS csub_field,csub.title AS csub_title FROM 
(
SELECT primarytablesub.* FROM `mis_dynamic_database_sub` AS primarytablesub,
(SELECT mas1.id FROM 

`mis_dynamic_database_mas` AS mas1

WHERE mas1.formid = {$formid}
AND mas1.`ischoise`=0 
AND mas1.`isprimary`=1
) AS primarytable

WHERE primarytablesub.`masid` = primarytable.id
) AS psub
LEFT JOIN 
(
SELECT copytablesub.* FROM `mis_dynamic_database_sub` AS copytablesub,
(
SELECT mas2.id FROM 

`mis_dynamic_database_mas` AS mas1

LEFT JOIN 
mis_dynamic_database_mas AS mas2
ON
mas1.`tablename` = mas2.`tablename`
WHERE mas1.formid = {$formid}
AND mas2.`formid` != {$formid}
AND mas1.`ischoise`=0 
AND mas1.`isprimary`=1
) AS copytable
WHERE copytablesub.`masid` = copytable.id
) AS csub
ON psub.field = csub.field";
    		$needModifyData = $obj->query($needModifyDataSql);
    		if( false === $needModifyData ){
    			$msg = "同步sub表记录：检索当前表的复制表时出错！" .$obj->getDBError();;
    			throw new NullDataExcetion ( $msg );
    		}
    		// 最终执行sql
    		$excuteSql='';
    		// 更新的全部数据sql数据
    		$needCopySqlArr='';
 ///////////////////////////////////////////////////////////////////
 //		单条数据的修改
 //////////////////////////////////////////////////////////////////
    		// 单条更新sql语句
//     		$needCopySingleSql='update mis_dynamic_database_sub set  ';
//     		if($needModifyData){
//     			foreach ($needModifyData as $key => $val){
//     				$modifyFieldList='';
//     				$modifyFieldWhere ='';
//     				// `field`,`title`,`type`,`length`,`category`,`weight`,`sort`,`isshow`,`isdatasouce`,`status`
//     				if( is_array($souceData[$val['id']] ) ){
//     					if($val['csub_id']){
//     						$modifyFieldWhere=" where id={$val['csub_id']};";
//     					}
//     					$modifyFieldList = "`field`='{$souceData[$val['id']]['field']}',`title`='{$souceData[$val['id']]['title']}',`type`='{$souceData[$val['id']]['type']}',`length`='{$souceData[$val['id']]['length']}',`category`='{$souceData[$val['id']]['category']}',`weight`='{$souceData[$val['id']]['weight']}',`sort`='{$souceData[$val['id']]['sort']}',`isshow`='{$souceData[$val['id']]['isshow']}',`isdatasouce`='{$souceData[$val['id']]['isdatasouce']}',`status`='{$souceData[$val['id']]['status']}'";
//     					if($modifyFieldWhere){
//     						$needCopySqlArr[] = $needCopySingleSql.$modifyFieldList.$modifyFieldWhere;
//     					}
//     				}
//     			}
//     		}
//     		$excuteSql=join('', $needCopySqlArr);
///////////////////////////////////////////////////////////////////////////////////////////////////////
    		// 单条更新sql语句
    		$needCopySingleSql = "INSERT INTO `mis_dynamic_database_sub`(`id`,`field`,`title`,`type`,`length`,`category`,`weight`,`sort`,`isshow`,`isdatasouce`,`status`) VALUES ";
    		if($needModifyData){
    			foreach ($needModifyData as $key => $val){
    				if( $val['csub_id'] && $val['id'] && is_array($souceData[$val['id']] ) ){
    					$needCopySqlArr[]= "({$val['csub_id']},'{$souceData[$val['id']]['fieldname']}','{$souceData[$val['id']]['title']}','{$souceData[$val['id']]['fieldtype']}','{$souceData[$val['id']]['tablelength']}','{$souceData[$val['id']]['category']}','{$souceData[$val['id']]['weight']}','{$souceData[$val['id']]['sort']}','{$souceData[$val['id']]['isshow']}','{$souceData[$val['id']]['isdatasouce']}','{$souceData[$val['id']]['status']}')";
    				}
    			}
    			if(is_array($needCopySqlArr)){
    				$excuteSql = $needCopySingleSql.join(',', $needCopySqlArr)." ON DUPLICATE  KEY UPDATE `id`=VALUES(`id`),`field`=VALUES(`field`),`title`=VALUES(`title`),`type`=VALUES(`type`),`length`=VALUES(`length`),`category`=VALUES(`category`),`weight`=VALUES(`weight`),`sort`=VALUES(`sort`),`isshow`=VALUES(`isshow`),`isdatasouce`=VALUES(`isdatasouce`),`status`=VALUES(`status`)";
    			}else{
    				$excuteSql = '';
    			}
    		}
    		if($excuteSql){
	    		$updateret = $obj->query($excuteSql);
	    		if( false === $updateret ){
	    			$msg = '修改sub表失败！！'.$obj->getDbError().$obj->getLastSql();
	    			throw new NullDataExcetion( $msg );
	    		}
    		}
    		// 处理 同步追加数据
    		if(is_array($needAddSubData)){ 
    			$needSynchronizationSubSql = "SELECT mas2.* FROM 
					`mis_dynamic_database_mas` AS mas1
					LEFT JOIN 
					mis_dynamic_database_mas AS mas2
					ON
					mas1.`tablename` = mas2.`tablename`
					WHERE mas1.formid = {$formid}
					AND mas2.`formid` != {$formid} 
					AND mas1.`isprimary`=1";
    			$needAddSubRelationData = $obj->query($needSynchronizationSubSql);
    			if( is_array($needAddSubRelationData) ){
    				$orderSql='insert into mis_dynamic_database_sub(`masid`,`souceid`,`field`,`title`,`type`,`length`,`category`,`weight`,`sort`,`isshow`,`isdatasouce`,`status`,`formid`,`modelname`,`ischoise`) values';
    				foreach ($needAddSubData as $needkey=>$needval){
    					foreach ($needAddSubRelationData as $akey=>$aval){
    						$needval['weight'] = $needval['weight'] ? 1: 0;
    						$needval['sort'] = $needval['sort']=='' ? 0: $needval['sort'];
    						$needval['isshow'] = $needval['isshow'] ? 1: 0;
    						$needval['isdatasouce'] = $needval['isdatasouce'] ? 1: 0;
    						$needval['status'] = $needval['status'] ? 1: 0;
    						unset($str);
    						// ,{$val['rowid']}
    						$str ="({$aval['id']}, '{$soureid}' , '{$needval['field']}'  , '{$needval['title']}' ".
    								", '{$needval['type']}' , '{$needval['length']}', '{$needval['category']}',".
    								" {$needval['weight']}, {$needval['sort']}, {$needval['isshow']}, {$needval['isdatasouce']} ,".
    								" {$needval['status']} , {$aval['formid']} , '{$aval['modelname']}',1)";
    						
    						$orderSqlArr[] = $str;
    					}
    				} 
    				if(is_array($orderSqlArr)){
    					$orderSql .= join(',', $orderSqlArr);
    				}else {
    					$orderSql = '';
    				} 
    				if($orderSql){
	    				$addret = $obj->query($orderSql);
		    				if(false === $addret){
		    					$msg = '添加sub表失败！！'.$obj->getDbError().$obj->getLastSql();
		    					throw new NullDataExcetion( $msg );
		    				}
	    				}
    				}
    			}
    		}
    	}
    
    /**
     * 根据组件的复用关系，整体更新需要与主表同步的数据【calculate表记录】
     * @Title: modifySubByForeignField
     * @Description: todo(这里用一句话描述这个方法的作用)
     * @param unknown $data
     * @param unknown $formid
     * @author quqiang
     * @date 2015年4月15日 下午3:14:08
     * @throws
     */
    private function modifyCalculateByForeignField($data , $formid){
    	// mis_dynamic_controll_record		关联条件附加记录
    	// mis_dynamic_form_datatable		数据表格附加记录
    	// mis_dynamic_calculate				计算关联附加记录
    	//	mis_dynamic_form_indatatable	生单设置
    	 
    }
    
    
 /**
     * 根据组件的复用关系，整体更新需要与主表同步的数据【datatable -- 数据表格附加记录	表记录】
     * @Title: modifySubByForeignField
     * @Description: todo(这里用一句话描述这个方法的作用)
     * @param mix $data 处理的数据
     * @param int $formid 模型id
     * @param int $propertyid 组件id
     * @param int $type (1=>新增,2=>修改,3=>删除)
     * @author quqiang
     * @date 2015年4月15日 下午3:14:08
     * @throws
     * 
     */
    
    private function modifyDatatableteByForeignField($data , $formid ,$propertyid, $type){
    	/**
    	 * 主表 复用表 前提 对同一个物理表维护
    	 * 
    	 * 1 默认按单条修改
    	 * 2 复用表找主表 propertyid在proprey表查属性记录isforeignfield 有值表示是复用，其值为复用的propertyid
    	 * 3来源表找主表 mis_dynamic_database_mas里找formid 
    	 */
    	/**
    	 * 同步：主表->复用表
    	 * 一、外部设置  1、复用表字段是否可进行新增、删除 2、字段属性是否可修改 
    	 * 二、判断是否复用表
    	 * 三、如果是主表、找到所有复用表，对所有复用表操作；
    	 * 五、对字段属性config、correspondfield单独处理
    	 */
    	require CONF_PATH."property.php";
    	$masModel = M("mis_dynamic_database_mas");
    	$propertyModel = M("mis_dynamic_form_propery");
    	$datatableModel = M("mis_dynamic_form_datatable");
    	//复用表数据(组件信息)
    	$reusetableinfo = array();
    	$sql = "SELECT p.id ,p.formid  FROM mis_dynamic_database_mas AS m LEFT JOIN mis_dynamic_form_propery AS p ON p.dbname=m.tablename AND p.formid=m.formid WHERE p.isforeignfield={$propertyid} AND p.category='datatable'";
    	$reusetableinfo = $masModel->query($sql);
    	
    	if($reusetableinfo){
    		switch ($type){
    			case 1: //增    				
			    	//拼装插入sql语句需要的数据
    				$newadddata = array();
    				foreach($reusetableinfo as $fk=>$fv){
    					foreach($data as $ik=>$iv){
    						//复用表新增数据
    						$newadddata[]= "('{$iv[fieldname]}','{$iv[fieldtitle]}','{$iv[fieldtype]}','{$iv[fieldlength]}','{$iv[category]}','{$iv[unit]}','{$iv[unitls]}','{$iv[config]}','{$fv[formid]}','{$fv[id]}','{$iv[isshow]}','{$iv[isedit]}','{$iv[iscount]}','{$iv[fieldsort]}','{$iv[tablename]}','{$iv[id]}')";
    					}
    				}
    				//拼装sql
    				$finsertsql = "INSERT INTO mis_dynamic_form_datatable ";
    				$finsertsql .= " (fieldname,fieldtitle,fieldtype,fieldlength,category,unit,unitls,config,formid,propertyid,isshow,isedit,iscount,fieldsort,tablename,sourceid) ";
    				$finsertsql .= " VALUES ";
    				$finsertsql .= implode(",",$newadddata);
    				//执行
    				$ret = $datatableModel->execute($finsertsql);
    				unset($newadddata);
    				unset($fk);
    				unset($data);
    				if($ret === false){
    					$msg = "新增失败！".$datatableModel->getDBError().$datatableModel->getLastSql();
    					throw new NullDataExcetion( $msg );
    				}
    			case 2: //改
    				//直接传递需要修改的字段$data,一个主表数据对应多个复用表数据。对单条数据多个表记录进行修改
    				foreach($data as $key=>$val){
    					foreach($val as $k=>$v){
    						if($DATATABLE_CONTROL[$k]['edit']){
    							$up[] = $k."=".$v; 
    						}    						
    					}
    					if($up){
    						$upsql = "UPDATE mis_dynamic_form_datatable SET ".implode(",",$up)." WHERE sourceid={$val['id']}";
    						$ret = $datatableModel->execute($upsql); 
    						if($ret === false){
    							$msg = "修改失败！".$datatableModel->getDBError().$datatableModel->getLastSql();
    							throw new NullDataExcetion( $msg );
    						}  						
    					}
    				}
    				unset($data);
    				break;
    			case 3: //删 
    				//传入主表删除字段id 相应的复用表关联字段为sourceid
    				if($DATATABLE_CONTROL['main_delete']){
    					if($data){
    						$delmap['sourceid'] = array('in',$data);
    						$ret = $datatableModel->where($delmap)->delete();
    						if(false === $ret){
    							$msg = "删除失败！".$datatableModel->getDBError().$datatableModel->getLastSql();
    							throw new NullDataExcetion( $msg );
    						}
    					}    					
    		
    				}else{
    					$msg = "表单不允许删除！";
    					throw new NullDataExcetion( $msg );
    				}
    				unset($data);
    				break;
    		}
    	}
    	
    }
    private function modifyDatatableteByCalculate($data,$formid,$propertyid,$id){
    	//查是否有被复用
    	$obj = M();
    	$datatableModel = M("mis_dynamic_form_datatable");
    	$reusetableinfo = array();
    	$sql = "SELECT p.id ,p.formid  FROM mis_dynamic_database_mas AS m LEFT JOIN mis_dynamic_form_propery AS p ON p.dbname=m.tablename AND p.formid=m.formid WHERE p.isforeignfield={$propertyid} AND p.category='datatable'";
    	$reusetableinfo = $obj->query($sql);
    	if($reusetableinfo){
    		//主内嵌字段
    		$inlist = $datatableModel->where("propertyid={$propertyid} AND formid={$formid}")->select();
    		//复用表字段
    		$newlist = array();
    		$datatablelist = $datatableModel->select();
    		foreach($reusetableinfo as $rk=>$rv){    			
    			foreach($datatablelist as $key=>$val){
    				if($rv['id'] == $val['propertyid'] && $rv['formid'] == $val['formid']){
    					$newlist[$val['id']] = $val;
    				}
    			}
    		}
    		//主表和复用表字段对应关系
    		$relation = array();
    		foreach($inlist as $ik=>$iv){
    			foreach($newlist as $nk=>$nv){
    				if($iv['id']==$nv['sourceid']){
    					$relation[$nv['propertyid']][$iv['id']] = $nv['id'];
    				}
    			}
    		}
    		
    		
    	}
    	$correspondfield = json_decode($data['correspondfield']);
    	foreach($relation as $rk=>$rv){
    		$newdata[$rk] = $data;
    		foreach($correspondfield as $ck=>$cv){
    				$newcor[$rk][$ck] = $rv[$cv];
    		}
    		foreach($data['subassembly'] as $sk=>$sv){
    			$data['subassembly'][$sk] = $rv[$sv];
    		}
    		$data['calculate'] = 
    		$newdata[$rk]['correspondfield'] = json_encode($newcor[$rk]);
    		
    		
    		
    		
    		unset($newdata[$rk]['id']);
    		$upmap['propertyid'] = array('eq',$rk);
    		$upmap['sourceid'] = array('eq',$id);    		
    		$ret = $datatableModel->where($upmap)->save($newdata[$rk]);
    		echo $ret;
    		echo $datatableModel->getLastSql();
    	}
    }
    /**
     * 根据组件的复用关系，整体更新需要与主表同步的数据【mis_dynamic_form_indatatable -- 生单设置	表记录】
     * @Title: modifySubByForeignField
     * @Description: todo(这里用一句话描述这个方法的作用)
     * @param unknown $data
     * @param unknown $formid
     * @author quqiang
     * @date 2015年4月15日 下午3:14:08
     * @throws
     */
    private function modifyInDatatableteByForeignField($data , $formid){
    	// mis_dynamic_controll_record		关联条件附加记录
    	// mis_dynamic_form_datatable		数据表格附加记录
    	// mis_dynamic_calculate				计算关联附加记录
    	//	mis_dynamic_form_indatatable	生单设置
    	
    }
    
    /**
     * 根据组件的复用关系，整体更新需要与主表同步的数据【mis_dynamic_controll_record -- 关联条件附加记录	表记录】
     * @Title: modifySubByForeignField
     * @Description: todo(这里用一句话描述这个方法的作用)
     * @param unknown $data
     * @param unknown $formid
     * @author quqiang
     * @date 2015年4月15日 下午3:14:08
     * @throws
     */
    private function modifyControllRecordByForeignField($data , $formid){
    	// mis_dynamic_controll_record		关联条件附加记录
    	// mis_dynamic_form_datatable		数据表格附加记录
    	// mis_dynamic_calculate				计算关联附加记录
    	//	mis_dynamic_form_indatatable	生单设置
    	
    	
    	   
    	
    	
    	
    }
    
    
    function getDefaultCheckItem(){
    	var_dump($_POST);
    }
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //													测试函数																																	//
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	function methodtest(){
$vo['id']=1;
$vo['name'] = '';
$vo['sysdutyid'] = 'asd';

$fieldlist['name']='name';
$fieldlist['id']='idasds';
$fieldlist['departmentid']='departmentid';
$fieldlist['sysdutyid']='sysdutyid';
$a=getAppendCondition($vo, $fieldlist);
header("Content-Type:text/html; charset=utf-8");
$this->display('test:index'); 

		$obj = M();
		$formid=190;
		$sql="SELECT * FROM `mis_dynamic_database_sub` WHERE masid=
(SELECT id FROM `mis_dynamic_database_mas` WHERE formid={$formid} AND `ischoise`=0 AND `isprimary`=1)";
		
		$data = $obj->query($sql);
		$data[]=array(
			  'masid' =>  '38' ,
			  'rowid' =>  'jinrongjigoumingchen',
			  'field' =>  'jinrongjigoumingchen',
			  'title' =>  'aaaaaaaaaaaaaa',
			  'type' =>  'varchar',
			  'length' =>  '80' ,
			  'category' =>  'text',
			  'weight' =>  '1',
			  'sort' =>  '0' ,
			  'isshow' =>  '1' ,
			  'isdatasouce' =>  '0' ,
			  'status' =>  '1' ,
			  'formid' =>  '190' ,
			  'modelname' =>  'MisAutoSft',
			  'ischoise' => null
		);
		try {
			$this->modifySubByForeignField($data, $formid);
		}catch (Exception $e){
			echo $e->__toString();
		}
	}
	public function editcountassignment(){
		if($_REQUEST['assignmentfield']){
			$this->assign("assignmentfield",$_REQUEST['assignmentfield']);
		}
		$formid = $_REQUEST['formid'];
		$fieldtag = $_REQUEST['fieldtag'];
		$model=M("mis_dynamic_form_propery");
		$list = $model->where("formid={$formid} and status=1 and category='text'")->getField('id,fieldname,title');
		$this->assign("fieldtag",$fieldtag);
		$this->assign("list",$list);
		$this->display();
	}
	public function accountassignmentreturn(){
		$newdata = json_encode($_POST);
		$this->success("添加成功",'',$newdata);
	}


	function truncate(){
		$obj = M('mis_dynamic_form_manage');
		$map['isrecord'] = 0;
		$logsName='destriylogs_'.date('Ymd' ,time());
		$basepath  = ROOT . '/Dynamicconf/FormLogs';
		
		switch ($_POST['status']){
			case 'start':
// 				A('Http')->create( '1.docx' , './systemui/Admin/Dynamicconf/FormLogs/');
				
				// 开启异步
				$logsName .='end';
				$directory = $basepath.'/'.$logsName;
				if(file_exists($directory)){
					$obj_dir = new Dir;
					$obj_dir->del($directory);
				}
				$parame = '/MisDynamicFormManage/truncateStart';
				A('Asynchronousrequest')->start('collecttodata' );
				$this->ajaxReturn('开始清除');
				break;
			case 'log':
				// 获取日志 
				$fname = $basepath.'/'.$logsName.'.log';
// 				$efname = $basepath.'/'.$logsName.'end.log';
// 				if(file_exists($efname)){
// 					// 存在结束标识，操作完结
// 					$msg = file_get_contents($fname);
// 					$msg  .= '清理完成！！';
// 					$this->ajaxReturn($msg , '' , 0);
// 				}
				// 模拟正在删除中
				if(!file_exists($fname)){
					$msg  = '没有清理日志或，正在清理中';
					$this->ajaxReturn($msg , '' , 0);
				}else{
					$msg = file_get_contents($fname);
					$this->ajaxReturn($msg , '' , 1);
				}
				break;
			case 'test':
					// 获取日志
					$this->ajaxReturn('测试');
					break;
			default:
				// 默认显示页面
				$data = $obj->where($map)->count();
				$this->assign('data' , $data);
				$this->display();
				break;
		}
	}
	
	function truncateStart(){
		$logsName='destriylogs_'.date('Ymd' , time());
		logs('测试异步' , $logsName);
		$obj = M('mis_dynamic_form_manage');
		$map['isrecord'] = 0;
// 		$nodeDel = A('NodeFileDestroy');
		$data = $obj->where($map)->select();
		if(is_array($data)){
			foreach ($data as $key=>$val){
// 				$nodeDel->destroy($val['actionname']);
				$this->destroy($val['actionname']);
			}
		}
		// 生成结束文件
		logs('异步删除结束' , $logsName.'end');
	}
	
	
	public function destroy($actionName, $logsName) {
		$logsName = 'destriylogs_' . date ( 'Ymd', time () );
		logs ( "===清除表单 '{$actionFileName}'========" );
	
		if (! $actionName) {
			$msg = 'Action名称为空';
			throw new NullDataExcetion ( $msg );
		}
	
		// 日志存储位置
		$logsName = $logsName ? $logsName : 'destriylogs_' . date ( 'Ymd' );
		$tableName = '';
		/**
		 * 动态表单删除文件流程：
		 * 1：销毁action、model、view、actionExtend、modelExtend、模板文件
		 * 2：销毁动态配置下的Models项、Sytem下的listInc
		 * 3：动态表单的组件配置文件，动态表单的字段记录信息(MisAutoAnameList.inc.php)，
		 * 4：清DB数据 mis_auto_primary_mas 表 mis_auto_primary_sub
		 * 5：表单(mis_dynamic_form_manage) 及 表单下的字段记录(mis_dynamic_form_field)
		 */
	
		// 检查Action是否存在
		$actionFileName = LIB_PATH . "Action/" . $actionName . "Action.class.php";
		if (! file_exists ( $actionFileName )) {
			logs ( "用户指定删除Action:{$actionName}不存在", $logsName );
		}
		// 得到当前action的表名。
		$curModel = D ( $actionName );
		// 主表名称
		$tableName = $curModel->getTableName ();
		//
		logs ( $tableName, $logsName );
		// 得到主表在表单记录中的Id然后找到所有datatable
	
		/**
		 * 构建删除文件列表
		 * 1.Action , Model , View , ActionExtend , ModelExtend , Template
		 * 2.销毁动态配置
		*/
	
		/**
		 * 单文件删除列表
		 *
		 * @var unknown_type
		*/
		$destriyFileList = array ();
	
		/**
		 * 文件夹删除列表
		 *
		 * @var unknown_type
		*/
		$destriyFolderList = array ();
		// 1
		// action , actionExtend
		$destriyFileList [] = LIB_PATH . "Action/" . $actionName . "Action.class.php";
		$destriyFileList [] = LIB_PATH . "Action/" . $actionName . "ExtendAction.class.php";
		// Model , ModelExtend , View
		$destriyFileList [] = LIB_PATH . "Model/" . $actionName . "Model.class.php";
		$destriyFileList [] = LIB_PATH . "Model/" . $actionName . "ExtendModel.class.php";
		$destriyFileList [] = LIB_PATH . "Model/" . $actionName . "ViewModel.class.php";
		// 模板文件目录
		$destriyFolderList [] = TMPL_PATH . C ( 'DEFAULT_THEME' ) . '/' . $actionName;
		// 2
		// Models , 组件配置文件
		$dir = '/autoformconfig/';
		$destriyFolderList [] = C ( 'DYNAMIC_PATH' ) . '/Models/' . $actionName;
		$destriyFileList [] = C ( 'DYNAMIC_PATH' ) . '/autoformconfig/' . $actionName . '.php';
		/**
		 * 删除表记录
		 * 1.删除模板
		 * 1.删除组件表propery
		 * 2.删除sub表字段记录
		 * 3.删除mas表
		 * 4.内嵌表格删除
		 */
		$formid = getFieldBy ( $actionName, "actionname", "id", "mis_dynamic_form_manage" );
	
		// 删除mas表
		$deltemp = "DELETE FROM mis_dynamic_form_template WHERE formid={$formid}";
		// 删除组件记录表
		$delpropery = "DELETE FROM mis_dynamic_form_propery WHERE formid={$formid}";
		// 查找当前表单主表
		$tablename = getFieldBy ( $formid, "formid", "tablename", "mis_dynamic_database_mas", "isprimary", "1" );
		// 查询表是否被复用
		$ischoise = getFieldBy ( $tablename, "tablename", "tablename", "mis_dynamic_database_mas", "ischoise", "1" );
		// 丢弃表SQL
		$droptable = "";
		if (! $ischoise) {
			// 删除mas表记录
			$delMas = "DELETE FROM mis_dynamic_database_mas WHERE formid={$formid}";
			// 丢弃表
			$droptable = "drop table " . $tablename;
			// 删除sub表记录
			$delSub = "DELETE FROM mis_dynamic_database_sub WHERE formid={$formid}";
		}
		// 4.删除数据表。
		$getDataTableSql = "SELECT
		CONCAT(tablename, '_sub_', `FIELD`) AS datatablename
		FROM
		mis_dynamic_database_sub AS sub,
		(SELECT
		tablename
		FROM
		mis_dynamic_database_mas
		WHERE id IN
		(SELECT
		masid
		FROM
		mis_dynamic_database_sub
		WHERE formid = {$formid}
		AND category = 'datatable')) AS tablename
		WHERE sub.formid = {$formid}
		AND sub.category = 'datatable' ";
		/**
		* 删除数据表，
		* 先得到所有数据表格记录
		 * 1。删除字段记录表
		 * 2。删除内嵌表格的表
		 * 3. 删除表单记录
		 * 4. 删除当前使用表
		 */
			//
	
			$delFieldSql = "DELETE FROM mis_dynamic_form_field WHERE formid ={$formid}";
			$delDataTableSql = "";
		$delFormSql = "DELETE FROM mis_dynamic_form_manage WHERE actionname='$actionName'";
		if ($tableName) {
		$delSql = "DELETE FROM $tableName";
		}
		logs ( '单文件删除列表：' . arr2string ( $destriyFileList ), $logsName );
		logs ( '文件件删夹除列表：' . arr2string ( $destriyFolderList ), $logsName );
		logs ( '数据表-字段记录删除：' . $delFieldSql, $logsName );
		logs ( '数据表-内嵌表删除：' . $delDataTableSql, $logsName );
					logs ( '数据表-表单记录删除：' . $delFormSql, $logsName );
	
		/**
			* 真实删除
			*/
	
			foreach ( $destriyFileList as $k => $v ) {
			$ret = unlink ( $v );
			logs ( "文件 $v 删除" . ($ret ? '成功' : '失败'), $logsName );
			}
			foreach ( $destriyFolderList as $k => $v ) {
			$ret = deldir ( $v );
			logs ( "文件夹 $v 删除" . ($ret ? '成功' : '失败'), $logsName );
			}
	
			$modelObj = M ();
			$fieldDelRet = $modelObj->query ( $delFieldSql );
			logs ( "组件字段记录 $delFieldSql  删除  " . ($fieldDelRet ? '失败' : '成功'), $logsName );
			$formDelRet = $modelObj->query ( $delFormSql );
			logs ( "表单记录  $delFormSql  删除 " . ($formDelRet ? '失败' : '成功'), $logsName );
			if ($delSql) {
			$delRet = $modelObj->query ( $delSql );
			logs ( "表单记录  $delFormSql  删除 " . ($delRet ? '失败' : '成功'), $logsName );
			} else {
			logs ( "表 $tableName  不存在", $logsName );
	}
	// 删除数据表
	$dataTableList = $modelObj->query ( $getDataTableSql );
	logs ( "删除数据表SQL  $getDataTableSql ", $logsName );
	if ($dataTableList) {
	foreach ( $dataTableList as $k => $v ) {
	$delDataTableSql = "DROP TABLE {$v['datatablename']}";
	// 删除数据表
		$tempDelRet = $modelObj->query ( $delDataTableSql );
		logs ( "删除数据表  $delDataTableSql  删除 " . ($tempDelRet ? '失败' : '成功'), $logsName );
	}
	}
	
	// 删除模板
	$tempDelRet = $modelObj->query ( $deltemp );
	logs ( "删除模板  $deltemp  删除 " . ($tempDelRet ? '失败' : '成功'), $logsName );
	// 删除组件
	$properyDelRet = $modelObj->query ( $delpropery );
			logs ( "删除组件  $delpropery  删除 " . ($properyDelRet ? '失败' : '成功'), $logsName );
			// 删除sub表
			$subResult = $modelObj->query ( $delSub );
			logs ( "删除sub表  $delSub  删除 " . ($subResult ? '失败' : '成功'), $logsName );
			// 删除mas表记录
			$masResult = $modelObj->query ( $delMas );
			logs ( "删除mas表  $delMas  删除 " . ($masResult ? '失败' : '成功'), $logsName );
			$modelObj->commit ();
	}
	
	/**
	 * @Title: updatewarn
	 * @Description: todo(内嵌表修改提醒->主要是其他表用到了这个表的字段情况)
	 * @param unknown_type $data  需要formid（哪个模块），如果是内嵌表还需要proertyid（哪个内嵌表）
	 * @param unknown_type $type  类型（主表还是内嵌表）
	 * @param unknown_type $fieldarr 被修改或者被删除的字段(array('old'=>array(),'new'=>array()))
	 * @author 谢友志
	 * @date 2015-7-21 上午11:10:34
	 * @throws
	 */
public function dtupdatewarn($formid,$proertyid=null,$type='',$fieldarr=array(),$returnarr=false){
		$fields = $fieldarr['old'];
		if($proertyid){//查询该内嵌表在mis_dynamic_form_datatable所有的记录
			$old['propertyid'] = $proertyid;
			$dtmodel = M("mis_dynamic_form_datatable");
			$oldList = $dtmodel->where($old)->select();
			$tableName = $dtmodel->where($old)->getField("tablename");
			$type = $type?$type:2;
		}else{//为主表时，因为有了formid，所以必定有记录
			$oldList = true;
			$tableName = getFieldBy($formid,'formid','tablename','mis_dynamic_database_mas');
			$type = $type?$type:1;
		}
		//主表模型名称
		$oldAction = getFieldBy($formid,"id","actionname","mis_dynamic_form_manage");
		//如果有记录 才做处理
		// 1->修改 2->删除
		$needList = array();
		$list = array();
		if($oldList){			
			$oldIdArr = array();
			$oldListById = array(); //
			foreach($oldList as $key=>$val){
				$oldListById[$val['id']] = $val;
				$oldIdArr[] = $val['id'];
			}
			
			

			$nModel = M();
			//查询视图、漫游、lookup是否引用了这个表
			//1.视图
			$vsql = "SELECT m.id as id,m.name,m.modelname,m.title as viewtitle,s.field,s.otherfield,s.title FROM mis_system_dataview_sub AS s
			left join mis_system_dataview_mas AS m ON s.masid=m.id	WHERE s.tablename='{$tableName}' AND s.status=1
			AND ((s.isback!=0) OR (s.islistshow=1) OR (s.funcdata is not null AND s.funcdata !=''))";
			$dviewList = $nModel->query($vsql);
			if($dviewList){
				foreach($dviewList as $vk=>$vv){
					$tempfield = explode('.',$vv['field']);
					$dviewList[$vk]['truefield'] = $tempfield[1];
					foreach($fields as $k=>$v){
						if($v == $tempfield[1]){
							$needList['dataview'][$vv['id']]['title'] = $vv['viewtitle'];
							$needList['dataview'][$vv['id']]['fields'][] = $v;
						}
					}
					unset($tempfield);
				}
			}
			
			
			
// 			$viewtemp = array(); //引用了该表的视图数据
// 			//$viewnamearr // 用于漫游引用
// 			$dataviewSubModel = M("mis_system_dataview_sub");
// 			$where = "tablename='{$tableName}' and status=1 and ()";
// 			$dataviewSubList = $dataviewSubModel->where($where)->group("masid")->select();
			
// 			if($dataviewSubList){
// 				$dataviewMasModel = M("mis_system_dataview_mas");
// 				$dataviewMasList = $dataviewMasModel->where("status=1")->getField("id,name,title");
// 				foreach($dataviewSubList as $key=>$val){
// 					$viewtemp[$val['masid']] = $dataviewMasList[$val['masid']];
// 					//$viewnamearr[] = $dataviewMasList[$val['masid']]['name'];
// 				}
// 			}
			//漫游
			
			$rsql = "SELECT  m.id,m.title,m.sourcemodel,m.sourcename,s.sourcetable,m.targetmodel,m.targetname,s.targettable,s.sfield,s.tfield 
					 FROM mis_system_data_roam_sub AS s LEFT JOIN mis_system_data_roam_mas AS m ON s.masid=m.id
					 WHERE m.status=1 and (s.targettable='{$tableName}' OR s.sourcetable='{$tableName}')";
			$roamList = $nModel->query($rsql);
			if($roamList){
				foreach($roamList as $rk=>$rv){
					foreach($fields as $k=>$v){
						if(($rv['targettable'] == $tableName&&$rv['tfield'] == $v) || ($rv['sourcetable'] == $tableName&&$rv['sfield'] == $v) ){
							$needList['roam'][$rv['id']]['title'] = $rv['title'];
							$needList['roam'][$rv['id']]['fields'][] = $v;
						}
					}
				}
			}
			
			
			
// 			$roamtemp = array();//引用了该表的漫游数据
// 			$roamSubModel=M("mis_system_data_roam_sub");
// 			$roamSubList = $roamSubModel->where("targettable='{$tableName}' OR sourcetable='{$tableName}'")->group("masid")->select();
// 			if($roamSubList){
// 				$roamMasModel = M("mis_system_data_roam_mas");
// 				$roamMasList = $roamMasModel->where("status=1")->getField("id,sourcename,targetname,title");
// 				foreach($roamSubList as $key=>$val){
// 					$roamtemp[$val['masid']] = $dataviewMasList[$val['masid']];
// 					//$viewnamearr[] = $dataviewMasList[$val['masid']]['name'];
// 				}
// 			}
			//lookup
			if($type == 1){
				$rsql = "SELECT m.id,m.title,m.`mode`,m.`fields`,m.filed,m.val,m.rulesinfo, m.listshowfields,m.funccheck,s.treemodel,s.treeshow,s.treevalue
				FROM mis_system_lookupobj AS m LEFT JOIN mis_system_lookupobj_sub_treeconfig AS s ON m.id=s.masid
				WHERE (m.mode='{$oldAction}' OR s.treemodel='{$tablename}') and m.status=1 and m.viewtype<1";
				$lookuplist = $nModel->query($rsql);
				if($lookuplist){
				foreach($lookuplist as $lk=>$lv){
				$lookupfields = explode(',',$lv['fields']);
						$lookuplistshowfields = explode(',',$lv['listshowfields']);
								$lookupfunccheck = explode(',',$lv['funccheck']);
								$templookup = array_filter(array_merge($lookupfields,$lookuplistshowfields,$lookupfunccheck));
								foreach($fields as $k=>$v){
								if(($lv['mode'] == $oldAction&&in_array($v,$templookup)) || ($lv['treemodel'] == $tableName&&($lv['treeshow'] == $v||$lv['treevalue'] == $v)) ){
								$needList['lookup'][$lv['id']]['title'] = $lv['title'];
										$needList['lookup'][$lv['id']]['fields'][] = $v;
								}
								}
					unset($templookup);
								}
				}
			}else{
				$rsql = "Select * from mis_system_lookupobj WHERE mode='{$oldAction}' AND datetable != '' AND datetable is not null ";
				$lookupdtlist = $nModel->query($rsql);
				if($lookupdtlist){
					foreach($lookupdtlist as $lk=>$lv){
						$temp = unserialize(base64_decode($lv['datetable']));
						foreach($temp as $tk=>$tv){
							if($tk == $tableName){
								$field1[] = $tv['orderfield']&&$tv['orderfield']!='null'?$tv['orderfield']:'';
								$field2 = $tv['dtfuncfields'];
								$field3 = array_keys($tv['list']);
								$templookup = array_filter(array_unique(array_merge($field1,$field2,$field3)));
								$needList['lookup'][$lv['id']]['title'] = $lv['title'];
								foreach($fields as $k=>$v){
									if(in_array($v,$templookup)){
										$needList['lookup'][$lv['id']]['title'] = $lv['title'];
										$needList['lookup'][$lv['id']]['fields'][] = $v;
									}
								}
							unset($templookup);
							}
						unset($temp);
						}
					}
				}
			}
			
			
			//动态建模 主表 下拉框/单、复选框 引用
			$dymsql = "SELECT p.id,m.actionname,m.actiontitle,p.fieldname,p.title,p.category,p.subimporttableobj,p.subimporttableobj,p.subimporttablefield2obj,p.treedtable,p.treeshowfield,p.treevaluefield,p.treeparentfield FROM mis_dynamic_form_propery AS p LEFT JOIN mis_dynamic_form_manage AS m ON m.id=p.formid WHERE (p.subimporttableobj='{$tableName}' AND p.subimporttablefieldobj is not null AND p.subimporttablefieldobj != '') OR (p.treedtable='{$tableName}' AND p.treeshowfield is not null AND p.treeshowfield !='') GROUP BY m.actionname,p.id";		
			$dymList = $nModel->query($dymsql);
			foreach($dymList as $mk=>$mv){
				foreach($fields as $k=>$v){
					if($mv['subimporttablefield2obj'] == $v || $mv['subimporttablefieldobj'] == $v || $mv['treeshowfield'] == $v ||$mv['treevaluefield'] == $v || $mv['treeparentfield']=$v){
						$needList['auto'][] = $dymList[$rk];
					}
				}
			}
			
			//动态建模 内嵌表 下拉框/单、复选框 引用 ---方案待定（解析加密字符串，不科学）
			$dydsql = "";
			
			//构建临时表数据
			$sqldata = array(
					'formid'=>$formid,
					'propertyid'=>$proertyid,
					'fields'=>'',
					'tabletype'=>$type,
					'quotetype'=>0,
					'quotetitle'=>'',
					'createid'=>$_SESSION[C("USER_AUTH_KEY")]
			);
			//数据入库
			if($needList){
				foreach($needList as $key=>$val){
					foreach($val as $k=>$v){
						$sqldata['fields'] = implode("|",$v['fields']);
						$sqldata['quotetitle'] = $v['title'];
						switch ($key){
							case 'dataview'://视图
								$sqldata['quotetype'] = 1;
								break;
							case 'lookup':
								$sqldata['quotetype'] = 2;
								break;
							case 'roam':
								$sqldata['quotetype'] = 3;
								break;
							case 'auto':
								$sqldata['quotetype'] = 4;
								break;
							default:
								break;
						}
						$tempModel= M("mis_system_fields_quote_temp");
						$ret = $tempModel->add($sqldata);
						if(false === $ret){
							$this->error("改变字段入临时表失败");
						}
					}
				
				}
			}
			
			
			
			
			
			
			
			//合并数据
// 			$list = array(
// 					'dataview'=>$viewtemp,
// 					'roam'=>$roamtemp,
// 					'lookup'=>$lookuptemp
// 					);
		}
// 		$tempModel = M("mis_system_data_warn");
// 		if($returnarr){
// 			//return $list;
// 			return $needList;
// 		}else{
// 			echo json_encode($needList);
// 			//echo json_encode($list);
// 		}
		
	}
	public function dtUpdateWarnHtml(){
		
		if(!$_REQUEST['jump']){
			$model = M("mis_system_fields_quote_temp");
			$map['createid'] = $_SESSION[C("USER_AUTH_KEY")];
			$map['formid'] = $_REQUEST['formid'];
			$map['propertyid'] = $_REQUEST['propertyid']?$_REQUEST['propertyid']:0;
			//$map['quotetype'] = $_REQUEST['type']?$_REQUEST['type']:$_REQUEST['propertyid']?2:1;
			$list = $model->where($map)->select();
			$quotytype = array(
					'1'=>'系统视图',
					'2'=>'lookup',
					'3'=>'数据漫游',
					'4'=>'动态建模'
					);
			$quoteeng = array(
					'1'=>'dataview',
					'2'=>'lookup',
					'3'=>'roam',
					'4'=>'auto'
					);
			$newlist = array();
			if($list){
				foreach($list as $key=>$val){
					$newlist[$quoteeng[$val['quotetype']]][$key]['title'] = $val['quotetitle'];
					$newlist[$quoteeng[$val['quotetype']]][$key]['fields'] = $val['fields'];
					$newlist[$quoteeng[$val['quotetype']]][$key]['quotetype'] = $quotytype[$val['quotetype']];
				}
			}
			$model->where($map)->delete();
			$this->commint();
			echo json_encode($newlist);
		}else{
			$data = $_POST['data'];
			$formid = $_POST['formid'];
			$propertyid = $_POST['id'];
			$reqote = array_keys($data);
			$this->assign("data",$data);
			$this->assign("formid",$formid);
			$this->assign("propertyid",$propertyid);
			$this->assign("reqote",$reqote);
			$this->display("dtUpdateWarnHtml");
		}
		
		
	}

}


//preg_replace_callback返回需要值
function lookupControllCallBackFunc($v){
	global $pidArr;
	return '#'.$pidArr[$v[0]].'#';
}

function lookupTurnformulaCallBackFunc($v){
	global $turnFormulArr;
	$v[0] = str_replace('#', '', $v[0]);
	return '#'.$turnFormulArr[$v[0]].'#';
}

function lookupControllSubCallBackFunc($v){
	global $pidArr1;
	return '['.$pidArr1[$v[0]].']';
}
function lookupControllDateCallBackFunc($v){
	global $pidArrDate;
	return '#'.$pidArrDate[$v[0]].'#';
}

