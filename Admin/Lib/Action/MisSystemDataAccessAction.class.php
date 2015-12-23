<?php
/**
 * @Title: MisSystemDataAccessAction 
 * @Package package_name
 * @Description: todo(数据权限字段及分组) 
 * @author谢友志 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015-6-1 下午2:58:37 
 * @version V1.0
 */
class MisSystemDataAccessAction extends CommonAction{
	public function _filter (&$map ){
		//左侧树 分类 按角色 或 人员
		if($_REQUEST['aname']){
			$map['actionname'] = array("eq",$_REQUEST['aname']);
		}
	}
	function _before_index(){
		if(empty($_REQUEST['jump'])){
		$MisDynamicFormManageModel=D('MisDynamicFormManage');
		$typeTree=$MisDynamicFormManageModel->getAnametree("MisSystemDataAccessBOX",'','jump');
		$this->assign("typeTree",json_encode($typeTree));
		$this->assign("uilid",$typeTree[0]['id']);		
		}
		$_REQUEST['aname']=$_REQUEST['aname'];
	}
	function _before_add(){
		$MisDynamicFormManageModel=D('MisDynamicFormManage');
		$list=$MisDynamicFormManageModel->getAnametree("MisDynamicFormManageview");
		foreach($list as $key=>$val){
			if($val['id']<=0){
				unset($list[$key]);
			}
		}
		$this->assign('list',$list);
		
	}
	function edit(){
		//获取所有模型
		$MisDynamicFormManageModel=D('MisDynamicFormManage');
		$list=$MisDynamicFormManageModel->getAnametree("MisDynamicFormManageview");
		foreach($list as $key=>$val){
			if($val['id']<=0){
				unset($list[$key]);
			}
		}
		$this->assign('list',$list);
		//获取编辑纪录的数据
		$masmodel = D($this->getActionName());
		$vo = $masmodel->where("id={$_REQUEST['id']}")->find();
		//查询是否有分组、分配情况
		$submodel = M("mis_system_data_access_sub");
		$sublist = $submodel->where("masid=".$_REQUEST['id'])->find();
		$groupmodel = M("mis_system_data_access_group");
		$grouplist = $groupmodel->where("masid=".$_REQUEST['id'])->find();
		$havesub = 0;
		if($sublist||$grouplist){ //
			$havesub = 1;
		}
		$this->assign("subid",$sublist['id']);
		$this->assign("havesub",$havesub);
		$this->assign("vo",$vo);
		$this -> assign('jsonvo',json_encode($vo));
		$this->display();
	}
	function _before_insert(){
		$_POST['formid'] = getFieldBy($_POST['actionname'],'actionname','id','mis_dynamic_form_manage');
		if(strpos($_POST['table'],"_sub_")>0){
			//内嵌表
			$_POST['type'] = 2;
			$datamodel = M("mis_dynamic_form_datatable");
			$dmap['tablename'] = $_POST["table"];
			$dmap['fieldname'] = $_POST["field"];
			$dmap['formid'] = $_POST["formid"];
			$rs = $datamodel->where($dmap)->find();
			$_POST['propertyid'] = $rs['propertyid'];
			$_POST['datatableid'] = $rs['id'];
		}else{
			//主表
			$_POST['type'] = 1;
			$promodel = M("mis_dynamic_form_propery");
			$pmap['formid'] = $_POST['formid'];
			$pmap['fieldname'] = $_POST['field'];
			$pmap['status'] = 1;
			$ps = $promodel->where($pmap)->find();			
			$_POST['propertyid']  = $ps['id'];
		}
			
	}
	
	public function _before_update(){
		$_POST['formid'] = getFieldBy($_POST['actionname'],'actionname','id','mis_dynamic_form_manage');
		if(strpos($_POST['table'],"_sub_")>0){
			//内嵌表
			$_POST['type'] = 2;
			$datamodel = M("mis_dynamic_form_datatable");
			$dmap['tablename'] = $_POST["table"];
			$dmap['fieldname'] = $_POST["field"];
			$dmap['formid'] = $_POST["formid"];
			$rs = $datamodel->where($dmap)->find();
			$_POST['propertyid'] = $rs['propertyid'];
			$_POST['datatableid'] = $rs['id'];
		}else{
			//主表
			$_POST['type'] = 1;
			$promodel = M("mis_dynamic_form_propery");
			$pmap['formid'] = $_POST['formid'];
			$pmap['fieldname'] = $_POST['field'];
			$pmap['status'] = 1;
			$ps = $promodel->where($pmap)->find();
			$_POST['propertyid']  = $ps['id'];
		}
	}
	function _after_update($num){
		if($num){
			$id = $_POST['id'];
			$name = $this->getActionName();
			$model = D($name);
			$list = $model->fieldConfigToList($id);
			$data['accesscontentsource']=$list['listsource'];
			$data['accesscontentsave']=$list['sourcesave'];
			$data['accesscontentshow']=$list['sourceshow'];
			$data['accesscontentcategory']=$list['sourcetype'];
			$model->where("id={$id}")->save($data);
			//数据源验证 一个模型只能对同一数据源配置一次
			if(!$data['accesscontentsource']||!$data['accesscontentsave']||!$data['accesscontentcategory']){
				$this->error("数据缺失");
			}
			$smap = array();
			$smap['actionname'] = $_POST['actionname'];
			$smap['id'] = array('neq',$id);
			if($data['accesscontentcategory'] == 1){
				//配置文件
				$smap['accesscontentsave'] = $data['accesscontentsave'];
			}else{
				//数据表
				$smap['accesscontentsource'] = $data['accesscontentsource'];
			}
			$slist = $model->where($smap)->find();
			if($slist){
				$this->error("一个模型只能对同一数据源配置一次");
			}
		}
		//删除浏览级别权限
		$obj_dir = new Dir;
		$directory =  DConfig_PATH."/BrowsecList";
		if(isset($directory)){
			$obj_dir->del($directory);
		}
	}
	/**
	 * @Title: _after_insert
	 * @Description: todo(反写字段来源信息) 
	 * @param unknown_type $list  
	 * @author 谢友志 
	 * @date 2015-6-11 下午6:29:05 
	 * @throws
	 */
	function _after_insert($id){
		$name = $this->getActionName();
		$model = D($name);
		$list = $model->fieldConfigToList($id);
		$data['accesscontentsource']=$list['listsource'];
		$data['accesscontentsave']=$list['sourcesave'];
		$data['accesscontentshow']=$list['sourceshow'];
		$data['accesscontentcategory']=$list['sourcetype'];
		$model->where("id={$id}")->save($data);
		//数据源验证 一个模型只能对同一数据源配置一次
		if(!$data['accesscontentsource']||!$data['accesscontentsave']||!$data['accesscontentcategory']){
			$this->error("数据缺失");
		}
		$smap = array();
		$smap['actionname'] = $_POST['actionname'];
		$smap['id'] = array('neq',$id);
		if($data['accesscontentcategory'] == 1){
			//配置文件
			$smap['accesscontentsave'] = $data['accesscontentsave'];
		}else{
			//数据表
			$smap['accesscontentsource'] = $data['accesscontentsource'];
		}
		$slist = $model->where($smap)->find();
		if($slist){
			$this->error("一个模型只能对同一数据源配置一次");
		}
		//删除浏览级别权限
		$obj_dir = new Dir;
		$directory =  DConfig_PATH."/BrowsecList";
		if(isset($directory)){
			$obj_dir->del($directory);
		}
	}
	/**
	 * @Title: allotgroup
	 * @Description: todo(数据分组)   
	 * @author 谢友志 
	 * @date 2015-6-6 下午5:07:29 
	 * @throws
	 */
	function allotgroup(){
		$groupmodel = M("mis_system_data_access_group");
		$id = $_GET['id'];
		if($_REQUEST['subid']){
			$id = $_REQUEST['masid'];
			$subid = $_REQUEST['subid'];
		}
		$name = $this->getActionName();
		$model = D($name);
		//主表信息
		$maslist = $model->where("id={$id}")->find();
		if($maslist['accesscontenttype']==1){
			$this->error("直接授权不用分组");
		}
		$this->assign("maslist",$maslist);
		
		//获取对应表的字段信息(可选列表)
		$list = $model->fieldConfigToList($id);
		
		//获取字段已分组信息		
		
		//组成员
		$groupmodel = M("mis_system_data_access_group");
		$grouplist = $groupmodel->where("masid=".$maslist['id'])->order("orderno")->select();
		foreach($grouplist as $k=>$v){
			if($v['member']){
				$t = explode(",",$v['member']);
				foreach($t as $k1=>$v1){
						$t[$k1] = str_replace("'","",$v1);
				}
				$grouplist[$k]['member'] = $t;
			}else{
				$grouplist[$k]['member'] = array();
			}
		}
		//来源字段信息
		$mlist = array(); 
		if($maslist['accesscontentcategory'] == 2){ //数据表
			$mModel = M($maslist['accesscontentsource']);
			$mlist = $mModel->getField("{$maslist['accesscontentsave']},{$maslist['accesscontentshow']}");
			//echo $mModel->getlastsql();		
		}else{//配置文件
			$file = D("Selectlist")->GetFile();
			$conflist = require $file;
			$mlist = $conflist[$maslist['accesscontentsave']][$maslist['accesscontentsave']];			
		}
		//对组成员进行编译，加入显示字段
		foreach($grouplist as $key=>$val){
			$subid = $subid?$subid:$val['id'];
			if($val['member']){
				$temp = array();
				foreach($val['member'] as $k=>$v){
					$temp[$k]['name'] = $v;
					$temp[$k]['showname'] = $mlist[$v];
				}
				$grouplist[$key]['memberinfo'] = $temp;
			}else{
				$grouplist[$key]['memberinfo'] = array();
			}
		}
		foreach($grouplist as $gk=>$gv){
			$tempgroup[$gv['id']] = $gv;
		}
		
		$grouplist = $tempgroup;
		//print_r($list);
		if($list['treetype']){//树形菜单分组
			
			//获取ztree树形
			$newdepartmentlist = array();
			foreach ($list as $lkey=>$lval){
				foreach ($lval as $vkey=>$vval){
					
					$newdepartmentlist[]=array(
							
							'id'=>$vval['id'],
							'name'=>$vval[$list['sourceshow']],
							'parentid'=>$vval[$list['treetype']],
							'checked'=>$checked,
					);
				}
			}
			$param['url']= __URL__."/index/jump/jump/hy/#id#";
			$param['rel']= "abc";
			$param['open']= "true";
			$param['isParent']="true";
			
			$arr = $this->getTree($newdepartmentlist,$param);
			$decodearr = json_decode($arr,true);
			$members = getFieldBy($subid,"id","treemember","mis_system_data_access_group");
			$memberarr = explode(",",$members);
			foreach($decodearr as $k=>$v){
				$checked = false;
				if(in_array($v['id'],$memberarr)){
					$decodearr[$k]['checked']=true;
				}
			}
			$arr = json_encode($decodearr);
			$this->assign("ztree",$arr);
		}

		$this->assign("subid",$subid);
		$this->assign("maslist",$maslist);
		$this->assign("grouplist",$grouplist);
		$this->assign("list",$list);
		$smap['masid'] = $id;
		$rs = M("mis_system_data_access_sub")->where($smap)->find();
		$subhaving = '';
		if($rs){
			$subhaving = '1';
		}
		$this->assign("subhaving",$subhaving);
		//print_r($list);
		if($list['treetype']){
			//带树形页面
			if($_REQUEST['masid']){
				$this->display("allotgrouptree");
			}else{
				$this->display("allotgroupnew");
			}
			exit;
		}elseif($_REQUEST['masid']){
			$this->display("allotgrouplist");
			exit;
		}else{
			//普通页面
			$this->display("allotgroupnew");
			exit;
		}
	}
	/**
	 * @Title: allotgroupinsert
	 * @Description: todo(新增、修改组成员)   
	 * @author 谢友志 
	 * @date 2015-6-26 下午9:06:51 
	 * @throws
	 */
	public function allotgroupinsert(){
		$data = $_POST;
		$model=M("mis_system_data_access_group");

		$newdata = array();
		//对传递过来的数据进行入库操作
		if($_POST['typetree']){
			$rs = M("mis_system_data_access_mas")->where("id={$data['masid']}")->find();
			$data['member'] = explode(",",$data['treenode']);
			$newdata['treemember'] = $data['member'];
			$newdata['treemember'] = implode(",",array_filter($newdata['treemember']));
			if($rs['accesscontentsave']!="id"){				
				foreach($data['member'] as $k=>$v){	
					if(empty($v)){
						unset($data['member'][$k]);
						continue;
					} 			
					$data['member'][$k] = "'".getFieldBy($v,'id',$rs['accesscontentsave'],$rs['accesscontentsource'])."'";
				}		
			}else{ 
				foreach($data['member'] as $k=>$v){	
					if(empty($v)){
						unset($data['member'][$k]);
						continue;
					}			
					$data['member'][$k] = "'".$v."'";
				}
			}
			
		}else{
			foreach($data['member'] as $k=>$v){
				if(empty($v)){
						unset($data['member'][$k]);
						continue;
					} 
				$data['member'][$k] = "'".$v."'";
			}
		}
		$newdata['name'] 			= $data['name'];
		$newdata['orderno'] 		= $data['orderno'];
		$newdata['masid'] 			= $data['masid'];
		$newdata['startstatus'] 	= $data['startstatus']?$data['startstatus']:1;
		$newdata['member'] 	= implode(",",$data['member']);
		$newdata['id'] = $data['id'];
		$ret = $model->where("id=".$data['id'])->save($newdata);
		//对这个分组对应字段进行过分配的话，取消分配
// 		$smap['masid'] = $data['masid'];
// 		$submodel = M("mis_system_data_access_sub");
// 		$rs = $submodel->where($smap)->find();
// 		if($rs){
// 			$submodel->where($smap)->delete();			
// 		}
		//删除浏览级别权限
		$obj_dir = new Dir;
		$directory =  DConfig_PATH."/BrowsecList";
		if(isset($directory)){
			$obj_dir->del($directory);
		}
		if(false===$ret){
			$this->error("操作失败");
		}
		$this->success("操作成功");
		
	}
	/**
	 * @Title: access
	 * @Description: todo(授权)   
	 * @author 谢友志 
	 * @date 2015-6-6 下午3:40:30 
	 * @throws
	 */
	function access(){
		if($_REQUEST['type'] == 1){
			//直接授权
			print_r($_REQUEST);
			$this->display();
		}else{
			//分组授权
		}
	}
	function groupaccess(){
		
	}
	/**
	 * @Title: gettables
	 * @Description: todo(获取模型下的数据表---过渡方法) 
	 * @param unknown_type $model
	 * @return unknown  
	 * @author 谢友志 
	 * @date 2015-6-2 下午3:09:26 
	 * @throws
	 */
	function gettables($model){
 		$model = $model?$model:$_REQUEST['model'];		
		$list = $this->lookuptablelist($model);
		if($_REQUEST['model']){
			$list = json_encode($list);
			echo $list;
		}else{
			return $list;
		}
		
	}
	/**
	 * @Title: gettablefields
	 * @Description: todo(获取数据表对应<单、多选，下拉，lookup>字段及中文名) 
	 * @param string $table 数据表
	 * @param string $model 数据表所属model
	 * @return unknown  
	 * @author 谢友志 
	 * @date 2015-6-2 上午9:39:28 
	 * @throws
	 */
	function gettablefields($table,$model){
		$table = $table?$table:$_POST['table'];
		$model = $model?$model:$_POST['model'];
		if(empty($table)) return $this->error("缺少对象数据表");
		if(empty($model)) return $this->error("缺少对象模型");
		$formid = getFieldBy($model,'actionname','id','mis_dynamic_form_manage');
		$promodel = M("mis_dynamic_form_propery");
		$datamodel = M("mis_dynamic_form_datatable");
		//判断是否是内嵌表,内嵌表查mis_dynamic_form_datatable，主表查mis_dynamic_form_propery
		$filedlist=array();
		if(strpos($table,"_sub_")>0){
			$dmap['formid'] 		= $formid;
			$dmap['tablename'] 		= $table;
			$dmap['category'] 		= array("in","select,lookup,checkbox,radio");
			$fieldlist = $datamodel->where($dmap)->getField('fieldname,fieldtitle');
		}else{
			$map['category'] 	= array("in","select,lookup,checkbox,radio");
			$map['formid'] 		= $formid;
			$map['status'] 		= 1;
			$fieldlist = $promodel->where($map)->getField('fieldname,title');
		}
		if($_POST['table']&&$_POST['model']){
			echo json_encode($fieldlist);
		}else{
			return $filedlist;
		}
	}
	/**
	 *
	 * @Title: lookuptablelist
	 * @Description: todo(查找模型下的主表、附属表)
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
	/**
	 * @Title: addzu
	 * @Description: todo(ajax添加组)
	 * @author 谢友志
	 * @date 2015-6-26 下午2:05:31
	 * @throws
	 */
	public function addzu(){
		$model = M("mis_system_data_access_group");
		$data['masid'] = $_POST['masid'];
		$data['name'] = $_POST['name'];
		$data['orderno'] = $_POST['orderno'];		
		$rs = $model->where($data)->find();
		
		$list = array();
		$list['msg']=1;
		if($rs){
			$this->error("名称重复");
		}else{
			$model->add($data);
			$model->commit();
			$list['data'] = $model->where("masid=".$_POST['masid'])->select();
			$this->success("操作成功");
		}
	}
	
	public function addItem(){
		$this->assign("masid",$_REQUEST['masid']);
		$this->display("additem");
	}
	public function deleteCarrierForId(){
		$map['masid'] = $_POST['id'];
		$model = M("mis_system_data_access_sub");
		$model->where($map)->delete();
		$model->commit();
	}
}