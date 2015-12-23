<?php
class MisSystemDataAccessCarrierAction extends CommonAction{
	public function _filter (&$map ){
		//左侧树 分类 按角色 或 人员
		if($_REQUEST['id']){
			$map['objid'] = array("eq",$_REQUEST['id']);
			$map['objtype'] = array("eq",$_REQUEST['type']);
		}
	}
	public function _before_index(){
		//左侧树（暂时只定义人员、角色）
		$rel = "MisSystemDataAccessCarrierBOX";
		$model = D($this->getActionName());
		$typeTree = $model->userAndRolegroupToTree($rel);
		//print_r($typeTree);{$uilid}
		$this->assign("typeTree",json_encode($typeTree));
		//$this->assign("uilid",1);
		
		if($_REQUEST['id']){
			$model = M("mis_system_data_access_mas");
			$list = $model->select();
			$this->assign("datalist",$list);
			//获取模型
// 			$nodemodel = M("node");
// 			$modellist = $nodemodel->field("id,name,title")->where("status=1 and level=3")->select();
			//获取模型 更改为mas表里的数据模型
			$masModel = M("mis_system_data_access_mas");
			$mmap['startstatus'] = 1;
			$mmap['status'] = 1;
			$rs = $masModel->distinct(true)->field("actionname")-> where($mmap)->select();//getField("id,actionname,actiontitle");
			$sql = "select id,actionname as name,actiontitle as title from mis_system_data_access_mas where id in (select min(id) as id from mis_system_data_access_mas where startstatus=1 and status=1 group by actionname )";
			$modellist = $masModel->query($sql);
			
			$this->assign("modellist",$modellist);
			$this->assign('objid',$_REQUEST['id']);
			$this->assign('objtype',$_REQUEST['type']);
		}
	}
	public function _before_add(){
		$model = M("mis_system_data_access_mas");
		$list = $model->select();
		$this->assign("datalist",$list);
		$nodemodel = M("node");
		$modellist = $nodemodel->field("id,name,title")->where("status=1 and level=3")->select();
		$this->assign("modellist",$modellist);
	}
	/**
	 * @Title: modelChangeGetRecord
	 * @Description: todo(根据模型找可分配权限字段)   
	 * @author 谢友志 
	 * @date 2015-6-10 下午5:13:54 
	 * @throws
	 */
	public function modelChangeGetRecord(){
		//查询当前模型
		$modelname = $_POST['model'];
		$model = M("mis_system_data_access_mas");
		$list = $model->where("actionname='{$modelname}'")->select();
		//查询第一个字段
		
		
		echo json_encode($list);
	}
	/**
	 * @Title: getFieldOrGroup
	 * @Description: todo(根据分配权限字段id返回对应数据表或配置文件的数据)   
	 * @author 谢友志 
	 * @date 2015-6-10 下午9:06:05 
	 * @throws
	 */
	public function getFieldOrGroup(){
		$model 		= D("MisSystemDataAccessView");
		$map=array();
		$map['actionname']=$_POST['model'];
		$map['field']=$_POST['fieldname'];
		$maslist 	= $model->where($map)->find();
		//查询可能有的数据
		$smap['masid']=$maslist['id'];
		$smap['objid']=$_POST['objid'];
		$smap['objtype']=$_POST['objtype'];
		$submodel = M("mis_system_data_access_sub");
		$sublist = $submodel->where($smap)->find();
		
		if($maslist["accesscontenttype"]==2){//分组
			$groupmodel = M("mis_system_data_access_group");
			$list['list'] 		= $groupmodel->where("masid={$maslist['id']}")->getField("id,name");
			$list['listsource'] = $maslist['accesscontentsource'];
		}else{//直接分配
			$accmodel 	= D("MisSystemDataAccess");
			$list 		= $accmodel->fieldConfigToList($maslist['id']);
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
				$param['open']= "true";
				$param['isParent']="true";
				$arr = $this->getTree($newdepartmentlist,$param);
				if($sublist['treenode']){
					$decodearr = json_decode($arr,true);
					$members = $sublist['treenode'];
					
					$memberarr = explode(",",$members);
					foreach($decodearr as $k=>$v){
						$checked = false;
						if(in_array($v['id'],$memberarr)){
							$decodearr[$k]['checked']=true;
						}
					}
					$arr = json_encode($decodearr);
					$list['treenode']=$sublist['treenode'];
				}
				$list['tree']=$arr;
			}
		}
		$list['masid']=$maslist['id'];
		$list['id']=$maslist['subid'];
		
		$subcontentarr = explode(",",$sublist['accesscontent']);
		foreach($subcontentarr as $key=>$val){
			$subcontentarr[$key] = str_replace("'","",$val);			
		}
		if($subcontentarr){
			//复选以前数据
			$list['content'] = $subcontentarr;
		}
		echo json_encode($list);
	}
	
	public function insert(){
		if(!$_POST['objtype']||!$_POST['objid']){
			$this->error("没有选择授权对象");
		}		
		//成员数据
		if($_POST['treetype']){
			$rs = M("mis_system_data_access_mas")->where("id={$_POST['masid']}")->find();
			$member = array_filter(explode(",",$_POST['treenode']));
			$source = D($rs['accesscontentsource'])->getField("id,{$rs['accesscontentsave']}");
			//节点保存
			$_POST['treemember'] = implode(",",$member);
			//转换成真实的成员数据
			if($rs['sourcesave']!="id"){
				foreach($member as $k=>$v){
					if(empty($v)){
						unset($member[$k]);
						continue;
					}
					$member[$k] = "'".$source[$v]."'";
				}
			}else{
				
				foreach($member as $k=>$v){
					if(empty($v)){
					unset($member[$k]);
					continue;
				}
					$member[$k] = "'".$v."'";
				}
			}
			$_POST['accesscontent'] = implode(",",$member);
		}else{
			foreach($_POST['accesscontent'] as $key=>$val){
				$_POST['accesscontent'][$key] = "'".$val."'";
			}
			$_POST['accesscontent'] = implode(",",$_POST['accesscontent']);
		}
		/**
		 * 如果该授权对象已经对授权字段做过授权，那么新增变为修改
		 */
		$submap['objid'] = $_POST['objid'];
		$submap['objtype'] = $_POST['objtype'];
		$submap['masid'] = $_POST['masid'];
		$submodel = M("mis_system_data_access_sub");
		$rs = $submodel->where($submap)->find();
		if($rs){
			if(empty($_POST['accesscontent'])){
				//$this->delete();
				$ret = $submodel->where($submap)->delete();
				if(false === $ret){
					$this->error("操作失败555！");
				}else{
// 					if(!$_POST['accesscontent']){
// 						$this->error("请先选择授权内容");
// 					}
					//删除浏览级别权限
					$obj_dir = new Dir;
					$directory =  DConfig_PATH."/BrowsecList";
					if(isset($directory)){
						$obj_dir->del($directory);
					}
					$this->success("操作成功！");
				}
				exit;
			}else{
				$_POST['id']=$rs['id'];
				if (false ===  $data = $submodel->create ()) {
					if(!$isrelation){
						$this->error ( $model->getError () );
					}else{
						throw new NullPointExcetion($model->getError () . ' ACTION: ' .$name);
						return false;
					}
				}
				$result=$submodel->data($data)->save();
				//echo $submodel->getlastsql();
				if(false === $result){
					$this->error("操作失败444！");
				}else{
					//删除浏览级别权限
					$obj_dir = new Dir;
					$directory =  DConfig_PATH."/BrowsecList";
					if(isset($directory)){
						$obj_dir->del($directory);
					}
					$this->success("操作成功！");
				}
				exit;
			}			
		}else{
// 			if(!$_POST['accesscontent']){
// 				$this->error("请先选择授权内容");
// 			}
			if (false ===  $data = $submodel->create ()) {
				//print_r($data);
				if(!$isrelation){
					$this->error ( $model->getError () );
				}else{
					throw new NullPointExcetion($model->getError () . ' ACTION: ' .$name);
					return false;
				}
			}
			$result=$submodel->add();
			if(false === $result){
				$this->error("操作失败2！");
			}else{
				//删除浏览级别权限
				$obj_dir = new Dir;
				$directory =  DConfig_PATH."/BrowsecList";
				if(isset($directory)){
					$obj_dir->del($directory);
				}
				$this->success("操作成功！");
			}
			exit;
		}
	}
	
	public function _after_insert($id){
		
	}
	public function _after_update(){
		//删除浏览级别权限
		$obj_dir = new Dir;
		$directory =  DConfig_PATH."/BrowsecList";
		if(isset($directory)){
			$obj_dir->del($directory);
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
}