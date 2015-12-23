<?php
/**
 * @Title: MisTaskSetupAction
 * @Package 任务管理-任务管理设置
 * @Description: TODO(任务管理设置)
 * @author jiangx
 * @company 重庆特米洛科技有限公司˾
 * @copyright 重庆特米洛科技有限公司˾
 * @date 2013-08-09
 * @version V1.0
 */   
class MisTaskSetupAction extends CommonAction{
    
    public function index(){
	$setupModel = D("MisTasksColorCoded");
	$map = array();
	$map['status'] = 1;
	$map['createid'] = $_SESSION[C('USER_AUTH_KEY')];
	$setupinfo = $setupModel->where($map)->find();
	$this->assign('setupinfo', $setupinfo);
	$this->display();
    }
	
    function update() {
		$model = D("MisTasksColorCoded");
		if ($_POST['id']) {
			if (false === $model->create ()) {
				$this->error ( $model->getError () );
			}
			// 添加数据
			$list=$model->save ();
			if (false !== $list) {
				$this->success ( L('_SUCCESS_'));
			} else {
				$this->error ( L('_ERROR_') );
			}
		} else {
			unset($_POST['id']);
			if (false === $model->create ()) {
				$this->error ( $model->getError () );
			}
			// 添加数据
			$list=$model->add ();
			if (false !== $list) {
				$this->success ( L('_SUCCESS_'));
			} else {
				$this->error ( L('_ERROR_') );
			}
		}
	}
    
}
?>