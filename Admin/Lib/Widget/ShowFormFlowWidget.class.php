<?php
class ShowFormFlowWidget extends Widget{
	
	public function render($data){
		//变更的流程状态值
		$bgval = $_REQUEST['bgval'];
		// 当前类名
		$modulename = MODULE_NAME;
		//生成一个随机数
		$curid = rand(1000,999999).uniqid();
		$htmlid = md5($modulename.$curid);
		// 获取 所有流程 view 查看页面
		$pInfoModel = D("ProcessInfo");
		//实例化流程模型
		$ProcessManageModel = D("ProcessManage");
		//如有nodename 则查询此节点的所有流程
		$pInfoMap['status'] = 1;
		$pInfoMap['default'] = 1; //默认使用的流程
		$pInfoMap['catgory'] = 1; //使用固定流程
		if($bgval == 1){
			$pInfoMap['catgory'] = 2; //使用变更流程
		}
		$pInfoMap['nodename'] = $modulename;
		//查看多个流程简要信息
		$pInfoList = $pInfoModel->where($pInfoMap)->find();
		if(count($pInfoList)>0){
			$isComplete = array();
			if($data){
				//当数据不为空时，进行流程走向辨别
				$ProcessRelationModel = D ( "ProcessRelation" );
				// 表单流程相关
				$map['pinfoid'] = $pInfoList ['id'];
				$map['tablename'] = "process_info";
				$map['status'] = 1;
				// 获取流程节点数据
				$relationList = $ProcessRelationModel->where ( $map )->select ();
				$auditNode = $pInfoModel->getFlowAuitNode($relationList,$data,0);
				foreach ($auditNode as $k=>$v){
					array_push($isComplete, $v['flowid']);
				}
			}
			//存在流程
			$flowdata = array();
			$flowdata = json_decode($pInfoList['flowtrack'],true);
			//输出组件需要的json内容
			$jsondata = $ProcessManageModel->setDataJson($flowdata,1,$isComplete);
		}else{
			//不存在流程
			$flowdata = array();
			$flowdata[] = array("ids"=>1,"processname"=>"开始","flag"=>2,"key"=>0,"level"=>1,"processto"=>2,'prcsid'=>1,'modelname'=>$modulename);
			$flowdata[] = array("ids"=>2,"processname"=>"结束","flag"=>4,"key"=>0,"level"=>2,'prcsid'=>2,'modelname'=>$modulename);
			//输出组件需要的json内容
			$jsondata = $ProcessManageModel->setDataJson($flowdata,1);
		}
		
		$html = <<<EOF
		<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
		<script src="__PUBLIC__/Js/flow/html5.js" type="text/javascript"></script>
		<![endif]-->
		<script src="__PUBLIC__/Js/flow/jquery.jsPlumb-1.3.14-all-min.js" type="text/javascript"></script>
		<script src="__PUBLIC__/Js/flow/workflow.js" type="text/javascript"></script>
		<script type="text/javascript">
			\$(function() {
				// 等待所有script加载完再执行这个代码块
		    	var flow_data = {$jsondata};
		    	var id = '{$htmlid}';
		    	var data = flow_data.data;
		    	var connectkey = flow_data.connectkey;
		    	var conditions = flow_data.condition;
				pr.fnInit(id,data,conditions,connectkey);
		    	$(".liuchengtu").hide();
			});
		</script>
EOF;
		if($bgval == 1){
			$html .='<div class="col_1_7 form_group_lay field_huizongshuoming " >
					<label class="label_new">变更描述:</label>
					<textarea  cols="100" rows="4" class="required text_area" name="bgms"></textarea>
				</div>';
		}
		$html .='';
		$html .='<div class="fieldset_show_box">';
		$html .='	<legend class="fieldset_legend_toggle side-catalog-text side-catalog-firstanchor">';
		$html .='		<a name="liuchengtu"></a><b>流程图</b>';
		$html .='		<div class="tml_style_line tml_sl4 tml_slb_blue"></div>';
		if($bgval == 1){
			$html .='<input name="changeid" type="hidden" value="'.$pInfoList ['id'].'"/>';
		}
		$html.='	</legend>';
        $html.= '</div>';
        $html.= '<div class="fieldsetjs_show_box liuchengtu">';
		
		$html .='<div class="col_8_0">
					<div class="processgraph" oncontextmenu="return false;" style="min-height: 340px;" oncontextmenu="return true;">
						<div class="prcslist" style="position: relative;"></div>
					</div>
				</div>';
		$html .='</div>';
		
		return $html;
	}
}