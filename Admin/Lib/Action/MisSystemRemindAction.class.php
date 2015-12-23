<?php
/**
 * @Title: file_name
 * @Package package_name
 * @Description: todo(打印配置控制器)
 * @author liminggang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-6-1 上午9:16:42
 * @version V1.0
 */
class MisSystemRemindAction extends CommonAction {
	public function getConfig(){}
	/**
	 *
	 * @Title: getUserRemind
	 * @Description: todo(提醒中心)
	 * @author renling
	 * @date 2014-3-26 下午5:08:34
	 * @throws
	 */
	public function getUserRemind(){
		$count=5;
		if($_REQUEST['page']){
			if($_REQUEST['page']=='prevpage'){
				$max=$_REQUEST['maxlimit']-$count;
				$min=$_REQUEST['minlimit']-$count;
			}
			if($_REQUEST['page']=='nextpage'){
				$max=$_REQUEST['maxlimit']+$count;
				$min=$_REQUEST['minlimit']+$count;
			}
		}else{
			// 默认取值6个
			$min=0;
			$max=$count;
		}
		$MisSystemRemindModel=D("MisSystemRemind");
		$remindAllList=$MisSystemRemindModel->lookupremindList();
		$remindNewAllList=array();
		foreach ($remindAllList as $key=>$val){
			if($key>=$min && $key<$max){
				$remindNewAllList[]=$val;
			}
		}
		$this->assign("nextlist",$remindAllList[$max]);
		$this->assign("prevlist",$remindAllList[$min-1]);
		$this->assign("maxcount",$max);
		$this->assign("mincount",$min);
		$this->assign("remindAllList",$remindNewAllList);
	}
	public function lookupmyRemindDis(){
		$this->getUserRemind();
		$this->display("MisSystemRemind:lookupmyRemindDis");
	}
	public function calder(){
		//日历上面的日程信息
		$MisUserEventsModel = D("MisUserEvents");
		$myEvents=$MisUserEventsModel->getMyEvents();
		$this->assign('myEvents',json_encode($myEvents));
		$this->display("MisSystemRemind:calder");
	}
	/**
	 *
	 * @Title: lookupchangeedit
	 * @Description: todo(修改提醒条件)
	 * @author renling
	 * @date 2014-8-11 下午2:16:31
	 * @throws
	 */
	public  function lookupchangeedit(){
		$map['id']=$_REQUEST['id'];
		$MisSystemRemindModel=D("MisSystemRemind");
		$remindAllList=$MisSystemRemindModel->lookupremindList($map);
		$this->assign("listkey",$_REQUEST['list']);
		$this->assign("remindAllList",$remindAllList);
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($remindAllList[0]['name']);
		$this->assign("detailList",$detailList);
		//查询连字符
		$html=getSelectByHtml('roleinexp','select');
		$html= str_replace('"', "'", $html);
		$this->assign("html",$html);
		$this->assign("md",$_REQUEST['md']);
		$this->display();
	}
	/**
	 *
	 * @Title: lookupchangeremind
	 * @Description: todo(修改提醒中心)
	 * @author renling
	 * @date 2014-8-8 下午2:18:25
	 * @throws
	 */
	public function lookupchangeremind(){
		//节点模型
		$nodemodel=D("node");
		$MisSystemRemindModel=D("MisSystemRemind");
		$remindAllList=$MisSystemRemindModel->lookupremindList();
		$this->assign("remindAllList",$remindAllList);
		$this->display();
	}
	/**
	 *
	 * @Title: lookupaddremind
	 * @Description: todo(工具栏加入提醒)
	 * @author renling
	 * @date 2014-8-4 下午3:35:48
	 * @throws
	 */
	public function lookupaddremind(){
		$modelname=$_REQUEST['md'];
		//读取配置信息
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($modelname);
		$this->assign("detailList",$detailList);
		$this->assign("md",$_REQUEST['md']);
		//查询连字符
		$html=getSelectByHtml('roleinexp','select');
		$html= str_replace('"', "'", $html);
		$this->assign("html",$html);
		//查询list文件
		$this->display();
	}
	/**
	 * 
	 * @Title: lookupremindinsert
	 * @Description: todo(添加提醒)   
	 * @author renling 
	 * @date 2014-9-1 下午3:11:20 
	 * @throws
	 */
	public function lookupremindinsert(){
		$MisSystemRemindModel=D("MisSystemRemind");
		$result=$MisSystemRemindModel->lookupinsertremind();
		if($result){
			$this->success("添加提醒成功！");
		}else{
			$this->error("添加提醒失败！");
		}
	}
	/**
	 *
	 * @Title: lookupdeleteremind
	 * @Description: todo(ajax请求删除提醒条件)
	 * @author renling
	 * @date 2014-8-13 下午3:04:00
	 * @throws
	 */
	public function lookupdeleteremind(){
		$MisSystemRemindModel=D('MisSystemRemind');
		$id=$_POST['id'];
		$uid=$_SESSION[C('USER_AUTH_KEY')];
		$keyid=$_POST['keyv'];
		$type=$_POST['type'];
		$date=array();
		$date['id']=$_POST['id'];
		if($type=="all"){
			//排除此用户常用
			$date['deluserid']=getFieldBy($id, "id", "deluserid", "mis_system_remind").$uid.",";
		}else{
			//去掉数组中的提醒
			$RemindVo=$MisSystemRemindModel->where("id=".$id)->find();
			$addlist=unserialize($RemindVo['reminddetail']);
			$mapoldList=unserialize($RemindVo['map']);
			unset($addlist['list'][$keyid]);
			unset($mapoldList[$keyid]);
			$date['createid']=$uid;
			$date['createtime']=time();
			$date['map']=serialize($mapoldList);
			$date['reminddetail']=serialize($addlist);
			if(!$addlist['list']){
				$date['status']=-1;
			}
		}
		$result=$MisSystemRemindModel->save($date);
		$MisSystemRemindModel->commit();
		echo $result;
	}

}