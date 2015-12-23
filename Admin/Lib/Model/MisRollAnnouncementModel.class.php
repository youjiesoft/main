<?php
/**
 * @Title: MisRollAnnouncement 
 * @Package package_name 
 * @Description: todo(滚动公告model) 
 * @author libo 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-1-13 下午2:31:00 
 * @version V1.0
 */
class MisRollAnnouncementModel extends CommonModel{
	protected $trueTableName = 'mis_roll_announcement';
	
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