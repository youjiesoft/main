<?php
/**
 * @Title: file_name
 * @Package package_name
 * @Description: todo(表单流程类型)
 * @author liminggang
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2014-8-16 上午11:03:19
 * @version V1.0
 */
class MisSystemFlowTypeAction extends CommonAction {
	public function _filter(&$map){
		$map['outlinelevel'] = 1; //业务类型
		if ($_SESSION["a"] != 1){
			$map['status']=array("gt",-1);
			//当前登录人公司
			//$map ['_string'] = 'FIND_IN_SET(  ' . $_SESSION ["companyid"] . ',cmpid )';
		}
	}
}
?>