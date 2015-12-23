<?php
/**
 * @Title: MisTaskInfoPointAction
 * @Package 任务管理-任务管理动态-归属任务：功能类
 * @Description: TODO(任务管理-任务管理动态-执行任务)
 * @author jiangx
 * @company 重庆特米洛科技有限公司˾
 * @copyright 重庆特米洛科技有限公司˾
 * @date 2013-6-06
 * @version V1.0
 */   
class MisTaskAdscriptionAction extends CommonAction{
    /**
	 * @Title: _filter 
	 * @Description: todo(构造检索条件) 
	 * @param HASHMAP $map  
	 * @author jiangx 
	 * @throws 
	*/  
    public function _filter(&$map){
        if ($_SESSION["a"] != 1) {
            $map['status']=array("gt",-1);
        }
        $map['executeuser'] = isset($_SESSION[C('USER_AUTH_KEY')])?$_SESSION[C('USER_AUTH_KEY')]:0;
    }
    /**
	 * @Title: index 
	 * @Description: todo(index) 
	 * @author jiangx 
	 * @throws 
	*/ 
    public function index(){
    	$map = array();
    	$map = $this->_search ();
    	if (method_exists ( $this, '_filter' )) {
    		$this->_filter ( $map );
    	}
    	//进行中
    	$map = array();
    	$map['status'] = 1;
		$map['executingstatus'] = array('not in', '4,7');
    	$map['executeuser'] = $_SESSION[ C('USER_AUTH_KEY') ];
    	$map['executingstatus'] = array('EQ', 3);
    	$viewModel = D("MisTaskInformationView");
    	//状态0未启动1未查看2已查看3进心中4关闭5暂停6完成7结束8申请暂停
    	$doinglist = $viewModel->where($map)->field('id,title,createid,enddate,difficulty,urgentstatus')->order('id DESC')->select();
    	$this->assign('doing',$doinglist);
    	//已完成
    	$map['executingstatus'] = array('in', '6,7');
    	$overlist = $viewModel->where($map)->field('id,title,createid,realityenddate,difficulty,urgentstatus')->order('id DESC')->select();
    	$this->assign('complete',$overlist);
    	//未完成
    	$map['executingstatus'] = array('in', '0,1,2,4,5,8');
    	$nooverlist = $viewModel->where($map)->field('id,title,createid,enddate,difficulty,urgentstatus')->order('id DESC')->select();
    	$this->assign('uncomplete',$nooverlist);
        $this->display();
    }
    /**
	 * @Title: communicationFeedback
	 * @Description: todo(获取反馈信息)
	 * @param $taskid 任务id
	 * @author jiangx
	 * @throws
	*/
    private function communicationFeedback($taskid){
        $model = D("MisTaskFeedbackInformation");
        $map = array();
        $map['type'] = array('in', '2,3');
        $map['taskid'] = $taskid;
        $map['status'] = 1;
        $feedbacklist = $model->where($map)->order('id DESC')->select();
		$feedbackarrlist = array();
		foreach ($feedbacklist as $key => $val) {
			$feedbacklist[$key]['typename'] = $val['type'] == 2 ? '交流' : '审核';
			$val['typename'] = $val['type'] == 2 ? '交流' : '审核';
			if ($val['pid'] == 0) {
				$feedbackarrlist[$val['id']] = $val;
				unset($feedbacklist[$key]);
			}
		}
		
		foreach ($feedbacklist as $key => $val) {
			$feedbackarrlist[$val['pid']]['child'][$val['id']] = $val;
			if ( count ($feedbackarrlist[$val['pid']]['child']) >= 3 ) {
				$feedbackarrlist[$val['pid']]['shrink'] = true;
			}
		}
        $this->assign('feedbackarrlist',$feedbackarrlist);
        $this->display("communicationFeedback");
    }
    /**
	 * @Title: lookupSaveFeedback
	 * @Description: todo(ajax保存反馈信息)
	 * @author jiangx
	 * @throws
	*/
    public function lookupSaveFeedback(){
        foreach ($_POST['data'] as $key => $val) {
			$_POST[$key] = $val;
		}
        $_POST['__hash__'] = $_POST['hash'];
		unset($_POST['data']);
		unset($_POST['hash']);
        $model = D("MisTaskFeedbackInformation");
        if (false === $model->create ()) {
            exit('0');
        }
        //保存当前数据对象
        $list=$model->add ();
        if ($list === false) {
            exit('0');
        }
        $this->transaction_model->commit();//事务提交
        exit('1');
    }
    /**
	 * @Title: _after_edit
	 * @Description: todo(_after_edit)
	 * @author jiangx
	 * @throws
	*/
    public function _after_edit($vo){
		$this->assign('zhi', $_REQUEST['zhi']);
        $this->assign( 'vo', $vo );
        //获取任务头信息
        $model = D('MisTask');
        $task = $model->where('id=' . $vo['taskid'])->find();
        //判断是否包含子任务
        $count = $model->where('pid = ' . $task['id'])->count();
        $this->assign('ishavechild', $count);
        //判断是查看还是可操作
        $taskisView = true;
		//判断交流反馈是否可回复 (即是否结束或关闭)
		$isexchange = true;
        if ($vo['executingstatus'] >3) {
            $taskisView = false;
        }
        if ($_SESSION[C('USER_AUTH_KEY')] !=  $vo['executeuser']) {
            $taskisView = false;
			$isexchange = false;
        }
        $this->assign('taskView', $taskisView);
        $this->assign('task',$task);
		
		if ($vo['executingstatus'] == 4 || $vo['executingstatus'] == 7) {
			$isexchange = false;
		}
		$this->assign('isexchange', $isexchange);
        //获取父任务头信息
        $pidtask = $model->where('id=' . $task['pid'])->find();   
        $this->assign('pidtask', $pidtask);
        $navtab = $_REQUEST['navtab'];
		//获取该任务结束的时间,如果没有结束，则当前时间拟定为结束时间
		if ($info['executingstatus'] == 4 || $info['executingstatus'] == 5 || $info['executingstatus'] == 7) {
			$taskendtime = $info['updatetime'];
		} else {
			$taskendtime = time();
		}
		$this->assign('taskendtime', $taskendtime);
        switch ($navtab) {
            //case 'getAttachment':
            //    //附件文档
            //    $this->getAttachment($_REQUEST['id']);
            //    exit;
            //    break;
            case 'communicationFeedback':
                //交流与反馈
                $this->communicationFeedback($_REQUEST['taskid']);
                exit;
                break;
            case 'lookupIdea':
                //跟踪意见
                $this->lookupIdea($_REQUEST['taskid']);
                exit;
                break;
            case 'lookupPlanCondition':
                //进度情况
                $this->lookupPlanCondition($_REQUEST['taskid'], $vo, $taskendtime);
                exit;
                break;
            case 'relevques':
                //问题列表
                $this->relevques($_REQUEST['taskid']);
                exit;
                break;
			case 'getchildtask':
				//获取子任务信息
				$this->getChildTaskView($_REQUEST['taskid']);
				exit;
				break;
        }
        //设置为已查看，并添加实际开始时间
        if ($vo['executingstatus'] == 1) {
            $data = array('executingstatus' => 2, 'realityknowdate' => time());
            $model = D("MisTaskAdscription");
            $result = $model->where('id = ' .$vo['id'])->setField($data);
            if ($result) {
                $this->transaction_model->commit();//事务提交
            }
        }
        //查看关注
        $attentModel = D("MisTaskAttention");
        $map = array();
        $map['tableid'] = $vo['id'];
        $map['modelname'] = "MisTaskAdscription";
        $map['type'] = 3;
        $attent = $attentModel->where($map)->find();
        $this->assign('attent', $attent);
		//获取跟踪人最后一次审核
		$model = D('MisTaskFeedbackInformation');
		$map = array();
		$map['taskid'] = $vo['taskid'];
		$map['createid'] = $vo['trackuser'];
		$endtrackremark = $model->where($map)->order('id DESC')->getField('remark');
		$this->assign('endtrackremark', $endtrackremark);
		$map['createid'] = $vo['createid'];
		$endcreateremark = $model->where($map)->order('id DESC')->getField('remark');
		$this->assign('endcreateremark', $endcreateremark);
		//时钟字体颜色区分
		$setupModel = D("MisTasksColorCoded");
		$map = array();
		$map['status'] = 1;
		$map['createid'] = $_SESSION[C('USER_AUTH_KEY')];
		$setupinfo = $setupModel->where($map)->find();
		if ($setupinfo) {
			$colorlist = explode(':', $setupinfo['colorcoded']);
			$intervaltime = $vo['enddate'] - time();
			if ((60 * 60 * $setupinfo['interval']) < $intervaltime) {
				$timescolor = $colorlist[0];
			}
			if (0 > $intervaltime) {
				$timescolor = $colorlist[2];
			}
			if ((60 * 60 * $setupinfo['interval']) >= $intervaltime && $intervaltime >= 0) {
				$timescolor = $colorlist[1];
			}
			if ($timescolor) {
				$this->assign('timescolor', $timescolor);
			}
		}
    }
	/**
	 * @Title: getAttachment
	 * @Description: todo(获取附件)
	 * @param $id 任务基本信息id
	 * @author jiangx
	 * @throws
	*/
    private function getAttachment($id){
		$this->assign('id', $id);
		$this->assign('asattarrytype', $_REQUEST['asattarrytype']);
		//附件id为infoid 反馈为taskid
        //获取附件信息
		$map = array();
        $map["status"]  =1;
        $map["orderid"] =$id;
        $map["type"] =66;
        $m=M("mis_attached_record");
        $attarry=$m->where($map)->select();
		$attarrylist = array();
		foreach ($attarry as $key => $val) {
			$attarrylist[$val['subid']][] = $val;
		}
		//根据反馈信息区分附件
		$feedModel = D("MisTaskFeedbackInformation");
		$infoModel = D("MisTaskInformation");
		$info = $infoModel->where('id = ' .$id)->find();
		$feedlist = $feedModel->where('taskid ='.$info['taskid'].' AND (type =1 OR isdefect=1)')->getField('id,pid,type,statustype,isdefect,createtime');
		//缺陷反馈
		$defectlist = array();
		foreach ($feedlist as $key => $val) {
			$feedlist[$key]['attarry'] = $attarrylist[$key];
			if ($val['isdefect'] == 1) {
				$defectlist[$key] = $val;
				//缺陷反馈的id数组
				$defectidarr[] = $key;
			}
		}
		//插入需求附件
		$feedlist[0]['attarry'] = $attarrylist[0];
		$feedlist[0]['createtime'] = $info['createtime'];
		if ($defectidarr) {
			$map["orderid"] = array('in', $defectidarr);
			$map["type"] =72;
			$attarry=$m->where($map)->select();
			$attarrylist = array();
			foreach ($attarry as $key => $val) {
				$attarrylist[$val['orderid']][] = $val;
			}
			foreach ($defectidarr as $key => $val) {
				$feedlist[$val]['attarry'] = $attarrylist[$val];
			}
		}
		//按key升序排序
		ksort($feedlist);
		$attarrylistselecet = array();
		//1:需求附件,2:缺陷附件,3:成果附件,6:完成附件,8:申请暂停附件,9:缺陷解决附件
		foreach($feedlist as $key => $val) {
			$feedlist[$key]['attcount'] = count($val['attarry']);
			if (!$feedlist[$key]['attcount']) {
				continue;
			}
			if ($key == 0) {
				$feedlist[$key]['statustype'] = 1;
			}
			if ($val['isdefect'] && $val['pid'] == 0) {
				$feedlist[$key]['statustype'] = 2;
			}
			if ($val['pid'] != 0) {
				$feedlist[$key]['statustype'] = 9;
			}
			//筛选
			if ($_REQUEST['asattarrytype']) {
				if ($_REQUEST['asattarrytype'] == $feedlist[$key]['statustype']) {
					$attarrylistselecet[] = $feedlist[$key];
				}
				//如果文档类型为2 (缺陷文档)，则包括9 (缺陷解决文档)
				if ($_REQUEST['asattarrytype'] == 2) {
					if (9 == $feedlist[$key]['statustype']) {
						$attarrylistselecet[] = $feedlist[$key];
					}
				}
			} else {
				$attarrylistselecet[] = $feedlist[$key];
			}
		}
		$attarrytype = array(
			'0' => '全部文档',
			'1' => '需求文档',
			'2' => '缺陷文档',
			'3' => '成果文档',
			'8' => '申请文档',
		);
		$this->assign('attarrytype', $attarrytype); 
		$this->assign('feedlist', $attarrylistselecet);
        $this->display("getAttachment");
    }
	/**
	 * @Title: lookupPlanCondition
	 * @Description: todo(进度情况)
	 * @author jiangx
	 * @throws
	*/
	private function lookupPlanCondition($taskid, $info, $endtime){
		$this->assign('asattarrytype', $_REQUEST['asattarrytype']);
		$attarrytype = array(
			'-1'=> '需求附件',
			'0' => '全部信息',
			'3' => '成果信息',
			'6' => '完成操作信息',
			'8' => '申请信息',
		);
		$this->assign('attarrytype', $attarrytype);
    	$feedbackInformationModel = D('MisTaskFeedbackInformation');
    	$map = array();
		$map['type'] = 1;
    	$map['status'] = 1;
    	$map['taskid'] = $taskid;
		$feedlist = $feedbackInformationModel->where($map)->order('createtime DESC')->select();
		//获取任务的开始时间戳
		$begintime = strtotime(date('Y-m-d', $info['begindate']));
		//获取该任务结束的时间,如果没有结束，则当前时间拟定为结束时间
		$endtime = strtotime(date('Y-m-d', $endtime)) + 86400;
		//结束时间 - 开始时间/一天的时间戳 = 天数
		$days = floor (($endtime - $begintime) / 86400);
		//获取附件
		$map = array();
		$map["status"]  =1;
		$map["orderid"] =$info['id'];
		$map["type"] =66;
		$map["_string"] ="subid is not null";
		$m=M("mis_attached_record");
		$attarry=$m->where($map)->select();
		//获取需求文档
		$attarrycreate = array();
		//附件以反馈信息id分组
		$attarraylist = array();
		foreach ($attarry as $key => $val) {
			$attarraylist[$val['subid']][] = $val;
			if (!$val['subid']) {
				$attarrycreate['attarrys'][] = $val;
			}
		}
		$attarrycreate['datetime'] = $info['createtime'];
		$attarrycreate['count'] = count($attarrycreate['attarrys']);
		//如果附件条件为全部，需求文档或则为非
		if (!$_REQUEST['asattarrytype'] || $_REQUEST['asattarrytype'] == -1) {
			$this->assign('attarrycreate', $attarrycreate);
		}
		//以天为单位分组反馈信息
		//1:需求附件,2:缺陷附件,(3:成果附件,6:完成附件,8:申请暂停附件,)9:缺陷解决附件
		$datefeedlist = array();
		foreach ($feedlist as $key => $val) {
			//筛选
			if ($_REQUEST['asattarrytype']) {
				if ($_REQUEST['asattarrytype'] != $val['statustype']) {
					continue;
				}
			}
			if ($val['statustype'] == 3) {
				$feedlist[$key]['exetype'] = '任务进行';
			}
			if ($val['statustype'] == 8) {
				$feedlist[$key]['exetype'] = '申请暂停';
			}
			if ($val['statustype'] == 6) {
				$feedlist[$key]['exetype'] = '完成操作';
			}
			//以当前为第几天为数组下标组织数组
			$indexdate = floor (($val['createtime'] - $begintime)) / 86400;
			$datefeedlist[$indexdate]['exe'][$val['id']] = $feedlist[$key];
			//插入相关附件
			$datefeedlist[$indexdate]['exe'][$val['id']]['attr'] = $attarraylist[$val['id']];
			$datefeedlist[$indexdate]['datetime'] = $begintime + ($indexdate * 86400);
			$datefeedlist[$indexdate]['count'] = count($datefeedlist[$indexdate]['exe']);
		}
		//循环组织每天的类容，若当天没有执行则为空
		if ($_REQUEST['asattarrytype'] != -1) {
			$days--;
			while ($days >= 0) {
				if (!$datefeedlist[$days]) {
					$datefeedlist[$days]['exe'] = null;
					$datefeedlist[$days]['datetime'] = $begintime + ($days * 86400);
				}
				$days --;
			}
		}
		//key值降序排序
		krsort($datefeedlist);
		$this->assign('datefeedlist', $datefeedlist);
    	$this->display('lookupPlanCondition');
    }
    
    /**
	 * @Title: lookupUpload
	 * @Description: todo(添加附件)
	 * @author jiangx
	 * @throws
	*/
    public function lookupUpload(){
        $model = D("MisTaskAdscription");
        $task = $model->where('status = 1 AND id = '. $_POST['id'])->find();
        $this->swf_upload($_POST['id'], 66, 3);
        $this->success("执行成功");
    }
    
    /**
	 * @Title: update
	 * @Description: todo(update)
	 * @author jiangx
	 * @throws
	*/
    public function update(){
        if (!$_REQUEST['id']) {
            $this->error ( L('该任务不存在，请刷新后重试') );exit;
        }
        $model = D("MisTaskAdscription");
        $task = $model->where('status = 1 AND id = '. $_REQUEST['id'])->find();
        $status = isset($_REQUEST['exestatus']) ? $_REQUEST['exestatus'] : 3;
        //如果该任务未执行状态，则修改状态、开始时间
        if ($task['executingstatus'] < 3) {
            $data = array('executingstatus' => $status, 'realitybegindate' => time());
            $result = $model->where('id = ' .$task['id'])->setField($data);
        }
        $modelview = D("MisTaskInformationView");
        $tasklist = $modelview->where('mis_task.status = 1 AND mis_task.pid= ' .$task['taskid'])->select();
        $list = array();
        $this->getChildTask(&$list, $tasklist);
        $idlist = array();
        switch ($status) {
            case 8:
                foreach ($list as $key => $val) {
                    $idlist['id'][] = $val['id'];
                    $idlist['sendid'][] = array('0' => $val['createid'], '1' => $val['executeuser']);
                }
                break;
            case 6:
                foreach ($list as $key => $val) {
                    if ($val['executingstatus'] != 6) {
                        $this->error ( L('该任务有子任务未结束') );
                    }
                }
                $_POST['chedule'] = 100.00;
                break;
        }
        $idlist['id'][] = $task['id'];
        $idlist['sendid'][] = $task['createid'];
        //修改任务状态
        $map = array();
        $map['id'] = array('in', $idlist['id']);
        //设置状态 添加结束时间
        $data = array('executingstatus' => $status, 'chedule' => $_POST['chedule']);
        if ($status > 3) {
            $data['realityenddate'] = time();
            $result = $model->where($map)->setField($data);
        } else{
            $result = $model->where($map)->setField($data);
        }
        if ($result === false) {
            $this->error ( L('_ERROR_') );
        }
        $feedbackInformationModel = D('MisTaskFeedbackInformation');
        $_POST['type'] = 1;
        $_POST['statustype'] = $status;
        unset($_POST['id']);
        if (false === $feedbackInformationModel->create ()) {
        	$this->error ( $feedbackInformationModel->getError () );
        }
        $feedresult = $feedbackInformationModel->add();
        if ($feedresult === false) {
        	$this->error ( L('_ERROR_') );
        }
		$this->swf_upload($_REQUEST['id'],66,$feedresult);
		if ($_POST['ispushmsg']) {
			foreach ($idlist['id'] as $key => $val) {
				if (is_array($idlist['sendid'][$key])) {
					//系统给新建人发信息
					$this->systemMessage($val, $idlist['sendid'][$key][0], $status);
					//系统给负责人发信息
					$this->systemMessage($val, $idlist['sendid'][$key][1], $status);
				}else{
					//系统给新建人发信息
					$this->systemMessage($val, $idlist['sendid'][$key], $status);
				}
			}
		}
        $isopfeedback = $status == 3 ? '执行' : ($status == 8 ? '申请暂停' : '完成');
        $this->insertTaskHistory($task['id'],'mis_task_feedback_information',$feedresult,$isopfeedback . '任务并发送反馈信息', $status);
        $this->success ( L('_SUCCESS_'));
    }
	
	/**
	 * @Title: getChildTaskView
	 * @Description: todo(查看子任务信息)
	 * @param $taskid 任务id
	 * @author jiangx
	 * @throws
	*/
    private function getChildTaskView($taskid){
        $model = D("MisTaskInformationView");
        $tasklist = $model->where('pid = ' . $taskid)->select();
        $list = array();
        $this->getChildTask(&$list, $tasklist, -1, 0);
		//组织子任务数据
		$childlist = array();
		//所有人员
		$alluserlist = array();
		while ($value = current($list)) {
            $childlist['response'][] = $value;
			$alluserlist[$value['createid']]['id'] = $value['createid'];
			$alluserlist[$value['trackuser']]['id'] = $value['trackuser'];
			$alluserlist[$value['executeuser']]['id'] = $value['executeuser'];
            next($list);	
        }
		$userviewmodel = D("MisHrPersonnelPersonInfoView");
		$map = array();
		foreach ($alluserlist as $key => $val) {
			$map['id'] = $val['id'];
			$result = $userviewmodel->where($map)->field('dutyname,name')->find();
			$alluserlist[$key]['dutyname'] = $result['dutyname'];
			$alluserlist[$key]['name'] = $result['name'];
		}
		$this->assign('alluserlist', $alluserlist);
		$this->assign('childlist', json_encode($childlist));
        $this->display("getChildTaskView");
    }
    /**
	 * @Title: getChildTask
	 * @Description: todo(获取子任务信息)
	 * @param $list 装载结果集数组
	 * @param $tasklist 父节点数组集
	 * @param $level 节点层级 默认为0级
	 * @param $pid 父节点id
	 * @date 2013-08-22
	 * @author jiangx
	 * @throws
	*/
    private function getChildTask($list, $tasklist, $level, $pid){
		$level++;
        $model = D("MisTaskInformationView");
        foreach ($tasklist as $key => $val) {
            $tasklist1 = $model->where('mis_task.status = 1 AND mis_task.pid= ' .$val['taskid'])->select();
			if ($tasklist1) {
				$val['isLeaf'] = false;
			} else {
				$val['isLeaf'] = true;
			}
			$val['executingstatus'] = excelTplselected($val['executingstatus'], '1:未查看,2:已查看,3:进行中,4:关闭,5:暂停,6:完成,7:结束,8:申请暂停,0:未启动');
			$val['executeusername'] = getFieldBy($val['executeuser'],'id','name','User');
			$val['chedule'] = '<span class="taskprogressbar"><span class="taskpspan1" style="width:'.$val['chedule'].'%;"></span><span class="taskpspan2">'.$val['chedule'].'%</span></span>';
            $val['difficulty'] = excelTplselected($val['difficulty'], '1:简单,2:一般,3:困难,4:极难');
			$val['urgentstatus'] = excelTplselected($val['urgentstatus'], '1:<span class="levelGreen">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>,2:<span class="levelOrange">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>,3:<span class="levelRed">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>');
			$val['toview'] = '<a class="iconView" mask="true" title="子任务查看" rel="mistaskadschildview_'.$val['taskid'].'" href="__URL__/edit/id/'.$val['id'].'" target="dialog" width="900" height="630"></a>';
			$val['parent'] = $pid;
            $val['loaded'] = true;
            $val['expanded'] = true;
            $val['level'] = $level;
			$val['id'] = $val['taskid'];
			$list[$val['id']] = $val;
            $this->getChildTask(&$list, $tasklist1, $level, $val['taskid']);
        }
    }
    /**
	 * @Title: systemMessage
	 * @Description: todo(系统发送消息)
	 * @return array
	 * @param $infoid 任务id
	 * @param $userid 通知的人id
	 * @param $type 类型 3任务开始通知  8 任务申请暂停通知 6 任务完成通知
	 * @author jiangx
	 * @throws
	*/
    private function systemMessage($infoid, $userid, $type = 3){
        $model = D("MisTaskInformationView");
        $task = $model->where('mis_task_information.id = '. $infoid)->find();
        if (!$task) {
            return '';
        }
        //接收人
        $recipientname = getFieldBy($userid,'id','name','User');
        //消息发送人
        $sendusername = getFieldBy($_SESSION[C('USER_AUTH_KEY')],'id','name','User');
        $oplist = array('3' => '执行','8' => '申请暂停','6' => '完成','11' => '转派');
        $opname = $oplist[$type];
        $messageTitle = '任务' . $opname . '通知';
		$this->assign('recipientname', $recipientname);
		$this->assign('sendusername', $sendusername);
		$this->assign('opname', $opname);
		$this->assign('task', $task);
		$this->assign('type', $type);
		$this->assign('typeopname', $type == 0 ? $opname.'审核' : '知晓');
		$this->assign('task', $task);
		$messageContent = $this->fetch("msgcontent");
        //系统推送消息
         $this->pushMessage(array($userid), $messageTitle, $messageContent, true, 1, $infoid);
    }
    /**
     * @Title: optionTask
     * @Description: todo(审核   申请暂停  关闭页面的交流反馈信息)
     * @author xiafengqin
     * @date 2013-6-24 下午3:45:12
     * //3执行5申请暂停6结束
     * @throws
     */
    public function optionTask(){
	     switch ($_REQUEST['exetatus']){
            case 3:
                $exename = '执行';
                break;
	    	case 8:
	    		$exename = '申请暂停';
	    		break;
	    	case 6:
	    		$exename = '完成';
	    		break;
	    }
	    $this->assign('exename',$exename);
	    $this->assign('exestatus',$_REQUEST['exetatus']);
	    $model = D("MisTask");
	    $task = $model->where('id = ' . $_REQUEST['taskid'])->find();
	    if ($task) {
	    	$pidtask = $model->where('id = ' . $task['pid'])->find();
	    }
	    $this->assign('task',$task);
	    $this->assign('pidtask',$pidtask);
	   	$model = D("MisTaskAdscription");
	   	$info = $model->where('taskid = ' . $task['id'])->find();
	   	$this->assign('vo', $info);
	    $this->display();
    }
    /**
     * @Title: lookupIdea 
     * @Description: todo(我的任务面板的跟踪人意见)   
     * @author xiafengqin 
     * @date 2013-6-29 下午2:14:48 
     * @throws
     */
    public function lookupIdea($taskid){
    	$InformationModel=D("MisTaskInformation");
    	$trackuser = $InformationModel->where('taskid='.$taskid)->getField('trackuser');
    	$this->assign('trackuser',$trackuser);
    	$feedbackModel = D('MisTaskFeedbackInformation');
    	//缺陷
    	$voList = $feedbackModel->where('isdefect = 1 AND pid = 0 AND type = 3 AND taskid='.$taskid )->select();
    	foreach ($voList as $key => $val) {
    	    $voList[$key]['remark'] = missubstr($val['remark'], 180);
    	}
    	//审核
    	$trace = $feedbackModel->where('isdefect = 0 AND pid = 0 AND type = 3 AND taskid='.$taskid )->select();
    	foreach ($trace as $key => $val) {
    		$trace[$key]['remark'] = missubstr($val['remark'], 180);
    	}
    	$this->assign('trace',$trace);
    	$this->assign('voList',$voList);
    	$this->display('lookupIdea');
    }
    /**
     * @Title: lookupsolve 
     * @Description: todo(缺陷操作)   
     * @author jiangx 
     * @date 2013-7-25 下午5:24:48 
     * @throws
     */
    public function lookupsolve(){
		$taskid = $_REQUEST['taskid'];
		$infoModel = D("MisTaskAdscription");
		$taskinfo = $infoModel->where('taskid = ' . $taskid)->find();
		$this->assign('taskinfo', $taskinfo);
		$feedbackModel = D("MisTaskFeedbackInformation");
		if ($_POST['remark']) {
			//插入缺陷解决
			$feedback = $feedbackModel->where('id = '. $_REQUEST['pid'])->find();
			$_POST['taskid'] = $feedback['taskid'];
			$_POST['type'] = 3;
			$_POST['isdefect'] = 1;
			if (false === $feedbackModel->create ()) {
				$this->error ( $feedbackModel->getError () );
			}
			$list = $feedbackModel->add ();
			if ($list == false) {
				$this->error ( L('_ERROR_') );
			}
			$this->swf_upload($list, 72);
			$feedbackModel->where('id = '. $_REQUEST['pid'])->setField('issolve',1);
			$this->success ( L('_SUCCESS_') ,'',$list);
		} else {
			//进入缺陷页面
			$feedback = $feedbackModel->where('id = '. $_REQUEST['id'])->find();
			$this->assign('feedback', $feedback);
			$feedback['child'] = $feedbackModel->where('pid = ' .$_REQUEST['id'])->find();
			$this->assign('feedback', $feedback);
			//获取附件
			$map1["status"]  =1;
			if ($feedback['child']) {
				$idarr[] = $_REQUEST['id'];
				$idarr[] = $feedback['child']['id'];
				$map1["orderid"] = array('in', $idarr);
			} else {
				$map1["orderid"] = $_REQUEST['id'];
			}
			$map1["type"] =72;
			$m=M("mis_attached_record");
			$attarry=$m->where($map1)->select();
			foreach ($attarry as $key => $val) {
				if ($val['orderid'] == $_REQUEST['id']) {
					$feedbackatt[] = $val;
				}
				if ($val['orderid'] == $feedback['child']['id']) {
					$childfeedbackatt[] = $val;
				}
			}
			$this->assign('feedbackatt', $feedbackatt);
			$this->assign('childfeedbackatt', $childfeedbackatt);
			if ($feedback['issolve'] == 1) {
				$this->display("solveView");
				exit;
			}
			$this->display();
		}
    }
    /**
     * @Title: lookupGetdate 
     * @Description: todo(获得存在数据库中的任务名，显示在日历上)   
     * @author xiafengqin 
     * @date 2013-7-23 下午1:48:00 
     * @throws
     */
    public function lookupGetdate(){
		$executelist = array();//执行的任务
		$tracklist = array();//跟踪的任务
		$viewModel = D("MisTaskInformationView");
		$map['status'] = 1;
		//判断是否有执行权限
		if ($_SESSION['a'] || $_SESSION['mistaskadscription_edit']) {
			$map['_string'] = 'mis_task_information.executeuser = '.$_SESSION[C('USER_AUTH_KEY')];
			$executelist = $viewModel->where($map)->select();
		}
		if ($_SESSION['a'] || $_SESSION['mistaskinformation_trace']) {
			$map['_string'] = 'mis_task_information.trackuser = '.$_SESSION[C('USER_AUTH_KEY')];
			$tracklist = $viewModel->where($map)->select();
		}
		$arr = array();
		foreach ($executelist as $key => $value) {
			$datas['id'] = $value['id'];
			$datas['taskid'] = $value['taskid'];
			$datas['title'] = missubstr($value['title'],20,true);
			$datas['start'] = transTime($value['begindate'],'Y-m-d H:i:s');
			$datas['end'] = transTime($value['enddate'],'Y-m-d H:i:s');
			$datas['urgentstatus'] = $value['urgentstatus'];
			$datas['className'] = $value['urgentstatus'] == 1 ? 'myblue' : ($value['urgentstatus'] == 2 ? 'myorange' : 'myred');//这里可以设置此action的样式
			$datas['type'] = 'execute';
			$arr[] = $datas;
		}
		foreach ($tracklist as $key => $value) {
			$datas['id'] = $value['id'];
			$datas['taskid'] = $value['taskid'];
			$datas['title'] = missubstr($value['title'],20,true);
			$datas['start'] = transTime($value['begindate'],'Y-m-d H:i:s');
			$datas['end'] = transTime($value['enddate'],'Y-m-d H:i:s');
			$datas['urgentstatus'] = $value['urgentstatus'];
			$datas['className'] = $value['urgentstatus'] == 1 ? 'myblue' : ($value['urgentstatus'] == 2 ? 'myorange' : 'myred');//这里可以设置此action的样式
			$datas['type'] = 'track';
			$arr[] = $datas;
		}
		echo json_encode($arr);
    }
    /**
     * @Title: lookupSetAttention
     * @Description: todo(加关注)
     * @author jiangx
     * @date 2013-7-24 下午4:15:10
     * @throws
     */
    public function lookupSetAttention(){
    	if (!$_POST['modelname']) {
    		exit('-1');
    	}
    	$map['tableid'] = $_POST['tableid'];
    	$map['modelname'] = $_POST['modelname'];
	$map['type'] = $_POST['type'];
    	$AttentModel = D("MisTaskAttention");
    	$result = $AttentModel->where($map)->find();
    	if (!$result) {
    		//添加关注
    		$_POST['status'] = 1;
    		C ( "TOKEN_ON", false );
    		if (false === $AttentModel->create ()) {
    			$this->error ( $AttentModel->getError () );
    		}
    		//保存表头信息
    		$list = $AttentModel->add ();
    		$this->transaction_model->commit();//事务提交
    		C ( "TOKEN_ON", true );
    		if ($list === false) {
    			exit('-1');
    		}
    		exit('1');
    	} else {
    		//修改关注
    		$status = $result['status'] == 1 ? 0 : 1;
    		$AttentModel->where($map)->setField('status', $status);
    		$this->transaction_model->commit();//事务提交
    		echo $status;
    		eixt;
    	}
    }
    /**
     * @Title: relevques
     * @Description: todo(获取提问列表)
     * @param $taskid mis_task.id
     * @author xiafengqin
     * @date 2013-7-24 下午4:15:10
     * @throws
     */
	private function relevques($taskid){
		$questionviewModel = D("MisExpertquestionList");
		$map = array();
		$map['sourcetype'] = 1;
		$map['sourceid'] = $taskid;
		$map['status'] = 1;
		$map['type'] = 'Q';
		$questionlist = $questionviewModel->where($map)->select();
		foreach ($questionlist as $key => $val) {
			if ($val['expertype'] == 0) {
				$questionlist[$key]['expertid'] = getFieldBy($val['expertid'],'id','userid','MisExpertList');
			}
			if ($val['selchildid']) {
				$questionlist[$key]['isanswer'] = 1;
			} else {
				$questionlist[$key]['isanswer'] = 0;
			}
		}
		$this->assign('questionlist', $questionlist);
    	$this->display('relevques');
    }
	/**
     * @Title: lookupsettrackingstate 
     * @Description: todo(跟踪周期) 
     * @author jiangx 
     * @date 2013-7-23 下午1:50:10 
     * @throws
     */
	public function lookupsettrackingstate(){
		$model = D("MisTaskTrackingstate");
		if ($_REQUEST['trackstatus']) {
			if ($_POST['date'] == 'day') {
				$_POST['cycle'] = $_POST['cycle'] * 24;
			}
			if (false === $model->create ()) {
				$this->error ( $model->getError () );
			}
			if ($_REQUEST['id']) {
				$list=$model->save ();
				if ($list == false) {
					$this->error ( L('_ERROR_') );
				}
			} else {
				//保存当前数据对象
				$list=$model->add ();
				if ($list == false) {
					$this->error ( L('_ERROR_') );
				}
			} 
			$this->success ( L('_SUCCESS_') ,'',$list);
		} else {
			if (!$_REQUEST['type']) {
				$_REQUEST['type'] = 1;
			}
			$this->assign('type', $_REQUEST['type']);
			$viewModel = D('MisTaskInformationView');
			$taskview = $viewModel->where('taskid = ' . $_REQUEST['taskid'])->find();
			$this->assign('taskview', $taskview);
			$trackstate = $model->where('taskid = ' .$_REQUEST['taskid'])->find();
			$this->assign('trackstate', $trackstate);
			$this->display();
		}
	}
	/**
     * @Title: getTimeLine
     * @Description: todo(获取时间轴)
     * @author jiangx
     * @date 2013-08-6
     * @throws
     */
	public function getTimeLine(){
		$taskModel = D("MisTask");
		$task = $taskModel->where('id = ' .$_REQUEST['taskid'])->find();
		$this->assign('task', $task);
		$historyModel = D("MisTaskHistory");
		$map = array();
		$map['taskid'] = $_REQUEST['taskid'];
		
		$history = $historyModel->where($map)->select();
		foreach ($history  as $key => $val) {
			$timediffer = $val['createtime'] - $history[0]['createtime'];
			$day = floor($timediffer / (60 * 60 * 24));
			$hour = floor(($timediffer % (60 * 60 * 24)) / (60 * 60));
			$minute = floor((($timediffer % (60 * 60 * 24)) % (60 * 60)) /60);
			$history[$key]['timediffer'] = '';
			if ($minute) {
				$history[$key]['timediffer'] = $minute. '分';
			}
			if ($hour) {
				$history[$key]['timediffer'] = $hour. '时'. $history[$key]['timediffer'];
			}
			if ($day) {
				$history[$key]['timediffer'] = $day. '天'. $history[$key]['timediffer'];
			}
			//获取反馈意见
			if ($val['tablename'] == 'mis_task_feedback_information') {
				$model = M($val['tablename']);
				$history[$key]['feedback'] = $model->where('id = ' . $val['tableid'])->getField('remark');
			}
		}
		$this->assign('history', $history);
		$this->display();
	}
	/**
     * @Title: lookupDeleteComment
     * @Description: todo(删除任务评论--假删)
     * @author jiangx
     * @date 2013-08-21
     * @throws
     */
	public function lookupDeleteComment(){
		foreach ($_POST['data'] as $key => $val) {
			$_POST[$key] = $val;
		}
		if (!$_POST['taskid'] || !$_POST['id']) {
			$this->error ( L('请刷新后再操作') );
		}
		$feedbackmodel = D("MisTaskFeedbackInformation");
		$map = array();
		$map['taskid'] = $_POST['taskid'];
		$map['_string'] = 'id= '. $_POST['id'] .' OR pid = '.$_POST['id'];
		$result = $feedbackmodel->where($map)->setField('status', 0);
		$this->transaction_model->commit();//事务提交
	}
}
?>