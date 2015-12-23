<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(车辆类型维护) 
 * @author yuansl 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-7-15 上午9:52:38 
 * @version V1.0
 */
class MisVehicleTypeAction extends CommonAction{
	/**
	 * @Title: _filter 
	 * @Description: todo(这里用一句话描述这个方法的作用) 
	 * @param unknown_type $map  
	 * @author yuansl 
	 * @date 2014-7-15 上午10:23:23 
	 * @throws
	 */
	public function _filter(&$map){
		if ($_SESSION["a"] != 1){
			$map['status']=array("gt",0);
		}
	}
	/**
	 * (non-PHPdoc)
	 * @see CommonAction::index()
	 */
	public function index(){
		$name = $this->getActionName();
		//列表过滤器，生成查询Map对象
		$map = $this->_search ($name);
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
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
		$this->display('index');
	}
	/**
	 * @Title: _before_delete 
	 * @Description: todo(限制删除)   
	 * @author yuansl 
	 * @date 2014-7-18 下午4:03:57 
	 * @throws
	 */
	public function _before_delete(){
		$id = $_REQUEST['id'];
		$model = D("MisSystemCar");
		$re = $model->where("status = 1 and cartype = ".$id)->field("id")->find();
		if($re){
			$this->error("该类型不能删除！");
			exit;
		}
	}
	public function _before_add(){
		//订单是否可写
		$scnmodel = D('SystemConfigNumber');
		$writable= $scnmodel->GetWritable('mis_vehicle_type');
		$this->assign("writable",$writable);
		//自动生成编号
		$code = $scnmodel->GetRulesNO('mis_vehicle_type');
		$this->assign("code", $code);
	}
}