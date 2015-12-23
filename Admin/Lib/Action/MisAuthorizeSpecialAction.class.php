<?php
/**
 * @Title: MisAuthorizeSpecialAction
 * @Package 系统配置-特殊权限：功能类
 * @Description: TODO(特殊权限的记录及维护)
 * @author yangxi
 * @company 重庆特米洛科技有限公司˾
 * @copyright 重庆特米洛科技有限公司˾
 * @date 2013-1-10 19:18:54
 * @version V1.0
 */
class MisAuthorizeSpecialAction extends CommonAction
{
	/**
	 * @Title: _filter
	 * @Description: todo(重写CommonAction的_filter方法，传递过滤参数后返回列表页面)
	 * @return string
	 * @author yangxi
	 * @date 2013-5-31 下午3:59:44
	 * @throws
	 */	
	public function _filter(&$map) {
		 if ($_SESSION["a"] != 1){
		 	$map['status']=array("gt",-1);
		 }
	}
	/**
	 * @Title: _before_add
	 * @Description: todo(进入新增)
	 * @author laicaixia
	 * @date 2013-5-31 下午6:15:02
	 * @throws
	 */
	public function _before_add(){
		//判断是否为项目权限
		$category=$_GET['category'];
		$this->assign('category',$category);
		
	    //请注意，这里的step只从整体权限控制处传过来的，方便页面刷新问题
		$step=$_GET['step'];
		if($step){
			$rel="rel/abc";
		}else{
			$rel="navTabId/__MODULE__";
		}
		$this->assign('rel',$rel);
	}
	
	/**
	 * @Title: _before_insert
	 * @Description: todo(插入数据前操作)
	 * @author jiangx
	 * @date 2013-7-27 
	 * @throws
	 */
	public function _before_insert(){
		if ($_POST['tablename']) {
			$_POST['modelname'] = parse_name($_POST['tablename'], 1);
			if($_POST['methodname']){
				if (! method_exists(A($_POST['modelname']),$_POST['methodname'])) {
					$this->error('录入方法名错误，请检查修改');
				}
			}
			if ($_POST['type'] == 1) {
				$_POST['objectid'] = implode(',', $_POST['rolegroupid']);
			}
			if ($_POST['type'] == 0) {
				$_POST['objectid'] = implode(',', $_POST['personid']);
			}
		}else{
			$this->error('录入数据错误，请检查修改');
		}
	}
	public function _before_update(){
		if ($_POST['type'] == 1) {
			$_POST['objectid'] = implode(',', $_POST['rolegroupid']);
		}
		if ($_POST['type'] == 0) {
			$_POST['objectid'] = implode(',', $_POST['personid']);
		}
	}
	/**
	 * @Title: _before_edit
	 * @Description: todo(编辑之前获得一些特殊数据)
	 * @author laicaixia
	 * @date 2013-5-31 下午6:15:02
	 * @throws
	 */
	public function _before_edit(){
		//判断是否为项目权限
		$category=$_GET['category'];
		$this->assign('category',$category);
		//请注意，这里的step只从整体权限控制处传过来的，方便页面刷新问题
		$step=$_GET['step'];
		if($step){
			$rel="rel/abc";
		}else{
			$rel="navTabId/__MODULE__";
		}
		$this->assign('rel',$rel);
	}
	/**
	 * @Title: edit
	 * @Description: todo(查看数据)
	 * @author jiangx
	 * @date 2013-7-27 
	 * @throws
	 */
	public function edit() {
		$name=$this->getActionName();
		$model = D ( $name );
		$id = $_REQUEST [$model->getPk ()];
		$map['id']=$id;
		$vo = $model->where($map)->find();
		$scdmodel = D('SystemConfigDetail');
		$modelname = $this->getActionName();
		$detailList = $scdmodel->getDetail($modelname,false);
		if ($detailList) {
			$fieldsarr = array();
			$sclmodel = D('SystemConfigList');
			foreach ($detailList as $k => $v) {
				$showname = '';
				if($v['status'] != -1){
					$showMethods = "";
					if($v['methods']){
						$methods = explode(';', $v['methods']);// 分解所有方法
						$normArray = $sclmodel->getNormArray();// 中文解析
						$showMethods .= "<span class='xyminitooltip'><span class='xyminitooltip_con'>";
						//$showname .= "<span class='xyminitooltip'><span class='xyminitooltip_con'>";
						$isfalse = false;
						foreach ($methods as $key => $vol) {
							if ($isfalse) {
								//$showname .= " | ";
								$showMethods .= " | ";
							}
							$volarr = explode(',', $vol);// 分解target和方法
							$target = $volarr[0];// 弹出方式
							$method = $volarr[1];// 方法名称
							$modelarr = explode('/', $method);// 分解方法0：model；1：方法
							if ($_SESSION[strtolower($modelarr[0].'_'.$modelarr[1])] || $_SESSION["a"]) {
								$showMethods .= "<a rel='".$modelarr[0].$modelarr[1]."' target='".$target."' href='__APP__/".$method."' mask='true'>".$normArray[$modelarr[1]]."</a>";
								$isfalse = true;
							}
							//$showname .= "<a rel='".$modelarr[0].$modelarr[1]."' target='".$target."' href='__APP__/".$method."' mask='true'>".$normArray[$modelarr[1]]."</a>";
							$isfalse = true;
						}
						if($showMethods){
							$showMethods .= "<span class='xyminitooltip_arrow_outer'></span><span class='xyminitooltip_arrow'></span></span></span>";
						}
					}
					if($showMethods){
						$showname .= $showMethods;
					}
					if ($v['models']) {
						if ($_SESSION[strtolower($v['models'].'_index')] || $_SESSION["a"]) {
							$showname .= "<a rel='".$v['models']."' target='navTab' href='__APP__/".$v['models']."/index'>".$v['showname']."</a>";
						} else {
							$showname .= $v['showname'];
						}
					} else{
						$showname .= $v['showname'];
					}
				}
				$fieldsarr[$v['name']] = $showname;
			}
			$this->assign ( 'fields', $fieldsarr );
		}
		$module=A($name);
		if (method_exists($module,"_after_edit")) {
			call_user_func(array(&$module,"_after_edit"),&$vo);
		}
		$this->assign( 'vo', $vo );
		$this->display ();
	}
	/**
	 * @Title: _after_edit
	 * @Description: todo(查看数据后操作)
	 * @author jiangx
	 * @date 2013-7-27 
	 * @throws
	 */
	public function _after_edit($vo){
		if ($vo['type'] == 1) {
			$rolegroupModel = D("Rolegroup");
			$map = array();
			$map['id'] = array('in', $vo['objectid']);
			$rolegrouplist = $rolegroupModel->where($map)->getField('id, name');
			$this->assign('rolegrouplist', $rolegrouplist);
		}
		if ($vo['type'] == 0) {
			$userModel = D("User");
			$map = array();
			$map['id'] = array('in', $vo['objectid']);
			$userlist = $userModel->where($map)->getField('id, name');
			$this->assign('userlist', $userlist);
		}
	}
	
	/**
	 *
	 * @Title: lookupmanage
	 * @Description: todo(用ztree形式查询出所有部门员工信息。)
	 * @author liminggang
	 * @throws
	 */
	public function lookupmanage(){
		$this->assign('ulId', $_REQUEST['ulId']);
		$userid=$_SESSION[C('USER_AUTH_KEY')];
		$viewModel=D('MisHrPersonnelUserDeptView');
		$companyList=$viewModel->where('user.id='.$userid)->field('companyid')->select();
		foreach ($companyList as $comk=>$comv){
			$companyArr[]=$comv['companyid'];
		}
		//构造左侧部门树结构
		$model= M('mis_system_department');
		$departmentmap['status']=1;
		if(!empty($companyList) && $userid!=1){
			$departmentmap['companyid']=array('in',$companyArr);
		}
		$deptlist = $model->where($departmentmap)->order("id asc")->select();
		$param['rel']="positiveBox";
		$param['url']="__URL__/lookupmanage/jump/1/deptid/#id#/parentid/#parentid#/companyid/#companyid#/ulId/".$_REQUEST['ulId'];
		$typeTree = $this->getTree($deptlist,$param);
		$this->assign('tree',$typeTree);
		$map = array();
		$keyword	= $_POST['keyword'];
		$searchby = str_replace("-", ".", $_POST["searchby"]);
		if ($keyword) {
			if($searchby =="all"){
				$MisSystemDepartmentModel=D("MisSystemDepartment");
				$deptMap=array();
				$deptMap['name']=array('like','%'.$keyword.'%');
				$deptMap['status']=1;
				$MisSystemDepartmentList=$MisSystemDepartmentModel->where($deptMap)->getField("id,name");
				if($MisSystemDepartmentList){
				$where['deptid']=array("in",array_keys($MisSystemDepartmentList));
				}
				$MisSystemDutyModel=D("MisSystemDuty");
				$dutyMap=array();
				$dutyMap['name']=array('like','%'.$keyword.'%');
				$dutyMap['status']=1;
				$MisSystemDutyList=$MisSystemDutyModel->where($dutyMap)->getField("id,name");
				if($MisSystemDutyList){
				$where['dutyid']=array("in",array_keys($MisSystemDutyList));
				}
				$where['user.name']=array('like','%'.$keyword.'%');
				$where['_logic']='OR';
				$map['_complex'] = $where;
			}else{
				if($searchby=="mis_system_department.name"){
					$MisSystemDepartmentModel=D("MisSystemDepartment");
					$deptMap=array();
					$deptMap['name']=array('like','%'.$keyword.'%');
					$deptMap['status']=1;
					$MisSystemDepartmentList=$MisSystemDepartmentModel->where($deptMap)->getField("id,name");
					$map['deptid']=array("in",array_keys($MisSystemDepartmentList));
				}else if($searchby=="duty.name"){
					$MisSystemDutyModel=D("MisSystemDuty");
					$dutyMap=array();
					$dutyMap['name']=array('like','%'.$keyword.'%');
					$dutyMap['status']=1;
					$MisSystemDutyList=$MisSystemDutyModel->where($dutyMap)->getField("id,name");
					$map['dutyid']=array("in",array_keys($MisSystemDutyList));
				}else{
					$map[$searchby]=array('like','%'.$keyword.'%');
				}
			}
			$searchby = str_replace(".", "-", $_POST["searchby"]);
			if($searchby=='name'){
				$placeholder="搜索姓名";
			}
			if($searchby=='mis_system_department-name'){
				$placeholder="搜索部门";
			}
			if($searchby=='duty-name'){
				$placeholder="搜索职级";
			}
			if($searchby=='all'){
				$placeholder="搜索员工姓名,部门,职级";
			}
			$this->assign('placeholder', $placeholder);
			$this->assign('searchby', $searchby);
			$this->assign('keyword', $keyword);
		}
		$map['working']=1;	//在职状态
		$map['user.status']=1;	//后台用户正常状态
		$map['_string'] = "user.id<>'' or user.id<>0";
		$map['user_dept_duty.status']=1;
		if($_REQUEST['companyid']!=null){
			$map['companyid']=$_REQUEST['companyid'];
		}else{
			$map['companyid']=$deptlist[0]['companyid'];
		}
		$deptid		= $_REQUEST['deptid'];	//获得部门节点
		$parentid	= $_REQUEST['parentid'];	//获得父类节点
		$companyid	= $_REQUEST['companyid'];
		if ($deptid && $parentid) {
			$deptlist =array_unique(array_filter (explode(",",$this->downAllChildren($deptlist,$deptid))));
			$map['deptid'] = array(' in ',$deptlist);
		}
		$multi = intval($_REQUEST['multi']) ? 1:0;
		$this->_list('MisHrPersonnelUserDeptView',$map,'id',true);
		$this->assign('deptid',$deptid);
		$this->assign('parentid',$parentid);
		$this->assign('companyid',$companyid);
		$this->assign('multi',$multi);
		if ($_REQUEST['jump']) {
			$this->display('lookupmanagelist');
		} else {
			$this->display("lookupmanage");
		}
	}
	/**
	 * @Title: lookuprolegroup
	 * @Description: todo(获取所有组权限)
	 * @author jiangx
	 * @throws
	 */
	public function lookuprolegroup(){
		$this->assign('ulId', $_REQUEST['ulId']);
		$map = array();
		$searchby = $_POST["searchby"];
		$keyword=$this->escapeChar($_POST["keyword"]);
		$searchtype = $_POST['searchtype'];
		if($_POST["keyword"]){
			$map[$searchby] = ($searchtype==2)  ? array('like','%'.$keyword.'%'):$keyword;
			$this->assign('keyword',$keyword);
			$this->assign('searchby',$searchby);
			$this->assign('searchtype',$searchtype);
		}
		$searchby=array(
			array("id" =>"name","val"=>"组权限名称"),
			array("id" =>"id","val"=>"组权限id"),
		);
		$searchtype=array(array("id" =>"2","val"=>"模糊查找"),
			array("id" =>"1","val"=>"精确查找"));
		$this->assign("searchbylist",$searchby);
		$this->assign("searchtypelist",$searchtype);
		$this->_list ( 'Rolegroup', $map );
		$this->display();
	}
	
}
?>