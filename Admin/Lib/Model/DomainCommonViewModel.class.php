<?php
//Version 1.0
/**
+----------------------------------------------------------
* 通用公共型维度视图
+----------------------------------------------------------
* @author wangcheng
* @date:2012-04-7
*/
class DomainCommonViewModel extends ViewModel{
	public $viewFields = array();

	public $_auto	=array(
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
		    array('companyid','getCompanyID',self::MODEL_INSERT,'callback'),
			array('departmentid','getDeptID',self::MODEL_INSERT,'callback'),
	);
	
}
?>