<?php
/**
 * @Title: MisSystemDBInitializationAction
 * @Package package_name
 * @Description: todo(数据库初始化)
 * @author jiangx
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-8-27 上午10:05:01
 * @version V1.0
 */
class MisSystemDBInitializationAction extends CommonAction {
	/*
	 * 列出不必清除数据的数据表
	 */
	private $tablelist = array(
		'group' => 'group',
		'group_index_setting' => 'group_index_setting',
		'message_type' => 'message_type',
		'mis_apps_modules' => 'mis_apps_modules',
		'mis_import_excel' => 'mis_import_excel',
		'mis_import_excel_sub' => 'mis_import_excel_sub',
		'mis_import_special' => 'mis_import_special',
		'mis_inventory_domain' => 'mis_inventory_domain',
		'mis_inventory_order_types' => 'mis_inventory_order_types',
		'mis_finance_order_types' => 'mis_finance_order_types',
		'mis_order_types' => 'mis_order_types',
		'mis_payment_condition' => 'mis_payment_condition',
		'mis_payment_method' => 'mis_payment_method',
		'mis_payment_settlemethod' => 'mis_payment_settlemethod',
		'mis_product_unit' => 'mis_product_unit',
		'mis_report_center' => 'mis_report_center',
		'mis_report_configuration' => 'mis_report_configuration',
		'mis_sales_site' => 'mis_sales_site',
		'mis_system_areas' => 'mis_system_areas',
		'mis_system_panel'=>'mis_system_panel',
		'mis_system_data_penetrate' => 'mis_system_data_penetrate',
		'mis_system_data_penetrate_sub' => 'mis_system_data_penetrate_sub',
		'node' => 'node',
		'nodecategory' => 'nodecategory',
		'nodetype' => 'nodetype',
		'process_info' => 'process_info',
		'process_relation' => 'process_relation',
		'process_template' => 'process_template',
		'process_type' => 'process_type',
		'role' => 'role'
	);
	/**
	 * @Title: _before_index
	 * @Description: todo(index前置函数)
	 * @author jiangx
	 * @throws
	*/
	public function _before_index(){
		$tables = array();
		$tables[] = true;
		$this->assign('tables', $tables);
		$this->assign('tablelist', $this->tablelist);
	}
	/**
	 * @Title: todoDBInit
	 * @Description: todo(清除数据表数据)
	 * @author jiangx
	 * @throws
	*/
	public function todoDBInit(){
		//获取当前数据库的所有表名
		$database = C ('DB_NAME');
		$model = M("INFORMATION_SCHEMA.TABLES",'','',1);
		$map['_string'] = " TABLE_SCHEMA = '".$database."'";
		$tableslist = $model->where($map)->field($field)->getfield('TABLE_NAME,TABLE_COMMENT');
		//不会被清空的数据表
		$tables = $this->tablelist;
		foreach ($_REQUEST['tables'] as $val) {
			$tables[$val] = $val;
		}
		foreach ($tableslist as $key => $val) {
			if ($tables[$key]) {
				continue;
			}
			$tablemodel = M($key);
			$sql = "DELETE FROM `". $key. "`";
			$result = $tablemodel->execute($sql);
			if ($result === false) {
				$this->error ( L('_ERROR_') );
			}
		}
		//初始化一个admin账号 姓名admin 密码 admin
		if (!isset($tableslist['user'])) {
			$this->error ( L('数据库没有user数据表，不能初始化admin用户') );
		}
		$usermodel = M("user");
		$map = array();
		$map['account'] = 'admin';
		$adminuser = $usermodel->where($map)->find();
		if (!$adminuser) {
			$_POST['account'] = 'admin';
			$_POST['name'] = 'admin';
			$_POST['password'] = md5('admin');
			unset($_POST['tables']);
			if (false === $usermodel->create ()) {
				$this->error ( $usermodel->getError () );
			}
			$list=$usermodel->add ();
			if ($list === false) {
				$this->error ( L('初始化user用户失败') );
			}
		}
		$this->transaction_model->commit();//事务提交
		if ($adminuser) {
			//注销登录
			$data = array();
			//$data['last_login_time'] ="";
			$data['isonline'] = 0;
			$data['sessionid'] = "";
			$data['leavetime'] = time();
			$data['id']	= $_SESSION[C('USER_AUTH_KEY')];
			$usermodel->save($data);
			$usermodel->commit();		
		}
		unset($_SESSION);
		Cookie::delete("userinfo");
		Cookie::clear();
		session_destroy();
		$this->success('清空数据表成功！');
		$this->redirect('/Public/login/') ;
	}
}
?>