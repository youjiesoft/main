<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(工作计划查阅) 
 * @author libo 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-7-15 下午4:33:51 
 * @version V1.0
 */
class MisWorkPlanContentAction extends CommonAction{
	public function _before_index(){
		$this->getlefttree();
	}
	public function getlefttree(){
		$returnarr=array(
			array(
				'id'=>0,
				'pId'=>0,
				'name'=>'评论状态',
				'open'=>true,
				'title'=>'评论状态',
			),
			array(
				'id'=>1,
				'pId'=>0,
				'name'=>'未评',
				'open'=>true,
				'rel'=>'MisWorkPlanContentBox',
				'url'=>'__URL__/index/jump/1/iscomment/0',
				'target'=>'ajax',
				'title'=>'未评'
			),
			array(
				'id'=>2,
				'pId'=>0,
				'name'=>'已评',
				'open'=>true,
				'rel'=>'MisWorkPlanContentBox',
				'url'=>'__URL__/index/jump/1/iscomment/1',
				'target'=>'ajax',
				'title'=>'已评'
			),
		);
		$treearr=json_encode($returnarr);
		$this->assign("treearr",$treearr);
	}
	public function _filter(&$map){
		if( !isset($_SESSION['a']) ){
			$map['status']=1;
		}
		$userid=$_SESSION[C('USER_AUTH_KEY')];
		$map['_string'] .="( FIND_IN_SET('$userid', lookpeople) )";
		$map['stepType']=1;
		if($_REQUEST['iscomment']==1){
			$map['_string'] .=" and (FIND_IN_SET('$userid', commentpeople))";
		}else if($_REQUEST['iscomment']==0){
			$planModel=D("MisWorkPlan");
			$plan['_string']="( FIND_IN_SET('$userid', commentpeople) )";
			$list=$planModel->field("id")->where($plan)->select();
			if($list){
				foreach ($list as $v){
					$comid.=",".$v['id'];
				}
				$comid=substr($comid, 1);
				$map['_string'] .=" and  id not in (".$comid.")";
			}
		}
		if($_REQUEST['datetime']){
			$map['_string'] .= " and ( plantime >= ".strtotime($_REQUEST['datetime'])." and plantime <".strtotime($_REQUEST['datetime'])."+24*3600 )" ;
		}
		$this->assign("iscomment",$_REQUEST['iscomment']);
	}
	public function index() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search ("MisWorkPlanContent");
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name = "MisWorkPlan";
		if (! empty ( $name )) {
			$qx_name=$name;
			if(substr($name, -4)=="View"){
				$qx_name = substr($name,0, -4);
			}
			//验证浏览及权限
			$this->_list ( $name, $map );
		}
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail("MisWorkPlanContent");
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		//扩展工具栏操作
		$toolbarextension = $scdmodel->getDetail("MisWorkPlanContent",true,'toolbar');
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
		$this->display ();
		return;
	}
	/**
	 * @Title: _after_list 
	 * @Description: todo(列表处理内容显示) 
	 * @param unknown_type $voList  
	 * @author libo 
	 * @date 2014-7-16 上午11:00:32 
	 * @throws
	 */
	function _after_list(&$voList){
		foreach ($voList as $k => $v) {
			$str = htmlspecialchars_decode($v['content'], ENT_QUOTES);//转码
			$str = trim(strip_tags(str_replace("&nbsp;", ' ', $str)));//过滤html
			$voList[$k]['content'] = missubstr($str,35,true);
			$userid=$_SESSION[C('USER_AUTH_KEY')];
			$voList[$k]['readpeople']=in_array($userid, explode(",", $v['readpeople'])) ? "<span style='color:green;'>已阅</span>" : "<span style='color:red;'>未阅</span>";
			$voList[$k]['commentpeople']=in_array($userid, explode(",", $v['commentpeople'])) ? "<span style='color:green;'>已评</span>" : "<span style='color:red;'>未评</span>";
		}
// 		dump($voList);
	}
	/**
	 * (non-PHPdoc)
	 * @see CommonAction::edit()点评
	 */
	function edit(){
		$planModel = D("MisWorkPlan");
		$list=$planModel->where("id=".$_REQUEST['id'])->find();
		$this->assign("vo",$list);
		$userid=$_SESSION[C('USER_AUTH_KEY')];
		$this->assign("userid",$userid);
		$this->assign("time",time());
		//保存 改计划单 已查看人员id
		$readpeople=$list['readpeople'];
		if(!in_array($userid, explode(",", $readpeople))){
			if($readpeople){
				$readpeople .= ",".$userid;
			}else{
				$readpeople .= $userid;
			}
			$re=$planModel->where("id=".$_REQUEST['id'])->setField("readpeople",$readpeople);
			if($re===false){
				$this->error("查阅人添加失败");
			}
		}
		$planContentModel=M("mis_work_plan_content");
		//查询点评内容
		$contentlist=$planContentModel->where("planid=".$_REQUEST['id']." and isrecord=0 and status=1")->select();
		$this->assign("contentlist",$contentlist);
		//获取附件信息
		$this->getAttachedRecordList($_REQUEST['id'],true,true,'MisWorkPlan');
		$this->display();
	}
	/**
	 * (non-PHPdoc)
	 * @see CommonAction::view()查看
	 */
	public function view(){
		$planModel=D("MisWorkPlan");
		$list=$planModel->where("id=".$_REQUEST['id'])->find();
		$this->assign("vo",$list);
		$planContentModel=M("mis_work_plan_content");
		//查询点评内容
		$contentlist=$planContentModel->where("planid=".$_REQUEST['id']." and isrecord=0 and status=1")->select();
		$this->assign("contentlist",$contentlist);
		//查询自己记录内容
		$recordlist=$planContentModel->where("planid=".$_REQUEST['id']." and isrecord=1 and status=1")->select();
		$this->assign("recordlist",$recordlist);
		$userid=$_SESSION[C('USER_AUTH_KEY')];
		$this->assign("userid",$userid);
		//获取附件信息
		$this->getAttachedRecordList($_REQUEST['id'],true,true,'MisWorkPlan');
		$this->display();
	}
	/**
	 * @Title: _before_update 
	 * @Description: todo(这里用一句话描述这个方法的作用)   
	 * @author libo 
	 * @date 2014-7-16 下午2:29:21 
	 * @throws
	 */
	public function _before_update(){
		//点评该计划人id 插入计划单
		$planModel = D("MisWorkPlan");
		$userid=$_SESSION[C('USER_AUTH_KEY')];
		$commentpeople=$planModel->where("id=".$_POST['planid'])->getField("commentpeople");
		if(!in_array($userid, explode(",", $commentpeople))){
			if($commentpeople){
				$commentpeople .= ",".$userid;
			}else{
				$commentpeople .= $userid;
			}
			$re=$planModel->where("id=".$_POST['planid'])->setField("commentpeople",$commentpeople);
			if($re===false){
				$this->error("插入点评人失败");
			}
		}
	}
	function update() {
		//B('FilterString');
		$name=$this->getActionName();
		$model = D ( $name );
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$list=$model->add ();
		if (false !== $list) {
			$module2=A($name);
			if (method_exists($module2,"_after_update")) {
				call_user_func(array(&$module2,"_after_update"),$list);
			}
			$this->success ( L('_SUCCESS_'));
		} else {
			$this->error ( L('_ERROR_') );
		}
	}
	
	public function lookupGetdate(){
		$name = 'MisWorkPlanContent';
		$model = M('mis_work_plan_content');
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
	function _after_update($list) {
		//查询工作报告 创建人
		$WorkPlanModel=D("MisWorkPlan");
		$list=$WorkPlanModel->where("id=".$_REQUEST['planid'])->find();
		//邮件发送人
		$sendidArr=array('1'=>$list['createid']);
		
		//点评人
		$commentusername=getFieldBy($_POST['userid'], "id", "name", "user");
		//发送的系统日志的标题
		$messageTitle ="工作计划:".$list['title'].'已经被【'.$commentusername.'】评论，请关注！';
		//单据系统
		$modelNameChineseUrl='<a class="" style="text-decoration:underline" title="工作计划" target="navTab" rel="MisWorkPlan" href="__APP__/MisWorkPlan/index">工作计划</a>';
		$noticeUrl='<a class="edit" style="text-decoration:underline" title="工作计划_查看" target="navTab" rel="MisWorkplanedit" href="__APP__/MisWorkPlan/view/id/'.$_REQUEST['planid'].'">' . $list['title'] . '</a>';
		//message信息拼装
		$messageContent="";
		$messageContent.='
			<p></p>
			<span></span>
			<div style="width:98%;">
				<p class="font_darkblue">您好！</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;工作计划：'.$list['title'].'已评论 </p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;评论内容：</p>
				<ul>
					<li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>单据系统：</strong>' . $modelNameChineseUrl . '
					</li>
					<li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>工作计划：</strong>'. $noticeUrl.'
				</ul>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;如果您有任何问题，请联系评论人：' . $commentusername . '。</p>
			</div>';
		$this->pushMessage($sendidArr, $messageTitle, $messageContent);
	}
}

?>