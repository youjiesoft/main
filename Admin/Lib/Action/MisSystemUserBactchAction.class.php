<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: 流程批次核心控制器
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-9-18 下午2:39:16 
 * @version V1.0
 */
class MisSystemUserBactchAction extends CommonAction{
	
	public function lookupAddProcessRelation(){
		//获取节点的模型名称
		$this->assign("nodename",$_REQUEST['modelname']);
		$this->assign("jsid",$_REQUEST['relaid']);
		//获取节点模板（用于动态建模）
		$misSystemVo= require ROOT . '/Dynamicconf/autoformconfig/MisAutoAnameList.inc.php';
		$misytemlist=$misSystemVo[$_REQUEST['modelname']]['temp'];
		unset($misytemlist['index']);
		if($misytemlist){
		$this->assign("misSystemVo",$misytemlist);
		}
		//选人类型表
		$MisSystemUserObjModel = D("MisSystemUserObj");
		$userObj = $MisSystemUserObjModel->where(" status = 1")->select();
		$this->assign("userObj",$userObj);
		$this->assign("ptype",$_REQUEST['ptype']);
		$this->display();
	}
	
	/**
	 * @Title: lookupGetDuty
	 * @Description: 按职级
	 * @author liminggang
	 * @date 2014-9-18 下午2:36:53
	 * @throws
	 */
	public function lookupGetDuty(){
		//查询获取角色
		$map = array();
		$searchby = $_POST["searchby"];
		$keyword= $_POST["keyword"];
		if($keyword){
			$map[$searchby] = array('like','%'.$keyword.'%');
			$this->assign('keyword',$keyword);
			$this->assign('searchby',$searchby);
		}
		$searchby=array(
				array("id" =>"name","val"=>"按职级名称"),
		);
		$this->assign("searchbylist",$searchby);
		$this->assign("switch",$_REQUEST['switch']);
	
		$this->_list("Duty", $map,"level","desc");
	
		$this->display();
	}
	public function lookupGetField(){
		//获取模型
		$modelname = $_REQUEST['modelname'];
		//获取modelname的配置文件信息
		$scdmodel = D('SystemConfigDetail');
		//读取列名称数据(按照规则，应该在index方法里面)
		$detailList = $scdmodel->getDetail($modelname,"","","",'rules');
		$this->assign ( 'detailList', $detailList );
		
		$this->assign("switch",$_REQUEST['switch']);
		$this->display();
	}
	/**
	 * @Title: lookupGetUser
	 * @Description: 按照用户选中人员
	 * @author liminggang
	 * @date 2014-9-18 下午2:29:37
	 * @throws
	 */
	public function lookupGetUser(){
		//增加封装编号
		$fengzhuangbianhao = $_REQUEST['fengzhuangbianhao'];
		$this->assign("fengzhuangbianhao",$fengzhuangbianhao);
		
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
		$param['rel']="lookupGetUserProcess";
		$param['url']="__URL__/lookupgetUser/jump/1/deptid/#id#/parentid/#parentid#/companyid/#companyid#";
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
				$deparr=array_keys($MisSystemDepartmentList);
				if($MisSystemDepartmentList){
					$where['user_dept_duty.deptid']=array("in",$deparr);
				}
				$MisSystemDutyModel=D("MisSystemDuty");
				$dutyMap=array();
				$dutyMap['name']=array('like','%'.$keyword.'%');
				$dutyMap['status']=1;
				$MisSystemDutyList=$MisSystemDutyModel->where($dutyMap)->getField("id,name");
				if($MisSystemDutyList){
					$where['user_dept_duty.dutyid']=array("in",array_keys($MisSystemDutyList));
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
			$this->display('lookupGetUserList');
		} else {
			$this->display("lookupGetUser");
		}
	}
	/**
	 * @Title: lookupGetRoleGroup
	 * @Description: 按角色
	 * @author liminggang
	 * @date 2014-9-18 下午2:31:24
	 * @throws
	 */
	public function lookupGetRoleGroup(){
		//查询获取角色
		$map = array();
		$searchby = $_POST["searchby"];
		$keyword= $_POST["keyword"];
		if($keyword){
			$map[$searchby] = array('like','%'.$keyword.'%');
			$this->assign('keyword',$keyword);
			$this->assign('searchby',$searchby);
		}
		$searchby=array(
				array("id" =>"name","val"=>"按角色名称"),
		);
		$this->assign("searchbylist",$searchby);
		$this->assign("switch",$_REQUEST['switch']);
		$map['catgory'] = array('lt',5);
		$this->_list("Rolegroup", $map);
	
		$this->display();
	}
	/**
	 * @Title: lookupGetDeptRole
	 * @Description: todo(部门角色)   
	 * @author 黎明刚
	 * @date 2014年11月29日 下午3:55:15 
	 * @throws
	 */
	public function lookupGetDeptRole(){
		//查询获取角色
		$map = array();
		$searchby = $_POST["searchby"];
		$keyword= $_POST["keyword"];
		if($keyword){
			$map[$searchby] = array('like','%'.$keyword.'%');
			$this->assign('keyword',$keyword);
			$this->assign('searchby',$searchby);
		}
		$searchby=array(
				array("id" =>"name","val"=>"按组织角色"),
		);
		$this->assign("searchbylist",$searchby);
		$this->assign("switch",$_REQUEST['switch']);
		$map['status'] = 1;
		$map['catgory'] = 5;
		$this->_list("Rolegroup", $map);
		
		$this->display();
	}
	/**
	 * @Title: lookupInsert 
	 * @Description: 进行 process_relation 流程节点插入，和流程节点审核人信息的插入   
	 * @author liminggang 
	 * @date 2014-9-18 下午2:51:49 
	 * @throws
	 */
	public function lookupInsert(){
		//获取流程节点名称
		$relaname=$_POST['process_relation_name'];
		//是否并行
		$parallel = $_POST['parallel']?$_POST['parallel']:0;
		//获取批次和条件类型
		$userobjid =implode(";",$_POST['userobjid']);
		$userobjidname =implode(";",$_POST['userobjidname']);
		//获取条件类型ID
		$userobj = implode(";",$_POST['userobj']);
		$userobjname = implode(";",$_POST['userobjname']);
		//获取批次条件
		$rulename = implode(";",$_POST['rulename']);
		$rule = implode(";",$_POST['rule']);
		//获取批次信息
		$bactch = implode(";",$_POST['bactch']);
		//获取批次加密信息
		$rulesinfo = implode(";",$_POST['rulesinfo']);
		
		$date=array(
				'time'=>time(),
				'name'=>$relaname,
				'parallel'=>$parallel,
				'userobjid'=>$userobjid,
				'userobjidname'=>$userobjidname,
				'userobj'=>$userobj,
				'userobjname'=>$userobjname,
				'rule'=>$rule,
				'rulename'=>$rulename,
				'bactch'=>$bactch,
				'tempname'=>$_POST['tempname'],
				'rulesinfo'=>$rulesinfo,
		);
		if($userobj){
			$this->success("添加成功",'',json_encode($date));
		}else{
			$this->error("请添加条件");
		}
		
	}
}