<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(培训课程模型) 
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-3-27 上午10:58:51 
 * @version V1.0
 */
class MisHrEvaluationTrainModel extends CommonModel {
		protected $trueTableName = 'mis_hr_evaluation_train';	
		//自动验证
		public $_validate=array(
			array('orderno','','编号已经存在',self::EXISTS_VAILIDATE,'unique',self::MODEL_BOTH),		
			array('orderno','require','编号必须'),
			array('name','','课程名称已经存在',self::EXISTS_VAILIDATE,'unique',self::MODEL_BOTH),
		);
		//自动填充
		public $_auto	=array(
				array('createid','getMemberId',self::MODEL_INSERT,'callback'),
				array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
				array('createtime','time',self::MODEL_INSERT,'function'),
				array('updatetime','time',self::MODEL_UPDATE,'function'),
				array('traintime','dateToTimeString',self::MODEL_BOTH,'callback'),
				array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
				array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
				array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
		);
}
?>