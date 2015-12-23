<?php
/**
 * @Title: HiddenInputWidget 
 * @Package package_name
 * @Description: 表单内部需要隐藏的input值，此W先目前只作用于项目
 * @author 黎明刚 
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2014年10月22日 下午5:51:40 
 * @version V1.0
 */
class HiddenInputWidget extends Widget{
	
	public function render($data){
		$html ="";
		if($data){
			//获取项目ID
			$projectid = $data['projectid'];
			//获取任务ID
			$projectworkid = $data['projectworkid'];
			//获取子流程生单的模型和ID值。
			$sourcemodel = $data['auditFlowTuiTablename'];
			$sourceid = $data['auditFlowTuiTableid'];
			//获取主流程的模型和ID值。
			$auditZhuLicModel = $data['auditZhuLicModel'];
			$auditZhuLicId = $data['auditZhuLicId'];
		}else{
			//获取项目ID
			$projectid = $_REQUEST['projectid'];
			//获取任务ID
			$projectworkid = $_REQUEST['projectworkid'];
			//获取子流程生单的模型和ID值。
			$sourcemodel = $_GET['auditFlowTuiTablename'];
			$sourceid = $_GET['auditFlowTuiTableid'];
			//获取主流程的模型和ID值。
			$auditZhuLicModel = $_GET['auditZhuLicModel'];
			$auditZhuLicId = $_GET['auditZhuLicId'];
		}
		
		$html .='<input type="hidden" name="projectid" value="'.$projectid.'"/>'; //项目ID
		$html .='<input type="hidden" name="projectworkid" value="'.$projectworkid.'"/>';//项目任务ID
		
		$html .='<input type="hidden" name="auditFlowTuiTablename" value="'.$sourcemodel.'"/>'; //子流程来源模型
		$html .='<input type="hidden" name="auditFlowTuiTableid" value="'.$sourceid.'"/>';//子流程来源模型ID
		
		$html .='<input type="hidden" name="auditZhuLicModel" value="'.$auditZhuLicModel.'"/>'; //主流程模型
		$html .='<input type="hidden" name="auditZhuLicId" value="'.$auditZhuLicId.'"/>';//主流程模型ID
		
		return $html;
	}
}
?>