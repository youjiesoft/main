<?php
/**
 * @Title: MisSystemUnitModelView
 * @Package package_name
 * @Description: todo(动态表单_自动生成-单位设置)
 * @author 管理员
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015-01-08 17:31:18
 * @version V1.0
*/
class MisSystemUnitViewModel extends ViewModel {
	public $viewFields = array(
	'mis_system_unit'=>array('id','danweimingchen','danweidaima','danweileixing','danweizhuangtai','ptmptid','flowid','ostatus','auditState','curAuditUser','curNodeUser','alreadyAuditUser','alreadyauditnode','auditUser','allnode','informpersonid','bindid','orderno','status','companyid','createid','createtime','updateid','updatetime','operateid','departmentid','projectid','projectworkid','isbase','sysdutyid','_type'=>'LEFT'),
);
}
?>