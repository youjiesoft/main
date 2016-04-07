<?php
/**
 *
 * @Title: ProcessManageAction
 * @Package package_name
 * @Description: todo(流程管理)
 * @author renling
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-1-7 上午10:26:01
 * @version V1.0
 */
class ProcessManageAction extends CommonAction {
	
	public function index(){
		//第一步，查询所有带审批流的模型（根据分组排序来查询）
		$sql = "SELECT node.id as id,node.name as name,node.title as title,node.pid as pid FROM node AS node ,`group` AS `group` WHERE node.status = 1 AND node.isprocess = 1 AND node.level = 3 AND `group`.status = 1 AND node.group_id = `group`.id  ORDER BY `group`.`sorts`,node.pid ASC";
		$model = M();
		$NodeTreeList = $model->query($sql);
		
		//默认选中第一个节点
		$nodeId = $NodeTreeList[0]['id'];
		//传送默认选中节点id
		$this->assign("nodeId",$nodeId);
		
		//获取检索过来的模型名称
		$nodename = $_REQUEST['nodename']?$_REQUEST['nodename']:$NodeTreeList[0]['name'];
		$this->assign('nodename',$nodename);
		
		//组合查询条件
		$map = array();
		$map['nodename'] = $nodename;
		//查询数据
		$this->_list('ProcessInfo', $map);
		
		if($_REQUEST['jump']){
			$this->display("indexview");
		}else{
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
						'rel'=>'ProcessManageBox',
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
	
	public function add(){
		$nodename = $_REQUEST['nodename'];
		$this->assign("nodename",$nodename);
		//获取表单字段配置
		$scdmodel = D('SystemConfigDetail');
		//读取列名称数据(按照规则，应该在index方法里面)
		$detailList = $scdmodel->getDetail($nodename,true,'','sortnum');
		$this->assign("detailList",$detailList);
		//不存在流程
		$data = array();
		$data[] = array("ids"=>1,"processname"=>"开始","flag"=>2,"key"=>0,"level"=>1,"choose"=>0,'prcsid'=>1,'modelname'=>$nodename);
		unset($_SESSION["flowsdata"]);
		//为session存储内容，方便读取
		$_SESSION["flowsdata"] = $data;
		//实例化流程模型
		$ProcessManageModel = D("ProcessManage");
		//输出组件需要的json内容
		$jsondata = $ProcessManageModel->setDataJson($data);
		$this->assign("data",$jsondata);
		//查询流程分类信息
		$mis_auto_ziqmiDao = M("mis_auto_ziqmi");
		$typelist = $mis_auto_ziqmiDao->field("id,leixingmingchen")->select();
		$this->assign("typelist",$typelist);
		$this->display();
	}
	
	public function edit(){
		// 获取 所有流程 view 查看页面
		$pInfoModel = D("ProcessInfo");
		//实例化流程模型
		$ProcessManageModel = D("ProcessManage");
		$pInfoMap['status'] = 1;
		//如有nodename 则查询此节点的所有流程
		$pInfoMap['id'] = $_REQUEST['id'];
		//查看多个流程简要信息
		$pInfoList = $pInfoModel->where($pInfoMap)->order('id desc')->find();
		$this->assign("pInfoVo",$pInfoList);
		//获取表单字段配置
		$scdmodel = D('SystemConfigDetail');
		//读取列名称数据(按照规则，应该在index方法里面)
		$detailList = $scdmodel->getDetail($pInfoList['nodename'],true,'','sortnum');
		$this->assign("detailList",$detailList);
		//存在流程
		$data = array();
		$data = json_decode($pInfoList['flowtrack'],true);
		unset($_SESSION["flowsdata"]);
		//为session存储内容，方便读取
		$_SESSION["flowsdata"] = $data;
		//输出组件需要的json内容
		$jsondata = $ProcessManageModel->setDataJson($data);
		$this->assign("data",$jsondata);
		//查询流程分类信息
		$mis_auto_ziqmiDao = M("mis_auto_ziqmi");
		$typelist = $mis_auto_ziqmiDao->field("id,leixingmingchen")->select();
		$this->assign("typelist",$typelist);
		$this->display();
	}
	
	/**
	 * @Title: getProcessNodeArr 
	 * @Description: todo(根据传入的流程节点，获取所有上级审批节点) 
	 * @param 所有流程节点数据 $nodelist
	 * @param 当前选中的流程节点 $nodid  
	 * @param 类型，新增或者修改 $type  1为新增2位修改
	 * @author liminggang
	 * @date 2015-8-15 下午3:59:31 
	 * @throws
	 */
	private function getProcessNodeArr($nodelist,$nodinfo,$nodearr = array()){
		if($nodinfo){
			//循环所有流程节点，根据当前选中节点，查找上级
			foreach($nodelist as $key=>$val){
				if(in_array($nodinfo['ids'],explode(",", $val['processto']))){
					$nodearr[] = $val;
					return $this->getProcessNodeArr($nodelist, $val,$nodearr);
				}
			}
		}
		return $nodearr;
	}
	
	
	public function lookupAddProcessRelation(){
		//获取当前节点所有信息数据
		$row = $_REQUEST['row'];		
		$this->assign("row",$row);
		//获取流程全部节点
		$data = $_SESSION["flowsdata"];
		//递归获取所有上级可以退回的节点
		$newarr = $this->getProcessNodeArr($data, $row,array($row));
		 
		foreach($newarr as $key=>$val){
			if($val['choose'] == 1 || $val['choose'] == 0){
				unset($newarr[$key]);
			}
		}
		$this->assign("datalist",$newarr);
		//获取节点的模型名称
		$this->assign("modelname",$row['modelname']);
		//选人类型表
		$MisSystemUserObjModel = D("MisSystemUserObj");
		$userObj = $MisSystemUserObjModel->where(" status = 1")->select();
		$this->assign("userObj",$userObj);
 		
		$this->display();
	}
	
	public function lookupAddChildSonFlows(){
		//获取当前节点所有信息数据
		$step = $_REQUEST['step'];
		$row = $_REQUEST['row'];
		if($step !=1){
			if($row['processaudit']){
				//定义审核人信息变量
				$processaudit = $row['processaudit'];
				//对审核信息进行转换
				$userobjid = explode(";",$processaudit['userobjid']);
				$userobjidname = explode(";",$processaudit['userobjidname']);
				$userobj = explode(";",$processaudit['userobj']);
				$userobjname = explode(";",$processaudit['userobjname']);
				
				$htmlrule = str_replace("&#39;", "'", html_entity_decode($processaudit['rule']));
				$htmlrulename = str_replace("&#39;", "'", html_entity_decode($processaudit['rulename']));
				$rule = explode(";",$htmlrule);
				$rulename = explode(";",$htmlrulename);
				
				$bactch = explode(";",$processaudit['bactch']);
				$rulesinfo = explode(";",$processaudit['rulesinfo']);
				$recipient = explode(",",$processaudit['recipient']);
				$recipientname = explode(",",$processaudit['recipientname']);
				$email = explode(",",$processaudit['email']);
				foreach($userobjid as $key=>$val){
					$arr = array();
					$arr['userobjid'] = $val;
					$arr['userobjidname'] = $userobjidname[$key];
					$arr['userobj'] = $userobj[$key];
					$arr['userobjname'] = $userobjname[$key];
					$arr['rule'] = $rule[$key];
					$arr['rulename'] = $rulename[$key];
					$arr['bactch'] = $bactch[$key];
					$arr['rulesinfo'] = $rulesinfo[$key];
					$bacharr[] = $arr;
				}
				$recipientarr = array();
				foreach($recipient as $k=>$v){
					$temp = array();
					$temp['recipient'] = $v;
					$temp['recipientname'] = $recipientname[$k];
					$temp['email'] = $email[$k];
					$recipientarr[$k] = $temp;
				}
			}
			if($row['processrulesinfo']){
				$this->assign("choose",1);
			}
			//获取节点的模型名称
			$this->assign('recipientarr',$recipientarr);
			$this->assign("bacharr",$bacharr);
			$this->assign("modelname",$row['modelname']);
			$this->assign("key",$row['prcsid']);
			$this->assign("ids",$row['ids']);
			$this->assign("row",$row);
			//查询漫游配置信息
			$map['sourcemodel'] = $row['issourcemodel'];
			$map['targetmodel'] = $row['isauditmodel'];
			$model = D("MisSystemDataRoamMas");
			$list = $model->field('id,title')->where($map)->select();
			$this->assign("mylist",$list);
			$this->assign("function","lookupUpdate");
		}else{
			//获取节点的模型名称
			$this->assign("modelname",$row['modelname']);
			$this->assign("key",$row['prcsid']);
			$this->assign("ids",$row['ids']);
			$this->assign("function","lookupInsert");
			$this->assign("choose",$row['choose']);
			$this->assign("nextId",$row['processto']);
			$this->assign("nextAll",$row['processall']);
			$this->assign("nextChoose",$row['choose']);
		}
		//查询3级节点表数据
		$nodeModel = M("node");
		$where =array();
		$where['level'] = 3;
		$where['status'] =1;
		$nodelist = $nodeModel->where($where)->field("name as id,title as name")->select();
		$this->assign("nodelist",$nodelist?json_encode($nodelist):"[]");
		//选人类型表
		$MisSystemUserObjModel = D("MisSystemUserObj");
		$userObj = $MisSystemUserObjModel->where(" status = 1")->select();
		$this->assign("userObj",$userObj);
		//获取流程模板
		$this->display();
	}
	public function ajaxGetRoam(){
		$map['sourcemodel'] = $_POST['sourcemodel'];
		$map['targetmodel'] = $_POST['targetmodel'];
		//$map['isbindsettable'] = 0;
		$model = D("MisSystemDataRoamMas");
		$list = $model->field('id,title')->where($map)->select();
		exit(json_encode($list));
	}
	/**
	 * @Title: lookupEditBeginFlows
	 * @Description: todo(修改开始节点名称)   
	 * @author 黎明刚 
	 * @date 2016年4月6日 下午12:00:03 
	 * @throws
	 */
	public function lookupEditBeginFlows(){
		//获取当前节点所有信息数据
		$row = $_REQUEST['row'];
		$this->assign("row",$row);
		$this->display();
	}
	
	/**
	 * @Title: lookupEditProcessRelation
	 * @Description: todo(流程节点修改)   
	 * @author 黎明刚
	 * @date 2015年1月24日 上午11:09:32 
	 * @throws
	 */
	public function lookupEditProcessRelation(){
		//审核人类型选择
		$MisSystemUserObjModel = D("MisSystemUserObj");
		$userObj = $MisSystemUserObjModel->where(" status = 1")->select();
		$this->assign("userObj",$userObj);
		//获取当前节点所有信息数据
		$row = $_REQUEST['row'];
		//获取流程全部节点
		$data = $_SESSION["flowsdata"];
		//递归获取所有上级可以退回的节点
		$newarr = $this->getProcessNodeArr($data, $row);
		
		foreach($newarr as $key=>$val){
			if($val['choose'] == 1 || $val['choose'] == 0){
				unset($newarr[$key]);
			}
		}
		$this->assign("datalist",$newarr);
		
		//获取知会人
		$informpersonid = $row['informpersonid'];
		$this->assign("userlist",$informpersonid);
		$bacharr = array();
		if($row['processaudit']){
			//定义审核人信息变量
			$processaudit = $row['processaudit'];
			//对审核信息进行转换
			$bactchname =explode(";",$processaudit['bactchname']);
			$userobjid = explode(";",$processaudit['userobjid']);
			$userobjidname = explode(";",$processaudit['userobjidname']);
			$userobj = explode(";",$processaudit['userobj']);
			$userobjname = explode(";",$processaudit['userobjname']);
			
			$htmlrule = str_replace("&#39;", "'", html_entity_decode($processaudit['rule']));
			$htmlrulename = str_replace("&#39;", "'", html_entity_decode($processaudit['rulename']));
			$rule = explode(";",$htmlrule);
			$rulename = explode(";",$htmlrulename);
			$bactch = explode(";",$processaudit['bactch']);
			$rulesinfo = explode(";",$processaudit['rulesinfo']);
			$recipient = explode(",",$processaudit['recipient']);
			$recipientname = explode(",",$processaudit['recipientname']);
			$email = explode(",",$processaudit['email']);
			foreach($userobjid as $key=>$val){
				$arr = array();
				$arr['bactchname'] = $bactchname[$key];
				$arr['userobjid'] = $val;
				$arr['userobjidname'] = $userobjidname[$key];
				$arr['userobj'] = $userobj[$key];
				$arr['userobjname'] = $userobjname[$key];
				$arr['rule'] = $rule[$key];
				$arr['rulename'] = $rulename[$key];
				$arr['bactch'] = $bactch[$key];
				$arr['rulesinfo'] = $rulesinfo[$key];
				$bacharr[] = $arr;
			}
		}
		$this->assign("bacharr",$bacharr);
		$this->assign("row",$row);
		$this->display();
	}
	/**
	 * @Title: lookupInsert 
	 * @Description: 添加下级节点时，session数据封装，组装成需要的格式数据   
	 * @author liminggang 
	 * @date 2014-9-18 下午2:51:49 
	 * @throws
	 */
	public function lookupInsert(){
		//实例化流程模型
		$ProcessManageModel = D("ProcessManage");
		//获取流程节点名称
		$relaname=$_POST['process_relation_name'];
		//获取是否存在退回节点
		$istuihui = $_POST['istuihui'];
		//1为判定节点，0为审批节点
		$choose = $_REQUEST['choose'];
		//是否自动生单1是0否
		$isauto = $_POST['isauto']?$_POST['isauto']:0;
		//是否并行
		$parallel = $_POST['parallel']?$_POST['parallel']:0;
		//是否手机审批
		$ismobile=$_POST['ismobile']?$_POST['ismobile']:0;
		//是否生成文档
		$document=$_POST['document']?$_POST['document']:0;
		//获取批次名称
		$bactchname =implode(";",$_POST['bactchname']);
		//获取批次和条件类型
		$userobjid =implode(";",$_POST['userobjid']);
		$userobjidname =implode(";",$_POST['userobjidname']);
		//获取条件类型ID
		$userobj = implode(";",$_POST['userobj']);
		$userobjname = implode(";",$_POST['userobjname']);
		//获取批次条件
		$rulename = implode(";",$_POST['rulename']);
		$rule = implode(";",$_POST['rule']);
		//获取批次信息
		$bactch = implode(";",$_POST['bactch']);
		//获取批次加密信息
		$rulesinfo = implode(";",$_POST['rulesinfo']);
		$informpersonid = array();
		if($_POST['recipient'] || $_POST['groupid']){
			//依次获取知会人id
			$informpersonid = array(
					'recipientname'=>$_POST['recipientname'],
					'recipient'=>$_POST['recipient'],
					'groupname'=>$_POST['groupname'],
					'groupid'=>$_POST['groupid'],
					'sysselectuser'=>$_POST['sysselectuser'],
			);
		}
		/*
		 * 批次信息
		 */
		$processaudit=array(
				'bactchname'=>$bactchname,
				'parallel'=>$parallel,
				'isauto'=>$isauto,
				'document'=>$document,
				'ismobile'=>$ismobile,
				'userobjid'=>$userobjid,
				'userobjidname'=>$userobjidname,
				'userobj'=>$userobj,
				'userobjname'=>$userobjname,
				'rule'=>$rule,
				'rulename'=>$rulename,
				'bactch'=>$bactch,
				'rulesinfo'=>$rulesinfo,
		);
		//获取当前节点条件
		$newprocesscondition= $_REQUEST['processcondition'];//"今晚打老虎";
		$newprocessrules= $_REQUEST['processrules'];//"今晚打老虎"; sql型  a = '今晚打老虎'
		$newprocessrulesinfo = str_replace("=",'',$_REQUEST['processrulesinfo']);//"今晚打老虎";数组型  $data['a'] = "今晚打老虎"
		//获取当前的level级数(作用：上下级线条关联关系)
		$prcsid = $_REQUEST['prcsid'];
		//获取先前存储在session中的数据
		$data = array();
		$data = $_SESSION["flowsdata"];
		
		//定义一个变量，存储原始的下级节点的ID
		$nextId = "";
		if($_REQUEST['nextId']){
			$nextId = $_REQUEST['nextId'];
		}
		$nextAll = "";
		if($_REQUEST['nextAll']){
			$nextAll = $_REQUEST['nextAll'];
		}
		$nextChoose =$_REQUEST['nextChoose'];
		//
		$exceptionArr=array(1,4);
		//计算总节点数量
		$k = array_slice($data,-1,1);
		$nd = $k[0]['ids']+1;
		$level = 1;
		
		$newprocessall = "";
		$newprocessto = "";
		/*
		 * 循环当前已存在的流程节点
		 * 1、获取当前新增节点的父级ID
		 */
		foreach($data as $key=>$val){
			
			/*
			 * 第一步，处理与上级节点的关系
			 */
			if($prcsid == $val['prcsid']){
				//当前节点等级
				$level = $val['level']+1;
				
				
				if(in_array($val['choose'], $exceptionArr)){
					/*
					 * 组合上下级关联的id值
					 */
					$processto = array_filter(explode(",", $val['processto']));
					if($processto){
						array_push($processto, $nd);
					}else{
						$processto = array($nd);
					}
				}else{
					$processto = array($nd);
				}
				$data[$key]['processto'] = implode(",", $processto);
 
				/*
				 * 组合上下级节点条件
				 * 只有判定节点才会存在上下级关系条件
				 */
				$processcondition = array_filter(explode("#@#", $val['processcondition']));
				$processrules = array_filter(explode("#@#", $val['processrules']));
				$processrulesinfo = array_filter(explode(",", $val['processrulesinfo']));
				//显示条件
				if($newprocesscondition){
					if($processcondition){
						array_push($processcondition, $newprocesscondition);
					}else{
						$processcondition = array($newprocesscondition);
					}
					$data[$key]['processcondition'] = implode("#@#", $processcondition);
				}
				//sql型条件
				if($newprocessrules){
					if($processrules){
						array_push($processrules, $newprocessrules);
					}else{
						$processrules = array($newprocessrules);
					}
					$data[$key]['processrules'] = implode("#@#", $processrules);
				}
				//数组型条件
				if($newprocessrulesinfo){
					if($processrulesinfo){
						array_push($processrulesinfo, $newprocessrulesinfo);
					}else{
						$processrulesinfo = array($newprocessrulesinfo);
					}
					$data[$key]['processrulesinfo'] = implode(",", $processrulesinfo);
				}
			}//与上级关系处理结束
			if($nextId &&  !in_array($nextChoose, $exceptionArr) && in_array($val['ids'], explode(",", $nextAll))){
				/*
				 * 
				 * 此处进行中间节点插入
				 * 第一步、中间节点插入，不可能在判定节点，所以：排除判定节点
				 * 第二步、寻找当前节点原来的下级节点数据
				 * 第三步、将当前节点的下级processall节点存入当前新插入的节点中
				 */
				$data[$key]['level'] +=1;
				$newprocessall = $nextAll;
				$newprocessto = $nextId;
			}
			/*
			 * 第二步。将当前节点ID存储到所有上级的processall字段中
			 */
			if($val['choose'] == 1){
				//判定节点,存储所有判定节点一下的所有子节点
				$processall = array_filter(explode(";", $val['processall']));
				if($prcsid == $val['prcsid']){
					//是当前判定节点，则进行分号分隔
					if($processall){
						array_push($processall, $nd);
					}else{
						$processall = array($nd);
					}
				}else{
					//不是当前判定节点。则判定属于哪个判定节点内的数据
					foreach($processall as $ndkey=>$ndval){
						$newndval = explode(",", $ndval);
						if(in_array($prcsid, $newndval)){
							array_push($newndval, $nd);
							$processall[$ndkey] = implode(",", $newndval);
						}
					}
				}
				$data[$key]['processall'] = implode(";", $processall);
			}else{
				//判定节点,存储所有判定节点一下的所有子节点
				$processall = array_filter(explode(",", $val['processall']));
				if($prcsid == $val['prcsid']){
					//是当前判定节点，则进行分号分隔
					if($processall){
						array_push($processall, $nd);
					}else{
						$processall = array($nd);
					}
				}else{
					//不是当前判定节点。则判定属于哪个判定节点内的数据
					if(in_array($prcsid, $processall)){
						array_push($processall, $nd);
					}
				}
				$data[$key]['processall'] = implode(",", $processall);
			}
		}
		/*
		 * 需要参数，及说明
		 * ids:唯一表示，标记每个流程节点的key
		 * processname:节点名称
		 * flag:节点颜色值  区间1-7
		 * key:节点序号
		 * prcsid:节点序号
		 * processto :子节点ID组
		 * parents：父节点ID组
		 * level：当前节点等级
		 * choose 类别  0开始节点1判定节点2审批节点3子流程节点
		 */
		if($choose == 1){
			//判定节点
			$data[] = array("ids"=>$nd,"processname"=>$relaname,"flag"=>3,'prcsid'=>$nd,"key"=>$nd,"level"=>$level,"choose"=>1,'modelname'=>$_REQUEST['modelname']);
		}else if($choose == 4){
			//并行节点
			$data[] = array("ids"=>$nd,"processname"=>$relaname,"flag"=>4,'prcsid'=>$nd,"key"=>$nd,"level"=>$level,"choose"=>4,'modelname'=>$_REQUEST['modelname']);
		}else if($choose == 3){
			//子流程
			$data[] = array("ids"=>$nd,"processall"=>$newprocessall,"processto"=>$newprocessto,"processname"=>$relaname,"flag"=>1,'prcsid'=>$nd,"key"=>$nd,"level"=>$level,'informpersonid'=>$informpersonid,
					'processcondition'=>$newprocesscondition,'processrules'=>$newprocessrules,'processrulesinfo'=>$newprocessrulesinfo,
					'istuihui'=>$istuihui,'issourcemodel'=>$_REQUEST['issourcemodel'],'isauditmodel'=>$_REQUEST['isauditmodel'],'processaudit'=>$processaudit,'roamid'=>$_REQUEST['roamid'],"choose"=>3,'modelname'=>$_REQUEST['modelname']);
		}else{
			//普通节点
			$data[] = array("ids"=>$nd,"processall"=>$newprocessall,"processto"=>$newprocessto,"processname"=>$relaname,"flag"=>2,'prcsid'=>$nd,"key"=>$nd,"level"=>$level,'informpersonid'=>$informpersonid,
					'processcondition'=>$newprocesscondition,'processrules'=>$newprocessrules,'processrulesinfo'=>$newprocessrulesinfo,
					'istuihui'=>$istuihui,'processaudit'=>$processaudit,"choose"=>2,'modelname'=>$_REQUEST['modelname']);
		}
		// 存入内存中
		$_SESSION["flowsdata"] = $data;
		$jsondata = $ProcessManageModel->setDataJson($data);
		echo $jsondata;
	}
	
	public function lookupAddOrEditField(){
		//实例化流程模型
		$ProcessManageModel = D("ProcessManage");
		$ids = $_REQUEST['ids'];
		//获取不可编辑数据
		$filterWritSetEmpty = $_REQUEST['filterWritSetEmpty'];
		//获取不可查看的
		$filterReadSetEmpty = $_REQUEST['filterReadSetEmpty'];
		//获取先前存储在session中的数据
		$data = array();
		$data = $_SESSION["flowsdata"];
		/*
		 * 循环当前已存在的流程节点
		 * 1、获取当前新增节点的父级ID
		 */
		foreach($data as $key=>$val){
			if($val['ids'] == $ids){
				//进行替换当前内容
				$data[$key]['filterwritsetempty'] = implode(",", array_keys($filterWritSetEmpty));
				$data[$key]['filterreadsetempty'] = implode(",", array_keys($filterReadSetEmpty));
				break;
			}
		}
		// 存入内存中
		$_SESSION["flowsdata"] = $data;
		$jsondata = $ProcessManageModel->setDataJson($data);
		echo $jsondata;
	}
	
	public function lookupUpdate(){
		//实例化流程模型
		$ProcessManageModel = D("ProcessManage");
		//获取流程节点名称
		$relaname=$_POST['process_relation_name'];
		//获取是否存在退回节点
		$istuihui = $_POST['istuihui'];
		//1为判定节点，0为审批节点
		$choose = $_REQUEST['choose'];
		//是否并行
		$parallel = $_POST['parallel']?$_POST['parallel']:0;
		//是否手机审批
		$ismobile=$_POST['ismobile']?$_POST['ismobile']:0;
		//是否生成文档
		$document=$_POST['document']?$_POST['document']:0;
		//获取批次名称
		$bactchname =implode(";",$_POST['bactchname']);
		//获取批次和条件类型
		$userobjid =implode(";",$_POST['userobjid']);
		$userobjidname =implode(";",$_POST['userobjidname']);
		//获取条件类型ID
		$userobj = implode(";",$_POST['userobj']);
		$userobjname = implode(";",$_POST['userobjname']);
		//获取批次条件
		$rulename = implode(";",$_POST['rulename']);
		$rule = implode(";",$_POST['rule']);
		//获取批次信息
		$bactch = implode(";",$_POST['bactch']);
		//获取批次加密信息
		$rulesinfo = implode(";",$_POST['rulesinfo']);
		$informpersonid = array();
		if($_POST['recipient'] || $_POST['groupid']){
			//依次获取知会人id
			$informpersonid = array(
					'recipientname'=>$_POST['recipientname'],
					'recipient'=>$_POST['recipient'],
					'groupname'=>$_POST['groupname'],
					'groupid'=>$_POST['groupid'],
					'sysselectuser'=>$_POST['sysselectuser'],
			);
		}
		$processaudit=array(
				'bactchname'=>$bactchname,
				'parallel'=>$parallel,
				'document'=>$document,
				'ismobile'=>$ismobile,
				'userobjid'=>$userobjid,
				'userobjidname'=>$userobjidname,
				'userobj'=>$userobj,
				'userobjname'=>$userobjname,
				'rule'=>$rule,
				'rulename'=>$rulename,
				'bactch'=>$bactch,
				'rulesinfo'=>$rulesinfo,
		);
		//获取当前节点条件
		$newprocesscondition= $_REQUEST['processcondition'];//"今晚打老虎";
		$newprocessrules= $_REQUEST['processrules'];//"今晚打老虎"; sql型  a = '今晚打老虎'
		$newprocessrulesinfo = str_replace("=",'',$_REQUEST['processrulesinfo']);//"今晚打老虎";数组型  $data['a'] = "今晚打老虎"
		
		//获取当前的level级数(作用：上下级线条关联关系)
		$prcsid = $_REQUEST['prcsid'];
		$ids = $_REQUEST['ids'];
		//获取先前存储在session中的数据
		$data = array();
		$data = $_SESSION["flowsdata"];
		/*
		 * 循环当前已存在的流程节点
		 * 1、获取当前新增节点的父级ID
		 */
		foreach($data as $key=>$val){
			$processto =  explode(",", $val['processto']);
			if(in_array($ids,$processto) && $val['choose'] == 1){
				//判定节点为父级，修改了条件，就要变更判定的条件
				$processcondition =  explode("#@#", $val['processcondition']);
				$processrules =  explode("#@#", $val['processrules']);
				$processrulesinfo =  explode(",", $val['processrulesinfo']);
				foreach($processto as $tokey=>$toval){
					if($toval == $ids){
						$processcondition[$tokey] = $newprocesscondition;
						$processrules[$tokey] = $newprocessrules;
						$processrulesinfo[$tokey] = $newprocessrulesinfo;
					}
				}
				$data[$key]['processcondition'] = implode("#@#", $processcondition);
				$data[$key]['processrules'] = implode("#@#", $processrules);
				$data[$key]['processrulesinfo'] = implode(",", $processrulesinfo);
			}
			if($val['ids'] == $ids){
				//进行替换当前内容
				$data[$key]['processname'] = $relaname;
				$data[$key]['processcondition'] = $newprocesscondition;
				$data[$key]['processrules'] = $newprocessrules;
				$data[$key]['processrulesinfo'] = $newprocessrulesinfo;
				$data[$key]['processaudit'] = $processaudit;
				$data[$key]['informpersonid'] = $informpersonid;
				$data[$key]['istuihui'] = $istuihui;
				if($val['choose'] == 3){
					$data[$key]['isauditmodel'] = $_REQUEST['isauditmodel'];//目标模型
					$data[$key]['issourcemodel'] = $_REQUEST['issourcemodel'];//子流程漫游来源模型
					$data[$key]['roamid'] = $_REQUEST['roamid'];
				}
				break;
			}
		}
		// 存入内存中
		$_SESSION["flowsdata"] = $data;
		$jsondata = $ProcessManageModel->setDataJson($data);
		echo $jsondata;
	}
	
	/**
	 * @Title: insert
	 * @Description: 流程节点插入，和流程节点审核人信息的插入
	 * @author liminggang
	 * @date 2014-9-18 下午2:51:49
	 * @throws
	 */
	public function insert(){
		//实例化流程节点模板过滤
		$MisFieldFilterModel = D("MisFieldFilter");
		//模型名称
		$nodename = $_POST['nodename'];
		//获取流程所有数据
		$workflowVal = $_REQUEST['workflowVal'];
		$flowdata = array();
		foreach($workflowVal as $key=>$val){
			//组合流程节点格式
			$flowdata[] = json_decode(html_entity_decode($val),true);
		}
		//流程节点数据表
		$ProcessRelationModel = D("ProcessRelation");
		//查询当前模型是否存在其他流程
		$process_infoDao = D("processInfo");
 		//获取流程信息
		if($_POST['id']){//修改方法
			//组合流程数据
			$name = $_POST['name'];
			$data['id'] = $_POST['id'];
			$data['name'] = $name;
			$data['catgory'] = $_POST['catgory'];
			$data['typeid'] = $_POST['typeid'];
			$data['flowtrack'] = json_encode($flowdata);
			$data['title'] = $_POST['title'];
			$data['updatetime'] = time();
			$data['default'] = $_POST['default']?$_POST['default']:'';
			$data['updateid'] = $_SESSION[C('USER_AUTH_KEY')];
			$addresult = $process_infoDao->save($data);
			if($addresult === false){
				$this->error("流程操作失败，请联系管理员");
			}
			//成功重新赋值为数据ID
			$addresult = $_POST['id'];
			//先删除原有的流程批次信息，在进行新增
			$map=array();
			$map['pinfoid']=$addresult;
			$map['tablename']="process_info";
			$relaids = $ProcessRelationModel->where($map)->getField("id,pinfoid");
			if($relaids){
				//删除知会人员信息
				$mis_system_selectuserDao = M("mis_system_selectuser");
				$map = array();
				$map['modelname'] = "ProcessManage";
				$map['tableid'] = array(' in ',array_keys($relaids));
				$result = $mis_system_selectuserDao->where($map)->delete();
				if(!$result){
					$this->error("数据清理失败，请联系管理员");
				}
				//流程审核人数据表mis_system_user_bactch
				$MisSystemUserBactchModel = D("MisSystemUserBactch");
				$map = array();
				$map['id'] = array(' in ',array_keys($relaids));
				$resultrela=$ProcessRelationModel->where($map)->delete();
				$map = array();
				$map['tablename'] = 'process_relation';
				$map['tableid'] = array(' in ',array_keys($relaids));
				$resultbactch=$MisSystemUserBactchModel->where($map)->delete();
				if(!$resultrela || !$resultbactch){
					$this->error("数据清理失败，请联系管理员");
				}
			}
		}else{
			//流程名称
			$where = array();
			$where['nodename'] = $nodename;
			$where['catgory'] = $_POST['catgory'];
			$infolist = $process_infoDao->where($where)->getField("id,name");
			if(count($infolist)>0){
				//存在其他流程，修改为非默认使用流程
				$result = $process_infoDao->where($where)->setField("default",0);
				if($result === false){
					$this->error("修改其他默认流程失败，请联系管理员");
				}
			}
			//组合流程数据
			$name = $_POST['name'];
			$data['name'] = $name;
			$data['catgory'] = $_POST['catgory'];
			$data['nodename'] = $nodename;
			$data['typeid'] = $_POST['typeid'];
			$data['default'] = $_POST['default'];
			$data['title'] = $_POST['title'];
			$data['flowtrack'] = json_encode($flowdata);
			$data['createtime'] = time();
			$data['createid'] = $_SESSION[C('USER_AUTH_KEY')];
			$data['companyid'] = $_SESSION['companyid'];
			$addresult = $process_infoDao->add($data);
		}
		if($addresult === false){
			$this->error("流程操作失败，请联系管理员");
		}else{
			//流程审核人数据表mis_system_user_bactch
			$MisSystemUserBactchModel = D("MisSystemUserBactch");
			//实例化用户模型
			$UserModel=D("User");
			foreach($flowdata as $key=>$val){
				//进行html标签替换
				$processcondition= str_replace (array ('&quot;','&#39;','&lt;','&gt;'), array ('"', "'",'<','>'),$val['processcondition']);
				$processrules= str_replace (array ('&quot;','&#39;','&lt;','&gt;'), array ('"', "'",'<','>'),$val['processrules']);
				//组装存储数组
				$data = array();
				$data['tablename'] = "process_info";
				$data['pinfoid'] = $addresult;
				$data['flowid'] = $val['ids'];
				$data['name'] = $val['processname'];
				$data['flowtype'] = $val['choose']?$val['choose']:0;
				$data['processto'] = $val['processto']?$val['processto']:'';
				$data['processall'] = $val['processall']?$val['processall']:'';
				$data['processcondition'] = $processcondition?$processcondition:'';
				$data['processrules'] = $processrules?$processrules:'';
				$data['processrulesinfo'] = $val['processrulesinfo']?$val['processrulesinfo']:'';
				$data['filterwritsetempty'] = $val['filterwritsetempty']?$val['filterwritsetempty']:'';
				$data['filterreadsetempty'] = $val['filterreadsetempty']?$val['filterreadsetempty']:'';
				$data['isauditmodel'] = $val['isauditmodel']?$val['isauditmodel']:'';
				$data['issourcemodel'] = $val['issourcemodel']?$val['issourcemodel']:'';
				$data['sort'] =  $val['level'];
				$data['istuihui'] =  $val['istuihui'];
				$data['parallel'] = $val['processaudit']['parallel'];
				$data['isauto'] = $val['processaudit']['isauto'];
				$data['document'] = $val['processaudit']['document'];
				$data['ismobile'] = $val['processaudit']['ismobile'];
				$data['createtime'] = time();
				$data['createid'] = $_SESSION[C('USER_AUTH_KEY')];
				$data['companyid'] = $_SESSION['companyid'];
				$relaid = $ProcessRelationModel->add($data);
				if($relaid){
					$filterwritsetempty = array_filter(explode(",", $data['filterwritsetempty']));
					$filterreadsetempty = array_filter(explode(",", $data['filterreadsetempty']));
					$arr = array();
					foreach($filterwritsetempty as $wkey=>$wval){
						$arr[$wval] = array(1,0);
					}
					foreach($filterreadsetempty as $rkey=>$rval){
						if(in_array($rval, array_keys($arr))){
							$arr[$rval] = array(1,1);
						}else{
							$arr[$rval] = array(0,1);
						}
					}
					if($arr){
						//组合节点不可操作字段
						$data = array();
						$data = array($nodename,$relaid,'',$arr);
						try {
							$MisFieldFilterModel->createFilter($data);
						} catch (Exception $e) {
							logs($e->getMessage(),"filter".transTime(time()));
						}
					}
					if(is_array($val['informpersonid']) && isset($val['informpersonid'])){
						//插入数据
						if($val['informpersonid']['sysselectuser'] && $val['informpersonid']){
							/*
							 * 满足此setSelectUser方法。配置post参数
							 */
							$_POST['recipientname']= $val['informpersonid']['recipientname'];
							$_POST['recipient'] = $val['informpersonid']['recipient'];
							$_POST['groupname'] = $val['informpersonid']['groupname'];
							$_POST['groupid'] = $val['informpersonid']['groupid'];
							$_POST['sysselectuser'] = $val['informpersonid']['sysselectuser'];
							$result=$UserModel->setSelectUser("ProcessManage",$relaid,"sysselectuser");
							if(!$result){
								$this->error("添加知会人失败!");
							}
						}
					}
					$dataList = array();
					if(is_array($val['processaudit']) && isset($val['processaudit'])){
						$bacthcval = $val['processaudit'];
						//审核人相关信息
						$userobjid = explode(";", $bacthcval['userobjid']);
						$bactchname = explode(";", $bacthcval['bactchname']);
						$userobjidname = explode(";", $bacthcval['userobjidname']);
						$userobj = explode(";", $bacthcval['userobj']);
						$userobjname = explode(";", $bacthcval['userobjname']);
						//进行html标签替换
						$htmlrule= str_replace (array ('&quot;','&#39;','&lt;','&gt;'), array ('"', "'",'<','>'),$bacthcval['rule']);
						$htmlrulename= str_replace (array ('&quot;','&#39;','&lt;','&gt;'), array ('"', "'",'<','>'),$bacthcval['rulename']);
						$rule = explode(";", $htmlrule);
						$rulename = explode(";", $htmlrulename);
						$bactch = explode(";", $bacthcval['bactch']);
						$rulesinfo = explode(";", $bacthcval['rulesinfo']);
						foreach($userobjid as $objkey=>$objval){
							//插入批次信息
							$dataList[] = array(
									'name'=>$bactchname[$objkey],
									'tablename'=>"process_relation",
									'tableid'=>$relaid,
									'userobjid'=>$objval,
									'userobj'=>$userobj[$objkey],
									'userobjname'=>$userobjname[$objkey],
									'rule'=>$rule[$objkey],
									'rulename'=>$rulename[$objkey],
									'rulesinfo'=>$rulesinfo[$objkey],
									'sort'=> $bactch[$objkey],
									'createtime'=> time(),
									'createid'=>$_SESSION[C('USER_AUTH_KEY')],
							);
						}
						$bactchResult = $MisSystemUserBactchModel->addAll($dataList);
						if($bactchResult === false){
							$this->error("流程新增失败，请联系管理员");
						}
					}
				}else{
					$this->error("流程新增失败，请联系管理员");
				}
			}
			$this->success("流程新增成功！");
		}
	}
	function lookupAjaxSelect2(){
		$data = array();
		$data["total_count"]= 40;
		$data["incomplete_results"]= false;
// 		$query["page_limit"] = $query["page_limit"]?$query["page_limit"]:0;
// 		$query["type"] = $query["type"]?$query["type"]:0;
		$data["items"][] = array("id"=>"","text"=>"请选择");
		
		$query = $_REQUEST;
		print_r($query);
		//第一步、获取查询的表明
		//$tablename = $query['tablename'];
		//$id = $query['id'];
		//$name = $query['name'];
		$like = html_entity_decode($query['key']);
		
		
		
		//组合条件参数
		$map['status'] = 1;
		$map['title'] = array('like',"%".$like."%");
		$map['level'] =3;
		$Model = M("node");
		$dataArr = $Model->where($map)->getField("name,title");
		foreach($dataArr as $key=>$val){
			$data["items"][] = array("id"=>$key,"text"=>$val);
		}
		echo json_encode($data);
		
	}
	
	
	/**
	 * @Title: lookupAddChildChoose
	 * @Description: todo(流程节点上设置判定节点空方法，直接输出判定节点模板)
	 * @author 黎明刚
	 * @date 2015年1月28日 下午8:50:13
	 * @throws
	 */	
	public  function lookupAddChildChoose(){
		$this->display();
	}
	/**
	 * @Title: lookupAddNoOperationField
	 * @Description: todo(流程节点上设置当前节点表单不可操作字段)   
	 * @author 黎明刚
	 * @date 2015年1月28日 下午8:50:13 
	 * @throws
	 */
	public function lookupAddNoOperationField(){
		//不可编辑字段
		$filterwritsetempty = array_filter(explode(",", $_REQUEST['filterwritsetempty']));
		$this->assign("filterwritsetempty",$filterwritsetempty);
		//print_r($filterwritsetempty);
		//不可查看字段
		$filterreadsetempty = array_filter(explode(",", $_REQUEST['filterreadsetempty']));
		$this->assign("filterreadsetempty",$filterreadsetempty);
		//获取模型名称
		$modelname = $_REQUEST['modelname'];
		//读取模型配置文件list.inc.php
		//读取动态配制
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($modelname,false);
		require CONF_PATH . 'property.php';
		$prolist = array_merge($NBM_DEFAULTFIELD,$NBM_SYSTEM_FIELD_CONFIG,$NBM_AUDITFELD);
		$profieldarr = array_keys($prolist);
		foreach($detailList as $key=>$val){
			if(in_array($val['name'],$profieldarr)){
				unset($detailList[$key]);
			}
		}
		//unset($detailList['auditState']);
		
		$this->assign("detailList",$detailList);
		
		$this->display();
	}
	/**
	 * @Title: lookupDeleteFlowUser
	 * @Description: todo(这里用一句话描述这个方法的作用)
	 * @author 杨东
	 * @date 2014-5-5 下午5:03:48
	 * @throws
	 */
	public function lookupDeleteFlowUser(){
		//获取所有节点数据信息
		$data = $_SESSION["flowsdata"];
		//获取当前删除节点内容
		$row = $_REQUEST['row'];
		$delflowid = $row['ids'];
		/*
		 * 第一步、移除当前删除节点与上级节点的关联关系
		 * 第二步、移除当前节点和所有下级节点。
		 */
		//存储需要清除的节点
		$allArr = array();
		foreach ($data as $k => $v) {
			$processto = explode(",", $v['processto']);
			//第一步、处理与上级的关联关系
			if(in_array($delflowid, $processto)){ //验证所属上级
				//清空上级存储的当前节点值
				$data[$k]['processto'] = implode(",", array_diff($processto,array($delflowid)));
				//上级节点是判定节点
				if($v['choose'] == 1){
					//因为判定节点存在分支情况，分支以分号“；”分隔，解析上级节点的总节点分支信息，
					if(count(array_filter(explode(";", $v['processall'])))>0){
						//分解判定节点上的条件存储。1.显示条件。2sql条件，3map数组 条件
						$processcondition =  explode(",", $v['processcondition']);
						$processrules =  explode(",", $v['processrules']);
						$processrulesinfo =  explode(",", $v['processrulesinfo']);
						//重新组装判定节点的条件
						$data[$k]['processcondition'] = implode(",",array_diff($processcondition,array($row['processcondition'])));
						$data[$k]['processrules'] = implode(",",array_diff($processrules,array($row['processrules'])));
						$data[$k]['processrulesinfo'] = implode(",",array_diff($processrulesinfo,array($row['processrulesinfo'])));
					}
					$allArr = explode(",", $row['processall']);
				}else{//非判定节点。
					
					//非判定节点存储的是下级总节点，用逗号分隔“，”
					$processall = explode(",", $v['processall']);
					//上级非判定节点。那么当前删除的节点。有多中情况
					if($row['choose'] == 1){
						//当前删除节点为判定节点,需要解析判定节点的所有下级节点值
						$processall = explode(";", $row['processall']);
						foreach($processall as $alkey=>$alval){
							$a = explode(",", $alval);
							foreach($a as $nkey=>$nval){
								array_push($allArr, $nval);
							}
						}
						$data[$k]['processall'] = "";
					}else{
						//非判定节点
						$delprocessall = explode(",", $row['processall']);
						$data[$k]['processall'] = implode(",",array_diff($processall,$delprocessall));
						$allArr = $delprocessall;
					}
				}
			}
			//第二步、清除当前和所有下级的关联关系
			if($v['prcsid'] == $delflowid) {
				 array_push($allArr, $delflowid);
			}
		}
		// 移除当前节点后的节点
		foreach ($data as $delk => $delv) {
			if (count($allArr)>0 && in_array($delv["prcsid"], $allArr)) {
				unset($data[$delk]);
			}
		}
		//清除所有上级的processall；
		foreach($data as $key=>$val){
			if (count($allArr)>0) {
				if($val['choose'] == 1){
					$processall = array();
					$fenhaoprocessall	= explode(";", $val['processall']);
					$newprocessall = array();
					foreach ($fenhaoprocessall as $fhkey=>$fhval){
						$dhval = explode(",", $fhval);
						$dhval = array_diff($dhval, $allArr);
						if($dhval){
							$newprocessall[] = implode(",", $dhval);
						}
					}
					if($newprocessall){
						$data[$key]['processall'] = implode(";", $newprocessall);
					}else{
						$data[$key]['processall'] = "";
					}
				}else{
					$processall = array();
					$processall	= explode(",", $val['processall']);
					$processall = array_diff($processall, $allArr);
					if($processall){
						$data[$key]['processall']	= implode(",", $processall);
					}else{
						$data[$key]['processall']	= "";
					}
				}
			}
		}
		$_SESSION["flowsdata"] = $data;
		//实例化流程模型
		$ProcessManageModel = D("ProcessManage");
		$jsondata = $ProcessManageModel->setDataJson($data);
		$this->success ( "节点删除成功" ,'',$jsondata);
	}
	
	/**
	 * @Title: lookupLinkFlowUser
	 * @Description: todo(连接节点)
	 * @author 杨东
	 * @date 2014-5-5 上午11:32:49
	 * @throws
	 */
	public function lookupLinkFlowUser(){
		//流程节点ID
		$ids = $_REQUEST['ids'];
		//等级
		$level = $_REQUEST['level'];
		//获取session的内容
		$data = $_SESSION["flowsdata"];
		
		// 第一步：获取可以选择的流程节点
		$list = array();
		foreach ($data as $k => $v) {
			if($v['ids'] == $ids){
				continue;
			}
			if($v['choose'] == 1){
				$fhval = array_filter(explode(";", $v['processall']));
				$bool = true;
				foreach($fhval as $key=>$val){
					if(in_array($ids, explode(",",$val))){
						$bool = false;
						break;
					}
				}
				if($bool){
					$list[] = array("key"=>$v['ids'],"name"=>$v['processname']);
				}
			}else if($v['choose'] == 2){
				if(!in_array($ids, explode(",", $v['processall']))){
					$list[] = array("key"=>$v['ids'],"name"=>$v['processname']);
				}
			}
		}
		$this->assign("list",$list);
		$this->assign("key",$ids);
		$this->assign("ids",$ids);
		$this->display("lookupLinkFlowUser");
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
		//连接的节点ID
		$ids = $_REQUEST['ids'];
		$key = $_POST['key'];
		//被连接的节点ID
		$linkkeys = $_POST["linkkeys"];
		$parents = array($key);
		foreach ($data as $k => $v) {
			//获取被连接ID的下级所有节点
			if(in_array($linkkeys, explode(",", $v['processto']))){
				//判断包含的的节点是否为判定节点
				if($v['choose'] == 1){
					
				}
			}
		}
		unset($_SESSION["flowsdata"]);
		$_SESSION["flowsdata"] = $data;
		$json = $this->setDataJson($data, $_POST["ids"]);
		$this->success ( L('_SUCCESS_') ,'',$json);
		exit;
	}
	
	
	
	
	
	//-------------------------------------下面为老代码---------------------------------------------------//
	
	public function lookupaddtemplete(){
		$date=array(
				'code'=>$_POST['code'],
				'name'=>$_POST['name'],
				'remark'=>$_POST['remark'],
		);
		$this->success("添加成功",'',json_encode($date));
	}


	
	public function add2(){
		//增加流程节点
		$this->display("addptemplate");
		$this->assign("pinfoid",$_REQUEST['pinfoid']);
		$this->assign("nodenmae",getFieldBy($_REQUEST['pinfoid'], "id", "nodename", "process_info"));
	}
	/**
	 *
	 * @Title: lookupcommon
	 * @Description: todo(公用查询)
	 * @author renling
	 * @date 2014-1-13 下午3:41:15
	 * @throws
	 */
	private function lookupcommon($defaultId){
		//视图model
		$ProcessNodeModel=D('ProcessNode');
		//构造查询条件
		$NodeMap['status']=1;
		$NodeMap['id']=$defaultId;
		//获取节点信息
		$ProcessNodeList=$ProcessNodeModel->where($NodeMap)->getField("name");
		//读取配置字段
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($ProcessNodeList,false);
		foreach($detailList as $key=>$val){
			if($val['rules']!=1){
				unset($detailList[$key]);
			}
		}
		$html=getSelectByHtml('roleinexp','select');
		$html= str_replace('"', "'", $html);
		$this->assign("html",$html);
		$this->assign("detailList",$detailList);
	}
	/**
	 * @Title: _before_insert
	 * @Description: todo(插入节点构造POST)
	 * @author 杨东
	 * @date 2014-1-8 上午9:45:11
	 * @throws
	 */
	public function _before_insert12(){
		//格式知会人
		$_POST['executorid']=implode(",", $_POST['executorid']);
		$_POST['rules']=html_entity_decode($_POST['rules']);
		$_POST['showrules']=html_entity_decode($_POST['showrules']);
	}
 
	/**
	 * @Title: _before_update
	 * @Description: todo(修改前置函数)
	 * @author renling
	 * @date 2014-9-19 上午9:49:11
	 * @throws
	 */
	public function _before_update12(){
		if(!$_POST['rulesinfo']){
			$_POST['rulesinfo']="";
		}
		if(!$_POST['showrules']){
			$_POST['showrules']="";
		}
		if(!$_POST['rules']){
			$_POST['rules']="";
		}
		//格式知会人
		$_POST['executorid']=implode(",", $_POST['executorid']);
		$_POST['rules']=html_entity_decode($_POST['rules']);
		$_POST['showrules']=html_entity_decode($_POST['showrules']);
	}
	/**
	 *
	 * @Title: _after_insert
	 * @Description: todo(添加流程节点)
	 * @param unknown_type $list
	 * @author renling
	 * @date 2014-9-18 下午2:42:43
	 * @throws
	 */
	public function _after_update12($list){
		$this->lookupinserttinfo($_POST['id']);
	}
	/**
	 *
	 * @Title: lookupinserttinfo
	 * @Description: todo(保存流程走向)
	 * @param unknown_type $id
	 * @author renling
	 * @date 2014-9-19 上午9:46:58
	 * @throws
	 */
	private function lookupinserttinfo1($id){
		$tname=$_POST['tname'];  //流程节点名称
		$tparallel=$_POST['tparallel']; //是否并行
		$ProcessRelationModel=D('ProcessRelation');
		//批次信息表
		$MisSystemUserBactchModel = D("MisSystemUserBactch");
		
		//先删除原有的流程批次信息，在进行新增
		$map=array();
		$map['pinfoid']=$id;
		$map['tablename']="process_info";
		$relaids = $ProcessRelationModel->where($map)->getField("id,pinfoid");
		if($relaids){
			$map = array();
			$map['id'] = array(' in ',array_keys($relaids));
			$resultrela=$ProcessRelationModel->where($map)->setField('status',-1);
			$map = array();
			$map['tablename'] = 'process_relation';
			$map['tableid'] = array(' in ',array_keys($relaids));
			$resultbactch=$MisSystemUserBactchModel->where($map)->setField('status',-1);
			if(!$resultrela || !$resultbactch){
				$this->error("数据绑定失败，请联系管理员");
			}
		}
		foreach ($tname as $key=>$val){
			$data=array();
			$data['pinfoid']=$id;
			$data['tablename']="process_info";
			$data['sort']=$key;
			$data['name']=$val;
			$data['parallel']=$tparallel[$key];
			$data['createtime']=time();
			$data['createid']=$_SESSION[C('USER_AUTH_KEY')];
			$result=$ProcessRelationModel->add($data);
			if(!$result){
				$this->error("数据提交错误！");
			}
			//获取当前流程节点的批次，规则
			$userobjidStr=explode(";",$_POST['userobjidStr'][$key]);
			$userobjStr=explode(";",$_POST['userobjStr'][$key]);
			$userobjname = explode(";",$_POST['userobjStrname'][$key]);
			$bactchStr=explode(";",$_POST['bactchStr'][$key]);
			
			//替换html标签字符
			$fields = str_replace("&#39;", "'", html_entity_decode($_POST['ruleStr'][$key]));
			$ruleStr = explode(";",$fields);
			$rulenameStr = explode(";",str_replace("&#39;", "'", html_entity_decode($_POST['rulename'][$key])));
			$rulesinfoStr = explode(";",$_POST['rulesinfoStr'][$key]);
			
			$dataList = array();
			foreach($userobjidStr as $bactchkey=>$bactchval){
				$dataList[] = array(
						'tablename'=>"process_relation",
						'tableid'=>$result,
						'userobjid'=>$bactchval,
						'userobj'=>$userobjStr[$bactchkey],
						'userobjname'=>$userobjname[$bactchkey],
						'rule'=>$ruleStr[$bactchkey],
						'rulename'=>$rulenameStr[$bactchkey],
						'rulesinfo'=>$rulesinfoStr[$bactchkey],
						'sort'=> $bactchStr[$bactchkey],
						'createtime'=> time(),
						'createid'=>$_SESSION[C('USER_AUTH_KEY')],
					);
			}
			$bactchResult=$MisSystemUserBactchModel->addAll($dataList);
			if(!$bactchResult){
				$this->error("流程节点批次报错失败,请联系管理员");
			}
		}
	}
	/**
	 * @Title: getnode
	 * @Description: todo(获取当前流程节点)
	 * @param $pid  流程ID
	 * @author 杨东
	 * @date 2013-3-27 下午12:00:41
	 * @throws
	 */
	public function getnode($pinfoid,$stp='0',$modelname,$istemp) {
		//获取流程走向list
		$ProcessRelationModel=D('ProcessRelation');
		//批次信息表
		$MisSystemUserBactchModel = D("MisSystemUserBactch");
		$MisSystemUserObjModel = D("MisSystemUserObj");
		//查询类型名称
		$userobjlist = $MisSystemUserObjModel->where("status = 1")->select();
		$info=$ProcessRelationModel->getProessInfoRelation($pinfoid);
		foreach ($info as $key => $val) {
			//根据流程节点。查询批次信息 
			$map = array();
			$map['tablename'] = 'process_relation';
			$map['tableid'] = $val['id'];
			$bactchlist=$MisSystemUserBactchModel->where($map)->select();
			$rulesinfo= $ruleStr = $rulenameStr = $userobjidStr = $userobjidStrname = $userobjStr = $userobjStrname = $bactchStr=array();
			foreach($bactchlist as $k=>$v){
				//用户对象ID
				array_push($userobjidStr,$v['userobjid']);
				foreach($userobjlist as $k1=>$v1){
					if($v['userobjid'] == $v1['id']){
						array_push($userobjidStrname,$v1['name']);
					}
				}
				//用户对象存储字段
				array_push($userobjStr,$v['userobj']);
				//用户对象中文存储
				array_push($userobjStrname,$v['userobjname']);
				//批次条件
				array_push($ruleStr,$v['rule']);
				//批次条件中文展示
				array_push($rulenameStr,$v['rulename']);
				//批次顺序
				array_push($bactchStr,$v['sort']);
				//批次序列化
				array_push($rulesinfo,$v['rulesinfo']);
			}
			$userobjid = implode(";",$userobjidStr);
			$userobjidname = implode(";",$userobjidStrname);
			$userobj = implode(";",$userobjStr);
			$userobjname = implode(";",$userobjStrname);
			$rule = implode(";",$ruleStr);
			$rulename = implode(";",$rulenameStr);
			$bactch = implode(";",$bactchStr);
			$rulesinfo = implode(";",$rulesinfo);
			//默认显示
			$str .= '<li class="itemID">';
			$str .= '<span class="js-itemId" id="js-itemId'.$val['id'].'">';
			$str .=	'	<input type="hidden" name="tparallel[]" value="' . $val['parallel'] . '"/>';
			$str .=	'	<input type="hidden" name="tname[]" value="' . $val['name'] . '"/>';
			$str .=	'	<input type="hidden" name="userobjidStr[]" value="' .$userobjid . '"/>';
			$str .=	'	<input type="hidden" name="userobjidStrname[]" value="' . $userobjidname . '"/>';
			$str .=	'	<input type="hidden" name="userobjStr[]" value="' . $userobj . '"/>';
			$str .=	'	<input type="hidden" name="userobjStrname[]" value="' . $userobjname . '"/>';
			$str .=	'	<input type="hidden" name="ruleStr[]" value="' . $rule . '"/>';
			$str .=	'	<input type="hidden" name="rulename[]" value="' . $rulename . '"/>';
			$str .=	'	<input type="hidden" name="bactchStr[]" value="' . $bactch. '"/>';
			$str .=	'	<input type="hidden" name="rulesinfoStr[]" value="' . $rulesinfo. '"/>';
			$str .=	'	<input type="hidden" name="tempname[]" value="' . getFieldBy($val['id'], "relationid", "tempname", "mis_auto_process"). '"/>';
			$str .=	'	<input type="hidden" name="tid[]" value="' . $val['id'] . '"/>';
			$str .= '</span>';
			$str .= '<div class="process_box"">';
			if($istemp==1){
				$str .= '<a href="__APP__/MisSystemUserBactch/lookupAddProcessRelation/ptype/1/modelname/'.$modelname.'/relaid/'.$val['id'].'" rel="lookupAddProcessRelation" width="800" height="580" mask="true"  target="dialog">'.$val['name'].'</a>';
			}else{
				$str .= '<a href="__APP__/MisSystemUserBactch/lookupAddProcessRelation/modelname/'.$modelname.'/relaid/'.$val['id'].'" rel="lookupAddProcessRelation" width="800" height="580" mask="true"  target="dialog">'.$val['name'].'</a>';
			}
			$str .= '</div><div class="process_ico"></div>';
			if($istemp==1){
				$tempname="";
				if(getFieldBy($val['id'], "relationid", "tempname", "mis_auto_process")){
					$tempname=getFieldBy($val['id'], "relationid", "tempname", "mis_auto_process");
				}
				//查询绑定节点名称
				$str .='<input type="hidden" name="mpinfoid" value="'.getFieldBy($val['id'], "relationid", "id", "mis_auto_process").'"/>   <div class="nbm_tpl_item">'.$tempname.'</div>';
			}
			$str .= '</li>';
		}
		if($stp){
			return $str;
		}else{
			$this->assign("str", $str);
		}
	}
	/**
	 * @Title: accredit
	 * @Description: todo(流程节点授权)
	 * @author 杨东
	 * @date 2013-3-27 上午11:57:59
	 * @throws
	 */
	public function accredit() {
		$model = D("ProcessRelation");
		$list = $model->where("pid='" . $_REQUEST['pid'] . "' and tid='" . $_REQUEST['tid'] . "'")->find();
		$i=$j=0;
		if($list["filteruid"]){
			$oldarr = $a = array();

			$arr =unserialize($list["filteruid"]);
			foreach($arr['func'] as $k=>$v){
				$a['num']=$j;
				foreach($v as $k1=>$v1){
					if($v1){
						if($k1==0){
							$a['one']=1;
							if(count($v)>1) $a['haschildren']=1;
						}
						$i++;
						$a['func']=$v1;
						$a['k1']=$k1;
						$a['magrin']=($k1-1)*12;
						$a['inputsize']=20-$k1*2;
						$a['funcdata']=implode(";",$arr['funcdata'][$k][$k1]);
						$a['funcdata']=htmlspecialchars($a['funcdata']);
						if($arr['extention_html_start'][$k]) $a['htmlstart']=$arr['extention_html_start'][$k];
						if($arr['extention_html_end'][$k]) $a['htmlend']=$arr['extention_html_end'][$k];
						array_push($oldarr,$a);
					}
					$a=array();
					$a['num']=$j;
				}
				$j++;
			}
			$this->assign("filteruid",$oldarr);
		}
		$this->assign("i",$i);
		$this->assign("vo", $list);
		if (isset($_REQUEST['step'])) {
			//判断有没有向前或向后并行
			$has_pre = $model->where("pid=" . $list['pid'] . " and sort < " . $list['sort'])->count('*');
			$has_next = $model->where("pid=". $list['pid'] . " and sort > " . $list['sort'])->count('*');
			if($has_pre==0 ) $has_next=0;
			$this->assign("pre", $has_pre);
			$this->assign("next", $has_next);

			if ($_REQUEST['step'] == 0) {
				/**
				 * 按用户授权
				 */
				//查询对应的流程节点
				$list['userid'] = explode(",", $list['userid']);
				$usermodel = D("User");//初始化用户表
				$usermap1['id'] = array('in',$list['userid']);
				$rolelist = $usermodel->where($usermap1)->distinct(true)->field('dept_id')->select();
				$role = array();
				foreach ($rolelist as $k => $v) {
					$role[] = $v['dept_id'];
				}
				$list['role'] = $role;
				//获取部门
				$deptmodel = D('MisSystemDepartment'); //部门模型
				$deptlist = $deptmodel->where('status = 1')->getField('id,name');
				foreach ($deptlist as $k => $v) {
					//获取部门下的用户
					$usermap['status'] = 1;
					$usermap['dept_id'] = $k;
					$userlist = $usermodel->where($usermap)->getField('id,name');
					if ($userlist) {
						$str .= "<div id='accredituser$k' class='unit textInput' style='margin-top:5px;width: 100%;'><label>";
						if (in_array($k,$list['role'])) {
							$str .= "<input type='checkbox' class='parentCls' onClick='nowchecked(\"accredituser$k\");' checked='true' />";
						} else {
							$str .= "<input type='checkbox' class='parentCls' onClick='nowchecked(\"accredituser$k\");' />";
						}
						$str .= $v. '：</label><div class="divider"></div><ul class="userul">';
						foreach ($userlist as $k1 => $v1) {
							$str .= '<li>';
							//判断用户选中
							if(in_array($k1,$list['userid'])){
								$str .= '<input type="checkbox" onclick="subCheck(\'accredituser'.$k.'\');" name="userid[]" value="'.$k1.'" checked="true" />';
							}else{
								$str .= '<input type="checkbox" onclick="subCheck(\'accredituser'.$k.'\');" name="userid[]" value="'.$k1.'" />';
							}
							$str .= $v1.'</li>';
						}
						$str .= '</ul></div>';
					}
				}
				$this->assign("str", $str);
				$this->display('accredituser');
				exit;
			}
			else if ($_REQUEST['step'] == 1) {//按职级授权
				//获取职级
				$dutymodel = M('duty');
				$dutylist = $dutymodel->where("status = 1")->getField('id,name',true);
				$selected_duty = explode(",", $list['duty']);
				$this->assign("selected_duty", $selected_duty);
				$this->assign("dutylist", $dutylist);
				//获取部门
				$deptmodel = D('MisSystemDepartment'); //部门模型
				$deptlist = $deptmodel->where('status = 1')->getField('id,name',true);
				$selected_dept = explode(",", $list['role']);
				$this->assign("selected_dept", $selected_dept);
				$this->assign("deptlist", $deptlist);
				$this->display('accreditrank');
				exit;
			}
			else if ($_REQUEST['step'] == 2) {
				/**
				 * 按项目授权
				 */
				$m=M("mis_sales_project_usertype");
				$projectList = $m->where("status=1")->order("sort asc")->getField("id,name",true);

				if ($list['step'] == 2) {
					$list['duty'] = explode(",", $list['duty']);
				} else {
					$list['duty'] = 0;
				}
				$projectStr = '';
				foreach ($projectList as $k => $v) {
					$projectStr .= '<li>';
					if(in_array($k,$list['duty'])){
						$projectStr .= '<input type="checkbox" class="parentCls" name="duty[]" value="'.$k.'" checked="checked"/>';
					} else {
						$projectStr .= '<input type="checkbox" class="parentCls" name="duty[]" value="'.$k.'"/>';
					}
					$projectStr .= $v.'_'.$k.'</li>';
				}
				$this->assign("projectStr", $projectStr);
				$this->display('accrediproject');
				exit;
			}
			else if ($_REQUEST['step'] == 3) {
				/**
				 * 按流程审核角色授权
				 */
				/**
				 * 按项目授权
				 */
				$model = D('ProcessRole');
				$roleList = $model->where('status = 1')->getField('id,name');
				if ($list['step'] == 3) {
					$list['duty'] = explode(",", $list['duty']);
				} else {
					$list['duty'] = 0;
				}
				$roleStr = '';
				foreach ($roleList as $k => $v) {
					$roleStr .= '<li>';
					if(in_array($k,$list['duty'])){
						$roleStr .= '<input type="checkbox" class="parentCls" name="duty[]" value="'.$k.'" checked="checked"/>';
					} else {
						$roleStr .= '<input type="checkbox" class="parentCls" name="duty[]" value="'.$k.'"/>';
					}
					$roleStr .= $v.'</li>';
				}
				$this->assign("roleStr", $roleStr);
				$this->display('accredirole');
				exit;
			}
			else if($_REQUEST['step'] == 4){
				//获取职级
				$dutymodel = M('duty');
				$dutylist = $dutymodel->where("status = 1")->getField('id,name',true);
				if ($list['step'] == 4) {
					$selected_duty = explode(",", $list['duty']);
					$this->assign("selected_duty_step_four", $selected_duty);
					$selected_dept = explode(",", $list['role']);
					$this->assign("selected_dept_four", $selected_dept);
				}
				$this->assign("dutylist", $dutylist);
				//获取部门
				$deptmodel = D('MisSystemDepartment'); //部门模型
				$deptlist = $deptmodel->where('status = 1')->getField('id,name',true);

				$this->assign("deptlist", $deptlist);
				$this->display('accreditoption');
				exit;
			}
		} else {
			/**
			 * 进入授权界面
			 */
			$this->display();
			exit;
		}
	}
}
?>