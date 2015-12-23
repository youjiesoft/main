<?php
class MisSystemPrintModel extends CommonModel {
	protected $trueTableName = 'mis_system_print';
	public $_validate=array(
		array('name','','打印模块名称已经存在',self::EXISTS_VAILIDATE,'unique',self::MODEL_BOTH),
		array('name','require','打印模块名称必须'),
	);
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