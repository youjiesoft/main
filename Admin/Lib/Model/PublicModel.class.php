<?php
class PublicModel extends Model{
	/**
	 * @Title: metulefttree
	 * @Description: todo(菜单分组下的左侧2-3级子菜单数组)   
	 * @author 谢友志 
	 * @date 2014-12-1 上午11:57:42 
	 * @throws
	 */
	function menuLeftTree($groupid){
		$mode4 = M("node");
		$where['group_id'] = $groupid;
		$where['status'] =  1;
		$where['showmenu'] = 1; //模板显示
		$where['level'] =array("neq",4); //除操作节点外
		$nodelist = $mode4->field("id,title,pid,name,level")->where($where)->order('sort asc')->select();
		//获取系统授权模板
		$model_syspwd=D('SerialNumber');
		$modules_sys=$model_syspwd->checkModule();
		$m_list = explode(",",$modules_sys);
		//获取当前用户授权
		$access = getAuthAccess();
		//存储授权的数组信息
		$accessNode = array();
		foreach($nodelist as $key=>$val){
			if($val['level'] == 2){
				$levelthreeNode = array();
				foreach($nodelist as $key1=>$val1 ){
					if($val1['level'] == 3 && $val['id'] == $val1['pid']){
						//校验系统模块授权   如果是以MisDynamic开头的控制器，一律不显示在菜单上面
						if(substr($val1['name'], 0, 10)!="MisDynamic"){
							if(!in_array($val1['name'], $m_list))  continue;
						}
						//显示有权限的模块
						if (isset ($access[strtoupper( APP_NAME )][strtoupper ($val1 ['name'])]["INDEX"]) || $_SESSION [C('ADMIN_AUTH_KEY')]) {
							$levelthreeNode[] = $val1;
						}
					}
				}
				//判断当前是否存在3级控制器，否组2级面板将不显示出来
				if($levelthreeNode){
					$val['levelthree'] = $levelthreeNode;
					$accessNode[$key] = $val;
				}
			}
		}
		return $accessNode;
	}
}