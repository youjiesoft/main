<?php
/**
 * @Title: MisSystemUnitAction
 * @Package package_name
 * @Description: todo(动态表单_自动生成-单位设置)
 * @author 管理员
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015-01-08 17:31:18
 * @version V1.0
*/
class MisSystemUnitAction extends MisSystemUnitExtendAction {
	public function _filter(&$map){
	$fieldtype=$_REQUEST['fieldtype'];
	if($fieldtype){
		$map[$fieldtype]=$_REQUEST[$fieldtype];
		$this->assign("fieldtype",$fieldtype);
		$this->assign("fieldtypeval",$_REQUEST[$fieldtype]);
	}
		if ($_SESSION["a"] != 1){
			$map['status']=array("gt",-1);
		}
		$this->_extend_filter(&$map);
	}
	/**
	 * @Title: edit
	 * @Description: todo(重写父类编辑函数)
	 * @author 管理员
	 * @date 2015-01-08 17:31:18
	 * @throws 
	*/
	function edit($isdisplay=1){
		$mainTab = 'mis_system_unit';
		//获取当前控制器名称
		$name=$this->getActionName();
		$model = D("MisSystemUnitView");
		//获取当前主键
		$map[$mainTab.'.id']=$_REQUEST['id'];
		$vo = $model->where($map)->find();
		if(method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		//读取动态配制
		$this->getSystemConfigDetail($name);
		//扩展工具栏操作
		$scdmodel = D('SystemConfigDetail');
		// 上一条数据ID
		$map['id'] = array("lt",$id);
		$updataid = $model->where($map)->order('id desc')->getField('id');
		$this->assign("updataid",$updataid);
		// 下一条数据ID
		$map['id'] = array("gt",$id);
		$downdataid = $model->where($map)->getField('id');
		$this->assign("downdataid",$downdataid);
		//lookup带参数查询
		$module=A($name);
		if (method_exists($module,"_after_edit")) {
			call_user_func(array(&$module,"_after_edit"),&$vo);
		}
		$this->assign( 'vo', $vo );
		if($isdisplay)
		$this->display ();
	}
	/**
	 * @Title: _before_index
	 * @Description: todo(前置index函数)
	 * @author 管理员
	 * @date 2015-01-08 17:31:18
	 * @throws 
	*/
	function _before_index() {
		//查询绑定数据源
		//$this->getDateSoure();
		$unitTypeModel=M('mis_system_unit_type');
		$nitTypelist=$unitTypeModel->select();
		$this->assign('MisSaleClientTypeList',$nitTypelist);
		$this->_extend_before_index();
	}
	
	public function index() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		$danweileixing=$_REQUEST['danweileixing'];
		if($danweileixing){
			$map['danweileixing']=$danweileixing;
		}
		$this->assign('danweileixing',$danweileixing);
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		if($_REQUEST['projectid']){
			$map['projectid'] = $_REQUEST['projectid'];
		}
		if($_REQUEST['projectworkid']){
			$map['projectworkid'] = $_REQUEST['projectworkid'];
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
			$this->_list ( $name, $map );
		}
		//begin
		$scdmodel = D('SystemConfigDetail');
		//读取列名称数据(按照规则，应该在index方法里面)
		$detailList = $scdmodel->getDetail($name);
		if(file_exists(ROOT . '/Dynamicconf/Models/'.$name.'/form.inc.php')){
			$anameList = require ROOT . '/Dynamicconf/Models/'.$name.'/form.inc.php';
			//dump($anameList);
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
		//dump($detailList);
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		//扩展工具栏操作
		$toolbarextension = $scdmodel->getDetail($name,true,'toolbar');
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
	
	/**
	 * @Title: _before_edit
	 * @Description: todo(前置编辑函数)
	 * @author 管理员
	 * @date 2015-01-08 17:31:18
	 * @throws 
	*/
	function _before_edit(){
		$this->_extend_before_edit();
	}
	/**
	 * @Title: _before_insert
	 * @Description: todo(前置添加函数)
	 * @author 管理员
	 * @date 2015-01-08 17:31:18
	 * @throws 
	*/
	function _before_insert(){
		$this->_extend_before_insert();
	}
	/**
	 * @Title: _before_update
	 * @Description: todo(前置修改函数)  
	 * @author 管理员
	 * @date 2015-01-08 17:31:18
	 * @throws
	*/
	function _before_update(){
		$this->_extend_before_update();
	}
	/**
	 * @Title: _after_edit
	 * @Description: todo(后置编辑函数)
	 * @author 管理员
	 * @date 2015-01-08 17:31:18
	 * @throws 
	*/
	function _after_edit($vo){
		$model=M("mis_system_unit_type");
		$listdanweileixing =$model->where('status=1')->field("name,id")->select();
		$this->assign("danweileixinglist",$listdanweileixing);
		$model=D('Selectlist');
		$selectlis = $model->GetRules('unittype');
		$selectlistdanweizhuangtai=array();
		foreach($selectlis['unittype'] as $k=>$v){
			$temp['key']=$k;
			$temp['val']=$v;
			$selectlistdanweizhuangtai[]=$temp;
		}
		$this->assign("selectlistdanweizhuangtai",$selectlistdanweizhuangtai);
		$this->_extend_after_edit($vo);
	}
	/**
	 * @Title: _after_insert
	 * @Description: todo(后置insert函数)  
	 * @author 管理员
	 * @date 2015-01-08 17:31:18
	 * @throws
	*/
	function _after_insert($id){
		$this->_extend_after_insert($id);
	}
	/**
	 * @Title: _before_add
	 * @Description: todo(前置add函数)  
	 * @author 管理员
	 * @date 2015-01-08 17:31:18
	 * @throws
	*/
	function _before_add(){
		$model=M("mis_system_unit_type");
		$listdanweileixing =$model->where('status=1')->field("name,id")->select();
		$this->assign("danweileixinglist",$listdanweileixing);
		$model=D('Selectlist');
		$selectlis = $model->GetRules('unittype');
		$selectlistdanweizhuangtai=array();
		foreach($selectlis['unittype'] as $k=>$v){
			$temp['key']=$k;
			$temp['val']=$v;
			$selectlistdanweizhuangtai[]=$temp;
		}
		$this->assign("selectlistdanweizhuangtai",$selectlistdanweizhuangtai);
		$this->_extend_before_add();
	}
	/**
	 * @Title: _after_update
	 * @Description: todo(后置update函数)  
	 * @author 管理员
	 * @date 2015-01-08 17:31:18
	 * @throws
	*/
	function _after_update(){
		$this->_extend_after_update();
	}
}
?>