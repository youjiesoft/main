<?php
/**
 * @Title: MisHrEmployeeLeaveManagementAction
 * @Package 人力资源管理
 * @Description: todo(员工请假管理)
 * @author lcx
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-7-17 上午11:21:33
 * @version V1.0
 */
class MisHrEmployeeLeaveManagementAction extends CommonAuditAction {
	/** @Title: _filter
	 * @Description: (构造检索条件)
	 * @author lcx
	 * @date 2013-7-17 下午3:59:44
	 * @throws
	 */
	public function _filter(&$map) {
		if ($_SESSION["a"] != 1){
			if( !$_SESSION['mishremployeeleavemanagement_leaveview']){
				$map['status'] = 1;
				$uid=$_SESSION[C('USER_AUTH_KEY')];
				$personid=getFieldBy($uid, 'id', 'employeid', 'user');
				$map['_string']="personid = $personid or createid = $uid";
			}
		}
	}

	/**
	 * @Title: getAuditTree
	 * @Description: todo(获取审核列表树)
	 * @author 杨东
	 * @date 2013-6-14 上午9:16:20
	 * @throws
	 */
	protected function getAuditTree(){
		$name = $this->getActionName();
		$rel = $name."indexview";
		$this->assign("actionName",$name);
		$defaultSelect = $_GET['default'];
		if(!$defaultSelect){
			$defaultSelect = Cookie::get($name.'defaultSelect');
		}
		$arrSelect = array();// 权限作用
		$defaultSelectUrl = "";
		$tree = array(); // 树初始化
		$_POST['indextype'] = 1;
		$session = strtolower($name);
		$masmodel = D($name);
		$ConfigListModel = D('SystemConfigList');
		$doctimelist = $ConfigListModel->GetValue('doctime');// 单据时间检索
		$auditStateLsit = array(
				'4' => array(
						'id' => '3',
						'name' => '已批准',
						'icon' => '__PUBLIC__/Images/icon/order_accept.png',
				),
				'2' => array(
						'id' => '1',
						'name' => '待批准',
						'icon' => '__PUBLIC__/Images/icon/order_wait.png',
				),
				'3' => array(
						'id' => '2',
						'name' => '审核中',
						'icon' => '__PUBLIC__/Images/icon/order_audit.png',
				),
				'1' => array(
						'id' => '-1',
						'name' => '未批准',
						'icon' => '__PUBLIC__/Images/icon/order_decline.png',
				),
		);
		// 个人单据
		if ($_SESSION["a"] == 1 || $_SESSION[$session."_index"]) {
			$pid = 1;
			if(!$defaultSelect){
				$defaultSelect = $pid;
			}
			$map = array();
			if (method_exists ( $this, '_filter' )) {
				$this->_filter ( &$map );
			}
			$arrSelect[$pid] = $pid;
			$tree[] = array(
					'id' => $pid,
					'pId' => 0,
					'name' => '请假申请列表',
					'title' => '请假申请列表',
					'rel' => $rel,
					'target' => 'ajax',
					'icon' => "__PUBLIC__/Images/icon/order_user.png",
					'url' => "__URL__/index/type/1/defid/".$pid,
					'open' => true
			);//个人单据根节点
			$id = 1;
			$qx_name=$name;
			if(substr($name, -4)=="View"){
				$qx_name = substr($name,0, -4);
			}
			if( !isset($_SESSION['a']) ){
				if( $_SESSION[strtolower($qx_name.'_index')]==2 ){////判断部门及子部门权限
					$c["createid"]=array("in",$_SESSION['user_dep_all_child']);
				}else if($_SESSION[strtolower($qx_name.'_index')]==3){//判断部门权限
					$c["createid"]=array("in",$_SESSION['user_dep_all_self']);
				}else if($_SESSION[strtolower($qx_name.'_index')]==4){//判断个人权限
					$c["createid"]=$_SESSION[C('USER_AUTH_KEY')];
				}
			}
			foreach ($auditStateLsit as $k => $v) {
				$valname = $v['name'];
				if ($v['id'] != '-2') {
					$c["status"] = 1;
					$c['auditState'] = $v['id'];
					if($map){
						$c = array_merge($map,$c);
					}
					$count = $masmodel->where($c)->count("*");
					$valname = $v['name']."(".$count.")";
				}
				$thisid = $pid.''.$k;
				$tree[] =  array(
						'id' => $thisid,
						'pId' => $pid,
						'name' => $valname,
						'title' => $valname,
						'rel' => $rel,
						'target' => 'ajax',
						'icon' => $v['icon'],
						'url' => '__URL__/index/type/1/ortype/'.$v['id'].'/defid/'.$thisid,
						'open' => false
				);
				foreach ($doctimelist as $k1 => $v1) {
					$thisid2 = $thisid.''.$k1;
					$tree[] =  array(
							'id' => $thisid2,
							'pId' => $thisid,
							'name' => $v1['name'],
							'title' => $v1['name'],
							'rel' => $rel,
							'icon' => $v1['icon'],
							'target' => 'ajax',
							'url' => '__URL__/index/type/1/ortype/'.$v['id'].'/time/'.$k1.'/defid/'.$thisid2,
							'open' => false
					);
				}
			}
		}
		// 待审任务单据
		// 		if ($_SESSION["a"] == 1 || $_SESSION[$session."_waitaudit"]) {
		$pid = 2;
		if(!$defaultSelect){
			$defaultSelect = $pid;
		}
		$arrSelect[$pid] = $pid;
		$maps = array();
		if($map){
			$maps = array_merge($map,$maps);
		}
		$maps = array();
		$maps['status'] =1;
		$maps['_string'] = 'FIND_IN_SET(  '.$_SESSION[C('USER_AUTH_KEY')].',curAuditUser )';
		$count = $masmodel->where($maps)->count('id');
		$tree[] = array(
				'id' => $pid,
				'pId' => 0,
				'name' => '待审请假申请('.$count.')',
				'title' => '待审请假申请('.$count.')',
				'rel' => $rel,
				'icon' => "__PUBLIC__/Images/icon/order_missionwait.png",
				'target' => 'ajax',
				'url' => '__URL__/waitAudit/defid/'.$pid,
				'open' => true
		);//待审任务根节点
		foreach ($doctimelist as $k1 => $v1) {
			$thisid = $pid.''.$k1;
			$tree[] =  array(
					'id' => $thisid,
					'pId' => $pid,
					'name' => $v1['name'],
					'title' => $v1['name'],
					'rel' => $rel,
					'icon' => $v1['icon'],
					'target' => 'ajax',
					'url' => '__URL__/waitAudit/time/'.$k1.'/defid/'.$thisid,
					'open' => true
			);
		}
		// 		}
		// 已审任务单据
		// 		if ($_SESSION["a"] == 1 || $_SESSION[$session."_alreadyaudit"]) {
		$pid = 3;
		if(!$defaultSelect){
			$defaultSelect = $pid;
		}
		$arrSelect[$pid] = $pid;
		$maps = array();
		$maps['status'] =1;
		$maps['_string'] = 'FIND_IN_SET(  '.$_SESSION[C('USER_AUTH_KEY')].',alreadyAuditUser )';
		$count = $masmodel->where($maps)->count('id');
		$tree[] = array(
				'id' => $pid,
				'pId' => 0,
				'name' => '已审请假申请('.$count.')',
				'title' => '已审请假申请('.$count.')',
				'rel' => $rel,
				'target' => 'ajax',
				'icon' => "__PUBLIC__/Images/icon/order_missionaudit.png",
				'url' => '__URL__/alreadyAudit/defid/'.$pid,
				'open' => true
		);//已审任务根节点
		foreach ($doctimelist as $k1 => $v1) {
			$thisid = $pid.''.$k1;
			$tree[] =  array(
					'id' => $thisid,
					'pId' => $pid,
					'name' => $v1['name'],
					'title' => $v1['name'],
					'rel' => $rel,
					'icon' => $v1['icon'],
					'target' => 'ajax',
					'url' => '__URL__/alreadyAudit/time/'.$k1.'/defid/'.$thisid,
					'open' => true
			);
		}
		// 		}
		$default = substr($defaultSelect, 0,1);
		if(!$arrSelect[$default]){
			$arrKey = array_keys($arrSelect);
			$defaultSelect = $default = $arrKey[0];
		}
		$_REQUEST['defid'] = $defaultSelect;
		if($default == 1){
			$_REQUEST['type'] = 1;
			if (strlen($defaultSelect) > 1) $_REQUEST['ortype'] = $auditStateLsit[substr($defaultSelect, 1,1)]['id'];
			if (strlen($defaultSelect) > 2) $_REQUEST['time'] = substr($defaultSelect, 2,1);
			$this->index();
		} else if($default == 2){
			if (strlen($defaultSelect) > 1) $_REQUEST['time'] = substr($defaultSelect, 1,1);
			$this->waitAudit();
		} else if($default == 3){
			if (strlen($defaultSelect) > 1) $_REQUEST['time'] = substr($defaultSelect, 1,1);
			$this->alreadyAudit();
		}
		$this->assign("default",$default);
		$this->assign("defaultSelect",$defaultSelect);
		$this->assign("auditTree",json_encode($tree));
	}

	/**
	 * @Title: _before_add
	 * @Description: todo(打开页面前置函数)
	 * @author lcx
	 * @throws
	 */
	public function _before_add(){
		$this->common();
		//订单号可写
		$scnmodel = D('SystemConfigNumber');
		$elaveno = $scnmodel->GetRulesNO('mis_hr_personnel_leave_info');
		$this->assign("orderno", $elaveno);
		$this->assign('time',time());
	}
	public function _before_insert(){
		//dirct by　yuansl 2014 06  11执行过滤
		$mode_x = D("MisHrPersonnelLeaveInfo");
		$colidList = $mode_x->where("status > 0 and auditState != 3 ")->getField('personid',true);
		// 		dump($colidList);
		if(in_array($_REQUEST['personid'],$colidList)){
			$this->error("请不要重复提交请假单!");
			exit;
		}
		if($_POST['forleavetype']){//代办请假申请单
			if($_POST['deptid']){
				$_POST['deptname']=getFieldBy($_POST['deptid'], 'id', 'name', 'mis_system_department');
			}
			if($_POST['dutylevelid']){
				$_POST['cname']=getFieldBy($_POST['dutylevelid'], 'id', 'name', 'duty');
			}
		}
	}
	/**
	 * @Title: _before_auditEdit
	 * @Description: todo(打开审核页面前置函数)
	 * @author
	 * @date 2013-5-31 下午4:14:13
	 * @throws
	 */
	public function _before_auditEdit(){
		$this->common();
	}
	/**
	 * @Title: _before_auditView
	 * @Description: todo(打开审核预览前置函数)
	 * @author
	 * @date 2013-5-31 下午4:14:56
	 * @throws
	 */
	public function _before_auditView(){
		$this->common();
	}
	/**
	 * @Title: common
	 * @Description: todo(公共调用函数)
	 * @author
	 * @date 2013-5-31 下午4:07:47
	 * @throws
	 */
	private function common(){
		//单据类型
		$OrderTypes     =D("MisOrderTypes");
		$OrderTypesList =$OrderTypes->where("type='59'  and status=1")->select();
		$this->assign('OrderTypesList',$OrderTypesList);
		$userModel=D('User');
		$MisHrPersonnelPersonInfoModel=D('MisHrPersonnelPersonInfo');
		$dutyModel=D('Duty');
		$MisSystemDepartmentModel=D('MisSystemDepartment');
		//查询当前登录者信息
		$uid=$_SESSION[C('USER_AUTH_KEY')];
		// 		$uid = getFieldBy($_SESSION[C('USER_AUTH_KEY')], '', '', 'mis_hr_personnel_person_info');
		//$cu_eid = getFieldBy($uid, 'id', 'employeid', 'user');
		$userList=$userModel->where(" status=1 and id=".$uid)->find();
		$MisHrBasicEmployeeList=$MisHrPersonnelPersonInfoModel->where(" status=1  and id=".$userList['employeid'])->find();
		$this->assign("MisHrBasicEmployeeList",$MisHrBasicEmployeeList);
		//当前登陆者部门
		$depList=$MisSystemDepartmentModel->where(" status=1 and  id=".$MisHrBasicEmployeeList['deptid'])->find();
		$this->assign("depList",$depList);
		//查看当前登陆者职级
		$dutyList=$dutyModel->where(" status=1 and id=".$MisHrBasicEmployeeList['dutylevelid'])->find();
		$this->assign("dutyList",$dutyList);
		$this->assign("MisHrBasicEmployeeList",$MisHrBasicEmployeeList);
		//查询请假类别
		$model=D("Typeinfo");
		$typelist=$model->where("status=1 and pid=1 and typeid=2")->getField("id,name");
		$this->assign("typelist",$typelist);
		//请假编号是否可写
		$scnmodel = D('SystemConfigNumber');
		$writable= $scnmodel->GetWritable('mis_hr_personnel_leave_info');
		$this->assign("writable",$writable);
	}
	/**
	 * @Title: _before_edit
	 * @Description: todo(打开修改页面前置函数)
	 * @author
	 * @date 2013-5-31 下午4:11:26
	 * @throws
	 */
	public function _before_edit(){
		$this->common();
	}
	/**
	 * (non-PHPdoc)
	 * @see CommonAction::lookupmanage()
	 */
	public function lookupmanage(){
		$hrsearchlist=$_POST['lookuphrsearchlist'];
		$this->assign("lookuphrsearchlist",$hrsearchlist);
		$step=$_GET['step'];
		$this->assign("step",$step);
		$model= M('mis_system_department');
		$deptlist = $model->where("status=1")->order("id asc")->select();
		$param['rel']="positiveBox";
		$param['url']="__URL__/lookupmanage/jump/1/deptid/#id#/";
		$param['open']=true;
		$typeTree = $this->getTree($deptlist,$param);
		$this->assign('tree',$typeTree);
		$map = array();
		$searchby = str_replace("-", ".", $_POST["searchby"]);
		$keyword=$this->escapeChar($_POST["keyword"]);
		//动态配置显示字段
		$name=$this->getActionName();
		if (! empty ( $name )) {
			$qx_name=$name;
			if(substr($name, -4)=="View"){
				$qx_name = substr($name,0, -4);
			}
			//验证浏览及权限
			if( !isset($_SESSION['a']) ){
				$map=D('User')->getAccessfilter($qx_name,$map);	
			}
		}
		if($_POST["keyword"]){
			if($searchby =="all"){
				$where['mis_hr_personnel_person_info.name']=array('like','%'.$keyword.'%');
				$departmentlist = $model->where("status=1 and name like '%".$keyword."%'")->getField("id,name");
				if($departmentlist){
					$where['deptid']=array('in',array_keys($departmentlist));
				}
				$where['dutyname']=array('like','%'.$keyword.'%');
				$where['_logic']='OR';
				$map['_complex'] = $where;
			}else{
				if($searchby=="mis_system_department.name"){
					$departmentlist = $model->where("status=1 and name like '%".$keyword."%'")->getField("id,name");
					$map['deptid']=array('in',array_keys($departmentlist));
				}else{
					$map[$searchby] = array('like','%'.$keyword.'%');
				}
			}
			$searchby = str_replace(".", "-", $_POST["searchby"]);
			$this->assign('keyword',$keyword);
			$this->assign('searchby',$searchby);
		}
		if($_GET['workstatus']){
			$map['workstatus']=$_GET['workstatus'];    //试用
		}else{
			$map['working']=1; //在职员工
		}
		$this->assign("workstatus",$_GET['workstatus']);
		$deptid		= $_REQUEST['deptid'];
		if ($deptid ) {
			$map['mis_hr_personnel_person_info.deptid'] = $deptid;
		}
		$common=A("Common");
		$common->_list('MisHrBasicEmployee',$map);
		$this->assign('deptid',$deptid);
		if ($_REQUEST['jump']) {
			$this->display('lookupmanagelist');
		} else {
			$this->display("lookupmanage");
		}
	}
	/**
	 * @Title: lookupgethours
	 * @Description: todo(动态计算请假时间)
	 * @author l
	 * @date 2013-4-18 下午4:26:35
	 * @throws
	 */
	function lookupgethours(){
		$s = strtotime(str_replace ( '&nbsp;',' ', $_REQUEST["sdate"] ));
		$e = strtotime(str_replace ( '&nbsp;',' ', $_REQUEST["edate"] ));
		if($e<=$s || !$s || !$e){
			echo "";
			exit;
		}
		import ( "@.ORG.Date" );
		$date=new Date(intval( $s ));
		echo  $date->timeDiff($e,2,false);
		exit;
	}
}
?>