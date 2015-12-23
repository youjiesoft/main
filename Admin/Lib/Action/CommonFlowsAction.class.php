<?php
/** 
 * @Title: CommonFlowsAction 
 * @Package package_name
 * @Description: todo(柔性流程继承核心类) 
 * @author 杨东 
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2014-4-9 上午9:43:36 
 * @version V1.0 
*/ 
class CommonFlowsAction extends CommonAction{
	/**
	 * @Title: commitFlows
	 * @Description: todo(初次提交审核流程函数)   
	 * @author 杨东 
	 * @date 2014-4-9 上午9:44:12 
	 * @throws 
	*/  
	public function commitFlows(){
		$aflowtrack = array();
		//判断是否是回复
		if($_REQUEST['type'] === "reply"){
			$userid = $_REQUEST['userid'];
			$username = D("user")->where("id=".$userid)->getField("name");
			$aflowtrack[] = array("id"=>0,"name"=>"开始","key"=>0,"to"=>array(1),"level"=>1);
			$aflowtrack[] = array("id"=>1,"name"=>$username,"key"=>$userid,"parents"=>array(0),"level"=>2);
			$_POST['flowtrack'] = serialize($aflowtrack);
			$_POST['dealstatus'] = 2;
		} else {
			$flowid = $_POST['flowid'];
			if(!$flowid){
				$this->error ("请先选择流程后再进行提交！");
				exit;
			}
			$mflow = D("MisOaFlows");
			// 流程记录
			$flowtrack = $mflow->where('id='.$flowid)->getField("flowtrack");
			$_POST['flowtrack'] = $flowtrack;
			$_POST['dealstatus'] = 2;
			$aflowtrack = unserialize($flowtrack);
		}
		$to = array();
		// 取出接下来要审核的人的列表
		foreach ($aflowtrack as $k => $v) {
			if($v['key'] === 0) $to = $v['to'];
		}
		$name=$this->getActionName();
		$model = D ($name);
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
			exit;
		}
		if($_POST['id']){
			if (method_exists($this,"_before_update")) {
				call_user_func(array(&$this,"_before_update"));
			}
			// 更新数据
			$list=$model->save ();
			if (method_exists($this,"_after_update")) {
				call_user_func(array(&$this,"_after_update"),$list);
			}
		} else {
			if (method_exists($this,"_before_insert")) {
				call_user_func(array(&$this,"_before_insert"));
			}
			//保存当前数据对象
			$list=$model->add ();
			if (method_exists($this,"_after_insert")) {
				call_user_func(array(&$this,"_after_insert"),$list);
			}
			$_POST['id'] = $list;
		}
		if (false !== $list) {
			$MisOaFlowsInstance = D("MisOaFlowsInstance");
			// 插入流程审核人
			foreach ($aflowtrack as $k => $v) {
				if(in_array($v['key'], $to)){
					$data = array(
							'itemsid'=>$_POST['id'],
							'itemstable'=>$name,
							'flowskey'=>$v['key'],
							'flowsuser'=>$v['id'],
							'createtime'=>time(),
							'flowsusername'=>$v['name'],
							'nextflowskey'=>$v['to']?serialize($v['to']):""
					);
					$ids = $MisOaFlowsInstance->data($data)->add($data);
					$this->commitFlowsMessage(array($v['id']), $_SESSION[C('USER_AUTH_KEY')], $_POST['title'],$ids);
				}
			}
			$this->success ( L('_SUCCESS_') ,'',$list);
		} else {
			$this->error ( L('_ERROR_') );
		}
	}
	/**
	 * @Title: commitFlowsMessage
	 * @Description: todo(提交时发送消息) 
	 * @param 用户组 $auserid
	 * @param 发布人 $douser
	 * @param 标题 $title
	 * @param 查看数据ID $ids  
	 * @author 杨东 
	 * @date 2014-4-25 上午11:40:32 
	 * @throws 
	*/  
	private function commitFlowsMessage($auserid,$douser,$title,$ids){
		//发布人
		$name = getFieldBy($douser,'id','name','user');
		//发送的系统日志的标题
		$messageTitle = $name.'给您发送了协同事项【'.$title.'】 ，请查阅审核！  '.transTime(time(),'Y-m-d H:i');
		//系统日志的内容
		$messageContent ='
			<p></p>
			<span></span>
			<div style="width:98%;">
				<p class="font_darkblue">&nbsp;您好！</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;<strong>' . $name . ' </strong>给您发送了协同事项【
				<a class="redo tml-c-blue" style="text-decoration: underline" href="__APP__/MisOaItems/audit/id/'.$ids.'" target="navTab" title="协同事项_审核" rel="MisOaItemsedit">'.$title.'</a>
				】 ，请点击链接进行查阅审核！。</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;如果您有任何问题，请联系' . $name . '。</p>
			</div>';
		//系统推送消息
		$this->pushMessage($auserid, $messageTitle, $messageContent);
	}
	/**
	 * @Title: abcd 
	 * @Description: todo(回复信息)   
	 * @author liminggang 
	 * @date 2014-7-1 上午11:09:07 
	 * @throws
	 */
	public function lookupReply(){
		//查询当前协同流程所有对象，并把当前审核信息通过邮件发送个协同创建人员。进行知会。
		$model = D($_REQUEST['model']);
		$itemsVo = $model->where("id=".$_POST['itemsid'])->find();
		
		$MisOaItemsReplyModel = D ("MisOaItemsReply");
		if (false === $MisOaItemsReplyModel->create ()) {
			$this->error ( $MisOaItemsReplyModel->getError () );
		}
		//保存当前数据对象
		$list=$MisOaItemsReplyModel->add ();
		if ($list!==false) {
			if($_POST['reply']){
				//回复信息。
				$MisOaItemsReplyModel->where('id = '.$_POST['reply'])->setField("replyid",1);
			}
			//插入成功后，进行附件的插入
			$this->swf_upload($list,99,0,'MisOaItemsReply');
			//封装邮件格式和内容
			if($_REQUEST['step'] == 2){
				$mis_oa_flows_instanceDao = M("mis_oa_flows_instance");
				//发送给审核人
				$userid = getFieldBy($_REQUEST['instanceid'],'id','flowsuser','mis_oa_flows_instance');
				//发起人回复
				$this->comFlowsMessage(array($userid), $itemsVo['title'], $_POST['instanceid'], "回复");// 发送处理消息
			}else{
				//协同人回复
				$this->doFlowsMessage(array($itemsVo['createid']), $itemsVo['title'], $_POST['itemsid'], "回复");// 发送处理消息
			}
			$this->success ( L('_SUCCESS_') ,'',$list);
			exit;
		} else {
			$this->error ( L('_ERROR_') );
		}
	}
	
	private function comFlowsMessage($auserid,$title,$ids,$type){
		//发布人
		$name = getFieldBy($_SESSION[C('USER_AUTH_KEY')],'id','name','user');
		//发送的系统日志的标题
		$messageTitle = $name.$type.'了您对【'.$title.'】 协同事项的疑问，请查阅！  '.transTime(time(),'Y-m-d H:i');
		//系统日志的内容
		$messageContent ='
			<p></p>
			<span></span>
			<div style="width:98%;">
				<p class="font_darkblue">&nbsp;您好！</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;<strong>' . $name."&nbsp;".$type. ' </strong>了您对【
				<a class="redo tml-c-blue" style="text-decoration: underline" href="__APP__/MisOaItems/audit/id/'.$ids.'" target="navTab" title="协同事项_查看" rel="MisOaItemsview">'.$title.'</a>
				】  协同事项的疑问，请点击链接进行查阅！。</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;如果您有任何问题，请联系' . $name . '。</p>
			</div>';
		//系统推送消息
		$this->pushMessage($auserid, $messageTitle, $messageContent);
	}
	
	
	/**
	 * @Title: doFlowsMessage
	 * @Description: todo(处理协同发送消息) 
	 * @param 接收人 $auserid
	 * @param 标题 $title
	 * @param 协同ID $ids
	 * @param 处理类型 $type ：“审核”、“终止”
	 * @author 杨东 
	 * @date 2014-4-25 上午11:55:21 
	 * @throws 
	*/  
	private function doFlowsMessage($auserid,$title,$ids,$type){
		//发布人
		$name = getFieldBy($_SESSION[C('USER_AUTH_KEY')],'id','name','user');
		//发送的系统日志的标题
		$messageTitle = $name.$type.'了您发送的协同事项【'.$title.'】 ，请查阅！  '.transTime(time(),'Y-m-d H:i');
		//系统日志的内容
		$messageContent ='
			<p></p>
			<span></span>
			<div style="width:98%;">
				<p class="font_darkblue">&nbsp;您好！</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;<strong>' . $name."&nbsp;".$type. ' </strong>了您发送的协同事项【
				<a class="redo tml-c-blue" style="text-decoration: underline" href="__APP__/MisOaItems/view/id/'.$ids.'" target="navTab" title="协同事项_查看" rel="MisOaItemsview">'.$title.'</a>
				】 ，请点击链接进行查阅！。</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;如果您有任何问题，请联系' . $name . '。</p>
			</div>';
		//系统推送消息
		$this->pushMessage($auserid, $messageTitle, $messageContent);
	}
	/**
	 * @Title: setDataJson
	 * @Description: todo(格式化图表生成JSON)
	 * @param 数据 $data
	 * @return string
	 * @author 杨东
	 * @date 2014-4-1 下午2:51:29
	 * @throws
	 */
	protected function setDataJson($data,$id,$isShow,$isView=0){
		// 获取最多节点的级别
		$aLevel = array();// 级别组
		$counttos = array();// 子节点数量
		foreach ($data as $k => $v) {
			$aLevel[$v['level']][] = $v['key'];
			$counttos[$v['key']] = count($v['to']);
		}
		$maxnum = 1;//最大值
		$maxlevel = 1;//最大值的级别
		foreach ($aLevel as $k => $v) {
			if ($maxnum<count($v)) {
				$maxnum = count($v);
				$maxlevel = $k;
			}
		}
		$maxtop = $maxnum*70;// 最大高度
		
		$setval = array();
		$setval[0] = array("setleft"=>80,"settop"=>$maxtop);
		$jsondata = array();
		$jsondata["data"]["total"]=count($data);// 总数
		$jsondata["data"]["type"]=1;//类型
		$jsondata["data"]["show"]=$isShow?$isShow:0;//是否有双击事件
		$keyArr = array();
		if ($isView) {
			$MisOaFlowsInstance = D("MisOaFlowsInstance");
			$map=array();
			$map['itemsid'] = $id;
			$map['itemstable'] = $_REQUEST['model']?$_REQUEST['model']:$this->getActionName();
			$keyArr = $MisOaFlowsInstance->where($map)->getField("flowskey,dostatus");
		}
		$keyArr[0] = "2";
		$n = 0;// 流程序号 必须
		$setleft = 0;
		$level = array();
// 		$data[] = array("id"=>0,"name"=>"开始","key"=>0,"level"=>1,"to"=>"","condition"=>"","left"=>"","top"=>"","parentid"=>"0");
/* 		"processname"=>"基本信息录入1",
		"processdept"=>"alldept",
		"processposition"=>"",
		"processuser"=>"",
		"processto"=>"2,3,4",
		"processcondition"=>"<5000,>10000,<1000",
		"plugin"=>"",
		"timeout"=>"",
		"signlook"=>"1",
		"row"=>"252",
		"col"=>"132",
		"setleft"=>"250",
		"settop"=>"110",
		"timestr"=>"2分钟46秒",
		"flag"=>"4",
		"runchild"=>"0",
		"prcsid"=>1,
		"parentid"=>0
 */		
		foreach ($data as $k => $v) {
			//$to = "";// 连接节点
			if(empty($v['top'])){
				if(in_array($v['level'],array_keys($level))){
					$countto = count($level[$v['level']]);
					$settop -= $countto*55;
					$level[$v['level']][] = $v['level'];
				}else{
					$settop = $maxtop;
					$level[$v['level']] = array($v['level']);
				}
			}else{
				$settop = $v['top'];
			}
			$setleft = empty($v['left']) ? $v['level']*150-70 : $v['left'];
			$val = array();
			$val["ids"] = $id;//当前流程ID
			$val["processname"] = $v['name'];//节点名称
			$val["processuser"] = $v['id'];//节点用户ID
			// 节点状态
			if($keyArr[$v['key']]) {
				$val["flag"] = $keyArr[$v['key']];
			}else if($v['id'] =='999999'){
				$val["flag"] = 3;
			} else {
				$val["flag"] = 4;
			}
			$val["prcsid"] = $v['key'];// 节点序号
			$val["setleft"] = $setleft;//节点高度
			$val["settop"] = $settop;//节点位置
					$to = "";// 连接节点
			if($v['to']){
				$to = implode(",", $v['to']);// 逗号分割链接节点
			}
			$val["processto"] = $to;

			$condition = "";// 连接节点
			if($v['condition']){
				$condition = implode(",", $v['condition']);// 逗号分割链接节点
			}
			$val["processcondition"] = $condition;

			$keys = strval($n+1);
			$n ++;
			$jsondata["data"]["list"][$keys] = $val;
		}
		if(!empty($jsondata["data"]["list"])){
			$jsondata["condition"] = array();
			foreach ($jsondata["data"]["list"] as $flow_val) {
				$node_config	= trim($flow_val['processto']);
				$node_condition	= trim($flow_val['processcondition']);
				if(sizeof($node_config) && sizeof($node_condition)){
					$arr_node_config	= explode(',', $node_config);
					$arr_node_condition	= explode(',', $node_condition);
					foreach($arr_node_config as $conf_key=>$conf_val){
						$jsondata["condition"][$flow_val['prcsid'].'-'.$conf_val] = $arr_node_condition[$conf_key];
					}
				}
			}
		}
		return json_encode($jsondata);
	}
	/**
	 * @Title: lookupShowFlows
	 * @Description: todo(显示流程)   
	 * @author 杨东 
	 * @date 2014-4-11 上午10:09:08 
	 * @throws 
	*/  
	public function lookupShowFlows(){
		$id = $_POST["id"];
		$name = "MisOaFlows";
		$model = D ( $name );
		$map['id']=$id;
		$vo = $model->where($map)->find();
		$this->getSystemConfigDetail($name);//动态配置文件
		$data = unserialize($vo['flowtrack']);
		$this->assign( 'vo', $vo );
		// 如果修改界面则弹到修改页面
		if($_POST['edit']){
			// 进入修改页面
			unset($_SESSION["flowsdata"]);
			$_SESSION["flowsdata"] = $data;
			$jsondata = $this->setDataJson($data,$vo['id']);
			$this->assign("data",$jsondata);
			$this->display ("CommonFlows:editFlows");
		} else {
			// 进入查看页面
			$jsondata = $this->setDataJson($data,$vo['id'],1);
			$this->assign("data",$jsondata);
			$this->display ("CommonFlows:showFlows");
		}
	}
	/**
	 * @Title: lookupFlowsView
	 * @Description: todo(流程监控)   
	 * @author 杨东 
	 * @date 2014-4-22 下午2:24:05 
	 * @throws 
	*/  
	public function lookupFlowsView(){
		$id = $_REQUEST["id"];
		$name = $_REQUEST["model"];
		$model = D ( $name );
		$map['id']=$id;
		$flowtrack = $model->where($map)->getField("flowtrack");
		$data = unserialize($flowtrack);
		$jsondata = $this->setDataJson($data,$id,1,1);
		
		$this->assign("data",$jsondata);
		$this->display ("CommonFlows:flowsView");
	}
	/**
	 * @Title: lookupAddFlows
	 * @Description: todo(打开快速新增页面)   
	 * @author 杨东 
	 * @date 2014-4-11 上午10:21:14 
	 * @throws 
	*/  
	public function lookupAddFlows(){
		$this->getSystemConfigDetail("MisOaFlows");
		$data = array();
		$data[] = array("id"=>0,"name"=>"开始","key"=>0,"level"=>1);
		unset($_SESSION["flowsdata"]);
		$_SESSION["flowsdata"] = $data;
		$jsondata = $this->setDataJson($data,0);
		$this->assign("data",$jsondata);
		$this->display("CommonFlows:addFlows");
	}
	/**
	 * @Title: lookupInsertFlows
	 * @Description: todo(快速插入流程)   
	 * @author 杨东 
	 * @date 2014-4-11 上午10:26:33 
	 * @throws 
	*/  
	public function lookupInsertFlows(){
		if(count($_SESSION["flowsdata"]) <=1 ){
			$this->error ("请先添加流程节点再进行流程保存！");
			exit;
		}
		$_POST["flowtrack"] = serialize($_SESSION["flowsdata"]);
		unset($_SESSION["flowsdata"]);
		$name = "MisOaFlows";
		$model = D ($name);
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		//保存当前数据对象
		$list=$model->add ();
		if ($list!==false) {
			$this->success ( L('_SUCCESS_') ,'',$list);
			exit;
		} else {
			$this->error ( L('_ERROR_') );
		}
	}
	/**
	 * @Title: lookupUpdateFlows
	 * @Description: todo(快速保存流程)   
	 * @author 杨东 
	 * @date 2014-4-11 上午10:29:06 
	 * @throws 
	*/  
	public function lookupUpdateFlows(){
		if(count($_SESSION["flowsdata"]) <=1 ){
			$this->error ("请先添加流程节点再进行流程保存！");
			exit;
		}
		$_POST["flowtrack"] = serialize($_SESSION["flowsdata"]);
		
		unset($_SESSION["flowsdata"]);
		$id = $_REQUEST['id'];
		if(!$id){
			unset($_POST['id']);	
		}
		$name = "MisOaFlows";
		$model = D ( $name );
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		if($id){
			// 更新数据
			$list=$model->save ();
			if (false !== $list) {
				$this->success ( L('_SUCCESS_'),'',$id);
			} else {
				$this->error ( L('_ERROR_') );
			}
		}else{
			// 更新数据
			$list=$model->add ();
			if (false !== $list) {
				$this->success ( L('_SUCCESS_'),'',$list);
			} else {
				$this->error ( L('_ERROR_') );
			}
		}
	}
	/**
	 * @Title: lookupRefreshSelect
	 * @Description: todo(快速修改/插入流程刷新流程)   
	 * @author 杨东 
	 * @date 2014-4-11 上午10:31:11 
	 * @throws 
	*/  
	public function lookupRefreshSelect(){
		$MisOaFlows = D("MisOaFlows");
		$map['status'] = 1;
		$map['_string'] = "(createid = ".$_SESSION[C("USER_AUTH_KEY")]." or isshare = 1)";
		$flowsList = $MisOaFlows->where($map)->select();
		$this->assign("flowsList",$flowsList);
		$this->display("CommonFlows:refreshSelect");
	}
	/**
	 * @Title: auditEdit
	 * @Description: todo(打开审核)   
	 * @author 杨东 
	 * @date 2014-4-22 下午2:19:07 
	 * @throws 
	*/  
	public function auditEdit(){
		$this->display();
	}
	/**
	 * @Title: auditProcess
	 * @Description: todo(审核节点)   
	 * @author 杨东 
	 * @date 2014-4-22 下午2:18:53 
	 * @throws 
	*/  
	public function auditProcess(){
		$id = $_REQUEST['id'];
		$itemsid = $_REQUEST['itemsid'];
		$model = D($_REQUEST['model']);
		$model->where("id=".$itemsid)->setField("updatetime",time());
		$_POST['dostatus'] = 2;
		$MisOaFlowsInstance = D("MisOaFlowsInstance");
		if (false === $MisOaFlowsInstance->create ()) {
			$this->error ( $MisOaFlowsInstance->getError () );
		}
		// 更新数据
		$list=$MisOaFlowsInstance->save();
		if (false !== $list) {
			$this->swf_upload($id,91,0,"MisOaFlowsInstance");
			$this->setNextProcess($id, $itemsid);
			$this->success ( L('_SUCCESS_'));
		} else {
			$this->error ( L('_ERROR_') );
		}
	}
	/**
	 * @Title: setNextProcess
	 * @Description: todo(设置下一个审核节点)   
	 * @author 杨东 
	 * @date 2014-4-22 上午10:31:45 
	 * @throws 
	*/  
	private function setNextProcess($id,$itemsid){
		//查询当前协同流程所有对象，并把当前审核信息通过邮件发送个协同创建人员。进行知会。
		$model = D($_REQUEST['model']);
		$itemsVo = $model->where("id=".$itemsid)->find();
		$this->doFlowsMessage(array($itemsVo['createid']), $itemsVo['title'], $itemsid, "审核");// 发送处理消息
		$aflowtrack = unserialize($itemsVo['flowtrack']);
		//查询下一协同对象
		$MisOaFlowsInstance = D("MisOaFlowsInstance");
		$instanceVo = $MisOaFlowsInstance->where("id=".$id)->find();
		$anextflowskey = unserialize($instanceVo['nextflowskey']);
		//获取加签对象
		$flowUserid = $_POST['flowUserid'];
		
		if($flowUserid){
			$newflowUserid = array($flowUserid);
			//判断是否进行到最后一个人加签
			if($anextflowskey){
				//将两个数据进行对比。判断是否存在新的加签对象。
				$newflowUserid=array_diff(array($flowUserid),$anextflowskey);
			}
			if($newflowUserid){
				//获取当前的level
				$level = 1;
				$parents = $aflowtrack[$anextflowskey[0]]['parents'];
				$to = array();
				//构造加签后的流程走向。
				foreach($aflowtrack as $key=>$val){
					if($instanceVo['flowskey'] == $val['key']){
						$level = $val['level'];
						//替换to
						$aflowtrack[$key]['to'] = array(count($aflowtrack));
						$to = $val['to'];
					}
				}
				foreach($aflowtrack as $k1=>$v1){
					if($v1['level'] > $level){
						//追加后的协同人员的level都要加1 。因为添加协同只有一个人。
						$aflowtrack[$k1]['level']+=1;
					}
					if(in_array($v1['key'], $to)){
						$aflowtrack[$k1]['parents'][] = count($aflowtrack);
					}
				}
				//组合新的协同人员
				$a = array(
						'id'=>$newflowUserid[0],
						'name'=>"(加签)".getFieldBy($newflowUserid[0],'id','name','user'),
						'key'=>count($aflowtrack),
						'parents'=>$parents,
						'to'=> $to,
						'level'=>$level+1,
						);
				//存在加签对象。
				$anextflowskey = array(count($aflowtrack));
				
				$aflowtrack[count($aflowtrack)] = $a;
				//修改整条流程信息
				$model->where("id=".$itemsid)->setField("flowtrack",serialize($aflowtrack));
				//修改下次审核人员
				$MisOaFlowsInstance->where("id=".$id)->setField("nextflowskey",serialize($anextflowskey));
			}
		}
		foreach ($anextflowskey as $k => $v) {
			$data = array();
			$data['itemsid'] = $itemsid;
			$data['itemstable'] = $instanceVo['itemstable'];
			foreach ($aflowtrack as $k1 => $v1) {
				if($v1['key'] == $v){
					$data['flowskey'] = $v1['key'];
					$data['flowsuser'] = $v1['id'];
					$data['createtime']= time();
					$data['flowsusername'] = $v1['name'];
					$data['nextflowskey'] = $v1['to']?serialize($v1['to']):"";
				}
			}
			$map['itemsid'] = $itemsid;
			$map['flowskey'] = $data['flowskey'];
			$map['itemstable'] = $instanceVo['itemstable'];
			$count = $MisOaFlowsInstance->where($map)->count("id");
			
			if($count < 1) {
				$ids = $MisOaFlowsInstance->data($data)->add($data);
				// 发送审核消息
				$this->commitFlowsMessage(array($data['flowsuser']), $_SESSION[C('USER_AUTH_KEY')], $itemsVo['title'], $ids);
			}


		}
	}
	/**
	 * @Title: stopProcess
	 * @Description: todo(终止流程)   
	 * @author 杨东 
	 * @date 2014-4-22 下午3:31:23 
	 * @throws 
	*/  
	public function stopProcess(){
		$id = $_REQUEST['id'];
		$itemsid = $_REQUEST['itemsid'];
		$model = D($_REQUEST['model']);
		$model->where("id=".$itemsid)->setField("updatetime",time());
		$_POST['dostatus'] = 3;
		$MisOaFlowsInstance = D("MisOaFlowsInstance");
		if (false === $MisOaFlowsInstance->create ()) {
			$this->error ( $MisOaFlowsInstance->getError () );
		}
		// 更新数据
		$list=$MisOaFlowsInstance->save();
		if (false !== $list) {
			$this->swf_upload($id,91,0,"MisOaFlowsInstance");
			$model = D($_REQUEST['model']);
			$itemsVo = $model->where("id=".$itemsid)->find();
			$this->doFlowsMessage(array($itemsVo['createid']), $itemsVo['title'], $itemsid, "终止");// 发送处理消息
			$this->success ( L('_SUCCESS_'));
		} else {
			$this->error ( L('_ERROR_') );
		}
	}
	/**
	 * @Title: lookupSetflow
	 * @Description: todo(树选择节点后保存函数)   
	 * @author 杨东 
	 * @date 2014-4-11 上午10:52:54 
	 * @throws 
	*/  
	public function lookupSetflow(){
		$model = D("MisOaFlows");
		$data = array();
		$data = $_SESSION["flowsdata"];
		$key = $_POST['key'];
		$userid = $_POST['userid'];
		$username = $_POST['username'];
		// 替换流程审核人
		if ($_REQUEST['type'] == "replace") {
			foreach ($data as $k => $v) {
				if($v['key'] == $key) {
					$data[$k]['id'] = $userid;
					$data[$k]['name'] = $username;
				}
			}
			// 存入内存中
			$_SESSION["flowsdata"] = $data;
			$jsondata = $this->setDataJson($data,$_POST['ids']);
			echo $jsondata;
			exit;
		}
		// 添加流程审核人
		$parents = array();// 父类数组
		$num = 0;// 计算最后节点的key值
		$level = 1;// 流程节点级别
		foreach ($data as $k => $v) {
			if($v['key'] == $key) {
				if($v["parents"]) $parents = $v["parents"];
				$level = $v['level'];
			}
			if($num < $v["key"]) {
				$num = $v["key"];
			}
		}
		$count = $num+count($userid)+1;// 最后节点的总数量
		$num = $num+1;// 节点的开始数量
		$to = array();
		$condition = array();
		$newdata = array();
		$parents[] = $key;
		// 添加新节点 （倒序）
		foreach ($userid as $k => $v) {
			if ($_POST['selectflowtype'] == 1) {
				if (count($to)<1) $to = array($num);
				if (count($condition)<1) $condition = array("test".$num);
				$level = $level+1;
				if($num+1 == $count){
					// 		$data[] = array("id"=>0,"name"=>"开始","key"=>0,"level"=>1,"to"=>"","condition"=>"","left"=>"","top"=>"","parentid"=>"0");
					$newdata[] = array("id"=>$v,"name"=>$username[$v],"key"=>$num,"parents"=>array_unique($parents),"level"=>$level);
				} else {
					$newdata[] = array("id"=>$v,"name"=>$username[$v],"key"=>$num,"to"=>array($num+1),"parents"=>array_unique($parents),"level"=>$level);
				}
				if ($num) $parents[] = $num;
			} else {
				$to[] = $num;
				$condition[] = "test".$num;
				$newdata[] = array("id"=>$v,"name"=>$username[$v],"key"=>$num,"parents"=>array_unique($parents),"level"=>$level+1);
			}
			$num = $num + 1;
		}
		// 合并两个数据
		$data = array_merge($data,$newdata);
		foreach ($data as $k => $v) {
			if($v['key'] == $key) {
				if($v['to']) $to = array_merge($v['to'],$to);
				if($v['condition']) $condition = array_merge($v['condition'],$condition);
				$data[$k]["to"] = $to;
				$data[$k]['condition'] = $condition;
			}
		}
		// 存入内存中
		$_SESSION["flowsdata"] = $data;
		$jsondata = $this->setDataJson($data,$_POST['ids']);
		echo $jsondata;
	}
	
	/**
	 * @Title: lookupSelectFlowUser
	 * @Description: todo(打开添加用户界面)   
	 * @author 杨东 
	 * @date 2014-4-11 下午1:52:49 
	 * @throws 
	*/  
	public function lookupSelectFlowUser(){
		$this->setUserTree();
		if($_REQUEST['type'] == 'replace'){
			$data = $_SESSION["flowsdata"];
			$key = $_REQUEST['key'];
			$user = array();
			foreach ($data as $k => $v) {
				if($key == $v['key']){
					$user = $v;
				}
			}
			$this->assign("user",$user);
			$this->display("CommonFlows:lookupReplaceFlowUser");
		} else {
			$this->display("CommonFlows:lookupSelectFlowUser");
		}
	}
	/**
	 * @Title: setUserTree
	 * @Description: todo(生成用户树)   
	 * @author 杨东 
	 * @date 2014-4-23 下午3:06:36 
	 * @throws 
	*/  
	protected function setUserTree(){
		$model=D("User");
		$map = array();
		$map['status'] = array('GT',0);
		//是管理员的不显示出来
		//$map['name'] = array('NEQ','管理员');
		if (method_exists ( $this, '_filterLookupSelectUser' )) {
			$this->_filterLookupSelectUser ( $map );
		}
		$list = $model->field("id,name,dept_id,email,mobile,pinyin")->where($map)->order('sort ASC')->select();
		foreach ($list as $uk=>$uval){
			if($uval['employeid']){
				$working=getFieldBy($uval['employeid'], 'id', 'working', 'mis_hr_personnel_person_info');
				if($working==0){
					unset($list[$uk]);
				}
			}
		}
		$num = count($list);
		$this->assign("num",$num);// 总人数
		$returnarr = array();
		$dptmodel = D("MisSystemDepartment");//部门表
		$deptlist = $dptmodel->where("status=1")->order("id asc")->field('id,name,parentid')->select();
		//部门树形
		$returnarr=$dptmodel->getDeptZtree($_SESSION['companyid'],'','','','',1);
		$this->assign('usertree',$returnarr);
		//用户组的树
		$rolegroup = array();
		$rolegroupModel = D('Rolegroup');
		$rolegroupList = $rolegroupModel->where("status=1")->order("id asc")->field('id,name,pid')->select();//所有的组
		$rolegroup_userModel = M('rolegroup_user');
		$rolegroup_userList = $rolegroup_userModel->field("rolegroup_id,user_id")->order('rolegroup_id ASC')->select();
		foreach ($rolegroupList as $k => $v) {
			foreach ($rolegroup_userList as $k2 => $v2) {
				if($v["id"] == $v2["rolegroup_id"]){
					$rolegroupList[$k]["useridarr"][] = $v2["user_id"];
				}
			}
		}
		foreach($rolegroupList as $ke=>$va){
			$newRole=array();
			$newRole['id'] = -$va['id'];
			$newRole['pId'] = 0;
			$newRole['title'] = $va['name']; //光标提示信息
			$newRole['name'] = missubstr($va['name'],18,true); //结点名字，太多会被截取,针对于现在的ZTREE，宽度不能超过18个字符。
			$newRole['open'] = false;
			$istrue = false;
			$userarr = array();
			$usernamearr = array();
			$emailarr = array();
			foreach ($list as $k2 => $v2) {
				if(in_array($v2['id'],$va["useridarr"])){
					$istrue = true;
					$newv2 = array();
					$userarr[] = $v2['id'];
					$usernamearr[] = $v2['name'];
					$emailarr[] = $v2['email'];
					$newv2['email'] = $v2['email'];
					$newv2['id'] = $v2['id'];
					$newv2['userid'] = $v2['id'];
					$newv2['pId'] = -$va['id'];
					$newv2['pinyin'] = $v2['pinyin']; //拼音
					$newv2['title'] = $v2['name']; //光标提示信息
					$newv2['username'] = $v2['name']; //光标提示信息
					$newv2['name'] = missubstr($v2['name'],18,true); //结点名字，太多会被截取,针对于现在的ZTREE，宽度不能超过18个字符。
					$newv2['icon'] = "__PUBLIC__/Images/icon/group.png";
					$newv2['open'] = false;
					array_push($rolegroup,$newv2);
				}
			}
			if($istrue){
				$newRole["userid"] = implode(",",$userarr);
				$newRole["email"] = implode(",",$emailarr);
				$newRole["username"] = implode(",",$usernamearr);
				array_push($rolegroup,$newRole);
			}
		}
// 		dump($rolegroup);
		$this->assign('rolegrouptree',json_encode($rolegroup));
		//公司的树
		$companytree=$dptmodel->getDeptZtree('','','','','',2);
		$this->assign('sysCompanytree',$companytree);
// 		$this->assign('ProcessRoletree',json_encode($ProcessRole));
	}
	/**
	 * @Title: lookupLinkFlowUser
	 * @Description: todo(连接节点)   
	 * @author 杨东 
	 * @date 2014-5-5 上午11:32:49 
	 * @throws 
	*/  
	public function lookupLinkFlowUser(){
		$key = $_REQUEST['key'];
		$data = $_SESSION["flowsdata"];
		$filterkeys = array($key);
		// 第一步：获取需要过滤的数据（过滤父级节点和下级节点）
		foreach ($data as $k => $v) {
			if ($v['key'] == $key) {
				if ($v['parents']) $filterkeys = array_merge($filterkeys,$v['parents']);
				if ($v['to']) $filterkeys = array_merge($filterkeys,$v['to']);
			}
		}
		// 第二步：获取可以选择的流程节点
		$list = array();
		foreach ($data as $k => $v) {
			if (in_array($v['key'], $filterkeys)) {
				continue;
			}
			$list[] = array("key"=>$v['key'],"name"=>$v['name']);
		}
		$this->assign("list",$list);
		$this->assign("key",$key);
		$this->assign("ids",$_REQUEST['ids']);
		$this->display("CommonFlows:lookupLinkFlowUser");
	}
	/**
	 * @Title: lookupUpdateLinkFlows
	 * @Description: todo(连接节点保存)   
	 * @author 杨东 
	 * @date 2014-5-5 下午5:01:32 
	 * @throws 
	*/  
	public function lookupUpdateLinkFlows(){
		$data = $_SESSION["flowsdata"];
		$key = $_POST['key'];
		$linkkeys = $_POST["linkkeys"];
		$parents = array($key);
		foreach ($data as $k => $v) {
			if($key == $v['key']) {
				if($v['parents']) $parents = array_merge($parents,$v['parents']);
				if($v['to']) {
					$data[$k]["to"] = array($v['to'],$linkkeys);
				} else {
					$data[$k]["to"] = $linkkeys;
				}
			}
			if (in_array($v['key'], $linkkeys)) {
				if($v['parents']) {
					$parents = array_merge($parents,$v['parents']);
					$data[$k]["parents"] = array_unique($parents);
				} else {
					$data[$k]["parents"] = $parents;
				}
			}
		}
		unset($_SESSION["flowsdata"]);
		$_SESSION["flowsdata"] = $data;
		$json = $this->setDataJson($data, $_POST["ids"]);
		$this->success ( L('_SUCCESS_') ,'',$json);
		exit;
	}
	/**
	 * @Title: lookupDeleteFlowUser
	 * @Description: todo(这里用一句话描述这个方法的作用)   
	 * @author 杨东 
	 * @date 2014-5-5 下午5:03:48 
	 * @throws 
	*/  
	public function lookupDeleteFlowUser(){
		$data = $_SESSION["flowsdata"];
		$key = $_POST['key'];
		// 第一步：获取需要删除的所有节点
		$removekey = array($key);
		foreach ($data as $k => $v) {
			if(in_array($key, $v['to'])){
				$data[$k]['to'] = array_diff($v['to'],array($key));
			}
			if($v['key'] == $key) {
				if($v['to']) {
					$parents = $v['parents'];
					$parents[] = $key;
					$this->removeFlowsData($data, $parents, $v['to'],$removekey);
				}
			}
		}
		// 移除当前节点后的节点
		foreach ($data as $k => $v) {
			if (count($removekey)>0 && in_array($v["key"], $removekey)) {
				unset($data[$k]);
			}
		}
		$ids = $_POST["ids"];
		$_SESSION["flowsdata"] = $data;
		$json = $this->setDataJson($data, $ids);
		$this->success ( L('_SUCCESS_') ,'',$json);
	}
	/**
	 * @Title: removeFlowsData
	 * @Description: todo(这里用一句话描述这个方法的作用)
	 * @param 当前数据 $data
	 * @param 需要删除的数组 $to
	 * @param 需要移除的数组key集合 $removekey
	 * @author 杨东
	 * @date 2014-4-11 下午1:43:45
	 * @throws
	 */
	private function removeFlowsData(&$data,$parents,$to,&$removekey){
		foreach ($data as $k => $v) {
			if (in_array($v["key"], $to)) {
				if ($v['parents'] == $parents) {
					$newparents = $v['parents'];
					$newparents[] = $v["key"];
					$removekey[] = $v["key"];
					if($v['to']) $this->removeFlowsData($data, $newparents, $v['to'],$removekey);
				} else {
					$parents = array_diff($parents,array(0));
					$data[$k]['parents'] = array_diff($v["key"],$parents);
				}
			}
		}
	}
	/**
	 * @Title: lookupCustomFlow 
	 * @Description: todo(用户自定义流程方法)   
	 * @author liminggang 
	 * @date 2014-6-6 下午5:57:00 
	 * @throws
	 */
	public function lookupCustomFlow(){
		//获取当前登录人可用的自定义流程，和别人共享的流程。	
		$md = $_REQUEST['md'];
		$this->assign('md',$md);
		//获取当前登录人
		$userid = $_SESSION[C('USER_AUTH_KEY')];
		$flowMap['tablename'] = $md;
		$flowMap['createid'] =$userid;
		$MisOaFlowsDao = M("mis_oa_flows");
		$flowslist=$MisOaFlowsDao->where($flowMap)->field("id,title,flowtrack")->select();
		$this->assign('flowslist',$flowslist);
		//获得是否已经存在自定义的流程了。如果存在直接修改当前自定义流程即可
		$flowid = $_REQUEST["flowid"];
		if($flowid){
			//存在自定义流程，直接查询旧流程数据
			$name = "MisOaFlows";
			$model = D ( $name );
			$map['id']=$flowid;
			$vo = $model->where($map)->find();
			$this->assign( 'vo', $vo );
			//将流程走向数据解析。
			$data = unserialize($vo['flowtrack']);
		}else{
			//不存在，构造一个开始节点
			$data[] = array("id"=>0,"name"=>"开始","key"=>0,"level"=>1);
		}
		//将原来的session清空，重新存入新的流程走向session
		unset($_SESSION["flowsdata"]);
		$_SESSION["flowsdata"] = $data;
		// 进入查看页面
		$jsondata = $this->setDataJson($data);
		$this->assign("data",$jsondata);
		$this->display ("editFlowsWidget");
	}
	
	function lookupChooseFlow(){
		//获得是否已经存在自定义的流程了。如果存在直接修改当前自定义流程即可
		$flowid = $_REQUEST["flowid"];
		if($flowid){
			//存在自定义流程，直接查询旧流程数据
			$name = "MisOaFlows";
			$model = D ( $name );
			$map['id']=$flowid;
			$vo = $model->where($map)->find();
			$this->assign( 'vo', $vo );
			//将流程走向数据解析。
			$data = unserialize($vo['flowtrack']);
		}else{
			//不存在，构造一个开始节点
			$data[] = array("id"=>0,"name"=>"开始","key"=>0,"level"=>1,"to"=>"","condition"=>"","left"=>"","top"=>"","parentid"=>"0");
		}
		//将原来的session清空，重新存入新的流程走向session
		unset($_SESSION["flowsdata"]);
		$_SESSION["flowsdata"] = $data;
		var_dump($data);
		echo '444444444444444';
		exit;
		// 进入查看页面
		$jsondata = $this->setDataJson($data);
		$this->assign("data",$jsondata);
		$this->display ("chooseFlowWidget");
	}
	
	/**
	 * @Title: lookupFixFlow 
	 * @Description: todo(还原以前流程)   
	 * @author liminggang 
	 * @date 2014-7-3 上午9:19:07 
	 * @throws
	 */
	public function lookupFixFlow(){
		//第一步，获取当前类名称
		$modulename = $_REQUEST['md'];// 当前类名
		//第二步，获取审核节点
		$pcmodel = D('ProcessConfig');
		$pcarr =  $pcmodel->getprocessinfo($modulename,$data);
		//获取流程节点ID
		$pid = $pcarr['pid'];
		if($pid){
			//存在流程
			$ProcessTemplateModel = M("process_template");
			$sql = " and process_relation.pid= ".$pid;
			$info = $ProcessTemplateModel->table('process_template as process_template,process_relation as process_relation')->where('process_template.id=process_relation.tid '.$sql)->field('parallelid,parallel,process_relation.tid as tid,process_template.name as name,process_relation.userid as userid,process_relation.duty as duty')->order('process_relation.sort')->select();
			$aflowtrack[] = array("id"=>0,"name"=>"开始","key"=>0,"level"=>1);
			$count = 1;
			foreach($info as $key=>$val){
				++$key;
				if($val['parallel'] == 1){
					//向前并行
					$aflowtrack[] = array(
							'id'=>$val['tid'],
							'name'=>$val['name'],
							'key'=>$key,
							'level'=>$count,
					);
	
				}else if($val['parallel'] == 2){
					//向后并行
					$aflowtrack[] = array(
							'id'=>$val['tid'],
							'name'=>$val['name'],
							'key'=>$key,
							'level'=>$count+1,
					);
				}else{
					$parents = array('0'=>'0');
					//非并行
					$aflowtrack[] = array(
							'id'=>$val['tid'],
							'name'=>$val['name'],
							'key'=>$key,
							'parents'=>$parents,
							'level'=>$count+1,
					);
					$count++;
				}
			}
			$c=count($info)+1;
			$aflowtrack[] = array("id"=>'999999',"name"=>"结束","key"=>$c,"level"=>$count+1);
			$flowdata = array();
			foreach($aflowtrack as $k=>$v){
				$flowdata[$k]['id'] = $v['id'];
				$flowdata[$k]['name'] = $v['name'];
				$flowdata[$k]['key'] = $v['key'];
				$flowdata[$k]['level'] = $v['level'];
				$to = array();
				$parents = array();
				foreach($aflowtrack as $k1=>$v1){
					if($v['level']+1 == $v1['level']){
						array_push($to,$k1);
					}
				}
				if($to){
					$flowdata[$k]['to'] = $to;
				}
			}
		}else{
			$flowdata[] = array("id"=>0,"name"=>"开始","key"=>0,"to"=>array(1),"level"=>1);
			$flowdata[] = array("id"=>1,"name"=>"结束","key"=>1,"level"=>2);
		}
		$jsondata = $this->setDataJson($flowdata,0,1);
		$this->assign("data",$jsondata);
		$this->display("showFixFlowsWidget");
	}
}
?>