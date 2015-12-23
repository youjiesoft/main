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
class MisPerformanceTemplateAction extends CommonAction {
	function index(){
		$MisPerformanceTypeModel=D("MisPerformanceType");
		$MisPerformanceTemplateModel=M("MisPerformanceTemplate");
		$MisPerformanceTypeList=$MisPerformanceTypeModel->where('status = 1 and type = 3')->field('id,name,type')->select();
		$MisPerformanceTemplateList=$MisPerformanceTemplateModel->where('status = 1')->field('id,name,typeid')->select();
		$returnarr[]=array(
				'id'=>0,
				'pId'=>0,
				'name'=>'绩效考核模板管理',
				'title'=>'绩效考核模板管理',
				'open'=>true
		);
		foreach($MisPerformanceTypeList as $key=>$val){
			$returnarr[]=array(
					'id'=>-$val['id'],
					'pId'=>0,
					'name'=>$val['name'],
					'title'=>$val['name'],
					'open'=>true
			);
		}
		foreach($MisPerformanceTemplateList as $k=>$v){
			$returnarr[]=array(
					'id'=>$v['id'],
					'pId'=>-$v['typeid'],
					'name'=>$v['name'],
					'title'=>$v['name'],
					'open'=>true,
					'rel'=>'misperformancetemplate_rightcontent1',
					'url'=>'__URL__/index/jump/1/type/'.$v['id'],
					'target'=>'ajax'
			);
		}
		$param['rel']="misperformancetemplate_rightcontent1";
		$param['url']="__URL__/index/jump/1/type/#id#";
		$list=array_merge($MisPerformanceTypeList,$MisPerformanceTemplateList);
		$this->assign("treearr",json_encode($returnarr));
		$map = $this->_search('MisPerformanceTemplateDetail');
		$typeid = $_REQUEST['type'];
		$map['tempid'] = $_REQUEST['type'];
		$map['status']=1;
		//查询模板信息
		$TemplateList=$MisPerformanceTemplateModel->where(" status=1 and id=".$typeid)->find();
		$this->assign("TemplateList",$TemplateList);
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$model = D('MisPerformanceTemplateDetail');
		$list = $model->where($map)->select();
		$volist = array();
		$sumscore=0;
		foreach ($list as $k => $v) {
			$sumscore+=$v['kpiscore'];
			if($volist[$v['kpitypeid']]){
				$volist[$v['kpitypeid']]['kpi'][] = array(
						'id' => $v['id'],
						'kpiid' => $v['kpiid'],
						'kpiscore' => $v['kpiscore'],
				);
			} else {
				$volist[$v['kpitypeid']] = array(
						'kpitypeid' => $v['kpitypeid'],
						'kpitypeqz' => $v['kpitypeqz']
				);
				$volist[$v['kpitypeid']]['kpi'][] = array(
						'id' => $v['id'],
						'kpiid' => $v['kpiid'],
						'kpiscore' => $v['kpiscore'],
				);
			}
		}
		$this->assign('sumscore',$sumscore);
		$this->assign('list',$volist);
		$this->assign('type',$typeid);
		if ($_REQUEST['jump']) {
			$this->display('unitlist');
		} else {
			$this->display();
		}
	}
	public function edit(){
		switch ($_REQUEST['temptype']){
			case 'detail':
				$this->lookupeditdetail();
				break;
			default:
				//修改模板
				$name=$this->getActionName();
				$model = D ( $name );
				$id = $_REQUEST ['id'];
				$map['id']=$id;
				if ($_SESSION["a"] != 1) $map['status'] = 1;
				$vo = $model->where($map)->find();
				if(empty($vo)){
					$this->display ("Public:404");
					exit;
				}
				$this->assign( 'vo', $vo );
				$this->display();
				break;
		}
	}
	/**
	 * @Title: lookupeditdetail
	 * @Description: todo(修改查看模板)
	 * @author renling
	 * @date 2013-10-28 下午3:09:06
	 * @throws
	 */
	private function lookupeditdetail(){
		$MisPerformanceTemplateModel=M("MisPerformanceTemplate");
		$tempid=$_REQUEST['tempid'];
		$this->assign("type",$tempid);
		//查询模板信息
		$TemplateList=$MisPerformanceTemplateModel->where(" status=1 and id=".$tempid)->find();
		$this->assign("TemplateList",$TemplateList);
		$model = D('MisPerformanceTemplateDetail');
		$list = $model->where("tempid=".$tempid)->select();
		$volist = array();
		$sumscore=0;
		foreach ($list as $k => $v) {
			$sumscore+=$v['kpiscore'];
			if($volist[$v['kpitypeid']]){
				$volist[$v['kpitypeid']]['kpi'][] = array(
						'id' => $v['id'],
						'kpiid' => $v['kpiid'],
						'kpiscore' => $v['kpiscore'],
				);
			} else {
				$volist[$v['kpitypeid']] = array(
						'kpitypeid' => $v['kpitypeid'],
						'kpitypeqz' => $v['kpitypeqz']
				);
				$volist[$v['kpitypeid']]['kpi'][] = array(
						'id' => $v['id'],
						'kpiid' => $v['kpiid'],
						'kpiscore' => $v['kpiscore'],
				);
			}
		}
		$this->assign('sumscore',$sumscore);
		$this->assign('list',$volist);
		$this->display('editdetail');
	}
	/**
	 * @Title: common
	 * @Description: todo(公用方法)
	 * @author renling
	 * @date 2013-8-2 上午11:05:36
	 * @throws
	 */
	private function common(){
		//查询等级分类
		$misPerformanceTypeModel = D('MisPerformanceType');
		$map['status'] = 1;
		$map['type'] = 3; //类型为模板
		$list = $misPerformanceTypeModel->where($map)->getField('id,name');
		$this->assign('typelist',$list);
	}
	public function _before_add(){
		$tempid=$_GET['id'];
		$this->assign("tempid",$tempid);
		$this->common();
	}
	public function _before_edit(){
		$this->common();
	}
	public function add() {
		switch ($_REQUEST['step']) {
			case 1:
				//考核分类
				$this->addkpitype();
				break;
			case 2:
				//考核指标
				$this->addkpi();
				break;
			default:
				$this->display();
				break;
		}
	}
	private function addkpitype(){
		$isedit=$_GET['edit'];
		$typeid=$_GET['typeid'];
		$kpitypeqz=$_GET['kpitypeqz'];
		$this->assign("tempid",$_GET['tempid']);
		$this->assign('typeid',$typeid);
		$this->assign('kpitypeqz',$kpitypeqz);
		$this->assign('isedit',$isedit);
		//查询等级分类
		$misPerformanceTypeModel = D('MisPerformanceType');
		$map['status'] = 1;
		$map['type'] = 2; //类型为指标
		$list = $misPerformanceTypeModel->where($map)->getField('id,name,remark');
		$this->assign('typelist',$list);
		$this->display('addkpitype');
	}
	/**
	 * @Title: addkpi
	 * @Description: todo(考核指标)
	 * @author renling
	 * @date 2013-10-28 下午3:00:56
	 * @throws
	 */
	private function addkpi(){
		$typeid=$_GET['typeid'];
		$MisPerformanceKpiModel=D('MisPerformanceKpi');
		$kpimap['status']=1;
		$kpimap['typeid']=$typeid;
		$this->assign('typeid',$typeid);
		$list = $MisPerformanceKpiModel->where($kpimap)->getField('id,code,name,remark');
		$MisPerformanceTemplateModel=M("MisPerformanceTemplateDetail");
		foreach ($list  as $key=>$val){
			$editmap['tempid']=$_GET['tempid'];
			$editmap['kpitypeid']=$typeid;
			$editmap['kpiid']=$val['id'];
			$editmap['status']=1;
			$MisPerformanceTemplateList=$MisPerformanceTemplateModel->where($editmap)->find();
			$list[$key]['editid']=$MisPerformanceTemplateList['id'];
		}
		$this->assign("kpilist",$list);
		$this->display('addkpi');
	}
	/**
	 * @Title: updatetemp
	 * @Description: todo(保存修改模板)
	 * @author renling
	 * @date 2013-8-8 下午4:18:57
	 * @throws
	 */
	public function update(){
		switch($_REQUEST['step']){ //修改模板标题信息
			case 1:
				$this->updatedetails();
				break;
			default:
				$this->updatetemp();
				break;
		}
	}
	/**
	 * @Title: updatetemp 
	 * @Description: todo(修改模板指标、考核分类信息)   
	 * @author renling 
	 * @date 2013-11-11 下午4:35:41 
	 * @throws
	 */
	private function updatetemp(){
		$name=$this->getActionName();
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
	 * @Title: updatedetails 
	 * @Description: todo(修改模板分类信息)   
	 * @author renling 
	 * @date 2013-11-11 下午4:35:25 
	 * @throws
	 */
	private function updatedetails(){
		$MisPerformanceKpiModel=D('MisPerformanceKpi');
		$MisPerformanceTemplateDetailModel=D('MisPerformanceTemplateDetail');
		$kpimap['status']=1;
		$MisPerformanceKpiList = $MisPerformanceKpiModel->where($kpimap)->getField('id,typeid');
		$kpiid=$_POST['add_kpi'];
		$kpitypeid=$_POST['add_kpitypeid'];
		$editid=$_POST['edit_id'];
		$map['tempid'] = $_POST['tempid'];
		$istypedeleteList=$MisPerformanceTemplateDetailModel->where($map)->field('id,kpitypeid')->select();
		foreach($istypedeleteList as $key=>$val){
			if(!in_array($val['kpitypeid'], $_POST['add_kpitypeid'])){
				$result=$MisPerformanceTemplateDetailModel->where("tempid=".$_POST['tempid']." and kpitypeid=".$val['kpitypeid'])->delete();
			}
		}
		unset($map);
		$map['tempid'] = $_POST['tempid'];
		$map['kpitypeid'] =array(' in ',$_POST['add_kpitypeid']);
		$map['status']=1;
		$isdeleteList=$MisPerformanceTemplateDetailModel->where($map)->field('id,kpitypeid,kpiid')->select();
		foreach($isdeleteList as $key=>$val){
			if(!in_array($val['kpiid'], $_POST['add_kpi'])){
				$result=$MisPerformanceTemplateDetailModel->where("id=".$val['id'])->delete();
			}
		}
		foreach($kpiid as $key=>$val){
			$data['tempid']=$_POST['tempid'];
			$data['kpitypeid']=$MisPerformanceKpiList[$val];
			foreach($kpitypeid as $k=>$v){
				if($v==$MisPerformanceKpiList[$val]){
					$data['kpitypeqz']=$_POST['add_kpitypeqz'][$k];
				}
			}
			$data['kpiid']=$val;
			$data['kpiscore']=$_POST['add_kpiscore'][$val];
			if($editid[$key]){
				$data['updatetime']=time();
				$data['updateid']=$_SESSION[C('USER_AUTH_KEY')];
				$list=$MisPerformanceTemplateDetailModel->where("id=".$editid[$key])->save ($data);
			}else{
				$data['createtime']=time();
				$data['createid']=$_SESSION[C('USER_AUTH_KEY')];
				$list=$MisPerformanceTemplateDetailModel->data($data)->add ();
			}
		}
		if($list==false){
			$this->error ( L('_ERROR_') );
		}else{
			$this->success('操作成功');
		}
	}
}
?>