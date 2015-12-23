<?php
/**
 * @Title: NodeFileDestroy
 * @Package package_name
 * @Description: 节点及文件删除清理模型
 * @author liminggang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-9-3 下午3:33:54
 * @version V1.0
 */
class NodeFileDestroyModel extends NodeModel {
	//指定表
	protected $trueTableName = 'node';
	
	public function getNodeTree($map){
		if($map){
			$map=" status=1  and  ".$map;
		}else{
			$map="status=1";
		}
		//查询全部三级数据
		$NodeTreeList=$this->where($map)->select();
		$nodeTree=array();
		$pname = array();
		foreach($NodeTreeList as $key=>$val){
			$nodeTree[] = array(
					'id'=>$val['id'],
					'name'=>$val['title'],//显示名称
					'title'=>$val['title'],//显示名称
					'ename'=>$val['name'], //model名称
					'pId'=>"-".$val['pid'],
					'url'=>"__URL__/index/jump/1/nodename/".$val['name'],
					'target'=>'ajax',
					'rel'=>'ProcessManageBox',
					'open'=>false
			);
			$pid[] = $val['pid'];
		}
		$pid = array_unique($pid);
		foreach ($pid as $k => $v) {
			if(getFieldBy($v, "id", "name", "node")){
				$nodeTree[] = array(
						'id'=>"-".$v,
						'name'=>getFieldBy($v, "id", "title", "node"),//显示名称
						'title'=>getFieldBy($v, "id", "title", "node"), //model名称
						'pId'=>0,
				);
			}
		}
		return $nodeTree;
	}
	public function getGroupNodeList(){
		//查询系统分组信息
		$model	=	M("Group");
		$list = $model->where("status=1")->order("sorts asc")->select();
		//获取顶级节点ID
		$initpid = $this->where('pid=0')->getField('id');
		// 查询菜单节点
		$map['status'] = 1;
		$map['type'] = array('lt',4);
		$nodelist = $this->where($map)->order('sort asc')->select();
		//存储所有分组下面的所有节点
		$returnarr = array();
		// 第一个循环构造分组节点
		foreach ($list as $k2 => $v2) {
			$newv1 = array();
			$newv1['id'] = -$v2['id'];
			$newv1['pId'] = 0;
			$newv1['title'] = $v2['name']; //光标提示信息
			$newv1['name'] = missubstr($v2['name'],20,true); //结点名字，太多会被截取
			$newv1['url']="__URL__/index/frame/1/group_id/".$v2['id']."/pid/".$initpid;
			$newv1['target']='ajax';
			$newv1['rel']="jbsxNodeBox";
			$newv1['open'] = 'false';
			$returnarr[] = $newv1;
			// 第二个循环构造组分类节点
			foreach($nodelist as $k => $v){
				$newv2 = array();
				if ($v['type'] == 1 && $v['group_id'] == $v2['id']) {
					//面板级
					unset($nodelist[$k]);
					$newv2['id'] = $v['id'];
					$newv2['pId'] = -$v2['id'];
					$newv2['title'] = $v['title']; //光标提示信息
					$newv2['name'] = missubstr($v['title'],20,true); //结点名字，太多会被截取
					$newv2['url']="__URL__/index/frame/1/group_id/".$v['group_id']."/pid/".$v['id'];
					$newv2['target']='ajax';
					$newv2['rel']="jbsxNodeBox";
					$newv2['open'] = 'false';
					$returnarr[] = $newv2;
					// 第三个循环判断模块节点
					foreach($nodelist as $k3 => $v3){
						$newv3 = array();
						if ($v3['type'] == 3 && $v3['pid'] == $v['id']) {
							//模块级
							$newv3['id'] = $v3['id'];
							$newv3['pId'] = $v['id'];
							$newv3['title'] = $v3['title']; //光标提示信息
							$newv3['name'] = missubstr($v3['title'],20,true); //结点名字，太多会被截取
							$newv3['url']="__URL__/index/frame/1/group_id/".$v['group_id']."/pid/".$v3['id'];
							$newv3['target']='ajax';
							$newv3['rel']="jbsxNodeBox";
							$newv3['false'] = 'true';
							$returnarr[] = $newv3;
						}
					}
				}
			}
		}
		return $returnarr;
	}
	
	
	/**
	 +----------------------------------------------------------
	 * 重写删除操作
	 +----------------------------------------------------------
	 * @access private
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 * @throws ThinkExecption
	 +----------------------------------------------------------
	 */	
	public function  delete(){
		//删除指定记录
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			//判断如果非模块的话，是否有子级数据
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				// 超管对已经成为删除状态的数据进行测试无记录的删除
				if($_SESSION['a']){
					$condition["status"] = array ("eq",-1);
					$list=$model->where ( $condition )->delete();
					$condition["status"] = array ("neq",-1);
				}
				$list=$model->where ( $condition )->setField ( 'status', - 1 );
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
}
?>