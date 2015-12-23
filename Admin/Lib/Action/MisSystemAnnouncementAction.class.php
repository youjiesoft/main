<?php
/**
 * @Title: MisSystemAnnouncement 
 * @Package package_name 
 * @Description: todo(系统公告) 
 * @author libo 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-12-23 下午4:43:08 
 * @version V1.0
 */
class MisSystemAnnouncementAction extends CommonAction{
	
	public function _filter(&$map){
		$_REQUEST ['orderDirection'] = "desc";
		$_REQUEST ['orderField'] = "`top`,`sendtime`";
		if( !isset($_SESSION['a']) ){//不是管理员 只能看到在范围内的公告
			$map['status']=1;
		}
		//点击左侧树判断右侧显示
		$this->assign('type',$_REQUEST['type']);//type
		$this->assign('typeid',$_REQUEST['typeid']);//type
		$this->assign("myAnnouncement",$_REQUEST['myAnnouncement']);
		if($_REQUEST['type']){
			$map['type']=$_REQUEST['type'];
		}
		if($_REQUEST['ptype']){//ptype
			$map['ptype']=$_REQUEST['ptype'];
		}
		if($_REQUEST['audit'] ||$_REQUEST['waitAudit']==2 ||$_REQUEST['waitAudit']==3){
			$map['commit']=2;
		}
		if(!$_REQUEST['type'] && !$_REQUEST['typeid'] && !$_REQUEST['myAnnouncement']){
			$map['createid']=$_SESSION[C('USER_AUTH_KEY')];
		}else if(!$_REQUEST['myAnnouncement']){
			$map['commit']=1;
		}else if($_REQUEST['myAnnouncement']){
			$map['createid']=$_SESSION[C('USER_AUTH_KEY')];
		}
	}
	public function _before_index(){
		//判断置顶时间
		$MisSystemAnnouncementModel=D("MisSystemAnnouncement");
		$Mlist=$MisSystemAnnouncementModel->field("id,top,toptime,endtime,commit")->where("status=1")->select();
		$time=time();
		foreach ($Mlist as $k=>$v){
			if($v['commit'] ==1 && $v['endtime'] <= $time && $v['endtime'] > 0){//判断是否过期 过期终止
				$v['commit']=4;
				$v['top']=2;
				$MisSystemAnnouncementModel->where("id=".$v['id'])->save($v);
				$this->transaction_model->commit();//事务提交
			}
			if($v['toptime'] != 0 && $v['top'] == 1){//置顶
				if($v['toptime'] < $time){
					$v['top']=2;
					$MisSystemAnnouncementModel->where("id=".$v['id'])->save($v);
					$this->transaction_model->commit();//事务提交
				}
			}
		}
		if(getCommonSettingSkey("SYSTEM_ANNOUNCEMENT")){//开启开关 发布公告需审核
			//判断是否有权限 有则显示
			if($_SESSION['a'] or $_SESSION['missystemannouncement_auditupdate']){
				$iswaitAudit=2;
				$this->assign("iswaitAudit",$iswaitAudit);
			}
		}
		// 审核
		if($_REQUEST['audit']){
			$audit=$_REQUEST['audit'];
			$this->assign('audit',$audit);
		}
		if(!isset($_SESSION['a'])){
			$this->assign("userid",$_SESSION[C('USER_AUTH_KEY')]);
		}
	}
	public function index() {
		//分页过滤
		if( isset($_REQUEST["type"]) && $_REQUEST["type"]==0 ){
			unset($_REQUEST["type"]);
		}
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
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
		//扩展工具栏操作
		$toolbarextension = $scdmodel->getDetail($name,true,'toolbar');
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		if ($_REQUEST['waitAudit'] == "1") {//已发布
			$this->getannoucemeTree();//公告类型树
			$this->display('passauditindex');
			exit;
		}
		if ($_REQUEST['waitAudit'] == "2") {//待审核
			$this->getaudittree();// 审核树
			$this->display('waitAuditindex');
			exit;
		}
		if ($_REQUEST['waitAudit'] == "3") {//待审核
			$this->getaudittree();// 审核树
			$this->display('waitAuditindexview');
			exit;
		}
		if ($_REQUEST['jump']) {
			$this->display('indexview');
			exit;
		}
		$this->display();exit;
	}
	function _after_list(&$list){
		//查出最新发布的三条数据
		$model=D("MisSystemAnnouncement");
		$mlist=$model->field("id")->where("status=1 and commit=1")->order("sendtime desc")->limit(3)->select();
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
	}
	/**
	 * @Title: _before_add
	 * @Description: todo(新增之前操作)
	 * @author yuanshanlin
	 * @date 2013-11-27 上午11:31:55
	 * @throws
	 */
	public function _before_add(){
		if($_REQUEST['typeid']){//左侧树添加
			$MSASmodel=D("MisSystemAnnouncementSet");
			$MSASpid=$MSASmodel->where("status=1 and id=".$_REQUEST['id'])->getField('pid');
			if($MSASpid==0){//点击的主类型
				$pname=$MSASmodel->where("status=1 and id=".$_REQUEST['id'])->getField('name');
				$pid=$_REQUEST['id'];
			}else{
				$pname=$MSASmodel->where("status=1 and id=".$MSASpid)->getField('name');
				$pid=$MSASpid;
			}
			$this->assign("pname",$pname);
			if($_REQUEST['id']==-1){//顶级节点
				$apid=-1;
			}else{
				$apid=$MSASmodel->where("status=1 and id=".$_REQUEST['id'])->getField("pid");
			}
			$this->assign("apid",$apid);
			$this->assign("pid",$pid);
			$this->display("addtype");exit;
		}
		$this->assign('time',time());
		$this->assign('ptype',$_REQUEST['ptype']);
		$this->assign('type',$_REQUEST['type']);
		//查询公告类型
		$model=D("MisSystemAnnouncementSet");
		$list=$model->where("status=1 and pid=0")->select();
		$this->assign("list",$list);
		//查询下级类型
		if($_REQUEST['type']==0){//点击的顶级节点新增时
			$plist=$model->where("status=1 and pid=".$list['0']['id'])->select();
		}else{
			$plist=$model->where("status=1 and pid=".$_REQUEST['type'])->select();
		}
		$this->assign('plist',$plist);
		$ttime=date('Y-m-d',time()+2400*3*24);
		$this->assign('ttime',$ttime);
	}
	//修改数据方法
 	public function update(){
 		if($_REQUEST['typeid']){//左侧树的修改类型
 			$MSASmodel=D("MisSystemAnnouncementSet");
 			if($_POST['typevalue']==1){//判断添加的类型 1为主类型
 				$_POST['pid']=0;
 			}
 			if (false === $MSASmodel->create ()) {
 				$this->error ( $MSASmodel->getError () );
 			}
 			$re=$MSASmodel->save();
 			if($re === false){
 				$this->error("操作失败");
 			}else{
 				$this->success("操作成功");
 			}
 		}
 		$id=$_REQUEST['id'];
 		if(getCommonSettingSkey("SYSTEM_ANNOUNCEMENT")){//开启开关 发布公告需审核
 			if($_POST['commit'] ==5){
 				$_POST['commit']=5;
 			}else if($_POST['commit'] ==1){
 				$_POST['commit']=2;//代表审核中
 				$_POST['auditstatus']=1;
 			}
 		}
 		//判断未选部门和人员时
 		if(!$_POST['deptid'] && !$_POST['personid']){
 			$_POST['scopetype']=3;
 		}
 		if($_POST['deptid'] || $_POST['personid']){
 			$_POST['scopetype']=2;
 		}
 		if($_POST['deptid'] <> "" && $_POST['personid'] <> ""){
 			$_POST['scopetype']=1;
 		}
 		//将选择的部门(多个)转化数组为字符串
		if($_POST['deptid']){
			$_POST['deptid']=implode(',',$_POST['deptid']);
		}else{
			$_POST['deptid']=null;
		}
		//将选择的人员id(多个)数组转化为字符串
		if($_POST['personid']){
			$_POST['personid']=implode(',',$_POST['personid']);
		}else{
			$_POST['personid']=null;
		}
 		$_POST['updateid']=$_SESSION[C('USER_AUTH_KEY')];
 		$_POST['updatetime']=time();
 		if($_POST['endtime']){
 			$_POST['endtime']=strtotime($_POST['endtime'])+24*3600-1;
 		}else{
 			$_POST['endtime']=0;
 		}
 		$_POST['toptime']=strtotime($_POST['toptime'])+24*3600-1;
 		$_POST['starttime']=strtotime($_POST['starttime']);
 		if(!isset($_POST['ptype'])){
 			$_POST['ptype']=null;
 		}
 		//创建公告表对象
		$MSAmodel=D('MisSystemAnnouncement');
		$affecta=$MSAmodel->data($_POST)->save();
		if($affecta){
			$px=$this->swf_upload($id,78);//上传附件
			$this->success('操作成功!');
		}else{
			$this->error('操作失败!');
		}
 	}
 	public function insert(){
 		if($_REQUEST['typeid']){//左侧树的添加类型
 			$MSASmodel=D("MisSystemAnnouncementSet");
 			if($_POST['typevalue']==1){//判断添加的类型 1为主类型
 				$_POST['pid']=0;
 				$_POST['typeid']=1;
 			}else if($_POST['typevalue']==2){//子类型
 				$_POST['typeid']=2;
 				$_POST['pid']=$_REQUEST['pid'];
 			}
 			if (false === $MSASmodel->create ()) {
 				$this->error ( $MSASmodel->getError () );
 			}
 			$re=$MSASmodel->add();
 			if($re === false){
 				$this->error("操作失败");
 			}else{
 				$this->success("操作成功");
 			}
 		}
 		if(getCommonSettingSkey("SYSTEM_ANNOUNCEMENT")){//开启开关 发布公告需审核
 			if($_POST['commit'] ==5){
 				$_POST['commit']=5;
 			}else if($_POST['commit'] ==1){
 				$_POST['commit']=2;//代表审核中
 			}
 		}
 		//判断未选部门和人员时
 		if($_POST['deptid']=="" && $_POST['personid']==""){
 			$_POST['scopetype']=3;
 		}
 		if($_POST['deptid'] || $_POST['personid']){
 			$_POST['scopetype']=2;
 		}
 		if($_POST['deptid'] <> "" && $_POST['personid'] <> ""){
 			$_POST['scopetype']=1;
 		}
 		//将选择的部门(多个)转化数组为字符串
 		if($_POST['deptid']){
 			$_POST['deptid']=implode(',',$_POST['deptid']);
 		}
 		//将选择的人员id(多个)数组转化为字符串
 		if($_POST['personid']){
 			$_POST['personid']=implode(',',$_POST['personid']);
 		}
 		//公告结束时间
 		if($_POST['endtime']){
 			$_POST['endtime']=strtotime($_POST['endtime'])+24*3600-1;
 		}else{
 			$_POST['endtime']=0;
 		}
 		$_POST['type']=$_POST['type'];
 		$_POST['toptime']=strtotime($_POST['toptime'])+24*3600-1;
 		//公告开始时间转化时间戳
 		$_POST['starttime']=strtotime($_POST['starttime']);
 		//公告结束时间戳
 		$_POST['createid']=$_SESSION[C('USER_AUTH_KEY')];
 		//创建公告表对象
 		$MSAModel=D('MisSystemAnnouncement');
 		$result=$MSAModel->data($_POST)->add();
 		if($result){
 			$this->swf_upload($result,78);//上传附件
 			$this->success('操作成功!');
 		}else{
 			$this->error('操作失败!');
 		}
 	}
	/**
	 * @Title: MisSystemAnnouncementAction
	 * @Package 工共应用-系统公告：功能类
	 * @Description: TODO(系统公告的发布,编辑前置函数)
	 * @author yuanshanlin
	 * @company 重庆特米洛科技有限公司˾
	 * @copyright 重庆特米洛科技有限公司˾
	 * @date 2013-12-4 17:47:54
	 * @version V1.0
	 */
 	public  function _before_edit(){
 		if($_REQUEST['typeid']){//左侧树修改
 			$MSASmodel=D("MisSystemAnnouncementSet");
 			$MSASpid=$MSASmodel->where("status=1 and id=".$_REQUEST['id'])->getField('pid');
 			if($MSASpid==0){//点击的主类型
 				$pname="顶级节点";
 			}else{
 				$pname=$MSASmodel->where("status=1 and id=".$MSASpid)->getField('name');
 			}
 			$this->assign("pname",$pname);
 			$this->assign('pid',$MSASpid);//pid
 			$vo=$MSASmodel->where("status=1 and id=".$_REQUEST[id])->find();
 			$this->assign("vo",$vo);
 			$plist=$MSASmodel->where("status=1 and pid=0")->select();
 			$this->assign("plist",$plist);
 			if($_REQUEST['id']==-1){
 				$this->error("顶级节点不能修改");exit;
 			}
 			$this->display("edittype");exit;
 		}
 		//查询当前公告类型
 		$MisSystemAnnouncementModle=D('MisSystemAnnouncement');
 		$type=$MisSystemAnnouncementModle->where("id=".$_REQUEST['id'])->getfield("type");
 		//查询公告类型
 		$model=D("MisSystemAnnouncementSet");
 		$list=$model->where("status=1 and pid=0")->select();
 		$this->assign("list",$list);
 		//查询下级类型
 		$plist=$model->where("status=1 and pid=".$type)->select();
 		$this->assign('plist',$plist);
 		$id=$_REQUEST['id'];
 		//实例化公告'对象'模型
 		$MisSystemAnnouncementList=$MisSystemAnnouncementModle->where('id='.$id)->find();
 		$deptList=explode(',',$MisSystemAnnouncementList['deptid']);
 		$personidList=explode(',',$MisSystemAnnouncementList['personid']);
		//判断,如果为空就不传过去
		if($deptList[0]!=null){
			$this->assign('deptList',$deptList);
		}
		if($personidList[0]!=null){
			$this->assign('personidList',$personidList);
		}
 		$this->getAttachedRecordList($_REQUEST['id']);
 		$this->assign('time',time());
 	}
 	/**
 	 * @Title: auditedit 
 	 * @Description: todo(公告审核)   
 	 * @author libo 
 	 * @date 2014-1-27 下午1:33:46 
 	 * @throws
 	 */
 	public function audit(){
 		$this->assign("time",time());
 		$this->view();
 	}
 	public function announcementAuditUpdate(){
 		//审核状态 2为通过 3为未通过
 		$data=$_POST;
 		$data['auditstatus']=$_POST['auditstatus'];
 		if($data['auditstatus'] == 2){
 			$data['commit']=1;
 		}else if($data['auditstatus'] == 3){
 			$data['commit']=3;
 		}
 		$model=D("MisSystemAnnouncement");
 		$list=$model->where("id=".$_REQUEST['id'])->save($data);
 		if($list){
 			$this->success("操作成功");
 		}else{
 			$this->error("操作失败");
 		}
 	}
 	/**
 	 * @Title: top 
 	 * @Description: todo(公告快速置顶)   
 	 * @author libo 
 	 * @date 2013-12-25 上午10:36:18 
 	 * @throws
 	 */
 	public function totop(){
 		//接收id top
 		$id=$_REQUEST['id'];
 		$top=$_REQUEST['top'];
 		$data=$_REQUEST;
 		$data['toptime']=time()+24*3600*3;
 		$Model=D('MisSystemAnnouncement');
 		$list=$Model->where("id=".$id)->save ($data);
 		if($list === false){
 			$this->error('操作失败');
 		}
 		$this->success('操作成功');
 	}
 	//公告查看
 	public function view(){
 		$MSAmodel=D("MisSystemAnnouncement");
 		$list=$MSAmodel->where("status=1 and id=".$_REQUEST['id'])->find();
 		$this->assign("vo",$list);
 		$typeid=$list['type'];//公告主类型id
 		$ptypeid=$list['ptype'];//公告子类型id
 		//查询公告类型
 		$MSASmodel=D("MisSystemAnnouncementSet");
 		$list=$MSASmodel->where("status=1 and id=".$typeid)->getField("name");
 		$this->assign("list",$list);
 		//查询下级类型
 		$plist=$MSASmodel->where("status=1 and id=".$ptypeid)->getField("name");
 		$this->assign('plist',$plist);
 		//实例化公告'对象'模型
 		$MSAlist=$MSAmodel->where('status=1 and id='.$_REQUEST['id'])->find();
 		$deptList=explode(',',$MSAlist['deptid']);
 		$personidList=explode(',',$MSAlist['personid']);
 		$deptempty=array('0'=>'全体部门');//未选部门和人员时
		//判断,如果为空就不传过去
		if($deptList[0]!=null){
			$this->assign('deptList',$deptList);
		}
		if($personidList[0]!=null){
			$this->assign('personidList',$personidList);
		}else{
			$this->assign('deptList',$deptempty);
		}
 		$this->getAttachedRecordList($_REQUEST['id']);
 		//读取动态配制
 		$this->getSystemConfigDetail($name,$list);
 		$this->display();
 	}
 	//查询结果
	public function lookupdepartment($par){
		$this->assign('par', $_REQUEST['par']);
		//构造部门等级树形
		if($_REQUEST['par'] == 'User' && empty($_REQUEST['dept_id'])) {
			$model = D('MisSystemDepartment');
			$list = $model->where($map)->select();
			$treemiso=array();
			$param['url']="__URL__/lookupdepartment/jump/1/par/".$_REQUEST['par']."/dept_id/#id#";
			$param['rel']="cementdepartmentindexBox";
			$typeTree = $this->getTree($list,$param,$treemiso);
			$this->assign('departmentTree',$typeTree);
			}
			if ($_REQUEST['dept_id']) {
				$this->assign('dept_id', $_REQUEST['dept_id']);
				$map['dept_id']=$_REQUEST['dept_id'];
			}
	 		$map['status']=1;
			if(isset($_POST['keyword'])&&$_POST['keyword']!=null){
				$map['name']=array('like','%'.$_POST['keyword'].'%');//名字
			}
			$this->assign('keyword',$_POST['keyword']);
			$this->assign('searchby',$_POST['seachby']);//下拉框选定,传回去
	 		$this->_list($_REQUEST['par'], $map);
	 		//搜索角色
	 		if($_REQUEST['par']=='Duty'){
	 			$this->display('lookupduty');
	 			exit;
	 		}
	 		//搜索人员
	 		if ($_REQUEST['par'] == 'User') {
	 			if($_REQUEST['jump']){
	 				$this->display('lookuppersonlist');
	 				exit;
	 			} else {
	 				$this->display('lookupperson');
	 				exit;
	 			}
	 		}
	 		//搜索部门
	 		if ($_REQUEST['par'] == 'MisSystemDepartment') {
	 			$this->display('lookupdepartment');
	 			exit;
	 		}
	 	}
	 	/**
	 	 * (non-PHPdoc)左侧公告类型树
	 	 * @see CommonAction::getTree()
	 	 */
 	public function getannoucemeTree(){
 		$model=D("MisSystemAnnouncementSet");
//  		$list = $model->where('status=1')->field("id,pid,name")->select();
//  		foreach ($list as $key => $val) {
//  			$list[$key]['parentid'] = $val['pid'];
//  		}
//  		$param['rel']="missystemannouncementbox";
//  		$param['url']="__URL__/index/jump/1/waitAudit/1/id/#id#";
//  		$typeTree = $this->getTree($list,$param);
//  		$this->assign('typetree', $typeTree);
 		//查询顶节点
 	    $list = $model->where('status=1 and pid=0')->select();
 		$list=$model->where('pid=0 and status=1')->select();
 		$tree = array(); // 树初始化
 		$treez[] = array(
 				'id' => -1,
 				'pId' => 0,
 				'name' => '我的公告管理',
 				'title' => '我的公告管理',
 				'rel' => "missystemannouncementbox",
 				'target' => 'ajax',
 				'icon' => "",
 				'url' => "__URL__/index/jump/2/myAnnouncement/1",
 				'open' => true
 		);
 		$treez[] = array(
 				'id' => -1,
 				'pId' => 0,
 				'name' => '系统公告',
 				'title' => '系统公告',
 				'rel' => "missystemannouncementbox",
 				'target' => 'ajax',
 				'icon' => "",
 				'url' => "__URL__/index/jump/2/typeid/1",
 				'open' => true
 		);
 		$tree=$treez;
 		foreach ($list as $key=>$value){
 			$treenode[] = array(
 					'id' => $value['id'],
 					'pId' => -1,
 					'name' =>$value['name'],
 					'title' =>$value['name'],
 					'rel' => "missystemannouncementbox",
 					'target' => 'ajax',
 					'icon' => "",
 					'url' => "__URL__/index/type/".$value['id']."/jump/2",
 					'open' => true
 			);
 		}
 		if($treenode){
 			$tree=array_merge($tree,$treenode);
 		}
 		foreach ($list as $key=>$value){
 			$plist=$model->where("status=1 and pid=".$value['id'])->select();
 			foreach ($plist as $k=>$v){
 				$treep[] = array(
 						'id' => $v['id'],
 						'pId' => $v['pid'],
 						'name' => $v['name'],
 						'title' => $v['name'],
 						'rel' => "missystemannouncementbox",
 						'target' => 'ajax',
 						'icon' => "",
 						'url' => "__URL__/index/type/".$value['id']."/ptype/".$v['id']."/jump/2",
 						'open' => true
 				);
 			}
 		}
 		if($treep){
 			$tree=array_merge($tree,$treep);
 		}
 		$this->assign("typetree",json_encode($tree));
 	}
 	/**
 	 * @Title: getaudittree 
 	 * @Description: todo(审核树)   
 	 * @author libo 
 	 * @date 2014-2-14 上午9:12:12 
 	 * @throws
 	 */
 	public function getaudittree(){
 		$trees[] = array(
 				'id' => -1,
 				'pId' => 0,
 				'name' => '待审核',
 				'title' => '待审核',
 				'open' => true
 		);
 		$treess[] = array(
 				'id' => 1,
 				'pId' => -1,
 				'name' => '待审核',
 				'title' => '待审核',
 				'open' => true
 		);
 		$trees=array_merge($trees,$treess);
 		$this->assign("tree",json_encode($trees));
 	}
 	/**
 	 * @Title: lookupcomboxgetBankAccount 
 	 * @Description: todo(公告类型二级联动)   
 	 * @author libo 
 	 * @date 2014-1-3 下午4:13:37 
 	 * @throws
 	 */
 	public function lookupcomboxgetBankAccount(){
 		//
 		$bepart=$_POST['bepart'];
 		$Model = D('MisSystemAnnouncementSet');
 		$userlist=$Model->where("pid=".$bepart.' and status=1')->field("id,name")->select();
 		$arr2=array();
 		foreach($userlist as $k=>$v){
 			$arr2[$k][]=$v['id'];
 			$arr2[$k][]=$v['name'];
 		}
 		if($arr2){
 			echo json_encode($arr2);
 		} else {
 			echo false;
 		}
 	}
 	public function delete() {
 		if($_REQUEST['typeid']){
 			$id=$_REQUEST['id'];
 			if($id==-1){
 				$this->error("顶级节点不能删除");
 			}
 			$MSASmodel=D("MisSystemAnnouncementSet");
 			$list=$MSASmodel->where('id='.$id)->find();
 			$mname=$list['name'];
 			//判断父节点有没有子节点
 			$modelList=$MSASmodel->where("status = 1 and pid=".$id)->select();
 			//判断节点下是否有内容
 			$MSAmodel=D("MisSystemAnnouncement");
 			$map["_string"]="(status=1 and (type=".$id." or ptype=".$id."))";
 			$annlist=$MSAmodel->where($map)->select();
 			if($modelList){//父节点下有子节点
 				$this->error("【".$mname."】下有子节点,请先删除子节点。");
 			}
 			if($annlist){//节点下有内容
 				$this->error("【".$mname."】下有内容,请先删除内容。");
 			}else{
 				$re=$MSASmodel->where("id=".$id)->setField ( 'status', - 1 );
 				if($re === false){
 					$this->error("操作失败");
 				}else{
 					$this->success("操作成功");
 				}
 			}
 		}
 		$name=$this->getActionName();
 		$model = D ($name);
 		//删除指定记录
 		if (! empty ( $model )) {
 			$pk = $model->getPk ();
 			$id = $_REQUEST [$pk];
 			if (isset ( $id )) {
 				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
 				// 超管对已经成为删除状态的数据进行测试无记录的删除
 				if($_SESSION['a']){
 					$condition["status"] = array ("eq",-1);
 					$list=$model->where ( $condition )->delete();
 					$condition["status"] = array ("neq",-1);
 				}
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
 	 * @Title: announcementGetBack 
 	 * @Description: todo(撤回成新建方法)   
 	 * @author libo 
 	 * @date 2014-4-11 下午4:36:09 
 	 * @throws
 	 */
 	function announcementGetBack(){
 		$MSAmodel=D("MisSystemAnnouncement");
 		//改状态为新建
 		$list=$MSAmodel->where("id=".$_REQUEST['id'])->setField('commit',$_REQUEST['commit']);
 		$mSAU=M("mis_system_announcement_user");
 		//改已读为未读
 		$list=$mSAU->where("announceid=".$_REQUEST['id'])->setField('status',0);
 		if($list !== false){
 			$this->success("操作成功");
 		}else{
 			$this->error("操作失败");
 		}
 	}
}
?>