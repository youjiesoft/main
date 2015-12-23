<?php
/**
 * @Title: MisSystemTempleteManageModelView
 * @Package package_name
 * @Description: todo(动态表单_自动生成-模板管理)
 * @author 管理员
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015-01-27 10:48:48
 * @version V1.0
*/
class MisSystemTempleteManageViewModel extends ViewModel {
	public $viewFields = array(
	'mis_system_templete_manage'=>array('id','name','zhuangtai','upload3','upload4','modelname','modelid','ptmptid','flowid','ostatus','auditState','curAuditUser','curNodeUser','alreadyAuditUser','alreadyauditnode','auditUser','allnode','informpersonid','bindid','orderno','status','companyid','createid','createtime','updateid','updatetime','operateid','departmentid','projectid','projectworkid','sysdutyid','rules','rulesinfo','showrules','_type'=>'LEFT'),
);
}
?>