<?php
/**
 * @Title: file_name
 * @Package package_name
 * @Description: todo(党员信息模型)
 * @author chenxijun
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-3-27 上午11:23:22
 * @version V1.0
 */
class MisHrPartOrganizationModel extends CommonModel {
	protected $trueTableName = 'mis_hr_part_organization';
	public $_validate=array(
			array('employe_id','require','员工ID必须'),
	);
	//自动填充
	public $_auto	=array(
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
			array('out_time','dateToTimeString',self::MODEL_BOTH,'callback'),
			array('ln_time','dateToTimeString',self::MODEL_BOTH,'callback'),
			array('approve_time','dateToTimeString',self::MODEL_BOTH,'callback'),
			array('apply_time','dateToTimeString',self::MODEL_BOTH,'callback'),
			array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
	);
}
?>