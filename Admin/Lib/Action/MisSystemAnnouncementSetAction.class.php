<?php
/**
 * @Title: MisSystemAnnouncementSet
 * @Package package_name 
 * @Description: todo(系统公告设置) 
 * @author libo 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-1-2 下午5:13:07 
 * @version V1.0
 */
class MisSystemAnnouncementSetAction extends CommonAction{
	public function _filter(&$map){
		if(!isset($_SESSION['a']) ){
			$map['status']=1;
		}
		$this->assign('pid',$_REQUEST['pid']);
		$map['typeid']=2;//只有子节点
		if($_REQUEST['pid']){
			$map['pid']=$_REQUEST['pid'];
		}
	}
	public function _before_index(){
		if(!$_REQUEST['jump']){
			$this->getsettree();
		}
	}
	public function index() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
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
		$this->display ();
		return;
	}
	/**
	 * @Title: getbasesettree
	 * @Description: todo(左侧树结构)
	 * @author libo
	 * @date 2013-12-27 下午4:44:10
	 * @throws
	 */
	function getsettree(){
		$model = D('MisSystemAnnouncementSet');
		$map['typeid']=1;
		$map['status']=1;
		$typelist=$model->where($map)->select();
		$treemiso[]=array(
				'id'=>0,
				'pId'=>-1,
				'name'=>'系统公告',
				'rel'=>'MisSystemAnnouncementSetBox',
				'target'=>'ajax',
				'title'=>'系统公告',
				'url'=>'__URL__/index/jump/1',
				'open'=>true
		);
		$param['url']="__URL__/index/jump/1/pid/#id#";
		$param['rel']="MisSystemAnnouncementSetBox";
		$typeTree = $this->getTree($typelist,$param,$treemiso);//构造树
		$this->assign('typeTree',$typeTree);
	}
	public function _before_add(){
		$typeid=$_REQUEST['typeid'];
		$this->assign('typeid',$typeid);
		//查询父节点
		$model=D("MisSystemAnnouncementSet");
		$map['status']=1;
		$map['typeid']=1;
		$modelList=$model->where($map)->select();
		$this->assign('plist',$modelList);
		$this->assign('pid',$_REQUEST['pid']);
	}
	public function _before_edit(){
		$this->assign('stepType',$_GET['stepType']);
		//查询父节点
		$model=D("MisSystemAnnouncementSet");
		$map['status']=1;
		$map['typeid']=1;
		$modelList=$model->where($map)->select();
		$this->assign('plist',$modelList);
		$id=$_REQUEST['id'];
		$typelist=$model->where("id=".$id)->find();
		$typeid=$typelist['typeid'];
		$this->assign('typeid',$typeid);
	}
	public function delete() {
		$name=$this->getActionName();
		$model = D ($name);
		$id=$_REQUEST['id'];
		if($_REQUEST['typeid']){
			if($id==-1){
				$this->error("顶级节点不能删除");
			}
			$modelname=$model->where('id='.$id)->find();
			$mname=$modelname['name'];
			//判断父节点下有木有子节点
			$modelList=$model->where("status = 1 and pid=".$id)->select();
			//判断节点下是否有内容
			$MSAmodel=D("MisSystemAnnouncement");
			$map["_string"]="(status=1 and (type=".$id." or ptype=".$id."))";
			$annlist=$MSAmodel->where($map)->select();
			if($modelList){//父节点下有子节点
				$this->error("【".$mname."】下有子节点,请先删除子节点");
			}
			if($annlist){//节点下有内容
				$this->error("【".$mname."】下有内容,请先删除内容");
			}
		}
		//删除指定记录
		if (! empty ( $model )) {
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