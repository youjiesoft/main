<?php
class MisWorkFacilityTypeModel extends CommonModel {
	protected $trueTableName = 'mis_work_facility_type';
	
		public $_auto =array(
		
		array("createid","getMemberId",self::MODEL_INSERT,"callback"),
		
		array("createid","getMemberId",self::MODEL_UPDATE,"callback"),
		
		array("createtime","time",self::MODEL_INSERT,"function"),
		
		array("updatetime","time",self::MODEL_UPDATE,"function"),
	   array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),

	);
	public $_validate=array(
		array('orderno,status','','编码重复,请检查！',self::MUST_VALIDATE,'unique',self::MODEL_BOTH),//多字段组合验证
		array('orderno','require','编码必须'),
	);
}
?>