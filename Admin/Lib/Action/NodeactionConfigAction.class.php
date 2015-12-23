<?php
/**
 * @Title: file_name
 * @Package package_name
 * @Description: todo(模块操作方法配置控制器)
 * @author yangxi
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-5-31 上午10:40:33
 * @version V1.0
 */
class NodeactionConfigAction extends CommonAction {

	//存储第一次选中的节点
	private $firstDetail = array();
	/**
	 * (non-PHPdoc)
	 * 展示方法节点模板
	 * @see CommonAction::index()
	*/
	public function index(){
		if ($_REQUEST['jump']) {
			$type=$_REQUEST['type'];
			$this->assign('check',$type);
			
			$list=$this->comboxgetaction($type);
			$this->assign('list', $list);
			$this->display('confpanel');
		} else {
			$this->getRoleTree();
			//获取第一次默认选中的节点方法
			$node = M("node");
			$pid=$this->firstDetail['check'];
			$list=$this->comboxgetaction($pid);
			$map['pid']= $this->firstDetail['check'];
			$map['status'] = 1;
			$map['type'] = 4;
			$map['level'] = 4;
			$list1 = $node->where($map)->getField("name,title");

			$this->assign('list', $list);
			$this->assign('check', $this->firstDetail['check']);
			$this->display();
		}
	}

	/**
	 * @Title: getRoleTree
	 * @Description: todo(菜单树)
	 * @author 杨东
	 * @date 2013-5-28 上午10:41:15
	 * @throws
	 */
	public function getRoleTree(){
		// 组
		$model=M("group");
		$list = $model->where("status=1")->order("sorts asc")->select();
		//获取系统授权
		$model_syspwd = D('SerialNumber');
		$modules_sys = $model_syspwd->checkModule();
		$m_list = explode(",",$modules_sys);
		//需过滤的model
		$model = D('SystemConfigDetail');
		$filter = $model->getDCFilter();
		// 查询菜单节点
		$node = M("Node");
		$map['status'] = 1;
		//$map['showmenu'] = 1;
		$map['name'] = array('not in',$filter);
		$map['type'] = array('lt',4);
		$data = $node->where($map)->order('sort asc')->select();
		// 获取授权节点
		$accessList = getAuthAccess();
		$returnarr = array();
		// 第一个循环构造分组节点
		foreach ($list as $k2 => $v2) {
			$newv1 = array();
			$newv1['id'] = -$v2['id'];
			$newv1['pId'] = 0;
			$newv1['title'] = $v2['name']; //光标提示信息
			$newv1['name'] = missubstr($v2['name'],20,true); //结点名字，太多会被截取
			$newv1['open'] = 'true';
			$returnarr2 = array();
			// 第二个循环构造组分类节点
			foreach($data as $k => $v){
				$newv2 = array();
				// 过来权限
				if(substr($v['name'], 0, 10)!="MisDynamic"){
					if(!in_array($v['name'], $m_list))  continue;
				}
				if (!isset ($access[strtoupper( APP_NAME )][strtoupper ($v ['name'])]) && !$_SESSION [C('ADMIN_AUTH_KEY')]) {
					continue;
				}
				if($v['name']!='Public' && $v['name']!='Index') {
					if ($v['type'] == 1 && $v['group_id'] == $v2['id']) {
						$newv2['id'] = $v['id'];
						$newv2['pId'] = -$v2['id'];
						$newv2['title'] = $v['title']; //光标提示信息
						$newv2['name'] = missubstr($v['title'],20,true); //结点名字，太多会被截取
						$newv2['open'] = 'true';
						$returnarr3 = array();
						// 判断当前节点是否显示1显示0不显示
						if($v["toolshow"]==1 ){
							// 第三个循环判断模块节点
							foreach($data as $k3 => $v3){
								// 过来权限
								if(substr($v3['name'], 0, 10)!="MisDynamic"){
									if(!in_array($v3['name'], $m_list))  continue;
								}
								if (!isset ($access[strtoupper( APP_NAME )][strtoupper ($v3 ['name'])]) && !$_SESSION [C('ADMIN_AUTH_KEY')]) {
									continue;
								}
								$newv3 = array();
								if ($v3['type'] == 3 && $v3['pid'] == $v['id']) {
									if (!$this->firstDetail) {
										$this->firstDetail['name'] = $v3['name'];
										$this->firstDetail['title'] = $v3['title'];
										$this->firstDetail['check'] = $v3['id'];
									}
									$newv3['id'] = $v3['id'];
									$newv3['pId'] = $v['id'];
									$newv3['title'] = $v3['title']; //光标提示信息
									$newv3['name'] = missubstr($v3['title'],20,true); //结点名字，太多会被截取
									$newv3['url']="__URL__/index/jump/1/type/".$v3['id'];
									$newv3['target']='ajax';
									$newv3['rel']="nodeactionconfig";
									$newv3['open'] = 'true';
									$returnarr3[] = $newv3;
								}
							}
							if($returnarr3){
								$returnarr3[] = $newv2;
							}
						} else {
							// 第三个循环判断模块节点
							foreach($data as $k3 => $v3){
								if(substr($v3['name'], 0, 10)!="MisDynamic"){
									if(!in_array($v3['name'], $m_list))  continue;
								}
								if (!isset ($access[strtoupper( APP_NAME )][strtoupper ($v3 ['name'])]) && !$_SESSION [C('ADMIN_AUTH_KEY')]) {
									continue;
								}
								$newv3 = array();
								if ($v3['type'] == 3 && $v3['pid'] == $v['id']) {
									if (!$this->firstDetail) {
										$this->firstDetail['name'] = $v3['name'];
										$this->firstDetail['title'] = $v3['title'];
										$this->firstDetail['check'] = $v3['id'];
									}
									$newv3['id'] = $v3['id'];
									$newv3['pId'] = -$v2['id'];
									$newv3['title'] = $v3['title']; //光标提示信息
									$newv3['name'] = missubstr($v3['title'],20,true); //结点名字，太多会被截取
									$newv3['url']="__URL__/index/jump/1/type/".$v3['id'];
									$newv3['target']='ajax';
									$newv3['rel']="nodeactionconfig";
									$newv3['open'] = 'true';
									$returnarr3[] = $newv3;
								}
							}
						}
						if($returnarr3){
							$returnarr2 = array_merge($returnarr2,$returnarr3);
						}
					}
				}
			}
			if($returnarr2){
				$returnarr[] = $newv1;
				$returnarr = array_merge($returnarr,$returnarr2);
			}
		}
		$this->assign('typeTree',json_encode($returnarr));
	}

	/**
	 * (non-PHPdoc)
	 * 插入方法节点到节点表中
	 * @see CommonAction::insert()
	 */
	public function insert(){
		$model	=M("Node");
		$pid = $_POST["type"];
		$selectedarr=$_POST["selectedarr"];
		$titlearr=$_POST["titlearr"];
		$namearr=$_POST["namearr"];
		$data=array();
		foreach($selectedarr as $k=>$v ){
			if(!$titlearr[$k]){
				$this->error("操作的".$namearr[$k]."名称不能为空");
			}else{
				$dataone=array();
				$dataone['title']=$titlearr[$k];
				$dataone['name']=$namearr[$k];
				$dataone['pid']=$pid;
				$dataone['level']=4;
				$dataone['type']=4;
				$dataone['status']=1;
				$map['pid']=$pid;
				$map['status']=1;
				$map['name']=$namearr[$k];
				$has = $model->where($map)->find();
				if( !$has){
					$data[]=$dataone;
				}
			}
		}
		if($data){
			foreach($data as $key=>$val){
				$re = $model->add($val);
				if($re){
					$this->abc($re,$val);
				}
			}
			$this->success("操作成功,请到注意到相应模块授权");
		}else{
			$this->error("请修改后再提交");
		}
	}
	private function abc( $insertid ,$obj){
		$node_model = M("node");
		$p_title = $node_model->where("id=".$obj["pid"])->getField("title");
		$roleinsertid_arr=array();
		$j=0;
		$roleModule=D("Role");
		for($s=1;$s<=4;$s++){
			if($s==1){
				$t="所有";
			}else if($s==2){
				$t="部门及子部门";
			}else if($s==3){
				$t="部门";
			}else{ $t="个人";
			}
			$data=array();
			$data["name"]=$p_title."_".$t."_".$obj["title"];

			$data["nodetitle"] = $obj["title"];
			$data["nodeidcategory"] = 5;

			$data["plevels"] = $s;
			$data["nodepid"] = $obj["pid"];
			$data["nodeid"] = $insertid;
			$data["createtime"]=time();
			$data["createid"]=1;
			$data["sort"] = $j;
			$data["status"] = 1;
			$j++;
			$list = $roleModule->add($data);
			if($list){
				$data["id"]=$list;
				$roleinsertid_arr[]=$data;
			}else{
				$this->error();
			}
		}
		////添加权限
		$nodeModel = D("Node");
		$accessModel=M("access");
		$role_list = $roleinsertid_arr;
		$parr=array();
		foreach($role_list as $k=>$v){
			if(!in_array($parr,$v["nodepid"])){
				$vo = $nodeModel->find($v["nodepid"]);//获取模块信息
				$vo2 = $nodeModel->find($vo['pid']);//获取面板或者空模块信息
				$pid_type0=$pid_type0=$pid_type0="";
				if($vo2['type']==2){//kong mokuai
					$vo3 = $nodeModel->find($vo2['pid']);//获取面板
					$pid_type3=$vo['id'];//模块id
					$pid_type2=$vo2['id'];//控模块id
					$pid_type1=$vo3['id'];//面板模块id
					$pid_type0=$vo3['pid'];//项目id
				}else{//mianban
					$pid_type2=$vo['id'];//模块id
					$pid_type1=$vo2['id'];//面板模块id
					$pid_type0=$vo2['pid'];//项目id
				}
				if($pid_type0!="") $idlist[$v["id"]][]=$pid_type0;
				if($pid_type1!="") $idlist[$v["id"]][]=$pid_type1;
				if($pid_type2!="") $idlist[$v["id"]][]=$pid_type2;
				if($pid_type3!="") $idlist[$v["id"]][]=$pid_type3;
			}
			$data=array();
			$data["role_id"]	=$v["id"];
			$data["node_id"]	=$v["nodeid"];
			$data["level"]		=4;
			$data["type"]		=4;
			$data["pid"]		=$v["nodepid"];
			$data["plevels"]	=$v["plevels"];
			$result =$accessModel->add($data);
			if($result){
				if( $idlist ){
					$roleModule->setGroupActions($v["id"],$idlist[$v["id"]]);
				}
			}
		}

	}
	/**
	 * @Title: comboxgetmodule
	 * @Description: todo(此方法未使用了)
	 * @param unknown_type $t
	 * @return multitype:multitype:string
	 * @author liminggang
	 * @date 2013-6-4 下午2:49:05
	 * @throws
	 */
	public function comboxgetmodule($t=""){
		$model=M("node");
		if( $t=="" ){
			$pid =$_POST['mianban'];
		}else{
			$pid = $t;
		}
		$arr=array(array("","请选择模块"));
		if ($pid!='') {
			$map["level"]=3;
			$map["status"]=1;
			$map["pid"]=$pid;
			$list = $model->where($map)->select();
			foreach($list as $k=>$v){
				if($v["type"]==2){
					$map2["level"]=3;
					$map2["status"]=1;
					$map2["pid"]=$v['id'];
					$list2 = $model->where($map2)->getField("id,title");
					foreach($list2 as $k2=>$v2){
						array_push($arr,array($k2, $v2));
					}
				}else{
					array_push($arr,array($v['id'], $v['title']));
				}
			}
		}
		if( $t=="" ){
			echo json_encode($arr);
		}else{
			return $arr;
		}
	}
	/**
	 * @Title: comboxgetaction
	 * @Description: todo(根据节点父ID查询出控制器中的未加入节点的方法)
	 * @param unknown_type $t
	 * @return multitype:multitype:unknown
	 * @author liminggang
	 * @date 2013-6-4 下午2:49:23
	 * @throws
	 */
	public function comboxgetaction($t=""){
		$model=M("node");
		if( $t=="" ){
			$id =$_POST['moduleid'];
		}else{
			$id = $t;
		}
		$map=array();
		$map['pid']=$id;
		$map['level']=4;
		$map['status'] =1;
		$list = $model->where($map)->getField("name,title");
		//获取已存在的信息
		$already_action=array_keys($list);
		//标准模式数组
		$sclmodel = D('SystemConfigList');
// 		$normArray = $sclmodel->getNormArray();
		//过滤数组，过滤不需要显示的方法名称
		$notarr = $sclmodel->getCheckOutMethod();
		
		$map=array();
		$id="240";
		$map["id"]=$id;
		$info = $model->where($map)->find();
		print_r($model->getLastSql());
		$class=$info['name']."Action";

	     //开启反射	
		$my_object = new ReflectionClass($class);
		$class_methods = $my_object->getMethods(ReflectionMethod::IS_PUBLIC);
		$class_methods=obj2arr($class_methods);
		foreach($class_methods as $key => $val){
			//
			if($val['class']!=$class){
				unset($class_methods[$key]);
			}
			if( substr($val['name'],0,1) == "_" ||  substr($val['name'],0,6) == "lookup" || substr($val['name'],0,6) == "combox" ) {
				//unset($class_methods[$key]);
			}				
			
		}
		print_r($class_methods);
		exit;
// 		$arr=array();
// 		$m=A($info['name']);
// 		$m2=A("Common");

// 		foreach($class_methods as $k=>$v ){
// 			//过滤数组及某些带前缀的通用方法
// 			if( substr($v,0,7) == "_before" || substr($v,0,6) == "_after" || substr($v,0,6) == "lookup" || substr($v,0,6) == "combox" ||in_array($v,$notarr)) {
// 				continue;
// 			}
// 			//判断控制器中，是否有此方法
// 			if( !method_exists($m,$v) && !method_exists($m2,$v)){
// 				continue;
// 			}
// 			$a=array();
// 			$a[]=$v;
// 			if(in_array($v,$already_action)){
// 				$a[]=$list[$v];
// 			}
// // 			else{
// // 				$a[]=$normArray[$v];
// // 			}
// 			$arr[]=$a;
// 		}
// 		if( $t=="" ){
// 			echo json_encode($arr);
// 		}else{
// 			return $arr;
// 		}
	}
}
?>