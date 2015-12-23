<?php
/**
 * @Title: MisSystemDepartmentAction
 * @Package package_name
 * @Description: todo(人事相关-部门管理)
 * @author xiafengqin
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-6-1 下午2:47:40
 * @version V1.0
 */
class MisSystemDepartmentAction extends CommonAction {
	/**
	 * @Title: __filter
	 * @Description: todo(构造检索条件)
	 * @param unknown_type $map
	 * @author xiafengqin
	 * @date 2013-6-1 下午2:51:40
	 * @throws
	 */
	public function _filter(&$map){
		if ($_SESSION["a"] != 1){
			$map['status']=array("gt",-1);
		}
	}
	/**
	 * @Title: index
	 * @Description: todo(首页列表显示)
	 * @author liminggang
	 * @date 2013-11-28 下午6:18:54
	 * @throws
	 */
	public function index(){
		if($_REQUEST['jump']){
			$rel="MisSystemDepartmentBox";
			$jump="/jump/1";
		}else{
			$rel="MisSystemDepartmentBox";
		}
// 		//获得部门集
		$MisSystemDepartmentModel=D('MisSystemDepartment');		
		$MisSystemCompanyDao = M("mis_system_company");
		$where = array();
		$where['status'] = 1;
		$where['iscompany']=1;
		$companylist=$MisSystemDepartmentModel->where($where)->order('orderno')->select();
		$this->assign("companylist",$companylist);
		foreach($companylist as $k=>$v){
			$companylist[$k]['name']="(".$v['orderno'].")".$v['name'];
		}
		$where['iscompany']=array('neq',1);
		$departmentlist=$MisSystemDepartmentModel->where($where)->order('orderno')->select(); 
		//dump($companylist);
		//构造结构树
		$newdepartmentlist = $departmentlist;
		foreach($newdepartmentlist as $k=>$v){
			$newdepartmentlist[$k]['name']="(".$v['orderno'].")".$v['name'];
		}
		$param['url']= __URL__."/index/jump/jump/hy/#id#";
		$param['rel']= $rel;
		$param['open']= "true";
		$param['isParent']="true";
		$arr = $this->getTree($newdepartmentlist,$param,$companylist);
		$this->assign("ztree",$arr);
		//获取部门ID
		$hy = $_REQUEST['hy'];
		//定义一个存储行业数据数组
		$vo = array();
		//扩展工具栏操作
		$model = D('SystemConfigDetail');
		$toolbarextension = $model->getDetail($this->getActionName(),true,'toolbar');
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		if($hy){
			$map['id'] = $hy;
			$vo=$MisSystemDepartmentModel->where($map)->find();
			$this->_before_edit();
			$this->assign("vo",$vo);
			//获取部门角色
			$relist=$MisSystemDepartmentModel->getDeptRoleGroup($hy);
			$this->assign("relist",$relist);
			//另赋予一个给配置文件toolbar调用
			$companyid = getFieldBy($hy, 'id', 'companyid', "mis_system_department");
			$_REQUEST['parentid']=$companyid;
			$detailList = $model->getDetail($this->getActionName());
			$this->assign('detaillist',$detailList);
			// 		
		}else{
			//判断是否有cookie默认
			$cookiecheck = cookie::get("departmentcheck");
			cookie::delete("departmentcheck");
			if($cookiecheck){//新增、修改、删除后进入新增页面
				$hy = $cookiecheck;
			}elseif($_REQUEST['add']){//点击按钮进入新增页面
				$hy = $_REQUEST['companyid'];
				$this->assign("companyid",$_REQUEST['companyid']);
			}else{
				if($departmentlist){//默认首次进入页面时为新增页面
						//判断是否存在公司
						//$vo = $departmentlist[0];
						$hy = $departmentlist[0]['id'];
				}				
			}
			$this->_before_add();		
		}
			$this->assign("valid",$hy);
		//查询公司列表
		$MisSystemCompanyModel=D("MisSystemCompany");
		$MisSystemCompanyList=$MisSystemCompanyModel->where("status=1")->order("orderno")->getField("id,name");
		$this->assign("MisSystemCompanyList",$MisSystemCompanyList);
		//传送公司id
		$this->assign("companyid",$_REQUEST['companyid']);
		if ($_REQUEST['jump']){
			$this->display("indexview");
		}else{
			$this->display();
		}
	}
	public function _before_update(){
// 		$name = $this->getActionName();
// 		$orderno = $_POST['orderno'];
// 		$MisSystemOrdernoDao = D("MisSystemOrderno");
// 		$data = $MisSystemOrdernoDao->validateOrderno($name,$orderno,$_POST['id'],$_POST['companyid']);
// 		if($data['result']){
// 			$_POST['parentid'] = $data['parentid'];
// 		}else{
// 			$this->error($data['altMsg']);
// 		}
		if(!$_POST['parentid']&&$_POST['parentid']==0){
			$_POST['parentid']=getFieldBy($_POST['companyid'], "companyid", "id", "MisSystemDepartment","iscompany",1);
		}
	}
	public function _before_insert(){
		$name = $this->getActionName();
		$orderno = $_POST['orderno'];
		$MisSystemOrdernoDao = D("MisSystemOrderno");
		$data = $MisSystemOrdernoDao->validateOrderno($name,$orderno,'',$_POST['companyid']);
		if($data['result']){
			$_POST['parentid'] = $data['parentid'];
		}else{
			$this->error($data['altMsg']);
		}
		if(!$_POST['parentid']&&$_POST['parentid']==0){
			$_POST['parentid']=getFieldBy($_POST['companyid'], "companyid", "id", "MisSystemDepartment","iscompany",1);
		}
		unset($_POST['id']);
	}
	public function _before_add(){
		//查询部门角色信息
		$orgsetModel=M("rolegroup");
		$where = array();
		$where['status'] = 1;
		$where['catgory'] = 5;
		$list=$orgsetModel->where($where)->select();
		$this->assign("orglist",$list);
		$this->assign("companyid",$_REQUEST['companyid']);
		
	}
	public function _before_edit(){
		$MisSystemDepartmentModel=D("MisSystemDepartment");
		//查询部门角色信息
		$orgsetModel=M("rolegroup");
		$where = array();
		$where['status'] = 1;
		$where['catgory'] = 5;
		$list=$orgsetModel->where($where)->select();
		$_REQUEST['deptid']=$_REQUEST['id'];
		//获取部门角色信息
		$this->assign("userid",$_SESSION[C('USER_AUTH_KEY')]);
		$this->assign("orglist",$list);
	}
	/**
	 * @Title: adddeptuser
	 * @Description: todo(增加部门管理信息)
	 * @param int $id  部门ID
	 * @param int $m  0表示修改，1表示插入
	 * @author libo
	 * @date 2014-4-29 下午3:01:09
	 * @throws
	 */
	public function adddeptuser($id,$m=0){
		//获得部门角色管理信息
		$orgsetModel=M("rolegroup");
		$where = array();
		$where['status'] = 1;
		$where['catgory'] = 5; //5代表部门角色
		$re=$orgsetModel->where($where)->select();
		//部门角色和用户绑定关系表
		$recodemodel = M ("mis_organizational_recode");
		if($m){//如是修改 则先删除 在添加
			//原始数据
			$oldArr=$recodemodel->where("deptid=".$id)->select();
			foreach ($oldArr as $k=>$v){
				$rolmode=D("Rolegroup");
				$boolean=$rolmode->DelGroupUsers($v['rolegroup_id'],$v['userid']);
				if(!$boolean){
					$this->error("用户权限修改失败，请联系管理员");
				}
			}
			$result=$recodemodel->where("deptid=".$id)->delete();
			if(!$result){
				$this->error("用户部门角色关系修改失败，请联系管理员");
			}
		}
		foreach ($re as $k=>$v){//循环类型
			if(is_array($_POST['recipient'.$v['id']])){ //判断是否选择了部门管理信息
				$data['deptid'] = $id;  //部门id
				$data['rolegroup_id'] = $v['id']; //角色ID
				$data['userid'] = implode(",", $_POST['recipient'.$v['id']]); //人员
				$res=$recodemodel->add($data);
				if ($res===false) {
					$this->error ( "部门角色人员添加失败,请联系管理员" );
					exit;
				}else{
					$rolmode=D("Rolegroup");
					$boolean=$rolmode->AddGroupUsers($v['id'],implode(",", $_POST['recipient'.$v['id']]));
					if(!$boolean){
						$this->error("用户权限添加失败，请联系管理员");
					}
				}
			}
		}
	}
	/**
	 *
	 * @Title: lookupsendmessage
	 * @Description: todo(组织结构发送短消息)
	 * @author renling
	 * @date 2014-9-4 上午10:35:52
	 * @throws
	 */
	public function lookupsendmessage(){
		//组织结构发送短消息
		if($_GET['userid']){
			$userid=$_GET['userid'];
			$this->assign('userid',$userid);
		}
		$this->display();
	}

	/**
	 * @Title: update
	 * @Description: todo(修改方法)
	 * @author xiafengqin
	 * @date 2013-6-1 下午3:01:58
	 * @throws
	 */
	public function update(){
		$name = $this->getActionName();
		$orderno = $_POST['orderno'];
// 		$MisSystemOrdernoDao = D("MisSystemOrderno");
// 		$data = $MisSystemOrdernoDao->validateOrderno($name,$orderno,$_POST['id'],$_POST['companyid']);
// 		if($data['result']){
// 			$_POST['parentid'] = $data['parentid'];
// 		}else{
// 			$this->error($data['altMsg']);
// 		}
		$misSystemDepartmentModel = D('MisSystemDepartment');
		//取得原父节点与现父节点值及当前操作节点
		if(!$_POST['parentid']&&$_POST['parentid']==0){
			$_POST['parentid']=getFieldBy($_POST['companyid'], "companyid", "id", "MisSystemDepartment","iscompany",1);
		}
// 		//部门旧上级ID
		$oldparentid=$_POST["oldparentid"];
		if($oldparentid!=$_POST['parentid']){
			//当前部门ID
			$id=$_REQUEST["id"];
			//查询当前部门是否有下级部门
			$isexpar=getFieldBy($id, "parentid", "id", "mis_system_department","status","1");
				if($isexpar){
					$this->error("当前部门存在下级部门  请勿移动");
				}
				//查询当前部门是否有岗位
			$isjobinfo=getFieldBy($id, "deptid", "id", "mis_hr_job_info","status","1");
				if($isjobinfo){
					$this->error("当前部门存在岗位  请勿移动");
				}
				
		}
// 		//自己不能与自己调换
// 		if($id==$parentid){
// 			$this->error ("上级部门不能是本身！");
// 		}
		//当存在父节点时验证
		// 		if($parentid){
		// // 			if($parentid==$oldparentid){
		// // 				$this->error ("原父节点与调换节点相同，请检查！");
		// // 			}
		// // 			if($oldparentid == 0 && $parentid){
		// // 				$this->error ("顶级节点不允许移动，请检查！");
		// // 			}
		// 		}
		// 		else{
		// 			//没选新上级时默认添加顶级节点
		// 			if($parentid==0){
		// 				$_POST["parentid"]= $oldparentid;
		// 			}else{
		// 				//父节不变时，将父节点值赋为旧父节点值
		// 				$_POST["parentid"]=$oldparentid;
		// 			}
		// 		}
		if (false === $data=$misSystemDepartmentModel->create ()) {
			$this->error ( $misSystemDepartmentModel->getError () );
		}
		// 更新数据
		$list=$misSystemDepartmentModel->save ();

		
		if (false !== $list) {
			$this->adddeptuser($_POST['id'],1);
			cookie::set("departmentcheck",$_POST['id']);
			//执行成功则对新父节点的父节点做更新
			//如果新父节点的父节点与当前操作节点相同则执行改变
			// 			$sign= $misSystemDepartmentModel->where("id=".$parentid)->getField("parentid");
			// 			if($sign==$id){
			// 				$maps['parentid']=$oldparentid;
			// 				$misSystemDepartmentModel->where("id=".$parentid)->save($maps);
			// 			}
			//	    print_r($model->getLastSql());
			//		exit;
			//成功提示，并提交事务
			$this->success ( L('_SUCCESS_'));
		} else {
			$this->error ( L('_ERROR_') );
		}
	}
	/**
	 * @Title: _before_delete
	 * @Description: todo(删除前置函数)
	 * @author xiafengqin
	 * @date 2013-6-1 下午3:02:13
	 * @throws
	 */
	public function _before_delete(){
		$id=$_REQUEST['id'];
		$name=$this->getActionName();
		$model=D($name);
		//判断父节点，如果有，删除后树节点默认高亮
		$parentid = $model->where('id='.$id)->getField('parentid');
		cookie::set("departmentcheck",$parentid);
		$isjobinfo=getFieldBy($id, "deptid", "id", "mis_hr_job_info","status","1");
		if($isjobinfo){
			$this->error("该部门下存在岗位，不能删除 ");
		}
		//判断是否有下节点
		$list=$model->where("parentid=".$id." and status=1")->select();
		//$oldparentid=$model->where("id=".$id)->getField("parentid");
		//判断是否为顶级节点，顶级节点父节点为0
		//if($oldparentid==0){
		//$this->error("该部门为顶级部门，不能删除");
		if($list){
			$this->error("该部门下存在下级部门，不能删除");
		}else{
			//判断该部门是否存在员工
			$mis_hr_personnel_person_infoDao = M("mis_hr_personnel_person_info");
			$personlist=$mis_hr_personnel_person_infoDao->where("deptid = ".$id." and status = 1")->count();
			if($personlist){
				$this->error("该部门下面有员工，请勿删除！");
			}
			//部门角色和用户绑定关系表
			$recodemodel = M ("mis_organizational_recode");
			//正确。则删除角色组关联关系
			$oldArr=$recodemodel->where("deptid=".$id)->select();
			foreach ($oldArr as $k=>$v){
				$rolmode=D("Rolegroup");
				$boolean=$rolmode->DelGroupUsers($v['rolegroup_id'],$v['userid']);
				if(!$boolean){
					$this->error("用户权限修改失败，请联系管理员");
				}
			}
			$result=$recodemodel->where("deptid=".$id)->delete();
			if(!$result){
				$this->error("用户部门角色关系删除失败，请联系管理员");
			}
			//如若删除的是公司需删除公司数据
			if(getFieldBy($id, "id", "iscompany", "mis_system_department")==1){
				$MisSystemCompanymodel=M("mis_system_company");
				$companyresult=$MisSystemCompanymodel->where("deptid=".$id)->delete();
				if(!$companyresult){
					$this->error("公司信息删除失败，请联系管理员");
				}
			}
		}
	}


	/**
	 * @Title: lookupUser
	 * @Description: todo(查询后台用户)
	 * @author liminggang
	 * @date 2013-11-29 下午5:42:31
	 * @throws
	 */
	public function lookupUser(){
		$model= M('mis_system_department');
		$deptlist = $model->where("status=1")->order("id asc")->select();
		$param['rel']="positiveBox";
		$param['url']="__URL__/lookupUser/jump/1/deptid/#id#/parentid/#parentid#";
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
				array("id" =>"mis_hr_personnel_person_info-name","val"=>"按员工姓名"),
				array("id" =>"orderno","val"=>"按员工编号"),

		);
		$searchtype=array(array("id" =>"2","val"=>"模糊查找"),
				array("id" =>"1","val"=>"精确查找"));
		$this->assign("searchbylist",$searchby);
		$this->assign("searchtypelist",$searchtype);

		$deptid		= $_REQUEST['deptid'];
		$parentid	= $_REQUEST['parentid'];
		if ($deptid && $parentid) {
			$deptlist =array_unique(array_filter (explode(",",$this->downAllChildren($deptlist,$deptid))));
			$map['dept_id'] = array(' in ',$deptlist);
		}
		$this->_list('user',$map);
		$this->assign('deptid',$deptid);
		$this->assign('parentid',$parentid);

		if ($_REQUEST['jump']) {
			$this->display('lookupUserlist');
		} else {
			$this->display("lookupUser");
		}
	}

	/**
	 * @Title: organization
	 * @Description: todo(首页组织结构加载)
	 * @author renling
	 * @date 2013-7-25 下午4:55:36
	 * @throws
	 */
	public function organization(){
		$_REQUEST['stpe'] = 'reconsitution';//用于_after_list()方法重构list数据 jiangx 2013-12-31
		$keyword	= $_POST['keyword'];
		$searchby = str_replace("-", ".", $_POST["searchby"]);
		$map['companyid']=$_SESSION['companyid'];
		if($_SESSION['a']){
			//管理员登陆取第一个公司
			$MisSystemCompanyModel=D("MisSystemCompany");
			$companyid=$MisSystemCompanyModel->getCompanyOne();
			$map['companyid']=$companyid;
		}
		if($_REQUEST['companyid']){
			$map['companyid']=$_REQUEST['companyid'];
		}
		$this->assign("MisSystemCompanyid",getFieldBy($map['companyid'], "companyid", "id", "mis_system_department","iscompany",1));
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
			if($searchby=='mis_hr_personnel_person_info-phone'){
				$placeholder="搜索电话";
			}
			if($searchby=='all'){
				$placeholder="搜索员工姓名,部门,职级";
			}
			$this->assign('placeholder', $placeholder);
			$this->assign('searchby', $searchby);
			$this->assign('keyword', $keyword);
		}
		$dptmodel = D("MisSystemDepartment");//部门表
		$deptlist = $dptmodel->where("status=1")->order("id asc")->field('id,name,companyid,parentid')->select();
		$param['rel']="missystemdepartment";
		$param['url']="__URL__/organization/jump/1/companyid/#companyid#/deptid/#id#";
		$typeTree = $this->getTree($deptlist,$param);
		$this->assign('tree',$typeTree);
		// 		if (method_exists ( $this, '_filter' )) {
		// 			$this->_filter ( $map );
		// 		}
		//得到部门编号
		$deptid=$_REQUEST['deptid'];
		$deptlist =array_unique(array_filter (explode(",",$this->findAlldept($deptlist,$deptid))));
		if($deptid){
		$map['deptid'] = array(' in ',$deptlist);
		}
		$this->assign("deptid",$deptid);
		//显示在线人数
		if ($_REQUEST['showtype']) {
			$mUserOnline = D("UserOnline");
			$useridlist =  $mUserOnline->getField('userid', true);
			$map['id'] = array('in', $useridlist);
		}
		$map['mis_hr_personnel_person_info.status']=1;
		$this->_list ('MisHrPersonnelUserDeptView', $map );
		$uid=$_SESSION[C('USER_AUTH_KEY')];
		$this->assign('uid',$uid);
		if ($_REQUEST['jump']) {
			$this->display('deptindex');
		} else {
			$this->display();
		}
	}
	public function _after_insert($id){
		cookie::set('departmentcheck',$id);
		$this->adddeptuser($id);
	}
	/**
	 * @Title: _after_list
	 * @Description: todo(对list进行重新构造)
	 * @param 数组 $list
	 * @author jiangx
	 * @date 2013-12-31
	 * @throws
	 */
	public function _after_list( &$list){
		if($_REQUEST['stpe'] == 'reconsitution'){
			$mUserOnline = D("UserOnline");
			//获取在线人uid
			$useridlist =  $mUserOnline->getField('userid, modify_time');
			foreach ($list as $key => $val) {
				//组织照片路径
				if ($val['picture']) {
					$val['picture'] = '__PUBLIC__/'. $val['picture'];
				}
				if ($val['sex'] == 1 && (!$val['picture'])) { //男性无照片
					$val['picture'] = '__PUBLIC__/Images/xyimages/organization/user_male.jpg';
				}
				if ((!$val['sex']) && (!$val['picture'])) { //女性无照片
					$val['picture'] = '__PUBLIC__/Images/xyimages/organization/user_female.jpg';
				}
				//判断是否在线
				$pictureinfo = pathinfo($val['picture']);
				if (!$useridlist[$val['id']]) {
					//$val['picture'] = $pictureinfo['dirname']. '/'. $pictureinfo['filename'].'gray'. '.' . $pictureinfo['extension'];
				}
				$list[$key]['picture'] = $val['picture'];
			}
		}
	}

	/**
	 * @Title: getMessages
	 * @Description: todo(查指部门信息出来,在OAHelperAction中有调用)
	 * @param int $whidDepart 部门ID
	 * @return 返回一个二维数组
	 * @author eagle
	 * @date 2014-5-15 上午9:37:31
	 * @throws
	 */
	public function getMessages($whidDepart=''){
		//实例化mis_message_user表
		$model = D('MisSystemDepartment');
		if ($_SESSION[C('USER_AUTH_KEY')]){

			if($whidDepart){
				//定义where条件
				$model = D('MisHrPersonnelPersonInfo');
				$map = array();
				$map['status'] = 1;
				$map['working'] = 1;   				//离职不能显示出来
				$map['deptid'] = $whidDepart;
				$list = $model->where( $map )->order('id asc')->select();
				return $list;
			}else{
				$mode1=D("User");
				$map = array();
				$map['status'] = array('GT',0);
				//是管理员的不显示出来
				$map['name'] = array('NEQ','管理员');
				$Userlist=$mode1->field("id,name,dept_id,email,employeid")->where($map)->order('sort ASC')->select();

				$returnarr = array();
				$dptmodel = D("MisSystemDepartment");//部门表
				$deptlist = $dptmodel->where("status=1")->order("id asc")->field('id,name,parentid,iscenter')->select();
				foreach($deptlist as $k=>$v){
					$newDept=array();
					$newDept['id']         = $v['id'];
					$newDept['parentid']   = $v['parentid'] ? $v['parentid']:0;
					$newDept['title']      = $v['name']; //光标提示信息
					$newDept['name']       = missubstr($v['name'],28,true); //结点名字，太多会被截取,针对于现在的ZTREE，宽度不能超过18个字符。
					$newDept['isParent']   = "true";
					if($v['parentid'] == 0){
						$newDept['open'] = $v['open'] ? false : true;
					}

					//是管理中心的才把，人找出来
					if($v['iscenter']){
						// 构造用户
						foreach ($Userlist as $k2 => $v2) {
							if($v2['dept_id'] == $v['id']){
								$newv2              = array();
								$newv2['id']        = $v2['employeid'];
								$newv2['parentid']  = $v['id'];
								$newv2['title']     = $v2['name']; //光标提示信息
								$newv2['name']      = missubstr($v2['name'],28,true); //结点名字，太多会被截取,针对于现在的ZTREE，宽度不能超过18个字符。
								$newv2['icon']      = "./images/group.png";
								$newv2['open']      = true;
								$newv2['isParent']  = "false";
								array_push($returnarr,$newv2);
							}
						}
					}
					array_push($returnarr,$newDept);
				}
				return $returnarr;
			}
		}else {
			$this->error ( '没有获取到当前登陆人！' );
		}
	}
	/**
	 * @Title: getPhoneMessages
	 * @Description: todo(查指部门信息出来显示到手机上)  在OAHelperAction中有调用
	 * @author eagle
	 * @date 2014-2-17 下午4:21:44
	 * @throws
	 */
	public function getPhoneMessages(){
		$map = array();
		$map['status'] = array('GT',0);
		$returnarr = array();
		$dptmodel = D("MisSystemDepartment");//部门表
		$deptlist = $dptmodel->where("status=1")->order("id asc")->field('id,name,parentid,iscenter')->select();
		//('id'=>1,'name'=>'集团管理层','parentid'=>0,'iscenter'=>0,'leader'=>"集团公司")
		//去除中心，部门说明加入中心字段
		$newA[0] =array('id'=>1,'name'=>'集团管理层','parentid'=>0,'iscenter'=>0,'leader'=>"集团公司");
		foreach($deptlist as $k=>$v){
			if($v['iscenter']==0){
				foreach($deptlist as $sk=>$sv){
					if($v['parentid']==$sv['id']){
						$v['leader'] = $sv['name'];
					}
				}
				$newA[]=$v;
			}
		}
		//dump($newA);
		return $newA;  //反回重新拼装的部门树。 适合在手机上显示
	}
	/**
	 * @Title: getPhoneCompanyMessages
	 * @Description: todo(查指公司信息出来显示到手机上) 在OAHelperAction中有调用
	 * @author eagle
	 * @date 2014-2-17 下午4:21:44
	 * @throws
	 */
	public function getPhoneCompanyMessages(){
		$map = array();
		$map['status'] = array('GT',0);
		$map['iscompany'] = 1;
		$returnarr = array();
		$model = D("MisSystemDepartment");//部门表
		$companylist = $model->where($map)->order("id asc")->field('id,name,parentid,iscenter')->select();

		foreach($companylist as $k=>$v){
			$v['leader'] = "集团管理层";
			$newA[]=$v;
		}
		//dump($newA);
		return $newA;  //反回重新拼装的部门树。 适合在手机上显示
	}
	/**
	 * @Title: comboxrefreshIntomas
	 * @Description: todo(根据公司ID获取部门信息)
	 * @author liminggang
	 * @date 2013-5-31 下午5:28:54
	 * @throws
	 */
	public function comboxrefreshDept(){
		//获得公司ID
		$companyid = $_POST['companyid'];
		//部门信息
		$MisSystemDepartment = M("mis_system_department");
		//查询所有部门信息
		$where = array();
		$where['status'] = 1;
		$where['companyid'] = $companyid;
		$deptlist = $MisSystemDepartment->where($where)->getField("id,name");
		//给模板赋值
		$arr = array();
		if($deptlist){
			foreach($deptlist as $k=>$v){
				$arr2=array();
				$arr2[]=$k;
				$arr2[]=$v;
				array_push($arr,$arr2);
			}
		}
		echo  json_encode($arr);
	}
	
	public function lookupBackendQuyu(){
		//构造左侧部门树结构
		$name='MisAutoMji';
		$model= D('MisAutoMji');
		$deptlist=array();
		$condition['parentid']=0;
		$deptlist = $model->where($condition)->select();
		foreach ($deptlist as $k=>$v){
			$deptlist[$k]['parentid']=$v['parentid'];
		}
		$treemiso[]=array(
				'id'=>0,
				'pId'=>0,
				'name'=>'区域档案',
				'orderno'=>'0',
				'title'=>'区域档案',
				'open'=>true,
				'isParent'=>true,
		);
		//$deptlist=array_merge($deptlist,$misAutoNthzgList);
		//dump($deptlist);
		$param['rel']="misrowaccessright1";
		$param['url']="__URL__/lookupBackendQuyu/jump/1/parentid/#id#";
		$common = A('Common');
		$typeTree = $common->getTree($deptlist,$param,$treemiso);
		//获得树结构json值
		$this->assign('tree',$typeTree);
		$map = array();
		$parentid=$_REQUEST['parentid'];
		if(empty($_REQUEST['parentid'])){
			$parentid=$deptlist[0]['id'];
		}
		$map['parentid']=$parentid;
		$this->assign('parentid',$_REQUEST['parentid']);
		$keyword	= $_POST['keyword'];
		$searchby = str_replace("-", ".", $_POST["searchby"]);
		if ($keyword) {
			if($searchby =="all"){
				$map['name']=array('like','%'.$keyword.'%');
			}else{
				$map[$searchby]=array('like','%'.$keyword.'%');
			}
			$searchby =$_POST["searchby"];
			if($searchby=='name'){
				$placeholder="搜索区域名称";
			}
			if($searchby=='all'){
				$placeholder="搜索区域名称";
			}
			$this->assign('placeholder', $placeholder);
			$this->assign('searchby', $searchby);
			$this->assign('keyword', $keyword);
		}
		$common->_list($name,$map,'id',true);
		if ($_REQUEST['jump']) {
			$this->display('lookupBackendQuyuList');
		} else {
			$this->display("lookupBackendQuyu");
		}
	}
	
}
?>