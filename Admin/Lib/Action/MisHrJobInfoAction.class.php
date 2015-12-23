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
class MisHrJobInfoAction extends CommonAction {
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
		$deptlists=$this->selectTree($deptlist,0,0,$_REQUEST['deptid']);
		$this->assign("deptlist",$deptlists);
		//echo $_REQUEST['deptid'].">>>".getFieldBy($_REQUEST['deptid'], "id", "companyid", "mis_system_department");exit;
		//查询当前部门是否有下级部门
		$deptVo=$deptmodel->where("status=1 and parentid=".$_REQUEST['deptid'])->field("id")->find();
		$deptid="";
		if($deptVo){
			$deptid=$deptVo['id'];
		}else{
			$deptid=$_REQUEST['deptid'];
		}
		$this->assign("deptid",$deptid);
		$this->assign("companyid",getFieldBy($deptid, "id", "companyid", "mis_system_department"));
		//公司信息进入新增
		if($_REQUEST['com']){
			$this->assign("com",$_REQUEST['com']);
		}
		$this->assign('depdata',$this->getCompanyDept());
	}
	public function _before_edit(){
		$deptid=getFieldBy($_REQUEST['id'], "id", "deptid", "mis_hr_job_info");
		$this->assign("companyid",getFieldBy($deptid, "id", "companyid", "mis_system_department"));
		$this->assign('depdata',$this->getCompanyDept());
		$this->assign("cdeptid",$deptid);
		//公司信息进入修改
		if($_REQUEST['com']){
			$this->assign("com",$_REQUEST['com']);
		}
	}
	public function _before_insert(){
		//获取相应的数据
		$deptid=$_POST['deptid'];
		$name = $_POST['name'];
		//检查岗位是否同名。
		$MisHrJobInfoModel = M("mis_hr_job_info");
		$where = array();
		$where['deptid'] =$deptid;
		$where['name'] = $name;
		$where['status'] = 1;
		$cmlist=$MisHrJobInfoModel->where($where)->find();
		if($cmlist){
			$deptname=getFieldBy($deptid,'id','name','mis_system_department');
			$this->error($deptname.$name."已经存在，请勿重复新建岗位！");
		}
	}
	
	
	public function _after_insert($id){
		//插入成功后，向rolegroup插入相应的数据
		$RolegroupDao = M("rolegroup");
		//获取相应的数据
		$deptid=$_POST['deptid'];
		$name = getFieldBy($deptid,'id','name','mis_system_department').$_POST['name'];
		$data['name'] = $name;
		$data['remark'] = $_POST['remark'];
		$data['catgory'] = 3;
		$data['sort'] = $_POST['sort'];
		$data['companyid'] = $_POST['companyid'];
		$rolegroupId=$RolegroupDao->add($data);
		if($rolegroupId){
			//成功后，反写rolegroupid到岗位表中
			$MisHrJobInfoModel = M("mis_hr_job_info");
			$MisHrJobInfoModel->where("id = ".$id)->setField("rolegroup_id",$rolegroupId);
		}else{
			$this->error("添加岗位信息失败，请联系管理员");
		}
		//$this->getPinyin('rolegroup', 'name');
		//创建成功，则给管理员发信息，负责对该项目人员类别进行权限分配
		//构造内部邮件title 和content
		//$this->pushMessage();
		//$this->pushMessage($recipientListID);
	}
	
	public function _after_edit($vo){
		//查询所有部门信息
		$deptmodel = D("MisSystemDepartment");
		$deptlist=$deptmodel->where("status=1")->select();
		$deptlists=$this->selectTree($deptlist,0,0,$vo['deptid']);
		$this->assign("deptlist",$deptlists);
	}
	
	public function _before_update(){
		//修改相应的rolegroup数据
		$RolegroupDao = M("rolegroup");
		//获取相应的数据
		$deptid=$_POST['deptid'];
		$name = getFieldBy($deptid,'id','name','mis_system_department').$_POST['name'];
		$data['name'] = $name;
		$data['remark'] = $_POST['remark'];
		$data['companyid'] = $_POST['companyid'];
		$data['catgory'] = 3;
		$data['sort'] = $_POST['sort'];
		$data['status'] = $_POST['status'];
		$data['id'] = $_POST['rolegroup_id'];
		$rolegroupId=$RolegroupDao->save($data);
		$data = array();
		if(!$rolegroupId){
			$this->error("岗位信息修改失败，请联系管理员");
		}
	}
	public function delete() {
		//删除指定记录
		$name=$this->getActionName();
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
	
	public function index() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		//构成树形菜单
		$model = D('MisSystemDepartment');
		$typeTree=$model->getDeptZtree('','true',"MisHrJobInfoBox");
		$this->assign('typeTree',$typeTree);
		$deptid=$_REQUEST['deptid']?$_REQUEST['deptid']:0;
		if($deptid){
			if($_REQUEST['companyid']){
				$deptmap=array();
				$deptmap['companyid']=$_REQUEST['companyid'];
			}
			$deptmap["status"]=1;
			$deptlist=$model->where($deptmap)->select();
			$deptlist =array_unique(array_filter (explode(",",$this->findAlldept($deptlist,$deptid))));
			$map['deptid'] = array(' in ',$deptlist);
			$this->assign("deptid",$deptid);
		}
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		//将树节点参数赋值给模板，用于刷新
		$this->assign('companyid',$_REQUEST['companyid']);
		$this->assign('deptid',$_REQUEST['deptid']);
		$this->assign('ptId',$_REQUEST['ptId']);
		
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
		if( intval($_POST['dwzloadhtml']) ){
			$this->display("dwzloadindex");exit;
		}
		//首页收件箱调用方法，为ajax调用
		if ($_GET['type'] == "ajaxcall") {
			$this->display ("ajax_index");exit;
		}
		if($_REQUEST['com']){
			$this->assign("com",$_REQUEST['com']);
		}
		if($_REQUEST['jump'] == "jump"){
			if($_REQUEST['com']){
				$this->display("MisSystemCompany:indexjoblist");exit;
			}else{
				$this->display('indexview');exit;
			}
		}
		if($_REQUEST['com']){
			$this->display ("MisSystemCompany:indexjob");
		}else{
			$this->display ();
		}
		return;
	}
}
?>