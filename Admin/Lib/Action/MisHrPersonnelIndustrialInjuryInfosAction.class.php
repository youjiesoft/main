<?php
/**
 * @Title: MisHrPersonnelIndustrialInjuryInfosAction 
 * @Package package_name 
 * @Description: todo(记录人事工伤信息) 
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-1-7 上午8:45:12 
 * @version V1.0
 */
class MisHrPersonnelIndustrialInjuryInfosAction extends CommonAction {
	/** @Title: _filter
	 * @Description: (构造检索条件) 
	 * @author  
	 * @date 2013-5-31 下午3:59:44 
	 * @throws 
	*/
	public function _filter(&$map){
		if ($_SESSION["a"] != 1)$map['status']=array("gt",-1);
		/* $searchby = $_POST["searchby"];
		$keyword=$this->escapeChar($_POST["keyword"]);
		$searchtype = $_POST['searchtype'];
		$this->assign("ifhidden", 0);
		if($searchby=='accidentdate'){
			$this->assign("ifdatehidden", 0);
			$this->assign("ifhidden", 1);
			$datestart = $_POST["datestart"];
			$dateend = $_POST["dateend"];
			$map1 = $map2 = array ();
			if ($datestart) $map1 = array (array ("egt",strtotime($datestart)));
			if ($dateend) $map2 = array (array ("elt",strtotime($dateend)));
			$map[$searchby] = array_merge($map1, $map2);$this->assign("datestart", $datestart);$this->assign("dateend", $dateend);
		}
		if($_POST["keyword"]){
			$this->assign("ifdatehidden", 1);
			$this->assign("ifhidden", 0);
				$map[$searchby] = ($searchtype==2)  ? array('like','%'.$keyword.'%'):$keyword;
		}
		$this->assign('keyword',$keyword);
		$this->assign('searchby',$searchby);
		$this->assign('searchtype',$searchtype);
		$searchtype=array(array("id" =>"2","val"=>"模糊查找"),
						array("id" =>"1","val"=>"精确查找"));
		$this->assign("searchtypelist",$searchtype); */
	}
	/**
	 * @Title: _before_add
	 * @Description: todo(打开页面前置函数)
	 * @author liminggang
	 * @throws
	*/
	public function _before_add(){
		$scnmodel = D('SystemConfigNumber');
		//自动生成单据编号
		$etverno = $scnmodel->GetRulesNO('mis_hr_personnel_industrial_injury_info');
		$this->assign("orderno", $etverno);
		//订单号可写
		$writable= $scnmodel->GetWritable('mis_hr_personnel_industrial_injury_info');
		$this->assign("writable",$writable);
		$this->assign('nowtime',time());
		$this->assign('userid',$_SESSION[C('USER_AUTH_KEY')]);
	}
	/**
	 * @Title: _before_insert 
	 * @Description: todo(插入前置函数)   
	 * @author  
	 * @date 2013-5-31 下午4:08:58 
	 * @throws 
	*/  
	public function _before_insert(){
		//插入之前，验证编码是否重复。如果重复自动更新一个编码
		$this->checkifexistcodeororder('mis_hr_personnel_industrial_injury_info','orderno');
	}
	/**
	 * @Title: _before_update 
	 * @Description: todo(修改保存前置函数)   
	 * @author  
	 * @date 2013-5-31 下午4:08:58 
	 * @throws 
	*/ 
	public function _before_update(){
		//修改之前，验证编码是否重复。如果重复自动更新一个编码
		$this->checkifexistcodeororder("mis_hr_personnel_industrial_injury_info",'orderno',1);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CommonAction::lookupmanage()
	 */
	public function lookupmanage(){
		$step=$_REQUEST['step'];
		$this->assign("step",$step);
		$model= M('mis_system_department');
		$deptlist = $model->where("status=1")->order("id asc")->select();
		$param['rel']="positiveBox";
		$param['url']="__URL__/lookupmanage/jump/1/deptid/#id#/";
		$param['open']=true;
		$typeTree = $this->getTree($deptlist,$param);
		$this->assign('tree',$typeTree);
		$map = array();
		$searchby = str_replace("-", ".", $_POST["searchby"]);
		$keyword=$this->escapeChar($_POST["keyword"]);
		if($_POST["keyword"]){
			$step=$_POST['step'];
			$this->assign("step",$step);
			if($searchby =="all"){
				$where['mis_hr_personnel_person_info.name']=array('like','%'.$keyword.'%');
				$where['mis_system_department.name']=array('like','%'.$keyword.'%');
				$where['mis_hr_personnel_person_info.dutyname']=array('like','%'.$keyword.'%');
				$where['duty.name']=array('like','%'.$keyword.'%');
				$where['_logic']='OR';
				$map['_complex'] = $where;
			}else{
				$map[$searchby] = array('like','%'.$keyword.'%');
			}
			$searchby = str_replace(".", "-", $_POST["searchby"]);
			$this->assign('keyword',$keyword);
			$this->assign('searchby',$searchby);
		}
		$map['workstatus']=1;    //在职
		//$map['istransfer'] = 1;  //可调动。
		$deptid		= $_REQUEST['deptid'];
		if ($deptid ) {
			$map['mis_hr_personnel_person_info.deptid'] = $deptid;
		}
		$this->_list('MisHrBasicEmployeeView',$map);
		$this->assign('deptid',$deptid);
		if ($_REQUEST['jump']) {
			$this->display('lookupmanagelist');
		} else {
			$this->display("lookupmanage");
		}
	}
}
?>