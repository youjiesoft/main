<?php
class MisSystemPanelDesingMasModel extends CommonModel{
	protected $trueTableName = 'mis_system_panel_desing_mas';
	public $_auto	=array(
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
// 			array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
// 			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
// 			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
	);
	public $_validate	=	array(
			array('name','','英文命名重复，请检查！',self::MUST_VALIDATE,'unique',self::MODEL_BOTH),//多字段组合验证
	);
	
	/**
	 * @Title: userAndRolegroupToTree
	 * @Description: todo(包含人员和角色的左侧树数组)
	 * @param unknown_type $rel
	 * @author 谢友志
	 * @date 2015-6-8 下午1:36:57
	 * @throws
	 */
	public function userAndRolegroupToTree($rel,$url=""){
		
		$url = __URL__."/role";
		
		$usermodel = M("user");
		$rolegroupmodel = M("rolegroup");
		$companyModel = M("mis_system_company");
		$companylist = $companyModel->where("status=1")->getField("id,name");
		$userlist = $usermodel->field("id,name,companyid")->where("status=1")->select();
		$rolegrouplist = $rolegroupmodel->field("id,name,companyid")->where("status=1 and catgory=1")->select();
		//echo $rolegroupmodel->getlastsql();
		$newv = array();
		$new = array();
		foreach($userlist as $k=>$v){
			$newv[$k]['id']=$v['id'];
			$newv[$k]['pId']=-1;
			$newv[$k]['type']='post';
			$newv[$k]['url']=$url."/jump/jump/type/1/id/".$v['id'];
			$newv[$k]['target']='ajax';
			$newv[$k]['rel']=$rel;
			$newv[$k]['title']=$v['name']; //光标提示信息
			$newv[$k]['name']=$v['name']; //结点名字，太多会被截取,针对于现在的ZTREE，宽度不能超过18个字符。
			$newv[$k]['open']=false;
			$newv[$k]['isParent'] = false;
		}
		foreach($rolegrouplist as $k=>$v){
			$new[$k]['id']="R_".$v['id'];
			$new[$k]['pId']=-2;
			$new[$k]['type']='post';
			$new[$k]['url']=$url."/jump/jump/type/2/id/".$v['id'];
			$new[$k]['target']='ajax';
			$new[$k]['rel']=$rel;
			$new[$k]['title']=$v['name']; //光标提示信息
			$new[$k]['name']=$v['name']; //结点名字，太多会被截取,针对于现在的ZTREE，宽度不能超过18个字符。
			$new[$k]['open']=false;
			$new[$k]['isParent'] = false;
		}
		$ztree[]=array(
				'id'=>-1,
				'pId'=>-3,
				'title'=>'人员', //光标提示信息
				'name'=>'人员',
				'open'=>false,
				'isParent' => true,
		);
		$ztree[]=array(
				'id'=>-2,
				'pId'=>-3,
				'title'=>'角色', //光标提示信息
				'name'=>'角色',
				'open'=>false,
				'isParent' => true,
		);
		$ztree[]=array(
				'id'=>-3,
				'pId'=>"0",
				'title'=>'对象', //光标提示信息
				'name'=>'对象',
				'open'=>true,
				'isParent' => true,
		);
		 
		$typeTree = array_merge($ztree,$newv,$new);
		return $typeTree;
	}
	/**
	 * @Title: getForbitRoleOfPanel
	 * @Description: todo(面板禁权接口，) 
	 * @param unknown_type $userid 用户id
	 * @param unknown_type $panelid 面板id 可以为三种方式：1、单个id 2、多个id以逗号隔开的字符串 3.多个id构成的一维数组
	 * @param $returnarr 强制返回数组形式
	 * @return boolean  
	 * @author 谢友志 
	 * @date 2015-11-4 下午1:56:13 
	 * @throws
	 */
	public function getForbitRoleOfPanel2($userid,$panelid,$returnarr=false){
		$paid = '';
		if(is_string($panelid)){
			if(false===strpos($panelid,",")){
				$paid = $panelid;
			}else{
				$paid = explode(",",$panelid);
			}
		}else if(is_array($panelid)){
			$paid = $panelid;
		}else if(is_int($panelid)){
			$paid = $panelid;
		}
		//用户所在组
		$usergroup = array();
		$usergroupmodel = M("rolegroup_user");
		$umap['user_id'] = $userid;
		$role = $usergroupmodel->where($umap)->select();
		foreach($role as $k=>$v){
			$usergroup[] = $v['rolegroup_id'];
		}
		$usergroup = array_unique($usergroup);
		
		//面板禁权模型
		$model = M("mis_system_panel_desing_role");
		//---------------单面板禁权------------------
		if(is_string($paid)||is_int($paid)){
			//个人禁权
			$map['objid'] = $userid; //对象id
			$map['objtype'] = 1;	//对象类型 用户
			$map['_string'] = "FIND_IN_SET(".$paid.",forbidroleids)";
			$list = $model->where($map)->select();
			if($list){
				if($returnarr){
					return array($paid=>true);
				}else{
					return true;
				}
				
			}else{
				//角色禁权
				$map['objid'] = array("in",$usergroup);	//对象id
				$map['objtype'] = 2;					//对象类型 角色	
				$map['_string'] = "FIND_IN_SET(".$panelid.",forbidroleids)";
				$group = $model->where($map)->select();
				if(count($role)==count($group)){
					if($returnarr){
						return array(true);
					}else{
						return true;
					}
				}
			}
			if($returnarr){
					return array($paid=>false);
				}else{
					return false;
				}
		}
		//---------------多面板禁权------------------
		
		if(is_array($paid)){
			//个人禁权
			$list = array();//返回结果
			$temp = array();//个人没禁权的面板id
			$map['objid'] = $userid; //对象id
			$map['objtype'] = 1;	//对象类型 用户
			$userrolelist = $model->where($map)->select();
			foreach($paid as $k=>$v){
				foreach ($userrolelist as $uk=>$uv){
					$fb = explode(",",$uv['forbidroleids']);				
					if(in_array($v,$fb)){
						$list[$v]=true;
					}
				}
				if(!$list[$v]){
					$list[$v]=false;
					$temp[]=$v;
				}
			}
			//角色禁权
			if($temp){
				foreach($temp as $key=>$val){
					$map['objid'] = array("in",$usergroup);
					$map['objtype'] = 2;
					$map['_string'] = "FIND_IN_SET(".$val.",forbidroleids)";
					$groups = $model->where($map)->select();
					
					if(count($role)==count($groups)){
						$list[$val] = true;
					}else{
						$list[$val] = false;
					}
				}
			}
			return $list;
		}
	}
	public function panelRoleConfig(){
		$model = D("mis_system_panel_desing_role");
		//个人禁权
		$userrolelist = array();
		$map['status'] = 1;
		$map['objtype'] = 1;
		$userrolelist = $model->where($map)->getField("objid,forbidroleids");
		foreach($userrolelist as $usk=>$usv){
			$userrolelist[$usk] = explode(",",$usv);
		}
		//-------------------------------
		//组禁权
		$map2['mis_system_panel_desing_role.status'] = 1;
		$map2['mis_system_panel_desing_role.objtype'] = 2;
		$list2 = $model->where($map2)->getField("objid,forbidroleids");
		$panalarr = array(); //组-面板集合 array("组id"=>array("面板id集"))
		foreach($list2 as $lk=>$lv){
			if($panalarr[$lk]){
				$temp = explode(",",$lv);
				$panalarr[$lk] = array_unique(array_merge($panalarr[$lk],$temp));
			}else{
				$panalarr[$lk] = explode(",",$lv);
			}
		}
		//用户的组集合
		$group = array(); //用户-组集合 array("用户id"=>array("组集合"))
		$usergroup = D("rolegroup_user")->where("user_id!='' and user_id is not null")->order("user_id")->select();
		foreach($usergroup as $key=>$val){
			//if($val['user_id']=='') $val['user_id']=1;
			$group[$val['user_id']][] = $val['rolegroup_id'];
		}
		
		//用户
		$userlist = array(); //用户集 array("用户id")
		$userarr = D("user")->where("status=1")->select();
		foreach($userarr as $urk=>$urv){
			$userlist[] = $urv['id'];
		}
		/**
		 * 0.定义一个空数组 用于收集每个用户被禁止面板id
		 * 1.遍历用户集合
		 * 2.遍历每个用户的组集合
		 * 3.判断用户所有的组是否都有禁用记录
		 * 3-1.如果不是、直接终止遍历该用户禁止记录的判断 不生成对应的数组元素
		 * 3-2.如果每个组都生成了禁止记录
		 * 3-2-1 如果禁止记录有多条，取出都包含有的面板id
		 * 3-2-2 将禁止的面板id放入预定义数组里
		 */	
		$arr = array();
		foreach($userlist as $key=>$val){		//遍历用户集合
			$have = true; // 判断当前组是否在禁用数据中
			$content = array();//临时存储禁用数据
			foreach($group[$val] as $k=>$v){	//遍历每个用户的组集合
				if($panalarr[$v] && $have){
					$content[$v] = $panalarr[$v];
				}else{
					$have = false;
					break;
				}
			}
			$temp = array();
			if(!$have){							// 如果不是、直接终止遍历该用户禁止记录的判断 不生成对应的数组元素
				$content=array();
			}else{
				if(count($content)){
					$temp =reset($content);
					if(count($content) > 1){// 如果禁止记录有多条，取出都包含有的面板id
						array_shift($content);
						foreach($content as $ck=>$cv){
							foreach($temp as $k=>$v){
								if(!in_array($v,$cv)){
									unset($temp[$k]);
								}
							}
						}
					}
					$arr[$val] = $temp;
				}
			}
		}
		if($arr){
			foreach($arr as $ak=>$av){
				foreach($userrolelist as $uk=>$uv){
					if($ak == $uk){
						$arr[$ak] = array_unique(array_merge($av,$uv));
					}else{
						$arr[$uk] = $uv;
					}
				}
			}
		}elseif($userrolelist){
			$arr = $userrolelist;
			
		}
		
		//写入缓存文件
		//定义个人文件缓存地址
		$p= DConfig_PATH."/Panelconf";
		if(!is_dir($p)){
			$this->make_dir($p,0777);
		}
		$filename = $p."/panelconf.php";
		$this->writeover($filename,"return ".$this->pw_var_export($arr).";\n",true);
	}
	public function getForbitRoleOfPanel($userid,$panelid){
		$file = DConfig_PATH."/Panelconf/panelconf.php";
		if(is_file($file)){
			$list = require $file;
			if($list[$userid]&&in_array($panelid,$list[$userid])){
				return true;
			}
		}
		return false;
	}
}












