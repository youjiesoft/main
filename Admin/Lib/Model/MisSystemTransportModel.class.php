<?php
/**
 * @Title: MisSystemTransportModel
 * @Package package_name
 * @Description: todo(运输方式模型)
 * @author mashihe
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2011-9-8 下午3:23:53
 * @version V1.0
 */
class MisSystemTransportModel extends CommonModel{
	protected $trueTableName = 'mis_system_transport';

	public $_auto	=array(
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
		    array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),

	);

	public $_validate	=	array(
			array('code,status','','数据有重复，请检查！',self::MUST_VALIDATE,'unique',self::MODEL_BOTH),//多字段组合验证
	);
}
?>