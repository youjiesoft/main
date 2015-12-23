<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(车辆&&油卡) 
 * @author yuansl 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-7-28 上午9:40:09 
 * @version V1.0
 */
class MisSystemCarOilViewModel extends ViewModel {
	//车辆和油卡
	public $viewFields = array(
			'mis_system_car'=>array(
					'_as'=>'mis_system_car',
					'id',
					'carno',
					'name',
					'oilID',
					'departmentID',
					'_type'=>'LEFT'
					),
			'mis_car_card_bind'=>array(
					'_as'=>'mis_car_card_bind',
					'oil_balance',
					'_on'=>'mis_system_car.oilID=mis_car_card_bind.oil_id',
					'_type'=>'LEFT'
					),
	);
}