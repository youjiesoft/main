<?php
//Version 1.0
// 高级角色模型
class RolegroupModel extends CommonModel {
	protected $trueTableName = 'rolegroup';
	public $_validate = array(
		array('name,status','require','名称必须'),
		);
	
public $_auto	=array(
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
		    
			array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),

	);
	
	function getGroupUserList($groupId)
	{
		$table = $this->tablePrefix.'rolegroup_user';
		$rs = $this->db->query('select b.id,b.name,b.email from '.$table.' as a ,'.$this->tablePrefix.'user as b where a.user_id=b.id and  a.rolegroup_id='.$groupId.' ');
		return $rs;
	}
	
	function delGroupUser($groupId)
	{
		$table = $this->tablePrefix.'rolegroup_user';
	
		$result = $this->db->execute('delete from '.$table.' where rolegroup_id='.$groupId);
		if($result===false) {
			return false;
		}else {
			return true;
		}
	}
	/**
	 +----------------------------------------------------------
	 * 指向性：用户增加组操作权限
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param  int    $groupId     权限组id
	 * @param  string $userIdList  用户id字符串（“，”号分隔）
	 +----------------------------------------------------------
	 * @return Boolean
	 +----------------------------------------------------------
	 * @throws FcsException
	 +----------------------------------------------------------
	 */	
	function AddGroupUsers($groupId,$userIdList)
	{
		$model=M('RolegroupUser');
		if(empty($userIdList)) {
			return true;
		}
		if(is_string($userIdList)) {
			$userIdList = explode(',',$userIdList);
		}
		foreach($userIdList as $key=>$val){
			$val=intval($val);
			$sql="
			INSERT INTO rolegroup_user(rolegroup_id,user_id)
			SELECT ".$groupId." , ".$val." from rolegroup_user
			WHERE not exists (select * from rolegroup_user
			where rolegroup_id= ".$groupId." and  user_id= ".$val." )
			limit 1
			";
			$result=$model->execute($sql);
			if($result===false) {
				return false;
			}
		}
			return true;
	}
	/**
	 +----------------------------------------------------------
	 * 指向性：用户删除组操作权限
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param  int    $groupId     权限组id
	 * @param  string $userIdList  用户id字符串（“，”号分隔）
	 +----------------------------------------------------------
	 * @return Boolean
	 +----------------------------------------------------------
	 * @throws FcsException
	 +----------------------------------------------------------
	 */
	function DelGroupUsers($groupId,$userIdList)
	{		
		$model=M('RolegroupUser');
		if(empty($userIdList)) {
			return true;
		}
		if(is_string($userIdList)) {
			$userIdList = explode(',',$userIdList);
		}
		foreach($userIdList as $key=>$val){
			$map['rolegroup_id']=$groupId;
			$map['user_id']=intval($val);
			$result=$model->where($map)->delete();
			if($result===false) {
				return false;
			}
		}
			return true;
	}	
	
	function setGroupUsers($groupId,$userIdList)
	{
		if(empty($userIdList)) {
			return true;
		}
		if(is_string($userIdList)) {
			$userIdList = explode(',',$userIdList);
		}
		array_walk($userIdList, array($this, 'fieldFormat'));
		$userIdList	 =	 implode(',',$userIdList);
		$where = 'a.id ='.$groupId.' AND b.id in('.$userIdList.')';
		$result = $this->execute('INSERT INTO '.$this->tablePrefix.'rolegroup_user (rolegroup_id,user_id) SELECT a.id, b.id FROM '.$this->tablePrefix.'rolegroup a, '.$this->tablePrefix.'user b WHERE '.$where);
		if($result===false) {
			return false;
		}else {
			return true;
		}
	}
	
	protected function fieldFormat(&$value)
	{
		if(is_int($value)) {
			$value = intval($value);
		} else if(is_float($value)) {
			$value = floatval($value);
		}else if(is_string($value)) {
			$value = '"'.addslashes($value).'"';
		}
		return $value;
	}
	
	//获取角色
	function getRoleId(){
		//获取查询的部门经理
		$manager = "部门经理";
		if(file_exists(DConfig_PATH."/System/list.inc.php")){
			require DConfig_PATH."/System/list.inc.php";
			$manager = $aryRule['部门经理'];
		}
		//指定部门角色为“部门经理”，据此查询角色id
		$roleid = M ( "rolegroup" )->where ( "name='{$manager}' and status=1 and catgory=5" )->getField("id");
		return $roleid;
	}
	
}
?>