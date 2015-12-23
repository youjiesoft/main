<?php
/**
 *
 * @Title: ProcessManageAction
 * @Package package_name
 * @Description: todo(流程干预)
 * @author renling
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-1-7 上午10:26:01
 * @version V1.0
 */
class ProcessManageInterventionAction extends CommonAction {
	
	public function index(){
		//组合查询条件
		$map = array();
		//获取检索过来的模型名称
		$nodename = $_REQUEST['nodename'];
		$this->assign('nodename',$nodename);
		if ($nodename)$map['tablename'] = $nodename;
		$map['ganyustate'] = array('eq',1);
		$map['auditState'] = 0;
		//查询数据
		$this->_list('process_relation_form', $map);
		
		if($_REQUEST['jump']){
			$this->display("indexview");
		}else{
			//第一步，查询所有带审批流的模型（根据分组排序来查询）
			$sql = "SELECT node.id as id,node.name as name,node.title as title,node.pid as pid FROM node AS node ,`group` AS `group` WHERE node.status = 1 AND node.isprocess = 1 AND node.level = 3 AND `group`.status = 1 AND node.group_id = `group`.id  ORDER BY `group`.`sorts`,node.pid ASC";
			$model = M();
			$NodeTreeList = $model->query($sql);
			// 第一次进来构造树
			foreach($NodeTreeList as $key=>$val){
				$nodeTree[] = array(
						'id'=>$val['id'],
						'name'=>$val['title'],//显示名称
						'title'=>$val['title'],//显示名称
						'ename'=>$val['name'], //model名称
						'pId'=>"-".$val['pid'],
						'url'=>"__URL__/index/jump/1/nodename/".$val['name'],
						'target'=>'ajax',
						'rel'=>'ProcessManageInterventionBox',
						'open'=>false
				);
				$pid[] = $val['pid'];
			}
			$pid = array_unique($pid);
			foreach ($pid as $k => $v) {
				if(getFieldBy($v, "id", "name", "node")){
					$nodeTree[] = array(
							'id'=>"-".$v,
							'name'=>getFieldBy($v, "id", "title", "node"),//显示名称
							'title'=>getFieldBy($v, "id", "title", "node"), //model名称
							'pId'=>0,
					);
				}
			}
			$this->assign('typeTree',json_encode($nodeTree));
			$this->display();
		}
	}
	
	function showContent(){
		//流程收回接受参数
		$tablename = $_POST['tablename'];
		$this->assign("tablename",$tablename);
		//获取表ID
		$tableid = $_POST['tableid'];
		$this->assign("tableid",$tableid);
		//获取流程审批节点
		$ostatus = $_POST['ostatus'];
		$this->assign("ostatus",$ostatus);
		//变更节点还是普通节点
		$catgory = $_POST['catgory']?$_POST['catgory']:0;
		$this->assign("catgory",$catgory);
		//查询当前流程节点
		$process_relation_formDao = M("process_relation_form");
		$where = array();
		$where['tablename'] = $tablename;
		$where['tableid'] = $tableid;
		$where['catgory'] = $catgory?$catgory:0;
		$where['doing'] = 1;//执行的节点
		$where['flowtype'] = array('gt','1');//判断流程类型
		$processList = $process_relation_formDao->where($where)->field("id,flowid,name,parallel,auditUser")->select();
 		//遍历循环查找到所有的串并混搭的数据 
		$process_relation_parallelDao = M("process_relation_parallel");
		$parallels = array();
		foreach($processList as $key=>$val){
			if($val['parallel']==2){
				$where = array();
				$where['relation_formid']= $val['id'];
				$listparallel = $process_relation_parallelDao->where($where)->field("id,curAuditUser,bactchname,relation_formid")->select();
				//把查询到的所有数组添加进去
				foreach($listparallel as $key=>$val){
					array_push($parallels,$val);
				}
			}
		} 
		$this->assign("parallels",$parallels);
		$this->assign("processList",$processList);
		$this->display();
	}
	
	/**
	 * @Title: ganyuProcess 
	 * @Description: todo(流程向前向后干预节点)   
	 * @author liminggang
	 * @date 2015-9-30 上午11:15:03 
	 * @throws
	 */
	public function ganyuProcess(){
		//获取当前的登录用户(干预用户)
		$userid = $_SESSION[C('USER_AUTH_KEY')];
		//流程收回接受参数
		$tablename = $_POST['tablename'];
		//获取表ID
		$tableid = $_POST['tableid'];
		//获取流程审批节点
		$ostatus = $_POST['ostatus'];
		//变更节点还是普通节点
		$catgory = $_POST['catgory']?$_POST['catgory']:0;
		//获取干预到的节点ID
		$tuiprocessid = $_POST['tuiprocessid'];
		$where = array();
		$where['tablename'] = $tablename;
		$where['tableid'] = $tableid;
		$where['catgory'] = $catgory;   //普通节点
		$where['doing'] = 1;//执行的节点
		$where['flowid'] =$tuiprocessid;
		//实例化流程审批
		$process_relation_formDao = M("process_relation_form");
		//实例化并串流程模型
		$process_relation_parallelDao = M("process_relation_parallel");
		//如果names==-1则为并 串混搭 先修改串流数据中的 
		if($_REQUEST['nameas']==-1){
			//设置一个用于串并链接字符串
			$chuanbingpingjie = "";
			$listprfwhere = $process_relation_formDao->where($where)->field("id")->find();
			$prfwhere['relation_formid'] = $listprfwhere['id'];
			$listprp = $process_relation_parallelDao->where($prfwhere)->field("id,curAuditUser")->select();
			foreach($listprp as $key=>$val){
				//判断是否有值
				if($_REQUEST['userobjStr'.$val['id']]){
				$datauser['curAuditUser'] = $_REQUEST['userobjStr'.$val['id']];
				$process_relation_parallelDao->where($val)->save($datauser);
				//判断这个链接字符串为不为空 如果为空前面就不加,隔开
					if($chuanbingpingjie==""){
						$chuanbingpingjie = $_REQUEST['userobjStr'.$val['id']];
					}else{
						$chuanbingpingjie .= ",".$_REQUEST['userobjStr'.$val['id']];
					}
				}else{
					//判断这个链接字符串为不为空 如果为空前面就不加,隔开
					if($chuanbingpingjie==""){
						$chuanbingpingjie = $val["curAuditUser"];
					}else{
						$chuanbingpingjie .= ",".$val["curAuditUser"];
					}
				}
			}
		}
		//判断是否选中更改的修改人 如果选中则更新里面的修改人
		if($_REQUEST['nameas']){
			//判断是否有值
			if($_REQUEST['userobjStr'.$tuiprocessid]){
				$datauser['curAuditUser'] = $_REQUEST['userobjStr'.$tuiprocessid];
				$datauser['auditUser'] = $_REQUEST['userobjStr'.$tuiprocessid];
				$process_relation_formDao->where($where)->save($datauser);
			}else if($chuanbingpingjie != ""){//如果串并没有杯修改 则这时也不会被修改
				$datauser['curAuditUser'] = $chuanbingpingjie;
				$datauser['auditUser'] = $chuanbingpingjie;
				$process_relation_formDao->where($where)->save($datauser);
			}else{
				$this->error("请选择更变人");
			}
		}
		//获取当前进行的节点 各种信息
		$oldnode = $process_relation_formDao->where("id = ".$ostatus)->find();
		//查询当前选中干预的节点信息，获取节点顺序，  将向前，向后追查数据
		$onevo = $process_relation_formDao->where($where)->find();
		if(count($onevo) <1){
			$this->error("节点查询错误，请联系管理员");
		}
		//更变原来的所有推送 改为0
// 		$where = array();
// 		$where['tablename'] = $tablename;
// 		$where['tableid'] = $tableid;
// 		$setbool = $process_relation_formDao->where($where)->setField("ganyustate",0);
// 		if($setbool == false){
// 			$this->error("流程干预节点状态修改失败，请联系管理员");
// 		}
		
		//向前干预。  表示需要将干预前的节点处理成完成
		$where = array();
		$where['tablename'] = $tablename;
		$where['tableid'] = $tableid;
		$where['catgory'] = $catgory; //普通流程
		$where['doing'] = 1;//执行的节点
		$where['auditState'] = 0;
		$where['sort'] = array('lt',$onevo['sort']);
		//想后干预。及向后面未审核的节点干预
		$newinfo = $process_relation_formDao->where($where)->order('sort asc')->select();
		if($newinfo){
			//存在   代表干预到后面未审批节点，向前查询未审核过的节点
			foreach($newinfo as $key=>$val){
				//代表未开始或者已推送
				$newinfo[$key]['auditState'] = 1;
				$newinfo[$key]['alreadyAuditUser'] = $userid;//将处理人修改成干预人员
				//修改流程数据
				$boolean = $process_relation_formDao->save($newinfo[$key]);
				if($boolean === false){
					$this->error("流程回退数据写入失败，请联系管理员");
				}
				//判断是否存在并串混带审批节点，将节点干预成完成
				$map = array();
				$map['relation_formid'] = $val['id'];
				$map['auditState'] = array('lt',2);
				$parbool = $process_relation_parallelDao->where($map)->setField("auditState",2);
				if($parbool == false){
					$this->error("流程批次节点干预失败，请联系管理员");
				}
			}
		}
		//处理未审核的
		$where = array();
		$where['tablename'] = $tablename;
		$where['tableid'] = $tableid;
		$where['catgory'] = $catgory; //普通流程
		$where['doing'] = 1;//执行的节点
		$where['auditState'] = 1;
		$where['sort'] = array('egt',$onevo['sort']);
		$newinfo = $process_relation_formDao->where($where)->order('sort asc')->select();
		if($newinfo){
			//处理自身为并串混带审批节的节点
			$map = array();
			$map['relation_formid'] = $ostatus;
			$paralbool = $process_relation_parallelDao->where($map)->setField("auditState",0);
			if($paralbool == false){
				$this->error("流程批次节点干预失败，请联系管理员");
			}
			//存在， 代表干预到已经审批过后的节点 。 向后查询已经审核过的节点
			foreach($newinfo as $k=>$v){
				$newinfo[$k]['auditState'] = 0;
				$newinfo[$k]['curAuditUser'] = $v['auditUser'];
				$newinfo[$k]['alreadyAuditUser'] = "";
				//修改流程数据
				$boolean = $process_relation_formDao->save($newinfo[$k]);
				if($boolean === false){
					$this->error("流程回退数据写入失败，请联系管理员");
				}
				//判断是否存在并串混带审批节点
				$map = array();
				$map['relation_formid'] = $v['id'];
				$map['auditState'] = array('gt',0);
				$paralbool = $process_relation_parallelDao->where($map)->setField("auditState",0);
			 	if($paralbool == false){
					$this->error("流程批次节点干预失败，请联系管理员");
				}
			}
		}

		//修改单据状态
		$data = array();
		$data['auditState'] = 2; 	//审核中状态
		$data['id'] = $tableid;
		$model = D($tablename);
		$result = $model->save($data);
		if($result == false){
			$this->error("单据状态修改失败，请联系管理员");
		}
		//单据打回成功，为当前单据存储历史记录信息
		$ProcessInfoHistoryModel = D ( "ProcessInfoHistory" );
		//封装审核意见参数
		$_REQUEST['dotype'] = 8;// "流程干预";
		$_REQUEST ['doinfo'] = "流程节点从【{$oldnode['name']}】强制干预到【{$onevo['name']}】";
		//封装审核意见中的审核人变更信息
		if($_REQUEST['nameas']){
			//如果有获取的ID那么就封装到里面去
			if($_REQUEST['userobjStr'.$tuiprocessid]){
				$whereuserin["_string"]="id in(".$_REQUEST['userobjStr'.$tuiprocessid].")";
				$modeluser=D("User");
				$usernamelist = $modeluser->where($whereuserin)->getField("name",true);
				$strname=implode(",",$usernamelist);
				$_REQUEST ['doinfo'] .= "更变审核人为:".$strname;
			}else if($chuanbingpingjie != ""){//如果串并没有杯修改 则这时也不会被修改
				$whereuserin["_string"]="id in(".$chuanbingpingjie.")";
				$modeluser=D("User");
				$usernamelist = $modeluser->where($whereuserin)->getField("name",true);
				$strname=implode(",",$usernamelist);
				$_REQUEST ['doinfo'] .= "更变审核人为:".$strname;
				
				$datauser['curAuditUser'] = $chuanbingpingjie;
				$datauser['auditUser'] = $chuanbingpingjie;
				$process_relation_formDao->where($where)->save($datauser);
			}else{
				$this->error("请选择更变人");
			}
		}
		$result = $ProcessInfoHistoryModel->addProcessInfoHistory($tablename,$tableid,$oldnode['id']);
		if(!$result){
			$this->error("单据历史记录保存失败，请联系管理员");
		}
		//插入工作审批数据
		$MisWorkMonitoringModel = D("MisWorkMonitoring");
		//还原待处理人员
		$onevo['alreadyAuditUser'] = "";
		$onevo['curAuditUser'] = $onevo['auditUser'];
		$result = $MisWorkMonitoringModel->addWorkMonitoring($tablename,$tableid,array($onevo),0,false);
		if(!$result){
			$this->error("单据工作审批池数据推送失败，请联系管理员");
		}
		//将原来的节点状态改变为干扰(必须要先修改成干扰 避免自己干扰自己这样的情况)
		$setbool = M("process_relation_form")->where("id = ".$ostatus)->setField("ganyustate",2);
		//推送成功，将process_relation_form表的执行的节点标记出来
		$setbool = M("process_relation_form")->where("id = ".$onevo['id'])->setField("ganyustate",1);
		
		if($setbool == false){
			$this->error("流程干预节点状态修改失败，请联系管理员");
		}
		$this->success("流程干预成功");
	}
	public function ganyuEdit(){
		//流程收回接受参数
		$tablename = $_GET['tablename'];
		$this->assign("tablename",$tablename);
		$title = getFieldBy($tablename,'name','title','node');
		$this->assign("title",$title);
		//获取表ID
		$tableid = $_GET['tableid'];
		$this->assign("tableid",$tableid);
		//获取流程审批节点
		$ostatus = $_GET['ostatus'];
		$this->assign("ostatus",$ostatus);
		//变更节点还是普通节点
		$catgory = $_GET['catgory'];
		$this->assign("catgory",$catgory);
		
		// 审批流 动态建模页面时的子表数据获取 add by nbmkxj@20150129 2214
		$viewCheckPath = LIB_PATH.'Model/'.$tablename.'ViewModel.class.php';
		if(is_file($viewCheckPath)){
			$model = D ( $tablename.'View' );
		}else{
			$model = D ( $tablename);
		}
		$map['id']=$tableid;
		$vo = $model->where($map)->find();
		//获取附件信息
		$this->getAttachedRecordList($id,true,true,$tablename);
		// 获取现 可能有的地区信息
		$areaModel = M('MisAddressRecord');
		if(C('AREA_TYPE')==1){
			$areainfomap['tablename'] = $tablename;
		}elseif(C('AREA_TYPE')==2){
			$areainfomap['tablename'] = D($tablename)->getTableName();
		}
		$areainfomap['tableid'] = $id ;
		$areaArr = $areaModel->where($areainfomap)->select();
		foreach ($areaArr as $key=>$val){
			$areainfoarry[$val['fieldname']][]=$val;
		}
		$this->assign('areainfoarry' , $areainfoarry);
		
		//lookup带参数查询
		$module=A($tablename);
		if (method_exists($module,"_after_edit")) {
			call_user_func(array(&$module,"_after_edit"),&$vo);
		}
		//读取动态配制
		$this->getSystemConfigDetail($tablename,$vo);
		$this->assign( 'vo', $vo );
		//需要抓取目标对象的html内容过来。
		$content = $this->fetch("{$tablename}:contenthtml");
		$this->assign("content",$content);
		$this->assign('tablename',$tablename);
		
		$this->display();
	}
	
	
	/**
	 * @Title: backProcess 
	 * @Description: todo(强制流程收回)   
	 * @author liminggang
	 * @date 2015-9-23 下午4:05:38 
	 * @throws
	 */
	public function backProcess(){
		//流程收回接受参数
		$tablename = $_GET['tablename'];
		//获取表ID
		$tableid = $_GET['tableid'];
		//获取流程审批节点
		$ostatus = $_GET['ostatus'];
		//变更节点还是普通节点
		$catgory = $_GET['catgory'];
		if(!$tablename || !$tableid || !$ostatus){
			$this->error("强制撤回缺少参数，请联系管理员");
		}
		if($catgory == 1){
			/**
			 * 此处还原已被修改了的单据内容
			 */
			//先查询是否存在数据恢复
			$where = array();
			$where['tablename'] = $tablename;
			$where['tableid'] = $tableid;
			$where['category'] = 1;  //变更流程
			//反向排序，取最新的一条记录
			$sqlresume = M('mis_system_data_resuming')->where($where)->order('id desc')->getField("sqlresume");
			$sqlArr    = unserialize(base64_decode($sqlresume));
			//如果存在，执行恢复
			if($sqlArr){
				foreach($sqlArr as $sqlkey => $sqlval){
					$result = M('mis_system_data_resuming')->execute($sqlval);
				}
			}
		}else{
			$model2=D($tablename);
			$data = array();
			$data['id'] = $tableid;
			$data['auditState'] = -1;//退回状态
			$data['operateid'] = 0; 	//单据未终审。
			$result = $model2->save($data);
		}
		
		if($result===false){
			$this->error("当前审批节点强制收回失败，请联系管理员");
		}else{
			//修改节点状态值 将节点表auditState修改为4，强制收回
			M("process_relation_form")->where("id ={$ostatus}")->setField("ganyustate",4);
			//单据打回成功，为当前单据存储历史记录信息
			$ProcessInfoHistoryModel = D ( "ProcessInfoHistory" );
			//封装审核意见参数
			$_REQUEST['dotype'] = 6;// "流程打回";
			$_REQUEST['doinfo'] ="当前流程被强制收回 ,如有疑问，请联系收回人 "; //处理意见
			$result = $ProcessInfoHistoryModel->addProcessInfoHistory($tablename,$tableid,$ostatus);
			if(!$result){
				$this->error("单据强制收回历史记录保存失败，请联系管理员");
			}
			//插入工作审批数据
			$MisWorkMonitoringModel = D("MisWorkMonitoring");
			$result = $MisWorkMonitoringModel->addWorkMonitoring($tablename,$tableid,array(),0,false);
			if(!$result){
				$this->error("强制收回工作池数据失败，请联系管理员");
			}
		}
		$this->success("强制收回单据流程成功。");
	}
	/**
	 * @Title: overProcess 
	 * @Description: todo(强制流程结束)   
	 * @author liminggang
	 * @date 2015-9-23 下午4:07:34 
	 * @throws
	 */
	public function overProcess(){
		//流程收回接受参数
		$tablename = $_GET['tablename'];
		//获取表ID
		$tableid = $_GET['tableid'];
		//获取流程审批节点
		$ostatus = $_GET['ostatus'];
		//变更节点还是普通节点
		$catgory = $_GET['catgory'];
		if(!$tablename || !$tableid || !$ostatus){
			$this->error("强制结束缺少参数，请联系管理员");
		}
		//先判断当前表数据是否已经完成了审批，无需强制结束，避免重复结束，执行漫游
		$list = D ( $tablename )->where ( "id = " . $tableid )->find ();
		if($list['auditState'] == 3){
			$this->error("数据超时，请刷新页面。");
		}else{
			//标记流程结束
			$_POST = array();
			$_POST ['id'] = $tableid;
			$_POST ['auditState'] = 3;
			$_POST ['operateid'] = 1;//单据终审标记
			$_POST ['ischageoperateid'] = $catgory;//1为变更审批标记，0为普通审批标记
			//保存下一审核信息数据到单据上
			$_POST ['startprocess'] = 1;
			//因为此处没有__hash__ 码过来。导致表单令牌错误
			C("TOKEN_ON",false);
			$Model = A($tablename);
			//调用此修改方法，此处必须用A实例化。不然完成不了后续的任务，必须当前单据是别人的子流程
			$Model->update ();
			C("TOKEN_ON",true);
			//修改节点状态值 将节点表auditState修改为5，强制结束
			M("process_relation_form")->where("id ={$ostatus}")->setField("ganyustate",3);
			
			//单据打回成功，为当前单据存储历史记录信息
			$ProcessInfoHistoryModel = D ( "ProcessInfoHistory" );
			//封装审核意见参数
			$_REQUEST['doinfo'] = "当前流程被强制完成 ,如有疑问，请联系操作人";
			$_REQUEST['dotype'] = 4; //"流程结束";
			$result = $ProcessInfoHistoryModel->addProcessInfoHistory($tablename,$tableid,$ostatus);
			if(!$result){
				$this->error("单据历史记录保存失败，请联系管理员");
			}
			//---------------------工作审核结束，已发办结单据状态-----------------------------//
			//插入工作审批数据
			$MisWorkMonitoringModel = D("MisWorkMonitoring");
			//代表单据撤回。将单据状态改变
			$map = array ();
			$map ['tablename'] = $tablename;
			$map ['tableid'] = $tableid;
			$map ['dostatus'] = 1;
			$workdescid= $MisWorkMonitoringModel->where($map)->order("id desc")->getField("id");
			if($workdescid){
				$result = $MisWorkMonitoringModel->where ( "id = ".$workdescid )->setField ( "auditState",3 );
				if($result == false){
					$this->error("单据历史记录保存失败，请联系管理员");
				}
			}
			//代表单据撤回。将单据状态改变
			$map = array ();
			$map ['tablename'] = $tablename;
			$map ['tableid'] = $tableid;
			$map ['dostatus'] = 0;
			$result = $MisWorkMonitoringModel->where ( $map )->delete ();
			if($result==false){
				$this->error("清理监控表数据失败，请联系管理员");
			}
			$this->success("单据强制结束成功");
		}
	}
}
?>