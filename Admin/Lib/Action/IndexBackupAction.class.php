<?php
//Version 1.0
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
class IndexAction extends CommonAction {
	public $treeMenu;
	public $oftenMenu;

	// 框架首页
	public function index() {
		//判断首页是否有跳转值传入，JSON传值，没有传值就默认为0；
		if($_REQUEST['data']){
			$data = $this->setURLdata($_REQUEST['data']);
		}else{
			$data = 0;
		}
		$this->assign("data",$data);
		//查询当前登录用户关联员工ID
		$userModel=D('User');
		$mMisHrBasicEmployeeModel=D('MisHrBasicEmployee');
		$Usercompany=$userModel->where(" status=1 and id=".$_SESSION[C('USER_AUTH_KEY')])->find();
		//查看公司登陆LOGO
		$CommonSettingModel=M('common_setting'); //配置表
		$nameList=$CommonSettingModel->where(" skey='companyname'")->find();//名称
		$this->assign('nameList',$nameList);
		//查询当前员工公司ID
		$companyId=$mMisHrBasicEmployeeModel->where(" status=1 and id=".$Usercompany['employeid'])->find();
		$msg= $this->getCurrentMsg(2, 1);
		if($msg>0){
			$this->assign("newmsg",$msg);
		}
		//查询当前用户有无代办事项
		$userMap['id']=$_SESSION[C('USER_AUTH_KEY')];
		$userMap['status']=1;
		$userRemindList=$userModel->where($userMap)->find();
		$remindList=require DConfig_PATH."/System/remindconfig.inc.php";
		$remindcount=array();
		//当前时间
		$time=time();
		foreach ($remindList as $key=>$val){
			if($userRemindList['remindid']){
				if(in_array($val['sortnum'], explode(',',$userRemindList['remindid']))&&$_SESSION[$val['authmodule']]||$_SESSION['a']) {
					$count=0;
					//查询满足条件条数
					if($val['modulename']){
						$model=D($val['modulename']);
					}
					if($val['map']){
						//替换map中当前时间$time
						$val['map']=str_replace('$time', $time,$val['map']);
					}
					$count['count']=$model->where($val['map'])->count('*');
				}
			}else{
				if($_SESSION[$val['authmodule']]||$_SESSION['a']) {
					$count=0;
					//查询满足条件条数
					if($val['modulename']){
						$model=D($val['modulename']);
					}
					if($val['map']){
						//替换map中当前时间$time
						$val['map']=str_replace('$time', $time,$val['map']);
					}
					$count['count']=$model->where($val['map'])->count('*');
				}
			}
			$remindcount[]=$count;
		}
		$this->assign("remindcount",$remindcount);
		$MisWorkMonitoringViewModel = D("MisWorkMonitoringView");
		$map=array();
		$map['dostatus'] = 0;//代表待办任务strtolower
		//$map['_string'] = 'FIND_IN_SET(  '.$_SESSION[C('USER_AUTH_KEY')].', curNodeUser )';
		if(!$_SESSION["a"]==1){
			$map['_string'] = 'FIND_IN_SET(  '.$_SESSION[C('USER_AUTH_KEY')].', curNodeUser )';
		}
		$map['node.status']=1;
		$MisWorkMonitoringcount=$MisWorkMonitoringViewModel->where($map)->count('*');
		unset($map);
		$this->assign("MisWorkMonitoringcount",$MisWorkMonitoringcount);

		//获取在线人数
		$this->assign('isonline', $this->lookupRefreshOnLine());
		//获取顶部LOGO 图片
		$MisSystemCompanyModel = M("MisSystemCompany");
		$MisSystemCompanyPicture=$MisSystemCompanyModel->where(" status=1 and id=".$companyId['companyid'])->field('id,picture')->find();
		if(!$MisSystemCompanyPicture){
			$MisSystemCompanyPicture=$MisSystemCompanyModel->where(" status=1 ")->field('id,picture')->find();
		}
		$this->assign("logoPic",$MisSystemCompanyPicture['picture']);
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
		$defaultworkbench = Cookie::get("defaultworkbench");
		if(!$defaultworkbench) {
			$defaultworkbench = 0;
		}
		$this->assign('defaultworkbench',$defaultworkbench);
		if($defaultworkbench === 0){
			$this->lookupsystemindex();
		} else if($defaultworkbench === 2){
			$isopWorkbench = $this->lookupisOpWorkbench();
			if($isopWorkbench){
				$this->assign('isopWorkbench',$isopWorkbench);
				$this->opWorkbench($isopWorkbench);
			} else {
				$defaultworkbench = 0;
			}
		}
		if(isset($_SESSION[C('USER_AUTH_KEY')])) {
			$pannels="<ul class=\"clearfix\">";
			$systempanels="<ul class=\"clearfix xymainapp\">";
			foreach($list as $k =>$v){
				$h="<li>";
				$sysh="<li class=\"mainlist\">";
				if(!$v["icon"]) $v["icon"] = "appbtn_61.png";
				if( $v['indexlink'] ){
					//$h .= "<a href='__APP__/Public/nvigateTO/id/".$v["id"]."' target='navTab' rel='".$v["name"]."'>";
					$h .= "<a href='__APP__/Common/nvigateTO/id/".$v["id"]."' target='navTab' rel='".$v["name"]."'>";
					//$sysh .= '<a href="#" url="__APP__/Public/nvigateTO/id/'.$v["id"].'" targets="navTab" rel="'.$v["name"].'" title="'.$v["name"].'"><img alt="'.$v["name"].'" height="64" src="__PUBLIC__/Images/xyicon/'.$v["icon"].'" width="64" /><span>'.$v["name"].'</span></a>';
					$sysh .= '<a href="#" url="__APP__/Common/nvigateTO/id/'.$v["id"].'" targets="navTab" rel="'.$v["name"].'" title="'.$v["name"].'"><img alt="'.$v["name"].'" height="64" src="__PUBLIC__/Images/xyicon/'.$v["icon"].'" width="64" /><span>'.$v["name"].'</span></a>';
				}else{
					$h .= "<a href='#'>";
					$sysh .= '<a class="maina" href="#" title="'.$v["name"].'"><img alt="'.$v["name"].'" height="64" src="__PUBLIC__/Images/xyicon/'.$v["icon"].'" width="64" /><span>'.$v["name"].'</span></a>';
				}
				$h .= '<img alt="'.$v["title"].'" height="32" src="__PUBLIC__/Images/xyicon/'.$v["icon"].'" width="32" />';
				$h .= "<span>".$v["name"]."</span><span class=\"xytriangel\"></span></a>";
				$pannels_tree = $this->getIndexTree($v['id'],$nodedata,0,"","",$m_list);
				$h_e="</li>";
				//注释功能盒子加入菜单 by renl
				if($pannels_tree[0]){
					//获取功能盒子
					$functionalBoxmodel=D("MisSystemFunctionalBox");
					$functionalarr=$functionalBoxmodel->where("status=1")->select();
					$functionalBox="";
					foreach ($functionalarr as $key=>$val){
						if($val['groupid'] == $v['id']){
							$functionalBox .= "<li><a title='".$val['name']."' rel='MisSystemFunctionalBox' target='".$val['isblank']."' href='".$val['qlink'].$val['link']."'>";
							if($val['logo']){
								$functionalBox .="<img width='64' height='64' src='__PUBLIC__/Uploads/".$val['logo']."' alt='".$val['name']."'>";
							}else{
								$functionalBox .="<img width='64' height='64' src='__PUBLIC__/Images/xyicon/xyicon_36.png' alt='".$val['name']."'>";
							}
							$functionalBox .="<span>".$val['name']."</span></a></li>";
						}
					}
					$pannels.=$h."<ul class=\"xystartmenu_s_ul clearfix\">".$pannels_tree[0].$functionalBox."</ul>".$h_e;
				}
				$systempanels_tree = $pannels_tree[1];
				$sysh_e="</li>";
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
					$systempanels.=$sysh."<ul class=\"xysubapp clearfix\">".$systempanels_tree.$functionalBox."</ul>".$sysh_e;
				}
			}
			$pannels.= "</ul>";
			$this->assign('pannels',$pannels);
			$systempanels.= "</ul>";
			if($defaultworkbench){
				$this->assign('systempanels',$systempanels);
			}
		}
		// 常用功能
		$uommodel = D("UserOftenMenu");
		$umap['uid'] = $_SESSION[C('USER_AUTH_KEY')];
		$umap['status'] = 1;
		$oftenList = $uommodel->where($umap)->order("createtime asc")->select();
		$access = getAuthAccess();
		foreach ($oftenList as $k => $v) {
			$mdoelarr = explode('/', $v ['url']);
			if(!$mdoelarr[1]) $mdoelarr[1] = 'index';
			if (!isset ($access[strtoupper( APP_NAME )][strtoupper($mdoelarr[0])][strtoupper($mdoelarr[1])]) && !$_SESSION [C('ADMIN_AUTH_KEY')]) {
				unset($oftenList[$k]);
				continue;
			}
		}
		$this->assign('oftenList',$oftenList);
		C ( 'SHOW_RUN_TIME', false ); // 运行时间显示
		C ( 'SHOW_PAGE_TRACE', false );
		//滚动公告
		$model=D('MisRollAnnouncement');
		$list=$model->where("status =1 and typeid=1")->order('createtime desc')->field('content')->select();
		$roll="";
		foreach ($list as $k=>$v){
			$roll.=$v['content']."&nbsp;&nbsp;&nbsp;&nbsp;";
		}
		$this->assign('roll',$roll);
		$this->display();
	}
	private function setURLdata($data){
		//分割后[0]为实际路径,[1]为标签显示名称,[2]对应Model,[3]对应类型 dialog跟navtab 默认是navtabf
		$data = str_replace("%3b", ";", $data);
		$objarr = explode(";", $data);
		$value["url"] = $objarr[0];
		$value["title"] = getFieldBy($objarr[1], "name", "title", "node");
		$value["model"] = $objarr[2];
		if($objarr[3] === "Dialog") {
			$value["target"] = $objarr[3];
		} else {
			$value["target"] = "navTab";
		}
		return json_encode($value);
	}
	/**
	 * @Title: lookupisOpWorkbench
	 * @Description: todo(判断是否有操作工作台权限,并返回权限)
	 * @author jiangx
	 * @date 2013-4-15
	 * @throws
	 */
	public function lookupisOpWorkbench(){
		$node = D('Node');
		$mapnode['name'] = 'OpWorkbench';
		$opVo = $node->where($mapnode)->find();
		$arrtree = array();
		$listnode = $node->where('pid='.$opVo['id'])->select();
		foreach ($listnode as $k => $v) {
			$arrnode = array();
			$v['url'] = $v['name'].'/index';
			$listsubnode = $node->where('pid='.$v['id'].' and showmenu = 1 and status=1')->select();
			if ($listsubnode) {
				foreach ($listsubnode as $k2 => $v2) {
					// 判断权限，转换为小写
					if($_SESSION[strtolower($v['name']).'_'.strtolower($v2['name'])] || $_SESSION["a"] == 1){
						$v2['url'] = $v['name'].'/'.$v2['name'];
						$arrnode['this'][] = $v2;
					}
				}
				if($arrnode){
					$arrnode['pantent'] = $v;
				}
			}
			if($arrnode){
				$arrtree[$arrnode['pantent']['id']] = $arrnode;
			}
		}
		if($arrtree){
			$arremail=array(
					'this' => array(
							'0' => array(
									'id'    => -1,
									'title' => '收件箱',
									'url'   => 'MisMessageInbox/index',
							),
					),
					'pantent' => array(
							'id'    => 0,
							'title' => '邮箱',
							'url'   => ''
					),
			);
			$arrtree[0]=$arremail;
			asort($arrtree);
		}
		return $arrtree;
	}


	/**
	 * @Title: getIndexTree
	 * @Description: todo(构造首页菜单)
	 * @param $group_id 分组ID
	 * @param $nodedata 节点数据
	 * @param $pid 父节点
	 * @param $pname 父节点名称
	 * @param $ptitle 父节点标题
	 * @param $m_list 授权验证
	 * @return Ambigous <string, unknown> 首页菜单
	 * @author 杨东
	 * @date 2013-4-2 下午4:16:48
	 * @throws
	 */
	private function getIndexTree($group_id,$nodedata,$pid=0,$pname="",$ptitle="",$m_list){
		$html="";
		$html2="";
		$node = D( "Node" );
		$access = getAuthAccess();
		$Skip_System_Out_List = require DConfig_PATH."/System/SkipSystemOutListConfig.inc.php";
		foreach($nodedata as $k => $v) {
			if($v['group_id'] !=$group_id) continue;
			if(!$v["icon"]) $v["icon"] = "appbtn_61.png";
			if( $pid ){
				if($pid!=$v['pid'])continue;
			}
			if($v['name']!='Public' && $v['name']!='Index') {
				if($v['type'] == 3 ){
					if($v["pid"]==$pid){
						unset($nodedata[$k]);
						//校验系统模块授权
						if(substr($v['name'], 0, 10)!="MisDynamic"){
							if(!in_array($v['name'], $m_list))  continue;
						}
						if (!isset ($access[strtoupper( APP_NAME )][strtoupper ($v ['name'])]["INDEX"]) && !$_SESSION [C('ADMIN_AUTH_KEY')]) {
							continue;
						}
						//yuansl 2014 06 20替换掉跳转 ,目的跳出系统外部
						foreach($Skip_System_Out_List as $cofli){
							if($v['name'] == $cofli['model']){
								$html.= "<li><a target= \"{$cofli['target']}\" href=\"__APP__".$Skip_System_Out_List[0]['url']."/>";
								break;
							}else{
								$html.= "<li><a target=\"navTab\" rel=\"".$v['name']."\" href=\"__APP__/".$v['name']."/index\">";
								break;
							}
						}
						$html .= '<img alt="'.$v["title"].'" height="32" src="__PUBLIC__/Images/xyicon/'.$v["icon"].'" width="32" />';
						$html .= "<span>".$v["title"]."</span>";
						$html2 .= '<li><a href="__APP__/'.$v['name'].'/index" target="navTab" rel="'.$v['name'].'" title="'.$v['title'].'"><img alt="'.$v['title'].'" height="64" src="__PUBLIC__/Images/xyicon/'.$v['icon'].'" width="64" /><span>'.$v['title'].'</span></a>';
						$map['status'] = 1;
						$map['level'] =4;
						$map['pid'] =$v['id'];
						$map['group_id'] =$group_id;
						$nodedata2 = $node->where($map)->select();
						if($nodedata2){
							$sys_str3 = $this->getIndexTree($group_id,$nodedata2,$v['id'],$v['name'],$v['title'],$m_list);
							$s3_1 = $sys_str3[0];
							if($s3_1){
								$html.="<span class=\"xytriangel\"></span></a><ul class=\"xystartmenu_s_ul clearfix\">";
								$html .=$s3_1;
								$html.="</ul>";
							}
							$syss3_1 = $sys_str3[1];
							if($syss3_1){
								$html2.="<ul class=\"xytriapp clearfix\">";
								$html2 .=$syss3_1;
								$html2.="</ul>";
							}
						}
						$html.="</a></li>";
						$html2.="</a></li>";
					}
				}
				else if($v['type'] == 4 ){
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
						$html.= "<li><a title=".$title." target=\"navTab\" rel=\"".$rel."\" href=\"__APP__/".$pname."/".$url_parem."\">";
						$html .= '<img alt="'.$v["title"].'" height="32" src="__PUBLIC__/Images/xyicon/'.$v["icon"].'" width="32" />';
						$html.= "<span>".$v["title"]."</span>"."</a></li>";
						$html2 .= '<li><a href="__APP__/'.$pname.'/'.$url_parem.'\" target="navTab" rel="'.$rel.'" title="'.$v['title'].'"><img alt="'.$v['title'].'" height="64" src="__PUBLIC__/Images/xyicon/'.$v['icon'].'" width="64" /><span>'.$v['title'].'</span></a></li>';
					}
				}else {
					if($v['type'] == 1 ){
						if($v["toolshow"]==1){
							$s1_1= "<li><a href=\"#\">";
							$s1_1 .= '<img alt="'.$v["title"].'" height="32" src="__PUBLIC__/Images/xyicon/'.$v["icon"].'" width="32" />';
							$s1_1 .= "<span>".$v["title"]."</span>"."<span class=\"xytriangel\"></span></a>";
							$syss1_1 = '<li class="sublist"><a class="suba" href="#" title="'.$v['title'].'"><img alt="'.$v['title'].'" height="64" src="__PUBLIC__/Images/xyicon/'.$v['icon'].'" width="64" /><span>'.$v['title'].'</span></a>';
						}
						unset($nodedata[$k]);
						//$s1_2 = $this->getIndexTree($group_id,$nodedata,$v['id'],"","",$m_list);
						$sys_str = $this->getIndexTree($group_id,$nodedata,$v['id'],"","",$m_list);
						$s1_2 = $sys_str[0];
						$syss1_2 = $sys_str[1];
						if($s1_2){
							if($v["toolshow"]==1 ){
								$html.=$s1_1."<ul class=\"xystartmenu_s_ul clearfix\">".$s1_2."</ul></li>";
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
						if($syss1_2){
							if($v["toolshow"]==1 ){
								$html2.=$syss1_1."<ul class=\"xytriapp clearfix\">".$syss1_2."</ul></li>";
							}else{
								$html2.=$syss1_2;
							}
						}else{
							if($_SESSION [C('ADMIN_AUTH_KEY')] && $syss1_1){
								if($v["toolshow"]==1){
									$html2.=$syss1_1."</li>";
								}
							}
						}
					}else if($v['type'] == 2 ){
						if($v["pid"]==$pid){
							unset($nodedata[$k]);
							if($v["toolshow"]==1){
								$s2_1= "<li><a href=\"#\">";
								$s2_1 .= '<img alt="'.$v["title"].'" height="32" src="__PUBLIC__/Images/xyicon/'.$v["icon"].'" width="32" />';
								$s2_1 .= "<span>".$v["title"]."</span>"."<span class=\"xytriangel\"></span></a>";
								/*$models = M('mis_report_center');
								 $lists = $models->where('type='.$v['id'].' and status = 1')->select();
								if ($lists)  $s2_1.="<ul>";
								foreach ($lists as $ks => $vs) {
								$s2_1 .= "<li><a rel='ReportCenter".$vs[id]."' target='navTab' href='__APP__/ReportCenter/index/reporttype/".$vs[id]."'>".$vs['name']."</a></li>";
								}
								if ($lists)  $s2_1.="</ul>";*/
								$syss2_1= '<li class="sublist"><a class="suba" href="#" title="'.$v['title'].'"><img alt="'.$v['title'].'" height="64" src="__PUBLIC__/Images/xyicon/'.$v['icon'].'" width="64" /><span>'.$v['title'].'</span></a>';
							}
							$sys_str2 = $this->getIndexTree($group_id,$nodedata,$v['id'],"","",$m_list);
							$s2_2 = $sys_str2[0];
							$syss2_2 = $sys_str2[1];
							if($s2_2){
								if($v["toolshow"]==1){
									$html.=$s2_1."<ul class=\"xystartmenu_s_ul clearfix\">".$s2_2."</ul></li>";
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
							if($syss2_2){
								if($v["toolshow"]==1){
									$html2.=$syss2_1."<ul class=\"xytriapp clearfix\">".$syss2_2."</ul></li>";
								}else{
									$html2.=$syss2_2;
								}
							}else{
								if($_SESSION [C('ADMIN_AUTH_KEY')] && $syss2_1){
									if($v["toolshow"]==1){
										$html2.=$syss2_1."</li>";
									}
								}
							}
						}
					}
				}
			}
		}
		return array($html,$html2);
	}
	/**
	 * @Title: getIndexTree_old
	 * @Description: todo(以前用的构造首页菜单函数，做暂存用)
	 * @param $group_id 分组ID
	 * @param $nodedata 节点数据
	 * @param $pid 父节点
	 * @param $pname 父节点名称
	 * @param $ptitle 父节点标题
	 * @param $m_list 授权验证
	 * @return Ambigous <string, unknown> 首页菜单
	 * @author 杨东
	 * @date 2013-4-2 下午4:16:48
	 * @throws
	 */
	
	/**
	 * @Title: userindex
	 * @Description: todo(工作台切换)
	 * @author 杨东
	 * @date 2013-4-1 上午11:48:16
	 * @throws
	 */
	public function lookupuserindex(){
		if($_GET['type']){
			$this->userIndexOperate();
		}
		if(isset($_GET['workbench'])){
			switch ($_GET['workbench']) {
				//yuansl 2014 06 24  新增工功能盒子
				case 4:
					//我的功能盒子
					$this->lookupmyfunctionbox();
					// 					$this->display('lookupmyfunctionbox');
					break;
				case 0:
					//企业工作台
					$this->lookupsystemindex();
					$this->display('lookupsystemindex');
					break;
				case 1:
					$this->userWorkbench();
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
	 *
	 * @Title: lookupmyfunctionbox
	 * @Description: todo(app应用)
	 * @author renling
	 * @date 2014-7-1 下午5:04:53
	 * @throws
	 */
	public function lookupmyfunctionbox(){
		$saveType=6;
		$selectlist = require('./Dynamicconf/System/selectlist.inc.php');
		if(!$_REQUEST['typeid']){
			$_REQUEST['typeid']=1;//默认为第一个
		}
		//查询类型
		$this->assign("apptypelist",$selectlist['apptype']['apptype']);
		$newappList=$this->lookupappList(1);
		if($_REQUEST['child']){
			$this->assign("newappVo",$newappList[$_REQUEST['typeid']]['list'][$_REQUEST['child']]);
		}else{
			$this->assign("newappVo",$newappList[$_REQUEST['typeid']]['list'][$newappList[$_REQUEST['typeid']]['list']['id']]);
		}
		$count=6;
		if($_REQUEST['page']){
			if($_REQUEST['page']=='prevpage'){
				$max=$_REQUEST['maxlimit']-$count;
				$min=$_REQUEST['minlimit']-$count;

			}
			if($_REQUEST['page']=='nextpage'){
				$max=$_REQUEST['maxlimit']+$count;
				$min=$_REQUEST['minlimit']+$count;
			}
		}else{
			// 默认取值6个
			$min=0;
			$max=$count;
		}
		//判断数组中下一组是否还有数据
		if($_REQUEST['typeid']<$saveType){
			$this->assign("nextlist",count($newappList[$_REQUEST['typeid']]['list'])>$max);
			$this->assign("prevlist",$userAppVo[$_REQUEST['typeid']]['list']['id']<$min);
		}else{
			$this->assign("nextlist",$newappList[$_REQUEST['typeid']]['list'][$max]);
			$this->assign("prevlist",$newappList[$_REQUEST['typeid']]['list'][$min-1]);
		}
		$this->assign("maxcount",$max);
		$this->assign("mincount",$min);
		$cnewapplist=array();
		foreach ($newappList[$_REQUEST['typeid']]['list'] as $apkey=>$apval){
			if($apval['pageid']>$min&&$apval['pageid']<=$max &&!$userAppVo[$_REQUEST['typeid']]['list'][$apkey]['del']){
				$cnewapplist[$_REQUEST['typeid']]['list'][$apkey]=$apval;
			}
		}
		$this->assign("typeid",$_REQUEST['typeid']);
		$this->assign("newappList",$cnewapplist);
		if($_REQUEST['page']){
			$this->display("apppage");
		}else if(isset($_REQUEST['child'])){
			$this->display("appdis");
		}else{
			$this->display("lookupmyfunctionbox");
		}
	}
	public function serizeAppList(){
		//组合数组
		$applist = require('./Dynamicconf/System/appconfig.inc.php');
		foreach ($applist as $key=>$val){
			$typearr=array();
			if($val['show']==1 && !$val['isnew']){
				if(in_array($val['type'], array_keys($newappList))){
					$newappList[$val['type']]['list'][] = array(
							'title'=>$val['title']
					);
				}else{
					$newappList[$val['type']]['list'][]= array(
							'title'=>$val['title']
					);
				}
			}
		}
		return  $newappList;
	}


	/**
	 * @Title: opWorkbench
	 * @Description: todo(操作工作台数据获取)
	 * @author jiangx
	 * @date 2013-4-10 下午16:11
	 * @throws
	 * //默认收件箱
		$arremail = array(
			
		);
	 */
	private function opWorkbench($isopWorkbench){
		//获取第二级Cookie
		$opworkbenchoption = Cookie::get("opworkbenchoption");
		//获取第三极cookie
		$subopworkbenchoption = Cookie::get("subopworkbenchoption");
		//默认模板
		//如果二级没值，默认邮箱->收件箱
		if(!$opworkbenchoption){
			$opworkbenchoption = 0;
			$subopworkbenchoption = -1;
			$defaulttemplet = 'MisMessageInbox/index';
		}
		if($opworkbenchoption) {
			if($subopworkbenchoption and $subopworkbenchoption>0){
				foreach($isopWorkbench[$opworkbenchoption]['this'] as $val){
					if($val['id'] == $subopworkbenchoption){
						$defaulttemplet =$val['url'];
						break;
					}
				}
			}else{
				$subopworkbenchoption = $isopWorkbench[$opworkbenchoption]['this'][0]['id'];
				$defaulttemplet = $isopWorkbench[$opworkbenchoption]['this'][0]['url'];
			}
		}
		$this->assign('defaulttemplet',$defaulttemplet);
		$this->assign('opworkbenchoption',$opworkbenchoption);
		$this->assign('subopworkbenchoption',$subopworkbenchoption);
	}

	/**
	 * @Title: userWorkbench
	 * @Description: todo(个人工作台数据获取)
	 * @author 杨东
	 * @date 2013-3-30 下午2:29:40
	 * @throws
	 */
	private function userWorkbench(){
		$uommodel = D("UserOftenMenu");
		$umap['uid'] = $_SESSION[C('USER_AUTH_KEY')];
		$umap['status'] = 1;
		$oftenList = $uommodel->where($umap)->order("createtime asc")->select();
		$access = getAuthAccess();
		foreach ($oftenList as $k => $v) {
			$mdoelarr = explode('/', $v ['url']);
			if(!$mdoelarr[1]) $mdoelarr[1] = 'index';
			if (!isset ($access[strtoupper( APP_NAME )][strtoupper($mdoelarr[0])][strtoupper($mdoelarr[1])]) && !$_SESSION [C('ADMIN_AUTH_KEY')]) {
				unset($oftenList[$k]);
				continue;
			}
		}
		$this->assign('oftenList',$oftenList);
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
	/**
	 * @Title: userIndexOperate
	 * @Description: todo(userIndex页面的操作调用函数)
	 * @author 杨东
	 * @date 2013-3-30 下午2:09:53
	 * @throws
	 */
	private function userIndexOperate(){
		if($_GET['type'] == 1) {
			//常用功能
			$uommodel = D("UserOftenMenu");
			$umap['uid'] = $_SESSION[C('USER_AUTH_KEY')];
			$umap['status'] = 1;
			$workoftenList = $uommodel->where($umap)->order("createtime asc")->select();
			$file =  DConfig_PATH."/AccessList/access_".$_SESSION[C('USER_AUTH_KEY')].".php";
			$access = $_SESSION[C('ADMIN_AUTH_KEY')]? array():require $file;
			foreach ($workoftenList as $k => $v) {
				$mdoelarr = explode('/', $v ['url']);
				if(!$mdoelarr[1]) $mdoelarr[1] = 'index';
				if (!$access[strtoupper( APP_NAME )][strtoupper($mdoelarr[0])][strtoupper($mdoelarr[1])] && !$_SESSION [C('ADMIN_AUTH_KEY')]) {
					unset($workoftenList[$k]);
					continue;
				}
			}
			$this->assign('oftenListCount',count($workoftenList));
			$oftenconfiglist=require DConfig_PATH."/System/oftenconfig.inc.php";
			foreach ($oftenconfiglist as $key=>$val){
				if($val['type']==1){//1为首页默认常用   2为工作中心常用
					$defaultoftenlist[]=$val;
				}
			}
			if($workoftenList){
				if(count($workoftenList)<7){
					//算出差数
					$count=7-(count($workoftenList));
					//去除重复数据
					foreach ($workoftenList as $wkey=>$wval){
						$relList[]=$wval['rel'];
					}
					$i=0;
					foreach ($defaultoftenlist as $dkey=>$dval){
						if(in_array($dval['rel'],$relList)){
							unset($defaultoftenlist[$dkey]);
						}else{
							if($i<=$count){
								$workoftenList[]=$dval;
							}
							$i++;
						}
					}
				}
			}else{
				//默认常用图标
				$workoftenList=$defaultoftenlist;
			}
			$this->assign('oftenList',$workoftenList);
			//加载常用功能页面
			$this->display('oftenindex');
			exit;
		} elseif ($_GET['type'] == 2) {
			//弹出新增常用功能页面
			$this->oftenadd();
			exit;
		} elseif ($_GET['type'] == 3) {
			//删除常用功能
			$uommodel = D("UserOftenMenu");
			$uommap['id'] = $_POST['id'];
			$uommodel->startTrans();
			$res = $uommodel->where($uommap)->delete();
			$uommodel->commit();
			echo $res;
			exit;
		} elseif ($_GET['type'] == 4) {
			// 查询常用功能列表
			//获取系统授权
			$model_syspwd=D('SerialNumber');
			$modules_sys=$model_syspwd->checkModule();
			$m_list = explode(",",$modules_sys);
			$oftenList = $this->oftenaddtree($_GET['gid'],$m_list);
			$this->assign('oftenList',$oftenList);
			$this->display('oftenlist');
			exit;
		} elseif ($_GET['type'] == 5) {
			//新增常用功能
			$uommodel = D("UserOftenMenu");
			//查询当前用户是否已添加6个常用
			$umap['uid'] = $_SESSION[C('USER_AUTH_KEY')];
			$umap['status'] = 1;
			$workoftenList = $uommodel->where($umap)->order("createtime asc")->select();
			if(count($workoftenList)>6){
				$this->error("常用菜单已存在7个,请删除后再添加！");
				exit;
			}
			//新增常用功能
			$url = $_POST['url'];
			$list = $uommodel->where("uid='".$_SESSION[C('USER_AUTH_KEY')]."' and url='".$_POST['url']."'")->count('*');
			if($list){
				$this->error("已经存在相同菜单！");
				exit;
			}
			$nodeModel = D('Node');
			$nodeArr = explode('/', $url);
			$nodeVO = $nodeModel->where("name = '".$nodeArr[0]."'")->find();
			if(!$_POST['rel'] || !$_POST['title'] || !$nodeVO['icon'] || !$url){
				$this->error("常用菜单添加失败！");
				exit;
			}
			$data['uid'] = $_SESSION[C('USER_AUTH_KEY')];
			$data['rel'] = $_POST['rel'];
			$data['title'] = $_POST['title'];
			$data['url'] = $url;
			$data['icon'] = $nodeVO['icon'];
			$data['createtime'] = time();
			$uommodel->startTrans();
			$uommodel->data($data);
			$res = $uommodel->add($data);
			$uommodel->commit();
			if ($res!==false) { //保存成功
				$this->success ( L('_SUCCESS_'));
			} else {
				$this->error ( L('_ERROR_') );
			}
			exit;
		}elseif ($_GET['type'] == 6) {//功能盒子添加常用
			//新增常用功能
			$uommodel = D("UserOftenMenu");
			$url = $_POST['url'];
			$list = $uommodel->where("uid='".$_SESSION[C('USER_AUTH_KEY')]."' and url='".$_POST['url']."'")->count('*');
			if($list){
				$this->error("已经存在相同菜单！");
				exit;
			}
			$data['uid'] = $_SESSION[C('USER_AUTH_KEY')];
			$data['rel'] = $_POST['rel'];
			$data['title'] = $_POST['title'];
			$data['url'] = $url;
			$data['icon'] = $_POST['icon'];
			$data['target'] = $_POST['target'];
			$data['createtime'] = time();
			$uommodel->startTrans();
			$uommodel->data($data);
			$res = $uommodel->add($data);
			$uommodel->commit();
			if ($res!==false) { //保存成功
				$this->success ( L('_SUCCESS_'));
			} else {
				$this->error ( L('_ERROR_') );
			}
			exit;
		}
	}

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
	/**
	 * @Title: oftenadd
	 * @Description: todo(常用功能添加)
	 * @author 杨东
	 * @date 2013-3-11 下午3:33:02
	 * @throws
	 */
	private function oftenadd(){
		$model = M("group");
		$list = $model->where("status=1")->order("sorts asc")->select();
		//获取系统授权
		$model_syspwd=D('SerialNumber');
		$modules_sys=$model_syspwd->checkModule();
		$m_list = explode(",",$modules_sys);
		$tree[] = array(
				'id'=>0,
				'pId'=> -1,
				'name'=> '功能组',
				'title'=>'功能组',
				'open'=>true
		);
		$oftenList = array();
		$valid = '';
		foreach ($list as $k => $v) {
			$oftenaddtree = $this->oftenaddtree($v['id'],$m_list);
			if ($oftenaddtree) {
				$tree[] = array(
						'id'=>$v['id'],
						'pId'=> 0,
						'name'=>$v['name'],
						'title'=>$v['name'],
						'rel'=>'indexOften',
						'target'=>'ajax',
						'url'=>'__URL__/lookupuserindex/type/4/gid/'.$v['id'],
						'open'=>true
				);
				if(!$oftenList){
					$oftenList = $oftenaddtree;
					$valid = $v['id'];
				}
			}
		}
		$this->assign('valid',$valid);
		$this->assign('oftenList',$oftenList);
		$this->assign('tree', json_encode($tree));
		$this->assign("mdname",$_REQUEST['mdname']);
		$this->display('oftenadd');
	}
	private function oftenaddtree($group_id = 1,$m_list){
		$access = getAuthAccess();
		$uommodel = D("UserOftenMenu");
		$umap['uid'] = $_SESSION[C('USER_AUTH_KEY')];
		$umap['status'] = 1;
		$oftenList = $uommodel->where($umap)->getField('url',true);
		$node = D( "Node" );
		$map['status'] = 1;
		$map['showmenu'] = 1;
		$map['type'] = 3;
		$map['group_id'] = $group_id;
		$list = $node->where($map)->order('sort asc')->select();
		$tree = array();
		foreach ($list as $k => $v) {
			if($v['name']!="Index"){
				if(substr($v['name'], 0, 10)!="MisDynamic"){
					if(!in_array($v['name'], $m_list))  continue;
				}
				if (!isset ($access[strtoupper( APP_NAME )][strtoupper ($v ['name'])]) && !$_SESSION [C('ADMIN_AUTH_KEY')]) {
					continue;
				}
				if (in_array($v['name']."/index", $oftenList)) continue;
				$tree[] = $v;
			}
		}
		//查询功能盒子
		$FunctionalBoxmodel=D("MisSystemFunctionalBox");
		$Functionalarr=$FunctionalBoxmodel->where("status=1 and groupid=".$group_id)->select();
		$this->assign("Functionalarr",$Functionalarr);
		return $tree;
	}
	/**
	 * @Title: lookupsystemindex
	 * @Description: todo(个人界面)
	 * @author 杨东
	 * @date 2013-3-15 上午11:54:07
	 * @throws
	 */
	public function lookupsystemindex(){
		//常用功能
		$uommodel = D("UserOftenMenu");
		$umap['uid'] = $_SESSION[C('USER_AUTH_KEY')];
		$umap['status'] = 1;
		$workoftenList = $uommodel->where($umap)->order("createtime asc")->select();
		$file =  DConfig_PATH."/AccessList/access_".$_SESSION[C('USER_AUTH_KEY')].".php";
		$access = $_SESSION[C('ADMIN_AUTH_KEY')]? array():require $file;
		foreach ($workoftenList as $k => $v) {
			$mdoelarr = explode('/', $v ['url']);
			if(!$mdoelarr[1]) $mdoelarr[1] = 'index';
			if (!$access[strtoupper( APP_NAME )][strtoupper($mdoelarr[0])][strtoupper($mdoelarr[1])] && !$_SESSION [C('ADMIN_AUTH_KEY')]) {
				unset($workoftenList[$k]);
				continue;
			}
		}
		$this->assign('oftenListCount',count($workoftenList));
		//读取配置文件
		$defaultoftenlist=array();
		$oftenconfiglist=require DConfig_PATH."/System/oftenconfig.inc.php";
		foreach ($oftenconfiglist as $key=>$val){
			if($val['type']==1){//1为首页默认常用   2为工作中心常用
				$defaultoftenlist[]=$val;
			}
		}
		if($workoftenList){
			if(count($workoftenList)<7){
				//算出差数
				$count=7-(count($workoftenList));
				//去除重复数据
			 foreach ($workoftenList as $wkey=>$wval){
			 	$relList[]=$wval['rel'];
			 }
			 $i=1;
			 foreach ($defaultoftenlist as $dkey=>$dval){
			 	if(in_array($dval['rel'],$relList)){
			 		unset($defaultoftenlist[$dkey]);
			 	}else{
			 		if($i<=$count){
			 			$workoftenList[]=$dval;
			 		}
			 		$i++;
			 	}
			 }
			}
		}else{
			//默认常用图标
			$workoftenList=$defaultoftenlist;
		}
		$this->assign('workoftenList',$workoftenList);
		//循环模块显示
		$panelmethod  = A("MisSystemPanelMethod");
		$panelmethod->index();
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
	/**
	 * @Title: toolBox 工具箱
	 * @Description: todo(下载相关系统工具)
	 * @author eagle
	 * @date 2014-03-21 下午5:23:47
	 * @throws
	 */
	function lookupToolBox(){
		//显示模板
		$this->display();
	}

	/**
	 * @Title: setDbhaSmsgType
	 * @Description: todo(右下角系统提示信息点击确定后操作)
	 * @author jiangx
	 * @date 2013-7-9
	 * @throws
	 */
	public function setDbhaSmsgType(){
		// 		foreach ($_POST['datalist'] as $key => $val) {
		// 			$model = D($key);
		// 			$model->where('id in ( '. $val .' )')->setField('isread', 1);
		// 		}
		// 		$this->transaction_model->commit();//事务提交
		$data = getTaskulous();
		$_SESSION['popupTaskulous'] = md5(serialize($data));
		exit('1');
	}
	/**
	 * @Title: getAllScheduleList
	 * @Description: todo(右下角系统提示信息)
	 * @author jiangx
	 * @date 2013-7-9
	 * @throws
	 */
	public function getAllScheduleList(){
		//任务预超期提醒开始：夏凤琴7200表示的是任务提前两个小时提醒
		/* $ismistaskmsg = true; //表示要提醒
		 if ($_SESSION['Mis_Task_Beforehand_Exceed']) {
		if(($time - $_SESSION['Mis_Task_Beforehand_Exceed']) < 7200){
		$ismistaskmsg = false;
		}
		}
		if ($ismistaskmsg) {
		$Mis_Task_Map = array();
		$Mis_Task_Map['executeuser'] = $_SESSION[C('USER_AUTH_KEY')];
		$Mis_Task_Map['executingstatus'] = array('not in','7,6');
		$model = D('MisTaskInformationView');
		$resultMisTask = $model->where($Mis_Task_Map)->select();
		foreach ($resultMisTask as  $key=>$value) {
		$nowM = $value['enddate']-time();
		if ($nowM <= 7200) {
		$recipientListID = array($value['executeuser'],$value['trackuser'],$value['createid']);
		$messageContent = '<p style="width: auto;float:none;">您好！</p><br/>';
		$messageTitle = "任务预超期提醒";
		if ($nowM <0) {
		$messageTitle = "任务超期提醒";
		$messageContent .= '<p style="width: auto;float:none;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;任务“'. $value['title'] .'”，已经超期，请知晓 。</p><br/>';
		}else {
		$messageTitle = "任务预超期提醒";
		$messageContent .= '<p style="width: auto;float:none;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;任务“'. $value['title'] .'”，即将超期，请知晓 。</p><br/>';
		}
		$messageContent .= '
		<p style="width: auto;float:none;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>任务详细情况：</strong></p>
		<p style="width: auto;float:none;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>任务名称：</strong>'. $value['title'] .'</p><br/>
		<p style="width: auto;float:none;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>任务负责人员：</strong>' . getFieldBy($value['executeuser'],'id','name','User') . '</p><br/>
		<p style="width: auto;float:none;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>任务跟踪人员：</strong>' . getFieldBy($value['trackuser'],'id','name','User') . '</p><br/>
		<p style="width: auto;float:none;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>计划开始时间：</strong>' . date('Y-m-d',$value['begindate']) . '</p><br/>
		<p style="width: auto;float:none;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>计划结束时间：</strong>' . date('Y-m-d',$value['enddate']) . '</p><br/>
		<p style="width: auto;float:none;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>任务描述：</strong>' . ($value['remark'] ? $value['remark'] : "(无)") . '</p><br/>
		<p style="width: auto;float:none;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;如果您有任何疑问，请联系' . getFieldBy($value['createid'],'id','name','User') . '。</p><br/>
		<p style="width: auto;float:none;">&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>';
		$yesOrNo = $this->pushMessage($recipientListID,$messageTitle,$messageContent);
		if ($yesOrNo){
		$_SESSION['Mis_Task_Beforehand_Exceed'] = time();
		}
		}
		}
		} */
	
		//任务预超期提醒结束：夏凤琴
		$usermodel =D("User");
		$hasmsg = $usermodel->where("id=".$_SESSION [C ( 'USER_AUTH_KEY' )])->field('isnewmsg')->find();
		if ($hasmsg['isnewmsg'] == 0) {
			$rehtml["html"]= 0;
			$rehtml['date'] = $hasmsg;
			$rehtml['datalist'] = 0;
			echo json_encode($rehtml);
			exit;
		}
		$html = '';
		$moduleNameList=array();
		// 			$file =  DConfig_PATH . "/System/ProcessModelsConfig.inc.php";
		// 			$arr = require $file;
		$arr=getTaskulous();
		$scheduleList = array();
		$datalist = array();
		$data = getTaskulous();
		$md5 = md5(serialize($data));
		if($_SESSION['popupTaskulous'] !== $md5){
			unset($_SESSION['popupTaskulous']);
			//待办事项
			foreach( $arr as $k=>$v ){
				if(!in_array($v['tablename'], array_keys($moduleNameList))){
					// 					if ($_SESSION["a"] == 1 ||  $_SESSION[strtolower($v['tablename'])."_waitaudit"]) {
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
						$new['href'] = __APP__."/".$v['tablename']."/index/default/2";
						$new['count'] = count($idarr);
						//$datalist[$v['model']] = '0';
						//foreach ($idarr as $val) {
						//	$datalist[$v['model']] .= "," . $val;
						//}
						$scheduleList[] = $new;
					}
					// 					}
				}
			}
		}
		if ($scheduleList) {
			$this->assign('msgscheduleList',$scheduleList);
			$html = $this->fetch("sysgmsgschedule");
			$rehtml["html"]= $html;
			$rehtml['date'] = $hasmsg;
			$rehtml['datalist'] = 0;//$datalist;
			echo json_encode($rehtml);
			exit;
		}
	}
	/**
	 *
	 * @Title: mistaskinfo
	 * @Description: todo(任务管理展示)
	 * @author renling
	 * @date 2014-7-4 下午2:06:50
	 * @throws
	 */
	public function mistaskinfo(){
		//判断权限 任务
		$mistaskModel = D("MisTaskInformationView");
		$map = array();
		$map['executingstatus'] = array('NEQ', 7);
		$map['status'] = 1;
		$mistaskinfo = array();
		if ($_REQUEST['mistasktype'] == 2) {
			$map['executeuser'] = $_SESSION[C('USER_AUTH_KEY')];
			if ($_SESSION['a'] || $_SESSION['mistaskadscription_edit']) {
				$mistaskinfo  = $mistaskModel->where($map)->field('id,title,createtime')->order('taskid DESC')->limit(7)->select();
			}
		} else {
			$map['createid'] = $_SESSION[C('USER_AUTH_KEY')];
			if($_SESSION['a'] || $_SESSION['mistaskinformation_edit']){
				$mistaskinfo  = $mistaskModel->where($map)->field('id,title,createtime')->order('taskid DESC')->limit(7)->select();
			}
		}
		foreach ($mistaskinfo as $key => $val) {
			$mistaskinfo[$key]['title'] = missubstr($val['title'], 60, true);
		}
		$this->assign('mistaskinfo', $mistaskinfo);
		$this->assign('mistasktype', $_REQUEST['mistasktype']);
		if ($_REQUEST['mistasktype']) {
			$this->display();
		}
	}	
}
?>