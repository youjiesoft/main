<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(评估指标具体内容模型) 
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-3-27 上午11:12:52 
 * @version V1.0
 */
class MisHrEvaluationContentModel extends CommonModel {
	protected $trueTableName = 'mis_hr_evaluation_content';
	public $_validate=array(
			array('code','','编号已经存在',self::EXISTS_VAILIDATE,'unique',self::MODEL_BOTH),
			array('code','require','编号必须'),
			array('name','require','评估名称必须'),
	);
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