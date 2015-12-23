<?php
/**
 * @Title: ShowNotifyWidget
 * @Package package_name
 * @Description: 知会人W主键
 * @author liminggang
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2014-6-4 上午10:34:03
 * @version V1.0
 */
class ShowNotifyViewWidget extends Widget{
	public function render($data){
		$modulename = MODULE_NAME;// 当前类名
		//获取节点数据
		$process_relation_formDao = M("process_relation_form");
		$where = array();
		$where['tableid'] = $data['id'];
		$where['tablename'] = $modulename;
		$relaformlist = $process_relation_formDao->where($where)->order("sort asc")->getField("relationid,flowtype,auditState,id,name,informpersonid");
		//实例化后台用户模型
		$userModel = D("User");
		$auditUserArr = array();
		$bool = true;
		foreach($relaformlist as $key=>$val){
			$arr = array();
			$arr['name'] = $val['name'];
			$arr['nodeid'] = $val['relationid'];
			$arr['flowtype'] = $val['flowtype'];
			$arr['informpersonid'] = $val['informpersonid'];
			if($val['auditState'] == 1){
				//表示当前节点以审核
				$arr['auditStatus'] = 1;
				$arr['classval'] = 'check_current_ed';
			}else{
				if($bool){
					//表示当前为待审核节点
					$arr['auditStatus'] = 2;
					$arr['classval'] = 'check_current';
					$bool = false;
				}else{
					//未审核节点
					$arr['auditStatus'] = 0;
					$arr['classval'] = '';
				}
			}
			$map = array();
			$map['id'] = array(' in ',explode(",", $val["informpersonid"]));
			$userlist = $userModel->where($map)->field("id,name")->select();
			$arr['audituser'] = $userlist;
			$auditUserArr[] = $arr;
		}
		//$disableNoneModel知会人员 默认不显示状态的模块 -by xyz 2015-10-28
		$disableNoneModel = array("MisAutoJds","MisAutoHxz","MisAutoXny");
		$disable = '';
		if(in_array($modulename,$disableNoneModel)){
			$disable = 'style="display:none;"';
		}
		$html = "";
		//*******************流程审核人部分********************//
		$html .='<div class="fieldset_show_box">';
		$html .='	<legend class="fieldset_legend_toggle side-catalog-text side-catalog-firstanchor">';
		$html .='		<a name="zhihuirenyuan"></a><b>知会人员</b>';
		$html .='		<div class="tml_style_line tml_sl4 tml_slb_blue"></div>';
		$html .='	</legend>';
		$html.= '</div>';
		$html.= '<div class="fieldsetjs_show_box" '.$disable.'>';
		
		$html.='<div class="check_info"><div class="check_flow_path">';
		foreach($auditUserArr as $k=>$v){
			if(count($v['audituser'])){
				$html .='<div class="">';
				if($k > 0)$html .='<span class="forward_sign icon-double-angle-right"></span>';
				$html .='<div class="per_big_box">';
				foreach($v['audituser'] as $k1=>$v1){
					//获取用户的头像。
					$pic = $userModel->getUserPic($v1['id']);
					$html .='<div class="per_box">';
					$html .='	<img width="45" src="'.$pic.'" alt=""/>';
					$html .='   <span>'.$v1['name'].'</span>';
					$html .='</div>';
				}
				$html.= '</div></div>';
			}
		}
		$html .='</div></div></div>';
		$html .='<div class="clear"></div>';
		/* $html="";
		$html.='<div class="fieldset_show_box">';
        $html.='    <legend class="fieldset_legend_toggle side-catalog-text side-catalog-firstanchor">';
        $html.='         <a name="zhihuirenyuan"></a><b>知会人员</b>';
        $html.='         <div class="tml_style_line tml_sl4 tml_slb_blue"></div>';
        $html.='    </legend>';
        $html.='    <div class="check_info">';
        $html.='         <div class="check_flow_path js-ShowNotify-checkuser">';
	    $html .=' <ul id="showNotiy_add">';
        	if($infoArr){
        		foreach($infoArr as $k=>$v){
        			//获取用户的头像。
        			$pic = $userModel->getUserPic($v['id']);
        	
			    $html .='     <li class="per_box">';
			    $html .='    	<input type="hidden" name="informpersonname[]" value="'.$v['name'].'">';
			    $html .='    	<input type="hidden" name="informpersonid[]" value="'.$v['id'].'">';
			    $html .='    	<input type="hidden" name="pic[]" value="'.$pic.'">';
			    $html .='		<img width="45" src="'.$pic.'" alt=""/>';
			    $html .='    	<span>'.$v['name'].'</span>';
			    $html .='     </li>';
			    }
			  }
	    $html .='</ul>';
        $html.='    	</div>';
        $html.='    </div>';
        $html.='</div>'; */
		return $html;
	}
}