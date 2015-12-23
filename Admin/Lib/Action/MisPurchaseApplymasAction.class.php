<?php
/**
 * @Title: MisPurchaseApplymasAction
 * @Package package_name
 * @Description: todo(采购申请单管理)
 * @author liminggang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2012-1-7 下午2:39:41
 * @version V1.0
 */
class MisPurchaseApplymasAction extends CommonAuditAction {
	/** @Title: _filter
	 * @Description: (构造检索条件)
	 * @author
	 * @date 2013-5-31 下午3:59:44
	 * @throws
	 */
	public function _filter(&$map){
		if ($_SESSION["a"] != 1){
			$map['status']=array("gt",-1);
		}
	}
	public function _before_add(){
		$this->comm();
	}
	public function _before_edit(){
		$this->comm();
		$this->subcomm();
	}
	public function _before_auditEdit(){
		$this->subcomm();
	}
	public function _before_auditView(){
		$this->subcomm();
	}
	
	/**
	 * @Title: comm
	 * @Description: todo(公共查询)
	 * @author
	 * @throws
	 */
	public function comm(){
		//报销编号自动生成&报销编号可写
		$scnmodel = D("SystemConfigNumber");
		$writable = $scnmodel->GetWritable('mis_purchase_applymas');
		$orderno = $scnmodel->GetRulesNO('mis_purchase_applymas');
		$this->assign('writable',$writable);
		$this->assign('orderno',$orderno);
		//人员id
		$uid=$_SESSION[C('USER_AUTH_KEY')];
		$this->assign("uid",$uid);
		//获取订单类型
		$model = D('MisOrderTypes');
		$list = $model->where("type='5' and status=1")->select();
		$this->assign('typeidlist', $list);
		//制单人信息
		$this->assign("time",time());
	}
	public function subcomm(){
		//查询 sublist
		$PurchaseApplysubModel=D("MisPurchaseApplysub");
		$sublist=$PurchaseApplysubModel->where("masid=".$_REQUEST['id'])->select();
		$this->assign("sublist",$sublist);
	}
	/**
	 * @Title: _after_insert
	 * @Description: todo(新增 插入sub表明细)
	 * @param unknown_type $list
	 * @author libo
	 * @date 2014-6-25 下午5:25:12
	 * @throws
	 */
	public function _after_insert($list){
		$PurchaseApplysubModel=D("MisPurchaseApplysub");
		if($_POST['arr_nd']){
			$data=array();
			foreach ($_POST['arr_nd'] as $k=>$v){
				$data['masid']=$list;
				$data['nd']=$_POST['arr_nd'][$k];
				$data['type']=$_POST['arr_type'][$k];
				$data['name']=$_POST['arr_name'][$k];
				$data['psize']=$_POST['arr_psize'][$k];
				$data['unit']=$_POST['arr_unit'][$k];
				$data['price']=floatval(str_replace(",","",$_POST['arr_price'][$k]));
				$data['qty']=$_POST['arr_qty'][$k];
				$data['apamount']=floatval(str_replace(",","",$_POST['arr_apamount'][$k]));
				$data['createid']=$_SESSION[C('USER_AUTH_KEY')];
				$data['createtime']=time();
				$re=$PurchaseApplysubModel->add($data);
				$PurchaseApplysubModel->commit();
			}
		}
	}
	/**
	 * @Title: _after_update
	 * @Description: todo(修改 sub明细)
	 * @param unknown_type $list
	 * @author libo
	 * @date 2014-6-26 上午9:44:37
	 * @throws
	 */
	public function _after_update($list){
		$PurchaseApplysubModel=D("MisPurchaseApplysub");
		//查询是否有明细
		$PurchaseApplysublist=$PurchaseApplysubModel->where("masid=".$_POST['id'])->select();
		if($PurchaseApplysublist){
			//删除原始明细
			$re=$PurchaseApplysubModel->where("masid=".$_POST['id'])->delete();
			if($re===false){
				$this->error("修改详细信息失败");
			}
		}
		//新增 修改后的明细
		if($_POST['arr_nd']){
			$data=array();
			foreach ($_POST['arr_nd'] as $k=>$v){
				$data['masid']=$_POST['id'];
				$data['nd']=$_POST['arr_nd'][$k];
				$data['type']=$_POST['arr_type'][$k];
				$data['name']=$_POST['arr_name'][$k];
				$data['psize']=$_POST['arr_psize'][$k];
				$data['unit']=$_POST['arr_unit'][$k];
				$data['price']=floatval(str_replace(",","",$_POST['arr_price'][$k]));
				$data['qty']=$_POST['arr_qty'][$k];
				$data['apamount']=floatval(str_replace(",","",$_POST['arr_apamount'][$k]));
				$data['updateid']=$_SESSION[C('USER_AUTH_KEY')];
				$data['updatetime']=time();
				$re=$PurchaseApplysubModel->add($data);
				$PurchaseApplysubModel->commit();
			}
		}
	}
	
}
?>