<?php

/**
 * @Title: IndexAction
 * @Package package_name
 * @Description: todo(操作工作台的类)
 * @author xiafengqin
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-6-1 下午3:54:13
 * @version V1.0
 */
class IndexAction extends IndexExtendAction {
	/**
	 * 进入系统后，需要加载和显示的内容(方法)
	 * 1、此处查询数据量大，一定要控制好查询范围，以及查询内容
	 * 2、避免重复查询，尽量一次查询后，用数组进行操作。
	 * 3、避免php和html代码组合使用，导致耦合度上升，难以维护，达不到 模板与功能分离的效果
	 * 4、代码逻辑结构清晰，层次明显。
	 * 5、禁止外部其他方法用传入参数形式调用此入口方法，不然以后难以查错，更难维护。
	 *
	 * 该模块制作一下功能
	 * 1、待办事务
	 * 2、提醒中心
	 * 3、我的常用   SystemMyOften
	 * 4、日程安排
	 * 5、动态面板     SystemPanel
	 * 6、系统菜单栏
	 */
	//onload事件加载时间为2S
	public function index(){
		//判断首页是否有跳转值传入，JSON传值，没有传值就默认为0；
		if($_REQUEST['data']){
			$data = $this->setURLdata($_REQUEST['data']);
		}else{
			$data = 0;
		}
		$this->assign("data",$data);
		//wm需要的参数
		$wsdata = array('uid'=>$_SESSION[C('USER_AUTH_KEY')], 'percent'=>'88%');
		$this->assign('wsdata',json_encode($wsdata));
		//获取系统中存储的当前登陆人ID
		$userid = $_SESSION[C('USER_AUTH_KEY')];
		$this->assign("uid",$userid);
		//获取当前系统中登陆人的公司ID
		$companyid = $_SESSION['companyid'];
		$this->assign("companyid",$companyid);
		/*
		 * 视图查询登陆用户以及所在公司
		 *   *注 多组织架构，存在同一个人在多个公司
		 */
		$UserDeptDutyModel=D("UserDeptDuty");
		$UserDeptDutyMap=array();
		$UserDeptDutyMap['user_dept_duty.status']=1;
		$UserDeptDutyMap['user_dept_duty.userid']=$userid;
		
		$MisSystemCompanyList = $UserDeptDutyModel->join("mis_system_company as mis_system_company on mis_system_company.id = user_dept_duty.companyid")
		->field("user_dept_duty.id as id,mis_system_company.id as companyid,mis_system_company.name as name,mis_system_company.picture as picture")
		->where($UserDeptDutyMap)->select();
		//取出重复的公司信息
		$companyname = "";
		foreach ($MisSystemCompanyList as $key=>$val){
			if($companyid == $val['companyid']){
				$companyname = $val['name'];
				$logoPic =  $val['picture'];
			}
			if(in_array($val['companyid'],array_keys($companyidList))){
				unset($MisSystemCompanyList[$key]);
			}else{
				$companyidList[$val['companyid']]=1;
			}
		}
		$this->assign("logoPic",$logoPic);
		$this->assign("companyname",$companyname);
		$this->assign("MisSystemCompanyList",$MisSystemCompanyList);
		//公司获取结束 end
		
		
		/*---------------单一查询-------------------*/
		//当前登录用户人事ID
// 		$Usercompany['employeid'] = $_SESSION['user_employeid'];
// 		//查询当前员工公司ID
// 		$UserDeptDutyViewModel=D('UserDeptDutyView');
// 		$companyId=$UserDeptDutyViewModel->where(" status=1 and employeid=".$Usercompany['employeid'])->find();
// 		// 获取 当前员工公司名称
// 		if($_SESSION['companyid']){
// 			$company = $_SESSION['companyid'];
// 		}else{
// 			$company = $companyId['companyid'];
// 		}
// 		$companyname =  getFieldBy($company, 'id', 'name', 'mis_system_company');
// 		if(empty($companyname)){
// 			$rs = M("mis_system_company")->where("status=1 and parentid=0")->find();
// 			$companyname=$rs['name'];
// 		} 
// 		$this->assign('companyname',$companyname);
		
		
		//查看公司登陆LOGO
		
		//$CommonSettingModel=M('common_setting'); //配置表
		//$nameList=$CommonSettingModel->where(" skey='companyname'")->find();//名称
		//$this->assign('nameList',$nameList);
		
		//获取在线人数
		//$this->assign('isonline', $this->lookupRefreshOnLine());
		
		//获取顶部LOGO 图片
		//$MisSystemCompanyModel = M("MisSystemCompany");
		//$MisSystemCompanyPicture=$MisSystemCompanyModel->where(" status=1 and id=".$companyid)->field('id,picture')->find();
// 		if(!$MisSystemCompanyPicture){
// 			$MisSystemCompanyPicture=$MisSystemCompanyModel->where(" status=1 ")->field('id,picture')->find();
// 		}
		//$this->assign("logoPic",$MisSystemCompanyPicture['picture']);
		//当前登录用户人事ID
		$employeid= $_SESSION['user_employeid'];
		//获取登录人头像
		$loaduserpicture = getFieldBy($employeid, 'id','picture', 'mis_hr_personnel_person_info');
		$this->assign('loaduserpicture',$loaduserpicture);
		//end

		C ( 'SHOW_RUN_TIME', false ); // 运行时间显示
		C ( 'SHOW_PAGE_TRACE', false );

 		//系统首页滚动公告  载入降低300MS
		$model=D('MisRollAnnouncement');
		$list=$model->where("status =1 and typeid=1")->order('createtime desc')->field('content')->select();
		$roll="";
		foreach ($list as $k=>$v){
			$roll.=$v['content']."&nbsp;&nbsp;&nbsp;&nbsp;";
		}
		$this->assign('roll',$roll);
		/*---------------下面为调用查询-------------------*/

		//返回当前登录用户的header头部邮件条数，载入降低300ms
		$MisMessageUserModel = D("MisMessageUser");
		$msg = $MisMessageUserModel->getUserCurrentMsg(2);
		if($msg>0){
			$this->assign("newmsg",$msg);
		}
		//调用生成系统分组方法,载入降低1.5S 采用换成形式降低 0.025S
		$MisSystemMenuModel = D("MisSystemMenu");
		$pannels = $MisSystemMenuModel->getSysGroupList();
		$this->assign('pannels',$pannels);
		
 		//系统导航 3.46S     系统导航采用缓存形式降低至0.002S
		$navilist = $this->navidialog();		
		$this->assign('navilist',$navilist);
 		//获取首页默认桌面，载入降低1.5S
		$this->lookupsystemindex();
		
		$this->display();
	}
	/**
	 * @Title: navidialog
	 * @Description: todo(系统导航数据)   
	 * @author 谢友志 
	 * @date 2014-11-6 上午10:05:02 
	 * @throws
	 */
	public function navidialog(){
		$list = "";
		// 实例化换成模型
		$mMisRuntimeData = D ( 'MisRuntimeData' );
		// 从当前登录用户获取group的换成
		$list = $mMisRuntimeData->getRuntimeCache ( "GroupAndNode", 'groupnodelist' );
		if (empty ( $list )) {
			// 组
			$model = M ( "group" );
			$list = $model->where ( "status=1" )->order ( "sorts asc" )->select ();
			// 获取系统授权
			$model_syspwd = D ( 'SerialNumber' );
			$modules_sys = $model_syspwd->checkModule ();
			$m_list = explode ( ",", $modules_sys );
			// 需过滤的model
			$model = D ( 'SystemConfigDetail' );
			$filter = $model->getDCFilter ();
			// 查询菜单节点
			$map ['status'] = 1;
			$map ['showmenu'] = 1;
			$map ['type'] = array ('lt',4);
			$node = M ( "Node" );
			$data = $node->where ( $map )->order ( 'sort asc' )->field ( "id,pid,name,level,type,group_id,title,toolshow" )->select ();
			
			// 获取授权节点
			$access = getAuthAccess ();
			$newlist = array ();
			foreach ( $list as $k2 => $v2 ) {
				foreach ( $data as $k => $v ) {
					// 过来权限
					if (substr ( $v ['name'], 0, 10 ) != "MisDynamic") {
						if (! in_array ( $v ['name'], $m_list ))
							continue;
					}
					// if (!isset ($access[strtoupper( APP_NAME )][strtoupper ($v ['name'])]) && !$_SESSION [C('ADMIN_AUTH_KEY')]) {
					// continue;
					// }
					if ($v ['name'] != 'Public' && $v ['name'] != 'Index') {
						if ($v ['type'] == 1 && $v ['group_id'] == $v2 ['id']) {
							$list [$k2] ['_child'] [$k] = $v;
							// 第三个循环判断模块节点
							foreach ( $data as $k3 => $v3 ) {
								// 过来权限
								if (substr ( $v3 ['name'], 0, 10 ) != "MisDynamic") {
									if (! in_array ( $v3 ['name'], $m_list ))
										continue;
								}
								if (! isset ( $access [strtoupper ( APP_NAME )] [strtoupper ( $v3 ['name'] )] ) && ! $_SESSION [C ( 'ADMIN_AUTH_KEY' )]) {
									continue;
								}
								if ($v3 ['level'] == 3 && $v3 ['pid'] == $v ['id']) {
									$list [$k2] ['_child'] [$k] ['_child'] [$k3] = $v3;
								}
							}
						}
					}
					if (empty ( $list [$k2] ['_child'] [$k] ['_child'] ))
						unset ( $list [$k2] ['_child'] [$k] );
				}
				if (empty ( $list [$k2] ['_child'] ))
					unset ( $list [$k2] );
			}
			// 如果pannels不为空，就写入当前用户换成中
			if ($list) {
				$mMisRuntimeData->setRuntimeCache ( $list,"GroupAndNode", 'groupnodelist');
			}
		}
		return $list;
	}
	/**
	 * @Title: userindex
	 * @Description: todo(工作台切换)
	 * @author 杨东
	 * @date 2013-4-1 上午11:48:16
	 * @throws
	 */
	public function lookupuserindex(){
		if(isset($_GET['workbench'])){
			switch ($_GET['workbench']) {
				//yuansl 2014 06 24  新增工功能盒子
				case 4:
					$MisSystemAppAction=A("MisSystemApp");
					$MisSystemAppAction->getUserApp();
					$this->display("MisSystemApp:lookupmyfunctionbox");
					break;
				case 0:
					//企业工作台
					$this->lookupsystemindex();
					$this->display('lookupsystemindex');
					break;
				case 1:
					//报表中心
					//$this->userWorkbench();
					//读取报表中心
					$MisSystemReportModel=M("mis_system_report");
					$MisSystemReportList=$MisSystemReportModel->where("status=1")->select();
					$groupList=array();
					$listArr=array();
					foreach ($MisSystemReportList as $key=>$val){
						if($val['group_id']){
							if(in_array($val['group_id'], array_keys($groupList))){
								$listArr[$val['group_id']][]=$val;
							}else{
								$listArr[$val['group_id']][]=$val;
								$groupList[$val['group_id']]=$val['group_id'];
							}
						}
					}
					$this->assign("MisSystemReportList",$MisSystemReportList);
					$this->assign("reportlistArr",$listArr);
					$this->assign("groupList",$groupList);
					$this->display('preferences');
					break;
				case 2:
					//判断权限
					$isopWorkbench = $this->lookupisOpWorkbench();
					if($isopWorkbench){
						$this->opWorkbench($isopWorkbench);
						$this->assign('isopWorkbench',$isopWorkbench);
						$this->display('opworkbench');
					}else{
						$this->userWorkbench();
						$this->display('preferences');
					}
					break;
			}
			exit;
		}
		$this->display();
	}

	/**
	 * @Title: userWorkbench
	 * @Description: todo(个人工作台数据获取)
	 * @author 杨东
	 * @date 2013-3-30 下午2:29:40
	 * @throws
	 */
	private function userWorkbench(){
		$model=M("group");
		$list = $model->where("status=1")->order("sorts asc")->select();
		$node = D( "Node" );
		$map['status'] = 1;
		$map['showmenu'] = 1;
		$map['level'] =array("neq",4);
		$nodedata = $node->where($map)->order("sort asc")->select();
		//获取系统授权
		$model_syspwd=D('SerialNumber');
		$modules_sys=$model_syspwd->checkModule();
		$m_list = explode(",",$modules_sys);
		if(isset($_SESSION[C('USER_AUTH_KEY')])) {
			$systempanels="<ul class=\"clearfix xymainapp\">";
			foreach($list as $k =>$v){
				$h="<li class=\"mainlist\">";
				if(!$v["icon"]) $v["icon"] = "appbtn_61.png";
				if( $v['indexlink'] ){
					//$h .= '<a href="#" url="__APP__/Public/nvigateTO/id/'.$v["id"].'" targets="navTab" rel="'.$v["name"].'" title="'.$v["name"].'"><img alt="'.$v["name"].'" height="64" src="__PUBLIC__/Images/xyicon/'.$v["icon"].'" width="64" /><span>'.$v["name"].'</span></a>';
					$h .= '<a href="#" url="__APP__/Common/nvigateTO/id/'.$v["id"].'" targets="navTab" rel="'.$v["name"].'" title="'.$v["name"].'"><img alt="'.$v["name"].'" height="64" src="__PUBLIC__/Images/xyicon/'.$v["icon"].'" width="64" /><span>'.$v["name"].'</span></a>';
				}else{
					$h .= '<a class="maina" href="#" title="'.$v["name"].'"><img alt="'.$v["name"].'" height="64" src="__PUBLIC__/Images/xyicon/'.$v["icon"].'" width="64" /><span>'.$v["name"].'</span></a>';
				}
				$systempanels_tree= $this->getIndexPanels($v['id'],$nodedata,0,"","",$m_list);
				$h_e="</li>";
				if($systempanels_tree){
					//获取功能盒子
					$functionalBoxmodel=D("MisSystemFunctionalBox");
					$functionalarr=$functionalBoxmodel->where("status=1")->select();
					$functionalBox="";
					foreach ($functionalarr as $key=>$val){
						if($val['groupid'] == $v['id']){
							$functionalBox .= "<li><a title='".$val['name']."' rel='MisSystemFunctionalBox' target='navTab' href='".$val['qlink'].$val['link']."'>";
							if($val['logo']){
								$functionalBox .="<img width='64' height='64' src='__PUBLIC__/Uploads/".$val['logo']."' alt='".$val['name']."'>";
							}else{
								$functionalBox .="<img width='64' height='64' src='__PUBLIC__/Images/xyicon/xyicon_36.png' alt='".$val['name']."'>";
							}
							$functionalBox .="<span>".$val['name']."</span></a></li>";
						}
					}
					$systempanels.=$h."<ul class=\"xysubapp clearfix\">".$systempanels_tree.$functionalBox."</ul>".$h_e;
				}
			}
			$systempanels.= "</ul>";
			$this->assign('systempanels',$systempanels);
		}
	}

// 	/**
// 	 * @Title: userIndexOperate
// 	 * @Description: todo(userIndex页面的操作调用函数)
// 	 * @author 杨东
// 	 * @date 2013-3-30 下午2:09:53
// 	 * @throws
// 	 */
// 	private function userIndexOperate(){
// 		if($_GET['type'] == 1) {
// 			//调用生成首页常用功能
// 			$uommodel = D("UserOftenMenu");
// 			$workoftenList=$uommodel->getSysOftenList();
				
// 			$oftenconfiglist=require DConfig_PATH."/System/oftenconfig.inc.php";
// 			foreach ($oftenconfiglist as $key=>$val){
// 				if($val['type']==1){//1为首页默认常用   2为工作中心常用
// 					$defaultoftenlist[]=$val;
// 				}
// 			}
// 			if($workoftenList){
// 				if(count($workoftenList)<7){
// 					//算出差数
// 					$count=7-(count($workoftenList));
// 					//去除重复数据
// 					foreach ($workoftenList as $wkey=>$wval){
// 						$relList[]=$wval['rel'];
// 					}
// 					$i=0;
// 					foreach ($defaultoftenlist as $dkey=>$dval){
// 						if(in_array($dval['rel'],$relList)){
// 							unset($defaultoftenlist[$dkey]);
// 						}else{
// 							if($i<=$count){
// 								$workoftenList[]=$dval;
// 							}
// 							$i++;
// 						}
// 					}
// 				}
// 			}else{
// 				//默认常用图标
// 				$workoftenList=$defaultoftenlist;
// 			}
// 			$this->assign('oftenList',$workoftenList);
// 			//加载常用功能页面
// 			$this->display('oftenindex');
// 			exit;
// 		} elseif ($_GET['type'] == 2) {
// 			//弹出新增常用功能页面
// 			$this->oftenadd();
// 			exit;
// 		} elseif ($_GET['type'] == 3) {
// 			//删除常用功能
// 			$uommodel = D("UserOftenMenu");
// 			$uommap['id'] = $_POST['id'];
// 			$uommodel->startTrans();
// 			$res = $uommodel->where($uommap)->delete();
// 			$uommodel->commit();
// 			echo $res;
// 			exit;
// 		} elseif ($_GET['type'] == 4) {
// 			// 查询常用功能列表
// 			//获取系统授权
// 			$model_syspwd=D('SerialNumber');
// 			$modules_sys=$model_syspwd->checkModule();
// 			$m_list = explode(",",$modules_sys);
// 			$oftenList = $this->oftenaddtree($_GET['gid'],$m_list);
// 			$this->assign('oftenList',$oftenList);
// 			$this->display('oftenlist');
// 			exit;
// 		} elseif ($_GET['type'] == 5) {
// 			//新增常用功能
// 			$uommodel = D("UserOftenMenu");
// 			//查询当前用户是否已添加6个常用
// 			$umap['uid'] = $_SESSION[C('USER_AUTH_KEY')];
// 			$umap['status'] = 1;
// 			$workoftenList = $uommodel->where($umap)->order("createtime asc")->select();
// 			if(count($workoftenList)>6){
// 				$this->error("常用菜单已存在7个,请删除后再添加！");
// 				exit;
// 			}
// 			//新增常用功能
// 			$url = $_POST['url'];
// 			$list = $uommodel->where("uid='".$_SESSION[C('USER_AUTH_KEY')]."' and url='".$_POST['url']."'")->count('*');
// 			if($list){
// 				$this->error("已经存在相同菜单！");
// 				exit;
// 			}
// 			$nodeModel = D('Node');
// 			$nodeArr = explode('/', $url);
// 			$nodeVO = $nodeModel->where("name = '".$nodeArr[0]."'")->find();
// 			if(!$_POST['rel'] || !$_POST['title'] || !$nodeVO['icon'] || !$url){
// 				$this->error("常用菜单添加失败！");
// 				exit;
// 			}
// 			$data['uid'] = $_SESSION[C('USER_AUTH_KEY')];
// 			$data['rel'] = $_POST['rel'];
// 			$data['title'] = $_POST['title'];
// 			$data['url'] = $url;
// 			$data['icon'] = $nodeVO['icon'];
// 			$data['createtime'] = time();
// 			$uommodel->startTrans();
// 			$uommodel->data($data);
// 			$res = $uommodel->add($data);
// 			$uommodel->commit();
// 			if ($res!==false) { //保存成功
// 				$this->success ( L('_SUCCESS_'));
// 			} else {
// 				$this->error ( L('_ERROR_') );
// 			}
// 			exit;
// 		}elseif ($_GET['type'] == 6) {//功能盒子添加常用
// 			//新增常用功能
// 			$uommodel = D("UserOftenMenu");
// 			$url = $_POST['url'];
// 			$list = $uommodel->where("uid='".$_SESSION[C('USER_AUTH_KEY')]."' and url='".$_POST['url']."'")->count('*');
// 			if($list){
// 				$this->error("已经存在相同菜单！");
// 				exit;
// 			}
// 			$data['uid'] = $_SESSION[C('USER_AUTH_KEY')];
// 			$data['rel'] = $_POST['rel'];
// 			$data['title'] = $_POST['title'];
// 			$data['url'] = $url;
// 			$data['icon'] = $_POST['icon'];
// 			$data['target'] = $_POST['target'];
// 			$data['createtime'] = time();
// 			$uommodel->startTrans();
// 			$uommodel->data($data);
// 			$res = $uommodel->add($data);
// 			$uommodel->commit();
// 			if ($res!==false) { //保存成功
// 				$this->success ( L('_SUCCESS_'));
// 			} else {
// 				$this->error ( L('_ERROR_') );
// 			}
// 			exit;
// 		}
// 	}

	/**
	 * @Title: getIndexPanels
	 * @Description: todo(这里用一句话描述这个方法的作用)
	 * @param $group_id 组
	 * @param $nodedata 节点数据
	 * @param $pid 父节点
	 * @param $pname 父名称
	 * @param $ptitle 父标题
	 * @param $m_list 系统授权数据
	 * @return string
	 * @author 杨东
	 * @date 2013-3-11 下午1:49:42
	 * @throws
	 */
	private function getIndexPanels($group_id,$nodedata,$pid=0,$pname="",$ptitle="",$m_list){
		$html="";
		$node = D( "Node" );
		$access = getAuthAccess();
		foreach($nodedata as $k => $v) {
			if($v['group_id'] !=$group_id) continue;
			if(!$v["icon"]) $v["icon"] = "appbtn_61.png";
			if( $pid ){
				if($pid!=$v['pid'])continue;
			}
			if($v['name']!='Public' && $v['name']!='Index') {
				switch ($v['type']) {
					case 1:
						if($v["toolshow"]==1){
							$s1_1= '<li class="sublist"><a class="suba" href="#" title="'.$v['title'].'"><img alt="'.$v['title'].'" height="64" src="__PUBLIC__/Images/xyicon/'.$v['icon'].'" width="64" /><span>'.$v['title'].'</span></a>';
						}
						unset($nodedata[$k]);
						$s1_2= $this->getIndexPanels($group_id,$nodedata,$v['id'],"","",$m_list);
						if($s1_2){
							if($v["toolshow"]==1 ){
								$html.=$s1_1."<ul class=\"xytriapp clearfix\">".$s1_2."</ul></li>";
							}else{
								$html.=$s1_2;
							}
						}else{
							if($_SESSION [C('ADMIN_AUTH_KEY')] && $s1_1){
								if($v["toolshow"]==1){
									$html.=$s1_1."</li>";
								}
							}
						}
						break;
					case 2:
						if($v["pid"]==$pid){
							unset($nodedata[$k]);
							if($v["toolshow"]==1){
								$s2_1= '<li class="sublist"><a class="suba" href="#" title="'.$v['title'].'"><img alt="'.$v['title'].'" height="64" src="__PUBLIC__/Images/xyicon/'.$v['icon'].'" width="64" /><span>'.$v['title'].'</span></a>';
							}
							$s2_2=$this->getIndexPanels($group_id,$nodedata,$v['id'],"","",$m_list);
							if($s2_2){
								if($v["toolshow"]==1){
									$html.=$s2_1."<ul class=\"xytriapp clearfix\">".$s2_2."</ul></li>";
								}else{
									$html.=$s2_2;
								}
							}else{
								if($_SESSION [C('ADMIN_AUTH_KEY')] && $s2_1){
									if($v["toolshow"]==1){
										$html.=$s2_1."</li>";
									}
								}
							}
						}
						break;
					case 3:
						if($v["pid"]==$pid){
							unset($nodedata[$k]);
							//校验系统模块授权
							if(substr($v['name'], 0, 10)!="MisDynamic"){
								if(!in_array($v['name'], $m_list))  continue;
							}
							if (!isset ($access[strtoupper( APP_NAME )][strtoupper ($v ['name'])]) && !$_SESSION [C('ADMIN_AUTH_KEY')]) {
								continue;
							}
							$html .= '<li><a href="__APP__/'.$v['name'].'/index" target="navTab" rel="'.$v['name'].'" title="'.$v['title'].'"><img alt="'.$v['title'].'" height="64" src="__PUBLIC__/Images/xyicon/'.$v['icon'].'" width="64" /><span>'.$v['title'].'</span></a>';
							//$html.= "<li><a class=\"add_new\" target=\"navTab\" rel=\"".$v['name']."\" href=\"__APP__/".$v['name']."/index\"><span class=\"left\">".$v['title']."</span>";
							$map['status'] = 1;
							$map['level'] =4;
							$map['pid'] =$v['id'];
							$map['group_id'] =$group_id;
							$nodedata2 = $node->where($map)->select();
							if($nodedata2){
								$s3_1= $this->getIndexPanels($group_id,$nodedata2,$v['id'],$v['name'],$v['title'],$m_list);
								if($s3_1){
									$html.="<ul class=\"xytriapp clearfix\">";
									$html .=$s3_1;
									$html.="</ul>";
								}
							}
							$html.="</a></li>";
						}
						break;
					case 4:
						unset($nodedata[$k]);
						if (!isset ($access[strtoupper( APP_NAME )][strtoupper ($pname)][strtoupper ( $v['name'])])  && !$_SESSION [C('ADMIN_AUTH_KEY')]) {
							continue;
						}
						if( $v["toolshow"]){
							$url_parem=$v['name'];
							$title	=$v['title'];
							$rel	=$pname.$v['name'];
							if($v['name']=="waitAudit"){
								$title	=$ptitle;
								$rel	=$pname;
								$url_parem="index/ntdata/1";
							}else if($v['name']=="alreadyAudit"){
								$title	=$ptitle;
								$rel	=$pname;
								$url_parem="index/ntdata/2";
							}
							$html .= '<li><a href="__APP__/'.$pname.'/'.$url_parem.'\" target="navTab" rel="'.$rel.'" title="'.$v['title'].'"><img alt="'.$v['title'].'" height="64" src="__PUBLIC__/Images/xyicon/'.$v['icon'].'" width="64" /><span>'.$v['title'].'</span></a></li>';
							//$html.= "<li><a class=\"add_new\" title=".$title." target=\"navTab\" rel=\"".$rel."\" href=\"__APP__/".$pname."/".$url_parem."\"><span class='left'>".$v['title']."</span></a></li>";
						}
						break;
				}
			}
		}
		return $html;
	}
	
	public function lookupsystemindex(){
		//获取工作协同数据
		/* $MisOaItemsModel = D("MisOaItems");
		$userOaitemlist = $MisOaItemsModel->getUserOaItemsList(); */
		$MisOaItemsModel = D("MisAutoShb");
		$MisOaItemsMap['_string']='(`lingquren`='.$_SESSION [C ( 'USER_AUTH_KEY' )].' and `zhixingqingkuang`=2) or (`faburen`='.$_SESSION [C ( 'USER_AUTH_KEY' )].' and `zhixingqingkuang`=4)';
		$userOaitemlist['list'] = $MisOaItemsModel->where($MisOaItemsMap)->select();
		$userOaitemlist['count']=count($userOaitemlist['list']);
		$this->assign("userOaitemlist",$userOaitemlist);
		//获取工作审批数据
		$MisWorkMonitoringModel = D("MisWorkMonitoring");
		$userAuditlist = $MisWorkMonitoringModel->getUserAuditList();
		$this->assign("userAuditlist",$userAuditlist);
		//获取项目执行数据(项目分派，项目执行，表决)
		$MisWorkExecutingModel = D("MisWorkExecuting");
		$userWorkExecutList = $MisWorkExecutingModel->getUserWorkExecutList();
		
		$this->assign("userWorkExecutList",$userWorkExecutList);
		//综合计算所有代办条数(在index页面显示)
		$workCount = $userOaitemlist['count']+$userAuditlist['count']+$userWorkExecutList['count'];
		$this->assign("workCount",$workCount);
		
		//工作只会数据
		$notifyModel=D('MisNotify');
		$nmap ['_string'] = 'FIND_IN_SET(  ' . $_SESSION [C ( 'USER_AUTH_KEY' )] . ',recipient )';
		$nmap['isread']=0;
		$userZhuilist['count'] =$notifyModel->where ( $nmap )->count ( '*' );;
		$this->assign("userZhuilist",$userZhuilist);
		
		//获取我的常用功能模块
		$UserOftenMenuModel = D("UserOftenMenu");
		$userMenus=$UserOftenMenuModel->getUserMenu();
		$this->assign('workoftenList',$userMenus);
		
		//日历上面的日程信息
		$MisUserEventsModel = D("MisUserEvents");
		$myEvents=$MisUserEventsModel->getMyEvents();
		$this->assign('myEvents',json_encode($myEvents));
		
		//提醒中心
		$MisSystemRemindAction = A("MisSystemRemind");
		$MisSystemRemindAction->getUserRemind();
		
		//获取系统默认及动态面板
		$MisSystemPanelAction = A("MisSystemPanel");
		$MisSystemPanelAction->lookupSysPanel();
	}
	
	/**
	 * @Title: clearcache
	 * @Description: todo(显示清空缓存选择页面)
	 * @author libo
	 * @date 2014-4-18 下午4:42:22
	 * @throws
	 */
	function clearcache(){
		$this->display();
	}
	/*
	 * @jiangx
	* 将此函数从Public 移到Index
	* date 2013-11-04
	*/
	function clear_cache(){
		import ( '@.ORG.Dir' );
		Dir::delDir(RUNTIME_PATH);
		$this->success('删除缓存成功！');
	}
	/**
	 * @Title: getNewestBbs
	 * @Description: todo(获取论坛中最新的帖子,分享)
	 * @$num 数量 默认调取10条
	 * @author qchlian
	 * @date 2013-12-27 下午14:39:42
	 * return size
	 */
	public function getNewestBbs( $num=10 ){
		import('@.ORG.UcenterBbs', '', $ext='.php');
		$objUcenterBbs = new UcenterBbs();
		$list = $objUcenterBbs->getNewestBbs( $num );
		return $list;
	}
	public function lookupchangec(){
		$companyid=$_POST['companyid'];
		//当前登录公司id
		$_SESSION['companyid']=$companyid;
		//查询该用户在此公司中主岗部门
		$userMap=array();
		$userMap['status']=1;
		//查询该公司
		$userMap['companyid']=$companyid;
		//查询主岗
		$userMap['typeid']=1;
		//当前用户
		$userMap['userid']=$_SESSION[C('USER_AUTH_KEY')];
		$userDeptDutyModel=D("UserDeptDuty");
		//查询当前登录公司主岗部门id
		$userDeptDutyVo=$userDeptDutyModel->where($userMap)->find();
 		$_SESSION['user_dep_id']=$userDeptDutyVo['deptid'];
 		//删除原有浏览权限
 		$obj_dir = new Dir;
 		$directory =  DConfig_PATH."/BrowsecList/borwse_".$_SESSION[C('USER_AUTH_KEY')].".php";
 		$ret = file_exists($directory);
 		if($ret)
 		{
 			$ret = unlink($directory);
 		}
 		/*if(isset($directory)){
 			unlink($directory)
 			$obj_dir->del($directory);
 		}*/
 		
 		//写入浏览及权限
 		Browse::saveBrowseList();
 		//修改公司后 重新读取浏览权限
		echo $_SESSION['companyid'];
	}
	
	/**
	 * @Title: getAllScheduleList
	 * @Description: todo(右下角系统提示信息)
	 * @author jiangx
	 * @date 2013-7-9
	 * @throws
	 */
	public function getAllScheduleList(){
		$html = '';
		$moduleNameList=array();
		$arr=getTaskulous();
		//审批提醒
		$scheduleList = array();
		//项目执行提醒
		$datalist = array();
		foreach( $arr as $k=>$v ){
			if(!in_array($v['tablename'], array_keys($moduleNameList))){
				//if ($_SESSION["a"] == 1 ||  $_SESSION[strtolower($v['tablename'])."_waitaudit"]) {
				$moduleNameList[$v['tablename']]=1;
				$model = D($v['tablename']);
				$map = array();
				if (method_exists ( $action, '_filter' )) {
					$action->_filter ( $map );
				}
				$map['status'] = 1;
				$map['_string'] = 'FIND_IN_SET(  '.$_SESSION[C('USER_AUTH_KEY')].', curAuditUser )';
				//$map['isread'] = 0;
				$idarr = $model->where($map)->getField('id', true);
				if (count($idarr) > 0) {
					$new = array();
					$new['model'] = $v['tablename'];
					$new['name'] =  getFieldBy($v['tablename'], 'name', 'title', 'node');
					$new['href'] = __APP__."/".$v['tablename']."/index/default/7";
					$new['count'] = count($idarr);
					//$datalist[$v['model']] = '0';
					//foreach ($idarr as $val) {
					//	$datalist[$v['model']] .= "," . $val;
					//}
					$scheduleList[] = $new;
				}
			}
		}
		$MisWorkExecutingModel = D("MisWorkExecuting");
		$projectlist = $MisWorkExecutingModel->getUserWorkExecutList();
		if($projectlist['count']>0){
			foreach($projectlist['list'] as $key=>$val){
				if($val['count']){
					$new = array();
					$new['model'] = $val['tablename'];
					$new['name'] =  $val['title'];
					$new['href'] = __APP__."/".$val['tablename']."/index".$val['condition'];
					$new['count'] = $val['count'];
					$scheduleList[] = $new;
				}
			}
		}
		//定时任务 单据提醒
		//查询定时任务
		$MisSystemDataRemindMasViewModel=D("MisSystemDataRemindMasView");
		$map=array();
		$map['userid']=$_SESSION[C('USER_AUTH_KEY')];
		$map['status']=1;
		$map['substatus']=1;
		$map['operation']=1;
		$map['issend']=0;
		//$map['sendtime']=time();
		$MisSystemDataRemindMasList=$MisSystemDataRemindMasViewModel->where($map)->find();
		if($MisSystemDataRemindMasList){
			$new = array();
			$new['model'] = $MisSystemDataRemindMasList['modelname'];
			$new['name'] =  getFieldBy($MisSystemDataRemindMasList['modelname'], "name", "title", "node");
			$new['href'] = __APP__."/".$MisSystemDataRemindMasList['modelname']."/view/id/".$MisSystemDataRemindMasList['pkey'];
			$new['msginfo'] = $MisSystemDataRemindMasList['msginfo'];
			$new['chinese'] = getFieldBy($MisSystemDataRemindMasList['modelname'], "name", "title", "node");
			$megList[] = $new;
		}
		if ($scheduleList) {
			$this->assign('msgscheduleList',$scheduleList);
			$this->assign("megList",$megList);
			$html = $this->fetch("sysgmsgschedule");
			$rehtml["html"]= $html;
			$rehtml['date'] = $hasmsg;
			$rehtml['datalist'] = 0;//$datalist;
			echo json_encode($rehtml);
			exit;
		}
	}
	/**
	 * lookupSetTime接收提示框时间数据
	 */
	function lookupSetTime(){
		//重设登陆超时时间
		$userinfo = Cookie::get("userinfo");
		$userModel=D('User');
		$userMap['id']=$_SESSION [C ( 'USER_AUTH_KEY' )];
		$$authInfo=$userModel->where($userMap)->find();
		$cook_userinfo['pwd'] =$userinfo['pwd'];
		$cook_userinfo['user']=$userinfo['user'];
		$cook_userinfo['logintime'] =$userinfo['logintime'];
		Cookie::set("userinfo",$cook_userinfo,C('COOKIE_EXPIRE'));
		
		$onlineModel=M('user_online');
		$map['userid']=$_SESSION [C ( 'USER_AUTH_KEY' )];
		$time="+".$_REQUEST['nexttime']." minute";
		$data['nexttime']=strtotime($time);
		//$data['nexttime']=$_REQUEST['nexttime'];
		$list=$onlineModel->where($map)->save($data);
		echo json_encode($list);
	}
	
	/**
	 * lookupSelectTime查询当前用户提示框时间数据
	 */
	function lookupSelectTime(){
		$onlineModel=M('user_online');
		$map['userid']=$_SESSION [C ( 'USER_AUTH_KEY' )];
		$list=$onlineModel->where($map)->find();
		echo json_encode($list['nexttime']);
	}
	public function lookupRemindCount(){
		$map=array();
		$map['userid']=11;//$_SESSION[C('USER_AUTH_KEY')];
		$map['isread']=0;
		$map['status']=1;
		$map['substatus']=1;
		$map['operation']=1;
		$readcount=D("MisSystemDataRemindMasView")->where($map)->count();
		if($readcount){
			$this->assign("readcount",$readcount);
			$html = $this->fetch("sysgmsgscheduleremind");
			$rehtml["html"]=$html;
			echo json_encode($rehtml);
		}
	}
}
?>
