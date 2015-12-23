<?php
//自定义表单模块
class MisSystemExtendFieldModel extends CommonModel {
        protected $trueTableName = 'mis_typeform_field';

        public $_auto	=array(
        		array('createid','getMemberId',self::MODEL_INSERT,'callback'),
        		array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
        		array('createtime','time',self::MODEL_INSERT,'function'),
        		array('updatetime','time',self::MODEL_UPDATE,'function'),
				array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
				array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
				array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
        );
        
	protected $_validate	=array(
		array('modelname,fieldtypeid,rename,status','','改字段已经存在',self::MUST_VALIDATE,'unique',self::MODEL_BOTH),
	);
}
?>