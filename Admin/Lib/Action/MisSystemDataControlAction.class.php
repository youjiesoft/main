<?php
/**
 * @Title: MisSystemDataControlAction 
 * @Package package_name
 * @Description: todo(数据控制控制器类) 
 * @author谢友志 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015年3月25日 上午11:41:10 
 * @version V1.0
 */
class MisSystemDataControlAction extends CommonAction{
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
			$map['modelname']  = array('eq',$_REQUEST['model']);		
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
			$list = $model->getRoleTree('MisSystemDataControl');
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
	
	
	
	public function add(){
		//来源model
		if(!$_REQUEST['modelname']) $this->error("请先控制模型");
		$this->assign('sourcemodel',$_REQUEST['modelname']);
		//获取对象选择的数据
		$this->objchoise($_REQUEST['modelname']);
		//来源主表
		$sourcetable = D($_REQUEST['modelname'])->getTableName();
		$firstsqlselectform = $this->sqlselectform($sourcetable);
		$this->assign("firstsqlselectform",$firstsqlselectform);
		$this->assign("sourcetable",$sourcetable);
		$this->display ();
	}
	public function edit(){
		
		$id = $_REQUEST['id'];
		$masmodel = D($this->getActionName());
		$submodel = M("mis_system_data_control_sub");
		//主表数据
		$maslist = $masmodel->where("id={$id}")->find();
		//子表数据
		$sublist = $submodel->where("masid={$id}")->select();
		foreach($sublist as $key=>$val){			
			$sublist[$key]['operation'] = explode(",",$val['operation']);
			if($val['choicetablesforrole']){
				$sublist[$key]['choicetablesforrolearr'] = unserialize(base64_decode($val['choicetablesforrole']));
			}
			$sublist[$key]['choicetablesforrolearr'][$maslist['modelname']][0]=$val['objtable'];
		}
		//print_r($sublist);
		//对象数据
		$this->objchoise($maslist['modelname']);
		
		$sourcetable = D($maslist['modelname'])->getTableName();
		
		$this->assign('maslist',$maslist);
		$this->assign("sublist",$sublist);
		//print_r($sublist);	
		$this->display();
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
		$index = strpos($_POST['inlinetable'],"_sub_");
		if($index>0){
			$data = W('ShowAddResult' , array('model'=>$_POST['model'],'multitype'=>'multi','inlinetable'=>$_POST['inlinetable']));
		}else{
			$data = W('ShowAddResult' , array('model'=>$_POST['model'],'multitype'=>'multi'));
		}
		
		echo $data;
	}
	public function _after_insert($list){
		$data = $_POST;
		$submodel = M("mis_system_data_control_sub"); 
		//print_r($data);
		if($data['sql']){
			foreach($data['sql'] as $key=>$val){
				//仅允许有验证规则的入库
				$temp = array();
				$temp['masid'] = $list;
				$temp['objtable'] = $data['objtable'][$key];
				$temp['operation'] = implode(",",$data['operation'][$key]);
				$temp['roamtype'] = $data['roamtype'][$key];
				$temp['rules'] = $data['rules'][$key]?str_replace("&#39;", "'", html_entity_decode($data['rules'][$key])):'';
				$temp['rulesinfo'] = $data['rulesinfo'][$key];
				$temp['showrules'] = $data['showrules'][$key]?str_replace("&#39;", "'", html_entity_decode($data['showrules'][$key])):'';
				$temp['treenode'] = $data['treenode'][$key];
				$temp['showchoicetables'] = $data['choicetables'][$key];
				$temp['choicetablesforrole'] = $data['choicetablesforrole'][$key];
				$temp['msginfo'] = $data['msginfo'][$key]?str_replace("&#39;", "'", html_entity_decode($data['msginfo'][$key])):'';
				$temp['sql'] = $val?str_replace("&#39;", "'", html_entity_decode($val)):'';
				$temp['sqlselectform'] = $data['sqlselectform'][$key];
				$temp['sqlselectformarr'] = $data['sqlselectformarr'][$key];
				if($val&&$temp['operation']){
					$ret = $submodel->add($temp);
					// echo $submodel->getlastsql();
					if(false === $ret){
						$this->error("入库失败");
					}	
				}elseif(!$temp['operation']){
					$this->error("有一行的动作未指明");
				}
			}
		}else{
			$this->error("请配置相应的验证规则");
		}
		//echo "+++++++++";exit;
	}
	
	public function _before_update(){
		$data = $_POST;
		
		
// 		$massavedata['id'] = $data['masid'];
// 		$massavedata['name'] = $data['name'];
// 		$masmodel = D($this->getActionName());
// 		$masret = $masmodel->save($massavedata);
// 		if(false === $masret){
// 			$this->error("表头数据入库失败");
// 		}
		$submodel = M("mis_system_data_control_sub");
		//删除掉被删除的数据
		$delmap['id'] = array("not in",$_POST['id']);
		$delmap['masid'] = $data['masid'];
		$delret = $submodel -> where($delmap)->delete();
		if(false === $delret){
			$this->error("删除多余数据失败");
		}
		//print_r($data);
		if($data['sql']){
			foreach($data['sql'] as $key=>$val){
				//仅允许有验证规则的入库
				$temp = array();
				$temp['masid'] = $data['masid'];
				$temp['objtable'] = $data['objtable'][$key];
				$temp['operation'] = implode(",",$data['operation'][$key]);
				$temp['roamtype'] = $data['roamtype'][$key];
				$temp['rules'] = $data['rules'][$key]?str_replace("&#39;", "'", html_entity_decode($data['rules'][$key])):'';
				$temp['rulesinfo'] = $data['rulesinfo'][$key];
				$temp['showrules'] = $data['showrules'][$key]?str_replace("&#39;", "'", html_entity_decode($data['showrules'][$key])):'';
				$temp['treenode'] = $data['treenode'][$key];
				$temp['showchoicetables'] = $data['choicetables'][$key];
				$temp['choicetablesforrole'] = $data['choicetablesforrole'][$key];
				$temp['msginfo'] = $data['msginfo'][$key]?str_replace("&#39;", "'", html_entity_decode($data['msginfo'][$key])):'';
				$temp['sql'] = $val?str_replace("&#39;", "'", html_entity_decode($val)):'';
				$temp['sqlselectform'] = $data['sqlselectform'][$key];
				$temp['sqlselectformarr'] = $data['sqlselectformarr'][$key];
				if($data['id'][$key]){
					if($val&&$temp['operation']){
						$temp['id'] = $data['id'][$key];
						$ret = $submodel->save($temp);
						//echo $submodel->getlastsql();
						if(false === $ret){
							$this->error("入库失败");
						}
					}elseif(!$temp['operation']){
						$this->error("有一行的动作未指明");
					}
				}else{
					if($val&&$temp['operation']){
						$ret = $submodel->add($temp);
						//echo $submodel->getlastsql();
						if(false === $ret){
							$this->error("入库失败");
						}
					}elseif(!$temp['operation']){
						$this->error("有一行的动作未指明");
					}
				}
				
			}
		}else{
			$this->error("请配置相应的验证规则");
		}
		$_POST['id']=$data['masid'];
	}
	/**
	 * @Title: _before_delete
	 * @Description: todo(当主表真删时，删除所有对应的子表数据)   
	 * @author 谢友志 
	 * @date 2015-4-12 下午5:03:27 
	 * @throws
	 */
	public function _before_delete(){
		
		
	}
	/**
	 * @Title: objchoise
	 * @Description: todo(模型对象) 
	 * @param unknown_type $sourcemodel  
	 * @author 谢友志 
	 * @date 2015-5-27 下午4:04:43 
	 * @throws
	 */
	private function objchoise($sourcemodel){
		
	//------选择对象（本模型） -----开始------需要动态传入来源模型名称
		//来源model主表
		$sourcemodeltalbe = D($sourcemodel)->getTableName();
		$this->assign('sourcemodeltable',$sourcemodeltalbe);
		$mastable[$sourcemodeltalbe] = getFieldBy($sourcemodel,'name','title','node');
		//查询来源model 内嵌表
		$MisDynamicDatabaseMasList=$this->lookuptablelist($sourcemodel);
		if(!$MisDynamicDatabaseMasList) $MisDynamicDatabaseMasList=array();
		//合并来源表数据表集合
			$selftable = array_merge($mastable,$MisDynamicDatabaseMasList);
			$this->assign("selftable",$selftable);
	//------选择对象（本模型） -----开始------
		
	}
	/**
	 * @Title: mainAndInlinetable
	 * @Description: todo(查询模型下的所有数据表--主表、内嵌表) (传入model时返回数组，接收model表示是ajax传值处理 返回json字符串)
	 * @param unknown_type $model  
	 * @author 谢友志 
	 * @date 2015-5-27 下午4:14:09 
	 * @throws
	 */
	public function mainAndInlinetable($model){
		$model = $model?$model:$_REQUEST['model'];
// 		$maintablename = D($model)->getTableName();
// 		$maintable[$maintablename] = getFieldBy($maintablename,'name','title','node');
		
		$formid=getFieldBy($model, "actionname", "id", "mis_dynamic_form_manage");
		$MisDynamicDatabaseMasModel=M("mis_dynamic_database_mas");
		$maintable=$MisDynamicDatabaseMasModel->where("status=1 and formid=".$formid)->getField("tablename,tabletitle");
		
		$inlinetable = $this->lookuptablelist($model);
		$list = array_merge($maintable,$inlinetable);
		if($_REQUEST['model']){
			$list = json_encode($list);
			echo $list;
		}else{
			return $list;
		}
		
	}
	/**
	 * @Title: choicetables
	 * @Description: todo(控制数据对象选择 )   
	 * @author 谢友志 
	 * @date 2015-7-7 上午11:15:34 
	 * @throws
	 */
	public function choicetables(){
		$data=$_POST;
		//根据传递的模型获取主子表
		$sourcetables = $this->mainAndInlinetable($data['sourcemodel']);
		//获取动态建模建的所有模型 和其对应的主、子表
		$model=D($this->getActionName());
		$newdata = $model->getAutoAllActionAndTable();
		$list = array(); 
		//树节点集
		$newdepartmentlist = array();
		//组合树节点数据
		foreach($newdata as $key=>$val){
			$list[$key]['actionname'] = $val['actionname'];
			$list[$key]['actiontitle'] = $val['actiontitle'];
			$list[$key]['tablename'][] = $val['mastablename'];
			$list[$key]['tabletitle'][] = $val['mastabletitle'];
			$newdepartmentlist[]=array(//模型
						'id'=>$key,
						'name'=>$val['actiontitle'],
						'tablename'=>$val['mastablename'],
						'parentid'=>0,					
						
				);
// 			$newdepartmentlist[]=array(//主表
// 					'id'=>-$key,
// 					'name'=>$val['mastabletitle'],
// 					'parentid'=>$key,
			
// 			);
			if($val['sublist']){//子表
				foreach($val['sublist'] as $k=>$v){
					$newdepartmentlist[]=array(
							'id'=>-$v['propertyid'],
							'name'=>$v['subtabletitle'],
							'tablename'=>$v['subtablename'],
							'parentid'=>$key,
								
					);
					$list[$key]['tablename'][] = $v['subtablename'];
					$list[$key]['tabletitle'][] = $v['subtabletitle'];
				}
				
			}
		}
		
		$param['url']= "#";
		$param['rel']= "";
		$param['open']= "true";
		$param['isParent']="true";
		$arr = $this->getTree($newdepartmentlist,$param);
		//已选择的树节点
		$table = explode(',',$data['tables']); 
		//已选择的树节点 配置的关系
		$sqlselectformarr = unserialize(base64_decode($data['sqlselectformarr']));
		//print_r($sqlselectformarr);
		//如果有已选节点，在节点集里加入checked属性
		if($table){
			$decodearr = json_decode($arr,true);
			foreach($decodearr as $key=>$val){
				if(in_array($val['id'],$table)){
					$decodearr[$key]['checked']=true;
					$decodearr[$key]['objid']=$sqlselectformarr[$val['id']]['objid'];
					$decodearr[$key]['relationlink']=$sqlselectformarr[$val['id']]['relationlink'];
					$decodearr[$key]['relationcont']=$sqlselectformarr[$val['id']]['relationcont'];
				}
			}
			//print_r($decodearr);
			$arr = json_encode($decodearr);
		}
		
 		$this->assign("list",$arr);
 		$this->assign("decodelist",json_decode($arr,true));
 		//print_r($decodearr);
 		$this->assign("modelname",$data['sourcemodel']);
 		$this->assign("sourcetable",$data['sourcetable']);
 		$this->assign("tag",$data['tag']);
 		$this->assign('treenode',$data['tables']);
 		$this->display();
	}
	/**
	 * @Title: ztreeValDo
	 * @Description: todo(控制数据对象选择 数据处理,龚云要求 ：如果选择了子表 则主表默认选中,可单选主表，父级为主表、子集为子表)   
	 * @author 谢友志 
	 * @date 2015-7-8 下午5:06:08 
	 * @throws
	 */
	public function ztreeValDo(){
		$data = $_POST;
		//获取所有的模型和表信息(模型中英文名称，主、子表中英文名称)
		$model=D($this->getActionName());
		//查询所有表单对应字段
		$proModel = M("mis_dynamic_form_propery");
		$datatableModel = M("mis_dynamic_form_datatable");
		$dyModel = D("Dynamicconf");
		$ModelAndTableList = $model->getAutoAllActionAndTable();
		//print_r($ModelAndTableList);
	
		$list = array();//选择的树节点信息(模型名称、表名称、表字段、连接符、连接关系)
		$wdata = array();//用于规则条件的参数（array($modelname=>array($table1,$table2,...))）
		//获取选中的树节点（模型+表）
		$treenode = $data['treenode']?explode(",",$data['treenode']):array();//explode分隔空字符串产生的数组会产生一个空元素
		if($treenode){
			foreach($treenode as $key=>$val){
				//$mastableid = '';//---》主表节点缓存，来解析主表数据。每次循环都必须重置
				$subtableid = '';//---》子表节点缓存，来解析子表数据。每次循环都必须重置
				if($val>0){//模型
					$modelformid = $val; //---》模型节点缓存，来解析主、子表数据。所以不能每次循环都重置，只能是到下个模型时替换
				}elseif($val<0){//主表
					$subtableid = $val;
				}
				$map['formid'] = $datamap['formid'] = $modelformid;
				$map['dbname'] = '';
				$fields = array();
				if(!$subtableid){
					$list[$modelformid]['actionname'] = $ModelAndTableList[$modelformid]['actionname'];
					$list[$modelformid]['actiontitle'] = $ModelAndTableList[$modelformid]['actiontitle'];
					$list[$modelformid]['table'][$modelformid]['tablename'] = $ModelAndTableList[$modelformid]['mastablename'];
					$list[$modelformid]['table'][$modelformid]['tabletitle'] = $ModelAndTableList[$modelformid]['mastabletitle'];
					$list[$modelformid]['table'][$modelformid]['tag'] = 'mas';
					$map['dbname'] = $ModelAndTableList[$modelformid]['mastablename'];
					$map['status'] = 1;
					
					$fields = $dyModel->getTableInfo($ModelAndTableList[$modelformid]['mastablename']);
					$list[$modelformid]['table'][$modelformid]['fields'] = array_keys($fields);
					foreach ($data['objid'] as $k=>$v){
						if($val == $v){
							$list[$modelformid]['table'][$modelformid]['relationlink'] = $data['relationlink'][$k]; //连接符
							$list[$modelformid]['table'][$modelformid]['relationcont'] = $data['relationcont'][$k]; //连接关系
						}
					}
					$wdata[$ModelAndTableList[$modelformid]['actionname']][] = $ModelAndTableList[$modelformid]['mastablename'];
				}else{
					$list[$modelformid]['actionname'] = $ModelAndTableList[$modelformid]['actionname'];
					$list[$modelformid]['actiontitle'] = $ModelAndTableList[$modelformid]['actiontitle'];
					$list[$modelformid]['table'][$subtableid]['tablename'] = $ModelAndTableList[$modelformid]['sublist'][-$subtableid]['subtablename'];
					$list[$modelformid]['table'][$subtableid]['tabletitle'] = $ModelAndTableList[$modelformid]['sublist'][-$subtableid]['subtabletitle'];
					$list[$modelformid]['table'][$subtableid]['tag'] = 'sub';
					$datamap['tablename'] = $ModelAndTableList[$modelformid]['mastablename'];
					$fields = $dyModel->getTableInfo($ModelAndTableList[$modelformid]['sublist'][-$subtableid]['subtablename']);
					$list[$modelformid]['table'][$subtableid]['fields'] = array_keys($fields);
					if ($data['objid'][$modelformid]){
						$list[$modelformid]['table'][$subtableid]['relationlink'] = $data['relationlink'][$k];
						$list[$modelformid]['table'][$subtableid]['relationcont'] = $data['relationcont'][$k];
							
					}
					$wdata[$ModelAndTableList[$modelformid]['actionname']][] = $ModelAndTableList[$modelformid]['sublist'][-$subtableid]['subtablename'];
				}
					
			}
		}
	
		//关系处理、生成sql代码
		$sourcetable = $data['sourcetable'];
		//$sourceformid = getFieldBy($data['modelname'],'actionname','id','mis_dynamic_form_manage','status',1);
		//$sourcefields = $proModel->where("status=1 and formid={$sourceformid} and dbname='{$sourcetable}'" )->getField("id,fieldname");
		$sourcefieldsarr = $dyModel->getTableInfo($sourcetable);
		$sourcefields = array_keys($sourcefieldsarr);
		
		$select = "SELECT "; 						//sql查询语句的select部分
		$from = " FROM `{$data['sourcetable']}` "; 	//sql查询语句的from部分
		$china = '';								//中文表名
		foreach($sourcefields as $key=>$val){
			$select .= "`".$data['sourcetable']."`.`".$val."` AS '".$data['sourcetable'].".".$val."',";
		}
		foreach ($list as $key=>$val){
			$china2 = '';
			foreach($val['table'] as $k=>$v){
				$china .= $china?"|".$v['tabletitle']:$v['tabletitle'];
				//关系处理
				$from .= " ".$v['relationlink']." `".$v['tablename']."` ON ".$v['relationcont']." ";
				foreach($v['fields'] as $k1=>$v1){
					$select .= "`".$v['tablename']."`.`".$v1."` AS '".$v['tablename'].".".$v1."',";
				}
			}
		}
		//用于回写连接关系的数组
		$sqlselectformarr = array();
		foreach($data['objid'] as $key=>$val){
			$temp['objid'] = $val;
			$temp['relationlink'] = $data['relationlink'][$key];
			$temp['relationcont'] = $data['relationcont'][$key];
			$sqlselectformarr[$key]=$temp;
			unset($temp);
		}
		$select = substr($select,0,-1); //去掉最后一个逗号
		$sqlselectform = $select.$from; //sql查询语句的select+from部分
		$newlist['wdata'] = $wdata?base64_encode(serialize($wdata)):'';
		$newlist['list'] = $list;
		$newlist['treenode'] = $data['treenode'];
		$newlist['china'] = $china;
		$newlist['sqlselectform'] = base64_encode(serialize($sqlselectform)); //sql查询语句的select+from部分
		$newlist['sqlselectformarr'] = $data['treenode']?base64_encode(serialize($sqlselectformarr)):'';//用于回写连接关系数组
	
	
		echo json_encode($newlist);exit;
		//print_r($list);exit;
	}
// 	public function ztreeValDo(){
// 		$data = $_POST;
// 		//获取所有的模型和表信息(模型中英文名称，主、子表中英文名称)
// 		$model=D($this->getActionName());
// 		//查询所有表单对应字段
// 		$proModel = M("mis_dynamic_form_propery");
// 		$datatableModel = M("mis_dynamic_form_datatable");
// 		$ModelAndTableList = $model->getAutoAllActionAndTable();
// 		//print_r($ModelAndTableList);
		
// 		$list = array();//选择的树节点信息(模型名称、表名称、表字段、连接符、连接关系)
// 		$wdata = array();//用于规则条件的参数（array($modelname=>array($table1,$table2,...))）
// 		//获取选中的树节点（模型+表）
// 		$treenode = $data['treenode']?explode(",",$data['treenode']):array();//explode分隔空字符串产生的数组会产生一个空元素
// 		if($treenode){
// 			foreach($treenode as $key=>$val){
// 				//$mastableid = '';//---》主表节点缓存，来解析主表数据。每次循环都必须重置
// 				$subtableid = '';//---》子表节点缓存，来解析子表数据。每次循环都必须重置
// 				if($val>0){//模型
// 					$modelformid = $val; //---》模型节点缓存，来解析主、子表数据。所以不能每次循环都重置，只能是到下个模型时替换
// 				}elseif($val<0){//主表
// 					$subtableid = $val;
// 				}
// 				$map['formid'] = $datamap['formid'] = $modelformid;
// 				$map['dbname'] = '';
// 				if(!$subtableid){
// 					$list[$modelformid]['actionname'] = $ModelAndTableList[$modelformid]['actionname'];
// 					$list[$modelformid]['actiontitle'] = $ModelAndTableList[$modelformid]['actiontitle'];
// 					$list[$modelformid]['table'][$modelformid]['tablename'] = $ModelAndTableList[$modelformid]['mastablename'];
// 					$list[$modelformid]['table'][$modelformid]['tabletitle'] = $ModelAndTableList[$modelformid]['mastabletitle'];
// 					$list[$modelformid]['table'][$modelformid]['tag'] = 'mas';
// 					$map['dbname'] = $ModelAndTableList[$modelformid]['mastablename'];
// 					$map['status'] = 1;
// 					$list[$modelformid]['table'][$modelformid]['fields'] = $proModel->where($map)->getField("id,fieldname");
// 					foreach ($data['objid'] as $k=>$v){
// 						if($val == $v){
// 							$list[$modelformid]['table'][$modelformid]['relationlink'] = $data['relationlink'][$k]; //连接符
// 							$list[$modelformid]['table'][$modelformid]['relationcont'] = $data['relationcont'][$k]; //连接关系
// 						}
// 					}
// 					$wdata[$ModelAndTableList[$modelformid]['actionname']][] = $ModelAndTableList[$modelformid]['mastablename'];
// 				}else{
// 					$list[$modelformid]['actionname'] = $ModelAndTableList[$modelformid]['actionname'];
// 					$list[$modelformid]['actiontitle'] = $ModelAndTableList[$modelformid]['actiontitle'];
// 					$list[$modelformid]['table'][$subtableid]['tablename'] = $ModelAndTableList[$modelformid]['sublist'][-$subtableid]['subtablename'];
// 					$list[$modelformid]['table'][$subtableid]['tabletitle'] = $ModelAndTableList[$modelformid]['sublist'][-$subtableid]['subtabletitle'];
// 					$list[$modelformid]['table'][$subtableid]['tag'] = 'sub';
// 					$datamap['tablename'] = $ModelAndTableList[$modelformid]['mastablename'];
// 					$list[$modelformid]['table'][$subtableid]['fields'] = $datatableModel->where($datamap)->getField("id,fieldname");
// 					if ($data['objid'][$modelformid]){
// 						$list[$modelformid]['table'][$subtableid]['relationlink'] = $data['relationlink'][$k];
// 						$list[$modelformid]['table'][$subtableid]['relationcont'] = $data['relationcont'][$k];
							
// 					}
// 					$wdata[$ModelAndTableList[$modelformid]['actionname']][] = $ModelAndTableList[$modelformid]['sublist'][-$subtableid]['subtablename'];
// 				}
					
// 			}
// 		}
		
// 		//关系处理、生成sql代码
// 		$sourcetable = $data['sourcetable'];
// 		$sourceformid = getFieldBy($data['modelname'],'actionname','id','mis_dynamic_form_manage','status',1);
// 		$sourcefields = $proModel->where("status=1 and formid={$sourceformid} and dbname='{$sourcetable}'" )->getField("id,fieldname");
		
// 		$select = "SELECT "; 						//sql查询语句的select部分
// 		$from = " FROM `{$data['sourcetable']}` "; 	//sql查询语句的from部分
// 		$china = '';								//中文表名
// 		foreach($sourcefields as $key=>$val){
// 			$select .= "`".$data['sourcetable']."`.`".$val."` AS '".$data['sourcetable'].".".$val."',";
// 		}		
// 		foreach ($list as $key=>$val){
// 			$china2 = '';
// 			foreach($val['table'] as $k=>$v){
// 				$china .= $china?"|".$v['tabletitle']:$v['tabletitle'];
// 				//关系处理
// 				$from .= " ".$v['relationlink']." `".$v['tablename']."` ON ".$v['relationcont']." ";
// 				foreach($v['fields'] as $k1=>$v1){
// 					$select .= "`".$v['tablename']."`.`".$v1."` AS '".$v['tablename'].".".$v1."',";					
// 				}
// 			}
// 		}
		
// 		//用于回写连接关系的数组
// 		$sqlselectformarr = array();
// 		foreach($data['objid'] as $key=>$val){
// 			$temp['objid'] = $val;
// 			$temp['relationlink'] = $data['relationlink'][$key];
// 			$temp['relationcont'] = $data['relationcont'][$key];
// 			$sqlselectformarr[$key]=$temp;
// 			unset($temp);
// 		}
// 		$select = substr($select,0,-1); //去掉最后一个逗号
// 		$sqlselectform = $select.$from; //sql查询语句的select+from部分
// 		$newlist['wdata'] = $wdata?base64_encode(serialize($wdata)):'';
// 		$newlist['list'] = $list;
// 		$newlist['treenode'] = $data['treenode'];
// 		$newlist['china'] = $china;
// 		$newlist['sqlselectform'] = base64_encode(serialize($sqlselectform)); //sql查询语句的select+from部分
// 		$newlist['sqlselectformarr'] = $data['treenode']?base64_encode(serialize($sqlselectformarr)):'';//用于回写连接关系数组
		
		
// 		echo json_encode($newlist);exit;
// 		//print_r($list);exit;
// 	}
	/**
	 * @Title: lookupdetailadd
	 * @Description: todo(控制数据对象选择后更改规则条件)   
	 * @author 谢友志 
	 * @date 2015-7-8 下午5:07:36 
	 * @throws
	 */
	public function lookupdetailadd(){
		$data = $_POST;		
		$newdata=array();
		$newdata['model']=$data['modelname'];
		$newdata['inputname'] = $data['inputname'];
		$newdata['table'][$data['modelname']][0]=$data['sourcetable'];
		//组合成符合W方法所需结构的数组
		foreach($data['data'] as $key=>$val){
			foreach($val['table'] as $k=>$v){
				$newdata['table'][$val['actionname']][] = $v['tablename'];
			}
		}
		//print_r($newdata);
		$this->assign('data',$newdata);
		$this->display();
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
		//$MisDynamicDatabaseMasList=$MisDynamicDatabaseMasModel->where("status=1 and formid=".$formid)->getField("tablename,tabletitle");
		$MisDynamicDatabaseMasList=array();
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
	
	public function sqlselectform($sourcetable){
		$sourcetable = $sourcetable?$sourcetable:$_POST['inlinetable'];
		$dyModel = D("Dynamicconf");
		$fields = $dyModel->getTableInfo($sourcetable);
		$sourcefields = array_keys($fields);		
		
		$select = "SELECT "; 						//sql查询语句的select部分
		$from = " FROM `{$sourcetable}` "; 	//sql查询语句的from部分
		foreach($sourcefields as $key=>$val){
			$select .= "`".$sourcetable."`.`".$val."` AS '".$sourcetable.".".$val."',";
		}
		$select = substr($select,0,-1); //去掉最后一个逗号
		$sqlselectform = $select.$from; //sql查询语句的select+from部分
		$sqlselectform = base64_encode(serialize($sqlselectform)); //sql查询语句的select+from部分
		if($_POST['inlinetable']){
			echo $sqlselectform;
		}else{
			return $sqlselectform;
		}
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}