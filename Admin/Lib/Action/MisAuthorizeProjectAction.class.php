<?php
/**
 * @Title: MisAuthorizeProjectAction
 * @Package 系统配置-项目特殊权限：功能类
 * @Description: TODO(项目特殊权限的记录及维护)
 * @author liminggang
 * @company 重庆特米洛科技有限公司˾
 * @copyright 重庆特米洛科技有限公司˾
 * @date 2013-1-10 19:18:54
 * @version V1.0
 */
class MisAuthorizeProjectAction extends CommonAction
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
		$userid=$_GET['userid'];
		$stepType=$_GET['stepType'];
	    //请注意，这里的step只从整体权限控制处传过来的，方便页面刷新问题
		$step=$_GET['step'];
		if($step == 1){
			$rel="rel/abc";
		}
		$this->assign('userid',$userid);
		$this->assign('stepType',$stepType);
		$this->assign('rel',$rel);
	}
	
	public function insert(){
		//获得对象ID数组
		$personid = $_POST['objectid'];
		$MisAuthorizeProjectDao = M("mis_authorize_project");
		//根据获得的对象ID查询对象是否有存在数据
		if($personid){
			//循环取得对象
			foreach($personid as $key=>$val){
				$map['status'] = 1;
				$map['objectid'] = $val;
				$list=$MisAuthorizeProjectDao->where($map)->find();
				if($list){
					//如果数据库中存在相同对象，则只修改对象的查看项目权限。
					$data=array(
						'id'=>$list['id'],
						'projectAuthorize'=>$_POST['projectAuthorize'],
						'remark' =>$_POST['remark'],
					);
					$MisAuthorizeProjectDao->data($data);
					$result=$MisAuthorizeProjectDao->save();
					if(!$result){
						$this->error("修改失败");
					}
				}else{
					//不存在相同对象。则进行插入操作
					unset($data);
					$data=array(
							'type'=>$_POST['type'],
							'objectid'=>$val,
							'objectname'=>$_POST['objectname'][$key],
							'projectAuthorize'=>$_POST['projectAuthorize'],
							'remark' =>$_POST['remark'],
							'modelname'=>'MisSalesProject',
							'tablename'=>'mis_sales_project',
							'createid'=>$_SESSION[C('USER_AUTH_KEY')],
							'createtime'=>time(),
					);
					$MisAuthorizeProjectDao->data($data);
					$result=$MisAuthorizeProjectDao->add();
					if(!$result){
						$this->error("新增失败！");
					}
				}				
			}
		}
		$this->success("新增成功");
	}

	/**
	 * @Title: _before_edit
	 * @Description: todo(编辑之前获得一些特殊数据)
	 * @author laicaixia
	 * @date 2013-5-31 下午6:15:02
	 * @throws
	 */
	public function _before_edit(){
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
	 * @Title: lookupmanage
	 * @Description: todo(用ztree形式查询出所有部门员工信息)
	 * @author jiangx
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
		$param['rel']="misauthorizeprojectBox";
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
	
	public function lookupGetAuthorizeProject(){
		$userid=$_GET['userid'];
		$this->assign('userid',$userid);
		if($_GET['jump']){
			$MisAuthorizeProjectDAO = M("mis_authorize_project");
			$map['type'] = 1;
			$map['status'] = 1;
			//获取到授权组的数据
			$list=$MisAuthorizeProjectDAO->where($map)->select();
			
			//根据授权组的ID获取到当前选中的人是否在授权组中
			$RolegroupUserDao=M("rolegroup_user");
			unset($map);
			$arr=array();
			 foreach($list as $key=>$val){
					$arr[]= $val['objectid'];
			 }
			$map['rolegroup_id'] = array(' in ',$arr);
			$map['user_id'] = $userid;
			//存在选中用户的项目授权组权限
			$rulist=$RolegroupUserDao->where($map)->select();
			$volist = array();
			if($rulist){
				foreach($rulist as $k=>$v){
					foreach($list as $k1=>$v1){
						if($v1['objectid'] == $v['rolegroup_id']){
							$volist[] = $v1;
						}
					}
				}
			}
			$this->assign('volist',$volist);
			$this->display('lookupGetAuthorize_role');
		}else{
			$this->display();
		}
	}
	public function lookupGetAuthorize_User(){
		$userid=$_GET['userid'];
		$this->assign('userid',$userid);
		$MisAuthorizeProjectDAO = M("mis_authorize_project");
		$map['type'] = 0;
		$map['status'] = 1;
		$map['objectid'] = $userid;
		$list=$MisAuthorizeProjectDAO->where($map)->select();
		$this->assign('list',$list);
		$this->display();
	}
}
?>