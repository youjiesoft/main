<?php
/**
 * @Title: file_name
 * @Package package_name
 * @Description: todo(部门模型)
 * @author liminggang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-6-17 上午10:52:37
 * @version V1.0 
 */
class MisSystemDepartmentModel extends CommonModel{
	protected $trueTableName = 'mis_system_department';
	public $_auto	=array(
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
	);
	/**
	 *
	 * @Title: getDeptZtree
	 * @Description: todo(传入公司id生成树形结构 $parentisclick顶级是否可点击)
	 * @param unknown_type $deptlist
	 * @return multitype:multitype:string boolean unknown
	 * @author renling
	 * @date 2014-9-3 上午10:51:02
	 * @throws
	 */
	public function getDeptZtree($companyid,$parentisclick,$rel,$method,$url,$checkuser){
		//查询所有部门信息
		$where = array();
		$where['status'] = 1;
		if($companyid){
			$where['companyid'] = $companyid;
		}
		if(!$method){
			$method="index";
		}
		$deptlist = $this->where($where)->select();
		$MisHrPersonnelUserDeptViewModel=D('MisHrPersonnelUserDeptView');
		$arr = array();
		foreach($deptlist as $k=>$v){
			if($v['iscompany']==1){
				$pid=0;
			}else{
				$pid=$v['parentid'];
			}
			if($v['iscompany']==1&&!$parentisclick){
				$arr[] = array(
						'id'=>-$v['id'],
						'name'=>$v['name'],
						'title'=>$v['name'],
						'pId'=>$pid,
						'isParent'=>true,
						'open'=>'true',
				);
			}else{
				if($checkuser){
					$hrMap=array();
					$hrMap['_string']="user_dept_duty.status=1  and user.status>0 and user_dept_duty.deptid=".$v['id'];
					if($companyid){
						$newcompanyid=$companyid;
					}else{
						$newcompanyid=$v['companyid'];
					}
					$hrMap['user_dept_duty.companyid']=$newcompanyid;
					//查询该部门下的人员
					$MisHrPersonnelList=$MisHrPersonnelUserDeptViewModel->where($hrMap)->select();
					if($MisHrPersonnelList){
						$eamil=array();
						$userid=array();
						$username=array();
						$mobile=array();
						$personid=array();
						foreach ($MisHrPersonnelList as $hrkey=>$hrval){
							if($hrval['id']){
								$arr[] = array(
										'id'=>$hrval['id'],
										'email'=>$hrval['email'],
										'mobile'=>$hrval['mobile'],
										'personid'=>$hrval['id'],
										'userid'=>$hrval['userid'],
										'deptname'=>getFieldBy($hrval['deptid'], "id", "name", "mis_system_department"),
										'deptid'=>$hrval['deptid'],
										'worktype'=>$hrval['worktype'],
										'worktypename'=>getFieldBy($hrval['worktype'], "id", "name", "mis_hr_job_info"),
										'dutyid'=>$hrval['dutyid'],
										'chinaid'=>$hrval['chinaid'],
										'dutyname'=>getFieldBy($hrval['dutyid'], "id", "name", "mis_system_duty"),
										'indate'=>transTime($hrval['indate']),
										'pinyin'=>$hrval['pinyin'],
										'name'=>$hrval['name'],
										'title'=>$hrval['name'],
										'username'=>$hrval['username'],
										'pId'=>-$v['id'],
										'type'=>'post',
										"icon" => "__PUBLIC__/Images/icon/group.png",
										'open'=>'true',
								);
								$eamil[]=$hrval['email'];
								$userid[]=$hrval['userid'];
								$username[]=$hrval['username'];
								$mobile[]=$hrval['mobile'];
								$personid[]=$hrval['id'];
							}
						}
					}
					$arr[] = array(
						'id'=>-$v['id'],
						'ename'=>'组部门',
						'email'=>implode(",",$eamil),
						'mobile'=>implode(",",$mobile),
						'username'=>implode(",",$username),
						'personid'=>implode(",",$personid),
						'userid'=>implode(",",$userid),
						'name'=>$v['name'],
						'title'=>$v['name'],
						'pId'=>-$pid,
						'type'=>'post',
						'isParent'=>true,
						'open'=>'true',
					);
					$eamil=array();
					$userid=array();
					$username=array();
					$mobile=array();
					$personid=array();
				}else{
					$arr[] = array(
							'id'=>$v['id'],
							'name'=>$v['name'],
							'title'=>$v['name'],
							'pId'=>$pid,
							'type'=>'post',
							'url'=>'__URL__/'.$method.'/jump/jump/deptid/'.$v['id'].'/ptId/'.$v['parentid'].'/companyid/'.$v['companyid'].$url,
							'target'=>'ajax',
							'rel'=>$rel,
							'isParent'=>true,
							'open'=>'false',
					);
				}
				
				$id++;
			}
		}
		return json_encode($arr);
	}
	/**
	 *
	 * @Title: getDeptCombox
	 * @Description: todo(公司、部门、岗位 三级联动下拉菜单 )
	 * @return Ambigous <multitype:, multitype:unknown >
	 * @author renling
	 * @date 2014-9-3 下午3:24:09
	 * @throws
	 */
	public function  getDeptCombox($screecompany){
		//查询岗位
		$MisHrJobInfoModel=D('MisHrJobInfo');
		$MisHrJobInfoList=$MisHrJobInfoModel->where("status=1")->field("id,name,deptid,iscompany")->select();
		$deptList = $this->where(" status=1  ")->order("orderno")->select();
		$companyList=array();
		$newDeptList=array();
		$newJobList=array();
		foreach ($deptList as $key=>$val){
			if(in_array($val['companyid'],array_keys($companyList))){
				if($val['iscompany']!=1){
				$newDeptList['dept'][]=array(
						'id'=>$val['id'],
						'name'=>$val['name'],
						'companyid'=>$val['companyid'],
				);
				}
			}else{
				$companyList[$val['companyid']]=1;
				$newDeptList['company'][]=array(
						'id'=>$val['companyid'],
						'name'=>getFieldBy($val['companyid'], "id", "name", "mis_system_company"),
				);
				if($val['iscompany']!=1){
				$list=array(
							'id'=>$val['id'],
							'name'=>$val['name'],
							'companyid'=>$val['companyid'],
					);
				}
				 if($val['iscompany']!=1){
				 	$newDeptList['dept'][]=$list;
				 }
				 $newDeptList['dept'][]=array(
				 		'id'=>0,
				 		'name'=>"顶级部门",
				 		'companyid'=>$val['companyid'],
				 );
			}
			foreach ($MisHrJobInfoList as $jkey=>$jval){
				if($jval['deptid']==$val['id']){
					$newDeptList['job'][]=array(
							'id'=>$jval['id'],
							'name'=>$jval['name'],
							'deptid'=>$val['id'],
					);
				}
			}
		}
		return json_encode($newDeptList);
	}
	/**
	 * 
	 * @Title: getDeptRoleGroup
	 * @Description: todo(获取部门角色信息)   
	 * @author renling 
	 * @date 2014-9-4 上午11:03:52 
	 * @throws
	 */
	public function getDeptRoleGroup($deptid=''){
		if($deptid==''){
			$deptid=$_REQUEST['deptid'];
		}
		$relist=array();  //存储绑定了用户的部门角色
		if($deptid){
			//部门角色和用户绑定关系表
			$recodemodel = M ("mis_organizational_recode");
			$where = array();
			$where['deptid'] = $deptid;
			$recodelist = $recodemodel->where($where)->select();
			foreach ($recodelist as $k=>$v){
				$relist[$v["rolegroup_id"]][] = explode(",", $v['userid']);
			}
		}
		return $relist;
	}
	
	/**
	 * @Title: getDeptManager 
	 * @Description: todo(根据部门获取部门经理) 
	 * @param 部门ID $deptid
	 * @author liminggang
	 * @date 2015-7-7 下午3:55:25 
	 * @throws
	 */
	public function getDeptManager($deptorderno){
		//获取查询的部门经理
		$manager = "部门经理";
		if(file_exists(DConfig_PATH."/System/list.inc.php")){
			require DConfig_PATH."/System/list.inc.php";
			$manager = $aryRule['部门经理'];
		}
		//指定部门角色为“部门经理”，据此查询角色id
		$roleid = M ( "rolegroup" )->where ( "name='{$manager}' and status=1 and catgory=5" )->getField("id");
		if($roleid && $deptorderno){
			$deptid = getFieldBy($deptorderno,"orderno","id","MisSystemDepartment");
			$manageruserid = M ( "mis_organizational_recode" )->where ( "rolegroup_id={$roleid} and deptid={$deptid} and status=1" )->getField("userid");
			if($manageruserid){
				return $manageruserid;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
}
?>