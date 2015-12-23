<?php
/**
 * @Title: file_name
 * @Package package_name
 * @Description: todo(评估指标模型)
 * @author liminggang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-3-27 上午11:28:27
 * @version V1.0
 */
class MisHrEvaluationTargetModel extends CommonModel {
	protected $trueTableName = 'mis_hr_evaluation_target';
	public $_validate=array(
			array('code','','编号已经存在',self::EXISTS_VAILIDATE,'unique',self::MODEL_BOTH),
			array('code','require','编号必须'),
			array('name','require','指标名称必须'),
	);
	//自动填充
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