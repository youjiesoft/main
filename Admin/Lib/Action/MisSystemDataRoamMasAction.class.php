<?php
/**
 * @Title: MisSystemDataRoamMasAction 
 * @Package package_name
 * @Description: todo(数据漫游控制器类) 
 * @author谢友志 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015年3月25日 上午11:41:10 
 * @version V1.0
 */
class MisSystemDataRoamMasAction extends CommonAction{
	//动作集
	private $arr = array(
			1=>'新增',
			2=>'修改',
			3=>'删除',
			4=>'单据生成'
	);
	//表类型
	private $tablecategory = array(
				1=>'主表',
				2=>'内嵌表',
				3=>'视图'
			);
	
	//审批字段
	//private $limitfields = array('auditState','ostatus','curAuditUser','alreadyAuditUser','curNodeUser','alreadyauditnode','auditUser','allnode','informpersonid','oprateid','updateid','updatetime','status','bindid');
	private $limitfields = array();
	//系统字段
	private $systemfield = array('companyid','createid','createtime','name','orderno');
	
	public function _filter (&$map ){
		if($_REQUEST['model']){
			$where['sourcemodel']  = array('eq',$_REQUEST['model']);			
			$where['targetmodel']  = array('eq',$_REQUEST['model']);			
			$where['_logic'] = 'or';			
			$map['_complex'] = $where;
			
				
			//$map['sourcemodel']=$_REQUEST['model'];
		}
	}
	public function _before_index(){
		if($_REQUEST['jump']){
			$modelName = $_REQUEST['model'];
			$model = D($modelName);
			$tablename = $model->getTableName();
			$this->assign('modelname',$modelName);
		}else{
			//$newAryRule = $aryRule;/*
			/* 建左边树
			 */
			$model = D('SystemConfigNumber');			
			//$list = $model->getRoleTree('MisDataWanderBox');
			$list = $model->getRoleTreeCache('MisDataWanderBox',$url='__URL__/index',$map2=array (),$modelname=false,$target='ajax',$title='',"MisSystemDataRoamMas",$type='');
			//因为数据要全显示、不再默认高亮（选中）节点
 			$this->assign('returnarr',$list);
			$_REQUEST['model'] = $_REQUEST['model']?$_REQUEST['model']:$_REQUEST['modelname'];
			if($_REQUEST['model']){
				$check = getFieldBy($_REQUEST['model'], 'name', 'id', 'node');
				$this->assign('check',$check);
				$modelName = $_REQUEST['model'];
			}
			$modeltotable = D($firstDetail['name']);
			$tablename=$modeltotable->getTableName();
		}
		$this->assign('id',$tablename);
	}
	
	public function _before_edit(){
		$roamFunction = C("roamFunction");
		$this->assign('functionName',$roamFunction);
	}
	
	public function add(){
		//列表页面初始为全选择，没有确定的来源model，需判断（选择左边末级节点）
		if(!$_REQUEST['modelname']) $this->error("请先选择来源model");
		//目标model
		//获取权限允许的model，直接调用左侧树，再对树做处理
		$model = D('SystemConfigNumber');		
		$list = $model->getRoleTreeCache('MisDataWanderBox',$url='__URL__/index',$map2=array (),$modelname=false,$target='ajax',$title='',"MisSystemDataRoamMas",$type='');
		$list = json_decode($list,true);
		//过滤掉非最末级			(和已使用过的model以及来源model  已释放)
		$newlist = $list;
		$targetmodels = array();
		foreach($newlist as $k=>$v){
			foreach($list as $k1=>$v1){
				if($v['id']==$v1['pId']) unset($newlist[$k]);
			}
		}
		//获取目标model集
		foreach($newlist as $k=>$v){
			$url = explode('/',$v['url']);
			$targetmodels[$k]['modelid'] = $v['id'];
			$targetmodels[$k]['modelname'] =end($url);
			$targetmodels[$k]['modeltitle'] = $v['title'];
			
		}
		
// 		$nodeModel = D("Node");
// 		$nmap['status'] = 1;
// 		$nmap['level'] = 3;
// 		$gModel = D("Group");
// 		$grouplist = $gModel->where("status=1")->getField("id,name");
// 		$nmap['group_id'] = array("in",array_keys($grouplist));
// 		$list = $nodeModel->where($nmap)->getField("id,name,title");
// 		foreach($list as $k=>$v){
// 			$targetmodels[$k]['modelid'] = $v['id'];
// 			$targetmodels[$k]['modelname'] =$v['name'];
// 			$targetmodels[$k]['modeltitle'] = $v['title'];
// 		}
		$this->assign('targetmodels',$targetmodels);
		//操作方式的函数处理数据
		$roamFunction = array(
				array("value"=>"plus","name"=>'增加值'),
				array("value"=>"minus","name"=>'减少值')
				);
		$this->assign('functionName',$roamFunction);
		//来源model
		$modelName = $_REQUEST['modelname']?$_REQUEST['modelname']:$_REQUEST['model'];				
		$this->assign('sourcemodel',$modelName);
		//来源表集合（主表、内嵌表、视图）
		$MisDynamicDatabaseMasList = $this->sourceinfo($modelName);
		$this->assign("MisDynamicDatabaseMasList",$MisDynamicDatabaseMasList);
		$this->display ();
	}
	/**
	 * @Title: sourceinfo
	 * @Description: todo(根据传递的model查找其真实表和其内嵌表、视图集合) 
	 * @param unknown_type $modelName
	 * @param bool $tablecategory 是否需要表类型
	 * @return unknown  
	 * @author 谢友志 
	 * @date 2015-2-14 下午5:17:36 
	 * @throws
	 */
	public function sourceinfo($modelName,$tablecategory=false){
		//来源model主表
		$sourcemodeltalbe = D($modelName)->getTableName();
		$mastable[$sourcemodeltalbe] = getFieldBy($modelName,'name','title','node');
		//查询来源model 内嵌表
		$MisDynamicDatabaseMasList=$this->lookuptablelist($modelName);
		if(!$MisDynamicDatabaseMasList) $MisDynamicDatabaseMasList=array();
		//查询来源model视图（所有视图）
		$viewmodel = M('mis_system_dataview_mas');
		//$viewlist = $viewmodel->field('name,title')->where("modelname='{$modelName}'")->select();
		$viewlist = $viewmodel->field('name,title')->select();
		$viewarr = array();
		foreach($viewlist as $k=>$v){
			if(substr($v['name'],-4)=='View'){
				$viewarr[$v['name']] = $v['title'].'【视图】';
			}
		}
		if($tablecategory){
			if($sourcemodeltalbe){
				$NewMisDynamicDatabaseMasList[$sourcemodeltalbe]['name'] = $sourcemodeltalbe;
				$NewMisDynamicDatabaseMasList[$sourcemodeltalbe]['title'] = $mastable[$sourcemodeltalbe];
				$NewMisDynamicDatabaseMasList[$sourcemodeltalbe]['tablecategory'] = 1;
			}			
			if($MisDynamicDatabaseMasList){
				foreach($MisDynamicDatabaseMasList as $k=>$v){
					$NewMisDynamicDatabaseMasList[$k]['name']=$k;
					$NewMisDynamicDatabaseMasList[$k]['title']=$v;
					$NewMisDynamicDatabaseMasList[$k]['tablecategory']=2;
				}
			}
			if($viewlist){
				foreach($viewlist as $k=>$v){
					$NewMisDynamicDatabaseMasList[$k]['name']=$v['name'];
					$NewMisDynamicDatabaseMasList[$k]['title']=$v['title'];
					$NewMisDynamicDatabaseMasList[$k]['tablecategory']=3;
				}
			}
			return $NewMisDynamicDatabaseMasList;
		}
		//合并来源表所有可能的数据表集合
		$MisDynamicDatabaseMasList = array_merge($mastable,$MisDynamicDatabaseMasList,$viewarr);
		return $MisDynamicDatabaseMasList;
	}
	/**
	 *
	 * @Title: lookuptablelist
	 * @Description: todo(查找模型附属表)
	 * @author renling
	 * @date 2014年12月29日 下午4:29:12
	 * @throws
	 */
	private function lookuptablelist($modelName){
		$formid=getFieldBy($modelName, "actionname", "id", "mis_dynamic_form_manage");
		$MisDynamicDatabaseMasModel=M("mis_dynamic_database_mas");
		$MisDynamicDatabaseMasList=$MisDynamicDatabaseMasModel->where("status=1 and formid=".$formid)->getField("tablename,tabletitle");
		//查询是否存在内嵌表
		$sql="SELECT CONCAT(m.tablename,'_sub_',p.fieldname) AS datatablename ,IF( p.title <> '' OR p.title <> NULL , p.title , p.fieldname) AS title   FROM `mis_dynamic_form_propery` AS p LEFT JOIN mis_dynamic_database_mas AS m ON p.formid=m.formid  WHERE p.formid={$formid} AND p.category = 'datatable' AND m.`isprimary` = 1";
		$MisDatabaseList=$MisDynamicDatabaseMasModel->query($sql);
		if($MisDatabaseList){
			//存在内嵌表
			foreach ($MisDatabaseList as $dkey=>$dval){
				$MisDynamicDatabaseMasList[$dval['datatablename']]=$dval['title'];
			}
		}
		return $MisDynamicDatabaseMasList;
	}
	public function edit(){
		$this->assign('id',$_REQUEST['id']);
		//主表数据(表头数据)
		$model = D($this->getActionName());
		$maslist = $model->where("id=".$_REQUEST['id'])->find();
// 		$sourcetypestr = explode(',',$maslist['sourcetype']);
// 		$this->assign("sourcetypestr",$sourcetypestr);
		$relation = htmlentities($maslist['relation']);
		$this->assign("relation",$relation);
		$comparerelation = htmlentities($maslist['comparerelation']);
		$this->assign("comparerelation",$comparerelation);
		$comparesourcerelation = htmlentities($maslist['comparesourcerelation']);
		$this->assign("comparesourcerelation",$comparesourcerelation);
		//漫游类型
		$roamtypeall = array("普通漫游","套表漫游","子流程漫游");
		$roamtype =$roamtypeall[$maslist['isbindsettable']];
		$this->assign('roamtype',$roamtype);
		
		//内嵌表数据
		//目标model信息
		$targetinfo = $this->changefield($maslist['targetmodel']);
		//$this->assign('targetinfo',$targetinfo);
		//来源表集合(不加载字段信息)
		$sourcearr = $this->sourceinfo($maslist['sourcemodel']);
		$this->assign('sourcearr',$sourcearr);
		$this->assign('sourcearrjson',json_encode($sourcearr));
// 		//来源动作
// 		$sourcetypestr = explode(',',$maslist['sourcetype']);
// 		$this->assign('sourcetypestr',$sourcetypestr);

		//字段子表数据
		$model = D("mis_system_data_roam_sub");
		$sublist = $model->where("masid={$maslist['id']}")->select();
		//漫游对应表
		$sftable = array();
		foreach($sublist as $k=>$v){
			$sftable[$v['targettable']] = $v['sourcetable'];
		}
		$this->assign('sftable',json_encode($sftable));
		
		//漫游来源表字段(被入了子表库的来源字段) 分主表 内嵌表 
		$sourcetablefields = array();
		foreach($sftable as $k=>$v){
			$sourcemastable = D($maslist['sourcemodel'])->getTableName();
			if($v==$sourcemastable){
				$sourcetablefields[$k] = $this->changesourcefield($v,'1',$maslist['sourcemodel']);
			}else{
				$sourcetablefields[$k] = $this->changesourcefield($v,'1');
			}			
		}
		$this->assign('sourcetablefields',$sourcetablefields);
		$dataarr = array();
		foreach($targetinfo as $key=>$val){			
			foreach($val['fields'] as $k=>$v){
				foreach ($sublist as $k1=>$v1){
					if($v['name']==$v1['tfield']&&$v1['targettable']==$key){
						$targetinfo[$key]['fields'][$k]['sublistid'] = $v1['id'];
						$targetinfo[$key]['fields'][$k]['sfield'] = $v1['sfield'];
						$targetinfo[$key]['fields'][$k]['sname'] = $v1['sname'];
						$targetinfo[$key]['fields'][$k]['condo'] = $v1['condo'];
						$targetinfo[$key]['fields'][$k]['conremark'] = $v1['conremark'];
						$targetinfo[$key]['fields'][$k]['expdo'] = $v1['expdo'];
						$targetinfo[$key]['fields'][$k]['tsort'] = $v1['tsort'];
						$targetinfo[$key]['fields'][$k]['expremark'] = $v1['expremark'];
						$targetinfo[$key]['fields'][$k]['sqlcuttomarr'] = $v1['sqlcuttomarr'];
						$targetinfo[$key]['fields'][$k]['deldo'] = $v1['deldo'];
						$targetinfo[$key]['fields'][$k]['delremark'] = $v1['delremark'];
						$targetinfo[$key]['fields'][$k]['delsqlcuttomarr'] = $v1['delsqlcuttomarr'];
						$targetinfo[$key]['fields'][$k]['sourcetable'] = $v1['sourcetable'];
					}
				}
			}
		}
		//多视图关系 （换子表读取）不再从主表读取，换做视图子表读取，原因在于旧的主表视图关系字段结构和现在的不一致 导致解析出现错误
		//dump($maslist);exit;
		
		$dataviewmodel = M("mis_system_data_roam_relation_view");
		$viewrelationlist = $dataviewmodel->where("masid={$maslist['id']}")->select();
		foreach($viewrelationlist as $key=>$val){
			$tfield = $val['vfield'];
			$viewmasid = getFieldBy($val['viewtable'],'name','id','mis_system_dataview_mas');
			$kk = M("mis_system_dataview_sub");
			$dd = $kk->where("masid=".$viewmasid." and field='".$val['vfield']."' and status=1")->find();
			$viewrelationlist[$key]['vfield'] = $dd['otherfield'];//getFieldBy($val['vfield'],'field','otherfiled','mis_system_dataview_sub');
		}
		$maslist['viewrelation'] = json_encode($viewrelationlist);
		//关联关系 (换关联关系子表表读取)
		$relationmodel = M("mis_system_data_roam_relation");
		 $relationdata = $relationmodel->where("masid={$maslist['id']}")->select();
		 $maslist['relation'] = json_encode($relationdata);
		//新规则条件（规则条件子表读取）
		$rulesmodel = M("mis_system_data_roam_relation_rules");
		$rs = $rulesmodel->where("masid=".$_REQUEST['id'])->select();
		foreach($rs as $k=>$v){
			$rs[$k]['sourcetype'] = explode(",",$v['sourcetype']);
			$rs[$k]['rules']=html_entity_decode(str_replace("&#39;","'",$v['rules']));
			$rs[$k]['showrules']=html_entity_decode(str_replace("&#39;","'",$v['showrules']));			
		}
		$this->assign('rulespageinfo',base64_encode(json_encode($rs)));
		$this->assign('targetinfo',$targetinfo);
		if(empty($targetinfo)){
			$targetinfojson = '';
		}else{
			$targetinfojson = json_encode($targetinfo);
		}
		$this->assign('targetinfojson',$targetinfojson);
		
		//print_r(json_encode($targetinfo));exit;
		//操作方式的函数处理数据
		$roamFunction = array(
				array("value"=>"plus","name"=>'增加值'),
				array("value"=>"minus","name"=>'减少值')
		);
		$this->assign('functionName',$roamFunction);
		$this->assign('functionNameJson',json_encode($roamFunction));
		$this->assign("maslist",$maslist);
		//dump($targetinfo);exit;
		$this->display('editview');
	}
	public function insert(){
		if($_POST['targetmodel']=='-1'||$_POST['targetmodel']=='') $this->error("请选择漫游model！");
		$name=$this->getActionName();
		$_POST['sourcename'] = getFieldBy($_POST['sourcemodel'], 'name', 'title','node');
		$_POST['targetname'] = getFieldBy($_POST['targetmodel'], 'name', 'title','node');
		$_POST['relation'] = html_entity_decode($_POST['relation']);
		$_POST['comparerelation']=html_entity_decode($_POST['comparerelation']);
		$_POST['comparesourcerelation']=html_entity_decode($_POST['comparesourcerelation']);
		$_POST['viewrelation'] = html_entity_decode($_POST['viewrelation']);
		$_POST['sourcetype']=implode(',', $_POST['sourcetype']);
		//特殊情况 如果内嵌表是节点 添加issubtable属性
		$specialtargetmodel = D($_POST['targetmodel']);
		$specialtargettable = $specialtargetmodel->getTableName();
		if(substr($specialtargettable,-4)=='_sub'||strpos($specialtargettable,'_sub_')>0){
			$_POST['issubtable'] = 1;
		}
		$sourcetable=D($_POST['sourcemodel'])->getTableName();
		$targettable = D($_POST['targetmodel'])->getTableName();
		//获取来源表与目标表对应关系
		$newsf = array();
		$sf = $_POST['s_f'];
		$i='';
		foreach($sf as $k=>$v){
			if($v){
				$v = explode(',',$v);
				$sftodb[$v[1]]=$v[0];
				//必须匹配来源字段
				if(count($_POST['sfield'][$v[1]])<1){
					$this->error('不能只选择来源表不匹配字段！');
				}
				$newsf[$k]['targettable']['name'] = $v[1];
				if(substr($v[1],-4)=='_sub'||strpos($v[1],'_sub_')>0&& $v[0]!=$targettable){
					$newsf[$k]['targettable']['tablecategory'] = 2;
					$newsf[$k]['targettable']['tablecategoryname'] = '内嵌表';
				}elseif(substr($v[1],-4)=='View'){
					$newsf[$k]['targettable']['tablecategory'] = 3;
					$newsf[$k]['targettable']['tablecategoryname'] = '视图';
				}else{
					$newsf[$k]['targettable']['tablecategory'] = 1;
					$newsf[$k]['targettable']['tablecategoryname'] = '主表';
				}
				$newsf[$k]['sourcetable']['name'] = $v[0];
				if(substr($v[0],-4)=='_sub'||strpos($v[0],'_sub_')>0 && $v[0]!=$sourcetable){
					$newsf[$k]['sourcetable']['tablecategory'] = 2;
					$newsf[$k]['sourcetable']['tablecategoryname'] = '内嵌表';
				}elseif(substr($v[0],-4)=='View'){
					$newsf[$k]['sourcetable']['tablecategory'] = 3;
					$newsf[$k]['sourcetable']['tablecategoryname'] = '视图';
					//来源表有视图是必须有视图关联关系
					if(empty($_POST['viewrelation'])) $this->error("来源表带有视图，请配置视图关联关系！");
				}else{
					$newsf[$k]['sourcetable']['tablecategory'] = 1;
					$newsf[$k]['sourcetable']['tablecategoryname'] = '主表';
				}
				$i=1;
			}
		}
		if($i==''){
			$this->error("请选择来源表！");
		}
		
		$_POST['strelation'] = json_encode($newsf);
		
		$model = D ($name);
// 		//判断是否已经保存
// 		$map['sourcemodel']=$_POST['sourcemodel'];
// 		$map['targetmodel']=$_POST['targetmodel'];
// 		$map['isbindsettable'] = $_POST['isbindsettable'];
// 		$rs = $model->where($map)->select();
// 		if($rs) $this->error('数据已保存');
		$data=$model->create ();
		if (false === $data) {
			$this->error ( $model->getError () );
		}
		//保存当前数据对象
		$list=$model->add ();
		if ($list!==false) {
			$model=D("mis_system_data_roam_sub");
			$dataarr = array();
			/*
			 * 详情表数据拼装
			 */
			foreach($_POST['sfield'] as $key=>$val){
				foreach($val as $k=>$v){
					$dataarr[$k]['sourcetable'] = $sftodb[$key];
					$dataarr[$k]['targettable'] = $key;
					$dataarr[$k]['sfield'] = $v;
					$dataarr[$k]['sname'] = $_POST['sname'][$key][$k];
					$dataarr[$k]['tname'] = $_POST['tname'][$key][$k];
					$dataarr[$k]['tfieldtype'] = $_POST['tfieldtype'][$key][$k];
					$dataarr[$k]['tsort'] = $_POST['tsort'][$key][$k];
					$dataarr[$k]['tfield'] = $_POST['tfield'][$key][$k];
					$dataarr[$k]['condo'] = $_POST['condo'][$key][$k];
					$dataarr[$k]['conremark'] = $_POST['conremark'][$key][$k];
					$dataarr[$k]['expdo'] = $_POST['expdo'][$key][$k];
					$dataarr[$k]['tsort'] = $_POST['tsort'][$key][$k];
					$dataarr[$k]['expremark'] = $_POST['expremark'][$key][$k];
					$dataarr[$k]['sqlcuttomarr'] = html_entity_decode($_POST['sqlcuttomarr'][$key][$k]);
					$dataarr[$k]['deldo'] = $_POST['deldo'][$key][$k];
					$dataarr[$k]['delremark'] = $_POST['delremark'][$key][$k];
					$dataarr[$k]['delsqlcuttomarr'] = html_entity_decode($_POST['delsqlcuttomarr'][$key][$k]);
					$dataarr[$k]['masid'] = $list;
				}
				//判断是否插入	
				foreach($dataarr as $k=>$v){
					if($dataarr[$k]['sfield']){ //丢弃多余判断条件
						$subboole= $model->add($v);
						if($subboole == false){
							$this->error('匹配字段插入失败，请联系管理员');
						}
					}
				}
			}
			/*
			 * 关联关系数据拼装入库
			 */
			if($_POST['relation']){
				$relationmodel = M("mis_system_data_roam_relation");
				$relation = json_decode($_POST['relation'],true);
				foreach ($relation as $k=>$v){
					$tempk = explode(',',$k);
					foreach($v as $key=>$val){
						$relationlist = array();
						$relationlist['sourcetable'] = $tempk[1];
						$relationlist['targettable'] = $tempk[0];
						$relationlist['sfield'] = $val['sfield'];
						$relationlist['tfield'] = $val['tfield'];
						$relationlist['masid'] = $list;
						$relabool = $relationmodel->add($relationlist);
						if($relabool == false){
							$this->error('关联关系入库失败，请联系管理员');
						}
					}
				}
			}
			/*
			 * 目标比较字段 
			 */
			if($_POST['comparerelation']){
				$comparemodel = M("mis_system_data_roam_compare");
				$comparerelation = json_decode($_POST['comparerelation'],true);
				foreach ($comparerelation as $k=>$v){
					$tempk = explode(',',$k);
					foreach($v as $key=>$val){
						$relationlist = array();
						$relationlist['sourcetable'] = $tempk[1];
						$relationlist['targettable'] = $tempk[0];
						$relationlist['tfield'] = $val['tfield'];
						$relationlist['roleinexp'] = $val['roleinexp'];
						$relationlist['compare'] = $val['compare'];
						$relationlist['compareval'] = $val['compareval'];
						$relationlist['operatetype'] = $val['operatetype'];
						$relationlist['msginfo'] = $val['msginfo'];
						$relationlist['masid'] = $list;
						$relabool = $comparemodel->add($relationlist);
						if($relabool == false){
							$this->error('比较字段入库失败，请联系管理员');
						}
					}
				}
			}	
			/**
			 * 本表
			 */
			if($_POST['comparesourcerelation']){
				$comparemodel = M("mis_system_data_roam_compare");
				$comparesourcerelation = json_decode($_POST['comparesourcerelation'],true);
				foreach ($comparesourcerelation as $k=>$v){
					$tempk = explode(',',$k);
					foreach($v as $key=>$val){
						$relationlist = array();
						$relationlist['sourcetable'] = $tempk[1];
						$relationlist['targettable'] = $tempk[0];
						$relationlist['tfield'] = $val['tfield'];
						$relationlist['roleinexp'] = $val['roleinexp'];
						$relationlist['compare'] = $val['compare'];
						$relationlist['compareval'] = $val['compareval'];
						$relationlist['operatetype'] = $val['operatetype'];
						$relationlist['msginfo'] = $val['msginfo'];
						$relationlist['masid'] = $list;
						$relationlist['roamtype'] = 2;//本表
						$relabool = $comparemodel->add($relationlist);
						if($relabool == false){
							$this->error('比较字段入库失败，请联系管理员');
						}
					}
				}
			}		
			/*
			 * 如果存在视图关联关系。则进行视图关联关系入库操作
			 */
			if($_POST['viewrelation']){
				//实例化视图关联关系模型				
				$relationviewmodel = M("mis_system_data_roam_relation_view");
				//获取的$_POST['viewrelation']因为js传值？可能出现html实体，需要实体转换为字符
				$relationview = json_decode(html_entity_decode($_POST['viewrelation']),true);
				foreach($relationview as $k=>$v){
					$v['masid'] = $list;//获取主表id
					$viewmasid = getFieldBy($v['viewtable'],'name','id','mis_system_dataview_mas');
					$viewsubmodel = M("mis_system_dataview_sub");
					$viewfind = $viewsubmodel->where("masid={$viewmasid} and otherfield='{$v["vfield"]}' and status=1")->find();
					if($v["vfield"]&&empty($viewfind)){
						$this->error("{$v['vfield']}该别名未找到对应的字段名！");
					}
					$v['vfield'] = $viewfind['field'];
					$viewbool = $relationviewmodel->add($v);
					if($viewbool == false){
						$this->error('视图关联关系入库失败，请联系管理员');
					}
				}
			}
			//规则条件入库
			$rulespageinfo = json_decode(html_entity_decode(base64_decode($_POST['rulespageinfo'])),true);
			if(count($rulespageinfo)<1){
				$this->error('请填写规则条件，必须要有来源、目标动作');
			}else{
				$rulemodel = M("mis_system_data_roam_relation_rules");
				foreach ($rulespageinfo as $k=>$v){
					if(in_array($v['targettype'],array(2,3))&&$_POST['relation']=='') $this->error("请配置关联关系！");
					$v['sourcemodel'] = $_POST['sourcemodel'];
					$v['masid'] = $list;					
					$v['sourcetype'] = implode(',',$v['sourcetype']);
					$v['rules']=str_replace("&#39;","'",html_entity_decode($v['rules']));
					$v['showrules']=str_replace("&#39;","'",html_entity_decode($v['showrules']));
					$v['endsql']=str_replace("&#39;", "'", html_entity_decode($v['endsql']));
					if(strtolower($v['rules'])=="null") $v['rules']='';
					
					$rulebool = $rulemodel->add($v);
					if($rulebool == false){
						$this->error('规则条件入库失败，请联系管理员');
					}
				}
			}		
			$this->success ( L('_SUCCESS_') ,'',$list);
			exit;
		} else {
			$this->error (L('_ERROR_'));
		}
	
	}
	public function update(){
		if(in_array($_POST['targettype'],array(2,3))&&$_POST['relation']=='') $this->error("请配置关联关系！");
		$id=$_POST['id'];
		if(!$id){
			$this->error("提交信息不存在主键标示，请联系管理员！");
		}
		$where['id'] = $id;
		$model = D ("mis_system_data_roam_mas");
		//对关联关系 规则条件 清除时做处理
		$_POST['rules'] = $_POST['rules']?str_replace("&#39;", "'", html_entity_decode($_POST['rules'])):'';
		$_POST['rulesinfo'] = $_POST['rulesinfo']?$_POST['rulesinfo']:'';
		$_POST['showrules'] = $_POST['showrules']?str_replace("&#39;", "'", html_entity_decode($_POST['showrules'])):'';
		$_POST['showrelation'] = $_POST['showrelation']?$_POST['showrelation']:'';
		//保存当前数据对象
		$_POST['relation'] = html_entity_decode($_POST['relation']);
		$_POST['comparerelation'] = html_entity_decode($_POST['comparerelation']);
		$_POST['comparesourcerelation'] = html_entity_decode($_POST['comparesourcerelation']);
		//保存当前视图数据对象
		$_POST['showviewrelation'] = $_POST['showviewrelation']?$_POST['showviewrelation']:'';
		$_POST['viewrelation'] = html_entity_decode($_POST['viewrelation']);
		//特殊情况 如果内嵌表是节点 添加issubtable属性
		$specialtargetmodel = D($_POST['targetmodel']);
		$specialtargettable = $specialtargetmodel->getTableName();
		if(substr($specialtargettable,-4)=='_sub'||strpos($specialtargettable,'_sub_')>0){
			$_POST['issubtable'] = 1;
		}
		$sourcetable=D($_POST['sourcemodel'])->getTableName();
		$targettable = D($_POST['targetmodel'])->getTableName();
		//获取来源表与目标表对应关系
		$newsf = array();
		$sf = $_POST['s_f'];
		$i = 0;
		foreach($sf as $k=>$v){
			if($v){
				$v = explode(',',$v);
				$sftodb[$v[1]]=$v[0];
				//必须匹配来源字段
				if(count($_POST['sfield'][$v[1]])<1){
					$this->error('漫游来源字段必须！');
				}
				$newsf[$k]['targettable']['name'] = $v[1];
				if(substr($v[1],-4)=='_sub'||strpos($v[1],'_sub_')>0&& $v[0]!=$targettable){
					$newsf[$k]['targettable']['tablecategory'] = 2;
					$newsf[$k]['targettable']['tablecategoryname'] = '内嵌表';
				}elseif(substr($v[1],-4)=='View'){
					$newsf[$k]['targettable']['tablecategory'] = 3;
					$newsf[$k]['targettable']['tablecategoryname'] = '视图';
				}else{
					$newsf[$k]['targettable']['tablecategory'] = 1;
					$newsf[$k]['targettable']['tablecategoryname'] = '主表';
				}
				$newsf[$k]['sourcetable']['name'] = $v[0];
				if(substr($v[0],-4)=='_sub'||strpos($v[0],'_sub_')>0 && $v[0]!=$sourcetable){
					$newsf[$k]['sourcetable']['tablecategory'] = 2;
					$newsf[$k]['sourcetable']['tablecategoryname'] = '内嵌表';
				}elseif(substr($v[0],-4)=='View'){
					$newsf[$k]['sourcetable']['tablecategory'] = 3;
					$newsf[$k]['sourcetable']['tablecategoryname'] = '视图';
					//来源表有视图是必须有视图关联关系
					$viewrelation = json_decode($_POST['viewrelation'],true);
					if(count($viewrelation) < 1){
						$this->error("来源表带有视图，请配置视图关联关系！");
					}
				}else{
					$newsf[$k]['sourcetable']['tablecategory'] = 1;
					$newsf[$k]['sourcetable']['tablecategoryname'] = '主表';
				}
				$i = 1;
			}
		}
		if($i<1){
			$this->error("请选择来源表！");
		}
		
// 		foreach($sf as $k=>$v){
// 			if($v){
// 				$v = explode(',',$v);
// 				$newsf[$v[1]]=$v[0];
// 			}
// 		}
		//内嵌表是否单行插入
		$_POST['onlyoneinsert'] = $_POST['onlyoneinsert']?$_POST['onlyoneinsert']:'';
		$_POST['strelation'] = json_encode($newsf);
		if (false === $data = $model->create ()) {
			$this->error ( $model->getError () );
		}
		$data['sourcetype'] = implode(',',$_POST['sourcetype']);
		$list=$model->where($where)->save($data);
		//入库
		if ($list!==false) {
			//内嵌表数据入库
			$model=D("mis_system_data_roam_sub");
			/*
			 * 此处是对数据库进行真删除，在PHP后端是无法控制产生的结果
			 * 检查问题方向1：必须满足POST提交的数据完整，删除条件标示唯一。
			 * 检查问题方向2：漫游都存在内嵌表，内嵌表检索后form标签内不存在完整的数据提交，导致这里删除后，重新保存的数据字段不完整
			 */
			$model->where("masid=".$id)->delete();
			//logs($model->getLastSql(),'delProperty');
			//组合入库数据
			foreach($_POST['sfield'] as $key=>$val){
				$dataarr = array();
				foreach($val as $k=>$v){
					$dataarr[$k]['sourcetable'] = $sftodb[$key];
					$dataarr[$k]['targettable'] = $key;
					$dataarr[$k]['sfield'] = $v;
					$dataarr[$k]['sname'] = $_POST['sname'][$key][$k];
					$dataarr[$k]['tname'] = $_POST['tname'][$key][$k];
					$dataarr[$k]['tfieldtype'] = $_POST['tfieldtype'][$key][$k];
					$dataarr[$k]['tsort'] = $_POST['tsort'][$key][$k];
					$dataarr[$k]['tfield'] = $_POST['tfield'][$key][$k];
					$dataarr[$k]['condo'] = $_POST['condo'][$key][$k];
					$dataarr[$k]['conremark'] = $_POST['conremark'][$key][$k];
					$dataarr[$k]['expdo'] = $_POST['expdo'][$key][$k];
					$dataarr[$k]['tsort'] = $_POST['tsort'][$key][$k];
					$dataarr[$k]['expremark'] = html_entity_decode($_POST['expremark'][$key][$k]);
					$dataarr[$k]['sqlcuttomarr'] = html_entity_decode($_POST['sqlcuttomarr'][$key][$k]);
					$dataarr[$k]['deldo'] = $_POST['deldo'][$key][$k];
					$dataarr[$k]['delremark'] = $_POST['delremark'][$key][$k];
					$dataarr[$k]['delsqlcuttomarr'] = html_entity_decode($_POST['delsqlcuttomarr'][$key][$k]);
					$dataarr[$k]['masid'] = $id;
				}
				//判断是否插入	
				foreach($dataarr as $k=>$v){
					if($dataarr[$k]['sfield']){ //丢弃多余判断条件
						$subboole = $model->add($v);
						if($subboole == false){
							logs($model->getLastSql() ,'errorInsertMis_system_data_roam_sub');
							$this->error('匹配字段插入失败，请联系管理员');
						}
					}
				}
			}
			//关联关系入库
			$relationmodel = M("mis_system_data_roam_relation");
			/*
			 * 此处是对数据库进行真删除，在PHP后端是无法控制产生的结果
			 * 检查问题方向1：必须满足POST提交的数据完整，删除条件标示唯一。
			 * 检查问题方向2：漫游都存在内嵌表，内嵌表检索后form标签内不存在完整的数据提交，导致这里删除后，重新保存的数据字段不完整
			 */
			$relationmodel->where("masid=".$id)->delete();
			if($_POST['relation']){
				$relation = json_decode($_POST['relation'],true);
				foreach ($relation as $k=>$v){
					$tempk = explode(',',$k);
					foreach($v as $key=>$val){
						$relationlist = array();
						$relationlist['sourcetable'] = $tempk[1];
						$relationlist['targettable'] = $tempk[0];
						$relationlist['sfield'] = $val['sfield'];
						$relationlist['tfield'] = $val['tfield'];
						$relationlist['masid'] = $id;
						$relabool = $relationmodel->add($relationlist);
						if($relabool == false){
							logs($relationmodel->getLastSql() ,'errorInsertMis_system_data_roam_relation');
							$this->error('关联关系入库失败，请联系管理员');
						}
					}
				}
			}	
			//比较关系入库
			$comparerelationmodel = M("mis_system_data_roam_compare");
			/*
			 * 此处是对数据库进行真删除，在PHP后端是无法控制产生的结果
			 * 检查问题方向1：必须满足POST提交的数据完整，删除条件标示唯一。
			 * 检查问题方向2：漫游都存在内嵌表，内嵌表检索后form标签内不存在完整的数据提交，导致这里删除后，重新保存的数据字段不完整
			*/
			$comparerelationmodel->where("roamtype=1 and  masid=".$id)->delete();
			if($_POST['comparerelation']){
				$comparerelation = json_decode($_POST['comparerelation'],true);
				foreach ($comparerelation as $k=>$v){
					$tempk = explode(',',$k);
					foreach($v as $key=>$val){
						$relationlist=array();
						$relationlist['sourcetable'] = $tempk[1];
						$relationlist['targettable'] = $tempk[0];
						$relationlist['tfield'] = $val['tfield'];
						$relationlist['roleinexp'] = $val['roleinexp'];
						$relationlist['compare'] = $val['compare'];
						$relationlist['compareval'] = $val['compareval'];
						$relationlist['operatetype'] = $val['operatetype'];
						$relationlist['msginfo'] = $val['msginfo'];
						$relationlist['masid'] = $id;
						$relabool = $comparerelationmodel->add($relationlist);
						if($relabool == false){
							logs($comparerelationmodel->getLastSql() ,'errorInsertMis_system_data_roam_relation');
							$this->error('比较字段入库失败，请联系管理员');
						}
					}
				}
			}		
			/*
			 * 此处是对数据库进行真删除，在PHP后端是无法控制产生的结果
			 * 检查问题方向1：必须满足POST提交的数据完整，删除条件标示唯一。
			 * 检查问题方向2：漫游都存在内嵌表，内嵌表检索后form标签内不存在完整的数据提交，导致这里删除后，重新保存的数据字段不完整
			*/
			$comparerelationmodel->where("roamtype=2 and  masid=".$id)->delete();
			if($_POST['comparesourcerelation']){
				$comparerelation = json_decode($_POST['comparesourcerelation'],true);
				foreach ($comparerelation as $k=>$v){
					$tempk = explode(',',$k);
					foreach($v as $key=>$val){
						$relationlist=array();
						$relationlist['sourcetable'] = $tempk[1];
						$relationlist['targettable'] = $tempk[0];
						$relationlist['tfield'] = $val['tfield'];
						$relationlist['roleinexp'] = $val['roleinexp'];
						$relationlist['compare'] = $val['compare'];
						$relationlist['compareval'] = $val['compareval'];
						$relationlist['operatetype'] = $val['operatetype'];
						$relationlist['msginfo'] = $val['msginfo'];
						$relationlist['masid'] = $id;
						$relationlist['roamtype'] = 2;
						$relabool = $comparerelationmodel->add($relationlist);
						if($relabool == false){
							logs($comparerelationmodel->getLastSql() ,'errorInsertMis_system_data_roam_relation');
							$this->error('比较字段入库失败，请联系管理员');
						}
					}
				}
			}
			
			//视图关系入库
			$relationviewmodel = M("mis_system_data_roam_relation_view");
			/*
			 * 此处是对数据库进行真删除，在PHP后端是无法控制产生的结果
			 * 检查问题方向1：必须满足POST提交的数据完整，删除条件标示唯一。
			 * 检查问题方向2：漫游都存在内嵌表，内嵌表检索后form标签内不存在完整的数据提交，导致这里删除后，重新保存的数据字段不完整
			 */
			$relationviewmodel->where("masid=".$id)->delete();
			if($_POST['viewrelation']){
				$relationview = json_decode(html_entity_decode($_POST['viewrelation']),true);
				foreach($relationview as $k=>$v){
					if(!$v['sfield']||!$v['vfield']){
						$this->error("视图关系配置失败，请重新配置");
					}
					$v['masid'] = $id;
					$viewmasid = getFieldBy($v['viewtable'],'name','id','mis_system_dataview_mas');
					$viewsubmodel = M("mis_system_dataview_sub");
					$viewfind = $viewsubmodel->where("masid={$viewmasid} and otherfield='{$v["vfield"]}'  and status=1")->find();
					if($v["vfield"]&&empty($viewfind['field'])){
						$this->error("{$v['vfield']}该别名未找到对应的字段名！");
					}					
					$v['vfield'] = $viewfind['field'];				
					$viewbool = $relationviewmodel->add($v);
					if($viewbool == false){
						$this->error('视图关联关系入库失败，请联系管理员');
					}
				}
			}
			//规则条件入库
			$rulespageinfo = json_decode(html_entity_decode(base64_decode($_POST['rulespageinfo'])),true);
			//print_r($rulespageinfo);exit;
			if(count($rulespageinfo)<1){
				$this->error('请填写规则条件，必须要有来源、目标动作');
			}else{
				$rulemodel = M("mis_system_data_roam_relation_rules");
				/*
				 * 此处是对数据库进行真删除，在PHP后端是无法控制产生的结果
				 * 检查问题方向1：必须满足POST提交的数据完整，删除条件标示唯一。
				 * 检查问题方向2：漫游都存在内嵌表，内嵌表检索后form标签内不存在完整的数据提交，导致这里删除后，重新保存的数据字段不完整
				 */
				$rulemodel->where("masid=".$id)->delete();			
				foreach ($rulespageinfo as $k=>$v){
					if(in_array($v['targettype'],array(2,3))&&$_POST['relation']=='') $this->error("来源动作为修改、删除时必须配置关联关系！");
					$v['sourcemodel'] = $_POST['sourcemodel'];
					$v['masid'] = $id;
					$v['sourcetype'] = implode(',',$v['sourcetype']);
					$v['rulesinfo'] = $v['rulesinfo']?$v['rulesinfo']:'';
					$v['rules']=$v['rules']&&strtolower($v['rules'])!="null"?str_replace("&#39;", "'", html_entity_decode($v['rules'])):'';
					$v['showrules']=$v['showrules']&&strtolower($v['showrules'])!="null"?str_replace("&#39;", "'", html_entity_decode($v['showrules'])):'';
					$v['endsql']=$v['endsql']&&$v['endsql']!='null'?str_replace("&#39;", "'", html_entity_decode($v['endsql'])):'';
					$rulebool = $rulemodel->add($v);
					if($rulebool == false){
						$this->error('规则条件入库失败，请联系管理员');
					}
				}
			}			
			$this->success ( L('_SUCCESS_') ,'',$list);
		} else {
			$this->error ( L('_ERROR_') );
		}
	}
	/**
	 * @Title: _before_delete
	 * @Description: todo(当主表真删时，删除所有对应的子表数据)   
	 * @author 谢友志 
	 * @date 2015-4-12 下午5:03:27 
	 * @throws
	 */
	public function _before_delete(){
		$id = $_REQUEST['id'];
		M("mis_system_data_roam_sub")->where("masid=".$id)->delete();
		M("mis_system_data_roam_relation")->where("masid=".$id)->delete();
		M("mis_system_data_roam_relation_rules")->where("masid=".$id)->delete();
		M("mis_system_data_roam_relation_view")->where("masid=".$id)->delete();
		M("mis_system_data_roam_compare")->where("masid=".$id)->delete();
		
		
	}
	/**
	 * @Title: getTableList
	 * @Description: todo(最新取值方式，平配合页面显示重新命名key值) 
	 * @param unknown_type $tablename
	 * @return unknown  
	 * @author 谢友志 
	 * @date 2015-4-14 下午2:32:44 
	 * @throws
	 */
	private function getTableList($tablename,$modelname){

		$roamMasModel = D($this->getActionName());
		$tablelist = $roamMasModel->getTableFieldForRoam($tablename,$modelname);
		//echo $roamMasModel->getLastsql();
		foreach($tablelist as $tk=>$tv){
			$newlist[$tk]['name']=$tv['field'];
			$newlist[$tk]['showname']=$tv['title'];
			$newlist[$tk]['fieldtype']=$tv['type'];
		}
		return $newlist;
	}
	/**
	 * @Title: changefield
	 * @Description: todo(获取目标model的主表和内嵌表信息) 
	 * @param string $targetmodel 目标model 用于编辑时  不传参则用于新增改变目标model下拉框时
	 * @return unknown  
	 * @author 谢友志 
	 * @date 2015-2-12 下午7:55:41 
	 * @throws
	 */
	public function changefield($targetmodel){
		//目标model
		if($targetmodel){
			$modelName=$targetmodel;
		}else{
			$modelName = $_POST['modelname'];
		}		
		//目标model主表
		$targetmodeltalbe = D($modelName)->getTableName();
		//查询目标model 内嵌表
		$inLineTableList = array();
		$inLineTableList=$this->lookuptablelist($modelName);
		//
//		$DynamicconfModel=D("Dynamicconf");
		//主表字段
		$targetmasfields = array();
		$targetmasfields[$targetmodeltalbe]['target_tag'] = '主表';
		$targetmasfields[$targetmodeltalbe]['target_title'] = getFieldBy($modelName,'name','title','node');
		$mastablelist = $this->getTableList('',$modelName);
		foreach($mastablelist as $mk=>$mv){
			$targetmasfields[$targetmodeltalbe]['fields'][$mk]['name']=$mv['name'];
			$targetmasfields[$targetmodeltalbe]['fields'][$mk]['showname']=$mv['showname'];
			$targetmasfields[$targetmodeltalbe]['fields'][$mk]['fieldtype']=$mv['fieldtype'];
		}
		//内嵌表字段
		$targetsubfields = array();
		if($inLineTableList){
			foreach($inLineTableList as $k=>$v){
				if($k==$targetmodeltalbe) continue;
				$targetsubfields[$k]['target_tag'] = '内嵌表';
				$targetsubfields[$k]['target_title'] = $v;
//				$subtablelist=$DynamicconfModel->getTableInfo($k);
				$subtablelist = $this->getTableList($k);
				foreach($subtablelist as $sk=>$sv){
					$targetsubfields[$k]['fields'][$sk]['name']=$sv['name'];
					$targetsubfields[$k]['fields'][$sk]['showname']=$sv['showname'];
					$targetsubfields[$k]['fields'][$sk]['fieldtype']=$sv['fieldtype'];
				}
			}			
		}		
		//数据组合传递
		$arr = array_merge($targetmasfields,$targetsubfields);
		if($targetmodel){
			return $arr;
		}else{
			exit(json_encode($arr));
		}		
	}
	/**
	 * @Title: changesourcefield
	 * @Description: todo(获取来源表字段,本php页面调用 和 html页面js调用) + 判断是否是主表，若是，则通过modelname查询  
	 * @author 谢友志 
	 * @date 2015-2-13 上午11:02:14 
	 * @throws
	 */
	public function changesourcefield($sourcetable='',$isarr=false,$modelname=''){
		//如果是本页面调用 传入$sourcetable 如果是html页面js调用 获取$_POST['sourcetable']
		$sourcetable = $sourcetable?$sourcetable:$_POST['sourcetable'];
		$newlist = array();
		//获取当前model中的真实表
//		$DynamicconfModel=D("Dynamicconf");
		if($modelname){
			$newlist=$this->getTableList('',$modelname);
		}elseif($sourcetable&&substr($sourcetable,-4)=="View"){
			$id = getFieldBy($sourcetable,'name','id','mis_system_dataview_mas');
			$viewmodel = M("mis_system_dataview_sub");
			$list = $viewmodel->field('otherfield,title')->where("status=1 and masid='{$id}'")->select();
			foreach($list as $k=>$v){
				$newlist[$v['otherfield']]['name'] = $v['otherfield'];
				$newlist[$v['otherfield']]['showname'] = $v['title']?$v['title']:$v['otherfield'];
				$newlist[$v['otherfield']]['fieldtype'] = '';
			}
		}elseif($sourcetable){
			//已经选择了来源表名称
			$newlist=$this->getTableList($sourcetable);
		}
		if($isarr){
			return ($newlist);
		}else{
			echo json_encode($newlist);
		}
	}
	public function lookupDataViewRoamRaletion(){
		//$_POST['viewrelation']如果有值，说明要带数据进入页面
		$viewrelation = json_decode(html_entity_decode($_POST['viewrelation']),true);
		$this->assign('viewrelation',$viewrelation);
		
		//来源视图
		$sourceview = explode(',',$_POST['sourceview']);
		$viewnamelist=array();
		foreach($sourceview as $k=>$v){
			$viewmodelname = getFieldBy($v,'name','modelname','mis_system_dataview_mas');
			//查询该视图配置文件
			$path=DConfig_PATH . "/Models/".$viewmodelname."/".$v.".inc.php";
			$detailArr = require $path;
			foreach ($detailArr as $dkey=>$dval){
				$viewnamelist[$v][]=array("value"=>$dval['name'],"name"=>$dval['name']."【".$dval['showname']."】");
			}
		}
		//来源真实表
		$sourcemodel = D($_POST['sourcemodel']);
		$sourceName = getFieldBy($_POST['sourcemodel'],"name","title","node");
		$sourcetable = $sourcemodel->getTableName();
// 		$DynamicconfModel=D("Dynamicconf");
// 		$sourcelist = $DynamicconfModel->getTableInfo($sourcetable);
		$sourcelist = $this->getTableList('',$_POST['sourcemodel']);
		foreach ($sourcelist as $tk=>$tv){
			$sourcefields[] = array("value"=>$tv['name'],"name"=>$tv['name']."【".$tv['showname']."】");
		}
		//来源目标对应关系 去掉不是视图的来源表
		$sf = explode(';',$_POST['sf']);
		$newsf = array();//来源目标对应关系
		$targetlist = array();//目标表字段
		foreach($sf as $k=>$v){
			$v = explode(',',$v);
			if(substr($v[0],-4)=='View'){
				$newsf[$v[1]] = $v[0];
			}
		}
		$this->assign('source_data',$sourcefields);
		$this->assign('source_view',$viewnamelist);
		$this->assign('newsf',$newsf);
		$this->assign('newsfjson',json_encode($newsf));
		$this->assign('targetmodel',$_POST['targetmodel']);
		$this->assign('sourcemodel',$_POST['sourcemodel']);
		$this->assign('sourceName',$sourceName);
		$this->assign('sourcetable',$sourcetable);
		$this->display();
			
	}
	public function lookupDataInsertViewRoamRaletion(){
		$data = $_POST;
		//获取来源表全字段中文名
		$sourcetable = $data['sourcetable'];
// 		$DynamicconfModel=D("Dynamicconf");
// 		$sourcelist = $DynamicconfModel->getTableInfo($sourcetable);
		$sourcelist = $this->getTableList('',$data['sourcemodel']);
		foreach ($sourcelist as $tk=>$tv){
			$sourceinfo[$tk]=$tv['name'].'【'.$tv['showname'].'】';			
		}
		//传递过来的数据
		$jsonarr = array();
		$str = array();
		foreach($data['data']['sourcefield'] as $key=>$val){
			//视图字段中文名
			$viewname = $key;
			$id = getFieldBy($viewname,'name','id','mis_system_dataview_mas');
			$viewmodel = M("mis_system_dataview_sub");
			$viewlist = $viewmodel->field('otherfield,title')->where("status=1 and masid='{$id}'")->select();
			foreach($viewlist as $wk=>$wv){
				$viewinfo[$wv['otherfield']] = $wv['otherfield'].'【'.$wv['title'].'】';
			}
			//
			foreach($val as $k=>$v){
				$list = array();
				$viewfield = $data['data']['viewfield'][$key][$k];
				if($v&&$viewfield){
					$list['sourcetable'] = $sourcetable;
					$list['viewtable'] = $key;
					$list['sfield'] = $v;
					$list['vfield'] = $viewfield;	
					//组合数据的json存值
					$jsonarr[] = $list;
					$str[] = $viewinfo[$viewfield] ." = ".$sourceinfo[$v];
				}
				
			}
			
		}
		$string = implode(" AND ",$str);
		//因为你本身数据库存储用了2维数组，所以我这里也匹配你数据库格式
		//$arr_a[] = $arr;
		$json_arr['vo'] = json_encode($jsonarr);
		$json_arr['show'] = $string;
		//if($arr_a){
		$this->success("添加成功",'',$json_arr);
	}
	/**
	 * 
	 * @Title: lookupDataRoamCompareSourceRaletion
	 * @Description: todo(本表字段比较)   
	 * @author renling 
	 * @date 2015年5月14日 上午11:26:26 
	 * @throws
	 */
	public function lookupDataRoamSourceRaletion(){
		//来源表
// 		$sourcemodel = $_POST['sourcemodel'];
// 		$tablename=D($sourcemodel)->getTableName();
		//来源表
		$targetinfo = $this->changefield($_POST['sourcemodel']);
		//数据表中文字段
		$fieldsAll = array();
		foreach($targetinfo as $k=>$v){
			$fieldsAll[$k] = $this->changesourcefield($k,1);
		}
// 		$fieldsAll = $this->changesourcefield($tablename,'1');
// 		print_r($targetinfo);
		//重组表字段用于datatabl下拉框
		foreach($targetinfo as $k=>$v){
			foreach($v['fields'] as $key=>$val){
				if($val['name']!="action" && $val['name'] !='auditState'){
					$fields_data[$k][]=array("value"=>$val['name'],"name"=>$val['name']."【".$val['showname']."】【".$val['fieldtype']."】");
				}
			}
		}
// 		print_r($fields_data);
		//数据表中文  开始未考虑到展示中文、这里暂时补上，有空可以优化 （得从add edit开始传递过来）
		$sourcemodel = $_POST['sourcemodel'];
		$targetmodel = $_POST['targetmodel'];
		$target = $this->changefield($targetmodel);
		$source = $this->sourceinfo($sourcemodel);
		$this->assign('target',$target);
		$this->assign('source',$source);
		$this->assign("tablename",$tablename);
		$this->assign('fields_data',$fields_data);
		$this->assign('tableMatchup',array_keys($targetinfo));
		$this->assign('fieldsAll',$fieldsAll);
		$relation = json_decode(html_entity_decode($_POST['relation']),true);
		$relationlist = array();
		foreach($relation as $k=>$v){
			$tempr = explode(',',$k);
			$relationlist[$tempr[0]] = $v;
		}
		$this->assign('relation',$relationlist);
		$this->display();
		exit;
	}
	/**
	 * 
	 * @Title: lookupInsertDataRoamSourceCompare
	 * @Description: todo(本表比较字段保存)   
	 * @author renling 
	 * @date 2015年5月14日 上午11:38:52 
	 * @throws
	 */
	public function lookupInsertDataRoamSourceCompare(){
		//对应字段处理
		$data = $_POST['data'];
		foreach ($data['mymodel'] as $k1=>$v1){
			$temp = array();
			foreach($v1 as $key=>$val){
				if($val){
					//组合数据的json存值
					$temp[] = array(
							'tfield'=>$val,
							'roleinexp'=>$data['roleinexp'][$k1][$key],
							'compare'=>$data['compare'][$k1][$key],
							'compareval'=>$data['compareval'][$k1][$key],
							'operatetype'=> $data['operatetype'][$k1][$key],
							'msginfo'=>$data['info'][$k1][$key],
					);
				}
		
			}
			$jsonarr[$k1.','.$k1]=$temp;
		}
		$arr_a['vo'] = json_encode($jsonarr); //用于传值到数据库
		if($arr_a){
			$this->success("添加成功",'',$arr_a);
		}
		exit;
	}
	public function lookupDataRoamCompare(){
		//数据源对象
		//tableMatchup 对应表 目标->来源
		$tableMatchup = array();
		//tablesall 关系到的所有数据表
		$tablesAll = array();
		foreach($_POST['tablesarr'] as $k=>$v){
			if($v){
				$temp = explode(',',$v);
				if($temp[0]&&$temp[1]){
					$tableMatchup[$temp[1]]=$temp[0];
					$tablesAll = array_merge($tablesAll,$temp);
				}else{
					$this->error('数据表对应关系有问题！');
				}
				
			}
		}
		$tablesAll = array_unique($tablesAll);
		//所有表字段
		$fieldsAll = array();
		foreach($tablesAll as $k=>$v){
			$fieldsAll[$v] = $this->changesourcefield($v,'1');
		}
		//重组表字段用于datatabl下拉框
		foreach($fieldsAll as $k=>$v){
			foreach($v as $key=>$val){
				if($val['name']!="action" && $val['name'] !='auditState'){
					$fields_data[$k][]=array("value"=>$val['name'],"name"=>$val['name']."【".$val['showname']."】【".$val['fieldtype']."】");
				}
			}
		}
		//数据表中文  开始未考虑到展示中文、这里暂时补上，有空可以优化 （得从add edit开始传递过来）
		$sourcemodel = $_POST['sourcemodel'];
		$targetmodel = $_POST['targetmodel'];
		$target = $this->changefield($targetmodel);
		$source = $this->sourceinfo($sourcemodel);
		$this->assign('target',$target);
		$this->assign('source',$source);
		$this->assign('fields_data',$fields_data);
		$this->assign('tableMatchup',$tableMatchup);
		$this->assign('fieldsAll',$fieldsAll);
		$relation = json_decode(html_entity_decode($_POST['relation']),true);
		$relationlist = array();
		foreach($relation as $k=>$v){
			$tempr = explode(',',$k);
			$relationlist[$tempr[0]] = $v;
		}
		$this->assign('relation',$relationlist);
		$this->display();
		exit;
		
	}
	public function lookupInsertDataRoamCompare(){
		//回来的数据表集合
		$tableMatchup = $_POST['tableMatchup'];
		$tableAll = array();
		foreach($tableMatchup as $k=>$v){
			$tableAll[] = $k;
			$tableAll[] = $v;
		}
		$tableAll = array_unique($tableAll);
		//数据表中文字段
		$fieldsAll = array();
		foreach($tableAll as $k=>$v){
			$fieldsAll[$v] = $this->changesourcefield($v,1);
		}
		//对应字段处理
		$data = $_POST['data'];
		foreach ($data['mymodel'] as $k1=>$v1){
			$temp = array();
			foreach($v1 as $key=>$val){
				if($val){
					$sourcekey = $tableMatchup[$k1];
					//组合数据的json存值
					$temp[] = array(
							'tfield'=>$val,
							'roleinexp'=>$data['roleinexp'][$k1][$key],
							'compare'=>$data['compare'][$k1][$key],
							'compareval'=>$data['compareval'][$k1][$key],
							'operatetype'=> $data['operatetype'][$k1][$key],
							'msginfo'=>$data['info'][$k1][$key],
					);
				}
		
			}
			$jsonarr[$k1.','.$sourcekey]=$temp;
		}
		$arr_a['vo'] = json_encode($jsonarr); //用于传值到数据库
		if($arr_a){
			$this->success("添加成功",'',$arr_a);
		}
		exit;
		
	}
	public function lookupDataRoamRaletion(){
		//数据源对象
		//tableMatchup 对应表 目标->来源
		$tableMatchup = array();
		//tablesall 关系到的所有数据表
		$tablesAll = array();
		foreach($_POST['tablesarr'] as $k=>$v){
			if($v){
				$temp = explode(',',$v);
				if($temp[0]&&$temp[1]){
					$tableMatchup[$temp[1]]=$temp[0];
					$tablesAll = array_merge($tablesAll,$temp);
				}else{
					$this->error('数据表对应关系有问题！');
				}
				
			}
		}
		$tablesAll = array_unique($tablesAll);
		//所有表字段
// 		$fieldsAll = array();
// 		foreach($tablesAll as $k=>$v){
// 			$fieldsAll[$v] = $this->changesourcefield($v,'1');
// 		}
		$fieldsAll = array();
		$sourcemastable = D($_POST['sourcemodel'])->getTableName();
		$targetmastable = D($_POST['targetmodel'])->getTableName();
		foreach($tablesAll as $k=>$v){
			if($v==$sourcemastable){
				$fieldsAll[$v] = $this->changesourcefield('','1',$_POST['sourcemodel']);
			}elseif($v==$targetmastable){
				$fieldsAll[$v] = $this->changesourcefield('','1',$_POST['targetmodel']);
			}else{
				$fieldsAll[$v] = $this->changesourcefield($v,'1');
			}
		}
		//重组表字段用于datatabl下拉框
		foreach($fieldsAll as $k=>$v){
			foreach($v as $key=>$val){
				if($val['name']!="action" && $val['name'] !='auditState'){
					$fields_data[$k][]=array("value"=>$val['name'],"name"=>$val['name']."【".$val['showname']."】【".$val['fieldtype']."】");
				}
			}
		}
		//数据表中文  开始未考虑到展示中文、这里暂时补上，有空可以优化 （得从add edit开始传递过来）
		$sourcemodel = $_POST['sourcemodel'];
		$targetmodel = $_POST['targetmodel'];
		$target = $this->changefield($targetmodel);
		$source = $this->sourceinfo($sourcemodel);
		$this->assign('target',$target);
		$this->assign('source',$source);
		$this->assign('fields_data',$fields_data);
		$this->assign('tableMatchup',$tableMatchup);
		$this->assign('fieldsAll',$fieldsAll);
		$relation = json_decode(html_entity_decode($_POST['relation']),true);
		$relationlist = array();
		foreach($relation as $k=>$v){
			$tempr = explode(',',$k);
			$relationlist[$tempr[0]] = $v;
		}
		$this->assign('relation',$relationlist);
		$this->display();
		exit;
	}
	
	public function lookupInsertDataRoamReltion(){
		//回来的数据表集合
		$tableMatchup = $_POST['tableMatchup'];
		$tableAll = array();
		foreach($tableMatchup as $k=>$v){
			$tableAll[] = $k;
			$tableAll[] = $v;
		}
		$tableAll = array_unique($tableAll);
		//数据表中文字段		
		$fieldsAll = array(); 
		$sourcemastable = D($_POST['sourcemodel'])->getTableName();
		foreach($tableAll as $k=>$v){
			if($v==$sourcemastable){
				$fieldsAll[$v] = $this->changesourcefield('','1',$maslist['sourcemodel']);
			}else{
				$fieldsAll[$v] = $this->changesourcefield($v,'1');
			}
		}
		//对应字段处理
		$data = $_POST['data'];
		foreach ($data['mymodel'] as $k1=>$v1){
			$temp = array();
			foreach($v1 as $key=>$val){	
				if($val && $data['sourcemodel'][$k1][$key]){
					$sourcefield = $data['sourcemodel'][$k1][$key];
					$sourcekey = $tableMatchup[$k1];
					$vname = $val."【".$fieldsAll[$sourcekey][$sourcefield]['showname']."】";
					$v2name = $sourcefield."【".$fieldsAll[$k1][$val]['showname']."】";
					//组合数据的json存值
					$temp[] = array('sfield'=>$fieldsAll[$sourcekey][$sourcefield]['name'],'tfield'=>$val);
					$str[] = $vname ." = ".$v2name;
				}
		
			}
			$jsonarr[$k1.','.$sourcekey]=$temp;
		}
		$string = implode(" AND ",$str);
		$arr_a['vo'] = json_encode($jsonarr); //用于传值到数据库
		$arr_a['show'] = $string; //页面展示
		if($arr_a){
			$this->success("添加成功",'',$arr_a);
		}
		exit;
		
		
		//实例化配置模型
		$configmodel = D('SystemConfigDetail');
		//数据源对象
		$sourcemodel = $_REQUEST['sourcemodel']; //来源
		$tablename=D($sourcemodel)->getTableName();
		$list=$this->getTableList('',$sourcemodel);
		//漫游数据源字段数组
		$mymodel = $_REQUEST['mymodel']; //目标
		$mytablename=D($mymodel)->getTableName();
		$mylist=$this->getTableList('',$mymodel);
		//获取提交过来的关联数据
		$data = $_POST['data'];
		$jsonarr = array();
		$str = "";		
		foreach ($data['sourcemodel'] as $key=>$val){
				if($val && $data['mymodel'][$key]){
					foreach($list as $k=>$v){
						if($v['name'] == $val){
							$vname = $val."【".$v['showname']."】";
						}
					}
					foreach($mylist as $k2=>$v2){
						if($v2['name'] == $data['mymodel'][$key]){
							$v2name = $data['mymodel'][$key]."【".$v2['showname']."】";
						}
					}
					//组合数据的json存值
					$jsonarr[] = array('sfield'=>$val,'tfield'=>$data['mymodel'][$key]);
					$str[] = $vname ." = ".$v2name;
			}
		}	
		$string = implode(" AND ",$str);
		//因为你本身数据库存储用了2维数组，所以我这里也匹配你数据库格式
		$arr_a[] = $arr;
		$arr_a['vo'] = json_encode($jsonarr);
		$arr_a['show'] = $string;
		if($arr_a){
			$this->success("添加成功",'',$json_arr);
		}
		
		
	}
	public function _after_list(&$voList){
		$arr = $this->arr;
		foreach($voList as $k=>$v){
			$targettypelist = explode(',',$v['sourcetype']);
			$val = '';
			foreach($arr as $k1=>$v1){
				foreach($targettypelist as $k2=>$v2){
					if($k1 == $v2){
						$val .= $val?','.$v1:$v1;
					}
				}
			}
			$voList[$k]['sourcetype']=$val;
		}
	}
	/**
	 * @Title: getTableInfolimit
	 * @Description: todo(表字段获取的过渡方法，对字段进行过滤) 
	 * @param unknown_type $tablename
	 * @return unknown  
	 * @author 谢友志 
	 * @date 2015-3-12 下午2:51:30 
	 * @throws
	 */
	function getTableInfolimit($tablename){
		$DynamicconfModel=D("Dynamicconf");
		$mytablelist=$DynamicconfModel->getTableInfo($tablename);
		foreach($mytablelist as $tk=>$tv){
			if(in_array($tv['COLUMN_NAME'],$this->limitfields)) unset($mytablelist[$tk]);
		}
		return($mytablelist);
	}
	
	/**
	 * @Title: initdb
	 * @Description: todo(老数据处理，将子表的targettable和sourcetable字段为空的补上)   
	 * @author 谢友志 
	 * @date 2015-2-28 下午2:32:32 
	 * @throws
	 */
	public function initdb(){
		$model = D($this->getActionName());
		$list = $model->select();
		$submodel = M('mis_system_data_roam_sub');
		foreach($list as $k=>$v){
			$sublist = $submodel->where('masid='.$v['id'])->find();
			if(empty($sublist['targettable'])&&$sublist){
				if($v['targettable']&&$v['targettable']!=-1){
					$alter['targettable'] = $v['targettable'];
				}else{
					$targettablemodel = D($v['targetmodel']);
					$alter['targettable'] = $targettablemodel->getTableName();
				}
				if($v['sourcetable']&&$v['sourcetable']!=-1){
					$alter['sourcetable'] = $v['sourcetable'];
				}else{
					$sourcetablemodel = D($v['sourcemodel']);
					$alter['sourcetable'] = $sourcetablemodel->getTableName();
				}
				$map['masid'] = array('eq',$v['id']);
				$num = $submodel->where($map)->save($alter);
			}
		}
		$this->success();
	}
	/**
	 * @Title: initstrelation
	 * @Description: todo(老数据的strelation字段为空的处理，仅对以前的单对单)   
	 * @author 谢友志 
	 * @date 2015-2-28 下午1:58:16 
	 * @throws
	 */
	public function initstrelation(){
		$model=D($this->getActionName());
		$list = $model->select();
		foreach($list as $k=>$v){
			//对strelation字段为空的记录做处理
			if($v['strelation']==''){
				$newsf = array();
				//目标表
				if($v['targettable']&&$v['targettable']!=-1){
					$targettable = $v['targettable'];
				}else{
					$targettablemodel = D($v['targetmodel']);
					$targettable = $targettablemodel->getTableName();
				}
				//检查目标表类型
				$newsf[0]['targettable']['name'] = $targettable;
				if(substr($targettable,-4)=='_sub'||strpos($targettable,'_sub_')>0){
					$newsf[0]['targettable']['tablecategory'] = 2;
					$newsf[0]['targettable']['tablecategoryname'] = '内嵌表';
				}elseif(substr($targettable,-4)=='View'){
					$newsf[0]['targettable']['tablecategory'] = 3;
					$newsf[0]['targettable']['tablecategoryname'] = '视图';
				}else{
					$newsf[0]['targettable']['tablecategory'] = 1;
					$newsf[0]['targettable']['tablecategoryname'] = '主表';
				}
				//来源表
				if($v['sourcetable']&&$v['sourcetable']!=-1){
					$sourcetable = $v['sourcetable'];
				}else{
					$sourcetablemodel = D($v['sourcemodel']);
					$sourcetable = $sourcetablemodel->getTableName();
				}
				//检查来源表类型
				$newsf[0]['sourcetable']['name'] = $sourcetable;
				if(substr($sourcetable,-4)=='_sub'||strpos($sourcetable,'_sub_')>0){
					$newsf[0]['sourcetable']['tablecategory'] = 2;
					$newsf[0]['sourcetable']['tablecategoryname'] = '内嵌表';
				}elseif(substr($sourcetable,-4)=='View'){
					$newsf[0]['sourcetable']['tablecategory'] = 3;
					$newsf[0]['sourcetable']['tablecategoryname'] = '视图';
				}else{
					$newsf[0]['sourcetable']['tablecategory'] = 1;
					$newsf[0]['sourcetable']['tablecategoryname'] = '主表';
				}
				$save['strelation'] = json_encode($newsf);
				//修改数据
				$model->where('id='.$v['id'])->save($save);																
			}
		}
		$this->success();
	}
	/**
	 * @Title: initstsub
	 * @Description: todo(将老数据的关联关系放到关联关系表中，视图关联关系放到视图关联关系表中)   
	 * @author 谢友志 
	 * @date 2015-2-28 下午6:15:47 
	 * @throws
	 */
	public function initrelationtable(){
		$model=D($this->getActionName());
		$list = $model->select();
		$relationmodel = M("mis_system_data_roam_relation");
		$viewmodel = M("mis_system_data_roam_relation_view");
		$submodel = M("");
		foreach($list as $k=>$v){
			//关联关系 主表2中情况 单表单 多表单 ；关联关系表2中情况，一种已经有，一种没有
			$relationlist = $relationmodel->where("masid=".$v['id'])->find();
			$relation = array();
			if(empty($relationlist)){
				if($v['relation']){
					$relation = json_decode($v['relation'],true);
					foreach($relation as $k1=>$v1){
						$data = array();
						if(is_int($k1)){
							$strelation = json_decode($v['strelation'],true);	
							$data['sourcetable'] = $strelation[0]['sourcetable']['name'];
							$data['targettable'] = $strelation[0]['targettable']['name'];
							$data['sfield'] = $v1['sfield'];
							$data['tfield'] = $v1['tfield'];
							$data['masid'] = $v['id'];
							//$relationmodel->add($data);
 						}
					}
				}
			}
			//视图关联关系
			$viewlist = $viewmodel->where("masid=".$v['id'])->find();
			$view = array();
			if(empty($viewlist)){
				if($v['viewrelation']){
					$view = json_decode($v['viewrelation'],true);
					foreach($view as $k2=>$v2){
						$data = array();
						if(is_int($k2)){							
							$data['viewtable'] = $v['sourcetable'];							
							$data['sourcetable'] = D($v['sourcemodel'])->getTableName();
							$data['vfield'] = $v2['tfield'];
							$data['sfield'] = $v2['sfield'];
							$data['masid'] = $v['id'];
							$viewmodel->add($data);
						}
					}
				}
			}
		}
		$this->success();
	}
	/**
	 * @Title: initrules
	 * @Description: todo(规则条件整理,包含了来源目标动作)   
	 * @author 谢友志 
	 * @date 2015-3-10 上午9:06:35 
	 * @throws
	 */
	function initrules(){
		$masmodel = D($this->getActionName());
		$list = $masmodel->select();
		$submodel = M("mis_system_data_roam_relation_rules");
		foreach($list as $k=>$v){
			$rs = $submodel->where("masid=".$v['id'])->find();
			if(empty($rs)){
				$data['sourcemodel'] = $v['sourcemodel'];
				$data['masid']=$v['id'];
				$data['targettype']=$v['targettype'];
				$data['sourcetype']=$v['sourcetype'];
				$data['rules']=$v['rules'];
				$data['rulesinfo']=$v['rulesinfo'];
				$data['showrules']=$v['showrules'];
				$submodel->add($data);
			}
		}
		$this->success();
	}
	/**
	 * @Title: openRulesPage
	 * @Description: todo(打开规则条件编辑页面)   
	 * @author 谢友志 
	 * @date 2015-3-12 下午2:47:13 
	 * @throws
	 */
	function openRulesPage(){
		$modelname = $_POST['sourcemodel'];
		$tablename = D($modelname)->getTableName();
		$datatableModel = M("mis_dynamic_form_datatable");
		$formid = $datatableModel->where("tablename='{$tablename}'")->getField("formid");
		if($formid){
			$modelname = getFieldBy($formid,'id',"actionname","mis_dynamic_form_manage");
			$inlinetable = $tablename;
		}
		$this->assign("modelname",$modelname);
		$this->assign("inlinetable",$inlinetable);
		$this->assign("multitype",$_POST['multitype']);
		$rulespageinfo = json_decode(base64_decode($_POST['rulespageinfo']),true);
		$rulespageinfo['sourcetype'] = explode(',',$rulespageinfo);
		foreach($rulespageinfo as $k=>$v){
			if(empty($v)) unset($rulespageinfo[$k]);
		}
		$this->assign("rulespageinfo",$rulespageinfo);
		$this->display();
	}
	/**
	 * @Title: roamrules
	 * @Description: todo(关闭规则条件页面是对数据做处理)   
	 * @author 谢友志 
	 * @date 2015-3-12 下午2:47:03 
	 * @throws
	 */
	function roamrules(){
		$data = $_POST;
		$i=0;
		$newdata = array();
		foreach($data['sourcetype'] as $k=>$v){
			$newdata[$i]['sourcetype']=$v;//implode(",",$v);
			$newdata[$i]['targettype']=$data['targettype'][$k];
			//$newdata[$i]['showrules']=$data['showrules'][$i];
			
			$newdata[$i]['rulesinfo']=$data['rulesinfo'][$i];
			//$newdata[$i]['rules']=$data['rules'][$i];

			$newdata[$i]['rules']=str_replace("&#39;", "'", html_entity_decode($data['rules'][$i]));
			$newdata[$i]['showrules']=str_replace("&#39;", "'", html_entity_decode($data['showrules'][$i]));
			$newdata[$i]['endsql'] = str_replace("&#39;", "'", html_entity_decode($data['endsql'][$i]));
			$i++;
		}
		foreach($newdata as $k=>$v){
			if(empty($v)) unset($newdata[$k]);
		}
		//$newdata['order'] = $data['order'];
		$this->success("添加成功",'',base64_encode(json_encode($newdata)));
	}
	/**
	 * @Title: lookupSqlCuttom
	 * @Description: todo(自定义Sql操作)   
	 * @author 谢友志 
	 * @date 2015-3-14 下午6:21:21 
	 * @throws
	 */
	public function lookupSqlCuttom(){
		$this->assign("objname",$_POST['objname']);
		$sqlcuttomarr = html_entity_decode($_POST['sqlcuttomarr']);
		$sqlcuttomarr = json_decode($sqlcuttomarr,true);
		$this->assign("sqlcuttomarr",$sqlcuttomarr);
		//所有真实表
		$model = M();
		$allTables = $model->query("SELECT `TABLE_NAME` AS name,`TABLE_COMMENT` AS showname FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '".C('DB_NAME')."'");
		$newTables = array();
		foreach($allTables as $k=>$v){
			$newTables[$v['name']]=$v['showname'];
		}
		//查询来源model视图（所有视图）
		$viewmodel = M('mis_system_dataview_mas');
		//$viewlist = $viewmodel->field('name,title')->where("modelname='{$modelName}'")->select();
		$viewlist = $viewmodel->field('name,title')->select();
		$viewarr = array();
		foreach($viewlist as $k=>$v){
			if(substr($v['name'],-4)=='View'){
				$viewarr[$v['name']] = $v['title'].'【视图】';
			}
		}
		$tablefields = $this->changesourcefield($sqlcuttomarr['sqltable'],1);
		$this->assign('tablefields',$tablefields);
		$newTables = array_merge($viewarr,$newTables);
		$this->assign('newTables',$newTables);
		$this->display();
	}
	public function lookupSqlCuttomInsert(){
		
		$data['sqltable'] = $_POST['sqltable'];
		$data['sqlmatchingfield'] = $_POST['sqlmatchingfield'];
		$data['sqlbringbackfield'] = $_POST['sqlbringbackfield'];
		$data['sqlcondition'] = $_POST['sqlcondition'];
		if(!$data['sqlmatchingfield']||!$data['sqlbringbackfield']) $this->error("请先配置字段");
		$condition = $data['sqlcondition']?" AND ".$data['sqlcondition']:"";
		$sql = "SELECT `".$data["sqlbringbackfield"]."` FROM `".$data["sqltable"]."` WHERE `".$data['sqlmatchingfield']."`=### ".$condition." ORDER BY `id` DESC";
		$back["sqlstr"] = $sql;
		$back["data"] =json_encode($data);
		$this->success('操作成功','',json_encode($back));
	}
}