<?php
/** 
 * @Title: AuthorizeUserAction 
 * @Package package_name
 * @Description: todo(用户授权) 
 * @author 杨东 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-3-24 上午11:32:58 
 * @version V1.0 
*/ 
class AuthorizeUserAction extends CommonAction {
	
	/** @Title: _filter
	 * @Description: (列表进入的过滤器)
	 * @author
	 * @date 2013-5-31 下午3:59:44
	 * @throws
	 */
	public function _filter(&$map){
		if ($_SESSION["a"] != 1){
			$map['status']=array("gt",-1);
			$map['_string'] = "id != ".$_SESSION[C('USER_AUTH_KEY')];
		}
		if($_REQUEST["deptid"]){
			$map['dept_id']=$_REQUEST["deptid"];
			$this->assign("deptid",$_REQUEST["deptid"]);
		}
	}
	/**
	 * (non-PHPdoc)
	 * @Description: TODO(重写父类的index方法，实现此处的查询功能)
	 * @see CommonAction::index()
	 */
	public function index(){
		$name = $this->getActionName();
		$map = $this->_search ($name);
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		if (! empty ( $name )) {
			$qx_name=$name;
			if(substr($name, -4)=="View"){
				$qx_name = substr($name,0, -4);
			}
			//验证浏览及权限
			if( !isset($_SESSION['a']) ){
				if( $_SESSION[strtolower($qx_name.'_'.ACTION_NAME)]!=1 ){
					if( $_SESSION[strtolower($qx_name.'_'.ACTION_NAME)]==2 ){////判断公司权限
						$map["id"]=array("in",$_SESSION['user_dep_all_child']);
					}else if($_SESSION[strtolower($qx_name.'_'.ACTION_NAME)]==3){//判断部门权限
						$map["id"]=array("in",$_SESSION['user_dep_all_self']);
					}else if($_SESSION[strtolower($qx_name.'_'.ACTION_NAME)]==4){//判断个人权限
						$map["id"]=$_SESSION[C('USER_AUTH_KEY')];
					}
				}
			}
			$this->_list ( $name, $map );
		}
		$this->assign('module',$name);
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($name);
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		//扩展工具栏操作
		$toolbarextension = $scdmodel->getDetail($name,true,'toolbar');
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		if($_REQUEST['jump']){
			$this->display('indexview');
			exit;
		}
		$model = D('MisSystemDepartment');
		$deptmap['status']=1;
		$list = $model->where($deptmap)->select();
		//构造部门等级树形
		$treemiso=array();
		$param['url']="__URL__/index/jump/1/deptid/#id#";
		$param['rel']="AuthorizeUserjbsxBox";
		$typeTree = $this->getTree($list,$param,$treemiso);
		$this->assign('typeTree',$typeTree);
		$this->display();
	}
	/**
	 * @Title: lookupAuthorizeUser
	 * @Description: todo(通过用户ID查找用户权限)   
	 * @author 杨东 
	 * @date 2014-3-24 下午3:59:18 
	 * @throws 
	*/  
	public function lookupAuthorizeUser(){
		$model = M();
		$map["_string"] = "node.id=role.nodepid and role.id=role_user.role_id and nodecategory.id=role.nodeidcategory";
		$map["role_user.user_id"] = $_REQUEST['id'];
		$map["role_user.role_id"] = array("not in",C("USER_BASIC_ROLE_GROUP"));
		// 权限列表
		$permissionslist = $model->table("role_user AS role_user,role AS role,node AS node,nodecategory AS nodecategory")->
		where($map)->field("node.title AS model,nodecategory.name AS nodeidcategory,role.plevels AS plevels,role.nodetitle AS method")->
		order("role.nodepid asc,role.nodeidcategory asc")->select();
		$this->assign("permissionslist",$permissionslist);
		$this->display();
	}
	/**
	 * @Title: roleAccess
	 * @Description: todo(授权)   
	 * @author 杨东 
	 * @date 2014-3-24 下午5:46:09 
	 * @throws 
	*/  
	public function roleAccess(){
		$groupModel=M("Group");
		$nodeModel = D("Node" );
		$accessModel=M("Access");
		$roleuserModel=M("role_user");
		if( $_GET['id'] ){
			$this->roleAccessRight();
		}
		$maproleuser=array();
		$maproleuser["user_id"] = $_GET['userid'];
		$maproleuser["role_id"] = array("not in",C("USER_BASIC_ROLE_GROUP"));
		$role_list = $roleuserModel->where($maproleuser)->getField("role_id",true);
		$alreadyaccess = array();
		if($role_list){
			$mapacc["role_id"]=array("in",$role_list);
			$alreadyaccess = $accessModel->where($mapacc)->getField("node_id",true);
		}
		$list = $groupModel->where("status=1")->order("sorts asc")->select();
		$map['status'] = 1;
		$map['level'] =array("neq",4);
		$map['pid'] =array("gt",0);
		$nodedata = $nodeModel->where($map)->order("sort asc")->select();
		$start_groupid=100000;
		$returnarr[]=array('id'=>0,'pId'=>-1,'title'=>'系统功能','name'=>'系统功能','open'=>true,'nocheck'=>'true');
		$valid = null;
		foreach($list as $k =>$v){
			$parendchecked=false;
			$newv['id']=$v['id']+100000;
			$newv['pId']=0;
			$newv['title']=$v['name']; //光标提示信息
			$newv['name']=missubstr($v['name'],20,true); //结点名字，太多会被截取
			$newv['open']=false;
			$newv['checked']=false;
			$newv['type']=0;
			$returnarr1= $this->getUserGroupModuleTree($v['id'],$nodedata,0,$alreadyaccess,$parendchecked);
			if($parendchecked) {
				$newv['checked'] = true;
				if($valid === null) $valid = $parendchecked;
				$newv['open'] = true;
			}
			if($returnarr1){
				array_push($returnarr,$newv);
				$returnarr=array_merge($returnarr,$returnarr1);
			}
		}
		if($valid === null){
			foreach ($returnarr as $k => $v) {
				if ($v['type'] == 3) {
					$valid = $v['id'];
					break;
				}
			}
		}
		$this->assign("valid",$valid);
		$_GET['id'] = $valid;
		$this->roleAccessRight();
		$this->assign("roleaccesstree",json_encode($returnarr));
		$this->display();
	}
	
	/**
	 * @Title: getUserGroupModuleTree
	 * @Description: todo(授权树) 
	 * @param unknown_type $group_id
	 * @param unknown_type $nodedata
	 * @param unknown_type $pid
	 * @param unknown_type $alreadyaccess
	 * @param unknown_type $parendchecked
	 * @param unknown_type $func
	 * @return multitype:  
	 * @author 杨东 
	 * @date 2014-3-24 下午5:46:24 
	 * @throws 
	*/  
	private function getUserGroupModuleTree($group_id,$nodedata,$pid,$alreadyaccess,&$parendchecked=false, $func = 'roleAccess'){
		$returnarr=array();
		foreach($nodedata as $k => $v) {
			if($v['group_id'] !=$group_id) continue;
			if( $pid ){
				if($pid!=$v['pid'])continue;
			}
			$newv=array();
			$newv['id']=$v['id'];
			$newv['title']=$v['title']; //光标提示信息
			$newv['name']=missubstr($v['title'],12); //结点名字，太多会被截取
			$newv['open']=false;
			if($v['type'] == 3 ){
				if($v['pid']==$pid){
					// 判断权限
					if($_SESSION['a'] || $_SESSION[strtolower($v['name'].'_index')]) {
						$newv['pId']=$pid;
						unset($nodedata[$k]);
						$newv['url']="__URL__/" . $func . "/jump/1/id/".$v['id']."/md/".$v['name']."/userid/".$_GET['userid'];
						$newv['type']='post';
						$newv['target']='ajax';
						if ($func == 'roleAccess') {
							$newv['rel']="userroleaccessright";
						} else {
							$newv['rel'] = $func . "userroleaccessright";
						}
						$newv['type']=3;
						if(in_array($v['id'],$alreadyaccess)){
							$newv['checked']=true;
							if(!$parendchecked) $parendchecked = $v['id'];
							$newv['open']=true;
						}
						array_push($returnarr,$newv);
					} else {
						$newv = null;
					}
				}
			} else if($v['type'] == 1 ){
				$newv['pId']=100000+$group_id;
				unset($nodedata[$k]);
				$newv['type']=1;
				$s1_2= $this->getUserGroupModuleTree($group_id,$nodedata,$v['id'],$alreadyaccess,$parendchecked, $func);
				if(in_array($v['id'],$alreadyaccess)){
					$newv['checked']=true;
					if(!$parendchecked) $parendchecked = $v['id'];
					$newv['open']=true;
				}
				if($s1_2){
					array_push($returnarr,$newv);
					$returnarr=array_merge($returnarr,$s1_2);
				}
			} else if($v['type'] == 2 ){
				if($v['pid']==$pid){
					$newv['pId']=$pid;
					unset($nodedata[$k]);
					$newv['type']=2;
					$s2_2=$this->getUserGroupModuleTree($group_id,$nodedata,$v['id'],$alreadyaccess,$parendchecked, $func);
					if(in_array($v['id'],$alreadyaccess)){
						$newv['checked']=true;
						if(!$parendchecked) $parendchecked = $v['id'];
						$newv['open']=true;
					}
					if($s2_2){
						array_push($returnarr,$newv);
						$returnarr=array_merge($returnarr,$s2_2);
					}
				}
			}
		}
		return $returnarr;
	}
	
	/**
	 * @Title: roleAccessRight
	 * @Description: todo(右侧列表)   
	 * @author 杨东 
	 * @date 2014-3-24 下午5:46:36 
	 * @throws 
	*/  
	private function roleAccessRight(){
		$groupModel=M("Group");
		$nodeModel = D("Node" );
		$accessModel=M("Access");
		$roleuserModel=M("role_user");
		$roleModel = D( "Role" );
		$map['status'] = 1;
		$map['nodepid'] =$_GET['id'];
		$module_rolelist = $roleModel->where($map)->select();//获取对应模块下的所以角色组
		$nodecategory=M("nodecategory");
		$vo = $nodeModel->find($_GET['id']);
		//获取该模块下面已经授权的组
		$alreadyaccess=array();
		$maproleuser = array();
		$maproleuser["user_id"] = $_GET['userid'];
		$maproleuser["role_id"] = array("not in",C("USER_BASIC_ROLE_GROUP"));
		$alreadyaccess = $roleuserModel->where($maproleuser)->getField("role_id",true);
		$data=array();
		foreach($module_rolelist as $k=>$v ){
			$v['nodename'] = $nodeModel->where("id=".$v["nodeid"])->getField("name");
			if(!$_SESSION[strtolower($vo['name']."_".$v['nodename'])]) {
				continue;
			} else {
				$v['parentplevels'] = $_SESSION[strtolower($vo['name']."_".$v['nodename'])];
			}
			if(in_array($v['id'],$alreadyaccess)){
				$v["already_role"]=1;
			}else{
				$v["already_role"]=0;
			}
			if( isset($data[$v["nodeidcategory"]]) ){
				if( $v["already_role"] ) $data[$v["nodeidcategory"]]["already_role_count"]+=1;
				$data[$v["nodeidcategory"]]["count"]+=1;
				//$data[$v["nodeidcategory"]]["child"][$v["nodeid"]]["nodeid"]=$v["nodeid"];
				$data[$v["nodeidcategory"]]["child"][$v["nodeid"]][]=$v;
			}else{
				$title = $nodecategory->where("id=".$v["nodeidcategory"])->getField("name");
				if( !$title ) continue;
				$data[$v["nodeidcategory"]]=array();
				$data[$v["nodeidcategory"]]["title"]=$title;
				$data[$v["nodeidcategory"]]["count"]=1;
				$data[$v["nodeidcategory"]]["already_role_count"]=0;
				if( $v["already_role"] ) $data[$v["nodeidcategory"]]["already_role_count"]=1;
				$data[$v["nodeidcategory"]]["child"][$v["nodeid"]]=array();
				//$data[$v["nodeidcategory"]]["child"][$v["nodeid"]]["nodeid"]=$v["nodeid"];
				$data[$v["nodeidcategory"]]["child"][$v["nodeid"]][]=$v;
			}
		}
		$this->assign("vo",$vo);
		$this->assign("list",$data);
		$this->assign("nodeid",$_GET["id"]);
		//$this->assign("user_role",$alreadyaccess);
		$this->assign("userid",$_GET['userid']);
		if($_GET['jump']){
			$this->display("roleAccessRight");
			exit;
		}
	}
	/**
	 * @Title: authorizeRole
	 * @Description: todo(点击y右边action保存时授权)
	 * @author qchlian
	 * @date 2013-5-31 下午6:30:07
	 * @throws
	 */
	public function authorizeRole(){
		$user = $_POST["userid"];
		$role = $_POST["role"];
		$roleuser_model =M("role_user");
		$role_model = M( "role" );
		foreach($role as $k=>$v){
			$r=explode(";",$v);
			if( count($r)==5 ){
				$map=array();
				$map['user_id'] = $user;
				$map['role_id'] =array("in",$r);
				$return = $roleuser_model->where($map)->delete();
				if(!$return){
					//echo $roleuser_model->getlastsql();
					$this->error("删除权限组失败");
				}
			}else{
				$map=array();
				$map["nodeid"]=$k;
				$rolearr = $role_model->where($map)->getField("id",true);
	
				$data=array();
				$data['user_id'] = $user;
				$data['role_id'] =array("in",$rolearr);
				$return = $roleuser_model->where($data)->delete();
				if(!$return){
					//echo $roleuser_model->getlastsql();
					$this->error("删除权限组失败");
				}
				$data['role_id'] =$v;
				$return = $roleuser_model->add($data);
				if(!$return){
					$this->error("添加权限组失败");
				}
			}
		}
		$this->success ( L('_SUCCESS_') );
	}
}
?>