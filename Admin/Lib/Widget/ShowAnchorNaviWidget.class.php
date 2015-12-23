<?php
/**
 * @Title: ShowRightToolBarWidget 
 * @Package package_name
 * @Description: todo(将toolbar放在右侧) 
 * @author谢友志 
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2014-12-25 上午11:46:59 
 * @version V1.0
 */
class ShowAnchorNaviWidget extends Widget{

	public function render($data){
		//标记，暂时用来获取从工作中心进入的刷新问题
		$myworkInit = $_GET['myworkInit']?$_GET['myworkInit']:0;
		
		$html = '<script src="__PUBLIC__/Js/ShowAnchorNavi.js" type="text/javascript"></script>';
		$html .='<div class="inside_pages_btn_group">';
        $html .=	'<ul class="pages_btn_group"></ul>';
        $html .='</div>';
        //标记，暂时用来获取从工作中心进入的刷新问题
        $html .='<input type="hidden" name="myworkInit" value="'.$myworkInit.'"/>';
        return $html;
	}
}