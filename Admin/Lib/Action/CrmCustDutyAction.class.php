<?php
/**
 * @Title: CrmCustDutyAction
 * @Package 客户相关-客户组织内职务信息：功能类
 * @Description: TODO(客户组织内职务的记录及维护)
 * @author yangxi
 * @company 重庆特米洛科技有限公司˾
 * @copyright 重庆特米洛科技有限公司˾
 * @date 2013-1-10 19:18:54
 * @version V1.0
 */
class CrmCustDutyAction extends CommonAction{
	/**
	 * @Title: _filter
	 * @Description: todo(重写CommonAction的_filter方法，传递过滤参数后返回列表页面)
	 * @return string
	 * @author yangxi
	 * @date 2013-5-31 下午3:59:44
	 * @throws
	 */
	public function _filter(& $map) {
	 if ($_SESSION["a"] != 1)$map['status']=array("gt",-1);
	}
}
?>