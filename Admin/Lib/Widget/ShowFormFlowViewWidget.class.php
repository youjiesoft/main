<?php
class ShowFormFlowViewWidget extends Widget{
	
	public function render($data){
		if($data['ShowActionName']){
			$modulename =$data['ShowActionName'];
		}else{
			$modulename = MODULE_NAME;// 当前类名
		}
		//获取流程转授用户信息
		$zhuanshoupson = $this->getZshouPserSon();
		//实例化后台用户模型
		$userModel = D("User");
		//获取节点数据
		$process_relation_formDao = M("process_relation_form");
		//批次并行
		$process_relation_parallelDao = M("process_relation_parallel");
		$where = array();
		$where['tableid'] = $data['id'];
		$where['tablename'] = $modulename;
		$where['doing'] = 1;
		$where['flowtype'] =array('gt',1);
		$relaformlist = $process_relation_formDao->where($where)->order("sort asc")->getField("relationid,flowtype,auditState,id,name,auditUser,parallel,bacthname,isaudittableid,isauditmodel");
		$createUser[] = array(
				'relationid'=>0,
				'auditUser'=>$data['createid'],
				'name'=>'制单节点',
				'auditState'=>1,
		);
		$relaformlist = array_merge($createUser,$relaformlist);
		$auditUserArr = array();
		$bool = true;
		foreach($relaformlist as $key=>$val){
			$userlist = array();
			$arr = array();
			$arr['isaudittableid'] = $val['isaudittableid'];
			$arr['name'] = $val['name'];
			$arr['isauditmodel'] = $val['isauditmodel'];
			$arr['parallel'] = $val['parallel'];
			$arr['nodeid'] = $val['id'];
			$arr['flowtype'] = $val['flowtype'];
			if($val['auditState'] == 1){
				//表示当前节点已审核
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
			if($val['parallel'] == 2){
				$person = array();
				//多批次审核人串并混搭
				$map = array ();
				$map ['tablename'] = $modulename;
				$map ['tableid'] = $data['id'];
				$map ['relation_formid'] = $val['id'];
				$relation_parallellist = $process_relation_parallelDao->where($map)->select();
				foreach ($relation_parallellist as $k1=>$v1){
					if(explode(",",$v1['curAuditUser'])){
						$map = array();
						$map['id'] = array(' in ',explode(",",$v1['curAuditUser']));
						$userlist = $userModel->where($map)->field("id,name")->select();
					}
					$arr1 = array();
					//判断颜色值
					$classval = "";
					if($v1['auditState'] == 2){
						//审核完成
						$classval = "check_current_ed";
					}
					if($v1['auditState'] == 1){
						//待审核
						$classval = "check_current";
					}
					$arr1[] = array(
							'classval'=>$classval,
							'bacthname'=>$v1['bactchname'],
							'auditUser'=>$userlist,
					);
					if(!in_array($v1['sort'], array_keys($person))){
						$person[$v1['sort']] = $arr1;
					}else{
						$person[$v1['sort']] = array_merge($person[$v1['sort']],$arr1);
					}
				}
				$arr['audituser'] = $person;
			}else{
				if(explode(",",$val['curAuditUser'])){
					$map = array();
					$map['id'] = array(' in ',explode(",", $val["auditUser"]));
					$userlist = $userModel->where($map)->field("id,name")->select();
				}
				$arr['audituser'] = $userlist;
			}
			$auditUserArr[] = $arr;
		}
		//$disableNoneModel审核信息 默认不显示状态的模块 by xyz 2015-10-28
		$disableNoneModel = array("MisAutoJds","MisAutoHxz","MisAutoXny");
		$disable = '';
		if(in_array($modulename,$disableNoneModel)){
			$disable = 'style="display:none;"';
		}
		$html = "";
		//*******************流程审核人部分********************//
		$html .='<div class="fieldset_show_box">';
		$html .='	<legend class="fieldset_legend_toggle side-catalog-text side-catalog-firstanchor">';
		$html .='		<a name="liuchengtu"></a><b>审核信息</b>';
		$html .='		<div class="tml_style_line tml_sl4 tml_slb_blue"></div>';
		$html .='	</legend>';
        $html.= '</div>';
        $html.= '<div class="fieldsetjs_show_box" '.$disable.'>';
		
        $html.='<div class="check_info"><div class="check_flow_path check_flow_path_new">';
		foreach($auditUserArr as $k=>$v){
			$html .='<div class="'.$v['classval'].'">';
			if($k > 0)$html .='<span class="forward_sign icon-double-angle-right"></span>';
			$html .='<div class="per_big_box">';
			$html .='<div class="pro_top_title">'.$v['name'].'</div>';
			//加上串并行图标
			if(count(array_keys($v['audituser']))>1){
				if($v['parallel'] >= 1){
					$html .='<i class="parallel_tips"></i>';
				}else{
					$html .='<i class="serial_tips"></i>';
				}
			}
			if($v['flowtype'] == 3){
				$html .='<div class="per_box">';
				$html .='	<img width="45" src="__PUBLIC__/Images/sub_process.jpg" alt=""/>';
				
				$name = array();
				foreach($v['audituser'] as $k1=>$v1){
					//定义转授人
					$zhuanshouren = "";
					if($zhuanshoupson){
						foreach ($zhuanshoupson as $zskey=>$zsval){
							if($zsval['zhuanshouren'] == $v1['id']){
								if($zsval['zhuanshoufanwei'] == "全部"){
									$zhuanshouren = getFieldBy($zsval['zhuanshougei'], "id", "name", "user");
								}else{
									//转授明细，暂留
										
								}
							}
						}
					}
					if($zhuanshouren){
						$str = $v1['name']."->".$zhuanshouren;
					}else{
						$str = $v1['name'];
					}
					//拼装转子流程人
					array_push($name, $str);
				}
				$name = implode(",", $name);
				if($v['parallel'] == 0){
					if($v['isaudittableid']){
						$bool = getFieldBy($v["isauditmodel"],'name','isprocess','node');
						$function = $bool?'auditView':'view';
						$html .=' <a href="__APP__/'.$v["isauditmodel"].'/'.$function.'/id/'.$v["isaudittableid"].'" title="'.$v['name'].'" target="navTab"><span>'.$v['name'].'<br/>'.$name.'</span></a>';
					}else{
						$html .=' <span>'.$v['name'].'<br/>'.$name.'</span>';
					}
				}else{
					$html .=' <span>'.$v['name'].'<br/>'.$name.'</span>';
				}
				$html .='</div>';
			}else{
				if($v['parallel'] == 2){
					foreach($v['audituser'] as $k2=>$v2){
						foreach($v2 as $kk=>$vv){
							if($kk > 0)$html .='<span class="forward_sign icon-double-angle-right"></span>';
							$html.='<div class="per_big_box per_big_box_new">';
								$html.='<div class="'.$vv['classval'].'">';
									$html.='<div class="pro_top_title">'.$vv['bacthname'].'</div>';
									foreach($vv['auditUser'] as $k3=>$v3){
										//定义转授人
										$zhuanshouren = "";
										if($zhuanshoupson){
											foreach ($zhuanshoupson as $zskey=>$zsval){
												if($zsval['zhuanshouren'] == $v3['id']){
													if($zsval['zhuanshoufanwei'] == "全部"){
														$zhuanshouren = getFieldBy($zsval['zhuanshougei'], "id", "name", "user");
													}else{
														//转授明细，暂留
															
													}
												}
											}
										}
										if($zhuanshouren){
											$str = $v3['name']."->".$zhuanshouren;
										}else{
											$str = $v3['name'];
										}
										//获取用户的头像。
										$pic = $userModel->getUserPic($v3['id']);
										$html.='<div class="per_box"><img width="45" alt="" src="'.$pic.'">   <span>'.$str.'</span></div>';
									}
								$html.='</div>';
							$html.='</div>';
						}
						$html .='<div class="clear"></div>';
					}
				}else{
					foreach($v['audituser'] as $k1=>$v1){
						//定义转授人
						$zhuanshouren = "";
						if($zhuanshoupson){
							foreach ($zhuanshoupson as $zskey=>$zsval){
								if($zsval['zhuanshouren'] == $v1['id']){
									if($zsval['zhuanshoufanwei'] == "全部"){
										$zhuanshouren = getFieldBy($zsval['zhuanshougei'], "id", "name", "user");
									}else{
										//转授明细，暂留
											
									}
								}
							}
						}
						if($zhuanshouren){
							$str = $v1['name']."->".$zhuanshouren;
						}else{
							$str = $v1['name'];
						}
						//获取用户的头像。
						$pic = $userModel->getUserPic($v1['id']);
						$html .='<div class="per_box">';
						$html .='	<img width="45" src="'.$pic.'" alt=""/>';
						$html .='   <span>'.$str.'</span>';
						$html .='</div>';
					}
				}
			}
			$html.= '</div></div>';
		}
		$html .='</div><div class="clear"></div></div>';
		$html .='<div class="clear"></div>';
		
		//获取审核意见部分信息
		$ProcessInfoHistoryModel = D("ProcessInfoHistory");
		//查询最新流程
		$pihmap = array();
		//$pihmap['ptmptid'] = $data['ptmptid'];
		$pihmap['tableid']	= $data['id'];
		$pihmap['tablename']= $modulename;
		$pihlist = $ProcessInfoHistoryModel->where($pihmap)->order('id desc')->select();
		// 过滤回退以前的数据
// 		$ftrue = true;
// 		$judge = array();
// 		foreach ($pihlist as $k2 => $v2) {
// 			$judge[] = $v2;
// 		}
		$i = 0;
        foreach($pihlist as $k3=>$v3){
//         	if($v3['ostatus']>0 || $v3['dotype'] == 5 || $v3['dotype']==8 || $v3['dotype'] == 2){
        		//获取用户的头像。
        		$pic = $userModel->getUserPic($v3['userid']);
        		
        		$html .='<div class="check_info_content">';
        		if($i == 0)$html .='<div class="file_name">审批</div><div class="tml_style_line tml_slb_red"></div>';
        		//获取职级名称
        		if($v3['ostatus']>1430000000){
        			$relationname="加签节点";
        		}else{
        			$relationname = getFieldBy($v3['ostatus'], 'id', 'name', 'process_relation_form');
        		}
        		$class = "";
        		if($v3['dotype'] == 6){
        			//打回的样式
        			$class = "tml_waring_color";
        		}
	        		$html .='<div class="check_body">';
	                $html .='	<div class="check_body_left left">';
	                $html .='		<img width="45" src="'.$pic.'" alt="">';
	                $html .='	</div>';
	                $html .='	<div class="check_body_right left">';
	                $html .='		<div class="">';
	                $html .='			<span class="author_color">'.getFieldBy($v3['userid'], 'id', 'name', 'user').'</span>';
	                $html .='			<span class="tml-mr5 time_color">【'.$relationname.'】</span>';
	                $html .='			<span class="time_color">'.transTime($v3['createtime'],'Y-m-d H:i').'</span>';
	                $html .='		</div>';
	                $html .='		<div class="check_content '.$class.'">'.$v3['doinfo'].'</div>';
	                $html .='	</div>';
	                $html .='<div class="clear"></div>';
	                $html .='</div>';
                $html .='</div>';
                $i++;
//         	}
        }
        $html .='</div>';
		return $html;
	}
	
	function getZshouPserSon(){
		//实例化流程转授模型
		$mis_auto_guikcDao = M("mis_auto_guikc");
		$where = array();
		$where['operateid'] = 1;
		$where['id']  = array('gt',0);
		$where['shengxiaoriqi'] = array('elt',time());
		$where['shixiaoriqi'] = array('egt',time());
		$zlist = $mis_auto_guikcDao->where($where)->field("id,zhuanshoufanwei,zhuanshouren,zhuanshougei")->order("id desc")->select();
		return $zlist;
	}
}