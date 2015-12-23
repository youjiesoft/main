<?php
/**
 * @Title: MisOaArchivesManageModel 
 * @Package package_name
 * @Description: todo(档案管理的MODEL) 
 * @author xiafengqin 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-9-24 下午5:17:51 
 * @version V1.0
 */
class MisOaArchivesManageSubModel extends CommonModel {
	protected $trueTableName = 'mis_oa_archives_manage_sub';
	public $_auto	=array(
		array('createid','getMemberId',self::MODEL_INSERT,'callback'),
		array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
		array('createtime','time',self::MODEL_INSERT,'function'),
		array('updatetime','time',self::MODEL_UPDATE,'function'),
			
		array('datetime','dateToTimeString',self::MODEL_BOTH,'callback'),
	    array('companyid','getCompanyID',self::MODEL_INSERT,'callback'),
		array('departmentid','getDeptID',self::MODEL_INSERT,'callback'),
	);
}
?>
