<?php
/** 
 * @Title: MisWorkExecutingAction 
 * @Package package_name
 * @Description: todo(工作执行) 
 * @author 杨希
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-2-21 上午10:15:21 
 * @version V1.0 
*/ 
class MisSystemWorkCenterAction extends CommonAction{
	
	/**
	 * @Title: getOaItemsList 
	 * @Description: 获取协同数据
	 * @author liminggang 
	 * @date 2014-9-2 下午7:23:02 
	 * @throws
	 */
	protected  function getOaItemsList(){
		//获取查询的对象
		$name =$_REQUEST['md'];
		$this->assign("md",$name);
		$type = $_REQUEST['type'];
		$this->assign('type',$type);
		$this->assign("rel",$_REQUEST['rel']);
		//加载配置文件 判断是否是dialog打开方式
		$dialogList=require DConfig_PATH."/System/dialogconfig.inc.php";
		$this->assign('dialogList',$dialogList);
		//列表过滤器，生成查询Map对象
		$newname='MisAutoShb';
		$map = array();
		$map = $this->_search ($newname);
	
		if($name == 'MisOaItems'){//工作协同
			if($type == 1){//已发
				$map['createid'] = $_SESSION[C('USER_AUTH_KEY')];
			}
			if($type == 2){//待发
				$map['zhixingqingkuang'] = null;
				$map["createid"]=$_SESSION[C('USER_AUTH_KEY')];
			}
			if($type == 4){// 已办
				/* $MisOaFlowsInstanceModel = D("MisOaFlowsInstance");
				$OAwhere = array();
				$OAwhere['flowsuser']=$_SESSION[C('USER_AUTH_KEY')];
				$OAwhere['status']=1;
				$OAwhere['dostatus'] = array('EGT',2);
				$aitemsid = $MisOaFlowsInstanceModel->where($OAwhere)->getField("itemsid",true);
				$aitemsid = array_unique($aitemsid);
				if($aitemsid){
					$map['mis_oa_items.id'] = array('in',$aitemsid);
				} else {
					$map['mis_oa_items.id'] = 0;
				} */
				//$map['zhixingqingkuang'] = array(' not in ',array('1','2'));
				//$map['lingquren']  = $_SESSION [C ( 'USER_AUTH_KEY' )];
				$map['_string']=" (`lingquren`=".$_SESSION [C ( 'USER_AUTH_KEY' )]." and `zhixingqingkuang` NOT IN ('1','2')) or (`faburen`=".$_SESSION [C ( 'USER_AUTH_KEY' )]." and `zhixingqingkuang` IN ('3','5'))";
				
			}
		}
	
		if (! empty ( $name )) {
			if($type == 3 && $_REQUEST['md']=='MisOaItems'){
				// 待办任务列表
				//$map['lingquren']  = $_SESSION [C ( 'USER_AUTH_KEY' )];
				//$map['zhixingqingkuang'] = 2;
				$map['_string']=' (`lingquren`='.$_SESSION [C ( 'USER_AUTH_KEY' )].' and `zhixingqingkuang`=2) or (`faburen`='.$_SESSION [C ( 'USER_AUTH_KEY' )].' and `zhixingqingkuang`=4)';
				$this->_list ( "MisAutoShbView", $map ,"urgentlevel");
			}else{
				//已办事项，已发事项，待发事项
				$this->_list($newname, $map);
			}
		}
	
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($newname);
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		//扩展工具栏操作
		$toolbarextension = $scdmodel->getDetail($name,true,'toolbar');
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		if($_REQUEST['wjump']==1){
			$this->display();exit;
		}
		if($_REQUEST['jump']="jump"){
			$this->display('depindex');exit;
		}
		$this->display ();
	}
	
	/* protected  function getOaItemsList(){
		//获取查询的对象
		$name =$_REQUEST['md'];
		$this->assign("md",$name);
		$type = $_REQUEST['type'];
		$this->assign('type',$type);
		$this->assign("rel",$_REQUEST['rel']);
		//加载配置文件 判断是否是dialog打开方式
		$dialogList=require DConfig_PATH."/System/dialogconfig.inc.php";
		$this->assign('dialogList',$dialogList);
		//列表过滤器，生成查询Map对象
		$map = array();
		$map = $this->_search ($name);
		
		if($name == 'MisOaItems'){//工作协同
			if($type == 1){//已发
				$map['dealstatus'] = 2;
				$map['createid'] = $_SESSION[C('USER_AUTH_KEY')];
			}
			if($type == 2){//待发
				$map['dealstatus'] = 1;
				$map["createid"]=$_SESSION[C('USER_AUTH_KEY')];
			}
			if($type == 4){//待办 已办
				$MisOaFlowsInstanceModel = D("MisOaFlowsInstance");
				$OAwhere = array();
				$OAwhere['flowsuser']=$_SESSION[C('USER_AUTH_KEY')];
				$OAwhere['status']=1;
				$OAwhere['dostatus'] = array('EGT',2);
				$aitemsid = $MisOaFlowsInstanceModel->where($OAwhere)->getField("itemsid",true);
				$aitemsid = array_unique($aitemsid);
				if($aitemsid){
					$map['mis_oa_items.id'] = array('in',$aitemsid);
				} else {
					$map['mis_oa_items.id'] = 0;
				}
			}
		}
		
		if (! empty ( $name )) {
			if($type == 3 && $_REQUEST['md']=='MisOaItems'){
				// 待办任务列表
				$map['mis_oa_flows_instance.flowsuser']  = $_SESSION [C ( 'USER_AUTH_KEY' )];
				$map['mis_oa_flows_instance.dostatus'] = array('lt',2);//默认显示未处理的
				$this->_list ( "MisOaItemsWaitForView", $map ,"urgentlevel");
			}else{
				//已办事项，已发事项，待发事项
				$this->_list($name, $map);
			}
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
		if($_REQUEST['wjump']==1){
			$this->display();exit;
		}
		if($_REQUEST['jump']="jump"){
			$this->display('depindex');exit;
		}
		$this->display ();
	} */
		
	/**
	 * @Title: getWorkoldinfo 
	 * @Description: todo(获取工作已发待批和已发办结数据)   
	 * @author liminggang
	 * @date 2015-10-13 下午5:20:59 
	 * @throws
	 */
	protected function getWorkoldinfo(){
		//获取查询的对象
		$name =$_REQUEST['md'];
		$this->assign("md",$name);
		$worktype = $_REQUEST['worktype'];
		$this->assign('worktype',$worktype);
		$this->assign("rel",$_REQUEST['rel']);
		$this->assign("jump",$_REQUEST['jump']);
		$Model = D($name);
		//查询流程分类信息
		$mis_auto_ziqmiDao = M("mis_auto_ziqmi");
		$typelist = $mis_auto_ziqmiDao->field("id,leixingmingchen")->select();
		$doingtypelist = $typelist;
		$array = array();
		foreach($typelist as $k=>$v){
			//已发待批
			$map = array();
			$map['dostatus'] = 0;
			$map['createid'] = $_SESSION[C('USER_AUTH_KEY')];
			$map['typeid'] = $v['id'];//分类ID
			$list = $Model->where($map)->group("tablename,tableid")->field("id,tablename,typeid")->select();
			if($list){
				$nodearr = array();
				foreach($list as $kk=>$vv){
					if(in_array($vv['tablename'], array_keys($nodearr))){
						$nodearr[$vv['tablename']]['count']+=1;
					}else{
						$nodetitle = getFieldBy($vv['tablename'], 'name', 'title', 'node');
						$nodearr[$vv['tablename']] = array('tablename'=>$vv['tablename'],'nodename'=>$nodetitle,'count'=>1);
					}
				}
				$typelist[$k]['waitcount'] = $nodearr;
			}else{
				unset($typelist[$k]);
				continue;
			}
		}
		foreach($doingtypelist as $dk=>$dv){
			//已发办结
			$map = array();
			$map['typeid'] = $dv['id'];//分类ID
			$map['auditState'] = 3;
			$map['createid'] = $_SESSION[C('USER_AUTH_KEY')];
			//查询数量
			$list = $Model->where($map)->field("id,tablename,typeid")->select();
			if($list){
				$nodearr = array();
				foreach($list as $kk=>$vv){
					if(in_array($vv['tablename'], array_keys($nodearr))){
						$nodearr[$vv['tablename']]['count']+=1;
					}else{
						$nodetitle = getFieldBy($vv['tablename'], 'name', 'title', 'node');
						$nodearr[$vv['tablename']] = array('tablename'=>$vv['tablename'],'nodename'=>$nodetitle,'count'=>1);
					}
				}
				$doingtypelist[$dk]['overcount'] = $nodearr;
			}else{
				unset($doingtypelist[$dk]);
				continue;
			}
		}
		$this->assign("doingtypelist",$doingtypelist);
		$this->assign("typelist",$typelist);
		if($_REQUEST['jumptemp']){
			$this->display ("lookupWorkComp");
		}else{
			$this->display ();
		}
	}
	
	/**
	 * @Title: getWorkMonitinfo 
	 * @Description: todo(获取分类审批信息数据)   
	 * @author liminggang
	 * @date 2015-8-14 下午5:57:56 
	 * @throws
	 */
	protected function getWorkMonitinfo(){
		//获取查询的对象
		$name =$_REQUEST['md'];
		$this->assign("md",$name);
		$worktype = $_REQUEST['worktype'];
		$this->assign('worktype',$worktype);
		$this->assign("rel",$_REQUEST['rel']);
		$this->assign("jump",$_REQUEST['jump']);
		$Model = D($name);
		//查询流程分类信息
		$mis_auto_ziqmiDao = M("mis_auto_ziqmi");
		$typelist = $mis_auto_ziqmiDao->field("id,leixingmingchen")->select();
		$doingtypelist = $typelist;
		$array = array();
		foreach($typelist as $k=>$v){
			//待我审批
			$map = array();
			$map['dostatus'] = 0;
			$map['isauditstatus'] = 1; //过滤掉已经转了子流程的数据。
			$map['typeid'] = $v['id'];//分类ID
			$map['_string'] = 'FIND_IN_SET(  '.$_SESSION[C('USER_AUTH_KEY')].', curAuditUser )';
			$list = $Model->where($map)->field("id,tablename,typeid")->select();
			if($list){
				$nodearr = array();
				foreach($list as $kk=>$vv){
					if(in_array($vv['tablename'], array_keys($nodearr))){
						$nodearr[$vv['tablename']]['count']+=1;
					}else{
						$nodetitle = getFieldBy($vv['tablename'], 'name', 'title', 'node');
						$nodearr[$vv['tablename']] = array('tablename'=>$vv['tablename'],'nodename'=>$nodetitle,'count'=>1);
					}
				}
				$typelist[$k]['waitcount'] = $nodearr;
			}else{
				unset($typelist[$k]);
				continue;
			}
		}
		foreach($doingtypelist as $dk=>$dv){
			$map = array();
			$map['typeid'] = $dv['id'];//分类ID
			$map['userid'] = $_SESSION[C('USER_AUTH_KEY')];
			//查询数量
			$list = $Model->where($map)->field("id,tablename,typeid")->select();
			if($list){
				$nodearr = array();
				foreach($list as $kk=>$vv){
					if(in_array($vv['tablename'], array_keys($nodearr))){
						$nodearr[$vv['tablename']]['count']+=1;
					}else{
						$nodetitle = getFieldBy($vv['tablename'], 'name', 'title', 'node');
						$nodearr[$vv['tablename']] = array('tablename'=>$vv['tablename'],'nodename'=>$nodetitle,'count'=>1);
					}
				}
				$doingtypelist[$dk]['overcount'] = $nodearr;
			}else{
				unset($doingtypelist[$dk]);
				continue;
			}
		}
		$this->assign("doingtypelist",$doingtypelist);
		$this->assign("typelist",$typelist);
		if($_REQUEST['jumptemp']){
			$this->display ("lookupWork");
		}else{
			$this->display ();
		}
	}
	
	/**
	 * @Title: getWorkMonitList 
	 * @Description: 获取审核信息   
	 * @author liminggang 
	 * @date 2014-9-2 下午7:22:48 
	 * @throws
	 */
	protected function getWorkMonitList(){
		//获取查询的对象
		$name =$_REQUEST['md'];
		$this->assign("md",$name);
		$worktype = $_REQUEST['worktype'];
		$this->assign("tablename",$_REQUEST['tablename']);
		$this->assign('worktype',$worktype);
		$this->assign("rel",$_REQUEST['rel']);
		$this->assign("jump",$_REQUEST['jump']);
		//加载配置文件 判断是否是dialog打开方式
		$dialogList=require DConfig_PATH."/System/dialogconfig.inc.php";
		$this->assign('dialogList',$dialogList);
		
		if($worktype==3 || $worktype==1){
			$detailname="MisMyWorking";
		}else{
			$detailname = $name;
		}
		//列表过滤器，生成查询Map对象
		$map = array();
		$map = $this->_search ($detailname);
		//获取流程分类ID
		$typeid = $_REQUEST['typeid'];
		if($typeid){
			$map['typeid'] = $typeid;
		}
		$map['tablename'] = $_REQUEST['tablename'];
		if($_REQUEST['md'] == 'MisWorkMonitoring'){//工作审批
			if($worktype == 1){//待我审批
				$map['dostatus'] = 0;
				$map['isauditstatus'] = 1; //过滤掉已经转了子流程的数据。
				$map['_string'] = 'FIND_IN_SET(  '.$_SESSION[C('USER_AUTH_KEY')].', curAuditUser )';
			}
			if($worktype == 2){//我已审批
				$map['userid'] = $_SESSION[C('USER_AUTH_KEY')];
			}
			if($worktype == 3){//已发待批
				$map['dostatus'] = 0;
				$map['createid'] = $_SESSION[C('USER_AUTH_KEY')];
				$group = "tablename,tableid";
			}
			if($worktype == 4){//已发办结
				$map['auditState'] = 3;
				$map['createid'] = $_SESSION[C('USER_AUTH_KEY')];
			}
		}

		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($detailname);
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		
		if (! empty ( $name )) {
			$qx_name=$name;
			if(substr($name, -4)=="View"){
				$qx_name = substr($name,0, -4);
			}
			// 			//验证浏览及权限
			// 			if( !isset($_SESSION['a']) ){
			// 				$map=D('User')->getAccessfilter($qx_name,$map);
			// 			}
			//列表页排序 ---开始-----2015-08-06 15:07 write by xyz
			if($_REQUEST['orderField']&&strpos(strtolower($_REQUEST['orderField']),' asc')===false&&strpos(strtolower(strpos($_REQUEST['orderField'])),' desc')===false){
				$this->_list($name, $map,'',false,$group);
			}else{
				$sortsorder = '';
				$sortsmap['modelname'] = $detailname;//搞不懂，乱填的 by---xyz
				$sortsmap['sortsorder'] = array("gt",0);
				//管理员读公共设置
				if($_SESSION['a']){
					$listincModel = M("mis_system_public_listinc");
					$sortslist = $listincModel->where($sortsmap)->order("sortsorder")->select();
				}else{
					//个人先读个人设置、没有再读公共设置
					$sortsmap['userid'] = $_SESSION [C ( 'USER_AUTH_KEY' )];
					$listincModel = M("mis_system_private_listinc");
					$sortslist = $listincModel->where($sortsmap)->order("sortsorder")->select();
					if(empty($sortslist)){
						unset($sortsmap['userid']);
						$listincModel = M("mis_system_public_listinc");
						$sortslist = $listincModel->where($sortsmap)->order("sortsorder")->select();
					}
				}
				//如果在设置里有相关数据、提取排序字段组合order by
				if($sortslist){
					foreach($sortslist as $k=>$v){
						if(is_numeric($v['fieldname'])){
							$v['fieldname'] = $detailList[$v['fieldname']]['name'];
						}
						$sortsorder .= $v['fieldname'].' '.$v['sortstype'].',';
					}
					$sortsorder = substr($sortsorder,0,-1);
				}
				//列表页排序 ---结束-----
				$this->_list ( $name, $map,'', false,$group,'',$sortsorder);
			}
		}
// 		if (! empty ( $name )) {
// 			$this->_list($name, $map,'',false,$group);
// 		}
		
		//扩展工具栏操作
		$toolbarextension = $scdmodel->getDetail($name,true,'toolbar');
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		$this->assign("jumpxrel",$_REQUEST['jumpxrel']);
		if($_REQUEST['jump']=="jump" && !$_REQUEST['jumpxrel']){
			$this->display('depindex');exit;
		}
		
		$this->display ();
		exit;
	}
	
	/**
	 * @Title: getlefttree
	 * @Description: todo(获取左侧树)
	 * @author libo
	 * @date 2014-4-23 下午5:02:26
	 * @throws
	 */
	public  function getlefttree(){
		
		//---------查询工作协同数据-----------------//
		$MisOaFlowsInstanceModel = D("MisOaFlowsInstance");
		$oamapdoing['flowsuser']=$_SESSION[C('USER_AUTH_KEY')];
		$oamapdoing['status']=1;
		$oamapdoing['dostatus'] = array('lt',2);
		$oaitemdoing=$MisOaFlowsInstanceModel->where($oamapdoing)->count();
		$this->assign("oaitemdoing",$oaitemdoing);
		//工作协同数组
		$synergy=array( 
				'0'=>array('id'=>111,'name'=>'待办事项','md'=>'MisOaItems','type'=>3),
				'1'=>array('id'=>112,'name'=>'已办事项','md'=>'MisOaItems','type'=>4),
				'2'=>array('id'=>113,'name'=>'已发事项','md'=>'MisOaItems','type'=>1),
				/* '3'=>array('id'=>114,'name'=>'待发事项','md'=>'MisOaItems','type'=>2) */);
		$this->assign("synergy",$synergy);
		
		//------------end------------------//
		
		//-------------获取带我审批的信息-----------//
		$myworkModel=D("MisWorkMonitoring");
		$mywaitmap['dostatus'] = 0;
		$mywaitmap['_string'] = 'FIND_IN_SET('.$_SESSION[C('USER_AUTH_KEY')].', curNodeUser )';
		$myworkwait=$myworkModel->where($mywaitmap)->count();
		$this->assign('myworkwait',$myworkwait);
		//工作审批数组
		$examine=array( '0'=>array('id'=>111,'name'=>'待我审批','num'=>$myworkwait,'md'=>'MisWorkMonitoring','worktype'=>1),
				'1'=>array('id'=>112,'name'=>'我已审批','num'=>$myworkdone,'md'=>'MisWorkMonitoring','worktype'=>2),
				'2'=>array('id'=>113,'name'=>'已发待批','num'=>'','md'=>'MisWorkMonitoring','worktype'=>3),
				'3'=>array('id'=>114,'name'=>'已发办结','num'=>'','md'=>'MisWorkMonitoring','worktype'=>4));
		$this->assign("examine",$examine);
		//------------end------------------//
		
		//-----------项目执行和分派数据----------------//
		$MisWorkExecutingModel = D("MisWorkExecuting");
		$list=$MisWorkExecutingModel->getUserWorkExecutList();
		$this->assign("arrlist",$list);
		//------------end------------------//
		//工作知会
		$this->getworkinforpersonmessage();
	}
	
	/**
	 * @Title: getworkinforpersonmessage
	 * @Description: todo(获取知会人模块信息)
	 * @author libo
	 * @date 2014-6-11 下午5:51:37
	 * @throws
	 */
	public function getworkinforpersonmessage(){
		$processInfoModel=M("process_info");
		$workModel=D("MisWorkExecuting");
		$uid=$_SESSION[C('USER_AUTH_KEY')];
		$Model = M();
		//查询已阅 的知会信息
		$inforid="";
		$readlist=$workModel->field('id,isread')->where("status=1")->select();
		foreach ($readlist as $k=>$v){
			if(in_array($uid, explode(",", $v['isread']))){
				$inforid .=",".$v['id'];
			}
		}
		if($inforid){//查询条件 排除已阅读的
			$inforidstr=substr($inforid, 1);
		}else{
			$inforidstr="";
		}
		//查询知会人数组 数量
		$map['_string']="( FIND_IN_SET('$uid', mis_work_executing.informpersonid) or FIND_IN_SET('$uid', p.informpersonid))";
		$list=$workModel->where($map)->field("mis_work_executing.tablename,mis_work_executing.informpersonid,p.informpersonid as informpersonidp")->join(" process_info as p on mis_work_executing.ptmptid=p.id")->group("tablename")->select();
		$numsql.="SELECT COUNT(tablename) AS num,mis_work_executing.tablename as name FROM `mis_work_executing` LEFT JOIN process_info as p on mis_work_executing.ptmptid=p.id WHERE ( mis_work_executing.status=1 and ";
		if(!isset($_SESSION['a'])){//如果不是管理员 过滤条件
			$numsql.=" (FIND_IN_SET('$uid', mis_work_executing.informpersonid) or FIND_IN_SET('$uid', p.informpersonid)) and ";
		}
		$numsql.=" mis_work_executing.id not in ($inforidstr) )";
		$numsql.="GROUP BY tablename";
		$numlist=$Model->query($numsql);
		//查询知会人数组 //主要 当该模板下的数据全部已读状态 查的数量就过滤了该模板 页面就不显示
		$listsql.="SELECT mis_work_executing.tablename as name FROM `mis_work_executing` LEFT JOIN process_info as p on mis_work_executing.ptmptid=p.id  ";
		if(!isset($_SESSION['a'])){//如果不是管理员 过滤条件
			$listsql.="WHERE ( (FIND_IN_SET('$uid', mis_work_executing.informpersonid) or FIND_IN_SET('$uid', p.informpersonid)) and mis_work_executing.status=1 )";
		}
		$listsql.="GROUP BY tablename";
		$listinfo=$Model->query($listsql);
		foreach ($numlist as $k=>$v){//
			foreach ($listinfo as $k1=>$v1){
				if($v1['name']==$v['name']){
					$listinfo[$k1]["num"]=$v['num'];
				}
			}
		}
		//显示的全部已读时 数量为0
		foreach ($listinfo as $k=>$v){
			if(!$v['num']){
				$listinfo[$k]["num"]=0;
			}
		}
		$Allinfonum="";
		foreach ($listinfo as $k=>$v){
			$listinfo[$k]["cname"]=getFieldBy($v['name'], "name", "title", "node");
			$Allinfonum += $v['num'];//总数量
		}
		$this->assign("Allinfonum",$Allinfonum);
		$this->assign("arrinfolist",$listinfo);
	}
	
	/**
	 * @Title: lookupNewWork
	 * @Description: todo(无需权限的发起新工作方法)
	 * @author yangxi
	 * @date 2014-6-30 上午9:02:26
	 * @throws
	 */
	public function lookupNewWork(){
		$this->getOftenWork();
		$this->getMyWork();
		$this->display("newWork");
	}
	
	
	/**
	 * @Title: lookupNewWork
	 * @Description: todo(无需权限的发起新工作方法.个人常用功能)
	 * @author yangxi
	 * @date 2014-6-30 上午9:02:26
	 * @throws
	 */
	public function getOftenWork(){
		//初始化我的常用功能存入数组
		$oftenwork=array();
		//直接查询该用户有哪些常用功能
		$oftenModellist=M('mis_user_often_menu')->where('status=1 and uid='.$_SESSION[C('USER_AUTH_KEY')])->select();
		//$oftenconfiglist=require DConfig_PATH."/System/oftenconfig.inc.php";
		//print_r($oftenModellist);
		if($oftenModellist){
			foreach ($oftenModellist as $key=>$val){
				$html="";//初始化html内容
				$html='<a class="often_work_btn" href="__APP__/'.$val['url'].'" target="navTab" rel="'.$val['rel'].'"  title="'.$val['title'].'">';
				//$html.='<img alt="'.$val['title'].'" height="64" src="__PUBLIC__/Images/xyicon/'.$val['icon'].'"; width="64">';
				//$html.='<span>'.$val['title'].'</span>';
				$html.=$val['title'];
				$html.='</a>';
				//增加删除功能
				//$html.='<a id="" class="delapp" href="#" onclick="delOften(this);" title="删除该功能"></a>';
				$oftenwork[$key]['html']=$html;
			}
			$this->assign("isoftenwork","1");
			$this->assign("oftenwork",$oftenwork);
		}else{
			$this->assign("isoftenwork","0");
			$this->assign("oftenwork",$oftenwork);
		}	
	}
	/**
	 * @Title: getMyWork
	 * @Description: todo(获取所有我有权限制单的带审批流的单据)
	 * @author yangxi
	 * @date 2014-6-30 上午9:02:26
	 * @throws
	 */
	protected function getMyWork(){
		if($_SESSION[C('ADMIN_AUTH_KEY')]) return array();
		$authId = $authId ? $authId:$_SESSION[C('USER_AUTH_KEY')];
		$AccessFile =  DConfig_PATH."/AccessList/access_".$authId.".php";
		//获取当前用户全部权限
		$access =require $AccessFile;
		//print_R($access);
		//获取表单列表相关信息
		$SystemFile=DConfig_PATH."/System/list.inc.php";
		require $SystemFile;
		$work=array();//所有工作
		$dowork=array();//可制单工作
		$tmpArray=array();//临时数组存储
		$num=0;//初始化循环计数
		foreach($aryRule as $key => $val){
			if($val['isprocess']==1){
				$modelname= strtoupper(preg_replace('/_/','',$key));
				$work[]=$modelname;
			}
		}
		$nodeModel= D("Node");
		$scdModel = D('SystemConfigDetail');
		foreach($access['ADMIN'] as $key => $val){
			if(in_array($key,$work)){
				//只核验用户的ADD方法
				if(isset($val['ADD'])){
					$where1="status=1  and upper(name)='".$key."'";
					$return1= $nodeModel->where($where1)->field('pid,name,title,icon')->find();
					//获取操作方法
					$toolbarextension = $scdModel->getDetail($return1['name'],true,'toolbar');
					//拼装数据都在这里维护
					//替换新增方法为可识别的路径
					$model=$return1['name'];
					$html="";//初始化html内容
					$html=str_replace('__URL__','__APP__/'.$return1['name'],$toolbarextension['js-add']['html']);
					$html=str_replace('新增','',$html);
					$html=preg_replace('/<\\/a>/','',$html);//去掉过去的a标签
	
					$html=preg_replace('/\<span[^>]*?>.*?<\/span[^>]*\>/','',$html);//去掉过去的span标签
					$html=preg_replace('/(class\s*=")(.*?)(")/', 'class="often_work_btn"', $html);
						
					//去掉过去的</a>标签
					//$html.='<img alt="'.$return1['title'].'" height="64" src="__PUBLIC__/Images/xyicon/'.$return1['icon'].'"; width="64">';
					$html.=$return1['title'];
					$html.='</a>';
					//                  测试输出效果；核验正则替换
					//         			$handle = fopen('manyouo.txt','w+');
					//         			if( fwrite($handle,$html) ){
					//         				echo 'ok';
					//         			}else{
					//         				echo 'write error';
					//         			}
					//         			exit;
					//存入到临时数组
					$tmpArray['html']=$html;
					$tmpArray['model']=$model;
					//获取父类分类信息
					$where2="status=1  and id='".$return1['pid']."'";
					$return2= $nodeModel->where($where2)->field('title')->find();
					//如果存在KEY值，则压入数组栈内
					if(array_key_exists($return2['title'],$dowork)){
						array_push($dowork[$return2['title']],$tmpArray);
	
					}
					//如果不存在KEY值，则初始化第一个值
					else{
						$dowork[$return2['title']][0]['html']=$html;
						$dowork[$return2['title']][0]['model']=$model;
					}
				}
			}
		}
// 		print_R($dowork);
// 		exit;
		$this->assign("dowork",$dowork);
		//获取当前模型所具有的操作信息
		//$accesslist=[$modelname];
		//return $accesslist;
	}
}
?>