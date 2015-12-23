<?php
class MisSystemClientChangeRoleModel extends CommonModel{
	protected $trueTableName = 'mis_system_client_change_role';
	
	public $_auto =array(
			array("createid","getMemberId",self::MODEL_INSERT,"callback"),
			array("updateid","getMemberId",self::MODEL_UPDATE,"callback"),
			array("createtime","time",self::MODEL_INSERT,"function"),
			array("updatetime","time",self::MODEL_UPDATE,"function"),
			array('acquiretime','strtotime',self::MODEL_BOTH,'function'),
			array('endtime','strtotime',self::MODEL_BOTH,'function'),
			array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
	);

	public function getAname($authId){
		import ( '@.ORG.RBAC' );
		$groupList = RBAC::getFileGroupAccessList ();
		// 查询菜单分组
		$model = M ( "group" );
		//管理员排除验证
		if (! isset ( $_SESSION ['a'] )) {
			$map ['status'] = 1;
			if ($groupList) {
				$map ['id'] = array ( " in ", $groupList);
			} else {
				$map ['id'] = 0;
			}
		}else{
			$map ['status'] = array('gt',0);
		}
		$list = $model->where ( $map )->order ( "sorts asc" )->select ();
		$pModel = D("Public");
		$nlist = array();
		foreach($list as $key=>$val){
			$nlist[$val['id']] = $pModel->menuLeftTree($val['id']);
		}
		return $nlist;
	}
}