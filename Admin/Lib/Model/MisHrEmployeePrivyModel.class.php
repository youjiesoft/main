<?php class MisHrEmployeePrivyModel extends CommonModel{
	//put your code here
	/**
	* 人事关系模型
	* @var unknown_type
	*/
	protected  $trueTableName = 'mis_hr_employee_privy ';
	public $_auto	=array(
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
		    array('companyid','getCompanyID',self::MODEL_INSERT,'callback'),
			array('departmentid','getDeptID',self::MODEL_INSERT,'callback'),
	);
	/* protected function insertStrtotime($time) {
	 if(isset($time)) {
	return strtotime($time);
	}else{
	return false;
	}
	} */
}
?>
