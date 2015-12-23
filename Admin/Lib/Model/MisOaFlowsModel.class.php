<?php
/** 
 * @Title: MisOaFlowsModel 
 * @Package package_name
 * @Description: todo(协同流程设置) 
 * @author 杨东 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-4-3 下午2:37:58 
 * @version V1.0 
*/ 
class MisOaFlowsModel extends CommonModel {
	protected $trueTableName = 'mis_oa_flows';
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
	
	
	
	
	public function getOaFlowsInfo($flowid){
		//查询自定义流程数据，和审核人走向
		$flowlist=$this->where("id = ".$flowid)->find();
		$flowtrack = unserialize($flowlist['flowtrack']);
		//存储审核人ID
		$a = array();
		foreach($flowtrack as $key=>$val){
			if($val['level']>1){
				if(in_array($val['level'],array_keys($a))){
					$a[$val['level']] = $a[$val['level']].",".$val['id'];
				}else{
					$a[$val['level']] = $val['id'];
				}
			}
		}
		$arrList = array();
		if($a){
			//实例化后台用户模型
			$UserModel = D("User");
			foreach($a as $k1=>$v1){
				if($v1){
					$map = array();
					$map['id'] = array(' in ',$v1);
					$map['status'] = 1;
					$lst=$UserModel->where($map)->select();
					foreach($lst as $k2=>$v2){
						$arr = array(
								'id'=>$v2['id'],
								'account'=>$v2['account'],
								'name'=>$v2['name'],
								'allnode'=>$k1,
								'ptmptid'=>0,
								'flowid'=>$flowid
						);
						$arrList[$k1][] = $arr;
					}
				}else{
					$arr = array(
							'id'=>'',
							'name'=>'请选择',
							'allnode'=>$k1,
							'ptmptid'=>0,
							'flowid'=>$flowid
					);
					$arrList[$k1][] = $arr;
				}
			}
		}
		return $arrList;
	}
}
?>