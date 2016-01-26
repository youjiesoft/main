<?php
/**
 * 项目权限组用户模型
 * @author liuzhihong
 * @data 2015-10-12
 */
class MisSalesMyProjectRolegroupUserModel extends CommonModel {
	protected $trueTableName = 'mis_sales_project_rolegroup_user';
	//存储部门ID
	private $deptidlists = array();
	//存储用户ID
	private $useridlists = array();
	//存储项目ID
	private $projectidlists = array();
	public $_auto =array(
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
	  		array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),

	);
	//查询用户所有权限以及等级 还有单独授权项目
	public function getPlevelslists($userid){
		$plevels = $this->table("mis_sales_project_rolegroup a")->join("LEFT JOIN mis_sales_project_rolegroup_user b ON a.id =b.rolegroup_id ")->where("b.user_id = ".$userid." OR a.userid = ".$userid)->getField("a.id,plevels,rules,dandushouquanID");
		return $plevels;
	}
	//查询用户的部门所在的子部门
	public function getDeptid($userid,$Plevels){
		$deptid = $this->table("user_dept_duty")->where("userid = ".$userid)->getField("deptid");
		array_push($this->deptidlists,$deptid);
		//如果权限等于2才查询子部门
		if($Plevels == 2){
			//调用递归查询子部门的方法
			$this->getDeptidList($deptid);
		}
		//删除相同的部门
		$this->deptidlists = array_unique($this->deptidlists);
		return $this->deptidlists;
	}
	//递归查询到下级所有部门
	public function getDeptidList($deptid){
		$deptidlist = $this->table("mis_system_department")->where("parentid = ".$deptid)->getField("id,parentid");
		foreach($deptidlist as $k=>$v){
			//把查询到的所有ID放进私有的属性内
			array_push($this->deptidlists,$k);
			$this->getDeptidList($k);
		}
	}
	//查询部门下的所有ID
	public function getDeptidByUserId($deptidlists){
		foreach($deptidlists as $k=>$v){
			$useridlist = $this->table("user_dept_duty")->where("deptid = ".$v)->getField("userid,deptid");
			//合并保存数组 并且把值保存到useridlists里
			foreach($useridlist as $k=>$v){
				array_push($this->useridlists,$k);
			}
		}
		//删除相同的ID
		$this->useridlists = array_unique($this->useridlists);
		return $this->useridlists;
	}
	//查询所有id对应的ProjectId $useridlists是用户数组 $rules是过滤条件
	public function getMyProjectIdList($useridlists,$rules){
		//遍历每个ID 去查询得到每个人的项目ID
 		foreach($useridlists as $k=>$v){
 			$list = $this->getMyProjectdanIdList($v,$rules);
 			foreach ($list as $k=>$v){
 				//把获得的项目ID添加到projectidlists里面
 				array_push($this->projectidlists ,$v);
 			}
		}
		//去除重复的项目ID
		$this->projectidlists = array_unique($this->projectidlists);
		return $this->projectidlists;
	}
	//查询所有单个ID的ProjectId
	public function getMyProjectdanIdList($userid,$rules){
		//替换掉特殊
		if ($rules) {
			$rules = str_replace ( array (
					'&quot;',
					'&#39;',
					'&lt;',
					'&gt;',
					'=='
			), array (
					'"',
					"'",
					'<',
					'>',
					'='
			), $rules);}
		//当前传入的用户能查看的项目ID和过滤条件
		//$where = array();
		//根据用户获取能查看的项目
		//$where['userid'] = $userid;
		//if($rules){
			//$where['_string'] = $rules;
		//}
		//$manangerprojectidArr = $this->table("mis_project_flow_manager")->join("mis_auto_bklne ON mis_project_flow_manager.projectid =mis_auto_bklne.projectid ")->where($where)->getField("mis_auto_bklne.projectid,mis_project_flow_manager.projectid");
		//$viewProject .=",".implode(",",$manangerprojectidArr);
		//分派人和执行人
		$where = array();
		//根据用户获取能查看的项目
		$where['_string'] = "( mis_project_flow_resource.executorid = ".$userid." ) OR (mis_project_flow_resource.alloterid = ".$userid.")";
		if($rules){
			$where['_string'] .= " AND ( ".$rules." ) ";
		}
		$fenandzhiprojectidArr = $this->table("mis_project_flow_resource")->join("INNER JOIN mis_auto_bklne ON mis_project_flow_resource.projectid =mis_auto_bklne.projectid")->where($where)->getField("mis_auto_bklne.projectid,mis_project_flow_resource.projectid");
		$viewProject .=",".implode(",",$fenandzhiprojectidArr);
		//零时协作人员
		$where = array();
		$where['chengyuanmingchen'] = $userid;
		if($rules){
			$where['_string'] .= $rules;
		}
		//根据用户获取能查看的项目
		$linshiprojectidArr = $this->table("mis_auto_twidh_sub_xiezuochengyuanqingk AS a")->join("INNER JOIN mis_auto_twidh AS b INNER JOIN mis_auto_bklne AS mis_auto_kimpu ON a.masid = b.id AND b.projectid = mis_auto_bklne.projectid")->where($where)->getField("mis_auto_bklne.projectid,b.projectid");
		$viewProject .=",".implode(",",$linshiprojectidArr);
		//去重。
		$readuseridArr = array_unique(array_filter(explode(",", $viewProject)));
		return $readuseridArr;
	
	}
	//获取根据过滤条件的全部项目ID 
	public function getMyProjectIdLists($rules){
		if ($rules) {
			$rules = str_replace ( array (
					'&quot;',
					'&#39;',
					'&lt;',
					'&gt;',
					'=='
			), array (
					'"',
					"'",
					'<',
					'>',
					'='
			), $rules);}
			//根据过滤条件获取能所有查看的项目
			if($rules){
				$where['_string'] = $rules;
			}
			$projectidArr = $this->table("mis_auto_bklne")->where($where)->getField("id,projectid");
			return $projectidArr;
	}
	
	public function getRoleGroupByUserId($userid){
		$rolegrouplist = $this->where("user_id = ".$userid)->getField("rolegroup_id");
		return $rolegrouplist;
	}
}
?>