<?php
/** 
 * @Title: MisWorkMonitoringModel 
 * @Package package_name
 * @Description: 工作监控模型，模型作用 1、指定表，2、数据自动填充，3、数据验证、4、获取本表相关的数据信息  主要获取待审批的信息
 * @author 杨东 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-2-20 下午3:48:05 
 * @version V1.0 
*/ 
class MisWorkMonitoringModel extends CommonModel {
	//指定当前模型对应的表
	protected $trueTableName = 'mis_work_monitoring';
	//数据自动填充
	public $_auto =array(
		array("createid","getMemberId",self::MODEL_INSERT,"callback"),
		array("createid","getMemberId",self::MODEL_UPDATE,"callback"),
		array("createtime","time",self::MODEL_INSERT,"function"),
		array("updatetime","time",self::MODEL_UPDATE,"function"),
	    
			array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),

	);
	/**
	 * @Title: getUserAuditList 
	 * @Description: 获取当前登录人审批的数据信息
	 * @param 后台用户ID $userid 如果不传入参数，则取当前登录人，如果传入参数，则取传入的用户待审核信息
	 * @return 返回待$userid审核的数据数组
	 * @author liminggang 
	 * @date 2014-9-2 上午11:50:03 
	 * @throws
	 */
	public function getUserAuditList($userid){
		if(!$userid){
			//当前登录人id
			$userid = $_SESSION[C('USER_AUTH_KEY')];
		}
		//获取带我处理的工作条数信息
		$map['id']  = array('gt',0);
		$map['dostatus'] = 0;
		$map['isauditstatus'] = 1;
		$map['_string'] = 'FIND_IN_SET(  '.$userid.', curAuditUser )';
		$workdata = $this->where($map)->field("tablename,createtime")->select();
		//计算总条数
		$daibanNum = count($workdata);
		//定义变量存放  待我审核，待我执行的数据集合
		$moduleNameList=array();
		foreach( $workdata as $k=>$v ){
			$auditTimeOut= "";
			if(in_array($v['tablename'], array_keys($moduleNameList))){
				if($v['createtime']>$moduleNameList[$v['tablename']]['createtime']){
					$moduleNameList[$v['tablename']]['createtime'] = $v['createtime'];
				}
				$moduleNameList[$v['tablename']]['count'] += 1;
			}else{
				$moduleNameList[$v['tablename']] = array(
						'createtime'=>$v['createtime'],
						'title'=>"[审批]".getFieldBy($v['tablename'], 'name', 'title', 'node'),
						'url'=>"__APP__/MisWorkExecuting/index/jump/3/md/MisWorkMonitoring/worktype/1/rel/MisWorkExecutingbox/mytodoname/".$v['tablename'],
						'count'=>1,
						'auditTimeOut'=>$auditTimeOut,
				);
			}
		}
		//工作审批 end
		$UserAudit['list'] = $moduleNameList;
		$UserAudit['count'] = $daibanNum;
		return $UserAudit;
	}
	
	/**
	 * @Title: addWorkMonitoring
	 * @Description: todo(表单工作审核池数据) 
	 * @param string $tablename	当前控制器名称
	 * @param string $tableid   当前单据ID
	 * @param array $infolist   当前可执行的所有流程节点。
	 * @param bolean $douserid 
	 * @param int $dobool 流程回退标记，传入false表示是流程回退/代表子流程审批
	 * @return boolean  
	 * @author 黎明刚
	 * @date 2014年12月4日 下午4:07:41 
	 * @throws
	 */
	public function addWorkMonitoring($tablename,$tableid,$infolist = array(),$douserid = 0,$dobool = true){
		//获取当前登录人
		$userid = $douserid?$douserid:$_SESSION [C ( 'USER_AUTH_KEY' )];
		//获取当前用户名称
		$username = getFieldBy($userid, "id", "name", "user");
		//获取当前模型中文名称
		$modelname= getFieldBy($tablename, "name", "title", "node");
		// 查询当前这条记录
		$list = D ( $tablename )->where ( "id = " . $tableid )->find ();
		if ($list) {
			//表示终审
			if($list ['auditState'] == 3){
				//代表单据撤回。将单据状态改变
				$map = array ();
				$map ['tablename'] = $tablename;
				$map ['tableid'] = $tableid;
				$map ['dostatus'] = 1;
				$workdescid= $this->where($map)->order("id desc")->getField("id");
				if($workdescid){
					$result = $this->where ( "id = ".$workdescid )->setField ( "auditState",3 );
					if($result == false){
						return false;	
					}
				}
			}
			/*
			 * 流程撤回
			 */
			if($list ['auditState'] == 0){
				//代表单据撤回。将单据状态改变
				$map = array ();
				$map ['tablename'] = $tablename;
				$map ['tableid'] = $tableid;
				$map ['dostatus'] = 0;
				$result = $this->where ( $map )->delete ();
				if($result==false){
					return false;
				}
			}
			/*
			 * 处理状态为审核和打回，将进行当前工作池记录的修改。
			 */
			if ($list ['auditState'] == 2 || $list ['auditState'] == -1 || !$dobool) {
				// 修改以前的数据 修改为处理完成
				$map = array ();
				$map ['tablename'] = $tablename;
				$map ['tableid'] = $tableid;
				$map ['dostatus'] = 0;
				if($list ['auditState'] == 2 && $dobool){
					//此参数$brankbool 为false时，表示流程打回
					$map ['_string'] = 'FIND_IN_SET(  ' . $userid . ',curAuditUser )';
				}
				// 根据单据本身获取信息
				$data = array ();
				$data ['auditState'] = $list ['auditState'];
				// 2.POST传值获取信息
				$data ['doinfo'] = $_REQUEST ['doinfo'];
				$data ['dotime'] = time ();
				// 3、自动获取信息赋值
				$data ['dostatus'] = 1;
				$data ['updateid'] = $_SESSION [C ( 'USER_AUTH_KEY' )];
				$data ['userid'] = $_SESSION [C ( 'USER_AUTH_KEY' )];
				$data ['updatetime'] = time ();
				$result = $this->where ( $map )->save ( $data );
				if($result == false){
					return false;
				}
			}
			/*
			 * 判断流程不是打回，或者不是新建，则向工作池中推送审核人信息
			 * 1、判断推送的节点是什么类型的节点  
			 *    parallel 0 单批次审核人串行节点，1单批次审核人并行节点， 2多批次审核串并混搭
			 *    
			 *  0：单批次审核人串行，则向工作池只推送一条审核任务
			 *  1：单批次审核人并行，判断一共有多少个审核，则向工作池推送多少条审核信息
			 *  2：多批次审核人串并混搭，这种情况是最复杂的，先推送并行的，在推送单个并行节点上面的串行审批
			 *    
			 */
			if ($list ['auditState'] != - 1 && $list ['auditState'] != 0) {
				//存储unset之前的数组，方便下面运用
				foreach($infolist as $key=>$val){
					if($val['doing'] == 0 || $val['flowtype']<2){
						unset($infolist[$key]);
					}
				}
				$infolist = array_merge($infolist);
				//当前本该进行执行的节点数据
				$info = $infolist[0];
				if($infolist && $info){
					//定义标题大概
					if($info['title'] && $list[$info['title']]){
						//有定义标题大概，进行标题大概转换
						$scdmodel = D('SystemConfigDetail');
						//读取列名称数据(按照规则，应该在index方法里面)
						$detailList = $scdmodel->getDetail($tablename,false,'','sortnum','status');
						foreach($detailList as $k1=>$v1){
							if($v1['name'] == $info['title']){
								//判断标题大概是否匹配上字段
								if(count($v1['func']) >0){
									$varchar = "";
									foreach($v1['func'] as $k2=>$v2){
										//开始html字符
										if(isset($v1['extention_html_start'][$k2])){
											$varchar = $v1['extention_html_start'][$k2];
										}
										//中间内容
										$varchar .= getConfigFunction($list[$v1['name']],$v2,$v1['funcdata'][$k2],$list);
										if(isset($v1['extention_html_end'][$k2])){
											$varchar .= $v1['extention_html_end'][$k2];
										}
										//结束html字符
									}
									$list[$info['title']] = $varchar;
									break;
								}
							}
						}
					}
					/*
					 * 限制子流程生单，控制自动生单
					 */
					if($info['flowtype'] == 3 && $info['isauto'] == 1 && $list['projectid'] && false){
						//根据projectid查询来源模型数据
						$issourcemodel = D($info['issourcemodel']);
						//来源数据ID
						$issourceid = $issourcemodel->where("projectid = ".$projectid)->getField("id");
						if($issourceid){
							//子流程数据漫游  iatuo = 1 自动生单
							$MisSystemDataRoamingModel = D("MisSystemDataRoaming");
							//第一个参数为类型，3是子流程类型；第二个参数为来源模型，对应$info['tablename'];第三个参数为来源模型主键，对应$info['tableid'];
							//第四个参数为来源操作类型，这里可以扩展，初始指定为来源模型的修改动作为2;第五个参数为子流程对应的目标模型，这里对应$info['isauditmodel'];
							$MisSystemDataRoamingModel->main(3,$info['issourcemodel'],$issourceid,2,$info['isauditmodel']);
							//这里查询目标model最大值
							$isaudittableid = D($info['isauditmodel'])->max('id');
							M('process_relation_form')-> where('id='.$info['id'])->setField('isaudittableid',$isaudittableid);
						}else{
							return false;
						}
					}else{
						if($info['parallel'] == 2){
							//多批次审核人串并混搭
							$process_relation_parallelDao = M("process_relation_parallel");
							$map = array ();
							$map ['tablename'] = $tablename;
							$map ['tableid'] = $tableid;
							$map ['relation_formid'] = $info['id'];
							$map ['parentid'] = 0;
							$relation_parallellist = $process_relation_parallelDao->where($map)->select();
							foreach ($relation_parallellist as $pk=>$pv){
								if($pv['auditState'] == 0 && $pv['parentid'] == 0){
									//单批次审核人串行
									$data = array ();
									$data ['tablename'] = $tablename; // 单据对应表名称
									$data ['tableid'] = $tableid; // 单据对应表ID
									$data ['title'] = $list[$info['title']]; //存储标题大概
									
									$data ['orderno'] = $list['orderno']; // 单据对应表编号
									$data ['ptmptid'] = $list ['ptmptid']; // 流程ID
									$data ['auditState'] = $list ['auditState']; //表单状态
									
									$data ['relationname'] = $info['name']; //审核节点名称
									$data ['bactchname'] = $pv['bactchname']; //批次名称
									$data ['bactchid'] = $pv['id']; //批次id
									$data ['typeid'] = $info['typeid']; //流程分类
									//验证流程转授
									$relatinfo = $this->setZhuanShouCurAuditUser($pv['curAuditUser'],$tablename);
									$data ['curAuditUser'] = $relatinfo['curAuditUser']; //节点可审核人清单
									$data ['miaoshu'] = $relatinfo['zhuanmiaoshu']; //流程转授信息
									$data ['zhuanshou'] = $relatinfo['zhuanshou']?1:0; //转授标记
									
									$data ['ostatus'] = $info['id']; //可以审核节点
									$data ['parallel'] = $info['parallel']; ////单批次审核人串行
									$data ['ismobile'] = $info['ismobile']; //是否可以手机审批
									
									$data ['createid'] = $list ['createid']; // 创建人
									$data ['createtime'] = time(); // 创建时间
									//$data ['updateid'] = $list ['updateid']; // 更新人
									//$data ['updatetime'] = $list ['updatetime']; // 更新时间
									$data ['companyid'] = $list ['companyid']; // 所属公司
									$data ['departmentid'] = $list ['departmentid']; // 所属部门
									$data ['projectid'] = $list ['projectid']; // 项目id
									$data ['projectworkid'] = $list ['projectworkid']; // 项目任务id
									//通过POST 插入一条心的工作任务
									$result = $this->add ( $data );
									if($result == false){
										return false;
									}else{
										$process_relation_parallelDao->where("id = ".$pv['id'])->setField("auditState",1);
									}
								}
								if($pv['auditState'] == 2){
									//完成的批次
									$diguivo = $this->diguiList($pv['id']);
									if($diguivo){
										//单批次审核人串行
										$data = array ();
										$data ['tablename'] = $tablename; // 单据对应表名称
										$data ['tableid'] = $tableid; // 单据对应表ID
										$data ['title'] = $list[$info['title']]; //存储标题大概
										$data ['orderno'] = $list['orderno']; // 单据对应表编号
										$data ['ptmptid'] = $list ['ptmptid']; // 流程ID
										$data ['auditState'] = $list ['auditState']; //表单状态
										
										$data ['relationname'] = $info['name']; //审核节点名称
										$data ['bactchname'] = $diguivo['bactchname']; //批次名称
										$data ['bactchid'] = $diguivo['id']; //批次ID
										$data ['typeid'] = $info['typeid']; //流程分类
										//$data ['curAuditUser'] = $diguivo['curAuditUser']; //节点可审核人清单
										//验证流程转授
										//$data ['curAuditUser'] = $this->setZhuanShouCurAuditUser($diguivo['curAuditUser'],$tablename);
										//验证流程转授
										$relatinfo = $this->setZhuanShouCurAuditUser($diguivo['curAuditUser'],$tablename);
										$data ['curAuditUser'] = $relatinfo['curAuditUser']; //节点可审核人清单
										$data ['miaoshu'] = $relatinfo['zhuanmiaoshu']; //流程转授信息
										$data ['zhuanshou'] = $relatinfo['zhuanshou']?1:0; //转授标记
										
										$data ['ostatus'] = $info['id']; //可以审核节点
										$data ['parallel'] = $info['parallel']; //单批次审核人串行
										$data ['ismobile'] = $info['ismobile']; //是否可以手机审批
										
										$data ['createid'] = $list ['createid']; // 创建人
										$data ['createtime'] = time(); // 创建时间
										//$data ['updateid'] = $list ['updateid']; // 更新人
										//$data ['updatetime'] = $list ['updatetime']; // 更新时间
										$data ['companyid'] = $list ['companyid']; // 所属公司
										$data ['departmentid'] = $list ['departmentid']; // 所属部门
										$data ['projectid'] = $list ['projectid']; // 项目id
										$data ['projectworkid'] = $list ['projectworkid']; // 项目任务id
										//通过POST 插入一条心的工作任务
										$result = $this->add ( $data );
										if($result == false){
											return false;
										}else{
											$process_relation_parallelDao->where("id = ".$diguivo['id'])->setField("auditState",1);
											//进入推送信息
											$this->pushInfo($relatinfo['curAuditUser'],$modelname,$data ['title']);
										}
									}
								}
							}
						}else if($info['parallel'] == 1){
							//单批次并行没人审核时才推送
							$alreadyAuditUser = array_filter(explode(",", $info['alreadyAuditUser']));
							if(count($alreadyAuditUser) == 0){
								$userstr = "";
								//单批次审核人并行
								$curAuditUser = explode(",", $info['curAuditUser']);
								$dataAll = array ();
								foreach($curAuditUser as $key=>$val){
									$data = array ();
									$data ['tablename'] = $tablename; // 单据对应表名称
									$data ['tableid'] = $tableid; // 单据对应表ID
									$data ['title'] = $list[$info['title']]; //存储标题大概
									$data ['orderno'] = $list['orderno']; // 单据对应表编号
									$data ['ptmptid'] = $list ['ptmptid']; // 流程ID
									$data ['auditState'] = $list ['auditState']; //表单状态
									$data ['typeid'] = $info['typeid']; //流程分类
									$data ['relationname'] = $info['name']; //审核节点名称
									//验证流程转授
									$relatinfo = $this->setZhuanShouCurAuditUser($val,$tablename);
									//组合推送用户信息
									$userstr.=",".$relatinfo;
									$data ['curAuditUser'] = $relatinfo['curAuditUser']; //节点可审核人清单
									$data ['miaoshu'] = $relatinfo['zhuanmiaoshu']; //流程转授信息
									$data ['zhuanshou'] = $relatinfo['zhuanshou']?1:0; //转授标记
									
									$data ['ostatus'] = $info['id']; //可以审核节点
									$data ['parallel'] = $info['parallel']; //单批次审核人并行
									$data ['ismobile'] = $info['ismobile']; //是否可以手机审批
									
									$data ['createid'] = $list ['createid']; // 创建人
									$data ['createtime'] = time(); // 创建时间
									//$data ['updateid'] = $list ['updateid']; // 更新人
									//$data ['updatetime'] = $list ['updatetime']; // 更新时间
									$data ['companyid'] = $list ['companyid']; // 所属公司
									$data ['departmentid'] = $list ['departmentid']; // 所属部门
									$data ['projectid'] = $list ['projectid']; // 项目id
									$data ['projectworkid'] = $list ['projectworkid']; // 项目任务id
									$dataAll[] = $data;
								}
								if($dataAll){
									//通过POST 插入一条心的工作任务
									$result = $this->addAll ( $dataAll );
									if($result == false){
										return false;
									}
									$userarr = array_unique(array_filter(explode(",", $userstr)));
									$pushcurAuditUser = implode(",", $userarr);
									//进入推送信息
									$this->pushInfo($pushcurAuditUser,$modelname,$data ['title']);
								}
							}
						}else{
							//单批次审核人串行
							$data = array ();
							$data ['tablename'] = $tablename; // 单据对应表名称
							$data ['tableid'] = $tableid; // 单据对应表ID
							$data ['title'] = $list[$info['title']]; //存储标题大概
							$data ['orderno'] = $list['orderno']; // 单据对应表编号
							$data ['ptmptid'] = $list ['ptmptid']; // 流程ID
							$data ['auditState'] = $list ['auditState']; //表单状态
							
							$data ['relationname'] = $info['name']; //审核节点名称
							$data ['typeid'] = $info['typeid']; //流程分类
							//验证流程转授
							$relatinfo = $this->setZhuanShouCurAuditUser($info['curAuditUser'],$tablename);
							$data ['curAuditUser'] = $relatinfo['curAuditUser']; //节点可审核人清单
							$data ['miaoshu'] = $relatinfo['zhuanmiaoshu']; //流程转授信息
							$data ['zhuanshou'] = $relatinfo['zhuanshou']?1:0; //转授标记
							
							//$data ['curAuditUser'] = $info['curAuditUser']; //节点可审核人清单
							$data ['ostatus'] = $info['id']; //可以审核节点
							$data ['parallel'] = $info['parallel']; ////单批次审核人串行
							$data ['ismobile'] = $info['ismobile']; //是否可以手机审批
							
							$data ['createid'] = $list ['createid']; // 创建人
							$data ['createtime'] = time(); // 创建时间
							//$data ['updateid'] = $list ['updateid']; // 更新人
							//$data ['updatetime'] = $list ['updatetime']; // 更新时间
							$data ['companyid'] = $list ['companyid']; // 所属公司
							$data ['departmentid'] = $list ['departmentid']; // 所属部门
							$data ['projectid'] = $list ['projectid']; // 项目id
							$data ['projectworkid'] = $list ['projectworkid']; // 项目任务id
							//通过POST 插入一条心的工作任务
							$result = $this->add ( $data );
							if($result == false){
								return false;
							}
							//进入推送信息
							$this->pushInfo($relatinfo['curAuditUser'],$modelname,$data ['title']);
						}
					}
					//推送成功，将process_relation_form表可以进行干预的节点标记出来
					$setbool = M("process_relation_form")->where("id = ".$info['id'])->setField("ganyustate",1);
					if($setbool == false){
						return false;
					}
				}
			}
			return true;
		} else {
			return false;
		}
	}
	public function pushInfo($curAuditUser,$modelname,$title){
		//把要推送的信息存入APP推送表
 		$appModel=M("mis_system_app_tuisong");
 		$data = array();
 		$data['title'] = "你有{$modelname}需要审批:{$title}";
 		$data['userIds'] = $curAuditUser;
 		$data['status'] = 1;
 		$data['createtime'] = time();
 		$bool = $appModel->add($data);
 		if($bool == false){
 			return false;
 		}
	}
	/**
	 * @Title: setZhuanShouCurAuditUser 
	 * @Description: todo(流程转授，替换当前审核人) 
	 * @param 当前审核人 $curAuditUser
	 * @param 当前模型名称 $tablename
	 * @return string  
	 * @author liminggang
	 * @date 2015-7-24 下午5:19:37 
	 * @throws
	 */
	private function setZhuanShouCurAuditUser($curAuditUser,$tablename){
		$newarr['curAuditUser'] = $curAuditUser;
		$newarr['zhuanmiaoshu'] = "";
		$curArr = array_unique(explode(",", $curAuditUser));
		$bool = false;
		if($curArr){
			//实例化流程转授模型
			$mis_auto_guikcDao = M("mis_auto_guikc");
			$miaoshu = "";
			foreach($curArr as $key=>$val){
				$where = array();
				$where['operateid'] = 1;
				$where['zhuanshouren'] = $val;
				$where['shengxiaoriqi'] = array('elt',time());
				$where['shixiaoriqi'] = array('egt',time());
				$where['zhuanshoufanwei'] = "全部";
				$zlist = $mis_auto_guikcDao->where($where)->field("id,zhuanshoufanwei,zhuanshougei")->order("id desc")->find();
				if($zlist){
					$bool = true;//存在转授人员标记
					$curArr[$key] = $zlist['zhuanshougei'];
					$miaoshu = getFieldBy($val, "id", "name", "user")."转授给".getFieldBy($zlist['zhuanshougei'], "id", "name", "user");
				}else{
					$where['zhuanshoufanwei'] = "部分";
					$zvolist = $mis_auto_guikcDao->where($where)->field("id,zhuanshoufanwei,zhuanshougei")->select();
					if($zvolist){
						$bool = true;//存在转授人员标记
						//存在定向制定流程转授模块
						$mis_auto_guikc_sub_datatable6Dao = M("mis_auto_guikc_sub_datatable6");
						foreach($zvolist as $k=>$v){
							$where = array();
							$where['masid'] = $v['id'];
							$where['zhuanshoumoxing'] = $tablename;
							$count = $mis_auto_guikc_sub_datatable6Dao->where($where)->count();
							if($count){
								$curArr[$key] = $v['zhuanshougei'];
								$miaoshu = getFieldBy($val, "id", "name", "user")."转授给".getFieldBy($v['zhuanshougei'], "id", "name", "user");
								break;
							}
						}
					}
				}
			}
			$curArr = array_unique(array_filter($curArr));
			$curAuditUser = implode(",", $curArr);
			$newarr['curAuditUser'] = $curAuditUser;
			$newarr['zhuanmiaoshu'] = $miaoshu;
			$newarr['zhuanshou'] = $bool;
		}
		return $newarr;
	}
	public function diguiList($id){
		//多批次审核人串并混搭
		$process_relation_parallelDao = M("process_relation_parallel");
		$map = array();
		$map['parentid'] = $id;
		$diguivo = $process_relation_parallelDao->where($map)->find();
		if($diguivo){
			if($diguivo['auditState'] == 0){
				return $diguivo;
			}else if($diguivo['auditState'] == 2){
				return $this->diguiList($diguivo['id']);
			}else{
				return array();
			}
		}else{
			return $diguivo;
		}
	}
	
	
	
// 	//当前节点可审核人
// 	$curAuditUser = $infolist[0]['curAuditUser'];
// 	if($infolist[0]['parallel'] == 2){
// 		$curAuditUser = array();
// 		//多批次并行。需要解析多批次审核人员
// 		$countAuditUser = explode(";", $infolist[0]['curAuditUser']);
// 		foreach($countAuditUser as $kk=>$vv){
// 			$countAuditUser1 =  explode(",", $vv);
// 			foreach($countAuditUser1 as $kkk=>$vvv){
// 				array_push($curAuditUser, $vvv);
// 			}
// 		}
// 		$curAuditUser = implode(",", $curAuditUser);
// 	}
// 	$countAuditUser = array_filter(explode(",", $curAuditUser));
// 	/*
// 	 * 此处调用漫游。推单
// 	*/
// 	if($infolist[0]['flowtype'] == 3 && !$countAuditUser){
// 		//子流程数据漫游 (如果是子流程，并且子流程不存在生单人，则进行静默漫游，直接推送数据信息)
	
// 		$MisSystemDataRoamingModel = D("MisSystemDataRoaming");
// 		//第一个参数为类型，3是子流程类型；第二个参数为来源模型，对应$infolist[0]['tablename'];第三个参数为来源模型主键，对应$infolist[0]['tableid'];
// 		//第四个参数为来源操作类型，这里可以扩展，初始指定为来源模型的修改动作为2;第五个参数为子流程对应的目标模型，这里对应$infolist[0]['isauditmodel'];
// 		$MisSystemDataRoamingModel->main(3,$infolist[0]['tablename'],$infolist[0]['tableid'],2,$infolist[0]['isauditmodel']);
// 		//这里查询目标model最大值
// 		$isaudittableid = D($infolist[0]['isauditmodel'])->max('id');
// 		M('process_relation_form')-> where('id='.$infolist[0]['id'])->setField('isaudittableid',$isaudittableid);
// 	}
}
?>