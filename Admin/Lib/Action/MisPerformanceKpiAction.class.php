<?php
/**
 * @Title: MisPerformanceKpiAction 
 * @Package 
 * @Description: todo(绩效指标管理) 
 * @author laicaixia
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-8-1 下午3:17:41 
 * @version V1.0
 */

class MisPerformanceKpiAction extends CommonAction {
	
	/** 
	 *  @Description: index(进入首页)   
	 *  @author laicaixia 
	 *  @date 2013-8-1 下午3:33:38 
	 */  
	public function index() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$mptModel = M("mis_performance_type");//效绩考核类型表
		$mptList=$mptModel->where('status = 1 and type = 2')->select();
		//取出左边信息
		$this->znodes($mptList);
		//取出type(左边类型等于右边相应内容)
		$map['typeid'] = $mptList[0]['id'];
		if ($_REQUEST['jump']) {
			$map['typeid'] = $_REQUEST['type'];
		}
		$this->assign("type",$map['typeid']);
		//取出右边信息
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
		if ($_REQUEST['jump']) {
			$this->display('unitlist');
		} else {
			$this->display();
		}
		return;
	}
	
	/** 
	 *  @Description: znodes(构造树形结构)   
	 *  @author laicaixia 
	 *  @date 2013-8-1 下午3:33:38 
	 */  
	private function znodes($mptList){
		$mpkTreemiso[]=array(
				'id'=>0,
				'pId'=>0,
				'name'=>'考核指标分类',
				/* 'rel'=>'MisPerformanceKpiZnodes',
				'target'=>'ajax',
				'url'=>'__URL__/index/jump/1/type/#id#', */
				'open'=>true
		);
		foreach($mptList as $key=>$v){
			$mpkTreemiso[]=array(
					'id'=>$v['id'],
					'pId'=>0,
					'name'=>$v['name'],
					'open'=>true,
					'rel'=>'MisPerformanceKpiZnodes',
					'url'=>'__URL__/index/jump/1/type/'.$v['id'],
					'target'=>'ajax'
			);
		}
		$this->assign('type',$mptList[0]['id']);
		$this->assign("mpkTreearr",json_encode($mpkTreemiso));
	}
	
	/**
	 * @Description: _before_add(进入新增)   
	 * @author laicaixia 
	 * @date 2013-8-1 下午6:13:53 
	*/  
	public function _before_add(){
		//自动生成绩效指标编码
		$scnmodel = D('SystemConfigNumber');
		$code = $scnmodel->GetRulesNO('mis_performance_kpi');
		$this->assign("code",$code);
		//获取类型type
		$type=$_GET['type'];
		$this->assign("type",$type);
		//调用公共函数
		$this->common();
	}
	
	/**
	 * @Description: _before_edit(进入修改)   
	 * @author laicaixia 
	 * @date 2013-8-1 下午6:18:33 
	*/  
	public function _before_edit(){
		//调用公共函数
		$this->common();
	}
	/**
	 * @Description: common(共用信息)   
	 * @author laicaixia 
	 * @date 2013-8-1 下午5:36:03 
	 * @throws 
	*/  
	private function common(){
		//取出绩效指标类型
		$mptModel = M("mis_performance_type");//绩效考核类型表
		$mptList=$mptModel->where('status = 1 and type = 2')->getField('id,name');
		$this->assign('mptList',$mptList);
	}
}
?>