<?php
//Version 1.0
/**
 * @Title: DutyAction
 * @Package package_name
 * @Description: todo(人事相关-职务管理)
 * @author xiafengqin
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-6-1 下午2:46:00
 * @version V1.0
 */
class DutyAction extends CommonAction {
	/**
	 * @Title: _filter
	 * @Description: todo(构造检索条件)
	 * @param unknown_type $map
	 * @author xiafengqin
	 * @date 2013-6-1 下午2:46:22
	 * @throws
	 */
	public function _filter(&$map){
		if(!$_REQUEST ['orderField']){
			$_REQUEST ['orderField'] = 'level';
		}
		if(!$_REQUEST ['orderDirection']){
			$_REQUEST ['orderDirection'] = 'desc';
		}
		if ($_SESSION["a"] != 1)$map['status']=array("gt",-1);
	}
	public function index() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
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
		if($_REQUEST['com']){
			$this->display("MisSystemCompany:indexduty");
			exit;
		}
		$this->display ();
		return;
	}
	public function _before_add(){
		//公司信息进入新增
		if($_REQUEST['com']){
			$this->assign("com",$_REQUEST['com']);
		}
	}
	public function _before_edit(){
		//公司信息进入新增
		if($_REQUEST['com']){
			$this->assign("com",$_REQUEST['com']);
		}
	}
}
?>