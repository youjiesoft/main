<?php
/**
 * @Title: MisHrInvitereFormAction
 * @Package 人事管理-人员招聘
 * @Description: todo(人员招聘管理)
 * @author liminggang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-3-27 上午11:21:33
 * @version V1.0
 */
class MisHrInvitereSpecialFormAction extends CommonAuditAction {
	public function _filter(&$map){
		$map['isinjob']=1;
	}
	/**
	 * @Title: _before_add
	 * @Description: todo(打开页面前置函数)
	 * @author
	 * @throws
	 */
	public function _before_add(){
		//自动生成订单
		$scnmodel = D('SystemConfigNumber');
		$orderno = $scnmodel->GetRulesNO('mis_hr_invitere_special_form');
		$this->assign("orderno", $orderno);
		//订单编号可写
		$writable= $scnmodel->GetWritable('mis_hr_invitere_special_form');
		$this->assign("writable",$writable);
		
		$this->assign("time",time());
	}
	public function _before_edit(){
		//自动生成订单
		$scnmodel = D('SystemConfigNumber');
		//订单编号可写
		$writable= $scnmodel->GetWritable('mis_hr_invitere_special_form');
		$this->assign("writable",$writable);
	}
	/**
	 * @Title: birthday
	 * @Description: todo(根据出生年月计算年龄)
	 * @param unknown_type $mydate
	 * @return number
	 * @author renling
	 * @date 2013-7-15 上午11:48:24
	 * @throws
	 */
	public	function lookupbirthday(){
		//出生年月
		$birth=$_POST['birthday'];
		list($by,$bm,$bd)=explode('-',$birth);
		$cm=date('n');
		$cd=date('j');
		$age=date('Y')-$by-1;
		if ($cm>$bm || $cm==$bm && $cd>$bd) $age++;
		$MisHrBasicEmployeeModel=D('MisHrBasicEmployee');
		$result=$MisHrBasicEmployeeModel->where(" chinaid=".$_POST['chinaid']." and working=0")->find();
		if($result){
			$result=1;
		}else{
			$result=-1;
		}
		$voInfo['result']=$result;
		$voInfo['age']=$age;
		echo  json_encode($voInfo);
	}
}
?>