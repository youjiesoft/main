<?php
/**
 * @Title: MisTaskAction
 * @Package 任务管理-任务管理动态：功能类
 * @Description: TODO(任务管理-任务管理动态)
 * @author jiangx
 * @company 重庆特米洛科技有限公司˾
 * @copyright 重庆特米洛科技有限公司˾
 * @date 2013-6-06
 * @version V1.0
 */
class MisTaskInformationAction extends CommonAction{
    /**
	 * @Title: _filter
	 * @Description: todo(列表数据展示过滤器)
	 * @param unknown_type $map
	 * @throws
	*/
    public function _filter(&$map){
        if ($_SESSION["a"] != 1) {
            $map['createid'] = $_SESSION[C('USER_AUTH_KEY')] ? $_SESSION[C('USER_AUTH_KEY')]:0;
        } else {
		    if (!$_REQUEST['type']) {
				$map['pid'] =0;
		    }
        }
    }
    /**
	 * @Title: _before_add
	 * @Description: todo(add页面打开前传递展示信息)
	 * @return string
	 * @author 
	 * @throws
	*/
    public function _before_add(){
		//exeadd 存在则表示为执行人员添加子任务
		$this->assign('exeadd', $_REQUEST['exeadd']);
        $this->assign('nowtime',time());
        //加载父任务信息---仅加载一级
        if ($_REQUEST['pid']) {
            $viewmodel = D("MisTaskInformationView");
            $pidtask = $viewmodel->where('mis_task_information.taskid = ' . $_REQUEST['pid'])->find();
			if ($pidtask['executingstatus'] == 7) {
				$this->error ( L('该任务已结束，不能添加子任务') );
				exit;
			}
            $this->assign('pidtask',$pidtask);
        }
        $urgentstatuslist = array(
	    '1' => '宽松','2' => '一般','3' => '紧急'
	);
        $this->assign('urgentstatuslist', $urgentstatuslist);
        $difficultylist = array(
	    '1' => '简单','2' => '一般','3' => '困难','4' => '极难'
	);
        $this->assign('difficultylist', $difficultylist);
    }
    /**
	 * @Title: _before_insert
	 * @Description: todo()
	 * @return string
	 * @author 
	 * @throws
	*/
    public function _before_insert(){
		//判断是否能添加子任务
		if ($_POST['pid']) {
            $viewmodel = D("MisTaskInformationView");
            $pidtask = $viewmodel->where('mis_task_information.taskid = ' . $_POST['pid'])->find();
			if ($pidtask['executingstatus'] == 7) {
				$this->error ( L('该任务已结束，不能添加子任务') );
				exit;
			}
        }
        $model = D("MisTask");
		if (!$_POST['trackuser']) {
			$_POST['trackuser'] = $_SESSION[C('USER_AUTH_KEY')];
		}
        if (false === $model->create ()) {
            $this->error ( $model->getError () );
        }
        //保存表头信息
        $_POST['taskid'] = $list = $model->add ();
        if ($list === false) {
            $this->error ( L('_ERROR_') );
        }
    }
    /**
	 * @Title: _after_insert
	 * @Description: todo()
	 * @return string
	 * @author 
	 * @throws
	*/
    public function _after_insert($id){
		if ($_POST['ispushmsg']) {
			//系统推送负责人消息
			$this->systemMessage($id, $_POST['executeuser'], 1);
			//系统推送跟踪人消息
			$this->systemMessage($id, $_POST['trackuser'], 0);
		}
        $idlist['infoid'][] = $id;
        $idlist['taskid'][] = $_POST['taskid'];
        $this->swf_upload($id,66);
        $model = D("MisTaskInformation");
        $_POST['pid'] = $model->where(' id = ' . $id)->getField('taskid');
        //添加历史
        foreach ($idlist['infoid'] as $key => $val) {
            $this->insertTaskHistory($idlist['taskid'][$key], 'mis_task_information', $val, '添加新任务', 1);
        }
        //添加跟踪设置
        $trackingstateModel = D("MisTaskTrackingstate");
        $_POST['type'] = 3;
        if (false === $trackingstateModel->create ()) {
        	$this->error ( $trackingstateModel->getError () );
        }
        $list = $trackingstateModel->add ();
        if ($list === false) {
        	$this->error ( L('_ERROR_') );
        }
    }
    /**
	 * @Title: traceindex
	 * @Description: todo(跟踪人任务list)
	 * @return string
	 * @author 
	 * @throws
	*/
    public function traceindex(){
    	$map = array();
    	//跟踪的
    	$map['status'] = 1;
    	$map['trackuser'] = $_SESSION[C('USER_AUTH_KEY')];
    	$misTaskTrackingstateModel = M('mis_task_trackingstate');
    	$volist = $misTaskTrackingstateModel->where($map)->select();
    	$tracktypearr = array();
    	$mapid = array();
    	foreach ($volist as $key => $val) {
    		$tracktypearr[$val['type']][] = $val['taskid'];
    		$mapid[] = $val['taskid'];
    	}
    	//查询出所有跟踪任务列表
    	$viewModel = D("MisTaskInformationView");
    	$map = array();
    	$map['trackuser'] = $_SESSION[C('USER_AUTH_KEY')];
    	$map['status'] = 1;
    	$map['taskid'] = array('in', $mapid);
    	$tasklist = $viewModel->where($map)->order('id DESC')->select();
    	//主动跟踪
    	$initiativetasklist = array();
    	//申请跟踪
    	$applytasklist = array();
    	//完成跟踪
    	$overtasklist = array(); 
    	foreach ($tasklist as $key => $val) {
    		if(in_array($val['taskid'], $tracktypearr[1])){
    			$initiativetasklist[] = $val;
    		}
    		if(in_array($val['taskid'], $tracktypearr[2])){
    			$applytasklist[] = $val;
    		}
    		if(in_array($val['taskid'], $tracktypearr[3])){
    			$overtasklist[] = $val;
    		}
    	}
    	$this->assign('initiativetasklist', $initiativetasklist);
    	$this->assign('applytasklist', $applytasklist);
    	$this->assign('overtasklist', $overtasklist);
		$this->display ();
    }
    
	/**
	 * @Title: taskboard
	 * @Description: todo(任务看板)
	 * @return string
	 * @author 
	 * @throws
	*/
	public function index(){
		$viewModel = D('MisTaskInformationView');
		$map = array();
		$map['status'] = 1;
		$map['executingstatus'] = array('not in', '4,7');
		//获取发布的任务
		$map['createid'] = $_SESSION[C('USER_AUTH_KEY')];
		$mistaskinfo = $viewModel->where($map)->field('id,taskid,title,enddate,executingstatus,chedule,urgentstatus,difficulty,executingstatus')->order('enddate')->select();
		$this->assign('mistaskinfo', $mistaskinfo);
		$map['trackuser'] = $_SESSION[C('USER_AUTH_KEY')];
		unset($map['createid']);
		//获取跟踪的任务
		$map['executingstatus'] = array(' not in','4,5,7');
		$mistasktrack = $viewModel->where($map)->field('id,taskid,title,createtime,createid,executingstatus,urgentstatus,difficulty,chedule')->order('enddate')->select();
		$this->assign('mistasktrack', $mistasktrack);
		//状态0未启动1未查看2已查看3进心中4关闭5暂停6完成7结束8申请暂停
		//获取执行的任务
		unset($map['trackuser']);
		$map['executeuser'] = $_SESSION[C('USER_AUTH_KEY')];
		$map['executingstatus'] = array(' not in','4,5,6,7,8');
		$mistaskexecute = $viewModel->where($map)->field('id,title,createtime,createid,executingstatus,urgentstatus,difficulty,chedule')->order('enddate')->select();
		$mistaskexecuteidarr = $viewModel->where($map)->getField('taskid', true);
		$this->assign('mistaskexecute', $mistaskexecute);
		//获取缺陷反馈
		$feedbackModel = D("MisTaskFeedbackInformation");
		$map = array();
		$map['type'] = 3;
		$map['isdefect'] = 1;
		$map['issolve'] = 0;
		$map['status'] = 1;
		$map['pid'] = 0;
		$map['taskid'] = array('in', $mistaskexecuteidarr);
		$feedbacklist = $feedbackModel->where($map)->field('id,remark,taskid,createid,createtime')->order('id DESC')->select();
		foreach ($feedbacklist as $key => $val) {
			$feedbacklist[$key]['remark'] = missubstr($val['remark'], 40);
		}
		$this->assign('feedbacklist', $feedbacklist);
		/*<--问题-->*///获取待办问题
		$questionModel = D("MisExpertquestionList");
		$map = array();
		$map['sourcetype'] = 1;
		$map['status'] = 1;
		$map['expertid'] = $_SESSION[C('USER_AUTH_KEY')];
		$map['selchildid'] = 0;
		$todoquestionlist = $questionModel->where($map)->limit(8)->order('id DESC')->select();
		$this->assign('todoquestionlist', $todoquestionlist);
		//任务人员转交
		$map = array();
		$map['isreplaprincipal'] = 1;
		$map['status'] = 1;
		$taskidary = $feedbackModel->where($map)->Distinct(true)->getField('taskid',true);
		$map = array();
		$map['status'] = 1;
		$map['createid'] = $_SESSION[C('USER_AUTH_KEY')];
		$map['taskid'] = array('in',$taskidary);
		$result = $viewModel->where($map)->select();
		
		$this->assign('result',$result);
		//任务消息
		$msgUserModel = D('MisMessageUser');
		$map = array();
		$map['status'] = 1;
		$map['contentType'] = 1;
		$map['readedStatus'] = 0;
		$map['recipient'] = $_SESSION[C('USER_AUTH_KEY')];
		$msglist = $msgUserModel->where($map)->field('id,sendid,relevanceid,readedStatus,createtime')->order('id DESC')->select();
		$this->assign('msglist', $msglist);
		//获取关注信息
		$attentionModel = D("MisTaskAttention");
		$map = array();
		$map['createid'] = $_SESSION[C('USER_AUTH_KEY')];
		$map['status'] = 1;
		$map['type'] = array('in','1,2,3');
		$attentionidarr = $attentionModel->where($map)->getField('tableid,type');
		$taskidarr = array();
		foreach ($attentionidarr as $key => $val) {
			$taskidarr[] = $key;
		}
		$attarr = array();
		$map = array();
		$map['id'] = array('in', $taskidarr);
		$attenlist = $viewModel->where($map)->select();
		foreach ($attenlist as $key => $val){
			$attenlist[$key]['type'] = $attentionidarr[$val['id']];
		}
		$this->assign('attenlist',$attenlist);
		$this->display();
	}
	/**
	 * @Title: indexview
	 * @Description: todo(打开列表函数)
	 * @return string
	 * @author 
	 * @throws
	*/
	public function indexview(){
	    $map = $this->_search ();
	    if (method_exists ( $this, '_filter' )) {
		$this->_filter ( $map );
	    }
	    $name = "MisTaskInformation";
	    //验证浏览及权限
        if( !isset($_SESSION['a']) ){
            if( $_SESSION[strtolower($name.'_'.ACTION_NAME)]==2 ){////判断部门及子部门权限
                $map["mis_task_information.createid"]=array("in",$_SESSION['user_dep_all_child']);
            }else if($_SESSION[strtolower($name.'_'.ACTION_NAME)]==3){//判断部门权限
                $map["mis_task_information.createid"]=array("in",$_SESSION['user_dep_all_self']);
            }else if($_SESSION[strtolower($name.'_'.ACTION_NAME)]==4){//判断个人权限
                $map["mis_task_information.createid"]=$_SESSION[C('USER_AUTH_KEY')];
            }
        }
        $model = D("MisTaskInformationView");
        $map['status'] =1;
        if ( $_SESSION["a"] != 1 ) {
            $list = $model->where($map)->select();
            $taskid_array = array();//任务taskid 数组
            $taskpid_array = array();//任务父节点数组
            foreach ($list as $val) {
            $taskid_array[$val['taskid']] = $val['taskid'];
            $taskpid_array[$val['pid']] = $val['pid'];
            }
            //获取全部最顶节点的父节点
            $x= array_diff($taskpid_array,$taskid_array);
            if ($x) {
            	$map['pid'] = array('in',$x);
            }
        }
        $count = $model->where($map)->count ( '*' );
        if ($count > 0) {
		    import ( "@.ORG.Page" );
		    //创建分页对象
		    $numPerPage=C('PAGE_LISTROWS');
		    $dwznumPerPage=C('PAGE_DWZLISTROWS');
		    if (! empty ( $_REQUEST ['numPerPage'] )) {
			    $numPerPage = $_REQUEST ['numPerPage'];
		    }
		    if( $_POST["dwzpageNum"]=="") $dwznumPerPage = $numPerPage;
	
		    $p = new Page ( $count, $numPerPage,'',$dwznumPerPage );
		    //分页查询数据
		    if($_POST['dwzloadhtml']) $p->firstRow = $p->firstRow + (intval($_POST['dwzpageNum'])-1)*$numPerPage;
		    $voList = $model->where($map)->order( "mis_task_information.id DESC" )->limit($p->firstRow . ',' . $p->listRows)->select();
		    $this->getParentTaskTree($model, $map, $tasklist, 'mis_task_information.id DESC', $p->firstRow, $p->listRows, 'DESC');
		    foreach ( $map as $key => $val ) {
			if (! is_array ( $val )) {
			    $p->parameter .= "$key=" . urlencode ( $val ) . "&";
			}
		    }
		    $page = $p->show ();
		    //列表排序显示
		    $sortImg = $sort; //排序图标
		    $sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
		    $sort = $sort == 'desc' ? 'desc' : 'asc'; //排序方式
		    $pageNum= !empty($_REQUEST[C('VAR_PAGE')])?$_REQUEST[C('VAR_PAGE')]:1;
		    //模板赋值显示
		    $this->assign ( 'pageNum', $pageNum );
		    $this->assign ( "page", $page );
		}
		$this->assign ( 'dwztotalpage',C('PAGE_DWZLISTROWS')/$numPerPage );
		$this->assign ( 'sort', $sort );
		$this->assign ( 'order', $order );
		$this->assign ( 'sortImg', $sortImg );
		$this->assign ( 'sortType', $sortAlt );
		$this->assign ( 'totalCount', $count );
		$this->assign ( 'numPerPage', $numPerPage);
		$this->assign ( 'dwznumPerPage', C('PAGE_DWZLISTROWS'));
		$this->assign ( 'currentPage', !empty($_REQUEST[C('VAR_PAGE')])?$_REQUEST[C('VAR_PAGE')]:1);
		Cookie::set ( '_currentUrl_', __SELF__ );
        $list = array();
        while ($value = current($tasklist)) {
            $list['response'][] = $value;
            next($tasklist);	
        }
        $this->assign('tasklist',json_encode($list));
		$this->display ();
	}
    /**
	 * @Title: edit
	 * @Description: todo(edit)
	 * @return string
	 * @author 
	 * @throws
	*/
    public function edit(){
        //获取任务头信息
        $model = D('MisTask');
        $task = $model->where('id=' . $_REQUEST ['id'])->find();
        $this->assign('task',$task);
		$model = D ( "MisTaskInformation" );
		$map['taskid']=$task['id'];
		if ($_SESSION["a"] != 1) $map['status']=1;
		$vo = $model->where($map)->find();
		if(empty($vo)){
			$this->display ("Public:404");
			exit;
		}
        $scdmodel = D('SystemConfigDetail');
		$modelname = $this->getActionName();
		$detailList = $scdmodel->getDetail($modelname,false);
		if ($detailList) {
			$fieldsarr = array();
			$sclmodel = D('SystemConfigList');
			foreach ($detailList as $k => $v) {
				$showname = '';
			if($v['status'] != -1){
					$showMethods = "";
					if($v['methods']){
						$methods = explode(';', $v['methods']);// 分解所有方法
						$normArray = $sclmodel->getNormArray();// 中文解析
						$showMethods .= "<span class='xyminitooltip'><span class='xyminitooltip_con'>";
						//$showname .= "<span class='xyminitooltip'><span class='xyminitooltip_con'>";
						$isfalse = false;
						foreach ($methods as $key => $vol) {
							if ($isfalse) {
								$showMethods .= " | ";
							}
							$volarr = explode(',', $vol);// 分解target和方法
							$target = $volarr[0];// 弹出方式
							$method = $volarr[1];// 方法名称
							$modelarr = explode('/', $method);// 分解方法0：model；1：方法
							if ($_SESSION[strtolower($modelarr[0].'_'.$modelarr[1])] || $_SESSION["a"]) {
								$showMethods .= "<a rel='".$modelarr[0].$modelarr[1]."' target='".$target."' href='__APP__/".$method."' mask='true'>".$normArray[$modelarr[1]]."</a>";
								$isfalse = true;
							}
							$isfalse = true;
						}
						if($showMethods){
							$showMethods .= "<span class='xyminitooltip_arrow_outer'></span><span class='xyminitooltip_arrow'></span></span></span>";
						}
					}
					if($showMethods){
						$showname .= $showMethods;
					}
					if ($v['models']) {
						if ($_SESSION[strtolower($v['models'].'_index')] || $_SESSION["a"]) {
							$showname .= "<a rel='".$v['models']."' target='navTab' href='__APP__/".$v['models']."/index'>".$v['showname']."</a>";
						} else {
							$showname .= $v['showname'];
						}
					} else{
						$showname .= $v['showname'];
					}
				}
				$fieldsarr[$v['name']] = $showname;
			}
			$this->assign ( 'fields', $fieldsarr );
		}
        if (method_exists($this,"_after_edit")) {
			call_user_func(array($this,"_after_edit"),$vo);
		}
		$this->assign( 'vo', $vo );
        //获取当前节点所有子任务信息
        $model = D("MisTaskInformationView");
        $map=array();
        $map['pid'] = $task['id'];
        $map['status'] = 1;
        $tasktree = array();
        $this->getParentTaskTree($model,$map,$tasktree);
        $list = array();
        if ($tasktree) {
            while ($value = current($tasktree)) {
                $list['response'][] = $value;
                next($tasktree);	
            }
        }
        if (($vo['executingstatus'] >= 6 || $vo['executingstatus'] == 4) && $vo['executingstatus']!= 8) {
            if ($list) {
                $this->assign('ishavechildtask',1);
            } else {
                $this->assign('ishavechildtask',0);
            }
            $this->assign('childtask',json_encode($list));
            $this->display('stakView');
            exit;
        }else{
            $this->assign('childtask',$list['response']);
        }
        $this->assign('isreplaprincipal',$_REQUEST['isreplaprincipal']);
		$this->display ();
    }
    /**
	 * @Title: _after_edit
	 * @Description: todo(_after_edit)
	 * @return string
	 * @author 
	 * @throws
	*/
    public function _after_edit($vo){
        //获取附件信息
        $map["status"]  =1;
		$map["orderid"] =$_REQUEST["id"];
        $map["subid"] =0;
		$map["type"] =66;
		$m=M("mis_attached_record");
		$attarry=$m->where($map)->select();
		$this->assign('attarry',$attarry);
        $urgentstatuslist = array(
			'1' => '宽松','2' => '一般','3' => '紧急'
		);
        $this->assign('urgentstatuslist', $urgentstatuslist);
        $difficultylist = array(
			'1' => '简单','2' => '一般','3' => '困难','4' => '极难'
		);
        $this->assign('difficultylist', $difficultylist);
		//查看关注
		$attentModel = D("MisTaskAttention");
		$map = array();
		$map['tableid'] = $vo['id'];
		$map['modelname'] = "MisTaskInformation";
		$map['type'] = 1;
		$attent = $attentModel->where($map)->find();
		$this->assign('attent', $attent);
    }
    
    /**
	 * @Title: auditTask
	 * @Description: todo(跟踪审核)
	 * @return string
	 * @author jiangx
	 * @throws
	*/
    public function auditTask(){
        $id = $_POST['id'];
        unset($_POST['id']);
        if ($_REQUEST['taskid']) {
            $_POST['type'] = 3;
            $model = D("MisTaskInformation");
            $feedbackmodel = D("MisTaskFeedbackInformation");
            switch ($_POST['executingstatus']) {
                case '0':
                    //继续
                    $infostatus = 3;
                    $_POST['statustype'] = 3;
					$exestatus = 12;
                    $todo = '继续';
                    break;
                case '3':
                    //修改即任务不合格，重新来过
                    $infostatus = 3;
                    $_POST['statustype'] = 3;
                    $todo = '继续';
					$exestatus = 12;
                    break;
                case '4':
                    //关闭
                    $infostatus = 4;
                    $_POST['statustype'] = 4;
                    $todo = '关闭';
					$exestatus = 4;
                    break;
                case '5':
                    //暂停
                    $infostatus = 5;
                    $_POST['statustype'] = 5;
                    $todo = '暂停';
					$exestatus = 5;
                    break;
                case '7':
                    //结束
                $infostatus = 7;
                $_POST['statustype'] = 7;
                $todo = '结束';
				$exestatus = 7;
                break;
            }
            if (false === $feedbackmodel->create ()) {
                $this->error ( L('_ERROR_') );
            }
            $feedresult = $feedbackmodel->add();
            if ($feedresult === false) {
                $this->error ( L('_ERROR_') );
            }
			$setdata = array('executingstatus' => $infostatus, 'updatetime' => time());
            $result = $model->where('id = ' . $id)->setField($setdata);
            if ($result) {
				
                //添加历史记录
                $this->insertTaskHistory($_REQUEST['taskid'], 'mis_task_feedback_information',$feedresult, $todo.'任务', $exestatus);
                if ($_POST['ispushmsg']) {
					//系统推送消息
					$info = $model->where('taskid = ' . $_POST['taskid'])->find();
					$this->systemMessage($info['id'], $info['executeuser'], $_POST['statustype']);
				}
                $this->success ( L('_SUCCESS_'));
            }
        }
    }
    /**
     * @Title: _before_update 
     * @Description: todo(修改执行人)   
     * @author xiafengqin 
     * @date 2013-7-31 上午9:52:53 
     * @throws
     */
    public function _before_update(){
    	$id = $_POST['id'];
    	$model = D("MisTaskInformation");
    	$oldexecuteuser = $model->where('id='.$id)->select();
    	if( !$oldexecuteuser[0]['oldexecuteusers']) {
    		$_POST['oldexecuteusers'] = $oldexecuteuser[0]['executeuser'];
    	} else {
    		$_POST['oldexecuteusers'] = $oldexecuteuser[0]['oldexecuteusers'] . ',' . $oldexecuteuser[0]['executeuser'];
    	}
    	//把换负责人的状态改了，表示 已经更换过负责人
    	$feedbackmodel = D("MisTaskFeedbackInformation");
    	$result = $feedbackmodel->where('taskid='.$oldexecuteuser[0]['taskid'])->setField('isreplaprincipal',0);
    	
    }
    /**
	 * @Title: _after_update
	 * @Description: todo(_after_update)
	 * @author 
	 * @throws
	*/
    public function _after_update(){
        $id = $_POST['id'];
        $_POST['id'] = $_POST['taskid'];
        $model = D("MisTask");
        if (false === $model->create ()) {
            $this->error ( $model->getError () );
        }
        //保存表头信息
        $list = $model->save ();
        if ($list === false) {
            $this->error ( L('_ERROR_') );
        }
        $this->swf_upload($_REQUEST['id'],66);
        $this->insertTaskHistory($_POST['id'], 'mis_task_information',$id,'修改任务', 2);
		if ($_POST['ispushmsg']) {
			if ($_POST['executeuser'] != $_POST['oldexecuteusers']) {
				// 修改负责人 系统推送消息
				$this->systemMessage($id, $_POST['oldexecuteusers'], 11);
			}
			//系统推送消息
			$this->systemMessage($id, $_POST['executeuser'], 2);
		}
    }
     /**
	 * @Title: delete
	 * @Description: todo(delete)
	 * @author 
	 * @throws
	*/
    public function delete(){
        $model = D("MisTaskInformation");
        $taskid = $model->where('id=' .$_REQUEST['id'])->getField('taskid');
        //修改 mis_task_information 表 状态
        $result = $model->where('id=' . $_REQUEST['id'])->setField('status',-1);
        if ($result!==false) {
            //修改 mis_task 表状态
            $model = D("MisTask");
            $result = $model->where('id=' . $taskid)->setField('status',-1);
            if ($result!==false) {
                $this->success ( L('_SUCCESS_') );
            }else {
                $this->error ( L('_ERROR_') );
            }
        } else {
            $this->error ( L('_ERROR_') );
        }
    }
    
    /**
	 * @Title: lookupTask
	 * @Description: todo(查看子任务)
	 * @author 
	 * @throws
	*/
    public function lookupTask(){
        $id = $_REQUEST['id'];
        //获取任务信息
        $model = D("MisTaskInformation");
        $taskinfo = $model->where('id=' . $id)->find();
        $this->assign('vo', $taskinfo);
        //获取头信息
        $model = D("MisTask");
        $task = $model->where('id=' . $taskinfo['taskid'])->find();
        $this->assign('task', $task);
        //获取附件信息
        $map["status"]  =1;
		$map["orderid"] =$_REQUEST["id"];
		$map["type"] =66;
		$m=M("mis_attached_record");
		$attarry=$m->where($map)->select();
		$this->assign('attarry',$attarry);
        $scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail("MisTaskInformation");
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
        $this->display();
    }
    /**
     * @Title: lookupFeedback 
     * @Description: todo(跟踪查看的信息反馈)   
     * @author xiafengqin 
     * @date 2013-6-25 下午3:06:40 
     * @throws
     */
    public function lookupFeedback(){
    	$map["status"]  = 1;
    	$map['taskid'] = $_REQUEST['id'];
    	$model = D('MisTaskFeedbackInformation');
    	$remarkList = $model->where($map)->select();
    	$this->assign('feedbacklist',$remarkList);
    	$taskModel = D('MisTask');
    	$title = $taskModel->where('id='.$_REQUEST['id'])->find();
    	$this->assign('task',$title);
    	$this->display();
    }
    /**
	 * @Title: getParentTaskTree
	 * @Description: todo(获取父节点并组装数据)
	 * @return array
	 * @param $model 模型名
	 * @param $map 查询父节点条件
	 * @param $tree 空数组(装载结果)
	 * @param $order 排序字段
	 * @param $firstRow 起始条数
	 * @param $listRows 止条数
	 * @author $level
	 * @throws
	*/
    private function getParentTaskTree($model, $map, &$tree, $order = '', $firstRow = '', $listRows = ''){
        if($listRows) {
            $list = $model->where($map)->order( $order )->limit($firstRow . ',' . $listRows)->select();
        } else {
            $list = $model->where($map)->select();
        }
        foreach ($list as $key => $val) {
            $new = array();
            $new['id'] = $val['taskid'];
            $new['parent'] = 0;
            $new['loaded'] = true;
            $new['expanded'] = false;
            $new['level'] = 0;
            $new['remark'] = missubstr(strip_tags($val['remark']), 60, true);
            $new['title'] = missubstr($val['title'], 30, true);
            $new['begindate'] = transTime($val['begindate'], 'Y-m-d H:m');
            $new['enddate'] = transTime($val['enddate'], 'Y-m-d H:m');
            $new['executeuserid'] =$val['executeuser'];
            $new['executeuser'] = getFieldBy($val['executeuser'],'id','name','User');
            $new['status'] = getStatus($val['status']);
            $new['executingstatus'] = excelTplselected($val['executingstatus'], '1:未查看,2:已查看,3:进行中,4:关闭,5:暂停,6:完成,7:结束,8:申请暂停,0:未启动');
            $new['urgentstatus'] = excelTplselected($val['urgentstatus'], '1:<span class="levelGreen">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>,2:<span class="levelOrange">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>,3:<span class="levelRed">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>');
            $new['difficulty'] = excelTplselected($val['difficulty'], '1:简单,2:一般,3:困难,4:极难');
            $new['chedule'] = '<span class="taskprogressbar"><span class="taskpspan1" style="width:'.$val['chedule'].'%;"></span><span class="taskpspan2">'.$val['chedule'].'%</span></span>';
            $new['button'] = "<a href='__URL__/lookupTask/id/".$val['id']."' title='子任务基本信息查看' target='dialog' width='600' height='500' mask='true'>基本信息</a>";
            $tree[$new['id']] = $new;
            $isLeaf = $this->getChildTaskTree($val['taskid'], $tree,0);
            $tree[$new['id']]['isLeaf'] = $isLeaf;
        }
        return $tree;
    }
    /**
	 * @Title: getChildTaskTree
	 * @Description: todo(获取子节点)
	 * @return array
	 * @param $taskid taskid
	 * @param $taskTree 节点树
	 * @param $level 层级
	 * @author jiangx
	 * @throws
	*/
    private function getChildTaskTree($taskid, &$taskTree, $level){
        $level ++;
        $model = D("MisTaskInformationView");
        $childtask = $model->where('mis_task.pid = ' .$taskid . ' AND mis_task.status = 1')->select();
        if (!$childtask) {
            return true;
        }
        foreach ($childtask as $key => $val) {
            $new = array();
            $new['id'] = $val['taskid'];
            $new['parent'] = $val['pid'];
            $new['loaded'] = true;
            $new['expanded'] = false;
            $new['level'] = $level;
            $new['remark'] = missubstr(strip_tags($val['remark']), 60, true);
            $new['title'] = missubstr($val['title'], 30, true);
            $new['begindate'] = transTime($val['begindate'], 'Y-m-d H:m');
            $new['enddate'] = transTime($val['enddate'], 'Y-m-d H:m');
            $new['executeuserid'] =$val['executeuser'];
            $new['executeuser'] = getFieldBy($val['executeuser'],'id','name','User');
            $new['executingstatus'] = excelTplselected($val['executingstatus'], '1:未查看,2:已查看,3:进行中,4:关闭,5:暂停,6:完成,7:结束,8:申请暂停,0:未启动');
            $new['urgentstatus'] = excelTplselected($val['urgentstatus'], '1:<span class="levelGreen">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>,2:<span class="levelOrange">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>,3:<span class="levelRed">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>');
            $new['difficulty'] = excelTplselected($val['difficulty'], '1:简单,2:一般,3:困难,4:极难');
            $new['chedule'] = '<span class="taskprogressbar"><span class="taskpspan1" style="width:'.$val['chedule'].'%;"></span><span class="taskpspan2">'.$val['chedule'].'%</span></span>';
            $new['status'] = getStatus($val['status']);
            $new['button'] = "<a href='__URL__/lookupTask/id/".$val['id']."' title='子任务基本信息查看' target='dialog' width='600' height='500' mask='true'>基本信息</a>";
            $taskTree[$new['id']] = $new;
            $isLeaf = $this->getChildTaskTree($val['taskid'], $taskTree, $level);
            $taskTree[$new['id']]['isLeaf'] = $isLeaf;
        }
        return false;
    }
    
    public function lookupmanage(){
		$model= M('mis_system_department');
		$deptlist = $model->where("status=1")->order("id asc")->select();
		$param['rel']="mistaskinformationuserlistBox";
		$param['url']="__URL__/lookupmanage/jump/1/deptid/#id#/parentid/#parentid#";
		$typeTree = $this->getTree($deptlist,$param);
		$this->assign('tree',$typeTree);
		$map = array();
		$searchby = str_replace("-", ".", $_POST["searchby"]);
		$keyword=$this->escapeChar($_POST["keyword"]);
		$searchtype = $_POST['searchtype'];
		if($_POST["keyword"]){
		    $map[$searchby] = ($searchtype==2)  ? array('like','%'.$keyword.'%'):$keyword;
		    $this->assign('keyword',$keyword);
		    $searchby = str_replace(".", "-", $_POST["searchby"]);
		    $this->assign('searchby',$searchby);
		    $this->assign('searchtype',$searchtype);
		}
		$searchby=array(
            array("id" =>"user-name","val"=>"按员工姓名"),
		    array("id" =>"orderno","val"=>"按员工编号"),
		);
		$searchtype=array(array("id" =>"2","val"=>"模糊查找"),
		    array("id" =>"1","val"=>"精确查找"));
		$this->assign("searchbylist",$searchby);
		$this->assign("searchtypelist",$searchtype);
		
		$map['working']=1;   //未离职
		$map['user.status']=1;	//状态正常
		
		$deptid		= $_REQUEST['deptid'];
		$parentid	= $_REQUEST['parentid'];
		if ($deptid && $parentid) {
		    $deptlist =array_unique(array_filter (explode(",",$this->findAlldept($deptlist,$deptid))));
		    $map['user.dept_id'] = array(' in ',$deptlist);
		}
		$this->_list('MisHrPersonnelPersonInfoView',$map);
		$this->assign('deptid',$deptid);
		$this->assign('parentid',$parentid);
		if ($_REQUEST['jump']) {
		    $this->display('lookupmanagelist');
		} else {
		    $this->display("lookupmanage");
		}
	}
    /**
	 * @Title: systemMessage
	 * @Description: todo(系统发送消息)
	 * @return array
	 * @param $infoid 任务id
	 * @param $userid 通知的人id
	 * @param $type 类型 3任务开始通知 4 任务关闭通知 5 任务暂停通知 6 任务完成通知 11 任务转派通知以前负责人
	 * @author jiangx
	 * @throws
	*/
    private function systemMessage($infoid, $userid, $type = 1){
        $model = D("MisTaskInformationView");
        $task = $model->where('mis_task_information.id = '. $infoid)->find();
        if (!$task) {
            return '';
        }
        //接收人
        $recipientname = getFieldBy($userid,'id','name','User');
        //消息发送人
        $sendusername = getFieldBy($_SESSION[C('USER_AUTH_KEY')],'id','name','User');
        //任务责任人员
        $executeusername = getFieldBy($task['executeuser'],'id','name','User');
        //任务跟踪人员
        $trackusername = getFieldBy($task['trackuser'],'id','name','User');
        $oplist = array('0' => '要求继续','1' => '发布','2' => '修改','3'=>'要求继续','4' => '关闭','5' => '暂停','7' => '结束','0' => '跟踪','11' => '转派','12' => '提出缺陷反馈');
        $opname = $oplist[$type];
        $messageTitle = '任务' . $opname . '通知';
		$this->assign('recipientname', $recipientname);
		$this->assign('sendusername', $sendusername);
		$this->assign('opname', $opname);
		$this->assign('task', $task);
		$this->assign('type', $type);
		$this->assign('executeusername', $executeusername);
		$this->assign('trackusername', $trackusername);
		$this->assign('typeopname', $type == 0 ? $opname.'审核' : '知晓');
		$messageContent = $this->fetch("msgcontent");
        //系统推送消息
        $this->pushMessage(array($userid), $messageTitle, $messageContent, true, 1, $infoid);
    }
    /**
     * @Title: trace 
     * @Description: todo(任务列表的跟踪查看)   
     * @author xiafengqin 
     * @date 2013-6-26 上午10:39:38 
     * @throws
    */
    public function trace(){
		$this->assign('zhi', $_REQUEST['zhi']);
		//传过来的id值 实际全为taskid
		$_REQUEST['taskid'] = $_REQUEST['id'];
		$informationModel = D('MisTaskInformation');
		$result = $informationModel->where('taskid='.$_REQUEST['taskid'])->find();
		$this->assign('vo',$result);
		$_REQUEST['id'] = $result['id'];
		$model = D('MisTask');
		$task = $model->where('id=' . $_REQUEST['taskid'])->find();
		$this->assign('task',$task);
		//判断是否包含子任务
        $count = $model->where('pid = ' . $task['id'])->count();
        $this->assign('ishavechild', $count);
		//判断是查看还是可操作
		$taskisView = true;
		//判断交流反馈是否可回复 (即是否结束或关闭)
		$isexchange = true;
        if ($result['executingstatus'] >3) {
            $taskisView = false;
        }
		//子任务显示时，判断当前用户是否为发布人或跟踪人
        if ($_SESSION[C('USER_AUTH_KEY')] !=  $result['trackuser'] || $_SESSION[C('USER_AUTH_KEY')] !=  $result['createid']) {
            $taskisView = false;
			$isexchange = false;
        }
		 $this->assign('taskView', $taskisView);
		
		if ($result['executingstatus'] == 4 || $result['executingstatus'] == 7) {
			$isexchange = false;
		}
		$this->assign('isexchange', $isexchange);
		switch ($_REQUEST['navtab']) {
			//case 'getAttachment':
			//	//附件文档
			//	$this->getAttachment($result['id']);
			//	exit;
			//	break;
			case 'communicationFeedback':
				//交流与反馈
				$this->communicationFeedback($_REQUEST['taskid']);
				exit;
				break;
			case 'lookupIdea':
				//交流与反馈
				$this->lookupIdea($_REQUEST['taskid'],$_REQUEST['identifying']);
				exit;
				break;
			case 'lookupPlanCondition':
				//执行情况
				$this->getTaskPlanAnalysis($_REQUEST['taskid'], $result);
				exit;
				break;
			case 'relevques':
				//关联问题
				$this->relevques($_REQUEST['taskid']);
				exit;
				break;
			case 'getchildtask':
				//获取子任务信息
				$this->getChildTaskView($_REQUEST['taskid']);
				exit;
				break;
		}            
		$userModel = D("User");
		$userinfo = $userModel->where('id = ' . $result['executeuser'])->getField('qq');
		$this->assign('userinfo', $userinfo);
		$this->assign('teaceindex',$_REQUEST['teaceindex']);
		//查看关注
		$attentModel = D("MisTaskAttention");
		$map = array();
		$map['tableid'] = $_REQUEST['id'];
		$map['modelname'] = "MisTaskInformation";
		$map['type'] = 2;
		$attent = $attentModel->where($map)->find();
		$this->assign('attent', $attent);
		$this->assign('identifying',$_REQUEST['identifying']);
		$setupModel = D("MisTasksColorCoded");
		//时钟字体颜色区分
		$map = array();
		$map['status'] = 1;
		$map['createid'] = $_SESSION[C('USER_AUTH_KEY')];
		$setupinfo = $setupModel->where($map)->find();
		if ($setupinfo) {
			$colorlist = explode(':', $setupinfo['colorcoded']);
			$intervaltime = $result['enddate'] - time();
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
		$this->display();
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
	 * @author jiangx
	 * @date 2013-08-22
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
			$val['toview'] = '<a class="iconView" mask="true" title="子任务查看" rel="mistaskinfochildview_'.$val['taskid'].'" href="__URL__/trace/identifying/1/id/'.$val['taskid'].'" target="dialog" width="900" height="630"></a>';
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
     * @Title: relevques 
     * @Description: todo(关联问题)
     * @param $taskid mis_task.id
     * @author xiafengqin 
     * @date 2013-8-1 上午11:12:33 
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
     * @Title: lookupIdea
     * @Description: todo(获取缺陷)
     * @author xiafengqin
     * @date 2013-6-29 下午2:14:48
     * @throws
     */
    private function lookupIdea($taskid,$identifying){
    	$InformationModel=D("MisTaskInformation");
    	$trackuser = $InformationModel->where('taskid='.$taskid)->getField('trackuser');
    	$this->assign('trackuser',$trackuser);
    	$feedbackModel = D('MisTaskFeedbackInformation');
    	//缺陷
    	$voList = $feedbackModel->where('isdefect = 1 AND pid = 0 AND type = 3 AND taskid='.$taskid )->select();
		foreach ($voList as $key => $val) {
    		$voList[$key]['remark'] = missubstr($val['remark'], 180);
    	}
    	$this->assign('voList',$voList);
    	$this->assign('identifying',$identifying);
    	$this->display('lookupIdea');
    }
    
    /**
     * @Title: getAttachment
     * @Description: todo(获取附件)
     * @param $id 任务基本信息id
     * @author jiangx
     * @throws
     */
    private function getAttachment($id){
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
		$this->assign('taskid', $info['taskid']);
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
     * @Title: getTaskHistory 
     * @Description: todo(获取操作历史)   
     * @author jiangx 
     * @date 2013-6-26 上午10:39:38 
     * @throws
     */
    private function getTaskHistory($taskid){
        $model = D("MisTaskHistory");
        $historylist = $model->where('taskid = ' . $taskid)->order('id ASC')->select();
        $this->assign('historylist', $historylist);
        
    }
    /**
     * @Title: getTaskPlanAnalysis 
     * @Description: todo(跟踪/查看页面的进度分析) 
     * @param unknown_type $taskid  
     * @author xiafengqin
     * @date 2013-6-28 下午4:34:10
     * @modify 2013-08-08 jiangx 2013-08-21 jiangx
     * @throws
     */
    private function getTaskPlanAnalysis($taskid, $info){
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
		$feedlist = $feedbackInformationModel->where($map)->order('createtime')->select();
		//获取任务的开始时间戳
		$begintime = strtotime(date('Y-m-d', $info['begindate']));
		//获取该任务结束的时间,如果没有结束，则当前时间拟定为结束时间
		if ($info['executingstatus'] == 4 || $info['executingstatus'] == 5 || $info['executingstatus'] == 7) {
			$endtime = $info['updatetime'];
		} else {
			$endtime = time();
		}
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
		//key值升序排序
		ksort($datefeedlist);
		$this->assign('datefeedlist', $datefeedlist);
    	$this->display('lookupPlanCondition');
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
     * @Title: lookupsolve
     * @Description: todo(缺陷) 
     * @author jiangx 
     * @date 2013-7-24 下午4:15:10 
     * @throws
     */
	public function lookupsolve(){
		$feedbackModel = D("MisTaskFeedbackInformation");
		if ($_REQUEST['type'] == 'add') {
			//插入缺陷
			$_POST['type'] = 3;
			$_POST['isdefect'] = 1;
			$_POST['issolve'] = 0;
			if (false === $feedbackModel->create ()) {
				$this->error ( $feedbackModel->getError () );
			}
			$list = $feedbackModel->add ();
			if ($list == false) {
				$this->error ( L('_ERROR_') );
			}
			$infoid = getFieldBy($_POST['taskid'],'taskid','id','MisTaskInformation');
			$this->swf_upload($list, 72);
			//插入加关注
			if ($_POST['isattention']) {
				$AttentModel = D("MisTaskAttention");
				$_POST['tableid'] = $list;
				$_POST['modelname'] = 'MisTaskFeedbackInformation';
				$_POST['type'] = 4;
				if (false === $AttentModel->create ()) {
					$this->error ( $AttentModel->getError () );
				}
				$result = $AttentModel->add ();
				if ($result === false) {
					$this->error ( L('_ERROR_') );
				}
			}
			if ($_POST["ispushmsg"]) {
				$infoModel = D("MisTaskInformation");
				$info = $infoModel->where('taskid = ' . $_POST['taskid'])->find();
				//系统推送执行人消息
				$this->systemMessage($info['id'], $info['executeuser'], 12);
			}
			$this->success ( L('_SUCCESS_') );
		} else {
			//进入添加缺陷页面
			$taskModel = D("MisTask");
			if ($_REQUEST['type'] == 'view') {
				//查看缺陷页面
				$feedback = $feedbackModel->where('id = ' .$_REQUEST['id'])->find();
				
				$task = $taskModel->where('id = ' . $feedback['taskid'])->find();
				$this->assign('task', $task);
				
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
				$this->display("solveView");
				exit;
			}
			$task = $taskModel->where('id = ' . $_REQUEST['taskid'])->find();
			$this->assign('task', $task);
			$this->display();
			exit;
		}
	}
	
	/**
     * @Title: lookupaddtrace
     * @Description: todo(添加审核意见) 
     * @author xiafengqin 
     * @date 2013-7-24 下午4:15:10 
     * @throws
     */
	public function lookupaddtrace(){
		$taskModel = D("MisTask");
		$model = D ( "MisTaskInformation" );
		$task = $taskModel->where('id = ' . $_REQUEST['taskid'])->find();
		$map['taskid']=$_REQUEST['taskid'];
		$map['status']=1;
		$vo = $model->where($map)->find();
		$this->assign('vo',$vo);
		$this->assign('task', $task);
		$this->display();
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
     * @Title: myAttention
     * @Description: todo(进入我的关注列表页)
     * @author jiangx
     * @date 2013-08-14
     * @throws
     */
	public function myAttention(){
		$model = D("MisTaskAttention");
		$map = array();
		$map['status'] = 1;
		$map['createid'] = $_SESSION[C('USER_AUTH_KEY')];
		$attenlist = $model->where($map)->select();
		$idarr = array();
		foreach ($attenlist as $key =>$val) {
			$idarr[$val['modelname']][] = $val['tableid'];
		}
		//获取问题关注
		if ($idarr['MisTaskquestion']) {
			$map = array();
			$map['id'] = array('in', $idarr['MisTaskquestion']);
			$map['status'] = 1;
			$questionModel = D("MisTaskquestion");
			$questionlist = $questionModel->where($map)->select();
			$this->assign('questionlist', $questionlist);
		}
		//获取缺陷关注
		if ($idarr['MisTaskFeedbackInformation']) {
			$map = array();
			$map['id'] = array('in', $idarr['MisTaskFeedbackInformation']);
			$map['status'] = 1;
			$feedModel = D("MisTaskFeedbackInformation");
			$feedlist = $feedModel->where($map)->select();
			$this->assign('feedlist', $feedlist);
		}
		//获取任务关注
		$viewModel = D("MisTaskInformationView");
		$infolist = array();
		$infoadlist = array();
		if ($idarr['MisTaskInformation']) {
			$map = array();
			$map['id'] = array('in', $idarr['MisTaskInformation']);
			$map['status'] = 1;
			$infolist = $viewModel->where($map)->select();
		}
		if ($idarr['MisTaskAdscription']) {
			$map = array();
			$map['id'] = array('in', $idarr['MisTaskAdscription']);
			$map['status'] = 1;
			$infoadlist = $viewModel->where($map)->select();
			foreach ($infoadlist as $key => $val) {
				//标示为我的执行关注
				$infoadlist[$key]['isinfo']  = true;
			}
		}
		$infolist = array_merge($infolist, $infoadlist);
		$this->assign('infolist', $infolist);
		$this->display();
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
     * @Title: lookupdeleteMSG
     * @Description: todo(删除任务消息--真删)
     * @author jiangx
     * @date 2013-08-14
     * @throws
     */
	public function lookupdeleteMSG(){
		if ($_REQUEST['id']) {
			$msgModel = D("MisMessage");
			$msgModel->delete($_REQUEST['id']);
			$msguserModel = D("MisMessageUser");
			$result = $msguserModel->where('sendid = '. $_REQUEST['id'])->delete();echo '1';
			if ($result) {
				$this->transaction_model->commit();//事务提交
				exit;
			}
			exit(0);
		}
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