<?php
/**
 * @Title: MisWorkFacilityManageAction
 * @Package package_name
 * @Description: todo(办公设备管理)
 * @author xiafengqin
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-9-22 下午2:32:26
 * @version V1.0
 */
class MisWorkFacilityManageAction extends CommonAction  {
	public function index(){
		if($_REQUEST['step']){
			//实例化办公设备类型表
			$dptmodel = D("MisWorkFacilityType");
			//查name和id
			$deptlist = $dptmodel->where("status=1")->order("id asc")->field('id,name,pid as parentid')->select();
			$treemiso[]=array(
					'id'=>0,
					'pId'=>-1,
					'name'=>'设备类型',
					'rel'=>'misworkfacilitymanage',
					'target'=>'ajax',
					'url'=>'__URL__/index/step/1/jump/1',
					'open'=>true
			);
			$param['rel']="misworkfacilitymanage";
			$param['url']="__URL__/index/step/1/jump/1/equipmenttype/#id#";
			$typeTree = $this->getTree($deptlist,$param,$treemiso);
			$this->assign('tree',$typeTree);
			$map=$this->_search();
			$deptid=$this->escapeChar($_REQUEST['equipmenttype']);
			if($deptid){
				//加入递归
				$deptlist =array_unique(array_filter (explode(",",$this->findAlldept($deptlist,$deptid))));
				$map['equipmenttype']=array("in",$deptlist);
				$this->assign("equipmenttype",$deptid);
			}
			//动态配置显示字段
			$name=$this->getActionName();
			if (! empty ( $name )) {
				$qx_name=$name;
				if(substr($name, -4)=="View"){
					$qx_name = substr($name,0, -4);
				}
				//验证浏览及权限
				if( !isset($_SESSION['a']) ){
					$map=D('User')->getAccessfilter($qx_name,$map);	
				}
				$_REQUEST ['orderField']="kyqty";
				$_REQUEST ['orderDirection']="asc";
				$this->_list ($name, $map );
			}
			$scdmodel = D('SystemConfigDetail');
			$detailList = $scdmodel->getDetail($name);
			if ($detailList) {
				$this->assign ( 'detailList', $detailList );
			}
			//扩展工具栏操作
			$toolbarextension = $scdmodel->getDetail($name,true,'toolbar');
			if ($toolbarextension) {
				$this->assign ( 'toolbarextension', $toolbarextension );
			}

			if ($_REQUEST['jump']) {
				$this->display('indexview');
			} else {
				$this->display("lookupindexmanage");
			}
		}else{
			$this->display();
		}
	}
	/**
	 * @Title: _before_insert
	 * @Description: todo(新增的前置函数)
	 * @author xiafengqin
	 * @date 2013-9-22 下午4:38:23
	 * @throws
	 */
	public function _before_insert() {
		$this->checkifexistcodeororder('mis_work_facility_manage','orderno');
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
		$this->addInvetory($id,1);
	}
	private function addInvetory($id,$type,$oldty){
		//插入库存操作表
		$indate=array();
		$indate['manageid']=$id;
		$indate['objid']=$id;
		$indate['objmodel']="MisWorkFacilityManage";
		$indate['managename']=getFieldBy($id, 'id', 'equipmentname', 'mis_work_facility_manage');
		$indate['equipmenttype']=getFieldBy($id, 'id', 'equipmenttype', 'mis_work_facility_manage');
		$indate['operation']=$type;//添加 或修改库存
		$indate['qty']=str_replace(',','',$_POST['qty']);//添加库存
		 $indate['oldqty']=$oldty;
		$indate['createid']=$_SESSION[C('USER_AUTH_KEY')];
		$indate['deptid']=getFieldBy($_SESSION[C('USER_AUTH_KEY')], "id", "dept_id", "user");
		$indate['createtime']=time();
		$indate['remark']=getFieldBy($id, "id", "remark", "mis_work_facility_manage");
		$MisWorkFacilityInventoryModel=M('mis_work_facility_inventory');
		$MisWorkFacilityInventoryResult=$MisWorkFacilityInventoryModel->data($indate)->add();
		$MisWorkFacilityInventoryModel->commit();
	}
	/**
	 * @Title: _before_edit
	 * @Description: todo(edit页面都前置函数)
	 * @author xiafengqin
	 * @date 2013-9-22 下午4:40:23
	 * @throws
	 */
	public function _before_edit(){
		//获取附件信息
		$this->getAttachedRecordList($_REQUEST['id']);
	}
	/**
	 * @Title: _before_update
	 * @Description: todo(修改的前置函数)
	 * @author xiafengqin
	 * @date 2013-9-22 下午4:41:01
	 * @throws
	 */
	public function _before_update() {
		$this->checkifexistcodeororder('mis_work_facility_manage','orderno',1);
	}
	/**
	 * @Title: _after_update
	 * @Description: todo(修改的后置函数)
	 * @param unknown_type $list
	 * @author xiafengqin
	 * @date 2013-9-22 下午4:41:45
	 * @throws
	 */
	public function _after_update($list) {
		$this->addInvetory($_REQUEST['id'],2,$_POST['oldqty']);
	}
}
?>