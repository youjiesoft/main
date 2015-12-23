<?php
/**
 * @Title: MisSystemMotorcycleAction
 * @Package 基础配置-简单车辆信息
 * @Description: TODO(简单车辆信息维护功能模块)
 * @author yangxi
 * @company 重庆特米洛科技有限公司˾
 * @copyright 重庆特米洛科技有限公司˾
 * @date 2013-1-10 19:18:54
 * @version V1.0
 */
class MisSystemMotorcycleAction extends CommonAction{
	/**
	 * @Title: _filter
	 * @Description: todo(重写CommonAction的_filter方法，传递过滤参数后返回列表页面)
	 * @return string
	 * @author 杨希
	 * @date 2013-5-31 下午3:59:44
	 * @throws
	 */	
	public function _filter(&$map){
		 if ($_SESSION["a"] != 1)$map['status']=array("gt",-1);
	}
}
?>