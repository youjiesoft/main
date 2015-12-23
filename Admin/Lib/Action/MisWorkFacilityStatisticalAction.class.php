<?php
/**
 *
 * @Title: MisWorkFacilityStatisticalAction
 * @Package package_name
 * @Description: todo(设备统计)
 * @author renling
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-7-31 下午2:14:16
 * @version V1.0
 */
class MisWorkFacilityStatisticalAction extends CommonAction {
	/**
	 * (non-PHPdoc)
	 * @see CommonAction::index()
	 */
	function index(){
		$this->createwfDistributeTree();
		$this->display();
	}

	/**
	 * @var 设备报表树
	 */
	private $report = array("wfDistributeChart"=>"设备分布");
	/**
	 *
	 * @Title: createwfDistributeTree
	 * @Description: todo(构造设备树)
	 * @author renling
	 * @date 2014-7-31 下午2:32:46
	 * @throws
	*/
	private function createwfDistributeTree(){
		$zNodes = array();
		$zNodes[] = array('id' => 0,
				'name' => '设备报表',
				'title' => '设备报表',
				'open' => true);
		$id = 0;
		foreach ($this->report as $k => $v) {
			$id = $id+1;
			$zNodes[] = array('id' => $id,
					'pId' => 0,
					'name' => $v,
					'title' => $v,
					'rel' => "MisWorkFacilityStatisticalindexview",
					'target' => 'ajax',
					'url' => "__URL__/".$k,
					'open' => true);
		}
		$this->assign("zNodes",json_encode($zNodes));
	}
	/**
	 *
	 * @Title: wfDistributeChart
	 * @Description: todo(设备分布)
	 * @author renling
	 * @date 2014-7-31 下午2:33:20
	 * @throws 
	 */
	public function wfDistributeChart(){
		$this->createwfDistributeTree();
		$typeModel = D('MisWorkFacilityType');
		$typelist = $typeModel->where('status=1 and pid !=0')->getField("id,name");
		$this->assign("MisWorkFacilityTypeList",$typelist);
		//查询第一个类型
		$MisWorkFacilityDistributeModel=D('MisWorkFacilityDistribute');
		$MisWorkFacilityDistributeVO=$MisWorkFacilityDistributeModel->where("status=1")->find();
		$map=array();
		$map['status']=1;
		if($_REQUEST['equipmenttype']){
			$map['equipmenttype']=$_REQUEST['equipmenttype'];
		}else{
			$map['equipmenttype']=$MisWorkFacilityDistributeVO['equipmenttype'];
		}
		if($_REQUEST['manageid']){
			$map['manageid']=$_REQUEST['manageid'];
		}
		$this->assign("equipmenttype",$map['equipmenttype']);
		$this->assign("title",getFieldBy($map['equipmenttype'], "id", "name", "mis_work_facility_type")."分布图");
		$map['appqty']=array("neq",0);
		$MisWorkFacilityDistributeList=$MisWorkFacilityDistributeModel->where($map)->select();
		$xAxis = array("categories"=>array());
		$deptlist=array();
		$manageidList=array();
		$series = array();
		foreach ($MisWorkFacilityDistributeList as $key=>$val){
			if(!in_array($val['applydepartmentid'],array_keys($deptlist))){
				$deptlist[$val['applydepartmentid']]=1;
				$xAxis["categories"][]= getFieldBy($val['applydepartmentid'], 'id', "name", "mis_system_department");
			}
			if(!in_array($val['manageid'],array_keys($manageidList))){
				$manageidList[$val['manageid']]=1;
				$newseries=array(
						"name"=>$val['managename'],
						"data"=>array(),
				);
				$series[]=$newseries;
			}
		}
		$i=0;
		foreach ($manageidList as $mkey=>$mval){
			$alist=array();
			foreach ($deptlist as $key=>$val){
				$manageMap=array();
				$manageMap['manageid']=$mkey;
				$manageMap['applydepartmentid']=$key;
				$manageMap['equipmenttype']=$map['equipmenttype'];
				//查询该部门下设备数量
				$MisWorkFacilityDistributeVo= $MisWorkFacilityDistributeModel->where($manageMap)->find();
				$alist[]=intval($MisWorkFacilityDistributeVo['appqty']);
			}
			$series[$i]["data"]=$alist;
			$i++;
		}
		$this->assign("xAxis",json_encode($xAxis));
		$this->assign("series",json_encode($series));
		$this->display("wfDistributeChart");
	}
}