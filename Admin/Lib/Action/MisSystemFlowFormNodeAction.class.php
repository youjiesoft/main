<?php
/**
 * @Title: MisSystemFlowFormnNodeAction
 * @Package package_name
 * @Description: todo(审批任务节点)
 * @author laicaixia
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2013-5-31 下午5:36:44
 * @version V1.0
 */
class MisSystemFlowFormNodeAction extends CommonAction {
	public function abc($typelist, $id) {
		foreach ( $typelist as $key => $val ) {
			if ($id == $val ['parentid'] && $val ['outlinelevel'] == 1) {
				unset ( $typelist [$key] );
				$id = $this->abc ( $typelist, $val ['id'] );
			}
		}
		return $id;
	}
	private function getType() {
		// 获取流程类型 组合成树结构
		$MisSystemFlowTypeDao = M ( "mis_system_flow_type" );
		$where ['status'] = 1;
		$typelist = $MisSystemFlowTypeDao->order("sort asc")->where ( $where )->select ();
		
		// 任务节点
		$MisSystemFlowFormDao = M ( "mis_system_flow_form" );
		$where ['outlinelevel'] = 3;
		$formlist = $MisSystemFlowFormDao->order("sort asc")->where ( $where )->select ();
		//任务
		$renmap ['status'] = 1;
		$renmap['outlinelevel']=4;
		$renList= $MisSystemFlowFormDao->order("sort asc")->where ( $renmap )->select ();
		 $NodeModel=D('Node');
		 foreach ($renList as $rek=>$rev){
			$nmap['status']=1;
			$nmap['name']=$rev['formobj'];
			$nmap['isprocess']=1;
			$nodeList=$NodeModel->where($nmap)->select();
				if(empty($nodeList)){
					unset($renList[$rek]);
				}
			} 
		$supcategory = 0;
		$jsonArr = array ();
		foreach ( $typelist as $key => $val ) {
			// 递归获取最小一层类型
			$supcategory = $this->abc ( $typelist, $val ['id'] );
			
			$typelist [$key] ['default'] = $supcategory;
			$array = array ();
			$array ['id'] = $val ['id'];
			$array ['name'] = missubstr($val['name'],18,true);
			$array ['title'] = $val ['name'];
			$array ['pId'] = $val ['parentid'];
			$array ['open'] = true;
			$array ['url'] = "__URL__/index/jump/jump/supcategory/" . $supcategory."/category/0/pid/0";
			$array ['rel'] = "MisSystemFlowFormNodeindexview";
			$array ['type'] = 'post';
			$array ['target'] = 'ajax';
			// 阶段
			if ($val ['outlinelevel'] == 2) {
				$array ['url'] = "__URL__/index/jump/jump/supcategory/" .$val['parentid']."/category/" . $val ['id']."/pid/0";
				$array ['rel'] = "MisSystemFlowFormNodeindexview";
				$array ['type'] = 'post';
				$array ['target'] = 'ajax';
				$jsonArr [] = $array;
				foreach ( $formlist as $k => $v ) {
					if ($val ['id'] == $v ['category']) {
						$array = array ();
						$array ['id'] = $v ['id'];
						$array ['name'] = missubstr($v['name'],18,true);
						$array ['title'] = $v ['name'];
						$array ['pId'] = $val ['id'];
						$array ['url'] = "__URL__/index/jump/jump/supcategory/" .$val['parentid']."/category/" . $val ['id']."/pid/" . $v ['id'];
						$array ['rel'] = "MisSystemFlowFormNodeindexview";
						$array ['type'] = 'post';
						$array ['target'] = 'ajax';
						$jsonArr [] = $array;
						//任务
						foreach ($renList as $renk=>$renval){
							if($v['id']==$renval['parentid']){
								$array = array ();
								$array ['id'] = $renval ['id'];
								$array ['name'] = missubstr($renval['name'],18,true);
								$array ['title'] = $renval ['name'];
								$array ['pId'] = $renval['parentid'];
								$array ['url'] = "__URL__/index/jump/jump/supcategory/" .$val['parentid']."/category/" . $val ['id']."/pid/" . $renval ['parentid']."/id/".$renval['id'];
								$array ['rel'] = "lookupisfile";
								$array ['type'] = 'post';
								$array ['target'] = 'ajax';
								$jsonArr[] = $array;
						
							}
						}
					}
				}
			} else {
				$jsonArr [] = $array;
			}
		}
		$this->assign ( 'json_arr', json_encode ( $jsonArr ) );
		return $typelist;
	}
	public function index() {
		// 获取流程类型
		$typelist = $this->getType ();
		
		$name = 'MisSystemFlowWork';
		
		// 列表过滤器，生成查询Map对象
		$map = $this->_search ( $name );
		
		// 获取点击的类型
		$supcategory = $_REQUEST ['supcategory'] = $_REQUEST ['supcategory']?$_REQUEST ['supcategory']:0;
		if ($supcategory) {
			$map ['supcategory'] = $supcategory;
			$this->assign ( 'supcategory', $supcategory );
		}
		// 获取点击的阶段
		$category = $_REQUEST ['category'] = $_REQUEST ['category']?$_REQUEST ['category']:0;
		if ($category) {
			$map ['category'] = $category;
			$this->assign ( 'category', $category );
		}
		// 获取点击的节点
		$pid = $_REQUEST ['pid'] = $_REQUEST ['pid']?$_REQUEST ['pid']:0;
		if ($pid) {
			$map ['parentid'] = $pid;
			$this->assign ( 'pid', $pid );
		}
		// 树形结构默认选中
		$this->assign ( "ztreeid", $typelist [0] ['id'] );
		// 第一次进入，默认查询第一个类型下面的所有任务
		if (! $supcategory && ! $category && ! $pid) {
			$map ['supcategory'] = $typelist [0] ['default'];
		}
		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$map ['outlinelevel'] = 4; // 查询任务
		if (! empty ( $name )) {
			$this->_list ( $name, $map );
		}
		
		$scdmodel = D ( 'SystemConfigDetail' );
		$detailList = $scdmodel->getDetail ( $name );
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		// 扩展工具栏操作
		$toolbarextension = $scdmodel->getDetail ( $name, true, 'toolbar' );
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		if ($_REQUEST ['jump'] == "jump") {
			$this->display ( 'indexview' );
		} else {
			$this->display ();
		}
	}
}
?>
