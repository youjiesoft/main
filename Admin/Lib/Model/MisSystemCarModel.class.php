<?php
/**
 * @Title: file_name
 * @Package package_name
 * @Description: todo(车辆基本信息管理)
 * @author liminggang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-10-17 下午2:18:03
 * @version V1.0
 */
class MisSystemCarModel extends CommonModel {
	protected $trueTableName = 'mis_system_car';
	
	public $_auto =array(
			array("createid","getMemberId",self::MODEL_INSERT,"callback"),
			array("createid","getMemberId",self::MODEL_UPDATE,"callback"),
			array("createtime","time",self::MODEL_INSERT,"function"),
			array("updatetime","time",self::MODEL_UPDATE,"function"),
			
			array("oilAmount","numberToReplace",self::MODEL_BOTH,"callback"),
			array("oilBalance","numberToReplace",self::MODEL_BOTH,"callback"),
			array("totalKM","numberToReplace",self::MODEL_BOTH,"callback"),
			
			
			array("transportreleasedate","dateToTimeString",self::MODEL_BOTH,"callback"),
			array("drivingregistrationdate","dateToTimeString",self::MODEL_BOTH,"callback"),
			array("drivingreleasedate","dateToTimeString",self::MODEL_BOTH,"callback"),
			array("madedate","dateToTimeString",self::MODEL_BOTH,"callback"),
			array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
		);
	
 
}
?>