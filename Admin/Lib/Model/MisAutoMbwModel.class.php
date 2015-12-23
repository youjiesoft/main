<?php
/**
 * @Title: MisAutoMbwModel
 * @Package package_name
 * @Description: todo(动态表单_自动生成-流程转授)
 * @author 汤文志
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2015-07-24 14:06:31
 * @version V1.0
*/
class MisAutoMbwModel extends MisAutoMbwExtendModel {
	protected $trueTableName = 'mis_auto_guikc';		
	// 字段权限过滤
	protected $_filter = array();
	
	public $_auto =array(
		array("createid","getMemberId",self::MODEL_INSERT,"callback"),
		array("updateid","getMemberId",self::MODEL_UPDATE,"callback"),
		array("createtime","time",self::MODEL_INSERT,"function"),
		array("updatetime","time",self::MODEL_UPDATE,"function"),
		array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
		array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
		array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
		array('allnode','getActionName',self::MODEL_INSERT,'callback'),
		array('zhuanshouren','setnull',self::MODEL_BOTH,'callback'),
		array('zhuanshougei','setnull',self::MODEL_BOTH,'callback'),
		array('shengxiaoriqi','strtotime',self::MODEL_BOTH,'function'),
		array('shengxiaoriqi','setnull',self::MODEL_BOTH,'callback'),
		array('shixiaoriqi','strtotime',self::MODEL_BOTH,'function'),
		array('shixiaoriqi','setnull',self::MODEL_BOTH,'callback'),
	);
	public $_validate=array(
		array('orderno,status','','单号已经存在',self::MUST_VALIDATE,'unique',self::MODEL_BOTH),
		array('orderno','require','单号必须'),
	);
}
?>