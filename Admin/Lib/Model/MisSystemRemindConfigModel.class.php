<?php
class MisSystemRemindConfigModel extends CommonModel{

	protected $trueTableName = 'mis_system_remind_config';
	public $_auto	=array(
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
			array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
	);
	
	
	public function getRemindTree($rel,$type,$jump=1){
		//组成左侧树形菜单
		$MisDynamicFormManageModel=D("MisDynamicFormManage");
		//节点模型
		$NodeModel=D('Node');
		//菜单模型
		$GropModel=D('Group');
		$manMap=array();
		if($type){
			//排除套表主表
			$misAutoBindSettableModel=D("MisAutoBindSettable");
			$misAutoBindSettableList=$misAutoBindSettableModel->where("pid=0 and status=1 and typeid=2")->getField("id,bindformid");
			if($misAutoBindSettableList){
				$manMap['id']=array("not in",array_values($misAutoBindSettableList));
			}
		}
		$manMap['status']=1;
		$manMap['isreminds'] = 1;
		//查询动态表单记录表
		$MisDynamicFormManageList=$MisDynamicFormManageModel->where($manMap)->field("id,actionname,actiontitle")->select();
		//查询所有显示的分组
		$GroupMap = array();
		$GroupMap['status']=1;
		$groupList=$GropModel->where($GroupMap)->order("sorts asc")->getField("id,name");
		//查询node表3级节点
		$where = array();
		$where['level']=3;
		$where['status']=1;
		$where['group_id'] =array("in",array_keys($groupList));
		$nodevo = $NodeModel->where($where)->getField("name,id,pid");
		//存储node节点第二级的id数组
		$pNodeList=array();
		$typeTree=array();
		//组合动态表单节点（最下级）
		foreach ($MisDynamicFormManageList as $key=>$val){
			//节点表存在此节点
			if(in_array($val['actionname'], array_keys($nodevo))){
				$typeTree[]=array('id'=>$val['id'],
						'name'=>$val['actiontitle'],//显示名称
						'title'=>$val['actiontitle'],//显示名称
						'ename'=>$val['actionname'], //model名称
						'pId'=>"-100".$nodevo[$val['actionname']]['pid'],
						'url'=>"__URL__/index/jump/{$jump}/id/".$val['id']."/aname/".$val['actionname'],
						'target'=>'ajax',
						'rel'=>$rel,
						'open'=>false
				);
				$pNodeList[] = $nodevo[$val['actionname']]['pid'];
			}else{
				//$typeTree[]=array('id'=>$val['id'],
				//		'name'=>$val['actiontitle'],//显示名称
				//		'title'=>$val['actionname'],//显示名称
				//		'ename'=>$val['actionname'], //model名称
				//		'pId'=>"-100001",
				//		'url'=>"__URL__/index/jump/1/id/".$val['id']."/aname/".$val['actionname'],
				//		'target'=>'ajax',
				//		'rel'=>$rel,
				//		'open'=>false
				//);
			}
		}
		$pNodeList = array_unique($pNodeList);
		$NodeMap['id']=array("in",$pNodeList);
		$NodeMap['status']=1;
		$nodeList=$NodeModel->where($NodeMap)->getField("id,name,title,group_id");
		//存储菜单分组ID数组
		$groupidArr=array();
		//组合父节点
		foreach ($pNodeList as $k => $v) {
			if($nodeList[$v] ) {
				$typeTree[] = array(
						'id'=>"-100".$v,
						'name'=>$nodeList[$v]['title'],//显示名称
						'title'=>$nodeList[$v]['name'], //model名称
						'pId'=>'-'.$nodeList[$v]['group_id'],
						'open'=>false
				);
				$groupidArr[]=$nodeList[$v]['group_id'];
			}
		}
		$groupidArr=array_unique($groupidArr);
		//组合菜单节点
		foreach ($groupList as $gkey=>$gval){
			if(in_array($gkey, $groupidArr)) {
				$typeTree[] = array(
						'id'=>"-".$gkey,
						'name'=>$gval,//显示名称
						'title'=>$gval, //model名称
						'pId'=>0,
						'open'=>false
				);
			}
		}
		//	$typeTree[]=array(
		//			'id'=>'-100001',
		//		'name'=>"未指定节点",//显示名称
		//			'title'=>"未指定节点", //model名称
		//			'pId'=>0,
		//			'open'=>false
		//	);
		return $typeTree;
	}
}