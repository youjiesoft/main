<?php
/**
 * @Title: MisPerformancePlanModel 
 * @Package package_name
 * @Description: todo(绩效计划) 
 * @author laicaixia
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-8-2 上午11:25:38 
 * @version V1.0
 */



class MisPerformancePlanModel extends CommonModel {
	protected $trueTableName = 'mis_performance_plan';

	public $_auto		=	array(
		array('createid','getMemberId',self::MODEL_INSERT,'callback'),
		array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
		array('createtime','time',self::MODEL_INSERT,'function'),
		array('updatetime','time',self::MODEL_UPDATE,'function'),
		array('salarydate','dateToTimeString',self::MODEL_BOTH,'callback'),
		array('setdate','dateToTimeString',self::MODEL_BOTH,'callback'),
		array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
		array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
		array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
	);
}
?>