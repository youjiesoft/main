<?php
//Version 1.0
class MisImportExcelModel extends CommonModel {
	 protected $trueTableName = 'mis_import_excel';
	 public $_validate	=	array(
		array('name,tableobj,status','','该名称与数据对象组合已存在,请切换到[添加已有节点]中查找添加！',self::MUST_VALIDATE,'unique',self::MODEL_BOTH),
		array('tableobj，status','','该数据对象已存在,请切换到[添加已有节点]中查找添加！',self::VALUE_VAILIDATE,'unique',self::MODEL_BOTH),
		array('pid,tableobj,status','','改节点下有此数据表对象，请检查！',self::MUST_VALIDATE,'unique',self::MODEL_BOTH),
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