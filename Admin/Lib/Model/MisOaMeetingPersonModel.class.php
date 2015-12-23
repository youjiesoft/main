<?php
/** 
 * @Title: MisOaMeetingPersonModel 
 * @Package package_name
 * @Description: todo(会议管理参会人员模型) 
 * @author 杨东 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-9-24 上午11:00:41 
 * @version V1.0 
*/ 
class MisOaMeetingPersonModel extends CommonModel {
	protected $trueTableName = 'mis_oa_meeting_person';
	public $_auto = array(
		array('createid','getMemberId',self::MODEL_INSERT,'callback'),
		array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
		array('createtime','time',self::MODEL_INSERT,'function'),
		array('updatetime','time',self::MODEL_UPDATE,'function'),
		array('starttime','dateToTimeString',self::MODEL_BOTH,'callback'),
		array('endtime','dateToTimeString',self::MODEL_BOTH,'callback'),
	    array('companyid','getCompanyID',self::MODEL_INSERT,'callback'),
		array('departmentid','getDeptID',self::MODEL_INSERT,'callback'),
	);
}
?>