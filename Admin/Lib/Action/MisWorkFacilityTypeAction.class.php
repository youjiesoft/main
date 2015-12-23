<?php
/**
 * @Title: file_name
 * @Package package_name
 * @Description: todo(用一句话描述该文件做什么)
 * @author libo
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-11-25 上午10:41:23
 * @version V1.0
 */
class MisWorkFacilityTypeAction extends CommonAction {
	public function _filter(&$map) {
		//列表过滤器，生成查询Map对象
		if(!isset($_SESSION['a'])){
			$map['status']=1;
		}
	}
	/**
	 * (non-PHPdoc)
	 * @see CommonAction::index()
	 */
	public function index(){
		//第一步、先生成左侧结构树
		$this->getLeaveTree();
		//第二步、默认查询设备类型
		$name=$_REQUEST['md']?$_REQUEST['md']:"MisWorkFacilityType";
		//列表过滤器，生成查询Map对象
		$map = $this->_search ($name);
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		if($_REQUEST['md']=='MisProductUnit'){
			$map['typeid']=1;
		}else{
			if($_REQUEST['pid']){
				$map['pid']=$_REQUEST['pid'];
				$this->assign("pid",$map['pid']);
			}else{
				$MisWorkFacilityTypeModel=D($name);
				$MisWorkFacilityTypeMap['status']=1;
				$MisWorkFacilityTypeMap['pid']=0; //顶级节点
				$MisWorkFacilityTypeList=$MisWorkFacilityTypeModel->where($MisWorkFacilityTypeMap)->getField("id,name");
				$map['pid']=array('in',array_keys($MisWorkFacilityTypeList));
			}
		}
		//验证浏览及权限
		if( !isset($_SESSION['a']) ){
			$map=D('User')->getAccessfilter($qx_name,$map);
		}
		$this->_list ( $name, $map );

		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($name);
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		//扩展工具栏操作
		$toolbarextension = $scdmodel->getDetail($name,true,'toolbar');
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		$jump=$_REQUEST['jump'];
		if($_REQUEST['jump']){
			if($jump == 2){
				$this->display("workfacilitytype");
				exit;
			}else{
				$this->display("productunit");
				exit;
			}
		}
		$this->display('index');
	}
	function insert() {
		//B('FilterString');
		$name=$_POST['md'];
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
	function update() {
		//B('FilterString');
		$name=$_POST['md'];
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
	function edit() {
		$name=$_REQUEST['mdtype'];
		$model = D ( $name );
		$id = $_REQUEST [$model->getPk ()];
		$map['id']=$id;
		if ($_SESSION["a"] != 1) $map['status'] = 1;
		$vo = $model->where($map)->find();
		if(empty($vo)){
			$this->display ("Public:404");
			exit;
		}
		$this->assign( 'vo', $vo );
		//读取动态配制
		$this->getSystemConfigDetail($name);
		//扩展工具栏操作
		$scdmodel = D('SystemConfigDetail');
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
		if($name =="MisWorkFacilityType"){
			//查询设备分类Pid为0
			$MisWorkFacilityTypeModel=D('MisWorkFacilityType');
			$MisWorkFacilityTypeMap['status']=1;
			$MisWorkFacilityTypeMap['pid']=0; //顶级节点
			$MisWorkFacilityTypeList=$MisWorkFacilityTypeModel->where($MisWorkFacilityTypeMap)->getField("id,name");
			$this->assign("MisWorkFacilityTypeList",$MisWorkFacilityTypeList);
			$this->display ();
		}else{
			$model = D("MisProductUnittype");
			$map2['status']= 1;
			$map2['unittypeid']=1;
			$typeList = $model->where($map2)->select();
			$this->assign('typelist',$typeList);
			$select_config = require DConfig_PATH."/System/selectlist.inc.php";
			$select_config = $select_config['unittype']['unittype'];
			$this->assign('unittypelist',$select_config);
			$this->display('editunit');
		}
	}
	public function add() {
		$modelname=$_REQUEST['mdtype'];
		$this->getSystemConfigDetail($modelname);
		$model = D($modelname);
		$qx_name = $modelname;
		if(substr($modelname, -4)=="View"){
			$qx_name = substr($modelname,0, -4);
		}
		if( !isset($_SESSION['a']) ){
			if( $_SESSION[strtolower($qx_name.'_indexview')]==2 ){////判断部门及子部门权限
				$map["createid"]=array("in",$_SESSION['user_dep_all_child']);
			}else if($_SESSION[strtolower($qx_name.'_indexview')]==3){//判断部门权限
				$map["createid"]=array("in",$_SESSION['user_dep_all_self']);
			}else if($_SESSION[strtolower($qx_name.'_indexview')]==4){//判断个人权限
				$map["createid"]=$_SESSION[C('USER_AUTH_KEY')];
			}
		}

		$map['status']=1;
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}

		// 上一条数据ID
		$updataid = $model->where($map)->order('id desc')->getField('id');
		$this->assign("updataid",$updataid);
		$mrdmodel = D('MisRuntimeData');
		$data = $mrdmodel->getRuntimeCache($modelname,'add');
		$this->assign("vo",$data);
		//此处添加_after_add方法，方便在方法中，判断跳转
		$module2 = A($modelname);
		if (method_exists($module2,"_after_add")) {
			call_user_func(array(&$module2,"_after_add"),$data);
		}
		if($modelname=="MisWorkFacilityType"){
			//查询设备分类Pid为0
			$MisWorkFacilityTypeModel=D('MisWorkFacilityType');
			$MisWorkFacilityTypeMap['status']=1;
			$MisWorkFacilityTypeMap['pid']=0; //顶级节点
			$MisWorkFacilityTypeList=$MisWorkFacilityTypeModel->where($MisWorkFacilityTypeMap)->getField("id,name");
			//自动生成订单
			$scnmodel = D('SystemConfigNumber');
			$orderno = $scnmodel->GetRulesNO('mis_work_facility_type');
			$this->assign("orderno", $orderno);
			$pid=$_REQUEST['pid'];
			$this->assign("pid",$pid);
			$this->assign("MisWorkFacilityTypeList",$MisWorkFacilityTypeList);
			$this->display();
		}else {
			$model = D("MisProductUnittype");
			$map2['status']= 1;
			$map2['typeid']=1;
			$typeList = $model->where($map2)->select();
			$this->assign('typelist',$typeList);
			$select_config = require DConfig_PATH."/System/selectlist.inc.php";
			$select_config = $select_config['unittype']['unittype'];
			$this->assign('unittypelist',$select_config);
			$this->display("addunit");
		}

	}
	public function getLeaveTree(){
		$tree = array(); // 树初始化
		$tree[] = array(
				'id' => -1,
				'pId' => 0,
				'name' => '基础设置',
				'title' => '基础设置',
				'open' => true
		);
		$tree[] = array(
				'id' => -2,
				'pId' => -1,
				'name' => '设备类型',
				'title' => '设备类型',
				'rel' => "misworkfacilitytype",
				'target' => 'ajax',
				'icon' => "",
				'url' => "__URL__/index/md/MisWorkFacilityType/jump/2",
				'open' => true
		);
		$tree[] = array(
				'id' => -3,
				'pId' => -1,
				'name' => '设备单位',
				'title' => '设备单位',
				'rel' => "misworkfacilitytype",
				'target' => 'ajax',
				'icon' => "",
				'url' => "__URL__/index/md/MisProductUnit/jump/3",
				'open' => true
		);
		//查询设备分类Pid为0
		$MisWorkFacilityTypeModel=D('MisWorkFacilityType');
		$MisWorkFacilityTypeMap['status']=1;
		$MisWorkFacilityTypeMap['pid']=0; //顶级节点
		$MisWorkFacilityTypeList=$MisWorkFacilityTypeModel->where($MisWorkFacilityTypeMap)->getField("id,name");
		foreach ($MisWorkFacilityTypeList as $key=>$val){
			$tree[] = array(
					'id' => $key,
					'pId' => -2,
					'name' =>$val,
					'title' =>$val,
					'rel' => "misworkfacilitytype",
					'target' => 'ajax',
					'icon' => "",
					'url' => "__URL__/index/md/MisWorkFacilityType/pid/".$key."/jump/2",
					'open' => true
			);
		}
		$this->assign("tree",json_encode($tree));
	}
	public function delete() {
		//删除指定记录
		if($_REQUEST['table']){
			$name = $_REQUEST['table'];
		}else {
			$name=$this->getActionName();
		}
		$model = D ($name);
		if (! empty ( $model )) {
			 $id=$_REQUEST['id'];
				if($name=="MisWorkFacilityType"){
					//查询此父级是否存在子级
					$MisWorkFacilityTypeModel=D($name);
					$MisWorkFacilityTypeMap['status']=1;
					$MisWorkFacilityTypeMap['pid']=$id;
					$MisWorkFacilityTypeIdList=$MisWorkFacilityTypeModel->where($MisWorkFacilityTypeMap)->getField("id,name");
					if($MisWorkFacilityTypeIdList){
						$this->error ("删除失败,此节点下存在子节点。");
					}
				$list=$model->where ("id=".$id)->setField ( 'status', - 1 );
			} else {
				$MisWorkFacilityTypeModel=D($name);
				$list=$MisWorkFacilityTypeModel->where ("id=".$id)->setField ( 'status', - 1 );
			}
			if ($list!==false) {
				$this->success ( L('_SUCCESS_') );
			} else {
				$this->error ( L('_ERROR_') );
			}
		}
	}
}
?>