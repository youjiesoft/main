<?php
/** 
 * @Title: MisSystemPanelModel 
 * @Package package_name
 * @Description: todo(面板管理：系统面板管理) 
 * @author jiangx
 * @company 重庆特米洛科技有限公司
 * @copyright 重庆特米洛科技有限公司
 * @date 2013-10-10 16:18:54
 * @version V1.0 
*/ 
class MisSystemReportModel extends CommonModel {
	protected $tableName = 'mis_system_report';
	public $_auto	=array(
        array('createid','getMemberId',self::MODEL_INSERT,'callback'),
        array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
        array('createtime','time',self::MODEL_INSERT,'function'),
		array('updatetime','time',self::MODEL_UPDATE,'function'),
		array('sort','getSortNumber',self::MODEL_INSERT,'callback'),
	   	array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
		array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
		array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),

	);
	
	protected function getSortNumber(){
		$sort = $this->max('sort');
		if ($sort) {
			return ++$sort;
		}else {
			return 1;
		}
	}
	/**
	 * 
	 * @Title: getSysPanel
	 * @Description: todo(获取系统默认面板)   
	 * @author renling 
	 * @date 2014-9-1 下午3:36:29 
	 * @throws
	 */
	public  function getSysPanel(){
		$sPanelMap=array();
		$sPanelMap['status']=1;
		//是基础面板
		$sPanelMap['isbasepanel']=1;
		$MisSystemPanelList=$this->where($sPanelMap)->select();
		return $MisSystemPanelList;
	}
}
?>