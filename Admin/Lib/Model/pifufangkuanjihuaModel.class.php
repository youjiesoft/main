<?php
/**
 * @Title: pifufangkuanjihuaModel
 * @Package package_name
 * @Description: todo(动态表单_自动生成-批复放款参照视图)
 * @author 管理员
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015-01-29 16:10:13
 * @version V1.0
*/
class pifufangkuanjihuaModel extends ViewModel {
	public $viewFields = array(
		'mis_auto_wrmve'=>array('_as'=>'mis_auto_wrmve','_type'=>'INNER'),
		'mis_auto_wrmve_sub_datatable18'=>array('_as'=>'mis_auto_wrmve_sub_datatable18','_on'=>'mis_auto_wrmvemis_auto_wrmve_sub_datatable18.id= mis_auto_wrmve_sub_datatable18mis_auto_wrmve_sub_datatable18.masid'),
		''=>array('_as'=>'','orderno','xiangmubianma',),
);
}
?>