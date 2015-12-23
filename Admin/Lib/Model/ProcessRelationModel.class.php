<?php
//Version 1.0
// 系统流程节点关系表
class ProcessRelationModel extends CommonModel {
	protected $trueTableName = 'process_relation';
	public $_auto	=array(
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
		   array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),

	);
	public function checkprocess($pid=0,$tid){
		$where=" 1=1 ";
		if($pid){
			$where.=" and pid='".$pid."' ";
		}
		if($tid){
			$where.=" and tid='".$tid."' ";
		}
		$list=$this->where($where)->field("id")->find();
		if($list!==false){
			return $list['id'];
		}else{
			return 0;
		}
	}
	/**
	 *
	 *
	 *
	 * @Title: getProessInfoRelation
	 * @Description: todo(根据流程节点 获取流程走向节点)
	 * @author renling
	 * @date 2014-9-13 下午2:43:48
	 * @throws
	 */
	public function getProessInfoRelation($pInfoid){
		$map['status'] = 1;
		$map['tablename'] = array('eq','process_info');
		$map['pinfoid'] = $pInfoid;
		$ProessInfoList=$this->where($map)->select();
		return  $ProessInfoList;
	}

}
?>