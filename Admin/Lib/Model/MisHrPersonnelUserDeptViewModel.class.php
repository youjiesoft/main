<?php
/**
 * @Title: file_name
 * @Package package_name
 * @Description: todo(员工岗位)
 * @author lcx
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-3-27 上午11:43:49
 * @version V1.0
 */
class MisHrPersonnelUserDeptViewModel extends ViewModel{
	public $viewFields = array(
			"user_dept_duty"=>array('_as'=>'user_dept_duty','typeid','companyid','deptid','userid'=>'usersid','dutyid','worktype','id'=>'uddid','status'=>'dstatus','_type'=>'LEFT'),
			"mis_hr_personnel_person_info"=>array('_as'=>'mis_hr_personnel_person_info','email','picture','phone','qq','pinyin','shortNumber','officeNumber','id','accounttype','education','itemid','working','employeeheight','orderno','name','sex','indate','nativeaddress','dutyname','transferdate','email','chinaid','phone','workstatus','address','status','_on'=>'user_dept_duty.employeid=mis_hr_personnel_person_info.id','_type'=>'LEFT'),
			"user"=>array('_as'=>'user','password','id'=>'userid','account','name'=>'username','last_login_time','login_count','status'=>'ustatus','_on'=>'user.employeid=mis_hr_personnel_person_info.id','_type'=>'LEFT'),
			"mis_system_duty"=>array('_as'=>'mis_system_duty','id'=>'systemdutyid','name'=>'dutyname','_on'=>'user_dept_duty.dutyid=mis_system_duty.id','_type'=>'LEFT'),
	);
	public  function getUserInfoList($uid,$field1='',$value1='',$typeid=''){
		$companyid="";
		$UserDeptDutyModel=D("UserDeptDuty");
		if($field1){
			//$var = "and '.$field11.'='.$value1.'";
			$map[$field1] = $value1;
		}
		if(is_array($uid)){
			$map['usersid']=array("in",$uid);
		}else{
			$map['usersid']=$uid?$uid:$_SESSION[C('USER_AUTH_KEY')];
		}
		$map['typeid']=$typeid?$typeid:1;//存在typeid值取typeid 默认为主岗
		$map['_string'] = "1=1";
		// 	"'.$key.'='.$value.' and 1=1 '.$var.'"
		if(is_array($uid)){
			$data = $this->where($map)->group("usersid")->select();
		}else{
			$temp = $this->where($map)->find();
			$data[]=$temp;
		}
		return $data;
	}
}
