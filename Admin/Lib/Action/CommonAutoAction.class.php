<?php
/*
 * author : arrowing
 * date   : 2012-12-17
 * note   : 流程审核通过后自动进入下一步，无须手动建立文档
 */
class CommonAutoAction extends CommonAuditAction {

	/*
	 * private prototype must be set in setAuto()
	 */

	/* type: array
	 * eg. array('ManagerPrincipal', 'ManagerPrincipalCheck');
	 */
	private $steps;

	/* type: string
	 * eg. ManagerPrincipal
	 */
	private $stepKey;

	/* type: string
	 * eg. ManagerPrincipalCheck
	 */
	private $nextStep;

	/* type: boolean
	 * eg. true
	 */
	private $isHaveNext;

	/* type: array
	 * eg. array('id' => 1, 'createtime' => 1350000000);
	 */
	private $nextStepData;

	public function setStepKey($stepKey, $nextStep = ''){
		$this->stepKey = $stepKey;
		$inc = require DConfig_PATH . "/System/processauto.inc.php";
		$this->steps = $inc[$this->stepKey];

		$action = $this->getActionName();
		$exists = array_search($action, $this->steps);

		if($exists === false){//存在多选择流程
			foreach($this->steps as $k => $v){
				if(is_array($v) && in_array($action, $v)){
					$exists = $k;
					break;
				}
			}
		}

		//判断是否有下一步流程
		if($exists !== false && $exists < count($this->steps) - 1){
			$this->isHaveNext = true;

			if(!$nextStep){
				$nextStep = $this->steps[$exists+1];
				if(is_array($nextStep)){
					//如果存在选择关系，默认下一步流程为第一个
					$nextStep = $nextStep[0];
				}
			}
			$this->nextStep = $nextStep;
		}else{
			$this->isHaveNext = false;
		}
	}

	//设置要传递的参数
	public function setPassData($data, $id){
		$this->nextStepData = $data;
		$this->createNewProcess($id);
	}

	//自动创建下一步流程
	public function createNewProcess($id){

		if($this->isHaveNext){
			$name = $this->getActionName();
			$module = D($name);
			$data = $module->find($id);
			foreach($this->nextStepData as $k => $v){
				if(is_numeric($k)){
					$map[$v] = $data[$v];
				}else if($v){
					$map[$k] = $v;
				}
			}
			$modelname = $this->nextStep;
			$pcmodel = D('ProcessConfig');
			$pcarr = $pcmodel->getprocessinfo($modelname);
			$map['ptmptid'] = $pcarr['pid'];

			$module = D($modelname);
			$result = $module->add($map);

			if($result === false){
				$this->error ( $module->getError () );
			}else{
				//创建成功
				$processModel = D('ProcessInfoHistory');
				$pih['pid'] = $pcarr['pid'];
				$pih['ostatus'] = -2;
				$pih['tablename'] = $modelname;
				$pih['tableid'] = $result;
				$pih['dotype'] = '流程新建';
				$pih['dotime'] = time();
				$pih['userid'] = $data['createid'];
				if (false === $processModel->add ($pih)) {
					$this->error ( $processModel->getError () );
				}
			}
		}
	}
}
?>