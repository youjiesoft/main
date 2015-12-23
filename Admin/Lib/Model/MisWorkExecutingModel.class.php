<?php
/**
 * @Title: MisWorkExecutingModel
 * @Package package_name
 * @Description: 工作执行模型   模型作用，1、指定表，2、数据自动填充，3、数据验证、4、获取本表相关的数据信息   主要获取工作执行数据
 * @author 杨东
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-2-20 下午3:48:05
 * @version V1.0
 */
class MisWorkExecutingModel extends CommonModel {
	//指定当前模型对应的表
	protected $trueTableName = 'mis_work_executing';
	//数据自动填充
	public $_auto =array(
            array("createid","getMemberId",self::MODEL_INSERT,"callback"),
			array("createtime","time",self::MODEL_INSERT,"function"),
			array("updateid","getMemberId",self::MODEL_UPDATE,"callback"),
			array("updatetime","time",self::MODEL_UPDATE,"function"),
		    
			array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
	);
	/**
	 * @Title: getUserZhList
	 * @Description: 获取知会信息
	 * @param int $uid 后台用户ID， 可传可不传，如果不传入则默认取当前登录用户ID
	 * @return 返回知会信息和知会条数  
	 * @author 黎明刚
	 * @date 2014年11月28日 下午2:36:22 
	 * @throws
	 */
	public function getUserZhList($uid){
		$workModel=D("MisWorkExecuting");
		if(!$uid){
			//当前登录人id
			$uid = $_SESSION[C('USER_AUTH_KEY')];
		}
		$Model = M();
		//查询已阅 的知会信息
		$inforid=array();
		$readlist=$workModel->field('id,isread')->where("status=1")->select();
		foreach ($readlist as $k=>$v){
			if(in_array($uid, explode(",", $v['isread']))){
				array_push($inforid, $v['id']);
			}
		}
		if($inforid){//查询条件 排除已阅读的
			$inforidstr = implode(",",$inforid);
		}else{
			$inforidstr="";
		}
		//查询知会人数组 数量
		$map['_string']="( FIND_IN_SET('$uid', mis_work_executing.informpersonid) or FIND_IN_SET('$uid', p.informpersonid))";
		$list=$workModel->where($map)->field("mis_work_executing.tablename,mis_work_executing.informpersonid,p.informpersonid as informpersonidp")->join(" process_info as p on mis_work_executing.ptmptid=p.id")->group("tablename")->select();
		$numsql.="SELECT COUNT(tablename) AS num,mis_work_executing.tablename as name FROM `mis_work_executing` LEFT JOIN process_info as p on mis_work_executing.ptmptid=p.id WHERE ( mis_work_executing.status=1 and ";
		if(!isset($_SESSION['a'])){//如果不是管理员 过滤条件
			$numsql.=" (FIND_IN_SET('$uid', mis_work_executing.informpersonid) or FIND_IN_SET('$uid', p.informpersonid)) and ";
		}
		$numsql.=" mis_work_executing.id not in ($inforidstr) )";
		$numsql.="GROUP BY tablename";
		$numlist=$Model->query($numsql);
		//查询知会人数组 //主要 当该模板下的数据全部已读状态 查的数量就过滤了该模板 页面就不显示
		$listsql.="SELECT mis_work_executing.tablename as name FROM `mis_work_executing` LEFT JOIN process_info as p on mis_work_executing.ptmptid=p.id  ";
		if(!isset($_SESSION['a'])){//如果不是管理员 过滤条件
			$listsql.="WHERE ( (FIND_IN_SET('$uid', mis_work_executing.informpersonid) or FIND_IN_SET('$uid', p.informpersonid)) and mis_work_executing.status=1 )";
		}
		$listsql.="GROUP BY tablename";
		$listinfo=$Model->query($listsql);
		foreach ($numlist as $k=>$v){//
			foreach ($listinfo as $k1=>$v1){
				if($v1['name']==$v['name']){
					$listinfo[$k1]["num"]=$v['num'];
				}
			}
		}
		//显示的全部已读时 数量为0
		foreach ($listinfo as $k=>$v){
			if(!$v['num']){
				$listinfo[$k]["num"]=0;
			}
		}
		$Allinfonum=0;
		foreach ($listinfo as $k=>$v){
			$listinfo[$k]["cname"]=getFieldBy($v['name'], "name", "title", "node");
			$Allinfonum += $v['num'];//总数量
		}
		$userZhui['list'] = $listinfo;
		$userZhui['count'] = $Allinfonum;
		return $userZhui;
	}
	/**
	 * @Title: getUserWorkExecutList 
	 * @Description: 获取$userid 工作执行的数据信息
	 * @param 后台用户ID $userid 如果不传入参数，则取当前登录人，如果传入参数，则取传入的用户
	 * @return 返回待$userid 执行的数据数组   
	 * @author liminggang 
	 * @date 2014-9-2 下午12:02:32 
	 * @throws
	 */
	public function getUserWorkExecutList($userid){
		//项目执行
		$zhixingNum = $this->getWorkExecutingNum(1);
		//项目分派
		$fenpaiNum = $this->getWorkExecutingNum(2);
		//评审表决
		$biaojueNum=$this->lookupGetBiaojueNum();
		//审批数据；
		$MisWorkMonitoringModel = D("MisWorkMonitoring");
		$userAuditlist = $MisWorkMonitoringModel->getUserAuditList();
		$shenpiNum=$userAuditlist['count'];
		//工作只会数据
		$notifyModel=D('MisNotify');
		$nmap ['_string'] = 'FIND_IN_SET(  ' . $_SESSION [C ( 'USER_AUTH_KEY' )] . ',recipient )';
		$nmap['isread']=0;
		$userZhuilist['count'] =$notifyModel->where ( $nmap )->count ( '*' );;
		$zhihuiNum=$userZhuilist['count'];
		
		//商机数据
		$shangjiNum=$this->getShangjiNum();
		$UserAudit['count'] = $zhixingNum+$fenpaiNum+$biaojueNum+$shangjiNum+$shenpiNum+$zhihuiNum;
		$UserAudit['list']=array(
				'0'=>array('id'=>111,'title'=>'项目执行','name'=>'MisSalesMyProject','worktype'=>1,'count'=>$zhixingNum,'tablename'=>'MisSalesMyProject'),
				'1'=>array('id'=>112,'title'=>'项目分配','name'=>'MisAutoQzu','worktype'=>2,'count'=>$fenpaiNum,'tablename'=>'MisAutoQzu'),
				'2'=>array('id'=>113,'title'=>'工作审批','name'=>'MisWorkExecuting','worktype'=>3,'count'=>$shenpiNum,'tablename'=>'MisWorkExecuting','condition'=>'/jump/3/md/MisWorkMonitoring/worktype/1/rel/MisWorkExecutingbox'),
				'3'=>array('id'=>114,'title'=>'工作知会','name'=>'MisWorkExecuting','worktype'=>4,'count'=>$zhihuiNum,'tablename'=>'MisWorkExecuting','condition'=>'/jump/6/md/MisWorkExecuting/istepperson/1'),
				'4'=>array('id'=>113,'title'=>'评审表决','name'=>'MisAutoZpg','worktype'=>5,'count'=>$biaojueNum,'tablename'=>'MisAutoZpg'),
				'5'=>array('id'=>114,'title'=>'商机处理','name'=>'MisSaleMyBusiness','worktype'=>6,'count'=>$shangjiNum,'tablename'=>'MisSaleMyBusiness'));
		return $UserAudit;
	}

	public function getWorkExecutingNum($step){
		if($step == 1){
			$myProjectModel = D('MisAutoPvb');
			$this->_filter("MisSalesMyProject",$map);
			$map['xiangmuzhuangtai']="01";//执行中的项目
			$count = $myProjectModel->where($map)->count();
		}else{
			$myProjectModel = D('MisAutoQzu');
			$this->_filter("MisSalesProjectAllocation",$map);
			$count = $myProjectModel->where($map)->count();
		}
		return $count?$count:0;
	}
	
	/**
	 * @Title: _filter
	 * @Description: todo(项目管理中，分派和执行的数据查询条件) 
	 * @param unknown $model
	 * @param unknown $map  
	 * @author 黎明刚
	 * @date 2014年12月29日 上午10:54:44 
	 * @throws
	 */
	public function _filter($model,&$map){
		// 判定是否为超级管理员
		if ($_SESSION ["a"] != 1) {
			//我的项目
			if($model == "MisSalesMyProject"){
				//优先确定系统存储的userid
				if($_SESSION[C('USER_AUTH_KEY')]){
					$userid = $_SESSION[C('USER_AUTH_KEY')];
				}else if($_REQUEST['userid']){//特殊处理手机端用户的userid获取方法
					$userid = $_REQUEST['userid'];
				}
				//用户的查询权限组等级
					$misSalesMyProjectRolegroupUserModel = D('MisSalesMyProjectRolegroupUser');
					//获取所有权限和单独授权的项目ID
					$Plevelslists = $misSalesMyProjectRolegroupUserModel->getPlevelslists($userid);
					//创建一个用于获取全部ID的数组
					$plist =array();
					foreach($Plevelslists as $k=>$v){
						//判断单独授权 把单独授权的所有ID放进装有用于存放项目ID的数组
						if ($v['dandushouquanID']){
							//打散成数组
							$plists = explode(",", $v['dandushouquanID']);
							//添加进去
							foreach($plists as $kk1=>$vv1){
								array_push($plist,$vv1);
							}
						}
						//判断是不是获取权限 0为无任何权限 
						if($v['plevels']!=0){	
							if($v['plevels']==1){
								
								//传入一个过滤条件
								$plists = $misSalesMyProjectRolegroupUserModel->getMyProjectIdLists($v['rules']);
							}else{
								//获取部门或部门子部门的部门ID
								$deptidlists = $misSalesMyProjectRolegroupUserModel->getDeptid($userid,$v['plevels']);
								//根据部门ID获取部门下的所有ID
								$useridlists = $misSalesMyProjectRolegroupUserModel->getDeptidByUserId($deptidlists);
								//根据ID获取所有ID的项目执行列表ID  传入一个过滤条件
								$plists = $misSalesMyProjectRolegroupUserModel->getMyProjectIdList($useridlists,$v['rules']);
							}
							//把获取的所有ID添加到全部的ID数组里面
							foreach($plists as $kk2=>$vv2){
								array_push($plist,$vv2);
							}
						}
					}
					//去除所有相同的项目ID
					$plist = array_unique($plist);
					//获取有权限查看的角色
					$misProjectFlowResource = D('MisProjectFlowResource');
					$plists = $misProjectFlowResource->getMyProjectIdList($userid);
					foreach($plists as $k=>$v){
						array_push($plist,$v);
					}
					//删除相同的项目ID
					$plist = array_unique($plist);
					//根据后台用户，获取角色信息
					$roleGroupList = D('RolegroupUser');
					$rolegrouplist = $roleGroupList->getRoleGroupByUserId($userid);
					foreach($rolegrouplist as $key=>$val){
						$where = array();
						$where['outlinelevel'] = 4;
						$where['status'] = 1;
						$where['_string'] = 'FIND_IN_SET(  '.$val.',readtaskrole )';
						//任务
						$mis_project_flow_form = M("mis_project_flow_form");
						$worklist = $mis_project_flow_form->where($where)->distinct("projectid")->getField("id,projectid");
						if($worklist){
							foreach($worklist as $k=>$v){
								if($plist){
									if(!in_array($v, $plist)){
										array_push($plist, $v);
									}
								}else{
									array_push($plist, $v);
								}
							}
						}
					}
					//删除重复项目ID
					$plist = array_unique($plist);
					if($plist){
						$map['projectid'] = array(" in ",$plist);
					}else{
						$map['projectid'] = array("lt",0);
					}
			}else if($model=="MisSalesProjectAllocation"){//项目分派
				$map['operateid'] = 0;
				$map['auditState'] = array('lt',3); //未审核完毕的
				//针对手机端用户做判断
				if($_SESSION[C('USER_AUTH_KEY')]){
					$map['createid'] = $_SESSION[C('USER_AUTH_KEY')];
				}else if($_REQUEST['userid']){
					$map['createid'] = $_REQUEST['userid'];
				}else{
					$map['createid'] = null;
				}
				// 查询当前用户
				if ($_SESSION ['a'] != 1) {
					import("@.ORG.Browse");
					$broMap = Browse::getUserMap ( "MisAutoQzu" );
					if ($broMap) {
						if($map['_string']){
							if(is_string($broMap) !== false){
								$map['_string'] .= " and " . $broMap;
							}else if(is_array($broMap)){
								if($broMap[0]){
									$map['_string'] .= " and " . $broMap;
								}
								if($broMap[1]){
									$map["_logic"] = "and";
									$m['_complex'] = $map;
									$m['_string'] = $broMap[1];
									$m['_logic'] = 'or';
									$map = $m;
								}
							}
						}else{
							if(is_string($broMap) !== false){
								$map['_string'] .=  $broMap;
							}else if(is_array($broMap)){
								if($broMap[0]){
									$map['_string'] .= $broMap;
								}
								if($broMap[1]){
									$map["_logic"] = "and";
									$m['_complex'] = $map;
									$m['_string'] = $broMap[1];
									$m['_logic'] = 'or';
									$map = $m;
								}
							}
						}
					}
				}
			}
		}
	}
	
	/**
	 * @Title: getCurrentWork
	 * @Description: 首页index页面，点击待处理后请求执行的方法，为了获取代办事项
	 * @return 返回带处理任务的数据信息,以及点击数据后，请求的方法地址。
	 * @author 黎明刚
	 * @date 2014年11月28日 下午2:34:56 
	 * @throws
	 */
	public function getCurrentWork(){
		$workexeList = array();
		//当前登录人id
		$userid = $_SESSION[C('USER_AUTH_KEY')];
		//获取带我处理的工作条数信息
		$model = D('MisWorkMonitoring');
		$map['dostatus'] = 0;
		$map['_string'] = 'FIND_IN_SET(  '.$userid.', curNodeUser )';
		$workdata = $model->where($map)->select();
		//计算总条数
		$daibanNum = count($workdata);
		//定义变量存放  待我审核，待我执行的数据集合
		$moduleNameList=array();
		foreach( $workdata as $k=>$v ){
			$auditTimeOut= "";
			//计算推送时间到至今有多少小时。判断是否即将超时。
			$hours=getCHStimeDifference($v['createtime'],time(),"H");
			//获取系统中预留的超时提醒
			$referHours = C("AUDIT_TIMEOUT");
			//超期提前提醒时间
			$remindHours =C("AUDIT_TIMEOUT_REMIND");
			$countH = $hours+$remindHours;
			if($countH>=$referHours){
				$auditTimeOut="即将超期";
				if($hours>=$referHours){
					$auditTimeOut="已超期";
				}
			}
			if(in_array($v['tablename'], array_keys($moduleNameList))){
				if($v['createtime']>$moduleNameList[$v['tablename']]['createtime']){
					$moduleNameList[$v['tablename']]['createtime'] = $v['createtime'];
				}
				if($moduleNameList[$v['tablename']]['auditTimeOut'] != "已超期" && $auditTimeOut){
					$moduleNameList[$v['tablename']]['auditTimeOut'] = $auditTimeOut;
				}
				$moduleNameList[$v['tablename']]['count'] = $moduleNameList[$v['tablename']]['count']+1;
			}else{
				$moduleNameList[$v['tablename']] = array(
						'createtime'=>$v['createtime'],
						'title'=>"[审批]".getFieldBy($v['tablename'], 'name', 'title', 'node'),
						'url'=>__APP__."/MisWorkExecuting/index/jump/3/md/MisWorkMonitoring/worktype/1/rel/MisWorkExecutingbox/mytodoname/".$v['tablename'],
						'count'=>1,
						'auditTimeOut'=>$auditTimeOut,
				);
			}
		}
		//工作审批 end
		
		//审批后执行数组
		$Model = M();
		//查询 可执行的 模块 名称
		$isexecutoridSql="SELECT a.`name`,a.cname,b.executorid AS executorida,b.turnexecutorid,p.executorid
				FROM mis_work_executing_set AS a
				LEFT JOIN mis_work_executing AS b ON a. NAME = b.tablename
				LEFT JOIN process_info as p ON p.id = b.ptmptid
				WHERE dotype <> 1 and a.status=1";
		$isexecutoridArr=$this->query($isexecutoridSql);
		
		$executorid=array();//执行人 数组
		$workExecutor=array();//定义 可执行的tablename
		foreach ($isexecutoridArr as $k=>$v){
			$executorid=array_merge(explode(",", $v['executorida']),explode(",", $v['turnexecutorid']),explode(",", $v['executorid']));
			if(in_array($userid, $executorid) || isset($_SESSION['a']) ){//当前登录人在执行人数组中
				$workExecutor[]=$v['name'];
			}
		}
		//构造 查询 要处理的数量sql
		$numsql.="SELECT COUNT(tablename) AS num, tablename, b.executorid AS executoridb, b.turnexecutorid,p.executorid
				FROM mis_work_executing_set AS a
				LEFT JOIN mis_work_executing AS b ON a. NAME = b.tablename
				LEFT JOIN process_info AS p ON b.ptmptid = p.id
				WHERE (dotype <> 1)";
		
		if(!isset($_SESSION['a'])){//如果不是管理员 过滤条件
			$numsql.="AND ( ( FIND_IN_SET('$userid', turnexecutorid) )
			OR ( FIND_IN_SET('$userid', b.executorid) )
			OR ( FIND_IN_SET('$userid', p.executorid) ) ) ";
		}
		$numsql.="GROUP BY tablename";
		//获取 需要处理的数量 数组
		$numlist=$this->query($numsql);
		//查询 信息sql
		$workmap['name']=array('in',$workExecutor);
		$workmap['dotype']=array('neq',1);
		$workSet=M("mis_work_executing_set");
		//数组
		$arrlist=$workSet->field("mis_work_executing_set.id,name,b.createtime,cname,dotype")->where($workmap)->join(" LEFT JOIN mis_work_executing as b ON b.tablename =mis_work_executing_set.`name`")->group("tablename")->select();
		
		//组装数量到数组中
		foreach ($numlist as $k=>$v){
			foreach ($arrlist as $k1=>$v1){
				if($v['tablename'] == $v1['name']){
					$moduleNameList[$v1['name'].$k1] = array(
							'createtime'=>$v1['createtime'],
							'title'=>"[待办]".$v1['cname'],
							'url'=>__APP__."/MisWorkExecuting/index/jump/4/md/".$v1['name']."/dotype/0/rel/".$v1['name']."_1",
							'count'=>$v['num'],
					);
				}
			}
			$daibanNum +=$v['num'];//待办总数量
		}
		//工作协同待办  查询待办事项条数
		$MisOaFlowsInstanceModel = D("MisOaFlowsInstance");
		$oamapdoing['flowsuser']= $userid;
		$oamapdoing['status']=1;
		$oamapdoing['dostatus'] = array('lt',2);
		$oaitemdoing=$MisOaFlowsInstanceModel->where($oamapdoing)->order("createtime desc")->select();
		
		if($oaitemdoing){
			$moduleNameList["MisWorkExecutingbox"] = array(
					'createtime'=>$oaitemdoing[0]['createtime'],
					'title'=>"[协同]工作协同",
					'url'=>__APP__."/MisWorkExecuting/index/jump/5/md/MisOaItems/type/3/rel/MisWorkExecutingbox",
					'count'=>count($oaitemdoing),
			);
		}
		//对数组进行排序
		rsort($moduleNameList);
		//循环数组。进行li的封装
		if($moduleNameList){
			foreach($moduleNameList as $key=>$val){
				$workexeList['html'] .= '<li><a tabid="MisWorkExecuting" tabname="工作中心" href="javascript:;" taburl="'.$val['url'].'">'.$val['title'].'&nbsp;( '.$val['count'].' )</a></li>';
			}
		}
		$daibanNum +=count($oaitemdoing);//待办总数量
		if($daibanNum > 99){
			$daibanNum = '99+';
		}
		$workexeList['daiban'] = $daibanNum;
		$workexeList['list'] = $moduleNameList;
		return $workexeList;
	}
	
	public function test(){
		//获取流程自选知会人
		$informpersonid = $_POST['informpersonid'];
		$dhinformpersonid=implode(",",$informpersonid);
		if($informpersonid){
				// 发送邮件,加入知会人
			$this->workNoticeMessages ( $dhinformpersonid, false, $name, $vo ['id'] );
			// 插入工作知会
			$_POST ['informpersonid'] = $dhinformpersonid;
			if ($tid != - 1 && ! $tonextprocessnode) {
				$this->insertWorkExecuting ();
			}
		}
	}
	
	
	
	/**
	 * @Title: insertWorkExecuting
	 * @Description: todo(插入已审批，待执行的工作安排)
	 * @author 杨希
	 * @date 2014-4-21 下午4:40:12
	 * @throws
	 */
	public function addWorkExecuting($tablename,$tableid,$ostatus){
		$data = array();
		//查询当前这条记录
		$list=D($tablename)->where("id = ".$tableid)->find();
		if($list)
			//拼装数据
			$data['tablename']= $tablename;
			$data['tableid']= $tableid;
			$data['orderno']= $list['orderno'];
			$data['ptmptid']= $list['ptmptid'];
			$data['informpersonid']= $list['informpersonid'];
			// 判断 如果是打回或者是 审核完成 不进行插入新工作
			if($result){
				$data['updateid'] = $_SESSION[C('USER_AUTH_KEY')];
				$data['updatetime'] = time();
				//更新一条记录
				$result=$this->where($map)->save($data);
				if(!$result){
					$this->error('更新执行事项出错啦！请联系管理员！');
				}
			}else{
				$workdata=$_POST;
				unset($workdata['id']);
				// 通过POST 插入一条新的工作任务
				if (false === $this->create ($workdata)) {
					$this->error ( $this->getError () );
				}
				$result = $this-> add();
				if(!$result){
					$this->error('新增执行事项出错啦！请联系管理员！');
				}
			}
		}
	
		
	function lookupGetBiaojueNum(){
		//查询评审会类型
		$MisAutoJtmModel=D('MisAutoJtm');
		$MisAutoJtmList=$MisAutoJtmModel->where(array('status'=>1))->select();
		$num=0;
		//查询当前用户是否已经表决
		$subModel=M('mis_auto_fuhhu_sub_datatable5');
		$subList=$subModel->select();
		$name1='MisAutoVphSubView';
		$MisAutoZpgModel=D($name1);
		foreach ($MisAutoJtmList as $jk=>$jv){
			//查询评审会类型下的表决模板
			$ModelAction=$jv['duiyingmoban'];
			//查询该用户是否已经表决
			$model=D($ModelAction);
			
			$map['conveneStatus']=2;
			$map['shifoujieshu']=0;
			foreach ($subList as $k=>$val){
				//查询是否该用户是否在需要表决里面
				$neibuArr= explode(',',$val['neiburenyuan']);
				$neibiaojueArr=explode(',',$val['userid']);
				$waibuArr= explode(',',$val['waiburenyuan']);
				$waibiaojueArr=explode(',',$val['expertid']);
				$waibulist=in_array($_SESSION[C('USER_AUTH_KEY')],$waibuArr);
				$waibiaojuelist=in_array($_SESSION[C('USER_AUTH_KEY')],$waibiaojueArr);
				$neibulist=in_array($_SESSION[C('USER_AUTH_KEY')],$neibuArr);
				$neibiaojuelist=in_array($_SESSION[C('USER_AUTH_KEY')],$neibiaojueArr);
					
				if(($neibulist && !$neibiaojuelist) || ($waibulist && !$waibiaojuelist)){
					$biaojueMap['pingshenhuiid']=$val['pingshenhuiid'];
					$biaojueMap['zhaojidanid']=$val['masid'];
					$biaojueMap['zhaojidansubid']=$val['id'];
					$biaojueMap['_string']='userid='.$_SESSION[C('USER_AUTH_KEY')].' or expertid='.$_SESSION[C('USER_AUTH_KEY')];
					$biaojuelist=$model->where($biaojueMap)->select();
					if(empty($biaojuelist)){
						$subId[]=$val['id'];
					}
				}
				$map['pingshenhuileixing']=$jv['id'];
				$map['id']=array('in',$subId);
			}
			$count = $MisAutoZpgModel->where ( $map )->count ( '*' );
			$num=$num+$count;
		}
		return $num;
	}
	
	function getShangjiNum(){
		$MisSaleBusinessModel=D('MisSaleBusiness');
		$busMap['businessstatus']=array('in',array('2',3));
		$busMap['userid']=$_SESSION [C ( 'USER_AUTH_KEY' )];
		$busList=$MisSaleBusinessModel->where($busMap)->count( '*' );
		return $busList;
		
	}
		
}
?>