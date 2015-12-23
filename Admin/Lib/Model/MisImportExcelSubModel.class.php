<?php
//Version 1.0
class MisImportExcelSubModel extends CommonModel {
	 protected $trueTableName = 'mis_import_excel_sub';

	 public $_auto	=array(
	 		array('createid','getMemberId',self::MODEL_INSERT,'callback'),
	 		array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
	 		array('createtime','time',self::MODEL_INSERT,'function'),
	 		array('updatetime','time',self::MODEL_UPDATE,'function'),
	 	    array('companyid','getCompanyID',self::MODEL_INSERT,'callback'),
	 		array('departmentid','getDeptID',self::MODEL_INSERT,'callback'),
	 );
	 
	 public $_validate	=	array(
	 	array("name","require","导入数据列名称不能为空!"),
		array('eid,name','','该名称已经存在！',self::MUST_VALIDATE,'unique',self::MODEL_BOTH),
	 );
}
?>