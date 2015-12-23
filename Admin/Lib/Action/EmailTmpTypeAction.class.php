<?php
//Version 1.0
/**
 * @Title: EmailTmpTypeAction 
 * @Package package_name
 * @Description: todo(邮件模板分类) 
 * @author laicaixia
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-6-2 上午10:28:20 
 * @version V1.0 
*/ 
class EmailTmpTypeAction extends CommonAction {
	/**
	 * @Title: _filter
	 * @Description: todo(检索)
	 * @param 检索条件 $map
	 * @author laicaixia
	 * @date 2013-6-2 上午10:23:42
	 * @throws
	 */
	public function _filter(&$map){
		if ($_SESSION["a"] != 1)$map['status']=array("gt",-1);
	}
}
?>