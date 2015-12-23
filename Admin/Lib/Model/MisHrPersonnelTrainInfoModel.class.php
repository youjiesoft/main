<?php 

/** 
 * @Title: MisHrPersonnelTrainInfoModel 
 * @Package package_name
 * @Description: todo(员工调职模型) 
 * @author 杨东 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-7-22 上午10:27:28 
 * @version V1.0 
*/ 
class MisHrPersonnelTrainInfoModel extends CommonModel{
    /**
     * 员工调职表
     * @var unknown_type
     */
    protected  $trueTableName = 'mis_hr_personnel_train_info';
    public $_auto	=array(
    		array('createtime','time',self::MODEL_INSERT,'function'),
            array('updatetime','time',self::MODEL_UPDATE,'function'),
    		array('indate','dateToTimeString',self::MODEL_BOTH,'function'),
    		array('createid','getMemberId',self::MODEL_INSERT,'callback'),
    		array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
    );
}
?>