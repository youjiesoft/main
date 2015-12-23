<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: 项目流程=》业务阶段模型 
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-8-16 上午11:04:15 
 * @version V1.0
 */
class MisSystemFlowPhaseModel extends CommonModel {
	protected $trueTableName = 'mis_system_flow_type';
	public $_auto =array(
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
			array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
	);
	
	public $_validate = array(
			array('name,parentid','','类型名称已存在',self::EXISTS_VAILIDATE,'unique',self::MODEL_BOTH),
	);
}
?>