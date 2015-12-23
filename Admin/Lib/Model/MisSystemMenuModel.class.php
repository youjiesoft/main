<?php
/**
 * @Title: MisSystemMenuModel 
 * @Package package_name 
 * @Description: todo(系统菜单模型) 
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-9-1 下午3:49:05 
 * @version V1.0
 */
class MisSystemMenuModel extends CommonModel{
	
	/**
	 * @Title: getSysGroupList
	 * @Description: todo(生成系统首页菜单分组方法)
	 * @author liminggang
	 * @date 2014-8-29 上午10:56:35
	 * @throws
	 */
	public function getSysGroupList(){
		$pannels = "";
		// 实例化换成模型
		$mMisRuntimeData = D ( 'MisRuntimeData' );
		// 从当前登录用户获取group的换成
		$pannels = $mMisRuntimeData->getRuntimeCache ( "Group", 'grouplist' );
		if (empty ( $pannels )) {
			import ( '@.ORG.RBAC' );
			$groupList = RBAC::getFileGroupAccessList ();
			// 查询菜单分组
			$model = M ( "group" );
			// 管理员排除验证
			if (! isset ( $_SESSION ['a'] )) {
				$map ['status'] = 1;
				if ($groupList) {
					$map ['id'] = array ( " in ", $groupList);
				} else {
					$map ['id'] = 0;
				}
			}else{
				$map ['status'] = array('gt',0);
			}
			$list = $model->where ( $map )->order ( "sorts asc" )->select ();
			if (isset ( $_SESSION [C ( 'USER_AUTH_KEY' )] )) {
				$i=0;
				$h .= "<ul class=\"clearfix\">";
				foreach ( $list as $k => $v ) {
					$i++;
					$h .= "<li>";
					$sysh = "<li class=\"mainlist\">";
					if (! $v ["icon"])
						$v ["icon"] = "appbtn_61.png";
					if ($v ['indexlink']) {
						//$h .= "<a href='__APP__/Public/nvigateTO/groupid/" . $v ["id"] . "' target='navTab' rel='" . $v ["name"] . "'>";
						$h .= "<a href='__APP__/Common/nvigateTO/groupid/" . $v ["id"] . "' target='navTab' rel='" . $v ["name"] . "'>";
						//$sysh .= '<a href="#" url="__APP__/Public/nvigateTO/id/' . $v ["id"] . '" targets="navTab" rel="' . $v ["name"] . '" title="' . $v ["name"] . '"><img alt="' . $v ["name"] . '" height="64" src="__PUBLIC__/Images/xyicon/' . $v ["icon"] . '" width="64" /><span>' . $v ["name"] . '</span></a>';
						$sysh .= '<a href="#" url="__APP__/Common/nvigateTO/id/' . $v ["id"] . '" targets="navTab" rel="' . $v ["name"] . '" title="' . $v ["name"] . '"><img alt="' . $v ["name"] . '" height="64" src="__PUBLIC__/Images/xyicon/' . $v ["icon"] . '" width="64" /><span>' . $v ["name"] . '</span></a>';
					} else {
						$h .= "<a href='#'>";
						$sysh .= '<a class="maina" href="#" title="' . $v ["name"] . '"><img alt="' . $v ["name"] . '" height="64" src="__PUBLIC__/Images/xyicon/' . $v ["icon"] . '" width="64" /><span>' . $v ["name"] . '</span></a>';
					}
					$h .= '<img alt="' . $v ["title"] . '" height="32" src="__PUBLIC__/Images/xyicon/' . $v ["icon"] . '" width="32" />';
					$h .= "<span>" . $v ["name"] . "</span></a>";
					$h .= "</li>";
				}
				$h .= "</ul>";
				if($i>0){
					$pannels .= $h;
				}
			}
			// 如果pannels不为空，就写入当前用户换成中
			if ($pannels) {
				$mMisRuntimeData->setRuntimeCache ( $pannels, "Group", 'grouplist' );
			}
		}
		return $pannels;
	}
	
// 	public function getSysGroupList(){
// 		//查询菜单分组
// 		$model=M("group");
// 		$list = $model->where("status=1")->order("sorts asc")->select();
// 		//获取除了操作节点以为的节点信息
// 		$node = D( "Node" );
// 		$map['status'] = 1; //状态正常
// 		$map['showmenu'] = 1; //模板显示
// 		$map['level'] =array("lt",4); //除操作节点外
// 		$nodedata = $node->where($map)->order("sort asc")->select();
// 		//获取系统授权模板
// 		$model_syspwd=D('SerialNumber');
// 		$modules_sys=$model_syspwd->checkModule();
// 		$m_list = explode(",",$modules_sys);
		
// 		$pannels ="";
// 		if(isset($_SESSION[C('USER_AUTH_KEY')])) {
// 			$pannels="<ul class=\"clearfix\">";
// 			foreach($list as $k =>$v){
// 				$h="<li>";
// 				$sysh="<li class=\"mainlist\">";
// 				if(!$v["icon"]) $v["icon"] = "appbtn_61.png";
// 				if( $v['indexlink'] ){
// 					$h .= "<a href='__APP__/Public/nvigateTO/groupid/".$v["id"]."' target='navTab' rel='".$v["name"]."'>";
// 					$sysh .= '<a href="#" url="__APP__/Public/nvigateTO/id/'.$v["id"].'" targets="navTab" rel="'.$v["name"].'" title="'.$v["name"].'"><img alt="'.$v["name"].'" height="64" src="__PUBLIC__/Images/xyicon/'.$v["icon"].'" width="64" /><span>'.$v["name"].'</span></a>';
// 				}else{
// 					$h .= "<a href='#'>";
// 					$sysh .= '<a class="maina" href="#" title="'.$v["name"].'"><img alt="'.$v["name"].'" height="64" src="__PUBLIC__/Images/xyicon/'.$v["icon"].'" width="64" /><span>'.$v["name"].'</span></a>';
// 				}
// 				$h .= '<img alt="'.$v["title"].'" height="32" src="__PUBLIC__/Images/xyicon/'.$v["icon"].'" width="32" />';
// 				$h .= "<span>".$v["name"]."</span><span class=\"xytriangel\"></span></a>";
// 				$pannels_tree = $this->getIndexTree($v['id'],$nodedata,0,"","",$m_list);
// 				$h_e="</li>";
// 				//注释功能盒子加入菜单 by renl
// 				if($pannels_tree[0]){
// 					//获取功能盒子
// 					$functionalBoxmodel=D("MisSystemFunctionalBox");
// 					$functionalarr=$functionalBoxmodel->where("status=1")->select();
// 					$functionalBox="";
// 					foreach ($functionalarr as $key=>$val){
// 						if($val['groupid'] == $v['id']){
// 							$functionalBox .= "<li><a title='".$val['name']."' rel='MisSystemFunctionalBox' target='".$val['isblank']."' href='".$val['qlink'].$val['link']."'>";
// 							if($val['logo']){
// 								$functionalBox .="<img width='64' height='64' src='__PUBLIC__/Uploads/".$val['logo']."' alt='".$val['name']."'>";
// 							}else{
// 								$functionalBox .="<img width='64' height='64' src='__PUBLIC__/Images/xyicon/xyicon_36.png' alt='".$val['name']."'>";
// 							}
// 							$functionalBox .="<span>".$val['name']."</span></a></li>";
// 						}
// 					}
// 					$pannels.=$h."<ul class=\"xystartmenu_s_ul clearfix\">".$pannels_tree[0].$functionalBox."</ul>".$h_e;
// 				}
// 				$systempanels_tree = $pannels_tree[1];
// 				$sysh_e="</li>";
// 				if($systempanels_tree){
// 					//获取功能盒子
// 					$functionalBoxmodel=D("MisSystemFunctionalBox");
// 					$functionalarr=$functionalBoxmodel->where("status=1")->select();
// 					$functionalBox="";
// 					foreach ($functionalarr as $key=>$val){
// 						if($val['groupid'] == $v['id']){
// 							$functionalBox .= "<li><a title='".$val['name']."' rel='MisSystemFunctionalBox' target='navTab' href='".$val['qlink'].$val['link']."'>";
// 							if($val['logo']){
// 								$functionalBox .="<img width='64' height='64' src='__PUBLIC__/Uploads/".$val['logo']."' alt='".$val['name']."'>";
// 							}else{
// 								$functionalBox .="<img width='64' height='64' src='__PUBLIC__/Images/xyicon/xyicon_36.png' alt='".$val['name']."'>";
// 							}
// 							$functionalBox .="<span>".$val['name']."</span></a></li>";
// 						}
// 					}
// 				}
// 			}
// 			$pannels.= "</ul>";
// 			print_r($pannels);
// 			exit;
// 			return $pannels;
// 		}
// 	}
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
}