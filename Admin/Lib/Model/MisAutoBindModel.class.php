<?php
class MisAutoBindModel extends CommonModel{
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
    public function getBindtree($rel){
    	//绑定关系表
    	$MisAutoBindModel=D("MisAutoBind");
    	//节点模型
    	$NodeModel=D('Node');
    	//菜单模型
    	$GropModel=D('Group');
    	//查询动态表单记录表
    	$MisAutoBindList=$this->where("status=1")->select();
    	$MisAutoBindnewList=array();
    	$anameList=array();
      //dump(array_unique($MisAutoBindList));
      	//循环踢出重复的actionname
      	foreach ($MisAutoBindList as $akey=>$aval){
      		if(in_array($aval['bindaname'], array_keys($anameList))){
      			 //存在不做任何操作
      		}else{
      			//不存在则加入新list
      			if($aval['bindaname']){
	      			$anameList[$aval['bindaname']]=1;
	      			$MisAutoBindnewList[]=$aval; 
      			}
      		}
      	}
    	$pNodeList=array();
    	$typeTree=array();
    	//组合动态表单节点
    	foreach ($MisAutoBindnewList as $key=>$val){
    		$where = array();
    		$where['name'] = $val['bindaname'];
    		$where['status']=1;
    		$nodevo = $NodeModel->where($where)->field("id,pid")->find();
    		//节点表存在此节点
    		if($nodevo['id']){
    			$typeTree[]=array('id'=>$val['id'],
    					'name'=>getFieldBy($val['bindaname'], "actionname", "actiontitle", "mis_dynamic_form_manage"),//显示名称
    					'title'=>$val['bindaname'],//显示名称
    					'ename'=>$val['bindaname'], //model名称
    					'pId'=>"-100".$nodevo['pid'],
    					'url'=>"__URL__/index/jump/1/id/".$val['id']."/aname/".$val['bindaname'],
    					'target'=>'ajax',
    					'rel'=>$rel,
    					'open'=>true
    			);
    			$pNodeList[] = $nodevo['pid'];
    		}else{
    			$typeTree[]=array('id'=>$val['id'],
    					'name'=>getFieldBy($val['bindaname'], "actionname", "actiontitle", "mis_dynamic_form_manage"),//显示名称
    					'title'=>$val['bindaname'],//显示名称
    					'ename'=>$val['bindaname'], //model名称
    					'pId'=>"-100001",
    					'url'=>"__URL__/index/jump/1/id/".$val['id']."/aname/".$val['bindaname'],
    					'target'=>'ajax',
    					'rel'=>$rel,
    					'open'=>true
    			);
    		}
    	}
    	$pNodeList = array_unique($pNodeList);
    	$NodeMap['id']=array("in",$pNodeList);
    	$NodeMap['status']=1;
    	$nodeList=$NodeModel->where($NodeMap)->getField("id,name,title,group_id");
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
    	$groupList=$GropModel->where($GroupMap)->getField("id,name");
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
   /**
     * @Title: getBindVo
     * @Description: todo(获取组合表单绑定值) 
     * @param unknown $rel
     * @return multitype:multitype:string NULL boolean  multitype:string number boolean  multitype:string boolean unknown  multitype:string number boolean unknown    
     * @author renling 
     * @date 2014年12月9日 下午5:46:36 
     * @throws
     */
    public function getBindVo($actionname,$id){
    	$orderno=getFieldBy($id, "id", "orderno",$actionname);
    	$bindList=array();
    	if($orderno){
    		$MisAutoBindModel=D("MisAutoBind");
    		// 查询符合条件的表单
    		$MisAutoBindVo=$MisAutoBindModel->where("status=1 and bindaname='{$actionname}'  and bindresult<>'' and typeid=0")->field("bindresult")->find();
    		// 过滤掉可能的错误。
    		$bindCondition = getFieldBy($orderno,"orderno",$MisAutoBindVo['bindresult'],$actionname);
    		if(isset($bindCondition)){
    			$bindMap['_string']="bindval={$bindCondition}";
    		}
    		$bindMap['status'] = 1;
    		$bindMap['bindaname'] = $actionname;
    		$MisAutoBindSettableVo = $MisAutoBindModel->where($bindMap)->order("inbindsort asc")->find();
    		if($MisAutoBindSettableVo){
	    		$map = array();
	    		$map['status'] = 1;
	    		$map['bindid'] = $orderno;
	    		$map['relationmodelname'] = $actionname;
	    		$model = D($MisAutoBindSettableVo['inbindaname']);
	    		$bindVo = $model->where($map)->find();
	    		$bindList['modelname']=$MisAutoBindSettableVo['inbindaname'];
	    		$bindList['tableid']=$bindVo['id'];
    		}
    	}
    	return $bindList;
    }
}
?>