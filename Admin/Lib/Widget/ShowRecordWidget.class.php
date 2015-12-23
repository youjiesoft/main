<?php
/**
 * @Title: ShowRecordWidget
 * @Package package_name
 * @Description: todo(审批记录小主键)
 * @author liminggang
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2014-6-4 上午10:34:03
 * @version V1.0
 */
class ShowRecordWidget extends Widget{
	public function render($data){
		//dump($data);
		//dump($data);exit;
		$tablename = MODULE_NAME;// 当前类名
		//当前id
		$tableid=$data['id'];
		$projectid=$data['projectid'];
		$projectworkid=$data['projectworkid'];
		//实例化后台用户模型
		$userModel = D("User");
		//获取节点数据
		$process_relation_formDao = M("process_relation_form");
		$where = array();
		$where['tableid'] = $tableid;
		$where['tablename'] = $tablename;
		$relaformlist = $process_relation_formDao->where($where)->order("sort asc")->getField("relationid,flowtype,auditState,id,name,auditUser,parallel,isaudittableid,isauditmodel");
		$auditUserArr = array();
		$bool = true;
		foreach($relaformlist as $key=>$val){
			$arr = array();
			$arr['isaudittableid'] = $val['isaudittableid'];
			$arr['name'] = $val['name'];
			$arr['isauditmodel'] = $val['isauditmodel'];
			$arr['parallel'] = $val['parallel'];
			$arr['nodeid'] = $val['relationid'];
			$arr['flowtype'] = $val['flowtype'];
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
			if( $val['flowtype'] == 3){
				$arr['audituser'] = array();
			}else{
				$map = array();
				$map['id'] = array(' in ',explode(",", $val["auditUser"]));
				$userlist = $userModel->where($map)->field("id,name")->select();
				$arr['audituser'] = $userlist;
			}
			$auditUserArr[] = $arr;
		}
		//dump($auditUserArr);
		$html = "";
		//*******************流程审核人部分********************//
		/* <div class="tml_time_line">
				<p><span class="time_line_header">该商机沟通记录：</span></p>
				<div class="timeline">
				<div class="separator"></div>
				<volist id="voList" name="voList" key="key">
				<div class="timeline-entry group_bl_color">
				<span class="time_warp">{$voList['linktime']||transtime='Y-m-d'}</span>
				<div class="metadata">
				<p>沟通人：<span class="group_per">{$voList['linkpeople']}</span><span class="icon-map-marker flower_adds"> {$voList['address']}</span></p>
				</div>
				<div class="time_line_content">
				<ul class="tlc_ul1">
				<li>
				<span>沟通主题：<span class="group_per">{$voList['title']}</span></span>
				 
				<ul class="tlc_ul1 tlc_ul2" >
				 
				<li style="list-style-type:none">{$voList['linknews']}</li>
				<!--   <li>公司规模比较大（拥有2万多头肉羊）</li>
				<li>对羊肉的品质要求比较高</li> -->
				</ul>
				</li>
				</ul>
				</div>
				</div>
				</volist>
				</div>
		 </div> */
		
		
		/* $html .='<div class="fieldset_show_box">';
		$html .='	<legend class="fieldset_legend_toggle side-catalog-text side-catalog-firstanchor">';
		$html .='		<a name="liuchengtu"></a><b>信息</b>';
		$html .='		<div class="tml_style_line tml_sl4 tml_slb_blue"></div>';
		$html .='	</legend>';
        $html.= '</div>';
        $html.= '<div class="fieldsetjs_show_box">';
		
        $html.='<div class="check_info"><div class="check_flow_path">';
		foreach($auditUserArr as $k=>$v){
			$html .='<div class="'.$v['classval'].'">';
			if($k > 0)$html .='<span class="forward_sign icon-double-angle-right"></span>';
			$html .='<div class="per_big_box">';
			//加上串并行图标
			if(count(array_keys($v['audituser']))>1){
				if($v['parallel'] == 1){
					$html .='<i class="parallel_tips"></i>';
				}else{
					$html .='<i class="serial_tips"></i>';
				}
			}
			if($v['flowtype'] == 3){
				$html .='<div class="per_box">';
				$html .='	<img width="45" src="__PUBLIC__/Images/sub_process.jpg" alt=""/>';
				if($v['isaudittableid']){
					$bool = getFieldBy($v["isauditmodel"],'name','isprocess','node');
					$function = $bool?'auditView':'view';
					$html .=' <a href="__APP__/'.$v["isauditmodel"].'/'.$function.'/id/'.$v["isaudittableid"].'" title="'.$v['name'].'" target="navTab"><span>'.$v['name'].'</span></a>';
				}else{
					$html .='   <span>'.$v['name'].'</span>';
				}
				$html .='</div>';
			}else{
				foreach($v['audituser'] as $k1=>$v1){
					//获取用户的头像。
					$pic = $userModel->getUserPic($v1['id']);
					$html .='<div class="per_box">';
					$html .='	<img width="45" src="'.$pic.'" alt=""/>';
					$html .='   <span>'.$v1['name'].'</span>';
					$html .='</div>';
				}
			}
			$html.= '</div></div>';
		}
		$html .='</div></div>';
		$html .='<div class="clear"></div>'; */
		
		//获取审核意见部分信息
		$ProcessInfoHistoryModel = D("ProcessInfoHistory");
		//查询最新流程
		$pihmap = array();
		//$pihmap['ptmptid'] = $data['ptmptid'];
		//$pihmap['tableid']	= $tableid;
		//$pihmap['tablename']= $tablename;
		$pihmap['projectid']=$projectid;
		/* if($projectworkid){
			$pihmap['projectworkid']=$projectworkid;
		} */
		$pihmap['document']=1;
		$pihlist = $ProcessInfoHistoryModel->where($pihmap)->order('createtime asc')->select();
		//dump($ProcessInfoHistoryModel->getlastsql());
		//dump($pihlist);
		// 过滤回退以前的数据
		$ftrue = true;
		$judge = array();
		foreach ($pihlist as $k2 => $v2) {
			if ($v2['dotype'] == 2) {
				continue;
			}
			if ($ftrue) {
				$judge[] = $v2;
			}
		}
		$i = 0;
		$html .='<div class="fieldset_show_box">';
		$html .='	<legend class="fieldset_legend_toggle side-catalog-text side-catalog-firstanchor">';
		$html .='		<a name="liuchengtu"></a><b>审批信息</b>';
		$html .='		<div class="tml_style_line tml_sl4 tml_slb_blue"></div>';
		$html .='	</legend>';
		$html.= '</div>';
		$html .='<div class="tml_time_line">';
		$html .='<p><span class="time_line_header">审批记录：</span></p>';
		$html .='<div class="timeline">';
		$html .='<div class="separator"></div>';
        foreach($judge as $k3=>$v3){
        	if($v3['ostatus']>0 || $v3['dotype'] == 5 || $v3['dotype']==8){
        		//dump($v3);
        		//获取职级名称
        		$dutyname = getFieldBy(getHrInfo($v3['userid'],"dutyid"), 'id', 'name', 'MisSystemDuty');
        		$html .='	<div class="timeline-entry group_bl_color">';
        		$html .='	<span class="time_warp">'.transTime($v3['dotime'],'Y-m-d H:i').'</span>';
        		$html .='	<div class="metadata">';
        		$html .='<p>审批人：<span class="group_per">'.getFieldBy($v3['userid'], 'id', 'name', 'user').'【'.$dutyname.'】</span></p>';
        		$html .='</div>';
        		$html .='<div class="time_line_content">';
        		$html .='<ul class="tlc_ul1">';
        		$html .='<li>';
        		$html .='<span>审批意见：<span class="group_per">'.$v3['doinfo'].'</span></span>';
        		$html .='<ul class="tlc_ul1 tlc_ul2" >';
        		$html .='<li style="list-style-type:none"></li>';
        		$html .='</ul>';
        		$html .='</li>';
        		$html .='</ul>';
        		$html .='</div>';
        		$html .='</div>';
        		
        		//获取用户的头像。
        	/* 	$pic = $userModel->getUserPic($v3['userid']);
        		
        		$html .='<div class="check_info_content">';
        		if($i == 0)$html .='<div class="file_name">审批</div><div class="tml_style_line tml_slb_red"></div>';
        		//获取职级名称
        		$dutyname = getFieldBy(getHrInfo($v3['userid'],"dutyid"), 'id', 'name', 'MisSystemDuty');
        		$class = "";
        		if($v3['dotype'] == 6){
        			//打回的样式
        			$class = "tml_waring_color";
        		}
        		$html .='<div class="check_body">';
                $html .='            <div class="check_body_left left">';
                $html .='                <img width="45" src="'.$pic.'" alt="">';
                $html .='            </div>';
                $html .='            <div class="check_body_right left">';
                $html .='                <div class="">';
                $html .='                    <span class="author_color">'.getFieldBy($v3['userid'], 'id', 'name', 'user').'</span>';
                $html .='                    <span class="tml-mr5 time_color">【'.$dutyname.'】</span>';
                $html .='                    <span class="time_color">'.transTime($v3['createtime'],'Y-m-d H:i').'</span>';
                $html .='                </div>';
                $html .='                <div class="check_content '.$class.'">'.$v3['doinfo'].'</div>';
                $html .='            </div>';
                $html .='       </div>';
                $html .='</div>';
                 */
                $i++;
        	}
        }
        $html .='</div>';
        $html .='</div>';
        
        $html .='</div>';
		return $html;
	}
}