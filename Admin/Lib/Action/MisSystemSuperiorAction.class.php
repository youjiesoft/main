<?php
/**
 * @Title: file_name
 * @Package package_name
 * @Description: todo(后台用户上级)
 * @author liuzhihong
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015-9-30 
 * @version V1.0
 */
class MisSystemSuperiorAction extends CommonAction {
	function _filter(&$map){
		if ($_SESSION["a"] != 1){
			$map['status']=array("gt",-1);
		}
	}
	public function index() { 
	
	 $UserId = $_SESSION [C ( 'USER_AUTH_KEY' )];
	 $UserModel = D('User');
	 //拿出所有人
	 $Userlist = $UserModel->where("usertype=1")->getfield("id,name,shangji");
	 //echo $UserModel->getlastsql();
	 //遍历这下面的所有ID 获取所有下级
     $list=array_unique(array_filter(explode(",",$UserId.$this->recursionId($UserId,$Userlist))));
     
     
     //查询这个人下的项目条数
     $getmodel = D('MisWorkMonitoring');
     $arr = array();
     foreach($list as $k=>$v){
        $map = array();
     	$map['id']  = array('gt',0);
     	$map['dostatus'] = 0;
     	$map['isauditstatus'] = 1;
     	$map['_string'] = 'FIND_IN_SET(  '.$v.', curAuditUser )';
     	if($a = $getmodel->where($map)->field("tablename,createtime,curAuditUser")->select()){
     	$arr[$k] = $getmodel->where($map)->field("tablename,createtime,curAuditUser")->select();
     	$arr[$k]['count'] = count($arr[$k]);
     	$arr[$k]['user']=$v;
     	}
     } 
     //根据ID获取所有的部门ID
     $getDeptidmodel = M('user_dept_duty');
     $deptidListArr = array();
     foreach ($arr as $k=>$v){
     	$where = array();
     	$where['a.userid'] = $v['user'];
     	$deptidList = $getDeptidmodel->table("user_dept_duty AS a")->join("mis_system_department AS b ON a.deptid = b.id")->where($where)->getField("a.userid,b.id,b.name");
     	foreach ($deptidList as $k=>$v){
     		$deptidListArr[] = $v;
     	}
     }
     $this->assign("deptidListArr",$deptidListArr);
      
     //把相同的部门找出来
     $deptidArr = array();
     foreach ($deptidListArr as $k=>$v){
     	$deptidArr[] = $v['name'];
     }
     //去掉相同的部门名字
     $deptidArr = array_unique($deptidArr);
     
     $this->assign("deptidArr",$deptidArr);
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
     $this->assign("User",$Userlist);
     $this->assign("vorr",$arr);
     if($_REQUEST['jump']){
     	$this->getWorkMonitList();
     }else{
     	$this->display();
     }
	}
	//查询所有下级
	public function recursionId($userId,$Userlist,$hasparentsid=true){
		$arr='';
			foreach ($Userlist as $k=>$v){
				if($v['shangji']==$userId ){
					unset($Userlist[$k]); 
					$arr.=",".$v['id'].$this->recursionId($v['id'],$Userlist);
				}
			}
			return $arr;
	}
	protected function getWorkMonitList(){
		$this->assign("userid",$_REQUEST['userid']);
		$detailname="MisMyWorking";

		//列表过滤器，生成查询Map对象
		$map = array();
		$map = $this->_search ($detailname);
		//获取流程分类ID
		
		$map['dostatus'] = 0;
     	$map['isauditstatus'] = 1;
     	$map['_string'] = 'FIND_IN_SET(  '.$_REQUEST['userid'].', curAuditUser )';
		if (! empty ( $detailname )) {
			$this->_list('MisWorkMonitoring', $map);
		}
		$scdmodel = D('SystemConfigDetail');
		
		$detailList = $scdmodel->getDetail($detailname);
		
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		//扩展工具栏操作
		$toolbarextension = $scdmodel->getDetail($name,true,'toolbar');
		
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		if($_REQUEST['jump']=="jump"){
			$this->display('indexview');exit;
		}else if($_REQUEST['jump']=="serach"){
			$this->display('indexviewtree');exit;
		}
		$this->display ();
		exit;
	}
}