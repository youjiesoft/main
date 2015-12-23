<?php
/** 
 * @Title: MisSystemPanelUsersortModel 
 * @Package package_name
 * @Description: todo(面板管理：用户面板排序) 
 * @author jiangx
 * @company 重庆特米洛科技有限公司
 * @copyright 重庆特米洛科技有限公司
 * @date 2013-10-10 16:18:54
 * @version V1.0 
*/ 
class MisSystemPanelUsersortModel extends CommonModel {
	protected $tableName = 'mis_system_panel_usersort';
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
	 * @Title: getUserSort
	 * @Description: todo(查询当前用户对面板设置的排序) 
	 * @return unknown  
	 * @author renling 
	 * @date 2014-9-1 下午3:55:51 
	 * @throws
	 */
	public function getUserSort(){
		//查出排序
		$aMap = array();
		$aMap['status'] = 1;
		$aMap['userid'] = $_SESSION[C('USER_AUTH_KEY')];
		$userSort = $this->where($aMap)->find();
		return $userSort;
	}
}
?>