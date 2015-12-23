<?php
/**
 *
 * @Title: MisSystemRemindModel
 * @Package package_name
 * @Description: todo(首页提醒模块)
 * @author renling
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-8-8 上午10:22:50
 * @version V1.0
 */
class MisSystemRemindModel extends CommonModel{
	protected $trueTableName = 'mis_system_remind';
	public $_auto	=array(
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
		   array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),

	);
	/**
	 *
	 * @Title: lookupremindList
	 * @Description: todo(获取用户提醒list)
	 * @param unknown_type $map
	 * @return unknown
	 * @author renling
	 * @date 2014-8-29 下午5:18:47
	 * @throws
	*/
	public function lookupremindList($map){
		$uid=$_SESSION[C('USER_AUTH_KEY')];
		//提醒表
		$MisSystemRemindModel=D('MisSystemRemind');
		if($map){
			$userMap=$map;
		}
		$alluserid=array();
		//查询排除id
		$MisSystemRemindallList=$MisSystemRemindModel->where(" status=1 and userid ='all'  and deluserid not in (".$uid.")")->select();
		foreach ($MisSystemRemindallList as $akey=>$aval){
			if($aval['deluserid']){
				$deluserid=explode(",",$aval['deluserid']);
				if(in_array($uid,array_values($deluserid))){
					$alluserid[]=$aval['id'];
				}
			}
		}
		// 		if($alluserid){
		// 			$userMap['id']=array("not in",array_values($alluserid));
		// 		}
		$userMap['_string'] = "  userid =".$uid." or  userid ='all' and status=1 and deluserid not in (".$uid.")";
		$userMap['status']=1;
		$remindList=$MisSystemRemindModel->where($userMap)->select();
		$remindAllList=array();
		//当前时间
		$time=time();
		$nowtime= strtotime(transTime($time,'Y-m-d')." 00:00");
		//提醒中心单独查询条数
		$remindModel=D("MisSystemDataRemindMasView");
		foreach ($remindList as $key=>$rval){
			$count=0;
			$sumcount=0;
			$val=unserialize($rval['reminddetail']);
			if($val['modulename']){
				$model=D($val['modulename']);
			}
			//封装外层list
			$remindAllList[$rval['id']]=array(
					'id'=>$rval['id'],
					'userid'=>$rval['userid'],
					'color'=>$val['color'],
					'title'=>mb_substr($val['title'], 0, 2, 'utf-8'),
					'map'=>unserialize($rval['map']),
					'name'=>$val['modulename'],
					'span'=>$val['span'],
			);
			//循环替换map里 time 以及特殊符号表示
			foreach ($val['list'] as $lkey=>$lval){
				if($lval['map']){
					//替换map中不规范数据
					$lval['map']= str_replace (array ('&quot;','&#39;','&lt;','&gt;','$uid','$time','$nowtime'), array ('"', "'",'<','>',$uid,$time,$nowtime),$lval['map']);
					if($val['modulename'] == 'MisWorkPlan'){
						$planModel=D("MisWorkPlan");
						$plan['_string']="( FIND_IN_SET('$uid', commentpeople) )";
						//查询满足条件条数
						$planlist=$planModel->field("id")->where($plan)->select();
						if($planlist){
							foreach ($planlist as $v){
								$comid.=",".$v['id'];
							}
							$comid=substr($comid, 1);
							$lval['map'] .=" and id not in (".$comid.")";
						}
					}
				}
				if($val['modulename']=="MisSystemDataRemindSub"){
					$remindMap=array();
					$remindMap['mis_system_data_remind_sub.status']=1;
					//当前用户
					$remindMap['mis_system_data_remind_sub.userid']=$_SESSION[C('USER_AUTH_KEY')];
					//未读
					$remindMap['mis_system_data_remind_sub.isread']= 0;
					//弹框
					$remindMap['mis_system_data_remind_sub.operation']=1;
					$remindList=$remindModel->where($remindMap)->getField('pkey,modelname');
					$count=count($remindList);
				}else{
					//查询符合条件条数
					$count=$model->where($lval['map'])->count('*');
				}
				$sumcount+=$count;
				if($count > 99){
					$count='99+';
				}
				//封装符合条件子数组 list
				$remindAllList[$rval['id']]['list'][$lkey]=array(
						'relhref'=>$lval['relmodule'],
						'remindMap'=>base64_encode($lval['map']),
						'showrules'=>$lval['showmap'],
						'rulesinfo'=>$lval['listarr'],
						'rules'=>$lval['map'],
						'count'=>$count,
						'keyv'=>$lkey,
						'reltitle'=>$val['title'],
						'rtitle'=>$lval['rtitle'],
				);
			}
			$remindAllList[$rval['id']]['sumcount']=$sumcount;
		}
		//通过keyv 重新对数组进行排序
		array_sort($remindAllList,'keyv','desc');
		//根据条数重新对数组进行排序
		$remindAllList = array_merge(array_sort($remindAllList,'sumcount','desc'));
		return  $remindAllList;
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
	/**
	 *
	 * @Title: lookupinsertremind
	 * @Description: todo(添加提醒)
	 * @author renling
	 * @date 2014-8-7 上午11:11:53
	 * @throws
	 */
	public function lookupinsertremind(){
		$MisSystemRemindModel=D('MisSystemRemind');
		$uid=$_SESSION[C('USER_AUTH_KEY')];
		$list=$MisSystemRemindModel->where("status=1 and userid=".$uid)->select();
		$addlist=array();
		$addkey=0;
		$mapList=array();
		$mapoldList=array();
		if($list){
			foreach ($list as $key=>$val){
				$remindVo=unserialize($val['reminddetail']);
				if($remindVo['modulename'] ==$_POST['md']){
					$addlist=$remindVo;
					$addkey=$val['id'];
					$mapoldList=unserialize($val['map']);
				}
			}
		}
		$map="status=1  ";
		$map.=" and ".$_POST['rules'];
		$dayList=array();
		$ruleList=array();
		$rulesoldList=array();
		$i=0;
		$rulesoldList=explode(";",$_POST['rulesinfo']);
		$addnewlist=array();
		if($addlist){
			if($_POST['listkey']!=""){
				//修改先移除原有的条件
				unset($addlist['list'][$_POST['listkey']]);
				unset($mapoldList[$_POST['listkey']]);
			}
			$newaddremindList=array(
					'relmodule'=>$_POST['md'].'/index',
					'rtitle'=>$_POST['title'],
					'timemap'=>$relday,
					'map'=>$map,
					'showmap'=>$_POST['showrules'],
					'listarr'=>$_POST['rulesinfo'],
			);
			if($_POST['listkey']!=""){
				$addlist['list'][$_POST['listkey']]=$newaddremindList;
			}else{
				$addlist['list'][]=$newaddremindList;
			}
		}else{
			$addlist=array(
					'modulename' => $_POST['md'],
					'title'=>getFieldBy($_POST['md'], "name", "title", "node"),
					'span'=>'icon-th-large',
					'color'=>'tml-bg-recolor',
					'list'=>array(
							'0'=>array(
									'relmodule'=>$_POST['md'].'/index',
									'rtitle'=>$_POST['title'],
									'timemap'=>$relday,
									'map'=>$map,
									'showmap'=>$_POST['showrules'],
									'listarr'=>$_POST['rulesinfo'],
							),
					)
			);
		}
		if($_POST['listkey']!=""){
			$mapoldList[$_POST['listkey']]=$rulesoldList;
		}else{
			$mapoldList[]=$rulesoldList;
		}
		ksort($mapoldList);
		ksort($addlist['list']);
		$data=array();
		if($_POST['id']){
			$data['id']=$_POST['id'];
		}
		$data['modulename']=$_POST['md'];
		$data['userid']=$uid;
		$data['createid']=$uid;
		$data['createtime']=time();
		$data['map']=serialize($mapoldList);
		$data['reminddetail']=serialize($addlist);
		if($addkey){
			//修改序列化
			$result=$MisSystemRemindModel->where("id=".$addkey)->save($data);
		}else{
			//添加提醒模块
			$result=$MisSystemRemindModel->add($data);
		}
		if($result!==false){
			return 1;
		}
	}


}
?>