<?php 
class MisSystemDataRoamMasViewModel extends ViewModel{
    /**
     * 人事离职模型
     * @var unknown_type
     */
	public $viewFields = array(
			'mis_system_data_roam_mas'=>array('_as'=>'mis_system_data_roam_mas','id','title','sourcemodel','targetmodel','isbindsettable','cycle','strelation','onlyoneinsert','issubtable','forceadd','isdebug','status','_type'=>'LEFT'),
			'mis_system_data_roam_relation_rules'=>array('_as'=>'mis_system_data_roam_relation_rules','id'=>'subid','targettype','sourcetype','rules','endsql','_on'=>'mis_system_data_roam_mas.id=mis_system_data_roam_relation_rules.masid'),
	);
}
?>