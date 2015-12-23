<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(首页待办提醒--员工合同处理) 
 * @author yuansl 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-3-19 下午6:35:18 
 * @version V1.0
 */
class MisHrRemindEmployeeContractAction extends CommonAction{
	public function index() {
		$this->getContractWorkstatus(); //合同类型树
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$map['working']=1;//默认查询在职员工
// 		$map['contracttype']='合同';
		$map['working'] = 1 ;//工作状态正常
		if($_REQUEST['deptid']){
			$map['deptid'] = $_REQUEST['deptid'];//部门id
			$this->assign('deptid',$_REQUEST['deptid']);
		}
		//
		$mMisHrBasicEmployeeContractModel=D('MisHrBasicEmployeeforContractView');
		//获取一个月时间
		$time=time() + 2592000;
		$map['contractstatus'] = array('eq',0);
		$map['endtime'] = array(array('neq',0),array('lt',$time,array('neq',''),'and'));
// 		$aMisHrBasicEmployeeContractList=$mMisHrBasicEmployeeContractModel->where("endtime < ".$time." and endtime<>0  and endtime <> '' and contractstatus=0")->select();
		$map['status'] = 1 ;//
		$map['contractstatus'] = 0 ;//合作状态(0合同中,1已结束)
		$name = $this->getActionName();
		if (! empty ( $name )) {
			$qx_name=$name;
			if(substr($name, -4)=="View"){
				$qx_name = substr($name,0, -4);
			}
			//验证浏览及权限
// 			if( !isset($_SESSION['a']) ){
// 				if( $_SESSION[strtolower($qx_name.'_'.ACTION_NAME)]!=1 ){
// 					if( $_SESSION[strtolower($qx_name.'_'.ACTION_NAME)]==2 ){////判断部门及子部门权限
// 						$map["createid"]=array("in",$_SESSION['user_dep_all_child']);
// 					}else if($_SESSION[strtolower($qx_name.'_'.ACTION_NAME)]==3){//判断部门权限
// 						$map["createid"]=array("in",$_SESSION['user_dep_all_self']);
// 					}else if($_SESSION[strtolower($qx_name.'_'.ACTION_NAME)]==4){//判断个人权限
// 						$map["createid"]=$_SESSION[C('USER_AUTH_KEY')];
// 					}
// 				}
// 			}
			if($_REQUEST ['orderField']){
				if($_REQUEST ['orderField'] == "endtime"){
					$_REQUEST ['orderField'] = 'limittype` asc, `'.$_REQUEST ['orderField'];
				}
				$_REQUEST ['orderField'] = 'contractstatus` asc,`'.$_REQUEST ['orderField'];
			} else {
				$_REQUEST ['orderField'] = 'contractstatus` asc,`id';
			}
			$this->_list ( "MisHrBasicEmployeeContractView", $map );
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
		//首页收件箱调用方法，为ajax调用
		if ($_GET['type'] == "ajaxcall") {
			$this->display ("ajax_index");exit;
		}
		if($_REQUEST['jump']){
			$this->display("contractindex");
		}else{
			$this->display ();
		}
		return;
	}

	private  function getContractWorkstatus(){
		$tree = array(); // 树初始化
		$dptmodel = D("MisSystemDepartment");//部门表
		$deptlist = $dptmodel->where("status=1")->order("id asc")->field('id,name,parentid')->select();
		$param['rel']="MisHrRemindEmployeeContract";
		$param['url']="__URL__/index/jump/1/deptid/#id#/parentid/#parentid#";
		$param['open']= true;
		$typeTree = $this->getTree($deptlist,$param);
		$this->assign('tree',$typeTree);
	}
	public function _before_add(){
		//自动生成绩效指标编码
		$scnmodel = D('SystemConfigNumber');
		$orderno = $scnmodel->GetRulesNO('mis_hr_personnel_person_info_contract');
		$this->assign("orderno",$orderno);
		$time = time();
		$id = $_GET['id'];
		if($id){
			$becModel = D('MisHrBasicEmployeeContract');
			$vos = $becModel->where('id='.$id)->find();
			$this->assign("vos",$vos);
			$time = $vos['endtime'] + 86400;
		}
		$this->assign('time',$time);
	}
	public function edit(){
		$model=D('MisHrBasicEmployeeContract');
		$map['employeeid'] = $_REQUEST['employeeid'];
		$map['status']=1;
		$map['id']=$_REQUEST['id'];
		$vo=$model->where($map)->find();
		unset($map);
		//获取附件信息
		$this->getAttachedRecordList($vo['id']);
		
		$map['employeeid'] = $vo['employeeid'];
		$vo['accounttype']=getFieldBy($vo['employeeid'], 'id', 'accounttype', 'mis_hr_personnel_person_info');//户口类型
		$becModel = D('MisHrBasicEmployeeContract');
		$becList = $becModel->where($map)->order('starttime desc')->select();
		$becTreemiso[]=array(
				'id'=>0,
				'pId'=>-1,
				'name'=>'合同记录',
				'open'=>true
		);
		foreach($becList as $key=>$v){
			$url = '__URL__/edit/jump/2/id/'.$v['id'].'/employeeid/'.$v['employeeid'];
			if($v['contractstatus'] == 0){
				$url = '__URL__/edit/jump/1/id/'.$v['id'].'/employeeid/'.$v['employeeid'];
			}
			$becTreemiso[] = array(
					'id'=>$v['id'],
					'pId'=>0,
					'name'=>"第".(count($becList)-$key)."次",
					'title'=>"第".(count($becList)-$key)."次",
					'open'=>true,
					'rel'=>'MisHrBasicEmployeeContractRel',
					'url'=>$url,
					'target'=>'ajax'
			);
		}
		$this->assign("becTreemiso",json_encode($becTreemiso));
		$employeeid = $_REQUEST['employeeid'];
		$ide = $_REQUEST['id'];
		if($ide){
			$map['id'] = $_REQUEST['id'];
		}
		$this->assign("ide",$ide);
		if ($_REQUEST['jump'] == 1) {
			$this->assign( 'vo', $vo );
			$this->display('editlist');
			exit;
		} else if ($_REQUEST['jump'] == 2) {
			$this->assign( 'vo', $vo );
			$this->display('viewlist');
			exit;
		} else {
			if($vo['contractstatus'] == 1){
				$this->assign( "url", "viewlist" );
			} else {
				$this->assign( "url", "editlist" );
			}
		}
		$this->assign( 'vo', $vo );
		$this->display("view");
	}
	public function _before_insert(){
		//插入一条新合同时  会将前面的合同设置为结束状态
		$map['employeeid'] = $_POST['employeeid'];
		$model = D("MisHrBasicEmployeeContract");
		$model->where($map)->setField("contractstatus",1);//设置结束状态
		if($_POST['sex']=='男'){
			$_POST['sex']=1;
		}else{
			$_POST['sex']=0;
		}
		if($_POST['limittype'] == '无固定期'){
			$_POST['endtime'] = '';
			$_POST['fixed'] = '无固定';
		}  else {
			if(!$_POST['endtime']){
				$this->error ("期限为固定期时请选择结束时间！");
				exit;
			}
			$s = strtotime($_POST["starttime"]);
			$e = strtotime($_POST["endtime"]);
			if($e <= $s){
				$this->error ("结束时间必须大于开始时间！");
			}
			import ( "@.ORG.Date" );
			$date=new Date(intval( $s ));
			$_POST['fixed'] = $date->timeDiff($e,2,false);
		}
	}

	public function _after_insert($insertid){
		if ($insertid) {
			$this->swf_upload($insertid,89);
		}
	}

	public function _before_update(){
		if($_POST['sex']=='男'){
			$_POST['sex']=1;
		}else{
			$_POST['sex']=0;
		}
		if($_POST['limittype'] == '无固定期'){
			$_POST['endtime'] = '';
			$_POST['fixed'] = '无固定';
		}  else {
			if(!$_POST['endtime']){
				$this->error ("期限为固定期时请选择结束时间！");
				exit;
			}
			$s = strtotime($_POST["starttime"]);
			$e = strtotime($_POST["endtime"]);
			if($e <= $s){
				$this->error ("结束时间必须大于开始时间！");
			}
			import ( "@.ORG.Date" );
			$date=new Date(intval( $s ));
			$_POST['fixed'] = $date->timeDiff($e,2,false);
		}
		$this->swf_upload($_POST['id'],89);
	}

	function lookupgetfixed(){
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
		$map['mis_hr_personnel_person_info.working'] = 1; //在职
		//$map['positivestatus']=1; //转正 */
		//$map['workstatus']=array(1,2,'or'); //试用与正试状态
		//转正
		$map['mis_hr_personnel_person_info.status'] = 1; //正常
		$deptid	= $_REQUEST['deptid'];
		$parentid = $_REQUEST['parentid'];
		if ($deptid && $parentid) {
			$deptlist =array_unique(array_filter (explode(",",$this->findAlldept($deptlist,$deptid))));
			$map['mis_hr_personnel_person_info.deptid'] = array(' in ',$deptlist);
		}

// 		//创建合同model对象
// 		$MisHrBasicEmployeeContractModel=D('MisHrBasicEmployeeforContractView');
// 		//获取一个月时间
// 		$time=time() + 2592000;
// 		//查询合同结束日期大于当前日期人员
// 		$MisHrBasicEmployeeContractList=$MisHrBasicEmployeeContractModel->where("endtime > ".$time." and endtime<>0  and endtime <> '' and contractstatus=0")->getField('employeeid,name');
// 		//查找带回加入筛选已签合同未到期人员
// 		if($MisHrBasicEmployeeContractList){
// 			$map['id']=array('not in',array_keys($MisHrBasicEmployeeContractList));
// 		}
		$this->_list('MisHrBasicEmployeeView',$map);
		$this->assign('deptid',$deptid);
		$this->assign('parentid',$parentid);
		if ($_REQUEST['jump']) {
			$this->display('lookupmanagelist');
		} else {
			$this->display("lookupmanage");
		}
	}
	public  function importconfig(){
		$importconfig=$_POST['importconfig'];
		//如果没有配置字段默认导入全部字段
		$importconfigMap='';
		$MisImportFieldconfigModel=D('MisImportSpecial');
		$MisImportFieldconfigList=$MisImportFieldconfigModel->where(" tablename='MisHrBasicEmployeeContract' and status=1")->find();
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
				$importconfigMap.='C,';  //身份证号
			}
			if($_POST['D']){
				$importconfigMap.='D,';   //入职时间
			}
			if($_POST['E']){
				$importconfigMap.='E,';   //第一次签订合同开始时间
			}
			if($_POST['F']){
				$importconfigMap.='F,';   //第一次签订合同结束时间
			}
			if($_POST['G']){
				$importconfigMap.='G,';   //第二次签订合同开始时间
			}
			if($_POST['H']){
				$importconfigMap.='H,';   //第二次签订合同结束时间
			}
			if($_POST['I']){
				$importconfigMap.='I,';   //第三次签订合同开始时间
			}
			if($_POST['J']){
				$importconfigMap.='J,';   //第三次签订合同结束时间
			}
			if($_POST['K']){
				$importconfigMap.='K,';   //第四次签订合同开始时间
			}
			if($_POST['L']){
				$importconfigMap.='L,';   //第四次签订合同结束时间
			}
			if($_POST['M']){
				$importconfigMap.='M,';   //第五次签订合同开始时间
			}
			if($_POST['N']){
				$importconfigMap.='N,';   //第五次签订合同结束时间
			}
			if($_POST['O']){
				$importconfigMap.='O,';   //终止合同时间
			}
			if($_POST['P']){
				$importconfigMap.='P,';   //备注
			}
			$saveconfigMap['tablename']='MisHrBasicEmployeeContract';
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
	public function import(){
		//订单号可写
		$scnmodel = D('SystemConfigNumber');
		//员工合同表模型
		$MisHrBasicEmployeeContractModel=D('MisHrBasicEmployeeContract');
		$maxid=$MisHrBasicEmployeeContractModel->max('id');
		//人事表模型
		$MisHrBasicEmployeeModel=D('MisHrBasicEmployee');
		//读取配置字段
		$MisImportFieldconfigModel=D('MisImportSpecial');
		$MisImportFieldconfigList=$MisImportFieldconfigModel->where(" tablename='MisHrBasicEmployeeContract' and status=1")->find();
		$fieldAll=explode(',', $MisImportFieldconfigList['fieldname']);
		//字母列表 (五次合同对应字母)
		$letterArr = range('E','N');
		//获得操作类型
		$operation=$_POST['operation'];
		import ( '@.ORG.UploadFile');
		//算出合同期限
		import ( "@.ORG.Date" );
		if($_FILES){
			$upload = new UploadFile(); // 实例化上传类
			$upload->savePath = UPLOAD_PATH."contract_excel/";//C('savePath'); // 上传目录
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
				$inputFileName = UPLOAD_PATH."contract_excel/".$info[0]['savename'];
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
				$saveMap=array();
				foreach($dataList as $k=>$v){
					if($i >= 1 && $v['A']){ //标题除外
						//通过身份证号码查询员工ID
						$MisHrBasicEmployeeList=$MisHrBasicEmployeeModel->where("status=1 and working=1  and chinaid='".$v['C']."'")->find();
						$saveMap['employeeid']=$MisHrBasicEmployeeList['id'];//员工ID
						$maxid=$maxid+1;
						$orderno = $scnmodel->GetRulesNO('mis_hr_personnel_person_info_contract',$maxid); //合同编码
						$saveMap['orderno']=$orderno;//合同编码
						$saveMap['name']=$v['B'];//姓名
						$saveMap['sex']=$MisHrBasicEmployeeList['sex'];//性别
						$saveMap['tel']=$MisHrBasicEmployeeList['mobilephone'];//联系电话
						$saveMap['cardid']=$v['C'];//身份证
						$saveMap['familyaddress']=$MisHrBasicEmployeeList['householdregister'];//家庭地址
						$saveMap['newsleaddress']=$MisHrBasicEmployeeList['address'];//通信地址
						$saveMap['accounttype']=$MisHrBasicEmployeeList['accounttype'];//户口类型
						//2.兼职,3.自主择业,4.4050,5.退休
						if($v['Q']){ //合约性质
							//$saveMap['contracttype']="协议";//合同类型
							switch ($v['Q']){
								case '兼职':
									$saveMap['protocoltype']=2;
									break;
								case '自主择业':
									$saveMap['protocoltype']=3;
									break;
								case '4050':
									$saveMap['protocoltype']=4;
									break;
								case '退休':
									$saveMap['protocoltype']=5;
									break;
								default:
									$saveMap['protocoltype']=2;
									break;
							}
						}
						//else{
							//$saveMap['contracttype']="合同";//合同类型
						//}
						$saveMap['remark']=$v['P'];//备注
						if($v['R']){
						 $protocoltime=strtotime(str_replace('.', '-',$v['R']));
						}
						for ($num = 1; $num < 6; $num ++) {
							if($v[$letterArr[($num *2) - 2]]){
								$v[$letterArr[($num *2) - 2]]=str_replace('.', '-',$v[$letterArr[($num *2) - 2]]);
								$saveMap['starttime']=strtotime($v[$letterArr[($num *2) - 2]]);//1开始时间
								if($saveMap['starttime']>=$protocoltime && $v['R']){
									$saveMap['contracttype']="协议";//合同类型
								}else{
									$saveMap['contracttype']="合同";//合同类型
								}
								if($v[$letterArr[($num *2) - 1]]=="无限期合同" || !$v[$letterArr[($num *2) - 1]]){ // 无固定期限合同
									$saveMap['limittype']="无固定期";
									$saveMap['endtime']='';
									$saveMap['fixed']="无固定";
								}else{ //1结束时间
									$v[$letterArr[($num *2) - 1]]=str_replace('.', '-',$v[$letterArr[($num *2) - 1]]);
									$saveMap['endtime']=strtotime($v[$letterArr[($num *2) - 1]]);//1结束时间
									$saveMap['limittype']="固定期";
									//算出合同年限
									$s = $saveMap['starttime'];
									$e = $saveMap['endtime'];
									$date=new Date(intval( $s ));
									$saveMap['fixed'] = $date->timeDiff($e,2,false);
								}
								if(!$v[$letterArr[($num *2) + 1]] || $v[$letterArr[($num *2) - 1]]=="无限期合同"){//如果第二次签订开始时间没有值
									$saveMap['contractstatus']=0;//合同中
								}else{
									$saveMap['contractstatus']=1;//已结束
								}
								$result=$MisHrBasicEmployeeContractModel->add($saveMap);
								if($v[$letterArr[($num *2) - 1]]=="无限期合同" ||!$v[$letterArr[($num *2) + 1]]){
									break;
								}
							}
						}
						if($v['O']){
							unset($saveMap);
							$v['O']=str_replace('.', '-',$v[O]);
							$saveMap['endtime']=strtotime($v['O']); //合同终止时间
							$saveMap['contractstatus']=1;//已结束
							//查询map 合同为合同中的数据
							$map['employeeid']=$MisHrBasicEmployeeList['id'];//员工ID
							$map['contractstatus']=0; //合同中
							//修改合同 终止时间为最后一次签订合同结束日期
							$MisHrBasicEmployeeContractModel->where($map)->save($saveMap);
						}
					}
					$i++;
				}
				$this->success("导入成功！");
			}
		}
		$this->display();
	}
	public function stop(){
		if($_POST['step']){
			$name=$this->getActionName();
			$model = D ( $name );
			$s = $model->where('id='.$_POST['id'])->getField("starttime");
			$e = strtotime($_POST["endtime"]);
			if($e <= $s){
				$this->error ("终止时间必须大于开始时间！");
			}
			import ( "@.ORG.Date" );
			$date=new Date(intval( $s ));
			$_POST['fixed'] = $date->timeDiff($e,2,false);
			if (false === $model->create ()) {
				$this->error ( $model->getError () );
			}
			// 更新数据
			$list=$model->save ();
			if (false !== $list) {
				$this->success ( L('_SUCCESS_'));
			} else {
				$this->error ( L('_ERROR_') );
			}
		} else {
			$this->display();
		}
	}
}
?>
