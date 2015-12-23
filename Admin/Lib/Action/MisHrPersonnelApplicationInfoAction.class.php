<?php
/**
 * @Title: MisHrPersonnelApplicationInfoAction 
 * @Package package_name 
 * @Description: todo(人事申请管理) 
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-1-25 上午10:15:24 
 * @version V1.0
 */
class MisHrPersonnelApplicationInfoAction extends CommonAuditAction {
		
	/** @Title: _filter
	 * @Description: (构造检索条件) 
	 * @author  
	 * @date 2013-5-31 下午3:59:44 
	 * @throws 
	*/
	public function _filter(&$map){
		if ($_SESSION["a"] != 1){
			$map['status']=array("gt",-1);
		}
		
	}
	
	/**
	 * @Title: _before_add
	 * @Description: todo(打开页面前置函数)
	 * @author 
	 * @throws
	 */
	public function _before_add(){
		$this->assign("time",time());
		//得到当前登录的用户绑定的员工
		$userid = $_SESSION[C('USER_AUTH_KEY')];
		$deptid=getFieldBy($userid, 'id', 'dept_id', 'user');
		//获取部门
		//$model=D("MisHrPersonnelPersonInfo");
		//$deptid = $model->where("id=".$employeid)->getField("deptid");
		//查询职位
		$DutyModel=D('Duty');
		$DutyList=$DutyModel->where(" status=1")->getField('id,name');
		$this->assign("DutyList",$DutyList);
		//根据当前用户的部门选择select下拉框的部门
		$this->_before_everymethor($deptid);
	}
	/**
	 * @Title: _before_edit
	 * @Description: todo(打开修改页面前置函数)   
	 * @author  
	 * @date 2013-5-31 下午4:11:26 
	 * @throws 
	*/
	public function _before_edit(){
		$id=$_REQUEST['id'];
		$model=D($this->getActionName());
		$deptid=$model->where("id=".$id)->getField("deptid");
		$this->_before_everymethor($deptid);
		//查询职位
		$DutyModel=D('Duty');
		$DutyList=$DutyModel->where(" status=1")->getField('id,name');
		$this->assign("DutyList",$DutyList);
	}
	/**
	 * @Title: _after_edit
	 * @Description: todo(打开修改页面后置函数)   
	 * @author  
	 * @date 2013-5-31 下午4:11:26 
	 * @throws 
	*/
	public function _after_edit( &$vo ){
		if($vo['positiondetails']) $vo["positiondetails"]=unserialize($vo["positiondetails"]);
		
		//$arr["catalogid"]=0;//0表示产品类的扩展属性;
		$arr['tableid']=$vo['id'];
		$arr['modelname']=$this->getActionName();
		//$arr["linkid"]=1;//当前属性对象的id
		$model = M("mis_typeform_data");
		$data=array();
		$expand_property_info = $model->where($arr)->find();
		if($expand_property_info){
			$data = unserialize($expand_property_info['content']);
			$this->assign("expand_property_data_id",$expand_property_info['id']);
		}
		
		$arr["linkid"]=1;//产品类型下对应的分类id
		$expand_property= $this->get_expand_property($arr,$data);
		$this->assign("expand_property",$expand_property);
	}
	/**
	 * @Title: _before_insert 
	 * @Description: todo(插入前置函数)   
	 * @author  
	 * @date 2013-5-31 下午4:08:58 
	 * @throws 
	*/ 
	public function _before_insert(){
		$this->common();
	}
	public function _after_insert($list){
		$this->set_expand_property($list);
	}
	/**
	 * @Title: _before_update
	 * @Description: todo(修改保存前置函数)   
	 * @author  
	 * @date 2013-5-31 下午4:12:50 
	 * @throws 
	*/
	public function _before_update(){
		$this->common();
		$this->checkifexistcodeororder('mis_hr_personnel_applicationinfo','orderno',1);		
	}
	
	public function _after_update(){
		$this->set_expand_property($_POST);
	}
	public function _before_startprocess(){
		$this->common();
		$this->checkifexistcodeororder('mis_hr_personnel_applicationinfo','orderno',1);
	}
	/**
	 * @Title: lookupgetdeptpers 
	 * @Description: todo(ajax请求获取部门总共人数)   
	 * @author yuansl 
	 * @date 2014-4-24 下午11:01:39 
	 * @throws
	 */
	public function lookupgetdeptpers(){
		$deptid = $_POST['deptid'];
		$MisHrPersonnelPersonInfoModel = D("MisHrPersonnelPersonInfo");
		$AllPersonCount = $MisHrPersonnelPersonInfoModel->where("status = 1 and working = 1 and deptid = ".$deptid)->count();
		echo $AllPersonCount;
	}
	/**
	 * @Title: _before_everymethor
	 * @Description: todo(1:查询自动生成单号，2：查询选中的部门)   
	 * @author  
	 * @date 2013-5-31 下午4:16:45 
	 * @throws 
	*/ 
	private function _before_everymethor($deptid){
		$model=M("mis_system_department");
		$list =$model->where("status=1")->select();
// 		$firstdeptid = $list[0]['id'];
		//查询第一个部门的人数
		$MisHrPersonnelPersonInfoModel = D("MisHrPersonnelPersonInfo");
		$AllPersonCount = $MisHrPersonnelPersonInfoModel->where("status = 1 and working = 1 and deptid = ".$deptid)->count();
// 		echo $MisHrPersonnelPersonInfoModel->getLastSql();
		$this->assign('AllPersonCount',$AllPersonCount);
		$deptlist=$this->selectTree($list,0,0,$deptid);
		$this->assign("deptidlist",$deptlist);
		//自动生成单号
		$scnmodel = D('SystemConfigNumber');
		$orderno = $scnmodel->GetRulesNO('mis_hr_personnel_applicationinfo');
		$this->assign("orderno", $orderno);
		//订单号是否可写
		$writable= $scnmodel->GetWritable('mis_hr_personnel_applicationinfo');
		$this->assign("writable",$writable);
	}
	/**
	 * @Title: common 
	 * @Description: todo(公共调用函数,处理传过来的数组形式的数据)   
	 * @author  
	 * @date 2013-5-31 下午4:07:47 
	 * @throws 
	*/
	private function common(){
		$manageList=array();
		if($_POST['dutyname']){
			foreach ($_POST['dutyname'] as $key=>$val){
				$manageList[]=array(
						'dutyname'=>$val,
						'dutycount'=>$_POST['dutycount'][$key],
						'qualification'=>$_POST['qualification'][$key],
						'dutystatement'=>$_POST['dutystatement'][$key],
				);
			}
			$_POST['positiondetails']=serialize($manageList);
		}else{
			 $this->error("岗位信息不能为空！");
		}
		 
	}
}
?>