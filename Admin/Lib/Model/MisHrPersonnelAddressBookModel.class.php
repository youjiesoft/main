<?php
class MisHrPersonnelAddressBookModel extends CommonModel {

    protected  $trueTableName = 'mis_hr_personnel_person_info';
    
    public $_validate	=	array(
    		array('transferdate','dataCompare',"转正日期晚于入职日期",self::VALUE_VAILIDATE,'callback',self::MODEL_BOTH,
    				array('$_POST[indate]')
    		),
    );
    public $_auto	=array(
    		array('createtime','time',self::MODEL_INSERT,'function'),
            array('updatetime','time',self::MODEL_UPDATE,'function'),
    		array('createid','getMemberId',self::MODEL_INSERT,'callback'),
    		array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
    		array('birthday','dateToTimeString',self::MODEL_BOTH,'callback'),
    		array('indate','dateToTimeString',self::MODEL_BOTH,'callback'),//入职时间
    		array('transferprobationdate','dateToTimeString',self::MODEL_BOTH,'callback'),//转试用时间
    		array('factindate','dateToTimeString',self::MODEL_BOTH,'callback'),//实际入职时间
    		array('contractdate','dateToTimeString',self::MODEL_BOTH,'callback'),//签约时间
    		array('expirationdate','dateToTimeString',self::MODEL_BOTH,'callback'),//合同到期时间
    		array('transferdate','dateToTimeString',self::MODEL_BOTH,'callback'),//转正日期
    		array('leavedate','dateToTimeString',self::MODEL_BOTH,'callback'),//转正日期
			array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),

    );

}
?>