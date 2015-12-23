<?php
/**
 * @Title: MisWorkInformationAction
 * @Package package_name
 * @Description: todo(工作知会)
 * @author 杨希
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-2-21 上午10:15:21
 * @version V1.0
 */
class MisWorkInformationAction extends MisSystemWorkCenterAction{
	/**
	 * @Title: lookupInformperson 
	 * @Description: todo(查看知会人)   
	 * @author libo 
	 * @date 2014-6-11 下午3:47:24 
	 * @throws
	 */
	public function lookupInformperson(){
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
		//扩展工具栏操作
// 		$toolbarextension = $scdmodel->getDetail($name,true,'toolbar');
// 		if ($toolbarextension) {
// 			$this->assign ( 'toolbarextension', $toolbarextension );
// 		}
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
	 * @Title: lookupinforpersonread 
	 * @Description: todo(知会人阅读 Ajax)   
	 * @author libo 
	 * @date 2014-6-12 上午10:50:21 
	 * @throws
	 */
	public function lookupinforpersonread(){
		//工作中心点击查看 修改邮件状态为查看
		$workModel=D("MisWorkExecuting");
		$uid=$_SESSION[C('USER_AUTH_KEY')];
		$messageid=$workModel->where("tableid=".$_REQUEST['id']." and tablename='".$_REQUEST['md']."'")->getField("messageid");
		$messageuserModel=M("mis_message_user");
		$list=$messageuserModel->where("sendid=".$messageid." and recipient=".$uid)->setField("readedStatus",1);
		//查询是否查看
		$worklist=$workModel->where("tableid=".$_REQUEST['id']." and tablename='".$_REQUEST['md']."'")->getField("isread");
		if(!in_array($uid, explode(",", $worklist))){//在数组中 则已查看
			$readstr="";
			if($worklist==NULL){
				$readstr = $uid;
			}else{
				$readstr = $worklist.",".$uid;
			}
			$re=$workModel->where("tableid=".$_REQUEST['id']." and tablename='".$_REQUEST['md']."'")->setField("isread",$readstr);
			$workModel->commit();
		}
		echo 1;
	}
	public function exportBysearchHtml(){
		if($_REQUEST['worktype']==3 || $_REQUEST['worktype']==1){
			$name="MisMyWorking";
		}else{
			$name=$_REQUEST['md'];
		}
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($name,false);
		$newdetail = array();
		foreach($detailList as $k=>$v){
			if(isset($v['isexport']) && $v['isexport']==1){
				$newdetail[]=$v;
			}
		}
		$formid = isset($_POST['formid'])? $_POST['formid']:"";
		$this->assign("formid",$formid);
		$this->assign("fieldarr",$newdetail);
		$this->display ("Public:exportBysearchHtml");
	}
}
?>