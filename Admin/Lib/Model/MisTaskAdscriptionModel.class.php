<?php
class MisTaskAdscriptionModel extends CommonModel {
    protected $trueTableName = 'mis_task_information';
    
    public $_auto = array(
	array('createid','getMemberId',self::MODEL_INSERT,'callback'),
	array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
	array('createtime','time',self::MODEL_INSERT,'function'),
	array('updatetime','time',self::MODEL_UPDATE,'function'),
	array('begindate','dateToTimeString',self::MODEL_BOTH,'callback'),
	array('enddate','dateToTimeString',self::MODEL_BOTH,'callback'),
	array('realityknowdate','dateToTimeString',self::MODEL_BOTH,'callback'),
	array('realitybegindate','dateToTimeString',self::MODEL_BOTH,'callback'),
	array('realityenddate','dateToTimeString',self::MODEL_BOTH,'callback'),
    array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),

    );
}
?>