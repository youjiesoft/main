<?php
/**
 * @Title: MisAutoAamModel
 * @Package package_name
 * @Description: todo(动态表单_自动生成-内部表决单)
 * @author 管理员
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2015-08-11 13:47:11
 * @version V1.0
*/
class MisSystemEsblogModel extends CommonModel {
	protected $trueTableName = 'mis_system_esblog';		
	// 字段权限过滤
	protected $_filter = array();
	
	public $_auto =array(
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('createtime','time',self::MODEL_INSERT,'function'),
		);
	
}
?>