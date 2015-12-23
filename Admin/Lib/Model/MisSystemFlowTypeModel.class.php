<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: 项目流程=》业务类型模型
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-8-16 上午11:04:15 
 * @version V1.0
 */
class MisSystemFlowTypeModel extends CommonModel {
	protected $trueTableName = 'mis_system_flow_type';
	public $_auto =array(
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
			array('cmpid','implodFeld',self::MODEL_BOTH,'callback'),
	);
	
	public $_validate = array(
			array('name,parentid','','类型名称已存在',self::EXISTS_VAILIDATE,'unique',self::MODEL_BOTH),
	);
	
	public function  getDeptCombox(){
		//查询岗位
		$MisSystemFlowFormModel=D('MisSystemFlowForm');
		$parentidList=$MisSystemFlowFormModel->where('status=1')->field("id,name,category,outlinelevel")->select();
	//	print_r($parentidList);
		$supcategoryList = $this->where('status=1')->field("id,name,parentid,outlinelevel")->select();
		//echo "<br/>**************************************************<br/>";
		//print_r($supcategoryList);
		//exit;
		foreach ($supcategoryList as $key=>$val){
			if($val['outlinelevel']==1){
				$newDeptList['supcategory'][]=array(
						'id'=>$val['id'],
						'name'=>$val['name'],
						'parentid'=>$val['parentid'],
				);
			}elseif($val['outlinelevel']==2){
				$newDeptList['category'][]=array(
						'id'=>$val['id'],
						'name'=>$val['name'],
						'parentid'=>$val['parentid'],
				);
			}
		}
		foreach ($parentidList as $jkey=>$jval){
			if($jval['outlinelevel']==3){
				$newDeptList['parentid'][]=array(
						'id'=>$jval['id'],
						'name'=>$jval['name'],
						'category'=>$jval['category'],
				);
			}
		}
		//print_r($newDeptList);
	
		return json_encode($newDeptList);
	}
	
	
}
?>