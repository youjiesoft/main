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

class MisSystemPanelAction extends CommonAction {
	public function index() {
		//组装树
		$groupModel=D('group');
		$groupList=$groupModel->where("status=1 ")->getField("id,name");
		$tree = array(); // 树初始化
		$tree[] = array(
				'id' => -1,
				'pId' => 0,
				'name' => '组名称',
				'title' => '组名称',
				'rel' => "missystempanellookupindex",
				'target' => 'ajax',
				'url' => "__URL__/index/jump/1",
				'open' => true
		);
		foreach ($groupList as $gkey=>$gval){
			$tree[] = array(
					'id' => $gkey,
					'pId' => -1,
					'name' => $gval,
					'title' => $gval,
					'rel' => "missystempanellookupindex",
					'target' => 'ajax',
					'url' => "__URL__/index/jump/1/group_id/".$gkey,
					'open' => true
			);
		}
		$this->assign("tree",json_encode($tree));
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		if($_REQUEST['group_id']){
			$map['group_id']=$_REQUEST['group_id'];
			$this->assign("group_id",$_REQUEST['group_id']);
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
			$this->display('indexview');exit;
		}
		if($_REQUEST['type']){
			$this->display ('lookupindex');
		}else{
			$this->display ();
		}
	}
	/**
	 *
	 * @Title: lookupSysPanel
	 * @Description: todo(显示系统默认面板)
	 * @$groupid string 用于判断是菜单进入的分组模块          ##  xyz 2014-10-27
	 * @author renling
	 * @date 2014-9-1 下午3:38:33
	 * @throws
	 */
	public function lookupSysPanel($groupid=''){
		//设置参数$groupid，用于判断是菜单进入的分组模块
		$MisSystemPanelModel=D("MisSystemPanel");
		$MisSystemPanelList=$MisSystemPanelModel->getSysPanel();
		$mappanel = array();
		if($groupid){
			$mappanel['group_id'] = $groupid;
		}
		$mappanel['status'] = 1;
		$mappanel['isbasepanel'] = 1;
		$model = D("MisSystemPanel");
		$userRoleModel = D("MisSystemPanelUserRole");
		//获取固定面板
		$fixlist = $model->where($mappanel)->order("sort asc")->select();
		$pnnelhtmllist = array();
		foreach ($fixlist as $key => $val) {
			if (method_exists($this, $val['methodname'])) {
				$pnnelhtmllist[] = $this->$val['methodname']($val['name'], $val['id'].'-'.$val['sort'],'',$groupid);
			}
		}
		if($_GET['nvigetTo']){
		//获取用户可查看的面板list
		$uerrole = $userRoleModel->getUserRolePanel();
		//获取当前用户部门可查看面板list
		$MisSystemPanelDepartmentRoleModel = D("MisSystemPanelDepartmentRole");
		$departmentrole =$MisSystemPanelDepartmentRoleModel->getDepartRolePanel();
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
		$mappanel['isbasepanel'] = array("neq",1);
		$panellist = $model->where($mappanel)->order("sort ASC")->select();
		//查出排序
		$mPanelUserSort = D("MisSystemPanelUsersort");
		$userSort = $mPanelUserSort->getUserSort();
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
		//组装面板显示的html
		$runpannellist =array();
		foreach($panellistnew as $v){
			if($v['modelname']){
				$runpannellist[] = $this->runpanel($v,$groupid);
			}
		}
			$this->assign('runpannellist',$runpannellist);
		}
				
		$this->assign("pnnelhtmllist", $pnnelhtmllist);
	}
	/**
	 * @Title: notice
	 * @Description: todo(自动面板)
	 * @author xyz
	 * @date 2014-10-22 下午6:22:56
	 * @throws
	 */
	private function runpanel($val,$groupid){
		$length = 30; //当为显示2的时候 限制显示长度 
		$model = D($val['modelname']);	
		$this->assign('isscroll',$val['issroll']);
		$val['map'] = str_replace (array ('&quot;','&#39;','&lt;','&gt;'), array ('"', "'",'<','>'),$val['map']);
		$conlist = $model->field($val['showtitle'])->where($val['map'])->select();
		$this->assign('name',$val['name']);
		$showtitle = explode(',',$val['showtitle']);
		$this->assign("url",$val['url']);
		$this->assign('showtitle',$showtitle);
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($val['modelname']);
		
		foreach($conlist as $conk=>$conv){
			foreach($detailList as $k=>$v){
				foreach($showtitle as $k1=>$v1){
					if($v['name']==$v1&&$v['func'][0][0]=='getFieldBy'&&$v['funcdata']){
							$conlist[$conk][$v1] = getFieldBy($conv[$v1], $v['funcdata'][0][0][1], $v['funcdata'][0][0][2], $v['funcdata'][0][0][3]);	
					}
				}
			}
			if($val['showtitletype']==2){
				$conlist[$conk]['str'] = $conlist[$conk][$showtitle[0]];
				$conlist[$conk][$showtitle[0]] = missubstr($conv[$showtitle[0]], $length,'...');
			}
		}
		$this->assign('conlist',$conlist);		
		$this->assign('model',$val['modelname']);		
		if($groupid) $this->assign('groupid',$groupid);
		if($val['type']==2){
			//iframe
			return	$this->fetch("MisSystemPanel:runpaneliframe");
		}else{
			if($val['showtitletype']==1){
				return	$this->fetch("MisSystemPanel:runpanel1");
			}else{
				return	$this->fetch("MisSystemPanel:runpanel2");
			}
		}
		
		
	}
	
	
	/**
	 * @Title: notice
	 * @Description: todo(公司新闻)
	 * @author 杨东
	 * @date 2013-3-28 下午3:22:56
	 * @throws
	 */
	private function notice($name = "", $sort,$color='',$groupid){
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
		if($groupid) $this->assign('groupid',$groupid);
		$list = D('MisNotice')->getNoticeList();
		$this->assign('noticelist',$list);
		return $this->fetch("MisSystemPanel:notice");
	}
	/**
	 * @Title: knowledgeSee
	 * @Description: todo(首页面板知识库)
	 * @author yuansl
	 * @date 2014-4-28 下午2:01:23
	 * @throws
	 */
	public function knowledgeSee($name="", $sort,$color='',$groupid){
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
		if($groupid) $this->assign('groupid',$groupid);
		return $this->fetch("MisSystemPanel:knowledgeSee");
	}
	/**
	 * @Title: SystemNotices
	 * @Description: todo(系统公告)
	 * @author 杨东
	 * @date 2013-3-15 下午5:45:07
	 * @throws
	 */
	private function systemNotices($name = "", $sort,$color='',$groupid){
		//获取当前登录人部门id 角色id
		$userModel=M("user");
		$userList=$userModel->where("id=".$_SESSION[C('USER_AUTH_KEY')])->find();
		//
		$this->assign('sort', $sort);
		if (!$name) {
			$name = "系统公告";
		}
		$this->assign('name', $name);
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
		if($groupid) $this->assign('groupid',$groupid);
		//点击更多 初始默认类型
		$ptypelist=$snmodel->group("type")->where($map)->limit("0,1")->getfield('type');
		$this->assign('ptypelist',$ptypelist);
		return $this->fetch("MisSystemPanel:systemNotices");
	}
	/**
	 * @Title: expertQuestions
	 * @Description: todo(专家库)
	 * @author yuansl
	 * @date 2014-4-28 上午9:55:48
	 * @throws
	 */
	public function expertQuestions ($name="", $sort,$color='',$groupid){
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
		$this->assign('name',"专家库");
		$this->assign('lastFiveQues',$lastFiveQues);
		if(!false==$groupid=$groupid?$groupid:$_REQUEST['groupid']) $this->assign('groupid',$groupid);
		if($_REQUEST['type']){
			$this->display('inlExpertQuestions');
			exit;
		}
		return $this->fetch("MisSystemPanel:expertQuestions");
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
		//获取附件信息
		$this->getAttachedRecordList($id,true,true,$actionname);
		/* $attarry=$MisAttachedRecordModel->where($condition)->select();
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
		} */
		$this->display();
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
				$result=$mPanelUsersort->where($aMap)->save();
				$mPanelUsersort->commit();
			} else {
				$result=$mPanelUsersort->where($aMap)->add();
				$mPanelUsersort->commit();
			}
			C('TOKEN_ON',true);
		}
		exit;
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
	public function workplatform($name = "", $sort , $type=0,$groupid){
	//	获取当前登录人部门id 角色id
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
		if(!false==$groupid=$groupid?$groupid:$_REQUEST['groupid']) $this->assign('groupid',$groupid);
		if($type==2){
			$this->onlineManager(4,0,true);
		}else{
			$this->onlineManager();
		}
		if($type){
			$this->display('workplatformindex');
			exit;
		}
		return $this->fetch("MisSystemPanel:workplatform");
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
	private function onlineManager($num=4,$userid=0 ,$myself=false){
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
	public function _before_add(){
		$this->assign("group_id",$_REQUEST['modeid']);
		//查询连字符
		$html=getSelectByHtml('roleinexp','select');
		$html= str_replace('"', "'", $html);
		$this->assign("html",$html);
		$this->lookgroupandnode();
		
	}
	public function _before_insert(){
		if($_POST['modelid']){
			$_POST['modelname']=getFieldBy($_POST['modelid'], "id", "name", "node");
		}
		$_POST['showtitle']=implode(',',$_POST['showtitle']);
		$_POST['map']= str_replace("&#39;", "'", html_entity_decode($_POST['rules']));
		$_POST['showmap']=str_replace("&#39;", "'", html_entity_decode($_POST['showrules']));
		//$this->lookupinsertMap();
	}
	public function update(){
		//获取固定面板
		$model = D('MisSystemPanel');
		$fixlist = $model->where('status=1 and isbasepanel=1')->order("sort asc")->select();
		$fixlistarr = array();
		foreach ($fixlist as $key => $val) {
			if (method_exists($this, $val['methodname'])) {
				$fixlistarr []= $val['id'];
			}
		}
		if($_POST['modelid']){
			$_POST['modelname']=getFieldBy($_POST['modelid'], "id", "name", "node");
		}
		$_POST['showtitle']=implode(',',$_POST['showtitle']);
		$_POST['map']=str_replace("&#39;", "'", html_entity_decode($_POST['rules']));
		$_POST['showmap']=str_replace("&#39;", "'", html_entity_decode($_POST['showrules']));
		$this->lookupinsertMap();
		if(in_array($_POST['id'],$fixlistarr)) $this->error('该面板不允许修改');
		$name=$this->getActionName();
		$model = D ( $name );
		if (false ===  $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$list=$model->save ();
		if (false !== $list) {
			//修改附件信息
			$this->swf_upload($_POST['id']);
			//执行成功后，用A方法进行实例化，判断当前控制器中是否存在_after_update方法
			$module2=A($name);
			if (method_exists($module2,"_after_update")) {
				call_user_func(array(&$module2,"_after_update"),$list);
			}
			$this->success ( L('_SUCCESS_'));
		} else {
			$this->error ( L('_ERROR_') );
		}
	}
	public function _after_edit(&$vo){
		$this->assign("group_id",$_REQUEST['modeid']);
		$vo['showrules']=$vo['showmap'];
		$vo['rules']=$vo['map'];
		$showtitle = explode(',',$vo['showtitle']);
		$this->assign("isfirst","1");
// 		foreach($showtitle as $k=>$v){
// 			$showtitle[$k]=getFieldBy($v,"name","id","node");
// 		}
		$vo["showtitle"]=$showtitle;
		$vo['modelid']=getFieldBy($vo['modelname'], "name", "id", "node");
		$vo['group_id']=getFieldBy($vo['modelname'], "name", "group_id", "node");
		$this->lookgroupandnode();
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($vo['modelname']);
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		$showtitle1=array();
		foreach($detailList as $v){
			if(in_array($v['name'],$showtitle)){
				$showtitle1[]=$v['showname'];
			}
		}
		$this->assign("showtitle",$showtitle1);
		$smapList=array();
		$this->assign("smapList",$smapList);
		$this->assign("rlistarr",$rlistarr);
	}
	function delete(){
		//获取固定面板id集 不允许删除
		$model = D('MisSystemPanel');
		$fixlist = $model->where('status=1 and isbasepanel=1')->order("sort asc")->select();
		$fixlistarr = array();
		foreach ($fixlist as $key => $val) {
			if (method_exists($this, $val['methodname'])) {
				$fixlistarr []= $val['id'];
			}
		}
		$id=$_POST['id'];
		$idarr = explode(',',$id);
		if(count($idarr)==1){
			if(in_array($idarr[0],$fixlistarr)){
				$this->error("该面板禁止删除！");
			}
		}else{
			foreach($idarr as $k=>$v){
				if(in_array($v,$fixlistarr)) unset($idarr[$k]);
			}
		}
		if (isset ( $id )) {
			$condition = array ('id' => array ('in', $idarr ) );
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
	private function lookgroupandnode(){
		//组装菜单及操作级
		$NodeModel=D('Node');
		$NodeList=$NodeModel->where("status=1 and level=3")->getField("id,title,group_id");
		$groupList=array();
		$newNodelList=array();
		foreach ($NodeList as $key=>$val){
			if(in_array($val['group_id'],array_keys($groupList))){
				$newNodelList['node'][]=array(
						'id'=>$val['id'],
						'name'=>$val['title'],
						'group_id'=>$val['group_id'],
				);
			}else{
				if(!$defaultVal){
					$defaultVal=$val['id'];
				}
				$groupList[$val['group_id']]=1;
				$newNodelList['group'][]=array(
						'id'=>$val['group_id'],
						'name'=>getFieldBy($val['group_id'], "id", "name", "group"),
						'sort'=>getFieldBy($val['group_id'], "id", "sorts", "group"),
				);
				$newNodelList['node'][]=array(
						'id'=>$val['id'],
						'name'=>$val['title'],
						'group_id'=>$val['group_id'],
				);
			}
		}
		$newNodelList['group']=array_merge(array_sort($newNodelList['group'],'sort','asc'));
		$this->assign("defaultVal",$defaultVal);
		$this->assign("nodelist",json_encode($newNodelList));
	}
	public function lookupdetail(){
		$scdmodel = D('SystemConfigDetail');
		$modelName=getFieldBy($_REQUEST['modelid'], "id", "name", "node");
		$detailList = $scdmodel->getDetail($modelName);
		if($_REQUEST['type']==1){
			$modelname=$_REQUEST['modelname'];
			$this->assign("modelname",$modelname);
			$this->display();
		}else{
			$detailList['modelname']=$modelName;
			echo json_encode($detailList);
		}
	}
	public function lookupdetailadd(){
		$scdmodel = D('SystemConfigDetail');
		$modelName=getFieldBy($_REQUEST['modelid'], "id", "name", "node");
		$detailList = $scdmodel->getDetail($modelName);
		if($_REQUEST['type']==1){
			$modelname=$_REQUEST['modelname'];
			$this->assign("modelname",$modelname);
			$this->display();
		}else{
			$detailList['modelname']=$modelName;
			echo json_encode($detailList);
		}
	}
	/**
	 * @Title: lookupcomboxregulation
	 * @Description: todo(Ajax规则获取类型)
	 * @author libo
	 * @date 2014-3-17 下午4:36:34
	 * @throws
	 */
	public function lookupcomboxregulation(){
		$modelName=getFieldBy($_REQUEST['modelid'], "id", "name", "node");
		//读取配置字段
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($modelName);
		$arr=array();
		foreach($detailList as $k=>$v){
			if($v['name']== $_POST['name']){
				$arr=$v;//筛选出当前数组
				break;
			}
		}
		//if(isset($arr['func'])){//是否有联查
		if(isset($arr['func'])&& $arr['func'][0][0]=="getFieldBy"){
			$tbmodel=D($arr['funcdata'][0][0][3]);
			$list=$tbmodel->field($arr['funcdata'][0][0][1].",".$arr['funcdata'][0][0][2])->where("status=1")->select();
			$arr2=array();
			foreach($list as $k=>$v){
				$arr2[$k][]=$v[$arr['funcdata'][0][0][1]];
				$arr2[$k][]=$v[$arr['funcdata'][0][0][2]];
			}
			echo json_encode($arr2);
		}else if($arr['func'][0][0]=="transTime"||$arr['func'][0][0]=="transtime"){
			echo 1;
		}else{
			echo -1;
		}
	}
	private function lookupinsertMap(){
		if($_POST['modelid']){
			$_POST['modelname']=getFieldBy($_POST['modelid'], "id", "name", "node");
		}
	}

	
}
?>