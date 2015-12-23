<?php
/**
 * @Title: MisSystemPanelAction
 * @Package 基础配置-面板管理：系统面板管理
 * @Description: TODO()
 * @author jiangx
 * @company 重庆特米洛科技有限公司
 * @copyright 重庆特米洛科技有限公司
 * @date 2013-10-10 16:18:54
 * @version V1.0
 */

class MisSystemReportAction extends CommonAction {
	public function index() {
		//组装树
		$groupModel=D('group');
		$groupList=$groupModel->where("status=1 ")->getField("id,name");
		$tree = array(); // 树初始化
		$tree[] = array(
				'id' => -1,
				'pId' => 0,
				'name' => '组名称',
				'title' => '组名称',
				'rel' => "missystemreportlookupindex",
				'target' => 'ajax',
				'url' => "__URL__/index/jump/1",
				'open' => true
		);
		foreach ($groupList as $gkey=>$gval){
			$tree[] = array(
					'id' => $gkey,
					'pId' => -1,
					'name' => $gval,
					'title' => $gval,
					'rel' => "missystemreportlookupindex",
					'target' => 'ajax',
					'url' => "__URL__/index/jump/1/group_id/".$gkey,
					'open' => true
			);
		}
		$this->assign("tree",json_encode($tree));
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		if($_REQUEST['group_id']){
			$map['group_id']=$_REQUEST['group_id'];
			$this->assign("group_id",$_REQUEST['group_id']);
		}
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
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
		if($_REQUEST['jump']){
			$this->display('indexview');exit;
		}
		if($_REQUEST['type']){
			$this->display ('lookupindex');
		}else{
			$this->display ();
		}
	}
	public function _before_add(){
		$this->assign("group_id",$_REQUEST['modeid']);
		//查询连字符
		$html=getSelectByHtml('roleinexp','select');
		$html= str_replace('"', "'", $html);
		$this->assign("html",$html);
		$this->lookgroupandnode();
		
	}
	public function _before_insert(){
		$this->lookupinsertMap();
	}
	public function update(){
		//获取固定面板
		$model = D('MisSystemReport');
// 		$fixlist = $model->where('status=1 and isbasetab=1')->order("sort asc")->select();
// 		$fixlistarr = array();
// 		foreach ($fixlist as $key => $val) {
// 			//if (method_exists($this, $val['methodname'])) {
// 				$fixlistarr []= $val['id'];
// 			//}
// 		}
 		$this->lookupinsertMap();
// 		if(in_array($_POST['id'],$fixlistarr)) $this->error('该面板不允许修改');
		$name=$this->getActionName();
		$model = D ( $name );
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		$list = $model->save();
		// 更新数据
		if (false !== $list) {
			$this->success ( L('_SUCCESS_'));
		} else {
			$this->error ( L('_ERROR_') );
		}
	}
	public function _after_edit(&$vo){
		$this->assign("isfirst","1");
		$vo['modelid']=getFieldBy($vo['modelname'], "name", "id", "node");
		$vo['group_id']=getFieldBy($vo['modelname'], "name", "group_id", "node");
		$this->lookgroupandnode();
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($vo['modelname']);
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		$showtitle1=array();
		foreach($detailList as $v){
			if(in_array($v['name'],$showtitle)){
				$showtitle1[]=$v['showname'];
			}
		}
		$this->assign("showtitle",$showtitle1);
	}
	function delete(){
		//获取固定面板id集 不允许删除
		$model = D('MisSystemReport');
// 		$fixlist = $model->where('status=1 and isbasepanel=1')->order("sort asc")->select();
// 		$fixlistarr = array();
// 		foreach ($fixlist as $key => $val) {
// 			if (method_exists($this, $val['methodname'])) {
// 				$fixlistarr []= $val['id'];
// 			}
// 		}
		$id=$_POST['id'];
 		$idarr = explode(',',$id);
// 		if(count($idarr)==1){
// 			if(in_array($idarr[0],$fixlistarr)){
// 				$this->error("该面板禁止删除！");
// 			}
// 		}else{
// 			foreach($idarr as $k=>$v){
// 				if(in_array($v,$fixlistarr)) unset($idarr[$k]);
// 			}
// 		}
		if (isset ( $id )) {
			$condition = array ('id' => array ('in', $idarr ) );
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
	
	/**
	 * @Title: leftmessage
	 * @Description: todo(获取左侧数据)
	 * @author libo
	 * @date 2013-12-31 下午4:07:28
	 * @throws
	 */
	public function leftmessage(){
		//获取当前登录人部门id 角色id
		$userModel=M("user");
		$userList=$userModel->where("id=".$_SESSION[C('USER_AUTH_KEY')])->find();
		//查询公司网站
		$model=D("MisSystemCompany");
		$url=$model->where("status=1")->getField('website');
		$this->assign('url',$url);
		//
		$MisSystemAnnouncementModel=D('MisSystemAnnouncement');
		//查询主类型
		$typemodel=D("MisSystemAnnouncementSet");
		if( !isset($_SESSION['a']) ){//不是管理员 只能看到在范围内的公告
			$map['_string']="( (scopetype=2 and ( (find_in_set('".$userList['dept_id']."',deptid) or find_in_set('".$userList['id']."',personid)) or createid=".$_SESSION[C('USER_AUTH_KEY')]."  ) ) or (scopetype=3))";
		}
		$map['commit']=1;
		$map['status']=1;
		$time=time();
		$map['endtime']=array(array('eq',0),array('gt',$time),'or');
		$map['starttime']=array('lt',$time);
		$typeList=$MisSystemAnnouncementModel->where($map)->group('type')->getField("id,type");
		$list=array();
		foreach ($typeList as $key=>$val){
			$map['type']=$val;
			$list[$val]=$MisSystemAnnouncementModel->where($map)->order("top,sendtime desc")->limit('0,'.C("ANNOUNCEMENT_TYPE_NUM"))->select();
		}
		$this->assign("amlist",$list);
		$this->assign('typelist',$typeList);
	}
	private function lookgroupandnode(){
		//组装菜单及操作级
		$NodeModel=D('Node');
		$NodeList=$NodeModel->where("status=1 and level=3")->limit(2)->getField("id,title,group_id");
		//$NodeList=$NodeModel->field("id,title,group_id")->where("status=1 and level=3")->select();

		$groupList=array();
		$newNodelList=array();
		foreach ($NodeList as $key=>$val){
			if(in_array($val['group_id'],array_keys($groupList))){
				$newNodelList['node'][]=array(
						'id'=>$val['id'],
						'name'=>$val['title'],
						'group_id'=>$val['group_id'],
				);
			}else{
				if(!$defaultVal){
					$defaultVal=$val['id'];
				}
				$groupList[$val['group_id']]=1;
				$newNodelList['group'][]=array(
						'id'=>$val['group_id'],
						'name'=>getFieldBy($val['group_id'], "id", "name", "group"),
						'sort'=>getFieldBy($val['group_id'], "id", "sorts", "group"),
				);
				$newNodelList['node'][]=array(
						'id'=>$val['id'],
						'name'=>$val['title'],
						'group_id'=>$val['group_id'],
				);
			}
		}
		$newNodelList['group']=array_merge(array_sort($newNodelList['group'],'sort','asc'));
		$this->assign("defaultVal",$defaultVal);
		$this->assign("nodelist",json_encode($newNodelList));
	}
	public function lookupdetail(){
		$scdmodel = D('SystemConfigDetail');
		$modelName=getFieldBy($_REQUEST['modelid'], "id", "name", "node");
		$detailList = $scdmodel->getDetail($modelName);
		if($_REQUEST['type']==1){
			$modelname=$_REQUEST['modelname'];
			$this->assign("modelname",$modelname);
			$this->display();
		}else{
			$detailList['modelname']=$modelName;
			echo json_encode($detailList);
		}
	}
	public function lookupdetailadd(){
		$scdmodel = D('SystemConfigDetail');
		$modelName=getFieldBy($_REQUEST['modelid'], "id", "name", "node");
		$detailList = $scdmodel->getDetail($modelName);
		if($_REQUEST['type']==1){
			$modelname=$_REQUEST['modelname'];
			$this->assign("modelname",$modelname);
			$this->display();
		}else{
			$detailList['modelname']=$modelName;
			echo json_encode($detailList);
		}
	}
	private function lookupinsertMap(){
		if($_POST['modelid']){
			$_POST['modelname']=getFieldBy($_POST['modelid'], "id", "name", "node");
		}
	}

	
}
?>