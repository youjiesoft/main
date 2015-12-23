<?php
/**
 * @Title: NodeAction
 * @Package package_name
 * @Description: todo(节点管理)
 * @author laicaixia
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-5-31 下午5:36:44
 * @version V1.0
 */
class NodeAction extends CommonAction {
	/**
	 * @Title: _filter
	 * @Description: todo(检索)
	 * @param unknown_type $map
	 * @author laicaixia
	 * @date 2013-5-31 下午5:37:11
	 * @throws
	 */
	public function _filter(&$map){
		if(empty($_POST['search']) && !isset($map['pid']) ) {
			if($_REQUEST['group_id'] || $_REQUEST['level']){
				if($_REQUEST['group_id']) $map['group_id']=$_REQUEST['group_id'];
				if($_REQUEST['level']) $map['level']=$_REQUEST['level'];
			}else {
				$map['pid']	=0;
			}
		}
		//获取父节点ID
		$pid=$_REQUEST['pid']?$_REQUEST['pid']:0;
		//将父节点赋值到$_request中。方便操作按钮toolbar上面取值
		$_REQUEST['pid'] = $pid;

		$this->assign("pid",$pid);
		$node	=	M("Node");
		//TP方法。getById,根据ID查询当前模型的数据
		$node->getById($_REQUEST['pid']);
		$this->assign('type',$node->type); //取type值
		$nodelevel= (2 == $node->type)?  $node->level: $node->level+1;
		$this->assign('level',$nodelevel);
		if($pid){
			$map['pid'] = $pid;
		}
		//获取分组id
		$group_id = $_REQUEST['group_id']?$_REQUEST['group_id']:0;
		//将分组ID赋值到$_request中，方便操作按钮toolbar上面取值
		$_REQUEST['group_id'] = $group_id;
		$this->assign("group_id",$group_id);
		if ($group_id) {
			//查询出组
			$map['group_id'] = $group_id;
		}
	}

	public function index() {
		//获取左侧结构数据
		$nodeModel = D("Node");
		$groupNodelist = $nodeModel->getGroupNodeList();
		$this->assign('typeTree',json_encode($groupNodelist));
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		//查询模型
		$name = 'NodeView';
		if (! empty ( $name )) {
			$this->_list ( $name, $map ,"sort",'asc');
		}
		//配置列名称
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail("Node");
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		//扩展工具栏操作    配置操作按钮
		$toolbarextension = $scdmodel->getDetail("Node",true,'toolbar');
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		if ($_REQUEST['frame']) {
			$this->display ('indexlist');
		} else {
			$this->display ();
		}
	}
	
	
	public function lookupcoding(){
		$name='Node';
		$model = D ( $name );
		//获取当前主键
		$id = $_REQUEST [$model->getPk ()];
		$map['id']=$id;
		$vo = $model->where($map)->find();
		$this->assign('vo',$vo);
		$ordernoModel=D('MisSystemOrderno');
		$condition['tablename']=$vo['name'];
		$voList = $ordernoModel->where($condition)->find();
		$this->assign('voList',$voList);
		$this->display();
	}
	public function lookupinsertCoding(){
		$ordernoModel=D('MisSystemOrderno');
		$type=$_REQUEST['type'];
		if (false === $ordernoModel->create ()) {
			$this->error ( $ordernoModel->getError () );
		}
		if($type=='edit'){
			$map['tablename']=$_REQUEST['tablename'];
			$data['maxlevels']=$_REQUEST['maxlevels'];
			$data['maxlenght']=$_REQUEST['maxlenght'];
			$data['singlelenght']=$_REQUEST['singlelenght'];
			$list=$ordernoModel->where($map)->save ($data);
		}else{
			$list=$ordernoModel->add ();
		}
		if ($list!==false) {
			$this->success ( L('_SUCCESS_') ,'',$list);
			exit;
		} else {
			$this->error ( L('_ERROR_') );
		}
	}
	/**
	 * @Title: _before_add
	 * @Description: todo(进入新增)
	 * @author laicaixia
	 * @date 2013-5-31 下午5:38:44
	 * @throws
	 */
	public function _before_add() {
		$node	=	M("Node");
		//TP方法。getById,根据ID查询当前模型的数据
		$node->getById($_GET['pid']);
		$this->assign('pid',$node->id); //取ID值
		$this->assign('type',$node->type); //取type值
		$nodelevel= (2 == $node->type)?  $node->level: $node->level+1;
		$this->assign('level',$nodelevel);

		//得到节点类型
		$typeList = M('nodetype')->select();
		$this->assign('treeTypeList',$typeList);

		//如果级数为4，则查出当前同级的所有节点
		if ($nodelevel) {
			$level_list = $node->where('status = 1 AND pid = ' . $node->id)->select();
			$this->assign('level_list', $level_list);
		}
		$this->assign('p_name',array('1'=>"项目名",'2'=>"面板名",'3'=>"模块名",'4'=>"操作名"));

	}
	/**
	 * @Title: _before_insert
	 * @Description: todo(执行新增)
	 * @author laicaixia
	 * @date 2013-5-31 下午5:39:08
	 * @throws
	 */
	public function _before_insert() {
		if($_POST['type']!=4 ){
			//验证除操作节点以为的节点是否重名
			$model=D("Node");
			$m['name']=$this->escapeStr($_POST['name']);
			$m['level']=array("neq",4);
			$m['type']=array("neq",4);
			$count = $model->where ( $m )->count ( '*' );
			if( $count > 0 ) $this->error("节点已存在，请重新输入！");
			//查询当前最大的排序数
			$map['pid'] = $_POST['pid'] ? $_POST['pid']:0;
			$map['type'] = $_POST['type'];
			$list=$model->where($map)->max('sort');
			$_POST['sort']=$list+1;
		}
	}
	/**
	 * @Title: _after_insert
	 * @Description: todo(生成基础权限组)
	 * @param int   $insertid          插入返回的ID
	 * @param int   $_POST['IsRole']   0不插入基础组，1插入基础组
	 * @author yangxi
	 * @date 2013-6-26 下午3:41:17
	 * @throws
	 */
	public function _after_insert( $insertid ){
		if( $_POST["level"]==4 ){
			$nodeModel = D("Node");  //node节点表
			$roleModel=D("Role");    //role基础角色表
			$accessModel=M("Access");//Access权限表
			if ($_POST['binding'] || ($_POST['bindingtype'] == 0 && $_POST['IsRole'] == 1) ) {
				//调用生产access节点方法
				$_POST['id'] = $insertid;
				$this->addAccess(false,$_POST['binding']);
			}
			if ($_POST['bebinding'] && $_POST['bindingtype'] == 1) {
				//被绑定功能
				foreach ($_POST['bebinding'] as $key => $val) {
					//查询被绑定方法是否有权限组
					$role_list = $roleModel->where('nodeid = ' . $val)->select();
					if ($role_list) {
						//如果有权限组，那么一定就在 Access 表中添加了数据
						////添加权限
						foreach($role_list as $k => $v){
							$data=array();
							$data["role_id"]	= $v["id"];
							$data["node_id"]	= $insertid;
							$data["level"]		= 4;
							$data["type"]		= 4;
							$data["pid"]		= $_POST['pid'];
							$data["plevels"]	= $v["plevels"];
							$result = $accessModel->add($data);
						}
					}
					else {
						//如果不是，则添加绑定方法权限组
						//获取绑定方法的信息
						$bindingnode = $nodeModel->where('id = ' . $val)->find();
						$_POST['pid'] = $bindingnode["pid"];
						$_POST['title'] = $bindingnode["title"];
						$_POST['id'] = $bindingnode["id"];
						$this->addAccess(false,array($insertid));
					}
				}
			}
		}
	}
	/**
	 * @Title: _before_update
	 * @Description: todo(执行修改)
	 * @author laicaixia
	 * @date 2013-5-31 下午5:39:23
	 * @throws
	 */
	public function _before_update(){
		if($_POST['type']!=4 ){
			$model=D("Node");
			$m['name']=$this->escapeStr($_POST['name']);
			$m['id']=array("neq",$this->escapeStr($_POST['id']));
			$m['level']=array("neq",4);
			$m['type']=array("neq",4);
			$count = $model->where ( $m )->count ( '*' );
			if( $count >0 ) $this->error("节点已存在，请重新输入！");
		}
	}
	/**
	 * @Title: _after_update
	 * @Description: todo(_after_update)
	 * @author jiangx
	 * @date 2013-7-2 下午3:39:23
	 * @throws
	 */
	public function _after_update(){
		$roleModel = D("Role");
		$nodeModel = D("Node");
		$accessModel = D("Access");
		//修改权限组 nodeidcategory 值
		$role_list = $roleModel->where('nodeid = ' . $_REQUEST['id'])->select();

		if ($role_list) {
			$isstatus = array();
			if ($_POST['bindingtype'] == 0 && $_POST['IsRole'] == 0) {
				//当不带绑定，且不生成权限组时，删除权限组(状态设为0)
				$isstatus['status'] = 0;
			} else {
				$isstatus['status'] = 1;
			}
			if ($_POST['oldtitle'] != $_POST['title']) {
				foreach ($role_list as $key => $val) {
					$data = array();
					$data['name'] = preg_replace('/_'.$_POST['oldtitle'].'$/', '_'.$_POST['title'], $val['name']);
					$data['nodeidcategory'] = $_POST['category'];
					$data = array_merge($data, $isstatus);
					$data['nodetitle'] = $_POST['title'];
					$roleModel->where('id = ' . $val['id'])->setField($data);
				}
			} else {
				$data = array();
				$data['nodeidcategory'] = $_POST['category'];
				$data = array_merge($data, $isstatus);
				$roleModel->where('nodeid = ' . $_REQUEST['id'])->setField($data);
			}
			if($isstatus['status'] == 0){
				$this->deleteAccess($role_list);
			}else{
				if($_POST['IsRole']!=$_POST['OldIsRole'] && $_POST['IsRole']==1 && $_POST['OldIsRole']==0){
					$this->addAccess($role_list);
				}
			}
		}
		if ($_POST['binding'] && $_POST['oldbinding']) {
			//获取绑定增加的方法节点id
			//绑定方法id
			$addfuncbinding = array_diff($_POST['binding'], $_POST['oldbinding']);
			//取消绑定方法id
			$delfuncbinding = array_diff($_POST['oldbinding'], $_POST['binding']);
		}
		if ($_POST['binding'] && (!$_POST['oldbinding'])) {
			//绑定方法id
			$addfuncbinding = $_POST['binding'];
		}
		if ((!$_POST['binding']) && $_POST['oldbinding']) {
			$delfuncbinding = $_POST['oldbinding'];
		}
		//增加绑定
		if ( $_POST['IsRole'] == 1 || $addfuncbinding && ($_POST['bindingtype'] ==1)) {
				
			if ($role_list) {
				//先删除再添加ACCESS
				$this->deleteAccess($role_list);
				$this->addAccess($role_list,$addfuncbinding);
			} else {
				//添加ACCESS
				$this->addAccess();
			}
		}
		//删除绑定
		if ($delfuncbinding) {
			$this->deleteAccess($role_list);
		}
	}
	private function addAccess($role_list,$addfuncbinding=null){
		$nodeModel=D('Node');
		$roleModel=D('Role');
		$accessModel=D('Access');
		if($role_list){
			////添加权限
			foreach($role_list as $k=>$v){
				//根据pid获取模块节点信息
				$vo = $nodeModel->find($v["nodepid"]);
				//根据模块的pid获取面板信息或者空面板信息
				$vo2 = $nodeModel->find($vo['pid']);
				//定义4个参数，存放上级id  $pid_type0 节点admin的ID，$pid_type1面板id ，$pid_type2模块ID或者空模块ID ，$pid_type3模块ID
				$pid_type0=$pid_type1=$pid_type2=$pid_type3="";
				if($vo2['type']==2){//kong mokuai
					$vo3 = $nodeModel->find($vo2['pid']);//获取面板
					$pid_type3=$vo['id'];//模块id
					$pid_type2=$vo2['id'];//控模块id
					$pid_type1=$vo3['id'];//面板模块id
					$pid_type0=$vo3['pid'];//项目id
				}else{//mianban
					$pid_type2=$vo['id'];//模块id
					$pid_type1=$vo2['id'];//面板模块id
					$pid_type0=$vo2['pid'];//项目id
				}

				if($pid_type0!="") $idlist[$v["id"]][]=$pid_type0;
				if($pid_type1!="") $idlist[$v["id"]][]=$pid_type1;
				if($pid_type2!="") $idlist[$v["id"]][]=$pid_type2;
				if($pid_type3!="") $idlist[$v["id"]][]=$pid_type3;

				$data=array();
				$data["role_id"]	=$v["id"];
				$data["node_id"]	=$v["nodeid"];
				$data["level"]		=4;
				$data["type"]		=4;
				$data["pid"]		=$v["nodepid"];
				$data["plevels"]	=$v["plevels"];
				$result =$accessModel->add($data);
				if($result){
					if( $idlist ){
						if ($_POST['bindingtype'] == 1) {
							foreach ($_POST['binding'] as $val) {
								$idlist[$v["id"]][] = $val;
							}
						}
						$roleModel->setGroupActions($v["id"],$idlist[$v["id"]], true);
					}
				}
			}
		}else{
			//根据pid获取模块节点信息
			$vo = $nodeModel->find($_POST["pid"]);
			//根据模块的pid获取面板信息或者空面板信息
			$vo2 = $nodeModel->find($vo['pid']);
				
			//获取父类信息
			$p_title = $nodeModel->where("id=".$_POST["pid"])->getField("title");
			$j=0;
			for($s=1;$s<=5;$s++){
				if($s==1){
					$t="所有";
				}else if($s==2){
					$t="公司";
				}else if($s==3){
					$t="部门";
				}else if($s==5){
					$t="禁用";
				}else{ $t="个人";
				}
				$data=array();
				$data["name"] = $p_title."_".$t."_".$_POST["title"];
				$data["nodetitle"] = $_POST["title"];
				$data["nodeidcategory"] = $_POST['category'];
				$data["plevels"] = $s;
				$data["nodepid"] = $_POST["pid"];
				$data["nodeid"] = $_POST['id'];
				$data["createtime"] = time();
				$data["createid"] = 1;
				$data["sort"] = $j;
				$data["status"] = 1;
				$j++;
				$list = $roleModel->add($data);
				if($list){
					$data["id"] = $list;
					$role_list[] = $data;
				}else{
					$this->error("插入role节点失败，请联系管理员");
				}
			}
			foreach($role_list as $k=>$v){
				//定义4个参数，存放上级id  $pid_type0 节点admin的ID，$pid_type1面板id ，$pid_type2模块ID或者空模块ID ，$pid_type3模块ID
				$pid_type0=$pid_type1=$pid_type2=$pid_type3="";
				if($vo2['type']==2){//kong mokuai
					$vo3 = $nodeModel->find($vo2['pid']);//获取面板
					$pid_type3 = $vo['id'];//模块id
					$pid_type2 = $vo2['id'];//控模块id
					$pid_type1 = $vo3['id'];//面板模块id
					$pid_type0 = $vo3['pid'];//项目id
				}else{//mianban
					$pid_type2 = $vo['id'];//模块id
					$pid_type1 = $vo2['id'];//面板模块id
					$pid_type0 = $vo2['pid'];//项目id
				}
				if($pid_type0!="") $idlist[$v["id"]][] = $pid_type0;
				if($pid_type1!="") $idlist[$v["id"]][] = $pid_type1;
				if($pid_type2!="") $idlist[$v["id"]][] = $pid_type2;
				if($pid_type3!="") $idlist[$v["id"]][] = $pid_type3;
				$data=array();
				$data["role_id"] = $v["id"];
				$data["node_id"] = $v["nodeid"];
				$data["level"]   = 4;
				$data["type"]    = 4;
				$data["pid"]     = $v["nodepid"];
				$data["plevels"] = $v["plevels"];
				$result = $accessModel->add($data);
				if ($result) {
					if ( $idlist ) {
						if ( $addfuncbinding && ($_POST['bindingtype'] ==1) ) {
							foreach ($addfuncbinding as $val) {
								$idlist[$v["id"]][] = $val;
							}
						}
						$result = $roleModel->setGroupActions($v["id"],$idlist[$v["id"]], true);
						if ($result === false) {
							$this->error ( L('_ERROR_') );
						}
					}
				} else {
					$this->error ( L('_ERROR_') );
				}
			}
		}
	}

	private function deleteAccess($role_list){
		$accessModel=D('Access');
		//存在权限组
		foreach ($role_list as $val) {
			//删除 access 数据
			$result = $accessModel->where('role_id = '. $val['id'])->delete();
			//改变 权限组 数据
			if(!$result){
				$this->error('删除权限出错');
			}
		}
		//再次删除余留数据
		$result=$accessModel->where('node_id = '. $_REQUEST['id'])->delete();
		if(!$result){
			$this->error('删除权限出错');
		}
	}
	/**
	 * @Title: _before_delete
	 * @Description: 删除之前操作
	 * @author jiangx
	 * @date 2013-7-2 下午3:39:23
	 * @throws
	 */
	public function _before_delete(){
		$roleModel = D("Role");
		$accessModel = D("Access");
		//获取权限组信息
		$role_list = $roleModel->where('nodeid = ' . $_REQUEST['id'])->select();
		if ($role_list) {
			//存在权限组
			foreach ($role_list as $val) {
				//删除 access 数据
				$result = $accessModel->where('role_id = '. $val['id'])->delete();
				//改变 权限组 数据
				$result = $roleModel->where('id = '. $val['id'])->delete();
			}
		}
		//再次删除余留数据
		$accessModel->where('node_id = '. $_REQUEST['id'])->delete();
	}

	/**
	 * @Title: _before_recycle
	 * @Description: todo(_before_recycle)
	 * @author jiangx
	 * @date 2013-7-2 下午3:39:23
	 * @throws
	 */
	public function _before_recycle(){
		$roleModel = D("Role");
		//还原权限组状态
		$roleModel->where('nodeid = ' . $_REQUEST['id'])->setField('status', 1);
	}
	/**
	 +----------------------------------------------------------
	 * 默认禁用操作
	 *
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 * @throws FcsException
	 +----------------------------------------------------------
	 */
	public function forbid() {
		$name=$this->getActionName();
		$model = D ($name);
		$pk = $model->getPk ();
		$id = $_GET [$pk];
		$condition = array ($pk => array ('in', $id ) );
		if (false !== $model->forbid ( $condition )) {
			$roleModel = D("Role");
			$accessModel = D("Access");
			//获取权限组信息
			$role_list = $roleModel->where('nodeid = ' . $id)->select();
			if ($role_list) {
				//改变 权限组 数据
				$where = array();
				$where['nodeid'] = $id;
				$result = $roleModel->where($where)->setField('status',0);
			}
			$this->success ( L ( '_SUCCESS_' ) );
		} else {
			$this->error ( L ( '_ERROR_' ) );
		}
	}
	/**
	 * @Title: _before_resume
	 * @Description: 恢复
	 * @author jiangx
	 * @date 2013-7-2 下午3:39:23
	 * @throws
	 */
	public function _before_resume(){
		$roleModel = D("Role");
		//还原权限组状态
		$roleModel->where('nodeid = ' . $_REQUEST['id'])->setField('status', 1);
	}
	/**
	 * @Title: _before_edit
	 * @Description: todo(进入修改)
	 * @author laicaixia
	 * @date 2013-5-31 下午5:39:48
	 * @throws
	 */
	public function _before_edit() {
		//得到节点类型
		$typeList = M('nodetype')->select();
		$this->assign('nodeTypeList',$typeList);

		$this->assign('p_name',array('1'=>"项目名",'2'=>"面板名",'3'=>"模块名",'4'=>"操作名"));
	}
	/**
	 * @Title: _after_edit
	 * @Description: todo(_after_edit)
	 * @author jiangx
	 * @date 2013-7-2 下午4:39:48
	 * @throws
	 */
	public function _after_edit($vo){
		//获取父类id
		//加载 category 分类
		$node = D('Node');
		//如果级数为4，则查出当前同级的所有节点
		if ($vo['level'] == 4) {
			$level_list = $node->where('id <> '.$vo['id'].' AND status = 1 AND pid = ' . $vo['pid'])->select();
		}
		// 		if ($level_list) {
		$rolemodel = D("Role");
		$access = D('Access');
		//获取绑定方法id 开始---->
		//获取当前节点权限组的一条
		$role = $rolemodel->where('nodeid = ' . $vo['id'])->find();
		if ($role) {
			//有权限组，传入页面
			$this->assign('IsRole',1);
			//查出 access 表中的数据
			$accesslist = $access->where('level  = 4 AND node_id <> '.$vo['id'].' AND role_id = ' . $role['id'])->field('node_id')->select();
			$nodeidlist = array();
			foreach ($accesslist as $val) {
				$nodeidlist[$val['node_id']] = $val['node_id'];
			}
		} else {
			$this->assign('IsRole', 0);
		}
		//获取绑定方法id <---- 结束
		// 		}
		if ($nodeidlist) {
			foreach ($level_list as $key => $val) {
				if($nodeidlist[$val['id']]){
					$level_list[$key]['binding'] = true;
				}
			}
			$this->assign('nodelist_binding', 1);
		} else {
			$this->assign('nodelist_binding', 0);
		}
		$this->assign('level_list', $level_list);
	}

	/**
	 * @Title: sort
	 * @Description: todo(排序(未启用))
	 * @author laicaixia
	 * @date 2013-6-2 上午9:57:03
	 * @throws
	 */
	private function sort(){
		$node = M('Node');
		if(!empty($_GET['sortId'])) {
			$map = array();
			$map['status'] = 1;
			$map['id']   = array('in',$_GET['sortId']);
			$sortList   =   $node->where($map)->order('sort asc')->select();
		}else{
			if(!empty($_GET['pid'])) {
				$pid  = $_GET['pid'];
			}else {
				$pid  = $_SESSION['currentNodeId'];
			}
			if($node->getById($pid)) {
				$level   =  $node->level+1;
			}else {
				$level   =  1;
			}
			$this->assign('level',$level);
			$sortList   =   $node->where('status=1 and pid='.$pid.' and level='.$level)->order('sort asc')->select();
		}
		$this->assign("sortList",$sortList);
		$this->display();
		return ;
	}
	/**
	 * @Title: setsort
	 * @Description: todo(排序)
	 * @author laicaixia
	 * @date 2013-5-31 下午5:46:07
	 * @throws
	 */
	public function setsort(){
		$direct=$_GET['d'];
		$id = $_GET ['id'];
		$model = D("Node");
		$map['id']=$id;
		$list=$model->where ( $map )->find();
		$map1['pid']=$list['pid'];
		$list1=$model->where ( $map1 )->field('sort,pid,id')->order('sort asc')->select();
		$count = count($list1);
		if( $count >1 ){
			if( $direct=="up"){
				foreach($list1 as $k=>$v){
					if($list['sort']==$v['sort']) break;
					$pos_before=$v['sort'];
					$id_before =$v['id'];
				}
				$model->where ( 'id='.$id_before )->setField( 'sort', $list['sort']);
				$model->where ( 'id='.$list['id'])->setField( 'sort',$pos_before );
				$this->success ( L ( '_SUCCESS_' ) );
			}else if($direct=="down"){
				$arr=array();
				$i=1;
				foreach($list1 as $k=>$v){
					if( $i<=$count){
						$pos_next=$list1[$k+1]['sort'];
						$id_next =$list1[$k+1]['id'];
					}
					if($list['sort']==$v['sort']) break
					$i++;
				}
				$model->where ( 'id='.$id_next )->setField( 'sort', $list['sort']);
				$model->where ( 'id='.$list['id'])->setField( 'sort',$pos_next );
				$this->success ( L ( '_SUCCESS_' ) );
			}
		}
		$this->success ( L ( '_SUCCESS_' ) );
	}
	/**
	 * @Title: filelistpath
	 * @Description: todo(未启用)
	 * @param unknown_type $dir
	 * @param unknown_type $pattern
	 * @return multitype:string unknown
	 * @author laicaixia
	 * @date 2013-5-31 下午5:51:07
	 * @throws
	 */
	private function filelistpath($dir,$pattern=''){
		$arr=array();
		$dir_handle=opendir($dir);
		if($dir_handle)
		{
			// 这里必须严格比较，因为返回的文件名可能是“0”
			while(($file=readdir($dir_handle))!==false)
			{
				if($file==='.' || $file==='..')
				{
					continue;
				}
				$tmp=realpath($dir.'/'.$file);
				if(is_dir($tmp))
				{
					$retArr=$this->filelistpath($tmp,$pattern);
					if(!empty($retArr))
					{
						$arr[]=$retArr;
					}
				}
				else
				{
					if($pattern==="" || preg_match($pattern,$tmp))
					{
						$arr[]=$tmp;
					}
				}
			}
			closedir($dir_handle);
		}
		return $arr;
	}
	/**
	 * @Title: upgradeTml
	 * @Description: todo(未启用)
	 * @author laicaixia
	 * @date 2013-5-31 下午5:51:12
	 * @throws
	 */
	public function upgradeTml(){
		set_time_limit(180000) ;
		$roleModel = D("Role");
		$accessModel = D("Access");
		$map["plevels"]=4;
		$map["status"] = 1;
		$list = $roleModel->where($map)->select();
		foreach($list as $k=>$v ){
			$accessmap=array();
			$accessmap["role_id"]=$v["id"];
			$accesslist = $accessModel->where($accessmap)->select();
			unset($v["id"]);
			$name=explode("_",$v["name"]);
			$name[1] = "禁用";
			$v["nodetitle"]=$name[2];
			$v["name"] = implode("_",$name);
			$v["plevels"]=5;
			//插入组
			$insertid = $roleModel->add($v);
			//插入权限
			$accessdata=array();
			foreach($accesslist as $k2=>$v2 ){
				$v2["plevels"]=5;
				$v2["role_id"]=$insertid;
				$re = $accessModel->add($v2);
				if(!$re){
					$this->transaction_model->rollback();
					echo "错误";exit;
				}
			}
		}
		$this->transaction_model->commit();//事务提交
		echo "ok";
		exit;

		$user=D("User");
		$list = $user->where("status=1")->getField("id",true);
		$role=D("RolrUser");
		foreach($list as $k=>$v ){
			echo "INSERT INTO `role_user` (`role_id`,`user_id`) VALUES (8405,'".$v."');<br />";
		}
			
		exit;


		//批量修改title
		//$roleModule=D("role");
		//$nodeModel = D("Node");
		//$map["type"]=4;
		//$l = $nodeModel->where($map)->select();
		//foreach($l as $k=>$v ){
		//	$data["nodeidcategory"]=$v["category"];
		//	$data["nodetitle"]=$v["title"];
		//	$roleModule->where("nodeid = ".$v["id"])->save($data);
		//}
		//exit;
		//

		/*升级节点排序
		 $model = D("Node");
		$t=array(0,1,2,3);
		foreach($t as $k=>$v){
		$list=$model->where ( 'type='.$v )->order('id asc')->select();
		$i=1;
		foreach($list as $k2=>$v2){
		$model->where ( 'id='.$v2['id'] )->setField( 'sort',$i);
		$i++;
		}
		}
		*/

		/*升级配置文件Models下面的list.inc.php和sublist.inc.php 中sortname
		 $path=DConfig_PATH . "/Models";
		$re=$this->filelistpath($path);
		foreach($re as $k=>$v){
		foreach($v as $k2=>$v2){
		$list =require $v2;
		foreach($list as $k3=>$v3 ){
		if(isset($v3['name']) && !isset($v3['sortname'])){
		$list[$k3]['sortname']=$v3['name'];
		}
		}
		$this->writeover($v2,"return ".$this->pw_var_export($list).";\n",true);
		echo "更新完毕文件：".$v2."<br/>";
		}
		}*/

		/*升级node 。重构node表，删除所有方法，并重新排序id*/
		/*$model = D("Node");
		 $model->where("type=4 and level=4")->delete();
		 $list = $model->order("type Asc , id Asc ")->getField("id",true);
		 foreach($list as $k=>$v){
			$id=$k+10000;
			$re = $model->where ( "id=".$v )->setField ( 'id', $id );
			if($re ){
			$model->where ( "pid=".$v )->setField ( 'pid', $id );
			}
			}
			$list = $model->order("type Asc , id Asc ")->getField("id",true);
			foreach($list as $k=>$v){
			$id=$k+1;
			$re = $model->where ( "id=".$v )->setField ( 'id', $id );
			if($re ){
			$model->where ( "pid=".$v )->setField ( 'pid', $id );
			}
			}
			*/
			
	}

	/**
	 * @Title: writeover
	 * @Description: todo(未启用)
	 * @param unknown_type $filename
	 * @param unknown_type $data
	 * @param unknown_type $safe
	 * @param unknown_type $method
	 * @author laicaixia
	 * @date 2013-5-31 下午5:51:17
	 * @throws
	 */
	private function writeover($filename,$data,$safe = false,$method='wb'){
		$safe && $data = "<?php \n".$data."\n?>";
		$handle = fopen($filename,$method);
		fwrite($handle,$data);
		fclose($handle);
	}
	/**
	 * @Title: pw_var_export
	 * @Description: todo(未启用)
	 * @param unknown_type $input
	 * @param unknown_type $t
	 * @return string
	 * @author laicaixia
	 * @date 2013-5-31 下午5:51:23
	 * @throws
	 */
	private function pw_var_export($input,$t = null) {
		$output = '';
		if (is_array($input))
		{
			$output .= "array(\r\n";
			foreach ($input as $key => $value)
			{
				$output .= $t."\t".$this->pw_var_export($key,$t."\t").' => '.  $this->pw_var_export($value,$t."\t");
				$output .= ",\r\n";
			}
			$output .= $t.')';
		} elseif (is_string($input)) {
			$output .= "'".str_replace(array("\\","'"),array("\\\\","\'"),$input)."'";
		} elseif (is_int($input) || is_double($input)) {
			$output .= "'".(string)$input."'";
		} elseif (is_bool($input)) {
			$output .= $input ? 'true' : 'false';
		} else {
			$output .= 'NULL';
		}

		return $output;
	}

	function upgradePackage(){
		$nodeids=$_GET["id"];
		$step=$_POST["step"];
		if( $step==2 ){
			$nodeids=$_POST["id"];
			if($nodeids){
				$ids = explode(",",$nodeids);
				$nodeMap["id"]=array("in",$ids);
				$nodeModel = M("node");
				$nodearr = $nodeModel->where( $nodeMap )->select();
				//打包对应模块的数据库节点权限部分，如需打包数据库模块对应表需自己导出sql
				if($_POST['nodesql']) $sqlnode = $this->bulidNodeSql($nodearr);
				$relatedfile = $_POST['relatedfile'];
				//打包所需基本程序文件
				if($relatedfile){
					foreach($nodearr as $k=>$v ){
						$modelname = $v["name"];
						$a =LIB_PATH."Action/".$modelname."Action.class.php";
						$m =LIB_PATH."Model/".$modelname."Model.class.php";
						$t = TMPL_PATH.C('DEFAULT_THEME')."/".$modelname;
						$d = APP_PATH."Dynamicconf/Models/".$modelname;
						import ( '@.ORG.FileUtil' );
						$FileUtil = new FileUtil();
						if( file_exists($a)){
							$a_a =APP_PATH."/modelpack/".$modelname."/Admin/Lib/Action/".$modelname."Action.class.php";
							$FileUtil->copyFile($a,$a_a);
						}
						if( file_exists($m)){
							$m_m =APP_PATH."/modelpack/".$modelname."/Admin/Lib/Model/".$modelname."Model.class.php";
							$FileUtil->copyFile($m,$m_m);
						}
						if( is_dir($t) ){
							$t_t =APP_PATH."/modelpack/".$modelname."/Admin/Tpl/".C('DEFAULT_THEME')."/".$modelname;
							$FileUtil->copyDirIncludeChildren($t,$t_t);
						}
						if( is_dir($d) ){
							$d_d =APP_PATH."/modelpack/".$modelname."/Admin/Dynamicconf/Models/".$modelname;
							$FileUtil->copyDirIncludeChildren($d,$d_d);
						}
						$this->getMd5fileByModel($modelname);
					}
				}

				//othersql
				$this->success("打包成功");
			}else{
				$this->error("找不到相应打包模块");
			}
		}else{
			$ids = explode(",",$nodeids);
			$nodeMap['level']=array("in",array(1,2,4));
			$nodeMap['id']=array("in",$ids);
			$nodeModel = M("node");
			$count = $nodeModel->where( $nodeMap )->find();
			//echo $nodeModel->getLastsql();
			if($count){
				$this->error("只能选择对模块打包!");
			}
			$this->assign("ids",$nodeids);
			$this->display();
		}
	}

	protected function bulidNodeSql($node){
		$phpcode="<?php\r\nclass UpgradeServiceAction extends CommonAction {\r\n\tpublic function doUpgrade(){\r\n\t\t\$nodemodel = D(\"Node\");\r\n\t\t\$rolemodel = D(\"Role\");\r\n\t\t\$accessmodel = D(\"Access\");";
		$nodemodel = M("node");
		$rolemodel = M("role");
		$accessmodel = M("access");
		$nodeid=array();
		$phpcode.="\r\n\t\t\$access_nodeid=array();\r\n\t\t\$access_nodeid['1']=1;\r\n\t\t\$access_nodeid['0']=0;";
		$phpcode.="\r\n\t\t\$access_roleid=array();";
		foreach($node as $k=>$data){
			$nodeid[]=$data["id"];
			$access_roleid=array();
			//判断父级在node有没有
			$parent_node= $nodemodel->find($data['pid']);
			if($parent_node['type']==2){
				$parentup_node= $nodemodel->find($parent_node['pid']);
				$valuespp=$fieldspp=array();
				foreach($parentup_node as $keyp=>$valpp){
					if($keypp=="id") continue;
					$valuepp   =  $this->parseValue($valpp);
					if(is_scalar($valuepp)) { // 过滤非标量数据
						$valuespp[] =  $valuepp;
						$fieldspp[] =  $this->parseKey($keypp);
					}
				}
				//判断是否有对应面板
				$map="name='".$parentup_node["name"]."' and type=".$parentup_node["type"];
				$phpcode.="\r\n\t\t\$parentup_node= \$nodemodel->where(\"".$map."\")->find();\r\n\t\tif(\$parentup_node){\r\n\t\t\t\$mianbannodeid=\$parentup_node[\"id\"];\r\n\t\t}else{";
				$phpcode.= "\r\n\t\t\t\$nodemodel->query(\"INSERT INTO `node` (".implode(',', $fieldspp).") VALUES (".implode(',', $valuespp).");\");\r\n\t\t\$mianbannodeid = \$nodemodel->max(\"id\");\r\n\t\t}";
				$phpcode.="\r\n\t\t\$access_nodeid[\"".$parentup_node["id"]."\"]=\$mianbannodeid;";
				//判断是否有对应空模块
				$parent_node["pid"]="-9999";
			}
			$valuesp=$fieldsp=array();
			foreach($parent_node as $keyp=>$valp){
				if($keyp=="id") continue;
				$valuep   =  $this->parseValue($valp);
				if(is_scalar($valuep)) { // 过滤非标量数据
					$valuesp[] =  $valuep;
					$fieldsp[] =  $this->parseKey($keyp);
				}
			}
			$map="name='".$parent_node["name"]."' and type=".$parent_node["type"];
			$phpcode.="\r\n\t\t\$parent_node= \$nodemodel->where(\"".$map."\")->find();\r\n\t\tif(\$parent_node){\r\n\t\t\t\$modelparentid=\$parent_node[\"id\"];\r\n\t\t}else{";
			$phpcode.= "\r\n\t\t\t\$nodemodel->query(\"INSERT INTO `node` (".implode(',', $fieldsp).") VALUES (".implode(',', $valuesp).");\");\r\n\t\t\t\$modelparentid = \$nodemodel->max(\"id\");\r\n\t\t}";
			$phpcode.="\r\n\t\t\$access_nodeid[\"".$parent_node["id"]."\"]=\$modelparentid;";
			$values=$fields=array();
			$data["pid"]="-9998";
			foreach($data as $key=>$val){
				if($key=="id") continue;
				$value   =  $this->parseValue($val);
				if(is_scalar($value)) { // 过滤非标量数据
					$values[] =  $value;
					$fields[] =  $this->parseKey($key);
				}
			}
			//插入当前模块
			$phpcode.= "\r\n\t\t\$nodemodel->query(\"INSERT INTO `node` (".implode(',', $fields).") VALUES (".implode(',', $values).");\");\r\n\t\t\$insertid = \$nodemodel->max(\"id\");";
			$phpcode.="\r\n\t\t\$access_nodeid[\"".$data["id"]."\"]=\$insertid;";
			$mapc=array();
			$mapc["pid"]=$data["id"];
			$mapc["level"]=4;
			$mapc["status"]=1;
			 
			$childrenarr = $nodemodel->where($mapc)->select();
			$phpcode_children="";
			foreach($childrenarr as $kc=>$datac){
				$valuesc=$fieldsc=array();
				$datac["pid"]="-10000";
				foreach($datac as $keyc=>$valc){
					if($keyc=="id") continue;
					$valuec   =  $this->parseValue($valc);
					if(is_scalar($valuec)) { // 过滤非标量数据
						$valuesc[]   =  $valuec;
						$fieldsc[]     =  $this->parseKey($keyc);
					}
				}

				//插入当前模块下的操作节点
				$phpcode_children.= "\r\n\t\t\$nodemodel->query(\"INSERT INTO `node` (".implode(',', $fieldsc).") VALUES (".implode(',', $valuesc).");\");";
				$phpcode_children.="\r\n\t\t\$access_nodeid[\"".$datac["id"]."\"]=\$nodemodel->max(\"id\");";
				//获取当前节点权限组的一条
				$role=array();
				$role["nodeid"]= $datac["id"];
				$rolearr = $rolemodel->where($role)->select();
				if ( $rolearr) {
					$phpcode_children.="\r\n\t\t\$insertidactionid = \$nodemodel->max(\"id\");";
					//$accesslist = $access->where('level  = 4 AND node_id <> '.$vo['id'].' AND role_id = ' . $role['id'])->field('node_id')->select();
					foreach($rolearr as $kr=>$datar){
						$valuesr=$fieldsr=array();
						$datar["nodeid"]="-10001";
						$datar["nodepid"]="-10000";
						foreach($datar as $keyr=>$valr){
			    if($keyr=="id") continue;
			    $valuer   =  $this->parseValue($valr);
			    if(is_scalar($valuer)) { // 过滤非标量数据
			    	$valuesr[] = $valuer;
			    	$fieldsr[] = $this->parseKey($keyr);
			    }
						}
						//插入当前模块下操作节点对应的role记录
						$phpcode_children.= "\r\n\t\t\$rolemodel->query(\"INSERT INTO `role` (".implode(',', $fieldsr).") VALUES (".implode(',', $valuesr).");\");";
						$phpcode_children.="\r\n\t\t\$access_roleid[\"".$datar["id"]."\"]=\$rolemodel->max(\"id\");";
					}
				}
			}
			 
			$phpcode=str_replace("-9998","\$modelparentid", $phpcode);
			$phpcode=str_replace("-9999","\$mianbannodeid", $phpcode);
			$phpcode_children=str_replace("-10000","\$insertid", $phpcode_children);
			$phpcode_children=str_replace("-10001","\$insertidactionid", $phpcode_children);
			$phpcode.=$phpcode_children;
			 

			 
		}
		//向access插入权限控制
		$map=array();
		$map["node_id"]=array("in",$nodeid);
		$rolelist = $accessmodel->where($map)->getField("role_id",true);
		$map=array();
		$map["role_id"]=array("in",$rolelist);
		$accesslist = $accessmodel->where($map)->select();


		foreach($accesslist as $keyc=>$valc){
			$valc['role_id']="\$access_roleid[".$valc['role_id']."]";
			$valc['node_id']="\$access_nodeid[".$valc['node_id']."]";
			$valc['pid']="\$access_nodeid[".$valc['pid']."]";
			$valuesc=$fieldsc=array();
			foreach($valc as $keycc=>$valcc){
				$valuecc   =  $this->parseValue($valcc);
				if(is_scalar($valuecc)) { // 过滤非标量数据
					$valuesc[]   =  $valuecc;
					$fieldsc[]     =  $this->parseKey($keycc);
				}
			}
			$phpcode.= "\r\n\t\t\$accessmodel->query(\"INSERT INTO `access` (".implode(',', $fieldsc).") VALUES (".implode(',', $valuesc).");\");";
		}

		$phpcode.="\r\n\t\t\$this->transaction_model->commit();\r\n\t\t\$result[\"status\"]=1;\r\n\t\treturn \$result;\r\n\t}\r\n}?>";
		file_put_contents(LIB_PATH.'Action/UpgradeServiceAction.class.php',$phpcode);
	}
	protected function parseValue($value) {
		if(is_string($value)) {
			$value = '\''.mysql_escape_string($value).'\'';
		}elseif(isset($value[0]) && is_string($value[0]) && strtolower($value[0]) == 'exp'){
			$value   =  mysql_escape_string($value[1]);
		}elseif(is_null($value)){
			$value   =  'null';
		}
		return $value;
	}
	protected function parseKey($value) {
		$value   =  trim($value);
		if( false !== strpos($value,' ') || false !== strpos($value,',') || false !== strpos($value,'*') ||  false !== strpos($value,'(') || false !== strpos($value,'.') || false !== strpos($value,'`')) {
			//如果包含* 或者 使用了sql方法 则不作处理
		}else{
			$value = '`'.$value.'`';
		}
		return $value;
	}
	/**
	 *todo 生成对应升级文件的对比字符串,管理员可以上传对应txt方便对比
	 *author qchlian
	 **/
	public function getMd5fileByModel( $modelname="" ){
		if($modelname){
			//action and upgrade file
			if(file_exists(APP_PATH."/Lib/Action/".$modelname."Action.class.php")){
				$upgradefilelist["Lib/Action/".$modelname."Action.class.php"] = md5_file(APP_PATH."/Lib/Action/".$modelname."Action.class.php");
			}
			if(file_exists(APP_PATH."/Lib/Action/UpgradeServiceAction.class.php")){
				$upgradefilelist["Lib/Action/UpgradeServiceAction.class.php"] = md5_file(APP_PATH."/Lib/Action/UpgradeServiceAction.class.php");
			}
			//model
			if(file_exists(APP_PATH."/Lib/Model/".$modelname."Model.class.php")){
				$upgradefilelist["Lib/Model/".$modelname."Model.class.php"] = md5_file(APP_PATH."/Lib/Model/".$modelname."Model.class.php");
			}
			//tpl
			$t = TMPL_PATH.C('DEFAULT_THEME')."/".$modelname;
			if( is_dir($t) ){
				$dir =APP_PATH."/Tpl/".C('DEFAULT_THEME')."/".$modelname;
				$dirHandle = opendir($dir);
				while(false !== ($file = readdir($dirHandle))) {
					if ($file == '.' || $file == '..') {
						continue;
					}
					$upgradefilelist["Tpl/".C('DEFAULT_THEME')."/".$modelname."/".$file] = md5_file(APP_PATH."/Tpl/".C('DEFAULT_THEME')."/".$modelname."/".$file);
				}
			}
			//d tpl
			$d = APP_PATH."/Dynamicconf/Models/".$modelname;
			if( is_dir($d) ){
				$dir =APP_PATH."/Dynamicconf/Models/".$modelname;
				$dirHandle = opendir($dir);
				while(false !== ($file = readdir($dirHandle))) {
					if ($file == '.' || $file == '..') {
						continue;
					}
					$upgradefilelist["Dynamicconf/Models/".$modelname."/".$file] = md5_file(APP_PATH."/Dynamicconf/Models/".$modelname."/".$file);
				}
			}
			if($upgradefilelist){
				foreach($upgradefilelist as $k=>$v){
					file_put_contents(APP_PATH."/modelpack/".$modelname."/cross_upgradelist_sc_utf8.txt","\r\n".$v." *".$k,FILE_APPEND);
				}
			}
		}
	}
	/**
	 * @Title: move
	 * @Description: todo(移动节点)
	 * @param $data
	 * @param $iscrossDomain 是否为跨类调用 动态表单调用此函数 jiangx 2014-02-22
	 * @author jiangx
	 * @date 2013-11-18
	 * @throws
	 */
	public function move($data = array(), $iscrossDomain = false){
		if ($iscrossDomain) {
			$temporaryvar = $_POST; //把原$_POST赋值给一个临时变量  --- 不可删，用于动态表单调用
			$_POST = $data;
		}
		$mNodeModel = D("Node");
		$model	= D("Group");
		$aMap = array();
		$aMap['id'] = $_REQUEST["id"];
		if (!$aMap['id']) {
			$aMap['id'] = $_POST["id"];
		}
		$aMap['status'] = 1;
		$aNodeResult = $mNodeModel->where($aMap)->find();
		if (!$aNodeResult) {
			$this->error ( '无数据，请刷新页面再试' );
		}
		if ($_POST['optiontype'] == 1) { //移动保存
			$ProcessNodeModel=D("ProcessNode");
			//修改流程配置pname
			if(getFieldBy($_REQUEST['name'], 'name', 'id', 'process_node')){
				$nodate['pname']=getFieldBy($_REQUEST['pid'], 'id', 'name', 'node');
				$nodate['updateid']=$_SESSION[C('USER_AUTH_KEY')];
				$nodate['updatetime']=time();
				$ProcessNodeModel->where("id=".getFieldBy($_REQUEST['name'], 'name', 'id', 'process_node')." and status=1 ")->save($nodate);
			}
			if (!$_POST['pid']) {
				unset($_POST['pid']);
			}
			//修改access表
			$accessModel = M("access");
			$mRoleModel = D("Role");
			$data = array();
			$accessMap = array();
			//移动第三级节点
			if ($aNodeResult['level'] == 3) {
				//二级节点信息
				//$pidnode = $model->where('id= '. $aNodeResult['pid'])->find();
				//查出第三节点下面的所有子节点对应的role数据
				$aChildsNode = $mNodeModel->where('pid = '. $aNodeResult['id'])->select();
				foreach ($aChildsNode as $key => $val) {
					$aRole = $mRoleModel->where('nodeid = '. $val['id'])->getField('id', true);
					if ($aRole) {
						$where = array();
						$where['role_id'] = array('in', $aRole);
						//修改第三级acess
						$where['level'] = 3;
						$data = array();
						$data['pid'] = $_POST['pid'];
						$list = $accessModel->where($where)->save($data);
						if (false === $list) {
							$this->error( L('_ERROR_') );
						}
						//修改第二级access
						$where['level'] = 2;
						$data = array();
						$data['node_id'] = $_POST['pid'];
						$list = $accessModel->where($where)->save($data);
						if (false === $list) {
							$this->error( L('_ERROR_') );
						}
					}
				}
				//修改所有子节点的group_id
				if ($aNodeResult['group_id'] != $_POST['group_id']) {
					$data = array();
					$data['group_id'] = $_POST['group_id'];
					$list = $mNodeModel->where('pid= '. $aNodeResult['id'])->save($data);
				}
			}
			//移动第二级节点
			if ($aNodeResult['level'] == 2) {
				//获取当前节点下所有第三节点id
				$aChildNodeid = $mNodeModel->where('pid = '.$aNodeResult['id'])->getField('id', true);
				if($aChildNodeid){
					//存在下级的时候，才进行修改下级内容
					
					//修改所有第四节点的group_id
					$data = array();
					$data['group_id'] = $_POST['group_id'];
					$map = array();
					$map['pid'] = array('in', $aChildNodeid);
					$list = $mNodeModel->where($map)->save($data);
					if (false === $list) {
						$this->error( L('_ERROR_') );
					}
					//修改所有第三节点的group_id
					$map = array();
					$map['id'] = array('in', $aChildNodeid);
					$list = $mNodeModel->where($map)->save($data);
					if (false === $list) {
						$this->error( L('_ERROR_') );
					}
				}
			}
			if (false === $mNodeModel->create ()) {
				$this->error( $mNodeModel->getError() );
			}
			if (false === $mNodeModel->save() ) {
				$this->error( L('_ERROR_') );
			}
			//动态表单调用此函数 jiangx 2014-02-22
			if ($iscrossDomain) {
				$_POST = $temporaryvar; ////把原临时变量赋值给还原$_POST  --- 不可删，用于动态表单调用
				return true;
			} else {
				$this->success( L('_SUCCESS_') );
			}
		} else {
			if ($aNodeResult['level'] == 3) {
				$mappnode = array();
				$mappnode['group_id'] = $aNodeResult['group_id'];
				$mappnode['level'] = 2;
				$pnodelist = $mNodeModel->where($mappnode)->select();
				$this->assign('pnodelist', $pnodelist);
			}
			$list	=	$model->where('status=1')->order("sorts asc")->getField('id,name,status');
			$this->assign('list', $list);
			$this->assign('vo', $aNodeResult);
			$this->assign('p_name',array('2'=>"面板名",'3'=>"模块名"));
			$this->display();
		}
	}
	/**
	 * @Title: lookupgetpnode 
	 * @Description: 节点移动的时候，获取分组下面的面板   
	 * @author liminggang 
	 * @date 2014-9-4 下午6:41:46 
	 * @throws
	 */
	public function lookupgetpnode(){
		$mNodeModel = D("Node");
		$nodelist = $mNodeModel->where("status='1' and level=2 and group_id='".$_POST['valu']."'")->getField("id,title");
		//给模板赋值
		$arr = array();
		if($nodelist){
			foreach($nodelist as $k=>$v){
				$arr2=array();
				$arr2[]=$k;
				$arr2[]=$v;
				array_push($arr,$arr2);
			}
		}
		echo  json_encode($arr);
	}
}
?>
