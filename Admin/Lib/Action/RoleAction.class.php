<?php
/**
 * @Title: RoleAction 
 * @Package package_name
 * @Description: todo(权限管理) 
 * @author laicaixia
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-5-31 下午6:04:23 
 * @version V1.0
 */
class RoleAction extends CommonAction {
	/**
	 +----------------------------------------------------------
	 * 检索
	 * 赖彩霞
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return void
	 +----------------------------------------------------------
	 * @throws FcsException
	 +----------------------------------------------------------
	 */
	public function _filter(& $map) {
		if ($_SESSION["a"] != 1) $map['status']=array("gt",-1);
		if (!isset ( $_REQUEST ['orderField'] )) {
			 $_REQUEST ['orderField'] = "nodepid` ASC,`nodeid";
		} 
	}
}
?>