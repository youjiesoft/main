<?php
class ShowToLevelMenuWidget extends Widget{
	/**
	 * (non-PHPdoc)
	 * @see Widget::render(系统三级页面左树与导航切换)
	 */
	public function render($data){
		return ;
		//获取改模型所在分组的菜单
		$name = MODULE_NAME;
		$groupid = getFieldBy($name, 'name', 'group_id', 'node');
		//实例化缓存 有就读缓存的内容
		$mrdmodel = D('MisRuntimeData');
		$html = $mrdmodel->getRuntimeCache($name,$groupid.'ShowToLevelMenu');
		if(empty($html)){
			$model = D('Public');
			$accessNode = $model->menuLeftTree($groupid);
			//组合html
			$html.='<script src="__PUBLIC__/Js/showtolevelmemu.js" type="text/javascript"></script>';
			
			$html .= '<a class="menu_tag" href="#"><span class="icon-reorder"></span></a>';
			$html .= '<div class="nbmaccordionFloat">';
			$html .= '<div class="nbmaccordion"  data-option="close:icon-double-angle-down;open:icon-double-angle-up" layouth="85">';
			foreach ($accessNode as $k=>$vo){
			
				$html .= '<div title="'.$vo["title"].'">';
				$html .= '<ul>';
				foreach ($vo['levelthree'] as $k1=>$vv){
					$html .= '<li>';
					$html .= '<a title="'.$vv["title"].'" href="__APP__/'.$vv["name"].'/index" target="navTab">';
					$html .= '<span class="icon icon-tags mr10"></span>';
					$html .= '<span>'.$vv["title"].'</span>';
					$html .= '</a>';
					$html .= '</li>';
				}
				$html .= '</ul>';
				$html .= '</div>';
			}
			$html .= '</div>';
			$html .= '</div>';
			$mrdmodel->setRuntimeCache($html,$name,$groupid.'ShowToLevelMenu');
		}	
		return $html;
	}
}