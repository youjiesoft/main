<?php
/**
 * @Title: CollaborationApplicationFormModel
 * @Package package_name
 * @Description: todo(动态表单_自动生成-非计划类品牌工作协作申请表)
 * @author 管理员
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-08-25 15:05:50
 * @version V1.0
*/
class CollaborationApplicationFormModel extends CommonModel {
	protected $trueTableName = 'collaboration_application_form';
	public $_auto =array(
		array('applytime','strtotime',self::MODEL_BOTH,'function'),
		array('applyenttime','strtotime',self::MODEL_BOTH,'function'),
	    array('companyid','getCompanyID',self::MODEL_INSERT,'callback'),
		array('departmentid','getDeptID',self::MODEL_INSERT,'callback'),
	);
}
?>