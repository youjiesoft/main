<?php
/** 
 * @Title: MisPerformancePlanDetailModel 
 * @Package package_name
 * @Description: todo(用一句话描述该类的作用) 
 * @author 杨东 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-8-6 下午5:24:48 
 * @version V1.0 
*/ 
class MisPerformancePlanDetailModel extends CommonModel {
	protected $trueTableName = 'mis_performance_plan_detail';

	public $_auto		=	array(
		array('createid','getMemberId',self::MODEL_INSERT,'callback'),
		array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
		array('createtime','time',self::MODEL_INSERT,'function'),
		array('updatetime','time',self::MODEL_UPDATE,'function'),
		array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
		array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
		array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
	);
}
?>