<?php
class Browse {
	// 用于检测用户的浏览权限
	static function saveBrowseList($authId = null) {
		if (null === $authId)
			$authId = $_SESSION [C ( 'USER_AUTH_KEY' )];
			// 如果使用普通权限模式，保存当前用户的访问权限列表
			// 管理员不生成浏览权限
		if (C ( 'USER_AUTH_TYPE' ) != 2 && ! $_SESSION [C ( 'ADMIN_AUTH_KEY' )]) {
			// 把权限写入文件通过文件判断
			//$file = DConfig_PATH . "/BrowsecList/borwse_" . $_SESSION [C ( 'USER_AUTH_KEY' )] . ".php"; ---xyz
			$file = DConfig_PATH . "/BrowsecList/borwse_" .$authId. ".php"; //---xyz
			if (! file_exists ( $file )) {
				$browselist = Browse::getBrowseList ( $authId );
				if (! file_exists ( DConfig_PATH . "/BrowsecList" )) {
					createFolder ( dirname ( DConfig_PATH . "/BrowsecList" ) );
					mkdir ( DConfig_PATH . "/BrowsecList", 0777 );
				}
				Browse::writeover ( $file, "return " . Browse::pw_var_export ( $browselist ) . ";\n", true );
			}
		}  
		return;
	}
	/**
	 * +----------------------------------------------------------
	 * 取得当前认证号的所有浏览map
	 * +----------------------------------------------------------
	 *
	 * @param integer $authId
	 *        	用户ID
	 *        	+----------------------------------------------------------
	 * @access public
	 *         +----------------------------------------------------------
	 */
	static public function getBrowseList($authId) {
		// 获取当前用户所有组
		$myallroles = Browse::getAuthorGroup ( $authId );
		$rolesstr=implode(',',array_filter($myallroles));
		$MisSystemDataAccessViewModel=D("MisSystemDataAccessView");
		$MisSystemDataAccessModelQuoteModel=M("mis_system_data_access_model_quote");
		// 查询当前用户的浏览权限
		if ($authId) {
			$db = Db::getInstance ( C ( 'RBAC_DB_DSN' ) );
			$userborwseList = $MisSystemDataAccessViewModel->where("mis_system_data_access_mas.status = 1 and startstatus=1 and mis_system_data_access_sub.accesscontent !='' and mis_system_data_access_sub.accesscontent is not null")->order("actionname,tablename,fieldname")->select();
			//根据用户组装一个数组
			$userAllborwseList = $MisSystemDataAccessViewModel->where("mis_system_data_access_mas.status = 1 and startstatus=1 and mis_system_data_access_sub.accesscontent !='' and mis_system_data_access_sub.accesscontent is not null  AND ( (  mis_system_data_access_sub.objtype = 1) AND  (mis_system_data_access_sub.objid = ".$authId.")) OR ((mis_system_data_access_sub.objtype=2) AND (mis_system_data_access_sub.`objid` IN (".$rolesstr."))) ")->order("actionname,tablename,fieldname")->select();
			$userNewAlllist=array();
			foreach ($userAllborwseList as $userkey=>$userval){
				$userNewAlllist[$userval['actionname']][$userval['fieldname']][]=$userval;
			}
			//print_r($userborwseList);
			$MisSystemDataAccessModelQuoteList=$MisSystemDataAccessModelQuoteModel->where("status=1")->select();
			 foreach ($MisSystemDataAccessModelQuoteList as $mqkey=>$mqval){
				$MisSystemDataAccessModelQuoteNewList[$mqval['actionname']][]=$mqval;
			}
			//查询继承数据权限表单
			$list = array ();
			if ($userborwseList) {
				$formArr=array();
				$dformArr=array();
				foreach ( $userborwseList as $ukey => $uval ) {
					$tablename=D($uval['actionname'])->getTablename();
					if($uval['fieldname']){
						if($uval['accesscontenttype']==2){//分组授权
							$groupval="";
							//分组所得数据权限
							foreach ($userNewAlllist[$uval['actionname']][$uval['fieldname']]  as $nekey=>$neval){
								$groupval[]=Browse::getGroupVal($neval['accesscontent']);
							}
							$str=implode ( ',',array_values($groupval) );
						}else {
							$newval="";
							//分组所得数据权限
							foreach ($userNewAlllist[$uval['actionname']][$uval['fieldname']]  as $nekey=>$neval){
								$newval.=$neval['accesscontent'];
							}
							$str=$newval;
						}
							if($uval['typeid']==1){
								if($uval['isalldata']==1||$str){
								//主表字段
								if($formArr[$uval['actionname']][$uval['fieldname']]){
									$newstr=$formArr[$uval['actionname']][$uval['fieldname']].','.$str;
									$formArr[$uval['actionname']][$uval['fieldname']]=$newstr;
									//$newmap=$list[$uval ['actionname']]['formsql'] . " AND {$uval['fieldname']}  in (" . $newstr .")";
								}else{
									$formArr[$uval['actionname']][$uval['fieldname']]=$str;
								}
								
								if($list[$uval ['actionname']]['formsql']){
									$newmap=$list[$uval ['actionname']]['formsql'] . " AND {$tablename}.{$uval['fieldname']}  in (" . $formArr[$uval['actionname']][$uval['fieldname']] .")";
								}else{
									$newmap="{$tablename}.{$uval['fieldname']}  in (" . $formArr[$uval['actionname']][$uval['fieldname']] .")";
								}
							if($uval['fieldname']!=$userborwseList[$ukey+1]['fieldname']){
								$list [$uval ['actionname']]['formsql']=$newmap;
							}
							if($uval['actionname']!=$userborwseList[$ukey+1]['actionname']){
								$list [$uval ['actionname']]['formsql']=$newmap;
							}
							if($uval['accesscontentcategory']==1){ //selectlist.inc
								if($selectlist[$uval ['actionname']][$uval['accesscontentsave']]){
									$selectlist[$uval ['actionname']][$uval['accesscontentsave']].=','.$str;
								}else{ 
									$selectlist[$uval ['actionname']][$uval['accesscontentsave']]=$str;
								}
								$list [$uval ['actionname']]['selectlist']=$selectlist[$uval ['actionname']];
							}else{
								$list [$uval ['actionname']][$uval['accesscontentsource']] = array (
											$uval['accesscontentsave'] => $formArr[$uval['actionname']][$uval['fieldname']],
								);
							}
								}
						}else{//内嵌表
							$newmap="";
							if($dformArr[$uval['actionname']][$uval['tablename']][$uval['fieldname']]){
								//echo $dformArr[$uval['actionname']][$uval['tablename']][$uval['fieldname']];exit;
								$dnewstr=$dformArr[$uval['actionname']][$uval['tablename']][$uval['fieldname']].','.$str;
								$dformArr[$uval['actionname']][$uval['tablename']][$uval['fieldname']]=$dnewstr;
								//$newmap=$list[$uval ['actionname']][$uval['tablename']]['formsql'] . " AND {$uval['fieldname']}  in (" . $str .")";
							}else{
								$dformArr[$uval['actionname']][$uval['tablename']][$uval['fieldname']]=$str;
								//$newmap=$uval['fieldname']."  in(".$str.")";
							}
							if($list[$uval['actionname']][$uval['tablename']]['formsql']){
								$newmap=$list[$uval['actionname']][$uval['tablename']]['formsql'] . " AND {$tablename}.{$uval['fieldname']}  in (" . $dformArr[$uval['actionname']][$uval['tablename']][$uval['fieldname']] .")";
							}else{
								$newmap="{$tablename}.{$uval['fieldname']}  in (" . $dformArr[$uval['actionname']][$uval['tablename']][$uval['fieldname']] .")";
							}
							if($uval['fieldname']!=$userborwseList[$ukey+1]['fieldname']){
								$list [$uval ['actionname']][$uval['tablename']]['formsql']=$newmap;
							}
							if($uval['actionname']!=$userborwseList[$ukey+1]['actionname']){
								$list [$uval ['actionname']][$uval['tablename']]['formsql']=$newmap;
							}
							if($uval['accesscontentcategory']==1){ //selectlist.inc
								if($selectlist[$uval['actionname']][$uval['tablename']][$uval['accesscontentsave']]){
									$selectlist[$uval['actionname']][$uval['tablename']][$uval['accesscontentsave']].=','.$str;
								}else{
									$selectlist[$uval['actionname']][$uval['tablename']][$uval['accesscontentsave']]=$str;
								}
								$list[$uval['actionname']][$uval['tablename']]['selectlist']=$selectlist[$uval ['actionname']][$uval['tablename']];
							}else{
								$list[$uval['actionname']][$uval['tablename']][$uval['accesscontentsource']] = array (
										$uval['accesscontentsave'] => $dformArr[$uval['actionname']][$uval['tablename']][$uval['fieldname']],
								);
							}
							//$list [$uval ['actionname']][$uval['tablename']]['formsql']=$newmap;//列表用的map 条件
							if($uval['accesscontentcategory']==1){ //selectlist.inc
								if($selectlist[$uval['actionname']][$uval['tablename']][$uval['accesscontentsave']]){
									$selectlist[$uval['actionname']][$uval['tablename']][$uval['accesscontentsave']].=','.$str;
								}else{
									$selectlist[$uval['actionname']][$uval['tablename']][$uval['accesscontentsave']]=$str;
								}
								$list[$uval['actionname']][$uval['tablename']]['selectlist']=$selectlist[$uval['actionname']][$uval['tablename']];
							}else{
								$list[$uval['actionname']][$uval['tablename']][$uval['accesscontentsource']][$uval['accesscontentsave']] = $dformArr[$uval['actionname']][$uval['tablename']][$uval['fieldname']];
							}
						}
						if($MisSystemDataAccessModelQuoteNewList[$uval['actionname']]&&$uval['actionname']!=$userborwseList[$ukey+1]['actionname']){
							//有继承权限模块
							foreach ($MisSystemDataAccessModelQuoteNewList[$uval['actionname']] as $mqnkey => $mqnval) {
								//获取客户表
								if($list[$mqnval['quoteaction']]['extend'][$tablename]){
									$list[$mqnval['quoteaction']]['extend'][$tablename].=" and ".$list[$uval['actionname']]['formsql'];
								}else{
									$list[$mqnval['quoteaction']]['extend'][$tablename]=$list[$uval['actionname']]['formsql'];
								}
								if($list[$mqnval['quoteaction']]['extend']['formsql']){
									$list[$mqnval['quoteaction']]['extend']['formsql'].=" and ".$mqnval['quotefield']." in (select ".$mqnval['savefield']." from  ".$tablename."  where ".$list[$uval['actionname']]['formsql']." ) ";
								}else{
									$list[$mqnval['quoteaction']]['extend']['formsql']=$mqnval['quotefield']." in (select ".$mqnval['savefield']." from  ".D($uval['actionname'])->getTablename()."  where ".$list[$uval['actionname']]['formsql']." ) ";
								}
							}
						}
					}
				}
			}
			// 返回数组
			return $list;
		}
	}
	/**
	 * 获取该用户的浏览级权限
	 * 
	 * @param unknown $aname  当前action名称
	 * @param unknown $type   $type=1 读取物理文件格式	
	 * @param unknown $tablename   内嵌表格读取数据权限 传入内嵌表格表名	
	 */
	public function getUserMap($aname='',$type,$tablename) {
			$authId = $_SESSION [C ( 'USER_AUTH_KEY' )];
			$map="";
		if ($authId && $_SESSION[C ( 'ADMIN_AUTH_KEY' )]!=1) {
			$file = DConfig_PATH . "/BrowsecList/borwse_" . $authId . ".php";
			if (! file_exists ( $file )) {
				Browse::saveBrowseList ( $authId );
			}
			// 查询当前用户当前模块的浏览权限，如果没获取到，要1、重新生成 2、如果未生成抛出异常 3、对生成文件再做检查权限工作
			$borwseList = require $file;
			// 返回當前aciton的瀏覽權限
			if($tablename){
				if($borwseList [$aname][$tablename]['formsql']){
					$map =$borwseList[$aname][$tablename]['formsql'] ;
				}
			}else{
				if($borwseList [$aname]['formsql']||$borwseList [$aname]['extend']['formsql']){
					$map =$borwseList [$aname]['formsql'] ;
					if($borwseList [$aname]['extend']['formsql']){
						if($map){
							$map.=" and ".$borwseList [$aname]['extend']['formsql'];
						} else{
							$map.=$borwseList [$aname]['extend']['formsql'];
						}
					}
				}
			}
		}		
		if($type==1){
			//物理文件样式
			return $borwseList;
		}else{
			//表单sql
			//echo $tablename;
			$map2 = Browse::changerole($aname,'',$tablename);
			if($map2){
				$map = array($map,$map2);
				
				//$map = $map." OR (".$map2.")";
			}
			//print_r($map);
			return $map;
		}
	}
	static public function changerole($aname='',$type,$tablename){
		$authId = $_SESSION [C ( 'USER_AUTH_KEY' )];
		$changeroleModel = M("mis_system_client_change_role");
		$chmap['modelname'] = $aname;
		$chmap['userid'] = $authId;
		$chmap['status'] = 1;
		$changerolelist = $changeroleModel->where($chmap)->select();
		$map2="";
		$contents = '';
		$contentarr = array();
		foreach($changerolelist as $k=>$v){
			if($changerolelist ){
				$temp = explode(',',$v['content']);		
				$contentarr = array_merge($contentarr,$temp);		
			}
			
		}
		array_unique($contentarr);
		foreach($contentarr as $key=>$val){
			$contents.=$contents?",'".$val."'":"'".$val."'";
		}
		$tablename = D($aname)->getTableName();
		if($contents){
			$map2 = $tablename.".".'id'." IN(".$contents.")";
		}		
		return $map2;
	}
	// getAuthorRole($authId,$t=0)
	public function writeover($filename, $data, $safe = false, $method = 'wb') {
		$safe && $data = "<?php \n" . $data . "\n?>";
		file_put_contents ( $filename, $data );
	}
	public function pw_var_export($input, $t = null) {
		import("@.ORG.RBAC");
		$output = '';
		if (is_array ( $input )) {
			$output .= "array(\r\n";
			foreach ( $input as $key => $value ) {
				$output .= $t . "\t" . RBAC::pw_var_export ( $key, $t . "\t" ) . ' => ' . RBAC::pw_var_export ( $value, $t . "\t" );
				$output .= ",\r\n";
			}
			$output .= $t . ')';
		} elseif (is_string ( $input )) {
			$output .= "'" . str_replace ( array (
					"\\",
					"'" 
			), array (
					"\\\\",
					"\'" 
			), $input ) . "'";
		} elseif (is_int ( $input ) || is_double ( $input )) {
			$output .= "'" . ( string ) $input . "'";
		} elseif (is_bool ( $input )) {
			$output .= $input ? 'true' : 'false';
		} else {
			$output .= 'NULL';
		}
		
		return $output;
	}
	public function getGroupVal($accesscontent){
		$MisSystemDataAccessGroupModel=M("mis_system_data_access_group");
		$MisSystemDataAccessGroupList=$MisSystemDataAccessGroupModel->where("startstatus=1 and id in (".$accesscontent.") and member!='' and member is not null")->getField("id,member");
		$str=implode ( ',',array_values($MisSystemDataAccessGroupList) );
		return $str;
	}
	public function getAuthorGroup($authId) {
		$myallroles = "";
		if ($authId) {
			import("@.ORG.RBAC");
			$db = Db::getInstance ( C ( 'RBAC_DB_DSN' ) );
			$table = array (
					'role_rolegroup' => "role_rolegroup",
					'rolegroup_user' => "rolegroup_user" 
			);
			$sql_rolegroup_user = "select role_rolegroup  from " . $table ['role_rolegroup'] . " where status=1";
			$rolesgrouplist = $db->query ( $sql_rolegroup_user );
			$sql_rolegroup = "select rolegroup_user.rolegroup_id from " . $table ['rolegroup_user'] . " as rolegroup_user " . "where rolegroup_user.user_id='{$authId}'";
			$myrolesgroup = $db->query ( $sql_rolegroup );
			//echo $db->getlastsql();
			//print_r($myrolesgroup);
			foreach ( $myrolesgroup as $key => $val ) {
				if ($val ['rolegroup_id']) {
					$temp = RBAC::findAllrole ( $sql_rolegroup_user, $val ['rolegroup_id'] );
					if($temp){
						$myallroles .= $temp;
					}
				}
			}
			// 所有組id
			return explode ( ',', $myallroles );
		}
	}
}