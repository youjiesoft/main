<?php
/**
 * @Title: PublicExtend
 * @Package package_name
 * @Description: todo(public未知扩展函数)
 * @author everyone
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2011-4-7 下午5:13:20
 * @version V1.0
 */
/**
 * @Title: index_old
 * @Description: todo(以前用的INDEX，做暂存用)
 * @author 杨东
 * @date 2013-4-2 下午4:24:03
 * @throws
 */

class IndexExtendAction extends CommonAction {
	public function mistaskinfo(){
		//判断权限 任务
		$mistaskModel = D("MisTaskInformationView");
		$map = array();
		$map['executingstatus'] = array('NEQ', 7);
		$map['status'] = 1;
		$mistaskinfo = array();
		if ($_REQUEST['mistasktype'] == 2) {
			$map['executeuser'] = $_SESSION[C('USER_AUTH_KEY')];
			if ($_SESSION['a'] || $_SESSION['mistaskadscription_edit']) {
				$mistaskinfo  = $mistaskModel->where($map)->field('id,title,createtime')->order('taskid DESC')->limit(7)->select();
			}
		} else {
			$map['createid'] = $_SESSION[C('USER_AUTH_KEY')];
			if($_SESSION['a'] || $_SESSION['mistaskinformation_edit']){
				$mistaskinfo  = $mistaskModel->where($map)->field('id,title,createtime')->order('taskid DESC')->limit(7)->select();
			}
		}
		foreach ($mistaskinfo as $key => $val) {
			$mistaskinfo[$key]['title'] = missubstr($val['title'], 60, true);
		}
		$this->assign('mistaskinfo', $mistaskinfo);
		$this->assign('mistasktype', $_REQUEST['mistasktype']);
		if ($_REQUEST['mistasktype']) {
			$this->display();
		}
	}
	/**
	 * @Title: toolBox 工具箱
	 * @Description: todo(下载相关系统工具)
	 * @author eagle
	 * @date 2014-03-21 下午5:23:47
	 * @throws
	 */
	function lookupToolBox(){
		//显示模板
		$this->display();
	}
	
	/**
	 * @Title: setDbhaSmsgType
	 * @Description: todo(右下角系统提示信息点击确定后操作)
	 * @author jiangx
	 * @date 2013-7-9
	 * @throws
	 */
	public function setDbhaSmsgType(){
		// 		foreach ($_POST['datalist'] as $key => $val) {
		// 			$model = D($key);
		// 			$model->where('id in ( '. $val .' )')->setField('isread', 1);
		// 		}
		// 		$this->transaction_model->commit();//事务提交
		$data = getTaskulous();
		$_SESSION['popupTaskulous'] = md5(serialize($data));
		exit('1');
	}
	
	protected  function setURLdata($data){
		//分割后[0]为实际路径,[1]为标签显示名称,[2]对应Model,[3]对应类型 dialog跟navtab 默认是navtabf
		$data = str_replace("%3b", ";", $data);
		$objarr = explode(";", $data);
		$value["url"] = $objarr[0];
		$value["title"] = getFieldBy($objarr[1], "name", "title", "node");
		$value["model"] = $objarr[2];
		if($objarr[3] === "Dialog") {
			$value["target"] = $objarr[3];
		} else {
			$value["target"] = "navTab";
		}
		return json_encode($value);
	}
	
	protected function index_old() {
		$model=M("group");
		$list = $model->where("status=1")->order("sorts asc")->select();
		$node = D( "Node" );
		$map['status'] = 1;
		$map['showmenu'] = 1;
		$map['level'] =array("neq",4);
		$nodedata = $node->where($map)->order("sort asc")->select();
		//获取系统授权
		$model_syspwd=D('SerialNumber');
		$modules_sys=$model_syspwd->checkModule();
		$m_list = explode(",",$modules_sys);
		if(isset($_SESSION[C('USER_AUTH_KEY')])) {
			$pannels="<ul class=\"dropdown\">";
			foreach($list as $k =>$v){
				$h="<li class=\"menu_now\">";
				if( $v['indexlink'] ){
					//$h .= "<a href='__APP__/Public/nvigateTO/id/".$v["id"]."' target='navTab' rel='".$v["name"]."'>".$v["name"]."</a>";
					$h .= "<a href='__APP__/Common/nvigateTO/id/".$v["id"]."' target='navTab' rel='".$v["name"]."'>".$v["name"]."</a>";
				}else{
					$h .= "<a href='#'>".$v["name"]."</a>";
				}
				$pannels_tree= $this->getIndexTree($v['id'],$nodedata,0,"","",$m_list);
				$h_e="</li>";
				if($pannels_tree){
					$pannels.=$h."<ul>".$pannels_tree."</ul>".$h_e;
				}
			}
			$pannels.= "</ul>";
			$this->assign('pannels',$pannels);
		}
		C ( 'SHOW_RUN_TIME', false ); // 运行时间显示
		C ( 'SHOW_PAGE_TRACE', false );
		$this->display ();
	}
	
	protected  function getIndexTree_old($group_id,$nodedata,$pid=0,$pname="",$ptitle="",$m_list){
		$html="";
		$node = D( "Node" );
	
		$access = getAuthAccess();
		foreach($nodedata as $k => $v)
		{
			if($v['group_id'] !=$group_id) continue;
			if( $pid ){
				if($pid!=$v['pid'])continue;
			}
			if($v['name']!='Public' && $v['name']!='Index')
			{
				if($v['type'] == 3 ){
					if($v["pid"]==$pid){
						unset($nodedata[$k]);
						//校验系统模块授权
						if(substr($v['name'], 0, 10)!="MisDynamic"){
							if(!in_array($v['name'], $m_list))  continue;
						}
						if (!isset ($access[strtoupper( APP_NAME )][strtoupper ($v ['name'])]) && !$_SESSION [C('ADMIN_AUTH_KEY')]) {
							continue;
						}
						$html.= "<li><a class=\"add_new\" target=\"navTab\" rel=\"".$v['name']."\" href=\"__APP__/".$v['name']."/index\"><span class=\"left\">".$v['title']."</span>";
	
						$map['status'] = 1;
						$map['level'] =4;
						$map['pid'] =$v['id'];
						$map['group_id'] =$group_id;
						$nodedata2 = $node->where($map)->select();
						if($nodedata2){
							$s3_1= $this->getIndexTree($group_id,$nodedata2,$v['id'],$v['name'],$v['title'],$m_list);
							if($s3_1){
								$html.="<span class=\"sf_sub_indicator right\"></span></a><ul>";
								$html .=$s3_1;
								$html.="</ul>";
							}
						}
						$html.="</a></li>";
					}
				}
				else if($v['type'] == 4 ){
					unset($nodedata[$k]);
					if (!isset ($access[strtoupper( APP_NAME )][strtoupper ($pname)][strtoupper ( $v['name'])])  && !$_SESSION [C('ADMIN_AUTH_KEY')]) {
						continue;
					}
					if( $v["toolshow"]){
						$url_parem=$v['name'];
						$title	=$v['title'];
						$rel	=$pname.$v['name'];
						if($v['name']=="waitAudit"){
							$title	=$ptitle;
							$rel	=$pname;
							$url_parem="index/ntdata/1";
						}else if($v['name']=="alreadyAudit"){
							$title	=$ptitle;
							$rel	=$pname;
							$url_parem="index/ntdata/2";
						}
						$html.= "<li><a class=\"add_new\" title=".$title." target=\"navTab\" rel=\"".$rel."\" href=\"__APP__/".$pname."/".$url_parem."\"><span class='left'>".$v['title']."</span></a></li>";
					}
				}else {
					if($v['type'] == 1 ){
						if($v["toolshow"]==1){
							$s1_1= "<li><a class='add_new'><span class=\"left\">".$v['title']."</span><span class=\"sf_sub_indicator right\"></span></a>";
						}
						unset($nodedata[$k]);
						$s1_2= $this->getIndexTree($group_id,$nodedata,$v['id'],"","",$m_list);
						if($s1_2){
							if($v["toolshow"]==1 ){
								$html.=$s1_1."<ul>".$s1_2."</ul></li>";
							}else{
								$html.=$s1_2;
							}
						}else{
							if($_SESSION [C('ADMIN_AUTH_KEY')] && $s1_1){
								if($v["toolshow"]==1){
									$html.=$s1_1."</li>";
								}
							}
						}
					}else if($v['type'] == 2 ){
						if($v["pid"]==$pid){
							unset($nodedata[$k]);
							if($v["toolshow"]==1){
								$s2_1= "<li><a class='add_new'><span class=\"left\">".$v['title']."</span><span class=\"sf_sub_indicator right\"></span></a>";
								/*$models = M('mis_report_center');
								 $lists = $models->where('type='.$v['id'].' and status = 1')->select();
								if ($lists)  $s2_1.="<ul>";
								foreach ($lists as $ks => $vs) {
								$s2_1 .= "<li><a rel='ReportCenter".$vs[id]."' target='navTab' href='__APP__/ReportCenter/index/reporttype/".$vs[id]."'>".$vs['name']."</a></li>";
								}
								if ($lists)  $s2_1.="</ul>";*/
							}
	
							$s2_2=$this->getIndexTree($group_id,$nodedata,$v['id'],"","",$m_list);
							if($s2_2){
								if($v["toolshow"]==1){
									$html.=$s2_1."<ul>".$s2_2."</ul></li>";
								}else{
									$html.=$s2_2;
								}
							}else{
								if($_SESSION [C('ADMIN_AUTH_KEY')] && $s2_1){
									if($v["toolshow"]==1){
										$html.=$s2_1."</li>";
									}
								}
							}
						}
					}
				}
			}
		}
		return $html;
	}
	
	/**
	 * @Title: workstatement
	 * @Description: todo(待阅报告)
	 * @author liminggang
	 * @date 2013-7-4 上午9:17:21
	 * @throws
	 */
	private function workstatement(){
		$MisWorkStatementModel=M('mis_work_read_statement');
		//获得当前登录人
		$userid=$_SESSION[C('USER_AUTH_KEY')];
		$map['userid']=$userid;
		$map['status'] = 1;
		$map['readstatus'] = 1; //查阅状态为，未查阅
		$worklist=$MisWorkStatementModel->where($map)->select();
		$this->assign('worklist',$worklist);
	
	}
	
	
	/**
	 * @Title: Procurement
	 * @Description: todo(采购分派)
	 * @author renling
	 * @date 2013-4-8 下午5:46:52
	 * @throws
	 */
	private function Procurement(){
		//采购分派模型
		$MisPurchaseAssignsubModel=D('MisPurchaseAssignsub');
		$map['uninqty']= array('gt',0); //未采购数量大于0
		//当前登录用户查询采购任务
		if(!$_SESSION['a'] ){
			$map['proposer']=$_SESSION[C('USER_AUTH_KEY')];
		}
		$list=$MisPurchaseAssignsubModel->where($map)->count();
		//采购任务条数
		$this->assign('AssignsubCount',$list);
	}
	/**
	 * @Title: purchaseAssignsub
	 * @Description: todo(采购分派dilog加载)
	 * @author renling
	 * @date 2013-4-8 下午5:58:02
	 * @throws
	 */
	public function purchaseAssignsub(){
		$modelname='MisPurchaseAssignsub';
		//保留查询条件
		$map=array();
		//构造项目ztree
		$name = $this->getActionName();
		//采购物料表
		$MisPurchaseAssignsubModel=D('MisPurchaseAssignsub');
		$list=$MisPurchaseAssignsubModel->query("SELECT mis_sales_project.name,mis_sales_project.id  FROM `mis_purchase_assignsub`,mis_sales_project WHERE mis_purchase_assignsub.projectid=mis_sales_project.id   GROUP BY mis_sales_project.name");
		$MisSalesProjectModel=D('MisSalesProject');
		// 		$projectID=$MisSalesProjectModel->where('id='.$list[0]['projectid'])->getField('name');
		$treemiso[]=array(
				'id'=>0,
				'pid'=>-1,
				'name'=>$list['name'],
				'rel'=>'purchaseAssignsub',
				'target'=>'ajax',
				'url'=>'__URL__/purchaseAssignsub/jump/1/',
				'title'=>$list['name'],
				'open'=>true
		);
		$param['rel']="purchaseAssignsub";
		$param['url']="__URL__/purchaseAssignsub/jump/1/projectid/#id#";
		$typeTree=$this->getTree($list,$param);
		$this->assign('typeTree',$typeTree);
		//分条件查询
		$searchby	= $_POST['searchby'];
		$keyword	= $_POST['keyword'];
		$searchtype	= $_POST['searchtype'];
		$this->assign('keyword', $keyword);
		$this->assign('searchby', $searchby);
		$this->assign('searchtype', $searchtype);
		if($searchby=="code"){
			if ($keyword) {
				$model1=D("MisProductCode");
				$map2[$searchby]=$searchtype==2 ? array('like',"%".$keyword."%"):$keyword;
				$arrayId=$model1->where($map2)->getField('id,code');
				$ids = array_keys($arrayId);
				$map['prodid']=array('in',$ids);
			}
		}
		else if($searchby=="name"){
			if($keyword){
				$model1=D("MisProductCode");
				if($searchtype==2){
					$where['name']=array('like',"%".$keyword."%");
					$where['pinyin']=array('like',"%".$keyword."%");
					$where['_logic'] = 'or';
					$map1['_complex']=$where;
					$arrayId=$model1->where($map1)->getField('id,code');
				}
				else{
					$where['name']=$keyword;
					$where['pinyin']=$keyword;
					$where['_logic'] = 'or';
					$map1['_complex']=$where;
					$arrayId=$model1->where($map1)->getField('id,code');
				}
				$ids = array_keys($arrayId);
				$map['prodid']=array('in',$ids);
			}
		}
		else if($searchby=="projectid"){
			if($keyword){
				$model1=D("MisSalesProject");
				$map1["code"]=$searchtype==2 ? array('like',"%".$keyword."%"):$keyword;
				$arrayId=$model1->where($map1)->getField('id,code');
				$ids = array_keys($arrayId);
				$map["mis_purchase_applymas.projectid"]=array('in',$ids);
			}
		}else if($searchby=="projectname"){
			if($keyword){
				$model1=D("MisSalesProject");
				$map1["name"]=$searchtype==2 ? array('like',"%".$keyword."%"):$keyword;
				$arrayId=$model1->where($map1)->getField('id,code');
				$ids = array_keys($arrayId);
				$map["mis_purchase_applymas.projectid"]=array('in',$ids);
			}
		} else if($searchby=="indate"){
			$datestart	= $_POST['purdatestart'];
			$dateend	= $_POST['purteend'];
			$map1 = $map2 = array ();
			if ($datestart) $map1 = array ( array ('egt',strtotime($datestart)) );
			if ($dateend) $map2 = array ( array ('elt',strtotime($dateend)));
			$map["mis_purchase_applymas.dmdate"] = array_merge($map1, $map2);
			$this->assign('purdatestart', $datestart);
			$this->assign('purdateend', $dateend);
		} else{
			if ($keyword) {
				$map[$searchby] = ($searchtype == 2) ? array ('like',"%" . $keyword . "%") : $keyword;
			}
		}
		//检索
		$searchby = array (
				array ("id" => "orderno","val" => "按申请单号"),
				array ("id" => "projectid","val" => "按项目编号"),
				array ("id" => "code","val" => "按物料代码"),
				array ("id" => "name","val" => "按物料名称"),
				array ("id" => "indate","val" => "按需求日期"),
		);
		$searchtype = array (
				array ("id" => "2","val" => "模糊查找"),
				array ("id" => "1","val" => "精确查找")
		);
		$this->assign('searchbylist', $searchby);
		$this->assign('searchtypelist', $searchtype);
		$this->assign('projectid',$_GET['projectid']);
		$map['projectid']=$_GET['projectid']; //项目ID等于当前传过来的ID
		//当前登录用户查询采购任务
		$map['proposer']=$_SESSION[C('USER_AUTH_KEY')];
		$map['uninqty']= array('gt',0); //未采购数量大于0
		if (! empty ( $modelname )) {
			$this->_list( $modelname, $map );
		}
		//其他配置
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($modelname);
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		if($_GET['jump']){
			$this->display('purchaselist');
		}else{
			$this->display();
		}
	}
	
	/**
	 * @Title: insertPurchaseAssignsub
	 * @Description: todo(采购分派任务保存)
	 * @author renling
	 * @date 2013-4-12 下午2:37:44
	 * @throws
	 */
	//public  function insertPurchaseAssignsub(){
	//	 //加入到采购申请详情里
	//	$scnmodel = D('SystemConfigNumber');
	//	$etverno = $scnmodel->GetRulesNO('mis_purchase_ordermas');
	//	//采购订单模型
	//	$MisPurchaseOrdermasModel=D('MisPurchaseOrdermas');
	//
	//
	//
	//	$this->assign("orderno", $etverno);
	//}
	
	/**
	 * @Title: SystemNotices
	 * @Description: todo(系统公告)
	 * @author 杨东
	 * @date 2013-3-15 下午5:45:07
	 * @throws
	 */
	private function SystemNotices(){
		$snmodel = D('MisSystemAnnouncement');
		$map['status']=array("gt",-1);
		$map['type'] = array("eq","system");
		$list = $snmodel->where($map)->order('createtime desc')->limit('0,5')->select();
		$this->assign('snlist',$list);
	}
	/**
	 * @Title: myToDoList
	 * @Description: todo(我的待办任务)
	 * @author 杨东
	 * @date 2013-3-15 下午5:02:51
	 * @throws
	 */
	private function myToDoList(){
		$arr			= array();
		$html = '';
		$file =  DConfig_PATH . "/System/ProcessModelsConfig.inc.php";
		$arr = require $file;
		foreach( $arr as $k=>$v ){
			// 			if ($_SESSION["a"] == 1 || $_SESSION[$v['session']."_waitaudit"]) {
			$model = D($v['model']);
			//验证浏览及权限
			// 				if( !isset($_SESSION['a']) ){
			// 					if( $_SESSION[strtolower($v['model'].'_waitaudit')]==2 ){////判断部门及子部门权限
			// 						$map["createid"]=array("in",$_SESSION['user_dep_all_child']);
			// 					}else if($_SESSION[strtolower($v['model'].'_waitaudit')]==3){//判断部门权限
			// 						$map["createid"]=array("in",$_SESSION['user_dep_all_self']);
			// 					}else if($_SESSION[strtolower($v['model'].'_waitaudit')]==4){//判断个人权限
			// 						$map["createid"]=$_SESSION[C('USER_AUTH_KEY')];
			// 					}
			// 				}
			$map['status'] = 1;
			$map['_string'] = 'FIND_IN_SET(  '.$_SESSION[C('USER_AUTH_KEY')].', curAuditUser )';
			$count = $model->where($map)->count('id');
			if ($count) {
				$m = strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $v['model']), "_"));
				if($_SESSION[strtolower($m.'_index')] || isset($_SESSION['a'])){
					$ntdata =1;
				}else{
					$ntdata=0;
				}
				$html .= '<li><a rel="'.$v['model'].'" target="navTab" title="'.$v['name'].'" href="'.__APP__.'/'.$v['model'].'/index/default/2">'.$v['name'].' &nbsp;&nbsp; 共 '.$count.' 条</a></li>';
			}
			// 			}
		}
		$this->assign('myToDo',$html);
	}
	/**
	 * @Title: lookupmyWorkflow
	 * @Description: todo(工作流)
	 * @author 杨东
	 * @date 2013-3-16 下午1:49:36
	 * @throws
	 */
	function lookupmyWorkflow(){
		//企业工作台下工作流类型
		$type = $_GET['type'];
		//操作工作台下工作流类型
		$optype = $_GET['optype'];
		//$type,$optype不会同存在,$typemap用于switch组织查询条件
		if($type) {
			$typemap = $type;
		}
		if ($optype) {
			$typemap = $optype;
		}
		$file =  DConfig_PATH . "/System/ProcessModelsConfig.inc.php";;
		$arr = require $file;
		$nolist = array();
		$time = time()-2*24*3600;//5天
		foreach( $arr as $k=>$v ){
			$map = $hmap = array();
			if ($_SESSION["a"] == 1 || $_SESSION[$v['session']."_index"]) {
				$model = D($v['model']);
				switch ($typemap) {
					case 1:
						$map['auditState'] = -1;
						$hmap['auditstatus'] = -1;
						break;
					case 2:
						$map['auditState'] = 1;
						$hmap['auditstatus'] = 1;
						$mapv['dotime'] = array('elt',$time);
						break;
					case 3:
						$map['auditState'] = 2;
						$hmap['auditstatus'] = 2;
						$mapv['dotime'] = array('elt',$time);
						break;
					case 4:
						$map['auditState'] = 3;
						$hmap['auditstatus'] = 3;
						break;
					default:
						$map['auditState'] = -1;
						$hmap['auditstatus'] = -1;
						break;
				}
				$map['status'] = 1;
				if( !isset($_SESSION['a']) ){
					if( $_SESSION[strtolower($v['model'].'_index')]==2 ){////判断部门及子部门权限
						$map["createid"]=array("in",$_SESSION['user_dep_all_child']);
					}else if($_SESSION[strtolower($v['model'].'_index')]==3){//判断部门权限
						$map["createid"]=array("in",$_SESSION['user_dep_all_self']);
					}else if($_SESSION[strtolower($v['model'].'_index')]==4){//判断个人权限
						$map["createid"]=$_SESSION[C('USER_AUTH_KEY')];
					}
				}
				$tableid = $model->where($map)->getField('id',true);
				if ($tableid) {
					$hmodel = D('ProcessInfoHistory');
					$hmap['tableid'] = array('in',$tableid);
					$hmap['tablename'] = $v['model'];
					$id = $hmodel->where($hmap)->group('tableid')->getField("max(id)",true);
					if ($id) {
						$mapv['id'] = array('in',$id);
						$vlist = $hmodel->where($mapv)->limit('0,5')->select();
						if ($vlist) {
							$v['count'] = count($vlist);
							$v['vlist'] = $vlist;
							$nolist[] = $v;
						}
					}
				}
			}
		}
		$this->assign("nolist",$nolist);
		if($optype){
			$this->display('opmywordflowindex');
		}
		if($type){
			$this->display('myWorkflowindex');
		}
	}
	/**
	 * @Title: schedule
	 * @Description: todo(日程安排)
	 * @author 杨东
	 * @date 2013-3-20 下午4:28:17
	 * @throws
	 */
	function schedule(){
		$model = D('MisUserEvents');
		$time = time();
		$type = $_GET['type'];
		$this->assign('type',$type);
		$list = array();
		switch ($type) {
			case 1:
				$list = $model->where('FIND_IN_SET(  '.$_SESSION[C('USER_AUTH_KEY')].', personid ) and status = 1 and \''.$time.'\'<=enddate')->order('enddate')->limit('0,7')->select();
				break;
			case 2:
				$list = $model->where('userid = '.$_SESSION[C('USER_AUTH_KEY')].' and status = 1 and \''.$time.'\'<=enddate')->order('enddate')->limit('0,7')->select();
				break;
			default:
				$list = $model->where('FIND_IN_SET(  '.$_SESSION[C('USER_AUTH_KEY')].', personid ) and status = 1 and \''.$time.'\'<=enddate')->order('enddate')->limit('0,7')->select();
				break;
		}
		$this->assign('selist',$list);
		if ($type) {
			$this->display('scheduleindex');
		}
	}
	/**
	 * @Title: guidance
	 * @Description: todo(引导界面)
	 * @author renling
	 * @date 2013-6-25 下午5:23:47
	 * @throws
	 */
	function guidance(){
		$step=$_POST['step'];
		$this->assign('companyId',$_REQUEST['companyId']);
		switch ($step) {
			case '2': //导入公司信息
				$this->guiComany();
				$this->display('guiComRegistry');
				break;
			case '3':
				$this->guiSysDepImport(); //导入部门信息
				$this->display('guiDuty');
				break;
			case '4':
				$this->guiDutyImport();//导入职级
				$this->display('guiMisHrpersonInfo');
				break;
			case '5':
				$this->guiHrImport(); //导入人事基本信息
				$this->display('guiUser');
				break;
			case '6':
				$this->guiUserImport(); //导入后台用户
				$this->display('guiEnd');
				break;
			default:
				$this->selectComany();
				$this->display();
				break;
		}
		// 		if($step=='1'){
	
		// 			$this->display('guiComRegistry');
		// 		}else{s
		// 			$this->display();
		// 		}
	}
	public function guiUserImportConfig(){
		$importconfig=$_POST['importconfig'];
		//如果没有配置字段默认导入全部字段
		$importconfigMap='';
		$MisImportFieldconfigModel=D('MisImportSpecial');
		$MisImportFieldconfigList=$MisImportFieldconfigModel->where(" tablename='User' and status=1")->find();
		$fieldAll=explode(',', $MisImportFieldconfigList['fieldname']);
		$this->assign('fieldAll',$fieldAll);
		$this->assign('MisImportFieldconfigList',$MisImportFieldconfigList);
		//导入字段配置
		if($importconfig){
			$id=$_POST['id'];
			if($_POST['A']){
				$importconfigMap='A,';  //用户名
			}
			if($_POST['B']){
				$importconfigMap.='B,';  //姓名
			}
			if($_POST['C']){
				$importconfigMap.='C,';  //性别
			}
			if($_POST['D']){
				$importconfigMap.='D,';   //部门
			}
			if($_POST['E']){
				$importconfigMap.='E,';   //职务
			}
			if($_POST['F']){
				$importconfigMap.='F,';   //入职时间
			}
			if($_POST['G']){
				$importconfigMap.='G,';   //手机号码
			}
			if($_POST['I']){
				$importconfigMap.='I,';    //电子邮件
			}
			if($_POST['J']){
				$importconfigMap.='J,';   //备注
			}
			if($_POST['K']){
				$importconfigMap.='K,';   //如若没该职级的审核职级是否默认插入
			}
			$saveconfigMap['tablename']='User';
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
	 * @Title: guiUserImport
	 * @Description: todo(后台用户导入excel)
	 * @author renling
	 * @date 2013-6-26 下午4:48:21
	 * @throws
	 */
	public  function guiUserImport(){
		//读取配置字段
		$MisImportFieldconfigModel=D('MisImportSpecial');
		$MisImportFieldconfigList=$MisImportFieldconfigModel->where(" tablename='User' and status=1")->find();
		$fieldAll=explode(',', $MisImportFieldconfigList['fieldname']);
		//获得操作类型
		$operation=$_POST['operation'];
		import ( '@.ORG.UploadFile');
		if($_FILES){
			$upload = new UploadFile(); // 实例化上传类
			$upload->savePath = UPLOAD_PATH."user_excel/";//C('savePath'); // 上传目录
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
				$inputFileName = UPLOAD_PATH."user_excel/".$info[0]['savename'];
				if($filetype=="xls"){
					$inputFileType = 'Excel5';
				}
				$objReader = PHPExcel_IOFactory::createReader($inputFileType);
				$objPHPExcel = $objReader->load($inputFileName);
				$objPHPExcel->setActiveSheetIndex(0);
				$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
				$dataList =$sheetData;
				$excel=true;
				//部门表模型
				$MisSystemDepartmentModel=D('MisSystemDepartment');
				//人事模型
				$MisHrBasicEmployeeModel=D('MisHrBasicEmployee');
				//用户表模型
				$UserModel=D('User');
				//职务表
				$DutyModel=D('Duty');
				//用户基础权限角色表
				$RoleUserModel=D('RoleUser');
				//基础权限组
				$role=C('USER_BASIC_ROLE_GROUP');
				//用户高级权限角色表
				$RolegroupUserModel=D('RolegroupUser');
				//高级权限组
				$roleGroup=C('USER_SENIOR_ROLE_GROUP');
				$i=0;
				$parentMap=array();
				foreach($dataList as $k=>$v){
					//把标题列除外
					if($i >= 1){
	
						//判断名称是否存在部门表
						$list = $MisSystemDepartmentModel->where("status=1 and name='".$v['D']."'")->find();
						if(!$list){
							$this->error("第".($i+1)."条".$v['D']."部门不存在,请先新建部门再导入数据！");
							exit;
						}
						//查询后台用户的员工编号 通过身份证号码
						$employeid=$MisHrBasicEmployeeModel->where(" status=1 and working=1  and chinaid='".$v['H']."'")->find();
						if(!$employeid){
							$this->error("第".($i+1)."条".$v['C']."人事基本资料不存在,请先新建此人人事基本资料再导入数据！");
							exit;
						}
						// 					判断此后台用户是否已存在
						$UserList=$UserModel->where("status=1 and  account='".$v['A']."'")->find();
						//查看职务是否存在
						$Duty=$DutyModel->where(" status=1  and  name='".$v['E']."'")->find();
						if(!$Duty){
							$this->error("第".($i+1)."条".$v['E']."职务不存在,请先新建该职务再导入数据！");
							exit;
						}
						//获得相同职务的审批职级
						$auditduty=$UserModel->where(" status=1  and duty_id='".$Duty['id']."'")->find();
						if($MisImportFieldconfigList){ //用户已配置导入字段
							if(in_array('A', $fieldAll)){
								$saveMap['account']=$v['A']; //用户名
							}
							if(in_array('B', $fieldAll)){ //姓名
								$saveMap['name']=$v['B'];
							}
							if(in_array('C', $fieldAll)){  //性别
								if($v['C']=="男"){
									$saveMap['sex']=1;
								}else if($v['C']=="女"){
									$saveMap['sex']=0;
								}else{
									$saveMap['sex']="";
								}
							}
							if(in_array('D', $fieldAll)){ //部门
								$saveMap['dept_id']=$list['id'];
							}
							if(in_array('E', $fieldAll)){
								$saveMap['duty_id']=$Duty['id'];  //职务
							}
							if(in_array('F', $fieldAll)){
								if($v['F']){
									$saveMap['work_date']=strtotime($v['F']);  //入职时间
								}else{
									$saveMap['work_date']="";
								}
							}
							if(in_array('G', $fieldAll)){ //手机号码
								$saveMap['mobile']=$v['G'];
							}
							if(in_array('I', $fieldAll)){ //电子邮件
								$saveMap['email']=$v['I'];
							}
							if(in_array('J', $fieldAll)){
								$saveMap['remark']=$v['J'];
							}
							if($auditduty){ //有该职务的审核职级
								$saveMap['auditduty']=$auditduty['auditduty']; //审核职级
							}else{
								if(in_array('K', $fieldAll)){
									$saveMap['auditduty']=$Duty['id'];  //职位相同的审核职级
								}else{
									$saveMap['auditduty']="";		//审核职级留空
								}
							}
						}else{ //用户没有配置导入字段默认全部导入
							$saveMap['account']=$v['A']; //用户名
							$saveMap['name']=$v['B']; 	//姓名
							if($v['C']=="男"){			//性别
								$saveMap['sex']=1;
							}else if($v['C']=="女"){
								$saveMap['sex']=0;
							}else{
								$saveMap['sex']="";
							}
							$saveMap['dept_id']=$list['id'];	//部门
							$saveMap['duty_id']=$Duty['id'];  //职务
							if($v['F']){
								$saveMap['work_date']=strtotime($v['F']);  //入职时间
							}else{
								$saveMap['work_date']="";
							}
							$saveMap['mobile']=$v['G'];
							$saveMap['email']=$v['I'];
							$saveMap['remark']=$v['J'];
							if($auditduty){ //有该职务的审核职级
								$saveMap['auditduty']=$auditduty['auditduty']; //审核职级
							}else{
								$saveMap['auditduty']=$Duty['id'];  //职位相同的审核职级
							}
						}
						$saveMap['password']=pwdHash(C('USER_DEFAULT_PASSWORD'));
						if($operation=="add"){//操作类型是 新增
							$saveMap['createid']=$_SESSION[C('USER_AUTH_KEY')];
							$saveMap['createtime']=time();
							$saveMap['employeid']=$employeid['id'];
							$result=$UserModel->add($saveMap); //插入用户表
							if(C('SWITCH_BASIC_ROLE_GROUP')=="On"){
								foreach($role  as $rk=>$rv){
									$roleMap["role_id"]=$rv;
									$roleMap['user_id']=$result;
									$roleMap['companyid']=$company['id'];
									$roleMap['createtime']=time();
									$roleMap['createid']=$_SESSION[C('USER_AUTH_KEY')];
									$RoleUserModel->add($roleMap);   //插入基础权限组
									unset($roleMap);
								}
							}
							if(C('SWITCH_SENIOR_ROLE_GROUP')=="On"){
								foreach($roleGroup  as $k1=>$v1){
									$roleGroupMap["rolegroup_id"]=$v1;
									$roleGroupMap['user_id']=$result;
									$roleGroupMap['companyid']=$company['id'];
									$roleGroupMap['createtime']=time();
									$roleGroupMap['createid']=$_SESSION[C('USER_AUTH_KEY')];
									$RolegroupUserModel->add($roleGroupMap);   //插入高级权限组
									unset($roleGroupMap);
								}
							}
						}else{  //操作类型是新增及修改
							if($UserList){ //修改数据
								$saveMap['id']=$UserList['id'];
								$saveMap['updateid']=$_SESSION[C('USER_AUTH_KEY')];
								$saveMap['updatetime']=time();
								$saveMap['employeid']=$employeid['id'];
								$result=$UserModel->save($saveMap);
							}else{ //新增
								$saveMap['createid']=$_SESSION[C('USER_AUTH_KEY')];
								$saveMap['createtime']=time();
								$saveMap['employeid']=$employeid['id'];
								$result=$UserModel->add($saveMap);
								$result=$UserModel->add($saveMap); //插入部门表
								if(C('SWITCH_BASIC_ROLE_GROUP')=="On"){
									foreach($role  as $rk=>$rv){
										$roleMap["role_id"]=$rv;
										$roleMap['user_id']=$result;
										$roleMap['companyid']=$company['id'];
										$roleMap['createtime']=time();
										$roleMap['createid']=$_SESSION[C('USER_AUTH_KEY')];
										$RoleUserModel->add($roleMap);   //插入基础权限组
										unset($roleMap);
									}
								}
								if(C('SWITCH_SENIOR_ROLE_GROUP')=="On"){
									foreach($roleGroup  as $k1=>$v1){
										$roleGroupMap["rolegroup_id"]=$v1;
										$roleGroupMap['user_id']=$result;
										$roleGroupMap['companyid']=$company['id'];
										$roleGroupMap['createtime']=time();
										$roleGroupMap['createid']=$_SESSION[C('USER_AUTH_KEY')];
										$RolegroupUserModel->add($roleGroupMap);   //插入高级权限组
										unset($roleGroupMap);
									}
								}
							}
						}
						if(!$result){
							$this->error("第".($i+1)."条数据有误,请查看！");
							exit;
						}
					}
					$i++;
				}
				$this->success("导入成功!");
			}else{
				$this->error('文件格式不正确,请上传后缀为xls的文件!');
			}
		}
	}
	/**
	 * @Title: guiHrImportConfig
	 * @Description: todo(人事基本信息字段配置)
	 * @author renling
	 * @date 2013-6-26 下午3:48:04
	 * @throws
	 */
	Public function guiHrImportConfig(){
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
	 * @Title: guiDutyImport
	 * @Description: todo(导入人事基本信息)
	 * @author renling
	 * @date 2013-6-26 下午2:45:13
	 * @throws
	 */
	public function guiHrImport(){
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
			$upload->savePath = UPLOAD_PATH."person_excel/";//C('savePath'); // 上传目录
			$upload->saveRule = date("Y_m_d_H_i_s").rand(1000,9999);
			$upload->allowExts= array("xls","xlsx");
			if(!$upload->upload()) { // 上传错误提示错误信息
				$this->error($upload->getErrorMsg());
			}else{ // 上传成功 获取上传文件信息
				$info = $upload->getUploadFileInfo();
			}
			$filetype =end(explode(".",$info[0]['savename']));
			if($filetype=="xls" || $filetype=="xlsx"){
				import('@.ORG.PHPExcel.IOFactory', '', $ext='.php');
				$inputFileName = UPLOAD_PATH."person_excel/".$info[0]['savename'];
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
						if($v['R']=="正式"){
							$saveMap['workstatus']=1; //正式员工
							if($v['Q']){//添加转正日期
								$transferdate=str_replace('.', '-',$v['Q']);
								$saveMap['transferdate']=strtotime($transferdate);
							}
						}else{
							$saveMap['workstatus']=2; //试用员工
						}
						$saveMap['specialskill']=$v['S'];//特长
						$saveMap['deptid']=$depList['id'];//所属部门
						$saveMap['dutylevelid']=$dutylist['id'];//职级
						if($v['V']=="兼职"){
							$saveMap['agreetypeid']=2;//兼职
						}else if($v['V']=="自主择业"){
							$saveMap['agreetypeid']=3;//自主择业
						}else if($v['V']=="4050"){
							$saveMap['agreetypeid']=4;//4050
						}else{
							$saveMap['agreetypeid']=5;//退休
						}
						$v['W']=str_replace('.', '-');
						$saveMap['agreetime']=$v['W'];//签约时间
						$saveMap['fingerprint']=$v['X']=="是"?1:0;//指纹
						$saveMap['identity']=$v['Y']=="是"?1:0;//身份证信息
						$saveMap['iscommit']=$v['Z']=="是"?1:0;//是否提交
						$saveMap['staffnumber']=$v['AA'];//保安证编号
						$saveMap['remark']=$v['AB'];//其他补充说明
						$saveMap['firetrain']=$v['AC']=="消防培训"?1:0;//涪陵消防培训
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
								unset($saveMap['workstatus']); //正式员工
								unset($saveMap['transferdate']);//转正日期
							}
							if(!in_array('S', $fieldAll)){
								unset($saveMap['specialskill']);//特长
							}
							if(!in_array('V', $fieldAll)){
								unset($saveMap['agreetypeid']);//兼职
							}
							if(!in_array('W', $fieldAll)){
								unset($saveMap['agreetime']);//签约时间
							}
							if(!in_array('X', $fieldAll)){
								unset($saveMap['fingerprint']);//指纹
							}
							if(!in_array('Y', $fieldAll)){
								unset($saveMap['identity']);//身份证信息
							}
							if(!in_array('Z', $fieldAll)){
								unset($saveMap['iscommit']);//是否提交
							}
							if(!in_array('AA', $fieldAll)){
								unset($saveMap['staffnumber']);//保安证编号
							}
							if(!in_array('AB', $fieldAll)){
								unset($saveMap['remark']);//备注
							}
							if(!in_array('AC', $fieldAll)){
								unset($saveMap['firetrain']);//涪陵消防培训
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
						}else{  //操作类型是新增及修改
							if($list){ //修改数据
								$saveMap['id']=$list['id'];
								$saveMap['updateid']=$_SESSION[C('USER_AUTH_KEY')];
								$saveMap['updatetime']=time();
								$result=$MisHrBasicEmployeeModel->save($saveMap);
							}else{ //新增
								//自动生成订单编号
								$maxid = $maxid+1;
								$orderno = $scnmodel->GetRulesNO('mis_hr_personnel_person_info',$maxid);
								$saveMap['orderno']=$orderno;
								$saveMap['createid']=$_SESSION[C('USER_AUTH_KEY')];
								$saveMap['createtime']=time();
								$result=$MisHrBasicEmployeeModel->add($saveMap);
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
	}
	/**
	 * @Title: guiDutyImport
	 * @Description: todo(导入职级excel)
	 * @author renling
	 * @date 2013-6-26 下午4:16:09
	 * @throws
	 */
	public function guiDutyImport(){
		//职位表模型
		$DutyModel=D('Duty');
		//审批职务表
		$MisSystemDutyModel=D('MisSystemDuty');
		//读取配置字段
		$MisImportFieldconfigModel=D('MisImportSpecial');
		$MisImportFieldconfigList=$MisImportFieldconfigModel->where(" tablename='Duty' and status=1")->find();
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
			if($filetype=="xls" || $filetype=="xlsx"){
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
				foreach($dataList as $k=>$v){
					//把标题列除外
					if($i >= 1){
						//判断名称是否存在职务表
						$list = $DutyModel->where("status=1 and name='".$v['B']."'")->find();
						$MisSystemDutyList = $MisSystemDutyModel->where("status=1 and name='".$v['B']."'")->find();
						if($MisImportFieldconfigList){ //用户已配置导入字段
							if(in_array('A', $fieldAll)){
								$saveMap['code']=$v['A'];
							}
							if(in_array('B', $fieldAll)){
								$saveMap['name']=$v['B'];
							}
							if(in_array('C', $fieldAll)){
								$saveMap['level']=$v['C'];
							}
							$saveMap['companyid']=$company['id'];
							if(in_array('D', $fieldAll)){
								$saveMap['remark']=$v['D'];
							}
						}else{ //用户没有配置导入字段默认全部导入
							$saveMap['code']=$v['A'];
							$saveMap['name']=$v['B'];
							$saveMap['level']=$v['C'];
							$saveMap['companyid']=$company['id'];
							$saveMap['remark']=$v['D'];
						}
						if($operation=="add"){//操作类型是新增
							$saveMap['createid']=$_SESSION[C('USER_AUTH_KEY')];
							$saveMap['createtime']=time();
							$result=$DutyModel->add($saveMap); //插入职位表
							$MisSystemDutyModel->add($saveMap); //插入审批职位表
	
						}else{  //操作类型是新增及修改
							if($list){ //修改数据
								$saveMap['id']=$list['id'];
								$saveMap['updateid']=$_SESSION[C('USER_AUTH_KEY')];
								$saveMap['updatetime']=time();
								$result=$DutyModel->save($saveMap);
								$saveMap['id']=$MisSystemDutyList['id'];
								$MisSystemDutyModel->save($saveMap); //插入审批职位表
							}else{ //新增
								$saveMap['createid']=$_SESSION[C('USER_AUTH_KEY')];
								$saveMap['createtime']=time();
								$result=$DutyModel->add($saveMap);
								$MisSystemDutyModel->add($saveMap); //插入审批职位表
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
	}
	/**
	 * @Title: guiDutyImportConfig
	 * @Description: todo(配置导入职级字段)
	 * @author renling
	 * @date 2013-6-26 下午2:44:15
	 * @throws
	 */
	public function guiDutyImportConfig(){
		$importconfig=$_POST['importconfig'];
		//如果没有配置字段默认导入全部字段
		$importconfigMap='';
		$MisImportFieldconfigModel=D('MisImportSpecial');
		$MisImportFieldconfigList=$MisImportFieldconfigModel->where(" tablename='Duty' and status=1")->find();
		$fieldAll=explode(',', $MisImportFieldconfigList['fieldname']);
		$this->assign('fieldAll',$fieldAll);
		$this->assign('MisImportFieldconfigList',$MisImportFieldconfigList);
		//导入字段配置
		if($importconfig){
			$id=$_POST['id'];
			if($_POST['A']){
				$importconfigMap='A,';  //编码
			}
			if($_POST['B']){
				$importconfigMap.='B,';  //名称
			}
			if($_POST['C']){
				$importconfigMap.='C,';  //级别
			}
			if($_POST['D']){
				$importconfigMap.='D,';   //备注
			}
			$saveconfigMap['tablename']='Duty';
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
	 * @Title: importconfig
	 * @Description: todo(配置导入字段)
	 * @author renling
	 * @date 2013-6-21 下午2:58:29
	 * @throws
	 */
	public function guiImportconfig(){
		$importconfig=$_POST['guiImportconfig'];
		//如果没有配置字段默认导入全部字段
		$importconfigMap='';
		$MisImportFieldconfigModel=D('MisImportSpecial');
		$MisImportFieldconfigList=$MisImportFieldconfigModel->where(" tablename='MisSystemDepartment' and status=1")->find();
		$fieldAll=explode(',', $MisImportFieldconfigList['fieldname']);
		$this->assign('fieldAll',$fieldAll);
		$this->assign('MisImportFieldconfigList',$MisImportFieldconfigList);
		//导入字段配置
		if($importconfig){
			$id=$_POST['id'];
			if($_POST['A']){
				$importconfigMap='A,';  //编码
			}
			if($_POST['B']){
				$importconfigMap.='B,';  //部门名称
			}
			if($_POST['C']){
				$importconfigMap.='C,';  //上级部门名称
			}
			if($_POST['D']){
				$importconfigMap.='D,';   //备注
			}
			$saveconfigMap['tablename']='MisSystemDepartment';
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
	 * @Description: todo(导入部门excel)
	 * @author renling
	 * @date 2013-6-20 下午2:16:00
	 * @throws
	 */
	private function guiSysDepImport(){
		//读取配置字段
		$MisImportFieldconfigModel=D('MisImportSpecial');
		$MisImportFieldconfigList=$MisImportFieldconfigModel->where(" tablename='MisSystemDepartment' and status=1")->find();
		$fieldAll=explode(',', $MisImportFieldconfigList['fieldname']);
		//获得操作类型
		$operation=$_POST['operation'];
		import ( '@.ORG.UploadFile');
		if($_FILES){
			$upload = new UploadFile(); // 实例化上传类
			$upload->savePath = UPLOAD_PATH."system_department_excel/";//C('savePath'); // 上传目录
			$upload->saveRule = date("Y_m_d_H_i_s").rand(1000,9999);
			$upload->allowExts= array("xls","xlsx");
			if(!$upload->upload()) { // 上传错误提示错误信息
				$this->error($upload->getErrorMsg());
			}else{ // 上传成功 获取上传文件信息
				$info = $upload->getUploadFileInfo();
			}
			$filetype =end(explode(".",$info[0]['savename']));
			if($filetype=="xls" || $filetype=="xlsx"){
				import('@.ORG.PHPExcel.IOFactory', '', $ext='.php');
				$inputFileName = UPLOAD_PATH."system_department_excel/".$info[0]['savename'];
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
				//部门表模型
				$MisSystemDepartmentModel=D('MisSystemDepartment');
				$i=0;
				$parentMap=array();
				foreach($dataList as $k=>$v){
					//把标题列除外
					if($i >= 1){
						//判断名称是否存在部门表
						$list = $MisSystemDepartmentModel->where("status=1 and name='".$v['B']."'")->find();
						if($MisImportFieldconfigList){ //用户已配置导入字段
							if(in_array('A', $fieldAll)){
								$saveMap['code']=$v['A'];
							}
							if(in_array('B', $fieldAll)){
								$saveMap['name']=$v['B'];
							}
							if(in_array('C', $fieldAll)){
								if($v['C']){ //判断上级节点有没有值 没值默认为顶级节点
									if($parentMap[$v['C']]){
										$saveMap['parentid']=$parentMap[$v['C']]; //第一个默认为顶级节点
									}else{
										//部门表已存在上级部门记录
										$parentid = $MisSystemDepartmentModel->where("status=1 and name='".$v['C']."'")->find();
										$saveMap['parentid']=$parentid['id'];
									}
								}else{
									$saveMap['parentid']=0;
								}
							}
							$saveMap['companyid']=$company['id'];
							if(in_array('D', $fieldAll)){
								$saveMap['remark']=$v['D'];
							}
						}else{ //用户没有配置导入字段默认全部导入
							$saveMap['code']=$v['A'];
							$saveMap['name']=$v['B'];
							if($v['C']){ //判断上级节点有没有值 没值默认为顶级节点
								$saveMap['parentid']=$parentMap[$v['C']]; //第一个默认为顶级节点
							}else{
								$saveMap['parentid']=0;
							}
							$saveMap['companyid']=$company['id'];
							$saveMap['remark']=$v['D'];
						}
						if($operation=="add"){//操作类型是新增
							$saveMap['createid']=$_SESSION[C('USER_AUTH_KEY')];
							$saveMap['createtime']=time();
							$result=$MisSystemDepartmentModel->add($saveMap); //插入部门表
							//便于取parentid  键：名称 值：id
							$parentMap[$v['B']]=$result;
						}else{  //操作类型是新增及修改
							if($list){ //修改数据
								$saveMap['id']=$list['id'];
								$saveMap['updateid']=$_SESSION[C('USER_AUTH_KEY')];
								$saveMap['updatetime']=time();
								$result=$MisSystemDepartmentModel->save($saveMap);
								//便于取parentid  键：名称 值：id
								$parentMap[$v['B']]=$list['id'];
							}else{ //新增
								$saveMap['createid']=$_SESSION[C('USER_AUTH_KEY')];
								$saveMap['createtime']=time();
								$result=$MisSystemDepartmentModel->add($saveMap);
								//便于取parentid  键：名称 值：id
								$parentMap[$v['B']]=$result;
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
				unset($parentMap);
				$this->success("导入成功");
			}else {
				$this->error('文件格式不正确,请上传后缀为xls的文件!');
			}
		}
	}
	/**
	 * @Title: selectComany
	 * @Description: todo(查询公司信息)
	 * @author renling
	 * @date 2013-6-26 上午11:27:01
	 * @throws
	 */
	private function selectComany(){
		if($_POST['companyId']){
			$MisSystemCompanyModel=D('MisSystemCompany');
			$MisSystemCompanyList=$MisSystemCompanyModel->where("status=1 and id=".$_POST['companyId'])->find();
			$this->assign("MisSystemCompanyList",$MisSystemCompanyList);
		}
		$this->assign('companyId',$_POST['companyId']);
	}
	/**
	 * @Title: guiComany
	 * @Description: todo(引导页面公司注册信息)
	 * @author renling
	 * @date 2013-6-26 上午10:07:54
	 * @throws
	 */
	private function guiComany(){
		$step1=$_POST['res'];
		$MisSystemCompanyModel=D('MisSystemCompany');
		$saveComMap['code']=$_POST['code'];
		$saveComMap['juridicalperson']=$_POST['juridicalperson'];
		$saveComMap['name']=$_POST['name'];
		$saveComMap['ename']=$_POST['ename'];
		$saveComMap['address']=$_POST['address'];
		$saveComMap['website']=$_POST['website'];
		$saveComMap['tel']=$_POST['tel'];
		$saveComMap['address']=$_POST['address'];
		$saveComMap['picture']=$_POST['picture'];
		if($_POST['companyId']){ //修改
			if($step1){
				$saveComMap['id']=$_POST['companyId'];
				$saveComMap['updatetime']=time();
				$saveComMap['updateid']=$_SESSION[C('USER_AUTH_KEY')];
				$result=$MisSystemCompanyModel->save($saveComMap);
			}
			$this->assign('companyId',$_POST['companyId']);
		}else{ //增加
			$saveComMap['createtime']=time();
			$saveComMap['createid']=$_SESSION[C('USER_AUTH_KEY')];
			$_POST['companyId'] = $result=$MisSystemCompanyModel->add($saveComMap);
			$this->assign('companyId',$result);
		}
		//echo $MisSystemCompanyModel->getLastSql();
		if ($result === false) {
			$this->error('填入信息有误！');
			exit;
		} else {
			$this->transaction_model->commit();//事务提交
		}
	}
	/**
	 * @Title: notice
	 * @Description: todo(公司公告)
	 * @author 杨东
	 * @date 2013-3-28 下午3:22:56
	 * @throws
	 */
	private function notice(){
		$list = D('MisNotice')->getNoticeList();
		$this->assign('noticelist',$list);
	}
	

	//定义一个全局变量
	private $md="";
	
	/**
	 * @Title: abcd
	 * @Description: todo(查询所有6天内的待审单据)
	 * @author liminggang
	 * @date 2013-7-8 下午1:39:54
	 * @throws
	 */
	public function lookupOnselfReceipts(){
		$type=$_REQUEST['type'];
		$arrTrue = array();
		$file = DConfig_PATH . "/System/ProcessModelsConfig.inc.php";
		$list = require $file;
		$tree = array();
		$onemodel="";
		$selectedid=0;
		foreach ($list as $k => $v) {
			if ($_SESSION["a"] == 1 || $_SESSION[$v['session']."_index"]) {
				$masmodel = D($v['model']);
				$maps = array();
				//新建或者审核中
				$maps['status'] =1;
				if( !isset($_SESSION['a']) ){
					if( $_SESSION[strtolower($v['model'].'_index')]==2 ){////判断部门及子部门权限
						$maps["createid"]=array("in",$_SESSION['user_dep_all_child']);
					}else if($_SESSION[strtolower($v['model'].'_index')]==3){//判断部门权限
						$maps["createid"]=array("in",$_SESSION['user_dep_all_self']);
					}else if($_SESSION[strtolower($v['model'].'_index')]==4){//判断个人权限
						$maps["createid"]=$_SESSION[C('USER_AUTH_KEY')];
					}
				}
				$time=time()-60*60*24*6;  //相差6天的时间戳
				$maps['createtime'] =array('egt',$time);
				$maps['auditState']=array("exp","< 3 and auditState >-1");
				$count = $masmodel->where($maps)->count('id');
				if($count<1){
					continue;
				}
				if(!$onemodel){
					$onemodel = $v['model'];
					$selectedid = $k+1;
				}
				$tree[] = array(
						'id' => $k+1,
						'pId' => $v['level'],
						'name' => $v['name'].'('.$count.')',
						'title' => $v['name'].'('.$count.')',
						'rel' => "lookupOnselfReceiptsBox",
						'icon' => "__PUBLIC__/Images/icon/order_missionwait.png",
						'target' => 'ajax',
						'url' => '__URL__/lookupOnselfReceipts/md/'.$v['model'].'/type/1/jump/1',
						'open' => false
				);//待审任务根节点
				$arrTrue[$v['level']]= 1;
			}
		}
		$model=M("group");
		$list2 = $model->where("status=1")->order("sorts asc")->select();
		foreach ($list2 as $k => $v) {
			$id = -$v['id'];
			if($arrTrue[$id]){
				$tree[] = array(
						'id' => $id,
						'pId' => 0,
						'name' => $v['name'],
						'title' => $v['name'],
						'icon' => "__PUBLIC__/Images/icon/order_missionwait.png",
						'open' => true
				);//待审任务根节点
			}
		}
		$ztreess=json_encode($tree);
		$this->assign('ztrees',$ztreess);
		//类型树构造完毕，查询数据
	
		$md=$_REQUEST['md'];
		if(!$md){
			$name=$onemodel;
			// 			$this->md=$name;
			$this->assign('selectedid',$selectedid);
		}else{
			$name=$md;
			// 			$this->md=$name;
		}
	
		$this->assign('md',$name);
		$this->_list($name, $maps);
	
		if ($_REQUEST['jump']) {
			$this->display('Index:lookupOnselfReceiptsList');
		} else {
			$this->display('Index:lookupOnselfReceipts');
		}
	}
	/**
	 * @Title: lookupOnselfReceiptsResult
	 * @Description: todo(查询所有6天内的待审单据)
	 * @author liminggang
	 * @date 2013-7-8 下午1:39:54
	 * @throws
	 */
	public function lookupOnselfReceiptsResult(){
		$arrTrue = array();
		$file = DConfig_PATH . "/System/ProcessModelsConfig.inc.php";
		$list = require $file;
		$tree = array();
		$onemodel=array();
		$selectedid=0;
		foreach ($list as $k => $v) {
			if ($_SESSION["a"] == 1 || $_SESSION[$v['session']."_index"]) {
				$masmodel = D($v['model']);
				$maps = array();
				//审核完毕或者被打回
				$maps['status'] =1;
	
				if( !isset($_SESSION['a']) ){
					if( $_SESSION[strtolower($v['model'].'_index')]==2 ){////判断部门及子部门权限
						$maps["createid"]=array("in",$_SESSION['user_dep_all_child']);
					}else if($_SESSION[strtolower($v['model'].'_index')]==3){//判断部门权限
						$maps["createid"]=array("in",$_SESSION['user_dep_all_self']);
					}else if($_SESSION[strtolower($v['model'].'_index')]==4){//判断个人权限
						$maps["createid"]=$_SESSION[C('USER_AUTH_KEY')];
					}
				}
				$time=time()-60*60*24*6;  //相差6天的时间戳
				$maps['createtime']=array('egt',$time);
				$maps['auditState']= array("exp","=-1 or auditState=3");
				$count = $masmodel->where($maps)->count('id');
				if($count<1){
					continue;
				}
				if(!$onemodel){
					$onemodel = $v['model'];
					$selectedid = $k+1;
				}
				$tree[] = array(
						'id' => $k+1,
						'pId' => $v['level'],
						'name' => $v['name'].'('.$count.')',
						'title' => $v['name'].'('.$count.')',
						'rel' => "lookupOnselfReceiptsResultBox",
						'icon' => "__PUBLIC__/Images/icon/order_missionwait.png",
						'target' => 'ajax',
						'url' => '__URL__/lookupOnselfReceiptsResult/md/'.$v['model'].'/type/1/jump/1',
						'open' => false
				);//待审任务根节点
				$arrTrue[$v['level']]= 1;
			}
		}
		$model=M("group");
		$list2 = $model->where("status=1")->order("sorts asc")->select();
		foreach ($list2 as $k => $v) {
			$id = -$v['id'];
			if($arrTrue[$id]){
				$tree[] = array(
						'id' => $id,
						'pId' => 0,
						'name' => $v['name'],
						'title' => $v['name'],
						'icon' => "__PUBLIC__/Images/icon/order_missionwait.png",
						'open' => true
				);//待审任务根节点
			}
		}
		$ztreess=json_encode($tree);
		$this->assign('ztrees',$ztreess);
		//类型树构造完毕，查询数据
	
		$md=$_REQUEST['md'];
		if(!$md){
			$name=$onemodel;
			//$this->md=$name;
			$this->assign('selectedid',$selectedid);
		}else{
			$name=$md;
			//$this->md=$name;
		}
	
		$this->assign('md',$name);
		$this->_list($name, $maps);
	
		if ($_REQUEST['jump']) {
			$this->display('Index:lookupOnselfReceiptsList_result');
			exit;
		} else {
			$this->display('Index:lookupOnselfReceiptsListResult');
		}
	}
	public function _after_list(&$voList){
		$name=$this->md;
		foreach($voList as $key=>$val){
			$map=array();
			$map['tablename'] = $name;
			$map['tableid'] = $val['id'];
			$map['auditState'] = 1;
			$model=M("process_info_history");
			$list=$model->where($map)->field('dotime')->find();
			$voList[$key]['dotime'] = $list['dotime'];
			$map=array();
			$map['tablename'] = $name;
			$map['tableid'] = $val['id'];
			$map['auditState'] = $val['auditState'];
			$user=$model->where($map)->field('userid,dotime')->find();
			$voList[$key]['dousername'] = getFieldBy($user['userid'],'id','name','User');
			$voList[$key]['dousertime'] = $user['dotime'];
		}
	}
	/*
	 * @yangxi
	* 20120906
	* remark:将用户加入到对应的基础权限组
	* @parameter  string  $userId    用户ID
	* @parameter  array   $roleGroup 权限组
	* return      boolen  $result    结果
	*/
	protected function addRole($userId,$roleGroup="") {
		if($roleGroup!=""){
			foreach($roleGroup as $key => $value){
				//新增用户自动加入相应权限组
				$RoleUser = M("role_user");
				$RoleUser->user_id	=	$userId;
				// 默认加入高级权限组
				$RoleUser->role_id	=	$value;
				$result=$RoleUser->add();
				if(!$result){
					$this->error(L('_ERROR_'));
				}
			}
			return $result;
		}
	}
	
	/*
	 * @yangxi
	* 20120906
	* remark:将用户加入到对应的高级权限组
	* @parameter  string  $userId    用户ID
	* @parameter  array   $roleGroup 权限组
	* return      boolen  $result    结果
	*/
	protected function addRolegroup($userId,$roleGroup="") {
		if($roleGroup!=""){
			foreach($roleGroup as $key => $value){
				//新增用户自动加入相应权限组
				$RolegroupUser = M("rolegroup_user");
				$RolegroupUser->user_id	=	$userId;
				// 默认加入高级权限组
				$RolegroupUser->rolegroup_id	=	$value;
				$result=$RolegroupUser->add();
				if(!$result){
					$this->error(L('_ERROR_'));
				}
			}
			return $result;
		}
	}
	/*
	 * @jiangx
	* 将此函数从Public 移到Index
	* date 2013-11-04
	*/
	function clear_cache(){
		import ( '@.ORG.Dir' );
		Dir::delDir(RUNTIME_PATH);
		$this->success('删除缓存成功！');
	}
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
	 *
	 * @Title: lookupremind
	 * @Description: todo(修改首页提醒中心提醒事项 以及报表显示)
	 * @author renling
	 * @date 2014-3-28 下午3:40:08
	 * @throws
	 */
	public function lookupremind(){
		$userModel=D('User');
		$userdata['id']=$_SESSION[C('USER_AUTH_KEY')];
		$userdata['status']=1;
		//修改报表默认显示
		if($_REQUEST['step']=="report"){
			$userdata['reportmethod']=$_POST['reportmethod'];
		}else{
			$userdata['remindid']=implode(',', $_POST['remindid']);
		}
		$userRemindResult=$userModel->save($userdata);
		if($userRemindResult){
			$this->success("修改成功");
		}else{
			$this->error("修改失败");
		}
	}

	
	/**
	 * @Title: rollAnnouncement
	 * @Description: todo(滚动公告)
	 * @author libo
	 * @date 2014-1-13 下午3:24:48
	 * @throws
	 */
	public function rollAnnouncement(){
	
	}
}
?>