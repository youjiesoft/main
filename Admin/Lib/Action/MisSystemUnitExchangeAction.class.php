<?php
/**
 * @Title: MisSystemUnitExchangeAction
 * @Package package_name
 * @Description: todo(动态表单_自动生成-单位转换)
 * @author 管理员
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015-01-08 20:12:57
 * @version V1.0
*/
class MisSystemUnitExchangeAction extends MisSystemUnitExchangeExtendAction {
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
	
	public function index() {
		$name = $this->getActionName();
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		
		$MisSystemUnitModel=D('MisSystemUnit');
		//查询当前所有单位
		$MisSystemUnitList=$MisSystemUnitModel->where('status = 1')->select();
		//封装单位结构树
		$listnew = $MisSystemUnitList;
		foreach($listnew as $k=>$v){
			$listnew[$k]['name'] =$v['danweimingchen'];
		}
		$param['rel']="MisSystemUnitExchangeindexview";
		$param['url']="__URL__/index/jump/jump/baseunitid/#id#";
		$treemiso[]=array(
				'id'=>0,
				'pId'=>0,
				'name'=>'主单位',
				'title'=>'主单位',
				'open'=>true,
				'isParent'=>true,
		);
		$treearr = $this->getTree($listnew,$param,$treemiso,false);
		$this->assign("treearr",$treearr);
		$baseunitid = $_REQUEST['baseunitid'];
		if($baseunitid){
			$map['baseunitid']=$baseunitid;
		}
		$this->assign("baseunitid",$baseunitid);
		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		if($_REQUEST['projectid']){
			$map['projectid'] = $_REQUEST['projectid'];
		}
		if($_REQUEST['projectworkid']){
			$map['projectworkid'] = $_REQUEST['projectworkid'];
		}
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
	
	public function index1(){
		$name=$this->getActionName();
		$this->getSystemConfigDetail($name);
		$model=D($name);
		//查询当前所有单位
		$list=$model->where('status = 1')->select();
		//封装单位结构树
		$listnew = $list;
		foreach($listnew as $k=>$v){
			$listnew[$k]['name'] =$v['name'];
		}
		$param['rel']="MisSystemUnitExchangeindexview";
		$param['url']="__URL__/index/jump/jump/zt/#id#";
		$treemiso[]=array(
				'id'=>0,
				'pId'=>0,
				'name'=>'主体档案',
				'title'=>'主体档案',
				'open'=>true,
				'isParent'=>true,
		);
		$treearr = $this->getTree($listnew,$param,$treemiso,false);
		$this->assign("treearr",$treearr);
		$zt = $_REQUEST['zt'];
		$this->assign("valid",$zt);
		if($_REQUEST['jump'] == 1){
			$this->display("indexview");
		}else{
			$this->display();
		}
	}
	
	/**
	 * @Title: edit
	 * @Description: todo(重写父类编辑函数)
	 * @author 管理员
	 * @date 2015-01-08 20:12:57
	 * @throws 
	*/
	function edit($isdisplay=1){
		$mainTab = 'mis_system_unit_exchange';
		//获取当前控制器名称
		$name=$this->getActionName();
		$model = D("MisSystemUnitExchangeView");
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
	 * @date 2015-01-08 20:12:57
	 * @throws 
	*/
	function _before_index() {
		//查询绑定数据源
		$this->getDateSoure();
		$this->_extend_before_index();
	}
	/**
	 * @Title: _before_edit
	 * @Description: todo(前置编辑函数)
	 * @author 管理员
	 * @date 2015-01-08 20:12:57
	 * @throws 
	*/
	function _before_edit(){
		$this->_extend_before_edit();
	}
	/**
	 * @Title: _before_insert
	 * @Description: todo(前置添加函数)
	 * @author 管理员
	 * @date 2015-01-08 20:12:57
	 * @throws 
	*/
	function _before_insert(){
		$this->_extend_before_insert();
	}
	/**
	 * @Title: _before_update
	 * @Description: todo(前置修改函数)  
	 * @author 管理员
	 * @date 2015-01-08 20:12:57
	 * @throws
	*/
	function _before_update(){
		$this->_extend_before_update();
	}
	/**
	 * @Title: _after_edit
	 * @Description: todo(后置编辑函数)
	 * @author 管理员
	 * @date 2015-01-08 20:12:57
	 * @throws 
	*/
	function _after_edit($vo){
		$model=M("mis_system_unit");
		$listbaseunitid =$model->where('status=1')->field("danweimingchen,id")->select();
		$this->assign("baseunitidlist",$listbaseunitid);
		$model=M("mis_system_unit");
		$listsubunitid =$model->where('status=1')->field("danweimingchen,id")->select();
		$this->assign("subunitidlist",$listsubunitid);
		$this->_extend_after_edit($vo);
	}
	/**
	 * @Title: _after_insert
	 * @Description: todo(后置insert函数)  
	 * @author 管理员
	 * @date 2015-01-08 20:12:57
	 * @throws
	*/
	function _after_insert($id){
		$this->_extend_after_insert($id);
	}
	/**
	 * @Title: _before_add
	 * @Description: todo(前置add函数)  
	 * @author 管理员
	 * @date 2015-01-08 20:12:57
	 * @throws
	*/
	function _before_add(){
		$model=M("mis_system_unit");
		$listbaseunitid =$model->where('status=1')->field("danweimingchen,id")->select();
		$this->assign("baseunitidlist",$listbaseunitid);
		$model=M("mis_system_unit");
		$listsubunitid =$model->where('status=1')->field("danweimingchen,id")->select();
		$this->assign("subunitidlist",$listsubunitid);
		$this->_extend_before_add();
	}
	/**
	 * @Title: _after_update
	 * @Description: todo(后置update函数)  
	 * @author 管理员
	 * @date 2015-01-08 20:12:57
	 * @throws
	*/
	function _after_update(){
		$this->_extend_after_update();
	}
}
?>