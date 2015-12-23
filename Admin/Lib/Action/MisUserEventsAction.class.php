<?php
/**
 * @Title: MisUserEventsAction
 * @Package package_name
 * @Description: todo(重要日程)
 * @author renling
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-2-28 上午9:39:02
 * @version V1.0
 */
class MisUserEventsAction extends CommonAction{

	private $bgcolor = array(
			'1' => array(
					'index' => '1',
					'color' => 'myblue',
					'checked' => false,
			),
			'2' => array(
					'index' => '2',
					'color' => 'myorange',
					'checked' => false,
			),
			'3' => array(
					'index' => '3',
					'color' => 'myred',
					'checked' => false,
			)
	);
	/**
	 * @Title: _filter
	 * @Description: todo(检索)
	 * @param unknown_type $map
	 * @author laicaixia
	 * @date 2013-5-31 下午4:56:21
	 * @throws
	*/
	public function _filter(&$map) {
		$mapList=array();
		$map['status'] = 1;
		//条件搜索
		if($_POST['keywordindex']){
			$where['text'] = array('like','%'.$_POST['keywordindex'].'%');//主要内容
			$where['personname'] = array('like','%'.$_POST['keywordindex'].'%');//相关人员
			$where['_logic'] = 'or';
			$map['_complex'] = $where;
			$this->assign('keywordindex',$_POST['keywordindex']);
		}
	}
	public function index(){
		//当前登陆人id
		$this->assign('loginuserid',$_SESSION [C ( 'USER_AUTH_KEY' )]);
		if ($_REQUEST['jump']) {
			//首页收件箱调用方法，为ajax调用
			$this->display('indexview');
		} else {
			$this->display();
		}
	}
	/**
	 * @Title: app
	 * @Description: todo(暂时没有用)
	 * @author laicaixia
	 * @date 2013-5-31 下午4:56:40
	 * @throws
	 */
	private function app() {
		$d=isset($_REQUEST['d'])?$_REQUEST['d']:"";
		$model = D('MisUserEvents');
		if($d=='xml'){
			$xml="<data>";
			header("Content-Type: text/xml;");

			$list = $model->where('userid='.$_SESSION[C('USER_AUTH_KEY')].' and status = 1')->field('id,text,startdate,enddate,details')->select();
			foreach ($list as $k => $v) {
				$xml.="<event id='".$k."' start_date='".date("Y-m-d H:i", $v['startdate'])."' end_date='".date("Y-m-d H:i", $v['enddate'])."' text='".$v['details']."' />";
			}
			$xml.="</data>";
			echo $xml;
			exit;
		} else if($d=='date'){
			$curday=$_REQUEST['time'];
			$list = $model->where('userid='.$_SESSION[C('USER_AUTH_KEY')].' and status = 1 and \''.$curday.'\'>=FROM_UNIXTIME((startdate+3600*8+59),"%Y-%m-%d") and \''.$curday.'\'<=FROM_UNIXTIME((enddate+3600*8+59),"%Y-%m-%d")')->select();
			$this->assign("list",$list);
			$this->display("date");
		}
		else{
			$this->display();
		}
	}
	/**
	 * @Title: _before_add
	 * @Description: todo(进入新增)
	 * @author laicaixia
	 * @date 2013-5-31 下午4:56:43
	 * @throws
	 */
	public function _before_add(){
		$enddate = $_REQUEST['enddate']?$_REQUEST['enddate']:time();
		$startdate = $_REQUEST['startdate']?$_REQUEST['startdate']:time();
		if($enddate === $startdate){
			$enddate = $enddate+(60*60*24)-1;
		}
		$bgcolor = $this->bgcolor;
		$bgcolor[1]['checked'] = true;
		$this->assign('bgcolor', $bgcolor);
		$this->assign('startdate',$startdate);
		$this->assign('enddate',$enddate);
		$this->assign('type',$_REQUEST['type']);
	}
	/**
	 * @Title: _before_insert
	 * @Description: todo(获得关联人员的名称)
	 * @author xiafengqin
	 * @date 2013-7-19 上午11:33:08
	 * @throws
	 */
	public function _before_insert(){
		$id = $_REQUEST['personid'];
		if(count($id)>0){
			$_POST['personid'] = implode(',',$id);
			$model = D('User');
			$nameary = array();
			foreach ($id as $val){
				$nameary[] = $model->where('id='.$val)->getField('name');
			}
			$_POST['personname'] = implode(',',$nameary);
		} else {
			$_POST['personid'] = "";
			$_POST['personname'] = "";
		}
	}
	/**
	 * @Title: _after_insert
	 * @Description: todo(进入新增)
	 * @param unknown_type $id
	 * @author laicaixia
	 * @date 2013-5-31 下午4:57:00
	 * @throws
	 */
	public function _after_insert($id){
		$this->swf_upload($id,65);
		$this->systemMessage($id);
	}
	/**
	 * @Title: _before_update
	 * @Description: todo(获得要修改的人名)
	 * @author xiafengqin
	 * @date 2013-7-19 下午3:42:25
	 * @throws
	 */
	public function _before_update(){
		if ($_REQUEST['personid']) {
			$userid = $_REQUEST['personid'];
			$_POST['personid'] = implode(',',$userid);
			$model = D('User');
			$map['id'] = array('in',$userid);
			$nameary = $model->where($map)->getField('name',true);
			$_POST['personname'] = implode(',',$nameary);
		}else {
			$_POST['personid'] = '';
			$_POST['personname'] = '';
		}
	}
	/**
	 * @Title: _after_update
	 * @Description: todo(_after_update)
	 * @param unknown_type $id
	 * @author jiangx
	 * @throws
	 */
	public function _after_update(){
		$this->swf_upload($_POST['id'],65);
	}
	public function _before_edit(){
		$map = array();
		$map['id'] = $_REQUEST['id'];
		$map['status'] = 1;
		$model = D('MisUserEvents');
		$vo  = $model->where($map)->find();
		/* if($vo['enddate'] === $vo['startdate']){ */
		$enddate = $vo['enddate']+(60*60*24)-1;
		/* } */
		$this->assign('startdate',$vo['startdate']);
		$this->assign('enddate',$enddate);
	}
	public function  _after_edit($vo){
		//加载附件信息
		$map = array();
		$map["status"]  =1;
		$map["orderid"] =$_REQUEST['id'];
		$map["type"] =65;
		$m=M("mis_attached_record");
		$attarry=$m->where($map)->select();
		$this->assign('attarry',$attarry);
		if ($vo['personid']) {
			$personnameary = explode(',',$vo['personname']);
			$personidary = explode(',',$vo['personid']);
			$this->assign('personnameary',$personnameary);
			$this->assign('personidary',$personidary);
		}
		$bgcolor = $this->bgcolor;
		if ($bgcolor[$vo['color']]) {
			$bgcolor[$vo['color']]['checked'] = true;
		} else {
			$bgcolor[1]['checked'] = true;
		}
		$this->assign('bgcolor', $bgcolor);
	}
	//查看
	/**
	* @Title: lookupview
	* @Description: todo(查看)
	* @author laicaixia
	* @date 2013-5-31 下午4:57:29
	* @throws
	*/
	public function lookupview(){
		$id = $_GET['id'];
		$model = D('MisUserEvents');
		$list = $model->where('status = 1 and id = '.$id)->find();
		$this->assign('vo',$list);
		$map = array();
		$map["status"]  =1;
		$map["orderid"] =$id;
		$map["type"] =65;
		$m=M("mis_attached_record");
		$attarry=$m->where($map)->select();
		$this->assign('attarry',$attarry);
		//添加日程重要程度的颜色
		if ($vo['personid']) {
			$personnameary = explode(',',$vo['personname']);
			$personidary = explode(',',$vo['personid']);
			$this->assign('personnameary',$personnameary);
			$this->assign('personidary',$personidary);
		}
		$bgcolor = $this->bgcolor;
		if ($bgcolor[$vo['color']]) {
			$bgcolor[$vo['color']]['checked'] = true;
		} else {
			$bgcolor[1]['checked'] = true;
		}
		$this->assign('bgcolor', $bgcolor);
		$this->display();
	}
	/**
	 * @Title: lookupGetdate
	 * @Description: todo(获得存在数据库中的日程安排)
	 * @author xiafengqin
	 * @date 2013-7-11 上午11:23:50
	 * @throws
	 */
	public function lookupGetdate(){
		$start = strtotime($_POST['start']);
		$end = strtotime($_POST['end']);
		$model = D('MisUserEvents');
		$map = array();
		$map['status'] = 1;
		$map['_string'] = ' (FIND_IN_SET( '. $_SESSION[C('USER_AUTH_KEY')].' , personid ) OR userid = '.$_SESSION[C('USER_AUTH_KEY')].') and ( startdate <'.$end.' and enddate >='.$start.')';
		$list = $model->where($map)->select();
		$arr = array();
		$bgcolor = $this->bgcolor;
		foreach ($list as $key => $value) {
			$start = transTime($value['startdate'],'Y-m-d H:i:s');
			$end = transTime($value['enddate'],'Y-m-d H:i:s');
			//86400秒=1天 
			$allDay = true;
			if(($value['enddate'] - $value['startdate']) < 86400){
				$allDay = false;
			}
			if (!$value['importancedegree']) {
				$value['importancedegree'] = 1;
			}
			$scheduletype = $value['scheduletype'] == 2 ? '[工作]' : '[个人]';
			$arr[] = array(
					'id' => $value['id'],
					'createid' => $value['createid'],
					'title' => $scheduletype . missubstr($value['text'],18,true),
					'start' => $start,
					'end' => $end,
					'allDay' =>$allDay,
					'importancedegree' =>$value['importancedegree'],
					'className' => $bgcolor[$value['importancedegree']]['color']
			);
		}
		echo json_encode($arr);
	}
	/**
	 * @Title: lookupUpdateDate
	 * @Description: todo(移动日程后修改数据库信息)
	 * @author xiafengqin
	 * @date 2013-7-12 上午10:47:34
	 * @throws
	 */
	public function lookupUpdateDate(){
		$id = $_REQUEST['id'];
		$changeSize = $_REQUEST['changeSize'];
		if ($changeSize) {
			$_POST['enddate'] = strtotime($_POST['enddate']);
			$_POST['startdate'] = strtotime($_POST['startdate']);
		}else {
			$_POST['enddate'] = strtotime($_POST['enddate']);
			$_POST['startdate'] = strtotime($_POST['startdate']);
		}
		$model = D('MisUserEvents');
		$rel = $model->where('id='.$id)->save($_POST);
		$model->commit();
		if ($rel) {
			exit('1');
		}else {
			exit('0');
		}
	}
	/**
	 * @Title: lookupDelete
	 * @Description: todo(edit页面里面的Delete方法)
	 * @author xiafengqin
	 * @date 2013-7-13 下午3:46:33
	 * @throws
	 */
	public function lookupDelete() {
		//删除指定记录
		$model = D ('MisUserEvents');
		$pk = $model->getPk ();
		$id = $_REQUEST [$pk];
		$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
		$list=$model->where ( $condition )->setField ( 'status', - 1 );
		if ($_REQUEST['edit']) {
			$model->commit();
			exit($list);
		}else {
			$this->success("删除成功！");
		}
	}
	/**
	 * @Title: lookuptraceindex
	 * @Description: todo(工作日程加载页面)
	 * @author xiafengqin
	 * @date 2013-7-13 下午4:00:13
	 * @throws
	 */
	public function lookuptraceindex(){
		$map=$this->_search();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		if ($_SESSION["a"] != 1) {
			$map['mis_user_events.status']=array('gt',-1);
		}
		//今天
		$today1 = strtotime(date('Y-m-d'));
		$today2 = strtotime(date("Y-m-d",strtotime("+1 day")));
		$todaymap['_string'] ='startdate >='.$today1.' AND startdate < '.$today2 .") AND (FIND_IN_SET(".$_SESSION [C ( 'USER_AUTH_KEY' )].",personid) OR createid = ".$_SESSION [C ( 'USER_AUTH_KEY' )];
		$todaymap['scheduletype'] = 2;
		$todaymap['status'] = 1;
		//查看关联人员有自己的邮件
		//$todaymap['_string'] = "FIND_IN_SET(".$_SESSION [C ( 'USER_AUTH_KEY' )].",personid) OR createid = ".$_SESSION [C ( 'USER_AUTH_KEY' )];
		$model = D('MisUserEvents');
		$todayList = $model ->where($todaymap)->order('enddate DESC')->select();
		$n = count($todayList);
		$arr = array();
		for ($i = 0; $i < 4-$n; $i++) {
			$arr[] = $i;
		}
		$this->assign("arrList",$arr);
		$this->assign('todayList',$todayList);
		$this->assign('today1',$today1);
		//明天
		$tomorrowmap = array();
		$tomorrow1 = strtotime(date("Y-m-d",strtotime("+1 day")));
		$tomorrow2 = strtotime(date("Y-m-d",strtotime("+2 day")));
		$tomorrowmap['_string'] ='startdate >='.$tomorrow1.' AND startdate < '.$tomorrow2 .") AND (FIND_IN_SET(".$_SESSION [C ( 'USER_AUTH_KEY' )].",personid) OR createid = ".$_SESSION [C ( 'USER_AUTH_KEY' )];
		$tomorrowmap['scheduletype'] = 2;
		$tomorrowmap['status'] = 1;
		//查看关联人员有自己的邮件
		//$tomorrowmap['_string'] = "FIND_IN_SET(".$_SESSION [C ( 'USER_AUTH_KEY' )].",personid) OR createid = ".$_SESSION [C ( 'USER_AUTH_KEY' )];
		$tomorrowList = $model ->where($tomorrowmap)->order('enddate DESC')->select();
		$n = count($tomorrowList);
		$tomorrowArr = array();
		for ($i = 0; $i < 4-$n; $i++) {
			$tomorrowArr[] = $i;
		}
		$this->assign("tomorrowArrList",$tomorrowArr);
		$this->assign('tomorrowList',$tomorrowList);
		$this->assign('tomorrow1',$tomorrow1);
		//今天以前
		$todaybefore['startdate'] = array('LT', strtotime(date('Y-m-d')));
		$todaybefore['scheduletype'] = 2;
		$todaybefore['status'] = 1;
		//查看关联人员有自己的邮件
		$todaybefore['_string'] = "FIND_IN_SET(".$_SESSION [C ( 'USER_AUTH_KEY' )].",personid) OR createid = ".$_SESSION [C ( 'USER_AUTH_KEY' )];
		$todaybeforeList = $model ->where($todaybefore)->order('enddate DESC')->limit(5)->select();
		$count = $model ->where($todaybefore)->order('id DESC')->count();
		$this->assign('count',$count);
		$this->assign('todaybeforeList',$todaybeforeList);
		//明天以后
		$tomorrowafter['startdate'] = array('EGT', strtotime(date("Y-m-d",strtotime("+2 day"))));
		$tomorrowafter['scheduletype'] = 2;
		$tomorrowafter['status'] = 1;
		//查看关联人员有自己的邮件
		$tomorrowafter['_string'] = "FIND_IN_SET(".$_SESSION [C ( 'USER_AUTH_KEY' )].",personid) OR createid = ".$_SESSION [C ( 'USER_AUTH_KEY' )];
		$tomorrowafterList = $model ->where($tomorrowafter)->order('enddate DESC')->limit(5)->select();
		$n = count($tomorrowafterList);
		$beforArr = array();
		for ($i = 0; $i < 4-$n; $i++) {
			$beforArr[] = $i;
		}
		$this->assign("beforArrList",$beforArr);
		$countresult = $model ->where($tomorrowafter)->order('enddate DESC')->count();
		$this->assign('tomorrowafterList',$tomorrowafterList);
		$this->assign('countresult',$countresult);
		$this->assign('loginuserid',$_SESSION [C ( 'USER_AUTH_KEY' )]);
		$this->display();
	}
	/**
	 * @Title: lookupMoreresoult
	 * @Description: todo(查看更多页面)
	 * @author xiafengqin
	 * @date 2013-7-13 下午3:59:19
	 * @throws
	 */
	public function lookupMoreresoult(){
		$daysa = $_REQUEST['daysa'];
		$model = D('MisUserEvents');
		$map['scheduletype'] = 2;
		$map['status'] = 1;
		if ($daysa == 3){
			$map['startdate'] = array('LT', strtotime(date('Y-m-d')));
		}else {
			$map['startdate'] = array('EGT', strtotime(date("Y-m-d",strtotime("+2 day"))));
		}
		$voList = $model ->where($map)->order('id DESC')->select();
		$this->assign('voList',$voList);
		$this->display();
	}
	/**
	 * @Title: systemMessage
	 * @Description: todo(发系统消息)
	 * @param unknown_type $infoid
	 * @param unknown_type $userid
	 * @param unknown_type $type
	 * @return string
	 * @author xiafengqin
	 * @date 2013-7-18 上午10:51:18
	 * @throws
	 */
	public function systemMessage($infoid){
		$model = D("MisUserEvents");
		$vo = $model->where('id='.$infoid)->find();
		if (!$vo) {
			return '';
		}
		if($vo['personid']){
			//接收人
			$personidary=explode(",",$vo['personid']);
			$sendusername = getFieldBy($_SESSION[C('USER_AUTH_KEY')],'id','name','User');
			$messageTitle = '日程安排通知';
			foreach ($personidary as $key=>$personid){
				$recipientname = getFieldBy($personid,'id','name','User');
				$messageContent = '';
				$messageContent .='
					<p></p>
					<span></span>
					<div style="width:98%;">
						<p class="font_darkblue">' . $recipientname . '，您好！</p>
							<p><strong>' . $sendusername . ' </strong> 发布了一条关于您的日程安排：
								<strong><a target="dialog" width="850" height="600" rel="MisUserEventsView" mask="true" title="日程安排" href="__APP__/MisUserEvents/lookupview/id/'.$infoid.'">' . $vo['text'] . '</a></strong> ，请配合。</p>
									<p>日程的详细情况：</p>
									<ul>
										<li><strong>日程名称：</strong>
											<a class="edit" href="javascript:;">
											' . $vo['text'] . '
													</a>
													</li>
													<li>
													<strong>计划开始时间：</strong>' . date('Y-m-d',$vo['startdate']) . '
															</li>
															<li>
															<strong>计划结束时间：</strong>' . date('Y-m-d',$vo['enddate']) . '
																	</li>
																	<li>
																	<strong>任务描述：</strong> ' . ($vo['details'] ? $vo['details'] : "(无)") . '
																			</li>
																			</ul>
																			<p>如果您有任何问题，请联系' . $sendusername . '。</p>
																					</div>';
				//系统推送消息
				$this->pushMessage(array($personid), $messageTitle, $messageContent);
			}
		}
	}
	public function _before_lookuptraceadd(){
		$stype = $_REQUEST['stype'];
		$val = 2;
		if($stype){
			$this->assign('scheduletype',$val);
		}
	}
	public function lookuptraceadd(){
		$bgcolor = $this->bgcolor;
		$bgcolor[1]['checked'] = true;
		$this->assign('bgcolor', $bgcolor);
		$this->display();
	}
	/**
	 * @Title: lookuptraceedit
	 * @Description: todo(工作日程页面的修改)
	 * @author xiafengqin
	 * @date 2013-7-26 下午4:49:30
	 * @throws
	 */
	public function lookuptraceedit(){
		$model = D ('MisUserEvents');
		$map['id'] = $_REQUEST['id'];
		if ($_SESSION["a"] != 1) $map['status'] = 1;
		$vo = $model->where($map)->find();
		if(empty($vo)){
			$this->display ("Public:404");
			exit;
		}
		$module=A('MisUserEvents');
		if (method_exists($module,"_after_lookuptraceedit")) {
			call_user_func(array(&$module,"_after_lookuptraceedit"),&$vo);
		}
		$this->assign('vo',$vo);
		$bgcolor = $this->bgcolor;
		if ($bgcolor[$vo['color']]) {
			$bgcolor[$vo['color']]['checked'] = true;
		} else {
			$bgcolor[1]['checked'] = true;
		}
		$this->assign('bgcolor', $bgcolor);
		$this->display();
	}
	public function  _after_lookuptraceedit($vo){
		//加载附件信息
		$map = array();
		$map["status"]  =1;
		$map["orderid"] =$_REQUEST['id'];
		$map["type"] =65;
		$m=M("mis_attached_record");
		$attarry=$m->where($map)->select();
		$this->assign('attarry',$attarry);
		if ($vo['personid']) {
			$personnameary = explode(',',$vo['personname']);
			$personidary = explode(',',$vo['personid']);
			$this->assign('personnameary',$personnameary);
			$this->assign('personidary',$personidary);
		}
	}
	/**
	 * 工作台的新增日程
	 */
	public function addSchedule(){
		$endtime = strtotime(date('Y-m-d'))+(60*60*24)-1;
		$this->assign('startdate',time());
		$this->assign('enddate',$endtime);
		$this->display("MisUserEvents:addSchedule");
	}
}
?>