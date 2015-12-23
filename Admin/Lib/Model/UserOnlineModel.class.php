<?php
//Version 1.0
// 用户在线模型
class UserOnlineModel extends CommonModel {
	protected $trueTableName = 'user_online';
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
	 * @Title: getOnLineCount
	 * @Description: todo(获取在线人数)
	 * @author jiangx
	 * @date 2014-01-02
	 */
	public function getOnLineCount(){
		$count = 0;
		$count = $this->count('userid');
		return $count;
	}
}
?>