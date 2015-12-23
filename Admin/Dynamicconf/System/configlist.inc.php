<?php 
$configlist = array(
	'auditState' => array(
		'0' => array(
			'id' => '0',
			'name' => '新建',
			'icon' => '__PUBLIC__/Images/icon/order_new.png',
		),
		'1' => array(
			'id' => '-1',
			'name' => '未批准',
			'icon' => '__PUBLIC__/Images/icon/order_decline.png',
		),
		'2' => array(
			'id' => '1',
			'name' => '待审核',
			'icon' => '__PUBLIC__/Images/icon/order_wait.png',
		),
		'3' => array(
			'id' => '2',
			'name' => '审核中',
			'icon' => '__PUBLIC__/Images/icon/order_audit.png',
		),
		'4' => array(
			'id' => '3',
			'name' => '审核完毕',
			'icon' => '__PUBLIC__/Images/icon/order_accept.png',
		),
	),
	'doctime' => array(
		array('name'=>'今    天','icon'=>'__PUBLIC__/Images/icon/order_week.png','format'=>strtotime(date('Y-m-d',time())),'exp'=>'gt'),
		array('name'=>'昨    天','icon'=>'__PUBLIC__/Images/icon/order_day.png','format'=>strtotime('yesterday'),'exp'=>'gt'),
		array('name'=>'7 天内','icon'=>'__PUBLIC__/Images/icon/order_week.png','format'=>strtotime('-7 days'),'exp'=>'gt'),
		array('name'=>'30天内','icon'=>'__PUBLIC__/Images/icon/order_month.png','format'=>strtotime('-30 days'),'exp'=>'gt'),
	),//时间状态
	'lookupGeneralInclude' => array(
		//'MisSalesProject' => array('layoutH'=>212,'tpl'=>'codeName','isauto'=>1),//项目
		'MisSalesCustomer' => array('layoutH'=>246,'tpl'=>'salesCustomer','isauto'=>1),//客户
		//'MisSalesContractmas' => array('layoutH'=>202,'tpl'=>'codeName','isauto'=>1),//销售合同
		//'MisPurchaseContractmas' => array('layoutH'=>202,'tpl'=>'ordernoName','isauto'=>1),//采购合同
		//'MisProjectContractmas' => array('layoutH'=>202,'tpl'=>'codeName','isauto'=>1),//项目合同
		'MisPurchaseSupplier' => array('layoutH'=>150,'tpl'=>'codeName','isauto'=>1),//供应商
	),//时间状态
	'lookupAddSelectInclude' => array(
		'default' => array('tpl'=>'defaultSelect'),//默认
	),//快速新增select
	'auditUser' => '0', //在common.php 1681行使用
	'digits' => '2',  //在common.php 1336行使用     CrmSaleCostFormAction 102行使用  MisProjectCostFormAction  102行使用
	'capacity' => '5',   //在common.php 1709行使用
	'weight' => '7',   //在common.php 1681行使用
);
?>