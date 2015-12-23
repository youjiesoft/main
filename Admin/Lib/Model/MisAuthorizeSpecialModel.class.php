<?php
//Version 1.0
// 特殊权限模型
class MisAuthorizeSpecialModel extends CommonModel {
	protected $tableName = 'mis_authorize_special';
	public $_validate = array(
			array('name,status','require','名称必须'),
	);
	public $_auto	=array(
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
		    array('companyid','getCompanyID',self::MODEL_INSERT,'callback'),
			array('departmentid','getDeptID',self::MODEL_INSERT,'callback'),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
	);
}
?>