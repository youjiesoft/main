<?php
 class MisDynamicFormManageModel extends CommonModel{
	/*protected $_link=array(
		'DynamicFormField'=>array(
			'mapping_type'=>HAS_MANY,
			'foreign_key'=>'fieldid',
			'as_fields'=>'fieldname,fieldtigetAnametreetle,fieldtype,fieldonly',
		),
	);*/
	protected $trueTableName = 'mis_dynamic_form_manage';
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
	 * @Title: getAnametree
	 * @Description: todo(列表页面树形构造) 
	 * @return multitype:string number multitype:string NULL  multitype:string boolean unknown  multitype:string boolean unknown NULL  multitype:string number unknown    
	 * @author renling 
	 * @date 2014-9-28 下午4:01:53 
	 * @throws
	 */
	public function getAnametree($rel,$type,$jump=1){
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
		//查询动态表单记录表
		$MisDynamicFormManageList=$this->where($manMap)->field("id,actionname,actiontitle")->select(); 
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
	public function getAnameSqltree(){
		//组成左侧树形菜单
		$MisDynamicDatabaseMasModel=M("mis_dynamic_database_mas");
		$MisDynamicFormProperyModel=M("mis_dynamic_form_propery");
		$nodeModel=M("Node");
		$nodeList=$nodeModel->where("status=1")->getField("name,title");
		$MisDynamicDatabaseMasList=$MisDynamicDatabaseMasModel->where("status=1")->select();
		$listArr=array();
		$MisDynamicFormProperyList=$MisDynamicFormProperyModel->where("category='datatable'")->getField("id,fieldname,title,formid");
		foreach ($MisDynamicFormProperyList as $dakey=>$daval){
			$MisDynamicFormProperyList[$daval['formid']][]=$daval;
		}
		foreach($MisDynamicDatabaseMasList as $key=>$val){
			$listArr[$val['modelname']][]=array(
				'name'=>$val['tablename'],
				'title'=>$val['tabletitle']
			);
			//查询当前表单书否有内嵌表格
			if($MisDynamicFormProperyList[$val['formid']]){
				foreach ($MisDynamicFormProperyList[$val['formid']] as $fkey=>$fval){
					$listArr[$val['modelname']][]=array(
							'name'=>$val['tablename']."_sub_".$fval['fieldname'],
							'title'=>$fval['title']
					);
				}
			}
		}
		$k=1;
		$i=1;
		foreach ($listArr as $lkey=>$lval){
			$typeTree[]=array('id'=>-$k,
					'name'=>$nodeList[$lkey],//显示名称
					'title'=>$val[$lkey],//显示名称
					'pId'=>0,
					'open'=>false,
					'isParent'=>true,
			);
			foreach ($lval as $vkey=>$vval){
				$typeTree[]=array('id'=>$i,
						'name'=>$vval['title'],//显示名称
						'title'=>$vval['title'],//显示名称
						'tableinfo'=>$vval['name'],
						'pId'=>-$k,
						'open'=>false,
						'isParent'=>true,
				);
				$i++;
			}
			$k++;
		}
		return $typeTree;
	}
	
}
?>