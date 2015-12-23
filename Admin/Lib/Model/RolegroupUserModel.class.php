<?php
/**
 * 权限组用户模型
 * @author 杨希
 * @data 2014-04-29
 */
class RolegroupUserModel extends CommonModel {
	protected $trueTableName = 'rolegroup_user';
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
	 * @Title: getRoleGroupByUserId
	 * @Description: todo(获取当前用户的项目执行ID列表)
	 * @author Alex
	 * @date 2014-12-4 下午22:52:00
	 * @throws
	 */
	public function getRoleGroupByUserId($userid){
		$rolegrouplist = $this->where("user_id = ".$userid)->getField("user_id,rolegroup_id");
		return $rolegrouplist;
	}
}
?>