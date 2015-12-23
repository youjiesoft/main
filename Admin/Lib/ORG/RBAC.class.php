<?php
class RBAC {
	// 认证方法,获取当前用户的信息
	static public function authenticate($map,$model='')
	{	
		if(empty($model)) $model =  C('USER_AUTH_MODEL');
		//使用给定的Map进行认证
		return M($model)->where($map)->find();
	}

    //用于检测用户权限的方法,并保存到Session中
	static function saveAccessList($authId=null)
	{
		if(null===$authId)   $authId = $_SESSION[C('USER_AUTH_KEY')];
		// 如果使用普通权限模式，保存当前用户的访问权限列表
		// 对管理员开发所有权限
		if(C('USER_AUTH_TYPE') !=2 && !$_SESSION[C('ADMIN_AUTH_KEY')] ){
			//把权限写入文件通过文件判断
			
			$file = DConfig_PATH."/AccessList/access_".$_SESSION[C('USER_AUTH_KEY')].".php";
			if( !file_exists($file) ){
				$accesslist = RBAC::getAccessList($authId);
				if (!file_exists(DConfig_PATH."/AccessList")){
					createFolder(dirname(DConfig_PATH."/AccessList"));
					mkdir(DConfig_PATH."/AccessList", 0777);
				}
				RBAC::writeover($file,"return ".RBAC::pw_var_export($accesslist).";\n",true);
			}
		}
		return ;
	}

	// 取得模块的所属记录访问权限列表 返回有权限的记录ID数组
	static function getRecordAccessList($authId=null,$module='') {
		if(null===$authId)   $authId = $_SESSION[C('USER_AUTH_KEY')];
		if(empty($module))  $module	=	MODULE_NAME;
		//获取权限访问列表
		$accessList = RBAC::getFileModuleAccessList($authId,$module);
		return $accessList;
	}

    //检查当前操作是否需要认证
    static function checkAccess()
    {
        //如果项目要求认证，并且当前模块需要认证，则进行权限认证
        if( C('USER_AUTH_ON') ){
			$_module	=	array();
			$_action	=	array();
			//读取无需认证的模块及方法
			require  DConfig_PATH.'/System/notauth.php';
            if("" != C('REQUIRE_AUTH_MODULE')) {
                //需要认证的模块
                $_module['yes'] = explode(',',strtoupper(C('REQUIRE_AUTH_MODULE')));
            }else {
                //无需认证的模块
                $_module['no'] = explode(',',strtoupper($notauth['NOT_AUTH_MODULE']));
            }
            //检查当前模块是否需要认证
            if((!empty($_module['no']) && !in_array(strtoupper(MODULE_NAME),$_module['no'])) || (!empty($_module['yes']) && in_array(strtoupper(MODULE_NAME),$_module['yes']))) {
				if("" != C('REQUIRE_AUTH_ACTION')) {
					//需要认证的操作
					$_action['yes'] = explode(',',strtoupper(C('REQUIRE_AUTH_ACTION')));
				}else {
					//无需认证的操作
					$_action['no'] = explode(',',strtoupper($notauth['NOT_AUTH_ACTION']));
				}
				//检查当前操作是否需要认证
				if((!empty($_action['no']) && !in_array(strtoupper(ACTION_NAME),$_action['no'])) || (!empty($_action['yes']) && in_array(strtoupper(ACTION_NAME),$_action['yes']))) {
					return true;
				}else {
					return false;
				}
            }else {
                return false;
            }
        }
        return false;
    }

    //权限认证的过滤器方法
	static public function AccessDecision($appName=APP_NAME)
	{
		//检查是否需要认证
		if(RBAC::checkAccess()) {
			//存在认证识别号，则进行进一步的访问决策
			$accessGuid   =   md5($appName.MODULE_NAME.ACTION_NAME);
			if(empty($_SESSION[C('ADMIN_AUTH_KEY')])) {
				if(C('USER_AUTH_TYPE')==2) {
					//加强验证和即时验证模式 更加安全 后台权限修改可以即时生效
					//通过数据库进行访问检查
					$accessList = RBAC::getAccessList($_SESSION[C('USER_AUTH_KEY')]);
				}else {
					// 如果是管理员或者当前操作已经认证过，无需再次认证
					//if( $_SESSION[$accessGuid]) {by wangcheng
					if($_SESSION[strtolower(MODULE_NAME.'_'.ACTION_NAME)]){
						return true;
					}
					//登录验证模式，比较登录后保存的权限访问列表
					//$accessList = $_SESSION['_ACCESS_LIST'];
					
					$file =  DConfig_PATH."/AccessList/access_".$_SESSION[C('USER_AUTH_KEY')].".php";
					if( !file_exists($file) ){
						if(null===$authId)   $authId = $_SESSION[C('USER_AUTH_KEY')];
						$accessList =RBAC::getAccessList($authId);
						if (!file_exists(DConfig_PATH."/AccessList")){
							createFolder(dirname(DConfig_PATH."/AccessList"));
							mkdir(DConfig_PATH."/AccessList", 0777);
						}
						RBAC::writeover($file,"return ".RBAC::pw_var_export($accessList).";\n",true);
						if(!C("_access_list")){
							foreach($accessList as $k3 => $v3){
								foreach($accessList[$k3] as $k1 => $v1 ){
									foreach($accessList[$k3][$k1]  as $k => $v ){
										$p=explode("-",$v);
										if($p[1]!=5){//过滤禁止权限
											$_SESSION[strtolower($k1.'_'.$k)] = $p[1];
										}
									}
								}
							}
							C("_access_list",true);
						}
					}else{
						$accessList=require $file;
						if(!C("_access_list")){
							foreach($accessList as $k3 => $v3){
								foreach($accessList[$k3] as $k1 => $v1 ){
									foreach($accessList[$k3][$k1]  as $k => $v ){
										$p=explode("-",$v);
										if($p[1]!=5){//过滤禁止权限
											$_SESSION[strtolower($k1.'_'.$k)] = $p[1];
										}
									}
								}
							}
							C("_access_list",true);
						}
					}
				}
				
			    //判断是否为组件化模式，如果是，验证其全模块名
				$module = defined('P_MODULE_NAME')?  P_MODULE_NAME   :   MODULE_NAME;
				if(!isset($accessList[strtoupper($appName)][strtoupper($module)][strtoupper(ACTION_NAME)])) {
					$n=substr(ACTION_NAME,0,6) == "lookup" ? 1:0;
					$n2=substr(ACTION_NAME,0,6) == "combox" ? 1:0;
					$n3=substr($module,0,7) == "MisAuto" ? 1:0;
					if( $n ||$n2 || $n3){
					    $_SESSION[strtolower(MODULE_NAME.'_'.ACTION_NAME)]=true;
					}else{
					    $_SESSION[strtolower(MODULE_NAME.'_'.ACTION_NAME)]=   false;
					    return false;
					}
				}else{
					$p=explode("-",$accessList[strtoupper($appName)][strtoupper($module)][strtoupper(ACTION_NAME)]);
					if($p[1]!=5){//过滤禁止权限
						$_SESSION[strtolower(MODULE_NAME.'_'.ACTION_NAME)]=$p[1];
					}else{
						return false;
					}
				}
			}else{
				//管理员无需认证
				$_SESSION['a']	=	true;
				return true;
			}
		}
		return true;
	}

	function findAllrole($list,$id,$hasself=true){
		if($id){
		    $arr="";
		    if( $hasself ) $arr =",".$id;
		    foreach ($list as $k=>$v){
			    if($v['id']==$id){
				    unset($list[$k]);
				    $arr.=RBAC::findAllrole($list,$v['pid']);
			    }
		    }
		    return $arr;
		}
	}
	
	/**
	+----------------------------------------------------------
	* 查找出当前用户所有继承及所属的组
	+----------------------------------------------------------
	* @param integer $authId 用户ID
	* @param integer $t 类型 0 全部,1(role_user)组，2(rolegroup)组
	+----------------------------------------------------------
	* @access public
	*
	*/
	public function getAuthorRole($authId,$t=0){
		$myallroles="";
		if($authId){
			$db     =   Db::getInstance(C('RBAC_DB_DSN'));
			if($t==0 || $t==1){
				$table = array('role'=>C('RBAC_ROLE_TABLE'),'role_user'=>C('RBAC_USER_TABLE'));
				$sql_user_1    =   "select role_user.role_id from ".$table['role_user']." as role_user "."where role_user.user_id='{$authId}'";
				$sql_user_2    =   "select role.id,role.pid from ".$table['role']." as role "."where role.status=1 and role.pid is not null";
				$myroles =   $db->query($sql_user_1);
				$allroles =   $db->query($sql_user_2);
				foreach( $myroles as $k=>$v ){
				    $myallroles.=RBAC::findAllrole($allroles,$v['role_id']);
				}
			}
			
			if($t==0 || $t==2){
				$table = array('role_rolegroup'=>"role_rolegroup",'rolegroup_user'=>"rolegroup_user");		
				$sql_rolegroup=   "select rolegroup_user.rolegroup_id from ".$table['rolegroup_user']." as rolegroup_user "."where rolegroup_user.user_id='{$authId}' group by rolegroup_user.rolegroup_id,rolegroup_user.user_id";
				$myrolesgroup =   $db->query($sql_rolegroup);
				foreach($myrolesgroup as $key=>$value){
					$sql_group_1    =   "select role_rolegroup.role_id from ".$table['role_rolegroup']." as role_rolegroup "."where role_rolegroup.rolegroup_id='{$value[rolegroup_id]}'";
					$myrolesTop =   $db->query($sql_group_1);
					foreach( $myrolesTop as $k=>$v ){
					    $myallroles.=RBAC::findAllrole($allroles,$v['role_id']);
					}
				}
			}
			$myallroles = substr($myallroles, 1);
			$myallroles = explode(",",$myallroles);
			$myallroles = array_unique($myallroles);
			$myallroles =implode(",",$myallroles);
		}
		return $myallroles;
	}

	/**
	 +----------------------------------------------------------
	 * 取得当前认证号的所有权限列表
	 +----------------------------------------------------------
	 * @param integer $authId 用户ID
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 */
	static public function getAccessList($authId)
	{
		//获取当前用户所有组
		$myallroles=RBAC::getAuthorRole($authId);
		$db     =   Db::getInstance(C('RBAC_DB_DSN'));
		$table = array('role'=>C('RBAC_ROLE_TABLE'),'user'=>C('RBAC_USER_TABLE'),'access'=>C('RBAC_ACCESS_TABLE'),'node'=>C('RBAC_NODE_TABLE'));
		/*$sql    =   "select DISTINCT(node.id),node.name from ".
			    $table['role']." as role,".
			    $table['user']." as user,".
			    $table['access']." as access ,".
			    $table['node']." as node ".
			    "where access.role_id in ({$myallroles}) and role.status=1 and access.node_id=node.id and node.level=1 and node.status=1";*/
		 
		//获取用户所在项目
		$sql2 = "select node.id ,node.name,node.group_id from node LEFT JOIN  access ON node.id=access.node_id where access.role_id in(".$myallroles.") and node.level=1 and node.status=1 group by node.id";
		$apps =   $db->query($sql2);
		
		$access =  array();
		$modules=array();
		$already_app=array();
		foreach($apps as $key=>$app) {
			if( $already_app[$app['id']]){
				continue;
			}else{
				$already_app[$app['id']]=$app['id'];
			}
			
			$appId	 =$app['id'];
			$appName =$app['name'];
			$access[strtoupper($appName)]   =  array();
			// 读取项目的面板权限
			/*$sql="select DISTINCT(node.id),node.name from ".
				$table['role']." as role,".
				$table['user']." as user,".
				$table['access']." as access ,".
				$table['node']." as node ".
				"where access.role_id in ({$myallroles}) and role.status=1 and access.node_id=node.id and node.level=2 and node.pid={$appId} and node.status=1";*/
			//获取用户所在项目的面板
			$sql2 = "select node.id ,node.name,node.group_id from node LEFT JOIN  access ON node.id=access.node_id where access.role_id in(".$myallroles.") and node.level=2  and node.pid=".$appId." and node.status=1" ;
			$mianban =$db->query($sql2);
			// 读取项目的模块权限
			$already_mianban=array();
			foreach($mianban as $k=>$v){
				if( $already_mianban[$v['id']]){
					continue;
				}else{
					$already_mianban[$v['id']]=1;
				}
				/*$sql    =   "select DISTINCT(node.id),node.type,node.name from ".
					$table['role']." as role,".
					$table['user']." as user,".
					$table['access']." as access ,".
					$table['node']." as node ".
					"where access.role_id in ({$myallroles}) and role.status=1 and access.node_id=node.id and node.level=3 and node.pid={$v['id']} and node.status=1";*/
				$sql2 = "select node.id ,node.type,node.name,node.group_id from node LEFT JOIN  access ON node.id=access.node_id where access.role_id in(".$myallroles.") and node.level=3  and node.pid=".$v['id']." and node.status=1" ;
				$m =$db->query($sql2);
				if( $m ){
					$modules= array_merge($modules,$m);
					foreach($m as $k2=>$v2 ){
						if( $v2['type']==2 ){
							/*$sql2    =   "select DISTINCT(node.id),node.type,node.name from ".
							$table['role']." as role,".
							$table['user']." as user,".
							$table['access']." as access ,".
							$table['node']." as node ".
							"where access.role_id in ({$myallroles}) and role.status=1 and access.node_id=node.id and node.level=3 and node.pid={$v2['id']} and node.status=1";*/
							$sql2 = "select node.id ,node.type,node.name from node LEFT JOIN  access ON node.id=access.node_id where access.role_id in(".$myallroles.") and node.level=3  and node.pid=".$v2['id']." and node.status=1" ;
							$m2 =   $db->query($sql2);
							if( $m2 ) $modules= array_merge($modules,$m2);
						}
					}
				}		
			}
			
			// 判断是否存在公共模块的权限
			$publicAction  = array();
			foreach($modules as $k3=>$v3) {
				$moduleId=$v3['id'];
				$moduleName=$v3['name'];
				if('PUBLIC'== strtoupper($moduleName)) {
				/*$sql    =   "select DISTINCT(node.id),node.name,access.plevels from ".
					$table['role']." as role,".
					$table['user']." as user,".
					$table['access']." as access ,".
					$table['node']." as node ".
					"where access.role_id in ({$myallroles}) and role.status=1 and access.node_id=node.id and node.level=4 and node.pid={$moduleId} and node.status=1";*/
					$sql2 = "select node.id ,node.name,node.group_id,access.plevels from node LEFT JOIN  access ON node.id=access.node_id where access.role_id in(".$myallroles.") and node.level=4 and node.pid=".$moduleId." and node.status=1" ;
					$rs =   $db->query($sql2);
					foreach ($rs as $a){
						$publicAction[$a['name']] = $a['id']."-".$a['plevels'];
					}
					unset($modules[$k3]);
					break;
				}
			}
			
			 // 依次读取模块的操作权限
			 
			$already_action = array();
			foreach($modules as $k4=>$v4) {
				if( $already_action[$v4['id']]){
					continue;
				}else{
					$already_action[$v4['id']]=1;
				}
				
				if($v4['type']==2) continue;
				$moduleId=$v4['id'];
				$moduleName =$v4['name'];
				/*$sql =   "select DISTINCT(node.id),node.name,access.plevels from ".
					$table['role']." as role,".
					$table['user']." as user,".
					$table['access']." as access ,".
					$table['node']." as node ".
					"where access.role_id in ({$myallroles}) and role.status=1 and access.node_id=node.id and node.level=4 and node.pid={$moduleId} and node.status=1";*/
				$sql2 = "select node.id ,node.name,node.group_id,access.plevels from node LEFT JOIN  access ON node.id=access.node_id where access.role_id in(".$myallroles.") and node.level=4 and node.pid=".$moduleId." and node.status=1" ;
				
				$rs =   $db->query($sql2);
				$action = array();
				foreach ($rs as $a){
					$action['GROUPID'] = $a['group_id'];
					if( isset( $action[$a['name']] )){
						$ex=explode("-",$action[$a['name']]);
						if($ex[1]>$a['plevels']){
							$action[$a['name']] = $a['id']."-".$a['plevels'];
						}
					}else{
						$action[$a['name']] = $a['id']."-".$a['plevels'];
					}					
				}
				foreach ($publicAction as $b=>$c){
					if( !isset($action[$b]) ) $action[$b] = $c;
				}
				// 和公共模块的操作权限合并
				//$action += $publicAction;
				$access[strtoupper($appName)][strtoupper($moduleName)]   =  array_change_key_case($action,CASE_UPPER);
				
			}
		}
		//获取我的权限
		$access2 =RBAC::getAccessListSelf($authId);
		if($access2){
			//过滤相关权限
			foreach($access2 as $k=>$v){
				foreach($v as $k2=>$v2){
					foreach($v2 as $k3=>$v3){
						$ex=explode("-",$v3);
						if($ex[1]==5){
							unset($access[$k][$k2][$k3]);
						}else{
							$access[$k][$k2][$k3]=$v3; 
						}
					}
					if(count($access[$k][$k2])==0){
						unset($access[$k][$k2]);
					}
				}
			}
		}
		return $access;
	}
	
	static public function getAccessListSelf($authId)
	{
		//获取当前用户所有组
		$myallroles=RBAC::getAuthorRole($authId,1);
		$db     =   Db::getInstance(C('RBAC_DB_DSN'));
		$table = array('role'=>C('RBAC_ROLE_TABLE'),'user'=>C('RBAC_USER_TABLE'),'access'=>C('RBAC_ACCESS_TABLE'),'node'=>C('RBAC_NODE_TABLE'));
		//获取用户所在项目
		$sql2 = "select node.id ,node.name from node LEFT JOIN  access ON node.id=access.node_id where access.role_id in(".$myallroles.") and node.level=1 and node.status=1";
		$apps =   $db->query($sql2);
		
		$access =  array();
		$modules=array();
		$already_app=array();
		foreach($apps as $key=>$app) {
			if( $already_app[$app['id']]){
				continue;
			}else{
				$already_app[$app['id']]=$app['id'];
			}
			
			$appId	 =$app['id'];
			$appName =$app['name'];
			$access[strtoupper($appName)]   =  array();
			//获取用户所在项目的面板
			$sql2 = "select node.id ,node.name from node LEFT JOIN  access ON node.id=access.node_id where access.role_id in(".$myallroles.") and node.level=2  and node.pid=".$appId." and node.status=1" ;
			$mianban =$db->query($sql2);
			// 读取项目的模块权限
			$already_mianban=array();
			foreach($mianban as $k=>$v){
				if( $already_mianban[$v['id']]){
					continue;
				}else{
					$already_mianban[$v['id']]=1;
				} 
				$sql2 = "select node.id ,node.type,node.name from node LEFT JOIN  access ON node.id=access.node_id where access.role_id in(".$myallroles.") and node.level=3  and node.pid=".$v['id']." and node.status=1" ;
				$m =$db->query($sql2);
				if( $m ){
					$modules= array_merge($modules,$m);
					foreach($m as $k2=>$v2 ){
						if( $v2['type']==2 ){ 
							$sql2 = "select node.id ,node.type,node.name from node LEFT JOIN  access ON node.id=access.node_id where access.role_id in(".$myallroles.") and node.level=3  and node.pid=".$v2['id']." and node.status=1" ;
							$m2 =   $db->query($sql2);
							if( $m2 ) $modules= array_merge($modules,$m2);
						}
					}
				}		
			}
			
			// 判断是否存在公共模块的权限
			$publicAction  = array();
			foreach($modules as $k3=>$v3) {
				$moduleId=$v3['id'];
				$moduleName=$v3['name'];
				if('PUBLIC'== strtoupper($moduleName)) { 
					$sql2 = "select node.id ,node.name,access.plevels from node LEFT JOIN  access ON node.id=access.node_id where access.role_id in(".$myallroles.") and node.level=4 and node.pid=".$moduleId." and node.status=1" ;
					$rs =   $db->query($sql2);
					foreach ($rs as $a){
						$publicAction[$a['name']] = $a['id']."-".$a['plevels'];
					}
					unset($modules[$k3]);
					break;
				}
			}
			
			 // 依次读取模块的操作权限
			 
			$already_action = array();
			foreach($modules as $k4=>$v4) {
				if( $already_action[$v4['id']]){
					continue;
				}else{
					$already_action[$v4['id']]=1;
				}
				
				if($v4['type']==2) continue;
				$moduleId=$v4['id'];
				$moduleName =$v4['name']; 
				$sql2 = "select node.id ,node.name,access.plevels from node LEFT JOIN  access ON node.id=access.node_id where access.role_id in(".$myallroles.") and node.level=4 and node.pid=".$moduleId." and node.status=1" ;
				
				$rs =   $db->query($sql2);
				$action = array();
				foreach ($rs as $a){
					if( isset( $action[$a['name']] )){
						$ex=explode("-",$action[$a['name']]);
						if($ex[1]>$a['plevels']){
							$action[$a['name']] = $a['id']."-".$a['plevels'];
						}
					}else{
						$action[$a['name']] = $a['id']."-".$a['plevels'];
					}
				}
				foreach ($publicAction as $b=>$c){
					if( !isset($action[$b]) ) $action[$b] = $c;
				}
				// 和公共模块的操作权限合并
				//$action += $publicAction;
				$access[strtoupper($appName)][strtoupper($moduleName)]   =  array_change_key_case($action,CASE_UPPER);
			}
		}
		return $access;
	}
	// 通过权限配置文件读取模块所属的记录访问权限
	static public function getFileModuleAccessList($authId,$module) {
		if($_SESSION[C('ADMIN_AUTH_KEY')]) return array();
		$authId = $authId ? $authId:$_SESSION[C('USER_AUTH_KEY')];
		$file =  DConfig_PATH."/AccessList/access_".$authId.".php";
		if( !file_exists($file) ){
			$accessList =RBAC::getAccessList($authId);
			if (!file_exists(DConfig_PATH."/AccessList")){
				createFolder(dirname(DConfig_PATH."/AccessList"));
				mkdir(DConfig_PATH."/AccessList", 0777);
			}
			RBAC::writeover($file,"return ".RBAC::pw_var_export($accessList).";\n",true);
			foreach($accessList as $k3 => $v3){
				foreach($accessList[$k3] as $k1 => $v1 ){
					foreach($accessList[$k3][$k1]  as $k => $v ){
						$p=explode("-",$v);
						$_SESSION[strtolower($k1.'_'.$k)] = $p[1];
					}
				}
			}
		}
		$access =require $file;
		//模型名称转全大写；与accessList相匹配
		$modelname=strtoupper($module);
		//获取当前模型所具有的操作信息
		$accesslist=$access['ADMIN'][$modelname];
		return $accesslist;
	}
	
	// 通过权限配置文件读取模块所属的记录访问权限
	//返回组权限
	static public function getFileGroupAccessList($authId) {
		if($_SESSION[C('ADMIN_AUTH_KEY')]) return array();
		$authId = $authId ? $authId:$_SESSION[C('USER_AUTH_KEY')];
		$file =  DConfig_PATH."/AccessList/access_".$authId.".php";
		if( !file_exists($file) ){
			$accessList =RBAC::getAccessList($authId);
			if (!file_exists(DConfig_PATH."/AccessList")){
				createFolder(dirname(DConfig_PATH."/AccessList"));
				mkdir(DConfig_PATH."/AccessList", 0777);
			}
			RBAC::writeover($file,"return ".RBAC::pw_var_export($accessList).";\n",true);
			foreach($accessList as $k3 => $v3){
				foreach($accessList[$k3] as $k1 => $v1 ){
					foreach($accessList[$k3][$k1]  as $k => $v ){
						$p=explode("-",$v);
						$_SESSION[strtolower($k1.'_'.$k)] = $p[1];
					}
				}
			}
		}
		$access =require $file;
		$groupList=array();
		$num=0;
		foreach($access as $k1 => $v1){
			foreach($v1  as $k2=>$v2){
				if($k2=="INDEX"){
					continue;
				}
				foreach($v2  as $k3=>$v3){
					//等于组ID时压入到组数组
					if($k3=="GROUPID"){
						$groupList[$num]=$v3;
						$num++;
					}
				}
			}
		}
		$groupList=array_unique($groupList);
		return $groupList;
	}
	
	// 读取模块所属的记录访问权限
	static public function getModuleAccessList($authId,$module) {
		$myallroles=RBAC::getAuthorRole($authId);
		// Db方式
		$db     =   Db::getInstance(C('RBAC_DB_DSN'));
		$table = array('role'=>C('RBAC_ROLE_TABLE'),'user'=>C('RBAC_USER_TABLE'),'access'=>C('RBAC_ACCESS_TABLE'));
		$sql    =   "select access.node_id from ".
			    $table['role']." as role,".
			    $table['user']." as user,".
			    $table['access']." as access ".
			    "where  access.role_id in ({$myallroles}) and role.status=1 and  access.module='{$module}' and access.status=1";
		$rs =   $db->query($sql);
		$access	=	array();
		foreach ($rs as $node){
		    $access[]	=$node['node_id'];
		}
		return $access;
	}
	
	public  function writeover($filename,$data,$safe = false,$method='wb'){
		$safe && $data = "<?php \n".$data."\n?>";
		// $handle = fopen($filename,$method);
		// fwrite($handle,$data);
		// fclose($handle);
		file_put_contents($filename,$data);
	}
	
	public  function pw_var_export($input,$t = null)
	{
		$output = '';
		if (is_array($input))
		{
		    $output .= "array(\r\n";
			foreach ($input as $key => $value)
			{
			    $output .= $t."\t".RBAC::pw_var_export($key,$t."\t").' => '.  RBAC::pw_var_export($value,$t."\t");
			    $output .= ",\r\n";
			}
			$output .= $t.')';
		} elseif (is_string($input)) {
			$output .= "'".str_replace(array("\\","'"),array("\\\\","\'"),$input)."'";
		} elseif (is_int($input) || is_double($input)) {
			$output .= "'".(string)$input."'";
		} elseif (is_bool($input)) {
			$output .= $input ? 'true' : 'false';
		} else {
			$output .= 'NULL';
		}

		return $output;
	}	
}