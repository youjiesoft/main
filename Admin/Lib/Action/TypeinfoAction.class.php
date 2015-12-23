<?php
/**
 * @Title: TypeinfoAction 
 * @Package package_name
 * @Description: todo人事管理-人事配置) 
 * @author xiafengqin 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-6-1 下午3:06:00 
 * @version V1.0
 */
class TypeinfoAction extends CommonAction{
	/**
	 * @Title: _filter 
	 * @Description: todo(构造检索条件) 
	 * @param unknown_type $map  
	 * @author xiafengqin 
	 * @date 2013-6-1 下午3:17:07 
	 * @throws
	 */
	function _filter(&$map){
	    if ($_SESSION["a"] != 1)$map['status']=array("gt",-1);
	}
	/**
	 * @Title: index 
	 * @Description: todo()   
	 * @author xiafengqin 
	 * @date 2013-6-1 下午3:07:03 
	 * @throws
	 */
	function index(){
		//构造人事类型树
		$model = D('Typeinfo');
		$maps['typeid']=1;
		$maps['status']=1;
		$typelist=$model->where($maps)->select();
		$treemiso[]=array(
				'id'=>0,
				'pId'=>-1,
				'name'=>'所有分类',
				'rel'=>'TypeinfoActionBox',
				'target'=>'ajax',
				'title'=>'所有分类',
				'url'=>'__URL__/index/jump/1',
				'open'=>true
		);
		$param['url']="__URL__/index/jump/1/pid/#id#";
		$param['rel']="TypeinfoActionBox";
		$typeTree = $this->getTree($typelist,$param,$treemiso);
		$this->assign('typeTree',$typeTree);
		
		$name = $this->getActionName();
		$map = $this->_search($name);
		//得到节点类型
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
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
		$pid=$this->escapeChar($_REQUEST['pid']);
		if ($pid) {
			$map["pid"] = $pid;
			$this->assign('pid',$pid);
		}
		$map["typeid"]=	2;
		if (! empty ( $name )) {
			$this->_list ( $name, $map );
		}
		if ($_REQUEST['jump']) {
			$this->display('indexview');
		} else {
			$this->display();
		}
	}
	
	/**
	 * @Title: _before_add 
	 * @Description: todo(添加页面前置函数)   
	 * @author xiafengqin 
	 * @date 2013-6-1 下午3:17:27 
	 * @throws
	 */
	public function _before_add(){
		$this->assign('pid',$_REQUEST['pid']);
		$this->everyaddedit();
	}
	/**
	 * @Title: _before_edit 
	 * @Description: todo(打开修改页面前置函数)   
	 * @author xiafengqin 
	 * @date 2013-6-1 下午3:17:48 
	 * @throws
	 */
	public function _before_edit(){
		$this->everyaddedit();
	}
	/**
	 * @Title: everyaddedit 
	 * @Description: todo(此页面的公用函数)   
	 * @author xiafengqin 
	 * @date 2013-6-1 下午3:18:24 
	 * @throws
	 */
	private function everyaddedit(){
		$typeid=$_GET['typeid'];
		$this->assign('typeid',$typeid);
		//判断是新增类型。还是新增类型下面的类容
		if($typeid!=1){
			$model = D('Typeinfo');
			$map['typeid']=1;//类型
			$map['status']=1;
			$pypelist=$model->where($map)->field("id,name")->select();
			$this->assign("plist",$pypelist);
		}
	}
	public function delete() {
		$name=$this->getActionName();
		$model = D ($name);
		$id=$_REQUEST['id'];
		$modelname=$model->where('id='.$id)->find();
		$mname=$modelname['name'];
		//判断父节点下有木有子节点
		$modelList=$model->where("status = 1 and pid=".$id)->select();
		if($modelList){
			$this->error("父节点【".$mname."】下有内容,请先删除子节点");
		}
		//删除指定记录
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
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