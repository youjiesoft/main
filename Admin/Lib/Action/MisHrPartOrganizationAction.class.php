<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(党员信息) 
 * @author chenxinjun 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-3-27 上午11:22:38 
 * @version V1.0
 */
class MisHrPartOrganizationAction extends CommonAction {
	/**
	 * @Title: _filter
	 * @Description: todo(构造检索条件)
	 * @param HASHMAP $map
	 * @author 杨东
	 * @date 2013-5-31 下午4:01:22
	 * @throws
	 */
	public function _filter(&$map){
		if ($_SESSION["a"] != 1)$map['status']=array("gt",-1);
		/* $searchby = $_POST["searchby"];
		$keyword=$_POST["keyword"];
		$searchtype = $_POST['searchtype'];
		$this->assign("ifhidden", 0);
		if($searchby=='apply_time'){
			$this->assign("ifdatehidden", 0);
			$this->assign("ifhidden", 1);
			$datestart = $_POST["datestart"];
			$dateend = $_POST["dateend"];
			$map1 = $map2 = array ();
			if($datestart || $dateend){
				if ($datestart) $map1 = array (array ("egt",strtotime($datestart)));
				if ($dateend) $map2 = array (array ("elt",strtotime($dateend)));
				$map[$searchby] = array_merge($map1, $map2);
			}$this->assign("datestart", $datestart);$this->assign("dateend", $dateend);
		}
		if($_POST["keyword"]){
			$this->assign("ifdatehidden", 1);
			$this->assign("ifhidden", 0);
			$map[$searchby] = ($searchtype==2)  ? array('like','%'.$keyword.'%'):$keyword;
			$this->assign('keyword',$keyword);
			$this->assign('searchby',$searchby);
			$this->assign('searchtype',$searchtype);
		}
		$searchtype=array(array("id" =>"2","val"=>"模糊查找"),array("id" =>"1","val"=>"精确查找"));
		$this->assign("searchtypelist",$searchtype); */
	}
	/**
	 * @Title: _before_edit
	 * @Description: todo(打开修改页面前置函数)
	 * @author 杨东
	 * @date 2013-5-31 下午4:11:26
	 * @throws
	 */
	public function _before_edit(){
		$model=M("mis_hr_personnel_person_info");
		$list =$model->field("id,name")->select();
		$this->assign("employe_idlist",$list);
	}
	/**
	 * @Title: _before_add
	 * @Description: todo(打开新增前置函数)
	 * @author 杨东
	 * @date 2013-6-1 下午3:37:38
	 * @throws
	 */
	public function _before_add(){
		$model=M("mis_hr_personnel_person_info");
		$list =$model->field("id,name")->select();
		$this->assign("employe_idlist",$list);
	}
}
?>