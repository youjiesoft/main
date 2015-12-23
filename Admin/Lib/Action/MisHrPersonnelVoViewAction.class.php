<?php
/**
 * @Title: MisHrPersonnelVoViewAction
 * @Package package_name
 * @Description: todo(员工对象预览)
 * @author renling
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-7-10 上午11:16:59
 * @version V1.0
 */
class MisHrPersonnelVoViewAction extends CommonAuditAction{
	protected $adduserid="";
	public function index() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name = $this->getActionName();

		if (! empty ( $name )) {
			$qx_name=$name;
			if(substr($name, -4)=="View"){
				$qx_name = substr($name,0, -4);
			}
			//验证浏览及权限
			if( !isset($_SESSION['a']) ){
				$map=D('User')->getAccessfilter($qx_name,$map);	
			}
			$this->_list ( "MisHrPersonnelUserDeptView", $map );
		}
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($name);
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		//扩展工具栏操作
		$toolbarextension = $scdmodel->getDetail($name,true,'toolbar');
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		if( intval($_POST['dwzloadhtml']) ){
			$this->display("dwzloadindex");exit;
		}
		//首页收件箱调用方法，为ajax调用
		if ($_GET['type'] == "ajaxcall") {
			$this->display ("ajax_index");exit;
		}
		$this->display ();
		return;
	}
	/**
	 +----------------------------------------------------------
	 * 根据表单生成查询条件
	 * 进行列表过滤
	 +----------------------------------------------------------
	 * @access protected
	 +----------------------------------------------------------
	 * @param Model $model 数据对象
	 * @param HashMap $map 过滤条件
	 * @param string $sortBy 排序
	 * @param boolean $asc 是否正序
	 * @param mothed $mothed 针对_list查询完后对返回的数组进行再次处理，所以需要传入一个处理方法名。
	 * @param module $modules 如果当前传入的$name 是视图的话，是无法用method_exists方法，所以需要多传入一个当前模型名字。
	 +----------------------------------------------------------
	 * @return void
	 +----------------------------------------------------------
	 * @throws ThinkExecption
	 +----------------------------------------------------------
	 */
	protected function _list($name, $map, $sortBy = '', $asc = false) {
		//测试点
		//dump($map);
		$model = D($name);
		//排序字段 默认为主键名
		if (isset ( $_REQUEST ['orderField'] )) {
			$order = $_REQUEST ['orderField'];
		} else {
			$order = ! empty ( $sortBy ) ? $sortBy : $model->getPk ();
		}
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['orderDirection'] )) {
			$sort = $_REQUEST ['orderDirection'];
		} else {
			$sort = $asc ? 'asc' : 'desc';
		}
		if ($_SESSION ['a'] != 1) {
			$broMap = Browse::getUserMap ( $this->getActionName() );
			if ($broMap) {
				if($map['_string']){
					$map['_string'] .= " and " . $broMap;
				}else{
					$map['_string']= $broMap;
				}
				
			}
		}
		/* ***************** 修改 ***************** */
		if($_POST['search_flag'] == 1){
			$this->setAdvancedMap($map);
		}
		//取得满足条件的记录数
		// 		if($_POST['search_flag'] == 1){
		// 			//获取search模板唯一标示ename
		// 			$ename=$_REQUEST["ename"];
		// 			$search_sql = R("Search/spellSql", array($map, $name,$ename));
		// 			$_POST['search_sql']=$search_sql;
		// 			$count = count($model->query($search_sql));
		// 		}else{
		$count = $model->where ( $map )->count ( '*' );
		// 		}
		/* ***************** 修改 ***************** */
		//不存在则遍历一遍重新拼装$map来处理视图类型数据
		if ($count > 0) {
			import ( "@.ORG.Page" );
			//创建分页对象
			$numPerPage=C('PAGE_LISTROWS');
			$dwznumPerPage=C('PAGE_DWZLISTROWS');
			if (! empty ( $_REQUEST ['numPerPage'] )) {
				$numPerPage = $_REQUEST ['numPerPage'];
			}
			if( $_POST["dwzpageNum"]=="") $dwznumPerPage = $numPerPage;

			$p = new Page ( $count, $numPerPage,'',$dwznumPerPage );
			//分页查询数据
			if($_POST['dwzloadhtml']) $p->firstRow = $p->firstRow + (intval($_POST['dwzpageNum'])-1)*$numPerPage;
			/* ***************** 修改 ***************** */

			// 			if($_POST['search_flag'] == 1){
			// 				if($_POST['export_bysearch']==1){//如果是导出则无分页
			// 					$search_sql .= " ORDER BY `{$_POST['maintable']}`.`$order` $sort";
			// 				}else{
			// 					$search_sql .= " ORDER BY `{$_POST['maintable']}`.`$order` $sort LIMIT {$p->firstRow},{$p->listRows}";
			// 				}

			// 				/* 开启搜索缓存 */
			// 				$md5_sql = md5($search_sql);
			// 				$voList = S($md5_sql);
			// 				//开启搜索缓存
			// // 				if(!$voList){
			// // 					$voList = $model->query($search_sql);
			// // 					S($md5_sql, $voList);
			// // 				}
			// 				//关闭搜索缓存
			// 				$voList = $model->query($search_sql);

			// 			}else{
			if($_POST['export_bysearch']==1){//如果是导出则无分页
				$voList = $model->where($map)->order( "`" . $order . "` " . $sort)->select();
			}else{
				$voList = $model->where($map)->order( "`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select();
			}
			// 			}
			/* ***************** 修改 ***************** */
			$this->setToolBorInVolist($voList);
			$module=A($this->getActionName());

			if (method_exists($module,"_after_list")) {
				call_user_func(array(&$module,"_after_list"),&$voList);
			}
			//如果是导出直接输出到excel
			if($_POST['export_bysearch']==1){
				$this->exportBysearch($voList);
			}
			// 处理lookup数据 by杨东
			if($_POST['dealLookupList']==1){
				$this->dealLookupList($voList);
			}

			foreach ( $map as $key => $val ) {
				if (! is_array ( $val )) {
					$p->parameter .= "$key=" . urlencode ( $val ) . "&";
				}
			}
			$page = $p->show ();
			//列表排序显示
			$sortImg = $sort; //排序图标
			$sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
			$sort = $sort == 'desc' ? 'desc' : 'asc'; //排序方式
			$pageNum= !empty($_REQUEST[C('VAR_PAGE')])?$_REQUEST[C('VAR_PAGE')]:1;
			//模板赋值显示
			$this->assign ( 'pageNum', $pageNum );
			$this->assign ( 'list', $voList );
			$this->assign ( "page", $page );
		}else{
			if($_POST['export_bysearch']==1){
				$this->exportBysearch(array());
			}
		}
		$this->assign ( 'dwztotalpage',C('PAGE_DWZLISTROWS')/$numPerPage );
		$this->assign ( 'sort', $sort );
		$this->assign ( 'order', $order );
		$this->assign ( 'sortImg', $sortImg );
		$this->assign ( 'sortType', $sortAlt );
		$this->assign ( 'totalCount', $count );
		$this->assign ( 'numPerPage', $numPerPage);
		$this->assign ( 'dwznumPerPage', C('PAGE_DWZLISTROWS'));
		$this->assign ( 'currentPage', !empty($_REQUEST[C('VAR_PAGE')])?$_REQUEST[C('VAR_PAGE')]:1);
		Cookie::set ( '_currentUrl_', __SELF__ );
		return;
	}
	public function _after_list( &$voList ){
		foreach ($voList as $key=>$val) {
			if($val['id']){
				if(getFieldBy($val['id'], 'employeid', 'id', 'user')){
					//存在则为查看
					$voList[$key]['uid']=getFieldBy($val['id'], 'employeid', 'id', 'user');
				}else{
					$voList[$key]['uid']=-1;
				}
			}
			//查询该员工是否在申请转正
			$becomeEmployeeModel=D("MisHrBecomeEmployee");
			$bre=$becomeEmployeeModel->where("employeeid=".$val['id']." and (auditState = 1 or auditState = 2)")->getField("id");
			if($bre){
				$voList[$key]['employeeStatus']='1';
			}
			//查询该员工是否在申请离职
			$LeaveEmployeeModel=D("MisHrLeaveEmployee");
			$lre=$LeaveEmployeeModel->where("employeeid=".$val['id']." and (auditState = 1 or auditState = 2)")->getField("id");
			if($lre){
				$voList[$key]['employeeStatus']='2';
			}
			//查询改员工是否在申请请假
			$employeeLeaveManagementModel=D("MisHrEmployeeLeaveManagement");
			$mre=$employeeLeaveManagementModel->where("personid=".$val['id']." and auditState = 3")->select();
			foreach ($mre as $k=>$v){//判断当前时间是否在请假期内
				$beginleavedate=$v['beginleavedate'];//请假开始
				$endleavedate=$v['endleavedate'];//请假结束
				$nowtime=time();
				if($nowtime > $beginleavedate && $nowtime < $endleavedate){
					$voList[$key]['employeeStatus']='3';
				}
			}
			//查询改员工是否在申请异动
			$personnelTrainInfoModel=D("MisHrPersonnelTrainInfo");
			$tre=$personnelTrainInfoModel->where("personid=".$val['id']." and (auditState = 1 or auditState = 2)")->getField("id");
			if($tre){
				$voList[$key]['employeeStatus']='4';
			}
			//查询工龄
			if($val['workstatus']==0){//离职的
				$s = $val['indate'];
				$e = intval($val['leavedate']);
			}else{
				$s = $val['indate'];
				$e = time();
			}
			import ( "@.ORG.Date" );
			$date=new Date(intval( $s ));
			$voList[$key]['workage']=$date->timeDiff($e,1,false);
		}
		//检索员工状态
		if($_REQUEST['quickemployeeStatus']){
			foreach ($voList as $k1=>$v1){
				if($v1['employeeStatus'] != $_REQUEST['quickemployeeStatus']){
					unset($voList[$k1]);
				}
			}
		}
	}
	/**
	 * @Title: editgeneral
	 * @Description: todo(修改员工综合信息)
	 * @author renling
	 * @date 2013-10-25 下午4:32:19
	 * @throws
	 */
	public function editgeneral(){
		//获取公司
		$UserDeptDutyModel=D("UserDeptDuty");
		$UserDeptDutyList=$UserDeptDutyModel->where(" typeid=1 and  status=1 and employeid=".$_REQUEST['id'])->select();
		$this->assign("UserDeptDutyList",$UserDeptDutyList);
		//查询岗位类别
		$MisHrTypeinfoModel=D('MisHrTypeinfo');
		//查询工种
		//$worktypeList=$MisHrTypeinfoModel->where(" status=1 and pid=63")->getField('id,name');
		//$this->assign("worktypeList",$worktypeList);
		//查询民族
		$nationList=$MisHrTypeinfoModel->where(" status=1 and pid=96")->getField('id,name');
		$this->assign("nationList",$nationList);
		$map['id']=$_REQUEST['id'];
		$map['status']=1;
		$mMisHrBasicEmployeeModel=D('MisHrBasicEmployee');
		$aMisHrBasicEmployeeVo=$mMisHrBasicEmployeeModel->where($map)->find();
		//获取当前所在公司 部门 岗位 id
		$current = array();
		$current['deptid'] = $aMisHrBasicEmployeeVo['deptid'];
		$current['workid'] = $aMisHrBasicEmployeeVo['worktype'];
		$this->assign("current",$current);
		$aMisHrBasicEmployeeVo['age'] = round((time()-$aMisHrBasicEmployeeVo['birthday'])/(365*86400));
		//var_dump($aMisHrBasicEmployeeVo);
		$this->assign("vo",$aMisHrBasicEmployeeVo);

		//根据人员部门获取部门下面的岗位
		// 		$MisHrjJobInfomodel=M("mis_hr_job_info");
		// 		$deptid = $aMisHrBasicEmployeeVo['deptid'];
		// 		$where = array();
		// 		$where['status'] = 1;
		// 		$where['deptid'] = $deptid;
		// 		$joblist=$MisHrjJobInfomodel->where($where)->getField("id,name");
		// 		$this->assign('joblist',$joblist);
		//end

		$this->generalCommon();

		// 		//查询部门
		// 		$MisSystemDepartmentModel=D('MisSystemDepartment');
		// 		$deptList = $MisSystemDepartmentModel->where(" status=1")->select();
		// 		$deptList = $this->selectTree($deptList,0,0,$aMisHrBasicEmployeeVo['deptid']);
		// 		$this->assign("deptidlist",$deptList);

		$this->display("MisHrPersonnelVoView:editgeneral");
	}
	/**
	 * @Title: birthday
	 * @Description: todo(根据出生年月计算年龄)
	 * @param unknown_type $mydate
	 * @return number
	 * @author renling
	 * @date 2013-7-15 上午11:48:24
	 * @throws
	 */
	public	function lookupbirthday(){
		//出生年月
		$birth=$_POST['birthday'];
		list($by,$bm,$bd)=explode('-',$birth);
		$cm=date('n');
		$cd=date('j');
		$age=date('Y')-$by-1;
		if ($cm>$bm || $cm==$bm && $cd>$bd) $age++;
		echo $age;
	}
	/**
	 * @Title: uploadimg
	 * @Description: todo(上传公司logo)
	 * @author renling
	 * @date 2013-6-27 下午4:19:43
	 * @throws
	 */
	public function lookuploadimg(){
		//添加成功后，临时文件夹里面的文件转移到目标文件夹
		$fileinfo=pathinfo($_POST['upload_name']);
		$from = UPLOAD_PATH_TEMP.$_POST['upload_name'];//临时存放文件
		if( file_exists($from) ){
			$p=UPLOAD_PATH.$fileinfo['dirname'];// 目标文件夹
			if( !file_exists($p) ) {
				$this->createFolders($p); //判断目标文件夹是否存在
			}
			$to= UPLOAD_PATH.$_POST['upload_name'];
			rename($from,$to);
			echo $_POST['upload_name'];
		}
	}
	/**
	 * @Title: generalCommon
	 * @Description: todo(这里用一句话描述这个方法的作用)
	 * @author renling
	 * @date 2013-10-25 下午2:24:27
	 * @throws
	 */
	private function generalCommon(){
		//查询职位
		$DutyModel=D('Duty');
		$DutyList=$DutyModel->where(" status=1")->getField('id,name');
		$this->assign("DutyList",$DutyList);
		//所属公司信息
		$MisSystemCompanyDAO=M('mis_system_company');
		$companylist=$MisSystemCompanyDAO->where('status = 1')->field('id,name')->select();
		$this->assign('companylist',$companylist);
		//查询学历
		$typeinfoModel=D('TypeInfo');
		$typeinfoList=$typeinfoModel->where(" status=1  and  pid=44")->getField("id,name");
		$this->assign("typeinfoList",$typeinfoList);
		/**
		 * 以下是修改公用
		* **/
		//人事关系模型
		$id=$_REQUEST['id'];
		$misHrEmployeePrivyModel = D ("MisHrEmployeePrivy");
		//工作经验
		$misHrPersonnelExperienceInfoModel=D('MisHrPersonnelExperienceInfo');
		//教育经历
		$misHrPersonnelEducationInfoModel=D('MisHrPersonnelEducationInfo');
		$PrivyMap['employeeid']=$id;
		$PrivyMap['privytype']=1;  //介绍人
		$PrivyMap['status']=1;
		//介绍人集合
		$misHrEmployeePrivyList=$misHrEmployeePrivyModel->where($PrivyMap)->select();
		$familyMap['employeeid']=$id;
		$familyMap['privytype']=0;  //家庭成员
		$familyMap['status']=1;
		//家庭成员集合
		$familyList=$misHrEmployeePrivyModel->where($familyMap)->select();
		$experienceMap['personinfoid']=$id;
		$experienceMap['status']=1;
		//工作经验集合
		$misHrPersonnelExperienceInfoList=$misHrPersonnelExperienceInfoModel->where($experienceMap)->select();
		$educationInfoMap['personinfoid']=$id;
		$educationInfoMap['status']=1;
		//教育经历集合
		$misHrPersonnelEducationInfoList=$misHrPersonnelEducationInfoModel->where($educationInfoMap)->select();
		$this->assign("misHrEmployeePrivyList",$misHrEmployeePrivyList);
		$this->assign("familyList",$familyList);
		$this->assign("misHrPersonnelExperienceInfoList",$misHrPersonnelExperienceInfoList);
		$this->assign("misHrPersonnelEducationInfoList",$misHrPersonnelEducationInfoList);
	}
	/**
	 * @Title: lookupdelete
	 * @Description: todo(ajax 请求删除员工基本信息中的附加信息)
	 * @author renling
	 * @date 2013-10-25 下午5:38:16
	 * @throws
	 */
	public function lookupdelete(){
		$name=$_POST['name'];
		$model=D($name);
		$map['id']=$_POST['id'];
		$result=$model->where($map)->delete();
		$model->commit();
		echo $result;
	}
	/**
	 * @Title: updateGeneral
	 * @Description: todo(员工管理-修改综合信息方法)
	 * @return boolean
	 * @author renling
	 * @date 2013-10-25 下午2:10:15
	 * @throws
	 */
	public  function updateGeneral() {
		$model = D ('MisHrBasicEmployee');
		//在职状态是否有该员工
		$isResultList=$model->where(" chinaid='".$_POST['chinaid']."'")->select();
		$boolReasult=$this->idcard_checksum18($_POST['chinaid']);
		if($boolReasult==false){
			$this->error ('身份证格式有误,请输入正确的身份证号码！');
		}
		if($_POST['workstatus']=='2'){ //试用员工算出转正日期
			// 根据试用期 算出转正日期
			$_POST['transferdate'] = strtotime('+'.$_POST['probationcycle'].' month', strtotime($_POST['indate']));
		}else{
			$_POST['transferdate']=strtotime($_POST['transferdate']);
		}
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		$employeid=$_POST['id'];
		// 更新数据
		$list=$model->save ();
		$model->commit();
		if(!$list){
			$this->error ('员工基本资料修改失败！');
		}
		cookie::set("checkcompanyid",$_REQUEST['checkcompanyid']);// 新增时默认选中 cookie
		unset($_POST['id']);
		//修改多岗位   
		$companyid=$_POST['companyid'];
		//多组织记录表
		$UuserDeptDutyModel=D('UserDeptDuty');
		//权限组
		$rolegroupUserModel=D("RolegroupUser");
		$companyList=array();
		foreach ($companyid as $key=>$val){
			if(in_array($val,array_keys($companyList))){
				$this->error(getFieldBy($val,"id","name","mis_system_company")."添加重复,请查证后提交！");
			}
			$UserDeptDuty=array();
			$UserDeptDuty['userid']=$_POST['userid'];
			$UserDeptDuty['deptid']=$_POST['deptid'][$key];
			$UserDeptDuty['dutyid']=$_POST['dutylevelid'][$key];
			$UserDeptDuty['worktype']=$_POST['worktype'][$key];
			$UserDeptDuty['employeid']=$employeid;
			$UserDeptDuty['typeid']=1;
			$UserDeptDuty['companyid']=$_POST['companyid'][$key];
			
			//添加修改案例
			if($_POST['udid'][$key]){
				$UserDeptDuty['id']=$_POST['udid'][$key];
				$UserDeptDuty['updateid']=$_SESSION[C('USER_AUTH_KEY')];
				$UserDeptDuty['updatetime']=time();
				if($_POST['oldworktye'][$key]!=$_POST['worktype'][$key]){
					//删除原有权限组，添加新的权限组
					if($_POST['userid']){
						//已建立用户 查询该职级权限组
						$role_groupid=getFieldBy($_POST['oldworktye'][$key],"id", "rolegroup_id", "mis_hr_job_info");
						//删除原有权限组
						$rolegroupUserModel->where("userid={$_POST['userid']} and rolegroup_id={$role_groupid}")->delete();
						//新的权限组id
						$newrole_groupid=getFieldBy($_POST['worktye'][$key],"id", "rolegroup_id", "mis_hr_job_info");
						//添加现有权限组
						$rolegroupdate=array();
						$rolegroupdate['userid']=$_POST['userid'];
						$rolegroupdate['rolegroup_id']=$newrole_groupid;
						$rolegroupdate['companyid']=$val;
						$rolegroupUserResult=$rolegroupUserModel->add($rolegroupdate);
						if(!$rolegroupUserResult){
							$this->error("添加现有权限组失败！");
						}
					}
				}
				$UserDeptUserResult=$UuserDeptDutyModel->save($UserDeptDuty);
			}else{
				$UserDeptDuty['createid']=$_SESSION[C('USER_AUTH_KEY')];
				$UserDeptDuty['createtime']=time();
				$UserDeptUserResult=$UuserDeptDutyModel->add($UserDeptDuty);
				if($_POST['userid']){
					//新的权限组id
					$newrole_groupid=getFieldBy($_POST['worktype'][$key],"id", "rolegroup_id", "mis_hr_job_info");
					//添加现有权限组
					$rolegroupdate=array();
					$rolegroupdate['user_id']=$_POST['userid'];
					$rolegroupdate['rolegroup_id']=$newrole_groupid;
					$rolegroupdate['companyid']=$val;
					$rolegroupUserResult=$rolegroupUserModel->add($rolegroupdate);
					if(!$rolegroupUserResult){
						$this->error("添加现有权限组失败！");
					}
				}
			}
			$UuserDeptDutyModel->commit();
			$companyList[$val]=1;
			if(!$UserDeptUserResult){
				$this->error("数据异常");
			}
		}
		//删除已取消的多组织
		$userDelMap=array();
		$userDelMap['id']=array("in",$_POST['delids']);
		$UuserDeptDutyModel->where($userDelMap)->setField ( 'status', - 1 );
		//已取消多组织需删除原有权限组 deldutyids 已是rolegroup_id 多个逗号分隔
		if($_POST['delworktypes']&&$_POST['userid']){
			$delRoleGroupUserMap['rolegroup_id']=array("in",$_POST['delworktypes']);
			$delRoleGroupUserMap['user_id']=$_POST['userid'];
			//直接删除原有权限组
			$rolegroupUserDel=$rolegroupUserModel->where($delRoleGroupUserMap)->delete();
			if(!$rolegroupUserDel){
				$this->error("删除原有权限组失败,请联系管理员！");
			}
		}
		//人事关系模型
		$mfbsModel = D ("MisHrEmployeePrivy");
		//工作经验
		$MisHrPersonnelExperienceInfoModel=D('MisHrPersonnelExperienceInfo');
		//家庭成员
		$MisHrPersonnelFamilyInfoModel=D('MisHrEmployeePrivy');
		//教育经历
		$MisHrPersonnelEducationInfoModel=D('MisHrPersonnelEducationInfo');
		//人事关系标记
		$bPrivyFamily=true;
		//介绍人标记
		$bPrivyRemid=true;
		//教育经历标记
		$bEducation=true;
		//工作经验标记
		$bExperience=true;
		//获取到要插入数据
		if($_REQUEST['privyname']){ //介绍人
			foreach($_REQUEST["privyname"] as $key=>$value){
				$date=array();
				$date["employeeid"]=$id;
				$date["privytype"]=1;//介绍人
				if($_REQUEST['privyemid']){
					$date["privyemid"]=$_REQUEST["privyemid"][$key];//关联员工ID
				}
				$date["privyname"]=$_REQUEST["privyname"][$key];//姓名
				$date["relation"]=$_REQUEST["privyrelation"][$key];//关系
				$date["privytel"]=$_REQUEST["privytelephone"][$key];//联系电话
				if($_REQUEST["Privyid"][$key]){
					//修改信息
					$date['id']=$_REQUEST["Privyid"][$key];
					$list=$mfbsModel->data($date)->save();
				}else{
		 			//保存当前数据对象
					unset($date['id']);
					$list=$mfbsModel->data($date)->add();
				}
				if (!$list) {
					//介绍人标记
					$bPrivyRemid=false;
				}
			}
		}
		if($_REQUEST['startdate']){ //工作经验
			$Expdate=array();
			foreach($_REQUEST["startdate"] as $key=>$value){
				$Expdate["personinfoid"]=$id; //基础表ID
				$Expdate["company"]=$_REQUEST['company'][$key];//公司
				$Expdate["position"]=$_REQUEST["position"][$key];//职业
				$Expdate["remark"]=$_REQUEST["expremark"][$key];//备注
				$Expdate["startdate"]=strtotime($_REQUEST["startdate"][$key]);//备注
				$Expdate["finishdate"]=strtotime($_REQUEST["finishdate"][$key]);//备注
				if($_REQUEST["Experienceid"][$key]){
					//修改信息
					$Expdate['id']=$_REQUEST["Experienceid"][$key];
					$list=$MisHrPersonnelExperienceInfoModel->data($Expdate)->save();
				}else{
					unset($Expdate['id']);
					//保存当前数据对象
					$list=$MisHrPersonnelExperienceInfoModel->data($Expdate)->add();
				}
				$MisHrPersonnelExperienceInfoModel->commit();
				if (!$list) {
					//工作经验标记
					$bExperience=false;
				}
			}
		}
		if($_REQUEST['edustartdate']){ //教育经历
			$edudate=array();
			foreach($_REQUEST["edustartdate"] as $key=>$value){
				$edudate["personinfoid"]=$id; //基础表ID
				$edudate["startDate"]=strtotime($_REQUEST['edustartdate'][$key]);//开始时间
				$edudate["finishDate"]=strtotime($_REQUEST['edufinishdate'][$key]);//结束时间
				$edudate["school"]=$_REQUEST['newschool'][$key];//学校机构
				$edudate["skillAndCertificate"]=$_REQUEST["skillAndCertificate"][$key];//专业与技能
				if($_REQUEST["Educationid"][$key]){
					//修改信息
					$edudate['id']=$_REQUEST["Educationid"][$key];
					$list=$MisHrPersonnelEducationInfoModel->data($edudate)->save();
				}else{
		 		//保存当前数据对象
					unset($edudate['id']);
					$list=$MisHrPersonnelEducationInfoModel->data($edudate)->add();
				}
				if ($list==false) {
					//教育经历标记
					$bEducation=false;
				}
			}
		}
		if($_REQUEST['relation']){ //家庭成员
			$Familydata=array();
			foreach($_REQUEST["relation"] as $key=>$value){
				$Familydata["employeeid"]=$id; //基础表ID
				$Familydata["relation"]=$_REQUEST['relation'][$key];//关系
				$Familydata["privyname"]=$_REQUEST['familyname'][$key];//姓名
				$Familydata["privycompany"]=$_REQUEST['familycompany'][$key];//工作单位
				$Familydata["privytel"]=$_REQUEST['telephone'][$key];//联系电话
				$Familydata["skillAndCertificate"]=$_REQUEST["skillAndCertificate"][$key];//专业与技能
				if($_REQUEST["familyid"][$key]){
					//修改信息
					$Familydata['id']=$_REQUEST["familyid"][$key];
					$list=$MisHrPersonnelFamilyInfoModel->data($Familydata)->save();
				} else{
					//保存当前数据对象
					unset($Familydata['id']);
					$list=$MisHrPersonnelFamilyInfoModel->data($Familydata)->add ();
				}
				if ($list==false) {
					//人事关系标记
					$bPrivyFamily=false;
				}
			}
		}
		//name字段拼音生成
		$this->getPinyin('mis_hr_personnel_person_info','name',$id);
		if($bPrivyFamily==false&&$bPrivyRemid==false&&$bEducation==false&&$bExperience==false){
			$this->success('员工附加信息修改失败！');
		}else{
			$this->success ('员工综合信息修改成功！');
		}
	}
	public function view(){
		$this->getBasis();//基础信息
		$this->getuserdept();//组织结构
		$this->display("MisHrPersonnelManagement:view");
	}
	/**
	 * @Title: getAgeByToCarId
	 * @Description: todo(根据身份证号码计算年龄或者出生年月日)
	 * @param 身份证 $idcard
	 * @param 是否返回出生年月 $isYear  //默认返回出生年月日，如果传入false则返回年龄
	 * @return string|Ambigous <number, unknown>
	 * @author liminggang
	 * @date 2013-11-7 下午2:52:27
	 * @throws
	 */
	public function getAgeByToCarId($idcard,$isYear=true){
		//获得身份证的出生年月日
		$year = substr($idcard,6, 4);
		$month = substr($idcard,10, 2);
		$day = intval(substr($idcard,12, 2));
		$birthday=$year."-".$month."-".$day;
		$date=strtotime($birthday);
		if($isYear){
			return $birthday;
		}else{
			//获得出生年月日的时间戳
			$today=strtotime('today');
			//获得今日的时间戳
			$diff=floor(($today-$date)/86400/365);
			//得到两个日期相差的大体年数
			//strtotime加上这个年数后得到那日的时间戳后与今日的时间戳相比
			$age=strtotime(substr($id,6,8).' +'.$diff.'years')>$today?($diff):$diff-1;
			return $age;
		}
	}
	/**
	 * @Title: getBasis
	 * @Description: todo(获取基础信息)
	 * @author 杨东
	 * @date 2013-7-18 下午4:13:29
	 * @throws
	 */
	protected function getBasis(){
		$model = D ('MisHrBasicEmployee');
		$map['id'] = $_REQUEST['id'];
		if ($_SESSION["a"] != 1) $map['status'] = 1;
		$vo = $model->where($map)->find();
		if(empty($vo)){
			$this->display ("Public:404");
			exit;
		}
		$vo['age'] = $this->getAgeByToCarId($vo['chinaid'],false)+1;
		$this->assign("vo",$vo);
		//$this->display("MisHrPersonnelVoView:basisview");
	}
	protected  function getuserdept(){
		//获取公司
		$UserDeptDutyModel=D("UserDeptDuty");
		$UserDeptDutyList=$UserDeptDutyModel->where(" typeid=1 and  status=1 and employeid=".$_REQUEST['id'])->select();
		$this->assign("list",$UserDeptDutyList);
		//$this->display("MisHrPersonnelVoView:userdept");
	}
	/**
	 * @Title: getEdu
	 * @Description: todo(获取教育信息)
	 * @author 杨东
	 * @date 2013-7-19 上午9:36:35
	 * @throws
	 */
	protected function getEdu(){
		$name = "MisHrPersonnelEducationInfo";
		$map['personinfoid'] = $_REQUEST['id'];
		$map['status'] = 1;
		$this->_list ( $name, $map );
		$this->display("MisHrPersonnelVoView:eduview");
	}

	/**
	 * @Title: getWork
	 * @Description: todo(工作经历)
	 * @author 杨东
	 * @date 2013-7-19 上午10:35:08
	 * @throws
	 */
	protected function getWork(){
		$name = "MisHrPersonnelExperienceInfo";
		$map['personinfoid'] = $_REQUEST['id'];
		$map['status'] = 1;
		$this->_list ( $name, $map );
		$this->display("MisHrPersonnelVoView:workview");
	}

	/**
	 * @Title: getPerson
	 * @Description: todo(获取关联人)
	 * @author 杨东
	 * @date 2013-7-19 上午11:05:55
	 * @throws
	 */
	protected function getPerson(){
		$name = "MisHrEmployeePrivy";
		$map['employeeid'] = $_REQUEST['id'];
		$map['status'] = 1;
		$list = D($name)->where($map)->select();
		$familyList = $introducerList = array();
		foreach ($list as $k => $v) {
			if($v['privytype'] == 0){
				$familyList[] = $v;
			} else if($v['privytype'] == 1){
				$introducerList[] = $v;
			}
		}
		$this->assign("familyList",$familyList);
		$this->assign("introducerList",$introducerList);
		$this->display("MisHrPersonnelVoView:personview");
	}
	/**
	 * @Title: getTrain
	 * @Description: todo(培训记录)
	 * @author 杨东
	 * @date 2013-7-19 下午3:09:25
	 * @throws
	 */
	protected function getTrain(){
		$MisHrPersonnelTrainManageRelationModel=D('MisHrPersonnelTrainManageRelation');
		$mMisHrEvaluationTrainModel=D('MisHrEvaluationTrain');
		$MisHrPersonnelTrainManageRelationList=$MisHrPersonnelTrainManageRelationModel->where(" status=1 and personid=".$_REQUEST['id'])->field("personid,trainid")->select();
		$arrTrainId = array();
		foreach($MisHrPersonnelTrainManageRelationList as $key=>$val){
			if(!$arrTrainId){
				$arrTrainId =$val['trainid'];
			}else{
				$arrTrainId =$arrTrainId.",".$val['trainid'];
			}
		}
		// 		$a=array_unique(explode(",", $arr)); //去除重复
		$trainMap['status']=1;
		$trainMap['id']=array("in",$arrTrainId);
		$aMisHrEvaluationTrainList=$mMisHrEvaluationTrainModel->where($trainMap)->select();
		$this->assign("list",$aMisHrEvaluationTrainList);
		$this->display("MisHrPersonnelVoView:trainview");
	}
	/**
	 * @Title: getIssue
	 * @Description: todo(装备发放记录)
	 * @author renling
	 * @date 2013-9-30 下午2:21:16
	 * @throws
	 */
	protected function getIssue(){
		$name = "MisEquipmentIssue";
		$map['status'] = 1;
		$map['employeeid'] = $_REQUEST['id'];
		$this->_list ( $name, $map );
		$this->display("MisHrPersonnelVoView:issueview");
	}
	/**
	 *
	 * @Title: getapplication
	 * @Description: todo(装备申请发放记录)
	 * @author renling
	 * @date 2014-4-2 下午1:36:57
	 * @throws
	 */
	protected function getapplication(){
		$name = "MisEquipmentApplication";
		$map['status'] = 1;
		$map['issue'] = 1; //已发
		$map['employeeid'] = $_REQUEST['id'];
		$this->_list ( $name, $map );
		$this->display("MisHrPersonnelVoView:applicationview");
	}
	/**
	 * @Title: getDispatching
	 * @Description: todo(派工记录)
	 * @author renling
	 * @date 2013-10-24 下午5:34:17
	 * @throws
	 */
	protected function getDispatching(){
		$detailMap['mis_hr_personnel_person_info.id']=$_REQUEST['id'];
		$this->_list ('MisBusinessDispatchingManageView', $detailMap );
		$this->display("MisHrPersonnelVoView:dispatchingview");
	}
	/**
	 * @Title: getTransfer
	 * @Description: todo(调动记录)
	 * @author 杨东
	 * @date 2013-7-19 下午3:09:42
	 * @throws
	 */
	protected function getTransfer(){
		$name = "MisHrPersonnelTransferManage";
		$map['personid'] = $_REQUEST['id'];
		$map['status'] = 1;
		$this->_list ( $name, $map );
		$this->display("MisHrPersonnelVoView:transferview");
	}
	/**
	 * @Title: getHurt
	 * @Description: todo(工伤记录)
	 * @author 杨东
	 * @date 2013-7-19 下午3:09:42
	 * @throws
	 */
	protected function getHurt(){
		$name = "MisHrPersonnelIndustrialInjuryInfo";
		$map['personid'] = $_REQUEST['id'];
		$map['status'] = 1;
		$this->_list ( $name, $map );
		$this->display("MisHrPersonnelVoView:hurtview");
	}
	/**
	 * @Title: getLeaveEmployee
	 * @Description: todo(离职信息)
	 * @author laicaixia
	 * @date 2013-7-23 下午5:23:09
	 * @throws
	 */
	protected function getLeaveEmployee(){
		$name = "MisHrPersonnelLeave";
		$map['employeeid'] = $_REQUEST['id'];
		$map['status'] = 1;
		$this->_list ( $name, $map );
		$this->display("MisHrPersonnelVoView:leaveemployeeview");
	}
	/**
	 * @Title: gethrpEmployeeLeave
	 * @Description: todo(请假记录)
	 * @author laicaixia
	 * @date 2013-7-23 下午5:23:31
	 * @throws
	 */
	protected function getEmployeeLeave(){
		$name = "MisHrEmployeeLeaveManagement";
		$map['personid'] = $_REQUEST['id'];
		$map['status'] = 1;
		$this->_list ( $name, $map );
		$this->display("MisHrPersonnelVoView:employeeleaveview");
	}
	/**
	 * @Title: getEmployeeContract
	 * @Description: todo(合同记录)
	 * @author laicaixia
	 * @date 2013-8-30 下午11:23:31
	 * @throws
	 */
	protected function getEmployeeContract(){
		$name = "MisHrBasicEmployeeContract";
		$map['employeeid'] = $_REQUEST['id'];
		$map['status'] = 1;
		$this->_list ( $name, $map );
		$this->display("MisHrPersonnelVoView:employeecontractview");
	}

	/**
	 * @Title: getLevelName
	 * @Description: todo(获取绩效等级名称)
	 * @param 等级数据 $levelList
	 * @param 被考核人总分 $totalscore
	 * @param 被考核人修正总分 $amendscore
	 * @param 是否被修正 $isamend
	 * @return 绩效等级名称
	 * @author 杨东
	 * @date 2013-8-14 下午3:21:19
	 * @throws
	 */
	protected function getLevelName($levelList,$vo,$levelid=0){
		if(!$levelid){
			$model = D("MisPerformancePlan");
			$levelid = $model->where("id=".$vo['planid'])->getField('levelid');
		}
		if($vo["isamend"] == 1){
			$score = $vo['amendscore'];
		}else{
			$score = $vo['totalscore'];
		}
		foreach ($levelList as $k => $v) {
			if($score > $v['startresult'] && $score < $v['endresult'] && $v['typeid'] == $levelid){
				return $v['name'];
			}
		}
	}

	/**
	 * @Title: getPerformanceAssess
	 * @Description: todo(绩效考评)
	 * @author laicaixia
	 * @date 2013-7-23 下午5:23:31
	 * @throws
	 */
	protected function getPerformanceAssess(){
		// 获取绩效等级数据
		$plmodel = D("MisPerformanceLevel");
		$pllist = $plmodel->where("status=1")->select();
		// 分页
		if (isset ( $_REQUEST ['orderField'] )) {
			$order = $_REQUEST ['orderField'];
		} else {
			$order = ! empty ( $sortBy ) ? $sortBy : "mis_performance_assess.id";
		}
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['orderDirection'] )) {
			$sort = $_REQUEST ['orderDirection'];
		} else {
			$sort = 'desc';
		}
		// 绩效记录
		$pavmodel = D("MisPerformanceAssessView");
		$pavmap['mis_performance_assess.byuser'] = $_REQUEST['id'];//设置选中人员为过滤条件
		$pavmap['mis_performance_assess.status'] = 1;
		if($_POST['planid']) {
			// 判断是否选中计划
			$pavmap['mis_performance_assess.planid'] = $_POST['planid'];
			$this->assign("planid",$_POST['planid']);
		}
		$count = $pavmodel->where ($pavmap)->count ( '*' );
		if ($count > 0) {
			import ( "@.ORG.Page" );
			//创建分页对象
			$numPerPage=C('PAGE_LISTROWS');
			$dwznumPerPage=C('PAGE_DWZLISTROWS');
			if (! empty ( $_REQUEST ['numPerPage'] )) {
				$numPerPage = $_REQUEST ['numPerPage'];
			}
			if( $_POST["dwzpageNum"]=="") $dwznumPerPage = $numPerPage;
			$p = new Page ( $count, $numPerPage,'',$dwznumPerPage );
			//分页查询数据
			if($_POST['dwzloadhtml']) $p->firstRow = $p->firstRow + (intval($_POST['dwzpageNum'])-1)*$numPerPage;
			$voList = $pavmodel->where($pavmap)->order( "`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select();
			foreach ($voList as $k => $v) {
				// 获取总分
				$score = $v['totalscore'];
				// 如果有修正取修正总分
				if($v['isamend']) $score = $v['amendscore'];
				$voList[$k]['score'] = $score;// 设置总分
				$voList[$k]['level'] = $this->getLevelName($pllist, $v, $vo['levelid']);//设置等级中文
			}

			$page = $p->show ();
			//列表排序显示
			$sortImg = $sort; //排序图标
			$sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
			$sort = $sort == 'desc' ? 'desc' : 'asc'; //排序方式
			$pageNum= !empty($_REQUEST[C('VAR_PAGE')])?$_REQUEST[C('VAR_PAGE')]:1;
			//模板赋值显示
			$this->assign ( 'pageNum', $pageNum );
			$this->assign ( 'list', $voList );
			$this->assign ( "page", $page );
		}
		$this->assign ( 'dwztotalpage',C('PAGE_DWZLISTROWS')/$numPerPage );
		$this->assign ( 'sort', $sort );
		$this->assign ( 'order', $order );
		$this->assign ( 'sortImg', $sortImg );
		$this->assign ( 'sortType', $sortAlt );
		$this->assign ( 'totalCount', $count );
		$this->assign ( 'numPerPage', $numPerPage);
		$this->assign ( 'dwznumPerPage', C('PAGE_DWZLISTROWS'));
		$this->assign ( 'currentPage', !empty($_REQUEST[C('VAR_PAGE')])?$_REQUEST[C('VAR_PAGE')]:1);
		Cookie::set ( '_currentUrl_', __SELF__ );



		$pavlist = $pavmodel->where($pavmap)->select();//查询单个人员绩效数据
		foreach ($pavlist as $k => $v) {
			// 获取总分
			$score = $v['totalscore'];
			// 如果有修正取修正总分
			if($v['isamend']) $score = $v['amendscore'];
			$pavlist[$k]['score'] = $score;// 设置总分
			$pavlist[$k]['level'] = $this->getLevelName($pllist, $v, $vo['levelid']);//设置等级中文
		}
		$this->assign("list",$pavlist);
		$this->display("MisHrPersonnelVoView:PerformanceAssessview");
	}
	public function lookupissuesubview(){
		$typedetails=getFieldBy($_REQUEST['id'],'id', 'typedetails', $_REQUEST['model']);
		$manageidarr= unserialize($typedetails);
		$this->assign("misEquipmentManagementlist",$manageidarr);
		$this->display();
	}
	/**
	 * @Title: comboxrefreshIntomas
	 * @Description: todo(获取岗位信息)
	 * @author liminggang
	 * @date 2013-5-31 下午5:28:54
	 * @throws
	 */
	public function comboxrefreshIntomas(){
		//赋值数据表模型
		$MisHrjJobInfomodel=M("mis_hr_job_info");
		//获得部门ID
		$deptid = $_POST['deptid'];
		$where = array();
		$where['status'] = 1;
		$where['deptid'] = $deptid;
		$joblist=$MisHrjJobInfomodel->where($where)->getField("id,name");
		//给模板赋值
		$arr = array();
		if($joblist){
			foreach($joblist as $k=>$v){
				$arr2=array();
				$arr2[]=$k;
				$arr2[]=$v;
				array_push($arr,$arr2);
			}
		}
		echo  json_encode($arr);
	}
	public function _before_add(){
		//带入公司部门id
		$this->assign('companyid',$_GET['companyid']);
		$this->assign('ptId',$_GET['ptId']);
// 		//获取公司部门联动 下拉插件所需代码
// 		$deptanComList=D('MisSystemDepartment')->getDeptCombox();
// 		$this->assign("deptanComList",$deptanComList);
		//获取初始密码
		$pwd=C('USER_DEFAULT_PASSWORD');
		$this->assign("pwd",$pwd);
		// 		//查询岗位类别
		// 		if($_REQUEST["id"]){ //普通人员招聘信息点击办理入职
		// 			$MisHrInvitereFormModel=D("MisHrInvitereForm");
		// 			$MisHrInvitereFormList=$MisHrInvitereFormModel->where(" status=1 and  id=".$_REQUEST["id"])->find();
		// 			$this->assign("MisHrInvitereFormList",$MisHrInvitereFormList);
		// 		}
		// 		if($_REQUEST['InvitereSpecialId']){ //特殊人员招聘信息点击办理入职
		// 			$MisHrInvitereSpecialFormModel=D("MisHrInvitereSpecialForm");
		// 			$MisHrInvitereSpecialFormList=$MisHrInvitereSpecialFormModel->where(" status=1 and  id=".$_REQUEST["InvitereSpecialId"])->find();
		// 			$this->assign("MisHrInvitereSpecialFormList",$MisHrInvitereSpecialFormList);
		// 		}
		//当前时间
		$now=time();
		$this->assign("now",$now);
		//订单号可写
		$scnmodel = D('SystemConfigNumber');
		$writable= $scnmodel->GetWritable('mis_hr_personnel_person_info');
		$this->assign("writable",$writable);
		//自动生成订单编号
		$orderno = $scnmodel->GetRulesNO('mis_hr_personnel_person_info');
		$this->assign("orderno",$orderno);
		$this->lookupcdw();
		// 		//查询职位
		// 		$DutyModel=D('Duty');
		// 		$DutyList=$DutyModel->where(" status=1")->getField('id,name');
		// 		$this->assign("DutyList",$DutyList);
		// 		//查询学历
		// 		$typeinfoModel=D('Typeinfo');
		// 		$typeinfoList=$typeinfoModel->where(" status=1  and  pid=44")->getField("id,name");
		// 		$this->assign("typeinfoList",$typeinfoList);
	}
	public function lookupcdw(){
		$type=$_POST['type'];//$type为公司信息联动 ajax传值
		//所属公司信息
		$companyid = $_GET['companyid'];//初始带入公司id
		$MisSystemCompanyDAO=M('mis_system_company');
		$companylist=$MisSystemCompanyDAO->where('status = 1')->order("orderno")->field('id,name')->select();
		//查询公司的部门信息
		$MisSystemDepartmentDAO=M('mis_system_department');
		$deptmap=array();
		if($type==1){
			$deptmap['companyid']=$_POST['val'];
		}else{
			$deptmap['companyid']=(int)$companyid>0?$companyid:$companylist[0]['id'];
		}
		$rootVal=getFieldBy($deptmap['companyid'], 'companyid', 'id', 'mis_system_department','iscompany',1);
// 		$deptmap['status']=1;
// 		$deptmap['_string']="iscompany!=1";
		$model=D('MisSystemRecursion');
		$MisSystemDepartmentList=$model->modelShow('MisSystemDepartment',array('key'=>'id','pkey'=>'parentid','fields'=>"id,name",'pkeyVal'=>$rootVal,'conditions'=>"status=1 and companyid=".$deptmap['companyid']." AND iscompany != 1"),0,1);
		//dump($MisSystemDepartmentList);
		//初始化部门id 分为带入id 当未带入时选择第一个出现的末级部门id
		$ondedptid="";
		if(empty($type)){
			foreach ($MisSystemDepartmentList as $key=>$val){				
				if($_GET['deptid']==$val['id']&&$val['nextEnd']==1){
					$ondedptid=$val['id'];
				}
			}
			if(empty($ondedptid)){
				foreach ($MisSystemDepartmentList as $key=>$val){
					if($val['nextEnd']==1){
						$ondedptid=$val['id'];
						break ;
					}
				}
			}
		}else{
			foreach ($MisSystemDepartmentList as $key=>$val){
				if($val['nextEnd']==1){
					$ondedptid=$val['id'];
					break ;
				}
			}
		}
		$this->assign('deptid',$ondedptid);	
		//$MisSystemDepartmentList=$MisSystemDepartmentDAO->where($deptmap)->getField("id,name");
// 		$MisSystemDepartmentKeys=array_keys($MisSystemDepartmentList['id']);
		//查询岗位信息
		$MisHrJobInfoModel=D('MisHrJobInfo');
		$jobmap=array();
		$jobmap['companyid']=$_REQUEST['companyid']?$_REQUEST['companyid']:$companylist[0]['id'];
		//部门id 当联动ajax传值时为$_POST['val']
		if($type==1){
			$jobmap['deptid']=$ondedptid;
		}else{
			$jobmap['deptid']=$type==2?$_POST['val']:$ondedptid;
		}
		$jobmap['status']=1;
		$MisHrJobInfoList=$MisHrJobInfoModel->where($jobmap)->getField("id,name");
// 		file_put_contents('a.txt',$a);
		$list['dept']=$MisSystemDepartmentList;
		$list['job']=$MisHrJobInfoList;
		if($type==1){
			echo json_encode($list);
		}else if($type==2){
			echo json_encode($MisHrJobInfoList);
		}else{
			$this->assign('companylist',$companylist);
			$this->assign("MisSystemDepartmentList",$MisSystemDepartmentList);
			$this->assign("MisHrJobInfoList",$MisHrJobInfoList);
		}
		
	}
	public  function insert() {
		if(!$_POST['companyid']){
			$this->error("数据提交不完整！");
		}
		foreach($_POST['deptid'] as $k=>$v){
			if($v<=0) $this->error('入职部门漏选，请选择入职部门！');
		}
		 
		foreach($_POST['dutylevelid'] as $k=>$v){
			if($v<=0) $this->error('企业职级漏选，请选择企业职级！');
		} 
		//B('FilterString');
		$boolReasult=$this->idcard_checksum18($_POST['chinaid']);
		//身份证验证
		if($boolReasult==false){
			$this->error ('身份证格式有误,请输入正确的身份证号码！');
		}
		$name=$this->getActionName();
		$model = D ($name);
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		$list=$model->add ();
		if ($list!==false) {
			$mrdmodel = D('MisRuntimeData');
			$mrdmodel->setRuntimeCache($_POST,$name,'add');
			$module2=A($name);
			if (method_exists($module2,"_after_insert")) {
				call_user_func(array(&$module2,"_after_insert"),$list);
			}
			$this->success ( L('_SUCCESS_') ,'',$list);
		} else {
			$this->error ( L('_ERROR_') );
		}
	}
	public function _before_lookupadduser(){
		$id=$_REQUEST['id'];
		$UserModel=D('User');
		$userMap['zhname']=getFieldBy($id, 'id', 'name', 'mis_hr_personnel_person_info');
		$userMap['status']=1;
		$userResult=$UserModel->where($userMap)->count();
		if($userResult){
			$name=getFieldBy($id, 'id', 'name', 'mis_hr_personnel_person_info').intval($userResult+1);
		}else{
			$name=getFieldBy($id, 'id', 'name', 'mis_hr_personnel_person_info');
		}
		$pwd=C('USER_DEFAULT_PASSWORD');
		$phone=getFieldBy($id, 'id', 'phone', 'mis_hr_personnel_person_info');
		$this->assign("phone",$phone);
		$this->assign("name",$name);
		$this->assign("pwd",$pwd);
		$this->assign("id",$id);
	}
	public function lookupcheckuser(){
		$UserModel=D('User');
		$userMap[$_POST['name']]=$_POST['val'];
		$userMap['status']=1;
		$userResult=$UserModel->where($userMap)->count();
		echo $userResult;
	}
	/**
	 * @Title: _after_insert
	 * @Description: todo(添加介绍人信息)
	 * @param int $id 后置函数获得执行成功的主键值
	 * @author renling
	 * @date 2013-7-17 下午3:56:42
	 * @throws
	 */
	public function _after_insert($id){
		cookie::set("checkcompanyid", $_REQUEST['checkcompanyid']);
		//是否选中后台用户
		$this->addUser($id);
		if($_POST['isaddUser']!=1){
		$companyid=$_POST['companyid'];
		$UserDeptDutyModel=D('UserDeptDuty');
		$companyList=array();
		foreach ($companyid as $key=>$val){
			if(in_array($val,array_keys($companyList))){
				$this->error(getFieldBy($val,"id","name","mis_system_company")."添加重复,请查证后提交！");
			}
			$UserDeptDuty=array();
			$UserDeptDuty['deptid']=$_POST['deptid'][$key];
			$UserDeptDuty['dutyid']=$_POST['dutylevelid'][$key];
			$UserDeptDuty['typeid']=1;
			$UserDeptDuty['worktype']=$_POST['worktype'][$key];
			$UserDeptDuty['employeid']=$id;
			$UserDeptDuty['typeid']=1;
			$UserDeptDuty['companyid']=$_POST['companyid'][$key];
			$UserDeptDuty['createid']=$_SESSION[C('USER_AUTH_KEY')];
			$UserDeptDuty['createtime']=time();
			$UserDeptUserResult=$UserDeptDutyModel->add($UserDeptDuty);
			$UserDeptDutyModel->commit();
			$companyList[$val]=1;
			if(!$UserDeptUserResult){
				$this->error("数据异常");
			}
			}
		}
		//数据源从普通招聘信息带过来, 反写回招聘信息。 是否已办理入职为已办理
		if($_REQUEST['invitereId']){
			$MisHrInvitereFormModel=D("MisHrInvitereForm");
			$MisHrInvitereFormResult=$MisHrInvitereFormModel->where("id=".$_REQUEST['invitereId'])->setField("isinjob",1);
		}
		//数据源从特殊招聘信息带过来, 反写回招聘信息。 是否已办理入职为已办理
		if($_REQUEST['invitereSpecialId']){
			$MisHrInvitereSpecialFormModel=D("MisHrInvitereSpecialForm");
			$MisHrInvitereSpecialFormModel=$MisHrInvitereSpecialFormModel->where("id=".$_REQUEST['invitereSpecialId'])->setField("isinjob",0);
		}
		//人事关系模型
		$mfbsModel = D ("MisHrEmployeePrivy");
		//获取到要插入数据
		if($_REQUEST['privyname']){ //工作经验
			foreach($_REQUEST["privyname"] as $key=>$value){
				$date["employeeid"]=$id;
				$date["privytype"]=1;//介绍人
				if($_REQUEST['privyemid']){
					$date["privyemid"]=$_REQUEST["privyemid"][$key];//关联员工ID
				}
				$date["privyname"]=$_REQUEST["privyname"][$key];//姓名
				$date["relation"]=$_REQUEST["privyrelation"][$key];//关系
				$date["privytel"]=$_REQUEST["privytelephone"][$key];//联系电话
				// 				$date['remark']=$_REQUEST['remark'][$key];
				//保存当前数据对象
				$list=$mfbsModel->data($date)->add ();
				if ($list==false) {
					$this->error ( L('_ERROR_') );
				}
			}
		}
		if($_REQUEST['startdate']){ //工作经验
			$MisHrPersonnelExperienceInfoModel=D('MisHrPersonnelExperienceInfo');
			foreach($_REQUEST["startdate"] as $key=>$value){
				$Expdate["personinfoid"]=$id; //基础表ID
				$Expdate["company"]=$_REQUEST['company'][$key];//公司
				$Expdate["position"]=$_REQUEST["position"][$key];//职业
				$Expdate["remark"]=$_REQUEST["expremark"][$key];//备注
				$Expdate["startdate"]=strtotime($_REQUEST["startdate"][$key]);//备注
				$Expdate["finishdate"]=strtotime($_REQUEST["finishdate"][$key]);//备注
				//保存当前数据对象
				$list=$MisHrPersonnelExperienceInfoModel->data($Expdate)->add();
				if ($list==false) {
					$this->error ( L('_ERROR_') );
				}
			}
		}
		if($_REQUEST['edustartdate']){ //教育经历
			$MisHrPersonnelEducationInfoModel=D('MisHrPersonnelEducationInfo');
			foreach($_REQUEST["edustartdate"] as $key=>$value){
				$edudate["personinfoid"]=$id; //基础表ID
				$edudate["startDate"]=strtotime($_REQUEST['edustartdate'][$key]);//开始时间
				$edudate["finishDate"]=strtotime($_REQUEST['edufinishdate'][$key]);//结束时间
				$edudate["school"]=$_REQUEST['newschool'][$key];//学校机构
				$edudate["skillAndCertificate"]=$_REQUEST["skillAndCertificate"][$key];//专业与技能
				//保存当前数据对象
				$list=$MisHrPersonnelEducationInfoModel->data($edudate)->add ();
				if ($list==false) {
					$this->error ( L('_ERROR_') );
				}
			}
		}
		if($_REQUEST['relation']){ //家庭成员
			$MisHrPersonnelFamilyInfoModel=D('MisHrEmployeePrivy');
			foreach($_REQUEST["relation"] as $key=>$value){
				$Familydata["employeeid"]=$id; //基础表ID
				$Familydata["relation"]=$_REQUEST['relation'][$key];//关系
				$Familydata["privyname"]=$_REQUEST['familyname'][$key];//姓名
				$Familydata["privycompany"]=$_REQUEST['familycompany'][$key];//工作单位
				$Familydata["privytel"]=$_REQUEST['telephone'][$key];//联系电话
				$Familydata["skillAndCertificate"]=$_REQUEST["skillAndCertificate"][$key];//专业与技能
				//保存当前数据对象
				$list=$MisHrPersonnelFamilyInfoModel->data($Familydata)->add ();
				if ($list==false) {
					$this->error ( L('_ERROR_') );
				}
			}
		}
		//name字段拼音生成
		$this->getPinyin('mis_hr_personnel_person_info','name');
		$this->success('操作成功');
	}
}