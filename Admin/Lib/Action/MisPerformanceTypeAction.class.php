<?php
/**
 * @Title: MisPerformanceTypeAction 
 * @Package package_name
 * @Description: todo(人力资源管理-绩效分类管理) 
 * @author renling 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-8-1 上午11:59:58 
 * @version V1.0
 */
class MisPerformanceTypeAction extends CommonAction {
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
		$map['type']=1;//已发布计划
		$type = $_REQUEST['type'];
		if($type==1){
			$map['type']=1;//考核等级
		}else if($type==-2){
			$map['type']=2;//考核指标
		}else if($type==3){
			$map['type']=3;//考核模板
		}else if($type==4){
			$map['type']=4;//评分人设置
		}else if($type){
			$map['type'] = $_REQUEST['type'];
		}
		$this->assign("type",$type); 
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
		$mpppTreemiso=array(
				array(
						'id'=>0,
						'pId'=>0,
						'name'=>'考核分类',
						'open'=>true
				),
				array(
						'id'=>1,
						'pId'=>0,
						'name'=>'考核等级',
						'title'=>'考核等级',
						'rel'=>'MisPerformanceTypeRelIndex',
						'target'=>'ajax',
						'url'=>'__URL__/index/jump/1/type/1',
						'open'=>true
				),
				array(
						'id'=>2,
						'pId'=>0,
						'name'=>'考核指标',
						'title'=>'考核指标',
						'rel'=>'MisPerformanceTypeRelIndex',
						'target'=>'ajax',
						'url'=>'__URL__/index/jump/1/type/2',
						'open'=>true
				),
				array(
						'id'=>3,
						'pId'=>0,
						'name'=>'考核模板',
						'title'=>'考核模板',
						'rel'=>'MisPerformanceTypeRelIndex',
						'target'=>'ajax',
						'url'=>'__URL__/index/jump/1/type/3',
						'open'=>true
				),
				array(
						'id'=>4,
						'pId'=>0,
						'name'=>'评分人设置',
						'title'=>'评分人设置',
						'rel'=>'MisPerformanceTypeRelIndex',
						'target'=>'ajax',
						'url'=>'__URL__/index/jump/1/type/4',
						'open'=>true
				),
		);
		$mptypeTreearr=json_encode($mpppTreemiso);
		$this->assign("mperfotypeTreearr",$mptypeTreearr);
	}
	/**
	 * @Description: _before_add(进入新增)   
	 * @author laicaixia 
	 * @date 2013-8-27 上午9:58:16 
	*/  
	public function _before_add(){
		 $type=$_GET['type'];
		 $this->assign("type",$type);
		 $this->assign("vid",1);
	}
	
	public function _before_edit(){
	    $type=$_GET['type'];
		$this->assign("type",$type);
		$this->assign("vid",1);
	}
	/**
	 * @Description: _before_delete(绩效分类管理的删除)   
	 * @author laicaixia 
	 * @date 2013-8-21 下午2:04:16 
	*/  
	public function _before_delete() {
		$id = $_REQUEST['id'];
		//取出  绩效分类
		$TypeModel = D('MisPerformanceType');//绩效分类表
		$TypelVo = $TypeModel->where(" status=1 and id=".$id)->find();
		//考核模板
		$MisPerformanceTemplateModel=D('MisPerformanceTemplate');
		$MisPerformanceTemplateList=$MisPerformanceTemplateModel->where(" status=1 and typeid=".$id)->find();
		if($MisPerformanceTemplateList){
			$this->error ('请先删除【'.$TypelVo['name'].'】下的模板！');
		}
		// 考核等级
		$LevelModel = D('MisPerformanceLevel');//考核等级表
		$LevelVo = $LevelModel->where(" status=1 and typeid=".$id)->find();
		if($LevelVo){
			$this->error ('请先删除【'.$TypelVo['name'].'】下的数据！');
		}
		// 考核指标
		$KpiModel = D('MisPerformanceKpi');//考核指标表
		$KpiVo = $KpiModel->where(" status=1 and typeid=".$id)->find();
		if($KpiVo){
			$this->error ('请先删除【'.$TypelVo['name'].'】下的数据！');
		}
	}
}
?>