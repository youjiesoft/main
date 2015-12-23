<?php
/**
 * @Title: AuthorizeAction
 * @Package 基础配置-授权管理
 * @Description: TODO(授权管理)
 * @author yangxi
 * @company 重庆特米洛科技有限公司˾
 * @copyright 重庆特米洛科技有限公司˾
 * @date 2013-6-15 19:18:54
 * @version V1.0
 */
class AuthorizeAction extends AuthorizeCoreAction {
	
	private $arr=array(
			array(
					'id' =>1,
					'name'=>'权限菜单',
					'pId'=>0,
					'title'=>'权限菜单',
					'open'=>true,
					),
			array(
					'id' =>2,
					'name'=>'用户授权',
					'pId'=>1,
					'title'=>'用户授权',
					'rel' =>'abc',
					'target'=>'ajax',
					'type' =>'post',
					'url'=>'__APP__/Authorize/index/type/1/jump/1/md/User',
					),
			/* array(
					'id' =>3,
					'name'=>'特殊权限',
					'pId'=>1,
					'title'=>'特殊权限',
					'rel' =>'abc',
					'target'=>'ajax',
					'type' =>'post',
					'url'=>'__APP__/Authorize/index/type/3/jump/1/md/MisAuthorizeSpecial',
			), */
			array(
					'id' =>3,
					'name'=>'权限复制',
					'pId'=>1,
					'title'=>'权限复制',
					'rel' =>'abc',
					'target'=>'ajax',
					'type' =>'post',
					'url'=>'__APP__/Authorize/index/type/4/jump/1/md/',
			),
			array(
					'id' =>4,
					'name'=>'组授权',
					'pId'=>1,
					'title'=>'组授权',
					'rel' =>'abc',
					'target'=>'ajax',
					'type' =>'post',
					'url'=>'__APP__/Authorize/index/type/2/catgory/1/jump/1/md/Rolegroup',
			),
			array(
					'id' =>5,
					'name'=>'部门角色',
					'pId'=>1,
					'title'=>'部门角色',
					'rel' =>'abc',
					'target'=>'ajax',
					'type' =>'post',
					'url'=>'__APP__/Authorize/index/type/2/catgory/5/jump/1/md/Rolegroup',
			),
			array(
					'id' =>6,
					'name'=>'人事岗位',
					'pId'=>1,
					'title'=>'人事岗位',
					'rel' =>'abc',
					'target'=>'ajax',
					'type' =>'post',
					'url'=>'__APP__/Authorize/index/type/2/catgory/3/jump/1/md/Rolegroup',
			),
		);
	/** @Title: _filter
	 * @Description: (列表进入的过滤器)
	 * @author
	 * @date 2013-5-31 下午3:59:44
	 * @throws
	 */
	public function _filter(&$map){
		if ($_SESSION["a"] != 1){
			$map['status']=array("gt",-1);
		}
	}
	/**
	 * (non-PHPdoc)
	 * @Description: TODO(重写父类的index方法，实现此处的查询功能)
	 * @see CommonAction::index()
	 */
	public function index(){
		$name=$_REQUEST['md']?$_REQUEST['md']:'User';
		$this->assign('ztree', json_encode($this->arr));
		
		$map = $this->_search ($name);
		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		if ($_REQUEST['type'] == 2){
			$map['status'] = array('gt',0);
		}
		if (! empty ( $name )) {
			$qx_name=$name;
			if(substr($name, -4)=="View"){
				$qx_name = substr($name,0, -4);
			}
			//验证浏览及权限
			if( !isset($_SESSION['a']) ){
				$map=D('User')->getAccessfilter($qx_name,$map);
			}
			$catgory=$_REQUEST['catgory'];
			if($catgory){
				$map['catgory'] = $catgory;
				$this->assign('catgory',$catgory);
			}
			if($name=='user'){
				$this->_list ( "MisHrPersonnelUserDeptView", $map );
			}else{
				$this->_list ( $name, $map );
			}
		}
		$this->assign('module',$name);
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($name);
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		//searchby搜索扩展
		$searchby = $scdmodel->getDetail($name,true,'searchby');
		if ($searchby && $detailList) {
			$searchbylist=array();
			foreach( $detailList as $k=>$v ){
				if(isset($searchby[$v['name']])){
					$arr['id']= $searchby[$v['name']]['field'];
					$arr['val']= $v['showname'];
					array_push($searchbylist,$arr);
				}
			}
			$this->assign("searchbylist",$searchbylist);
		}
		
		$type=$_REQUEST['type'];
		//扩展工具栏操作
		if($type==2 && $catgory==5){
			$toolbarextension = $scdmodel->getDetail('MisOrganizationalSet',true,'toolbar');
		}elseif($type==2 && $catgory==3){
			$toolbarextension = $scdmodel->getDetail('MisRoleGroup',true,'toolbar');
		}else{
			$toolbarextension = $scdmodel->getDetail($name,true,'toolbar');
		}
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		if ($_REQUEST['jump']) {
			if($type==2){
				$this->display('AuthorizeRolegroup');
			}else if($type==3){
				$this->display('AuthorizeSpecial');
			}else if($type==4){
				$this->display('AuthorizeUser');
			}else if($type == 5){
				$this->display('AuthorizeProject');
			}else{
				$this->display('AuthorizeUser');
			}
		} else {
			$this->display();
		}
	}
	public function roleAccess(){
		//获取ztree的ID
		$this->assign("ztree",$_REQUEST['ztree']);
		//获取div的ID
		$this->assign("divid",$_REQUEST['divid']);
		$this->getUserAccess($_REQUEST['divid']);
	}
}
?>