<?php
/**
 * @Title: MisPerformanceKpiModel 
 * @Package package_name
 * @Description: todo(绩效指标管理) 
 * @author laicaixia
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-8-1 下午3:19:28 
 * @version V1.0
 */

class MisPerformanceKpiModel extends CommonModel {
	protected $trueTableName = 'mis_performance_kpi';

	public $_auto =	array(
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