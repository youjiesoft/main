<?php
/**
 * @Title: MisSystemAnnouncementModel
 * @Package 工共应用-系统公告：模型类
 * @Description: TODO(系统公告的发布)
 * @author yangxi
 * @company 重庆特米洛科技有限公司˾
 * @copyright 重庆特米洛科技有限公司˾
 * @date 2013-3-14 19:18:54
 * @version V1.0
 */
class MisSystemAnnouncementModel extends CommonModel {
	protected $trueTableName = 'mis_system_announcement';
// 	public $_validate	=	array(
// 			array('endtime','dataCompare',"结束时间晚于开始时间",self::VALUE_VAILIDATE,'callback',self::MODEL_BOTH,
// 					array('$_POST[starttime]')
// 			),
// 	);
	public $_auto	=array(
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
			array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
// 			array('toptime','getStrTimeTip',self::MODEL_UPDATE,'callback'),
// 			array('starttime','dateToTimeString',self::MODEL_UPDATE,'callback'),
// 			array('endtime','getStrTimeTip',self::MODEL_UPDATE,'callback'),
	);
	
// 	public function getStrTimeTip($date){
// 		return strtotime($date)+24*3600-1;
// 	}
	
}
?>