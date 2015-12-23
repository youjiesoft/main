<?php
/** 
 * @Title: MisOaMeetingPersonViewModel 
 * @Package package_name
 * @Description: todo(会议管理参会人员视图模型) 
 * @author 杨东 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-9-24 上午11:00:41 
 * @version V1.0 
*/ 
class MisOaMeetingPersonViewModel extends ViewModel {
	public $viewFields = array(
			'mis_oa_meeting_person' => array('_as'=>'mis_oa_meeting_person','status','id','manageid','userid','eventsid','isattend','reasons','status','_type'=>'LEFT'),
			'mis_oa_meeting_manage'=>array('_as'=>'mis_oa_meeting_manage','orderno','personname','typename','name','address','content','starttime','createtime','endtime','ostatus','conclusions','records','remark','_on'=>'mis_oa_meeting_manage.id=mis_oa_meeting_person.manageid','_type'=>'LEFT'),
			'user'=>array('_as'=>'user','name'=>'username','dept_id','_on'=>'user.id=mis_oa_meeting_person.userid'),
			'mis_system_department'=>array('_as'=>'mis_system_department','name'=>'deptname','_on'=>'user.dept_id=mis_system_department.id','_type'=>'LEFT'),
	);
}
?>