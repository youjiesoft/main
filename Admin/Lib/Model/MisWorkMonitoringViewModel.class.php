<?php
/**
 * 
 * @Title: MisWorkMonitoringViewModel 
 * @Package package_name
 * @Description: todo(工作监控关联节点表  首页专用) 
 * @author renling 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-3-25 下午5:00:54 
 * @version V1.0
 */ 
class MisWorkMonitoringViewModel extends ViewModel {
	//用户和员工视图
	public $viewFields = array(
        'mis_work_monitoring' => array('_as'=>'mis_work_monitoring','id'=>'mid','tableid','tablename','userid','dostatus','dotime','orderno','createid','createtime','curNodeUser','_type'=>'LEFT'),
        'node'=>array('_as'=>'node' ,'id','name', 'title','pid','level','_on'=>'mis_work_monitoring.tablename=node.name'),
    );		
	
	
}
?>