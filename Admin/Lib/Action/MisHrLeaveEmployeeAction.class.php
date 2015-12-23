<?php
/**
 * @Title: MisHrLeaveEmployeeAction 
 * @Package package_name
 * @Description: todo(人事离职申请) 
 * @author renling 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-7-19 上午11:57:12 
 * @version V1.0
 */
class MisHrLeaveEmployeeAction extends CommonAuditAction{
	/**
	 * (non-PHPdoc)
	 * @see CommonAuditAction::index()
	 */
	public function index(){
		if ($_REQUEST['type']) {
			$name=$this->getActionName();
			$map = $this->_search ($name);
			if (method_exists ( $this, '_filter' )) {
				$this->_filter ( $map );
			}
			//验证浏览及权限
			$qx_name=$name;
			if(substr($name, -4)=="View"){
				$qx_name = substr($name,0, -4);
			}
			if( !isset($_SESSION['a']) ){
				if( $_SESSION[strtolower($qx_name.'_index')]==2 ){////判断部门及子部门权限
					$map["createid"]=array("in",$_SESSION['user_dep_all_child']);
				}else if($_SESSION[strtolower($qx_name.'_index')]==3){//判断部门权限
					$map["createid"]=array("in",$_SESSION['user_dep_all_self']);
				}else if($_SESSION[strtolower($qx_name.'_index')]==4){//判断个人权限
					$map["createid"]=$_SESSION[C('USER_AUTH_KEY')];
				}
			}
			if($_REQUEST['time'] != ""){
				$time = $this->timeToString($_REQUEST['time']);
				$map['createtime'] = array($time[1],$time[0]);
				$this->assign('time',$_REQUEST['time']);
			}
			$ConfigListModel= D('SystemConfigList');
			$auditStateLsit	= $ConfigListModel->GetValue('auditState');// 审核状态
			$masmodel = D($name);
			foreach ($auditStateLsit as $k => $v) {
				if ($v['id'] != '-2') {
					$c["status"] = 1;
					$c['auditState'] = $v['id'];
					$c = array_merge($map,$c);
					$count = $masmodel->where($c)->count("*");
					$auditStateLsit[$k]['name'] = $v['name']."(".$count.")";
				}
			}
			$this->assign('auditStateLsit',$auditStateLsit);
			$ortype	=-2;
			if ($_REQUEST['ortype'] !== null ) {
				$ortype = $_REQUEST['ortype'];
			}
			if ($ortype != -2) {
				$map['auditState'] = array('eq',$ortype);
			}
			$this->assign('onetype',$ortype);
			$this->_list("MisHrPersonnelLeaveView",$map);
			//扩展工具栏操作
			$scdmodel = D('SystemConfigDetail');
			$toolbarextension = $scdmodel->getDetail($name,true,'toolbar');
			if ($toolbarextension) {
				$this->assign ( 'toolbarextension', $toolbarextension );
			}
			$zhi= ($_SESSION['navTab_data']!='') ? $_SESSION['navTab_data']:0;
			$this->assign('zhi',$zhi);
			if( intval($_POST['dwzloadhtml']) ){$this->display("dwzloadindex");exit;}
			if($_REQUEST['defid']){
				Cookie::delete($name.'defaultSelect');
				Cookie::set($name.'defaultSelect', $_REQUEST['defid']);
			}
			if(!$_POST['indextype']){
				$this->display('indexview');
			}
		} else {
			$this->getAuditTree();
			$this->display("CommonAudit:index");
		}
	}
	/**
	 * @Title: _before_insert
	 * @Description: todo(插入前置函数)
	 * @author
	 * @date 2013-5-31 下午4:08:58
	 * @throws
	 */
	public function _before_insert(){
		//查询此离职申请人是否已提交过相同单据
		$MisHrLeaveEmployeeModel=D('MisHrLeaveEmployee');
		$MisHrLeaveEmployeeResult=$MisHrLeaveEmployeeModel->where("status=1 and employeeid=".$_POST['employeeid'])->find();
		if($MisHrLeaveEmployeeResult){
			$this->error("请不要重复提交离职申请单！");
			
		}
		//人事模型
		$MisHrBasicEmployeeModel=D('MisHrBasicEmployee');
		$MisHrBasicEmployeeVo=$MisHrBasicEmployeeModel->where("status=1 and id=".$_POST['employeeid'])->find();
		//查询职级ID level id
		$_POST['dutylevelid']=$MisHrBasicEmployeeVo['dutylevelid'];
		$_POST['level']=getFieldBy($_POST['dutylevelid'], 'id', 'level', 'duty');
		$this->checkifexistcodeororder('mis_hr_leave_employee','orderno');
		if($_REQUEST['isprincipal']){
			if($_POST['leavetype']==2){
				$_POST['leavetype']=4;
			}else{
				$_POST['leavetype']=3;
			}
			
		}
	}
	/**
	 * @Title: _before_update 
	 * @Description: todo(修改前置函数)   
	 * @author renling 
	 * @date 2013-12-7 下午5:34:11 
	 * @throws
	 */
	public function _before_update(){
		//查询此离职申请人是否已提交过相同单据
		$MisHrLeaveEmployeeModel=D('MisHrLeaveEmployee');
		$MisHrLeaveEmployeeResult=$MisHrLeaveEmployeeModel->where("status=1 and employeeid=".$_POST['employeeid'])->count();
		if($MisHrLeaveEmployeeResult>1){
			$this->error("请不要重复提交离职申请单！");
				
		}
		if($_POST['oldleavetype']){
			if($_POST['oldleavetype'] == 3 || $_POST['oldleavetype'] == 4){
				if($_POST['leavetype']==1){
					$_POST['oldleavetype'] == 3;
				}else{
					$_POST['oldleavetype'] == 4;
				}
			}
		}
		//人事模型
		$MisHrBasicEmployeeModel=D('MisHrBasicEmployee');
		$MisHrBasicEmployeeVo=$MisHrBasicEmployeeModel->where("status=1 and id=".$_POST['employeeid'])->find();
		//查询职级ID level id
		$_POST['dutylevelid']=$MisHrBasicEmployeeVo['dutylevelid'];
		$_POST['level']=getFieldBy($_POST['dutylevelid'], 'id', 'level', 'duty');
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
			$arrSelect[$pid] = $pid;
			$tree[] = array(
					'id' => $pid,
					'pId' => 0,
					'name' => '离职申请列表',
					'title' => '离职申请列表',
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
			$maps['status'] =1;
			$maps['_string'] = 'FIND_IN_SET(  '.$_SESSION[C('USER_AUTH_KEY')].',curAuditUser )';
			$count = $masmodel->where($maps)->count('id');
			$tree[] = array(
					'id' => $pid,
					'pId' => 0,
					'name' => '待审离职申请('.$count.')',
					'title' => '待审离职申请('.$count.')',
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
					'name' => '已审离职申请('.$count.')',
					'title' => '已审离职申请('.$count.')',
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
	 * @author
	 * @throws
	 */
	public function _before_add(){
		//单据类型
		$OrderTypes     =D("MisOrderTypes");
		$OrderTypesList =$OrderTypes->where("type='60'  and status=1")->select();
		$this->assign('OrderTypesList',$OrderTypesList);
		//人事独立类别
		$model=D("Typeinfo");
		//查询离职原因
		$typelist=$model->where("status=1 and pid=23 and typeid=2")->getField("id,name");
		$this->assign("typelist",$typelist);
		//订单编号是否可写
		$scnmodel = D('SystemConfigNumber');
		$writable= $scnmodel->GetWritable('mis_hr_leave_employee');
		$this->assign("writable",$writable);

		$userModel=D('User');
		$MisHrBasicEmployeeModel=D('MisHrBasicEmployee');
		$MisSystemDepartmentModel=D('MisSystemDepartment');
		//查询当前登录者信息
		$uid=$_SESSION[C('USER_AUTH_KEY')];
		$userList=$userModel->where(" status=1 and id=".$uid)->find();
		$MisHrBasicEmployeeList=$MisHrBasicEmployeeModel->where(" status=1  and id=".$userList['employeid'])->find();
		//当前登陆者部门
		$depList=$MisSystemDepartmentModel->where(" status=1 and  id=".$MisHrBasicEmployeeList['deptid'])->find();
		$this->assign("MisHrBasicEmployeeList",$MisHrBasicEmployeeList);
		$this->assign("depList",$depList);
		$MisHrBasicEmployeeModel=D('MisHrBasicEmployee');
		$userModel=D('user');
		$uid=$_SESSION[C('USER_AUTH_KEY')];
		$userList=$userModel->where(" status=1 and id=".$uid)->find();
		$MisHrBasicEmployeeList=$MisHrBasicEmployeeModel->where("  status=1 and  id=".$userList['employeid'])->find();
		$this->assign("MisHrBasicEmployeeList",$MisHrBasicEmployeeList);
		//计算工龄
		$s = $MisHrBasicEmployeeList["indate"];
		$e = time();
		if($e<=$s || !$s || !$e){
			$workage="";
		}else{
		import ( "@.ORG.Date" );
		$date=new Date(intval( $s ));
		$workage=$date->timeDiff($e,2,false);
		$this->assign("workage",$workage);
		}
		//查询当前登录者信息
		//自动生成离职订单编号
		$scnmodel = D('SystemConfigNumber');
		$etverno = $scnmodel->GetRulesNO('mis_hr_leave_employee');
		$this->assign("orderno", $etverno);
		$this->assign("time",time());
	}
	//Ajax 请求计算工期
	public function lookupgetdate(){
		$s = strtotime($_REQUEST["sdate"]);
		$e = strtotime($_REQUEST["edate"]);
		if($e<=$s || !$s || !$e){
			echo "";
			exit;
		}
		import ( "@.ORG.Date" );
		$date=new Date(intval( $s ));
		echo  $date->timeDiff($e,2,false);
		exit;
	}
	/**
	 * @Title: common
	 * @Description: todo(公共调用函数)
	 * @author 杨东
	 * @date 2013-5-31 下午4:07:47
	 * @throws
	 */
	private function common(){
		//单据类型
		$OrderTypes     =D("MisOrderTypes");
		$OrderTypesList =$OrderTypes->where("type='60'  and status=1")->select();
		$this->assign('OrderTypesList',$OrderTypesList);
		//人事独立类别
		$model=D("Typeinfo");
		//查询离职原因
		$typelist=$model->where("status=1 and pid=23 and typeid=2")->getField("id,name");
		$this->assign("typelist",$typelist);
		//离职类别
		$typelists=$model->where("status=1 and pid=23 and typeid=2")->getField("id,name");
		$this->assign("forLeaveList",$typelists);
		//订单编号是否可写
		$scnmodel = D('SystemConfigNumber');
		$writable= $scnmodel->GetWritable('mis_hr_personnel_quit_info');
		$this->assign("writable",$writable);

		$userModel=D('User');
		$MisHrLeaveEmployeeModel=D('MisHrLeaveEmployee');
		$MisHrBasicEmployeeModel=D('MisHrBasicEmployee');
		$MisSystemDepartmentModel=D('MisSystemDepartment');
		//查询当前登录者信息
		$id=$_GET['id'];
		$MisHrLeaveEmployeeList=$MisHrLeaveEmployeeModel->where(" status=1  and id=".$id)->find();
		$MisHrBasicEmployeeList=$MisHrBasicEmployeeModel->where("status=1  and id=".$MisHrLeaveEmployeeList['employeeid'])->find();
		
		//当前登陆者部门
		$depList=$MisSystemDepartmentModel->where(" status=1 and  id=".$MisHrBasicEmployeeList['deptid'])->find();
		$this->assign("MisHrBasicEmployeeList",$MisHrBasicEmployeeList);
		$this->assign("depList",$depList);
		//计算工龄
		$s = $MisHrBasicEmployeeList["indate"];
		if($MisHrLeaveEmployeeList['leavedate']){
			$e =intval($MisHrLeaveEmployeeList['leavedate']);
		}else{
			$e = time();
		}
		if($e<=$s || !$s || !$e){
			$workage="";
			//$this->display();
		} else {
			import ( "@.ORG.Date" );
			$date=new Date(intval( $s ));
			$workage=$date->timeDiff($e,2,false);
			$this->assign("workage",$workage);
		}
		$this->assign("time",$time);
	}
	function edit() {
		$name=$this->getActionName();
		$model = D ( 'MisHrPersonnelLeaveView' );
		$qx_name=$name;
		if(substr($name, -4)=="View"){
			$qx_name = substr($name,0, -4);
		}
		if( !isset($_SESSION['a']) ){
			if( $_SESSION[strtolower($qx_name.'_index')]==2 ){////判断部门及子部门权限
				$map["createid"]=array("in",$_SESSION['user_dep_all_child']);
			}else if($_SESSION[strtolower($qx_name.'_index')]==3){//判断部门权限
				$map["createid"]=array("in",$_SESSION['user_dep_all_self']);
			}else if($_SESSION[strtolower($qx_name.'_index')]==4){//判断个人权限
				$map["createid"]=$_SESSION[C('USER_AUTH_KEY')];
			}
		}
		$id = $_REQUEST [$model->getPk ()];
		$map['id']=$id;
		if ($_SESSION["a"] != 1) $map['status']=1;
		$vo = $model->where($map)->find();
		if(empty($vo)){
			$this->display ("Public:404");
			exit;
		}
		// 上一条数据ID
		$map['id'] = array("lt",$id);
		$updataid = $model->where($map)->order('id desc')->getField('id');
		$this->assign("updataid",$updataid);
		// 下一条数据ID
		$map['id'] = array("gt",$id);
		$downdataid = $model->where($map)->getField('id');
		$this->assign("downdataid",$downdataid);
		// 动态配置
		$scdmodel = D('SystemConfigDetail');
		$modelname = $this->getActionName();
		$detailList = $scdmodel->getDetail($modelname,false);
		if ($detailList) {
			$fieldsarr = array();
			$sclmodel = D('SystemConfigList');
			foreach ($detailList as $k => $v) {
				$showname = '';
				if($v['status'] != -1){
					$showMethods = "";
					if($v['methods']){
						$methods = explode(';', $v['methods']);// 分解所有方法
						$normArray = $sclmodel->getNormArray();// 中文解析
						$showMethods .= "<span class='xyminitooltip'><span class='xyminitooltip_con'>";
						//$showname .= "<span class='xyminitooltip'><span class='xyminitooltip_con'>";
						$isfalse = false;
						foreach ($methods as $key => $vol) {
							if ($isfalse) {
								//$showname .= " | ";
								$showMethods .= " | ";
							}
							$volarr = explode(',', $vol);// 分解target和方法
							$target = $volarr[0];// 弹出方式
							$method = $volarr[1];// 方法名称
							$modelarr = explode('/', $method);// 分解方法0：model；1：方法
							if ($_SESSION[strtolower($modelarr[0].'_'.$modelarr[1])] || $_SESSION["a"]) {
								$showMethods .= "<a rel='".$modelarr[0].$modelarr[1]."' target='".$target."' href='__APP__/".$method."' mask='true'>".$normArray[$modelarr[1]]."</a>";
								$isfalse = true;
							}
							//$showname .= "<a rel='".$modelarr[0].$modelarr[1]."' target='".$target."' href='__APP__/".$method."' mask='true'>".$normArray[$modelarr[1]]."</a>";
							$isfalse = true;
						}
						if($showMethods){
							$showMethods .= "<span class='xyminitooltip_arrow_outer'></span><span class='xyminitooltip_arrow'></span></span></span>";
						}
					}
					if($showMethods){
						$showname .= $showMethods;
					}
					if ($v['models']) {
						if ($_SESSION[strtolower($v['models'].'_index')] || $_SESSION["a"]) {
							$showname .= "<a rel='".$v['models']."' target='navTab' href='__APP__/".$v['models']."/index'>".$v['showname']."</a>";
						} else {
							$showname .= $v['showname'];
						}
					} else{
						$showname .= $v['showname'];
					}
				}
				$fieldsarr[$v['name']] = $showname;
			}
			$this->assign ( 'fields', $fieldsarr );
			$this->assign ( 'detailList', $detailList );
		}
		$subdetailList = $scdmodel->getSubDetail($modelname);
		if ($subdetailList) {
			$this->assign ( 'subdetailList', $subdetailList );
		}
		//sublink扩展连接
		$sublinkextension = $scdmodel->getSubDetail($modelname,true,'link');
		if ($sublinkextension) {
			$this->assign ( 'sublinkextension', $sublinkextension );
		}
		//扩展工具栏操作
		$toolbarextension = $scdmodel->getSubDetail($modelname,true,'toolbar');
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		$this->assign('edit','edit');
		switch ($vo['auditState']) {
			case -1:
				$this->assign("auditImgClass","xyNotAudit");
				break;
			case 1:
				$this->assign("auditImgClass","xyForAudit");
				break;
			case 2:
				$this->assign("auditImgClass","xyNowAudit");
				break;
			case 3:
				$this->assign("auditImgClass","xyHasAudit");
				break;
		}
		//lookup带参数查询
		$module=A($name);
		if (method_exists($module,"_after_edit")) {
			call_user_func(array(&$module,"_after_edit"),&$vo);
		}
		$this->common();
		$this->assign( 'vo', $vo );
		if ($vo['auditState']>=1) {
			$this->display ('auditView');
		} else {
			$this->display ();
		}
	}
	/**
	 * @Title: _before_auditEdit
	 * @Description: todo(打开审核页面)
	 * @author
	 * @date 2013-5-31 下午4:14:13
	 * @throws
	 */
	public function auditEdit(){
		$id = $_GET['id'];//获取到对应需要修改的单据ID
		//实例化对应数据表模型
		$name = $this->getActionName();
		$model = D('MisHrPersonnelLeaveView');
		$map['id'] = $id;
			//赋值数据表模型
		$vo	=$model->where($map)->find();
		$map['status'] =1;
		$map['_string'] = 'FIND_IN_SET(  '.$_SESSION[C('USER_AUTH_KEY')].',curAuditUser )';
		// 上一条数据ID
		$map['id'] = array("lt",$id);
		$updataid = $model->where($map)->order('id desc')->getField('id');
		$this->assign("updataid",$updataid);
		// 下一条数据ID
		$map['id'] = array("gt",$id);
		$downdataid = $model->where($map)->getField('id');
		$this->assign("downdataid",$downdataid);
			//给模板赋值
		$scdmodel = D('SystemConfigDetail');
		$modelname = $this->getActionName();
		$detailList = $scdmodel->getDetail($modelname,false);
		if ($detailList) {
			$fieldsarr = array();
			$fieldsmodels = array();
			foreach ($detailList as $k => $v) {
				if($v['status'] != -1){
					$fieldsarr[$v['name']] = $v['showname'];
					$fieldsmodels[$v['name']] = $v['models'];
				}
			}
			$this->assign ( 'fields', $fieldsarr );
			$this->assign ( 'fieldsmodels', $fieldsmodels );
		}
		$subdetailList = $scdmodel->getSubDetail($modelname);
		if ($subdetailList) {
			$this->assign ( 'subdetailList', $subdetailList );
		}
		//扩展工具栏操作
		$toolbarextension = $scdmodel->getSubDetail($modelname,true,'toolbar');
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		//控制待审核和审核中的查看附件
		$typemodelview = D('SystemTypeView');
		$typelist=$typemodelview->GetTypelistValue();
		foreach ($typelist as $key=>$val){
			if($val['modelname'] == $name){
				$this->assign("type", base64_encode($val['type']));
			}
		}
		$this->assign("orderid", base64_encode($id));
		$module=A($name);
		if (method_exists($module,"_after_edit")) {
			call_user_func(array(&$module,"_after_edit"),&$vo);
		}
		$this->common();
		$this->assign('vo',$vo);
		$this->display();
	}
	// 查看审核明细界面
	public function auditView(){
		$id = $_GET['id'];//获取到对应需要修改的单据ID
		//实例化对应数据表模型
		$name = $this->getActionName();
		$model = D('MisHrPersonnelLeaveView');
		$map['id'] = $id;
		//赋值数据表模型
		$vo	= $model->where($map)->find();
		$map['status'] = array('eq',1);
		$map['_string'] = 'FIND_IN_SET(  '.$_SESSION[C('USER_AUTH_KEY')].', alreadyAuditUser )';
		// 上一条数据ID
		$map['id'] = array("lt",$id);
		$updataid = $model->where($map)->order('id desc')->getField('id');
		$this->assign("updataid",$updataid);
		// 下一条数据ID
		$map['id'] = array("gt",$id);
		$downdataid = $model->where($map)->getField('id');
		$this->assign("downdataid",$downdataid);
		//给模板赋值
		$scdmodel = D('SystemConfigDetail');
		$modelname = $this->getActionName();
		$detailList = $scdmodel->getDetail($modelname,false);
		if ($detailList) {
			$fieldsarr = array();
			$fieldsmodels = array();
			foreach ($detailList as $k => $v) {
				if($v['status'] != -1){
					$fieldsarr[$v['name']] = $v['showname'];
					$fieldsmodels[$v['name']] = $v['models'];
				}
			}
			$this->assign ( 'fields', $fieldsarr );
			$this->assign ( 'fieldsmodels', $fieldsmodels );
		}
		$subdetailList = $scdmodel->getSubDetail($modelname);
		if ($subdetailList) {
			$this->assign ( 'subdetailList', $subdetailList );
		}
		//扩展工具栏操作
		$toolbarextension = $scdmodel->getSubDetail($modelname,true,'toolbar');
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		//控制待审核和审核中的查看附件
		$typemodelview = D('SystemTypeView');
		$typelist=$typemodelview->GetTypelistValue();
		foreach ($typelist as $key=>$val){
			if($val['modelname'] == $name){
				$this->assign("type", base64_encode($val['type']));
			}
		}
		$this->assign("orderid", base64_encode($id));
	
		$module=A($name);
		if (method_exists($module,"_after_edit")) {
			call_user_func(array(&$module,"_after_edit"),&$vo);
		}
	
		$this->assign('vo',$vo);
		$this->assign('edit','auditView');
		switch ($vo['auditState']) {
			case -1:
				$this->assign("auditImgClass","xyNotAudit");
				break;
			case 1:
				$this->assign("auditImgClass","xyForAudit");
				break;
			case 2:
				$this->assign("auditImgClass","xyNowAudit");
				break;
			case 3:
				$this->assign("auditImgClass","xyHasAudit");
				break;
		}
		$this->common();
		$this->display();
	}
	
	/**
	 * @Title: _before_edit
	 * @Description: todo(打开修改页面前置函数)
	 * @author
	 * @date 2013-5-31 下午4:11:26
	 * @throws
	 */
	
	
	
	public function waitAudit(){
		$name = $this->getActionName();
		$map = $this->_search ($name);
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		if($_REQUEST['time'] != ''){
			$time = $this->timeToString($_REQUEST['time']);
			$map['updatetime'] = array($time[1],$time[0]);
			$this->assign('time',$_REQUEST['time']);
		}
		$map['status'] =1;
		$map['_string'] = 'FIND_IN_SET(  '.$_SESSION[C('USER_AUTH_KEY')].',curAuditUser )';
		$this->assign('audit','0');
	
		//验证浏览及权限
		// 		$qx_name=$name;
		// 		if(substr($name, -4)=="View"){
		// 			$qx_name = substr($name,0, -4);
		// 		}
		// 		if( !isset($_SESSION['a']) ){
		// 			if( $_SESSION[strtolower($qx_name.'_'.ACTION_NAME)]==2 ){////判断部门及子部门权限
		// 				$map["createid"]=array("in",$_SESSION['user_dep_all_child']);
		// 			}else if($_SESSION[strtolower($qx_name.'_'.ACTION_NAME)]==3){//判断部门权限
		// 				$map["createid"]=array("in",$_SESSION['user_dep_all_self']);
		// 			}else if($_SESSION[strtolower($qx_name.'_'.ACTION_NAME)]==4){//判断个人权限
		// 				$map["createid"]=$_SESSION[C('USER_AUTH_KEY')];
		// 			}
		// 		}
	
		$this->_list('MisHrPersonnelLeaveView',$map,'',true);
		$this->assign('jumpUrl','waitAudit');
		$_SESSION['navTab_data']=1;
		$this->assign('zhi',1);
		if($_REQUEST['defid']){
			Cookie::delete($name.'defaultSelect');
			Cookie::set($name.'defaultSelect', $_REQUEST['defid']);
		}
		if(!$_POST['indextype']){
			$this->display('auditIndex');
		}
	}
	/**
	 * @Title: overProcess
	 * @Description: todo(审核完毕执行函数)
	 * @param 当前审核单据ID $id
	 * @author 杨东
	 * @date 2013-5-31 下午4:17:37
	 * @throws
	 */
	function overProcess( $id ){
		
		$MisHrLeaveEmployeeModel=D('MisHrLeaveEmployee');
		$MisHrLeaveEmployeeList=$MisHrLeaveEmployeeModel->where("id=".$id)->find();
		//审核完成后。修改人力资源员工基本资料状态为离职
		$MisHrBasicEmployeeModel=D('MisHrBasicEmployee');
		$employeeDate['workstatus']=0;
		$employeeDate['working']=0;
		$employeeDate['leavedate']=time(); //离职日期
		$BasicResult=$MisHrBasicEmployeeModel->where(" id=".$MisHrLeaveEmployeeList['employeeid'])->save($employeeDate);
		//第二，修改后台用户状态 。
		$UserModel=D("User");
		unset($data);
		$map['employeid'] = $MisHrLeaveEmployeeList['employeeid'];
		$map['status'] = 1;
		$data['status'] = 0;
		$result=$UserModel->where($map)->save($data);
		if(!$list && !$result && !$BasicResult){
			$this->error ( L('_ERROR_') );
		}
	}
	/**
	 *
	 * @Title: lookupmanage
	 * @Description: todo(用ztree形式查询出所有部门员工信息)
	 * @author liminggang
	 * @throws
	 */
	public function lookupmanage(){
		$model= M('mis_system_department');
		$deptlist = $model->where("status=1")->order("id asc")->select();
		$param['rel']="positiveBox";
		$param['url']="__URL__/lookupmanage/jump/1/deptid/#id#/parentid/#parentid#";
		$param['open']=true;
		$typeTree = $this->getTree($deptlist,$param);
		$this->assign('tree',$typeTree);
		$map = array();
		$searchby = str_replace("-", ".", $_POST["searchby"]);
		$keyword=$this->escapeChar($_POST["keyword"]);
		$searchtype = $_POST['searchtype'];
		if($_POST["keyword"]){
			$map[$searchby] = ($searchtype==2)  ? array('like','%'.$keyword.'%'):$keyword;
			$this->assign('keyword',$keyword);
			$searchby = str_replace(".", "-", $_POST["searchby"]);
			$this->assign('searchby',$searchby);
			$this->assign('searchtype',$searchtype);
		}
		$searchby=array(
				array("id" =>"mis_hr_personnel_person_info-name","val"=>"按员工姓名"),
				array("id" =>"orderno","val"=>"按员工编号"),
		);
		$searchtype=array(array("id" =>"2","val"=>"模糊查找"),
				array("id" =>"1","val"=>"精确查找"));
		$this->assign("searchbylist",$searchby);
		$this->assign("searchtypelist",$searchtype);
		$map['working']=1; //在职员工
		$map['mis_hr_personnel_person_info.status'] = 1; //正常
		$deptid		= $_REQUEST['deptid'];
		//$parentid	= $_REQUEST['parentid'];
		if ($deptid >1) {
			//$deptlist =array_unique(array_filter (explode(",",$this->findAlldept($deptlist,$deptid))));
			$map['mis_hr_personnel_person_info.deptid'] =$deptlist;
		}
		//dirct by　yuansl 2014 06  11执行过滤
		$currModelNmae = $this->getActionName();
		$mode_x = D($currModelNmae);
		$colidList = $mode_x->where("status > 0")->getField('employeeid',true);
		if($colidList){
			$map['mis_hr_personnel_person_info.id'] = array('not in',$colidList);
		}
		$common=A("Common");
		$common->_list('MisHrBasicEmployeeView',$map);
		$this->assign('deptid',$deptid);
		$this->assign('parentid',$parentid);
		if ($_REQUEST['jump']) {
			$this->display('lookupmanagelist');
		} else {
			$this->display("lookupmanage");
		}
	}
	// 获取已审核数据
	public function alreadyAudit(){
		$name = $this->getActionName();
		$map = $this->_search ();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$map['status'] = array('eq',1);
		$map['_string'] = 'FIND_IN_SET(  '.$_SESSION[C('USER_AUTH_KEY')].', alreadyAuditUser )';
		$this->assign('audit','1');
		if($_REQUEST['time'] != ''){
			$time = $this->timeToString($_REQUEST['time']);
			$model = D('ProcessInfoHistory');
			$pihmap['dotime'] = array($time[1],$time[0]);
			$pihmap['tablename'] = $name;
			//$pihmap['ostatus'] = array('gt',1);
			$tibid = $model->where($pihmap)->getField('tableid',true);
			$tibid = array_unique($tibid);
			$map['id'] = array('in',$tibid);
			$this->assign('time',$_REQUEST['time']);
		}
		//验证浏览及权限
		// 		$qx_name=$name;
		// 		if(substr($name, -4)=="View"){
		// 			$qx_name = substr($name,0, -4);
		// 		}
		// 		if( !isset($_SESSION['a']) ){
		// 			if( $_SESSION[strtolower($qx_name.'_'.ACTION_NAME)]==2 ){////判断部门及子部门权限
		// 				$map["createid"]=array("in",$_SESSION['user_dep_all_child']);
		// 			}else if($_SESSION[strtolower($qx_name.'_'.ACTION_NAME)]==3){//判断部门权限
		// 				$map["createid"]=array("in",$_SESSION['user_dep_all_self']);
		// 			}else if($_SESSION[strtolower($qx_name.'_'.ACTION_NAME)]==4){//判断个人权限
		// 				$map["createid"]=$_SESSION[C('USER_AUTH_KEY')];
		// 			}
		// 		}
	
		$this->_list('MisHrPersonnelLeaveView',$map);
		$this->assign('jumpUrl','alreadyAudit');
		if($_REQUEST['defid']){
			Cookie::delete($name.'defaultSelect');
			Cookie::set($name.'defaultSelect', $_REQUEST['defid']);
		}
		if(!$_POST['indextype']){
			$this->display('auditIndex');
		}
		$this->common();
	}
	public function addleave(){
		//人事独立类别
		$model=D("Typeinfo");
		//查询离职类别
		$forLeaveList=$model->where("status=1 and pid=23 and typeid=2")->getField("id,name");
		$this->assign("forLeaveList",$forLeaveList);
		//订单编号是否可写
		$scnmodel = D('SystemConfigNumber');
		$writable= $scnmodel->GetWritable('mis_hr_personnel_person_info');
		$etverno = $scnmodel->GetRulesNO('mis_hr_personnel_person_info');
		$this->assign("orderno", $etverno);
		$this->assign("writable",$writable);
		$this->assign("time",time());
		$this->display();
	}
}