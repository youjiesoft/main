<?php
//Version 1.0
/** 
 * @Title: MisPerformanceReportAction 
 * @Package package_name
 * @Description: todo(绩效打分)
 * @author 杨东 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-8-9 上午9:44:54 
 * @version V1.0 
*/ 
class MisPerformanceReportAction extends MisPerformancePlanParentsAction {
	
	/**
	 * @Description: _filter(首面列表显示条件)
	 * @param unknown_type $map
	 * @author laicaixia
	 * @date 2013-8-15 下午3:59:58
	 */
	public function _filter(&$map){
		$map['ostatus'] = 2;
	}
	
	/**
	 * @Title: index(重写index方法)
	 * @Description: todo(进入首页)
	 * @author lcx
	 */
	public function index() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name = 'MisPerformancePlan';//绩效计划表
		if (! empty ( $name )) {
			$this->_list ( $name, $map );
		}
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail('MisPerformanceReport');
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
	 * @Title: _before_edit
	 * @Description: todo(前置打开修改函数)   
	 * @author 杨东 
	 * @date 2013-8-10 下午4:14:45 
	 * @throws 
	*/  
	public function _before_edit(){
		$_POST['url'] = "__URL__/lookupeditview/planid/".$_REQUEST['id']."/id/";
		$_POST['rel'] = "MisPerformanceReportEdit";
	}
	/**
	 * @Title: lookupeditview
	 * @Description: todo(左边加载数据)   
	 * @author 杨东 
	 * @date 2013-8-10 下午4:15:48 
	 * @throws 
	*/  
	public function lookupeditview(){
		$model = D ( "MisPerformancePlan" );
		$vo = $model->where('id='.$_REQUEST['planid'])->find();
		// 指标
		$this->getPlanDetails($vo);
		$vo['setscoretype'] = explode(",", $vo['setscoretype']);//评分人类型
		$vo['inusersqz'] = unserialize($vo['inusersqz']);//评分人类型对应权重
		$vo['inuserstype'] = unserialize($vo['inuserstype']);//评分人类型对应评分人
		foreach ($vo['inuserstype'] as $key => $value) {
			$vo['inuserstype'][$key] = explode(",", $value);
		}
		$prmodel = D("MisPerformanceReport");
		$prmap['planid'] = $vo['id'];
		$prmap['byuser'] = $_REQUEST['id'];
		if ($_SESSION["a"] != 1) {
			$prmap['inuser'] = $_SESSION[C('USER_AUTH_KEY')];
		}
		$list = $prmodel->where($prmap)->select();
		$prlist = array();
		foreach ($list as $k => $v) {
			$prlist[$v['inuser']] = $v;
			$prlist[$v['inuser']]['inuserskpi'] = explode(",", $v['inuserskpi']);
			$prlist[$v['inuser']]['inuserscore'] = unserialize($v['inuserscore']);
		}
		$this->assign("vo",$vo);
		$this->assign("planid",$vo['id']);
		$this->assign("byuser",$_REQUEST['id']);
		$this->assign("prlist",$prlist);
		$this->display();
	}
	public function update(){
		$model = D("MisPerformanceReport");
		$map['planid'] = $_POST['planid'];
		$map['byuser'] = $_POST['byuser'];
		//打分人员数组 inuser
		$inuserlist = $model->where($map)->getField("id,inuser");
		//系统管理员
		if ($_SESSION["a"]) {
			$prlist = $model->where($map)->select();
			foreach ($prlist as $k => $v) {
				$arr = array();
				$inuserskpi = explode(",", $v['inuserskpi']);
				$total = 0;
				foreach ($inuserskpi as $k1 => $v1) {
					$arr[$v1] = $_POST['inuserscore'.$v1][$v['inuser']]?$_POST['inuserscore'.$v1][$v['inuser']]:0;
					$total += $arr[$v1];
				}
				$data = array(
					'inuserscore' => serialize($arr),
					'total' => $total
				);
				$result=$model->where('id='.$v['id'])->save($data);
			}
			//指定打分人员
		}else if(in_array($_SESSION[C('USER_AUTH_KEY')],array_values($inuserlist))){
			$map["inuser"]=$_SESSION[C('USER_AUTH_KEY')];
			$inuserskpilist = $model->where($map)->field("id,inuserskpi")->find();
			$arr = array();
			$total = 0;
			$inuserskpi = explode(",", $inuserskpilist['inuserskpi']);
			foreach ($inuserskpi as $k1 => $v1) {
				$arr[$v1] = $_POST['inuserscore'.$v1][$_SESSION[C('USER_AUTH_KEY')]]?$_POST['inuserscore'.$v1][$_SESSION[C('USER_AUTH_KEY')]]:0;
				$total += $arr[$v1];
			}
			$data = array(
					'inuserscore' => serialize($arr),
					'total' => $total
			);
			$result=$model->where('id='.$inuserskpilist['id'])->save($data);
		}
		if($result===false){
			$this->error("操作失败！");			
		}else{
			$this->success ( L('_SUCCESS_'));
		}
	}
}
?>