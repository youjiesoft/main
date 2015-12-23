<?php
/**
 * @Title: MisHrJobInfoAction
 * @Package 
 * @Description: TODO(人事模块，岗位)
 * @author liminggang
 * @company 重庆特米洛科技有限公司˾
 * @copyright 重庆特米洛科技有限公司˾
 * @date 2013-1-10 19:18:54
 * @version V1.0
 */
class MisRoleGroupAction extends CommonAction {
	/**
	 * @Title: _filter 
	 * @Description: todo(构造检索条件) 
	 * @param unknown_type $map  
	 * @author xiafengqin 
	 * @date 2013-6-1 下午3:49:29 
	 * @throws
	 */
	public function _filter(&$map){
		if ($_SESSION["a"] != 1)$map['status']=array("gt",-1);
		//排序字段默认
		if(!$_REQUEST ['orderField']){
			$_REQUEST ['orderField'] = "sort";
		}
		//顺序
		if(!$_REQUEST ['orderDirection']){
			$_REQUEST ['orderDirection'] = "asc";
		}
	}
	
	public function _before_add(){
		//查询所有部门信息
		$deptmodel = D("MisSystemDepartment");
		$deptlist=$deptmodel->where("status=1")->select();
		$deptlists=$this->selectTree($deptlist,0,0,$_REQUEST['departmentid']);
		$this->assign("deptlist",$deptlists);
		//echo $_REQUEST['departmentid'].">>>".getFieldBy($_REQUEST['departmentid'], "id", "companyid", "mis_system_department");exit;
		//查询当前部门是否有下级部门
		$deptVo=$deptmodel->where("status=1 and parentid=".$_REQUEST['departmentid'])->field("id")->find();
		$departmentid="";
		if($deptVo){
			$departmentid=$deptVo['id'];
		}else{
			$departmentid=$_REQUEST['departmentid'];
		}
		$this->assign("departmentid",$departmentid);
		$this->assign("companyid",getFieldBy($departmentid, "id", "companyid", "mis_system_department"));
		//公司信息进入新增
		if($_REQUEST['com']){
			$this->assign("com",$_REQUEST['com']);
		}
		$this->assign('depdata',$this->getCompanyDept());
	}
	
	public function add($obj) {
		$modelname = 'Rolegroup';
		$this->getSystemConfigDetail($modelname);
		$model = D($modelname);
		//这里是否需要统一修改成唯一权限控制函数方法？
		if( !isset($_SESSION['a']) ){
			if( $_SESSION[strtolower($modelname.'_index')]==2 ){////判断部门及子部门权限
				$map["createid"]=array("in",$_SESSION['user_dep_all_child']);
			}else if($_SESSION[strtolower($modelname.'_index')]==3){//判断部门权限
				$map["createid"]=array("in",$_SESSION['user_dep_all_self']);
			}else if($_SESSION[strtolower($modelname.'_index')]==4){//判断个人权限
				$map["createid"]=$_SESSION[C('USER_AUTH_KEY')];
			}
		}
	
		$map['status']=1;
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		// 上一条数据ID
		$updataid = $model->where($map)->order('id desc')->getField('id');
		$this->assign("updataid",$updataid);
	
		//取出插入insert时存入的当前表单新增的缓存
		$mrdmodel = D('MisRuntimeData');
		$data = $mrdmodel->getRuntimeCache($modelname,'add');
		//$this->assign("vo",$data); // 修改页面有值原因@nbmxkj 201421
	
		//此处添加_after_add方法，方便在方法中，判断跳转
		$module2 = A($modelname);
		if (method_exists($module2,"_after_add")) {
			call_user_func(array(&$module2,"_after_add"),$data);
		}
		$this->display ($obj);
	}
	
	function insert() {
		$RolegroupDao = M("rolegroup");
		//获取相应的数据
		$data['departmentid']=$_POST['departmentid'];
		$data['name'] = $_POST['name'];;
		$data['remark'] = $_POST['remark'];
		$data['catgory'] = 3;
		$data['sort'] = $_POST['sort'];
		$data['companyid'] = $_POST['companyid'];
		$rolegroupId=$RolegroupDao->add($data);
		if ($rolegroupId) {
			$this->success ( "保存成功！");
		} else {
			$this->error ( L('_ERROR_') );
		}
	}
	
	
	public function _before_edit(){
		$this->assign('depdata',$this->getCompanyDept());
	}
	
	function edit($num=1) {
		//获取当前控制器名称
		$name='Rolegroup';
		$model = D ( $name );
		//获取当前主键
		$id = $_REQUEST [$model->getPk ()];
		$map['id']=$id;
		$vo = $model->where($map)->find();
	
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		//读取动态配制
		$this->getSystemConfigDetail($name);
		// 上一条数据ID
		$map['id'] = array("lt",$id);
		$updataid = $model->where($map)->order('id desc')->getField('id');
		$this->assign("updataid",$updataid);
		// 下一条数据ID
		$map['id'] = array("gt",$id);
		$downdataid = $model->where($map)->getField('id');
		$this->assign("downdataid",$downdataid);
	
		//获取附件信息
		$this->getAttachedRecordList($id,true,true,$name);
		// 获取现 可能有的地区信息
		$areaModel = M('MisAddressRecord');
		$areainfomap['tablename'] = 'Rolegroup';
		$areainfomap['tableid'] = $id ;
		$areainfoarry = $areaModel->where($areainfomap)->find();
		$this->assign('areainfoarry' , $areainfoarry);
		//lookup带参数查询
		$module=A($name);
		//		$module->_after_edit();
		if (method_exists($module,"_after_edit")) {
			call_user_func(array(&$module,"_after_edit"),&$vo);
		}
		$this->assign( 'vo', $vo );
		if($num){
			$this->display ();
		}
	
	}
	
	public function _after_edit($vo){
		//查询所有部门信息
		$deptmodel = D("MisSystemDepartment");
		$deptlist=$deptmodel->where("status=1")->select();
		$deptlists=$this->selectTree($deptlist,0,0,$vo['departmentid']);
		$this->assign("deptlist",$deptlists);
	}
	

	function update() {
		$name='Rolegroup';
		$model = D ( $name );
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$list=$model->save ();
		if (false !== $list) {
			$vo = $model->where("id =".$_POST['id'])->find();
			//如果存在项目编号跟项目任务，而且审核状态为审核完毕
			if ($vo['projectid'] && $vo['projectworkid'] && $_POST['operateid'] ) {
				$MSFFModel = D ( 'MisProjectFlowForm' );
				$MSFFModel->setWorkComplete ( $vo['projectworkid'] ,$vo['projectid']);
				// 这里不报错，如果报错的话会有很多影响，如果没更新状态成功，那需要他人为再去更新状态
			}
			//修改附件信息
			$this->swf_upload($_POST['id']);
			// 地区信息修改 @nbmxkj - 20141030 16:05
			$this->area_info($_POST['id']);
			//执行成功后，用A方法进行实例化，判断当前控制器中是否存在_after_update方法
			$module2=A($name);
			if (method_exists($module2,"_after_update")) {
				call_user_func(array(&$module2,"_after_update"),$list);
			}
			// 这里因为在新增时启动流程调用了 update方法。而又不能中断，所有如果是 新增启动流程。将不输出成功信息
			$startprocess = $_POST ['startprocess'];
			if ($startprocess !== 1) {
				$this->success ( "表单数据保存成功！" );
			}
		} else {
			$this->error ( L('_ERROR_') );
		}
	}
	
	public function delete() {
	//	dump($_REQUEST);exit;
		//删除指定记录
		$name='Rolegroup';
		$model = D ($name);
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
				//超管对已删除数据再次删除时为真删
				if($_SESSION['a']){
					$condition["status"] = array ("eq",-1);
					$list=$model->where ( $condition )->delete();
					$condition["status"] = array ("neq",-1);
				}
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				//判断当前删除的岗位是否存在员工信息。
				$UserDeptDutyDao = M("user_dept_duty");
				$hrmap['worktype'] = $id;
// 				$hrmap['leavestatus'] = 1; //在职
				$hrmap['status']=1; //状态为正常
				$hrresult=$UserDeptDutyDao->where($hrmap)->count();
				if($hrresult){
					$this->error("当前岗位已经存在人事资料，请勿删除！");
				}
				//先查询rolegroup信息
				$list=$model->where($condition)->field("rolegroup_id")->select();
				$rolegroupArr = array();
				foreach($list as $key=>$val){
					array_push($rolegroupArr, $val['rolegroup_id']);
				}
				if($rolegroupArr){
					//修改相应的rolegroup数据
					$RolegroupDao = M("rolegroup");
					$where = array();
					$where['id'] = array(' in ',$rolegroupArr);
					$result=$RolegroupDao->where ( $where )->setField ( 'status', - 1 );
					if($result===false){
						$this->error("岗位删除失败，请联系管理员");
					}
				}
				$result=$model->where ( $condition )->setField ( 'status', - 1 );
				if ($result!==false) {
					$this->success ( L('_SUCCESS_') );
				} else {
					$this->error ( L('_ERROR_') );
				}
			} else {
				$this->error ( C('_ERROR_ACTION_') );
			}
		}
	}
	

}
?>