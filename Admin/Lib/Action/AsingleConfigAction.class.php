<?php
/**
 * @Title: SystemTypeViewAction
 * @Package 基础配置-系统单个配置：配置文件操作类
 * @Description: TODO(系统某些单独配置信息的显示及更新)
 * @author yangxi
 * @company 重庆特米洛科技有限公司˾
 * @copyright 重庆特米洛科技有限公司˾
 * @date 2013-1-10 19:18:54
 * @version V1.0
 */
class AsingleConfigAction extends CommonAction {
	/**
	 * @Title: index
	 * @Description: todo(重写CommonAction的index方法，展示列表)
	 * @return string
	 * @author 杨希
	 * @date 2013-5-31 下午3:59:44
	 * @throws
	 */	
	public function index() {
		// 获取流程号
		$model2=D("ProcessInfo");
		$procelist=$model2->where("status=1")->getField("id,name");
		$this->assign("procelist",$procelist);
		//获取调整单据类型
		$OrderTypes   =D("MisOrderTypes");
		$OrderTypesList = $OrderTypes->where("type='11' and status=1")->getField("id,name");
		$this->assign('aduList',$OrderTypesList);
		//获取采购申请单据类型
		$OrderTypesList = $OrderTypes->where("type='05' and status=1")->getField("id,name");
		$this->assign('appList',$OrderTypesList);
		//获取销售退货审核后生成物料入库单据类型
		//物料入库之采购入库
		$OrderTypesList = $OrderTypes->where("type='08' and status=1")->getField("id,name");
		$this->assign('intoList',$OrderTypesList);
		//销售退货生成入库单默认仓库
		$whmodel	= D('MisInventoryWarehouse');
		$whmap['status'] = 1;
		$whlist		= $whmodel->where($whmap)->getField("id,name");
		$this->assign('whlist',$whlist);
		//采购退货完成自动生成出库单类型  //物料出库单据之销售出库
		$OrderTypesList =$OrderTypes->where("type='09' and status=1")->getField("id,name");
		$this->assign('outList',$OrderTypesList);
		//容积和重量单位
		$smap['status'] = 1;
		$list = getOptionList('mis_product_unit',$smap);
		$this->assign('unitlist',$list);
		//获取配置文件内容
		$ConfigListModel= D('SystemConfigList');
		$list=$ConfigListModel->GetAllValue();
		$this->assign("list",$list);
		$this->display();
	}
	
	/**
	 * @Title: update
	 * @Description: todo(重写CommonAction的update方法，更新)
	 * @return string
	 * @author 杨东
	 * @date 2013-5-31 下午3:59:44
	 * @throws
	 */
	public function update(){
		$auditState=$_POST['auditState'];
		$ConfigListModel = D('SystemConfigList');
		$list = $ConfigListModel->GetAllValue();
		$list1= array();
		foreach ($list as $key=>$val){
			if ($key == 'auditState') {
				$list2 = array();
				foreach ($val as $k1=>$v1){
					$list2[$k1] = array(
						'id'=> $v1['id'],
						'name'=>$auditState[$v1['id']]
					);
				}
				$list1[$key] = $list2;
			} else {
				$list1[$key] = $_POST[$key];
			}
		}
		$ConfigListModel= D('SystemConfigList');
		$ConfigListModel->setValue($list1);
		$this->success ( L('_SUCCESS_') );
	}
}
?>