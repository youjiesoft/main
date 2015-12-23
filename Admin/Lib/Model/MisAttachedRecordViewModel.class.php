<?php
class MisAttachedRecordViewModel extends ViewModel {
	public $viewFields = array(
			"mis_attached_record"=>array('_as'=>'mis_attached_record','id','type','orderid','attached','status','upname','subid','companyid','departmentid','createid','operateid','createtime','updatetime','updateid','tablename','tableid','sysdutyid','fieldname','projectid','projectworkid','isfile','_type'=>'LEFT'),
			"mis_system_attach_file"=>array('_as'=>'mis_system_attach_file','id'=>'fileid','orderno'=>'orderno','position'=>'position','page'=>'page','remark'=>'remark','attachid'=>'attachid','formtype'=>'formtype','_on'=>'mis_system_attach_file.attachid=mis_attached_record.id'),

	);
}
?>