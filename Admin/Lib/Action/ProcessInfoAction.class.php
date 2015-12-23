<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(系统流程信息) 
 * @author mashihe 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-6-3 下午3:36:19 
 * @version V1.0
 */
class ProcessInfoAction extends CommonAction {
	function insert() {
		$name=$this->getActionName();
		$model = D ($name);
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		import("ORG.Util.Cookie");
		//保存当前数据对象
		$list=$model->add();
		if ($list !== false) { //保存成功
			Cookie::set("ProcessNowSelected",$list);
			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success( L('_SUCCESS_') );
		} else {
			$this->error( L('_ERROR_') );
		}
	}
	public function _before_add(){
		$model = M("process_type");
		$typelist = $model->where("status = 1")->select();
		$this->assign ( 'typelist',$typelist);
	}
	public function _before_edit(){
		$model = M("process_type");
		$typelist = $model->where("status = 1")->select();
		$this->assign ( 'typelist',$typelist);
	}
	public function _before_update() {
		Cookie::set("ProcessNowSelected",$_POST['id']);
	}
	public function accredit() {
		$model = D('ProcessRelation');
		if ($_REQUEST['step'] == 0) {
			/**
			 * 按用户授权
			 */
			if(!$_REQUEST['userid']) {
				$this->error( L('请选择用户！') );
			}
			$data = array (
				'step'=>$_REQUEST['step'],
				'types' => 0,
				'duty' => '',
				'role' => '',
				'userid' => implode(",", $_REQUEST['userid']),
				'remark' => $_REQUEST['remark'],
				'updatetime' => time(),
				'updateid' => $_SESSION[C('USER_AUTH_KEY')]
			);
		} else if($_REQUEST['step'] == 1) {
			if(!$_REQUEST['duty']) {
				$this->error( L('请选择职级！') );
			}
			if($_POST['types'] == 0){
				$_POST['role'] = '';
			}
			$data = array (
				'deptupleves'=> $_POST['deptupleves'],
				'types' => $_POST['types'],
				'step'=> $_POST['step'],
				'duty' => implode(",",$_POST['duty']),
				'role' => implode(",",$_POST['role']),
				'userid' => '',
				'remark' => $_POST['remark'],
				'updatetime' => time(),
				'updateid' => $_SESSION[C('USER_AUTH_KEY')]
			);
		} else if($_REQUEST['step'] == 2) {
			if(!$_REQUEST['duty']) {
				$this->error( L('请选择项目人员！') );
			}
			if($_REQUEST['types'] == 0){
				$_REQUEST['role'] = '';
			}
			$data = array (
				'types' => 0,
				'step'=> $_REQUEST['step'],
				'duty' => implode(",",$_REQUEST['duty']),
				'role' => '',
				'userid' => '',
				'remark' => $_REQUEST['remark'],
				'updatetime' => time(),
				'updateid' => $_SESSION[C('USER_AUTH_KEY')]
			);
		} else if($_REQUEST['step'] == 3) {
			if(!$_REQUEST['duty']) {
				$this->error( L('请选择项目项目审核角色！') );
			}
			if($_REQUEST['types'] == 0){
				$_REQUEST['role'] = '';
			}
			$data = array (
				'types' => 0,
				'step'=> $_REQUEST['step'],
				'duty' => implode(",",$_REQUEST['duty']),
				'role' => '',
				'userid' => '',
				'remark' => $_REQUEST['remark'],
				'updatetime' => time(),
				'updateid' => $_SESSION[C('USER_AUTH_KEY')]
			);
		}else if($_REQUEST['step'] == 4){
			$data = array (
				'deptupleves'=> $_POST['deptupleves'],
				'types' => $_POST['types'],
				'step'=> $_POST['step'],
				'duty' => implode(",",$_POST['duty']),
				'role' => implode(",",$_POST['role']),
				'userid' => '0',
				'remark' => $_POST['remark'],
				'updatetime' => time(),
				'updateid' => $_SESSION[C('USER_AUTH_KEY')]
			);
		}
		//判断是否并行，如果并行则 绑定并行节点标志
		$parallel = $_POST['parallel'];
		$data['parallel']=$parallel;
		$data['parallelid']=rand(10000,99999);;
		
		$list = $model->where(" id=".$_POST['id'])->save($data);
		if ($list !== false) { //保存成功
			Cookie::set("ProcessNowSelected",$_POST['pid']);
			$this->success( L('_SUCCESS_') );
		} else {
			$this->error( L('_ERROR_') );
		}
	}
	public function _empty() {
		$this->error("您访问的页面不存在！");
	}
}
?>