<?php

/**
 * @Title: AutoformAction
 * @Package package_name
 * @Description: todo(用一句话描述该类的作用)
 * @author quqiang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-8-18 上午11:45:58
 * @version V1.0
 */
class AutoformAction extends CommonAction{

	/**
	 * 组件私有属性列表
	 * @var array('caotegory'=>'property{}')
	 */
	private $privateProperty;

	/**
	 * 公用属性列表
	 * @var array('property'=>'val ,val')
	 */
	private $publicProperty;

	/**
	 * 组件信息列表
	 * @var array
	 */
	private $controls;
	/**
	 * 组件配置文件信息
	 * @var array
	 */
	private $controlConfig;
	/**
	 * 表单名称
	 * @var string
	 */
	private $nodeName = '';

	/**
	 * 表单标题
	 * @var string
	 */
	private $nodeTitle ='';

	/**
	 * 最终的表名
	 * @var string
	 */
	private $tableName = '';

	/**
	 * 审批流使用字段
	 * @var array
	 */
	private $auditField;
	/**
	 * 默认字段
	 * @var array
	 */
	private $defaultField;

	/**
	 * 系统使用的字段
	 * @var array
	 */
	private  $systemUserField;

	/**
	 * 当前节点
	 * @var string
	 */
	private $curNode ;
	/**
	 * 所有节点
	 * @var array
	 */
	private $allNode;

	/**
	 * 是否是审核
	 * @var boolean
	 */
	private $isaudit;

	/**
	 * 是否生成数据表
	 * @var boolean
	 */
	private	$inserttable;

	/**
	 * lookup的属性可用属性列表
	 * @var array
	 */
	private $lookupConfig;

	private $checkforConfig;

	function __construct(){
		parent::__construct();
		$dir = ROOT.'/Conf/';
		$controlls = require $dir.'controlls.php';
		$this->controlConfig = $controlls;
		require $dir.'property.php';
		$privateProperty = array();
		foreach ($controlls as $key=>$val){
			if($val['property']){
				//				$privateProperty[$key] =$val['property']; // 将公有属性合并到每个组件上
				$privateProperty[$key] = array_merge($NBM_COMMON_PROPERTY , $val['property']);
			}
			if($val['show']){
				$temp='';
				$temp['iscreate']	=	$val['iscreate'] ; //是否生成到数据库中字段
				$temp['isline'] = $val['isline']; // 一个标签是否占一行
				$temp['title']=$val['title'];
				$title = $val['title'];
				$delTag = htmlspecialchars('<a class="nnbm_delete_plain_ctl" onclick="javascript:$(this).parent().parent().remove();"></a>');
				$curTagHtml = htmlspecialchars($val['html']); // 当前组件html结构
				eval("\$curTagHtml = \"$curTagHtml\";");
				$temp['html'] =htmlspecialchars_decode($curTagHtml);
				$this->controls[]=$temp;
			}
		}

		/* look up */
		if(!is_array($this->lookupConfig)){
			$lookupMode = D('LookupObj');
			$lookupPath = $lookupMode->GetFile();
			if(file_exists($lookupPath)){
				$lookupTemp = require_once($lookupPath);
				unset($temp);
				foreach ($lookupTemp as $k=>$v){
					if($v['status']){
						$temp["{$k}"]=$v;
					}
				}
				if(count($temp)){
					$this->lookupConfig = $temp;
				}
			}
		}
		/*  look up end */
		/* checkfor  */
		if(!is_array($this->checkforConfig)){
			$checkfoMode = D('CheckForObj');
			$checkforPath = $checkfoMode->GetFile();
			if(file_exists($checkforPath)){
				$checkforTemp = require_once($checkforPath);
				unset($temp);
				foreach ($checkforTemp as $k=>$v){
					if($v['status']){
						$temp["{$k}"]=$v;
					}
				}
				if(count($temp)){
					$this->checkforConfig = $temp;
				}
			}
		}
		/*  checkfor end */

		$this->publicProperty = $NBM_COMMON_PROPERTY;
		$this->privateProperty = $privateProperty;
		$this->auditField=array(
				'ostatus'=>"varchar(100) NULL COMMENT '当前可审核流程节点清单'",
				'ptmptid'=>"int(10) NULL COMMENT '流程ID'",
				'flowid'=>"int(10) DEFAULT '0'	NULL COMMENT '自定义流程ID'",
				'auditState'=>"tinyint(1) DEFAULT '0' NULL COMMENT '待审核状态'",
				'auditUser'=>"varchar(500)	NULL COMMENT '所有审核人清单'",
				'curAuditUser'=>"varchar(300) NULL COMMENT '当前可审核人清单'",
				'alreadyAuditUser'=>"varchar(500) NULL COMMENT '所有已审核人'",
				'alreadyauditnode'=>"varchar(200) DEFAULT '0' NULL COMMENT '当前并行时已审核节点'",
				'curNodeUser'=>"varchar(300) NULL COMMENT '当前待审核人清单'",
				'noderule'=>"varchar(500) NULL COMMENT '所有节点规则'",
				'noderuleinfo'=>"varchar(100) NULL COMMENT '所有节点规则描述'",
				'allnode'=>"varchar(100) NULL COMMENT '所有流程节点'",
				'informpersonid'=>"varchar(500) 	NULL COMMENT '节点知道会人'"
				);

				$this->defaultField=array(
				'orderno'=>"varchar(100) NULL COMMENT '编号'",
				'status'=>"tinyint(1) DEFAULT '1'  COMMENT '状态'",
				'companyid'=>"int(10) DEFAULT '0' NULL COMMENT '公司ID'",
				'createid'=>"int(10) NULL COMMENT '创建人ID'",
				'operateid'=>"int(10) DEFAULT '0' NULL COMMENT '操作人ID'",
				'createtime'=>"int(11) NULL COMMENT '创建时间'",
				'updatetime'=>"int(11)  NULL COMMENT '修改时间'",
				'updateid'=>"int(10) NULL COMMENT '修改人ID'"
				);

				$reservedField = array();
				foreach ($this->defaultField as $k=>$v){
					$reservedField[]=$k;
				}
				foreach ($this->auditField as $k=>$v){
					$reservedField[]=$k;
				}
				$reservedField[]='id';
				$this->systemUserField = $reservedField;
				$this->assign('reserved',json_encode($reservedField));
				$this->assign('lookupConfig',json_encode($this->lookupConfig));
				$this->assign('checkforConfig',json_encode($this->checkforConfig));
				$this->nodeName = ucfirst($_POST['nodename']);
				$this->nodeTitle = $_POST['nodetitle'];
				$this->tableName = strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $this->nodeName), "_"));

				$this->curNode = $_REQUEST['curnode']?$_REQUEST['curnode']:'index';
				$this->allNode = array('index','node1','node2','node3');

				$this->isaudit = $_POST['isaudit']?true:false;
				$this->inserttable = $_POST['inserttable']?true:false;
					
				$this->assign('curnode',$this->curNode);
				$this->assign('allnode',$this->allNode);
	}
	/*******************************************************************************/
	/*******************************	业务控制		********************************/
	/*******************************************************************************/

	/**
	 * @Title: index
	 * @Description: todo(添加新表单加载页)
	 * @author quqiang
	 * @date 2014-8-18 下午4:52:13
	 * @throws
	 */
	public function index(){
			
		$this->assign('reservedField',$this->systemUserField);
		$this->assign('NBM_COMMON_PROPERTY',$this->publicProperty);
		$this->assign('NBM_COMMON_PROPERTY_JSON',json_encode($this->publicProperty));
		$this->assign('NBM_PRIVATE_PROPERTY_JSON',json_encode($this->privateProperty));
		$this->assign('controlls',$this->controls);
		$this->getNodeList();
		$this->display('MisDynamicFormManage:autoformindex');
	}

	/**
	 * @Title: add
	 * @Description: todo(数据添加控制器)
	 * @author quqiang
	 * @date 2014-8-18 下午4:52:52
	 * @throws
	 */
	function add(){
		$controlProperty = $this->getParame();

		// 更新配置文件
		$this->modifyConfig($controlProperty['visibility'],$this->nodeName,$this->isaudit);
		// 生成组件配置文件
		$this->createAutoFormConfig($this->getParame() , $_POST['curNode']);
		//		$this->createDatabase($controlProperty['all']);
		// 生成数据库
		$this->craeteDatabaseAppend($controlProperty['all'],true);
		//组件修改
		self::insertFormManager($this->getParame());
		//生成Action 文件
		$this->createAction($controlProperty, $this->nodeName ,false,$this->isaudit);
		//生成Model 文件
		$this->createModel($controlProperty, $this->nodeName , false , $this->isaudit);
		//生成模板
		$this->createTemplate($controlProperty['visibility'] , $this->nodeName , $this->isaudit);

		$this->success('添加成功');
	}

	/**
	 * (non-PHPdoc)
	 * @see Admin/Lib/Action/CommonAction::edit()
	 */
	function edit(){
		if(!$_POST){
			$id = $_GET['id'];
			$model = D('MisDynamicFormManage');
			$filedModel = M('MisDynamicFormField');
			$formInfo = $model->where('id='.$id)->find();
			$this->nodeName = $formInfo['actionname'];
			$this->nodeTitle = $formInfo['actiontitle'];
			$this->assign('action',$this->nodeName);
			// 现有组件
			$data = $filedModel->where('formid='.$id)->select();
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
		}else{
			$controlProperty = $this->getParame();
			// 更新配置文件
			$this->modifyConfig($controlProperty['visibility'],$this->nodeName,$this->isaudit);
			// 生成组件配置文件
			$this->createAutoFormConfig($this->getParame() , $_POST['curnode']);
			// 生成数据库
			$this->craeteDatabaseAppend($controlProperty['all']);
			//组件修改
			self::modifyFormManager($this->getParame());
			//生成Action 文件
			$this->createAction($controlProperty, $this->nodeName ,false,$this->isaudit);
			//生成Model 文件
			$this->createModel($controlProperty, $this->nodeName , false , $this->isaudit);
			//生成模板
			$this->createTemplate($controlProperty['visibility'] , $this->nodeName , $this->isaudit);
			$this->success('修改成功');
		}
	}

	/**
	 * 显示节点页面【都是带审批流】
	 * @param string $node 节点名称
	 */
	function showNode($action , $node , $isedit){


		$action = $action?$action:$_GET['action'];
		$node = $node?$node:$_GET['node'];
		$isedit = $isedit ? $isedit : $_GET['isedit'];
		$action = ucfirst($action);
		$tplname= $action."_".$node."_"."add";
		if(!$isedit){
			$obj = A($action);
			$modelname = $action;
			$this->getSystemConfigDetail($modelname);
			$model = D($modelname);
			$qx_name = $modelname;
			if(substr($modelname, -4)=="View"){
				$qx_name = substr($modelname,0, -4);
			}
			$map['status']=1;
			if (method_exists ( $obj, '_filter' )) {
				$obj->_filter ( $map );
			}
			// 上一条数据ID
			$updataid = $model->where($map)->order('id desc')->getField('id');
			$obj->assign("updataid",$updataid);
			$mrdmodel = D('MisRuntimeData');
			$data = $mrdmodel->getRuntimeCache($modelname,'add');
			$obj->assign("vo",$data);
			//此处添加_after_add方法，方便在方法中，判断跳转
			if (method_exists($obj,"_before_add")) {
				call_user_func(array(&$obj,"_before_add"),$data);
			}
			$obj->display($action.':'.$tplname);
		}else{
			$tplname= $action."_".$node."_"."edit";
			$obj = A($action);
			/*****************/
			$name=$action;
			$model = D ( $name );
			$id = $_REQUEST [$model->getPk ()];
			$map['id']=$id;
			if ($_SESSION["a"] != 1) $map['status'] = 1;
			$vo = $model->where($map)->find();
			if(empty($vo)){
				$this->display ("Public:404");
				exit;
			}
			//读取动态配制
			$this->getSystemConfigDetail($name);
			//扩展工具栏操作
			$scdmodel = D('SystemConfigDetail');
			$toolbarextension = $scdmodel->getSubDetail($modelname,true,'toolbar');
			if ($toolbarextension) {
				$this->assign ( 'toolbarextension', $toolbarextension );
			}
			// 上一条数据ID
			$map['id'] = array("lt",$id);
			$updataid = $model->where($map)->order('id desc')->getField('id');
			$this->assign("updataid",$updataid);
			// 下一条数据ID
			$map['id'] = array("gt",$id);
			$downdataid = $model->where($map)->getField('id');
			$this->assign("downdataid",$downdataid);
			//判断系统授权
			//$vo=$this->process_filter($vo);
			//lookup带参数查询
			$module=A($name);
			if (method_exists($module,"_after_edit")) {
				call_user_func(array(&$module,"_after_edit"),&$vo);
			}
			$this->assign( 'vo', $vo );
			/*************************/
			$obj->display($action.':'.$tplname);
		}
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
			$this->assign('id',$id);
			$this->assign('check',$check);
			$this->assign('container',$container);
			$this->assign('tagIndetity',$_GET['tagIndetity']); // 属性查找标识
			$configPath = $this->getAutoFormConfig();
			if(file_exists($configPath)){
				$data = require $this->getAutoFormConfig();
				//$data = $aryRule;
			}
			$tem = $data[$name][$id];
			$obj = json_decode(htmlspecialchars_decode($tem));
			foreach ($obj[0] as $k=>$v){
				foreach ($v as $k1=>$v1){
					$filed[$k1]=$v1;
				}
				$data1[]=$filed;
			}
			$this->assign('tabledata',$data1);
			$this->display('MisDynamicFormManage:field');
		}else{
			// 内嵌表字段信息
			$arr=array();
			if($_POST['items']){
				$field;
				foreach($_POST['items'] as $key=>$val){
					$field[$val['fieldname']]=$val;
				}
				$arr[] = $field;
			}
			$this->success('操作成功','',json_encode($arr));
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

	/*******************************************************************************/
	/*******************************	业务控制	end	********************************/
	/*******************************************************************************/


	/**************************			函数			*********************************/

	/**
	 * @Title: createAction
	 * @Description: todo(生成Action代码)
	 * @param array $fieldData 组件属性列表
	 * @param string $cotrollname 控制器名称
	 * @param boolean $add 是否覆盖现有文件，默认为false:需要覆盖
	 * @param boolean $isaudit 是否带审批流，默认为false:不带审批流
	 * @author quqiang
	 * @date 2014-8-21 上午10:03:39
	 * @throws
	 */
	private function createAction($fieldData , $cotrollname,$add=false ,$isaudit=false){
		$cotrollname=ucfirst($cotrollname);
		$actionPath =  LIB_PATH."Action/".$cotrollname."Action.class.php";
		$isExist = (file_exists($actionPath));
		if( $isExist && $add){
			$this->error($actionPath."文件已存在!");
		}
		$phpcode.="<?php\r\n/**";
		$phpcode.="\r\n * @Title: {$cotrollname}Action";
		$phpcode.="\r\n * @Package package_name";
		$phpcode.="\r\n * @Description: todo(动态表单_自动生成-".$this->nodeTitle.")";
		$phpcode.="\r\n * @author ".$_SESSION['loginUserName'];
		$phpcode.="\r\n * @company 重庆特米洛科技有限公司";
		$phpcode.="\r\n * @copyright 本文件归属于重庆特米洛科技有限公司";
		$phpcode.="\r\n * @date ".date('Y-m-d H:i:s');
		$phpcode.="\r\n * @version V1.0";
		$phpcode.="\r\n*/";
		$phpcode.="\r\nclass ";
		if( $isaudit ){
			$jcc="CommonAuditAction";
		}else{
			$jcc="CommonAction";
		}

		$phpcode.= $cotrollname."Action extends ".$jcc." {\r\n";

		$phpcode.="\tpublic function _filter(&\$map){";//start filter
		$phpcode.="\r\n\t\tif (\$_SESSION[\"a\"] != 1)\r\n\t\t\t\$map['status']=array(\"gt\",-1);";
		$phpcode .="\r\n\t}";
		// 默认生成 前置函数
		$before_edit ='';
		$before_edit .="\r\n\t/**";
		$before_edit .="\r\n\t * @Title: _before_edit";
		$before_edit .="\r\n\t * @Description: todo(前置编辑函数)";
		$before_edit .="\r\n\t * @author ".$_SESSION['loginUserName'];
		$before_edit .="\r\n\t * @date ".date('Y-m-d H:i:s');
		$before_edit .="\r\n\t * @throws ";
		$before_edit .="\r\n\t*/";
		$before_edit .="\r\n\tfunction _before_edit(){";

		$before_insert='';
		$before_insert.="\r\n\t/**";
		$before_insert.="\r\n\t * @Title: _before_insert";
		$before_insert.="\r\n\t * @Description: todo(前置添加函数)";
		$before_insert.="\r\n\t * @author ".$_SESSION['loginUserName'];
		$before_insert.="\r\n\t * @date ".date('Y-m-d H:i:s');
		$before_insert.="\r\n\t * @throws ";
		$before_insert.="\r\n\t*/";
		$before_insert.="\r\n\tfunction _before_insert(){";

		$before_update = '';
		$before_update .= "\r\n\t/**";
		$before_update .= "\r\n\t * @Title: _before_update";
		$before_update .= "\r\n\t * @Description: todo(前置修改函数)  ";
		$before_update .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$before_update .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$before_update .= "\r\n\t * @throws";
		$before_update .= "\r\n\t*/";
		$before_update .= "\r\n\tfunction _before_update(){";

		// 默认生成后置函数
		$after_edit = '';
		$after_list ='';
		$after_insert ='';
		$before_add='';
		$after_update='';

		// 删除子表数据
		$delChildData='';
		$is_create_del_child = false;
		$is_create_opraete_child = false;
		$is_create_modify_child = false;
		$is_create_insert_child = false;

		// 实例化MODE
		$model_code = '';
		$is_include_model = false;

		$after_edit .="\r\n\t/**";
		$after_edit .="\r\n\t * @Title: _after_edit";
		$after_edit .="\r\n\t * @Description: todo(后置编辑函数)";
		$after_edit .="\r\n\t * @author ".$_SESSION['loginUserName'];
		$after_edit .="\r\n\t * @date ".date('Y-m-d H:i:s');
		$after_edit .="\r\n\t * @throws ";
		$after_edit .="\r\n\t*/";
		$after_edit .="\r\n\tfunction _after_edit(\$vo){";

		$after_list.="\r\n\t/**";
		$after_list.="\r\n\t * @Title: _after_list";
		$after_list.="\r\n\t * @Description: todo(前置List)";
		$after_list.="\r\n\t * @author ".$_SESSION['loginUserName'];
		$after_list.="\r\n\t * @date ".date('Y-m-d H:i:s');
		$after_list.="\r\n\t * @throws ";
		$after_list.="\r\n\t*/";
		$after_list.="\r\n\tfunction _after_list(){";

		$after_insert .= "\r\n\t/**";
		$after_insert .= "\r\n\t * @Title: _after_insert";
		$after_insert .= "\r\n\t * @Description: todo(后置insert函数)  ";
		$after_insert .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$after_insert .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$after_insert .= "\r\n\t * @throws";
		$after_insert .= "\r\n\t*/";
		$after_insert .= "\r\n\tfunction _after_insert(\$id){";

		$before_add .= "\r\n\t/**";
		$before_add .= "\r\n\t * @Title: _before_add";
		$before_add .= "\r\n\t * @Description: todo(前置add函数)  ";
		$before_add .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$before_add .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$before_add .= "\r\n\t * @throws";
		$before_add .= "\r\n\t*/";
		$before_add .= "\r\n\tfunction _before_add(){";

		$after_update .= "\r\n\t/**";
		$after_update .= "\r\n\t * @Title: _after_update";
		$after_update .= "\r\n\t * @Description: todo(后置update函数)  ";
		$after_update .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$after_update .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$after_update .= "\r\n\t * @throws";
		$after_update .= "\r\n\t*/";
		$after_update .= "\r\n\tfunction _after_update(){";


		// 审批流
		if($isaudit){
		}
		foreach ($fieldData['visibility'] as $k=>$v){
			$property = $this->getProperty($v['catalog']);
			if($v[$property['catalog']['name']] == 'upload'){
				$after_edit		.= "\r\n\t\t\$this->getAttachedRecordList(\$vo['id']);";

				$after_update 	.= "\r\n\t\t\$id=\$_REQUEST['id'];";
				$after_update 	.= "\r\n\t\t\$this->swf_upload(\$id);";

				$after_insert	.= "\r\n\t\t\$this->swf_upload(\$id);";
			}
			if($v[$property['catalog']['name']] == 'checkbox'){
				$after_edit .="\r\n\t\tif(\$vo['".$v[$property['fields']['name']]."'])\n\t\t\t\$vo[\"".$v[$property['fields']['name']]."\"]=explode(',',\$vo[\"".$v[$property['fields']['name']]."\"]);";
			}
			if($v[$property['catalog']['name']] == 'datatable'){
				// 删除子表数据
				if(!$is_create_del_child){
					$delChildData .= "\r\n\t/**";
					$delChildData .= "\r\n\t * @Title: delsubinfo";
					$delChildData .= "\r\n\t * @Description: todo(子表数据删除)  ";
					$delChildData .= "\r\n\t * @author ".$_SESSION['loginUserName'];
					$delChildData .= "\r\n\t * @date ".date('Y-m-d H:i:s');
					$delChildData .= "\r\n\t * @parame \$_POST['table'] 表名";
					$delChildData .= "\r\n\t * @parame \$_POST['id'] 数据ID值";
					$delChildData .= "\r\n\t * @throws";
					$delChildData .= "\r\n\t*/";
					$delChildData.="\r\n\tfunction delsubinfo(){";
					$delChildData.="\r\n\t	\$table = \$_POST['table'];";
					$delChildData.="\r\n\t	\$id = intval(\$_POST['id']);";
					$delChildData.="\r\n\t	if(\$table){";
					$delChildData.="\r\n\t		\$model = M(\$table);";
					$delChildData.="\r\n\t		\$map['id'] = array('eq',\$id);";
					$delChildData.="\r\n\t		\$model->where(\$map)->delete();";
					$delChildData.="\r\n\t		\$this->success('数据成功删除');";
					$delChildData.="\r\n\t	}";
					$delChildData.="\r\n\t}";
					$is_create_del_child = true;
				}
				//if(!$is_create_opraete_child){
				$after_edit .="\r\n\t\t// 内嵌表处理".$v[$property['fields']['name']];
				$after_edit .="\r\n\t\t\$innerTabelObj".$v[$property['fields']['name']]." = M('".$this->tableName.'_sub_'.$v[$property['fields']['name']]."');"; // 内嵌表名 生成规则： 主表名称_sub_组件名称
				$after_edit .="\r\n\t\t\$innerTabelObj".$v[$property['fields']['name']]."Data = \$innerTabelObj".$v[$property['fields']['name']]."->where('masid='.\$vo['id'])->select();";
				$after_edit .="\r\n\t\t\$this->assign(\"innerTabelObj".$v[$property['fields']['name']]."Data\",\$innerTabelObj".$v[$property['fields']['name']]."Data);";
				//$is_create_opraete_child = true;
				//}

				if(!$is_create_modify_child){
					$after_update .="\r\n\t\t// 内嵌表数据添加处理";
					$after_update .="\r\n\t\t\$datatablefiexname =\"{$this->tableName}_sub_\";";
					$after_update .="\r\n\t\t\$insertData = array();// 数据添加缓存集合";
					$after_update .="\r\n\t\t\$updateData = array();// 数据修改缓存集合";
					$after_update .="\r\n\t\tif(\$_POST['datatable']){";
					$after_update .="\r\n\t\t	foreach(\$_POST['datatable'] as \$key=>\$val){";
					$after_update .="\r\n\t\t		foreach(\$val as \$k=>\$v){";
					$after_update .="\r\n\t\t			if(\$v['id']){";
					$after_update .="\r\n\t\t				\$updateData[\$k][]=\$v;";
					$after_update .="\r\n\t\t			}else{";
					$after_update .="\r\n\t\t				\$insertData[\$k][]=\$v;";
					$after_update .="\r\n\t\t			}";
					$after_update .="\r\n\t\t		}";
					$after_update .="\r\n\t\t	}";
					$after_update .="\r\n\t\t}";
					$after_update .="\r\n\t\t//数据处理";
					$after_update .="\r\n\t\tif(\$insertData){";
					$after_update .="\r\n\t\t	foreach(\$insertData as \$k=>\$v){";
					$after_update .="\r\n\t\t		\$model = M(\$datatablefiexname.\$k);";
					$after_update .="\r\n\t\t		foreach(\$v as \$key=>\$val){";
					$after_update .="\r\n\t\t			\$val['masid'] = \$_POST['id'];";
					$after_update .="\r\n\t\t			\$model->add(\$val);";
					$after_update .="\r\n\t\t		}";
					$after_update .="\r\n\t\t	}";
					$after_update .="\r\n\t\t}";
					$after_update .="\r\n\t\tif(\$updateData){";
					$after_update .="\r\n\t\t	foreach(\$updateData as \$k=>\$v){";
					$after_update .="\r\n\t\t		\$model = M(\$datatablefiexname.\$k);";
					$after_update .="\r\n\t\t		foreach(\$v as \$key=>\$val){";
					$after_update .="\r\n\t\t			\$model->save(\$val);";
					$after_update .="\r\n\t\t		}";
					$after_update .="\r\n\t\t	}";
					$after_update .="\r\n\t\t}";
					$is_create_modify_child = true;
				}

				if(!$is_create_insert_child){
					$after_insert.="\r\n\t\t// 内嵌表数据添加处理";
					$after_insert.="\r\n\t\t\$datatablefiexname =\"{$this->tableName}_sub_\";";
					$after_insert.="\r\n\t\t\$insertData = array();// 数据添加缓存集合";
					$after_insert.="\r\n\t\tif(\$_POST['datatable']){";
					$after_insert.="\r\n\t\t	foreach(\$_POST['datatable'] as \$key=>\$val){";
					$after_insert.="\r\n\t\t		foreach(\$val as \$k=>\$v){";
					$after_insert.="\r\n\t\t			\$insertData[\$k][]=\$v;";
					$after_insert.="\r\n\t\t		}";
					$after_insert.="\r\n\t\t	}";
					$after_insert.="\r\n\t\t}";
					$after_insert.="\r\n\t\t//数据处理";
					$after_insert.="\r\n\t\tif(\$insertData){";
					$after_insert.="\r\n\t\t	foreach(\$insertData as \$k=>\$v){";
					$after_insert.="\r\n\t\t		\$model = M(\$datatablefiexname.\$k);";
					$after_insert.="\r\n\t\t		foreach(\$v as \$key=>\$val){";
					$after_insert.="\r\n\t\t			\$val['masid'] = \$id;";
					$after_insert.="\r\n\t\t			\$model->add(\$val);";
					$after_insert.="\r\n\t\t		}";
					$after_insert.="\r\n\t\t	}";
					$after_insert.="\r\n\t\t}";
					$is_create_insert_child = true;
				}
			}


			if($v[$property['subimporttableobj']['name']] ){
				$condition = '';
				if($v[$property['conditions']['name']]){
					$condition = "->where('{$v[$property['conditions']['name']]}')";
				}

				$before_add.= "\r\n\t\t\$model=M(\"".$v[$property['subimporttableobj']['name']]."\");";
				$before_add.= "\r\n\t\t\$list{$v[$property['fields']['name']]} =\$model->where('status=1')->field(\"".$v[$property['subimporttablefieldobj']['name']].",".$v[$property['subimporttablefield2obj']['name']]."\"){$condition}->select();";
				$before_add.="\r\n\t\t\$this->assign(\"".$v[$property['fields']['name']]."list\"".",\$list{$v[$property['fields']['name']]});";

				$after_edit .="\r\n\t\t\$model=M(\"{$v[$property['subimporttableobj']['name']]}\");";
				$after_edit .="\r\n\t\t\$list{$v[$property['fields']['name']]} =\$model->where('status=1')->field(\"".$v[$property['subimporttablefieldobj']['name']].",".$v[$property['subimporttablefield2obj']['name']]."\"){$condition}->select();";
				$after_edit .="\r\n\t\t\$this->assign(\"".$v[$property['fields']['name']]."list\"".",\$list{$v[$property['fields']['name']]});";
			}else{
				if($v[$property['showoption']['name']]){
					if(!$is_include_model){
						$model_code = "\r\n\t\t\$model=D('Selectlist');";
						$is_include_model = true;
					}
					$after_edit .=$model_code. "\r\n\t\t\$selectlis = \$model->GetRules('{$v['showoption']}');";
					$after_edit .="\r\n\t\t\$selectlist{$v[$property['fields']['name']]}=array();";
					$after_edit .="\r\n\t\tforeach(\$selectlis['{$v['showoption']}'] as \$k=>\$v){";
					$after_edit .="\r\n\t\t\t\$temp['key']=\$k;";
					$after_edit .="\r\n\t\t\t\$temp['val']=\$v;";
					$after_edit .="\r\n\t\t\t\$selectlist{$v[$property['fields']['name']]}[]=\$temp;";
					$after_edit .="\r\n\t\t}";
					$after_edit .="\r\n\t\t\$this->assign(\"selectlist{$v[$property['fields']['name']]}\",\$selectlist{$v[$property['fields']['name']]});";

					$before_add .=$model_code."\r\n\t\t\$selectlis = \$model->GetRules('{$v['showoption']}');";
					$before_add .="\r\n\t\t\$selectlist{$v[$property['fields']['name']]}=array();";
					$before_add .="\r\n\t\tforeach(\$selectlis['{$v['showoption']}'] as \$k=>\$v){";
					$before_add .="\r\n\t\t\t\$temp['key']=\$k;";
					$before_add .="\r\n\t\t\t\$temp['val']=\$v;";
					$before_add .="\r\n\t\t\t\$selectlist{$v[$property['fields']['name']]}[]=\$temp;";
					$before_add .="\r\n\t\t}";
					$before_add .="\r\n\t\t\$this->assign(\"selectlist{$v[$property['fields']['name']]}\",\$selectlist{$v[$property['fields']['name']]});";
				}
			}

		}

		$before_edit.="\r\n\t}";
		$before_insert.="\r\n\t}";
		$before_update.="\r\n\t}";

		$after_edit .="\r\n\t}";
		$after_list .="\r\n\t}";
		$after_insert .="\r\n\t}";
		$before_add .="\r\n\t}";
		$after_update .="\r\n\t}";

		//$phpcode.=$before_edit.$before_insert.$before_update.$after_edit.$after_list.$after_insert.$after_add.$after_update;
		$phpcode.=$before_edit.$before_insert.$before_update.$after_edit.$after_insert.$before_add.$after_update.$delChildData;
		$phpcode.="\r\n}\r\n?>";

		if(!is_dir(dirname($actionPath))) mk_dir(dirname($actionPath),0777);
		if( false === file_put_contents( $actionPath , $phpcode )){
			$this->error ("控制器文件生成失败!");
		}
		//return $phpcode;
	}

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
	private function createModel($fieldData , $modelname ,$add=false ,$isaudit=false){
		$modelname=ucfirst($modelname);
		$truetablename=strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $modelname), "_"));
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
			$autohtml1.="\n\t\tarray(\"createid\",\"getMemberId\",self::MODEL_UPDATE,\"callback\"),";
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

		$phpcode.="<?php\r\n/**";
		$phpcode.="\r\n * @Title: {$modelname}Model";
		$phpcode.="\r\n * @Package package_name";
		$phpcode.="\r\n * @Description: todo(动态表单_自动生成-".$this->nodeTitle.")";
		$phpcode.="\r\n * @author ".$_SESSION['loginUserName'];
		$phpcode.="\r\n * @company 重庆特米洛科技有限公司";
		$phpcode.="\r\n * @copyright 本文件归属于重庆特米洛科技有限公司";
		$phpcode.="\r\n * @date ".date('Y-m-d H:i:s');
		$phpcode.="\r\n * @version V1.0";
		$phpcode.="\r\n*/";
		$phpcode.="\r\nclass ";
		$phpcode.= $modelname."Model extends CommonModel {\r\n\t";
		$phpcode.="protected \$trueTableName = '".$truetablename."';";
		$hasvalidate=false;
		$validate="";
		$i=1;
		$j=count($fieldData['visibility']);
		foreach($fieldData['visibility'] as $k=>$v){
			$property = $this->getProperty($v['catalog']);
			if( $v["checkexist"] ){
				if( !$hasvalidate ){
					$validate.="\n\tpublic \$_validate=array(\r";//start filter
					$hasvalidate=true;
				}
				$validate.="\n\t\tarray('".$v[$property['fields']['name']]."','','".$v[$property['title']['name']]."已经存在',self::EXISTS_VAILIDATE,'unique',self::MODEL_BOTH),";
			}

			if( $v["checkfunc"]=="required" ){
				if( !$hasvalidate ){
					$validate.="\n\tpublic \$_validate=array(\r";//start filter
					$hasvalidate=true;
				}
				$validate.="\n\t\tarray('".$v[$property['fields']['name']]."','require','".$v[$property['title']['name']]."必须'),";
			}
			if($i==$j && $hasvalidate ){
				$validate.="\r\n\t);";
			}
			if($v['catalog'] == 'date'){
				$autohtml1 .= "\n\t\tarray('{$v[$property['fields']['name']]}','strtotime',self::MODEL_BOTH,'function'),";
			}

			if($v['catalog'] == 'checkbox'){
				$autohtml1 .= "\n\t\tarray('{$v[$property['fields']['name']]}','arrayToString',self::MODEL_BOTH,'callback'),";
			}

			$i++;
		}
		$autohtml1.="\n\t);";
		$autohtml=$autohtml1;

		$phpcode.=$autohtml.$validate;
		$phpcode.="\r\n}\r\n?>";

		if(!is_dir(dirname($modelPath))) mk_dir(dirname($modelPath),0777);
		if( false === file_put_contents( $modelPath , $phpcode )){
			$this->error ("控制器文件生成失败!");
		}
	}

	/**
	 * @Title: createTemplate
	 * @Description: todo(生成模板物理文件)
	 * @param array $fieldData	组件属性列表
	 * @param string $filedPathName 存储文件夹名称
	 * @param boolean $isaudit 是否带审批流，默认为false:不带审批流
	 * @author quqiang
	 * @date 2014-8-21 上午10:11:59
	 * @throws
	 */
	private function createTemplate($fieldData ,$filedPathName,$isaudit=false){

		$f=ucfirst($filedPathName);
		// 复选框样式
		$styles = <<<EOF
		<style>
		.tml-form-row label.tmp_label{margin-left:0px;width: 120px;float: none;font: 14px/30px "Microsoft Yahei","微软雅黑",sans-serif;}
		.tml-form-row label.tmp_label:hover{color:#006699;}
		</style>
EOF;
		//生成公共页面 dwzloadindex.html
		$dynamic_dwzloadindex = $this->getDynamicDwzloadindexHtml();
		$file = TMPL_PATH.C('DEFAULT_THEME')."/".$f."/dwzloadindex.html";
		if(!is_dir(dirname($file))) mk_dir(dirname($file),0777);
		if( false === file_put_contents( $file , $dynamic_dwzloadindex )){
			$this->error ( "dwzloadindex.html生成失败");
		}

		// 生成节点模板 , xxadd.html		xxxedit.html
		// 节点模板的生成全是 审批流

		$configPath = $this->getAutoFormConfig($this->curNode);
		$fieldNodeData = '';
		if(file_exists($configPath)){
			$fieldNodeData = require_once($configPath);
			//			$fieldNodeData = $aryRule;
		}
		$node_add = $styles.$this->getAddHtmlTemplate($fieldNodeData,0,1);
		$file = TMPL_PATH.C('DEFAULT_THEME')."/".$f."/".$this->nodeName."_".$this->curNode."_add.html";
		if(!is_dir(dirname($file))) mk_dir(dirname($file),0777);
		if( false === file_put_contents( $file , $node_add )){
			$this->error ( "node_add.html生成失败");
		}
		$node_edit = $styles.$this->getAddHtmlTemplate($fieldNodeData,0,2);
		$file = TMPL_PATH.C('DEFAULT_THEME')."/".$f."/".$this->nodeName."_".$this->curNode."_edit.html";
		if(!is_dir(dirname($file))) mk_dir(dirname($file),0777);
		if( false === file_put_contents( $file , $node_edit )){
			$this->error ( "node_edit.html生成失败");
		}
		/******************************************************/

		if($isaudit){
			/**************************
			 *  审核 7个
			 **************************/
			// add.html
			$dynamic_add = $styles.$this->getAddHtmlTemplate($fieldData,0,1);
			$file = TMPL_PATH.C('DEFAULT_THEME')."/".$f."/add.html";
			if(!is_dir(dirname($file))) mk_dir(dirname($file),0777);
			if( false === file_put_contents( $file , $dynamic_add )){
				$this->error ( "add.html生成失败");
			}
			// edit.html
			$dynamic_edit = $styles.$this->getAddHtmlTemplate($fieldData,0,2);
			$file = TMPL_PATH.C('DEFAULT_THEME')."/".$f."/edit.html";
			if(!is_dir(dirname($file))) mk_dir(dirname($file),0777);
			if( false === file_put_contents( $file , $dynamic_edit )){
				$this->error ( "edit.html生成失败");
			}
			// indexView.html
			$dynamic_indeview = $this->getIndexviewHtml($isaudit);
			$file = TMPL_PATH.C('DEFAULT_THEME')."/".$f."/indexview.html";
			if(!is_dir(dirname($file))) mk_dir(dirname($file),0777);
			if( false === file_put_contents( $file , $dynamic_indeview )){
				$this->error ( "indexview.html生成失败");
			}

			// view.html
			$dynamic_view = $styles.$this->getAddHtmlTemplate($fieldData,0,1);
			$file = TMPL_PATH.C('DEFAULT_THEME')."/".$f."/view.html";
			if(!is_dir(dirname($file))) mk_dir(dirname($file),0777);
			if( false === file_put_contents( $file , $dynamic_view )){
				$this->error ( "view.html生成失败");
			}
			// auditIndex.html
			$dynamic_auditindex = $this->createAuditIndexhtml($fieldData,$nodename);
			$file = TMPL_PATH.C('DEFAULT_THEME')."/".$f."/auditIndex.html";
			if(!is_dir(dirname($file))) mk_dir(dirname($file),0777);
			if( false === file_put_contents( $file , $dynamic_auditindex )){
				$this->error ( "auditIndex.html生成失败");
			}
			// auditView.html
			$dynamic_auditView = $styles.$this->getAddHtmlTemplate($fieldData,0,4);
			$file = TMPL_PATH.C('DEFAULT_THEME')."/".$f."/auditView.html";
			if(!is_dir(dirname($file))) mk_dir(dirname($file),0777);
			if( false === file_put_contents( $file , $dynamic_auditView )){
				$this->error ( "auditView.html生成失败");
			}
			// auditEdit.html
			$dynamic_auditEdit = $styles.$this->getAddHtmlTemplate($fieldData,0,3);
			$file = TMPL_PATH.C('DEFAULT_THEME')."/".$f."/auditEdit.html";
			if(!is_dir(dirname($file))) mk_dir(dirname($file),0777);
			if( false === file_put_contents( $file , $dynamic_auditEdit )){
				$this->error ( "auditEdit.html生成失败");
			}

		}else{
			/**************************
			 *  非审核 4个
			 *************************/
			// add.html
			$dynamic_add = $styles.$this->getAddHtmlTemplate($fieldData,0,0);
			$file = TMPL_PATH.C('DEFAULT_THEME')."/".$f."/add.html";
			if(!is_dir(dirname($file))) mk_dir(dirname($file),0777);
			if( false === file_put_contents( $file , $dynamic_add )){
				$this->error ( "auditEdit.html生成失败");
			}
			// edit.html
			$dynamic_edit = $styles.$this->getAddHtmlTemplate($fieldData,1,0);
			$file = TMPL_PATH.C('DEFAULT_THEME')."/".$f."/edit.html";
			if(!is_dir(dirname($file))) mk_dir(dirname($file),0777);
			if( false === file_put_contents( $file , $dynamic_edit )){
				$this->error ( "auditEdit.html生成失败");
			}
			// view.html
			$dynamic_view = $styles.$this->getAddHtmlTemplate($fieldData,2,0);
			$file = TMPL_PATH.C('DEFAULT_THEME')."/".$f."/view.html";
			if(!is_dir(dirname($file))) mk_dir(dirname($file),0777);
			if( false === file_put_contents( $file , $dynamic_view )){
				$this->error ( "auditEdit.html生成失败");
			}
			// index.html
			$dynamic_index = $styles.$this->getIndexviewHtml( $isaudit);
			$file = TMPL_PATH.C('DEFAULT_THEME')."/".$f."/index.html";
			if(!is_dir(dirname($file))) mk_dir(dirname($file),0777);
			if( false === file_put_contents( $file , $dynamic_index )){
				$this->error ( "index.html生成失败");
			}
		}
	}

	/**
	 * @Title: createDatabase
	 * @Description: todo(生成数据库)
	 * @param array $fieldData 组件属性列表
	 * @author quqiang
	 * @date 2014-8-21 上午11:55:05
	 * @throws
	 */
	private function createDatabase($fieldData){
		// 先生成主表
		// 再生成内嵌表。
		$controllProperty = $this->controlConfig;
		$privateProperty = $this->privateProperty;
		$publicProperty = $this->publicProperty;
		// 内嵌表缓存数组
		$inserTables=array();

		if($this->inserttable){
			$reatetable_html_s .= "DROP TABLE IF EXISTS `".$this->tableName;
			$ceatetable_html_s .= "\r\nCREATE TABLE IF NOT EXISTS `".$this->tableName."` \r\n(`id` int(11) NOT NULL AUTO_INCREMENT  COMMENT 'ID'";
			$ceatetable_html_e="\r\n\t,PRIMARY KEY (`id`)";
			$ceatetable_html="";
			foreach($fieldData as $k=>$v ){
				$property = $this->getProperty($controllProperty['catalog']);
				$iscreateDB = $controllProperty[$v[$publicProperty['catalog']['name']]]['iscreate'];
				if($iscreateDB){
					if($v[$publicProperty['catalog']['name']]=="text"){
						if( !$v[$publicProperty['tabletype']['name']] )
						$v[$publicProperty['tabletype']['name']]="varchar";
						if( !$v[$publicProperty['length']['name']] )
						$v[$publicProperty['length']['name']]="100";
					}else if($v[$publicProperty['catalog']['name']]=="date"){
						if( !$v[$publicProperty['tabletype']['name']] )
						$v[$publicProperty['tabletype']['name']]="int";
						if( !$v[$publicProperty['length']['name']] )
						$v[$publicProperty['length']['name']]="11";
					}else if($v[$publicProperty['catalog']['name']]=="textarea" || $v[$publicProperty['catalog']['name']]=="checkbox"){
						if( !$v[$publicProperty['tabletype']['name']] )
						$v[$publicProperty['tabletype']['name']]="text";
						if( !$v[$publicProperty['length']['name']] )
						$v[$publicProperty['length']['name']]="500";
					}else if($v[$publicProperty['catalog']['name']]=="select" || $v[$publicProperty['catalog']['name']]=="radio"){
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
					}
					// defaultval
					$ceatetable_html.="\r\n\t,`".$v[$publicProperty['fields']['name']]."` ".$v[$publicProperty['tabletype']['name']]."(".$v[$publicProperty['length']['name']].")";
					if($v[$publicProperty['requiredfield']['name']]=="required" || $v[$publicProperty['defaultval']['name']]){
						$ceatetable_html.=" NOT NULL COMMENT '".$v[$publicProperty['title']['name']]."'";
						if($v[$publicProperty['defaultval']['name']])
						$ceatetable_html.="DEFAULT '".$v[$publicProperty['defaultval']['name']]."'  COMMENT '".$v[$publicProperty['title']['name']]."'";
					}else{
						$ceatetable_html.=" DEFAULT NULL COMMENT '".$v[$publicProperty['title']['name']]."'";
					}
					if($v['checkexist']) // 唯一检查 约束创建【已废弃】
					$ceatetable_html_e.=",UNIQUE KEY `".$v[$publicProperty['dbfield']['name']]."` (`".$v[$publicProperty['dbfield']['name']]."`)";


					if($v[$publicProperty['catalog']['name']]=="datatable"){
						// 取得从名后缀名
						$suff = $v[$publicProperty['fields']['name']];
						// 获取从表字段信息
						$jsonStr = $v[$privateProperty['datatable']['fieldlist']['name']];
						$obj = json_decode(htmlspecialchars_decode($jsonStr));
						$tebs['name']=$suff;
						foreach ($obj[0] as $k=>$v){
							$temp['fieldname']=$v->fieldname;
							$temp['fieldtype']=$v->fieldtype;
							$temp['fieldlength']=$v->fieldlength;
							$tebs['field'][]=$temp;
						}
					}
				}
				if($tebs){
					$inserTables[]=$tebs;
					unset($tebs);
				}
			}
			foreach ($this->defaultField as $k=>$v){
				$ceatetable_html .= "\r\n\t,`".$k .'` '.$v;
			}
			if( $isaudit ){
				foreach ($this->auditField as $k=>$v){
					$ceatetable_html .= "\r\n\t,`".$k .'` '.$v;
				}
			}


			$createtable_str=$ceatetable_html_s.$ceatetable_html.$ceatetable_html_e."\r\n)ENGINE=InnoDB  DEFAULT CHARSET=utf8";

			file_put_contents(ROOT.'/databack/'.$this->tableName.'_create.sql', "DROP TABLE IF EXISTS `".$this->tableName."`;\r\n\t".$createtable_str);

			// 主表修改前备份数据，表修改后恢复数据
			$this->backupandrecoverdb($this->tableName);
			// 处理子表修改及数据恢复
			//$this->createChildTable($inserTables);


			/* $isfllow = $this->inserttable;
			 if($isfllow){
			 $tmodel=M();
			 $res1 = $tmodel->query("DROP TABLE IF EXISTS `".$this->tableName);
			 $res2 = $tmodel->query($createtable_str);
			 }*/
		}
	}

	/**
	 * @Title: craeteDatabaseAppend
	 * @Description: todo(生成数据表,添加时 直接生成表，非添加时当字段信息中ids项为空时做字段追加操作。)
	 * @param array $filedData	字段信息
	 * @author quqiang
	 * @date 2014-9-11 上午9:51:09
	 * @throws
	 */
	private function craeteDatabaseAppend($filedData , $isadd=false){
		$mainTable = '';
		$createtable_str='';
		$suffTableData=array();

		if(!$this->validateTable($this->tableName))
		$isadd = true;
		if($isadd){	// 新增
			$reatetable_html_s .= "DROP TABLE IF EXISTS `".$this->tableName;
			$ceatetable_html_s .= "CREATE TABLE IF NOT EXISTS `".$this->tableName."` (`id` int(11) NOT NULL AUTO_INCREMENT  COMMENT 'ID'";
			$ceatetable_html_e=",PRIMARY KEY (`id`)";
			$sql = $this->getUserSetFiledsArr($filedData);
			$mainFiled = $sql['main'];
			$suffTableData= $sql['suff'];
			if($mainFiled){
				$ceatetable_html = ','.join(',', $mainFiled);
			}

			foreach ($this->defaultField as $k=>$v){
				$ceatetable_html .= ",`".$k .'` '.$v;
			}
			if( $isaudit ){
				foreach ($this->auditField as $k=>$v){
					$ceatetable_html .= ",`".$k .'` '.$v;
				}
			}
			$createtable_str=$ceatetable_html_s.$ceatetable_html.$ceatetable_html_e.")ENGINE=InnoDB  DEFAULT CHARSET=utf8";
		}else{	// 字段追加
			$sql = $this->getUserSetFiledsArr($filedData);
			$mainFiled = $sql['main'];
			$suffTableData= $sql['suff'];
			$createtable_str = $mainFiled?'ALTER	TABLE `'.$this->tableName.'` ADD '.join(', ADD', $mainFiled):'';

		}
		// return array('main'=>$sqlArr,'suff'=>$inserTables);
		if($createtable_str){
			$tmodel=M();
			$res2 = $tmodel->query($createtable_str);
		}
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
		foreach($fieldData as $k=>$v ){
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
				foreach ($obj[0] as $k=>$v){
					if(!$v->fieldid){
						$fieldname=$v->fieldname;
						$fieldtype=$v->fieldtype?$v->fieldtype:'varchar';
						$fieldlength=$v->fieldlength?$v->fieldlength:10;
						$fieldntitle = $v->fieldntitle?$v->fieldntitle:'用户添加时未填写名称';
						$sql="`{$fieldname}` {$fieldtype}({$fieldlength}) DEFAULT NULL COMMENT '动态表单内嵌表单字段-{$suff}-{$fieldntitle}'";
						$tebs['field'][]=$sql;
					}
				}
			}
			/***/

			if($tebs){
				$inserTables[]=$tebs;
				unset($tebs);
			}
		}
		return array('main'=>$sqlArr,'suff'=>$inserTables);
	}

	/**
	 * 生成内嵌子表
	 * @param array $data 子表结构数据
	 */
	private function createChildTable($data){

		$curtabnamePrefix=$this->tableName.'_sub_';
		$tables=array();
		$sqlStr = '';
		foreach ($data as $k=>$v){
			$curTableName = $curtabnamePrefix.$v['name'];
			// charset utf8; 设置编码
			$ceatetable_html_s="CREATE TABLE IF NOT EXISTS `".$curTableName."` (";
			$ceatetable_html_s.="`id` int(11) NOT NULL AUTO_INCREMENT  COMMENT 'ID'";
			$ceatetable_html_e=",PRIMARY KEY (`id`)";
			$ceatetable_html_e.=",`masid` int(10) COMMENT '关联动态表单数据ID'";
			$ceatetable_html='';
			if($v['field']){
				$ceatetable_html = ','.join(',', $v['field']);
			}
			// 外键删除功能
			$ceatetable_html.=",KEY `delete_{$v['name']}` (`masid`),";
			$ceatetable_html.="CONSTRAINT `delete_{$v['name']}` FOREIGN KEY (`masid`) REFERENCES `{$this->tableName}` (`id`) ON DELETE CASCADE";
			// 外键删除功能 end
			$sqlStr = $ceatetable_html_s.$ceatetable_html_e.$ceatetable_html.")comment='动态表单:{$this->tableName} 内嵌表 ".$curTableName."'";
			// 如果当前子表已存在，就做字段追加，否则就做表生成
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
	 * 备份并恢复数据
	 * @param string $curTableName	当前需要恢复/创建的数据表名
	 */
	public function backupandrecoverdb($curTableName){
		// 1：命令所在路径
		$cmdpath = "D:\installdir\dev\wamp\mysql\bin/";
		// 2：保存路径
		$savepath = ROOT.'/databack/';
		// 3：HOST地址
		$host=C('DB_HOST');
		// 4：端口
		$port = C('DB_PORT');
		// 5：用户名
		$user = C('DB_USER');
		// 6：密码
		$pwd = C('DB_PWD');
		// 7：库名
		$database=C('DB_NAME');
		// 8：表名
		$tabblename = $curTableName;

		$cmd=ROOT.'/Lib/bat/backsql.bat '.$cmdpath.' '.$savepath.' '.$host.' '.$port.' '.$user.' '.$pwd.' '.$database.' '.$tabblename;

		/*
		 // 1：命令bin所有路径。2：服务器地址。3：端口。4：用户名。5：密码。6：要导出的库名称。7：表名称。8：备份文件保存路径
		 $mysqldumpDir="D:\installdir\dev\wamp\mysql\bin/";
		 $host=C('DB_HOST');
		 $port = C('DB_PORT');
		 $user = C('DB_USER');
		 $pwd = C('DB_PWD');
		 $database=C('DB_NAME');
		 $tabblename = $curTableName;
		 $saveDir=ROOT.'/databack/'.$curTableName.'_'.date('ymdhis',time()).'_back.sql';
		 $deaultDataDir = ROOT.'/databack/'.$curTableName.'_data.sql';
		 // 调用批处文件备份表
		 $cmd =  ROOT.'/Lib/bat/backsql.bat '.$mysqldumpDir.' '.$host.' '.$port.' '.$user.' '.$pwd.' '.$database.' '.$tabblename.' '.$saveDir.' '.$deaultDataDir;

		 */
		//echo $cmd;
		exec($cmd);
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
	 * @Title: getDynamicDwzloadindexHtml
	 * @Description: todo(生成下拉滚动触发事件的数据展示模板)
	 * @param unknown_type $fieldsinfo
	 * @param unknown_type $model
	 * @param unknown_type $isauditindex
	 * @return string
	 * @author liminggang
	 * @date 2013-6-1 上午9:53:45
	 * @throws
	 */
	private function getDynamicDwzloadindexHtml(){
		$pd="";
		$pd="data-tool='{\$vo[classarr]}'";
		$content_h.="<volist id=\"vo\" name=\"list\" key=\"key2\">";
		$content_h.="\r\n\t<tr target=\"sid_node\" rel=\"{\$vo['id']}\"  ".$pd.">";

		//$content_h.="\r\n\t\t<td><input type=\"checkbox\" name=\"id\" value=\"{\$vo['id']}\" /></td>";
		$content_h.="\r\n\t\t<td class=\"tml-first-td\">{\$numPerPage*(\$currentPage-1)+\$key+1}</td>";

		$content_h.="\r\n\t\t<volist id=\"vo1\" name=\"detailList\">";
		$content_h.="\r\n\t\t\t<if condition=\"\$vo1[shows] eq 1\">";
		$content_h.="\r\n\t\t\t\t<td width=\"{\$vo1[widths]}\">";
		$content_h.="\r\n\t\t\t\t\t<if condition=\"count(\$vo1['func']) gt 0\">";
		$content_h.="\r\n\t\t\t\t\t\t<volist name=\"vo1.func\" id=\"nam\">";
		$content_h.="\r\n\t\t\t\t\t\t\t<if condition=\"!empty(\$vo1['extention_html_start'][\$key])\">{\$vo1['extention_html_start'][\$key]}</if>";
		$content_h.="\r\n\t\t\t\t\t\t\t\t{:getConfigFunction(\$vo[\$vo1['name']],\$nam,\$vo1['funcdata'][\$key],\$list[\$key2-1])}";
		$content_h.="\r\n\t\t\t\t\t\t\t<if condition=\"!empty(\$vo1['extention_html_end'][\$key])\">{\$vo1['extention_html_end'][\$key]}</if>";
		$content_h.="\r\n\t\t\t\t\t\t</volist>";
		$content_h.="\r\n\t\t\t\t\t<else />";
		$content_h.="\r\n\t\t\t\t\t\t{\$vo[\$vo1['name']]}";
		$content_h.="\r\n\t\t\t\t\t</if>";
		$content_h.="\r\n\t\t\t\t</td>";
		$content_h.="\r\n\t\t\t</if>";
		$content_h.="\r\n\t\t</volist>";
		$content_h.="\r\n\t</tr>";
		$content_h.="\r\n</volist>";
		return $content_h;
	}

	/**
	 * @Title: getView
	 * @Description: todo(生成查看页面，三列版)
	 * @param unknown_type $fieldsinfo
	 * @return string
	 * @author 屈强
	 * @date 2014-9-10 上午11:10:45
	 * @throws
	 */
	private function getView($fieldsinfo){
		$style= <<<EOF
<style>
.cell_left{
    text-align: center;
}
.cell_right{
    color:#0090ff;
}
</style>
EOF;
		$privateProperty = $this->privateProperty;
		$controlProperty = $this->controlConfig;
		/**
		 * 按三个一组拆分
		 * 	独占一行的，先补全上次数据
		 * */
		$lineshownum = 3;
		$index=1;
		$res=array();
		$a3=array();
		$temp=array();
		foreach ($fieldsinfo as $k=>$v){
			if($controlProperty[$v['catalog']]['iscreate']){ // 允许生成
				if($controlProperty[$v['catalog']]['isline']){ // 允许单行
					$v['isline'] =true;
					// 补全上次已有值点位
					if($index % $lineshownum){
						if($a3){
							for($i=0;$i<$index%$lineshownum;$i++){
								$a3[]='';
							}
							$res[]=$a3;
							unset($a3);
						}
					}
					$index += 2*$lineshownum-$index % $lineshownum ; // 将序号补全,
					$a3[]=$v;
				}else{
					$v['isline'] =false;
					// 正常情况下的分组
					$a3[]=$v;
				}
				if( $index % $lineshownum==0 ){
					$res[]=$a3;
					unset($a3);
				}
				$index ++ ;
			}
		}
		if($a3){
			-- $index;
			// 最后
			if($index%$lineshownum){
				if($a3){
					for($i=0;$i<$lineshownum-$index%$lineshownum;$i++){ // 计算缺少个数
						$a3[]='';
					}
					$res[]=$a3;
					unset($a3);
				}
			}
		}

		// iscreatef
		// 遍历 构造html
		$html="\r\n<table class=\"data-table\" width=\"100%\" cellspacing=\"0\" style=\"line-height:32px;\" >";
		$html.="\r\n\t<tbody>";
		$i=1;
		$j=1;
		$td1='';
		$td2 ='';
		foreach ($res as $k=>$v){
			$html.="\r\n\t\t<tr>";
			foreach ($v as $key=>$val){
				if($k==0){
					$td1 = $k==0?'width="12%"':'';
					$td2 = $k==0?'width="21%"':'';
				}
				$html.="\r\n\t\t\t<td class=\"cell_left\" {$td1}>".($val?$val['title'].':':'')."</td>";
				$html.="\r\n\t\t\t<td class=\"cell_right\" {$td2} ".($val['isline']?'colspan="5"':'').">".
				(is_array($val)?$this->getControl($val,true,true):'').
				"</td>";
			}
			$html.="\r\n\t\t</tr>";
		}
		$html.="\r\n\t</tbody>";
		$html.="\r\n</table>";
		/* UL版算法
		 $html="\r\n<ul class=\"data_list\">";
		 $i=1;
		 $j=1;
		 foreach ($fieldsinfo as $k=>$v){
			if($controlProperty[$v['catalog']]['iscreate']){
			$cls=$j<=3?'1':'2';
			$e=$j%3==1?'1':'n';
			//				if($privateProperty[$v['catalog']]['isline']){
			if($controlProperty[$v['catalog']]['isline']){
			$ct = 4-$i>=3?0:4-$i;
			for($o=0; $o<$ct ;$o++){
			$html.="\r\n\t<li class=\"data_list_cell cell_{$cls}_{$e} \" j=\"".($o+$j)."\">";
			$html.="\r\n\t\t<span class=\"cell_left\"></span>";
			$html.="\r\n\t\t<span class=\"cell_right\"></span>";
			$html.="\r\n\t</li>";
			}
			$j+=$o; // 加上补全个数
			$j+=2; // 加上独占
			$html.="\r\n\t<li class=\"data_list_line\" j=\"{$j}\">";
			$html.="\r\n\t\t<span class=\"cell_line_left\">{$v['title']}：</span>";
			$html.="\r\n\t\t<span class=\"cell_line_right\">".$this->getControl($v,true,true)."</span>";
			$html.="\r\n\t</li>";
			$i=0;
			}else{
			$html.="\r\n\t<li class=\"data_list_cell cell_{$cls}_{$e} \" j=\"{$j}\">";
			$html.="\r\n\t\t<span class=\"cell_left\">{$v['title']}：</span>";
			$html.="\r\n\t\t<span class=\"cell_right\">".$this->getControl($v,true,true)."</span>";
			$html.="\r\n\t</li>";
			}
			$i++;
			$j++;
			}
			}
			--$j;
			$tj = $j;
			if($j%3!=0){
			// 补全占位
			for($o=0; $o<3-$tj%3;$o++){
			$j++;
			$cls=$j<=3?'1':'2';
			$e=$j%3==1?'1':'n';
			$html.="\r\n\t<li class=\"data_list_cell cell_{$cls}_{$e} \" j=\"{$j}\">";
			$html.="\r\n\t\t<span class=\"cell_left\"></span>";
			$html.="\r\n\t\t<span class=\"cell_right\"></span>";
			$html.="\r\n\t</li>";

			}
			}
			$html.="\r\n</ul>";
			*/
		return $style."\r\n".$html;
	}







	/**
	 * @Title: create_indexviewhtml
	 * @Description: todo(获取indexView.html页面)
	 * @param boolean $isaudit 是否带审批流，默认为false:不带审批流
	 * @return string
	 * @author quqiang
	 * @date 2014-8-21 下午3:20:10
	 * @throws
	 */
	private function getIndexviewHtml($isaudit=false){
		$content_h.="<div class=\"pageContent\">";
		$content_h.="\r\n\t\t<form id=\"pagerForm\" action=\"__URL__/index/type/1\" method=\"post\">";
		$content_h.="\r\n\t\t\t\t<input type=\"hidden\" name=\"pageNum\" value=\"1\"/>";
		$content_h.="\r\n\t\t\t\t<input type=\"hidden\" name=\"orderField\" value=\"{\$order}\" />";
		$content_h.="\r\n\t\t\t\t<input type=\"hidden\" name=\"orderDirection\" value=\"{\$sort}\"/>";
		$content_h.="\r\n\t\t</form>";
		$content_h.="\r\n\t\t<div class=\"panelBar\">";
		$content_h.="\r\n\t\t\t\t<ul class=\"toolBar\">";
		$content_h.="\r\n\t\t						<volist name=\"toolbarextension\" id=\"toolb\">";
		$content_h.="\r\n\t\t							<if condition=\"\$_SESSION.a eq 1 or \$toolb['ifcheck'] eq 0 or (\$toolb['ifcheck'] eq 1 and !empty(\$toolb['permisname']) and \$_SESSION[\$toolb['permisname']])\">";
		$content_h.="\r\n\t\t								<li>{\$toolb['html']}</li>";
		$content_h.="\r\n\t\t							</if>";
		$content_h.="\r\n\t\t						</volist>";
		$content_h.="\r\n\t\t					</ul>";
		$content_h.="\r\n\t\t					<form rel=\"pagerForm\" onsubmit=\"return  ".($isaudit?'divSearch(this, \'__MODULE__indexview\')':'navTabSearch(this)')."\" action=\"__URL__/index/type/1\" method=\"post\">";
		$content_h.="\r\n\t\t						<div class=\"searchBar\">";
		$content_h.="\r\n\t\t							<table class=\"searchContent\">";
		$content_h.="\r\n\t\t								<tr>";
		$content_h.="\r\n\t\t									<include file=\"Public:quickSearchConditionForAudit\" />";
		$content_h.="\r\n\t\t								</tr>";
		$content_h.="\r\n\t\t							</table>";
		$content_h.="\r\n\t\t						</div>";
		$content_h.="\r\n\t\t					</form>";
		$content_h.="\r\n\t\t				</div>";
		$content_h.="\r\n\t\t				<table class=\"table\" width=\"100%\" layoutH=\"98\">";
		$content_h.="\r\n\t\t					<thead ename=\"{\$ename}\">";
		$content_h.="\r\n\t\t						<tr>";
		$content_h.="\r\n\t\t							<th width=\"26\">序号</th>";
		$content_h.="\r\n\t\t							<volist id=\"vo\" name=\"detailList\">";
		$content_h.="\r\n\t\t								<if condition=\"\$vo[shows] eq 1\"><th <if condition=\"\$vo[widths]\">width=\"{\$vo[widths]}\"</if><if condition=\"\$vo[sorts]\"> ".($isaudit?'rel="__MODULE__indexview"':'')." orderField=\"{\$vo[sortname]}\" class=\"{\$sort}\"</if>>{\$vo[showname]}</th></if>	<!--类型-->";
		$content_h.="\r\n\t\t							</volist>";
		$content_h.="\r\n\t\t						</tr>";
		$content_h.="\r\n\t\t					</thead>";
		$content_h.="\r\n\t\t					<tbody>";
		$content_h.="\r\n\t\t						<include file=\"dwzloadindex\" />";
		$content_h.="\r\n\t\t					</tbody>";
		$content_h.="\r\n\t\t				</table>";
		$content_h.="\r\n\t\t				<div class=\"panelBar panelPageBar\">";
		$content_h.="\r\n\t\t					<div class=\"pages\">";
		$content_h.="\r\n\t\t						<span>共{\$totalCount}条</span>";
		$content_h.="\r\n\t\t					</div>";
		$content_h.="\r\n\t\t					<div class=\"pagination\" ".($isaudit?'rel="__MODULE__indexview"':'')." targetType=\"navTab\" totalCount=\"{\$totalCount}\" numPerPage=\"{\$numPerPage}\" pageNumShown=\"10\" currentPage=\"{\$currentPage}\"></div>";
		$content_h.="\r\n\t\t				</div>";
		$content_h.="\r\n\t\t			</div>";
		return $content_h;
	}


	/**
	 * @Title: create_auditIndexhtml
	 * @Description: todo(这里是生成带审批流的待审和已审核模板文件)
	 * @param array $fieldsinfo
	 * @param unknown_type $model
	 * @return string
	 * @author liminggang
	 * @date 2013-6-1 上午9:52:31
	 * @throws
	 */
	private function createAuditIndexhtml($fieldsinfo,$model){
		$content_h = <<<EOF
<div class="pageContent">
	<form id="pagerForm" action="__URL__/{\$jumpUrl}" method="post">
		<input type="hidden" name="pageNum" value="1"/>
		<input type="hidden" name="orderField" value="{\$order}" />
		<input type="hidden" name="orderDirection" value="{\$sort}" />
	</form>
	<div class="panelBar">
		<ul class="toolBar">
			<if condition="\$audit eq 0">
			<li><a class="redo" href="__URL__/auditEdit/id/{sid}" target="navTab" title="申请单审核" rel="__MODULE__auditEdit" width="690" height="450" mask="true" warn="请选择节点"><span>{\$Think.lang.auditprocess}</span></a></li>
			</if>
			<if condition="\$audit eq 1">
			<li><a class="icon" href="__URL__/auditView/id/{sid}" target="navTab" title="查看" rel="__MODULE__auditView" width="690" height="450" mask="true" warn="请选择节点"><span><span class="icon icon-eye-open icon_lrp"></span>查看</span></a></li>
			</if>
			<!-- <li class="line">line</li>
			<li><a class="detail" href="__URL__/seeProcessDetail/id/{sid}" target="dialog" height="450" width="580" mask="true" title="流程查看" rel="__MODULE__seeProcessDetail" warn="请选择节点"><span>流程查看</span></a></li> -->
		</ul>
		<form rel="pagerForm" onsubmit="return divSearch(this, '__MODULE__indexview')" action="__URL__/{\$jumpUrl}" method="post">
			<div class="searchBar">
				<table class="searchContent">
					<tr>
						<include file="Public:quickSearchCondition" />
					</tr>
				</table>
			</div>
		</form>
	</div>
	<table class="table" width="100%" layoutH="96">
		<thead>
		<tr>
			<th width="26">序号</th>
			<volist id="vo" name="detailList">
				<if condition="\$vo[shows] eq 1"><th <if condition="\$vo[widths]">width="{\$vo[widths]}"</if><if condition="\$vo[sorts]"> rel="__MODULE__indexview" orderField="{\$vo[sortname]}" class="{\$sort}"</if>>{\$vo[showname]}</th></if>	<!--类型-->
			</volist>
		</tr>
		</thead>
		<tbody>
			<volist id="vo" name="list" key="key2">
				<tr target="sid" rel="{\$vo['id']}" onclick="onTrClickCheckbox(this,'id')" <if condition="\$vo[curNodeUser] && in_array(\$_SESSION[C('USER_AUTH_KEY')],explode(',',\$vo[curNodeUser]))"> class="auditformeselected" </if>  <if condition="\$audit eq 1">  title="查看" ondblclick="ondblclick_navTab(this,'__MODULE__auditView','__URL__/auditView/id/{\$vo['id']}');"</if><if condition="\$audit eq 0">title="申请单审核" ondblclick="ondblclick_navTab(this,'__MODULE__auditEdit','__URL__/auditEdit/id/{\$vo['id']}');"</if>>
					<td class="tml-first-td">{\$numPerPage*(\$currentPage-1)+\$key+1}</td>
					<volist id="vo1" name="detailList">
						<if condition="\$vo1[shows] eq 1">
							<td width="{\$vo1[widths]}">
								<if condition="count(\$vo1['func']) gt 0">
									<volist name="vo1.func" id="nam">
										<if condition="!empty(\$vo1['extention_html_start'][\$key])">{\$vo1['extention_html_start'][\$key]}</if>
											{:getConfigFunction(\$vo[\$vo1['name']],\$nam,\$vo1['funcdata'][\$key],\$list[\$key2-1])}
										<if condition="!empty(\$vo1['extention_html_end'][\$key])">{\$vo1['extention_html_end'][\$key]}</if>
									</volist>
								<else />
									{\$vo[\$vo1['name']]}
								</if>
							</td>
						</if>
					</volist>
				</tr>
			</volist>
		</tbody>
   </table>
	<div class="panelBar panelPageBar">
		<div class="pages">
			<span>共{\$totalCount}条</span>
		</div>
		<div class="pagination" rel="__MODULE__indexview" totalCount="{\$totalCount}" numPerPage="{\$numPerPage}" pageNumShown="10" currentPage="{\$currentPage}"></div>
	</div>
</div>
EOF;
		return $content_h;
	}

	/**
	 * @Title: getAddHtmlTemplate
	 * @Description: todo(获取新增页面模板，审批流的附属模板 必须)
	 * @param array $fieldsinfo	组件属性列表
	 * @param int $isedit 是否修改 0：添加add.html。 1：修改edit.html。2：查看view.html。【当$isaudit 值大于0 时 本参数失效！！】
	 * @param int $isaudit 是否审批   0:非审批。1：add.html。2：edit.html。3：auditEdit.html。4:auditView.html
	 * @return string
	 * @author quqiang
	 * @date 2014-8-21 下午2:44:06
	 * @throws
	 */
	private function getAddHtmlTemplate($fieldsinfo , $isedit = 0 , $isaudit = 0 ){
		$url='';
		$formHtml='';
		$formHtmlEnd ='';
		$titleHtml='';
		$hiddenHtml='';
		$buttonHtml='';
		$contentHtml = '';
		$IS_BIND_DATA=false; // 是否绑定值
		$isView = false;
		if($isaudit){
			// 审批流
			switch ($isaudit){
				case '1': //add.html
					$formHtml = "\r\n\t\t<form method=\"post\" action=\"__APP__/{$this->nodeName}/insert/navTabId/__MODULE__\" class=\"pageForm required-validate\" onsubmit=\"return validateCallback(this,navTabAjaxDone);\">";
					$hiddenHtml ="\r\n\t\t\t<input type=\"hidden\" name=\"callbackType\" value=\"closeCurrent\">";
					$hiddenHtml .="\r\n\t\t\t<input type=\"hidden\" name=\"dotype\" value=\"流程新建\" />";
					$hiddenHtml .="\r\n\t\t\t<input type=\"hidden\" name=\"beforeInsert\" value=\"1\">";
					$titleHtml= "{$this->nodeTitle}{:W('ShowFlow')}{:W('ShowOrderno',array(1,'{$this->tableName}'))}";
					$formHtmlEnd ="\r\n\t\t</form>";
					$buttonHtml ="\r\n\t\t{:W('ShowAction')}";
					$IS_BIND_DATA = false;
					break;
				case '2': //edit.html
					$formHtml = "\r\n\t\t<form method=\"post\" action=\"__APP__/{$this->nodeName}/update/navTabId/__MODULE__\" class=\"pageForm required-validate\" onsubmit=\"return validateCallback(this,navTabAjaxDone);\">";
					$hiddenHtml ="\r\n\t\t\t<input type=\"hidden\" name=\"id\" value=\"{\$vo['id']}\" />";
					$hiddenHtml .="\r\n\t\t\t<input type=\"hidden\" name=\"callbackType\" value=\"closeCurrent\">";
					$hiddenHtml .="\r\n\t\t\t<input type=\"hidden\" name=\"refreshtabs[data]\" value=\"1\">";
					$hiddenHtml .="\r\n\t\t\t<input type=\"hidden\" name=\"dotype\" value=\"流程启动\" />";
					$hiddenHtml .="\r\n\t\t\t<input type=\"hidden\" name=\"ostatus\" value=\"{\$vo.ostatus}\" />";
					$titleHtml = "{$this->nodeTitle}{:W('ShowFlow',\$vo)}{:W('ShowOrderno',array(1,'{$this->tableName}',\$vo['orderno']))}";
					$formHtmlEnd ="\r\n\t\t</form>";
					$buttonHtml ="\r\n\t\t{:W('ShowAction',array('data'=>\$vo))}";
					$IS_BIND_DATA = true;
					break;
				case '3': //auditEdit.html
					$formHtml = "\r\n\t\t<form method=\"post\" action=\"__APP__/{$this->nodeName}/auditProcess/navTabId/__MODULE__\" class=\"pageForm-validate\" onsubmit=\"return validateCallback(this, refreshtabsAudit);\">";
					$hiddenHtml ="\r\n\t\t\t<input type=\"hidden\" name=\"id\" value=\"{\$vo['id']}\" />";
					$hiddenHtml .="\r\n\t\t\t<input type=\"hidden\" name=\"pid\" value=\"{\$vo['ptmptid']}\" />";
					$hiddenHtml .="\r\n\t\t\t<input type=\"hidden\" name=\"ostatus\" value=\"{\$vo['ostatus']}\" />";
					$hiddenHtml .="\r\n\t\t\t<input type=\"hidden\" name=\"refreshtabs[tabid]\" value=\"__MODULE__\" />";
					$hiddenHtml .="\r\n\t\t\t<input type=\"hidden\" name=\"refreshtabs[url]\" value=\"__APP__/{$this->nodeName}/index\" />";
					$hiddenHtml .="\r\n\t\t\t<input type=\"hidden\" name=\"refreshtabs[data]\" value=\"1\" />";
					$hiddenHtml .="\r\n\t\t\t<input type=\"hidden\" name=\"refreshtabs[title]\" value=\"人员申请管理\" />";
					$hiddenHtml .="\r\n\t\t\t<input type=\"hidden\" name=\"refreshtabs[type]\" value=\"navtab\" />";
					$hiddenHtml .="\r\n\t\t\t<input type=\"hidden\" name=\"backprocess\" value=\"流程回退\" />";
					$hiddenHtml .="\r\n\t\t\t<input type=\"hidden\" name=\"auditprocess\" value=\"流程审核\" />";
					$hiddenHtml .="\r\n\t\t\t<input type=\"hidden\" name=\"endprocess\" value=\"流程结束\" />";
					$hiddenHtml .="\r\n\t\t\t<input type=\"hidden\" name=\"alreadyAuditUser\" value=\"{\$vo['alreadyAuditUser']}\" />";
					$hiddenHtml .="\r\n\t\t\t<input type=\"hidden\" name=\"auditUser\" value=\"{\$vo['auditUser']}\" />";
					$titleHtml = "{$this->nodeTitle}{:W('ShowAdvices',array('id'=>\$vo['id']))}{:W('ShowOrderno',array(1,'{$this->tableName}',\$vo['orderno']))}";
					$formHtmlEnd ="\r\n\t\t</form>";
					$buttonHtml ="\r\n\t\t{:W('ShowAction')}";
					$IS_BIND_DATA = true;
					break;
				case '4': // auditView.html
					$formHtml = "";
					$titleHtml = "{$this->nodeTitle}{:W('ShowAdvices',array('id'=>\$vo['id']))}{:W('ShowOrderno',array(0,'{$this->tableName}',\$vo['orderno']))}";
					$formHtmlEnd ="";
					$buttonHtml ="\r\n\t\t{:W('ShowAction')}";
					$IS_BIND_DATA = true;
					break;
			}

		}else{
			switch ($isedit){
				case '0': //add.html
					$formHtml = "\r\n\t\t<form method=\"post\" action=\"__APP__/{$this->nodeName}/insert/navTabId/__MODULE__\" class=\"pageForm required-validate\" onsubmit=\"return iframeCallback(this, navTabAjaxDone)\">";
					$hiddenHtml ="\r\n\t\t\t<input type=\"hidden\" name=\"callbackType\" value=\"closeCurrent\" />";
					$titleHtml = "{$this->nodeTitle}{:W('ShowOrderno',array(0,'{$this->tableName}'))}";
					$formHtmlEnd ="\r\n\t\t</form>";
					$buttonHtml ="\r\n\t\t{:W('ShowAction')}";
					$IS_BIND_DATA = false;
					break;
				case '1': //edit.html
					$formHtml = "\r\n\t\t<form method=\"post\" action=\"__APP__/{$this->nodeName}/update/navTabId/__MODULE__\" class=\"pageForm required-validate\" onsubmit=\"return iframeCallback(this, navTabAjaxDone)\">";
					$hiddenHtml ="\r\n\t\t\t<input type=\"hidden\" name=\"callbackType\" value=\"closeCurrent\" />";
					$hiddenHtml .="\r\n\t\t\t<input type=\"hidden\" name=\"id\" value=\"{\$vo['id']}\" />";
					$titleHtml = "{$this->nodeTitle}{:W('ShowOrderno',array(0,'{$this->tableName}',\$vo['orderno']))}";
					$formHtmlEnd ="\r\n\t\t</form>";
					$buttonHtml ="\r\n\t\t{:W('ShowAction',array('data'=>\$vo))}";
					$IS_BIND_DATA = true;
					break;
				case '2': //view.html
					$formHtml = "\r\n\t\t<form method=\"post\" action=\"__APP__/{$this->nodeName}/update/navTabId/__MODULE__\" class=\"pageForm required-validate\" onsubmit=\"return iframeCallback(this, navTabAjaxDone)\" enctype=\"multipart/form-data\">";
					$hiddenHtml ="\r\n\t\t\t<input type=\"hidden\" name=\"callbackType\" value=\"closeCurrent\"/>";
					$titleHtml = "{$this->nodeTitle}{:W('ShowOrderno',array(0,'{$this->tableName}',\$vo['orderno']))}";
					$formHtmlEnd ="\r\n\t\t</form>";
					$buttonHtml ="\r\n\t\t{:W('ShowAction')}";
					$IS_BIND_DATA = true;
					$isView = true;
					break;
			}
		}

		$contentHtml = "\r\n\t<div class=\"page\">";
		$contentHtml .= "\r\n\t<div class=\"pageContent\">";
		$contentHtml .= $formHtml;
		$contentHtml .= $hiddenHtml;
		$contentHtml .= "\r\n\t\t\t<div class=\"pageFormContent applecloth anchorsToolBarParent\" layoutH=\"56\">";
		$contentHtml .= "\r\n\t\t\t\t<div class=\"keepContentCenter form-affix-content\">";
		$contentHtml .= "\r\n\t\t\t\t\t<H2 class=\"contentTitle contentTitle_center form-affix-title\">";
		$contentHtml .= $titleHtml;
		$contentHtml .= "\r\n\t\t\t\t\t</H2>";
		// 组件列表
		$contentHtml .= self::getControlContainerHtml($fieldsinfo , $IS_BIND_DATA , $isView);
		// 组件列表end
		$contentHtml .= "\r\n\t\t\t\t</div>";
		$contentHtml .="\r\n\t\t\t</div>";
		$contentHtml .= $buttonHtml;
		$contentHtml .= $formHtmlEnd;
		$contentHtml .= "\r\n\t</div>";
		$contentHtml .= "\r\n\t</div>";
		return $contentHtml;
	}
	/**
	 * @Title: getControlContainerHtml
	 * @Description: todo(生成组件容器 ，调用生成组件结构)
	 * @param array $fieldsinfo 字段数据
	 * @param boolean $isedit 是否编辑
	 * @return string
	 * @author quqiang
	 * @date 2014-8-21 下午2:47:04
	 * @throws
	 */
	private function getControlContainerHtml($fieldsinfo,$isedit=false ,$isview = false){

		$outwidthandheight = array();
		$except=$this->systemUserField;//nbmxkj@过滤特殊字段名。-->审批流程,基础字段
		$keysArr;
		foreach ($fieldsinfo as $k1 => $v1) {
			if($v[catalog]=='fieldset'){
				$keysArr[]=$k1;
			}
			if(in_array($v1['field'],$except)){
				unset($fieldsinfo[$k1]);
			}
		}


		if($isview){
			$content_h = $this->getView($fieldsinfo);
		}
		else{
			$html .= "<div class=\"tml-row\">";
			$tagCountHasinsert = 0;
			foreach($fieldsinfo as $k => $v ){
				$property = $this->getProperty($v['catalog']);

				if(!$v[$property['islock']['name']]){
					$readonly ="readonly";
				}

				switch($v[catalog]){
					case 'radio':
						$html .='</div>';
						$html .="\r\n\t\t\t\t\t\t\t<div class=\"tml-form-row {$readonly}\" >";
						$html .= $this->getControl($v , $isedit);
						$html .="\r\n\t\t\t\t\t\t\t</div>";
						$html .='<div class="tml-row">';
						break;
					case 'text':
						$html .="\r\n\t\t\t\t\t\t\t<div class=\"tml-form-col {$readonly}\">";
						$html .= $this->getControl($v , $isedit);
						$html .="\r\n\t\t\t\t\t\t\t</div>";

						break;
					case 'date':
						$html .="\r\n\t\t\t\t\t\t\t<div class=\"tml-form-col {$readonly}\">";
						$html .= $this->getControl($v , $isedit);
						$html .="\r\n\t\t\t\t\t\t\t</div>";
						break;
					case 'textarea':
						$html .='</div>';
						$html .="\r\n\t\t\t\t\t\t\t<div class=\"tml-form-row {$readonly}\">";
						$html .= $this->getControl($v , $isedit);
						$html .="\r\n\t\t\t\t\t\t\t</div>";
						$html .='<div class="tml-row">';
						break;
					case 'checkbox':
						$html .='</div>';
						$html .="\r\n\t\t\t\t\t\t\t<div class=\"tml-form-row {$readonly}\">";
						$html .= $this->getControl($v , $isedit);
						$html .="</div>";
						$html .='<div class="tml-row">';
						break;
					case 'upload':
						$html .='</div>';
						$html .= $this->getControl($v , $isedit);
						$html .='<div class="tml-row">';
						break;
					case 'select':
						$html .="\r\n\t\t\t\t\t\t\t<div class=\"tml-form-col {$readonly}\">";
						$html .= $this->getControl($v , $isedit);
						$html .="\r\n\t\t\t\t\t\t\t</div>";
						break;
					case 'lookup':
						$html .="\r\n\t\t\t\t\t\t\t<div class=\"tml-form-col {$readonly}\">";
						$html .= $this->getControl($v , $isedit);
						$html .="\r\n\t\t\t\t\t\t\t</div>";
						break;
					case 'divider1':
						$html .='</div>';
						$html .="\r\n\t\t\t\t\t\t\t<div class=\"divider-content\">";
						$html .="\r\n\t\t\t\t\t\t\t<span></span></div>";
						$html .='<div class="tml-row">';
						break;
					case 'divider2':
						$html .='</div>';
						$html .="\r\n\t\t\t\t\t\t\t<div class=\"divider-content help\">";
						$html .="\r\n\t\t\t\t\t\t\t<span></span></div>";
						$html .='<div class="tml-row">';
						break;
					case 'datatable':
						$html .='</div>';
						$html .="\r\n\t\t\t\t\t\t\t<div class=\"tml-row {$readonly}\">";
						$html .= $this->getControl($v , $isedit);
						$html .="\r\n\t\t\t\t\t\t\t</div>";
						$html .='<div class="tml-row">';
						break;
					case 'fieldset':
						// 第一次生成fieldset时不需要生成结束标签
						if($tagCountHasinsert>0){
							$html .='</div>';
							$html.="</fieldset>";
						}else{
							$html .='</div>';
						}
						$html.="\r\n\t\t\t<fieldset>";
						$html.="\r\n\t\t\t\t<legend class=\"fieldset_legend_toggle\">";
						$html.="\r\n\t\t\t\t\t<b>{$v['title']}</b>";
						$html.="\r\n\t\t\t\t</legend>";
						$html .='<div class="tml-row">';
						$tagCountHasinsert ++;
						break;
				}
			}
			$html .="</div>";
			if($tagCountHasinsert){
				$html.="</fieldset>";
			}

			$html = str_replace('<div class="tml-row"></div>','',$html);
			$content_h.= $html;

		}
		return $content_h;
	}

	/**
	 * @Title: getControl
	 * @Description: todo(将组件属性生成组件实际html代码)
	 * @param array $controllProperty  组件信息
	 * @param boolean $isedit 是否是编辑，默认flase:新增
	 * @author quqiang
	 * @date 2014-8-21 上午10:12:43
	 * @throws
	 */
	private function getControl($controllProperty , $isedit=false , $isreturnvalue = false){
		if(!is_array($controllProperty)){
			return '';
		}
		$property = $this->getProperty($controllProperty['catalog']);
		if( $controllProperty[$property['checkfunc']['name']] ){
			$chtml=$controllProperty[$property['checkfunc']['name']];
		}
		$readonly = 0;
		$readonlyStr='';
		if(! $controllProperty[$property['islock']['name']] ){
			$chtml.=" readonly";
			//			$islockHtml="<div class=\"locked\"></div>";
			$readonly = 1;
			$readonlyStr = "readonly=\"readonly\" disabled=\"disabled\"";
		}

		$html = '';
		$prefTag = "\t\t\t<label>{\$fields[\"".$controllProperty[$property['fields']['name']]."\"]}:</label>\r\n\t\t\t\t\t\t\t\t";
		$org = '';
		if($controllProperty[$property['org']['name']]){
			$org = $controllProperty[$property['org']['name']];
		}
		$required = ''; // 必填验证
		if($controllProperty[$property['requiredfield']['name']]){
			$required = 'required';
		}
		$chtml = $required.' '. $chtml;
		switch ($controllProperty['catalog']){
			case 'text':
				if(!$isreturnvalue){
					// checkfor 属性设置
					// checkfor 当  $controllProperty[$property['checkfortable']['name']] 指定了表名，
					//$controllProperty[$property['checkforshow']['name']] 显示字段
					//$controllProperty[$property['checkforbindd']['name']] 绑定真实值
					if($controllProperty[$property['checkfortable']['name']]){
						$insert = $controllProperty[$property['checkforbindd']['name']]?htmlspecialchars("insert=\"".$controllProperty[$property['checkforbindd']['name']]."\""):''; //隐藏域绑定字段名
						$show = $controllProperty[$property['checkforshow']['name']]?htmlspecialchars("show=\"".$controllProperty[$property['checkforshow']['name']]."\""):''; // 显示字段名
						$table = $controllProperty[$property['checkfortable']['name']]?htmlspecialchars("checkfor=\"".$controllProperty[$property['checkfortable']['name']]."\""):''; //checkfor查询表
						$orderby = $controllProperty[$property['checkfororderby']['name']]?htmlspecialchars("order=\"".$controllProperty[$property['checkfororderby']['name']]."\""):''; // 排序条件
						$iswrite = $controllProperty[$property['checkforiswrite']['name']]?htmlspecialchars("iswrite=\"".$controllProperty[$property['checkforiswrite']['name']]."\""):''; // 是否清除未找到内容
						$map = $controllProperty[$property['checkformap']['name']]?htmlspecialchars("map=\"".$controllProperty[$property['checkformap']['name']]."\""):''; // 过滤条件
						$org = '';
						// 	时当前文本框 没得name属性，
						if($controllProperty[$property['checkforbindd']['name']]){
							$iswrite = '';

							$hiden="<input type=\"hidden\"  name=\"".
							$controllProperty[$property['fields']['name']]."\" value=\"".($isedit?"{\$vo['".
							$controllProperty[$property['fields']['name']]."']}":'')."\">";

							$html="\r\n\t\t\t\t\t{$prefTag}{$hiden}<input type=\"text\" {$readonlyStr}  class=\"checkByInput {$chtml} {$org}\" {$table} {$insert} {$show} {$orderby} {$iswrite} autocomplete=\"on\" value=\"".($isedit?"{\$vo['".
							$controllProperty[$property['fields']['name']]."']|getFieldBy='{$controllProperty[$property['checkforbindd']['name']]}','{$controllProperty[$property['checkforshow']['name']]}','{$controllProperty[$property['checkfortable']['name']]}'}":'')."\">";

						}else{
							$html="\r\n\t\t\t\t\t{$prefTag}<input type=\"text\" {$readonlyStr} name=\"".
							$controllProperty[$property['fields']['name']]."\" class=\"checkByInput {$chtml} {$org}\" {$table} {$insert} {$show} {$orderby} {$iswrite} value=\"".($isedit?"{\$vo['".
							$controllProperty[$property['fields']['name']]."']}":'')."\">";
						}
					}else{
						$html="\r\n\t\t\t\t\t{$prefTag}<input type=\"text\" {$readonlyStr} name=\"".
						$controllProperty[$property['fields']['name']]."\" class=\"{$chtml} {$org}\" value=\"".($isedit?"{\$vo['".
						$controllProperty[$property['fields']['name']]."']}":'')."\">";
					}
				}else{
					if($controllProperty[$property['checkfortable']['name']]){
						if($controllProperty[$property['checkforbindd']['name']]){
							$html="{\$vo['".$controllProperty[$property['fields']['name']]."']|getFieldBy='{$controllProperty[$property['checkforbindd']['name']]}','{$controllProperty[$property['checkforshow']['name']]}','{$controllProperty[$property['checkfortable']['name']]}'}";
						}else{
							$html="{\$vo['".$controllProperty[$property['fields']['name']]."']}";
						}
					}else{
						$html="{\$vo['".$controllProperty[$property['fields']['name']]."']}";
					}
				}
				$html = htmlspecialchars_decode($html);
				break;
			case 'select':
				$selected="";
				$html = "\r\n\t\t\t\t\t{$prefTag}<select {$readonlyStr} name=\"".$controllProperty[$property['fields']['name']]."\"  class=\"combox {$org} {$chtml} ".($v['quicktag']?'additem':'')."\"";
				$html.= ">";
				if(!$isreturnvalue){
					if( $controllProperty[$property['subimporttableobj']['name']] ){
						$html.="{:getControllbyHtml('table',array('type'=>'select','table'=>'{$controllProperty[$property['subimporttableobj']['name']]}','id'=>'{$controllProperty[$property['subimporttablefield2obj']['name']]}','name'=>'{$controllProperty[$property['subimporttablefieldobj']['name']]}','selected'=>\$vo['{$controllProperty[$property['fields']['name']]}']))}";
					}else{
						$html.="{:getControllbyHtml('selectlist',array('type'=>'select','key'=>'{$controllProperty[$property['showoption']['name']]}','selected'=>\$vo['{$controllProperty[$property['fields']['name']]}']))}";
					}
					$html.= "\r\n\t\t\t\t\t\t\t\t</select>";
				}else{
					if( $controllProperty[$property['subimporttableobj']['name']] ){
						$html="{\$vo['".$controllProperty[$property['fields']['name']]."']|getFieldBy='";
						$html .="{$controllProperty[$property['subimporttablefield2obj']['name']]}'";
						$html .=",'{$controllProperty[$property['subimporttablefieldobj']['name']]}',";
						$html .="'{$controllProperty[$property['subimporttableobj']['name']]}'}";

					}else{
						$html="{\$vo['{$controllProperty[$property['fields']['name']]}']|getSelectlistValue='{$controllProperty[$property['showoption']['name']]}'}";
					}
				}
				break;
			case 'checkbox':
				$checked="";
				$html = "\r\n\t\t\t\t\t{$prefTag}";
				if(!$isreturnvalue){
					//					if( $controllProperty[$property['subimporttableobj']['name']] ){
					//						if( $isedit ){
					//							$checked="<if condition=\"in_array(\$vo".$controllProperty[$property['fields']['name']]."['".$controllProperty[$property['subimporttablefield2obj']['name']]."'],\$vo['".$controllProperty[$property['fields']['name']]."'])\"> checked=\"checked\" </if> ";
					//						}
					//						$html.="\r\n\t\t\t\t\t".$prefTag;
					//						$html.="\r\n\t\t\t\t\t\t<volist name=\"".$controllProperty[$property['fields']['name']]."list\" id=\"vo".$controllProperty[$property['fields']['name']]."\">";
					//						$html.="\r\n\t\t\t\t\t\t<input type=\"checkbox\" value=\"{\$vo".$controllProperty[$property['fields']['name']]."['".$controllProperty[$property['subimporttablefield2obj']['name']]."']}\" ".$checked." name=\"".$controllProperty[$property['fields']['name']]."[]\" > {\$vo".$controllProperty[$property['fields']['name']]."[".$controllProperty[$property['subimporttablefieldobj']['name']]."]}";
					//						$html.="\r\n\t\t\t\t\t\t</volist>".$islockHtml;
					//
					//						//					$html.="\r\n\t\t\t\t\t\t\t\t\t{:getDataBaseByHtml('{$controllProperty[$property['subimporttableobj']['name']]}',array('id'=>'{$controllProperty[$property['subimporttablefield2obj']['name']]}','name'=>'{$controllProperty[$property['subimporttablefieldobj']['name']]}','names'=>'{$controllProperty[$property['fields']['name']]}[]','type'=>'checkbox','selected'=>\$vo['{$controllProperty[$property['fields']['name']]}']))}";
					//						//					$html.="\r\n\t\t\t\t\t\t\t\t\t{:getDataBaseByHtml('{$controllProperty[$property['subimporttableobj']['name']]}',array('id'=>'{$controllProperty[$property['subimporttablefield2obj']['name']]}','name'=>'{$controllProperty[$property['subimporttablefieldobj']['name']]}','names'=>'{$controllProperty[$property['fields']['name']]}[]','type'=>'checkbox','selected'=>\$vo['{$controllProperty[$property['fields']['name']]}'],'readonly'=>{$readonly}))}";
					//					}else{
					//						if( $controllProperty[$property['showoption']['name']] ){
					//							$html.="\r\n\t\t\t\t\t".$prefTag;
					//							if( $isedit ){
					//								$checked="<if condition=\"in_array(\$vo".$controllProperty[$property['fields']['name']]."['key'] , \$vo['{$controllProperty[$property['fields']['name']]}'])\">checked=\"checked\"</if>";
					//							}
					//							if( $controllProperty[$property['showoption']['name']]){
					//								$html.="\r\n\t\t\t\t\t\t<volist name=\"selectlist".$controllProperty[$property['fields']['name']]."\" id=\"vo".$controllProperty[$property['fields']['name']]."\">";
					//								$html.="\r\n\t\t\t\t\t\t\t\t<input ".$checked." name=\"".$controllProperty[$property['fields']['name']]."[]\" type=\"checkbox\" value=\"{\$vo".$controllProperty[$property['fields']['name']]."['key']}\" name=\"".$controllProperty[$property['fields']['name']]."[]\" /> {\$vo".$controllProperty[$property['fields']['name']]."['val']}";
					//								$html.="\r\n\t\t\t\t\t\t</volist>".$islockHtml;
					//							}
					//						}
					//						//					$html.="\r\n\t\t\t\t\t\t\t\t\t{:getSelectlistByHtml('{$controllProperty[$property['showoption']['name']] }','checkbox',\$vo['{$controllProperty[$property['fields']['name']]}']),'{$controllProperty[$property['fields']['name']]}[]'}";
					//						//					$html.="\r\n\t\t\t\t\t\t\t\t\t{:getSelectlistByHtml('{$controllProperty[$property['showoption']['name']] }', array('type'=>'checkbox','selected'=>\$vo['{$controllProperty[$property['fields']['name']]}'],'name'=>'{$controllProperty[$property['fields']['name']]}[]','readonly'=>{$readonly}))}";
					//
					//					}
					if( $controllProperty[$property['subimporttableobj']['name']] ){
						$html.="{:getControllbyHtml('table',array('type'=>'checkbox','names'=>'{$controllProperty[$property['fields']['name']]}[]','table'=>'{$controllProperty[$property['subimporttableobj']['name']]}','id'=>'{$controllProperty[$property['subimporttablefield2obj']['name']]}','name'=>'{$controllProperty[$property['subimporttablefieldobj']['name']]}','selected'=>\$vo['{$controllProperty[$property['fields']['name']]}']))}";
					}else{
						$html.="{:getControllbyHtml('selectlist',array('type'=>'checkbox','names'=>'{$controllProperty[$property['fields']['name']]}[]','key'=>'{$controllProperty[$property['showoption']['name']]}','selected'=>\$vo['{$controllProperty[$property['fields']['name']]}']))}";
					}

				}else{
					if( $controllProperty[$property['subimporttableobj']['name']] ){
						$html="{\$vo['".$controllProperty[$property['fields']['name']]."']|excelTplidTonameAppend='";
						$html .="{$controllProperty[$property['subimporttablefieldobj']['name']]}'";
						$html .=",'{$controllProperty[$property['subimporttableobj']['name']]}'}";
					}else{
						$html="{\$vo['{$controllProperty[$property['fields']['name']]}']|excelTplidTonameAppend='{$controllProperty[$property['showoption']['name']]}'}";
					}
				}
				break;
			case 'radio':
				$checked="";
				if(!$isreturnvalue){
					$html = "\r\n\t\t\t\t\t{$prefTag}";
					/*if( $controllProperty[$property['subimporttableobj']['name']] ){
						if( $isedit ){
						//$checked="<if condition=\"\$vo{$v['linktable_field']}['".$v['field']."'] eq \$vo{$v['linktable_field']}"."['".$v['linktable_field']."']> checked=\"checked\" </if> ";
						$checked="<if condition=\"\$vo.".$controllProperty[$property['fields']['name']]." eq \$vo{$controllProperty[$property['subimporttablefield2obj']['name']]}.".$controllProperty[$property['subimporttablefield2obj']['name']]."\">checked=\"checked\"</if> ";
						}
						$html.="\r\n\t\t\t\t\t{$prefTag}<volist name=\"".$controllProperty[$property['fields']['name']]."list\" id=\"vo".$controllProperty[$property['subimporttablefield2obj']['name']]."\">";
						$html.="\r\n\t\t\t\t\t\t<input type=\"radio\" ";
						$html.="value=\"{\$vo{$controllProperty[$property['subimporttablefield2obj']['name']]}[".$controllProperty[$property['subimporttablefield2obj']['name']]."]}\" ";
						$html.=$checked." name=\"".$controllProperty[$property['fields']['name']]."\" >";
						$html.="{\$vo{$controllProperty[$property['subimporttablefield2obj']['name']]}[".$controllProperty[$property['subimporttablefieldobj']['name']]."]}";
						$html.="\r\n\t\t\t\t\t\t</volist>".$islockHtml;

						//$html.="\r\n\t\t\t\t\t\t\t\t\t{:getDataBaseByHtml('{$controllProperty[$property['subimporttableobj']['name']]}',array('id'=>'{$controllProperty[$property['subimporttablefield2obj']['name']]}','name'=>'{$controllProperty[$property['subimporttablefieldobj']['name']]}','names'=>'{$controllProperty[$property['fields']['name']]}','type'=>'radio','selected'=>\$vo['{$controllProperty[$property['fields']['name']]}'],'readonly'=>{$readonly}))}";
						}else{
						$html.="\r\n\t\t\t\t\t{$prefTag}";
						if( $controllProperty[$property['showoption']['name']] ){
						if( $isedit ){
						$checked="<if condition=\"\$vo".$controllProperty[$property['fields']['name']]."['key'] eq \$vo['{$controllProperty[$property['fields']['name']]}']\">checked=\"checked\"</if>";
						}
						if( $controllProperty[$property['showoption']['name']] ){
						$html.="\r\n\t\t\t\t\t\t<volist name=\"selectlist".$controllProperty[$property['fields']['name']]."\" id=\"vo".$controllProperty[$property['fields']['name']]."\">";
						$html.="\r\n\t\t\t\t\t\t\t\t<input ".$checked." name=\"".$controllProperty[$property['fields']['name']]."\" type=\"radio\" value=\"{\$vo".$controllProperty[$property['fields']['name']]."['key']}\" name=\"".$controllProperty[$property['fields']['name']]."\" /> {\$vo".$controllProperty[$property['fields']['name']]."['val']}";
						$html.="\r\n\t\t\t\t\t\t</volist>".$islockHtml;
						}
						}
						//$html.="\r\n\t\t\t\t\t\t\t\t\t{:getSelectlistByHtml('{$controllProperty[$property['showoption']['name']] }', array('type'=>'radio','selected'=>\$vo['{$controllProperty[$property['fields']['name']]}'],'name'=>'{$controllProperty[$property['fields']['name']]}','readonly'=>{$readonly}))}";
						}
						*/



					if( $controllProperty[$property['subimporttableobj']['name']] ){
						$html.="{:getControllbyHtml('table',array('type'=>'radio','names'=>'{$controllProperty[$property['fields']['name']]}','table'=>'{$controllProperty[$property['subimporttableobj']['name']]}','id'=>'{$controllProperty[$property['subimporttablefield2obj']['name']]}','name'=>'{$controllProperty[$property['subimporttablefieldobj']['name']]}','selected'=>\$vo['{$controllProperty[$property['fields']['name']]}']))}";
					}else{
						$html.="{:getControllbyHtml('selectlist',array('type'=>'radio','names'=>'{$controllProperty[$property['fields']['name']]}','key'=>'{$controllProperty[$property['showoption']['name']]}','selected'=>\$vo['{$controllProperty[$property['fields']['name']]}']))}";
					}



				}else{
					if( $controllProperty[$property['subimporttableobj']['name']] ){
						$html="{\$vo['".$controllProperty[$property['fields']['name']]."']|getFieldBy='";
						$html .="{$controllProperty[$property['subimporttablefield2obj']['name']]}'";
						$html .=",'{$controllProperty[$property['subimporttablefieldobj']['name']]}',";
						$html .="'{$controllProperty[$property['subimporttableobj']['name']]}'}";
					}else{
						$html="{\$vo['{$controllProperty[$property['fields']['name']]}']|getSelectlistValue='{$controllProperty[$property['showoption']['name']]}'}";
					}
				}
				break;
			case 'textarea':
				if(!$isreturnvalue){
					$rows =2;
					$cols = 100;
					if($controllProperty[$property['rows']['name']])
					$rows = $controllProperty[$property['rows']['name']];
					if($controllProperty[$property['cols']['name']])
					$cols = $controllProperty[$property['cols']['name']];
					$html="\r\n\t\t\t\t\t{$prefTag}<textarea {$readonlyStr} cols=\"{$cols}\" rows=\"{$rows}\" class=\"{$chtml}\" name=\"".$controllProperty[$property['fields']['name']]."\">".($isedit?"{\$vo['".$controllProperty[$property['fields']['name']]."']}":'')."</textarea>".$islockHtml;
				}else{
					$html="<pre>{\$vo['{$controllProperty[$property['fields']['name']]}']}</pre>";
				}
				break;
			case 'date':
				$style="";
				$format = '';
				$format1 = '';
				if($controllProperty[$property['format']['name']]){
					$temp = '';
					$temp = explode('@', $controllProperty[$property['format']['name']]);
					$format = "format=\"{dateFmt:'".$temp[0]."'}\"";
					$format1 = "='{$temp[1]}'";
				}
				if(!$isreturnvalue){
					if($isedit){
						$html ="\r\n\t\t\t\t\t{$prefTag}<div {$readonlyStr} class=\"tml-input-append\">";
						$html .="\r\n\t\t\t\t\t<input type=\"text\" name=\"".$controllProperty[$property['fields']['name']]."\" class=\"Wdate js-wdate {$chtml}\" {$format} value=\"{\$vo['".$controllProperty[$property['fields']['name']]."']|transtime{$format1}}\"/>";
						$html .="\r\n\t\t\t\t\t<a href=\"javascript:;\" class=\"input-addon input-addon-calendar js-inputCheckDate\">选择</a>";
						$html .="\r\n\t\t\t\t</div>";
					}else{
						$html ="\r\n\t\t\t\t\t{$prefTag}<div {$readonlyStr} class=\"tml-input-append\">";
						$html .="\r\n\t\t\t\t\t<input type=\"text\" name=\"".$controllProperty[$property['fields']['name']]."\" class=\"Wdate js-wdate {$chtml}\" {$format} value=\"\"/>";
						$html .="\r\n\t\t\t\t\t<a href=\"javascript:;\" class=\"input-addon input-addon-calendar js-inputCheckDate\">选择</a>";
						$html .="\r\n\t\t\t\t</div>";
					}
				}else{
					$html="{\$vo['".$controllProperty[$property['fields']['name']]."']|transtime{$format1}}";
				}
				break;
			case 'checkfor':
				break;
			case 'lookup':
				if(!$isreturnvalue){
					$urls = $controllProperty[$property['geturls']['name']];
					$lookupgroup = $controllProperty[$property['lookupgroup']['name']];
					$filedback = $controllProperty[$property['filedback']['name']];
					$model = $controllProperty[$property['model']['name']];
					$orgchar = $controllProperty[$property['lookuporg']['name']]?$controllProperty[$property['lookuporg']['name']]:'name';
					$orgval = $controllProperty[$property['lookuporgval']['name']]?$controllProperty[$property['lookuporgval']['name']]:'id';
					$conditions = $controllProperty[$property['conditions']['name']]; // auditState,3;status,1;ischange,0
					$title = "{\$fields[\"".$controllProperty[$property['fields']['name']]."\"]}";

					$val='';
					$tempModel =D($model);
					$html ="\r\n\t\t\t\t\t\t{$prefTag}<div class=\"tml-input-append2\">";
					$html .="\r\n\t\t\t\t\t\t<input type=\"text\" class=\"{$lookupgroup}.{$orgchar} {$chtml} \" autocomplete=\"off\" readonly=\"readonly\" disabled=\"disabled\"  value=\"".($isedit?"{\$vo['".$controllProperty[$property['fields']['name']]."']|getFieldBy='{$orgval}','{$orgchar}','{$tempModel->getTableName()}'}":'')."\" />";
					$html .="\r\n\t\t\t\t\t\t<input type=\"hidden\" class=\"{$lookupgroup}.{$orgval}\" name=\"".$controllProperty[$property['fields']['name']]."\" value=\"{\$vo['".$controllProperty[$property['fields']['name']]."']}\"  />";
					if($readonly){
						$html .="\r\n\t\t\t\t\t\t<a class=\"input-addon input-addon-addon input-addon-add\">$title</a>";
						$html .="\r\n\t\t\t\t\t\t<a title=\"清空信息\" class=\"input-addon input-addon-recycle\"  href=\"javascript:void(0);\"></a>";
					}else{
						$html .="\r\n\t\t\t\t\t\t<a class=\"input-addon input-addon-addon input-addon-add\" param=\"field={$filedback}&model={$model}&conditions={$conditions}\" href=\"__URL__/{$urls}\" lookupGroup=\"{$lookupgroup}\" >$title</a>";
						$html .="\r\n\t\t\t\t\t\t<a title=\"清空信息\" class=\"input-addon input-addon-recycle\"  href=\"javascript:void(0);\" onclick=\"clearInputInDialog(this,'{$controllProperty[$property['fields']['name']]},{$orgchar}');\"></a>";
					}
					$html .="\r\n\t\t\t\t\t\t</div>";
				}else{
					$html="{\$vo['".$controllProperty[$property['fields']['name']]."']}";
				}
				break;
			case 'upload':
				if($isedit){
					if($readonly){
						$html.="\r\n\t\t\t\t\t{:W('ShowUpload',\$attarry)}".$islockHtml;
					}else{
						$html.="\r\n\t\t\t\t\t{:W('ShowUpload',\$attarry)}".$islockHtml;
					}
				}else{
					$html.="\r\n\t\t\t\t\t{:W('ShowUpload')}";
				}
				break;
			case 'datatable':
				$titles = "\t\t\t<div class=\"tml-h3\">{\$fields[\"".$controllProperty[$property['fields']['name']]."\"]}:</div>\r\n\t\t\t\t\t\t\t\t";
				/* 获取字段信息 */
				$filedTag = $property['fieldlist']['name'];// 获取配置信息中的标识名称
				// 获取所有节点
				$configPath = $this->getAutoFormConfig();
				if(file_exists($configPath)){
					$selectlist = require $this->getAutoFormConfig();
				}
				$controlTag = $controllProperty[$property['fields']['name']]; // 组件唯一标识  datatable8,......
				if($selectlist){
					$innerTableFieldInfo = $selectlist[$controlTag][$filedTag]; // 获取到内嵌表字段设置信息
				}
				$innerTableFieldInfoObj = json_decode(htmlspecialchars_decode($innerTableFieldInfo));// 得到字段信息json对象数据

				if(!$isreturnvalue){
					// 遍历成页面字段列表。。
					/*  表格头生成  */
					$html="\r\n\t\t\t\t\t{$titles}";
					$html.="<table class=\"itemDetail data-table tml-table-w\" addButton=\"新增行\" showrow=\"true\" width=\"100%\" showbutton=\"".($readonly?0:1)."\" >";
					$html.="\r\n\t\t\t\t\t\t<thead>";
					$html.="\r\n\t\t\t\t\t\t\t<tr>";
					// 绑定页面模板遍历字段名称
					$fileBind = '';
					// 遍历生成表格头
					/**

					fieldname	字段名
					fieldtitle	中文标题
					fieldtype	字段类型
					fieldlength	字段长度
					fieldshowtype	显示组件类型
					fieldshow		显示状态

					*/
					foreach ($innerTableFieldInfoObj[0] as $key=>$val){
						if($val->fieldshow){

							//$html.="\r\n\t\t\t\t\t\t\t\t<th>".$val->fieldname."</th>";
							$html.="\r\n\t\t\t\t\t\t\t\t<th type=\"{$val->fieldshowtype}\" name=\"datatable[#index#][{$controlTag}][".$val->fieldname."]\" size=\"30\" fieldClass=\"required\">".$val->fieldtitle."</th>";
							switch ($val->fieldshowtype) {
								case 'text':
									$fileBind.="\r\n\t\t\t\t\t\t\t\t<td><span class=\"xyInputWithUnit\"><input type=\"text\" name=\"datatable[{\$key}][{$controlTag}][".$val->fieldname."]\" value=\"{\$item.".$val->fieldname."}\" class=\"required textInput\"></span></td>";
									break;
								case "date":
									unset($temp);
									$temp="<div>";
									$temp.="<span class=\"xyInputWithUnit\">";
									$temp.="<input type=\"text\" format=\"yyyy-MM-dd\" class=\"date required \" value=\"{\$item.".$val->fieldname."}\" name=\"datatable[{\$key}][{$controlTag}][".$val->fieldname."]\" \">";
									$temp.="<a href=\"javascript:void(0)\" class=\"inputDateButton\">选择</a>";
									$temp.="</span>";
									$temp.="</div>";

									$fileBind.="\r\n\t\t\t\t\t\t\t\t<td>{$temp}</td>";
									break;
							}


						}
					}
					$html.="\r\n\t\t\t\t\t\t\t\t<th type=\"del\" width=\"60\">操作</th>";
					$html.="\r\n\t\t\t\t\t\t\t</tr>";
					$html.="\r\n\t\t\t\t\t\t</thead>";
					$html.="\r\n\t\t\t\t\t\t<tbody>";

					//////////////

					// 模板数据遍历
					$html.="\r\n\t\t\t\t\t\t\t{~\$key=0;}";
					$html.="\r\n\t\t\t\t\t\t\t<volist name=\"innerTabelObj".$controlTag."Data\" id=\"item\">";
					$html.="\r\n\t\t\t\t\t\t\t<tr>";
					$html.=$fileBind;
					$html.="\r\n\t\t\t\t\t\t\t<td>";
					$html.="\r\n\t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"datatable[{\$key}][{$controlTag}][id]\" value=\"{\$item.id}\" />";
					if(!$readonly){
						$html.="\r\n\t\t\t\t\t\t\t\t<a class=\"btnDel \" onclick=\"del_sub_info('".$this->nodeName."','".$this->tableName.'_sub_'.$controlTag."',{\$item.id})\" href=\"javascript:void(0)\">删除</a>";
					}
					$html.="\r\n\t\t\t\t\t\t\t</td>";
					$html.="\r\n\t\t\t\t\t\t\t{~\$key++}";
					$html.="\r\n\t\t\t\t\t\t\t</tr>";
					$html.="\r\n\t\t\t\t\t\t\t</volist>";
					// 数据遍历end

					$html.="\r\n\t\t\t\t\t\t</tbody>";
					$html.="</table>";
				}else{
					// 遍历成页面字段列表。。
					/*  表格头生成  */
					$html.="\r\n\t\t\t<table class=\"data-table tml-table-w\" >";
					$html.="\r\n\t\t\t\t<thead>";
					$html.="\r\n\t\t\t\t\t<tr>";
					// 绑定页面模板遍历字段名称
					$fileBind = '';
					// 遍历生成表格头
					foreach ($innerTableFieldInfoObj[0] as $key=>$val){
						$html.="\r\n\t\t\t\t\t\t<th>".$val->fieldtitle."</th>";
						$fileBind.="\r\n\t\t\t\t\t\t<td>{\$item.".$val->fieldname."}</td>";
					}
					$html.="\r\n\t\t\t\t\t</tr>";
					$html.="\r\n\t\t\t\t</thead>";
					$html.="\r\n\t\t\t\t<tbody>";

					//////////////

					// 模板数据遍历
					$html.="\r\n\t\t\t\t\t{~\$key=0;}";
					$html.="\r\n\t\t\t\t\t<volist name=\"innerTabelObj".$controlTag."Data\" id=\"item\">";
					$html.="\r\n\t\t\t\t\t<tr>";
					$html.=$fileBind;
					$html.="\r\n\t\t\t\t\t{~\$key++}";
					$html.="\r\n\t\t\t\t\t</tr>";
					$html.="\r\n\t\t\t\t\t</volist>";
					// 数据遍历end

					$html.="\r\n\t\t\t\t</tbody>";
					$html.="\r\n\t\t\t</table>\r\n\t\t";
				}
				break;
			default:
				$html = '';
		}
		return $html;
	}


	/**
	 * @Title: getToolBar
	 * @Description: todo(获取页面权限)
	 * @param boolean $isaudit 是否是审批流
	 * @return array:最终页面权限信息
	 * @author quqiang
	 * @date 2014-8-21 上午10:27:08
	 * @throws
	 */
	private function getToolBar($isaudit = false){
		$temp = array();
		if($isaudit){
			//新增
			$temp['js-add'] = array(
					'ifcheck' => '1',
					'permisname' => strtolower($this->nodeName).'_add',
					'html' => '<a class="add js-add tml-btn tml_look_btn tml_mp" href="__URL__/add" rel="__MODULE__add" target="navTab"  title="'.$this->nodeTitle.'_新增"><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
					'shows' => '1',
					'sortnum' => '1',
			);
			//删除
			$temp['js-delete'] = array(
					'ifcheck' => '1',
					'rules' => '#auditState#==0||#auditState#==-1',
					'permisname' => strtolower($this->nodeName).'_delete',
					'html' => '<a class="js-delete delete tml-btn tml_look_btn tml_mp" href="__URL__/delete/id/{sid_node}/navTabId/__MODULE__" target="ajaxTodo" title="你确定要删除吗？" warn="请选择一条组记录"><span><span class="icon icon-trash icon_lrp"></span>删除</span></a>',
					'shows' => '1',
					'sortnum' => '2',
			);
			//edit按钮
			$temp['js-edit'] = array(
					'ifcheck' => '1',
					'rules' => '#auditState#==0||#auditState#==-1',
					'permisname' => strtolower($this->nodeName).'_edit',
					'html' => '<a class="edit js-edit tml-btn tml_look_btn tml_mp" href="__URL__/edit/id/{sid_node}" rel="__MODULE__edit" target="navTab"  title="'.$this->nodeTitle.'_修改"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
					'shows' => '1',
					'sortnum' => '3',
			);
			$temp['js-view'] = array(
					'ifcheck' => '0',
					'permisname' => strtolower($this->nodeName).'_view',
					'html' => '<a class="js-view icon tml-btn tml_look_btn tml_mp" href="__URL__/auditView/id/{sid_node}" rel="__MODULE__view" target="navTab"   title="'.$this->nodeTitle.'_查看"><span><span class="icon icon-eye-open icon_lrp"></span>查看</span></a>',
					'shows' => '1',
					'sortnum' => '4',
			);
			//单据撤回
			$temp['js-iconBack'] = array(
					'ifcheck' => '1',
					'rules' => '#auditState#==1',
					'permisname' => strtolower($this->nodeName).'_add',
					'html' => '<a class="js-iconBack tbundo tml-btn tml_look_btn tml_mp" href="__URL__/lookupGetBackprocess/id/{sid_node}/navTabId/__MODULE__" warn="请选择节点" target="ajaxTodo" title="您确定要撤回单据吗?"><span><span class="icon icon-external-link icon_lrp"></span>单据撤回</span></a>',
					'shows' => '1',
					'sortnum' => '5',
			);
		}else{
			$temp['js-add'] = array(
					'ifcheck' => '1',
					'permisname' => strtolower($this->nodeName).'_add',
					'html' => '<a class="add js-add tml-btn tml_look_btn tml_mp" href="__URL__/add" target="navTab" rel="__MODULE__add"  title="'.$this->nodeTitle.'_新增"><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
					'shows' => '1',
					'sortnum' => '1',
			);
			$temp['js-edit'] = array(
					'ifcheck' => '1',
					'permisname' => strtolower($this->nodeName).'_edit',
					'html' => '<a class="edit js-edit tml-btn tml_look_btn tml_mp"  href="__URL__/edit/id/{sid_node}"  target="navTab" title="'.$this->nodeTitle.'_修改"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
					'shows' => '1',
					'sortnum' => '3',
			);
			$temp['js-view'] = array(
					'ifcheck' => '0',
					'permisname' => strtolower($this->nodeName).'_view',
					'html' => '<a class="js-view icon tml-btn tml_look_btn tml_mp" href="__URL__/view/id/{sid_node}" target="navTab"  title="'.$this->nodeTitle.'_查看"><span><span class="icon icon-eye-open icon_lrp"></span>查看</span></a>',
					'shows' => '1',
					'sortnum' => '4',
			);
			$temp['js-delete'] = array(
					'ifcheck' => '1',
					'permisname' => strtolower($this->nodeName).'_delete',
					'html' => '<a class="js-view icon tml-btn tml_look_btn tml_mp" href="__URL__/delete/id/{sid_node}/navTabId/__MODULE__" target="ajaxTodo" title="你确定要删除吗？" warn="请选择一条组记录"><span><span class="icon icon-trash icon_lrp"></span>删除</span></a>',
					'shows' => '1',
					'sortnum' => '2',
			);

		}
		return $temp;
	}


	/**
	 * @Title: modifyConfig
	 * @Description: todo(更新字段配置文件和单号规则配置文件)
	 * @param array $fieldData	组件属性列表
	 * @param string $nodename	当前action名称
	 * @param boolean $isaudit	是否审核
	 * @author quqiang
	 * @date 2014-8-22 上午09:38:49
	 * @throws
	 */
	private function modifyConfig($fieldData,$nodename , $isaudit){

		$nodename = ucfirst($nodename);
		$controllPropertyList = $this->controlConfig;
		$publicProperty = $this->publicProperty;

		/*********************************
		 *  生成权限、DB字段配置文件
		 *********************************/
		$model = D('SystemConfigDetail');
		$detailList=$searchlist=$toolbar=array();
		$except=array("createid","updateid","createtime","updatetime","ptmptid","auditUser","alreadyAuditUser","ostatus");
		$toolbar = $this->getToolBar($isaudit);
		$controllProperty = $this->getProperty($category);
		$k1=0;
		foreach ($fieldData as $k => $v1) {

			$iscreateDB = $controllPropertyList[$v1[$publicProperty['catalog']['name']]]['iscreate'];
			$iscreateConfifg = $controllPropertyList[$v1[$publicProperty['catalog']['name']]]['isconfig'];
			if($iscreateDB){
				$controllProperty = $this->getProperty($v1['catalog']);
				if($v1[$controllProperty['searchlist']['name']]){ // 是否添加到搜索列表
					$searchlist[$v1[$controllProperty['fields']['name']]]["field"]=$v1[$controllProperty['searchlist']['name']];
					$searchlist[$v1[$controllProperty['fields']['name']]]["shows"]=$v1[$controllProperty['allsearchlist']['name']];
				}
				$detailList[$k1]['name']=$v1[$controllProperty['fields']['name']];
				$detailList[$k1]['showname']=$v1[$controllProperty['title']['name']];
				$detailList[$k1]['shows']=$iscreateConfifg?1:0;
				$detailList[$k1]['widths']='';
				$detailList[$k1]['sorts']=0;
				$detailList[$k1]['models']='';
				$detailList[$k1]['sortname']=$v1[$controllProperty['fields']['name']];
				$detailList[$k1]['sortnum']=$k1+1;

				$obj = M($nodename);
				$tableName = $obj->getTableName();
				$detailList[$k1]['searchField'] = $v1[$controllProperty['fields']['name']]?$tableName.'.'.$v1[$controllProperty['fields']['name']]:"";
				$detailList[$k1]['conditions'] = $v1[$controllProperty['conditions']['name']]?$v1[$controllProperty['conditions']['name']]:"";

				if($v1[$controllProperty['catalog']['name']] == 'date'){
					$detailList[$k1]['type'] = 'time';
				}else{
					$detailList[$k1]['type'] = $v1[$controllProperty['catalog']['name']]?$v1[$controllProperty['catalog']['name']]:"";
				}

				$detailList[$k1]['issearch'] = $v1[$controllProperty['searchlist']['name']]?$v1[$controllProperty['searchlist']['name']]:0; //局部检索
				$detailList[$k1]['isallsearch'] = $v1[$controllProperty['allsearchlist']['name']]?$v1[$controllProperty['allsearchlist']['name']]:0;//全局检索
				$detailList[$k1]['searchsortnum'] = $k1;

				if($v1[$controllProperty['catalog']['name']]=="date"){
					$fun=$fund=array();
					$fun[0][0]="transtime";
					$detailList[$k1]['func']=$fun;
					$fd=array("###");
					$fund[0][0]=$fd;
					$detailList[$k1]['funcdata']=$fund;
				}else if($v1[$controllProperty['catalog']['name']]=="select" || $v1[$controllProperty['catalog']['name']]=="radio"){
					if($v1[$controllProperty['subimporttableobj']['name']]){
						$fun=$fund=array();
						$fun[0][0]="getFieldBy";
						$detailList[$k1]['func']=$fun;
						$fd=array("###",$v1[$controllProperty['subimporttablefield2obj']['name']],$v1[$controllProperty['subimporttablefieldobj']['name']],$v1[$controllProperty['subimporttableobj']['name']]);
						$fund[0][0]=$fd;
						$detailList[$k1]['funcdata']=$fund;
						$detailList[$k1]['table'] = $v1[$controllProperty['subimporttableobj']['name']]?$v1[$controllProperty['subimporttableobj']['name']]:"";
						$detailList[$k1]['field'] = $v1[$controllProperty['subimporttablefieldobj']['name']]?$v1[$controllProperty['subimporttablefieldobj']['name']]:"";

					}else{
						$fun=$fund=array();
						$fun[0][0]="getSelectlistValue";
						$detailList[$k1]['func']=$fun;
						$fds=implode(",",explode(";",$v1[$controllProperty['showoption']['name']]));
						$fd=array("###",$fds);
						$fund[0][0]=$fd;
						$detailList[$k1]['funcdata']=$fund;
					}
				}else if($v1[$controllProperty['catalog']['name']]=="lookup"){
					$fun=$fund=array();
					$fun[0][0]="getFieldBy";
					$detailList[$k1]['func']=$fun;
					$fd=array("###",$v1[$controllProperty['lookuporgval']['name']],$v1[$controllProperty['lookuporg']['name']],$v1[$controllProperty['model']['name']]);
					$fund[0][0]=$fd;
					$detailList[$k1]['funcdata']=$fund;
					$detailList[$k1]['table'] = $v1[$controllProperty['model']['name']]?$v1[$controllProperty['model']['name']]:"";
					$detailList[$k1]['field'] = $v1[$controllProperty['lookuporg']['name']]?$v1[$controllProperty['lookuporg']['name']]:"";
				}else if($v1[$controllProperty['catalog']['name']]=="checkbox"){
					if($v1[$controllProperty['subimporttableobj']['name']]){
						$fun=$fund=array();
						$fun[0][0]="excelTplidTonameAppend";
						$detailList[$k1]['func']=$fun;
						$fd=array("###",$v1[$controllProperty['subimporttablefieldobj']['name']],$v1[$controllProperty['subimporttableobj']['name']]);
						$fund[0][0]=$fd;
						$detailList[$k1]['funcdata']=$fund;
						$detailList[$k1]['table'] = $v1[$controllProperty['subimporttableobj']['name']]?$v1[$controllProperty['subimporttableobj']['name']]:"";
						$detailList[$k1]['field'] = $v1[$controllProperty['subimporttablefield2obj']['name']]?$v1[$controllProperty['subimporttablefield2obj']['name']]:"";
					}else{
						$fun=$fund=array();
						$fun[0][0]="getSelectlistValue";
						$detailList[$k1]['func']=$fun;
						$fds=implode(",",explode(";",$v1[$controllProperty['showoption']['name']]));
						$fd=array("###",$fds,"checkbox");
						$fund[0][0]=$fd;
						$detailList[$k1]['funcdata']=$fund;
					}
				}else if($v1[$controllProperty['catalog']['name']]=="datatable"){
					//$detailList[$k1]['shows']=0;
				}

				if( in_array($v1[$controllProperty['fields']['name']],$except)){
					//					$detailList[$k1]['shows']=0;
				}
				$k1 ++ ;
			}
		}
		//array_unshift ($detailList,array('name' => 'id','showname' => '编号','shows' => '1','widths' => '','sorts' => '0','models' => '','sortname' => 'id','sortnum' => '1'));
		if( $this->isaudit){
			array_push($detailList, array('name' => 'auditState','showname' => '审核状态','shows' => '1','widths' => '','sorts' => '0','models' => '','sortname' => 'auditState','func' => array('0' => array('0' => 'getAuditState',),),'funcdata' => array('0' => array('0' => array('0' => '###','1' => '#id#','2' => '#ptmptid#',),),),'sortnum' =>(count($detailList)+1)));
		}else{
			array_push ($detailList,array('name' => 'action','showname' => '操作','shows' => '1','widths' => '','sorts' => '0','models' => '','sortname' => 'status','func' => array('0' => array('0' => 'showStatus',),),'funcdata' => array('0' => array('0' => array('0' => '#status#','1' => '#id#',),),),'sortnum' => (count($detailList)+1)));
		}
		$model->setDetail($nodename, $detailList);
		//if( count($searchlist) ) $model->setDetail($nodename, $searchlist,"searchby");
		if( count($toolbar) ) $model->setDetail($nodename, $toolbar,"toolbar");

		/*****************************************************
		 * 	更新单号规则数据。
		 *****************************************************/
		$modelNumber=D('SystemConfigNumber');
		$id = $this->tableName;
		$vo = $modelNumber->GetRules($id);
		if(file_exists($modelNumber->GetFile())){
			require $modelNumber->GetFile();
		}
		if(!$vo){
			$aryRule[$id] = array(
					'rulename'=>'动态表单自动生成：'.$this->nodeTitle,
					'rule'=>substr(ucfirst($this->nodeName), 0,1).'yearmothdaynum',
					'year'=>date('Y',time()),
					'moth'=>date('m',time()),
					'day'=>date('d',time()),
					'num'=>5,
					'writable'=>0,
					'status'=>1,
					'isprocess'=>1,
					'modelname'=>ucfirst($this->nodeName)
			);
		}else{
			foreach ($aryRule as $key => $val) {
				if ($key == $id) {
					$aryRule[$id] = array(
							'rulename'=>'动态表单自动生成：'.$this->nodeTitle,
							'rule'=>substr(ucfirst($this->nodeName), 0,1).'yearmothdaynum',
							'year'=>date('Y',time()),
							'moth'=>date('m',time()),
							'day'=>date('d',time()),
							'num'=>5,
							'writable'=>0,
							'status'=>1,
							'isprocess'=>1,
							'modelname'=>ucfirst($this->nodeName),
					);
				}
			}
		}
		$modelNumber->SetRules($aryRule);


	}
	/**
	 * 生成组件详细（编辑页面使用）
	 * @param array $data 数据库数据
	 */
	private function createContrlHtml($data){

		// 获取所有节点
		$configPath = $this->getAutoFormConfig();
		if(file_exists($configPath)){
			//	$allControlConfig = require $configPath;
			$selectlist = require $this->getAutoFormConfig();
			//			$selectlist = $aryRule;
		}
		if(!$selectlist)return '';
		$temp= array();
		foreach ($data as $k=>$v){
			$temp[$v['fieldname']] = $v;
		}
		$html='';
		$data = $this->controlConfig;
		foreach ($selectlist as $k=>$v){
			$v['ids'] = $temp[$k]['id'];
			$hidden =  $this->createHidden($k, $v);
			$title = $v['title'];
			$checkorder = "checkorder=\"{$v['fields']}\"";
			$curTagHtml = htmlspecialchars($data[$v['catalog']]['html']); // 当前组件html结构
			eval("\$curTagHtml = \"$curTagHtml\";");
			$html .= '<li class="nbmmove '.($data[$v['catalog']]['isline']?'line':'noline').' '.($v['isshow']?'':'removed').' '.($v[$this->privateProperty[$v['catalog']]['islock']['name']]?'':'locked').'" data="'.$v['catalog'].'" onclick="setProp(this)" checkorder="'.$v['fields'].'" names="'.$v['fields'].'">'.htmlspecialchars_decode($curTagHtml).'</li>';
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

		$count = $model->where("actionname='{$this->nodeName}'")->count();
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
		$data = array();
		$data['actionname'] = $this->nodeName;
		$data['actiontitle'] = $this->nodeTitle;
		$data['isaudit'] = $this->isaudit;
		$data['inserttable'] = $this->inserttable;

		$model = D('MisDynamicFormManage');

		if(C('TOKEN_NAME')) $data[C('TOKEN_NAME')]= $_POST[C('TOKEN_NAME')];

		/* 自带唯一验证
		 if(false == $model->create($data)){
		 $this->error($model->getError());
		 }
		 $ret = $model->add();
		 */

		$count = $model->where("actionname='{$this->nodeName}' and id<>".$_POST['dynamicformid'])->count();
		if($count){
			$this->error('当即前Action名称已存在 ');
		}
		$data = $model->create($data);
		// 添加表单信息
		$ret = $model->where('id='.$_POST['dynamicformid'])->save($data);
		if(!$ret){
			$this->error($model->getDbError().' '.$model->getError());
		}
		if($_POST['insertnode']){
			$this->addNode($ret);
		}
		//添加表单组件信息
		$filedModel = M('MisDynamicFormField');
		foreach ($filedData['public'] as $key=>$val){
			if($val[$this->publicProperty['ids']['dbfield']]){
				$filedModel->save($val);
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
	 */
	public function createAutoFormConfig($data ='' , $node=''){
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
	}

	/**
	 * @Title: getAutoFormConfig
	 * @Description: 获取组件配置文件信息 ，
	 * @param string $node 配置文件名称，默认为空，获取index_(Action名称).php
	 * @author quqiang
	 * @date 2014-8-20 下午05:34:03
	 * @throws
	 */
	function getAutoFormConfig($node=''){
		$dir = '/autoformconfig/';
		if(!$node){
			$path = $dir.$this->nodeName.'.php';
		}else{
			$path = $dir.$this->nodeName.'_'.$node.'.php';
		}
		$model = D('Autoform');
		$model->setPath($path);
		return $model->GetFile();
	}

	/**
	 * @Title: getParame
	 * @Description: todo(公共函数，获取页面POST参数并按组件封装数组)
	 * @return array|三项 'all':'返回所有数据，包括不显示的、不生成字段的','public':'只含公用属性，用以更新数据','visibility':'所有属性为可见的，用以生成模板'
	 * @author quqiang
	 * @date 2014-8-18 下午4:57:12
	 * @throws
	 */
	function getParame(){
		$temp = array(); // 临时缓存
		$temp1 = array(); // 临时缓存
		$publicArr = array(); // 公共属性，更新到字段信息表中的数据
		$visibilityArr = array(); //可见组件的属性，用以生成页面
		$allArr = array(); // 所有组件属性。
		$fieldsArr = $_POST['fields']; // 得到组件的唯一标识
		foreach ($fieldsArr as $k=>$v){
			/**
			 * 获取公用属性
			 */
			$controlTempArr=array();
			foreach ($this->publicProperty as $kye=>$val){
				//				if($val['identity']){
				//					$temp[$v][$val['name']] = $_POST[$val['name']][$k];
				//				}else{
				//					$temp[$v][$val['name']] = $_POST[$val['name']][$v];
				//				}
				//
				if($val['identity']){
					$t = $_POST[$val['name']][$k];
				}else{
					$t = $_POST[$val['name']][$v];
				}
				$controlTempArr[$v][$val['name']]=$t;
				if($val['dbfield']){
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
			foreach ($this->privateProperty[$controlTempArr[$v][$this->publicProperty['catalog']['name']]] as $key=>$val){
				/* 得到具体属性的值 */
				// 得到所有组件
				if($val['identity']){
					if($val['name']){
						$t[$val['name']] = $_POST[$val['name']][$k];
					}
				}else{
					if($val['name']){
						$t[$val['name']] = $_POST[$val['name']][$v];
					}
				}
			}
			$allArr[$v] = $t;
			// 可显示的组件
			if($t['isshow']){
				$visibilityArr[$v]=$t;
			}
		}
		return array('all'=>$allArr , 'public'=>$publicArr , 'visibility'=>$visibilityArr);

	}

	/**
	 * @Title: getProperty
	 * @Description: todo(获取组件的属性列表)
	 * @param string $category	组件类型
	 * @return array $temp; 组件所有属性
	 * @author quqiang
	 * @date 2014-8-21 下午12:48:01
	 * @throws
	 */
	private function getProperty($category){
		$temp = array();
		$this->publicProperty;
		$this->privateProperty[$category];
		$temp= array_merge($this->privateProperty[$category], $this->publicProperty);
		return 	$temp;
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
		$data["showmenu"]=1;
		$data['icon']='-.png';
		$data['remark']='自动生成:'.$nodename;
		$nodemodel = D("Node");

		$row = $nodemodel->where('name="'.$nodename.'" and level=3')->field('id')->find();
		if($row){
			if($_POST['modeltype']=='update'){
				$data['pid']=$_POST['parentnodename'];
				$nodemodel->where('id='.$row['id'])->save($data);
			}else{
				$this->error ( '节点已存在，请修改' );
				exit;
			}
		}
		if($_POST['modeltype']=='add'){
			if(C('TOKEN_NAME')) $data[C('TOKEN_NAME')]= $_POST[C('TOKEN_NAME')];
			if (false === $nodemodel->create ($data)) {
				$this->error ( $nodemodel->getError () );
			}
			$list=$nodemodel->data($data)->add();
			if ( !$list ){
				$this->error ('插入节点失败!');
			}else{
				//更新现有模板的Nodeid
				$obj = M('MisDynamicFormManage');
				$data['nodeid']=$list;
				$data['actiontitle']=$_POST['nodetitle'];

				$obj->where('id='.$_POST['dynamicformid'])->save($data);
				//插入操作节点
				$cnode[]=array('name'=>'index','title'=>'查看','type'=>4,'pid'=>$list,'group_id'=>$groupId,'showmenu'=>1,'icon'=>'-.png',remark=>'自动生成:查看','level'=>4,'category'=>1);
				$cnode[]=array('name'=>'add','title'=>'新增','type'=>4,'pid'=>$list,'group_id'=>$groupId,'showmenu'=>1,'icon'=>'-.png',remark=>'自动生成:新增','level'=>4,'category'=>2);
				$cnode[]=array('name'=>'edit','title'=>'编辑','type'=>4,'pid'=>$list,'group_id'=>$groupId,'showmenu'=>1,'icon'=>'-.png',remark=>'自动生成:编辑','level'=>4,'category'=>2);
				$cnode[]=array('name'=>'delete','title'=>'删除','type'=>4,'pid'=>$list,'group_id'=>$groupId,'showmenu'=>1,'icon'=>'-.png',remark=>'自动生成:删除','level'=>4,'category'=>2);
				foreach($cnode as $k=>$v){
					$nodemodel->data($v)->add();
				}
			}
		}
	}

	/**
	 * @Title: getNodeList
	 * @Description: todo(获取节点列表)
	 * @author quqiang
	 * @date 2014-8-18 下午4:53:11
	 * @throws
	 */
	private function getNodeList(){
		$nodeModel= D("Node");
		$map["type"]=1;
		$map['level']=2;
		$map["status"]=1;
		$list = $nodeModel->where($map)->select();
		foreach( $list as $k=> $v ){
			$map['type']=2;
			$map['level']=3;
			$map['pid']=$v['id'];
			$list2 = $nodeModel->where($map)->getField("id,title");
			if($list2){
				$list[$k]["children"]=$list2;
			}
		}
		$map2["type"]=3;
		$map2['level']=3;
		$map2["status"]=1;
		$nodemodellist = $nodeModel->where($map2)->select();
		$this->assign("nodemodellist", $nodemodellist);
		$this->assign("nodelist", $list);
	}


	/******************************************  为页面ajax获取数据准备  ****************************************************/

	/**
	 * @Title: getOptions
	 * @Description: 获取 selelist 枚举数据
	 * @author quqiang
	 * @date 2014-8-20 下午05:51:53
	 * @throws
	 */
	function getOptions(){
		$model=D('Selectlist');

		if(file_exists($model->GetFile())){
			$selectlist = require $model->GetFile();
		}
		$temp = array();
		foreach ($selectlist as $k=>$v){
			$temp[$k] =$k.'('.$v['name'].')';
		}
		echo json_encode($temp);
	}
	/**
	 * @Title: getTables
	 * @Description: todo(获取当前所有数据表信息)
	 * @author quqiang
	 * @date 2014-8-18 下午4:54:35
	 * @throws
	 */
	public function getTables(){
		$model3=M();
		$tables = $model3->query("SELECT `TABLE_NAME`,`TABLE_COMMENT` FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '".C('DB_NAME')."'");
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
		echo json_encode($tables2);
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
		if( $t=="" ){
			echo json_encode($arr);
		}else{
			return $arr;
		}
	}
	function lookuoconfig(){
		/* look up */
		$lookupMode = D('LookupObj');
		$lookupPath = $lookupMode->GetFile();
		if(file_exists($lookupPath)){
			$lookupTemp = require($lookupPath);
			unset($temp);
			foreach ($lookupTemp as $k=>$v){
				if($v['status']){
					$temp[$k]=$v['title'];
				}
			}
		}

		/*  look up end */
		echo json_encode($temp);

	}

	function checkforconfig(){
		/* look up */
		$lookupMode = D('CheckForObj');
		$lookupPath = $lookupMode->GetFile();
		if(file_exists($lookupPath)){
			$lookupTemp = require($lookupPath);
			unset($temp);
			foreach ($lookupTemp as $k=>$v){
				if($v['status']){
					$temp[$k]=$v['title'];
				}
			}
		}
		/*  look up end */
		echo json_encode($temp);

	}

}


