<?php
/**
 * 
 * @Title: MisWorkFacilityReturnMasAction 
 * @Package package_name
 * @Description: todo(设备归还) 
 * @author renling 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-7-28 下午4:23:10 
 * @version V1.0
 */
class MisWorkFacilityReturnMasAction extends CommonAuditAction {
	/**
	 * @Title: _before_add
	 * @Description: todo(add页面的前置函数)
	 * @author xiafengqin
	 * @date 2013-9-22 下午5:54:24
	 * @throws
	 */
	public function _before_add(){
		//部门树
		$model=M("mis_system_department");
		$list =$model->where("status=1")->select();
		$deptlist=$this->selectTree($list,0,0,getFieldBy($_SESSION[C('USER_AUTH_KEY')], 'id', 'dept_id', 'user'));
		$this->assign("deptidlist",$deptlist);
		//自动生成单号
		$scnmodel = D('SystemConfigNumber');
		$orderno = $scnmodel->GetRulesNO('mis_work_facility_return_mas');
		$this->assign("orderno", $orderno);
		//订单号是否可写
		$writable= $scnmodel->GetWritable('mis_work_facility_return_mas');
		$this->assign("writable",$writable);
		//单位
		$MisProductUnitModel = D('MisProductUnit');
		$unitList = $MisProductUnitModel->where('status=1')->select();
		$this->assign('unitList',$unitList);
		//获取当天时间
		$curday = date("Y-m-d", time());
		$this->assign("curday", $curday);
		//创建时间
		$this->assign('createtime', time());
	}
	/**
	 * @Title: _before_insert
	 * @Description: todo(新增的前置函数)
	 * @author xiafengqin
	 * @date 2013-9-22 下午4:38:23
	 * @throws
	 */
	public function _before_insert() {
		$this->checkifexistcodeororder('mis_work_facility_return_mas','orderno');
	}
	/**
	 * @Title: _after_insert
	 * @Description: todo(新增插入附件)
	 * @param unknown_type $id
	 * @author xiafengqin
	 * @date 2013-9-22 下午4:39:40
	 * @throws
	 */
	public function _after_insert($id){
		//插入附件
		if ($id) {
			//添加sub表
			$MisWorkFacilityReturnSubModel=D('MisWorkFacilityReturnSub');
			$MisWorkFacilityDistributeModel=D('MisWorkFacilityDistribute');
			//保存附件
			$this->swf_upload($_REQUEST['id'],100);
			$subid=$_POST['disid'];
			foreach ($subid as $key=>$val){
				$date=array();
				$date['masid']=$id;
				$date['objid']=$val;
				$date['objmodel']="MisWorkFacilityDistribute";
				$date['manageid']=getFieldBy($val, "id", "manageid", "mis_work_facility_distribute");
				$date['managename']=getFieldBy($val, "id", "managename", "mis_work_facility_distribute");
				$date['equipmenttype']=getFieldBy($val, "id", "equipmenttype", "mis_work_facility_distribute");
				$date['returnqty']=str_replace(",","",$_POST['rqty'][$key]);
				$date['createid']=$_SESSION[C('USER_AUTH_KEY')];
				$date['createtime']=time();
				$result=$MisWorkFacilityReturnSubModel->add($date);
				$MisWorkFacilityReturnSubModel->commit();
				if($result){
					//修改分布可归还数量
					$managedate['id']=$val;
					$managedate['returnqty']=array("exp","returnqty-".str_replace(",","",$_POST['rqty'][$key]));
					$MisWorkFacilityDistributeModel->save($managedate);
					$MisWorkFacilityDistributeModel->commit();
				}
			}
		}
	}
	/**
	 * @Title: _before_edit
	 * @Description: todo(edit页面都前置函数)
	 * @author xiafengqin
	 * @date 2013-9-22 下午4:40:23
	 * @throws
	 */
	public function _before_edit(){
		//部门
		$id = $_REQUEST['id'];
		//部门
		$model=M("mis_system_department"); 
		$list =$model->where("status=1")->select();
		$deptlist=$this->selectTree($list,0,0,getFieldBy($id, 'id', 'returndeptid', 'mis_work_facility_return_mas'));
		$this->assign("deptidlist",$deptlist);
		//当前申请单的明细
		$model = D("MisWorkFacilityReturnSub");
		$map['masid'] = $id;
		$map['status'] = 1;
		$sublist = $model->where($map)->select();
		foreach ($sublist as $key=>$val){  //计算可用数量
			$sublist[$key]['sumreturnqty']=getFieldBy($val['objid'], 'id', 'returnqty', 'mis_work_facility_distribute')+$val['returnqty'];
			$sublist[$key]['operationname']=getFieldBy($val['objid'], 'id', 'operation', 'mis_work_facility_distribute');
		}
		$this->assign("sublist",$sublist);
		//单位
		$MisProductUnitModel = D('MisProductUnit');
		$unitList = $MisProductUnitModel->where('status=1')->select();
		$this->assign('unitList',$unitList);
		//获取附件信息
		$this->getAttachedRecordList(84,$_REQUEST['id']);
		//编号可写
		$model1	=D('SystemConfigNumber');
		$writable= $model1->GetWritable('mis_work_facility_abnormalmove');
		$this->assign("writable",$writable);
	}
	/**
	 * @Title: _before_update
	 * @Description: todo(修改的前置函数)
	 * @author xiafengqin
	 * @date 2013-9-22 下午4:41:01
	 * @throws
	 */
	public function _before_update() {
		$this->checkifexistcodeororder('mis_work_facility_return_mas','orderno',1);
	}
	/**
	 * @Title: _after_update
	 * @Description: todo(修改的后置函数)
	 * @param unknown_type $list
	 * @author xiafengqin
	 * @date 2013-9-22 下午4:41:45
	 * @throws
	 */
	public function _after_update($id) {
		if($id && !$_POST['beforeInsert']){
			$id=$_POST['id'];
			//保存附件
			$this->swf_upload($_REQUEST['id'],100);
			//修改设备可异动数量
			$appsubid=$_POST['disid'];
			$MisWorkFacilityReturnSubModel=D('MisWorkFacilityReturnSub');
			$MisWorkFacilityDistributeModel=D('MisWorkFacilityDistribute');
			foreach ($appsubid as $key=>$val){
				//修改sub表数据
				$date=array();
				$date['masid']=$id;
				$date['objid']=$val;
			 	$date['objmodel']="MisWorkFacilityDistribute";
				$date['manageid']=getFieldBy($val, "id", "manageid", "mis_work_facility_distribute");
				$date['managename']=getFieldBy($val, "id", "managename", "mis_work_facility_distribute");
				$date['equipmenttype']=getFieldBy($val, "id", "equipmenttype", "mis_work_facility_distribute");
				$date['returnqty']=str_replace(",","",$_POST['rqty'][$key]);
				if($_POST['subid'][$key]){
					$date['updateid']=$_SESSION[C('USER_AUTH_KEY')];
					$date['updatetime']=time();
					$date['id']=$_POST['subid'][$key];
					$result=$MisWorkFacilityReturnSubModel->save($date);
				}else{
					$date['createid']=$_SESSION[C('USER_AUTH_KEY')];
					$date['createtime']=time();
					$result=$MisWorkFacilityReturnSubModel->add($date);
				}
				$MisWorkFacilityReturnSubModel->commit();
				if($result){
					//修可用数量
					if($_POST['oldrty'][$key]){
						$managedate['returnqty']=array("exp","returnqty+".$_POST['oldrty'][$key]."-".str_replace(",","",$_POST['rqty'][$key]));
					}else{
						$managedate['returnqty']=array("exp","returnqty-".str_replace(",","",$_POST['rqty'][$key]));
					}
					$managedate['id']=$val;
					$MisWorkFacilityDistributeModel->save($managedate);
					$MisWorkFacilityDistributeModel->commit();
				}
			}
		}
	}
	/**
	 * @Title: _before_auditEdit
	 * @Description: todo(打开审核预览前置函数)
	 * @author xiafengqin
	 * @date 2013-9-22 下午6:32:55
	 * @throws
	 */
	public function _before_auditEdit(){
		//获取附件信息
		$this->getAttachedRecordList(100,$_REQUEST['id']);
			//当前申请单的明细
		$model = D("MisWorkFacilityReturnSub");
		$map['masid'] = $_REQUEST['id'];
		$map['status'] = 1;
		$sublist = $model->where($map)->select();
		foreach ($sublist as $key=>$val){  //计算可用数量
			$sublist[$key]['sumreturnqty']=getFieldBy($val['objid'], 'id', 'returnqty', 'mis_work_facility_distribute')+$val['returnqty'];
			$sublist[$key]['operationname']=getFieldBy($val['objid'], 'id', 'operation', 'mis_work_facility_distribute');
		}
		$this->assign("sublist",$sublist);
	}
	/**
	 * @Title: _before_auditView
	 * @Description: todo(审核流程查看前置函数)
	 * @author xiafengqin
	 * @date 2013-9-22 下午6:33:32
	 * @throws
	 */
	public function _before_auditView(){
		//获取附件信息
		$this->getAttachedRecordList(100,$_REQUEST['id']);
		//当前申请单的明细
		$model = D("MisWorkFacilityReturnSub");
		$map['masid'] = $_REQUEST['id'];
		$map['status'] = 1;
		$sublist = $model->where($map)->select();
		foreach ($sublist as $key=>$val){  //计算可用数量
			$sublist[$key]['sumreturnqty']=getFieldBy($val['objid'], 'id', 'returnqty', 'mis_work_facility_distribute')+$val['returnqty'];
			$sublist[$key]['operationname']=getFieldBy($val['objid'], 'id', 'operation', 'mis_work_facility_distribute');
		}
		$this->assign("sublist",$sublist);
	}
	/**
	 * @Title: overProcess
	 * @Description: todo(审核通过执行的操作  修改库存分布 添加库存动态 修改设备管理可申请数量)
	 * @param unknown_type $id
	 * @author xiafengqin
	 * @date 2013-9-22 下午6:53:58
	 * @throws
	 */
	function overProcess( $id ){
		//归还submodel
		$submodel=D("MisWorkFacilityReturnSub");
		//库存model
		$MisWorkFacilityInventorymodel=D("MisWorkFacilityInventory");
		//分布model
		$MisWorkFacilityDistributemodel=D("MisWorkFacilityDistribute");
		//修改设备管理可申请数量
		$MisWorkFacilityManageModel=D('MisWorkFacilityManage');
		$subMap=array();
		$subMap['masid']=$id;
		$subMap['status']=1;
		$subList=$submodel->where($subMap)->select();
		foreach ($subList as $key=>$val){
			//修改库存分布数量
			$disMap['id']=getFieldBy($val['id'], "id", "objid", "mis_work_facility_return_sub");
			$disMap['status']=1;
			$dissavedate=array();
			$dissavedate['appqty']=array("exp","appqty-".$val['returnqty']);
			$saveresult=$MisWorkFacilityDistributemodel->where($disMap)->save($dissavedate);
			$MisWorkFacilityDistributemodel->commit();
			if($saveresult){
				//添加库存动态
				$disDate=array();
				$disDate['objid']=$id;
				$disDate['objmodel']="MisWorkFacilityReturnMas";
				$disDate['operation']=5;//归还
				$disDate['remark']=getFieldBy($id, "id", "remark", "mis_work_facility_return_mas");
				$disDate['manageid']=$val['manageid'];
				$disDate['managename']= $val['managename'];
				$disDate['equipmenttype']=$val['equipmenttype'];
				$disDate['qty']=$val['returnqty'];
				$disDate['deptid']=getFieldBy($id, "id", "returndeptid", "mis_work_facility_return_mas");
				$disDate['createid']=getFieldBy($id, "id", "createid", "mis_work_facility_abnormalmove");
				$disDate['createtime']=time();
				$result=$MisWorkFacilityInventorymodel->data($disDate)->add();
				$MisWorkFacilityInventorymodel->commit();
				if(!$result){
					$this->error("操作失败！");
				}else{
					$managedate=array();
					$managedate['id']=$val['manageid'];
					$managedate['kyqty']=array("exp","kyqty+".$val['returnqty']);
					$mresult=$MisWorkFacilityManageModel->save($managedate);
					$MisWorkFacilityManageModel->commit();
					if(!$mresult){
						$this->error("操作失败！");
					}
				}
				
			}
		}
	}
	/**
	 * 
	 * @Title: lookupworkfacilityreturn
	 * @Description: todo(查找该部门设备)   
	 * @author renling 
	 * @date 2014-7-29 上午10:28:36 
	 * @throws
	 */
	public function lookupworkfacilityreturn(){
		//实例化办公设备类型表
		$dptmodel = D("MisWorkFacilityType");
		$MisWorkFacilityManagemodel = D("MisWorkFacilityManage");
		//查name和id
		$deptlist = $dptmodel->where("status=1")->order("id asc")->field('id,name,pid as parentid')->select();
		$treemiso[]=array(
				'id'=>0,
				'pId'=>-1,
				'name'=>'设备类型',
				'rel'=>'lookupworkfacilityreturnlistli',
				'target'=>'ajax',
				'url'=>'__URL__/lookupworkfacilityreturn/jump/1',
				'open'=>true
		);
		$param['rel']="lookupworkfacilityreturnlistli";
		$param['url']="__URL__/lookupworkfacilityreturn/jump/1/equipmenttype/#id#";
		$typeTree = $this->getTree($deptlist,$param,$treemiso);
		$this->assign('tree',$typeTree);
		$map=$this->_search("MisWorkFacilityApplySub");
		$map['returnqty']=array("neq",0);
		$deptid=$_REQUEST['equipmenttype'];
		if($deptid){
			//加入递归
			$deptlist =array_unique(array_filter (explode(",",$this->findAlldept($deptlist,$deptid))));
			$map['equipmenttype']=array("in",$deptlist);
			//查询此类型的设备di
			$this->assign("equipmenttype",$deptid);
		}
		$map['applydepartmentid']=getFieldBy($_SESSION[C('USER_AUTH_KEY')], "id", "dept_id", "user");
		$Common=A('Common');//
		$Common->_list('MisWorkFacilityDistribute', $map);
		if($_REQUEST['jump']){
			$this->display('lookupworkfacilityreturnlist');
		}else{
			$this->display();
		}
	}
	public function lookupsubdelete(){
		$id = $_POST['id'];// 明细ID
		$model = D("MisWorkFacilityReturnsub");
		$map['id'] = $id;
		$res = $model->where($map)->setField ( 'status', - 1 );
		$model->commit();
		//还原可异动数量
		if($res){
			$MisWorkFacilityDistributeModel=D('MisWorkFacilityDistribute');
			$ManageDate=array();
			$ManageDate['id']=getFieldBy($id, 'id', 'objid', 'mis_work_facility_return_sub');
			$ManageDate['returnqty']=array("exp","returnqty+".getFieldBy($id, 'id', 'returnqty', 'mis_work_facility_return_sub'));
			$MisWorkFacilityDistributeModel->save($ManageDate);
		}
		echo $res;
	}
	public function delete(){
		$id=$_REQUEST['id'];
		$oldstatus=getFieldBy($id, "id", "status", "MisWorkFacilityAbnormalmove");
		$model = D("MisWorkFacilityAbnormalmove");
		$submodel = D("MisWorkFacilityAbnormalmovesub");
		$map['id'] = $id;
		$res = $model->where($map)->setField ( 'status', - 1 );
		if($res && $oldstatus!=-1){
			$MisWorkFacilityApplySubModel=D('MisWorkFacilityApplySub');
			//查找该mas下的sub
			$subMap['masid']=$id;
			$subMap['status']=1;
			$subList=$submodel->where($subMap)->select();
			foreach ($subList as $key=>$val){
				$submodel->where("id=".$val['id']." and status=1")->setField ( 'status', - 1 );
				$ManageDate=array();
				$ManageDate['id']=$val['appsubid'];
				$ManageDate['kymove']=array("exp","kymove+".$val['qty']);
				$MisWorkFacilityApplySubModel->save($ManageDate);
			}
		}
		if ($res!==false) {
			$this->success ( L('_SUCCESS_') );
		} else {
			$this->error ( L('_ERROR_') );
		}

	}
}