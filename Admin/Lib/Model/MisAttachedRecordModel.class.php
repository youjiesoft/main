<?php
//Version 1.0
/**
 * Description of MisAttachedRecordModel
 *
 * @author Administrator
 */
class MisAttachedRecordModel extends CommonModel{
    protected $trueTableName = 'mis_attached_record';

    public $_auto	=array(
    		array('createid','getMemberId',self::MODEL_INSERT,'callback'),
    		array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
    		array('createtime','time',self::MODEL_INSERT,'function'),
    		array('updatetime','time',self::MODEL_UPDATE,'function'),
    	    array('companyid','getCompanyID',self::MODEL_INSERT,'callback'),
    		array('departmentid','getDeptID',self::MODEL_INSERT,'callback'),
    		array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
    );
    /**
     * @Title: getAttachedFields 
     * @Description: todo(获取附件信息列表信息)   
     * @author yangxi 
     * @date 2015-5-16 下午2:47:55 
     * @throws
     */
    public  function  getAttachedFields($tablename,$tableid){
    	//获取附件信息
    	$attachList=array();//初始化附件列表空数组
    	$num=0;
    	$map = array();
    	$map["status"]  =1;
    	$map["tablename"]=$tablename;
    	$map["tableid"] =$tableid;
    	$attArry=$this->field("upname,attached")->where($map)->select();
    	if($attArry){
    		$returnData['attach']=array();
    		foreach($attArry as $attkey => $attval){
    			$attachList[$num]['attachname']=$attval['upname'];
    			//注意这里的路径，必须是__APP__."/MisSystemAnnouncement/misFileManageDownload/：对应模型名称，不能是__URL__."/misFileManageDownload/；将取不到数据
    			$attachList[$num]['attachurl']=C('SERVER_ADDRESS').__APP__."/MisSystemAnnouncement/misFileManageDownload/path/".base64_encode($attval['attached'])."/rename/".$attval['upname'];
    			$num++;
    		}
    	}else{
    		//你存在数据是赋予空值
    		$attachList=null;
    	} 
    	return  $attachList;
    }
}
?>