<?php
/**
 * @Title: MisHrLeaveEmployeeAction
 * @Package package_name
 * @Description: todo(离职员工)
 * @author renling
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-7-10 上午11:17:10
 * @version V1.0
 */
class MisHrPersonnelLeaveAction extends MisHrPersonnelVoViewAction{
	public function index(){
		$this->getLeaveTree();
		$map=$this->_search();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
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
								 $MisHrBasicEmployeeList= $MisHrBasicEmployeeModel->where("status=1 and workstatus=0 and deptid=".$deptid)->getField("id,name");
								 $map['mis_hr_personnel_person_info.id']=array("in",array_keys($MisHrBasicEmployeeList));
							}
					}else if($_SESSION[strtolower($qx_name.'_'.ACTION_NAME)]==3){//判断部门权限
						//查询该部门人员
						$map['mis_hr_personnel_person_info.deptid']=getFieldBy(getFieldBy($_SESSION[C('USER_AUTH_KEY')], 'id', 'employeid', 'user'),'id','deptid','mis_hr_personnel_person_info');
// 						$map["createid"]=array("in",$_SESSION['user_dep_all_self']);
					}else if($_SESSION[strtolower($qx_name.'_'.ACTION_NAME)]==4){//判断个人权限
						$map["createid"]=$_SESSION[C('USER_AUTH_KEY')];
					}
				}
			}else{
				if($deptid) {
					$map['deptid'] =$deptid;
				}
			}
			$map['mis_hr_personnel_person_info.workstatus']=0;//离职
			if($_GET['type']){
				unset($map['mis_hr_personnel_person_info.workstatus']);
				$type=$_GET['type'];
				switch ($type){
					case '2':
						$map['mis_hr_leave_employee.leavetype']=2; //试用自离
						$map['mis_hr_leave_employee.auditState']=3; //审核完毕
						break;
					case '3':
						$map['mis_hr_leave_employee.leavetype']=1; //正式自离
						$map['mis_hr_leave_employee.auditState']=3; //审核完毕
						break;
					case '4':
						$map['mis_hr_leave_employee.leavetype']=4; //试用代办
						$map['mis_hr_leave_employee.auditState']=3; //审核完毕
						break;
					case '5':
						$map['mis_hr_leave_employee.leavetype']=3; //正式代办
						$map['mis_hr_leave_employee.auditState']=3; //审核完毕
						break;
					case '6':
						$map['mis_hr_leave_employee.auditState']=array("lt",3); //正在办理
						break;
					case '7':
						$map['mis_hr_leave_employee.auditState']=array("lt",3); //正在办理
						$map['mis_hr_leave_employee.leavetype']=2; //试用自离
						break;
					case '8':
						$map['mis_hr_leave_employee.auditState']=array("lt",3); //正在办理
						$map['mis_hr_leave_employee.leavetype']=1; //正式自离
						break;
					case '9':
						$map['mis_hr_leave_employee.auditState']=array("lt",3); //正在办理
						$map['mis_hr_leave_employee.leavetype']=4; //试用代办自离
						break;
					case '10':
						$map['mis_hr_leave_employee.auditState']=array("lt",3); //正在办理
						$map['mis_hr_leave_employee.leavetype']=3; //正式代办自离
						break;
					default:
						$map['mis_hr_personnel_person_info.workstatus']=0;//离职
						break;
				}
			}
			$this->_list ('MisHrPersonnelLeaveView', $map );
		}
		$scdmodel = D('SystemConfigDetail');
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
		$toolbarextension = $scdmodel->getDetail($name,true,'toolbar');
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		if( intval($_POST['dwzloadhtml']) ){
			$this->display("dwzloadindex");exit;
		}
		if ($_REQUEST['type']) {
			$this->display('deptindex');
		} else {
			$this->display();
		}
	}
	public function getLeaveTree(){
		$tree = array(); // 树初始化
		$tree[] = array(
				'id' => 6,
				'pId' => 0,
				'name' => '已离职',
				'title' => '已离职',
				'rel' => "mishrleaveemployee",
				'target' => 'ajax',
				'icon' => "__PUBLIC__/Images/icon/user_left.png",
				'url' => "__URL__/index/type/1",
				'open' => true
		);
		$tree[] = array(
				'id' => 7,
				'pId' => 6,
				'name' => '试用自离',
				'title' => '试用自离',
				'rel' => "mishrleaveemployee",
				'target' => 'ajax',
				'icon' => "__PUBLIC__/Images/icon/user_left_1.png",
				'url' => "__URL__/index/type/2",
				'open' => true
		);
		$tree[] = array(
				'id' => 8,
				'pId' => 6,
				'name' => '正式自离',
				'title' => '正式自离',
				'rel' => "mishrleaveemployee",
				'target' => 'ajax',
				'icon' => "__PUBLIC__/Images/icon/user_left_2.png",
				'url' => "__URL__/index/type/3",
				'open' => true
		);
		$tree[] = array(
				'id' => 9,
				'pId' => 6,
				'name' => '试用代办离职',
				'title' => '试用代办离职',
				'rel' => "mishrleaveemployee",
				'target' => 'ajax',
				'icon' => "__PUBLIC__/Images/icon/user_left_3.png",
				'url' => "__URL__/index/type/4",
				'open' => true
		);
		$tree[] = array(
				'id' => 10,
				'pId' => 6,
				'name' => '正式代办离职',
				'title' => '正式代办离职',
				'rel' => "mishrleaveemployee",
				'target' => 'ajax',
				'icon' => "__PUBLIC__/Images/icon/user_left_4.png",
				'url' => "__URL__/index/type/5",
				'open' => true
		);
		$tree[] = array(
				'id' => 1,
				'pId' => 0,
				'name' => '办理中',
				'title' => '办理中',
				'rel' => "mishrleaveemployee",
				'target' => 'ajax',
				'icon' => "__PUBLIC__/Images/icon/user_leave.png",
				'url' => "__URL__/index/type/6",
				'open' => true
		);
		$tree[] = array(
				'id' => 2,
				'pId' => 1,
				'name' => '试用自离',
				'title' => '试用自离',
				'rel' => "mishrleaveemployee",
				'target' => 'ajax',
				'icon' => "__PUBLIC__/Images/icon/user_leave_1.png",
				'url' => "__URL__/index/type/7",
				'open' => true
		);
		$tree[] = array(
				'id' => 3,
				'pId' => 1,
				'name' => '正式自离',
				'title' => '正式自离',
				'rel' => "mishrleaveemployee",
				'target' => 'ajax',
				'icon' => "__PUBLIC__/Images/icon/user_leave_2.png",
				'url' => "__URL__/index/type/8",
				'open' => true
		);
		$tree[] = array(
				'id' => 4,
				'pId' => 1,
				'name' => '试用代办离职',
				'title' => '试用代办离职',
				'rel' => "mishrleaveemployee",
				'target' => 'ajax',
				'icon' => "__PUBLIC__/Images/icon/user_leave_3.png",
				'url' => "__URL__/index/type/9",
				'open' => true
		);
		$tree[] = array(
				'id' => 5,
				'pId' => 1,
				'name' => '正式代办离职',
				'title' => '正式代办离职',
				'rel' => "mishrleaveemployee",
				'target' => 'ajax',
				'icon' => "__PUBLIC__/Images/icon/user_leave_4.png",
				'url' => "__URL__/index/type/10",
				'open' => true
		);

		$this->assign("tree",json_encode($tree));
	}
	public function lookupviewresult(){
		$model=D('MisHrLeaveEmployee');
		$id=$_REQUEST['id'];
		$map['id']=$id;
		if ($_SESSION["a"] != 1) $map['status']=1;
		$volist = $model->where($map)->find();
		if(empty($volist)){
			$this->display ("Public:404");
			exit;
		}
		$this->assign('vo',$volist);
		$this->display();
	}

	/* (查看)
	 * @see CommonAction::edit()
	*/
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
			case "person"://关系人
				$this->getPerson();
				break;
			case "train"://培训记录
				$this->getTrain();
				break;
			case "transfer"://调动记录
				$this->getTransfer();
				break;
			case "hurt"://调动记录
				$this->getHurt();
				break;
			case "leaveemployee"://离职信息
				$this->getLeaveEmployee();
				break;
			case "employeeleave"://请假记录
				$this->getEmployeeLeave();
				break;
			case "assess"://绩效考评
				$this->getPerformanceAssess();
				break;
			case "contract"://合同记录
				$this->getEmployeeContract();
				break;
			case "issue"://装备发放记录
				$this->getIssue();
				break;
			case "application":
				$this->getapplication();
				break;
			default:
				$this->display('edit');
				break;
		}
	}
	public function _after_insert($list){
		$companyid=$_POST['companyid'];
		$UuserDeptDutyModel=D('UserDeptDuty');
		$companyList=array();
		foreach ($companyid as $key=>$val){
			if(in_array($val,array_keys($companyList))){
				$this->error(getFieldBy($val,"id","name","mis_system_company")."添加重复,请查证后提交！");
			}
			$UserDeptDuty=array();
			$UserDeptDuty['userid']=$this->adduserid;
			$UserDeptDuty['deptid']=$_POST['deptid'][$key];
			$UserDeptDuty['dutyid']=$_POST['dutylevelid'][$key];
			$UserDeptDuty['worktype']=$_POST['worktype'][$key];
			$UserDeptDuty['typeid']=1;
			$UserDeptDuty['companyid']=$_POST['companyid'][$key];
			$UserDeptDuty['createid']=$_SESSION[C('USER_AUTH_KEY')];
			$UserDeptDuty['createtime']=time();
			$UserDeptUserResult=$UuserDeptDutyModel->add($UserDeptDuty);
			$UuserDeptDutyModel->commit();
			$companyList[$val]=1;
			if(!$UserDeptUserResult){
				$this->error("数据异常");
			}
		}
		//插入 到 离职管理
		$hrLeaveEmployeeModel=D("MisHrLeaveEmployee");
		$data=$_POST;
		$scnmodel = D('SystemConfigNumber');
		$data['orderno'] = $scnmodel->GetRulesNO('mis_hr_leave_employee');
		//人事模型
		$MisHrBasicEmployeeModel=D('MisHrBasicEmployee');
		$MisHrBasicEmployeeVo=$MisHrBasicEmployeeModel->where("status=1 and id=".$list)->find();
		//查询职级ID level id
		$data['level']=getFieldBy($MisHrBasicEmployeeVo['dutylevelid'], 'id', 'level', 'duty');
		$data['employeeid']=$list;
		$data['forleavetype']=94;
		$data['leavetype']=1;
		$data['auditState']=3;
		$data['mobilephone']=$_POST['phone'];
		if (false === $hrLeaveEmployeeModel->create ($data)) {
			$this->error ( $hrLeaveEmployeeModel->getError () );
		}
		$re=$hrLeaveEmployeeModel->add ();
		if($re === false){
			$this->error("操作失败");
		}
	}
	public function _before_insert(){
		$model = D ('MisHrBasicEmployee');
		//在职状态是否有该员工
		$isResult=$model->where(" chinaid='".$_POST['chinaid']."' and working=0")->find();
		if($isResult){
			$this->error ('已有该员工的离职信息,请勿重复录入！');
			return false;
		}
	}
	/**
	 * @Title: updateGeneral
	 * @Description: todo(员工管理-修改综合信息方法)
	 * @return boolean
	 * @author renling
	 * @date 2013-10-25 下午2:10:15
	 * @throws
	 */
	public  function updateGeneral() {
		$model = D ('MisHrBasicEmployee');
		//在职状态是否有该员工
		$isResultList=$model->where(" chinaid='".$_POST['chinaid']."'")->select();
		$boolReasult=$this->idcard_checksum18($_POST['chinaid']);
		if($boolReasult==false){
			$this->error ('身份证格式有误,请输入正确的身份证号码！');
		}
		if($_POST['workstatus']=='2'){ //试用员工算出转正日期
			// 根据试用期 算出转正日期
			$_POST['transferdate'] = strtotime('+'.$_POST['probationcycle'].' month', strtotime($_POST['indate']));
		}
		if($_POST['transferdate']){
			$_POST['transferdate']=strtotime($_POST['transferdate']);
		}
		if($_POST['leavedate']){
			$_POST['leavedate']=strtotime($_POST['leavedate']);
		}
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$list=$model->save ();
		$model->commit();
		if(!$list){
			$this->error ('员工基本资料修改失败！');
		}
		$id=$_POST['id'];
		unset($_POST['id']);
		//获取人事岗位对应的角色
		$rolegroup_id=getFieldBy($_POST['worktype'], 'id', 'rolegroup_id', 'mis_hr_job_info');
		//修改后台用户职级
		$this->syncEmployeeUser($id,$_POST['deptid'],$_POST['dutylevelid'],$rolegroup_id);
		//人事关系模型
		$mfbsModel = D ("MisHrEmployeePrivy");
		//工作经验
		$MisHrPersonnelExperienceInfoModel=D('MisHrPersonnelExperienceInfo');
		//家庭成员
		$MisHrPersonnelFamilyInfoModel=D('MisHrEmployeePrivy');
		//教育经历
		$MisHrPersonnelEducationInfoModel=D('MisHrPersonnelEducationInfo');
		//人事关系标记
		$bPrivyFamily=true;
		//介绍人标记
		$bPrivyRemid=true;
		//教育经历标记
		$bEducation=true;
		//工作经验标记
		$bExperience=true;
		//获取到要插入数据
		if($_REQUEST['privyname']){ //介绍人
			foreach($_REQUEST["privyname"] as $key=>$value){
				$date=array();
				$date["employeeid"]=$id;
				$date["privytype"]=1;//介绍人
				if($_REQUEST['privyemid']){
					$date["privyemid"]=$_REQUEST["privyemid"][$key];//关联员工ID
				}
				$date["privyname"]=$_REQUEST["privyname"][$key];//姓名
				$date["relation"]=$_REQUEST["privyrelation"][$key];//关系
				$date["privytel"]=$_REQUEST["privytelephone"][$key];//联系电话
				if($_REQUEST["Privyid"][$key]){
					//修改信息
					$date['id']=$_REQUEST["Privyid"][$key];
					$list=$mfbsModel->data($date)->save();
				}else{
					//保存当前数据对象
					unset($date['id']);
					$list=$mfbsModel->data($date)->add();
				}
				if (!$list) {
					//介绍人标记
					$bPrivyRemid=false;
				}
			}
		}
		if($_REQUEST['startdate']){ //工作经验
			$Expdate=array();
			foreach($_REQUEST["startdate"] as $key=>$value){
				$Expdate["personinfoid"]=$id; //基础表ID
				$Expdate["company"]=$_REQUEST['company'][$key];//公司
				$Expdate["position"]=$_REQUEST["position"][$key];//职业
				$Expdate["remark"]=$_REQUEST["expremark"][$key];//备注
				$Expdate["startdate"]=strtotime($_REQUEST["startdate"][$key]);//备注
				$Expdate["finishdate"]=strtotime($_REQUEST["finishdate"][$key]);//备注
				if($_REQUEST["Experienceid"][$key]){
					//修改信息
					$Expdate['id']=$_REQUEST["Experienceid"][$key];
					$list=$MisHrPersonnelExperienceInfoModel->data($Expdate)->save();
				}else{
					unset($Expdate['id']);
					//保存当前数据对象
					$list=$MisHrPersonnelExperienceInfoModel->data($Expdate)->add();
				}
				$MisHrPersonnelExperienceInfoModel->commit();
				if (!$list) {
					//工作经验标记
					$bExperience=false;
				}
			}
		}
		if($_REQUEST['edustartdate']){ //教育经历
			$edudate=array();
			foreach($_REQUEST["edustartdate"] as $key=>$value){
				$edudate["personinfoid"]=$id; //基础表ID
				$edudate["startDate"]=strtotime($_REQUEST['edustartdate'][$key]);//开始时间
				$edudate["finishDate"]=strtotime($_REQUEST['edufinishdate'][$key]);//结束时间
				$edudate["school"]=$_REQUEST['newschool'][$key];//学校机构
				$edudate["skillAndCertificate"]=$_REQUEST["skillAndCertificate"][$key];//专业与技能
				if($_REQUEST["Educationid"][$key]){
					//修改信息
					$edudate['id']=$_REQUEST["Educationid"][$key];
					$list=$MisHrPersonnelEducationInfoModel->data($edudate)->save();
				}else{
					//保存当前数据对象
					unset($edudate['id']);
					$list=$MisHrPersonnelEducationInfoModel->data($edudate)->add();
				}
				if ($list==false) {
					//教育经历标记
					$bEducation=false;
				}
			}
		}
		if($_REQUEST['relation']){ //家庭成员
			$Familydata=array();
			foreach($_REQUEST["relation"] as $key=>$value){
				$Familydata["employeeid"]=$id; //基础表ID
				$Familydata["relation"]=$_REQUEST['relation'][$key];//关系
				$Familydata["privyname"]=$_REQUEST['familyname'][$key];//姓名
				$Familydata["privycompany"]=$_REQUEST['familycompany'][$key];//工作单位
				$Familydata["privytel"]=$_REQUEST['telephone'][$key];//联系电话
				$Familydata["skillAndCertificate"]=$_REQUEST["skillAndCertificate"][$key];//专业与技能
				if($_REQUEST["familyid"][$key]){
					//修改信息
					$Familydata['id']=$_REQUEST["familyid"][$key];
					$list=$MisHrPersonnelFamilyInfoModel->data($Familydata)->save();
				} else{
					//保存当前数据对象
					unset($Familydata['id']);
					$list=$MisHrPersonnelFamilyInfoModel->data($Familydata)->add ();
				}
				if ($list==false) {
					//人事关系标记
					$bPrivyFamily=false;
				}
			}
		}
		//人事离职 管理 修改
		$hrLeaveEmployeeModel=D("MisHrLeaveEmployee");
		//人事模型
		$MisHrBasicEmployeeModel=D('MisHrBasicEmployee');
		$MisHrBasicEmployeeVo=$MisHrBasicEmployeeModel->where("status=1 and id=".$id)->find();
		//查询职级ID level id
		$data=$_POST;
		$data['level']=getFieldBy($MisHrBasicEmployeeVo['dutylevelid'], 'id', 'level', 'duty');
		$data['mobilephone']=$_POST['phone'];
		// 更新数据
		$re=$hrLeaveEmployeeModel->where("employeeid=".$id)->save($data);
		if($re===false){
			$this->error("操作失败");
		}
		//name字段拼音生成
		$this->getPinyin('mis_hr_personnel_person_info','name',$id);
		if($bPrivyFamily==false&&$bPrivyRemid==false&&$bEducation==false&&$bExperience==false){
			$this->success('员工附加信息修改失败！');
		}else{
			$this->success ('员工综合信息修改成功！');
		}
	}
}