<?php
//Version 1.0
/** 
 * @Title: MisPerformancePlanPutAction 
 * @Package package_name
 * @Description: todo(绩效实施管理) 
 * @author 杨东 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-8-5 上午10:14:45 
 * @version V1.0 
*/ 
class MisPerformancePlanPutAction extends MisPerformancePlanParentsAction {
	
	/**
	 * @Description: _filter(首页列表显示条件)
	 * @param unknown_type $map
	 * @author laicaixia
	 * @date 2013-8-15 下午3:59:58
	 */
	public function _filter(&$map){
		$map['ostatus'] = array(1,2,3,'or');
	}
	
	/**
	 *  @Description: index(进入首页)
	 *  @author laicaixia
	 *  @date 2013-8-21 下午5:33:38
	 */
	public function index() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$this->znodes();
		//取出(左边考核计划等于右边相应内容)
		$map['ostatus']=1;//已发布计划
		$planid = $_REQUEST['id'];
		if($planid==-1){
			$map['ostatus']=1;//已发布计划
		}else if($planid==-2){
			$map['ostatus']=2;//执行中计划
		}else if($planid==-3){
			$map['ostatus']=3;//暂停状态的计划
		}
		else if($planid==-4){ //计划状态为已发布 执行的数据
			$map['ostatus']=array('in','2,1');
		}else if($_REQUEST['id']){
			$map['id'] = $_REQUEST['id'];
		}
		$this->assign("id",$planid);
		$name = $this->getActionName();
		if (! empty ( $name )) {
			$qx_name=$name;
			if(substr($name, -4)=="View"){
				$qx_name = substr($name,0, -4);
			}
			//验证浏览及权限
			if( !isset($_SESSION['a']) ){
				$map=D('User')->getAccessfilter($qx_name,$map);	
			}
			$this->_list ( $name, $map );
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
		if ($_REQUEST['jump']) {
			$this->display('unitlist');
		} else {
			$this->display();
		}
		return;
	}
	/**
	 *  @Description: znodes(构造树形结构)
	 *  @author laicaixia
	 *  @date 2013-8-1 下午3:33:38
	 */
	private function znodes(){
// 		$mppModel = D("MisPerformancePlan");//考核计划表
// 		$mpaList=$mppModel->where('status = 1 and ostatus = 1 or ostatus = 2')->order('id desc')->select();
// 		$mpppParam['rel']="MisPerformancePlanPutRel";
// 		$mpppParam['url']="__URL__/index/jump/1/id/#id#";
		$mpppTreemiso=array(
					array(
						'id'=>0,
						'pId'=>0,
						'name'=>'计划状态',
						'title'=>'计划状态',
						'rel'=>'MisPerformancePlanPutRel',
						'target'=>'ajax',
						'url'=>'__URL__/index/jump/1/id/-4',
						'open'=>true
					),
					array(
						'id'=>-1,
						'pId'=>0,
						'name'=>'发布',
						'title'=>'发布',
						'rel'=>'MisPerformancePlanPutRel',
						'target'=>'ajax',
						'url'=>'__URL__/index/jump/1/id/-1',
						'open'=>true
					),
					array(
						'id'=>-2,
						'pId'=>0,
						'name'=>'执行',
						'title'=>'执行',
						'rel'=>'MisPerformancePlanPutRel',
						'target'=>'ajax',
						'url'=>'__URL__/index/jump/1/id/-2',
						'open'=>true
					),
				array(
						'id'=>-3,
						'pId'=>0,
						'name'=>'暂停',
						'title'=>'暂停',
						'rel'=>'MisPerformancePlanPutRel',
						'target'=>'ajax',
						'url'=>'__URL__/index/jump/1/id/-3',
						'open'=>true
				),
				);
// 		foreach($mpaList as $key=>$val){
// 			$mpaList[$key]['parentid'] = -$val['ostatus'];
// 		}		
// 		$mpppTreearr = $this->getTree($mpaList,$mpppParam,$mpppTreemiso);
		$mpppTreearr=json_encode($mpppTreemiso);
		$this->assign("mpppTreearr",$mpppTreearr);
	}
	
	public function _after_edit($vo){
		$vo['setscoretype'] = explode(",", $vo['setscoretype']);//评分人类型
		$vo['inusersqz'] = unserialize($vo['inusersqz']);//评分人类型对应权重
		$vo['inuserstype'] = unserialize($vo['inuserstype']);//评分人类型对应评分人
		foreach ($vo['inuserstype'] as $key => $value) {
			$vo['inuserstype'][$key] = explode(",", $value);
		}
		if($vo['inusers']){
		$vo['inusers'] = explode(",", $vo['inusers']);//评分人
		}
		// 考核人
		// 		$uv = D("UserView");
		// 		$uvmap['id'] = array('in',$vo['inusers']);
		// 		$inuserslist = $uv->where($uvmap)->select();
		// 		$this->assign("inuserslist",$inuserslist);
		// 考核人权重
		$inusersqz = unserialize($vo['inusersqz']);
		$this->assign("inusersqz",$inusersqz);
		// 考核人负责指标
		$inuserskpi = unserialize($vo['inuserskpi']);
		foreach ($inuserskpi as $k => $v) {
			$inuserskpi[$k] = explode(",", $v);
		}
		$this->assign("inuserskpi",$inuserskpi);
		return $vo;
	}
	
	/**
	 * @Title: stopAndUpdatePlan 
	 * @Description: todo(ajax 请求停止计划)   
	 * @author renling 
	 * @date 2013-8-22 下午5:55:25 
	 * @throws
	 */
	public function stopAndUpdatePlan(){
		$id=$_POST['id'];
		 $MisPerformancePlanModel=D('MisPerformancePlan');
		 $result=$MisPerformancePlanModel->where(" id=".$id)->setField("ostatus",3);
		 $MisPerformancePlanModel->commit();
		 echo $result;
	}
	/**
	 * @Title: lookupmanage
	 * @Description: todo(设置被考核人)
	 * @author lcx
	 */
	public function lookupmanage(){
		//初始化信息
		$putid = $_REQUEST['putid'];
		$this->assign('putid', $putid);
		$model= M('mis_system_department');
		$deptlist = $model->where("status=1")->order("id asc")->select();
		$param['rel']="MisPerformancePlanPutList";
		$param['url']="__URL__/lookupmanage/jump/1/deptid/#id#/putid/".$putid;
		$typeTree = $this->getTree($deptlist,$param);
		$this->assign('tree',$typeTree);
		//检索功能
		$map = array();
		$searchby = str_replace("-", ".", $_POST["searchby"]);
		$keyword=$this->escapeChar($_POST["keyword"]);
		$searchtype = $_POST['searchtype'];
		if($_POST["keyword"]){
			if($searchby =="all"){
				$where['mis_hr_personnel_person_info.name']=array('like','%'.$keyword.'%');
				$where['mis_hr_personnel_person_info.orderno']=array('like','%'.$keyword.'%');
				$where['mis_system_department.name']=array('like','%'.$keyword.'%');
				$where['duty.name']=array('like','%'.$keyword.'%');
				$where['_logic']='OR';
				$map['_complex'] = $where;
			}else{
				$map[$searchby] = ($searchtype==2)  ? array('like','%'.$keyword.'%'):$keyword;
			}
			$this->assign('keyword',$keyword);
			$searchby = str_replace(".", "-", $_POST["searchby"]);
			$this->assign('searchby',$searchby);
			$this->assign('searchtype',$searchtype);
		}
		$searchby=array(
				array("id" =>"all","val"=>"全部"),
				array("id" =>"mis_hr_personnel_person_info-name","val"=>"员工姓名"),
				array("id" =>"mis_hr_personnel_person_info-orderno","val"=>"员工编号"),
				array("id" =>"mis_system_department-name","val"=>"员工部门"),
				array("id" =>"duty-name","val"=>"员工职级"),
		);
		$searchtype=array(
				array("id" =>"2","val"=>"模糊查找"),
				array("id" =>"1","val"=>"精确查找")
		);
		$this->assign("searchbylist",$searchby);
		$this->assign("searchtypelist",$searchtype);
		//
		$map['mis_hr_personnel_person_info.status']=1;
		$map['mis_hr_personnel_person_info.workstatus']=1;
		if($_REQUEST['deptid']) $map['mis_hr_personnel_person_info.deptid'] = $_REQUEST['deptid'];
		if($_GET['type']){
			$pppmodel = D("MisPerformancePlanPut");
			$byusers = $pppmodel->where('id='.$putid)->getField('byusers');
			$mv = D("MisHrBasicEmployeeView");
			$mvmap['id'] = array('in',$byusers);
			$byuserlist = $mv->where($mvmap)->select();
			$this->assign("byuserlist",$byuserlist);
		}
		$this->_list('MisHrBasicEmployeeView',$map);
		$this->assign('deptid',$_REQUEST['deptid']);
		if ($_REQUEST['jump']) {
			$this->display('lookupmanagelist');
		} else {
			$this->display("lookupmanage");
		}
	}
	/**
	 * @Title: lookupsetbyusers
	 * @Description: todo(设置保存被考核人)   
	 * @author 杨东 
	 * @date 2013-8-9 上午9:49:38 
	 * @throws 
	*/  
	public function lookupsetbyusers(){
		$id = $_POST['putid'];
		$byusers = implode(",", $_POST['byusers']);
		$pppmodel = D("MisPerformancePlanPut");
		$list = $pppmodel->where('id='.$id)->setField('byusers',$byusers);
		if (false !== $list) {
			$this->success ( L('_SUCCESS_'));
		} else {
			$this->error ( L('_ERROR_') );
		}
	}
	/**
	 *
	 * @Title: lookupmanageplan
	 * @Description: todo(设置考核人)
	 * @author lcx
	 * @throws
	 */
	public function lookupmanageplan(){ 
// 	echo 111;exit;
		$setscoretype=$_GET['setscoretype'];
		$this->assign("setscoretype",$setscoretype);
		$planid = $_REQUEST['planid'];
		$this->assign('planid', $planid);
		$sin = $_REQUEST['sin'];
		$this->assign('sin', $sin);
		$model= M('mis_system_department');
		$deptlist = $model->where("status=1")->order("id asc")->select();
		$param['rel']="lookupplanBox";
		$param['url']="__URL__/lookupmanageplan/jump/1/deptid/#id#/parentid/#parentid#/planid/".$planid."/setscoretype/".$setscoretype;
		$typeTree = $this->getTree($deptlist,$param);
		$this->assign('tree',$typeTree);
		$map = array();
		$searchby = str_replace("-", ".", $_POST["searchby"]);
		$keyword=$this->escapeChar($_POST["keyword"]);
		$searchtype = $_POST['searchtype'];
		if($_POST["keyword"]){
			if($searchby =="all"){
				$where['user.name']=array('like','%'.$keyword.'%');
				$where['mis_hr_personnel_person_info.orderno']=array('like','%'.$keyword.'%');
				$where['mis_system_department.name']=array('like','%'.$keyword.'%');
				$where['duty.name']=array('like','%'.$keyword.'%');
				$where['_logic']='OR';
				$map['_complex'] = $where;
			}else{
				$map[$searchby] = ($searchtype==2)  ? array('like','%'.$keyword.'%'):$keyword;
			}
			$this->assign('keyword',$keyword);
			$searchby = str_replace(".", "-", $_POST["searchby"]);
			$this->assign('searchby',$searchby);
			$this->assign('searchtype',$searchtype);
		}
		$searchby=array(
			array("id" =>"all","val"=>"全部"),
			array("id" =>"user-name","val"=>"员工姓名"),
			array("id" =>"mis_hr_personnel_person_info-orderno","val"=>"员工编号"),
			array("id" =>"mis_system_department-name","val"=>"员工部门"),
			array("id" =>"duty-name","val"=>"员工职级"),
		);
		$searchtype=array(
			array("id" =>"2","val"=>"模糊查找"),
			array("id" =>"1","val"=>"精确查找")
		);
		$this->assign("searchbylist",$searchby);
		$this->assign("searchtypelist",$searchtype);
		//取出考核人相关信息
		$pppmodel = D("MisPerformancePlanPut");//绩效实施管理表
		$vos = $pppmodel->where('id='.$planid)->field("inusers,inusersqz,inuserstype,inuserskpi,seusersqz,seuserskpi")->find();
		$this->assign("vos",$vos);
		//考核人
		$md = D("MisHrPersonnelPersonInfoView");
		$inuserstype=unserialize($vos['inuserstype']);
		if($setscoretype){
			$inusersList=$inuserstype[$setscoretype];
		}
		//所有评分人
		$oldinusersList=explode(',', $vos['inusers']);
		//当前选择评分类型中的评分人
		$newinusersList =explode(',', $inusersList);
		//找到两个数组不同之处
		$diffList = array_merge(array_diff($newinusersList,$oldinusersList),array_diff($oldinusersList,$newinusersList));
		//排除已选评分人
		if($diffList){
			$map['id']=array('not in',$diffList);
		}
		$mdmap['id'] = array('in',$inusersList);
		$inusersList = $md->where($mdmap)->select();
		$this->assign("inuserslist",$inusersList);
		//权重
		$inusersqz = unserialize($vos['inusersqz']);
		$this->assign("inusersqz",$inusersqz);
		//指标
		$inuserskpi = unserialize($vos['inuserskpi']);
		$this->assign("inuserskpi",$inuserskpi);
		$map['working']=1;
		$map['user.status']=1;
		$deptid		= $_REQUEST['deptid'];
		$parentid	= $_REQUEST['parentid'];
		if ($deptid && $parentid) {
			$deptlist =array_unique(array_filter (explode(",",$this->findAlldept($deptlist,$deptid))));
			$map['user.dept_id'] = array(' in ',$deptlist);
		}
		$this->_list('MisHrPersonnelPersonInfoView',$map);
		$this->assign('deptid',$deptid);
		$this->assign('parentid',$parentid);
		if ($_REQUEST['jump']) {
			$this->display('lookupmanageplanlist');
		} else {
			$this->display("lookupmanageplan");
		}
	}
	
	/**
	 * @Description: lookupsetInusers(选择考核人)   
	 * @author laicaixia 
	 * @date 2013-8-6 下午5:38:50 
	 * @update yangdong
	*/  
	public function lookupsetInusers(){
		$pppmodel = D("MisPerformancePlanPut");//绩效实施管理表
		$id = $_POST['planid'];//绩效实施ID
		$ppdmodel = D("MisPerformancePlanDetail");//绩效计划明细
		$pdkpilist = $ppdmodel->where('planid='.$id)->getField('id',true);//查询当前计划的所有对应指标的明细ID
		$pdkpistr = implode(",", $pdkpilist);//将明细ID用逗号分隔
		$inuserskpi = $_POST['inuserskpi'];//考核人对应指标
		$setscoretype = $_POST['setscoretype']; //评分人类型
		$inusers = implode(",",$_POST['inusers']);
		$inuserstype=$pppmodel->where(" id=".$id)->getField('inuserstype');
		$inuserstypearr = array();//评分人类型对应评分人
		if($inuserstype){
			$inuserstypearr = unserialize($inuserstype);
		}
		$inuserstypearr[$setscoretype] = $inusers;
		$inusersarr = array();//评分人
		foreach ($inuserstypearr as $key=>$val){
			$inusersarr[] = $val;
		}
		//逗号分隔评分人
 		$inusersarray=implode(",", $inusersarr);
 		//将评分人分隔成数组形式
 		$inusersarray=explode(",", $inusersarray);
 		$inuserskpiarr = array();//考核人对应指标数组
		foreach ($inusersarray as $k => $v) {
			// 			$inusersqz += $_POST['inusersqz'][$v]; 权重
			// 			$inusersqzarr[$v] = $_POST['inusersqz'][$v]; 权重
			$inuserskpiarr[$v]= $inuserskpi[$k]?$inuserskpi[$k]:$pdkpistr; //指标
		}
		//将（考核 人、权重、指标、本人权重）插入数据库
		$data = array(
			'inusers' => implode(",", $inusersarr),//考核 人(逗号分隔)
			'inuserskpi' => serialize($inuserskpiarr),//考核人对应指标数组
			'inuserstype'=>serialize($inuserstypearr), //评分人类型对应评分人
		);
		$listIn = $pppmodel->where('id='.$id)->save($data);
		if (false !== $listIn) {
			$this->success ( L('_SUCCESS_'));
		} else {
			$this->error ( L('_ERROR_') );
		}
	}
	/**
	 * @Title: lookupmanageplanperson 
	 * @Description: todo(设置考核人)   
	 * @author renling 
	 * @date 2013-8-27 上午10:44:21 
	 * @throws
	 */
   public function	lookupmanageplanperson(){
   	$id=$_REQUEST['planid'];
   	$this->assign("planid",$id);
   	$mppModel = D("MisPerformancePlan");//考核计划表
   	$MisPerformanceTypeModel = D("MisPerformanceType");//考核分类表
   	$mpaList=$mppModel->where('status = 1 and  id='.$id)->find();
   	$mpppParam['rel']="misperformanceplanput_dept";
   	$mpppParam['url']="__URL__/lookupmanageplanperson/jump/1/setscoretype/#type#/planid/".$id;
   	$mpppTreemiso[]=array(
   					'id'=>0,
   					'pId'=>0,
   					'rel'=>"misperformanceplanput_dept",
   					'url'=>"__URL__/lookupmanageplanperson/jump/1",
   					'name'=>'评分人类别',
   					'title'=>'评分人类别',
   					'target'=>'ajax',
   					'open'=>true
   			);
   	$setScoreList=explode(',',$mpaList['setscoretype']);
   	$typeMap['id']=array('in',$setScoreList);
   	$typeMap['status']=1;
   	$MisPerformanceTypeList=$MisPerformanceTypeModel->where($typeMap)->select();
   	foreach($MisPerformanceTypeList as $key=>$val){
   		$MisPerformanceTypeList[$key]['type']=$val['id'];
   		$MisPerformanceTypeList[$key]['parentid'] = 0;
  	}
    $this->assign('setscoretype',$MisPerformanceTypeList[0]['type']) ;
    $mpppTreearr = $this->getTree($MisPerformanceTypeList,$mpppParam,$mpppTreemiso);
   	$this->assign("tree",$mpppTreearr);
   	if($_GET['setscoretype']){//评分人类别
   		$setscoretypePerson=$_GET['setscoretype'];
   		$this->assign("setscoretype",$_GET['setscoretype']);
   	}else{ //默认第一个评分人类型中的评分人
   		$setscoretypePerson=$MisPerformanceTypeList[0]['type'];
   	}
   		$inusersList=unserialize($mpaList['inuserstype']);
   		$inusersList=explode(',',$inusersList[$setscoretypePerson]);
   		$UserViewModel = D("MisHrPersonnelPersonInfoView");
   		$usermap['id']=array("in",$inusersList);
   		$inusersListarr = $UserViewModel->where($usermap)->select();
   		$this->assign("inusersList",$inusersListarr);
   	if($_GET['jump']){
   		$this->display('lookupmanageplanpersonlist');
   	}else{
   		$this->display();
   		}
   	}
	/** (保存)
	 * 杨东
	 * @see CommonAction::update()
	 */
	public function update(){
		$list = $this->inusersKpiSave();
		if (false !== $list) {
			$this->success ( L('_SUCCESS_'));
		} else {
			$this->error ( L('_ERROR_') );
		}
	}
	/**
	 * @Title: inusersKpiSave
	 * @Description: todo(保存考核人涉及的KPI值) 
	 * @return 执行结果（成功与否）  
	 * @author 杨东 
	 * @date 2013-8-9 上午9:47:32 
	 * @throws 
	*/  
	private function inusersKpiSave(){
		$ppmodel = D("MisPerformancePlan");
		$id = $_POST['id'];
		$inusers = $ppmodel->where("id=".$id)->getField('inusers');
		$inusersList = explode(",", $inusers);
		$ppdmodel = D("MisPerformancePlanDetail");//
		$ppdidlist = $ppdmodel->where('planid='.$id)->getField('id',true);//查询当前计划的所有对应指标的明细ID
		$inuserskpi = array();
		foreach ($inusersList as $k => $v) {
			$kpi = array();
			foreach ($ppdidlist as $k1 => $v1) {
				if($_POST["inuserkpi"][$v1.$v]) $kpi[] = $_POST["inuserkpi"][$v1.$v];
			}
			$inuserskpi[$v] = implode(",", $kpi);
		}
		$data = array();
		$data["inuserskpi"] = serialize($inuserskpi);
		if($_POST['ostatus']) $data["ostatus"] = $_POST['ostatus'];
		$list = $ppmodel->where('id='.$id)->save($data);
		return $list;
	}
	/**
	 * @Title: startAndUpdate
	 * @Description: todo(启动并保存)   
	 * @author 杨东 
	 * @date 2013-8-9 上午9:51:00 
	 * @throws 
	*/  
	public function startAndUpdate(){
		$ppmodel = D("MisPerformancePlan"); //计划模型
		$prmodel = D("MisPerformanceReport");
		$ostatus = $_POST['ostatus'];
		unset($_POST['ostatus']);
		$_POST['ostatus'] = 2;
		$list = $this->inusersKpiSave();
		unset($_POST['ostatus']);
		if($ostatus == 1){
			if($list == false){
				$this->error ( L('_ERROR_') );
			}
			$id = $_POST['id'];
			$vo = $ppmodel->where("id=".$id)->field('byusers,inusers,inusersqz,inuserskpi')->find();
			$byusers = explode(",", $vo['byusers']);//被考核人
			$inusers = explode(",", $vo['inusers']);//考核人
			$inusersqz = unserialize($vo['inusersqz']);//考核人指标
			$inuserskpi = unserialize($vo['inuserskpi']);//考核人打分
			$data['planid'] = $id;
			foreach ($byusers as $k => $v) {
				$data['byuser'] = $v;
				foreach ($inusers as $k1 => $v1) {
					$data['inuser'] = $v1;
					$data['inuserqz'] = $inusersqz[$v1];
					$data['inuserskpi'] = $inuserskpi[$v1];
					$data['createtime'] = time();
					$data['createid'] = $_SESSION[C('USER_AUTH_KEY')];
					$list = $prmodel->data($data)->add();
					if($list == false){
						$this->error ( L('_ERROR_') );
					}
				}
			}
		}
		if($ostatus==3){ //暂停之后再保存启动
			if($list == false){
				$this->error ( L('_ERROR_') );
			}
			$id = $_POST['id'];
			$vo = $ppmodel->where("id=".$id)->field('byusers,inusers,inusersqz,inuserskpi')->find();
			$byusers = explode(",", $vo['byusers']);//被考核人
			$inusers = explode(",", $vo['inusers']);//考核人
			$inusersqz = unserialize($vo['inusersqz']);//考核人指标
			$inuserskpi = unserialize($vo['inuserskpi']);//考核人打分
			$MisPerformanceReportList=$prmodel->where("planid=".$id)->select();
			foreach ($MisPerformanceReportList as $key => $value) {
				foreach ($byusers as $k => $v) {
					foreach ($inusers as $k1 => $v1) {
						if($value['byuser']==$v && $value['inuser']==$v1){
							echo $value['inuser']."<br/>";
						}
					}
				}
			}
		 echo 'end';exit;	
// 			$data['planid'] = $id;
// 			foreach ($byusers as $k => $v) {
// 				$data['byuser'] = $v;
// 				foreach ($inusers as $k1 => $v1) {
// 					$data['inuser'] = $v1;
// 					$data['inuserqz'] = $inusersqz[$v1];
// 					$data['inuserskpi'] = $inuserskpi[$v1];
// 					$data['createtime'] = time();
// 					$data['createid'] = $_SESSION[C('USER_AUTH_KEY')];
// 					$list = $prmodel->data($data)->add();
// 					if($list == false){
// 						$this->error ( L('_ERROR_') );
// 					}
// 				}
// 			}
			
		}
		if (false !== $list) {
			$this->success ( L('_SUCCESS_'));
		} else {
			$this->error ( L('_ERROR_') );
		}
	}
}
?>