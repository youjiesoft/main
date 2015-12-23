<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(用户模型，整体授权部分的) 
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-6-18 下午5:27:13 
 * @version V1.0
 */
class MisAttachedTemplateModel extends CommonModel {
	protected $trueTableName = 'mis_system_attached_template_mas';
	
	public $_validate	=	array(
	);

	public $_auto		=	array(
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
		    array('companyid','getCompanyID',self::MODEL_INSERT,'callback'),
			array('departmentid','getDeptID',self::MODEL_INSERT,'callback'),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
	);
}
?>