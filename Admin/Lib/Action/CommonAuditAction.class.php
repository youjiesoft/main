<?php
/**
 * @Title: CommonAuditAction
 * @Package package_name
 * @Description: todo(审核公共处理器)
 * @author 杨东
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2012-3-16 下午5:12:40
 * @version V1.0
 */
class CommonAuditAction extends CommonAction {
	public function index() { 
		//左边菜单
		$name = $this->getActionName();
		$groupid = getFieldBy($name, 'name', 'group_id', 'node');
		$model = D('Public');
		$accessNode = $model->menuLeftTree($groupid);
		$this->assign('accessNode',$accessNode);
		if ($_REQUEST ['type']) {
			$type = $_POST ['type']?$_POST ['type']:$_REQUEST ['type'];
			// 获取当前控制器
			$name = $this->getActionName ();
			// 当请求进入到此处撒。保留用户点击留下的查询条件
			//Cookie::delete ( $_SESSION [C ( 'USER_AUTH_KEY' )] . $name . 'defaultSelect' );
			//Cookie::set ( $_SESSION [C ( 'USER_AUTH_KEY' )] . $name . 'defaultSelect', $type );
			$this->assign ( "type", $type );
			
			// 构造检索条件
			$map = $this->_search ( $name );
			// 验证浏览及权限
			$qx_name = $name;
			if (substr ( $name, - 4 ) == "View") {
				$qx_name = substr ( $name, 0, - 4 );
			}
			if (! isset ( $_SESSION ['a'] )) {
				$map = D ( 'User' )->getAccessfilter ($qx_name,$map);
			}
			if ($type == 2) {
				// 待启动单据
				$map ['auditState'] = array (
						'eq',
						0 
				);
			} else if ($type == 3) {
				// 审核中
				$map ['_string'] = "auditState > 0 and auditState <3";
			} else if ($type == 4) {
				// 审核完毕
				$map ['auditState'] = array ('eq',3);
			} else if ($type == 5) {
				// 未批准
				$map ['auditState'] = array ('eq',-1);
			}
			if (method_exists ( $this, '_filter' )) {
				$this->_filter ( $map );
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
				$this->_list ( $name, $map,'', false,'*','',$sortsorder);
			}
			//列表页排序 ---结束-----
			
			// 存在滚动加载时，进行模板输出
			if (intval ( $_POST ['dwzloadhtml'] )) {
				$this->display ( "dwzloadindex" );
				exit ();
			}
			// 此处的indextype参数，是在getAuditTree方法中传入过来的。起判断页面加载作用
			if (! $_POST ['indextype']) {
				$this->display ( 'indexview' );
			}
		} else {
			$this->getAuditTree ();
			$this->display ( "CommonAudit:index" );
		}
	}
	/**
	 * @Title: getAuditTree
	 * @Description: todo(获取审核列表树)
	 * 
	 * @author 杨东
	 *         @date 2013-6-14 上午9:16:20
	 * @throws
	 *
	 */
	protected function getAuditTree() {
		// 第一步获取当前模块节点名称
		$name = $this->getActionName ();
		$nodetitle = getFieldBy ( $name, 'name', 'title', 'node' );
		$this->assign ( "nodeltitle", $nodetitle );
		
		// 存储字段，标志本次进入后，不进入indexview页面
		$_POST ['indextype'] = 1;
		
		/**
		 * **********此部分将进行cookie控制，如果需要***************
		 */
		$defaultSelect = $_REQUEST ['default'];
		//if (! $defaultSelect) {
			//$defaultSelect = Cookie::get ( $_SESSION [C ( 'USER_AUTH_KEY' )] . $name . 'defaultSelect' );
		//}
		if (! $defaultSelect) {
			// 不存在默认值，则默认查询待启动数据
			$defaultSelect = 2;
			if($_REQUEST['projectid'] && $_REQUEST['projectworkid']){
				//如果是项目进入的，直接查询全部数据
				$defaultSelect = 1;
			}
		}
		$this->assign ( "type", $defaultSelect );
		// 标识字段
		$_REQUEST ['type'] = $defaultSelect;
		
		if ($defaultSelect < 7) {
			// 我的单据
			$this->index (); // 再次调用index方法，进行请求数据
		} else if ($defaultSelect == 7) {
			// 审核中
			$this->waitAudit ();
		} else if ($defaultSelect == 8) {
			// 审核完毕
			$this->alreadyAudit ();
		}
	}
	
	/**
	 * +----------------------------------------------------------
	 * 根据表单生成查询条件
	 * 进行列表过滤
	 * +----------------------------------------------------------
	 * 
	 * @access protected
	 *         +----------------------------------------------------------
	 * @param Model $model
	 *        	数据对象
	 * @param HashMap $map
	 *        	过滤条件
	 * @param string $sortBy
	 *        	排序
	 * @param boolean $asc
	 *        	是否正序
	 * @param
	 *        	echoSql 当等1时， 输出记算行数的sql语名；
	 *        	+----------------------------------------------------------
	 * @return void +----------------------------------------------------------
	 * @throws ThinkExecption +----------------------------------------------------------
	 */
	protected function _list($name, $map, $sortBy = '', $asc = true, $countfield = '*', $echoSql = '0',$sortstr='') {
		// ------首页小模块组合查询的条件------//
		if ($_REQUEST ['remindMap']) {
			$remindMap = base64_decode ( $_REQUEST ['remindMap'] );
			if ($map['_string']) {
				$map['_string'] .= " and " . $remindMap;
			} else {
				$map['_string'] = $remindMap;
			}
			$this->assign("remindMap",$_REQUEST ['remindMap']);
		}
		// end
		// 查询当前用户
		if ($_SESSION ['a'] != 1) {
			import("@.ORG.Browse");
			$broMap = Browse::getUserMap ( $this->getActionName() );
// 			if ($broMap) {
// 				if($map['_string']){
// 					$map['_string'] .= " and " . $broMap;
// 				}else{
// 					$map['_string']= $broMap;
// 				}
// 			}
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
		if($_REQUEST['projectid'] && $_REQUEST['projectworkid']){
			$map['projectid']= $_REQUEST['projectid'];
		}
		// 获取当前控制器名称
		$actionname = $this->getActionName ();
		
		// begin
		$scdmodel = D ( 'SystemConfigDetail' );
		// 读取列名称数据(按照规则，应该在index方法里面)
		$detailList = $scdmodel->getDetail ( $actionname );
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		// 扩展工具栏操作
		$toolbarextension = $scdmodel->getDetail ( $actionname,true,'toolbar','sortnum','shows',true);
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		// end
		
		$model = D ( $name );
		// 排序字段 默认为主键名
		if (isset ( $_REQUEST ['orderField'] )) {
			$order = $_REQUEST ['orderField'];
		} else {
			//$order = 'auditState'; //龚云要求全改为默认id升序 --xyz
			$order = 'createtime'; 
		}
		// 排序方式默认按照倒序排列
		// 接受 sost参数 0 表示倒序 非0都 表示正序
		$ss = strtolower(C("SYSTEM_SORT"));
		$ssu = $ss == 'asc'?'desc':'asc';
		
		if (isset ( $_REQUEST ['orderDirection'] )) {
			$sort = $_REQUEST ['orderDirection'];
		} else {			
			$sort = $asc ? $ssu : $ss;
		}
		
		//$orderBy =  "`" . $order . "` " . $sort . ',id '. $ss;
		$orderBy =  "`" . $order . "` " . $sort ;
		//特殊处理 字符串排序参数 write by xyz
		if($sortstr){
			$orderBy = $sortstr;
		}
		/* ***************** 搜索修改 ***************** */
		if ($_POST ['search_flag'] == 1) {
			// 检索信息，返回&$map条件
			$this->setAdvancedMap ( $map );
		}
		// 取得满足条件的记录数
		$count = $model->where ( $map )->count ("*");
		// 传参开启调式 eagle
		if ($echoSql) {
			echo $model->getLastSql ();
		}
		/* ***************** 搜索修改 ***************** */
		if ($count > 0) {
			import ( "@.ORG.Page" );
			// 创建分页对象
			$numPerPage = C ( 'PAGE_LISTROWS' );
			$dwznumPerPage = C ( 'PAGE_DWZLISTROWS' );
			if (! empty ( $_REQUEST ['numPerPage'] )) {
				$numPerPage = $_REQUEST ['numPerPage'];
			}
			if ($_POST ["dwzpageNum"] == "")
				$dwznumPerPage = $numPerPage;
			$p = new Page ( $count, $numPerPage, '', $dwznumPerPage );
			// 分页查询数据
			if ($_POST ['dwzloadhtml'])
				$p->firstRow = $p->firstRow + (intval ( $_POST ['dwzpageNum'] ) - 1) * $numPerPage;
				
				/* ***************** 搜索修改 ***************** */
			if ($_POST ['export_bysearch'] == 1) { // 如果是导出则无分页
				$voList = $model->where ( $map )->order ($orderBy)->select ();
			} else {
				$voList = $model->where ( $map )->order ( $orderBy )->limit ( $p->firstRow . ',' . $p->listRows )->select ();
			}
			
			// 处理lookup数据 by杨东
			if($_POST['dealLookupList']==1){
				$this->dealLookupDelegate($voList,$name,$_POST['dealLookupType'],$_POST['viewname']);
			}
			
			// 给每条数据分配该有的toolbar操作按钮
			$this->setToolBorInVolist ( $voList );
			
			/* ***************** 搜索修改 ***************** */
			$module = A ( $name );
			if (method_exists ( $module, "_after_list" )) {
				// 这里的 & 符号必须加上。不然进不到_after_list
				call_user_func ( array (
						&$module,
						"_after_list" 
				), $voList );
			}
			// 如果是导出直接输出到excel
			if ($_POST ['export_bysearch'] == 1) {
				$this->exportBysearch ( $voList );
			}
			// 分页显示
			$page = $p->show ();
			// 列表排序显示
			$sortImg = $sort; // 排序图标
			$sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; // 排序提示
			$sort = $sort == 'desc' ? 'desc' : 'asc'; // 排序方式
			$pageNum = ! empty ( $_REQUEST [C ( 'VAR_PAGE' )] ) ? $_REQUEST [C ( 'VAR_PAGE' )] : 1;
			// 模板赋值显示
			$this->assign ( 'pageNum', $pageNum );
			$this->assign ( 'list', $voList );
			$this->assign ( "page", $page );
		} else {
			if ($_POST ['export_bysearch'] == 1) {
				$this->exportBysearch ( array () );
			}
		}
		$this->assign ( 'sort', $sort );
		if($sortstr){
			$this->assign ( 'order', $orderBy );
		}else{
			$this->assign ( 'order', $order );
		}
		
		$this->assign ( 'sortImg', $sortImg );
		$this->assign ( 'sortType', $sortAlt );
		$this->assign ( 'totalCount', $count );
		$this->assign ( 'numPerPage', $numPerPage );
		$this->assign ( 'dwznumPerPage', C ( 'PAGE_DWZLISTROWS' ) );
		$this->assign ( 'dwztotalpage', C ( 'PAGE_DWZLISTROWS' ) / $numPerPage );
		$this->assign ( 'currentPage', ! empty ( $_REQUEST [C ( 'VAR_PAGE' )] ) ? $_REQUEST [C ( 'VAR_PAGE' )] : 1 );
		Cookie::set ( '_currentUrl_', __SELF__ );
	}
	/**
	 * @Title: waitAudit
	 * @Description: 查询待审核数据信息
	 * 
	 * @author liminggang
	 *         @date 2014-9-11 下午6:29:24
	 * @throws
	 *
	 */
	public function waitAudit() {
		// 获取当前控制器名称
		$name = $this->getActionName ();
		
		//去监控表查询当前我可执行的数据
		$where = array();
		$where['tablename'] = $name;//当前模型
		$where['dostatus'] = 0;//未完成
		$where['isauditstatus'] = 1;//1 为子流程未开始。
		$where['_string'] = 'FIND_IN_SET(  ' . $_SESSION [C ( 'USER_AUTH_KEY' )] . ',curAuditUser )';
		$mis_work_monitoringDao = M("mis_work_monitoring");
		$tableidArr = $mis_work_monitoringDao->where($where)->getField("orderno,tableid");
		
		// 配置检索条件，检索功能。
		$map = $this->_search ( $name );
		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$map ['status'] = 1;
		if($tableidArr){
			$map ['id'] = array(" in ",$tableidArr);
		}else {
			$map ['id'] = 0;
		}
		
		$this->_list ( $name, $map, '', true );
		
		// 因为待审核和审核页面是公用页面。所有必须传入一个请求的方法。
		$this->assign ( 'jumpUrl', 'waitAudit' );
		// 作用于 auditIndex页面的按钮控制，0为审核。1为查看
		$this->assign ( 'audit', '0' );
		$type = $_REQUEST ['type'];
		if ($type) {
			Cookie::delete ( $_SESSION [C ( 'USER_AUTH_KEY' )] . $name . 'defaultSelect' );
			// 存储当前用户选中的默认节点
			Cookie::set ( $_SESSION [C ( 'USER_AUTH_KEY' )] . $name . 'defaultSelect', $type );
		}
		$this->assign ( "type", $type );
		if (! $_POST ['indextype']) {
			$this->display ( 'auditIndex' );
		}
	}
	// 获取已审核数据
	public function alreadyAudit() {
		// 获取当前控制器名称
		$name = $this->getActionName ();
		
		//去监控表查询当前我可执行的数据
		$where = array();
		$where['tablename'] = $name;//当前模型
		$where['dostatus'] = 1;//未完成
		$where ['userid'] = $_SESSION [C ( 'USER_AUTH_KEY' )];
		$mis_work_monitoringDao = M("mis_work_monitoring");
		$tableidArr = $mis_work_monitoringDao->where($where)->getField("orderno,tableid");
		
		// 配置检索条件，检索功能。
		$map = $this->_search ( $name );
		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$map ['status'] = 1;
		if($tableidArr){
			$map ['id'] = array(" in ",$tableidArr);
		}else {
			$map ['id'] = 0;
		}
		
		$this->_list ( $name, $map );
		
		// 因为待审核和审核页面是公用页面。所有必须传入一个请求的方法。
		$this->assign ( 'jumpUrl', 'alreadyAudit' );
		// 作用于 auditIndex页面的按钮控制，0为审核。1为查看
		$this->assign ( 'audit', '1' );
		
		$type = $_REQUEST ['type'];
		if ($type) {
			Cookie::delete ( $_SESSION [C ( 'USER_AUTH_KEY' )] . $name . 'defaultSelect' );
			// 存储当前用户选中的默认节点
			Cookie::set ( $_SESSION [C ( 'USER_AUTH_KEY' )] . $name . 'defaultSelect', $type );
		}
		$this->assign ( "type", $type );
		
		if (! $_POST ['indextype']) {
			$this->display ( 'auditIndex' );
		}
	}
	/**
	 * @Title: auditEdit
	 * @Description: 打开审核明细界面
	 * @author liminggang
	 * @date 2014-9-12 上午10:54:29
	 * @throws
	 */
	public function auditEdit() {
		//获取到对应需要修改的单据ID
		$id = $_GET ['id'];
		//实例化对应数据表模型
		$name = $this->getActionName ();
		//查询当前审批节点  model 权限取值begin
		$mis_work_monitoringDao = M("mis_work_monitoring");
		$userid = $_SESSION[C('USER_AUTH_KEY')];
		$map['tablename'] = $name;
		$map['tableid'] = $id;
		$monlist = $mis_work_monitoringDao->where($map)->field("curAuditUser,parallel,dostatus,ostatus")->order("id desc")->select();
		$bool = true;
		if(count($monlist)>0){
			foreach($monlist as $mk=>$mv){
				//获取历史节点ID
				$relationid = getFieldBy($mv['ostatus'], "id", "relationid", "process_relation_form");
				if($relationid < 1422900000){
					$_SESSION["nodeActionName".$userid] = $name;
					$_SESSION['nodeAuditId'.$userid] = $relationid;
					$_REQUEST['node'] = $relationid;
					break;
				}
			}
		}
		//model 权限取值 End
		
		// 审批流 动态建模页面时的子表数据获取 add by nbmkxj@20150129 2214
		$viewCheckPath = LIB_PATH.'Model/'.$name.'ViewModel.class.php';
		if(is_file($viewCheckPath)){
			$model = D ( $name.'View' );
		}else{
			$model = D ( $name);
		}
		$map = array();
		$map ['id'] = $id;
		//赋值数据表模型
		$vo = $model->where ( $map )->find ();
 
		// 获取附件信息
		$this->getAttachedRecordList ( $id, true, true, $name );
		
		$module = A ( $name );
		//验证当前实例化的控制器中，是否存在_after_edit方法。
		if (method_exists ( $module, "_after_edit" )) {
			call_user_func ( array (
					&$module,
					"_after_edit" 
			), $vo );
		}
		//配置表单字段显示
		$this->getSystemConfigDetail ( $name,$vo );
		$this->assign ( 'vo', $vo );
		if($bool){
			$this->display ();
		}else{
			$this->display ("auditView");
		}
	}
	// 查看审核明细界面
	public function auditView() {
		// 获取到对应需要修改的单据ID
		$id = $_GET ['id']; 
		// 实例化对应数据表模型
		$name = $this->getActionName ();
		// 审批流 动态建模页面时的子表数据获取 add by nbmkxj@20150129 2214
		$viewCheckPath = LIB_PATH.'Model/'.$name.'ViewModel.class.php';
		if(is_file($viewCheckPath)){
			$model = D ( $name.'View' );
		}else{
			$model = D ( $name);
		}
		
		$map ['id'] = $id;
		// 赋值数据表模型
		$vo = $model->where ( $map )->find ();
		$name = $this->getActionName();
		$remindMap=array();
		$remindMap['modelname']=$name;
		$remindMap['userid']= $_SESSION[C('USER_AUTH_KEY')];
		//$remindMap['isread']=0; by xyz 2015-11-03
		$remindMap['pkey']=$id;
		$remindModel=M("mis_system_sysremind");
		$result=$remindModel->where($remindMap)->find();
		if($result){ // by xyz 2015-11-03
			 if($result['isread']==0){
			 	$readdata['remindcreatetime'] = time();
				$readdata['isread'] = 1;
			 }else{
			 	$readdata['updatetime'] = time();
			 }
			$readdata['readcount'] = $result['readcount']+1;
			$remindModel->where($remindMap)->setField($readdata);//("isread",1);
		}
		// 获取附件信息
		$this->getAttachedRecordList ( $id, true, true, $name );
		
		$module = A ( $name );
		//验证当前实例化的控制器中，是否存在_after_edit方法。
		if (method_exists ( $module, "_after_edit" )) {
			call_user_func ( array (
					&$module,
					"_after_edit" 
			), $vo );
		}
		
		$this->assign ( 'vo', $vo );
		// 页面图标样式
		switch ($vo ['auditState']) {
			case - 1 :
				$this->assign ( "auditImgClass", "xyNotAudit" );
				break;
			case 1 :
				$this->assign ( "auditImgClass", "xyForAudit" );
				break;
			case 2 :
				$this->assign ( "auditImgClass", "xyNowAudit" );
				break;
			case 3 :
				$this->assign ( "auditImgClass", "xyHasAudit" );
				break;
		}
		// 配置表单字段显示
		$this->getSystemConfigDetail ( $name,$vo );
		$this->display ();
	}
	
	/**
	 * @Title: lookupAuditTuiProcess
	 * @Description: todo(流程审核转子流程生单方法)   
	 * @author 黎明刚
	 * @date 2015年3月10日 下午2:41:32 
	 * @throws
	 */
	public function lookupAuditTuiProcess(){
		$name = $this->getActionName();
		//获取当前表单ID值
		$id = $_POST['id'];
		$data = array();
		if($id){
			//获取流程审批节点信息
			$process_relation_formDao = M("process_relation_form");
			$where = array();
			$where['tablename'] = $name;
			$where['tableid'] = $id;
			$where['auditState'] = 0;//未处理
			$where['doing'] = 1;//进行中的节点
			$where['flowtype'] = array("gt",1);//审批节点或者转子流程节点
			$newinfo = $process_relation_formDao->where($where)->order('sort asc')->select();
			$infolist = array_merge($newinfo);
			$vo = $infolist[0];
			if($vo['flowtype'] == 3 && $vo['isauditmodel'] && $vo['issourcemodel']){
				//转子流程，进行对子流程权限的配置
				$MisSystemFlowWorkModel = D ( 'MisSystemFlowWork' );
				$MisSystemFlowWorkModel->pJAccesslist2($vo ['isauditmodel']);
				
				if($name == $vo['issourcemodel']){
					//来源对象和当前流程主模型一样，则直接获取数据
					$issourceid = $id;
				}else{
					//获取当前表的项目ID
					$projectid = getFieldBy($id, "id", "projectid", $name);
					$projectid = $projectid?$projectid:0;
					if($projectid){
						//根据projectid查询来源模型数据
						$issourcemodel = D($vo['issourcemodel']);
						//来源数据ID
						$issourceid = $issourcemodel->where("projectid = ".$projectid)->getField("id");
					}
				}
				$projectid = $projectid?$projectid:0;
				$issourceid = $issourceid?$issourceid:0;
				// 获取title名称
				$nodeModel = M("node");
				$nodemap['name'] = array('eq', $vo ['isauditmodel']);
				$nodevo = $nodeModel->where($nodemap)->field("title,isprocess")->find();
				$nodename = $nodevo['title'];
				//实例化子流程表
				$process_relation_childrenDao = M("process_relation_children");
				//找到原始流程的对象及ID
				$map = array();
				$map['tablename'] = $name; //子流程模型名称
				$map['tableid'] = $id;  //子流程id
				$map['relation_formid'] = $vo['id']; //审批节点
				if($vo['parallel'] > 0 ){
					//并行
					$map['createid'] = $_SESSION [C ( 'USER_AUTH_KEY' )]; //审核人
				}
				$childrenvo=M('process_relation_children')->where($map)->find();
				
				//计算查询审核人信息，是否绑定单据
				if($childrenvo['zitableid']){
					if($nodevo['isprocess'] == 1){
						//判定是否带审批流
						$auditState = getFieldBy($childrenvo['zitableid'], "id", "auditState", $childrenvo['zitablename']);
						if($auditState <= 0){
							$data ['url'] = __APP__ . "/" . $childrenvo['zitablename'] . "/edit/id/".$childrenvo['zitableid']."/auditZhuLicModel/".$name."/auditZhuLicId/".$id."/auditFlowTuiTablename/".$vo['issourcemodel']."/auditFlowTuiTableid/".$issourceid;
							$data ['rel'] = $vo ['isauditmodel'] . "edit";
							$data ['title'] = $nodename . "_修改";
						}else{
							$data ['url'] = __APP__ . "/" . $childrenvo['zitablename'] . "/auditView/id/".$childrenvo['zitableid'];
							$data ['rel'] = $vo ['isauditmodel'] . "view";
							$data ['title'] = $nodename . "_查看";
						}
					}else{
						$operateid = getFieldBy($childrenvo['zitableid'], "id", "operateid",$childrenvo['zitablename']);
						if($operateid == 0){
							$data ['url'] = __APP__ . "/" . $childrenvo['zitablename'] . "/edit/id/".$childrenvo['zitableid']."/auditZhuLicModel/".$name."/auditZhuLicId/".$id."/auditFlowTuiTablename/".$vo['issourcemodel']."/auditFlowTuiTableid/".$issourceid;
							$data ['rel'] = $vo ['isauditmodel'] . "edit";
							$data ['title'] = $nodename . "_修改";
						}else{
							$data ['url'] = __APP__ . "/" . $childrenvo['zitablename'] . "/view/id/".$childrenvo['zitableid'];
							$data ['rel'] = $vo ['isauditmodel'] . "view";
							$data ['title'] = $nodename . "_查看";
						}
					}
				}else{
					/*
					 * 此处。是子流程审核生单新增页面。如果需要项目ID和任务ID。必须加到这个URL地址上面。
					 * projectid 和 projectworkid
					 * URL地址型。那么没个参数不能为空。如果是空值。起码要默认为0
					 */
					$mis_project_flow_formDao = M("mis_project_flow_form");
					if($projectid){
						$projectmap['projectid'] = $projectid;    //来源信息
						$projectmap['formobj'] = $vo ['isauditmodel'];//子流程模型
						$projectworkid = $mis_project_flow_formDao->where($projectmap)->getField("id");
					}
					$projectworkid = $projectworkid?$projectworkid:0;
					$data ['url'] = __APP__ . "/" . $vo ['isauditmodel'] . "/add/auditZhuLicModel/".$name."/auditZhuLicId/".$id."/auditFlowTuiTablename/".$vo['issourcemodel']."/auditFlowTuiTableid/".$issourceid."/projectid/".$projectid."/projectworkid/".$projectworkid;
					$data ['rel'] = $vo ['isauditmodel'] . "add";
					$data ['title'] = $nodename . "_新增";
				}
				$data ['target'] = "navTab";
				$data ['json_Msg'] = 1;
			}else{
				$data ['json_Msg'] = 0;
				$data ['json_info'] = "信息错误，请关闭当前窗口并刷新";
			}
		}else{
			$data ['json_Msg'] = 0;
			$data ['json_info'] = "信息错误，请联系管理员！";
		}
		echo json_encode ( $data );
	}
	/**
	 * @Title: updateprocess
	 * @Description: todo(变更流程启动方法)
     * @param boolean $isrelation   是否返回状态
	 * @author 黎明刚
	 * @date 2015年3月4日 下午5:01:48
	 * @throws
	 */
	public function lookupUpdateProcess($isrelation=FALSE){
		//获取当前模型
		$modelname = $this->getActionName ();
		$model = D ( $modelname );
		//第二步、接收当前单据保存的ID
		$masid = $_POST['id'];
		$vo = $model->find($masid);//杨希处理  存过去的VO值
		//验证$masid是否有值(主要是为了判断单据新建时启动流程)
		if(!$masid){
		    if(true == $isrelation){
		        $msg = '当前单据保存失败，请联系管理员';
                throw NEW NullDataExcetion($msg);
		    }else{
			     $this->error("当前单据保存失败，请联系管理员");
            }
		}else{
			//获取当前表名
			$targetTable=$model->getTableName();
			//指定where查询条件
			$where=" where id=".$masid." ";
			$resumeArr=$model->_dataRoamResumeSql($targetTable,array_keys($vo),$where,$operation='update',$type=false);
			$data=array();
			$data ['tablename'] = $modelname;
			$data ['tableid'] = $masid;
			$data ['sqlresume'] = $resumeArr['sqlresume'];
			$data ['dataresume'] = $resumeArr['dataresume'];
			$data ['category'] = 1;	
			$data ['createtime'] = time();
			M('mis_system_data_resuming')->add( $data );
		}
		/*
		 * 第三步、判断是何种流程，
		 * 1、自定义流程   $flowid = $_POST ['flowid']; 如果获取到此参数。表示自定义流程
		 * 2、固定流程
		 * 3、启动流程后，将流程走向和审批人信息，保存到当前单据中
		 */
		$process_relation_formDao = M("process_relation_form");
		//删除原有的变更审批节点信息
		$where = array();
		$where['tablename'] = $modelname;
		$where['tableid'] = $masid;
		$where['catgory'] = 1;
		$delbool = $process_relation_formDao->where($where)->delete();
		if($delbool == false){
			if(!$isrelation){
				$this->error("原流程节点删除失败，请联系管理员");
			}else{
				$msg = "原流程节点删除失败，请联系管理员".$process_relation_formDao->getDBerror();
				throw new NullDataExcetion($msg);
			}
		}
		//获取变更流程ID
		$changeid = $_POST['changeid'];
		//查询固定流程是否存在
		$ProcessInfoModel = D ( "ProcessInfo" );
		//第一步，根据modelname获取一个默认流程
		$map = array ();
		$map ['id'] = $changeid;
		$pcarr = $ProcessInfoModel->where ( $map )->find();
		if($pcarr){
			/*
			 * 参数1、当前选中的流程，
			 * 参数2、当前表单POST的数据，进行批次的匹配  获取流程审核人信息
			 * 参数3、 返回当前流程每个节点的审核人
			 * 参数4、验证流程是否为空流程
			 */
			$auditList = $ProcessInfoModel->getFlowAuitUser ( $pcarr, $vo );
			$auditList = array_merge($auditList);
			
			if ($auditList) {
				//先查询目前流程最大顺序号
				$where = array();
				$where['tablename'] = $modelname;
				$where['tableid'] = $masid;
				$sort = $process_relation_formDao->where($where)->order("sort desc")->getField("sort");
				
				foreach ( $auditList as $aukey => $auval ) {
					$sort++;
					if($auval['flowtype']==2 && $auval['doing'] == 1){
						//非子流程节点
						if($auval['curAuditUser']==''){
						    if(true == $isrelation){
                                $msg = $auval['name'] . " 未找到相关审核人员，请检查！";
                                throw NEW NullDataExcetion($msg);
                            }else{
                                $this->error ( $auval['name'] . " 未找到相关审核人员，请检查！" );
                            }
						}
					}
					//组合数据
					$auval['sort'] = $sort; //当前模型
					$auval['catgory'] = 1; //变更节点
					$auval['tablename'] = $modelname; //当前模型
					$auval['tableid'] = $masid;  //当前模型ID
					$auval['infoid'] = $vo['ptmptid'];
					$auval['typeid'] = $pcarr['typeid']; //流程分类
					$auval['createtime'] = time();
					$auval['createid'] = $_SESSION[C('USER_AUTH_KEY')];
					$auval['projectid'] = $vo['projectid'];
					$relaform = $process_relation_formDao->add($auval);
					$auditList[$aukey]['id'] = $relaform;
					$auditList[$aukey]['typeid'] = $pcarr['typeid'];
					if(!$relaform){
						$this->error("审核节点信息表添加失败");
					}else{
						//串并行混搭
						if($auval['parallel'] == 2){
							if(count($auval['relation_parallel'])==0){
								if(!$isrelation){
									$this->error ( $auval['name'] . " 未找到相关批次，请检查！" );
								}else{
									$msg = $auval['name'] . " 未找到相关批次，请检查！";
									throw new NullDataExcetion($msg);
								}
								exit;
							}
							//定义存储同级且定义成为父类
							$sort = array();
							$process_relation_parallelDao = M("process_relation_parallel");
							foreach($auval['relation_parallel'] as $pkey=>$pval ){
								//数组公共部分
								$data = array();
								$data['tablename'] = $modelname;
								$data['tableid'] = $masid;
								$data['bactch'] = $pval['bactch'];
								$data['bactchname'] = $pval['bactchname'];
								$data['curAuditUser'] = $pval['curAuditUser'];
								$data['relation_formid'] = $relaform;
								//$data['relation_formid'] = $auval['relationid'];
								$data['sort'] = $pval['sort'];
								$data['createid'] = $_SESSION[C('USER_AUTH_KEY')];
								$data['createtime'] = time();
								if(!in_array($pval['sort'], array_keys($sort))){
									$data['parentid'] = 0;
									$relation_parentid = $process_relation_parallelDao->add($data);
									//赋值上级ID
									$sort[$pval['sort']] = $relation_parentid;
								}else{
									$data['parentid'] = $sort[$pval['sort']];
									$relation_parentid = $process_relation_parallelDao->add($data);
									//相同顺序，赋值最新的一个上级ID
									$sort[$pval['sort']] = $relation_parentid;
								}
							}
						}
					}
				}
				$_POST ['ischageoperateid'] = 1;// 变更标记
				$_POST ['auditState'] = 4;//变更状态
				$_POST ['operateid'] = 0;//变更，变更未终审标志
			}else{
				//空流程
				$_POST ['ischageoperateid'] = 1;// 变更标记
				$_POST ['auditState'] = 3;
				$_POST ['operateid'] = 1;//变更，变更终审标志
			}
		}else{
			//变更流程不存在。也直接审核完成
			$_POST ['ischageoperateid'] = 1; // 变更标记
			$_POST ['auditState'] = 3;
			$_POST ['operateid'] = 1;//变更，变更终审标志
		}
		
		if (method_exists ( $this, "_before_update" )) {
			// 流程最后一步时入库或其他操作
			call_user_func ( array ( &$this, "_before_update") );
		}
		$_POST ['startprocess'] = 1;
		//修改当前单据数据
		$this->update ();
		
		//单据新建成功，为当前单据存储历史记录信息
		$ProcessInfoHistoryModel = D ( "ProcessInfoHistory" ); 
		//封装审核意见参数
		$_REQUEST['doinfo'] = "流程变更提交，变更描述：".$_POST['bgms'];//
		$_REQUEST['dotype'] = 8;// "流程变更";
		$result = $ProcessInfoHistoryModel->addProcessInfoHistory($modelname,$masid);
		if(!$result){
			if(true == $isrelation){
				$msg = "单据历史记录保存失败，请联系管理员";
				throw NEW NullDataExcetion($msg);
			}else{
				$this->error("单据历史记录保存失败，请联系管理员");
			}
		}
		if($_POST ['auditState'] == 4){
			//插入工作审批数据
			$MisWorkMonitoringModel = D("MisWorkMonitoring");
			$result = $MisWorkMonitoringModel->addWorkMonitoring($modelname,$masid,$auditList);
			if(!$result){
				if(true == $isrelation){
					$msg = "单据工作审批池数据推送失败，请联系管理员";
					throw NEW NullDataExcetion($msg);
				}else{
					$this->error("单据工作审批池数据推送失败，请联系管理员");
				}
			}
		}
		if($_POST ['auditState'] == 3){
			//变更带审批流的。启动完成，判断是否存在overProcess方法。
			if (method_exists ( $this, "overProcess" )) {
				$this->overProcess ( $masid );
			}
		}
		//$this->lookupdataInteractionPrepareData($masid);
		if(true == $isrelation){
			return true;
		}else{
			$this->success("单据启动成功");
		}
	}

	/**
	 * @Title: startprocess
	 * @Description: 表单流程启动
	 * @author liminggang
	 * @param boolean $isrelation	是否返回状态
	 * @date 2014-9-12 下午3:33:47
	 * @throws
	 */
	function startprocess($isrelation=false) {
		//获取当前模型
		$modelname = $this->getActionName ();
		$model = D ( $modelname );
		//第一步、验证是否是新建单据启动流程
		if ($_POST ['beforeInsert']) {
			/*
			 * 1、表示新建单据时，直接启动流程审批
			 * 2、新建单据启动流程，需要直接调用控制器中的insert方法保存当前单据数据，并且不能有成功信息输出(因为后面程序还需要继续执行)。
			 * 3、$_POST ['startprocess'] = 1 为封装参数，在insert方法中获取，控制success信息是否输出,切记（此参数不能在任何模板中使用）。
			 * 4、如果在insert方法中，获取到$_POST ['startprocess'] = 1参数。那么必须在insert方法中为当前数据post中封装当前单据ID值
			 */
			$_POST ['startprocess'] = 1;
			$module2 = A ( $modelname );
			if (method_exists ( $module2, "_before_insert" )) {
				// 流程最后一步时入库或其他操作
				call_user_func ( array ( &$module2, "_before_insert" ) );
			}
			$module2->insert ();
		}
		//第二步、接收当前单据保存的ID
		$masid = $_POST['id'];
		$process_relation_formDao = M("process_relation_form");
		//删除原有的审批节点信息
		$where = array();
		$where['tablename'] = $modelname;
		$where['tableid'] = $masid;
		$delbool = $process_relation_formDao->where($where)->delete();
		if($delbool == false){
			if(!$isrelation){
				$this->error("原流程节点删除失败，请联系管理员");
			}else{
				$msg = "原流程节点删除失败，请联系管理员".$process_relation_formDao->getDBerror();
				throw new NullDataExcetion($msg);
			}
		}
		//验证$masid是否有值(主要是为了判断单据新建时启动流程)
		if(!$masid){
			if(!$isrelation){
				$this->error("当前单据保存失败，请联系管理员");
			}else{
				$msg = "当前单据保存失败，请联系管理员";
				throw new NullDataExcetion($msg);
			}
		}
		//单据新建成功，为当前单据存储历史记录信息
		$ProcessInfoHistoryModel = D ( "ProcessInfoHistory" );
		//获取当前数据信息
		$vo = $model->find($masid);
		//当子流程启动后，则关闭子流程生单的审核提示，子流程数组反写主流程绑定ID
		$map = array();
		$map['zitablename']=$modelname;
		$map['zitableid']=$masid;//原来是POST传值，现在改为参数传值
		$obj=M('process_relation_children')->where($map)->find();
		if($obj){
			//流程监控表,当子流程启动后，修改转子流程人当前审核节点不可见
			$mis_work_monitoringDao = M("mis_work_monitoring");
			$map = array();
			$map['tablename'] = $obj['tablename'];
			$map['tableid'] = $obj['tableid'];
			$map['ostatus'] = $obj['relation_formid'];  //实例化后的审批节点表
			$map['_string'] = "FIND_IN_SET( {$vo['createid']},curAuditUser )";
			$mis_work_monitoringDao->where($map)->setField("isauditstatus",0);
			//封装审核意见参数
			$_REQUEST['doinfo'] = "子流程单据已经发起，待领导审批。";// "流程启动";
			$_REQUEST['dotype'] = 9;// "子流程启动";
			$result = $ProcessInfoHistoryModel->addProcessInfoHistory($obj['tablename'],$obj['tableid'],$obj['relation_formid']);
		}
		//封装审核意见参数
		$_REQUEST['doinfo'] = "流程新建";// "流程新建";
		$_REQUEST['dotype'] = 1;// "流程新建";
		$result = $ProcessInfoHistoryModel->addProcessInfoHistory($modelname,$masid);
		if(false === $result){
			if(!$isrelation){
				$this->error("单据历史记录保存失败，请联系管理员");
			}else{
				$msg = "单据历史记录保存失败，请联系管理员".$ProcessInfoHistoryModel->getDBerror();
				throw new NullDataExcetion($msg);
			}
		}
		/*
		 * 第三步、判断是何种流程，
		 * 1、自定义流程   $flowid = $_POST ['flowid']; 如果获取到此参数。表示自定义流程
		 * 2、固定流程
		 * 3、启动流程后，将流程走向和审批人信息，保存到当前单据中
		 */
		$flowid = $_POST ['flowid'];
		if($flowid){
			//实例化自定义流程模型
			$MisOaFlowsModel = D ( "MisOaFlows" );
			// 查询自定义流程数据，和审核人走向
			$flowlist = $MisOaFlowsModel->where ( "id = " . $flowid )->find ();  
			$flowtrack = unserialize ( $flowlist ['flowtrack'] );
			// 存储审核人ID
			$a = array ();
			foreach ( $flowtrack as $key => $val ) {
				if ($val ['level'] > 1) {
					if (in_array ( $val ['level'], array_keys ( $a ) )) {
						$a [$val ['level']] = $a [$val ['level']] . "," . $val ['id'];
					} else {
						$a [$val ['level']] = $val ['id'];
					}
				}
			}
			// 当前流程状态，1表示启动。
			$_POST ['auditState'] = 1;
		}else{
			//查询固定流程是否存在
			$ProcessInfoModel = D ( "ProcessInfo" );
			/*
			 * $allnode当前流程所有审批节点
			 * $audituser当前流程节点所有审核人
			 */
			//验证流程是否存在
			$pcarr = $ProcessInfoModel->getProcessInfo($modelname);
			if($pcarr){
				/*
				 * 插入新的流程表单表
				 */
				$process_info_formDao = M("process_info_form");
				//串并行混搭表
				$process_relation_parallelDao = M("process_relation_parallel");
				$data = array();
				$data = $pcarr;
				$data['tablename'] = $modelname;
				$data['tableid'] = $masid;
				unset($data['id']);
				$info_formid = $process_info_formDao->add($data);
				if(false === $info_formid){
					if(!$isrelation){
						$this->error("审核信息表添加失败");
					}else{
						$msg = "审核信息表添加失败".$process_info_formDao->getDBError();
						throw new NullDataExcetion($msg);
					}
				}
				/*
				 * 参数1、当前选中的流程，
				 * 参数2、当前表单POST的数据，进行批次的匹配  获取流程审核人信息
				 * 参数3、 返回当前流程每个节点的审核人
				 * 参数4、验证流程是否为空流程
				 */
				$auditList = $ProcessInfoModel->getFlowAuitUser ( $pcarr, $vo );
				$auditList = array_merge($auditList);
				if ($auditList) {
					//实例化流程审核节点信息
					foreach ( $auditList as $aukey => $auval ) {
						if($auval['flowtype']==2&&$auval['doing'] == 1){
							//非子流程节点
							if($auval['curAuditUser']==''){
								if(!$isrelation){
									$this->error ( $auval['name'] . " 未找到相关审核人员，请检查！" );
								}else{
									$msg =  $auval['name'] . " 未找到相关审核人员，请检查！";
									throw new NullDataExcetion($msg);
								}
							}
						}
						//组合数据
						$auval['tablename'] = $modelname; //当前模型
						$auval['tableid'] = $masid;  //当前模型ID
						$auval['infoid'] = $info_formid;
						$auval['title'] = $pcarr['title'];//加入标题大概
						$auval['typeid'] = $pcarr['typeid'];
						$auval['createtime'] = time();
						$auval['createid'] = $_SESSION[C('USER_AUTH_KEY')];
						$auval['projectid'] = $vo['projectid']; //存储项目ID
						$relaform = $process_relation_formDao->add($auval);
						//加入实例化后的流程节点ID
						$auditList[$aukey]['id'] = $relaform;
						$auditList[$aukey]['typeid'] = $pcarr['typeid'];
						$auditList[$aukey]['title'] = $pcarr['title'];//加入标题大概
						if(false === $relaform){
							if(!$isrelation){
								$this->error("审核节点信息表添加失败");
							}else{
								$msg = "审核节点信息表添加失败".$process_relation_formDao->getDBerror();
								throw new NullDataExcetion($msg);
							}
						}else{
							//串并行混搭
							if($auval['parallel'] == 2){
								if(count($auval['relation_parallel'])==0 && $auval['doing'] == 1){
									if(!$isrelation){
										$this->error ( $auval['name'] . " 未找到相关批次，请检查！" );
									}else{
										$msg = $auval['name'] . " 未找到相关批次，请检查！";
										throw new NullDataExcetion($msg);
									}
									exit;
								}
								//定义存储同级且定义成为父类
								$sort = array();
								foreach($auval['relation_parallel'] as $pkey=>$pval ){
									if(!$pval['curAuditUser']){
										if(!$isrelation){
											$this->error ( $pval['name'] . " 未找到相关审核人员，请检查！" );
										}else{
											$msg =  $pval['name'] . " 未找到相关审核人员，请检查！";
											throw new NullDataExcetion($msg);
										}
									}
									//数组公共部分
									$data = array();
									$data['tablename'] = $modelname;
									$data['tableid'] = $masid;
									$data['bactch'] = $pval['bactch'];
									$data['bactchname'] = $pval['bactchname'];
									$data['curAuditUser'] = $pval['curAuditUser'];
									$data['relation_formid'] = $relaform;//实例化后的审批节点表
									$data['sort'] = $pval['sort'];
									$data['createid'] = $_SESSION[C('USER_AUTH_KEY')];
									$data['createtime'] = time();
									if(!in_array($pval['sort'], array_keys($sort))){
										$data['parentid'] = 0;
										$relation_parentid = $process_relation_parallelDao->add($data);
										//赋值上级ID
										$sort[$pval['sort']] = $relation_parentid;
									}else{
										$data['parentid'] = $sort[$pval['sort']];
										$relation_parentid = $process_relation_parallelDao->add($data);
										//相同顺序，赋值最新的一个上级ID
										$sort[$pval['sort']] = $relation_parentid;
									}
								}
							}
						}
					}
					//当前固定流程ID
					$_POST ['ptmptid'] = $info_formid;
					// 当前流程状态，1表示启动。
					$_POST ['auditState'] = 1;
				}else{
					//空流程
					$_POST ['ptmptid'] = $info_formid;
					$_POST ['auditState'] = 3;  //流程状态
					$_POST ['operateid'] = 1;  //单据状态审批完成单据状态为终审
					$_POST ['alreadyauditnode'] = time();  //单据状态审批完成单据终审时间
				}
			}else{
				//空流程
				$_POST ['ptmptid'] =0;
				$_POST ['auditState'] = 3;  //流程状态
				$_POST ['operateid'] = 1;  //单据状态审批完成单据状态为终审
				$_POST ['alreadyauditnode'] = time();  //单据状态审批完成单据终审时间
			}
		}
		
		if (method_exists ( $this, "_before_update" )) {
			// 流程最后一步时入库或其他操作
			call_user_func ( array ( &$this, "_before_update") );
		}
		$_POST ['startprocess'] = 1;
		//修改当前单据数据
		$this->update ();
		//封装审核意见参数
		$_REQUEST['doinfo'] = $_POST['doinfo']?$_POST['doinfo']:"流程启动";// "流程启动";
		$_REQUEST['dotype'] = 2;// "流程启动";
		$result = $ProcessInfoHistoryModel->addProcessInfoHistory($modelname,$masid);
		if(false === $result){
			if(!$isrelation){
				$this->error("单据历史记录保存失败，请联系管理员");
			}else{
				$msg = "单据历史记录保存失败，请联系管理员".$ProcessInfoHistoryModel->getDBerror();
				throw new NullDataExcetion($msg);
			}
		}else{
			if($_POST ['auditState'] == 3){//流程结束
				//封装审核意见参数
				$_REQUEST['doinfo'] = "流程结束";// "流程启动";
				$_REQUEST['dotype'] = 4;// "流程启动";
				$result = $ProcessInfoHistoryModel->addProcessInfoHistory($modelname,$masid);
				if(false === $result){
					if(!$isrelation){
						$this->error("单据历史记录保存失败，请联系管理员");
					}else{
						$msg = "单据历史记录保存失败，请联系管理员".$ProcessInfoHistoryModel->getDBerror();
						throw new NullDataExcetion($msg);
					}
				}
				if (method_exists ( $this, "overProcess" )) {
					$this->overProcess ( $masid );
				}
				//$this->lookupdataInteractionPrepareData($data['tableid']);
				//子流程操作
				$this->_before_overProcess ( $masid );
			}
		}
		if($_POST ['auditState'] == 1){
			//插入工作审批数据
			$MisWorkMonitoringModel = D("MisWorkMonitoring");
			$result = $MisWorkMonitoringModel->addWorkMonitoring($modelname,$masid,$auditList);
			if(false === $result){
				if(!$isrelation){
					$this->error("单据工作审批池数据推送失败，请联系管理员");
				}else{
					$msg = "单据工作审批池数据推送失败，请联系管理员".$MisWorkMonitoringModel->getDBerror();
					throw new NullDataExcetion($msg);
				}
			}
		}
		if(!$isrelation){
			$this->success("单据启动成功");
		}else{
			return true;
		}
	}
	/**
	 * @Title: backprocess
	 * @Description: todo(单据流程打回)   
	 * @author 黎明刚
	 * @date 2014年12月2日 上午11:54:58 
	 * @throws
	 */
	public function backprocess($step=0) {
		/*
		 * 流程打回问题，经过讨论。一致认为需要先修改原表单数据，从原表单数据中获取其他信息。
		 * 第一步、修改原表单数据。
		 * 第二步、记录打回表单的操作历史记录
		 * 第三步、操作审核数据池信息
		 * 第四步、发送打回信息邮件通知
		 */
		//获取当前控制器名称
		$modelname = $this->getActionName ();
		$model2=D($modelname);
		//获取当前表单ID
		$masid = $_POST ['id'];
		$vo = $model2->find ( $masid );
		//获取当前登陆人
		$userid = $_SESSION[C('USER_AUTH_KEY')];
		//获取流程审批节点信息
		$mis_work_monitoringDao = M("mis_work_monitoring");
		$where = array();
		$where['tablename'] = $modelname;
		$where['tableid'] = $masid;
		$where['dostatus'] = 0;//未处理
		$where ['_string'] = 'FIND_IN_SET(  ' . $userid . ',curAuditUser )';
		$myworklist = $mis_work_monitoringDao->where($where)->select();
		//获取流程审批节点信息
		$process_relation_formDao = M("process_relation_form");
		$where = array();
		$where['tablename'] = $modelname;
		$where['tableid'] = $masid;
		$where['auditState'] = 0;//未处理
		$where['doing'] = 1;//进行中的节点
		$where['flowtype'] = array("gt",1);//审批节点或者转子流程节点
		$newinfo = $process_relation_formDao->where($where)->order('sort asc')->select();
		$infolist = array_merge($newinfo);
		/*
		 * 验证串行同时审核问题。这里要排除子流程情况，子流程时$infolist[0]['curAuditUser']为null
		*/
		if(count($myworklist)==0 && $infolist[0]['flowtype']!=3 && $step!=1){
			$this->error("单据已被其他同事操作，无需再审！");
			exit;
		}
		$infovo = $infolist[0];
		//获取当前审批的节点
		$current_audit_node_id = $infovo['id'];
		//实例化表单信息
		if($infovo['catgory'] == 1){
			$ifbool = true; //标记是非存在退回到某级
			$newinfo = array();
			//判断是否设置了退回节点
			if($infovo['istuihui']){
				//此查询主要是过滤掉审批追加的的审批节点
				$where = array();
				$where['tablename'] = $modelname;
				$where['tableid'] = $masid;
				$where['catgory'] = 1; //变更节点
				$where['doing'] = 1;//执行的节点
				$where['flowid'] = $infovo['istuihui'];
				$onevo = $process_relation_formDao->where($where)->field("sort")->find();
				//实例化并串流程模型
				$process_relation_parallelDao = M("process_relation_parallel");
				//实例化子流程流程模型
				$process_relation_childrenDao = M("process_relation_children");
				$where = array();
				$where['tablename'] = $modelname;
				$where['tableid'] = $masid;
				$where['catgory'] = 1; //变更节点
				$where['doing'] = 1;//执行的节点
				$where['sort'] = array('egt',$onevo['sort']);
				$where['flowid'] = array('egt',$infovo['istuihui']);	
				$newinfo = $process_relation_formDao->where($where)->order('sort asc')->select();
				$bool = true;
				if($newinfo){
					$ifbool = false;
					foreach($newinfo as $key=>$val){
						if($infovo['istuihui'] == $val['flowid'] && $bool){
							$info = $val['name'];
							$bool = false;
						}
						if($val['auditState'] == 1){
							$newinfo[$key]['auditState'] = 0;
							$newinfo[$key]['curAuditUser'] = $val['auditUser'];
							$newinfo[$key]['alreadyAuditUser'] = "";
							//修改流程数据
							$boolean = $process_relation_formDao->save($newinfo[$key]);
							if($boolean === false){
								$this->error("流程回退数据写入失败，请联系管理员");
							}
						}
						//判断是否存在并串混带审批节点
						$map = array();
						$map['relation_formid'] = $val['id'];
						$map['auditState'] = array('gt',0);
						$paralllist = $process_relation_parallelDao->where($map)->getField("id",true);
						if($paralllist){
							$process_relation_parallelDao->where($map)->setField("auditState",0);
						}
						//判断是否存在子流程
						$map = array();
						$map['relation_formid'] = $val['id'];
						$childrenbool = $process_relation_childrenDao->where($map)->delete();
						if($childrenbool == false){
							$this->error("子流程数据退回失败，请联系管理员");
						}
					}
					$_POST ['id']= $masid;
					$_REQUEST['doinfo'] = $_POST['doinfo']."  [{$infovo['name']}] 退回到  [{$info}]";
				}
			}
			if($ifbool){
				//变更流程节点打回， 只打回到原单据已审核完成部分。
				unset($_POST);
				$_POST ['auditState'] = 3; 	//打回到原单据已审核完成状态，杨希处理，还原过去的数据
				$_POST ['id']= $masid;
				/**
				 * 此处还原已被修改了的单据内容
				 */
				//先查询是否存在数据恢复
				$where = array();
				$where['tablename'] = $modelname;
				$where['tableid'] = $masid;
				$where['category'] = 1;  //变更流程
				//反向排序，取最新的一条记录
				$sqlresume = M('mis_system_data_resuming')->where($where)->order('id desc')->getField("sqlresume");
				$sqlArr    = unserialize(base64_decode($sqlresume));
				//如果存在，执行恢复
				if($sqlArr){
					foreach($sqlArr as $sqlkey => $sqlval){
						M('mis_system_data_resuming')->execute($sqlval);
					}
				}
			}
			$result=true;
		}else{
			$newinfo = array();
			//判断是否设置了退回节点
			if($infovo['istuihui']){
				//此查询主要是过滤掉审批追加的的审批节点
				$where = array();
				$where['tablename'] = $modelname;
				$where['tableid'] = $masid;
				$where['catgory'] = 0; //变更节点
				$where['doing'] = 1;//执行的节点
				$where['flowid'] = $infovo['istuihui'];
				$onevo = $process_relation_formDao->where($where)->field("sort")->find();
				//实例化并串流程模型
				$process_relation_parallelDao = M("process_relation_parallel");
				//实例化子流程流程模型
				$process_relation_childrenDao = M("process_relation_children");
				$where = array();
				$where['tablename'] = $modelname;
				$where['tableid'] = $masid;
				$where['catgory'] = 0; //普通流程
				$where['doing'] = 1;//执行的节点
				$where['sort'] = array('egt',$onevo['sort']);
				$where['flowid'] = array('egt',$infovo['istuihui']);
				$newinfo = $process_relation_formDao->where($where)->order('sort asc')->select();
				$bool = true;
				if($newinfo){
					foreach($newinfo as $key=>$val){
						if($infovo['istuihui'] == $val['flowid'] && $bool){
							$info = $val['name'];
							$bool = false;
						}
						if($val['auditState'] == 1){
							$newinfo[$key]['auditState'] = 0;
							$newinfo[$key]['curAuditUser'] = $val['auditUser'];
							$newinfo[$key]['alreadyAuditUser'] = "";
							//修改流程数据
							$boolean = $process_relation_formDao->save($newinfo[$key]);
							if($boolean === false){
								$this->error("流程回退数据写入失败，请联系管理员");
							}
						}
						//判断是否存在并串混带审批节点
						$map = array();
						$map['relation_formid'] = $val['id'];
						$map['auditState'] = array('gt',0);
						$paralllist = $process_relation_parallelDao->where($map)->getField("id",true);
						if($paralllist){
							$process_relation_parallelDao->where($map)->setField("auditState",0);
						}
						//判断是否存在子流程
						$map = array();
						$map['relation_formid'] = $val['id'];
						//$map['auditState'] = array('gt',0);
						$childrenbool = $process_relation_childrenDao->where($map)->delete();
						if($childrenbool == false){
							$this->error("子流程数据退回失败，请联系管理员");
						}
					}
					$_POST['auditState'] = 2; 	//打回字段状态
					$_REQUEST['doinfo'] = $_POST['doinfo']."  [{$infovo['name']}] 退回到  [{$info}]";
				}else{
					$_POST ['auditState'] = - 1; 	//打回字段状态
					$_POST ['operateid'] = 0; 	//单据未终审。
				}
			}else{
				$_POST ['auditState'] = - 1; 	//打回字段状态
				$_POST ['operateid'] = 0; 	//单据未终审。
			}
			if (false === $model2->create ()) {
				$this->error ( $model2->getError () );
			}
			$result = $model2->save();
		}
		if($result===false){
			$this->error("当前单据打回失败，请联系管理员");
		}else{
			//单据打回成功，为当前单据存储历史记录信息
			$ProcessInfoHistoryModel = D ( "ProcessInfoHistory" );
			//封装审核意见参数
			$_REQUEST['dotype'] = 6;// "流程打回";
			$result = $ProcessInfoHistoryModel->addProcessInfoHistory($modelname,$masid,$current_audit_node_id);
			if(!$result){
				$this->error("单据历史记录保存失败，请联系管理员");
			}
			//插入工作审批数据
			$MisWorkMonitoringModel = D("MisWorkMonitoring");
			$result = $MisWorkMonitoringModel->addWorkMonitoring($modelname,$masid,$newinfo,false,false);
			if(!$result){
				$this->error("单据工作审批池数据推送失败，请联系管理员");
			}
			// 审核了就发系统邮件
			$this->auditremindmessage ( $vo ["createid"], $vo ['orderno'], $modelname, 2, $masid );
			//审核了就发系统邮件给知会人
			$this->workNoticeMessages($infolist[0]['informpersonid'],false,$modelname,$masid);
			//保存到知会表
			$this->lookupWorkMisNotify($infolist[0]['informpersonid'],$modelname,$masid,2);
			if (method_exists ( $this, "overBackProcess" )) {
				$this->overBackProcess ( $masid );
			}
			if($step !=1){
				$this->success("当前单据打回成功。");
			}
		}
	}
	/**
	 * @Title: lookupGetBackprocess
	 * @Description: todo(单据撤回)
	 * @author 黎明刚
	 * @date 2014年12月2日 上午11:55:27
	 * @throws
	 */
	public function lookupGetBackprocess(){
		/*
		 * 流程退回，包括退回到起点 ，退回到当前审核的上一级节点
		* 1、查询流程信息数据
		* 2、判断上一级审核节点类型 1并行 2并串混搭，0 串行
		*/
		//获取当前控制器名称
		$tablename = $this->getActionName ();
		//获取当前表单ID
		$tableid = $_REQUEST ['id'];
		//查询当前表单数据
		$model2 = D ( $tablename );
		$vo = $model2->find ( $tableid );
		//获取当前撤销人
		$userid = $_SESSION[C('USER_AUTH_KEY')];
		//查询流程走向
		$relmap = array();
		$relmap['tablename'] = $tablename;
		$relmap['tableid'] = $tableid;
		$relmap['doing'] = 1;
		$relmap['flowtype'] = array("gt",1);//审批节点或者转子流程节点
		//实例化表单流程表
		$process_relation_formDao = M("process_relation_form");
		$newinfo = $process_relation_formDao->where($relmap)->order('sort asc')->select();
		$newinfo = array_merge($newinfo);
		//实例化历史记录表
		$ProcessInfoHistoryModel = D ( "ProcessInfoHistory" );
		//存储已经审核过的流程节点数据
		$alreadyAuditNode = array();
		//存储最后一个撤回节点的下级
		$num = 0;
		//循环遍历审批节点数据
		foreach($newinfo as $key=>$val){
			if($val['auditState'] == 0){
				//表示是当前审核节点, 那么判断是否存在已审核了
				$alreadyAuditUser = array_filter(explode(",", $val['alreadyAuditUser']));
				//存在审核人(并行，并串混搭，子流程)
				if($alreadyAuditUser || ($val['isaudittableid'] && $val['flowtype']==3)){
					$num++;
					$alreadyAuditNode[] = $val;
				}
				break;
			}
			$num++;
			$alreadyAuditNode[] = $val;
		}
		//进行已审核和正在审核的节点进行解析
		if($alreadyAuditNode){
			//寻找最新的一个数据
			$c = count($alreadyAuditNode)-1;
			//取出最新一个审批节点
			$oldalreadyAuditNode = $alreadyAuditNode[$c];
			//获取已审核人员
			$oldalreadyAuditUser =explode(",", $oldalreadyAuditNode['alreadyAuditUser']);
			//判断当前撤回人是否在已审核人员中
			if(!in_array($userid, $oldalreadyAuditUser)){
				//撤回人，不是上级节点审核人，则不能撤回
				$this->error ( "上级领导已审批，不能对单据进行撤回！" );
			}
			//删除非当前审核点的审核信息
			$MisWorkMonitoringModel = D("MisWorkMonitoring");
			
			//对最新的审批节点进行解析 ，判断是那种审批节点 0串行，1并行，2并串混搭
			if($oldalreadyAuditNode['parallel'] == 2){
				//实例化批次表
				$process_relation_parallel = M("process_relation_parallel");
				//查询批次当前审核人信息
				//查询并串混搭走向 。可以撤回的节点
				$where = array();
				$where['tablename'] = $tablename;
				$where['tableid'] = $tableid;
				$where['relation_formid'] = $oldalreadyAuditNode['id'];
				$where['auditState'] = 2;//已审核完成的节点
				$where['parentid'] = 0;//先找顶级
				$parallist =$process_relation_parallel->where($where)->order("sort asc")->select();
				if($parallist){
					$bool = false;//定义判定变量
					foreach($parallist as $ke=>$va){
						$data = $this->digui($va);
						//进行解析串行混搭的批次
						if(in_array($userid, explode(",", $data['curAuditUser']))){
							$bool = true;
							//此处进行数据回撤。
							break;
						}
					}
					if($bool && $data){
						//1、先删除下级节点推送信息(并行的时候。还暂无下级推送信息，则删除为0)
						//查询下一个批次推送信息
						$nextdata = $process_relation_parallel->where('parentid = '.$data['id'])->find();
						if($nextdata){
							//删除已推送审核信息数据
							$map = array ();
							$map ['tablename'] = $tablename;
							$map ['tableid'] = $tableid;
							$map ['bactchid'] = $nextdata['id'];
							$map ['dostatus'] = 0;
							$result = $MisWorkMonitoringModel->where ( $map )->delete ();
							if($result == false){
								$this->error ( "清除下级审核信息失败，请联系管理员" );
							}
							//撤回已经发送的审批节点信息
							$map = array ();
							$map ['tablename'] = $tablename;
							$map ['tableid'] = $tableid;
							$map ['relation_formid'] = $oldalreadyAuditNode['id'];
							$map ['parentid'] = $data['id'];
							$ppresult = $process_relation_parallel->where($map)->setField("auditState",0);
							if($ppresult == false){
								$this->error ( "修改下级撤回状态失败，请联系管理员！" );
							}
						}
						//2、清除当前节点已审核信息
						$map = array ();
						$map ['tablename'] = $tablename;
						$map ['tableid'] = $tableid;
						$map ['ostatus'] =  $oldalreadyAuditNode['id'];
						$map ['dostatus'] = 1;
						$map ['bactchid'] = $data['id'];
						$result = $MisWorkMonitoringModel->where ( $map )->delete ();
						if($result == false){
							$this->error ( "清除当前审核信息失败，请联系管理员" );
						}
						//3、修改当前节点审批状态
						$map = array();
						$map['id'] = $data['id'];
						$map['auditState'] = 0;
						$map['curAuditUser'] = $userid;
						$plre = $process_relation_parallel->save($map);
						if($plre == false){
							$this->error ( "修改当前节点审批状态失败，请联系管理员" );
						}
						//4、将流程审核节点推送到当前节点
						$oldalreadyAuditNode['curAuditUser'] = $oldalreadyAuditNode['alreadyAuditUser'];
						$oldalreadyAuditNode['alreadyAuditUser'] = "";
						$result = $MisWorkMonitoringModel->addWorkMonitoring($tablename,$tableid,array($oldalreadyAuditNode));
						if(!$result){
							$this->error("单据工作审批池数据推送失败，请联系管理员");
						}
						
						//5、封装审核意见参数
						$_REQUEST['dotype'] = 3;// "流程审核";
						$_REQUEST['doinfo'] = "【撤回审核过的节点，进行数据修改】  ";  //处理意见
						$result = $ProcessInfoHistoryModel->addProcessInfoHistory($tablename,$tableid,$oldalreadyAuditNode['id']);
						if($result == false){
							$this->error("单据历史记录保存失败，请联系管理员");
						}
						//6、修改当前审批节点为未完成
						$data = array();
						$data['id'] = $oldalreadyAuditNode['id'];
						$data['curAuditUser'] = $oldalreadyAuditNode['curAuditUser'];
						$data['auditState'] = 0;
						$data['isaudittableid'] = 0; //子流程问题
						$fresut = $process_relation_formDao->save($data);
						if($fresut == false){
							$this->error("单据流程数据修改失败，请联系管理员");
						}
						//判断撤回当前审核节点是否为并串混搭
						$nextinfo = $newinfo[$num];
						if($nextinfo['parallel'] == 2 && $oldalreadyAuditNode['auditState'] == 1){
							//代表单据撤回。将单据状态改变
							$map = array ();
							$map ['tablename'] = $tablename;
							$map ['tableid'] = $tableid;
							$map ['ostatus'] =  $nextinfo['id'];
							$map ['dostatus'] = 0;
							$result = $MisWorkMonitoringModel->where ( $map )->delete ();
							if($result == false){
								$this->error ( "清除下级审核信息失败，请联系管理员" );
							}
							//表示下级为混搭，而且已经推送了。
							$map = array ();
							$map ['tablename'] = $tablename;
							$map ['tableid'] = $tableid;
							$map ['relation_formid'] =  $nextinfo['id'];
							$ppresult = $process_relation_parallel->where($map)->setField("auditState",0);
							if($ppresult == false){
								$this->error ( "修改下级撤回状态失败，请联系管理员！" );
							}
						}
					}else{
						//撤回人，不是上级节点审核人，则不能撤回
						$this->error ( "上级领导已审批，不能对单据进行撤回！" );
					}
				}
			}else{
				//--------------并行和串行 合并处理-------------------------------------
				//1、先删除下级节点推送信息(并行的时候。还暂无下级推送信息，则删除为0)
				$map = array ();
				$map ['tablename'] = $tablename;
				$map ['tableid'] = $tableid;
				$map ['ostatus'] = array('neq',$oldalreadyAuditNode['id']);
				$map ['dostatus'] = 0;
				$result = $MisWorkMonitoringModel->where ( $map )->delete ();
				if($result == false){
					$this->error ( "清除下级审核信息失败，请联系管理员" );
				}
				//2、清除当前节点已审核信息
				$map = array ();
				$map ['tablename'] = $tablename;
				$map ['tableid'] = $tableid;
				$map ['ostatus'] =  $oldalreadyAuditNode['id'];
				$map ['dostatus'] = 1;
				$map ['_string'] = 'FIND_IN_SET(  ' . $userid . ',userid )';
				$result = $MisWorkMonitoringModel->where ( $map )->delete ();
				if($result == false){
					$this->error ( "清除下级审核信息失败，请联系管理员" );
				}
				//3、将流程审核节点推送到当前节点
				$oldcurAuditUser = $oldalreadyAuditNode['curAuditUser'];
				$oldalreadyAuditNode['curAuditUser'] = $userid;
				$oldalreadyAuditNode['alreadyAuditUser'] = "";
				$result = $MisWorkMonitoringModel->addWorkMonitoring($tablename,$tableid,array($oldalreadyAuditNode));
				if(!$result){
					$this->error("单据工作审批池数据推送失败，请联系管理员");
				}
				//4、修改当前节点审批状态
				$data = array();
				$data['id'] = $oldalreadyAuditNode['id'];
				if($oldcurAuditUser){
					$oldcurAuditUser = $oldcurAuditUser.",".$userid;
				}else{
					$oldcurAuditUser = $userid;
				}
				$data['curAuditUser'] = $oldcurAuditUser;
				$data['alreadyAuditUser'] = "";
				$data['auditState'] = 0;
				$data['isaudittableid'] = 0; //子流程问题
				$fresut = $process_relation_formDao->save($data);
				if($fresut == false){
					$this->error("单据流程数据修改失败，请联系管理员");
				}
				/*
				 * 如果撤回节点为子流程节点。那么必须清理掉已经转过的子流程数据对应的ID。
				 */
				if($oldalreadyAuditNode['flowtype'] == 3 && $oldalreadyAuditNode['id']){
					//删除是子流程推送过的节点信息
					$process_relation_childrenDao = M("process_relation_children");
					$data = array();
					//清理掉自己审批节点转过的子流程，(因为此处存在一个节点转N个子流程。不能清理掉别人审批流的转子流程)
					$data['relation_formid'] = $oldalreadyAuditNode['id'];
					$data['createid'] = $userid;
					$childresult = $process_relation_childrenDao->where($data)->delete();
					if($childresult == false){
						$this->error("清理转子流程数据失败，请联系管理员");
					}
				}
				
				//5、封装审核意见参数
				$_REQUEST['dotype'] = 3;// "流程审核";
				$_REQUEST['doinfo'] = "【撤回审核过的节点，进行数据修改】  ";  //处理意见
				$result = $ProcessInfoHistoryModel->addProcessInfoHistory($tablename,$tableid,$oldalreadyAuditNode['id']);
				if($result == false){
					$this->error("单据历史记录保存失败，请联系管理员");
				}
				//判断撤回当前审核节点是否为并串混搭
				$nextinfo = $newinfo[$num];
				if($nextinfo['parallel'] == 2){
					//表示下级为混搭，而且已经推送了。
					$process_relation_parallel = M("process_relation_parallel");
					$map = array ();
					$map ['tablename'] = $tablename;
					$map ['tableid'] = $tableid;
					$map ['relation_formid'] =  $nextinfo['id'];
					$ppresult = $process_relation_parallel->where($map)->setField("auditState",0);
					if($ppresult == false){
						$this->error ( "修改下级撤回状态失败，请联系管理员！" );
					}
				}
			}
		}else{
			//启动后的节点撤回，直接进行打回到新建状态
			if($vo['createid'] != $userid && $_SESSION['a'] != 1){
				//撤回人，不是当前制单人，又不是管理员，则不能撤回
				$this->error ( "非本人或管理员，不能对单据进行撤回！" );
			}else{
				//先修改原数据，撤回人
				$data ['id'] = $tableid;
				$data ['auditState'] = 0;
				$data ['updateid'] = $userid;
				$data ['updatetime'] = time ();
				$list = $model2->save ( $data );
				if($list){
					//单据打回成功，为当前单据存储历史记录信息
					$ProcessInfoHistoryModel = D ( "ProcessInfoHistory" );
					//封装审核意见参数
					$_REQUEST['dotype'] = 5;	// "流程取回";
					$_REQUEST['doinfo'] = "流程取回";
					$result = $ProcessInfoHistoryModel->addProcessInfoHistory($tablename,$tableid);
					if(!$result){
						$this->error("单据历史记录保存失败，请联系管理员");
					}
					//表示下级为混搭，而且已经推送了。
					//实例化批次表
					$process_relation_parallel = M("process_relation_parallel");
					$map = array ();
					$map ['tablename'] = $tablename;
					$map ['tableid'] = $tableid;
					$ppresult = $process_relation_parallel->where($map)->delete();
					if($ppresult == false){
						$this->error ( "删除下级撤回状态失败，请联系管理员！" );
					}
					//删除原有的审批节点信息
					$where = array();
					$where['tablename'] = $tablename;
					$where['tableid'] = $tableid;
					//获取流程审批节点信息
					$process_relation_formDao = M("process_relation_form");
					$delbool = $process_relation_formDao->where($where)->delete();
					if($delbool == false){
						$this->error("原流程节点删除失败，请联系管理员");
					}
					$process_info_formDao = M("process_info_form");
					$delboolinfo = $process_info_formDao->where($where)->delete();
					if($delboolinfo == false){
						$this->error("原流程删除失败，请联系管理员");
					}
					//插入工作审批数据
					$MisWorkMonitoringModel = D("MisWorkMonitoring");
					$result = $MisWorkMonitoringModel->addWorkMonitoring($tablename,$tableid);
					if(!$result){
						$this->error("单据工作审批池数据撤回失败，请联系管理员");
					}
					$this->success("当前单据撤回成功。");
				}else{
					$this->error ("当前单据撤回失败，请联系管理员");
				}
			}
		}
		$this->success("当前单据撤回成功。");
	}
	/**
	 * @Title: digui
	 * @Description: todo(递归查询批次信息数据)
	 * @param array 批次数据 $data
	 * @return 返回数据
	 * @author liminggang
	 * @date 2015-9-17 下午3:37:08
	 * @throws
	 */
	protected function digui($data){
		//实例化并串表
		$process_relation_parallel = M("process_relation_parallel");
		$where = array();
		$where['parentid'] = $data['id'];
		$where['auditState'] = 2;
		//查询是否下级也已经完成了
		$diguidata = $process_relation_parallel->where($where)->order("sort asc")->find();
		if($diguidata){
			return $this->digui($diguidata);
		}else{
			return $data;
		}
	}
	/**
	 * @Title: lookupGetBackprocess
	 * @Description: todo(单据撤回)   
	 * @author 黎明刚
	 * @date 2014年12月2日 上午11:55:27 
	 * @throws
	 */
	public function lookupGetBackprocess1() {
		/*
		 * 流程撤回问题，经过讨论。一致认为需要先修改原表单数据，从原表单数据中获取其他信息。
		 * 第一步、修改原表单数据。
		 * 第二步、记录撤回表单的操作历史记录
		 * 第三步、操作审核数据池信息
		 */
		//获取当前控制器名称
		$modelname = $this->getActionName ();
		//获取当前表单ID
		$masid = $_REQUEST ['id'];
		
		$model2 = D ( $modelname );
		$vo = $model2->find ( $masid );
		if ($vo ['auditState'] != 1) {
			$this->error ( "当前单据审核状态已经改变，请刷新页面！" );
			exit ();
		}
		$data ['id'] = $masid;
		$data ['auditState'] = 0;
		$data ['updatetime'] = $_SESSION [C ( 'USER_AUTH_KEY' )];
		$data ['updateid'] = time ();
		$list = $model2->save ( $data );
		if($list){
			//单据打回成功，为当前单据存储历史记录信息
			$ProcessInfoHistoryModel = D ( "ProcessInfoHistory" );
			//封装审核意见参数
			$_REQUEST['dotype'] = 5;	// "流程取回";
			$_REQUEST['doinfo'] = "流程取回";
			$result = $ProcessInfoHistoryModel->addProcessInfoHistory($modelname,$masid);
			if(!$result){
				$this->error("单据历史记录保存失败，请联系管理员");
			}
			
			//删除原有的审批节点信息
			$where = array();
			$where['tablename'] = $modelname;
			$where['tableid'] = $masid;
			//获取流程审批节点信息
			$process_relation_formDao = M("process_relation_form");
			$delbool = $process_relation_formDao->where($where)->delete();
			if($delbool == false){
				$this->error("原流程节点删除失败，请联系管理员");
			}
			//插入工作审批数据
			$MisWorkMonitoringModel = D("MisWorkMonitoring");
			$result = $MisWorkMonitoringModel->addWorkMonitoring($modelname,$masid);
			if(!$result){
				$this->error("单据工作审批池数据撤回失败，请联系管理员");
			}
			$this->success("当前单据撤回成功。");
			
		}else{
			$this->error ("当前单据撤回失败，请联系管理员");
		}
	}
	/**
	 * @param string $step 默认为false，当为false时，出现错误直接提示error，当传入为真时，只中断代码执行，不提示error信息,因为后面还有执行语句
	 * @param number $douserid 转子流程才会传入此参数，为子流程审核人
	 * @return boolean
	 */
	public function auditProcess($step=false,$douserid=0){
		// 获取当前控制器名称
		$name = $this->getActionName ();
		// 获取当前表单ID
		$masid = $_POST ['id'];
		// 获取单据上的信息
		$vo = D ( $name )->find ( $masid );
		//判断，当传入douserid时，应该是转子流程的审核
		$userid = $douserid?$douserid:$_SESSION[C('USER_AUTH_KEY')];
		//获取流程审批节点信息
		$mis_work_monitoringDao = M("mis_work_monitoring");
		$where = array();
		$where['tablename'] = $name;
		$where['tableid'] = $masid;
		$where['dostatus'] = 0;//未处理
		$where ['_string'] = 'FIND_IN_SET(  ' . $userid . ',curAuditUser )';
		$myworklist = $mis_work_monitoringDao->where($where)->select();
		if(count($myworklist)==0){
			//没有当前我需要审核的数据
			if($step){
				//当step为真时，只终止执行，不提示错误
				return true;
			}else{
				//结果为假时，终止执行并提示错误。
				$this->error("单据已被其他同事审核，无需再审！");
				exit;
			}
		}
		//获取当前审批节点信息
		$process_relation_formDao = M("process_relation_form");
		$where = array();
		$where['tablename'] = $name;
		$where['tableid'] = $masid;
		$where['auditState'] = 0;//未处理
		$where['doing'] = 1;//进行中的节点
		$where['flowtype'] = array("gt",1);//审批节点或者转子流程节点
		$newinfo = $process_relation_formDao->where($where)->order('sort asc')->select();
		$infolist = array_merge($newinfo);
		
		//获取当前审批的节点
		$current_audit_node_id = $infolist[0]['id'];
		//获取当前审核节点名称
		$current_audit_node_name = $infolist[0]['name'];
		//获取当前审批节点是否是变更节点
		$ischageoperateid = $infolist[0]['catgory'];
		
		/*
		 * 第一步、修改当前表单数据审核状态 auditStatus 
		 * 第二步、加入ProcessInfoHistory审核的历史记录信息
		 * 第三步、如果当前节点存在知会，则进行邮件知会。
		 * 第四步、验证当前表单流程是否还存在审批节点未完成
		 * 第五步、修改监控表当前数据审批状态，以及如果还存在审批节点，向监控表插入下一节点审批人信息
		 */
		
		//---------------------第一步、修改当前表单数据审核状态 auditStatus -----------------------------//
		//拼装当前表单需要修改的字段数据
		$_POST ['auditState'] = 2; //审核中
		//保存下一审核信息数据到单据上
		$_POST ['startprocess'] = 1;
		$_POST ['ischageoperateid'] = $ischageoperateid;//1为变更审批标记，0为普通审批标记
		//保存审核信息,这里进去会出现表单令牌错误
		$this->update ();
		
		//---------------------第二步、加入ProcessInfoHistory审核的历史记录信息-----------------------------//
		
		//插入当前审核人审核历史记录信息
		$ProcessInfoHistoryModel = D ( "ProcessInfoHistory" );
		//封装审核意见参数
		$_REQUEST['dotype'] = 3;// "流程审核";
		$result = $ProcessInfoHistoryModel->addProcessInfoHistory($name,$masid,$current_audit_node_id);
		if($result == false){
			$this->error("单据历史记录保存失败，请联系管理员");
		}
		//---------------------第三步、如果当前节点存在知会，则进行邮件知会。-----------------------------//
		//审核了就发系统邮件知会制单人
		$this->auditremindmessage($vo['createid'],$vo['orderno'],$name,1,$vo['id']);
		//审核了就发系统邮件给知会人
		$this->workNoticeMessages($infolist[0]['informpersonid'],false,$name,$vo['id']);
		//保存到知会表
		$this->lookupWorkMisNotify($infolist[0]['informpersonid'],$name,$vo['id'],1);
		
		
		//---------------------第四步、验证当前表单流程是否还存在审批节点未完成-----------------------------//
		// 如果是特殊流程（自定义流程）
		if ($vo ['flowid']) {
			//拼装update数据(审核信息数据) 返回下一审核流程节点tid
			 $this->auditSpecialProcess ( $vo );
		} else {
			//拼装update数据(审核信息数据) 返回下一审核流程节点tid
			 $this->auditGdingProcess($infolist,$name,$masid);
		}
		//重新查询一次可执行的节点
		$where = array();
		$where['tablename'] = $name;
		$where['tableid'] = $masid;
		$where['auditState'] = 0;//未处理
		$newinfo = $process_relation_formDao->where($where)->order('sort asc')->select();
		$infolist = array_merge($newinfo);
		//过滤掉当前非可执行节点
		foreach($newinfo as $key=>$val){
			if($val['doing'] == 0 || $val['flowtype'] < 2){
				unset($newinfo[$key]);
			}
		}
		
		/*
		 * 判断是否为同一审核人处理N节点
		 */
		//用来存储同一人审核跨级处理自己的节点
		$kuajiinfoList = array();
		if($newinfo){
			foreach($newinfo as $nk=>$nv){
				//必须是串行节点，而且当前登录人是审核人
				if($nv['parallel']>0 || !in_array($userid, explode(",", $nv['curAuditUser'])) || $nv['flowtype']!=2){
					break;
				}
				//存储跨级节点数据
				$kuajiinfoList[] = $nv;
				unset($newinfo[$nk]);
			}
		}
		//判断是否存在同人跨级处理的节点
		if($kuajiinfoList){
			//存储审核意见
			$doinfo = $_REQUEST['doinfo'];
			foreach($kuajiinfoList as $kjkey=>$kjval){
				//封装审核意见参数
				$_REQUEST['dotype'] = 3;// "流程审核";
				$_REQUEST['doinfo'] = $doinfo."  【当前节点由 {$current_audit_node_name} 并行审核】  ";  //处理意见
				$result = $ProcessInfoHistoryModel->addProcessInfoHistory($name,$masid,$kjval['id']);
				if($result == false){
					$this->error("单据历史记录保存失败，请联系管理员");
				}
				$data = array();
				$data['id'] = $kjval['id'];
				//串行(审核人)
				$data['alreadyAuditUser'] = $userid;
				//清除当前审核人
				$data ["curAuditUser"]="";
				//当前节点审核状态完成
				$data['auditState'] = 1;
				$result = $process_relation_formDao->save($data);
				if($result == false){
					$this->error("单据并行处理失败，请联系管理员");
				}
			}
		}
		//---------------------第五步  插入工作审批数据-----------------------------//
		$MisWorkMonitoringModel = D("MisWorkMonitoring");
		//标记为子流程任务
		$bool =true;
		//if($step==2)$bool = false; //这里代表是子流程审批。因为子流程审批处理人非本人，只有转子任务才是本人
		$result = $MisWorkMonitoringModel->addWorkMonitoring($name,$masid,$newinfo,$userid);
		if(!$result){
			$this->error("单据工作审批池数据推送失败，请联系管理员");
		}
		// 如果流程结束插入相应的流程结束信息
		if (count($newinfo)<1) {
			//标记流程结束
			$_POST ['auditState'] = 3;
			$_POST ['operateid'] = 1;//单据终审标记
			$_POST ['ischageoperateid'] = $ischageoperateid;//1为变更审批标记，0为普通审批标记
			//保存下一审核信息数据到单据上
			$_POST ['startprocess'] = 1;
			$this->update ();
			//记录流程历史信息
			//封装审核意见参数
			$_REQUEST['doinfo'] = "流程结束";
			$_REQUEST['dotype'] = 4; //"流程结束";
			$result = $ProcessInfoHistoryModel->addProcessInfoHistory($name,$masid);
			if(!$result){
				$this->error("单据历史记录保存失败，请联系管理员");
			}
			//---------------------工作审核结束，已发办结单据状态-----------------------------//
			$result = $MisWorkMonitoringModel->addWorkMonitoring($name,$masid);
			if(!$result){
				$this->error("单据工作审批池数据推送失败，请联系管理员");
			}
			if (method_exists ( $this, "overProcess" )) {
				$this->overProcess ( $masid );
			}
			//$this->lookupdataInteractionPrepareData($masid);
			//子流程操作(终审时才调用)
			//$this->_before_overProcess ( $masid );
		}
		if($step==false){
			$this->success("单据审核成功");
		}
	}
	
	function auditGdingProcess($infolist,$tablename,$tableid){
		$data = array();
		//定义是否当前节点审核完毕
		$bool = false;
		// 当前审核人
		$userid = $_SESSION [C ( 'USER_AUTH_KEY' )];
		//第一步获取当前审核节点信息
		$infovo = $infolist[0];
		//判断当前审批节点属于什么类型，（串行，并行）
		if($infovo['parallel'] == 2){
			//多批次并行
			$process_relation_parallelDao = M("process_relation_parallel");
			$where = array();
			$where['tablename'] = $tablename;
			$where['tableid'] = $tableid;
			$where['relation_formid'] = $infovo['id'];
			$where['auditState'] = 1;
			$parallelist = $process_relation_parallelDao->where($where)->select();
			if($parallelist){
				foreach ($parallelist as $key=>$val){
					if(in_array($userid, explode(",", $val['curAuditUser']))){
						$process_relation_parallelDao->where("id = ".$val['id'])->setField("auditState",2);
					}
				}
				unset($where['auditState']);
				$where['auditState'] = array("elt",1);//"auditState = 0 or auditState = 1";
				$parallvo = $process_relation_parallelDao->where($where)->find();
				if(!$parallvo){
					//当前节点审核状态完成
					$data['auditState'] = 1;
					$data["curAuditUser"]="";
					$bool = true;
				}
			}else{
				//当前节点审核状态完成
				$data['auditState'] = 1;
				$data["curAuditUser"]="";
				$bool = true;
			}
			if($infovo['alreadyAuditUser']){
				$data ["alreadyAuditUser"] .= $infovo ["alreadyAuditUser"].",".$userid ; //保存当前已审核人
			}else{
				$data ["alreadyAuditUser"] = $userid ; //保存当前已审核人
			}
		}else if($infovo['parallel'] == 1){ //单批次并行 
			// 当前待审核人
			$curAuditUser = array_filter(explode ( ",", $infovo ['curAuditUser'] ));
			/*
			 * 判断当前节点类型
			 * 1、普通流程节点
			 * 2、子流程节点，如果是子流程节点，则不能使用人员替换功能，只能进行人员总数对比
			 */
			if($infovo['flowtype'] == 3){
				//转子流程节点的审核
				$alreadyAuditUser = array_filter(explode ( ",", $infovo ['alreadyAuditUser'] ));
				$curcount = count($curAuditUser);
				$alreadcount = count($alreadyAuditUser)+1;
				if($alreadcount == $curcount){
					//当2个审批人数相同时，则判定子流程节点完成
					$data['auditState'] = 1;
					$data["curAuditUser"]="";
					$bool = true;
				}
			}else{
				// 节点并行，表示必须把当前待审核人全部审核完成，才通过此流程节点。才代表此流程节点完成。
				$diffUserArr = array_diff ( $curAuditUser, array ( $userid ) );
				if (count ( $diffUserArr )) {
					// 此流程节点未完成
					//保留已审核人员
					$data ["curAuditUser"] = implode ( ",", $diffUserArr ); //保存此节点下一个待审核人
				} else {
					// 此流程节点完成
					//当前节点审核状态完成
					$data['auditState'] = 1;
					$data["curAuditUser"]="";
					$bool = true;
				}
			}
			//组合已审核人员
			if($infovo['alreadyAuditUser']){
				$data ["alreadyAuditUser"] .= $infovo ["alreadyAuditUser"].",".$userid ; //保存当前已审核人
			}else{
				$data ["alreadyAuditUser"] = $userid ; //保存当前已审核人
			}
		}else{
			//串行(审核人)
			$data['alreadyAuditUser'] = $userid;
			//清除当前审核人
			$data ["curAuditUser"]="";
			//当前节点审核状态完成
			$data['auditState'] = 1;
			$bool = true;
		}
		// 判断是否存在加签
		$flowUserid = $_REQUEST ['flowUserid'];
		$time = time();
		$data['id'] = $infovo['id'];
		if($flowUserid){
			//存在价签节点。则改变processto。
			$data['processto'] = $time;
		}
		$process_relation_formDao = M("process_relation_form");
		$result = $process_relation_formDao->save($data);
		if($result == false){
			$this->error("单据工作审批失败，请联系管理员");
		}
		if($flowUserid){
			//给数据中组合内容,修改排序顺序
			$where = array();
			$where['tablename'] = $infovo['tablename'];
			$where['tableid'] = $infovo['tableid'];
			$where['sort'] = array("gt",$infovo['sort']);
			$data = array();
			$data['sort'] = array("exp","sort+1");
			$result = $process_relation_formDao->where($where)->save($data);
			if($result == false){
				$this->error("加签失败，请联系管理员");
			}
			//插入加签节点
			$data = array();
			$data['tablename'] = $infovo['tablename'];
			$data['tableid'] = $infovo['tableid'];
			$data['infoid'] = $infovo['infoid'];
			$data['typeid'] = $infovo['typeid'];//流程分类
			$data['relationid'] = $time;
			$data['name'] = "加签节点";
			$data['processto'] = $infovo['processto'];
			$data['flowid'] = $time;
			$data['flowtype'] = 2;
			$data['curAuditUser'] = implode(",", $flowUserid);
			$data['auditUser'] = implode(",", $flowUserid);
			$data['sort'] = $infovo['sort']+1;
			$data['createtime'] = time();
			$data['createid'] = $userid;
			$relaid = $process_relation_formDao->add($data);
			if($relaid == false){
				$this->error("加签失败，请联系管理员");
			}
		}
		if($bool){
			//上个审批节点后面的审核节点
			$nextprocessto = $infovo['processto'];
			if($flowUserid){
				//价签节点的话。下级节点就变成加签节点了。
				$nextprocessto = $time;
			}
			//查询下及节点是否为判定节点，进行动态荀子
			$where = array();
			$where['tablename'] = $tablename;
			$where['tableid'] = $tableid;
			$where['flowid'] = $nextprocessto;
			$where['catgory'] = $infovo['catgory']; //区别变更流程和普通流程节点
			$info = $process_relation_formDao->where($where)->order('sort asc')->find();
			
			if($info['flowtype'] == 1){
				//进行重新构造审批节点。（动态寻址，根据判定节点）
				$where = array();
				$where['tablename'] = $tablename;
				$where['tableid'] = $tableid;
				$where['auditState'] = 0;//未处理
				$where['id'] = array('gt',$infovo['id']);//审批节点比当前节点大
				$newinfo = $process_relation_formDao->where($where)->order('sort asc')->select();
				$infolist = array_merge($newinfo);
				
				//如果判定节点一个都不满足，则结束流程
				$where = array();
				$where['tablename'] = $tablename;
				$where['tableid'] = $tableid;
				$where['auditState'] = 0;//未处理
				$data = array();
				$data['doing'] = 0;
				$resutl = $process_relation_formDao->where($where)->save($data);
				if($resutl === false){
					$this->error("动态寻子节点变更失败，请联系管理员");
				}
				// 获取单据上的信息
				$vo = D ( $tablename )->find ( $tableid );
				$nextall = $this->auditNodeDiGui($info,$infolist,$vo);
				if(count($nextall)>0){
					//这里将进行递归，验证是否满足条件的所有下级节点中继续存在判定节点。需要变化判定节点走向
					$where = array();
					$where['tablename'] = $tablename;
					$where['tableid'] = $tableid;
					$where['auditState'] = 0;//未处理
					$where['flowid'] = array('in',$nextall);//未处理
					$data = array();
					$data['doing'] = 1;
					$resutl = $process_relation_formDao->where($where)->save($data);
					if($resutl === false){
						$this->error("动态寻子节点变更失败，请联系管理员");
					}
				}
			}
		}
	}
	
	public function auditNodeDiGui($info,$infolist,$vo,$nextall=array()){
		$nextinfo = array();
		//判定节点
		if($info['flowtype'] == 1){
			//遇到判定节点，我则进行条件重新梳理
			$processto = explode(",", $info['processto']);
			//规则(条件数组)
			$rulesinfo = explode(",", $info['processrulesinfo']);
			if(count($processto)>0){
				foreach($processto as $key=>$val){
					//解密，反序列化
					$derulesinfo = unserialize ( base64_decode(base64_decode ( $rulesinfo[$key] )));
					unset($derulesinfo['mapsql']);
					$bool = D("ProcessInfo")->getRuleInfo ( $derulesinfo, $vo );
					if($bool){
						//存储满足条件的下级节点
						$nextinfo['nextnode'] =$val;
						break;
					}
				}
			}
		}else{
			//普通节点
			$nextinfo['nextnode'] = $info['processto'];
		}
		if($nextinfo['nextnode']){
			//存储当前获取到的节点
			array_push($nextall, $nextinfo['nextnode']);
			//循环，获取当前下级节点，进行递归数据
			foreach($infolist as $k=>$v){
				if($v['flowid'] == $nextinfo['nextnode']){
					return $this->auditNodeDiGui($v,$infolist,$vo,$nextall);
				}
			}
		}
		return $nextall;
	}
	
	/**
	 * @Title: auditGdingProcess
	 * @Description: todo(这里用一句话描述这个方法的作用)
	 * @param unknown $vo        	
	 * @author 黎明刚
	 * @date 2014年12月1日 下午8:56:24
	 * @throws
	 */
	function auditGdingProcess1($vo,$infovo) {
		//当前审核的流程节点ID
		$current_audit_node_id = $vo['ostatus'];
		/**
		 * ***********************************************
		 * 此处开始查询流程效果
		 * **********************************************
		 */
		// 获取下级审核节点,赋值 curAuditUser 和 $tid
		$ostatus = explode ( ",", $vo ['ostatus'] ); // 当前节点
		$alreadyauditnode = explode ( ",", $vo ['alreadyauditnode'] ); // 已审核节点
		$alluser = explode ( ";", $vo ['auditUser'] ); // 所有审核人
		$allnode = explode ( ",", $vo ['allnode'] ); // 所有流程节点ID
		$flowUserid = $_REQUEST ['flowUserid'];// 判断是否存在加签
		
		// 查询流程模板信息，查询出所有该流程上的节点，通过ProcessConfigModel里的getprocessinfo函数返回信息
		// 当前只获取了流程模板id-》pid;是否跨级-》crosslevel，知会人-》informpersonid，执行人-》executorid
		
		// 暂时模拟一个数据
		$pcarr = array (
				'pid' => $vo ['ptmptid'],
				'crosslevel' => 0,
				'informpersonid' => "",
				'executorid' => "" 
		); // 初始化
		   
		// 查询流程节点信息,查询的流程关联表：专门存储流程与节点的关联信息
		// 获取流程下全部节点，拼装为二维数组，键值为节点id;一维数组里包含如下元素：[tid]节点id;[userid]该节点审核人；是否并行[parallelid]
		$model_pr = M ( "process_relation" );
		$map_pr ["pinfoid"] = $vo ['ptmptid'];
		$map_pr ["tablename"] = "process_info";
		$relationInfo = $model_pr->where ( $map_pr )->order ( 'sort asc' )->getField ( "id,pinfoid,parallel", true );
		
		// 将表单的审核人审核节点封装。
		$process_info = array ();
		foreach ( $allnode as $knode => $vnode ) {
			$process_info [] = array (
					'tid' => $vnode,
					'userid' => $alluser [$knode],
					'parallel' => $relationInfo [$vnode] ['parallel'], // 是否并行状态
					'parallelid' => 0 
			);
			if ($flowUserid) {//判断是否加签
				
				$flowUseriddh = implode ( ",", $flowUserid );//获取加签审核人
				//加签，则加入当前审核人之后
				if ($vnode == $current_audit_node_id) {
					if (isset ( $relationInfo [$vnode] )) {
						$process_info [] = array (
								'tid' => time (),
								'userid' => $flowUseriddh,
								'parallel' => 0, // 是否并行状态
								'parallelid' => 0 
						);
					} else {
						$process_info [] = array (
								'tid' => time (),
								'userid' => $flowUseriddh,
								'parallel' => 0, // 是否并行状态
								'parallelid' => 0 
						);
					}
				}
			}
		}
		
		$tid = - 1; //流程节点id，默认-1代表终止节点
		
		$jqauditUser = array (); // 加签后，所有审核人。
		$jqallnode = array (); // 加签后所有的审核节点 
		// 根据 并行重新排列对应的审核节点
		foreach ( $process_info as $k1 => $v1 ) {
			$userArr = explode ( ",", $v1 ['userid'] );
			if ($v1 ['parallel'] && count ( $userArr ) > 1) {
				//并行 并且并行人数大于一
				$process_info [$k1] ['bx'] = 1;
			} else {
				$process_info [$k1] ['bx'] = 0;
			}
			// 加签后，新的所有审核人
			array_push ( $jqauditUser, $v1 ['userid'] );
			// 加签后，所有审核节点
			array_push ( $jqallnode, $v1 ['tid'] );
		}
		if ($flowUserid) {
			// 重新保存一份POST中的所有审核人，和所有审核节点，此POST将进入update修改表单数据
			$_POST ['auditUser'] = implode ( ";", $jqauditUser );
			$_POST ["allnode"] = implode ( ",", $jqallnode );
		}
		//重置键值
		$process_info = array_merge ( $process_info );
		
		$doinfo = "";
		if ($pcarr ['crosslevel'] > 0) { //跨级
		    //当前流程存在跨级， 注释： 跨级级 流程节点中。都有同一人 '张三' 如果跨级审核，那么作用效果就是流程节点最后一个张三也审核通过。
			foreach ( $process_info as $gk => $gv ) {
				$out = false;
				// 跨级形式，一律不处理并行问题。直接一杆通过。
				if ($gv ['tid'] == $current_audit_node_id) {
					if (isset ( $process_info [$gk + 1] )) {
						// 当前审核人
						$userid = $_SESSION [C ( 'USER_AUTH_KEY' )];
						// 判断当前审核人属于第几级。跨级审核(存在后面人员审核前面流程节点。如果同意，表示流程走向下一位)
						// 获取跨级级数
						$corsslevel = $pcarr ["crosslevel"];
						$curAuditUser = "";
						$onlybool = true;
						$tokey = 0;
						for($j = 0; $j <= $corsslevel; $j ++) {
							if (in_array ( $userid, explode ( ",", $gv ['userid'] ) )) {
								// 判断当前审核人是否在当前节点中。
								if ($onlybool) {
									// 是当前审核人。则把标志$onlybool改为false
									$onlybool = false;
									$tid = $process_info [$gk + 1] ['tid'];
									}
									if (isset ( $process_info [$gk + $j + 1] )) {
										if ($curAuditUser) {
											$curAuditUser .= "," . $process_info [$gk + $j + 1] ['userid']; // 下次可以审核的用户id
										} else {
											$curAuditUser = $process_info [$gk + $j + 1] ['userid']; // 下次可以审核的用户id
										}
									}
								}
								if ($onlybool && isset ( $process_info [$gk + $j + 1] )) {
									if (in_array ( $userid, explode ( ",", $process_info [$gk + $j + 1] ['userid'] ) )) {
										$tokey = $gk + $j + 1;
									}
								}
							}
							if ($onlybool && $tokey) {
								$_POST ['doinfo'] .= "  （跨级审核）";
								// 当前审核节点
								$current_audit_node_id = $process_info [$tokey] ['tid'];
								
								$curAuditUser = "";
								if (isset ( $process_info [$tokey + 1] )) {
									// 下级审核节点
									$tid = $process_info [$tokey + 1] ['tid'];
									for($h = 0; $h <= $corsslevel; $h ++) {
										if (isset ( $process_info [$tokey + $h + 1] )) {
											if ($curAuditUser) {
												$curAuditUser .= "," . $process_info [$tokey + $h + 1] ['userid']; // 下次可以审核的用户id
											} else {
												$curAuditUser = $process_info [$tokey + $h + 1] ['userid']; // 下次可以审核的用户id
											}
										}
									}
								}
							}
							$_POST ["curAuditUser"] = $curAuditUser;
							$_POST ["curNodeUser"] = $curAuditUser;
						}
						$out = true;
					}
					if ($out)
						break;
				}
			} else {
				// 不存在跨级时的审核功能
				foreach ( $process_info as $k => $v ) {
					$out = false;
					if ($v ['tid'] == $current_audit_node_id) {
						if (isset ( $process_info [$k + 1] )) {
							// 判断当前节点是否并行
							if ($v ['bx']) {
								$tid = $current_audit_node_id;
								// 当前审核人
								$userid = $_SESSION [C ( 'USER_AUTH_KEY' )];
								// 当前待审核人
								$curAuditUser = explode ( ",", $vo ['curAuditUser'] );
								// 节点并行，表示必须把当前待审核人全部审核完成，才通过此流程节点。才代表此流程节点完成。
								$diffUserArr = array_diff ( $curAuditUser, array (
										$userid 
								) );
								if (count ( $diffUserArr )) {
									// 此流程节点未完成
									$tid = $current_audit_node_id;
									$_POST ["curAuditUser"] = implode ( ",", $diffUserArr ); // 保存此节点下一个待审核人
									$_POST ["curNodeUser"] = implode ( ",", $diffUserArr ); // 保存此节点下一个待审核人
								} else {
									// 此流程节点完成
									$tid = $process_info [$k + 1] ['tid'];
									$_POST ["curAuditUser"] = $process_info [$k + 1] ['userid']; // 保存下一节点待审核人
									$_POST ["curNodeUser"] = $process_info [$k + 1] ['userid']; // 保存下一节点待审核人
								}
							} else {
								$tid = $process_info [$k + 1] ['tid'];
								$_POST ["curAuditUser"] = $process_info [$k + 1] ['userid'];
								$_POST ["curNodeUser"] = $process_info [$k + 1] ['userid']; // 保存下一级的节点用户
							}
						}
						$out = true;
					}
					if ($out)
						break;
				}
			}
			return $tid;
		}
	/**
	 * @Title: auditSpecialProcess
	 * @Description: 自定义流程操作数据方法
	 * @param array $vo        	
	 * @return unknown
	 * @author liminggang
	 * @date 2014-9-12 下午4:40:22
	 * @throws
	 *
	 */
	function auditSpecialProcess($vo) {
		// 保存全部审核人
		$allnode = explode ( ",", $vo ['allnode'] ); // 审批节点
		$audituser = explode ( ";", $vo ['auditUser'] ); // 审批人列表
		$num = 0; // 初始化key值
		if ($allnode) {
			// 判断是否存在加签
			$flowUserid = $_REQUEST ['flowUserid'];
			if ($flowUserid) {
				// 存在加签
				$flowUseriddh = implode ( ",", $flowUserid );
				$allnodes = "";
				foreach ( $allnode as $k => $v ) {
					if ($k + 1 == count ( $allnode )) {
						$node = $v + 1;
						$allnodes = $vo ['allnode'] . ',' . $node;
					}
					if ($v == $vo ['ostatus']) {
						$num = $k + 1;
					}
				}
				$au = array ();
				
				foreach ( $audituser as $key => $val ) {
					if ($key == $num) {
						array_push ( $au, $flowUseriddh, $val );
					} else if (count($audituser) == $num && count($audituser) == $key+1) {
						array_push ( $au, $val ,$flowUseriddh);
					} else {
						array_push ( $au, $val );
					}
				}
				// 当前审核人
				$userid = $_SESSION [C ( 'USER_AUTH_KEY' )];
				// 当前待审核人
				$curAuditUser = explode ( ",", $vo ['curAuditUser'] );
				// 节点并行，表示必须把当前待审核人全部审核完成，才通过此流程节点。才代表此流程节点完成。
				$diffUserArr = array_diff ( $curAuditUser, array ($userid) );
				if (count ( $diffUserArr )) {
					// 此流程节点未完成
					$_POST ['ostatus'] = $vo ['ostatus'];
					$_POST ["curAuditUser"] = implode ( ",", $diffUserArr ); // 保存此节点下一个待审核人
					$_POST ["curNodeUser"] = implode ( ",", $diffUserArr ); // 保存此节点下一个待审核人
				} else {
					$_POST ["curNodeUser"] = $_POST ["curAuditUser"] = $flowUseriddh; // 当前可审核用户 //当前节点审核用户等于当前可审核用户
					$allnodes = explode ( ",", $allnodes );
					$_POST ['ostatus'] = $allnodes [$num];
					$allnodes = implode ( ",", $allnodes );
					// 保存已审核节点
					if ($vo ['alreadyauditnode']) {
						$_POST ['alreadyauditnode'] = $vo ['alreadyauditnode'] . "," . $vo ['ostatus'];
					} else {
						$_POST ['alreadyauditnode'] = $vo ['ostatus'];
					}
				}
				$_POST ["allnode"] = $allnodes;
				$_POST ["auditUser"] = implode ( ";", $au );
			} else {
				//当前审核人
				$userid = $_SESSION [C ( 'USER_AUTH_KEY' )];
				//当前待审核人
				$curAuditUser = explode ( ",", $vo ['curAuditUser'] );
				// 节点并行，表示必须把当前待审核人全部审核完成，才通过此流程节点。才代表此流程节点完成。
				$diffUserArr = array_diff ( $curAuditUser, array ($userid ) );
				if (count ( $diffUserArr )) {
					// 此流程节点未完成
					$_POST ['ostatus'] = $vo ['ostatus'];
					$_POST ["curAuditUser"] = implode ( ",", $diffUserArr ); // 保存此节点下一个待审核人
					$_POST ["curNodeUser"] = implode ( ",", $diffUserArr ); // 保存此节点下一个待审核人
				} else {
					foreach ( $allnode as $key => $val ) {
						if ($val == $vo ['ostatus']) {
							$num = $key + 1;
						}
					}
					if ($allnode [$num]) {
						$_POST ['ostatus'] = $allnode [$num];
						$_POST ["curNodeUser"] = $_POST ["curAuditUser"] = $audituser [$num]; // 当前可审核用户 //当前节点审核用户等于当前可审核用户
						// 保存已审核节点
						if ($vo ['alreadyauditnode']) {
							$_POST ['alreadyauditnode'] = $vo ['alreadyauditnode'] . "," . $vo ['ostatus'];
						} else {
							$_POST ['alreadyauditnode'] = $vo ['ostatus'];
						}
					} else {
						//这里是节点末尾
						$_POST ['ostatus'] = - 1;
						$_POST ['alreadyauditnode'] = $vo ['allnode'];
					}
				}
			}
			return $_POST ['ostatus'];
		} else {
			$this->error ( "自定义流程存在错误，请联系管理员！" );
		}
	}
	
	// 获取下一节点
	function getNextProcessNode($tid, $pid, $userid = 0) {
	}
	
	/**
	 * @Title: _before_delete
	 * @Description: 此删除前置方法。是针对带有明细的表单数据删除时，将明细的数据也同时删除 必须在单头控制器中写入getSubModelName方法才生效
	 * @author liminggang
	 * @date 2014-9-12 下午4:42:15
	 * @throws
	 */
	public function _before_delete() {
		$id = $_REQUEST ['id'];
		if (method_exists ( $this, 'getSubModelName' )) {
			$subName = $this->getSubModelName ();
			$a = A ( $subName );
			if (! is_array ( $id )) {
				$id = explode ( ",", $id );
			}
			$a->delete ( $id );
		}
	}
	/**
	 * @Title: seeProcessDetail
	 * @Description: todo(新版流程查看)
	 * @author 杨东
	 * @date 2013-4-17 上午10:34:50
	 * @throws
	 */
	public function seeProcessDetail() {
		$id = $this->escapeStr ( $_GET ['id'] ); // 当前单据ID
		$name = $this->getActionName (); // 当前单据模型
		$model = D ( $name ); // 构造当前单据模型
		$ptmp = $model->where ( array ("id" => $id) )->find (); // 查询当前表的状态noderuleinfo
		
		$pihmodel = D ( 'process_info_history' ); // 构造流程明细模型
		$usermodel = D ( 'User' ); // 用户模型
		                        // 新建状态的流程查看(初始化节点)
		$list = array ();
		if ($ptmp ['flowid']) {
			// 查询最新流程
			$pihmap = array ();
			$pihmap ['pid'] = $ptmp ['ptmptid'];
			$pihmap ['tableid'] = $id;
			$pihmap ['tablename'] = $name;
			$pihlist = $pihmodel->where ( $pihmap )->order ( 'id desc' )->select ();
			// 存在自定义流程
			$allnode = $auditUser = array ();
			if ($ptmp ['allnode'])
				$allnode = explode ( ',', $ptmp ['allnode'] ); // 所有节点
			if ($ptmp ['auditUser'])
				$auditUser = explode ( ';', $ptmp ['auditUser'] ); // 所有用户
			$tekey = 1;
			// 构造所有节点
			foreach ( $allnode as $k => $v ) {
				$auArr = explode ( ",", $auditUser [$k] );
				$str = "";
				if (count ( $auArr ) > 1) {
					$list [$tekey] ['bx'] = "(并行)";
				}
				$list [$tekey] ['tid'] = $v;
				$list [$tekey] ['name'] = "自定义节点" . $tekey;
				$list [$tekey] ['user'] = $auditUser [$k];
				$tekey ++;
			}
			// 过滤回退以前的数据
			$ftrue = true;
			$judge = array ();
			foreach ( $pihlist as $k => $v ) {
				if ($v ['dotype'] == 1) { // 流程新建
					$judge [] = $v;
					break;
				}
				if ($ftrue) {
					$judge [] = $v;
				}
				if ($v ['dotype'] == 2) { // 流程启动
					$ftrue = false;
				}
			}
			// 添加-4流程结束、-2流程启动、-1流程新建节点
			array_unshift ( $list, array (
					'tid' => - 1 
			), array (
					'tid' => -2 
			) );
			array_push ( $list, array (
					'tid' => - 4 
			) ); // 结束
			
			// 给流程填满基础信息
			foreach ( $list as $k => $v ) {
				// 添加中文名称及查看审核人链接
				if ($v ['tid'] > 0) {
					$list [$k] ["url"] = "__URL__/seeAuditUser/id/" . $id . "/tid/" . $v ['tid'];
				} else {
					if ($v ['tid'] == - 1) {
						$list [$k] ['name'] = '新建';
						$list [$k] ['dotime'] = $ptmp ["createtime"];
					} else if ($v ['tid'] == - 2) {
						$list [$k] ['name'] = '开始';
						$list [$k] ['dotype'] = '创建';
					} else if ($v ['tid'] == -4) {
						$list [$k] ['name'] = '结束';
					}
				}
				$bxuser = array_filter ( explode ( ",", $v ['user'] ) );
				if (count ( $bxuser ) > 1) {
					$douser = $doinfo = array ();
					$dotime = $dotype =0;
					foreach ( $bxuser as $bxkey => $bcval ) {
						// 添加流程记录信息
						foreach ( $judge as $k2 => $v2 ) {
							// 判断当前节点等于流程明细的审核节点
							if ($v ['tid'] == $v2 ['ostatus'] && $bcval == $v2 ['userid'] && $v ['tid']>0) {
								$douser[] = $usermodel->where ( 'id=' . $v2 ['userid'] )->getField ( 'name' );
								$doinfo[] = $v2 ['doinfo'];
								$dotime =  $v2 ['dotime'];
								$dotype =  $v2 ['dotype'];
							}
						}
					}
					if(count($bxuser) == count($douser)){
						$list [$k] ['show'] = 1; // 是否显示
					}
					$list [$k] ['dotime'] = $dotime; // 处理时间
					$list [$k] ['dotype'] = $dotype; // 处理状态
					$list [$k] ['doinfo'] = $doinfo; // 处理意见
					$list [$k] ['douser'] = $douser; // 处理人
				} else {
					// 添加流程记录信息
					foreach ( $judge as $k2 => $v2 ) {
						$douser = $usermodel->where ( 'id=' . $v2 ['userid'] )->getField ( 'name' );
						// 判断当前节点等于流程明细的审核节点
						if ($v ['tid'] == $v2 ['ostatus'] && $v['tid'] > 0) {
							$list [$k] ['show'] = 1; // 是否显示
							$list [$k] ['dotime'] = $v2 ['dotime']; // 处理时间
							$list [$k] ['dotype'] = $v2 ['dotype']; // 处理状态
							$list [$k] ['douser'] [] = $douser; // 处理人
							$list [$k] ['doinfo'] [] = $v2 ['doinfo']; // 处理意见
						}else{
							if(abs($v ['tid']) == $v2['dotype'] && $v['tid'] < 0){
								$list [$k] ['show'] = 1; // 是否显示
								$list [$k] ['dotime'] = $v2 ['dotime']; // 处理时间
								$list [$k] ['dotype'] = $v2 ['dotype']; // 处理状态
								$list [$k] ['douser'] [] = $douser; // 处理人
								$list [$k] ['doinfo'] [] = $v2 ['doinfo']; // 处理意见
							}
						}
					}
				}
			}
		} else if ($ptmp ['ptmptid'] && ! $ptmp ['flowid']) {
			// 查询流程名称
			$pimodel = M ( 'process_info_form' );
			$where = array();
			$where['tableid'] = $id;
			$where['tablename'] = $name;
			$pname = $pimodel->where ($where)->order("id desc")->field ( 'id,name' )->find ();
			$this->assign ( 'pname', $pname ['name'] );
			
			//查询最新流程
			$pihmap = array ();
			$pihmap ['tableid'] = $id;
			$pihmap ['tablename'] = $name;
			//流程历史记录
			$pihlist = $pihmodel->where ( $pihmap )->order ( 'id desc' )->select ();
			//获取节点数据
			$process_relation_formDao = M("process_relation_form");
			$where = array();
			$where['tableid'] = $id;
			$where['tablename'] = $name;
			$where['doing'] = 1;
			$where['flowtype'] = array('gt',1);
			$relaformlist = $process_relation_formDao->where($where)->order("sort asc")->getField("relationid,flowtype,id,name,curAuditUser,parallel");
			//构造所有节点
			foreach ( $relaformlist as $k => $v ) {
				$list [$k] ['relaid'] = $v['id'];
				$list [$k] ['name'] = $v['name'];
				$list [$k] ['tid'] = $v['id'];
				$list [$k] ['flowtype'] = $v['flowtype'];
				
				//当前节点可审核人
				$curAuditUser = $v['curAuditUser'];
				if($v['parallel'] == 2){
					$curAuditUser = array();
					//多批次并行。需要解析多批次审核人员
					$countAuditUser = explode(";", $v['curAuditUser']);
					foreach($countAuditUser as $kk=>$vv){
						$countAuditUser1 =  explode(",", $vv);
						foreach($countAuditUser1 as $kkk=>$vvv){
							array_push($curAuditUser, $vvv);
						}
					}
					$curAuditUser = implode(",", $curAuditUser);
				}
				$list [$k] ['user'] = $curAuditUser;
				$list [$k] ['parallel'] = $v ["parallel"];
			}
			// 过滤回退以前的数据
			$ftrue = true;
			$lcjs = true;
			$judge = array ();
			$i = 0;
			$bool = true;
			foreach ( $pihlist as $k => $v ) {
				if($v ['dotype'] == 8 && $bool){
					//第一个就遇到变更，则不填写结束信息
					$bool = false;
				}
				if($v ['dotype'] == 4 && $bool){
					$judge [] = $v;
					$bool = false;
				}
				if ($v ['dotype'] == 1) { // 流程新建
					$judge [] = $v;
					break;
				}
				if ($ftrue && $v ['dotype'] != 4) {
					$judge [] = $v;
				}
				if ($v ['dotype'] == 2) { // 流程启动
					$ftrue = false;
				}
			}
			// 添加-4流程结束、-2流程启动、-1流程新建节点
			array_unshift ( $list, array (
					'tid' => - 1 
			), array (
					'tid' => -2 
			) );
			array_push ( $list, array (
					'tid' => - 4 
			) ); // 结束
			// 给流程填满基础信息
			foreach ( $list as $k => $v ) {
				// 添加中文名称及查看审核人链接
				if ($v ['tid'] > 0) {
					if ($v ['parallel']) {
						$list [$k] ['bx'] = "(并行)";
					}
					if($v['flowtype'] != 3){
						$list [$k] ["url"] = "__URL__/seeAuditUser/id/" . $id . "/tid/" . $v ['relaid'];
					}
					
					if ($v ['tid'] > 1356969600) {
						$list [$k] ['name'] = "加签节点";
					}else{
						$list [$k] ['name'] = $v['name'];
					}
				}else {
					if ($v ['tid'] == - 1) {
						$list [$k] ['name'] = '新建';
						$list [$k] ['dotime'] = $ptmp ["createtime"];
					} else if ($v ['tid'] == - 2) {
						$list [$k] ['name'] = '开始';
						$list [$k] ['dotype'] = '创建';
					} else if ($v ['tid'] == -4) {
						$list [$k] ['name'] = '结束';
					}
				}
				if($v ['parallel'] == 2){
					// 添加流程记录信息
					foreach ( $judge as $k2 => $v2 ) {
						$douser = $usermodel->where ( 'id=' . $v2 ['userid'] )->getField ( 'name' );
						// 判断当前节点等于流程明细的审核节点
						if ($v ['tid'] == $v2 ['ostatus'] && $v['tid'] > 0) {
							$list [$k] ['show'] = 1; // 是否显示
							$list [$k] ['dotime'] = $v2 ['dotime']; // 处理时间
							$list [$k] ['dotype'] = $v2 ['dotype']; // 处理状态
							$list [$k] ['douser'] [] = $douser; // 处理人
							$list [$k] ['doinfo'] [] = $v2 ['doinfo']; // 处理意见
						}else{
							if(abs($v ['tid']) == $v2['dotype'] && $v['tid'] < 0){
								$list [$k] ['show'] = 1; // 是否显示
								$list [$k] ['dotime'] = $v2 ['dotime']; // 处理时间
								$list [$k] ['dotype'] = $v2 ['dotype']; // 处理状态
								$list [$k] ['douser'] [] = $douser; // 处理人
								$list [$k] ['doinfo'] [] = $v2 ['doinfo']; // 处理意见
							}
						}
					}
				}else if ($v ['parallel'] == 1) {
					//添加流程记录信息
					$bxuser = explode ( ",", $v ['user'] );
					if(count($bxuser)>1){
						$doinfo = array ();
						$douser = array ();
						$dotime = $dotype =0;
						foreach ( $bxuser as $bk => $bv ) {
							// 添加流程记录信息
							foreach ( $judge as $k2 => $v2 ) {
								// 判断当前节点等于流程明细的审核节点
								if ($v ['tid'] == $v2 ['ostatus'] && $bv == $v2 ['userid']) {
									$douser[] = $usermodel->where ( 'id=' . $v2 ['userid'] )->getField ( 'name' );
									$doinfo[] = $v2 ['doinfo'];
									$dotime =  $v2 ['dotime'];
									$dotype =  $v2 ['dotype'];
								}
							}
						}
						if(count($bxuser) == count($douser)){
							$list [$k] ['show'] = 1; // 是否显示
						}
						$list [$k] ['dotime'] = $dotime; // 处理时间
						$list [$k] ['dotype'] = $dotype; // 处理状态
						$list [$k] ['doinfo'] = $doinfo; // 处理意见
						$list [$k] ['douser'] = $douser; // 处理人
					}else{
						// 添加流程记录信息
						foreach ( $judge as $k2 => $v2 ) {
							$douser = $usermodel->where ( 'id=' . $v2 ['userid'] )->getField ( 'name' );
							// 判断当前节点等于流程明细的审核节点
							if ($v ['tid'] == $v2 ['ostatus'] && $v['tid'] > 0) {
								$list [$k] ['show'] = 1; // 是否显示
								$list [$k] ['dotime'] = $v2 ['dotime']; // 处理时间
								$list [$k] ['dotype'] = $v2 ['dotype']; // 处理状态
								$list [$k] ['douser'] [] = $douser; // 处理人
								$list [$k] ['doinfo'] [] = $v2 ['doinfo']; // 处理意见
							}else{
								if(abs($v ['tid']) == $v2['dotype'] && $v['tid'] < 0){
									$list [$k] ['show'] = 1; // 是否显示
									$list [$k] ['dotime'] = $v2 ['dotime']; // 处理时间
									$list [$k] ['dotype'] = $v2 ['dotype']; // 处理状态
									$list [$k] ['douser'] [] = $douser; // 处理人
									$list [$k] ['doinfo'] [] = $v2 ['doinfo']; // 处理意见
								}
							}
						}
					}
				} else {
					// 添加流程记录信息
					foreach ( $judge as $k2 => $v2 ) {
						$douser = $usermodel->where ( 'id=' . $v2 ['userid'] )->getField ( 'name' );
						// 判断当前节点等于流程明细的审核节点
						if ($v ['tid'] == $v2 ['ostatus'] && $v['tid'] > 0) {
							$list [$k] ['show'] = 1; // 是否显示
							$list [$k] ['dotime'] = $v2 ['dotime']; // 处理时间
							$list [$k] ['dotype'] = $v2 ['dotype']; // 处理状态
							$list [$k] ['douser'] [] = $douser; // 处理人
							$list [$k] ['doinfo'] [] = $v2 ['doinfo']; // 处理意见
						}else{
							if(abs($v ['tid']) == $v2['dotype'] && $v['tid'] < 0){
								$list [$k] ['show'] = 1; // 是否显示
								$list [$k] ['dotime'] = $v2 ['dotime']; // 处理时间
								$list [$k] ['dotype'] = $v2 ['dotype']; // 处理状态
								$list [$k] ['douser'] [] = $douser; // 处理人
								$list [$k] ['doinfo'] [] = $v2 ['doinfo']; // 处理意见
							}
						}
					}
				}
			}
		}
		// 构造节点处理等待时间
		foreach ( $list as $k => $v ) {
			// 判断处理时间是否存在
			if ($list [$k] ['dotime']) {
				$next = $k + 1; // 取到下个审核节点
				              // 判断下个节点是否小于所有节点之和
				if ($next < count ( $list )) {
					$nexttime = $list [$next] ['dotime']; // 下个节点处理时间
					// 判断下个节点时间是否存在
					if (! $nexttime) {
						$nexttime = time (); // 等于当前时间
					}
					// 下个节点时间减去当前节点时间获取小时数
					$hours = number_format ( ($nexttime - $list [$k] ['dotime']) / 3600 );
					// 判断小时数是否大于一天
					if ($hours > 24) {
						// 大于一天的 用填进行计算
						$list [$k] ['days'] = round ( number_format ( $hours ) / 24 ) . '天';
					} else {
						$list [$k] ['days'] = number_format ( $hours ) . '小时';
					}
				} else {
					$list [$k] ['days'] = $list [$k - 1] ['days'];
				}
				$list [$k] ['dotime'] = transTime ( $v ['dotime']);
			} else {
				$list [$k] ['days'] = "&nbsp;";
			}
		}
		$this->assign ( 'list', $list );
		$orderno = "";
		if ($ptmp ['orderno']) {
			$orderno = $ptmp ['orderno'];
		} else {
			$orderno = $ptmp ['code'];
		}
		$this->assign ( 'orderno', $orderno );
		$this->display ( 'Public:process' );
	}
	/**
	 * @Title: seeAuditUser
	 * @Description: todo(查看审核人)
	 * @author 杨东
	 * @date 2013-4-17 上午10:50:46
	 * @throws
	 *
	 */
	public function seeAuditUser() {
		//获取审核节点ID
		$tid = $this->escapeStr ( $_GET ['tid'] );
		//查询节点审核人信息
		$process_relation_formDao = M("process_relation_form");
		$where = array();
		$where['id'] = $tid;
		$list = $process_relation_formDao->where($where)->field("auditUser,parallel")->find();
		
		//当前节点可审核人
		$curAuditUser = $list['auditUser'];
		if($list['parallel'] == 2){
			$curAuditUser = array();
			//多批次并行。需要解析多批次审核人员
			$countAuditUser = explode(";", $list['auditUser']);
			foreach($countAuditUser as $kk=>$vv){
				$countAuditUser1 =  explode(",", $vv);
				foreach($countAuditUser1 as $kkk=>$vvv){
					array_push($curAuditUser, $vvv);
				}
			}
			$curAuditUser = implode(",", $curAuditUser);
		}
	 	$userArr = explode(",", $curAuditUser);
		$MisHrPersonnelUserDeptViewModel=D("MisHrPersonnelUserDeptView");
		$infolist = $MisHrPersonnelUserDeptViewModel->getUserInfoList($userArr);
		$this->assign ( "list", $infolist );
		$this->display ( 'SeeProcessDetail:seeAuditUser' );
	}
	
	/**
	 * @Title: lookupSelectAuditOption
	 * @Description: 用ztree形式查询出所有部门员工信息 作用范围为流程审核人自选时，启动流程选审核的的公用方法 到时候新的流程方法出来，这个方法将被弃用
	 * @author liminggang
	 * @date 2014-9-12 下午4:45:17
	 * @throws
	 *
	 */
	public function lookupSelectAuditOption() {
		// 查询公司信息，将公司封装到部门上面
		// 第一步，构造部门结构树。组合公司一起。
		$MisSystemCompanyDao = M ( "mis_system_company" );
		$where = array ();
		$where ['status'] = 1;
		$companylist = $MisSystemCompanyDao->where ( $where )->select ();
		
		$mname = $_REQUEST ["modulename_auditoption"] ? $_REQUEST ["modulename_auditoption"] : $this->getActionName ();
		$model = M ( 'mis_system_department' );
		$deptlist = $model->where ( "status=1" )->order ( "id asc" )->select ();
		$alldeptid = $deptlist;
		$param ['rel'] = "auditOpionBox";
		$param ['url'] = "__URL__/lookupSelectAuditOption/modulename_auditoption/" . $mname . "/jump/1/deptid/#id#/parentid/#parentid#";
		$jump = $_REQUEST ['jump'];
		$role = $_REQUEST ["role"]; // 审核部门
		$this->assign ( "role", $role );
		$duty = $_REQUEST ["duty"]; // 审核职级
		$this->assign ( "duty", $duty );
		$mapdepid = array ();
		if ($role) {
			$rolearr = explode ( ",", $role );
			$map = $allup = array ();
			$map ["id"] = array (
					"in",
					$rolearr 
			);
			$map ["status"] = 1;
			$rolearr = $model->where ( $map )->field ( "id,parentid" )->select ();
			$up = $down = "";
			foreach ( $rolearr as $k => $v ) {
				$up .= $this->upAllParentid ( $deptlist, $v ['id'], $v ['parentid'] );
				$down .= $this->downAllChildren ( $deptlist, $v ["id"] );
			}
			if ($up) {
				$up = explode ( ",", $up );
				array_shift ( $up );
				$up = array_unique ( $up );
			}
			if ($down) {
				$down = explode ( ",", $down );
				array_shift ( $down );
				$down = array_unique ( $down );
			}
			if ($down || $up) { // 过滤管理员文件的id
				$mapdepid = array_merge ( $down, $up );
				$map = array ();
				$map ["id"] = array (
						"in",
						$mapdepid 
				);
				$map ["status"] = 1;
				$deptlist = $model->where ( $map )->order ( "id asc" )->select ();
			}
			$this->assign ( 'role', $role );
			if ($duty) {
				$param ['url'] = "__URL__/lookupSelectAuditOption/modulename_auditoption/" . $mname . "/jump/1/deptid/#id#/parentid/#parentid#/duty/" . $duty . "/role/" . $role;
			}
		}
		if (! $jump) {
			$typeTree = $this->getTree ( $deptlist, $param, array (), $companylist );
			$this->assign ( 'tree', $typeTree );
		}
		
		$map = array ();
		$searchby = str_replace ( "-", ".", $_POST ["searchby"] );
		$keyword = $_POST ["keyword"];
		$searchtype = $_POST ['searchtype'];
		if ($_POST ["keyword"]) {
			$map [$searchby] = ($searchtype == 2) ? array (
					'like',
					'%' . $keyword . '%' 
			) : $keyword;
			$this->assign ( 'keyword', $keyword );
			$searchby = $_POST ["searchby"];
			$this->assign ( 'searchby', $searchby );
			$this->assign ( 'searchtype', $searchtype );
		}
		$searchby = array (
				array (
						"id" => "user-name",
						"val" => "按员工姓名" 
				),
				array (
						"id" => "orderno",
						"val" => "按员工编号" 
				) 
		);
		$searchtype = array (
				array (
						"id" => "2",
						"val" => "模糊查找" 
				),
				array (
						"id" => "1",
						"val" => "精确查找" 
				) 
		);
		$this->assign ( "searchbylist", $searchby );
		$this->assign ( "searchtypelist", $searchtype );
		$map ['workstatus'] = 1; // 排除非转正人员
		$map ['user.status'] = 1; // 排除非正常用户
		$map ['working'] = 1; // 排除离职的人员
		                   
		// 通过搜索或者点击树传过来的部门id
		if ($_GET ["deptid"]) {
			$mapdepid = $this->downAllChildren ( $alldeptid, $_GET ["deptid"] );
			if ($mapdepid) {
				$mapdepid = explode ( ",", $mapdepid );
				array_shift ( $mapdepid );
				$mapdepid = array_unique ( $mapdepid );
				if ($down) {
					$mapdepid = array_intersect ( $mapdepid, $down );
				}
			}
			$this->assign ( 'deptid', $_GET ["deptid"] );
		}
		
		if ($mapdepid)
			$map ['mis_system_department.deptid'] = array (
					' in ',
					$mapdepid 
			);
		
		if ($duty) {
			$userdeptdutymodel = M ( "user_dept_duty" );
			$dutyarr = explode ( ",", $duty );
			$usermap = array ();
			$usermap ['status'] = 1;
			$usermap ["dutyid"] = array (
					"in",
					$dutyarr 
			);
			$uarr = $userdeptdutymodel->where ( $usermap )->getField ( "id,userid", true );
			if ($uarr) {
				$map ['user.id'] = array (
						' in ',
						$uarr 
				);
			}
			$this->assign ( 'duty', $duty );
		}
		$common = A ( "Common" );
		$common->_list ( 'MisHrPersonnelPersonInfoView', $map );
		$this->assign ( 'modulename_auditoption', $mname );
		$this->assign ( 'audittype', $_REQUEST ["audittype"] );
		if ($jump) {
			$this->display ( "Public:lookupSelectAuditOptionRight" );
		} else {
			$this->display ( "Public:lookupSelectAuditOption" );
		}
	}
	/**
	 * @Title: auditremindmessage
	 * @Description: todo(审核或打回都发送系统消息)
	 * 
	 * @param
	 *        	int 接收人姓名 $receiveterNameId 接收人姓名
	 * @param
	 *        	string 单据编号 $orderno 单据编号
	 * @param
	 *        	string 对应model名称 $modelname 对应的model名称
	 * @param int $notation
	 *        	1表示审核，2表示打回，可扩展
	 * @param int $id
	 *        	当前这条数据的id
	 * @author xiafengqin
	 *         @date 2014-2-26 下午6:06:38
	 * @throws
	 *
	 */
	private function auditremindmessage($receiveterNameId, $orderno = '', $modelname, $notation, $id) {
		// 审核意见
		if ($_POST ['doinfo']) {
			$doinfo = $_POST ['doinfo'];
		} else {
			$doinfo = '无';
		}
		// 判断$notation是审核还是打回
		switch ($notation) {
			case 1 :
				$notationName = '审核';
				break;
			case 2 :
				$notationName = '打回';
				break;
			default :
				$notationName = '审核';
				break;
		}
		$nodeModel = M ( 'node' );
		// 通过modelname查找到对应的模块中文名
		$modelNameChinese = $nodeModel->where ( "name='" . $modelname . "'" )->getField ( "title" );
		// 接收人的名称
		$receiveterName = getFieldBy ( $receiveterNameId, 'id', 'name', 'user' );
		// 审核或打回人
		$auditName = getFieldBy ( $_SESSION [C ( 'USER_AUTH_KEY' )], 'id', 'name', 'user' );
		// 发送的系统日志的标题
		$messageTitle = '您的工作  ' . $modelNameChinese . ' 单据号为  ' . $orderno . ' 已经被' . $notationName . '！';
		// 加载配置文件 判断是否是dialog打开方式
		$dialogList = require DConfig_PATH . "/System/dialogconfig.inc.php";
		$target = 'target=navTab';
		foreach ( $dialogList as $k => $v ) {
			if ($modelname == $v ['tablename']) {
				$target = 'target=dialog width="' . $v ['width'] . '" height="' . $v ['height'] . '" mask="true"';
			}
		}
		// 系统日志的内容
		$messageContent = '';
		$messageContent .= '
				<p></p>
				<span></span>
				<div style="width:98%;">
				<p class="font_darkblue">' . $receiveterName . '，您好！</p>
						<p>&nbsp;&nbsp;&nbsp;&nbsp;<strong>' . $auditName . ' </strong> ' . $notationName . '了 ' . $modelNameChinese . ' 系统下的  ' . $orderno . ' 单据,请查看。</p>
								<p>&nbsp;&nbsp;&nbsp;&nbsp;单据的详细情况：</p>
								<ul>
								<li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>单据系统：</strong>' . $modelNameChinese . '
										</li>
										<li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>单据编号：</strong><a class="edit" style="text-decoration:underline" title="' . $modelNameChinese . '_查看" ' . $target . ' rel="' . $modelname . 'auditView" href="__APP__/' . $modelname . '/auditView/id/' . $id . '">' . $orderno . '</a>
												</li>
												<li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>审核时间：</strong>' . date ( 'Y-m-d', time () ) . '
														</li>
														<li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>审核意见：</strong>' . $doinfo . '
																</li>
																</ul>
																<p>&nbsp;&nbsp;&nbsp;&nbsp;如果您有任何问题，请联系' . $auditName . '。</p>
																		</div>';
		// 系统推送消息
		$this->pushMessage ( array (
				$receiveterNameId 
		), $messageTitle, $messageContent );
	}
	
	/**
	 * @Title: checkSubProcess
	 * @Description: todo(核验订单是否能启动或审核)
	 * 
	 * @author yangxi
	 * @param
	 *        	eter submodel 明细模型名
	 * @param
	 *        	eter submodel 明细模型名
	 * @param
	 *        	eter type 类型定义 Sales销售 Purchase采购 Invent仓储 delivery货运 Finance财务
	 *        	@date 2013-9-29 上午11:29:27
	 * @throws error
	 */
	public function checkSubProcess($submodel, $masid, $type) {
		switch ($type) {
			case "Invent" :
				$this->__checkSubProcess ( $submodel, $masid, $type = 'Invent', $checkStock = true, $checkAdjust = true );
				break;
			case "Stock" :
				$this->__checkSubProcess ( $submodel, $masid, $type = 'Stock', $checkStock = false, $checkAdjust = true );
				break;
			case "Finance" :
				$this->__checkSubProcess ( $submodel, $masid, $type = 'Finance', $checkStock = true, $checkAdjust = true );
				break;
			default :
				$this->__checkSubProcess ( $submodel, $masid, $type = 'Sales', $checkStock = false, $checkAdjust = false );
				break;
		}
	}
	/**
	 * @Title: workNoticeMessages
	 * @Description: 发送信息给知会人和执行人方法
	 * 
	 * @param string $informpersonid
	 *        	知会人ID
	 * @param string $executorid
	 *        	执行人ID
	 * @param string $modelname
	 *        	对应的model名称
	 * @param int $tableid
	 *        	单据编号id
	 * @param int $noticeType
	 *        	1表示知会，2表示执行，可扩展
	 * @author yangxi
	 *         @date 2014-2-26 下午6:06:38
	 * @throws
	 *
	 */
	private function workNoticeMessages($informpersonid, $executorid, $modelname, $tableid) {
		if ($informpersonid) {
			$sendidArr = array ();
			$sendidArr = explode ( ",", $informpersonid );
			// 插入
			$this->workNoticeMessage ( $sendidArr, $modelname, $tableid, 1 );
		}
		if ($executorid) {
			$sendidArr = array ();
			$sendidArr = explode ( ",", $executorid );
			// 插入
			$this->workNoticeMessage ( $sendidArr, $modelname, $tableid, 2 );
		}
	}
	
	function lookupWorkMisNotify($informpersonid,$modelname, $tableid,$messagetype){
		if ($informpersonid) {
			$sendidArr = array ();
			$sendidArr = explode ( ",", $informpersonid );
			$nodeModel = M('node');
			//通过modelname查找到对应的模块中文名
			$modelNameChinese = $nodeModel->where("name='".$modelname."'")->getField("title");
			//通过modelname查找到对应的单据编号
			$orderInfo = D($modelname)->where("id='".$tableid."'")->field("orderno")->find();
			$orderno=$orderInfo['orderno'];
			$noticeTypeName="工作知会：";
			if($messagetype==1){
				$messageTitle =$noticeTypeName.$modelNameChinese.' 单据号为  '.$orderno.' 的单据已经审批完成，请关注！';
			}elseif ($messagetype==2){
				$messageTitle = '您的工作  ' . $modelNameChinese . ' 单据号为  ' . $orderno . ' 已经被打回！';
			}
			//系统推送消息
			if(!is_array($sendidArr)){
				$sendidArr=array($sendidArr);
			}
			//生成发件人名字符串
			$modelUser = D('User');
			$condition = array('id'=>array('in', $sendidArr) );
			$nameArray = $modelUser->where($condition)->getField('id,name');
			
			//名字，字符串
			$recipientNameString = implode(",",$nameArray);
			
			//接收人，ID字符串
			$recipientListIDString = implode(',',$sendidArr);
			 $notifyModel=D('MisNotify');
			//数据区
			$data['title'] = $messageTitle;
			$data['recipient'] = $recipientListIDString;
			$data['recipientname'] = $recipientNameString;
			$data['createid'] = 0;
			$data['createtime'] =  time();
			$data['status'] = 1;
			$data['type'] = $messagetype;
			$data['orderno'] = $orderno;
			$data['tableid'] = $tableid;
			$data['modelname'] = $modelname;
			$data['name'] = $modelNameChinese;
			$notifyModel->add($data);
		}
	}
	/**
	 * @Title: getFilterAudit
	 * @Description: todo(获取对应流程的对应节点上面过滤的用户)
	 * 
	 * @author wangcheng
	 * @param eter $pid
	 *        	流程id
	 * @param eter $tid
	 *        	节点id
	 * @param eter $useridarr
	 *        	当前节点可审核人员
	 * @param eter $vo
	 *        	当前数据
	 *        	@date 2013-11-26 上午15:52
	 * @throws error
	 */
	public function getFilterAudit($pid, $tid, $useridarr, $vo = array()) {
		if (is_array ( $useridarr )) {
			$useridarr = implode ( ",", $useridarr );
		}
		if ($pid != "" && $tid != "" && $useridarr != "") {
			$model = M ( "process_relation" );
			$map = array ();
			$map ["pid"] = $pid;
			$map ["tid"] = $tid;
			$nodeinfo = $model->where ( $map )->find ();
			if ($nodeinfo ["filteruid"]) {
				$arr = unserialize ( $nodeinfo ["filteruid"] );
				// 获取当前审核人员 和 对应流程节点 的过滤用户条件 取交集
				$data = array ();
				foreach ( $arr ['func'] as $k3 => $v3 ) {
					$filter_userid = getConfigFunction ( array (), $v3, $arr ['funcdata'] [$k3], $vo, false );
				}
				if ($filter_userid) {
					if (! is_array ( $filter_userid )) {
						$filter_userid = explode ( ",", $filter_userid );
					}
					$userarr2 = explode ( ",", $useridarr );
					$useridarr = array_intersect ( $userarr2, $filter_userid );
					if (count ( $useridarr ) == 0) {
						$this->error ( "流程的第 " . ($nodeinfo ["sort"] + 1) . " 个节点在获取交集用户时为空" );
					} /*
					   * else{
					   * $useridarr = implode(",",$useridarr);
					   * }
					   */
				} else {
					$this->error ( "流程的第 " . ($nodeinfo ["sort"] + 1) . " 个节点在查询过滤用户时为空" );
				}
			}
		}
		if (! is_array ( $useridarr )) {
			$useridarr = explode ( ",", $useridarr );
		}
		return $useridarr;
	}
}
?>
