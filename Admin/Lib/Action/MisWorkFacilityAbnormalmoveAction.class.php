<?php
/**
 * @Title: MisWorkFacilityAbnormalmoveAction
 * @Package package_name
 * @Description: todo(办公设备异动的ACTION)
 * @author xiafengqin
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-9-22 下午5:45:42
 * @version V1.0
 */
class MisWorkFacilityAbnormalmoveAction extends CommonAuditAction {
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
		$deptlist=$this->selectTree($list,0,0,$deptid);
		$this->assign("deptidlist",$deptlist);
		//自动生成单号
		$scnmodel = D('SystemConfigNumber');
		$orderno = $scnmodel->GetRulesNO('mis_work_facility_abnormalmove');
		$this->assign("orderno", $orderno);
		//订单号是否可写
		$writable= $scnmodel->GetWritable('mis_work_facility_abnormalmove');
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
		$this->checkifexistcodeororder('mis_work_facility_abnormalmove','orderno');
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
			$MisWorkFacilityAbnormalmovesubModel=D('MisWorkFacilityAbnormalmovesub');
			$this->swf_upload($id,84);
			//保存附件
			$this->swf_upload($_REQUEST['id'],84);
			//修改设备可异动数量
			$subid=$_POST['appsubid'];
			$MisWorkFacilityApplySubModel=D('MisWorkFacilityApplySub');
			foreach ($subid as $key=>$val){
				$date=array();
				$date['masid']=$id;
				$date['appsubid']=$val;
				$date['manageid']=$_POST['manageid'][$key];
				$date['managename']=getFieldBy($_POST['manageid'][$key], "id", "equipmentname", "mis_work_facility_manage");
				$date['equipmenttype']=getFieldBy($_POST['manageid'][$key], "id", "equipmenttype", "mis_work_facility_manage");
				$date['olddeptid']=getFieldBy(getFieldBy($val, "id", "masid", "mis_work_facility_apply_sub"), "id", "applydepartmentid", "mis_work_facility_apply_mas");
				$date['oldqty']=getFieldBy($val, "id", "appqty", "mis_work_facility_apply_sub");
				$date['qty']=$_POST['appqty'][$key];
				$date['createid']=$_SESSION[C('USER_AUTH_KEY')];
				$date['createtime']=time();
				$result=$MisWorkFacilityAbnormalmovesubModel->add($date);
				$MisWorkFacilityAbnormalmovesubModel->commit();
				if($result){
					$appmap=array();
					$appmap['id']=$val;
					$appmap['manageid']=$_POST['manageid'][$key];
					//修可用数量
					$managedate['kymove']=array("exp","kymove-".str_replace(",","",$_POST['appqty'][$key]));
					$MisWorkFacilityApplySubModel->where($appmap)->save($managedate);
					$MisWorkFacilityApplySubModel->commit();
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
		$deptlist=$this->selectTree($list,0,0,getFieldBy($id, 'id', 'movedeptid', 'mis_work_facility_abnormalmove'));
		$this->assign("deptidlist",$deptlist);
		//当前申请单的明细
		$model = D("MisWorkFacilityAbnormalmovesub");
		$map['masid'] = $id;
		$map['status'] = 1;
		$sublist = $model->where($map)->select();
		foreach ($sublist as $key=>$val){  //计算可用数量
			$sublist[$key]['sumkymove']=getFieldBy($val['appsubid'], 'id', 'kymove', 'mis_work_facility_apply_sub')+$val['qty'];
		}
		$this->assign("sublist",$sublist);
		//单位
		$MisProductUnitModel = D('MisProductUnit');
		$unitList = $MisProductUnitModel->where('status=1')->select();
		$this->assign('unitList',$unitList);
		//获取附件信息
		$this->getAttachedRecordList($_REQUEST['id']);
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
		$this->checkifexistcodeororder('mis_work_facility_abnormalmove','orderno',1);
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
			$this->swf_upload($_REQUEST['id'],84);
			//修改设备可异动数量
			$appsubid=$_POST['appsubid'];
			$MisWorkFacilityAbnormalmovesubModel=D('MisWorkFacilityAbnormalmovesub');
			$MisWorkFacilityApplySubModel=D('MisWorkFacilityApplySub');
			foreach ($appsubid as $key=>$val){
				//修改sub表数据
				$date=array();
				$date['masid']=$id;
				$date['appsubid']=$val;
				$date['manageid']=$_POST['manageid'][$key];
				$date['managename']=getFieldBy($_POST['manageid'][$key], "id", "equipmentname", "mis_work_facility_manage");
				$date['equipmenttype']=getFieldBy($_POST['manageid'][$key], "id", "equipmenttype", "mis_work_facility_manage");
				$date['olddeptid']=getFieldBy(getFieldBy($val, "id", "masid", "mis_work_facility_apply_sub"), "id", "applydepartmentid", "mis_work_facility_apply_mas");
				$date['oldqty']=getFieldBy($val, "id", "appqty", "mis_work_facility_apply_sub");
				$date['qty']=$_POST['appqty'][$key];
				if($_POST['subid'][$key]){
					$date['updateid']=$_SESSION[C('USER_AUTH_KEY')];
					$date['updatetime']=time();
					$date['id']=$_POST['subid'][$key];
					$result=$MisWorkFacilityAbnormalmovesubModel->save($date);
				}else{
					$date['createid']=$_SESSION[C('USER_AUTH_KEY')];
					$date['createtime']=time();
					$result=$MisWorkFacilityAbnormalmovesubModel->add($date);
				}
				$MisWorkFacilityAbnormalmovesubModel->commit();
				if($result){
					$appmap=array();
					$appmap['id']=$val;
					$appmap['manageid']=$_POST['manageid'][$key];
					//修可用数量
					if($_POST['oldqty'][$key]){
						$managedate['kymove']=array("exp","kymove+".$_POST['oldqty'][$key]."-".str_replace(",","",$_POST['appqty'][$key]));
					}else{
						$managedate['kymove']=array("exp","kymove-".str_replace(",","",$_POST['appqty'][$key]));
					}
					$MisWorkFacilityApplySubModel->where($appmap)->save($managedate);
					$MisWorkFacilityApplySubModel->commit();
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
		$this->getAttachedRecordList($_REQUEST['id']);
		//当前申请单的明细
		$model = D("MisWorkFacilityAbnormalmovesub");
		$map['masid'] = $_REQUEST['id'];
		$map['status'] = 1;
		$sublist = $model->where($map)->select();
		foreach ($sublist as $key=>$val){  //计算可用数量
			$sublist[$key]['sumkymove']=getFieldBy($val['appsubid'], 'id', 'kymove', 'mis_work_facility_apply_sub')+$val['qty'];
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
		$this->getAttachedRecordList($_REQUEST['id']);
		//当前申请单的明细
		$model = D("MisWorkFacilityAbnormalmovesub");
		$map['masid'] = $_REQUEST['id'];
		$map['status'] = 1;
		$sublist = $model->where($map)->select();
		foreach ($sublist as $key=>$val){  //计算可用数量
			$sublist[$key]['sumkymove']=getFieldBy($val['appsubid'], 'id', 'kymove', 'mis_work_facility_apply_sub')+$val['qty'];
		}
		$this->assign("sublist",$sublist);
	}
	/**
	 * @Title: overProcess
	 * @Description: todo(审核通过执行的操作)
	 * @param unknown_type $id
	 * @author xiafengqin
	 * @date 2013-9-22 下午6:53:58
	 * @throws
	 */
	function overProcess( $id ){
		//修改分布图
		$submodel=D("MisWorkFacilityAbnormalmovesub");
		$subMap['masid']=$id;
		$subMap['status']=1;
		$subList=$submodel->where($subMap)->select();
		$MisWorkFacilityDistributeModel=D('MisWorkFacilityDistribute');
		foreach ($subList as $key=>$val){
			//修改库存分布数量
			$disMap['objid']=getFieldBy($val['appsubid'], "id", "masid", "mis_work_facility_apply_sub");
			$disMap['objmodel']="MisWorkFacilityApplyMas";
			$disMap['status']=1;
			$disMap['manageid']=getFieldBy($val['appsubid'], "id", "manageid", "mis_work_facility_apply_sub");
			$dissavedate=array();
			$dissavedate['appqty']=array("exp","appqty-".$val['qty']);
			$disDate['returnqty']=array("exp","returnqty-".$val['qty']);
			$saveresult=$MisWorkFacilityDistributeModel->where($disMap)->save($dissavedate);
			$MisWorkFacilityDistributeModel->commit();
			if($saveresult){
				$disDate=array();
				$disDate['objid']=$id;
				$disDate['objmodel']="mis_work_facility_abnormalmove";
				$disDate['operation']=2;
				$disDate['remark']=getFieldBy($id, "id", "remark", "mis_work_facility_abnormalmove");
				$disDate['manageid']=$val['manageid'];
				$disDate['managename']= $val['managename'];
				$disDate['equipmenttype']=$val['equipmenttype'];
				$disDate['appqty']=$val['qty'];
				$disDate['olddeptid']=$val['olddeptid'];
				$disDate['oldqty']=$val['oldqty'];
				$disDate['oldplace']=getFieldBy(getFieldBy($val['appsubid'], "id", "masid", "mis_work_facility_apply_sub"), "id", "place", "mis_work_facility_apply_mas");
				$disDate['applydepartmentid']=getFieldBy($id, "id", "movedeptid", "mis_work_facility_abnormalmove");
				$disDate['place']=getFieldBy($id, "id", "place", "mis_work_facility_abnormalmove");;
				$disDate['createid']=getFieldBy($id, "id", "createid", "mis_work_facility_abnormalmove");
				$disDate['createtime']=time();
				$result=$MisWorkFacilityDistributeModel->add($disDate);
				if(!$result){
					$this->error("操作失败！");
				}
			}
		}
	}
	public function lookupworkfacilityabnormalmove(){
		//实例化办公设备类型表
		$dptmodel = D("MisWorkFacilityType");
		$MisWorkFacilityManagemodel = D("MisWorkFacilityManage");
		//查name和id
		$deptlist = $dptmodel->where("status=1")->order("id asc")->field('id,name,pid as parentid')->select();
		$treemiso[]=array(
				'id'=>0,
				'pId'=>-1,
				'name'=>'设备类型',
				'rel'=>'lookupworkfacilityabnormalmoveli',
				'target'=>'ajax',
				'url'=>'__URL__/lookupworkfacilityabnormalmove/jump/1',
				'open'=>true
		);
		$param['rel']="lookupworkfacilityabnormalmoveli";
		$param['url']="__URL__/lookupworkfacilityabnormalmove/jump/1/equipmenttype/#id#";
		$typeTree = $this->getTree($deptlist,$param,$treemiso);
		$this->assign('tree',$typeTree);
		$map=$this->_search("MisWorkFacilityApplySub");
		$deptid=$this->escapeChar($_REQUEST['equipmenttype']);
		$map['kymove']=array("neq",0);
		if($deptid){
			//加入递归
			$deptlist =array_unique(array_filter (explode(",",$this->findAlldept($deptlist,$deptid))));
			$magemap=array();
			$magemap['equipmenttype']=array("in",$deptlist);
			$MisWorkFacilityManagelist=$MisWorkFacilityManagemodel->where($magemap)->getField("id,equipmentname");
			$map['manageid']= array("in",array_keys($MisWorkFacilityManagelist));
			//查询此类型的设备di
			$this->assign("equipmenttype",$deptid);
		}
		//查询审核通过的申请单
		$MisWorkFacilityApplyMasModel=D('MisWorkFacilityApplyMas');
		$MisWorkFacilityApplyMasList=$MisWorkFacilityApplyMasModel->where("status=1 and auditState=3")->getField("id,orderno");
		$map['masid']=array("in",array_keys($MisWorkFacilityApplyMasList));
		$Common=A('Common');//
		$Common->_list('MisWorkFacilityApplySub', $map);
		if($_REQUEST['jump']){
			$this->display('lookupworkfacilityabnormalmovelist');
		}else{
			$this->display();
		}
	}
	public function lookupsubdelete(){
		$id = $_POST['id'];// 明细ID
		$model = D("MisWorkFacilityAbnormalmovesub");
		$map['id'] = $id;
		$res = $model->where($map)->setField ( 'status', - 1 );
		$model->commit();
		//还原可异动数量
		if($res){
			$MisWorkFacilityApplySubModel=D('MisWorkFacilityApplySub');
			$ManageDate=array();
			$ManageDate['id']=getFieldBy($id, 'id', 'appsubid', 'mis_work_facility_abnormalmovesub');
			$ManageDate['kymove']=array("exp","kymove+".getFieldBy($id, 'id', 'qty', 'mis_work_facility_abnormalmovesub'));
			$MisWorkFacilityApplySubModel->save($ManageDate);
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