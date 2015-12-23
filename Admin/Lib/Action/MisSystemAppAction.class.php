<?php
/**
 * 
 * @Title: MisSystemAppAction 
 * @Package package_name
 * @Description: todo(APP中心) 
 * @author renling 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-9-1 上午11:11:31 
 * @version V1.0
 */
class MisSystemAppAction extends CommonAction {
	/**
	 *
	 * @Title: lookupappinsert
	 * @Description: todo(app 应用添加)
	 * @author renling
	 * @date 2014-7-8 上午10:42:36
	 * @throws
	 */
	public function lookupappinsert(){
		$model=D('User');
		$data=array(
				'title'=>$_POST['title'],
				'type'=>$_POST['type'],
				'src'=>$_POST['src'],
				'imgsrc'=>$_POST['imgsrc'],
				'height'=>$_POST['height'],
				'show'=>1,
				'isnew'=>1,
		);
		$model->setApp($data);
		$this->success ( L('_SUCCESS_') );
	}
	/**
	 *
	 * @Title: lookupmyfunctionbox
	 * @Description: todo(app应用 首页调用)
	 * @author renling
	 * @date 2014-7-1 下午5:04:53
	 * @throws
	 */
	public function getUserApp(){
		$saveType=6;
		$selectlist = require('./Dynamicconf/System/selectlist.inc.php');
		if(!$_REQUEST['typeid']){
			$_REQUEST['typeid']=1;//默认为第一个
		}
		//查询类型
		$this->assign("apptypelist",$selectlist['apptype']['apptype']);
		$newappList=$this->lookupappList(1);
		if($_REQUEST['child']){
			$this->assign("newappVo",$newappList[$_REQUEST['typeid']]['list'][$_REQUEST['child']]);
		}else{
			$this->assign("newappVo",$newappList[$_REQUEST['typeid']]['list'][$newappList[$_REQUEST['typeid']]['list']['id']]);
		}
		$count=6;
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
		//判断数组中下一组是否还有数据
		if($_REQUEST['typeid']<$saveType){
			$this->assign("nextlist",count($newappList[$_REQUEST['typeid']]['list'])>$max);
			$this->assign("prevlist",$userAppVo[$_REQUEST['typeid']]['list']['id']<$min);
		}else{
			$this->assign("nextlist",$newappList[$_REQUEST['typeid']]['list'][$max]);
			$this->assign("prevlist",$newappList[$_REQUEST['typeid']]['list'][$min-1]);
		}
		$this->assign("maxcount",$max);
		$this->assign("mincount",$min);
		$cnewapplist=array();
		foreach ($newappList[$_REQUEST['typeid']]['list'] as $apkey=>$apval){
			if($apval['pageid']>$min&&$apval['pageid']<=$max &&!$userAppVo[$_REQUEST['typeid']]['list'][$apkey]['del']){
				$cnewapplist[$_REQUEST['typeid']]['list'][$apkey]=$apval;
			}
		}
		$this->assign("typeid",$_REQUEST['typeid']);
		$this->assign("newappList",$cnewapplist);
	}
	/**
	 * 
	 * @Title: lookupmyfunctionbox
	 * @Description: todo(App中心)   
	 * @author renling 
	 * @date 2014-9-1 下午1:54:07 
	 * @throws
	 */
	public function lookupmyfunctionbox(){
		$this->getUserApp();
		$this->display("MisSystemApp:lookupmyfunctionbox");
	}
	/**
	 *
	 * @Title: appdis
	 * @Description: todo(App中心 类别刷新)
	 * @author renling
	 * @date 2014-9-1 下午1:54:07
	 * @throws
	 */
	public function appdis(){
		$this->getUserApp();
		$this->display();
	}
	/**
	 *
	 * @Title: apppage
	 * @Description: todo(App中心 分页刷新)
	 * @author renling
	 * @date 2014-9-1 下午1:54:07
	 * @throws
	 */
	public function apppage(){
		$this->getUserApp();
		$this->display();
	}
	
	/**
	 *
	 * @Title: lookupuserSetApp
	 * @Description: todo(app个人设置加载页面)
	 * @author renling
	 * @date 2014-7-4 下午2:06:50
	 * @throws
	 */
	public function lookupuserSetApp(){
		if(!$_REQUEST['typeid']){
			$_REQUEST['typeid']=1;//默认为第一个
		}
		$selectlist = require('./Dynamicconf/System/selectlist.inc.php');
		//传送类型
		$this->assign("apptypelist",$selectlist['apptype']['apptype']);
		$newappList=$this->lookupappList(2);
		//查询用户自行设置应用
		$this->assign("newappList",$newappList);
		$this->assign("typeid",$_REQUEST['typeid']);
		$this->display();
	}

	//查询当前用户设置的app
	public function lookupuserappInfo(){
		//查询当前用户设置app
		$userModel=D('User');
		$userMap=array();
		$userMap['id']=$_SESSION[C('USER_AUTH_KEY')];
		$userMap['status']=1;
		$userserAppVo=$userModel->where($userMap)->field("appdateils,id,leavetime")->find();
		return $userserAppVo;
	}
	
	public function lookupappList($type){
		$saveType=6;
		//组合数组
		$applist = require('./Dynamicconf/System/appconfig.inc.php');
		//查询用户自行设置应用
		$userserAppVo=$this->lookupuserappInfo(1);
		$userAppVo=unserialize($userserAppVo['appdateils']);
		//组装最新应用及我的添加
		$newappList=array();
		$newdeffappList=array();
		$newappListapp=array();
		$newappListUser=array();
		$sorti=0;
		foreach ($applist as $key=>$val){
			$typearr=array();
			if($val['show']==1){
				if($val['type']==$_REQUEST['typeid']){
					$val['typename']=$selectlist['apptype'][$val['type']];
					$val['sortnum']=$sorti;
					$newappListapp[$val['type']]['list'][]= $val;
					$sorti++;
				}
				$val['typename']=$selectlist['apptype'][$val['type']];
				$newdeffappList[$val['type']]['list'][]= $val;
			}
		}
		//存在自行配制应用
		if($userAppVo){
			foreach ($userAppVo[$_REQUEST['typeid']]['list'] as $vokey=>$voval){
				$newappListUser[$_REQUEST['typeid']]['list'][$vokey]=$newappListapp[$_REQUEST['typeid']]['list'][$vokey] ;
			}
		}
		if($newappListUser){
			$newappList=$newappListUser;
		}else{
			$newappList=$newappListapp;
		}
		//组装最新应用
		if($userAppVo){
			if($_REQUEST['typeid']==6){
				foreach($newdeffappList as $newkey=>$newval){
					foreach ($newval['list'] as $childkey=>$chilval){
						if(!$userAppVo[$newval['list'][$childkey]['type']]['list'][$childkey] && $chilval ){
							//最新应用
							$newval['list'][$childkey]['oldtype']=$chilval['type'];
							$newappList[$_REQUEST['typeid']]['list'][]=$newval['list'][$childkey];
						}
					}
				}
			}
		}else{
			foreach($newdeffappList as $newkey=>$newval){
				foreach ($newval['list'] as $childkey=>$chilval){
					if($chilval['isnew']){
						//最新应用
						if($_REQUEST['typeid']==6){
							$newval['list'][$childkey]['oldtype']=$chilval['type'];
							$newappList[$_REQUEST['typeid']]['list'][]=$newval['list'][$childkey];
						}else{
							unset($newappList[$chilval['type']]['list'][$childkey]);
						}
					}
				}
			}
		}
		//组装我的添加
		if($_REQUEST['typeid']==7){
			//查询当前用户添加的功能盒子
			$MisSystemFunctionalBoxModel=D('MisSystemFunctionalBox');
			$funMap=array();
			$funMap['status']=1;
			$funMap['userid']=$_SESSION[C('USER_AUTH_KEY')];
			$MisSystemFunctionalBoxList=$MisSystemFunctionalBoxModel->where($funMap)->select();
			if($MisSystemFunctionalBoxList){
				foreach ($MisSystemFunctionalBoxList as $fkey=>$fval){
					$imgsrc="";
					if($fval['logo']){
						$imgsrc="__PUBLIC__/Uploads/".$fval['logo'];
					}
					$newappList[$_REQUEST['typeid']]['list'][]=array(
							"title" => $fval['name'],
							"type" =>7,
							"src" =>$fval['qlink'].$fval['link'],
							"imgsrc" =>$imgsrc,
							"height" =>$fval['height'],
							"show" => 1,
							"sqlid"=>$fval['id'],
							"width" => $fval['width'],
							"typename" =>"我的添加",
	
					);
				}
			}
		}
		//查询用户自行设置应用
		$userserAppVo=$this->lookupuserappInfo();
		$userAppVo=unserialize($userserAppVo['appdateils']);
		$childarray=array();
		$i=1;
		foreach ($newappList[$_REQUEST['typeid']]['list'] as $nkey=>$nval){
			if($_REQUEST['typeid']<$saveType){
				if($userAppVo[$nval['type']]['list'][$nkey]['del']){
					unset($newappList[$nval['type']]['list'][$nkey]);
				}else{
					$childarray[]=array("id"=>$nkey);
					$newappList[$nval['type']]['list'][$nkey]['pageid']=$i;
					$i++;
				}
			}else{
				$childarray[]=array("id"=>$nkey);
				$newappList[$_REQUEST['typeid']]['list'][$nkey]['pageid']=$i;
				$newappList[$_REQUEST['typeid']]['list'][$nkey]['sortnum']=$i-1;
				$i++;
			}
		}
		if($type==1){
			$newappList[$_REQUEST['typeid']]['list']['id']=$childarray[0]['id'];
		}
		return $newappList;
	}
	
	/**
	 *
	 * @Title: saveUserApp
	 * @Description: todo(保存用户自行添加应用)
	 * @author renling
	 * @date 2014-7-4 下午3:40:18
	 * @throws
	 */
	public function lookupsaveUserApp(){
		$typeid=$_REQUEST['typeid'];
		$key=$_REQUEST['key'];
		//查询当前用户设置app
		$userModel=D('User');
		$userMap['id']=$_SESSION[C('USER_AUTH_KEY')];
		$userMap['status']=1;
		$userAppVo=$userModel->where($userMap)->field("appdateils,id")->find();
		$restAppList=array();
		if($userAppVo['appdateils']){
			$restAppList=unserialize($userAppVo['appdateils']);
		}else{
			//不存在查询管理员数据列表进行赋值
			$restAppList=$this->getSysAppList();
		}
		//还原删除数据
		if($_REQUEST['reset']){
			foreach ($restAppList as  $rkey=>$rval){
				foreach($rval['list'] as $rlkey=>$rval){
					if($rval['del']){
						unset($restAppList[$rkey]['list'][$rlkey]['del']);
					}
				}
			}
		}else if($_REQUEST['newapp']){
			//组合数组   改到此处 数据有误
			$applist = require('./Dynamicconf/System/appconfig.inc.php');
			$newdeffappList=array();
			foreach ($applist as $key=>$val){
				$typearr=array();
				$val['typename']=$selectlist['apptype'][$val['type']];
				$newdeffappList[$val['type']]['list'][]= $val;
			}
			foreach ($newdeffappList[$_REQUEST['oldtype']]['list'] as $akey=>$aval){
				if($_REQUEST['src']==$aval['src']&&$_REQUEST['imgsrc']==$aval['imgsrc'] ){
					$restAppList[$_REQUEST['oldtype']]['list'][$akey]=array(
							'title'=>$aval['title'],
					);
					continue;
				}
			}
		}else{
			$restAppList[$typeid]['list'][$key]['del']=1;
		}
	
		$userDate=array();
		$userDate['appdateils']=serialize($restAppList);
		$userDate['id']=$userAppVo['id'];
		$userResult=$userModel->save($userDate);
		$userModel->commit();
		echo $userResult;
	}
	/**
	 * 
	 * @Title: serizeAppList
	 * @Description: todo(获取默认app) 
	 * @return multitype:unknown   
	 * @author renling 
	 * @date 2014-9-1 下午2:52:26 
	 * @throws
	 */
	private function getSysAppList(){
		//组合数组
		$applist = require('./Dynamicconf/System/appconfig.inc.php');
		foreach ($applist as $key=>$val){
			$typearr=array();
			if($val['show']==1 && !$val['isnew']){
				if(in_array($val['type'], array_keys($newappList))){
					$newappList[$val['type']]['list'][] = array(
							'title'=>$val['title']
					);
				}else{
					$newappList[$val['type']]['list'][]= array(
							'title'=>$val['title']
					);
				}
			}
		}
		return  $newappList;
	}
	/**
	 *
	 * @Title: saveMyApp
	 * @Description: todo(删除我的添加)
	 * @author renling
	 * @date 2014-7-8 下午2:46:36
	 * @throws
	 */
	public function saveMyApp(){
		$MisSystemFunctionalBoxModel=D('MisSystemFunctionalBox');
		$MisSystemFunctionalBoxResult=$MisSystemFunctionalBoxModel->where("id=".$_REQUEST['key'])->setField ( 'status', - 1 );
		$MisSystemFunctionalBoxModel->commit();
		echo $MisSystemFunctionalBoxResult;
	}
}
?>