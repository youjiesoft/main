<?php
/*
 * author : zhongyong
 * date   : 2013-1-16
 * usage  : 获取审批流的审核意见信息
 */

class ShowAdvicesWidget extends Widget{
/*
 * 扩展插件：展示审核信息
 * @auther  zhongyong
 * @create time:20130113
 * @editor  yangxi
 * @update time:20130118
 * paramate  $data
 * 是一个数组：array('modelname'=>'MisPrincipalInput','tableid'=>$vo['id'], 'orderno'=>$vo['orderno'])
 * 传递3个值：(1)modelname:当前审批节点的模型名称 （2）tableid:当前单据的ID （3）orderno:当前单据的编号
 */
/*
       public function render($data){
        //首先取流程历史记录表
    	$dao = M('process_info_history');
//		$pcmodel = D('ProcessConfig');
//		//调用方法获取当前节点信息
//		$pcarr = $pcmodel->getprocessinfo($data['modelname']);
//		$pid = $pcarr['pid'];
        //获取流程历史记录
		$advices = $dao->where("tablename='{$data['modelname']}' and tableid='{$data['tableid']}'")->order('dotime asc')->select();

		//获取审核单信息
		$modeldao = D($data['modelname']);
		$result = $modeldao->where("id='{$data['tableid']}'")->find();
        //创建人信息获取环节
		$html = '<fieldset><legend class="fieldset_legend_toggle"><b>审核信息</b></legend>';
		$html .= '<p center>审核流程：'.getFieldBy($result['ptmptid'], 'id', 'name', 'process_info');	
		//$html .= '<p>创建时间：'.date('Y-m-d', $result['createtime']).'</p>';
		$html .='------';
        //当前审核状态获取
        switch($result['auditState']){
		case -1:
		  $html .= '审核状态：未批准</p>';
		  break;
		case 0:
		  $html .= '审核状态：新建</p>';
		  break;
		case 1:
		  $html .= '审核状态：待审核</p>';
		  break;
		case 2:
		  $html .= '审核状态：审核中</p>';
		  break;
		case 3:
		  $html .= '审核状态：审核完毕</p>';
		  break;
        }
//		if($result['auditState'] == 3){//审核完毕
//			$html .= '<p>审核状态：已经审核通过</p>';
//		}else if(){
//			$days = round((time() - $result['updatetime']) / (3600 * 24));
//			$html .= '<p>审核状态：至今有'.$days.'天没人审核了</p>';
//		}
        //已审核信息环节
		if(count($advices) > 0 && is_array($advices)){

			$html .= '<table class="advice">';
			$html .= '<thead><tr><th>流程节点</th><th>审核人</th><th>审核意见</th><th>审核时间</th><th>时间间隔</th></tr></thead>';
			$html .= '<tbody>';
			foreach($advices as $k => $v){
				$class = $k % 2 == 0 ? 'evenRow' : 'oddRow';
				$html .= '<tr class="'.$class.'"><td>'.getFieldBy($v['ostatus'],'id','name','process_template').'</td>';
				$html .= '<td>'.getFieldBy($v['userid'],'id','name','user').' <a title="发送消息" href="'.__APP__.'/SendMsg/index/userid/'.$v['userid'].'" target="dialog" rel="ADVICE" mask="true" class="send">&nbsp;</a></td>';
				$html .= '<td>'.$v['doinfo'].'</td>';
				$html .= '<td>'.transTime($v['dotime']).'</td>';

					//下一数组的时间记录减去当前数组时间记录
					$next=$k+1;
					if($advices[$next]['dotime']){
						$days = round(($advices[$next]['dotime'] - $advices[$k]['dotime'])/ (3600 * 24));
					}
					else{
		    			$waitdays = round((time() - $v['dotime']) / (3600 * 24));
					}
					if($k==0){
	                    $html .= '<td></td>';
					}
					else{
						$html .= '<td>等待'.$days.'天</td></tr>';
					}
				}
			}

		//下一节点审核信息环节
		if($result['auditState'] != 3){//审核完毕
				$ostatus = explode(',', $result['ostatus']);
				//$ostatus = $ostatus[0];
			foreach($ostatus as $osKey=>$osVal){
				//获取该节点模板信息：名称，类型
				$ptModel=M('process_template');
				$ptObj = $ptModel->where("id='".$osVal."'")->field('name,type')->find();
				$html .= '<tr><td>'.$ptObj['name'].'</td>';
                //获取待审核节点审核人
                $user=M('user');
                //如果节点模板类型为只看部门,type-0全部,1部门
		                if($ptObj['type']==1){
		                	//增加验证创建人部门
		                    $createid=$user->where('id='.$result['createid'])->field('dept_id')->find();
							$map['id']  = array('in', $result['curNodeUser']);
							$map['dept_id']=array('EQ',$createid['dept_id']);
							$nowUsersArray = $user->where($map)->field('id,name')->select();
							//print_r($user->getLastSql());
		                }
		                else{
							$map['id']  = array('in', $result['curNodeUser']);
							$nowUsersArray = $user->where($map)->field('id,name')->select();
		                }
		                //当前审核节点的查看
		                if($osKey==0){
		                	$html .= '<td>';
		                	foreach($nowUsersArray as $nowUsersKey => $nowUsersVal){
		                		$html .=$nowUsersVal['name'].' <a title="发送消息" href="'.__APP__.'/SendMsg/index/userid/'.$val['id'].'" target="dialog" rel="ADVICE" mask="true" class="send">&nbsp;</a>';
		                	}
		                	$html .= '</td>';                	
		                }
		                //跨级审核节点的查看		
		                if($osKey>0){	
		                	//当前跨流程节点用户
		                	$curAuditUserArray = explode(',', $result['curAuditUser']);
		                	//当前
		                	$curNodeUserArray = explode(',', $result['curNodeUser']);
		                	//获取下一节点的userid
		                	$nextUsersArray=array_diff($curAuditUserArray,$curNodeUserArray);
		                	$html .= '<td>';
		                	foreach($nextUsersArray as $nextUsersKey => $nextUsersKeyVal){
		                		$html .= getFieldBy($nextUsersKeyVal['name'],'id','name','user').' <a title="发送消息" href="'.__APP__.'/SendMsg/index/userid/'.$val['id'].'" target="dialog" rel="ADVICE" mask="true" class="send">&nbsp;</a>';
		                	}
		                	$html .= '</td>';
		                }	
		                $html .= '<td></td>';
		                $html .= '<td></td>';
		                $html .= '<td>停滞'.$waitdays.'天</td></tr>';
		           }
				}
		$html .= '</tbody></table>';
		$html .= '</fieldset>';

        return $html;
    }
    */
/*
* 扩展插件：带审批流页面提供带审核信息链接按钮
* @auther  zhongyong
* @create time:20130113
* @editor  yangxi
* @update time:20130118
* paramate  $data
*/	
	public function render($data){	
		$html .= '<a class="headProcessDetail" href="__URL__/seeProcessDetail/id/'.$data['id'].'" target="dialog" height="450" width="580" mask="true" title="流程明细查看" rel="__MODULE__seeProcessDetail" warn="请选择节点">[流程明细查看]</a>';		
		return $html;
	}
	
}
