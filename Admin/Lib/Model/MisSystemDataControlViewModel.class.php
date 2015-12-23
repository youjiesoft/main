<?php 
class MisSystemDataControlViewModel extends ViewModel{
    /**
     * 人事离职模型
     * @var unknown_type
     */
	public $viewFields = array(
			'mis_system_data_control_mas'=>array('_as'=>'mis_system_data_control_mas','id'=>'masid','name','modelname','status'=>'mstatus','_type'=>'LEFT'),
			'mis_system_data_control_sub'=>array('_as'=>'mis_system_data_control_sub','id','masid','roamtype','objtable','operation','rules','rulesinfo','showrules','treenode','showchoicetables','choicetablesforrole','`sql`'=>'`sql`','sqlselectform','sqlselectformarr','msginfo','_on'=>'mis_system_data_control_mas.id=mis_system_data_control_sub.masid'),
	);

}
?>