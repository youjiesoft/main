<?php
/**
 * @Title: MisAddressRecordModel
 * @Package package_name
 * @Description: todo(地区信息处理Model)
 * @author quqiang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-10-29 下午07:44:54
 * @version V1.0
 */
class MisAddressRecordModel extends CommonModel{
	protected $trueTableName = 'mis_address_record';

	public $_auto	=array(
	array('createid','getMemberId',self::MODEL_INSERT,'callback'),
	array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
	array('createtime','time',self::MODEL_INSERT,'function'),
	array('updatetime','time',self::MODEL_UPDATE,'function'),
    array('companyid','getCompanyID',self::MODEL_INSERT,'callback'),
	array('departmentid','getDeptID',self::MODEL_INSERT,'callback'),
	array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
	);
}