<?php class MisHrInvitereSpecialFormModel extends CommonModel{
	//put your code here
	/**
	* 人事基础信息模型
	* @var unknown_type
	*/
	protected  $trueTableName = 'mis_hr_invitere_special_form';
    public $_auto	=array(
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
    		array('interviewpositiondate','dateToTimeString',self::MODEL_BOTH,'callback'),
			array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
    		);
	}
	?>