<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(财务盖章类型管理) 
 * @author yuansl 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-4-25 下午4:04:18 
 * @version V1.0
 */
class MisSealOfRegistrationAction extends CommonAction {
	/**
	 * @Title: _filter
	 * @Description: todo(这里用一句话描述这个方法的作用)
	 * @param unknown_type $map
	 * @author yuansl
	 * @date 2014-4-25 下午4:07:21
	 * @throws
	 */
	public function _filter(&$map){
		if ($_SESSION["a"] != 1)$map['status']=array("gt",0);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CommonAction::index()
	 */
	public function index(){
		$name = $this->getActionName();
		//获取树节点
		$this->gettypetree();
		//列表过滤器，生成查询Map对象
		$map = $this->_search ($name);
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		if($_REQUEST['typeid']){
			$map['typeid'] = $_REQUEST['typeid'];
		}else{
			$map['typeid'] = 1;
		}
		$this->assign('typeid',$map['typeid']);
// 		dump($map);
		//验证浏览及权限
		if( !isset($_SESSION['a']) ){
			$map=D('User')->getAccessfilter($qx_name,$map);
		}
		$this->_list ( $name, $map );
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($name);
		//扩展工具栏操作
		$toolbarextension = $scdmodel->getDetail($name,true,'toolbar');
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		if($_REQUEST['jump']){
			$this->display('indexview');
			exit;
		}
		$this->display('index');
	}
	/**
	 * @Title: gettypetree 
	 * @Description: todo(构造树节点)   
	 * @author yuansl 
	 * @date 2014-4-26 上午9:21:17 
	 * @throws
	 */

	public function gettypetree(){
		//盖章管理主章
		$MisSealOfRegistrationModel = D("MisSealOfRegistration");
		$MisSealOfRegistrationList = $MisSealOfRegistrationModel->where("status = 1")->select();
		$typecount = count($MisSealOfRegistrationList);
		//构造顶级节点
		$tree[] = array(
				'id' => 1,
				'pId' =>  0,
				'name' => '盖章类型',
				'title' => '盖章类型',
				'icon' => "",
				'open' => "true",
				);
		$tree[] = array(
				'id' => 2,
				'pId' =>  1,
				'name' => '主章分类',
				'title' => '',
				'icon' => "",
				'target' => 'ajax',
				'rel' => "missealofregistration",
				'url' => "__URL__/index/jump/2/typeid/1",
				'open' => true
		);
		$tree[] = array(
				'id' => 3,
				'pId' =>  1,
				'name' => '辅章分类',
				'title' => '',
				'icon' => "",
				'rel' => "missealofregistration",
				'target' => 'ajax',
				'url' => "__URL__/index/jump/2/typeid/2",
				'open' => true
		);
// 		foreach($MisSealOfRegistrationList as $vatype){
// 			if($vatype['parentid'] == 0){
// 				$tree[] = array(
// 						'id' => $vatype['id'],
// 						'pId' =>  $vatype['pid'],
// 						'name' => $vatype['name'],
// 						'title' => $vatype['name'],
// 						//'rel' => "missealofregistration",
// 						//'target' => 'ajax',
// 						'icon' => "",
// 						//'url' => "__URL__/index/jump/2/typeid/".$vatype['id'],
// 						'open' => true
// 				);
// 			}else{
// 				$tree[] = array(
// 						'id' => $vatype['id'],
// 						'pId' =>  $vatype['pid'],
// 						'name' => $vatype['name'],
// 						'title' => $vatype['name'],
// 						'icon' => "",
// 						'open' => true
// 				);
// 			}
// 		}
		$this->assign("tree",json_encode($tree));
	}
	/**
	 * @Title: _before_edit 
	 * @Description: todo(这里用一句话描述这个方法的作用)   
	 * @author yuansl 
	 * @date 2014-4-26 上午10:20:50 
	 * @throws
	 */
	public function _before_edit(){
		$id = $_REQUEST['id'];
		$this->assign('createid',$_SESSION[C('USER_AUTH_KEY')]);
		$this->assign('createid',time());
		$MisSealOfRegistrationModel = D("MisSealOfRegistration");
		$typeinfor = $MisSealOfRegistrationModel->where("id  = ".$id)->find();
		$this->assign('vo',$typeinfor);
		
	}
	/**
	 * @Title: _before_add 
	 * @Description: todo(这里用一句话描述这个方法的作用)   
	 * @author yuansl 
	 * @date 2014-4-26 上午10:30:45 
	 * @throws
	 */
	public function _before_add(){
		if($_REQUEST['typeid']){
			$this->assign('typeid',$_REQUEST['typeid']);
		}
		$this->assign('createid',$_SESSION[C('USER_AUTH_KEY')]);
		$this->assign('createtime',time());
	}
}