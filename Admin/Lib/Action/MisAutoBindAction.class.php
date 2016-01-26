<?php
/**
 * 
 * @Title: MisAutoBindActionAction 
 * @Package package_name
 * @Description: todo(组合表单绑定关系) 
 * @author renling 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014年12月9日 下午5:10:32 
 * @version V1.0
 */
class MisAutoBindAction extends CommonAction {
	
	public function index(){
		// 获取当前需要显示的绑定主表单名称
		$bindFormName = $_REQUEST['aname'];
		
		if(!$bindFormName){
			
		}
		$name=$this->getActionName();
		//生成左侧树结构
		// 表单绑定查询对象
		$MisDynamicFormManageModel=D('MisDynamicFormManage');
		$typeTree=$MisDynamicFormManageModel->getAnametree("MisAutoBindview",1);
		$this->assign("typeTree",json_encode($typeTree));
		$MisAutoBindModel=D('MisAutoBind');
		//查询当前表字段
		$tablename=D($bindFormName)->getTableName();
		$DynamicconfModel=D("Dynamicconf");
		$tablelist=$DynamicconfModel->getTableInfo($tablename);
		unset($tablelist['id']);
		$this->assign("tablelist",$tablelist);
		// 查询绑定表单信息
		$bindFormSearchMap['bindaname']=array('eq',$bindFormName);
		$bindFormSearchMap['typeid']=array('neq',2);//排除套表
		$bindFormData= $MisAutoBindModel->where($bindFormSearchMap)->select();
		foreach ($bindFormData as $bkey=>$bval){
			//查询被绑表字段
			$tablenames=D($bval['inbindaname'])->getTableName();
			$bindFormData[$bkey]['tablelist']=$DynamicconfModel->getTableInfo($tablenames);
			//获取漫游数据
			$dataroam[$bkey] = $this->getroambase($bval['bindaname'],$bval['inbindaname']);
			unset($bindFormData[$bkey]['tablelist']['id']);
		}
		if($dataroam) $this->assign('dataroam',$dataroam);
		//读取可绑定的表单
		$MisDynamicFormManageModel=D("MisDynamicFormManage");
		$formid=$_REQUEST['id'];
		$actionname=$_REQUEST['aname'];
		$this->assign("actionname",$actionname);
		$mdMap=array();
		$mdMap['status']=1;
		$mdMap['id']=array("not in",$formid);
		//去除pid为0的绑定表单主表
		$bindMap=array();
		$bindMap['pid']=0;
		$MisAutoBindList=$MisAutoBindModel->where($bindMap)->getField("id,bindformid");
		$MisAutoBindnewList=array();
// 		if($MisAutoBindList){
// 			$MisAutoBindnewList=array_unique(array_values($MisAutoBindList));
// 			$MisAutoBindnewList[]=$formid;
// 			$mdMap['id']=array("not in",array_unique(array_values($MisAutoBindnewList)));
// 		}
		//查询动态表单记录表
		$MisDynamicFormManageList=$MisDynamicFormManageModel->where($mdMap)->getField("id,actiontitle");
		$this->assign("MisDynamicFormManageList",$MisDynamicFormManageList);
		$MisDynamicFormManageVo=$MisDynamicFormManageModel->where("id=".$formid)->find();
		$this->assign("MisDynamicFormManageVo",$MisDynamicFormManageVo);
		$this->assign('formvodata',$bindFormData);
		//查询当前表单绑定数据源
		
		// 查询真实表字段
		$tablename = D ( $_REQUEST['aname'] )->getTableName ();
		$DynamicconfModel = D ( "Dynamicconf" );
		$tablelist = $DynamicconfModel->getTableInfo ( $tablename );
		unset ( $tablelist ['id'] ); // 清除id
		
		foreach ( $tablelist as $tkey => $tval ) {
			$MisDynamicDatabaseSubList [$tval ['COLUMN_NAME']] = $tval ['COLUMN_COMMENT'];
		}
		$this->assign ( "MisDynamicDatabaseSubList", $MisDynamicDatabaseSubList );
		//获取可配置数据源
		$datasoucelist=$this->getDateSoure($actionname);
		$this->assign('datasoucelist',$datasoucelist);
		if( intval($_POST['dwzloadhtml']) ){
			$this->display("dwzloadindex");exit;
		}
		if($_REQUEST['jump']){
			$this->display("indexview");
		}else{
			$this->display();
		}
	}
	public function insert(){
		$MisSystemDataRoamSubDao=M("mis_system_data_roam_sub");
		$MisSystemDataRoamMasDao=M("mis_system_data_roam_mas");
		$MisAutoBindDao=M("mis_auto_bind");
		$inbindaname=$_POST['inbindaname'];
		$modelList=array();
		//先验证一下是否配置重复选项
		foreach($inbindaname as $key=>$val){
			if($modelList[$val]){
				if($_POST['bindval'][$key]==$modelList[$val]){
					$this->error("数据重复,不能同时绑定相同表单且绑定值相同！ ");
				}
			}else{
				$modelList[$val]=$_POST['bindval'][$key];
			}
		}
// 		if (count($inbindaname) != count(array_unique($inbindaname))) {
// 			$this->error("数据重复,请查证后提交！");
// 		}
		
		///////////////////////////////s///////////////////////////////////////////////////////////
		//		事务控制数据操作。by nbmxkj@20150409 1713
		/////////////////////////////////////////////////////////////////////////////////////////
		//	-- 数据抓取
		$inbindaname=$_POST['inbindaname'];
		$pid=0;
		//查询该表除套表外是否还有主节点
		$bindMap=array();
		$bindMap['inbindaname']=$_POST['actionname'];
		$bindMap['typeid']=array("eq",$_POST['typeid']);
		$MisAutoBindVo=$MisAutoBindDao->where($bindMap)->find();
		//查询当前节点是否为顶级节点
		if($MisAutoBindVo){
			//当前有绑定上级
			$pid=getFieldBy($_POST['actionname'],"actionname","id","mis_dynamic_form_manage");
		}
		//查询最高级formid
		$level=getFieldBy($_POST['actionname'],"actionname","id","mis_dynamic_form_manage");
		$endlevel=$level;
		if($pid!=0){
			$level=$this->getFirstLevel($_POST['actionname']);
			$endlevel=getFieldBy($level,"actionname","id","mis_dynamic_form_manage");
		}
		
		
		// 事务控制处理数据处理
		$MisAutoBindDao->startTrans();
		
		//删除原有的绑定数据
		$delresult=$MisAutoBindDao->where("bindaname='".$_POST['actionname']."'")->delete();
		logs('delete : '.$MisAutoBindDao->getLastSql() , 'bindFormLog');
		// 批量添加数据。
		foreach($inbindaname as $key=>$val){
			$dataroamid="";
			$inbindformid=$val;
			$val=getFieldBy($val,"id","actionname","mis_dynamic_form_manage");
			$bindconditionArr = $_POST ['bindcondition'] [$key];
			$inbindconditionArr = $_POST ['inbindcondition'] [$key];
			$bindconlistArr=array();
			foreach ( $bindconditionArr as $akey => $aval ) {
				if ($aval) {
					$bindconlistArr [$aval] = $inbindconditionArr [$akey];
				}
			}
			//储存字段
			$MisAutoBindData=array();
			if($bindconlistArr){
				$MisAutoBindData ['bindconlistArr'] = serialize ( $bindconlistArr );
			}
			$MisAutoBindData['inbackval']=implode(',', $_POST['inbackval'][$val]);
			$MisAutoBindData['backval']=implode(',', $_POST['backval'][$val]);
			$MisAutoBindData['dataroamid']=$_POST['dataroamid'][$key];
			$MisAutoBindData['bindaname']=$_POST['actionname'];
			$MisAutoBindData['inbindaname']=$val;
			$MisAutoBindData['inbindtitle'] = $_POST ['inbindtitle'][$key];
			$MisAutoBindData['inbindsort'] = $_POST ['inbindsort'][$key];
			$MisAutoBindData['bindfield']="bindid"; //绑定字段
			$MisAutoBindData['bindtype']=$_POST['bindtype'][$key];
			$MisAutoBindData['formshowtype']=$_POST['formshowtype'][$key];
			$MisAutoBindData['bindresult']=$_POST['bindresult'];
			$MisAutoBindData['bindval']=$_POST['bindval'][$key];
			$MisAutoBindData['isdelete']=$_POST['isdelete'][$key];
			$MisAutoBindData['typeid']=$_POST['typeid'];
			$MisAutoBindData['inbindformid']=$inbindformid;
			$MisAutoBindData['bindformid']=getFieldBy($_POST['actionname'],"actionname","id","mis_dynamic_form_manage");
			$MisAutoBindData['pid']=$pid;
			$MisAutoBindData['level']=$endlevel;
			//添加附加条件 -by xyz 2016-1-15
				$MisAutoBindData ['bindcondition'] = implode ( ',', $_POST ['bindcondition'] [$key] ); // 主表附加条件
				$MisAutoBindData ['inbindcondition'] = implode ( ',', $_POST ['inbindcondition'] [$key] ); // 子表附加条件
				$MisAutoBindData ['showrules'] = str_replace ( "&#39;", "'", html_entity_decode ( $_POST ['showrules'] [$key] ) );
				$MisAutoBindData ['rulesinfo'] = $_POST ['rulesinfo'] [$key];
				$MisAutoBindData ['inbindmap'] = str_replace ( "&#39;", "'", html_entity_decode ($_POST ['rules'] [$key]));
			$addresult=$MisAutoBindDao->data($MisAutoBindData)->add();
			logs('add : '.$MisAutoBindDao->getLastSql() , 'bindFormLog');
			if(!$addresult)
				break;
		}
		if($delresult && $addresult){
			$MisAutoBindDao->commit();
			$this->success("操作成功！");
		}else{
			$MisAutoBindDao->rollback();
			$this->error("数据添加失败！！！");
		}
		
	}
/**
	 * 
	 * @Title: lookupChangeField
	 * @Description: todo(ajax请求数据表字段)   
	 * @author renling 
	 * @date 2014年12月13日 下午2:59:11 
	 * @throws
	 */
	public function lookupChangeField(){
		
		if($_POST['type']){
			$delinbindname=$_POST['delinbindname'];
			//查询当前要删除的子级是否为绑定表
			if(getFieldBy($delinbindname,"bindaname","inbindaname","mis_auto_bind")){
				echo 1; //有子表信息
			}else{
				echo -1; //无子表信息
			}
		}else{//得到查询的formid
			$formid=$_POST['id'];
			// 获取节点名称
			$actionname = getFieldBy ( $formid, "id", "actionname", "mis_dynamic_form_manage" );
			// 获取表结构
			$tablename = D ( $actionname )->getTableName ();
			$DynamicconfModel = D ( "Dynamicconf" );
			$tablelist = $DynamicconfModel->getTableInfo ( $tablename );
			unset ( $tablelist ['id'] ); // 清除id
			foreach ( $tablelist as $tkey => $tval ) {
				$MisDynamicDatabaseSubList ['list'] [$tval ['COLUMN_NAME']] = $tval ['COLUMN_COMMENT'];
			}
			$MisDynamicDatabaseSubList ['modelname'] = getFieldBy ( $formid, "id", "actionname", "mis_dynamic_form_manage" );
			echo json_encode($MisDynamicDatabaseSubList);
		}
		
	}
	public function getFirstLevel($actionname){
		$MisAutoBindModel=D("MisAutoBind");
		$pFirstAction=getFieldBy($actionname,"bindaname","inbindaname","MisAutoBind","pid",0);
		//查询当前节点的父级
		if(!$pFirstAction){
			$firstaction=getFieldBy($actionname,"inbindaname","bindaname","MisAutoBind");
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

		file_put_contents('c.txt',$targetmodel);
		file_put_contents('a.txt',$model->getlastsql());
		file_put_contents('b.txt',json_encode($list));
		return $list;
	}
	public function delete(){
		$ids = array_values($_POST['bindid']);
		$map['id'] = array("in",$ids);
		if(!empty($map['id'])){
			$model=D($this->getActionName());
			$ret = $model->where($map)->delete();
			if(false === $ret){
				$this->error("删除失败");
			}else{
				$this->success("删除成功");
			}
		}
	}
	
}
