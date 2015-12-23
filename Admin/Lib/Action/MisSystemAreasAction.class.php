<?php
class MisSystemAreasAction extends CommonAction {
	public function _filter(&$map){
		if ($_SESSION["a"] != 1)
			$map['status']=array("gt",-1);
	}
	public function index(){
		$name=$this->getActionName();
		$this->getSystemConfigDetail($name);
		$model=D($name);
		//查询当前所有行业类型
		$list=$model->where('status = 1')->select();
		//封装行业类型结构树
		$listnew = $list;
		foreach($listnew as $k=>$v){
			$listnew[$k]['name'] = "(".$v['id'].")".$v['name'];
		}
		$param['rel']="MisSystemAreasview";
		$param['url']="__URL__/index/jump/1/dq/#id#";
		$treemiso[]=array(
				'id'=>0,
				'pId'=>0,
				'name'=>'地区类别',
				'title'=>'地区类别',
				'open'=>false,
				'isParent'=>true,
		);
		$treearr = $this->getTree($listnew,$param,$treemiso,false);
		$this->assign("treearr",$treearr);
		//获取行业ID
		$dq = $_REQUEST['dq'];
		//定义一个存储行业数据数组
		$vo = array();
		if($dq){
			$map['id'] = $dq;
			$vo=$model->where($map)->find();
		}else{
			if($list){
				//判断是否存在行业
				$vo = $list[0];
				$dq = $list[0]['id'];
			}
		}
		$this->assign("valid",$dq);
		$this->assign("vo",$vo);

		if($_REQUEST['jump'] == 1){
			$this->display("indexview");
		}else{
			$this->display();
		}
	}

	function _before_insert(){
		$name = $this->getActionName();
		$_POST['orderno']=$_REQUEST['id'];
	}
	
	function _after_insert(){
		$result=M()->execute('TRUNCATE  `mis_system_areas_temp`;INSERT INTO `mis_system_areas_temp`  SELECT * FROM  `mis_system_areas`;');
		if($result===false){
			$this->error('地址组件数据初始化失败！');
		}
	}
	public function _before_update(){
		$_POST['orderno']=$_REQUEST['id'];
	}
	
	public function _after_update(){
			$result=M()->execute('TRUNCATE  `mis_system_areas_temp`;INSERT INTO   `mis_system_areas_temp`  SELECT * FROM  `mis_system_areas`;');
		if($result===false){
			$this->error('地址组件数据初始化失败！');
		}
	}
	
	public function _before_delete(){
		$name = $this->getActionName();
		$map['parentid'] = $_REQUEST['id'];
		$map['status'] = 1;
		$model = D($name);
		$data = $model->where($map)->select();
		if($data){
			$this->error('此类下面有分类，不能删除');
		}
	}
	
	
	public function delete() {
		//删除指定记录
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
					$list=$model->where ( $condition )->delete();
				if ($list!==false) {
					$this->success ( L('_SUCCESS_') );
				} else {
					$this->error ( L('_ERROR_') );
				}
			} else {
				$this->error ( C('_ERROR_ACTION_') );
			}
		}
	}
	
	
	function lookupGetJoin(){
		$map['id']=$_REQUEST['parentid'];
		$model=D('MisSystemAreas');
		$list=$model->where($map)->find();
		echo  $joinname=json_encode($list['joinname']);
	}
}