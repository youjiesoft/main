<?php
/**
 * @Title: ShowFlowWidget
 * @Package package_name
 * @Description: todo(获取审批流节点)
 * @author liminggang
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2014-6-4 上午10:34:03
 * @version V1.0
 */
class ShowFlowWidget extends Widget{
	public function render($data){
		$flowid=$data['flowid']?$data['flowid']:'0';
		$infoid = $data['id']?$data['id']:'0';
		$html .= '<input type="hidden" name="flowid" value="'.$flowid.'"/><a class="headProcessDetail js_FlowWidget" dhref="__URL__/lookupSeeFlow/infoid/'.$infoid.'/flowid/" href="__URL__/lookupSeeFlow/infoid/'.$infoid.'/flowid/'.$flowid.'" target="dialog" height="450" width="850" mask="true" title="流程明细查看" rel="__MODULE__lookupSeeFlow" warn="请选择节点">[流程明细查看]</a>';
		return $html;
	}
}