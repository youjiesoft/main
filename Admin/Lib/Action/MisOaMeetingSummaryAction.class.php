<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(会议纪要) 
 * @author libo 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-7-23 下午2:08:44 
 * @version V1.0
 */
class MisOaMeetingSummaryAction extends CommonAction{
	public function _filter(&$map) {
		if($_REQUEST['datetime']){
			$map['_string'] = "( createtime >= ".strtotime($_REQUEST['datetime'])." and createtime <".strtotime($_REQUEST['datetime'])."+24*3600 )" ;
		}
		//管理员权限判断
		if ($_SESSION["a"] != 1) $map['status']=array("gt",-1);
	}
	public function index() {
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
		if( intval($_POST['dwzloadhtml']) ){
			$this->display("dwzloadindex");exit;
		}
		//首页收件箱调用方法，为ajax调用
		if ($_GET['type'] == "ajaxcall") {
			$this->display ("ajax_index");exit;
		}
		if($_REQUEST['jump'] == "jump"){
			$this->display('indexview');exit;
		}
		$this->display ();
		return;
	}
	/**
	 * @Title: _before_add 
	 * @Description: todo(新增前置)   
	 * @author libo 
	 * @date 2014-7-23 下午3:06:57 
	 * @throws
	 */
	public function _before_add(){
		//自动生成单号
		$scnmodel = D('SystemConfigNumber');
		$orderno = $scnmodel->GetRulesNO('mis_oa_meeting_summary');
		$this->assign("orderno", $orderno);
		//订单号是否可写
		$writable= $scnmodel->GetWritable('mis_oa_meeting_summary');
		$this->assign("writable",$writable);
		$userid=$_SESSION[C('USER_AUTH_KEY')];
		$this->assign("userid",$userid);
		$this->assign("time",time());
		if($_REQUEST['meetid']){
			$OaMeetingManageModel=D("MisOaMeetingManage");
			$joinperson=$OaMeetingManageModel->where("id=".$_REQUEST['meetid'])->getfield("joinpeople");
			if($joinperson){
				$joinperson=explode(",", $joinperson);
				$this->assign("joinperson",$joinperson);
			}
			$this->assign("meetid",$_REQUEST['meetid']);
		}
	}
	public function _before_edit(){
		$OaMeetingSummaryModel=D("MisOaMeetingSummary");
		$joinperson=$OaMeetingSummaryModel->where("id=".$_REQUEST['id'])->getfield("joinpeople");
		if($joinperson){
			$joinperson=explode(",", $joinperson);
			$this->assign("joinperson",$joinperson);
		}
	}
	/**
	 * @Title: lookupGetdate
	 */
	public function lookupGetdate(){
		$name = 'MisOaMeetingSummary';
		$model = D($name);
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
	public function _before_insert(){
		$_POST['joinpeople']=implode(",", $_POST['joinpeople']);
		$_POST['joinpeoplename']=implode(",", $_POST['joinpeoplename']);
		$_POST['notread']=$_POST['joinpeople'];
	}
	public function _before_update(){
		$_POST['joinpeople']=implode(",", $_POST['joinpeople']);
		$_POST['joinpeoplename']=implode(",", $_POST['joinpeoplename']);
		$_POST['notread']=$_POST['joinpeople'];
	}
}


?>