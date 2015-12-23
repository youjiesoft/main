<?php
/**
 * @Title: MisCarTrafficAccidentModel 
 * @Package package_name
 * @Description: todo(用一句话描述该类的作用) 
 * @author renling 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-10-7 下午5:56:51 
 * @version V1.0
 */
class MisSystemDataviewMasModel extends CommonModel {
	protected $trueTableName = 'mis_system_dataview_mas';
	/*
	 * 自动填充
	 */
	public $_auto		=	array(
		array('createid','getMemberId',self::MODEL_INSERT,'callback'),
		array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
		array('createtime','time',self::MODEL_INSERT,'function'),
		array('updatetime','time',self::MODEL_UPDATE,'function'),
		array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
		array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
		array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
	);
	public $_validate	=	array(
			array('name,status','','视图名称重复，请检查！',self::MUST_VALIDATE,'unique',self::MODEL_BOTH),//多字段组合验证
	);
	
	//查询返回视图条件
	//paramate $name  string    视图名称
	//return viewWhere   string 视图条件
	public function viewWhere($name){
		$where="";//初始化条件
		//查询视图附加条件
		$map="";
		$map['name']=$name;
		$map['status']=1;
		$viewWhere=$this->where($map)->find();
		if($viewWhere){
			if(!empty($viewWhere['condition'])){
			    return $viewWhere['condition'];
			}
		}
	}
	public function getViewConf($name){
		$modelname = getFieldBy($name,'name','modelname','mis_system_dataview_mas');
		$file = Dynamicconf."/Models/".$modelname."/".$name.".inc.php";
		if(is_file($file)){
			return require $file;
		}else{
			return array();
		}
		
	}
}
?>