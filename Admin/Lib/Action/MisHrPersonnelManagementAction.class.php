<?php
/**
 * @Title: MisHrPersonnelManagementAction
 * @Package package_name
 * @Description: todo(员工管理)
 * @author renling
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-7-10 上午11:16:59
 * @version V1.0
 */
class MisHrPersonnelManagementAction extends MisHrPersonnelVoViewAction{
	public function index(){
		if($_GET['type']){
			$this->regularemployee();
		} else {
			$zhi= ($_REQUEST['ntdata']!='') ? $_REQUEST['ntdata']:0;
			$this->assign('zhi',$zhi);
			$this->display();
		}
	}
	public function regularemployee(){
		$dptmodel = D("MisSystemDepartment");//部门表
		$typeTree=$dptmodel->getDeptZtree('','true','mishrpersonnelmanagement',"regularemployee");
		$this->assign('tree',$typeTree);
		$map=$this->_search();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$MisSystemCompanyModel=D("MisSystemCompany");
		$MisSystemCompanyid=$MisSystemCompanyModel->getCompanyOne();
		$this->assign("MisSystemCompanyid",$MisSystemCompanyid);
		$map['dstatus']=1;
		if($_REQUEST['companyid']){
			$deptmap=array();
			$deptmap['companyid']=$_REQUEST['companyid'];
			$map['companyid']=$deptmap['companyid'];
		} 
		$deptmap["status"]=1;
		$deptlist=$dptmodel->where($deptmap)->select();
		//得到部门编号
		$deptid=$_REQUEST['deptid'];
		//部门父id名字是dpId 将公司部门id赋予模板
		//$parentid=$_REQUEST['parentid'];
		$ptId=$_REQUEST['ptId'];
		$this->assign("deptid",$deptid);
		//$this->assign("parentid",$parentid);
		$this->assign('ptId',$ptId);
		$this->assign('companyid',$deptmap['companyid']);
		$map['workstatus']=1;//在职
		//动态配置显示字段
		$name=$this->getActionName();
		//$name='MisHrPersonnelManagement';
		if (! empty ( $name )) {
			$qx_name=$name;
			if(substr($name, -4)=="View"){
				$qx_name = substr($name,0, -4);
			}
		//验证浏览及权限
			if( !isset($_SESSION['a']) ){				
				if( $_SESSION[strtolower($qx_name.'_'.ACTION_NAME)]!=1 ){
					if( $_SESSION[strtolower($qx_name.'_'.ACTION_NAME)]==2 ){////判断公司权限
						//查询该部门及子部门
						$map["companyid"]=$_SESSION['companyid'];
						
					}else if($_SESSION[strtolower($qx_name.'_'.ACTION_NAME)]==3){//判断部门权限
						//查询该部门人员
						$map['deptid']=$_SESSION['user_dep_id'];
// 						$map["createid"]=array("in",$_SESSION['user_dep_all_self']);
					}else if($_SESSION[strtolower($qx_name.'_'.ACTION_NAME)]==4){//判断个人权限
						$map["createid"]=$_SESSION[C('USER_AUTH_KEY')];
					}
				}else{
					if($_REQUEST['deptid']){
					$map['deptid'] =$_REQUEST['deptid'];
					}
				}
			}else{
				 if($deptid ){
					$deptlist =array_unique(array_filter (explode(",",$this->findAlldept($deptlist,$deptid))));
					$map['deptid'] = array(' in ',$deptlist);
				}
			}
			$map['user_dept_duty.status']=1;
			//检索员工状态时 过滤掉
			if($map['mis_hr_personnel_person_info.employeeStatus']){
				unset($map['mis_hr_personnel_person_info.employeeStatus']);
			}
			$this->_list ('MisHrPersonnelUserDeptView', $map );
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
		if ($_REQUEST['jump']) {
			$this->display('deptindex');
		} else {
			$this->display('regularemployee');
		}
	}
	public function addleave(){
		$id=$_GET['id'];
		if($id){
			$model = D ('MisHrRegularEmployeeView');
			$volist = $model->where("mis_hr_personnel_person_info.status=1 and    mis_hr_personnel_person_info.id=".$id)->find();
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
		//自动生成订单编号
		$orderno = $scnmodel->GetRulesNO('mis_hr_leave_employee');
		$this->assign("orderno",$orderno);
		//查询离职类别
		$forLeaveList=$MisHrTypeinfoModel->where("status=1 and pid=23 and typeid=2")->getField("id,name");
		$this->assign("forLeaveList",$forLeaveList);
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
		if($id){
			$model = D ('MisHrBasicEmployee');
			$volist = $model->where("status=1 and  id=".$id)->find();
			$this->assign('MisHrBasicEmployeeVO',$volist);
		}
		//查询岗位类别
		//$MisHrTypeinfoModel=D('MisHrTypeinfo');
		//查询工种
		//$worktypeList=$MisHrTypeinfoModel->where(" status=1 and pid=63")->getField('id,name');
		//$this->assign("worktypeList",$worktypeList);
		//查询岗位信息，判断是否选择了人员
		//根据人员部门获取部门下面的岗位
		
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
		$this->display();
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
			case "userdept"://组织结构
				$this->getuserdept();
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
			case "dispatching":
				$this->getDispatching();
				break;
			default:
				$this->display('edit');
				break;
		}
	}
	/**
	 * @Title: importconfig
	 * @Description: todo(字段配置)
	 * @author renling
	 * @date 2013-11-25 上午11:40:54
	 * @throws
	 */
	public  function importconfig(){
		$importconfig=$_POST['importconfig'];
		//如果没有配置字段默认导入全部字段
		$importconfigMap='';
		$MisImportFieldconfigModel=D('MisImportSpecial');
		$MisImportFieldconfigList=$MisImportFieldconfigModel->where(" tablename='MisHrBasicEmployee' and status=1")->find();
		$fieldAll=explode(',', $MisImportFieldconfigList['fieldname']);
		$this->assign('fieldAll',$fieldAll);
		$this->assign('MisImportFieldconfigList',$MisImportFieldconfigList);
		//导入字段配置
		if($importconfig){
			$id=$_POST['id'];
			if($_POST['A']){
				$importconfigMap='A,';  //内部编码
			}
			if($_POST['B']){
				$importconfigMap.='B,';  //姓名
			}
			if($_POST['C']){
				$importconfigMap.='C,';  //性别
			}
			if($_POST['D']){
				$importconfigMap.='D,';   //出生年月
			}
			if($_POST['E']){
				$importconfigMap.='E,';   //身份证
			}
			if($_POST['F']){
				$importconfigMap.='F,';   //籍贯
			}
			if($_POST['G']){
				$importconfigMap.='G,';   //婚姻状况
			}
			if($_POST['H']){
				$importconfigMap.='H,';   //政治面貌
			}
			if($_POST['I']){
				$importconfigMap.='I,';   //学历
			}
			if($_POST['J']){
				$importconfigMap.='J,';   //专业
			}
			if($_POST['K']){
				$importconfigMap.='K,';   //职务
			}
			if($_POST['L']){
				$importconfigMap.='L,';   //部门
			}
			if($_POST['M']){
				$importconfigMap.='M,';   //电子邮件
			}
			if($_POST['N']){
				$importconfigMap.='N,';   //手机
			}
			$saveconfigMap['tablename']='MisHrBasicEmployee';
			$saveconfigMap['fieldname']=$importconfigMap;
			if($id){  //修改
				$saveconfigMap['id']=$id;
				$saveconfigMap['updateid']=$_SESSION[C('USER_AUTH_KEY')];
				$saveconfigMap['updatetime']=time();
				$result=$MisImportFieldconfigModel->save($saveconfigMap);
					
			}else{	 //增加
				$saveconfigMap['createid']=$_SESSION[C('USER_AUTH_KEY')];
				$saveconfigMap['createtime']=time();
				$result=$MisImportFieldconfigModel->add($saveconfigMap);
			}
			if($result){
				$this->success("配置成功");
			}
		}
		$this->display();
	}
	/**
	 * @Title: import
	 * @Description: todo(导入操作)
	 * @author renling
	 * @date 2013-11-25 上午11:41:06
	 * @throws
	 */
	public function import(){
		//订单号可写
		$scnmodel = D('SystemConfigNumber');
		//职位表模型
		$DutyModel=D('Duty');
		//人事离职表
		$MisHrLeaveEmployeeModel=D('MisHrLeaveEmployee');
		$Leavemaxid=$MisHrLeaveEmployeeModel->max('id');
		//人事表模型
		$MisHrBasicEmployeeModel=D('MisHrBasicEmployee');
		$maxid=$MisHrBasicEmployeeModel->max('id');
		//$orderno = $scnmodel->GetRulesNO('mis_hr_personnel_person_info',$maxid);
		//部门表模型
		$MisSystemDepartmentModel=D('MisSystemDepartment');
		//人事配置 -- 学历
		$mMisHrTypeinfoModel=D('MisHrTypeinfo');
		//读取配置字段
		$MisImportFieldconfigModel=D('MisImportSpecial');
		$MisImportFieldconfigList=$MisImportFieldconfigModel->where(" tablename='MisHrBasicEmployee' and status=1")->find();
		$fieldAll=explode(',', $MisImportFieldconfigList['fieldname']);
		//获得操作类型
		$operation=$_POST['operation'];
		import ( '@.ORG.UploadFile');
		if($_FILES){
			$upload = new UploadFile(); // 实例化上传类
			$upload->savePath = UPLOAD_PATH."duty_excel/";//C('savePath'); // 上传目录
			$upload->saveRule = date("Y_m_d_H_i_s").rand(1000,9999);
			$upload->allowExts= array("xls","xlsx");
			if(!$upload->upload()) { // 上传错误提示错误信息
				$this->error($upload->getErrorMsg());
			}else{ // 上传成功 获取上传文件信息
				$info = $upload->getUploadFileInfo();
			}
			$filetype =end(explode(".",$info[0]['savename']));
			if($filetype=="xls"){
				import('@.ORG.PHPExcel.IOFactory', '', $ext='.php');
				$inputFileName = UPLOAD_PATH."duty_excel/".$info[0]['savename'];
				if($filetype=="xls"){
					$inputFileType = 'Excel5';
				}else if( $filetype=="xlsx"){
					$inputFileType = 'Excel2007';
				}
				$objReader = PHPExcel_IOFactory::createReader($inputFileType);
				$objPHPExcel = $objReader->load($inputFileName);
				$objPHPExcel->setActiveSheetIndex(0);
				$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
				$dataList =$sheetData;
				$excel=true;
				$i=0;
				$parentMap=array();
				foreach($dataList as $k=>$v){
					//把标题列除外
					if($i >= 1){
						if($v['E']){
							//判断是否有此身份证数据  有更新
							$list=$MisHrBasicEmployeeModel->where(" status=1 and chinaid='".$v['E']."'")->find();
						}
						if($v['L']){
							//查看部门是否存在
							$depList=$MisSystemDepartmentModel->where("status=1 and name='".$v['L']."'")->find();
							if(!$depList){
								$this->error("第".($i+1)."条".$v['L']."部门名称不存在,请先新建部门再导入数据！");
								exit;
							}
						}
						if($v['K']){
							//判断名称是否存在职务表
							$dutylist = $DutyModel->where("status=1 and name='".$v['K']."'")->find();
							if(!$dutylist){
								$this->error("第".($i+1)."条".$v['K']."职级不存在,请先新建职务再导入数据！");
								exit;
							}
						}
						if($MisImportFieldconfigList){ //用户已配置导入字段
							if(in_array('A', $fieldAll)){
								$saveMap['itemid']=$v['A'];
							}
							if(in_array('B', $fieldAll)){
								$saveMap['name']=$v['B'];
							}
							if(in_array('C', $fieldAll)){
								if($v['C']=="男"){
									$saveMap['sex']=1;
								}else if($v['C']=="女"){
									$saveMap['sex']=0;
								}
							}
							if(in_array('D', $fieldAll)){
								$chinaid=$v['E'];//18位身份证号
								$saveMap['age']=$this->getAgeByToCarId($chinaid,false);
								$saveMap['birthday']=$this->getAgeByToCarId($chinaid);
							}
							if(in_array('E', $fieldAll)){
								$saveMap['chinaid']=$v['E'];
							}
							if(in_array('F', $fieldAll)){
								$saveMap['nativeaddress']=$v['F'];
							}
							if(in_array('G', $fieldAll)){
								$saveMap['ismarry']=$v['G'];
							}
							if(in_array('H', $fieldAll)){
								$saveMap['politicsstatus']=$v['H'];
							}
							if(in_array('I', $fieldAll)){
								$eduName=$mMisHrTypeinfoModel->where("name=".$v['I'])->find();
								$saveMap['education']=$eduName['id'];
							}
							if(in_array('J', $fieldAll)){
								$saveMap['profession']=$v['J'];
							}
							if(in_array('K', $fieldAll)){
								$saveMap['dutylevelid']=$dutylist['id'];
							}
							if(in_array('L', $fieldAll)){
								$saveMap['deptid']=$depList['id'];
							}
							if(in_array('M', $fieldAll)){
								$saveMap['email']=$v['M'];
							}
							if(in_array('N', $fieldAll)){
								$saveMap['phone']=$v['N'];
							}
						}else{ //用户没有配置导入字段默认全部导入
							$saveMap['itemid']=$v['A'];
							$saveMap['name']=$v['B'];
							if(trim($v['C'])=="男"){
								$saveMap['sex']='1';
							}else if(trim($v['C'])=="女"){
								$saveMap['sex']='0';
							}
							$chinaid=$v['E'];//18位身份证号
							$saveMap['chinaid']=$chinaid;//身份证
							$saveMap['age']=$this->getAgeByToCarId($chinaid,false);
							$saveMap['birthday']=strtotime($this->getAgeByToCarId($chinaid));
							$saveMap['nativeaddress']=$v['F'];
							$saveMap['ismarry']=$v['G'];
							$saveMap['politicsstatus']=$v['H'];
							$eduName=$mMisHrTypeinfoModel->where("name='".$v['I']."'")->find();
							$saveMap['education']=$eduName['id'];
							$saveMap['profession']=$v['J'];
							$saveMap['dutylevelid']=$dutylist['id'];
							$saveMap['deptid']=$depList['id'];
							$saveMap['email']=$v['M'];
							$saveMap['phone']=$v['N'];
						}
						if($v['S']){  //员工状态  2试用,1正式,0离职
							switch ($v['S']){
								case '正式':
									$saveMap['workstatus']=1;
									if($v['O']){  //入职日期
										$saveMap['indate']=strtotime($v['O']);
									}
									if($v['P']){  //转正日期
										$saveMap['transferdate']=strtotime($v['P']);
									}
									break;
								case '离职':
									$Leavemaxid=$Leavemaxid+1;
									$saveMap['workstatus']=0; //离职
									$saveMap['working']=0;
									if($v['O']){  //入职日期
										$saveMap['indate']=strtotime($v['O']);
									}
									if($v['P']){  //转正日期
										$saveMap['transferdate']=strtotime($v['P']);
									}
									if($v['Q']){  //离职日期
										$saveMap['leavedate']=strtotime($v['Q']);
										$saveLeaveDate['leavedate']=strtotime($v['Q']);
									}
									$saveLeaveDate['orderno']=$Leavemaxid;  //离职编号
									$saveLeaveDate['leavetype']=1;  //正式自离
									$saveLeaveDate['leavereason']=$v['R'];  //离职原因
									break;
								default :
									$saveMap['workstatus']=2;
									if($v['O']){  //入职日期
										$saveMap['indate']=strtotime($v['O']);
									}
									break;
							}
						}
						if($operation=="add"){//操作类型是新增
							//自动生成订单编号
							$maxid = $maxid+1;
							$orderno = $scnmodel->GetRulesNO('mis_hr_personnel_person_info',$maxid);
							$saveMap['orderno']=$orderno;
							$saveMap['createid']=$_SESSION[C('USER_AUTH_KEY')];
							$saveMap['createtime']=time();
							$result=$MisHrBasicEmployeeModel->add($saveMap);
							$saveLeaveDate['employeeid']=$result;
							//添加入离职表
							$leaveResult=$MisHrLeaveEmployeeModel->add($saveLeaveDate);
						}else{  //操作类型是新增及修改
							if($list){ //修改数据
								$saveMap['id']=$list['id'];
								$saveMap['updateid']=$_SESSION[C('USER_AUTH_KEY')];
								$saveMap['updatetime']=time();
								$result=$DutyModel->save($saveMap);
								$result=$MisHrBasicEmployeeModel->save($saveMap);
								$leaveResult=$MisHrLeaveEmployeeModel->where(" employeeid="+$list['id'])->save($saveLeaveDate);
							}else{ //新增
								//自动生成订单编号
								$maxid = $maxid+1;
								$orderno = $scnmodel->GetRulesNO('mis_hr_personnel_person_info',$maxid);
								$saveMap['orderno']=$orderno;
								$saveMap['createid']=$_SESSION[C('USER_AUTH_KEY')];
								$saveMap['createtime']=time();
								$result=$MisHrBasicEmployeeModel->add($saveMap);
								//添加入离职表
								$leaveResult=$MisHrLeaveEmployeeModel->add($saveLeaveDate);
							}
						}
						unset($saveMap);
						if(!$result){
							$this->error("第".($i+1)."条数据有误,请查看！");
							exit;
						}
					}
					$i++;
				}
				$this->success("导入成功");
			}else{
				$this->error('文件格式不正确,请上传后缀为xls的文件!');
			}
		}
		$this->display();
	}
	/**
	 * @Title: viewInfo
	 * @Description: todo(正式员工查看详情)
	 * @author renling
	 * @date 2013-7-15 下午2:54:29
	 * @throws
	 */
	public function viewInfo(){
		//得到当前模型
		$modelname=$this->escapeChar($_REQUEST['ml']);
		//得到当前跳转的方法和跳转的页面
		$method=$this->escapeChar($_REQUEST['md']);
		$personinfoid=$this->escapeChar($_REQUEST['personinfoid']);
		$this->assign("modelname",$modelname);
		$this->assign("method",$method);
		//内部匹配模型
		$modelarray=array(
				'pr'=>'MisHrPersonnelProjectRecords',
				'ed'=>'MisHrPersonnelEducationInfo',
				'exp'=>'MisHrPersonnelExperienceInfo',
				'fam'=>'MisHrPersonnelFamilyInfo',
				'itd'=>'MisHrPersonnelIntroducerInfo');
		switch ($method) {
			//员工教育、和培训经历
			case addEducationForm:
				$this->everyAddmethod($modelarray[$modelname],$method);
				break;
				//员工工作经历
			case addWorkExperienceForm:
				$this->everyAddmethod($modelarray[$modelname],$method);
				break;
				//员工家庭成员信息
			case addFamilyForm:
				$this->everyAddmethod($modelarray[$modelname],$method);
				break;
				//员工介绍人信息
			case addIntroducerForm:
				$this->everyAddmethod($modelarray[$modelname],$method);
				break;
				//新增员工所在项目
			case subAddProjectForm:
				$this->everyAddSubmethod($method);
				break;
				//新增教育或培训经历
			case subAddEducationForm:
				$this->everyAddSubmethod($method);
				break;
				//新增工作经历
			case subAddWorkExperienceForm:
				$this->everyAddSubmethod($method);
				break;
				//新增家庭成员信息
			case subAddFamily:
				$this->everyAddSubmethod($method);
				break;
				//新增介绍人信息
			case subAddIntroducer:
				$this->everyAddSubmethod($method);
				break;
				//员工基本信息新增
			default:
				$this->addBaseInfoForm();
				break;
		}

	}
	/**
	 * @Title: addBaseInfoForm
	 * @Description: todo(查看员工基本信息)
	 * @author renling
	 * @date 2013-7-15 下午2:58:27
	 * @throws
	 */
	public function addBaseInfoForm(){
		//自动生成员工基本资料编号
		$scnmodel = D('SystemConfigNumber');
		$orderno = $scnmodel->GetRulesNO('mis_hr_personnel_person_info');
		$this->assign("orderno",$orderno);
		//订单号可写
		$writable= $scnmodel->GetWritable('mis_hr_personnel_person_info');
		$this->assign("writable",$writable);
		//附件信息表
		//$this->assign("upload_path", date("Y/m/d",time())."/".$_SESSION[C('USER_AUTH_KEY')]);

		//查询部门
		$MisSystemDepartmentModel=D('MisSystemDepartment');
		$DeptList=$MisSystemDepartmentModel->where(" status=1")->getField('id,name');
		$this->assign("DeptList",$DeptList);
		//所属公司信息
		$MisSystemCompanyDAO=M('mis_system_company');
		$companylist=$MisSystemCompanyDAO->where('status = 1')->field('id,name')->select();
		$this->assign('companylist',$companylist);
		//查询职级信息
		$deptleval = D("Duty");
		$deptlevellist=$deptleval->where('status=1')->select();
		$this->assign("dutylist",$deptlevellist);
		$this->assign('now',time());
		//查看员工基本信息
		unset($map);
		$id=$_GET['id'];
		$map['id']=$id;
		$map['status']=1;
		$model=D('MisHrRegularEmployee');
		$list=$model->where($map)->find();
		$this->assign('list', $list);
		$this->display('viewInfo');

	}
	public function delete() {
		//删除指定记录
		$model = D ('MisHrRegularEmployee');
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				$list=$model->where ( $condition )->setField ( 'status', - 1 );
				if ($list!==false) {
					$this->success ( L('_SUCCESS_') );
				} else {
					$this->error ( L('_ERROR_') );
				}
			} else {
				$this->error ( C('_ERROR_ACTION_') );
			}
		}
	}
	/**
	 * (non-PHPdoc)
	 * @see CommonAction::lookupmanage()
	 */
	public function lookupmanage(){
		$hrsearchlist=$_POST['lookuphrsearchlist'];
		$this->assign("lookuphrsearchlist",$hrsearchlist);
		$model= M('mis_system_department');
		$deptlist = $model->where("status=1")->order("id asc")->select();
		$param['rel']="MisHrPersonnelManagement_positiveBox";
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
			if($searchby=='name'){
				$placeholder="搜索姓名";
			}
			if($searchby=='mis_system_department-name'){
				$placeholder="搜索部门";
			}
			if($searchby=='duty-name'){
				$placeholder="搜索职位";
			}
			if($searchby=='dutyname'){
				$placeholder="搜索职级";
			}
			if($searchby=='all'){
				$placeholder="搜索员工姓名,部门,职位,职级";
			}
			$this->assign("placeholder",$placeholder);
			$this->assign('keyword',$keyword);
			$this->assign('searchby',$searchby);
		}
		$map['workstatus']=1;    //在职
		$deptid		= $_REQUEST['deptid'];
		if ($deptid ) {
			$map['mis_hr_personnel_person_info.deptid'] = $deptid;
		}
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
		$this->_list('MisHrBasicEmployee',$map);
		$this->assign('deptid',$deptid);
		if ($_REQUEST['jump']) {
			$this->display('lookupmanagelist');
		} else {
			$this->display("lookupmanage");
		}
	}
	public function _before_insert(){
		$_POST['account'] = $_POST['accountenglish'];
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
	}
}