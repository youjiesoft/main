<?php
//Version 1.0
/**
 * @Title: MisPerformanceAssessAction 
 * @Package package_name
 * @Description: todo(绩效评估管理) 
 * @author laicaixia
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-8-14 上午9:39:24 
 * @version V1.0
 */
class MisPerformanceAssessAction extends MisPerformancePlanParentsAction {
	/**
	 * @Title: index(重写index方法)
	 * @Description: todo(进入首页)
	 * @author lcx
	 */
	public function index() {
		$map = $this->_search ('MisPerformancePlan');
		$this->znodes();
		//取出(左边考核计划等于右边相应内容)
		$planid = $_REQUEST['id'];
		$map['ostatus']=2;//执行
		if($planid==-1){
			$map['ostatus']=2;//执行中计划
		}else if($planid==-2){
			$map['ostatus']=4;//结束的任务
		}else if($planid==-3){ //计划状态为已发布 执行的数据
			$map['ostatus']=array('in','2,4');
		}else if($_REQUEST['id']){
			$map['id'] = $_REQUEST['id'];
		} 
		$this->assign("id",$planid);
		//取出右边信息
		$name = $this->getActionName();
		$qx_name=$name;
		if(substr($name, -4)=="View"){
			$qx_name = substr($name,0, -4);
		}
		//验证浏览及权限
		if( !isset($_SESSION['a']) ){
			$map=D('User')->getAccessfilter($qx_name,$map);	
		}
		//列表过滤器，生成查询Map对象
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
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$this->_list ( 'MisPerformancePlan', $map );
		if( intval($_POST['dwzloadhtml']) ){
			$this->display("dwzloadindex");exit;
		}
		//首页收件箱调用方法，为ajax调用
		if ($_GET['type'] == "ajaxcall") {
			$this->display ("ajax_index");exit;
		}
		if ($_REQUEST['jump']) {
			$this->display('unitlist');
		} else {
			$this->display();
		}
		return;
	}
	
	/**
	 *  @Description: znodes(构造树形结构)
	 *  @author laicaixia
	 *  @date 2013-8-1 下午3:33:38
	 */
	private function znodes(){
		$mpppTreemiso=array(
					array(
						'id'=>0,
						'pId'=>0,
						'name'=>'计划状态',
						'title'=>'计划状态',
						'rel'=>'MisPerformanceAssessRel',
						'target'=>'ajax',
						'url'=>'__URL__/index/jump/1/id/-3',
						'open'=>true
					),
					array(
						'id'=>-1,
						'pId'=>0,
						'name'=>'执行',
						'title'=>'执行',
						'rel'=>'MisPerformanceAssessRel',
						'target'=>'ajax',
						'url'=>'__URL__/index/jump/1/id/-1',
						'open'=>true
					),
					array(
						'id'=>-2,
						'pId'=>0,
						'name'=>'结束',
						'title'=>'结束',
						'rel'=>'MisPerformanceAssessRel',
						'target'=>'ajax',
						'url'=>'__URL__/index/jump/1/id/-2',
						'open'=>true
					),
				);
		$mpfassessTreearr=json_encode($mpppTreemiso);
		$this->assign("mpfassessTreearr",$mpfassessTreearr);
	}
	
	public function edit(){
		$id = $_REQUEST['id'];
		$model = D("MisPerformancePlan");
		$ppmap['id'] = $id;
		$vo = $model->where($ppmap)->find();
		$this->assign('vo',$vo);
		// 获取绩效等级数据
		$plmodel = D("MisPerformanceLevel");
		$pllist = $plmodel->where("status=1")->select();
		$MisHrBasicEmployeeView = D('MisHrBasicEmployeeView');//员工模型
		$map['mis_hr_personnel_person_info.id'] = array('in',$vo['byusers']);
		$list = $MisHrBasicEmployeeView->where($map)->select();//查询员工
		$pamodel = D("MisPerformanceAssess");
		$palist = $pamodel->where("planid=".$id)->select();
		foreach ($list as $k => $v) {
			foreach ($palist as $k1 => $v1) {
				if ($v['id'] == $v1['byuser']) {
					$list[$k]['totalscore'] = $v1['totalscore'];
					$list[$k]['paid'] = $v1['id'];
					$list[$k]['amendscore'] = $v1['amendscore'];
					$list[$k]['level'] = $this->getLevelName($pllist, $v1, $vo['levelid']);
				}
			}
		}
		$this->assign("byusers",$list);
		$this->assign("id",$id);
		$this->display();
	}
	/**
	 * @Title: countPlan
	 * @Description: todo(计算得分)   
	 * @author 杨东 
	 * @date 2013-8-14 下午3:01:45 
	 * @throws 
	*/  
	public function countPlan(){
		$id = $_POST['id'];
		$ppmodel = D("MisPerformancePlan");
		$ppmap['id'] = $id;
		$vo = $ppmodel->where($ppmap)->find();//计划数据
		$inuserstype = unserialize($vo['inuserstype']);//评分人对应类型
		foreach ($inuserstype as $key => $value) {
			$inuserstype[$key] = explode(",", $value);//评分人转换数组
		}
		$inusersqz = unserialize($vo['inusersqz']);//类型对应权重
		$prmodel = D("MisPerformanceReport");
		$prlist = $prmodel->where("planid=".$id)->select();//评分数据
		$byusers = explode(",", $vo['byusers']);
		$model = D("MisPerformanceAssess");// 评估表
		foreach ($byusers as $k => $v) {
			$data["planid"] = $id;
			$data["byuser"] = $v;
			$totalscore = 0;//总分
			foreach ($inuserstype as $k2 => $v2) {
				$typetotalscore = 0;//评分人类型总分
				foreach ($prlist as $k1 => $v1) {
					if($v == $v1['byuser'] && in_array($v1['inuser'], $v2)){
						$typetotalscore += $v1['total'];
					}
				}
				//总分=评分人类型总分/评分人类型中的评分人数量*评分人类型权重/100
				$totalscore += $typetotalscore/count($v2)*$inusersqz[$k2]/100;
			}
			$data["totalscore"] = $totalscore;
			$data['createtime'] = time();
			$data['createid'] = $_SESSION[C('USER_AUTH_KEY')];
			$list = $model->data($data)->add();
			if($list == false){
				$this->error ( L('_ERROR_') );
			}
		}
		
		$pplist = $ppmodel->where('id = '.$id)->setField('ostatus',4);
		if (false !== $list && false !== $pplist) {
			$this->success ( L('_SUCCESS_'));
		} else {
			$this->error ( L('_ERROR_') );
		}
	}
	
	/**
	 * @Description: resetPlan(重置)   
	 * @author lcx
	 * @date 2013-8-16 上午10:24:12 
	*/  
	public function resetPlan(){
		$id = $_POST['id'];
		//删除相应数据
		$model = D("MisPerformanceAssess");//绩效评估表
		$list = $model->where('planid = '.$id)->delete();
		//计划状态改为1
		$ppmodel = D("MisPerformancePlan");//考核计划表
		$data = array(
				'ostatus' => 1,
			);
		$pplist = $ppmodel->where('id = '.$id)->save($data);
		//重置时绩效评估表的数据删除、考核计划表的计划状态改为1；
		if (false != $list && false != $pplist) {
			$this->success ( L('_SUCCESS_'));
		} else {
			$this->error ( L('_ERROR_') );
		}
	}
	
	/**
	 * @Description: amendscore(修改【修正总分】)   
	 * @author laicaixia 
	 * @date 2013-8-15 下午2:45:40 
	*/  
	public function amendscore(){
		//取出考核计划的最高分
		$mapP['id'] = $_REQUEST['planid'];
		$modelP = D('MisPerformancePlan');//考核计划表
		$voP =  $modelP->where($mapP)->find();
		//修正总分的取出与修改
		$map['id'] = $_REQUEST['id'];
		$modelA = D("MisPerformanceAssess");//绩效评估表
		if($_POST['type']){
			//修改 修正总分、修正原因
			if($_POST['amendscore'] <= $voP['mostscore']){
				$data = array(
					'amendscore' => $_POST['amendscore'],//修正分值
					'amendremark' => $_POST['amendremark'],//修正原因
					'isamend' => 1,//修正后的状态
				);
			}else{
				$this->error ('考核满分为'.$voP['mostscore'].'分！');
				exit;
			}
			$list1 = $modelA->where($map)->save($data);
			if(false != $list1){
				$this->success ( L('_SUCCESS_'));
			}else{
				$this->error ( L('_ERROE_'));
			}
		} else {
			//取出 修正总分、修正原因
			$vo = $modelA->where($map)->find();
			$this->assign('vo',$vo);
			$this->display();
		}
	}
	
	/**
	 * @Description: comments_conclusions(修改【评语及总结】)   
	 * @author laicaixia 
	 * @date 2013-8-15 下午2:45:40 
	*/  
	public function commentsConclusions(){
		$map['id'] = $_REQUEST['id'];
		$modelA = D("MisPerformanceAssess");//绩效评估表
		if($_POST['type']){
			//修改 评语、总结
			$data = array(
					'comments' => $_POST['comments'],//评语
					'conclusions' => $_POST['conclusions'],//总结
			);
			$list1 = $modelA->where($map)->save($data);
			if(false != $list1){
				$this->success ( L('_SUCCESS_'));
			}else{
				$this->success ( L('_ERROE_'));
			}
		} else {
			//取出 评语、总结
			$vo = $modelA->where($map)->find();
			$this->assign('vo',$vo);
			$this->display();
		}
	}
	
}
?>