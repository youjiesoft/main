<?php
/**
 * @Title: MisPerformancePlanAction
 * @Package package_name
 * @Description: todo(绩效计划)
 * @author laicaixia
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-8-2 上午11:27:51
 * @version V1.0
 */
class MisPerformancePlanAction extends CommonAction {

	public function index() {
		//计划状态树
		$this->getPlanTree();
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		$map['ostatus'] = 0;
		$this->assign("type",1);
		if($_REQUEST['type']){
			$map['ostatus'] =$_REQUEST['type']-1;
			$this->assign("type",$_REQUEST['type']);
		}
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
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
		//searchby搜索扩展
		// 		$searchby = $scdmodel->getDetail($name,true);
		// 		if ($searchby && $detailList) {
		// 			$searchbylist=array();
		// 			foreach( $detailList as $k=>$v ){
		// 				if(isset($searchby[$v['name']])){
		// 					$arr['id']= $searchby[$v['name']]['field'];
		// 					$arr['val']= $v['showname'];
		// 					array_push($searchbylist,$arr);
		// 				}
		// 			}
		// 			$this->assign("searchbylist",$searchbylist);
		// 		}
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
		if ($_REQUEST['type']) {
			$this->display('deptindex');
		} else {
			$this->display();
		}
		return;
	}
	public function getPlanTree(){
		//0起草1发布2执行3暂停4结束
		$tree = array(); // 树初始化
		$tree[] = array(
				'id' => 1,
				'pId' => 0,
				'name' => '计划状态',
				'title' => '计划状态',
				'open' => true
		);
		$tree[] = array(
				'id' => 2,
				'pId' => 1,
				'name' => '起草',
				'title' => '起草',
				'rel' => "misperformanceplan",
				'target' => 'ajax',
				'url' => "__URL__/index/type/1",
				'open' => true
		);
		$tree[] = array(
				'id' => 3,
				'pId' => 1,
				'name' => '发布',
				'title' => '发布',
				'rel' => "misperformanceplan",
				'target' => 'ajax',
				'url' => "__URL__/index/type/2",
				'open' => true
		);
		$tree[] = array(
				'id' => 4,
				'pId' => 1,
				'name' => '执行',
				'title' => '执行',
				'rel' => "misperformanceplan",
				'target' => 'ajax',
				'url' => "__URL__/index/type/3",
				'open' => true
		);
		$tree[] = array(
				'id' => 4,
				'pId' => 1,
				'name' => '暂停',
				'title' => '暂停',
				'rel' => "misperformanceplan",
				'target' => 'ajax',
				'url' => "__URL__/index/type/4",
				'open' => true
		);
		$tree[] = array(
				'id' => 5,
				'pId' => 1,
				'name' => '结束',
				'title' => '结束',
				'rel' => "misperformanceplan",
				'target' => 'ajax',
				'url' => "__URL__/index/type/5",
				'open' => true
		);
		$this->assign("tree",json_encode($tree));
	}
	/**
	 * @Description: _before_add(进入新增)
	 * @author laicaixia
	 * @date 2013-8-2 下午2:12:10
	 */
	public function _before_add(){
		//自动生成绩效计划编码
		$scnmodel = D('SystemConfigNumber');
		$orderno = $scnmodel->GetRulesNO('mis_performance_plan');
		$this->assign("orderno",$orderno);
		//建档日期
		$this->assign('setdate',time());
		//薪资关联期间
		$this->assign('salarydate',time());
		//查询当前登录用户信息
		$uid=$_SESSION[C('USER_AUTH_KEY')];
		$employeid=getFieldBy($uid, 'id', 'employeid', 'user');
		$this->assign("employeid",$employeid);
		//调用公共函数
		$this->common();
	}
	public function add(){
		switch ($_REQUEST['step']) {
			case 1:
				//考核分类
				$this->addkpitype();
				break;
			case 2:
				//考核指标
				$this->addkpi();
				break;
			default:
				$this->loadadd();
				$this->display();
				break;
		}
	}
	private function loadadd(){
		$modelname = $this->getActionName();
		$this->getSystemConfigDetail($modelname);
		$model = D($modelname);
		$qx_name = $modelname;
		if(substr($modelname, -4)=="View"){
			$qx_name = substr($modelname,0, -4);
		}
		if( !isset($_SESSION['a']) ){
			if( $_SESSION[strtolower($qx_name.'_index')]==2 ){////判断部门及子部门权限
				$map["createid"]=array("in",$_SESSION['user_dep_all_child']);
			}else if($_SESSION[strtolower($qx_name.'_index')]==3){//判断部门权限
				$map["createid"]=array("in",$_SESSION['user_dep_all_self']);
			}else if($_SESSION[strtolower($qx_name.'_index')]==4){//判断个人权限
				$map["createid"]=$_SESSION[C('USER_AUTH_KEY')];
			}
		}
		$map['status']=1;
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		// 上一条数据ID
		$updataid = $model->where($map)->order('id desc')->getField('id');
		$this->assign("updataid",$updataid);
		$mrdmodel = D('MisRuntimeData');
		$data = $mrdmodel->getRuntimeCache($modelname,'add');
		$this->assign("vo",$data);
		//此处添加_after_add方法，方便在方法中，判断跳转
		$module2 = A($modelname);
		if (method_exists($module2,"_after_add")) {
			call_user_func(array(&$module2,"_after_add"),$data);
		}
	}
	public  function  edit(){
		switch ($_REQUEST['step']) {
			case 1:
				//考核分类
				$this->editkpitype();
				break;
			case 2:
				//考核指标
				$this->editkpi();
				break;
			default:
				$this->loadedit();
				$this->display();
				break;
		}
	}
	private function loadedit(){
		$id=$_GET['id'];
		//绩效计划模型
		$MisPerformancePlanModel=D('MisPerformancePlan');
		$map['id']=$id;
		if ($_SESSION["a"] != 1){
			$map['status'] = 1;
		}
		$MisPerformancePlanList=$MisPerformancePlanModel->where($map)->find();
		if(empty($MisPerformancePlanList)){
			$this->display ("Public:404");
			exit;
		}
		$volist=unserialize($MisPerformancePlanList['detailserlize']);
		$this->assign('list',$volist);
		$setScoreList=unserialize($MisPerformancePlanList['inusersqz']);
		$this->assign("setScoreList",$setScoreList);
		$this->assign("vo",$MisPerformancePlanList);
	}
	/**
	 * @Description: _before_edit(进入修改)
	 * @author laicaixia
	 * @date 2013-8-2 下午2:36:41
	 */
	public function _before_edit(){
		//调用公共函数
		$this->common();
	}
	/**
	 * @Description: common(公共函数)
	 * @author laicaixia
	 * @date 2013-8-5 上午9:41:20
	 */
	private function common(){
		// 		$mptList = $typeModel->where('status = 1 and type = 3')->getField('id');
		// 		$this->assign('mptList',$mptList);
		//取出关联模板
		$tempModel = D('MisPerformanceTemplate');
		$tempList  = $tempModel->where('status = 1')->getField('id,name');
		$this->assign('tempList',$tempList);
		//取出绩效等级
		$typeModel = D('MisPerformanceType');
		$mplList = $typeModel->where('status = 1 and type = 1')->getField('id,name');
		$this->assign('mplList',$mplList);
	}
	/**
	 * @Title: detail
	 * @Description: todo(加载模板页面)
	 * @author renling
	 * @date 2013-8-10 上午10:19:00
	 * @throws
	 */
	public function detail(){
		//取出模板信息
		$templateModel = D('MisPerformanceTemplateDetail');
		$tmap['status'] = 1;
		$tmap['tempid'] = $_REQUEST['id'];
		$model = D('MisPerformanceTemplateDetail');
		$list = $model->where($tmap)->select();
		$volist = array();
		$sumscore=0;
		foreach ($list as $k => $v) {
			$sumscore+=$v['kpiscore'];
			if($volist[$v['kpitypeid']]){
				$volist[$v['kpitypeid']]['kpi'][] = array(
						'id' => $v['id'],
						'kpiid' => $v['kpiid'],
						'kpiscore' => $v['kpiscore'],
				);
			} else {
				$volist[$v['kpitypeid']] = array(
						'kpitypeid' => $v['kpitypeid'],
						'kpitypeqz' => $v['kpitypeqz']
				);
				$volist[$v['kpitypeid']]['kpi'][] = array(
						'id' => $v['id'],
						'kpiid' => $v['kpiid'],
						'kpiscore' => $v['kpiscore'],
				);
			}
		}
		$this->assign('list',$volist);
		$this->display();
	}
	public function editdetail(){
		//取出模板信息
		$templateModel = D('MisPerformanceTemplateDetail');
		$tmap['status'] = 1;
		$tmap['tempid'] = $_REQUEST['id'];
		$model = D('MisPerformanceTemplateDetail');
		$list = $model->where($tmap)->select();
		$volist = array();
		$sumscore=0;
		foreach ($list as $k => $v) {
			$sumscore+=$v['kpiscore'];
			if($volist[$v['kpitypeid']]){
				$volist[$v['kpitypeid']]['kpi'][] = array(
						'id' => $v['id'],
						'kpiid' => $v['kpiid'],
						'kpiscore' => $v['kpiscore'],
				);
			} else {
				$volist[$v['kpitypeid']] = array(
						'kpitypeid' => $v['kpitypeid'],
						'kpitypeqz' => $v['kpitypeqz']
				);
				$volist[$v['kpitypeid']]['kpi'][] = array(
						'id' => $v['id'],
						'kpiid' => $v['kpiid'],
						'kpiscore' => $v['kpiscore'],
				);
			}
		}
		$this->assign('list',$volist);
		$this->assign("ostatus",0);
		$this->display();
	}
	/**
	 * @Description: lookup(关于绩效周期)
	 * @author laicaixia
	 * @date 2013-8-5 上午9:41:20
	 */
	public function lookup(){
		//年度、月份
		$this->assign('lookupdate',time());
		$this->display();
	}
	private function addkpitype(){
		$isedit=$_GET['edit'];
		$typeid=$_GET['typeid'];
		$kpitypeqz=$_GET['kpitypeqz'];
		$this->assign('typeid',$typeid);
		$this->assign('kpitypeqz',$kpitypeqz);
		$this->assign('isedit',$isedit);
		//查询等级分类
		$misPerformanceTypeModel = D('MisPerformanceType');
		$map['status'] = 1;
		$map['type'] = 2; //类型为指标
		$list = $misPerformanceTypeModel->where($map)->getField('id,name,remark');
		$this->assign('typelist',$list);
		$this->display('addkpitype');
	}
	private function addkpi(){
		$typeid=$_GET['typeid'];
		$MisPerformanceKpiModel=D('MisPerformanceKpi');
		$kpimap['status']=1;
		$kpimap['typeid']=$typeid;
		$list = $MisPerformanceKpiModel->where($kpimap)->getField('id,code,name,remark');
		$this->assign("kpitypeid",$typeid);
		$this->assign("kpilist",$list);
		$this->display('addkpi');
	}
	private function editkpitype(){
		$isedit=$_GET['edit'];
		$typeid=$_GET['typeid'];
		$kpitypeqz=$_GET['kpitypeqz'];
		$this->assign('typeid',$typeid);
		$this->assign('kpitypeqz',$kpitypeqz);
		$this->assign('isedit',$isedit);
		//查询等级分类
		$misPerformanceTypeModel = D('MisPerformanceType');
		$map['status'] = 1;
		$map['type'] = 2; //类型为指标
		$list = $misPerformanceTypeModel->where($map)->getField('id,name,remark');
		$this->assign('typelist',$list);
		$this->display('editkpitype');
	}
	private function editkpi(){
		$typeid=$_GET['typeid'];
		$MisPerformanceKpiModel=D('MisPerformanceKpi');
		$kpimap['status']=1;
		$kpimap['typeid']=$typeid;
		$list = $MisPerformanceKpiModel->where($kpimap)->getField('id,code,name,remark');
		$this->assign("kpitypeid",$typeid);
		$this->assign("kpilist",$list);
		$this->display('editkpi');
	}
	/**
	 * @Description: lookupmanage(关于建档人)
	 * @author laicaixia
	 * @date 2013-8-5 上午9:41:20
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
				array("id" =>"mis_hr_personnel_person_info-name","val"=>"员工姓名"),
				array("id" =>"orderno","val"=>"员工编号"),
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
	function lookupselecttempmaxscore(){
		$MisPerformanceTemplateModel=D('MisPerformanceTemplate');
		$tempid=$_POST['tempid'];
		$result=$MisPerformanceTemplateModel->where("id=".$tempid)->getField('mostscore',true);
		$this->assign("ostatus",0);
		echo $result[0];
	}
	function  insert(){
		//绩效计划模型
		$MisPerformancePlanModel=D('MisPerformancePlan');
		//绩效计划明细模型
		$MisPerformancePlanDetailModel=D('MisPerformancePlanDetail');
		//指标模型
		$MisPerformanceKpiModel=D('MisPerformanceKpi');
		$MisPerformanceKpiList = $MisPerformanceKpiModel->where($kpimap)->getField('id,typeid');
		$plankpi=$_POST['plan_kpi'];
		$kpitypeid=$_POST['plan_kpitypeid'];
		$kpitypeqz=$_POST['plan_kpitypeqz'];
		$setscoretype=$_POST['setscoretype']; //评分人类型
		$inusersqz=array();
		foreach ($setscoretype as $key=>$val) {
			$inusersqz[$val]= $_POST['inusersqz'.$val];
		}
		$volist = array();
		foreach ($kpitypeid as $key=>$val) {
			$arr = array();
			foreach ($_POST['plan_kpi'.$val] as $key2 => $val2) {
				$arr[] = array(
						'kpiid' => $val2,
						'kpiscore' => $_POST['plan_kpiscore'.$val][$val2],
				);
			}
			$volist[$val] = array(
					'kpitypeid' => $val,
					'kpitypeqz' => $kpitypeqz[$key],
					'kpi' => $arr
			);
		}
		$data['setscoretype']=implode(',',$setscoretype); //逗号分隔评分人类型
		$data['inusersqz']=serialize($inusersqz); //评分人类型对应权重序列化
		$data['name']=$_POST['name'];
		$data['detailserlize']=serialize($volist);  //序列化明细
		$data['cycle']=$_POST['cycle'];
		$data['course']=$_POST['course'];
		$data['tempid']=$_POST['tempid'];
		$data['salarydate']=strtotime($_POST['salarydate']);
		$data['userid']=$_POST['userid'];
		$data['setdate']=strtotime($_POST['setdate']);
		$data['mostscore']=$_POST['mostscore'];
		$data['levelid']=$_POST['levelid'];
		$data['createtime']=time();
		$data['createid']=$_SESSION[C('USER_AUTH_KEY')];
		$data['orderno']=$_POST['orderno'];
		$MisPerformancePlanResult=$MisPerformancePlanModel->data($data)->add();
		$MisPerformancePlanModel->commit();
		if($MisPerformancePlanResult==false){
			$this->error("操作失败！");
		}else{
			$this->success("操作成功！");
		}
	}
	public  function update(){
		//绩效计划模型
		$MisPerformancePlanModel=D('MisPerformancePlan');
		//绩效计划明细模型
		$MisPerformancePlanDetailModel=D('MisPerformancePlanDetail');
		//指标模型
		$MisPerformanceKpiModel=D('MisPerformanceKpi');
		$resultstep=$_POST['resultstep'];
		$id=$_POST['id'];
		if($resultstep=='1'){ //发布
			$MisPerformanceKpiList = $MisPerformanceKpiModel->where($kpimap)->getField('id,typeid');
			$plankpi=$_POST['plan_kpi'];
			$kpitypeid=$_POST['planedit_kpitypeid'];
			$kpitypeqz=$_POST['plan_kpitypeqz'];
			$volist = array();
			foreach ($kpitypeid as $key=>$val) {
				$arr = array();
				foreach ($_POST['plan_kpi'.$val] as $key2 => $val2) {
					$arr[] = array(
							'kpiid' => $val2,
							'kpiscore' => $_POST['plan_kpiscore'.$val][$val2],
					);
				}
				$volist[$val] = array(
						'kpitypeid' => $val,
						'kpitypeqz' => $kpitypeqz[$key],
						'kpi' => $arr
				);
			}
			$setscoretype=$_POST['setscoretype']; //评分人类型
			$data['setscoretype']=implode(',',$setscoretype); //逗号分隔评分人类型
			$inusersqz=array();
			foreach ($setscoretype as $key=>$val) {
				$inusersqz[$val]= $_POST['inusersqz'.$val];
			}
			$data['ostatus']=1;
			$data['inusersqz']=serialize($inusersqz); //评分人类型对应权重序列化
			$data['name']=$_POST['name'];
			$data['detailserlize']=serialize($volist);  //序列化明细
			$data['cycle']=$_POST['cycle'];
			$data['course']=$_POST['course'];
			$data['tempid']=$_POST['tempid'];
			$data['salarydate']=strtotime($_POST['salarydate']);
			$data['userid']=$_POST['userid'];
			$data['setdate']=strtotime($_POST['setdate']);
			$data['mostscore']=$_POST['mostscore'];
			$data['levelid']=$_POST['levelid'];
			$data['createtime']=time();
			$data['createid']=$_SESSION[C('USER_AUTH_KEY')];
			$data['orderno']=$_POST['orderno'];
			$MisPerformancePlanResult=$MisPerformancePlanModel->where("id=".$id)->data($data)->save();
			$MisPerformancePlanModel->commit();
			if($MisPerformancePlanResult==false){
				$this->error("操作失败！");
			}
			$MisPerformancePlanList=$MisPerformancePlanModel->where("id=".$id)->find();
			$detailserlize=unserialize($MisPerformancePlanList['detailserlize']);
			$MisPerformancePlanResult=$MisPerformancePlanModel->where("id=".$id)->setField('ostatus',1); //此条计划状态为发布
			$data['planid']=$id;
			$data['tempid']=$_POST['tempid'];
			$data['createtime']=time();
			$data['createid']=$_SESSION[C('USER_AUTH_KEY')];
			foreach ($detailserlize as $key=>$val){
				$data['kpitypename']=getFieldBy($detailserlize[$key]['kpitypeid'], 'id', 'name', 'mis_performance_type');
				$data['kpitypeqz']=$detailserlize[$key]['kpitypeqz'];
				foreach ($detailserlize[$key]['kpi'] as $k => $v) {
					$data['kpiname']=getFieldBy($v['kpiid'], 'id', 'name', 'mis_performance_kpi');
					$data['kpiscore']=$v['kpiscore'];
					$result=$MisPerformancePlanDetailModel->data($data)->add();
					if(!$result){
						$this->error("操作失败！");
					}
				}
			}
			$this->success("操作成功！");
		}else if($resultstep=='2'){//取消发布
			$saveDate['ostatus']=0;  //此条计划状态为起草
			$saveDate['byusers']=""; //清空被考核人
			$saveDate['inusers']=""; //清空考核人
			$saveDate['inuserstype']=""; //清空评分人类型对应评分人
			$MisPerformancePlanResult=$MisPerformancePlanModel->where("id=".$id)->save($saveDate); 
			$delmap['planid']=$id;
			$result=$MisPerformancePlanDetailModel->where($delmap)->delete();
			if(!$result){
				$this->error("操作失败！");
			}else{
				$this->success("操作成功！");
			}
		}else{ //修改数据
			$MisPerformanceKpiList = $MisPerformanceKpiModel->where($kpimap)->getField('id,typeid');
			$plankpi=$_POST['plan_kpi'];
			$kpitypeid=$_POST['planedit_kpitypeid'];
			$kpitypeqz=$_POST['plan_kpitypeqz'];
			$volist = array();
			foreach ($kpitypeid as $key=>$val) {
				$arr = array();
				foreach ($_POST['plan_kpi'.$val] as $key2 => $val2) {
					$arr[] = array(
							'kpiid' => $val2,
							'kpiscore' => $_POST['plan_kpiscore'.$val][$val2],
					);
				}
				$volist[$val] = array(
						'kpitypeid' => $val,
						'kpitypeqz' => $kpitypeqz[$key],
						'kpi' => $arr
				);
			}
			$setscoretype=$_POST['setscoretype']; //评分人类型
			$inusersqz=array();
			foreach ($setscoretype as $key=>$val) {
				$inusersqz[$val]= $_POST['inusersqz'.$val];
			}
			$data['setscoretype']=explode(',', $setscoretype); //逗号分隔评分人类型
			$data['inusersqz']=serialize($inusersqz); //评分人类型对应权重序列化
			$data['name']=$_POST['name'];
			$data['detailserlize']=serialize($volist);  //序列化明细
			$data['cycle']=$_POST['cycle'];
			$data['ostatus']=0;
			$data['course']=$_POST['course'];
			$data['tempid']=$_POST['tempid'];
			$data['salarydate']=strtotime($_POST['salarydate']);
			$data['userid']=$_POST['userid'];
			$data['setdate']=strtotime($_POST['setdate']);
			$data['mostscore']=$_POST['mostscore'];
			$data['levelid']=$_POST['levelid'];
			$data['createtime']=time();
			$data['createid']=$_SESSION[C('USER_AUTH_KEY')];
			$data['orderno']=$_POST['orderno'];
			$MisPerformancePlanResult=$MisPerformancePlanModel->where("id=".$id)->data($data)->save();
			$MisPerformancePlanModel->commit();
			if($MisPerformancePlanResult==false){
				$this->error("操作失败！");
			}else{
				$this->success("操作成功！");
			}
		}
	}
// 	public function selectplan(){
// 		$id=$_POST['id'];
// 		//绩效计划模型
// 		$MisPerformancePlanModel=D('MisPerformancePlan');
// 		$list=$MisPerformancePlanModel->where(" id=".$id)->find();
// 		echo $list['ostatus'];
// 	}
	public function setScore(){
		if($_GET['id']){
			$this->assign("id",$_GET['id']);
		}
		//查询评分人类型
		$misPerformanceTypeModel = D('MisPerformanceType');
		$map['type']=4;  //评分人设置
		$map['status']=1;
		$misPerformanceTypeList=$misPerformanceTypeModel->where($map)->getField("id,name");
		$this->assign("misPerformanceTypeList",$misPerformanceTypeList);
		$this->display();
	}
}
?>