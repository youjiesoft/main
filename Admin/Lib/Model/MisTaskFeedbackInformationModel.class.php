<?php
//任务反馈信息
class MisTaskFeedbackInformationModel extends CommonModel{
    protected $trueTableName = 'mis_task_feedback_information';
    public $_auto =array(
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