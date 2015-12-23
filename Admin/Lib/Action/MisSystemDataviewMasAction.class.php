<?php
/**
 * @Title: MisSystemDataviewMasAction
 * @Package 底层配置-系统视图
 * @Description: TODO()
 * @author renling
 * @company 重庆特米洛科技有限公司
 * @copyright 重庆特米洛科技有限公司
 * @date 2014-11-20 16:18:54
 * @version V1.0
 */
class MisSystemDataviewMasAction extends CommonAction {
	private $curSQLFiled='';
	public function _filter(&$map) {
		if ($_SESSION ["a"] != 1) {
			$map ['status'] = array (
					"gt",
					- 1 
			);
		}
	}
	/**
	 * @Title: edit
	 * @Description: todo(重写父类编辑函数)
	 * 
	 * @author 管理员
	 *         @date 2014-11-20 16:39:53
	 * @throws
	 *
	 */
	function edit() {
		//树列表id
		$viewname = getFieldBy($_REQUEST['id'],"id","name","mis_system_dataview_mas");
		$treemap['masid'] = getFieldBy($viewname,"viewname","id","mis_system_lookupobj");
		$treelistconfigModel = M("mis_system_lookupobj_sub_treelistconfig");
		$treeListConfigData = $treelistconfigModel->where($treemap)->find();
		$this->assign("treeListConfigData",$treeListConfigData);
		
		
		$mainTab = 'mis_system_dataview_mas';
		// 获取当前控制器名称
		$name = $this->getActionName ();
		$model = D ( $name );
		// 获取当前主键
		$map [$mainTab . '.id'] = $_REQUEST ['id'];
		$vo = $model->where ( $map )->find ();
		if (empty ( $vo )) {
			$this->display ( "Public:404" );
			exit ();
		}
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		// 读取动态配制
		$this->getSystemConfigDetail ( $name );
		// 扩展工具栏操作
		$scdmodel = D ( 'SystemConfigDetail' );
		// 上一条数据ID
		$map ['id'] = array (
				"lt",
				$id 
		);
		$updataid = $model->where ( $map )->order ( 'id desc' )->getField ( 'id' );
		$this->assign ( "updataid", $updataid );
		// 下一条数据ID
		$map ['id'] = array (
				"gt",
				$id 
		);
		$downdataid = $model->where ( $map )->getField ( 'id' );
		$this->assign ( "downdataid", $downdataid );
		// lookup带参数查询
		$module = A ( $name );
		if (method_exists ( $module, "_after_edit" )) {
			call_user_func ( array (
					&$module,
					"_after_edit" 
			), &$vo );
		}
		$this->assign ( 'vo', $vo );
		$this->display ();
	}
	/**
	 * @Title: _before_edit
	 * @Description: todo(前置编辑函数)
	 * 
	 * @author 管理员
	 *         @date 2014-11-20 16:39:53
	 * @throws
	 *
	 */
	function _before_edit() {
		$mainTab = 'mis_system_dataview_mas';
		// 获取当前控制器名称
		$name = $this->getActionName ();
		$model = D ( $name );
		// 获取当前主键
		$map [mis_system_dataview_mas . '.id'] = $_REQUEST ['id'];
		$vo = $model->where ( $map )->find ();
		$this->assign ( 'vo', $vo );
	}
	/**
	 * @Title: _after_edit
	 * @Description: todo(后置编辑函数)
	 * 
	 * @author 管理员
	 *         @date 2014-11-20 16:39:53
	 * @throws
	 *
	 */
	function _after_edit($vo) {		
		// 内嵌表处理datatable2
		$innerTabelObjdatatable2 = M ( 'mis_system_dataview_sub' );
		$innerTabelObjdatatable2Data = $innerTabelObjdatatable2->where ( 'status=1 and masid=' . $vo ['id'] )->select ();
		$this->assign ( "innerTabelObjdatatable2Data", $innerTabelObjdatatable2Data );
	}
	function _before_insert(){
		$name = substr($_POST['name'],-4);
		if(strtolower($name)=='view'){
			$_POST['name'] = substr($_POST['name'],0,-4).'View';
		}else{
			$_POST['name'] = $_POST['name'].'View';
		}
		$_POST['spellsql']=str_replace("&#39;", "'", html_entity_decode($_POST['spellsql']));
	}
	/**
	 * @Title: _after_insert
	 * @Description: todo(后置insert函数)
	 * 
	 * @author 管理员
	 *         @date 2014-11-20 16:39:53
	 * @throws
	 *'1' => array(
			'title' => '视图lookup',
			'fields' => 'orderno,deptid,dutylevelid,sumpeople,deptpeople',
			'url' => 'lookupGeneral',
			'mode' => 'MisHrPersonnelApplicationInfo',
			'filed' => 'sumpeople',
			'val' => 'sumpeople',
			'condition' => '',
			'status' => '1',
			'level' => '15',
			'viewname' =>'viewsearch',
			'viewtype'=>'1', // 视图类型 1:视图，0:非视图
	),
	 */
	function _after_insert($id) {
		// 内嵌表数据添加处理
		$datatablefiexname = "mis_system_dataview_sub";
		$insertData = array (); // 数据添加缓存集合
		$selectfield=array(); //替换查询语句
		$backList=array(); //带回字段数组
		$showfield="";
		$dataval="";
		if ($_POST ['datatable']) {
			foreach ( $_POST ['datatable'] as $key => $val ) {
				foreach ( $val as $k => $v ) {
					 if($v['otherfield']==$_POST['isshow']){
					 	$v['isshow']=$_POST['isshow'];
					 	$showfield=$_POST['isshow'];
					 }else{
					 	$v['isshow']=0;
					 }
					 if($v['otherfield']==$_POST['dataval']){
					 	$v['dataval']=$_POST['dataval'];
					 	$dataval=$_POST['dataval'];
					 }else{
					 	$v['dataval']=0;
					 }
					if($v['isback']==1){
						$backList[]=$v['otherfield'];
						$selectfield[]=$v['field'];
					}
					//存入关联函数
					$func=$v['funname'];
					if($func){
						if(!function_exists($func)) $this->error("关联操作函数".$func."不存在！");
						$v['funname']=$func;
					}
					$funcdata=$v['funfield'];
					if($funcdata){
						$dchar=explode(";",$funcdata);
						if(count($dchar)>10) $this->error("关联操作函数参数最多只能传递10个！");
						$v['funcdata']=$dchar;
					}
					if($v['datasort']){
						$v['datasort'] = $v['datasort'];
						$v['sorttype'] = $v['sorttype'];
					}else{
						$v['datasort'] = '';
						$v['sorttype'] = '';
					}
					$insertData [$k] [] = $v;
				}
			}
		}
		$NodeModel=D("Node");
		if($_POST['isdefault']==1){
			//修改节点默认视图
			$NodeResult=$NodeModel->where("name=".$_POST['modelname'])->setField("viewname",$_POST['name']);
		}else{
			//判断节点默认视图是否是当前视图
			if(getFieldBy($_POST['modelname'], "name", "viewname", "node")==$_POST['name']){
				$NodeResult=$NodeModel->where("name=".$_POST['modelname'])->setField("viewname"," ");
			}
		}
		$selectfieldStr=implode(',', $selectfield);
		$spellsql=$_POST['spellsql'];
		
		
		
		$spellnewsql=preg_replace('/\s/',' ',$spellsql);
		$replacesql = preg_replace('/(SELECT) (.*) (FROM)/is', "\\1  {$selectfieldStr}  \\3",$spellnewsql);
		$replacenewsql = str_replace(array('&quot;','&#39;','&lt;','&gt;'), array('"', "'",'<','>'), $replacesql);
		
		//读取类
		import ( '@.ORG.SqlParse' );
		$viewlist=SqlParse::checkSQLFields($replacenewsql);
	 	$oldcondition=$viewlist['condition']['condition'];
	 	$grouplist=$viewlist['condition']['group'][0];
	 	//转换格式
	 	$condition=str_replace("&#39;", "'", html_entity_decode($oldcondition));
	 	$groupcondition=str_replace("&#39;", "'", html_entity_decode($grouplist));
	 	$spellSqlList = preg_split('/(\bwhere\b)/i', $spellsql, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
	 	$spellwheresql=$spellSqlList[0]."    %WHERE% %ORDER% %GROUP% %LIMIT%";
	 	//加入条件
	 	$MisSystemDataviewMasModel=M("mis_system_dataview_mas");
	 	$date['groupcondition']=$groupcondition;
	 	$date['condition']=$condition;
	 	$date['spellwheresql']=str_replace("&#39;", "'", html_entity_decode($spellwheresql));;
	 	$MisSystemDataviewMasModel->where("id=".$id)->data($date)->save();
		unset($viewlist['condition']);
		//生成视图文件
		$this->appendView($viewlist);
		$this->lookupsetLookup($backList,$showfield,'',$dataval,$condition);
		
		
		//插入转义后的sql语句
		$misSystemDataviewMasModel=D('MisSystemDataviewMas');
		$misSystemDataviewMasModel->where("id=".$id)->setField ( 'replacesql', $replacenewsql);
		// 数据处理
		if ($insertData) {
			foreach ( $insertData as $k => $v ) {
				$model = M ( $datatablefiexname );
				foreach ( $v as $key => $val ) {
					$val ['masid'] = $id;
					$result=$model->add ( $val );
				}
			}
		}
		//echo $model->getLastSql();exit;
// 		if($result===false){
// 			$this->error("操作失敗！！");			
// 		}else{
// 			$this->success("操作成功！！");			
// 		}
		
	}
	/**
	 *
	 * checkSQLCondition
	 * @Title: checkSQLCondition
	 * @desc: todo(获取sql的条件列表)
	 * @param unknown_type $str
	 * @return return_type
	 * @throws
	 */
	function checkSQLCondition($str) {
		$t = preg_split('/(\bwhere\b|\bgroup\b|\border\b|\blimit\b)/i', $str, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		$t = preg_split('/(\band\b|\bor\b)/i', $t[2], -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		$r="";
		for ($i = 0; $i < count($t); $i++) {
			$v = $t[$i];
			if (in_array($v, array('and', 'or'))) {
				$v .= $t[++$i];
				if (strstr($v, '('))
					while (!strstr($v, ')'))
						$v .= $t[++$i];
			}
			$r.=$v;
	
		}
		return $r;
	}
	/**
	 * 
	 * @Title: lookupsetLookup
	 * @Description: todo(这里用一句话描述这个方法的作用) 
	 * @param unknown $data 写入数据
	 * @param unknown $oldViewName  源视图名称
	 * @author renling 
	 * @date 2014年11月29日 下午5:22:33 
	 * @throws
	 */
	private function lookupsetLookup($backList,$showfield,$oldViewName,$dataval,$condition,$treeListConfig=''){
		
		$LookupObjmodel=D('LookupObj');//查找带回模型
		//判断索引数组里的名称是否有重复的，有重复的就用以前的，没有重复的，添加一个元素(根据配置文件判断、数据库仅做记录使用)
		$index = $LookupObjmodel->GetLookupIndex();
		$details = $LookupObjmodel->GetLookupDetails();
		$datakey1 = '';
		//当$oldViewName为空表示为新增
		if($oldViewName){
			foreach($details as $k=>$v){
				//当出现新老视图名称一致时才为修改，否则仍为新增
				if($oldViewName==$v['viewname']){
					$datakey1 = $k;
				}
			}
		}		
		//配置索引文件
		if(!$datakey1){
			$indexkey = array_keys($index);
			$datakey1 = md5($_SESSION[C('USER_AUTH_KEY')].time().rand(1000,9999));
			//防止与现有的key值重复
			for($i=0;$i<10;$i++){
				if(!in_array($datakey1,$indexkey)){
					continue;
				}else{
					$datakey1 = md5($_SESSION[C('USER_AUTH_KEY')].time().rand(1000,9999));
				}
			}
		}
		//将本记录添加（置换）到原index索引文件
		$index = array_merge($index,array($datakey1=>$_POST['title']));
		$indexfile = $LookupObjmodel->GetConflistFile();
		$LookupObjmodel->SetSongleRule($indexfile,$index);
		
		//创建（修改）配置详情文件
		$file = $LookupObjmodel->GetDetailsPath().'/'.$datakey1.'.php';
		//重构配置文件数据
		$conflist = array();
		$conflist['title'] = $oldViewName;
		$conflist['url'] = 'lookupGenerals';
		$conflist['mode'] = $_POST['modelname'];
		$conflist['filed'] = $_POST['isshow']?$_POST['isshow']:'name';
		$conflist['val'] = $_POST['dataval']?$_POST['dataval']:'id';
		
		// 遍历datatable组合视图字段信息
		$datasort = '';
		if($_POST['datatable']){
			foreach ($_POST['datatable'] as $k1=>$v1){
					$otherfield = $v1['datatable2']['otherfield'];
					//带回字段
					if($v1['datatable2']['isback']){
						$conflist['fields'] .= $conflist['fields']?','.$otherfield:$otherfield;
						$conflist['fields_china'][$otherfield]=$v1['datatable2']['title']?$v1['datatable2']['title']:$otherfield;
					}	
					//列表显示字段	
					if($v1['datatable2']['islistshow']){
						$conflist['listshowfields'] .= $conflist['listshowfields']?','.$otherfield:$otherfield;
						$conflist['listshowfields_china'][$otherfield]=$v1['datatable2']['title']?$v1['datatable2']['title']:$otherfield;
					}	
					//转换函数字段
					if($v1['datatable2']['funccheck'])	$conflist['funccheck'] .= $conflist['funccheck']?','.$otherfield:$otherfield;
					//转换函数名称方法数组
					if($v1['datatable2']['funcheck']){ 
						$conflist['funcinfo'][$otherfield]=array(
								'field'=>$otherfield,
								'funcname'=>array(
										0=>array(
												0=>$v1['datatable2']['funname']
										),
								),
								'funcdata'=>array(
										0=>array(
												0=>explode(';',$v1['datatable2']['funfield']),
										),
								),
						);
					}	
					if($v1['datatable2']['datasort']){
						$datasort .= $v1['datatable2']['datasort'].' '.$v1['datatable2']['sorttype'].',';
					}		
				}
		}
		if($datasort){
			$conflist['datasort'] = substr($datasort,0,-1);
		}
		$conflist['viewname'] = $_POST['name'];
		$conflist['viewtype']='1'; // 视图类型 1:视图，0:非视图
		$conflist['title'] =  $_POST['title'];
		$conflist['condition'] =  $condition;
		$conflist['dialogwidth'] = $_POST['dialogwidth']?$_POST['dialogwidth']:'820';
		$conflist['dialogheight'] =  $_POST['dialogheight']?$_POST['dialogheight']:'550';
		$conflist['status'] = '1';
		$conflist['level'] =  '15';
		//配置文件组合完成，再组合一个用于数据库的数组
		$confdb = $conflist;
		$confdb['funcinfo'] = base64_encode(serialize($confdb['funcinfo']));
		$confdb['id'] = $datakey1;
		//	存在树形列表配置时，将配置信息写入物理文件
		if(is_array($treeListConfig)&&!empty($treeListConfig)){
			$conflist['treelist'] = $treeListConfig;
		}else{
			if(isset($conflist['treelist'])) unset($conflist['treelist']);
		}
		//所有数据都组合完成，分别入库和生成配置详情文件
		$rs = $LookupObjmodel->where("id='".$datakey1."'")->find($confdb);
		if($rs){
			$num = $LookupObjmodel->where("id='".$datakey1."'")->save($confdb);
		}else{
			$num = M("mis_system_lookupobj")->add($confdb);
		}
		$LookupObjmodel->SetSongleRule($file,$conflist); 
// 		$selectlist = require $LookupObjmodel->GetFile();
// 		$date=array(
// 				'title' => getFieldBy($_POST['modelname'], "name", "title", "node"),
// 				'fields' => implode(',', $backList),
// 				'url' => 'lookupGeneral',
// 				'mode' =>$_POST['modelname'],
// 				'filed' => $showfield,
// 				'val' => $dataval,
// 				'condition' => '',
// 				'status' => '1',
// 				'level' => '15',
// 				'viewname' =>$_POST['name'],
// 				'viewtype'=>'1', // 视图类型 1:视图，0:非视图
// 		);
// 		$selectlist = require $LookupObjmodel->GetFile();
// 		$date=array(
// 				'title' =>$_POST['title'],
// 				'fields' => implode(',', $backList),//带回字段
// 				'url' => 'lookupGenerals',
// 				'mode' =>$_POST['modelname'],
// 				'filed' => $showfield,//显示字段
// 				'listshowfields' => '',
// 				'val' => $dataval,//储存字段
// 				'condition' => $condition,//条件
// 				'status' => '1',
// 				'level' => '15',
// 				'viewname' =>$_POST['name'],
// 				'viewtype'=>'1', // 视图类型 1:视图，0:非视图
// 		);
// 		if($oldViewName){
// 			 $datakey="";
// 			foreach ($selectlist as $key=>$val){
// 				if($val['viewname']==$oldViewName){
// 					$datakey=$key;
// 					break;
// 				}
// 			}
// 			if(!$datakey){
// 				$date['id']= md5($_SESSION[C('USER_AUTH_KEY')].time());
// 				$selectlist[$date['id']]=$date;
// 			}else{
// 				$selectlist[$datakey]=$date;
// 			}
			
// 		}else {
// 			$date['id']= md5($_SESSION[C('USER_AUTH_KEY')].time());
// 			$selectlist[$date['id']]=$date;
// 		}
// 		if($datakey){
// 			$LookupObjmodel->where("id='".$datakey."'")->save($date);
// 		}else{
// 			$LookupObjmodel->add($date);
// 		}
//		$LookupObjmodel->commit();
		//生成list文件 
		//视图查找带回
			$MisSystemDataviewMasViewModel=D("MisSystemDataviewMasView");
			$MisSystemDataviewMasList=$MisSystemDataviewMasViewModel->where("name='{$_POST['name']}'  and modelname='{$_POST['modelname']}' and mis_system_dataview_sub.status=1")->select();
			//$list=$MisSystemDataviewMasViewModel->query($MisSystemDataviewMasList[0]['replacesql']); 
			$viesList=array();
			$detailList=array();
				foreach ($MisSystemDataviewMasList as $msdkey=>$msdval){
					$isshow=0;
					$issearch=0;
					if($msdval['islistshow']==1){
						$isshow=1;
						if(strpos($msdval['field'], "(")===false){
							$issearch=1;
						}
					}
					$detailList[$msdval['otherfield']]=array(
							'name' => $msdval['otherfield'],
							'showname' =>$msdval['subtitle'],
							'shows' => $isshow,
							'widths' => '',
							'sorts' => '0',
							'models' => '',
							'sortname' => $msdval['otherfield'],
							'sortnum' => '5',
							'searchField' => $msdval['field'],
							'conditions' => '',
							'type' => 'text',
							'issearch' => $issearch,
							'isallsearch' => $issearch,
							'searchsortnum' => $msdkey,
							'helpvalue' => '',
							
					);
					if($msdval['funcdata']){
						$fundata=unserialize(base64_decode($msdval['funcdata']));
						if(strtolower($msdval['funname'])=="getfieldby"){
							$detailList[$msdval['otherfield']]['table']=$fundata[3];
							$detailList[$msdval['otherfield']]['field']=$fundata[1];
							$detailList[$msdval['otherfield']]['type']="group";
							$detailList[$msdval['otherfield']]['isallsearch']=0;
						}else if(strtolower($msdval['funname'])=="transtime"){
							$detailList[$msdval['otherfield']]['type']="time";
							$detailList[$msdval['otherfield']]['isallsearch']=0;
						}else if(strtolower($msdval['funname'])=="exceltplselected"){
							$detailList[$msdval['otherfield']]['issearch']=0;
							$detailList[$msdval['otherfield']]['isallsearch']=0;
						}
						$detailList[$msdval['otherfield']]['func'][][]=$msdval['funname'];
						$detailList[$msdval['otherfield']]['funcdata'][][]=$fundata;
					}
			}
			//list文件 判断是否已有文件
			// 新版 带扩展的修改
			$model = D('Autoform');
			$dir = './Admin/Dynamicconf/Models/';
			$listincpath = '/Models/'.$_POST['modelname'].'/'.$_POST['name'].'.inc.php';
			$listincpathdir = $dir.$_POST['modelname'];
			if(!is_dir($listincpathdir)) mk_dir($listincpathdir,0777);
			$model->setPath($listincpath);
			$model->SetListinc($detailList , '系统视图list.inc 配置文件' , false);
	}
	public function _before_update(){
		$name = substr($_POST['name'],-4);
		if(strtolower($name)=='view'){
			$_POST['name'] = substr($_POST['name'],0,-4).'View';
		}else{
			$_POST['name'] = $_POST['name'].'View';
		}
		$_POST['spellsql']=str_replace("&#39;", "'", html_entity_decode($_POST['spellsql']));
	}
	
	/**
	 * @Title: _after_update
	 * @Description: todo(后置update函数)
	 * 
	 * @author 管理员
	 *         @date 2014-11-20 16:39:53
	 * @throws
	 *
	 */
	function _after_update() {
		
		/////////////////////////////////////////////////////////////////////////////////////////////////
		//									lookup树形列表	by nbmxkj 20150921                							//
		/////////////////////////////////////////////////////////////////////////////////////////////////
		$treeListConfig = '';
		if($_POST['treelist']){
			$treeListDataSouce = $_POST['treelist'];
			unset($temp);
			if($treeListDataSouce['istreelist'] ){
				$temp['istreelist'] 		=	$treeListDataSouce['istreelist'];
				$temp['isnextend']	=	$treeListDataSouce['isnextend'];
			}
			if(is_array($temp)){
				$treeListConfig = array(
						'show'=>'name',
						'value'=>'id',
						'parentid'=>'parentid',
						'isnextend'=>$temp['isnextend']
				);
				//$temp['masid'] = $_POST['id'];
				$viewname = getFieldBy($_POST['id'],"id","name","mis_system_dataview_mas");	
				$temp['masid'] = getFieldBy($viewname,"viewname","id","mis_system_lookupobj");				
				if($treeListDataSouce['id']){
					$temp['id'] 		=	$treeListDataSouce['id'];
				}
				$treeListConfigObj = M('mis_system_lookupobj_sub_treelistconfig');
				// 数据写入到树列表配置表中
				if($temp['id']){
					$ret = $treeListConfigObj->data($temp)->save();
					if(false === $ret){
						$this->error(' |树列表配置失败'.$treeListConfigObj->getDbError().'  | '.$treeListConfigObj->getLastSql());
					}
				}else{
					$ret = $treeListConfigObj->data($temp)->add();
					if(false === $ret){
						$this->error(' |树列表配置失败'.$treeListConfigObj->getDbError() . ' ' . $treeListConfigObj->getLastSql());
					}
				}
			}else if($_POST['treelist']['id']){
				$treeListConfigObj = M('mis_system_lookupobj_sub_treelistconfig');
				unset($temp);
				$temp['id'] = $_POST['treelist']['id'];
				$treeListConfigObj->where($temp)->delete();
			}
		}
		/////////////////////////////////////////////////////////////////////////////////////////////////
		
		
		// 内嵌表数据添加处理
		$datatablefiexname = "mis_system_dataview_sub";
		$insertData = array (); // 数据添加缓存集合
		$updateData = array (); // 数据修改缓存集合
		$selectfield=array(); //替换查询语句
		$backList=array();//带回字段集合
		$showfield="";
		$dataval="";
		//如果改变sql 则删除子表原字段信息
		if($_POST['changesql']){
			D($datatablefiexname)->where("status=1 and masid={$_POST ['id']}")->setField ( 'status', - 1 );
		}
		
		
		if ($_POST ['datatable']) {
			foreach ( $_POST ['datatable'] as $key => $val ) {
				foreach ( $val as $k => $v ) {
					if($_POST['changesql']){
						unset($v['id']);
					}
					//存入关联函数
					$func=$v['funname'];
					if($func){
						if(!function_exists($func)) $this->error("关联操作函数".$func."不存在！");
						$v['funname']=$func;
					}
					if($v['funfield']){
						$funcdata=$v['funfield'];
						if($funcdata){
							$dchar=explode(";",$funcdata);
							if(count($dchar)>10) $this->error("关联操作函数参数最多只能传递10个！");
							$v['funcdata']=base64_encode(serialize($dchar));
						}
					}else{
						$v['funcdata']="";
					}
					//ishow 没有key值
					if($v['otherfield']==$_POST['isshow']){
						$v['isshow']=$_POST['isshow'];
						// 						 	if($_POST ['datatable'][($key+1)][$k]['funname']=="transtime"){
						// 						 		$v['otherfield']=$v['otherfield']."-date";
						// 						 	}
						$showfield=$_POST['isshow'];
					}else{
						$v['isshow']=0;
					}
					if($v['otherfield']==$_POST['dataval']){
						$v['dataval']=$_POST['dataval'];
						$dataval=$_POST['dataval'];
					}else{
						$v['dataval']=0;
					}

					if($v['datasort']){
						$v['datasort'] = $v['datasort'];
						$v['sorttype'] = $v['sorttype'];
					}else{
						$v['datasort'] = '';
						$v['sorttype'] = '';
					}
					if ($v ['id']) {
						 if($v['isback']==1){
						 	$v['isback']=1;
						 }else{
						 	$v['isback']=0;
						 }
						 if($v['islistshow']==1){
						 	$v['islistshow']=1;
						 }else{
						 	$v['islistshow']=0;
						 }
						 if($v['funccheck']==1){
						 	$v['funccheck']=1;
						 }else{
						 	$v['funccheck']=0;
						 }
						$updateData [$k] [] = $v;
					} else {
						$insertData [$k] [] = $v;
					}
					if($v['isback']==1||$v['islistshow']==1){
						$selectfield[]=$v['field']." AS ".$v['otherfield'];
						if($v['isback']==1){
							$backList[]=$v['otherfield'];
						}
					}
				}
			}
		}
		$NodeModel=D("Node");
		if($_POST['isdefault']==1){
			$data['viewname']=$_POST['name'];
			//修改节点默认视图
			$NodeResult=$NodeModel->where("name='".$_POST['modelname']."'")->save($data);
			//$NodeModel->commit();
		}else{
			//判断节点默认视图是否是当前视图
			if(getFieldBy($_POST['modelname'], "name", "viewname", "node")==$_POST['name']){
				$data['viewname']="";
				$NodeResult=$NodeModel->where("name='".$_POST['modelname']."'")->save($data);
				//$NodeModel->commit();
			}
		}
		$selectfieldStr=implode(',', $selectfield);
		$spellsql=$_POST['spellsql'];
		$spellnewsql=preg_replace('/\s/',' ',$spellsql);
		// 		$this->curSQLFiled=$selectfieldStr;
		$replacesql = preg_replace('/(SELECT) (.*) (FROM)/is', "\\1  {$selectfieldStr}  \\3",$spellnewsql);
		$replacenewsql = str_replace(array('&quot;','&#39;','&lt;','&gt;'), array('"', "'",'<','>'), $replacesql);
	 	//读取类
	 	import ( '@.ORG.SqlParse' );
	 	$obj = new SqlParse();
	 	$viewlist=$obj->checkSQLFields($spellsql);
	 	$oldcondition=$viewlist['condition']['condition'];
	 	$grouplist=$viewlist['condition']['group'][0];
	 	//转换格式
	 	$condition=str_replace("&#39;", "'", html_entity_decode($oldcondition));
	 	$groupcondition=str_replace("&#39;", "'", html_entity_decode($grouplist));
	 	$spellSqlList = preg_split('/(\bwhere\b)/i', $spellsql, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
	 	$spellwheresql=$spellSqlList[0]."    %WHERE% %ORDER% %GROUP% %LIMIT%";
	 	//加入条件
	 	$MisSystemDataviewMasModel=M("mis_system_dataview_mas");
	 	$date['groupcondition']=$groupcondition;
	 	$date['condition']=$condition;
	 	$date['spellwheresql']=str_replace("&#39;", "'", html_entity_decode($spellwheresql));;
	 	$MisSystemDataviewMasModel->where("id=".$_POST['id'])->data($date)->save();
	 	//$MisSystemDataviewMasModel->commit();
	 	unset($viewlist['condition']);
	 	//生成视图文件
	 	$this->appendView($viewlist);
	 	
		//插入转义后的sql语句
		$misSystemDataviewMasModel=D('MisSystemDataviewMas');
		$misSystemDataviewMasModel->where("id=".$_POST['id'])->setField ( 'replacesql', $replacenewsql);

		// 数据处理
		if ($insertData) {
			foreach ( $insertData as $k => $v ) {
				$model = M ( $datatablefiexname );
				foreach ( $v as $key => $val ) {
					$val ['masid'] = $_POST ['id'];
					$model->add ( $val );
				}
			}
		}
			//print_r($updateData);exit;
		if ($updateData) {
			foreach ( $updateData as $k => $v ) {
				$model = M ( $datatablefiexname );
				foreach ( $v as $key => $val ) {
					$result=$model->save ( $val );
				}
			}
		}
		
		
		$this->lookupsetLookup($backList,$showfield,$_POST['oldViewName'],$dataval,$condition,$treeListConfig);
// 		if($result===false){
// 			$this->success("操作失敗！！");
// 		}else{
// 			$this->error("操作成功！！");
// 		}
	}
	/**
	 * 
	 * @Title: appendView
	 * @Description: todo(拼装list文件) 
	 * @param unknown $viewlist  
	 * @author renling 
	 * @date 2015年1月26日 下午12:42:33 
	 * @throws
	 */
	private function appendView($viewlist){
		$appendHtml="";
		$stkey=1;
		foreach ($viewlist as $key=>$val){
			$fields="";
			foreach ($val['fields'] as $fkey=>$fval){
				if($fkey==$fval){
					$fields.="'".$fval."',";
				}else{
					$fields.="'{$fkey}'=>'{$fval}',";
				}
			 }
			 $str=$fields;
			 $typecon="'_on'=>'".trim($val['_on'])."'";
			 if($val['_on']){
			 	$str.=$typecon;
			 }
			 if($stkey!=count($viewlist)){
			 	$type="";
			 	if($val['_on']){
			 		$type.=",";
			 	}
			 $type.="'_type'=>"."'".$val['_type']."'";
			 if($val['_type']){
			 	$str.=$type;
			 }
			 }
			 $str.="),";
			$appendHtml.="\t\t'".$val['tablename']."'=>array('_as'=>'".$val['tablename']."',$str\r\n";
			$stkey++;
		}
		$appendHtml.")";
		//生成到对应文件
		$modelPath =  LIB_PATH."Model/".$_POST['name']."Model.class.php";
		$phpcode.="<?php\r\n/**";
		$phpcode.="\r\n * @Title: ".$_POST['name']."Model";
		$phpcode.="\r\n * @Package package_name";
		$phpcode.="\r\n * @Description: todo(动态表单_自动生成-".$_POST['title'].")";
		$phpcode.="\r\n * @author ".$_SESSION['loginUserName'];
		$phpcode.="\r\n * @company 重庆特米洛科技有限公司";
		$phpcode.="\r\n * @copyright 本文件归属于重庆特米洛科技有限公司";
		$phpcode.="\r\n * @date ".date('Y-m-d H:i:s');
		$phpcode.="\r\n * @version V1.0";
		$phpcode.="\r\n*/";
		$phpcode.="\r\nclass ";
		$phpcode.= $_POST['name']."Model extends ViewModel {\r\n\t";
		$phpcode.="public \$viewFields = array(\r\n".$appendHtml.");";
		$phpcode.="\r\n}\r\n?>";
		if(!is_dir(dirname($modelPath))) mk_dir(dirname($modelPath),0777);
		if( false === file_put_contents( $modelPath , $phpcode )){
			$this->error ("视图文件生成失败!");
		}
	}
	/**
	 * @Title: delsubinfo
	 * @Description: todo(子表数据删除)
	 * 
	 * @author 管理员
	 *         @date 2014-11-20 16:39:53
	 * @param e $_POST['table']
	 *        	表名
	 * @param e $_POST['id']
	 *        	数据ID值
	 * @throws
	 *
	 */
	function delsubinfo() {
		$table = $_POST ['table'];
		$id = intval ( $_POST ['id'] );
		if ($table) {
			$model = M ( $table );
			$map ['id'] = array (
					'eq',
					$id 
			);
			$model->where ( $map )->delete ();
			$this->success ( '数据成功删除' );
		}
	}
	/**
	 *
	 * @Title: lookupsubstr
	 * @Description: todo(分割字段)
	 * 
	 * @author renling
	 *         @date 2014年11月28日 下午5:16:26
	 * @throws
	 *
	 */
	public function lookupsubstr() {
		import ( '@.ORG.SqlParse' );
		$sql_string=$_POST['shepllsql'];
		$return_data=SqlParse::getFileds($sql_string);
		foreach ($return_data as $rkey=>$rval){
			$DynamicconfModel=D("Dynamicconf");
			$tablelist=$DynamicconfModel->getTableInfo($rval['table'],$rkey);
			$return_data[$rkey]['title']=$tablelist[$rkey]['COLUMN_COMMENT'];
		}		
		$returnArr=array();
		$oldformdata=unserialize(base64_decode($_POST['oldformdata']));
		//查询之前缓存数据
		$randtableDao=M($oldformdata['randtable']);	
		$map=array();
		$map['tablename']="mis_system_dataview_sub";
		$map['randnum']=$oldformdata['randnum'];
		$map['createid']=$oldformdata['createid'];;
		$randtableVo=$randtableDao->where($map)->find();
		$oldList=unserialize(base64_decode($randtableVo['backupdata']));
		$oldArr=array();
		foreach ($oldList as $oldkey=>$oldval){
			$oldArr[$oldval['otherfield']]=$oldval;
		}
		foreach ($return_data as $rekey=>$reval){
			if($oldArr[$rekey]){
				$returnArr[$rekey]=array_merge($reval,$oldArr[$rekey]);
			}else{
				$returnArr[$rekey]=$reval;
			}
		}
		echo json_encode($returnArr);
	}
	private function diy_preg_split($string = '', $preg_array = array()) {
		if (empty ( $string ) || empty ( $preg_array )) {
			return array ();
		} else {
			$i = 0;
			$preg_string = '/(';
			foreach ( $preg_array as $preg ) {
				$preg_string .= ($i ? '|' : '') . '\b' . $preg . '\b';
				$i ++;
			}
			$preg_string .= ')/i';
			
			return preg_split ( $preg_string, $string, - 1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE );
		}
	}
	/**
	 * 
	 * @Title: _before_delete
	 * @Description: todo(删除前置方法 删除sub表数据)   
	 * @author renling 
	 * @date 2014年12月2日 下午6:15:54 
	 * @throws
	 */
	public function _before_delete(){		
		$id=$_REQUEST['id'];
		//获取视图名称，用于检查其在漫游是否有调用，以及lookupobj数据表数据和配置文件
		$viewname = getFieldBy($id,'id','name',"mis_system_dataview_mas");
		
		//检查是否在漫游有使用，如果有不允许删除
		$roamlist = D("mis_system_data_roam_relation_view")->where("viewtable='{$viewname}'")->find();
		if($roamlist) $this->error("该视图在漫游有使用，不能删除");
		
		//删除lookupobj数据库数据及生成的lookupobj配置文件
		/**
		 * 1、视图保证了 视图名称唯一，故生成的lookupobj数据唯一 用find方法就好
		 * 2、----注意 ----以前的数据是假删，现在好像是一次删除数据，这里按一次删除数据处理的
		 */
		$lookupmodel = D("LookupObj");
		$lookupmap['viewtype'] = array('eq',1);
		$lookupmap['viewname'] = array('eq',$viewname);
		$lookuplist = $lookupmodel->where($lookupmap)->find();
		
		//删除index索引文件的记录
		$lookupIndexarr = $lookupmodel->GetLookupIndex();
		unset($lookupIndexarr[$lookuplist['id']]);
		$filename = Dynamicconf."/LookupObj/Index/list.inc.php";
		$lookupmodel->SetSongleRule($filename,$lookupIndexarr);
		
		//删除Details配置详情文件
		unlink(Dynamicconf."/LookupObj/Details/".$lookuplist['id'].".php");
		
		//删除mis_system_lookupobj数据表的记录
		$lookupmodel->where($lookupmap)->delete();		

		//删除从表信息
		$MisSystemDataviewSubModel=D("MisSystemDataviewSub");
		$MisSystemDataviewSubResult=$MisSystemDataviewSubModel->where("status=1 and masid={$id}")->delete();
		if(!$MisSystemDataviewSubResult){
			$this->error("删除从表错误！");
		}
	}
	/**
	 * @Title: nvigateTO
	 * @Description:临时表存储历史记录
	 * @author liminggang
	 * @date 2014-10-13 下午8:22:48
	 * @paramate $model mix  $pkval int
	 * @return $updateBackup(临时表关键查找信息);
	 * @throws
	 */
	private function setDataViewToCache($modelname,$vo){
		$model=D($modelname);
		$updateTable="mis_system_dataview_sub";
		$updateSave= base64_encode(serialize($vo));
		$randtable="update_dataview_cache".mt_rand(1,5);//选择对应的内存表，减少并发压力
		$randnum=mt_rand(1000,9999);//随机数号；通过表名+随机数+操作人锁定唯一的记录
		M()->execute("CREATE TABLE if not exists `".$randtable."` (
					  `id` int(10) NOT NULL AUTO_INCREMENT,
					  `backupdata` text DEFAULT NULL,
				      `tablename` varchar(100) DEFAULT NULL,
					  `randnum` int(4) DEFAULT NULL,
					  `createid` int(10) DEFAULT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;");
		M()->execute("INSERT INTO  ".$randtable." (`tablename`,`backupdata`,`randnum`,`createid`)VALUES('".$updateTable."','".$updateSave."','".$randnum."','".$_SESSION[C('USER_AUTH_KEY')]."');");
		$updateBackup=array('randtable'=>$randtable,'tablename'=>$updateTable,'randnum'=>$randnum,'createid'=>$_SESSION[C('USER_AUTH_KEY')]);
		return $updateBackup;
	}
}
?>