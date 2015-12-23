<?php
//Version 1.0
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Description of MisAppsAction
 *
 * @author mashihe
 */
class MisAppsModulesAction extends CommonAction{
    //put your code here
    public function _filter(&$map){
		 if ($_SESSION["a"] != 1)$map['status']=array("gt",-1);
    }
    public function _before_index(){
    }
    //天气模块
    public function weather(){
        $this->display();
    }
    //公司大事记模块
    public function companynews(){
		$model=D('SystemAnnouncement');
		$list=$model->where("status = 1 and  type='company'")->order('createtime')->limit(5)->select();
		$this->assign("list",$list);
        $this->display();
    }
    //公司大事记模块
    public function aa(){
        $this->display();
    }
    //待办模块
    public function todo(){
    	$list			= array();
    	$arr			= array();
    	$arr[]	= array("model" => "CrmSaleCostForm",		"name" => "销售费用");
    	$arr[]	= array("model" => "MisSalesQuotationmas",	"name" => "销售报价");
		$arr[]	= array("model" => "MisSalesOrdermas",		"name" => "销售订单");
		$arr[]	= array("model" => "MisPurchaseApplymas",	"name" => "采购申请");
		$arr[]	= array("model" => "MisPurchaseOrdermas",	"name" => "采购订单");
		$arr[]	= array("model" => "MisInventoryIntoMas",	"name" => "物料入库");
		$arr[]	= array("model" => "MisInventoryIntoFmas",	"name" => "财务入库");
		$arr[]	= array("model" => "MisInventoryOutMas",	"name" => "物料出库");
		$arr[]	= array("model" => "MisInventoryOutFmas",	"name" => "财务出库");
		$arr[]	= array("model" => "MisInventoryMoveMas",	"name" => "物料转移");
		$arr[]	= array("model" => "MisInventoryAdjustFmas","name" => "财务调整");
    	foreach( $arr as $k=>$v ){
    		$model = D($v['model']);
    		$map['_string'] = $tableName.'.ptmptid = process_relation.pid AND '.$tableName.'.ostatus = process_relation.tid AND FIND_IN_SET(  '.$_SESSION[C('USER_AUTH_KEY')].', process_relation.userid )';
			$tableName = $model->getTableName();
			$count = $model->table($tableName.' as '.$tableName.',process_relation as process_relation')->where($map)->count($tableName.'.id');
			if ($count) {
				$list[] = array(
	    			'name'	=> $v['name'],
	    			'count'	=> $count,
	    			'url'	=> '__APP__/'.$v['model'].'/index/ntdata/1'
	    		);
			}
    	}
		$this->assign('list',$list);
        $this->display();
    }
}
?>