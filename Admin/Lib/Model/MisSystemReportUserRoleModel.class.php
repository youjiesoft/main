<?php
/** 
 * @Title: MisSystemPanelUserRoleModel 
 * @Package package_name
 * @Description: todo(面板管理：用户面板权限) 
 * @author jiangx
 * @company 重庆特米洛科技有限公司
 * @copyright 重庆特米洛科技有限公司
 * @date 2013-10-10 16:18:54
 * @version V1.0 
*/ 
class MisSystemPanelUserRoleModel extends CommonModel {
	protected $tableName = 'mis_system_report_user_role';
	public $_auto	=array(
        array('createid','getMemberId',self::MODEL_INSERT,'callback'),
        array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
        array('createtime','time',self::MODEL_INSERT,'function'),
	    array('updatetime','time',self::MODEL_UPDATE,'function'),
	    array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),

	);	
	/**
	 * 
	 * @Title: getUserRolePanel
	 * @Description: todo(获取当前用户有权限的面板) 
	 * @return unknown  
	 * @author renling 
	 * @date 2014-9-1 下午3:51:29 
	 * @throws
	 */
	public function getUserRoleReport(){
		$map = array();
		$map['status'] = 1;
		$map['userid'] = $_SESSION[C('USER_AUTH_KEY')];
		$uerrole = $this->where($map)->find();
		return $uerrole;
	}
}
?>