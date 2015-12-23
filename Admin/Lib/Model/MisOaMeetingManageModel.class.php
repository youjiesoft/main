<?php
/** 
 * @Title: MisOaMeetingManageModel 
 * @Package package_name
 * @Description: todo(会议管理模型) 
 * @author 杨东 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-9-24 上午11:00:41 
 * @version V1.0 
*/ 
class MisOaMeetingManageModel extends CommonModel {
	protected $trueTableName = 'mis_oa_meeting_manage';
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
	public $_validate	=	array(
			array('endtime','dataCompare',"会议结束日期应晚于开始日期",self::VALUE_VAILIDATE,'callback',self::MODEL_BOTH,
					array('$_POST[starttime]')
			),
	);
}
?>