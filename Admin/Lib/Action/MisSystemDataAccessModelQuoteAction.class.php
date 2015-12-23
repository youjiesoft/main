<?php
/**
 * @Title: MisSystemDataAccessModelQuoteAction 
 * @Package package_name
 * @Description: todo(授权模型被引用) 
 * @author谢友志 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015-6-23 下午2:04:06 
 * @version V1.0
 */
class MisSystemDataAccessModelQuoteAction extends CommonAction{
	public function _filter(&$map){
		if($_REQUEST['jump']){
			$map['actionname'] = $_REQUEST['aname'];
		}
	}
	public function index(){
		$model = D($this->getActionName());
		//组装树
		if(!$_REQUEST['jump']){
			$nodeList = $model->quoteActionTree("MisSystemDataAccessModelQuoteBOX");
			$this->assign("nodeList",json_encode($nodeList));
		}
		//根据模块名称查找引用这个模块的模块及字段
		if($_REQUEST['aname']){	
			$this->assign('aname',$_REQUEST['aname']);
			$formlist = $this->anameinfo($_REQUEST['aname']);
			$this->assign("aname",$_REQUEST['aname']);
			//查询选择了的控制模型	
			$formModel = M("mis_system_data_access_action");
			$fmap ['actionname'] = $_REQUEST['aname'];
			$formactionformids = getFieldBy($_REQUEST['aname'],'actionname','formid','mis_system_data_access_action');
			$formarr = explode(",",$formactionformids);
			$newformlist = array();
			foreach($formarr as $key=>$val){
				$newformlist[$val] = $formlist[$val];
			}
			$this->assign("formlist",$newformlist);
			
			//print_r($formlist);
			//查询本模型数据表查看有无记录
			$model = D($this->getActionName());
			$map["actionname"]=$_REQUEST['aname'];
			$list = $model->where($map)->select();
			foreach($list as $key=>$val){
				$newlist[$val['quoteaction']]['list'][$val['quotefield']] = $val;
				$newlist[$val['quoteaction']]['fields'][] = $val['quotefield'];
			}
			$this->assign("list",$newlist);
		}
		
		//扩展工具栏操作
		$scdmodel = D('SystemConfigDetail');
		$name = $this->getActionName();
		$toolbarextension = $scdmodel->getDetail($name,true,'toolbar','sortnum','shows',true);
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		if($_REQUEST['jump']){
			$this->display("indexview");
		}else{
			$this->display();
		}
	}
	public function add(){
		if(!$_REQUEST['aname']) $this->error("请先选择一个模块");
		$formlist = $this->anameinfo($_REQUEST['aname']);
		//查询选择了的控制模型
		$formModel = M("mis_system_data_access_action");
		$fmap ['actionname'] = $_REQUEST['aname'];
		$formactionformids = getFieldBy($_REQUEST['aname'],'actionname','formid','mis_system_data_access_action');
		$formarr = explode(",",$formactionformids);
		$newformlist = array();
		foreach($formlist as $key=>$val){
			if(in_array($key,$formarr)){
				unset($formlist[$key]);
			}
		}
		$this->assign("formlist",$newformlist);
		//print_r($formlist);
		//查询本模型数据表查看有无记录
		$model = D($this->getActionName());
		$map["actionname"]=$_REQUEST['aname'];
		$list = $model->where($map)->select();
		foreach($list as $key=>$val){
			$newlist[$val['quoteaction']]['list'][$val['quotefield']] = $val;
			$newlist[$val['quoteaction']]['fields'][] = $val['quotefield'];
		}
		$this->assign("aname",$_REQUEST['aname']);
		$this->assign("formlist",$formlist);
		$this->assign("list",$newlist);
		$this->display();
	}
	public function insertformid(){
		$data['actionname'] = $_POST['actionname'];
		$data['formid'] = implode(",",$_POST['formid']);
		$formModel = M("mis_system_data_access_action");
		$rs = $formModel->where("actionname='".$_POST['actionname']."'")->find();
		if($rs){
			$data['formid'] = $rs['formid']?$rs['formid'].','.$data['formid']:$data['formid'];
			$ret = $formModel->where("actionname='".$_POST['actionname']."'")->data($data)->save();
		}else{
			$ret = $formModel->data($data)->add();
		}
		
		if(false === $ret){
			$this->error("数据入库失败");
		}else{
			$this->success("操作成功");
		}
	}
	/**
	 * @Title: insert
	 * @Description: todo(新增、修改合一)   
	 * @author 谢友志 
	 * @date 2015-6-25 下午2:37:02 
	 * @throws
	 */
	public function insert(){
		$data = $_POST;
		$model = D($this->getActionName());
		$idarr = array();//页面传递过来的数据id+新增id集 用于和数据库该model下的id集比较，多余的将删除
		foreach($data['field'] as $k=>$v){
			foreach($v as $key=>$val){
				$temp = array();
				$fieldinfo = array();
				$fieldinfo = explode("|",$val);
				$temp['actionname'] = $data['aname'];
				$temp['quotefield'] = $fieldinfo[0];
				$temp['quotetable'] = $fieldinfo[4];
				$temp['quoteaction'] = $k;
				$temp['quotetype'] = $fieldinfo[1];
				$temp['quoteformid'] = $fieldinfo[3];
				$temp['quotepropertyid'] = $fieldinfo[2];
				$temp['savefield'] = $fieldinfo[5];
				//根据传递过来的$fieldinfo[6]判断该条记录是否已存在于数据库
				if($fieldinfo[6]) $temp['id'] = $fieldinfo[6];
				
				if(empty($temp['id'])){				
					$ret = $model->add($temp);
					$idarr[] = $ret;
				}else{
					$idarr[] = $temp['id'];
					$ret = $model->where("id=".$temp['id'])->save($temp);
				}
				if(false === $ret) {
					$this->error();
				}
			}
		}
		
		//清除被取消的数据
		$rs = $model->where("actionname='".$data['aname']."'")->getField("id,actionname");
		if($rs){
			$ids = array_keys($rs);
			foreach($ids as $k=>$v){
				if(in_array($v,$idarr)){
					unset($ids[$k]);
				}
			}
			if(count($ids)>0){
				$delmap['id'] = array("in",$ids);
				$ret = $model->where($delmap)->delete();
				if(false === $ret){
					$this->error();
				}
			}
		}
		//删除浏览级别权限
		$obj_dir = new Dir;
		$directory =  DConfig_PATH."/BrowsecList";
		if(isset($directory)){
			$obj_dir->del($directory);
		}
		$this->success("操作成功!");
	}
	public function delete(){
		if(!$_REQUEST['id']) $this->error("请选择一条记录");	
		//查询选择了的控制模型
		$formModel = M("mis_system_data_access_action");
		$fmap ['actionname'] = $_REQUEST['aname'];
		if(!$_REQUEST['aname']) $this->error("模块丢失");
		$formactionformids = getFieldBy($_REQUEST['aname'],'actionname','formid','mis_system_data_access_action');
		$formarr = explode(",",$formactionformids);
		$newformlist = array();
		foreach($formarr as $key=>$val){
			if($val==$_REQUEST['id']){
				unset($formarr[$key]);
			}
		}
		$formids['formid'] = implode(",",$formarr);
		$model = M("mis_system_data_access_model_quote");
		$ret1 = $model->where("actionname='".$_REQUEST['aname']."' and quoteformid=".$_REQUEST['id'])->delete();
		//echo $model->getlastsql();
		if(false === $ret1){
			$this->error("删除控制字段表失败");
		}
		$ret2 = $formModel->where('actionname="'.$_REQUEST['aname'].'"')->save($formids);
		if(false === $ret2){
			$this->error("删除控制模块失败");
		}else{
			//删除浏览级别权限
			$obj_dir = new Dir;
			$directory =  DConfig_PATH."/BrowsecList";
			if(isset($directory)){
				$obj_dir->del($directory);
			}
			$this->success("操作成功");
		}
	}
	/**
	 * @Title: anameinfo
	 * @Description: todo(根据模型找到引用该模型的模块、表、字段信息) 
	 * @param unknown_type $aname
	 * @return Ambigous <multitype:, unknown>  
	 * @author 谢友志 
	 * @date 2015-6-25 上午10:07:26 
	 * @throws
	 */
	public function anameinfo($aname){
		//在mis_dynamic_form_propery表中查询该模块对应数据
		$table = D($aname)->getTableName();
		$dyModel = M("mis_dynamic_form_propery");
		$dymap['status'] = 1;
		$where['lookupmodel'] = $aname;
		$where['subimporttableobj'] = $table;
		$where['subimporttablefield2obj'] = $table;
		$where['treedtable'] = $table;
		$where['_logic'] = 'or';
		$dymap['_complex'] = $where;
		$dymap['category'] = array("in" ,array("lookup","select","radio","checkbox"));
		$list = $dyModel->where($dymap)->limit(10)->getField("id,formid,category,fieldname,title,lookuporgval,subimporttablefieldobj,subimporttablefield2obj,treevaluefield");
		$nodelist = M('node')->getField('name,title');
		//print_r($nodelist);
		//$listformid 符合要求的formid数组
		$listformid = array();
		foreach($list as $key=>$val){
			$listformid[]=$val['formid'];
		}
		$listformid = array_unique($listformid);
		//根据查询到的formid 查询表及模块
		$formlist = array();
		$dyMasModel = M("mis_dynamic_database_mas");
		//查询formid对应模块名称和主表，并将字段信息糅合进该模块
		foreach($listformid as $key=>$val){
			$mmap['status'] = 1;
			$mmap['formid'] = $val;
		
			//	$mmap['_string'] = "ischoise=0 or ischoise is null"; //排除复用表
			//	$mmap['isprimary'] = 1; //限定主表
			$rs = $dyMasModel->where($mmap)->find();
			$formlist[$val]['actionname'] = $rs['modelname'];
			$formlist[$val]['actiontitle'] = $nodelist[$rs['modelname']];//getFieldBy($rs['modelname'],'name','title','node');
			$formlist[$val]['tablename'] = $rs['tablename'];
			$formlist[$val]['tabletitle'] = $rs['tabletitle'];
			//将
			foreach ($list as $k=>$v){
				if($val == $v['formid']){
					$v['savefield'] = '';
					if($v['category'] == 'lookup'){
						$v['savefield'] = $v["lookuporgval"];
					}else{
						if($v["subimporttablefieldobj"]){
							$v['savefield'] = $v["subimporttablefieldobj"];
						}elseif($v["subimporttablefield2obj"]){
							$v['savefield'] = $v["subimporttablefield2obj"];
						}elseif($v["treevaluefield"]){
							$v['savefield'] = $v["treevaluefield"];
						}
					}
					unset($v['lookuporgval'],$v['subimporttablefieldobj'],$v['subimporttablefield2obj'],$v['treevaluefield']);
					$formlist[$val]['fieldinfo'][] = $v;
				}
			}
			//print_r($formlist);
		}
		return $formlist;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}