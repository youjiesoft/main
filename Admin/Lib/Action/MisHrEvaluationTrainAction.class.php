<?php
/**
 * @Title: file_name
 * @Package package_name
 * @Description: todo(培训课程)
 * @author liminggang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-3-27 上午10:59:50
 * @version V1.0
 */
class MisHrEvaluationTrainAction extends CommonAction {
	/**
	 * @Title: _filter 
	 * @Description: todo(构造检索条件) 
	 * @param unknown_type $map  
	 * @author xiafengqin 
	 * @date 2013-6-1 下午3:32:42 
	 * @throws
	 */
	public function _filter(&$map){
		if ($_SESSION["a"] != 1){
			$map['status']=array("gt",-1);
		}
	}
			
	/* (non-PHPdoc) 显示列表
	 * @see CommonAction::index()								
	*/
	function index(){
		//---------------------------split-----------------------------------
		$map=$this->_search();
			
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		
		//动态配置显示字段
		$name=$this->getActionName();
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($name);
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}

		//searchby搜索扩展
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
			
		//扩展工具栏操作
		$toolbarextension = $scdmodel->getDetail("MisHrEvaluationTrain",true,'toolbar');
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}		
		//---------------------------split-----------------------------------
		
		//管理员跳转到订课模式，分页时保留条件
		$adminOrderCourses = $_REQUEST['adminOrderCourses'];
		
		//物殊判断，用户在一个特定角色中，就跳转管理员权限页面
		$userID= $_SESSION[C('USER_AUTH_KEY')];
		$courseAdmin = $this->isRoleMember($userID,'人事-培训管理');					//人事管理权限
		$courseDeptAdmin = $this->isRoleMember($userID,'人事-部门培训课程');	 		//部门管理权限
		$courseProjectAdmin = $this->isRoleMember($userID,'人事-项目培训管理');		//人事-项目培训管理
		
		//筛选不同分类的结果显示出来
		$findStatusID = $_REQUEST['classsource'];
		switch($findStatusID){
			case 1:
				$map['classsource'] = 1;
				$this->assign('classsource', 1); //新增按钮添加一个值传到模板，让所选择的分类被选中
				break;
			case 2:
				$map['classsource'] = 2;
				$this->assign('classsource', 2); //新增按钮添加一个值传到模板，让所选择的分类被选中
				break;
			case 3:
				$map['classsource'] = 3;
				$this->assign('classsource', 3); //新增按钮添加一个值传到模板，让所选择的分类被选中
				break;
			case 4:
				$map['classsource'] = 4;
				$this->assign('classsource', 4); //新增按钮添加一个值传到模板，让所选择的分类被选中
				break;
		}
		//保留条件
		$this->assign('findKeyValue',$findStatusID);
		
		//第一步，组装左侧树结构，默认为员工列表		
		//手动组装个数组，生成左侧树型结构 
		if($courseDeptAdmin ){
			$coursesTypelist = Array(
			'0'=> Array('id'=>'0','name'=>'课程类型','parentid'=>'0'),
			'2'=> Array('id'=>'2','name'=>'部门课程','parentid'=>'0'),
			);
			
			//控制初入页面显示数据
			if(empty($adminOrderCourses)){
				$map['classsource'] = 2;
				$this->assign('classsource', 2); //新增按钮添加一个值传到模板，让所选择的分类被选中			
			}			
		}elseif($courseProjectAdmin){
			$coursesTypelist = Array(
			'0'=> Array('id'=>'0','name'=>'课程类型','parentid'=>'0'),
			'4'=> Array('id'=>'4','name'=>'项目课程','parentid'=>'0'),
			);
			
			//控制初入页面显示数据
			//控制初入页面显示数据
			if(empty($adminOrderCourses)){
				$map['classsource'] = 4;
				$this->assign('classsource', 4); //新增按钮添加一个值传到模板，让所选择的分类被选中
			}
		}else{
			$coursesTypelist = Array(
			'0'=> Array('id'=>'0','name'=>'课程类型','parentid'=>'0'),
			'1'=> Array('id'=>'1','name'=>'公共课程','parentid'=>'0'),
			'2'=> Array('id'=>'2','name'=>'部门课程','parentid'=>'0'),
			'3'=> Array('id'=>'3','name'=>'外派课程','parentid'=>'0'),
			'4'=> Array('id'=>'4','name'=>'项目课程','parentid'=>'0'),
			);
		}
		
		$typeTree = $this->getZtreelist($coursesTypelist);

		$this->assign('typetree',$typeTree);
		
		//未审核的，课程不显示
		if($adminOrderCourses){
		 	$map['status']=array("gt",0);
		}	
				
		//去_list方法中把数据取出来
		if (! empty ( $name )) {
			$qx_name=$name;
			if(substr($name, -4)=="View"){
				$qx_name = substr($name,0, -4);
			}

			//验证浏览及权限
			if( !isset($_SESSION['a']) ){
				$map=D('User')->getAccessfilter($qx_name,$map);	
			}
			$this->_list ($name, $map );
		}

		if( intval($_POST['dwzloadhtml']) ){
			$this->display("dwzloadindex");exit;
		}
		
		
		//读出当前用户，所订过的课程 //人员选课单和课程关联表
		$MisHrPersonnelTrainManageRelationModel=D('MisHrPersonnelTrainManageRelationView');
		$mapCourses['mis_hr_personnel_train_manage_relation.personid'] = $userID;
		$mapCourses['mis_hr_personnel_train_manage_relation.status'] = 1;
				
		$coursesList = $MisHrPersonnelTrainManageRelationModel->where($mapCourses)->select(); //取出当前用户所有的课程列表
		$this->assign('coursesList',$coursesList);
		//echo $courseAdmin.'-'.$courseDeptAdmin.'-'.$courseProjectAdmin.'-'.$_SESSION["a"];
		//模板显示选择
		if($courseAdmin || $courseDeptAdmin || $courseProjectAdmin || $_SESSION["a"]){
			//指定区域显示模板
			if ($_REQUEST['jump'] ) {
				if(!empty($_REQUEST['adminOrderCourses'])){		//adminOrderCourses 管理员定课分页问题
					$this->assign('adminOrderCourses',1);
					$this->display('subwebindex');				//订课web显示页面
				}else{
					$this->display('right'); 					//课程管理员页面 EXCEL行样式
				}
			} else {
				$this->display();								//课程管理员页面
			}				
		}else{		
			//指定区域显示模板
			if ($_REQUEST['jump'] ) {
				$this->display('subwebindex'); 					//普通用户订课web显示明细页面
			} else {
				$this->display('webindex');						//普通用户订课web显示引导页面
			}	
		}

	}	

	/**
	 * 构造部门树形
	 * @param array  $alldata  所有部门信息
	 * @param int $pid  部门节点ID
	 * @param int $i  节点等级
	 * @return string
	 */
	private function getZtreelist($alldata){
		$returnarr=array();
		foreach($alldata as $k=>$v){
			$newv=array();
			$newv['id']=$v['id'];
			$newv['pId']=$v['parentid'];
			$newv['name']=$v['name'];
			$newv['title']=$v['name'];
			$newv['type']='post';
			if($v['id']==0){
				$newv['url']='__URL__/index/jump/1/classsource/';
			}else{
				$newv['url']='__URL__/index/jump/1/classsource/'.$v['id'];
			}
			$newv['target']='ajax';
			$newv['rel']='mishrevaluationtrainmodel';
			$newv['open']='true';
			array_push($returnarr,$newv);
		}
		return json_encode($returnarr);
	}
	
	/**
	 * @Title: _before_add 
	 * @Description: todo(打开新增页面前置函数)   
	 * @author xiafengqin 
	 * @date 2013-6-1 下午3:33:00 
	 * @throws
	 */
	public function _before_add(){
		//自动生成订单
		$scnmodel = D('SystemConfigNumber');
		$orderno = $scnmodel->GetRulesNO('mis_hr_evaluation_train');
		$this->assign("orderno", $orderno);
		
		//订单编号可写
		$writable= $scnmodel->GetWritable('mis_hr_evaluation_train');
		$this->assign("writable",$writable);
		

		//查询所有部门信息
		$deptmodel = D("MisSystemDepartment");
		$deptlist=$deptmodel->where("status=1")->select();
		
		//selectTree做用是生成树型结构的下接选择
		$deptlists=$this->selectTree($deptlist);
		$this->assign("deptlist",$deptlists);

		//部门ID，装表单中的部门默认选种
		$classsource =$_REQUEST['classsource'];
		$this->assign('classsource',$classsource);	
		//获取当前时间
		$time=transTime(time(),'Y');
		$this->assign("time",$time);	
	}
	
	/**
	 * @Title: attlist 
	 * @Description: todo(上传附件列表)   
	 * @author eagle 
	 * @date 2013-5-31 下午5:33:06 
	 * @throws
	 */
	private function attlist(){
		$map["status"]  =1;
		$map["orderid"] =$_REQUEST["id"];
		$map["type"] =74;
		$m=M("mis_attached_record");
		$attarry=$m->where($map)->select();
		$this->assign('attarry',$attarry);
	}	
	
	/**
	 * @Title: _before_edit 
	 * @Description: todo(打开修改页面前置函数)   
	 * @author xiafengqin 
	 * @date 2013-6-1 下午3:33:17 
	 * @throws
	 */
	public function _before_edit(){
		$id = $_REQUEST ['id'];
		$map['id']=$id;
		
		//上传附件的展示清单
		$this->attlist();
		
		//查出当前记录的部门ID
		$modelHRET = D($this->getActionName());
		$modelHRETList = $modelHRET->where($map)->find();
			
		//查询所有部门信息
		$deptmodel = D("MisSystemDepartment");
		$deptlist=$deptmodel->where("status=1")->select();
		
		//selectTree做用是生成树型结构的下接选择
		$deptlists=$this->selectTree($deptlist,0,0,$modelHRETList['departmentid']);
		$this->assign("deptlist",$deptlists);
	}	
	
	/**
	 * @Title: _after_insert 
	 * @Description: todo(插入后置函数) 
	 * @param unknown_type $id  
	 * @author eagle 
	 * @date 2013-5-31 下午5:40:13 
	 * @throws
	 */
	public function _after_insert($id){
		if($id){
			$this->swf_upload($id,74);
		}
	}
	/**
	 * @Title: _after_update 
	 * @Description: todo(修改后置函数)   
	 * @author eagle 
	 * @date 2013-5-31 下午5:41:20 
	 * @throws
	 */
	public function _after_update(){
		if ($_POST['id']) {
			$this->swf_upload($_POST['id'],74);
		}
	}	
	
	/**
	 * @Title: accredit
	 * @Description: todo(课程培训人员选择)
	 * @author liminggang
	 * @date 2013-3-27 上午11:57:59
	 * @throws
	 */
	public function accredit() {
		//获得课程编号
		$trainid=$_GET['id'];
		//根据课程查询相关人员
		$MisHrPersonnelTrainManageRelationModel=D('MisHrPersonnelTrainManageRelation');
		$personlist=$MisHrPersonnelTrainManageRelationModel->where('trainid = '.$trainid.' and status = 1')->getField('id,personid');
		
		//人事基本资料表
		$MisHrPersonnelPersonInfoDAO=M('mis_hr_personnel_person_info');
		unset($map);
		if($personlist){
			$map['id'] = array(' in ',$personlist);
		}
		$map['status'] = 1;   //状态
		$rolelist=$MisHrPersonnelPersonInfoDAO->where($map)->distinct(true)->field('deptid')->select();
		$role = array();
		foreach ($rolelist as $k => $v) {
			$role[] = $v['deptid'];
		}
		$list['role'] = $role;
		//获取部门
		$MisSystemDepartmentModel = D('MisSystemDepartment'); //部门模型
		$deptlist = $MisSystemDepartmentModel->where('status = 1')->getField('id,name');
		foreach ($deptlist as $k => $v) {
			//获取部门下的用户
			unset($map['id']);
			$map['deptid'] = $k;  //部门
			$userlist = $MisHrPersonnelPersonInfoDAO->where($map)->getField('id,name');
			if ($userlist) {
				$str .= "<div id='person$k' class='unit textInput' style='margin-top:5px;width: 100%;'><label>";
				if (in_array($k,$list['role'])) {
					$str .= "<input type='checkbox' class='parentCls' disabled='disabled'  onClick='nowchecked(\"person$k\");' checked='true' />";
				} else {
					$str .= "<input type='checkbox' class='parentCls' onClick='nowchecked(\"person$k\");' />";
				}
				$str .= $v. '：</label><div class="divider"></div><ul class="userul">';
				foreach ($userlist as $k1 => $v1) {
					$str .= '<li>';
					//判断用户选中
					if(in_array($k1,$personlist)){
						$str .= '<input type="checkbox" onclick="subCheck(\'person'.$k.'\');" disabled="disabled" name="userid[]" value="'.$k1.'" checked="true" />';
					}else{
						$str .= '<input type="checkbox" onclick="subCheck(\'person'.$k.'\');" name="userid[]" value="'.$k1.'" />';
					}
					$str .= $v1.'</li>';
				}
				$str .= '</ul></div>';
			}
		}
		$this->assign("str", $str);
		$this->assign("trainid", $trainid);
		$this->display('accredituser');
		exit;
	}
	/**
	 * @Title: setPerson 
	 * @Description: todo(设置课程人员)   
	 * @author liminggang 
	 * @date 2013-4-12 上午10:26:50 
	 * @throws
	 */
	function setPerson(){
		//个人用户订课，管理员订课,如果没有设定值，默认是 1 
		$courses_type = empty($_REQUEST['courses_type'])?$_REQUEST['courses_type']:1;
		//课程ID
		$trainid=$_REQUEST['trainid'];
		//获取课程的基本信息
		$MisHrEvaluationTrainModel = D ($this->getActionName());
		$tranlist=$MisHrEvaluationTrainModel->where('id='.$trainid.' and status = 1')->find();
		
		//获得培训人员
		$useridArray[] = $_SESSION[C('USER_AUTH_KEY')];
		$userid=$_REQUEST['userid']?$_REQUEST['userid']:$useridArray;
		
		//人员选课单和课程关联表
		$MisHrPersonnelTrainManageRelationModel=D('MisHrPersonnelTrainManageRelation');
		
		//人员选课单模型
		$MisHrPersonnelTrainingManageModel=D('MisHrPersonnelTrainingManage');
		$trainmanagelist=$MisHrPersonnelTrainingManageModel->where('status = 1')->getField('id,personid');
		
		foreach($userid as $key=>$val){
			//第一步、先判定是否有关联数据。同样的课。不能插入2次
			unset($map);
			$map['personid'] = $val; //员工ID
			$map['trainid'] = $trainid; //课程ID
			$map['status'] = 1;
			$count=$MisHrPersonnelTrainManageRelationModel->where($map)->count("*");
			if($count==0){
				unset($map);
				//查询是否创建了选课单
				$map['personid'] = $val; //员工ID
				$map['status'] = 1;
				$trainmanagelist=$MisHrPersonnelTrainingManageModel->where($map)->field('id,personid')->find();
				if($trainmanagelist){
					//说明选中的人员中。有已经创建好了的选课单、直接在下面新增课程即可。
					unset($data);
					$data=array(
							'manageid' =>$trainmanagelist['id'], //人员选课单ID
							'trainid' =>$trainid,  //课程ID
							'personid' =>$val,  //员工ID
							'classhour' =>$tranlist['classhour'], //课时数
							'createtime'=>time(),
							'createid' =>$_SESSION[C('USER_AUTH_KEY')],
							'courses_type'=>$courses_type,
					);
					$result=false;
					$result=$MisHrPersonnelTrainManageRelationModel->data($data)->add();
					if(!$result){
						$this->error ( L('_ERROR_') );
					}
				}else{
					//说明人员选课单没有次人、所以需要先创建此人的选课单。在进行数据插入。
					unset($data);
					$scnmodel = D('SystemConfigNumber');
					$orderno = $scnmodel->GetRulesNO('mis_hr_personnel_training_manage');
					$data=array(
							'personid' =>$val, //人员选课单ID
							'orderno' =>$orderno,  //编号
							'createtime'=>time(),
							'createid' =>$_SESSION[C('USER_AUTH_KEY')],
					);
					$result=false;
					$result=$MisHrPersonnelTrainingManageModel->data($data)->add();
					if($result){
						//插入成功后,进行对详情数据的插入。
						unset($data);
						$data=array(
								'manageid' =>$result, //人员选课单ID
								'trainid' =>$trainid,  //课程ID
								'personid' =>$val,  //员工ID
								'classhour' =>$tranlist['classhour'], //课时数
								'createtime'=>time(),
								'createid' =>$_SESSION[C('USER_AUTH_KEY')],
								'courses_type'=>$courses_type,
						);
						$result=false;
						$result=$MisHrPersonnelTrainManageRelationModel->data($data)->add();
						if(!$result){
							$this->error ( L('_ERROR_') );
						}
					}else{
						$this->error ( L('_ERROR_') );
					}
				}
			}
		}
		$this->success ( L('_SUCCESS_') );
	}
	
	/**
	 * @Title: cancelPerson 
	 * @Description: todo(删除)   
	 * @author liminggang 
	 * @date 2013-4-12 上午10:26:50 
	 * @throws
	 */
	function lookupcancelPerson(){
		//课程订单ID
		$result=false;
		$couresid = $_REQUEST['couresid'];
		$map['id']= $couresid;
		//人员选课单和课程关联表
		$MisHrPersonnelTrainManageRelationModel=D('MisHrPersonnelTrainManageRelation');
		$result=$MisHrPersonnelTrainManageRelationModel->where($map)->setField('status','-1');
		//echo $MisHrPersonnelTrainManageRelationModel->getlastSql();
		if($result){
			$this->success ( "退订成功！" );
		}else{
			$this->error ( L('_ERROR_') );
		}
		
	}
	/**
	 * @Title: checkOrderCoures 
	 * @Description: todo(查看定课清单)   
	 * @author liminggang 
	 * @date 2013-4-12 上午10:26:50 
	 * @throws
	 */
	function checkOrderCoursesList(){		
		
		$keyword	=$_REQUEST['keyword'];
		$type		=$_REQUEST['type'];
		$yearDate	=$_REQUEST['yearDate'];
		
		if($keyword){
			$map['name']=array('like','%'.$keyword.'%');
		}
		if($type){
			$map['classsource']=$type;
		}
		if($yearDate){
			$map['year_date']=$yearDate;
		}
		
		//模型
		$name=$this->getActionName();
		
		//去_list方法中把数据取出来
		if (! empty ( $name )) {
			$qx_name=$name;
			if(substr($name, -4)=="View"){
				$qx_name = substr($name,0, -4);
			}

			//验证浏览及权限
			if( !isset($_SESSION['a']) ){
				$map=D('User')->getAccessfilter($qx_name,$map);	
			}
			$this->_list($name, $map);
		}
		$this->display('checkOrderCoursesList');
	}
	
	/**
	 * @Title: showOrderList 
	 * @Description: todo(显示订课名单)   
	 * @author liminggang 
	 * @date 2013-4-12 上午10:26:50 
	 * @throws
	 */
	function showOrderList(){
		$couresid = $_REQUEST['trainid'];	//课程id
		$map['trainid'] =$couresid;
		
		//模型
		$orderCouresModel = D('MisHrPersonnelTrainManageRelation');
		$result=$orderCouresModel->where($map)->select();
		$this->assign('list',$result);
		$this->assign('trainid',$couresid);
		//print_r($result);
		$this->display();
	}
	
	/**
	 * @Title: tagUserDone 
	 * @Description: todo(标记用户已上课) 
	 * @databes's table : MisHrPersonnelTrainManageRelation  
	 * @author liminggang 
	 * @date 2013-4-12 上午10:26:50 
	 * @throws
	 */
	function tagUserDone(){
		$userArray = $_REQUEST['onOrder']; //订课人
		$couresid = $_REQUEST['trainid']; //课程id
		
		//筛选条件
		$map['trainid'] = $couresid;
		
		//模型定义mis_hr_personnel_train_manage_relation
		$orderCouresModel = D('MisHrPersonnelTrainManageRelation');

		//在标记上课人员前把这个课程中所有的人都标记为，待上课  status = 1 
		$resultEmpty= $orderCouresModel->where($map)->setField('status',1);
		//echo $orderCouresModel->getLastSql();
		if(!$resultEmpty){
			$this->error ( L('_ERROR_') );
		}
		//开始标记，选中人员的状态 status = 2；
		foreach($userArray as $key=>$val){
			$map['personid'] = $val; 									//设定哪个人定的课程
			$result= $orderCouresModel->where($map)->setField('status',2);
			//echo $orderCouresModel->getLastSql();
			if(!$result){
				$this->error ( L('_ERROR_') );
			}
		}
		$this->success ( L('_SUCCESS_') );
	}
	
	/**
	 * @Title: myCousesList 
	 * @Description: todo(我的历史课程) 
	 * @databes's table :   
	 * @author eagle 
	 * @date 2013-4-12 上午10:26:50 
	 * @throws
	 */
	function myCoursesList(){
		//dump($_SESSION);
		$userID = $_SESSION[C('USER_AUTH_KEY')];
		unset($map);
		$map['mis_hr_personnel_train_manage_relation.personid'] = $userID;
		$map['mis_hr_personnel_train_manage_relation.status'] = 1;
		//视图模型
		$modelViewName = D("MisHrPersonnelTrainManageRelationView");
		
		$this->_list ('MisHrPersonnelTrainManageRelationView', $map );
		$this->display();
		
	}

	/**
	 * @Title: commitMessageDialog1 
	 * @Description: todo(我的历史课程) 
	 * @databes's table :   
	 * @author eagle 
	 * @date 2013-4-12 上午10:26:50 
	 * @throws
	 */
	function commitMessageDialog(){
		$trainid = $_REQUEST['id']; 			//课程ID
		$userID = $_SESSION[C('USER_AUTH_KEY')];//用户ID
		
		//查询条件
		unset($map);
		$map['trainid'] =$trainid;
		$map['userID'] = $userID;
		
		//判断用户,是不是评论过了，如果评论过后，只能修改不能，新增
		$name="MisHrEvaluationAssess";
		$myModel = D ($name);
		$result = $myModel->where($map)->find();
		//评论显示到模板
		$this->assign('vo',$result);	
		if($result){
			$this->assign('hasResult',1);	//显示ID字段 
		}
		//分配到模板
		$this->assign('trainid',$trainid);
		$this->assign('userid',$userID);

		$this->display();

	}
	
	/**
	 * @Title: commitMessageInsert
	 * @Description: todo(插入函数,更新操作)
	 * @author 
	 * @throws
	 */
	 function commitMessageInsert(){
	 	//dump($_REQUEST);
		$name="MisHrEvaluationAssess";
		$model = D ($name);
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		if($_REQUEST['id']){
			// 更新数据
			$list=$model->save ();
		}else{
			//保存当前数据对象
			$list=$model->add ();
		}

		if ($list!==false) {
			$mrdmodel = D('MisRuntimeData');
			$mrdmodel->setRuntimeCache($_POST,$name,'add');
			$module2=A($name);
			if (method_exists($module2,"_after_insert")) {
				call_user_func(array(&$module2,"_after_insert"),$list);
			}
			$this->success ( L('_SUCCESS_') ,'',$list);
			exit;
		} else {
			$this->error ( L('_ERROR_') );
		}
	}		
	/**
	 * @Title: showCommitMessage
	 * @Description: todo(查看课程所有评论信息)
	 * @author 
	 * @throws
	 */
	public function showCommitMessage(){
		$trainid = $_REQUEST['trainid']; 				//课程id
		$map['trainid'] = $trainid;
		
		//判断用户,是不是评论过了，如果评论过后，只能修改不能，新增
		$name=$this->getActionName();;
		$myModel = D ($name);
		$result = $myModel->where("id=".$trainid)->find();
		
		//评论显示到模板
		$this->assign('oneinfo',$result);	
		
		//统计多少实际上课
		$cMap['status'] = 2;
		$cMap['trainid'] = $trainid;
		$modelName = "MisHrPersonnelTrainManageRelation";  //mis_hr_evaluation_assess
		$modelNameHPTMR = D($modelName);
		$countShangKerRen = $modelNameHPTMR->where ( $map )->count ( '*' );
		$this->assign("countShangKerRen",$countShangKerRen);
			
		//模型
		$modelName = "MisHrEvaluationAssess";  //mis_hr_evaluation_assess
		$this->_list_self($modelName, $map );

		$this->assign('trainid',$trainid);
		$this->display();
	}
	
	//对结果处理一下，某人的分段评分压入$vo
	public function resetList(&$vo){
		foreach($vo as $k=>$v){
			foreach($v as $kk=>$vv){
				switch($kk){
					case z1:
						switch($vv){
							case 1:
								$pa['pa'] += 10*0.15;
								break;
							case 2:
								$pa['pa'] += 8*0.15;
								break;
							case 3:
								$pa['pa'] += 6*0.15;
								break;
							case 4:
								$pa['pa'] += 4*0.15;
								break;
						}
						break;
					case z2:	
						switch($vv){
							case 1:
								$pa['pa'] += 10*0.05;
								break;
							case 2:
								$pa['pa'] += 8*0.05;
								break;
							case 3:
								$pa['pa'] += 6*0.05;
								break;
							case 4:
								$pa['pa'] += 4*0.05;
								break;
						}
						break;
					case z3:	
						switch($vv){
							case 1:
								$pa['pa'] += 10*0.05;
								break;
							case 2:
								$pa['pa'] += 8*0.05;
								break;
							case 3:
								$pa['pa'] += 6*0.05;
								break;
							case 4:
								$pa['pa'] += 4*0.05;
								break;
						}
						break;
					case z4:	
						switch($vv){
							case 1:
								$pa['pa'] += 10*0.1;
								break;
							case 2:
								$pa['pa'] += 8*0.1;
								break;
							case 3:
								$pa['pa'] += 6*0.1;
								break;
							case 4:
								$pa['pa'] += 4*0.1;
								break;
						}
						break;
					case z5:	
						switch($vv){
							case 1:
								$pa['pa'] += 10*0.15;
								break;
							case 2:
								$pa['pa'] += 8*0.15;
								break;
							case 3:
								$pa['pa'] += 6*0.15;
								break;
							case 4:
								$pa['pa'] += 4*0.15;
								break;
						}
						break;
					case z6:	
						switch($vv){
							case 1:
								$pa['pb'] += 10*0.05;
								break;
							case 2:
								$pa['pb'] += 8*0.05;
								break;
							case 3:
								$pa['pb'] += 6*0.05;
								break;
							case 4:
								$pa['pb'] += 4*0.05;
								break;
						}
						break;
					case z7:	
						switch($vv){
							case 1:
								$pa['pb'] += 10*0.15;
								break;
							case 2:
								$pa['pb'] += 8*0.15;
								break;
							case 3:
								$pa['pb'] += 6*0.15;
								break;
							case 4:
								$pa['pb'] += 4*0.15;
								break;
						}
						break;
					case z8:	
						switch($vv){
							case 1:
								$pa['pb'] += 10*0.15;
								break;
							case 2:
								$pa['pb'] += 8*0.15;
								break;
							case 3:
								$pa['pb'] += 6*0.15;
								break;
							case 4:
								$pa['pb'] += 4*0.15;
								break;
						}
						break;
					case z9:	
						switch($vv){
							case 1:
								$pa['pb'] += 10*0.1;
								break;
							case 2:
								$pa['pb'] += 8*0.1;
								break;
							case 3:
								$pa['pb'] += 6*0.1;
								break;
							case 4:
								$pa['pb'] += 4*0.1;
								break;
						}
						break;
					case z10:	
						switch($vv){
							case 1:
								$pa['pb'] += 10*0.05;
								break;
							case 2:
								$pa['pb'] += 8*0.05;
								break;
							case 3:
								$pa['pb'] += 6*0.05;
								break;
							case 4:
								$pa['pb'] += 4*0.05;
								break;
						}
						break;

				}
			}
			$vo[$k]['pa']=$pa['pa'];
			$vo[$k]['pb']=$pa['pb'];
			$vo[$k]['pc']=$vo[$k]['pa']+$vo[$k]['pb'];
			unset($pa['pa']);
			unset($pa['pb']);
			unset($pa['pc']);
			
		}
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
	protected function _list_self($name, $map, $sortBy = '', $asc = false,$group="") {
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
		/* ***************** 修改 ***************** */
		if($_POST['search_flag'] == 1){
			$this->setAdvancedMap($map);
		}
 
		$count = $model->where ( $map )->count ( '*' );
		if($group){
			$count = $model->group($group)->where ( $map )->getField( 'id',true );
			$count = count($count);
		}
		//echo $model->getLastSql();
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
 
			if($_POST['export_bysearch']==1){//如果是导出则无分页
				if($group){
					$voList = $model->group($group)->where($map)->order( "`" . $order . "` " . $sort)->select();
				}else{
					$voList = $model->where($map)->order( "`" . $order . "` " . $sort)->select();
				}
			}else{
				if($group){
					$voList = $model->group($group)->where($map)->order( "`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select();
				}else{
					$voList = $model->where($map)->order( "`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select();
				}
// 				echo $model->getlastsql();
			}
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
			
			//对 $voList进行处理
			$this->resetList($voList);
			
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
	
	/**
	 * @Title: coursesReport
	 * @Description: todo(课程评估报告)
	 * @author 
	 * @throws
	 */
	public function coursesReport($trainid='',$showTpl=''){
		//课程id
		$trainid =empty($trainid)?$_REQUEST['trainid']:$trainid; 				
		$map['id'] = $trainid;

		//取出课程信息
		$name=$this->getActionName();;
		$myModel = D ($name);
		$result = $myModel->where($map)->find();
		$this->assign('oneinfo',$result); 
		
		//查出指定课程所有评论
		unset($map);
		$map['trainid'] = $trainid;
		
		//模型
		$modelName = "MisHrEvaluationAssess";  //mis_hr_evaluation_assess
		$modelObj = D($modelName);
		$dataList = $modelObj->where($map)->field('z1,z2,z3,z4,z5,z6,z7,z8,z9,z10,z11')->select();
		
		//计算评论总人数
		$countPersion = count($dataList);
		$this->assign('diaoChaPersion',$countPersion);
		
		//统计多少实际上课
		$cMap['status'] = 2;
		$cMap['trainid'] = $trainid;
		$modelName = "MisHrPersonnelTrainManageRelation";  //mis_hr_evaluation_assess
		$modelNameHPTMR = D($modelName);
		$countShangKerRen = $modelNameHPTMR->where ( $map )->count ( '*' );
		$this->assign("countShangKerRen",$countShangKerRen);
		
		//计算44个统计格中，有多少人选
		foreach($dataList as $k=>$v){
			foreach($v as $kk=>$vv){
				switch ($kk) {
				    case z1:
				    	switch($vv){
					    	case 1;
					    	$z[$kk][1]++;
					    	 break;
					    	case 2;
					    	$z[$kk][2]++;
					    	 break;
					    	case 3;
					    	$z[$kk][3]++;
					    	 break;
					    	case 4;
					    	$z[$kk][4]++;	
					    	 break;				    		
				    	}
				        break;
				    case z2:
				        switch($vv){
					    	case 1;
					    	$z[$kk][1]++;
					    	 break;
					    	case 2;
					    	$z[$kk][2]++;
					    	 break;
					    	case 3;
					    	$z[$kk][3]++;
					    	 break;
					    	case 4;
					    	$z[$kk][4]++;	
					    	 break;						    		
				    	}
				        break;
				    case z3:
				        switch($vv){
					    	case 1;
					    	$z[$kk][1]++;
					    	 break;
					    	case 2;
					    	$z[$kk][2]++;
					    	 break;
					    	case 3;
					    	$z[$kk][3]++;
					    	 break;
					    	case 4;
					    	$z[$kk][4]++;	
					    	 break;							    		
				    	}
				        break;
				    case z4:
				        switch($vv){
					    	case 1;
					    	$z[$kk][1]++;
					    	 break;
					    	case 2;
					    	$z[$kk][2]++;
					    	 break;
					    	case 3;
					    	$z[$kk][3]++;
					    	 break;
					    	case 4;
					    	$z[$kk][4]++;	
					    	 break;							    		
				    	}
				        break;
				     case z5:
				        switch($vv){
					    	case 1;
					    	$z[$kk][1]++;
					    	 break;
					    	case 2;
					    	$z[$kk][2]++;
					    	 break;
					    	case 3;
					    	$z[$kk][3]++;
					    	 break;
					    	case 4;
					    	$z[$kk][4]++;	
					    	 break;							    		
				    	}
				        break;
				     case z6:
				        switch($vv){
					    	case 1;
					    	$z[$kk][1]++;
					    	 break;
					    	case 2;
					    	$z[$kk][2]++;
					    	 break;
					    	case 3;
					    	$z[$kk][3]++;
					    	 break;
					    	case 4;
					    	$z[$kk][4]++;	
					    	 break;							    		
				    	}
				        break;
				     case z7:
				        switch($vv){
					    	case 1;
					    	$z[$kk][1]++;
					    	 break;
					    	case 2;
					    	$z[$kk][2]++;
					    	 break;
					    	case 3;
					    	$z[$kk][3]++;
					    	 break;
					    	case 4;
					    	$z[$kk][4]++;	
					    	 break;							    		
				    	}
				        break;
				     case z8:
				        switch($vv){
					    	case 1;
					    	$z[$kk][1]++;
					    	 break;
					    	case 2;
					    	$z[$kk][2]++;
					    	 break;
					    	case 3;
					    	$z[$kk][3]++;
					    	 break;
					    	case 4;
					    	$z[$kk][4]++;	
					    	 break;							    		
				    	}
				        break;
				     case z9:
				        switch($vv){
					    	case 1;
					    	$z[$kk][1]++;
					    	 break;
					    	case 2;
					    	$z[$kk][2]++;
					    	 break;
					    	case 3;
					    	$z[$kk][3]++;
					    	 break;
					    	case 4;
					    	$z[$kk][4]++;	
					    	 break;							    		
				    	}
				        break;
				     case z10:
				        switch($vv){
					    	case 1;
					    	$z[$kk][1]++;
					    	 break;
					    	case 2;
					    	$z[$kk][2]++;
					    	 break;
					    	case 3;
					    	$z[$kk][3]++;
					    	 break;
					    	case 4;
					    	$z[$kk][4]++;	
					    	 break;							    		
				    	}
				        break;		
				     case z11:
				        switch($vv){
					    	case 1;
					    	$z[$kk][1]++;
					    	 break;
					    	case 2;
					    	$z[$kk][2]++;
					    	 break;
					    	case 3;
					    	$z[$kk][3]++;
					    	 break;
					    	case 4;
					    	$z[$kk][4]++;	
					    	 break;							    		
				    	}
				        break;
				     	
				     	
				 }
					
			}
		}
		
		//分配统计人员数据到模板
		$this->assign('totalNumber',$z);
		
		//讲算总评饼图0.15    0.05 0.05 0.1 0.15 0.5 0.15 0.15 0.1 0.05
		foreach($dataList as $k=>$v){
			
			foreach($v as $kk=>$vv){
				
				switch($kk){
			    	case z1:
			    		switch($vv){
				    		case 1:
				    			$one = $vv*0.15*10;
				    			break;
				    		case 2:
				    			$one = $vv*0.15*4;
				    			break;
				    		case 3:
				    			$one = $vv*0.15*2;
				    			break;
				    		case 4:
				    			$one = $vv*0.15;
				    			break;
			    		}
			    		break;
			    	case z2:
			    		switch($vv){
				    		case 1:
				    			$one = $vv*0.05*10;
				    			break;
				    		case 2:
				    			$one = $vv*0.05*4;
				    			break;
				    		case 3:
				    			$one = $vv*0.05*2;
				    			break;
				    		case 4:
				    			$one = $vv*0.05;
				    			break;
			    		}
			    		break;
			    	case z3:
			    		switch($vv){
				    		case 1:
				    			$one = $vv*0.05*10;
				    			break;
				    		case 2:
				    			$one = $vv*0.05*4;
				    			break;
				    		case 3:
				    			$one = $vv*0.05*2;
				    			break;
				    		case 4:
				    			$one = $vv*0.05;
				    			break;
			    		}
			    		break;
			    	case z4:
			    		switch($vv){
				    		case 1:
				    			$one = $vv*0.1*10;
				    			break;
				    		case 2:
				    			$one = $vv*0.1*4;
				    			break;
				    		case 3:
				    			$one = $vv*0.1*2;
				    			break;
				    		case 4:
				    			$one = $vv*0.1;
				    			break;
			    		}
			    		break;
			    	 break;
			    	case z5:
			    		switch($vv){
				    		case 1:
				    			$one = $vv*0.15*10;
				    			break;
				    		case 2:
				    			$one = $vv*0.15*4;
				    			break;
				    		case 3:
				    			$one = $vv*0.15*2;
				    			break;
				    		case 4:
				    			$one = $vv*0.15;
				    			break;
			    		}
			    		break;	
			    	case z6:
			    		switch($vv){
				    		case 1:
				    			$one = $vv*0.05*10;
				    			break;
				    		case 2:
				    			$one = $vv*0.05*4;
				    			break;
				    		case 3:
				    			$one = $vv*0.05*2;
				    			break;
				    		case 4:
				    			$one = $vv*0.05;
				    			break;
			    		}
			    		break;
			    	case z7:
			    		switch($vv){
				    		case 1:
				    			$one = $vv*0.15*10;
				    			break;
				    		case 2:
				    			$one = $vv*0.15*4;
				    			break;
				    		case 3:
				    			$one = $vv*0.15*2;
				    			break;
				    		case 4:
				    			$one = $vv*0.15;
				    			break;
			    		}
			    		break;
			    	case z8:
			    		switch($vv){
				    		case 1:
				    			$one = $vv*0.15*10;
				    			break;
				    		case 2:
				    			$one = $vv*0.15*4;
				    			break;
				    		case 3:
				    			$one = $vv*0.15*2;
				    			break;
				    		case 4:
				    			$one = $vv*0.15;
				    			break;
			    		}
			    		break;	
			    	case z9:
			    		switch($vv){
				    		case 1:
				    			$one = $vv*0.1*10;
				    			break;
				    		case 2:
				    			$one = $vv*0.1*4;
				    			break;
				    		case 3:
				    			$one = $vv*0.1*2;
				    			break;
				    		case 4:
				    			$one = $vv*0.1;
				    			break;
			    		}
			    		break;
			    	case z10:
			    		switch($vv){
				    		case 1:
				    			$one = $vv*0.05*10;
				    			break;
				    		case 2:
				    			$one = $vv*0.05*4;
				    			break;
				    		case 3:
				    			$one = $vv*0.05*2;
				    			break;
				    		case 4:
				    			$one = $vv*0.05;
				    			break;
			    		}
			    		break;
		    	}
		    	$onehe += $one;
		    	
			}
			$he[$k]=$onehe;
			$onehe =0;
		}
		
		//得分区间
		$totalPersent = array();
		foreach($he as $k=>$v)
		{
			if($v>=9 && $v<=10){
				$totalPersent['a'] += 1;
			}elseif($v>=8 && $v<9){
				$totalPersent['b'] += 1;
			}elseif($v>=6 && $v<8){
				$totalPersent['c'] += 1;
			}else{
				$totalPersent['d'] += 1;
			}
		}
		//分配到模板
		$this->assign('totalPersent',$totalPersent);
		
		
		//竖列最多人选的三个指标
		//dump($z);
		$lie = array();
		foreach($z as $k=>$v){
			if($k=="z11"){  //只选十个数
				continue;
			}
			
			foreach($v as $kk=>$vv)
			{
				
				$lie[$kk][$k]=$vv;

			}		
			
		}
		// dump($lie);

		$keyValue = array('z1',"z2","z3","z4","z5","z6","z7","z8","z9","z10");
		foreach($keyValue as $k=>$v){
			foreach($lie as $kk=>$vv ){
				if (!array_key_exists($v,$vv )) {
					$lie[$kk][$v] = 0;
				}
			}
			
		}
				
		//dump($lie);
		$lie_sort =array();
		foreach($lie  as $k=>$v)
		{
			$keys = array_keys($v);
			$keys_sort=array();
			foreach($keys as $kk=>$vv){
				$kkk = substr($vv,1);
				$keys_sort[$kkk]=$kkk;
			}
			ksort($keys_sort,SORT_NUMERIC);
			foreach($keys_sort as $kk=>$vv){
				$lie_sort[$k]["z".$vv]=$lie[$k]["z".$vv];
			}
		}

		//dump($lie_sort);

		foreach($lie_sort  as $k=>$v)
		{
			 $rateString[]=implode(",",$v);
		}
		
		//dump($rateString);
		$this->assign('rateString',$rateString);
		
		$this->_list ($modelName, $map );
		$this->assign('trainid',$trainid);
		
		$allData['rateString'] =$lie_sort;				//四条线的数据
		$allData['countPersion'] =$countPersion;		 //订课人数
		$allData['countShangKerRen'] =$countShangKerRen; //实际上课人数
		$allData['totalPersent'] =$z;			//人数统计44个表格数据
		$allData['bingtu'] =$totalPersent;		//饼图四个数值
		$allData['oneinfo'] =$result;  			//课程信息
		
		//其它方法调用， 不显示模板，返回数据
		if(!$showTpl){
			$this->display();
		}else{
			return $allData;
		}
	}
     

	
	/**
	 * @Title: export
	 * @Description: todo(导出excel)   
	 * @author 杨东 
	 * @date 2013-6-1 上午11:24:57 
	 * @throws
	 */
	public function export(){
		$arrayHead = array(
				'id'=>'序号',
				'name'=>'姓名',
				'department'=>'部门',
				'dutyname'=>'职位',
				'phonenumber'=>'电话',
				'remark'=>'备注',
				);
		$map['trainid'] = $_REQUEST['trainid'];						//课程id
		
		//模型
		$orderCouresModel = D('MisHrPersonnelTrainManageRelation');
		$dateList=$orderCouresModel->where($map)->select();
		
		$i=1; 														//序号
		//处理数据外键问题。显示明确数据
		foreach($dateList as $key=>$val){
			$data[]=$this->dataconv($val,$i++);
		}
		//处理数据外键问题。显示明确数据
		$this->export_excel2($arrayHead,array(),$data);

		exit;
	}
	
	/**
	 * @Title: dataconv
	 * @Description: todo(导出转换) 
	 * @param 值 $val
	 * @param 头部标识 $aryhead
	 * @return multitype:string NULL Ambigous <string, 获取小数位数> unknown Ambigous <string, 获取小数位数, unknown>   
	 * @author 杨东 
	 * @date 2013-6-1 上午11:33:40 
	 * @throws 
	*/  
	private function dataconv($val,$number){
		$aryval=array();
		foreach($val as $k=>$v){
			switch($k){
				case "id"://仓库
					$aryval['id'] = $number;
					break;
				case "manageid"://姓名
					$aryval['name'] = getFieldBy($val['personid'],"id","name","MisHrPersonnelPersonInfo");
					break;
				case "trainid"://部门
					$aryval['department'] = getFieldBy($val['personid'],"id","name","MisSystemDepartment");
					break;
				case "personid"://职务
					$aryval['dutyname'] = getFieldBy($v,"id","dutyname","MisHrPersonnelPersonInfo");
					break;
				case "classhour"://库存数量
					$aryval['phonenumber'] = getFieldBy($val['personid'],"id","phone","MisHrPersonnelPersonInfo").getFieldBy($val['personid'],"id","shortNumber","MisHrPersonnelPersonInfo");
					break;
				case "status"://可用数量
					$aryval['remark'] = $val['remark'];
					break;
			}
		}
		return $aryval;
	}	
	
	/**
	 * @Title: exportExcel
	 * @Description: todo(导出Excel)
	 * @author 
	 * @throws
	 */
	public function exportExcel() {
		
		//引入excel导出类
		import('@.ORG.PHPExcel', '', $ext='.php');
	
		$trainid = $_REQUEST['trainid'];
		//执行一个方法，阻止显示模板
		$allData = $this->coursesReport($trainid,'1');
		
		//dump($allData);
		
		//PHPExcel支持读模版 所以我还是比较喜欢先做好一个Excel的模版  比较好，不然要写很多代码  模版我放在根目录了
		//创建一个读Excel模版的对象, 作用是模板中的样式之类的，如底色了，字号了， 大小了...
		$objReader = PHPExcel_IOFactory::createReader ( 'Excel5' );
		
		//当前模板路径，引入的模板文件放到当前模块下对应的模板文件夹中
		$currentTmplPath = TMPL_PATH.'default/'.MODULE_NAME;
		
		$objPHPExcel = $objReader->load ($currentTmplPath."/exportExcel.xls" );
		//获取当前活动的表
		$objActSheet = $objPHPExcel->getActiveSheet ();
		$objActSheet->setTitle ( '培训报告' );  //设定ＥＸＣＥＬ中的工作表名 
		
		// 以下向单元格内填充数据
		$objActSheet->setCellValue ( 'A1', $allData['oneinfo']['name']."报告" ); 
		$objActSheet->setCellValue ( 'B3', $allData['oneinfo']['name']  );  
		$objActSheet->setCellValue ( 'B4', $allData['oneinfo']['lecturer']  );  
		$objActSheet->setCellValue ( 'B5', $allData['countShangKerRen']  );  
		$objActSheet->setCellValue ( 'B6', $allData['countPersion']."人"  );  
		
		$objActSheet->setCellValue ( 'B31', $allData['oneinfo']['comment10']  ); 
		$objActSheet->setCellValue ( 'B32', $allData['oneinfo']['comment11']  );  
		$objActSheet->setCellValue ( 'B33', $allData['oneinfo']['comment12']  );  
				
		$objActSheet->setCellValue ( 'E3',$allData['oneinfo']['classhour']."小时" );  
		$objActSheet->setCellValue ( 'E4',$allData['oneinfo']['targetstudent'] );  
		$objActSheet->setCellValue ( 'E5',$allData['countPersion']."人" );  

		//循环输出数据 现在就开始填充数据了  
		$row = 10; 
		$itemNumber=1;
		foreach ( $allData['totalPersent'] as $r => $dataRow ) {
			$row ++;
			switch($r){
				case z1:
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'C' . $row, isset($dataRow[1])?$dataRow[1]:0);
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'D' . $row, isset($dataRow[2])?$dataRow[2]:0);
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'E' . $row, isset($dataRow[3])?$dataRow[3]:0);
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'F' . $row, isset($dataRow[4])?$dataRow[4]:0);
					break;
				case z2:
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'C' . $row, isset($dataRow[1])?$dataRow[1]:0);
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'D' . $row, isset($dataRow[2])?$dataRow[2]:0);
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'E' . $row, isset($dataRow[3])?$dataRow[3]:0);
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'F' . $row, isset($dataRow[4])?$dataRow[4]:0);
					break;	
				case z3:
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'C' . $row, isset($dataRow[1])?$dataRow[1]:0);
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'D' . $row, isset($dataRow[2])?$dataRow[2]:0);
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'E' . $row, isset($dataRow[3])?$dataRow[3]:0);
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'F' . $row, isset($dataRow[4])?$dataRow[4]:0);
					break;	
				case z4:
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'C' . $row, isset($dataRow[1])?$dataRow[1]:0);
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'D' . $row, isset($dataRow[2])?$dataRow[2]:0);
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'E' . $row, isset($dataRow[3])?$dataRow[3]:0);
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'F' . $row, isset($dataRow[4])?$dataRow[4]:0);
					break;	
				case z5:
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'C' . $row, isset($dataRow[1])?$dataRow[1]:0);
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'D' . $row, isset($dataRow[2])?$dataRow[2]:0);
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'E' . $row, isset($dataRow[3])?$dataRow[3]:0);
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'F' . $row, isset($dataRow[4])?$dataRow[4]:0);
					break;
				case z6:
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'C' . $row, isset($dataRow[1])?$dataRow[1]:0);
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'D' . $row, isset($dataRow[2])?$dataRow[2]:0);
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'E' . $row, isset($dataRow[3])?$dataRow[3]:0);
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'F' . $row, isset($dataRow[4])?$dataRow[4]:0);
					break;
				case z7:
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'C' . $row, isset($dataRow[1])?$dataRow[1]:0);
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'D' . $row, isset($dataRow[2])?$dataRow[2]:0);
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'E' . $row, isset($dataRow[3])?$dataRow[3]:0);
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'F' . $row, isset($dataRow[4])?$dataRow[4]:0);
					break;
				case z8:
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'C' . $row, isset($dataRow[1])?$dataRow[1]:0);
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'D' . $row, isset($dataRow[2])?$dataRow[2]:0);
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'E' . $row, isset($dataRow[3])?$dataRow[3]:0);
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'F' . $row, isset($dataRow[4])?$dataRow[4]:0);
					break;
				case z9:
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'C' . $row, isset($dataRow[1])?$dataRow[1]:0);
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'D' . $row, isset($dataRow[2])?$dataRow[2]:0);
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'E' . $row, isset($dataRow[3])?$dataRow[3]:0);
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'F' . $row, isset($dataRow[4])?$dataRow[4]:0);
					break;
				case z10:
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'C' . $row, isset($dataRow[1])?$dataRow[1]:0);
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'D' . $row, isset($dataRow[2])?$dataRow[2]:0);
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'E' . $row, isset($dataRow[3])?$dataRow[3]:0);
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'F' . $row, isset($dataRow[4])?$dataRow[4]:0);
					break;
				case z11:
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'C' . $row, isset($dataRow[1])?$dataRow[1]:0);
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'D' . $row, isset($dataRow[2])?$dataRow[2]:0);
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'E' . $row, isset($dataRow[3])?$dataRow[3]:0);
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'F' . $row, isset($dataRow[4])?$dataRow[4]:0);
					break;
			}

		}
		
		//饼图数据
			$objPHPExcel->getActiveSheet ()->setCellValue ( 'B24' , isset($allData['bingtu']['a'])?$allData['bingtu']['a']:0);
			$objPHPExcel->getActiveSheet ()->setCellValue ( 'C24' , isset($allData['bingtu']['b'])?$allData['bingtu']['b']:0);
			$objPHPExcel->getActiveSheet ()->setCellValue ( 'D24' , isset($allData['bingtu']['c'])?$allData['bingtu']['c']:0);
			$objPHPExcel->getActiveSheet ()->setCellValue ( 'E24' , isset($allData['bingtu']['d'])?$allData['bingtu']['d']:0);

		//导出时间
		$objActSheet->setCellValue ( 'D36', '导出时间：' . date ( 'Y-m-d H:i:s' ) );
	
		
				
		//导出
		$filename = time ();
		
		header ( 'Content-Type: application/vnd.ms-excel' );
		header ( 'Content-Disposition: attachment;filename="' . $filename . '.xls"' ); //"'.$filename.'.xls"
		header ( 'Cache-Control: max-age=0' );
		
		$objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel5' ); //在内存中准备一个excel2003文件
		$objWriter->save ( 'php://output' );
	}
}
?>