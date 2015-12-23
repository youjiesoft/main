<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(待阅读报告) 
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-7-2 下午5:16:22 
 * @version V1.0
 */
class MisWorkReadStatementModel extends CommonModel {

	protected $trueTableName = 'mis_work_read_statement';
	
	public $_auto	=array(
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
// 			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
			array('commenttime','dateToTimeString',self::MODEL_UPDATE,'callback'),
		   array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),

			
			
			
			
	);
}
?>