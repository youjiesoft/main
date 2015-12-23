<?php
class MisDynamicFormFieldModel extends CommonModel{
    protected $trueTableName = 'mis_dynamic_form_field';

    public $_auto	=array(
    		array('createid','getMemberId',self::MODEL_INSERT,'callback'),
    		array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
    		array('createtime','time',self::MODEL_INSERT,'function'),
    		array('updatetime','time',self::MODEL_UPDATE,'function'),
			array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
    );
   /* public $_validate =array(
    	//array('actionname','','Action名称已存在！',1,'unique',3),
    	array('actionname','require','Action名称已存在！',Model::MUST_VALIDATE,'unique',1),
    );*/
    public $_validate = array(
			array('name','','类型名称已存在',self::EXISTS_VAILIDATE,'unique',self::MODEL_BOTH),
	);
	/*protected $_link=array(
		'DynamicForm'=>array(
			'mapping_type'=>BELONGS_TO,
			'class_name'    =>'DynamicForm',
			'mapping_name'	=> 'dynamicfieldarr',
			'foreign_key'=>'linkfield',
		),
	);*/
}
?>