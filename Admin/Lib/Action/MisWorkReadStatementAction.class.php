<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(工作报告控制器) 
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-7-2 下午5:02:46 
 * @version V1.0
 */
class MisWorkReadStatementAction extends CommonAction {
	/**
	 * @Title: _filter 
	 * @Description: todo(重写CommonAction的index方法，展示列表) 
	 * @return string  
	 * @author 杨希
	 * @date 2013-5-31 下午3:59:44 
	 * @throws
	 */
	public function _filter(&$map){
		if ($_SESSION["a"] != 1){
			//只有阅读人才可见
			$map['mis_work_read_statement.userid']=$_SESSION[C('USER_AUTH_KEY')];
			$map['mis_work_read_statement.status']=array("gt",-1);
		}
		if(!$_REQUEST['all'] && !$_REQUEST['iscomment']){
			$map['iscomment'] = 0;
		}
		if($_REQUEST['all']){
			$this->assign('all',$_REQUEST['all']);
		}
		//评论状态
		if(isset($_REQUEST['iscomment']) && $_REQUEST['iscomment'] !=''){
			$map['iscomment'] = $_REQUEST['iscomment'];
			$this->assign('iscomment',$_REQUEST['iscomment']);
		}
		//根据单个时间查询数据
		$datetime = $_REQUEST['datetime'];
		$this->assign('datetime',$datetime);
		$datetimes=strtotime($datetime);
		$endtime = $datetimes+86400;
		if($datetime){
			$map['mis_work_read_statement.createtime'] =array('exp','>='.$datetimes.' and mis_work_read_statement.createtime <'.$endtime);
		}
		//根据报告人查阅
		if($createid){
			$map['mis_work_read_statement.createid'] = $createid;
			$this->assign('createid',$createid);
		}
	}
	/**
	 * @Title: get_listUser 
	 * @Description: todo(获取管辖人员)   
	 * @author liminggang 
	 * @date 2013-7-26 下午5:51:23 
	 * @throws
	 */
	private function get_listUser(){
		//获取当前登录人
// 		$userid=$_SESSION[C('USER_AUTH_KEY')];
// 		$username=getFieldBy($userid,'id','name','User');
// 		$map['status'] = 1;
// 		$leadname =$userid;
// 		$staffname = $username;
// 		$MisWorkStatementSetDao=M('mis_work_statement_set');
// 		$list=$MisWorkStatementSetDao->where($map)->field('id,leadid,staffid,staffname')->select();
// 		foreach($list as $key=>$val){
// 			if(in_array($userid,explode(",",$val['leadid']))){
// 				$leadname =$leadname.",".$val['staffid'];
// 				$staffname =$staffname.",".$val['staffname'];
// 			}
// 		}
// 		$useridlist=array_unique(array_filter(explode(",",$leadname)));
// 		$usernamelist=array_unique(array_filter(explode(",",$staffname)));
// 		$this->assign('useridlist',$useridlist);
// 		$this->assign('usernamelist',$usernamelist);
		$useridlist=array();
		$usernamelist=array();
		$MisWorkStatement=D("MisWorkStatement");
		if ($_SESSION["a"] != 1){
			$map['_string']="find_in_set('".$_SESSION[C('USER_AUTH_KEY')]."',lookpeople)";
		}
		$map['status']=1;
		$wmslist=$MisWorkStatement->field("createid")->where($map)->group("createid")->select();
		foreach ($wmslist as $k=>$v){
			$useridlist[]=$v['createid'];
			$usernamelist[]=getFieldBy($v['createid'],'id','name','User');
		}
		$this->assign('useridlist',$useridlist);
		$this->assign('usernamelist',$usernamelist);
	}
	
	
	function index(){
		//获得管辖人员
		$this->get_listUser();
		
		$createid=$_REQUEST['createid'];
		$returnarr=array(
				array(
					'id'=>0,
					'pId'=>0,
					'name'=>'评论状态',
					'open'=>true,
					'rel'=>'mistoreadstatementBox',
					'url'=>'__URL__/index/jump/1/all/1/createid/'.$createid,
					'target'=>'ajax',
					'title'=>'评论状态',
				),
				array(
						'id'=>1,
						'pId'=>0,
						'name'=>'未评',
						'open'=>true,
						'rel'=>'mistoreadstatementBox',
						'url'=>'__URL__/index/jump/1/iscomment/0/createid/'.$createid,
						'target'=>'ajax',
						'title'=>'未评'
				),
				array(
						'id'=>2,
						'pId'=>0,
						'name'=>'已评',
						'open'=>true,
						'rel'=>'mistoreadstatementBox',
						'url'=>'__URL__/index/jump/1/iscomment/1/createid/'.$createid,
						'target'=>'ajax',
						'title'=>'已评'
				),
				
			);
		$treearr=json_encode($returnarr);
		$this->assign("treearr",$treearr);
		
		$map = $this->_search();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		//动态配置部分。
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($name);
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		//searchby搜索扩展
		$searchby = $scdmodel->getDetail($name,true,'searchby');
		if ($searchby && $detailList) {
			$searchbylist=array();
			foreach( $detailList as $k=>$v ){
				if(isset($searchby[$v['name']])){
					$arr['id']= $searchby[$v['name']]['field'];
					$arr['val']= $v['showname'];
					array_push($searchbylist,$arr);
				}
			}
			$this->assign("searchbylist",$searchbylist);
		}
		//扩展工具栏操作
		$toolbarextension = $scdmodel->getDetail($name,true,'toolbar');
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		//查询数据部分
		if (! empty ( $name )) {
			$this->_list ( 'MisWorkReadStatementView', $map ,"createtime");
		}
		if( intval($_POST['dwzloadhtml']) ){$this->display("dwzloadindex");exit;}
		if ($_REQUEST['jump']) {
			$this->display('unitlist');
		} else {
			$this->display();
		}
	}
	/**
	 * @Title: _after_list
	 * @Description: todo(执行了CommonAction类里面的_list方法的后置函数)
	 * @param unknown_type $voList
	 * @author xiafengqin
	 * @date 2013-5-31 下午4:25:44
	 * @throws
	 */
	function _after_list(&$voList){
		foreach ($voList as $k => $v) {
			$str = htmlspecialchars_decode($v['content'], ENT_QUOTES);//转码
			$str = trim(strip_tags(str_replace("&nbsp;", ' ', $str)));//过滤html
			$voList[$k]['content'] = missubstr($str,35,true);
			$voList[$k]['md'] = $this->getActionName();
		}
	}
	public function _before_edit(){
		$id=$_GET['id'];
		$MisToreadStatementModel=D($this->getActionName());
		$MisToreadStatementModel->where('id = '.$id)->setField('readstatus',0);
		
		//如果打开了报告查看。就修改工作报告状态为已查看
		$workid=getFieldBy($id,'id','workid','mis_work_read_statement');
		$MisWorkStatementModel=D("MisWorkStatement");
		$map['readstatus'] = 1;
		$map['id'] = $workid;
		$result=$MisWorkStatementModel->save($map);
		if(!$result){
			$this->error(L("__ERROR__"));
		}else{
			$this->transaction_model->commit();
		}
		//获取附件信息		
		$this->getAttachedRecordList($workid,true,true,'MisWorkStatement');
		
	}
	public function edit(){
		//获取ID
		$id=$_GET['id'];
		//查询数据
		$MisWorkReadStatementViewDao=D("MisWorkReadStatementView");
		$map['mis_work_read_statement.id'] = $id;
		if ($_SESSION["a"] != 1) $map['mis_work_read_statement.status'] = 1;
		$list=$MisWorkReadStatementViewDao->where($map)->find();
		if(empty($list)){
			$this->display ("Public:404");
			exit;
		}
		$this->assign('userid',$_SESSION[C('USER_AUTH_KEY')]);
		$this->assign('time',time());
		$this->assign('vo',$list);
		$this->display();
	}
	public function lookupGetdate(){
		$name = 'MisWorkReadStatement';
		$model = M('mis_work_read_statement');
		$map = array();
		$map['status'] = 1;
		if ($_SESSION["a"] != 1){
			$map['userid'] = $_SESSION[C('USER_AUTH_KEY')];
		}
		if($_REQUEST['createid']){
			$map['createid'] = $_REQUEST['createid'];
		}
		$starttime = strtotime($_REQUEST['starttime']);
		$endtime = strtotime($_REQUEST['endtime']);
		$map['createtime'] = array("exp",">=".$starttime." and createtime <".$endtime);
		$list = $model->where($map)->field('id,createtime')->select();
		$arr = array();
		$aaa = array();
		foreach ($list as $key => $value) {
			$daytime = date("Y-m-d",$value['createtime']);
			$daytime = strtotime($daytime);
			if(!in_array($daytime,$aaa)){
				$start = transTime($value['createtime'],'Y-m-d H:i:s');
				$end = transTime($value['createtime']+1,'Y-m-d H:i:s');
				$arr[] = array(
						'id' => $value['id'],
						'title' =>"",
						'start' => $start,
						'end' => $end,
						'tdclassName'=>'pred',
				);
				array_push($aaa,$daytime);
			}
		}
		echo json_encode($arr);
	}
	/**
	 * @Title: _after_update 
	 * @Description: todo(评论后邮件提示报告人)   
	 * @author libo 
	 * @date 2014-7-1 下午3:48:28 
	 * @throws
	 */
	function _after_update() {
		//查询工作报告 创建人
		$ReadStatementModel=D("MisWorkReadStatement");
		$workid=$ReadStatementModel->where("id=".$_REQUEST['id'])->getfield("workid");
		$WorkStatementModel=D("MisWorkStatement");
		$worklist=$WorkStatementModel->where("id=".$workid)->find();
		//邮件发送人
		$sendidArr=array('1'=>$worklist['createid']);
		//点评人
		$commentusername=getFieldBy($_POST['commentuserid'], "id", "name", "user");
		//发送的系统日志的标题
		$messageTitle ="工作报告:".$worklist['title'].'已经被【'.$commentusername.'】评论，请关注！';
		//单据系统
		$modelNameChineseUrl='<a class="" style="text-decoration:underline" title="工作报告" target="navTab" rel="MisWorkStatement" href="__APP__/MisWorkStatement/index">工作报告</a>';
		$noticeUrl='<a class="edit" style="text-decoration:underline" title="工作报告_查看" target="navTab" rel="MisWorkStatementedit" href="__APP__/MisWorkStatement/view/id/'.$workid.'">' . $worklist['title'] . '</a>';
		//message信息拼装
		$messageContent="";		
		$messageContent.='
			<p></p>
			<span></span>
			<div style="width:98%;">
				<p class="font_darkblue">您好！</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;工作报告：'.$worklist['title'].'已评论 </p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;评论内容：</p>
				<ul>
					<li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>单据系统：</strong>' . $modelNameChineseUrl . '
					</li>
					<li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>工作标题：</strong>'. $noticeUrl.'
				</ul>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;如果您有任何问题，请联系评论人：' . $commentusername . '。</p>
			</div>';
		$this->pushMessage($sendidArr, $messageTitle, $messageContent);
	}
}
?>