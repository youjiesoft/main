<?php
class MisSystemDataUpdateManageAction extends CommonAction{
	public function _filter(&$map) {
		if($_REQUEST['showtype']==1){
				$map['modelname'] = $_REQUEST['model'];
// 				if($_REQUEST['id']){
// 					$map['tabledataid'] = $_REQUEST['id'];
// 				}
				
		}
	}
	public function _before_index(){
		if($_REQUEST['jump']){
			if($_REQUEST['showtype']==1){
				$map['tabledataid'] = $_REQUEST['id'];
				$map['modelname'] = $_REQUEST['model'];
				$this->assign("modelname",$_REQUEST['model']);
				$this->assign('showtype',$_REQUEST['showtype']);
			}elseif($_REQUEST['model']){
				$modelName = $_REQUEST['model'];
				$model = D($modelName);
				$tablename = $model->getTableName();
				$this->assign('modelname',$modelName);
				$userid = $_SESSION[C("USER_AUTO_KEY")];
				$this->lookupGetDetailList($modelName,$userid);
				exit;
			}else{
				
			}
			
		}else{
			//$newAryRule = $aryRule;/*
			/* 建左边树
			 */
			$model = D('SystemConfigNumber');			
			//$list = $model->getRoleTree('MisDataWanderBox');
			$list = $model->getRoleTreeCache('MisSystemDataUpdateManageBox',$url='__URL__/index',$map2=array (),$modelname=false,$target='ajax',$title='',"MisSystemDataUpdateManage",$type='');
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
	public function lookupGetDetailList($modelname='',$userid=''){
		$name=$modelname?$modelname:$_POST['modelname'];
		$userid=$userid?$userid:$_POST['userid'];
		$this->assign('modelname',$name);
		$this->assign('userid',$userid);
		//获取模型 权限内列表 ----开始-----
		$map = $this->_search ($name);
		$nameAction = A($name);
		if (method_exists ( $nameAction, '_filter' )) {
			$nameAction->_filter ( $map );
		}
		if($_REQUEST['fieldtype']){
			$this->getBindSetTables($map);
		}
		if($_REQUEST['projectid']){
			$map['projectid'] = $_REQUEST['projectid'];
		}
		if($_REQUEST['projectworkid']){
			//$map['projectworkid'] = $_REQUEST['projectworkid'];
		}
	
		if (! empty ( $name )) {
			$qx_name=$name;
			if(substr($name, -4)=="View"){
				$qx_name = substr($name,0, -4);
			}
			//验证浏览及权限
			if( !isset($_SESSION['a']) ){
				$map=D('User')->getAccessfilter($qx_name,$map);
			}
			//列表页排序 ---开始-----2015-08-06 15:07 write by xyz
	
			if($_REQUEST['orderField']&&strpos(strtolower($_REQUEST['orderField']),' asc')===false&&strpos(strtolower(strpos($_REQUEST['orderField'])),' desc')===false){
				$this->_list ( $name, $map);
			}else{
				$sortsorder = '';
				$sortsmap['modelname'] = $name;
				$sortsmap['sortsorder'] = array("gt",0);
				//管理员读公共设置
				if($_SESSION['a']){
					$listincModel = M("mis_system_public_listinc");
					$sortslist = $listincModel->where($sortsmap)->order("sortsorder")->select();
				}else{
					//个人先读个人设置、没有再读公共设置
					$sortsmap['userid'] = $_SESSION [C ( 'USER_AUTH_KEY' )];
					$listincModel = M("mis_system_private_listinc");
					$sortslist = $listincModel->where($sortsmap)->order("sortsorder")->select();
					if(empty($sortslist)){
						unset($sortsmap['userid']);
						$listincModel = M("mis_system_public_listinc");
						$sortslist = $listincModel->where($sortsmap)->order("sortsorder")->select();
					}
				}
				//如果在设置里有相关数据、提取排序字段组合order by
				if($sortslist){
					foreach($sortslist as $k=>$v){
						$sortsorder .= $v['fieldname'].' '.$v['sortstype'].',';
					}
					$sortsorder = substr($sortsorder,0,-1);
				}
				//列表页排序 ---结束-----
				$this->_list ( $name, $map,'', false,'','',$sortsorder);
	
			}
				
	
		}
		//获取模型权限内列表 ---结束----
	
		//获取模型list配置文件 ----开始----
		//begin
		$scdmodel = D('SystemConfigDetail');
		//读取列名称数据(按照规则，应该在index方法里面)
		$detailList = $scdmodel->getDetail($name,true,'','sortnum');
		if(file_exists(ROOT . '/Dynamicconf/Models/'.$name.'/form.inc.php')){
			$anameList = require ROOT . '/Dynamicconf/Models/'.$name.'/form.inc.php';
			if(!empty($detailList) && !empty($anameList)){
				foreach($detailList as $k => $v){
					$detailList[$k]["datatable"] = 'template_key=""';
					foreach($anameList as $kk => $vv){
						if($k==$kk){
							$detailList[$k]["datatable"] = $vv["datatable"];
						}
					}
				}
			}
		}
		//print_r($detailList);
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		//获取模型list配置文件 ----结束----
	
		//获取已经授权的数据
		$contentarr = array();
		if($_POST['content']){
			$contentarr = explode(",",$_POST['content']);
		}
		$this->assign("model",$modelname);
		$this->assign('content',$content);
		$this->assign('contentarr',$contentarr);
		$this->assign('isall',$_POST['isall']);
		$this->assign('id',$_POST['id']);
		$this->display("updatedata");
	}
	public function edit(){
		//对应数据的模型
		$name = $_REQUEST['model'];
		$formid = getFieldBy($name,"actionname","id","mis_dynamic_form_manage");
		$a = A("MisSystemDataUpdateTemlete");
		$dd = $a->test($name,$formid);
		try{
			$aa = A($name);
			$aa->edit();
			$a2 = $this->fetch($dd);//("{$name}:edit");
		}catch (Exception $e){
			$this->error($e->getMessage());
		}
		
		
		$this->assign("curModel",$name);
		$this->assign("a2",$a2);
 		$this->display();
		//$this->display();
	}
	
	function add(){
		$this->display();
	}
	 /**
	  * @Title: update
	  * @Description: todo(数据修改chuli)   
	  * @author 谢友志 
	  * @date 2015-9-23 上午9:21:05 
	  * @throws
	  */
	 
	function update(){
		$data = $_POST;
		$_REQUEST['id'] = $data['id'];
		//当前数据的模型名和表名
		$name = $data['curModelForDataManage'];
		$curTable = D($name)->getTableName();
		//获取对应内嵌表
		$datatablenamearr = array();
		$pModel = D("mis_dynamic_form_propery");
		$formid = getFieldBy($name,"actionname","id","mis_dynamic_form_manage");
		$pMap['formid'] = $formid;
		$pMap['formid'] = $formid;
		$pMap['status'] = 1;
		$pMap['category'] = array("eq","datatable");
		$pList = $pModel->field("id,fieldname,category,sort,title,dbname")->where($pMap)->select();
		foreach($pList as $key=>$val){
			$datatablenamearr[] = $val['dbname']."_sub_".$val['fieldname'];
		}
		
		
		// 获取修改前数据 -------开始-------		 
		//主表数据
		$mainList = D($name)->where("id='{$data["id"]}'")->find();
		//内嵌表数据
		$dList = $this->inlinedata($datatablenamearr,$data['id']);
		// 获取修改前数据 -------结束-------
		
		//修改数据
		$aa = A($name);
		$aa->update(true);

		// 获取修改后数据 -------开始-------
		//查询修改后主表数据
		$mainList2 = D($name)->where("id='{$data["id"]}'")->find();	
		//查询修改后内嵌表数据
		$dList2 = $this->inlinedata($datatablenamearr,$data['id']);
		// 获取修改后数据-------结束-------
		// 比较修改前后差异
		//不需要比较的字段 
		$comparefields = array("updatetime","updateid");	
		$list = array();
		foreach($dList as $key=>$val){
			foreach($val as $ke=>$va){
				foreach($va as $k=>$v){
					if(!in_array($k,$comparefields)&&$v !== $dList2[$key][$ke][$k]){
						$temp['beforeupdate'] = $v;
						$temp['afterupdate'] = $dList2[$key][$ke][$k];
						$list['inline'][$key][$ke][$k] =$temp;
						unset($temp);
					}
				}
			}
			
		}
		foreach($mainList as $mk=>$mv){
			if(!in_array($mk,$comparefields)&&$mv!==$mainList2[$mk]){
				$temp['beforeupdate'] = $mv;
				$temp['afterupdate'] = $mainList2[$mk];
				$list['main'][$curTable][$mk] = $temp;
				unset($temp);
			}
		}
		//有做修改入库mis_system_data_update_manage
		$dbdata = array();
		$dbdata['tabledataid'] = $data['id'];
		$dbdata['createtime'] = time();
		$dbdata['createid'] = $_SESSION[C("USER_AUTH_KEY")];
		$thisModel = D($this->getActionName());	
		if(!empty($list['main'])||!empty($list['inline'])){
			$dbdata['modelname'] = $name;
			foreach($list as $lk=>$lv){	
				if(!empty($lv)){
					switch ($lk){
						case 'main':
							$dbdata['tabletype'] = 1; //主表
							break;
						case 'inline':
							$dbdata['tabletype'] = 2; //内嵌表
							break;
						default:
							break;
					}
					foreach($lv as $ut=>$uv){
						$dbdata['tablename']=$ut;
						$dbdata['updateinfo'] = serialize($uv);
						$ret = $thisModel->data($dbdata)->add();
						if(false === $ret){
							$this->error();
						}
					}
				}
			}
		}
		$this->success();
	}
	/**
	 * @Title: inlinedata
	 * @Description: todo(获取表单对应id内嵌表数据) 
	 * @param unknown_type $datatablenamearr 内嵌表数组
	 * @param unknown_type $id 主数据id 
	 * @author 谢友志 
	 * @date 2015-9-23 上午9:17:19 
	 * @throws
	 */
	public function inlinedata($datatablenamearr,$id){
		$dList = array();
		if($datatablenamearr){
			foreach($datatablenamearr as $dk=>$dv){
				$dModel = D($dv);
				$dMap['masid'] = $id;
				$temp =  $dModel->where($dMap)->select();
				foreach($temp as $k=>$v){
					$dList[$dv][$v['id']] = $v;
				}
				unset($temp);
			}
		}
		return $dList;
	}
	
	public function view(){
		$name=$this->getActionName();
		$model = D($name);
		$rs = $model->where("id=".$_REQUEST['id'])->find();
		$rs['modelnameChina'] = getFieldBy($rs['modelname'],"actionname","actiontitle","mis_dynamic_form_manage");
		//表中文名
		$rs['tablenameChina'] = '';
		if($rs['tabletype'] == 2){
				$tablenameChina= $this->getInlineTableName($rs['tablename']);
		}else{
				$tablenameChina = getFieldBy($rs['modelname'],"actionname","actiontitle","mis_dynamic_form_manage");
		}
		$rs['tablenameChina'] = $tablenameChina;
		//配置文件
		$scdmodel = D('SystemConfigDetail');
		if($rs['tabletype'] == 1){
			$detailList = $scdmodel->getDetail($rs['modelname'],false);
		}else{
			$detailList = $scdmodel->getSubDetail($rs['modelname'],false);
		}
		$detaillists = array();
		foreach($detailList as $dk=>$dv){
			$detaillists[$dv['name']] = $dv;
		}
		//表字段中文名
		$dModel = D("Dynamicconf");
		$fields = $dModel->getTableInfo($rs['tablename']);
		$updateinfo = unserialize($rs['updateinfo']);
		foreach($updateinfo as $key=>$val ){
			if($rs['tabletype'] == 2){
				foreach($val as $k=>$v){
						if(count($detaillists[$k]['func'])>0){
							foreach($detaillists[$k]['func'] as $fk=>$fv){
								$v['beforeupdate'] = getConfigFunction($v['beforeupdate'],$fv,$detaillists[$k]['funcdata'][$fk]);
								$v['afterupdate'] = getConfigFunction($v['afterupdate'],$fv,$detaillists[$k]['funcdata'][$fk]);
							}
						}else{
							$v = $v;
						}
					foreach($v as $k1=>$v1){
						if($k1 =='beforeupdate'){
							$rs['beforeupdata'][$key][$k]['fieldname'] = $k;
							$rs['beforeupdata'][$key][$k]['fieldnameChina'] = $fields[$k]['COLUMN_COMMENT'];
							$rs['beforeupdata'][$key][$k]['data'] = $v1;
						}else{
							$rs['afterupdata'][$key][$k]['fieldname'] = $k;
							$rs['afterupdata'][$key][$k]['fieldnameChina'] = $fields[$k]['COLUMN_COMMENT'];
							$rs['afterupdata'][$key][$k]['data'] = $v1;
						}
						
					}
				}
			}else{
				if(count($detaillists[$key]['func'])>0){
					foreach($detaillists[$key]['func'] as $fk=>$fv){
						$val['beforeupdate'] = getConfigFunction($val['beforeupdate'],$fv,$detaillists[$key]['funcdata'][$fk]);	
						$val['afterupdate'] = getConfigFunction($val['afterupdate'],$fv,$detaillists[$key]['funcdata'][$fk]);
					}
				}else{
					$val = $val;
				}
					
					$rs['beforeupdata'][$rs['tabledataid']][$key]['fieldname'] = $key;
					$rs['beforeupdata'][$rs['tabledataid']][$key]['fieldnameChina'] = $fields[$key]['COLUMN_COMMENT'];
					$rs['beforeupdata'][$rs['tabledataid']][$key]['data'] = $val['beforeupdate'];
				
				
					$rs['afterupdata'][$rs['tabledataid']][$key]['fieldname'] = $key;
					$rs['afterupdata'][$rs['tabledataid']][$key]['fieldnameChina'] = $fields[$key]['COLUMN_COMMENT'];
					$rs['afterupdata'][$rs['tabledataid']][$key]['data'] = $val['afterupdate'];
				
			}
		}
		$tabletypec = array("1"=>"主表","2"=>"内嵌表");
		$this->assign("tabletypec",$tabletypec); 
		$this->assign("vo",$rs); 
		$this->display();
		exit;	
		//获取修改的主表id
		
		

		$map['tabledataid']= $rs['tabledataid'];
		$map['createtime'] = $rs['createtime'];
		$map['createid'] = $rs['createid'];
		$list = $model->where($map)->select();
		$tablearr = array();
		$tablenameChina = array();//表中文名
		foreach($list as $key=>$val){
			$list[$key]['updateinfo'] = unserialize($val['updateinfo']);
			$tablearr[] = $val['tablename'] ;//表名集合
			if($val['tabletye'] == 2){
				$tablenameChina[$val['tablename']]= $this->getInlineTableName($val['tablename']);
			}else{
				$tablenameChina[$val['tablename']]= getFieldBy($val['modelname'],"actionname","actiontitle","mis_dynamic_form_manage");
			}
			
		}
		//表字段中文名
		$dModel = D("Dynamicconf");
		$tablefieldsChina = array();
		foreach($tablearr as $v){		
			$tablefield = $dModel->getTableInfo($v);
			$newfield = array();
			foreach($tablefield as $key=>$val){
				$newfield[$key] = $val['COLUMN_COMMENT'];
			}
			$tablefieldsChina[$v] = $newfield;
		}
		//配置文件
		$scdmodel = D('SystemConfigDetail');
		if($rs['tabletype'] == 1){
			$detailList = $scdmodel->getDetail($rs['modelname'],false);
		}else{
			$detailList = $scdmodel->getSubDetail($rs['modelname'],false);
		}
		
		$detaillists = array();
		foreach($detailList as $dk=>$dv){
			$detaillists[$dv['name']] = $dv;
		}
		print_r($detailList);
		
		//修改记录数据处理为中文显示
		$clist = array();
		$tabletypec = array("1"=>"主表","2"=>"内嵌表");
		foreach($list as $key=>$val){
			$updateinfo = unserialize($val['updateinfo']);
			$clist[$key]['modelname'] = getFieldBy($val['modelname'],'name','title','node');
			$clist[$key]['tablename'] = $tablenameChina[$val['tablename']];
			$clist[$key]['tabletype'] = $tabletypec[$val['tabletype']];
			$clist[$key]['masid'] = $val['id'];
			foreach($updateinfo as $k=>$v){
				if($val['tabletype'] == 1){
					$clist[$key]['data'];
				}else{
					foreach($v as $k1=>$v1){
						$clist[$key]['data']['id'] = $k1;
						foreach($v1 as $k2=>$v2){
							
							foreach($v2 as $k3=>$v3){
								if(count($detaillists[$k2]['func'])>0){
									$clist[$key]['data']['id'] [$k2][$k3] = getConfigFunction($v3,$nam,$detaillists[$k2]['funcdata'][$key]);
								}else{
									$clist[$key]['data']['id'] [$k2][$k3] = $v3;
								}
							}
						}
					}
				}
			}
		}
		
		
		$
		
		
		$this->display();
		exit;
	}
	
	public function getInlineTableName($tablename){
		$proid = getFieldBy($tablename,"tablename","propertyid","mis_dynamic_form_datatable");
		$title = getFieldBy($proid,'id','title','mis_dynamic_form_propery');
		return $title;
	}
	
	
	
	public function aaa(){
		$idarr = array();
		
		$chuchaiModel = M("mis_auto_xeyis"); //出差
		$sql2 = "
				SELECT
					mis_auto_xeyis.id
				FROM
					mis_auto_xeyis
				WHERE
					mis_auto_xeyis.orderno NOT IN (
						SELECT
							x.orderno
						FROM
							mis_auto_xeyis AS x
						LEFT JOIN mis_auto_jevyr AS j ON x.orderno = j.laiyuandanhao where j.laiyuanmoxing='MisAutoCdk' and j.leibie='出差'
					)
				";
		
		$gonggaoModel = M("mis_auto_jevyr");
		$sourcemodel='MisAutoCdk'; //出差
		$ids = M()->query($sql2);
		foreach($ids as $ik=>$iv){
			$idarr[]=$iv['id'];
		}
		$idarr = array("7","11","33");
		$map['id'] = array("in",$idarr);
		$clist = $chuchaiModel->where($map)->select();
		//出差漫游字段
		$roamModel = M("mis_system_data_roam_sub");
		$masid = 1004;
		$roamlist =  $roamModel->where("masid=".$masid)->select();
		$newlist = array();
		foreach($roamlist as $key=>$val){
			$newlist['sourcefield'][]= $val['sfield'];
			$newlist['targetfield'] .= $newlist['targetfield']?",".$val['tfield']:$val['tfield'];
		}
		foreach($clist as $k){
			$id = $k['id'];
			$list2 = array();
			foreach($newlist['sourcefield'] as $sk=>$sv){
				$list2[] = "'".$k[$sv]."'";
			}
			$strsource = implode(",",$list2);
		
			$sql = "INSERT INTO mis_auto_jevyr ({$newlist['targetfield']}) values ({$strsource})";
			$tModel = M();
			$tModel->execute($sql);
			echo $tModel->getlastsql();
			exit;
		}
		$tModel->commit();
		exit;
		
		
	}
	
	
	
	
	
	
	  
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}