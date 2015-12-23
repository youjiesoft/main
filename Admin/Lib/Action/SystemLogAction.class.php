<?php
/**
 * @Title: SystemLogAction 
 * @Package package_name
 * @Description: todo(系统日志) 
 * @author laicaixia
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-6-2 上午10:45:13 
 * @version V1.0 
*/ 
class SystemLogAction extends CommonAction {
	/**
	 * @Title: _filter 
	 * @Description: todo(过滤函数) 
	 * @param 检索条件 $map  
	 * @author laicaixia 
	 * @date 2013-6-2 上午10:46:32 
	 * @throws 
	*/  
	public function _filter(&$map){
		if ($_SESSION["a"] != 1)$map['status']=array("gt",-1);
		/* $searchby = $_POST["searchby"];
		$keyword=$_POST["keyword"];//获取关键字
		$searchtype = $_POST['searchtype'];//2模糊查找；1精确查找
		$this->assign('searchby',$searchby);
		$this->assign("ifhidden", 0);
		if($searchby=='create_time'){
			$this->assign("ifdatehidden", 0);
			$this->assign("ifhidden", 1);
			$datestart = $_POST['datestart'];
			$dateend = $_POST['dateend'];
			if ($datestart && !$dateend) {
				$map['create_time'] = array ('egt',strtotime($datestart));
				$this->assign('datestart', $datestart);
			} else if (!$datestart && $dateend){
				$map['create_time'] = array ('elt',strtotime($dateend));
				$this->assign('dateend', $dateend);
			} else if ($datestart && $dateend){
				$map['create_time'] = array(array ('egt',strtotime($datestart)), array ('elt',strtotime($dateend)));
				$this->assign('datestart', $datestart);
				$this->assign('dateend', $dateend);
			}
		}else  if($searchby=='logtype'){
			$this->assign("ifdatehidden", 1);
			$this->assign("ifhidden", 0);
			if($_POST["keyword"]!=""){
				$map[$searchby] = ($searchtype==2)  ? array('like','%'.$keyword.'%'):$keyword;
				$this->assign('keyword',$keyword);
				$this->assign('searchtype',$searchtype);
			}
		}
		$searchtype=array(array("id" =>"2","val"=>"模糊查找"),array("id" =>"1","val"=>"精确查找"));
		$this->assign("searchtypelist",$searchtype);
		 */
	}
	/* (non-PHPdoc)列表
	 * @see CommonAction::index()
	 */
	public function index(){
		$map=$this->_search();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		if ($_SESSION["a"] != 1) {
			$map['mis_system_log.status']=array('gt',-1);
		}
		/*
		 * 所有表ztree
		*/
		$model = D('SystemLog');
		// 		$typelist = $model->where("status=1")->order("id asc")->select();
		$namelist = $model->where("status=1")->field('name')->select();
		$treemiso[]=array(
				'id'=>0,
				'pId'=>-1,
				'name'=>'所有表名',
				'rel'=>'jbsxBoxLog',
				'target'=>'ajax',
				'url'=>'__URL__/index/frame/1',
				'open'=>true
		);
		$param['url']="__URL__/index/frame/1/name/#name#";
		$param['rel']="jbsxBoxLog";
		$typeTree = $this->getTree($namelist,$param,$treemiso);
		$this->assign('typeTree',$typeTree);
		//组合递归查询。
		$name=$_REQUEST['name'];
		if($name){
			$$names =array_unique(array_filter (explode(",",$this->findAlldept($typelist,$name))));
			$map['mis_system_log.name'] =array("in",$names);
			$this->assign('name',$_REQUEST['name']);
		}
		$name = 'SystemLog';
		if (! empty ( $name )) {
			$this->_list ( $name, $map );
		}
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($name);
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		$searchby = $scdmodel->getDetail($name,true,'searchby');
		if ($searchby && $detailList) {
			$searchbylist=array();
			foreach( $detailList as $k=>$v ){
				if(isset($searchby[$v['name']])){
					$arr['id']= $searchby[$v['name']]['field'];
					$arr['val']= $v['showname'];
					array_push($searchbylist,$arr);
				}
			}
			$this->assign("searchbylist",$searchbylist);
		}
		if( intval($_POST['dwzloadhtml']) ){
			$this->display("dwzloadindex");exit;
		}    //瀑布流控制跳转
		if($_REQUEST['frame']==1){
			$this->display("index_notcat");
		}else{
			$this->display();
		}
	}
	/**
	 * @Title: _after_list 
	 * @Description: todo(列表后置函数) 
	 * @param 列表 $volist  
	 * @author laicaixia 
	 * @date 2013-6-2 上午10:45:43 
	 * @throws 
	*/  
	public function _after_list(&$volist){
		foreach ($volist as $key=>$val){
			if($val['logtype']=='1'){
				$volist[$key]['logtype']='更新';
			}else if($val['logtype']=='2'){
				$volist[$key]['logtype']='删除';
			}else if($val['logtype']=='0'){
				$volist[$key]['logtype']='异常';
			}
		}
	}

}
?>