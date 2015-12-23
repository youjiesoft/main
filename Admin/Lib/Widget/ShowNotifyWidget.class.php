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
class ShowNotifyWidget extends Widget{
	public function render($data){
		//定义一个自定义流程获取的地址
// 		$zdUrl = __APP__."/Common/lookupUserListNotify";
// 		$infoArr = array();
// 		if($data['informpersonid']){
// 			$info = explode(",", $data['informpersonid']);
// 			$UserModel = D("User");
// 			$where['id'] = array(' in ',$info);
// 			$infoArr = $UserModel->where($where)->field("id,name")->select();
// 		}
// 		//实例化后台用户模型
// 		$userModel = D("User");
		
		$html="";
// 		$html.='<div class="fieldset_show_box">';
//         $html.='    <legend class="fieldset_legend_toggle side-catalog-text side-catalog-firstanchor">';
//         $html.='         <a name="zhihuirenyuan"></a><b>知会人员</b>';
//         $html.='         <div class="tml_style_line tml_sl4 tml_slb_blue"></div>';
//         $html.='    </legend>';
//         $html.= '</div>';
//         $html.= '<div class="fieldsetjs_show_box">';
//         $html.='    <div class="check_info">';
//         $html.='         <div class="check_flow_path js-ShowNotify-checkuser">';
// 	     $html .=' <ul id="showNotiy_add">';
//         	if($infoArr){
//         		foreach($infoArr as $k=>$v){
//         			//获取用户的头像。
//         			$pic = $userModel->getUserPic($v['id']);
        	
// 			    $html .='     <li class="per_box">';
// 			    $html .='    	<input type="hidden" name="informpersonname[]" value="'.$v['name'].'">';
// 			    $html .='    	<input type="hidden" name="informpersonid[]" value="'.$v['id'].'">';
// 			    $html .='    	<input type="hidden" name="pic[]" value="'.$pic.'">';
// 			    $html .='		<img width="45" src="'.$pic.'" alt=""/>';
// 			    $html .='    	<span>'.$v['name'].'</span>';
// 			    $html .='     </li>';
// 			    }
// 			  }
// 	    $html .='</ul>';
//         $html.='              <div class="per_box nbm_ShowNotify ShowNotify_add">';
//         $html.='                    <a class="tml_plus_link checkUser" href="javascript:;"  checktype="notifyPerson"  ulid="showNotiy_add" data="pic,name,image;informpersonname,username,text;informpersonid,userid,hidden,1">';
//         $html.='                      <span class="tml_plus_per icon-plus"></span>';
//         $html.='                      <span>添加</span>';
//         $html.='                   </a>';
//         $html.='              </div>';
//         $html.='    	</div>';
//         $html.='    </div>';
//         $html.='</div>';
		return $html;
	}
}