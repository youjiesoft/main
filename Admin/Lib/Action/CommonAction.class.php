<?php
/**
 * @Title: CommonAction
 * @Package package_name
 * @Description: todo(核心控制器)
 * @author everyone
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2011-4-7 下午5:13:20
 * @version V1.0
 */
class CommonAction extends CommonExtendAction {
	public $transaction_model='';
	//$_excludeAction非动态表单生成action,用于排除动态表单的新增修改数据检查异常
	public $_excludeAction = array('MisSalesMyProject');
	function _initialize() {
		$this->transaction_model=M();
		$this->transaction_model->startTrans();
		//过登录验证方法
		$func = array("saveOnlineEditWord","saveoffice","playSWF","curl_export_word","export_word_one","misFileManageDownload","guoqizhuanshou","SaveOnlineWord");
		if(!in_array(ACTION_NAME, $func)){
			//$this->SendTelmMsg("hi,good luck to you","13983475645");
			//随机数命中cookie改变
			$this->randNumExecFunc();
		}
		$_REQUEST=$this->escapeChar($_REQUEST);
		$_POST=$this->escapeChar($_POST);
		$_GET=$this->escapeChar($_GET);
		//extract($_REQUEST);
		//if($_REQUEST['ntdata']!=='') $_SESSION['navTab_data']=$_REQUEST['ntdata'];
	}
// 	public function lookupadvancedresult(){
// 		 $this->display();
		
// 	}

	/*
	 * @author  yangxi
	* @date:2015-02-11 15:00:00
	* @describe: 随机数满足时触发函数
	*/
	protected function randNumExecFunc($function='cookieTimeExtend',$funcdata=array(),$min=1, $max=10,$num=0) {
	
		if(is_int($min) && is_int($max) && is_int($num)){
			$randnum=sprintf("%".strlen($max)."d", mt_rand($min,$max));
			//echo $randnum;
			if($randnum>$num){
				//受核验的函数才能够被执行
				$funcArr=array("cookieTimeExtend");
				if(in_array($function,$funcArr)){
					switch($function){
						case 'cookieTimeExtend':
							$this->checkLogin();
							$this->checkRBAC();
							break;
						default:
							$name=$this->getActionName();
							$model = A ($name);
							if (method_exists($model,$function)) {
								call_user_func_array(array(&$model,$function),$funcdata);
							}
							break;	
					}
				}
			}else if($num>$randnum){
				$this->cookieTimeExtend();
		    }
		}
	}
	
	protected function cookieTimeExtend(){
		$userinfo = Cookie::get("userinfo");
		if($userinfo){
		    Cookie::set("userinfo",Cookie::get("userinfo"),C('COOKIE_EXPIRE'));
		}else{
			$this->checkLogin();
		}
	}

	protected function checkLogin(){
		$p=A("Public");
		if(!$p->checkLogin()){
			$arr=explode("/",$_SERVER['PATH_INFO']);
			if(strtolower($arr[1])=="index" || $arr[1]==""){
				redirect ( PHP_FILE . C ( 'USER_AUTH_GATEWAY' ) );
			}else{
				$this->assign("jumpUrl",__APP__.'/Public/login/');
				$this->error("核验登录超时",'','','301');
				exit;
			}
		}	
	}
	
	protected function checkRBAC(){
		// 用户权限检查
		if (C ( 'USER_AUTH_ON' ) && !in_array(MODULE_NAME,explode(',',D('User')->getConfNotAuth()))) {
			import ( '@.ORG.RBAC' );
			if (! RBAC::AccessDecision ()) {
				//检查认证识别号
				if (! $_SESSION [C ( 'USER_AUTH_KEY' )]) {
					$arr=explode("/",$_SERVER['PATH_INFO']);
					if($arr[1]=="Index" || $arr[1]==""){
						redirect ( PHP_FILE . C ( 'USER_AUTH_GATEWAY' ) );
					}else{
						$this->assign("jumpUrl",__APP__.'/Public/login/');
						$this->error("用户登录超时",'','','301');
						exit;
					}
				}
				// 没有权限 抛出错误
				if (C ( 'RBAC_ERROR_PAGE' )) {
					// 定义权限错误页面
					redirect ( C ( 'RBAC_ERROR_PAGE' ) );
				} else {
					if (C ( 'GUEST_AUTH_ON' )) {
						$this->assign ( 'jumpUrl', PHP_FILE . C ( 'USER_AUTH_GATEWAY' ) );
					}
					$nodemodel = M("node");
					$nodemap=array();
					$nodemap['status']=1;
					$nodemap['name']=MODULE_NAME;
					$mtitle=$nodemodel->where($nodemap)->getField("title");
					$nodemap['name']=ACTION_NAME;
					$atitle=$nodemodel->where($nodemap)->getField("title");
					// 提示错误信息
					$this->error ( $mtitle."_".$atitle." ".L ( '_VALID_ACCESS_' ) );
				}
			}else{
				//验证浏览及权限
				if( !isset($_SESSION['a']) && $_REQUEST['id'] ){
					if( $_SESSION[strtolower(MODULE_NAME.'_'.ACTION_NAME)]!=1 ){
						// 						$model=D(MODULE_NAME);
						// 						$map=array();
						// 						$id=$_REQUEST['id'];
						// 						if( intval($id) ){
						// 							$map['id']=$id;
						// 						}else if(is_array($id)){
						// 							if($id) {
						// 								$map['id'] = array("in",$id);
						// 							} else {
						// 								$map['id'] = -1;
						// 							}
						// 						}else if(is_string($id)){
						// 							$idarr=explode(",",$id);
						// 							if($idarr){
						// 								$map['id'] = array("in",$idarr);
						// 							} else {
						// 								$map['id'] = -1;
						// 							}
						// 						}
						// 						$creatidarr=$model->where($map)->getField("createid",true);
		
		
		
						// 						if( $_SESSION[strtolower(MODULE_NAME.'_'.ACTION_NAME)]==2 ){////判断部门及子部门权限
						// 							if(array_diff($creatidarr,$_SESSION['user_dep_all_child'])){
						// 								$this->error ( L ( '_VALID_ACCESS_' ) );
						// 							}
						// 						}else if($_SESSION[strtolower(MODULE_NAME.'_'.ACTION_NAME)]==3){//判断部门权限
						// 							if(array_diff($creatidarr,$_SESSION['user_dep_all_self'])){
						// 								$this->error ( L ( '_VALID_ACCESS_' ) );
						// 							}
						// 						}else if($_SESSION[strtolower(MODULE_NAME.'_'.ACTION_NAME)]==4){//判断个人权限
						// 							$currentuserdep[]=$_SESSION[C('USER_AUTH_KEY')];
						// 							if(array_diff($creatidarr,$currentuserdep)){
						// 								$this->error ( L ( '_VALID_ACCESS_' ) );
						// 							}
						// 						}
					}
				}
			}
		}		
	}
	public function index() {  
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		if($_REQUEST['fieldtype']){
			$this->getBindSetTables($map);
		}
		if($_REQUEST['projectid']){
			$map['projectid'] = $_REQUEST['projectid'];
		}
		if($_REQUEST['projectworkid']){
			//$map['projectworkid'] = $_REQUEST['projectworkid'];
		}
		$name = $this->getActionName();
		
		if (! empty ( $name )) {
			$qx_name=$name;
			if(substr($name, -4)=="View"){
				$qx_name = substr($name,0, -4);
			}
			//验证浏览及权限 
			if( !isset($_SESSION['a']) ){
				$map=D('User')->getAccessfilter($qx_name,$map);	
			}
			if(empty($map['companyid'])){
				unset($map['companyid']);
			}
			//列表页排序 ---开始-----2015-08-06 15:07 write by xyz
			if($_REQUEST['orderField']&&strpos(strtolower($_REQUEST['orderField']),' asc')===false&&strpos(strtolower($_REQUEST['orderField']),' desc')===false){
				$this->_list ( $name, $map);
			}else{
				$sortsorder = '';
				$sortsmap['modelname'] = $name;
				$sortsmap['sortsorder'] = array("gt",0);
				//管理员读公共设置
				if($_SESSION['a']){
					$listincModel = M("mis_system_public_listinc");
					$sortslist = $listincModel->where($sortsmap)->order("sortsorder")->select();
				}else{
					//个人先读个人设置、没有再读公共设置
					$sortsmap['userid'] = $_SESSION [C ( 'USER_AUTH_KEY' )];
					$listincModel = M("mis_system_private_listinc");
					$sortslist = $listincModel->where($sortsmap)->order("sortsorder")->select();
					if(empty($sortslist)){
						unset($sortsmap['userid']);
						$listincModel = M("mis_system_public_listinc");
						$sortslist = $listincModel->where($sortsmap)->order("sortsorder")->select();
					}
				}
				//如果在设置里有相关数据、提取排序字段组合order by
				if($sortslist){
					foreach($sortslist as $k=>$v){
						if(is_numeric($v['fieldname'])){//防止配置文件以索引数组构成找不到真实字段名
							$scdmodel = D('SystemConfigDetail');
							$detailList = $scdmodel->getDetail($name);
							$v['fieldname'] = $detailList[$v['fieldname']]['name'];
						}
						$sortsorder .= $v['fieldname'].' '.$v['sortstype'].',';
					}
					$sortsorder = substr($sortsorder,0,-1);
				}
				//列表页排序 ---结束-----			
				$this->_list ( $name, $map,'', false,'','',$sortsorder);
			}
			
			
		}
		
 		//begin
		$scdmodel = D('SystemConfigDetail');
		//读取列名称数据(按照规则，应该在index方法里面)
		$detailList = $scdmodel->getDetail($name,true,'','sortnum');
		if(file_exists(ROOT . '/Dynamicconf/Models/'.$name.'/form.inc.php')){
			$anameList = require ROOT . '/Dynamicconf/Models/'.$name.'/form.inc.php';
			if(!empty($detailList) && !empty($anameList)){
				foreach($detailList as $k => $v){
					$detailList[$k]["datatable"] = 'template_key=""';
					foreach($anameList as $kk => $vv){
						if($k==$kk){
							$detailList[$k]["datatable"] = $vv["datatable"];
						}
					}
				}
			}
		}
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
 		//扩展工具栏操作
		$toolbarextension = $scdmodel->getDetail($name,true,'toolbar','sortnum','shows',true);
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		//end
		if( intval($_POST['dwzloadhtml']) ){
			$this->display("dwzloadindex");exit;
		}
		//首页收件箱调用方法，为ajax调用
		if ($_GET['type'] == "ajaxcall") {
			$this->display ("ajax_index");exit;
		}
		if($_REQUEST['jump'] == "jump"){
			$this->display('indexview');exit;
		}
		$this->display ();
		return;
	}
	public function getBindSetTables(&$map,$type){
		//获取主模型
		$bindmodel=$_REQUEST['bindaname'];
		$name=$this->getActionName();
		//获取主数据id 
		$bindrdid=$_REQUEST['bindrdid'];
		$MisAutoBindSettableModel=D("MisAutoBindSettable");
		$bindanameModel=D($bindmodel);
		if($type){//组合表
			
		}else{//套表
			$bindMap=array();
			$bindMap['id']=$bindrdid;
			$bindVo=$bindanameModel->where($bindMap)->find();
			//查询绑定的附加条件
			$MisAtuoMap=array();
			$MisAtuoMap['bindaname']=$bindmodel;
			$MisAtuoMap['inbindaname']=$name;
			$MisAutoBindSettableVo=$MisAutoBindSettableModel->where($MisAtuoMap)->find();
			$bindconlistArr=unserialize($MisAutoBindSettableVo['bindconlistArr']);
			foreach ($bindconlistArr as $key=>$val){
				//如果有值才生成查询
				if($val){				
				 $map[$val]=$bindVo[$key];
				}
			}
			//查询当前表单自己组装的条件
			if($MisAutoBindSettableVo['inbindmap']){
				$newconditions = str_replace ( array ( '&quot;', '&#39;', '&lt;','&gt;'), array ('"',"'",'<','>'
				), $MisAutoBindSettableVo['inbindmap'] );
				 $map['_string']=$newconditions;
			}
		}
	}
	/**
	 +----------------------------------------------------------
	 * 取得操作成功后要返回的URL地址
	 * 默认返回当前模块的默认操作
	 * 可以在action控制器中重载
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 * @throws ThinkExecption
	 +----------------------------------------------------------
	 */
	function getReturnUrl() {
		return __URL__ . '?' . C ( 'VAR_MODULE' ) . '=' . MODULE_NAME . '&' . C ( 'VAR_ACTION' ) . '=' . C ( 'DEFAULT_ACTION' );
	}
	/**
	 * @Title: _search
	 * @Description: todo(根据表单生成查询条件并进行列表过滤)
	 * @param 数据对象名称 $name
	 * @param 检索配置文件名 $searchbyinc
	 * @return HashMap
	 * @author 杨东
	 * @date 2013-5-30 上午9:43:56
	 * @throws ThinkExecption
	 */
	protected function _searchs($name = '',$dmlist,$searchbyinc='',$detailList) {
		//生成查询条件
		if (empty ( $name )) {
			$name = $this->getActionName();
		}
		$this->assign('ename',$name);
		$this->assign('searchbyinc',$searchbyinc);
		//$model = D ( $name );
		$map = array ();
// 		if ($_SESSION["a"] != 1) $map['status']=array("egt",0);
		$quicksearchby = $_POST['quicksearchby'];
		// 如果检索条件存在就存入缓存
		$mrdmodel = D('MisRuntimeData');
		$runtimeCacheName = 'quicksearchby';//缓存名称
		if($searchbyinc) $runtimeCacheName = $searchbyinc;//如果有其他查询条件则更改缓存名称
		if($quicksearchby){
			$mrdmodel->setRuntimeCache($quicksearchby,$name,$runtimeCacheName);
		}
		$model = D('SystemConfigDetail');//系统配置文件模型
		if(!$detailList){
			$detailList = $model->getDetail($name,false,$searchbyinc,'sortnum');//获取searchby.inc.php文件内容
		}
		//如果默认选中对象不存在，就先取缓存中的选中对象
		if(!$quicksearchby){
			$quicksearchby = $mrdmodel->getRuntimeCache($name,$runtimeCacheName);
		}
		$bool = 0;
		if($dmlist){
			$bool = 1;
			foreach ($dmlist as $k => $v) {
				if ($v['refertable']) {
					$searchField = 'dd'.$v['id'].'.'.$v['referfield'];
					$name = 'dd'.$v['id'].$v['referfield'];
				} else {
					$searchField = 'd'.$v['id'].'.content';
					$name = 'd'.$v['id'].'content';
				}
				$detailList[] = array(
						'name' => $name,
						'searchField' => $searchField,
						'showname' => $v['name'],
						'type' => 'text',
						'issearch' => 1,
						'isallsearch' => 1,
						'status' => 1
				);
			}
		}
		$this->assign('bool',$bool); //高级检索配置维度检索
		// 不是高级检索
		if($_POST['search_flag'] != 1){
			if($quicksearchby){
				$where = array();// 全局检索的条件初始化
				foreach ($detailList as $k => $v) {
					// 判断是否为快捷查询条件
					if($v['issearch'] != '1' || $v['status'] < 0){
						unset($detailList[$k]);
						continue;
					}
					$search = $v['name'];// 默认检索条件
					// 当存在检索条件是启用检索条件
					if($v['searchField']){
						$search = $v['searchField'];
					}
					// 全局检索构造MAP
					if ($quicksearchby == 'quickall') {
						if($v['isallsearch']){
							if (!is_array($_POST['qkeysword'])) {
								$_POST['qkeysword'] = trim($_POST['qkeysword']);
							}
							$type = $v['type'];
							if(strpos($type, '|')){
								$type_tmp = explode('|', $type);
								$type = $type_tmp[0];
								if($type_tmp[1]){
									$search_field = $type_tmp[1];
								}
							}
							if($type == 'text' || $type == 'db'){
								if(isset($_POST['qkeysword'])){
									$this->setQuickMap($_POST['qkeysword'],$search,$v['type'],$where,$v['table'],$v['field'],true,$v['conditions']);
								}
							}
						}
						continue;
					}
					// 单个检索构造MAP
					if($quicksearchby == 'quick'.$v['name']){
						if (!is_array($_POST['quick'.$v['name']])) {
							$_POST['quick'.$v['name']] = trim($_POST['quick'.$v['name']]);
						}
						if(isset($_POST['quick'.$v['name']])){
							$this->setQuickMap($_POST['quick'.$v['name']],$search,$v['type'],$map,$v['table'],$v['field']);
						}
						break;
					}
				}
				// 全局检索设置为OR
				if($where){
					$where['_logic'] = 'or';
					$map['_complex'] = $where;
				}
			}
		} else {
			foreach ($_POST as $k => $v) {
				if($k == "qkeysword") unset($_POST[$k]);
				else if(strpos($k,"quick") === 0) {
					unset($_POST[$k]);
				}
			}
		}
		// 调用查询条件显示
		$this->showQuickSearch($quicksearchby,$detailList);
		return $map;
	}
	/**
	 * @Title: _search
	 * @Description: todo(根据表单生成查询条件并进行列表过滤)
	 * @param 数据对象名称 $name
	 * @param 检索配置文件名 $searchbyinc
	 * @return HashMap
	 * @author 杨东
	 * @date 2013-5-30 上午9:43:56
	 * @throws ThinkExecption
	 */
	protected function _search($name = '',$dmlist,$searchbyinc='') {
		//生成查询条件
		if (empty ( $name )) {
			$name = $this->getActionName();
		}
		$this->assign('ename',$name);
		$this->assign('searchbyinc',$searchbyinc);
		//$model = D ( $name );
		$map = array ();
// 		if ($_SESSION["a"] != 1) $map['status']=array("egt",0);
		$quicksearchby = $_POST['quicksearchby'];
		// 如果检索条件存在就存入缓存
		$mrdmodel = D('MisRuntimeData');
		$runtimeCacheName = 'quicksearchby';//缓存名称
		if($searchbyinc) $runtimeCacheName = $searchbyinc;//如果有其他查询条件则更改缓存名称
		if($quicksearchby){
			$mrdmodel->setRuntimeCache($quicksearchby,$name,$runtimeCacheName);
		}
		$model = D('SystemConfigDetail');//系统配置文件模型
		$detailList = $model->getDetail($name,false,$searchbyinc,'sortnum');//获取searchby.inc.php文件内容
		//如果默认选中对象不存在，就先取缓存中的选中对象
		if(!$quicksearchby){
			$quicksearchby = $mrdmodel->getRuntimeCache($name,$runtimeCacheName);
		}
		$bool = 0;
		if($dmlist){
			$bool = 1;
			foreach ($dmlist as $k => $v) {
				if ($v['refertable']) {
					$searchField = 'dd'.$v['id'].'.'.$v['referfield'];
					$name = 'dd'.$v['id'].$v['referfield'];
				} else {
					$searchField = 'd'.$v['id'].'.content';
					$name = 'd'.$v['id'].'content';
				}
				$detailList[] = array(
						'name' => $name,
						'searchField' => $searchField,
						'showname' => $v['name'],
						'type' => 'text',
						'issearch' => 1,
						'isallsearch' => 1,
						'status' => 1
				);
			}
		}
		$this->assign('bool',$bool); //高级检索配置维度检索
		// 不是高级检索
		if($_POST['search_flag'] != 1){
			if($quicksearchby){
				$where = array();// 全局检索的条件初始化
				foreach ($detailList as $k => $v) {
					// 判断是否为快捷查询条件
					if($v['issearch'] != '1' || $v['status'] < 0){
						unset($detailList[$k]);
						continue;
					}
					$search = $v['name'];// 默认检索条件
					// 当存在检索条件是启用检索条件
					if($v['searchField']){
						$search = $v['searchField'];
					}
					// 全局检索构造MAP
					if ($quicksearchby == 'quickall') {
						if($v['isallsearch']){
							if (!is_array($_POST['qkeysword'])) {
								$_POST['qkeysword'] = trim($_POST['qkeysword']);
							}
							$type = $v['type'];
							if(strpos($type, '|')){
								$type_tmp = explode('|', $type);
								$type = $type_tmp[0];
								if($type_tmp[1]){
									$search_field = $type_tmp[1];
								}
							}
							if($type == 'text' || $type == 'db'){
								if(isset($_POST['qkeysword'])){
									$this->setQuickMap($_POST['qkeysword'],$search,$v['type'],$where,$v['table'],$v['field'],true,$v['conditions']);
								}
							}
						}
						continue;
					}
					// 单个检索构造MAP
					if($quicksearchby == 'quick'.$v['name']){
						if (!is_array($_POST['quick'.$v['name']])) {
							$_POST['quick'.$v['name']] = trim($_POST['quick'.$v['name']]);
						}
						if(isset($_POST['quick'.$v['name']])){
							$this->setQuickMap($_POST['quick'.$v['name']],$search,$v['type'],$map,$v['table'],$v['field']);
						}
						break;
					}
				}
				// 全局检索设置为OR
				if($where){
					$where['_logic'] = 'or';
					$map['_complex'] = $where;
				}
			}
		} else {
			foreach ($_POST as $k => $v) {
				if($k == "qkeysword") unset($_POST[$k]);
				else if(strpos($k,"quick") === 0) {
					unset($_POST[$k]);
				}
			}
		}
		// 调用查询条件显示
		$this->showQuickSearch($quicksearchby,$detailList);
		return $map;
	}

	/**
	 * @Title: showQuickSearch
	 * @Description: todo(显示查询条件（配置文件里面读取）)
	 * @param 默认选中对象 $quicksearchby
	 * @author 杨东
	 * @date 2013-5-30 上午9:26:56
	 * @throws
	 */
	protected function showQuickSearch($quicksearchby,$detailList){
		$quicksearchbytrue = true;
		$quickSearchList = array();// 初始化全局检索
		$onSelectName = '';// 初始化第一次选中对象
		$showName = '';//初始化全局搜索默认显示名称
		foreach ($detailList as $k => $v) {
			// 判断是否为快捷查询条件
			if($v['issearch'] != '1' || $v['status'] < 0){
				unset($detailList[$k]);
				continue;
			}
			if($quicksearchby){
				if($quicksearchby == 'quick'.$v['name']){
					$quicksearchbytrue = false;
				}
			}
			// 设置第一个条件为默认选中对象
			if(!$onSelectName){
				$onSelectName = 'quick'.$v['name'];
			}
			// 获取当前查询对象的HTML值$v['type']
			$detailList[$k]['html'] = $this->quickSearchToHtml($v['type'],'quick'.$v['name'],$v['showname'],$v['table'],$v['field'],'');
			$detailList[$k]['quickname'] = 'quick'.$v['name'];
			// 判断是否为全局查询条件
			if($v['isallsearch']){
				$type = $v['type'];
				if(strpos($type, '|')){
					$type_tmp = explode('|', $type);
					$type = $type_tmp[0];
					if($type_tmp[1]){
						$search_field = $type_tmp[1];
					}
				}
				if($type == 'text' || $type == 'db'){
					if($showName != ''){
						$showName .= ',';
					}
					$showName .= $v['showname'];
				}
			}
		}
		//print_r($detailList);
		// 判断如果存在全局查询条件就构造全局查询条件
		if($showName){
			// 设置默认选中全局查询条件
			$onSelectName = 'quickall';
			$_POST['qkeysword'] = trim($_POST['qkeysword']);
			// 文本框默认显示内容
			$showName = "搜索".$showName;
			$html .= "<input type='text' class='quicksearch' title=".$showName." placeholder=".$showName." name='qkeysword' value='{$_POST['qkeysword']}' />";
			$quickSearchList['all'] = array(
					'name' => 'all',
					'quickname' => 'quickall',
					'showname' => '全部',
					'html' => $html
			);
		}
		if($quicksearchbytrue){
			$quicksearchby = "";
		}
		// 如果默认选中对象不存在就取初始化选中对象
		if($quicksearchby){
			$onSelectName = $quicksearchby;
		}
		$quickSearchList = array_merge($quickSearchList,$detailList);
		$this->assign('quickSearchList',$quickSearchList);
		$this->assign('quicksearchby',$onSelectName);
	}

	/**
	 * @Title: quickSearchToHtml
	 * @Description: todo(构造查询对象的HTML值（快捷搜索配置文件中的）)
	 * @param 类型 $type
	 * @param 英文标识 $name
	 * @param 匹配表 $table
	 * @param 匹配条件 $condition
	 * @return string
	 * @author 杨东
	 * @date 2013-5-30 上午9:36:12
	 * 添加快速搜索样式quicksearch@ nbmxkj
	 * @date 2014-07-02
	 * @throws
	 */
	protected function quickSearchToHtml($type, $name, $showname,$table, $field,$condition){
		if (!$field) {
			$field = 'id';
		}
		$html = '';
		//单条件查找 不需要的都隐藏
		if(strpos($type, ':')){
			$type_tmp = explode(':', $type);
			$type = $type_tmp[0].'2';
			$search_name = $type_tmp[1];
		}
		$search_field = 'name';
		if(strpos($type, '|')){
			$type_tmp = explode('|', $type);
			$type = $type_tmp[0];
			if($type_tmp[1]){
				$search_field = $type_tmp[1];
			}
		}
		if($name == $_POST['quicksearchby']){
			if(!is_array($_POST[$name])){
				$val = trim($_POST[$name]);
			} else {
				$val = $_POST[$name];
			}
		}
		$showName = "搜索".$showname;
		switch($type){
			case 'text':
				//$html .= "<label style='width:110px;;'>请输入查询关键字：</label>";
				$html .= "<input type='text' placeholder='".$showName."' name='$name' value='$val' class='quicksearch' />";
				break;
			case 'time':
				if($search_field == 'hm'){
					$format = "format={dateFmt:'yyyy-MM-dd HH:mm'}";
				} else if($search_field == 'hms'){
					$format = "format={dateFmt:'yyyy-MM-dd HH:mm:ss'}";
				} else if($search_field == 'h'){
					$format = "format={dateFmt:'yyyy-MM-dd HH'}";
				} else {
					$format = "format={dateFmt:'yyyy-MM-dd'}";
				}
				// 数组名称加[]
				//$html .= "<label style='width:110px;;'>请输入查询日期：</label>";
				$html .= "<div class='tml-input-date'><div class='tml-input-append'>";
				$html .= "<input type='text' class='time_input Wdate js-wdate textInput valid' ".$format." name='{$name}[0]' value='".$val[0]."'/>";
				$html .= "<a class='xyInputWithUnitButton inputDateButton js-inputCheckDate' href='javascript:;'>选择</a>";
				$html .= "</div>";
				$html .= "<span class='tml-form-text'>&nbsp;至&nbsp;</span>";
				$html .= "<div class='tml-input-append'>";
				$html .= "<input type='text' class='time_input quicksearch Wdate js-wdate textInput valid' ".$format." name='{$name}[1]' value='".$val[1]."'/>";
				$html .= "<a class='xyInputWithUnitButton inputDateButton js-inputCheckDate' href='javascript:;'>选择</a>";
				$html .= "</div></div>";
				break;
			case 'number':
				// 数组名称加[]
				//$html .= "<label style='width:110px;;'>请输入查询数量：</label>";
				$html .= "<input type='text' class='time_input textInput floatNone' name='{$name}[0]' size='14' value='".$val[0]."'/>";
				$html .= "<span class='xyInputTo'>至</span>";
				$html .= "<input type='text' class='time_input quicksearch textInput floatNone' name='{$name}[1]' size='14' value='".$val[1]."'/>";
				break;
			case 'group':
				// select 方式
				//$html .= "<label style='width:110px;;'>请选择查询类型：</label>";
				$html .= "<SELECT name='{$name}' class='select2 super_lookup_select2'>";
				$html .= "<option value=''>请选择".$showname."</option>";
				$group = D($table);
				$c = array();
				$c['status'] = 1;
				// 根据匹配条件过滤数据
				if($condition){
					// 过滤条件,检查是否为手写条件 add by nbmxkj@20150610 1804
					$condition = html_entity_decode($condition); // 将实体代码转换字符
					$checkConditionType = preg_match_all('/and|<|>|like|in|(BETWEEN\w+AND)|\!=/i',$condition , $subject, $matches);
					if($checkConditionType){
						$c['_string'] = $condition;
					}else{
						$conditions = explode(',', $condition);
						foreach($conditions as $c_v){
							$f = explode('=', $c_v);
							$c[$f[0]] = $f[1];
						}
					}
				}
				$list = $group->where($c)->select();
				foreach($list as $k => $v){
					if(isset($val) && $val == $v[$field]){
						$html .= "<option value='{$v[$field]}' selected>{$v[$search_field]}</option>";
					} else {
						$html .= "<option value='{$v[$field]}'>{$v[$search_field]}</option>";
					}
				}
				$html .= "</SELECT>";
				break;
			case 'checkfor':
				// checkfor 方式
				if($type_tmp[6]){
					$map = "map=".$type_tmp[6];
				}
				//$html .= "<label style='width:110px;;'>请输入查询关键字：</label>";
				$html .= "<span class='xyInputWithUnit'>";
				$html .= "<input class='quicksearch checkByInput textInput' name='check{$name}' value='{$_POST['check'.$name]}' {$map} show='{$type_tmp[2]}' insert='{$type_tmp[3]}' checkfor='{$type_tmp[1]}' limit='{$type_tmp[4]}' order='{$type_tmp[5]}' autocomplete='off'/>
				<input type='hidden' name='{$name}' value='{$val}'/>";
				$html .= "<span class='xyInputWithUnitButton inputCheckForButton'></span>";
				$html .= "</span>";
				break;
			case 'select':
				//$html .= "<label style='width:110px;;'>请选择查询类型：</label>";
				$html .= "<SELECT name='{$name}' class='select2 super_lookup_select2'>";
				$html .= "<option value=''>请选择".$showname."</option>";
				$select_config = require DConfig_PATH."/System/selectlist.inc.php";
				$select_config = $select_config[$search_field][$search_field];
				if($select_config){
					foreach($select_config as $select_k => $select_v){
						if(isset($val) && $val == $select_k){
							$html .= "<option value='{$select_k}' selected>{$select_v}</option>";
						} else {
							$html .= "<option value='{$select_k}' >{$select_v}</option>";
						}
					}
				}
				$html .= "</SELECT>";
				break;
			case 'radio':
				//$html .= "<label style='width:110px;;'>请选择查询类型：</label>";
				$select_config = require DConfig_PATH."/System/selectlist.inc.php";
				$select_config = $select_config[$search_field][$search_field];
				if($select_config){
					$html .= "<span>";
					foreach($select_config as $k => $v){
						if(isset($val) && $val === $k){
							$html .= "<input type='radio' name='{$name}' value='$k' checked='checked'/> $v ";
						} else {
							$html .= "<input type='radio' name='{$name}' value='$k' /> $v ";
						}
					}
					$html .= "</span>";
				}
				break;
			case 'checkbox':
				//$html .= "<label style='width:110px;;'>请选择查询类型：</label>";
				$select_config = require DConfig_PATH."/System/selectlist.inc.php";
				$select_config = $select_config[$search_field][$search_field];
				if($select_config){
					$html .= "<span>";
					foreach($select_config as $k => $v){
						if (in_array($k, $val)) {//.$name.'['.$k.']  $k
							$html .= "<input type='checkbox' name='".$name."[".$k."]' value='".$k."' checked='checked'/> $v ";
						} else {
							$html .= "<input type='checkbox' name='".$name."[".$k."]' value='".$k."'/> $v ";
						}
					}
					$html .= "</span>";
				}
				break;
			case 'db':
				//$html .= "<label style='width:110px;;'>请输入查询关键字：</label>";
				$html .= "<input type='text' placeholder='".$showName."' name='{$name}' value='{$val}'/>";
				break;
			default:
				//$html .= "<label style='width:110px;;'>请输入查询关键字：</label>";
				$html .= "<input type='text' placeholder='".$showName."' name='{$name}' value='{$val}'/>";
				break;
		}
		return $html;
	}

	/**
	 * @Title: setQuickMap
	 * @Description: todo(构造MAP检索条件)
	 * @param 检索关键字 $qkeysword
	 * @param 检索条件 $name
	 * @param 检索类型 $type
	 * @param map对象 $map
	 * @param 关联表 $table
	 * @param 关联字段 $field
	 * @param 全局检索 $all
	 * @param 关联条件 $conditions
	 * @author 杨东
	 * @date 2013-5-30 上午10:47:47
	 * @throws
	 */
	protected function setQuickMap($qkeysword,$name,$type,& $map,$table,$field,$all,$conditions){
		if (!$field) {
			$field = 'id';
		}
		$search_field = 'name';
		if(strpos($type, '|')){
			$type_tmp = explode('|', $type);
			$type = $type_tmp[0];
			if($type_tmp[1]){
				$search_field = $type_tmp[1];
			}
		}
		if (!isset($qkeysword) || $qkeysword == ''){
			return;
		}
		switch($type){
			case 'text':
				$map[$name] = array('like',"%".$qkeysword."%");
				break;
			case 'time':
				if($search_field == 'h'){
					$endtime = 3600;
				} else if($search_field == 'hm'){
					$endtime = 60;
				} else if($search_field == 'hms'){
					$endtime = 0;
				} else {
					$endtime = 86400;
				}
				$map1 = $map2 = array ();
				if ($qkeysword[0]) $map1 = array(array('egt',strtotime($qkeysword[0])));
				if ($qkeysword[1]) $map2 = array(array('elt',strtotime($qkeysword[1])+$endtime-1));
				if(!$map1 && !$map2) break;
				$map[$name] = array_merge($map1, $map2);
				break;
			case 'number':
				$map1 = $map2 = array ();
				if ($qkeysword[0]) $map1 = array(array('egt',$qkeysword[0]));
				if ($qkeysword[1]) $map2 = array(array('elt',$qkeysword[1]));
				if(!$map1 && !$map2) break;
				$map[$name] = array_merge($map1, $map2);
				break;
			case 'checkbox':
				if($qkeysword){
					$map[$name] = array('in',$qkeysword);
				} else {
					$map[$name] = "#$#@@@@%";
				}
				break;
			case 'checkfor':
				if($all){
					$m = M($table);
					$c['status'] = 1;
					$c[$type_tmp[2]] = array('like',"%".$qkeysword."%");
					$fieldarr = $m->where($c)->getField($field,true);
					if($fieldarr){
						$map[$name] = array('in',$fieldarr);
					}
				} else {
					$map[$name] = $qkeysword;
				}
				break;
			case 'group':
				if($all){
					$m = M($table);
					$c['status'] = 1;
					$c[$search_field] = array('like',"%".$qkeysword."%");
					$c['_string'] = $conditions;
					$fieldarr = $m->where($c)->getField($field,true);
					if($fieldarr){
						$map[$name] = array('in',$fieldarr);
					}
				} else {
					$map[$name] = $qkeysword;
				}
				break;
			case 'db':
				$idarr = array();
				if(strpos($table, ',')){
					$table_tmp = explode(',', $table);
					$search_field_tmp = explode(',', $search_field);
					$field_tmp = explode(',', $field);
					$true = true;
					foreach ($table_tmp as $key => $val) {
						$m = M($val);
						$c = array();
						$c['status'] = 1;
						if ($true) {
							$sf = $search_field_tmp[$key]?$search_field_tmp[$key]:'name';
							$c[$sf] = array('like',"%".$qkeysword."%");
							$true = false;
						} else {
							$sf = $search_field_tmp[$key]?$search_field_tmp[$key]:'name';
							if($idarr){
								$idarr = implode(",", $idarr);
								if(strpos($idarr, ',')){
									$c[$sf] = array('in',$idarr);
									$c["_string"] = " id>0 ";
								} else {
									$c[$sf] = array('eq',$idarr);
								}
							} else {
								$c[$sf] = "###$@$$$";
							}
						}
						$f = $field_tmp[$key]?$field_tmp[$key]:'id';
						$idarr = $m->where($c)->getField($f,true);
					}
				} else {
					$m = M($table);
					$c = array();
					$c['status'] = 1;
					$c[$search_field] = array('like',"%".$qkeysword."%");
					$idarr = $m->where($c)->getField($field,true);
				}
				if($all && !$idarr){
					break;
				}
				if($idarr){
					$idarr = implode(",", $idarr);
					if(strpos($idarr, ',')){
						$map[$name] = array('in',$idarr);
					} else {
						$map[$name] = array('eq',$idarr);
					}
				} else {
					$map[$name] = "#$#@@@@%";
				}
				break;
			default:
				// radio select radio
				$map[$name] = $qkeysword;
				break;
		}
	}
	/**
	 * @Title: setAdvancedMap
	 * @Description: todo(构造高级检索map)
	 * @param map对象 $map
	 * @author 杨东
	 * @date 2013-6-8 上午10:43:28
	 * @throws
	 */
	protected function setAdvancedMap(&$map,$subdetail,$dolist){
		$name = $_POST['model'];
		if(!$name) $name = $this->getActionName();
		$model = D('SystemConfigDetail');//系统配置文件模型
		$wheresql = array();
		if ($subdetail) {
			$detailList = $model->getSubDetail($name,false,'','searchsortnum');//获取searchby.inc.php文件内容
			foreach ($dolist as $k => $v) {
				if ($v['refertable']) {
					$searchField = 'dd'.$v['id'].'.'.$v['referfield'];
					$name = 'dd'.$v['id'].$v['referfield'];
				} else {
					$searchField = 'd'.$v['id'].'.content';
					$name = 'd'.$v['id'].'content';
				}
				$detailList[] = array(
						'name' => $name,
						'searchField' => $searchField,
						'showname' => $v['name'],
						'type' => 'text',
						'issearch' => 1,
						'isallsearch' => 1,
						'status' => 1
					
				);
			}
		} else {
			if($_REQUEST['viewname']){//读取当前模型下的视图配置文件
				$path=DConfig_PATH . "/Models/".$_POST['model']."/".$_POST['viewname'].".inc.php";
				$detailList = require $path;
			}else{
				$detailList = $model->getDetail($name,false,$searchbyinc,'searchsortnum');//获取searchby.inc.php文件内容
			}
			$searchbyinc = $_POST['searchbyinc'];
		}
		foreach ($detailList as $k => $v) {
			if($v['issearch'] != '1' || $v['status'] < 0){
				continue;
			}
			$table = $v['table'];
			$type = $v['type'];
			$field = $v['field'];
			if (!$field) {
				$field = 'id';
			}
			$search_field = 'name';
			if(strpos($type, '|')){
				$type_tmp = explode('|', $type);
				$type = $type_tmp[0];
				if($type_tmp[1]){
					$search_field = $type_tmp[1];
				}
			}
			if (!is_array($_POST['advanced'.$v['name']])) {
				$_POST['advanced'.$v['name']] = trim($_POST['advanced'.$v['name']]);
			}
			if (isset($_POST['advanced'.$v['name']]) && $_POST['advanced'.$v['name']] != '') {
				$qkeysword = $_POST['advanced'.$v['name']];
			} else {
				continue;
			}
			$search = $v['name'];// 默认检索条件
			// 当存在检索条件是启用检索条件
			if($v['searchField']){
				$search = $v['searchField'];
			}
			switch($type){
				case 'text':
					$wheresql[] = $search." like '%".$qkeysword."%' ";
					break;
				case 'time':
					if($search_field == 'h'){
						$endtime = 3600-1;
					} else if($search_field == 'hm'){
						$endtime = 60-1;
					} else if($search_field == 'hms'){
						$endtime = 0;
					} else {
						$endtime = 86400-1;
					}
					$map1 = array ();
					if ($qkeysword[0]) {
						$start = strtotime($qkeysword[0]);
						$map1[] = $search." >= ".$start;
					}
					if ($qkeysword[1]) {
						$endtime = strtotime($qkeysword[1])+$endtime;
						$map1[] = $search." <= ".$endtime;
					}
					if(!$map1) break;
					$wheresql[] = implode(" and ", $map1);
					break;
				case 'number':
					$map1 = array ();
					if ($qkeysword[0]) $map1[] = $search." >= ".$qkeysword[0];
					if ($qkeysword[1]) $map1[] = $search." <= ".$qkeysword[1];
					if(!$map1) break;
					$wheresql[] = implode(" and ", $map1);
					break;
				case 'checkbox':
					if($qkeysword) {
						$this->arrayMap($qkeysword);
						$qkeysword = implode(',', $qkeysword);
						$wheresql[] = $search." IN (".$qkeysword.")";
					}
					break;
				case 'checkfor':
					$wheresql[] = $search." = ".$qkeysword;
					break;
				case 'group':
					$wheresql[] = $search." = '".$qkeysword."'";
					break;
				case 'db':
					$idarr = array();
					if(strpos($table, ',')){
						$table_tmp = explode(',', $table);
						$search_field_tmp = explode(',', $search_field);
						$field_tmp = explode(',', $field);
						$true = true;
						foreach ($table_tmp as $key => $val) {
							$m = M($val);
							$c = array();
							$c['status'] = 1;
							if ($true) {
								$sf = $search_field_tmp[$key]?$search_field_tmp[$key]:'name';
								$c[$sf] = array('like',"%".$qkeysword."%");
								$true = false;
							} else {
								$sf = $search_field_tmp[$key]?$search_field_tmp[$key]:'name';
								if($idarr){
									if(strpos($idarr, ',')){
										$c[$sf] = array('in',$idarr);
										$c["_string"] = " id>0 ";
									} else {
										$c[$sf] = array('eq',$idarr);
									}
								} else {
									$c[$sf] = "###$@$$$";
								}
							}
							$f = $field_tmp[$key]?$field_tmp[$key]:'id';
							$idarr = $m->where($c)->getField($f,true);
						}
					} else {
						$m = M($table);
						$c = array();
						$c['status'] = 1;
						$c[$search_field] = array('like',"%".$qkeysword."%");
						$idarr = $m->where($c)->getField($field,true);
					}
					if($idarr){
						$this->arrayMap($idarr);
						$idarr = implode(',', $idarr);
						if($idarr){
							$wheresql[] = $search." IN (".$idarr.")";
						} else {
							$wheresql[] = $search." = '#$$@#$@'";
						}
					} else {
						$wheresql[] = $search." = -3 ";
					}
					break;
				default:
					// radio select radio
					$wheresql[] = $search." = '".$qkeysword."'";
					break;
			}
		}
		$sql = "";
		if(isset($map['_string'])){
			$sql = $map['_string'];
		}
		if ($wheresql) {
			if ($sql) {
				$map['_string'] = "(".$sql.") and ".implode(" and ", $wheresql);
			} else {
				$map['_string'] = implode(" and ", $wheresql);
			}
		}
	}
	/**
	 * @Title: arrayMap
	 * @Description: todo(作用于格式化数组（检索IN所用）)
	 * @param 数组 $arr
	 * @author 杨东
	 * @date 2013-6-8 上午10:25:07
	 * @throws
	 */
	protected function arrayMap(&$arr){
		foreach ($arr as $k => $v) {
			$arr[$k] = "'$v'";
		}
	}
	protected function advanced_search(){
		$this->display();
	}
	/*
	 * 委托传参
	 */
	protected function _list_delegate(){
		$this->_list($name, $map, $sortBy = '', $asc = false,$group='',$echoSql='0');
		
	}
	protected function _viewlist($name,$map,$sortBy = '', $asc = false,$group='',$echoSql='',$sortstr=''){
		$model=D($name);
		//视图信息
		$sysviewmap=array();
		$sysviewmap['name']=$name;
		$viewInfo=D("MisSystemDataviewMas")->where($sysviewmap)->find();
		if($viewInfo===false){
			$this->error("查询系统视图数据存在错误，请检查系统视图关联配置。sql:".D("MisSystemDataviewMas")->getLastSql());
		}
		/* ***************** 修改 ***************** */
		if($_POST['search_flag'] == 1){
			$this->setAdvancedMap($sysviewmap);
		}
		if(!$viewInfo['orderDirection']){
			$viewInfo['orderDirection']="desc";
		}
		if($viewInfo['ordrnocondition']){
			$order = $viewInfo['ordrnocondition'].' '.$viewInfo['orderDirection'];
		}else{
			$order = '';
		}		
		if($sortstr){
			$order = $sortstr;
		}
		//$map['status']=1;
		$object = $model->where($map)->order($order)->group($viewInfo['gruopcondition'])->query($viewInfo['spellwheresql'],true);
		if($object===false){
			$this->error("查询系统视图错误。".$model->getDBError()." sql:".$model->getLastSql());
		}
		$count=count($object);
		if(count($object)>0){
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
// 			$sql=$viewInfo['spellsql']." LIMIT {$p->firstRow},{$p->listRows}";
// 			$voList = $model->query($sql);
			$voList =$model->where($map)->order($order)->group($viewInfo['groupcondition'])->limit($p->firstRow . ',' . $p->listRows)->query($viewInfo['spellwheresql'],true);
			//echo $model->getlastsql();
			// 处理lookup数据 by杨东
			if($_POST['dealLookupList']==1){
				$this->dealLookupDelegate($voList,$name,$_POST['dealLookupType'],$_POST['viewname']);
			}
			$page = $p->show ();
			//列表排序显示
			$sortImg = $sort; //排序图标
			$sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
			$sort = $sort == 'desc' ? 'desc' : 'asc'; //排序方式
			$pageNum= !empty($_REQUEST[C('VAR_PAGE')])?$_REQUEST[C('VAR_PAGE')]:1;
			//模板赋值显示
			$this->assign ( 'pageNum', $pageNum );
			$this->assign ( 'list', $voList );
			$this->assign ( "page", $page );
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
		}else{
			// 处理lookup数据 by杨东
			if($_POST['dealLookupList']==1){
				$this->dealLookupDelegate($voList,$name,$_POST['dealLookupType'],$_POST['viewname']);
			}
		}
	}
	/**
	 +----------------------------------------------------------
	 * 根据表单生成查询条件
	 * 进行列表过滤
	 +----------------------------------------------------------
	 * @access protected
	 +----------------------------------------------------------
	 * @param Model $model 数据对象
	 * @param HashMap $map 过滤条件
	 * @param string $sortBy 排序
	 * @param boolean $asc 是否正序
	 * @param mothed $mothed 针对_list查询完后对返回的数组进行再次处理，所以需要传入一个处理方法名。
	 * @param module $modules 如果当前传入的$name 是视图的话，是无法用method_exists方法，所以需要多传入一个当前模型名字。
	 * @param echoSql  当等1时， 输出记算行数的sql语名；
	 * @param $sorttype  字符串排序（多字段时使用） (exec: parentid desc,id asc) 2015-8-3 14:15 Write By xyz
	 +----------------------------------------------------------
	 * @return void
	 +----------------------------------------------------------
	 * @throws ThinkExecption
	 +----------------------------------------------------------
	 */
	protected function _list($name, $map, $sortBy = '', $asc = false,$group='',$echoSql='',$sortstr='',$limit=true) {
		import ( '@.ORG.Browse' );
		//map附加
		//提醒条件
		if($_REQUEST['remindMap']){
			$remindMap=base64_decode($_REQUEST['remindMap']);
			if($map['_string']){			
				$map['_string'].=" and ".$remindMap;
			}else{
				$map['_string']=$remindMap;
			}
			$this->assign("remindMap",$_REQUEST ['remindMap']);
		}
		//权限验证条件
		$getactionname = $this->getActionName();
		$alicname = array("MisSalesMyProject","MisSystemClientChangeRole");
		if ($_SESSION ['a'] != 1&&($name==$this->getActionName() || in_array($getactionname,$alicname))) {
			//当遇到项目项目模型查询数据权限的时候，进行转移到项目动态信息
			$newname = $this->getActionName()=="MisSalesMyProject" ?"MisAutoPvb":$name;
			$broMap = Browse::getUserMap ($newname );
			//添加商机特殊数据权限过滤
			if($name=="MisSaleMyBusiness"){
				if(!$_POST['isbrows']){
					unset($broMap);
				}
			}
			//echo 111;
			if($_REQUEST['projectid'] && $_REQUEST['projectworkid']){
				$map['projectid']= $_REQUEST['projectid'];
			}
			//将以前所有的$map置前，以免和转授权起冲突--放于$broMap判断之前
			if ($broMap) {
				if($map['_string']){
					if(is_string($broMap) !== false){
						$map['_string'] .= " and " . $broMap;  //自身权限
					}else if(is_array($broMap)){
						if($broMap[0]){
							$map['_string'] .= " and " . $broMap[0];//自身权限
						}
						if($broMap[1]){ //转授权限
							$map["_logic"] = "and";
							$m['_complex'] = $map;
							$m['_string'] = $broMap[1];
							$m['_logic'] = 'or';
							$map = $m;
						}						
					}					
				}else{
					if(is_string($broMap) !== false){
						$map['_string'] .=  $broMap;//自身权限
					}else if(is_array($broMap)){
						if($broMap[0]){
							$map['_string'] .= $broMap[0];//自身权限
						}
						if($broMap[1]){//转授权限
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
		
		$model = D($name);
		// 视图对象的排序
		$viewSign = substr ($name, -4 );
		if($viewSign=='View'){
			$viewTables=array_keys($model->viewFields);
			$viewTable=$viewTables[0];
			$viewOrderBy=$model->viewFields[$viewTable][0];
			$order = $viewTable.".".$viewOrderBy;
		}
		//排序字段 默认为主键名
		else if (isset ( $_REQUEST ['orderField'] ) && $_REQUEST ['orderField']) {
			$order = $_REQUEST ['orderField'];			
			//$order="`" .$order."`" ;
		} else {
			$order = ! empty ( $sortBy ) ? $sortBy : $model->getPk ();
			//$order="`" .$order."`" ;
		}
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['orderDirection'] )) {
			if(false === strpos(strtolower($_REQUEST['orderField']),' asc')&&false === strpos(strtolower($_REQUEST['orderField']),' desc')){
				$sort = $_REQUEST ['orderDirection'];
			}else{
				$sort = '';
			}
			
		} else {
			$ss = strtolower(C("SYSTEM_SORT"));
			$ssu = $ss == 'asc'?'desc':'asc';
			$sort = $asc ? $ssu : $ss;
		}		//特殊处理 字符串排序参数 write by xyz
		if($sortstr){
			$order = $sortstr;
			$sort = '';
		}
		/* ***************** 修改 ***************** */
		if($_POST['search_flag'] == 1){
			$this->setAdvancedMap($map);
		}
		// '*'
		$count = $model->where ( $map )->count ( '*' ); 
		$htmls.=<<<EOF
	<script>
	console.log("list查询:{$model->getLastSql()}");
	</script>
EOF;
// 			echo $htmls;
		//print_r($map);
		if($group){
			$count = $model->group($group)->where ( $map )->getField( 'id',true );
			$count = count($count);
		}
		if($echoSql=='count' && $_SESSION['a']==1){
			echo $model->getLastSql();
		}
				//传参开启调式 eagle
		/* ***************** 修改 ***************** */
		//不存在则遍历一遍重新拼装$map来处理视图类型数据
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
            //查数据，导出不控制分页
			if($_POST['export_bysearch']==1){//如果是导出则无分页
				if($group){
					$voList = $model->group($group)->where($map)->order($order." ". $sort)->select();
				}else{
					$voList = $model->where($map)->order( $order." ". $sort)->select();
				}
			}else{
				$limitval = $limit?$p->firstRow . ',' . $p->listRows:'';
				if($group){					
					$voList = $model->group($group)->where($map)->order( $order." ".$sort)->limit($limitval)->select();
				}else{
					$voList = $model->where($map)->order(  $order." ".$sort)->limit($limitval)->select();
				}
			}
			logs($model->getLastSql(),'listSql');
			$htmls.=<<<EOF
				<script>
				console.log("查询语句:{$model->getLastSql()}");
				</script>
EOF;
// 			 echo $htmls;
// 			dump($voList);
//			echo $model->getLastSql();
			if($echoSql=='list' && $_SESSION['a']==1){
				echo $model->getLastSql();
			}
			// 处理lookup数据 by杨东
			if($_POST['dealLookupList']==1){
				$this->dealLookupDelegate($voList,$name,$_POST['dealLookupType'],$_POST['viewname']);
			}

			//给每条数据分配该有的toolbar操作按钮
			$this->setToolBorInVolist($voList);
			// end
			$module=A($this->getActionName());
			if (method_exists($module,"_after_list")) {
				call_user_func(array(&$module,"_after_list"),&$voList);
			}
			//如果是导出直接输出到excel
			if($_POST['export_bysearch']==1){
				$this->exportBysearch($voList,$name);
			}
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
			$this->assign ( 'list', $voList );
			$this->assign ( "page", $page );
		}else{
			if($_POST['export_bysearch']==1){
				$this->exportBysearch(array());
			}
			// 处理lookup数据 当数据库无数据时，也需调用一次dealLookupDelegate，才能得到detaillist
			if($_POST['dealLookupList']==1){
				$voList = array();
				$this->dealLookupDelegate($voList,$name,$_POST['dealLookupType'],$_POST['viewname']);
			}
				
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
		return;
	}

	/**
	 * @Title: _subsearch
	 * @Description: todo(明细检索)
	 * @param 模型名称 $name
	 * @param 维度 $dolist
	 * @return multitype:|multitype:multitype:string
	 * @author 杨东
	 * @date 2013-6-26 上午10:52:45
	 * @throws
	 */
	protected function _subsearch($name="",$dolist){
		$map = array();
		//生成查询条件
		if (empty ( $name )) {
			$name = $this->getActionName();
		}
		if (!$name) {
			return $map;
		}
		$quicksearchby = $_POST['quicksearchby'];
		$mrdmodel = D('MisRuntimeData');
		// 如果检索条件存在就存入缓存
		if($quicksearchby){
			$mrdmodel->setRuntimeCache($quicksearchby,$name,'subquicksearchby');
		}
		$model = D('SystemConfigDetail');//系统配置文件模型
		$detailList = $model->getSubDetail($name,false,'','sortnum');//获取searchby.inc.php文件内容
		foreach ($dolist as $k => $v) {
			if ($v['refertable']) {
				$searchField = 'dd'.$v['id'].'.'.$v['referfield'];
				$name = 'dd'.$v['id'].$v['referfield'];
			} else {
				$searchField = 'd'.$v['id'].'.content';
				$name = 'd'.$v['id'].'content';
			}
			$detailList[] = array(
					'name' => $name,
					'searchField' => $searchField,
					'showname' => $v['name'],
					'type' => 'text',
					'issearch' => 1,
					'isallsearch' => 1,
					'status' => 1
			);
		}
		//如果默认选中对象不存在，就先取缓存中的选中对象
		if(!$quicksearchby){
			$quicksearchby = $mrdmodel->getRuntimeCache($name,'subquicksearchby');
		}
		$this->showQuickSearch($quicksearchby,$detailList);
		if($quicksearchby){
			$where = array();// 全局检索的条件初始化
			foreach ($detailList as $k => $v) {
				// 判断是否为快捷查询条件
				if($v['issearch'] != '1' || $v['status'] < 0){
					unset($detailList[$k]);
					continue;
				}
				$search = $v['name'];// 默认检索条件
				// 当存在检索条件是启用检索条件
				if($v['searchField']){ 
					$search = $v['searchField'];
				}
				// 全局检索构造MAP
				if ($quicksearchby == 'quickall') {
					if($v['isallsearch']){
						if (!is_array($_POST['qkeysword'])) {
							$_POST['qkeysword'] = trim($_POST['qkeysword']);
						}
						$type = $v['type'];
						if(strpos($type, '|')){
							$type_tmp = explode('|', $type);
							$type = $type_tmp[0];
							if($type_tmp[1]){
								$search_field = $type_tmp[1];
							}
						}
						if($type == 'text' || $type == 'db'){
							if(isset($_POST['qkeysword'])){
								$this->setQuickMap($_POST['qkeysword'],$search,$v['type'],$where,$v['table'],$v['field'],true,$v['conditions']);
							}
						}
					}
					continue;
				}
				// 单个检索构造MAP
				if($quicksearchby == 'quick'.$v['name']){
					if (!is_array($_POST['quick'.$v['name']])) {
						$_POST['quick'.$v['name']] = trim($_POST['quick'.$v['name']]);
					}
					if(isset($_POST['quick'.$v['name']])){
						$this->setQuickMap($_POST['quick'.$v['name']],$search,$v['type'],$map,$v['table'],$v['field']);
					}
					break;
				}
			}
			// 全局检索设置为OR
			if($where){
				$where['_logic'] = 'or';
				$map['_complex'] = $where;
			}
		}
		return $map;
	}

	function insert($isrelation=false) {
		//确认终审时间
		if($_POST['operateid']==1){
			$_POST ['alreadyauditnode'] = time();  //单据状态审批完成单据终审时间
		}
		//end
		$name=$this->getActionName();
		$model = D ($name);
		if (false === $model->create ()) {
			if(!$isrelation){
				$this->error ( $model->getError () );
			}else{
				throw new NullPointExcetion($model->getError () . ' 出错表单为: ' .$name);
			}
		}
		//保存当前数据对象
		try {
			$list=$model->add ();
            logs($model->getLastSql() , $name.'_add_sql_'.date('Y-m-d-H' , time()) ,'',__CLASS__,__FUNCTION__,__METHOD__);
            logs($model->getDBError() , $name.'_add_sql[ERROR]_'.date('Y-m-d-H' , time()) ,'',__CLASS__,__FUNCTION__,__METHOD__);
		}catch (Exception $e){
			$this->error($e->__toString());
		}
		$msg['ret'] = $list;
		$msg['err'] = $model->getDberror();
		$msg['sql'] = $model->getlastsql();
		if ($list!==false) {
			//子流程数组反写主流程绑定ID @liminggang 2015-03-10
			if ($_POST['auditFlowTuiTablename'] && $_POST['auditFlowTuiTableid'] && $_POST['auditZhuLicModel'] && $_POST['auditZhuLicId']) {
				/*
				 * 转子流程因为支持一对一、一对多
				 * 升级日期 2015-11-20
				 */
				$map = array();
				$map['tablename'] = $_POST['auditZhuLicModel'];
				$map['tableid'] = $_POST['auditZhuLicId'];
				$map['isauditmodel'] = $name;
				$map['doing'] = 1;
				$process_relation_formDao = M("process_relation_form");
				//查询当前审批节点表数据
				$relation_formlist = $process_relation_formDao->where($map)->field("id,flowtype,parallel")->find();
				if($relation_formlist && $relation_formlist['flowtype'] == 3){
					//转子流程专用表
					$process_relation_childrenDao = M("process_relation_children");
					$data = array();
					$data['relation_formid'] = $relation_formlist['id'];
					$data['tablename'] = $_POST['auditZhuLicModel'];
					$data['tableid'] = $_POST['auditZhuLicId'];
					$data['zitableid'] = $list;
					$data['zitablename'] = $name;
					//存在值，并且是子流程
					if($relation_formlist['parallel'] == 0){
						//串行，有且只应该一条转子流程
						$childrebool = $process_relation_childrenDao->where($data)->find();
						if($childrebool){
							if(!$isrelation){
								$this->error("已经转过子流程任务了，无需再转子流程");
							}else{
								exit;
							}
						}
					}
					$data['createid'] = $_SESSION[C('USER_AUTH_KEY')];
					$process_relation_childrenDao->add($data);
					$process_relation_formDao->where("id = {$relation_formlist['id']}")->setField("isaudittableid",$list);
				}
				//end
			}
			//上传附件信息
			$this->swf_upload($list,0,null,$_POST['projectid'],$_POST['projectworkid']);
			// 地区信息处理@nbmxkj - 20141030 1603
			$this->area_info($list);
			$this->map_info($list);
			/*
			 * 处理插入成功后，处理数据的后置函数
			 */
			$module2=A($name);
			if (method_exists($module2,"_after_insert")) {
				call_user_func(array(&$module2,"_after_insert"),$list);
			}
			/*
			 * _over_insert方法为静默插入生单。
			 */
			if (method_exists($module2,"_over_insert")) {
				//这里用来存储临时缓存文件
				$updateBackup=$this->setOldDataToCache($model,$name,$list,'insert');
				//模型对象名与插入的id值
				$paramate=array($name,$list,$updateBackup);
				call_user_func_array(array(&$module2,"_over_insert"),$paramate);
			}
			/*
			 * startprocess,在页面是不存在。此参数是在启动流程startprocess方法中默认赋值的，
			 * 为了关闭insert的成功success输出,此参数在update方法中也是同样的效果
			 * 此参数不能再出现其他作用，否则会出现参数多用数据判断出错
			 * @author:liminggang
			 */
			$startprocess = $_POST ['startprocess'];
			/*
			 * operateid 确认提交标记
			 */
			if($_POST['operateid']==1){
				//这里清除内存表缓存的修改前数据代码
				$this->unsetOldDataToCache($updateBackup);
				
				//表单终审后，进行 任务，或者子流程处理
				$this->_before_overProcess($list);
				
				/*
				 * __coverformoperateid__ : mian/children
				 * 套表时特有属性，nbmxkj@20150625 1516
				 */
				if(!$_POST['__coverformoperateid__']){
					//先关闭版控生成
					$saveList=$this->SaveVersionWord($list,$name);
				}
				//调用此单据是否带提示
				$this->sysremind($name,$list);
			}
			if ($startprocess != 1) {
				$this->lookupdataInteractionPrepareData($list);
				if(!$isrelation){
					try{
						// 排除一些非动态表单生成的 action
						if(!in_array($name, $this->_excludeAction)){
							//当前表单是否是组合或组从主表
							$isAction=checkActionIsMain($name);  
							if( $isAction !=false && $isAction['type'] != 2 ){
								//调用添加从表函数
// 								$lastID = $model->query('SELECT LAST_INSERT_ID() as returnval');
// 								$list = $lastID[0]['returnval'];
								$this->getCombinationTableAndSlaveTableSpecialCode( $name , $list,$isAction['type']  , $isrelation );
							}
						}
						$this->success ( "表单数据保存成功！" ,'',$list);
					}catch (Exception $e){
						$this->error($e->__toString());
					}
					
				}else{
					return $list;
				}
				exit;
			}else{
				$_POST ['id'] = $list;
			}
		} else {
			if(!$isrelation){
				$this->error ( L('_ERROR_') );
			}else{
				throw new NullPointExcetion( $model->getDberror().L('_ERROR_'));
			}
		}
	}
	public function  _over_insert($modelname,$id,$updateBackup){
			//获取主模型名称
			$targetname=$this->getActionName();
			//加入数据控制
			$this->dataControl($modelname,'1',$id,$targetname,1);
			//数据漫游,新增自动插入
			$this->dataRoam(1,$modelname,$id,1,$updateBackup);
			//加入数据控制
			$this->dataControl($modelname,'1',$id,$targetname,2);
			//加入提醒
			$this->dataRemind($modelname,'1',$id,$targetname);
		//$this->error ('错误中断');
	}
	/*
	 *自动编码重复解决
	 * @author  wangcheng
	 * @date:2012-11-1
	 * @param sting $table 表名
	 * @param sting $field 字段名
	 * @param int   $update 是否是更新（1,0
	 */
	public function checkifexistcodeororder($tableName, $field='orderno', $modelName='', $update=0,$typefield){
		$modelName = empty($modelName) ? $this->getActionName() : $modelName;
		$condition = array($field=>array('eq', $_POST[$field]));
		if($update)	{
			$condition['id'] = array('neq', $_POST['id']);
			$status="修改时";
		}else{
			$status="新增时";
		}

		$tablemodel = D($modelName);
		$list = $tablemodel->where($condition)->getField('id');
		//print_r($tablemodel->getLastSql());
		if($list){
			if($typefield){
				$fieldval=$_POST[$typefield];
				//递归检查
				$model	= D('SystemConfigNumber');
				$newOrderNoInfo=$model->getOrderno($tableName,$modelName);
				$newOrderNo["setFormFiledVal"] = $newOrderNoInfo;
				$newOrderNo["setFormFiledVal"]['modelname'] = $modelName;
				$newOrderNo["setFormFiledVal"]['oprate'] = $update? 'edit' : 'add' ;
				
				
				$leixing=$_REQUEST['pingshenhuileixing'];
				$misAutoFwvvgModel=M('mis_auto_fwvvg');
				$fwmap['status']=1;
				$fwmap['id']=$leixing;
				$fwlist=$misAutoFwvvgModel->where($fwmap)->find();
				$companyName1=$_SESSION['companyid'];
				$companyName=getFieldBy($companyName1, 'gongsimingchen', 'gongsijianchen', 'mis_auto_offsz');
				$gongsiquanchen=getFieldBy($companyName1, 'gongsimingchen', 'gongsiquanchen', 'mis_auto_offsz');
				$year=date('Y');
				//查询当前年是否跟数据库年相同 不相同则重置
				$classifyModel=M('mis_system_config_orderno_classify');
				$classifymap=array();
				$classifymap['table']=mis_auto_fuhhu;
				$classifymap['modelname']=MisAutoVph;
				$classifymap['fieldval']=$firstList;
				$classifyList=$classifyModel->where($classifymap)->select();
				if($classifyList['oldrule']!=$year){
					$classifdata['numshow'] = 0;
					$classifdata['numnew'] = 1;
					$classifdata['oldrule'] = $year;
					$result = $classifyModel->where($classifymap)->setField($classifdata);
				}
				$scnmodel = D('SystemConfigNumber');
				$ordernoInfo = $scnmodel->getOrderno('mis_auto_fuhhu','MisAutoVph',null,$leixing);
				$num=$ordernoInfo['orderno'];
				//名称
				$qlnum=preg_replace('/^0*/', '', $num);
				$zwnum=numToCh($qlnum);
				if($leixing==5){
					$firstordernoName='重农担业评';
					$firstName=$fwlist['name'];
					$mingchen=$year.'年第'.$zwnum.'次'.$firstName;
				}elseif($leixing==2){
					if($companyName1==2){
						$firstordernoName=$companyName.'专评';
						$firstName='项目评审会';
						$mingchen=$year.'年'.$gongsiquanchen.'第'.$zwnum.'次'.$firstName;
					}else{
						$firstordernoName=$companyName.'专评';
						$firstName='外部专家评审会';
						$mingchen=$year.'年第'.$zwnum.'次'.$firstName;
					}
				}elseif($leixing==1){
					$firstordernoName='重农担控评';
					$firstName=$fwlist['name'];
					$mingchen=$year.'年第'.$zwnum.'次'.$firstName;
				}elseif($leixing==6){
					$firstordernoName='重农担资保评';
					$firstName='资保内部评审会';
					$mingchen=$year.'年第'.$zwnum.'次'.$firstName;
				}elseif($leixing==3){
					$firstordernoName='集中评';
					$firstName='集中评';
					$mingchen=$year.'年第'.$zwnum.'次'.$firstName;
				}elseif($leixing==7){
					$firstordernoName='重农资专评';
					$firstName='重农资专评';
					$mingchen='重庆市农业资产经营管理有限公司基金项目第'.$zwnum.'次投资决策专家顾问咨询（评审）会';
				}
				$orderno =$firstordernoName.'【'.$year.'】第'.$ordernoInfo['orderno'].'号';
				//编码重复，重新构建一个编码
				$_POST['orderno'] = $orderno;
				$_POST['mingchen'] = $mingchen;
				
				$model	= M('mis_system_config_orderno_classify');
				$map=array();
				$map['table']=$tableName;
				$map['modelname']=$modelName;
				$map['fieldval']=$_REQUEST[$typefield];
				$numInfo = $model->where($map)->field('numnew,numshow')->find();
				if($numInfo['numnew']>$numInfo['numshow']){
					$ordernum=$numInfo['numnew'];
				}else{
					$ordernum=$numInfo['numshow']+1;
				}
				$data = array('numshow'=>$ordernum);
				$result = $model->where($map)->setField($data);
				//$newOrderNo['setFormFiledVal']['orderno']=$orderno;
				//$newOrderNo['setFormFiledVal']['mingchen']=$mingchen;
				//$_POST["validate_field"] = $field;
				//$modelTitle = M('node')->where("name='".$modelName."'")->getfield('title');
				//$msg = $modelTitle.$modelName.$status.'-编码重复,编码 if,请重新保存!';
				//throw new NullDataExcetion($msg , 0, $newOrderNo);
				//$this->error ( $modelTitle.$modelName.$status.'-编码重复,系统已经重新生成编码,请重新保存' , '', $newOrderNo);
					
			}else{
				//递归检查
				$model	= D('SystemConfigNumber');
				$newOrderNoInfo=$model->getOrderno($tableName,$modelName);
				//编码重复，重新构建一个编码
				$_POST['orderno'] = $newOrderNoInfo['orderno'];
				
				$model	= D('SystemConfigNumber');
				$map=array();
				$map['table']=$tableName;
				$map['modelname']=$modelName;
				$numInfo = $model->where($map)->field('numnew,numshow')->find();
				if($numInfo['numnew']>$numInfo['numshow']){
					$ordernum=$numInfo['numnew'];
				}else{
					$ordernum=$numInfo['numshow']+1;
				}
				$data = array('numshow'=>$ordernum);
				$result = $model->where($map)->setField($data);
				//$newOrderNo["setFormFiledVal"] = $newOrderNoInfo;			
				//$newOrderNo["setFormFiledVal"]['modelname'] = $modelName;
				//$newOrderNo["setFormFiledVal"]['oprate'] = $update? 'edit' : 'add' ;
				//$_POST["validate_field"] = $field;
				//$modelTitle = M('node')->where("name='".$modelName."'")->getfield('title');
				//$msg = $modelTitle.$modelName.$status.'-编码重复, else 重新生成编码!';
				//throw new NullDataExcetion($msg ,0,$newOrderNo);
			
				//$this->error ( $modelTitle.$modelName.$status.'-编码重复,系统已经重新生成编码,请重新保存' , '', $newOrderNo);
			}
		}else{
			if($typefield){
				if(!$update){
					$model	= M('mis_system_config_orderno_classify');
					$map=array();
					$map['table']=$tableName;
					$map['modelname']=$modelName;
					$map['fieldval']=$_REQUEST[$typefield];
					$numInfo = $model->where($map)->field('numnew,numshow')->find();
					if($numInfo['numnew']>$numInfo['numshow']){
						$ordernum=$numInfo['numnew'];
					}else{
						$ordernum=$numInfo['numshow']+1;
					}
					$data = array('numshow'=>$ordernum);
					$result = $model->where($map)->setField($data);
				}
			}else{
				//如果是新增，往系统内插入数据
				if(!$update){			
				$model	= D('SystemConfigNumber');
				$map=array();
				$map['table']=$tableName;
				$map['modelname']=$modelName;
				$numInfo = $model->where($map)->field('numnew,numshow')->find();
				if($numInfo['numnew']>$numInfo['numshow']){
					$ordernum=$numInfo['numnew'];		
				}else{
					$ordernum=$numInfo['numshow']+1;
				}
				$data = array('numshow'=>$ordernum);
				$result = $model->where($map)->setField($data);
				}
			}
		}
	}
	/*
	 *自动编码重复递归解决方案
	* @author  wangcheng
	* @date:2012-11-1
	* @param sting $table 表名
	* @param sting $field 字段名
	* @param int   $update 是否是更新（1,0
	*/
	protected function checkOrderno($tableName,$modelName,$orderno){
		$modelName = empty($modelName) ? $this->getActionName() : $modelName;
		$condition = array($field=>array('eq', $orderno));
		if($update)	$condition['id'] = array('neq', $_POST['id']);
		
		$tablemodel = D($modelName);
		$list = $tablemodel->where($condition)->getField('id');
		//print_r($tablemodel->getLastSql());
		if($list){
			//递归检查
			$model	= D('SystemConfigNumber');
			$newOrderNoInfo=$model->getOrderno($tableName,$modelName);
			$newOrderNo["setFormFiledVal"] = $newOrderNoInfo;
			$newOrderNo["setFormFiledVal"]['modelname'] = $modelName;
			$newOrderNo["setFormFiledVal"]['oprate'] = $update? 'edit' : 'add' ;
			$_POST["validate_field"] = $field;
			$msg = '编码重复,系统已经重新生成编码,请重新保存';
			throw new NullDataExcetion($msg , $newOrderNo);
			//$this->error ( '编码重复,系统已经重新生成编码,请重新保存' , '', $newOrderNo);
		}else{
			//往系统内插入数据
			$model	= D('SystemConfigNumber');
			$map=array();
			$map['table']=$tableName;
			$map['modelname']=$modelName;
			$numInfo = $model->where($map)->field('numnew,numshow')->find();
			if($numInfo['numnew']>$numInfo['numshow']){
				$ordernum=$numInfo['numnew'];
			}else{
				$ordernum=$numInfo['numshow']+1;
			}
			$data = array('numshow'=>$ordernum);
			$result = $model->where($map)->setField($data);
		}
	}
	
	/*
	 *datatable自动编码
	* @author  wangcheng
	* @date:2012-11-1
	* @param sting $table 表名
	* @param sting $field 字段名
	* @param int   $update 是否是更新（1,0
	*/
	protected function checkOrdernoDatatable($actionName,$orderno,$update){
		$modelName=$this->getActionName();
		$tableName=D('modelName')->getTableName();
		$_POST['orderno']=$orderno;
		$this->checkifexistcodeororder($tableName, $field='orderno', $modelName, $update);
	}
	
	
	/**
	 +----------------------------------------------------------
	 * 读取动态配制操作
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 * @throws ThinkExecption
	 +----------------------------------------------------------
	 */
	public function getSystemConfigDetail($modelname='',$vo){
		//dump($vo);
		$scdmodel = D('SystemConfigDetail');
		if(empty($modelname)){
			$modelname = $this->getActionName();
		}
		$detailList = $scdmodel->getDetail($modelname,false);
		if ($detailList) {
			$fieldsarr = array();
			foreach ($detailList as $k => $v) {
				$showname = '';
				if($v['status'] != -1){
					$showMethods = "";
					if($v['methods']){
						$methods = explode(';', $v['methods']);// 分解所有方法
						$showMethods .= "<span class='xyminitooltip'><span class='xyminitooltip_con'>";
						foreach ($methods as $key => $vol) {
							$volarr = explode(',', $vol);// 分解target和方法
							$target = $volarr[0];// 弹出方式
							$method = $volarr[1];// 方法名称
							$modelarr = explode('/', $method);// 分解方法0：model；1：方法
							//此处默认方法session权限，表单内部的url权限问题
							$_SESSION[strtolower($modelarr[0].'_'.$modelarr[1])] = 1;
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
						/*
						 * 构造动态显示字段
						*/
						$model=D($v['models']);//模型
						$table=$model->getTableName();//表名
						$relation=getFieldBy($vo[$v['name']], $v['relation'], 'id', $table);//ID
						$method = $v['methods'];// 函数
						$url = "/id/".$relation;// 构造加长链接
						$target = "navTab";// 打开类型
						$attr = ""; //扩展属性
						
						/*
						 * 判断配置字段是否有组合
						* 组合有两种 第一种是  查看列表;查看明细
						*/
						if(strpos($v['models'], ';')){
							$marray = explode(';', $v['models']);
							// 如果有传值 这 构造明细查看
							if($vo) {
								// 明细查看 组合为：model名称/函数名称/查询字段/打开方式（dialog、navtab）/高度/宽度/是否有遮罩层
								$array = explode('/', $marray[1]);
								$models = $array[0];//model名称
								$method = $array[1];//函数名称
								//$url = "/".$array[2]."/".$vo[$v['name']];// 查询字段构造带参数URL
								$url = "/id/".$relation;
								// 判断dialog方式的情况
								if($array[3]) {
									$target = $array[3];
									if($target == "dialog"){
										// 添加dialog方式的属性
										if($array[4]) $attr .= " height='".$array[4]."' ";
										if($array[5]) $attr .= " width='".$array[5]."' ";
										if($array[6]) $attr .= " mask='".$array[6]."' ";
									}
								}
							} else {
								// 没有传值则直接去model 显示列表
								$models = $marray[0];
							}
						} else {
							// 获取model
							$models = $v['models'];
							// 如果存在两种方式则
							if(strpos($v['models'], '/')){
								// 明细查看 组合为：model名称/函数名称/查询字段/打开方式（dialog、navtab）/高度/宽度/是否有遮罩层
								$array = explode('/', $v['models']);
								$models = $array[0];//model名称
								// 传参方式 构造链接
								if($vo) {
									$method = $array[1];
									//$url = "/".$array[2]."/".$vo[$v['name']];
									$url = "/id/".$relation;
									if($array[3]) {
										$target = $array[3];
										if($target == "dialog"){
											if($array[4]) $attr .= " height='".$array[4]."' ";
											if($array[5]) $attr .= " width='".$array[5]."' ";
											if($array[6]) $attr .= " mask='".$array[6]."' ";
										}
									}
								}
							}
						}
						//此处默认方法session权限，表单内部的url权限问题
						$_SESSION[strtolower($v['models'].'_'.$method)] = 1;
						if ($_SESSION[strtolower($v['models'].'_'.$method)] || $_SESSION["a"]) {
							$showname .= "<a rel='".$models.$method."' class='word_node_link' target='".$target."' ".$attr." href='__APP__/".$models."/".$method.$url."'><i class='icon-link'></i><span title='".$v['showname']."'>".$v['showname']."</span></li></a>";
						} else {
							$showname .= "<span title='".$v['showname']."'>".$v['showname']."</span>";
						}
					} else{
						$showname .= "<span title='".$v['showname']."'>".$v['showname']."</span>";
					}
					if($v['helpvalue']){
						$showname = '<a title="'.$v['helpvalue'].'">?</a>'.$showname;
					}
				}
				$fieldsarr[$v['name']] = $showname;
			}
				
			// 			print_R($fieldsarr);
			// 			exit;
			$this->assign ( 'fields', $fieldsarr );
		}
	}
// 	/**
// 	 +----------------------------------------------------------
// 	 * 读取动态配制操作
// 	 +----------------------------------------------------------
// 	 * @access public
// 	 +----------------------------------------------------------
// 	 * @return string
// 	 +----------------------------------------------------------
// 	 * @throws ThinkExecption
// 	 +----------------------------------------------------------
// 	 */
// 	public function getSystemConfigDetail($modelname='',$vo){
// 		$scdmodel = D('SystemConfigDetail');
// 		if(empty($modelname)){
// 			$modelname = $this->getActionName();
// 		}
// 		$detailList = $scdmodel->getDetail($modelname,false);

// 		if ($detailList) {
// 			$fieldsarr = array();
// 			foreach ($detailList as $k => $v) {
// 				$showname = '';
// 				if($v['status'] != -1){
// 					$showMethods = "";
// 					if($v['methods']){
// 						$methods = explode(';', $v['methods']);// 分解所有方法
// 						$showMethods .= "<span class='xyminitooltip'><span class='xyminitooltip_con'>";
// 						foreach ($methods as $key => $vol) {
// 							$volarr = explode(',', $vol);// 分解target和方法
// 							$target = $volarr[0];// 弹出方式
// 							$method = $volarr[1];// 方法名称
// 							$modelarr = explode('/', $method);// 分解方法0：model；1：方法
// 							if ($_SESSION[strtolower($modelarr[0].'_'.$modelarr[1])] || $_SESSION["a"]) {
// 								$showMethods .= "<a rel='".$modelarr[0].$modelarr[1]."' target='".$target."' href='__APP__/".$method."' mask='true'>".$normArray[$modelarr[1]]."</a>";
// 								$isfalse = true;
// 							}
// 							//$showname .= "<a rel='".$modelarr[0].$modelarr[1]."' target='".$target."' href='__APP__/".$method."' mask='true'>".$normArray[$modelarr[1]]."</a>";
// 							$isfalse = true;
// 						}
// 						if($showMethods){
// 							$showMethods .= "<span class='xyminitooltip_arrow_outer'></span><span class='xyminitooltip_arrow'></span></span></span>";
// 						}
// 					}
// 					if($showMethods){
// 						$showname .= $showMethods;
// 					}
// 					if ($v['models']) {
// 						/*
// 						 * 构造动态显示字段
// 						 */
// 						$method = "index";// 函数
// 						$url = "";// 构造加长链接
// 						$target = "navTab";// 打开类型
// 						$attr = ""; //扩展属性
// 						/*
// 						 * 判断配置字段是否有组合
// 						 * 组合有两种 第一种是  查看列表;查看明细
// 						 */
// 						if(strpos($v['models'], ';')){
// 							$marray = explode(';', $v['models']);
// 							// 如果有传值 这 构造明细查看
// 							if($vo) {
// 								// 明细查看 组合为：model名称/函数名称/查询字段/打开方式（dialog、navtab）/高度/宽度/是否有遮罩层
// 								$array = explode('/', $marray[1]);
// 								$models = $array[0];//model名称
// 								$method = $array[1];//函数名称
// 								$url = "/".$array[2]."/".$vo[$v['name']];// 查询字段构造带参数URL
// 								// 判断dialog方式的情况
// 								if($array[3]) {
// 									$target = $array[3];
// 									if($target == "dialog"){
// 										// 添加dialog方式的属性
// 										if($array[4]) $attr .= " height='".$array[4]."' ";
// 										if($array[5]) $attr .= " width='".$array[5]."' ";
// 										if($array[6]) $attr .= " mask='".$array[6]."' ";
// 									}
// 								}
// 							} else {
// 								// 没有传值则直接去model 显示列表
// 								$models = $marray[0];
// 							}
// 						} else {
// 							// 获取model
// 							$models = $v['models'];
// 							// 如果存在两种方式则
// 							if(strpos($v['models'], '/')){
// 								// 明细查看 组合为：model名称/函数名称/查询字段/打开方式（dialog、navtab）/高度/宽度/是否有遮罩层
// 								$array = explode('/', $v['models']);
// 								$models = $array[0];//model名称
// 								// 传参方式 构造链接
// 								if($vo) {
// 									$method = $array[1];
// 									$url = "/".$array[2]."/".$vo[$v['name']];
// 									if($array[3]) {
// 										$target = $array[3];
// 										if($target == "dialog"){
// 											if($array[4]) $attr .= " height='".$array[4]."' ";
// 											if($array[5]) $attr .= " width='".$array[5]."' ";
// 											if($array[6]) $attr .= " mask='".$array[6]."' ";
// 										}
// 									}
// 								}
// 							}
// 						}
// 						if ($_SESSION[strtolower($v['models'].'_'.$method)] || $_SESSION["a"]) {
// 							$showname .= "<a rel='".$models.$method."' target='".$target."' ".$attr." href='__APP__/".$models."/".$method.$url."'><span title='".$v['showname']."'>".$v['showname']."</span></a>";
// 						} else {
// 							$showname .= "<span title='".$v['showname']."'>".$v['showname']."</span>";
// 						}
// 					} else{
// 						$showname .= "<span title='".$v['showname']."'>".$v['showname']."</span>";
// 					}
// 					if($v['helpvalue']){
// 						$showname = '<a title="'.$v['helpvalue'].'">?</a>'.$showname;
// 					}
// 				}
// 				$fieldsarr[$v['name']] = $showname;
// 			}
			
// // 			print_R($fieldsarr);
// // 			exit;
// 			$this->assign ( 'fields', $fieldsarr );
// 		}
// 	}
	/**
	 +----------------------------------------------------------
	 * 默认添加操作
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @parameter obj  string  模板输出对象
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 * @throws ThinkExecption
	 +----------------------------------------------------------
	 */
	public function add($obj) {
		//查询当前审批节点  model 权限取值begin
		$modelname = $this->getActionName();
		/*
		 * 验证新增页面或者修改页面字段权限控制
		 * 查询固定流程是否存在
		 */
		$ProcessInfoModel = D ( "ProcessInfo" );
		//验证流程是否存在
		$pcarr = $ProcessInfoModel->getProcessInfo($modelname);
		if($pcarr){
			//存在流程配置
			$map = array();
			$map['pinfoid'] = $pcarr ['id'];
			$map['tablename'] = "process_info";
			$map['status'] = 1;
			$map['flowtype'] = 0; //开始节点
			$ProcessRelationModel = D ( "ProcessRelation" );
			// 获取流程节点数据
			$relationList = $ProcessRelationModel->where ( $map )->find();
			if($relationList){
				$userid = $_SESSION[C('USER_AUTH_KEY')];
				$_SESSION["nodeActionName".$userid] = $modelname;
				$_SESSION['nodeAuditId'.$userid] = $relationList['id'];
				$_REQUEST['node'] = $relationList['id'];
			}
		}
		//end 
		//项目管理中的生单
		$projectid = $_REQUEST['projectid'];
		$projectworkid = $_REQUEST['projectworkid'];
		//获取子流程生单的模型和ID值。
		$sourcemodel = $_GET['auditFlowTuiTablename'];
		$sourceid = $_GET['auditFlowTuiTableid'];
		//获取主流程的模型和ID值。
		$auditZhuLicModel = $_GET['auditZhuLicModel'];
		$auditZhuLicId = $_GET['auditZhuLicId'];
		//组合参数，为了满足套表项目任务数据
		$projectParam = array('projectid'=>$projectid,
				'projectworkid'=>$projectworkid,
				'auditFlowTuiTablename'=>$sourcemodel,
				'auditFlowTuiTableid'=>$sourceid,
				'auditZhuLicModel'=>$auditZhuLicModel,
				'auditZhuLicId'=>$auditZhuLicId);
		//采用哪种生单标记
		$bool = true;
		if($sourceid && $sourcemodel){
			//进入子流程生单，则不进项目生单
			$bool = false;
			//子流程推单     则查询数据漫游信息
			$MisSystemDataRoamingModel=D('MisSystemDataRoaming');
			$dataArr=$MisSystemDataRoamingModel->main(1,$sourcemodel,$sourceid,4,$modelname);
			$targetvo=array();
			/*
			 * 此部分，需要加入内嵌表生单
			 */
			foreach($dataArr as $targettable=>$data){
				if(strpos($targettable, "_sub_")){
					//内嵌表型,内嵌表数据就是2维数组型
					$neiqianlist = array();
					foreach($data as $k => $v){
						$arr = array();
						foreach($v as $k1 => $v1){
							if(reset($v1)){
								$arr[key($v1)] = reset($v1);
							}
						}
						if($arr){
							$neiqianlist[] = $arr;
						}
					}
					$neiqianname = end(explode("_", $targettable));
					//先抓取before_add的vo值输出；
					$before_add_neiqian_list = $this->__get("innerTabelObj{$neiqianname}Data");
					if($before_add_neiqian_list && $neiqianlist){
						$neiqianlist = array_merge($before_add_neiqian_list,$neiqianlist);
					}
					//输出内嵌表的volist值
					$this->assign("innerTabelObj{$neiqianname}Data",$neiqianlist);
				}else{
					//主表型
					foreach($data as $k => $v){
						foreach($v as $k1 => $v1){
							$targetvo[key($v1)] = reset($v1);
						}
					}
				}
			}
			$targetvo = array_merge($targetvo,$projectParam);
			//先抓取before_add的vo值输出；
			$before_add_vo = $this->__get("vo");
			if($before_add_vo){
				$targetvo = array_merge($before_add_vo,$targetvo);
			}
			$this->assign("vo",$targetvo);
		}
		
		if($projectid && $projectworkid && $bool){
			//存在前置任务、获取前置任务ID，查询前置任务是否完成
			$where = array();
			$where['id'] = $projectworkid;
			$mis_project_flow_formDao = M("mis_project_flow_form");
			$sourcemodel = $mis_project_flow_formDao->where($where)->getField("sourcemodel");
			if($sourcemodel){
				//查询来源数据
				$sourceModel = D($sourcemodel);
				$where = array();
				$where['projectid'] = $projectid;
				$sourceid = $sourceModel->where($where)->order("id desc")->getField("id");
				if($sourceid){
					$MisSystemDataRoamingModel=D('MisSystemDataRoaming');
					$dataArr=$MisSystemDataRoamingModel->main(1,$sourcemodel,$sourceid,4,$modelname);
					$targetvo=array();
					foreach($dataArr as $targettable=>$data){
						foreach($data as $k => $v){
							foreach($v as $k1 => $v1){
								$targetvo[key($v1)] = reset($v1);
							}
						}
					}
					$targetvo = array_merge($targetvo,$projectParam);
					//先抓取before_add的vo值输出；		
					$before_add_vo = $this->__get("vo");
					if($before_add_vo){
						$targetvo = array_merge($before_add_vo,$targetvo);
					}
					$this->assign("vo",$targetvo);
				}
			}	
			//生单完毕
		}
		//此处添加_after_add方法，方便在方法中，判断跳转
		$module2 = A($modelname);
		if (method_exists($module2,"_after_add")) {
			call_user_func(array(&$module2,"_after_add"),$data);
		}
		//读取动态配制
		$this->getSystemConfigDetail($modelname,$targetvo);
		$this->display ($obj);
	}

	function read() {
		$this->edit ();
	}

	/**
	 * @Title: view
	 * @Description: todo(预览界面)
	 * @author 杨东
	 * @date 2014-5-22 下午3:19:41
	 * @throws
	 */
	function view($num=1){
		$name = $this->getActionName();
		$remindMap=array();
		$remindMap['modelname']=$name;
		$remindMap['userid']= $_SESSION[C('USER_AUTH_KEY')];
		$remindMap['isread']=0;
		$remindMap['pkey']=$_REQUEST['id'];
		$remindModel=M("mis_system_sysremind");
		$result=$remindModel->where($remindMap)->find();
		if($result){
			$data = array('isread'=>1,'readcount'=>1,'remindcreatetime'=>time(),'updatetime'=>time());
			$remindModel->where($remindMap)->setField($data);
		}else {
			$remindMap['isread']=1;
			$result=$remindModel->where($remindMap)->find();
			if($result)
			{
				if($result['readcount']){
					$data = array('readcount'=>$result['readcount']+1,'updatetime'=>time());
					$remindModel->where($remindMap)->setField($data);
				}else{
					$data = array('readcount'=>1,'updatetime'=>time());
					$remindModel->where($remindMap)->setField($data);
				}
			}
		}
		//定时任务已读
		//print_r($result);
		//echo $result['readcount'];
		if($_REQUEST['preview']==1){
			$this->previewPost();
			//读取动态配制
			$this->getSystemConfigDetail($name);
			$this->display();
			exit;
		}
		$name=$this->getActionName();
		$module=A($name);
		if (method_exists($module,"_before_edit")) {
			call_user_func(array(&$module,"_before_edit"));
		}
		$this->edit ($num);
	}
	/**
	 * 
	 * @Title: previewPost
	 * @Description: todo(预览：转换post)   
	 * @author renling 
	 * @date 2015年3月16日 下午4:32:57 
	 * @throws
	 */
	function previewPost(){
		$name=$this->getActionName();
		if(false === D($name)->create ()) {
			$vo=$_POST;
		}else{
			$vo=D($name)->create();
		}
		//有内嵌表格
		if($_POST['datatable']||$_POST['#hide#datatable']){
			$datatable=$_POST['datatable'];
			if($_POST['#hide#datatable']){
				$datatable=$_POST['#hide#datatable'];
				if($_POST['datatable']){
					$datatable=array_merge($_POST['#hide#datatable'],$_POST['datatable']);
				}
			}
			foreach($datatable as $key=>$val){
				foreach($val as $k=>$v){
					$insertData[$k][]=$v;
				}
			}
		}
		foreach ($insertData as $key=>$val){
			//传送datatable值
			$this->assign("innerTabelObj".$key."Data",$val);
		}
// 		print_r($vo);
		$this->assign('vo' , $vo);
	}
	/**
	 * @Title: changeEdit
	 * @Description: todo(变更流程方法输出模板)   
	 * @author 黎明刚
	 * @date 2015年3月14日 下午9:26:47 
	 * @throws
	 */
	public function changeEdit(){
		// 实例化对应数据表模型
		$name = $this->getActionName ();
		// 审批流 动态建模页面时的子表数据获取 add by nbmkxj@20150129 2214
		$viewCheckPath = LIB_PATH.'Model/'.$name.'ViewModel.class.php';
		if(is_file($viewCheckPath)){
			$model = D ( $name.'View' );
		}else{
			$model = D ( $name);
		}
		if (method_exists ( $this, '_before_edit' )) {
			$this->_before_edit ();
		}
		//获取当前主键
		$id = $_REQUEST [$model->getPk ()];
		$map['id']=$id;
		$vo = $model->where($map)->find();
		/*
		 * 验证新增页面或者修改页面字段权限控制
		* 查询固定流程是否存在
		*/
		$ProcessInfoModel = D ( "ProcessInfo" );
		//验证流程是否存在
		$pcarr = $ProcessInfoModel->getProcessInfo($name,2);
		if($pcarr){
			//存在流程配置
			$map = array();
			$map['pinfoid'] = $pcarr ['id'];
			$map['tablename'] = "process_info";
			$map['status'] = 1;
			$map['flowtype'] = 0; //开始节点
			$ProcessRelationModel = D ( "ProcessRelation" );
			// 获取流程节点数据
			$relationList = $ProcessRelationModel->where ( $map )->find();
			if($relationList){
				$userid = $_SESSION[C('USER_AUTH_KEY')];
				$_SESSION["nodeActionName".$userid] = $name;
				$_SESSION['nodeAuditId'.$userid] = $relationList['id'];
				$_REQUEST['node'] = $relationList['id'];
			}
		}
		//获取附件信息
		$this->getAttachedRecordList($id,true,true,$name);
		// 获取现 可能有的地区信息
		$areaModel = M('MisAddressRecord');
		if(C('AREA_TYPE')==1){
			$areainfomap['tablename'] = $this->getActionName();
		}elseif(C('AREA_TYPE')==2){
			$areainfomap['tablename'] = D($this->getActionName())->getTableName();
		}
		$areainfomap['tableid'] = $id ;
		$areaArr = $areaModel->where($areainfomap)->select();
		foreach ($areaArr as $key=>$val){
			$areainfoarry[$val['fieldname']][]=$val;
		}
		$this->assign('areainfoarry' , $areainfoarry);
		//lookup带参数查询
		$module=A($name);
		if (method_exists($module,"_after_edit")) {
			call_user_func(array(&$module,"_after_edit"),&$vo);
		}
		//读取动态配制
		$this->getSystemConfigDetail($name,$vo);
		$this->assign( 'vo', $vo );
		$this->display("edit");
	}
	
	function edit($num=1) {
		// 实例化对应数据表模型
		$name = $this->getActionName ();
		// 审批流 动态建模页面时的子表数据获取 add by nbmkxj@20150129 2214
		$viewCheckPath = LIB_PATH.'Model/'.$name.'ViewModel.class.php';
		if(is_file($viewCheckPath)){
			$model = D ( $name.'View' );
		}else{
			$model = D ( $name);
		}
		
		//获取当前主键
		$id = $_REQUEST [$model->getPk ()];
		$map['id']=$id;
		$vo = $model->where($map)->find();
		$retSql = <<<EOF
		
				<script>
				console.log("表数据查询条件:{$model->getLastSql()}");
				</script>
EOF;
//   		 echo $retSql;
		if(false ===$vo && APP_DEBUG){
			$this->error($model->getDBError());
		}
		/*
		 * 验证新增页面或者修改页面字段权限控制
		 * 查询固定流程是否存在
		 */
		$ProcessInfoModel = D ( "ProcessInfo" );
		//验证流程是否存在
		$pcarr = $ProcessInfoModel->getProcessInfo($name);
		if($pcarr){
			//存在流程配置
			$map = array();
			$map['pinfoid'] = $pcarr ['id'];
			$map['tablename'] = "process_info";
			$map['status'] = 1;
			$map['flowtype'] = 0; //开始节点
			$ProcessRelationModel = D ( "ProcessRelation" );
			// 获取流程节点数据
			$relationList = $ProcessRelationModel->where ( $map )->find();
			if($relationList){
				$userid = $_SESSION[C('USER_AUTH_KEY')];
				$_SESSION['nodeAuditId'.$userid]="";
				$_SESSION["nodeActionName".$userid] = $name;
				$_SESSION['nodeAuditId'.$userid] = $relationList['id'];
				$_REQUEST['node'] = $relationList['id'];
			}
		}
		//end
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		
		// 上一条数据ID
		$map['id'] = array("lt",$id);
		$updataid = $model->where($map)->order('id desc')->getField('id');
		$this->assign("updataid",$updataid);
		// 下一条数据ID
		$map['id'] = array("gt",$id);
		$downdataid = $model->where($map)->getField('id');
		$this->assign("downdataid",$downdataid);

		//获取附件信息
		$this->getAttachedRecordList($id,true,true);
		//获取地图信息
		
		// 获取现 可能有的地区信息
		$areaModel = M('MisAddressRecord');
		if(C('AREA_TYPE')==1){
			$areainfomap['tablename'] = $this->getActionName();
		}elseif(C('AREA_TYPE')==2){
			$areainfomap['tablename'] = D($this->getActionName())->getTableName();
		}
		
		$areainfomap['tableid'] = $id ;
		$areaArr = $areaModel->where($areainfomap)->select();
		foreach ($areaArr as $key=>$val){
			$areainfoarry[$val['fieldname']][]=$val;
		}
		$this->assign('areainfoarry' , $areainfoarry);
		//lookup带参数查询
		$module=A($name);
//		$module->_after_edit();
		if (method_exists($module,"_after_edit")) {
			call_user_func(array(&$module,"_after_edit"),&$vo);
		}
		//读取动态配制
		$this->getSystemConfigDetail($name,$vo);
// 		dump($vo);
		$this->assign( 'vo', $vo );
		if($_REQUEST['curModelForDataManage']){
			return $vo;
		}else if($num){
			$this->display ();
		}
		
	}
	/**
	 * @Title: _before_overProcess
	 * @Description: todo(流程启动就完成，或者审核完成，1验证当前流程是否为别人的子流程，2验证当前流程是否是项目任务) 
	 * @param int 当前单据ID $id  
	 * @author 黎明刚
	 * @date 2015年3月5日 下午2:20:18 
	 * @throws
	 */
	public function _before_overProcess($id) {
		//获取当前模型名称
		$name = $this->getActionName();
		//流程项目任务
		$data = D($name)->find($id);
		//如果存在项目编号跟项目任务，而且审核状态为审核完毕
		if ($data['projectid'] && $data['projectworkid'] ) {
			$MSFFModel = D ( 'MisProjectFlowForm' );
			$MSFFModel->setWorkComplete ( $data ['projectworkid'],$data['projectid'] );
			// 这里不报错，如果报错的话会有很多影响，如果没更新状态成功，那需要他人为再去更新状态
		}
		//子流程
		if(1==1){
			//找到原始流程的对象及ID
			$map = array();
			$map['zitablename'] = $name; //子流程模型名称
			$map['zitableid'] = $id;  //子流程id
			$map['createid'] = $data['createid']; //子流程制单人
			$obj=M('process_relation_children')->where($map)->find();
			if(!$obj){
				//为了匹配以前的老数据
				//找到原始流程的对象及ID
				$map = array();
				$map['isauditmodel']=$name;
				$map['isaudittableid']=$id;//原来是POST传值，现在改为参数传值
				$map['auditState']=0;//未处理的子流程
				$map['doing']=1;//走向节点
				$obj=M('process_relation_form')->where($map)->find();
				if($obj){
					$obj['relation_formid'] = $obj['id'];
				}
			}
			if($obj){
				/*
				 * 流程监控表，修改子流程审核节点未启动。
				 * 当子流程启动后，则关闭子流程生单的审核提示，子流程数组反写主流程绑定ID
				 * @liminggang 2015-3-10
				 */
				$mis_work_monitoringDao = M("mis_work_monitoring");
				$map = array();
				$map['tablename'] = $obj['tablename'];//主流程模型
				$map['tableid'] = $obj['tableid'];	//主流程id
				$map['ostatus'] = $obj['relation_formid']; //节点id
				$map['_string'] = "FIND_IN_SET( {$data['createid']},curAuditUser )";
				$mis_work_monitoringDao->where($map)->setField("isauditstatus",0);
				//当前子流程审核完成，进行主流程当前节点通过
				$mainProcessModel=A($obj['tablename']);
				//旧的post值
				$oldpost = $_POST;
				unset($_POST);
				$_POST ['id']=$obj['tableid'];
				$_REQUEST['doinfo'] = "【".getFieldBy($data['createid'], "id", "name", "user")."】发起的子流程单据完成";
				C("TOKEN_ON",false);
				$mainProcessModel->auditProcess(2,$data['createid']);
				C("TOKEN_ON",true);
				//还原主数据的ID
				unset($_POST);
				//将旧的post内容。恢复到主数据上面
				$_POST = $oldpost;
			}
		}
	}
	/**
	 * 数据新增控制器
	 * @Title: insertControll
	 * @Description: todo(数据新增控制器，用于处理关系型表单的数据新增)
	 * @author quqiang
	 * @date 2015年2月28日 下午9:11:26
	 * @throws
	 */
	function insertControll(){
		$this->insert(true);
	}
	/**
	 * 数据修改控制器
	 * @Title: updateControll
	 * @Description: todo(数据修改控制器，用于处理关系型表单的数据修改)   
	 * @author quqiang 
	 * @date 2015年2月28日 下午9:11:26 
	 * @throws
	 */
	function updateControll(){
		//先获取老的数据。（因为套表中途可能重制了数据）
		$oldrequest = $_REQUEST;
		$originalPost = $_POST;
         //logs($originalPost , $this->getActionName().'_updateControll_'.date('Y-m-d-H' , time()) ,'',__CLASS__,__FUNCTION__,__METHOD__);
         
		// 新版套表提交
		if($originalPost['__apply__form__'] && $originalPost['__actionlistend__'] == 'end'){
			/**
			*	@todo
			*	没有处理套表无提交情况
			*/
			$isStartProcess = false;
			$model = new Model();
			$cacheFormData = $originalPost['__apply__form__'];
			
			//	遍历真实提交表单数据
			//logs(arr2string($cacheFormData) , 'settingtable');
			unset($_POST);
			$isret = true;
			$model->startTrans();
			
			try{
				foreach ($cacheFormData as $key => $val){
					$_POST = $val;
					// 操作批次标识
					$oprateTag = $_POST['__main__'];
					// 非主表，去除相关功能属性字段值。 // nbmxkj @20150625
					if($_POST['__main__'] == $_POST['__selfaction__']){
						$_POST['__coverformoperateid__'] = 'main';
					}else{
						$_POST['__coverformoperateid__'] = 'children';
					}
					unset($tempAction);
					unset($tempObj);
					unset($selfAction);
					unset($selfOprate);
					$selfAction = $_POST['__selfaction__'];
					$selfOprate = $_POST['__selfoprate__'];
						
					$tempAction = $val['__selfaction__'];
					$tempObj = A($tempAction);
					C("TOKEN_ON",false);
					$isret="";
					switch ( $selfOprate ){
						case 'add':
							if (method_exists($tempObj,"_before_insert")) {
								call_user_func(array(&$tempObj,"_before_insert"));
							}
							$isret = $tempObj->insert(1);
							break;
						case 'edit':
							if($_POST['__main__'] == $_POST['__selfaction__']){
								if(1==$_POST['__startprocessStatus__']){
									$isStartProcess = $isret = $tempObj->startprocess(true);
								}elseif(2==$_POST['__startprocessStatus__']){
									$isStartProcess = $isret = $tempObj->lookupUpdateProcess(true);
								}else{
									// 是主表
									if (method_exists($tempObj,"_before_update")) {
										call_user_func(array(&$tempObj,"_before_update"));
									}
									$isret = $tempObj->update(true);
								}
							}else{
								// 是子表修改
								if (method_exists($tempObj,"_before_update")) {
									call_user_func(array(&$tempObj,"_before_update"));
								}
								$isret = $tempObj->update(true);
							}
							// 是主表
// 							if (method_exists($tempObj,"_before_update")) {
// 								call_user_func(array(&$tempObj,"_before_update"));
// 							}
// 							$isret = $tempObj->update(true);
							// 类型为审批
// 							if($_POST['__main__'] == $_POST['__selfaction__'] && 'audit' == $_POST['__selfactiontype__'] ){
// 								if(1==$_POST['__startprocessStatus__']){
// 									  $isStartProcess = $tempObj->startprocess(true);
// 								}elseif(2==$_POST['__startprocessStatus__']){
// 									$isStartProcess = $tempObj->lookupUpdateProcess(true);
// 								}else{
// 									// 未定义操作
// 								}
// 							}
							break;    
						default:
							break;
					}
					$retArr[$tempAction] = $isret ;
					C("TOKEN_ON",true);
					//unset($_POST);
					if(!$isret){
						 break;
					}
				}
				if(!$isret){
					$model->rollback();
					
					$this->error("表单{$tempAction}进行数据操作时出现未知异常，已回滚!" );
				}else{
					// 拼接返回值
					$bindid = 0;
					if(getFieldBy($oprateTag, "bindaname", "inbindaname", "MisAutoBind","typeid",1)){
						$bindid = $retArr[$oprateTag] ;
					}else{
						$bindid=getFieldBy($retArr[$oprateTag], "id", "orderno", $oprateTag);
					}
					// 调用word版本控制生成函数
					if($retArr[$oprateTag]){
						$bool = getFieldBy($retArr[$oprateTag],"id","operateid",$oprateTag);
						if($bool == 1){
							$zhuModel = A($oprateTag);
							//终审后生成word版本
							//先关闭版控生成
							$saveList=$zhuModel->SaveVersionWord($retArr[$oprateTag],$oprateTag);
						}
					}
					$_REQUEST = $oldrequest;
					unset($oldrequest);
					$this->success('批量单据保存成功' , '' , array( 'id'=>$retArr[$oprateTag] ,'bindid'=>$bindid,'isprocess'=>$isStartProcess ) );
				//	}
				}
				
			}catch (Exception $e){
				$model->rollback();
				// getMessage()
				$this->error( $e->__toString().',指操作处理错误，数据回滚！' );
				Log::write($e->__toString());
			}
		}
	}
	
	/**
	 * @Title: updateprocess
	 * @Description: todo(普通表单变更方法)
	 * @author 黎明刚
	 * @date 2015年3月4日 下午5:01:48
	 * @throws
	 */
	public function lookupUpdateProcess($isrelation = false){
		$name=$this->getActionName();
		//model 权限取值 End
		$model = D ( $name );
		//这里用来存储临时缓存文件
		$updateBackup=$this->setOldDataToCache($model,$name,$_POST['id'],'lookupUpdateProcess');
		if (false === $model->create ()) {
			if(!$isrelation){
				$this->error ( $model->getError () );
			}else{
				throw new NullPointExcetion($model->getError () . ' ACTION: ' .$name);
				return false;
			}
		}
		// 更新数据
		$list=$model->save ();
		if (false !== $list) {
			$vo = $model->where("id =".$_POST['id'])->find();
			$pkValue=$_POST['id'];
			//修改附件信息
			$this->swf_upload($_POST['id'],0,null,$vo['projectid'],$vo['projectworkid']);
			// 地区信息修改 @nbmxkj - 20141030 16:05
			$this->area_info($_POST['id']);
			//执行成功后，用A方法进行实例化，判断当前控制器中是否存在_after_update方法
			$module2=A($name);
			if (method_exists($module2,"_after_update")) {
				call_user_func(array(&$module2,"_after_update"),$list);
			}
			//模板word文件版本生成
			if(!$_POST['__coverformoperateid__']){
				//版本控制
				$saveList=$this->SaveVersionWord($_POST['id'],$name);
			}
			/*
			 * _over_update 方法，为静默插入生单。
			 */
			if (method_exists($module2,"_over_update")) {
				//模型对象名与插入的id值
				$paramate=array($name,$pkValue,5,$updateBackup);  //参数值4为变更， gml 定义如下。新增1 、删除2、 修改3、变更5 ；
				call_user_func_array(array(&$module2,"_over_update"),$paramate);
				$this->unsetOldDataToCache($updateBackup);
			}
			if(!$isrelation){
				$this->success ( "表单数据变更成功！" );
			}else{
				return $pkValue;
			}
		} else {
			if(!$isrelation){
				$this->error ( "内容变更失败，请联系管理员");
			}else{
				throw new NullPointExcetion( $model->getDberror().L('_ERROR_'));
				return false;
			}
		}
	}
	
	/**
	 * 数据修改
	 * @Title: update
	 * @Description: todo(数据修改) 
	 * @param boolean $isrelation	是否返回状态
	 * @return boolean  
	 * 修改 ：屈强  2015年2月28日 下午9:09:02 
	 * @throws
	 */
	function update($isrelation=false) {
		//获取当前数据ID
		$id = $_POST['id'];
		$name=$this->getActionName();
		//查询当前审批节点  model 权限取值begin
		$mis_work_monitoringDao = M("mis_work_monitoring");
		$userid = $_SESSION[C('USER_AUTH_KEY')];
		$map = array();
		$map['tablename'] = $name;
		$map['tableid'] = $id;
		$map['dostatus'] = 0;
		$map['_string'] = 'FIND_IN_SET(  ' . $userid . ',curAuditUser )';
		$monlist = $mis_work_monitoringDao->where($map)->field("curNodeId,ostatus")->order("id desc")->select();
		if(count($monlist)>0){
			foreach($monlist as $mk=>$mv){
				$relationid = getFieldBy($mv['ostatus'], "id", "relationid", "process_relation_form");
				if($relationid < 1422900000 && $relationid>0){
					$_SESSION["nodeActionName".$userid] = $name;
					$_SESSION['nodeAuditId'.$userid] = $relationid;
					$_REQUEST['node'] = $relationid;
					break;
				}
			}
		}
		//model 权限取值 End
		
		$model = D ( $name );
		//这里用来存储临时缓存文件
		$updateBackup=$this->setOldDataToCache($model,$name,$id,'update');
		if (false === $model->create ()) {
			if(!$isrelation){
				$this->error ( $model->getError () );
			}else{
				throw new NullPointExcetion($model->getError () . ' ACTION: ' .MODULE_NAME);
				return false;
			}
		}
		//确认终审时间
		if($_POST['operateid']==1){
			$_POST ['alreadyauditnode'] = time();  //单据状态审批完成单据终审时间
		}
		// 更新数据
		$list=$model->save ();
		if (false !== $list) {
			$vo = $model->where("id =".$id)->find();
			$pkValue=$id;
 
			//修改附件信息
			$this->swf_upload($id,0,null,$vo['projectid'],$vo['projectworkid']);
			// 地区信息修改 @nbmxkj - 20141030 16:05
			$this->area_info($id);
			//地图定位修改
			$this->map_info($id);
			//执行成功后，用A方法进行实例化，判断当前控制器中是否存在_after_update方法
			$module2=A($name);
			if (method_exists($module2,"_after_update")) {
				//call_user_func(array(&$module2,"_after_update"),$list);//2015-09-29 by xyz
				call_user_func(array(&$module2,"_after_update"),$roamdo);
			}
			/*
			 * _over_update 方法，为静默插入生单。
			 */
			if (method_exists($module2,"_over_update")) {
				//模型对象名与插入的id值,这里2是修改
				$paramate=array($name,$pkValue,2,$updateBackup);
				//ischageoperateid为1的时候。表示审批流过来的变更节点审批终审。需要进行变更漫游
				if($_POST['ischageoperateid']==1){
					//模型对象名与插入的id值，这里5是变更
					$paramate=array($name,$pkValue,5,$updateBackup);
				}
				call_user_func_array(array(&$module2,"_over_update"),$paramate);
			}
			/*
			 * startprocess,在页面是不存在。此参数是在启动流程startprocess方法中默认赋值的，为了关闭insert的成功success输出
			 */
			$startprocess = $_POST ['startprocess'];
			/*
			 * operateid 确认提交标记
			 */
			if($_POST['operateid']==1){
				//这里清除内存表缓存的修改前数据代码
				$this->unsetOldDataToCache($updateBackup);
				/*
				 * 套表时特有属性，nbmxkj@20150625 1516
				 *  __coverformoperateid__ : mian/children
				 */
				if(!$_POST['__coverformoperateid__']){
					//版本控制
					$saveList=$this->SaveVersionWord($id,$name);
				}
				//修改子流程方法
				$this->_before_overProcess($id);
			}
			if ($startprocess !== 1) {
				
				$this->lookupdataInteractionPrepareData($id);
				
				if(!$isrelation){
					$this->success ( "表单数据保存成功！" );
				}else{
					return $id;
				}
			}
		} else {
			if(!$isrelation){
				$this->error ( L('_ERROR_').$model->getDBError().'=-='.$model->getLastSql() );
			}else{
				throw new NullPointExcetion( L('_ERROR_').$model->getDBError().'=+='.$model->getLastSql() );
				return false;
			}
		}
	}
	
	/**
	 * @Title: _over_update 
	 * @Description: todo(数据修改后动作) 
	 * @param string $modelname  模型名称
	 * @param int  $id   模型ID
	 * @param array $updateBackup  修改数据备份
	 * @param int $operatetype  操作类型 1新增 2修改3删除4变更  
	 * @author yangxi 
	 * @date 2015-5-14 下午3:07:53 
	 * @throws
	 */
	public function  _over_update($modelname,$id,$sourcetype,$updateBackup){
		logs("传入识别参数====$sourcetype",'DataControlsourcetype');
		//获取主模型名称
		$targetname=$this->getActionName();
		//echo $randnum;
		//echo $modelname."-".$id;
		//加入数据控制
		$this->dataControl($modelname,$sourcetype,$id,$targetname,1);
		//数据漫游,新增自动插入
		$this->dataRoam(1,$modelname,$id,$sourcetype,$updateBackup);
		//加入数据控制
		$this->dataControl($modelname,$sourcetype,$id,$targetname,2);
		//加入提醒
		$this->dataRemind($modelname,$sourcetype,$id,$targetname);
		if($sourcetype==5||$_POST['operateid']==1){
			$this->sysremind($modelname,$id);
		}
		//$this->error ('错误中断');
	}
	/**
	 * @Title: delete_record
	 * @Description: 删除表单时删除附件表、地址表、地图表数据
	 * @param string $modelName  模型名称
	 * @param int  $id   模型ID
	 * @author yangxi
	 * @date 2016-8-15 下午5:07:53
	 * @throws
	 */
	public function delete_record($id,$modelName){
		$addressmodel = D('MisAddressRecord');//地址
		$attachedmodel = M('mis_attached_record');//附件
		$mapmodel = M('mis_address_map_record');//地图
		if(C('AREA_TYPE')==1){
			$modelName = !empty($modelName) ? $modelName : $this->getActionName();
		}elseif(C('AREA_TYPE')==2){
			$modelName = !empty($modelName) ? $modelName : (D($this->getActionName())->getTableName());
		}else{
			$modelName = !empty($modelName) ? $modelName : $this->getActionName();
		}
		$map['tablename']=$modelName;
		$map['tableid']=$id;
		$addressresult=$addressmodel->where($map)->delete();
		$attachedresult=$attachedmodel->where($map)->delete();
		$mapresult=$mapmodel->where($map)->delete();
		
	}
	/**
	 +----------------------------------------------------------
	 * 默认主键删除操作
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 * @throws ThinkExecption
	 +----------------------------------------------------------
	 */
	public function delete() {
		//删除指定记录
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
				if(getFieldBy($name,"bindaname","inbindaname","mis_auto_bind")){
					//调用删除子项
					$retArr=$this->lookupdeletebind($name,$id,&$retArr);
					if($retArr){
						//循环删除项
						foreach ($retArr as $rkey=>$rval){
							$reModel=D($rkey);
							$ids=implode(',', $rval);
							$map=array();
							if($ids){
								$map['id']=array("in",$ids);
								$listArr=$reModel->where($map)->select();
								$result=$reModel->where($map)->delete();
								//删除备份数据
								logs($listArr,$rkey."test");
								logs("删除关联子项==".$reModel->getlastsql() ,"binddeletechild");
								if ($result==false) {
									$this->error ("关系表单数据删除失败,请联系管理员！");
								}  
							}
						}
					}
				}
				//这里用来存储临时缓存文件
// 				$updateBackup=self::setOldDataToCache($model,$name,$id,'delete');
				$updateBackup=$this->setOldDataToCache($model,$name,$id,'delete');
				//print_R( $id);
				if(1>1){
					//在此之前判断项目是否完毕
					//先判断是否存在静默生单且未被删除的数据
					$roamingid=M('MisSystemDataRoaming')->where ( "sourcemodel='".$name."' and sourceid=".$id )->getField('id');
					//获取下一行记录返回的存储值
					$roamingid=$roamingid+1;
					$targetid=M('MisSystemDataRoaming')->where ( "id=".$roamingid )->getField('sqlreturnval');
					if ($targetid  &&  is_int($targetid)) {
						//再判断该记录是否还存在，且未作废
							
					}
				
				}
				//执行成功后，用A方法进行实例化，判断当前控制器中是否存在_after_update方法
				$module2=A($name);
				//数据漫游
				/*
				 * _over_delete 方法，为静默插入生单。
				*/
				if (method_exists($module2,"_over_delete")) {
					//模型对象名与插入的id值
					$paramate=array($name,$id,$updateBackup);
					call_user_func_array(array(&$module2,"_over_delete"),$paramate);
					$this->unsetOldDataToCache($updateBackup);
// 					if($_POST['operateid']==1){
// 						//这里清除内存表缓存的修改前数据代码
// // 						self::unsetOldDataToCache($updateBackup);
// 						$this->unsetOldDataToCache($updateBackup);
// 					}
				}
				//删除附件表、地址表、地图表数据
				$this->delete_record($id);
				$condition = array ($pk => array ('eq',$id) );
				$list=$model->where ( $condition )->delete();
				// 				$list=true;
				if ($list!==false) {
					$this->success ( L('_SUCCESS_') );
				} else {
					$this->error ( L('_ERROR_') );
				}
			} else {
				$this->error ( C('_ERROR_ACTION_:只能单个删除！') );
			}
		}
	}
	/**
	 *
	 * @Title: lookupdeletebind
	 * @Description: todo(套表组合表数据删除)
	 * @param unknown $name
	 * @param unknown $id
	 * @author renling
	 * @date 2015年6月15日 下午5:18:33
	 * @throws
	 */
	public function lookupdeletebind($name,$id,&$retArr){
		$MisSystemDataRoamingModel=D("MisSystemDataRoaming");
		$MisAutoBindSettableModel=D("MisAutoBindSettable");
		//查询当前表单数据
		$actionmodel=D($name);
		$vo=$actionmodel->where("status=1 and id=".$id)->find();
		if($vo){
			$MisAutoBindModel=D("MisAutoBind");
			$MisSystemDataRoamSubDao=M("mis_system_data_roam_sub");
			if(getFieldBy($name, "bindaname", "inbindaname", "MisAutoBind","typeid",2)){
				//查询当前表单是否为套表主表
				$map=array();
				$map['status']=1;
				//查询当前模型绑定字段
				$map['bindaname']=$name;
				$map['typeid']=2;
				//允许同步删除
				$map['isdelete']=1;
				$MisAutoBindSettableList=$MisAutoBindSettableModel->where($map)->order("inbindsort asc")->select();
				if($MisAutoBindSettableList){
					foreach($MisAutoBindSettableList as $bskey=>$bsval){
						if($bsval['isdelete']==1){
							$model=D($bsval['inbindaname']);
							//获取真实表名
							$tname=$model->getTableName();
							$map=array();
							$bindmap=array();
							$listmap=array();
							$listmap[$bsval['inbindval']]=$vo[$bsval['bindval']];
							$bindmap[$bsval['inbindval']]=$vo[$bsval['bindval']];
							$bindmap['status']=1;
							//插入漫游数据进行查询 如果目标数据为空不单独处理
							$dataArr=$MisSystemDataRoamingModel->main(2,$actionname,$vo['id'],4,$val['inbindaname'],'',$val['dataroamid']);
							if(is_array(reset($dataArr))){
								foreach(reset($dataArr) as $targettable=>$data){
									foreach($data as $k => $v){
										if(reset($v)){
											$bindmap[key($v)]=reset($v);
										}
									}
								}
							}
// 							$MisSystemDataRoamSubList=$MisSystemDataRoamSubDao->where("masid=".$bsval['dataroamid']." and targettable='".$tname."'")->select();
// 							if($MisSystemDataRoamSubList){
// 								foreach ($MisSystemDataRoamSubList as $dskey=>$dsval){
// 									if($vo[$dsval['sfield']]){
// 										$bindmap[$dsval['tfield']]=$vo[$dsval['sfield']];
// 									}
// 								}
// 							}
							//查询绑定的附加条件
// 							$MisAtuoMap=array();
// 							$MisAtuoMap['bindaname']=$actionname;
// 							$MisAtuoMap['inbindaname']=$bsval['inbindaname'];
// 							$MisAutoBindSettableVo=$MisAutoBindSettableModel->where($MisAtuoMap)->find();
							if($bsval['inbindmap']){
								$newconditions = str_replace ( array ( '&quot;', '&#39;', '&lt;','&gt;'), array ('"',"'",'<','>'
								), $bsval['inbindmap'] );
								$bindmap['_string']=$newconditions;
							}
							$bindconlistArr=array();
							if($bsval['bindconlistArr']){
								$bindconlistArr=unserialize($bsval['bindconlistArr']);
								foreach ($bindconlistArr as $bindkey=>$bindval){
									//如果有值才生成查询
									if($vo[$bindkey]){
										$bindmap[$bindval]=$vo[$bindkey];
									}
								}
							}
							if($bsval['bindtype']==0){
								//表单型
								$list=$model->where($bindmap)->find();
								if($list){
									//组装数组
									$retArr[$bsval['inbindaname']][]=$list['id'];
									$this->lookupdeletebind($bsval['inbindaname'],$list['id'],&$retArr);
								}
							}else{
								//列表型
								$list=$model->where($listmap)->getField("id,orderno");
								if($list){
									//组装数组
									$retArr[$bsval['inbindaname']][]=implode(',', array_keys($list));
									foreach ($list as $lkey=>$lval){
										if($lkey){
											//调用当前方法
											$this->lookupdeletebind($bsval['inbindaname'],$lkey,&$retArr);
										}
									}
								}
							}
							//查询当前表单满足条件的数据
						}
					}
				}
			}else if (getFieldBy($name, "bindaname", "inbindaname", "MisAutoBind")){
				//查询当前表单是主从表 还是组合表
				$iszctpl=getFieldBy($name, "bindaname", "inbindaname", "MisAutoBind","typeid",1);
				if(!$iszctpl){//组合表单
					//获取绑定数组源
					$bindid=getFieldBy($vo['id'], "id", "orderno", $name);
					// 查询符合条件的表单
					$MisAutoBindVo=$MisAutoBindModel->where("status=1 and bindaname='{$name}'  and bindresult<>'' and typeid=0")->field("bindresult")->find();
					// 过滤掉可能的错误。
					$bindCondition = getFieldBy($bindid,"orderno",$MisAutoBindVo['bindresult'],$name);
					$bindMap['_string']="bindval={$bindCondition} or bindval='all'";
					$bindMap['status'] = 1;
					$bindMap['bindaname'] = $name;
					$MisAutoBindSettableList = $MisAutoBindModel->where($bindMap)->order("inbindsort asc")->getField("id,inbindaname,bindtype,bindaname,inbindtitle,inbindsort,isdelete,formshowtype,bindconlistArr,inbindmap,dataroamid");
				}else{
					$bindid=$vo['id'];
					// 查询符合条件的表单
					$MisAutoBindSettableList=$MisAutoBindModel->where("status=1 and bindaname='{$name}'  and typeid=1")->order("inbindsort asc")->select();
				}
				if($MisAutoBindSettableList){
					foreach ($MisAutoBindSettableList as $zckey=>$zcval){
						//选择同步删除
						if($zcval['isdelete']==1){
							$map = array();
							$map['status'] = 1;
							if($iszctpl){
								//组从表单需加此条件
								$map['bindid'] = $bindid;
								$map['relationmodelname'] = $name;
							}
							//根据数据漫游查找对应数据 改为根据附件条件查询关系数据
// 							$dataArr=$MisSystemDataRoamingModel->main(2,$name,$vo['id'],4,$zcval['inbindaname'],'',$zcval['dataroamid']);
// 							if(is_array(reset($dataArr))){
// 								foreach(reset($dataArr) as $targettable=>$data){
// 									foreach($data as $k => $v){
// 										//去掉为空判断
// 										if(reset($v)){
// 											$map[key($v)] = reset($v);
// 										}
// 									}
// 								}
// 							}
							//表单条件
							if($zcval['inbindmap']){
								$newconditions = str_replace ( array ( '&quot;', '&#39;', '&lt;','&gt;'), array ('"',"'",'<','>'
								), $zcval['inbindmap'] );
								$map['_string']=$newconditions;
							}
							$bindconlistArr=array();
							//表单附加条件
							if($zcval['bindconlistArr']){
								$bindconlistArr=unserialize($zcval['bindconlistArr']);
								foreach ($bindconlistArr as $bindkey=>$bindval){
									//如果有值才生成查询
									if($vo[$bindkey]){
										$map[$bindval]=$vo[$bindkey];
									}
								}
							}
							$model = D($zcval['inbindaname']);
							if($zcval['bindtype']==0){
								//表单型
								$list=$model->where($map)->find();
								logs($model->getlastsql(),"binddelereinfo");
								// 						if($name=="MisAutoEqe"){
								// 							echo $model->getlastsql();
								// 						}
								//组装数组
								if($list){
									$retArr[$zcval['inbindaname']][]=$list['id'];
									$this->lookupdeletebind($zcval['inbindaname'],$list['id'],&$retArr);
								}
							}else{
								//列表型
								$list=$model->where($map)->getField("id,orderno");
								logs($model->getlastsql(),"binddelereinfo");
								if($list){
									//组装数组
									$retArr[$zcval['inbindaname']][]=implode(',', array_keys($list));
									foreach ($list as $lkey=>$lval){
										if($lkey){
											//调用当前方法
											$this->lookupdeletebind($zcval['inbindaname'],$lkey,&$retArr);
										}
									}
								}
							}
						}
					}
				}
			}
			return $retArr;
		}
	}
	public function  _over_delete($modelname,$id,$updateBackup){
		//获取主模型名称
		$targetname=$this->getActionName();
		//加入数据控制
		$this->dataControl($modelname,3,$id,$targetname,1);
		//echo $modelname."-".$id;
		//数据漫游,删除触发
		$this->dataRoam(1,$modelname,$id,3,$updateBackup);
		//加入数据控制
		$this->dataControl($modelname,$sourcetype,$id,$targetname,2);
		//加入提醒
		$this->dataRemind($modelname,$sourcetype,$id,$targetname);
		//$this->error ('错误中断');
	}	
	/**
	 +----------------------------------------------------------
	 * 默认批量删除操作
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 * @throws ThinkExecption
	 +----------------------------------------------------------
	 */
	public function deleteBatch() {
		//删除指定记录
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );	}
				$list=$model->where ( $condition )->delete();
				if ($list!==false) {
					$this->success ( L('_SUCCESS_') );
				} else {
					$this->error ( L('_ERROR_') );
				}
			} else {
				$this->error ( C('_ERROR_ACTION_') );
		}
	}
	/**
	 +----------------------------------------------------------
	 * 默认批量更改状态操作
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 * @throws ThinkExecption
	 +----------------------------------------------------------
	 */
	public function batchangestatus() {
		//更改选定记录的状态
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$ids = $_REQUEST ['id'];
			$tostatus = $_GET ['tostatus'];
			switch ($tostatus){
				case 'forbid' :                        //禁用
				case 'recycle' : $tostatus = 0 ;break; //还原
				case 'delete' : $tostatus = -1 ;break; //删除
				case 'resume' : $tostatus = 1 ;break;  //恢复
				case 'approve' : $tostatus = 2 ;break; //批准 or 审核 待定。。
			}
			if (isset ( $ids )) {
				//echo M()->getLastsql();exit;
				$condition = array ($pk => array ('in', explode ( ',', $ids ) ) );
				//验证恢复或者还原时唯一性
				if($tostatus=="recycle"){
					$list_select=$model->where($condition)->select();
					foreach( $list_select as $k=>$v ){
						$v['status']=array("gt",-1);
						if(C('TOKEN_NAME')) $v[C('TOKEN_NAME')]= $_SESSION[C('TOKEN_NAME')];
						if (false === $model->create ($v)) {
							$this->error ( $model->getError () );
						}
					}
				}
				$list=$model->where ( $condition )->setField ( 'status', $tostatus );
				if ($list!==false) {
					$this->success ( L('_SUCCESS_') );
				} else {
					$this->error ( L('_ERROR_') );
				}
			} else {
				$this->error ( C('_ERROR_ACTION_') );
			}
		}
	}
	public function foreverdelete() {
		//删除指定记录
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				if (false !== $model->where ( $condition )->delete ()) {
					$this->success ( L('_SUCCESS_') );
				} else {
					$this->error ( L('_ERROR_') );
				}
			} else {
				$this->error ( C('_ERROR_ACTION_') );
			}
		}
		$this->forward ();
	}
	public function clear() {
		//删除指定记录
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			if (false !== $model->where ( 'status=-1' )->delete ()) { // zhanghuihua@msn.com change status=1 to status=-1
				//$this->assign ( "jumpUrl", $this->getReturnUrl () );
				$this->success ( L ( '_SUCCESS_' ) );
			} else {
				$this->error ( L ( '_ERROR_' ) );
			}
		}
		$this->forward ();
	}
	/**
	 +----------------------------------------------------------
	 * 默认禁用操作
	 *
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 * @throws FcsException
	 +----------------------------------------------------------
	 */
	public function forbid() {
		$name=$this->getActionName();
		$model = D ($name);
		$pk = $model->getPk ();
		$id = $_GET [$pk];
		$condition = array ($pk => array ('in', $id ) );
		if (false !== $model->forbid ( $condition )) {
			$this->success ( L ( '_SUCCESS_' ) );
		} else {
			$this->error ( L ( '_ERROR_' ) );
		}
	}
	/**
	 * @Title: recycle
	 * @Description: 禁用后还原状态方法
	 * @author liminggang
	 * @date 2014-9-13 下午5:39:45
	 * @throws
	 */
	public function recycle() {
		$name = $this->getActionName();
		$model = D ($name);
		$pk = $model->getPk ();
		$id = $_GET [$pk];
		$condition = array ($pk => array ('in', $id ) );

		//验证还原时唯一性
		$list_select=$model->where($condition)->select();
		if(isset($list_select['status'])){
			$list_select['status']=array("gt",-1);
			if (false === $model->create ($v)) {
				$this->error ( $model->getError () );
			}
		}

		if (false !== $model->recycle ( $condition )) {
			$this->success ( L ( '_SUCCESS_' ) );
		} else {
			$this->error ( L ( '_ERROR_' ) );
		}
	}
	/**
	 +----------------------------------------------------------
	 * 默认恢复操作
	 *
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 * @throws FcsException
	 +----------------------------------------------------------
	 */
	function resume() {
		//恢复指定记录
		$name=$this->getActionName();
		$model = D ($name);
		$pk = $model->getPk ();
		$id = $_GET [$pk];
		$condition = array ($pk => array ('in', $id ) );
		if (false !== $model->resume ( $condition )) {
			$this->success ( L ( '_SUCCESS_' ) );
		} else {
			$this->error ( L ( '_ERROR_' ) );
		}
	}

	/**
	 * @Title: getPinyin
	 * @Description: todo(自动往表里面插入某个字段的大写首字母拼音值)
	 * @author wangcheng
	 * @param @return bool 是否返回
	 * @param whstatus  1,0  仓库状态
	 * @date 2013-12-2
	 */
	public function getPinyin($table,$sourceField,$pkval=0){
		$getPinyinSql="";
		if(C('DB_TYPE')=='mysql'){
			$getPinyinSql="
					CREATE FUNCTION `getPinyin`(`in_string` varchar(255) charset gb2312) RETURNS varchar(255)
					DETERMINISTIC
					BEGIN
					DECLARE tmp_str VARCHAR(255) charset gb2312 DEFAULT '' ; #截取字符串，每次做截取后的字符串存放在该变量中，初始为函数参数in_string值
					DECLARE tmp_len SMALLINT DEFAULT 0;#tmp_str的长度
					DECLARE tmp_char VARCHAR(2) charset gb2312 DEFAULT '';#截取字符，每次 left(tmp_str,1) 返回值存放在该变量中
					DECLARE tmp_rs VARCHAR(255) charset gb2312 DEFAULT '';#结果字符串
					DECLARE tmp_cc VARCHAR(2) charset gb2312 DEFAULT '';#拼音字符，存放单个汉字对应的拼音首字符

					SET tmp_str = in_string;#初始化，将in_string赋给tmp_str
					SET tmp_len = LENGTH(tmp_str);#初始化长度
					WHILE tmp_len > 0 DO #如果被计算的tmp_str长度大于0则进入该while
					SET tmp_char = LEFT(tmp_str,1);#获取tmp_str最左端的首个字符，注意这里是获取首个字符，该字符可能是汉字，也可能不是。
					SET tmp_cc = tmp_char;#左端首个字符赋值给拼音字符
					IF LENGTH(tmp_char)>1 THEN#判断左端首个字符是多字节还是单字节字符，要是多字节则认为是汉字且作以下拼音获取，要是单字节则不处理。
					SELECT ELT(INTERVAL(CONV(HEX(tmp_char),16,10),0xB0A1,0xB0C5,0xB2C1,0xB4EE,0xB6EA,0xB7A2,0xB8C1,0xB9FE,0xBBF7,0xBFA6,0xC0AC
					,0xC2E8,0xC4C3,0xC5B6,0xC5BE,0xC6DA,0xC8BB,0xC8F6,0xCBFA,0xCDDA ,0xCEF4,0xD1B9,0xD4D1),
					'A','B','C','D','E','F','G','H','J','K','L','M','N','O','P','Q','R','S','T','W','X','Y','Z') INTO tmp_cc;   #获得汉字拼音首字符
					END IF;
					SET tmp_rs = CONCAT(tmp_rs,tmp_cc);#将当前tmp_str左端首个字符拼音首字符与返回字符串拼接
					SET tmp_str = SUBSTRING(tmp_str,2);#将tmp_str左端首字符去除
					SET tmp_len = LENGTH(tmp_str);#计算当前字符串长度
					END WHILE;
					RETURN tmp_rs;#返回结果字符串
					END	";
		}
		//echo $getPinyinSql;
		$Model=M($table);
		$fieldList=$Model->query("SHOW COLUMNS FROM `$table`");
		//print_r($fieldList);
		$pinyin=false;
		$where="";
		foreach($fieldList as $key => $val){
			if($val['Field']=='id'){
				if($pkval!==0){
					$where='or id='.$pkval;
				}
			}
			if($val['Field']=='pinyin'){
				$pinyin=true;
				break;
			}
		}
		if($pinyin===false){
			//插入语句
			$Model->execute("alter table `$table` add `pinyin` varchar(20) null   DEFAULT 0  COMMENT 'pinyin'");
		}
		$result=$Model->execute("UPDATE `$table` SET pinyin= getPinyin(`$sourceField`) WHERE  pinyin =0 ".$where);
		if($result===false){
			//插入初始化函数
			//如果是Mysql需要开启函数开关,解决1418
			if(C('DB_TYPE')=='mysql'){
				$Model->execute("SET GLOBAL log_bin_trust_function_creators = 1");
			}
			$result=$Model->execute($getPinyinSql);
			if($result===false){
				//屏蔽错误
				//$this->error('生成getPinyin函数失败,请联系管理员');
			}else{
				$result=$Model->execute("UPDATE `$table` SET pinyin= getPinyin(`$sourceField`) WHERE  pinyin =0 ".$where);
				if($result===false){
					//屏蔽错误
					//$this->error('调用getPinyin函数失败,请联系管理员');
				}
			}
		}
	}
	/*
	 * 导出excel
	 * @author  wangcheng
	 * date:2012-03-16
	 * $data_head:excel标题
	 * $data:excel 数据
	 * return:返回excel文件
	 */
	public function export_excel($data_head=array(),$data_head_hebing=array(),$data=array(),$before_head_data=array()){
		import('@.ORG.PHPExcel', '', $ext='.php');
		$objPHPExcel = new PHPExcel();
		$c = count($data_head);
		foreach($data_head as $k=>$v){
			$arrkey[]=$k;
			$arrtitle[]=$v;
		}
		$a=array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z",
				"AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU",
				"AV","AW","AX","AY","AZ","BA","BB","BC","BD");
		for($i=0;$i<$c;$i++){
			$objPHPExcel->getActiveSheet()->setCellValue($a[$i]."1",$arrtitle[$i]);
			$objPHPExcel->getActiveSheet()->getStyle($a[$i]."1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);//头部居中
		}
		if( count($data_head_hebing)> 0 ){
			$objPHPExcel->getActiveSheet()->insertNewRowBefore();//插入整行
			for($i=0;$i<$c;$i++){
				$have=false;
				foreach( $data_head_hebing as $k=>$v ){
					if($arrtitle[$i]==$v['startColumnName']){
						$have=true;
						break;
					}
				}
				if( $have ){
					$no = $v['numberOfColumns'];
					$s=$a[$i]."1:".$a[$i+$no-1]."1";
					$objPHPExcel->getActiveSheet()->mergeCells( $s );//合并
					$objPHPExcel->getActiveSheet()->setCellValue($a[$i]."1",strip_tags($v['titleText']));
					$objPHPExcel->getActiveSheet()->getStyle($a[$i]."1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);//头部居中
					$i=$i+2;
				}else{
					$s=$a[$i]."1:".$a[$i]."2";
					$value=$objPHPExcel->getActiveSheet()->getCell($a[$i]."2")->getValue();
					$objPHPExcel->getActiveSheet()->mergeCells( $s );//合并
					$objPHPExcel->getActiveSheet()->setCellValue($a[$i]."1",$value);
					$objPHPExcel->getActiveSheet()->getStyle($a[$i]."1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);//头部居中
				}
			}
		}

		if( count($before_head_data)> 0 ){
			foreach($before_head_data as $k=>$v ){
				$objPHPExcel->getActiveSheet()->insertNewRowBefore();//插入整行
				for($i=0;$i<count($v);$i++){
					if( $v["numberOfColumns"] ){
						$no = $v['numberOfColumns'];
						$s=$a[$i]."1:".$a[$i+$no-1]."1";
						$objPHPExcel->getActiveSheet()->mergeCells( $s );//合并
						$objPHPExcel->getActiveSheet()->setCellValue($a[$i]."1",strip_tags($v['titleText']));
						$objPHPExcel->getActiveSheet()->getStyle($a[$i]."1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);//头部居中
						$i=$i+2;
					}else{
						$s=$a[$i]."1:".$a[$i]."2";
						$value=$objPHPExcel->getActiveSheet()->getCell($a[$i]."2")->getValue();
						$objPHPExcel->getActiveSheet()->mergeCells( $s );//合并
						$objPHPExcel->getActiveSheet()->setCellValue($a[$i]."1",$value);
					}
				}
				$objPHPExcel->getActiveSheet()->getStyle($a[$i]."1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);//头部居中

			}
			$baseRow =3;
		}else{
			$baseRow =2;
		}

		foreach($data as $r => $dataRow){
			$row = $baseRow + $r;
			for($i=0;$i<$c;$i++){
				$style='';
				if(  $dataRow[$arrkey[$i]] ){
					$dataRow->$arrkey[$i]=(string)html_entity_decode($dataRow[$arrkey[$i]]);
					$objPHPExcel->getActiveSheet()->setCellValue($a[$i].$row,$dataRow[$arrkey[$i]],$style);
				}
			}
		}

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objPHPExcel->getActiveSheet()->setTitle('Sheet1');
		$objPHPExcel->setActiveSheetIndex(0);
		// Redirect output to a client’s web browser (Excel5)
		ob_end_clean();//清除缓冲区,避免乱码
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.time().'.xls"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}

	/*
	 * 导出excel
	 * @author  wangcheng
	 * date:2012-03-16
	 * $data_head:excel标题
	 * $data:excel 数据
	 * return:返回excel文件
	 */
	public function export_excel2($data_head=array(),$data_head_hebing=array(),$data=array()){
		import('@.ORG.PHPExcel', '', $ext='.php');
		$objPHPExcel = new PHPExcel();
		$c = count($data_head);
		foreach($data_head as $k=>$v){
			$arrkey[]=$k;
			$arrtitle[]=$v;
		}
		$a=array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z",
				"AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ");
		for($i=0;$i<$c;$i++){
			$objPHPExcel->getActiveSheet()->setCellValue($a[$i]."1",$arrtitle[$i]);
			$objPHPExcel->getActiveSheet()->getStyle($a[$i]."1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);//头部居中
		}
		$baseRow = 2;
		foreach($data as $r => $dataRow){
			$row = $baseRow + $r;
			for($i=0;$i<$c;$i++){
				$style='';
				if(  isset($dataRow[$arrkey[$i]])){
					$dataRow[$arrkey[$i]]=(string)html_entity_decode($dataRow[$arrkey[$i]]);
					$objPHPExcel->getActiveSheet()->setCellValue($a[$i].$row,$dataRow[$arrkey[$i]],$style);
				}
			}
		}
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objPHPExcel->getActiveSheet()->setTitle('Sheet1');
		$objPHPExcel->setActiveSheetIndex(0);
		ob_end_clean();//清除缓冲区,避免乱码
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.time().'.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;
	}

	/**
	 * 导出固定格式excel数据文件
	 * @author  wangcheng
	 * @param string  $tpl_excel_file 模板excel文件的路劲
	 * @param array	  $data		  数组	array("D2"=>"D2位置数据","F4"=>"f4位置的数据")
	 * @param array	  $cycdata	  数组  $cycdata=array(
	 * 											"7"=>array(array("a1","b1","c1"),array("a2","b2","c2"),array("a3","b3","c3")),
	 *					     					"9"=>array(array("a6","b6","c6"),array("a7","b7","c7")),
	 *					     					"11"=>array(array("a6","b6","c6"),array("a7","b7","c7"))
	 *				      					);
	 * @return excelfile
	 */
	function outExcelBytpl($tpl_excel_file="", $data =array() ,$cycdata=array()){
		import('@.ORG.PHPExcel', '', $ext='.php');
		$objReader = PHPExcel_IOFactory::createReader('Excel5');
		$objPHPExcel = $objReader->load( $tpl_excel_file );
		foreach($data as $row => $d) {
			$objPHPExcel->getActiveSheet()->setCellValue($row, $d);
		}
		if($cycdata){
			$a=array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z",
					"AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ");
			$baseRow =$baseaddRow=0;
			foreach($cycdata as $k => $list) {
				$j=false;
				foreach($list as $k2 => $dataRow) {
					$i=0;
					$row = $k + $k2+$baseaddRow;
					if($j) $objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
					$j=true;
					foreach($dataRow as $k3=>$v3){
						$objPHPExcel->getActiveSheet()->setCellValue($a[$i].$row,$v3);
						$i++;
					}
				}
				$baseaddRow+=count($list)-1;
			}
		}

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$newname = end(explode(".",$tpl_excel_file));
		ob_end_clean();//清除缓冲区,避免乱码
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.time().'.'.$newname);
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
		exit;
		//$objWriter->save( UPLOAD_PATH."b.xls");
	}

	/**
	 * @Title: exportsSample
	 * @Description: todo(原样导出excel方便打印)
	 * @author liminggang
	 * @date 2013-4-23 下午2:42:34
	 * @throws
	 */
	function exportsSample($step=1){
		//获得当前选中的ID值
		$id=$_GET['id'];
		$isprint = $_GET['isprint'] ? $_GET['isprint'] : 0;
		$name=$this->getActionName();
		$model=D($this->getActionName());
		//查询数据
		$volist=$model->where('id='.$id)->select();
		//获取审核意见信息
		$auditdate = $this->getTsDate($volist, $name);
			
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($name,false);
		//定义数组
		$data=array();
		foreach($volist as $k=>$v ){
			$data[$k]=array();
			foreach($detailList as $k2=>$v2){
				$data[$k][$v2['name']]="";
				if(count($v2['func']) >0){
					foreach($v2['func'] as $k3=>$v3){
						if(isset($v2['extention_html_start'][$k3])){
							$data[$k][$v2['name']]=$v2['extention_html_start'][$k3];
						}

						$data[$k][$v2['name']] .= getConfigFunction($v[$v2['name']],$v3,$v2['funcdata'][$k3],$volist[$k],true,$v2["domainid"]);

						if(isset($v2['extention_html_end'][$k3])){
							$data[$k][$v2['name']].=$v2['extention_html_end'][$k3];
						}
					}
				}else{
					$data[$k][$v2['name']]= $v[$v2['name']];
				}
			}
		}
		//读取导出模板路劲,这里execl模板的名字。必须模型名字一样。
		$filePath=UPLOAD_Sample."".$name.".xls";

		//调用导出方法
		$this->exports_exc($data[0],$filePath,$step,$tsdate=array(),$isprint,$auditdate);
	}
	/**
	 * @Title: getTsDate
	 * @Description: todo(获取审核意见信息)
	 * @param 存储流程节点ID和当前导出数据ID $volist
	 * @param 当前控制器名称 $name
	 * @return 返回一个带有审核信息的二维数组
	 * @author liminggang
	 * @date 2014-5-5 下午5:46:21
	 * @throws
	 */
	private function getTsDate($volist,$name){
		$ptmp = $volist[0];
		$pihmodel = M('process_info_history');//构造流程明细模型
		$ptmodel = D('process_template');//构造流程节点模型
		//查询最新流程
		$list = array();
		if($ptmp['ptmptid']){
			$pihmap = array();
			$pihmap['pid'] = $volist[0]['ptmptid'];
			$pihmap['tableid']	= $volist[0]['id'];
			$pihmap['tablename']= $name;
			$pihmap['ostatus'] = array('gt',0);
			$pihlist = $pihmodel->where($pihmap)->field("ostatus,userid,doinfo")->order('id asc')->select();
			//获取流程节点
			$allnode = $auditUser = array();
			if($ptmp['allnode'] ) $allnode = explode(',', $ptmp['allnode']);//所有节点
			if($ptmp['auditUser']) $auditUser = explode(';', $ptmp['auditUser']);//所有用户
			foreach ($allnode as $k => $v) {
				$list[$k]['tid'] = $v;
				$list[$k]['user'] = $auditUser[$k];
				if($pihlist){
					foreach($pihlist as $key=>$val){
						if($v == $val['ostatus']){
							$list[$k]['doinfo'] = $val['doinfo'];
							$list[$k]['name'] = $ptmodel->where('id='.$v)->getField('name')."(".getFieldBy($val['userid'],'id','name','user').")";
						}
					}
				}else{
					$list[$k]['doinfo'] = "";
					$list[$k]['name'] = $ptmodel->where('id='.$v)->getField('name');
				}
			}
		}
		return $list;
	}
	/**
	 * @Title: exports_exc
	 * @Description: todo(导出execl或者PDF或者直接打印)
	 * @param 导出的数据 $date
	 * @param 导出文件模板路径 $filePath
	 * @param 状态值 $step	1：表示答应或者导出execl 其他代表导出PDF
	 * @param 其他指定坐标信息 $tsdate		$tsdate=array('A1'=>$_POST['projectname'].$proname);
	 * @param 打印标记 $isprint	1表示打印
	 * @param 审核信息数组 $auditdate
	 * @author liminggang
	 * @date 2014-5-5 下午5:47:53
	 * @throws
	 */
	public function exports_exc($date,$filePath,$step=1,$tsdate=array(),$isprint,$auditdate=array()){
		import('@.ORG.PHPExcel','', $ext='.php');
		//创建一个PHPExcel对象
		$objReader = PHPExcel_IOFactory::createReader('Excel5');
		$objPHPExcel=$objReader->load($filePath);
		/**读取excel文件中的第一个工作表*/
		$currentSheet = $objPHPExcel->getSheet(0);
		/**取得最大的列号*/
		$allColumn = $currentSheet->getHighestColumn();
		/**取得一共有多少行*/
		$allRow = $currentSheet->getHighestRow();
		for($currentRow = 2;$currentRow <= $allRow;$currentRow++){
			/**从第A列开始输出*/
			for($currentColumn= 'A';$currentColumn<= $allColumn; $currentColumn++){
					
				$val = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65,$currentRow)->getValue();/**ord()将字符转为十进制数*/
				/**如果输出汉字有乱码，则需将输出内容用iconv函数进行编码转换，如下将gb2312编码转为utf-8编码输出
				 * 如果存在ereg判断汉字问题。则必须用utf-8，不然不识别汉字
				 * */
				$val =  iconv('utf-8','utf-8', $val)."\t";
				$val = trim($val);
				if (isset($date[$val])) {
					$objPHPExcel->getActiveSheet()->setCellValue($currentColumn.$currentRow, $date[$val],'');
				}
			}
		}
		if($auditdate){
			$coutnrow = $allRow+1;
			$style=array(
					'font'    => array(
							'name'      => '黑体',
							'bold'      => true,
							'italic'    => false,
			),
					'borders' => array(
							'bottom'     => array(
									'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
			),
							'top'     => array(
									'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
			),
							'left'     => array(
									'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
			),
							'right'     => array(
									'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
			),
			),
					'alignment' =>array(
							'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			),
			);
			foreach($auditdate as $k=>$v){
				for($currentColumn= 'A';$currentColumn<= $allColumn; $currentColumn++){
					$objPHPExcel->getActiveSheet()->getStyle($currentColumn.$coutnrow)->applyFromArray($style);
				}
				$objPHPExcel->getActiveSheet()->setCellValue("A".$coutnrow, $v['name']);
				$objPHPExcel->getActiveSheet()->mergeCells("B".$coutnrow.":".$allColumn.$coutnrow)->setCellValue("B".$coutnrow, $v['doinfo']);
				$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(40);
				$coutnrow++;
			}
		}
		foreach($tsdate as $row => $d) {
			$objPHPExcel->getActiveSheet()->setCellValue($row, $d);
		}
		if($step==1){
			if($isprint==1){//打印
				$objExcel = new PHPExcel();
				$objWriteHTML = new PHPExcel_Writer_HTML($objPHPExcel); //输出网页格式的对象
				$objWriteHTML->save(UPLOAD_Sample."tbprint.html");
				echo json_encode(UPLOAD_Sample."tbprint.html");
			}else{//转execl
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				ob_end_clean();//清除缓冲区,避免乱码
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment;filename="'.time().'.xls"');
				header('Cache-Control: max-age=0');
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				$objWriter->save('php://output');
			}
		}else{
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'pdf');
			//设置文件头信息
			header('Content-Type: application/pdf');
			header('Content-Disposition: attachment;filename="'.time().'.pdf"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'pdf');
			//设置字体
			$objWriter->SetFont('arialunicid0-chinese-simplified');
			$objWriter->save('php://output');
		}
		exit;
	}

	/*
	 * 导出PDF
	 * @author  jiangx
	 * date:2013-04-27
	 *$header 显示的header
	 *$data   显示的数据
	 *$title  头部显示类容
	 *$fontsize 内容字体大小
	 * return:返回PDF文件
	 */
	public function getByTCPDF($header = array(),$data = array(),$title,$fontsize = 10){
		import('@.ORG.tcpdf.tcpdf', '', $ext='.php');
		$l = Array();
		// PAGE META DESCRIPTORS
		$l['a_meta_charset'] = 'UTF-8';
		$l['a_meta_dir'] = 'ltr';
		$l['a_meta_language'] = 'en';
		// TRANSLATIONS
		$l['w_page'] = 'page';
		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		//$pdf->SetAuthor('');
		$pdf->SetTitle($title);
		//$pdf->SetSubject('TCPDF Tutorial');
		//$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
		// 设置头部显示数据
		//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $title, '作者与版权');
		//$pdf->SetHeaderData(LOGO图片, 图片宽度, 标题, PDF_HEADER_STRING);
		// 设置页眉和页脚的字体
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// 设置页边距
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		//设置自动换行
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		//set some language-dependent strings
		$pdf->setLanguageArray($l);

		// 设置字体
		$pdf->SetFont('stsongstdlight', '', $fontsize);
		//$pdf->SetFont('cid0jp', '', 8);//东亚字体

		// add a page
		$pdf->AddPage();
		$pdf->writeHTML('<h2>'.$title.'</h2>', true, false, false, false, '');
		//$pdf->Write(0, $title, '', 0, 'L', true, 0, false, false, 0);
		// Table
		$tbl = '';
		$tbl .= '<table border="1" cellpadding="2" cellspacing="0"><tr>';
		foreach ($header as $key=>$val) {
			$tdstr ='<td align="center"><b>'.$val.'</b></td>';
			$tbl .= $tdstr;
		}
		$tbl .= '</tr>';
		foreach ($data as $vaule) {
			$tbl .= '<tr>';
			foreach ($header as $key => $val) {
				$tdstr ='<td align="center">'.$vaule[$key].'</td>';
				$tbl .= $tdstr;
			}
			$tbl .= '</tr>';
		}
		$tbl .= '</table>';
		$pdf->writeHTML($tbl, true, false, false, false, '');
		$excel_file = time().".pdf";
		$pdf->Output($excel_file,'I');
	}

	/**
	 * @Title: exportBysearchHtml
	 * @Description: 列表导出按钮输出字段选择模板方法。  配合exportBysearch导出功能。
	 * @author wangcheng
	 * @date 2014-9-13 下午2:17:01
	 * @throws
	 */
	public function exportBysearchHtml(){
		$name=$this->getActionName();
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($name,false);
		$newdetail = array();
		foreach($detailList as $k=>$v){
			if(isset($v['isexport']) && $v['isexport']==1){
				$newdetail[]=$v;
			}
		}
		$formid = isset($_POST['formid'])? $_POST['formid']:"";
		$this->assign("formid",$formid);
		$this->assign("fieldarr",$newdetail);
		$this->display ("Public:exportBysearchHtml");
	}

	/**
	 * @Title: exportBysearch
	 * @Description: 列表导出方法。针对所有的列表数据导出。
	 * @param array 导出的数据 $volist
	 * @param string 对应的控制器名称 $name
	 * @author wangcheng
	 * @date 2014-9-13 下午2:15:34
	 * @throws
	 */
	public function exportBysearch($volist=array(),$name=""){
		$name=$name ? $name:$this->getActionName();
		$scdmodel = D('SystemConfigDetail');
		if(substr($name, -4)=="View"){
			$name = substr($name,0, -4);
		}
		$detailList = $scdmodel->getDetail($name,false);
		$data_head=array();
		$field= isset($_POST['export_out_field']) ? $_POST['export_out_field']:array();

		foreach($detailList as $k=>$v){
			if(isset($v['isexport']) && $v['isexport']==1 && in_array($v['name'],$field)){
				$data_head[$v['name']]=$v['showname'];
			}
		}
		$data=array();
		foreach($volist as $k=>$v ){
			$data[$k]=array();
			foreach($detailList as $k2=>$v2){
				if(isset($v2['isexport']) && $v2['isexport']==1){
					$data[$k][$v2['name']]="";
					if(count($v2['func']) >0){
						foreach($v2['func'] as $k3=>$v3){
							if(isset($v2['extention_html_start'][$k3])){
								$data[$k][$v2['name']]=$v2['extention_html_start'][$k3];
							}

							$data[$k][$v2['name']] .= getConfigFunction($v[$v2['name']],$v3,$v2['funcdata'][$k3],$volist[$k],true,$v2['domainid']);

							if(isset($v2['extention_html_end'][$k3])){
								$data[$k][$v2['name']].=$v2['extention_html_end'][$k3];
							}
						}
					}else{
						$data[$k][$v2['name']]= $v[$v2['name']];
					}
				}
			}
		}

		import('@.ORG.PHPExcel', '', $ext='.php');
		$objPHPExcel = new PHPExcel();
		$c = count($data_head);
		foreach($data_head as $k=>$v){
			$arrkey[]=$k;
			$arrtitle[]=$v;
		}
		$a=array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z",
				"AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ");
		for($i=0;$i<$c;$i++){
			$objPHPExcel->getActiveSheet()->setCellValue($a[$i]."1",$arrtitle[$i]);
			$objPHPExcel->getActiveSheet()->getStyle($a[$i]."1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);//头部居中
		}
		$baseRow = 2;
		foreach($data as $r => $dataRow){
			$row = $baseRow + $r;
			for($i=0;$i<$c;$i++){
				$style='';
				if(  isset($dataRow[$arrkey[$i]])){
					$dataRow[$arrkey[$i]] = strip_tags($dataRow[$arrkey[$i]]);
					$dataRow[$arrkey[$i]]=(string)html_entity_decode($dataRow[$arrkey[$i]]);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($a[$i].$row,$dataRow[$arrkey[$i]],PHPExcel_Cell_DataType::TYPE_STRING);
					//$objPHPExcel->getActiveSheet()->setCellValue($a[$i].$row,$dataRow[$arrkey[$i]],$style);
				}
			}

		}
		for($i=0;$i<$c;$i++){
			$objPHPExcel->getActiveSheet()->getColumnDimension($a[$i])->setAutoSize(true);
		}
			
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objPHPExcel->getActiveSheet()->setTitle('Sheet1');
		$objPHPExcel->setActiveSheetIndex(0);
		ob_end_clean();//清除缓冲区,避免乱码
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.time().'.xls"');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
		exit;
	}

	/**
	 * 通用多类型转换
	 * @author  wangcheng
	 * @param $mixed
	 * @param $isint
	 * @param $istrim
	 * @return mixture
	 */
	function escapeChar($mixed, $isint = false, $istrim = false) {
		if (is_array ( $mixed )) {
			foreach ( $mixed as $key => $value ) {
				$mixed [$key] = $this->escapeChar ( $value, $isint, $istrim );
			}
		} elseif ($isint) {
			$mixed = ( int ) $mixed;
		} elseif (! is_numeric ( $mixed ) && ($istrim ? $mixed = trim ( $mixed ) : $mixed) && $mixed) {
			$mixed = $this->escapeStr ( $mixed );
		}
		return $mixed;
	}
	/**
	 * 字符转换
	 * @author  wangcheng
	 * @param $string
	 * @return string
	 */
	function escapeStr($string) {
		$string = (MAGIC_QUOTES_GPC && is_string($string))?   stripslashes($string)  :  $string;
		$string = trim($string);
		if(!$_POST["iseditor"]){
			$string = str_replace ( array ("\0", "%00", "\r" ), '', $string );
			$string = preg_replace ( array ('/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F]/', '/&(?!(#[0-9]+|[a-z]+);)/is' ), array ('', '&amp;' ), $string );
			$string = str_replace ( array ("%3C", '<' ), '&lt;', $string );
			$string = str_replace ( array ("%3E", '>' ), '&gt;', $string );
			$string = str_replace ( array ('"', "'", "\t"), array ('&quot;', '&#39;', '    '), $string );
		}
		return $string;
	}
	/**
	 +----------------------------------------------------------
	 * 获取字段信息并缓存
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return void
	 * @author wangcheng
	 * @date:2012-03-27
	 +----------------------------------------------------------
	 */
	public function getFields($tableName) {
		$m=M();
		$result =   $m->query('SHOW COLUMNS FROM '.$tableName);
		$info   =$fields=  array();
		foreach ($result as $key => $val) {
			$info[$val['Field']] = array(
					'name'    => $val['Field'],
					'type'    => $val['Type'],
					'notnull' => (bool) ($val['Null'] === ''), // not null is empty, null is yes
					'default' => $val['Default'],
					'primary' => (strtolower($val['Key']) == 'pri'),
					'autoinc' => (strtolower($val['Extra']) == 'auto_increment'),
			);
		}
		$fields   =   array_keys($info);
		return $fields;
	}
	/**
	 +----------------------------------------------------------
	 * 设置活动id cookies 暂时只用于模板页面调用,待扩展
	 +----------------------------------------------------------
	 * @author wangcheng
	 * @date:2012-04-28
	 * @param string  $data
	 +----------------------------------------------------------
	 * @return json  返回ajax 或者其他格式
	 +----------------------------------------------------------
	 */
	function setActiveCookie( $data="" ,$ajax=true){
		if( empty($data) ){
			$data=$this->escapeChar($_POST);
			foreach($data as $k=>$v ){
				if( Cookie::is_set($k) ) Cookie::delete($k);
				if( $v!='' ) Cookie::set($k,$v);
			}
			//exit;
			$this->success ( L('_SUCCESS_') ,$ajax);
		}else{
			foreach($data as $k=>$v ){
				if( Cookie::is_set($k) ) Cookie::delete($k);
				if( $v!='' ) Cookie::set($k,$v);
			}
		}
	}
	/**
	 * @Title: regex
	 * @Description: 对值进行正则验证
	 * @param string 值 $value
	 * @param string 正则 $rule
	 * @return string
	 * @author liminggang
	 * @date 2014-9-13 下午2:23:13
	 * @throws
	 */
	public function regex($value,$rule) {
		$validate = array(
				'require'=> '/.+/',
				'email' => '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
				'url' => '/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/',
				'number' => '/\d+$/',
				'zip' => '/^[1-9]\d{5}$/',
				'integer' => '/^[-\+]?\d+$/',
				'double' => '/^[-\+]?\d+(\.\d+)?$/',
				'english' => '/^[A-Za-z]+$/',
		);
		$alert = array(
				'require'=> '必填项目',
				'email' => '邮箱格式出错(ex:admin@163.com)',
				'url' => '网址格式出错(ex:http://www.aa.com)',
				'number' => '必须数字',
				'zip' => '/^[1-9]\d{5}$/',
				'integer' => '必须整数',
				'double' => '必须浮点数',
				'english' => '必须字母',
		);
		$backalert="";
		if(isset($validate[strtolower($rule)])) $rule   =   $validate[strtolower($rule)];
		if( !preg_match($rule,$value)){
			$backalert=$alert[$rule];
		}
		return $backalert;
	}


	/*@wangcheng 短信发送通用方法
	 * @parameter  string  $content  短信内容
	 * @parameter  array   $telarr   电话号码（数组结构多电话）
	 */
	function  SendTelmMsg($content,$telarr){
		if($content==""){
			$this->error( "不能发送空短信");
		}
		if(is_array($telarr)){
			$tel=array();
			foreach($telarr as $k=>$v){
				$tel[] = $v;
			}
			$telnum=implode(",",$tel);
		}else{
			$telnum =$telarr;
		}
		$settingmsginfo=C("SendPhoneMsg");
		$CorpID =$settingmsginfo['CorpID'];
		$Pwd	=$settingmsginfo['Pwd'];
		//判断操作系统
		// 		$agent=$_SERVER["HTTP_USER_AGENT"];
		// 		if(eregi("win",$agent)){
		// 			import ( '@.ORG.Snoopy' );
		// 			$snoopy=new Snoopy();
		// 			$content= iconv("UTF-8","GB2312",$content);
		// 			$aryPara = array('CorpID' => $CorpID, 'Pwd'=> $Pwd, 'Mobile'=> $telnum, 'Content'=> $content,'Cell'=>'','SendTime'=>'');
		// 			$snoopy->submit("http://www.ht3g.com/htWS/linkWS.asmx/BatchSend",$aryPara);//$formvars为提交的数组
		// 			return $snoopy->results;
		// 		}else{
		import('@.ORG.TelMsg.nusoap', '', $ext='.php');
		$client = new SoapClient('http://www.ht3g.com/htWS/linkWS.asmx?WSDL');
		$aryPara = array('CorpID' => $CorpID, 'Pwd'=> $Pwd, 'Mobile'=> $telnum, 'Content'=> $content,'Cell'=>'','SendTime'=>'');
		$re = $client->__call('BatchSend',array('parameters'=> $aryPara));
		return $re->BatchSendResult;
		// 		}
	}
	/**
	 * @Title: sendAuditMsg
	 * @Description: todo(手机短信发送)
	 * @param int $id 当前模型数据ID
	 * @param string $m  控制器名称
	 * @param id $userid 后台用户ID
	 * @param unknown_type $systemtype
	 * @author liminggang
	 * @date 2013-7-12 下午4:41:45
	 * @throws
	 */
	public function sendAuditMsg($id="",$m="",$userid="",$systemtype=0){
		$md = $m ? $m:$this->getActionName();
		//第一步、获取需要发送的短信内容
		$content=$this->setProcessModelMesg($md,$id);
		//第二步、获取系统自带的内容,在判断内容大小
		import('@.ORG.String');
		$code = String::rand_string( 4,1);
		//第三步、验证回复验证码是否有重复
		$MessageVerifyDao=M('message_verify');
		$map=array();
		$map['code'] = $code;
		$map['isreply'] = 0;
		$count=$MessageVerifyDao->where($map)->count("*");
		while($count>0){
			$code = String::rand_string( 4,1);
			$map=array();
			$map['code'] = $code;
			$map['isreply'] = 0;
			$count=$MessageVerifyDao->where($map)->count("*");
		}
		//第四步、根据后台用户ID获取用户手机号码（这里必须获取的是通过验证的手机号码）
		$UserDao=M('user');
		$map=array();
		$map['mobilestatus'] = 1;
		$map['id'] = $userid;
		$mobile=$UserDao->where($map)->field("mobile")->find();
		if(!$mobile){
			$this->error("此用户的手机未通过验证！不能发送短信");
		}
		$data['code'] = $code;
		$data["tablename"]=$md;
		$data["tableid"]=$id;
		$data["replyuser"]=$userid;
		$data["replytel"] = $mobile['mobile'];
		$data["createtime"]=time();
		$re = $MessageVerifyDao->add($data);
		if( $re ){
			//限制发送内容长度在240个字符内,如果超出了则用...替代
			$content .= " 回复".$code."01+批复内容 批复; 回复".$code."00+批复内容 打回";
			$remsg=$this->SendTelmMsg($content,$data["replytel"],$systemtype);
			$msgstatus = intval(trim($remsg));
			if($msgstatus!=1 && $msgstatus!=0 ){
				$this->error("短信发送失败".$msgstatus);
			}
		}else{
			$this->error("操作失败");
		}
		//$r="||15123132519#123456#1同意#2013-07-10 17:33:55#";
		//$this->success ( L('_SUCCESS_') );
	}
	/**
	 * @Title: workNoticeMessage
	 * @Description: todo(审核完毕发送系统消息告知知会人/执行人)
	 * @param array        $sendidArr 接收人ID
	 * @param string       $modelname 对应的model名称
	 * @param int          $tableid   单据编号id
	 * @param int          $noticeType  1表示知会，2表示执行，可扩展
	 * @author yangxi
	 * @date 2014-2-26 下午6:06:38
	 * @throws
	 */
	public function workNoticeMessage($sendidArr,$modelName,$tableid,$noticeType){
		$model 		= D('SystemConfigDetail');
		$nodeModel = M('node');
		//通过modelname查找到对应的模块中文名
		$modelNameChinese = $nodeModel->where("name='".$modelName."'")->getField("title");
		//通过modelname查找到对应的单据名称
		$orderInfoname = D($modelName)->where("id='".$tableid."'")->field("name")->find();
		$ordername=$orderInfoname['name'];
		//获取制单人
		$createid=D($modelName)->where("id='".$tableid."'")->getfield("createid");
		$createname=getFieldBy($createid, 'id', 'name', "user");
		//通过modelname查找到对应的单据编号
		$orderInfo = D($modelName)->where("id='".$tableid."'")->field("orderno")->find();
		if($orderInfo){
			$orderno=$orderInfo['orderno'];
		}else{
			$orderInfo= D($modelName)->where("id='".$tableid."'")->field("code")->find();
			$orderno=$orderInfo['code'];
		}
		//知会还是执行
		if($noticeType=="1"){
			$noticeTypeName="工作知会：";
			//$noticeUrl='<a class="edit" style="text-decoration:underline" title="工作中心" target="navTab" rel="MisWorkExecuting" href="__APP__/MisWorkExecuting/index/jump/6/md/'.$modelName.'/tableid/'.$tableid.'">' . $orderno . '</a>';
			$noticeUrl='<a class="edit" style="text-decoration:underline" title="' . $modelNameChinese . '_查看" " target="navTab" rel="'.$modelName.'" href="__APP__/' . $modelName . '/auditView/id/' . $tableid. '">' . $orderno . '</a>';
		}else{
			$worksetmodel=M("mis_work_executing_set");
			$typeid=$worksetmodel->where("name='".$modelName."'")->getField("typeid");
			$Nameid=$worksetmodel->where("name='".$modelName."'")->getField("id");
			$noticeTypeName="工作中心：";
			$noticeUrl='<a class="edit" style="text-decoration:underline" title="工作中心" target="navTab" rel="MisWorkExecuting" href="__APP__/MisWorkExecuting/index/jump/4/md/'.$modelName.'/typeid/'.$typeid.'/dotype/0/rel/'.$modelName.'_'.$Nameid.'/tableid/'.$tableid.'">' . $orderno . '</a>';
		}
		//$modelNameChineseUrl='<a class="" style="text-decoration:underline" title="'.$modelNameChinese.'" target="navTab" rel="'.$modelName.'" href="__APP__/'.$modelName.'/index">'.$modelNameChinese.'</a>';
		$modelNameChineseUrl='<a class="edit" style="text-decoration:underline" title="' . $modelNameChinese . '_查看" " target="navTab" rel="'.$modelName.'" href="__APP__/' . $modelName . '/auditView/id/' . $tableid. '">' . $modelNameChinese . '</a>';
		//发送的系统日志的标题
		$messageTitle =$noticeTypeName.$modelNameChinese.' 单据号为  '.$orderno.' 的单据已经审批完成，请关注！';
		//message信息拼装
		$messageContent="";
		$messageContent.='
				<p></p>
				<span></span>
				<div style="width:98%;">
				<p class="font_darkblue">您好！</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;'. $modelNameChinese.' 单据号为  '.$orderno.' 的单据已经审批完成 </p>
						<p>&nbsp;&nbsp;&nbsp;&nbsp;单据的详细情况：</p>
						<ul>
						<li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>单据系统：</strong>' . $modelNameChineseUrl . '
								</li>
								<li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>单据编号：</strong>'.$noticeUrl.'
										</ul>
										<p>&nbsp;&nbsp;&nbsp;&nbsp;如果您有任何问题，请联系制单人：' . $createname . '。</p>
												</div>';
		//从动态配置获取到数据信息
		$result=$model->GetDynamicconfData($modelName,$tableid);
		//print_R($result);
		foreach($result as $key => $val){
			//初始化一个字符串容器
			$tmpstring="";
			foreach($val as $subkey => $subval){
				$tmpstring=$subkey.'：</b>'.$subval;
			}
			$messageContent.='<b class="t-gre1">'.$tmpstring.'<br/>';
		}
		//开始附件头部拼接
		$messageContent.='<div class="xyMessageAttach"><div style="padding:6px 10px 6px 8px;"><div class="attach left"></div><strong>附件：</strong></div><div class="xyMessageAttachItems">';
		//声明相关附件表
		$modelMAR = M('MisAttachedRecord');
		//获取附件信息
		$num=1;
		$map = array();
		$map["status"]  =1;
		$map["tableid"] =$tableid;
		$map["tablename"] =$modelName;
		$attarry=$modelMAR->field("id,upname,attached")->where($map)->select();
		if($attarry){
			foreach($attarry as $attkey => $attval){
				//下载路径
				$downloadName=__URL__."/misFileManageDownload/path/".base64_encode($attval['attached'])."/rename/".$attval['upname'];
				//归档路径
				$stockName=__APP__."/MisMessageInbox/lookupDocumentCollateAtta/t/0/id/".$attval['id'];
				//附件名称拼接
				$messageContent.='<div class="xyMessageAttachItem"><span class="tml-label tml-bg-orange tml-mr5">附件'.$num.'</span>';
				$messageContent.='</span><a class="attlink" rel="'.$attval['id'].'" target="_blank" href="'.$downloadName.'"><span>'.$attval['upname'].'</span>';
				$messageContent.='<a class="tml-btn tml-btn-small tml-btn-green" href="'.$stockName.'" title="文件归档" target="dialog"><span class="tml-icon tml-icon-file"></span><span class="tml-icon-text">归档</span></a></div>';
				$num++;
			}
		}
		//开始附件尾部拼接
		$messageContent.='</div></div>';
		// 		echo   $messageContent;
		// 		return $messageContent;
		//系统推送消息
		if(!is_array($sendidArr)){
			$sendidArr=array($sendidArr);
		}
		$messageexecuting=array();
		if($noticeType){
			$messageexecuting=array('tableid'=>$tableid,'tablename'=>$modelName,"noticeType"=>$noticeType);
		}
		$this->pushMessage($sendidArr, $messageTitle, $messageContent,'','','',$messageexecuting);
	}
	/**
	 * @Title: setProcessModelMesg
	 * @Description: todo(手机短信批复发送的内容)
	 * @param string $tabname 模型名称(一定要是模型名称要完全和配置文件的model一致)
	 * @param int $tabid 对应模型数据的ID值
	 * @return string
	 * @author liminggang
	 * @date 2013-7-12 下午2:49:15
	 * @throws
	 */
	public function setProcessModelMesg($tabname,$tabid){
		//第一步、获取到传入过来的数据
		$model=M($tabname);
		$volist=$model->where('status = 1 and id = '.$tabid)->select();
		//转换list2中一些特定的数据
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($tabname,false);
		$data=array();
		foreach($volist as $k=>$v ){
			$data[$k]=array();
			foreach($detailList as $k2=>$v2){
				$data[$k][$v2['name']]="";
				if(count($v2['func']) >0){
					foreach($v2['func'] as $k3=>$v3){
						if(isset($v2['extention_html_start'][$k3])){
							$data[$k][$v2['name']]=$v2['extention_html_start'][$k3];
						}

						$data[$k][$v2['name']] .= getConfigFunction($v[$v2['name']],$v3,$v2['funcdata'][$k3],$volist[$k],false,$v2["domainid"]);

						if(isset($v2['extention_html_end'][$k3])){
							$data[$k][$v2['name']].=$v2['extention_html_end'][$k3];
						}
					}
				}else{
					$data[$k][$v2['name']]= $v[$v2['name']];
				}
			}
		}
		//第二步、获取配置文件信息
		$file = DConfig_PATH . "/System/ProcessModelsConfig.inc.php";
		$list = require $file;
		//第三步、组合短信息内容
		$content="";
		foreach($list as $key=>$val){
			if($val['model'] == $tabname){
				$arr=array();
				$c="";
				foreach($val['key'] as $k=>$v){
					$c = $v."".$data[0][$k]." ".$val['unit'][$k]."，";
					$content = $content."".$c;
				}
			}
		}
		return $content;
	}
	/**
	 * @Title: lookEmail
	 * @Description: todo(获取接收短信人)
	 * @param unknown_type $id
	 * @author liminggang
	 * @date 2013-7-13 上午11:32:43
	 * @throws
	 */
	public function lookupMessage(){
		$id =$_REQUEST['id'];
		$this->assign('id',$id);
		//设置一个标志，判断是否是发送短信还是查询接收短信人员
		$step=$_POST['step'];
		if(!$step){
			//设置人员
			$name=$this->getActionName();
			$model=D($name);
			$map['id'] = $id;
			$map['auditState'] = array('exp','>= 1 and auditState <=2 '); //审核状态
			$list = $model->where($map)->field('curNodeUser')->find();

			//获取需要发送的短信内容，预览
			$messageContent=$this->setProcessModelMesg($name,$id);
			$this->assign('messageContent',$messageContent);

			//这里的审核人可能是多个。根据用户ID查询用户信息
			$UserDao=M("user");
			$map=array();
			$map['id'] = array(' in ',$list['curNodeUser']);
			$userlist=$UserDao->where($map)->field('id,name,dept_id,duty_id,mobile')->select();
			$this->assign('list',$userlist);
			$this->display("Public:lookupMessage");
		}else{
			//这里直接调用短信发送方法
			$this->sendAuditMsg($_POST['id'],$name,$_POST['userid']);
			$this->success("发送成功");
		}
	}

	/**
	 +----------------------------------------------------------
	 * 外部邮件发送
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param string $subject 邮件主题
	 * @param string $body 邮件内容
	 * @param array  $addresses 发送地址,英文逗号分隔
	 * @param array  $attachments 附件列表
	 * @param array  $configemail 邮件发送服务配置
	 * @param int	 $type		  是否为系统 邮件，0表示为系统邮件，1表示为个人邮件
	 +----------------------------------------------------------
	 * @return  string
	 +----------------------------------------------------------
	 * @throws ThinkExecption
	 +----------------------------------------------------------
	 */
	public function SendEmail($subject, $body, $addresses = array(),$attachments = array(),$configemail=array(),$type=0,$loadoption=true){
		import("@.ORG.Mailer.PHPMailer");
		$mail = new PHPMailer();
		$mail->CharSet = "UTF-8";
		$mail->IsSMTP(); // telling the class to use SMTP
		//$mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
		// 1 = errors and messages
		// 2 = messages only
		$mail->SMTPAuth   = true;                  // enable SMTP authentication
		if ($configemail['loginaccform'] ==1){
			$SMTPaccountusername = $configemail['address'];
		}else {
			$SMTPaccountusername = $configemail['email'];
		}
		if($type){
			$mail->Host       = $configemail['smtp'];         // sets the SMTP server
			$mail->Port       = $configemail['smtpport'];        // set the SMTP port for the GMAIL server
			$mail->Username   = $SMTPaccountusername;    // SMTP account username
			$mail->Password   = $configemail['password'];     // SMTP account password
			$mail->SetFrom($configemail['email'],$configemail['name']);          //必填，发件人邮箱
			$mail->AddReplyTo($configemail['email'], $configemail['name']);//回复EMAIL(留空则为发件人EMAIL回复名称留空则为发件人名称)
		}else {
			$mail->Host       = C('SMTP_HOST');         // sets the SMTP server
			$mail->Port       = C('SMTP_PORT');        // set the SMTP port for the GMAIL server
			$mail->Username   = C('EMAIL_USERNAME');    // SMTP account username
			$mail->Password   = C('EMAIL_PASSWORD');     // SMTP account password
			$mail->SetFrom(C('EMAIL_SERVERADDRESS'),C('EMAIL_SERVERNAME'));          //必填，发件人邮箱
		}
		$mail->Subject    = $subject;
		$body = eregi_replace("[\]",'',$body);
		$mail->MsgHTML($body);
		//地址分割
		foreach($addresses as $key=>$value){
			if ($value !== ''){
				$mail->AddAddress($value,$configemail['name']);
			}else {//发内部邮件
				$_POST['messageType'] = 0;//messageType改为站内信
				if($loadoption==true){
					if (method_exists($this,"_before_insert")) {
						call_user_func(array(&$this,"_before_insert"));
					}
					$this->insert();
					if (method_exists($this,"_after_insert")) {
						call_user_func(array(&$this,"_after_insert"),$list);
					}
				}
			}
		}
		foreach($attachments as $attachment){
			$mail->AddAttachment($attachment);
		}
		if(!$mail->Send()) {
			return false;//"发送失败，请联系管理员";
		} else {
			return true;
		}
	}
	/**
	 * @Title: test
	 * @Description: todo(解码，主要是为邮件接收服务的。)
	 * @param unknown_type $strHead
	 * @return unknown
	 * @author xiafengqin
	 * @date 2013-9-4 下午3:50:23
	 * @throws
	 */
	public function test($strHead){
		if(ereg("=\?.{0,}\?[Bb]\?",$strHead)){
			$arrHead=split("=\?.{0,}\?[Bb]\?",$strHead);
			while(list($key,$value)=each($arrHead)){
				if(ereg("\?=",$value)){
					$arrTemp=split("\?=",$value);
					$arrTemp[0]=base64_decode($arrTemp[0]);
					$arrHead[$key]=join("",$arrTemp);
				}
			}
			$strHead=join("",$arrHead);
		}
		return $strHead;
	}
	/**
	 +----------------------------------------------------------
	 * 邮件接收
	 * author:yangxi
	 * data:20130812
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 receiveMail‘s mothod
	 $username      = User name off the mail box
	 $password      = Password of mailbox
	 $emailAddress  = Email address of that mailbox some time the uname and email address are identical
	 $mailserver    = Ip or name of the POP or IMAP mail server
	 $servertype    = if this server is imap or pop default is pop
	 $port          = Server port for pop or imap Default is 110 for pop and 143 for imap
	 +----------------------------------------------------------
	 * @return  string
	 +----------------------------------------------------------
	 * @throws ThinkExecption
	 +----------------------------------------------------------
	 */
	public function ReceiveEmail($username, $password,$emailAddress = '',$mailserver,$servertype,$port){
		import("@.ORG.Mailer.receivemail");
		$obj= new receiveMail($username,$password,$emailAddress,$mailserver,$servertype,$port);
		$obj->connect();//建立连接
		$tot=$obj->getTotalMails(); //获取条数
		$emailArr = array();
		for($i=$tot;$i>0;$i--){
			$new = array();
			$head=$obj->getHeaders($i);  // Get Header Info Return Array Of Headers **Array Keys are (subject,to,toOth,toNameOth,from,fromName)
			$new['title'] = $this->test($head['subject']);
			$new['recipient'] = $head['to']; //收件人
			$new['copytopeopleid'] = $head['toOth']; //抄送人
			$new['createid'] = $head['from']; //发件人
			$new['emaildate'] = $head['date']; //邮件接收时间
			$new['message_id'] = $head['message_id']; //邮件唯一标识
			//	$new['content'] = $this->test($obj->getBody($i));

			$str=$obj->GetAttach($i,"./"); // Get attached File from Mail Return name of file in comma separated string  args. (mailid, Path to store file)
			$new['attr'] = explode(",",$str);
			$emailArr[] = $new;
			//$obj->deleteMails($i); // Delete Mail from Mail box
		}
		$obj->close_mailbox();   //Close Mail Box
		return $emailArr;
	}
	/**
	 * @Title: pushMessage 推送系统内部邮件
	 * @Description: todo(系统功能模块推送信息  推送内部邮件)
	 * @param array $recipientListID 收件人ID组合  数组结构{1,2,3,4}
	 * @param string $messageTitle  邮件标题
	 * @param string $messageContent 内部邮件内容
	 * @param string $messageLevel 不清楚何作用     备用 信息级别
	 * @param int $messagetype 0 无类型 1任务类型 (2013-07-22 jiangx添加)
	 * @param int $messagerelevanceid 外键关联ID
	 * @param array $messageexecuting 工作中心知会 主要反写会邮件ID到工作中心
	 * @return boolean  返回是否发送成功信息
	 * @author eagle
	 * @date 2013-01-14 下午4:21:42
	 * @throws
	 */
	public function pushMessage($recipientListID=array(),$messageTitle='system message',$messageContent='system push message function',$messageLevel=true, $messagetype = 0, $messagerelevanceid = 0 ,$messageexecuting=array()){
		//接收人数组不能为空,这里可能不太全面，多多注意
		if(!count($recipientListID)){
			return false;
			exit;
		}
		//生成发件人名字符串
		$modelUser = D('User');
		$condition = array('id'=>array('in', $recipientListID) );
		$nameArray = $modelUser->where($condition)->getField('id,name');

		//名字，字符串
		$recipientNameString = implode(",",$nameArray);

		//接收人，ID字符串
		$recipientListIDString = implode(',',$recipientListID);

		$modelMM= D('MisMessage');

		//数据区
		$data['title'] = $messageTitle;
		$data['recipient'] = $recipientListIDString;
		$data['recipientname'] = $recipientNameString;
		$data['content'] = $messageContent;
		$data['commit'] = '1';
		$data['createid'] = 0;
		$data['createtime'] =  time();
		$data['status'] = 1;
		$data['type'] = $messagetype;
		//保存当前数据对象
		$list=$modelMM->add ($data);
		//反写会邮件ID到工作中心
		if(count($messageexecuting)){
			$executingModel=D("MisWorkExecuting");
			if($messageexecuting['noticeType']==1){//工作知会
				$exre=$executingModel->where("tablename='".$messageexecuting['tablename']."' and tableid=".$messageexecuting['tableid'])->setField("messageid",$list);
			}else{//工作执行
				$exre=$executingModel->where("tablename='".$messageexecuting['tablename']."' and tableid=".$messageexecuting['tableid'])->setField("messexecutid",$list);
			}
			if($exre===false){
				$this->error("操作失败");
			}
		}
		if ($list!==false) {
			//存入收件箱，数据表存储的是，信件内容与用户间的逻辑关系
			$modelMMU = D('MisMessageUser');

			//把当前发件人的ID也存入数组 ,注意：前面的 ”-“ 号是有作用的,标记是放在发件箱里的数据。
			$recipientListID[]= -$_SESSION [C ( 'USER_AUTH_KEY' )];
			foreach ($recipientListID as $k => $v) {
				$data['recipient'] = $v; //接收人ID
				$data['sendid'] = $list; //邮件信主本ID
				$data['messageType'] = 1; //系统信息
				$data['contentType'] = $messagetype;
				$data['relevanceid'] = $messagerelevanceid;
				$list_next=$modelMMU->add ($data);
				//不成功反回错误
				if ($list_next===false) {
					return false;
					exit;
				}
			}
			$userModel = D("User");
			$map = array();
			$map['id'] = array('in', $recipientListID);
			$isnewmsg = $userModel->where($map)->setField("newmsg", 1);
			if ($isnewmsg!==false){
				return true;
				exit;
			}
		} else {
			$this->error('发送邮件失败！');
			return false;
		}
	}
	/*
	 * @yangxi
	 * 2012/09/18
	 * remark 数据表信息获取
	 * @parameter string $tablename 表名称
	 * return array $tableInfo      表信息
	 */
	public function TableInfo($tablename){
		$database=C('DB_NAME');
		// 实例化一个model对象 没有对应任何数据表
		$Model= new Model();
		$sql="select count(*)as num from ".$tablename;
		$recordNum=$Model->query($sql);
		// 切换数据库操作
		$Model = D("InformationSchema");
		//sql获取表大小，总行数
		$sql="select concat(round(sum(DATA_LENGTH/1024/1024),4),'MB') as data,TABLE_ROWS as rows from TABLES where table_schema='".$database."' and table_name='".$tablename."'";
		$result=$Model->query($sql);
		//print_r($result);
		if($result){
			//声明表信息二维数组，数据库，数据表，数据表大小，数据表行数
			$tableInfo=array(C('DB_NAME'),$tablename,$result[0]['data'],$recordNum[0]['num']);
			return $tableInfo;
		}
		else{
			return false;
		}
	}


	/**
	 *
	 * @Title: findAlldept
	 * @Description: todo(查找当前节点下的子节点)
	 * @param array $deptlist 指定模型的所有数据
	 * @param int $deptid   选中的节点
	 * @param unknown_type $hasparentsid  是否组合选中的节点
	 * @return string   返回选中节点在内的所有子节点
	 * @author liminggang
	 * @throws
	 */
	function findAlldept($deptlist,$deptid,$hasparentsid=true){
		if($deptid){
			$arr="";
			if( $hasparentsid ) $arr =",".$deptid;
			foreach ($deptlist as $k=>$v){
				if($v['parentid']==$deptid ){
					unset($deptlist[$k]);
					$arr.=",".$v['id'].$this->findAlldept($deptlist,$v['id']);
				}
			}
			return $arr;
		}
	}

	/**
	 *
	 * @Title: downAllChildren
	 * @Description: todo(查找当前节点下的子节点)
	 * @param array $list 指定模型的所有数据
	 * @param int $deptid   选中的节点
	 * @param unknown_type $hasparentsid  是否组合选中的节点
	 * @return string   返回选中节点在内的所有子节点
	 * @author liminggang
	 * @throws
	 */
	function downAllChildren($list,$id,$hasparentsid=true){
		$arr="";
		if($id){
			if( $hasparentsid ) $arr =",".$id;
			foreach ($list as $k=>$v){
				if($v['parentid']==$id){
					unset($list[$k]);
					$arr.=",".$v['id'].$this->downAllChildren($list,$v['id'],false);
				}
			}
		}
		return $arr;
	}

	/**
	 *
	 * @Title: upAllParentid
	 * @Description: todo(查找当前节点下的子节点)
	 * @param array $list 指定模型的所有数据
	 * @param int $id   选中的节点
	 * @param bool $includeself  是否组合选中的节点
	 * @return string   返回选中节点在内的所有子节点
	 * @author qchlian
	 * @throws
	 */
	public function upAllParentid($list,$id,$pid,$includeself=true){
		$arr="";
		if($id){
			if( $includeself ) $arr =",".$id;
			foreach ($list as $k=>$v){
				if($v['id']==$pid){
					unset($list[$k]);
					if($v['parentid']){
						$arr.=",".$v['id'].$this->upAllParentid($list,$v['id'],$v['parentid'],false);
					}else{
						$arr.=",".$v['id'];
					}
				}
			}
		}
		return $arr;
	}

	/**
	 *
	 * @Title: getParentDepartmentIdBylevel
	 * @Description: todo(查找当前节点向上levels级的id)
	 * @param array $deptlist 指定模型的所有数据
	 * @param int $deptmentid   选择的节点
	 * @param $levels           向上级数
	 * @return int   返回向上levels级的id
	 * @author qchlian
	 * @throws
	 */
	function getParentDepartmentIdBylevel($deptlist,$deptmentid,$levels=0,$all=false){
		if($levels){
			$arr="";
			foreach ($deptlist as $k=>$v){
				if($v['id']==$deptmentid){
					unset($deptlist[$k]);
					$levels--;
					$p=$this->getParentDepartmentIdBylevel($deptlist,$v['parentid'],$levels,$all);
					if($p){
						if($all){
							$deptmentid.=",".$p;
						}else{
							$deptmentid=$p;
						}
					}
					break;
				}
			}
			return $deptmentid;
		}else{
			return $deptmentid;
		}
	}

	/**
	 * 构造树形节点  @changeby wangcheng
	 * @param array  $alldata  构造树的数据
	 * @param array  $param    传入数组参数，包含rel，url【url中参数传递用#name#形式，name 为字段名字】
	 * @param array  $returnarr  初始化s树形节点，可以传入数字，默认选中哪一行数据
	 * @param array $companylist 公司信息 (有需求的才传入这个参数)
	 * @return string
	 */
	function getTree($alldata=array(),$param=array(),$returnarr=array()){
		foreach($alldata as $k=>$v){
			$newv=array();
			$matches=array();
			preg_match_all('|#+(.*)#|U', $param['url'], $matches);
			foreach($matches[1] as $k2=>$v2){
				if(isset($v[$v2])) $matches[1][$k2]=$v[$v2];
			}
			$url = str_replace($matches[0],$matches[1],$param['url']);
			$newv['id']=$v['id'];
			$newv['pId']=$v['parentid']?$v['parentid']:0;
			$newv['type']='post';
			$newv['url']=$url;
			$newv['target']='ajax';
			$newv['rel']=$param['rel'];
			$newv['title']=$v['name']; //光标提示信息
			$newv['name']=missubstr($v['name'],18,true); //结点名字，太多会被截取,针对于现在的ZTREE，宽度不能超过18个字符。
			if($v['tablename']){
				$newv['tablename']=$v['tablename'];
			}
			if($v['parentid']==0){
				$newv['open']=$v['open'] ? $v['open']:'true';
			}
			if($param['open']){
				$newv['open']='true';
			}
			$newv['isParent'] = $param['isParent']=="true" ? true:false;
			array_push($returnarr,$newv);
		}
		return json_encode($returnarr);
	}

	/**
	 * 构造select树形节点  @changeby wangcheng
	 * @param array  $alldata  构造树的数据
	 * @param int  $pid    传入顶级参数
	 * @param int  $i  级别默认为0
	 * @param int	 $selected 默认被选中id
	 * @return string
	 */
	function selectTree($alldata,$pid=0,$i=0,$selected="",$pcode="parentid"){
		$data=array();
		$html = '';
		$kong="";
		for($s=0;$s<$i;$s++){
			$kong.="&nbsp;&nbsp;&nbsp;&nbsp;";
		}
		foreach($alldata as $k2=>$v2){
			if( $v2[$pcode]==$pid ){
				unset($alldata[$k2]);
				$back=$selectedhtml="";
				if($selected && $selected==$v2['id'] ) $selectedhtml="selected='selected'";
				$back= $this->selectTree($alldata,$v2['id'],$i+1,$selected);

				/* if($back){
					$html .= "<option ".$selectedhtml." value=\"".$v2['id']."\">".$kong.$v2['name']."</option>";
					$html .= $back;
					}else{
					$html .= "<option  ".$selectedhtml." value=\"".$v2['id']."\">".$kong.$v2['name']."</option>";
					} */
				/* 2013-07-30 renling 改*/
				if($back){
					$html .= "<option ".$selectedhtml." vname=" . $kong.$v2['name'] . " value=\"".$v2['id']."\">".$kong.$v2['name']."</option>";
					$html .= $back;
				}else{
					$html .= "<option  ".$selectedhtml." vname=" . $kong.$v2['name'] . "  value=\"".$v2['id']."\">".$kong.$v2['name']."</option>";
				}
			}
		}
		return $html;
	}
	/**
	 * 创建目录 2012-12-17
	 * @author wangcheng
	 * @param string $path 创建目录路径
	 */
	function createFolders($path) {
		if (!file_exists($path)) {
			$this->createFolders(dirname($path));
			mkdir($path, 0777);
		}
	}
	/**
	 * 检查FTP上传配置
	 * @Title: checkFtp
	 * @Description: todo(这里用一句话描述这个方法的作用) 
	 * @throws NullPointExcetion
	 * @return Ftp  
	 * @author quqiang 
	 * @date 2016年1月26日 下午1:33:03 
	 * @throws
	 */
	function checkFtp(){
		import('@.ORG.Ftp');
		if(!class_exists('Ftp')){
			throw new NullPointExcetion("Ftp扩展类不存在，请检查");
		}
		$ftpIP = C('ftpIP');		// FTP地址
		$ftpPort = C('ftpPort')?C('ftpPort'):21;	// FTP端口
		$ftpId = C('ftpId');		// FTP帐号
		$ftpPwd = C('ftpPwd');		// FTP密码
		$ftpUrl= C('ftpUrl');		// 资源调用地址
		if(!$ftpIP)
			throw new NullPointExcetion('请配置FTP请求地址!');
		if(!$ftpId)
			throw new NullPointExcetion('请配置FTP帐号!');
		if(!$ftpPwd)
			throw new NullPointExcetion('请配置FTP密码!');
		if(!$ftpUrl)
			throw new NullPointExcetion('请配置FTP资源调用地址!');
		$ftp = new Ftp($ftpIP,$ftpPort,$ftpId,$ftpPwd);
		return $ftp;
	}
	/**
	 * @Title: swf_upload
	 * @Description: todo附件上传方法
	 * @param int 当前表单ID $insertid
	 * @param int $type  已经弃用此参数，为了减少修改。暂时没有删除。
	 * @param int 带明细的ID $subid 针对有明细数据时，也存在附件
	 * @param string 附件对应的控制器名称 $m
	 * @author liminggang
	 * @date 2014-10-10 下午7:40:06
	 * @throws
	 */
	function swf_upload($insertid,$subid=0,$m="",$projectid,$projectworkid){
		try{
			$save_file=$_POST['swf_upload_save_name'];
			$source_file=$_POST['swf_upload_source_name'];
			$attModel = D("MisAttachedRecord");
			//如果存在项目跟任务，判断是否需要归档77 
			if($projectid && $projectworkid && $save_file){
				$MPFFDAO = M("mis_project_flow_form"); // 实例化User对象
				$isfile = $MPFFDAO->where("id=".$projectworkid)->getField('isfile');
			}
			if(C('ftpUse')==1){
				$ftp = $this->checkFtp();
			}
			//临时文件夹里面的文件转移到目标文件夹
			foreach($save_file as $k=>$v){
				if(is_array($v)){
					foreach ($v as $key=>$val){
						$fileinfo=pathinfo($val);
						$from = UPLOAD_PATH_TEMP.$val;//临时存放文件
						
						if( file_exists($from) ){
							$p=UPLOAD_PATH.$fileinfo['dirname'];// 目标文件夹
							if( !file_exists($p) ) $this->createFolders($p); //判断目标文件夹是否存在
							$to = UPLOAD_PATH.$val;
						//获取文件大小及基本信息
							$filemsg=pathinfo($from);
							$filesize=filesize($from);
							//保存附件信息
							$data=array();
							if(C('ftpUse')==1){
								// Ftp上传资源 by nbmxkj@20160126
								$ftp->up_file($from,$val);   //上传文件
								$remotePic .= $val;
								$data['domain']= strrpos(C('ftpUrl'),'/')+1 == strlen(C('ftpUrl'))?C('ftpUrl'):C('ftpUrl').'/';
								$data['isremote']=1; // 外部资源调用方式
							}else{
								rename($from,$to);
							}
							// 处理资源地址
							//$remotePic= C('ftpUse')==1 && C('ftpUrl') ? ( strrpos(C('ftpUrl'),'/')+1 == strlen(C('ftpUrl'))?C('ftpUrl').$val:C('ftpUrl').'/'.$val ): $val;
							$remotePic = $val;
							//$data['type']=$type;
							//$data['orderid']=$insertid;
							
							if(C('AREA_TYPE')==1){
								$modelName = !empty($m) ? $m : $this->getActionName();
							}elseif(C('AREA_TYPE')==2){
								$modelName = !empty($m) ? $m : (D($this->getActionName())->getTableName());
							}else{
								$modelName = !empty($m) ? $m : $this->getActionName();
							}
							
							$data['tablename'] =$modelName;
							$data['tableid']=$insertid;
							$data['subid']=$subid;
							$data['attached']= $remotePic;
							$data['projectid']= $projectid;
							$data['projectworkid']= $projectworkid;
							$data['isfile']= $isfile;
							$data['fieldname']= $k;
							$data['upname']=$source_file[$k][$key];
						
							$data['filesize']=$this->formatBytes($filesize);
							$data['filesuffix']=$filemsg['extension'];
							$data['filename']=str_replace('.'.$data['filesuffix'], '', $data['upname']);
							
							$data['createtime'] = time();
							$data['createid'] = $_SESSION[C('USER_AUTH_KEY')]?$_SESSION[C('USER_AUTH_KEY')]:0;
							$rel=$attModel->add($data);
							if(!$rel){
								$this->error("附件上传失败，请联系管理员！");
							}
						}
					}
				}else{
					$fileinfo=pathinfo($v);
					$from = UPLOAD_PATH_TEMP.$v;//临时存放文件
					if( file_exists($from) ){
						$p=UPLOAD_PATH.$fileinfo['dirname'];// 目标文件夹
						if( !file_exists($p) ) $this->createFolders($p); //判断目标文件夹是否存在
						$to = UPLOAD_PATH.$v;
						//获取文件大小及基本信息
						$filemsg=pathinfo($from);
						$filesize=filesize($from);
						//保存附件信息
						$data=array();
						if(C('ftpUse')==1){
							// Ftp上传资源 by nbmxkj@20160126
							$ftp->up_file($from,$v);   //上传文件
							$data['isremote']=1; // 外部资源调用方式
							$data['domain']= strrpos(C('ftpUrl'),'/')+1 == strlen(C('ftpUrl'))?C('ftpUrl'):C('ftpUrl').'/';
						}else{
							rename($from,$to);
						}
// 						$remotePic= C('ftpUse')==1 && C('ftpUrl') ? ( strrpos(C('ftpUrl'),'/')+1 == strlen(C('ftpUrl'))?C('ftpUrl').$v:C('ftpUrl').'/'.$v ): $v;
						$remotePic = $v;
						//$data['type']=$type;
						//$data['orderid']=$insertid;
						if(C('AREA_TYPE')==1){
							$modelName = !empty($m) ? $m : $this->getActionName();
						}elseif(C('AREA_TYPE')==2){
							$modelName = !empty($m) ? $m : (D($this->getActionName())->getTableName());
						}else{
							$modelName = !empty($m) ? $m : $this->getActionName();
						}
							
						$data['tablename'] =$modelName;
						$data['tableid']=$insertid;
						$data['subid']=$subid;
						$data['attached']= $v;
						$data['fieldname']= $_REQUEST['fieldname'];
						$data['upname']=$source_file[$k];
						
						$data['filesize']=$this->formatBytes($filesize);
						$data['filesuffix']=$filemsg['extension'];
						$data['filename']=str_replace('.'.$data['filesuffix'], '', $data['upname']);
						
						$data['createtime'] = time();
						$data['createid'] = $_SESSION[C('USER_AUTH_KEY')]?$_SESSION[C('USER_AUTH_KEY')]:0;
						$data['projectid']= $projectid;
						$data['projectworkid']= $projectworkid;
						$data['isfile']= $isfile;
						$rel=$attModel->add($data);
						if(!$rel){
							$this->error("附件上传失败，请联系管理员！");
						}
					}
				}
			}
		}catch (Exception $e){
			$this->error($e->getMessage());
		}
	}

	function formatBytes($filesize) {
		if($filesize >= 1073741824) {
		  $filesize = round($filesize / 1073741824 * 100) / 100 . ' gb';
		 } elseif($filesize >= 1048576) {
		  $filesize = round($filesize / 1048576 * 100) / 100 . ' mb';
		 } elseif($filesize >= 1024) {
		  $filesize = round($filesize / 1024 * 100) / 100 . ' kb';
		 } else {
		  $filesize = $filesize . ' bytes';
		 }
		 return $filesize;
	}
	
	/**
	 * @Title: 地图定位信息_info
	 * @Description: todo(地区信息处理)
	 * @param int 		$inserid	数据ID
	 * @param string 	$modelName 	Model名 , 默认为空， 为空时代码中获取当中前的action名
	 * @author quqiang
	 * @date 2014-10-29 下午9:21:15
	 * @throws
	 */
	function map_info($inserid,$modelName=''){
		if($_POST['mapinfotag']){
			/*
			 * 获取页面地址POST数据。
			* */
			$dataArr=$_POST['mapinfo'];
			foreach ($dataArr as $key=>$val){
				// $key // 当前字段名
				$fieldName = $val['fieldname']; // 当前使用的字段名
				$detail = $val['detail']; // detail	//	完整信息
				$tablename=$val['tablename'];	//	tablename	//	存入表名
				$modelName=!empty($modelName) ? $modelName : $tablename;
				// 实例地区信息表
				$model = M('mis_address_map_record');
				if(C('AREA_TYPE')==1){
					$modelName = !empty($modelName) ? $modelName : $this->getActionName();
				}elseif(C('AREA_TYPE')==2){
					$modelName = !empty($modelName) ? $modelName : (D($this->getActionName())->getTableName());
				}else{
					$modelName = !empty($modelName) ? $modelName : $this->getActionName();
				}
				$data = '';
				$data['tablename'] = $modelName;
				$data['tableid'] = $inserid;
				$data['detail'] = $detail;
				$data['fieldname'] = $fieldName;
				//横坐标
				$data['coordinatex'] = $val['coordinatex'];
				//纵坐标
				$data['coordinatey'] =  $val['coordinatey'];
				// 检查当前数据是否存在
				$map['tablename'] = $data['tablename'];
				$map['tableid'] = $data['tableid'];
				$map['fieldname'] = $data['fieldname'];
				$resoult = $model->where($map)->find();
				if($resoult['id']){
					$data['id']=$resoult['id'];
				}
				if(C('TOKEN_NAME')) $data[C('TOKEN_NAME')]= $_POST[C('TOKEN_NAME')];
		
				$fmtdata = $model->create($data);
				if(false === $fmtdata){
					$this->error($model->getError());
				}
				// var_dump($data);
				if($resoult['id']){
					// 修改
					$ret = $model->where($map)->save($fmtdata);
					if(!$ret){
						//$this->error("地区信息更新失败");
						$msg = '地图信息更新失败'.'ERROR: '.$model->getDBError().'  SQL:'.$model->getLastSql();
						throw new NullDataExcetion($msg);
					}
				}else{
					$ret = $model->data($fmtdata)->add();
					if(!$ret){
						//$this->error("地区信息写入失败");
						$msg = '地图信息写入失败'.'ERROR: '.$model->getDBError().'  SQL:'.$model->getLastSql();
						throw new NullDataExcetion($msg);
					}
				}
			}
				
		}
		
	}
	/**
	 * @Title: area_info
	 * @Description: todo(地区信息处理)
	 * @param int 		$inserid	数据ID
	 * @param string 	$modelName 	Model名 , 默认为空， 为空时代码中获取当中前的action名
	 * @author quqiang
	 * @date 2014-10-29 下午9:21:15
	 * @throws
	 */
	function area_info($inserid , $modelName=''){
		if($_POST['areainfotag']){
			
	 		/*
	 		 * 获取页面地址POST数据。
	 		 * */
			$dataArr=$_POST['areainfo'];
			foreach ($dataArr as $key=>$val){
				// $key // 当前字段名
				$fieldName = $val['fieldname']; // 当前使用的字段名
				$address = $val['address']; // address	//	详细信息
				$detail = $val['detail']; // detail	//	完整信息
				$SelDataTemp = $val['data']; // data	//	级联下拉数据
				$SelData = array();
				if(is_array($SelData)){
					foreach($SelDataTemp as $k=>$v){
						$SelData['ext'.$k] = $v;
					}
				}
				$tablename=$val['tablename'];	//	tablename	//	存入表名
				$modelName=!empty($modelName) ? $modelName : $tablename;
				// 实例地区信息表
				$model = D('MisAddressRecord');
				if(C('AREA_TYPE')==1){
					$modelName = !empty($modelName) ? $modelName : $this->getActionName();
				}elseif(C('AREA_TYPE')==2){
					$modelName = !empty($modelName) ? $modelName : (D($this->getActionName())->getTableName());
				}else{
					$modelName = !empty($modelName) ? $modelName : $this->getActionName();
				}
				$data = '';
				$data['tablename'] = $modelName;
				$data['tableid'] = $inserid;
				$data['address'] = $address;
				$data['detail'] = $detail;
				$data['fieldname'] = $fieldName;
				//横坐标
				$data['coordinatex'] = $val['coordinatex'];
				//纵坐标
				$data['coordinatey'] =  $val['coordinatey'];
				// 将重新处理完格式的级联数据写入主数组
				$data = array_merge($data , $SelData);
				// 检查当前数据是否存在
				$map['tablename'] = $data['tablename'];
				$map['tableid'] = $data['tableid'];
				$map['fieldname'] = $data['fieldname'];
				$resoult = $model->where($map)->find();
				if($resoult['id']){
					$data['id']=$resoult['id'];
				}
				if(C('TOKEN_NAME')) $data[C('TOKEN_NAME')]= $_POST[C('TOKEN_NAME')];
				
				$fmtdata = $model->create($data);
				if(false === $fmtdata){
					$this->error($model->getError());
				}
				// var_dump($data);
				if($resoult['id']){
					// 修改
					$ret = $model->where($map)->save($fmtdata);
					if(!$ret){
						//$this->error("地区信息更新失败");
						$msg = '地区信息更新失败'.'ERROR: '.$model->getDBError().'  SQL:'.$model->getLastSql();
						throw new NullDataExcetion($msg);
					}
				}else{
					$ret = $model->data($fmtdata)->add();
					if(!$ret){
						//$this->error("地区信息写入失败");
						$msg = '地区信息写入失败'.'ERROR: '.$model->getDBError().'  SQL:'.$model->getLastSql();
						throw new NullDataExcetion($msg);
					}
				}
			}
			
		}
	}

	/**
	 * @Title: lookupdelatt
	 * @Description: 删除附件方法
	 * @author liminggang
	 * @date 2014-10-10 下午7:41:44
	 * @throws
	 */
	public function lookupdelatt(){
		$model = D("MisAttachedRecord");
		$id = $_REQUEST ["id"];
		if (isset ( $id )) {
			$vo = $model->find($id);
			$p=UPLOAD_PATH."/".$vo['attached'];
			$p=str_replace("//","/", $p);
			if(file_exists($p)){
				unlink($p);
			}
			$map['id']=$id;
			$list=$model->where ( $map )->delete();
			if ($list!==false) {
				$this->success ( L('_SUCCESS_') ,"",$id);
			} else {
				$this->error ( L('_ERROR_') );
			}
		} else {
			$this->error ( C('_ERROR_ACTION_') );
		}
	}

	/**
	 * @Title: misFileManageDownload
	 * @Description: 附件下载方法
	 * @param string $path  base64加密的附件地址
	 * @param string $rename 附件名称
	 * @author liminggang
	 * @date 2014-10-10 下午7:42:00
	 * @throws
	 */
	function misFileManageDownload($path="",$rename=""){
		$path = $path ? $path : $_GET['path'];
		$rename = $rename ? $rename : $_GET['rename'];
		if(!C('FILE_RENAME')) unset($rename);//如果不打开文件重命名配置，则提供系统自动转换后的文件名称下载
		if($path){
			$path = base64_decode($path);
			$info = pathinfo($path);
			$filename = $rename ? $rename:$info["basename"];
			$filename = @iconv('UTF-8', 'GBK', urldecode($filename));
			$filename = str_replace(" ", "", $filename);
			//$path = preg_replace("/^([\s\S]+)\/Public/", "../Public", $info["dirname"].'/'. $info['basename']);
//			$path = "../Public/Uploads/".$info["dirname"].'/'. $info['basename'];
// 			$filenameUTF8 = preg_replace("/^([\s\S]+)\/Public\//", "", $rs['fileurl']);
// 			$filenameUTF8 = PUBLIC_PATH.$filenameUTF8;
			$a=strstr($info["dirname"],"http://");
		 	if($a==false){
		 		$path = UPLOAD_PATH."/".$info["dirname"].'/'. $info['basename']; //xyz 2015-9-6
		 	}else{
		 		$path = $info["dirname"].'/'. $info['basename']; //xyz 2015-9-6
		 	}
			$path = @iconv('UTF-8', 'GBK', $path);
			header("Cache-Control: public");
			header("Content-Type: application/force-download");
			header("Content-Disposition: attachment; filename*=utf8''".basename(str_replace("+", "%20", urlencode($path))));
			
			// 			header("Content-Type: application/force-download");
			// 			//header("Content-Type: application/octet-stream");
			// 			header("Content-Disposition: attachment; filename=".$filename);
			// 			header("Content-Transfer-Encoding:­ binary");
			// 			header("Content-Length: " . filesize($path));
			ob_clean();
			flush();
			readfile($path);
			
		}else{
			$this->error("下载地址出错...");
		}
	}

	/**
	 * @Title: getAttachedRecordList
	 * @Description: todo(获取附件信息查询方法)
	 * @param 单据ID $tableid 单据对应的ID
	 * @param 是否显示在线查看 $online 是否显示在线查看按钮，默认为true显示
	 * @param 是否显示归档 $archived 是否显示归档按钮，默认为true显示
	 * @param 单据的控制器名称 $tablename 单据对应的控制器名称
	 * @param 单据关联的详情ID $subid  单据关联的详情存在附件信息的时候查询详情附件信息需传入此ID
	 * @param 是否输出页面或者返回数组 $isassign   true 表示输出页面，false 表示返回福建信息数组  默认为 true
	 * @param 返回的数组是三维数组还是二维数组 $isfourorthree 默认为返回二维数组， false表示返回三维数组
	 * @return unknown
	 * @author liminggang
	 * @date 2014-7-30 下午2:06:32
	 * @throws
	 */
	public function getAttachedRecordList($tableid,$online=true,$archived=true,$tablename='',$subid=0,$isassign=true,$isfourorthree=true){
		$armodel = D('MisAttachedRecord');
		$armap['tableid'] = $tableid;
		if($subid) $armap['subid'] = $subid;		
		$armap['status'] = 1;
		//添加一个查询的对象模型
		if ($tablename == '') {
			if(C('AREA_TYPE')==1){
				$armap['tablename'] = $this->getActionName();
			}elseif(C('AREA_TYPE')==2){
				$armap['tablename'] = D($this->getActionName())->getTableName();
			}
		} else {
			$armap['tablename'] = $tablename;
		}
		
		$attarry = $armodel->where($armap)->select();
		$filesArr = array('pdf','doc','docx','xls','xlsx','ppt','pptx','txt','jpg','jpeg','gif','png','apk');
		foreach ($attarry as $key => $val) {
			$pathinfo = pathinfo($val['attached']);
			//获取除后缀的文件名称
			//王昭侠:2015年8月21日 $upname = missubstr($val['upname'],18,true).".".$pathinfo['extension'];
			$upname = $val['upname'];
			if (in_array(strtolower($pathinfo['extension']), $filesArr)) {
				//在线查看，必须是指定的文件类型，才能在线查看。
				$attarry[$key]['online'] = $online;  //在线查看按钮。
			}
			//URL传参。一定要将base64加密后生成的  ‘=’ 号替换掉
			$attarry[$key]['name'] = str_replace("=", '', base64_encode($val['attached']));
			//文件显示名称
			$attarry[$key]['filename'] = $upname;
			//文件下载名称
			$attarry[$key]['lookname'] = $val['upname'];
			//任何文件都可以归档
			$attarry[$key]['archived'] = $archived; //归档按钮
		}
		$uploadarry=array();
		foreach ($attarry as $akey=>$aval){
			$uploadarry[$aval['fieldname']][]=$aval;
		}
		if($isassign){
			$this->assign('attarry',$attarry);
			$this->assign('attcount',count($attarry));
			$this->assign('uploadarry',$uploadarry);
		} else {
			if($isfourorthree){
				return $attarry;
			}else{
				return $uploadarry;
			}
		}
	}


	/**
	 * @Title: userAlert
	 * @Description:系统右下角弹出框，用户提醒
	 * @author liminggang
	 * @date 2014-9-13 下午6:01:25
	 * @throws
	 */
	function userAlert(){
		$usermodel =M("user");
		$hasmsg = $usermodel->where("id=".$_SESSION [C ( 'USER_AUTH_KEY' )])->getField("newmsg");
		if( $hasmsg ){
			$msgmodel = M("mis_message_user");
			$msgmap['status'] = 1;
			$msgmap['commit'] = 1;
			$msgmap['readedStatus'] = 0;
			$msgmap['recipient'] = $_SESSION [C ( 'USER_AUTH_KEY' )];
			$msgcount = $msgmodel->where($msgmap)->count("*");
			if($msgcount>0){
				$countmsg=1;
			}
			$return=array("count"=>$hasmsg,"msg"=>$msgcount);
			// $usermodel->where("id=".$_SESSION [C ( 'USER_AUTH_KEY' )])->setField("newmsg",0);
			//$usermodel->commit();
		}else{
			$return=array("count"=>$hasmsg,"msg"=>$msgcount);
		}
		echo json_encode($return);
		exit;
	}
	/**
	 * @Title: lookupGenerals
	 * @Description: todo(普通常用查找带回)
	 * @author 杨东
	 * @date 2013-7-9 下午6:04:06
	 * @throws
	 */
public function lookupGenerals(){
			// add by nbmxkj at 20150129 2107 lookup 属性自动获取
		$lookupKey = $_REQUEST['lookupchoice'];
		$lookupObj = D ( 'LookupObj' );
		$lookupDetail = $lookupObj->GetLookupDetail ( $lookupKey );
		// model由lookup配置获取 分视图和正常情况
        $name = $lookupDetail ['mode'];	
        $viewname=$lookupDetail ['viewname'];
		// 获取查找带回的字段
		$this->assign ( "field", $lookupDetail ['fields'] );
		$this->assign ( "fieldval", $lookupDetail ['val'] );
		$this->assign ( "fieldname", $lookupDetail ['filed'] );
		$_POST ['dealLookupList'] = 1; // 强制查找带回重构数据列表
		$_POST ['dealLookupType'] = 1; // 新版本lookup
		
		$this->assign ( "lookupchoice", $lookupKey );
		// 来源类型 type : dt[从数据表格中点击过来的]
		//	
		$type = $_POST['type'];
		$this->assign ( "type", $type );
		/**
		 * *************************************************
		 */
		/*
		 * 本段为快速新增记录功能，需要重新规划
		 * /***************************************************
		 */
		// 获取部门类型 ————快捷新增客户
		$deptid = $_REQUEST ['deptid'];
		$this->assign ( "deptid", $deptid );
		if (strpos ( $name, '_' )) { // 将表转换为model
			$nameArr = explode ( '_', $name );
			$names = "";
			foreach ( $nameArr as $k => $v ) {
				$names .= ucfirst ( $v );
			}
			if ($names) {
				$name = $names;
			}
		}
		if (substr ( $name, - 4 ) == "View") {
			$qx_name = $name;
			$name = substr ( $name, 0, - 4 );
		}
		
		$this->assign ( "model", $name );
		// 单据号是否可写
		$table = D ( $name )->getTableName ();
		$scnmodel = D ( 'SystemConfigNumber' );
		$writable = $scnmodel->GetWritable ( $table );
		$this->assign ( "writable", $writable );
		
		$ConfigListModel = D ( 'SystemConfigList' );
		$lookupGeneralList = $ConfigListModel->GetValue ( 'lookupGeneralInclude' ); // 快速新增配置列表
		$include = $lookupGeneralList [$name]; // 获取配置信息
		$layoutH = 110; // 默认高度
		if ($include) {
			$layoutH = $include ['layoutH']; // 获取高度
			$this->assign ( "tplName", 'LookupGeneral:' . $include ['tpl'] ); // 设置默认模版
			$this->assign ( "isauto", $include ['isauto'] ); // 设置编号自动生成
		}
		$this->assign ( "layoutH", $layoutH ); // 设置高度
		/**
		 * ***********************************************************************
		 */
		
		/**
		 * ************************************************************************
		 * 本段开始是普通查找带回查询效果
		 * ***********************************************************************
		 */
		// $name="MisHrPersonnelPersonInfoView";
		$scdmodel = D ( 'SystemConfigDetail' );
		if ($lookupDetail ['viewname']) { // 读取当前模型下的视图配置文件
			$path = DConfig_PATH . "/Models/" . $lookupDetail ['mode'] . "/" . $lookupDetail ['viewname'] . ".inc.php";
			$detailList = require $path; 
			$this->assign ( "viewname", $lookupDetail ['viewname']);
			$map = $this->_searchs ( $lookupDetail ['viewname'], '', '', $detailList );
		} else {
			$detailList = $scdmodel->getDetail ( $name );
			$map = $this->_searchs ( $name );
		}
		$action = A ( "Common" );
		// ////////////////////////////////////////////// 以前的条件 ////////////////////////////////////////////////
		$conditions = $_REQUEST['conditions']; // 检索条件
		if ($conditions) {
			$conditions = str_replace ( array (
					'&quot;',
					'&#39;',
					'&lt;',
					'&gt;',
					'=='
			), array (
					'"',	
					"'",
					'<',
					'>',
					'='
			), $conditions);
			
			$this->assign ( "conditions", $conditions );
			$cArr = explode ( ';', $conditions ); // 分号分隔多个参数
			foreach ( $cArr as $k => $v ) {
				$wArr = explode ( ',', $v ); // 逗号分隔字段、参数、修饰符
				if ($wArr [0] == "_string") { // 判断是否传的为字符串条件
					//替换掉用#代替的,
					$wArr [1] = str_replace ( array (
							'#'
					), array (
							','
					), $wArr [1]);
					$map ['_string'] .= $wArr [1];
				} else {
					if ($wArr [2]) { // 存在修饰符的以修饰符形式进行检索
						$map [$wArr [0]] = array (
								$wArr [2],
								$wArr [1] 
						);
					} else { // 普通检索
						$map [$wArr [0]] = $wArr [1];
					}
				}
			}
		}

		// //////////////////////////////////////////////////////////////////////////////////
		// add by nbmxkj at 20150129 2107 lookup 默认条件自动获取
		// /////////////////////////////////////// 附加条件 ///////////////////////////////////////////
		$appendCondition = $_REQUEST['newconditions'];//附加条件从页面获取 // $_POST['newconditions'];// lookup检索条件 为兼容条件选择器 by renling

		// 视图查询时，取出别名的真实字段名
		if ($lookupDetail ['viewname']) {
			$conditionFieldResolveArr = '';
			$resolveCondition = explode('and', $appendCondition);
			foreach ($resolveCondition as $k=>$v){
				unset($temp);
				//$temp= explode('=', $v);
				$temp = preg_split('/( in )|( = )/',$v);
				$conditionFieldResolveArr[] = trim($temp[0]);
			}
			$searchAlias="";
			if(is_array($conditionFieldResolveArr)){
				$searchAlias="'".join("','", $conditionFieldResolveArr)."'";
			}
			$sql="SELECT * FROM mis_system_dataview_sub
WHERE masid=(SELECT id FROM mis_system_dataview_mas WHERE NAME='{$lookupDetail ['viewname']}')
AND `otherfield` in({$searchAlias})
AND STATUS=1";
			if($searchAlias){
				$aliasObj = M();
				$data = $aliasObj->query($sql);
				if($data){
					$findArr='';
					$replaceArr='';
					foreach ($data as $k=>$v){
						$findArr[] = $v['otherfield'];
						$replaceArr[] = $v['field'];
					}
					if($findArr && $replaceArr){
						$appendCondition = str_replace($findArr, $replaceArr, $appendCondition);
					}
				}
			}
		}
		// 读取默认条件
		unset($newconditions);
		if ($lookupDetail ['condition']) {
			/*
			 * 1、判断是否存在条件sql语句，
			 * 2、判断是否存在动态值，进行解析动态值，目前只支持   1 $uid 当前登录人，2 $time 当前时间
			 */
			$newconditions = str_replace ( array (
					'$uid','$time','$admin',
				), array (
						$_SESSION[C('USER_AUTH_KEY')],time(),$_SESSION['a'],
				), $lookupDetail ['condition']);
		}
		if ($appendCondition) {
			$newconditions .= ($newconditions ? ' and ' : '') . $appendCondition;
		}
		
		// 数据过滤条件 by nbmxkj@20150617 1555
		$filterSouce = getCurrentUserDataRight(1 , D($lookupDetail['mode'])->getTableName()  , $lookupDetail['val'] ,true);
		if($filterSouce){
			$dataFilterMaps =$filterSouce;// key($filterSouce)." in ('".join("','", reset($filterSouce))."')";
			// 在需要时可以在此处加上过滤条件格式验证，防止异常。
		}
		
		if (trim($newconditions)) {
			$newconditions = str_replace ( array (
					'&quot;',
					'&#39;',
					'&lt;',
					'&gt;',
					'==' 
			), array (
					'"',
					"'",
					'<',
					'>',
					'=' 
			), $newconditions );
			// 把条件加入map 中
			$map ['_string'] .= $newconditions;
		}
		if($dataFilterMaps){
			$map ['_string'] .=  $map ['_string'] ? ' and '.$dataFilterMaps : $dataFilterMaps;
		}
		//////////////////////////////////////////////////////////////////////////////////////////////
		//					树菜单																									//
		//////////////////////////////////////////////////////////////////////////////////////////////
		if($lookupDetail['tree']){
			
			$this->assign('treecount' , count($lookupDetail['tree']));
			$treeContentArr='';
			$treeindex = $_REQUEST['treeindex'];
			$lookuptreeHidden="";
			if(isset($treeindex)){
				$treetext =$lookupDetail['tree'][$treeindex]['treetext'];
				$treetextArr = explode(',', $treetext);
				foreach ($treetextArr as $k=>$v){
					if($_REQUEST[$treetextArr[$k]]){
						$map[$treetextArr[$k]]=$_REQUEST[$treetextArr[$k]];
						$lookuptreeHidden .= "<input type=\"hidden\" name=\"{$treetextArr[$k]}\" value=\"{$_REQUEST[$treetextArr[$k]]}\" />";
					}
				}
				$lookuptreeHidden .= "<input type=\"hidden\" name=\"treeindex\" value=\"0\" />";
			}
			$this->assign('lookuptreeHidden' , $lookuptreeHidden);
			foreach ($lookupDetail['tree'] as $key=>$val){
				
				$treeModel = $val['treemodel'];
				$treevalue = $val['treevalue'];
				$treeshow = $val['treeshow'];
				$treetext = $val['treetext'];
				$treetitle = $val['treetitle'];
				$treecondition = $val['treecondition'];
				$treelength = $val['treelength'] ? $val['treelength'] : 10 ;
				
				$treevalueArr = explode(',', $treevalue);
				$treetextArr = explode(',', $treetext);
				
				//构造左侧部门树结构
				$model= M($treeModel);
				$departmentmap['status']=1;
				if($treecondition){
					$departmentmap['_string'] = $treecondition;
				}
                $ret = $model->where($departmentmap)->field("parentid")->find();
                
                if(false === $ret){
				    $deptlist = $model->where($departmentmap)->field("$treeshow as name , id , 0 as parentid , {$treevalue}")->order("id asc")-> limit($treelength) -> select();
                }else{
                    $deptlist = $model->where($departmentmap)->field("$treeshow as name , id ,parentid , {$treevalue}")->order("id asc")-> limit($treelength) -> select();
                }
				$urlParame = '/treeindex/'.$key;
				foreach ($treevalueArr as $k=>$v){ // '.$v.'
					$urlParame .= '/'.$treetextArr[$k].'/#'.$v.'#';
				}
				
				$param['rel']="treelookupcontent";
				$param['url']="__URL__/lookupGenerals/jump/1{$urlParame}/lookupchoice/".$lookupKey;
				$common = A('Common');
				$typeTree = $common->getTree($deptlist,$param);
				$treeContentArr[] = array('data'=>$typeTree,'title'=>$treetitle);
			}
			$this->assign('treedata',$treeContentArr);
		}
		//////////////////////////////////////////////////////////////////////////////////////////////
		//					树菜单 end																							//
		//////////////////////////////////////////////////////////////////////////////////////////////
		
		
		
		// 以树形列表显示数据。
		if( $lookupDetail['mode'] && $lookupDetail['treelist'] && $lookupDetail['treelist']['show'] && $lookupDetail['treelist']['value'] && $lookupDetail['treelist']['parentid'] ){
		    $searchFields = $lookupDetail['fields'];
		    $this->assign('searchFieldsArr' , explode(',', $lookupDetail['listshowfields']));
		    $model=D("MisSystemRecursion");
		    $id = $lookupDetail['treelist']['value'];
		    $name = $lookupDetail['treelist']['show'];
		    $parentid = $lookupDetail['treelist']['parentid'];
		    $isnextend = $lookupDetail['treelist']['isnextend'];
		    $searchFieldsArr = explode(',', $searchFields);
		    if(!in_array($id, $searchFieldsArr)){
		        array_push($searchFieldsArr, $id);
		    }
		    if(!in_array($name, $searchFieldsArr)){
		        array_push($searchFieldsArr, $name);
		    }
		    if(!in_array($parentid, $searchFieldsArr)){
		        array_push($searchFieldsArr, $parentid);
		    }
		    $searchFields = join(',', $searchFieldsArr);
		    $this->assign('value' , $id);
		    $this->assign('show' , $name);
		    $this->assign('detailList' , $detailList);
		    $this->assign('isnextend' , $isnextend);
		    if($lookupDetail ['viewname']){
		        // 视图
		        $data = D($lookupDetail ['viewname'])->where($map)->select();
		        $treeSelectselect6Data = $model->dataShow($data , array('key'=>$id,'pkey'=>$parentid,'conditions'=>$newconditions,'fields'=>"$searchFields") , 0 , 1);
		        
		       // print_r($data);
		       
		    }else{
		        // 模型
		        $table = D($lookupDetail['mode'])->getTableName();
		        $data = M($table)->where($map)->select();		        
		        $treeSelectselect6Data = $model->modelShow($table , array('key'=>$id,'pkey'=>$parentid,'conditions'=>$newconditions,'fields'=>"$searchFields") , 0 , 1);
		    }
		    //如果树形数据和原数据不一致（原因为父级缺失，补充父级记录）
		    if(count($data)!=count($treeSelectselect6Data)&&count($data)>0){
		    	foreach($data as $tk=>$tv){
		    		$newTreeList[$tv[$id]] = $tv;
		    	}
		    	//获取无条件数据
		    	if($lookupDetail ['viewname']){
		    		$dataNoMap = D($lookupDetail ['viewname'])->select();
		    	}else{
		    		$table = D($lookupDetail['mode'])->getTableName();
		    		$dataNoMap = M($table)->select();
		    	}		    	
		    	//show_tree_node -> 判断该记录是否选择
		    	foreach($dataNoMap as $tk2=>$tv2){
		    		if(empty($newTreeList[$tv2[$id]])){
		    			$dataNoMap[$tk2]['show_tree_node']=0;
		    		}else{
		    			$dataNoMap[$tk2]['show_tree_node']=1;
		    		}
		    	}
		    	$temp2 = $this->sonParentLine($data,$dataNoMap,$id,$parentid);
		    	
		    	//重新实例化MisSystemRecursion，把$temp2排序
		    	$ddds = D("MisSystemRecursion");
		    	$ddds->__construct();
		    	$treeSelectselect6Data = $ddds->dataShow($temp2 , array('key'=>$id,'pkey'=>$parentid,'conditions'=>'','fields'=>"$searchFields") , 0 , 1);
		    }

		    $this->assign('vo' , $treeSelectselect6Data);
		    $this->display('Public:lookupTreeList');
		    exit;
		}
		if($lookupKey=='2ba96fc5ab7bef0ac9a4d3f5d72bb1f0'){
			$userid=$_SESSION[C('USER_AUTH_KEY')];
			$viewModel=D('MisHrPersonnelUserDeptView');
			 $companyList=$viewModel->where('user.id='.$userid)->field('companyid')->select();
			foreach ($companyList as $comk=>$comv){
				$companyArr[]=$comv['companyid'];
			} 
			//构造左侧部门树结构
			$model= M('mis_system_department');
			$departmentmap['status']=1;
			if(!empty($companyList) && $userid!=1){
				//$departmentmap['companyid']=array('in',$companyArr);
			}
			$deptlist = $model->where($departmentmap)->order("id asc")->select();
			$param['rel']="misrowaccessright1";
			$param['url']="__URL__/lookupGenerals/jump/1/deptid/#id#/parentid/#parentid#/companyid/#companyid#/lookupchoice/".$lookupKey;
			$common = A('Common');
			$typeTree = $common->getTree($deptlist,$param);
			//获得树结构json值
			$this->assign('tree',$typeTree);
			
			if($_REQUEST['companyid']!=null){
				$map['companyid']=$_REQUEST['companyid'];
			}else{
				$map['companyid']=$deptlist[0]['companyid'];
			}
			$parentid	= $_REQUEST['parentid'];	//获得父类节点
			$companyid	= $_REQUEST['companyid'];
			if ($deptid && $parentid) {
				$deptlist =array_unique(array_filter (explode(",",$this->downAllChildren($deptlist,$deptid))));
				$map['deptid'] = array(' in ',$deptlist);
			}
			$this->assign('parentid',$parentid);
			$this->assign('companyid',$companyid);
		}
		
		// ////////////////////////////////////// 附加条件end /////////////////////////////////////////
		$this->assign ( "newconditions", $newconditions );
		if (! $_REQUEST ['viewname']) {
			if($lookupKey=='2ba96fc5ab7bef0ac9a4d3f5d72bb1f0'){
				$map ['user.status'] = 1;
			}else{
				if(!getFieldBy($lookupKey, "id", "viewname", "mis_system_lookupobj")){
					$map ['status'] = 1;
				}
			}
		}
		$filterfield = "_lookupGeneralfilter";
		if ($_REQUEST ['filtermethod'])
			$filterfield = $_REQUEST ['filtermethod'];
		if (method_exists ( $this, $filterfield )) {
			call_user_func ( array (
					&$this,
					$filterfield 
			), &$map );
		}
		// if($_REQUEST['viewname']){
		// $name = $_REQUEST['viewname'];
		// }else
		// {
		// $name = $_POST['model'];
		// }
		if ($viewname) {
			$sortstr = $lookupDetail['datasort']?$lookupDetail['datasort']:'';
			$action->_viewlist ( $viewname, $map, $sortBy = '', $asc = false, $group = '','',$sortstr );
		} else {
			//$action->_list( $name, $map, $sortBy = '', $asc = false, $group = '' );
			//   $echoSql=1
			$sortstr = $lookupDetail['datasort']?$lookupDetail['datasort']:'';
		//$sortstr = 'id asc';
			self::_list($name, $map, $sortBy = '', $asc = false, $group = '','',$sortstr );
		}
		/*
		 * 2ba96fc5ab7bef0ac9a4d3f5d72bb1f0
		 * 代表用户视图形lookup参照。特殊处理。
		 * a945d1a1f0487f8621c1d7797b894295
		 * 
		 * 
		 */
		
		if($lookupKey=='2ba96fc5ab7bef0ac9a4d3f5d72bb1f0'||$lookupKey=='a945d1a1f0487f8621c1d7797b894295'){//2ba96fc5ab7bef0ac9a4d3f5d72bb1f0 b6fe179d38c8034cee79b5b09dd6b1e7
			if($_REQUEST['jump']){// lookupUserGenerals
				$this->display ( 'Public:lookupUserGenerals' );
			}else{
				$this->display ( 'Public:lookupBackendUser' );
			}
		}else{
		//$this->assign("detailList",$detailList);
			if ($lookupDetail ['dt']) { // 内嵌表 页面 带回多条记录
				$dt = base64_encode(base64_encode(serialize($lookupDetail ['dt'])));
				$this->assign('dt',$dt);
				$this->assign("samefield",$lookupDetail['fieldcom']);
				$this->display ( 'Public:lookupfordatatable2' );
			} elseif($_REQUEST ['viewtype']=="checkfor") {
				$this->display('CheckFor:checkForNew');
			} elseif($_POST["type"]=="dt" || $_REQUEST['ismuchchoice']==1) {
				//echo 2323;
				$check_list = html_entity_decode($_POST["check_list"]);
				$check_list_arr = json_decode($check_list, TRUE);
				$this->assign('check_list',$check_list);
				$this->assign('check_list_arr',$check_list_arr);
				$this->assign('type',$_POST["type"]);
				$this->assign('ismuchchoice',$_REQUEST['ismuchchoice']);
				
				// 数据表格中的lookup实现按配置生成树形导航功能。
				// modify by nbmxkj@20150818 1414
				if($lookupDetail['tree']){
					//多选树形结构lookup带回
        			if($_REQUEST['jump']){
        				$this->display ( 'Public:lookupGenerals' );
        			}else {
        				$this->display('Public:lookupGeneralsTree');
        			}
                }else{
                	//多选非树形结构lookup带回
                	if($_REQUEST['jump']){
                		$this->display ( 'Public:lookupBackendUserCenterRight' );
                	}else {
                		$this->display('Public:lookupBackendUserCenter');
                	}
                }
			} else{
			    if($lookupDetail['tree']){
        			if($_REQUEST['jump']){
        				$this->display('Public:lookupGenerals');
        			}else {
        				$this->display('Public:lookupGeneralsTree');
        			}
                }else{
                    $this->display('Public:lookupGenerals');
                }
			}
		}
	}	
/**
 * @Title: sonParentLine
 * @Description: todo(两组二维数组对比找父级，用在lookupgenerals里的条件过滤) 
 * @param unknown_type $data1 $data2的部分数据array(array("id"=>4,"parentid"=2),,array("id"=>5,"parentid"=1))
 * @param unknown_type $data2 所有数据 array(array("id"=>4,"parentid"=2),array("id"=>3,"parentid"=1),array("id"=>2,"parentid"=1),array("id"=>1,"parentid"=0),array("id"=>5,"parentid"=1),array("id"=>7,"parentid"=0))
 * @param unknown_type $id 唯一字段
 * @param unknown_type $parentid 父级字段  
 * @author 谢友志 
 * @date 2015-10-22 下午2:07:14 
 * @throws
 */
public function sonParentLine($data1,$data2,$id='id',$parentid='parentid'){
	//将数组改为以$id为key的数组（$id 唯一）
	$list2 = array();
	foreach($data2 as $dk=>$dv){
		$list2[$dv[$id]] = $dv;
	}
	
	$newdata1=array();
	foreach($data1 as $k=>$v){
		$newdata1[$v[$id]] = $list2[$v[$id]];//以data2数据为主，防止$data1数据不全 exp:$data1只有id，parentid;$data2有很多元素（包含id,parentid）
	}
	//数据包含的父级id	
	$newdata2 = array();
	foreach($newdata1 as $k2=>$v2){
		$newdata2[] = $v2[$parentid];
	}
	//将$data1的父级数据提出来
	$newdata3 = array();
	foreach($list2 as $k3=>$v3){
		if(in_array($k3,$newdata2)){
			$newdata3[$k3] = $v3;
		}
	}
	//索引数组合并，数据会叠加，所以改key为字符型
	$newdata4 = array();
	$newdata5 = array();
	foreach($newdata1 as $mk=>$mv){
		$newdata4["f".$mk] = $mv;
	}
	foreach($newdata3 as $mk2=>$mv2){
		$newdata5["f".$mk2] = $mv2;
	}
	//合并本身数据和父级数据数组
	$dataid = array_merge($newdata4,$newdata5);
	$newlist = array();
	//重构成标准的查询数据库记录格式
	foreach($dataid as $key=>$val){
		$newlist[]=$val;
	}
	//如果没找到顶级数据，再次运行这个方法查找
	if(count($data1)!=count($dataid)){
		$newlist = $this->sonParentLine($newlist,$data2,$id,$parentid);
	}
	return $newlist;
}
     /**
	 * @Title: lookupSalesProject
	 * @Description: todo(普通常用查找带回)
	 * @author 杨东
	 * @date 2013-7-9 下午6:04:06
	 * @throws
	 */
	public function lookupGeneral(){
		//获取查找带回的字段
		$this->assign("field",$_REQUEST['field']);
		$_POST['dealLookupList'] = 1;//强制查找带回重构数据列表
		$_POST['dealLookupType'] = 0;//老版本lookup兼容
		$name = $_POST['model'];
		/****************************************************/
		/*本段为快速新增记录功能，需要重新规划
	    /****************************************************/
		//获取部门类型 ————快捷新增客户
		$deptid=$_REQUEST['deptid'];
		$this->assign("deptid",$deptid);
		if(strpos($name, '_')){//将表转换为model
			$nameArr = explode('_', $name);
			$names = "";
			foreach ($nameArr as $k => $v) {
				$names .= ucfirst($v);
			}
			if($names){
				$name = $names;
			}
		}
		if(substr($name, -4)=="View"){
			$qx_name = $name;
			$name = substr($name,0, -4);
		}
		
		$this->assign("model",$name);
		// 单据号是否可写
		$table = D($name)->getTableName();
		$scnmodel = D('SystemConfigNumber');
		$writable= $scnmodel->GetWritable($table);
		$this->assign("writable",$writable);

		$ConfigListModel = D('SystemConfigList');
		$lookupGeneralList = $ConfigListModel->GetValue('lookupGeneralInclude');// 快速新增配置列表
		$include = $lookupGeneralList[$name];//获取配置信息
		$layoutH = 110;//默认高度
		if($include){
			$layoutH = $include['layoutH'];//获取高度
			$this->assign("tplName",'LookupGeneral:'.$include['tpl']);//设置默认模版
			$this->assign("isauto",$include['isauto']);//设置编号自动生成
		}
		$this->assign("layoutH",$layoutH);//设置高度
		/**************************************************************************/
		
		
		/**************************************************************************
		 * 本段开始是视图查询效果
		 *************************************************************************/
		if($_REQUEST['viewname']){
			//视图查找带回
			$MisSystemDataviewMasViewModel=D("MisSystemDataviewMasView");
			$MisSystemDataviewMasList=$MisSystemDataviewMasViewModel->where("name='{$_REQUEST['viewname']}'  and modelname='{$_REQUEST['model']}'")->select();
			$list=$MisSystemDataviewMasViewModel->query($MisSystemDataviewMasList[0]['replacesql']); 
			$viesList=array();
			$detailList=array();
			foreach($list as $key =>$val){
				foreach ($MisSystemDataviewMasList as $msdkey=>$msdval){
					$isshow=0;
					if($msdval['islistshow']==1)$isshow=1;
					$detailList[$msdkey]=array(
							'name' => $msdval['otherfield'],
							'showname' => $msdval['subtitle'],
							'shows' => $isshow,
							'widths' => '',
							'sorts' => '0',
							'models' => '',
							'sortname' => $msdval['otherfield'],
							'sortnum' => '5',
							'searchField' => $msdval['field'],
							'conditions' => '',
							'type' => 'text',
							'issearch' => '1',
							'isallsearch' => '1',
							'searchsortnum' => '0',
							'helpvalue' => '',
							
					);
					if($msdval['funcdata']){
						$detailList[$msdkey]['func'][][]=$msdval['funname'];
					$detailList[$msdkey]['funcdata'][][]=unserialize(base64_decode($msdval['funcdata']));
					}
				}
			}
			//print_r($detailList);
			$this->dealLookupList($list);
// 			$this->assign("viewname",$_REQUEST['viewname']);
			$this->assign("list",$list);
		}else{
			/**************************************************************************
			 * 本段开始是普通查找带回查询效果
			*************************************************************************/
			$scdmodel = D('SystemConfigDetail');
			$detailList = $scdmodel->getDetail($name);
			if ($detailList) {
				$inArray = array('action','remark','status');
				//$_REQUEST['filterfield']一直没传递值，做什么用的？
				if ($_REQUEST['filterfield']) {
					$filterfield = explode(',', $_REQUEST['filterfield']);
					$inArray = array_merge($inArray,$filterfield);
					$this->assign('filterfield',$_REQUEST['filterfield']);
				}
				foreach ($detailList as $k => $v) {
					if(in_array($v['name'], $inArray)){
						unset($detailList[$k]);
					}
				}
			}
			$action=A("Common");
			$map = $this->_search($name);
			$conditions = $_POST['conditions'];// 检索条件
			if($conditions){
				$this->assign("conditions",$conditions);
				$cArr = explode(';', $conditions);//分号分隔多个参数
				foreach ($cArr as $k => $v) {
					$wArr = explode(',', $v);//逗号分隔字段、参数、修饰符
					if ($wArr[0] == "_string") { // 判断是否传的为字符串条件
						$map['_string'] = $wArr[1];
					} else {
						if ($wArr[2]) {//存在修饰符的以修饰符形式进行检索
							$map[$wArr[0]] = array($wArr[2],$wArr[1]);
						} else {//普通检索
							$map[$wArr[0]] = $wArr[1];
						}
					}
				}
			}
			$newconditions = $_POST['newconditions'];// lookup检索条件 为兼容条件选择器 by renling
			if($newconditions){
				$newconditions= str_replace (array ('&quot;','&#39;','&lt;','&gt;'), array ('"', "'",'<','>'),$newconditions);
				//把条件加入map 中
				$map['_string'].=$newconditions;
			}
			$map['status'] = 1;
			$filterfield = "_lookupGeneralfilter";
			if($_REQUEST['filtermethod']) $filterfield = $_REQUEST['filtermethod'];
			if (method_exists($this,$filterfield)) {
				call_user_func(array(&$this,$filterfield),&$map);
			}
			
			if ($qx_name) {
				$action->_list ( $qx_name, $map );
			} else {
				$action->_list ( $name, $map );
			}
		}
		$this->assign ( 'detailList', $detailList );
		if($_POST['lookuptodatatable']==1){//当有lookuptodatatable传值时，跳转为专为datatable做的页面 带回多条记录 
			//<a class="input-addon input-addon-addon input-addon-add" param="field=id,orderno,name,projectname,createtime&model=MisAutoEbm&conditions=status,1&lookuptodatatable=1&datatablehtmlid=into_table_new_one" href="__URL__/lookupGeneral" lookupGroup="ORG2" >{$fields["projectcode"]}</a>
			//datatablehtmlid为填充datatable的id
			$this->assign('datatablehtmlid',$_REQUEST['datatablehtmlid']);
			$this->display('Public:lookuptodatatable');
		}elseif($_POST['lookuptodatatable']==2){
			$this->assign("samefield",$_POST['fieldcomname']);
			$this->assign('dt',$_POST['dt']);
			$this->display('Public:lookupfordatatable2');
		} elseif($_REQUEST ['viewtype']=="checkfor") {
			$this->display('CheckFor:checkForNew');
		}else{
			$this->display('Public:lookupGeneral');
		}
	}
	/**
	 * @Title: lookupson
	 * @Description: todo(根据lookupgeneral生成的页面lookupfordatatable2生成查找主数据)   
	 * @author 谢友志 
	 * @date 2015-1-12 上午10:16:15 
	 * @throws
	 */
	function lookupson(){
		//从lookupfordatatable2接收到的数据 赋值到lookupson页面
		$userid = $_SESSION[C('USER_AUTH_KEY')];		
		$this->assign('modelname',$_REQUEST['model']);
		if($_GET['lookupdatabaseJson']){
			$_POST['lookupJson'] = base64_decode($_GET['lookupdatabaseJson']);
		}
		$this->assign('lookupJson',$_POST['lookupJson']);//主数据带回的json
		$id=$_REQUEST['masid'];
		$this->assign("masid",$id);	
		if(strpos($id,',')){
			$idarr = explode(",",$id);
			$map['masid'] = array("in",$idarr);
		}else{
			$map['masid']=$id;
		}			
		$this->assign('samefield',$_POST['samefield']);
		$mrdmodel = D('MisRuntimeData');
		if($_POST['dt']){
			$mrdmodel->setRuntimeCache($_POST['dt'],$lookupdt,$userid.'_lookupdt');
		}else{
			$_POST['dt'] = $mrdmodel->getRuntimeCache($lookupdt,$userid.'_lookupdt');
		}
		$dt=unserialize(base64_decode(base64_decode($_POST['dt'])));//子表数据
		
// 		//强制子表显示id项
// 		$unshiftid['id'] = array(
// 					'title' => 'ID',
// 					'category' => 'text',
// 					'length' => '10',
// 				);
// 		foreach($dt as $dk=>$dv){
// 			if(!$dv['list']['id']){
// 				$dt[$dk]['list'] = array_merge($unshiftid,$dt[$dk]['list']) ;
// 			}			
// 		}
		$this->assign('jsondt',$_POST['dt']);
		$this->assign('dt',$dt);
		$dtkey = array_keys($dt);
		//子表在分页时尚未真实获取（多表时）//
		if($_REQUEST['subtable']){
			$subtable=$_REQUEST['subtable'];
		}else{
			//全部子数据入库 2015-10-15 by xyz
			//先清除可能留下的数据（如果以前选择了子数据，又直接关闭页面会残留数据）
			$delmodel = M("mis_lookup_datatable");
			$delmap['mainmodel'] = $_REQUEST['model'];
			$delmap['mainid'] = $id;
			$delmap['userid'] = $_SESSION[C('USER_AUTH_KEY')];			
			$delmodel->where($delmap)->delete();
			//入库
			$dbmodel = M("mis_lookup_datatable");
			foreach($dtkey as $key=>$val){
				$subtable = $val;
				$datename=str_replace(' ','',ucwords(str_replace('_',' ',$subtable)));
				$filepath=DConfig_PATH . "/Models/".$_REQUEST['model']."/{$datename}.inc.php";
				$subfiles = array();
				if(is_file($filepath)){
					$subfiles = require $filepath;
				}else{
					echo $filepath;exit;
					$this->error("配置文件不存在");
				}
				
				$model=M($subtable);
				$condition = $dt[$subtable]['condition'];
				if($conditions){
					$this->assign("conditions",$conditions);
					$cArr = explode(';', $conditions);//分号分隔多个参数
					foreach ($cArr as $k => $v) {
						$wArr = explode(',', $v);//逗号分隔字段、参数、修饰符
						if ($wArr[0] == "_string") { // 判断是否传的为字符串条件
							$map['_string'] = $wArr[1];
						} else {
							if ($wArr[2]) {//存在修饰符的以修饰符形式进行检索
								$map[$wArr[0]] = array($wArr[2],$wArr[1]);
							} else {//普通检索
								$map[$wArr[0]] = $wArr[1];
							}
						}
					}
				}
				$dblist = $model->field($fields)->where($map)->select();//子表记录
				//内嵌表回调字段
				$dtfuncfields = $dt[$subtable]['dtfuncfields'];
				$dtfunc = $dt[$subtable]['dtfuncinfo'];
				foreach($dblist as $k1=>$v1){
					//$dblist[$k1]['json_code'] = json_encode($v1);//子表记录json
					$dblist[$k1]['json_code_key'] =$k.'_'.$k1;
					$funcv1 = $v1;
					foreach($v1 as $k2=>$v2){
						if(count($subfiles[$k2]['func'])>0){
							//中间内容
							$dblist[$k1][$k2."_alias"] = getConfigFunction($v2,$subfiles[$k2]['func'][0],$subfiles[$k2]['funcdata'][0],$v1);
						}
					}
					foreach($funcv1 as $k3=>$v3){
						if($dtfuncfields&&in_array($k3,$dtfuncfields)&&count($dtfunc[$k3]['funcname'])>0){
							$funcv1[$k3] =  $this->fieldFuncChangeForLookupSon($k3,$v3,$dtfunc);
						}
					}
					$dblist[$k1]['json_code'] = json_encode($funcv1);//子表记录json
					
					$dbdata['subjson'] = json_encode($funcv1);//子表记录json
					$dbdata['mainid'] = $id;
					$dbdata['mainmodel'] = $_REQUEST['model'];
					$dbdata['subtable'] = $subtable;
					$dbdata['lookupJson'] = $_POST['lookupJson'];
					$dbdata['userid'] = $_SESSION[C('USER_AUTH_KEY')];
						
					$dbmodel->add($dbdata);
				}
				//$dbalist[$val]=$dblist;
			}
$dbmodel->commit();
			//print_r($dbalist);
			//重置$subtable
			$subtable=$dtkey[0];
		}
		//子表配置文件
		$datename=str_replace(' ','',ucwords(str_replace('_',' ',$subtable)));
		$filepath=DConfig_PATH . "/Models/".$_REQUEST['model']."/{$datename}.inc.php";
		$subfiles = array();
		if(is_file($filepath)){
			$subfiles = require $filepath;
		}else{
			echo $filepath;exit;
			$this->error("配置文件不存在");
		}
		
		$model=M($subtable);
		$condition = $dt[$subtable]['condition'];
		if($conditions){
			$this->assign("conditions",$conditions);
			$cArr = explode(';', $conditions);//分号分隔多个参数
			foreach ($cArr as $k => $v) {
				$wArr = explode(',', $v);//逗号分隔字段、参数、修饰符
				if ($wArr[0] == "_string") { // 判断是否传的为字符串条件
					$map['_string'] = $wArr[1];
				} else {
					if ($wArr[2]) {//存在修饰符的以修饰符形式进行检索
						$map[$wArr[0]] = array($wArr[2],$wArr[1]);
					} else {//普通检索
						$map[$wArr[0]] = $wArr[1];
					}
				}
			}
		}
		$this->assign('subtable',$subtable);
		//排序
		$orderfield = $dt[$subtable]['orderfield']?$dt[$subtable]['orderfield']:'id';
		$ordersort = $dt[$subtable]['ordersort']?$dt[$subtable]['ordersort']:'asc';
		
		$order = $orderfield.' '.$ordersort;
		$list=array(); //返回数组
		//分页查询
		import("ORG.Util.Page");// 导入分页类
		$count = $model->where($map)->count();// 查询满足要求的总记录数
		$Page = new Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数
		foreach($map as $key=>$val) {		
			$Page->parameter   .=   "$key=".urlencode($val)."&";		
		}
		$Page->parameter   .= "model=".urlencode($_REQUEST['model'])."&";
		$show = $Page->show();
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
		$list['list'] = $model->field($fields)->where($map)->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();//子表记录
		$this->assign('page',$show);// 赋值分页输出
		
		//内嵌表回调字段
		$dtfuncfields = $dt[$subtable]['dtfuncfields'];
		$dtfunc = $dt[$subtable]['dtfuncinfo'];
		foreach($list['list'] as $k1=>$v1){
			//$list['list'][$k1]['json_code'] = json_encode($v1);//子表记录json
			$list['list'][$k1]['json_code_key'] =$k.'_'.$k1;
			$funcv1 = $v1;
			foreach($v1 as $k2=>$v2){
				if(count($subfiles[$k2]['func'])>0){
					//中间内容
					$list['list'][$k1][$k2."_alias"] = getConfigFunction($v2,$subfiles[$k2]['func'][0],$subfiles[$k2]['funcdata'][0],$v1);
				}
			}
			
			foreach($funcv1 as $k3=>$v3){	
				if($dtfuncfields&&in_array($k3,$dtfuncfields)&&count($dtfunc[$k3]['funcname'])>0){
					$funcv1[$k3] =  $this->fieldFuncChangeForLookupSon($k3,$v3,$dtfunc);
					//$funcv1[$k3] = getConfigFunction($v3,$dtfunc[$k3]['funcname'][0],$dtfunc[$k3]['funcdata'][0]);
				}
			}
			$list['list'][$k1]['json_code'] = json_encode($funcv1);//子表记录json
			
		}
// 		$list['fieldstitle']=$fieldstitle; //字段中文名称
// 		$list['title']=$v['title'];	//表中文名称
		//已选中的记录
		
		$secmodel = M("mis_lookup_datatable");
		$seclist = $secmodel->select();
		$this->assign('seclist',$seclist);
		//print_r($list['list']);
		$this->assign('list',$list);
		if($_REQUEST['subtable']){
			$this->display("Public:lookupsonextend");
		}else{
			$this->display("Public:lookupson");
		}
		

		
	}
	/**
	 * @Title: lookupsoninsert
	 * @Description: todo(带回子数据（包含主数据）lookupson页面的数据)   
	 * @author 谢友志 
	 * @date 2015-1-12 上午10:12:49 
	 * @throws
	 */
	function lookupsoninsert(){
		$lookupJson = json_decode(html_entity_decode($_POST['lookupJson']),true);
		$newlist = $lookupJson;
		//从临时表mis_lookup_datatable读取数据
		$model = M("mis_lookup_datatable");
		$userid = $_SESSION[C('USER_AUTH_KEY')];
		$list = $model->where("userid={$userid}")->select();
		//$mainlist = $model->distinct(true)->field('lookupJson')->select();
		$subtable = $model->distinct(true)->field('subtable')->where("userid={$userid}")->select();
 		$lookupjson =$list[0]['lookupJson'];
// 		$newlist = array();
		if($subtable){
// 			/**
// 			 * 生单操作修改：主表数据存储格式为序列化数组，
// 			 * modify by nbmxkj@20150602 2054
// 			 */
// 			if($lookupjson){
// 				$lookupjson = unserialize($lookupjson);
// 				$list[0]['lookupJson'] = $lookupjson;
// 			}
// 			$newlist = $list[0]['lookupJson'];
			foreach($subtable as $k1=>$v1){
				foreach($list as $k2=>$v2){
					if($v2['subtable']==$v1['subtable']){
						$newlist['datatable_data'][$v1['subtable']][]=json_decode($v2['subjson'],true);
					}
				}
			}
			
			//读取完成，删除表数据
			$sql = "delete from mis_lookup_datatable where userid={$userid}";
			$model=M();
			$model->query($sql);
			$model->commit();
// 		}else{
// 			$newlist = json_decode(html_entity_decode($lookupjson),true);
		}
		ob_clean();		
 		echo json_encode($newlist);
	}
	/**
	 * @Title: lookupinsert
	 * @Description: todo(lookupson页面单击选中复选框数据入库)   
	 * @author 谢友志 
	 * @date 2015-1-12 上午10:28:55 
	 * @throws
	 */
	function lookupinsert(){
		$data['userid'] = $_SESSION[C('USER_AUTH_KEY')];
		$data['mainmodel'] = $_POST['model'];
		//$data['mainid'] = $_POST['masid'];
		
		$data['mainid'] = $_POST['masid'];
// 		$data['lookupJson'] = $_POST['lookupJson'] ? serialize( json_decode($_POST['lookupJson'] , true) ) : '';

		
		//$_POST['lookupJson'] = json_encode( html_entity_decode($_POST['lookupJson']) );
		
		
		$_POST['lookupJson']  = str_replace('\"',  '"' , json_encode( html_entity_decode( $_POST['lookupJson'] ) ) );
		// 去除开始结束“
		if( substr($_POST['lookupJson'], 0, 2) == '"{' && substr($_POST['lookupJson'], -2, 2) == '}"'){
			$_POST['lookupJson'] = substr($_POST['lookupJson'], 1);
			$_POST['lookupJson'] = substr($_POST['lookupJson'], 0 , -1);
		}
		
		$data['lookupJson'] = $_POST['lookupJson'] ? serialize(json_decode( $_POST['lookupJson']  , true ) ) : '';
		
		$data['subtable'] = $_POST['subtable'];
		
		
		//$_POST['subjson']  = str_replace('\"','"',json_encode( html_entity_decode( $_POST['subjson'] ) )) ;
		
		// 去除开始结束“
		if( substr($_POST['subjson'], 0, 2) == '"{' && substr($_POST['subjson'], -2, 2) == '}"'){
			$_POST['subjson'] = substr($_POST['subjson'], 1);
			$_POST['subjson'] = substr($_POST['subjson'], 0 , -1);
		}
		$data['subjson'] = html_entity_decode( $_POST['subjson'] );
// 		//字段函数转换
// 		$subinfo = json_decode($data['subjson'],true);
// 		$dt=unserialize(base64_decode(base64_decode($_POST['jsondt'])));
// 		if(!empty($dt[$_POST['subtable']]['dtfuncinfo'])){
// 			$func = $dt[$_POST['subtable']]['dtfuncinfo'];
// 			$funcfields = array_keys($func);
// 			foreach($subinfo as $key=>$val){
// 				if(in_array($key,$funcfields)){
// 					$subinfo[$key] = $this->fieldFuncChangeForLookupSon($key,$val,$func);
// 				}
				
// 			}
// 		}
//		$data['subjson'] = json_encode($subinfo);
		$model = M('mis_lookup_datatable');
		$map = $data;
		unset($map['lookupJson']);
		$rs = $model->where($map)->find();
		
		//$rs = $model->where($data)->find();
		if(!$rs){
			$model->add($data);
			//echo $model->getLastSql();
			$model->commit();
		} 
	}
	public function fieldFuncChangeForLookupSon($field,$val,$dtfuncinfo){
		$s = $val;
		foreach($dtfuncinfo as $dkey=>$dval){
			if($field==$dkey){
				$fun_arr = explode(";",$dval['funcdata'][$field][0][0]);
				foreach($fun_arr as $k=>$v){
					if($v=="###"){
						$fun_arr[$k]=$val;
					}
				}
				$c = count($fun_arr);
				$fun_name = $dval['funcname'][$field][0][0];				
				$s = callfun($c,$fun_name,$fun_arr);
			}
		}
		return $s;
	}
	/**
	 * @Title: lookupsondel
	 * @Description: todo(lookupson页面单击取消复选框数据删除)   
	 * @author 谢友志 
	 * @date 2015-1-12 上午10:30:32 
	 * @throws
	 */
	function lookupsondel(){
		$data['userid'] = $_SESSION[C('USER_AUTH_KEY')];
		$data['mainmodel'] = $_POST['model'];
		$data['mainid'] = $_POST['masid'];
		$_POST['lookupJson']  = str_replace('\"',  '"' , json_encode( html_entity_decode( $_POST['lookupJson'] ) ) );
		// 去除开始结束“
		if( substr($_POST['lookupJson'], 0, 2) == '"{' && substr($_POST['lookupJson'], -2, 2) == '}"'){
			$_POST['lookupJson'] = substr($_POST['lookupJson'], 1);
			$_POST['lookupJson'] = substr($_POST['lookupJson'], 0 , -1);
		}
		
		//$data['lookupJson'] = $_POST['lookupJson'] ? serialize(json_decode( $_POST['lookupJson']  , true ) ) : '';
		$data['subtable'] = $_POST['subtable'];
		$data['subjson'] = html_entity_decode($_POST['subjson']);
		$model = M('mis_lookup_datatable');
		$model->where($data)->delete();
		//echo $model->getlastsql();
		$model->commit();
		
	}
	/**
	 * @Title: lookupson
	 * @Description: todo(lookupson页面全选复选框数据入库)   
	 * @author 谢友志 
	 * @date 2015-1-12 上午11:14:16 
	 * @throws
	 */
	function lookupsonallinsert(){
		$model = M('mis_lookup_datatable');
		$data['userid'] = $_SESSION[C('USER_AUTH_KEY')];
		$data['mainmodel'] = $_POST['model'];
		//$data['mainid'] = $_POST['masid'];
		$_POST['lookupJson']  = str_replace('\"',  '"' , json_encode( html_entity_decode( $_POST['lookupJson'] ) ) );
		// 去除开始结束“
		if( substr($_POST['lookupJson'], 0, 2) == '"{' && substr($_POST['lookupJson'], -2, 2) == '}"'){
			$_POST['lookupJson'] = substr($_POST['lookupJson'], 1);
			$_POST['lookupJson'] = substr($_POST['lookupJson'], 0 , -1);
		}

		$dt=unserialize(base64_decode(base64_decode($_POST['jsondt'])));
		$data['lookupJson'] = $_POST['lookupJson'] ? serialize(json_decode( $_POST['lookupJson']  , true ) ) : '';
		foreach($_POST['data'] as $k=>$v){
			$v = explode('|',$v);
			$data['subtable'] = $v[1];
			$data['subjson'] = html_entity_decode($v[0]);
// 			//字段函数转换
// 			$subinfo = json_decode($data['subjson'],true);
// 			if(!empty($dt[$data['subtable']]['dtfuncinfo'])){
// 				$func = $dt[$data['subtable']]['dtfuncinfo'];
// 				$funcfields = array_keys($func);
// 				foreach($subinfo as $key=>$val){
// 					if(in_array($key,$funcfields)){
// 						$subinfo[$key] = $this->fieldFuncChangeForLookupSon($key,$val,$func);
// 					}
			
// 				}
// 			}
//			$data['subjson'] = json_encode($subinfo);
			$data['mainid'] = $v[2];
			$map = $data;
			unset($map['lookupJson']);
			$rs = $model->where($map)->find();
			//$rs = $model->where($data)->find();
			if(!$rs){
				$model->add($data);
			}
		}
		$model->commit();		
	}
	/**
	 * @Title: lookupson
	 * @Description: todo(lookupson页面全选复选框数据删除)
	 * @author 谢友志
	 * @date 2015-1-12 上午11:14:16
	 * @throws
	 */
	function lookupsonalldel(){
		$model = M('mis_lookup_datatable');
		$data['userid'] = $_SESSION[C('USER_AUTH_KEY')];
		$data['mainmodel'] = $_POST['model'];
		$data['mainid'] = $_POST['masid'];
		$_POST['lookupJson']  = str_replace('\"',  '"' , json_encode( html_entity_decode( $_POST['lookupJson'] ) ) );
		// 去除开始结束“
		if( substr($_POST['lookupJson'], 0, 2) == '"{' && substr($_POST['lookupJson'], -2, 2) == '}"'){
			$_POST['lookupJson'] = substr($_POST['lookupJson'], 1);
			$_POST['lookupJson'] = substr($_POST['lookupJson'], 0 , -1);
		}
		
		//$data['lookupJson'] = $_POST['lookupJson'] ? serialize(json_decode( $_POST['lookupJson']  , true ) ) : '';
		foreach($_POST['data'] as $k=>$v){
			$v = explode('|',$v);
			$data['subtable'] = $v[1];
			$data['subjson'] = html_entity_decode($v[0]);
			$data['mainid'] = $v[2];
			$model->where($data)->delete();
			file_put_contents('a.txt',$model->getLastSql());
		}
		$model->commit();
	}
	/**
	 * @Title: lookupmaindel
	 * @Description: todo(lookupson页面主数据复选框单击数据删除)   
	 * @author 谢友志 
	 * @date 2015-1-12 上午11:42:26 
	 * @throws
	 */
	function lookupmaindel(){
		$model = M('mis_lookup_datatable');
		$data['mainid'] = $_POST['masid'];
		$model->where($data)->delete();
		$model->commit();
	}
	/**
	 * @Title: lookupsons
	 * @Description: todo(继续lookupGeneral，找出其带出数据的各自对应子数据)   
	 * @author 谢友志 
	 * @date 2015-1-5 上午9:48:23 
	 * @throws
	 */
	function lookupsons(){
		$id=$_POST['id'];
		$map['masid']=array('in',$id);
		$samefield = 'xingming';
		$this->assign('samefield',$samefield);		
// 		$td = array(
// 			'mis_auto_ddskf_sub_datatable4'=>array(
// 				'title'=>'内嵌表格4',
// 				'condition'=>'',
// 				'list'=>array(
// 					'xueli'=>array('title'=>'学历','category'=>'text','length'=>4,'unitl'=>'wang'),
// 					'congyejingyan'=>array('title'=>'从业经验','category'=>'select','datasouce'=>'user','showfield'=>'name','valfield'=>'id')
// 				)
// 			),
// 			'mis_auto_ddskf_sub_datatable5'=>array(
// 				'title'=>'内嵌表格5',
// 				'condition'=>'',
// 				'list'=>array(
// 					'gongsirenshu'=>array('title'=>'公司人数','category'=>'text','length'=>4,'unitl'=>'wang'),
// 					'gongsizichan'=>array('title'=>'公司资产','category'=>'text','length'=>4,'unitl'=>'wang'),
// 					'gongsidizhi'=>array('title'=>'公司地址','category'=>'text','length'=>4,'unitl'=>'wang'),
// 					'gongsizhuangkuang'=>array('title'=>'公司状况','category'=>'select','datasouce'=>'user','showfield'=>'name','valfield'=>'id')
// 				)
// 			),
// 			'mis_auto_ddskf_sub_datatable6'=>array(
// 				'title'=>'内嵌表格6',
// 				'condition'=>'',
// 				'list'=>array(
// 					'fuqinxingming'=>array('title'=>'父亲姓名','category'=>'text','length'=>4,'unitl'=>'wang'),
// 					'muqinxingming'=>array('title'=>'母亲姓名','category'=>'text','length'=>4,'unitl'=>'wang'),
// 					'haoyourenshu'=>array('title'=>'好友人数','category'=>'text','length'=>4,'unitl'=>'wang')
// 				)
// 			),
// 		);
		$tablestitle=array();
		$list=array();
		foreach($td as $k=>$v){
			$model=M($k);			
			$condition = $v['condition'];
			if($conditions){
				$this->assign("conditions",$conditions);
				$cArr = explode(';', $conditions);//分号分隔多个参数
				foreach ($cArr as $k => $v) {
					$wArr = explode(',', $v);//逗号分隔字段、参数、修饰符
					if ($wArr[0] == "_string") { // 判断是否传的为字符串条件
						$map['_string'] = $wArr[1];
					} else {
						if ($wArr[2]) {//存在修饰符的以修饰符形式进行检索
							$map[$wArr[0]] = array($wArr[2],$wArr[1]);
						} else {//普通检索
							$map[$wArr[0]] = $wArr[1];
						}
					}
				}
			}
			$fields='';
			$fieldstitle=array();
			foreach($v['list'] as $key=>$val){
				$fields.=$fields?','.$key:$key;
				$fieldstitle[$key]=$val['title'];
			} 
			
			$list[$k]['list'] = $model->field($fields)->where($map)->select();//子表记录
							
			foreach($list[$k]['list'] as $k1=>$v1){
				$list[$k]['list'][$k1]['json_code'] = json_encode($v1);//子表记录json
				$list[$k]['list'][$k1]['json_code_key'] =$k.'_'.$k1;
			}
			file_put_contents('a.txt',json_encode($list[$k]['list']));
			$list[$k]['fieldstitle']=$fieldstitle; //字段中文名称
			$list[$k]['title']=$v['title'];	//表中文名称
		}
		exit(json_encode($list));	
	}
	/**
	 *
	 * @Title: lookupaddresult
	 * @Description: todo(添加条件)
	 * @author renling
	 * @date 2014-9-19 下午3:53:17
	 * @throws
	 */
	public function lookupaddresult(){
		//获取模型名称
		$nodename=$_POST['nodename'];
		//获取html区域标志，方便JS绑定
		$orderContainer = $_POST['order'];	// 获取目标对象标识
		if($_POST['inlinetable']){
			$this->lookupresultdetails($nodename,$_POST['inlinetable']);
		}else{
			$this->lookupresultdetails($nodename);
		}
		//获取主表名称
		$tablename=D($nodename)->getTableName();
		$info="select * from ".$tablename;
		$this->assign("info",$info);
		//获取已选中的数据信息
		$ret = $_POST['listarr'];
// 		dump(unserialize(base64_decode(base64_decode($listarr['list']))));exit;
		$listarr=unserialize(base64_decode(base64_decode($ret)));
		if($listarr === false){//有老数据是base64_encode(serialize())加密的 --xyz 15-09-16
			$listarr = unserialize(base64_decode($ret));
		}
		//$obj = new CommonModel();
		//$obj->pw_var_export($listarr);
		$MisDynamicFormManageModel=D('MisDynamicFormManage');
		$typeTree=$MisDynamicFormManageModel->getAnameSqltree();
		$this->assign("typeTree",json_encode($typeTree));
		//是否为多行
		$this->assign("multitype",$_REQUEST['multitype']);
		$this->assign("akey",$_REQUEST['akey']);
		$this->assign("listarr",$listarr);
		$this->assign('order',$orderContainer);
		$this->display("Public:lookupaddresult");
	}
	/**
	 *
	 * @Title: lookupinsertresult
	 * @Description: todo(插入条件处理成list返回)
	 * @author renling
	 * @date 2014-9-19 下午3:53:39
	 * @throws
	 */
	public function lookupinsertresult(){
		
		$date=$this->lookupAssemble();
		$this->success("添加成功",'',json_encode($date));
	}
	/**
	 *
	 * @Title: lookupAssemble
	 * @Description: todo(组装条件 返回list)
	 * @return multitype:string Ambigous <multitype:, multitype:unknown , multitype:unknown NULL >
	 * @author renling
	 * @date 2014-9-17 下午6:33:52
	 * @throws
	 */
	private function lookupAssemble(){
		/* 黎明刚 加，为了满足流程管理，在一个页面同时出现多个条件选择器*/
		$order = $_POST['order'];
		
		$roleexp=$_POST['tiprole']?$_POST['tiprole']:$_POST['roleexp'];
		$roleexptype=$_POST['roleexptype'];
		$roleexptitle=$_POST['roleexptitle'];
		$modelname=$_POST['modelname'];
		if(substr($modelname,-4)=='View'){
			$modelname = getFieldBy($modelname,'name','modelname','mis_system_dataview_mas');
		}
		//获取配置文件
		$scdmodel = D('SystemConfigDetail');
		//读取列名称数据(按照规则，应该在index方法里面)
		$detailList = $scdmodel->getDetail($modelname,'','','','rules');
		//查询该模型是否有视图
		$MisSystemDataviewMasView=D('MisSystemDataviewMasView');
		$MisSystemDataviewMasMap=array();
		$MisSystemDataviewMasMap['modelname']=$modelname;
		$MisSystemDataviewMasMap['mstatus']=1;
		$MisSystemDataviewMasList=$MisSystemDataviewMasView->where($MisSystemDataviewMasMap)->getField("field,tablename");
		$listarr=array();
		$typearr=array();
		$orspan="<span style='color:red'> 或者  </span>";
		$andspan="<span style='color:blue'> 并且  </span>";
		if($order == "processcondition_batch"){
			/* 黎明刚 加，为了满足流程管理，在一个页面同时出现多个条件选择器*/
			$orspan="或者";
			$andspan="并且";
		}
		if($_POST['avgsql']){//高级sql模式
			$showmap=$_POST['avgsql'];
		}
		foreach ($roleexp as $key=>$val){
			//查询当前条件是否是视图字段
			if($MisSystemDataviewMasList[$val]){
				//组装视图条件
				$mapval=$MisSystemDataviewMasList[$val].".".$val;
			}else{
				if($detailList[$val]['searchField']){
					$mapval=$detailList[$val]['searchField'];
				}else{
					$mapval=$val;
				}
			}
			$leftipt="";
			$rightipt="";
			$centertip="";
			$centertip=$_POST['centertip'][$val];
			$leftipt=$_POST['leftipt'][$key];
			$rightipt=$_POST['rightipt'][$key];
			if($roleexptype[$val]=='text'){
				$showval=$_POST[$val.'text'];
				if($val=="auditState"){
					$showval=getSelectByName('auditStateVal', $_POST[$val.'text']);
				}
				if($val=="operateid"){
					$showval=getSelectByName('operateidVal', $_POST[$val.'text']);
				}
				$showmap.="(".$roleexptitle[$val].' '.getSelectByName('roletextinexp', $_POST[$val.'f'])." '".$showval."')  ";
				$map.=$leftipt." ".$mapval.getSelectByName('roletextinset', $_POST[$val.'f'])."'".$_POST[$val.'text']."' ".$rightipt." ";
				$typearr[$val][]=array(
						'name'=>$val, //字段名称
						'title'=>$_POST['roleexptitle'][$val],
						'symbol'=>$_POST[$val.'f'],
						'val'=>$_POST[$val.'text'],
						'control'=>'text',
						'widget'=>'roletextinset',
						'leftipt'=>$leftipt,
						'rightipt'=>$rightipt,
						'sort'=>$key,
						'centertip'=>$centertip,
				);
			}else if($roleexptype[$val]=='select'){
				$showmap.="(".$roleexptitle[$val].' '.getSelectByName('roletextinexp', $_POST[$val.'f'])." '".implode(',',$_POST[$val.'stitle'])."')";
				$tempData = $_POST[$val.'s'];
				if($tempData){
					$ret = "'";
					if(is_array($tempData)){
						$ret .= join("','" , $tempData);
					}
					$ret .= "'";
				}
				$map.=$leftipt.' '.$mapval.' '.getSelectByName('roletextinset', $_POST[$val.'f'])."(".$ret.")".$rightipt." ";
				$typearr[$val][]=array(
						'name'=>$val, //字段名称
						'symbol'=>$_POST[$val.'f'],
						'title'=>$_POST['roleexptitle'][$val],
						'showval'=>implode(',',$_POST[$val.'stitle']),
						'val'=>$_POST[$val.'s'],
						'control'=>'select',
						'widget'=>'roletextinset',
						'sort'=>$key,
						'leftipt'=>$leftipt,
						'rightipt'=>$rightipt,
						'centertip'=>$centertip,
				);
			}else if($roleexptype[$val]=='number'){
				//带入单位存值
				$showunit=getFieldBy($_POST[$val."unitshow"], "danweidaima", "danweimingchen", "mis_system_unit");
				//map转换单位
				$mapunitsval=$_POST[$val.'snum'];
				$mapuniteval=$_POST[$val.'enum'];
				if($showunit){
					//转换为存储单位
					$mapunitsval=unitExchange($_POST[$val.'snum'],$_POST[$val.'unitchange'], $_POST[$val.'unitshow']);
					$mapuniteval=unitExchange($_POST[$val.'enum'],$_POST[$val.'unitchange'], $_POST[$val.'unitshow']);
				}
				if($_POST[$val.'enum']){
					$showmap.="(".$roleexptitle[$val].' '.getSelectByName('roleinexp', $_POST[$val.'sf'])." '".$_POST[$val.'snum'].$showunit."' {$andspan}  ".$roleexptitle[$val].' '.getSelectByName('roleinexp', $_POST[$val.'ef'])." '".$_POST[$val.'enum'].$showunit."')";
					$map.=$leftipt." "."(".$mapval.' '.getSelectByName('roleexp', $_POST[$val.'sf']).$mapunitsval." and ".$mapval.' '.getSelectByName('roleexp', $_POST[$val.'ef']).$mapuniteval.")".$rightipt." ";
				}else{
					$showmap.="(".$roleexptitle[$val].' '.getSelectByName('roleinexp', $_POST[$val.'sf'])." '".$_POST[$val.'snum'].$showunit.")";
					$map.=$leftipt." "."(".$mapval.' '.getSelectByName('roleexp', $_POST[$val.'sf'])."'".$mapunitsval."')";
				}
				$typearr[$val][]=array(
						'name'=>$val, //字段名称
						'symbols'=>$_POST[$val.'sf'],
						'symbole'=>$_POST[$val.'ef'],
						'title'=>$_POST['roleexptitle'][$val],
						'vals'=>$mapunitsval,
						'vale'=>$mapuniteval,
						'sort'=>$key,
						'control'=>'number',
						'widget'=>'roleexp',
						'leftipt'=>$leftipt,
						'rightipt'=>$rightipt,
						'centertip'=>$centertip,
				);
			}else if($roleexptype[$val]=='time'){
				if(!$_POST[$val.'etime']){
					$_POST[$val.'etime']="$"."time";
					$showtime="当前时间";
					$showetime="$"."time";
				}else{
					$showtime=$_POST[$val.'etime'];
					$showetime=$showtime;
					$_POST[$val.'etime']=strtotime($_POST[$val.'etime']);
				}
				$showmap.="(".$roleexptitle[$val].' '.getSelectByName('roleinexp', $_POST[$val.'sf'])." '".$_POST[$val.'stime']."' {$andspan} ".$roleexptitle[$val].' '.getSelectByName('roleinexp', $_POST[$val.'ef'])." '".$showtime."') ";
				$map.=$leftipt." "."(".$mapval.getSelectByName('roleexp', $_POST[$val.'sf']).strtotime($_POST[$val.'stime'])." and ".$mapval.getSelectByName('roleexp', $_POST[$val.'ef']).($_POST[$val.'etime']).")".$rightipt." ";
				$typearr[$val][]=array(
						'name'=>$val, //字段名称
						'symbols'=>$_POST[$val.'sf'],
						'symbole'=>$_POST[$val.'ef'],
						'title'=>$_POST['roleexptitle'][$val],
						'vals'=>$_POST[$val.'stime'],
						'sort'=>$key,
						'vale'=>$showetime,
						'control'=>'time',
						'widget'=>'roleexp',
						'leftipt'=>$leftipt,
						'rightipt'=>$rightipt,
						'centertip'=>$centertip,
				);
			}
			if($roleexp[$key+1]){
				if(!$leftipt && !$rightipt ){
					$map.="  and  ";
					$showmap.=$andspan;
				}else{
					if($_POST['leftipt'][$key+1]=="and"||$_POST['rightipt'][$key+1]=="and"){
						if(!$centertip[$val]){
							$showmap.=$andspan;
						}
					}else{
						if(!$centertip[$val]){
							$showmap.=$orspan;
						}
					} 
				}
			}
			if($centertip[$val]){
				$map.=" ".$centertip." ";
				$showmap.=$centertip=="or"?$orspan:$andspan;
			}
		}
		if($_POST['mapsql']){
			$typearr['mapsql'][]=array(
				'name'=>'sql',
				'sql'=>$_POST['mapsql'],
			);
			$map.=$_POST['mapsql'];
		}
		if($_POST['avgsql']){//高级sql模式
			if($map){
				$endsql=$_POST['avgsql']." and ".$map;
			}else{
				$endsql=$_POST['avgsql'];
			}
			$typearr['avgsql'][]=array(
					'name'=>'avgsql',
					'avgsql'=>$_POST['avgsql'],
			);
			$avgmap=$_POST['avgsql'];
		}
		if($typearr){
			$typearr=base64_encode(base64_encode(serialize($typearr)));
		}else{
			$typearr="";
		}
		if(!$map){
			$map="";
		}
		$listarr=array(
			'list'=>$typearr, 
			'map'=>$map,
			'endsql'=>$endsql,
			'jsshowmap'=>$showmap." ".$_POST['mapsql'],
			'showmap'=>$showmap.$_POST['mapsql'],
		); 
		return $listarr;
	}
	/**
	 *
	 * @Title: lookupdetails
	 * @Description: todo(生成条件展示页面)
	 * @author renling
	 * //扩展 本来是做的模型规则，扩展为模型本表、模型单个内嵌表、视图规则  --xyz--2015-5-26
	 * $inlinetable 内嵌表名称
	 * @date 2014-9-15 下午2:24:19
	 * @throws
	 */
	private function lookupresultdetails($nodename,$inlinetable){
		//读取配置字段
		$scdmodel = D('SystemConfigDetail');
		$detailList = array();
		
		if($inlinetable){
			//内嵌表配置字段
			$inlinearr = explode('_',$inlinetable);
			$inlinestr = '';
			foreach($inlinearr as $ik=>$iv){
				$inlinestr .= ucfirst($iv);
			}
			$detailList = require DConfig_PATH."/Models/".$nodename."/".$inlinestr.".inc.php";
		}else{
			if(substr($nodename,-4)=="View"){
				//视图配置字段
				$modelname = getFieldBy($nodename,'name','modelname','mis_system_dataview_mas');
				$detailList = require DConfig_PATH."/Models/".$modelname.'/'.$nodename.'.inc.php';
			}else{
				//正常模型配置字段
				$detailList = $scdmodel->getDetail($nodename,false,'','','rules'); //原始数据
			}
		}
		$rlistarr=array();
		$typearr=array();
		foreach($detailList as $dk=>$dv){
			if($dv['rules']==1||$inlinetable||substr($nodename,-4)=="View"){
				$name="";
				$name=$dv['name'];
				$keywords= strtolower($dv['func'][0][0]);
				if($name=="auditState"){
					$keywords ="getselectlistvalue";
					$dv['funcdata'][0][0][1]="auditStateVal";
				}
				if(isset($dv['func'])&& ($keywords == "getfieldby"||$keywords == "getselectlistvalue")){
					if($keywords == "getselectlistvalue"){
						$typearr[$dv['name']]=array(
								'modelname'=>$dv['funcdata'][0][0][3],
								'show'=>"name",
								'hidden'=>"id",
								'selectlist'=>$dv['funcdata'][0][0][1],
								'keywords'=>$keywords,
						);
					}else{
						$typearr[$dv['name']]=array(
								'modelname'=>$dv['funcdata'][0][0][3],
								'show'=>$dv['funcdata'][0][0][2],
								'hidden'=>$dv['funcdata'][0][0][1],
								'keywords'=>$keywords,
						);
					}
					$rlistarr[]=array('name'=>$name,'showname'=>$dv['showname'],'isdis'=>'select','fieldtype'=>$dv['fieldtype']);
				}else if(isset($dv['func'])&& $keywords=="transtime"){
					$rlistarr[]=array('name'=>$name,'showname'=>$dv['showname'],'isdis'=>'time','fieldtype'=>$dv['fieldtype']);
				}else if(isset($dv['func'])&& $keywords=="unitexchange"){
					$rlistarr[]=array('name'=>$name,'showname'=>$dv['showname'],'isdis'=>'number','fieldtype'=>$dv['fieldtype'],'unitchange'=>$dv['funcdata'][0][0][1],'unitshow'=>$dv['funcdata'][0][0][2],'unittype'=>$dv['funcdata'][0][0][3]);
				}else{
					$rlistarr[]=array('name'=>$name,'showname'=>$dv['showname'],'isdis'=>'text','fieldtype'=>$dv['fieldtype']);
				}
			}
		}
		$this->assign("typearr",$typearr);
		$this->assign("rlistarr",$rlistarr);
	}
	/**
	 *
	 * @Title: lookupSingle
	 * @Description: todo(条件类型查找带回)
	 * @author renling
	 * @date 2014-9-19 下午1:52:02
	 * @throws
	 */
	public function lookupSingle(){
		//获取查找带回的字段
		$_POST['dealLookupList'] = 1;//强制查找带回重构数据列表
		$name = $_POST['model'];
		$this->assign("model",$name);
		$layoutH = 96;//默认高度
		$this->assign("layoutH",$layoutH);//设置高度
		$showtitle=$_REQUEST['showtitle'];
		$this->assign('showtitle',$showtitle);
		$showname=$_REQUEST['showname'];//设置显示名称
		$this->assign("showname",$showname);
		$index = $_REQUEST['index']?intval($_REQUEST['index']):0;
		$this->assign("fieldname",$_REQUEST['name']);
		$this->assign('index',$index);
		$show=$_REQUEST['show'];
		$hidden = $_REQUEST['hidden'];
		$keywords=$_REQUEST['keywords'];
		$showtype=$_REQUEST['showtype'];
		$this->assign("showtype",$showtype);
		$this->assign("hidden",$hidden);
		$callback = $_REQUEST['callback'];
		$this->assign('callback',$callback);
		$filedback = array($show,$hidden);
		$this->assign('filedback',$filedback); // 带回字段
		$action=A("Common");
		$map = $this->_search($name);
		$conditions = $_POST['conditions'];// 检索条件
		if($conditions){
			$this->assign("conditions",$conditions);
			$cArr = explode(';', $conditions);//分号分隔多个参数
			foreach ($cArr as $k => $v) {
				$wArr = explode(',', $v);//逗号分隔字段、参数、修饰符
				if ($wArr[0] == "_string") { // 判断是否传的为字符串条件
					$map['_string'] = $wArr[1];
				} else {
					if ($wArr[2]) {//存在修饰符的以修饰符形式进行检索
						$map[$wArr[0]] = array($wArr[2],$wArr[1]);
					} else {//普通检索
						$map[$wArr[0]] = $wArr[1];
					}
				}
			}
		}
		$map['status'] = 1;
		$filterfield = "_lookupGeneralfilter";
		if($_REQUEST['filtermethod']) $filterfield = $_REQUEST['filtermethod'];
		if (method_exists($this,$filterfield)) {
			call_user_func(array(&$this,$filterfield),&$map);
		}
		$name=$name;
		if ($qx_name) {
			$name=$qx_name;
		}  
		$model=D($name);
		$voList = $model->where($map)->select(); 
		if($keywords=="getselectlistvalue"){
			$selectlistKey=$_REQUEST['selectlist'];
			//查询selectlist中的数据
			$selectList = require ROOT . '/Dynamicconf/System/selectlist.inc.php';
			$svselectlist=$selectList[$selectlistKey][$selectlistKey];
			 $voList=array();
			 foreach ($svselectlist as $vkey=>$vval){
			 	$voList[]=array(
		 			'id'=>$vkey,
		 			'name'=>$vval
			 	);
			 }
		}
		$this->assign("list",$voList);
		$this->assign ( 'totalCount', count($voList));
		$this->display('Public:lookupSingle');
	}
	
	/*
	 * 委托传参
	 * $voList查询的数据
	 * $actionname  控制器名
	 * $type 委托类型 0为老版本 1为新模式
	*/
	protected function dealLookupDelegate(&$voList,$actionname,$type){
		switch ($type){
			case 0:
				$this->dealLookupList(&$voList);
				break;
			case 1:
				$this->dealLookupLists(&$voList);
				break;
		}			
	}
	/**
	 * @Title: dealLookupList
	 * @Description: todo(查找带回专用VoList重构)
	 * @param 数据列表 $voList
	 * @author 杨东
	 * @date 2013-7-9 下午6:03:18
	 * @throws
	 */
	private function dealLookupLists(&$voList){
		//$this->assign("field",$_REQUEST['field']);
		$key=$_REQUEST['lookupchoice'];
		$path=DConfig_PATH . "/LookupObj/Details/".$key.".php";
		$lookupList = array();
		if(is_file($path)){
			$lookupList=require $path;
		}		
		
		//查找带回显示字段
		$fieldList = explode(',', $lookupList['listshowfields']);
		$showList = $fieldList;
		$this->assign("field",$fieldList);
		//查找带回字段
		$fieldbackList = explode(',', $lookupList['fields']);
        //需要函数转换的字段
		$funccheckList = explode(',', $lookupList['funccheck']);
		
		$detailArr=array();
		if($lookupList['viewname']){
			$path=DConfig_PATH . "/Models/".$lookupList['mode']."/".$lookupList['viewname'].".inc.php";
			$detailArr = require $path;
		}else{
			$scdmodel = D('SystemConfigDetail');
			$detailArr = $scdmodel->getDetail($lookupList['mode'],false);
		}
		//通过查找带回字段获取对应的list配置数组 强制设置部分不显示字段（系统自动生成字段）
		$fieldList=array_merge($fieldList,$fieldbackList);
		$outArray = array('action');
		$detailList=array();
		if($fieldList){
			foreach($fieldList as $key =>$val){
				if ($detailArr[$val]) {
					//如果存在显示数组，则显示字段；否则不显示
					if(!in_array($val,$outArray)){
						if(in_array($val,$showList)){
							$detailArr[$val]['shows'] = 1;
						}else{
							$detailArr[$val]['shows'] = 0;
						}						
						$detailList[$val]=$detailArr[$val];
					}
				}
			}
		}else{
			foreach($detailArr as $k=>$v){
				if(in_array($k,$outArray)){
					unset($detailArr[$k]);					
				}
			}
			$detailList = $detailArr;
		}
		//重新解析voLost 函数转换 和配置lookupJson
		foreach($voList as $voKey => $voVal){
        	//处理内嵌表配置
        	foreach ($fieldbackList as $fieldKey => $fieldVal){
        		$temp="";//临时存储值
        			//print_r($detailList[$v1]);
        			//第二个参数可以替换成是否需要函数
        			//dump($lookupList['funcinfo'][$fieldVal]);
        			if(in_array($fieldVal, $funccheckList)){
        			  if($lookupList['funcinfo'][$fieldVal]){ 
        			  	//函数循环层
        			  	foreach($lookupList['funcinfo'][$fieldVal]['funcname'] as $funcKey=>$funcVal){
        			  		$funcdateArr=$lookupList['funcinfo'][$fieldVal]['funcdata'][$funcKey];
        			  		if(in_array("unitExchange", $funcVal)){
        			  			//单位转换 当从数据库提取数据用于显示时，参数3设置为2；当其他文本框org绑定这个字段的时候要求其单位也必须和此页面显示单位一致		  			
        			  			$funcdateArr[0][3] = 2;
        			  			$temp= getConfigFunction($voList[$voKey][$fieldVal],$funcVal,$funcdateArr,$voList[$voKey-1]);
        			  		}else{
        			  		    $temp= getConfigFunction($voList[$voKey][$fieldVal],$funcVal,$funcdateArr,$voList[$voKey-1]);
        			  		}
        			  	}
        			  }else if(count($detailList[$fieldVal]['func'])>0){
        			  	//用配置文件list.inc的函数处理
        			  	//函数循环层
        			  	foreach($detailList[$fieldVal]['func'] as $funcKey=>$funcVal){
        			  		$funcdateArr=$detailList[$fieldVal]['funcdata'][$funcKey];
        			  		if(in_array("unitExchange", $funcVal)){
        			  			//单位转换 当从数据库提取数据用于显示时，参数3设置为2；当其他文本框org绑定这个字段的时候要求其单位也必须和此页面显示单位一致
								$funcdateArr[0][3] = 2;
        			  			$temp= getConfigFunction($voList[$voKey][$fieldVal],$funcVal,$funcdateArr,$voList[$voKey-1]);
        			  		}else{      			  			
        			  			$temp= getConfigFunction($voList[$voKey][$fieldVal],$funcVal,$funcdateArr,$voList[$voKey-1]);
        			  		}
        			  		
//         			  		echo $funcKey.$fieldVal."--";
        			  		//最后一个参数是干什么用？$voList[$k-1]
        			  		
        			  	}	
        			  }else{
        			  	//如果没有转换函数，直接获取原值
        			  	$temp=$voList[$voKey][$fieldVal];
        			  }
        			}else{
        				//如果没有转换函数，直接获取原值
        				$temp=$voList[$voKey][$fieldVal];
        			}
        			$temp = str_replace('&quot;',"\"",$temp); // by xyz 富文本中的双引号在json编码前处理 
        		$jsonArr[$fieldVal] = $temp;
        		
        	}        	
        	$voList[$voKey]['lookupJson'] = json_encode($jsonArr);
        }
        $this->assign ( 'detailList', $detailList );
	}
	//判断是否索引数组
	function is_assoc($array) {
		return (bool)count(array_filter(array_keys($array), 'is_string'));
	}
	/**
	 * @Title: dealLookupList
	 * @Description: todo(查找带回专用VoList重构)
	 * @param 数据列表 $voList
	 * @author 杨东
	 * @date 2013-7-9 下午6:03:18
	 * @throws
	 */
	private function dealLookupList(&$voList){
		$this->assign("field",$_REQUEST['field']);
		//查找带回字段
		$fieldList = explode(',', $_REQUEST['field']);
		//获取当前配置文件信息
		$name = $_REQUEST['model'];
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($name); 
		//数据循环层
		foreach ($voList as $k => $v) {
			$jsonArr = array();
			//带回字段循环层
			foreach ($fieldList as $k1 => $v1) {
				if($detailList[$v1]){
					if(count($detailList[$v1]['func'])>0 && $v1[0]==$detailList[$v1]['name']){
						//函数循环层
						foreach($detailList[$v1]['func'] as $fk=>$fv){
							$v[$v1[0]] = getConfigFunction($v[$detailList[$v1]['name']],$fv,$detailList[$v1]['funcdata'][$fk],$voList[$k-1]);
						}
					}
				}
				$jsonArr[$v1] = $v[$v1];
			} 
			$voList[$k]['lookupJson'] = json_encode($jsonArr);
		}
	}
	/**
	 * @Title: lookupQuickInsert
	 * @Description: todo(lookup专用快捷新增)
	 * @author 杨东
	 * @date 2013-7-12 上午11:47:35
	 * @throws
	 */
	public function lookupQuickInsert(){
		$model = $_POST['quickInsertModel'];
		$a = A($model);
		$a->insert();
	}
	/**
	 * @Title: lookupGetCode
	 * @Description: lookup专用快捷新增弹出获取编号
	 * @author liminggang
	 * @date 2014-9-13 下午6:05:05
	 * @throws
	 */
	public function lookupGetCode(){
		$model = D($_POST['model']);
		$table = $model->getTableName();
		$scnmodel = D('SystemConfigNumber');
		//自动生成订单编号
		$orderno = $scnmodel->GetRulesNO($table);
		echo $orderno;
	}

	/*
	 ** @Title: lookupDocumentCollate
	 * @Description: todo(归档公共调用函数 --只针对列表url归档)
	 * @author qchlian
	 * @param $realfile 1是真实文件，0是列表url
	 * @data   2013-09-24
	 */
	function lookupDocumentCollate( $realfile=0 ){
		$model = D("MisFileManager");
		$step = $_POST["step"];
		if($step==2){
			$id=$_POST["id"] ? $_POST["id"]:0;
			$t=$_POST["t"] ? $_POST["t"]:0;
			$parentid=$_POST["parentid"] ? $_POST["parentid"]:0;
			if($parentid<=0){
				$this->error("不能归档到跟目录!");
			}
			$parentinfo = $model->find($parentid);
			$data=array();
			$data["category"]=$parentinfo["category"];
			$data["remark"]=$_POST["remark"];
			$data["orderno"]=$_POST["orderno"];
			$data["position"]=$_POST["position"];
			$data["page"]=$_POST["page"];
			$data["type"]=2;
			$data["parentid"]=$parentid;
			$data["createid"]= $_SESSION[C('USER_AUTH_KEY')];
			$data["createtime"]=time();

			if($t==0){
				//$t=时是附件归档
				$model_t0=M("mis_attached_record");
				$info = $model_t0->find($id);
				import ( '@.ORG.FileUtil' );
				$FileUtil = new FileUtil();
				$socurse = UPLOAD_PATH.$info['attached'];
				if( file_exists( $socurse ) ){
					$basename = pathinfo($info['attached'] );
					$data["filepath"]= $parentinfo["filepath"]."/".$basename["basename"];
					
					//查询是否已经归档
					$fileList=$model->where(array('category'=>$data["category"],'filepath'=>$data["filepath"],'status'=>1))->select();
					if($fileList){
						$this->error ('已经归档过了');exit;
					}
					$data["uploadname"]=$basename["basename"];
					$data["name"]= $info["upname"];
					$to =UPLOAD_PATH."MisFileManager/".$data["filepath"];
					$data["type"]=0;
					$FileUtil->copyFile($socurse,$to);
				}
			}if($t==1){//$t=1知识库
				$model_t1=M("mis_knowledge_list");
				$info = $model_t1->find($id);
				$data["name"] = "文章_".$info["title"]."_".date("ymd",time());
				$data["filepath"]="<a rel='__MODULE__view' target='navTab' href='__APP__/MisKnowledgeList/view/id/".$id."' title='".$info["title"]."'>查看</a>";
			}
			$model->data($data);
			$result=$model->add();
			if($result){
				$this->success ( L('_SUCCESS_'));
			}else{
				$this->error ( L('_ERROR_') );
			}
		}
		else{
			$id=$_GET["id"] ? $_GET["id"]:0;
			$t=$_GET["t"] ? $_GET["t"]:0;
			$this->assign('id',$id);
			$this->assign('t',$t);
			$managerid=array(1);
			$sytlist=array();

			$action = A("MisFileManager");
			$arr[] = array("id"=>0,"pId"=>-1,"realid"=>0,"category"=>0,"name"=>"文档树形结构","open"=>true,"title"=>"文档树形结构");
			//系统默认的文件夹
			$map=array();
			$map['type'] = 1;//文件夹
			$map['status'] = 1; //状态为正常
			$map['issystem'] = 1;//为系统文件
			$map['category'] = array("neq",5);
			$sytlist = $model->where($map)->select();
			$sytlist=array_merge($arr,$sytlist);
			$map=array();


			//查询我的文件夹
			$map['type'] = 1;
			$map['status'] = 1;
			$map['category'] = 1;
			$map['issystem'] = 0;
			$map['createid'] = $_SESSION[C('USER_AUTH_KEY')];
			$list = $model->where($map)->select();
			if($list){
				$sytlist=array_merge($sytlist,$list);
				foreach($list as $k=>$v){
					array_push($managerid,$v["id"]);
				}
			}

			//查询单位 公文
			if( $_SESSION['a'] ){//如果是管理员不过滤
				unset($map);
				$map['type'] = 1;
				$map['status'] = 1;
				$map['category'] = array("not in",array(1,5));
				$map['issystem'] = 0;
				$list = $model->where($map)->select();
				if($list){
					$sytlist=array_merge($sytlist,$list);
				}
				$this->assign('admin',1);
			}else{//找出对应 管理员 管理的所有文件
				unset($map);
				$map['type'] = 1;
				$map['status'] = 1;
				$map['issystem'] = 0;
				//找出管理员对应文件id
				$alllist = $model->where($map)->field("id,parentid,userid")->select();
				$downfid="";
				$upid="";
				$inid=array();
				foreach($alllist as $k=>$v){
					if( $v["userid"]==$_SESSION[C('USER_AUTH_KEY')] ){
						$downfid.= $this->downAllChildren($alllist,$v["id"]);
						$upid.=$action->parentRecursion($v["id"]);
					}
				}
				if($downfid){
					$downfid = explode(",",$downfid);
					array_shift ($downfid);
					$managerid= array_merge( $managerid,$downfid);
				}
					
				if($upid){
					$upid = explode(",",$upid);
					array_shift ($upid);
				}
				if($downfid || $upid){//过滤管理员文件的id
					$inid=array_merge($upid,$downfid);
				}
				if($inid){
					$map=array();
					$map['id'] = array(' in ',$inid);
					$map['status'] = 1;
					$map['type'] = 1;
					$list = $model->where($map)->select();
					if($list){
						$sytlist=array_merge($sytlist,$list);
					}
				}
				$this->assign('admin',0);
			}

			$fidarr=array();
			foreach($sytlist as $k=>$v){
				if( !in_array($v["id"],$fidarr) ){
					$sytlist[$k]["realid"]=$v["id"];
					$sytlist[$k]["id"]=$k;
					$fidarr[]= $v["id"];
				}else{
					unset($sytlist[$k]);
				}
			}

			$typeTree = $action->doTree($sytlist);
			$typeTree = $action->getTree($sytlist,$param);
			$this->assign('documentTree',json_encode($typeTree));
			$managerid=array_unique($managerid);
			$this->assign( 'managerid',json_encode($managerid) );
			$this->display( "MisFileManager:document_collate" );
		}
	}

	/*
	 ** @Title: lookupDocumentCollateAtta
	 * @Description: todo(归档公共调用函数 --只针文件归档)
	 * @author qchlian
	 * @data   2014-03-25
	 */
	function lookupDocumentCollateAtta(){
		$this->lookupDocumentCollate(1);exit;
	}
	/**
	 * @Title: repStrWORD
	 * @Description: todo(以字符串方式替换word中的类容，仅适用于win平台)
	 * @param $docfile 模板文件
	 * @param $causedir 存放结果文件的文件夹
	 * @param $bookNamearr 数据数组
	 * @author jiangx
	 * @date 2013-10-07 上午11:17:12
	 * @throws
	 */
	public function repStrWORD($docfile, $causedir, $bookNamearr){
		if (!file_exists ($docfile)) {
			$this->error("模板文件不存在，请检查");
		}
		if ( !file_exists($causedir) ) {
			$this->createFolders($causedir); //判断目标文件夹是否存在
		}
		$filepath = pathinfo($docfile);
		$filenameGBK = $causedir.'/'.$bookNamearr['$name'].'.'.$filepath['extension'];
		$filenameUTF8=iconv("UTF-8","GBK",$filenameGBK);
		copy($docfile, $filenameUTF8);
		$office = new COM("word.application", null, CP_UTF8) or die("请安装 office word");
		if( ! $office ) {
			$this->error("无法操作office word");
		}
		//设置Word显示文档是否显示
		$office->Visible = 0;
		//打开文档
		$filenameGBK = iconv("GBK","UTF-8",$filenameUTF8);
		$office->Documents->Open($filenameGBK) or die("无法打开目标文件");
		foreach ($bookNamearr as $key => $val) {
			if (!$val) {
				$val = "";
			}
			$office->Selection->Find->Execute($key,false,true,false,false,false,true,1,false,$val,2);
		}
		$office->Documents[1]->SaveAs();
		//$office->Documents->Close();
		$office->Quit();
		header("Cache-Control: public");
		header("Content-Type: application/force-download");
		header("Content-Disposition: attachment; filename=".basename($filenameUTF8));
		readfile($filenameUTF8);
	}

	/**
	 * @Title: lookupAddSelectValue
	 * @Description: todo(打开快速新增select页面)
	 * @author 杨东
	 * @date 2013-10-14 下午3:31:40
	 * @throws
	 */
	public function lookupAddSelectValue(){
		$thismodel = $this->getActionName();
		$ConfigListModel = D('SystemConfigList');
		$lookupGeneralList = $ConfigListModel->GetValue('lookupAddSelectInclude');// 快速新增配置列表
		$include = $lookupGeneralList[$thismodel];//获取配置信息
		if($include){
			$tplName = 'LookupGeneral:'.$include['tpl'];
		} else {
			$tplName = 'LookupGeneral:'.$lookupGeneralList["default"]["tpl"];
		}
		$this->assign("tplName",$tplName);//设置模版
		$this->assign("model",$_REQUEST['model']);

		//兼容插件获取方式。屈强@nbmxkj 2010611
		if($_REQUEST['accesstype']='plugs')
		$this->display("Public:lookupPlugsAddSelectValue");
		else
		$this->display("Public:lookupAddSelectValue");
	}
	/**
	 * @Title: lookupInsertSelectValue
	 * @Description: todo(打开快速新增select页面)
	 * @author 杨东
	 * @date 2013-10-14 下午3:31:40
	 * @throws
	 */
	public function lookupInsertSelectValue(){
		$name = $_REQUEST['model'];
		$model = D ($name);
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		//保存当前数据对象
		$list=$model->add();
		if ($list!==false) {
			//兼容插件获取方式。屈强@nbmxkj 2010611
			if($_REQUEST['accesstype']='plugs'){
				$pk = $model->getPk();
				$data = $model->where("{$pk}={$list}")->select();
				$this->success ( L('_SUCCESS_') ,'',$data);
			}else{
				$this->success ( L('_SUCCESS_') ,'',$list);
			}

			exit;
		} else {
			$this->error ( L('_ERROR_') );
		}
	}
	/**
	 * 动态表单快捷添加-显示
	 **/
	public function quickAddView(){
		$this->assign("model",$_REQUEST['model']);
		$arr = explode('@',$_GET['parame']);
		foreach ($arr as $k=>$v){
			$arr[$k]=explode('|',$v);
		}
		$this->assign('fields',$arr);
		$this->display("Public:quickAddView");
	}
	/**
	 * 动态表单快捷添加-数据操作
	 **/
	public function quickAddInsert(){
		$name = $_REQUEST['model'];
		$formid = $_POST['formid'];
		if($name == 'MisDynamicFormField' && $formid){
			$obj = M();
			$obj->query("UPDATE mis_dynamic_form_field SET `options` = CONCAT(`options` , ';{$_POST['key']}:{$_POST['value']}') WHERE id = {$formid}");
			$data[]=array('key'=>$_POST['key'],'value'=>$_POST['value']);
			$this->success ( L('_SUCCESS_') ,'',$data);
		}else{
			$model = D ($name);
			if (false === $model->create ()) {
				$this->error ( $model->getError () );
			}
			//保存当前数据对象
			$list=$model->add();
			if ($list!==false) {
				$pk = $model->getPk();
				$data = $model->where("{$pk}={$list}")->select();
				$this->success ( L('_SUCCESS_') ,'',$data);
			} else {
				$this->error ( L('_ERROR_') );
			}
		}
	}

	/**
	 * @Title: onchangeType
	 * @Description: todo(验证单头类型是否可以修改)
	 * @author liminggang
	 * @date 2013-10-17 上午9:49:33
	 * @throws
	 */
	public function onchangeType(){
		$masmodel = $_POST['model'];
		$type = $_POST['val'];
		$field = $_POST['field']; 
		$submodel = $_POST['submodel'];
		$masid = $_POST['masid'];
		$mapkey = $_POST['mapkey'] ?$_POST['mapkey']:"masid";
		if($_POST['judgeSub']){
			$count = D($submodel)->where($mapkey.' = '.$masid.' and status = 1')->count('id');
			if ($count <= 0) {
				$model = D($masmodel);
				$model->startTrans();
				$model->where('id = '.$masid)->setField($field,$type);
				$model->commit();
			}
			echo $count;
		} else {
			$model = D($masmodel);
			$model->startTrans();
			$model->where('id = '.$masid)->setField($field,$type);
			$model->commit();
			echo 0;
		}
	}
	/**
	 * @Title: getAgeByToCarId
	 * @Description: todo(根据身份证号码计算年龄或者出生年月日)
	 * @param 身份证 $idcard
	 * @param 是否返回出生年月 $isYear  //默认返回出生年月日，如果传入false则返回年龄
	 * @return string|Ambigous <number, unknown>
	 * @author liminggang
	 * @date 2013-11-7 下午2:52:27
	 * @throws
	 */
	public function getAgeByToCarId($idcard,$isYear=true,$isVail=true){
		if($this->idcard_checksum18($idcard)){
			//获得身份证的出生年月日
			$year = substr($idcard,6, 4);
			$month = substr($idcard,10, 2);
			$day = intval(substr($idcard,12, 2));
			$birthday=$year."-".$month."-".$day;
			$date=strtotime($birthday);
			if($isYear){
				return $birthday;
			}else{
				//获得出生年月日的时间戳
				$today=strtotime('today');
				//获得今日的时间戳
				$diff=floor(($today-$date)/86400/365);
				//得到两个日期相差的大体年数
				//strtotime加上这个年数后得到那日的时间戳后与今日的时间戳相比
				$age=strtotime(substr($id,6,8).' +'.$diff.'years')>$today?($diff):$diff-1;
				return $age;
			}

		}else{
			if($isVail!=false){
				$this->error("你输入的身份证有误！");
			}
		}
	}
	/**
	 * @Title: validation_filter_id_card
	 * @Description: todo(判断身份证位数，中国国际标准，15和18位，)
	 * @param unknown_type $id_card
	 * @return boolean
	 * @author liminggang
	 * @date 2013-11-7 下午2:29:37
	 * @throws
	 */
	function validation_filter_id_card($id_card)
	{
		if(strlen($id_card) == 18)
		{
			return idcard_checksum18($id_card);
		}
		elseif((strlen($id_card) == 15))
		{
			$id_card = idcard_15to18($id_card);
			return idcard_checksum18($id_card);
		}
		else
		{
			return false;
		}
	}
	/**
	 * @Title: idcard_verify_number
	 * @Description: todo(计算身份证校验码，根据国家标准GB 11643-1999)
	 * @param 身份证号 $idcard_base
	 * @return boolean|string
	 * @author liminggang
	 * @date 2013-11-7 下午2:31:17
	 * @throws
	 */
	function idcard_verify_number($idcard_base){
		if(strlen($idcard_base) != 17)
		{
			return false;
		}
		//加权因子
		$factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
		//校验码对应值
		$verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
		$checksum = 0;
		for ($i = 0; $i < strlen($idcard_base); $i++)
		{
			$checksum += substr($idcard_base, $i, 1) * $factor[$i];
		}
		$mod = $checksum % 11;
		$verify_number = $verify_number_list[$mod];
		return $verify_number;
	}
	/**
		* @Title: idcard_15to18
		* @Description: todo(将15位身份证升级到18位)
		* @param 身份证 $idcard
		* @return boolean|string
		* @author liminggang
		* @date 2013-11-7 下午2:31:54
		* @throws
		*/
	function idcard_15to18($idcard){
		if (strlen($idcard) != 15){
			return false;
		}else{
			// 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
			if (array_search(substr($idcard, 12, 3), array('996', '997', '998', '999')) !== false){
				$idcard = substr($idcard, 0, 6) . '18'. substr($idcard, 6, 9);
			}else{
				$idcard = substr($idcard, 0, 6) . '19'. substr($idcard, 6, 9);
			}
		}
		$idcard = $idcard . $this->idcard_verify_number($idcard);
		return $idcard;
	}
	/**
	 * @Title: idcard_checksum18
	 * @Description: todo(18位身份证校验码有效性检查)
	 * @param 身份证 $idcard
	 * @return boolean
	 * @author liminggang
	 * @date 2013-11-7 下午2:32:21
	 * @throws
	 */
	function idcard_checksum18($idcard){
		if (strlen($idcard) != 18){
			return false;
		}
		$idcard_base = substr($idcard, 0, 17);
		if ($this->idcard_verify_number($idcard_base) != strtoupper(substr($idcard, 17, 1))){
			return false;
		}else{
			return true;
		}
	}
	/**
	 * @Title: getImportFileData
	 * @Description: todo(获取文件数据,只识别xls文件)
	 * @author jiangx
	 * @date 2013-10-15
	 * @throws
	 */
	public function getImportFileData($file_path){
		//判断文件是否存在
		if (!file_exists($file_path)) {
			$this->error('文件上传丢失，请再次上传');
		}
		//判断文件后缀是否为xls
		$file_info = pathinfo($file_path);
		if ($file_info['extension'] != 'xls') {
			$this->error('文件格式错误，请上传xls格式文件');
		}
		import('@.ORG.PHPExcel.excel_reader2', '', $ext='.php');
		//创建对象
		$data = new Spreadsheet_Excel_Reader();
		//设置文本输出编码
		$data->setOutputEncoding('UTF-8');
		//读取Excel文件
		$data->read($file_path);
		$sheetData = $data->sheets;
		if (!$sheetData[0]['cells']) {
			$this->error("文件没有数据，请核查");
			exit;
		}
		return $sheetData;
	}
	/**
	 * @Title: numberToLetter
	 * @Description: todo(字符数组,)
	 * @$count 长度
	 * @$isnumber 格式 true : '1' => A ;  false : 'A' => 1
	 * @author jiangx
	 * @date 2013-10-15
	 * @throws
	 */
	public function numberToLetter($count, $isnumber = false){
		$resultArr = array();
		$letterArr = range('A','Z');
		$index = floor($count / 26);
		$num = 26;
		for ($i =0; $i<= $index; $i++) {
			if ($i == $index) {
				$num = $count % 26;
			}
			for ($j = 1; $j <= $num; $j++) {
				if ($isnumber) {
					$resultArr[($i * 26) + $j] = ($letterArr[$i-1] ? $letterArr[$i-1] : '') .$letterArr[$j-1];
				} else {
					$resultArr[($letterArr[$i-1] ? $letterArr[$i-1] : '') .$letterArr[$j-1]] = ($i * 26) + $j;
				}
			}
		}
		return $resultArr;
	}
	function tmlbbs(){
		$this->display ("Public:tmlbbs");
	}

	/**
	 * @Title: playSWF
	 * @Description: todo(文件在线查看方法)
	 * @author liminggang
	 * @date 2014-8-12 下午5:29:50
	 * @throws
	 */
	function playSWF(){
		$file_type = "file";
		$socuse = str_replace("\\", "/", base64_decode($_REQUEST['name']));
		$uid = $_REQUEST['uid'];
		$file_path = $resource_path = str_replace("\\","/", UPLOAD_PATH.$socuse);
		
		$this->assign("resource_path",$resource_path);
		if (!file_exists($file_path)) {
			$this->display("Public:playswfno");
			exit;
		}
		$info = pathinfo($file_path);
		$filesArr = C ('TRANSFORM_SWF');
		$filesArr[] = 'pdf';
		$photoArr = C ('IMG_file');
		$file_extension_lower = strtolower($info['extension']);
		if($file_extension_lower=="docx" || $file_extension_lower=="doc"){
			$action = $this->getActionName();
// 			$ip = gethostbyname($_SERVER['SERVER_NAME']);
			$ip = C("DB_HOST_WORD");//"192.168.0.238";
			require_once("http://{$ip}:8088/JavaBridge/java/Java.inc");//此行必须
			$PageOfficeCtrl = new Java("com.zhuozhengsoft.pageoffice.PageOfficeCtrlPHP");//此行必须
			$PageOfficeCtrl->setServerPage("http://{$ip}:8088/JavaBridge/poserver.zz");//此行必须，设置服务器页面
			$url = __ROOT__."/Admin/index.php/$action/saveOnlineEditWord/name/".$_REQUEST['name'];
			java_set_file_encoding("utf8");//设置中文编码，若涉及到中文必须设置中文编码
// 			//添加自定义按钮
// 			$PageOfficeCtrl->addCustomToolButton("保存","Save",1);
// 			//设置保存页面
			$PageOfficeCtrl->setSaveFilePage($url);
			$PageOfficeCtrl->addCustomToolButton("保存", "Save", 1);
			$PageOfficeCtrl->addCustomToolButton("显示/隐藏修改记录", "Show_HidRevisions", 7);
// 			$PageOfficeCtrl->setAllowCopy(false);//禁止拷贝
			//$PageOfficeCtrl->setMenubar(false);//隐藏菜单栏
// 			$PageOfficeCtrl->setOfficeToolbars(false);//隐藏Office工具条
// 			$PageOfficeCtrl->setCustomToolbar(false);//隐藏自定义工具栏

			//打开excel文档
			$PageOfficeCtrl->UserAgent = $_SERVER['HTTP_USER_AGENT'];//若使用谷歌浏览器此行代码必须有，其他浏览器此行代码可不加
			$OpenMode = new Java("com.zhuozhengsoft.pageoffice.OpenModeType");
			
			$socuse = str_replace("\\", "/", base64_decode($_REQUEST['name']));
			$file_path = str_replace("\\","/", UPLOAD_PATH.$socuse);
			$file_path = preg_replace("/^([\s\S]+)\/Public/", "/Public/", $file_path);
// 			$PageOfficeCtrl->webOpen($file_path, $OpenMode->docNormalEdit, "张三");//此行必须
			//获取用户名称
			$loginUserName = getFieldBy($uid, "id", "name", "user");
			$PageOfficeCtrl->webOpen($file_path, $OpenMode->docRevisionOnly,$loginUserName);//此行必须
			$this->assign('PageOfficeCtrl',$PageOfficeCtrl->getDocumentView("PageOfficeCtrl1"));
			$this->display("Public:showEditWord");
		}else{
			if (in_array($file_extension_lower, $filesArr)) {
				$file_path = preg_replace("/^([\s\S]+)\/Public/", "../Public", $info["dirname"]);
				if ('pdf' == $file_extension_lower) {
					$file_path .= '/' . $info['filename'] . '.pdf';
					$file_path = @iconv('UTF-8', 'GBK', $file_path);
					import ( '@.ORG.OfficeOnline.OfficeOnlineView' );
					$OfficeOnlineView= new OfficeOnlineView();
					if(!file_exists(str_replace("pdf", "swf", $file_path))){
						$OfficeOnlineView->pdf2swf($resource_path, str_replace("MisFileManagerPlaySWF", "MisFileManager", $info['dirname']),1);
					}
					$result = 0;
				}else{
					$result = 0;
					$file_path .= '/pdf/' . $info['filename'] . '.pdf';
					$file_path = @iconv('UTF-8', 'GBK', $file_path);
					if (!file_exists($file_path)) {
						$result = 1;
						//调用文件IO操作，生成文件
						$HttpSoctetIOAction = A('Http');
						$HttpSoctetIOAction->createPDF(str_replace("MisFileManager", "MisFileManagerPlaySWF", $info['dirname']),$resource_path);
						$file_path = str_replace("MisFileManager", "MisFileManagerPlaySWF", $file_path);
						$file_path = str_replace("pdf", "swf", $file_path);
					}
				}
				$this->assign('result',$result);
				$this->assign('file_name', $_REQUEST['filename']);
			}
			if (in_array($file_extension_lower, $photoArr)) {
				$file_type = "photo";
			}
			$file_path = preg_replace("/^([\s\S]+)\/Public/", "__PUBLIC__", $file_path);
			$this->assign("file_type", $file_type);
			$this->assign('file_path', str_replace("pdf", "swf", $file_path));
			$this->display("Public:playswf");
		}
	}
	/**
	 * @Title: checkFileExiside
	 * @Description: todo(文件在线查看，ajax实时请求，判断是否文件已经生成。)
	 * @author liminggang
	 * @date 2014-8-12 下午5:30:45
	 * @throws
	 */
	function checkFileExiside(){
		$filePath = $_REQUEST['path'];
		$info=pathinfo($filePath);
		$lockFile = $info['dirname'].'/'.$info['filename'].'.lock';
		$pdfile = $info['dirname'].'/pdf/'.$info['filename'].'.pdf';
		if(file_exists($lockFile) || !file_exists($pdfile)){
			// 生成中
			echo 1;
		}else{
			echo 0;
		}
	}
	/**
	 * @Title: lookupRefreshOnLine
	 * @Description: todo(刷新在线人数)
	 * @author jiangx
	 * @param $type 请求类型 1 ajax请求
	 * @date 2014-01-02
	 */
	public function lookupRefreshOnLine($type = 0){
		if (isset($_REQUEST['type'])) {
			$type = $_REQUEST['type'];
		}
		$mUserOnline = D("UserOnline");
		$data['onlinesum'] = $mUserOnline->getOnLineCount();
		if ($type == 1) {
			echo json_encode($data);
			exit;
		}
		return $data['onlinesum'];
	}
	/**
	 * @Title: insertWorkMonitoring
	 * @Description: todo(插入工作并设置以前的工作为完成状态)
	 * @author 杨东
	 * @date 2014-2-24 下午2:09:54
	 * @throws
	 */
	public function insertWorkMonitoring(){
		$mMisWorkMonitoring = D("MisWorkMonitoring");
		$id = $_POST['id'];// 替换ID
		unset($_POST['id']);
		// 修改以前的数据  修改为处理完成
		$map['tablename'] = $_POST['tablename'];
		$map['tableid'] = $_POST['tableid'];
		$map['dostatus'] = 0;
		$data['dostatus'] = 1;
		$data['updateid'] = $_SESSION[C('USER_AUTH_KEY')];
		$data['userid'] = $_SESSION[C('USER_AUTH_KEY')];
		$data['doinfo'] = $_POST['doinfo'];
		$data['auditState'] = $_POST['auditState'];
		$data['dotime'] = time();
		$data['updatetime'] = time();
		$mMisWorkMonitoring->where($map)->save($data);
		// 判断 如果是打回或者是 审核完成 不进行插入新工作
		if($_POST['ostatus'] != -1 && $_POST['ostatus'] != 0){
			$doinfo = $_POST['doinfo'];
			unset($_POST['doinfo']);
			//$_POST['title'] = "单据号为：".$_POST['orderno'];// 设置标题
			// 插入一条新的工作任务
			if(strpos($_POST['ostatus'], ',')){
				// 判断是否为逗号分隔，如果是则取第一个值
				$aCurNodeIdArr = explode(',', $_POST['ostatus']);
				$_POST['curNodeId'] = $aCurNodeIdArr[0];
			} else {
				// 如果不是则取全部
				$_POST['curNodeId'] = $_POST['ostatus'];
			}
			$_POST['createid'] = $_POST['createid']?$_POST['createid']:$_SESSION[C('USER_AUTH_KEY')];
			if (false === $mMisWorkMonitoring->create ()) {
				$this->error ( $mMisWorkMonitoring->getError () );
			}
			// 通过POST 插入一条心的工作任务
			$list = $mMisWorkMonitoring->add ();
			$_POST['doinfo'] = $doinfo;
		}
		$_POST['id'] = $id;
	}

	/**
	 * @Title: insertWorkExecuting
	 * @Description: todo(插入已审批，待执行的工作安排)
	 * @author 杨希
	 * @date 2014-4-21 下午4:40:12
	 * @throws
	 */
	public function insertWorkExecuting(){
		$MWEModel = D("MisWorkExecuting");
		// 修改以前的数据  修改为处理完成
		$_POST['ptmptid']=$_POST['pid'];
		$map['tablename'] = $_POST['tablename'];
		$map['tableid'] = $_POST['tableid'];
		$result=$MWEModel->where($map)->find();
		//拼装数据
		$data['tablename']= $_POST['tablename'];
		$data['tableid']= $_POST['tableid'];
		$data['orderno']= $_POST['orderno'];
		$data['ptmptid']= $_POST['ptmptid'];
		$data['informpersonid']= $_POST['informpersonid'];
		$data['executorid']= $_POST['executorid'];
		// 判断 如果是打回或者是 审核完成 不进行插入新工作
		if($result){
			$data['updateid'] = $_SESSION[C('USER_AUTH_KEY')];
			$data['updatetime'] = time();
			//更新一条记录
			$result=$MWEModel->where($map)->save($data);
			if($result===false){
				$this->error('更新执行事项出错啦！请联系管理员！');
			}
		}else{
			$workdata=$_POST;
			unset($workdata['id']);
			// 通过POST 插入一条新的工作任务
			if (false === $MWEModel->create ($workdata)) {
				$this->error ( $MWEModel->getError () );
			}
			$result = $MWEModel-> add();
			if($result===false){
				$this->error('新增执行事项出错啦！请联系管理员！');
			}
		}

	}
	/**
	 * @Title: lookupgetdate
	 * @Description: todo(AJAX请求计算天数)
	 * @author renling
	 * @date 2013-6-14 上午11:25:32
	 * @throws
	 */
	function lookupgetdate(){
		$s = strtotime($_REQUEST["sdate"]);
		$e = strtotime($_REQUEST["edate"]);
		if($e<=$s || !$s || !$e){
			echo "";
			exit;
		}
		import ( "@.ORG.Date" );
		$date=new Date(intval( $s ));
		echo  $date->dateDiff($e,2,false);
		exit;
	}
	/**
	 * @Title: deleteWorkMonitoring
	 * @Description: todo(当发生撤回时，删除所有关于当前工作任务记录)
	 * @author 杨东
	 * @date 2014-2-24 下午2:13:50
	 * @throws
	 */
	public function deleteWorkMonitoring(){
		$mMisWorkMonitoring = D("MisWorkMonitoring");
		$map['tablename'] = $this->getActionName();
		$map['tableid'] = $_REQUEST['id'];
		// 删除记录
		$mMisWorkMonitoring->where($map)->delete();
	}
	/**
	 * @Title: lookupSelectUser
	 * @Description: todo(通用化用户选择组件)
	 * @author 杨东
	 * @date 2014-2-28 上午11:40:22
	 * @throws
	 */
	public function lookupSelectUser(){
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
// 					unset($list[$uk]);
				}
			}
		}
		$num = count($list);
		$this->assign("num",$num);// 总人数
		$returnarr = array();
		$dptmodel = D("MisSystemDepartment");//部门表
		$deptlist = $dptmodel->where("status=1")->order("id asc")->field('id,name,parentid')->select();
		//部门树形
		$returnarr=$dptmodel->getDeptZtree('','','','','',1);
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
		if($_POST['sysUser']){
			//系统级选择用户
			foreach($rolegroupList as $ke=>$va){
				$newRole=array();
				$newRole['id'] = $va['id'];
				$newRole['pId'] = 0;
				$newRole['title'] = $va['name']; //光标提示信息
				$newRole['name'] = missubstr($va['name'],18,true); //结点名字，太多会被截取,针对于现在的ZTREE，宽度不能超过18个字符。
				$newRole['open'] = false;
				$rolegroup[]=$newRole;
			}
			$this->assign("sysUser",$_POST['sysUser']);
		}else{
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
		}
// 		print_r($rolegroup);
		$this->assign('rolegrouptree',json_encode($rolegroup));
		//公司的树
		$companytree=$dptmodel->getDeptZtree('','','','','',2);
		$this->assign('sysCompanytree',$companytree);
// 		$this->assign('usertree',$returnarr);
		
		$this->assign('data',$_POST["data"]);
		$this->assign('groupData',$_POST["groupData"]);
		$this->assign('ulid',$_POST["ulid"]);
		if($_POST["ulid"]){
			if($_POST['sysUser']){
				$this->display("SelectUser:sysmultiple");// 选多个用户-系统级
			}else{
				$this->display("SelectUser:multiple");// 选多个用户
			}
		} else {
			$this->display("SelectUser:singleUser");// 选单个用户
		}
	}
	
	/**
	 * @Title: lookupSelectXyzPerson
	 * @Description: todo(选人)
	 * @author xyz
	 * @date 2014-3-7 下午3:42:37
	 * @throws
	 * 		缺失功能：查询用户指定字段数据。@屈强
	 */
	public function lookupSelectXyzPerson(){
		$map = array();
		$map['user.id'] = array('NEQ',1);
		$map['mis_hr_personnel_person_info.working'] = 1;// 在职状态判断
		if (method_exists ( $this, '_filterLookupSelectPerson' )) {
			$this->_filterLookupSelectPerson ( $map );
		}
		/*$model = D("MisHrPersonnelAppraisalInfoView");
		$list = $model->field("id,name,deptid,userid,pinyin,dutylevelname,deptname")->where($map)->select();

		$this->assign("num",$num);// 总人数
		$returnarr = array();
		$dptmodel = D("MisSystemDepartment");//部门表
		$deptlist = $dptmodel->where("status=1")->order("id asc")->field('id,name,parentid')->select();
		
		foreach ($deptlist as $k=>$v){
			foreach($list as $k1=>$v1){
				if($v['id']==$v1['deptid']){
					$deptlist[$k]['uname'][]=$v1['name'];
					$deptlist[$k]['dutylevelname'][]=$v1['dutylevelname'];
					$deptlist[$k]['userid'][]=$v1['userid'];
				}
			}
		}
		$newlist = $this->getArrTree($deptlist,0, 'id', 'parentid', 'child');
		*/
		// 获取用户需要查询的字段
		$userfindfiel='';
		$userfindfieljson='';
		foreach(explode(';'	, $_POST["data"]) as $k=>$v){
			$v = explode(',', $v);
			$userfindfieljson[]=$v;
			$userfindfiel[]=next($v);
		}
		$this->assign('userfindfiel',$userfindfiel); // 人员信息
		$this->assign('userfindfieljson',json_encode($userfindfieljson)); // 人员信息
		// 系统默认查询的字段 
		$defaultField = array('id','name','deptid','userid','pinyin','dutylevelname','deptname');
		
		$findfield = array_merge($defaultField , $userfindfiel);
		$mode = D("MisHrPersonnelAppraisalInfoView");
		$userdata = $mode->field(join(',', $findfield))->where($map)->select();
		
		$dptmodel = D("MisSystemDepartment");//部门表
		$deptlist = $dptmodel->where("status=1")->order("id asc")->field('id,name,parentid')->select();
		$this->assign('departmentnarmol',$deptlist);// 部门树形数据
		
		$deptlist = listToTree($deptlist , 'id' , 'parentid');
		$tempData=array();
		foreach ($userdata as $k=>$v){
			$tempData[$v['deptid']][]=$v;
		}
		$this->assign('personnel',$tempData); // 人员信息
		$this->assign('department',$deptlist);// 部门树形数据
		
		
		$this->assign('data',$_POST["data"]);
		$this->assign('ulid',$_POST["ulid"]);

		if($_POST["ulid"]){
			$this->display("SelectUser:multiple");// 选多个用户
		} else {
			$this->display("SelectUser:singleUser");// 选单个用户
		}
	}

	/**
	 * @Title: lookupUserSetField
	 * @Description: todo(打开设置字段页面)
	 * @author 杨东
	 * @date 2014-3-3 下午2:05:56
	 * @throws
	 */
	public function lookupUserSetField(){
		$CurModel = $_REQUEST['model'];// 获取当前模型
		$this->assign('CurModel',$CurModel);
		$mMisRuntimeData = D("MisRuntimeData");// 缓存类初始化
		$model = D('SystemConfigDetail');// 动态配置类初始化

		//恢复默认配置(读取公用配置)
		if($_SESSION['a']||$_REQUEST['ttype']){
			// 系统管理员 配置 获取系统配置文件
			if($_REQUEST['issub']){
				$list = $model->getSubDetail($CurModel,false);
			} else {
				$list = $model->getDetail($CurModel,false);
			}
			//读取公共模块设置
			$PublicListincModel=M('mis_system_public_listinc');
			$PublicListincMap['modelname']=$CurModel;
			$PublicListincList=$PublicListincModel->where($PublicListincMap)->select();
			foreach ($PublicListincList as $pk=>$pv){
				$PublicListincListInfo[$pv['fieldname']]=$pv;
			}
			if(!empty($PublicListincListInfo)){
				//存在则替换list里面的shows、whdths、sortnum
				foreach ($list as $k=>$v){
					$isexisit=array_key_exists($v['name'], $PublicListincListInfo);
					if($isexisit){
						$list[$k]['shows']=$PublicListincListInfo[$v['name']]['shows'];
						$list[$k]['widths']=$PublicListincListInfo[$v['name']]['widths']?$PublicListincListInfo[$v['name']]['widths']:null;
						$list[$k]['sortnum']=$PublicListincListInfo[$v['name']]['sortnum'];
					}
				}
			}
			
		} else {
			// 普通用户 配置 获取自己的缓存配置文件
			if($_REQUEST['issub']){
				$list = $mMisRuntimeData->getRuntimeCache($CurModel,'subdetailList');
			} else {
				$list = $mMisRuntimeData->getRuntimeCache($CurModel,'detailList');
			}
			if(empty($list)){
				// 如果没有自己的缓存文件则获取系统配置文件
				if($_REQUEST['issub']){
					$list = $model->getSubDetail($CurModel,false);
				} else {
					$list = $model->getDetail($CurModel,false);
				}
				$privateListincModel=M('mis_system_private_listinc');
				//查询数据库是否有个人设置
				$privateListincMap['modelname']=$CurModel;
				$privateListincMap['userid']=$_SESSION[C('USER_AUTH_KEY')];
				$privateListincListInfo=$privateListincModel->where($privateListincMap)->select();
				foreach ($privateListincListInfo as $pk=>$pv){
					$privateListincListInfo[$pv['fieldname']]=$pv;
				}
				if(!empty($privateListincListInfo)){
					foreach ($list as $k=>$v){
						$isexisit=array_key_exists($v['name'], $privateListincListInfo);
						if($isexisit){
							$list[$k]['shows']=$privateListincListInfo[$v['name']]['shows'];
							$list[$k]['widths']=$privateListincListInfo[$v['name']]['widths']?$privateListincListInfo[$v['name']]['widths']:null;
							$list[$k]['sortnum']=$privateListincListInfo[$v['name']]['sortnum'];
						}
					}
				}else{
					//读取公共模块设置
					$PublicListincModel=M('mis_system_public_listinc');
					$PublicListincMap['modelname']=$CurModel;
					$PublicListincList=$PublicListincModel->where($PublicListincMap)->select();
					foreach ($PublicListincList as $pk=>$pv){
						$PublicListincListInfo[$pv['fieldname']]=$pv;
					}
					if(!empty($PublicListincListInfo)){
						//存在则替换list里面的shows、whdths、sortnum
						foreach ($list as $k=>$v){
							$isexisit=array_key_exists($v['name'], $PublicListincListInfo);
							if($isexisit){
								$list[$k]['shows']=$PublicListincListInfo[$v['name']]['shows'];
								$list[$k]['widths']=$PublicListincListInfo[$v['name']]['widths']?$PublicListincListInfo[$v['name']]['widths']:null;
								$list[$k]['sortnum']=$PublicListincListInfo[$v['name']]['sortnum'];
							}
						}
					}
				} 
			}
		}
		//print_r($list);
		$this->assign("issub",$_REQUEST['issub']);
		$this->assign('list',$list);
		$this->display("Public:lookupUserSetField");
	}
	/**
	 * @Title: lookupUserFieldUpdate
	 * @Description: todo(保存用户配置字段)
	 * @author 杨东
	 * @date 2014-3-3 下午2:28:27
	 * @throws
	 */
	public function lookupUserFieldUpdate(){
		$CurModel =  $_REQUEST['model'];
		//数据升降序主次
		$sortsorder = array();
		if($_POST['sorts']){
			$i=1;
			foreach($_POST['sorts'] as $key=>$val){
				$sortsorder[$key]=$i;
				$i++;
			}
			unset($i);
		}
		$sortstype = $_REQUEST['sortstype'];		
		if($_SESSION['a']){
			//管理员设置存入公共设置
			$publistListincModel=M('mis_system_public_listinc');
			$modelName = $CurModel;
			$shows=$_REQUEST['shows'];
			$showname=$_REQUEST['showname'];
			$widths=$_REQUEST['widths'];
			$sortnum=$_REQUEST['sortnum'];
			//查询是否存在数据中
			$publistMap=array();
			$publistMap['modelname']=$modelName;
			$publistListincList=$publistListincModel->where($publistMap)->select();
			foreach ($publistListincList as $pk=>$pv){
				$publistListincList[$pv['fieldname']]=$pv;
			}
			foreach ($showname as $shownamek=>$shownamev){
				$fieldname[]=$shownamek;
				//判断是否显示
				$fieldexist=array_key_exists($shownamek, $shows);
				if($publistListincList[$shownamek]==null){
						//不存在添加
						$publistData=array();
						$publistData['modelname']=$modelName;
						$publistData['fieldname']=$shownamek;
						$publistData['widths']=$widths[$shownamek];
						$publistData['sortnum']=$sortnum[$shownamek];	
						$publistData['shows']=$fieldexist?1:0;
						$publistData['sortsorder']=$sortsorder[$shownamek]?$sortsorder[$shownamek]:0;
						$publistData['sortstype']=$sortstype[$shownamek]?$sortstype[$shownamek]:'asc';
						$publistList=$publistListincModel->add($publistData);
						if($publistList==false){
							$this->error ( L('_ERROR_') );
						}
				}else{
					//存在修改
					$publistincMap=array();
					$publistData=array();
					$publistincMap['modelname']=$modelName;
					$publistincMap['fieldname']=$shownamek;
					$publistData['widths']=$widths[$shownamek];
					$publistData['sortnum']=$sortnum[$shownamek];
					$publistData['shows']=$fieldexist?1:0;
					$publistData['sortsorder']=$sortsorder[$shownamek]?$sortsorder[$shownamek]:0;
					$publistData['sortstype']=$sortstype[$shownamek]?$sortstype[$shownamek]:'asc';	
					$publistList=$publistListincModel->where($publistincMap)->save($publistData);
					if($publistList==false){
						$this->error ( L('_ERROR_') );
					}
				}
			}
			//删除不存在的字段
			$publistDeleteMap['modelname']=$modelName;
			$publistDeleteMap['fieldname']=array('not in',$fieldname);
			$publistincList=$publistListincModel->where($publistDeleteMap)->delete();
			if($publistincList==false){
				$this->error ( L('_ERROR_') );
			}
			
		}else{
			//个人设置存入数据库
			$privateListincModel=M('mis_system_private_listinc');
			$shows=$_REQUEST['shows'];
			$showname=$_REQUEST['showname'];
			$widths=$_REQUEST['widths'];
			$sortnum=$_REQUEST['sortnum'];
			//查询是否存在数据中
			$privateMap=array();
			$privateMap['modelname']=$CurModel;
			$privateMap['userid']=$_SESSION [C ( 'USER_AUTH_KEY' )];
			$privateListincList=$privateListincModel->where($privateMap)->select();
			foreach ($privateListincList as $prik=>$prival){
				$privateListincList[$prival['fieldname']]=$prival;
			}
			foreach ($showname as $shownamek=>$shownamev){
				$fieldname[]=$shownamek;
				//判段是否显示
				$fieldexist=array_key_exists($shownamek, $shows);
				if(empty($privateListincList[$shownamek])){
					//不存在添加
					$privateData=array();
					$privateData['modelname']=$CurModel;
					$privateData['fieldname']=$shownamek;
					$privateData['widths']=$widths[$shownamek];
					$privateData['sortnum']=$sortnum[$shownamek];
					$privateData['shows']=$fieldexist?1:0;
					$privateData['sortsorder']=$sortsorder[$shownamek]?$sortsorder[$shownamek]:0;
					$privateData['sortstype']=$sortstype[$shownamek]?$sortstype[$shownamek]:'asc';
					$privateData['userid']=$_SESSION [C ( 'USER_AUTH_KEY' )];
					$privateList=$privateListincModel->add($privateData);
					if($privateList==false){
						$this->error ( L('_ERROR_') );
					}
				}else{
					$privatesaveMap=array();
					$privatesaveMap['modelname']=$CurModel;
					$privatesaveMap['fieldname']=$shownamek;
					$privatesaveMap['userid']=$_SESSION [C ( 'USER_AUTH_KEY' )];
					$privateData=array();
					$privateData['widths']=$widths[$shownamek];
					$privateData['sortnum']=$sortnum[$shownamek];
					$privateData['shows']=$fieldexist?1:0;
					$privateData['sortsorder']=$sortsorder[$shownamek]?$sortsorder[$shownamek]:0;
					$privateData['sortstype']=$sortstype[$shownamek]?$sortstype[$shownamek]:'asc';
					$privateData['userid']=$_SESSION [C ( 'USER_AUTH_KEY' )];
					//存在修改
					$privateList=$privateListincModel->where($privatesaveMap)->save($privateData);
					if($privateList==false){
						$this->error ( L('_ERROR_') );
					}
				}
			}
			//删除不存在的字段
			$privarteDeleteMap['modelname']=$modelName;
			$privarteDeleteMap['fieldname']=array('not in',$fieldname);
			$privarteDeleteMap['userid']=$_SESSION [C ( 'USER_AUTH_KEY' )];
			$privarteincList=$privateListincModel->where($privarteDeleteMap)->delete();
			if($privarteincList==false){
				$this->error ( L('_ERROR_') );
			}
		}
		//重新设置缓存文件
		$mMisRuntimeData = D("MisRuntimeData");
		$model = D('SystemConfigDetail');
		$_REQUEST["detailadll"] = 1;
		if($_REQUEST["issub"]){
			// 系统管理员 配置 获取系统配置文件
			$detailList = $model->getSubDetail($CurModel,false);
			foreach ($detailList as $k1 => $v1) {
				$detailList[$k1]['shows'] = $_POST['shows'][$k1]?$_POST['shows'][$k1]:0;//是否显示字段 1->显示,0->不显示
				$detailList[$k1]['subshowname'] = $_POST['showname'][$k1]?$_POST['showname'][$k1]:"";
				$detailList[$k1]['widths'] = $_POST['widths'][$k1]?$_POST['widths'][$k1]:"";
				$detailList[$k1]['subsorts'] = $_POST['sorts'][$k1]?$_POST['sorts'][$$k1]:0;
				$detailList[$k1]['subsortstype'] = $_POST['sortstype'][$k1]?$_POST['sortstype'][$$k1]:'asc';
				$detailList[$k1]['subsortnum'] = $_POST['sortnum'][$k1]?$_POST['sortnum'][$k1]:$detailList[$k1]['subsortnum'];
			}
			if($_SESSION['a']){
				$model->setSubDetail($CurModel, $detailList);
			} else {
				$mMisRuntimeData->setRuntimeCache($detailList,$CurModel,'subDetailList');
			}
		} else {
			// 系统管理员 配置 获取系统配置文件
			$detailList = $model->getDetail($CurModel,false);
			//组装数据
			foreach ($detailList as $kex =>$valx) {
				$detailList[$kex]['shows'] = $_POST['shows'][$kex]?$_POST['shows'][$kex]:0;//是否显示字段 1->显示,0->不显示
				$detailList[$kex]['showname'] = $_POST['showname'][$kex]?$_POST['showname'][$kex]:"";
				$detailList[$kex]['widths'] = $_POST['widths'][$kex]?$_POST['widths'][$kex]:"";
				$detailList[$kex]['sorts'] = $_POST['sorts'][$kex]?$_POST['sorts'][$kex]:0;
				$detailList[$kex]['sortsorder'] = $sortsorder[$kex]?$sortsorder[$kex]:0;
				$detailList[$kex]['sortstype'] = $_POST['sortstype'][$kex]?$_POST['sortstype'][$kex]:'asc';
				$detailList[$kex]['sortnum'] = $_POST['sortnum'][$kex]?$_POST['sortnum'][$kex]:$detailList[$kex]['sortnum'];
			}
			if($_SESSION['a']){
				$model->setDetail($CurModel, $detailList);
			} else {
				$mMisRuntimeData->setRuntimeCache($detailList,$CurModel,'detailList');
			}
		}
		$this->success(L('_SUCCESS_'));
	}

	/**
	 * @Title: getdotype
	 * @Description: todo(获取工作执行模板(MisWorkExecuting)下 单据处理进度)
	 * @author libo
	 * @date 2014-5-6 下午3:25:51
	 * @throws
	 */
	public function getdotype(){
		//查询处理状态
		$workModel=M("mis_work_executing");
		$tablename = $this->getActionName();
		$tableid = $_REQUEST['id'];
		$dotype=$workModel->where("tablename='".$tablename."' and tableid=".$tableid)->getField('dotype');
		$this->assign("userid",$_SESSION[C('USER_AUTH_KEY')]);
		$this->assign("dotype",$dotype);
	}
	/**
	 * @Title: setToolBorInVolist
	 * @Description: todo(重构volist,加入按钮控制)
	 * @param 列表数据 $voList
	 * @author 杨东
	 * @date 2014-5-26 上午10:59:06
	 * @throws
	 */
	protected function setToolBorInVolist(&$voList){
		// 获取配置文件
		$scdmodel = D('SystemConfigDetail');
		$name = $this->getActionName();
		$toolbarextension = $scdmodel->getDetail($name,true,'toolbar');
// 		$str = "";
		if($toolbarextension){
			// 构造按钮控制
			foreach ($voList as $key => $val) {
// 				if($val['formtype'] == 2 && 1==2){
// 					$a = D($val['formobj']);
// 					$tablename = $a->getTableName();
// 					$str .= "delete from ".$tablename.";";
// 				}
				//封装单据个人，部门，部门及子部门，全部。等全线问题
				$classarr = array();//安置集合
				foreach ($toolbarextension as $k => $v) {
					$bool = true; //默认当前按钮可执行
					if($v['ifcheck'] && $k!='js-add' && $k!='js-printOut'){
						//表示验证session
						if( !isset($_SESSION['a']) ){
							if( $_SESSION[$v['permisname']]!=1 ){
								if( $_SESSION[$v['permisname']]==2 ){////判断公司权限
									if($val['companyid']!=$_SESSION['companyid']){
										$bool = false;
									}
								}else if($_SESSION[$v['permisname']]==3){//判断部门权限
									if($val['departmentid']!=$_SESSION['user_dep_id']){
										$bool = false;
									}
								}else if($_SESSION[$v['permisname']]==4){//判断个人权限
									if($val['createid'] != $_SESSION[C('USER_AUTH_KEY')]||$val['companyid']!=$_SESSION['companyid']){
										$bool = false;
									}
								}
							}
						}
					}
					if($bool){
						if ($v['rules']) {
							// 判断是否有传值过来
							$matches=array();
							preg_match_all('|#+(.*)#|U', $v['rules'], $matches);
							$a = $v['rules'];
							foreach($matches[1] as $k2=>$v2){
								if(isset($val[$v2])){
									$a = str_replace($matches[0][$k2],$val[$v2],$a);
								} else {
									$a = str_replace($matches[0][$k2],$v2,$a);
								}
							}
							//警告：这里一定不能修改成 if($a) eval("\$a = \"$a\";"); ，这样会导致所有的按钮不受数据控制。
							@eval("\$a =".$a.";");
							if( $a ){
								$classarr[$k] = $k;
							}
						} else {
							$classarr[$k] = $k;
						}
						if($classarr[$k]){
							if($v['disabledmap']){
								// 判断是否有传值过来
								$matches=array();
								preg_match_all('|#+(.*)#|U', $v['disabledmap'], $matches);
								$a = $v['disabledmap'];
								foreach($matches[1] as $k2=>$v2){
									if(isset($val[$v2])){
										$a = str_replace($matches[0][$k2],$val[$v2],$a);
									} else {
										$a = str_replace($matches[0][$k2],$v2,$a);
									}
								}
								//警告：这里一定不能修改成 if($a) eval("\$a = \"$a\";"); ，这样会导致所有的按钮不受数据控制。
								@eval("\$a =".$a.";");
								if( $a ){
									unset($classarr[$k]);
								}
							}
						}
					}
				}
				$voList[$key]['classarr'] = json_encode($classarr);
			}
		}
	}
	/**
	 * @Title: syncEmployeeUser
	 * @Description: todo(根据人事ID，变更部门，职级，岗位通用方法)
	 * @param int $employeid 人事ID
	 * @param int $deptid 变更后的部门ID
	 * @param int $dutyid 变更后的职级ID
	 * @param int $roleid 变更后的岗位对应的授权组id
	 * @author liminggang
	 * @date 2014-5-30 下午3:53:00
	 * @throws
	 */
	protected function syncEmployeeUser($employeid,$deptid,$dutyid,$roleid){
		//第一步，根据人事ID，查询是否存在后台用户
		$userDao = M("user");
		//获取后台用户信息
		$userlist = $userDao->where("employeid = ".$employeid)->find();
		//存在后台用户，则根据传过来的修改相应的内容
		if($userlist){
			//后台用户部门职级表
			$userDeptDutyDao = M("user_dept_duty");
			//判断是否修改部门
			if($deptid && $userlist['dept_id']!=$deptid){
				//部门修改  (此处修改包括两个地方，1、user表。2、user_dept_duty)
				$data = array();
				$data['dept_id'] = $deptid;
				$data['id'] = $userlist['id'];
				$userdeptresult=$userDao->save($data);
				//第二部分
				$data = array();
				$data['deptid'] = $deptid;
				$where = array();
				$where['deptid'] = $userlist['dept_id'];
				$where['dutyid'] = $userlist['duty_id'];
				$where['userid'] = $userlist['id'];
				$deptdutyresult= $userDeptDutyDao->where($where)->save($data);
				if(!$userdeptresult || !$deptdutyresult){
					$this->error($userlist['name']."的部门修改失败，请联系管理员");
				}
			}
			//修改职级
			if($dutyid && $userlist['duty_id']!=$dutyid){
				$data = array();
				$data['duty_id'] = $dutyid;
				$data['id'] = $userlist['id'];
				$userdutyresult=$userDao->save($data);
				//第二部分
				$data = array();
				$data['dutyid'] = $dutyid;
				$where = array();
				$where['deptid'] = $deptid;
				$where['dutyid'] = $userlist['duty_id'];
				$where['userid'] = $userlist['id'];
				$deptdutyresults= $userDeptDutyDao->where($where)->save($data);
				if(!$userdutyresult || !$deptdutyresults){
					$this->error($userlist['name']."的职级修改失败，请联系管理员");
				}
			}
			if($roleid  && $userlist['role_id'] != $roleid){
				//构造后台用户所有角色
				$attachrole = explode(",",$userlist['attachrole']);
				//将旧的授权组，排除了。
				$newrolegroup = array_diff($attachrole,array($userlist['role_id']));
				//将新的授权组封装到数组中。
				array_push($newrolegroup,$roleid);
				$newattachrole = implode(",",$newrolegroup);

				$data = array();
				$data['attachrole'] = $newattachrole;
				$data['role_id'] = $roleid;
				$data['id'] = $userlist['id'];
				$roleresult=$userDao->save($data);
				if(!$roleresult){
					$this->error ($userlist['name']."的岗位修改失败，请联系管理员！" );
				}
				//更新rolegroug_user 表
				$RolegroupUserDao = M("rolegroup_user");
				$where = array();
				$where['user_id'] = $userlist['id'];
				$where['rolegroup_id'] = $userlist['role_id'];
				$rolegrouplist=$RolegroupUserDao->where($where)->find();
				if($rolegrouplist){
					$data = array();
					$data['user_id'] = $userlist['id'];
					$data['rolegroup_id'] = $roleid;
					$rolegroupresult=$RolegroupUserDao->where($where)->save($data);
					if(!$rolegroupresult){
						$this->error ($userlist['name']."的岗位修改失败，请联系管理员！" );
					}
				}
				//用户组重新授权时 删除缓存权限
				$obj_dir = new Dir;
				$directory =  DConfig_PATH."/AccessList";
				$obj_dir->del($directory);
			}
		}
	}
	/**
	 * @yangxi
	 * 20120906
	 * remark:将用户加入到对应的高级权限组
	 * @parameter  string  $userId    用户ID
	 * @parameter  array   $roleGroup 权限组
	 */
	protected function addRolegroup($userId,$roleGroup="") {
		if($roleGroup!=""){
			foreach($roleGroup as $key => $value){
				//新增用户自动加入相应权限组
				$RolegroupUser = M("rolegroup_user");
				$RolegroupUser->user_id	=	$userId;
				// 默认加入高级权限组
				$RolegroupUser->rolegroup_id	=	$value;
				$result=$RolegroupUser->add();
				if(!$result){
					$this->error("角色授予失败");
				}
			}
		}
	}
	/**
	 *数字金额转换成中文大写金额的函数
	 *String Int $num 要转换的小写数字或小写字符串
	 *return 大写字母
	 *小数位为两位
	 **/
	function get_amount($num){
		$c1 = "零壹贰叁肆伍陆柒捌玖";
		$c2 = "分角元拾佰仟万拾佰仟亿";
		$num = round($num, 2);
		$num = $num * 100;
		if (strlen($num) > 18) {
			return "数据太长，没有这么大的钱吧，检查下";
		}
		$i = 0;
		$c = "";
		while (1) {
			if ($i == 0) {
				$n = substr($num, strlen($num)-1, 1);
			} else {
				$n = $num % 10;
			}
			$p1 = substr($c1, 3 * $n, 3);
			$p2 = substr($c2, 3 * $i, 3);
			if ($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == '元'))) {
				$c = $p1 . $p2 . $c;
			} else {
				$c = $p1 . $c;
			}
			$i = $i + 1;
			$num = $num / 10;
			$num = (int)$num;
			if ($num == 0) {
				break;
			}
		}
		$j = 0;
		$slen = strlen($c);
		while ($j < $slen) {
			$m = substr($c, $j, 6);
			if ($m == '零元' || $m == '零万' || $m == '零亿' || $m == '零零') {
				$left = substr($c, 0, $j);
				$right = substr($c, $j + 3);
				$c = $left . $right;
				$j = $j-3;
				$slen = $slen-3;
			}
			$j = $j + 3;
		}
		if (substr($c, strlen($c)-3, 3) == '零') {
			$c = substr($c, 0, strlen($c)-3);
		}
		if (empty($c)) {
			return "零元整";
		}else{
			return $c . "整";
		}
	}
	/**
	 * @Title: lookupSeeFlow
	 * @Description: todo(柔性流程前置表单展示)
	 * @author liminggang
	 * @date 2014-6-10 上午9:44:32
	 * @throws
	 */
	public function lookupSeeFlow(){
		//判断是否是自定义流程
		$flowid = $_REQUEST['flowid'];
		//判断是否为修改已经存储了数据
		$infoid = $_REQUEST['infoid'];
		$this->assign("flowid",$flowid);
		if($flowid){
			$name = "MisOaFlows";
			$model = D ( $name );
			$map['id']=$flowid;
			$vo = $model->where($map)->find();
			$flowdata = unserialize($vo['flowtrack']);
			// 进入查看页面
			$jsondata = A("CommonFlows")->setDataJson($flowdata,$vo['id'],1);
		}else{
			//第一步，获取当前类名称
			$modulename = $this->getActionName();// 当前类名

			if($infoid){
				$pid = getFieldBy($infoid, 'id', "ptmptid", $modulename);
			}else{
				//第二步，获取审核节点
				$pcmodel = D('ProcessConfig');
				$pcarr =  $pcmodel->getprocessinfo($modulename,$data);
				//获取流程节点ID
				$pid = $pcarr['pid'];
			}
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
			$jsondata =  A("CommonFlows")->setDataJson($flowdata,0,1);
		}
		$this->assign("data",$jsondata);
		$this->display("CommonFlows:showFlowsWidget");
	}
	/**
	 *
	 * @Title: addUser
	 * @Description: todo(添加后台用户W函数)
	 * @param unknown_type $id
	 * @param unknown_type $type
	 * @author renling
	 * @date 2014-9-10 上午10:16:18
	 * @throws
	 */
	public function addUser($id,$type=1){
		if($_POST['isaddUser'] ==1){
			//登陆地址不存在,将登陆地址保存入公司信息
			if(!$_POST['loginurl']){
				$MisSystemCompanyModel=D('MisSystemCompany');
				$loginurlResult=$MisSystemCompanyModel->where("status=1")->setField("loginurl", $_POST['userurl']);
				if(!$loginurlResult){
					$this->error("登陆地址修改失败。");
				}
			}
			$mis_hr_personnel_person_infoDao = M("mis_hr_personnel_person_info");
			$personnelData=$mis_hr_personnel_person_infoDao->where("id = ".$id)->find();
			$userModel=D('User');
			$userdate['account']=$_POST['account'];
			$userdate['employeid']=$id;
			$userdate['email']=$personnelData['email'];
			$userdate['mobile']=$_POST['phone'];
			$userdate['zhname']=$_POST['zhname'];
			$userdate['name']=$personnelData['name'];
			$userdate['sex']=$personnelData['sex'];
			$userdate['duty_id']=$personnelData['dutylevelid'];
			$userdate['dept_id']=$personnelData['deptid'];
			//获取人事岗位对应的角色
			$rolegroup_id=getFieldBy($personnelData['worktype'], 'id', 'rolegroup_id', 'mis_hr_job_info');
			$userdate['role_id'] = $rolegroup_id;
			$userdate['attachrole'] = $rolegroup_id;
			$userdate['password']=pwdHash(C('USER_DEFAULT_PASSWORD'));
			$userdate['createid']=$_SESSION[C('USER_AUTH_KEY')];
			$userdate['createtime']=time();
			$userResultid=$userModel->add($userdate);
			if(C('SWITCH_SENIOR_ROLE_GROUP')=='On'){
				// 插入用户初始化高级权限组
				$this->addRolegroup($userResultid,C("USER_SENIOR_ROLE_GROUP"));
			}
			if($userResultid){
				//反写userid到人事表 by xyz 2015-10-22
				$pimap['id'] = $id;
				$pidata['userid'] = $userResultid;
				$pret = $mis_hr_personnel_person_infoDao->where($pimap)->save($pidata);
				if(false === $pret){
					$this->error("反写userid到人事表失败");
				}
				//
				
				
				$companyid=$_REQUEST['companyid'];
				$UserDeptDutyModel=D('UserDeptDuty');
				$RolegroupUserModel=D("RolegroupUser");
				$companyList=array();
			 	if($type==1){
				foreach ($companyid as $key=>$val){
					if(in_array($val,array_keys($companyList))){
						$this->error(getFieldBy($val,"id","name","mis_system_company")."添加重复,请查证后提交！");
					}
			 	$UserDeptDuty=array();
			 	$UserDeptDuty['userid']=$userResultid;
			 	$UserDeptDuty['deptid']=$_POST['deptid'][$key];
			 	$UserDeptDuty['dutyid']=$_POST['dutylevelid'][$key];
			 	$UserDeptDuty['worktype']=$_POST['worktype'][$key];
			 	$UserDeptDuty['employeid']=$id;
			 	$UserDeptDuty['typeid']=1;
			 	$UserDeptDuty['companyid']=$_POST['companyid'][$key];
			 	$UserDeptDuty['createid']=$_SESSION[C('USER_AUTH_KEY')];
			 	$UserDeptDuty['createtime']=time();
			 	if($type==2){
			 		$UserDeptUserResult=$UserDeptDutyModel->where('employeid='.$id)->save($UserDeptDuty);
			 	}else{
			 		$UserDeptUserResult=$UserDeptDutyModel->add($UserDeptDuty);
			 	}
			 	$UserDeptDutyModel->commit();
			 	$companyList[$val]=1;
		 		$RolegroupDate=array();
		 		$RolegroupDate['user_id']=$userResultid;
		 		$RolegroupDate['companyid']=$_POST['companyid'][$key];
		 		$RolegroupDate['rolegroup_id']=getFieldBy($_POST['worktype'][$key], "id", "rolegroup_id", "mis_hr_job_info");
		 		$RolegroupUserResult=$RolegroupUserModel->add($RolegroupDate);
		 		if(!$RolegroupUserResult){
		 			$this->error("权限组插入错误！");
		 		}
			 	if(!$UserDeptUserResult){
			 		$this->error("数据异常");
			 	}
				}
			 	}else{
			 		//列表新增用户调用方法
			 		$UserDeptDutyMap['employeid']=$id;
			 		$UserDeptDutyMap['status']=1;
			 		$result=$UserDeptDutyModel->where($UserDeptDutyMap)->setField('userid',$userResultid );
			 		$UserDeptDutyList=$UserDeptDutyModel->where($UserDeptDutyMap)->select();
			 		foreach ($UserDeptDutyList as $key=>$val){
			 			$RolegroupDate=array();
			 			$RolegroupDate['user_id']=$userResultid;
			 			$RolegroupDate['rolegroup_id']=getFieldBy($val['worktype'], "id", "rolegroup_id", "mis_hr_job_info");
			 			$RolegroupDate['companyid']=$val['companyid']; 
			 			$RolegroupUserResult=$RolegroupUserModel->add($RolegroupDate);
			 			if(!$RolegroupUserResult){
			 				$this->error("权限组插入错误！");
			 			}
			 		}
			 	}
			}
			if($userResultid && $_POST['issendemail']){
				$email=array($_POST['useremail']);
				//邮箱发送
				$title="[".C('COPYRIGHT')."]请查收您的用户信息";
				$content="亲爱的用户，您好！<br/> 您在[".C('COPYRIGHT')."]注册信息如下: <br/>英文登陆名为:'".$_POST['account']."'";
				if($_POST['zhname']){
					$content.="<br/>中文登陆名为: '".$_POST['zhname']."'";
				}
				$content.="<br/>初始密码为: '".C('USER_DEFAULT_PASSWORD')."' <br/> 请点击<a href='".$_POST['userurl']."'> ".$_POST['userurl']."</a>登陆系统修改您的密码！";
				$vo['name'] = C("EMAIL_SERVERNAME");
				$vo['address'] ="brianl_yang";
				$vo['server'] = "163.com";
				$vo['email'] = C("EMAIL_SERVERADDRESS");
				$vo['pop3'] = "pop.163.com";
				$vo['smtp'] = "smtp.163.com";
				$vo['password'] =C("EMAIL_PASSWORD");
				$vo['pop3port']=110;
				$vo['smtpport']=25;
				$result = $this->SendEmail($title, $content, $email, "", $vo, 1,false);
				if($type!=1){
					if(!$result){
						$this->error("操作失败！");
					}else{
						$this->success("创建成功！");
					}
				}else{
					if(!$result){
						$this->error("操作失败！");
					}
				}
			}else{
				if($type!=1){
					if($userResultid){
						$this->success("创建成功！");
					}
				}
			}
		}
	}

	

	/*
	 * @author  wangcheng
	 * @date:2012-12-114 21:48:40
	 * @describe:分类的扩展属性[可通用]
	 * @param $map 查询条件 exp:map=array(
	 "catalogid"=>0;//类型 [可扩展1;2;3分别代表不同的l类型];
	 "linkid"   =>1024;//当前类型下所属分类id;
	 );
	 * @return:返回由<p> 标签组合的html 数组
	 */
	function get_expand_property($map,$data=array()){
		//print_r($data);
		unset($map['catalogid']);
		$fieldTypeModel = M("mis_typeform_field_type");
		$typeList = $fieldTypeModel->where("status=1")->getField('id,code');
		$fieldModel = M("mis_typeform_field");
		$map['status']=1;
		$fieldList =$fieldModel->where($map)->order("pos asc")->select();
		//print_R($fieldModel->getLastSql());
		$option=array();
		foreach($fieldList AS $k=>$field) {
			//定义检查条件数组
			$checkreg=array();
			if($field['checkreg']){
				$arrcheckreg=explode(";",$field['checkreg']);
				foreach($arrcheckreg as $k2=>$v2){
					$checkreg[]=$typeList[$v2];
				}
			}
			//print_r($defaultval);
			//通过传递的数组参数获取当前值或默认值
			$field['defaultval']= isset($data[$field['id']]['content']) ? $data[$field['id']]['content']:$field['defaultval'];
			$option[]= $this->expand_property_html($typeList[$field['fieldtypeid']], $field['name'], $field['options'], $field['defaultval'], $field['tips'] ,$data[$field['id']]['id'],$field['id'], $field['ismust'],$checkreg);
		}
		return $option;
	}

	/*
	 * @author  wangcheng
	* @date:2012-12-114 21:48:40
	* @describe:分类的扩展属性[可通用]
	* @param $map 查询条件 exp:map=array(
			"catalogid"=>0;//类型 [可扩展1;2;3分别代表不同的l类型];
			"linkid"   =>1024;//当前类型下所属分类id;
	);
	* @return:返回由<p> 标签组合的html 数组
	*/
	function expand_property_html($type = '', $title = '', $options = '' ,$default = '', $msg = '' ,$tableid = '',$extendid = '',$must = '',$checkreg='') {
		//print_R($default);
		//是否必填
		$must = $must ? 'required ' : '';
		//没有默认值时换成当前时间
		$default=$default ? $default:date('Y-m-d',time());
		//对对象进行验证
		foreach($checkreg as $key => $val ){
			switch($val){
				case 'date':
					//日期类型追加
					$dataExchange='<a href="javascript:;" class="inputDateButton">选择</a>';
					break;

				case 'readonly':
					//只读类型追加
					$readonly='readonly';
					break;

				case 'editor':
					//行数显示
					$rows='4';
					break;
			}
		}

		//定义提示信息
		$msg = $msg ? '<span class="info">&nbsp;'.$msg.'</span>':'';
		//根据类型返回字段类型
		switch ($type) {
			case 'text':
				//文本框
				$option .= '<div class="tml-form-row"><label>'.$title.'：</label>
						    <input type="hidden" name="expand_property_tableid[]" value="'.$tableid.'" />
						    <input type="hidden" name="expand_property_extendid[]" value="'.$extendid.'" />
						    <input type="text" class="'.$must.$checkreg.'" name="expand_property_content['.$extendid.']" value="'.$default.'" '.$readonly.' />'.$dataExchange.$msg.'</div>';
				break;

			case 'hidden':
				//隐藏表单
				$option .= '<input type="hidden" name="expand_property_tableid[]" value="'.$tableid.'" />
						    <input type="hidden" name="expand_property_extendid[]" value="'.$tableid.'" />
						    <input type="hidden" name="expand_property_content['.$extendid.']" value="'.$default.'" />';
				break;

			case 'file':
				//上传表单
				$option .= '<div class="tml-form-row"><label>'.$title.'：</label>
						    <input type="hidden" name="expand_property_tableid[]" value="'.$tableid.'" />    
						    <input type="hidden" name="expand_property_extendid[]" value="'.$extendid.'" />
						    <input type="file" class="'.$must.$checkreg.'" name="expand_property_content['.$extendid.']" value="'.$default.'" />'.$msg.'</div>';;
				break;

			case 'select':
				//下拉菜单
				$option .= '<div class="tml-form-row"><label>'.$title.'：</label>
						    <input type="hidden" name="expand_property_tableid[]" value="'.$tableid.'" />    
						    <input type="hidden" name="expand_property_extendid[]" value="'.$extendid.'" />
						    <select class="combox '.$must.$checkreg.'" name="expand_property_content['.$extendid.']">';
				$arr = explode(";", $options);
				$option .='<option value="">--选择--</option>';
				foreach($arr AS $k=>$val) {
					$select= ($default==$val) ? 'selected':'';
					$option .= '<option '.$select.' value="'.$val.'">'.$val.'</option>';
				}
				$option .= '</select>'.$msg.'</div>';
				break;

			case 'checkbox':
				//复选框
				$option .= '<div class="tml-form-row"><label>'.$title.'：</label>
						    <input type="hidden" name="expand_property_tableid[]" value="'.$tableid.'" />
						    <input type="hidden" name="expand_property_extendid[]" value="'.$extendid.'" />';
				$optionsArr = explode(";", $options);
				$defaultArr = explode(";", $default);
				foreach($optionsArr AS $k=>$v) {
					$checked= (in_array($v,$defaultArr,true)) ? 'checked':'';
					$option .= $v.'<input '.$checked.' name="expand_property_content['.$extendid.'][]" type="checkbox" value="'.$v.'" />&nbsp&nbsp&nbsp';
				}
				$option .= $msg.'</div>';
				break;

			case 'radio':
				//单选按钮
				$option .= '<div class="tml-form-row"><label>'.$title.'：</label>
						    <input type="hidden" name="expand_property_tableid[]" value="'.$tableid.'" />
						    <input type="hidden" name="expand_property_extendid[]" value="'.$extendid.'" />';
				$arr = explode(";", $options);
				foreach($arr AS $k=>$v) {
					$checked= ($default==$v) ? 'checked':'';
					$option .= $v.'<input '.$checked.' type="radio" name="expand_property_content['.$extendid.']" value="'.$v.'"/>&nbsp&nbsp&nbsp';
				}
				$option .= $msg.'</div>';
				break;
			case 'textarea':
				//文本区域
				$option .= '<div class="tml-form-row"><label>'.$title.'：</label>
						    <input type="hidden" name="expand_property_tableid[]" value="'.$tableid.'" />
						    <input type="hidden" name="expand_property_extendid[]" value="'.$extendid.'" />
						    <textarea name="expand_property_content['.$extendid.']" class="'.$must.$checkreg.'" cols="60" rows="'.$rows.'">'.$default.'</textarea>'.$msg.'</div>';
				break;
			case 'pass':
				//密码表单
				$option .= '<div class="tml-form-row"><label>'.$title.'：</label>
						    <input type="hidden" name="expand_property_tableid[]" value="'.$tableid.'" />
						    <input type="hidden" name="expand_property_extendid[]" value="'.$extendid.'" />
						    <input type="password" class="'.$must.$checkreg.'" name="expand_property_content['.$extendid.']" value="'.$default.'" />'.$msg.'</div>';
				break;
		}
		return $option;
	}

	/*
	 * @author  wangcheng
	 * @date:2012-12-114 21:48:40
	 * @describe:分类的扩展属性[可通用]
	 * @param $map 查询条件 exp:map=array(
	 "catalogid"=>0;//类型 [可扩展1;2;3分别代表不同的l类型];
	 "linkid"   =>1024;//当前类型下所属分类id;
	 );
	 * @return:返回由<p> 标签组合的html 数组
	 */
	public function set_expand_property($data){
			// 保存扩展属性
			// 循环插入,以下三个变量为一组同步值
			//获取当前存储表ID
			$expand_property_tableid=$_POST['expand_property_tableid'];
			//获取扩展属性ID
			$expand_property_extendid=$_POST['expand_property_extendid'];
			//获取内容
			$expand_property_content=$_POST['expand_property_content'];
			$tableid=$data['id'];
			foreach($expand_property_extendid as $key=>$val){
					if($expand_property_tableid[$key]){
						$model=M("mis_typeform_data");
						$data=array();
						$data['id']=$expand_property_tableid[$key];
						$data['extendid']=$val;
						$data['modelname']=$this->getActionName();
						$data['tableid'] = $tableid;
						//$data['linkid']="mis_product_code";
						if(is_array($expand_property_content[$val])){
							$expand_property_content[$val]=implode(";",$expand_property_content[$val]);
						}
						$data['content']=$expand_property_content[$val];
						$rel = $model->save($data);
						if(!$rel) $this->error( "扩展属性添加失败");
					}else{
							$model=M("mis_typeform_data");
							$data=array();
							$data['extendid']=$val;
							$data['modelname']=$this->getActionName();
							$data['tableid'] = $tableid;
							if(is_array($expand_property_content[$val])){
							$expand_property_content[$val]=implode(";",$expand_property_content[$val]);
							}
							$data['content']=$expand_property_content[$val];
							$rel = $model->add($data);
							if(!$rel) $this->error( "扩展属性添加失败");
				if($expand_property_tableid[$key]){
					$model=M("mis_typeform_data");
					$data=array();
					$data['id']=$expand_property_tableid[$key];
					$data['extendid']=$val;
					$data['linkid']="mis_product_code";
					if(is_array($expand_property_content[$val])){
						$expand_property_content[$val]=implode(";",$expand_property_content[$val]);
					}
					$data['content']=$expand_property_content[$val];
					$rel = $model->save($data);
					//print_r($model->getLastSql());
					if(!$rel) $this->error( "扩展属性添加失败");
				}else{
					//if($expand_property){
					$model=M("mis_typeform_data");
					$data=array();
					$data['extendid']=$val;
					$data['linkid']="mis_product_code";
					if(is_array($expand_property_content[$val])){
						$expand_property_content[$val]=implode(";",$expand_property_content[$val]);
					}
					$data['content']=$expand_property_content[$val];
					$rel = $model->add($data);
					if(!$rel) $this->error( "扩展属性添加失败");
					//}
				}
			}
		}
	}

	/**
	 *
	 * @Title: getArrTree
	 * @Description: todo(将一维数组做成树状结构)
	 * @param $data array 要转换的一维数组 （要求带有id和父id）
	 * @param $pid int  从父id为第几的位置开始转化（默认为0）
	 * @param $key string 数组的id键名(默认为id)；
	 * @param $pkey string 父id的键名
	 * @param $childkey string 键名 用于放子id数组
	 * @param $maxDepth int 防止无限循环
	 * @return array
	 * @author xyz
	 * @date 2014-9-10 上午10:16:18
	 * @throws
	 */
	function getArrTree($data, $pid = 0, $key = 'id', $pKey = 'parentId', $childKey = 'child', $maxDepth = 0){
		static $depth = 0;
		if (intval($maxDepth) <= 0)
		{
			$maxDepth = count($data) * count($data);
		}
		if ($depth > $maxDepth)
		{
			exit("error recursion:max recursion depth {$maxDepth}");
		}
		$tree = array();
		foreach ($data as $rk => $rv)
		{
			if ($rv[$pKey] == $pid)
			{
				$rv[$childKey] = $this->getArrTree($data, $rv[$key], $key, $pKey, $childKey, $maxDepth);
				$tree[] = $rv;
			}
		}
		return $tree;
	}
	
	/*
	 * @author  yangxi
	* @date:2014-11-21 11:48:40
	* @describe:生成单据-推：可推送单据对象列表
	* @return:直接输出值到页面模板
	*/	
	public function  lookupDataRoamPullList(){
		$sourcemodel=$_REQUEST['sourcemodel'];
		$sourceid=$_REQUEST['sourceid'];
		$modulename = MODULE_NAME;// 当前类名
		$model=D('MisSystemDataRoamMas');
		$map['sourcemodel']=$modulename;
		$map['sourcetype']=4;
		$map['status']=1;
		$result=$model->where($map)->select();	
		//echo $model->getLastSql();
		foreach($result as $key => $val){
			$map=array();
			$map['id']=$sourceid;
			if($val['rules']){
			   $map['_string']=$val['rules'];
			}
			$list=D($sourcemodel)->where($map)->select();
			//echo D($sourcemodel)->getLastSql();
			if(!$list){
				unset($result[$key]);
			}
				
		}	
		$this->assign ('result', $result );		
		//传递当前model与记录id
		$this->assign("sourcemodel",$_REQUEST['sourcemodel']);
		$this->assign("sourceid",$_REQUEST['sourceid']);		
		$this->display('Public:lookupDataRoamPullList');
	}
	/*
	 * @author  yangxi
	* @date:2014-11-21 11:48:40
	* @describe:生成单据-拉：可拉送单据对象列表
	* @return:直接输出值到页面模板
	*/	
	public function  lookupDataRoamPushList(){
		$modulename = MODULE_NAME;// 当前类名
		$model=D('MisSystemDataRoamMas');
		$map['targetmodel']=$modulename;
		$map['sourcetype']=4;
		$map['status']=1;
		$result=$model->where($map)->select();
		//print_r($result);
		$this->assign ('result', $result );		
// 		//传递当前model与记录id
		$this->assign("sourcemodel",$_REQUEST['sourcemodel']);
		$this->assign("sourceid",$_REQUEST['sourceid']);
		$this->display('Public:lookupDataRoamPushList');
	}
	/*
	 * @author  yangxi
	* @date:2014-11-21 11:48:40
	* @describe:生成单据-拉：可拉送单据对象的选择单据列表
	* @return:直接输出值到页面模板
	*/	
	public function  lookupDataRoamPush(){
	//获取查找带回的字段
		$this->assign("sourcemodel",$_REQUEST['sourcemodel']);
		$this->assign("targetmodel",$_REQUEST['targetmodel']);
		$this->assign("field",$_REQUEST['field']);
		$_POST['dealLookupList'] = 1;//强制查找带回重构数据列表
		$name = $_POST['model'];
		//获取部门类型 ————快捷新增客户
		$deptid=$_REQUEST['deptid'];
		$this->assign("deptid",$deptid);
		if(strpos($name, '_')){//将表转换为model
			$nameArr = explode('_', $name);
			$names = "";
			foreach ($nameArr as $k => $v) {
				$names .= ucfirst($v);
			}
			if($names){
				$name = $names;
			}
		}
		if(substr($name, -4)=="View"){
			$qx_name = $name;
			$name = substr($name,0, -4);
		}
		$this->assign("model",$name);
		// 单据号是否可写
		$table = D($name)->getTableName();
		$scnmodel = D('SystemConfigNumber');
		$writable= $scnmodel->GetWritable($table);
		$this->assign("writable",$writable);

		$ConfigListModel = D('SystemConfigList');
		$lookupGeneralList = $ConfigListModel->GetValue('lookupGeneralInclude');// 快速新增配置列表
		$include = $lookupGeneralList[$name];//获取配置信息
		$layoutH = 110;//默认高度
		if($include){
			$layoutH = $include['layoutH'];//获取高度
			$this->assign("tplName",'LookupGeneral:'.$include['tpl']);//设置默认模版
			$this->assign("isauto",$include['isauto']);//设置编号自动生成
		}
		$this->assign("layoutH",$layoutH);//设置高度
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($name);
		if ($detailList) {
			$inArray = array('action','remark','status');
			if ($_REQUEST['filterfield']) {
				$filterfield = explode(',', $_REQUEST['filterfield']);
				$inArray = array_merge($inArray,$filterfield);
				$this->assign('filterfield',$_REQUEST['filterfield']);
			}
			foreach ($detailList as $k => $v) {
				if(in_array($v['name'], $inArray)){
					unset($detailList[$k]);
				}
			}
			$this->assign ( 'detailList', $detailList );
		}
		$action=A("Common");
		$map = $this->_search($name);
		$conditions = $_POST['conditions'];// 检索条件
		if($conditions){
			$this->assign("conditions",$conditions);
			$cArr = explode(';', $conditions);//分号分隔多个参数
			foreach ($cArr as $k => $v) {
				$wArr = explode(',', $v);//逗号分隔字段、参数、修饰符
				if ($wArr[0] == "_string") { // 判断是否传的为字符串条件
					$map['_string'] = $wArr[1];
				} else {
					if ($wArr[2]) {//存在修饰符的以修饰符形式进行检索
						$map[$wArr[0]] = array($wArr[2],$wArr[1]);
					} else {//普通检索
						$map[$wArr[0]] = $wArr[1];
					}
				}
			}
		}
		$newconditions = $_POST['newconditions'];// lookup检索条件 为兼容条件选择器 by renling
		if($newconditions){
			$newconditions= str_replace (array ('&quot;','&#39;','&lt;','&gt;'), array ('"', "'",'<','>'),$newconditions);
			//把条件加入map 中
			$map['_string'].=$newconditions;
		}
		$map['status'] = 1;
		$filterfield = "_lookupGeneralfilter";
		if($_REQUEST['filtermethod']) $filterfield = $_REQUEST['filtermethod'];
		if (method_exists($this,$filterfield)) {
			call_user_func(array(&$this,$filterfield),&$map);
		}
		if ($qx_name) {
			$action->_list ( $qx_name, $map );
		} else {
			$action->_list ( $name, $map );
		}
		$this->display('Public:lookupDataRoamPush');
	}
	/*
	 * @author  yangxi
	* @date:2014-11-21 11:48:40
	* @describe:生成单据-推：直接将值输出到新增页面
	* @return:直接输出值到页面模板
	*/	
	public function  lookupDataRoamPull($sourcemodel,$sourceid,$targetmodel){
		//初始化赋值
		empty($sourcemodel)?$sourcemodel=$_REQUEST["sourcemodel"]:$sourcemodel;
		empty($sourceid)?$sourceid=$_REQUEST["sourceid"]:$sourceid;
		empty($targetmodel)?$targetmodel=$_REQUEST["targetmodel"]:$targetmodel;		
		$model=D('MisSystemDataRoaming');
		$data=$model->dataRoamOrderno($sourcemodel,$sourceid,$targetmodel);
		if($data===false){
			echo "单据生成数据不全！";
		}
		// 获取目标action的add模板
		$orderPath = TMPL_PATH.C('DEFAULT_THEME').'/'.$targetmodel.'/add.html';
		if(!file_exists($orderPath)){
			exit('目标对象中没有找到add.html');
		}
		$orderActionName = $targetmodel;
		$actionObj = A($orderActionName);
		//$actionObj->add();
		//$this->fetch($orderActionName.':add');
		//$this->display();
// 		$this->display($orderActionName.':add');
		//$content = file_get_contents($orderPath);
		//echo $content;
		
		$vo=array();
		//这里分离数据成为主表数据及内嵌表数据
		foreach($data as $k=>$v){
			$vo[key($v)] = reset($v);
		}
		//获取数据漫游映射
		$model=D('MisSystemDataRoaming');
		
		$dataArr=$model->main(1,$sourcemodel,$sourceid,4,$targetmodel);
		//$data=$model->dataRoamOrderno($sourcemodel,$sourceid,$targetmodel);
		//查询当前配置文件
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($name);
		foreach($dataArr as $k=>$v){
			foreach($v as $k1 => $v1){
				//当前组件为lookup
				if($detailList[key($v1)]['fieldcategory']=="lookup"){
					$showname=getFieldBy(reset($v1), $detailList[key($v1)]['funcdata'][0][0][1], $detailList[key($v1)]['funcdata'][0][0][2], $detailList[key($v1)]['funcdata'][0][0][3]);
					$vo[key($v1)]=array(
							'value'=>reset($v1),
							'showname'=>$showname,
					);
				}else{
					$vo[key($v1)] = reset($v1);
				}
			}
		}
		$this->getSystemConfigDetail($orderActionName,$vo);
		$this->assign("addvo",$vo);		
//define('MODULE_NAME',   "abc");
$this->assign('vo',$vo);
		$content = $this->fetch($orderActionName.':add');
		$this->assign('content',$content);		
		$this->assign('data',json_encode($data));
	//var_dump(MODULE_NAME);
	//var_dump($orderActionName);
	//echo "123";
	//exit;
		$this->display("Public:lookupDataRoamOrderno");
		//exit;
		
	}
	
   /*
	* @author  王昭侠
	* @date:2014-11-27 17:45:00
	* @describe: html模板替换
	* @return:替换后的html路径
	*/
	public function printout(){
		$map = array();
		$map['id'] = $_POST['id'];
		$name=$this->getActionName();
		$contractModel = D($name);
		$bookNamearr = array();
		$bookNamearr = $contractModel->where($map)->select();
		$is_exists = true;
		if($bookNamearr){
			$bookNamearr = $this->dataCL($name,$bookNamearr);
			if(!empty($bookNamearr[0]))
			{
				$bookNamearr = $bookNamearr[0];
			}
			$docfile = UPLOAD_SampleWord.$name.'.htm';
			$causedir = UPLOAD_SampleWord.$name;
			$fileName = time();
		}
		else{
			echo 0;exit;//$this->error ('没有查找可用的数据！');
		}
		if (!file_exists ($docfile)) {
			
			$is_exists = false;//$this->error("模板文件不存在，请检查");
		}
		$htrmUrl = "http://".$_SERVER['SERVER_NAME'].__ROOT__."/Admin/index.php/".$name."/view/id/".$map['id'];
		if($is_exists){
			if ( !file_exists($causedir) ) {
				$this->createFolders($causedir); //判断目标文件夹是否存在
			}
			$filepath = pathinfo($docfile);
			$htmlFileSaveName = $causedir.'/'.$fileName.'.htm';
			$htrmUrl = "http://".$_SERVER['SERVER_NAME'].__ROOT__."/Public/SampleWord/".$filepath["filename"]."/".$fileName.'.htm';
			$html = file_get_contents($docfile);
			//$html = iconv("UTF-8","GBK",$html);
			foreach($bookNamearr as $k => $v)
			{
				$html = $this->setHtmlValue($k,$v,$html);
			}
			if (is_writable($docfile)) {
		
				// 在这个例子里，我们将使用添加模式打开$filename，
				// 因此，文件指针将会在文件的开头，
				// 那就是当我们使用fwrite()的时候，$somecontent将要写入的地方。
				if (!$handle = fopen($htmlFileSaveName, 'w+')) {
					echo 0;exit;//不能打开文件 $htmlFileSaveName
				}
				if (fwrite($handle, $html) === FALSE) {
					echo 0;exit;//不能写入到文件 $htmlFileSaveName
				}
				fclose($handle);
			}
		}
		echo $htrmUrl;
	}
	
   /*
	* @author  王昭侠
	* @date:2014-11-28 9:45:00
	* @describe: 数据库取出的数据进行处理后显示
	* @return:处理后的数组
	*/
	public function dataCL($modelName,$dataList){
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($modelName,false);
		//定义数组
		$data=array();
		foreach($dataList as $k=>$v ){
			$data[$k]=array();
			foreach($detailList as $k2=>$v2){
				$data[$k][$v2['name']]="";
				if(count($v2['func']) >0){
					foreach($v2['func'] as $k3=>$v3){
						if(isset($v2['extention_html_start'][$k3])){
							$data[$k][$v2['name']]=$v2['extention_html_start'][$k3];
						}
							
						$data[$k][$v2['name']] .= getConfigFunction($v[$v2['name']],$v3,$v2['funcdata'][$k3],$volist[$k],true,$v2["domainid"]);
							
						if(isset($v2['extention_html_end'][$k3])){
							$data[$k][$v2['name']].=$v2['extention_html_end'][$k3];
						}
					}
				}else{
					$data[$k][$v2['name']]= $v[$v2['name']];
				}
			}
		}
		
		return $data;
	}
	
   /*
	* @author  王昭侠
	* @date:2014-11-28 9:45:00
	* @describe: 替换html内容
	* @return:处理后的html
	*/
	public function setHtmlValue($search, $replace, $html,$is_zhuan=true) {
		$search = '/(?:\$|＄)(?:\{|｛)(\<.[^<>]*\>|\s*|\n*|\r*)*'.$search.'(\s*|\n*|\r*)*(\<.[^<>]*\>|\s*\n*\r*)*(?:\}|｝)/';
		if(!is_array($replace) && $is_zhuan) {
			$replace = iconv("UTF-8","GBK",$replace);
		}
		$html = preg_replace($search, $replace, $html);
		return $html;
	}
	
   /*
	* @author  王昭侠
	* @date:2014-12-13 9:45:00
	* @describe: 导出Excel
	*/
	public function export_excel_one(){
		set_time_limit(0);
		$id = $_REQUEST['id'];
		$name = $this->getActionName();
		$list = $this->get_export_label($id,$name);
		
		import('@.ORG.PHPExcel.IOFactory', '', $ext='.php');
		$template_excel = UPLOAD_Sample.$name.".xls";
		
		$templateModel = D("MisSystemTempleteManage");
		$template["modelname"] = $name;
		$template["zhuangtai"] = 1;
		$templatelist = $templateModel->where($template)->find();
		$filename = time();
		if( ! empty($templatelist)){
			$attachedRecord = $this->getAttachedRecordList($templatelist["id"],true,true,"MisSystemTempleteManage",0,false);
			$filename = $templatelist["name"];
			if($attachedRecord){
				foreach($attachedRecord as $k => $v){
					$file = pathinfo(UPLOAD_PATH.$v["attached"]);
					if(strtolower($file["extension"])=="xls" || strtolower($file["extension"])=="xlsx"){
						$template_excel = UPLOAD_PATH.$v["attached"];
					}
				}
			}
		}
		$filetype =end(explode(".",$template_excel));
		if($filetype=="xls"){
			$inputFileType = 'Excel5';
		}else if( $filetype=="xlsx"){
			$inputFileType = 'Excel2007';
		}
		if (!file_exists ($template_excel)) { //模板文件不存在
			$objPHPExcel = new PHPExcel();
			$ii = 0;
			//设置表头
			foreach($list as $k => $v){
				$colum = PHPExcel_Cell::stringFromColumnIndex($ii);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum.'1', $v["showname"]."");
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum.'2', "\${".$v["name"]."}");
				$ii++;
			}
			$objPHPExcel->getActiveSheet()->mergeCells('A4:G4');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A4', "温馨提示:模板文件不存在,请在模板管理里先上传一个模板");
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $inputFileType);
			$objWriter->save($template_excel);
		}
		
		if(!file_exists($template_excel)){
			$this->error("模板文件不存在");
		}
		
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objPHPExcel = $objReader->load($template_excel);
		$currentSheet = $objPHPExcel->getActiveSheet();
		$allColumn = $currentSheet->getHighestColumn();
		$allRow = $currentSheet->getHighestRow();
			
		$columnIndex = PHPExcel_Cell::columnIndexFromString($allColumn);
		for($i=1;$i<=$allRow;$i++)
		{
			for($j=0;$j<$columnIndex;$j++){
				$colum = PHPExcel_Cell::stringFromColumnIndex($j);
				$value = trim($currentSheet->getCellByColumnAndRow($j,$i)->getValue());//获取到值
				foreach($list as $k => $v){
					if(!is_array($v["value"])){
						$value = $this->setHtmlValue($k,$v["value"],$value,false);
						$currentSheet->setCellValue($colum.$i, $value);
					}
				}
			}
		}
		$filename = $filename.".".$filetype;
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $timestamp + 86400) . ' GMT');
		header('Expires: ' . gmdate('D, d M Y H:i:s', $timestamp + 86400) . ' GMT');
		header('Cache-control: max-age=86400');
		header('Content-Encoding: utf-8');
		header("Content-Disposition: attachment; filename=\"{$filename}\"");
		header("Content-type: application/vnd.ms-excel");
		header("Content-Transfer-Encoding: binary");
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $inputFileType);
		$objWriter->save('php://output');
	}
	
	//获取模板标签及数据
	public function get_export_label($id,$name){
		$model = D($name);
		$map["id"] = $id;
		$list = $model->where($map)->find();
		$list = $this->getReportData($list,$name);
		$tabModel = M("mis_system_templete_label");
		$newTab = $tabModel->where(array("modelname"=>$name))->select();
		if( ! empty($newTab)){
			foreach($newTab as $k => $v){
				preg_match_all('/\[\w+\]/', $v["formula"], $matches);
				if(!empty($matches[0]) && mb_strpos($v["formula"], '###')!==0){
					foreach($matches[0] as $kk => $vv){
						$vv = str_replace(array("[","]"),"",$vv);
						if( ! isset($list[$vv]["original"])){
							$list[$vv]["original"] = "";
						}
						if(isset($list[$vv]["name"])){
							$newTab[$k]["formula"] = str_replace("[".$vv."]",$list[$vv]["original"],$newTab[$k]["formula"]);
						}
					}
				}elseif(mb_strpos($v["formula"], '###')===0){
					$table = json_decode((str_replace("###","",$v["formula"])),true);
					if(is_array($table)){
						$list[$v["name"]]["showtype"] = $table["showtype"];
						$list[$v["name"]]["showtitle"] = $table["showtitle"];
						$list[$v["name"]]["ziti"] = $v["ziti"];
						$list[$v["name"]]["zihao"] = $v["zihao"];
						$list[$v["name"]]["hangjianju"] = $v["hangjianju"];
						$list[$v["name"]]["fieldwidth"] = json_decode($v["fieldwidth"],true);
						if( ! empty($v["showfield"])){//内嵌表显示字段
							$showfield = json_decode($v["showfield"],true);
							if(isset($list[$v["name"]]) && is_array($showfield)){
								foreach($list[$v["name"]]["value"] as $kkk => $vvv){
									if($showfield[$vvv["name"]]!=="1"){
										unset($list[$v["name"]]["value"][$kkk]);
									}
								}
								if(!empty($list[$v["name"]]["value"])){//对数组键值重排序
									$newArray = array();
									foreach($list[$v["name"]]["value"] as $kkk => $vvv){
										$newArray[] = $list[$v["name"]]["value"][$kkk];
									}
									$list[$v["name"]]["value"] = $newArray;
								}
							}
						}
						if( ! empty($table["datatable"])){
							foreach($table["datatable"] as $kk => $vv){
								if(isset($list[$v["name"]])){
									if(empty($v["colORrow"])){
										if(isset($list[$v["name"]]["value"])){
											foreach($list[$v["name"]]["value"] as $kkk => $vvv){
												$value1 = $this->export_formula_replace($vvv["value"],$vv["formula"]);
												if( ! empty($value)){
													$list[$v["name"]]["value"][$kkk]["value"][] = $value1;
												}
											}
										}
									}else{
										if(isset($list[$v["name"]]["original"])){
											$value2 = $this->export_formula_replace($list[$v["name"]]["original"],$vv["formula"]);
											if( ! empty($value2)){
												$list[$v["name"]]["value"][] = $value2;
												
											}
										}
									}
								}
							}
						}
						$newTab[$k]["value"] = $list[$v["name"]]["value"];
					}
				}
				
				$newTab[$k]["formula"] = html_entity_decode($newTab[$k]["formula"]);
				@eval('$newTab[$k]["value"]='.$newTab[$k]["formula"].";");
				if(empty($newTab[$k]["value"]) &&  ! empty($newTab[$k]["defaultnull"])){
					$newTab[$k]["value"] = $newTab[$k]["defaultnull"];
				}
				if($v["colORrow"]){
					$list[$v["name"]] = array(
							"name"=>$v["name"],
							"showname"=>$v["showname"],
							"showtype" => $list[$v["name"]]["showtype"],
							"showtitle" => $list[$v["name"]]["showtitle"],
							"value"=>$newTab[$k]["value"],
							"original"=>$newTab[$k]["value"],
							"is_datatable"=>1,
							"colORrow"=>$v["colORrow"]
					);
				}else{
					$list[$v["name"]] = array(
							"name"=>$v["name"],
							"is_datatable"=>1,
							"showtype" => $list[$v["name"]]["showtype"],
							"showtitle" => $list[$v["name"]]["showtitle"],
							"showname"=>$v["showname"],
							"ziti"=>$list[$v["name"]]["ziti"],
							"zihao"=>$list[$v["name"]]["zihao"],
							"hangjianju"=>$list[$v["name"]]["hangjianju"],
							"fieldwidth"=>$list[$v["name"]]["fieldwidth"],
							"original"=>$newTab[$k]["value"],
							"value"=>$newTab[$k]["value"],
					);
				}
			}
		}
		return $list;
	}
	
	/*
	* @author  王昭侠
	* @date:2015-02-06 11:21:00
	* @describe: 公式替换
	*/
	public function export_formula_replace($data,$formula){
		$value = "";
		if(!empty($data) && is_array($data)){
			preg_match_all('/\[\w+\]/', $formula, $matches);
			if($matches[0]){
				foreach($matches[0] as $kk => $vv){
					$vv = str_replace(array("[","]"),"",$vv);
					$formula = str_replace("[".$vv."]",$data[$vv],$formula);
				}
			}
			$formula = html_entity_decode($formula);
			@eval('$value='.$formula.";");
		}
		return $value;
	}
	
	
	public function lookupWordChoiceextend(){
		$map['id'] = $_REQUEST['fileid'];
		$templateModel = M("mis_system_template_saveword");
		$rs = $templateModel->where($map)->find();	
		$filenameUTF8 = preg_replace("/^([\s\S]+)\/Public\//", "", $rs['fileurl']);
		$filenameUTF8=PUBLIC_PATH.$filenameUTF8;
		header("Cache-Control: public");
		header("Content-Type: application/force-download");
		header("Content-Disposition: attachment; filename=".basename($filenameUTF8));
		header("Content-Transfer-Encoding:­ binary");
		header("Content-Length: " . filesize($filenameUTF8));
		readfile($filenameUTF8);
	}
	//获取审批意见word表格
	public function get_sp_word_str($data){
		import('@.ORG.PHPWord', '', $ext='.php');
		$PHPWord = new PHPWord();
		$text = "";
		$objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
		foreach($data as $k => $v){
			$section = $PHPWord->createSection();
			$str = $section->addText($v["jiedianmingchen"]);
			$text .= $objWriter->getWriterPart('document')->getObjectAsText($str);
			$str = $section->addText("        ".$v["shenheyijian"]);
			$text .= $objWriter->getWriterPart('document')->getObjectAsText($str);
			$str = $section->addText("                                                                                                                    审核时间：".$v["shenheshijian"]);
			$text .= $objWriter->getWriterPart('document')->getObjectAsText($str);
			$str = $section->addText("                                                                                                                    审核人：".$v["shenheren"]);
			$text .= $objWriter->getWriterPart('document')->getObjectAsText($str);
		}
		return $text;
	}
 
	
	/**
	 * @Title: export_Pdf_one 
	 * @Description: todo(到处PDF方法)   
	 * @author 王昭侠
	 * @date 2015-6-29 下午5:02:39 
	 * @throws
	 */
	public function export_Pdf_one(){
		$id = $_REQUEST['id'];
		$name = $this->getActionName();
		$list = $this->get_export_label($id,$name);
		import('@.ORG.PHPWord', '', $ext='.php');
		$template_word = UPLOAD_SampleWord.$name.".docx";
		$causepath = UPLOAD_SampleWord.$name;
		$filetype =end(explode(".",$template_word));
		if($filetype=="doc"){
			$inputFileType = 'Word2005';
		}else if( $filetype=="docx"){
			$inputFileType = 'Word2007';
		}
		$tabModel = M("mis_system_templete_label");
		
		$templateModel = D("MisSystemTempleteManage");
		$template["modelname"] = $name;
		$template["zhuangtai"] = 1;
		$templatelist = $templateModel->where($template)->find();
		$filename = time();
		if( ! empty($templatelist)){
			$attachedRecord = $this->getAttachedRecordList($templatelist["id"],true,true,"MisSystemTempleteManage",0,false);
			$filename = $templatelist["name"];
			if($attachedRecord){
				foreach($attachedRecord as $k => $v){
					$file = pathinfo(UPLOAD_PATH.$v["attached"]);
					if(strtolower($file["extension"])=="docx"){
						$template_word = UPLOAD_PATH.$v["attached"];
					}
				}
			}
		}
		if (!file_exists ($template_word) || empty($templatelist)) { //模板文件不存在
			$PHPWord = new PHPWord();
			$section = $PHPWord->createSection();
			$section->addText("温馨提示:模板文件不存在,请在模板管理里先上传一个模板",array('color'=>'FF0000', 'size'=>18, 'bold'=>true));
			$section->addTextBreak();
			$section->addTextBreak();
			//设置表头
			foreach($list as $k => $v){
				if(!$v["is_datatable"]){
					$text = $v["showname"]." : "."\${".$v["name"]."}";
					$section->addText($text);
				}else{
					if( ! isset($v["colORrow"])){
						$section->addTextBreak();
						$section->addTextBreak();
						$section->addText($v["showname"].":");
						$section->addText("\${".$v["name"]."}");
					}
				}
			}
				
			$objWriter = PHPWord_IOFactory::createWriter($PHPWord, $inputFileType);
			$objWriter->save($template_word);
		}
		$this->repStrPHPword($template_word, $causepath, $list, $filename, "pdf");
	}
	
	/*
	* @author  王昭侠
	* @date:2014-12-13 18:25:00
	* @describe: 导出Pdf
	*/
	public function export_Pdf_one1(){
		$id = $_REQUEST['id'];
		$name = $this->getActionName();
		$list = $this->get_export_label($id,$name);
	
		$template_htm = UPLOAD_SampleWord.$name.".htm";
		$causepath = UPLOAD_SampleWord.$name;
		
		$templateModel = D("MisSystemTempleteManage");
		$template["modelname"] = $name;
		$template["zhuangtai"] = 1;
		$templatelist = $templateModel->where($template)->find();
		$filename = time();
		if( ! empty($templatelist)){
			$attachedRecord = $this->getAttachedRecordList($templatelist["id"],true,true,"MisSystemTempleteManage",0,false);
// 			$filename = $templatelist["name"];
			if($attachedRecord){
				foreach($attachedRecord as $k => $v){
					$file = pathinfo(UPLOAD_PATH.$v["attached"]);
					if(strtolower($file["extension"])=="htm" || strtolower($file["extension"])=="html"){
						$template_htm = UPLOAD_PATH.$v["attached"];
					}
				}
			}
		}
		if(!file_exists($template_htm)) { //模板文件存在
			$text = "";
			$text .="<h3 style='color:#FF0000;'>温馨提示:模板文件不存在,请在模板管理里先上传一个模板</h3>";
			//设置表头
			foreach($list as $k => $v){
				$text .= $v["showname"]." : "."\${".$v["name"]."}<br>";
			}
			if (!$handle = fopen($template_htm, 'w')) {
				echo "不能打开文件";
			}
			if (fwrite($handle, $text) === FALSE) {
				echo "不能写入到文件";
			}
			fclose($handle);
		}
		
		if(file_exists($template_htm)){
			if ( !file_exists($causepath) ) {
				$this->createFolders($causepath); //判断目标文件夹是否存在
			}
			$pdfFileSaveName = $filename.'.pdf';
			$pdfFileSaveName=iconv("UTF-8","",$pdfFileSaveName);
			$html = file_get_contents($template_htm);
			$html = iconv("UTF-8","GBK",$html);
			foreach($list as $k => $v)
			{
				if(!is_array($v["value"])){
					$html = $this->setHtmlValue($k,$v["value"],$html,true);
				}
			}
			$this->write_pdf($html,$pdfFileSaveName);
		}else{
			echo "模板文件不存在";
		}
	}
	
	/*
	* @author  王昭侠
	* @date:2014-12-18 13:55:00
	* @describe: 导出Pdf方式
	*/
	public function write_pdf($content, $filename = NULL, $flag = "D")
	{
		import('@.ORG.pdf', '', $ext='.php');
		$query = FALSE;
		$temp = array();
		$content=iconv("GBK","UTF-8",$content);
		if( ! empty($content))
		{
			// 参数 I：浏览器输出 D：下载
			$flag = ( ! empty($flag)) ? $flag : 'I';
	
			if( ! empty($filename))
			{
				$temp = pathinfo($filename);
	
				if( ! empty($temp['dirname']) && ! is_dir($temp['dirname']))
				{
					mkdir($temp['dirname'], 0777, TRUE);
				}
			}
			
			$pdf = new pdf(array('content' => $content, 'filename' => $filename, 'flag' => $flag));
			$query = TRUE;
		}
		unset($temp);
		return $query;
	}

	/*
	* @author  renlin
	* @date:2014-12-14 9:45:00
	* @describe: 动态表单生成树查询数据源
	* @return:处理后的html
	*/
	public function getDateSoure($name){
		$manmagename=$name?$name:$this->getActionName();
		$MisDynamicDatabaseSubModel=D("MisDynamicDatabaseSub");
		//组件属性
		$MisDynamicFormProperyModel=D("MisDynamicFormPropery");
// 		echo "==".$manmagename;
		//获取表单id
		$formid=getFieldBy($manmagename, "actionname", "id", "mis_dynamic_form_manage");
		//获取当前表单id
		$tpltype=getFieldBy($formid, "id", "tpl", "mis_dynamic_form_manage");
		//查询当前表单数据源
		$map=array();
		$map['formid']=$formid;
		$map['isdatasouce']=1;
		//查询字段id
		$MisDynamicDatabaseSubVo=$MisDynamicDatabaseSubModel->where($map)->find();
		//查询数据源字段属性
		$confVo=$MisDynamicFormProperyModel->where("status=1 and ids=".$MisDynamicDatabaseSubVo['id'])->find();
		$suroce=$confVo['fieldname'];
		$list=array();
		//数据源分析与获取
		$MisSaleClientTypeList=$this->getSelectSource($confVo,$suroce,$tpltype);
		
		$list['list']=$MisSaleClientTypeList;
		$list['field']=$suroce;
		$list['fielter']=$skey;
		if($name){
			return $list;
		}else{
			$this->assign("MisSaleClientTypeList",$MisSaleClientTypeList);
			$this->assign("suroce",$suroce);
		}
	}
	
	
	
	
	protected function getSelectSource($confVo,$suroce,$tpltype){
		if($confVo){
			$name=$this->getActionName();
			// 用户指定的枚举
			if($confVo['showoption']){
				//选择select配置文件
				$selectList = require ROOT . '/Dynamicconf/System/selectlist.inc.php';
				//取得配置的数据表
				$MisSaleClientTypeList=$selectList[$confVo['showoption']][$confVo['showoption']];
					
			}else{
				$skey="";
				$ekey="";
				// 用户指定的表数据来源
				if($confVo['subimporttableobj']){
					$maintable=D($name)->getTableName();
					//查询当前数据源是否为本表数据
					if($confVo['subimporttableobj']==$maintable){
						//此处为后期生成树形准备
						
					}else{
						//非树形展示
						$MisSaleClientTypeDao=M($confVo['subimporttableobj']);
						$skey=$confVo['subimporttablefield2obj'];
						$ekey=$confVo['subimporttablefieldobj'];
					}
				}else{
					// 树形展示
					$MisSaleClientTypeDao=M($confVo['treedtable']);
					$skey=$confVo['treevaluefield'];
					$ekey=$confVo['treeshowfield'];
					//查询数据
					$MisSaleClientTypeList=$MisSaleClientTypeDao->where("status=1")->select();
					
					foreach ($MisSaleClientTypeList as $key=>$val){
						//生成树形节点
						$typeTree[]=array(
								'id'=>$val['id'],
								'name'=>$val[$confVo['treeshowfield']],//显示名称
								'title'=>$val[$confVo['treeshowfield']],//显示名称
								'ename'=>$name, //model名称
								'pId'=>$val[$confVo['treeparentfield']],
								'url'=>"__URL__/index/jump/jump/fieldtype/{$suroce}/{$suroce}/{$val[$confVo['treevaluefield']]}/id/".$val['id']."/aname/".$name,
								'target'=>'ajax',
								'rel'=>$name."indexview",
								'open'=>true
						);
					}
					$this->assign("typeTree",json_encode($typeTree));
				}
				if($tpltype=="basisarchivestpl#ltrl"||$tpltype=="basisarchivestpl#ltrc"){
					$MisSaleClientTypeList=$MisSaleClientTypeDao->where("status=1")->select();
					
				}else{
					//update  liminggang 20150511
					$where = array();
					$where['status'] = 1;
					if($confVo['lookupconditions']){
						$where['_string'] = $confVo['lookupconditions'];
					}
					//end
					$MisSaleClientTypeList=$MisSaleClientTypeDao->where($where)->getField($skey.",".$ekey);
				}
			}
			return $MisSaleClientTypeList;
		}else{
			return false;
		}
	}
	/*
	 * @author  王昭侠
	* @date:2014-12-23 16:45:00
	* @describe: datatable数据保存
	*/
	public function datatablesave(){
		$name = $this->getActionName();
		$model = D($name);
		$table = "";
		$fieldtype="";
		if($_GET['fieldtype']){
			$fieldtype=$_GET['fieldtype'];
		}else{
			$fieldtype="bindid";
		}
		
		$relationmodelname=$_GET['bindaname'];
		if($model && !empty($_POST['datatable'])){
			$updateData = array();// 数据修改缓存集合
			if($_POST['datatable']){
				foreach($_POST['datatable'] as $key=>$val){
					foreach($val as $k=>$v){
						$table[] = $k;
						$v['relationmodelname']=$relationmodelname;
						if(empty($v["id"])){
							if(isset($v["id"]))unset($v["id"]);
							$v[$fieldtype] = $_GET[$fieldtype];
							$insertData[]=$v;
							unset($v[$fieldtype]);
							
						}else{
							$updateData[]=$v;
						}
					}
				}
			}
			$query = true;
			if($insertData){
				$this->checkOrdernoDatatable($name,$insertData[0]['orderno'],0);
				
				$re_data = array();
				C('TOKEN_ON',false);
				$uploadfile = array();
				foreach($insertData as $k=>$v){
					$re_data[$k]["table"] = $table[$k];
					$re_data[$k]["index"] = $k+1;
					$d = array();
					$d = $model->create($v);
					 
					$ret = $model->add($d);
					if(false === $ret){
					    $this->error('保存失败'.$model->getDBError());
					}
					$re_data[$k]["id"] = $ret;
// 					echo $model->getLastSql();
// 					echo $model->getDbError();
					foreach($v as $kk => $vv){
						if(is_array($vv)){
							$uploadfile[$kk]["file"] = $vv;
							$uploadfile[$kk]["tableid"] = $re_data[$k]["id"];
							$uploadfile[$kk]["subid"] = 0;
							$uploadfile[$kk]["tablename"] = $name;
							$uploadfile[$kk]["fieldname"] = $kk;
						}
					}
					if($uploadfile){
						$this->DT_swf_upload($uploadfile);
					}
				}
				
				C('TOKEN_ON',true);
				if(empty($re_data)){
					$query = false;
				}
			}
			
			if($updateData){
				C('TOKEN_ON',false);
				$uploadfile = array();
				foreach($updateData as $k=>$v){
					$d = array();
					$d = $model->create($v);
					$model->save($d);
					foreach($v as $kk => $vv){
						if(is_array($vv)){
							$uploadfile[$kk]["file"] = $vv;
							$uploadfile[$kk]["tableid"] = $v["id"];
							$uploadfile[$kk]["subid"] = 0;
							$uploadfile[$kk]["tablename"] = $name;
							$uploadfile[$kk]["fieldname"] = $kk;
						}
					}
					if($uploadfile){
						$this->DT_swf_upload($uploadfile);
					}
				}
				C('TOKEN_ON',true);
			}
			
			if($query){
				$this->success('保存成功',true,$re_data);
			}else{
				$this->error('保存失败');
			}
		}
	}
	
	/*
	* @author  王昭侠
	* @date:2015-1-6 16:25:00
	* @describe: datatable数据删除
	*/
	public function datatabledel(){
		$name = $this->getActionName();
		$model = D($name);
		$id = intval($_POST['id']);
		$map['id'] = array('eq',$id);
		$model->where($map)->delete();
		$this->success('数据成功删除');
	}

	/**
	 * @Title: getbatchSql
	 * @Description: todo(生成数据库批量新增或者批量更新语句)
	 * @param string $tableName    表名
	 * @param array  $titleList    表头
	 * @param array  $data    　　 要插入或更新的数据内容
	 * @param string $str    　　　分隔字符
	 * @author wangzhaoxia
	 * @date 2014-11-1
	 * @throws
	 */
	public function getbatchSql($tableName,$titleList,$data,$str="\n"){
		$sql_add_i=0;
		$sql_add = $str." INSERT INTO `".$tableName."` (`".implode("`,`", $titleList)."`) VALUES ";
		$index = 1;
		$sql_update_id=array();
		foreach($data as $k=>$v){
			if(empty($v["id"]))
			{
				if ($index % 1000 == 0) {
					$index = 1;
					$sql_add .= $str." INSERT INTO `".$tableName."` (`".implode("`,`", $titleList)."`) VALUES ";
				}
				if($sql_add_i>0&&$index>1) $sql_add.=",";
				$index++;
				$d_values = array();
				foreach($v as $kk=>$vv){
					$d_values[] = $vv;
				}
				$sql_add.="('".implode("','", $d_values)."')";
				$sql_add_i++;
			}
			else
			{
				$sql_update_id[$k]=$v["id"];
			}
		}
	
		foreach($data as $k=>$v){
			foreach($v as $kk=>$vv){
				if(empty($vv)&&isset($data[$k]["id"])) unset($data[$k][$kk]);
			}
		}
		$sql_update = $str." UPDATE `".$tableName."` SET ";
		foreach($data as $k=>$v){
			if(!empty($v["id"]))
			{
				$up_case_arr = array();
				foreach($v as $kk=>$vv){
					if($kk!="id")
					{
						$up_str = "`".$kk."` = CASE id";
						foreach($sql_update_id as $kkk=>$vvv){
							$up_str .= " WHEN ".$vvv." THEN '".$data[$kkk][$kk]."'";
						}
						$up_str .=" END";
						$up_case_arr[] = $up_str;
					}
				}
			}
		}
		$sql_update .=implode(",", $up_case_arr)." WHERE id IN (".implode(",", $sql_update_id).")";
		$sql = "";
		if($sql_add_i>0)
		{
			$sql .= $sql_add;
		}
		if(count($sql_update_id)>0)
		{
			$sql .= $sql_update;
		}
		return $sql;
	}
	
	/**
	 * @Title: getReportData
	 * @Description: todo(获取报告的所有数据公用方法)
	 * @param 主表数组 $volist
	 * @param 主表名称 $name
	 * @param 引用型返回数据 $totalArr
	 * @author 黎明刚
	 * @date 2015年1月14日 下午2:59:13
	 * @throws
	 */
	public function getReportData($volist,$name,$bool) {
		//定义存储变量 (表单数据)
		$totalArr = array();
		$a = array();
		//存储变量，内嵌数据
		$neiqiantotalArr = array(); 
		// 实例化mis_dynamic_form_manage
		$mis_dynamic_form_manageDao = M ( "mis_dynamic_form_manage" );
		// 获取list.inc.php配置文件内容的模型
		$scdmodel = D ( 'SystemConfigDetail' );
		// 实例化动态建模数据匹配表
		$mis_dynamic_form_properyModel = M ( "mis_dynamic_form_propery" );
	
		// 获取配置文件list.inc.php,方便下面数据进行转换
		$detailList = $scdmodel->getDetail ( $name, false );
		
		$showname = getFieldBy($name, 'name', 'title', 'node');
		//子表数据采集开始
		if($volist ['id']){
			//增加主表子表功能word版本  这个是以前缺失的部分
			$mis_dynamic_database_masDao = M("mis_dynamic_database_mas");
			$where = array();
			$where['modelname'] = $name;
			$where['isprimary'] = array('exp',"is null");
			//查询相关字表数据
			$sonvolist = $mis_dynamic_database_masDao->field("tablename")->where($where)->select();
			if($sonvolist){
				foreach ($sonvolist as $tkey=>$tval){
					//查询字表数据
					$sondata = M($tval['tablename'])->field("id",true)->where('masid ='.$volist ['id'])->find();
					if($sondata && $volist){
						$volist = array_merge($volist,$sondata);
					}
				}
			}
		}
		//字表数据获取完毕
		
		// 定义配置文件转换后的数组变量
		$totalArr = $this->getDqData ( $detailList, $volist);
		//数据标签数据结构
		if($bool){
			$a[] = array('title'=>$showname."__主表字段",
					'type'=>1,
				    'value'=>$totalArr
			);
		}
		// --------------验证一、是否存在内嵌表格-----------------//
		//查询内嵌表数据
		$mis_dynamic_form_datatable_sort = M("mis_dynamic_form_datatable_sort");
		$where = array();
		$where['modelname'] = $name;
		$neiqianlist = $mis_dynamic_form_datatable_sort->where($where)->select();
		
		foreach($neiqianlist as $key=>$val){
			// 实例化内嵌表
			$datatablename = D ( $val['tablename'] );
			// 获取内嵌表的配置文件
			$neiqdetailList = $scdmodel->getEmbedDetail ( $name, $val['tablename']);
			$innerTabelObjdatatable3Data = array();
			if($volist ['id']){
				$where = array ();
				$where ['masid'] = $volist ['id'];
				//内嵌表的数据
				$innerTabelObjdatatable3Data = $datatablename->where ( $where )->select ();
			}
			//定义配置文件转换后的数组变量
			$fieldname = "datatablebdnq".$name.$val['sort'];
			$neiqiandata= $this->getDetaileZhList($neiqdetailList,$innerTabelObjdatatable3Data,$fieldname,$val['title']);
			if($bool){
				// 获取表名称
				$a[] = array('title'=>$showname."内嵌表【".$val['title']."】",
						'type'=>2,
						'value'=>$neiqiandata
				);
			}
			//合并两个数组
			$totalArr = array_merge($totalArr,$neiqiandata);
		}
		//-----------------------内嵌表格结束-----------------------//
		
		//--------------验证二、是否存在组合表单-----------------//
		$sql = "SELECT dataroamid,bindtype,inbindmap,bindconlistArr,bindval,bindresult,inbindaname,title FROM `mis_auto_bind` LEFT JOIN node ON mis_auto_bind.`inbindaname` = node.`name`
			WHERE mis_auto_bind.`bindaname`= '".$name."' AND mis_auto_bind.`typeid` = 0 AND mis_auto_bind.`status`=1 GROUP BY inbindaname
			ORDER BY inbindsort ASC";
		$model = M();
		$bingdList = $model->query($sql);
		//实例化漫游表
		$MisSystemDataRoamingModel=D("MisSystemDataRoaming");
		
		foreach($bingdList as $bkey=>$bval){
			//被绑模型名
			$inbandaname = $bval['inbindaname'];
			//实例化模型对象
			$inbindaModel = D($inbandaname);
			//显示节点名称
			$showname = $bval['title'];
			if($volist['id']){
				$map = array();
				$map['status'] = 1;
				//插入漫游数据进行查询 如果目标数据为空不单独处理
				//$dataArr=$MisSystemDataRoamingModel->main(2,$name,$volist['id'],4,$inbandaname,'',$bval['dataroamid']);
				//if(is_array(reset($dataArr))){
					//foreach(reset($dataArr) as $targettable=>$data){
						//foreach($data as $k => $v){
							//$map[key($v)]=reset($v);
						//}
					//}
				//}
				/*
				 * 按照任玲绑定表关系，重新构建map条件获取绑定表查询条件
				 */
				if($bval['inbindmap']){
					$newconditions = str_replace ( array ( '&quot;', '&#39;', '&lt;','&gt;'), array ('"',"'",'<','>'), $bval['inbindmap'] );
					$map['_string']=$newconditions;
				}
				$bindconlistArr=unserialize($bval['bindconlistArr']);
				//获取表单子表附加
				foreach ($bindconlistArr as $bindkey=>$bindval){
					//如果有值才生成查询
					if($volist[$bindkey]){
						$map[$bindval]=$volist[$bindkey];
					}
				}
			}
			//第一步、判断是表单还是列表  bindtype = 0  表单
			if($bval['bindtype']==0){
				if($volist['id']){
					//以主表的数据orderno查询组合的表单数据
					$bindList = $inbindaModel->where($map)->find();
				}
				//获取配置文件list.inc.php,方便下面数据进行转换
				$detailList = array();
				$detailList = $scdmodel->getDetail($inbandaname,false);
				//定义配置文件转换后的数组变量
				$inbindanamedata=array();
				$inbindanamedata = $this->getDqData($detailList,$bindList,'zh'.$inbandaname);
				//数据标签数据结构
				if($bool){
					$a[] = array(
							'title'=>$showname."__主表字段",
							'type'=>1,
							'value'=>$inbindanamedata
					);
				}else{
					//合并两个数组
					$totalArr = array_merge($totalArr,$inbindanamedata);
				}
				
				$where = array();
				$where['modelname'] = $inbandaname;
				$neiqianlist = $mis_dynamic_form_datatable_sort->where($where)->select();
				
				foreach($neiqianlist as $k3=>$v3){
					// 实例化内嵌表
					$datatablename = D ( $v3['tablename'] );
					// 获取内嵌表的配置文件
					$neiqdetailList = $scdmodel->getEmbedDetail ( $inbandaname, $v3['tablename']);
					$innerTabelObjdatatable3Data = array();
					if($bindList['id']){
						$where = array ();
						$where ['masid'] = $bindList['id'];
						//内嵌表的数据
						$innerTabelObjdatatable3Data = $datatablename->where ( $where )->select ();
					}
					//定义配置文件转换后的数组变量
					$fieldname = "datatablezhnq".$inbandaname.$v3['sort'];
					$neiqiandata= $this->getDetaileZhList($neiqdetailList,$innerTabelObjdatatable3Data,$fieldname,$v3['title']);
					if($bool){
						// 获取表名称
						$a[] = array('title'=>$showname."内嵌表【".$v3['title']."】",
								'type'=>2,
								'value'=>$neiqiandata
						);
					}
					//合并两个数组
					$totalArr = array_merge($totalArr,$neiqiandata);
				}
			}else{
				if($volist['id'] && $map){
					//列表行数据
					$bindList = $inbindaModel->where($map)->select();
				}
				$fieldname = "datatablezh".$inbandaname;
				//获取配置文件list.inc.php,方便下面数据进行转换
				$detailList = array();
				$detailList = $scdmodel->getDetail($inbandaname,false);
				//定义根据配置文件转换后的数组变量
				$zhListdata=array();
				$zhListdata = $this->getDetaileZhList($detailList,$bindList,$fieldname,$showname);
				
				if($bool){
					$a[] = array('title'=>$showname.'_列表型标签',
							'type'=>2,
							'value'=>$zhListdata
					);
				}else{
					//合并两个数组
					$totalArr = array_merge($totalArr,$zhListdata);
				}
				
				
				$where = array();
				$where['modelname'] = $inbandaname;
				$neiqianlist = $mis_dynamic_form_datatable_sort->where($where)->select();
				
				foreach($neiqianlist as $k3=>$v3){
					// 实例化内嵌表
					$datatablename = D ( $v3['tablename'] );
					// 获取内嵌表的配置文件
					$neiqdetailList = $scdmodel->getEmbedDetail ( $inbandaname, $v3['tablename']);
					$innerTabelObjdatatable3Data = array();
					//循环数据列表
					if($bindList){
						foreach($bindList as $bdkey=>$bdval){
							$where = array ();
							$where ['masid'] = $bdval ['id'];
							//内嵌表的数据
							$innerTabelObjdatatable3Data = $datatablename->where ( $where )->select ();
							if($innerTabelObjdatatable3Data){
								break;
							}
						}
					}
					//定义配置文件转换后的数组变量
					$fieldname = "datatablezhnq".$v3['tablename'].$v3['sort'];
					$neiqiandata= $this->getDetaileZhList($neiqdetailList,$innerTabelObjdatatable3Data,$fieldname,$v3['title']);
					if($bool){
						// 获取表名称
						$a[] = array('title'=>$showname."内嵌表【".$v3['title']."】",
								'type'=>2,
								'value'=>$neiqiandata
						);
					}
					//合并两个数组
					$totalArr = array_merge($totalArr,$neiqiandata);
				}
			}
		}
		//--------------验证三、是否存在套表-----------------//
		$sql = "SELECT dataroamid,bindtype,inbindval,bindval,bindresult,inbindaname,title FROM `mis_auto_bind` LEFT JOIN node ON mis_auto_bind.`inbindaname` = node.`name`
			WHERE mis_auto_bind.`bindaname`= '".$name."' AND mis_auto_bind.`typeid` = 2 AND mis_auto_bind.`status`=1 GROUP BY inbindaname
			ORDER BY inbindsort ASC";
		$bingdsetList = $model->query($sql);
		if(count($bingdsetList)>0){
			//存在套表数据，则进行数据获取
			foreach($bingdsetList as $setkey=>$setval){
				//套表被绑定模型
				$tbinbindaname = $setval['inbindaname'];
				//实例化模型对象
				$inbindaModel = D($setval['inbindaname']);
				$showname = $setval['title'];
				$map = array();
				$map['status'] = 1;
				$map[$setval['inbindval']] = $volist[$setval['bindval']];
				//第一步、判断是表单还是列表
				if($setval['bindtype']==0){//表单
					//以主表的数据orderno查询组合的表单数据
					$bindList = array();
					if($volist){
						$bindList = $inbindaModel->where($map)->find();
					}
					//获取配置文件list.inc.php,方便下面数据进行转换
					$detailList = array();
					$detailList = $scdmodel->getDetail($tbinbindaname,false);
					//定义配置文件转换后的数组变量
					$inbindanamedata=array();
					$inbindanamedata = $this->getDqData($detailList,$bindList,'tb'.$tbinbindaname);
					
					//数据标签数据结构
					if($bool){
						$a[] = array(
								'title'=>$showname."_表字段",
								'type'=>1,
								'value'=>$inbindanamedata
						);
					}else{
						//合并两个数组
						$totalArr = array_merge($totalArr,$inbindanamedata);
					}
					//验证内嵌表
					$where = array();
					$where['modelname'] = $tbinbindaname;
					$neiqianlist = $mis_dynamic_form_datatable_sort->where($where)->select();
					
					foreach($neiqianlist as $k3=>$v3){
						// 实例化内嵌表
						$datatablename = D ( $v3['tablename'] );
						// 获取内嵌表的配置文件
						$neiqdetailList = $scdmodel->getEmbedDetail ( $tbinbindaname, $v3['tablename']);
						$innerTabelObjdatatable3Data = array();
						if($bindList ['id']){
							$where = array ();
							$where ['masid'] = $bindList ['id'];
							//内嵌表的数据
							$innerTabelObjdatatable3Data = $datatablename->where ( $where )->select ();
						}
						//定义配置文件转换后的数组变量
						$fieldname = "datatabletbnq".$v3['tablename'].$v3['sort'];
						$neiqiandata= $this->getDetaileZhList($neiqdetailList,$innerTabelObjdatatable3Data,$fieldname,$v3['title']);
						if($bool){
							// 获取表名称
							$a[] = array('title'=>$showname."内嵌表【".$v3['title']."】",
									'type'=>2,
									'value'=>$neiqiandata
							);
						}
						//合并两个数组
						$totalArr = array_merge($totalArr,$neiqiandata);
					}
				}else{
					//列表
					$bindList = array();
					if($volist){
						$bindList = $inbindaModel->where($map)->select();
					}
					$extend = $setval['inbindaname'];
					//获取配置文件list.inc.php,方便下面数据进行转换
					$detailList = array();
					$detailList = $scdmodel->getDetail($tbinbindaname,false);
					$fieldname = "datatabletb".$tbinbindaname;
					//定义根据配置文件转换后的数组变量
					$zhListdata=array();
					$zhListdata = $this->getDetaileZhList($detailList,$bindList,$fieldname,$showname);
					
					if($bool){
						$a[] = array('title'=>$showname.'_套表标签',
								'type'=>3,
								'value'=>$zhListdata
						);
					}else{
						//合并两个数组
						$totalArr = array_merge($totalArr,$zhListdata);
					}
					
					//处理内嵌表
					$where = array();
					$where['modelname'] = $tbinbindaname;
					$neiqianlist = $mis_dynamic_form_datatable_sort->where($where)->select();
					
					foreach($neiqianlist as $k3=>$v3){
						// 实例化内嵌表
						$datatablename = D ( $v3['tablename'] );
						// 获取内嵌表的配置文件
						$neiqdetailList = $scdmodel->getEmbedDetail ( $tbinbindaname, $v3['tablename']);
						$innerTabelObjdatatable3Data = array();
						//循环数据列表
						if($bindList){
							foreach($bindList as $bdkey=>$bdval){
								$where = array ();
								$where ['masid'] = $bdval ['id'];
								//内嵌表的数据
								$innerTabelObjdatatable3Data = $datatablename->where ( $where )->select ();
								if($innerTabelObjdatatable3Data){
									break;
								}
							}
						}
						//定义配置文件转换后的数组变量
						$fieldname = "datatabletbnq".$v3['tablename'].$v3['sort'];
						$neiqiandata= $this->getDetaileZhList($neiqdetailList,$innerTabelObjdatatable3Data,$fieldname,$v3['title']);
						if($bool){
							// 获取表名称
							$a[] = array('title'=>$showname."内嵌表【".$v3['title']."】",
									'type'=>2,
									'value'=>$neiqiandata
							);
						}
						//合并两个数组
						$totalArr = array_merge($totalArr,$neiqiandata);
					}
				}
			}
		}
		if($bool){
			return $a;
		}else{
			return $totalArr;
		}
	}
	/**
	 * @Title: getNeiQianVal
	 * @Description: todo(获取报告的所有数据公用方法)
	 * @param 主表数组 $volist
	 * @param 主表名称 $name
	 * @param 引用型返回数据 $totalArr
	 * @author 黎明刚
	 * @date 2015年1月14日 下午2:59:13
	 * @throws
	 */
	public function getNeiQianVal($volist,$name,$bool) {
		$bqArr = array();
		//定义存储变量 (表单数据)
		$totalArr = array();
		//存储变量，内嵌数据
		$neiqiantotalArr = array();
		// 实例化mis_dynamic_form_manage
		$mis_dynamic_form_manageDao = M ( "mis_dynamic_form_manage" );
		// 获取list.inc.php配置文件内容的模型
		$scdmodel = D ( 'SystemConfigDetail' );
		// 实例化动态建模数据匹配表
		$mis_dynamic_form_properyModel = M ( "mis_dynamic_form_propery" );
	
		// 获取配置文件list.inc.php,方便下面数据进行转换
		$detailList = $scdmodel->getDetail ( $name, false );
		$fieldname = "datatablezhnq".$name;
		//获取显示名称
		$showname = getFieldBy($name, 'name', 'title', 'node');
		// 定义配置文件转换后的数组变量
		$neiqiantotalArr = $this->getDetaileZhList ( $detailList, $volist,$fieldname,$showname);
		if($bool){
			//标签管理才进入此判断
				
			//分开组合表单，内嵌，以及内嵌字段
			if($neiqiantotalArr){
				$nqfieldArr = array();
				foreach($neiqiantotalArr as $n2key=>$n2val){
					foreach($n2val['value'] as $n3key=>$n3val){
						$fielename = substr(md5($n2val['name'].$n3val['name']),0,8);
						$nqfieldArr[$fielename] = array(
								'name'=>$fielename,
								'showname' =>$n2val['showname']."_".$n3val['showname'],
								'colORrow' =>1,
								'is_datatable' =>1,
								'original' =>$n3val['original'],
								'value' =>$n3val['value'],
						);
					}
				}
				$neiqiantotalArr = array_merge($neiqiantotalArr,$nqfieldArr);
			}
			$bqArr[] = array('title'=>$showname.'_列表标签',
					'type'=>2,
					'value'=>$neiqiantotalArr
			);
			return $bqArr;
		}
		//分开组合表单，内嵌，以及内嵌字段
		if($neiqiantotalArr){
			$nqfieldArr = array();
			foreach($neiqiantotalArr as $n2key=>$n2val){
				foreach($n2val['value'] as $n3key=>$n3val){
					$fielename = substr(md5($n2val['name'].$n3val['name']),0,8);
					$nqfieldArr[$fielename] = array(
							'name'=>$fielename,
							'showname' =>$n2val['showname']."_".$n3val['showname'],
							'colORrow' =>1,
							'is_datatable' =>1,
							'original' =>$n3val['original'],
							'value' =>$n3val['value'],
					);
				}
			}
			$totalArr = array_merge($totalArr,$neiqiantotalArr,$nqfieldArr);
		}
		return $totalArr;
	}
	/**
	 * @Title: getDqData
	 * @Description: todo(根据配置文件list.inc.php，转换数据格式)
	 * @param list配置文件数组 $detailList
	 * @param 数据二维数组 $volist
	 * @param 字段前缀名 $extend
	 * @author 黎明刚
	 * @return 返回转换后的二维数组
	 * @date 2015年1月13日 下午3:04:47
	 * @throws
	 */
	public function getDqData($detailList,$vo,$extend=""){
		$data = array();
		foreach($detailList as $k2=>$v2){
			if($v2['name'] =="id" || $v2['name'] =="action" || $v2['name'] =="auditState" || strpos(strtolower($v2['name']),"datatable")!==false){
				continue;
			}
			$fielename = substr(md5($extend.$v2['name']),0,8);
			if($vo){
				if(isset($v2['fieldcategory']) && $v2['fieldcategory']=='textarea'){
					$valArr = array('name'=>$fielename,'showname'=>$v2['showname'],'istestarea'=>1,'original'=>$vo[$v2['name']],'value'=>$vo[$v2['name']]);
				}else{
					$valArr = array('name'=>$fielename,'showname'=>$v2['showname'],'istestarea'=>0,'original'=>$vo[$v2['name']],'value'=>$vo[$v2['name']]);
					if(isset($v2['fieldcategory']) && $v2['fieldcategory']=='checkbox'){
						//组合下拉框数据格式
						if(count($v2['func']) >0){
							$varchar = "";
							foreach($v2['func'] as $k3=>$v3){
								foreach($v3 as $k5=>$v5){
									if($v5 == 'getSelectlistValue'){
										//获取枚举数据
										$meiju = $v2['funcdata'][$k3][$k5][1];
										if($meiju){
											$model=D('Selectlist');
											$temp = $model->GetRules($meiju);
											$meidata = $temp[$meiju];
											$meijuarr = implode(",", array_keys($meidata));
											break 2;
										}
									}else if($v5 == 'excelTplidTonameAppend'){
										//获取枚举数据
										$table = $v2['funcdata'][$k3][$k5][3];
										if($table){
											$field = $v2['funcdata'][$k3][$k5][1];
											$model = M($table);
											$map['status'] = 1;
											$meidata=$model->where($map)->getField($field,true);
											$meijuarr = implode(",", $meidata);
											break 2;
										}
									}
								}
							}
						}
						$valArr = array('name'=>$fielename,'showname'=>$v2['showname'],'istestarea'=>0,'ischecked'=>1,'checkList'=>$meijuarr,'original'=>$vo[$v2['name']],'value'=>$vo[$v2['name']]);
					}
				
					if(count($v2['func']) >0){
						$varchar = "";
						foreach($v2['func'] as $k3=>$v3){
							//开始html字符
							if(isset($v2['extention_html_start'][$k3])){
								$varchar = $v2['extention_html_start'][$k3];
							}
							//中间内容
							$varchar .= getConfigFunction($vo[$v2['name']],$v3,$v2['funcdata'][$k3],$vo);
				
							if(isset($v2['extention_html_end'][$k3])){
								$varchar .= $v2['extention_html_end'][$k3];
							}
							//结束html字符
						}
						$valArr['value'] = $varchar;
					}
				}
			}else{
				$valArr = array('name'=>$fielename,'showname'=>$v2['showname']);
			}
			$data[$fielename]= $valArr;
		}
		return $data;
	}
	
	/**
	 * @Title: getDetaileZhList
	 * @Description: todo(列表行数组)
	 * @param linc配置文件 $detailList
	 * @param 二维数据 $volist
	 * @param 数据字段 $fieldname
	 * @param 数据显示名称 $showname
	 * @return 返回数组
	 * @author 黎明刚
	 * @date 2015年1月30日 下午4:04:59
	 * @throws
	 */
	public function getDetaileZhList($detailList,$volist,$fieldname,$showname){
		$valArr = array();
		//先定义名称
		$fielename = substr(md5($fieldname),0,8);
		$data[$fielename] = array(
				'name'=>$fielename,
				'is_datatable'=>1,
				'showname'=>$showname,
		);
		foreach($detailList as $k2=>$v2){
			if($v2['fieldcategory']=="uploadfilenew" || $v2['name'] =="id" || $v2['name'] =="action" || $v2['name'] =="auditState" || strpos(strtolower($v2['name']),"datatable")!==false || $v2['shows']==0){
				continue;
			}
			$val = array();
			$original = array();
			foreach($volist as $k=>$v){
				$original[] = $v[$v2['name']];
				
				if(count($v2['func']) >0){
					$varchar = "";
					foreach($v2['func'] as $k3=>$v3){
						//开始html字符
						if(isset($v2['extention_html_start'][$k3])){
							$varchar = $v2['extention_html_start'][$k3];
						}
						//中间内容
						$varchar .= getConfigFunction($v[$v2['name']],$v3,$v2['funcdata'][$k3],$volist[$k]);

						if(isset($v2['extention_html_end'][$k3])){
							$varchar .= $v2['extention_html_end'][$k3];
						}
						//结束html字符
					}
					$val[] = $varchar;
				}else{
					if($v2['fieldtype'] == "DECIMAL"){
						//处理小数点后面的无效0
						$orgval = doubleval($v[$v2['name']]);
						$val[]  = preg_replace('/(.¥d+)0+$/', '${1}', $orgval.'');
					}else{
						$val[] = $v[$v2['name']];
					}
				}
			}
			$subfieldname = substr(md5($v2['name']),0,8);
			//组合内嵌表单个字段
			$subfielename = substr(md5($fielename.$subfieldname),0,8);
			if($volist){
				//先组合内嵌字段
				$valArr[] = array('name'=>$subfieldname,'is_stats'=>$v2['iscount'],'func'=>$v2['func'],'funcdata'=>$v2['funcdata'],'showname'=>$v2['showname'],'original'=>$original,'value'=>$val);
				$data[$subfielename] = array(
						'name'=>$subfielename,
						'showname' =>$showname."_".$v2['showname'],
						'colORrow' =>1,
						'is_datatable' =>1,
						'original' =>$original,
						'value' =>$val,
				);
			}else{
				//先组合内嵌字段
				$valArr[] = array('name'=>$subfieldname,'showname'=>$v2['showname']);
				$data[$subfielename] = array(
						'name'=>$subfielename,
						'showname' =>$showname."_".$v2['showname'],
				);
			}
		}
		$data[$fielename]['value'] = $valArr;
		
		return $data;
	}
	/**
	 * 处理数据列表无效的0
	 * @param array 数据配置文件 $detailList
	 * @param array 数据列表 $volist
	 * @return array 返回处理过后的数据列表
	 * @author liminggang
	 */
	public function setInvalidZero($detailList,$volist){
		foreach($detailList as $k2=>$v2){
			foreach($volist as $k=>$v){
				if($v2['fieldtype'] == "DECIMAL"){
					//处理小数点后面的无效0
					$orgval = doubleval($v[$v2['name']]);
					$volist[$k][$v2['name']]  = preg_replace('/(.¥d+)0+$/', '${1}', $orgval.'');
				}
			}
		}
		return $volist;
	}
	
	/**
	 * lookup反查值
	 * @Title: lookupCounterCheck
	 * @Description: todo(根据org写入值反查当前lookup的缺少值。)   
	 * @author quqiang 
	 * @date 2015年1月29日 下午11:15:36 
	 * @throws
	 */
	function lookupCounterCheck(){
		$lpkey = $_REQUEST['looukupkey']; // lookup 的配置key
		$field = $_REQUEST['key']; // 条件查询字段名
		$val = $_REQUEST['val']; // 当前使用的值
		if( $lpkey && $field && $val ){
			// 得lookup的配置。
			$lookupObj = D('LookupObj');
			$lookupDetail = $lookupObj->GetLookupDetail($lpkey);
			// 取出相关值。
			$textField = $lookupDetail['filed']; 	// 显示字段
			$valField = $lookupDetail['val']; 		// 存储字段
			$modelName = $lookupDetail['mode']; // model名称
			$viewType = $lookupDetail['viewname']; // 视图查询的标识
			
			// 视图查询时无法带回值，不管。
			// 开始做视图的代码解析@nbmxkj 20150723
			if($textField && $valField && $modelName ){
				// 查询条件字段
				$searchField = '';
				// 检索值字段
				$counterCheckField = '';
				switch ($field){
					case $valField:
						$searchField = $valField;
						$counterCheckField = $textField;
						break;
					case $textField:
						$searchField = $textField;
						$counterCheckField = $valField;
						break;
					default:
						$searchField = '';
						$counterCheckField = '';
						break;
					}
					// 测试完成后开启该功能
// 					if($searchField==$counterCheckField){
// 						//	检索字段与条件字段相同时直接返回
// 						unset($data);
// 						$data['code']=1;
// 						$data['data']=$val;
// 						$data['parame']="直接返回，不需要查询";
// 						echo json_encode($data);
// 						exit;
// 					}
					
				// 非视图模式
				if(!$viewType){
					if($searchField && $counterCheckField ){
						$counterCheckVal = getFieldBy($val, $searchField, $counterCheckField, D($modelName)->getTableName());
						// echo $counterCheckVal;
						unset($data);
						$data['code']=1;
						$data['data']=$counterCheckVal;
						echo json_encode($data);
					}else{
						 unset($data);
						 $data['code']=0;
						 $data['data']='错误,无效的配置数据';
						 $data['parame']=$lpkey.'__'.$field.'__'.$val;
						 echo json_encode($data);
					}
				}else{
					// 视图模式
					if($searchField && $counterCheckField ){
						// 得到视图的xql语句
						 $obj = M('mis_system_dataview_mas');
						 $map['name'] = $viewType;
						 $data=$obj->where($map)->find();
						 // 得到完整版视图sql
						 $orderSql = $data['replacesql'];
						 // 定义变量，临时的条件字段
						 $tempSearchField = '';
						 
						 if(!$orderSql){
							  unset($data);
							 $data['code']=0;
							 $data['data']='错误,视图sql不存在';
							 $data['parame']=$lpkey.'__'.$field.'__'.$val;
							 echo json_encode($data);
						 }else{
							 // 得到条件字段与检索字段在sql中的真实字段名
							$filedObj=M('mis_system_dataview_sub');
							unset($fieldMap);
							$fieldMap['masid']=$data['id'];
							$fieldMap['otherfield']=$searchField;
							$fieldMap['STATUS']=1;
							$tempSearchField = $filedObj->where($fieldMap)->field('field')->find();
							$tempSearchField = $tempSearchField['field'];
							 
							/**
							 * 重构真实查询条件 , 
							 * 		*只存在一项，不可出项多条件字段重构情况*
							 */ 
							$realSearchContent = ' and '.$tempSearchField."='{$val}'";
							// 在目标sql中定位并写入真实条件，
							/**
							 * 未考虑的情况	
							 * 					无where	无order 、group、 limit,
							 * 					无where	有..........................................
							 * 					多where	有/无....................................
							 * 再说吧。
							 */
							//	测试sql格式 范例
// 							$sql1="select id , name from user where order by id desc";
// 							$sql2="select id , name from user where id=1 group by id order by id desc";
// 							$sql3="select id , name from user order by id desc";
// 							$sql4="select id , name from user group by id";
// 							$sql5="select id , name from user where   ";
// 							$orderSql = $sql5;	
							// 检查where个数
							unset($whereCountReg);
							$whereCountReg = '/\bwhere\b/i';
							$ret = preg_match_all($whereCountReg, $orderSql , $match);
							switch ($ret){
								case 'null':
									unset($reg);
									$reg="/(from)(.+?)(group|order|limit|$)/";
									$orderSql = preg_replace_callback($reg , function($vo) use($realSearchContent) {
										if($vo[1] && $vo[2] && $vo[3]){
											// 对现有带有and的重构条件，再次重构为and变为where
											return $vo[1].'' .$vo[2] .str_replace(' and', 'where', $realSearchContent).' '.$vo[3];
										}
									} , $orderSql);
									break;
								case '1':
									// todo
									// where 的内容为空 且之后没有内容
									unset($orderSqlTemp);
									$orderSqlTemp = $orderSql;
									$characterArr = preg_split('/where/i',$orderSqlTemp);
									if(!trim($characterArr[1])){
										$orderSql=$orderSql.str_replace('and', '', $realSearchContent);
									}else {
										unset($reg);
										$reg="/(where)(.+?)(group|order|limit|$)/i";
										$orderSql = preg_replace_callback($reg , function($vo) use($realSearchContent) {
											if($vo[2] != ' ' ){
												return $vo[1].$vo[2].$realSearchContent;
											}else{
												return str_replace(' and', 'where', $realSearchContent).$vo[2];
											}
										} , $orderSql);
									}
									break;
								default:
									$orderSql='';
									break;
							}
							//	测试 sql 输出
// 							print_r($orderSql);exit;
							if($orderSql){
								$orderData = M()->query($orderSql);
								if($orderData){
									$orderData = reset($orderData);
								}
								$counterCheckVal = $orderData[$counterCheckField];
								unset($data);
								$data['code']=1;
								$data['data']=$counterCheckVal;
								$data['parame']=$lpkey.'__'.$field.'__'.$val.'_sql:'.$orderSql;
								echo json_encode($data);
							}else{
								unset($data);
								$data['code']=0;
								$data['data']='错误,反查sql语句为空。where不止一项！';
								echo json_encode($data);
							}
						}
					}else{
						 unset($data);
						 $data['code']=0;
						 $data['data']='错误,无效的配置数据';
						 $data['parame']=$lpkey.'__'.$field.'__'.$val;
						 echo json_encode($data);
					}
					
				}
			}else{
				 unset($data);
				 $data['code']=0;
				 $data['data']='错误,无效的检索字段'.$viewType;
				 $data['parame']=$lpkey.'__'.$field.'__'.$val;
				 echo json_encode($data);
			}
			
		}else{
		    unset($data);
		     $data['code']=0;
             $data['data']='错误';
             $data['parame']=$lpkey.'__'.$field.'__'.$val;
             echo json_encode($data);
		}
	}
	/**
	 * @Title: delsubinfo
	 * @Description: todo(子表数据删除)
	 * @author 管理员
	 * @date 2014-12-29 15:20:20
	 * @parame $_POST['table'] 表名
	 * @parame $_POST['id'] 数据ID值
	 * @throws
	 */
	function delsubinfo(){
		$table = $_POST['table'];
		$id = intval($_POST['id']);
		$modelname=$_GET['delmodel'];
		$name=$this->getActionName();
		if($table){
			if(isset ( $id )){
				$model = M($table);
				$map['id'] = array('eq',$id);
				if(getFieldBy($name,"bindaname","inbindaname","mis_auto_bind")){
					//调用删除子项
					$retArr=$this->lookupdeletebind($name,$id,&$retArr);
					if($retArr){
						//循环删除项
						foreach ($retArr as $rkey=>$rval){
							$reModel=D($rkey);
							$ids=implode(',', $rval);
							$dmap=array();
							if($ids){
								$dmap['id']=array("in",$ids);
								$listArr=$reModel->where($dmap)->select();
								//删除备份数据
								logs("被删除关联子项数据==".$listArr,$rkey."testsub");
								$result=$reModel->where($dmap)->delete(); 
								logs("删除关联子项==".$reModel->getlastsql() ,"binddeletechildsub");
								if ($result==false) {
									$this->error ("关系表单数据删除失败,请联系管理员！");
								}
							}
						}
					}
				}
				//备份删除数据
				$list2Arr=$model->where($map)->select();
				logs("被删除关联项数据==".$listArr,$table."testsub");
				//数据漫游
				/*
				 * _over_delete 方法，1、模型名称  2、当前ID对象
				*/
				$this->_over_delete($modelname,$id);
				$model->where($map)->delete();
				logs("删除关联项==".$model->getlastsql() ,$modelname."binddelete");
				$this->success('数据成功删除');
			}
		}
	}
	
	/**
	 * 
	 * @Title: getMiniIndexLoad
	 * @Description: todo(miniindex带值方法)   
	 * @author renling 
	 * @date 2015年2月8日 上午10:09:07 
	 * @throws
	 */
	public function getMiniIndexLoad(){
		//获取fieldtype参数
		$name=$this->getActionName();
		$fieldtype=$_REQUEST['fieldtype'];
		if($fieldtype||$_REQUEST['bindrdid']){
			if($fieldtype&&$fieldtype!="#"){
				$vo[$fieldtype]=$_REQUEST[$fieldtype];
			}
			$SystemConfigNumberAction= A('SystemConfigNumber');
			$newOrderNo= $SystemConfigNumberAction->lookupDatatableOrderNo($name);
			if($newOrderNo['status']){
				$vo['orderno']=$newOrderNo;
			}
			$sourcemodel=$_REQUEST['bindaname'];
			$sourceid=$_REQUEST['bindrdid'];
			$targetmodel=$name;
			//获取数据漫游映射
			$model=D('MisSystemDataRoaming');
			$romasID=array();
			$romasID['bindaname']=$sourcemodel;
			$romasID['inbindaname']=$name;
			$MisAutoBindSettableVo=M("mis_auto_bind")->where($romasID)->find();
			$datarommasid=$_REQUEST['urdataroamid']?$_REQUEST['urdataroamid']:$MisAutoBindSettableVo['dataroamid'];
			$dataArr=$model->main(2,$sourcemodel,$sourceid,4,$targetmodel,'',$datarommasid); 
			//$data=$model->dataRoamOrderno($sourcemodel,$sourceid,$targetmodel);
			//查询当前配置文件
			if($dataArr){
				$scdmodel = D('SystemConfigDetail');
				$detailList = $scdmodel->getDetail($name);
				if(is_array(reset($dataArr))){
					foreach(reset($dataArr) as $k=>$v){
						foreach($v as $k1 => $v1){				
							//当前组件为lookup
							if($detailList[key($v1)]['fieldcategory']=="lookup"){
								$showname=getFieldBy(reset($v1), $detailList[key($v1)]['funcdata'][0][0][1], $detailList[key($v1)]['funcdata'][0][0][2], $detailList[key($v1)]['funcdata'][0][0][3]);
								$vo[key($v1)]=array(
										'value'=>reset($v1),
										'showname'=>$showname,
								);
							}else{
								$vo[key($v1)] = reset($v1);
							}
						}
					}
				}
			}else if($_REQUEST['dataromaing']){
				$MisSystemDataRoamSubDao=M("mis_system_data_roam_sub");
				$dataroaming=unserialize(base64_decode($_REQUEST['dataromaing']));
				//查询缓存表
				$cachDao=M($dataroaming['randtable']);
				$cachMap=array();
				$cachMap['tablename']=$dataroaming['tablename'];
				$cachMap['randnum']=$dataroaming['randnum'];
				$cachMap['createid']=$dataroaming['createid'];
				$cachList=$cachDao->where($cachMap)->find();
				$dataromaList=unserialize(base64_decode($cachList['backupdata']));
				$misAutoBindModel=D("MisAutoBind");
				//查询当前漫游id
				$BindMap=array();
				$BindMap['inbindaname']=$name;
				$BindMap['bindaname']=$_REQUEST['bindaname'];
				$_REQUEST['typeid']=2;
				$misAutoBindVo=$misAutoBindModel->where($BindMap)->find();
				//加上数据漫游条件
				$MisSystemDataRoamSubList=$MisSystemDataRoamSubDao->where("masid=".$misAutoBindVo['dataroamid'])->select();
				if($MisSystemDataRoamSubList){
					foreach ($MisSystemDataRoamSubList as $dskey=>$dsval){
						if($dataromaList[$dsval['sfield']]){
							$vo[$dsval['tfield']]=$dataromaList[$dsval['sfield']];
						}
					}
				}
				// 删除缓存表
				//self::unsetOldDataToCache($dataroaming);
			}
			$this->assign("addvo",$vo);
		} 
		$this->assign("type",$_REQUEST['type']);
		$this->assign("ecode",$_REQUEST['ecode']);
	}
	/**
	 * 
	 * @Title: getFormIndexLoad
	 * @Description: todo(新增及修改带值方法)   
	 * @author renling 
	 * @date 2015年2月8日 上午10:09:24 
	 * @throws
	 */
	public function getFormIndexLoad($vo){
		$name=$this->getActionName();
		$misAutoBindModel=D("MisAutoBind");
		$MisSystemDataRoamSubDao=M("mis_system_data_roam_sub");
		//获取fieldtype参数
		$fieldtype=$_REQUEST['fieldtype'];
		if($fieldtype||$_REQUEST['bindrdid']){
			if($fieldtype){
				$vo[$fieldtype]=$_REQUEST[$fieldtype];
			}
			$sourcemodel=$_REQUEST['bindaname'];
			$sourceid=$_REQUEST['bindrdid'];
			$targetmodel=$name;
			$model=D('MisSystemDataRoaming');
			$romasID=array();
			$romasID['bindaname']=$sourcemodel;
			$romasID['inbindaname']=$name;
			$MisAutoBindSettableVo=M("mis_auto_bind")->where($romasID)->find();
			$datarommasid=$_REQUEST['urdataroamid']?$_REQUEST['urdataroamid']:$MisAutoBindSettableVo['dataroamid'];
			$dataArr=$model->main(2,$sourcemodel,$sourceid,4,$targetmodel,'',$datarommasid);
			//$dataArr=$model->main(2,$sourcemodel,$sourceid,4,$targetmodel);
			if($dataArr){
				if(is_array(reset($dataArr))){
					foreach(reset($dataArr) as $targettable=>$data){
						foreach($data as $k => $v){
							if(reset($v)){
								$vo[key($v)] = reset($v);
							}
						}
					}
				}
			}else if($_REQUEST['dataromaing']){
				$dataroaming=unserialize(base64_decode($_REQUEST['dataromaing']));
				//查询缓存表
				$cachDao=M($dataroaming['randtable']);
				$cachMap=array();
				$cachMap['tablename']=$dataroaming['tablename'];
				$cachMap['randnum']=$dataroaming['randnum'];
				$cachMap['createid']=$dataroaming['createid'];
				$cachList=$cachDao->where($cachMap)->find();
				$dataromaList=unserialize(base64_decode($cachList['backupdata']));
				//查询当前漫游id
				$BindMap=array();
				$BindMap['inbindaname']=$name;
				$BindMap['bindaname']=$_REQUEST['bindaname'];
				$_REQUEST['typeid']=2;
				$misAutoBindVo=$misAutoBindModel->where($BindMap)->find();
				//加上数据漫游条件
				$MisSystemDataRoamSubList=$MisSystemDataRoamSubDao->where("masid=".$misAutoBindVo['dataroamid'])->select();
				if($MisSystemDataRoamSubList){
					foreach ($MisSystemDataRoamSubList as $dskey=>$dsval){
						if($dataromaList[$dsval['sfield']]){
							$vo[$dsval['tfield']]=$dataromaList[$dsval['sfield']];
						}
					}
				}
				//	if($_POST['operateid']==1){
				// 		删除缓存表
				//		self::unsetOldDataToCache($dataroaming);
				//	}
			}
		} 
		$this->assign("vo",$vo);
	}
	/**
	 * 
	 * @Title: miniindex
	 * @Description: todo(表单转换为列表内嵌页面)   
	 * @author renling 
	 * @date 2015年2月10日 上午10:33:47 
	 * @throws
	 */
	public function miniindex(){
		//获取当前控制器名称
		$name=$this->getActionName();
		//判断当前表单是否是组合表单主入口
		if(checkActionIsMain($name,0)){
			//查询当前主入口表单的附属表单
			$MisAutoBindModel=D("MisAutoBind");
			$map['bindaname']=$name;
			$map['status']=1;
			$map['typeid']=0;
			$MisAutoBindList=$MisAutoBindModel->where($map)->getField("inbindaname,bindtype");
			
		}
		//获取传递过来的方法名,处理minindex表主表未现在，minindex不出现新增按钮
		$this->assign("func",$_REQUEST['func']);
		//$this->getMiniIndexLoad();
		
		//列表过滤器，生成查询Map对象
		$map = $this->_search (); 
	        
		//获取fieldtype参数
		$fieldtype=$_REQUEST['fieldtype'];
		if($fieldtype||$_REQUEST['bindrdid']){
			if($fieldtype&&$fieldtype!="#"){
				$vo[$fieldtype]=$_REQUEST[$fieldtype];
			}
			$SystemConfigNumberAction= A('SystemConfigNumber');
			$newOrderNo= $SystemConfigNumberAction->lookupDatatableOrderNo($name);
			if($newOrderNo['status']){
				$vo['orderno']=$newOrderNo;
			}
			$sourcemodel=$_REQUEST['bindaname'];
			$sourceid=$_REQUEST['bindrdid'];
			$targetmodel=$name;
			//获取数据漫游映射
			$model=D('MisSystemDataRoaming');
			$romasID=array();
			$romasID['bindaname']=$sourcemodel;
			$romasID['inbindaname']=$name;
			//组合表单
			if(getFieldBy($name, "inbindaname", "typeid", "mis_auto_bind")==0){
				//查询数据VO
				$actionmodel=D($sourcemodel);
				$vo=$actionmodel->where("id=".$sourceid)->find();
				$bindVo=M("mis_auto_bind")->where($romasID)->find();
				$romasID['bindval']=$vo[$bindVo['bindresult']];
			}
			$MisAutoBindSettableVo=M("mis_auto_bind")->where($romasID)->find();
			$datarommasid=$_REQUEST['urdataroamid']?$_REQUEST['urdataroamid']:$MisAutoBindSettableVo['dataroamid'];
			$dataArr=$model->main(2,$sourcemodel,$sourceid,4,$targetmodel,'',$datarommasid);
			//$data=$model->dataRoamOrderno($sourcemodel,$sourceid,$targetmodel);
			//查询当前配置文件
			if($dataArr){
				$scdmodel = D('SystemConfigDetail');
				$detailList = $scdmodel->getDetail($name);
				if(is_array(reset($dataArr))){
					foreach(reset($dataArr) as $k=>$v){
						foreach($v as $k1 => $v1){
							//当前组件为lookup
							if($detailList[key($v1)]['fieldcategory']=="lookup"){
								$showname=getFieldBy(reset($v1), $detailList[key($v1)]['funcdata'][0][0][1], $detailList[key($v1)]['funcdata'][0][0][2], $detailList[key($v1)]['funcdata'][0][0][3]);
								$vo[key($v1)]=array(
										'value'=>reset($v1),
										'showname'=>$showname,
								);
							}else{
								$vo[key($v1)] = reset($v1);
								if($vo[key($v1)]&&!$_REQUEST['fieldtype']){
									if($MisAutoBindSettableVo['typeid']==2){
										$map[key($v1)]=reset($v1);
									} 
								}
							}
						}
					}
				}
			}else if($_REQUEST['dataromaing']){
				$MisSystemDataRoamSubDao=M("mis_system_data_roam_sub");
				$dataroaming=unserialize(base64_decode($_REQUEST['dataromaing']));
				//查询缓存表
				$cachDao=M($dataroaming['randtable']);
				$cachMap=array();
				$cachMap['tablename']=$dataroaming['tablename'];
				$cachMap['randnum']=$dataroaming['randnum'];
				$cachMap['createid']=$dataroaming['createid'];
				$cachList=$cachDao->where($cachMap)->find();
				$dataromaList=unserialize(base64_decode($cachList['backupdata']));
				$misAutoBindModel=D("MisAutoBind");
				//查询当前漫游id
				$BindMap=array();
				$BindMap['inbindaname']=$name;
				$BindMap['bindaname']=$_REQUEST['bindaname'];
				$_REQUEST['typeid']=2;
				$misAutoBindVo=$misAutoBindModel->where($BindMap)->find();
				//加上数据漫游条件
				$MisSystemDataRoamSubList=$MisSystemDataRoamSubDao->where("masid=".$misAutoBindVo['dataroamid'])->select();
				if($MisSystemDataRoamSubList){
					foreach ($MisSystemDataRoamSubList as $dskey=>$dsval){
						if($dataromaList[$dsval['sfield']]){
							$vo[$dsval['tfield']]=$dataromaList[$dsval['sfield']];
							if($vo[$dsval['tfield']]&&!$_REQUEST['fieldtype']){
								//套表直接取漫游map
								if($MisAutoBindSettableVo['typeid']==2){
									$map[$dsval['tfield']]=$dataromaList[$dsval['sfield']];
								}
							}
						}
					}
					//组合表
					if($MisAutoBindSettableVo['typeid']==0){
						if($MisAutoBindSettableVo['inbindmap']){
							$newconditions = str_replace ( array ( '&quot;', '&#39;', '&lt;','&gt;'), array ('"',"'",'<','>'
							), $MisAutoBindSettableVo['inbindmap'] );
							$map['_string']=$newconditions;
						}
						//表单附加条件
						if($MisAutoBindSettableVo['bindconlistArr']){
							if($MisAutoBindSettableVo['bindconlistArr']){
								$bindconlistArr=unserialize($MisAutoBindSettableVo['bindconlistArr']);
								//获取表单子表附加
								foreach ($bindconlistArr as $bindkey=>$bindval){
									//如果有值才生成查询
									if($vo[$bindkey]){
										$map[$bindval]=$vo[$bindkey];
									}
								}
							}
						}
					}
				}
				// 删除缓存表
				//self::unsetOldDataToCache($dataroaming);
			}
			//组合表
			if($MisAutoBindSettableVo['typeid']==0){
				if($MisAutoBindSettableVo['inbindmap']){
					$newconditions = str_replace ( array ( '&quot;', '&#39;', '&lt;','&gt;'), array ('"',"'",'<','>'
					), $MisAutoBindSettableVo['inbindmap'] );
					$map['_string']=$newconditions;
				}
				//表单附加条件
				if($MisAutoBindSettableVo['bindconlistArr']){
					if($MisAutoBindSettableVo['bindconlistArr']){
						$bindconlistArr=unserialize($MisAutoBindSettableVo['bindconlistArr']);
						//获取表单子表附加
						foreach ($bindconlistArr as $bindkey=>$bindval){
							//如果有值才生成查询
							if($vo[$bindkey]){
								$map[$bindval]=$vo[$bindkey];
							}
						}
					}
				}
			}
			$this->assign("addvo",$vo);
		}
		
		$this->assign("type",$_REQUEST['type']);
		$this->assign("ecode",$_REQUEST['ecode']);
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		//套表专有
		if($_REQUEST['fieldtype']){
			$this->getBindSetTables($map);
		}
		if($_REQUEST['projectid']){
			$map['projectid'] = $_REQUEST['projectid'];
		}
		if($_REQUEST['projectworkid']){
			//$map['projectworkid'] = $_REQUEST['projectworkid'];
		}
		$name = $this->getActionName();
		
		if (! empty ( $name )) {
			$qx_name=$name;
			if(substr($name, -4)=="View"){
				$qx_name = substr($name,0, -4);
			}
			//验证浏览及权限
			if( !isset($_SESSION['a']) ){
				$map=D('User')->getAccessfilter($qx_name,$map);
			}
			//列表页排序 ---开始-----2015-08-06 15:07 write by xyz
			if($_REQUEST['orderField']&&strpos(strtolower($_REQUEST['orderField']),' asc')===false&&strpos(strtolower($_REQUEST['orderField']),' desc')===false){
				$this->_list ( $name, $map);
			}else{
				$sortsorder = '';
				$sortsmap['modelname'] = $name;
				$sortsmap['sortsorder'] = array("gt",0);
				//管理员读公共设置
				if($_SESSION['a']){
					$listincModel = M("mis_system_public_listinc");
					$sortslist = $listincModel->where($sortsmap)->order("sortsorder")->select();
				}else{
					//个人先读个人设置、没有再读公共设置
					$sortsmap['userid'] = $_SESSION [C ( 'USER_AUTH_KEY' )];
					$listincModel = M("mis_system_private_listinc");
					$sortslist = $listincModel->where($sortsmap)->order("sortsorder")->select();
					if(empty($sortslist)){
						unset($sortsmap['userid']);
						$listincModel = M("mis_system_public_listinc");
						$sortslist = $listincModel->where($sortsmap)->order("sortsorder")->select();
					}
				}
				//如果在设置里有相关数据、提取排序字段组合order by
				if($sortslist){
					foreach($sortslist as $k=>$v){
						if(is_numeric($v['fieldname'])){//防止配置文件以索引数组构成找不到真实字段名
							$scdmodel = D('SystemConfigDetail');
							$detailList = $scdmodel->getDetail($name);
							$v['fieldname'] = $detailList[$v['fieldname']]['name'];
						}
						$sortsorder .= $v['fieldname'].' '.$v['sortstype'].',';
					}
					$sortsorder = substr($sortsorder,0,-1);
				}
				//列表页排序 ---结束-----
				$this->_list ( $name, $map,'', false,'','',$sortsorder,false);
			}
				
				
		}
// 		if (! empty ( $name )) {
// 			$qx_name=$name;
// 			if(substr($name, -4)=="View"){
// 				$qx_name = substr($name,0, -4);
// 			}
// 			//验证浏览及权限
// 			if( !isset($_SESSION['a']) ){
// 				$map=D('User')->getAccessfilter($qx_name,$map);
// 			}
// 			$this->_list ( $name, $map );
// 		}
		
		//begin
		$scdmodel = D('SystemConfigDetail');
		//读取列名称数据(按照规则，应该在index方法里面)
		$detailList = $scdmodel->getDetail($name,true,'','sortnum');
		if(file_exists(ROOT . '/Dynamicconf/Models/'.$name.'/form.inc.php')){
			$anameList = require ROOT . '/Dynamicconf/Models/'.$name.'/form.inc.php';
			if(!empty($detailList) && !empty($anameList)){
				foreach($detailList as $k => $v){
					$detailList[$k]["datatable"] = 'template_key=""';
					foreach($anameList as $kk => $vv){
						if($k==$kk){
							$detailList[$k]["datatable"] = $vv["datatable"];
						}
					}
				}
			}
		}
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		$this->display();
	}
	/**
	 * 组合、主从表的特殊代码
	 * @Title: getCombinationTableAndSlaveTableSpecialCode
	 * @Description: todo(对组合、主从表的入库操作做区分的操作代码) 
	 * @param int $id  主数据ID值
	 * @author quqiang 
	 * @date 2015年2月12日 下午5:42:00 
	 * @throws
	 */
	protected function getCombinationTableAndSlaveTableSpecialCode($name,$id,$type , $isrelation = false){
		// 默认往关联表中插入空数据
		// 获取传送的id及主体id
		//$name=$this->getActionName();
		$MisAutoBindModel =M ( "mis_auto_bind" );
		$bindMap['status']=1;
		$bindMap['bindaname']=$name;
		$bindMap['bindtype']=0;
		$bindid = $id ;
		$MisAutoBindList=$MisAutoBindModel->where($bindMap)->getField("id,inbindaname");
		if($MisAutoBindList){
			$date = array ();
			//获取数据漫游date
			$MisSystemDataRoamingModel=D('MisSystemDataRoaming');
			if(getFieldBy($name, "bindaname", "inbindaname", "MisAutoBind","typeid",1)){
				$bindid = $id ;
			}else{
				$bindid=getFieldBy($id, "id", "orderno", $name);
			}
			$bindList=array();
			foreach ($MisAutoBindList as $key=>$val){
				if(in_array($val, array_keys($bindList))){
				}else{
					$bindList[$val]=1;
					$model = D ( $val);
					$dataArr=$MisSystemDataRoamingModel->main(2,$name,$bindid,4,$val);
					foreach($dataArr as $k=>$v){
						foreach($v as $k1=>$v1){
						  $date[key($v1)] = reset($v1);
						}
					}
					$date['bindid']=$bindid;
					$date['relationmodelname']=$name;//插入主表模型
					$result = $model->add ( $date );
					if (! $result) {
						if($isrelation){
							throw new NullCreateOprateException( "数据插入失败,请联系管理员！".$model->getLastSql() );
						}else{
							$this->error ( "数据插入失败,请联系管理员！".$model->getLastSql() );
							exit;
						}
					}
				}
			}
		}
		if($isrelation){
			return array (
					'bindid' => $bindid,
					'id'=>$id
			);
		}else{
			$this->success ( L ( '操作成功！！' ), '', array (
					'bindid' => $bindid,
					'id'=>$id
			));
		}
	}
	
	
	/**
	 * @Title: wordPlaySwf
	 * @Description: todo(导出时的在线查看)
	 * @author 谢友志
	 * @date 2015-6-19 下午3:02:29
	 * @throws
	 */
	public function wordPlaySwf(){
		$map['id'] = $_REQUEST['id'];
		$templateModel = M("mis_system_template_saveword");		
		$rs = $templateModel->where($map)->find();
		$this->assign("file_name", $rs['filename']);
		$this->assign("file_type", 'file');
		//$file_path = preg_replace("/^([\s\S]+)\/Public/", "__PUBLIC__", $rs['swfurl']);
		$file_path = "/Public/".$rs['swfurl'];
		if($_SESSION['a']==1){
			$file_path2 = preg_replace("/^([\s\S]+)\/Public\//", "", $rs['swfurl']);
			$file_path2=PUBLIC_PATH.$file_path2;
			if(!is_file($file_path2)){
				header( 'Content-Type:text/html;charset=utf-8 ');
				echo "文件不存在";
				exit;
			}
		}
		$this->assign('file_path', $file_path);
		$this->display("Public:playswf");
	}
	
	
	/**
	 * 根据值获取指定表单的下级子表单
	 * @Title: getAutoFormTabs
	 * @Description: todo(这里用一句话描述这个方法的作用) 
	 * @param unknown $action
	 * @param unknown $main
	 * @param unknown $val  
	 * @author renling 
	 * @date 2015年3月9日 下午4:11:56 
	 * @throws
	 */
	function getAutoFormTabs(){
		$action = $_POST['action'];
		$main = $_POST['main'];
		$val = $_POST['val'];
		$bindrdid=$_POST['bindrdid'];
		$filedval = $_POST['filedval'];
		$formtype = $_POST['formtype'];
		
		
		$mainBindActionCondition = '';
		if($main){
			$mainBindActionCondition='/main/'.$main;
		}
		/**
		 * 这个函数的调用限制。
		 * 传入的action为当前父级，必须为列表型表单。
		 * 获取它的子项
		 */
		$ret = array();
		$ret['code'] = 1;
		$ret['msg'] = '操作成功';
// 		if(empty($main)){
// 			$ret['code'] = 0;
// 			$ret['msg'] = '没有入口表单信息';
// 		}
		if(empty($val)){
			$ret['code'] = 0;
			$ret['msg'] = '关联值为空，不可查询！';
		}
		if(empty($filedval)){
			$ret['code'] = 0;
			$ret['msg'] = '关联字段为空，不可查询！';
		}
		
		$model = M('mis_auto_bind');
		// 获取当前表单的子级表单
		//$sql = "SELECT bindaname , inbindtitle, inbindaname , bindtype , typeid ,bindresult , bindval,inbindval FROM mis_auto_bind WHERE  bindaname ='{$action}' AND `level` = (SELECT id FROM `mis_dynamic_form_manage` WHERE actionname='{$action}') order by inbindsort asc";
		$sql = "SELECT bindaname , inbindtitle, bindconlistArr,dataroamid,inbindaname , bindtype , typeid ,bindresult , bindval,inbindval FROM mis_auto_bind WHERE  bindaname ='{$action}'  and bindval='{$filedval}' order by inbindsort asc";
		$childArr = $model->query($sql);
		$retTableInfoArr = array();
		$chilAction = '';
		$MisProjectFlowFormModel=D("MisProjectFlowForm");
		$MisSystemDataRoamingModel=D("MisSystemDataRoaming");
		$MisSystemDataRoamSubDao=M("mis_system_data_roam_sub");
		//此处页面传入的主表当前数据id如果无数据则是新增
		$resultid=$bindrdid?$bindrdid:0;
		$dataroamingCondition="";
		if(!$bindrdid){ 
			//获取
			$flowMap=array();
			$flowMap['projectid']=$_REQUEST['projectid'];
			$flowMap['formobj']=$action;
			$MisProjectFlowFormList=$MisProjectFlowFormModel->where($flowMap)->find();
			//查询真实节点返回数据
			$soureModel=D($MisProjectFlowFormList['sourcemodel']);
			$soureMap=array();
			$soureMap['status']=1;
			$soureMap['projectid']=$_REQUEST['projectid'];
			$soureVo=$soureModel->where($soureMap)->find();
			//根据去查询漫游 不是标准套表漫游
			$dataArr=$MisSystemDataRoamingModel->main(1,$MisProjectFlowFormList['sourcemodel'],$soureVo['id'],4,$action);
			foreach($dataArr as $targettable=>$data){
				foreach($data as $k => $v){
					foreach($v as $k1 => $v1){
						$vo[key($v1)] = reset($v1);
					}
				}
			}
		
			$vo['projectworkid']=$MisProjectFlowFormList['id'];
			$updateBackup=setOldDataToCache($actionname,$vo);
			$updateBackupList=str_replace('=', '', base64_encode(serialize($updateBackup)));
			$dataroamingCondition='/dataromaing/'.$updateBackupList;
		}
		foreach ($childArr as $ckey=>$cval){
			// 这里存在问题，  另一种类型的表单没有了，。
			if($cval['typeid']==2){ //套表管理
				unset($tempArr);
				if($cval['bindtype']==0){
					//查询对应表单数据
					$map=array();
					$model = D($cval['inbindaname']);
					$tempData=array();
					$tempMap=array();
					$tempMap[$cval['inbindval']]=$val;
					$tablename=$model->getTableName();
					//加上数据漫游条件
					$subMap=array();
					$subMap['masid']=$cval['dataroamid'];
					$subMap['targettable']=$tablename;
					$MisSystemDataRoamSubList=$MisSystemDataRoamSubDao->where($subMap)->select();
					if($MisSystemDataRoamSubList){
						foreach ($MisSystemDataRoamSubList as $dskey=>$dsval){
							if($vo[$dsval['sfield']]){
								$tempMap[$dsval['tfield']]=$vo[$dsval['sfield']];
							}
						}
					} 
					//查询绑定的附加条件
					if($cval['inbindmap']){
						$newconditions = str_replace ( array ( '&quot;', '&#39;', '&lt;','&gt;'), array ('"',"'",'<','>'
						), $cval['inbindmap'] );
						$tempMap['_string']=$newconditions;
					}
					$bindconlistArr=unserialize($cval['bindconlistArr']);
					//获取表单子表附加
					foreach ($bindconlistArr as $bindkey=>$bindval){
						//如果有值才生成查询
						if($val){
							if($vo[$bindkey]){
								$tempMap[$bindval]=$vo[$bindkey];
							}
						}
					}
					$tempMap['status']=1;
					//$tempMap['relationmodelname']=$cval['bindaname'];
					$tempData = $model->where($tempMap)->find();
					$tempArr['id'] = $tempData['id'];
					if($tempData['id']){
							$tempArr['id'] =$tempData['id'];
						}else{
							$tempArr['id'] =-1;
					}
					$tempArr['actionname'] =$cval['inbindaname'];
					$tempArr['typeid']=2;
				}else{
					$tempArr['actionname'] =$cval['inbindaname'];
					$tempArr['typeid']=2;
				}
				$tempArr['bindtype']=$cval['bindtype'];
				$tempArr['fieldtype']=$cval['inbindval'];
				$tempArr['inbindtitle']=$cval['inbindtitle'];
				$retTableInfoArr[] = $tempArr;
			}else if($cval['typeid']==0){
				//组合表单类型
				if($cval['bindval'] == $filedval){
					unset($tempArr);
					if($cval['bindtype']==0){
						//根据传过来的orderno 查询对应表单
						$map=array();
						$model = D($cval['inbindaname']);
						$tempData=array();
						$tempMap=array();
						$tempMap['bindid']=$val;
						$tempMap['status']=1;
						$tempMap['relationmodelname']=$cval['bindaname'];
						$tempData = $model->where($tempMap)->find();
						$tempArr['actionname'] =$cval['inbindaname'];
						if($tempData['id']){
							$tempArr['id'] =	$tempData['id'];
						}else{
							$tempArr['id'] =-1;
						}
					}else{
						$tempArr['actionname'] =$cval['inbindaname'];
					}
					$retTableInfoArr[] = $tempArr;
				}
			}
			/*elseif($cval['typeid']==1){
				// 主从表单
				//1.得到主表的ID值，
				//2.以主表的I为条件，在子表中查询bindid等主表ID的ID值
				$model = D($cval['inbindaname']);
				$tempData = $model->where("bindid='".$val."' and status=1")->find();
				
				$retTableInfoArr[] = $acitonList;
			}*/
		}
		$chilAction = $action;
		$retArr = array();
// 		foreach ($retTableInfoArr as $akey=>$aval){
// 			$model=D($aval['inbindaname']);
// 			$vo=$model->where("bindid='".$val."'")->find();
// 			$retArr[$aval['inbindaname']] = $vo;
// 		}
		// <div id="tabsContent" class="tabs proTabNav" currentindex="0" eventtype="click">
		$tablesHtml=<<<EOF
	<div class="tabsHeader proNavHeader">
		<div class="tabsHeaderContent proTabNavHeaderContent ">
			<ul>
EOF;
		$selected="";
		$footerhtml="<div class=\"tabsContent tml-p0\">";
		$listArr=array();
		foreach ($retTableInfoArr as $rkey=>$rval){
// 			$inbindtitle=getFieldBy($rval['actionname'], "actionname", "actiontitle", "mis_dynamic_form_manage");
// 			if($rval['inbindtitle']){
// 				$inbindtitle=$rval['inbindtitle'];
// 			}
			//存在id时为表单类型
			if($rval['id']){
				if($rval['typeid']==2){
					//没有找到该数据新增页面
					if($rval['id']==-1){
						//add页面
						$listArr[$rval['actionname']]=__APP__."/{$rval['actionname']}/add/fieldtype/{$rval['fieldtype']}/{$rval['fieldtype']}/{$val}/bindrdid/{$resultid}/bindaname/{$action}{$mainBindActionCondition}{$dataroamingCondition}";
					}else{
						//edit页面
						$listArr[$rval['actionname']]=__APP__."/{$rval['actionname']}/edit/fieldtype/{$rval['fieldtype']}/{$rval['fieldtype']}/{$val}/id/{$rval['id']}/bindrdid/{$resultid}/bindaname/{$action}{$mainBindActionCondition}";
					}
				}else{
					if($formtype=="edit"&&$rval['id']==-1){
						$listArr[$rval['actionname']]=__APP__."/{$rval['actionname']}/add/bindid/{$val}/bindrdid/{$resultid}/bindaname/{$action}{$mainBindActionCondition}";
					}else{
						$listArr[$rval['actionname']]=__APP__."/{$rval['actionname']}/{$formtype}/id/{$rval['id']}/bindrdid/{$resultid}/bindaname/{$action}{$mainBindActionCondition}";
					}
				}
// 				$tablesHtml.="<span>".$inbindtitle."</span>";
// 				$tablesHtml.="</a></li>";
// 				$footerhtml.="<div id=\"{$main}{$rval['actionname']}_edit\" style=\"{$block}\"></div>";
			}else{
				if($rval['typeid']==2){
					$listArr[$rval['actionname']]=__APP__."/{$rval['actionname']}/miniindex/bindtype/{$rval['bindtype']}/bindaname/{$action}/bindrdid/{$resultid}/fieldtype/{$rval['fieldtype']}/{$rval['fieldtype']}/{$val}{$mainBindActionCondition}{$dataroamingCondition}";
				}else{
					$listArr[$rval['actionname']]=__APP__."/{$rval['actionname']}/miniindex/bindtype/{$rval['bindtype']}/bindaname/{$action}/bindrdid/{$resultid}/bindid/{$val}{$mainBindActionCondition}";
				}			
// 				$tablesHtml.="<span>".$inbindtitle."</span>";
// 				$tablesHtml.="</a></li>";
// 				$footerhtml.="<div id=\"{$main}{$rval['actionname']}_edit\" style=\"{$block}\"></div>";
			}
			
// 			if(!$selected){
// 				//$footerhtml.="</div>";
// 			}
// 			$i++;
		}
		
// 		$tablesHtml.="</ul></div>";//</div>";
// 		$footerhtml.="</div></div></div>";//</div>";
// 		$tablesHtml.=$footerhtml;
		$ret['data'] = array($chilAction=>$listArr);
		echo json_encode($ret);
	}
	
	
	
	
	/**
	 * 根据值获取指定表单的下级子表单
	 * @Title: getAutoFormTabsForAll
	 * @Description: todo(子合同点击显示对应表单)
	 * @param unknown $action
	 * @param unknown $main
	 * @param unknown $val
	 * @author renling
	 * @date 2015年3月9日 下午4:11:56
	 * @throws
	 */
	function getAutoFormTabsForAll(){
		$action = $_POST['action'];
		$main = $_POST['main'];
		$val = $_POST['val'];
		//数据id
		$bindrdid=$_POST['bindrdid'];
		$filedval = $_POST['filedval'];
		$formtype = $_POST['formtype'];
	
	
		$mainBindActionCondition = '';
		if($main){
			$mainBindActionCondition='/main/'.$main;
		}
		/**
		 * 这个函数的调用限制。
		 * 传入的action为当前父级，必须为列表型表单。
		 * 获取它的子项
		 */
		$ret = array();
		$ret['code'] = 1;
		$ret['msg'] = '操作成功';
		// 		if(empty($main)){
		// 			$ret['code'] = 0;
		// 			$ret['msg'] = '没有入口表单信息';
		// 		}
		if(empty($val)){
			$ret['code'] = 0;
			$ret['msg'] = '关联值为空，不可查询！';
		}
		if(empty($filedval)){
			$ret['code'] = 0;
			$ret['msg'] = '关联字段为空，不可查询！';
		}
	
		$model = M('mis_auto_bind');
		$MisSystemDataRoamingModel=D("MisSystemDataRoaming");
		// 获取当前表单的子级表单
		//$sql = "SELECT bindaname , inbindtitle, inbindaname , bindtype , typeid ,bindresult , bindval,inbindval FROM mis_auto_bind WHERE  bindaname ='{$action}' AND `level` = (SELECT id FROM `mis_dynamic_form_manage` WHERE actionname='{$action}') order by inbindsort asc";
		$sql = "SELECT bindaname , inbindtitle, inbindaname , bindtype , typeid ,bindresult , bindval,inbindval,dataroamid,bindconlistArr,inbindmap FROM mis_auto_bind WHERE  bindaname ='{$action}'  order by inbindsort asc";
		$childArr = $model->query($sql);
		//获取当前表单数据
		$actionModel=D($action);
		$vo=$actionModel->where("status=1 and id={$bindrdid}")->find();
		$retTableInfoArr = array();
		$chilAction = '';
		foreach ($childArr as $ckey=>$cval){
			// 这里存在问题，  另一种类型的表单没有了，。
			if($cval['typeid']==2){ //套表管理
				unset($tempArr);
				if($cval['bindtype']==0){
					//查询对应表单数据
					$map=array();
					$model = D($cval['inbindaname']);
					$tempData=array();
					$tempMap=array();
					$tempMap[$cval['inbindval']]=$val;
					$tempMap['status']=1;
					//$tempMap['relationmodelname']=$cval['bindaname'];
					//加上数据漫游条件
					$dataArr=$MisSystemDataRoamingModel->main(2,$action,$_POST['bindrdid'],4,$cval['inbindaname'],'',$cval['dataroamid']);
					if(is_array(reset($dataArr))){
						foreach(reset($dataArr) as $targettable=>$data){
							foreach($data as $k => $v){
								if(reset($v)){
									$tempMap[key($v)]=reset($v);
								}
							}
						}
					} 
					$tempData = $model->where($tempMap)->order('id desc')->find();
					logs($model->getlastsql(),"taobiaominisql");
					$tempArr['id'] = $tempData['id'];
					if($tempData['id']){
						$tempArr['id'] =$tempData['id'];
					}else{
						$tempArr['id'] =-1;
					}
					$tempArr['actionname'] =$cval['inbindaname'];
					$tempArr['typeid']=2;
				}else{
					$tempArr['actionname'] =$cval['inbindaname'];
					$tempArr['typeid']=2;
				}
				$tempArr['bindtype']=$cval['bindtype'];
				$tempArr['fieldtype']=$cval['inbindval'];
				$tempArr['inbindtitle']=$cval['inbindtitle'];
				$retTableInfoArr[] = $tempArr;
			}else if($cval['typeid']==0){
				//组合表单类型
				if($cval['bindval'] == $filedval){
					unset($tempArr);
					if($cval['bindtype']==0){
						//根据传过来的orderno 查询对应表单
						$map=array();
						$model = D($cval['inbindaname']);
						//获取真实数据表名
						$tempData=array();
						$tempMap=array();
						//$tempMap['bindid']=$val;
						$tempMap['status']=1;
						//$tempMap['relationmodelname']=$cval['bindaname'];
						//加上数据漫游条件
// 						$dataArr=$MisSystemDataRoamingModel->main(2,$action,$_POST['bindrdid'],4,$cval['inbindaname'],'',$cval['dataroamid']);
// 						if(is_array(reset($dataArr))){
// 							foreach(reset($dataArr) as $targettable=>$data){
// 								foreach($data as $k => $v){
// 									if(reset($v)){
// 										$tempMap[key($v)]=reset($v);
// 									}
// 								}
// 							}
// 						} 
						//表单条件
						if($cval['inbindmap']){
							$newconditions = str_replace ( array ( '&quot;', '&#39;', '&lt;','&gt;'), array ('"',"'",'<','>'
							), $cval['inbindmap'] );
							$tempMap['_string']=$newconditions;
						}
						$bindconlistArr=array();
						//表单附加条件
						if($cval['bindconlistArr']){
							$bindconlistArr=unserialize($cval['bindconlistArr']);
							foreach ($bindconlistArr as $bindkey=>$bindval){
								//如果有值才生成查询
								if($vo[$bindkey]){
									$tempMap[$bindval]=$vo[$bindkey];
								}
							}
						}
						$tempData = $model->where($tempMap)->order('id desc')->find();
						logs($model->getlastsql(),"zuminisql");
						$tempArr['actionname'] =$cval['inbindaname'];
						$tempArr['bindtype']=$cval['bindtype'];
						if($tempData['id']){
							$tempArr['id'] =	$tempData['id'];
						}else{
							$tempArr['id'] =-1;
						}
					}else{
						$tempArr['actionname'] =$cval['inbindaname'];
					}
					$retTableInfoArr[] = $tempArr;
				}
			}
			/*elseif($cval['typeid']==1){
			 // 主从表单
			 //1.得到主表的ID值，
			 //2.以主表的I为条件，在子表中查询bindid等主表ID的ID值
			 $model = D($cval['inbindaname']);
			 $tempData = $model->where("bindid='".$val."' and status=1")->find();
	
			 $retTableInfoArr[] = $acitonList;
				}*/
		}
		$chilAction = $action;
		$retArr = array();
		// 		foreach ($retTableInfoArr as $akey=>$aval){
		// 			$model=D($aval['inbindaname']);
		// 			$vo=$model->where("bindid='".$val."'")->find();
		// 			$retArr[$aval['inbindaname']] = $vo;
		// 		}
		// <div id="tabsContent" class="tabs proTabNav" currentindex="0" eventtype="click">
		$tablesHtml=<<<EOF
	<div class="tabsHeader proNavHeader">
		<div class="tabsHeaderContent proTabNavHeaderContent ">
			<ul>
EOF;
		$selected="";
		$footerhtml="<div class=\"tabsContent tml-p0\">";
		//此处已写死orderno
		//$resultid=getFieldBy($val, "orderno", "id", $action);
		$resultid=$_POST['bindrdid']?$_POST['bindrdid']:'0';
		$i=0;
		foreach ($retTableInfoArr as $rkey=>$rval){
			if($i==0){
				$selected="selected='selected'";
				$block="display: block";
			}else{
				$selected="";
			}
			$inbindtitle=getFieldBy($rval['actionname'], "actionname", "actiontitle", "mis_dynamic_form_manage");
			if($rval['inbindtitle']){
				$inbindtitle=$rval['inbindtitle'];
			}
			//存在id时为表单类型
			if($rval['id']){
				if($rval['typeid']==2){
					//没有找到该数据新增页面
					if($rval['id']==-1){
						//add页面
						$tablesHtml.="<li {$selected}><a id=\"{$rval['actionname']}_#tagitem#\" class=\"j-ajax\" rel=\"{$main}{$rval['actionname']}_add\" href=\"".__APP__."/{$rval['actionname']}/add/fieldtype/{$rval['fieldtype']}/{$rval['fieldtype']}/{$val}/bindaname/{$action}{$mainBindActionCondition}\">";
					}else{
						//edit页面
						$tablesHtml.="<li {$selected}><a id=\"{$rval['actionname']}_#tagitem#\" class=\"j-ajax\" rel=\"{$main}{$rval['actionname']}_edit\" href=\"".__APP__."/{$rval['actionname']}/edit/fieldtype/{$rval['fieldtype']}/{$rval['fieldtype']}/{$val}/id/{$rval['id']}/bindaname/{$action}{$mainBindActionCondition}\">";
					}
				}else{
					if($formtype=="edit"&&$rval['id']==-1){
						$tablesHtml.="<li {$selected}><a id=\"{$rval['actionname']}_#tagitem#\" class=\"j-ajax\" rel=\"{$main}{$rval['actionname']}_edit\" href=\"".__APP__."/{$rval['actionname']}/add/bindid/{$val}/bindrdid/{$resultid}/bindaname/{$action}{$mainBindActionCondition}\">";
					}else{
						$tablesHtml.="<li {$selected}><a id=\"{$rval['actionname']}_#tagitem#\" class=\"j-ajax\" rel=\"{$main}{$rval['actionname']}_edit\" href=\"".__APP__."/{$rval['actionname']}/{$formtype}/id/{$rval['id']}/bindaname/{$action}{$mainBindActionCondition}\">";
					}
				}
				$tablesHtml.="<span>".$inbindtitle."</span>";
				$tablesHtml.="</a></li>";
				$footerhtml.="<div id=\"{$main}{$rval['actionname']}_edit\" style=\"{$block}\"></div>";
			}else{
				$viewtype="";
				//如果是修改页面，添加过滤按钮条件
				if($formtype=="view"){
					$viewtype="/minitype/1";
				}
				if($rval['typeid']==2){
					$tablesHtml.="<li {$selected}><a id=\"{$rval['actionname']}_#tagitem#\" class=\"j-ajax\" rel=\"{$main}{$rval['actionname']}_edit\" href=\"".__APP__."/{$rval['actionname']}/miniindex/func/edit/bindtype/{$rval['bindtype']}/bindaname/{$action}/bindrdid/{$resultid}/fieldtype/{$rval['fieldtype']}/{$rval['fieldtype']}/{$val}{$mainBindActionCondition}{$viewtype}\">";
				}else{
					$tablesHtml.="<li {$selected}><a id=\"{$rval['actionname']}_#tagitem#\" class=\"j-ajax\" rel=\"{$main}{$rval['actionname']}_edit\" href=\"".__APP__."/{$rval['actionname']}/miniindex/func/edit/bindtype/{$rval['bindtype']}/bindrdid/{$resultid}/bindaname/{$action}/bindid/{$val}{$mainBindActionCondition}{$viewtype}\">";
				}
				$tablesHtml.="<span>".$inbindtitle."</span>";
				$tablesHtml.="</a></li>";
				$footerhtml.="<div id=\"{$main}{$rval['actionname']}_edit\" style=\"{$block}\"></div>";
			}
				
			if(!$selected){
				//$footerhtml.="</div>";
			}
			$i++;
		}
	
		$tablesHtml.="</ul></div>";//</div>";
		$footerhtml.="</div></div></div>";//</div>";
		$tablesHtml.=$footerhtml;
		$ret['data'] = array($chilAction=>$tablesHtml);
		echo json_encode($ret);
	}
	/**
	 * @Title: nvigateTO
	 * @Description:点击group组，组合2级和3级菜单
	 * @author liminggang
	 * @date 2014-10-13 下午8:22:48
	 * @throws
	 */
	function nvigateTO(){
		//对菜单栏打开了导航的工具栏分组(一级菜单)在面板表(mis_system_panel)查询具体的数据并重组
		//左侧栏节点(二三级菜单)
		//第一步。获取group的id
		$this->randNumExecFunc();
		$groupid = $_REQUEST["groupid"];
		//获取节点
		$model = D("Public");
		$accessNode = $model->menuLeftTree($groupid);
		$panel = A('MisSystemPanel');
		$_GET['nvigetTo']=true;
		$panel->lookupSysPanel($groupid);
 		$this->assign("accessNode",$accessNode);
		$this->display("Public:nvigateTO");
	}
	
// 	function _empty() {
// 		header('HTTP/1.1 404 Not Found');
// 		$this->display('Public:404');
// 	}

	public function DT_uploadnew() {
		$subid=$_POST['rel_subid'];
		$index=$_POST['rel_index'];
		$type=$_POST['rel_type'];
		$tableid=$_POST['rel_tableid'];
		$tablename=$_POST['rel_tablename'];
		$fieldname=$_POST['rel_fieldname'];
		$recolist = $this->getDTAttachedRecordList($tableid,$tablename,$subid,$fieldname);
		if( ! empty($recolist)){
			foreach($recolist as $k => $v){
				$recolist[$k]["createdate"] = date("Y-m-d H:i:s",$v["createtime"]);
				$deptid = getFieldBy($v["createid"], "id", "dept_id", "user");
				$zhname = getFieldBy($v["createid"], "id", "zhname", "user");
				$recolist[$k]["userzhname"] = $zhname;
				$deptname = getFieldBy($deptid, "id", "name", "mis_system_department");
				$recolist[$k]["deptname"] = $deptname;
				if($deptname){
					$recolist[$k]["upuserinfo"] = $zhname."[".$deptname."]";
				}else{
					$recolist[$k]["upuserinfo"] = $zhname;
				}
	
			}
		}
		$list = $_POST["list"]?$_POST["list"]:array();
		$recolist = $recolist?$recolist:array();
		$list = array_merge($recolist,$list);
		$this->assign("rand",rand(100000,999999));
		$this->assign("recolist",$recolist);
		$this->assign("objId",$_POST["id"]);
		$this->assign("name",$_POST['rel_name']);
		$this->assign("index",$index);
		$this->assign("type",$type);
		$this->assign("list",$list);
		$this->display('Public:dtuploadnew');
	}
	
	function DT_swf_upload($arr){
		$attModel = D("MisAttachedRecord");
		//临时文件夹里面的文件转移到目标文件夹
		foreach($arr as $k=>$v){
			foreach($v["file"] as $kk => $vv){
				$file = explode("###",$vv);
				$fileinfo=pathinfo($file[0]);
				$from = UPLOAD_PATH_TEMP.$file[0];//临时存放文件
				if( file_exists($from) ){
					$p=UPLOAD_PATH.$fileinfo['dirname'];// 目标文件夹
					if( !file_exists($p) ) $this->createFolders($p); //判断目标文件夹是否存在
					$to = UPLOAD_PATH.$file[0];
					rename($from,$to);
					//保存附件信息
					$data=array();
					$data['tablename'] = $v["tablename"];
					$data['tableid']=$v["tableid"];
					$data['subid']=$v["subid"];
					$data['attached']= $file[0];
					$data['isfile']= 0;
					$data['fieldname']= $v["fieldname"];
					$data['upname']=$file[1];
					$data['createtime'] = time();
					$data['createid'] = $_SESSION[C('USER_AUTH_KEY')]?$_SESSION[C('USER_AUTH_KEY')]:0;
					$rel=$attModel->add($data);
					if(!$rel){
						//$this->error("附件上传失败，请联系管理员！");
					}
				}
			}
		}
	}
	
	//获取内嵌表附件
	public function getDTAttachedRecordList($tableid,$tablename='',$subid=0,$fieldname,$online=true,$archived=true){
		$armodel = D('MisAttachedRecord');
		$armap['tableid'] = $tableid;
		$armap['subid'] = $subid;
		$armap['status'] = 1;
		$armap['fieldname'] = $fieldname;
		$armap['tablename'] = $tablename;
	
		$attarry = $armodel->where($armap)->select();
		$filesArr = array('pdf','doc','docx','xls','xlsx','ppt','pptx','txt','jpg','jpeg','gif','png','apk');
		foreach ($attarry as $key => $val) {
			$pathinfo = pathinfo($val['attached']);
			//获取除后缀的文件名称
			$upname = missubstr($val['upname'],18,true).".".$pathinfo['extension'];
			if (in_array(strtolower($pathinfo['extension']), $filesArr)) {
				//在线查看，必须是指定的文件类型，才能在线查看。
				$attarry[$key]['online'] = $online;  //在线查看按钮。
			}
			//URL传参。一定要将base64加密后生成的  ‘=’ 号替换掉
			$attarry[$key]['name'] = str_replace("=", '', base64_encode($val['attached']));
			//文件显示名称
			$attarry[$key]['filename'] = $upname;
			//文件下载名称
			$attarry[$key]['lookname'] = $val['upname'];
			//任何文件都可以归档
			$attarry[$key]['archived'] = $archived; //归档按钮
		}
		$uploadarry=array();
		foreach ($attarry as $akey=>$aval){
			$uploadarry[$aval['fieldname']][]=$aval;
		}
		return $attarry;
	}
	//临时表存储历史记录
	//这里用来存储临时缓存文件
	/**
	 * @Title: nvigateTO
	 * @Description:临时表存储历史记录
	 * @author liminggang
	 * @date 2014-10-13 下午8:22:48
	 * @paramate $model mix  $pkval int
	 * @return $updateBackup(临时表关键查找信息);
	 * @throws
	 */
	protected function setOldDataToCache($modelobj,$modelname,$pkval,$sqltype){
		/*
		 * 1、明确插入表数据
		 * 2、明确当前分表对象，并核验表是否存在
		 * */
		$updateTable=$modelobj->getTableName();
		//$randtable="update_last_cache".mt_rand(1,5);//选择对应的内存表，减少并发压力
		$randtable="update_last_cache".(fmod($pkval,10)+1);//通过当前数据id除以分表数量的余数来确定缓存哪张表,被除数分表开关
// 		$cacheTable=M()->execute("CREATE TABLE if not exists `".$randtable."` (
// 					  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
// 					  `backupdata` varchar(10240) DEFAULT NULL COMMENT '修改前记录',
// 				      `tablename` varchar(100) DEFAULT NULL COMMENT '表名',
// 					  `tableid` int(11) DEFAULT NULL COMMENT '对应ID',
// 				      `sqltype` varchar(10) DEFAULT NULL COMMENT 'CURD类型',
// 					  `randnum` int(4) DEFAULT NULL COMMENT '随机数',
// 					  `createid` int(10) DEFAULT NULL COMMENT '创建人',
// 					  PRIMARY KEY (`id`)
// 					) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='漫游累加累减缓存表';");
		
// 		if($cacheTable===false){
// 			$msg='建立数据缓存表错误';
// 			throw new NullDataExcetion($msg);
// 		}
		//核验数据是否存在，不存在则插入，存在则跳过
		$upmodel=M($randtable);
		$result=$upmodel->query("select * from ".$randtable." where tablename='".$updateTable."' and  tableid='".$pkval."';");
		if($result===false){
// 			echo M()->getLastSql();
			$msg='元数据查询错误,[error]'.$upmodel->getDBError();
			throw new NullDataExcetion($msg);
// 			$this->error("元数据查询错误");
		}elseif($result){
			//如果已经存在了记录
// 			$updateSave=$modelobj->where('id='.$pkval)->find();
// 			$updateSave= base64_encode(serialize($updateSave));		
// 			//$randnum=mt_randa(1000,9999);//随机数号；通过表名+随机数+操作人锁定唯一的记录
// 			M()->execute("INSERT INTO  ".$randtable." (`tablename`,`tableid`, `sqltype`,`backupdata`,`randnum`,`createid`)VALUES('".$updateTable."','".$pkval."','".$sqltype."','".$updateSave."','".$randnum."','".$_SESSION[C('USER_AUTH_KEY')]."');");
// // 			echo M()->getLastSql();
			if($sqltype=='delete' || $sqltype=='lookupUpdateProcess'){
				$updateSave=$modelobj->where('id='.$pkval)->find();
				//查询数据表结构 过滤字段
				$getFieldList=D('Dynamicconf')->getTableInfo($updateTable);
				foreach ($updateSave as $uk=>$uv){
					//查询字段在$getFieldList里面的数据类型
					$COLUMN_TYPE=$getFieldList[$uk]['COLUMN_TYPE'];
					$rule  = "/^\int\(.*\)/";
					$rule1  = "/^\decimal\(.*\)/";
					$rule2  = "/^\float\(.*\)/";
					$result=preg_match($rule,$COLUMN_TYPE , $match);
					$result1=preg_match($rule1,$COLUMN_TYPE , $match1);
					$result2=preg_match($rule2,$COLUMN_TYPE , $match2);
					if($result==0 && $result1==0 && $result2=0){	
						unset($updateSave[$uk]);
					}
				}
				
				$updateSave= base64_encode(serialize($updateSave));
				$upmap['tablename']=$updateTable;
				$upmap['tableid']=$pkval;
				$updata['backupdata']=$updateSave;
				$uplist=$upmodel->where($upmap)->save($updata);
				if($uplist===false){
					$msg='数据更新错误,[error]'.$upmodel->getDBError();
					throw new NullDataExcetion($msg);
				}
			}
			$updateBackup=array('randtable'=>$randtable,'tablename'=>$updateTable,'tableid'=>$pkval,'randnum'=>$result[0]['randnum'],'createid'=>$result[0]['createid']);
			return $updateBackup;
		}else{
			//如果不存在记录
			if($sqltype=='insert'){
				$updateSave=null;
			}else{
				$updateSave=$modelobj->where('id='.$pkval)->find();
			}
			//查询数据表结构 过滤字段
			$getFieldList=D('Dynamicconf')->getTableInfo($updateTable);
			foreach ($updateSave as $uk=>$uv){
				//查询字段在$getFieldList里面的数据类型
				$COLUMN_TYPE=$getFieldList[$uk]['COLUMN_TYPE'];
				$rule  = "/^\int\(.*\)/";
				$rule1  = "/^\decimal\(.*\)/";
				$rule2  = "/^\float\(.*\)/";
				$result=preg_match($rule,$COLUMN_TYPE , $match);
				$result1=preg_match($rule1,$COLUMN_TYPE , $match1);
				$result2=preg_match($rule2,$COLUMN_TYPE , $match2);
				if($result==0 && $result1==0 && $result2=0){
					unset($updateSave[$uk]);
				}
			}
			$updateSave= base64_encode(serialize($updateSave));
			//$randnum=mt_randa(1000,9999);//随机数号；通过表名+随机数+操作人锁定唯一的记录
			$uplist=$upmodel->execute("INSERT INTO  ".$randtable." (`tablename`,`tableid`, `sqltype`,`backupdata`,`randnum`,`createid`)VALUES('".$updateTable."','".$pkval."','".$sqltype."','".$updateSave."','".$randnum."','".$_SESSION[C('USER_AUTH_KEY')]."');");
			if($uplist===false){
				$msg='数据插入错误,[error]'.$upmodel->getDBError();
				throw new NullDataExcetion($msg);
			}
			$updateBackup=array('randtable'=>$randtable,'tablename'=>$updateTable,'tableid'=>$pkval,'randnum'=>$randnum,'createid'=>$_SESSION[C('USER_AUTH_KEY')]);
			return $updateBackup;
		}
	}
	/**
	 * @Title: nvigateTO
	 * @Description:临时表存储历史记录
	 * @author liminggang
	 * @date 2014-10-13 下午8:22:48
	 * @paramate $model mix  $pkval int
	 * @return $updateBackup(临时表关键查找信息);
	 * @throws
	 */
	static private function getOldDataToCache($updateBackup){
		//这里清除内存表缓存的修改前数据代码
		//M()->execute("DELETE FROM  ".$randtable." WHERE randnum='".$randnum."' and createid='".$_SESSION[C('USER_AUTH_KEY')]."';");
		//echo M()->getLastSql();
	}
	/**
	 * @Title: nvigateTO
	 * @Description:清除临时表存储历史记录
	 * @author liminggang
	 * @date 2014-10-13 下午8:22:48
	 * @paramate $updateBackup array
	 * @return 无返回值;
	 * @throws
	 */
	 private function unsetOldDataToCache($updateBackup){	
		$randnum=$updateBackup['randnum'];
		$randtable=$updateBackup['randtable'];
		$createid=$updateBackup['createid'];
		$tablename=$updateBackup['tablename'];
		$tableid=$updateBackup['tableid'];
		//这里清除内存表缓存的修改前数据代码
// 		M()->execute("DELETE FROM  ".$randtable." WHERE randnum='".$randnum."' and createid='".$_SESSION[C('USER_AUTH_KEY')]."';");
		M()->execute("DELETE FROM  ".$randtable." WHERE tablename='".$tablename."' and tableid='".$tableid."';");
		//echo M()->getLastSql();
	}
	
	/**
	 * lookupcomboxgetTableField 得到表字段
	 * @param xiayuqin
	 * 2015-4-14
	 *  $modelName 模型名称 $table表名 (一个就可以)
	 * @return string
	 */
	function lookupcomboxgetTableField($modelName, $tableName,$t='' ){
		$model=M();
		if($tableName){
			$tablename=$tableName;
		}else{
			$tablename=D($modelName)->getTableName();
		}
		if( $t=="" ){
			$table = $this->escapeStr($tablename);
		}else{
			$table = $t;
		}
		$arr=array(array("","请选择字段"));
		$arr=array();
		if ($table!='') {
			$columns = $model->query("show columns from ".$table);
			$model2=M("INFORMATION_SCHEMA.COLUMNS","","",1);
			$columnstitle = $model2->where("table_name = '".$table."' AND TABLE_SCHEMA = '".C('DB_NAME')."'")->getField("COLUMN_NAME,COLUMN_COMMENT");
			foreach($columns as $k=>$v){
				$title=$v['Field'];
				if( $columnstitle[$v['Field']] ) $title=$columnstitle[$v['Field']];
				$arr[$v['Field']] = $title;
			}
		}
		//file_put_contents('a.txt',json_encode($arr));
		return $arr;
		/* if( $t=="" ){
		 echo json_encode($arr);
		}else{
		return $arr;
		} */
	}
	
	/**
	 * ajax获取文本框的固定显示-动态取值功能
	 * @Title: lookupAjaxAppendCondtionData
	 * @Description: todo(动态取值功能)   
	 * @author quqiang 
	 * @date 2015年4月28日 上午11:06:46 
	 * @throws
	 */
	function lookupAjaxAppendCondtionData(){
		$table 	=	$_POST['table'];		// 数据检索表名
		$field	= 	$_POST['field'];		// 数据检索字段名
		$action = $_POST['action'];	//	当前操作action名
		$key = $_POST['key'];			//	当前操作的字段名
		
		$condition	=	html_entity_decode($_POST['condition']);
		$condition = str_replace('&#39;' , "'" , $condition);
		if($table && $field){
			$obj = D($table);
			if(!$condition){
				$condition= '1=1';
			}
			$fieldVal = $obj->where($condition)->getField($field , 1);
			// 配置文件数据转换
			if($fieldVal && $action && $key){
				$data['debug_3'] = '进入格式换算！';
				$scdmodel = D('SystemConfigDetail');
				//读取列名称数据(按照规则，应该在index方法里面)
				$detailList = $scdmodel->getDetail($action , false);
				$data['debug_5'] = '测试配置：'.arr2string($detailList);
				if(is_array($detailList) && $detailList[$key]){
					$data['debug_4'] = '允许格式换算！';
					$funcName 	= $detailList[$key]['func'];
					$funcDataSouce 	= $detailList[$key]['funcdata'];
					// 排除子表时的特殊处理。直接取得数据中的最后一项
					$funcName = $funcName[count($funcName)-1];
					$funcData = $funcDataSouce[count($funcDataSouce)-1];
					switch ($funcName[0]){
						case 'unitExchange':
							$funcData[0][3]='2';
							break;
					}
					$fieldVal = getConfigFunction($fieldVal , $funcName ,$funcData );
					$data['debug_0'] = arr2string($funcName).'_'.arr2string($funcData);
				}
				
			}
			$data['debug_1'] = $action.'_'.$key.'_'.$fieldVal;
			$data[$field] = $fieldVal;
			$data['sql'] = $obj->getLastSql();
			echo json_encode($data);
		}
	}
	/**
	 *
	 * @Title: genRandomString
	 * @Description: todo(生成任意位字符随机数)
	 * @param unknown_type $len
	 * @return string
	 * @author renling
	 * @date 2014-9-25 上午11:23:35
	 * @throws
	 */
	public function genRandomString($len){
		$chars = array(
				"a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
				"l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
				"w", "x", "y", "z");
		$charsLen = count($chars) - 1;
		shuffle($chars);    // 将数组打乱
		$output = "";
		for ($i=0; $i<$len; $i++){
			$output .= $chars[mt_rand(0, $charsLen)];
		}
		return $output;
	}
	
	
	
	
	/**
	 *
	 * @Title: SaveVersionWord
	 * @Description: todo(生成任意位字符随机数)
	 * @param int		$id		操作数据ID
	 * @param string	$name	当前操作表单Model名称
	 * @param boolean	$isrelation	是否为直接输出错误信息，false：直接输出，true：异常抛出, 默认false
	 * @param boolean   $return 是否返回数据 默认为false 
	 * @author xiayuqin
	 * @date 2015-6-27 上午11:23:35
	 * @throws
	 */
	public function SaveVersionWord($id,$name,$isrelation=false,$return=false){
		$id = $id;
		if(!$id){
			if(!$isrelation){
				$this->error('数据版本错误，ID不存在！');
			}else{
				$msg = "数据版本错误，ID不存在！";
				throw new NullPointExcetion($msg);
			}
		}
			
		if(empty($name)){
			$name = $this->getActionName();
		}
		$list = $this->get_export_label($id,$name);
		//查询节点下面的审批记录
		$ProcessInfoHistoryModel=D('ProcessInfoHistory');
		//先查询启动流程的意见
		$HistoryMap = array();
		$HistoryMap['tableid']=$id;
		$HistoryMap['tablename']=$name;
		$HistoryMap['dotype']=2; //流程启动
		$History=$ProcessInfoHistoryModel->where($HistoryMap)->order("id asc")->field('id,tableid,tablename,ostatus,dotime,doinfo,userid')->find();
		//获取后台用户名称
		$userid = "";
		$time = "";
		if($History){
			$userid = getFieldBy($History['userid'], 'id', 'name', 'user');
			$time = transTime($History['dotime'],'Y/m/d H:i');
		}
		$prorelaName = "制单节点";
		//查询当前流程开始节点名称
		$process_info = M("process_info");
		$promap = array();
		$promap['nodename'] = $name;
		$promap['default'] = 1;
		$pinfoid = $process_info->where($promap)->getField("id");
		if($pinfoid){
			//实例化节点表
			$process_relation = M("process_relation");
			$promap = array();
			$promap['tablename'] = "process_info";
			$promap['pinfoid'] = $pinfoid;
			$promap['flowtype'] = 0; //开始节点
			//获取开始节点名称
			$prorelaName = $process_relation->where($promap)->getField("name");
		}
		//模式一 begin
		$newHinfo = array();
		//节点名称
		$newHinfo['createname']['original'][] = $prorelaName;
		$newHinfo['createname']['value'][] = $prorelaName;
		//审核意见
		$newHinfo['createdoinfo']['original'][] = $History['doinfo'];
		$newHinfo['createdoinfo']['value'][] = $History['doinfo'];
		//审核时间
		$newHinfo['createdotime']['original'][] = $time;
		$newHinfo['createdotime']['value'][] = $time;
		//审核人
		$newHinfo['createuser']['original'][] = $userid;
		$newHinfo['createuser']['value'][] = $userid;
		//模式一  end
		
		//模式二   begin
		$list["createname"] = array('name'=>"createname",'showname'=>'','original'=>'','value'=>$prorelaName);
		$list["createdoinfo"] = array('name'=>"createdoinfo",'showname'=>'','original'=>'','value'=>$History['doinfo']);
		$list["createdotime"] = array('name'=>"createdotime",'showname'=>'','original'=>'','value'=>$time);
		$list["createuser"] = array('name'=>"createuser",'showname'=>'','original'=>'','value'=>$userid);
		//模式二  end
		//流程启动节点拼装完成
		
		//拼装流程审核节点意见---- 不管意见是否存在。节点内容全部输出。满足表格形似的意见
		$sql = "SELECT flowid,process_relation_form.`name` AS name,user.`name` AS username,doinfo,dotime FROM process_relation_form
		LEFT JOIN `process_info_history` ON process_relation_form.`id` = process_info_history.`ostatus`
		LEFT JOIN `user` ON process_info_history.`userid` = user.`id`
		WHERE process_relation_form.`tablename` = '{$name}' AND process_relation_form.`tableid`={$id} 
		AND process_relation_form.`doing`=  1 AND process_relation_form.catgory = 0 AND process_relation_form.flowtype in (2,3)";
		$Mdl = M();
		//查询流程审核节点信息
		$bach = array();
		$relation_formlist = $Mdl->query($sql);
		foreach ($relation_formlist as $rk=>$rv){
			if(in_array($rv['flowid'], array_keys($bach))){
				$bach[$rv['flowid']]+=1;
			}else{
				$bach[$rv['flowid']] =0;
			}
			$time = "";
			if($rv['dotime']){
				$time = transTime($rv['dotime'],'Y/m/d H:i');
			}
			//如果为0 则标记没有
			$num = $bach[$rv['flowid']]>0?$bach[$rv['flowid']]:'';
			$list[substr(md5($rv['flowid']."HS_name"),0,8).$num] = array('name'=>substr(md5($rv['flowid']."HS_name"),0,8).$num,'showname'=>'','original'=>'','value'=>$rv['name']);
			$list[substr(md5($rv['flowid']."HS_doinfo"),0,8).$num] = array('name'=>substr(md5($rv['flowid']."HS_doinfo"),0,8).$num,'showname'=>'','original'=>'','value'=>$rv['doinfo']);
			$list[substr(md5($rv['flowid']."HS_dotime"),0,8).$num] = array('name'=>substr(md5($rv['flowid']."HS_dotime"),0,8).$num,'showname'=>'','original'=>'','value'=>$time);
			$list[substr(md5($rv['flowid']."HS_userid"),0,8).$num] = array('name'=>substr(md5($rv['flowid']."HS_userid"),0,8).$num,'showname'=>'','original'=>'','value'=>$rv['username']);
			//瓶装表格审批数据
			//节点名称
			$newHinfo['createname']['original'][] = $rv['name'];
			$newHinfo['createname']['value'][] = $rv['name'];
			//审核意见
			$newHinfo['createdoinfo']['original'][] = $rv['doinfo'];
			$newHinfo['createdoinfo']['value'][] = $rv['doinfo'];
			//审核时间
			$newHinfo['createdotime']['original'][] = $time;
			$newHinfo['createdotime']['value'][] = $time;
			//审核人
			$newHinfo['createuser']['original'][] = $rv['username'];
			$newHinfo['createuser']['value'][] = $rv['username'];
		}
 
		$tableInfo = array(
				'0'=>array(
					'name'=>'0',
					'is_stats'=>0,
					'func'=>'',
					'funcdata'=>'',
					'original'=>$newHinfo['createname']['original'],
					'value'=>$newHinfo['createname']['value'],
				),
				'1'=>array(
						'name'=>'1',
						'is_stats'=>0,
						'func'=>'',
						'funcdata'=>'',
						'original'=>$newHinfo['createdoinfo']['original'],
						'value'=>$newHinfo['createdoinfo']['value'],
				),
				'2'=>array(
					'name'=>'2',
					'is_stats'=>0,
					'func'=>'',
					'funcdata'=>'',
					'original'=>$newHinfo['createuser']['original'],
					'value'=>$newHinfo['createuser']['value'],
				),
				'3'=>array(
						'name'=>'3',
						'is_stats'=>0,
						'func'=>'',
						'funcdata'=>'',
						'original'=>$newHinfo['createdotime']['original'],
						'value'=>$newHinfo['createdotime']['value'],
				),
		);
		$hisarr = array();
		//构造整个审核意见框
		$hisarr = array(
				'name'=>'doinfoval',
				'is_datatable'=>1,
				'title_none'=>1,
				'showname'=>'审核意见',
				'value'=>$tableInfo,
		);
		$list['doinfoval'] = $hisarr;
		if($return){
			return $list;
		}else{
			//获取当前模型的名称，得到版本控制
			$actionName=getFieldBy($name, 'name', 'title', 'node');
			$savewordModel=M('mis_system_template_saveword');
			$savewordMap['modelid']=$id;
			$savewordMap['modelname']=$name;
			$savecount = $savewordModel->where ( $savewordMap )->count ( '*' );
			if($savecount){
				$savecount+=1;
				$savename=$actionName."(".$savecount.")";
			}else{
				$savename=$actionName."(1)";
			}
			$wordData['modelid']=$id;
			$wordData['modelname']=$name;
			$wordData['fileurl']=null;
			$wordData['swfurl']=null;
			$wordData['filename']=$savename.date('Y-m-d H:i:s',time());
			$wordData['isexport']=0;
			$wordData['createtime']=time();
			$wordData['type']=0;
			$query=$savewordModel->add($wordData);
			if($query==false){
				if(!$isrelation){
					$this->error('版本控制保存失败');
				}else{
					$msg = "版本控制保存失败 [sql error]".$savewordModel->getError ().'[action]'.$name;
					throw new NullPointExcetion($msg);
				}
			}
			//数据采用文件缓存方式。文件目录在Dynamicconf/FormListData/$name/$name.$id
			$FormListDataModel = D("FormListData");
			$FormListDataModel->SetRules($list,$name,$name.$query);
		}
	}
	
	/**
	 * @Title: lookupWordChoice 
	 * @Description: 导出word跳转模板，选择导出的word版本数据
	 * @author 王昭侠
	 * @date 2015-6-29 下午5:08:45 
	 * @throws
	 */
	public function  lookupWordChoice(){
		//获取当前数据ID
		$id = $_REQUEST['id'];
		//获取当前模型名称
		$name = $this->getActionName();
		$this->assign("name",$name);
		//实例化模板word版本数据信息模型
		$templateModel = M("mis_system_template_saveword");
		//查询当前模型和当前数据ID是否存在word版本数据。
		$template["modelid"] = $id;
		$template["modelname"] = $name;
		$temList=$templateModel->where($template)->order("id desc")->select();
		$this->assign('temList',$temList);
		$this->assign('id',$id);
		$this->display('Common:wordChoice');
	}
	
	/**
	 * @Title: export_word_one
	 * @Description: todo(导出word方法  new)
	 * @author xiayuqin
	 * @date 2015-6-29 下午5:00:51
	 * @throws
	 */
	public function export_word_one(){
		set_time_limit(0);
		//获取导出格式
		if($_REQUEST['export']){
			$export = $_REQUEST['export'];
		}else{
			$export = 'word';
		}
		if($export == 'swf'){
			//在线查看最新版本
			if($_REQUEST['fileid']){
				$swfmap['id'] = $_REQUEST['fileid'];
			}else{
				$swfmap['modelid']=$_REQUEST['id'];
				$swfmap['modelname']=$_REQUEST['modelname'];
			}
			$templateModel = M("mis_system_template_saveword");	
			$newrs = $templateModel->where($swfmap)->order("id desc")->find();
			if($newrs['swfurl']){
				//验证swf文件是否存在
				$swffile = preg_replace("/^([\s\S]+)\/Public\//", "", $newrs['swfurl']);
				$swf=PUBLIC_PATH.$swffile;				
				if(file_exists($swf)){
					$file_path = "/Public/".$swffile;//暂时修改，等保存路径正常后直接取路径
// 					$this->assign("file_name", $newrs['filename']);
// 					$this->assign("file_type", 'file');
// 					$this->assign('file_path', $file_path);
// 					$this->display("Public:playswf");

					//$ip = gethostbyname($_SERVER['SERVER_NAME']);
					$ip = C("DB_HOST_WORD");//"192.168.0.238";
					require_once("http://{$ip}:8088/JavaBridge/java/Java.inc");//此行必须
					$PageOfficeCtrl = new Java("com.zhuozhengsoft.pageoffice.PageOfficeCtrlPHP");//此行必须
					$PageOfficeCtrl->setServerPage("http://{$ip}:8088/JavaBridge/poserver.zz");//此行必须，设置服务器页面
					java_set_file_encoding("utf8");//设置中文编码，若涉及到中文必须设置中文编码
					$PageOfficeCtrl->setJsFunction_AfterDocumentOpened("AfterDocumentOpened");
					$PageOfficeCtrl->setAllowCopy(false);//禁止拷贝
					//$PageOfficeCtrl->setMenubar(false);//隐藏菜单栏
					$PageOfficeCtrl->setOfficeToolbars(false);//隐藏Office工具条
					$PageOfficeCtrl->setCustomToolbar(false);//隐藏自定义工具栏
					//打开excel文档
					$PageOfficeCtrl->UserAgent = $_SERVER['HTTP_USER_AGENT'];//若使用谷歌浏览器此行代码必须有，其他浏览器此行代码可不加
					$OpenMode = new Java("com.zhuozhengsoft.pageoffice.OpenModeType");
					$file_path = preg_replace("/^([\s\S]+)\/Public/", "/Public/", $newrs['fileurl']);
					$PageOfficeCtrl->webOpen($file_path, $OpenMode->docReadOnly, "张三");//此行必须
					$this->assign('PageOfficeCtrl',$PageOfficeCtrl->getDocumentView("PageOfficeCtrl1"));
					$this->display("Public:showEditWord");
					exit;
				}
			}else{
				//判断word文件是否存在 ，swf文件可以根据word文件直接转换
				if($newrs['fileurl']){
					$filenameUTF8 = preg_replace("/^([\s\S]+)\/Public\//", "", $newrs['fileurl']);
					$filenameUTF8 = PUBLIC_PATH.$filenameUTF8;
					if(file_exists($filenameUTF8)){
// 						//生成PDF
// 						$HttpSoctetIOAction = A('Http');
// 						//生成文件的目标目录
// 						$causedir = UPLOAD_SampleWord.$newrs['modelname'];
// 						$filenamePDFUTF8 = $HttpSoctetIOAction->createPDF($causedir,$filenameUTF8);
// 						$swfurl = str_replace("pdf", "swf", $filenamePDFUTF8);
// 						$swfurl=preg_replace("/^([\s\S]+)\/Public\//", "", $swfurl);
// 						//将swf路径保存数据库
// 						$savewordmodel = M('mis_system_template_saveword');
// 						$wordData = array();
// 						$wordData['id'] = $newrs['id'];
// 						$wordData['swfurl'] = $swfurl;
// 						$query=$savewordmodel->save($wordData);
// 						$savewordmodel->commit();
// 						//存库结束
// 						$file_path = "/Public/".$swfurl;//暂时修改，等保存路径正常后直接取路径
// 						$this->assign("file_name", $newrs['filename']);
// 						$this->assign("file_type", 'file');
// 						$this->assign('file_path', $file_path);
// 						$this->display("Public:playswf");
// 						$ip = gethostbyname($_SERVER['SERVER_NAME']);
						$ip =C("DB_HOST_WORD");// "192.168.0.238";
						require_once("http://{$ip}:8088/JavaBridge/java/Java.inc");//此行必须
						$PageOfficeCtrl = new Java("com.zhuozhengsoft.pageoffice.PageOfficeCtrlPHP");//此行必须
						$PageOfficeCtrl->setServerPage("http://{$ip}:8088/JavaBridge/poserver.zz");//此行必须，设置服务器页面
						java_set_file_encoding("utf8");//设置中文编码，若涉及到中文必须设置中文编码
						$PageOfficeCtrl->setAllowCopy(false);//禁止拷贝
						//$PageOfficeCtrl->setMenubar(false);//隐藏菜单栏
						$PageOfficeCtrl->setOfficeToolbars(false);//隐藏Office工具条
						$PageOfficeCtrl->setCustomToolbar(false);//隐藏自定义工具栏
						$PageOfficeCtrl->setJsFunction_AfterDocumentOpened("AfterDocumentOpened");
						//打开excel文档
						$PageOfficeCtrl->UserAgent = $_SERVER['HTTP_USER_AGENT'];//若使用谷歌浏览器此行代码必须有，其他浏览器此行代码可不加
						$OpenMode = new Java("com.zhuozhengsoft.pageoffice.OpenModeType");
						$file_path = preg_replace("/^([\s\S]+)\/Public/", "/Public/", $filenameUTF8);
						$PageOfficeCtrl->webOpen($file_path, $OpenMode->docReadOnly, "张三");//此行必须
						$this->assign('PageOfficeCtrl',$PageOfficeCtrl->getDocumentView("PageOfficeCtrl1"));
						$this->display("Public:showEditWord");
						exit;
					}
				}
			}
		}else if($export == "word"){
			$map['id'] = $_REQUEST['fileid'];
			if($map['id']){
				$templateModel = M("mis_system_template_saveword");
				$rs = $templateModel->where($map)->field("fileurl")->find();
				$filenameUTF8 = preg_replace("/^([\s\S]+)\/Public\//", "", $rs['fileurl']);
				$filenameUTF8 = PUBLIC_PATH.$filenameUTF8;
				if($rs['fileurl'] && file_exists($filenameUTF8)){
					header("Cache-Control: public");
					header("Content-Type: application/force-download");
					header("Content-Disposition: attachment; filename=".basename($filenameUTF8));
					header("Content-Transfer-Encoding:­ binary");
					header("Content-Length: " . filesize($filenameUTF8));
					readfile($filenameUTF8);
					exit;
				}
			}
		}
		////////////////////////下面为解析word 或者解析pdf//////////////////////
		
		//获取文件数据ID，判定存入的表
		$id=$_REQUEST['id'];
		$modelname=$this->getActionName();
		//获取数据
		$saveWordId=$_REQUEST['fileid'];
		if($export == 'swf'){
			$saveWordId = $newrs["id"];
		}
		if($saveWordId){
			//数据采用文件缓存方式。
			$FormListDataModel = D("FormListData");
			$list = $FormListDataModel->GetRules($modelname,$modelname.$saveWordId);
		}else{
			//z这种情况主要处理内嵌表导出问题。内嵌表导出无版本控制
			$list = $this->SaveVersionWord($id,$modelname,false,true);
		}
		//end  获取数据结束
		//模板标签文件地址
		$template_word = UPLOAD_SampleWord.$modelname.".docx";
		/**
		 * 参数一、$list 导出的数据源
		 * 参数二、模板标签文件地址
		 */
		$this->export_Word($list,$template_word,false,$export,$id);
	}
	
	/**
	 * @Title: export_Word
	 * @Description: todo(导出文件)
	 * @param 导出的数据源数组 $list
	 * @param 模板标签文件地址 $template_word
	 * @param bool 默认为flase $isTemplate
	 * @author 王昭侠
	 * @date 2015-6-29 下午4:46:41
	 * @throws
	 */
	public function export_Word($list,$template_word,$isTemplate=false,$export='word',$id=null){
		set_time_limit(0);
		//获取当前控制器名称
		$name = $this->getActionName();
		//引入word操作插件
		import('@.ORG.PHPWord', '', $ext='.php');
		//获取文件后缀名
		$filetype =end(explode(".",$template_word));
		if($filetype=="doc"){
			$inputFileType = 'Word2005';
		}else if( $filetype=="docx"){
			$inputFileType = 'Word2007';
		}
		//模板标签
		$templateModel = D("MisSystemTempleteManage");
		//查询条件
		$where["modelname"] = $name;
		$temp = $templateModel->where($where)->select();
		$findList = array();
		$model = D($name);
		if($temp){
			foreach($temp as $k => $v){
				if($v["rules"]){
					$v["rules"] = str_replace("&#39;", "'", html_entity_decode($v["rules"]));					
					$v["rules"] .= " and id = ".$id;
					$map="";
					$map['_string']=$v["rules"];
					$findList = $model->where($map)->find();
					if($findList){
						$findList = $v;
						break;
					}
				}
			}
			if(empty($findList)){
				$map["zhuangtai"] = 1;
				$map["modelname"] = $name;
				$findList = $templateModel->where($map)->find();
			}
		}
		if( ! empty($findList)){
			$attachedRecord = $this->getAttachedRecordList($findList["id"],true,true,"MisSystemTempleteManage",0,false);
			if($attachedRecord){
				foreach($attachedRecord as $k => $v){
					$file = pathinfo(UPLOAD_PATH.$v["attached"]);	
					if(strtolower($file["extension"])=="docx"){
						$template_word = UPLOAD_PATH.$v["attached"];
					}
				}
			}
		}
// 		$template_word = UPLOAD_SampleWord.$name.".docx";
		if (!file_exists ($template_word)) { //模板文件不存在
			$template_word = UPLOAD_SampleWord.$name.".docx";
			$PHPWord = new PHPWord();
			$section = $PHPWord->createSection();
			$section->addText("温馨提示:模板文件不存在,请在模板管理里先上传一个模板",array('color'=>'FF0000', 'size'=>18, 'bold'=>true));
			$section->addTextBreak();
			$section->addTextBreak();
			//设置表头
			foreach($list as $k => $v){
				if(!$v["is_datatable"]){
					$text = $v["showname"]." : "."\${".$v["name"]."}";
					$section->addText($text);
				}else{
					if( ! isset($v["colORrow"])){
						$section->addTextBreak();
						$section->addTextBreak();
						$section->addText($v["showname"].":");
						$section->addText("\${".$v["name"]."}");
					}
				}
			}
	
			$objWriter = PHPWord_IOFactory::createWriter($PHPWord, $inputFileType);
			$objWriter->save($template_word);
		}
		//生成的文件名称
		$filename = time();
		//生成文件的目标目录
		$causepath = UPLOAD_SampleWord.$name;
		//调用文件替换方法
		$this->repStrPHPword($template_word, $causepath, $list, $filename, 'word',$export);
	}
	
	/**
	 * @Title: repStrPHPword
	 * @Description: todo(模板标签文件和数据进行替换，然后到处word)
	 * @param 模板标签文件地址 $docfile
	 * @param 生成的最新文件目录 $causedir
	 * @param 导出的数据源数组 $bookNamearr
	 * @param 生成的最新文件名 $fileName
	 * @param 导出的文件类型，默认为word $type
	 * @author 王昭侠
	 * @date 2015-6-29 下午4:54:45
	 * @throws
	 */
	public function repStrPHPword($docfile, $causedir, $bookNamearr,$fileName,$type="word",$export='word'){
		set_time_limit(0);
		//验证模板标签文件是否存在
		if (!file_exists ($docfile)) {
			$this->error("模板文件不存在，请检查");
		}
		//验证最新文件目录是否存在
		if ( !file_exists($causedir) ) {
			//不存在则生成最新文件目录
			$this->createFolders($causedir); //判断目标文件夹是否存在
		}
		import('@.ORG.PHPWord', '', $ext='.php');
		$PHPWord = new PHPWord();
	
		$filepath = pathinfo($docfile);
		$filenameGBK = $causedir.'/'.$fileName.'.'.$filepath['extension'];
		$filenameUTF8=iconv("UTF-8","GBK",$filenameGBK);
		$document = $PHPWord->loadTemplate($docfile);
		$document->clearAllBiaoji();
		
		foreach($bookNamearr as $k => $v)
		{
			if($v["is_datatable"]){
				$data = array();
				$data["showname"] = $v["showname"];
				$data["value"]= $v["value"];
				$data["showtype"]= $v["showtype"]!==NULL?$v["showtype"]:0;
				$data["showtitle"]= $v["showtitle"]!==NULL?$v["showtitle"]:0;
				$data["zihao"] = $v["zihao"];
				$data["hangjianju"] = $v["hangjianju"];
				$data["ziti"] = $v["ziti"];
				if(isset($v["colORrow"])){
					$data["colORrow"] = $v["colORrow"];
				}
				//title_none 如果等于1表示去掉hearder头部
				if(is_array($v["value"]) && !isset($v["colORrow"]) && $v['title_none'] != 1){
					foreach($v["value"] as $kk => $vv){
						$data["titleArr"][] = empty($vv["showname"])?"":$vv["showname"];
						$data["fieldwidth"][] = $v["fieldwidth"][$vv["name"]];
					}
				}
				$document->setValue($v["name"],$data);
			}else{
				$document->setValue($v["name"],$v);
			}
		}
		if( ! empty($_SESSION["htmltodocx_img"])){
			$content = $document->getStr();
			$document->clearTemplateTag();
			$document->save($filenameUTF8);
			// New portrait section
			$section = $PHPWord->createSection();
			// Add image elements
			foreach($_SESSION["htmltodocx_img"] as $v => $k){
				$section->addImage($k["src"],$k["style"]);
				$section->addTextBreak(1);
			}
				
			// Save File
			$objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
			$imgurl = UPLOAD_SampleWord.'Image.docx';
			$objWriter->save($imgurl);
			unset($_SESSION["htmltodocx_img"]);
			$documentImg = $PHPWord->loadTemplate($imgurl);
			$documentImg->setStr($content);
			$documentImg->save($filenameUTF8);
			unlink($imgurl);
		}else{
			$document->clearTemplateTag();
			$document->save($filenameUTF8);
		}
		
		if($export=='swf'){
			//生成PDF
			$HttpSoctetIOAction = A('Http');
			$filenamePDFUTF8 = $HttpSoctetIOAction->createPDF($causedir,$filenameUTF8);
			$swfurl = str_replace("pdf", "swf", $filenamePDFUTF8);
			$swfurl=preg_replace("/^([\s\S]+)\/Public\//", "", $swfurl);
			if($type=="pdf"){
				$filenameUTF8 = $filenamePDFUTF8;
			}
		}
		
		//导出后，就修改版本信息
		$savewordmodel = M('mis_system_template_saveword');
		$filenameUTF8Quan=preg_replace("/^([\s\S]+)\/Public\//", "", $filenameUTF8);
		
		//获取参数
		$id=$_REQUEST['id'];		
		$saveWordId=$_REQUEST['fileid'];
		$modelname=$this->getActionName();
		$wordMap['modelid']=$id;
		$wordMap['modelname']=$modelname;
		$wordMap['id']=$saveWordId;
		if($export=='swf' && !$saveWordId){
			//如果是在线查看，却又没有版本id 那么直接获取最新版本号
			$swfnewmap['modelid'] = $id;
			$swfnewmap['modelname'] = $modelname;
			$saveWordId = $wordMap['id'] = $savewordmodel->where($swfnewmap)->order("id desc")->getField("id");
		}
		$wordData['fileurl']=$filenameUTF8Quan;
		$wordData['swfurl']=$swfurl;
		$wordData['isexport']=1;
		
		
		if($modelname=='MisSalesMyProject'){
			$wordData['modelid']=$id;
			$wordData['modelname']=$modelname;
			unset($wordMap['id']);
			$wlist=$savewordmodel->where($wordMap)->select();
			if($wlist){
				$query=$savewordmodel->where($wordMap)->save($wordData);
			}else{
				$query=$savewordmodel->add($wordData);
			}
		}else{
			if($wordMap['id']){
				$query=$savewordmodel->where($wordMap)->save($wordData);
			}
		}
		$savewordmodel->commit();
		
		if($export== 'word'){
			ob_end_clean();
			header("Cache-Control: public");
			header("Content-Type: application/force-download");
			header("Content-Disposition: attachment; filename=".basename($filenameUTF8));
			readfile($filenameUTF8);
			exit;
		}elseif($export == "swf"){
// 			if($saveWordId){
// 				$swfmap['id']=$saveWordId;
// 				$templateModel = M("mis_system_template_saveword");
// 				$rs = $templateModel->where($swfmap)->order("id desc")->find();
// 				$file_path = "/Public/".$rs['swfurl'];//暂时修改，等保存路径正常后直接取路径
// 			}else{
// 				$file_path = "/Public/".$swfurl;//暂时修改，等保存路径正常后直接取路径
// 			}
			//$file_path = preg_replace("/^([\s\S]+)\/Public/", "__PUBLIC__", $rs['swfurl']);
// 			$this->assign("file_name", $rs['filename']);
// 			$this->assign("file_type", 'file');
// 			$this->assign('file_path', $file_path);
// 			$this->display("Public:playswf");
// 			$ip = GetHostByName($_SERVER['SERVER_NAME']);//获取本机IP
			$ip = C("DB_HOST_WORD");//"192.168.0.238";
			require_once("http://{$ip}:8088/JavaBridge/java/Java.inc");//此行必须
			$PageOfficeCtrl = new Java("com.zhuozhengsoft.pageoffice.PageOfficeCtrlPHP");//此行必须
			$PageOfficeCtrl->setServerPage("http://{$ip}:8088/JavaBridge/poserver.zz");//此行必须，设置服务器页面
			java_set_file_encoding("utf8");//设置中文编码，若涉及到中文必须设置中文编码
			$PageOfficeCtrl->setAllowCopy(false);//禁止拷贝
			//$PageOfficeCtrl->setMenubar(false);//隐藏菜单栏
			$PageOfficeCtrl->setOfficeToolbars(false);//隐藏Office工具条
			$PageOfficeCtrl->setCustomToolbar(false);//隐藏自定义工具栏
			$PageOfficeCtrl->setJsFunction_AfterDocumentOpened("AfterDocumentOpened");
			//打开excel文档
			$PageOfficeCtrl->UserAgent = $_SERVER['HTTP_USER_AGENT'];//若使用谷歌浏览器此行代码必须有，其他浏览器此行代码可不加
			$OpenMode = new Java("com.zhuozhengsoft.pageoffice.OpenModeType");
			$file_path = preg_replace("/^([\s\S]+)\/Public/", "/Public/", $filenameUTF8);
			$PageOfficeCtrl->webOpen($file_path, $OpenMode->docReadOnly, "张三");//此行必须
			$this->assign('PageOfficeCtrl',$PageOfficeCtrl->getDocumentView("PageOfficeCtrl1"));
			$this->display("Public:showEditWord");
			exit;
		}
	}
	
	
	/**
	 * @Title: saveWord 
	 * @Description: todo(保存word) 
	 * @param 模板标签文件路径 $docfile
	 * @param 保存目标文件夹路径 $causedir
	 * @param 文件保存名称 $fileName
	 * @param 导出的数据源 $dataList
	 * @param 单据ID或者项目ID $id
	 * @param 1为阶段报告,0为单据 $type
	 * @param 模型名称 $modelname
	 * @param 保存的ID $saveWordId  
	 * @author 王昭侠
	 * @date 2015-6-29 下午5:18:30 
	 * @throws
	 */
	public function saveWord($docfile, $causedir, $fileName, $dataList,$id,$type=0,$modelname,$saveWordId){
		if (!file_exists ($docfile)) {
			$this->error("模板文件不存在，请检查");
		}
		if ( !file_exists($causedir) ) {
			$this->createFolders($causedir); //判断目标文件夹是否存在
		}
		import('@.ORG.PHPWord', '', $ext='.php');
		$PHPWord = new PHPWord();
	
		$filepath = pathinfo($docfile);
		
		$filenameGBK = $causedir.'/'.$fileName.'.'.$filepath['extension'];
	
		$filenameUTF8=iconv("UTF-8","GBK",$filenameGBK);
		$document = $PHPWord->loadTemplate($docfile);
		$document->clearAllBiaoji();
		foreach($dataList as $k => $v)
		{
			if($v["is_datatable"]){
				$data = array();
				$data["showname"] = $v["showname"];
				$data["value"]= $v["value"];
				$data["showtype"]= isset($v["showtype"])?$v["showtype"]:1;
				if(isset($v["colORrow"])){
					$data["colORrow"] = $v["colORrow"];
				}
				if(is_array($v["value"]) && !isset($v["colORrow"])){
					foreach($v["value"] as $kk => $vv){
						$data["titleArr"][] = empty($vv["showname"])?"":$vv["showname"];
					}
				}
				$document->setValue($k,$data);
			}else{
				if($v["istestarea"]==1 && ! empty($v["original"])){
					$v["value"] = htmlToWordXml(html_entity_decode($v["original"]));
				}
				$document->setValue($k,$v);
			}
		}
		$document->clearTemplateTag();
		$document->save($filenameUTF8);
		$HttpSoctetIOAction = A('Http');
		$swfurl = $HttpSoctetIOAction->createPDF($causedir,$filenameUTF8);
		$swfurl = str_replace("pdf", "swf", $swfurl);
		$model = M('mis_system_template_saveword');
		$filenameUTF8Quan=$filenameUTF8;
		$filenameUTF8=preg_replace("/^([\s\S]+)\/Public\//", "", $filenameUTF8);
		$swfurl=preg_replace("/^([\s\S]+)\/Public\//", "", $swfurl);
		$wordMap['modelid']=$id;
		$wordMap['modelname']=$modelname;
		$wordMap['id']=$saveWordId;
		$wordData['fileurl']=$filenameUTF8;
		$wordData['swfurl']=$swfurl;
		$wordData['isexport']=1;
		$query=$model->where($wordMap)->save($wordData);
		$model->commit();
		header("Cache-Control: public");
		header("Content-Type: application/force-download");
		header("Content-Disposition: attachment; filename=".basename($filenameUTF8Quan));
		readfile($filenameUTF8Quan);
	}
	
	/**
	 * lookupInsertUser  添加特殊用户
	 * @param 权限组:  $roleid    $isCreatePassword:是否生成密码 （true是 false否）
	 * 		$createDefaultPassword 是否生成默认密码  当没有密码的时候生成123456默认密码
	 * 		$type   1新增 2修改 3删除
	 * 
	 * xiayuqin
	 */
	function lookupInsertUser($account,$name,$zhname,$password,$roleid,$isCreatePassword=false,$createDefaultPassword=false,$type=1){
		$userModel=M('user');
		$roleUserModel=M('rolegroup_user');
		if(empty($account)){
			$account=$_REQUEST['orderno'];
		}
		if(empty($name)){
			$name=$_REQUEST['name'];
		}
		if(empty($zhname)){
				$zhname=$_REQUEST['name'];
		}
		if(empty($password)){
			$password=$_REQUEST['pwd'];
		}
		if($isCreatePassword){
			if($password){
				$password=md5($password);
			}else{
				if($createDefaultPassword){
					$password=md5('123456');
				}
			}
		}
		if($type==2){
			//修改
			$userData['name']=$name;
			$userData['zhname']=$zhname;
			$userData['password']=$password;
			$auserMap['account']=$account;
			$auserMap['name']=$name;
			$auserMap['zhname']=$zhname;
			//判断是否存在 不存在添加 存在删除
			$auserlist=$userModel->where($auserMap)->find();
			if($auserlist){
				$userlist=$userModel->where($auserMap)->save($userData);
				if($userlist==false){
					$this->error('用户保存失败');
				}
			}else{
				$userData['account']=$account;
				$userData['name']=$name;
				$userData['zhname']=$zhname;
				$userData['password']=$password;
				$userlist=$userModel->add($userData);
				if($userlist==false){
					$this->error('用户保存失败');
				}else{
					//添加用户组
					$roleData['rolegroup_id']=$roleid;
					$roleData['user_id']=$userlist;
					$roleList=$roleUserModel->add($roleData);
					if($roleList==false){
						$this->error('用户组保存失败');
					}else{
						//添加基础组
						$baseroleMap['rolegroup_id']=1;
						$baseroleMap['user_id']=$userlist;
						$baseroleList=$roleUserModel->add($baseroleMap);
						if($baseroleList==false){
							$this->error('用户基础组保存失败');
						}
					}
				}
			}
			
		}elseif($type==1){
			//新增
			$userData['account']=$account;
			$userData['name']=$name;
			$userData['zhname']=$zhname;
			$userData['password']=$password;
			$userlist=$userModel->add($userData);
			if($userlist==false){
				$this->error('用户保存失败');
			}else{
				//添加用户组
				$roleData['rolegroup_id']=$roleid;
				$roleData['user_id']=$userlist;
				$roleList=$roleUserModel->add($roleData);
				if($roleList==false){
					$this->error('用户组保存失败');
				}else{
					//添加基础组
					$baseroleMap['rolegroup_id']=1;
					$baseroleMap['user_id']=$userlist;
					$baseroleList=$roleUserModel->add($baseroleMap);
					if($baseroleList==false){
						$this->error('用户基础组保存失败');
					}
				}
			}
		}elseif($type==3){
			//删除
			$userMap['account']=$account;
			$list=$userModel->where($userMap)->find();
			//删除用户组
			$roleMap['user_id']=$list['id'];
			$roleList=$roleUserModel->where($roleMap)->delete();
			if($roleList==false){
				$this->error('用户组删除失败');
			}else{
				//删除用户
				$userlist=$userModel->where($userMap)->delete();
				if($userlist==false){
					$this->error('用户删除失败');
				}
			}
		}
		return $userlist;
	}

	function batchoperation(){
		try{
			// 允许组件，文件框，单选框，复选框，下拉框
			//var_dump($_GET);
			$id = $_GET['id']; // 需要修改数据的ID值。
			if(empty($id)){
				$msg = '操作数据异常';
				throw new NullDataExcetion($msg);
			}
			$controllname = $_GET['controll']; // 组件名 
			
			// 检查组件名是ID还是字段英文名
			// 组件名为不字段英文名
			if( !preg_match('/^[a-zA-Z]/' , $controllname) ){
				// 从property表中查询出
				$obj = D('mis_dynamic_form_propery');
				// 条件
				$map['category']=array('in' , 'text,radio,checkbox,select');
				$map['id'] = $controllname;
				$data = $obj->where($map)->find();
				echo $obj->getLAstSql();
				if(!$data){
					$msg =  '当前组件不能使用批量操作！'.$obj->getDBError();
					throw new NullDataExcetion($msg);
				}
			}
			switch ($data['category']){
				case 'text':
					break;
				case 'radio':
					break;
				case 'checkbox':
					break;
				case 'select':
						break;
			}
			$name= $this->getActionName();
			//读取动态配制
			$this->getSystemConfigDetail($name);
			
			$this->assign('field' , $data);
			//var_dump($id.'__'.$controllname);
			$tt = $_GET['tt'];
			$a = preg_match( '/^[a-zA-Z]/' , $tt );
			$b = preg_match( '/^[a-zA-Z]/' , $controllname );
			//var_dump($a);
			//var_dump($b);
			$this->display('Public:batchoperation');
		}catch(Exception $e){
			echo '<pre>'.$e->getMessage().'<br/>'.$e->__toString().'</pre>';
		}
	}
	
	function wordexporttest(){
		import('@.ORG.PHPWord', '', $ext='.php');
		$template_word = UPLOAD_SampleWord."5555.docx";
		$PHPWord = new PHPWord();
		$document = $PHPWord->loadTemplate($template_word);
		$str = $document->getStr();
		dump($str);exit;
// 		$xml = DOMDocument::loadXML($str, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
// 		$sectPr=$xml->getElementsByTagName("sectPr");
// 		$xml->getElementsByTagName("body")->item(0)->removeChild($sectPr->item(0));
// 		$str = $xml->saveXML();
// 		$startPos = strpos($str, "<w:body>")+8;
// 		$endPos = strpos($str, "</w:body>");
// 		$str = trim(substr($str, $startPos,$endPos-$startPos));
// 		$document->unlinkFile();
// 		unlink($template_word);
	}
	
	function htmltoword(){
		import('@.ORG.PHPWord', '', $ext='.php');
		$PHPWord = new PHPWord();
		$content = <<<code
	<table cellspacing="0" cellpadding="0"><tbody><tr class="firstRow"><td width="95" valign="top" style="border: 1px solid windowtext; padding: 0px 7px;"><br></td><td width="95" valign="top" style="border-width: 1px 1px 1px medium; border-style: solid solid solid none; border-color: windowtext windowtext windowtext -moz-use-text-color; -moz-border-top-colors: none; -moz-border-right-colors: none; -moz-border-bottom-colors: none; -moz-border-left-colors: none; border-image: none; padding: 0px 7px;"><br></td><td width="95" valign="top" style="border-width: 1px 1px 1px medium; border-style: solid solid solid none; border-color: windowtext windowtext windowtext -moz-use-text-color; -moz-border-top-colors: none; -moz-border-right-colors: none; -moz-border-bottom-colors: none; -moz-border-left-colors: none; border-image: none; padding: 0px 7px;"><br></td><td width="95" valign="top" style="border-width: 1px 1px 1px medium; border-style: solid solid solid none; border-color: windowtext windowtext windowtext -moz-use-text-color; -moz-border-top-colors: none; -moz-border-right-colors: none; -moz-border-bottom-colors: none; -moz-border-left-colors: none; border-image: none; padding: 0px 7px;"><br></td><td width="95" valign="top" style="border-width: 1px 1px 1px medium; border-style: solid solid solid none; border-color: windowtext windowtext windowtext -moz-use-text-color; -moz-border-top-colors: none; -moz-border-right-colors: none; -moz-border-bottom-colors: none; -moz-border-left-colors: none; border-image: none; padding: 0px 7px;"><br></td><td width="95" valign="top" style="border-width: 1px 1px 1px medium; border-style: solid solid solid none; border-color: windowtext windowtext windowtext -moz-use-text-color; -moz-border-top-colors: none; -moz-border-right-colors: none; -moz-border-bottom-colors: none; -moz-border-left-colors: none; border-image: none; padding: 0px 7px;"><br></td></tr><tr><td width="95" valign="top" style="border-width: medium 1px 1px; border-style: none solid solid; border-color: -moz-use-text-color windowtext windowtext; -moz-border-top-colors: none; -moz-border-right-colors: none; -moz-border-bottom-colors: none; -moz-border-left-colors: none; border-image: none; padding: 0px 7px;"><br></td><td width="95" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;"><br></td><td width="95" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;" rowspan="3"><br></td><td width="95" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;"><br></td><td width="95" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;"><br></td><td width="95" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;"><br></td></tr><tr><td width="95" valign="top" style="border-width: medium 1px 1px; border-style: none solid solid; border-color: -moz-use-text-color windowtext windowtext; -moz-border-top-colors: none; -moz-border-right-colors: none; -moz-border-bottom-colors: none; -moz-border-left-colors: none; border-image: none; padding: 0px 7px;"><br></td><td width="95" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px; word-break: break-all;">&#8203;<br></td><td width="95" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;"><br></td><td width="95" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;" rowspan="3"><br></td><td width="95" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;"><br></td></tr><tr><td width="95" valign="top" style="border-width: medium 1px 1px; border-style: none solid solid; border-color: -moz-use-text-color windowtext windowtext; -moz-border-top-colors: none; -moz-border-right-colors: none; -moz-border-bottom-colors: none; -moz-border-left-colors: none; border-image: none; padding: 0px 7px;"><br></td><td width="95" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;"><br></td><td width="95" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;"><br></td><td width="95" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;"><br></td></tr><tr><td width="95" valign="top" style="border-width: medium 1px 1px; border-style: none solid solid; border-color: -moz-use-text-color windowtext windowtext; -moz-border-top-colors: none; -moz-border-right-colors: none; -moz-border-bottom-colors: none; -moz-border-left-colors: none; border-image: none; padding: 0px 7px;"><br></td><td width="189" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;" rowspan="2" colspan="2"><br></td><td width="95" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;"><br></td><td width="95" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;"><br></td></tr><tr><td width="95" valign="top" style="border-width: medium 1px 1px; border-style: none solid solid; border-color: -moz-use-text-color windowtext windowtext; -moz-border-top-colors: none; -moz-border-right-colors: none; -moz-border-bottom-colors: none; -moz-border-left-colors: none; border-image: none; padding: 0px 7px;"><br></td><td width="95" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;"><br></td><td width="95" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;"><br></td><td width="95" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;"><br></td></tr><tr><td width="95" valign="top" style="border-width: medium 1px 1px; border-style: none solid solid; border-color: -moz-use-text-color windowtext windowtext; -moz-border-top-colors: none; -moz-border-right-colors: none; -moz-border-bottom-colors: none; -moz-border-left-colors: none; border-image: none; padding: 0px 7px;"><br></td><td width="95" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;"><br></td><td width="95" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;"><br></td><td width="95" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;"><br></td><td width="95" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;"><br></td><td width="95" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;"><br></td></tr></tbody></table>
		<table cellspacing="0" cellpadding="0"><tbody><tr class="firstRow"><td width="142" valign="top" style="border: 1px solid windowtext; padding: 0px 7px;" rowspan="3"><p class="edit-text">11111</p></td><td width="142" valign="top" style="border-width: 1px 1px 1px medium; border-style: solid solid solid none; border-color: windowtext windowtext windowtext -moz-use-text-color; -moz-border-top-colors: none; -moz-border-right-colors: none; -moz-border-bottom-colors: none; -moz-border-left-colors: none; border-image: none; padding: 0px 7px;"><br></td><td width="142" valign="top" style="border-width: 1px 1px 1px medium; border-style: solid solid solid none; border-color: windowtext windowtext windowtext -moz-use-text-color; -moz-border-top-colors: none; -moz-border-right-colors: none; -moz-border-bottom-colors: none; -moz-border-left-colors: none; border-image: none; padding: 0px 7px;"><br></td><td width="142" valign="top" style="border-width: 1px 1px 1px medium; border-style: solid solid solid none; border-color: windowtext windowtext windowtext -moz-use-text-color; -moz-border-top-colors: none; -moz-border-right-colors: none; -moz-border-bottom-colors: none; -moz-border-left-colors: none; border-image: none; padding: 0px 7px;"><br></td></tr><tr><td width="426" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;" colspan="3"><p class="edit-text">444444</p></td></tr><tr><td width="142" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;"><br></td><td width="142" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;"><br></td><td width="142" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;"><br></td></tr><tr><td width="142" valign="top" style="border-width: medium 1px 1px; border-style: none solid solid; border-color: -moz-use-text-color windowtext windowtext; -moz-border-top-colors: none; -moz-border-right-colors: none; -moz-border-bottom-colors: none; -moz-border-left-colors: none; border-image: none; padding: 0px 7px;"><br></td><td width="142" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;"><br></td><td width="142" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;"><br></td><td width="142" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;"><br></td></tr><tr><td width="142" valign="top" style="border-width: medium 1px 1px; border-style: none solid solid; border-color: -moz-use-text-color windowtext windowtext; -moz-border-top-colors: none; -moz-border-right-colors: none; -moz-border-bottom-colors: none; -moz-border-left-colors: none; border-image: none; padding: 0px 7px;" rowspan="2"><p class="edit-text">22222</p></td><td width="426" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;" colspan="3"><p class="edit-text">33333</p></td></tr><tr><td width="142" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;"><br></td><td width="142" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;"><br></td><td width="142" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;"><br></td></tr><tr><td width="142" valign="top" style="border-width: medium 1px 1px; border-style: none solid solid; border-color: -moz-use-text-color windowtext windowtext; -moz-border-top-colors: none; -moz-border-right-colors: none; -moz-border-bottom-colors: none; -moz-border-left-colors: none; border-image: none; padding: 0px 7px;"><br></td><td width="142" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;"><br></td><td width="142" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;"><br></td><td width="142" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;"><br></td></tr><tr><td width="142" valign="top" style="border-width: medium 1px 1px; border-style: none solid solid; border-color: -moz-use-text-color windowtext windowtext; -moz-border-top-colors: none; -moz-border-right-colors: none; -moz-border-bottom-colors: none; -moz-border-left-colors: none; border-image: none; padding: 0px 7px;"><br></td><td width="142" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;"><br></td><td width="142" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;"><br></td><td width="142" valign="top" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color windowtext windowtext -moz-use-text-color; padding: 0px 7px;"><br></td></tr></tbody></table>
code;
		$content= <<<code
<table width="100" cellspacing="0" cellpadding="0"><tbody><tr class="firstRow"><td width="2" style="border: 1px solid black; background: rgb(242, 242, 242) none repeat scroll 0% 0%; padding: 0px 7px;" rowspan="3"><p style="text-align:center"><strong><span style="font-family:宋体">接口名称</span></strong></p></td><td width="41" style="border-width: 1px 1px 1px medium; border-style: solid solid solid none; border-color: black black black -moz-use-text-color; -moz-border-top-colors: none; -moz-border-right-colors: none; -moz-border-bottom-colors: none; -moz-border-left-colors: none; border-image: none; background: rgb(242, 242, 242) none repeat scroll 0% 0%; padding: 0px 7px;" colspan="3"><p style="text-align:center"><strong><span style="font-family:宋体">源头数据库</span>(From)</strong></p></td><td width="45" style="border-width: 1px 1px 1px medium; border-style: solid solid solid none; border-color: black black black -moz-use-text-color; -moz-border-top-colors: none; -moz-border-right-colors: none; -moz-border-bottom-colors: none; -moz-border-left-colors: none; border-image: none; background: rgb(242, 242, 242) none repeat scroll 0% 0%; padding: 0px 7px;" colspan="3"><p style="text-align:center"><strong><span style="font-family:宋体">目标数据库</span>(To)</strong></p></td><td width="10" valign="top" style="border-width: 1px 1px 1px medium; border-style: solid solid solid none; border-color: black black black -moz-use-text-color; -moz-border-top-colors: none; -moz-border-right-colors: none; -moz-border-bottom-colors: none; -moz-border-left-colors: none; border-image: none; background: rgb(242, 242, 242) none repeat scroll 0% 0%; padding: 0px 7px;" rowspan="3"><p style="text-align:center"><strong><span style="font-family:宋体">说明</span></strong></p></td></tr><tr><td width="41" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color black black -moz-use-text-color; background: rgb(242, 242, 242) none repeat scroll 0% 0%; padding: 0px 7px;" colspan="3"><p style="text-align:left"><strong><span style="font-family:宋体">地址：</span>192.168.0.237</strong></p><p style="text-align:left"><strong><span style="font-family:宋体">数据库：</span>nydbexec</strong></p><p style="text-align:left"><strong><span style="font-family:宋体">端口：</span></strong></p><p style="text-align:left"><strong><span style="font-family:宋体">账号：</span>root</strong></p><p style="text-align:left"><strong><span style="font-family:宋体">密码：</span>tml123456</strong></p></td><td width="45" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color black black -moz-use-text-color; background: rgb(242, 242, 242) none repeat scroll 0% 0%; padding: 0px 7px;" colspan="3"><p style="text-align:left"><strong><span style="font-family:宋体">地址：</span>192.168.0.100</strong></p><p style="text-align:left"><strong><span style="font-family:宋体">数据库：</span>ufdata_101_2015</strong></p><p style="text-align:left"><strong><span style="font-family:宋体">端口：</span></strong></p><p style="text-align:left"><strong><span style="font-family:宋体">账号：</span>sa</strong></p><p style="text-align:left"><strong><span style="font-family:宋体">密码：</span></strong></p></td></tr><tr><td width="5" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color black black -moz-use-text-color; background: rgb(242, 242, 242) none repeat scroll 0% 0%; padding: 0px 7px;"><p style="text-align:center"><strong><span style="font-family:宋体">数据库表</span></strong></p></td><td width="5" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color black black -moz-use-text-color; background: rgb(242, 242, 242) none repeat scroll 0% 0%; padding: 0px 7px;"><p style="text-align:center"><strong><span style="font-family:宋体">属性</span></strong></p></td><td width="30" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color black black -moz-use-text-color; background: rgb(242, 242, 242) none repeat scroll 0% 0%; padding: 0px 7px;"><p style="text-align:center"><strong><span style="font-family:宋体">其他</span></strong></p></td><td width="2" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color black black -moz-use-text-color; background: rgb(242, 242, 242) none repeat scroll 0% 0%; padding: 0px 7px;"><p style="text-align:center"><strong><span style="font-family:宋体">数据库表</span></strong></p></td><td width="3" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color black black -moz-use-text-color; background: rgb(242, 242, 242) none repeat scroll 0% 0%; padding: 0px 7px;"><p style="text-align:center"><strong><span style="font-family:宋体">属性</span></strong></p></td><td width="39" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color black black -moz-use-text-color; background: rgb(242, 242, 242) none repeat scroll 0% 0%; padding: 0px 7px;"><p style="text-align:center"><strong><span style="font-family:宋体">其他</span></strong></p></td></tr><tr style=";height:120px"><td width="2" valign="top" height="120" style="border-width: medium 1px 1px; border-style: none solid solid; border-color: -moz-use-text-color black black; -moz-border-top-colors: none; -moz-border-right-colors: none; -moz-border-bottom-colors: none; -moz-border-left-colors: none; border-image: none; padding: 0px 7px;"><p><span style="font-family:   宋体">薪资接口：</span></p><p><span style="font-family:   宋体">按需同步</span> <span style="font-family:宋体">按年份</span>+<span style="font-family:宋体">月份同步</span> <span style="font-family:宋体">，</span>pmp<span style="font-family:宋体">存出过程会按年份</span>+<span style="font-family:宋体">月份删除即将同步数据</span></p></td><td width="5" valign="top" height="120" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color black black -moz-use-text-color; padding: 0px 7px;"><p>U8<span style="font-family:宋体">数据源视图：</span> pmpgzview</p><p>&nbsp;</p></td><td width="5" valign="top" height="120" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color black black -moz-use-text-color; padding: 0px 7px;"><p>gzlbinfo.cgzdlname &nbsp; as <span style="font-family:宋体">工资大类</span>,</p><p>WA_GZData.cgzgradenum &nbsp; as <span style="font-family:宋体">类别编码</span>,</p><p>gzlbinfo.cgzgradename &nbsp; as <span style="font-family:宋体">类别名称</span>,</p><p>WA_GZData.cpsn_num&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">人员编码</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>hr_hi_person.cpsn_name &nbsp; as <span style="font-family:宋体">人员名称</span>,</p><p>WA_GZData.cDept_Num&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">部门编码</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>Department.cdepname &nbsp; as <span style="font-family:宋体">部门名称</span>,</p><p>WA_GZData.iPsnGrd_id&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; iPsnGrd_id&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>WA_GZData.iYear&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">年份</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>iMonth&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">月份</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>iAccMonth&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; iAccMonth&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_1&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">应发合计</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_2&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">扣款合计</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_3&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">实发合计</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_4&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">本月扣零</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_5&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">上月扣零</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_6&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">代扣税</span>&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_7&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">计件工资</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>WA_GZData.bLastFlag&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; bLastFlag ,</p><p>WA_GZData.vStatus1&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; vStatus1&nbsp; ,</p><p>WA_GZData.nStatus2&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; nStatus2&nbsp; ,</p><p>WA_GZData.iRecordID&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; iRecordID&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_1000&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">代付税</span>&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_1001&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">年终奖</span>&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_1002&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">年终奖代扣税</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_1003&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">工资代扣税</span>&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_1004&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">扣税合计</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_1005&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">年终奖代付税</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_1006&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">工资代付税</span>&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_8&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">工资级别</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_9&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">工资序列</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_10&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">工资等级</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_11&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">工资档次</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_12&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">年岗位技能工资</span>&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_13&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">月工资</span>&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_14&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">月基薪</span>&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_15&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">月绩效</span>&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_16&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">年功工资</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_17&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">加班工资</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_18&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">月预扣风险金</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_19&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">社保基数</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_20&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">公积金基数</span>&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_21&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">养老保险金</span>&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_22&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">医疗保险金</span>&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_23&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">失业保险金</span>&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_24&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">生育保险金</span>&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_25&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">住房公积金</span>&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_26&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">社保调整</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_27&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">计税金额</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_28&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">其他免税项目</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_29&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">应发小计</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_30&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">通讯费扣款</span>&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_31&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">五险一金扣款</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_32&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">病事缺扣款</span>&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_33&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">迟到扣款</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_34&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">上月校错</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_35&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">车贴</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_36&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">财务挂账</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_37&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">消财务挂账</span>&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_38&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">扣款小计</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_39&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">一次性绩效发放</span>&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_40&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">季度绩效补差</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_41&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">风险金调差</span>&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_42&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">本月实际计薪天数</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_43&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">返风险金</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_44&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">实习补助标准</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_45&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">本月出勤率</span>&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_46&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">绩效考勤等级</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_47&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">绩效摊销</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_48&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">创新先进个人奖励</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_49&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">缺勤天数</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_50&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">平时加班天数</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_51&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">迟到次数</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_52&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">周末加班天数</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,</p><p>F_53&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AS&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">节假日加班天数</span></p></td><td width="30" valign="top" height="120" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color black black -moz-use-text-color; padding: 0px 7px;"><p><span style="font-family:   宋体">调用实例：</span></p><p>CALL &nbsp; `pmpxzin` (&nbsp; -- pmp <span style="font-family:宋体">平台的数据接口</span></p><p>'001',&nbsp; -- <span style="font-family:宋体">工资大类</span></p><p>'001' &nbsp; , -- <span style="font-family:宋体">类别编码</span></p><p>'<span style="font-family:宋体">基本工资</span>' , -- <span style="font-family:   宋体">类别名称</span></p><p>'100001', &nbsp; -- <span style="font-family:宋体">人员编码</span></p><p>2 , -- &nbsp; <span style="font-family:宋体">部门编码</span></p><p>NULL , &nbsp; -- iPsnGrd_id</p><p>NULL , &nbsp; -- iPsnAmt</p><p>2015 , &nbsp; -- <span style="font-family:宋体">年份</span></p><p>8, -- <span style="font-family:宋体">月份</span></p><p>NULL , &nbsp; -- iAccMonth</p><p>3882.44 &nbsp; , --&nbsp; <span style="font-family:   宋体">应发合计</span> </p><p>283.838 &nbsp; , --&nbsp; <span style="font-family:   宋体">扣款合计</span> </p><p>4883.83 &nbsp; , --&nbsp; <span style="font-family:   宋体">实发合计</span> </p><p>789.38 &nbsp; , --&nbsp; <span style="font-family:   宋体">本月扣零</span> </p><p>483.83 &nbsp; , --&nbsp; <span style="font-family:   宋体">上月扣零</span> </p><p>38.83 &nbsp; , --&nbsp; <span style="font-family:   宋体">代扣税</span> </p><p>448 , &nbsp; --&nbsp; <span style="font-family:   宋体">计件工资</span> </p><p>NULL , &nbsp; -- bLastFlag</p><p>NULL , &nbsp; -- vStatus1</p><p>NULL , &nbsp; -- nStatus2</p><p>NULL , &nbsp; -- iRecordID</p><p>38.3 , &nbsp; --&nbsp; <span style="font-family:   宋体">代付税</span> </p><p>3448.38 &nbsp; , --&nbsp; <span style="font-family:   宋体">年终奖</span> </p><p>32.23 &nbsp; , --&nbsp; <span style="font-family:   宋体">年终奖代扣税</span> &nbsp;</p><p>449.3 &nbsp; , --&nbsp; <span style="font-family:   宋体">工资代扣税</span> </p><p>22.82 &nbsp; , --&nbsp; <span style="font-family:   宋体">扣税合计</span> </p><p>382.3 &nbsp; , --&nbsp; <span style="font-family:   宋体">年终奖代付税</span> &nbsp;</p><p>373.8 &nbsp; , --&nbsp; <span style="font-family:   宋体">工资代付税</span> </p><p>1 , &nbsp; --&nbsp; <span style="font-family:   宋体">工资级别</span> </p><p>2 , &nbsp; --&nbsp; <span style="font-family:   宋体">工资序列</span> </p><p>3 , &nbsp; --&nbsp; <span style="font-family:   宋体">工资等级</span> </p><p>4 , &nbsp; --&nbsp; <span style="font-family:   宋体">工资档次</span> </p><p>5 , &nbsp; --&nbsp; <span style="font-family:   宋体">年岗位技能工资</span> &nbsp;</p><p>8000 , &nbsp; --&nbsp; <span style="font-family:   宋体">月工资</span> </p><p>4000 , &nbsp; --&nbsp; <span style="font-family:   宋体">月基薪</span> </p><p>2000 , &nbsp; --&nbsp; <span style="font-family:   宋体">月绩效</span> </p><p>283.83 &nbsp; , --&nbsp; <span style="font-family:   宋体">年功工资</span> </p><p>44.38 &nbsp; , --&nbsp; <span style="font-family:   宋体">加班工资</span> </p><p>4.38 , &nbsp; --&nbsp; <span style="font-family:   宋体">月预扣风险金</span> &nbsp;</p><p>34.82 &nbsp; , --&nbsp; <span style="font-family:   宋体">社保基数</span> </p><p>44.83 &nbsp; , --&nbsp; <span style="font-family:   宋体">公积金基数</span> </p><p>442.83 &nbsp; , --&nbsp; <span style="font-family:   宋体">养老保险金</span> </p><p>32 , &nbsp; --&nbsp; <span style="font-family:   宋体">医疗保险金</span> </p><p>443.8 &nbsp; , --&nbsp; <span style="font-family:   宋体">失业保险金</span> </p><p>44.38 &nbsp; , --&nbsp; <span style="font-family:   宋体">生育保险金</span> </p><p>33.38 &nbsp; , --&nbsp; <span style="font-family:   宋体">住房公积金</span> </p><p>44.38 &nbsp; , --&nbsp; <span style="font-family:   宋体">社保调整</span> </p><p>1903.8, &nbsp; -- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">计税金额</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p><p>43.11, &nbsp; -- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">其他免税项目</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p><p>7889.34, &nbsp; -- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">应发小计</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p><p>48.3, &nbsp; -- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">通讯费扣款</span>&nbsp;&nbsp;&nbsp;&nbsp;</p><p>889.2, &nbsp; -- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">五险一金扣款</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p><p>37.38, &nbsp; -- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">病事缺扣款</span>&nbsp;&nbsp;&nbsp;&nbsp;</p><p>28.77, &nbsp; -- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">迟到扣款</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p><p>12.38, &nbsp; -- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">上月校错</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p><p>1500, &nbsp; -- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">车贴</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p><p>38.84, &nbsp; -- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">财务挂账</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p><p>38.83, &nbsp; -- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">消财务挂账</span>&nbsp;&nbsp;&nbsp;&nbsp;</p><p>503.38, &nbsp; -- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">扣款小计</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p><p>3834.2, &nbsp; -- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">一次性绩效发放</span>&nbsp;&nbsp;&nbsp;&nbsp;</p><p>889.43, &nbsp; -- <span style="font-family:宋体">季度绩效补差</span></p><p>48.38, &nbsp; -- <span style="font-family:宋体">风险金调差</span></p><p>28, -- &nbsp; <span style="font-family:宋体">本月实际计薪天数</span></p><p>238.48, &nbsp; -- <span style="font-family:宋体">返风险金</span></p><p>1000, &nbsp; -- <span style="font-family:宋体">实习补助标准</span></p><p>100, &nbsp; -- <span style="font-family:宋体">本月出勤率</span></p><p>2, -- <span style="font-family:宋体">绩效考勤等级</span></p><p>448.38, &nbsp; -- <span style="font-family:宋体">绩效摊销</span></p><p>343.28, &nbsp; -- <span style="font-family:宋体">创新先进个人奖励</span></p><p>13, -- &nbsp; <span style="font-family:宋体">缺勤天数</span></p><p>12, -- &nbsp; <span style="font-family:宋体">平时加班天数</span></p><p>11, -- &nbsp; <span style="font-family:宋体">迟到次数</span></p><p>22, -- &nbsp; <span style="font-family:宋体">周末加班天数</span></p><p>24&nbsp; -- <span style="font-family:宋体">节假日加班天数</span></p><p>)</p></td><td width="2" valign="top" height="120" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color black black -moz-use-text-color; padding: 0px 7px;"><p>Pmp<span style="font-family:宋体">目标</span></p><p><span style="font-family:   宋体">存出过程：</span> pmpxzin</p></td><td width="3" valign="top" height="120" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color black black -moz-use-text-color; padding: 0px 7px;"><p>cgzdlname,&nbsp; -- <span style="font-family:宋体">工资大类</span></p><p>cgzgradenum &nbsp; , -- <span style="font-family:宋体">类别编码</span></p><p>cgzgradename &nbsp; , -- <span style="font-family:宋体">类别名称</span></p><p>cpsnnum &nbsp; , -- <span style="font-family:宋体">人员编码</span></p><p>cDeptNum &nbsp; , -- <span style="font-family:宋体">部门编码</span></p><p>iYear &nbsp; , -- <span style="font-family:宋体">年份</span></p><p>iMonth &nbsp; , -- <span style="font-family:宋体">月份</span></p><p>yingfaheji &nbsp; , --&nbsp; <span style="font-family:   宋体">应发合计</span> </p><p>koukuanheji &nbsp; , --&nbsp; <span style="font-family:   宋体">扣款合计</span> </p><p>shifaheji &nbsp; , --&nbsp; <span style="font-family:   宋体">实发合计</span> </p><p>benyuekouling &nbsp; , --&nbsp; <span style="font-family:   宋体">本月扣零</span> </p><p>shangyuekouling &nbsp; , --&nbsp; <span style="font-family:   宋体">上月扣零</span> </p><p>daikoushui &nbsp; , --&nbsp; <span style="font-family:   宋体">代扣税</span> </p><p>jijiangongzi &nbsp; , --&nbsp; <span style="font-family:   宋体">计件工资</span> </p><p>daifushui &nbsp; , --&nbsp; <span style="font-family:   宋体">代付税</span> </p><p>nianzhongjiang &nbsp; , --&nbsp; <span style="font-family:   宋体">年终奖</span> </p><p>nianzhongjiangdaikou &nbsp; , --&nbsp; <span style="font-family:   宋体">年终奖代扣税</span> &nbsp;</p><p>gongzidaikoushui &nbsp; , --&nbsp; <span style="font-family:   宋体">工资代扣税</span> </p><p>koushuiheji &nbsp; , --&nbsp; <span style="font-family:   宋体">扣税合计</span> </p><p>nianzhongjiangdaifus &nbsp; , --&nbsp; <span style="font-family:   宋体">年终奖代付税</span> &nbsp;</p><p>gongzidaifushui &nbsp; , --&nbsp; <span style="font-family:   宋体">工资代付税</span> </p><p>gongzijibie &nbsp; , --&nbsp; <span style="font-family:   宋体">工资级别</span> </p><p>gongzixulie &nbsp; , --&nbsp; <span style="font-family:   宋体">工资序列</span> </p><p>gongzidengji &nbsp; , --&nbsp; <span style="font-family:   宋体">工资等级</span> </p><p>gongzidangci &nbsp; , --&nbsp; <span style="font-family:   宋体">工资档次</span> </p><p>niangangweijinengong &nbsp; , --&nbsp; <span style="font-family:   宋体">年岗位技能工资</span> &nbsp;</p><p>yuegongzi &nbsp; , --&nbsp; <span style="font-family:   宋体">月工资</span> </p><p>yuejixin &nbsp; , --&nbsp; <span style="font-family:   宋体">月基薪</span> </p><p>yuejixiao &nbsp; , --&nbsp; <span style="font-family:   宋体">月绩效</span> </p><p>niangonggongzi &nbsp; , --&nbsp; <span style="font-family:   宋体">年功工资</span> </p><p>jiabangongzi &nbsp; , --&nbsp; <span style="font-family:   宋体">加班工资</span> </p><p>yueyukoufengxianjin &nbsp; , --&nbsp; <span style="font-family:   宋体">月预扣风险金</span> &nbsp;</p><p>shebaojishu &nbsp; , --&nbsp; <span style="font-family:   宋体">社保基数</span> </p><p>gongjijinjishu &nbsp; , --&nbsp; <span style="font-family:   宋体">公积金基数</span> </p><p>yanglaobaoxianjin &nbsp; , --&nbsp; <span style="font-family:   宋体">养老保险金</span> </p><p>yiliaobaoxianjin &nbsp; , --&nbsp; <span style="font-family:   宋体">医疗保险金</span> </p><p>shiyebaoxianjin &nbsp; , --&nbsp; <span style="font-family:   宋体">失业保险金</span> </p><p>shengyubaoxianjin &nbsp; , --&nbsp; <span style="font-family:   宋体">生育保险金</span> </p><p>zhufanggongjijin &nbsp; , --&nbsp; <span style="font-family:   宋体">住房公积金</span> </p><p>shebaodiaozheng &nbsp; , --&nbsp; <span style="font-family:   宋体">社保调整</span> </p><p>jishuijine &nbsp; , --&nbsp; <span style="font-family:   宋体">计税金额</span> </p><p>qitamianshuixiangmu &nbsp; , --&nbsp; <span style="font-family:   宋体">其他免税项目</span> &nbsp;</p><p>yingfaxiaoji &nbsp; , --&nbsp; <span style="font-family:   宋体">应发小计</span> </p><p>tongxunfeikoukuan &nbsp; , --&nbsp; <span style="font-family:   宋体">通讯费扣款</span> </p><p>wuxianyijinkoukuan &nbsp; , --&nbsp; <span style="font-family:   宋体">五险一金扣款</span> &nbsp;</p><p>bingshiquekoukuan &nbsp; , --&nbsp; <span style="font-family:   宋体">病事缺扣款</span> </p><p>chidaokoukuan &nbsp; , --&nbsp; <span style="font-family:   宋体">迟到扣款</span> </p><p>shangyuexiaocuo &nbsp; , --&nbsp; <span style="font-family:   宋体">上月校错</span> </p><p>chetie &nbsp; , --&nbsp; <span style="font-family:   宋体">车贴</span> </p><p>caiwuguazhang &nbsp; , --&nbsp; <span style="font-family:   宋体">财务挂账</span> </p><p>xiaocaiwuguazhang &nbsp; , --&nbsp; <span style="font-family:   宋体">消财务挂账</span> </p><p>koukuanxiaoji &nbsp; , --&nbsp; <span style="font-family:   宋体">扣款小计</span> </p><p>yicixingjixiaofafang &nbsp; , --&nbsp; <span style="font-family:   宋体">一次性绩效发放</span> &nbsp;</p><p>jidujixiaobucha &nbsp; , -- <span style="font-family:宋体">季度绩效补差</span></p><p>fengxianjindiaocha &nbsp; , -- <span style="font-family:宋体">风险金调差</span></p><p>benyueshijijixintian &nbsp; , -- <span style="font-family:宋体">本月实际计薪天数</span></p><p>fanfengxianjin &nbsp; , -- <span style="font-family:宋体">返风险金</span></p><p>shixibuzhubiaozhun &nbsp; , -- <span style="font-family:宋体">实习补助标准</span></p><p>benyuechuqinlv &nbsp; , -- <span style="font-family:宋体">本月出勤率</span></p><p>jixiaokaoqindengji &nbsp; , -- <span style="font-family:宋体">绩效考勤等级</span></p><p>jixiaotanxiao &nbsp; , -- <span style="font-family:宋体">绩效摊销</span></p><p>chuangxinxianjingere &nbsp; , -- <span style="font-family:宋体">创新先进个人奖励</span></p><p>queqintianshu &nbsp; , -- <span style="font-family:宋体">缺勤天数</span></p><p>pingshijiabantianshu &nbsp; , -- <span style="font-family:宋体">平时加班天数</span></p><p>chidaocishu &nbsp; , -- <span style="font-family:宋体">迟到次数</span></p><p>zhoumojiabantianshu &nbsp; , -- <span style="font-family:宋体">周末加班天数</span></p><p>jiejiarijiabantiansh&nbsp; -- <span style="font-family:宋体">节假日加班天数</span></p><p><span style="font-family:   宋体">（</span>67<span style="font-family:宋体">个字段）</span></p></td><td width="39" valign="top" height="120" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color black black -moz-use-text-color; padding: 0px 7px;"><br></td><td width="10" valign="top" height="120" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color black black -moz-use-text-color; padding: 0px 7px;"><br></td></tr><tr style=";height:216px"><td width="2" valign="top" height="216" style="border-width: medium 1px 1px; border-style: none solid solid; border-color: -moz-use-text-color black black; -moz-border-top-colors: none; -moz-border-right-colors: none; -moz-border-bottom-colors: none; -moz-border-left-colors: none; border-image: none; padding: 0px 7px;"><p><span style="font-family:   宋体">请假同步：</span></p><p>Pmp<span style="font-family:宋体">请假单终审后传递到</span>u8</p></td><td width="5" valign="top" height="216" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color black black -moz-use-text-color; padding: 0px 7px;"><p>Pmp<span style="font-family:宋体">数据源视图：</span> pmpqjsurview</p></td><td width="5" valign="top" height="216" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color black black -moz-use-text-color; padding: 0px 7px;"><p>yongyouorderno` &nbsp; AS <span style="font-family:宋体">创建人编码</span>,</p><p>mis_auto_ucjcm.`jieshuriqi&nbsp; AS <span style="font-family:宋体">结束时间</span>,</p><p>&nbsp;mis_auto_dtics.`u8leixingbianma` AS <span style="font-family:宋体">请假类型</span>,</p><p>&nbsp;ryinfoshenhe.name AS <span style="font-family:宋体">批准人</span>,</p><p>&nbsp;ryinfo.`name` AS <span style="font-family:宋体">创建人名</span>,</p><p>&nbsp;mis_auto_ucjcm.`kaishiriqi`&nbsp; AS <span style="font-family:宋体">开始时间</span>,</p><p>FROM_UNIXTIME(mis_auto_ucjcm.`createtime`,'%Y-%m-%d &nbsp; %H:%i:%s')&nbsp; AS <span style="font-family:宋体">创建时间</span>,</p><p>&nbsp;ryinfoqingjr.yongyouorderno AS <span style="font-family:宋体">请假人编码</span>,</p><p>&nbsp;mis_auto_ucjcm.qingjiashiyou AS <span style="font-family:宋体">请假原因</span>,</p><p>&nbsp;'' AS <span style="font-family:宋体">备注</span>,</p><p>&nbsp;mis_auto_ucjcm.`kaishiriqi`&nbsp; AS <span style="font-family:宋体">计划时间</span>,</p><p>&nbsp;ryinfoshenhe.yongyouorderno&nbsp; AS <span style="font-family:宋体">审核人编码</span>,</p><p>&nbsp;ryinfoshenhe.name AS <span style="font-family:宋体">审核人</span>,</p><p>&nbsp;process_info_history.`dotime` AS <span style="font-family:宋体">审核时间</span> ,</p><p>(CASE &nbsp; WHEN mis_auto_ucjcm.status=1 THEN '<span style="font-family:宋体">否</span>'</p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; WHEN mis_auto_ucjcm.status=0 THEN '<span style="font-family:宋体">是</span>'</p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; END ) AS <span style="font-family:宋体">审核状态</span></p><p>&nbsp;</p></td><td width="30" valign="top" height="216" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color black black -moz-use-text-color; padding: 0px 7px;"><p><span style="font-family:   宋体">调用实例：</span></p><p>&nbsp;</p><p>exec &nbsp; pmpqjwsc -- <span style="font-family:宋体">请假存储过程</span>&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">调试</span></p><p>&nbsp;'100005', --&nbsp; &nbsp; <span style="font-family:宋体">创建人编码</span></p><p>&nbsp;'2015-07-31 17:00'&nbsp; ,&nbsp;&nbsp; &nbsp; -- <span style="font-family:宋体">结束时间</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p><p>&nbsp;'BS00'&nbsp;&nbsp; &nbsp; ,&nbsp; -- <span style="font-family:宋体">请假类型</span></p><p>&nbsp;'<span style="font-family:宋体">批准人名</span>'&nbsp;&nbsp; , -- <span style="font-family:宋体">批准人</span></p><p>&nbsp;'<span style="font-family:宋体">周庆红</span>'&nbsp;&nbsp; ,&nbsp; --&nbsp; <span style="font-family:宋体">创建人名</span></p><p>&nbsp;'2015-07-31 17:00'&nbsp; ,&nbsp;&nbsp;&nbsp; &nbsp; --&nbsp;&nbsp; <span style="font-family:宋体">开始日期</span>&nbsp;&nbsp; &nbsp;</p><p>&nbsp;'2015-07-31 17:00'&nbsp; ,&nbsp;&nbsp;&nbsp; &nbsp; --&nbsp; <span style="font-family:宋体">创建日期</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p><p>&nbsp;'100005'&nbsp; &nbsp; ,&nbsp;&nbsp; --<span style="font-family:宋体">请假人编码</span></p><p>&nbsp;'<span style="font-family:宋体">请假原因</span>'&nbsp; , --&nbsp;&nbsp; <span style="font-family:宋体">请假原因</span></p><p>&nbsp;' <span style="font-family:宋体">备注说明</span> ' ,&nbsp;&nbsp;&nbsp; --<span style="font-family:宋体">备注</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p><p>&nbsp;'2015-07-31 17:00'&nbsp;&nbsp; ,--<span style="font-family:宋体">计划时间</span></p><p>&nbsp;'100005' , -- <span style="font-family:   宋体">审核人编码</span>&nbsp;</p><p>&nbsp;'<span style="font-family:宋体">周庆红</span>'&nbsp;&nbsp; , -- <span style="font-family:宋体">审核人</span>&nbsp;</p><p>&nbsp;'2015-07-31 17:00'&nbsp; ,&nbsp; -- &nbsp; <span style="font-family:宋体">审核时间</span>&nbsp;</p><p>&nbsp;1 &nbsp;-- <span style="font-family:宋体">审核状态</span></p><p>&nbsp;</p><p>&nbsp;</p></td><td width="2" valign="top" height="216" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color black black -moz-use-text-color; padding: 0px 7px;"><p><span style="font-family:   宋体">目标：</span>U8<span style="font-family:宋体">存出过程</span> pmpqjwsc</p></td><td width="3" valign="top" height="216" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color black black -moz-use-text-color; padding: 0px 7px;"><p>exec &nbsp; pmpqjwsc -- <span style="font-family:宋体">请假存储过程</span>&nbsp;&nbsp;&nbsp; <span style="font-family:宋体">调试</span></p><p>&nbsp;'100005', --&nbsp; &nbsp; <span style="font-family:宋体">创建人编码</span></p><p>&nbsp;'2015-07-31 17:00'&nbsp; ,&nbsp;&nbsp; &nbsp; -- <span style="font-family:宋体">结束时间</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p><p>&nbsp;'BS00'&nbsp;&nbsp; &nbsp; ,&nbsp; -- <span style="font-family:宋体">请假类型</span></p><p>&nbsp;'<span style="font-family:宋体">批准人名</span>'&nbsp;&nbsp; , -- <span style="font-family:宋体">批准人</span></p><p>&nbsp;'<span style="font-family:宋体">周庆红</span>'&nbsp;&nbsp; ,&nbsp; --&nbsp; <span style="font-family:宋体">创建人名</span></p><p>&nbsp;'2015-07-31 17:00'&nbsp; ,&nbsp;&nbsp;&nbsp; &nbsp; --&nbsp;&nbsp; <span style="font-family:宋体">开始日期</span>&nbsp;&nbsp; &nbsp;</p><p>&nbsp;'2015-07-31 17:00'&nbsp; ,&nbsp;&nbsp;&nbsp; &nbsp; --&nbsp; <span style="font-family:宋体">创建日期</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p><p>&nbsp;'100005'&nbsp; &nbsp; ,&nbsp;&nbsp; -- <span style="font-family:宋体">请假人</span></p><p>&nbsp;'<span style="font-family:宋体">请假原因</span>'&nbsp; , --&nbsp;&nbsp; <span style="font-family:宋体">请假原因</span></p><p>&nbsp;' <span style="font-family:宋体">备注说明</span> ' ,&nbsp;&nbsp;&nbsp; --<span style="font-family:宋体">备注</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p><p>&nbsp;'2015-07-31 17:00'&nbsp;&nbsp; ,--<span style="font-family:宋体">计划时间</span></p><p>&nbsp;'100005' , -- <span style="font-family:   宋体">审核人编码</span> &nbsp;</p><p>&nbsp;'<span style="font-family:宋体">周庆红</span>'&nbsp;&nbsp; , -- <span style="font-family:宋体">审核人</span>&nbsp;</p><p>&nbsp;'2015-07-31 17:00'&nbsp; ,&nbsp; -- &nbsp; <span style="font-family:宋体">审核时间</span>&nbsp;</p><p>&nbsp;1</p></td><td width="39" valign="top" height="216" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color black black -moz-use-text-color; padding: 0px 7px;"><br></td><td width="10" valign="top" height="216" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color black black -moz-use-text-color; padding: 0px 7px;"><br></td></tr><tr style=";height:216px"><td width="2" valign="top" height="216" style="border-width: medium 1px 1px; border-style: none solid solid; border-color: -moz-use-text-color black black; -moz-border-top-colors: none; -moz-border-right-colors: none; -moz-border-bottom-colors: none; -moz-border-left-colors: none; border-image: none; padding: 0px 7px;"><p><span style="font-family:   宋体">出差同步：</span></p><p>Pmp<span style="font-family:宋体">出差终审后传递到</span>u8</p></td><td width="5" valign="top" height="216" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color black black -moz-use-text-color; padding: 0px 7px;"><p>Pmp<span style="font-family:宋体">数据源视图：</span> pmpccsurview</p></td><td width="5" valign="top" height="216" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color black black -moz-use-text-color; padding: 0px 7px;"><p>ryinfo.yongyouorderno &nbsp; AS <span style="font-family:宋体">创建人编码</span>,</p><p>mis_auto_xeyis.`chuchajieshuoriqi` &nbsp; AS <span style="font-family:宋体">结束时间</span>,</p><p>ryinfochuc.`name`AS &nbsp; <span style="font-family:宋体">批准人</span>,</p><p>ryinfo.`name` &nbsp; AS <span style="font-family:宋体">创建人</span>,</p><p>mis_auto_xeyis.chuchakaishiriqi &nbsp; AS <span style="font-family:宋体">开始时间</span>,</p><p>mis_auto_xeyis.`createtime`&nbsp; AS <span style="font-family:宋体">创建时间</span>,</p><p>ryinfochucr.yongyouorderno&nbsp; AS <span style="font-family:宋体">出差人编码</span>,</p><p>mis_auto_vqzzh.`name` &nbsp; AS <span style="font-family:宋体">出差类型</span>,</p><p>&nbsp;mis_auto_xeyis.chuchashiyou AS <span style="font-family:宋体">出差事由</span>,</p><p>&nbsp;'' AS <span style="font-family:宋体">备注</span>,</p><p>ryinfochuc.`name` &nbsp; AS <span style="font-family:宋体">审核人名</span>,</p><p>process_info_history.`dotime` &nbsp; AS <span style="font-family:宋体">审核时间</span>,</p><p>(CASE &nbsp; WHEN mis_auto_xeyis.`status`='0'&nbsp; THEN &nbsp; '<span style="font-family:宋体">是</span>'</p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; WHEN mis_auto_xeyis.`status`='1' THEN '<span style="font-family:宋体">否</span>'&nbsp;</p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; END&nbsp;&nbsp;&nbsp; &nbsp; ) AS <span style="font-family:宋体">是否审核</span>,</p><p>ryinfochuc.yongyouorderno &nbsp; AS <span style="font-family:宋体">审核人编码</span>,</p></td><td width="30" valign="top" height="216" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color black black -moz-use-text-color; padding: 0px 7px;"><p>&nbsp;</p><p>exec &nbsp; pmpcc&nbsp;&nbsp; --<span style="font-family:宋体">出差调试</span> </p><p>&nbsp;&nbsp; 'demo', -- <span style="font-family:   宋体">创建人编码</span></p><p>&nbsp;&nbsp; '2015-08-04 00:00', -- <span style="font-family:宋体">结束时间</span></p><p>&nbsp;&nbsp; 'aaa', -- <span style="font-family:   宋体">批准人</span></p><p>&nbsp;&nbsp; 'demo', -- <span style="font-family:   宋体">创建人</span></p><p>&nbsp;&nbsp; '2015-08-03 00:00', -- <span style="font-family:宋体">开始时间</span></p><p>&nbsp;&nbsp; '2015-08-03 11:47:04', -- <span style="font-family:宋体">创建时间</span></p><p>&nbsp;&nbsp; '100001',&nbsp; &nbsp; -- <span style="font-family:宋体">出差人编码</span></p><p>&nbsp;&nbsp; 'DS01',&nbsp; &nbsp; -- <span style="font-family:宋体">出差类型</span></p><p>&nbsp;&nbsp; 'bbb', -- <span style="font-family:   宋体">出差事由</span></p><p>&nbsp;&nbsp; 'ccc111' , -- <span style="font-family:宋体">备注</span></p><p>&nbsp;&nbsp; 'demo',--&nbsp; &nbsp; <span style="font-family:宋体">审核人名</span>&nbsp;</p><p>&nbsp;&nbsp; '2015-08-03 11:47:04' , --&nbsp; <span style="font-family:宋体">审核时间</span>&nbsp;</p><p>&nbsp;&nbsp; 1 , -- <span style="font-family:   宋体">是否审核</span></p><p>&nbsp;&nbsp; 'demo'&nbsp; &nbsp; --<span style="font-family:宋体">审核人编码</span></p></td><td width="2" valign="top" height="216" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color black black -moz-use-text-color; padding: 0px 7px;"><p>U8<span style="font-family:宋体">目标：存出过程</span> &nbsp;pmpcc&nbsp;&nbsp; &nbsp;</p></td><td width="3" valign="top" height="216" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color black black -moz-use-text-color; padding: 0px 7px;"><p>&nbsp;</p><p>&nbsp;exec pmpcc&nbsp;&nbsp; &nbsp; --<span style="font-family:宋体">出差调试</span> </p><p>&nbsp;&nbsp; 'demo', -- <span style="font-family:   宋体">创建人编码</span></p><p>&nbsp;&nbsp; '2015-08-04 00:00', -- <span style="font-family:宋体">结束时间</span></p><p>&nbsp;&nbsp; 'aaa', -- <span style="font-family:   宋体">批准人</span></p><p>&nbsp;&nbsp; 'demo', -- <span style="font-family:   宋体">创建人</span></p><p>&nbsp;&nbsp; '2015-08-03 00:00', -- <span style="font-family:宋体">开始时间</span></p><p>&nbsp;&nbsp; '2015-08-03 11:47:04', -- <span style="font-family:宋体">创建时间</span></p><p>&nbsp;&nbsp; '100001',&nbsp; &nbsp; -- <span style="font-family:宋体">出差人编码</span></p><p>&nbsp;&nbsp; 'DS01',&nbsp; &nbsp; -- <span style="font-family:宋体">出差类型</span></p><p>&nbsp;&nbsp; 'bbb', -- <span style="font-family:   宋体">出差事由</span></p><p>&nbsp;&nbsp; 'ccc111' , -- <span style="font-family:宋体">备注</span></p><p>&nbsp;&nbsp; 'demo',--&nbsp; &nbsp; <span style="font-family:宋体">审核人名</span>&nbsp;</p><p>&nbsp;&nbsp; '2015-08-03 11:47:04' , --&nbsp; <span style="font-family:宋体">审核时间</span>&nbsp;</p><p>&nbsp;&nbsp; 1 , -- <span style="font-family:   宋体">是否审核</span></p><p>&nbsp;&nbsp; 'demo'&nbsp; &nbsp; --<span style="font-family:宋体">审核人编码</span></p><p>&nbsp;</p></td><td width="39" valign="top" height="216" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color black black -moz-use-text-color; padding: 0px 7px;"><br></td><td width="10" valign="top" height="216" style="border-width: medium 1px 1px medium; border-style: none solid solid none; border-color: -moz-use-text-color black black -moz-use-text-color; padding: 0px 7px;"><br></td></tr></tbody></table>	
code;
		$content = htmlToWordXml($content);
// 		dump($content);
// 		exit;
		$PHPWord = new PHPWord();
		
// 		dump(htmlToWordXml($content));
// 		exit;
		$docfile = UPLOAD_SampleWord."4444.docx";
		$filenameUTF8 = UPLOAD_SampleWord."5555.docx";
		$document = $PHPWord->loadTemplate($docfile);
		$document->clearAllBiaoji();
		$content = <<<code
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:ve="http://schemas.openxmlformats.org/markup-compatibility/2006" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" xmlns:m="http://schemas.openxmlformats.org/officeDocument/2006/math" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing" xmlns:w10="urn:schemas-microsoft-com:office:word" xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main" xmlns:wne="http://schemas.microsoft.com/office/word/2006/wordml"><w:body>$content</w:body></w:document>
code;
// 		dump($content);exit;
// 		$bookNamearr = array();
// 		$bookNamearr["wang"] = array(
// 				"name" => "wang",
// 				"showname" => "",
// 				"original" => $content,
// 				"value" => $content,
// 				"is_datatable" => 0,
// 				"istestarea" => 1
// 		);
// 		foreach($bookNamearr as $k => $v)
// 		{
// 			if($v["is_datatable"]){
// 				$data = array();
// 				$data["showname"] = $v["showname"];
// 				$data["value"]= $v["value"];
// 				$data["showtype"]= $v["showtype"]!==NULL?$v["showtype"]:0;
// 				$data["showtitle"]= $v["showtitle"]!==NULL?$v["showtitle"]:0;
// 				if(isset($v["colORrow"])){
// 					$data["colORrow"] = $v["colORrow"];
// 				}
// 				if(is_array($v["value"]) && !isset($v["colORrow"])){
// 					foreach($v["value"] as $kk => $vv){
// 						$data["titleArr"][] = empty($vv["showname"])?"":$vv["showname"];
// 					}
// 				}
//  				$document->setValue($v["name"],$data);
// 			}else{
				
// 				$document->setValue($v["name"],$v);
// 			}
// 		}
// 		$document->clearTemplateTag();
// 		$document->save($filenameUTF8);
// 		$filenameUTF8 = UPLOAD_SampleWord."6789.docx";
// 		$document = $PHPWord->loadTemplate($imgurl);
		$document->setStr($content);
		$document->save($filenameUTF8);
		header("Cache-Control: public");
		header("Content-Type: application/force-download");
		header("Content-Disposition: attachment; filename=".basename($filenameUTF8));
		readfile($filenameUTF8);
	}
	
	function getDBData($id=''){
		getDBData();
	}
	function lookupaddsqlresult(){
		$tableArr=$_POST['tableArr'];
		//查询表字段
		$modelname=$_POST['modelname'];
		$list=array();
		$model=D("Dynamicconf");
		$tableinfo=unserialize(base64_decode($tableArr)); 
		$i=1;
		$k=1;
		foreach ($tableinfo as $key=>$val){
			$actiontitle=getFieldBy($key,"actionname","actiontitle", "MisDynamicFormManage");
			$treemiso[]=array(
					'id'=>-$k,
					'pId'=>0,
					'name'=>$actiontitle,
					'title'=>$actiontitle,
					'open'=>true,
					'isParent'=>true,
			);
			foreach ($val as $tkey=>$tval){ 
				$resultlist=$model->getTableInfo($tval); 
				if($resultlist){
					foreach ($resultlist as $tinfokey=>$tinfoval){
						$treemiso[]=array(
								'id'=>$i,
								'pId'=>-$k,
								'name'=>$tinfoval['COLUMN_COMMENT']."【{$tinfoval['COLUMN_NAME']}】",
								'title'=>$tinfoval['COLUMN_COMMENT']."【{$actiontitle}】【{$tinfoval['COLUMN_NAME']}】【{$tinfoval['DATA_TYPE']}】",
								'tableinfo'=>$tinfoval['TABLE_NAME'].".{$tinfoval['COLUMN_NAME']}",
								'open'=>true,
								'isParent'=>true,
										);
						$i++;
					}
				}	
			}
			$k++;
		}
		$this->assign("inputname",$_POST['inputname']);
		$this->assign("listArr",$_POST['listArr']);
		$this->assign("order",$_POST['order']);
		$this->assign("tree",json_encode($treemiso));
		$this->display("Public:lookupaddsqlresult");		
	}
	/**
	*	下拉树，弹框显示框,只做单纯显示页面。DWZ框架限制
	*	@author nbmxk
	*	@date 2015-08-05 18:27
	*/
	function comboxtreedialog(){
		$treeid = $_GET['treeid'];
		$this->assign('treeid',$treeid);
		$this->display('Public:comboxtreedialog');
	}
	function lookuptreetable(){
		$model=D("Dynamicconf");
		$tableinfo=$_POST['tableinfo'];
		$pid=$_POST['id'];
		$resultlist=$model->getTableInfo($tableinfo);
		if($resultlist){
			foreach ($resultlist as $tinfokey=>$tinfoval){
				$treemiso[]=array(
						'id'=>'-10000'.$tinfokey,
						'pId'=>$pid,
						'name'=>$tinfoval['COLUMN_COMMENT']."【{$tinfoval['COLUMN_NAME']}】",
						'title'=>$tinfoval['COLUMN_COMMENT']."【{$actiontitle}】【{$tinfoval['COLUMN_NAME']}】【{$tinfoval['DATA_TYPE']}】",
						'tableinfo'=>$tinfoval['TABLE_NAME'].".{$tinfoval['COLUMN_NAME']}",
						'open'=>true,
				);
			}
		}
		echo  json_encode($treemiso);
	}
	
	/**
	 * @Title: dataInteractionPrepareData
	 * @Description: todo(数据交互 前置数据) 
	 * @param string $id  数据id
	 * @author 谢友志 
	 * @date 2015-9-18 下午4:02:56 
	 * @throws
	 */
	function lookupdataInteractionPrepareData($id){
		$actionName = $this->getActionName();
		$file =  ROOT."/Conf/datainteraction.php";
		if(file_exists($file)){
			$list = require $file;	
			
			//有相关数据才设置$_REQUEST['data_interaction']		
			if($list[$actionName]){
				$_REQUEST['data_interaction'] = $list[$actionName];
				$_REQUEST['data_interaction']['tablename'] = D($actionName)->getTableName();
				$_REQUEST['data_interaction']['id'] = $id;
			}
			
		}
	}
	/**
	 * @Title: dataInteraction
	 * @Description: todo(数据交互)   
	 * @author 谢友志 
	 * @date 2015-9-18 下午4:03:46 
	 * @throws
	 */
	function lookupdataInteraction(){		
		$tablename = $_POST['tablename'];
		$id = $_POST['id'];
		$sql = str_replace("&#39;","'",html_entity_decode($_POST['sql']));
		if($sql){
			$ieval="";
			//$sql="SELECT   * FROM  mis_auto_mnkrf  LEFT JOIN `mis_auto_bklne` ON mis_auto_mnkrf.projectid = mis_auto_bklne.id WHERE mis_auto_mnkrf.status = 1 AND mis_auto_bklne.id = mis_auto_mnkrf.projectid ";
			$whereCount = preg_match('/(\bwhere\b)/i', $sql);
			if($whereCount==1){
				$ret = preg_split('/(\bwhere\b)/i', $sql, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE );
				//强制组装当前数据id 至where 最前端
				$resql=" ".$tablename.".id={$id} and ".$ret[2];
				$ret[2]=$resql;
				$sql = join($ret);
			}else{
				$sql .= " WHERE ".$tablename.".id='{$id}'";
			}
		}
		$dbsource = $_POST['dbsource'];
		$proname = $_POST['proname'];
		$url = $_POST['url'];
		//测试查询
		$result=M()->query($sql);
		echo M()->getlastsql();
		import ( '@.ORG.EsbApi.EsbLinkApi' );
		$a=new esblink();
		//循环执行数据交换
		foreach($result as $key => $val){
			$a->main($url,$dbsource,$proname,$val);
		}
	}
	
	/**
	 * @Title: enterToSubmitAudit 
	 * @Description: todo(套表启动流程弹出意见框)   
	 * @author wangzhaoxia
	 * @date 2015-9-21 下午3:54:15 
	 * @throws
	 */
	function enterToSubmitAudit(){
		$type = $_REQUEST["type"];
		$module = $_REQUEST["module"];
	
		$form = $module.'_'.$type;
		$this->assign('form',$form);
		$this->display("Public:enterToSubmitAudit");
	}
	
	function getTMLNavTabId(){
		$module = $_REQUEST["module"];
		$orderno = $_REQUEST["orderno"];
		if($module&&$orderno){
			$model=D($module);
			$map["orderno"] = $orderno;
			$id = $model->where($map)->getField("id");
		}
		$url = "";
		if($id){
			$url = __ROOT__."/Admin/index.php/".$module."/view/id/".$id;
		}
		echo $url;
	}
	
	function saveOnlineEditWord(){
// 		$ip = GetHostByName($_SERVER['SERVER_NAME']);//获取本机IP
		$ip = C("DB_HOST_WORD");//"192.168.0.238";
		require_once("http://{$ip}:8088/JavaBridge/java/Java.inc");//此行必须
		$fs = new Java("com.zhuozhengsoft.pageoffice.FileSaverPHP");//此行必须
		echo $fs->close();//此行必须
		$fs->load(file_get_contents("php://input"));//此行必须
	
		java_set_file_encoding("utf8");//设置编码格式
		$socuse = str_replace("\\", "/", base64_decode($_REQUEST['name']));
		$file_path = str_replace("\\","/", UPLOAD_PATH.$socuse);
		$fs->saveToFile($file_path); //保存文件
		echo $fs->close();//此行必须
	}
	/**
	 * @Title: lookupkufubianhaos
	 * @Description: todo(通过客服编号查询关联的所有编号)
	 * @author liuzhihong
	 * @date 2015-10-22 下午3:54:15
	 * @throws
	 */
	//记录客服编号
	private $kufubianhao = array();
	//获取客服关联的项目编号的递归方法
	function lookupkufudigui($kufubianhao){
		$banmoModel = M('mis_auto_banmo');
		$datatable37 = M('mis_auto_banmo_sub_datatable37');
		$map['orderno'] = $kufubianhao;
		//根据order查询到ID
		$list=$banmoModel->where($map)->field("id")->select();
		foreach($list as $key=>$val){
			//用ID作为条件查询下级编号
			$mapa['masid'] = $val['id'];
			$listbianhaos = $datatable37->where($mapa)->field("kehubianma")->select();
			foreach ($listbianhaos as $kk=>$vv){
				//记录数组里面的东西
				$panduanID = true;
				if($vv['kehubianma']){
					//循环记录客服编号的数组 如果有则不则跳过记录 而且不会查询当前客服下的数据
					foreach($this->kufubianhao as $k=>$v){
						if($v==$vv['kehubianma']){
							$panduanID = false;
						}
					}
					//如果记录里没有则先添加进数组 然后重新调用自己查询这个编号下的所有数据
					if($panduanID){
						$this->kufubianhao[]=$vv['kehubianma'];
						$this->lookupkufudigui($vv['kehubianma']);
					}
				}
			}
		}
	}
	//获取客户所有关联的项目编码
	function lookupkufubianhaos(){
		$listfanhui = array();
		$kufubianhao = $_POST['kufubianhao'];
		$this->lookupkufudigui($kufubianhao);
		echo json_encode($this->kufubianhao);
	}
	/**
	 * 图片截取-通用函数
	 * @Title: lookupCropImage
	 * @Description: 图片截取-通用函数
	 *     <pre>
	 *     按传入的起点及高宽 将原图片截取，如果没有坐标信息不报错但也不处理
	 *     截取后的内容如果没有指定输出路径就覆盖原图片。
	 *     处理后输出路径
	 *     </pre>
	 * @param array $_POST   src：原始图片地址，pointx ,pointy , pointw, pointh,五个参数任一参数缺失不执行功能
	 *                         dst_path 输出路径，附加非必须参数
	 * @author quqiang 
	 * @date 2015年10月21日 上午11:00:10 
	 * @throws 基本参数值不足，处理文件不存在
	 */
	function lookupCropImage(){
	    try {
	        // 图片起始点及高宽
	        $x = intval($_POST['pointx']);
	        $y = intval($_POST['pointy']);
	        $w = intval($_POST['pointw']);
	        $h = intval($_POST['pointh']);
	        $src = $_POST['src'];
	        // 处理后输出路径
	        $dst_path = $_POST['dst_path'];
	        $filepath = explode(__ROOT__."/Public/Uploadstemp", $src);
	        $src=UPLOAD_PATH_TEMP.$filepath[1];
	        if(!$src){
	            $msg = "来源图片位置未指定";
	            throw new NullDataExcetion($msg);
	        }
	        if(!file_exists($src)){
	            $msg = "来源图片不存在";
	            throw new NullDataExcetion($msg.$src);
	        }
	        if($w && $h){
                $size=@getimagesize($src);
                switch($size['mime']){
                    case 'image/jpeg': $src_pic = imagecreatefromjpeg($src);break;
                    case 'image/gif' : $src_pic = imagecreatefromgif($src);break;
                    case 'image/png' : $src_pic = imagecreatefrompng($src);break;
                    default: $src_pic=false;break;
                }
                $savePath = $dst_path?$dst_path : $src;
	            //创建图片
	            $dst_pic = imagecreatetruecolor($w, $h);
	            $ret = imagecopyresampled($dst_pic,$src_pic,0,0,$x,$y,$w,$h,$w,$h);
	            if(!$ret){
	                $msg = "剪截内容生成失败，联系管理员!";
	                throw new NullDataExcetion($msg);
	            }
	            imagejpeg($dst_pic, $savePath);
	            imagedestroy($src_pic);
	            imagedestroy($dst_pic);
	            $this->success('剪裁完成');
	        }
	    } catch (Exception $e) {
	        $this->error($e->getMessage());
	    }
	}
	
	function lookupCrop(){
		import('@.ORG.CropAvatar');
		$src = $_POST['avatar_src'];
		$width = $_POST['avatar_width']?$_POST['avatar_width'] : 200;
		$height = $_POST['avatar_height']? $_POST['avatar_height'] : 200;
		
		// 分解URL地址信息 可已入库与未入库的都可
		// 分解 路径 ，区分 已上传 与 临时上传
		$isTemp = false;
		if(strpos($src, 'Uploadstemp/') < -1 ){
			$isTemp = true;
		}
		$data = preg_split('/Uploadstemp\/|Uploads\//', $src);
		if($isTemp){
			$src = UPLOAD_PATH.$data[1];
		}else{
			$src = UPLOAD_PATH_TEMP.$data[1];
		}
		$putdata = $_POST['avatar_data'];
		$crop = new CropAvatar($src, $putdata,$src , $width , $height);
		
// 		$response = array(
// 				'state'  => 200,
// 				'message' => $crop -> getMsg(),
// 				'result' => $crop -> getResult()
// 		);
		
// 		echo json_encode($response);
		$this->success('剪裁完成');
	}
	/**
	 * 显示图片剪裁操作页面
	 * @Title: lookupCropShow
	 * @Description: todo(这里用一句话描述这个方法的作用)   
	 * @author quqiang 
	 * @date 2015年10月21日 下午1:48:28 
	 * @throws
	 */
	function lookupCropShow(){
	    try{
	    	// 截取高宽
	    	$wh = '';
	        $src =$_POST['src'];// str_replace('/systemui', '', $_POST['src']);
	        $config = $_POST['config'];
	        if(!is_array($config)){
	            $config = '';
	        }else{
	            foreach ($config as $key=>$val){
	                if(is_array($val)){
	                    foreach ($val as $k=>$v){
	                        $val[$k] = intval($v);
	                    }
	                    $config[$key] = $val;
	                }else{
	                    $config[$key] = $val;
	                }
	            }
	            if($config['wh']){
	            	$wh = $config['wh'];
	            }
	            $config =  json_encode($config) ; //str_replace('"', '', json_encode($config) );
	        }
	        $this->assign('config',$config);
	        if(!$src){
	            $msg ="裁剪图片来源未知";
	        }
	        
	        $filepath = explode(__ROOT__."/Public", $src);
	        $absSrc =PUBLIC_PATH.$filepath[1];
	        if(!file_exists($absSrc)){
	            $msg = "来源图片不存在";
	            throw new NullDataExcetion($msg);
	        }
	        $image_size = getimagesize($absSrc);
	        if($wh && ( $wh['width'] >=$image_size[0] || $wh['height'] >=$image_size[1]) ){
				$this->error('图片尺寸小于或等于剪裁值，不可进行剪裁操作！');
	        }
	        $this->assign('showSrc',$src);
	        $this->assign('src',$filepath[1]);
	        $this->assign('width',$image_size[0]);
	        $this->assign('height',$image_size[1]);
	        $this->display('Public:lookupCropShow');
	    }catch (Exception $e){
	        $this->error($e->getMessage());
	    }
	}
	
	/**
	 * 
	 * @Title: rebuildSetting
	 * @Description: todo(为防止旧表单的运行错误加上的一个辅助无功能函数)   
	 * @author quqiang 
	 * @date 2016年1月7日 下午3:01:19 
	 * @throws
	 */
	function rebuildSetting(){
		$setting=array();
/* 		
 *多个追加标签间不能有字符必须为一行
 *以下为事例 
$setting['hiddens']="<input type=hidden value=MisAutoSxl name=jumpaction />"
				."<input type=hidden name=refreshtabs[data] value=1 />"
				."<input type=hidden name=callbackType value=closeCurrent />";
 */		
		$setting['hiddens']='';
		$setting['callback']=array(
				/* 
				'common'=>'return validateCallback(this, navTabAjaxDone)', // 普通表单时使用回调
				'audit'=>'return validateCallback(this, navTabAjaxDone)',// 审批表时使用的回调
				'callback'=>'return iframeCallback(this, navTabAjaxDone)', */
				// 套表时二开使用的
				'common'=>'', // 普通表单时使用回调
				'audit'=>'',// 审批表时使用的回调
				// 非套表时二开使用
				'callback'=>'',
		);
		$setting['url']=array(
				//暂时不用 ，为做提交地址作变更准备。
				'add' =>'updateControll', 	// 新增页面操作提交地址
				'edit' =>'updateControll', 	// 修改页面操作提交地址
				'add' =>'updateControll', 	// 查看页面操作提交地址
		);
		// 提交地址附加参数
		$setting['urlparame']='';
		return $setting;
	}
}
?>
