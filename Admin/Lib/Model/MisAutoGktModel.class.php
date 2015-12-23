<?php
/**
 * @Title: MisAutoGktModel
 * @Package package_name
 * @Description: todo(动态表单_自动生成-流程节点管理)
 * @author 管理员
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-10-20 15:52:25
 * @version V1.0
*/
class MisAutoGktModel extends CommonModel {
	protected $trueTableName = 'mis_auto_hcfwc';
	public $_auto =array(
		array('begintime','strtotime',self::MODEL_BOTH,'function'),
		array('endtime','strtotime',self::MODEL_BOTH,'function'),
		array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
		array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
		array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
	);
}
?>