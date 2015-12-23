<?php
/**
 * @Title: MisHrPersonnelLeaveInfoAction 
 * @Package package_name 
 * @Description: todo(人事管理之请假模块) 
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2012-12-25 上午10:15:42 
 * @version V1.0
 */
class MisHrPersonnelLeaveInfoAction extends CommonAuditAction{
	/** @Title: _filter
	 * @Description: (构造检索条件) 
	 * @author  
	 * @date 2013-5-31 下午3:59:44 
	 * @throws 
	*/
	public function _filter(&$map) {
		if ($_SESSION["a"] != 1){
			$map['status'] = 1;
		}
	}
	/**
	 * @Title: _before_add
	 * @Description: todo(打开页面前置函数)
	 * @author liminggang
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
		$MisHrBasicEmployeeModel=D('MisHrBasicEmployee');
		$dutyModel=D('Duty');
		$MisSystemDepartmentModel=D('MisSystemDepartment');
		//查询当前登录者信息
		$uid=$_SESSION[C('USER_AUTH_KEY')];
		$userList=$userModel->where(" status=1 and id=".$uid)->find();
		$MisHrBasicEmployeeList=$MisHrBasicEmployeeModel->where(" status=1  and id=".$userList['employeid'])->find();
		//当前登陆者部门
		$depList=$MisSystemDepartmentModel->where(" status=1 and  id=".$MisHrBasicEmployeeList['deptid'])->find();
		$this->assign("MisHrBasicEmployeeList",$MisHrBasicEmployeeList);
		$this->assign("depList",$depList);
		//查看当前登陆者职级
		$dutyList=$dutyModel->where(" status=1 and id=".$MisHrBasicEmployeeList['dutylevelid'])->find();
		$this->assign("dutyList",$dutyList);
		
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
	 *
	 * @Title: lookupmanage
	 * @Description: todo(用ztree形式查询出所有部门员工信息)
	 * @author liminggang
	 * @throws
	 */
	public function lookupmanage(){
		$model= M('mis_system_department');
		$deptlist = $model->where("status=1")->order("id asc")->select();
		$param['rel']="leaveinfoBox";
		$param['url']="__URL__/lookupmanage/jump/1/deptid/#id#/parentid/#parentid#";
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
		$map['working'] = 1; //在职
		$map['mis_hr_personnel_person_info.status'] = 1; //正常
		// 		$map['positivestatus']=1; //转正
		$deptid		= $_REQUEST['deptid'];
		$parentid	= $_REQUEST['parentid'];
		if ($deptid && $parentid) {
			$deptlist =array_unique(array_filter (explode(",",$this->findAlldept($deptlist,$deptid))));
			$map['mis_hr_personnel_person_info.deptid'] = array(' in ',$deptlist);
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
	/**
	 * @Title: lookupgethours 
	 * @Description: todo(动态计算请假时间)   
	 * @author liminggang 
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