<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(工作计划model) 
 * @author libo 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-7-16 上午10:13:13 
 * @version V1.0
 */
class MisWorkPlanModel extends CommonModel{
	protected $trueTableName = 'mis_work_plan';
	public $_auto =array(
			array("createid","getMemberId",self::MODEL_INSERT,"callback"),
			array("createtime","time",self::MODEL_INSERT,"function"),
			array("updateid","getMemberId",self::MODEL_UPDATE,"callback"),
			array("updatetime","time",self::MODEL_UPDATE,"function"),
			array('plantime','dateToTimeString',self::MODEL_BOTH,'callback'),
		   array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),

	);
}

?>