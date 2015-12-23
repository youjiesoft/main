<?php
/**
 * @Title: CrmCustEmployeeAction 
 * @Package package_name
 * @Description: todo(组织结构) 
 * @author laicaixia
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-5-31 下午4:16:29 
 * @version V1.0
 */
class CrmCustEmployeeAction extends CommonAction{
	
    /**
     * @Title: _filter 
     * @Description: todo(检索) 
     * @param unknown_type $map  
     * @author laicaixia 
     * @date 2013-5-31 下午4:14:29 
     * @throws 
    */  
    public function _filter(&$map){
        if ($_SESSION["a"] != 1)$map['status']=array("gt",-1);
    }
    /**
     * @Title: manager 
     * @Description: todo(暂时没有用)   
     * @author laicaixia 
     * @date 2013-5-31 下午4:14:57 
     * @throws 
    */  
    private function manager() {
		$model=D('CrmCustEmployee');
		$cid=$_GET['id'];
        $list=$model->where("customerid='".$cid."'")->select();
        $this->assign("list",$list);
        $this->display();
    }
     /**
      * @Title: _before_add 
      * @Description: todo(进入新增)   
      * @author laicaixia 
      * @date 2013-5-31 下午4:17:39 
      * @throws 
     */  
    public function _before_add(){
     	// 查询客户
     	$customerid = $_GET['customerid'];
     	if($customerid){
     		$model = D('MisSalesCustomer');
     		$map['status'] = 1;
     		$map['id'] = $customerid;
     		$cvo = $model->where($map)->find();
     		$this->assign('customer',$cvo);
     	}
		$this->assign('customerid',$customerid);
    }
	/**
	 * @Title: _before_update 
     * @Description: todo(进入首页)   
     * @author laicaixia 
     * @date 2013-5-31 下午4:17:51 
     * @throws 
    */  
	public function index(){
		//配置检索信息
		$map = $this->_search();
		//获取选中客户的联系人
		$custmerid = $_REQUEST['custmerid'];
		$this->assign('custmerid',$custmerid);
		if ($custmerid) {
			$map['customerid'] = $custmerid;
		}
		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name = 'CrmCustEmployee';
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($name);
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		//扩展工具栏操作
		if (! empty ( $name )) {
			$qx_name=$name;
			if(substr($name, -4)=="View"){
				$qx_name = substr($name,0, -4);
			}
			//验证浏览及权限
			if( !isset($_SESSION['a']) ){
				$map=D('User')->getAccessfilter($qx_name,$map);	
			}
			$this->_list ( $name, $map );
		}
		if ($_REQUEST['jump']) {
			$this->display('unitlist');
		} else {
			$this->assign('custmertree',$this->getTree());
			$this->display();
		}
	}
    /* (non-PHPdoc)
     * @see CommonAction::getTree()
     */
    public function getTree() {
		$model = D('MisSalesCustomer');
		$map['status'] = 1;
		$data = $model->where($map)->select();
		$zNodes[] = array(
			'id'=>0,
			'pId'=>-1,
			'name'=>'客户名称',
			'title'=>'客户名称',
			'open'=>true
		);
		foreach ($data as $k => $v) {
			$zNodes[] = array(
				'id'=>$v[id],
				'pId'=>0,
				'name'=>$v['enterprisename'],
				'title'=>$v['enterprisename'],
				'url'=>'__URL__/index/jump/1/custmerid/'.$v[id],
				'rel'=>'CrmCustEmployeeBox',
				'target'=>'ajax'
			);
		}
		return json_encode($zNodes);
	}
	/**
	 * @Title: _before_edit 
	 * @Description: todo(进入修改)   
	 * @author laicaixia 
	 * @date 2013-5-31 下午4:19:36 
	 * @throws 
	*/  
	public function _before_edit(){
	    $custmerid = $_GET['custmerid'];
		$this->assign('custmerid2',$custmerid);
    }
    /**
     * @Title: lookup 
     * @Description: todo(查找带回)   
     * @author laicaixia 
     * @date 2013-5-31 下午4:19:48 
     * @throws 
    */  
    public function lookup(){
    	if($_POST['name']) $map['name']=array("like","%".$_POST['name']."%");
    	if($_POST['typeid']) $map['typeid']=$_POST['typeid'];
    	$map['status']=1;
    	$name = "MisSalesCustomer";
    	if (! empty ( $name )) {
    		$this->_list ( $name, $map );
    	}
    	$model=D('MisSalesCustomertype');
    	$tlist=$model->where("status = 1 ")->select();
    	$this->assign("tlist",$tlist);
    	$this->assign("name",$_POST['name']);
    	$this->assign("typeid",$_POST['typeid']);
    	$this->display();
    }
}
?>