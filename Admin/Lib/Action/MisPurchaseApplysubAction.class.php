<?php
/**
 * @Title: MisPurchaseApplysubAction
 * @Package package_name
 * @Description: todo(采购申请详情管理)
 * @author liminggang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2012-1-7 下午5:34:34
 * @version V1.0
 */
class MisPurchaseApplysubAction extends MisProductSearchFilterAction {
 
	/**
	 * (non-PHPdoc)
	 * 新增采购申请单详情。
	 * @see CommonAction::insert()
	 */
	public function insert() {
		//采购申请表
		$MisPurchaseApplymasDAO = M('mis_purchase_applymas');		
		//采购申请详情表
		$MisPurchaseApplysubModel = D('MisPurchaseApplysub');		
		//获取提交过来的所有数据
		$ids = $_POST['ids'];     				//产品编号
		$masid = $_POST['masid'];  				//采购申请单ID
		$qty = $_POST['qty'];    				//数量
		$unitid = $_POST['unitid']; 			//单位
		$whid = $_POST['whid']; 			//单位
		$showprice = $_POST['showprice'];  		//单价
		$remark = $_POST['remark'];  			//备注
		//这里分为2中情况，第一种，单个插入，第二种，插入输入了数量的
		
		//第一种、获取单个插入的数据key值
		$listkey=$_REQUEST['key'];
		//判断$listkey是否被赋值
		if(isset($listkey)){
			foreach($qty as $k=>$v){
				if($listkey != $ids[$k]){
					unset($qty[$k]);
					unset($ids[$k]);
					unset($showprice[$k]);
					unset($whid[$k]);
					unset($remark[$k]);
				}
			}
		}else{
			foreach($qty as $k=>$v){
				if($v <=0 ){
					unset($qty[$k]);
					unset($ids[$k]);
					unset($showprice[$k]);
					unset($whid[$k]);
					unset($remark[$k]);
				}
			}
		}
		//获取详情中相同类型下面的最大序列值
		$nd = $MisPurchaseApplysubModel->where('masid='.$masid)->max('nd');
		//获得采购订单单头的一些基本内容
		$maslist = array();
		$maslist=$MisPurchaseApplymasDAO->where('id = '.$masid.' and status = 1')->field('istax,taxid,projectid')->find();
		//获得税金组的转换
		$MisTaxGroupDao = M("mis_tax_group");
		$taxValue=$MisTaxGroupDao->where('id = '.$maslist['taxid'].' and status =1')->getField("taxdetail");
		$data = array();
		foreach ($qty as $k => $v) {
			$qtyv=str_replace(',', '',$v);
			$showprice[$k]=str_replace(',', '',$showprice[$k]);
			//如果采购订单选中了含税。那么这里就计算成本总价,和成本单价,如果未勾选
			//向前计算单价
			$unitprice[$k]=$showprice[$k]/(100+$taxValue)*100;
			//向前计算总价
			$amount[$k] = $unitprice[$k]*$qtyv;
			//向后计算单价
			$taxunitprice[$k]=$showprice[$k]*(100+$taxValue)/100;
			//向后计算总价
			$taxamount[$k] = $taxunitprice[$k]*$qtyv;
			$nd += 1;
			$data[] = array(
					'masid'=>$masid,
					'nd'=>$nd,
					'prodid' =>$ids[$k],
					//'psize' =>getFieldBy($ids[$k], 'id', 'prodsize', 'mis_product_code'),
					'matchingprodid'=>$ids[$k],
					'unitid'=>$unitid[$k],	
					'qty' =>$qtyv,
					'unitprice'=>$unitprice[$k],
					'showprice'=>$showprice[$k],
					'taxunitprice'=>$taxunitprice[$k],
					'amount'=>$amount[$k],
					'showamount'=>$qtyv*$showprice[$k],
					'taxamount'=>$taxamount[$k],
					'projectid'=>$maslist['projectid'],
					'uninqty'=>$qtyv,
					'taxid'=>$maslist['taxid'],
					'whid'=>$whid[$k],
					'remark'=>$remark[$k],
					'createtime'=>time(),
					'createid'=>$_SESSION[C('USER_AUTH_KEY')],
				);
		}
		$result = $MisPurchaseApplysubModel->addAll($data);
		$unclose = $this->escapeStr($_GET['unclose']);
		if($result){
			$this->updateMasData($masid);
			if( $unclose==1 ){
				$this->success ( L('_SUCCESS_'),'',json_encode($ids));
			}else{
				$this->success ( L('_SUCCESS_') );
			}
		}else{
			$this->error ( L('_ERROR_') );
		}
	}
	/**
	 * (non-PHPdoc)
	 * 查询选中的采购申请明细
	 * @see CommonAction::edit()
	 */
	public function edit(){
		//获得采购申请详情ID。或者ID数组
		$id = $_GET['id'];
		//获得采购申请单头ID
		$masid = $this->escapeStr($_GET['masid']);
		//查询仓库。
		$MisInventoryWarehouseDao = M("mis_inventory_warehouse");
		$whidmap['status'] = 1;
		$whidlist=$MisInventoryWarehouseDao->where($whidmap)->getField("id,name");
		$this->assign('whidlist',$whidlist);
		
		//采购申请模型
		$MisPurchaseApplymasDao=D('MisPurchaseApplymas');
		//得到是否含税标志
		$istax=$MisPurchaseApplymasDao->where('id='.$masid)->getField("istax");
		//采购申请详情视图模型
		$MisPurchaseApplysubViewModel=D("MisPurchaseApplysubView");
		$MisPurchaseApplysubViewModel->viewFields=$this->getOtherView("mis_purchase_applysub",$condition);
		$condition['mis_purchase_applysub.status'] = 1;
		$this->assign("masid",$masid);
		$this->assign("istax",$istax);
		if($_GET['type']){
			// 单个编辑标志
			$condition['mis_purchase_applysub.id'] = $id;
			$vo = $MisPurchaseApplysubViewModel->where($condition)->find();
			$this->assign('vo',$vo);
			//调用公共方法，显示图片;
			$this->getProductImage($vo['prodid']);
			//附件信息
			$this->getAttachedRecordList($masid,true,true,'MisPurchaseApplysub',$id);
			$this->display('edit_detail');
		} else {
			$condition['mis_purchase_applysub.id'] = array('in',$id);
			$this->_list("MisPurchaseApplysubView", $condition);
			$this->display();
		}
	}
	/**
	 * (non-PHPdoc)
	 * 修改选中的采购申请明细
	 * @see CommonAction::update()
	 */
	public function update(){
		//查询sub
		$masid = $_POST['masid'];
		$MisPurchaseApplysubModel = D('MisPurchaseApplysub');
		$type=$_POST['type'];		//判断是根据编辑修改还是根据双击修改。
		//获得税金组的转换
		$MisTaxGroupDao = M("mis_tax_group");
		if($type){
			//获得物资ID
			$ids=$_POST['ids']; 
			//获取税金组ID
			$taxid=$_POST['taxid'];
			//单价
			$showprice=$_POST['showprice'];
			//数量
			$qty=$_POST['qty'];
			//采购仓
			$whid = $_POST['whid'];
			//单位
			$unitid = $_POST['unitid']; 	
			$remark = $_POST['remark'];
			//编辑按钮过来
			foreach($ids as $key=>$val){
				$qty[$key]=str_replace(',', '',$qty[$key]);
				$showprice[$key]=str_replace(',', '',$showprice[$key]);
				$data=array();
				//查询税金组转换系数
				$taxValue=$MisTaxGroupDao->where('id = '.$taxid[$key].' and status =1')->getField("taxdetail");
				//如果采购订单选中了含税。那么这里就计算成本总价,和成本单价,如果未勾选
				//向前计算单价
				$unitprice[$key]=$showprice[$key]/(100+$taxValue)*100;
				//向前计算总价
				$amount[$key] = $unitprice[$key]*$qty[$key];
				//向后计算单价
				$taxunitprice[$key]=$showprice[$key]*(100+$taxValue)/100;
				//向后计算总价
				$taxamount[$key] = $taxunitprice[$key]*$qty[$key];
				
				$data['id'] = $val;
				$data['unitid']		= $unitid[$key];
				$data['taxid']		= $taxid[$key];
				$data['qty'] 		= $qty[$key];
				$data['unitprice'] 	= $unitprice[$key];
				$data['showprice'] 	= $showprice[$key];
				$data['taxunitprice'] 	= $taxunitprice[$key];
				$data['amount'] 	= $amount[$key];
				$data['whid'] 	= $whid[$key];
				$data['showamount'] = $showprice[$key]*$qty[$key];
				$data['taxamount'] 	= $taxamount[$key];
				$data['remark'] 	= $remark[$key];
				//修改采购申请详情数据
				$MisPurchaseApplysubModel->data($data);
				$list=$MisPurchaseApplysubModel->save();
				if(!$list){
					$this->error(L('_ERROR_'));
				}
			}
		}else{
			//获取税金组ID
			$taxid=$_POST['taxid'];
			//查询税金组转换系数
			$taxValue=$MisTaxGroupDao->where('id = '.$taxid.' and status =1')->getField("taxdetail");
			//单价
			$showprice=str_replace(',', '',$_POST['showprice']);
			//数量
			$qty=str_replace(',', '',$_POST['qty']);
			//向前计算单价
			$unitprice=$showprice/(100+$taxValue)*100;
			//向前计算总价
			$amount = $unitprice*$qty;
			//向后计算单价
			$taxunitprice = $showprice*(100+$taxValue)/100;
			//向后计算总价
			$taxamount = $taxunitprice*$qty;
			
			$oldsubid=$_POST['oldsubid'];
			$data=array(
					'id' =>$oldsubid,
					'prodid' =>$_POST['prodid'],
					'unitid'=>$_POST['unitid'],
					'taxid'=>$_POST['taxid'],
					'qty'=>$qty,
					'whid'=>$whid,
					'unitprice'=>$unitprice,
					'showprice'=>$showprice,
					'taxunitprice'=>$taxunitprice,
					'amount'=>$amount,
					'showamount'=>$qty*$showprice,
					'taxamount'=>$taxamount,
					'remark'=>$_POST['remark'],
				);
			// 更新数据
			$MisPurchaseApplysubModel->data($data);
			$list=$MisPurchaseApplysubModel->save ();
			if(!$list){
				$this->error(L('_ERROR_'));
			}else{
				//调用公共方法      swf_upload（）;
				$this->swf_upload($masid,'5',$oldsubid);
			}
		}
		if($list !== false){
			$this->updateMasData($_POST['masid']);
			$this->success ( L('_SUCCESS_') );
		}else{
			$this->error ( L('_ERROR_') );
		}
	}
	/**
	 * (non-PHPdoc)
	 * 删除详情。
	 * @see CommonAction::delete()
	 */
	public function delete($tabmasid){
		$id = $_REQUEST['id'];
		$masid = $_REQUEST ['masid'];
		$model = D('MisPurchaseApplysub');
		if($tabmasid){
			$condition['masid'] = array ('in', $tabmasid );
		} else {
			$condition['id'] = array(' in ',$id);
		}
		$list=$model->where($condition)->delete();
		if($tabmasid){
			foreach ($tabmasid as $k=>$v){
				$this->updateMasData($v);
			}
		} else {
			$this->updateMasData($masid);
		}
		if ($list!==false) {
			if (!$tabmasid) {
				$this->success ( L('_SUCCESS_') );
			}
		} else {
			$this->error ( L('_ERROR_') );
		}
	}
 
	/**
	 * @Title: updateMasData 
	 * @Description: todo(更新mas表数据) 
	 * @param int $id	采购申请单头ID
	 * @param int $istax  是否含税标志
	 * @author laicaixia 
	 * @date 2013-9-3 下午2:15:09 
	 * @throws
	 */
	private function updateMasData($id){
		//查询采购申请sub表,算出ioamount（订单不含税金额）和  oamount（订单含税金额）
		$MisPurchaseApplysubDao = D('mis_purchase_applysub');//采购申请sub表
		$subClist = $MisPurchaseApplysubDao->where('status = 1 and masid='.$id)->field('sum(showamount) as showamount,sum(amount) as amount,sum(taxamount) as taxamount')->find();
		$MisPurchaseApplymasModel = D('MisPurchaseApplymas');
		//获取是否含税标志
		$istax=$MisPurchaseApplymasModel->where("id =".$id." and status = 1")->getField("istax");
		
		$showamount = $subClist['showamount']?$subClist['showamount']:'0';
		$taxamount = $subClist['taxamount']?$subClist['taxamount']:'0';
		$amount = $subClist['amount']?$subClist['amount']:'0';
		
		if($istax){
			//含税
			$data = array(
					'id' => $id,
					'ioamount' => $amount,
					'oamount' => $showamount,
					'invamount' => $showamount
			);
		}else{
			//不含税
			$data = array(
					'id' => $id,
					'ioamount' =>$showamount,
					'oamount' =>$taxamount,
					'invamount' =>$taxamount
			);
		}
		$MisPurchaseApplymasModel->data($data);
		$result=$MisPurchaseApplymasModel->save();
		if(!$result){
			$this->error ( L('_ERROR_') );
		}
	}
	/**
	 * @Title: comboxGetAllUnit 
	 * @Description: todo(二级联动查询转换单位)   
	 * @author renling 
	 * @date 2013-6-1 上午11:52:36 
	 * @throws
	 */
 	public	function comboxGetAllUnit(){
		//得到部门ID
		$prodid= $this->escapeStr($_POST['prodid']);
		$baseunitid = $this->escapeStr($_POST['baseunitid']);
		//$condition['prodid'] = $prodid;
		$condition['_string']=" prodid=".$prodid." or prodid=0";
		$condition['baseunitid'] = $baseunitid;
		$modle = D('MisProductUnitexchange');
		$salesEXchange = $modle->where($condition)->select();
		$untname=getFieldBy($baseunitid,'id','name','MisProductUnit');

		$MisProductUnitModel=M("mis_product_unit");
		$unitlist=$MisProductUnitModel->where('id='.$baseunitid)->field('code,name')->find();

		$arr=array(array('0',"[".$unitlist['code']."]".$unitlist['name'],"1"));
		foreach($salesEXchange as $k=>$v){
			$unitlisttwo=$MisProductUnitModel->where('id = '.$v['subunitid'])->field('code,name')->find();
			$arr2=array();
			$arr2[]=$v['id'];
			$arr2[]="[".$unitlisttwo['code']."]".$unitlisttwo['name'];
			$arr2[]=$v['exchange'];
			array_push($arr,$arr2);
		}
		echo json_encode($arr);
	}
}
?>