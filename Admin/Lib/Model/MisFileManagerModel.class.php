<?php 
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(文档管理模型) 
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-8-15 下午4:27:15 
 * @version V1.0
 */
class MisFileManagerModel extends CommonModel{
	protected  $trueTableName = 'mis_file_manager';
	//自动完成修改和插入
    public $_auto	=array(
			array('createtime','time',self::MODEL_INSERT,'function'),
    		array('updatetime','time',self::MODEL_UPDATE,'function'),
    		array('createid','getMemberId',self::MODEL_INSERT,'callback'),
    		array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
    	);
	}
	?>