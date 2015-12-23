<?php
/**
 * @Title: file_name
 * @Package package_name
 * @Description: todo(工作报告控制器)
 * @author liminggang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-7-2 下午5:02:46
 * @version V1.0
 */
class MisWorkStatementSetAction extends CommonAction {
	/**
	 * @Title: _filter
	 * @Description: todo(重写CommonAction的index方法，展示列表)
	 * @return string
	 * @author 杨希
	 * @date 2013-5-31 下午3:59:44
	 * @throws
	 */
	public function _filter(&$map){
		if ($_SESSION["a"] != 1){
			$map['status']=array("gt",-1);
		}
	}
	public function  _after_edit($vo){
		$leadid = explode(',',$vo['leadid']);
		$leadname = explode(',',$vo['leadname']);
		$staffid = explode(',',$vo['staffid']);
		$staffname = explode(',',$vo['staffname']);
		$this->assign('leadid',$leadid);
		$this->assign('leadname',$leadname);
		$this->assign('staffid',$staffid);
		$this->assign('staffname',$staffname);
	}
	/**
	 *
	 * @Title: lookupmanage
	 * @Description: todo(用ztree形式查询出所有部门员工信息。)
	 * @author liminggang
	 * @throws
	 */
	public function lookupmanage(){
		$step  = $_REQUEST['step'];
		$this->assign('step',$step);
		$model= M('mis_system_department');
		$deptlist = $model->where("status=1")->order("id asc")->select();
		$param['open'] = true;
		$param['rel']="misworkstatementlistBox";
		$param['url']="__URL__/lookupmanage/jump/1/deptid/#id#/parentid/#parentid#/step/".$step;
		$typeTree = $this->getTree($deptlist,$param);
		$this->assign('tree',$typeTree);
		$map = array();
		$searchby = str_replace("-", ".", $_POST["searchby"]);
		$keyword=$this->escapeChar($_POST["keyword"]);
		$searchtype = $_POST['searchtype'];
		if($_POST["keyword"]){
			$map[$searchby] = ($searchtype==2)  ? array('like','%'.$keyword.'%'):$keyword;
			$this->assign('keyword',$keyword);
			$searchby = str_replace(".", "-", $_POST["searchby"]);
			$this->assign('searchby',$searchby);
			$this->assign('searchtype',$searchtype);
		}
		$searchby=array(
				array("id" =>"user-name","val"=>"按员工姓名"),
				array("id" =>"orderno","val"=>"按员工编号"),
		);
		$searchtype=array(array("id" =>"2","val"=>"模糊查找"),
				array("id" =>"1","val"=>"精确查找"));
		$this->assign("searchbylist",$searchby);
		$this->assign("searchtypelist",$searchtype);
		$map['user.status']=1;
		$deptid		= $_REQUEST['deptid'];
		//$parentid	= $_REQUEST['parentid'];
		if ($deptid >1) {
			//$deptlist =array_unique(array_filter (explode(",",$this->findAlldept($deptlist,$deptid))));
			$map['user.dept_id'] = $deptid;
		}

		$this->_list('MisHrPersonnelPersonInfoView',$map);
		$this->assign('deptid',$deptid);
		$this->assign('parentid',$parentid);
		if ($_REQUEST['jump']) {
			$this->display('lookupmanagelist');
		} else {
			$this->display("lookupmanage");
		}
	}
}
?>