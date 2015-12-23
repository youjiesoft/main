<?php
/**
 * @Title: file_name
 * @Package package_name
 * @Description: todo(员工培训管理 )
 * @author liminggang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2012-11-29 下午5:05:42
 * @version V1.0
 */
class MisHrPersonnelTrainingManageAction extends CommonAction{
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
		/* $searchtype=array(
				array("id" =>"2","val"=>"模糊查找"),
				array("id" =>"1","val"=>"精确查找")
		);
		$this->assign('searchtypelist',$searchtype);
		if($_POST['keyword']){
			$keyword=$_POST['keyword'];
			$searchby=$_POST['searchby'];
			$searchtype=$_POST['searchtype'];
			$map[$searchby]=$searchtype==2 ? array('like','%'.$keyword.'%'):$keyword;
			$this->assign('searchby',$searchby);
			$this->assign('searchtype',$searchtype);
			$this->assign('keyword',$keyword);
		} */
	}
	/**
	 * @Title: _before_add
	 * @Description: todo(打开页面前置函数)
	 * @author 
	 * @throws
	 */
	public function _before_add(){
		//订单编号是否可写
		$scnmodel = D('SystemConfigNumber');
		$writable= $scnmodel->GetWritable('mis_hr_personnel_training_manage');
		$this->assign("writable",$writable);
		$etverno = $scnmodel->GetRulesNO('mis_hr_personnel_training_manage');
		$this->assign("orderno", $etverno);
		
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
	}
	/**
	 * @Title: _before_edit
	 * @Description: todo(打开修改页面前置函数)   
	 * @author  
	 * @date 2013-5-31 下午4:11:26 
	 * @throws 
	*/ 
	public function _before_edit(){
		//订单编号是否可写
		$scnmodel = D('SystemConfigNumber');
		$writable= $scnmodel->GetWritable('mis_hr_personnel_training_manage');
		$this->assign("writable",$writable);
		
		$id=$_REQUEST['id'];
		//获取人事ID
		$MisHrPersonnelTrainingManageDAO=M('mis_hr_personnel_training_manage');
		$list=$MisHrPersonnelTrainingManageDAO->where('id = '.$id)->find();
		//查询部门信息
		$MisHrBasicEmployeeDAO=M('mis_hr_personnel_person_info');
		$perlist=$MisHrBasicEmployeeDAO->where('id = '.$list['personid'])->field('name,deptid,indate,dutyname')->find();
		//人事选课和课程表中间关联表
		$MisHrPersonnelTrainManageRelationDAO=M("mis_hr_personnel_train_manage_relation");
		$trainarr=$MisHrPersonnelTrainManageRelationDAO->where('manageid = '.$id.' and status = 1')->field('id,trainid,classhour')->select();
		$MisHrEvaluationTrainDAO=M('mis_hr_evaluation_train');
		$arr=array();
		$classHours=0;
		foreach($trainarr as $key=>$val){
			//计算出当前人的总课时
			$classHours=$classHours+$val['classhour'];
			$map['id']= $val['trainid'];
			$trainlist=$MisHrEvaluationTrainDAO->where($map)->find();
			$trainlist['relation'] = $val['id'];
			$arr[]=$trainlist;
		}
		$this->assign('trainlist',$arr);
		$this->assign('perlist',$perlist);
		$this->assign('vo',$list);
		$this->assign('classHours',$classHours);
	}
	/**
	 * @Title: _before_insert 
	 * @Description: todo(插入前置函数)   
	 * @author  
	 * @date 2013-5-31 下午4:08:58 
	 * @throws 
	*/ 
	public function _before_insert(){
		$this->checkifexistcodeororder('mis_hr_personnel_training_manage','orderno');
	}
	/**
	 * @Title: _after_insert
	 * @Description: todo(插入之后操作。)
	 * @author liminggang
	 * @date 2013-4-11 下午5:08:52
	 * @throws
	 */
	public function _after_insert($id){
		//获得选中的课程ID值
		$trainarr=array_unique($_POST['trainid']);
		//关联表
		$MisHrPersonnelTrainManageRelationModel=D("MisHrPersonnelTrainManageRelation");
		//插入成功的员工选课ID
		$_POST['manageid'] = $id;
		foreach($trainarr as $key=>$val){
			$_POST['trainid'] = $val;
			$_POST['classhour'] =$_POST['classhours'][$key];
			if (false === $MisHrPersonnelTrainManageRelationModel->create ()) {
				$this->error ( $MisHrPersonnelTrainManageRelationModel->getError () );
			}
			$list=$MisHrPersonnelTrainManageRelationModel->add ();
			if ($list===false) {
				$this->error ( L('_ERROR_') );
			}
		}
	}
	/**
	 * @Title: delTrain 
	 * @Description: todo(移除培训课程)   
	 * @author liminggang 
	 * @date 2013-4-12 下午4:06:07 
	 * @throws
	 */
	public function	delTrain(){
		$MisHrPersonnelTrainManageRelationModel=D("MisHrPersonnelTrainManageRelation");
		//获取员工培训ID。
		$id=$_GET['id'];
		$classhour=$_GET['classhour'];  //课时。
		
		$map['id'] = $_GET['relationid'];
		$map['status'] = 0;
		$map['updateid'] = $_SESSION[C('USER_AUTH_KEY')];
		$map['updatetime'] = time();
		// 更新数据
		$list=$MisHrPersonnelTrainManageRelationModel->data($map)->save ();
		$name=$this->getActionName();
		$model = D ($name);
		unset($map);
		$map['classhour'] = array("exp","classhour-".$classhour);
		$map['id'] =$id;
		$realust=$model->data($map)->save();
		if ($list && $realust) {
			$this->success ("删除成功");
		} else {
			$this->error ("删除失败");
		}
	}
	/**
	 * @Title: _before_update
	 * @Description: todo(修改保存前置函数)   
	 * @author  
	 * @date 2013-5-31 下午4:12:50 
	 * @throws 
	*/ 
	public function _before_update(){
		$this->checkifexistcodeororder('mis_hr_personnel_training_manage','orderno',1);
		$itemsorg_id=array_unique($_POST['itemsorg_id']);
		$manageid=$_POST['id'];  //获取选课编号
		unset($_POST['id']);  //清空post中的id值。
		$_POST['manageid'] = $manageid;
		$MisHrPersonnelTrainManageRelationModel=D("MisHrPersonnelTrainManageRelation");
		foreach($itemsorg_id as $k=>$v){
			$_POST['trainid'] = $v;
			$_POST['classhour'] = getFieldBy($v, 'id', 'classhour', 'mis_hr_evaluation_train');
			if (false === $MisHrPersonnelTrainManageRelationModel->create ()) {
				$this->error ( $MisHrPersonnelTrainManageRelationModel->getError () );
			}
			//保存当前数据对象
			$list=$MisHrPersonnelTrainManageRelationModel->add ();
			if(!$list){
				$this->error ( L('_ERROR_') );
			}
		}
		//将ID值赋值给post；
		$_POST['id']=$manageid;
		
	}
	/**
	 * @Title: lookuptrain
	 * @Description: todo(查询课程培训信息)   
	 * @author  
	 * @date 2013-5-31 下午4:12:50 
	 * @throws 
	*/ 
	public function lookuptrain(){
		//得到选课编号ID
		$id=$_GET['id'];
		$MisHrPersonnelTrainManageRelationDAO=M('mis_hr_personnel_train_manage_relation');
		$list=$MisHrPersonnelTrainManageRelationDAO->where('manageid = '.$id.' and status = 1')->field('trainid')->select();
		$arr=array();
		foreach($list as $key=>$val){
			$arr[]=$val['trainid'];
		}
		$searchby=array(
				array("id" =>"orderno","val"=>"按课程编号"),
				array("id" =>"name","val"=>"按课程名称"),
				array("id" =>"lecturer","val"=>"按培训讲师")
		);
		$searchtype=array(
				array("id" =>"2","val"=>"模糊查找"),
				array("id" =>"1","val"=>"精确查找")
		);
		$this->assign("searchtypelist",$searchtype);
		$this->assign("searchbylist",$searchby);
		//检索部分
		if(!empty($_POST['keyword'])){
			$searchtypes=	$_POST['searchtype'];
			$searchbys	= $_POST['searchby'];
			$keywords	=	$_POST['keyword'];
			$map[$searchbys]=$searchtypes==2 ? array('like',"%".$keywords."%"):$keywords;
			//保留状态
			$this->assign('keyword',$keywords);
			$this->assign('searchby',$searchbys);
			$this->assign('searchtype',$searchtypes);
		}
		$map['status']=1;
		if($arr){
			$map['id'] = array('not in',$arr);
		}
		$this->_list('MisHrEvaluationTrain',$map);
		$this->display("lookuptrain");
	}
	/**
	 *
	 * @Title: lookupmanage
	 * @Description: todo(用ztree形式查询出所有部门员工信息。)
	 * @author liminggang
	 * @throws
	 */
	public function lookupmanage(){
		$model= M('mis_system_department');
		$deptlist = $model->where("status=1")->order("id asc")->select();
		$param['rel']="positiveBox";
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
		$deptid		= $_REQUEST['deptid'];
		$parentid	= $_REQUEST['parentid'];
		if ($deptid && $parentid) {
			$deptlist =array_unique(array_filter (explode(",",$this->findAlldept($deptlist,$deptid))));
			$map['mis_hr_personnel_person_info.deptid'] = array(' in ',$deptlist);
		}
		$this->_list('MisHrBasicEmployeeView',$map);
		$this->assign('deptid',$deptid);
		$this->assign('parentid',$parentid);
		if ($_REQUEST['jump']) {
			$this->display('lookupmanagelist');
		} else {
			$this->display("lookupmanage");
		}
	}
}
?>