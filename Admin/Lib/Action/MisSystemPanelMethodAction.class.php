<?php
/**
 * @Title: MisSystemPanelAction
 * @Package 基础配置-面板管理：系统面板管理
 * @Description: TODO()
 * @author jiangx
 * @company 重庆特米洛科技有限公司
 * @copyright 重庆特米洛科技有限公司
 * @date 2013-10-10 16:18:54
 * @version V1.0
 */
class MisSystemPanelMethodAction extends Action {
	function _initialize(){
		$this->getcompanylogo();
	}
	/**
	 * @Title: getcompanylogo
	 * @Description: todo(获取公司logo)
	 * @param unknown_type $companyid
	 * @author yuansl
	 * @date 2014-6-19 下午4:05:30
	 * @throws
	 */
	private function getcompanylogo($companyid){
		$model = D("MisSystemCompany");
		if($companyid){
			$map['id'] = $companyid;
		}
		$map['status'] = array('eq',1);
		$Copany_Info = $model->where($map)->find();
		$this->assign("Copany_Info",$Copany_Info);
	}
	/**
	 * @Title: onlineManager
	 * @Description: todo(文档在线查看方法)
	 * @author liminggang
	 * @num 数据条数
	 * @userid 用户ID；如果为0则为当前用户
	 * @myself true查询我的文档；false查询共享文档
	 * @date 2013-12-30 下午3:18:36
	 * @throws
	 */
	private function onlineManager($num=5,$userid=0 ,$myself=false){
		//最新文档前10条
		$filemanagerlist=A("MisFileManager")->lookupgetNewestFile($num,$userid,$myself);
		// 		dump($filemanagerlist);
		//可查看的类型
		$typeext=C("TRANSFORM_SWF");
		$typeext1=C("IMG_file");
		$typeext[] = 'pdf';
		$typeext = array_merge($typeext, $typeext1);
		foreach($filemanagerlist as $key => $val){
			$file = "MisFileManager/".$val['filepath'];
			$filemanagerlist[$key]['codeurl'] = base64_encode($file);
			//获取文件后缀
			$exten =strtolower(pathinfo($file, PATHINFO_EXTENSION));
			if($exten){
				$filemanagerlist[$key]['name']=missubstr(str_replace(".".$exten,"",$val['name']),25,true).'.'.$exten;
			}else{
				$filemanagerlist[$key]['name'] = missubstr($val['name'],25,true);
			}
			$filemanagerlist[$key]['online'] = false;
			//规定的文件才能在线查看
			if(in_array($exten, $typeext)){
				$filemanagerlist[$key]['online'] = true;
			}
		}
		$this->assign('snlist',$filemanagerlist);
	}

	public function index(){
		$panlist=D('MisSystemPanel')->lookupuserpanel();
		$this->assign("panlist",$panlist);
		
		//弹出窗报表
		//在线文档查看模块方法
		//$this->onlineManager();
		//$this->onlineleftManager();
		$this->lookupDisReport();
		//提醒中心
		$this->lookupmyRemind();
		//公告部分
		$this->missystemnotice();
		//我的日程管理
		$this->myEvents();

		// 个人事务
		$common = A("Common");

		// 邮件消息
		$msg= $common->getCurrentMsg(2);
		// 报告消息
		$MisWorkStatementModel=D('MisWorkReadStatementView');
		//获取待阅工作报告条件
		$map = array();
		$map['mis_work_read_statement.userid']=$_SESSION[C('USER_AUTH_KEY')];
		$map['mis_work_read_statement.status'] = 1;
		$map['mis_work_read_statement.readstatus'] = 1; //查阅状态为，未查阅
		//待阅工作报告条数
		$msg["readstatement"]=$MisWorkStatementModel->where($map)->count("*");
		// 任务动态
		$mistaskModel = D("MisTaskInformationView");
		$taskmap = array();
		$taskmap['executingstatus'] = array('NEQ', 7);
		$taskmap['status'] = 1;
		//执行的任务
		if( !isset($_SESSION['a']) ){
			if( $_SESSION[strtolower('MisTaskInformation_index')]==2 ){////判断部门及子部门权限
				$taskmap["mis_task_information.createid"]=array("in",$_SESSION['user_dep_all_child']);
			}else if($_SESSION[strtolower('MisTaskInformation_index')]==3){//判断部门权限
				$taskmap["mis_task_information.createid"]=array("in",$_SESSION['user_dep_all_self']);
			}else if($_SESSION[strtolower('MisTaskInformation_index')]==4){//判断个人权限
				$taskmap["mis_task_information.createid"]=$_SESSION[C('USER_AUTH_KEY')];
			}
		}
		$taskmap['executeuser'] = $_SESSION[C('USER_AUTH_KEY')];
		if ($_SESSION['a'] || $_SESSION['mistaskadscription_edit']) {
			$msg["releasetask"] = $mistaskModel->where($taskmap)->count("*");
		}

		//清空上面的条件
		$taskmap = array();
		$taskmap['status'] = 1;
		//创建的任务
		$taskmap['pid'] = 0;
		$taskmap['createid'] = $_SESSION[C('USER_AUTH_KEY')];
		if($_SESSION['a'] || $_SESSION['mistaskinformation_edit']){
			$msg["performtask"] = $mistaskModel->where($taskmap)->count("*");
		}
		// 日程
		$model = D('MisUserEvents');
		$starttime = strtotime(date('Y-m-d', time()));
		$endtime = strtotime(date('Y-m-d', time())) + 24 * 60 * 60;
		//获取个人日常条数
		$msg["myevents"] = $model->where('FIND_IN_SET(  '.$_SESSION[C('USER_AUTH_KEY')].', personid ) and status = 1 and enddate>'.time().' and startdate < '.$endtime)->count("*");
		//获取协同日常条数
		$msg["releaseevents"] = $model->where('userid = '.$_SESSION[C('USER_AUTH_KEY')].' and status = 1 and enddate>'.time().' and startdate < '.$endtime)->count("*");
		//获取工作审批。待我审核数据信息
		$arr=getTaskulous();

		$daibanNum = count($arr);
		$moduleNameList=array();
		foreach( $arr as $k=>$v ){
			$auditTimeOut= "";
			//计算推送时间到至今有多少小时。判断是否即将超时。
			$hours=getCHStimeDifference($v['createtime'],time(),"H");
			//获取系统中预留的超时提醒
			$referHours = C("AUDIT_TIMEOUT");
			//超期提前提醒时间
			$remindHours =C("AUDIT_TIMEOUT_REMIND");
			$countH = $hours+$remindHours;
			if($countH>=$referHours){
				$auditTimeOut="即将超期";
				if($hours>=$referHours){
					$auditTimeOut="已超期";
				}
			}
			if(in_array($v['tablename'], array_keys($moduleNameList))){
				if($v['createtime']>$moduleNameList[$v['tablename']]['createtime']){
					$moduleNameList[$v['tablename']]['createtime'] = $v['createtime'];
				}
				if($moduleNameList[$v['tablename']]['auditTimeOut'] != "已超期" && $auditTimeOut){
					$moduleNameList[$v['tablename']]['auditTimeOut'] = $auditTimeOut;
				}
				$moduleNameList[$v['tablename']]['count'] = $moduleNameList[$v['tablename']]['count']+1;

			}else{
				$moduleNameList[$v['tablename']] = array(
						'createtime'=>$v['createtime'],
						'title'=>"[审批]".getFieldBy($v['tablename'], 'name', 'title', 'node'),
						'url'=>"__APP__/MisWorkExecuting/index/jump/3/md/MisWorkMonitoring/worktype/1/rel/MisWorkExecutingbox/mytodoname/".$v['tablename'],
						'count'=>1,
						'auditTimeOut'=>$auditTimeOut,
				);
			}
		}
		//工作审批 end

// 		//审批后执行数组
// 		$Model = M();
// 		//查询 可执行的 模块 名称
// 		$isexecutoridSql="SELECT a.`name`,a.cname,b.executorid AS executorida,b.turnexecutorid,p.executorid
// 				FROM mis_work_executing_set AS a
// 				LEFT JOIN mis_work_executing AS b ON a. NAME = b.tablename
// 				LEFT JOIN process_info as p ON p.id = b.ptmptid
// 				WHERE dotype <> 1 and a.status=1";
// 		$isexecutoridArr=$Model->query($isexecutoridSql);
// 		$userid=$_SESSION[C('USER_AUTH_KEY')];//当前登录人id
// 		$executorid=array();//执行人 数组
// 		$workExecutor=array();//定义 可执行的tablename
// 		foreach ($isexecutoridArr as $k=>$v){
// 			$executorid=array_merge(explode(",", $v['executorida']),explode(",", $v['turnexecutorid']),explode(",", $v['executorid']));
// 			if(in_array($userid, $executorid) || isset($_SESSION['a']) ){//当前登录人在执行人数组中
// 				$workExecutor[]=$v['name'];
// 			}
// 		}
// 		//构造 查询 要处理的数量sql
// 		$numsql.="SELECT COUNT(tablename) AS num, tablename, b.executorid AS executoridb, b.turnexecutorid,p.executorid
// 				FROM mis_work_executing_set AS a
// 				LEFT JOIN mis_work_executing AS b ON a. NAME = b.tablename
// 				LEFT JOIN process_info AS p ON b.ptmptid = p.id
// 				WHERE (dotype <> 1)";
// 		if(!isset($_SESSION['a'])){//如果不是管理员 过滤条件
// 			$numsql.="AND ( ( FIND_IN_SET('$userid', turnexecutorid) )
// 			OR ( FIND_IN_SET('$userid', b.executorid) )
// 			OR ( FIND_IN_SET('$userid', p.executorid) ) ) ";
// 		}
// 		$numsql.="GROUP BY tablename";
// 		//获取 需要处理的数量 数组
// 		$numlist=$Model->query($numsql);
// 		//查询 信息sql
// 		$workmap['name']=array('in',$workExecutor);
// 		$workmap['dotype']=array('neq',1);
// 		$workSet=M("mis_work_executing_set");
// 		//数组
// 		$arrlist=$workSet->field("mis_work_executing_set.id,name,b.createtime,cname,dotype")->where($workmap)->join(" LEFT JOIN mis_work_executing as b ON b.tablename =mis_work_executing_set.`name`")->group("tablename")->select();
// 		//组装数量到数组中
// 		foreach ($numlist as $k=>$v){
// 			foreach ($arrlist as $k1=>$v1){
// 				if($v['tablename'] == $v1['name']){
// 					$moduleNameList[$v1['name'].$k1] = array(
// 							'createtime'=>$v1['createtime'],
// 							'title'=>"[待办]".$v1['cname'],
// 							'url'=>"__APP__/MisWorkExecuting/index/jump/4/md/".$v1['name']."/dotype/0/rel/".$v1['name']."_1",
// 							'count'=>$v['num'],
// 					);
// 				}
// 			}
// 			$daibanNum +=$v['num'];//待办总数量
// 		}
// 		//工作协同待办  查询待办事项条数
// 		$MisOaFlowsInstanceModel = D("MisOaFlowsInstance");
// 		$oamapdoing['flowsuser']=$_SESSION[C('USER_AUTH_KEY')];
// 		$oamapdoing['status']=1;
// 		$oamapdoing['dostatus'] = array('lt',2);
// 		$oaitemdoing=$MisOaFlowsInstanceModel->where($oamapdoing)->order("createtime desc")->select();
// 		if($oaitemdoing){
// 			$moduleNameList["MisWorkExecutingbox"] = array(
// 					'createtime'=>$oaitemdoing[0]['createtime'],
// 					'title'=>"[协同]工作协同",
// 					'url'=>"__APP__/MisWorkExecuting/index/jump/5/md/MisOaItems/type/3/rel/MisWorkExecutingbox",
// 					'count'=>count($oaitemdoing),
// 			);
// 		}
// 		//对数组进行排序
// 		rsort($moduleNameList);
// 		$msg["mytodo"] = $moduleNameList;
// 		$daibanNum +=count($oaitemdoing);//待办总数量
// 		if($daibanNum > 99){
// 			$daibanNum = '99+';
// 		}
// 		$this->assign("daibanNum",$daibanNum);
// 		// 会议
// 		$meetingmap['mis_oa_meeting_person.userid'] = $_SESSION[C('USER_AUTH_KEY')];//当前用户
// 		$time = strtotime(date('Y-m-d', time()));
// 		$meetingmap['mis_oa_meeting_manage.starttime'] = array("gt",$time);// 今天以前的
// 		$meetingmap['mis_oa_meeting_manage.ostatus'] = 0;//未开始
// 		$mModel = D("MisOaMeetingPersonView");
// 		$msg["meeting"] = $mModel->where($meetingmap)->count("*");
			
// 		$this->assign("msgs",$msg);
// 		//待办事物和提醒中心处理完成end
		//修改首页待处理工作 新版
		$MisWorkExecutingAction=A("MisWorkExecuting");
		$MisWorkExecutingAction->getlefttree();


		$mappanel = array();
		$mappanel['status'] = 1;
		$model = D("MisSystemPanel");
		$map = array();
		$map['status'] = 1;
		$map['userid'] = $_SESSION[C('USER_AUTH_KEY')];
		$userRoleModel = D("MisSystemPanelUserRole");
		$uerrole = $userRoleModel->where($map)->find();
		unset($map['userid']);
		$map['departmentid'] = $_SESSION['user_dep_id'];
		$departmentRoleModel = D("MisSystemPanelDepartmentRole");
		$departmentrole = $departmentRoleModel->where($map)->find();
		$panlerole = "0";
		//过滤没有授权的 面板 dirc
		if ($uerrole['panlerole'] && $departmentrole['panlerole']) {
			$panlerole .= ','. $uerrole['panlerole'] .','. $departmentrole['panlerole'];
		}elseif (!$uerrole['panlerole'] && $departmentrole['panlerole']) {
			$panlerole .= ','.  $departmentrole['panlerole'];
		}elseif ($uerrole['panlerole'] && !$departmentrole['panlerole']) {
			$panlerole .= ','.  $uerrole['panlerole'];
		}
		// 执行过滤
		$mappanel['id'] = array('in', $panlerole);
		if (!$uerrole && !$departmentrole) {
			unset($mappanel['id']);
			$mappanel['isbasepanel'] = 1;
			$panellist = $model->where($mappanel)->order('sort ASC')->select();
		}else {
			$panellist = $model->where($mappanel)->order('sort ASC')->select();
		}
		//测试点
		// 		dump($panellist);
		//查出排序
		$mPanelUserSort = D("MisSystemPanelUsersort");
		$aMap = array();
		$aMap['status'] = 1;
		$aMap['userid'] = $_SESSION[C('USER_AUTH_KEY')];
		$userSort = $mPanelUserSort->where($aMap)->find();
		$userSortarr = array();
		if ($userSort['panelsort']) {
			$sort = explode(',', $userSort['panelsort']);
			foreach ($sort as $val) {
				$valarr = explode('-', $val);
				$userSortarr[$valarr[0]] = $valarr[1];
			}
		}
		//按顺序重组数据
		$panellistnew = array();
		foreach ($panellist as $key => $val) {
			if ($userSortarr[$val['id']]) {
				$val['sort'] = $userSortarr[$val['id']];
				$panellistnew[$userSortarr[$val['id']]] = $val;
			} else {
				if ($val['sort']) {
					$panellistnew['100'.$val['sort']] = $val;
				} else {
					$panellistnew['101'.$key] = $val;
				}
			}
		}
		ksort($panellistnew);
		$pnnelhtmllist = array();
		foreach ($panellistnew as $key => $val) {
			if ($val['methodname'] == 'mistaskinfo') {
				if ($_SESSION['a'] != 1  && (!$_SESSION['mistaskinformation_edit'])) {
					continue;
				}
			}
			if (method_exists($this, $val['methodname'])) {
				if (!$val['color'] || $val['color'] == 'grd-white') {
					$val['color'] = 'grd-white';
				} else {
					$val['color'] .= ' tml-c-white';
				}
				$pnnelhtmllist[] = $this->$val['methodname']($val['name'], $val['id'].'-'.$val['sort'], $val['color']);
			}
		}
		$this->assign("pnnelhtmllist", $pnnelhtmllist);
	}
	/**
	 * @Title: userinterface
	 * @Description: todo(首页登录---弹出的浮层---待办任务)
	 * @author yuansl
	 * @date 2014-3-29 下午2:35:44
	 * @throws
	 */
	public function userinterface(){
		$WORKNUM =  10;//配置显示条数 审核进度跟踪模块
		$MisWorkMonitoringViewModel = D("MisWorkMonitoringView");
		$MisWorkMonitoringModel = D("MisWorkMonitoring");
		$map['dostatus'] = 0;//代表待办任务strtolower
		if(!$_SESSION["a"]==1){
			$map['_string'] = 'FIND_IN_SET(  '.$_SESSION[C('USER_AUTH_KEY')].', curNodeUser )';
		}
		$map['node.status'] = 1;
		//选项卡   ["MisBusinessContractFixation"] => "固定业务合同"
		$MisWorkMonitoringNameList = $MisWorkMonitoringViewModel->where($map)->getField("name,title",true);
		$MisWorkMonitoringNameList = array_unique($MisWorkMonitoringNameList);
		//组合url
		$selectlist = array();
		$tempx = array();
		foreach($MisWorkMonitoringNameList as $key=>$val){
			$mapc['tablename'] = $key;
			$mapc['dostatus'] = 0;
			$tempx['countnumx'] = $MisWorkMonitoringModel->where($mapc)->count();//组合进入数量
			//echo $MisWorkMonitoringModel->getLastSql();
			$tempx['title'] = $val."待办";
			$tempx['name'] = $key;
			$tempx['url'] = "__APP__/MisSystemPanelMethod/userinterface".'/typename/'.$key;
			if($tempx['countnumx'] >= 1){
				array_push($selectlist,$tempx);//数据条数为零的将选项卡将被摒弃.dirc 2014.4.12 9:48 yuan
			}
			$tempx = array();//清空
		}
		//待办完整选项卡$remindcards
		$remindcards = $selectlist;
		//组合提醒选项卡 并合并 提醒和待办选项卡
		$userModel=D('user');
		$userMap['id']=$_SESSION[C('USER_AUTH_KEY')];
		$userMap['status']=1;
		$userRemindList=$userModel->where($userMap)->find();
		$remindList=require DConfig_PATH."/System/remindconfig.inc.php";
		//当前时间
		$time=time();
		foreach ($remindList as $key=>$val){
			if($userRemindList['remindid']){
				if( in_array($val['sortnum'], explode(',',$userRemindList['remindid'])) && $_SESSION[$val['authmodule']] || $_SESSION['a'] ) {
					//查询满足条件条数
					if($val['modulename']){
						$model=D($val['modulename']);
					}
					//替换map中当前时间$time
					if($val['map']){
						$val['map']=str_replace('$time', $time,$val['map']);
					}
					$count=$model->where($val['map'])->count('*');//有任务提醒数据,就将它传到页面上作为选项卡
					if($count >0){//除去没有信息记录的条数
						$cardslist[] =array(//构造选项卡
								'countnumx'=>$count,
								'title'=>$val['title']."提醒",
								'name'=>$val['modulename'],
								'url'=>"__APP__/MisSystemPanelMethod/userinterface".'/typename/'.$val['modulename'],
								'rel'=>$val['relmodule']
						);
					}
				}
			}else{
				if($_SESSION[$val['authmodule']]||$_SESSION['a']) {
					//查询满足条件条数
					if($val['modulename']){
						$model=D($val['modulename']);
					}
					if($val['map']){
						//替换map中当前时间$time
						$val['map']=str_replace('$time', $time,$val['map']);
					}
					$count=$model->where($val['map'])->count('*');//有任务提醒的才会将它传到页面上作为选项卡
					$keyi = 0;
					if($count >0){
						$cardslist[] =array(//构造选项卡
								'countnumx'=>$count,
								'title'=>$val['title']."提醒",
								'name'=>$val['modulename'],
								'url'=>"__APP__/MisSystemPanelMethod/userinterface".'/typename/'.$val['modulename'],
								'rel'=>$val['relmodule']
						);
					}
				}//用户判断结束
			}//else结束
		}//foreach 结束
		$this->assign('remindarr',json_encode($cardslist));
		//合并两个选项卡数组
		foreach ($cardslist as $vaxb){
			array_push($selectlist,$vaxb);
		}
		$newselectlist = array();
		$keyi = 1;
		//为选项卡url再组合上参数
		foreach($selectlist as $newvaxb){
			$newvaxb['keyi'] = $keyi;
			$newvaxb['url'] = $newvaxb['url']."/keyi/".$newvaxb['keyi'];
			array_push($newselectlist,$newvaxb);
			$keyi++;
		}
		//完整的组合选项卡数据
		$this->assign('selectlist',$newselectlist);
		$this->assign('waitearr',json_encode($newselectlist));
		//构造选项卡结束

		//获取待办选项卡中model name集合
		$cardsa = array();
		foreach($cardslist as $carda){
			array_push($cardsa,$carda['name']);
		}
		//点击选项卡获取typename
		$typeName = $_REQUEST['typename'];
		if($_REQUEST['typename']){
			//如果该typename 不属于提醒中心
			if(!in_array($typeName,$cardsa)){
				//这里是进入待办读取数据
				$this->getWaiteData($typeName,$newselectlist);
			}else{
				//这里进入提醒读取数据
				$this->getRemindData($typeName,$newselectlist);
			}
		}else{//如果!$_REQUEST['typename'] 就获取选项卡第一个
			if(!in_array($newselectlist[0]['name'],$cardsa)){
				//这里是进入待办读取数据
				$this->getWaiteData($newselectlist[0]['name'],$newselectlist);
			}else{
				//这里进入提醒读取数据
				$this->getRemindData($newselectlist[0]['name'],$newselectlist);
			}
		}

		$this->display();
	}
	/**
	 * @Title: getWaiteData
	 * @Description: todo(首页浮层 ---获取待办数据)
	 * @param unknown_type $typename
	 * @author yuansl
	 * @date 2014-5-20 下午3:24:12
	 * @throws
	 */
	private function getWaiteData($typeName,$newselectlist){
		//刷新判断值
		foreach($newselectlist as $valx){
			if($valx['name'] == $typeName ){
				$this->assign("mark",$valx['keyi']);
				break;
			}
		}

		$WORKNUM =  10;//配置显示条数 审核进度跟踪模块
		$MisWorkMonitoringViewModel = D("MisWorkMonitoringView");
		$MisWorkMonitoringModel = D("MisWorkMonitoring");
		//未办任务
		$map['dostatus'] = 0;
		if(!$_SESSION["a"]==1){
			$map['_string'] = 'FIND_IN_SET(  '.$_SESSION[C('USER_AUTH_KEY')].', curNodeUser )';
		}
		$map['node.status'] = 1;
		$MisWorkMonitoringNameList = $MisWorkMonitoringViewModel->where($map)->getField("name",true);
		$MisWorkMonitoringNameList = array_unique($MisWorkMonitoringNameList);
		//如果typename存在于待办中,就把待办数据拿出来(并非工作表数据)
		unset($map);
		$map['name'] = $typeName;
		$map['dostatus'] = 0;
		$MisWorkMonitoringList = $MisWorkMonitoringViewModel->where($map)->select();
		//集合tableid
		$collection_tableid = array();
		foreach($MisWorkMonitoringList as $vmsi){
			$posiid = $vmsi['tableid'];
			array_push($collection_tableid,$posiid);
		}
		$maps['status'] =1;
		$maps['id'] = array('in',$collection_tableid);
		//查询出当前选项卡typename下的数据
		$tempmode = D($typeName);
		$templist = $tempmode->where($maps)->select();
		//echo $tempmode->getLastSql();
		//为每条数据组合双击事件跳转的url..
		$newtemplist = array();
		foreach ($templist as $vtep){
			$vtep['url'] = "__APP__/".$typeName."/auditEdit/id/".$vtep['id'];
			$vtep['target'] = 'navTab';
			$vtep['rel'] = $typeName;
			$vtep['tableid'] = $typeName."auditEdit";
			$vtep['title'] = getFieldBy($typeName, 'name', 'title', 'node')."_审核";
			array_push($newtemplist,$vtep);
		}
		//完整的数据列表
		$newMisWorkMonitoringList = $newtemplist;
		$this->assign('MisWorkMonitoringList',$newMisWorkMonitoringList);
		//这是根据这个根据模型名称分别读取每个选项卡下面的数据列的title 配置文件
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($typeName);
		//除去审核状态的显示
		array_pop($detailList);
		array_pop($detailList);
		$this->assign('detailList',$detailList);
	}
	/**
	 * @Title: getRemindData
	 * @Description: todo(获取提醒数据)
	 * @param unknown_type $typename
	 * @author yuansl
	 * @date 2014-5-20 下午3:33:39
	 * @throws
	 */
	private function getRemindData($typename,$newselectlist){
		//刷新判断值
		foreach($newselectlist as $valx){
			if($valx['name'] == $typename ){
				$this->assign("mark",$valx['keyi']);
				break;
			}
		}

		$WORKNUM = 10;
		//查询当前用户是否已配置显示模块
		$waitemodename = $typename;//这是获取到的modulename
		$time=time();
		$newlist = array();
		//查询满足条件条数
		$model=D($waitemodename);
		if($val['map']){
			//替换map中当前时间$time
			$val['map']=str_replace('$time', $time,$val['map']);
		}
		//$count = $model->where($val['map'])->count('*');
		$list = $model->where($val['map'])->limit($WORKNUM)->select();
		//组合双击事件参数
		foreach($list as $vallet){
			$vallet['url'] = "__APP__/".$typename."/index/isend/3/md/".$typename."/id/{node_sid}";
			$vallet['target'] = 'navTab';
			$vallet['rel'] = $typename;
			$vallet['tableid'] = $typename;
			$vallet['title'] = '统计查询';
			// vallet['title'] = getFieldBy($val['modulename'], 'name', 'title', 'node')."_统计";
			array_push($newlist,$vallet);
		}
		//获取配置信息
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($typename);
		array_pop($detailList);
		array_pop($detailList);
		$this->assign('detailList',$detailList);

		$this->assign("MisWorkMonitoringList",$newlist);
		$this->assign('alist',json_encode($interfList));
	}
	/**
	 *
	 * @Title: lookupDisReport
	 * @Description: todo(窗体加载报表显示)
	 * @author renling
	 * @date 2014-3-28 下午3:45:09
	 * @throws
	 */
	public function lookupDisReport(){
		$MisSystemRemindReport=A('MisSystemRemindReport');
		//获取数据库中选中的报表
		$userModel=D('User');
		$userMap = array();
		$userMap['id']=$_SESSION[C('USER_AUTH_KEY')];
		$userMap['status']=1;
		$userRemindVo=$userModel->where($userMap)->field("reportmethod")->find();
		//查询当前用户是否有图形报表权限
		$remindList = require DConfig_PATH."/System/remindreportconfig.inc.php";
		$remindUserAllList=array();
		//当前时间
		$time=time();
		foreach ($remindList as $key=>$val){
			if($_SESSION[$val['authmodule']]||$_SESSION['a']) {
				$remindUserAllList[$key]=array(
						'title'=>$val['title'],

						'methodname'=>$val['methodname'],
				);
			}
		}
		$this->assign("remindUserReportList",$remindUserAllList);
		if($userRemindVo['reportmethod']){
			$MisSystemRemindReport->$userRemindVo['reportmethod']();
		}else{
			$MisSystemRemindReport->hrDepartmentEmployeesChart();
		}
	}
	private function lookupremindList($map){
		$uid=$_SESSION[C('USER_AUTH_KEY')];
		//提醒表
		$MisSystemRemindModel=D('MisSystemRemind');
		if($map){
			$userMap=$map;
		}
		$alluserid=array();
		//查询排除id
		$MisSystemRemindallList=$MisSystemRemindModel->where(" status=1 and userid ='all'")->select();
		foreach ($MisSystemRemindallList as $akey=>$aval){
			if($aval['deluserid']){
				$deluserid=explode(",",$aval['deluserid']);
				if(in_array($uid,array_values($deluserid))){
					$alluserid[]=$aval['id'];
				}
			}
		}
		if($alluserid){
			$userMap['id']=array("not in",array_values($alluserid));
		}
		$userMap['_string'] = "  userid =".$uid." or  userid ='all' and status=1";
		$userMap['status']=1;
		$remindList=$MisSystemRemindModel->where($userMap)->select();
		$remindAllList=array();
		//当前时间
		$time=time();
		$nowtime= strtotime(transTime($time,'Y-m-d')." 00:00");
		foreach ($remindList as $key=>$rval){
			$count=0;
			$sumcount=0;
			$val=unserialize($rval['reminddetail']);
			if($val['modulename']){
				$model=D($val['modulename']);
			}
			$remindAllList[$rval['id']]=array(
					'id'=>$rval['id'],
					'userid'=>$rval['userid'],
					'color'=>$val['color'],
					'title'=>mb_substr($val['title'], 0, 2, 'utf-8'),
					'map'=>unserialize($rval['map']),
					'name'=>$val['modulename'],
					'span'=>$val['span'],
			);
			foreach ($val['list'] as $lkey=>$lval){
				if($lval['map']){
					if($lval['timemap']){
						foreach ($lval['timemap'] as $tkey=>$tval){
							$newtime="";
							$newtime=strtotime("+".$tval['key'].$tval['name']);
							$lval['map']=str_replace($tkey, $newtime,$lval['map']);
						}
					}
					//替换map中当前时间$uid
					$lval['map']=str_replace('$uid', $uid,$lval['map']);
					//替换map中当前时间$time
					$lval['map']=str_replace('$time', $time,$lval['map']);
					//替换map中当前时间$nowtime
					$lval['map']=str_replace('$nowtime', $nowtime,$lval['map']);
					if($val['modulename'] == 'MisWorkPlan'){
						$planModel=D("MisWorkPlan");
						$plan['_string']="( FIND_IN_SET('$uid', commentpeople) )";
						//查询满足条件条数
						$planlist=$planModel->field("id")->where($plan)->select();
						if($planlist){
							foreach ($planlist as $v){
								$comid.=",".$v['id'];
							}
							$comid=substr($comid, 1);
							$lval['map'] .=" and id not in (".$comid.")";
						}
					}
				}
				$count=$model->where($lval['map'])->count('*');
				$sumcount+=$count;
				$remindAllList[$rval['id']]['list'][$lkey]=array(
						'relhref'=>$lval['relmodule']."/remindMap/".base64_encode($lval['map']),
						'count'=>$count,
						'timemap'=>$lval['timemap'],
						'keyv'=>$lkey,
						'reltitle'=>$val['title'],
						'rtitle'=>$lval['rtitle'],
				);
			}
			$remindAllList[$rval['id']]['sumcount']=$sumcount;
		}
		array_sort($remindAllList,'keyv','desc');
		$remindAllList = array_merge(array_sort($remindAllList,'sumcount','desc'));
		return  $remindAllList;
	}
	/**
	 *
	 * @Title: lookupmyRemind
	 * @Description: todo(提醒中心)
	 * @author renling
	 * @date 2014-3-26 下午5:08:34
	 * @throws
	 */
	public function lookupmyRemind(){
		$count=6;
		if($_REQUEST['page']){
			if($_REQUEST['page']=='prevpage'){
				$max=$_REQUEST['maxlimit']-$count;
				$min=$_REQUEST['minlimit']-$count;
			}
			if($_REQUEST['page']=='nextpage'){
				$max=$_REQUEST['maxlimit']+$count;
				$min=$_REQUEST['minlimit']+$count;
			}
		}else{
			// 默认取值6个
			$min=0;
			$max=$count;
		}
		$remindAllList=$this->lookupremindList();
		$remindNewAllList=array();
		foreach ($remindAllList as $key=>$val){
			if($key>=$min && $key<$max){
				$remindNewAllList[]=$val;
			}
		}
		$this->assign("nextlist",$remindAllList[$max]);
		$this->assign("prevlist",$remindAllList[$min-1]);
		$this->assign("maxcount",$max);
		$this->assign("mincount",$min);
		$this->assign("remindAllList",$remindNewAllList);
		if($_REQUEST['page']||$_REQUEST['stepremind']){
			$this->display();
		}
	}
	/**
	 * @Title: misreportinfo
	 * @Description: todo(报表快捷入口)
	 * @author yuansl
	 * @date 2014-3-24 下午5:17:15
	 * @throws
	 */
	public function reportCenters($name="", $sort){
		$nodeModel = D("Node");
		//获取报表顶级
		$NodeMain = $nodeModel->where("status = 1 and   group_id =38 and level=3")->field("id,name,title,icon")->limit(10)->select();
		//权限判断
		foreach($NodeMain as $key=>$val){
			if($_SESSION[strtolower($val['name'])."_index"]||$_SESSION['a']){
				$cop_right[]=array(
						'name'=>$val['name'],
						'icon'=>$val['icon'],
						'title'=>$val['title'],
				);
			}
		}
		$this->assign("sort",$sort);
		$this->assign('rightList',$cop_right);
		return $this->fetch("MisSystemPanelMethod:reportCenters");
	}

	//首页---待办事务
	public function missystemnotice(){
		$WORKNUM =  10;//配置显示条数 审核进度跟踪模块
		$MisWorkMonitoringViewModel = D("MisWorkMonitoringView");
		$map['dostatus'] = 0;//代表待办任务strtolower
		//$map['_string'] = 'FIND_IN_SET(  '.$_SESSION[C('USER_AUTH_KEY')].', curNodeUser )';
		if(!$_SESSION["a"]==1){
			$map['_string'] = 'FIND_IN_SET(  '.$_SESSION[C('USER_AUTH_KEY')].', curNodeUser )';
		}
		$map['node.status']=1;
		$MisWorkMonitoringList=$MisWorkMonitoringViewModel->where($map)->select();
		$list=array();
		$pidList=array();
		foreach($MisWorkMonitoringList as $key=>$val){
			$listArray=array();
			if(getFieldBy($val['pid'], 'id', 'pid', 'node')!=1){
				$val['pid']=getFieldBy($val['pid'], 'id', 'pid', 'node');
			}
			if($pidList[$val['pid']]){
				$listArr=array(
						'name'=>$val['name'],
						'tableid'=>$val['tableid'],
						'title'=>$val['title'],
						'createid'=>$val['createid'],
						'orderno'=>$val['orderno'],
						'timeout'=>getCHStimeDifference($val['createtime'], $val['dotime']),
				);
				$list[$val['pid']]['list'][]=$listArr;
			}else{
				$pidList[$val['pid']]=1;
				$listArray[]=array(
						'name'=>$val['name'],
						'tableid'=>$val['tableid'],
						'title'=>$val['title'],
						'createid'=>$val['createid'],
						'orderno'=>$val['orderno'],
						'timeout'=>getCHStimeDifference($val['createtime'], $val['dotime']),
				);
				$list[$val['pid']]=array(
						'type'=>getFieldBy($val['pid'], 'id', 'title', 'node'),
						'list'=>$listArray,
				);
			}
		}
		$this->assign("WorkMonitonList",$list);
		// 		dump($list);
		return $this->fetch("MisSystemPanelMethod:waitingfirst");
	}
	/**
	 * @Title: myEvents
	 * @Description: todo(我的日程管理，在我的桌面日历上面标记我的日程)
	 * @author liminggang
	 * @date 2013-12-30 下午3:19:48
	 * @throws
	 */
	private function myEvents(){
		//获取当前登录用户
		$userid=$_SESSION[C('USER_AUTH_KEY')];
		//获取当前登录人的日程
		$model = D('MisUserEvents');
		//获取当前月份的日程
		// 		$y=date("Y",time());
		// 		$m=date("m",time());
		// 		$d=date("d",time());
		// 		$t0=date('t');           // 本月一共有几天
		// 		$starttime=mktime(0,0,0,$m,1,$y);        // 创建本月开始时间
		// 		$endtime=mktime(23,59,59,$m,$t0,$y);       // 创建本月结束时间
		$map['_string'] = 'FIND_IN_SET(  '.$userid.',personid ) or userid = '.$_SESSION[C('USER_AUTH_KEY')];
		$map['status'] = 1;
		$list=$model->where($map)->select();
		//第二部，分解日程，特别是一个日程中连续夸天数的。拆分开
		if($list){
			$num = count($list);
			foreach($list as $key=>$val){
				$a=strtotime(transTime($val['startdate']));
				$b=strtotime(transTime($val['enddate']));
				$x=($b-$a)/86400;
				if($x){
					for($i = 1;$i<=$x;$i++){
						$val['begintime'] =strtotime("+".$i." day", $a);
						$list[$num] = $val;
						$num++;
					}
				}
				$list[$key]['begintime'] = $a;
			}
		}
		$arr = array();
		//第三部、分解一天中多个日程。
		if($list){
			foreach($list as $k=>$v){
				$str = htmlspecialchars_decode($v['details'], ENT_QUOTES);//转码
				$str = trim(strip_tags(str_replace("&nbsp;", ' ', $str)));//过滤html
				$v['details'] = $str;
				if(!in_array($v['begintime'], array_keys($arr))){
					$arr[$v['begintime']][] = $v;
				}else{
					$arr[$v['begintime']][] = $v;
				}
			}
		}
		//第四部、判断日程属于那种类型，1,2,3
		$azz = array();
		foreach($arr as $k1=>$v1){
			$self = 1;
			foreach($v1 as $k2=>$v2){
				if($v2['userid'] == $userid){
					$self = 1;//自己发
					if(in_array($userid, explode(",", $v2['personid']))){
						$self = 3;
						break;
					}
				}else if(in_array($userid, explode(",", $v2['personid']) && $v2['userid'] != $userid)){
					$self = 2;//别人发的。
				}
			}
			$azz[$k1][$self] = $v1;
		}
		$this->assign('myEvents',json_encode($azz));
	}
	private function getCHStimeDifference($starttime,$endtime) {
		// 判断结束时间是否传入，没有传入设置为当前时间
		if (empty($endtime)) {
			$endtime = time();
		}
		$resulttime = $endtime-$starttime;//取结束时间减去开始时间的差值
		$day = intval($resulttime/86400);//天
		$hour = intval(($resulttime-$day*86400)/3600);//小时
		$minute = intval(($resulttime-$day*86400-$hour*3600)/60);//分钟
		$seconds = intval($resulttime-$day*86400-$hour*3600-$minute*60);//秒
		// 		return $day."天".$hour."小时".$minute."分钟".$seconds."秒";
		if($day == 0){
			if($minute < 10){
				return $hour."时"."0".$minute."分";
			}else{
				return $hour."时".$minute."分";
			}
		}else{
			if($minute < 10){
				return $day."天".$hour."时"."0".$minute."分";
			}else{
				return $day."天".$hour."时".$minute."分";
			}
		}
	}
	/**
	 * @Title: mywork_monitoring
	 * @Description: todo(首页我的工作---审核进度跟踪模块)
	 * @author yuansl
	 * @date 2014-3-20 下午4:24:58
	 * @throws
	 */
	public  function mywork_monitoring(){
		$WORKNUM =  10;//配置显示条数 审核进度跟踪模块
		$MisWorkMonitoringModel = D("MisWorkMonitoring");
		$map['dostatus'] = 0;//代表待办任务strtolower
		$map['_string'] = 'FIND_IN_SET(  '.$_SESSION[C('USER_AUTH_KEY')].', curNodeUser )';
		$MisWorkMonitoringList = $MisWorkMonitoringModel->where($map)->order("createtime desc")->limit($WORKNUM)->select();
		$NewMisWorkMonitoringList = array();
		foreach($MisWorkMonitoringList as $vax){
			$vax['time_last'] = $this->getCHStimeDifference($vax['createtime']);
			$vax['tablename_lower'] = strtolower($vax['tablename']);//页面权限判断的时候需要用到
			$vax['url_a'] = "__APP__"."/".$vax['tablename']."/auditEdit/id/".$vax['id'];
			$vax['task_from_userid'] = D($vax['tablename'])->where('id = '.$vax['tableid'])->getfield('createid');
			$vax['task_from_username'] = getFieldBy($vax['task_from_userid'], 'id', 'name', 'user');
			array_push($NewMisWorkMonitoringList,$vax);
		}
		$taskList = $NewMisWorkMonitoringList;
		$this->assign('name','待办事务');
		$this->assign('taskList',$taskList);
		return $this->fetch("MisSystemPanelMethod:waitingfirst");
	}
	/**
	 * @Title: pan_hrFullMember
	 * @Description: todo(首页事务待办提醒----员工转正提醒)
	 * @return string
	 * @author yuansl
	 * @date 2014-3-19 下午4:17:22
	 * @throws
	 */
	private function pan_hrFullMember(){
		if (!$name) {
			$name = "员工待转正";
		}
		$this->assign('name', $name);
		$mMisHrBasicEmployeeModel=D('MisHrBasicEmployee');
		//获取一个月时间
		$time=time() + 2592000;
		$mapContract['status'] = 1;
		$mapContract['workstatus'] = 2; //员工状态为试用
		$mapContract['transferdate'] = array('ELT',$time);
		$aMisHrBasicEmployeeList=$mMisHrBasicEmployeeModel->where($mapContract)->order('transferdate asc')->getField("id,name,transferdate");
		// 		$this->assign('MisHrBasicEmployeeList',$aMisHrBasicEmployeeList);//这是员工转正详细记录,暂时关闭,可能会用到
		// 		$this->assign('MisHrBasicEmployeeListCount',count($aMisHrBasicEmployeeList));
		// 		return array('name'=>$name,'url'=>'__APP__/MisHrPersonnelManagement/index/ntdata/1','count'=>count($aMisHrBasicEmployeeList));
		return array('name'=>$name,'url'=>'__APP__/RegularWaitingFor/index','rel'=>'regularwaitingfor','count'=>count($aMisHrBasicEmployeeList));
	}
	/**
	 * @Title: pan_hrContracts
	 * @Description: todo(首页定死--待办提醒---员工合同)
	 * @author yuansl
	 * @date 2014-3-19 下午4:04:16
	 * @throws
	 */
	private function pan_hrContracts(){
		if (!$name) {
			$name = "员工合同";
		}
		// 		$this->assign('pan_hrContracts', $name);
		// 		//创建合同model对象
		// 		$MisHrBasicEmployeeContractModel=D('MisHrBasicEmployeeforContractView');
		// 		//查询合同结束日期大于当前日期人员
		// 		$MisHrBasicEmployeeContractList=$MisHrBasicEmployeeContractModel->where("endtime < ".time()." and endtime<>0  and endtime <> '' and contractstatus=0")->getField('employeeid,name');
		$mMisHrBasicEmployeeContractModel=D('MisHrBasicEmployeeforContractView');
		//获取一个月时间
		$time=time() + 2592000;

		$mMisHrBasicEmployeeContractModel=D('MisHrBasicEmployeeforContractView');
		$map['working']=1;//默认查询在职员工
		//获取一个月时间
		$time=time() + 2592000;
		$map['contractstatus'] = array('eq',0);
		$map['endtime'] = array(array('neq',0),array('lt',$time,array('neq',''),'and'));
		$map['status'] = 1 ;
		$map['contractstatus'] = 0 ;
		$aMisHrBasicEmployeeContractList = $aMisHrBasicEmployeeContractList=$mMisHrBasicEmployeeContractModel->where($map)->select();

		// 		$aMisHrBasicEmployeeContractList=$mMisHrBasicEmployeeContractModel->where("endtime < ".$time." and endtime<>0  and endtime <> '' and contractstatus=0 and working = 1")->select();
		// 		$this->assign('MisHrBasicEmployeeContractList',$aMisHrBasicEmployeeContractList);//这是转正详细记录暂时关闭
		return array('name'=>$name,'url'=>'__APP__/RemindWaitingFor/index','rel'=>'remindwaitingfort','count'=>count($aMisHrBasicEmployeeContractList));
	}
	/**
	 * @Title: schedule
	 * @Description: todo(日程安排)
	 * @author 杨东
	 * @date 2013-3-20 下午4:28:17
	 * @throws
	 */
	public function schedule($name="", $sort, $color){
		$this->assign('sort', $sort);
		if (!$name) {
			$name = "日程安排";
		}
		$this->assign('name', $name);
		$this->assign('color', $color);
		$model = D('MisUserEvents');
		$starttime = strtotime(date('Y-m-d', time()));
		$endtime = strtotime(date('Y-m-d', time())) + 24 * 60 * 60;
		$type = $_GET['type'];
		$this->assign('type',$type);
		$list = array();
		switch ($type) {
			//我自己安排的日程
			case 1:
				$list = $model->where('userid = '.$_SESSION[C('USER_AUTH_KEY')].' and status = 1 and enddate>'.time().' and startdate < '.$endtime)->order('enddate')->limit('0,7')->select();
				break;
				//与我相关的日程：协同日程
			case 2:
				$list = $model->where('FIND_IN_SET(  '.$_SESSION[C('USER_AUTH_KEY')].', personid ) and status = 1 and enddate>'.time().' and startdate < '.$endtime)->order('enddate')->limit('0,7')->select();
				break;
				//默认应该是我自己安排的日程
			default:
				$list = $model->where('userid = '.$_SESSION[C('USER_AUTH_KEY')].' and status = 1 and enddate>'.time().' and startdate < '.$endtime)->order('enddate')->limit('0,7')->select();
				break;
		}
		$this->assign('selist',$list);
		if ($type) {
			$this->display('scheduleindex');
			exit;
		}
		return $this->fetch("MisSystemPanelMethod:schedule");
	}
	/**
	 * @Title: officialDocument
	 * @Description: todo(公文管理首页系统模块显示)
	 * @param unknown_type $name
	 * @param unknown_type $sort
	 * @return string
	 * @author liminggang
	 * @date 2014-2-24 下午6:11:38
	 * @throws
	 */
	public function officialDocument($name="", $sort){
		$this->assign('sort', $sort);
		if (!$name) {
			$name = "公文信息";
		}
		$this->assign('name', $name);
		$type = $_GET['type'];
		$this->assign('type',$type);
		switch ($type) {
			case 1:
				$model = M('mis_officialdocument_out');
				break;
			case 2:
				$model = M('mis_officialdocument_in');
				break;
			default:
				$model = M('mis_officialdocument_out');
				break;
		}
		$map['auditState'] = 3;
		$map['status'] = 1;
		$list=$model->where($map)->select();
		//echo $model->getlastSql();
		$this->assign('selist',$list);
		if ($type) {
			$this->display('officialDocumentindex');
			exit;
		}
		return $this->fetch("MisSystemPanelMethod:officialDocument");
	}


	/**
	 * @Title: notice
	 * @Description: todo(公司新闻)
	 * @author 杨东
	 * @date 2013-3-28 下午3:22:56
	 * @throws
	 */
	private function notice($name = "", $sort, $color){
		$this->assign('sort', $sort);
		if (!$name) {
			$name = "公司新闻";
		}
		//查询公司网址
		$MisSystemCompanyModel=D('MisSystemCompany');
		$MisSystemCompanyVo=$MisSystemCompanyModel->where("status=1")->find();
		if($MisSystemCompanyVo['website']){
			$website=$MisSystemCompanyVo['website'];
		}else{
			$website="http://epf.966580.com";
		}
		$this->assign("website",$website);
		$this->assign('name', $name);
		$this->assign('color', $color);
		$list = D('MisNotice')->getNoticeList();
		$this->assign('noticelist',$list);
		return $this->fetch("MisSystemPanelMethod:notice");
	}
	/**
	 * @Title: workstatement
	 * @Description: todo(待阅报告)
	 * @author liminggang
	 * @date 2013-7-4 上午9:17:21
	 * @throws
	 */
	private function workStatement($name="", $sort, $color){
		$this->assign('sort', $sort);
		if (!$name) {
			$name = "待阅报告";
		}
		$this->assign('name', $name);
		$this->assign('color', $color);
		$MisWorkStatementModel=D('MisWorkReadStatementView');
		//获得当前登录人
		if ($_SESSION["a"] != 1){
			//只有阅读人才可见
			$map['mis_work_read_statement.userid']=$_SESSION[C('USER_AUTH_KEY')];
			$map['mis_work_read_statement.status']=array("gt",-1);
		}
		$map['mis_work_read_statement.readstatus'] = 1; //查阅状态为，未查阅
		$worklist=$MisWorkStatementModel->where($map)->order("top,createtime,desc")->limit('1,10')->select();
		$this->assign('worklist',$worklist);
		return $this->fetch("MisSystemPanelMethod:workStatement");
	}
	/**
	 * @Title: expertQuestions
	 * @Description: todo(专家库)
	 * @author yuansl
	 * @date 2014-4-28 上午9:55:48
	 * @throws
	 */
	public function expertQuestions ($name="", $sort, $color){
		$this->assign('sort', $sort);
		//问题
		$MisExpertQuestionListModel = D("MisExpertQuestionList");
		$map['status'] = 1;
		$map['closedbyid'] = 0;
		$map['type'] = 'Q';
		//2 表示像我咨询的问题
		if($_REQUEST['type'] == 2){
			$con_expertid = getFieldBy($_SESSION[C('USER_AUTH_KEY')], 'userid', 'id', 'mis_expert_list');
			$map['expertid'] = array('eq',$con_expertid);
		}
		$this->assign('type',$type);
		//取五条数据
		$lastFiveQues = $MisExpertQuestionListModel->where($map)->order('id desc')->limit(5)->select();
		//echo $MisExpertQuestionListModel->getLastSql();

		//检索这些信息是否与当前登录人有关
		//1.是否回答过   2.是否评论过   3.是否是该问题的创建人
		// 		$parlist = array();
		// 		$newlastFiveQues = array();
		// 		foreach ($lastFiveQues as $val){
		// 			//获取parentid 集合
		// 			array_push($parlist, $val['parentid']);
		// 			$val['isrelation'] = 0;
		// 			array_push($newlastFiveQues,$val);
		// 		}
		// 		$map['status'] = 1 ;
		// 		$map['parentid'] = array('in',$parlist);
		// 		//可能是该条问题的回答/评论人
		// 		$map['createid'] = array('eq',$_SESSION[C('USER_AUTH_KEY')]);
		// 		foreach($newlastFiveQues as $cal){
		// 			$isRelationShip = $MisExpertQuestionListModel->where($map)->find();
		// 			if($isRelationShip){
		// 				$cal['isrelation'] = 1;
		// 			}
		// 		}

		// 		$this->assign('name',"专家库");
		// 		$this->assign('lastFiveQues',$newlastFiveQues);
		$this->assign('name',"专家库");
		$this->assign('lastFiveQues',$lastFiveQues);
		if($_REQUEST['type']){
			$this->display('inlExpertQuestions');
			exit;
		}
		return $this->fetch("MisSystemPanelMethod:expertQuestions");
	}
	/**
	 * @Title: knowledgeSee
	 * @Description: todo(首页面板知识库)
	 * @author yuansl
	 * @date 2014-4-28 下午2:01:23
	 * @throws
	 */
	public function knowledgeSee($name="", $sort, $color){
		$this->assign('sort', $sort);
		//知识文章
		$MisKnowledgeListModel= D('MisKnowledgeList');
		$map['status'] = 1;
		$map['type'] = 'Q';
		$map['parentid'] = 0;
		$map['closedbyid'] = 0;
		$map['parentid'] = 0;
		$map['auditState'] = 3;
		$lastFiveArct = $MisKnowledgeListModel->where($map)->order("id desc")->limit(5)->select();
		$this->assign('lastFiveArct',$lastFiveArct);
		$this->assign('name','知识库');
		return $this->fetch("MisSystemPanelMethod:knowledgeSee");
	}
	/**
	 * @Title: SystemNotices
	 * @Description: todo(系统公告)
	 * @author 杨东
	 * @date 2013-3-15 下午5:45:07
	 * @throws
	 */
	private function systemNotices($name = "", $sort, $color){
		//获取当前登录人部门id 角色id
		$userModel=M("user");
		$userList=$userModel->where("id=".$_SESSION[C('USER_AUTH_KEY')])->find();
		//
		$this->assign('sort', $sort);
		if (!$name) {
			$name = "系统公告";
		}
		$this->assign('name', $name);
		$this->assign('color', $color);
		$snmodel = D('MisSystemAnnouncement');
		$map['status']=array("gt",-1);
		$map['commit'] = array("eq",1);
		$time=time();
		$map['endtime']=array(array('eq',0),array('gt',$time),'or');
		$map['starttime']=array('lt',$time);
		if( !isset($_SESSION['a']) ){//不是管理员 只能看到在范围内的公告
			$map['_string']="( (scopetype=2 and ( (find_in_set('".$userList['dept_id']."',deptid) or find_in_set('".$userList['id']."',personid)) or createid=".$_SESSION[C('USER_AUTH_KEY')]."  ) ) or (scopetype=3))";
		}
		$list = $snmodel->where($map)->order('top,sendtime desc')->limit('0,7')->select();
		//查出最新发布的3条数据
		$model=D("MisSystemAnnouncement");
		$mlist=$model->field("id")->where("status=1 and commit=1")->order("sendtime desc")->limit(3)->select();
		$newids=array();
		foreach ($mlist as $k=>$v){
			$newids[]=$v['id'];
		}
		//是最新发布的 追加状态为最新
		foreach ($list as $k1=>$v1){
			if(in_array($v1['id'], $newids)){
				$list[$k1]['new']=1;
			}
		}
		$this->assign('snlist',$list);
		//点击更多 初始默认类型
		$ptypelist=$snmodel->group("type")->where($map)->limit("0,1")->getfield('type');
		$this->assign('ptypelist',$ptypelist);
		return $this->fetch("MisSystemPanelMethod:systemNotices");
	}
	/**
	 * @Title: systemNoticeslookup
	 * @Description: todo(查看更多公告列表)
	 * @author libo
	 * @date 2013-12-24 上午11:50:33
	 * @throws
	 */
	public function systemNoticeslookup(){
		$this->leftmessage();//获取左侧数据
		//实例化
		$userModel=M("user");
		$map['type']=1;
		if($_REQUEST['type']){//查询当前位置
			$map['type']=$_REQUEST['type'];
			//查询主类型
			$typemodel=D("MisSystemAnnouncementSet");
			$typename=$typemodel->where("status=1 and id=".$_REQUEST['type'])->getField("name");
			$this->assign("typename",$typename);
		}
		//搜索条件
		if($_REQUEST['titleseach']){
			$re=$userModel->where("name = '".$_REQUEST['titleseach']."'")->getField('id');
			$seach="and ((title like '%".$_REQUEST['titleseach']."%') or (sendtime >= '".strtotime($_REQUEST['titleseach'])."' and sendtime < '".(strtotime($_REQUEST['titleseach'])+24*3600-1)."') or (createid = '".$re."'))";
			unset($map['type']);
			$this->assign('seach',1);
			$this->assign('titleseach',$_REQUEST['titleseach']);
		}
		$this->assign('type',$_REQUEST['type']);
		//获取当前登录人部门id 角色id
		$userList=$userModel->where("id=".$_SESSION[C('USER_AUTH_KEY')])->find();
		//得到已发布和未终止的公告
		$MisSystemAnnouncementModel=D('MisSystemAnnouncement');
		if( !isset($_SESSION['a']) ){//不是管理员 只能看到在范围内的公告
			$map['_string']="( (scopetype=2 and ( (find_in_set('".$userList['dept_id']."',deptid) or find_in_set('".$userList['id']."',personid)) or createid=".$_SESSION[C('USER_AUTH_KEY')]."  ) ) or (scopetype=3) ".$seach.")";
		}else{
			$map['_string']="( status=1 ".$seach.")";
		}
		$map['commit']=1;
		$map['status']=1;
		$time=time();
		$map['endtime']=array(array('eq',0),array('gt',$time),'or');
		$map['starttime']=array('lt',$time);
		$MisSystemAnnouncementList=$MisSystemAnnouncementModel->where($map)->select();
		$count=count($MisSystemAnnouncementList);// 查询满足要求的总记录数
		$PageMap = array();
		$PageMap['titleseach'] = $_REQUEST['titleseach'];//分页查询条件
		import("ORG.Util.Page");// 导入分页类
		$Page=new Page($count,C("ANNOUNCEMENT_LIST_NUM"));// 实例化分页类 传入总记录数和每页显示的记录数
		foreach($PageMap as $key=>$val) {
			$Page->parameter.="$key=".urlencode($val).'&';//添加分页条件
		}
		$Page->setConfig('header','条','theme','%totalRow% %header% %nowPage%/%totalPage% 页 %upPage% %first% %prePage% %linkPage% %nextPage% %end% %downPage%');//自定义分页
		$show=$Page->show();// 分页显示输出
		$list = $MisSystemAnnouncementModel->where($map)->order('top,sendtime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		//查出最新发布的三条数据
		$mlist=$MisSystemAnnouncementModel->field("id")->where("status=1 and commit=1")->order("sendtime desc")->limit(3)->select();
		$newids=array();
		foreach ($mlist as $k=>$v){
			$newids[]=$v['id'];
		}
		//在最新发布时间里加 入 new 标示
		foreach ($list as $k1=>$v1){
			if(in_array($v1['id'], $newids)){
				$list[$k1]['new']=1;
			}
		}
		$this->assign('AnnouncementList',$list);// 赋值数据集
		$this->assign('page',$show);
		$this->display();
	}
	/**
	 * @Title: leftmessage
	 * @Description: todo(获取左侧数据)
	 * @author libo
	 * @date 2013-12-31 下午4:07:28
	 * @throws
	 */
	public function leftmessage(){
		//获取当前登录人部门id 角色id
		$userModel=M("user");
		$userList=$userModel->where("id=".$_SESSION[C('USER_AUTH_KEY')])->find();
		//查询公司网站
		$model=D("MisSystemCompany");
		$url=$model->where("status=1")->getField('website');
		$this->assign('url',$url);
		//
		$MisSystemAnnouncementModel=D('MisSystemAnnouncement');
		//查询主类型
		$typemodel=D("MisSystemAnnouncementSet");
		if( !isset($_SESSION['a']) ){//不是管理员 只能看到在范围内的公告
			$map['_string']="( (scopetype=2 and ( (find_in_set('".$userList['dept_id']."',deptid) or find_in_set('".$userList['id']."',personid)) or createid=".$_SESSION[C('USER_AUTH_KEY')]."  ) ) or (scopetype=3))";
		}
		$map['commit']=1;
		$map['status']=1;
		$time=time();
		$map['endtime']=array(array('eq',0),array('gt',$time),'or');
		$map['starttime']=array('lt',$time);
		$typeList=$MisSystemAnnouncementModel->where($map)->group('type')->getField("id,type");
		$list=array();
		foreach ($typeList as $key=>$val){
			$map['type']=$val;
			$list[$val]=$MisSystemAnnouncementModel->where($map)->order("top,sendtime desc")->limit('0,'.C("ANNOUNCEMENT_TYPE_NUM"))->select();
		}
		$this->assign("amlist",$list);
		$this->assign('typelist',$typeList);
	}
	/**
	 * @Title: systemNoticesview
	 * @Description: todo(查看公告信息)
	 * @author libo
	 * @date 2013-12-24 下午2:44:49
	 * @throws
	 */
	public function systemNoticesview(){
		$this->leftmessage();//获取左侧数据
		//判断当前用户是否已查看 未查看则添加记录
		$mSAUmodel=M("mis_system_announcement_user");
		$readStatus=$mSAUmodel->where("userid=".$_SESSION[C('USER_AUTH_KEY')]." and announceid=".$_REQUEST['id'])->getField("status");
		$readData=array();
		$readData['userid']=$_SESSION[C('USER_AUTH_KEY')];
		$readData['announceid']=$_REQUEST['id'];
		$readData['status']=1;
		if($readStatus == NULL){//新增
			$mSAUmodel->data($readData)->add();
		}else if($readStatus == 0){//状态为未读时
			$mSAUmodel->where("userid=".$_SESSION[C('USER_AUTH_KEY')]." and announceid=".$_REQUEST['id'])->setField ( 'status', 1 );
		}
		//获取公告
		$userModel=M("user");
		$userList=$userModel->where("id=".$_SESSION[C('USER_AUTH_KEY')])->find();
		if($_REQUEST['type']){//查询当前位置
			//查询主类型
			$typemodel=D("MisSystemAnnouncementSet");
			$typename=$typemodel->where("status=1 and id=".$_REQUEST['type'])->getField("name");
			$this->assign("typename",$typename);
		}
		$this->assign('type',$_REQUEST['type']);
		$id=$_REQUEST['id'];
		//创建公告模型
		$MisSystemAnnouncementModle=M('mis_system_announcement');
		$MisSystemAnnouncementList=$MisSystemAnnouncementModle->where("id=".$id)->find();
		//每点一次 浏览次数+1
		$count=$MisSystemAnnouncementList['count'];
		$data['count']=$count+1;
		$MisSystemAnnouncementModle->where('id='.$id)->save($data);
		$this->assign('vo',$MisSystemAnnouncementList);
		// 上一条数据ID
		$map['type']=$MisSystemAnnouncementList['type'];
		$map['status']=1;
		$map['commit']=1;
		$time=time();
		$map['endtime']=array(array('eq',0),array('gt',$time),'or');
		$map['starttime']=array('lt',$time);
		if( !isset($_SESSION['a']) ){//不是管理员 只能看到在范围内的公告
			$map['_string']="( (scopetype=2 and ( (find_in_set('".$userList['dept_id']."',deptid) or find_in_set('".$userList['id']."',personid)) or createid=".$_SESSION[C('USER_AUTH_KEY')]."  ) ) or (scopetype=3))";
		}
		$idlist=$MisSystemAnnouncementModle->where($map)->order("top,sendtime desc")->field("id")->select();
		foreach ($idlist as $key=>$val){
			if(in_array($id, $val)){
				//上一条ID
				$updataid=$idlist[$key-1]['id'];
				//下一条ID
				$downdataid=$idlist[$key+1]['id'];
			}
		}
		$this->assign("updata",$updataid);
		$this->assign("downdata",$downdataid);
		//获取当前控制器名称
		$actionname='MisSystemAnnouncement';

		//获取附件信息
		$MisAttachedRecordModel = D('MisAttachedRecord');
		$condition = array();
		$condition['tableid'] = $id;
		$condition['tablename'] = $actionname;
		$attarry=$MisAttachedRecordModel->where($condition)->select();
		$filesArr = array('pdf','doc','docx','xls','xlsx','ppt','pptx','txt','jpg','jpeg','gif','png');
		foreach ($attarry as $key => $val) {
			$pathinfo = pathinfo($val['attached']);
			if (in_array(strtolower($pathinfo['extension']), $filesArr)) {
				$attarry[$key]['isplay'] = true;
				$attarry[$key]['name'] = base64_encode($val['attached']);
				$attarry[$key]['filename'] = $val['upname'];
			}
		}
		if($attarry){
			$this->assign('attarry',$attarry);
		}
		$this->display();
	}
	/**
	 * @Title: misUserNote
	 * @Description: todo(工作便签)
	 * @author 杨东
	 * @date 2013-3-15 下午5:45:07
	 * @throws
	 */
	public function misUserNote($name = "", $sort, $color){
		$this->assign('sort', $sort);
		if (!$name) {
			$name = "工作便签";
		}
		$this->assign('name', $name);
		$this->assign('color', $color);
		$noteModel = D('MisUserNote');
		$noteMap['uid'] = $_SESSION[C('USER_AUTH_KEY')];
		$noteMap['status'] = 1;
		$noteVo = $noteModel->where($noteMap)->find();
		$type = $_REQUEST['type'];
		if ($type == 'note') {
			$noteModel->startTrans();
			$noteData = array();
			if($noteVo){
				$noteData['id'] = $noteVo['id'];
				$noteData['updateid'] = $_SESSION[C('USER_AUTH_KEY')];
				$noteData['updatetime'] = time();
				$noteData['note'] = $_POST['note'];
				$noteModel->data($noteData);
				$noteModel->save($noteData);
			} else {
				$noteData['uid'] = $_SESSION[C('USER_AUTH_KEY')];
				$noteData['note'] = $_POST['note'];
				$noteData['createid'] = $_SESSION[C('USER_AUTH_KEY')];
				$noteData['updateid'] = $_SESSION[C('USER_AUTH_KEY')];
				$noteData['createtime'] = time();
				$noteData['updatetime'] = time();
				$noteModel->data($noteData);
				$noteModel->add($noteData);
			}
			$noteModel->commit();
			exit;
		}
		$this->assign('note',$noteVo['note']);
		return $this->fetch("MisSystemPanelMethod:misUserNote");
	}
	/**
	 * @Title: myToDoList
	 * @Description: todo(我的待办任务)
	 * @author 杨东
	 * @date 2013-3-15 下午5:02:51
	 * @throws
	 */
	private function myToDoList($name = "", $sort, $color){
		$this->assign('sort', $sort);
		if (!$name) {
			$name = "我的待办任务";
		}
		$this->assign('name', $name);
		$this->assign('color', $color);
		$arr = array();
		$moduleNameList=array();
		$html = '';
		$arr=getTaskulous();
		foreach( $arr as $k=>$v ){
			if(!in_array($v['tablename'], array_keys($moduleNameList))){
				// 				if ($_SESSION["a"] == 1 || $_SESSION[strtolower($v['tablename'])."_waitaudit"]) {
				$moduleNameList[$v['tablename']]=1;
				$model = D($v['tablename']);
				$map = array();
				$action = A($v['tablename']);
				if (method_exists ( $action, '_filter' )) {
					$action->_filter ( $map );
				}
				$map['status'] = 1;
				$map['_string'] = 'FIND_IN_SET(  '.$_SESSION[C('USER_AUTH_KEY')].', curAuditUser )';
				$count = $model->where($map)->count('id');
				if ($count) {
					$m = strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $v['tablename']), "_"));
					if($_SESSION[strtolower($m.'_index')] || isset($_SESSION['a'])){
						$ntdata =1;
					}else{
						$ntdata=0;
					}
					$html .= '<li><a class="clearfix" rel="'.$v['tablename'].'" target="navTab" title="'.getFieldBy($v['tablename'], 'name', 'title', 'node').'" href="'.__APP__.'/'.$v['tablename'].'/index/default/2"><span>'.getFieldBy($v['tablename'], 'name', 'title', 'node').'</span> <em>共 '.$count.' 条</em></a></li>';
				}
				// 				}
			}
		}
		$this->assign('myToDo',$html);
		return $this->fetch("MisSystemPanelMethod:myToDoList");
	}

	/**
	 * @Title: getWillWorks
	 * @Description: todo(OA助手获取我的待办任务)
	 * @author 杨东
	 * @date 2013-3-15 下午5:02:51
	 * @throws
	 */
	private function getWillWorks(){
		$arr = array();
		$html = '';
		// 		$file =  DConfig_PATH . "/System/ProcessModelsConfig.inc.php";
		// 		$arr = require $file;
		$moduleNameList=array();
		$arr=getTaskulous();
		$rutrunDataArr=array();
		$num=0;//ruturnDataArr数组的顺序
		foreach( $arr as $k=>$v ){
			if(!in_array($v['tablename'], array_keys($moduleNameList))){
				// 				if ($_SESSION["a"] == 1 || $_SESSION[strtolower($v['tablename'])."_waitaudit"]) {
				$moduleNameList[$v['tablename']]=1;
				$model = D($v['tablename']);
				$map = array();
				$action = A($v['tablename']);
				if (method_exists ( $action, '_filter' )) {
					$action->_filter ( $map );
				}
				$map['status'] = 1;
				$map['_string'] = 'FIND_IN_SET(  '.$_SESSION[C('USER_AUTH_KEY')].', curAuditUser )';
				$count = $model->where($map)->count('id');
				if ($count) {
					$m = strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $v['tablename']), "_"));
					if($_SESSION[strtolower($m.'_index')] || isset($_SESSION['a'])){
						$ntdata =1;
					}else{
						$ntdata=0;
					}
					$rutrunDataArr[$num]['name']=getFieldBy($v['tablename'], 'name', 'title', 'node');
					$rutrunDataArr[$num]['title']=getFieldBy($v['tablename'], 'name', 'title', 'node');
					$rutrunDataArr[$num]['count']=$count;
					$rutrunDataArr[$num]['urldata']=$v['tablename'].",index,default,2".";".$v['tablename'].";".$v['tablename'];
					//$html .= '<li><a class="clearfix" rel="'.$v['model'].'" target="navTab" title="'.$v['name'].'" href="'.__APP__.'/'.$v['model'].'/index/default/2"><span>'.$v['name'].'</span> <em>共 '.$count.' 条</em></a></li>';
					$num++;
				}
				// 				}
			}
		}
		return $rutrunDataArr;
	}
	/**
	 * @Title: lookupmyWorkflow
	 * @Description: todo(工作流)
	 * @author 杨东
	 * @date 2013-3-16 下午1:49:36
	 * @throws
	 */
	public function lookupmyWorkflow($name = "", $sort, $color){
		$this->assign('sort', $sort);
		if (!$name) {
			$name = "工作流";
		}
		$this->assign('name', $name);
		$this->assign('color', $color);
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
		// 		$time = time()-2*24*3600;//5天
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
						// 						$mapv['dotime'] = array('elt',$time);
						break;
					case 3:
						$map['auditState'] = 2;
						$hmap['auditstatus'] = 2;
						// 						$mapv['dotime'] = array('elt',$time);
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
			exit;
		}
		if($type){
			$this->display('myWorkflowindex');
			exit;
		}
		return $this->fetch("MisSystemPanelMethod:lookupmyWorkflow");
	}
	/**
	 * @Title: mistaskinfo
	 * @Description: todo(任务)
	 * @author liminggang
	 * @date 2013-7-4 上午9:17:21
	 * @throws
	 */
	public function mistaskinfo($name = "", $sort, $color){
		$this->assign('sort', $sort);
		if (!$name) {
			$name = "任务信息";
		}
		$this->assign('name', $name);
		$this->assign('color', $color);
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
			$this->display("mistaskinfoindex");
			exit;
		}
		return $this->fetch("MisSystemPanelMethod:mistaskinfo");
	}
	/**
	 * @Title: meetingInfo
	 * @Description: todo(会议信息)
	 * @author 杨东
	 * @date 2013-10-22 下午5:19:51
	 * @throws
	 */
	public function meetingInfo($name = "", $sort, $color){
		$this->assign('sort', $sort);
		if (!$name) {
			$name = "会议信息";
		}
		$this->assign('name', $name);
		$this->assign('color', $color);
		$this->searchMeetingInfo(1);
		return $this->fetch("MisSystemPanelMethod:meetingInfo");
	}
	public function searchMeetingInfo($type){
		$map['mis_oa_meeting_person.userid'] = $_SESSION[C('USER_AUTH_KEY')];//当前用户
		//$map['mis_oa_meeting_person.isattend'] = 0;//未决定
		$time = strtotime(date('Y-m-d', time()));
		$map['mis_oa_meeting_manage.starttime'] = array("gt",$time);// 今天以前的
		$map['mis_oa_meeting_manage.ostatus'] = 0;//未开始
		$mModel = D("MisOaMeetingPersonView");
		$alist = $mModel->where($map)->order('starttime asc')->select();
		foreach ($alist as $key => $value) {
			$createtimestart = strtotime(date('Y-m-d',time()));//当前时间戳的开始时间搓
			$createtimeend = $createtimestart+(60*60*24)-1;//当前时间戳的结束时间搓
			if ($value['createtime'] >= $createtimestart && $value['createtime'] <= $createtimeend) {
				$alist[$key]['newdata'] = 1;
			}
		}
		$this->assign("meetingList",$alist);
		if(!$type){
			$this->display();
		}
	}
	/**
	 * @Title: hrContracts
	 * @Description: todo(员工合同到期提醒 提前一个月)
	 * @author renling
	 * @date 2013-10-23 下午4:50:28
	 * @throws
	 */
	private function hrContracts($name="", $sort, $color){
		$this->assign('sort', $sort);
		if (!$name) {
			$name = "员工合同";
		}
		$this->assign('name', $name);
		$this->assign('color', $color);
		$mMisHrBasicEmployeeContractModel=D('MisHrBasicEmployeeforContractView');
		//获取一个月时间
		$time=time() + 2592000;
		$aMisHrBasicEmployeeContractList=$mMisHrBasicEmployeeContractModel->where("endtime < ".$time." and endtime<>0  and endtime <> '' and contractstatus=0")->select();
		$this->assign('MisHrBasicEmployeeContractList',$aMisHrBasicEmployeeContractList);
		$this->assign($aMisHrBasicEmployeeContractListCount,count($aMisHrBasicEmployeeContractList));
		return $this->fetch("MisSystemPanelMethod:hrContracts");
	}
	/**
	 * @Title: userConstantly
	 * @Description: todo(企业工作台面板--常用功能)
	 * @author yuansl
	 * @date 2014-3-18 上午11:22:27
	 * @throws
	 */
	private function userConstantly(){
		$this->assign('sort', $sort);
		if (!$name) {
			$name = "常用功能";
		}
		$this->assign('name', $name);
		// 常用功能
		$uommodel = D("UserOftenMenu");
		$umap['uid'] = $_SESSION[C('USER_AUTH_KEY')];
		$umap['status'] = 1;
		$oftenList = $uommodel->where($umap)->order("createtime asc")->select();
		$file =  DConfig_PATH."/AccessList/access_".$_SESSION[C('USER_AUTH_KEY')].".php";
		$access = $_SESSION[C('ADMIN_AUTH_KEY')]? array():require $file;
		foreach ($oftenList as $k => $v) {
			$mdoelarr = explode('/', $v ['url']);
			if(!$mdoelarr[1]) $mdoelarr[1] = 'index';
			if (!$access[strtoupper( APP_NAME )][strtoupper($mdoelarr[0])][strtoupper($mdoelarr[1])] && !$_SESSION [C('ADMIN_AUTH_KEY')]) {
				unset($oftenList[$k]);
				continue;
			}
		}
		$this->assign('oftenList',$oftenList);
		return $this->fetch("MisSystemPanelMethod:userConstantly");
	}
	/**
	 * @Title: hrFullMember
	 * @Description: todo(员工转正提醒 提前一个月)
	 * @param unknown_type $name
	 * @author renling
	 * @date 2013-10-24 上午11:20:14
	 * @throws
	 */
	private function hrFullMember($name="", $sort, $color){
		$this->assign('sort', $sort);
		if (!$name) {
			$name = "员工待转正";
		}
		$this->assign('name', $name);
		$this->assign('color', $color);
		$mMisHrBasicEmployeeModel=D('MisHrBasicEmployee');
		//获取一个月时间
		$time=time() + 2592000;
		$mapContract['status']=1;
		$mapContract['workstatus']=2; //员工状态为试用
		$mapContract['transferdate']=array('ELT',$time);
		$aMisHrBasicEmployeeList=$mMisHrBasicEmployeeModel->where($mapContract)->order('transferdate asc')->getField("id,name,transferdate");
		$this->assign('MisHrBasicEmployeeList',$aMisHrBasicEmployeeList);
		return $this->fetch("MisSystemPanelMethod:hrFullMember");
	}
	/**
	 * @Title: carInsurance
	 * @Description: todo(车辆保险到期提醒)
	 * @param unknown_type $name
	 * @author renling
	 * @date 2013-10-24 下午4:35:17
	 * @throws
	 */
	private function carInsurance($name="", $sort, $color){
		$this->assign('sort', $sort);
		if (!$name) {
			$name = "车辆保险";
		}
		$this->assign('name', $name);
		$this->assign('color', $color);
		$mMisCarInsuranceModel=D('MisCarInsurance');
		//获取一个月时间
		$time=time() + 2592000;
		$aMisCarInsuranceList=$mMisCarInsuranceModel->query(" SELECT id,carid,expire_time FROM (SELECT * FROM `mis_car_insurance` ORDER BY expire_time DESC) AS mis_car_insurance WHERE  (`status` = 1)      AND expire_time <=1385174338 GROUP BY `carid` ORDER BY `expire_time` ASC ");
		$this->assign('MisCarInsuranceList',$aMisCarInsuranceList);
		return $this->fetch("MisSystemPanelMethod:carInsurance");
	}
	/**
	 * @Title: savepanelsort
	 * @Description: todo(保存拖动模块)
	 * @author jiangx
	 * @date 2013-11-22 下午4:35:17
	 * @throws
	 */
	public function savepanelsort(){
		if (!$_POST['panelindex']) {
			exit;
		}
		$sortindex = explode(',', $_POST['panelindex']);
		$mPanelUsersort = D("MisSystemPanelUsersort");
		unset($_POST);
		$_POST = array();
		foreach ($sortindex as $key => $val) {
			if (!$val) {
				continue;
			}
			$valarr = explode('-', $val);
			if ($_POST['panelsort']) {
				$_POST['panelsort'] .= ','.$valarr[0] . '-'. $key;
			} else {
				$_POST['panelsort'] .= $valarr[0] . '-'. $key;
			}
		}
		if ($_POST) {
			$aMap = array();
			$aMap['status'] = 1;
			$aMap['userid'] = $_SESSION[C('USER_AUTH_KEY')];
			$list = $mPanelUsersort->where($aMap)->find();
			C('TOKEN_ON',false);
			$_POST['userid'] = $aMap['userid'];
			if (false === $mPanelUsersort->create ()) {
				exit;
			}
			if ($list) {
				$mPanelUsersort->where($aMap)->save();
			} else {
				$mPanelUsersort->where($aMap)->add();
			}
			C('TOKEN_ON',true);
		}
		exit;
	}
	/**
	 *
	 * @Title: lookupchangeremind
	 * @Description: todo(修改提醒中心)
	 * @author renling
	 * @date 2014-8-8 下午2:18:25
	 * @throws
	 */
	public function lookupchangeremind(){
		//节点模型
		$nodemodel=D("node");
		$remindAllList=$this->lookupremindList();
		$this->assign("remindAllList",$remindAllList);
		$this->display();
	}
	/**
	 *
	 * @Title: lookupchangeedit
	 * @Description: todo(修改提醒条件)
	 * @author renling
	 * @date 2014-8-11 下午2:16:31
	 * @throws
	 */
	public  function lookupchangeedit(){
		$map['id']=$_REQUEST['id'];
		$remindAllList=$this->lookupremindList($map);
		$this->assign("listkey",$_REQUEST['list']);
		$this->assign("remindAllList",$remindAllList);
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($remindAllList[0]['name']);
		$this->assign("detailList",$detailList);
		//查询连字符
		$html=getSelectByHtml('roleinexp','select');
		$html= str_replace('"', "'", $html);
		$this->assign("html",$html);
		$this->assign("md",$_REQUEST['md']);
		$this->display();
	}
	/**
	 *
	 * @Title: lookupchangereport
	 * @Description: todo(更换报表中心显示数据)
	 * @author renling
	 * @date 2014-3-28 下午3:26:00
	 * @throws
	 */
	public function lookupchangereport(){
		//查询当前用户是否已配置显示模块
		$userModel=D('User');
		$userMap = array();
		$userMap['id']=$_SESSION[C('USER_AUTH_KEY')];
		$userMap['status']=1;
		$userRemindVo=$userModel->where($userMap)->field("reportmethod")->find();
		$this->assign("reportmethod",$userRemindVo['reportmethod']);
		$remindList=require DConfig_PATH."/System/remindreportconfig.inc.php";
		$remindUserAllList=array();
		//当前时间
		$time=time();
		$nodemodel=D("node");
		// 		dump($remindList);
		foreach ($remindList as $key=>$val){
			if($_SESSION[$val['authmodule']]||$_SESSION['a']) {
				$groupid=$nodemodel->where("name='".$val['methodname']."'")->getfield("group_id");
				$gounpname= getFieldBy($groupid,'id','name','group');
				$remindUserAllList[$val['type']][$key]=array(
						'title'=>$val['title'],
						'methodname'=>$val['methodname'],
				);
			}
		}
		// 		dump($remindUserAllList);
		$this->assign("remindUserAllList",$remindUserAllList);
		$this->display();
	}
	/**
	 * @Title: workplatform
	 * @Description: todo(办公平台)
	 * @param unknown_type $name
	 * @param unknown_type $sort
	 * @author libo
	 * @date 2014-3-28 下午6:17:11
	 * @throws
	 */
	public function workplatform($name = "", $sort , $type=0){
		//获取当前登录人部门id 角色id
		$userModel=M("user");
		$userList=$userModel->where("id=".$_SESSION[C('USER_AUTH_KEY')])->find();
		//
		$this->assign('sort', $sort);
		if (!$name) {
			$name = "系统公告";
		}
		$this->assign('name', $name);
		$type = $_GET['type'];
		$this->assign('type',$type);

		if($type==2){
			$this->onlineManager(5,0,true);
		}else{
			$this->onlineManager();
		}
		if($type){
			$this->display('workplatformindex');
			exit;
		}
		return $this->fetch("MisSystemPanelMethod:workplatform");
	}
	/**
	 * @Title: forumInformation
	 * @Description: todo(论坛新帖)
	 * @author jiangx
	 * @date 2013-11-22 下午4:35:17
	 * @throws
	 */
	public function forumInformation ($name="", $sort, $color){
		$this->assign('sort', $sort);
		if (!$name) {
			$name = "论坛资讯";
		}
		$this->assign('name', $name);
		$this->assign('color', $color);
		import('@.ORG.UcenterBbs', '', $ext='.php');
		$UcenterBbs = new UcenterBbs();
		$newest = $UcenterBbs->getNewestBbs();
		$this->assign('newest', $newest);
		return $this->fetch("MisSystemPanelMethod:forumInformation");
	}
	function playSWF(){
		$file_type = "file";
		$socuse = str_replace("\\", "/", base64_decode($_REQUEST['name']));
		$file_path = $resource_path = str_replace("\\","/", UPLOAD_PATH.$socuse);
		if (!file_exists($file_path)) {
			$this->error('文件不存在！');
		}
		$info = pathinfo($file_path);
		$filesArr = C ('TRANSFORM_SWF');
		$filesArr[] = 'pdf';
		$photoArr = C ('IMG_file');
		$file_extension_lower = strtolower($info['extension']);

		if (in_array($file_extension_lower, $filesArr)) {
			$file_path = preg_replace("/^([\s\S]+)\/Public/", "../Public", $info["dirname"]);
			$file_path .= '/swf/' . $info['filename'] . '.swf';
			$file_path = @iconv('UTF-8', 'GBK', $file_path);
			if (!file_exists($file_path)) {
				import ( '@.ORG.OfficeOnline.OfficeOnlineView' );
				$OfficeOnlineView= new OfficeOnlineView();
				if ('pdf' == $file_extension_lower) {
					$OfficeOnlineView->fileCreate($info['dirname'], $resource_path, 'swf');
				} else {
					$OfficeOnlineView->fileCreate($info['dirname'], $resource_path, 'swf/pdf');
				}
			}
			$this->assign('file_name', $_REQUEST['filename']);
		}
		if (in_array($file_extension_lower, $photoArr)) {
			$file_type = "photo";
		}
		$file_path = preg_replace("/^([\s\S]+)\/Public/", "__PUBLIC__", $file_path);
		$this->assign("file_type", $file_type);
		$this->assign('file_path', $file_path);
		$this->display("Public:playswf");
	}
	/**
	 * @Title: schedule
	 * @Description: todo(日程安排)
	 * @author 杨东
	 * @date 2013-3-20 下午4:28:17
	 * @throws
	 */
	public function setMethod($method){
		if (method_exists($this, $method)) {
			return $this->$method();
		}else{
			$this->error("没有这个方法！");
			//return false;
		}
	}
}
?>