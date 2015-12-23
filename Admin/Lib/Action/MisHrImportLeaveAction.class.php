<?php
/**
 * @Title: MisHrImportLeaveAction
 * @Package package_name
 * @Description: todo(离职员工导入)
 * @author renling
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-12-3 下午1:57:53
 * @version V1.0
 */
class MisHrImportLeaveAction extends CommonAction{
	/**
	 * @Title: getAgeByToCarId
	 * @Description: todo(根据身份证号码计算年龄或者出生年月日)
	 * @param 身份证 $idcard
	 * @param 是否返回出生年月 $isYear  //默认返回出生年月日，如果传入false则返回年龄
	 * @return string|Ambigous <number, unknown>
	 * @author liminggang
	 * @date 2013-11-7 下午2:52:27
	 * @throws
	 */
	public function getAgeByToCarId($idcard,$isYear=true){
		//获得身份证的出生年月日
		$year = substr($idcard,6, 4);
		$month = substr($idcard,10, 2);
		$day = intval(substr($idcard,12, 2));
		$birthday=$year."-".$month."-".$day;
		$date=strtotime($birthday);
		if($isYear){
			return $birthday;
		}else{
			//获得出生年月日的时间戳
			$today=strtotime('today');
			//获得今日的时间戳
			$diff=floor(($today-$date)/86400/365);
			//得到两个日期相差的大体年数
			//strtotime加上这个年数后得到那日的时间戳后与今日的时间戳相比
			$age=strtotime(substr($id,6,8).' +'.$diff.'years')>$today?($diff):$diff-1;
			return $age;
		}
	}
	/**
	 * @Title: importconfig
	 * @Description: todo(配置导入字段)
	 * @author renling
	 * @date 2013-12-2 下午2:02:21
	 * @throws
	 */
	public  function importconfig(){
		$importconfig=$_POST['importconfig'];
		//如果没有配置字段默认导入全部字段
		$importconfigMap='';
		$MisImportFieldconfigModel=D('MisImportSpecial');
		$MisImportFieldconfigList=$MisImportFieldconfigModel->where(" tablename='MisHrLeaveEmployee' and status=1")->find();
		$fieldAll=explode(',', $MisImportFieldconfigList['fieldname']);
		$this->assign('fieldAll',$fieldAll);
		$this->assign('MisImportFieldconfigList',$MisImportFieldconfigList);
		//导入字段配置
		if($importconfig){
			$id=$_POST['id'];
			if($_POST['A']){
				$importconfigMap='A,';  //档案号
			}
			if($_POST['B']){
				$importconfigMap.='B,';  //姓名
			}
			if($_POST['C']){
				$importconfigMap.='C,';  //身份证
			}
			if($_POST['D']){
				$importconfigMap.='D,';   //血型
			}
			if($_POST['E']){
				$importconfigMap.='E,';   //性别
			}
			if($_POST['F']){
				$importconfigMap.='F,';   //户口所在地
			}
			if($_POST['G']){
				$importconfigMap.='G,';   //婚姻状况
			}
			if($_POST['H']){
				$importconfigMap.='H,';   //民族
			}
			if($_POST['I']){
				$importconfigMap.='I,';   //文化程度
			}
			if($_POST['J']){
				$importconfigMap.='J,';   //政治面貌
			}
			if($_POST['K']){
				$importconfigMap.='K,';   //是否军人
			}
			if($_POST['L']){
				$importconfigMap.='L,';   //身高
			}
			if($_POST['M']){
				$importconfigMap.='M,';   //体重
			}
			if($_POST['N']){
				$importconfigMap.='N,';   //参加工作时间
			}
			if($_POST['O']){
				$importconfigMap.='O,';   //户籍性质
			}
			if($_POST['P']){
				$importconfigMap.='P,';   //通信电话
			}
			if($_POST['Q']){
				$importconfigMap.='Q,';   //转正日期
			}
			if($_POST['R']){
				$importconfigMap.='R,';   //员工状态
			}
			if($_POST['S']){
				$importconfigMap.='S,';   //特长
			}
			if($_POST['T']){
				$importconfigMap.='T,';   //所属部门
			}
			if($_POST['U']){
				$importconfigMap.='U,';   //职级
			}
			if($_POST['V']){
				$importconfigMap.='V,';   //合约性质
			}
			if($_POST['W']){
				$importconfigMap.='W,';   //签约时间
			}
			if($_POST['X']){
				$importconfigMap.='X,';   //指纹
			}
			if($_POST['Y']){
				$importconfigMap.='Y,';   //身份证信息
			}
			if($_POST['Z']){
				$importconfigMap.='Z,';   //是否提交
			}
			if($_POST['AA']){
				$importconfigMap.='AA,';   //保安证编号
			}
			if($_POST['AB']){
				$importconfigMap.='AB,';   //其他补充说明
			}
			if($_POST['AC']){
				$importconfigMap.='AC,';   //涪陵消防培训
			}
			if($_POST['AE']){
				$importconfigMap.='AE,';   //离职原因
			}
			$saveconfigMap['tablename']='MisHrLeaveEmployee';
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
	 * @Description: todo(导入辞职员工)
	 * @author renling
	 * @date 2013-12-2 上午11:06:18
	 * @throws
	 */
	public function import(){
		//订单号可写
		$scnmodel = D('SystemConfigNumber');
		//职位表模型
		$DutyModel=D('Duty');
		//人事表模型
		$MisHrBasicEmployeeModel=D('MisHrBasicEmployee');
		$maxid=$MisHrBasicEmployeeModel->max('id');
		//人事离职表
		$MisHrLeaveEmployeeModel=D('MisHrLeaveEmployee');
		$Leavemaxid=$MisHrLeaveEmployeeModel->max('id');
		//部门表模型
		$MisSystemDepartmentModel=D('MisSystemDepartment');
		//人事配置 -- 学历 岗位
		$mMisHrTypeinfoModel=D('MisHrTypeinfo');
		//读取配置字段
		$MisImportFieldconfigModel=D('MisImportSpecial');
		$MisImportFieldconfigList=$MisImportFieldconfigModel->where(" tablename='MisHrLeaveEmployee' and status=1")->find();
		$fieldAll=explode(',', $MisImportFieldconfigList['fieldname']);
		//获得操作类型
		$operation=$_POST['operation'];
		import ( '@.ORG.UploadFile');
		if($_FILES){
			$upload = new UploadFile(); // 实例化上传类
			$upload->savePath = UPLOAD_PATH."leavePerson_excel/";//C('savePath'); // 上传目录
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
				$inputFileName = UPLOAD_PATH."leavePerson_excel/".$info[0]['savename'];
				if($filetype=="xls"){
					$inputFileType = 'Excel5';
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
					//把标题列除外 并且姓名不为空
					if($i >= 1 &&$v['B']){  
						if($v['C']){
							//判断是否有此身份证数据  有更新
							$list=$MisHrBasicEmployeeModel->where(" status=1 and chinaid='".$v['C']."'")->find();
						}
						if($v['T']){
							//查看部门是否存在
							$depList=$MisSystemDepartmentModel->where("status=1 and name='".$v['T']."'")->find();
							if(!$depList){
								$this->error("第".($i+1)."条".$v['T']."部门名称不存在,请先新建部门再导入数据！");
								exit;
							}
						}
						if($v['U']){
							//判断名称是否存在职务表
							$dutylist = $DutyModel->where("status=1 and name='".$v['U']."'")->find();
							if(!$dutylist){
								$this->error("第".($i+1)."条".$v['U']."职级不存在,请先新建职务再导入数据！");
								exit;
							}
						}
						//必须导入字段
						$saveMap['itemid']=$v['A']; //档案号
						$saveMap['name']=$v['B']; //名字
						$chinaid=$v['C'];//18位身份证号
						$saveMap['chinaid']=$chinaid;//身份证
						$saveMap['age']=$this->getAgeByToCarId($chinaid,false);//年龄
						$saveMap['birthday']=strtotime($this->getAgeByToCarId($chinaid));//生日
						$saveMap['nativeaddress']=$v['F']; //户口所在地
						$saveMap['bloodtype']=$v['D'];//血型
						if($v['E']=="男"){//性别
							$saveMap['sex']=1;
						}else if($v['E']=="女"){
							$saveMap['sex']=0;
						}
						$saveMap['ismarry']=$v['G'];//婚姻状况
						$saveMap['national']=$v['H'];//民族
						$edulist=$mMisHrTypeinfoModel->where("status=1 and name='".$v['I']."'")->find();
						$saveMap['education']=$edulist['id'];//文化程度(学历)
						$saveMap['politicsstatus']=$v['J'];//政治面貌
						if($v['K']=="军人"){
							$saveMap['veteran']=1;//是否军人
						}
						$saveMap['employeeheight']=$v['L'];//身高
						$saveMap['weight']=$v['M'];//体重
						$indate=str_replace('.', '-', $v['N']);
						$saveMap['indate']=strtotime($indate); //参加工作时间
						if($v['O']=="城"){
							$saveMap['accounttype']=0; //城镇
						}else{
							$saveMap['accounttype']=1;//农村
						}
						$saveMap['phone']=$v['P'];//通信电话
						$saveMap['workstatus']=0; //离职员工
						$saveMap['working']=0; //离职员工
						$transferdate=str_replace('.', '-',$v['Q']);
						$saveMap['transferdate']=strtotime($transferdate);
						$leavedate=str_replace('.', '-',$v['AE']);
						$saveMap['leavedate']=strtotime($leavedate); //离职日期
						$saveLeaveDate['leavedate']=strtotime($leavedate); //离职日期
						$saveLeaveDate['leavereason']=$v['AF']; //离职原因
						$saveMap['specialskill']=$v['S'];//特长
						$saveMap['deptid']=$depList['id'];//所属部门
						$saveMap['dutylevelid']=$dutylist['id'];//职级
						if($v['V']){
							$worktypeList=$mMisHrTypeinfoModel->where("status=1 and name='".$v['V']."'")->find();
							$saveMap['worktype']=$worktypeList['id'];//工种
						}
						if($v['W']=="兼职"){
							$saveMap['agreetypeid']=2;//兼职
						}else if($v['W']=="自主择业"){
							$saveMap['agreetypeid']=3;//自主择业
						}else if($v['W']=="4050"){
							$saveMap['agreetypeid']=4;//4050
						}else{
							$saveMap['agreetypeid']=5;//退休
						}
						$v['X']=str_replace('.', '-',$v['X']);
						$saveMap['agreetime']=$v['X'];//签约时间
						$saveMap['fingerprint']=$v['Y']=="是"?1:0;//指纹
						$saveMap['identity']=$v['Z']=="是"?1:0;//身份证信息
						$saveMap['iscommit']=$v['AA']=="是"?1:0;//是否提交
						$saveMap['staffnumber']=$v['AB'];//保安证编号
						$saveMap['remark']=$v['AC'];//其他补充说明
						$saveMap['firetrain']=$v['AD']=="消防培训"?1:0;//涪陵消防培训
						if($MisImportFieldconfigList){ //用户已配置导入字段
							if(!in_array('D', $fieldAll)){ //血型
								unset($saveMap['bloodtype']);
							}
							if(!in_array('E', $fieldAll)){ //性别
								unset($saveMap['sex']);
							}
							if(!in_array('G', $fieldAll)){
								unset($saveMap['ismarry']);//婚姻状况
							}
							if(!in_array('H', $fieldAll)){
								unset($saveMap['national']);//民族
							}
							if(!in_array('I', $fieldAll)){
								unset($saveMap['education']);//文化程度(学历)
							}
							if(!in_array('J', $fieldAll)){
								unset($saveMap['education']);//政治面貌
							}
							if(!in_array('K', $fieldAll)){
								unset($saveMap['veteran']);//是否军人
							}
							if(!in_array('L', $fieldAll)){
								unset($saveMap['employeeheight']);//身高
							}
							if(!in_array('M', $fieldAll)){
								unset($saveMap['weight']);//体重
							}
							if(!in_array('O', $fieldAll)){//户籍性质
								unset($saveMap['accounttype']); //城镇
							}
							if(!in_array('Q', $fieldAll)){
								unset($saveMap['phone']);//通信电话
							}
							if(!in_array('R', $fieldAll)){
								unset($saveMap['transferdate']);//转正日期
							}
							if(!in_array('S', $fieldAll)){
								unset($saveMap['specialskill']);//特长
							}
							if(!in_array('W', $fieldAll)){
								unset($saveMap['agreetypeid']);//兼职
							}
							if(!in_array('X', $fieldAll)){
								unset($saveMap['agreetime']);//签约时间
							}
							if(!in_array('Y', $fieldAll)){
								unset($saveMap['fingerprint']);//指纹
							}
							if(!in_array('Z', $fieldAll)){
								unset($saveMap['identity']);//身份证信息
							}
							if(!in_array('AA', $fieldAll)){
								unset($saveMap['iscommit']);//是否提交
							}
							if(!in_array('AB', $fieldAll)){
								unset($saveMap['staffnumber']);//保安证编号
							}
							if(!in_array('AC', $fieldAll)){
								unset($saveMap['remark']);//备注
							}
							if(!in_array('AD', $fieldAll)){
								unset($saveMap['firetrain']);//涪陵消防培训
							}
							if(!in_array('AF', $fieldAll)){
								unset($saveLeaveDate['leavereason']);//离职原因
							}
						}
						$saveLeaveDate['leavetype']=1;  //正式自离
						$saveLeaveDate['auditState']=3;  //审核状态为：已批准
						if($operation=="add"){//操作类型是新增
							//自动生成订单编号
							$maxid = $maxid+1;
							$orderno = $scnmodel->GetRulesNO('mis_hr_personnel_person_info',$maxid);
							$saveMap['orderno']=$orderno;
							$saveMap['createid']=$_SESSION[C('USER_AUTH_KEY')];
							$saveMap['createtime']=time();
							$result=$MisHrBasicEmployeeModel->add($saveMap);
							$Leavemaxid = $Leavemaxid+1;
							$Leaveorderno = $scnmodel->GetRulesNO('mis_hr_leave_employee',$Leavemaxid);
							$saveLeaveDate['createid']=$_SESSION[C('USER_AUTH_KEY')];
							$saveLeaveDate['createtime']=time();
							$saveLeaveDate['orderno']=$Leaveorderno;  //离职编号
							$saveLeaveDate['employeeid']=$result;//人事ID
							$leaveresult=$MisHrLeaveEmployeeModel->add($saveLeaveDate);
						}else{  //操作类型是新增及修改
							if($list){ //修改数据
								$saveMap['id']=$list['id'];
								$saveMap['updateid']=$_SESSION[C('USER_AUTH_KEY')];
								$saveMap['updatetime']=time();
								$saveLeaveDate['updateid']=$_SESSION[C('USER_AUTH_KEY')];
								$saveLeaveDate['updatetime']=time();
								$result=$MisHrBasicEmployeeModel->save($saveMap);
								$leaveresult=$MisHrLeaveEmployeeModel->where(" employeeid=".$list['id'])->save($saveLeaveDate);
							}else{ //新增
								//自动生成订单编号
								$maxid = $maxid+1;
								$orderno = $scnmodel->GetRulesNO('mis_hr_personnel_person_info',$maxid);
								$saveMap['orderno']=$orderno;
								$saveMap['createid']=$_SESSION[C('USER_AUTH_KEY')];
								$saveMap['createtime']=time();
								$result=$MisHrBasicEmployeeModel->add($saveMap);
								$Leavemaxid = $Leavemaxid+1;
								$Leaveorderno = $scnmodel->GetRulesNO('mis_hr_leave_employee',$Leavemaxid);
								$saveLeaveDate['orderno']=$Leaveorderno;  //离职编号
								$saveLeaveDate['employeeid']=$result;//人事ID
								$saveLeaveDate['createid']=$_SESSION[C('USER_AUTH_KEY')];
								$saveLeaveDate['createtime']=time();
								$leaveresult=$MisHrLeaveEmployeeModel->add($saveLeaveDate);
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

	public function numberToLetter($count){
		$resultArr = array();
		$letterArr = range('A','Z');
		$index = floor($count / 26);
		$num = 26;
		for ($i =0; $i<= $index; $i++) {
			if ($i == $index) {
				$num = $count % 26;
			}
			for ($j = 1; $j <= $num; $j++) {
				$resultArr[($letterArr[$i-1] ? $letterArr[$i-1] : '') .$letterArr[$j-1]] = ($i * 26) + $j;
			}
		}
		return $resultArr;
	}

}