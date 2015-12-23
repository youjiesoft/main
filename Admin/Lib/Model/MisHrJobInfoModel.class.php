<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(人事岗位模型) 
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-4-13 下午4:51:46 
 * @version V1.0
 */
class MisHrJobInfoModel extends CommonModel {
	protected  $trueTableName = 'mis_hr_job_info';
	public $_auto=array(
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),			
	);
}
?>