<?php
/**
 * @Title: MisTaskquestionAction
 * @Package 任务管理-提问动态：功能类
 * @Description: TODO(任务管理-提问动态)
 * @author jiangx
 * @company 重庆特米洛科技有限公司˾
 * @copyright 重庆特米洛科技有限公司˾
 * @date 2013-7-30
 * @version V1.0
 */
class MisTaskquestionAction extends CommonAction{
	/**
	 * @Title: index 
	 * @Description: todo(通过视图进入首页)   
	 * @author 
	 * @throws
	 */
	public function index() {
		$map = array();
		$map = $this->_search ();
		if ($_REQUEST['expertype']) {
			$map['expertype'] = $_REQUEST['expertype'];
		}
		$map['sourcetype'] = 1;
		//$map['expertid'] = $_SESSION[C('USER_AUTH_KEY')];
		$this->_list ( 'MisExpertquestionListView', $map );
		$viewModel = D("MisExpertquestionListView");
		$viewlsit = $viewModel->where($map)->order('id DESC')->select();
		//$overquestionlist = array();
		//$noquestionlist = array();
		$myoutquestion = array();
		$myinquestion = array();
		
		foreach ($viewlsit as $key => $val) {
			//我提出的问题
			if ($val['createid'] == $_SESSION[C('USER_AUTH_KEY')]) {
				$myoutquestion[] = $val;
			}
			//问我的问题
			if ($val['expertype'] != 0 && $val['expertid'] == $_SESSION[C('USER_AUTH_KEY')]) {
				$myinquestion[] = $val;
			}
			
			//if ($val['selchildid']) {
			//	//已回复
			//	$overquestionlist[] = $viewlsit[$key];
			//} else {
			//	//未回复
			//	$noquestionlist[] = $viewlsit[$key];
			//}
		}
		$this->assign('myoutquestion', $myoutquestion);
		$this->assign('myinquestion', $myinquestion);
		$this->display();
	}
	/**
	 * @Title: edit 
	 * @Description: todo(进入问题页面)
	 * @author 
	 * @throws
	 */
	public function edit(){
		$this->assign('zhi', $_REQUEST['zhi']);
		$viewModel = D("MisExpertquestionListView");
		$question = $viewModel->where('mis_expert_question_list.id = ' . $_REQUEST['id'])->find();
		$answerquestion = $viewModel->where('mis_expert_question_list.parentid = ' . $_REQUEST['id'])->find();
		$this->assign('answerquestion', $answerquestion);
		if ($question['selchildid']) {
			$question['isover'] = 1;
		} else {
			$question['isover'] = 0;
		}
		//是否允许回答问题
		if ($question['expertype'] == 1 || $question['expertype'] == 2) {
			if ($question['expertid'] == $_SESSION[C('USER_AUTH_KEY')]) {
				$this->assign("isanswer", true);
			}
		}
		$this->assign('question', $question);
		switch ($_REQUEST['tabs']) {
			case 'questioncontent':
				$answerquestion = $viewModel->where('mis_expert_question_list.parentid = ' . $_REQUEST['id'])->find();
				
				$this->display('questioncontent');
				exit;
				break;
			case 'questionattarry':
				$map = array();
				$map["status"]  =1;
				$map["orderid"] =$_REQUEST["id"];
				$map["type"] =33;
				$m=M("mis_attached_record");
				$attarry=$m->where($map)->select();
				$this->assign('attarry',$attarry);
				if ($question['isover'] == 0 && $_REQUEST['type'] == 'execute') {
					//暂时不用修改附件
					//$this->display('questionattarry');
					//exit;
				}
				$this->display('questionattarryview');
				exit;
				break;
			case 'comment':
				$answerquestionid = $viewModel->where('mis_expert_question_list.parentid = ' . $_REQUEST['id'])->getField('id');
				$countlist = $viewModel->where('mis_expert_question_list.parentid = ' . $answerquestionid)->select();
				$this->assign('countlist', $countlist);
				$this->assign('parentid', $answerquestionid);
				$this->display('comment');
				exit;
				break;
			default :
				$this->display();
				break;
		}
	}
	/** 
	 * @Title: lookupSaveComment 
	 * @Description: todo(ajax保存评论)   
	 * @author jiangx 
	 * @date 2013-8-2
	 * @throws 
	*/  
	public function lookupSaveComment(){
		$model = D("MisExpertQuestionList");
		$_POST['sourcetype'] = 1;
		$_POST['type'] = 'C';
		C ( "TOKEN_ON", false );
		if (false === $model->create ()) {
			exit('0');
		}
		$list = $model->add ();
		$this->transaction_model->commit();//事务提交
		C ( "TOKEN_ON", true );
		if ($list === false) {
			exit('0');
		}
		$comment = $model->where('id = ' . $list)->find();
		$comment['createtime'] = transTime($comment['createtime'], 'Y-m-d H:i');
		echo json_encode($comment);
		exit;
	}
	/** 
	 * @Title: add 
	 * @Description: todo(对应TPL的Add方法)   
	 * @author jiangx 
	 * @date 2013-8-1
	 * @throws 
	*/  
	public function add() {
		if (!$_REQUEST['sourcetype']) {
			$_REQUEST['sourcetype'] = 0;
		}
		$this->assign('sourceid', $_REQUEST['sourceid']);
		$this->assign('sourcetype', $_REQUEST['sourcetype']);
		$taskinfoid = getFieldBy($_REQUEST['sourceid'],'taskid','id','MisTaskInformation');
		$this->assign('taskinfoid', $taskinfoid);
		$model=M("mis_expert_question_type");
		$list =$model->field("id,name")->select();
		$this->assign("typeidlist",$list);
		if($_GET['parentid']) $this->assign("parentid",$_GET['parentid']);
		$this->assign("type","Q");
		$scdmodel = D('SystemConfigDetail');
		$modelname = $this->getActionName();
		$detailList = $scdmodel->getDetail($modelname,false);
		if ($detailList) {
			$fieldsarr = array();
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
								//$showname .= " | ";
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
							//$showname .= "<a rel='".$modelarr[0].$modelarr[1]."' target='".$target."' href='__APP__/".$method."' mask='true'>".$normArray[$modelarr[1]]."</a>";
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
		$mrdmodel = D('MisRuntimeData');
		$data = $mrdmodel->getRuntimeCache($modelname,'add');
		$this->assign("data",$data);
		$this->display ();
	}
	/**
	 * @Title: lookup 
	 * @Description: todo(带回查找)   
	 * @author jiangx 
	 * @date 2013-8-1
	 * @throws
	 */
	function lookup(){
		//检索专家
		$searchbylist=array(array("id" =>"code","val"=>"编号"),array("id" =>"typename","val"=>"分类"),array("id" =>"name","val"=>"专家名称"));
		$searchtypelist=array(array("id" =>"2","val"=>"模糊查找"),array("id" =>"1","val"=>"精确查找"));
		$this->assign("searchbylist",$searchbylist);
		$this->assign("searchtypelist",$searchtypelist);
		$searchby= $_POST['searchby'];
		$searchtype= $_POST['searchtype'];
		$keyword= $_POST['keyword'];
		if( $searchby )$this->assign('searchby', $searchby);
		if( $searchtype )$this->assign('searchtype', $searchtype);
		if( $keyword )$this->assign('keyword', $keyword);
		if ($keyword){
			$map1[$searchby] = ($searchtype==2) ? array('like',"%".$keyword."%"):$keyword;
		}
		//显示对专家提问列表
		$model=D("MisExpertListView");
		$map1["mis_expert_list.status"]=1;
		$list =$model->where($map1)->select();
		$this->assign("list",$list);
		$this->display ();
	}
	/**
	 * 
	 * @Title: _before_insert 
	 * @Description: todo(插入前置函数) 
	 * @author jiangx 
	 * @date 2013-8-1
	 * @throws
	 */
	public function _before_insert(){
		$taskviewModel = D("MisTaskInformationView");
		$viewtask = $taskviewModel->where('taskid = ' . $_POST['sourceid'])->find();
		if ($_POST['expertype'] == 1) {
			$_POST['expertid'] = $viewtask['createid'];
		}
		if ($_POST['expertype'] == 2) {
			$_POST['expertid'] = $viewtask['trackuser'];
		}
		$_POST['isynchronous'] = 1;
	}
	/**
	 * 
	 * @Title: _after_insert 
	 * @Description: todo(插入前置函数) 
	 * * @author jiangx 
	 * @date 2013-8-1
	 * @throws
	 */
	public function _after_insert( $insertid){
		$this->swf_upload($insertid,33);//上传附件
		if($_POST["type"]=="A"){
			$model=M("mis_expert_question_list");
			$re = $model->where('id='.$_POST["parentid"])->setField('selchildid', $insertid);
			if( !$re ) $this->error( L("_ERROR_"));
		}
		//插入加关注
		if ($_POST['isattention']) {
			$AttentModel = D("MisTaskAttention");
			$_POST['tableid'] = $insertid;
			$_POST['modelname'] = 'MisTaskquestion';
			$_POST['type'] = 5;
			if (false === $AttentModel->create ()) {
				$this->error ( $AttentModel->getError () );
			}
			$result = $AttentModel->add ();
			if ($result === false) {
				$this->error ( L('_ERROR_') );
			}
		}
	}
	/**
	 * @Title: reply 
	 * @Description: todo(回复) 
	 * * @author jiangx 
	 * @date 2013-8-1
	 * @throws
	 */
	public function reply(){
		$questionmodel = D("MisExpertquestionList");
		$anwser = $questionmodel->where('parentid = ' . $_REQUEST['id'])->find();
		$this->assign('anwser', $anwser);
		if (!$_GET["id"]) {
			$this->error("请刷新页面重试!");
		}
		$this->assign("sourcetype",$_REQUEST['sourcetype']);
		$this->assign("sourceid",$_REQUEST['sourceid']);
		
		
		$this->assign("type","A");
		$this->assign("parentid",$_GET["id"]);
		$this->display();
	}
	/**
	 * @Title: update 
	 * @Description: todo(修改保存函数)   
	 * @throws
	 */	
	public function update() {
		$name=$this->getActionName();
		$model = D ( $name );
		if(C('TOKEN_NAME')) $_POST[C('TOKEN_NAME')]= $_SESSION[C('TOKEN_NAME')];
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$list=$model->save ();
		if (false !== $list) {
			$this->success ( L('_SUCCESS_'),"",$list);
		} else {
			$this->error ( L('_ERROR_') );
		}
	}
	/**
	 * @Title: lookupYnchronous 
	 * @Description: todo(同步知识库)
	 * @author jiangx
	 * @date 2013-08-05 
	 * @throws
	 */	
	public function lookupYnchronous(){
		$questionModel = D("MisExpertquestionList");
		$isynchronous = $questionModel->where('id = ' .$_POST['id'])->getField('isynchronous');
		//echo $questionModel->getlastsql();
		if ($isynchronous == 1) {
			$isynchronous = 0;
		} else {
			$isynchronous = 1;
		}
		$result = $questionModel->where('id = ' .$_POST['id'])->setField('isynchronous', $isynchronous);
		$this->transaction_model->commit();//事务提交
		if ($result) {
			$isynchronous++;
			exit("$isynchronous");
		} else {
			exit('0');
		}
	}
}
?>