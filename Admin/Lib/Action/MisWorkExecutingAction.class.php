<?php
/** 
 * @Title: MisWorkExecutingAction 
 * @Package package_name
 * @Description: todo(工作执行) 
 * @author 杨希
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-2-21 上午10:15:21 
 * @version V1.0 
*/ 
class MisWorkExecutingAction extends MisSystemWorkCenterAction{
	
	private  function getNewLeftTree(){
		//工作协同数组
		$synergy=array(
				'0'=>array('id'=>111,'name'=>'待办事项','md'=>'MisOaItems','type'=>3),
				'1'=>array('id'=>112,'name'=>'已办事项','md'=>'MisOaItems','type'=>4),
				'2'=>array('id'=>113,'name'=>'已发事项','md'=>'MisOaItems','type'=>1),
						/* '3'=>array('id'=>114,'name'=>'待发事项','md'=>'MisOaItems','type'=>2) */);
		$this->assign("synergy",$synergy);
		
		//工作审批数组
		$examine=array(
				'0'=>array('id'=>111,'name'=>'待我审批','jumptemp'=>1,'num'=>$myworkwait,'md'=>'MisWorkMonitoring','worktype'=>1),
				'1'=>array('id'=>112,'name'=>'我已审批','jumptemp'=>1,'num'=>$myworkdone,'md'=>'MisWorkMonitoring','worktype'=>2),
				'2'=>array('id'=>113,'name'=>'已发待批','jumptemp'=>1,'num'=>'','md'=>'MisWorkMonitoring','worktype'=>3),
				'3'=>array('id'=>114,'name'=>'已发办结','jumptemp'=>1,'num'=>'','md'=>'MisWorkMonitoring','worktype'=>4));
		$this->assign("examine",$examine);
		
		//-----------项目执行和分派数据----------------//
		$MisWorkExecutingModel = D("MisWorkExecuting");
		$list=$MisWorkExecutingModel->getUserWorkExecutList();
		$this->assign("arrlist",$list);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CommonAction::index()
	 */
	public function index() {
		//第一步、组合左侧的结构
		$this->getNewLeftTree();
		//从系统待处理点击过来的
		$this->assign("mytodoname",$_REQUEST['mytodoname']);
		//获取查询的模型
		$md=$_REQUEST['md'];
		
		$jump = 1;
		if($_REQUEST['jump']){
			$jump = $_REQUEST['jump'];
		}
		$this->assign("jump",$jump);
		if($jump == 2){
			$UserOftenMenuModel=D('UserOftenMenu');
			//调用用户自行添加常用
			$oftenList=$UserOftenMenuModel->getSysOftenList();
			$this->assign("oftenList",$oftenList);
			$this->display("indexmy");exit;
		}
		//工作审核
		if($md == "MisWorkMonitoring"){
			if($jump == 'jump'){
				$this->getWorkMonitList();
				exit;
			}else{
				if($_REQUEST['jumptemp']>2){
					$this->getWorkoldinfo();
					exit;
				}else{
					$this->getWorkMonitinfo();
					exit;
				}
			}
		}
		//工作协同
		if($md == "MisOaItems"){
			$this->getOaItemsList();exit;
		}
		//项目执行
		//if($md == 'MisWorkExecuting'){
// 			$this->display ();
			//$this->lookupExecuting();exit;
		//}
		//工作知会
		if($jump == 6){
			//$this->lookupInformperson();exit;
			$this->lookupMisNotify();exit;
		}
		if($jump == 7){//进入到发起工作
			$this->getMyWork();
			$this->getOftenWork();
			$this->display();
			exit;
		}
		if($_REQUEST['type']){
			//修改跳转页面 发起工作
			$this->display('initiateWork');exit;
		}else{
			if($_REQUEST['jump']=='1'){
				$this->display('indexList');exit;
			}
			if($_REQUEST['jump']=="jump"){
				$this->display('depindex');exit;
			}
			$this->display ();
		}
		return;
	}

	function lookupUpdateNotify(){
		$id=$_REQUEST['id'];
		$notifyModel=D('MisNotify');
		$list=$notifyModel->where(array('id'=>$id))->setField('isread','1');
		$notifyModel->commit();
		echo json_encode($list);
	}
	
	function lookupMisNotify(){
		$notifyModel=D('MisNotify');
		$name='MisNotify';
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		$map ['_string'] = 'FIND_IN_SET(  ' . $_SESSION [C ( 'USER_AUTH_KEY' )] . ',recipient )';
		//$map['recipient']=array('like',"%".$_SESSION [C ( 'USER_AUTH_KEY' )]."%");
		$map['isread']=0;
		$searchby =  $_POST["searchby"];
		$keyword=$this->escapeChar($_POST["keyword"]);
		$searchtype = $_POST['searchtype'];
		$this->assign('keyword',$keyword);
		$this->assign('searchby',$searchby);
		$this->assign('searchtype',$searchtype);
		if($keyword){
				$map[$searchby] = ($searchtype==2) ? array('like',"%".$keyword."%"):$keyword;
		}
		
		$searchby=array(
				array("id" =>"title","val"=>"主题"),
				array("id" =>"orderno","val"=>"单据编号"),
				array("id" =>"name","val"=>"单据名称"),
		);
		$searchtype=array(array("id" =>"2","val"=>"模糊查找"),
				array("id" =>"1","val"=>"精确查找"));
		$this->assign("searchbylist",$searchby);
		$this->assign("searchtypelist",$searchtype);
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
		if($_REQUEST['jump']){
			$this->assign("jump",$_REQUEST['jump']);
		}
		$this->display ();
		return;
	}
	/**
	 * @Title: lookupInformperson
	 * @Description: todo(查看知会人)
	 * @author libo
	 * @date 2014-6-11 下午3:47:24
	 * @throws
	 */
	public function lookupInformperson(){
		if($_REQUEST['istepperson']){
			$this->getworkinforpersonmessage();
		}else{
			$this->assign("indexstepperson",1);
		}
		$name = $_REQUEST['md'];
		$title=getFieldBy($_REQUEST['md'], 'name', 'title', 'node');
		$this->assign("title",$title);
		$this->assign("md",$_REQUEST['md']);
		$map = $this->_search ($name);
		//查询条件
		$workModel=D("MisWorkExecuting");
		$uid=$_SESSION[C('USER_AUTH_KEY')];
		$this->assign("uid",$uid);
		if(!isset($_SESSION['a'])){//非管理员
			$wmap['_string']="( FIND_IN_SET('$uid', mis_work_executing.informpersonid) or FIND_IN_SET('$uid', p.informpersonid))";
		}
		$wmap['mis_work_executing.tablename']=$_REQUEST['md'];
		$worklist=$workModel->where($wmap)->field("mis_work_executing.tableid")->join(" process_info as p on mis_work_executing.ptmptid=p.id")->select();
		$informpersonArr=array();
		foreach ($worklist as $k=>$v){
			$informpersonArr[]=$v['tableid'];//满足条件的 tableid
		}
		$map['id']=array('in',$informpersonArr);
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
			$dataarr="action,auditState";//过滤
			foreach ($detailList as $k=>$v) {
				if(in_array($v['name'], explode(",", $dataarr))){
					unset($detailList[$k]);
				}
			}
			$this->assign ( 'detailList', $detailList );
		}
		if( intval($_POST['dwzloadhtml']) ){
			$this->display("dwzloadindex");exit;
		}
		//加载配置文件 判断是否是dialog打开方式
		$dialogList=require DConfig_PATH."/System/dialogconfig.inc.php";
		$this->assign('dialogList',$dialogList);
		//首页收件箱调用方法，为ajax调用
		if ($_GET['type'] == "ajaxcall") {
			$this->display ("ajax_index");exit;
		}
		if($_REQUEST['tableid']){
			$this->assign("tableid",$_REQUEST['tableid']);
		}
		if($_REQUEST['jump']){
			$this->assign("jump",$_REQUEST['jump']);
		}
		$this->display ();
		return;
	}
	/**
	 * @Title: _before_lookupExecuting 
	 * @Description: todo(前置)   
	 * @author libo 
	 * @date 2014-5-14 上午11:39:55 
	 * @throws
	 */
	public function getExecutingtype(){
		$title=getFieldBy($_REQUEST['md'], "name", "title", "Node");
		$this->assign("title",$title);
		$ExecutingSetModel=D("MisWorkExecutingSet");
		//查询数据
		$list=$ExecutingSetModel->where("status=1 and name='".$_REQUEST['md']."'")->find();
		$this->assign("isshow",$list['isshow']);
		$this->getleftmessagetree($list);
	}
	
	public function lookupExecuting(){
		$istep = $_REQUEST['istep'];
		if(1==$istep){
			// 获取当前控制器名称
			$name = 'MisSalesMyProject';
			// 列表过滤器，生成查询Map对象
			$map = $this->_search ($name);
			$MisWorkExecutingModel = D("MisWorkExecuting");
			$MisWorkExecutingModel->_filter("MisSalesMyProject",$map);
			$map['type']=1;
			$this->assign('type',1);
			// 查询数据
			$this->_list ( $name, $map );
			
			// begin
			$scdmodel = D ( 'SystemConfigDetail' );
			// 读取列名称数据(按照规则，应该在index方法里面)
			$detailList = $scdmodel->getDetail ( $name );
			if ($detailList) {
				$this->assign ( 'detailList', $detailList );
			}
			// 扩展工具栏操作
			$toolbarextension = $scdmodel->getDetail ( $name, true, 'toolbar' );
			if ($toolbarextension) {
				$this->assign ( 'toolbarextension', $toolbarextension );
			}
			// end
		}elseif(2==$istep){
			// 获取当前控制器名称
			$name = 'MisAutoQzu';
			// 列表过滤器，生成查询Map对象
			$map = $this->_search ($name);
			$this->assign('type',1);
			
			$MisWorkExecutingModel = D("MisWorkExecuting");
			$MisWorkExecutingModel->_filter("MisSalesProjectAllocation",$map);
			
			// 查询数据
			$this->_list ( $name, $map );
			
			// begin
			$scdmodel = D ( 'SystemConfigDetail' );
			// 读取列名称数据(按照规则，应该在index方法里面)
			$detailList = $scdmodel->getDetail ( $name );
			if ($detailList) {
				$this->assign ( 'detailList', $detailList );
			}
			// 扩展工具栏操作
			$toolbarextension = $scdmodel->getDetail ( $name, true, 'toolbar' );
			if ($toolbarextension) {
				$this->assign ( 'toolbarextension', $toolbarextension );
			}
			// end
		}
		
		// 获取输出到的html模板
		$this->assign ( 'istep', $_REQUEST['istep'] );
		if($_REQUEST['jump'] == 'jump'){
			$this->display('lookupExecutingListvo');exit;
		}
		$this->display ();
		return;
	}
	/**
	 * @Title: getlefttree
	 * @Description: todo(获取左侧公共树)
	 * @author libo
	 * @date 2014-4-21 下午2:12:44
	 * @throws
	 */
	public function getleftmessagetree($list){
		$tree = array(); // 树初始化
		$tree[] = array(
				'id' => 0,
				'pId' => 0,
				'name' => '全部',
				'title' => '全部',
				'open' => true
		);
		$tree[] = array(
				'id' => 11111,
				'pId' => 0,
				'name' => $list['typenameone'],
				'title' => $list['typenameone'],
				'rel' => $_REQUEST['rel']."box",
				'target' => 'ajax',
				'icon' => "",
				'url' => "__URL__/lookupExecuting/jump/jump/dotype/0/md/".$_REQUEST['md']."/rel/".$_REQUEST['rel'],
				'open' => true
		);
		if($list['isshow']){
			$tree[] = array(
					'id' => 11113,
					'pId' => 0,
					'name' => $list['typenametow'],
					'title' => $list['typenametow'],
					'rel' => $_REQUEST['rel']."box",
					'target' => 'ajax',
					'icon' => "",
					'url' => "__URL__/lookupExecuting/jump/jump/dotype/2/md/".$_REQUEST['md']."/rel/".$_REQUEST['rel'],
					'open' => true
			);
		}
		$tree[] = array(
				'id' => 11112,
				'pId' => 0,
				'name' => $list['typenamethree'],
				'title' => $list['typenamethree'],
				'rel' => $_REQUEST['rel']."box",
				'target' => 'ajax',
				'icon' => "",
				'url' => "__URL__/lookupExecuting/jump/jump/dotype/1/md/".$_REQUEST['md']."/rel/".$_REQUEST['rel'],
				'open' => true
		);
		$this->assign("tree",json_encode($tree));
	}
	/**
	 * @Title: _before_edit
	 * @Description: todo(编辑页面)
	 * @author libo
	 * @date 2014-4-22 上午10:26:07
	 * @throws
	 */
	public function edit(){
		if($_REQUEST['md']=="MisLogisticsFixLog"){
			//查询报修设备
			$model = D("MisLogisticsFixLogSub");
			$map['masid'] = $_REQUEST['id'];
			$map['status'] = 1;
			$sublist = $model->where($map)->select();
			foreach ($sublist as $key=>$val){  //计算可用数量
				$sublist[$key]['sumreturnqty']=getFieldBy($val['objid'], 'id', 'returnqty', 'mis_work_facility_distribute')+$val['qty'];
			}
			$this->assign("sublist",$sublist);
		}
		$model=D($_REQUEST['md']);
		$list=$model->where("id=".$_REQUEST['id'])->find();
		$this->assign("vo",$list);
		$this->getSystemConfigDetail($_REQUEST['md']);//读取配置文件
// 		dump($_REQUEST['rel']);
		$this->assign("md",$_REQUEST['md']);
		$this->assign("rel",$_REQUEST['rel']);
		$this->assign("time",time());
		$this->assign("userid",$_SESSION[C('USER_AUTH_KEY')]);
		//获取附件
		$this->getAttachedRecordList($_REQUEST['id'],true,true,$_REQUEST['md']);
		//点击处理后 邮件提示未读状态修改为已读
		$workModel=D("MisWorkExecuting");
		$uid=$_SESSION[C('USER_AUTH_KEY')];
		$messexecutid=$workModel->where("tableid=".$_REQUEST['id']." and tablename='".$_REQUEST['md']."'")->getField("messexecutid");
		$messageuserModel=M("mis_message_user");
		$re=$messageuserModel->where("sendid=".$messexecutid." and recipient=".$uid)->setField("readedStatus",1);
// 		if($re===false){
// 			$this->error("操作失败");
// 		}
		$this->display($_REQUEST['md']);//显示模板
	}
	/**
	 * (non-PHPdoc)
	 * @see CommonAction::update()
	 */
	public function update(){
// 		dump($_REQUEST);die;
		//B('FilterString');
		$name=$_REQUEST['md'];
		$model = D ( $name );
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$list=$model->save ();
		if (false !== $list) {
			//改变执行状态
			$workModel=M("mis_work_executing");
			$workModel->where("tableid=".$_REQUEST['id']." and tablename='".$_REQUEST['md']."'")->setField("dotype", $_REQUEST['dotype']);
			//仅仅在派车时候 派车完成 发送邮件通知申请人
			if($name ==="MisRequestCar"){
				$createid = getFieldBy($_REQUEST['id'], 'id', 'createid', $name);
				$modelname = $name;
				$this->sendcarmessage($createid,$modelname,$_REQUEST['id'],2);
			}
			if($name=="MisLogisticsFixLog"){
				$submodel=D("MisLogisticsFixLogSub");
				$MisWorkFacilityDistributeModel=D('MisWorkFacilityDistribute');
				$msid=$_POST['id'];
				//查找该mas下的sub
				$subMap['masid']=$msid;
				$subMap['status']=1;
				$subList=$submodel->where($subMap)->select();
				foreach ($subList as $key=>$val){
					$ManageDate=array();
					$ManageDate['id']=$val['appsubid'];
					//还原数量 如是报废 未修复 减少总数量
					if($_POST['fixed']=='2'||$_POST['fixed']=='3'){
						//分布数量减去
						$ManageDate['appqty']=array("exp","appqty-".$val['qty']);
						//设备管理总数量减少
						$MisWorkFacilityManageModel=D("MisWorkFacilityManage");
						$fManageDate=array();
						$fManageDate['id']=$val['manageid'];
						$fManageDate['qty']=array("exp","qty-".$val['qty']);
						$fManageDate['kyqty']=array("exp","kyqty-".$val['qty']);
						$fManageDate['amount']=array("exp","unitprice*qty-".$val['qty']);
						$result=$MisWorkFacilityManageModel->save($fManageDate);
						$MisWorkFacilityManageModel->commit();
						if(!$result){
							$this->error("数据修改失败！");
						}
					}else{
						$ManageDate['returnqty']=array("exp","returnqty+".$val['qty']);
					}
					$MisWorkFacilityDistributeModel->save($ManageDate);
					$MisWorkFacilityDistributeModel->commit();
				}
			}
			$this->success ( L('_SUCCESS_'));
		} else {
			$this->error ( L('_ERROR_') );
		}
	}
	
	public function sendcarmessage($sendidArr,$modelName,$tableid,$noticeType){
		//派车人
		$sendcarid = getFieldBy($tableid, 'id', 'updateid', $modelName);
		$sendcarname = getFieldBy($sendcarid, 'id', 'name', "User");
		$model 		= D('SystemConfigDetail');
		$nodeModel = M('node');
		//通过modelname查找到对应的模块中文名
		$modelNameChinese = $nodeModel->where("name='".$modelName."'")->getField("title");
		//通过modelname查找到对应的单据名称
		$orderInfoname = D($modelName)->where("id='".$tableid."'")->field("name")->find();
		$ordername=$orderInfoname['name'];
		//获取制单人
		$createid=D($modelName)->where("id='".$tableid."'")->getfield("createid");
		$createname=getFieldBy($createid, 'id', 'name', "user");
		//通过modelname查找到对应的单据编号
		$orderInfo = D($modelName)->where("id='".$tableid."'")->field("orderno")->find();
		if($orderInfo){
			$orderno=$orderInfo['orderno'];
		}else{
			$orderInfo= D($modelName)->where("id='".$tableid."'")->field("code")->find();
			$orderno=$orderInfo['code'];
		}
		//知会还是执行
		if($noticeType=="1"){
			$noticeTypeName="工作知会：";
			$noticeUrl='<a class="edit" style="text-decoration:underline" title="工作中心" target="navTab" rel="MisWorkExecuting" href="__APP__/MisWorkExecuting/index/jump/6/md/'.$modelName.'/tableid/'.$tableid.'">' . $orderno . '</a>';
		}else{
			$worksetmodel=M("mis_work_executing_set");
			$typeid=$worksetmodel->where("name='".$modelName."'")->getField("typeid");
			$Nameid=$worksetmodel->where("name='".$modelName."'")->getField("id");
			$noticeTypeName="工作中心：";
			$noticeUrl='<a class="edit" style="text-decoration:underline" title="工作中心" target="navTab" rel="MisWorkExecuting" href="__APP__/MisWorkExecuting/index/jump/4/md/'.$modelName.'/typeid/'.$typeid.'/dotype/0/rel/'.$modelName.'_'.$Nameid.'/tableid/'.$tableid.'">' . $orderno . '</a>';
		}
		$modelNameChineseUrl='<a class="" style="text-decoration:underline" title="'.$modelNameChinese.'" target="navTab" rel="'.$modelName.'" href="__APP__/'.$modelName.'/index">'.$modelNameChinese.'</a>';
		//发送的系统日志的标题
		$messageTitle =$noticeTypeName.$modelNameChinese.' 单据号为  '.$orderno.' 的单据 '.$ordername.'已经派车成功，请关注！';
		//message信息拼装
		$messageContent="";
		$messageContent.='
			<p></p>
			<span></span>
			<div style="width:98%;">
				<p class="font_darkblue">您好！</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;'. $modelNameChinese.' 单据号为  '.$orderno.' 的单据 '.$ordername.' 已经派车成功! </p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;单据的详细情况：</p>
				<ul>
					<li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>单据系统：</strong>' . $modelNameChineseUrl . '
					</li>
					<li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>单据编号：</strong>'.$noticeUrl.'
				</ul>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;如果您有任何问题，请联系派车人：' . $sendcarname . '。</p>
			</div>';
		//从动态配置获取到数据信息
		$result=$model->GetDynamicconfData($modelName,$tableid);
		//print_R($result);
		foreach($result as $key => $val){
			//初始化一个字符串容器
			$tmpstring="";
			foreach($val as $subkey => $subval){
				$tmpstring=$subkey.'：</b>'.$subval;
			}
			$messageContent.='<b class="t-gre1">'.$tmpstring.'<br/>';
		}
		//开始附件头部拼接
		$messageContent.='<div class="xyMessageAttach"><div style="padding:6px 10px 6px 8px;"><div class="attach left"></div><strong>附件：</strong></div><div class="xyMessageAttachItems">';
		//声明相关附件表
		$modelMAR = M('MisAttachedRecord');
		//获取附件信息
		$num=1;
		$map = array();
		$map["status"]  =1;
		$map["tableid"] =$tableid;
		$map["tablename"] =$modelName;
		$attarry=$modelMAR->field("id,upname,attached")->where($map)->select();
		if($attarry){
			foreach($attarry as $attkey => $attval){
				//下载路径
				$downloadName=__URL__."/misFileManageDownload/path/".base64_encode($attval['attached'])."/rename/".$attval['upname'];
				//归档路径
				$stockName=__APP__."/MisMessageInbox/lookupDocumentCollateAtta/t/0/id/".$attval['id'];
				//附件名称拼接
				$messageContent.='<div class="xyMessageAttachItem"><span class="tml-label tml-bg-orange tml-mr5">附件'.$num.'</span>';
				$messageContent.='</span><a class="attlink" rel="'.$attval['id'].'" target="_blank" href="'.$downloadName.'"><span>'.$attval['upname'].'</span>';
				$messageContent.='<a class="tml-btn tml-btn-small tml-btn-green" href="'.$stockName.'" title="文件归档" target="dialog"><span class="tml-icon tml-icon-file"></span><span class="tml-icon-text">归档</span></a></div>';
				$num++;
			}
		}
		//开始附件尾部拼接
		$messageContent.='</div></div>';
		//系统推送消息
		if(!is_array($sendidArr)){
			$sendidArr=array($sendidArr);
		}
		$messageexecuting=array();
		if($noticeType){
			$messageexecuting=array('tableid'=>$tableid,'tablename'=>$modelName,"noticeType"=>$noticeType);
		}
		$this->pushMessage($sendidArr, $messageTitle, $messageContent,'','','',$messageexecuting);
	} 
	/**
	 * @Title: executoridEdit 
	 * @Description: todo(转派执行人)   
	 * @author libo 
	 * @date 2014-5-28 下午5:21:40 
	 * @throws
	 */
	public function lookupexecutoridEdit(){
		//查询这条数据的执行人
		$workModel=D("MisWorkExecuting");
		$turnexecutoridwork=$workModel->where("tableid=".$_REQUEST['id']." and tablename='".$_REQUEST['md']."'")->getfield("turnexecutorid");
		$turnexecutoridworkArr=explode(",", $turnexecutoridwork);
		if($_REQUEST['stepVal']){//执行人修改
			//接收的执行人
			$data=array("turnexecutorid"=>implode(",", $_REQUEST['turnexecutorid']));
			$re=$workModel->where("tableid=".$_REQUEST['id']." and tablename='".$_REQUEST['md']."'")->save($data);
			if($re){
				$this->success ( L('_SUCCESS_'));
			}else{
				$this->error ( L('_ERROR_') );
			}
		}else{//模板显示
			$this->assign("rel",$_REQUEST['rel']);
			$this->assign("md",$_REQUEST['md']);
			$this->assign("tableid",$_REQUEST['id']);
			$this->assign("turnexecutoridArr",$turnexecutoridworkArr);
			$this->display();
		}
	}

	/**
	 * @Title: lookupGeneralSelf 
	 * @Description: todo(基于 lookupGenera方法修改,在本模块定制使用:车辆派发,优化操作)   
	 * @author yuansl 
	 * @date 2014-7-28 上午9:43:47 
	 * @throws
	 */
	public function lookupGeneralSelf(){
		//获取查找带回的字段
		$this->assign("field",$_REQUEST['field']);
		$_POST['dealLookupList'] = 1;//强制查找带回重构数据列表
		$name = $_POST['model'];
		//获取部门类型 ————快捷新增客户
		$deptid=$_REQUEST['deptid'];
		$this->assign("deptid",$deptid);
		if(strpos($name, '_')){//将表转换为model
			$nameArr = explode('_', $name);
			$names = "";
			foreach ($nameArr as $k => $v) {
				$names .= ucfirst($v);
			}
			if($names){
				$name = $names;
			}
		}
		if(substr($name, -4)=="View"){
			$qx_name = $name;
			$name = substr($name,0, -4);
		}
		$this->assign("model",$name);
		// 单据号是否可写
		$table = D($name)->getTableName();
		$scnmodel = D('SystemConfigNumber');
		$writable= $scnmodel->GetWritable($table);
		$this->assign("writable",$writable);
	
		$ConfigListModel = D('SystemConfigList');
		$lookupGeneralList = $ConfigListModel->GetValue('lookupGeneralInclude');// 快速新增配置列表
		$include = $lookupGeneralList[$name];//获取配置信息
		$layoutH = 96;//默认高度
		if($include){
			$layoutH = $include['layoutH'];//获取高度
			$this->assign("tplName",'LookupGeneral:'.$include['tpl']);//设置默认模版
			$this->assign("isauto",$include['isauto']);//设置编号自动生成
		}
		$this->assign("layoutH",$layoutH);//设置高度
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($name);
		if ($detailList) {
			$inArray = array('action','remark','status');
			if ($_REQUEST['filterfield']) {
				$filterfield = explode(',', $_REQUEST['filterfield']);
				$inArray = array_merge($inArray,$filterfield);
			}
			foreach ($detailList as $k => $v) {
				if(in_array($v['name'], $inArray)){
					unset($detailList[$k]);
				}
			}
			$this->assign ( 'detailList', $detailList );
		}
		$action = A("MisRequestCarManage");
		$map = $this->_search($name);
		$conditions = $_POST['conditions'];// 检索条件
		if($conditions){
			$this->assign("conditions",$conditions);
			$cArr = explode(';', $conditions);//分号分隔多个参数
			foreach ($cArr as $k => $v) {
				$wArr = explode(',', $v);//逗号分隔字段、参数、修饰符
				if ($wArr[0] == "_string") { // 判断是否传的为字符串条件
					$map['_string'] = $wArr[1];
				} else {
					if ($wArr[2]) {//存在修饰符的以修饰符形式进行检索
						$map[$wArr[0]] = array($wArr[2],$wArr[1]);
					} else {//普通检索
						$map[$wArr[0]] = $wArr[1];
					}
				}
			}
		}
		$map['status'] = 1;
		$filterfield = "_lookupGeneralfilter";
		if($_REQUEST['filtermethod']) $filterfield = $_REQUEST['filtermethod'];
		if (method_exists($this,$filterfield)) {
			call_user_func(array(&$this,$filterfield),&$map);
		}
		// 		dump($this);
		// 		dump($name);
		$action->_list ( $name, $map );
		$this->display();
	}
	/**
	 * @Title: _after_list
	 * @Description: todo(处理带回车辆时候标红的一行显示效果)
	 * @param unknown_type $volist
	 * @author yuansl
	 * @date 2014-7-17 下午4:15:32
	 * @throws
	 */
	public function _after_list(&$volist){
		//获取到车辆id 查询  如果这辆车在当前时间已经再使用则新组合一个字段进入$volist isBusy
		$newvolist = array();
		foreach($volist as $val){
			$temp = $this->is_busy($val['id']);
			$val['isBusy'] = $temp['isBusy'];
			$val['orderid'] = $temp['orderid'];
			array_push($newvolist,$val);
			// 			dump($val['isBusy']);
		}
		$this->assign('list1',$newvolist);
		// 		dump($newvolist);
	}
	/**
	 * @Title: is_busy
	 * @Description: todo(判断在当前时间 改车辆是否正在被使用,如果在使用返回1,未使用返回0)
	 * @param string $carID
	 * @author yuansl
	 * @date 2014-7-17 下午4:38:04
	 * @throws
	 */
	private function is_busy($carID){
		if(!$carID){
			return false;
		}
		$time = time();
		$modelcarModel = D("MisSystemCar");
		$modelcarRequestModel = D("MisRequestCar");
		$misCarReturnModel = D("MisCarReturn");
		//$re = $modelcarModel->where("status = 1 and id = ".$carID)->find();
// 		if(!$re){
// 			$this->error("请确认改车辆是否存在!");
// 			exit;
// 		}
		$mapx['departureTime'] = array('elt',$time);//开始时间小于当前时间
		$mapx['expectRestitutionTime'] = array('egt',$time);//结束时间大于当前时间
		$mapx['status'] = array('gt',0);//
		$mapx['auditState'] = array('eq',3);//审核通过的
		// 并且是已经派过车的  且车没有正在被使用
// 		$mapx['returnTag'] = array('neq',1);
		$mapx['carID'] = array('eq',$carID);
		$rex = $modelcarRequestModel->where($mapx)->field("id,carID")->find();
		//是否还车
		//取申请单里里面该车辆的最后一条数据,根据这个申请单id,在还车列表里面查询,如有一条记录,则证明该车已经还了,则不变红.
		$is_use = $misCarReturnModel->where("status = 1 and roid = ".$rex['id'])->find();
		if($rex && !$is_use){
			return array('isBusy'=>1,"orderid"=>$rex['id']);
		}else{
			return array('isBusy'=>0);
		}
	}
}
