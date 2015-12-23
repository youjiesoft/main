<?php
/**
 * @Title: MisSystemProjectTempleteLabelModel
 * @Package package_name
 * @Description: todo(项目标签管理)
 * @author 管理员
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015-01-27 10:48:48
 * @version V1.0
*/
class MisSystemProjectTempleteLabelModel extends CommonModel {
	protected $trueTableName = 'mis_system_project_templete_label';
	public $_auto =array(
		array("createid","getMemberId",self::MODEL_INSERT,"callback"),
		array("updateid","getMemberId",self::MODEL_UPDATE,"callback"),
		array("createtime","time",self::MODEL_INSERT,"function"),
		array("updatetime","time",self::MODEL_UPDATE,"function"),
		array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
		array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
		array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
	);
	public $_validate=array(
		//array('name,modelname,modelid','','名称已经存在',self::MUST_VALIDATE,'unique',self::MODEL_BOTH),
	);
}
?>