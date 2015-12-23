<?php
/**
 *
 * @Title: MisWorkFacilityInventoryction
 * @Package package_name
 * @Description: todo(设备库存状态)
 * @author renling
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-7-19 上午11:50:33
 * @version V1.0
 */
class MisWorkFacilityDistributeAction extends CommonAction  {
	public function lookupdistribute(){
		$map=$this->_search();
		//实例化办公设备类型表
		$dptmodel = D("MisWorkFacilityType");
		//查name和id
		$deptlist = $dptmodel->where("status=1")->order("id asc")->field('id,name,pid as parentid')->select();
		$treemiso[]=array(
				'id'=>0,
				'pId'=>-1,
				'name'=>'设备类型',
				'rel'=>'misworkfacilitydistributeli',
				'target'=>'ajax',
				'url'=>'__URL__/lookupdistribute/jump/1',
				'open'=>true
		);
		$param['rel']="misworkfacilitydistributeli";
		$param['url']="__URL__/lookupdistribute/jump/1/equipmenttype/#id#";
		$typeTree = $this->getTree($deptlist,$param,$treemiso);
		$this->assign('tree',$typeTree);
		$equipmenttype=$_REQUEST['equipmenttype'];
		$id=$_REQUEST['id'];
		if($id){
			$map['manageid']=$id;
			$equipmenttype=getFieldBy($id, 'id', 'equipmenttype', 'mis_work_facility_manage');
		}
		$map['appqty']=array("neq",0);
		if($equipmenttype){
			//加入递归
			$deptlist =array_unique(array_filter (explode(",",$this->findAlldept($deptlist,$equipmenttype))));
			$map['equipmenttype']=array("in",$deptlist);
			$this->assign("equipmenttype",$equipmenttype);
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
			$this->_list ($name, $map );
		}
		$scdmodel = D('SystemConfigDetail');
		//扩展工具栏操作
		$toolbarextension = $scdmodel->getDetail($name,true,'toolbar');
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		$detailList = $scdmodel->getDetail($name);
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		if($_REQUEST['jump']){
			$this->display("lookupdistributeview");
		}else{
		 $this->display();
		}
	}
	/**
	 * @Title: _before_add
	 * @Description: todo(add页面的前置函数)
	 * @author xiafengqin
	 * @date 2013-9-22 下午4:37:03
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
		$orderno = $scnmodel->GetRulesNO('mis_work_facility_manage');
		$this->assign("orderno", $orderno);
		//订单号是否可写
		$writable= $scnmodel->GetWritable('mis_work_facility_manage');
		$this->assign("writable",$writable);
		//单位
		$MisProductUnitModel = D('MisProductUnit');
		$unitList = $MisProductUnitModel->where('status=1 and typeid=1')->select();
		$this->assign('unitList',$unitList);
		//选择类型
		$typeModel = D('MisWorkFacilityType');
		$typelist = $typeModel->where('status=1 and pid !=0')->select();
		$this->assign('typelist',$typelist);
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
		//插入附件
		if ($id) {
			$this->swf_upload($id,83);
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
		$model=M("mis_system_department");
		$list =$model->where("status=1")->select();
		$this->assign("deptidlist",$list);
		$id = $_REQUEST['id'];
		//单位
		$MisProductUnitModel = D('MisProductUnit');
		$unitList = $MisProductUnitModel->where('status=1 and  typeid=1')->select();
		$this->assign('unitList',$unitList);
		//获取附件信息
		$this->getAttachedRecordList(83,$_REQUEST['id']);
		//编号可写
		$model1	=D('SystemConfigNumber');
		$writable= $model1->GetWritable('mis_work_facility_manage');
		$this->assign("writable",$writable);
		//选择类型
		$typeModel = D('MisWorkFacilityType');
		$typelist = $typeModel->where('status=1  and pid!=0')->select();
		$this->assign('typelist',$typelist);
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
		//保存附件
		$this->swf_upload($_REQUEST['id'],83);
	}
}
?>