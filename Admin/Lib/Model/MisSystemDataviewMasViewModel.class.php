<?php 
class MisSystemDataviewMasViewModel extends ViewModel{
    /**
     * 人事离职模型
     * @var unknown_type
     */
	public $viewFields = array(
			'mis_system_dataview_mas'=>array('_as'=>'mis_system_dataview_mas','id','name','modelname','title','replacesql','status'=>'mstatus','_type'=>'LEFT'),
			'mis_system_dataview_sub'=>array('_as'=>'mis_system_dataview_sub','islistshow','title'=>'subtitle','masid','funname','funcdata','title'=>'subtitle','isback','isshow','field','otherfield','tablename','_on'=>'mis_system_dataview_mas.id=mis_system_dataview_sub.masid'),
	);

	public function getViewConf($name){
		$modelname = getFieldBy($name,'name','modelname','mis_system_dataview_mas');
		$file = Dynamicconf."/Models/".$modelname."/".$name.".inc.php";
		if(is_file($file)){
			return require $file;
		}else{
			return array();
		}
	
	}
}
?>