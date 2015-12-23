<?php
/**
 * @Title: showReadInfoWidget 
 * @Package package_name
 * @Description: todo(阅读情况) 
 * @author谢友志 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015-10-26 下午5:47:50 
 * @version V1.0
 */
class showReadInfoWidget extends Widget{
	/**
	 * @Title: render
	 * @Description: todo(这里用一句话描述这个方法的作用) 
	 * @param unknown_type $data  ==$vo
	 * @author 谢友志 
	 * @date 2015-10-26 下午5:48:45 
	 * @throws
	 */
	public function render($data){
		$model = M("mis_system_sysremind");
		$map['mis_system_sysremind.modelname'] = MODULE_NAME;// 当前类名
		$map['mis_system_sysremind.pkey'] = $data['id'];
		//$map['mis_system_sysremind.userid'] = $_SESSION[C("USER_AUTH_KEY")];
		$map['mis_system_sysremind.isread']=1; //这个条件待定
		$list = $model->field("user.name,mis_system_sysremind.remindcreatetime,mis_system_sysremind.updatetime,mis_system_sysremind.readcount")->join("LEFT JOIN user ON user.id=mis_system_sysremind.userid")->where($map)->order("remindcreatetime desc")->select();
		
		$html ='<div class="fieldset_show_box">';
		$html .='	<legend class="fieldset_legend_toggle side-catalog-text side-catalog-firstanchor">';
		$html .='		<a name="yuedurenyuan"></a><b>阅读情况</b>';
		$html .='		<div class="tml_style_line tml_sl4 tml_slb_blue"></div>';
		$html .='	</legend>';
		$html.= '</div>';
		$html.= '<div class="fieldsetjs_show_box" style="display:none;">';
		$html .= "<table class='nbm_data_table' style='width:60%'>";
		$html .= "<thead>";
		$html .= "<tr>";
		$html .= "	<td width='15%'>";
		$html .= "		阅读人";
		$html .= "	</td>";
		$html .= "	<td width='40%'>";
		$html .= "		首次阅读时间";
		$html .= "	</td>";
		$html .= "	<td width='40%'>";
		$html .= "		最近阅读时间";
		$html .= "	</td>";
		$html .= "	<td width='5%'>";
		$html .= "		阅读次数";
		$html .= "	</td>";
		$html .="</tr>";
		$html .= "</thead>";
		$html .= "<tbody>";
		
		if(!empty($list)){
			foreach($list as $key=>$val){
				$html .= "<tr>";
				$html .= "	<td>";				
				$html .= "{$val['name']}";
				$html .= "	</td>";
				$html .= "	<td>";				
				$html .= $val['remindcreatetime']?date("Y-m-d H:i:s",$val['remindcreatetime']):'';//"{$val['remindcreatetime']}";
				$html .= "	</td>";
				$html .= "	<td>";	
				if($val['remindcreatetime']){
					$uptime = $val['updatetime']?date("Y-m-d H:i:s",$val['updatetime']):date("Y-m-d H:i:s",$val['remindcreatetime']);
				}else{
					$uptime = $val['updatetime']?date("Y-m-d H:i:s",$val['updatetime']):'';
				}		
				$html .= $uptime;
				$html .= "	</td>";
				$html .= "	<td>";				
				$html .= $val['readcount']?$val['readcount']:1;
				$html .= "	</td>";
				$html .= "</tr>";
			}
		}else{	
			$html .= "<tr>";
			$html .= "<td colspan='4'>暂时还没有人阅读</td>";
			$html .= "</tr>";
		}
		$html .= "</tr>";
		$html .= "</tbody>";
		$html .= "</table>";	
		$html.= '</div>';
		return $html;
	}
	
}