<?php
/**
 * @Title: MisTodoAction
 * @Package package_name
 * @Description: todo(待办事务)
 * @author laicaixia
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-5-31 下午5:08:23
 * @version V1.0
 */
class MisTodoAction extends CommonAction{
	
	/* public function index() {
		$defaultSelect = Cookie::get($name.'defaultSelect');
		$ConfigListModel = D('SystemConfigList');
		$doctimelist = $ConfigListModel->GetValue('doctime');// 单据时间检索
		$file =  DConfig_PATH . "/System/ProcessModelsConfig.inc.php";
		$list =  require $file;
		$tree[] = array(
				'id' => 0,
				'pId' => -1,
				'name' => '待审单据',
				'title' => '待审单据',
				'open' => true
		);//个人单据根节点
		foreach ($list as $k => $v) {
			$masmodel = D($v['model']);
			$maps = array();
			$maps['status'] =1;
			$maps['_string'] = 'FIND_IN_SET(  '.$_SESSION[C('USER_AUTH_KEY')].',curAuditUser )';
			$count = $masmodel->where($maps)->count('id');
			if($count<1){
				continue;
			}
			$pid = $k+1;
			$tree[] = array(
					'id' => $pid,
					'pId' => 0,
					'name' => $v['name'].'('.$count.')',
					'title' => $v['name'].'('.$count.')',
					'rel' => "todoindexview",
					'icon' => "__PUBLIC__/Images/icon/order_missionwait.png",
					'target' => 'ajax',
					'url' => '__APP__/'.$v['model'].'/waitAudit/defid/'.$pid,
					'open' => false
			);//待审任务根节点
			foreach ($doctimelist as $k1 => $v1) {
				$thisid = $pid.''.$k1;
				$tree[] =  array(
						'id' => $thisid,
						'pId' => $pid,
						'name' => $v1['name'],
						'title' => $v1['name'],
						'rel' => "todoindexview",
						'icon' => $v1['icon'],
						'target' => 'ajax',
						'url' => '__APP__/'.$v['model'].'/waitAudit/time/'.$k1.'/defid/'.$thisid,
						'open' => false
				);
			}
			
		}
		$this->assign("defaultSelect",$defaultSelect);
		$this->assign("auditTree",json_encode($tree));
		$this->display();
	} */
	
	/**
	 * @Title: app
	 * @Description: todo(待处理)
	 * @author laicaixia
	 * @date 2013-5-31 下午5:08:00
	 * @throws
	 */
	public function lookupshow(){
		//查询当前登录用户待处理工作信息    在hearder头部用
		$MisWorkExecutingModel = D("MisWorkExecuting");
		$data=$MisWorkExecutingModel->getCurrentWork();
		echo json_encode($data);
	}
}
?>
