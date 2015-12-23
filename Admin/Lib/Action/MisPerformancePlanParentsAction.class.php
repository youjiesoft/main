<?php
//Version 1.0
/** 
 * @Title: MisPerformancePlanParentsAction 
 * @Package package_name
 * @Description: todo(解决通用考核计划的父类) 
 * @author 杨东 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-8-10 上午9:21:40 
 * @version V1.0 
*/ 
class MisPerformancePlanParentsAction extends CommonAction {
	
	/** (设置)
	 * @see CommonAction::edit()
	 */
	public function edit(){
		$model = D ( "MisPerformancePlan" );
		$map['id'] = $_REQUEST ['id'];
		if ($_SESSION["a"] != 1) $map['status'] = 1;
		$vo = $model->where($map)->find();
		if(empty($vo)){
			$this->display ("Public:404");
			exit;
		}
		// 被考核人及部门树
		$this->getDeptAndUserTree($vo);
		// 指标
		$this->getPlanDetails($vo);
		$module=A($this->getActionName());
		if (method_exists($module,"_after_edit")) {
			$vo = call_user_func(array($module,"_after_edit"),$vo);
		}
		$this->assign("vo",$vo);
		$this->display();
	}

	/**
	 * @Title: getDeptAndUserTree
	 * @Description: todo(获取部门及被考核员工) 
	 * @param 计划对象 $vo  
	 * @author 杨东 
	 * @date 2013-8-5 下午6:25:21 
	 * @throws 
	*/  
	protected function getDeptAndUserTree($vo){
		$byusers = $vo['byusers'];//被考核员工
		$MisHrBasicEmployee = D('MisHrBasicEmployee');//员工模型
		$map['id'] = array('in',$byusers);
		$list = $MisHrBasicEmployee->where($map)->field('id,name,deptid')->select();//查询员工
		$returnarr = array();
		$dptmodel = D("MisSystemDepartment");//部门表
		$deptlist = $dptmodel->where("status=1")->order("id asc")->field('id,name,parentid')->select();
		$this->openTreeDeptAndUser($deptlist, $list, 0, $returnarr);
		$this->assign('tree',json_encode($returnarr));
	}
	
	/**
	 * @Title: openTreeDeptAndUser
	 * @Description: todo(设置展开的部门及被考核人) 
	 * @param 部门列表 $deptlist
	 * @param 被考核人了列表 $list
	 * @param 父类ID $pid
	 * @param tree对象 $returnarr
	 * @return boolean  
	 * @author 杨东 
	 * @date 2013-8-9 下午6:50:16 
	 * @throws 
	*/  
	protected function openTreeDeptAndUser($deptlist,$list,$pid,&$returnarr){
		$istruep = false;
		foreach ($deptlist as $k => $v) {
			// 循环判断父节点
			if($v['parentid'] == $pid){
				$newv=array();
				$newv['id'] = -$v['id'];
				$newv['pId'] = -$v['parentid'] ? -$v['parentid']:0;
				$newv['title'] = $v['name']; //光标提示信息
				$newv['name'] = missubstr($v['name'],18,true); //结点名字，太多会被截取,针对于现在的ZTREE，宽度不能超过18个字符。
				if($v['parentid'] == 0){
					$newv['open'] = true;
				}
				// 判断是否有被考核人
				$istrue = false;
				foreach ($list as $k2 => $v2) {
					if($v2['deptid'] == $v['id']){
						// 有被考核人设置为true
						$istrue = true;
						$newv2 = array();
						$newv2['id'] = $v2['id'];
						$newv2['pId'] = -$v['id'];
						$newv2['orderno'] = $v2['orderno'];
						$newv2['title'] = $v2['name']; //光标提示信息
						$newv2['name'] = missubstr($v2['name'],18,true); //结点名字，太多会被截取,针对于现在的ZTREE，宽度不能超过18个字符。
						$newv2['icon'] = "__PUBLIC__/Images/icon/group.png";
						$newv2['open'] = true;
						if($_POST['rel']){
							$newv2['rel'] = $_POST['rel'];
							$newv2['url'] = $_POST['url'].$v2['id'];
							$newv2['target'] = 'ajax';
						}
						if($_POST['check']) $newv2['checked'] = true;
						array_push($returnarr,$newv2);
					}
				}
				// 获取直接点是否有被考核人
				$istrues = $this->openTreeDeptAndUser($deptlist, $list, $v['id'], $returnarr);
				if($istrues){
					// 如何有被考核人则设置为open 并将返回值设为true
					$istruep = true;
					$newv['open'] = true;
				}
				if($_POST['check']) $newv['checked'] = true;// 有checkbox的树,全选中
				if($istrue){
					// 当前部门如果有被考核人则设置为open 并将返回值设为true
					$istruep = true;
					$newv['open'] = true;
				}
				if($_POST['allopen']) {
					$newv['open'] = true;// 所有都展开
					$newv['id'] = $v['id'];
					$newv['pId'] = $v['parentid'] ? $v['parentid']:0;
				}
				array_push($returnarr,$newv);
			}
		}
		return $istruep;
	}
	
	/**
	 * @Title: getPlanAndInusers
	 * @Description: todo(获取指标及审核人) 
	 * @param 计划对象 $vo  
	 * @author 杨东 
	 * @date 2013-8-8 上午10:49:59 
	 * @throws 
	*/  
	protected function getPlanDetails($vo){
		//指标
		$model = D('MisPerformancePlanDetail');
		$map['planid'] = $vo['id'];
		$list = $model->where($map)->order('kpitypename asc')->select();
		$this->assign("planandinusers",$list);
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
			if(($score) >= $v['startresult'] && $score < $v['endresult']+1 && $v['typeid'] == $levelid){
				return $v['name'];
			}
		}
	}
	/**
	 * @Title: opanPlanDetail
	 * @Description: todo(查看考核明细页面)   
	 * @author 杨东 
	 * @date 2013-8-16 上午10:11:03 
	 * @throws 
	*/  
	public function opanPlanDetail(){
		$planid = $_REQUEST['planid'];
		$userid = $_REQUEST['userid'];
		$id = $_REQUEST['id'];
		//取出 计划名称、满分
		$mppModel = D('MisPerformancePlan');//考核计划表
		$mppMap['id'] = $planid; 
		$mppList  = $mppModel->where($mppMap)->field('id,name,mostscore,setscoretype,inusersqz,inuserstype')->find();
		$mppList['setscoretype'] = explode(",", $mppList['setscoretype']);//评分人类型
		$mppList['inusersqz'] = unserialize($mppList['inusersqz']);//评分人类型对应权重
		$mppList['inuserstype'] = unserialize($mppList['inuserstype']);//评分人类型对应评分人
		
		foreach ($mppList['inuserstype'] as $key => $value) {
			$mppList['inuserstype'][$key] = explode(",", $value);
		}
// 		print_r($mppList);
		$this->assign('mppList',$mppList);
		//取出  员工相关信息
		$mhbeModel = D('MisHrBasicEmployee');//员工 表
		$mhbeMap['id'] = $userid;
		$mhbeList  = $mhbeModel->where($mhbeMap)->find();
		$this->assign('mhbeList',$mhbeList);
		// 考核计划明细
		$ppdmodel = D('MisPerformancePlanDetail');
		$ppdlist = $ppdmodel->where("planid=".$planid." and status=1")->order('kpitypename asc')->select();
// 		print_r($ppdlist);
		$this->assign("ppdlist",$ppdlist);
		// 考核人针对被考核人的打分数据
		$model = D("MisPerformanceReport");
		$map['planid'] = $planid;
		$map['byuser'] = $userid;
		$list = $model->where($map)->select();
		$prlist = array();
		foreach ($list as $k => $v) {
			$prlist[$v['inuser']] = $v;
			$prlist[$v['inuser']]['inuserskpi'] = explode(",", $v['inuserskpi']);//考核人负责的KPI
			$prlist[$v['inuser']]['inuserscore'] = unserialize($v['inuserscore']);//考核人的每个KPI对应的分数
		}
		$this->assign("prlist",$prlist);
		// 获取绩效总成绩
		$pamodel = D("MisPerformanceAssess");
		$paVo = $pamodel->where("id=".$id)->find();
		$this->assign('paVo',$paVo);
		// 获取绩效等级数据
		$plmodel = D("MisPerformanceLevel");
		$pllist = $plmodel->where("status=1")->select();
		$level = $this->getLevelName($pllist, $paVo);
		$this->assign("level",$level);
		$this->display("MisPerformancePlanParents:opanPlanDetail");
	}
}
?>