<?php
class MisSystemDataAccessModelQuoteModel extends CommonModel{
	protected $trueTableName = 'mis_system_data_access_model_quote';
	public $_validate	=	array(
			array('actionname','','该模型已做过选择！',self::MUST_VALIDATE,'unique',self::MODEL_INSERT),//多字段组合验证
	);
	/**
	 * @Title: quoteActionTree
	 * @Description: todo(授权模块节点树)   
	 * @author 谢友志 
	 * @date 2015-6-23 下午3:11:32 
	 * @throws
	 */
	public function quoteActionTree($rel,$jump='jump'){
		//数据授权涉及到的模块
		$model = D("MisSystemDataAccess");
		$actionlist = $model->select();
		//授权模块数组
		$newactionlist = array();
		foreach($actionlist as $key=>$val){
			$newactionlist[$val['actionname']] = $val;
		}
		//模块节点树
		$nodeModel= D("node");
		$groupModel = D("group");
		//查询所有分组
		$gmap['status'] = 1;
		$groupList = $groupModel->where($gmap)->order("sorts asc")->getField("id,name");
		$groupKey = array_keys($groupList);
		//查询3级节点
		$nmap['level']=3;
		$nmap['status']=1;
		$nmap['group_id'] =array("in",array_keys($groupList));
		$nodelist = $nodeModel->where($nmap)->getField("name,id,pid,group_id");
		
		$nmap2['status']=1;
		$nmap2['group_id'] =array("in",array_keys($groupList));
		$nodelist2 = $nodeModel->where($nmap2)->getField("id,name,title,pid,group_id");
		$nodeKey = array_keys($nodelist);
		$nodeKey2 = array_keys($nodelist2);
		//组合树的3级节点，提取父id
		foreach($newactionlist as $key=>$val){
			if(in_array($key,$nodeKey)){
				$tree[] = array(
						'name'=>$val['actiontitle'],//显示名称
						'title'=>$val['actiontitle'],//显示名称
						'ename'=>$val['actionname'], //model名称
						'pId'=>"-100".$nodelist[$val['actionname']]['pid'],
						'url'=>"__URL__/index/jump/{$jump}/aname/".$val['actionname'],
						'target'=>'ajax',
						'rel'=>$rel,
						'open'=>false				
						
						);
				$nodePid[] = $nodelist[$val['actionname']]['pid'];
			}
		}
		$nodePid  = array_unique($nodePid);
		//组合树的2级节点，提取父id
		foreach($nodePid as $key=>$val){
			if(in_array($val,$nodeKey2)){
				$tree[] = array(
						'id'=>"-100".$val,
						'name'=>$nodelist2[$val]['title'],//显示名称
						'title'=>$nodelist2[$val]['title'], //model名称
						'pId'=>'-'.$nodelist2[$val]['group_id'],
						'open'=>false
						);
			}
			$groupPid[] = $nodelist2[$val]['group_id']; 
		}
		$groupPid = array_unique($groupPid);
		//组合1级节点
		foreach($groupPid as $key=>$val){
			if(in_array($val,$groupKey)){
				$tree[] = array(
						'id'=>"-".$val,
						'name'=>$groupList[$val],//显示名称
						'title'=>$groupList[$val], //model名称
						'pId'=>0,
						'open'=>false
				);
			}
		}
		return $tree;
	
	}
	
}