<?php
/**
 * @Title: 行政管理
 * @Package package_name 
 * @Description: todo( 后勤维护记录 ) 
 * @author eagle 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-4-18 下午5:47:22 
 * @version V1.0
 */
class MisHrPersonnelAddressBookAction extends CommonAction {
	/* (non-PHPdoc) 过滤条件
	 * @see CommonAction::index()								
	*/
	public function _filter(&$map){
		if ($_SESSION["a"] != 1)$map['status']=array("gt",-1);
	}
	//定义左侧结构树内容
	private $arr=array(
			array(
					'id' =>1,
					'name'=>'管理模块树结构',
					'pId'=>0,
					'title'=>'管理模块树结构',
					'open'=>true,
			),
			array(
					'id' =>2,
					'name'=>'员工列表',
					'pId'=>1,
					'title'=>'员工列表',
					'rel' =>'mishrpersonneladdressbookmodel',
					'target'=>'ajax',
					'type' =>'post',
					'url'=>'__URL__/index/type/1/jump/1/md/MisHrPersonnelAddressBook',
			),
			array(
					'id' =>3,
					'name'=>'分公司列表',
					'pId'=>1,
					'title'=>'分公司列表',
					'rel' =>'mishrpersonneladdressbookmodel',
					'target'=>'ajax',
					'type' =>'post',
					'url'=>'__URL__/index/type/3/jump/1/md/MisSystemCompany',
			),
	);
	
	/* (non-PHPdoc) 显示列表
	 * @see CommonAction::index()								
	*/
	function index(){
		//第一步，组装左侧树结构，默认为员工列表
		$name=$_REQUEST['md']?$_REQUEST['md']:'MisHrPersonnelAddressBook';
		$this->assign('typetree', json_encode($this->arr));
		$this->assign('md',$name);
		
		//根据获得模型名称。获取查询条件
		$map=$this->_search($name);
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		//动态配置显示字段
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
		
		//去_list方法中把数据取出来
		if (! empty ( $name )) {
			$qx_name=$name;
			if(substr($name, -4)=="View"){
				$qx_name = substr($name,0, -4);
			}
			//验证浏览及权限
			if( !isset($_SESSION['a']) ){
				$map=D('User')->getAccessfilter($qx_name,$map);	
			}
			$this->_list ($name, $map );
		}
		//引列表数据，通用模板
		if( intval($_POST['dwzloadhtml']) ){
			$this->display("dwzloadindex");exit;
		}
		
		//初次打开模块时， 显整个框架， 在左侧树型结构上点击在当前页面加载right模板
		if ($_REQUEST['jump'] ) {
			$type=$_REQUEST['type'];
			if($type==3){
				$this->display('showSystemCompany');
			}else if($type==2){
				$this->display('showProject');
			}else{
				$this->display('right');
			}
		} else {
			$this->display();
		}
	}
	
	/**
	 * @Title: add_project
	 * @Description: todo(添加项目)
	 * @author liminggang
	 * @throws
	 */
	public function add_project(){
		//读出项目类型，下拉菜单数据
		$this->_before_common(); 
		$this->display();
	}
	/**
	 * @Title: insert_project
	 * @Description: todo(添加)
	 * @author liminggang
	 * @throws
	 */
	function insert_project() {
		//B('FilterString');
		$name="MisSalesProject";
		$model = D ($name);
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		//保存当前数据对象
		$list=$model->add ();
		if ($list!==false) {
			$mrdmodel = D('MisRuntimeData');
			$mrdmodel->setRuntimeCache($_POST,$name,'add');
			$module2=A($name);
			if (method_exists($module2,"_after_insert")) {
				call_user_func(array(&$module2,"_after_insert"),$list);
			}
			$this->success ( L('_SUCCESS_') ,'',$list);
			exit;
		} else {
			$this->error ( L('_ERROR_') );
		}
	}
	/**
	 * @Title: edit_project
	 * @Description: todo(更新)
	 * @author liminggang
	 * @throws
	 */
	public function edit_project(){
		//读出项目类型，下拉菜单数据
		$this->_before_common(); 

		$name="MisSalesProject";
		$model = D ( $name );
		$id = $_REQUEST [$model->getPk ()];
		$map['id']=$id;
		if ($_SESSION["a"] != 1) $map['status'] = 1;
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
		$subdetailList = $scdmodel->getSubDetail($modelname);
		if ($subdetailList) {
			$this->assign ( 'subdetailList', $subdetailList );
		}
		//扩展工具栏操作
		$toolbarextension = $scdmodel->getSubDetail($modelname,true,'toolbar');
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}

		//判断系统授权
		//$vo=$this->process_filter($vo);
		//lookup带参数查询
		$module=A($name);
		if (method_exists($module,"_after_edit")) {
			call_user_func(array(&$module,"_after_edit"),&$vo);
		}
		$this->assign( 'vo', $vo );
		$this->display ();
	}

	/**
	 * @Title: update_project
	 * @Description: todo(更新)
	 * @author liminggang
	 * @throws
	 */
	function update_project() {
		//B('FilterString');
		$name="MisSalesProject";
		$model = D ( $name );
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$list=$model->save ();
		if (false !== $list) {
			$module2=A($name);
			if (method_exists($module2,"_after_update")) {
				call_user_func(array(&$module2,"_after_update"),$list);
			}
			$this->success ( L('_SUCCESS_'));
		} else {
			$this->error ( L('_ERROR_') );
		}
	}
	/**
	 * @Title: addBaseInfoForm
	 * @Description: todo(基本表单显示)
	 * @author liminggang
	 * @throws
	 */
	public function edit($html){
		//自动生成员工基本资料编号
		$scnmodel = D('SystemConfigNumber');
		$orderno = $scnmodel->GetRulesNO('mis_hr_personnel_person_info');
		$this->assign("orderno",$orderno);
		//订单号可写
		$writable= $scnmodel->GetWritable('mis_hr_personnel_person_info');
		$this->assign("writable",$writable);
		//附件信息表
		//$this->assign("upload_path", date("Y/m/d",time())."/".$_SESSION[C('USER_AUTH_KEY')]);

		//查询所有部门信息
		$deptmodel = D("MisSystemDepartment");
		$deptlist=$deptmodel->where("status=1")->select();
		//$this->assign("deptlist",$deptlist);
		//所属公司信息
		$MisSystemCompanyDAO=M('mis_system_company');
		$companylist=$MisSystemCompanyDAO->where('status = 1')->field('id,name')->select();
		$this->assign('companylist',$companylist);
		
		
		//查询职级信息
		$deptleval = D("Duty");
		$deptlevellist=$deptleval->where('status=1')->select();
		$this->assign("deptlevellist",$deptlevellist);
		$this->assign('time',time());
		/*
		 * 如果是选择了部门新增的情况。直接选中部门
		* 得到选中的部门
		*/
		$deptid=$this->escapeChar($_REQUEST['deptid']);
		if($deptid){
			$this->assign("deptid",$deptid);
		}
		$deptlists=$this->selectTree($deptlist,0,0,$deptid);
		$this->assign("deptlist",$deptlists);

		//判断是否是修改方法过来的 //用64位加密方式加密ID
		$personinfoid=base64_encode($_REQUEST['id']);
		if($personinfoid){
			$name=$this->getActionName();
			$model = D ( $name );
			$id = $_REQUEST [$model->getPk ()];
			$map['id']=$id;
			$map['status']=1;
			$vo = $model->where($map)->find();
			$this->assign('vo', $vo );
			$deptlists=$this->selectTree($deptlist,0,0,$vo['deptid']);
			$this->assign("deptlist",$deptlists);
		}
		$this->assign("personinfoid",$personinfoid);
		$this->display($html);
		exit;
	}
	/**
	 +----------------------------------------------------------
	 * 默认删除操作
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 * @throws ThinkExecption
	 +----------------------------------------------------------
	 */	
	public function delete_project(){
		$this->delete('MisSalesProject');
	}
	/**
	 +----------------------------------------------------------
	 * 默认删除操作
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 * @throws ThinkExecption
	 +----------------------------------------------------------
	 */	
	public function delete_company(){
		$this->delete('MisSystemCompany');
	}
	/**
	 +----------------------------------------------------------
	 * 默认删除操作
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 * @throws ThinkExecption
	 +----------------------------------------------------------
	 */
	public function delete($name='') {
		//删除指定记录
		if(empty($name)){
			$name=$this->getActionName();
		}
		$model = D ($name);
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				$list=$model->where ( $condition )->setField ( 'status', - 1 );
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

	/**
	 *
	 * @Title: lookupmanage
	 * @Description: todo(用ztree形式查询出所有部门员工信息)
	 * @author liminggang
	 * @throws
	 */
	public function lookupmanage(){
		//组装tree
		$model= M('mis_sales_site');
		$deptlist = $model->order("id asc")->select();
		//dump($deptlist);

		$typeTree = $this->getTree($deptlist);
		$this->assign('tree',$typeTree);
		
		//查询条件
		$map = array();
		$searchby = str_replace("-", ".", $_POST["searchby"]);
		$keyword=$this->escapeChar($_POST["keyword"]);
		$searchtype = $_POST['searchtype'];
		//关键字是否为空
		if($_POST["keyword"]){
			if($searchby=='user_id'){
				$modelHPPI=M('mis_hr_personnel_person_info');
				$map2['name'] = ($searchtype==2)  ? array('like','%'.$keyword.'%'):$keyword;
				$userIDList=$modelHPPI->where($map2)->getField('id,name');
				$map['user_id'] = array('in',array_keys($userIDList));
			}else{
				$map[$searchby] = ($searchtype==2)  ? array('like','%'.$keyword.'%'):$keyword;
			}
						
			$this->assign('keyword',$keyword);
			$searchby = str_replace(".", "-", $_POST["searchby"]);
			$this->assign('searchby',$searchby);
			$this->assign('searchtype',$searchtype);
		}
		//搜索按哪个类型
		$searchby=array(
				array("id" =>"name","val"=>"项目名"),
				array("id" =>"code","val"=>"销售项目编号"),
		);
		$this->assign("searchbylist",$searchby);
		//模糊查询还是精确查询
		$searchtype=array(array("id" =>"2","val"=>"模糊查找"),array("id" =>"1","val"=>"精确查找"));
		$this->assign("searchtypelist",$searchtype);
		
		$deptid		= $_REQUEST['deptid'];
		if ($deptid) {
			$map['siteid']=$deptid;
		}
		$this->_list('MisSalesProject',$map);
		$this->assign('deptid',$deptid);		
		if ($_REQUEST['jump']) {
			$this->display('lookupmanagelist'); //如果jump=1 ; 那么是刷新右侧数据区
		} else {
			$this->display("lookupmanage"); //如果jump= 空 ; 第一弹出窗口
		}
	}
	
	/**
	 * 构造树形节点  @changeby wangcheng
	 * @param array  $alldata  构造树的数据
	 * @param array  $param    传入数组参数，包含rel，url【url中参数传递用#name#形式，name 为字段名字】
	 * @param array  $returnarr  初始化s树形节点，可以传入数字，默认选中哪一行数据
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
			$param['rel']="mishrpersonneladdressbookBox";  //重要的地方，查找带回模板上的ID
			//$param['url']="__URL__/lookupmanage/jump/1/deptid/#id#/parentid/#parentid#";
			$url = str_replace($matches[0],$matches[1],$param['url']);
			$newv['id']=$v['id'];
			$newv['pId']=$v['parentid']?$v['parentid']:0;
			$newv['type']='post';
			if($v['id']==40){
				$newv['url']="__URL__/lookupmanage/jump/1";  //项级节点，显示所有记录不要传具体行记录ID
			}else{
				$newv['url']="__URL__/lookupmanage/jump/1/deptid/".$v['id']."/parentid/".$v['parentid'];
			}
			//$newv['url']=$url;
			$newv['target']='ajax';
			$newv['rel']=$param['rel'];
			$newv['title']=$v['name']; //光标提示信息
			$newv['name']=missubstr($v['name'],18,true); //结点名字，太多会被截取,针对于现在的ZTREE，宽度不能超过18个字符。
			if($v['parentid']==40){   //顶级结点，默认展开它
				$newv['open']=$v['open'] ? $v['open']:'true';
			}

			array_push($returnarr,$newv);
		}
		return json_encode($returnarr);
	}
	
	
	/**
	 * @Title: _before_common 
	 * @Description: todo(新增和修改之前的公共方法)   
	 * @author xiafengqin 
	 * @date 2013-6-1 上午9:43:21 
	 * @throws
	 */
	public function _before_common(){
		//获取项目类型
		$model = D('MisOrderTypes');
		$tlist = $model->where("type='01' and status = 1")->select();
		$this->assign("tlist", $tlist);
		
	}
	
	//新增新公司
	public function add_company(){
		$this->getSystemConfigDetail("MisSystemCompany");
		$this->display();
	}
	//编辑新公司
	public function edit_company(){
		$modelname=$this->getSystemConfigDetail("MisSystemCompany");
		$model = D ( $modelname );
		$id = $_REQUEST [$model->getPk ()];
		$map['id']=$id;
		if ($_SESSION["a"] != 1) $map['status'] = 1;
		$vo = $model->where($map)->find();

		if(empty($vo)){
			$this->display ("Public:404");
			exit;
		}

		//判断系统授权
		$module=A($modelname);
		if (method_exists($module,"_after_edit")) {
			call_user_func(array(&$module,"_after_edit"),&$vo);
		}
		$this->assign( 'vo', $vo );
		$this->display();
	}
	
		/**
	 * @Title: update_project
	 * @Description: todo(更新)
	 * @author liminggang
	 * @throws
	 */
	function update_company() {
		//B('FilterString');
		$name="MisSystemCompany";
		$model = D ( $name );
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$list=$model->save ();
		if (false !== $list) {
			$this->success ( L('_SUCCESS_'));
		} else {
			$this->error ( L('_ERROR_') );
		}
	}
	
}
?>