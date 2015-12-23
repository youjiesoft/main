<?php
/**
 * @Title: MisSystemFlowWorkModel 
 * @Package package_name
 * @Description: 项目流程=》节点任务模型
 * @author 黎明刚 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014年10月20日 上午10:56:50 
 * @version V1.0
 */
class MisSystemFlowWorkModel extends CommonModel {
	protected $trueTableName = 'mis_system_flow_form';
	
	public $_auto =array(
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
			
			array('begintime','dateToTimeString',self::MODEL_BOTH,'callback'),
			array('endtime','dateToTimeString',self::MODEL_BOTH,'callback'),
			array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
			array('readtaskrole','implodFeld',self::MODEL_BOTH,'callback'),
			
	);
	
	/**
	 * @Title: pJAccesslist
	 * @Description: todo(任务节点权限生成)
	 * @param $id int 任务节点id（以前用于生成数组key值，后改用控制器名称）
	 * @param $actionname string 控制器名称
	 * @author 谢友志
	 * @date 2015-4-7 上午9:59:20
	 * @throws
	 */
	public function pJAccesslist2($actionname,$id=''){
		//查找任务节点对应模板名称
		$Action = $actionname;
		//查找该模板名称对应记录
		$nodedetails = M("node")->where("name='".$Action."'")->find();
		if(! $nodedetails){
			logs($Action."---------没有对应模板------------","taskNodeAccess");
		}
		//$nodeid = getFieldBy($action,"name","id","node");
		//查找该模板的操作节点
		$nodemodel = M("node");
		$list = $nodemodel->where("pid=".$nodedetails['id'])->select();
		/**
		 索引文件以“paaccess_”+任务节点id为key值 已对应模板名称为value值组成数组
		*/
	
		if($list){
			//组合一个索引文件的元素 这里直接做成一个数组，后面与索引文件取得的数组进行合并
			$name = "pjaccess_".$Action;
			//对操作节点数据进行重组$detailes
			$temp = array('GROUPID'=>$nodedetails['group_id']);
			foreach($list as $k=>$v){
				$optionname = strtoupper($v['name']);
// 				if($optionname=="INDEX"){
					$temp1 = array($optionname=>$v['id']."-1");
// 				}else{
// 					$temp1 = array($optionname=>$v['id']."-4");
// 				}
				$temp = array_merge($temp,$temp1);
			}
			$detailes = array($Action=>$temp);
			//套表 组合表情况下 添加权限详情元素
			$isbinddetailes = $this->pjAccessisbind($Action);
			$detailes = array_merge($detailes,$isbinddetailes);
			//暂时生成文件 其操作节点权限全部为1
			//任务节点权限文件夹
			$pathconf = DConfig_PATH . '/PJAccessList';
			if (! file_exists ( $pathconf )) {
				createFolder ( $pathconf );
			}
			//调用lookupobj模型的写入文件方法
			$lookupmodel = D("LookupObj");
			// 每个任务节点生成单独的文件
			$detailesfile =  $pathconf.'/'.$name.'.php';
			$detailesnum = $lookupmodel->SetSongleRule($detailesfile,$detailes);
		}else{
			logs($Action."=======没有对应下级操作========","taskNodeAccess");
		}
	}
	/**
	 * @Title: pjAccessisbind
	 * @Description: todo(套表 组合表除开主表的表单权限)
	 * @param string $actionname 控制器名称
	 * @return multitype:multitype:unknown
	 * @author 谢友志
	 * @date 2015-4-8 上午10:07:04
	 * @throws
	 */
	public function pjAccessisbind($actionname){
		$islist = array();
		//查询关系表单是否有这个控制器绑定的其他表单
		$model = M("mis_auto_bind");
		$isbindlist = $model->where("bindaname='{$actionname}'")->select();
		//如果有，将绑定表单控制器的下级操作查出
		if($isbindlist){
			$nodemodel = M("node");
			foreach($isbindlist as $key=>$val){
				if($val['inbindaname']){
					$isbingaction = $val['inbindaname'];
					$nodedetails = $nodemodel->where("name='{$isbingaction}'")->find();
					$list = $nodemodel->where("pid=".$nodedetails['id'])->select();
					//对操作节点数据进行重组
					$temp = array('GROUPID'=>$nodedetails['group_id']);
					foreach($list as $k=>$v){
						//查询权限为1 其他为4
						$optionname = strtoupper($v['name']);
// 						if($optionname=="INDEX"){
							$temp1 = array($optionname=>$v['id']."-1");
// 						}else{
// 							$temp1 = array($optionname=>$v['id']."-4");
// 						}
						$temp = array_merge($temp,$temp1);
					}
					//以被绑定的action为key组装数组
					$islist[$isbingaction]=$temp;
				}
			}
		}
		//print_r($islist);
		return $islist;
	}
	/**
	 * @Title: getTaskNodeAccess
	 * @Description: todo(调用任务节点对应的模板权限，包含套表组合表) 
	 * @param int $actionname  控制器名称
	 * @author 谢友志 
	 * @date 2015-4-7 下午6:30:46 
	 * @throws
	 */
	public function getTaskNodeAccess($actionname){		
		$file = DConfig_PATH . '/PJAccessList/pjaccess_'.$actionname.'.php';
		$list = array();
		if(file_exists($file)){
			$list = require $file;
		}
		return $list;
	}
	/**
	 * @Title: savePjAccessList
	 * @Description: todo(合并权限写入$_SESSION)
	 * @param unknown_type $formobj 控制器名称
	 * @author 谢友志
	 * @date 2015-4-8 上午10:29:31
	 * @throws
	 */
	public function savePjAccessList($formobj){
		$pjaccesslist = $this->getTaskNodeAccess($formobj);
		foreach($pjaccesslist as $k=>$v){
			foreach($v as $k1=>$v1){
				$valarr = explode("-",$v1);
				//if($valarr[1]&&$k1!='ADD'){
					//$a[strtolower($k."_".$k1)] = $valarr[1];
					$_SESSION[strtolower($k."_".$k1)] = $valarr[1];
				//}
			}
		}
	}
}
?>