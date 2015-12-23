<?php
/**
 * @Title: MisSystemFlowInventoryTypeAction
 * @Package package_name
 * @Description: todo(表单流程类型)
 * @author liminggang
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2014-8-16 上午11:03:19
 * @version V1.0
 */
class MisSystemFlowInventoryTypeAction extends CommonAction {
	public function _filter(&$map){
		if ($_SESSION["a"] != 1){
			$map['status']=array("gt",-1);
		}
	}
}
?>