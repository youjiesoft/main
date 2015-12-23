<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: 工作协同模型,模型作用，1、指定表，2、数据自动填充，3、数据验证、4、获取本表相关的数据信息  主要获取待协同的数据
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-9-2 上午11:28:59 
 * @version V1.0
 */
class MisOaItemsModel extends CommonModel {
	//指定当前模型对应的表
	protected $trueTableName = 'mis_oa_items';
	
	//数据自动填充
	public $_auto =	array(
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
			array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
	);
	
	/**
	 * @Title: getUserOaItemsList 
	 * @Description: 获取$userid协同的数据信息
	 * @param 后台用户ID $userid 如果不传入参数，则取当前登录人，如果传入参数，则取传入的用户
	 * @return 返回待$userid协同的数据数组   
	 * @author liminggang 
	 * @date 2014-9-2 上午11:58:12 
	 * @throws
	 */
	public function getUserOaItemsList($userid){
		if(!$userid){
			//当前登录人id
			$userid = $_SESSION[C('USER_AUTH_KEY')];
		}
		//工作协同待办  查询待办事项条数
		$MisOaFlowsInstanceModel = D("MisOaFlowsInstance");
		$oamapdoing['flowsuser']= $userid;
		$oamapdoing['status']=1;
		$oamapdoing['dostatus'] = array('lt',2);
		$oaitemdoing=$MisOaFlowsInstanceModel->where($oamapdoing)->order("createtime desc")->select();
		
		$moduleNameList = array();
		if($oaitemdoing){
			$moduleNameList["MisWorkExecutingbox"] = array(
					'createtime'=>$oaitemdoing[0]['createtime'],
					'title'=>"工作协同",
					'url'=>__APP__."/MisWorkExecuting/index/jump/5/md/MisOaItems/type/3/rel/MisWorkExecutingbox",
					'count'=>count($oaitemdoing),
			);
		}
		//工作审批 end
		$UserAudit['list'] = $moduleNameList;
		$UserAudit['count'] = count($oaitemdoing);
		return $UserAudit;
	}
}
?>