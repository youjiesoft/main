<?php
/**
 * @Title: MisAutoEweModel
 * @Package package_name
 * @Description: todo(动态表单_自动生成-Model-扩展Modedl)
 * @author 管理员
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2015-07-20 14:55:22
 * @version V1.0
*/
class MisAutoEweExtendModel extends CommonModel {
	function getNodeArr(){
		$gModel = M("group");
		$nModel = M("node");
		$gmap['status'] = 1;
		$gList = $gModel->where($gmap)->order("id")->getField('id,name');
		$gkeys = array_keys($gList);
		$nmap['status'] = 1;
		$nmap['level'] = 3;
		$nmap['group_id'] = array("in",$gkeys);
		$nList = $nModel->where($nmap)->order("group_id")->getField("id,name,title");
		return $nList;
	}
}