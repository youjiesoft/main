<?php
//Version 1.0
// 角色模型
class RoleModel extends CommonModel {
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

	function setGroupApps($groupId,$appIdList)
	{
		if(empty($appIdList)) {
			return true;
		}
		$id = implode(',',$appIdList);
		$where = 'a.id ='.$groupId.' AND b.id in('.$id.')';
		$result = $this->db->execute('INSERT INTO '.$this->tablePrefix.'access (role_id,node_id,pid,level,type) SELECT a.id, b.id,b.pid,b.level,b.type FROM '.$this->tablePrefix.'role a, '.$this->tablePrefix.'node b WHERE '.$where);
		if($result===false) {
			return false;
		}else {
			return true;
		}
	}

	function getGroupAppList($groupId)
	{
		$rs = $this->db->query('select b.id,b.title,b.name from '.$this->tablePrefix.'access as a ,'.$this->tablePrefix.'node as b where a.node_id=b.id and  b.pid=0 and a.role_id='.$groupId.' ');
		return $rs;
	}

	function delGroupApp($groupId)
	{
		$table = $this->tablePrefix.'access';
		$result = $this->db->execute('delete from '.$table.' where level=1 and role_id='.$groupId);
		if($result===false) {
			return false;
		}else {
			return true;
		}
	}

	function delGroupModule($groupId,$appId)
	{
		$table = $this->tablePrefix.'access';
		$result = $this->db->execute('delete from '.$table.' where level=2 and pid='.$appId.' and role_id='.$groupId);
		if($result===false) {
			return false;
		}else {
			return true;
		}
	}

	function getGroupModuleList($groupId,$appId)
	{
		$table = $this->tablePrefix.'access';
		$rs = $this->db->query('select b.id,b.title,b.name,b.type from '.$table.' as a ,'.$this->tablePrefix.'node as b where b.status=1 and  a.node_id=b.id and  b.pid='.$appId.' and a.role_id='.$groupId.' ');
		return $rs;
	}
	
	function getMianbanModuleList($groupId,$appId)
	{
		$table = $this->tablePrefix.'access';
		//$rs1 = $this->db->query('select * from '.$table.' as a where a.status=1 and a.pid='.$appId);
		$rs1 = $this->db->query('select b.id,b.title,b.name,b.type from '.$table.' as a ,'.$this->tablePrefix.'node as b where a.node_id=b.id and  b.pid='.$appId.' and  a.role_id='.$groupId.' ');
		
		if( $rs1 ){
			$rs=array();
			foreach($rs1 as $k =>$v ){
				if($v['type']==2 ){
					$rs2=array();
					//$rs2 = $this->db->query('select * from '.$table.' as a where a.status=1 and a.pid='.$v['id']);
					$rs2 = $this->db->query('select b.id,b.title,b.name,b.type from '.$table.' as a ,'.$this->tablePrefix.'node as b where a.node_id=b.id and  b.pid='.$v['id'].' and  a.role_id='.$groupId.' ');
					if($rs2) $rs=array_merge($rs,$rs2);
				}else{
					$m[]=$v;
					$rs=array_merge($rs,$m);
				}
			}
		}
		return $rs;
	}
	

	function setGroupModules($groupId,$moduleIdList)
	{
		if(empty($moduleIdList)) {
			return true;
		}
		if(is_array($moduleIdList)) {
			$moduleIdList = implode(',',$moduleIdList);
		}
		$where = 'a.id ='.$groupId.' AND b.id in('.$moduleIdList.')';
		$result = $this->db->execute('INSERT INTO '.$this->tablePrefix.'access (role_id,node_id,pid,level,type) SELECT a.id, b.id,b.pid,b.level,b.type FROM '.$this->tablePrefix.'role a, '.$this->tablePrefix.'node b WHERE '.$where);
		
		if($result===false) {
			return false;
		}else {
			return true;
		}
	}

function delGroupAction($groupId,$moduleId)
{
	$table = $this->tablePrefix.'access';
	$rs = $this->db->query('select type,node_id from '.$table.' where level=3 and pid='.$moduleId.' and role_id='.$groupId);
	foreach($rs as $k =>$v){
		if( $v['type']==2 ){
			$result = $this->db->execute('delete from '.$table.' where level=3 and type=3 and pid='.$v['node_id'].' and role_id='.$groupId);
		}
	}
	$result = $this->db->execute('delete from '.$table.' where level=3 and pid='.$moduleId.' and role_id='.$groupId);
	if($result===false) {
	    return false;
	}else {
	    return true;
	}
}

function delGroupAction2($groupId,$moduleId)
{
    $table = $this->tablePrefix.'access';

    $result = $this->db->execute('delete from '.$table.' where level=4 and pid='.$moduleId.' and role_id='.$groupId);
    if($result===false) {
        return false;
    }else {
        return true;
    }
}

	function getGroupActionList($groupId,$moduleId)
	{
		$table = $this->tablePrefix.'access';
		$rs1 = $this->db->query('select b.id,b.title,b.name,b.type from '.$table.' as a ,'.$this->tablePrefix.'node as b where a.node_id=b.id and  b.pid='.$moduleId.' and  a.role_id='.$groupId.' ');
		if( $rs1 ){
			$rs=array();
			foreach($rs1 as $k =>$v ){
				if($v['type']==2 ){
					$rs2=array();
					$rs2 = $this->db->query('select b.id,b.title,b.name,b.type from '.$table.' as a ,'.$this->tablePrefix.'node as b where a.node_id=b.id and  b.pid='.$v['id'].' and  a.role_id='.$groupId.' ');
					if($rs2) $rs=array_merge($rs,$rs2);
				}else{
					$m[]=$v;
					$rs=array_merge($rs,$m);
				}
			}
		}
		return $rs;
	}

	/**
	 * @Title: setGroupActions 
	 * @Description: 插入4级操作节点的上 access数据，操作节点绑定，模块绑定，面板绑定，admin节点绑定
	 * @param 分组group的id $groupId
	 * @param role基础节点ID组合array $actionIdList
	 * @param 是否带绑定 $binding
	 * @return boolean  返回是否插入成功状态，TRUE，false
	 * @author liminggang 
	 * @date 2014-9-4 下午12:03:40 
	 * @throws
	 */
	function setGroupActions($groupId,$actionIdList, $binding=''){
		if (empty($actionIdList)) {
			return true;
		}
		if(is_array($actionIdList)) {
			$actionIdList = implode(',',$actionIdList);
		}
		$where = 'a.id ='.$groupId.' AND b.id in('.$actionIdList.')';
		if ($binding === true) {
			$result = $this->db->execute('INSERT INTO '.$this->tablePrefix.'access (role_id,plevels,node_id,pid,level,type) SELECT a.id,a.plevels, b.id,b.pid,b.level,b.type FROM '.$this->tablePrefix.'role a, '.$this->tablePrefix.'node b WHERE '.$where);
		}else{
			$result = $this->db->execute('INSERT INTO '.$this->tablePrefix.'access (role_id,node_id,pid,level,type) SELECT a.id, b.id,b.pid,b.level,b.type FROM '.$this->tablePrefix.'role a, '.$this->tablePrefix.'node b WHERE '.$where);
		}
		if($result===false) {
			return false;
		}else {
			return true;
		}
		
	}
	function setpublicGroupActions($groupId,$module,$actionIdList,$addaccess=true){
		if(empty($actionIdList)) {
			return true;
		}
		if(is_array($actionIdList)) {
			$table = $this->tablePrefix.'node';
			$actionIdList = implode(',',$actionIdList);
			$rs = $this->db->query('select id,name,title from '.$table.' where id in ('.$actionIdList.')');
			$insertId=array();
			foreach( $rs as $k=>$v ){
				$c= $this->db->query("select count(*) as total from ".$table." where pid=".$module." and name='".$v['name']."' and level=4");
				if( $c[0]['total']==0 ){
					$sql="INSERT INTO ".$table." (name,title,pid,level,type) values ('".$v['name']."','".$v['title']."',".$module.",4,4".")";
					$r2=$this->execute($sql);
					if($r2===false) {
						return false;
					}else {
						$insertId[]= $this->getLastInsID();
					}
				}
			}
			if( $insertId ){
				if( $addaccess ){
					$insertId=implode(",",$insertId);
					$where = 'a.id ='.$groupId.' AND b.id in('.$insertId.')';
					$result = $this->db->execute('INSERT INTO '.$this->tablePrefix.'access (role_id,node_id,pid,level,type) SELECT a.id, b.id,b.pid,b.level,b.type FROM '.$this->tablePrefix.'role a, '.$this->tablePrefix.'node b WHERE '.$where);
					if($result===false) {
						return false;
					}else {
						return true;
					}
				}else{
					return true;
				}
			}else{
				return true;
			}
		}
	}

	function getGroupUserList($groupId)
	{
		$table = $this->tablePrefix.'role_user';
		$rs = $this->db->query('select b.id,b.name,b.email from '.$table.' as a ,'.$this->tablePrefix.'user as b where a.user_id=b.id and  a.role_id='.$groupId.' ');
		return $rs;
	}

	function delGroupUser($groupId)
	{
		$table = $this->tablePrefix.'role_user';

		$result = $this->db->execute('delete from '.$table.' where role_id='.$groupId);
		if($result===false) {
			return false;
		}else {
			return true;
		}
	}

	function setGroupUser($groupId,$userId) {
		$sql	=	"INSERT INTO ".$this->tablePrefix.'role_user (role_id,user_id) values ('.$groupId.','.$userId.')';
		$result	=	$this->execute($sql);
		if($result===false) {
			return false;
		}else {
			return true;
		}
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
		$result = $this->execute('INSERT INTO '.$this->tablePrefix.'role_user (role_id,user_id) SELECT a.id, b.id FROM '.$this->tablePrefix.'role a, '.$this->tablePrefix.'user b WHERE '.$where);
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

}
?>