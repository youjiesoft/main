<?php
/**
 * @Title: MisSalesSiteModel 
 * @Package package_name 
 * @Description: todo(销售区域模型) 
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2012-5-8 下午3:38:38 
 * @version V1.0
 */
class MisSalesSiteModel extends CommonModel {
    protected $trueTableName = 'mis_sales_site';

    public $_auto	=array(
    		array('createid','getMemberId',self::MODEL_INSERT,'callback'),
    		array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
    		array('createtime','time',self::MODEL_INSERT,'function'),
    		array('updatetime','time',self::MODEL_UPDATE,'function'),
			array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
    );
    
    protected 	$_validate=array(
         array("code","require","code不能为空"),
         array("name","require","name不能为空"),
         array("status","integer","status不是整数"),
         array("expand0","require","expand0不能为空"),
         array("expand1","require","expand1不能为空"),
         array("expand2","require","expand2不能为空"),
         array("expand3","require","expand3不能为空"),
         array("expand4","require","expand4不能为空"),
    );
}
?>