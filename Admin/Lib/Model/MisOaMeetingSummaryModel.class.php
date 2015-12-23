<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(会议纪要) 
 * @author libo 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-7-23 下午2:09:38 
 * @version V1.0
 */
class MisOaMeetingSummaryModel extends CommonModel{
	protected $trueTableName = 'mis_oa_meeting_summary';
	public $_auto	=array(
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
			array('starttime','dateToTimeString',self::MODEL_BOTH,'callback'),
			array('endtime','dateToTimeString',self::MODEL_BOTH,'callback'),
			array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
	);
}


?>