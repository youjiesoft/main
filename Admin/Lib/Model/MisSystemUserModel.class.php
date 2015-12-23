<?php
//Version 1.0
/**
 * Description of MisSystemUserModel
 *
 * @author liudianpin
 */
class MisSystemUserModel extends CommonModel{
    protected $trueTableName = 'mis_system_user';

    public $_auto	=array(
    		array('createid','getMemberId',self::MODEL_INSERT,'callback'),
    		array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
    		array('createtime','time',self::MODEL_INSERT,'function'),
    		array('updatetime','time',self::MODEL_UPDATE,'function'),
    	   array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),

    );
    
    public $_validate	=	array(
		array('manname,status','','此用户已经存在！',self::MUST_VALIDATE,'unique',self::MODEL_BOTH),
	);

}
?>