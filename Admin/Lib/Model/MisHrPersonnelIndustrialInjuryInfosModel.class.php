<?php
/**
 * 人事事故模型
 * @author liminggang
 *
 */
class MisHrPersonnelIndustrialInjuryInfosModel extends CommonModel {
	protected $trueTableName = 'mis_hr_personnel_industrial_injury_info';
	
	public $_auto =array(
			array("createid","getMemberId",self::MODEL_INSERT,"callback"),
			array("updateid","getMemberId",self::MODEL_UPDATE,"callback"),
			array("createtime","time",self::MODEL_INSERT,"function"),
			array("updatetime","time",self::MODEL_UPDATE,"function"),
			array('prosubdate','dateToTimeString',self::MODEL_BOTH,'callback'),
			array('databjdate','dateToTimeString',self::MODEL_BOTH,'callback'),
			array('reportcutdate','dateToTimeString',self::MODEL_BOTH,'callback'),
			array('indate','dateToTimeString',self::MODEL_BOTH,'callback'),
			array('socialsecurity','dateToTimeString',self::MODEL_BOTH,'callback'),
			array('collecteddate','dateToTimeString',self::MODEL_BOTH,'callback'),
			array('accidenttime','dateToTimeString',self::MODEL_BOTH,'callback'),
			array('businestime','dateToTimeString',self::MODEL_BOTH,'callback'),
			array('businescolltime','dateToTimeString',self::MODEL_BOTH,'callback'),
			//千位分割
			array('sumcost','numberToReplace',self::MODEL_BOTH,'callback'),
			array('bxcost','numberToReplace',self::MODEL_BOTH,'callback'),
			array('businesscost','numberToReplace',self::MODEL_BOTH,'callback'),
			array('companycost','numberToReplace',self::MODEL_BOTH,'callback'),
			array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
		);
	
	public $_validate=array(
		array('accidentdate','require','事故时间必须'),
		array('name','require','伤者姓名必须'),
	);
}
?>