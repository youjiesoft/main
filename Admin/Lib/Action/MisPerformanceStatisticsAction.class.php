<?php
//Version 1.0
/** 
 * @Title: MisPerformanceStatisticsAction 
 * @Package package_name
 * @Description: todo(绩效统计) 
 * @author 杨东 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-8-15 上午10:54:18 
 * @version V1.0 
*/ 
class MisPerformanceStatisticsAction extends MisPerformancePlanParentsAction {
	/* (non-PHPdoc)
	 * @see CommonAction::index()
	 */
	public function index(){
		$this->display();
	}
	/**
	 * @Title: singlecontrast
	 * @Description: todo(单人对比分析)   
	 * @author 杨东 
	 * @date 2013-8-15 下午12:01:09 
	 * @throws 
	*/  
	public function singlecontrast(){
		$ppmodel = D( "MisPerformancePlan" );//计划模型
		$pplist = $ppmodel->where("status=1 and ostatus=4")->field("id,name,ostatus")->select();//查询计划列表(已结束的计划)
		$this->assign("pplist",$pplist);
		$planid = $_REQUEST['planid'];// 计划ID
		// 选择的计划过滤用户
		if($planid){
			$ppmap['id'] = $planid;
			$byusers = $ppmodel->where($ppmap)->getField("byusers");
			$map['id'] = array('in',$byusers);
			$this->assign("planid",$planid);
		}
		$MisHrBasicEmployee = D('MisHrBasicEmployee');//员工模型
		$list = $MisHrBasicEmployee->where($map)->field('id,name,deptid,orderno')->select();//查询员工
		$returnarr = array();
		$dptmodel = D("MisSystemDepartment");//部门表
		$deptlist = $dptmodel->where("status=1")->order("id asc")->field('id,name,parentid')->select();
		$this->openTreeDeptAndUser($deptlist, $list, 0, $returnarr);// 部门及人员信息
		$this->assign('singlecontrastTree',json_encode($returnarr));
		
		$startdate = date('Y-m-d', mktime(0,0,0,date('n'),1,date('Y')));//默认开始时间
		$enddate = date('Y-m-d', mktime(0,0,0,date('n'),date('t'),date('Y')));//默认开始时间
		$this->assign("startdate",$startdate);
		$this->assign("enddate",$enddate);
		$this->display();
	}
	/**
	 * @Title: lookupContrast
	 * @Description: todo(检索绩效数据)   
	 * @author 杨东 
	 * @date 2013-8-16 上午9:48:04 
	 * @throws 
	*/  
	public function lookupContrast(){
		$url = "lookupsinglecontrast";
		// 获取绩效等级数据
		$plmodel = D("MisPerformanceLevel");
		$pllist = $plmodel->where("status=1")->select();
		// 通过人查询统计数据
		$byuser = $_POST['byuser'];
		$this->assign("byuser",$byuser);
		$pavmodel = D("MisPerformanceAssessView");
		//设置选中人员为过滤条件
		if($_POST['type'] == 'many'){
			$pavmap['mis_performance_assess.byuser'] = array('in',$byuser);
			$url = "lookupmanycontrast";
		} else if($_POST['type'] == 'single'){
			if(!$byuser){
				$this->error("选择或输入被考核人！");
			}
			$pavmap['mis_performance_assess.byuser'] = $byuser;
			//时间检索条件
		} else if($_POST['type'] == 'results'){
			$pavmap['mis_performance_assess.byuser'] = array('in',$byuser);
			$url = "lookupresultscontrast";
		} 
		$this->assign("type",$_POST['type']);
		if($_POST['planid']) {
			// 判断是否选中计划
			$pavmap['mis_performance_assess.planid'] = $_POST['planid'];
			$this->assign("planid",$_POST['planid']);
		}
		$pavlist = $pavmodel->where($pavmap)->select();//查询绩效数据
		if(count($pavlist) < 1){
			$this->error("此人未被考核！");
		}
		$maxScore = null;//最大值
		$sumScore = 0;//总数
		foreach ($pavlist as $k => $v) {
			// 获取总分
			$score = $v['totalscore'];
			// 如果有修正取修正总分
			if($v['isamend']) $score = $v['amendscore'];
			$pavlist[$k]['score'] = $score;// 设置总分
			$pavlist[$k]['level'] = $this->getLevelName($pllist, $v, $vo['levelid']);//设置等级中文
			if($_POST['type'] == 'many'){//多人对比分析 需检索
				$this->filterPavlist($pavlist, $k);
			}
			if($pavlist[$k]['score']) {
				$sumScore += $pavlist[$k]['score'];//最大值
				// 设置最大值
				if($maxScore == null){
					// 第一次设置值
					$maxScore = $pavlist[$k]['score'];
				} else {
					if($maxScore < $pavlist[$k]['score']) $maxScore = $pavlist[$k]['score'];
				}
			}
		}
		F("MisPerformanceStatistics_StatisticsContrast",$pavlist,DATA_PATH);
		$avgScore = sprintf("%.2f", $sumScore/count($pavlist));
		$this->assign("avgScore",$avgScore);
		$this->assign("maxScore",$maxScore);
		$this->assign("pavlist",$pavlist);
		$this->display($url);
	}
	/**
	 * @Title: filterPavlist
	 * @Description: todo(过滤绩效数据-用于多人对比分析) 
	 * @param 绩效数据 $pavlist
	 * @param 当前行 $k  
	 * @author 杨东 
	 * @date 2013-8-19 下午5:58:41 
	 * @throws 
	*/  
	private function filterPavlist(&$pavlist,$k){
		switch ($_POST['ptype']) {
			case "dept"://部门
				if($_POST['deptkeword']){
					$keyword = $_POST['deptkeword'];
					$deptname = $pavlist[$k]['deptname'];
					switch ($_POST['deptType']) {
						case "like":
							if(!strstr($deptname, $keyword)) unset($pavlist[$k]);break;
						case "nlike":
							if(strstr($deptname, $keyword)) unset($pavlist[$k]);break;
						case "eq":
							if($deptname != $keyword) unset($pavlist[$k]);break;
						case "neq":
							if($deptname == $keyword) unset($pavlist[$k]);break;
					}
				}
				break;
			case "score"://分数
				if($_POST['scorekeword']){
					$keyword = $_POST['scorekeword'];
					$score = $pavlist[$k]['score'];
					switch ($_POST['scoreType']) {
						case "lt":
							if($score >= $keyword) unset($pavlist[$k]);break;
						case "elt":
							if($score < $keyword) unset($pavlist[$k]);break;
						case "gt":
							if($score <= $keyword) unset($pavlist[$k]);break;
						case "egt":
							if($score < $keyword) unset($pavlist[$k]);break;
						case "eq":
							if($score != $keyword) unset($pavlist[$k]);break;
						case "neq":
							if($score == $keyword) unset($pavlist[$k]);break;
					}
				}
				break;
			case "level"://等级
				if($_POST['levelkeword']){
					$keyword = $_POST['levelkeword'];
					$levelname = $pavlist[$k]['level'];
					switch ($_POST['levelType']) {
						case "eq":
							if($levelname != $keyword) unset($pavlist[$k]);break;
						case "neq":
							if($levelname == $keyword) unset($pavlist[$k]);break;
					}
				}
				break;
		}
	}
	/**
	 * @Title: lookupStatisticsChart
	 * @Description: todo(获取图表数据)   
	 * @author 杨东 
	 * @date 2013-8-16 上午9:48:41 
	 * @throws 
	*/  
	public function lookupStatisticsChart(){
		import('@.ORG.BaseCharts.Chart');//引入Chart
		// 通过人查询统计数据
		$pavlist = F("MisPerformanceStatistics_StatisticsContrast");//查询单个人员绩效数据
		$chart = new Chart();//创建chart对象
		$graphAttribute = array('useRoundEdges'=>'1',"showLegend"=>'1');// 默认参数
		$chart->setGraphAttribute($graphAttribute);//调用方法进行设置
		if($_GET['type'] == 'single'){
			$chart->setX("planname");//设置x坐标
			$chart->setY("score");//设置Y坐标
			$chart->builderChart(Chart::$SINGLE_SERIES, $pavlist );//生成图表
		} else if($_GET['type'] == 'many'){
			$chart->setX("name");//设置x坐标
			$chart->setY("score");//设置Y坐标
			$chart->builderChart(Chart::$SINGLE_SERIES, $pavlist );//生成图表
		} else if($_GET['type'] == 'results'){
			$chart->setX("name");//设置x坐标
			$chart->setY("score");//设置Y坐标
			$chart->builderChart(Chart::$SINGLE_SERIES, $pavlist );//生成图表
		}
	}
	/**
	 * @Title: manycontrast
	 * @Description: todo(这里用一句话描述这个方法的作用)   
	 * @author 杨东 
	 * @date 2013-8-16 上午10:53:43 
	 * @throws 
	*/
	public function manycontrast(){
		$ppmodel = D( "MisPerformancePlan" );//计划模型
		$pplist = $ppmodel->where("status=1 and ostatus=4")->field("id,name,ostatus,levelid")->select();//查询计划列表(已结束的计划)
		$this->assign("pplist",$pplist);
		$planid = $pplist[0]['id'];// 计划ID
		$levelid = $pplist[0]['levelid'];// 计划考核等级分类
		if($_REQUEST['planid']){
			$planid = $_REQUEST['planid'];// 计划ID
			// 取绩效计划的绩效考核等级分类
			foreach ($pplist as $k => $v) {
				if($v['id'] == $planid){
					$levelid = $v['levelid'];
					break;
				}
			}
		}
		// 获取绩效等级数据
		$plmodel = D("MisPerformanceLevel");
		$pllist = $plmodel->where("status=1 and typeid=".$levelid)->select();
		$this->assign("pllist",$pllist);
		// 选择的计划过滤用户
		if($planid){
			$ppmap['id'] = $planid;
			$byusers = $ppmodel->where($ppmap)->getField("byusers");
			$map['id'] = array('in',$byusers);
			$this->assign("planid",$planid);
		}
		$MisHrBasicEmployee = D('MisHrBasicEmployee');//员工模型
		$list = $MisHrBasicEmployee->where($map)->field('id,name,deptid,orderno')->select();//查询员工
		$returnarr = array();
		$dptmodel = D("MisSystemDepartment");//部门表
		$deptlist = $dptmodel->where("status=1")->order("id asc")->field('id,name,parentid')->select();
		$_POST['check'] = true;
		$this->openTreeDeptAndUser($deptlist, $list, 0, $returnarr);// 部门及人员信息
		$this->assign('manycontrastTree',json_encode($returnarr));
		$this->display();
	}
	/**
	 * @Title: resultscontrast
	 * @Description: todo(成绩分析)   
	 * @author 杨东 
	 * @date 2013-8-19 下午6:07:49 
	 * @throws 
	*/  
	public function resultscontrast(){
		$this->manycontrast();
	}
	/**
	 * @Title: levelcontrast
	 * @Description: todo(等级分析)   
	 * @author 杨东 
	 * @date 2013-8-20 上午9:57:23 
	 * @throws 
	*/  
	public function levelcontrast(){
		$ppmodel = D( "MisPerformancePlan" );//计划模型
		$pplist = $ppmodel->where("status=1 and ostatus=4")->field("id,name,ostatus,levelid")->select();//查询计划列表(已结束的计划)
		$this->assign("pplist",$pplist);
		$planid = $pplist[0]['id'];// 计划ID
		$levelid = $pplist[0]['levelid'];// 计划考核等级分类
		if($_REQUEST['planid']){
			$planid = $_REQUEST['planid'];// 计划ID
			// 取绩效计划的绩效考核等级分类
			foreach ($pplist as $k => $v) {
				if($v['id'] == $planid){
					$levelid = $v['levelid'];
					break;
				}
			}
			$this->assign("planid",$_REQUEST['planid']);
		}
		// 获取绩效等级数据
		$plmodel = D("MisPerformanceLevel");
		$pllist = $plmodel->where("status=1 and typeid=".$levelid)->order('id desc')->select();
		$this->assign("pllist",$pllist);
		$returnarr = array();
		$dptmodel = D("MisSystemDepartment");//部门表
		$deptlist = $dptmodel->where("status=1")->order("id asc")->field('id,name,parentid')->select();
		$_POST['check'] = true;
		$_POST['allopen'] = true;
		$this->openTreeDeptAndUser($deptlist, array(), 0, $returnarr);// 部门及人员信息
		$this->assign('levelcontrastTree',json_encode($returnarr));
		$this->display();
	}
	public function lookupLevelContrast(){
		$planid = $_REQUEST['planid'];// 计划ID
		$deptid = explode(",", $_REQUEST['deptid']);//部门ID
		$dptmodel = D("MisSystemDepartment");//部门表
		//$deptmap["status"] = 1;
		$deptlist = $dptmodel->where("status=1")->field('id,name')->select();//所有部门
		$ppmodel = D( "MisPerformancePlan" );//计划模型
		$ppVO = $ppmodel->where("status=1 and ostatus=4")->field("id,name,levelid")->find();//查询计划列表(已结束的计划)
		// 获取绩效等级数据
		$plmodel = D("MisPerformanceLevel");
		$pllist = $plmodel->where("status=1 and typeid=".$ppVO['levelid'])->order('id desc')->select();
		$this->assign("pllist",$pllist);
		$pavmodel = D("MisPerformanceAssessView");
		//设置选中人员为过滤条件
		$pavmap['mis_performance_assess.planid'] = $_POST['planid'];//选中的计划
		$pavlist = $pavmodel->where($pavmap)->select();//查询绩效数据
		$totalArr = array();//合计
		foreach ($deptlist as $k => $v) {
			// 取选中的部门
			if(in_array($v['id'], $deptid)){
				$pnum = array();// 部门各个等人数集合
				$sumNum = 0;
				// 构造等级选中
				foreach ($pllist as $k1 => $v1) {
					$num = 0;
					foreach ($pavlist as $k2 => $v2) {
						$score = $v2['totalscore'];// 获取总分
						if($v2['isamend']) $score = $v2['amendscore'];// 如果有修正取修正总分
						if($score >= $v1['startresult'] && $score < $v1['endresult']+1 && $v2['deptid'] == $v['id']){
							$num += 1;
						}
					}
					$pnum[$v1['id']] = $num;
					$sumNum += $num;
					$totalArr['pl'][$v1['id']] += $num;
				}
				$deptlist[$k]['sumNum'] = $sumNum;
				$deptlist[$k]['pnum'] = $pnum;
				$totalArr['total'] += $sumNum;
			} else {
				unset($deptlist[$k]);
			}
		}
		$statisticsContrast = array("data"=>$deptlist,"series"=>$pllist);
		F("MisPerformanceStatistics_StatisticsContrast",$statisticsContrast,DATA_PATH);
		$this->assign("deptlist",$deptlist);
		$this->assign("totalArr",$totalArr);//合计
		$this->display();
	}
	/**
	 * @Title: lookupLevelChart
	 * @Description: todo(等级图形)   
	 * @author 杨东 
	 * @date 2013-8-20 下午5:06:26 
	 * @throws 
	*/  
	public function lookupLevelChart(){
		$statisticsContrast = F("MisPerformanceStatistics_StatisticsContrast");//获取数据
		$xmlWriter = new XMLWriter();// 构造XML对象
		// 构造头部
		$xmlWriter->openURI('php://output');
		$xmlWriter->startDocument('1.0','UTF-8');
		$xmlWriter->setIndent(4);
		// 头部构造完毕 构造第一节点
		$xmlWriter->startElement("chart");
		$xmlWriter->writeAttribute("useRoundEdges", 1);//构造属性
		// 创建系列开始
		$xmlWriter->startElement("categories");
		//循环遍历创建
		foreach($statisticsContrast['data'] as $value){
			$xmlWriter->startElement("category");
			$xmlWriter->writeAttribute("label", $value['name']);
			$xmlWriter->endElement();
		}
		$xmlWriter->endElement();
		// 系列创建完成 开始创建数据
		foreach($statisticsContrast['series'] as $value){
			$xmlWriter->startElement("dataset");// 部门节点
			$xmlWriter->writeAttribute("seriesName", $value['name']);// 部门节点属性
			//循环遍历创建 部门数据
			foreach($statisticsContrast['data'] as $value2){
				//创建set
				$xmlWriter->startElement("set");
				$xmlWriter->writeAttribute("value",$value2['pnum'][$value['id']]);
				$xmlWriter->endElement();
			}
			$xmlWriter->endElement();
		}
		$xmlWriter->endDocument();//结束XML
		$xmlWriter->flush();//输出XML对象
		unset($xmlWriter);//销毁XML对象
	}
}
?>