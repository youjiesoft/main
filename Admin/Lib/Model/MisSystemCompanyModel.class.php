<?php
//Version 1.0
/**
 * Description of MisSystemCompanyModel
 *
 * @author yangxi
 */
class MisSystemCompanyModel extends CommonModel{
    protected $trueTableName = 'mis_system_company';

    public $_auto	=array(
    		array('createid','getMemberId',self::MODEL_INSERT,'callback'),
    		array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
    		array('createtime','time',self::MODEL_INSERT,'function'),
    		array('updatetime','time',self::MODEL_UPDATE,'function'),
    		array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
    		array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
    		array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),    		
    );
    
    public $_validate	=	array(
            array('code,status','','数据有重复，请检查！',self::MUST_VALIDATE,'unique',self::MODEL_BOTH),//多字段组合验证
    );
    public function getCompanyOne(){
    	$companyid=$this->where("status=1")->getField("id");
    	return $companyid;
    }
}
?>