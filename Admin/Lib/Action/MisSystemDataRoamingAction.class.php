<?php
class MisSystemDataRoamingAction extends CommonAction{
	//POST值根据查询结果重新赋予
	public  function AjaxDataRoam(){
		print_R($_REQUEST);
		$soucemodel=$_REQUEST['val'][0]['sourcemodel'];
		$sourceid=$_REQUEST['val'][0]['sourceid'];
		$targetmodel=$_REQUEST['val'][0]['targetmodel'];
        $this->lookupDataRoamPull($soucemodel,$sourceid,$targetmodel);
	}
}
?>