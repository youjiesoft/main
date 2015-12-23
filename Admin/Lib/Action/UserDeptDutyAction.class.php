<?php
/**
 * @Title: file_name
 * @Package package_name
 * @Description: todo(部门职级管理)
 * @author liminggang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2012-12-22 下午3:21:33
 * @version V1.0
 */
class UserDeptDutyAction extends CommonAction {
	
	public function _before_add(){
		//查询公司及部门联动
		$this->assign('deptComList',$this->getCompanyDept());
		$this->assign("companyid",$_REQUEST['companyid']);
		//查询职级
		$MisSystemDutyModel=D('MisSystemDuty');
		$dutylist=$MisSystemDutyModel->where('status = 1')->getField('id,name');
		$this->assign('dutylist',$dutylist);
	}
	public function _after_edit(&$vo){
		//查询公司及部门联动
		$this->assign('deptComList',$this->getCompanyDept());
		//查询职级
		$DutyModel=M('duty');
		$dutylist=$DutyModel->where('status = 1')->getField('id,name');
		$this->assign('dutylist',$dutylist);
		$this->assign('vo',$vo);
	}
	public function _before_insert(){
		//获得部门
		$deptid = $_POST['deptid'];
		//获得职级
		$dutyid = $_POST['dutyid'];
		//判断当前添加的部门职级是否已经存在了。
		
		$name=$this->getActionName();
		$model = D ($name);
		$where = array();
		$where['deptid'] = $deptid;
		$where['dutyid'] = $dutyid;
		$where['userid'] = $_POST['userid'];
		$list = $model->where($where)->find();
		$username = getFieldBy($_POST['userid'],'id','name','user');
		$dutyname = getFieldBy($dutyid,'id','name','duty');
		$deptname = getFieldBy($deptid,'id','name','mis_system_department');
		if($list){
			$this->error($username."已经是".$deptname."的".$dutyname."！ 请勿重复添加");
		}
	}
	
	public function _before_update(){
		$name=$this->getActionName();
		$model = D ($name);
		//判断是否修改了主要职级
		$id=$_POST['id'];
		$map['id'] = $id;
		$map['typeid'] = 1;
		$result=$model->where($map)->find();
		if($result){
			$this->error("主部门和职级请在人事资料修改");
		}
	}
	/**
	 +----------------------------------------------------------
	 * 默认删除操作
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 * @throws ThinkExecption
	 +----------------------------------------------------------
	 */
	public function delete() {
		//删除指定记录
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
				//查询是否属于主要部门和职级
				$map['id'] = $id;
				$map['typeid'] = 1;
				$result=$model->where($map)->find();
				if($result){
					$this->error("主部门和职级不能删除");
				}
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				$list=$model->where ( $condition )->delete();
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