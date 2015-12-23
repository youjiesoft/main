<?php
/** 
 * @Title: MisOaMeetingPersonAction 
 * @Package package_name
 * @Description: todo(会议参与查看) 
 * @author 杨东 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-10-23 上午10:58:41 
 * @version V1.0 
*/ 
class MisOaMeetingPersonAction extends CommonAction {
	
	/**
	 * @Title: _filter
	 * @Description: todo(过滤函数) 
	 * @param 条件组 $map  
	 * @author 杨东 
	 * @date 2013-9-13 下午2:01:41 
	 * @throws 
	*/  
	public function _filter(&$map) {
		$userid=$_SESSION[C('USER_AUTH_KEY')];
		$map['_string'] .= "FIND_IN_SET( '$userid' ,joinpeople )" ;
		$map['stepType'] = 1;
		if($_REQUEST['datetime']){
			$map['_string'] .= " and ( createtime >= ".strtotime($_REQUEST['datetime'])." and createtime <".strtotime($_REQUEST['datetime'])."+24*3600 )" ;
		}
		$this->assign("uid",$userid);
	}
	public function index() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name = "MisOaMeetingManage";
		if (! empty ( $name )) {
			$qx_name=$name;
			if(substr($name, -4)=="View"){
				$qx_name = substr($name,0, -4);
			}
			//验证浏览及权限
			$this->_list ( $name, $map );
		}
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail("MisOaMeetingPerson");
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		//扩展工具栏操作
		$toolbarextension = $scdmodel->getDetail("MisOaMeetingPerson",true,'toolbar');
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
	public function edit(){
		//修改状态
		$OaMeetingManageModel= D("MisOaMeetingManage");
		$notreadid=$OaMeetingManageModel->where("id=".$_REQUEST['id'])->getField('notread');
		$uid=$_SESSION[C('USER_AUTH_KEY')];
		$notreadarr=explode(",", $notreadid);//未读人员的id数组
		if(in_array($uid, $notreadarr)){
			foreach ($notreadarr as $k=>$v){
				if($v==$uid){
					unset($notreadarr[$k]);
				}
			}
			$notreadidstr=implode(",", $notreadarr);
			$re=$OaMeetingManageModel->where("id=".$_REQUEST['id'])->setField("notread",$notreadidstr);
			if($re===false){
				$this->error("修改查询状态失败");
			}
		}
		//查询信息
		$list=$OaMeetingManageModel->where("id=".$_REQUEST['id'])->find();
		$this->assign("vo",$list);
		//读取动态配制
		$this->getSystemConfigDetail("MisOaMeetingManage");
		//获取参会人
		$joinpeople=$list["joinpeople"];
		if($joinpeople){
			$joinpeople=explode(",", $joinpeople);
			$this->assign("joinpeople",$joinpeople);
		}
		//获取附件信息
		$this->getAttachedRecordList($_REQUEST['id'],true,true,'MisOaMeetingManage');
		$this->display();
	}
	/**
	 * @Title: lookupGetdate 
	 * @Description: todo(日历)   
	 */
	public function lookupGetdate(){
		$name = 'MisOaMeetingManage';
		$model = D($name);
		$map = array();
		$userid=$_SESSION[C('USER_AUTH_KEY')];
		$map['status'] = 1;
		$map['_string'] = "FIND_IN_SET( '$userid' ,joinpeople )" ;
		$map['stepType'] = 1;
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
}
	
	
?>