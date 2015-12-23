<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(协同待办) 
 * @author yuansl 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-4-9 下午6:01:53 
 * @version V1.0
 */
class MisOaItemsWaitForAction extends CommonFlowsAction{
	/**
	 * (non-PHPdoc)
	 * @see CommonAction::index()
	 */
	public function _filter(&$map){
		$map['mis_oa_items.status'] = 1 ;
	}
	/**
	 * @Title: _before_index 
	 * @Description: todo(组装数组传到页面)   
	 * @author yuansl 
	 * @date 2014-4-9 下午10:55:34 
	 * @throws
	 */
	public function index() {
		$tree = array();
		$tree[] = array(
				'id' => 1,
				'pId' => 0,
				'name' => '协同事项',
				'title' => '协同事项',
				'open' => true
		);
		$tree[] = array(
				'id' => 2,
				'pId' => 1,
				'name' => '待办事项',
				'title' => '待办事项',
				'rel' => "misoaitemswaitforview",
				'target' => 'ajax',
				'url' => "__URL__/index/jump/jump/type/0",
				'open' => true
		);
		$tree[] = array(
				'id' => 3,
				'pId' => 1,
				'name' => '已办事项',
				'title' => '已办事项',
				'rel' => "misoaitemswaitforview",
				'target' => 'ajax',
				'url' => "__URL__/index/jump/jump/type/1",
				'open' => true
		);
		$this->assign('typetree',json_encode($tree));
		$map = $this->_search ();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name = $this->getActionName();
		$type = $_REQUEST['type'];
		if($type == 1){//已经办理
			$instanceMap = array(
				'mis_oa_flows_instance.dostatus' => array('egt',2),
				'mis_oa_flows_instance.flowsuser' => $_SESSION [C ( 'USER_AUTH_KEY' )]
			);
			$MisOaFlowsInstance = D("MisOaFlowsInstance");
			$aitemsid = $MisOaFlowsInstance->where($instanceMap)->getField("itemsid",true);
			$aitemsid = array_unique($aitemsid);
			if($aitemsid){
				$map['mis_oa_items.id'] = array('in',$aitemsid);
			} else {
				$map['mis_oa_items.id'] = 0;
			}
			$this->_list ( "MisOaItems", $map );
		} else {
			$map['mis_oa_flows_instance.flowsuser']  = $_SESSION [C ( 'USER_AUTH_KEY' )];
			$map['mis_oa_flows_instance.dostatus'] = array('lt',2);//默认显示未处理的
			$this->_list ( "MisOaItemsWaitForView", $map );
		}
		$this->assign('selectedid',2);
		$this->assign('type',$type);
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
		if($_REQUEST['jump'] == "jump"){
			$this->display('indexview');exit;
		}
		$this->display();
	}
	/**
	 * @Title: edit 
	 * @Description: todo(获取待办事项详细类容)   
	 * @author yuansl 
	 * @date 2014-4-15 上午11:52:03 
	 * @throws
	 */
	public function edit(){
		// 详细信息
		$MisOaFlowsInstance = D("MisOaFlowsInstance");
		$id = $_REQUEST['id'];
		$savemap = array('dostatus'=>0,'id'=>$id);
		$data = array('dostatus'=>1,'updatetime'=>time());
		$MisOaFlowsInstance->where($savemap)->data($data)->save($data);
		$MisOaFlowsInstance->commit();
		$MisOaItemsWaitForViewModel = D("MisOaItemsWaitForView");
		$map['mis_oa_flows_instance.id'] = $id;
		$MisOaFlowsInstanceInfo = $MisOaItemsWaitForViewModel->where($map)->find();
		$this->assign('vo',$MisOaFlowsInstanceInfo);
		// 附件读取
		$this->getAttachedRecordList($MisOaFlowsInstanceInfo["id"],true,true,"MisOaItems");
		// 审核历史记录
		$instancemap['itemsid'] = $MisOaFlowsInstanceInfo["id"];
		$instancemap['itemstable'] = "MisOaItems";
		$instancemap['status'] = "1";
		$instancelist = $MisOaFlowsInstance->where($instancemap)->order("id desc")->select();
		foreach ($instancelist as $k => $v) {
			$instancelist[$k]['attached'] = $this->getAttachedRecordList($v["id"],true,true,"MisOaFlowsInstance",0,false);
		}
		$this->assign("instancelist",$instancelist);
		$this->display();
	}
	public function view(){
		// 详细信息
		$MisOaFlowsInstance = D("MisOaFlowsInstance");
		$id = $_REQUEST['id'];
		$MisOaItemsModel = D("MisOaItems");
		$map['id'] = $id;
		$MisOaItemsInfo = $MisOaItemsModel->where($map)->find();
		$this->assign('vo',$MisOaItemsInfo);
		// 附件读取
		$this->getAttachedRecordList($MisOaItemsInfo["id"],true,true,"MisOaItems");
		// 审核历史记录
		$instancemap['itemsid'] = $MisOaItemsInfo["id"];
		$instancemap['itemstable'] = "MisOaItems";
		$instancemap['status'] = "1";
		$instancelist = $MisOaFlowsInstance->where($instancemap)->order("id desc")->select();
		foreach ($instancelist as $k => $v) {
			$instancelist[$k]['attached'] = $this->getAttachedRecordList($v["id"],true,true,"MisOaFlowsInstance",0,false);
		}
		$this->assign("instancelist",$instancelist);
		$this->display();
	}
	/**
	 * @Title: getdatalist 
	 * @Description: todo(获取列表页数据)   
	 * @author yuansl 
	 * @date 2014-4-10 上午10:16:27 
	 * @throws
	 */
	public function getdatalist(){
		$type = $_REQUEST['type'];//获取的到的事项类型
		$MisOaItemsWaitForViewModel = D("MisOaItemsWaitForView");
		$map['mis_oa_flows_instance.status'] = 1 ;
		$map['mis_oa_flows_instance.flowsuser']  = $_SESSION [C ( 'USER_AUTH_KEY' )];
		$map['mis_oa_flows_instance.dostatus'] = array('lt',3);//默认显示未处理的
		if($_REQUEST['type'] == 1){//已经办理
			$map['mis_oa_flows_instance.dostatus'] = array('egt',2);
		}
// 		$this->assign('type',$type);
		$MisOaFlowsInstancelist = $MisOaItemsWaitForViewModel->where($map)->select();
// 		dump($MisOaFlowsInstancelist);
		$this->assign('list',$MisOaFlowsInstancelist);
	}
	/**
	 * @Title: comfirm 
	 * @Description: todo(这里用一句话描述这个方法的作用)   
	 * @author yuansl 
	 * @date 2014-4-15 下午2:45:41 
	 * @throws
	 */
	public function comfirm(){
		$id = $_REQUEST['id'];
		$this->assign('comfid',$id);
		$this->display();
	}
	/**
	 * @Title: overdata 
	 * @Description: todo(用户确认处理)   
	 * @author yuansl 
	 * @date 2014-4-15 下午3:08:02 
	 * @throws
	 */
	public function lookupoverdata(){
		$id = $_REQUEST['id'];
		$typeid = $_REQUEST['typeid'];
		if($typeid){
			$data['dostatus'] = 2;
		}else{
			$data['dostatus'] = 3;
		}
		$MisOaFlowsInstanceMode= D("MisOaFlowsInstance");
		$data['doinfo'] = $_REQUEST['doinfo'];
		$re = $MisOaFlowsInstanceMode->where("id=".$id)->save($data);
		if($re){
			$this->success("操作成功!");
		}else{
			$this->error("操作失败!");
		}
	}
	/**
	 * @Title: replyItems
	 * @Description: todo(回复)   
	 * @author 杨东 
	 * @date 2014-4-23 下午3:12:58 
	 * @throws 
	*/  
	public function replyItems(){
		$name = "MisOaItems";
		$this->getSystemConfigDetail($name);
		$id = $_REQUEST["id"];
		$vo = D($name)->where("id=".$id)->find();
		//上传附件的展示清单
		$this->getAttachedRecordList($id,true,true,$name);
		$this->assign("vo",$vo);
		$this->display();
	}
	/**
	 * @Title: replyItems
	 * @Description: todo(转发)   
	 * @author 杨东 
	 * @date 2014-4-23 下午3:12:58 
	 * @throws 
	*/  
	public function forwardingItems(){
		$name = "MisOaItems";
		$this->getSystemConfigDetail($name);
		$id = $_REQUEST["id"];
		$vo = D($name)->where("id=".$id)->find();
		//上传附件的展示清单
		$this->getAttachedRecordList($id,true,true,$name);
		$this->assign("vo",$vo);
		$this->display();
	}
}