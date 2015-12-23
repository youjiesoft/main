<?php
/**
 * @Title: MisPerformanceTypeAction
 * @Package package_name
 * @Description: todo(人力资源管理-绩效等级管理)
 * @author renling
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-8-1 上午11:59:58
 * @version V1.0
 */
class MisPerformanceLevelAction extends CommonAction {
	function index(){
		$misPerformanceTypeModel = D('MisPerformanceType');
		$map['status'] = 1;
		$map['type'] = 1; //类型为等级
		$list = $misPerformanceTypeModel->where($map)->select();
		$param['rel']="misperformancelevel_rightcontent";
		$param['url']="__URL__/index/jump/1/type/#id#";
		$returnarr[]=array(
				'id'=>0,
				'parentid'=>0,
				'name'=>'考核等级分类',
				'open'=>true,
				/* 'rel'=>'misperformancelevel_rightcontent',
				'url'=>'__URL__/index/jump/1',
				'target'=>'ajax' */
		);
		foreach ($list as $key => $v){
			$returnarr[]=array(
				'id'=>$v['id'],
				'pId'=>0,
				'name'=>$v['name'],
				'title'=>$v['name'],
				'open'=>true,
				'rel'=>'misperformancelevel_rightcontent',
				'url'=>'__URL__/index/jump/1/type/'.$v['id'],
				'target'=>'ajax'
			);
		}
		$this->assign('type',$list[0]['id']);
		$this->assign("treearr",json_encode($returnarr));
		unset($map);
		$map = $this->_search();
		$map['typeid'] =$list[0]['id'];
		if ($_REQUEST['jump']) {
			$map['typeid'] = $_REQUEST['type'];
		}
		$this->assign("type",$map['typeid']);
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name = 'MisPerformanceLevel';
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
		if ($_REQUEST['jump']) {
			$this->display('unitlist');
		} else {
			$this->display();
		}
	}
	private function common(){
		//查询等级分类
		$misPerformanceTypeModel = D('MisPerformanceType');
		$map['status'] = 1;
		$map['type'] = 1; //类型为等级
		$list = $misPerformanceTypeModel->where($map)->getField('id,name');
		$this->assign('typelist',$list);
	}
	public function _before_add(){
		//自动生成绩效等级编码
		$scnmodel = D('SystemConfigNumber');
		$code = $scnmodel->GetRulesNO('mis_performance_level');
		$this->assign("code",$code);
		
		$type=$_GET['type'];
		$this->assign("type",$type);
		$this->common();
	}
	public function _before_edit(){
		$this->common();
	}
}
?>