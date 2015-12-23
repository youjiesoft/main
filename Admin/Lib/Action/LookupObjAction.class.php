<?php
/**
 * @Title: file_name
 * @Package package_name
 * @Description: todo(selectlist配置文件维护)
 * @author libo
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-7-28 下午1:47:28
 * @version V1.0
 */
class LookupObjAction extends CommonAction{
	
	/**
	 * 查找动态配置组里边可用的节点
	 */
	private $firstDetail = array();
	public function index(){
		$map = $this->_search ();
		//左侧树
		if(!$_REQUEST['jump']){
			$models = D('SystemConfigNumber');
			$returnarr = $models->getRoleTreeCache('LookupobjBox',$url='__URL__/index',$map2=array(),$modelname=false,$target='ajax',$title='',"LookupObj","tt");
			$this->assign('returnarr',$returnarr);
		}		
		
		//动态配置列表项字段  包括：1、是否显示；2、是否排序；3、列宽度
		$scdmodel = D('SystemConfigDetail');
		$modelname = $this->getActionName();
		$detailList = $scdmodel->getDetail($modelname,false);
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		
		//扩展工具栏操作
		$toolbarextension = $scdmodel->getDetail($modelname,true,'toolbar');
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		
		//找到所有数据 
 		$model=D('LookupObj');
		$selectlist = $model->GetLookupDetails();
		foreach($selectlist as $key=>$val){
			if($val['viewtype']==1){
				unset($selectlist[$key]);
			}
		}
		//分页
		$list=array();
		$searchField = array();
		if($map['_complex']){
			// 做全检索
			$searchField = array_keys($map['_complex']);
			array_pop($searchField);
		}else{
			// 做指定字段的检索
			$searchField = array_keys($map);
		}
		trace($selectlist);
		$searchWord = $_POST['qkeysword'];
		foreach ($selectlist as $key=>$val){
			$selectlist[$key]['id']=$key;
			//查询字段中文名，太影响速度，首页不再显示这部分
// 			 $selectlist[$key]['fieldsshow']=implode(',',$this->getDeatilshowname($val['mode'], $val['fields']));
// 			 $selectlist[$key]['filedshow']=implode(',',$this->getDeatilshowname($val['mode'], $val['filed']));
// 			 $selectlist[$key]['field1show']=implode(',',$this->getDeatilshowname($val['mode'], $val['filed1']));
//			 $showval = implode(',',$this->getDeatilshowname($val['mode'], $val['val']));
// 			 $selectlist[$key]['val']=$showval?$shwoval:'id';
//			$selectlist[$key]['val']=$val['val']?$val['val']:'id';//如果不设置存储字段，默认设置为id
			 if($_REQUEST['model'] ){ //模块内查询
			 	if($val['mode']==$_REQUEST['model']){
			 		if($searchWord){
					 	foreach($searchField as $k=>$v){
					 		// || strpos($v[$val],$_POST['advanced'.$val])!==false  //高级检索
					 		if(strpos($val[$v] , $searchWord) !== false ){
					 			$list[][$key]=$selectlist[$key];
					 		} 
					 	}
				 	}else{
				 		$list[][$key]=$selectlist[$key];
				 	}			 		
			 	}
			 }else{		//全查询
				 if($searchWord){
				 	foreach($searchField as $k=>$v){
				 		// || strpos($v[$val],$_POST['advanced'.$val])!==false  //高级检索
				 		if(strpos($val[$v] , $searchWord) !== false ){
				 			$list[][$key]=$selectlist[$key];
				 		} 
				 	}
				 }else{
				 	$list[][$key]=$selectlist[$key];
				 }
			 } 
		}
		$SelectlistAction=A("Selectlist");
		if($_POST['pageNum']){
			$pageNum=$_POST['pageNum'];
		}else{
			$pageNum=1;
		}
		$this->assign("model",$_REQUEST['model']);
		
		$SelectlistAction->getPager($list,$pageNum,C("PAGE_LISTROWS"));
		if($_REQUEST['jump']){
			$this->display('indexview');
		}else{
			$this->display();
		}
	}
	public function add(){
		//查询字段列表
		$modelname=$_REQUEST['modelname'];		
		$detailList = $this->getHaveSystemFieldsOfDetailList($modelname);
		$this->assign ( 'detailList', $detailList );
		$this->getdateCount($modelname);
		$this->assign('modelname',$modelname);
		$this->display('addDeatil');
	}
	/**
	 * @Title: getHaveSystemFieldsOfDetailList
	 * @Description: todo(包含系统字段的list配置文件)   
	 * @author 谢友志 
	 * @date 2015-8-17 下午5:16:22 
	 * @throws
	 */
	private function getHaveSystemFieldsOfDetailList($actionname){
		$scdmodel = D('SystemConfigDetail');
		//查询字段列表
		$modelname=$actionname;
		$detailList = $scdmodel->getDetail($modelname,false);
		$file = CONF_PATH."property.php";
		$systemFields = array();
		if(is_file($file)){
			require $file;
			$systemFields = array_merge($NBM_DEFAULTFIELD,$NBM_AUDITFELD,$NBM_BASEARCHIVESFIELD);
		}
		foreach($systemFields as $key=>$val){
			$systemFields[$key]['name'] = $key;
			$systemFields[$key]['showname'] = $val['title'];
			$systemFields[$key]['status'] = 1;
			$systemFields[$key]['shows'] = 0;
				
		}
		if ($detailList) {
			foreach($detailList as $key=>$val){
				foreach($systemFields as $k=>$v){
					if($key == $k){
						unset($systemFields[$k]);
					}
				}
			}
			$detailList = array_merge($detailList,$systemFields);			
		}
		return $detailList;
	}
	private  function getdateCount($modelname){
		//查询当前表是否存在内嵌表
		$formid=getFieldBy($modelname, "actionname", "id", "mis_dynamic_form_manage");
		$dateMap=array();
		$dateMap['formid']=$formid;
		$dateMap['category']="datatable";
		$MisDynamicFormProperyDao=M("mis_dynamic_form_propery");
		$datatablecount=$MisDynamicFormProperyDao->where($dateMap)->count();
		$this->assign("datatablecount",$datatablecount);
		$this->assign("formid",$formid);
	}
	/**
	 * 
	 * @Title: lookupdatetable
	 * @Description: todo(内嵌表格配置)   
	 * @author renling 
	 * @date 2015年1月6日 下午4:01:12 
	 * @throws
	 */
	public function lookupdatetable(){
		if(!$_POST['subtype']){
			//查询当前表单中的内嵌表格
			//print_R($_REQUEST);
			$formid=$_REQUEST['formid'];
			$proMap=array();
			$proMap['category']="datatable";
			$proMap['formid']=$formid;
			$misDynamicFormProperyDao=M("mis_dynamic_form_propery");
			$datelist=$misDynamicFormProperyDao->where($proMap)->getField("id,title"); 
			$this->assign("datelist",$datelist);
			$this->assign("formid",$formid);
			$this->display();
		}else{
			$data = $_POST;
			//print_r($data);
			if($data['proid']){
				$fieldbackkey = array_keys($data['fieldback']);
				foreach($data['proid'] as $k=>$v){
					if(!in_array($v,$fieldbackkey)){
						unset($data['proid'][$k]);
					}
				}
			}else{
				$data=array();
			}
			
			$this->success("添加成功",'',json_encode($data));			
		}
	}
	public function lookupgetdatelist(){
		$proid=$_POST['proid'];
		$proMap=array();
		$proMap['propertyid']=$proid;
		//查询此内嵌表格中的字段
		$misDynamicFormDatatableDao=M("mis_dynamic_form_datatable");
		$misDynamicFormDatatableList=$misDynamicFormDatatableDao->where($proMap)->field("id,fieldtitle,fieldname")->select();
		echo json_encode($misDynamicFormDatatableList);
	}
	/**
	 * (non-PHPdoc)新增类型
	 * @see CommonAction::insert()
	 */
	public function insert() {
		$_POST['ordersort'] = $_POST['ordersort']&&$_POST['ordersort']!='null'?"desc":'';
		//新添加了排序功能，将页面字段按顺序存入数据库
		$_POST['allfileds'] = implode(',',array_keys($_POST['func']));
		
		if(empty($_POST['fields'])){
			$this->error("带回字段不能为空");
		}
		if(empty($_POST['filed'])||!in_array($_POST['filed'],$_POST['fields'])){
			$this->error("lookup显示字段必需存在 <br/>   &nbsp; &nbsp;lookup显示字段必须包含在带回字段中");
		}
		if(empty($_POST['val'])){
			$_POST['val']='id';
		}
		//查询是否有同名称的配置记录 $vo
		$model = D($this->getActionName());
		$vo = $model->where("title='".trim($_POST['title'])."'")->find(); 
		if($vo){
			$this->error("输入的名称有重名，请重新输入！");
		}else{
			//构建数组 插入数据库
			$fields=$_POST['fields'];			
			//主表数据转换函数处理  为了和以前的样式统一，这里把方法和参数处理成多维数组
			foreach($_POST['func'] as $k=>$v){
				if($v){
					if(!function_exists($v)){
						$this->error("转换函数".$v."不存在！");
					}	
				}				
				if($v){
					$func[$k]['field'] = $k;
					$func[$k]['funcname'][0][0] = $v;
					$func[$k]['funcdata'][0][0] = explode(";",$_POST['funcdata'][$k]);
				}
			}
			$funcinfo = base64_encode(serialize($func));
			//主表数据排序
			$datasort = '';
			if($_POST['datasort']){
				foreach($_POST['datasort'] as $dsk=>$dsv){
					$datasort .= $dsk." ".$_POST['sorttype'][$dsk].",";
				}
				$datasort = substr($datasort,0,-1);	
			}
			//内嵌表配置信息（新）
			$inlineinfo = $_POST['inlineinfo']?html_entity_decode($_POST['inlineinfo']):''; //用于页面修改
			$inlinedt = array();															//用于配置文件dt
			$dtinfo = '';																	//用于数据库保存配置文件dt信息
			if($inlineinfo){
	 			//内嵌表信息数组
	 			$inline = json_decode($inlineinfo,true);
	 			//获取mis_dynamic_form_propery里的相关信息
	 			$pModel = M('mis_dynamic_form_propery');
	 			$pmap['formid'] = $inline['formid'];
	 			$pmap['id'] = array("in",$inline['proid']);
	 			$pList = $pModel->where($pmap)->getField("id,fieldname,title,dbname");
	 			//获取mis_dynamic_form_datatable里的相关信息
	 			$dModel = M("mis_dynamic_form_datatable");
	 			$dmap['propertyid'] = array("in",$inline['proid']);
	 			$dList = $dModel->field("id,fieldname,fieldtitle,fieldtype,fieldlength,category,propertyid")->where($dmap)->select();
	 			$dList2 = array();
	 			//将mis_dynamic_form_datatable里的相关信息转成proid和id的数组key用于取值
	 			foreach($dList as $dk=>$dv){
	 				$dList2[$dv['propertyid']][$dv['id']] = $dv;
	 			}
	 			//组装配置文件的内嵌表信息
	 			foreach($inline['proid'] as $pk=>$pv){
	 				$inlinetable = $pList[$pv]['dbname']."_sub_".$pList[$pv]['fieldname'];
	 				$inlinedt[$inlinetable]['title'] = $pList[$pv]['title'];
	 				$inlinedt[$inlinetable]['datename'] = $inlinetable;
	 				$inlinedt[$inlinetable]['orderfield'] = $inline['orderfield'][$pv];
	 				$inlinedt[$inlinetable]['ordersort'] = '';
	 				if($inline['orderfield'][$pv]){
	 					$inlinedt[$inlinetable]['ordersort'] = $inline['ordersort'][$pv]?'desc':'asc';
	 				}
	 				//内嵌表转换函数字段
	 				$dtfuncfield = array(); 	
	 				foreach($inline['dtfuncfield'][$pv] as $fk=>$fv){
	 					$dtfuncfield[] = $dList2[$pv][$fv]['fieldname'];
	 				}  											
	 				$inlinedt[$inlinetable]['dtfuncfields'] = $dtfuncfield;
	 				//内嵌表转换函数方法
	 				$dtfunc = array();
	 				foreach($inline['dtfunc'][$pv] as $fck=>$fcv){ 					
	 					if(!empty($fcv)){
	 						if(!function_exists($fcv)){
	 							$this->error("转换函数".$fcv."不存在！");
	 						}
	 						$func['field'][$dList2[$pv][$fck]['fieldname']] = $dList2[$pv][$fck]['fieldtitle'];
	 						$func['funcname'][$dList2[$pv][$fck]['fieldname']][0][0] = $fcv;
	 						$func['funcdata'][$dList2[$pv][$fck]['fieldname']][0][0] = $inline['dtfuncdata'][$pv][$fck];
	 						$dtfunc[$dList2[$pv][$fck]['fieldname']] = $func;
	 					} 					
	 				}
	 				$inlinedt[$inlinetable]['dtfuncinfo'] = $dtfunc;
	 				//内嵌表带回字段
	 				$inlineback = array();
	 				foreach($inline['fieldback'][$pv] as $bk=>$bv){
	 					//字段名
	 					$infieldname = $dList2[$pv][$bv]['fieldname'];
	 					$inlineback[$infieldname]['name']=$infieldname;
	 					$inlineback[$infieldname]['title']=$dList2[$pv][$bv]['fieldtitle'];
	 					$inlineback[$infieldname]['category']=$dList2[$pv][$bv]['category'];
	 					$inlineback[$infieldname]['length']=$dList2[$pv][$bv]['fieldlength'];
	 				}
	 				$inlinedt[$inlinetable]['list'] =$inlineback;
	 			}
	 			$dtinfo = base64_encode(serialize($inlinedt));
	 		}
				
			//内嵌表数据排序

			
			//$dtfuncinfo = base64_encode(serialize($dtfuncinfoarr));
			//dump(base64_encode(str_replace("'","",serialize($func))));exit;
			//$funcinfo =  base64_encode(str_replace("'","",serialize($func)));
			$data=array(
				'id' => md5($_SESSION[C('USER_AUTH_KEY')].time()),
				'title'=>$_POST['title'],
				'fields' => implode(',',$fields),
				'checkforfields'=>implode(',',$this->getDeatilshowname($_POST['model'], $fields, 1)),
				'fieldcom' => implode(',',$_POST['fieldcom']),
				'listshowfields'=>implode(',',$_POST['listshowfields']),
				'funccheck'=>implode(',',$_POST['funccheck']),
				'funcinfo'=>$funcinfo,
				'url'=>$_POST['url'],
				'mode' => $_POST['model'], // 屈强@2014-08-07 新增标题项
				'checkformodel'=>$_POST['model'],//$_POST['checkformodel'],
				'filed'=> $_POST['filed'],
				'filed1'=> $_POST['filed1'],
				'val'=> $_POST['val'],
				'showrules'=>$_POST['showrules'],
				'rulesinfo'=>$_POST['rulesinfo'],
				'rules'=>$_POST['rules'],
				'condition'=> $_POST['rules'],
				'dialogwidth'=>$_POST['dialogwidth'],
				'dialogheight'=>$_POST['dialogheight'],
				'allfields'=>$_POST['allfileds'],
				'status' => 1,
				'level' => 15, // 屈强@2014-08-07 新增当前项权限
				'allfields' => $_POST['allfields'],
				'datasort' => $datasort,
				//'customstatus' => $_POST['customstatus'],
				'conftype'=>$_POST['conftype'],
				'inlineinfo'=>$inlineinfo,
				'dtinfo' =>$dtinfo
			);			
			$list = $model->data($data)->add();
			//插入成功 将数据库数据 重组写入配置文件
			if($list){
				$v = $model->where('status=1 and id="'.$data['id'].'"')->find();
				$conlist = array();
				$conlist = array(
							'title'=>$v['title'],
							'fields' => $v['fields'],
							'fields_china'=>$this->getDeatilshowname($v['mode'],$v['fields'],'','china'),
							'checkforfields'=>$v['checkforfields'],
							//'checkforfields_china'=>$this->getDeatilshowname($v['mode'],$v['checkforfields'],'','china'),
							'fieldcom'=>$v['fieldcom'],
							'listshowfields'=>$v['listshowfields'],
							'listshowfields_china'=>$this->getDeatilshowname($v['mode'],$v['listshowfields'],'','china'),
							'funccheck'=>$v['funccheck'],
							'funccheck_china'=>$this->getDeatilshowname($v['mode'],$v['funccheck'],'','china'),
							'funcinfo'=>unserialize(base64_decode($v['funcinfo'])),
							'url'=>$v['url'],
							'mode' => $v['mode'], // 屈强@2014-08-07 新增标题项
							'checkformodel'=>$v['checkformodel'],
							'filed'=> $v['filed'],
							'filed1'=> $v['filed1'],
							'val'=> $v['val'],
							'showrules'=>$v['showrules'],
							'rulesinfo'=>$v['rulesinfo'],
							'rules'=>$v['rules'],
							'condition'=> $v['rules'],
							'dialogwidth'=>$v['dialogwidth'],
							'dialogheight'=>$v['dialogheight'],
							'status' =>$v['status'],
							'level' =>$v['level'], // 屈强@2014-08-07 新增当前项权限
							'datasort' => $v['datasort'],
							//'customstatus' => $v['customstatus'],
							'conftype'=>$v['conftype'],
							);
			
				//如果有内嵌表配置信息
				if($v['inlineinfo']){
					$conlist['dt']=$inlinedt;
					$conlist['versions'] = '1.0';
				}
				//$model->SetRules($conlist);
				$path = $model->GetDetailsPath();
				$filename = $path.'/'.$v['id'].".php";
				$model->SetSongleRule($filename,$conlist);
				$fileindex[$v['id']] = $v['title'];
				$conflistfile = $model->GetConflistFile();
				$conflist = require $conflistfile;
				$conflist = array_merge($conflist,$fileindex);
				$model->SetSongleRule($conflistfile,$conflist);				
				$this->success('操作成功');
			}else{
				$this->error("写入数据库失败");
			}
		
		}
	}
	private function getDeatilshowname($model,$name,$type,$return){
		if(!$type){
			$name=explode(",",$name);
		}
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($model,false);
		require CONF_PATH. 'property.php';
		$syslist = array_merge($NBM_DEFAULTFIELD,$NBM_AUDITFELD,$NBM_BASEARCHIVESFIELD);
		foreach($syslist as $key=>$val){
			$syslist[$key]['name']=$key;
			$syslist[$key]['showname'] = $val['title'];
		}
		$detailList = array_merge($syslist,$detailList);
		$shownamechina = array();
		$showname=array();
		$shownamelist=array();
		if($return!='china'){
			foreach ($detailList as $dlist=>$dval){
				foreach ($name as $key=>$val){
					if($dval['name']==$val){
						$shownamechina[$val]= $dval['showname']?$dval['showname']:$dval['name'];
						$shownamelist[]=$dval['showname'];
						$showname[]="&#39;".$dval['name']."&#39;=>&#39;".$dval['showname']."&#39;";
							
					}
				}
			}
		}else{
			foreach ($name as $key=>$val){
				$shownamechina[$val]= $val;
				foreach ($detailList as $dlist=>$dval){						
					if($dval['name']==$val){
						$shownamechina[$val]= $dval['showname']?$dval['showname']:$dval['name'];
						$shownamelist[]=$dval['showname'];
						$showname[]="&#39;".$dval['name']."&#39;=>&#39;".$dval['showname']."&#39;";
					}
				}
			}
		}
		
		//$showname[]="&#39;id&#39;=>&#39;编号&#39;";
		if($return == 'china'){
			return $shownamechina;
		}elseif($type){
			return $showname;
		}else{
			return $shownamelist;
		}
	}
	/**
	 * (non-PHPdoc)编辑
	 * @see CommonAction::edit()
	 */
	public function edit(){
		try{
			
			$id = $_REQUEST['id'];
			//$scdmodel = D('SystemConfigDetail');
			//查询字段列表
			$model=D('LookupObj');
			$list = $model->where("id='{$id}'")->find();
			
	// 		if(file_exists($model->GetFile())){
	// 			$selectlist = require $model->GetFile();
	// 		}
	// 		$list=$selectlist[$_REQUEST['id']];
//			$path = $model->GetDetailsPath();			
//			$list = require $path.'/'.$id.'.php';
			$modelname=$list['mode'];
			$list['fields']=explode(',',$list['fields']);
			$list['id']=$id;
			$list['val'] = $list['val']?$list['val']:'id';
			$list['fieldcom'] = explode(',',$list['fieldcom']);
			$list['listshowfields'] = explode(',',$list['listshowfields']);
			$list['funccheck'] = explode(',',$list['funccheck']);
			foreach($list['funcinfo'] as $k=>$v){
				if($v['funcname']){				
					$list['func'][$v['field']] = $v['funcname'][0][0];
					$list['funcdata'][$v['field']] = implode(';',$v['funcdata'][0][0]);
				}
			}
			$datasort = explode(",",$list['datasort']);
			foreach($datasort as $dsk=>$dsv){
				$tempds = explode(' ',$dsv);
				$list['datasorts'][$tempds[0]]=$tempds[1];
				$list['datasortarr'][] = $tempds[0];
			}
			$this->assign("vo",$list);
			//$detailList = $scdmodel->getDetail($modelname,false);
			$detailList = $this->getHaveSystemFieldsOfDetailList($modelname);
			//如果数据库存入了allfields字段，需判断list字段排序；重新生成$detailList
			$dballfields = getFieldBy($id,'id','allfields','mis_system_lookupobj');
			if($dballfields){
				$allfields = explode(',',$dballfields);
					foreach($allfields as $key=>$val){
						foreach($detailList as $k=>$v){
							if($val == $v['name']){
								$allfields[$key] = $v;
								unset($detailList[$k]);
							}
						}
					}
					$detailList = array_merge($allfields,$detailList);
			}
			if ($detailList) {
				$this->assign ( 'detailList', $detailList );
			}
			
			/////////////////////////////////////////////////////////////////////////////////////////////////
			//									lookup左侧树数据源		by nbmxkj 20150527								//
			/////////////////////////////////////////////////////////////////////////////////////////////////
			// 获取所有可用于生成树的表	, 目前从动态建模的mas中获取表名
			$souceTableObj = M('mis_dynamic_database_mas');
			$tables = $souceTableObj->query("SELECT `TABLE_NAME`,`TABLE_COMMENT` FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '".C('DB_NAME')."' ");//limit 1
			$this->assign('tableList' , $tables);
			
			$treeConfigObj = M('mis_system_lookupobj_sub_treeconfig');
			$treeConfigMap['masid'] = $_REQUEST['id'];
			$treeConfigData = $treeConfigObj->where($treeConfigMap)->select();
			$this->assign('treeConfigData',$treeConfigData);
			////////////////////////////////////////////////////////////////////////////////////////////////
			
			/////////////////////////////////////////////////////////////////////////////////////////////////
			//									lookup树形列表	by nbmxkj 20150921                							//
			/////////////////////////////////////////////////////////////////////////////////////////////////
			$treeListConfigObj = M('mis_system_lookupobj_sub_treelistconfig');
			$treeListConfigMap['masid'] = $_REQUEST['id'];
			$treeListConfigData = $treeListConfigObj->where($treeListConfigMap)->find();
			$this->assign('treeListConfigData',$treeListConfigData);
			
			///////////////////////////  树列表end  ///////////////////////
			$this->getdateCount($modelname);
			$this->assign('modelname',$modelname);
			$this->display();
		}catch(Exception $e){
			echo '<pre>'.$e->__toString();
		}
		
	}
	
	/**
	 * @Title: getTableField
	 * @Description: todo(获取指定表下的字段信息)
	 * @param string $t	传表名。当该值为空时为ajax获取字段，post 参数为table，输出json数据。
	 * @return multitype:array|json
	 * @author quqiang
	 * @date 2015-5-27 下午9:05:54
	 * @throws
	 */
	function getTableField( $t='' ){
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
     * @Title: delTreeConfig
     * @Description: todo(删除菜单树的配置)
     * @return multitype:array|json
     * @author quqiang
     * @date 2015-5-28 下午9:39
     * @throws
     */
    function delTreeConfig(){
        try{
            $id = $_POST['id'];
            
            if(empty($id)){
                $msg = '删除数据失败，缺少数据ID';
                throw new NullDataExcetion($msg);
            }
            
            $treeConfigObj = M('mis_system_lookupobj_sub_treeconfig');
            $delMap['id']=$id;
            $ret = $treeConfigObj->where($delMap)->delete();
            if(false === $ret){
                $msg = '删除数据失败'.$ret->getDbError();
                throw new NullDataExcetion($msg);
            }
            $this->success('成功删除配置数据！');
        }catch(Exception $e){
            $this->error($e->getMessage());
        }
        
        
    }
	public function view(){
		$this->edit();
	}
	public function update() {//8480a35d9adc2bb33c6fbbfc6a3413a3
		$_POST['allfileds'] = implode(',',array_keys($_POST['func']));
		if(empty($_POST['fields'])){
			$this->error("带回字段不能为空");
		}
		if(empty($_POST['filed'])||!in_array($_POST['filed'],$_POST['fields'])){
			$this->error("lookup显示字段必需存在 <br/>   &nbsp; &nbsp;lookup显示字段必须包含在带回字段中");
		}
		if(empty($_POST['val'])){
			$_POST['val']="id";
		}
		//查询是否有同名称的配置记录 $vo
		$model = D($this->getActionName());
			//构建数组 插入数据库
		$fields=$_POST['fields'];		
 		//主数据转换函数处理
 		foreach($_POST['func'] as $k=>$v){
 			if($v){
 				if(!function_exists($v)){
 					$this->error("转换函数".$v."不存在！");
 				}
 			}
 			if($v){
 				$func[$k]['field'] = $k;
 				$func[$k]['funcname'][0][0] = $v;
 				$func[$k]['funcdata'][0][0] = explode(";",$_POST['funcdata'][$k]);
 			}
 		}
 		$funcinfo = base64_encode(serialize($func));
 		//主数据排序
 		$datasort = '';
 		if($_POST['datasort']){
 			foreach($_POST['datasort'] as $dsk=>$dsv){
 				$datasort .= $dsk." ".$_POST['sorttype'][$dsk].",";
 			}
 			$datasort = substr($datasort,0,-1);
 		}
 		//内嵌表配置信息（新）
 		$inlineinfo = $_POST['inlineinfo']?html_entity_decode($_POST['inlineinfo']):''; //用于页面修改
		$inlinedt = array();															//用于配置文件dt
		$dtinfo = '';																	//用于数据库保存配置文件dt信息
 		if($inlineinfo){
 			//内嵌表信息数组
 			$inline = json_decode($inlineinfo,true);
 			//获取mis_dynamic_form_propery里的相关信息
 			$pModel = M('mis_dynamic_form_propery');
 			$pmap['formid'] = $inline['formid'];
 			$pmap['id'] = array("in",$inline['proid']);
 			$pList = $pModel->where($pmap)->getField("id,fieldname,title,dbname");
 			//获取mis_dynamic_form_datatable里的相关信息
 			$dModel = M("mis_dynamic_form_datatable");
 			$dmap['propertyid'] = array("in",$inline['proid']);
 			$dList = $dModel->field("id,fieldname,fieldtitle,fieldtype,fieldlength,category,propertyid")->where($dmap)->select();
 			$dList2 = array();
 			//将mis_dynamic_form_datatable里的相关信息转成proid和id的数组key用于取值
 			foreach($dList as $dk=>$dv){
 				$dList2[$dv['propertyid']][$dv['id']] = $dv;
 			}
 			//组装配置文件的内嵌表信息
 			foreach($inline['proid'] as $pk=>$pv){
 				$inlinetable = $pList[$pv]['dbname']."_sub_".$pList[$pv]['fieldname'];
 				$inlinedt[$inlinetable]['title'] = $pList[$pv]['title'];
 				$inlinedt[$inlinetable]['datename'] = $inlinetable;
 				$inlinedt[$inlinetable]['orderfield'] = $inline['orderfield'][$pv];
 				$inlinedt[$inlinetable]['ordersort'] = '';
 				if($inline['orderfield'][$pv]){
 					$inlinedt[$inlinetable]['ordersort'] = $inline['ordersort'][$pv]?'desc':'asc';
 				}
 				//内嵌表转换函数字段
 				$dtfuncfield = array(); 	
 				foreach($inline['dtfuncfield'][$pv] as $fk=>$fv){
 					$dtfuncfield[] = $dList2[$pv][$fv]['fieldname'];
 				}  											
 				$inlinedt[$inlinetable]['dtfuncfields'] = $dtfuncfield;
 				//内嵌表转换函数方法
 				$dtfunc = array();
 				foreach($inline['dtfunc'][$pv] as $fck=>$fcv){ 					
 					if(!empty($fcv)){
 						if(!function_exists($fcv)){
 							$this->error("转换函数".$fcv."不存在！");
 						}
 						$func['field'][$dList2[$pv][$fck]['fieldname']] = $dList2[$pv][$fck]['fieldtitle'];
 						$func['funcname'][$dList2[$pv][$fck]['fieldname']][0][0] = $fcv;
 						$func['funcdata'][$dList2[$pv][$fck]['fieldname']][0][0] = $inline['dtfuncdata'][$pv][$fck];
 						$dtfunc[$dList2[$pv][$fck]['fieldname']] = $func;
 					} 					
 				}
 				$inlinedt[$inlinetable]['dtfuncinfo'] = $dtfunc;
 				//内嵌表带回字段
 				$inlineback = array();
 				foreach($inline['fieldback'][$pv] as $bk=>$bv){
 					//字段名
 					$infieldname = $dList2[$pv][$bv]['fieldname'];
 					$inlineback[$infieldname]['name']=$infieldname;
 					$inlineback[$infieldname]['title']=$dList2[$pv][$bv]['fieldtitle'];
 					$inlineback[$infieldname]['category']=$dList2[$pv][$bv]['category'];
 					$inlineback[$infieldname]['length']=$dList2[$pv][$bv]['fieldlength'];
 				}
 				$inlinedt[$inlinetable]['list'] =$inlineback;
 			}
 			$dtinfo = base64_encode(serialize($inlinedt));
 		}
 		
 		
 		
 		//print_r($dtfuncinfoarr);exit;
		$data=array(
				'id' => $_POST['id'],
				'title'=>$_POST['title'],
				'fields' => implode(',',$fields),
				'checkforfields'=>implode(',',$this->getDeatilshowname($_POST['model'], $fields, 1)),
				'fieldcom' => implode(',',$_POST['fieldcom']),
				'listshowfields'=>implode(',',$_POST['listshowfields']),
				'funccheck'=>implode(',',$_POST['funccheck']),
				'funcinfo'=>$funcinfo,
				'url'=>$_POST['url'],
				'mode' => $_POST['model'], // 屈强@2014-08-07 新增标题项
				'checkformodel'=>$_POST['model'],//$_POST['checkformodel'],
				'filed'=> $_POST['filed'],
				'filed1'=> $_POST['filed1'],
				'val'=> $_POST['val'],
				'showrules'=>$_POST['showrules'],
				'rulesinfo'=>$_POST['rulesinfo'],
				'rules'=>$_POST['rules'],
				'condition'=> $_POST['rules'], 
				'dialogwidth'=>$_POST['dialogwidth'],
				'dialogheight'=>$_POST['dialogheight'],
				'allfields' => $_POST['allfileds'],
				'status' => 1,
				'level' => 15, // 屈强@2014-08-07 新增当前项权限
				'allfields' => $_POST['allfileds'],
				'datasort' => $datasort,
				//'customstatus' => $_POST['customstatus'],
				'conftype'=>$_POST['conftype'],
				'inlineinfo'=>$inlineinfo,
				'dtinfo' =>$dtinfo
		);
		$list = $model->data($data)->save();
		//echo $model->getlastsql();exit;
		
		
		/////////////////////////////////////////////////////////////////////////////////////////////////
		//									lookup左侧树的配置处理	by nbmxkj 20150527							//
		/////////////////////////////////////////////////////////////////////////////////////////////////
		$treeConfig = '';
		//print_r($_POST);exit;
		if($_POST['treeconfig']){
			/**
			 * treemodel		树来源model
			 * treetitle			树显示标题
			 * treelength		树中读取的数据量
			 * treecondition		树过滤条件
			 * treeshow		树上的显示字段
			 * treevalue		为做筛选 追加检索字段。
			 * treetext			当前lookup对象中的可用字段，做为筛选条件构成元素
			 */
			$treeDataSouce = $_POST['treeconfig'];
			foreach ($treeDataSouce['treemodel'] as $k=>$v){
				unset($temp);
				if($v && $treeDataSouce['treeshow'][$k] && $treeDataSouce['treevalue'][$k] && $treeDataSouce['treetext'][$k]){
					$temp['treemodel'] 		=	$v;
					$temp['treetitle'] 			= 	$treeDataSouce['treetitle'][$k] ? $treeDataSouce['treetitle'][$k] : '默认菜单名称';
					$temp['treelength'] 		= 	$treeDataSouce['treelength'][$k];
					$temp['treecondition'] 	= 	$treeDataSouce['treecondition'][$k];
					$temp['treeshow'] 		=	$treeDataSouce['treeshow'][$k];
					$temp['treevalue']			=	$treeDataSouce['treevalue'][$k];
					$temp['treetext']			=	$treeDataSouce['treetext'][$k];
				}
				if(is_array($temp)){
					
					$treeConfig[] = $temp;
					$temp['masid'] = $data['id'];
					if($treeDataSouce['id'][$k]){
						$temp['id'] 		=	$treeDataSouce['id'][$k];
					}
					
					$treeConfigObj = M('mis_system_lookupobj_sub_treeconfig');
					// 数据写入到树配置表中
					if($temp['id']){
						$ret = $treeConfigObj->data($temp)->save();
						if(false === $ret){
							$this->error(' |树配置失败'.$treeConfigObj->getDbError().'  | '.$treeConfigObj->getLastSql());
						}
					}else{
						$ret = $treeConfigObj->data($temp)->add();
						if(false === $ret){
							$this->error(' |树配置失败'.$treeConfigObj->getDbError() . ' ' . $treeConfigObj->getLastSql());
						}
					}
				}
			}
		}
		/////////////////////////////////////////////////////////////////////////////////////////////////
		
		
		/////////////////////////////////////////////////////////////////////////////////////////////////
		//									lookup树形列表	by nbmxkj 20150921                							//
		/////////////////////////////////////////////////////////////////////////////////////////////////
		$treeListConfig = '';
//		if($_POST['treelist']){
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
		            $temp['masid'] = $data['id'];
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
		        }else{
		        	 if($treeListDataSouce['id']){
		                $temp['id'] 		=	$treeListDataSouce['id'];
		                $treeListConfigObj = M('mis_system_lookupobj_sub_treelistconfig');
		                $ret = $treeListConfigObj->where($temp)->delete();
		                if(false === $ret){
		                	$this->error(' |树列表配置失败'.$treeListConfigObj->getDbError().'  | '.$treeListConfigObj->getLastSql());
		                }
		            }
		        }
//		}
		
		//插入成功 将数据库数据 重组写入配置文件
		if($list){
			$v = $model->where('status=1 and id="'.$_POST['id'].'"')->find();
			$conlist = array(
					'title'=>$v['title'],
					'fields' => $v['fields'],
					'fields_china'=>$this->getDeatilshowname($v['mode'],$v['fields'],'','china'),
					'checkforfields'=>$v['checkforfields'],
					'fieldcom'=>$v['fieldcom'],
					'listshowfields'=>$v['listshowfields'],
					'listshowfields_china'=>$this->getDeatilshowname($v['mode'],$v['listshowfields'],'','china'),
					'funccheck'=>$v['funccheck'],
					'funccheck_china'=>$this->getDeatilshowname($v['mode'],$v['funccheck'],'','china'),
					'funcinfo'=>unserialize(base64_decode($v['funcinfo'])),
					'url'=>$v['url'],
					'mode' => $v['mode'], // 屈强@2014-08-07 新增标题项
					'checkformodel'=>$v['checkformodel'],
					'filed'=> $v['filed'],
					'filed1'=> $v['filed1'],
					'val'=> $v['val'],
					'showrules'=>$v['showrules'],
					'rulesinfo'=>$v['rulesinfo'],
					'rules'=>$v['rules'],
					'condition'=> $v['rules'],
					'dialogwidth'=>$v['dialogwidth'],
					'dialogheight'=>$v['dialogheight'],
					'status' =>$v['status'],
					'level' =>$v['level'], // 屈强@2014-08-07 新增当前项权限
					'datasort' => $v['datasort'],
					//'customstatus' => $v['customstatus'],
					'conftype'=>$v['conftype'],
			);
			//如果有内嵌表配置信息
			if($v['inlineinfo']){
				$conlist['dt']=$inlinedt;

 				$conlist['versions'] = '1.0';
			}
			//	存在树形菜单配置时，将配置信息写入物理文件
			if(is_array($treeConfig)){
				$conlist['tree'] = $treeConfig;
			}
			//	存在树形列表配置时，将配置信息写入物理文件
			if(is_array($treeListConfig)){
			    $conlist['treelist'] = $treeListConfig;
			}
			
			
			//$model->SetSongleRule("./Dynamicconf/LookupObj/Index/list.inc.php",$fileindex);
			//$model->SetRules($conlist);
			
			$path = $model->GetDetailsPath();
			$filename = $path.'/'.$_POST['id'].".php";
			$model->SetSongleRule($filename,$conlist);
			//$fileindex[$_POST['id']] = $v['title'];
			
			$conflistfile = $model->GetConflistFile();
			$conflist = require $conflistfile;
			$conflist[$_POST['id']] = $v['title'];
			//$conflist = array_merge($conflist,$fileindex);
			$model->SetSongleRule($conflistfile,$conflist);
			$this->success('操作成功');
		}else{
			$this->error("更改数据库失败");
		}
	}
	//初始化lookupobj-->将数据库数据转换为配置文件
	//删除原文件 重生成 不同数据库数据不一样 切换数据库是一定注意要初始化
	public function initializeLookupObj(){
		$model = D ( $this->getActionName () );
		// 删除原有的文件夹
		if (is_dir ( Dynamicconf . "/LookupObj" )) {
			import ( '@.ORG.xhprof.FileUtil' );
			$fileUtil = new FileUtil ();
			$fileUtil->unlinkDir ( Dynamicconf . "/LookupObj" );
		}
		$lookupobjlist = $model->where ( 'status=1' )->select ();
		
		
		
// 		print_r(count($lookupobjlist));
// 		echo 99;
// 		exit;
		$conlist = array ();
		$path = $model->GetDetailsPath ();
		if (! file_exists ( $path )) {
			createFolder ( $path );
		}
		foreach ( $lookupobjlist as $k => $v ) {
			if ($v ['viewtype'] == 1) {
				$viewmasid = getFieldBy ( $v ['viewname'], "name", "id", "mis_system_dataview_mas" );
				$chinaarr = array ();
				if($viewmasid){
					$viewsub = M ( "mis_system_dataview_sub" )->field ( "otherfield,title" )->where ( "masid=" . $viewmasid )->select ();
					$fieldsarr = explode ( ',', $v ['fields'] );
					foreach ( $fieldsarr as $key2 => $val2 ) {
						foreach ( $viewsub as $key1 => $val1 ) {
							if ($val2 == $val1 ['otherfield']) {
								$chinaarr [$val1 ['otherfield']] = $val1 ['title'] ? $val1 ['title'] : $val1 ['otherfield'];
							}
						}
					}
				}
				$conlist [$v ['id']] = array (
						'title' => $v ['title'],
						'fields' => $v ['fields'],
						'fields_china' => $chinaarr, // $this->getDeatilshowname($v['mode'],$v['fields'],'','china'),
						'checkforfields' => $v ['checkforfields'],
						'fieldcom' => $v ['fieldcom'],
						'listshowfields' => $v ['listshowfields'],
						'listshowfields_china' => $this->getDeatilshowname ( $v ['mode'], $v ['listshowfields'], '', 'china' ),
						'funccheck' => $v ['funccheck'],
						'funccheck_china' => $this->getDeatilshowname ( $v ['mode'], $v ['funccheck'], '', 'china' ),
						'funcinfo' => unserialize ( base64_decode ( $v ['funcinfo'] ) ),
						'url' => $v ['url'],
						'mode' => $v ['mode'], // 屈强@2014-08-07 新增标题项
						'checkformodel' => $v ['checkformodel'],
						'filed' => $v ['filed'],
						'filed1' => $v ['filed1'],
						'val' => $v ['val'],
						'showrules' => $v ['showrules'],
						'rulesinfo' => $v ['rulesinfo'],
						'rules' => $v ['rules'],
						'viewname' => $v ['viewname'],
						'viewtype' => $v ['viewtype'],
						'condition' => $v ['rules'],
						'dialogwidth' => $v ['dialogwidth'],
						'dialogheight' => $v ['dialogheight'],
						'status' => $v ['status'],
						'level' => $v ['level'] ,
						'datasort' =>$v['datasort'],
						//'customstatus' => $v['customstatus'],
						'conftype'=>$v['conftype']?$v['conftype']:0
						
				) // 屈强@2014-08-07 新增当前项权限
;
			} else {
				$conlist [$v ['id']] = array (
						'title' => $v ['title'],
						'fields' => $v ['fields'],
						'fields_china' => $this->getDeatilshowname ( $v ['mode'], $v ['fields'], '', 'china' ),
						'checkforfields' => $v ['checkforfields'],
						'fieldcom' => $v ['fieldcom'],
						'listshowfields' => $v ['listshowfields'],
						'listshowfields_china' => $this->getDeatilshowname ( $v ['mode'], $v ['listshowfields'], '', 'china' ),
						'funccheck' => $v ['funccheck'],
						'funccheck_china' => $this->getDeatilshowname ( $v ['mode'], $v ['funccheck'], '', 'china' ),
						'funcinfo' => unserialize ( base64_decode ( $v ['funcinfo'] ) ),
						'url' => $v ['url'],
						'mode' => $v ['mode'], // 屈强@2014-08-07 新增标题项
						'checkformodel' => $v ['checkformodel'],
						'filed' => $v ['filed'],
						'filed1' => $v ['filed1'],
						'val' => $v ['val'],
						'showrules' => $v ['showrules'],
						'rulesinfo' => $v ['rulesinfo'],
						'rules' => $v ['rules'],
						'viewname' => $v ['viewname'],
						'viewtype' => $v ['viewtype'],
						'condition' => $v ['rules'],
						'dialogwidth' => $v ['dialogwidth'],
						'dialogheight' => $v ['dialogheight'],
						'status' => $v ['status'],
						'level' => $v ['level'] ,
						'datasort' =>$v['datasort'],
						//'customstatus' => $v['customstatus'],
						'conftype'=>$v['conftype']?$v['conftype']:0
				) // 屈强@2014-08-07 新增当前项权限
;
			}
			if($v['dtinfo']){
				$conlist[$v ['id']]['dt']=unserialize(base64_decode($v['dtinfo']));
				$conlist['versions'] = '1.0';
			}else if ($v ['datetable']) {
				$conlist [$v ['id']] ['proid'] = $v ['proid'];
				$conlist [$v ['id']] ['fieldback'] = $v ['fieldback'];
				if($v['orderfield']){
					$conlist [$v ['id']] ['orderfield'] = $v ['orderfield'];
					$conlist [$v ['id']] ['ordersort'] = $v ['ordersort'];					
				}
				// $conlist[$v['id']]['fieldcom']=$v['fieldcom'];
				$conlist [$v ['id']] ['dt'] = unserialize ( base64_decode ( $v ['datetable'] ) );
				if($v['dtfuncfields']){
					$conlist['dtfuncfields'] = $v['dtfuncfields'];
					$conlist['dtfuncinfo'] = 	$v['dtfuncinfo'];
				}
			}
			$filename = $path . "/" . $v ['id'] . ".php";
			$model->SetSongleRule ( $filename, $conlist [$v ['id']] );
			$fileindex [$v ['id']] = $v ['title'];
		}
		$pathconf = DConfig_PATH . '/LookupObj/Index';
		if (! file_exists ( $pathconf )) {
			createFolder ( $pathconf );
		}
		$num = $model->SetSongleRule ( $pathconf . '/list.inc.php', $fileindex );
		$model->commit ();
		if ($num) {
			$json = '{"status":1,"statusCode":1,"navTabId":null,"message":"\u64cd\u4f5c\u6210\u529f","forward":null,"forwardUrl":null,"callbackType":null,"data":"","checkfield":null,"refreshtabs":null,"rel":null,"redalert":0}';
			echo '<script>reloadindexview(' . $json . ')</script>';
			exit ();
		} else {
			$this->error ( '转换失败' );
		}
		// $this->success('操作成功');
	}
	//老配置文件 url全加s
	function updateconfigurl(){
		$model=D($this->getActionName());
		$details = $model->GetLookupDetails();
		$path = $model->GetDetailsPath();
		foreach($details as $k=>$v){
			$details[$k]['url'] = 'lookupGenerals';
			$filename = $path.'/'.$k.'.php';
			$model->SetSongleRule($filename,$details[$k]);
		}
		
	}
	function initializeModel(){
		$id = $_REQUEST['id'];
		if($id=="{sid_node}"){
			$this->error('请选择一条的记录');
// 			$json='{"status":0,"statusCode":0,"navTabId":null,"message":"dd","forward":null,"forwardUrl":null,"callbackType":null,"data":false,"checkfield":null,"refreshtabs":null,"rel":null,"redalert":0}';
// 			echo '<script>reloadindexview('.$json.')</script>';
// 			exit;
		}
		$lookupmodel = D("LookupObj");		
		$detail = $lookupmodel->GetLookupDetail($id);
		$modelname = $detail['mode'];
		$file = DConfig_PATH.'/Models/'.$modelname.'/list.inc.php';
		$filelist = require $file;
		if($filelist){
			$newdetail=array();
			foreach($filelist as $k=>$v){
				$newdetail[$v['name']] = $v;
			}
			
			$num = $lookupmodel->SetSongleRule($file,$newdetail);
		}

		if($num){
			$json = '{"status":1,"statusCode":1,"navTabId":null,"message":"\u64cd\u4f5c\u6210\u529f","forward":null,"forwardUrl":null,"callbackType":null,"data":"","checkfield":null,"refreshtabs":null,"rel":null,"redalert":0}';
			echo '<script>reloadindexview('.$json.')</script>';
			exit;
		}else{
			$this->error('转换失败');
		}
		
	}
}

?>