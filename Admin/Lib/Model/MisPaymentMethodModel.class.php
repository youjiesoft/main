<?php
/**
 * @Title: MisPaymentMethodModel 
 * @Package package_name 
 * @Description: todo(付款方式模型) 
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2012-1-8 下午3:35:01 
 * @version V1.0
 */
class MisPaymentMethodModel extends CommonModel{
	protected $trueTableName = 'mis_payment_method';
	
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
	array('code,status','','此编码已经存在！',self::MUST_VALIDATE,'unique',self::MODEL_BOTH),
);

}
?>