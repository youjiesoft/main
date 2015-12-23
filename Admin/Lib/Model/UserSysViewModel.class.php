<?php
/**
 * @Title: UserSysViewModel
 * @Package package_name
 * @Description: todo(动态表单_自动生成-用户视图)
 * @author 管理员
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015-03-22 14:56:00
 * @version V1.0
*/
class UserSysViewModel extends ViewModel {
	public $viewFields = array(
		'user_dept_duty'=>array('_as'=>'user_dept_duty','typeid','companyid','deptid','userid'=>'usersid','dutyid','worktype','id'=>'uddid','status'=>'dstatus','_type'=>'LEFT'),
		'mis_hr_personnel_person_info'=>array('_as'=>'mis_hr_personnel_person_info','qq','pinyin','shortnumber'=>'shortNumber','officenumber'=>'officeNumber','id'=>'infoid','accounttype','education','itemid','working','employeeheight','orderno','name','sex','indate','nativeaddress','dutyname','transferdate','email','chinaid','phone','workstatus','address','status'=>'STATUS','_on'=>'user_dept_duty.employeid = mis_hr_personnel_person_info.id','_type'=>'LEFT'),
		'user'=>array('_as'=>'user','id','email','mobile','account','name'=>'username','last_login_time','login_count','status'=>'ustatus','_on'=>'user.employeid = mis_hr_personnel_person_info.id'),
);
}
?>