<?php
/**
 * @Title: MisHrAgentBecomeEmployeeAction 
 * @Package package_name
 * @Description: todo(人事代办离职管理) 
 * @author renling 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-12-11 下午6:25:02 
 * @version V1.0
 */
class MisHrAgentBecomeEmployeeAction extends CommonAuditAction{
	/** @Title: _filter
	 * @Description: (构造检索条件)
	 * @author
	 * @date 2013-5-31 下午3:59:44
	 * @throws
	 */
	public function _filter(&$map) {
		if ($_SESSION["a"] != 1){
			$map['status'] = 1;
			$map['regulartype']=2; //代办转正数据
		}
	}
	/**
	 * @Title: _before_add
	 * @Description: todo(打开页面前置函数)
	 * @author
	 * @throws
	 */
	public function _before_add(){
		$this->assign("time",time());

		$userModel=D('User');// 用户表
		$MisHrBasicEmployeeModel=D('MisHrBasicEmployee');//员工表
		$dutyModel=D('Duty');// 职级表
		$MisSystemDepartmentModel=D('MisSystemDepartment');//部门表
		//查询当前登录者信息
		$uid = $_SESSION[C('USER_AUTH_KEY')];
		$userVO = $userModel->where(" status=1 and id=".$uid)->find();
		$basicEmployeeVO = $MisHrBasicEmployeeModel->where(" status=1  and id=".$userVO['employeid'])->find();
		//当前登陆者部门
		$depVO = $MisSystemDepartmentModel->where(" status=1 and  id=".$basicEmployeeVO['deptid'])->find();
		$this->assign("basicEmployeeVO",$basicEmployeeVO);
		$this->assign("depVO",$depVO);
		//查看当前登陆者职级
		$dutyVO = $dutyModel->where(" status=1 and id=".$basicEmployeeVO['dutylevelid'])->find();
		$this->assign("dutyVO",$dutyVO);
		//计算试用天数
		$pedate = $basicEmployeeVO['transferdate'] - $basicEmployeeVO['indate'];
		if($pedate){
			$pedate = round($pedate/3600/24);
			$this->assign("pedate",$pedate);
		}
		//计算已经试用天数
		if($basicEmployeeVO['indate']){
			$date = time()-$basicEmployeeVO['indate'];
			$date = round($date/3600/24);
			$this->assign("date",$date);
		}
	}
	/**
	 * @Title: _before_auditProcess
	 * @Description: todo(审核流程前置函数)
	 * @author
	 * @date 2013-5-31 下午4:16:45
	 * @throws
	 */
	public function _before_auditProcess(){
		$this->checkifexistcodeororder('mis_hr_regular_employee','orderno',1);
	}

	/**
	 * @Title: _before_edit
	 * @Description: todo(打开修改页面前置函数)
	 * @author
	 * @date 2013-5-31 下午4:11:26
	 * @throws
	 */
	public function _before_edit(){
		$scnmodel = D('SystemConfigNumber');
		$writable= $scnmodel->GetWritable('mis_hr_regular_employee');
		$this->assign("writable",$writable);
	}
	public function _after_edit(&$vo){
		$MisHrBasicEmployeeModel = D('MisHrBasicEmployee');//员工表
		//查询当前员工信息
		$basicEmployeeVO = $MisHrBasicEmployeeModel->where(" status=1  and id=".$vo['employeeid'])->find();
		$this->assign("basicEmployeeVO",$basicEmployeeVO);
		//计算试用天数
		$pedate = $basicEmployeeVO['transferdate'] - $basicEmployeeVO['indate'];
		$pedate = round($pedate/3600/24);
		$this->assign("pedate",$pedate);
			//计算已经试用天数
			$date = time()-$basicEmployeeVO['indate'];
			$date = round($date/3600/24);
		$vo['date'] = $date;
		$MisHrRegularEmployeeModel=D('MisHrRegularEmployee');
		$MisHrRegularEmployeeList=$MisHrRegularEmployeeModel->where(" status=1 and id=".$_GET['id'])->find();
		if($MisHrRegularEmployeeList['regulartype']=="2"){
			$volistid=$MisHrRegularEmployeeList['employeeid'];
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
		//查询此员工是否已转正
		$MisHrBasicEmployeeModel=D('MisHrBasicEmployee');
		$MisHrBasicEmployeeList=$MisHrBasicEmployeeModel->where(" id=".$_POST['employeeid'])->find();
		if($MisHrBasicEmployeeList['workstatus']==1){
			$this->error("操作失败,此员工已是正式员工！");
		}
		$this->checkifexistcodeororder('mis_hr_regular_employee','orderno');
	}
	/**
	 * @Title: _before_update
	 * @Description: todo(修改保存前置函数)
	 * @author
	 * @date 2013-5-31 下午4:12:50
	 * @throws
	 */
	public function _before_update(){
		if($_POST['regulartype']==2){
			$_POST['employeeid']=implode(',', $_POST['employeeid']);
		}
		$this->checkifexistcodeororder("mis_hr_regular_employee",'orderno',1);
	}
	public function _before_startprocess(){
		if($_POST['regulartype']==2){
			$_POST['employeeid']=implode(',', $_POST['employeeid']);
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
		//审核完成后。修改员工基本资料
		$MisHrBecomeEmployeeModel=D('MisHrBecomeEmployee');
		$personmodel=D("MisHrBasicEmployee");
		$MisHrBecomeEmployeeList=$MisHrBecomeEmployeeModel->where("  id=".$id)->find();
		if($MisHrBecomeEmployeeList['regulartype']== 2){ //批量代办
			$employeeid=$_POST['employeeid'];
			$sdata['transferdate'] = strtotime($_POST['positivedate']);
			$sdata['workstatus']=1;   //转正
			foreach ($employeeid as $key=>$val){
				$sdata['id'] = $val;
				$personmodel->data($sdata);
				$list=$personmodel->save();
				if(!$list){
					$this->error ( L('_ERROR_') );
				}
			}
		}else{
			$data['id'] = $_POST['employeeid'];
			$data['transferdate'] = strtotime($_POST['positivedate']);
			$data['workstatus']=1;   //转正
			$list=$personmodel->save($data);
			$personmodel->commit();
			if(!$list){
				$this->error ( L('_ERROR_') );
			}
		}
	}
	/**
	 * @Title: lookupmanage
	 * @Description: todo(lookupmanage)
	 * @param 当前审核单据ID $id
	 * @author renling
	 * @date 2013-5-31 下午4:17:37
	 * @throws
	 */
public function lookupmanage(){
	$this->assign("step",$_REQUEST['step']);
		$this->assign("step",$_GET['step']);
		$hrsearchlist=$_POST['lookuphrsearchlist'];
		$this->assign("lookuphrsearchlist",$hrsearchlist);
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
		if ($deptid ) {
			$map['mis_hr_personnel_person_info.deptid'] = $deptid;
		}
		$_REQUEST ['orderField']='id';
		$common=A('common');
		$common->_list('MisHrBasicEmployee',$map);
		$this->assign('deptid',$deptid);
		if ($_REQUEST['jump']) {
			$this->display('lookupmanagelist');
		} else {
			$this->display("lookupmanage");
		}
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
	 * @Title: lookupregulardate 
	 * @Description: todo(通过入职日期转正日期算出试用期)   
	 * @author renling 
	 * @date 2013-12-7 下午3:09:32 
	 * @throws
	 */
	public function lookupregulardate(){
		//计算试用天数
			$pedate = strtotime($_POST['edate']) - strtotime($_POST['sdate']);
			$pedate = round($pedate/3600/24);
			$date = time()-strtotime($_POST['sdate']);//计算已经试用天数
			$date = round($date/3600/24);
			$regulardate['pedate']=$pedate;
			$regulardate['indate']=$date;
		echo json_encode($regulardate);
	}
}
?>