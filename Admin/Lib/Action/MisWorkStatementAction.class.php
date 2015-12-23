<?php
/**
 * @Title: file_name
 * @Package package_name
 * @Description: todo(工作报告控制器)
 * @author liminggang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-7-2 下午5:02:46
 * @version V1.0
 */
class MisWorkStatementAction extends CommonAction {
	/**
	 * @Title: _filter
	 * @Description: todo(重写CommonAction的index方法，展示列表)
	 * @return string
	 * @author 杨希
	 * @date 2013-5-31 下午3:59:44
	 * @throws
	 */
	public function _filter(&$map){
		if(!$_REQUEST ['orderField']){
			$_REQUEST ['orderField'] = 'createtime';
		}
		if(!$_REQUEST ['orderDirection']){
			$_REQUEST ['orderDirection'] = 'desc';
		}
		if ($_SESSION["a"] != 1){
			$map['status']=array("gt",-1);
		}
	}
	private function getLeftZtree(){
		//构造左侧报告类型树
		$MisExecutionTypeDao=M("mis_order_types");
		$extypelist=$MisExecutionTypeDao->where('type = 67 and status = 1')->field('id,name')->select();
		$param['rel']="misworkstatementBox";
		$param['url']="__URL__/index/jump/1/typeid/#id#";
		$arr=array();
		/* if($_SESSION['misworkreadstatement_index'] || $_SESSION['a']){
			//判断是否有查阅报告的权限。
			$arr=array();
		} */
		$returnarr=array(
				array(
						'id'=>0,
						'parentid'=>0,
						'name'=>'个人报告',
						'title'=>'个人报告',
						'open'=>true,
						'rel'=>'misworkstatementBox',
						'url'=>'__URL__/index/jump/1',
						'target'=>'ajax'
				),
		);
		$treearr=$this->getTree($extypelist,$param,$returnarr);
		return $treearr;
	}


	function index(){
		$treearr=$this->getLeftZtree();
		$this->assign("treearr",$treearr);
		
		$name=$_REQUEST['md']?$_REQUEST['md']:'MisWorkStatement';
		
		$map = $this->_search($name);
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$typeid=$_REQUEST['typeid']? $_REQUEST['typeid']: 0;
		$this->assign('typeid',$typeid);
		if($typeid){
			$map['typeid'] = $typeid;
		}
		$datetime = $_REQUEST['datetime']?$_REQUEST['datetime']  : 0;
		$this->assign('datetime',$datetime);
		$datetime=strtotime($datetime);
		$endtime = $datetime+86400;
		if($datetime){
			$map['createtime'] =array('exp','>='.$datetime.' and createtime <'.$endtime);
		}
		//动态配置部分。
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($name);
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		//searchby搜索扩展
		$searchby = $scdmodel->getDetail($name,true,'searchby');
		if ($searchby && $detailList) {
			$searchbylist=array();
			foreach( $detailList as $k=>$v ){
				if(isset($searchby[$v['name']])){
					$arr['id']= $searchby[$v['name']]['field'];
					$arr['val']= $v['showname'];
					array_push($searchbylist,$arr);
				}
			}
			$this->assign("searchbylist",$searchbylist);
		}
		//扩展工具栏操作
		$toolbarextension = $scdmodel->getDetail($name,true,'toolbar');
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		//查询数据部分
		if (! empty ( $name )) {
// 				$qx_name=$name;
// 				if(substr($name, -4)=="View"){
// 					$qx_name = substr($name,0, -4);
// 				}
// 				//验证浏览及权限
// 				if( !isset($_SESSION['a']) ){
// 					if( $_SESSION[strtolower($qx_name.'_'.ACTION_NAME)]!=1 ){
// 						if( $_SESSION[strtolower($qx_name.'_'.ACTION_NAME)]==2 ){////判断部门及子部门权限
// 							$map["createid"]=array("in",$_SESSION['user_dep_all_child']);
// 						}else if($_SESSION[strtolower($qx_name.'_'.ACTION_NAME)]==3){//判断部门权限
// 							$map["createid"]=array("in",$_SESSION['user_dep_all_self']);
// 						}else if($_SESSION[strtolower($qx_name.'_'.ACTION_NAME)]==4){//判断个人权限
// 							$map["createid"]=$_SESSION[C('USER_AUTH_KEY')];
// 						}
// 					}
// 				}
			if ($_SESSION["a"] != 1) $map["createid"]=$_SESSION[C('USER_AUTH_KEY')];
// 			foreach ($map as $k=>$v){//搜索全部时
// 				if($v['mis_work_statement.commentstatus'] && is_array($v)){
// 					unset($map[$k]['mis_work_statement.commentstatus']);
// 				}else if($k=='mis_work_statement.commentstatus'){//搜索 评论时
// 					unset($map);
// 				}
// 			}
			$this->_list ( $name, $map ,"createtime");
		}
		if( intval($_POST['dwzloadhtml']) ){
			$this->display("dwzloadindex");exit;
		}
		if ($_REQUEST['jump']) {
			$this->display('unitlist');
		} else {
			$this->display();
		}
	}
	
	/**
	 * @Title: _after_list
	 * @Description: todo(执行了CommonAction类里面的_list方法的后置函数)
	 * @param unknown_type $voList
	 * @author xiafengqin
	 * @date 2013-5-31 下午4:25:44
	 * @throws
	 */
	function _after_list(&$voList){
		foreach ($voList as $k => $v) {
			$str = htmlspecialchars_decode($v['content'], ENT_QUOTES);//转码
			$str = trim(strip_tags(str_replace("&nbsp;", ' ', $str)));//过滤html
			$voList[$k]['content'] = missubstr($str,35,true);
			$voList[$k]['md'] = $_REQUEST['md']?$_REQUEST['md']:'MisWorkStatement';
		}
	}


	/**
	 * @Title: _before_add
	 * @Description: todo(add页面打开前传递展示信息)
	 * @return string
	 * @author liminggang
	 * @date 2013-5-31 下午3:59:44
	 * @throws
	 */
	public function _before_add(){
		if ($_GET['datetime']) {
			$datetime=date('Y年m月d日', strtotime($_GET['datetime']));
			$this->assign('datetime',$datetime);
		}
		//订单号可写
		$scnmodel = D('SystemConfigNumber');
		$writable= $scnmodel->GetWritable('mis_order_types');
		$this->assign("writable",$writable);
		//自动生成订单编号
		$code = $scnmodel->GetRulesNO('mis_work_statement');
		$this->assign("code", $code);
		//查询报告类型
		$MisExecutionTypeDao=M("mis_order_types");
		$extypelist=$MisExecutionTypeDao->where('type = 67 and status = 1')->field('id,name')->select();
		$this->assign('extypelist',$extypelist);
		$userid=$_SESSION[C('USER_AUTH_KEY')];
		$this->assign('userid',$userid);
		$this->assign('time',time());
		$typeid=$_GET['type'];
		$this->assign('typeid',$typeid);
		
		//默认报告标题
		$userModel = D("User");
		$username = $userModel->where('id = '. $userid)->getField('name');
		$this->assign('username', $username);
		if ($datetime) {
			$this->assign('strdate', $datetime);
		}else{
			$this->assign('strdate', date('Y').'年'.date('m').'月'.date('d').'日');
		}
	}
	/**
	 * @Title: _before_insert
	 * @Description: todo(插入前置操作)
	 * @author jiangx
	 * @date 2013-7-22 下午17:44:49
	 * @throws
	 */
	public function _before_insert(){
		$patterns = array('/年/','/月/','/日/');
		$replacements = array('-','-','');
		$createtime = preg_replace($patterns,$replacements,$_POST['createtime']);
// 		if(count($_POST['lookpeople'])<1){
// 			$this->error("请先选择发送人，再进行保存或提交！");
// 			exit;
// 		}
		
		
		str_replace("年","-",$_POST['createtime']);
		
		$_POST['createtime'] = strtotime($createtime);
		$_POST['lookpeople'] = implode(",",	$_POST['lookpeople']);
		$_POST['lookpeoplename'] = implode(",",	$_POST['lookpeoplename']);
		$scnmodel = D('SystemConfigNumber');
		//自动生成订单编号
		$code = $scnmodel->GetRulesNO('mis_work_statement');
		$_POST['code'] = $code;
		$this->checkifexistcodeororder('mis_work_statement','code');
		//查询报告类型
		if($_POST['stepType']) $_POST['sendStatus'] = 1;// 发送状态
	}
	/**
	 * @Title: _after_insert
	 * @Description: todo(插入到待阅记录里面)
	 * @param unknown_type $workid
	 * @author liminggang
	 * @date 2013-7-3 下午2:56:49
	 * @throws
	 */
	public function _after_insert($workid){
		$UserModel=D("User");
		//插入数据
		if($_POST['sysselectuser']){
			$result=$UserModel->setSelectUser("MisWorkStatement",$workid,"sysselectuser");
			if(!$result){
				$this->error("添加知会人失败!");
			}
		}
		//会议纪要
		$arr=$arra=$copearr=array();
		if($_POST['stepType']){
			//将获得的数据分隔成数组
			$peoplearr=array_unique(explode(',',$_POST['lookpeople']));
			foreach($peoplearr as $key=>$val){
				$arr[]=array(
						'workid' =>$workid,
						'userid'=>$val,
						'copystep'=>1,
						'createtime'=>$_POST['createtime'],
						'createid'=>$_SESSION[C('USER_AUTH_KEY')]
				);
			}
			//实例化对象
			$MisWorkReadStatementModel=D("MisWorkReadStatement");
			$result=$MisWorkReadStatementModel->addAll($arr);
			if(!$result){
				$this->error(L("添加接收人失败！"));
			}
		}
		if($workid){
			$this->swf_upload($workid,67);
		}
	}
	/**
	 * @Title: _before_edit
	 * @Description: todo(edit页面前传入数据)
	 * @return string
	 * @author 杨希
	 * @date 2013-5-31 下午3:59:44
	 * @throws
	 */
	public function _before_edit() {
		//订单号可写
		$scnmodel = D('SystemConfigNumber');
		$writable= $scnmodel->GetWritable('mis_order_types');
		$this->assign("writable",$writable);
		$userModel=D("User");
		$userModel->getSelectUserList("MisWorkStatement",$_REQUEST['id'],"sysselectuser");
		$uerList=$userModel->getShowSelectUser("MisWorkStatement",$_REQUEST['id']);
		$this->assign("userList",$uerList);
		//获取附件信息
		$this->getAttachedRecordList($_REQUEST['id']);
	}
	/**
	 * @Title: _after_edit
	 * @Description: todo(edit页面后置函数传入数据)
	 * @return string
	 * @author 杨希
	 * @date 2013-5-31 下午3:59:44
	 * @throws
	 */
	public function _after_edit(&$vo){
// 		//查询当前查阅人
// 		$MisSystemSelectuserModel=M("mis_system_selectuser");
// 		$sUserMap=array();
// 		$sUserMap['modelname']="MisWorkStatement";
// 		$sUserMap['tableid']=$vo['id'];
// 		$sUserMap['fieldname']=$vo['id'];
		//查询报告类型
		$MisExecutionTypeDao=M("mis_order_types");
		$extypelist=$MisExecutionTypeDao->where('type = 67 and status = 1')->field('id,name')->select();
		$this->assign('extypelist',$extypelist);
		//默认报告标题
		$userModel = D("User");
		$username = $userModel->where('id = '. $vo['createid'])->getField('name');
		$this->assign('username', $username);
		if ($vo['lookpeople']) {
			$alookpeople = explode(",", $vo['lookpeople']);
			$alookpeoplename = explode(",", $vo['lookpeoplename']);
			$apeople = array();
			foreach ($alookpeople as $k => $v) {
				$apeople[] = array("userid"=>$v,"username"=>$alookpeoplename[$k]);
			}
		}
		$this->assign("apeople",$apeople);
	}
	/**
	 * @Title: _before_update
	 * @Description: todo(这里用一句话描述这个方法的作用)
	 * @author liminggang
	 * @date 2013-7-3 下午4:30:40
	 * @throws
	 */
	function _before_update(){
		// 撤回
		if($_POST['stepType'] == "2"){
			$MisWorkStatement = D("MisWorkStatement");
			$wsdata['updatetime'] = time();
			$wsdata['updateid'] = $_SESSION[C('USER_AUTH_KEY')];
			$wsdata['readstatus'] = 0;
			$wsdata['sendStatus'] = 0;
			$iftrue = $MisWorkStatement->where("id=".$_POST['id'])->data($wsdata)->save($wsdata);
			if($iftrue !== false){
				$MisWorkReadStatement = D("MisWorkReadStatement");
				$list = $MisWorkReadStatement->where("workid=".$_POST['id'])->delete();
				if($list !== false){
					$this->success ("当前工作报告撤回成功！");
				} else {
					$this->error("当前工作报告撤回失败！");
				}
			} else {
				$this->error("当前工作报告撤回失败！");
			}
			exit;
		}
// 		if(count($_POST['lookpeople'])<1){
// 			$this->error("请先选择发送人，再进行保存或提交！");
// 			exit;
// 		}
		$_POST['lookpeople'] = implode(",",	$_POST['lookpeople']);
		$_POST['lookpeoplename'] = implode(",",	$_POST['lookpeoplename']);
		$userModel = D("User");
		$username = $userModel->where('id = '. $_POST['createid'])->getField('name');
		//查询报告类型
		$MisExecutionTypeDao=M("mis_order_types");
		$extypename=$MisExecutionTypeDao->where('id = ' . $_POST['typeid'] . ' and status = 1')->getField('name');
		//$_POST['title'] = $_POST['createtime'] .'_'. $username .'_'. $extypename;
		$patterns = array('/年/','/月/','/日/');
		$replacements = array('-','-','');
		$createtime = preg_replace($patterns,$replacements,$_POST['createtime']);
		$_POST['createtime'] = strtotime($createtime);
		//$this->checkifexistcodeororder('mis_work_statement','code', 1);
		//工作报告ID
		$workid=$_POST['id'];
		//实例化对象
		$MisWorkReadStatementModel=D("MisWorkReadStatement");
		$MisWorkReadStatementModel->where('workid = '.$workid)->setField("createtime",$_POST['createtime']);
		//判断是否修改了阅读人或者抄送人
		if($_POST['stepType']){
			$_POST['sendStatus'] = 1;
			//将获得的数据分隔成数组
			$peoplearr=array_unique(explode(',',$_POST['lookpeople']));
			foreach($peoplearr as $key=>$val){
				$arr[]=array(
						'workid' =>$workid,
						'userid'=>$val,
						'copystep'=>1,
						'createtime'=>$_POST['createtime'],
						'createid'=>$_SESSION[C('USER_AUTH_KEY')]
				);
			}
			//实例化对象
			$MisWorkReadStatementModel=D("MisWorkReadStatement");
			$result=$MisWorkReadStatementModel->addAll($arr);
			if(!$result){
				$this->error(L("添加接收人失败！"));
			}
		}
	}
	/**
	 * @Title: _after_update
	 * @Description: todo(修改后置函数)
	 * @author renling
	 * @date 2013-7-4 上午11:13:20
	 * @throws
	 */
	public function _after_update(){
		if ($_POST['id']) {
			$UserModel=D("User");
			//插入数据
			if($_POST['sysselectuser']){
				$result=$UserModel->setSelectUser("MisWorkStatement",$_POST['id'],"sysselectuser");
				if(!$result){
					$this->error("添加知会人失败!");
				}
			}
			$this->swf_upload($_POST['id'],67);
		}
	}
	/**
	 * @Title: view
	 * @Description: todo(查看方法)
	 * @author liminggang
	 * @date 2013-7-4 上午10:27:17
	 * @throws
	 */
	public function view(){
		//获取当前ID
		$id=$_REQUEST['id'];
		//实例化对象
		$name=$this->getActionName();
		$MisWorkStatementModel=D($name);
		$list=$MisWorkStatementModel->where('id = '.$id)->find();
		$this->assign('vo',$list);

		//获取评论内容
		$MisWorkReadStatementModel=D("MisWorkReadStatement");
		$pglist=$MisWorkReadStatementModel->where('workid = '.$id.' and copystep = 1')->select();
		
		//获取附件信息
		$this->getAttachedRecordList($id,21);
		
		$this->assign('pglist',$pglist);
		$this->display();
	}
	/**
	 * @Title: lookupGetdate 
	 * @Description: todo(加载选中数据)   
	 * @author liminggang 
	 * @date 2013-7-3 上午11:07:34 
	 * @throws
	 */
	public function lookupGetdate(){
		$name = 'MisWorkStatement';
		$model = M('mis_work_statement');
		$map = array();
		$map['status'] = 1;
		$starttime = strtotime($_REQUEST['starttime']);
		$endtime = strtotime($_REQUEST['endtime']);
		/* if (! empty ( $name )) {
			$qx_name=$name;
			//验证浏览及权限
			if( !isset($_SESSION['a']) ){
				if( $_SESSION[strtolower($qx_name.'_'.ACTION_NAME)]!=1 ){
					if( $_SESSION[strtolower($qx_name.'_'.ACTION_NAME)]==2 ){////判断部门及子部门权限
						$map["createid"]=array("in",$_SESSION['user_dep_all_child']);
					}else if($_SESSION[strtolower($qx_name.'_'.ACTION_NAME)]==3){//判断部门权限
						$map["createid"]=array("in",$_SESSION['user_dep_all_self']);
					}else if($_SESSION[strtolower($qx_name.'_'.ACTION_NAME)]==4){//判断个人权限
						$map["createid"]=$_SESSION[C('USER_AUTH_KEY')];
					}
				}
			}
		} */
		$map["createid"]=$_SESSION[C('USER_AUTH_KEY')];
		$map['createtime'] = array("exp",">=".$starttime." and createtime <".$endtime);
		$list = $model->where($map)->field('id,createtime')->select();
		$aaa = $arr= array();
		foreach ($list as $key => $value) {
			$daytime = date("Y-m-d",$value['createtime']);
			$daytime = strtotime($daytime);
			if(!in_array($daytime,$aaa)){
				$start = transTime($value['createtime'],'Y-m-d H:i:s');
				$end = transTime($value['createtime']+1,'Y-m-d H:i:s');
				$arr[] = array(
						'id' => $value['id'],
						'title' =>"",
						'start' => $start,
						'end' => $end,
						'tdclassName'=>'pred',
				);
				array_push($aaa,$daytime);
			}
		}
		echo json_encode($arr);
	}
	/**
	 *
	 * @Title: lookupmanage
	 * @Description: todo(用ztree形式查询出所有部门员工信息。)
	 * @author liminggang
	 * @throws
	 */
	public function lookupmanage(){
		$step  = $_REQUEST['step'];
		$this->assign('step',$step);
		$model= M('mis_system_department');
		$deptlist = $model->where("status=1")->order("id asc")->select();
		$param['rel']="misworkstatementX";
		$param['url']="__URL__/lookupmanage/jump/1/deptid/#id#/parentid/#parentid#/step/".$step;
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
		$map['working']=1;
		$map['user.status']=1;
		$deptid		= $_REQUEST['deptid'];
		$parentid	= $_REQUEST['parentid'];
		if ($deptid && $parentid) {
			$deptlist =array_unique(array_filter (explode(",",$this->downAllChildren($deptlist,$deptid))));
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
}
?>