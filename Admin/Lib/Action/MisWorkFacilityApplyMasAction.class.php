<?php
/**
 * @Title: MisWorkFacilityApplyMasAction
 * @Package package_name
 * @Description: todo(办公设备申请)
 * @author xiafengqin
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-9-18 下午4:01:31
 * @version V1.0
 */
class MisWorkFacilityApplyMasAction extends CommonAuditAction {
	/**
	 * @Title: _before_add
	 * @Description: todo(add页面的前置函数)
	 * @author xiafengqin
	 * @date 2013-9-22 上午9:49:53
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
		$orderno = $scnmodel->GetRulesNO('mis_work_facility_apply_mas');
		$this->assign("orderno", $orderno);
		//订单号是否可写
		$writable= $scnmodel->GetWritable('mis_work_facility_apply_mas');
		$this->assign("writable",$writable);
		//获取当天时间
		$curday = date("Y-m-d", time());
		$this->assign("curday", $curday);
		//创建时间
		$this->assign('createtime', time());
	}
	public function lookuptypeselect(){
		//选择类型
		$typeModel = D('MisWorkFacilityType');
		$typelist = $typeModel->where('status=1')->select();
		$this->assign('typelist',$typelist);
		$this->display();
	}
	public function _before_insert() {
		$this->checkifexistcodeororder('mis_work_facility_apply_mas','orderno');
	}
	/**
	 * @Title: _after_insert
	 * @Description: todo(insert的后置函数)
	 * @param unknown_type $masid  表头的id
	 * @author xiafengqin
	 * @date 2013-9-22 上午9:50:47
	 * @throws
	 */
	public function _after_insert($masid){
		//插入附件
		if ($masid) {
			$this->swf_upload($masid,82);
			$MisWorkFacilityApplySubModel = D('MisWorkFacilityApplySub');
			$MisWorkFacilityManageModel=D('MisWorkFacilityManage');
			$manageid=$_POST['manageid'];
			if($masid){
				foreach ($manageid as $key=>$val){
					$date=array();
					$date['masid']=$masid;
					$date['manageid']=$val;
					$date['appqty']=str_replace(",","",$_POST['appqty'][$key]);
					$date['qty']=getFieldBy($val, 'id', 'qty', 'mis_work_facility_manage');
					$date['kyqty']=getFieldBy($val, 'id', 'kyqty', 'mis_work_facility_manage');
					$date['kymove']=str_replace(",","",$_POST['appqty'][$key]);
					$date['createid']=$_SESSION[C('USER_AUTH_KEY')];
					$date['createtime']=time();
					//保存当前数据对象
					$list=$MisWorkFacilityApplySubModel->add($date);
					if($list){
						//修改库存
						$managedate=array();
						$managedate['id']=$val;
						//修可用数量
						$managedate['kyqty']=array("exp","kyqty-".str_replace(",","",$_POST['appqty'][$key]));
						$MisWorkFacilityManageModel->data($managedate)->save();
					}
				}
			}
		}
	}
	/**
	 * @Title: _before_edit
	 * @Description: todo(edit页面的前置函数)
	 * @author xiafengqin
	 * @date 2013-9-22 上午10:03:13
	 * @throws
	 */
	public function _before_edit(){
		$id = $_REQUEST['id'];
		//部门
		$model=M("mis_system_department");
		$list =$model->where("status=1")->select();
		$deptlist=$this->selectTree($list,0,0,getFieldBy($id, 'id', 'applydepartmentid', 'mis_work_facility_apply_mas'));
		$this->assign("deptidlist",$deptlist);
		//当前申请单的明细
		$model = D("MisWorkFacilityApplySub");
		$map['masid'] = $id;
		$map['status'] = 1;
		$sublist = $model->where($map)->select();
		foreach ($sublist as $key=>$val){  //计算可用数量
			$sublist[$key]['sumkyqty']=getFieldBy($val['manageid'], 'id', 'kyqty', 'mis_work_facility_manage')+$val['appqty'];
		}
		$this->assign("sublist",$sublist);
		//获取附件信息
		$this->getAttachedRecordList($_REQUEST['id']);
		//编号可写
		$model1	=D('SystemConfigNumber');
		$writable= $model1->GetWritable('mis_business_contract_fixation');
		$this->assign("writable",$writable);
	}
	public function _before_update() {
		$this->checkifexistcodeororder('mis_work_facility_apply_mas','orderno',1);
	}
	/**
	 * @Title: _after_update
	 * @Description: todo(修改的后置函数)
	 * @param unknown_type $list
	 * @author xiafengqin
	 * @date 2013-9-22 上午11:21:16
	 * @throws
	 */
	public function _after_update($list) {
		if($list && !$_POST['beforeInsert']){
			$masid = $_POST['id'];
			//保存附件
			$this->swf_upload($_REQUEST['id'],82);
			$MisWorkFacilityApplySubModel = D('MisWorkFacilityApplySub');
			$MisWorkFacilityManageModel=D('MisWorkFacilityManage');
			$manageid=$_POST['manageid'];
			foreach ($manageid as $key=>$val){
				$date=array();
				$date['masid']=$masid;
				$date['manageid']=$val;
				$date['appqty']=str_replace("," ,"",$_POST['appqty'][$key]);
				$date['qty']=getFieldBy($val, 'id', 'qty', 'mis_work_facility_manage');
				$date['kyqty']=getFieldBy($val, 'id', 'kyqty', 'mis_work_facility_manage');
				$date['kymove']=str_replace(",","",$_POST['appqty'][$key]); //可异动数量
				//保存当前数据对象
				if($_POST['subid'][$key]){
					$date['updateid']=$_SESSION[C('USER_AUTH_KEY')];
					$date['updatetime']=time();
					$date['id']=$_POST['subid'][$key];
					$list=$MisWorkFacilityApplySubModel->save($date);
				}else{
					//新增申请数据
					$date['createid']=$_SESSION[C('USER_AUTH_KEY')];
					$date['createtime']=time();
					$list=$MisWorkFacilityApplySubModel->add($date);
				}
				if($list){
					//修改库存
					$managedate=array();
					$managedate['id']=$val;
					//修可用数量
					if($_POST['subid'][$key]){
						$managedate['kyqty']=array("exp","kyqty+".$_POST['oldappqty'][$key]."-".str_replace(",","",$_POST['appqty'][$key]));
					}else{
						$managedate['kyqty']=array("exp","kyqty-".str_replace(",","",$_POST['appqty'][$key]));
					}
					$MisWorkFacilityManageModel->data($managedate)->save();
				}
			}
		}
	}
	/**
	 * @Title: subdelete
	 * @Description: todo(删除明细)
	 * @author xiafengqin
	 * @date 2013-9-22 上午11:21:36
	 * @throws
	 */
	public function lookupsubdelete(){
		$id = $_POST['id'];// 明细ID
		$model = D("MisWorkFacilityApplySub");
		$map['id'] = $id;
		$res = $model->where($map)->setField ( 'status', - 1 );
		$model->commit();
		//还原可用数量
		if($res){
			$MisWorkFacilityManageModel=D('MisWorkFacilityManage');
			$ManageDate=array();
			$ManageDate['id']=getFieldBy($id, 'id', 'manageid', 'mis_work_facility_apply_sub');
			$ManageDate['kyqty']=array("exp","kyqty+".getFieldBy($id, 'id', 'appqty', 'mis_work_facility_apply_sub'));
			$MisWorkFacilityManageModel->save($ManageDate);
		}
		echo $res;
	}
	/**
	 *
	 * @Title: lookupworkfacilitymanage
	 * @Description: todo(查看设备详情)
	 * @author renling
	 * @date 2014-7-22 下午4:15:10
	 * @throws
	 */ 
	public function lookupworkfacilitymanage(){
		//实例化办公设备类型表
		$dptmodel = D("MisWorkFacilityType");
		//查name和id
		$deptlist = $dptmodel->where("status=1")->order("id asc")->field('id,name,pid as parentid')->select();
		$treemiso[]=array(
				'id'=>0,
				'pId'=>-1,
				'name'=>'设备类型',
				'rel'=>'lookupworkfacilitymanagelistli',
				'target'=>'ajax',
				'url'=>'__URL__/lookupworkfacilitymanage/jump/1',
				'open'=>true
		);
		$param['rel']="lookupworkfacilitymanagelistli";
		$param['url']="__URL__/lookupworkfacilitymanage/jump/1/equipmenttype/#id#";
		$typeTree = $this->getTree($deptlist,$param,$treemiso);
		$this->assign('tree',$typeTree);
		$map=$this->_search("MisWorkFacilityManage");
		$deptid=$_REQUEST['equipmenttype'];
		if($deptid){
			//加入递归
			$deptlist =array_unique(array_filter (explode(",",$this->findAlldept($deptlist,$deptid))));
			$map['equipmenttype']=array("in",$deptlist);
			$this->assign("equipmenttype",$deptid);
		}
		$Common=A('Common');//
		$Common->_list('MisWorkFacilityManage', $map);
		if($_REQUEST['jump']){ 
			$this->display('lookupworkfacilitymanagelist');
		}else{
			$this->display();
		}
	}
	/**
	 * @Title: _before_auditEdit
	 * @Description: todo(打开审核预览前置函数)
	 * @author xiafengqin
	 * @date 2013-9-22 上午11:38:02
	 * @throws
	 */
	public function _before_auditEdit(){
		//获取附件信息
		$this->getAttachedRecordList($_REQUEST['id']);
		//当前申请单的明细
		$model = D("MisWorkFacilityApplySub");
		$map['masid'] = $_REQUEST['id'];
		$map['status'] = 1;
		$sublist = $model->where($map)->select();
		foreach ($sublist as $key=>$val){  //计算可用数量
			$sublist[$key]['sumkyqty']=getFieldBy($val['manageid'], 'id', 'kyqty', 'mis_work_facility_manage')+$val['appqty'];
		}
		$this->assign("sublist",$sublist);
	}
	/**
	 * @Title: _before_auditView
	 * @Description: todo()
	 * @author xiafengqin
	 * @date 2013-9-22 上午11:52:47
	 * @throws
	 */
	public function _before_auditView(){
		//当前申请单的明细
		$model = D("MisWorkFacilityApplySub");
		$map['masid'] = $_REQUEST['id'];
		$map['status'] = 1;
		$sublist = $model->where($map)->select();
		foreach ($sublist as $key=>$val){  //计算可用数量
			$sublist[$key]['sumkyqty']=getFieldBy($val['manageid'], 'id', 'kyqty', 'mis_work_facility_manage')+$val['appqty'];
		}
		$this->assign("sublist",$sublist);
		//获取附件信息
		$this->getAttachedRecordList($_REQUEST['id']);
	}
	public function delete(){
		$id=$_REQUEST['id'];
		$oldstatus=getFieldBy($id, "id", "status", "MisWorkFacilityApplyMas");
		$id = $_POST['id'];// 明细ID
		$model = D("MisWorkFacilityApplyMas");
		$submodel = D("MisWorkFacilityApplySub");
		$map['id'] = $id;
		$res = $model->where($map)->setField ( 'status', - 1 );
		if($res && $oldstatus!=-1){
			$MisWorkFacilityManageModel=D('MisWorkFacilityManage');
			//查找该mas下的sub
			$subMap['masid']=$id;
			$subMap['status']=1;
			$subList=$submodel->where($subMap)->select();
			foreach ($subList as $key=>$val){
				$submodel->where("id=".$val['id']." and status=1")->setField ( 'status', - 1 );
				$ManageDate=array();
				$ManageDate['id']=$val['manageid'];
				$ManageDate['kyqty']=array("exp","kyqty+".$val['appqty']);
				$MisWorkFacilityManageModel->save($ManageDate);
			}
		}
		if ($res!==false) {
			$this->success ( L('_SUCCESS_') );
		} else {
			$this->error ( L('_ERROR_') );
		}
	}
	/**
	 *
	 * @Title: overProcess
	 * @Description: todo(审核完成后添加分布图)
	 * @param 单据ID $id
	 * @author renling
	 * @date 2014-7-23 下午4:22:13
	 * @throws
	 */
	public function overProcess($id){
		$submodel=D("MisWorkFacilityApplySub");
		$subMap['masid']=$id;
		$subMap['status']=1;
		$subList=$submodel->where($subMap)->select();
		$MisWorkFacilityDistributeModel=D('MisWorkFacilityDistribute');
		$MisWorkFacilityInventoryModel=M('MisWorkFacilityInventory');
		foreach ($subList as $key=>$val){
			$disDate=array();
			$disDate['objid']=$id;
			$disDate['objmodel']="MisWorkFacilityApplyMas";
			$disDate['operation']=1;
			$disDate['remark']=getFieldBy($id, "id", "remark", "mis_work_facility_apply_mas");
			$disDate['manageid']=$val['manageid'];
			$disDate['managename']=getFieldBy($val['manageid'], 'id', 'equipmentname', "mis_work_facility_manage");
			$disDate['equipmenttype']=getFieldBy($val['manageid'], 'id', 'equipmenttype', "mis_work_facility_manage");
			$disDate['appqty']=$val['appqty'];
			$disDate['returnqty']=$val['appqty'];
			$disDate['applydepartmentid']=getFieldBy($id, "id", "applydepartmentid", "mis_work_facility_apply_mas");
			$disDate['place']=getFieldBy($id, "id", "place", "mis_work_facility_apply_mas");;
			$disDate['createid']=getFieldBy($id, "id", "createid", "mis_work_facility_apply_mas");
			$disDate['createtime']=time();
			$result=$MisWorkFacilityDistributeModel->add($disDate);
			if(!$result){
				$this->error("操作失败！");
			}
			//添加库存动态表
			$indate=array();
			$indate['objid']=$id;
			$indate['objmodel']="MisWorkFacilityApplyMas";
			$indate['manageid']=$val['manageid'];
			$indate['managename']=getFieldBy($val['manageid'], 'id', 'equipmentname', "mis_work_facility_manage");
			$indate['equipmenttype']=getFieldBy($val['manageid'], 'id', 'equipmenttype', "mis_work_facility_manage");
			$indate['operation']=3;//申请
			$indate['qty']=$val['appqty'];//申请数量
			$indate['createid']=$_SESSION[C('USER_AUTH_KEY')];
			$indate['deptid']=getFieldBy($_SESSION[C('USER_AUTH_KEY')], "id", "dept_id", "user");
			$indate['createtime']=time();
			$indate['remark']=getFieldBy($id, "id", "remark", "mis_work_facility_apply_mas");
			$MisWorkFacilityInventoryResult=$MisWorkFacilityInventoryModel->data($indate)->add();
			$MisWorkFacilityInventoryModel->commit();
		}
	}
}