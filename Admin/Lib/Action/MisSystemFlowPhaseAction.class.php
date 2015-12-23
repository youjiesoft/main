<?php
/**
 * @Title: file_name
 * @Package package_name
 * @Description: todo(表单流程类型)
 * @author liminggang
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2014-8-16 上午11:03:19
 * @version V1.0
 */
class MisSystemFlowPhaseAction extends CommonAction {
	public function _filter(&$map){
		$map['outlinelevel'] = 2; //业务阶段
		if ($_SESSION["a"] != 1){
			$map['status']=array("gt",-1);
		}
	}
	
	public function index(){
		//获取流程类型 组合成树结构
		$MisSystemFlowTypeDao = M("mis_system_flow_type");
		$where['status'] = 1;
		$where['outlinelevel'] = 1; //业务类型
		$typelist=$MisSystemFlowTypeDao->where($where)->order("sort,id asc")->select();
		
		$paeam['url'] = "__URL__/index/jump/jump/pid/#id#";
		$paeam['rel'] = "MisSystemFlowPhase_left";
		$returnarr[] = array(
				'id'=>0,
				'name'=>'业务类型',
				'title'=>'业务类型',
				'open'=>true,
		);
		$json_arr=$this->getTree($typelist,$paeam,$returnarr);
		$this->assign('json_arr',$json_arr);
		
		$name = $this->getActionName();
		//列表过滤器，生成查询Map对象
		$map = $this->_search ($name);
		//如果没有选中业务类型ID。直接默认取第一个业务类型ID
		
		
		$_REQUEST['pid']=$_REQUEST['pid']?$_REQUEST['pid']:$typelist[0]['id'];
		$this->assign("pid",$_REQUEST['pid']);
		$map['parentid'] = $_REQUEST['pid'];
		
		if (! empty ( $name )) {
			$this->_list ( $name, $map ,"sort","asc");
		}
		//获取列字段
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($name);
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		//获取工具栏按钮
		$toolbarextension = $scdmodel->getDetail($name,true,'toolbar');
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		if($_REQUEST['jump'] == "jump"){
			$this->display('indexview');
		}else{
			$this->display();
		}
	}
	
	public function _before_add(){
		//获取点击标志，标记是从哪里点击过来的
		$this->assign("pid",$_GET['pid']);
	}
}
?>