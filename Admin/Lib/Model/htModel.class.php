<?php
/**
 * @Title: htModel
 * @Package package_name
 * @Description: todo(动态表单_自动生成-合同视图)
 * @author 管理员
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015-01-28 20:34:54
 * @version V1.0
*/
class htModel extends ViewModel {
	public $viewFields = array(
		'mis_auto_fqtej'=>array('_as'=>'mis_auto_fqtej','hezuoyinxing'=>'hzyh','jiekuanhetonghao'=>'jkhth','baozhenghetonghao'=>'bzhth','jiekuanjine'=>'jkje','weituodaikuanjiekuanhetonghao'=>'wtdkjkhth','weituodaikuanweituohetonghao'=>'wtdkwthth','_type'=>'LEFT'),
		'mis_auto_zbotm'=>array('_as'=>'mis_auto_zbotm','zhuhetonghao'=>'zhtbh','fenshu'=>'htfs','_on'=>'mis_auto_zbotm.zhuhetonghao=mis_auto_fqtej.orderno'),
);
}
?>