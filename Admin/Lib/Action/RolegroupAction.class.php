<?php
/**
 * @Title: RolegroupAction
 * @Package package_name
 * @Description: todo(高级权限组管理)
 * @author yangxi
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-6-15 下午6:04:23
 * @version V1.0
 */
class RolegroupAction extends AuthorizeCoreAction {
	/**
	 +----------------------------------------------------------
	 * 检索
	 * 赖彩霞
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return void
	 +----------------------------------------------------------
	 * @throws FcsException
	 +----------------------------------------------------------
	 */
	public function _filter(& $map) {
		if ($_SESSION["a"] != 1) $map['status']=array("gt",-1);
	}
	public function index() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name = $this->getActionName();
		if (! empty ( $name )) {
			$qx_name=$name;
			if(substr($name, -4)=="View"){
				$qx_name = substr($name,0, -4);
			}
			//验证浏览及权限
			if( !isset($_SESSION['a']) ){
				$map=D('User')->getAccessfilter($qx_name,$map);	
			}
			$this->_list ( $name, $map );
		}
		$this->assign('type',2);
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($name);
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		//searchby搜索扩展
		$searchby = $scdmodel->getDetail($name,true,'searchby');
		if ($searchby && $detailList) {
			$searchbylist=array();
			foreach( $detailList as $k=>$v ){
				if(isset($searchby[$v['name']])){
					$arr['id']= $searchby[$v['name']]['field'];
					$arr['val']= $v['showname'];
					array_push($searchbylist,$arr);
				}
			}
			$this->assign("searchbylist",$searchbylist);
		}
		//扩展工具栏操作
		$toolbarextension = $scdmodel->getDetail($name,true,'toolbar');
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		if( intval($_POST['dwzloadhtml']) ){
			$this->display("dwzloadindex");exit;
		}
		$this->display ();
		return;
	}
	/**
	 +----------------------------------------------------------
	 * 组操作权限列表
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return void
	 +----------------------------------------------------------
	 * @throws FcsException
	 +----------------------------------------------------------
	 */
	public function user(){
		//-------------检索开始-------------赖彩霞
		$map['status'] = 1; //只有正常创状态下的用户才能被授权
		// 获取查询条件
		$searchby1	= $_POST['searchby1'];
		$searchtype1	= $_POST['searchtype1'];
		$keyword1	= $_POST['keyword1'];
		$this->assign('searchby1', $searchby1);
		$this->assign('searchtype1', $searchtype1);
		$this->assign('keyword1', $keyword1);
		$this->assign("selectGroupId", isset($_REQUEST['id']) ? $_REQUEST['id'] : '');
		$searchuserList = array();
		$searchlist = array();
		if ($_POST['searchlist'] || $_POST['searchlist1']) {
			$_POST['searchlist'] = $_POST['searchlist'] ? $_POST['searchlist'] : $_POST['searchlist1'];
			$searchlist = explode('_',$_POST['searchlist']);
			$this->assign('searchlist', implode('_',$searchlist));
		}
		if ($keyword1) {
			//检索部门，通过duty_id，查询部门表
			if($searchby1 == 'dept_id'){//关联id
				$deptModel = D('MisSystemDepartment');	//部门表
				$deptMap['status'] = 1;
				$deptMap['name'] = ($searchtype1==2) ? array('like',"%".$keyword1."%"):$keyword1;
				$deptList = $deptModel->where($deptMap)->getField('id,name');
				$map['dept_id'] = array('in',array_keys($deptList));
			}else if($searchby1 == 'duty_id'){//关联id
				$dutyModel = D('Duty');	//职级
				$dutyMap['status'] = 1;
				$dutyMap['name'] = ($searchtype1==2) ? array('like',"%".$keyword1."%"):$keyword1;
				$dutyList = $dutyModel->where($dutyMap)->getField('id,name');
				$map['duty_id'] = array('in',array_keys($dutyList));
				//检索多条用户名、名称
			} else if ($searchby1 == 'account' || $searchby1 == 'name') {
				$kwArr = explode(' ', $keyword1);
				$kwList = array();
				foreach ($kwArr as $k => $v) {
					if(trim($v)){
						$kwList[] =  ($searchtype1==2) ? array('like',"%".trim($v)."%"):trim($v);
					}
				}
				if (count($kwList) > 1) {
					$kwList[] = 'or';
				}
				if($kwList){
					$map[$searchby1] = $kwList;
				}
			} else{
				$map[$searchby1] = ($searchtype1==2) ? array('like',"%".$keyword1."%"):$keyword1;
			}
		}
		$searchby1 = array (
				array ("id" => "account","val" => "用户名"),
				array ("id" => "name","val" => "名称"),
				array ("id" => "dept_id","val" => "部门"),
				array ("id" => "duty_id","val" => "职位"),
		);
		$searchtype1 = array (
				array ("id" => "2","val" => "模糊查找"),
				array ("id" => "1","val" => "精确查找")
		);
		$this->assign('searchbylist1', $searchby1);
		$this->assign('searchtypelist1', $searchtype1);

		//读取系统的用户列表
		$map['status']=array("gt",-1);

		//设置一个标记，在_after_list中进行判断。
		$_POST['afterListStep'] = 1;
		$this->_list("User", $map);
		$this->display();
	}

	public function _after_list($list2){
		if($_POST['afterListStep']){ //只是查询用户列表用
			// 获取选中组
			$group    =   D("Rolegroup");
			//获取当前用户组信息
			$groupId =  isset($_REQUEST['id'])?$_REQUEST['id']:'';
			$groupUserList = array();
			$model = D("User");
			if(!empty($groupId)) {
				//获取当前组的用户列表
				$isidlist = array();
				$list	=	$group->getGroupUserList($groupId);
				foreach ($list as $vo){
					$isidlist[]	=	$vo['id'];
				}
				$map = array();
				$map['id'] = array('in', $isidlist);
				//查询出当前已选中用户
				$groupUserList = $model->where($map)->getField('id,account,name,duty_id,dept_id');
			}
			foreach ($list2 as $key => $val) {
				if ($groupUserList[$val['id']]) {
					if ($_REQUEST['search']) {
						$list2[$key]['checked'] = true;
						unset($groupUserList[$val['id']]);
					} else {
						unset($list2[$key]);
					}
				}
			}
			if ($_POST['searchlist'] || $_POST['searchlist1'] || $_POST['pageNum'] > 1) {
				$_POST['searchlist'] = $_POST['searchlist'] ? $_POST['searchlist'] : $_POST['searchlist1'];
				$searchlist = array_unique(explode('_',$_POST['searchlist']));
				$this->assign('searchlist', implode('_',$searchlist));
				$condition['id'] = array('in',$searchlist);
				$condition['status'] = array('GT', -1);
				$searchuserList = $model->where($condition)->field('id,account,name,duty_id,dept_id')->select();
				// 循环第一次取得未选中记录
				$userList = array();
				foreach ($list2 as $vo){
					$vo['checked'] = false;
					$userList[$vo['id']] = $vo;
				}
				// 循环第二次取得选中记录
				foreach ($groupUserList as $val) {
					$val['checked'] = true;
					$userList[$val['id']] = $val;
				}
				// 循环加入原先已选列
				foreach ($searchuserList as $val) {
					$val['checked'] = true;
					$userList[$val['id']] = $val;
				}
			} else {
				// 循环第一次取得选中记录
				foreach ($groupUserList as $val) {
					$val['checked'] = true;
					$userList[$val['id']] = $val;
				}
				// 循环第二次取得未选中记录
				foreach ($list2 as $vo){
					if ($_REQUEST['search']) {
						$vo['checked'] = $vo['checked'] ? $vo['checked'] : false;
					} else {
						$vo['checked'] = false;
					}
					$userList[$vo['id']] = $vo;
				}
			}
			$this->assign('userList',$userList);
		}
	}
	/**
	 +----------------------------------------------------------
	 * 用户增加组操作权限
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return void
	 +----------------------------------------------------------
	 * @throws FcsException
	 +----------------------------------------------------------
	 */
	public function setUser() {
		$id     = $_POST['groupUserId'];
		$groupId	=	$_POST['groupId'];
		$group    =   D("Rolegroup");
		$group->delGroupUser($groupId);
		$result = $group->setGroupUsers($groupId,$id);
		if($result===false) {
			$this->error('授权失败！');
		}else {
			//删除浏览权限文件
			//删除浏览级别权限
			$obj_dir = new Dir;
			$directory =  DConfig_PATH."/BrowsecList";
			if(isset($directory)){
				$obj_dir->del($directory);
			}
			$this->success('授权成功！');
		}
	}
	/**
	 * @Title: _before_edit
	 * @Description: todo(进入修改)
	 * @author laicaixia
	 * @date 2013-5-31 下午6:14:52
	 * @throws
	 */
	public function _before_edit(){
		//请注意，这里的step只从整体权限控制处传过来的，方便页面刷新问题
		$step=$_GET['step'];
		if($step){
			$rel="rel/abc";
		}else{
			$rel="navTabId/__MODULE__";
		}
		$this->assign('rel',$rel);
	}
	/**
	 * @Title: _before_add
	 * @Description: todo(进入新增)
	 * @author laicaixia
	 * @date 2013-5-31 下午6:15:02
	 * @throws
	 */
	public function _before_add(){
		//请注意，这里的step只从整体权限控制处传过来的，方便页面刷新问题
		$step=$_GET['step'];
		if($step){
			$rel="rel/abc";
		}else{
			$rel="navTabId/__MODULE__";
		}
		$this->assign('rel',$rel);
	}
	
	public function lookupRoleGroupPerson(){
		$model=D("User");
		// 获取选中组
		$group    =   D("Rolegroup");
		//获取当前用户组信息
		$groupId =  isset($_REQUEST['selectGroupId'])?$_REQUEST['selectGroupId']:'';
		$groupUserList = array();
		if(!empty($groupId)) {
			//获取当前组的用户列表
			$isidlist = array();
			$grouplist	=	$group->getGroupUserList($groupId);
			foreach ($grouplist as $vo){
				$isidlist[]	=	$vo['id'];
			}
			$groupmap['id'] = array('in', $isidlist);
			//查询出当前已选中用户
			$groupUserList = $model->where($groupmap)->getField('id,account,name,duty_id,dept_id');
		}
		$this->assign('groupUserList',$groupUserList);
		$this->assign('selectGroupId',$_REQUEST['selectGroupId']);
		
		$map = array();
		$map['status'] = array('GT',0);
		//是管理员的不显示出来
		//$map['name'] = array('NEQ','管理员');
		if (method_exists ( $this, '_filterLookupSelectUser' )) {
			$this->_filterLookupSelectUser ( $map );
		}
		$list = $model->field("id,name,dept_id,email,mobile,pinyin")->where($map)->order('sort ASC')->select();
		foreach ($list as $uk=>$uval){
			if($uval['employeid']){
				$working=getFieldBy($uval['employeid'], 'id', 'working', 'mis_hr_personnel_person_info');
				if($working==0){
					unset($list[$uk]);
				}
			}
		}
		$num = count($list);
		$this->assign("num",$num);// 总人数
		$returnarr = array();
		$dptmodel = D("MisSystemDepartment");//部门表
		$deptlist = $dptmodel->where("status=1")->order("id asc")->field('id,name,parentid')->select();
		//部门树形
		$returnarr=$dptmodel->getDeptZtree($_SESSION['companyid'],'','','','',1);
		$this->assign('usertree',$returnarr);
		//用户组的树
		$rolegroup = array();
		$rolegroupModel = D('Rolegroup');
		$rolegroupList = $rolegroupModel->where("status=1")->order("id asc")->field('id,name,pid')->select();//所有的组
		$rolegroup_userModel = M('rolegroup_user');
		$rolegroup_userList = $rolegroup_userModel->field("rolegroup_id,user_id")->order('rolegroup_id ASC')->select();
		foreach ($rolegroupList as $k => $v) {
			foreach ($rolegroup_userList as $k2 => $v2) {
				if($v["id"] == $v2["rolegroup_id"]){
					$rolegroupList[$k]["useridarr"][] = $v2["user_id"];
				}
			}
		}
		foreach($rolegroupList as $ke=>$va){
			$newRole=array();
			$newRole['id'] = -$va['id'];
			$newRole['pId'] = 0;
			$newRole['title'] = $va['name']; //光标提示信息
			$newRole['name'] = missubstr($va['name'],18,true); //结点名字，太多会被截取,针对于现在的ZTREE，宽度不能超过18个字符。
			$newRole['open'] = false;
			$istrue = false;
			$userarr = array();
			$usernamearr = array();
			$emailarr = array();
			foreach ($list as $k2 => $v2) {
				if(in_array($v2['id'],$va["useridarr"])){
					$istrue = true;
					$newv2 = array();
					$userarr[] = $v2['id'];
					$usernamearr[] = $v2['name'];
					$emailarr[] = $v2['email'];
					$newv2['email'] = $v2['email'];
					$newv2['id'] = $v2['id'];
					$newv2['userid'] = $v2['id'];
					$newv2['pId'] = -$va['id'];
					$newv2['pinyin'] = $v2['pinyin']; //拼音
					$newv2['title'] = $v2['name']; //光标提示信息
					$newv2['username'] = $v2['name']; //光标提示信息
					$newv2['name'] = missubstr($v2['name'],18,true); //结点名字，太多会被截取,针对于现在的ZTREE，宽度不能超过18个字符。
					$newv2['icon'] = "__PUBLIC__/Images/icon/group.png";
					$newv2['open'] = false;
					array_push($rolegroup,$newv2);
				}
			}
			if($istrue){
				$newRole["userid"] = implode(",",$userarr);
				$newRole["email"] = implode(",",$emailarr);
				$newRole["username"] = implode(",",$usernamearr);
				array_push($rolegroup,$newRole);
			}
		}
// 		dump($rolegroup);
		$this->assign('rolegrouptree',json_encode($rolegroup));
		//公司的树
		$companytree=$dptmodel->getDeptZtree('','','','','',2);
		$this->assign('sysCompanytree',$companytree);
// 		$this->assign('usertree',$returnarr);
		
		$this->assign('data',$_POST["data"]);
		$this->assign('ulid',$_POST["ulid"]);
		if($_POST["ulid"]){
			$this->display("multiple");// 选多个用户
		} else {
			$this->display("singleUser");// 选单个用户
		}
	}
}
?>