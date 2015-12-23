<?php
/**
 * @Title: MisSystemFlowWorkAction 
 * @Package package_name
 * @Description: 项目流程=》节点任务控制器
 * @author 黎明刚 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014年10月20日 上午10:08:55 
 * @version V1.0
 */
class MisSystemFlowWorkAction extends CommonAction {
	public function abc($typelist, $id) {
		foreach ( $typelist as $key => $val ) {
			if ($id == $val ['parentid'] && $val ['outlinelevel'] == 1) {
				unset ( $typelist [$key] );
				$id = $this->abc ( $typelist, $val ['id'] );
			}
		}
		return $id;
	}
	private function getType() {
		// 获取流程类型 组合成树结构
		$MisSystemFlowTypeDao = M ( "mis_system_flow_type" );
		$where ['status'] = 1;
		$typelist = $MisSystemFlowTypeDao->order("sort asc")->where ( $where )->select ();
		
		// 任务节点
		$MisSystemFlowFormDao = M ( "mis_system_flow_form" );
		$where ['outlinelevel'] = 3;
		$formlist = $MisSystemFlowFormDao->order("sort asc")->where ( $where )->select ();
		$supcategory = 0;
		$jsonArr = array ();
		foreach ( $typelist as $key => $val ) {
			// 递归获取最小一层类型
			$supcategory = $this->abc ( $typelist, $val ['id'] );
			
			$typelist [$key] ['default'] = $supcategory;
			$array = array ();
			$array ['id'] = $val ['id'];
			$array ['name'] = missubstr($val['name'],18,true);
			$array ['title'] = $val ['name'];
			$array ['pId'] = $val ['parentid'];
			$array ['open'] = true;
			$array ['url'] = "__URL__/index/jump/jump/supcategory/" . $supcategory."/category/0/pid/0";
			$array ['rel'] = "MisSystemFlowWork_left";
			$array ['type'] = 'post';
			$array ['target'] = 'ajax';
			// 阶段
			if ($val ['outlinelevel'] == 2) {
				$array ['url'] = "__URL__/index/jump/jump/supcategory/" .$val['parentid']."/category/" . $val ['id']."/pid/0";
				$array ['rel'] = "MisSystemFlowWork_left";
				$array ['type'] = 'post';
				$array ['target'] = 'ajax';
				$jsonArr [] = $array;
				foreach ( $formlist as $k => $v ) {
					if ($val ['id'] == $v ['category']) {
						$array = array ();
						$array ['id'] = "999999" . $v ['id'];
						$array ['name'] = missubstr($v['name'],18,true);
						$array ['title'] = $v ['name'];
						$array ['pId'] = $val ['id'];
						$array ['url'] = "__URL__/index/jump/jump/supcategory/" .$val['parentid']."/category/" . $val ['id']."/pid/" . $v ['id'];
						$array ['rel'] = "MisSystemFlowWork_left";
						$array ['type'] = 'post';
						$array ['target'] = 'ajax';
						$jsonArr [] = $array;
					}
				}
			} else {
				$jsonArr [] = $array;
			}
		}
		$this->assign ( 'json_arr', json_encode ( $jsonArr ) );
		return $typelist;
	}
	
	public function index11(){
		$mis_system_flow_formDao = M("mis_system_flow_form");
		// 实例化动态建模数据匹配表
		$mis_dynamic_form_properyModel = M ( "mis_dynamic_form_propery" );
		// 实例化mis_dynamic_form_manage
		$mis_dynamic_form_manageDao = M ( "mis_dynamic_form_manage" );
	
		$mis_auto_bindDao = M("mis_auto_bind");
		$where = array();
		$where['outlinelevel'] = 4;
		$where['formtype'] = 2;
		$list = $mis_system_flow_formDao->where($where)->field("formobj")->select();
		$sql = "";
		$arr = array();
		foreach($list as $key=>$val){
			if(!in_array($val['formobj'], $arr)){
				$name = $val['formobj'];
				$tablename = D($val['formobj'])->getTableName();
				// 验证当前模型是否存在动态建模。
				$where = array ();
				$where ['actionname'] = $val['formobj'];
				$managevo = $mis_dynamic_form_manageDao->where ( $where )->find ();
				if ($managevo) {
					// --------------验证一、是否存在内嵌表格-----------------//
					$properywhere = array ();
					$properywhere ['formid'] = $managevo ['id'];
					$properywhere ['category'] = "datatable";
					$neiqianlist = $mis_dynamic_form_properyModel->where ( $properywhere )->field ( "fieldname,title,datatablemodel,datatablename" )->select ();
					if ($neiqianlist) {
						// 获取内嵌表格的数据信息
						foreach ( $neiqianlist as $nkey => $nval ) {
							// 实例化内嵌表
							$datatablename = D ( $nval ['datatablemodel'] )->getTableName();
								
							$sql.="TRUNCATE {$datatablename};\n";
						}
					}
					//--------------验证二、是否存在组合表单-----------------//
					$where = array();
					$where['bindaname'] = $name;
					$where['typeid'] = 0; //组合表类型
					$where['status'] = 1;
					// 查询符合条件的表单
					$bingdList=$mis_auto_bindDao->where($where)->select();
					foreach($bingdList as $bkey=>$bval){
						//实例化模型对象
						$inbindaModel = D($bval['inbindaname'])->getTableName();
						$sql.="TRUNCATE {$inbindaModel};\n";
	
						//验证当前模型是否存在动态建模。
						$where = array();
						$where['actionname'] = $bval['inbindaname'];
						$managevo = $mis_dynamic_form_manageDao->where($where)->find();
						if($managevo){
							//验证一、是否存在内嵌表格
							$properywhere = array();
							$properywhere['formid'] = $managevo['id'];
							$properywhere['category'] = "datatable";
							$neiqianlist1 = $mis_dynamic_form_properyModel->where($properywhere)->field("fieldname,title,datatablemodel,datatablename")->select();
							if($neiqianlist1){
								//获取内嵌表格的数据信息
								$num = 1;
								foreach($neiqianlist1 as $n1key=>$n1val){
									// 实例化内嵌表
									$datatablename1 = D ( $n1val ['datatablemodel'] )->getTableName();
									$sql.="TRUNCATE {$datatablename1};\n";
								}
							}
						}
					}
					//验证三、是否存在套表
					$where = array();
					$where['bindaname'] = $name;
					$where['status'] = 1;
					$where['typeid'] = 2;//套表类型
					// 查询符合条件的表单
					$bingdsetList=$mis_auto_bindDao->where($where)->select();
					if(count($bingdsetList)>0){
						//存在套表数据，则进行数据获取
						foreach($bingdsetList as $setkey=>$setval){
							//实例化模型对象
							$inbindaModel = D($setval['inbindaname'])->getTableName();
							$sql.="TRUNCATE {$inbindaModel};\n";
								
							//验证当前模型是否存在动态建模。
							$where = array();
							$where['actionname'] = $setval['inbindaname'];
							$managevo = $mis_dynamic_form_manageDao->where($where)->find();
							if($managevo){
								//验证一、是否存在内嵌表格
								$properywhere = array();
								$properywhere['formid'] = $managevo['id'];
								$properywhere['category'] = "datatable";
								$neiqianlist2 = $mis_dynamic_form_properyModel->where($properywhere)->field("fieldname,title,datatablemodel,datatablename")->select();
								if($neiqianlist2){
									//获取内嵌表格的数据信息
									foreach($neiqianlist2 as $n1key=>$n1val){
										//实例化内嵌表
										$datatablename = D($n1val['datatablemodel'])->getTableName();
										$sql.="TRUNCATE {$datatablename};\n";
									}
								}
							}
						}
					}
				}
				$sql.="TRUNCATE  {$tablename};\n";
				array_push($arr, $val['formobj']);
			}
		}
	}
	
	public function index() {
		// 获取流程类型
		$typelist = $this->getType ();
		
		$name = $this->getActionName ();
		
		// 列表过滤器，生成查询Map对象
		$map = $this->_search ( $name );
		
		// 获取点击的类型
		$supcategory = $_REQUEST ['supcategory'] = $_REQUEST ['supcategory']?$_REQUEST ['supcategory']:0;
		if ($supcategory) {
			$map ['supcategory'] = $supcategory;
			$this->assign ( 'supcategory', $supcategory );
		}
		// 获取点击的阶段
		$category = $_REQUEST ['category'] = $_REQUEST ['category']?$_REQUEST ['category']:0;
		if ($category) {
			$map ['category'] = $category;
			$this->assign ( 'category', $category );
		}
		// 获取点击的节点
		$pid = $_REQUEST ['pid'] = $_REQUEST ['pid']?$_REQUEST ['pid']:0;
		if ($pid) {
			$map ['parentid'] = $pid;
			$this->assign ( 'pid', $pid );
		}
		// 树形结构默认选中
		$this->assign ( "ztreeid", $typelist [0] ['id'] );
		// 第一次进入，默认查询第一个类型下面的所有任务
		if (! $supcategory && ! $category && ! $pid) {
			$map ['supcategory'] = $typelist [0] ['default'];
		}
		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$map ['outlinelevel'] = 4; // 查询任务
		if (! empty ( $name )) {
			$this->_list ( $name, $map );
		}
		
		$scdmodel = D ( 'SystemConfigDetail' );
		$detailList = $scdmodel->getDetail ( $name );
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		// 扩展工具栏操作
		$toolbarextension = $scdmodel->getDetail ( $name, true, 'toolbar' );
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		if ($_REQUEST ['jump'] == "jump") {
			$this->display ( 'indexview' );
		} else {
			$this->display ();
		}
	}
	function _before_add() {
		//获取业务类型ID
		$supcategory = $_REQUEST['supcategory'];
		$MisSystemFlowTypeDao = M ( "mis_system_flow_type" );
		if($supcategory == 0){
			//表示为选中业务类型。直接点击的新增按钮
			$where ['status'] = 1;
			$where ['outlinelevel'] = 1;
			$supcategory = $MisSystemFlowTypeDao->order("sort asc")->where ( $where )->getField("id");
			$_GET['supcategory'] = $supcategory;
		}
		//获取当前业务类型的公司
		$companyid = $MisSystemFlowTypeDao->where("id = ".$supcategory)->getField("cmpid");
		$this->assign("companyid",$companyid);
		
		$this->assign ( 'nodename', $nodename );
		$this->lookupcategory ();
	}
	/**
	 * @Title: lookupgetSupcategoryCompany
	 * @Description: todo(ajax请求，变更业务类型，将变更公司的部门)   
	 * @author 黎明刚
	 * @date 2014年12月3日 下午6:22:48 
	 * @throws
	 */
	function lookupgetSupcategoryCompany(){
		//获取业务类型ID
		$category = $_POST['category'];
		//获取当前业务类型的公司
		$MisSystemFlowTypeDao = M ( "mis_system_flow_type" );
		$companyid = $MisSystemFlowTypeDao->where("id = ".$category)->getField("cmpid");
		//获取部门信息
		$mis_system_departmentDao = M("mis_system_department");
		$where['companyid'] = $companyid;
		$where['iscompany'] = array('neq',1);
		$deptlist = $mis_system_departmentDao->where($where)->field("id,name")->select();
		echo json_encode($deptlist);
	}
	
	function _before_update(){
		if($_POST['rules']){
			$_POST['rules'] = str_replace (array ('&quot;','&#39;','&lt;','&gt;'), array ('"', "'",'<','>'),html_entity_decode($_POST['rules']));
		}
		if($_POST['showrules']){
			$_POST['showrules'] = str_replace (array ('&quot;','&#39;','&lt;','&gt;'), array ('"', "'",'<','>'),html_entity_decode($_POST['showrules']));
		}
		if($_REQUEST['totalreport']==null){
			$_POST['totalreport']=0;
		}
		if($_REQUEST['smallreport']==null){
			$_POST['smallreport']=0;
		}
		//获取表单对象，判断是否为审批流
		$formobj = $_REQUEST['formobj'];
		//实例化节点表
		$nodeDao = M("node");
		$where=array();
		$where['name'] = array("eq",$formobj);
		$where['isprocess'] = 0;
		$count = $nodeDao->where($where)->count();
		if($count){
			//非审批流。进行确认提交按钮。
			$autoformObj = D('Autoform');
			$autoformObj->setPath('System/confirmcmit.inc.php');
			$path = $autoformObj->GetFile();
			$aryRule = require  $path;
			if(!is_array($aryRule)){
				$aryRule = array();
			}
			if(!in_array($formobj, $aryRule)){
				array_push($aryRule, $formobj);
			}
			$autoformObj->SetRules($aryRule,'需要确认提交按钮的控制器名称');
		}
		if($_POST['isfile']==null){
			$_POST['isfile']=0;
		}
	}
	
	public function _before_insert(){
		if($_POST['rules']){
			$_POST['rules'] = str_replace (array ('&quot;','&#39;','&lt;','&gt;'), array ('"', "'",'<','>'),html_entity_decode($_POST['rules']));
		}
		if($_POST['showrules']){
			$_POST['showrules'] = str_replace (array ('&quot;','&#39;','&lt;','&gt;'), array ('"', "'",'<','>'),html_entity_decode($_POST['showrules']));
		}
		//获取表单对象，判断是否为审批流
		$formobj = $_REQUEST['formobj'];
		//排除确认提交按钮控制器
		$outAction = array("MisAutoHxr","MisAutoAux","MisAutoMrt","MisAutoTyl");
		if(!in_array($formobj, $outAction)){
			//实例化节点表
			$nodeDao = M("node");
			$where=array();
			$where['name'] = array("eq",$formobj);
			$where['isprocess'] = 0;
			$count = $nodeDao->where($where)->count();
			if($count){
				//非审批流。进行确认提交按钮。
				$autoformObj = D('Autoform');
				$autoformObj->setPath('System/confirmcmit.inc.php');
				$path = $autoformObj->GetFile();
				$aryRule = require  $path;
				if(!is_array($aryRule)){
					$aryRule = array();
				}
				if(!in_array($formobj, $aryRule)){
					array_push($aryRule, $formobj);
				}
				$autoformObj->SetRules($aryRule,'需要确认提交按钮的控制器名称');
			}
		}
	}
	function _after_edit($vo){
		$name=$this->getActionName();
		$model = D ( $name );
		$LinkModel=D('MisSystemFlowLink');
		//获取当前主键
		$id = $_REQUEST [$model->getPk ()];
		$map['id']=$id;
		$voList = $LinkModel->where($map)->find();
		$this->assign('voList',$voList);
		//获取查看角色
		$readtaskrole = array_filter(explode(',',$vo['readtaskrole']));
		$this->assign('readtaskrole',$readtaskrole);
		//获取当前任务的业务类型所属公司
		$supcategory = $vo['supcategory'];
		//表示为选中业务类型。直接点击的新增按钮
		$MisSystemFlowTypeDao = M ( "mis_system_flow_type" );
		//获取当前业务类型的公司
		$companyid = $MisSystemFlowTypeDao->where("id = ".$supcategory)->getField("cmpid");
		$this->assign("companyid",$companyid);
		
		//查询动态阶段信息
		if($vo['dycon']){
			//查询阶段名称
			$where['parentid'] = $vo['supcategory'];
			$where['orderno'] = array(' in ',$vo['dycon']);
			$where['outlinelevel'] = 2;
			$typename = $MisSystemFlowTypeDao->where($where)->getField("id,name");
			$typename = implode(",", $typename);
			$this->assign('typename',$typename);
		}
		// 内嵌表处理datatable2
		$innerTabelObjdatatable2 = M('mis_system_flow_form_sub_datatable2');
		$innerTabelObjdatatable2Data = $innerTabelObjdatatable2->where('masid='.$vo['id'])->select();
		$this->assign("innerTabelObjdatatable2Data",$innerTabelObjdatatable2Data);
		
	}
	
	/**
	 * @Title: lookuprolegroup
	 * @Description: todo(获取角色，部门，职级内容方法)
	 * @author liminggang
	 * @date 2013-11-26 上午9:34:13
	 * @throws
	 */
	public function lookuprolegroup(){
		//查询获取角色
		$obj=$_REQUEST['obj'];
		$stepType=$_REQUEST['stepType'];
		$this->assign('obj',$obj);
		$objname=$_REQUEST['objname'];
		$this->assign('objname',$objname);
		$this->assign('stepType',$stepType);
		$map = array();
		$searchby = $_POST["searchby"];
		$keyword= $_POST["keyword"];
		if($keyword){
			$map[$searchby] = array('like','%'.$keyword.'%');
			$this->assign('keyword',$keyword);
			$this->assign('searchby',$searchby);
		}
		$searchby=array(
				array("id" =>"name","val"=>"按角色名称"),
		);
		$this->assign("searchbylist",$searchby);
	
		$this->_list("rolegroup", $map);
		$this->display();
	}
	
	function _before_edit() {
		
		$name = $this->getActionName ();
		$MisSystemFlowWorkModel = D ( $name );
		// 获取当前主键
		$id = $_REQUEST [$MisSystemFlowWorkModel->getPk ()];
		$map = array ();
		$map ['pinfoid'] = $id;
		$map ['status'] = 1;
		$map ['tablename'] = "mis_system_flow_form";
		// 批次信息表
		$MisSystemUserBactchModel = D ( "MisSystemUserBactch" );
		$MisSystemUserObjModel = D ( "MisSystemUserObj" );
		//查询类型名称
		$userobjlist = $MisSystemUserObjModel->where("status = 1")->select();
		$pinfoid = 0;
		// 获取项目执行人和审核人信息
		$ProcessRelationModel = D ( 'ProcessRelation' );
		$info = $ProcessRelationModel->where ( $map )->select ();
		$list = array ();
		foreach ( $info as $key => $val ) {
			// 根据流程节点。查询批次信息
			$map = array ();
			$map ['tablename'] = 'process_relation';
			$map ['tableid'] = $val ['id'];
			$bactchlist = $MisSystemUserBactchModel->where ( $map )->select ();
			$rulesinfo = $ruleStr = $rulenameStr = $userobjidStr = $userobjidStrname = $userobjStr = $userobjStrname = $bactchStr = array ();
			foreach ( $bactchlist as $k => $v ) {
				// 用户对象ID
				array_push ( $userobjidStr, $v ['userobjid'] );
				// 对应表的name
				foreach($userobjlist as $k1=>$v1){
					if($v['userobjid'] == $v1['id']){
						array_push($userobjidStrname,$v1['name']);
					}
				}
				// 用户对象存储字段
				array_push ( $userobjStr, $v ['userobj'] );
				// 用户对象中文存储
				array_push ( $userobjStrname, $v ['userobjname'] );
				// 批次条件
				array_push ( $ruleStr, $v ['rule'] );
				// 批次条件中文展示
				array_push ( $rulenameStr, $v ['rulename'] );
				// 批次顺序
				array_push ( $bactchStr, $v ['sort'] );
				// 批次序列化
				array_push ( $rulesinfo, $v ['rulesinfo'] );
			}
			$userobjid = implode ( ";", $userobjidStr );
			$userobjidname = implode ( ";", $userobjidStrname );
			$userobj = implode ( ";", $userobjStr );
			$userobjname = implode ( ";", $userobjStrname );
			$rule = implode ( ";", $ruleStr );
			$rulename = implode ( ";", $rulenameStr );
			$bactch = implode ( ";", $bactchStr );
			$rulesinfo = implode ( ";", $rulesinfo );
			// 默认显示
			$str = "";
			$str .= '	<input type="hidden" name="tparallel[]" value="' . $val ['parallel'] . '"/>';
			$str .= '	<input type="hidden" name="tname[]" value="' . $val ['name'] . '"/>';
			$str .= '	<input type="hidden" name="userobjidStr[]" value="' . $userobjid . '"/>';
			$str .= '	<input type="hidden" name="userobjidStrname[]" value="' . $userobjidname . '"/>';
			$str .= '	<input type="hidden" name="userobjStr[]" value="' . $userobj . '"/>';
			$str .= '	<input type="hidden" name="userobjStrname[]" value="' . $userobjname . '"/>';
			$str .= '	<input type="hidden" name="ruleStr[]" value="' . $rule . '"/>';
			$str .= '	<input type="hidden" name="rulename[]" value="' . $rulename . '"/>';
			$str .= '	<input type="hidden" name="bactchStr[]" value="' . $bactch . '"/>';
			$str .= '	<input type="hidden" name="rulesinfoStr[]" value="' . $rulesinfo . '"/>';
			
			//赋值类容
			$list[$val['category']]['str'] = $str;
			$list[$val['category']]['name'] = $val ['name'];
		}
		$this->assign ( "list", $list );
		
		$this->lookupcategory ();
	}
	public function lookupcategory() {
		// 获取类型联动
		$categoryComList = D ( 'MisSystemFlowType' )->getDeptCombox ();
		$this->assign ( "categoryComList", $categoryComList );
	}
	function lookupGeneralA() {
		$map = $this->_search ( "MisSystemFlowForm" );
		$type=$_REQUEST['type'];
		$conditions = $_POST ['conditions']; // 检索条件
		if ($conditions) {
			$this->assign ( "conditions", $conditions );
			$cArr = explode ( ';', $conditions ); // 分号分隔多个参数
			foreach ( $cArr as $k => $v ) {
				$wArr = explode ( ',', $v ); // 逗号分隔字段、参数、修饰符
				if ($wArr [0] == "_string") { // 判断是否传的为字符串条件
					$map ['_string'] = $wArr [1];
				} else {
					if ($wArr [2]) { // 存在修饰符的以修饰符形式进行检索
						$map [$wArr [0]] = array (
								$wArr [2],
								$wArr [1] 
						);
					} else { // 普通检索
						$map [$wArr [0]] = $wArr [1];
					}
				}
			}
		}
		if($type == 4){
			//查询类型为单据
			$map ['formtype'] = 2;
			//单据数类型
			$map['datatype'] = 0;
		}
		$map ['status'] = 1;
		$this->assign ( "layoutH", 110 ); // 设置高度
		$scdmodel = D ( 'SystemConfigDetail' );
		$detailList = $scdmodel->getDetail ( "MisSystemFlowForm" );
		$this->assign ( 'detailList', $detailList );
		$this->assign('type',$type);
		$this->_list ( "MisSystemFlowForm", $map );
		
		$this->display ();
	}
	public function lookupAddProcessRelation() {
		// 获取节点的模型名称
		$this->assign ( "nodename", $_REQUEST ['modelname'] );
		// 选人类型表
		$MisSystemUserObjModel = D ( "MisSystemUserObj" );
		$userObj = $MisSystemUserObjModel->where ( " status = 1" )->select ();
		$this->assign ( "userObj", $userObj );
		$this->assign ( "ptype", $_REQUEST ['ptype'] );
		$this->display ();
	}

/**
	 * @Title: _after_insert
	 * @Description: todo(后置insert函数)  
	 * @author 管理员
	 * @date 2015-07-13 22:06:02
	 * @throws
	*/
	function _after_insert($id){
		//新增当前节点的前置节点
		$linkid = $_REQUEST['id'];
		$linkInfo= $_REQUEST['predecessorid'];
		$MisSystemFlowLinkModel = D("MisSystemFlowLink");
		$MisSystemFlowLinkModel->setLinkWork($linkid,$linkInfo,1);
		$MisSystemFlowWorkModel = D ( 'MisSystemFlowWork' );
		if($_REQUEST['predecessorid']){
			//存在前置任务，则对数据字段进行修改
			$MisSystemFlowWorkModel->where("id = ".$_REQUEST['id'])->setField("issubtask",1);
		}else{
			//不存在存在前置任务，则对数据字段进行修改
			$MisSystemFlowWorkModel->where("id = ".$_REQUEST['id'])->setField("issubtask",0);			
		}
		$MisSystemFlowWorkModel->pJAccesslist2($_POST['formobj'],$id);
		$this->lookupinserttinfo ( $id );
		// 内嵌表数据添加处理
				
		$datatablefiexname ="mis_system_flow_form_sub_";
		$insertData = array();// 数据添加缓存集合
		if($_POST['datatable']){
			foreach($_POST['datatable'] as $key=>$val){
				foreach($val as $k=>$v){
					$insertData[$k][]=$v;
				}
			}
		}
		//数据处理
		if($insertData){
			foreach($insertData as $k=>$v){
				$nqname = createRealModelName($datatablefiexname.$k);
				$model = D($nqname);
				$uploadfile = array();
				foreach($v as $key=>$val){
					if(C('TOKEN_NAME'))
						$val[C('TOKEN_NAME')]= $_POST[C('TOKEN_NAME')];
					$val['masid'] =$id ;
					$val = $model->create($val);
					$insertId = $model->add($val);
				}
				$a=$model->getLastInsID();
				$_REQUEST[$datatablefiexname.$k] = $a;
			}
		}
	}

	public function _after_insert1212($list) {
		//新增当前节点的前置节点
		$linkid = $_REQUEST['id'];
		$linkInfo= $_REQUEST['predecessorid'];
		$MisSystemFlowLinkModel = D("MisSystemFlowLink");
		$MisSystemFlowLinkModel->setLinkWork($linkid,$linkInfo,1);
		$MisSystemFlowWorkModel = D ( 'MisSystemFlowWork' );
		if($_REQUEST['predecessorid']){
			//存在前置任务，则对数据字段进行修改
			$MisSystemFlowWorkModel->where("id = ".$_REQUEST['id'])->setField("issubtask",1);
		}else{
			//不存在存在前置任务，则对数据字段进行修改
			$MisSystemFlowWorkModel->where("id = ".$_REQUEST['id'])->setField("issubtask",0);			
		}
		$MisSystemFlowWorkModel->pJAccesslist2($_POST['formobj'],$list);
		$this->lookupinserttinfo ( $list );
	}
	public function _after_update() {
		//修改当前节点的前置节点
		$linkid = $_REQUEST['id'];
		$linkInfo= $_REQUEST['predecessorid'];
		$MisSystemFlowLinkModel = D("MisSystemFlowLink");
		$MisSystemFlowLinkModel->setLinkWork($linkid,$linkInfo,1);
		$MisSystemFlowWorkModel = D ( 'MisSystemFlowWork' );
		if($_REQUEST['predecessorid']){
			//存在前置任务，则对数据字段进行修改
			$MisSystemFlowWorkModel->where("id = ".$_REQUEST['id'])->setField("issubtask",1);
		}else{
			//不存在存在前置任务，则对数据字段进行修改
			$MisSystemFlowWorkModel->where("id = ".$_REQUEST['id'])->setField("issubtask",0);			
		}
		$MisSystemFlowWorkModel->pJAccesslist2($_POST['formobj'],$linkid);
		$this->lookupinserttinfo ( $_POST ['id'] );

		// 内嵌表数据添加处理		// 内嵌表数据添加处理
				
		$datatablefiexname ="mis_system_flow_form_sub_";
		$insertData = array();// 数据添加缓存集合
		$updateData = array();// 数据修改缓存集合
		if($_POST['datatable']){
			foreach($_POST['datatable'] as $key=>$val){
				foreach($val as $k=>$v){
					if($v['id'] || $_REQUEST[$datatablefiexname.$k]){
						$updateData[$k][]=$v;
					}else{
						$insertData[$k][]=$v;
					}
				}
			}
		}
		//数据处理
		if($insertData){
			foreach($insertData as $k=>$v){
				$nqname = createRealModelName($datatablefiexname.$k);
				$model = D($nqname);
				$uploadfile = array();
				foreach($v as $key=>$val){
					if(C('TOKEN_NAME'))
						$val[C('TOKEN_NAME')]= $_POST[C('TOKEN_NAME')];
					$val['masid'] =$_POST["id"] ;
					$val = $model->create($val);
					$insertId = $model->add($val);
					/*
					 * _over_insert 方法，为静默插入生单。
					 */
					$this->_over_insert($nqname, $insertId);
					//处理内嵌表带附件信息数据
					foreach($val as $kk => $vv){
						if(is_array($vv)){
							$uploadfile[$kk.$key.$k]["file"] = $vv;
							$uploadfile[$kk.$key.$k]["tableid"] = $_POST["id"];
							$uploadfile[$kk.$key.$k]["subid"] = $insertId;
							$uploadfile[$kk.$key.$k]["tablename"] = createRealModelName($datatablefiexname.$k);
							$uploadfile[$kk.$key.$k]["fieldname"] = $kk;
						}
					}
				}
				if($uploadfile){
					$this->DT_swf_upload($uploadfile);
				}
			}
		}
		if($updateData){
			foreach($updateData as $k=>$v){
				$nqname = createRealModelName($datatablefiexname.$k);
				$model = D($nqname);
				$uploadfile = array();
				foreach($v as $key=>$val){
					if(C('TOKEN_NAME'))
						$val[C('TOKEN_NAME')]= $_POST[C('TOKEN_NAME')];
					$val = $model->create($val);
					$model->save($val);
				}
			}
		}


	}
	/**
	 * @Title: lookupinserttinfo
	 * @Description: 分派人和执行人的插入方法。全部以条件形式 
	 * @param unknown $id  
	 * @author 黎明刚 
	 * @date 2014年11月11日 上午10:34:34 
	 * @throws
	 */
	private function lookupinserttinfo($id) {
		$ProcessRelationModel = D ( 'ProcessRelation' );
		$MisSystemFlowWorkModel = D ( 'MisSystemFlowWork' );
		$workList = $MisSystemFlowWorkModel->where ( array ('id' => $id ) )->find ();
		// 批次信息表
		$MisSystemUserBactchModel = D ( "MisSystemUserBactch" );
		
		// 先删除原有的流程批次信息，在进行新增
		$map1 = array ();
		$map1 ['pinfoid'] = $id;
		$map1 ['tablename'] = "mis_system_flow_form";
		$relaids = $ProcessRelationModel->where ( $map1 )->getField ( "id,pinfoid" );
		if ($relaids) {
			$map = array ();
			$map ['id'] = array (' in ',array_keys ( $relaids ) );
			$resultrela = $ProcessRelationModel->where ( $map )->delete ();
			$map = array ();
			$map ['tablename'] = "process_relation";
			$map ['tableid'] = array (' in ',array_keys ( $relaids ) );
			$resultbactch = $MisSystemUserBactchModel->where ( $map )->delete ();
			if (! $resultrela || ! $resultbactch) {
				$this->error ( "数据绑定失败，请联系管理员" );
			}
		}
		$fpr = $_POST ['fpr']; // 标记，1为执行人，2为分派人
		$tname = $_POST ['tname']; // 流程节点名称
		$tparallel = $_POST ['tparallel']; // 是否并行
		
		foreach ( $fpr as $key => $val ) {
			if($tname [$key]){
				$data = array ();
				$data ['pinfoid'] = $id;
				$data ['tablename'] = "mis_system_flow_form";
				$data ['sort'] = $key;
				$data ['category'] = $val; //分派人和执行人标记
				$data ['name'] = $tname [$key];
				$data ['parallel'] = $tparallel [$key];
				$data ['createtime'] = time ();
				$data ['createid'] = $_SESSION [C ( 'USER_AUTH_KEY' )];
				$result = $ProcessRelationModel->add ( $data );
				if (! $result) {
					$this->error ( "数据提交错误！" );
				}
				// 获取当前流程节点的批次，规则
				$userobjidStr = explode ( ";", $_POST ['userobjidStr'] [$key] );
				$userobjStr = explode ( ";", $_POST ['userobjStr'] [$key] );
				$userobjname = explode ( ";", $_POST ['userobjStrname'] [$key] );
				$bactchStr = explode ( ";", $_POST ['bactchStr'] [$key] );
				
				// 替换html标签字符
				$fields = str_replace ( "&#39;", "'", html_entity_decode ( $_POST ['ruleStr'] [$key] ) );
				$ruleStr = explode ( ";", $fields );
				$rulenameStr = explode ( ";", str_replace ( "&#39;", "'", html_entity_decode ( $_POST ['rulename'] [$key] ) ) );
				$rulesinfoStr = explode ( ";", $_POST ['rulesinfoStr'] [$key] );
				
				$dataList = array ();
				foreach ( $userobjidStr as $bactchkey => $bactchval ) {
					$dataList [] = array (
							'tablename' => "process_relation",
							'tableid' => $result,
							'userobjid' => $bactchval,
							'userobj' => $userobjStr [$bactchkey],
							'userobjname' => $userobjname [$bactchkey],
							'rule' => $ruleStr [$bactchkey],
							'rulename' => $rulenameStr [$bactchkey],
							'rulesinfo' => $rulesinfoStr [$bactchkey],
							'sort' => $bactchStr [$bactchkey],
							'createtime' => time (),
							'createid' => $_SESSION [C ( 'USER_AUTH_KEY' )] 
					);
				}
				$bactchResult = $MisSystemUserBactchModel->addAll ( $dataList );
				if (! $bactchResult) {
					$this->error ( "流程节点批次报错失败,请联系管理员" );
				}
			}
		}
	}
	
	function _before_view(){
		$name=$this->getActionName();
		$model = D ( $name );
		$LinkModel=D('MisSystemFlowLink');
		//获取当前主键
		$id = $_REQUEST [$model->getPk ()];
		$map['id']=$id;
		$voList = $LinkModel->where($map)->find();
		$this->assign('voList',$voList);
	}
	/**
	 * @Title: lookupCopy
	 * @Description: todo(任务复制，将任务复制到不同的业务类型下面去)   
	 * @author 黎明刚
	 * @date 2015年1月12日 下午2:24:40 
	 * @throws
	 */
	public function lookupCopy(){
		//获取任务ID
		$workid = $_REQUEST['id'];
		if($_REQUEST['step']){
			$name=$this->getActionName();
			$model = D ( $name );
			
			//接收业务类型
			$supcategory = $_REQUEST['supcategory'];
			//接收业务阶段
			$category = $_REQUEST['category'];
			//接收业务节点
			$parentid = $_REQUEST['parentid'];
			
			if(!$supcategory || !$category || !$parentid){
				$this->error("请选择类型数据");
			}
			
			$vo = $model->where("id = ".$workid)->find();
			if($vo['supcategory'] == $supcategory && $vo['category'] == $category && $vo['parentid'] == $parentid){
				$this->error("当前任务未业务类型、阶段和节点未发生变化。");
			}else{
				$vo['supcategory'] = $supcategory;
				$vo['category'] = $category;
				$vo['parentid'] = $parentid;
				unset($vo['id']);
				unset($vo['quote']);
				$result = $model->add($vo);
				if($result == false ){
					$this->error("任务复制失败，请联系管理员。");
				}else{
					$this->success("任务复制成功！");
				}
			}
		}else{
			$this->assign("id",$workid);
			$this->lookupcategory ();
			$this->display();
		}
	}
	public function lookupDyCondition(){
		$this->assign("dycon",explode(",", $_POST['dycon']));
		//新增或修改未保存时，数据显示
		$data['rules'] = str_replace (array ('&quot;','&#39;','&lt;','&gt;'), array ('"', "'",'<','>'),html_entity_decode($_POST['rules']));
		$data['showrules'] = str_replace (array ('&quot;','&#39;','&lt;','&gt;'), array ('"', "'",'<','>'),html_entity_decode($_POST['showrules']));
		$data['rulesinfo'] = $_POST['rulesinfo'];
		// 获取节点的模型名称
		$this->assign ( "pInfoVo", $data );
		$this->assign ( "nodename", $_REQUEST ['modelname'] );
		$this->assign ( "type", $_REQUEST ['type'] );
		$this->display();
	}
	public function lookupInsert(){
		if($_POST['dycon']){
			$rules = $_POST['rules']?$_POST['rules']:'';
			$showrules = $_POST['showrules'] ? $_POST['showrules']:'';
			$rulesinfo = $_POST['rulesinfo'] ? $_POST['rulesinfo']:'';
			$dycon = implode(",", $_POST['dycon']);
			$type = $_POST['type'];
			//查询阶段名称
			$mis_system_flow_type = M("mis_system_flow_type");
			$where['parentid'] = $type;
			$where['orderno'] = array(' in ',$dycon);
			$where['outlinelevel'] = 2;
			$typename = $mis_system_flow_type->where($where)->getField("id,name");
			$typename = implode(",", $typename);
			$date=array(
					'showname'=>$typename,
					'dycon'=>$dycon,
					'rules'=>$rules,
					'showrules'=>$showrules,
					'rulesinfo'=>$rulesinfo,
			);
			$this->success("添加成功",'',json_encode($date));
		}else{
			$this->error("请添加条件");
		}
	}
	/**
	 * @Title: pJAccesslist
	 * @Description: todo(任务节点权限初始化)   
	 * @author 谢友志 
	 * @date 2015-4-7 下午2:54:02 
	 * @throws
	 */
	public function pJAccesslist(){
		// 删除原有的文件夹
		if (is_dir ( Dynamicconf . "/PJAccessList" )) {
			import ( '@.ORG.xhprof.FileUtil' );
			$fileUtil = new FileUtil ();
			$fileUtil->unlinkDir ( Dynamicconf . "/PJAccessList" );
		}
		//查找带模板的任务节点
		$map['formobj'] = array('neq','');
		$tasklist = M("mis_system_flow_form")->where($map)->select();
		//任务节点权限文件夹
		$pathconf = DConfig_PATH . '/PJAccessList';
		if (! file_exists ( $pathconf )) {
			createFolder ( $pathconf );
		}
		foreach($tasklist as $k=>$v){
			$Action = $val['formobj'];
			if((int)$Action>0) continue;
			$new[$v['formobj']] = $v;
		}

		//调用lookupobj模型的写入文件方法
		$lookupmodel = D("LookupObj");
		foreach($new as $key=>$val){
			$Action = $val['formobj'];
			if((int)$Action>0) continue;
			$nodedetails = M("node")->where("name='".$Action."'")->find();
			if(! $nodedetails){
				logs("模板".$Action."没有对应模板节点","taskNodeRole");
			}else{
				//$nodeid = getFieldBy($action,"name","id","node");
				//查找该模板的操作节点
				$nodemodel = M("node");
				$list = $nodemodel->where("pid=".$nodedetails['id'])->select();
				/**
				 索引文件以“paaccess_”+任务节点id为key值 已对应模板名称为value值组成数组
				*/
				if($list){
					//组合一个索引文件的元素
					$name = "pjaccess_".$Action;
					$title = $nodedetails['title'];
					$arr[$name] = $title;//array($name=>$title);
					//对操作节点数据进行重组$detailes
					$temp = array('GROUPID'=>$nodedetails['group_id']);
					foreach($list as $k=>$v){
						$optionname = strtoupper($v['name']);
						if($optionname=="CHANGEEDIT" || $optionname=="EDIT"){
							$temp1 = array($optionname=>$v['id']."-4");
						}else{
							$temp1 = array($optionname=>$v['id']."-1");
						}						
						$temp = array_merge($temp,$temp1);
					}
					$detailes = array($Action=>$temp);
					//套表 组合表情况下 添加权限详情元素
					$model = D($this->getActionName());					
					$isbinddetailes = $model->pjAccessisbind($Action);;
					$detailes = array_merge($detailes,$isbinddetailes);
					//暂时生成文件 其操作节点权限全部为1
					//任务节点详情文件 每个任务节点生成单独的文件
					$detailesfile =  $pathconf.'/'.$name.'.php';//$roledetailsdir.'/'.$name.'.inc.php';
					$detailesnum = $lookupmodel->SetSongleRule($detailesfile,$detailes);
				}else{
					logs("节点".$Action."没有对应下级操作","taskNodeRole");
				
				}
			}			
		}
		echo "初始化完成！";
// 		//任务节点进入索引文件
// 		$indexnum = $lookupmodel->SetSongleRule($roleindexdir.'/indexlist.inc.php',$arr);

		
	}
}
?>