<?php
/**
 +----------------------------------------------------------
 * 人力资源管理-绩效等级管理
 +----------------------------------------------------------
 * @author renling
 * @date:2013-8-1
 */
class MisPerformanceLevelModel extends CommonModel {
	protected $trueTableName = 'mis_performance_level';//实例化数据库模型对象
	public $_auto	=array(
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