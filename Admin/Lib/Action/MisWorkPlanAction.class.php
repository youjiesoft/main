<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(工作计划) 
 * @author libo 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-7-14 下午4:49:32 
 * @version V1.0
 */
class MisWorkPlanAction extends CommonAction{
	/**
	 * @Title: _before_index 
	 * @Description: todo(加载树)   
	 * @author libo 
	 * @date 2014-7-16 上午9:36:43 
	 * @throws
	 */
	public function _before_index(){
		$this->getLeftZtree();
	}
	/**
	 * @Title: _after_list 
	 * @Description: todo(处理内容显示) 
	 * @param unknown_type $voList  
	 * @author libo 
	 * @date 2014-7-16 上午9:36:52 
	 * @throws
	 */
	function _after_list(&$voList){
		foreach ($voList as $k => $v) {
			$str = htmlspecialchars_decode($v['content'], ENT_QUOTES);//转码
			$str = trim(strip_tags(str_replace("&nbsp;", ' ', $str)));//过滤html
			$voList[$k]['content'] = missubstr($str,35,true);
			$voList[$k]['readpeople']=$voList[$k]['readpeople'] ? "<span style='color:green;'>已阅</span>" : "<span style='color:red;'>未阅</span>";
			$voList[$k]['commentpeople']=$voList[$k]['commentpeople'] ? "<span style='color:green;'>已评</span>" : "<span style='color:red;'>未评</span>";
		}
	}
	public function index() {
		//列表过滤器，生成查询Map对象
		$name=$this->getActionName();
		$map = $this->_search ($name);
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		if($_REQUEST['typeid']){
			$map['typeid']=$_REQUEST['typeid'];
			$this->assign("typeid",$_REQUEST['typeid']);
		}
		if($_REQUEST['datetime']){
			$map['_string'] = "( plantime >= ".strtotime($_REQUEST['datetime'])." and plantime <".strtotime($_REQUEST['datetime'])."+24*3600 )" ;
		}
		if (! empty ( $name )) {
			$qx_name=$name;
			if(substr($name, -4)=="View"){
				$qx_name = substr($name,0, -4);
			}
			//验证浏览及权限
			$map["createid"]=$_SESSION[C('USER_AUTH_KEY')];
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
			$this->display('indexview');
		} else {
			$this->display();
		}
	}
	/**
	 * @Title: getLeftZtree 
	 * @Description: todo(左侧树)   
	 * @author libo 
	 * @date 2014-7-15 上午11:18:47 
	 * @throws
	 */
	private function getLeftZtree(){
		//构造左侧类型树
		$selectList=require DConfig_PATH."/System/selectlist.inc.php";
		$tree=array();
		$tree[]=array(
				'id'=>-1,
				'pId'=>0,
				'name'=>'个人计划',
				'title'=>'个人计划',
				'open'=>true,
				'rel'=>'misMisWorkPlanBox',
				'url'=>'__URL__/index/jump/1',
				'target'=>'ajax'
		);
		foreach ($selectList['workplan']['workplan'] as $k=>$v){
			$tree[]=array(
					'id'=>$k,
					'pId'=>-1,
					'name'=>$v,
					'title'=>$v,
					'open'=>true,
					'rel'=>'misMisWorkPlanBox',
					'url'=>'__URL__/index/jump/1/typeid/'.$k,
					'target'=>'ajax'
			);
		}
		$this->assign("treearr",json_encode($tree));
	}
	/**
	 * @Title: _before_add 
	 * @Description: todo(新增前置)   
	 * @author libo 
	 * @date 2014-7-15 上午11:18:33 
	 * @throws
	 */
	public function _before_add(){
		//订单号可写
		$scnmodel = D('SystemConfigNumber');
		$code = $scnmodel->GetRulesNO('mis_work_plan');
		$this->assign("code", $code);
		$userid=$_SESSION[C('USER_AUTH_KEY')];
		$this->assign('userid',$userid);
		$this->assign('time',time());
		//默认报告标题
		$userModel = D("User");
		$username = $userModel->where('id = '. $userid)->getField('name');
		$this->assign('username', $username);
		if($_REQUEST['typeid']){
			$this->assign("typeid",$_REQUEST['typeid']);
		}
	}
	/**
	 * @Title: _before_edit 
	 * @Description: todo(编辑前置)   
	 * @author libo 
	 * @date 2014-7-15 上午11:19:18 
	 * @throws
	 */
	public function _before_edit(){
		//获取附件信息
		$this->getAttachedRecordList($_REQUEST['id']);
		//获取查阅人
		$workplanModel=D("MisWorkPlan");
		$lookpeople=$workplanModel->where("id=".$_REQUEST['id'])->getField("lookpeople");
		$lookpeople=explode(",", $lookpeople);
		$this->assign("lookpeople",$lookpeople);
		//默认报告标题
		$userid=$_SESSION[C('USER_AUTH_KEY')];
		$this->assign('userid',$userid);
		$userModel = D("User");
		$username = $userModel->where('id = '. $userid)->getField('name');
		$this->assign('username', $username);
	}
	/**
	 * @Title: _before_insert 
	 * @Description: todo(新增前置转化数据)   
	 * @author libo 
	 * @date 2014-7-15 上午11:15:09 
	 * @throws
	 */
	public function _before_insert(){
		$_POST['lookpeople']=implode(",", $_POST['lookpeople']);
		$_POST['lookpeoplename']=implode(",", $_POST['lookpeoplename']);
	}
	/**
	 * @Title: _after_insert 
	 * @Description: todo(附件添加) 
	 * @param unknown_type $list  
	 * @author libo 
	 * @date 2014-7-15 上午11:17:43 
	 * @throws
	 */
	public function _after_insert($list){
		$this->swf_upload($list,99);
	}
	/**
	 * @Title: _before_update 
	 * @Description: todo(修改前置 数据转化)   
	 * @author libo 
	 * @date 2014-7-15 上午11:18:07 
	 * @throws
	 */
	public function _before_update(){
		$_POST['lookpeople']=implode(",", $_POST['lookpeople']);
		$_POST['lookpeoplename']=implode(",", $_POST['lookpeoplename']);
		//自己添加计划进行记录
		if($_POST['isrecord']){
			$planContentModel=M("mis_work_plan_content");
			$data=array();
			$data['planid']=$_POST['id'];
			$data['userid']=$_SESSION[C('USER_AUTH_KEY')];
			$data['content']=$_POST['record'];
			$data['commenttime']=time();
			$data['isrecord']=1;
			$data['createid']=$_SESSION[C('USER_AUTH_KEY')];
			$data['createtime']=time();
			$re=$planContentModel->add($data);
			if($re===false){
				$this->error("操作失败");
			}
		}
	}
	/**
	 * @Title: _after_update 
	 * @Description: todo(附件添加)   
	 * @author libo 
	 * @date 2014-7-15 上午11:17:59 
	 * @throws
	 */
	public function _after_update(){
		if ($_POST['id']) {
			$this->swf_upload($_POST['id'],99);
		}
	}
	public function _before_view(){
		$planContentModel=M("mis_work_plan_content");
		//查询点评内容
		$contentlist=$planContentModel->where("planid=".$_REQUEST['id']." and isrecord=0 and status=1")->select();
		$this->assign("contentlist",$contentlist);
		//查询自己记录内容
		$recordlist=$planContentModel->where("planid=".$_REQUEST['id']." and isrecord=1 and status=1")->select();
		$this->assign("recordlist",$recordlist);
		//获取附件信息
		$this->getAttachedRecordList($_REQUEST['id']);
		
	}
	/**
	 * @Title: lookupGetdate 
	 * @Description: todo(时间获取)   
	 * @author libo 
	 * @date 2014-7-15 上午11:18:22 
	 * @throws
	 */
	public function lookupGetdate(){
		$name = 'MisWorkPlan';
		$model = M('mis_work_plan');
		$map = array();
		$map['status'] = 1;
		$starttime = strtotime($_REQUEST['starttime']);
		$endtime = strtotime($_REQUEST['endtime']);
		
		$map["createid"]=$_SESSION[C('USER_AUTH_KEY')];
		$map['createtime'] = array("exp",">=".$starttime." and createtime <".$endtime);
		$list = $model->where($map)->field('id,createtime')->select();
		$aaa = $arr= array();
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
	public function lookupgetworkstatement(){
		$planModel=D("MisWorkPlan");
		$list=$planModel->where("id=".$_REQUEST['id'])->find();
		$data=array();
		//组装数据
		$data['lookpeople']=$list['lookpeople'];//查阅人
		$data['lookpeoplename']=$list['lookpeoplename'];
		$data['title']=str_replace("计划", "报", $list['title']);
		$data['summary']=$list['summary'];//摘要
		$data['createid']=$_SESSION[C('USER_AUTH_KEY')];
		$data['createtime']=time();
		//查询工作报告类型
		$TypeModel=M("mis_order_types");
		$typemap['type']=67;
		$typemap['status']=1;
		if($list['typeid']==1){
			$typemap['name']="日报";
		}else if($list['typeid']==2){
			$typemap['name']="周报";
		}else if($list['typeid']==3){
			$typemap['name']="月报";
		}
		$typeid=$TypeModel->where($typemap)->getField("id");
		$data['typeid']=$typeid;//工作报告类型
		//订单编号
		$scnmodel = D('SystemConfigNumber');
		$code = $scnmodel->GetRulesNO('mis_work_statement');
		$data['code']=$code;//单号
		//查询记录内容
		$planContentModel=M("mis_work_plan_content");
		$recordlist=$planContentModel->field("content")->where("planid=".$_REQUEST['id']." and isrecord=1 and status=1")->select();
		$content="";
		foreach ($recordlist as $K=>$v){
			$content .= $v['content'];
		}
		$data['content']=$content;//报告内容
		$data['planid']=$_REQUEST['id'];//计划ID
		$workstatementModel=D("MisWorkStatement");
// 		//判断是否已生成工作报告
// 		$stateid=$workstatementModel->where("planid=".$_REQUEST['id']." and status=1")->getField("id");
// 		if($stateid){
// 			echo -1;
// 			exit;
// 		}
		$re=$workstatementModel->add($data);
		//事物提交
		$workstatementModel->commit();
		if($re===false){
			echo -1;
		}else{
			echo $re;
		}
	}
}

?>