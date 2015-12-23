<?php
/**
 * @Title: pifufangkuanjihuaViewModel
 * @Package package_name
 * @Description: todo(动态表单_自动生成-批复放款参照视图)
 * @author 管理员
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015-03-13 20:08:54
 * @version V1.0
*/
class pifufangkuanjihuaViewModel extends ViewModel {
	public $viewFields = array(
		'mis_auto_wrmve'=>array('_as'=>'mis_auto_wrmve','orderno','xiangmubianma','shenqingjine','shenqingqixian','pifujine','pifuqixian','danbaobaozhengjinlv','_type'=>'INNER'),
		'mis_auto_wrmve_sub_datatable18'=>array('_as'=>'mis_auto_wrmve_sub_datatable18','id','fangkuanriqi','fangkuanjine','fangkuancishu','_on'=>'mis_auto_wrmve.id = mis_auto_wrmve_sub_datatable18.masid'),
);
}
?>