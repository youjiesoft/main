<?php
class MisHrEgressRegisterModel extends CommonModel {
	protected $trueTableName = 'mis_hr_egress_register';
	public $_auto =array(
		array("createid","getMemberId",self::MODEL_INSERT,"callback"),
		array("createid","getMemberId",self::MODEL_UPDATE,"callback"),
		array("createtime","time",self::MODEL_INSERT,"function"),
		array("updatetime","time",self::MODEL_UPDATE,"function"),
		array('starttime','strtotime',self::MODEL_BOTH,'function'),
		array('endtime','strtotime',self::MODEL_BOTH,'function'),
		array('returntime','strtotime',self::MODEL_BOTH,'function'),
		array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
		array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
		array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
	);
	public $_validate=array(
		array('starttime','require','外出起始时间必须'),
		array('endtime','require','外出结束时间必须'),
		array('returntime','require','回岗时间必须'),
	);
}
?>