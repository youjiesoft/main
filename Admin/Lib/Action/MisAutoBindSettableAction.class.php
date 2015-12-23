<?php
/**
 * 
 * @Title: MisAutoBindSettableAction 
 * @Package package_name
 * @Description: todo(套表管理) 
 * @author renling 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014年12月13日 上午11:49:20 
 * @version V1.0
 */
class MisAutoBindSettableAction extends CommonAction {
	
	public function index(){
		//获取当前控制器名称
		$name = $this->getActionName ();
		/*
		 * 第一步、查询左侧树形结构
		 * 第二步、查询右侧数据信息
		 * 
		 * 点击左侧树形 无需在查询树形结构数据
		 */
		$MisAutoBindSettableModel = D ( 'MisAutoBindSettable' );
		// 读取当前表单字段
		$formid = $_REQUEST ['id'] ? $_REQUEST ['id'] : Cookie::get ( "formid" );
		$map = array ();
		$map ['status'] = 1;
		$map ['bindaname'] = getFieldBy ( $formid, "id", "actionname", "mis_dynamic_form_manage" );
		$map ['typeid']=2;//套表绑定
		// 查询该表单绑定关系
		$DynamicconfModel=D("Dynamicconf");
		$MisAutoBindSettableList = $MisAutoBindSettableModel->where ( $map )->select ();
		foreach ($MisAutoBindSettableList as $bkey=>$bval){
			//查询被绑表字段
			$tablenames=D($bval['inbindaname'])->getTableName();
			$MisAutoBindSettableList[$bkey]['tablelist']=$DynamicconfModel->getTableInfo($tablenames);
			//获取漫游数据
			$dataroam[$bkey] = $this->getroambase($bval['bindaname'],$bval['inbindaname']);
			unset($MisAutoBindSettableList[$bkey]['tablelist']['id']);
		}
		if($dataroam) $this->assign('dataroam',$dataroam);
		$this->assign ( "MisAutoBindSettableList", $MisAutoBindSettableList );
		$this->assign ( "formid", $formid );
		// 查询真实表字段
		$tablename = D ( $map ['bindaname'] )->getTableName ();
		$DynamicconfModel = D ( "Dynamicconf" );
		$tablelist = $DynamicconfModel->getTableInfo ( $tablename );
		unset ( $tablelist ['id'] ); // 清除id
		foreach ( $tablelist as $tkey => $tval ) {
			$MisDynamicDatabaseSubList [$tval ['COLUMN_NAME']] = $tval ['COLUMN_COMMENT'];
		}
		$this->assign ( "MisDynamicDatabaseSubList", $MisDynamicDatabaseSubList );
		// 读取字段end
		// 读取可绑定的表单
		$MisDynamicFormManageModel = D ( "MisDynamicFormManage" );
		$mdMap = array ();
		$mdMap ['status'] = 1;
		//读取当前表单主表 2为套表主表 去掉0714
		//$MisAutoBindSettablePList=$MisAutoBindSettableModel->where("status=1 and pid=0 and typeid=2")->getField("id,bindformid");
		if($MisAutoBindSettablePList){
			$MisAutoBindSettablenewPList=array_values($MisAutoBindSettablePList);
			$MisAutoBindSettablenewPList[]=$formid;
			$mdMap['id']=array("not in",array_unique($MisAutoBindSettablenewPList));
		}else{
			$mdMap ['id'] = array (
					"not in",
					$formid
			);
		}
		// 查询动态表单记录表
		$MisDynamicFormManageList = $MisDynamicFormManageModel->where ( $mdMap )->getField ( "id,actiontitle" );
		// 		echo $MisDynamicFormManageModel->getlastSql();
		$this->assign ( "MisDynamicFormManageList", $MisDynamicFormManageList );
		$this->assign ( "formid", $formid );
		$actionname=$_REQUEST['aname'];
		$this->assign("actionname",$actionname);
		//获取系统默认字段$bindFormData
		if ($_REQUEST ['jump']) {
			$this->display ( "indexview" );
		} else {
			//组合树形结构
			$typeTree = $MisAutoBindSettableModel->getBindtree ( "MisAutoBindSetview",1);
			$this->assign ( "typeTree", json_encode ( $typeTree ) );
			
			$this->display ();
		}
	}
	
	
	
	public function index1() {
		$name = $this->getActionName ();
		// 生成左侧树结构
		// 表单绑定查询对象
		$MisAutoBindSettableModel = D ( 'MisAutoBindSettable' );
		$typeTree = $MisAutoBindSettableModel->getBindtree ( "MisAutoBindSetview",1);
		$this->assign ( "typeTree", json_encode ( $typeTree ) );
		// 读取当前表单字段
		$formid = $_REQUEST ['id'] ? $_REQUEST ['id'] : Cookie::get ( "formid" );
		$map = array ();
		$map ['status'] = 1;
		$map ['bindaname'] = getFieldBy ( $formid, "id", "actionname", "mis_dynamic_form_manage" );
		$map ['typeid']=2;//套表绑定
		// 查询该表单绑定关系
		$DynamicconfModel=D("Dynamicconf");
		$MisAutoBindSettableList = $MisAutoBindSettableModel->where ( $map )->select ();
// 		print_r($MisAutoBindSettableList);
		foreach ($MisAutoBindSettableList as $bkey=>$bval){
			//查询被绑表字段
			$tablenames=D($bval['inbindaname'])->getTableName();
			$MisAutoBindSettableList[$bkey]['tablelist']=$DynamicconfModel->getTableInfo($tablenames);
			//获取漫游数据
			$dataroam[$bkey] = $this->getroambase($bval['bindaname'],$bval['inbindaname']); 
			unset($MisAutoBindSettableList[$bkey]['tablelist']['id']);
		}
		if($dataroam) $this->assign('dataroam',$dataroam);
		$this->assign ( "MisAutoBindSettableList", $MisAutoBindSettableList );
		$this->assign ( "formid", $formid );
		// 查询真实表字段
		$tablename = D ( $map ['bindaname'] )->getTableName ();
		$DynamicconfModel = D ( "Dynamicconf" );
		$tablelist = $DynamicconfModel->getTableInfo ( $tablename );
		unset ( $tablelist ['id'] ); // 清除id
		foreach ( $tablelist as $tkey => $tval ) {
			$MisDynamicDatabaseSubList [$tval ['COLUMN_NAME']] = $tval ['COLUMN_COMMENT'];
		}
		$this->assign ( "MisDynamicDatabaseSubList", $MisDynamicDatabaseSubList );
		// 读取字段end
		// 读取可绑定的表单
		$MisDynamicFormManageModel = D ( "MisDynamicFormManage" );
		$mdMap = array ();
		$mdMap ['status'] = 1;
		//读取当前表单主表 2为套表主表
		$MisAutoBindSettablePList=$MisAutoBindSettableModel->where("status=1 and pid=0 and typeid=2")->getField("id,bindformid");
		if($MisAutoBindSettablePList){
			$MisAutoBindSettablenewPList=array_values($MisAutoBindSettablePList);
			$MisAutoBindSettablenewPList[]=$formid;
			$mdMap['id']=array("not in",array_unique($MisAutoBindSettablenewPList));
		}else{
			$mdMap ['id'] = array (
					"not in",
					$formid
			);
		}
		// 查询动态表单记录表
		$MisDynamicFormManageList = $MisDynamicFormManageModel->where ( $mdMap )->getField ( "id,actiontitle" );
// 		echo $MisDynamicFormManageModel->getlastSql();
		$this->assign ( "MisDynamicFormManageList", $MisDynamicFormManageList );
		$this->assign ( "formid", $formid );
		$actionname=$_REQUEST['aname'];
		$this->assign("actionname",$actionname);
		// //获取系统默认字段$bindFormData
		if (intval ( $_POST ['dwzloadhtml'] )) {
			$this->display ( "dwzloadindex" );
			exit ();
		}
		if ($_REQUEST ['jump']) {
			$this->display ( "indexview" );
		} else {
			$this->display ();
		}
	}
	public function insert() {
		$inbindaname = $_POST ['inbindaname'];
		$modelList = array ();
		if (count ( $inbindaname ) != count ( array_unique ( $inbindaname ) )) {
			$this->error ( "数据重复,请查证后提交！" );
		}
		
		//////////////////////////////////////////////////////////////////////////////////////////
		//		事务控制数据操作。by nbmxkj@20150409 1713
		/////////////////////////////////////////////////////////////////////////////////////////
		$MisAutoBindSettableModel = D ( "MisAutoBindSettable" );
		
		// -- 数据抓取
		// 获取pid
		$pid = 0;
		// 查询当前节点是否为顶级节点
		if (getFieldBy ( $_POST['bindaname'], "inbindaname", "bindaname", "mis_auto_bind","typeid",2)) {
			// 当前有绑定上级
			$pid = getFieldBy ( $_POST['bindaname'], "actionname", "id", "mis_dynamic_form_manage" );
		}
		//获取层级id
		//查询最高级formid
		$level=getFieldBy($_POST ['bindaname'],"actionname","id","mis_dynamic_form_manage");
		$endlevel=$level;
		if($pid!=0){
			$level=$this->getFirstLevel($_POST ['bindaname']);
			$endlevel=getFieldBy($level,"actionname","id","mis_dynamic_form_manage");
		}
		$MisSystemDataRoamSubDao = M ( "mis_system_data_roam_sub" );
		$MisSystemDataRoamMasDao = M ( "mis_system_data_roam_mas" );
		
		
		// 事务控制处理数据处理
		$MisAutoBindSettableModel->startTrans();
		$delresult = true;
		
		// 删除原有记录
		//if ($_POST ['void']) {
			$bindaname = $_POST ['bindaname'];
			$bindMap = array ();
			$bindMap ['bindaname'] = $bindaname;
			$delresult = $MisAutoBindSettableModel->where ( $bindMap )->delete ();
		//}
		
		foreach ( $inbindaname as $key => $val ) {
			if ($val) {
				$actionname = getFieldBy ( $val, "id", "actionname", "mis_dynamic_form_manage" );
				$data = array ();
				$bindconditionArr = $_POST ['bindcondition'] [$key];
				$inbindconditionArr = $_POST ['inbindcondition'] [$key];
				
				foreach ( $bindconditionArr as $akey => $aval ) {
					if ($aval) {
						$bindconlistArr [$aval] = $inbindconditionArr [$akey];
					}
				}
				$data ['bindconlistArr'] = serialize ( $bindconlistArr );
				unset ( $bindconlistArr );
				$data ['bindaname'] = $_POST ['bindaname'];
				$data ['bindval'] = $_POST ['bindval'] [$key];
				$data ['bindtype'] = $_POST ['bindtype'] [$key];
				$data ['formshowtype']=$_POST['formshowtype'][$key];
				$data ['isdelete']=$_POST['isdelete'][$key];
				$data ['inbindaname'] = $actionname;
				$data ['inbindtitle'] = $_POST ['inbindtitle'][$key];
				$data ['inbindsort'] = $_POST ['inbindsort'][$key];
				$data ['inbindval'] = $_POST ['inbindval'] [$key];
				$data ['bindformid'] = $_POST ['formid'];
				$data ['inbindformid'] = $val;
				$data ['backval'] = implode ( ',', $_POST ['backval'] [$key] ); // 主表带回字段
				$data ['bindcondition'] = implode ( ',', $_POST ['bindcondition'] [$key] ); // 主表附加条件
				$data ['inbackval'] = implode ( ',', $_POST ['inbackval'] [$key] ); // 子表带回字段
				$data ['inbindcondition'] = implode ( ',', $_POST ['inbindcondition'] [$key] ); // 子表附加条件
				$data ['showrules'] = str_replace ( "&#39;", "'", html_entity_decode ( $_POST ['showrules'] [$key] ) );
				$data ['rulesinfo'] = $_POST ['rulesinfo'] [$key];
				$data ['inbindmap'] = str_replace ( "&#39;", "'", html_entity_decode ($_POST ['rules'] [$key]));
				$data ['dataroamid'] = $_POST ['dataroamid'][$key];//$dataroamid; // 漫游数据id
				$data ['pid'] = $pid;
				$data ['level'] = $endlevel;
				$data ['iscopy'] =$_POST ['iscopy'] [$key];
				$data ['typeid'] = 2;//套表typeid为2
				$addresult = $MisAutoBindSettableModel->add ( $data );
				if(!$addresult)
					break;
			}
		}
		
		if($delresult && $addresult){
			$MisAutoBindSettableModel->commit();
			Cookie::set ( "formid", $_POST ['formid'] );
			$this->success("操作成功！");
		}else{
			$MisAutoBindSettableModel->rollback();
			$this->error("数据添加失败！！！");
		}
	}
	/**
	 *
	 * @Title: changeststus
	 * @Description: todo(修改状态)
	 * 
	 * @param int $type
	 *        	0：ajax 调用，1：代码内部函数调用
	 * @param int $status
	 *        	要被修改的状态值
	 * @param int $id        	
	 * @author renling
	 *         @date 2014年12月10日 上午11:41:09
	 * @throws
	 *
	 */
	function changestastus($type = 0, $status, $id) {
		$obj = D ( 'MisAutoBind' );
		if (! $status || ! $id) {
			$status = $_REQUEST ['status'];
			$id = $_REQUEST ['id'];
		}
		$binname = $_REQUEST ['binname'];
		$sMap = array ();
		$sMap ['status'] = $status == - 1 ? 1 : - 1;
		$sMap ['bindname'] = $binname ? $binname : getFieldBy ( $id, "id", "bindname", "mis_auto_bind" );
		if ($id) {
			$typeid = getFieldBy ( $id, "id", "typeid", "mis_auto_bind" );
		} else {
			$typeid = $binname;
		}
		$islast = $obj->where ( $sMap )->find ();
		if (! $islast) {
			// 不存在恢复代码
			if ($status == - 1) {
				// 恢复成独立可使用的模块
			} else {
				// 恢复成主从组合表单
				if ($typeid == 1) {
					
					// 恢复成表单组合
				} else {
				}
			}
		}
		if ($status) {
			$data ['status'] = $status;
			if ($binname) {
				// 一键解绑 一键绑定
				$map ['bindname'] = $binname;
			} else {
				$map ['id'] = $id;
			}
			$ret = $obj->where ( $map )->save ( $data );
			if ($ret)
				$this->success ( '修改成功' );
			else
				$this->error ( '修改失败' );
		}
	}
	/**
	 *
	 * @Title: lookupChangeField
	 * @Description: todo(ajax请求数据表字段)
	 * 
	 * @author renling
	 *         @date 2014年12月13日 下午2:59:11
	 * @throws
	 *
	 */
	public function lookupChangeField() {
		if ($_POST ['type']) {
			$delinbindaname = $_POST ['delinbindaname'];
			// 查询当前要删除的子级是否为绑定表
			if (getFieldBy ( $delinbindaname, "bindaname", "inbindaname", "mis_auto_bind_settable" )) {
				echo 1; // 有子表信息
			} else {
				echo - 1; // 无子表信息
			}
		} else {
			// 得到查询的formid
			$formid = $_POST ['id'];
			// 获取节点名称
			$actionname = getFieldBy ( $formid, "id", "actionname", "mis_dynamic_form_manage" );
			// 获取表结构
			$tablename = D ( $actionname )->getTableName ();
			$DynamicconfModel = D ( "Dynamicconf" );
			$tablelist = $DynamicconfModel->getTableInfo ( $tablename );
			$MisDynamicDatabaseSubDao = M ( "mis_dynamic_database_sub" );
			// $MisDynamicDatabaseSubList['list']=$MisDynamicDatabaseSubDao->where("formid={$formid}")->getField("field,title");
			// $tpltype=getFieldBy($formid, "id", "tpl", "mis_dynamic_form_manage");
			// 当前表单类型为组合表单 生成绑定字段
			// if($tpltype=="zuhetpl#list"||$tpltype=="zuhetpl#ltrl"){
			// $MisDynamicDatabaseSubList['list']['bindid']="组合表单关联字段";
			// }
			//unset ( $tablelist ['id'] ); // 清除id
			foreach ( $tablelist as $tkey => $tval ) {
				$MisDynamicDatabaseSubList ['list'] [$tval ['COLUMN_NAME']] = $tval ['COLUMN_COMMENT'];
			}
			$MisDynamicDatabaseSubList ['modelname'] = getFieldBy ( $formid, "id", "actionname", "mis_dynamic_form_manage" );
			echo json_encode ( $MisDynamicDatabaseSubList );
		}
	}
	public  function delete(){
	
		if($_POST['bindaname']){
			if(!getFieldBy($_POST['bindaname'],"inbindaname","id","inbindaname")){
				//删除当前表下的所有绑定关系
				$delMap=array();
				$delMap['bindaname']=$_POST['bindaname'];
				$delMap['typeid']=2;
				$MisAutoBindSettableModel=D("MisAutoBindSettable");
				// 事务控制处理数据处理
				$MisAutoBindSettableModel->startTrans();
				
// 				//清除数据漫游
// 				$MisAutoBindSettableList=$MisAutoBindSettableModel->where($delMap)->getField("id,dataroamid");
 				$MisSystemDataRoamSubRet = true;
 				$MisSystemDataRoamMasRet = true;
// 				if($MisAutoBindSettableList){
// 					$subMap['masid']=array("in",array_values($MisAutoBindSettableList));
// 					//查询漫游sub表记录
// 					$MisSystemDataRoamSubDao=M("mis_system_data_roam_sub");
// 					$MisSystemDataRoamMasDao=M("mis_system_data_roam_mas");
// 					$MisSystemDataRoamSubRet = $MisSystemDataRoamSubDao->where($subMap)->delete();
// 					//$MisSystemDataRoamSubDao->commit();
// 					$masMap['id']=array("in",array_values($MisAutoBindSettableList));
// 					$MisSystemDataRoamMasRet = $MisSystemDataRoamMasDao->where($masMap)->delete();
// 					//$MisSystemDataRoamMasDao->commit();
// 				}
				$result=$MisAutoBindSettableModel->where($delMap)->delete();
				if( $result && $MisSystemDataRoamSubRet && $MisSystemDataRoamMasRet ){
					$MisAutoBindSettableModel->commit();
					$this->success("清除关系成功！");
				}else{
					$MisAutoBindSettableModel->rollback();
					$this->error("删除失败！");
				}
			}else{
				$this->error("当前表单存在子级,删除失败！！");
			}
	
		}
	}
	private  function getFirstLevel($actionname){
		$pFirstAction=getFieldBy($actionname,"bindaname","inbindaname","MisAutoBindSettable","pid",0);
		//查询当前节点的父级
		if(!$pFirstAction){
			$firstaction=getFieldBy($actionname,"inbindaname","bindaname","MisAutoBindSettable");
			return $this->getFirstLevel($firstaction);
		}else{
			return $actionname;
		}
	}
	//ajax传来源目标model 获取套表漫游数据
	function getroam(){
		$map['sourcemodel'] = $_POST['sourcemodel'];
		$map['targetmodel'] = getFieldBy($_POST['targetmodel'],'id','actionname','mis_dynamic_form_manage');
		$list = $this->getroambase($map['sourcemodel'],$map['targetmodel']);
		
		exit(json_encode($list));
	}
	function getroambase($sourcemodel,$targetmodel){
		$map['sourcemodel'] = $sourcemodel;//$_POST['sourcemodel'];
		$map['targetmodel'] = $targetmodel;//getFieldBy($_POST['targetmodel'],'id','actionname','mis_dynamic_form_manage');
		$map['isbindsettable'] = array('eq',1);
		$map['status'] = array('eq',1);
		$model = M("mis_system_data_roam_mas");
		$list = $model->where($map)->select();
		foreach($list as $k=>$v){
			if(!$v['title']){
				$list[$k]['title']=$v['targetname'];
			}
		}
		return $list;
	}
	
	/**
	 * 获取新条件组件代码
	 * @Title: getConditionControll
	 * @Description: todo(这里用一句话描述这个方法的作用)   
	 * @author quqiang 
	 * @date 2015年3月12日 下午3:59:53 
	 * @throws
	 */
	function getConditionControll(){
		$data = W('ShowAddResult' , array('model'=>MODULE_NAME,'multitype'=>'multitype'));
		echo $data;
	}
}

