<?php 
/**
 * @Title: file_name
 * @Package package_name
 * @Description: todo(人事培训和培训课程关联表)
 * @author liminggang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-4-11 下午5:34:03
 * @version V1.0
 */
class MisHrPersonnelTrainManageRelationModel extends CommonModel{
	/**
	 * 员工培训关联表
	 */
	protected  $trueTableName = 'mis_hr_personnel_train_manage_relation';
	public $_auto	=array(
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
	);
	
	
	public $viewFields = array(
			'mis_hr_personnel_train_manage_relation'=>array('_as'=>'mis_hr_personnel_train_manage_relation','id','manageid','trainid','personid','classhour','status','_as'=>'mis_hr_personnel_train_manage_relation','_type'=>'LEFT'),
			'mis_hr_evaluation_train'=>array('_as'=>'mis_hr_evaluation_train','id'=>'relid','orderno','name','type','classsource','departmentid','lecturer','level','traintime','site','classhour','trainform','assessment','traincost','curriculumvalue','_as'=>'mis_hr_evaluation_train','_on'=>'mis_hr_personnel_train_manage_relation.id=mis_hr_evaluation_train.manageid'),
	);
}
?>