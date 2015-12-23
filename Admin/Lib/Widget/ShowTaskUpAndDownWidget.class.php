<?php
/**
 * @Title: ShowTaskUpAndDownWidget 
 * @Package package_name
 * @Description: todo(指定任务的上一个和下一个任务) 
 * @author谢友志 
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2014-12-26 上午9:46:13 
 * @version V1.0
 */
class ShowTaskUpAndDownWidget extends Widget{
	public function render($data){
		//必需传入当前任务id array('id'=>???)
		if($data['id']){
			$model = M("mis_project_flow_form");
			$projectid = getFieldBy($data['id'],'id','projectid',"mis_project_flow_form");
			$list = $model->where('status=1 AND outlinelevel = 4 AND projectid='.$projectid)->order('id asc')->select();
			foreach($list as $k=>$v){
				if($v['id']==$data['id']){
					break;
				}
			}
			$prev = $list[$k-1];//上一个任务
			$next = $list[$k+1];//下一个任务
			$html = '<div class="tml_search_tips">';
			$html .= '<div class="search_tips_title">上一个任务：</div>';
			if($prev){
				if($prev['complete']==1){
					$html .= '<p class="search_tips_center">'.$prev['name'].'--'.$prev['orderno'].'&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:grey">【已完成】</span>&nbsp;&nbsp;&nbsp;&nbsp;任务开启时间：'.date('Y-m-d',$prev['begintime']);
					if($prev['notes']) $html .= "&nbsp;&nbsp;&nbsp;&nbsp;任务描述：".$prev['notes'];
					$html .= '</p>';
				}else{
					$html .= '<p class="search_tips_center">'.$prev['name'].'--'.$prev['orderno'].'&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:blue">【未完成】</span>&nbsp;&nbsp;&nbsp;&nbsp;任务开启时间：'.date('Y-m-d',$prev['begintime']);
					if($prev['notes']) $html .= "&nbsp;&nbsp;&nbsp;&nbsp;任务描述：".$prev['notes'];
					$html .= '</p>';
				}
			}else{
				$html .= '<p class="search_tips_center"><span style="color:grey">任务不存在</span></p>';
			}
			
			$html .= '<div class="search_tips_title">下一个任务：</div>';
			if($next){
				if($next['complete']==1){
					$html .= '<p class="search_tips_center">'.$next['name'].'--'.$next['orderno'].'&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:grey">【已完成】</span>&nbsp;&nbsp;&nbsp;&nbsp;任务开启时间：'.date('Y-m-d',$next['begintime']);
					if($next['notes']) $html .= "&nbsp;&nbsp;&nbsp;&nbsp;任务描述：".$next['notes'];
					$html .= '</p>';
				}else{
					$html .= '<p class="search_tips_center">'.$next['name'].'--'.$next['orderno'].'&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:blue">【未完成】</span>&nbsp;&nbsp;&nbsp;&nbsp;任务开启时间：'.date('Y-m-d',$next['begintime']);
					if($next['notes']) $html .= "&nbsp;&nbsp;&nbsp;&nbsp;任务描述：".$next['notes'];
					$html .= '</p>';
				}
			}else{
				$html .= '<p class="search_tips_center"><span style="color:grey">任务不存在</span></p>';
			}
			$html .= '</div>';
			return $html;
		}
	} 
}
