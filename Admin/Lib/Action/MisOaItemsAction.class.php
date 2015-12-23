<?php
/** 
 * @Title: MisOaItemsAction 
 * @Package package_name
 * @Description: todo(协同办公事项) 
 * @author 杨东 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-3-29 下午1:59:46 
 * @version V1.0 
*/ 
class MisOaItemsAction extends CommonFlowsAction {
	/* (non-PHPdoc) 过滤条件
	 * @see CommonAction::index()
	*/
	public function _filter(&$map){
		if ($_SESSION["a"] != 1) $map['status']=array("gt",-1);
		//筛选不同分类的结果显示出来
		switch ($_REQUEST['type']) {
			case 1:
				$map['dealstatus']=2;break;
			case 2:
				$map['dealstatus']=1;break;
		}
	}
	
	/**
	 * @Title: index
	 * @Description: todo(上传附件列表)
	 * @author yangl
	 * @date 2014-04-02
	 * @throws
	 */
	public function index() {
		$type = $_REQUEST['type'];
		if(!$type) $type = 1;
		//手动组装个数组，生成左侧树型结构
		//创建左边菜单的树
		$tree = array();
		$tree[] = array(
				'id' => 0,
				'name' => '发起事项',
				'title' => '发起事项',
				'open' => true
			);
		$tree[] = array(
				'id' => -1,
				'name' => '处理事项',
				'title' => '处理事项',
				'open' => true
			);
		$tree[] = array(
				'id' => 1,
				'pId' => 0,
				'name' => '已发事项',
				'title' => '已发事项',
				'rel' => "misoaitemsview",
				'target' => 'ajax',
				'url' => "__URL__/index/jump/jump/type/1",
				'open' => true
		);
		$tree[] = array(
				'id' => 2,
				'pId' => 0,
				'name' => '待发事项',
				'title' => '待发事项',
				'rel' => "misoaitemsview",
				'target' => 'ajax',
				'url' => "__URL__/index/jump/jump/type/2",
				'open' => true
		);
		$tree[] = array(
				'id' => 3,
				'pId' => -1,
				'name' => '待办事项',
				'title' => '待办事项',
				'rel' => "misoaitemsview",
				'target' => 'ajax',
				'url' => "__URL__/index/jump/jump/type/3",
				'open' => true
		);
		$tree[] = array(
				'id' => 4,
				'pId' => -1,
				'name' => '已办事项',
				'title' => '已办事项',
				'rel' => "misoaitemsview",
				'target' => 'ajax',
				'url' => "__URL__/index/jump/jump/type/4",
				'open' => true
		);
		$this->assign('typetree',json_encode($tree));
		$name = $this->getActionName();
		$map = $this->_search ();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		if($type < 3){
			$map["createid"]=$_SESSION[C('USER_AUTH_KEY')];
		}
		if($type == 4){
			//已经办理
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
		} else if($type == 3){
			// 待办任务列表
			$map['mis_oa_flows_instance.flowsuser']  = $_SESSION [C ( 'USER_AUTH_KEY' )];
			$map['mis_oa_flows_instance.dostatus'] = array('lt',2);//默认显示未处理的
			$this->_list ( "MisOaItemsWaitForView", $map );
		} else {
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
		$this->assign("type",$type);
		if( intval($_POST['dwzloadhtml']) ){
			$this->display("dwzloadindex");exit;
		}
		//首页收件箱调用方法，为ajax调用
		if ($_GET['type'] == "ajaxcall") {
			$this->display ("ajax_index");exit;
		}
		if($_REQUEST['jump'] == "jump"){
			$this->display('indexview');exit;
		}
		$this->display ();
	}
	/**
	 * @Title: attlist
	 * @Description: todo(上传附件列表)
	 * @author eagle
	 * @date 2014-04-02 
	 * @throws
	 */
	private function attlist(){
		$map["status"]  =1;
		$map["orderid"] =$_REQUEST["id"];
		$map["type"] =73;
		$m=M("mis_attached_record");
		$attarry=$m->where($map)->select();
		$this->assign('attarry',$attarry);
	}
	
	/**
	 * @Title: _before_edit
	 * @Description: todo(打开修改页面前置函数)
	 * @author eagle
	 * @date 2013-5-31 下午5:33:33
	 * @throws
	 */
	public function _before_edit(){
		//上传附件的展示清单
		$this->getAttachedRecordList($_REQUEST["id"]);
	}
	/**
	 * @Title: view
	 * @Description: todo(查看页面)   
	 * @author 杨东 
	 * @date 2014-4-23 下午1:51:22 
	 * @throws 
	*/  
	public function view(){
		// 详细信息
		$MisOaFlowsInstance = D("MisOaFlowsInstance");
		$id = $_REQUEST['id'];
		//读取动态配制
		$this->getSystemConfigDetail("MisOaItems");
		$MisOaItemsModel = D("MisOaItems");
		$map['id'] = $id;
		$MisOaItemsInfo = $MisOaItemsModel->where($map)->find();
		$this->assign('vo',$MisOaItemsInfo);
		// 附件读取
		$this->getAttachedRecordList($MisOaItemsInfo["id"]);
		//判断是否存在回复信息
		$datareply['itemsid'] = $MisOaItemsInfo["id"];
		$MisOaItemsReplyDao = M("mis_oa_items_reply");
		$replylist=$MisOaItemsReplyDao->where($datareply)->order("id desc")->select();
		
		// 审核历史记录
		$instancemap['itemsid'] = $MisOaItemsInfo["id"];
		$instancemap['itemstable'] = "MisOaItems";
		$instancemap['status'] = "1";
		$instancelist = $MisOaFlowsInstance->where($instancemap)->order("id desc")->select();
		foreach ($instancelist as $k => $v) {
			$instancelist[$k]['attached'] = $this->getAttachedRecordList($v["id"],true,true,"MisOaFlowsInstance",0,false);
			//循环回复内容
			foreach($replylist as $key=>$val){
				$val['send'] = false;;
				if($val['senduserid']!=$_SESSION[C('USER_AUTH_KEY')]){
					$val['send'] = true;
				}
				$val['attached'] =  $this->getAttachedRecordList($val["id"],true,true,"MisOaItemsReply",0,false);
				if($v['id'] == $val['instanceid']){
					$instancelist[$k]['reply'][] = $val;
				}
			}
		}
// 		print_r($instancelist);
		$this->assign("instancelist",$instancelist);
		$this->display();
	}
	/**
	 * @Title: audit
	 * @Description: todo(审核)
	 * @author yuansl
	 * @date 2014-4-15 上午11:52:03
	 * @throws
	 */
	public function audit(){
		// 详细信息
		$MisOaFlowsInstance = D("MisOaFlowsInstance");
		$id = $_REQUEST['id']; //获取的是mis_oa_flows_instance 表的ID
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
		
		//判断是否存在回复信息
		$datareply['itemsid'] = $MisOaFlowsInstanceInfo["id"];
		$MisOaItemsReplyDao = M("mis_oa_items_reply");
		$replylist=$MisOaItemsReplyDao->where($datareply)->order("id desc")->select();
		
		// 审核历史记录
		$instancemap['itemsid'] = $MisOaFlowsInstanceInfo["id"];
		$instancemap['itemstable'] = "MisOaItems";
		$instancemap['status'] = "1";
		$instancelist = $MisOaFlowsInstance->where($instancemap)->order("id desc")->select();
		foreach ($instancelist as $k => $v) {
			$instancelist[$k]['attached'] = $this->getAttachedRecordList($v["id"],true,true,"MisOaFlowsInstance",0,false);
			//循环回复内容
			foreach($replylist as $key=>$val){
				$val['send'] = false;
				$val['attached'] =  $this->getAttachedRecordList($val["id"],true,true,"MisOaItemsReply",0,false);
				if($v['id'] == $val['instanceid']){
					$instancelist[$k]['reply'][] = $val;
				}
			}
		}
		$this->assign("instancelist",$instancelist);
		$this->display();
	}
	/**
	 * @Title: _after_insert
	 * @Description: todo(插入后置函数)
	 * @param id $id
	 * @author eagle
	 * @date 2013-5-31 下午5:40:13
	 * @throws
	 */
	public function _after_insert($id){
		if($id){
			$this->swf_upload($id,90);
			$fileid = $_REQUEST['itemsuploadfile'];
			if(count($fileid)>0){
				$attModel = D("MisAttachedRecord");
				$attMap = array("id"=>array("in",$fileid));
				$attList = $attModel->where($attMap)->select();
				$data = array();
				foreach ($attList as $k => $v) {
					$v['orderid'] = $id;
					$v['tableid'] = $id;
					$v['createid'] = $_SESSION[C('USER_AUTH_KEY')];
					$v['createtime'] = time();
					$v['updateid'] = $_SESSION[C('USER_AUTH_KEY')];
					$v['updatetime'] = time();
					unset($v['id']);
					$data[] = $v;
				}
				if(count($data)>0) $attModel->addAll($data);
			}
		}
	}
	/**
	 * @Title: _after_update
	 * @Description: todo(修改后置函数)
	 * @author eagle
	 * @date 2013-5-31 下午5:41:20
	 * @throws
	 */
	public function _after_update(){
		if ($_POST['id']) {
			$this->swf_upload($_POST['id'],90);
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
	/**
	 * @Title: flowShow
	 * @Description: todo(流程预览)   
	 * @author 杨东 
	 * @date 2014-4-1 下午2:51:08 
	 * @throws 
	*/  
	public function flowShow(){
		$data = array();
		$data[] = array("id"=>10,"name"=>"范家欣","to"=>array(2),"key"=>1);
		$data[] = array("id"=>15,"name"=>"郭丽燕","to"=>array(3,4),"key"=>2);
		$data[] = array("id"=>21,"name"=>"吴庆东","key"=>3);
		$data[] = array("id"=>25,"name"=>"郑伟","key"=>4);
		$jsondata = $this->setDataJson($data);
		$this->assign("data",$jsondata);
		$this->display();
	}
}
?>