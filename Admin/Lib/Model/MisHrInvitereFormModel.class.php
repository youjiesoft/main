<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(人事招聘信息模型) 
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-3-27 上午11:42:15 
 * @version V1.0
 */	
class MisHrInvitereFormModel extends CommonModel {
	protected $trueTableName = 'mis_hr_invitere_form';
	
	public $_auto	=array(
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('datetime','dateToTimeString',self::MODEL_BOTH,'callback'),
			array('interviewtime','dateToTimeString',self::MODEL_BOTH,'callback'),
			array('joinedtime','dateToTimeString',self::MODEL_BOTH,'callback'),
			array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
	);
	public $_validate=array(
		array('name','require','姓名必须'),
	);
}
?>