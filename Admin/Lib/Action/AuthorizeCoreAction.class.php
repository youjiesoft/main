<?php 
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(授权部分核心控制器) 
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-5-19 下午4:00:36 
 * @version V1.0
 */
class AuthorizeCoreAction extends CommonAction{
	
	public function userAuthorizeA(){
		$this->getUserAccess("A","userAuthorizeA");
	}
	public function userAuthorizeB(){
		$this->getUserAccess("B","userAuthorizeB");
	}
	public function roleGroupAuthorizeC(){
		$this->getRoleGroupAccess("C","roleGroupAuthorizeC");
	}
	public function roleGroupAuthorizeD(){
		$this->getRoleGroupAccess("D","roleGroupAuthorizeD");
	}
	
	/**
	 * @Title: getUserAccess 
	 * @Description: todo(获取用户权限关系数据，并输出到  AuthorizeCore：$mA模板  ,点击树节点输出到  AuthorizeCore:userAuthorizeRight 固定模板) 
	 * @param string $rel 点击树节点刷新的div的id
	 * @param string $mA 即是点击树节点的方法名，也是第一次进入后，输出到的模板名称。  
	 * @author liminggang 
	 * @date 2014-5-23 上午9:13:42 
	 * @throws
	 */
	protected function getUserAccess($rel,$mA){
		$this->assign('mA',$mA);
		$this->assign('divrel',$rel);
		//实例化模块分组模型
		$groupModel=M("Group");
		//获取当前授权的用户
		$userid = $_REQUEST['userid'];
		$this->assign("userid",$userid);
		//获取当前的组
		$group_id = $_REQUEST['group_id'];
		if($group_id){
			//获得点击后的数据
			$nodelist=$this->getUserOrRoleGroupAuthorizeList($group_id,$userid);
			$this->assign("nodelist",$nodelist);
			$this->assign("group_id",$group_id);
			$this->display("AuthorizeCore:userAuthorizeRight");
		}else{
			$groupdata=$groupModel->where("status=1")->order("sorts asc")->find();
			//获得点击后的数据
			$nodelist=$this->getUserOrRoleGroupAuthorizeList($groupdata['id'],$userid);
			$this->assign("group_id",$groupdata['id']);
			$this->assign("nodelist",$nodelist);
			//封装url和刷新的div的ID
			$param['url'] = "__URL__/".$mA."/jump/1/group_id/#id#/userid/".$userid;
			$param['rel'] = $rel;
				
			//获取所有分组信息
			$grouplist = $groupModel->where("status=1")->order("sorts asc")->select();
			//封装顶级树节点
			$returnarr[]=array('id'=>0,'pId'=>-1,'title'=>'系统分组','name'=>'系统分组','open'=>true);
			//获取分组的树结构
			$returnarr = $this->getTree($grouplist,$param,$returnarr);
			//以json格式输出到模板
			$this->assign("roleaccesstree",$returnarr);
			$this->display("AuthorizeCore:".$mA);
		}
	}
	/**
	 * @Title: getRoleGroupAccess 
	 * @Description: todo(获取用户权限关系数据，并输出到  AuthorizeCore：$mA模板  ,点击树节点输出到  AuthorizeCore:userAuthorizeRight 固定模板)
	 * @param string $rel 点击树节点刷新的div的id
	 * @param string $mA (方法名和模板名称)即是点击树节点的方法名，也是第一次进入后，输出到的模板名称。    
	 * @author liminggang 
	 * @date 2014-5-23 上午9:14:25 
	 * @throws
	 */
	protected function getRoleGroupAccess($rel,$mA){
		$this->assign('mA',$mA);
		$this->assign('divrel',$rel);
		//实例化模块分组模型
		$groupModel=M("Group");
		//获取当前授权的用户
		$rolegroupid = $_REQUEST['rolegroupid'];
		$this->assign("rolegroupid",$rolegroupid);
		//获取当前的组
		$group_id = $_REQUEST['group_id'];
		if($group_id){
			//获得点击后的数据
			$nodelist=$this->getUserOrRoleGroupAuthorizeList($group_id,0,$rolegroupid);
			$this->assign("nodelist",$nodelist);
			$this->assign("group_id",$group_id);
			$this->display("AuthorizeCore:roleGroupAuthorizeRight");
		}else{
			$groupdata=$groupModel->where("status=1")->order("sorts asc")->find();
			//获得点击后的数据
			$nodelist=$this->getUserOrRoleGroupAuthorizeList($groupdata['id'],0,$rolegroupid);
			$this->assign("group_id",$groupdata['id']);
			$this->assign("nodelist",$nodelist);
			//封装url和刷新的div的ID
			$param['url'] = "__URL__/".$mA."/jump/1/group_id/#id#/rolegroupid/".$rolegroupid;
			$param['rel'] = $rel;
				
			//获取所有分组信息
			$grouplist = $groupModel->where("status=1")->order("sorts asc")->select();
			//封装顶级树节点
			$returnarr[]=array('id'=>0,'pId'=>-1,'title'=>'系统分组','name'=>'系统分组','open'=>true);
			//获取分组的树结构
			$Common = A("Common");
			$returnarr = $Common->getTree($grouplist,$param,$returnarr);
			//以json格式输出到模板
			$this->assign("roleaccesstree",$returnarr);
			$this->display("AuthorizeCore:".$mA);
		}
	}
	
	/**
	 * @Title: getUserOrRoleGroupAuthorizeList 
	 * @Description: todo(获取用户权限数据，或者 角色权限数据) 
	 * @param int $group_id group表的ID，代表系统中的功能分组
	 * @param int $userid 后台用户ID，如果传入后台用户ID，代表获取后台用户权限数据
	 * @param int $rolegroupid 角色ID，如果传入的角色ID，代表获取角色权限数据
	 * @return $data 返回当前group组下所有面板，模块数据，其中数据以浏览，制单，特殊 3中分类组成。
	 * @author liminggang 
	 * @date 2014-5-23 上午9:18:36 
	 * @throws
	 */
	protected function getUserOrRoleGroupAuthorizeList($group_id,$userid=0,$rolegroupid=0){
		$roleuserModel=M("role_user");
		//node节点表
		$nodeModel = D("Node" );
		//系统所有操作节点的5级分类 
		$roleModel = D( "Role" );
		//操作分类
		$nodecategory=M("nodecategory");
		$map = array();
		$map['status'] = 1;
		$map['level'] = 2;
		$map['group_id'] =$group_id;
		//查询当前组的所有面板
		$nodedata = $nodeModel->where($map)->field("id,name,title")->select();
		foreach($nodedata as $key=>$val){
			$map = array();
			$map['status'] = 1;
			$map['level'] = 3;
			$map['pid'] = $val['id'];
			//过滤config中排除授权的控制器
			$excludeModel=explode(",",D('User')->getConfNotAuth());
			if($excludeModel){
				$map['name'] = array("not in ",$excludeModel);
			}
			//查询当前组的3级控制器
			$levethreenodelist=$nodeModel->where($map)->field("id,name,title")->select();
			
			if(!$levethreenodelist){
				unset($nodedata[$key]);
				continue;
			}
			if($userid){
				//获取用户权限数据
				$levethreenodelist = $this->getUserAuthorize($levethreenodelist, $userid);
			}else{
				//获取角色权限数据
				$levethreenodelist = $this->getRoleGroupAuthorize($levethreenodelist, $rolegroupid);
			}
			$nodedata[$key]['threeNode'] = $levethreenodelist;
		}
		return $nodedata;
	}
	
	/**
	 * @Title: getUserAuthorize 
	 * @Description: todo(返回后台用户授权数据，数据以 浏览，制单，特殊 3中分类组成的roleid) 
	 * @param array $levethreenodelist 控制器数组 在节点node表中level=3的数据
	 * @param int $userid 后台用户ID
	 * @return $data 返回的数据。
	 * @author liminggang 
	 * @date 2014-5-23 上午9:35:09 
	 * @throws
	 */
	protected function getUserAuthorize($levethreenodelist,$userid){
		$roleModel = D( "Role" );
		foreach($levethreenodelist as $ks=>$vs){
			$m['role_user.user_id'] = $userid;
			$m['role.nodepid'] = $vs['id'];
			$m['_string'] = "role_user.role_id = role.id";
			//关联查询，获取当前用户已经存在了那些权限
			$roleArrid = $roleModel->table("role_user role_user,role role")->where($m)->getField("role.id id,role.nodeidcategory nodeidcategory");
			// 个人role
			$where = array();
			$where['nodepid'] = $vs['id'];
			$where['plevels'] = 4;
			if(count($roleArrid) < 1) {
				//当前用户不存在已经授权的数据，则查询当前控制器的所有4级节点的roleid
				$roleArrid = $roleModel->where($where)->getField("id,nodeidcategory");
			}else {
				//构造一键授权的checkbox是否选中,1选中，代表以有授权数据
				$levethreenodelist[$ks]['selected'] = 1;
			}
			
			$liulan = array();
			$zhidan = array();
			$tesu = array();
			foreach ($roleArrid as $k1 => $v1) {
				if($v1 == 1){ //浏览权限
					$liulan[] = $k1;
				} else if($v1 == 2){ //制单权限
					$zhidan[] = $k1;
				} else if($v1 == 3){ //特殊权限
					$tesu[] = $k1;
				}
			}
			//构造一键授权的 浏览，制单，特殊 3中权限的roleid数据 因为一键授权只针对浏览和制单
			$levethreenodelist[$ks]['liulan'] = implode(",",$liulan);
			$levethreenodelist[$ks]['zhidan'] = implode(",",$zhidan);
			$levethreenodelist[$ks]['tesu'] = implode(",",$tesu);
			//取浏览的所有节点
			unset($where['plevels']);
			$where['nodeidcategory'] = 1;
			$roleArrid = $roleModel->where($where)->getField("id,plevels");
			$geren = array();
			$bumen = array();
			$zibumen = array();
			$quanbu = array();
			foreach ($roleArrid as $k1 => $v1) {
				if($v1 == 1){ //浏览全部
					$quanbu[] = $k1;
				} else if($v1 == 2){ //浏览部门及子部门
					$zibumen[] = $k1;
				} else if($v1 == 3){  //浏览部门
					$bumen[] = $k1;
				} else if($v1 == 4){  //浏览个人
					$geren[] = $k1;
				}
			}
			//封装浏览权限的级别， 此处是为了改变浏览级别的时候。替换掉浏览的roleid
			$levethreenodelist[$ks]['geren'] = implode(",",$geren);
			$levethreenodelist[$ks]['bumen'] = implode(",",$bumen);
			$levethreenodelist[$ks]['zibumen'] = implode(",",$zibumen);
			$levethreenodelist[$ks]['quanbu'] = implode(",",$quanbu);
		}
		return $levethreenodelist;
	}
	
	/**
	 * @Title: getRoleGroupAuthorize
	 * @Description: todo(返回后台用户授权数据，数据以 浏览，制单，特殊 3中分类组成的roleid)
	 * @param array $levethreenodelist 控制器数组 在节点node表中level=3的数据
	 * @param int $userid 后台用户ID
	 * @return $data 返回的数据。
	 * @author liminggang
	 * @date 2014-5-23 上午9:35:09
	 * @throws
	 */
	protected function getRoleGroupAuthorize($levethreenodelist,$rolegroupid){
		//系统所有操作节点的5级分类
		$roleModel = D( "Role" );
		foreach($levethreenodelist as $ks=>$vs){
			$m['role_rolegroup.rolegroup_id'] = $rolegroupid;
			$m['role.nodepid'] = $vs['id'];
			$m['_string'] = "role_rolegroup.role_id = role.id";
			//关联查询，获取当前角色已经存在了那些权限
			$roleArrid = $roleModel->table("role_rolegroup role_rolegroup,role role")->where($m)->getField("role.id id,role.nodeidcategory nodeidcategory");
			$where = array();
			$where['nodepid'] = $vs['id'];
			$where['plevels'] = 4;
			if(count($roleArrid) < 1){
				//当前角色不存在已经授权的数据，则查询当前控制器的所有4级节点的roleid
				$roleArrid = $roleModel->where($where)->getField("id,nodeidcategory");
			}else {
				//构造一键授权的checkbox是否选中,1选中，代表以有授权数据
				$levethreenodelist[$ks]['selected'] = 1;
			}
			$liulan = array();
			$zhidan = array();
			$tesu = array();
			foreach ($roleArrid as $k1 => $v1) {
				if($v1 == 1){ //浏览权限
					$liulan[] = $k1;
				} else if($v1 == 2){ //制单权限
					$zhidan[] = $k1;
				} else if($v1 == 3){ //特殊权限
					$tesu[] = $k1;
				}
			}
			//构造一键授权的 浏览，制单，特殊 3中权限的roleid数据 因为一键授权只针对浏览和制单
			$levethreenodelist[$ks]['liulan'] = implode(",",$liulan);
			$levethreenodelist[$ks]['zhidan'] = implode(",",$zhidan);
			$levethreenodelist[$ks]['tesu'] = implode(",",$tesu);
			// 取浏览的所有节点
			unset($where['plevels']);
			$where['nodeidcategory'] = 1;
			$roleArrid = $roleModel->where($where)->getField("id,plevels");
			$geren = array();
			$bumen = array();
			$zibumen = array();
			$quanbu = array();
			foreach ($roleArrid as $k1 => $v1) {
				if($v1 == 1){ //浏览全部
					$quanbu[] = $k1;
				} else if($v1 == 2){ //浏览部门及子部门
					$zibumen[] = $k1;
				} else if($v1 == 3){  //浏览部门
					$bumen[] = $k1;
				} else if($v1 == 4){  //浏览个人
					$geren[] = $k1;
				}
			}
			//封装浏览权限的级别， 此处是为了改变浏览级别的时候。替换掉浏览的roleid
			$levethreenodelist[$ks]['geren'] = implode(",",$geren);
			$levethreenodelist[$ks]['bumen'] = implode(",",$bumen);
			$levethreenodelist[$ks]['zibumen'] = implode(",",$zibumen);
			$levethreenodelist[$ks]['quanbu'] = implode(",",$quanbu);
		}
		return $levethreenodelist;
	}
	
	/**
	 * @Title: getUserOrRoleGroupAccess 
	 * @Description: todo(明细授权，获取授权数据) 
	 * @param int $nodeid 4级控制器node节点ID
	 * @param int $userid 后台用户ID 如果传入后台用户ID，代表获取后台用户权限数据
	 * @param int $rolegroupid 角色ID 如果传入的角色ID，代表获取角色权限数据
	 * @return $data 返回授权数据  
	 * @author liminggang 
	 * @date 2014-5-23 上午10:00:29 
	 * @throws
	 */
	protected function getUserOrRoleGroupAccess($nodeid,$userid=0,$rolegroupid=0){
		$roleuserModel=M("role_user");
		$roleRolegroupDao = M("role_rolegroup");
		$roleModel = D( "Role" );
		$map['status'] = 1;
		$map['nodepid'] =$nodeid;
		$module_rolelist = $roleModel->where($map)->order("id asc")->select();//获取对应模块下的所以角色组
		$nodecategory=M("nodecategory");
		//获取该模块下面已经授权的组
		$alreadyaccess=array();
		if($userid){
			$maproleuser = array();
			$maproleuser["user_id"] = $userid;
			$maproleuser["role_id"] = array("not in",C("USER_BASIC_ROLE_GROUP"));
			$alreadyaccess = $roleuserModel->where($maproleuser)->getField("role_id",true);
		}else{
			$maproleuser = array();
			$maproleuser["rolegroup_id"] = $rolegroupid;
			$maproleuser["role_id"] = array("not in",C("USER_BASIC_ROLE_GROUP"));
			$alreadyaccess = $roleRolegroupDao->where($maproleuser)->getField("role_id",true);
		}
		$data=array();
		foreach($module_rolelist as $k=>$v ){
			if(in_array($v['id'],$alreadyaccess)){
				$v["already_role"]=1;
			}else{
				$v["already_role"]=0;
			}
			if( isset($data[$v["nodeidcategory"]]) ){
				if( $v["already_role"] ) $data[$v["nodeidcategory"]]["already_role_count"]+=1;
				$data[$v["nodeidcategory"]]["count"]+=1;
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
				$data[$v["nodeidcategory"]]["child"][$v["nodeid"]][]=$v;
			}
		}
		return $data;
	}
	/**
	 * @Title: writeRoleUser 
	 * @Description: todo(一键授权写入用户权限关系数据。)   
	 * @author liminggang 
	 * @date 2014-5-23 上午10:03:51 
	 * @throws
	 */
	public function writeRoleUser(){
		//用户角色关系表
		$roleuser_model =M("role_user");
		//当前授权用户
		$userid = $_POST['userid'];
		//当前授权的所有模块集合，做判断用
		$groupModel = $_POST['groupModel'];
		//授权的浏览权限
		$oldliulan = $_POST['oldliulan'];
		//授权的浏览权限
		$liulan = $_POST['liulan'];
		//授权的制单权限
		$zhidan = $_POST['zhidan'];
		//授权的特殊权限 (特殊权限只有在明细授权的时候才能起作用,一键授权不做操作。)
		$tesu = $_POST['tesu'];
		//删除的权限
		$delAccess = $_POST['delAccess'];
		//新增的权限
		$coreAcess = $_POST['coreAcess'];
		$addrole =""; //存储需要新增的权限
		$delrole =""; //存储需要删除的权限
	
		foreach($groupModel as $key=>$val){
			$modelid=$_POST['coreAcess'][$val];
			if($coreAcess[$val]){
				$addrole = $addrole.",".$liulan[$val].",".$zhidan[$val];
				//修改浏览的级别
				$arrliulan=explode(",",$liulan[$val]);
				$arroldliulan=explode(",",$oldliulan[$val]);
				//获取删除的浏览
				$delliulan=array_diff($arroldliulan,$arrliulan);
				$delrole = $delrole.",".implode(",",$delliulan);
			}
			if($delAccess[$val] && !$coreAcess[$delAccess[$val]]){
				$delrole = $delrole.",".$liulan[$val].",".$zhidan[$val].",".$tesu[$val];
			}
		}
		//获取所有需要授权的。
		$addroleArr=array_unique(array_filter(explode(",",$addrole)));
		//获取所有需要删除的授权
		$delroleArr=array_unique(array_filter(explode(",",$delrole)));
	
		foreach($addroleArr as $key=>$val){
			$data=array();
			$data['user_id'] = $userid;
			$data['role_id'] = $val;
			$list = $roleuser_model->where($data)->find();
			if(!$list){
				$result = $roleuser_model->add($data);
				if(!$result){
					$this->error("授权失败，请联系管理员");
				}
			}
		}
		foreach($delroleArr as $k=>$v){
			$data=array();
			$data['user_id'] = $userid;
			$data['role_id'] = $v;
			$result = $roleuser_model->where($data)->delete();
			if(!$result){
				$this->error("删除授权失败，请联系管理员");
			}
		}
		//用户组重新授权时 删除缓存权限
		$obj_dir = new Dir;
		$directory =  DConfig_PATH."/AccessList";
		$obj_dir->del($directory);
		
		$this->success("授权成功");
	}
	
	/**
	 * @Title: writeRoleGroup 
	 * @Description: todo(一键授权写入角色权限关系数据)   
	 * @author liminggang 
	 * @date 2014-5-23 上午10:04:16 
	 * @throws
	 */
	public function writeRoleGroup(){
		//用户角色关系表
		$RoleRolegroupDao =M("role_rolegroup");
		//当前授权用户
		$rolegroupid = $_POST['rolegroupid'];
		//当前授权的所有模块集合，做判断用
		$groupModel = $_POST['groupModel'];
		//授权的浏览权限
		$oldliulan = $_POST['oldliulan'];
		//授权的浏览权限
		$liulan = $_POST['liulan'];
		//授权的制单权限
		$zhidan = $_POST['zhidan'];
		//授权的特殊权限 (特殊权限只有在明细授权的时候才能起作用,一键授权不做操作。)
		$tesu = $_POST['tesu'];
		//删除的权限
		$delAccess = $_POST['delAccess'];
		//新增的权限
		$coreAcess = $_POST['coreAcess'];
		$addrole =""; //存储需要新增的权限
		$delrole =""; //存储需要删除的权限
	
		foreach($groupModel as $key=>$val){
			$modelid=$_POST['coreAcess'][$val];
			if($coreAcess[$val]){
				$addrole = $addrole.",".$liulan[$val].",".$zhidan[$val];
				//修改浏览的级别
				$arrliulan=explode(",",$liulan[$val]);
				$arroldliulan=explode(",",$oldliulan[$val]);
				//获取删除的浏览
				$delliulan=array_diff($arroldliulan,$arrliulan);
				$delrole = $delrole.",".implode(",",$delliulan);
			}
			if($delAccess[$val] && !$coreAcess[$delAccess[$val]]){
				$delrole = $delrole.",".$liulan[$val].",".$zhidan[$val].",".$tesu[$val];
			}
		}
		//获取所有需要授权的。
		$addroleArr=array_unique(array_filter(explode(",",$addrole)));
		//获取所有需要删除的授权
		$delroleArr=array_unique(array_filter(explode(",",$delrole)));
	
		foreach($addroleArr as $key=>$val){
			$data=array();
			$data['rolegroup_id'] = $rolegroupid;
			$data['role_id'] = $val;
			$list = $RoleRolegroupDao->where($data)->find();
			if(!$list){
				$result = $RoleRolegroupDao->add($data);
				if(!$result){
					$this->error("授权失败，请联系管理员");
				}
			}
		}
		foreach($delroleArr as $k=>$v){
			$data=array();
			$data['rolegroup_id'] = $rolegroupid;
			$data['role_id'] = $v;
			$result = $RoleRolegroupDao->where($data)->delete();
			if(!$result){
				$this->error("删除授权失败，请联系管理员");
			}
		}
		//用户组重新授权时 删除缓存权限
		$obj_dir = new Dir;
		$directory =  DConfig_PATH."/AccessList";
		$obj_dir->del($directory);
		
		$this->success("授权成功");
	}
	
	
	
	/**
	 * @Title: lookupDetailUserAccess 
	 * @Description: todo(获取后台用户明细授权页面)   
	 * @author liminggang 
	 * @date 2014-5-23 上午10:05:12 
	 * @throws
	 */
	public function lookupDetailUserAccess(){
		$divrel = $_REQUEST['divrel']; //刷新的div的ID
		$this->assign('divrel',$divrel);
		$group_id = $_REQUEST['group_id']; //获取系统分类组的group 的ID
		$this->assign('group_id',$group_id);
		$nodeid = $_REQUEST['nodeid'];  //获取当前3级控制器 node 节点ID
		$this->assign('nodeid',$nodeid);
		$userid = $_REQUEST['userid'];  //获取后台用户ID
		$this->assign('userid',$userid);
		//调用获取授权数据信息
		$data=$this->getUserOrRoleGroupAccess($nodeid, $userid);
		$this->assign("list",$data);
		$this->display("AuthorizeCore:lookupDetailUserAccess");
	}
	/**
	 * @Title: lookupDetailRoleGroupAccess 
	 * @Description: todo(获取角色明细授权页面)   
	 * @author liminggang 
	 * @date 2014-5-23 上午10:05:15 
	 * @throws
	 */
	public function lookupDetailRoleGroupAccess(){
		$divrel = $_REQUEST['divrel']; //刷新的div的ID
		$this->assign('divrel',$divrel);
		$group_id = $_REQUEST['group_id']; //获取系统分类组的group 的ID
		$this->assign('group_id',$group_id);
		$nodeid = $_REQUEST['nodeid'];  //获取当前3级控制器 node 节点ID
		$this->assign('nodeid',$nodeid);
		$rolegroupid = $_REQUEST['rolegroupid']; //获取角色的ID
		$this->assign('rolegroupid',$rolegroupid);
		//调用获取授权数据信息
		$data=$this->getUserOrRoleGroupAccess($nodeid,0,$rolegroupid);
		$this->assign("list",$data);
		$this->display("AuthorizeCore:lookupDetailRoleGroupAccess");
	}
	
	/**
	 * @Title: authorizeRole
	 * @Description: todo(写入明细授权保存后台用户和role的关系数据)
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
					$this->error("删除权限组失败");
				}
				$data['role_id'] =$v;
				$return = $roleuser_model->add($data);
				if(!$return){
					$this->error("添加权限组失败");
				}
			}
		}
		//用户组重新授权时 删除缓存权限
		$obj_dir = new Dir;
		$directory =  DConfig_PATH."/AccessList";
		$obj_dir->del($directory);
		
		$this->success ( L('_SUCCESS_') );
	}
	/**
	 * @Title: authorizeRoleGroup
	 * @Description: todo(写入明细授权角色和role的关系数据)
	 * @author laicaixia
	 * @date 2013-5-31 下午6:30:07
	 * @throws
	 */
	public function authorizeRoleGroup(){
		$rolegroup = $_POST["rolegroupid"];
		$role = $_POST["role"];
		$RoleRoleGroupModel =M("role_rolegroup");
		$RoleModel = M( "role" );
			
		foreach($role as $k=>$v){
			$r=explode(";",$v);
			if( count($r)==5 ){
				$map=array();
				$map['rolegroup_id'] = $rolegroup;
				$map['role_id'] =array("in",$r);
				$return = $RoleRoleGroupModel->where($map)->delete();
				if(!$return){
					echo $RoleRoleGroupModel->getlastsql();
					$this->error("删除权限组失败");
				}
			}else{
				$map=array();
				$map["nodeid"]=$k;
				$rolearr = $RoleModel->where($map)->getField("id",true);
	
				$data=array();
				$data['rolegroup_id'] = $rolegroup;
				$data['role_id'] =array("in",$rolearr);
				$return = $RoleRoleGroupModel->where($data)->delete();
				if(!$return){
					echo $RoleRoleGroupModel->getlastsql();
					$this->error("删除权限组失败");
				}
				$data['role_id'] =$v;
				$return = $RoleRoleGroupModel->add($data);
				if(!$return){
					$this->error("添加权限组失败");
				}
			}
		}
		//用户组重新授权时 删除缓存权限
		$obj_dir = new Dir;
		$directory =  DConfig_PATH."/AccessList";
		$obj_dir->del($directory);
		
		$this->success ( L('_SUCCESS_') );
	}
	public function lookupAccessResult(){
		$UserBrowsConditionsDao=M("user_brows_conditions");
		if($_POST){
			//添加入分割浏览权限表
			$UserBrowsConditionsData=array();
			$UserBrowsConditionsData['type']=$_POST['type'];
			$UserBrowsConditionsData['typeval']=$_POST['typeval'];
			$UserBrowsConditionsData['showrules']=str_replace("&#39;", "'", html_entity_decode($_POST['showrules']));
			$UserBrowsConditionsData['rules']=$_POST['rules'];
			$UserBrowsConditionsData['rulesinfo']=$_POST['rulesinfo'];
			$UserBrowsConditionsData['nodename']=$_POST['md'];
			if($_POST['id']){
				$UserBrowsConditionsData['id']=$_POST['id'];
				$UserBrowsConditionsData['updateid']=$_SESSION[C('USER_AUTH_KEY')];
				$UserBrowsConditionsData['updatetime']=time();
				$UserBrowsConditionsResult=$UserBrowsConditionsDao->save($UserBrowsConditionsData);
			}else{
				$UserBrowsConditionsData['createid']=$_SESSION[C('USER_AUTH_KEY')];
				$UserBrowsConditionsData['createtime']=time();
				$UserBrowsConditionsResult=$UserBrowsConditionsDao->add($UserBrowsConditionsData);
			}
			$UserBrowsConditionsDao->commit();
			if(!$UserBrowsConditionsResult){
				$this->error("添加浏览及权限操作失败！");
			}else{
				//删除浏览级别权限
				$obj_dir = new Dir;
				$directory =  DConfig_PATH."/BrowsecList";
				if(isset($directory)){
					$obj_dir->del($directory);
				}
				$this->success ( L('_SUCCESS_') );
			}
		}else{
			//查询当前节点是否设置浏览级权限
			$UserBrowsConditionsMap=array();
			$UserBrowsConditionsMap['type']=$_REQUEST['type'];
			$UserBrowsConditionsMap['typeval']=$_REQUEST['typeval'];
			$UserBrowsConditionsMap['nodename']=$_REQUEST['modelname'];
			$UserBrowsConditionsMap['status']=1;
			$UserBrowsConditionsVo=$UserBrowsConditionsDao->where($UserBrowsConditionsMap)->find();
			$this->assign("UserBrowsConditionsVo",$UserBrowsConditionsVo);
			$this->display();
		}
	}
}
?>