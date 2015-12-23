<?php
/**
 * @Title: file_name
 * @Package package_name
 * @Description: todo(首页待办任务提醒----员工待转正提醒)
 * @author yuansl
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-3-20 下午2:14:45
 * @version V1.0
 */
class MisHrRemindBecomeEmployeeAction extends MisHrPersonnelVoViewAction{
	public function index(){
		$dptmodel = D("MisSystemDepartment");//部门表
		$deptlist = $dptmodel->where("status=1")->order("id asc")->field('id,name,parentid')->select();
		$param['rel']="regularwaitingfor";
		$param['url']="__URL__/index/jump/1/deptid/#id#/parentid/#parentid#";
		$param['open']=true;
		$typeTree = $this->getTree($deptlist,$param);
		$this->assign('tree',$typeTree);
		$map=$this->_search();
		$map['workstatus']=2; //员工状态为试用
		if($_REQUEST['deptid']){
			$map['deptid'] = array('eq',$_REQUEST['deptid']);
		}
		$time=time() + 2592000;
		$map['status']=1;
		$map['transferdate']=array('elt',$time);
		//得到部门编号
		$deptid=$this->escapeChar($_REQUEST['deptid']);
		$parentid=$_REQUEST['parentid'];
		$this->assign("deptid",$deptid);
		//$this->assign("parentid",$parentid);
		if($deptid>1){
			//$deptlist =array_unique(array_filter (explode(",",$this->findAlldept($deptlist,$deptid))));
			$map['deptid'] = $deptid;
		}
		//动态配置显示字段
		$name="MisHrBasicEmployee";
		if (! empty ( $name )) {
			$qx_name=$name;
			if(substr($name, -4)=="View"){
				$qx_name = substr($name,0, -4);
			}
			//验证浏览及权限
			if( !isset($_SESSION['a']) ){
				$map=D('User')->getAccessfilter($qx_name,$map);	
			}
			$this->_list ($name, $map );
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
	//代办转正申请单
	public function addBecome(){
		$volistid=explode(',', $_GET['id']);
		unset($volistid[0]);
		$MisHrBasicEmployeeModel=D('MisHrBasicEmployee');
		$map['id'] = array(' in ',$volistid);
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
	//AJAX 请求获得试用员工信息
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
		if($MisHrBasicEmployeeList['mobilephone']){
			$voInfo['mobilephone']=$MisHrBasicEmployeeList['mobilephone'];
		}else{
			$voInfo['mobilephone']="";
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
			$voInfo['dutyid']=getFieldBy($MisHrBasicEmployeeList['dutylevelid'],'id','name','duty');
		}else{
			$voInfo['dutyid']="";
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
	public function _before_add(){
		//查询岗位类别
		$MisHrTypeinfoModel=D('MisHrTypeinfo');
		//查询工种
		$worktypeList=$MisHrTypeinfoModel->where(" status=1 and pid=63")->getField('id,name');
		$this->assign("worktypeList",$worktypeList);
		//查询民族
		$nationList=$MisHrTypeinfoModel->where(" status=1 and pid=96")->getField('id,name');
		$this->assign("nationList",$nationList);
		if($_REQUEST["id"]){ //普通人员招聘信息点击办理入职
			$MisHrInvitereFormModel=D("MisHrInvitereForm");
			$MisHrInvitereFormList=$MisHrInvitereFormModel->where(" status=1 and  id=".$_REQUEST["id"])->find();
			$this->assign("MisHrInvitereFormList",$MisHrInvitereFormList);
		}
		if($_REQUEST['InvitereSpecialId']){ //特殊人员招聘信息点击办理入职
			$MisHrInvitereSpecialFormModel=D("MisHrInvitereSpecialForm");
			$MisHrInvitereSpecialFormList=$MisHrInvitereSpecialFormModel->where(" status=1 and  id=".$_REQUEST["InvitereSpecialId"])->find();
			$this->assign("MisHrInvitereSpecialFormList",$MisHrInvitereSpecialFormList);
		}
		//订单号可写
		$scnmodel = D('SystemConfigNumber');
		$writable= $scnmodel->GetWritable('mis_hr_personnel_person_info');
		$this->assign("writable",$writable);
		//自动生成订单编号
		$orderno = $scnmodel->GetRulesNO('mis_hr_personnel_person_info');
		$this->assign("orderno",$orderno);
		//查询职位
		$DutyModel=D('Duty');
		$DutyList=$DutyModel->where(" status=1")->getField('id,name');
		$this->assign("DutyList",$DutyList);
		//查询部门
		$MisSystemDepartmentModel=D('MisSystemDepartment');
		$deptList = $MisSystemDepartmentModel->where(" status=1")->select();
		$deptList = $this->selectTree($deptList);
		$this->assign("deptidlist",$deptList);
		//所属公司信息
		$MisSystemCompanyDAO=M('mis_system_company');
		$companylist=$MisSystemCompanyDAO->where('status = 1')->field('id,name')->select();
		$this->assign('companylist',$companylist);
		//当前时间
		$now=time();
		$this->assign("now",$now);
		//查询学历
		$typeinfoModel=D('Typeinfo');
		$typeinfoList=$typeinfoModel->where(" status=1  and  pid=44")->getField("id,name");
		$this->assign("typeinfoList",$typeinfoList);
	}
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
		$map['workstatus']=2;    //试用
		$deptid		= $_REQUEST['deptid'];
		if ($deptid >1) {
			$map['mis_hr_personnel_person_info.deptid'] = $deptid;
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
			default:
				$this->display('edit');
				break;
		}
	}
}
