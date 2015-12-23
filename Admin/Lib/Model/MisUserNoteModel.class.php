<?php
//Version 1.0
/**
 * @Title: MisUserNoteModel
 * @Package package_name
 * @Description: todo(用户便签表)
 * @author 杨东
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-3-15 上午11:43:04
 * @version V1.0
 */
class MisUserNoteModel extends CommonModel{
	protected  $trueTableName = 'mis_user_note';
	public $_auto		=	array(
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
			array('uid','getMemberId',self::MODEL_INSERT,'callback'),
		   array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),

	);
}