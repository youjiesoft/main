<?php 
/**
 * @Title: MisWorkFacilityApplyMasModel 
 * @Package package_name
 * @Description: todo(办公设备申请) 
 * @author xiafengqin 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-9-18 下午3:59:55 
 * @version V1.0
 */
class MisWorkFacilityApplySubModel extends CommonModel{
    protected  $trueTableName = 'mis_work_facility_apply_sub';
    public $_auto	=array(
    		array('createtime','time',self::MODEL_INSERT,'function'),
            array('updatetime','time',self::MODEL_UPDATE,'function'),
    		array('createid','getMemberId',self::MODEL_INSERT,'callback'),
    		array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
    	   array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),

    );
}
?>