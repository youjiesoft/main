<?php
/**
 * @Title: MisProductCodeViewModel 
 * @Package package_name 
 * @Description: todo(物料视图模型) 
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2012-1-12 上午11:06:05 
 * @version V1.0
 * 原始sql:
 select `mis_system_department`.`id`,`mis_system_department`.`name`,`mis_system_department`.`parentid`  from `mis_system_car` as `mis_system_car` left join `mis_system_department` as `mis_system_department` on `mis_system_car`.`departmentID`= `mis_system_department`.`id` group by `mis_system_department`.`id`
 */
class MisSystemCarViewModel extends ViewModel{
	//物料视图
	public $viewFields = array(
		'MisSystemCar'=>array('_as'=>'mis_system_car','_type'=>'LEFT'),
		'MisSystemDepartment' => array('_as'=>'mis_system_department','id','name','parentid','_on'=>'mis_system_car.departmentID=mis_system_department.id'),
	);

}
