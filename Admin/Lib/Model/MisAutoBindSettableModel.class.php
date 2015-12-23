<?php
/**
 * 
 * @Title: MisAutoBindSettableModel 
 * @Package package_name
 * @Description: todo(套表model) 
 * @author renling 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014年12月13日 上午11:50:47 
 * @version V1.0
 */
class MisAutoBindSettableModel extends CommonModel{
    protected $trueTableName = 'mis_auto_bind';

    public $_auto	=array(
    		array('createid','getMemberId',self::MODEL_INSERT,'callback'),
    		array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
    		array('createtime','time',self::MODEL_INSERT,'function'),
    		array('updatetime','time',self::MODEL_UPDATE,'function'),
    	    array('companyid','getCompanyID',self::MODEL_INSERT,'callback'),
    		array('departmentid','getDeptID',self::MODEL_INSERT,'callback'),
    );
   /* public $_validate =array(
    	//array('actionname','','Action名称已存在！',1,'unique',3),
    	array('actionname','require','Action名称已存在！',Model::MUST_VALIDATE,'unique',1),
    );*/
	/*protected $_link=array(
		'DynamicForm'=>array(
			'mapping_type'=>BELONGS_TO,
			'class_name'    =>'DynamicForm',
			'mapping_name'	=> 'dynamicfieldarr',
			'foreign_key'=>'linkfield',
		),
	);*/
    /**
     * @Title: getBindtree
     * @Description: todo(这里用一句话描述这个方法的作用) 
     * @param unknown $rel
     * @return multitype:multitype:string NULL boolean  multitype:string number boolean  multitype:string boolean unknown  multitype:string number boolean unknown    
     * @author renling 
     * @date 2014年12月9日 下午5:46:36 
     * @throws
     */
    public function getBindtree($rel,$type){
		//组成左侧树形菜单
		$MisDynamicFormManageModel=D("MisDynamicFormManage");
		//节点模型
		$NodeModel=D('Node');
		//菜单模型
		$GropModel=D('Group');
		$mdMap=array();
		$mdMap['status']=1;
		//$mdMap['_string']="tpl='noaudittpl#ltrl' or tpl='noaudittpl#lcrt' or tpl='noaudittpl#list'";
		if($type){//排除组合表主表
			$MisAutoBindModel=D("MisAutoBind");
			$BindMap=array();
			$BindMap['status']=1;
			$BindMap['typeid']=array("not in",'2');
			$BindMap['pid']=0;
			$MisAutoBindList=$MisAutoBindModel->where($BindMap)->getField("id,bindformid");
			if($MisAutoBindList){
				$mdMap['id']=array("not in",array_values($MisAutoBindList));
			}
		}
		//查询动态表单记录表
		$MisDynamicFormManageList=$MisDynamicFormManageModel->where($mdMap)->field("id,actionname,actiontitle")->select();
		//查询node表3级节点
		$where = array();
		$where['level']=3;
		$where['status']=1;
		$nodevo = $NodeModel->where($where)->getField("name,id,pid");
		$pNodeList=array();
		$typeTree=array();
		//组合动态表单节点
		foreach ($MisDynamicFormManageList as $key=>$val){
			//节点表存在此节点
			if(in_array($val['actionname'], array_keys($nodevo))){
				$typeTree[]=array('id'=>$val['id'],
						'name'=>$val['actiontitle'],//显示名称
						'title'=>$val['actiontitle'],//显示名称
						'ename'=>$val['actionname'], //model名称
						'pId'=>"-100".$nodevo[$val['actionname']]['pid'],
						'url'=>"__URL__/index/jump/1/id/".$val['id']."/aname/".$val['actionname'],
						'target'=>'ajax',
						'rel'=>$rel,
						'open'=>true
				);
				$pNodeList[] = $nodevo[$val['actionname']]['pid'];
			}else{
				$typeTree[]=array('id'=>$val['id'],
						'name'=>$val['actiontitle'],//显示名称
						'title'=>$val['actiontitle'],//显示名称
						'ename'=>$val['actionname'], //model名称
						'pId'=>"-100001",
						'url'=>"__URL__/index/jump/1/id/".$val['id']."/aname/".$val['actionname'],
						'target'=>'ajax',
						'rel'=>$rel,
						'open'=>true
				);
			}
		}
		$pNodeList = array_unique($pNodeList);
		$nodeList = array();
		if(count($pNodeList)>0){
			$NodeMap['id']=array("in",$pNodeList);
			$NodeMap['status']=1;
			$nodeList=$NodeModel->where($NodeMap)->getField("id,name,title,group_id");
		}
		$groupList=array();
		//组合父节点
		foreach ($pNodeList as $k => $v) {
			if($nodeList[$v] ) {
				$typeTree[] = array(
						'id'=>"-100".$v,
						'name'=>$nodeList[$v]['title'],//显示名称
						'title'=>$nodeList[$v]['name'], //model名称
						'pId'=>'-'.$nodeList[$v]['group_id'],
						'open'=>true
				);
				$groupList[]=$nodeList[$v]['group_id'];
			}
		}
		$groupList=array_unique($groupList);
		$GroupMap['id']=array("in",$groupList);
		$GroupMap['status']=1;
		$groupList=$GropModel->where($GroupMap)->order("sorts asc")->getField("id,name");
		//组合菜单节点
		foreach ($groupList as $gkey=>$gval){
			if($groupList[$gkey]) {
				$typeTree[] = array(
						'id'=>"-".$gkey,
						'name'=>$gval,//显示名称
						'title'=>$gval, //model名称
						'pId'=>0,
						'open'=>true
				);
			}
		}
		$typeTree[]=array(
				'id'=>'-100001',
				'name'=>"未指定节点",//显示名称
				'title'=>"未指定节点", //model名称
				'pId'=>0,
				'open'=>true
		);
		return $typeTree;
	}
}
?>
