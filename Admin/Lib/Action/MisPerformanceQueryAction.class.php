<?php
//Version 1.0
/** 
 * @Title: MisPerformanceQueryAction 
 * @Package package_name
 * @Description: todo(绩效查询) 
 * @author laicaixia
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-8-14 下午1:55:07 
 * @version V1.0
 */ 
class MisPerformanceQueryAction extends MisPerformancePlanParentsAction {
	
	/** 
	*  @Description: index(进入首页)   
	*  @author laicaixia 
	*  @date 2013-8-21 下午5:33:38 
	*/  
	public function index() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$this->znodes();
		//取出(左边考核计划等于右边相应内容)
		$planid = $_REQUEST['id'];
		if ($planid) {
			$map['planid'] = $_REQUEST['id'];
		}
		$this->assign("planid",$planid);
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
	private function znodes(){
		$mppModel = M("mis_performance_plan");//考核计划表
		$mppList=$mppModel->where('status = 1 and ostatus = 4')->order('id desc')->select();
		$mppParam['rel']="MisPerformanceQueryRel";
		$mppParam['url']="__URL__/index/jump/1/id/#id#";
		$mpqTreemiso[]=array(
				'id'=>0,
				'pId'=>0,
				'name'=>'考核计划',
				'rel'=>'MisPerformanceQueryRel',
				'target'=>'ajax',
				'url'=>'__URL__/index/jump/1/id/#id#',
				'open'=>true
		);
		$mpquertTreearr = $this->getTree($mppList,$mppParam,$mpqTreemiso);
		$this->assign("mpquertTreearr",$mpquertTreearr);
	}
	
	/**
	 * @Description: _after_list(_list方法的后置函数)
	 * @author lcx
	 * @date 2013-8-14  下午 4:04:45
	 */
	public function _after_list(&$voList){
		// 获取绩效等级数据
		$plmodel = D("MisPerformanceLevel");
		$pllist = $plmodel->where("status=1")->select();
		//【相应分数】对应【相应等级】
		foreach ($voList as $k => $v){
			$voList[$k]['level'] = $this->getLevelName($pllist, $v);
		}
	}
	
	/**
	 * @Description: _before_edit(进入修改)   
	 * @author laicaixia 
	 * @date 2013-8-15 上午10:57:01 
	*/  
	public function _before_edit(){
		$this->opanPlanDetail();
		exit;
	}
}
?>