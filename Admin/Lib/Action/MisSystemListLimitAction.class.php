<?php
/**
 * @Title: MisSystemListLimitAction
 * @Package package_name
 * @Description: todo(列权限查看)
 * @author xiayq
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-09-23 16:28:46
 * @version V1.0
*/
class MisSystemListLimitAction extends CommonAction {

	public function index(){
		$name='User';
		$scdmodel = D('SystemConfigDetail');
		$this->getSystemConfigDetail('MisSystemListLimit');
		$toolbarextension = $scdmodel->getDetail('MisSystemListLimit',true,'toolbar');
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		//查询当前所有用户
		$model=D($name);
		$list=$model->where('status = 1')->select();
		array_unshift($list,array('tableid'=>0,'name'=>'除允许以外的所有用户'));
		foreach($list as $k=>$v){
			$list[$k]['parentid'] = -1;	
			$list[$k]['tablename'] = 'user';
		}
		$list[]=array(
				'id'=>-1,
				'pId'=>0,
				'tablename'=>'user',
				'name'=>'用户姓名',
				'title'=>'用户姓名',
				'open'=>true,
				'isParent'=>true,
		);
		//查角色
		$roleModel=D('Rolegroup');
		$rolelist=$roleModel->where('status = 1')->select();
		array_unshift($rolelist,array('tableid'=>0,'name'=>'除允许以外的所有角色'));
		foreach($rolelist as $k=>$v){
			$rolelist[$k]['parentid'] = -2;
			$rolelist[$k]['tablename'] = 'rolegroup';
		}
		$rolelist[]=array(
				'id'=>-2,
				'pId'=>0,
				'tablename'=>'rolegroup',
				'name'=>'角色名称',
				'title'=>'角色名称',
				'open'=>true,
				'isParent'=>true,
		);
		//查专家
		$expertModel=D('MisExpertList');
		$expertlist=$expertModel->where('status = 1')->select();
		array_unshift($expertlist,array('tableid'=>0,'name'=>'除允许以外的所有专家'));
		foreach($expertlist as $k=>$v){
			$expertlist[$k]['parentid'] = -3;
			$expertlist[$k]['tablename'] = 'mis_expert_list';
		}
		$expertlist[]=array(
				'id'=>-3,
				'pId'=>0,
				'tablename'=>'mis_expert_list',
				'name'=>'专家名称',
				'title'=>'专家名称',
				'open'=>true,
				'isParent'=>true,
		);
		//封装用户类型结构树
		$listnew = $list;
		$listarr = array_merge($list,$rolelist,$expertlist);
		$param['rel']="MisListLimitview";
		$param['url']="__URL__/index/jump/1/tableid/#id#/tablename/#tablename#";
		$treemiso[]=array(
				'id'=>0,
				'pId'=>0,
				'name'=>'所有用户',
				'title'=>'所有用户',
				'open'=>true,
				'isParent'=>true,
		);
		$treearr = $this->getTree($listarr,$param,$treemiso,false);
		$this->assign("treearr",$treearr);
		//获取用户ID
		$tableid = $_REQUEST['tableid'];
		$tablename = $_REQUEST['tablename'];
		if($tablename!=null){
			$map['tablename']=$tablename;
		}
		if($tableid!=null){
			if($tableid!=-1 && $tableid!=-2 &&  $tableid!=-3){
				$map['tableid']=$tableid;
			}
		}
		$map['status']=array("gt",-1);
		
		//定义一个存储用户数据数组
		$this->_list('MisSystemListLimit', $map);
		$this->assign("valid",$tableid);
		$this->assign("tableid",$tableid);
		$this->assign("tablename",$tablename);
		if($_REQUEST['jump'] == 1){
			$this->display("indexview");
		}else{
			$this->display();
		}
	}
	
	function _after_list($voList){
		foreach ($voList as $vokey=>$voval){
			$nodeModel=D('Node');
			$nodeName=$nodeModel->where("name='".$voval['modelname']."'")->field('title')->find();
			$voList[$vokey]['ModelTitle']=$nodeName['title'];
				
			if($voval['denyfields']!=null){
				$deny=json_decode($voval['denyfields'],true);
				$denyfield = array();
				foreach ($deny as $dk=>$dv){
					$denyfield[]=$dv[1];
				}
				$denyString=implode("，",$denyfield);
				if(strlen($denyString)>90){
					$voList[$vokey]['deny']=mb_substr( $denyString, 0, 30, 'UTF8' ).'...';
				}else{
					$voList[$vokey]['deny']=mb_substr( $denyString, 0, 30, 'UTF8' );
				}
			}else{
				$voList[$vokey]['deny']=null;
			}
		
			if($voval['allowfields']!=null){
				$allow=json_decode($voval['allowfields'],true);
				$allowfield = array();
				foreach ($allow as $dk=>$dv){
					$allowfield[]=$dv[1];
				}
				$allowString=implode("，",$allowfield);
				if(strlen($allowString)>90){
					$voList[$vokey]['allow']=mb_substr( $allowString, 0, 30, 'UTF8' ).'...';
				}else{
					$voList[$vokey]['allow']=mb_substr( $allowString, 0, 30, 'UTF8' );
				}
					
			}else{
				$voList[$vokey]['allow']=null;
			}
		}
	}
	function view(){
		//获取当前控制器名称
		$name=$this->getActionName();
		$model = D ( $name );
		//获取当前主键
		$id = $_REQUEST [$model->getPk ()];
		$map['id']=$id;
		$voList=$model->where($map)->find();
		$nodeModel=D('Node');
		$nodeName=$nodeModel->where("name='".$voList['modelname']."'")->field('title')->find();
		$voList['ModelTitle']=$nodeName['title'];
		$deny=json_decode($voList['denyfields'],true);
		foreach ($deny as $dk=>$dv){
			$denyfield[]=$dv[1];
		}
		$voList['deny']=implode("，",$denyfield);
		$allow=json_decode($voList['allowfields'],true);
		foreach ($allow as $ak=>$av){
			$allowfield[]=$av[1];
		}
		$voList['allow']=implode("，",$allowfield);
		$this->assign('voList',$voList);
		$this->display();
	}
}