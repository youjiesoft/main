<?php
/**
 * @Title: MisAutoEweModelView
 * @Package package_name
 * @Description: todo(动态表单_自动生成-单据提醒)
 * @author 管理员
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2015-07-20 14:55:22
 * @version V1.0
*/
class MisAutoEweViewModel extends ViewModel {
			
	function __construct(){
		parent::__construct();
		$arr = getModelFilterByNodeSetting();
		if(is_array($arr)){
			$this->_filter = $arr;
		}
	}
	// 字段权限过滤
	protected $_filter = array();
		public $viewFields = array(
	'mis_auto_oyljp'=>array('id','danjumingchen','tixingtiaojian','tixingnarong','operation','name','danjutable','danjutablechina','userid','userinfo','noticetype','status','intercycle','intercycleulit','type','danjumingchenchina','checkfields','_type'=>'LEFT'),
);
}
?>