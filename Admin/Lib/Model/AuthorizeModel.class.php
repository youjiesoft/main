<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(用户模型，整体授权部分的) 
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-6-18 下午5:27:13 
 * @version V1.0
 */
class AuthorizeModel extends CommonModel {
	protected $trueTableName = 'user';
	public $_validate	=	array(
		array('account','/^[a-z]\w{1,}$/i','用户名必须是字母，且2位以上'),
		array('password','require','密码必须'),
		array('name','require','昵称必须'),
		array('repassword','require','确认密码必须'),
		array('account,status','','帐号已经存在',self::MUST_VALIDATE,'unique',self::MODEL_BOTH),
		array('account,employeid,status','','帐号与员工已绑定',self::MUST_VALIDATE,'unique',self::MODEL_BOTH),
		);

	public $_auto		=	array(
		array('password','pwdHash',self::MODEL_BOTH,'callback'),
		array('last_login_time','time',self::MODEL_INSERT,'function'),
		array('work_date','time',self::MODEL_INSERT,'function'),
		array('work_date','time',self::MODEL_UPDATE,'function'),
		array('createid','getMemberId',self::MODEL_INSERT,'callback'),
		array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
		array('createtime','time',self::MODEL_INSERT,'function'),
		array('updatetime','time',self::MODEL_UPDATE,'function'),
		array('ispur','returnIspur',self::MODEL_BOTH,'callback'),
	    array('companyid','getCompanyID',self::MODEL_INSERT,'callback'),
		array('departmentid','getDeptID',self::MODEL_INSERT,'callback'),
		array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
	);
	
	protected function returnIspur($tempval){
		if($tempval){
			return 1;
		}else{
			return 0;
		}
	}
	
	protected function pwdHash() {
		if(isset($_POST['password'])) {
			return pwdHash($_POST['password']);
		}else{
			return false;
		}
	}
}
?>