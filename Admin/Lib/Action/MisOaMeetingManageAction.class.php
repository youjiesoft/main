<?php
/** 
 * @Title: MisOaMeetingManageAction 
 * @Package package_name
 * @Description: todo(会议管理) 
 * @author 杨东 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-9-23 下午4:22:40 
 * @version V1.0 
*/ 
class MisOaMeetingManageAction extends CommonAction {
	
	/**
	 * @Title: _filter
	 * @Description: todo(过滤函数) 
	 * @param 条件组 $map  
	 * @author 杨东 
	 * @date 2013-9-13 下午2:01:41 
	 * @throws 
	*/  
	public function _filter(&$map) {
		if($_REQUEST['datetime']){
			$map['_string'] = "( createtime >= ".strtotime($_REQUEST['datetime'])." and createtime <".strtotime($_REQUEST['datetime'])."+24*3600 )" ;
		}
		//管理员权限判断
		if ($_SESSION["a"] != 1) $map['status']=array("gt",-1);
	}
	public function _before_add(){
		//自动生成单号
		$scnmodel = D('SystemConfigNumber');
		$orderno = $scnmodel->GetRulesNO('mis_oa_meeting_manage');
		$this->assign("orderno", $orderno);
		//订单号是否可写
		$writable= $scnmodel->GetWritable('mis_oa_meeting_manage');
		$this->assign("writable",$writable);
	}
	
	public function _after_insert($list){
		$this->swf_upload($list,85);//附件上传
	}
	
	public function _before_edit(){
		//获取附件信息
		$this->getAttachedRecordList($_REQUEST['id']);
		//获取参会人
		$OaMeetingManageModel=D("MisOaMeetingManage");
		$joinpeople=$OaMeetingManageModel->where("id=".$_REQUEST['id'])->getField("joinpeople");
		if($joinpeople){
			$joinpeople=explode(",", $joinpeople);
			$this->assign("joinpeople",$joinpeople);
		}
		$userid=$_SESSION[C('USER_AUTH_KEY')];
		$this->assign('userid',$userid);
	}
	/**
	 * @Title: complete 
	 * @Description: todo(会议完成)   
	 * @author yangd 
	 * @date 2013-10-26 下午4:02:46 
	 * @throws
	 */
	public function _after_update($list){
		$this->swf_upload($_POST['id'],85);//附件上传
	}
	/**
	 * @Title: lookupGetdate 
	 */
	public function lookupGetdate(){
		$name = 'MisOaMeetingManage';
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
	/**
	 * @Title: _before_insert 
	 * @Description: todo(参会人转化)
	 * @author libo 
	 * @date 2014-7-23 上午10:16:00 
	 * @throws
	 */
	public function _before_insert(){
		$_POST['joinpeople']=implode(",", $_POST['joinpeople']);
		$_POST['joinpeoplename']=implode(",", $_POST['joinpeoplename']);
		$_POST['notread']=$_POST['joinpeople'];
	}
	/**
	 * @Title: _before_update 
	 * @Description: todo(这里用一句话描述这个方法的作用) 
	 * @author libo 
	 * @date 2014-7-23 上午10:16:14 
	 * @throws
	 */
	public function _before_update(){
		$_POST['joinpeople']=implode(",", $_POST['joinpeople']);
		$_POST['joinpeoplename']=implode(",", $_POST['joinpeoplename']);
		$_POST['notread']=$_POST['joinpeople'];
	}
}
?>