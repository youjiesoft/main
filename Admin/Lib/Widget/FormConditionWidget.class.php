<?php
class FormConditionWidget extends Widget{
	
	public function render($data){
		$type = $data[0]?$data[0]:'add';
		$arr['searching']	= true;
		$arr['show_both_save_btn'] = true;
		$arr['displayLength'] = 5;  
		if($data['data']['orderno']['status']){
			$arr['mini_set_orderno']=$data['data']['orderno'];
			$data['data']['orderno']= $data['data']['orderno']['orderno'];
		}
		$arr["add_default_val"] = $data['data'];
		$bindrdid=$_REQUEST['bindrdid'];
		$bindaname=$_REQUEST['bindaname'];
		$bindtype=$_REQUEST['bindtype'];
		$main=$_REQUEST['main'];
		$dialog = "";
		if($_REQUEST['fieldtype']){
			$arr['isReloadNavTab']=true;
			$fieldtype = $_REQUEST["fieldtype"];
			$typeval=$_REQUEST[$fieldtype];
			if($_GET['bindtype']==2||$_GET['bindtype']==3){
				//此处控制组合表单列表型式弹出 还是内嵌
				if($_GET['bindtype']==3){
					$arr['mini_add_type']="dialog";
					$dialog = "/dialog/1";
				}
				$arr['mini_add_url'] = "__URL__/add{$dialog}/fieldtype/$fieldtype/$fieldtype/$typeval/bindaname/$bindaname/bindtype/$bindtype/bindrdid/$bindrdid";
			}
			$url = "__URL__/datatablesave/bindaname/$bindaname/fieldtype/$fieldtype/$fieldtype/$typeval";
		}else{
			$bindid = $_REQUEST["bindid"];
			//此处控制组合表单列表型式弹出 还是内嵌
			if($_GET['bindtype']==2||$_GET['bindtype']==3){
				if($_GET['bindtype']==3){
					$arr['mini_add_type']="dialog";
					$dialog = "/dialog/1";
				}
				$arr['mini_add_url'] = "__URL__/add{$dialog}/bindaname/$bindaname/bindtype/$bindtype/bindid/$bindid/bindrdid/$bindrdid";
			}
			$url = "__URL__/datatablesave/bindid/$bindid/bindaname/$bindaname";
		}
		
		$arrStr='';
		if(isset($arr)){
			$arrStr = json_encode($arr);
		}
		$html  ='';
		if($type == "edit"){
			if($_GET['minitype'] != 1){
				$html .="\r\ntable_type=\"{$type}\" ";
				$html .="\r\ntable_data='{$arrStr}' ";
				$html .="\r\najax_post_url=\"{$url}\" ";
			}
		}
		return $html;
	}
}