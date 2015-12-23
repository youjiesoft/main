<?php
/**
 * @Title: MisHrPersonnelTrainInfoAction
 * @Package package_name
 * @Description: todo(员工调职信息)
 * @author 杨东
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-7-22 上午10:11:03
 * @version V1.0
 */
class MisHrPersonnelTrainInfoAction extends CommonAuditAction{
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
	 * @Title: add
	 * @Description: (打开添加页面函数)
	 * @author
	 * @date 2013-5-31 下午3:59:44
	 * @throws
	 */
	public function _before_add(){
		//调用公用方法
		$this->common();
		//自动生成异动编号
		$scnmodel = D('SystemConfigNumber');
		$ptferno = $scnmodel->GetRulesNO('mis_hr_personnel_train_info');
		$this->assign("orderno", $ptferno);
		$this->assign("time",time());
		//查询当前登录者信息
		$uid = $_SESSION[C('USER_AUTH_KEY')];
		$userModel = D('User');
		$userList = $userModel->where(" status=1 and id=".$uid)->find();
		//人事表
		$MisHrBasicEmployeeModel = D("MisHrBasicEmployee");
		$MisHrBasicEmployeeVO = $MisHrBasicEmployeeModel->where(" status=1  and id=".$userList['employeid'])->find();
		$this->assign("MisHrBasicEmployeeVO",$MisHrBasicEmployeeVO);
		//当前登陆者部门
		$pcmodel = D("MisSystemDepartment");//部门 表
		$deptVO = $pcmodel->where(" status=1 and id=".$MisHrBasicEmployeeVO['deptid'])->find();
		$this->assign("deptVO",$deptVO);
		//查看当前登陆者职级
		$dutyModel = D('Duty');
		$dutyVO = $dutyModel->where(" status=1 and id=".$MisHrBasicEmployeeVO['dutylevelid'])->find();
		$this->assign("dutyVO",$dutyVO);

	}
	public function _before_insert(){
	 if($_POST['istranstype']){
	 	if($_POST['trantype']==1){
	 		$_POST['trantype']=3;
	 	}else{
	 		$_POST['trantype']=4;
	 	}
	 }
	 //查询此人是否已提交调职申请单
	 $MisHrPersonnelTrainInfoModel=D('MisHrPersonnelTrainInfo');
	 $MisHrPersonnelTrainInfoResult=$MisHrPersonnelTrainInfoModel->where("status=1 and personid=".$_POST['personid']."  and auditState<>3")->find();
	 if($MisHrPersonnelTrainInfoResult){
	 	$this->error("请不要重复提交调职申请单！");
	 }
	}
	public function _before_update(){
		if($_POST['oldtrantype']==3||$_POST['oldtrantype']==4 ){
			if($_POST['trantype']==1){
				$_POST['trantype']=3;
			}else{
				$_POST['trantype']=4;
			}
		}
		//查询此人是否已提交调职申请单
		$MisHrPersonnelTrainInfoModel=D('MisHrPersonnelTrainInfo');
		$MisHrPersonnelTrainInfoResult=$MisHrPersonnelTrainInfoModel->where("status=1 and personid=".$_POST['personid']."  and auditState<>3")->count();
		if($MisHrPersonnelTrainInfoResult>1){
			$this->error("请不要重复提交调职申请单！");
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
		//调用公用方法
		$this->common();
	}
	/**
	 * @Title: _before_startprocess
	 * @Description: todo(启动流程前置函数)
	 * @author
	 * @date 2013-5-31 下午4:13:33
	 * @throws
	 */
	public function _before_startprocess() {
		$this->checkifexistcodeororder('mis_hr_personnel_train_info','orderno',1);
	}
	/**
	 * @Title: _before_auditProcess
	 * @Description: todo(审核流程前置函数)
	 * @author
	 * @date 2013-5-31 下午4:16:45
	 * @throws
	 */
	public function _before_auditProcess(){
		$this->checkifexistcodeororder('mis_hr_personnel_train_info','orderno',1);
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
		//根据审核传过来的ID值，查询当前数据信息
		$name=$this->getActionName();
		$model=D($name);
		$translist=$model->where("id=".$id)->find();
		//审核完成后。修改员工基本资料
		$personmodel=D("mis_hr_personnel_person_info");
		$data['deptid']=$translist['newdeptid'];//更新部门id
		$data['dutylevelid']=$translist['newdutylevelid'];//更新职级ID
		$data['worktype']=$translist['newworktype'];//更新职务
		$data['id']=$translist['personid'];//更新条件
		$list=$personmodel->save ($data);
		if(!$list){
			$this->error ( "审核过后修改人事基本信息失败，请联系管理员" );
		}
		//根据岗位获取到角色ID
		$MisHrJobInfoDao = M("mis_hr_job_info");
		//获取新的授权组
		$newrolegroupid=$MisHrJobInfoDao->where('id = '.$translist['newworktype'])->getField('rolegroup_id');
		//修改部门，职级，岗位
		$this->syncEmployeeUser($translist['personid'], $translist['newdeptid'], $translist['newdutylevelid'], $newrolegroupid);
	}
	
	/**
	 * @Title: common
	 * @Description: todo(公共调用函数)
	 * @author 杨东
	 * @date 2013-5-31 下午4:07:47
	 * @throws
	 */
	public function common($deptid){
		$pcmozhiye = D("Duty");//职位表
		$pcarrzhiwei = $pcmozhiye->where("status = 1")->getField("id,name");//查询职位
		$this->assign ( 'zhiwei', $pcarrzhiwei );
		//查询岗位类别
		//$MisHrTypeinfoModel=D('MisHrTypeinfo');
		//查询工种
		//$worktypeList=$MisHrTypeinfoModel->where(" status=1 and pid=63")->getField('id,name');
		//$this->assign("worktypeList",$worktypeList);
		
		$pcmodel = D("MisSystemDepartment");//部门 表
		$deptlist=$pcmodel->where("status = 1")->select();//查询部门
		$depthtml=$this->selectTree($deptlist,0,0,$deptid);
		$this->assign ( 'depthtml', $depthtml );
		//查询岗位类别
		//$MisHrTypeinfoModel=D('MisHrTypeinfo');
		//查询工种
		//$worktypeList=$MisHrTypeinfoModel->where(" status=1 and pid=63")->getField('id,name');
		//$this->assign("worktypeList",$worktypeList);
		//查询岗位异动类型
		$model=D("Typeinfo");
		$list=$model->where("typeid=2 and pid=11")->select();
		$this->assign ('tranlist', $list );
		//异动编号是否可写
		$scnmodel = D('SystemConfigNumber');
		$writable= $scnmodel->GetWritable('mis_hr_personnel_train_info');
		$this->assign("writable",$writable);

	}
	/**
	 * @Title: _after_edit
	 * @Description: todo(打开修改页面后置函数)
	 * @author
	 * @date 2013-5-31 下午4:11:26
	 * @throws
	 */
	public function _after_edit($vo){
		//调用公用方法
		$this->common($vo['newdeptid']);
		//人事表
		$MisHrBasicEmployeeModel = D("MisHrBasicEmployee");
		$MisHrBasicEmployeeVO = $MisHrBasicEmployeeModel->where(" status=1  and id=".$vo['personid'])->find();
		$this->assign("MisHrBasicEmployeeVO",$MisHrBasicEmployeeVO);
		
		//查询岗位信息
		$MisHrBojInfoDao = M("mis_hr_job_info");
		$where = array();
		$where['deptid'] = $vo['newdeptid'];
		$where['status'] = 1;
		$worktypeList=$MisHrBojInfoDao->where($where)->getField('id,name');
		$this->assign("worktypeList",$worktypeList);
	}
	/**
	 *
	 * @Title: lookupmanage
	 * @Description: todo(用ztree形式查询出所有部门员工信息)
	 * @author liminggang
	 * @throws
	 */
	public function lookupmanage(){
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
		$map['working']=1;
		$deptid		= $_REQUEST['deptid'];
		if ($deptid >1) {
			$map['mis_hr_personnel_person_info.deptid'] = $deptid;
		}
		$common=A("Common");
		//dirct by　yuansl 2014 06  11执行过滤
		$currModelNmae = $this->getActionName();
		$mode_x = D($currModelNmae);
		$colidList = $mode_x->where("status > 0 and auditState != 3")->getField('personid',true);
		if($colidList){
		$map['mis_hr_personnel_person_info.id'] = array('not in',$colidList);
		}
		$common->_list('MisHrBasicEmployee',$map);
		$this->assign('deptid',$deptid);
		if ($_REQUEST['jump']) {
			$this->display('lookupmanagelist');
		} else {
			$this->display("lookupmanage");
		}
	}
	public function _before_addtransfer(){
		//调用公用方法
		$this->common();
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

	}
	/**
	 * @Title: comboxrefreshIntomas
	 * @Description: todo(获取岗位信息)
	 * @author liminggang
	 * @date 2013-5-31 下午5:28:54
	 * @throws
	 */
	public function comboxrefreshIntomas(){
		//赋值数据表模型
		$MisHrjJobInfomodel=M("mis_hr_job_info");
		//获得部门ID
		$deptid = $_POST['deptid'];
		$where = array();
		$where['status'] = 1;
		$where['deptid'] = $deptid;
		$joblist=$MisHrjJobInfomodel->where($where)->getField("id,name");
		//给模板赋值
		$arr = array();
		if($joblist){
			foreach($joblist as $k=>$v){
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