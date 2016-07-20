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
class ShowUploadPicWidget extends Widget{
	public function render($data){
		//字段名
		$fieldName=$data['1'];
		//标题
		$fieldTitle=$data['2'];
		//上传数量
		$uploadNum=$data['3'];
		//上传类型
		$uploadType=$data['4'];
		//上传的宽高
		$widthheight=$data['5'];
		if($uploadType){
			$upType='fileTypeDesc="'.$uploadType.'"  fileTypeExts="'.$uploadType.'"';
		}
		if($uploadNum){
			$uploadLimit='uploadLimit="'.$uploadNum.'"  queueSizeLimit="'.$uploadNum.'"';
		}
		$ActionName = ACTION_NAME;//操作方法
		//随机生成id选择器
		$num=rand(1000,999999);
		$str = "swfupload".$num;
		$str_queue = $str."-queue";
		$html = "";
		 if($data[0]){
		 	//封装头部
		 	$html.='<div class="fieldset_show_box">';
		 	$html.='<legend class="fieldset_legend_toggle side-catalog-text side-catalog-firstanchor">';
		 	$html.='<a name="upload"></a><b>相关附件</b>';
		 	$html.='<div class="tml_style_line tml_sl4 tml_slb_blue"></div>';
		 	$html.='</legend>';
        $html.= '</div>';
        $html.= '<div class="fieldsetjs_show_box">';
		 	$html.='<div class="tml-form-row">';
			$html.='	<label>'.$fieldTitle.'：</label>';
			$html.='<input type="hidden" name="'.$fieldName.'" value="'.$fieldName.'"/>';
			$html.='	<input id="'.$str.'" type="file" upload_save_name="'.$fieldName.'" '.$upType.'  '.$uploadLimit.'  uploader="true" auto="true" formData="{ uploadpath:\'__MODULE__\',widthheight:\''.$widthheight.'\'}"/>';
			$html.='</div>';
			$html.='<div class="tml-form-row">';
			$html.='<span id="'.$str_queue.'" class="info uploadify_queue">';
			$root = str_replace("\\","/", PUBLIC_PATH);
			$root = str_replace($_SERVER["DOCUMENT_ROOT"],"root/", $root);
			$root = str_replace("root/","http://".$_SERVER["HTTP_HOST"]."/", $root);
			$jpgArr = array("png","jpg","jpeg","gif","bmp");
		 	//封装中部
		 	if($data[0]){
		 		foreach($data[0] as $key=>$val){
		 			$a=strstr($val["attached"],"http://");
		 			if($a==false){
		 				$file_path = str_replace("\\","/", UPLOAD_PATH.$val["attached"]);
			 			$file_path = preg_replace("/^([\s\S]+)\/Public/", "", $file_path);
			 			$file_path = $root.$file_path;
		 			}else{
		 				$file_path=$val["attached"];
		 			}
		 			
		 			$info = pathinfo($val["attached"]);
		 			$file_extension_lower = strtolower($info['extension']);
		 			$html.='<div class="uploadify-queue-item">';
		 			if($ActionName != "auditEdit"){
		 				$html.='	<div class="cancel">';
		 				$html.='		<a class="dellink" href="__URL__/lookupdelatt/id/'.$val['id'].'" rel="'.$val['id'].'" target="ajaxTodo" callback="mis_swf_upload_del" callbackdata="'.$str_queue.'">X</a>';
		 				$html.='	</div>';
		 			}
					$html.='<span class="fileName">';
					$html.='	<a href="__URL__/misFileManageDownload/path/'.$val['name'].'/rename/'.$val['lookname'].'" target="_blank">'.$val['filename'].'</a>';
					$html.='</span>';
					$html.='<span class="data"> - 已经传</span>';
		 			if($val['archived']){
		 				//归档按钮
		 				$html.='<a class="tml-btn tml-btn-small tml-btn-green tml-ml5" href="__URL__/lookupDocumentCollateAtta/t/0/id/'.$val['id'].'" title="文件归档" target="dialog" width="650" height="550"><span class="icon icon-file"></span> 归档</a>';
		 			}
		 			if($val['online']){
		 				//在线查看按钮
		 				if(in_array($file_extension_lower,$jpgArr)){
		 					$html.='<a id="showimg'.$val['id'].'" download="'.$val['lookname'].'" class="tml-btn tml-btn-small tml-btn-green tml-ml5" style="cursor:pointer;" data-gallery="" title="'.$val['lookname'].'" href="'.$file_path.'"><span class="icon icon-file"></span> 在线查看</a><a id="showimg'.$val['id'].'" download="'.$val['lookname'].'" style="display:none" data-gallery="" title="'.$val['lookname'].'" href="'.$file_path.'"><span class="icon icon-file"></span> 在线查看</a>';
		 				}else{
			 				$html.='<a class="tml-btn tml-btn-small tml-btn-green tml-ml5" style="cursor:pointer;" rel="__URL__/playSWF/name/'.$val['name'].'/filename/'.$val['filename'].'" onclick="openNewWindowsDisplayFile(this)"><span class="icon icon-file"></span> 在线查看</a>';
		 				}
		 			}
					$html.='<div class="uploadify-progress">';
					$html.='	<div class="uploadify-progress-bar" style="width: 100%;"></div>';
					$html.='</div>';
					$html.='</div>';
		 		}
		 	}
		 	//封装后部
		 	$html.='</span></div>';
		 	$html.='</div>';
		 }else{
		 	$html.='<div class="fieldset_show_box">';
		 	$html.='<legend class="fieldset_legend_toggle side-catalog-text side-catalog-firstanchor">';
	 		$html.='	<a name="upload"></a><b>添加附件</b>';
		 	$html.='	<div class="tml_style_line tml_sl4 tml_slb_blue"></div>';
		 	$html.='</legend>';
        $html.= '</div>';
        $html.= '<div class="fieldsetjs_show_box">';
		 	$html.='<div class="tml-form-row">';
			$html.='	<label>'.$fieldTitle.'：</label>';
			$html.='<input type="hidden" name="'.$fieldName.'" value="'.$fieldName.'"/>';
			$html.='	<input id="'.$str.'" type="file" upload_save_name="'.$fieldName.'" '.$upType.'  '.$uploadLimit.'  uploader="true"  auto="true" formData="{ uploadpath:\'__MODULE__\',widthheight:\''.$widthheight.'\'}"/>';
			$html.='</div>';	
			$html.='<div class="tml-form-row">';
			$html.='	<span id="'.$str_queue.'" class="info uploadify-queue"></span>';
			$html.='</div>';
			$html.='</div>';
		 }
		return $html;
	}
}