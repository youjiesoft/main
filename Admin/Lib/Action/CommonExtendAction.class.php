<?php
/**
 * @Title: CommonAction
 * @Package package_name
 * @Description: todo(核心控制器)
 * @author everyone
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2011-4-7 下午5:13:20
 * @version V1.0
 */
class CommonExtendAction extends Action {

	/**
	 * @Title: dataRoam
	 * @Description: todo(数据漫游通用方法，新增时、修改时、删除时进行数据漫游)
	 * @param 当前执行数据的模型 $modelname
	 * @param 执行模型的数据 $pkValue
	 * @param 执行的何种操作 $sourcetype
	 * @param bool $do
	 * @param bool $debug 是否调试模型
	 * @param bool $dataromaDebug  漫游调试
	 * @author yangxi
	 * @date 2015-1-15 上午11:13:47
	 * @throws
	 */
	public function dataRoam($type,$modelname,$pkValue,$sourcetype,$updateBackup) {
    	//如果为调试模式跳过数据漫游.数据漫游调试模式直接到数据漫游内部
	    $debug=$_REQUEST["debug"]?true:false;
		$dataromaDebug=$_REQUEST["dataromaDebug"]?true:false;
    	  if($dataromaDebug===true){
    	  	$debug=false;
    	  }
    	  if($debug===false){
           $msdrModel=D('MisSystemDataRoaming');
           //这里是静默漫游类型
           $result=$msdrModel->main($type=1,$modelname,$pkValue,$sourcetype,'',$updateBackup);
           if($result){
           	  $this->error($result);
           }
    	  }
	}
	/**
	 * 
	 * @Title: dataControl
	 * @Description: todo(数据控制) 
	 * @param unknown $modelname 当前模型
	 * @param unknown $operation  操作方式
	 * @author renling 
	 * @date 2015年7月13日 上午11:21:15 
	 * @throws
	 */
	public function dataControl($modelname,$operation,$pkey,$targetname,$type){
		$masModel=D("MisSystemDataControl");
		//这里是静默漫游类型
		$infostr=$masModel->main($modelname,$operation,$pkey,$targetname,$type);
		if($infostr){
			$this->error($infostr);
		}
	}
	public function dataRemind($modelname,$operation,$pkey,$targetname){
		$masModel=D("MisAutoEwe");
		//这里是静默漫游类型
		$masModel->main($modelname,$operation,$pkey,$targetname);
	}
	/**
	 * 
	 * @Title: sysremind
	 * @Description: todo(单据提示) 
	 * @param unknown $modelname
	 * @param unknown $pkey  
	 * @author renling 
	 * @date 2015年8月28日 上午10:55:41 
	 * @throws
	 */
	public function sysremind($modelname,$pkey){
	  $map=array();
	  $map['isreminds']=1;
	  $map['actionname']=$modelname;
	  $model=M("mis_dynamic_form_manage");
	  $resultlist=$model->where($map)->find();
	 //单据需要提示
	  if($resultlist){
	  	$contentstr="";
		//读取单据配置
		$configModel=M("mis_system_remind_config");
		$remindModel=M("mis_system_sysremind");
		$cfMap=array();
		$cfMap['actionname']=$modelname;
		$configList=$configModel->where($cfMap)->select();
		//替换类容如果包含字段，需要查看字段配置信息是否有转换函数 by xyz
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($modelname,false);
		$detailListNew = array();
		foreach($detailList as $dk=>$dv){
			$detailListNew[$dv['name']] = $dv;
		}
		if($configList){
			foreach ($configList as $key=>$val){
				$length=$val['fieldlegth'];
				if($_POST[$val['field']]){
					//替换类容如果包含字段，需要查看字段配置信息是否有转换函数 by xyz
					if(count($detailListNew[$val['field']]['func'])>0){
						//函数循环层
						foreach($detailListNew[$val['field']]['func'] as $fk=>$fv){
							$_POST[$val['field']] = getConfigFunction($_POST[$val['field']],$fv,$detailListNew[$val['field']]['funcdata'][$fk]);
						}
					}
					$contentstr.=cut_str($_POST[$val['field']],$length);
				}
			}
		}
		$remindDate=array();
		$remindDate['modelname']=$modelname;
		$remindDate['content']=$contentstr;
		$remindDate['remindcreateid']=$_SESSION[C('USER_AUTH_KEY')];
		//$remindDate['remindcreatetime']=time();
		$remindDate['pkey']=$pkey;
		$remindDate['isread']=0;
		$remindDate['createid']=$_SESSION[C('USER_AUTH_KEY')];
		$remindDate['createtime']=time();
		if($_POST['auditUser']){
			//查询当前单据是否带有字段提示人
			$fieldModel=M("mis_system_remind_config_userfield");
			$fieldMap=array();
			$fieldMap['actionname']=$modelname;
			$fieldMap['status']=1;
			$fieldList=$fieldModel->where($fieldMap)->getField("id,userfield");
			if($fieldList){
				$fuserArr=array();
				foreach ($fieldList as $fkey=>$fval){
					if($_POST[$fval]){
						$fuserArr[]=$_POST[$fval];
					}
				}
				$_POST['auditUser']=$_POST['auditUser'].",".explode(',', $fuserArr);
			}
			$userList=explode(',', $_POST['auditUser']);
			foreach ($userList as $ukey=>$uval){
				$remindDate['userid']=$uval;
				$remindModel->add($remindDate);
			}
		}else{
			//通知所有人
			$userModel=D("User");
			$userMap=array();
			$userMap['status']=1;
			$userMap['account']=array('not in','admin');
			$userMap['usertype']=1;
			$userList=$userModel->where($userMap)->getField("id,account");
			foreach ($userList as $ulkey=>$ulval){
				$remindDate['userid']=$ulkey;
				$remindModel->add($remindDate);
			}
		}		
	  }
	}
	/**
	 * @Title: getFormFlow 
	 * @Description:根据post获取流程模板展示  
	 * @author liminggang 
	 * @date 2014-9-29 下午4:16:18 
	 * @throws
	 */
	public function getFormFlow(){
		$data = $_POST;
		$model= D($this->getActionName());
		$data = $model->create($data);
		$content = W("ShowFormFlow",$data,true);
		echo $content;
	}
	/**
	 * @Title: lookupadduser
	 * @Description: todo(选择员工)
	 * @author 杨东
	 * @date 2013-9-13 下午4:26:54
	 * @throws
	 */
	public function lookupadduser(){
		$model= M('mis_system_department');
		$deptlist = $model->where("status=1")->order("id asc")->select();
		$name = $this->getActionName();
		$param['rel']=$name."adduserlist";
		$param['url']="__URL__/lookupadduser/refresh/1/deptid/#id#/";
		$param['open'] = true;//全部展开
		$typeTree = $this->getTree($deptlist,$param);
		$this->assign('tree',$typeTree);
		$map = array();
		$searchby = str_replace("-", ".", $_POST["searchby"]);
		$keyword=$this->escapeChar($_POST["keyword"]);
		//动态配置显示字段
		// 		$name = "MisHrPersonnelManagement";
		// 		if (! empty ( $name )) {
		// 			$qx_name=$name;
		// 			if(substr($name, -4)=="View"){
		// 				$qx_name = substr($name,0, -4);
		// 			}
		// 			//验证浏览及权限
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
		// 		}
		if($_POST["keyword"]){
			if($searchby =="all"){
				$where['mis_hr_personnel_person_info.name']=array('like','%'.$keyword.'%');
				$departmentlist = $model->where("status=1 and name like '%".$keyword."%'")->getField("id,name");
				if($departmentlist){
					$where['deptid']=array('in',array_keys($departmentlist));
				}
				//$where['dutyname']=array('like','%'.$keyword.'%');
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
			if($searchby=='name') $placeholder="搜索姓名";
			if($searchby=='mis_system_department-name') $placeholder="搜索部门";
			//if($searchby=='duty-name') $placeholder="搜索职位";
			//if($searchby=='dutyname') $placeholder="搜索职级";
			if($searchby=='all') $placeholder="搜索员工姓名,部门";
			$this->assign("placeholder",$placeholder);
			$this->assign('keyword',$keyword);
			$this->assign('searchby',$searchby);
		}
		$map['working'] = 1;    //在职
		$deptid = $_REQUEST['deptid'];
		if ($deptid ) {
			$map['mis_hr_personnel_person_info.deptid'] = $deptid;
		}
		if (method_exists ( $this, '_adduserfilter' )) {
			$this->_adduserfilter ( $map );
		}
		$this->_list('MisHrBasicEmployee',$map);
		$this->assign('deptid',$deptid);
		if ($_REQUEST['refresh']) {
			$this->display('lookupadduserlist');
		} else {
			$this->display("lookupadduser");
		}
	}
	
		/**
	 * @Title: lookupUserList
	 * @Description:  用ztree形式查询出所有部门员工信息, 注意：查询的用户为绑定的后台用户)
	 * @author yangxi
	 * @date  2013-11-13
	 */
	public function lookupUserList($lookupObj='lookupUser',$lookupList='lookupUserList'){
		$userid=$_SESSION[C('USER_AUTH_KEY')];
		$viewModel=D('MisHrPersonnelUserDeptView');
		$companyList=$viewModel->where('user.id='.$userid)->field('companyid')->select();
		foreach ($companyList as $comk=>$comv){
			$companyArr[]=$comv['companyid'];
		}
		//构造左侧部门树结构
		$model= M('mis_system_department');
		$departmentmap['status']=1;
		if(!empty($companyList) && $userid!=1){
			$departmentmap['companyid']=array('in',$companyArr);
		}
		$deptlist = $model->where($departmentmap)->order("id asc")->select();
		$param['rel']="misrowaccessright";
		$param['url']="__URL__/lookupUserList/jump/1/deptid/#id#/parentid/#parentid#/companyid/#companyid#";
		$common = A('Common');
		$typeTree = $common->getTree($deptlist,$param);
		//获得树结构json值
		$this->assign('tree',$typeTree);
		$map = array();
		$keyword	= $_POST['keyword'];
		$searchby = str_replace("-", ".", $_POST["searchby"]);
		if ($keyword) {
			if($searchby =="all"){
				$MisSystemDepartmentModel=D("MisSystemDepartment");
				$deptMap=array();
				$deptMap['name']=array('like','%'.$keyword.'%');
				$deptMap['status']=1;
				$MisSystemDepartmentList=$MisSystemDepartmentModel->where($deptMap)->getField("id,name");
				if($MisSystemDepartmentList){
				$where['deptid']=array("in",array_keys($MisSystemDepartmentList));
				}
				$MisSystemDutyModel=D("MisSystemDuty");
				$dutyMap=array();
				$dutyMap['name']=array('like','%'.$keyword.'%');
				$dutyMap['status']=1;
				$MisSystemDutyList=$MisSystemDutyModel->where($dutyMap)->getField("id,name");
				if($MisSystemDutyList){
				$where['dutyid']=array("in",array_keys($MisSystemDutyList));
				}
				$where['user.name']=array('like','%'.$keyword.'%');
				$where['_logic']='OR';
				$map['_complex'] = $where;
			}else{
				if($searchby=="mis_system_department.name"){
					$MisSystemDepartmentModel=D("MisSystemDepartment");
					$deptMap=array();
					$deptMap['name']=array('like','%'.$keyword.'%');
					$deptMap['status']=1;
					$MisSystemDepartmentList=$MisSystemDepartmentModel->where($deptMap)->getField("id,name");
					$map['deptid']=array("in",array_keys($MisSystemDepartmentList));
				}else if($searchby=="duty.name"){
					$MisSystemDutyModel=D("MisSystemDuty");
					$dutyMap=array();
					$dutyMap['name']=array('like','%'.$keyword.'%');
					$dutyMap['status']=1;
					$MisSystemDutyList=$MisSystemDutyModel->where($dutyMap)->getField("id,name");
					$map['dutyid']=array("in",array_keys($MisSystemDutyList));
				}else{
					$map[$searchby]=array('like','%'.$keyword.'%');
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
				$placeholder="搜索职级";
			}
			if($searchby=='all'){
				$placeholder="搜索员工姓名,部门,职级";
			}
			$this->assign('placeholder', $placeholder);
			$this->assign('searchby', $searchby);
			$this->assign('keyword', $keyword);
		}
		$map['working']=1;	//在职状态
		$map['user.status']=1;	//后台用户正常状态
		$map['_string'] = "user.id<>'' or user.id<>0";
		$map['user_dept_duty.status']=1;
		if($_REQUEST['companyid']!=null){
			$map['companyid']=$_REQUEST['companyid'];
		}else{
			$map['companyid']=$deptlist[0]['companyid'];
		}
		$deptid		= $_REQUEST['deptid'];	//获得部门节点
		$parentid	= $_REQUEST['parentid'];	//获得父类节点
		$companyid	= $_REQUEST['companyid'];
		if ($deptid && $parentid) {
			$deptlist =array_unique(array_filter (explode(",",$this->downAllChildren($deptlist,$deptid))));
			$map['deptid'] = array(' in ',$deptlist);
		}
		$multi = intval($_REQUEST['multi']) ? 1:0;
		$this->_list('MisHrPersonnelUserDeptView',$map,'id',true);
		$this->assign('deptid',$deptid);
		$this->assign('parentid',$parentid);
		$this->assign('companyid',$companyid);
		$this->assign('multi',$multi);
		if ($_REQUEST['jump']) {
			$this->display($lookupList);
		} else {
			$this->display($lookupObj);
		}
	}
	/**
	 *
	 * @Title: lookupmanage
	 * @Description: todo(用ztree形式查询出所有部门员工信息,
	 * 					   注意：这里只实用与在职人员。如果不是请自己写方法在控制器里。)
	 * @author liminggang
	 * @throws
	 */
	public function lookupStaffList($lookupObj='lookupmanage',$lookupList='lookupmanagelist'){
		$model= M('mis_system_department');
		$deptlist = $model->where("status=1")->order("id asc")->select();
		$param['rel']="positiveBox";
		$param['url']="__URL__/lookupStaffList/jump/1/deptid/#id#/parentid/#parentid#";
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
		// 		$map['positivestatus']=1; //转正
		$deptid		= $_REQUEST['deptid'];
		$parentid	= $_REQUEST['parentid'];
		if ($deptid && $parentid) {
			$deptlist =array_unique(array_filter (explode(",",$this->downAllChildren($deptlist,$deptid))));
			$map['mis_hr_personnel_person_info.deptid'] = array(' in ',$deptlist);
		}
		$this->_list('MisHrPersonnelAppraisalInfoView',$map);
		$this->assign('deptid',$deptid);
		$this->assign('parentid',$parentid);

		$multi = intval($_REQUEST['multi']) ? 1:0;
		$this->assign('multi',$multi);

		if ($_REQUEST['jump']) {
			$this->display($lookupList);
		} else {
			$this->display($lookupObj);
		}
	}
	/**
	 *
	 * @Title: lookupmanage
	 * @Description: todo(用ztree形式查询出所有部门员工信息,
	 * 					   注意：这里只实用与在职人员。如果不是请自己写方法在控制器里。)
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
		// 		$map['positivestatus']=1; //转正
		$deptid		= $_REQUEST['deptid'];
		$parentid	= $_REQUEST['parentid'];
		if ($deptid && $parentid) {
			$deptlist =array_unique(array_filter (explode(",",$this->downAllChildren($deptlist,$deptid))));
			$map['mis_hr_personnel_person_info.deptid'] = array(' in ',$deptlist);
		}
		$this->_list('MisHrPersonnelAppraisalInfoView',$map);
		$this->assign('deptid',$deptid);
		$this->assign('parentid',$parentid);

		$multi = intval($_REQUEST['multi']) ? 1:0;
		$this->assign('multi',$multi);

		if ($_REQUEST['jump']) {
			$this->display('lookupmanagelist');
		} else {
			$this->display("lookupmanage");
		}
	}

	/**
	 * @Title: lookupBackendUserList
	 * @Description:  用ztree形式查询出所有部门员工信息, 注意：查询的用户为绑定的后台用户)
	 * @author wangcheng
	 * @date  2013-11-13
	 */
	public function lookupBackendUserList(){
		$userid=$_SESSION[C('USER_AUTH_KEY')];
		$viewModel=D('MisHrPersonnelUserDeptView');
		$companyList=$viewModel->where('user.id='.$userid)->field('companyid')->select();
		foreach ($companyList as $comk=>$comv){
			$companyArr[]=$comv['companyid'];
		}
		//构造左侧部门树结构
		$model= M('mis_system_department');
		$departmentmap['status']=1;
		if(!empty($companyList) && $userid!=1){
			$departmentmap['companyid']=array('in',$companyArr);
		}
		$deptlist = $model->where($departmentmap)->order("id asc")->select();
		$param['rel']="misrowaccessright1";
		$param['url']="__URL__/lookupBackendUserList/jump/1/deptid/#id#/parentid/#parentid#/companyid/#companyid#";
		$common = A('Common');
		$typeTree = $common->getTree($deptlist,$param);
		//获得树结构json值
		$this->assign('tree',$typeTree);
		$map = array();
		$keyword	= $_POST['keyword'];
		$searchby = str_replace("-", ".", $_POST["searchby"]);
		if ($keyword) {
			if($searchby =="all"){
				$MisSystemDepartmentModel=D("MisSystemDepartment");
				$deptMap=array();
				$deptMap['name']=array('like','%'.$keyword.'%');
				$deptMap['status']=1;
				$MisSystemDepartmentList=$MisSystemDepartmentModel->where($deptMap)->getField("id,name");
				if($MisSystemDepartmentList){
				$where['deptid']=array("in",array_keys($MisSystemDepartmentList));
				}
				$MisSystemDutyModel=D("MisSystemDuty");
				$dutyMap=array();
				$dutyMap['name']=array('like','%'.$keyword.'%');
				$dutyMap['status']=1;
				$MisSystemDutyList=$MisSystemDutyModel->where($dutyMap)->getField("id,name");
				if($MisSystemDutyList){
				$where['dutyid']=array("in",array_keys($MisSystemDutyList));
				}
				$where['user.name']=array('like','%'.$keyword.'%');
				$where['_logic']='OR';
				$map['_complex'] = $where;
			}else{
				if($searchby=="mis_system_department.name"){
					$MisSystemDepartmentModel=D("MisSystemDepartment");
					$deptMap=array();
					$deptMap['name']=array('like','%'.$keyword.'%');
					$deptMap['status']=1;
					$MisSystemDepartmentList=$MisSystemDepartmentModel->where($deptMap)->getField("id,name");
					$map['deptid']=array("in",array_keys($MisSystemDepartmentList));
				}else if($searchby=="duty.name"){
					$MisSystemDutyModel=D("MisSystemDuty");
					$dutyMap=array();
					$dutyMap['name']=array('like','%'.$keyword.'%');
					$dutyMap['status']=1;
					$MisSystemDutyList=$MisSystemDutyModel->where($dutyMap)->getField("id,name");
					$map['dutyid']=array("in",array_keys($MisSystemDutyList));
				}else{
					$map[$searchby]=array('like','%'.$keyword.'%');
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
				$placeholder="搜索职级";
			}
			if($searchby=='all'){
				$placeholder="搜索员工姓名,部门,职级";
			}
			$this->assign('placeholder', $placeholder);
			$this->assign('searchby', $searchby);
			$this->assign('keyword', $keyword);
		}
		$map['working']=1;	//在职状态
		$map['user.status']=1;	//后台用户正常状态
		$map['_string'] = "user.id<>'' or user.id<>0";
		$map['user_dept_duty.status']=1;
		if($_REQUEST['companyid']!=null){
			$map['companyid']=$_REQUEST['companyid'];
		}else{
			$map['companyid']=$deptlist[0]['companyid'];
		}
		$deptid		= $_REQUEST['deptid'];	//获得部门节点
		$parentid	= $_REQUEST['parentid'];	//获得父类节点
		$companyid	= $_REQUEST['companyid'];
		if ($deptid && $parentid) {
			$deptlist =array_unique(array_filter (explode(",",$this->downAllChildren($deptlist,$deptid))));
			$map['deptid'] = array(' in ',$deptlist);
		}
		$multi = intval($_REQUEST['multi']) ? 1:0;
		$common->_list('MisHrPersonnelUserDeptView',$map,'id',true);
		$this->assign('deptid',$deptid);
		$this->assign('parentid',$parentid);
		$this->assign('companyid',$companyid);
		$this->assign('multi',$multi);
		if ($_REQUEST['jump']) {
			$this->display('lookupBackendUserList');
		} else {
			$this->display("lookupBackendUser");
		}
	}
	/**
	 +----------------------------------------------------------
	 * 计算带回含税协议价 或者 参考价格
	 +----------------------------------------------------------
	 * @author wangcheng
	 * @date:2012-03-27
	 * @param decimal $price  	采购协议价 或者 含税采购协议价 或者 销售协议价 或者 含税销售协议价
	 * @param void 	$type  		1增值税，0 为参考协议价
	 * @param void 	$taxid  	对应的销售或供应商税金id
	 * @param void 	$typeo  	对应type=0时 采用百分比或者绝对值 1为百分比，0为绝对值
	 * @param string $typeovalue 	参考价格系数
	 +----------------------------------------------------------
	 * @return void $p
	 +----------------------------------------------------------
	 */
	public function getPrice(){
		$price= $this->escapeStr($_POST['price']) ? $this->escapeStr($_POST['price']):0;
		$type= 	$this->escapeStr($_POST['type']) ? $this->escapeStr($_POST['type']):0;
		$taxid= $this->escapeStr($_POST['taxid']) ? $this->escapeStr($_POST['taxid']):0;
		$typeo= $this->escapeStr($_POST['typeo']) ? $this->escapeStr($_POST['typeo']):0;
		$typeovalue=$this->escapeStr($_POST['typeovalue']) ? $this->escapeStr($_POST['typeovalue']):0;
		$ajax= $this->escapeStr($_POST['ajax']) ? $this->escapeStr($_POST['ajax']):0;
		$pos= $this->escapeStr($_POST['pos']) ? $this->escapeStr($_POST['pos']):0;
		$p=0;
		if( $price >= 0 ){
			if($type and $taxid){
				$model=D("MisTaxGroup");
				$map['id']=$taxid;
				$tax = $model->where($map)->getField("taxdetail");
				if( $tax ){
					$p=$price*(100+$tax)/100;
					if($pos) $p=$price*100/(100+$tax);
				}
			}else{
				if($typeo){
					$p=$price*(100+$typeovalue)/100;
					if($pos) $p=$price*100/(100+$tax);
				}else{
					$p=abs($typeovalue)+$price;
					if($pos) $p=$price-abs($typeovalue);
				}
			}
		}
		if($ajax ){
			$d['price']=$p;
			$this->ajaxReturn($d);
			exit;
		}else{
			return $p;
		}
	}
	/**
	 +----------------------------------------------------------
	 * 计算带回含税协议价 或者 参考价格
	 +----------------------------------------------------------
	 * @author wangcheng
	 * @date:2012-03-27
	 * @param string $parentTable  	主表 必须是表名
	 * @param string $type  	1库存;2出库;3转移;4入库;5待入库
	 * @param string $domaintype  	01物理库存维度,02财务库存维度
	 * @param array  $searchby  	扩展的搜索条件。
	 * @param void 	$taxid  	对应的销售或供应商税金id
	 * @param void 	$typeo  	对应type=0时 采用百分比或者绝对值 1为百分比，0为绝对值
	 * @param string $typeovalue 	参考价格系数
	 +----------------------------------------------------------
	 * @return void $p
	 +----------------------------------------------------------
	 */
	function getDomainView($parentTable,$type=0,$domaintype='01',$searchby=array(),&$map=array(),$linkby_field='id'){
		$model1=D("MisInventoryDomain");
		$list1= $model1->where("status=1 and type=".$domaintype)->select();
		$type = intval($type);
		$newviewfieldsarr=$searchby1=array();
		if($parentTable ){
			$fieldsarr=$this->getFields($parentTable);
			$newviewfieldsarr[$parentTable]=array_merge(array('_as'=>$parentTable,'_type'=>'LEFT'),$fieldsarr);
			if($list1){
				foreach($list1 as $k=>$v){
					$newviewfieldsarr["mis_domain_type".$v['id']]=array('_as'=>'d'.$v['id'],'d'.$v['id'].'.content'=>'content'.$v['id'],'d'.$v['id'].'.oldcontent'=>'oldcontent'.$v['id'],'d'.$v['id'].'.types'=>'types'.$v['id'],'_on'=>$parentTable.'.'.$linkby_field.'=d'.$v['id'].'.typeobjid  and d'.$v['id'].'.types='.$type ,'_type'=>'LEFT');
					if($v['refertable']){
						$newviewfieldsarr[$v['refertable']]=array('_as'=>'dd'.$v['id'],'dd'.$v['id'].'.'.$v['referfield']=> $v['referfield'].$v['id'],'_on'=>'d'.$v['id'].'.content=dd'.$v['id'].'.id' ,'_type'=>'LEFT');
						$newviewfieldsarr[]=array('_table'=>$v['refertable'], '_as'=>'olddd'.$v['id'],'olddd'.$v['id'].'.'.$v['referfield']=> "old".$v['referfield'].$v['id'],'_on'=>'d'.$v['id'].'.oldcontent=olddd'.$v['id'].'.id' ,'_type'=>'LEFT');
						$list1[$k]['newname']=$v['referfield'].$v['id'];
						$list1[$k]['oldname']="old".$v['referfield'].$v['id'];
						$searchby1[$k] = array("id"=>'dd'.$v['id'].'-'.$v['referfield'],"val"=>$v['name']);
					}else{
						$list1[$k]['newname']='content'.$v['id'];
						$searchby1[$k] = array("id"=>'d'.$v['id'].'-content',"val"=>$v['name']);
					}
					$list1[$k]['oldcontent']='oldcontent'.$v['id'];
					$list1[$k]['content']='content'.$v['id'];
					//if( $type ) $map['d'.$v['id'].'.types']=$type;
				}
				$this->assign('list1',$list1);
				$searchby= array_merge($searchby,$searchby1);
			}
			//$model->viewFields=$newviewfieldsarr;
		}
		$this->assign('searchbylist',$searchby);
		return $newviewfieldsarr;
	}
	/**
	 * 视图查询解决详情页面汉字处理。
	 * @author liminggang
	 * @param string $parentTable  	主表 必须是表名
	 */
	function getOtherView($parentTable,&$map=array(),$pid='prodid'){
		$newviewfieldsarr=array();
		if($parentTable ){
			$fieldsarr=$this->getFields($parentTable);
			$newviewfieldsarr[$parentTable]=array_merge(array('_as'=>$parentTable,'_type'=>'LEFT'),$fieldsarr);
			$newviewfieldsarr["mis_product_code"] =array('_as'=>'mis_product_code','code'=>'pcode','name'=>'pname','prodsize','pinyin','stockqty','baseunitid','purwhid','salewhid','invwhid','_on'=>$parentTable.'.'.$pid.'=mis_product_code.id','_type'=>'left');
			$newviewfieldsarr["mis_product_type"] =array('_as'=>'mis_product_type','code'=>'ptcode','name'=>'ptname','typeid'=>'ptypeid','_on'=>'mis_product_code.typeid=mis_product_type.id','_type'=>'left');
		}
		return $newviewfieldsarr;
	}
	/**
	 * @Title: getfirstletter 
	 * @Description: 在物料编码获取方法中使用到 
	 * @param unknown_type $str
	 * @return string  
	 * @author liminggang 
	 * @date 2014-9-13 下午5:46:33 
	 * @throws
	 */
	function  getfirstletter($str){
		import ( '@.ORG.PYInitials' );
		$py = new PYInitials();
		$result = $py->getInitials($str);
		return $result;
	}
	/*
	 * 图形显示接口
	* by:mashihe
	* 2012-5-8
	*/
	public function getchart(){
		$filename = null;
		$stype = null;
		if (isset($_GET))
		{
			$image = $_SESSION[$_GET["img"]];
			if (isset($_GET["filename"]))
				$filename = $_GET["filename"];
			if (isset($_GET["stype"]))
				$stype = $_GET["stype"];
		}
		else
		{
			$image = $HTTP_SESSION_VARS[$HTTP_GET_VARS["img"]];
			if (isset($HTTP_GET_VARS["filename"]))
				$filename = $HTTP_GET_VARS["filename"];
			if (isset($HTTP_GET_VARS["stype"]))
				$stype = $HTTP_GET_VARS["stype"];
		}
		$contentType = "text/html; charset=utf-8";
		if (strlen($image) >= 3)
		{
			$c0 = ord($image[0]);
			$c1 = ord($image[1]);
			$c2 = ord($image[2]);
			if (($c0 == 0x47) && ($c1 == 0x49))
				$contentType = "image/gif";
			else if (($c1 == 0x50) && ($c2 == 0x4e))
				$contentType = "image/png";
			else if (($c0 == 0x42) && ($c1 == 0x4d))
				$contentType = "image/bmp";
			else if (($c0 == 0xff) && ($c1 == 0xd8))
				$contentType = "image/jpeg";
			else if (($c0 == 0) && ($c1 == 0))
				$contentType = "image/vnd.wap.wbmp";
			else if ($stype == ".svg")
				$contentType = "image/svg+xml";
			if (($c0 == 0x1f) && ($c1 == 0x8b))
				header("Content-Encoding: gzip");
		}
		ob_clean();
		header("Content-type: $contentType");
		if ($filename != null)
			header("Content-Disposition: inline; filename=$filename");
		print $image;
	}
	/**
	 * @Title: getNumberFormat 
	 * @Description: 数据格式化 。具体什么地方用到。不清楚。 
	 * @author liminggang 
	 * @date 2014-9-13 下午5:48:23 
	 * @throws
	 */
	public function getNumberFormat(){
		$nums=$_POST['nums']? $_POST['nums']:0;
		$detil=$_POST['de']? $_POST['de']:3;
		$nums=number_format($nums,$detil);
		echo json_encode($nums);
		exit;
	}
	/**
	 * @Desc 订单追溯公共方法
	 * @author 杨东
	 * */
	function orderretrace(){
		$name = $this->getActionName();
		$id = $this->escapeStr($_GET['id']);
		$scmodel = D('SystemConfigList');
		$view = $scmodel->getOrderretraceview();
		$arr = $scmodel->getOrderretracearr();
		foreach ($view as $key => $value) {
			if ($key == $name) {
				break;
			}
			$view[$key]['_type'] = 'RIGHT';
		}
		$morv = D('MisOrderRetraceView');
		$morv->viewFields = $view;
		$map[$name.'.id'] = $this->escapeStr($_GET['id']);
		$list = $morv->where($map)->select();
		$arr2 = array();
		foreach ($arr as $k => $v) {
			$arr2[$k]['name'] = $v['name'];
			$arr3 = array();
			foreach ($list as $k1 => $v1) {
				//$arr2[$k]['str'] = '';//'{id:'.$v1[$k.'nd'].',pid:'.$v1[$k.'id'].',name:"'.$v1[$k.'prodid'].'"}';
				if ($v1[$k.'orderno']) {
					$arr2[$k]['str'][$v1[$k.'id']] = array('orderno'=>$v1[$k.'orderno']);
					foreach ($v1 as $k2 => $v2) {
						if ($k2 == $k.'prodid') {
							if ($v1[$k.'prodid']) $arr3[$v1[$k.'prodid']] = array('qty'=>$v1[$k.'qty'],'unitid'=>$v1[$k.'unitid']);
						}
					}
					if ($arr3) {
						$arr2[$k]['str'][$v1[$k.'id']]['pnode'] = $arr3;
					}
				}
			}
		}
		$n = 1;
		foreach ($arr2 as $k => $v) {
			if ($v['str']) {
				$zNodes = array();
				foreach ($v['str'] as $k1 => $v1) {
					$zNodes[] = array(
							'id'=>$n.$k1,
							'pId'=>0,
							'name'=>$v1['orderno'],
							'open'=>true
					);
					foreach ($v1['pnode'] as $k2 => $v2) {
						$name =  htmlspecialchars_decode(getFieldBy($k2, 'id', 'name', 'MisProductCode'));
						$unit = getUnitsNames2($k2,$v2['unitid']);
						$zNodes[] = array(
								'id'=>$k2,
								'pId'=>$n.$k1,
								'name'=>$name.'/'.floatval($v2['qty']).'('.$unit.')'
						);
					}
				}
				$n = $n+1;
				$arr2[$k]['zNodes'] = json_encode($zNodes);
			}
		}
		$this->assign('arr2',$arr2);
		$this->assign('list',$list);
		$morv->viewFields = array();
		$this->display('Public:orderretrace');
	}
	/*
	 * @author  wangcheng
	* @date:2012-12-114 21:48:40
	* @describe:分类的扩展属性[可通用]
	* @param $map 查询条件 exp:map=array(
			"catalogid"=>0;//类型 [可扩展1;2;3分别代表不同的l类型];
			"linkid"   =>1024;//当前类型下所属分类id;
	);
	* @return:返回由<p> 标签组合的html 数组
	*/
	function get_expand_property($map,$data=array()){
		$model = M("mis_typeform_field_type");
		$typelist = $model->where("status=1")->getField('id,code');
		$model = M("mis_typeform_field");
		$map['status']=1;
		$fieldlist =$model->where($map)->order("pos asc")->select();
		$option=array();
		foreach($fieldlist AS $k=>$field) {
			if($form['checkreg']){
				$arrcheckreg=explode(";",$field['checkreg']);
				$ck='';
				foreach($arrcheckreg as $k2=>$v2){
					$ck.=$TypeList[$v2].' ';
				}
			}
			if($data) $field['defaultval'] = isset($data[$field['rename']]) ? $data[$field['rename']]:'';
			$option[]= $this->expand_property_html($typelist[$field['fieldtypeid']], $field['name'], $field['options'], $field['defaultval'], $field['tips'] ,$field['rename'], $field['ismust'],$ck);
		}
		return $option;
	}
	
	/*
	 * @author  wangcheng
	* @date:2012-12-114 21:48:40
	* @describe:由get_expand_property调用，用于组合html
	* @param string $type  类型 exp:text,select  参数较多请参看具体内容
	* @return:返回由<p> 标签组合的html 数组
	*/
	function expand_property_html($type = '', $title = '', $options = '' ,$default = '', $msg = '' ,$name = '',$must = '',$checkreg='') {
		$must = $must ? 'required ' : '';
		$rows = (trim($checkreg) =="editor") ? '8':'4';
		$r=$m=$s='';
		if( count(explode("date",$checkreg)) >1 ){
			$default=$default ? $default:date('Y-m-d',time());
			$s='<a href="javascript:;" class="inputDateButton">选择</a>';
		}
		if( count(explode("readonly",$checkreg)) >1 ) $r='readonly';
		$msg = $msg ? '<span class="info">&nbsp;'.$msg.'</span>':'';
		switch ($type) {
			case 'text':
				//文本框
				$option .= '<p><label>'.$title.'：</label><input type="text" class="'.$must.$checkreg.'" name="expand_property['.$name.']" value="'.$default.'" '.$r.' />'.$s.$msg.'</p>';
				break;
	
			case 'hidden':
				//隐藏表单
				$option .= '<input type="hidden" name="expand_property['.$name.']" value="'.$default.'" />';
				break;
	
			case 'file':
				//上传表单
				$option .= '<p><label>'.$title.'：</label><input type="file" class="'.$must.$checkreg.'" name="expand_property['.$name.']" value="'.$default.'" />'.$msg.'</p>';;
				break;
	
			case 'select':
				//下拉菜单
				$option .= '<p><label>'.$title.'：</label><select class="combox '.$must.$checkreg.'" name="expand_property['.$name.']">';
				$arr = explode(";", $options);
				$option .='<option value="">--选择--</option>';
				foreach($arr AS $k=>$val) {
					$select= ($default==$val) ? 'selected':'';
					$option .= '<option '.$select.' value="'.$val.'">'.$val.'</option>';
				}
				$option .= '</select>'.$msg.'</p>';
				break;
	
			case 'checkbox':
				//复选框
				$option .= '<p><label>'.$title.'：</label>';
				$arr = explode(";", $options);
				foreach($arr AS $k=>$v) {
					$checked= ($default==$v) ? 'checked':'';
					$option .= $v.'<input '.$checked.' name="expand_property['.$name.'][]" type="checkbox" value="'.$v.'" />&nbsp&nbsp&nbsp';
				}
				$option .= $msg.'</p>';
				break;
	
			case 'radio':
				//单选按钮
				$option .= '<p><label>'.$title.'：</label>';
				$arr = explode(";", $options);
				foreach($arr AS $k=>$v) {
					$checked= ($default==$v) ? 'checked':'';
					$option .= $v.'<input '.$checked.' type="radio" name="expand_property['.$name.']" value="'.$v.'"/>&nbsp&nbsp&nbsp';
				}
				$option .= $msg.'</p>';
				break;
			case 'textarea':
				//文本区域
				$option .= '<p><label>'.$title.'：</label><textarea name="expand_property['.$name.']" class="'.$must.$checkreg.'" cols="60" rows="'.$rows.'">'.$default.'</textarea>'.$msg.'</p>';
				break;
			case 'pass':
				//密码表单
				$option .= '<p><label>'.$title.'：</label><input type="password" class="'.$must.$checkreg.'" name="expand_property['.$name.']" value="'.$default.'" />'.$msg.'</p>';
				break;
		}
		return $option;
	}
	/**
	 * 前端三维运算方法 接收4个参数  @changeby wangcheng
	 * @param float   $param1  第一个数据
	 * @param float   $param2  第二个数据
	 * @param int  	  $type    默认为0[0j加法,1减法,2乘法,3除法
	 * @param int  	  $decimal 保留位数默认是小数点后6位
	 * @param string  $seperator 千分位分割字符 默认为空
	 * @return float
	 */
	/*
	 public function	comboxMathematicalOperation($params1=0,$params2=0,$types=0,$decimal=2,$seperator=","){
	$param1= $params1 ? $params1:$_POST['params1'] ? $_POST['params1']:0;
	$param2= $params2 ? $params2:$_POST['params2'] ? $_POST['params2']:0;
	$type  = $types   ? $types:$_POST['types']     ? $_POST['types']:0;
	$decimal= $decimal?$decimal:empty($_POST['decimal']) ? 2:$_POST['decimal'];
	$seperator= $seperator?$seperator : empty($_POST['seperator']) ? ",":$_POST['seperator'];
	$return = 0;
	$decimal= intval($decimal);
	$param1 = str_replace(",","",$param1);
	$param2 = str_replace(",","",$param2);
	if( !is_numeric($param1) ) $param1=0;
	if( !is_numeric($param2) ) $param2=0;
	switch($type){
	case 1://减法
	$return=$param1-$param2;
	break;
	case 2://乘法
	$return=$param1*$param2;
	break;
	case 3://除法
	if($param2==0){
	$return=0;
	}else{
	$return=$param1/$param2;
	}
	break;
	default://j加法
	$return=$param1+$param2;
	break;
	}
	$return = number_format($return, $decimal,'.',$seperator);
	exit(json_encode($return));
	}
	*/
	/**
	 * 前端三维运算方法 接收1个参数  @changeby jingx
	 * @param float   $str  要运算的表达式的字符串 如：$str="100+100*100-100/100"
	 * @param int  	  $decimal 保留位数默认是小数点后6位
	 * @param string  $seperator 千分位分割字符 默认为空
	 * @return float
	 */
	public function comboxMathematicalOperation($str='',$decimal=2,$seperator=""){
		$str= $str ? $str:$_POST['str'] ? $_POST['str']:'';
		$decimal= empty($_POST['decimal']) ? $decimal:$_POST['decimal'];
		$seperator= $seperator?$seperator : empty($_POST['seperator']) ? ",":$_POST['seperator'];
		$return = 0;
		$decimal= intval($decimal);
		if ($str) {
			eval("\$return=".$str.";");
			$return=number_format($return,$decimal,'.',$seperator);
		}
		exit(json_encode($return));
	
	}
	/**
	 * 数据贯穿操作  @changeby wangcheng 2013-03-26
	 * @param float   $datafrom   	插入数据
	 * @param float   $datatoid  	数据贯穿表对应的id
	 * @param int  	  $multi    	是否是多条记录插入[默认0 单条记录插入]
	 * @param int  	  $type    	类型[0 插入,1更新,2删除,] 后续可扩展
	 * @return bool
	 */
	public function	dataPenetrate($datafrom=array(),$tableobj="",$multi=0,$type=0){
		if($datafrom){
			$model=M("MisSystemDataPenetrate");
			$map['status']=1;
			$map['tableobj']=$tableobj;
			$vo = $model->where($map)->find();
			if($vo){
				$modelsub=M('MisSystemDataPenetrateSub');
				$collist = $modelsub->where("eid=".$vo['id'])->getField("fieldname,id",true);
				if($collist){
					if( $type==0 ){
						$data=array();
						if($multi){//批量插入
							$i=0;
							foreach($datafrom as $k=>$v){
								foreach($collist as $k1=>$v1){
									if(isset($v[$k1])){
										$data[$i][$k1]=$v[$k1];
									}
								}
								$i++;
							}
							if($data){
								$m=M($vo['tableobj']);
								$result = $m->addAll($data);
								return $result;
							}
	
						}else{//一条插入
							foreach($collist as $k1=>$v1){
								if(isset($v[$k1])){
									$data[$k1]=$v[$k1];
								}
							}
							if($data){
								$m=M($vo['tableobj']);
								$result = $m->add($data);
								return $result;
							}
						}
					}
				}
			}
		}
	}
	/**
	 * @Title: getProductImage
	 * @Description: todo(通过物料ID获取产品图片列表)
	 * @param 物料ID $id
	 * @author 蒋雄
	 * @date 2013-5-18 下午2:04:48
	 * @throws
	 */
	function getProductImage($id){
		$model = D("MisProductCode");
		$code = $model->where('id='.$id)->getField('id,code,typeid');
		$typemodel = D("MisProductType");
		$typecode = $typemodel->where("status = 1 and id=".$code[$id]['typeid'])->getField('code');
		$dirArray=array();
		$dir = UPLOAD_PATH;
		$dir = str_replace('Uploads','MisProductCode',$dir);
		$dir .= $typecode;
		if (false != ($handle = opendir ( $dir ))) {
			$i=0;
			while ( false !== ($file = readdir ( $handle )) ) {
				if(preg_match('/^'.$code[$id]['code'].'/',$file)){
					$arr = array(
							'name' => iconv("GBK","UTF-8",$file),//中文乱码问题
							'url'  => iconv("GBK","UTF-8",$typecode.'/'.$file) //中文乱码问题
					);
					$dirArray[$i] = $arr;
					$i++;
				}
			}
			//关闭句柄
			closedir ( $handle );
		}
		$this->assign('piglist',$dirArray);
	}
	
	/**
	 * @Title: uploadProductImage
	 * @Description: todo(物料上传图片，上传张数不限)
	 * @param 产品ID $id
	 * @author 蒋雄
	 * @date 2013-5-18 下午2:21:25
	 * @throws
	 */
	public function uploadProductImage($id){
		$model = D("MisProductCode");
		$code = $model->where('status=1 AND id='.$id)->getField('id,code,typeid');
		$typemodel = D("MisProductType");
		$typecode = $typemodel->where("status = 1 and id=".$code[$id]['typeid'])->getField('code');
		$save_file=$_POST['swf_upload_save_name'];
		$source_file=$_POST['swf_upload_source_name'];
		$num = array();
		//获取文件中该物质的图片
		//文件自动命名规则 code_数字_当前时间戳.后缀
		$max=0;
		if ($save_file) {
			$dir = UPLOAD_PATH;
			$dir = str_replace('Uploads','MisProductCode',$dir);
			$dir .= $typecode.'/';
			if (false !== $handle = opendir($dir)) {
				$i = 0;
				while (false !== $file = readdir($handle)) {
					//获取文件名“_”后面的数字
					if(preg_match('/^'.$code[$id]['code'].'_[0-9]+/',$file)){
						$val = pathinfo($file);
						preg_match('/_[0-9]+_/',$val['filename'],$num);
						$num = str_replace('_','',$num[0]);
						//获取数字最大的数
						if($max <= $num){
							$max = $num;
						}
					}
				}
			}
			closedir($handle);
		}
		//临时文件夹里面的文件转移到目标文件夹
		foreach($save_file as $k=>$v){
			if($v){
				$fileinfo=pathinfo($v);
				$from = UPLOAD_PATH_TEMP.$v;//临时存放文件
				if( file_exists($from) ){
					$dir = UPLOAD_PATH;
					$dir = str_replace('Uploads','MisProductCode',$dir);
					$dir .= $typecode.'/';
					if( !file_exists(c) ){
						$this->createFolders($dir);
					}
					$max++;
					$to = $dir.$code[$id]['code'].'_'.$max.'_'.time().'.'.$fileinfo['extension'];
					rename($from,$to);
				}
			}
		}
	}
	/**
	 * @Title: removeProductImage
	 * @Description: todo(如果物料类别改变则转移图片)
	 * @param 产品ID $id
	 * @param 产品旧编号 $oldcode
	 * @param 产品新编号 $code
	 * @param 产品旧分类 $oldtypeid
	 * @param 产品新分类 $typeid
	 * @author 蒋雄
	 * @date 2013-5-18 下午2:21:25
	 * @throws
	 */
	public function removeProductImage($id,$oldcode = 0, $code = 0, $oldtypeid = 0, $typeid = 0){
		$dir = UPLOAD_PATH;
		$dir = str_replace('Uploads','MisProductCode',$dir);
		$model = D("MisProductType");
		//获取新分类
		$typecode = $model->where('status=1 AND id='.$typeid)->getField('code');
		if ($oldtypeid == $typeid && $oldcode != $code) {
			//只改变编号
			if (false !== $handle = opendir($dir.$typecode)) {
				while (false !== $file = readdir($handle)) {
					if (preg_match('/^'.$oldcode.'/',$file)) {
						$newname = preg_replace('/^'.$oldcode.'/',$code,$file);
						//修改文件名
						rename($dir.$typecode.'/'.$file, $dir.$typecode.'/'.$newname);
					}
				}
			}
			closedir($handle);
		} else {
			//编号、分类都改变
			//获取旧分类
			$oldtypecode = $model->where('status=1 AND id='.$oldtypeid)->getField('code');
			if (false !== $handle = opendir($dir.$oldtypecode)) {
				//判断新文件夹是否存在,不存在就新建
				if( !file_exists($dir.$typecode) ) {
					$this->createFolders($dir.$typecode);
				}
				$i = 0;
				$movelist = array();
				while (false !== $file = readdir($handle)) {
					if ($file !=='.' && $file !== '..' && preg_match('/^'.$oldcode.'/',$file)) {
						if (preg_match('/[\x7f-\xff]/', $file)) { //判断字符串中是否有中文
							$this->error ( '物料名含中文图片，请修改后手动添加到文件中' );
						}
						//转移文件
						//$from = $dir.$old.'/'.$file;
						//$to = iconv("GBK","UTF-8",$dir.$new.'/'.$file);//中文乱码问题
						//rename($from,$to);
						$movelist[$i]['old'] = $dir.$oldtypecode.'/'.$file;
						$newname = preg_replace('/^'.$oldcode.'/',$code,$file);
						$movelist[$i]['new'] = iconv("GBK","UTF-8",$dir.$typecode.'/'.$newname);//中文乱码问题
						$i++;
					}
					foreach ($movelist as $key => $val) {
						rename($val['old'],$val['new']);
					}
				}
			}
			closedir($handle);
		}
	}
	/**
	 * @Title: getProductCode
	 * @Description: todo(生成物料类别下面的物料code 规则为：见下面)
	 * @param 物料类型id $typeid
	 * @param $step 默认为0，表示AJAX请求过来。
	 * @author jiangx
	 * @date 2013-5-28
	 * @throws
	 * 编码方式
	 一级	二级	三级	物料
	 00		00		00		00000
	 */
	public function getProductCode($typeid = 0,$step=0){
		if (!$typeid) {
			$typeid = C("DEFAULTPRODUCTTYPE");
		}
		if(!$typeid && $step==0){
			exit;
		}
		$typemodel = D("MisProductType");
		$model = D("MisProductCode");
		//获取编码前缀
		$codeprefix = $typemodel->where('id='.$typeid)->getField('code');
		//获取编码前缀长度，按三级来生成编码，如果超过三级，则编码规则：一级 二级 三级 获取第三极中最大编码
		$codeprefixlen = strlen($codeprefix);
		//如果级数在三级之内
		if ($codeprefixlen <= 6) {
			$codeprefix = str_pad($codeprefix, 6, "0", STR_PAD_RIGHT);
	
			$codelist =  $model->where("typeid=".$typeid)->field('code')->select();
		} else {
			//三级以上
			$codeprefix = substr($codeprefix, 6);//获取第三极类型的编码
			$map = array();
			$map['code'] = $codeprefix;
			$typeid = $typemodel->where($map)->getField('id', true);
			$map = array();
			$map['typeid'] = $typeid;
			$codelist =  $model->where($map)->field('code')->select();
		}
		//获取当前节点下的物料编码中后缀最大的数
		$max = 0;
		$a = array();
		foreach ($codelist as $key => $val) {
			if(preg_match('/^'.$codeprefix.'/',$val['code'])){
				$codenum = intval(str_replace($codeprefix,'',$val['code']));
				if ($max < $codenum) {
					$max = $codenum;
				}
			}
		}
		$max++;
		//得到5位数的后缀
		$max = sprintf("%05d",$max);
		//在所有物资编码中判断该字段是否存在，如果存在就自增1
		$map = array();
		$map['code'] = $codeprefix.$max;
		$checkcode = $model->where($map)->count('id');
		//关于修改时编码不变，就不进入该方法；所以在此不做处理
		while ($checkcode) {
			$max++;
			$max = sprintf("%05d",$max);
			$map = array();
			$map['code'] = $codeprefix.$max;
			$checkcode = $model->where($map)->count('id');
		}
		if($step==1){
			return $codeprefix.$max;
		}else{
			echo $codeprefix.$max;
			exit;
		}
	}
	/**
	 * @Title: insertTaskHistory
	 * @Description: todo(任务管理系统插入历史记录)
	 * @param $taskid 任务id
	 * @param 表名 $tablename
	 * @param 表id $tableid
	 * @param  执行动作  $istodo
	 * @param  执行状态  $exestatus 1发布2修改3执行4关闭5暂停6完成7结束8申请暂停11转派12继续13申请审核
	 * @author jiangx
	 * @date
	 * @throws
	 */
	public function insertTaskHistory($taskid, $tablename, $tableid, $istodo, $exestatus){
		$m = D("MisTaskHistory");
		unset($_POST['id']);
		$_POST['taskid'] = $taskid;
		$_POST['tablename'] = $tablename;
		$_POST['tableid'] = $tableid;
		$_POST['istodo'] = $istodo;
		$_POST['exestatus'] = $exestatus;
		if (false === $m->create ()) {
			$this->error ( $m->getError () );
		}
		$list = $m->add ();
		if ($list === false) {
			$this->error ( L('_ERROR_') );
		}
	}
	/**
	 * @Title: getPeculiarWhere
	 * @Description: todo(项目特殊权限控制，返回一组项目ID)
	 * @return unknown
	 * @author liminggang
	 * @date 2013-8-2 下午2:02:41
	 * @throws
	 */
	public function getPeculiarWhere(){
		//获取当前登录用户
		$userid = $_SESSION [C ( 'USER_AUTH_KEY' )];
		//$map['category'] = 2; //查询项目权限组
		$map['status'] = 1;   //状态正常
		$map['modelname'] ='MisSalesProject';
		//第一步、查询数据
		$MisSalesProjectDao = M("mis_sales_project");
		$MisSalesProjectUserDao = M("mis_sales_project_user");
		$RolegroupUserDao=M("rolegroup_user");
		$MisAuthorizeProjectDao = M("mis_authorize_project");
		$list=$MisAuthorizeProjectDao->where($map)->field('id,type,objectid,projectAuthorize')->select();
		unset($map);
		//获得所在项目ID
		$map['uid'] = $userid;
		$map['status'] = 1;
		$map['condition'] = array('neq',2);  //排除已离开人员
		$projectlist=$MisSalesProjectUserDao->where($map)->group('projectid')->getField('projectid',true);
		$prolistid=implode(",",$projectlist);
		//定义一个条件
		$where="mis_sales_project.status = 1";
		$map=array();
		$listarr=array();
		if($list){
			foreach($list as $key=>$val){
				if($val['type'] == 0){
					//个人
					if($userid == $val['objectid']){
						$listarr[] = $val;
					}
				}else{
					//权限组形式，先取出权限组中的人。
					unset($map);
					$map['user_id'] = $userid;
					$map['rolegroup_id'] = $val['objectid'];
					$rglist=$RolegroupUserDao->where($map)->find();
					if($rglist){
						$listarr[] = $val;
					}
				}
			}
		}
		if($listarr){
			foreach($listarr as $key=>$val){
				if($val['type'] == 0){
					//个人
					if($userid == $val['objectid']){
						if($val['projectAuthorize'] == 0){
							//禁止查看项目
							$where.=" and mis_sales_project.id = 0" ;
						}else if($val['projectAuthorize'] == 1){
							//查看所有
							$where.=" and 1 = 1 ";
						}else if($val['projectAuthorize'] == 2){
	
							$where.=" and mis_sales_project.id in (".$prolistid.") ";
						}
					}
				}else{
					//权限组形式，先取出权限组中的人。
					unset($map);
					$map['user_id'] = $userid;
					$map['rolegroup_id'] = $val['objectid'];
					$rglist=$RolegroupUserDao->where($map)->find();
					if($rglist){
						//查询到有此人
						if($val['projectAuthorize'] == 0){
							//禁止查看项目
							$where.=" and mis_sales_project.id = 0" ;
	
						}else if($val['projectAuthorize'] == 1){
							//查看所有
							$where.=" and 1 = 1 ";
						}else if($val['projectAuthorize'] == 2){
	
							$where.=" and mis_sales_project.id in (".$prolistid.") ";
						}
					}
				}
			}
		}else{
			$where.=" and mis_sales_project.id in (".$prolistid.") ";
		}
		unset($map);
		$map['_string'] = $where;
		$projectarr=$MisSalesProjectDao->where($map)->getField('id',true);
		return $projectarr;
	}
	/**
	 * @Title: lookupInsertProduct
	 * @Description: todo(物资快速新增方法。待修改)
	 * @author liminggang
	 * @date 2013-9-8 下午9:46:09
	 * @throws
	 */
	function lookupInsertProduct(){
		$name="MisProductCode";
		$model = D ($name);
		if (!$_POST['type']) {
			$MisProductType = M("mis_product_type");
			$tp=$MisProductType->where('status = 1 and typeid =2')->field("id")->limit(1)->find();
			$_POST['typeid']=$tp['id'];
		} else {
			$_POST['typeid']=$_POST['type'];
		}
		$MisProductCode = A("MisProductCode");
		$return = $MisProductCode->lookupisgetProductCodeCheck(1);
		if ($return == 3) {
			$this->error ( '编号已存在' );
		}
		if ($return == 0) {
			$this->error ( '编号不合法' );
		}
		$prodname = $_POST['prodname'];
		$_POST['name'] = $prodname;
		$_POST['pinyin']=$this->getfirstletter($prodname);
		$_POST['used']=0;
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		//保存当前数据对象
		$list=$model->add ();
		if ($list!==false) {
			$MisProductCodeReviewModel=D("MisProductCodeReview");
			$data['productid'] = $list;
			$data['createid'] = $_SESSION[C('USER_AUTH_KEY')];
			$data['createtime'] = time();
			$MisProductCodeReviewModel->data($data);
			$result=$MisProductCodeReviewModel->add();
			if($result){
				$this->error ( L('_ERROR_') );				
			}else{
				$this->success ( L('_SUCCESS_') ,'',$list);				
			}
		} else {
			$this->error ( L('_ERROR_') );
		}
	}
	/**
	 * @Title: getExchangeRateList
	 * @Description: todo(获得汇率列表)
	 * @param HashMap map 过滤条件(默认为本位币的条件--人民币RMB)
	 * @param int status 状态(默认为1)
	 * @author（币种代码）
	 * @throws
	 */
	public function getExchangeRateList($map='',$status){
		if($map==''){
			$map['_string']="code='RMB'";
		}
		if(!$status){
			$status['status']=1;
		}else{
			$status['status']=$status;
		}
		$mfcmodel=D("MisFinanceCurrency");
		$list=$mfcmodel->where($map)->find();
		$this->assign('rate',$list);
		if($list){
			$map=array();
			$map['crto']=$list['id'];
			$mfrmodel = D("MisFinanceRate");
			$volist=$mfrmodel->where($status)->select();
			$this->assign('rateList',$volist);
		}
	}
	/**
	 * @Title: isRoleMember
	 * @Description: todo(判断当前用户是否为角色组成员)
	 * @param $userID   int 用户ID
	 * @param $roleName string 需要判断的角色组
	 * @return boolean
	 * @author（yangxi）
	 * @data   2013-09-14
	 * @throws
	 */
	public function isRoleMember($userID,$roleName){
		if($userID and $roleName){
			//拼装ProcessRole里name的查询条件
			$map['_string']="name='".$roleName."' ";
			$ProcessRoleModel=D("ProcessRole");
			$userList=$ProcessRoleModel->where($map)->find();
			$userIDArray=explode(",",$userList['userid']);
			return in_array($userID,$userIDArray);
		}
		else{
			$this->error ('判断条件不全，请联系管理员');
		}
	
	}
	/**
	 * @Title: lookupSummubAmount
	 * @Description: todo(根据折扣计算单头所有总价变化)
	 * @author liminggang
	 * @date 2013-9-29 上午11:29:27
	 * @throws
	 */
	public function lookupSummubAmount(){
		//得到单头ID
		$id=$_POST['id'];
		//得到需要查询的详情表
		$md=$_POST['md'];
		//获得折扣
		$percent = $_POST['percent'];
		//判断是否含税
		$istax = $_POST['istax'];
		if($id){
			//实例化模型对象
			$model = D($md);
			$subClist = $model->where('status = 1 and masid='.$id)->field('sum(showamount) as showamount,sum(amount) as amount,sum(taxamount) as taxamount')->find();
			$showamount = $subClist['showamount']?$subClist['showamount']:0;
			$amount = $subClist['amount']?$subClist['amount']:0;
			$taxamount = $subClist['taxamount']?$subClist['taxamount']:0;
			//如果有折扣，则计算折扣
			$amount = $amount*$percent/100;
			$showamount = $showamount*$percent/100;
			$taxamount = $taxamount*$percent/100;
			if($istax){
				$arr = array(
						'ioamount'=>$amount,
						'oamount'=>$showamount,
						'invamount'=>$showamount,
				);
			}else{
				$arr = array(
						'ioamount'=>$showamount,
						'oamount'=>$taxamount,
						'invamount'=>$taxamount,
				);
			}
			echo json_encode($arr);
		}else{
			echo '';
		}
	}
	/**
	 * @Title: checkSubProcess
	 * @Description: todo(核验订单是否能启动或审核)
	 * @author yangxi
	 * @parameter submodel 明细模型名
	 * @parameter submodel 明细模型名
	 * @parameter type     类型定义 Sales销售 Purchase采购 Invent仓储 Stock盘点delivery货运 Finance财务
	 * @parameter $checkstock 核验是否存在盘点锁定，默认开启，只有仓储部分有用，忽略错误
	 * @parameter $checkadjust 核验仓库是否锁定，默认开启，只有仓储部分有用，忽略错误
	 * @date 2013-9-29 上午11:29:27
	 * @throws error
	 */
	protected function __checkSubProcess($submodel,$masid,$type='Sales',$checkStock=false,$checkAdjust=false){
		//realinfo
		$inventArray=array('Invent','Stock','Finance');//要取realinfoid,whid
		$stockArray=array('Invent','Stock','Finance');//盘点锁定
		$adjustArray=array('Invent','Stock','Finance');//调整锁定
		// 		//第一步获取表头订单，getmasid获取明细masid,只允许取一个字段
		// 		$masmodel=D($obj['masmodel']);
		// 		$masid = $masmodel->where($obj['maswhere'])->getField($obj['getmasid']);
		if(empty($submodel)||empty($masid)){
			$this->error(L('订单不存在，请联系管理员'));
		}else{
			$subModel=M($submodel);//明细表
			$MisInventoryRealinfoModel	= M("MisInventoryRealinfo");//实时库存
			//查询明细的数据
			if(is_array($masid)){//qchlian 查询绑定字段不一定是masid
				$map=$masid;
			}else{
				$map["masid"]=$masid;
			}
			$map['status']=array('EGT',0);
			if(in_array($type,$inventArray)){
				$sublist = $subModel->where($map)->field("id,nd,realinfoid,whid")->select();
			}else{
				$sublist = $subModel->where($map)->field("id")->select();
			}
			//print_r($submodel->getLastSql());
			if(!$sublist){
				$this->error(L('明细为空，禁止操作!'));
			}
			/*****************开始判断*****************/
	
			//检查明细是否存在调整
			if($checkAdjust && in_array($type,$adjustArray)){
				unset($map);
				$map=array();
				$map['status']      =1;
				if($type=='Invent'){
					$map['ajusting']=1;
				}else if($type=='Finance'){
					$map['fajusting']=1;
				}else if($type=='Stock'){
					$map['ajusting']=1;
					$map['fajusting']=1;
				}
				$arr_realinfo =$arr_nd= array(0);
				foreach($sublist as $key=>$val){
					if($val['realinfoid']>0){
						if(!in_array($val['realinfoid'],$arr_realinfo)){
							array_push($arr_realinfo,$val['realinfoid']);
							$arr_nd[$val['realinfoid']]=$val['nd'];
						}
						$map['id'] = array(" in ",$arr_realinfo);
						$MIRNum=$MisInventoryRealinfoModel->where($map)->field('id')->find();
						//print_r($MisInventoryRealinfoModel->getLastSql());
						if($MIRNum){
							if($map['ajusting']==1){
								$this->error("序号为".$arr_nd[$MIRNum['id']]."的行存在库存调整锁定");
							}
							if(($map['fajusting']==1)){
								$this->error("序号为".$arr_nd[$MIRNum['id']]."的行存在财务调整锁定");
							}
						}
					}else if(!($submodel=='MisInventoryIntoSub' || $submodel== 'MisInventoryStockSub')){
						$this->error("序号为".$val['nd']."的行无法获取realinfoid");
						//等于物理入库，放到物理入库overprocess里判断，在此不做处理
					}
				}
			}
			//独立放置查询，核验是否明细存在盘点锁定
			if($checkStock && in_array($type,$stockArray)){
				unset($map);
				//1.核验
				//取明细中存在的whid
				$arr_whid =$arr_nd= array(0);
				foreach($sublist as $key=>$val){
					if($val['whid']>0){
						if(!in_array($val['whid'],$arr_whid)){
							array_push($arr_whid,$val['whid']);
							$arr_nd[$val['whid']]=$val['nd'];
						}
					}else{
						$this->error("序号为".$val['nd']."的行无法获取whid");
					}
				}
				//查数据库配置表锁定仓库
				$MisInventoryWarehouseModel=M('mis_inventory_warehouse');
				$map['status'] = 0; //状态为锁定状态
				$map['id'] = array(" in ",$arr_whid);
				$MIWNum=$MisInventoryWarehouseModel->where($map)->field('id')->find();
				if($MIWNum){
					$this->error("序号为".$arr_nd[$MIWNum['id']]."的行存在盘点锁定");
				}
			}
		}
	}
	/**
	 * @Title: calculatePriceAndQty
	 * @Description: todo(移动加权平均：计算产品对应的库存总量，和实际成本价)
	 * @param 产品ID数组 $prodidArray  3个数组的键值必须一致。相对应。
	 * @param 入库标示 $inv
	 * @param 总价 $amountArray
	 * @param 数量 $qtyArray  这个数量为基础单位的数量，如果不是基本单位，则转换 ;本数量是增减数量，注意
	 * @author liminggang
	 * @date 2013-11-9 下午4:27:14
	 * @throws
	 */
	public function calculatePriceAndQty($prodidArray,$qtyArray=array(),$inv =false,$amountArray=array(),$finv = false,$fqtyArray=array(),$famountArray = array()){
		//查询产品基础表单价
		$MisProductCodeModel = D("MisProductCode");
		if($prodidArray){
			foreach($prodidArray as $key=>$val){
				$data = array();
				if($inv){
					$map = array();
					$map['id'] = $val;
					$map['status'] = 1;
					$productlist=$MisProductCodeModel->where($map)->field("costofreal,stockqty")->find();
					if($finv){
						if(!$fqtyArray || !$famountArray){
							$this->error("财务入库核算价格变更库存实际成本失败");
						}else{
							if($productlist['stockqty']+$qtyArray[$key] == 0){
								$data['costofreal']=$productlist['costofreal'];//忽略最后一次价格波动
							}else{
								$totalamount = $productlist['costofreal']*$productlist['stockqty']+$amountArray[$key]-$famountArray[$key];
								$totalqty    = $productlist['stockqty']+$qtyArray[$key]-$fqtyArray[$key];
								$data['costofreal']=$totalamount/$totalqty;
							}
						}
					}else{
						if($productlist['stockqty']+$qtyArray[$key] == 0){
							$data['costofreal']=$productlist['costofreal'];//忽略最后一次价格波动
						}else{
							$totalamount = $productlist['costofreal']*$productlist['stockqty']+$amountArray[$key];
							$totalqty    = $productlist['stockqty']+$qtyArray[$key];
							$data['costofreal']=$totalamount/$totalqty;
						}
					}
				}
				if($finv){
					$newqty = 0;
					$newqty=$qtyArray[$key]-$fqtyArray[$key];
					$data['stockqty'] = array("exp","stockqty+".$newqty);
				}else{
					$data['stockqty'] = array("exp","stockqty+".$qtyArray[$key]);
				}
				$data['id'] = $val;
				$result=$MisProductCodeModel->save($data);
				if(!$result){
					$this->error("反写产品总数量和实际成本失败");
				}
			}
		}else{
			$this->error("请传入产品");
		}
	
	}
	
	/**
	 * @Title: getMyWarehouse
	 * @Description: todo(获取仓库)
	 * @author wangcheng
	 * @param @return bool 是否返回
	 * @param whstatus  1,0  仓库状态
	 * @param whtype    Invent物理环节，Finance财务环节
	 * @date 2013-12-2
	 */
	public function getMyWarehouse($return=false,$whtype='Invent',$whstatus=1){
		//仓库比较，大于等于0；盘点时等于0
		if($whstatus==1){
			//未盘点时
			$logic='EGT';
			$msg='[盘点中]';
		}else{
			//盘点时
			$logic='EQ';
			$msg='';
		}
		if($_SESSION ['a']){
			$map=array();
			$map['status']=array($logic,0);
			$mywhlist = M('mis_inventory_warehouse')->where($map)->getField("id,name");
			$this->assign("mywhlist",$mywhlist);
			return;
		}
		//初始化一个键与值都为0的数组
		$whlist = array(0=>0);
		//获取项目。
		$pusermodel = M('mis_sales_project_user');
		//项目人员表查询初始化
		$map=array();
		$map["uid"] = $_SESSION [C ( 'USER_AUTH_KEY' )];
		$map['status'] = 1;
		$projectlist = $pusermodel->where($map)->getField("id,projectid");
		$whmodel = M('mis_inventory_warehouse');
		if($projectlist){
			//根据项目获取仓库id
			$projectlist = array_unique($projectlist);
			//仓库查询初始化
			$whmap=array();
			$whmap['projectid'] =array("in",$projectlist);
			$whmap['status'] = array($logic,0);
			$whlist1 = $whmodel->where($whmap)->getField("id",true);
			if($whlist1){
				$whlist = array_merge($whlist,$whlist1);
			}
		}
		//根据特殊设置获取仓库id
		$userwarehousemodel = M('user_warehouse');
		//用户与仓库关系表查询初始化
		$userwarehousemap=array();
		$userwarehousemap['userid'] = $_SESSION [C ( 'USER_AUTH_KEY' )];
		$userwarehousemap['status'] = 1;
		$whlist2 = $userwarehousemodel->where($userwarehousemap)->getField("whid",true);
		if($whlist2){
			$whlist = array_merge($whlist,$whlist2);
		}
		//根据仓库绑定的管理员获取仓库id
		$managermap = array();
		if($whtype=='Invent'){
			$managermap['managerid'] = $_SESSION [C ( 'USER_AUTH_KEY' )];
		}
		if($whtype=='Finance'){
			$managermap['financeuserid'] = $_SESSION [C ( 'USER_AUTH_KEY' )];
		}
		$managermap['status'] = array($logic,0);
		$whlist3 = $whmodel->where($managermap)->getField("id",true);
		if($whlist3){
			$whlist = array_merge($whlist,$whlist3);
		}
		$whlist = array_unique($whlist);
	
		if($return){
			return $whlist;
		}else{
			if(count($whlist)>0){
				$mywhlist=array();
				$a=array();
				$map=array();
				$map['id'] = array("in",$whlist);
				$map['status'] =array($logic,0);
				$whlist = $whmodel->where($map)->field("id,name,status")->select();
				if(empty($whlist)){
					$mywhlist=array(''=>'仓库未锁定');
				}else{
					foreach($whlist as $key=>$val){
						if($val['status']==1){
							$mywhlist[$val['id']]=array($val['name']);
						}else{
							$mywhlist[$val['id']]=array($msg.$val['name']);
						}
					}
					foreach($mywhlist as $k=>$v){
						$mywhlist[$k] = $v[0];
					}
				}
				$this->assign("mywhlist",$mywhlist);
			}
		}
	}
	public function getCompanyDept(){
		//第一步，构造部门结构树。组合公司一起。
		$MisSystemCompanyDao = M("mis_system_company");
		$where = array();
		$where['status'] = 1;
		$companylist=$MisSystemCompanyDao->where($where)->field("id,name")->select();
	
		$MisSysemDeptDao = M("mis_system_department");
		$list=$MisSysemDeptDao->where('status = 1 and iscompany!=1')->field("id,name,companyid,parentid")->select();
		
		$model=D('MisSystemRecursion');
		$deptList=$model->modelShow('MisSystemDepartment',array('key'=>'id','pkey'=>'parentid','fields'=>"id,name,iscompany,companyid",'conditions'=>'status=1'),0,1);
		foreach ($deptList as $key=>$val){
			if($val['level']==1){
				unset($deptList[$key]);
			}
		}
		$data['compay'] =  $companylist;
		$data['dept'] = $deptList;
		return json_encode($data);
	}
	
	public function lookupSelectNotifyPerson(){
	$model=D("User");
		$map = array();
		$map['status'] = array('GT',0);
		//是管理员的不显示出来
		//$map['name'] = array('NEQ','管理员');
		if (method_exists ( $this, '_filterLookupSelectUser' )) {
			$this->_filterLookupSelectUser ( $map );
		}
		$list = $model->field("id,name,dept_id,email,mobile,pinyin")->where($map)->order('sort ASC')->select();
		foreach ($list as $uk=>$uval){
			if($uval['employeid']){
				$working=getFieldBy($uval['employeid'], 'id', 'working', 'mis_hr_personnel_person_info');
				if($working==0){
					unset($list[$uk]);
				}
			}
		}
		$num = count($list);
		$this->assign("num",$num);// 总人数
		$returnarr = array();
		$dptmodel = D("MisSystemDepartment");//部门表
		$deptlist = $dptmodel->where("status=1")->order("id asc")->field('id,name,parentid')->select();
		//部门树形
		$returnarr=$dptmodel->getDeptZtree($_SESSION['companyid'],'','','','',1);
		$this->assign('usertree',$returnarr);
		//用户组的树
		$rolegroup = array();
		$rolegroupModel = D('Rolegroup');
		$rolegroupList = $rolegroupModel->where("status=1")->order("id asc")->field('id,name,pid')->select();//所有的组
		$rolegroup_userModel = M('rolegroup_user');
		$rolegroup_userList = $rolegroup_userModel->field("rolegroup_id,user_id")->order('rolegroup_id ASC')->select();
		foreach ($rolegroupList as $k => $v) {
			foreach ($rolegroup_userList as $k2 => $v2) {
				if($v["id"] == $v2["rolegroup_id"]){
					$rolegroupList[$k]["useridarr"][] = $v2["user_id"];
				}
			}
		}
		foreach($rolegroupList as $ke=>$va){
			$newRole=array();
			$newRole['id'] = -$va['id'];
			$newRole['pId'] = 0;
			$newRole['title'] = $va['name']; //光标提示信息
			$newRole['name'] = missubstr($va['name'],18,true); //结点名字，太多会被截取,针对于现在的ZTREE，宽度不能超过18个字符。
			$newRole['open'] = false;
			$istrue = false;
			$userarr = array();
			$usernamearr = array();
			$emailarr = array();
			foreach ($list as $k2 => $v2) {
				if(in_array($v2['id'],$va["useridarr"])){
					$istrue = true;
					$newv2 = array();
					$userarr[] = $v2['id'];
					$usernamearr[] = $v2['name'];
					$emailarr[] = $v2['email'];
					$newv2['email'] = $v2['email'];
					$newv2['id'] = $v2['id'];
					$newv2['userid'] = $v2['id'];
					$newv2['pId'] = -$va['id'];
					$newv2['pinyin'] = $v2['pinyin']; //拼音
					$newv2['title'] = $v2['name']; //光标提示信息
					$newv2['username'] = $v2['name']; //光标提示信息
					$newv2['name'] = missubstr($v2['name'],18,true); //结点名字，太多会被截取,针对于现在的ZTREE，宽度不能超过18个字符。
					$newv2['icon'] = "__PUBLIC__/Images/icon/group.png";
					$newv2['open'] = false;
					array_push($rolegroup,$newv2);
				}
			}
			if($istrue){
				$newRole["userid"] = implode(",",$userarr);
				$newRole["email"] = implode(",",$emailarr);
				$newRole["username"] = implode(",",$usernamearr);
				array_push($rolegroup,$newRole);
			}
		}
// 		dump($rolegroup);
		$this->assign('rolegrouptree',json_encode($rolegroup));
		//公司的树
		$companytree=$dptmodel->getDeptZtree('','','','','',2);
		$this->assign('sysCompanytree',$companytree);
// 		$this->assign('usertree',$returnarr);
		
		$this->assign('data',$_POST["data"]);
		$this->assign('ulid',$_POST["ulid"]);
		if($_POST["ulid"]){
			$this->display("Common:multiple");// 选多个用户
		} else {
			$this->display("Common:singleUser");// 选单个用户
		}
	}
	/**
	 * @Title: lookupGetFlowWork
	 * @Description: todo(项目查看的方法。必须存放在此处，因为MODULE_NAME问题)   
	 * @author 黎明刚
	 * @date 2015年1月6日 下午4:31:06 
	 * @throws
	 */
	public function lookupGetFlowWork(){
		//获取是列表还是单据
		$datatype = $_REQUEST['datatype'];
		//获取任务ID
		$workid = $_REQUEST['workid'];
		//获取项目ID
		$projectid = $_REQUEST['projectid'];
		//项目任务数据
		$mis_project_flow_formDao = M("mis_project_flow_form");
	
		$where['id'] = $workid;
		$where['projectid'] = $projectid;
		//查询任务数据信息
		$fieldVal = $mis_project_flow_formDao->where($where)->find();
	
		switch ($fieldVal['formtype']){
			case 1:
				//附件类型
				$name = "MisProjectAttachedTemplate";
				break;
			case 2:
				//清单类型
				$name=$fieldVal['formobj'];
				break;
			default:
				break;
		}
		$where = array ();
		//查询数据
		$where ['projectid'] = $projectid;
		$where ['projectworkid'] = $workid;
		$model = D($name);
		$vo = $model->where ( $where )->find ();
		
		if ($fieldVal['formtype'] == 1) { //附件类型
			$type_list = array ();
			//获取上传格式限制
			$allexts = C ( "allexts" );
			$type_list [] = array (
					"value" => - 1,
					"name" => "全部"
			);
			foreach ( $allexts as $k => $v ) {
				$type_list [] = array (
						"value" => $k,
						"name" => $v
				);
			}
			$this->assign ( 'allexts', $type_list );
			if($vo){
				//查询附件单头信息
				$this->assign ( 'mas_vo', $vo );
				//附件明细信息
				//第一步，sub数据查询
				$submodel = M ( "mis_project_attached_template_sub" );
				$where = array ();
				$where['masid'] = $vo['id'];
				$sublist = $submodel->where($where)->select();
				//附件信息查询
				foreach($sublist as $key=>$val){
					//获取上传附件信息
					$recolist = $this->getAttachedRecordList($vo['id'],true,true,"MisProjectAttachedTemplate",$val['id'],false);
					$sublist[$key]['record'] = $recolist;
					//获取参照附件信息
					$czrecolist = $this->getAttachedRecordList($mas_vo['systemmasid'],true,true,"MisAttachedTemplate",$val['systemsubid'],false);
					$sublist[$key]['czrecord'] = $czrecolist;
				}
				$this->assign("sublist",$sublist);
				$content = $this->fetch("MisProjectAttachedTemplate:additemviewedit");
				$this->assign("content",$content);
			}else{
				//查询附件单头信息
				$model = D("MisAttachedTemplate");
				$map ["id"] = $fieldVal['formobj'];
				$mas_vo = $model->where ( $map )->find ();
				$this->assign ( 'mas_vo', $mas_vo );
				//附件明细信息
				//第一步，sub数据查询
				$submodel = M ( "mis_system_attached_template_sub" );
				$where = array ();
				$where['masid'] = $fieldVal['formobj'];
				$sublist = $submodel->where($where)->select();
				//附件信息查询
				foreach($sublist as $key=>$val){
					//获取附件信息
					$recolist = $this->getAttachedRecordList($mas_vo['id'],true,true,"MisAttachedTemplate",$val['id'],false);
					$sublist[$key]['record'] = $recolist;
				}
				$this->assign("sublist",$sublist);
				$content = $this->fetch("MisAttachedTemplate:additemviewedit");
				$this->assign("content",$content);
			}
			$this->display("MisAutoEbm:lookupGetFormobjHtmlContent");
		}else{
			//单据
			if($datatype == 0){
				$name = $fieldVal['formobj'];
				//根据模型名称，查询节点node表title
				$nodetitle = getFieldBy($name, "name", "title", "node");
				$this->assign("nodetitle",$nodetitle);
				$workname = getFieldBy($workid, "id", "name", "mis_project_flow_form");
				$this->assign("workname",$workname);
				$this->assign("name",$name);
				$_REQUEST['eid']=1;//主从表单用
				if(!$vo){
					$vo['id'] = 999999999;
				}
				$_REQUEST['eid']=$vo['id'];//主从表单用
				$_REQUEST['id']=$vo['id'];//主从表单用
				$newmodel = A($name);
				//在此方法中，有直接调用了_before_edit 和 _after_edit
				$newmodel->view(0);
				$content = $this->fetch($name.":contenthtml");
				$this->assign("content",$content);
				$this->display("MisAutoEbm:lookupGetFlowWork");
			}else{//列表
				$name = $fieldVal['formobj'];
				//列表行数据
				$list = $model->where ( $where )->select ();
				//获取配置list.inc.php配置文件
				$scdmodel = D('SystemConfigDetail');
				//读取列名称数据(按照规则，应该在index方法里面)
				$detailList = $scdmodel->getDetail($name);
				$this->assign("detailList",$detailList);
				$this->assign("list",$list);
				$this->display("MisAutoEbm:lookupGetFlowWorkList");
			}
		}
	}
	/**
	 * @Title: lookupgetMapCoordinate 
	 * @Description: todo(获取地图标点)   
	 * @author liminggang
	 * @date 2015-10-23 上午9:32:39 
	 * @throws
	 */
	public function lookupgetMapCoordinate(){
		$fieldname=$_REQUEST['fieldname'];
		$this->assign("fieldname",$fieldname);
		//获取地址
		$address = $_POST['address'];
		$this->assign("address",$address);
		//横坐标
		$newcoordinatex = $_POST['xmap'];
		$this->assign("newcoordinatex",$newcoordinatex);
		//纵坐标
		$newcoordinatey = $_POST['ymap'];
		$this->assign("newcoordinatey",$newcoordinatey);
		$this->display("map:coordinate");
	}
}

/**
 *	异常接口
 * @author quqiang
 * @date 2015年2月11日 下午2:22:42
 */
interface IException {
	/* Protected methods inherited from Exception class */
	public function getMessage();                 // Exception message
	public function getCode();                    // User-defined Exception code
	public function getFile();                    // Source filename
	public function getLine();                    // Source line
	public function getTrace();                   // An array of the backtrace()
	public function getTraceAsString();           // Formated string of trace

	/* Overrideable methods inherited from Exception class */
	public function __toString();                 // formated string for display
	public function __construct($message = null, $code = 0);
}
/**
 * 重写父类的用户自定义异常抽象类
 * @author quqiang
 * @date 2015年2月11日 下午2:25:31
 * @version V1.0
 */
abstract class CustomException extends Exception implements IException
{
	protected 	$message = 'Unknown exception';     // Exception message
	private   		$string;                            // Unknown
	protected	$code    = 0;                       // User-defined exception code
	protected	$file;                              // Source filename of exception
	protected	$line;                              // Source line of exception
	private			$trace;                             // Unknown

	public function __construct($message = null, $code = 0)
	{
		if (!$message) {
			$message = $this->message;
			//throw new $this('Unknown '. get_class($this));
		}
		parent::__construct($message, $code);
	}

	public function __toString()
	{
		return get_class($this) . " '{$this->message}' in {$this->file}({$this->line})\n"
		. "{$this->getTraceAsString()}";
	}
}
/**
 * 具体异常类-不知道具体类型的异常
 * @Title: NBM
 * @author quqiang
 * @date 2015年2月11日 下午2:30:31
 * @version V1.0
 */
class NBM extends CustomException{
	protected 	$message = '未知错误';
}
/**
 * 空指针异常
 * @Title: NPE
 * @author quqiang
 * @date 2015年2月11日 下午2:38:03
 * @version V1.0
 */
class NullPointExcetion extends CustomException{
	protected 	$message = '操作对象未找到';
}
/**
 * 空数据异常
 * @Title: NDE
 * @author quqiang
 * @date 2015年2月11日 下午2:38:03
 * @version V1.0
 */
class NullDataExcetion extends CustomException{
	protected 	$message = '操作数据为空';
}
class NullCreateOprateException extends CustomException{
	protected 	$message = '生成操作失败';
}

?>