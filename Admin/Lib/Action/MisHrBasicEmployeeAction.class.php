<?php
/**
 * @Title: MisHrProbationEmployeeAction
 * @Package package_name
 * @Description: todo(试用员工)
 * @author renling
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-7-10 上午11:17:59
 * @version V1.0
 */
class MisHrBasicEmployeeAction extends MisHrPersonnelVoViewAction{
	public function index(){
		//$model = D("MisHrBasicEmployee");
		//$arr = array();
		//$list2 = $model->where("orderno = '' ")->field("id")->select();
		//foreach ($list2 as $key => $val) {
		//	$model = D("MisHrBasicEmployee");
		//	$num = "HR20131107";
		//	$num .= sprintf("%05d", $val['id']);
		//	$arr = array('id'=>$val['id'],'orderno'=>$num);
		//	$model->save($arr);
		//	$model->commit();
		//}
		//部门树 数据
		$dptmodel = D("MisSystemDepartment");//部门表
	 	$typeTree=$dptmodel->getDeptZtree('','true','mishrprobationemployee');
	 	$MisSystemCompanyModel=D("MisSystemCompany");
	 	$this->assign('tree',$typeTree);
	 	//默认选中节点（高亮）
	 	$MisSystemCompanyid=$MisSystemCompanyModel->getCompanyOne();
	 	$this->assign("MisSystemCompanyid",$MisSystemCompanyid);
	 	//所选择的节点公司信息（具体公司的部门列表）
	 	$deptmap=array();
		if($_REQUEST['companyid']){
			$deptmap=array();
			$deptmap['companyid']=$_REQUEST['companyid'];
		}else{
			$checkcompanyid = cookie::get("checkcompanyid");// 新增时默认选中 cookie
			cookie::delete("checkcompanyid"); 
			$deptmap['companyid']=$checkcompanyid?$checkcompanyid:$MisSystemCompanyid;
		}
	 	$deptmap["status"]=1;
	 	$deptlist=$dptmodel->where($deptmap)->select();
	 	
	 	
		$map=$this->_search();
		$map['workstatus']=2; //员工状态为试用
		$map['companyid']=$deptmap['companyid'];
		$map['user_dept_duty.status']=1;
		//得到部门编号
		$deptid=$_REQUEST['deptid']?$_REQUEST['deptid']:$checkcompanyid;
		//部门父id名字是dpId 将公司部门id赋予模板
		$ptId=$_REQUEST['ptId'];
		$this->assign('ptId',$ptId);
		$this->assign('companyid',$deptmap['companyid']);
		$this->assign("deptid",$deptid);
		//动态配置显示字段
		$name=$this->getActionName();
		if (! empty ( $name )) {
			$qx_name=$name;
			if(substr($name, -4)=="View"){
				$qx_name = substr($name,0, -4);
			}
		//验证浏览及权限
			if( !isset($_SESSION['a']) ){
				if( $_SESSION[strtolower($qx_name.'_'.ACTION_NAME)]!=1 ){
					if( $_SESSION[strtolower($qx_name.'_'.ACTION_NAME)]==2 ){////判断部门及子部门权限
						//查询该部门及子部门
							$deptlist =array_unique(array_filter (explode(",",$this->findAlldept($deptlist,getFieldBy(getFieldBy($_SESSION[C('USER_AUTH_KEY')], 'id', 'employeid', 'user'),'id','deptid','mis_hr_personnel_person_info')))));
							$map['deptid'] = array(' in ',$deptlist);
							if($deptid) {
								 //查询该部门下的人员
								 $MisHrBasicEmployeeModel=D('MisHrBasicEmployee');
								 $MisHrBasicEmployeeList= $MisHrBasicEmployeeModel->where("status=1 and workstatus=2 and deptid=".$deptid)->getField("id,name");
								 $map['mis_hr_personnel_person_info.id']=array("in",array_keys($MisHrBasicEmployeeList));
							}
					}else if($_SESSION[strtolower($qx_name.'_'.ACTION_NAME)]==3){//判断部门权限
						//查询该部门人员
						$map['mis_hr_personnel_person_info.deptid']=getFieldBy(getFieldBy($_SESSION[C('USER_AUTH_KEY')], 'id', 'employeid', 'user'),'id','deptid','mis_hr_personnel_person_info');
// 						$map["createid"]=array("in",$_SESSION['user_dep_all_self']);
					}else if($_SESSION[strtolower($qx_name.'_'.ACTION_NAME)]==4){//判断个人权限
						$map["createid"]=$_SESSION[C('USER_AUTH_KEY')];
					}
				}else{
					if($deptid ){
						$deptlist =array_unique(array_filter (explode(",",$this->findAlldept($deptlist,$deptid))));
						$map['deptid'] = array(' in ',$deptlist);
					}
				}
			}else{
				if($deptid ){
					$deptlist =array_unique(array_filter (explode(",",$this->findAlldept($deptlist,$deptid))));
					$map['deptid'] = array(' in ',$deptlist);
				}
			}

			//检索员工状态时 过滤掉
			if($map['mis_hr_personnel_person_info.employeeStatus']){
				unset($map['mis_hr_personnel_person_info.employeeStatus']);
			}
			$this->_list ("MisHrPersonnelUserDeptView", $map );
		}
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
		
		$toolbarextension = $scdmodel->getDetail("MisHrBasicEmployee",true,'toolbar');
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		if( intval($_POST['dwzloadhtml']) ){
			$this->display("dwzloadindex");exit;
		}
		if ($_REQUEST['jump']) {
			$this->display('depindex');
		} else {
			$this->display();
		}
	}
	// 	public function _after_list($list){    记录选择项
	// 		if ($_POST['hrsearchlist']) {
	// 			$this->assign("hrsearchlist",$_POST['hrsearchlist']);
	// 			$selecetid = explode('_', $_POST['hrsearchlist']);
	// 			$map1 = array();
	// 			$map1['id'] = array('in', $selecetid);
	// 			$model = D("MisHrBasicEmployee");
	// 			$selectlist = $model->where($map1)->select();
	// 			foreach ($selectlist as $key => $val) {
	// 				$selectlist[$key]['isselected'] = 1;
	// 			}
	// 			$list = array_merge($list, $selectlist);
	// 		}
	// 		if ($_POST['lookuphrsearchlist']) {
	// 			$this->assign("lookuphrsearchlist",$_POST['lookuphrsearchlist']);
	// 			$selecetid = explode('_', $_POST['lookuphrsearchlist']);
	// 			$map1 = array();
	// 			$map1['id'] = array('in', $selecetid);
	// 			$model = D("MisHrBasicEmployee");
	// 			$selectlist = $model->where($map1)->select();
	// 			foreach ($selectlist as $key => $val) {
	// 				$selectlist[$key]['isselected'] = 1;
		// 			}
		// 			$list = array_merge($list, $selectlist);
		// 		}

		// 	}
		public function addleave(){
			$MisHrBasicEmployeeModel=D('MisHrBasicEmployee');
			$volistid=explode(',', $_GET['id']);
			foreach ($volistid as $key => $val) {
				$volist = $MisHrBasicEmployeeModel->where("status=1 and id=".$val)->find();
				//计算试用天数
				$pedate = $volist['transferdate'] - $volist['indate'];
				$pedate = round($date/3600/24);
				$volistid[$key]['pedate']=$pedate; //试用期
				$date = time()-$volist['indate'];//计算已经试用天数
				$date = round($date/3600/24);
				$volistid[$key]['date']=$date;
				$volistid[$key]['dutyname']=$volist['dutyname'];
				$volistid[$key]['dutylevelid']=$volist['dutylevelid'];
				$volistid[$key]['indate']=$volist['indate'];
			}
			$this->assign("volistid",$volistid);
			$id=$_GET['id'];
			if($volistid[1]){
					
				$this->assign('volist',$volist);
			}
			//查询岗位类别
			$MisHrTypeinfoModel=D('Typeinfo');

			//查询工种
			//$worktypeList=$MisHrTypeinfoModel->where(" status=1 and pid=63")->getField('id,name');
			//$this->assign("worktypeList",$worktypeList);

			//订单号可写
			$scnmodel = D('SystemConfigNumber');
			$writable= $scnmodel->GetWritable('mis_hr_leave_employee');
			$this->assign("writable",$writable);
			//查询离职类别
			$forLeaveList=$MisHrTypeinfoModel->where("status=1 and pid=23 and typeid=2")->getField("id,name");
			$this->assign("forLeaveList",$forLeaveList);
			//自动生成订单编号
			$orderno = $scnmodel->GetRulesNO('mis_hr_leave_employee');
			$this->assign("orderno",$orderno);
			$orderno1 = $scnmodel->GetRulesNO('mis_hr_basic_employee');
			$this->assign("horderno",$orderno1);
			echo "===".$orderno1;
			//查询职位
			$DutyModel=D('Duty');
			$DutyList=$DutyModel->where(" status=1")->getField('id,name');
			$this->assign("DutyList",$DutyList);
			//查询部门
			$MisSystemDepartmentModel=D('MisSystemDepartment');
			$DeptList=$MisSystemDepartmentModel->where(" status=1")->getField('id,name');
			$this->assign("DeptList",$DeptList);
			//代办时间
			$time=time();
			$this->assign("now",$time);
			$this->display();
		}
		/**
		 * @Title: addforleave
		 * @Description: todo(代办请假)
		 * @author renling
		 * @date 2013-8-15 下午3:35:49
		 * @throws
		 */
		public function addforleave(){
			$id=$_GET['id'];
			if($id){
				$model = D ('MisHrBasicEmployee');
				$volist = $model->where("status=1 and  id=".$id)->find();
				$this->assign('MisHrBasicEmployeeList',$volist);
			}
			//查询岗位类别
			//$MisHrTypeinfoModel=D('MisHrTypeinfo');
			//查询工种
			//$worktypeList=$MisHrTypeinfoModel->where(" status=1 and pid=63")->getField('id,name');
			//$this->assign("worktypeList",$worktypeList);
			//查询请假类别
			$model=D("Typeinfo");
			$typelist=$model->where("status=1 and pid=1 and typeid=2")->getField("id,name");
			$this->assign("typelist",$typelist);
			//请假编号是否可写
			$scnmodel = D('SystemConfigNumber');
			$writable= $scnmodel->GetWritable('mis_hr_personnel_leave_info');
			$this->assign("writable",$writable);
			//查询职位
			$DutyModel=D('Duty');
			$DutyList=$DutyModel->where(" status=1")->getField('id,name');
			$this->assign("DutyList",$DutyList);
			//查询部门
			$MisSystemDepartmentModel=D('MisSystemDepartment');
			$DeptList=$MisSystemDepartmentModel->where(" status=1")->getField('id,name');
			$this->assign("DeptList",$DeptList);
			//订单号可写
			$scnmodel = D('SystemConfigNumber');
			$elaveno = $scnmodel->GetRulesNO('mis_hr_personnel_leave_info');
			$this->assign("orderno", $elaveno);
			$this->assign('time',time());
			$this->display();
		}
		/**
		 * @Title: addtransfer
		 * @Description: todo(代办调职)
		 * @author renling
		 * @date 2013-8-15 下午4:29:23
		 * @throws
		 */
		public function  addtransfer(){
			$id=$_GET['id'];
			$volist = array();
			if($id){
				$model = D ('MisHrBasicEmployee');
				$volist = $model->where("status=1 and  id=".$id)->find();
				$this->assign('MisHrBasicEmployeeVO',$volist);
			}
			$pcmozhiye = D("Duty");//职位表
			$pcarrzhiwei = $pcmozhiye->where("status = 1")->getField("id,name");//查询职位
			$this->assign ( 'zhiwei', $pcarrzhiwei );
			//查询岗位异动类型
			$model=D("Typeinfo");
			$list=$model->where("typeid=2 and pid=11")->select();
			$this->assign ('tranlist', $list );
			$pcmodel = D("MisSystemDepartment");//部门 表
			$deptlist=$pcmodel->where("status = 1")->select();//查询部门
			$depthtml=$this->selectTree($deptlist,0,0,$deptid);
			$this->assign ( 'depthtml', $depthtml );
			//自动生成异动编号
			$scnmodel = D('SystemConfigNumber');
			$ptferno = $scnmodel->GetRulesNO('mis_hr_personnel_train_info');
			$this->assign("orderno", $ptferno);
			$this->assign("time",time());
			//查询部门
			$DeptList=$pcmodel->where(" status=1")->getField('id,name');
			$this->assign("DeptList",$DeptList);
			//查询岗位类别
			//$MisHrTypeinfoModel=D('MisHrTypeinfo');
			//查询工种
			//$worktypeList=$MisHrTypeinfoModel->where(" status=1 and pid=63")->getField('id,name');
			//$this->assign("worktypeList",$worktypeList);
			$this->display();
		}
		/**
		 * @Title: addBecome
		 * @Description: todo(代办转正申请单)
		 * @author renling
		 * @date 2013-7-31 上午11:27:52
		 * @throws
		 */
		public function addBecome(){
			$volistid=explode(',', $_GET['id']);
			unset($volistid[0]);
			//执行过滤
			$curModel = D("MisHrBecomeEmployee");
			$mapf['status'] = 1;
			$mapf['employeeid'] = array('in',$volistid);
			$repeatIdList = $curModel->where($mapf)->getField("employeeid",true);
			//系统自动过滤,并把名字提示给用户
			$repeatnames = "";
			if( count($repeatIdList) >0 ){
				foreach($repeatIdList as $reid){
					$repeatnames=$repeatnames.",".getFieldBy($reid, 'id', 'name', "mis_hr_personnel_person_info");
				}
			}
			$repeatnames = mb_substr($repeatnames, 1,mb_strlen($repeatnames), 'utf-8');
			if(strlen($repeatnames) > 0){
				$this->assign("repeatnames",$repeatnames);
			}
			//自动去除求差集
			$volistid = array_diff($volistid,$repeatIdList);
			$MisHrBasicEmployeeModel=D('MisHrBasicEmployee');
			if($volistid){
				 $map['id'] = array(' in ',$volistid);
			}else{
			  	$map['id'] =$mapf['employeeid'];
			}
			$map['status'] = 1;
			$list=$MisHrBasicEmployeeModel->where($map)->select();
			foreach($list as $key=>$val){
				//计算试用天数
				$pedate = $val['transferdate'] - $val['indate'];
				$pedate = round($pedate/3600/24);
				$list[$key]['pedate']=$pedate; //试用期
				$date = time()-$val['indate'];//计算已经试用天数
				$date = round($date/3600/24);
				$list[$key]['date']=$date;
			}
			$this->assign("volistid",$list);
			//订单号可写
			$scnmodel = D('SystemConfigNumber');
			$writable= $scnmodel->GetWritable('mis_hr_regular_employee');
			$this->assign("writable",$writable);
			$etverno = $scnmodel->GetRulesNO('mis_hr_regular_employee');
			$this->assign("orderno", $etverno);
			$this->assign("uid",$_SESSION[C('USER_AUTH_KEY')]);
			$this->assign("time",time());
			$this->display();
		}
		/**
		 * @Title: lookupemployeeInfo
		 * @Description: todo(AJAX 请求获得试用员工信息)
		 * @author renling
		 * @date 2013-7-31 下午4:23:25
		 * @throws
		 */
		public function  lookupemployeeInfo(){
			$id=$_POST['id'];
			$MisHrBasicEmployeeModel=D('MisHrBasicEmployee');
			$MisHrBasicEmployeeList=$MisHrBasicEmployeeModel->where(" status=1 and id=".$id)->find();
			//计算试用天数
			$pedate = $MisHrBasicEmployeeList['transferdate'] - $MisHrBasicEmployeeList['indate'];
			$pedate = round($pedate/3600/24);
			//计算已经试用天数
			$date = time()-$MisHrBasicEmployeeList['indate'];
			$date = round($date/3600/24);
			$this->assign("date",$date);
			//以下if判断为排除空null显示
			if($MisHrBasicEmployeeList['phone']){
				$voInfo['phone']=$MisHrBasicEmployeeList['phone'];
			}else{
				$voInfo['phone']="";
			}
			if(getFieldBy($MisHrBasicEmployeeList['deptid'],'id','name','mis_system_department')){
				$voInfo['deptname']=getFieldBy($MisHrBasicEmployeeList['deptid'],'id','name','mis_system_department');
			}else{
				$voInfo['deptname']="";
			}
			if($MisHrBasicEmployeeList['dutyname']){
				$voInfo['dutyname']=$MisHrBasicEmployeeList['dutyname'];
			}else{
				$voInfo['dutyname']="";
			}

			if(getFieldBy($MisHrBasicEmployeeList['dutylevelid'],'id','name','duty')){
				$voInfo['dutylevelid']=getFieldBy($MisHrBasicEmployeeList['dutylevelid'],'id','name','duty');
			}else{
				$voInfo['dutylevelid']="";
			}
			if(transTime($MisHrBasicEmployeeList['indate'])){
				$voInfo['indate']=transTime($MisHrBasicEmployeeList['indate']);
			}else{
				$voInfo['indate']="";
			}
			$voInfo['chinaid']=$MisHrBasicEmployeeList['chinaid'];
			$voInfo['name']=$MisHrBasicEmployeeList['name'];
			$voInfo['sex']=$MisHrBasicEmployeeList['sex'];
			$voInfo['age']=$MisHrBasicEmployeeList['age'];
			$voInfo['worktype']=$MisHrBasicEmployeeList['worktype'];
			$voInfo['pedate']=$pedate;
			$voInfo['date']=$date;
			echo  json_encode($voInfo);
		}
		/**
		 * @Title: setprobation
		 * @Description: todo(延长试用期页面加载)
		 * @author renling
		 * @date 2013-7-22 下午3:10:42
		 * @throws
		 */
		public function setprobation(){
			$id=$_GET['id'];
			$this->assign("id",$id);
			$this->display();
		}
		//延长试用期
		public function updateprobation(){
			$time=time();
			$MisHrBasicEmployeeModel=D('MisHrBasicEmployee');
			$transferdate=$_POST['transferdate'];
			if($transferdate=="week"){
				$transferdate=getFieldBy($_POST['id'], 'id', 'transferdate', 'mis_hr_personnel_person_info')+86400*7;
			}
			if($transferdate=="onemonth"){

				$transferdate=strtotime('+1 month',getFieldBy($_POST['id'], 'id', 'transferdate', 'mis_hr_personnel_person_info'));
			}
			if($transferdate=="twomonth"){
				$transferdate=strtotime('+2 month',getFieldBy($_POST['id'], 'id', 'transferdate', 'mis_hr_personnel_person_info'));
			}
			if($transferdate=="threemonth"){
				$transferdate=strtotime('+3 month',getFieldBy($_POST['id'], 'id', 'transferdate', 'mis_hr_personnel_person_info'));
			}
			if($transferdate=="other"){
				$day=$_POST['days'];
				$transferdate=strtotime('+  '.$day.' days',getFieldBy($_POST['id'], 'id', 'transferdate', 'mis_hr_personnel_person_info'));
			}
			if($_POST['id']>0){
				$data=array(
						'transferdate'=> $transferdate,
				);
				$MisHrBasicEmployeeModel->data($data);
				$list1=$MisHrBasicEmployeeModel->where('status = 1  and id='.$_POST['id'])->save($data);
				$MisHrBasicEmployeeModel->commit();
			}else{
				$data=array(
						'transferdate'=> $transferdate,
				);
				$MisHrBasicEmployeeModel->data($data);
				$list1=$MisHrBasicEmployeeModel->where('status = 1 ')->save($data);
				$MisHrBasicEmployeeModel->commit();
			}
			if(!$list1){
				$this->error ( L('_ERROR_') );
			}else{
				$this->success("操作成功！");
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
			if(!$_POST['companyid']){
				$this->error("数据提交不完整！");
			}
			$model = D ('MisHrBasicEmployee');
			//在职状态是否有该员工
			$isResult=$model->where(" chinaid='".$_POST['chinaid']."' and working=1")->find();
			if($isResult){
				$this->error ('已有该员工的入职信息,请勿重复录入！');
				return false;
			}
			// 根据试用期 算出转正日期
			$_POST['transferdate'] = strtotime('+'.$_POST['probationcycle'].' month', strtotime($_POST['indate']));
			$this->checkifexistcodeororder('mis_hr_personnel_person_info','orderno');
		}

		public function lookupinsertuser(){
			$id=$_POST['employeid'];
			$_POST['isaddUser'] =1;
			$_POST['account']=$_POST['accountenglish'];
			$id=$_POST['employeid'];
			$this->addUser($id,2);
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
			$param['isParent']=true;
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
			$map['workstatus']=2;    //试用
			$deptid		= $_REQUEST['deptid'];
			if($_REQUEST['type']==1){
				$MisHrLeaveEmployeeModel = D('MisHrLeaveEmployee');
				$colidList = $MisHrLeaveEmployeeModel->where("status > 0 and auditState != 3")->getField('employeeid',true);
			}
			if($_REQUEST['type']==2){
				$MisHrPersonnelTrainInfoModel = D('MisHrPersonnelTrainInfo');
				$colidList = $MisHrPersonnelTrainInfoModel->where("status > 0 and auditState != 3")->getField('personid',true);
			}
			if($_REQUEST['type']==3){
				//请假
				$MisHrEmployeeLeaveManagementModel = D('MisHrEmployeeLeaveManagement');
				$colidList = $MisHrEmployeeLeaveManagementModel->where("status =1 and auditState != 3")->getField('personid',true);
			}
			if($colidList){
				$map['mis_hr_personnel_person_info.id'] = array('not in',$colidList);
			}
			if ($deptid >1) {
				$map['mis_hr_personnel_person_info.deptid'] = $deptid;
			}
			//执行过滤
			if($_REQUEST['step'] == "addBecome"){
				$curModel = D("MisHrBecomeEmployee");
				$mapf['status'] = array('gt',0);
				$repeatIdList = $curModel->where($mapf)->getField("employeeid",true);
				$map['mis_hr_personnel_person_info.id'] = array('not in',$repeatIdList);
			}
			$this->_list('MisHrBasicEmployee',$map);
			$this->assign('deptid',$deptid);
			if ($_REQUEST['jump']) {
				$this->display('lookupmanagelist');
			} else {
				$this->display("lookupmanage");
			}
		}
		public function edit(){
			$id = $_REQUEST['id'];
			$this->assign('id',$id);
			switch ($_REQUEST['type']) {
				case "basis"://基础信息
					$this->getBasis();
					break;
				case "userdept"://组织结构
					$this->getuserdept();
					break;
				case "edu"://教育信息
					$this->getEdu();
					break;
				case "work"://工作经验
					$this->getWork();
					break;
				case "train"://培训记录
					$this->getTrain();
					break;
				case "contract"://合同记录
					$this->getEmployeeContract();
					break;
				case "person"://关系人
					$this->getPerson();
					break;
				case "issue"://装备发放记录
					$this->getIssue();
					break;
				case "application":
					$this->getapplication();
					break;
				case "dispatching":
					$this->getDispatching();
					break;
				default:
					$this->display('edit');
					break;
			}
		}
		/**
		 * @Title: lookupinspect
		 * @Description: todo(循环检查是否已经处于转正队列)
		 * @author yuansl
		 * @date 2014-6-11 下午3:15:12
		 * @throws
		 */
		public function lookupinspect(){
				
		}
	}