<?php
/**
 * @Title: MisSystemPanelDepartmentRoleModel 
 * @Package package_name 
 * @Description: todo(部门面板权限模块) 
 * @author jiangx 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-10-11 下午15:27:13 
 * @version V1.0
 */
class MisSystemPanelDepartmentRoleModel extends CommonModel {
	protected $trueTableName = 'mis_system_panel_department_role';
	/**
	 *
	 * @Title: getUserRolePanel
	 * @Description: todo(获取当前用户部门有权限的面板)
	 * @return unknown
	 * @author renling
	 * @date 2014-9-1 下午3:51:29
	 * @throws
	 */
	public function getDepartRolePanel(){
		$map['departmentid'] = $_SESSION['user_dep_id'];
		$departmentrole = $this->where($map)->find();
		return $departmentrole;
	}
}
?>