<?php
/**
 * @Title: ShowFlowWidget
 * @Package package_name
 * @Description: todo(附件上传小主键)
 * @author liminggang
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2014-6-4 上午10:34:03
 * @version V1.0
 */
class ShowUploadViewWidget extends Widget{
	public function render($data){
		$html="";
		if($data[0]){
			$html .='<fieldset class="side-catalog-anchor">';
			$html .='<legend class="fieldset_legend_toggle side-catalog-text side-catalog-firstanchor">';
			$html .='<b>相关附件</b>';
			$html .='<div class="tml_style_line tml_sl4 tml_slb_blue"></div>';
			$html .='</legend>';
			/* $html .='<ul class="tml_file_box">';
			$i = 1;
			foreach($data[0] as $key=>$val){
				$html .='<li>';
	            $html .='         	<span class="file_count_left file_count">附件'.$i.'</span>';
	            $html .='            <a href="__URL__/misFileManageDownload/path/'.$val['name'].'/rename/'.$val['lookname'].'" target="_blank" class="attlink file_count_center file_count">'.$val['filename'].'</a>';
	            $html .='            <a class="file_count_right file_count" mask="true" href="__URL__/lookupDocumentCollateAtta/t/0/id/'.$val['id'].'" title="文件归档" target="dialog"><span class="">归档</span></a>';
	            $html .='         </li>';
				$i++;
			} */
			$name = MODULE_NAME;
			foreach($data[0] as $key=>$val){
		 			$html.='<div class="uploadify-queue-item">';
					//$html.='	<div class="cancel">';
					//$html.='		<a class="dellink" href="__URL__/lookupdelatt/id/'.$val['id'].'" rel="'.$val['id'].'" target="ajaxTodo" callback="mis_swf_upload_del" callbackdata="'.$str_queue.'">X</a>';
					//$html.='	</div>';
					$html.='<span class="fileName">';
					$html.='	<a href="__URL__/misFileManageDownload/path/'.$val['name'].'/rename/'.$val['lookname'].'" target="_blank">'.$val['filename'].'</a>';
					$html.='</span>';
					$html.='<span class="data"> - 已经传</span>';
		 			if($val['archived']){
		 				//归档按钮
		 				$html.='<a class="tml-btn tml-btn-small tml-btn-green tml-ml5" href="__URL__/lookupDocumentCollateAtta/t/0/id/'.$val['id'].'" title="文件归档" target="dialog"><span class="icon icon-file"></span> 归档</a>';
		 			}
		 			if($val['online']){
		 				//在线查看按钮
		 				$html.='<a class="tml-btn tml-btn-small tml-btn-green tml-ml5" style="cursor:pointer;" href="PageOffice://|http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'].'/'.$name.'/playSWF/name/'.$val['name'].'/filename/'.$val['filename'].'/uid/'.$_SESSION[C('USER_AUTH_KEY')].'|width=1200px;height=800px;|"><span class="icon icon-file"></span> 在线查看</a>';
		 			}
					$html.='<div class="uploadify-progress">';
					$html.='	<div class="uploadify-progress-bar" style="width: 100%;"></div>';
					$html.='</div>';
					$html.='</div>';
		 		}
			$html .='</fieldset>';/* </ul> */
		}
		return $html;
	}
}