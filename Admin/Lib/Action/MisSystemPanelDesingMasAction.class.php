<?php
class MisSystemPanelDesingMasAction extends CommonAction{
	public function _filter(&$map){
		if($_REQUEST['type']){
			$map['type'] = $_REQUEST['type']-1;
		}		
	}
	public function _before_index(){
		
		//print_r($_REQUEST);
	  	//左边树
	  	if(!$_REQUEST['id']){
	  		$name = $this->getActionName();
	  		
	  		$model=D($name);
	  		
	  		$map['status'] = 1;
	  		$maslist = $model->where($map)->select();
	  		foreach($maslist as $key=>$val){
	  			$maslist[$key]['name'] = $val['title'];
	  			$maslist[$key]['parentid'] = -($val['type']+1);
	  		}
	  		//组装左边树
	  		$param['rel']="MisSystemPanelDesingMasBox";
	  		$param['url']="__URL__/index/jump/jump/id/#id#";
	  		$treemiso[]=array(
	  				'id'=>0,
	  				'pId'=>0,
	  				'name'=>'面板设计',
	  				'title'=>'面板设计',
	  				'open'=>true,
	  				'isParent'=>true,
	  				'rel'=>"MisSystemPanelDesingMasBox",
	  				'url'=>"__URL__/index/jump/jump",
	  				'target'=>'ajax'
	  		);
	  		$treemiso[]=array(
	  				'id'=>-1,
	  				'pId'=>0,
	  				'name'=>'报表',
	  				'title'=>'报表',
	  				'open'=>false,
	  				'isParent'=>false,
	  				'rel'=>"MisSystemPanelDesingMasBox",
	  				'url'=>"__URL__/index/jump/jump/type/1",
	  				'target'=>'ajax'
	  		);
	  		$treemiso[]=array(
	  				'id'=>-2,
	  				'pId'=>0,
	  				'name'=>'面板',
	  				'title'=>'面板',
	  				'open'=>false,
	  				'isParent'=>false,
	  				'rel'=>"MisSystemPanelDesingMasBox",
	  				'url'=>"__URL__/index/jump/jump/type/2",
	  				'target'=>'ajax'
	  		);
	  		$treemiso[]=array(
	  				'id'=>-3,
	  				'pId'=>0,
	  				'name'=>'新闻',
	  				'title'=>'新闻',
	  				'open'=>false,
	  				'isParent'=>false,
	  				'rel'=>"MisSystemPanelDesingMasBox",
	  				'url'=>"__URL__/index/jump/jump/type/3",
	  				'target'=>'ajax'
	  		);
	  		$treearr = $this->getTree($maslist,$param,$treemiso,false);
	  		$this->assign("treearr",$treearr);
	  	}else if($_REQUEST['id']){
	  		//获取数据
	  		$map['masid'] = $_REQUEST['id'];
	  		$this->assign("masid",$_REQUEST['id']);
	  		$submodel = M("mis_system_panel_desing_sub");
	  		$sublist = $submodel->where($map)->order("sort asc")->select();
	  		
	  		$this->assign('list',$sublist);
	  		//获取配置文件
	  		$detailList = require ROOT . '/Dynamicconf/Models/MisSystemPanelDesingMas/sublist.inc.php';
	  		if ($detailList) {
	  			$this->assign ( 'detailList', $detailList );
	  		}
	  		//获取toolbar配置文件
	  		$toolbarextension = require  ROOT . '/Dynamicconf/Models/MisSystemPanelDesingMas/subtoolbar.extension.inc.php';
	  		//扩展工具栏操作
	  		foreach ($toolbarextension as $k => $v) {
	  			$extendurl = $v["extendurl"];	  			
  				if($extendurl){
  					eval("\$extendurl =".$extendurl.";");
  					$toolbarextension[$k]['html'] = str_replace( "#extendurl#",$extendurl,$v['html'] );
  				}
	  		}
	  		if ($toolbarextension) {
	  			$this->assign ( 'toolbarextension', $toolbarextension );
	  		}
	  		$this->display("subindexview");
	  		exit;
	  	}
	}
	public function _before_add(){
		if($_REQUEST['id']){
			$type = getFieldBy($_REQUEST['id'],'id','type','mis_system_panel_desing_mas');
			$this->assign("id",$_REQUEST['id']);
			if($type==1){//面板				
				//查询连字符
				$html=getSelectByHtml('roleinexp','select');
				$html= str_replace('"', "'", $html);
				$this->assign("html",$html);
				$this->lookgroupandnode($type);
				$this->display("addsub");
			}elseif($type==0){//iframe
				//查询连字符
				$html=getSelectByHtml('roleinexp','select');
				$html= str_replace('"', "'", $html);
				$this->assign("html",$html);
				$this->lookgroupandnode($type);
				$this->display("addreport");
			}elseif($type==2){//新闻
				$this->display("addnews");
			}			
			exit;
		}
		$name = $this->getTypeActionName();
		$this->assign('name',$name);
	}
	public function _before_edit(){
		$id = $_REQUEST['id'];
		$this->assign("id",$id);
		$pid = $_REQUEST['pid'];
		if($pid){
			$vo = M('mis_system_panel_desing_sub')->where("id={$id}")->find();
			$this->assign('modelname',$vo['modelname']);	
			
			//处理条件字段
			//面板条件
			$vo2 = $vo;
			$vo['rulesinfo'] = array();
			$vo['showrules']['panel']=$vo2['showmap'];
			$vo['rules']['panel']=$vo2['map'];
			$vo['rulesinfo']['panel'] = $vo2['rulesinfo'];
			//更多按钮条件
			$vo['showrules']['more']=$vo2['moreshowrules'];
			$vo['rules']['more']=$vo2['morerules'];
			$vo['rulesinfo']['more'] = $vo2['morerulesinfo'];
			$vo['modelid'] = getFieldBy($vo['modelname'],'name','id','node');
			$vo['allfields'] = unserialize($vo['allfields']);
			$type = getFieldBy($pid,'id','type','mis_system_panel_desing_mas');
			if($type==1){//面板
				$temptitle = explode(',',$vo['showtitle']);
				foreach($temptitle as $tk=>$tv){
					$temp = explode('|',$tv);
					$vo['checkfield'][$temp[0]] = $temp[0];
					$vo['showwidth'][$temp[0]] = $temp[1];
					//$vo['showsort'][$temp[0]] = $temp[2];
					unset($temp);
				}
				$vo['showtitle'] = explode(',',$vo['showtitle']);
				$scdmodel = D('SystemConfigDetail');
				$detailList = $scdmodel->getDetail($vo['modelname'],false);
				$this->assign ( 'detailList', $detailList );
				if ($detailList) {
					$temp = array();
					foreach($vo['checkfield'] as $ck=>$cv){
						foreach($detailList as $k=>$v){
							if($cv == $v['name']){
								$temp[$k] = $v;
							}
						}
					}
// 					$detailList = array_merge($temp,$detailList);
// 					print_r($detailList);
					unset($temp);
// 					$this->assign ( 'detailList', $detailList );
				}
				
				//数据排序
				$showsort = array();
				if($vo['showsort']){
					$showsortarr = explode(',',$vo['showsort']);
					foreach($showsortarr as $v){
						$temp = explode(' ',$v);
						$showsort[$temp[0]] = $temp[1];							
					}
				}
				$this->assign("showsort",json_encode($showsort));
				//查询连字符
				$html=getSelectByHtml('roleinexp','select');
				$html= str_replace('"', "'", $html);
				$this->assign("html",$html);
				$this->lookgroupandnode($type);
				$this->assign("jsonvo",json_encode($vo));
				$this->assign("vo",$vo);
				$this->display("editsub");
			}elseif($type==0){//iframe
				//查询连字符
				$html=getSelectByHtml('roleinexp','select');
				$html= str_replace('"', "'", $html);
				$this->assign("html",$html);
				$this->lookgroupandnode($type);
				$this->assign("vo",$vo);
				$this->display("editreport");
			}elseif($type==2){//新闻
				$this->assign("vo",$vo);
				$this->display("editnews");
			}
			exit;
		}
	}
	public function _before_insert(){
		
	}
	public function _after_insert($list){
		
		$masmodel=M("mis_system_panel_desing_mas");
		$rs = $masmodel->where("id={$list} and status=1")->find();
		$name = ucfirst($rs['name']);
		if($_POST['isshow']==1){
			$designmodel = M("mis_system_desing_datasource");
			$_POST['actionname'] = 'MisSystemPanel'.$name;
			$_POST['functionname'] = 'showPanel';
			$_POST['title'] = $rs['title'];
			$_POST['createid'] = $_SESSION[C('USER_AUTH_KEY')];
			$_POST['createtime'] = time();
			if(false===$data = $designmodel->create()){
				$this->error("表字段有差异");
			}
			$designlist = $designmodel->add($data);
			if(false === $designlist){
				$this->error("添加失败");
			}
		}
		$this->createTypeActionFile($name,$list,$_POST['type']);
		$this->success('添加成功');
		
	}
	public function _after_update(){
		//验证是否已经存在action物理文件
		$name = getFieldBy($_POST['id'],'id','name','mis_system_panel_desing_mas');
		$name = ucfirst($name);
		$type=getFieldBy($_POST['id'],'id','type','mis_system_panel_desing_mas');
		//修改主表
		$model=D($this->getActionName());
		if(false === $model->create()){
			$this->error("创建数据对象失败");
		}
		$ret = $model->save();
		$designmodel = M("mis_system_desing_datasource");
		$dasdata['id'] = getFieldBy('MisSystemPanel'.$name,'actionname','id','mis_system_desing_datasource');
		if($_POST['isshow']==1){
			//修改mis_system_desing_datasource表
			$desdata['title'] = $_POST['title'];
			$desdata['createid'] = $_SESSION[C('USER_AUTH_KEY')];
			$desdata['createtime'] = time();
			$designlist = $designmodel->data($desdata)->where("id={$dasdata['id']}")->save();
		}else if($_POST['isshow']==0){//删除记录
			$designdel =$designmodel->where("id={$dasdata['id']}")->delete();
		}
		//重生成Action文件
		$this->createTypeActionFile($name,$_POST['id'],$type);
		if(false === $ret){
			$this->error("入库失败");
		}else{
			$this->success("操作成功");
		}
			
	}
	public function insertsub(){
		$_POST['allfields'] = serialize(array_keys($_POST['showwidth']));
		$showsort = ''; 
		if($_POST['showsort']){
			foreach($_POST['showsort'] as $k=>$v){
				$showsort .= $showsort?",".$k." ".$v:$k." ".$v;
			}
		}
		$_POST['showsort'] = $showsort;
		if(!$_POST['name']) $this->error("名称不能为空");
		$model=M("mis_system_panel_desing_sub");
		if($_POST['modelid']){
			$_POST['modelname']=getFieldBy($_POST['modelid'], "id", "name", "node");
		}
		$_POST['masid'] = $_POST['id'];
		unset($_POST['id']);
		//处理条件字段
		//面板条件
		$data2 = $_POST;
		$_POST['map']= str_replace("&#39;", "'", html_entity_decode($data2['rules']['panel']));
		$_POST['showmap']=str_replace("&#39;", "'", html_entity_decode($data2['showrules']['panel']));
		$_POST['rulesinfo'] = $data2['rulesinfo']['panel'];
		//更多按钮条件
		$_POST['morerules']= str_replace("&#39;", "'", html_entity_decode($data2['rules']['more']));
		$_POST['moreshowrules']=str_replace("&#39;", "'", html_entity_decode($data2['showrules']['more']));
		$_POST['morerulesinfo'] = $data2['rulesinfo']['more'];
		//处理显示字段
		$showfield = array();
		$i = 0;
		foreach($_POST['checkfield'] as $key=>$val){
			$showfield[] = $key."|".$_POST['showwidth'][$key]."|".$i;
			$i++;
		}
		$_POST['showtitle'] = implode(",",$showfield);
		/**
		 * 补加生成一个报表Action名称
		 * 需保证节点、物理文件唯一性
		 */
		$type = getFieldBy($_POST['masid'],'id','type','mis_system_panel_desing_mas');
		if($type==0){
			$_POST['reportaction'] = $this->getUniqueReportFielname(4);
		}		
		if (false === $data = $model->create ()) {
				$this->error ( $model->getError () );
		}
		//保存当前数据对象
		try {
			$list=$model->add ();
		}catch (Exception $e){
			$this->error($e->__toString());
		}
		/**
		 * //报表生成Action 位于所选节点下并生成节点
		 */			
		if($type==0){
			$actionName = $_POST['reportaction'];
			//生成Action文件			
			$time = date('Y-m-d H:i:s');
			$uid = $_SESSION['loginUserName'];
			$path = dirname(__FILE__);//__ROOT__."/Admin/Lib/Action";
			$filename = $actionName.'Action.class.php';
			$file = $path.'/'.$filename;
			$file = str_replace("\\","/",$file);
			$content = <<<EOF
			<?php
				/**
				 * @Title: {$actionName}Action
				 * @Package package_name
				 * @Description: todo(报表)";
				 * @author {$uid}
				 * @company 重庆特米洛科技有限公司";
				 * @copyright 本文件归属于重庆特米洛科技有限公司";
				 * @date {$time}
				 * @version V1.08
				*/
				class {$actionName}Action extends AutoPanelAction{
					public function setting(){
					}
					public function getConfig(){
					}
					/**
					 * 显示当前面板内容
					 * @Title: showPanel
					 * @Description: todo(页面展示)
					 * @author {$uid}
					 * @date {$time}
					 * @throws
					 */
					public function showPanel(){
						\$submodel=M("mis_system_panel_desing_sub");
						\$sublist = \$submodel->where("id={$list}")->find();
						\$this->assign("sublist",\$sublist);
						\$this->display("MisSystemPanelDesingMas:report");
					}
					public function index(){
						\$submodel=M("mis_system_panel_desing_sub");
						\$sublist = \$submodel->where("id={$list}")->find();
						\$this->assign("sublist",\$sublist);
						\$this->display("MisSystemPanelDesingMas:reportindex");
					}
				}	
		
EOF;
			if(!is_dir(dirname($file))) mk_dir(dirname($file),0777);
			if( false === file_put_contents( $file , $content )){
				$this->error ("Action文件生成失败!");
			}
			//生成节点
			$nodedata["name"]=$actionName;
			$nodedata["title"]=$_POST['name'];
			$nodedata["type"]=3;
			$nodedata["showmenu"]=1;
			$nodedata["level"]=3;
			if($type==0){
				$nodedata["pid"]=$_POST["modelid"];
			}else{
				$nodedata["pid"]=getFieldBy($_POST["modelid"],"id",'pid','node');
			}
			$nodedata["group_id"]=$_POST['group_id'];
			$nodedata["showmenu"]=1;
			$nodedata['icon']='-.png';
			$nodedata['remark']='面板生成的报表文件节点:'.$actionName."Action";
			$nodemodel = M("node");
			$nodelist = $nodemodel->add($nodedata);
			if(false===$nodelist){
				$this->error("节点生成失败");
			}
			//生成授权节点 ==================开始=====================
		$roleModel=D('Role');
		$accessModel=D('Access');
			//生成一个下级节点用于授权
			$nodedata2["name"]='index';
			$nodedata2["title"]='首页';
			$nodedata2["type"]=4;
			$nodedata2["showmenu"]=1;
			$nodedata2["level"]=4;
			$nodedata2["pid"]=$nodelist;
			$nodedata2["group_id"]=$_POST['group_id'];
			$nodedata2["showmenu"]=1;
			$nodedata2['icon']='-.png';
			$nodedata2['remark']='报表生成权限节点';
			$nodeindexid = $nodemodel->add($nodedata2);
			if(false===$nodeindexid){
				$this->error("报表生成权限节点失败");
			}
		
			//根据pid获取模块节点信息
			$vo = $nodemodel->find($nodeindexid);
			//根据模块的pid获取面板信息或者空面板信息
			$vo2 = $nodemodel->find($nodelist);
			//获取父类信息
			$p_title = $nodedata["title"];
			$j=0;
			for($s=1;$s<=5;$s++){
				if($s==1){
					$t="所有";
				}else if($s==2){
					$t="公司";
				}else if($s==3){
					$t="部门";
				}else if($s==5){
					$t="禁用";
				}else{ $t="个人";
				}
				$data=array();
				$data["name"] = $p_title."_".$t."_".$nodedata2["title"];
				$data["nodetitle"] = $nodedata2["title"];
				$data["nodeidcategory"] = 1; //???????????
				$data["plevels"] = $s;
				$data["nodepid"] = $nodelist;
				$data["nodeid"] = $nodeindexid;
				$data["createtime"] = time();
				$data["createid"] = 1;
				$data["sort"] = $j;
				$data["status"] = 1;
				$j++;
				$list = $roleModel->add($data);
				if($list){
					$data["id"] = $list;
					$role_list[] = $data;
				}else{
					$this->error("插入role节点失败，请联系管理员");
				}
			}
			foreach($role_list as $k=>$v){
				//定义4个参数，存放上级id  $pid_type0 节点admin的ID，$pid_type1面板id ，$pid_type2模块ID或者空模块ID ，$pid_type3模块ID
				$pid_type0=$pid_type1=$pid_type2=$pid_type3="";
				if($vo2['type']==2){//kong mokuai
					$vo3 = $nodemodel->find($vo2['pid']);//获取面板
					$pid_type3 = $vo['id'];//模块id
					$pid_type2 = $vo2['id'];//控模块id
					$pid_type1 = $vo3['id'];//面板模块id
					$pid_type0 = $vo3['pid'];//项目id
				}else{//mianban
					$pid_type2 = $vo['id'];//模块id
					$pid_type1 = $vo2['id'];//面板模块id
					$pid_type0 = $vo2['pid'];//项目id
				}
				if($pid_type0!="") $idlist[$v["id"]][] = $pid_type0;
				if($pid_type1!="") $idlist[$v["id"]][] = $pid_type1;
				if($pid_type2!="") $idlist[$v["id"]][] = $pid_type2;
				if($pid_type3!="") $idlist[$v["id"]][] = $pid_type3;
				$data=array();
				$data["role_id"] = $v["id"];
				$data["node_id"] = $v["nodeid"];
				$data["level"]   = 4;
				$data["type"]    = 4;
				$data["pid"]     = $v["nodepid"];
				$data["plevels"] = $v["plevels"];
				$result = $accessModel->add($data);
				if ($result) {
					$result = $roleModel->setGroupActions($v["id"],$idlist[$v["id"]], true);
					if ($result === false) {
						$this->error ( L('_ERROR_') );
					}
				} else {
					$this->error ( L('_ERROR_') );
				}
			}
		
		}		
		//生成授权节点 ==================结束=====================
		
		
		if(false===$list){
			$this->error('添加失败');
		}else{
			$this->success('添加成功');
		}
	}
	public function _before_delete(){
		$model = M("mis_system_panel_desing_sub");
		$rs = $model->where("masid=".$_REQUEST['id'])->find();
		if($rs){
			$this->error("该项有下级数据，请先清除下级数据后再删除该项！");
		}
		$name = getFieldBy($_REQUEST['id'],'id','name','mis_system_panel_desing_mas');
		if($name){
			$filename = 'MisSystemPanel'.$name.'Action.class.php';
			$path = dirname(__FILE__);//__ROOT__."/Admin/Lib/Action";
			$file = $path.'/'.$filename;
			unlink($file);
		}
		
	}
	public function deletesub(){
		$model=M("mis_system_panel_desing_sub");
		$rs = $model->where("id={$_REQUEST['id']}")->find();
		$reportaction = $rs['reportaction'];
		if($reportaction){
			$file=dirname(__FILE__).$reportaction."Action.class.php";
			if(is_file($file)){
				unlink($file);
			}
			$nodemodel = M("node");
			$nodelist = $nodemodel->where("name='{$reportaction}'")->find();
			if($nodelist){
				$accModel = M("access");
				$roleModel = M("role");
				$rolemap['nodeid'] = $nodelist['id'];
				$roleList = $roleModel->where($rolemap)->select();
				$roleidarr = array();
				foreach($roleList as $rk=>$rv){
					$roleidarr[] = $rv['id'];
				}
				//删除 access记录
				$assdelmap['role_id'] = array("in",$roleidarr);
				$assret = $accModel->where($assdelmap)->delete();
				if(false===$nodelretsun){
					$this->error("删除 access记录失败");
				}
				//删除role记录
				$rolret = $roleModel->where($rolemap)->delete();
				if(false===$nodelretsun){
					$this->error("删除role记录失败");
				}
				//删除权限子节点
				$delmap['pid'] = $nodelist['id'];
				$delmap['level'] = 4;
				$delmap['type'] = 4;
				$nodelretsun = $nodemodel->where($delmap)->delete();
				if(false===$nodelretsun){
					$this->error("删除权限子节点失败");
				}
				//删除节点
				$nodelret = $nodemodel->where("name='{$reportaction}'")->delete();
				if(false===$nodelret){
					$this->error("删除节点失败");
				}
			}
			
		}
		$ret = $model->where("id=".$_REQUEST['id'])->delete();
		
		if(false!==$ret){
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}
	}
	private function lookgroupandnode($type){
		//组装菜单及操作级
		$NodeModel=D('Node');
		$map=array();
		$map['status']=1;
		if($type==0){ //报表
			$map['level']=2;
		}else if($type==1){//面板
			$map['level']=3;
		}
		$NodeList=$NodeModel->where($map)->getField("id,title,group_id");
		//		$NodeList=$NodeModel->field("id,title,group_id")->where("status=1 and level=3")->select();
	
		$groupList=array();
		$newNodelList=array();
		foreach ($NodeList as $key=>$val){
			if(in_array($val['group_id'],array_keys($groupList))){
				$newNodelList['node'][]=array(
						'id'=>$val['id'],
						'name'=>$val['title'],
						'group_id'=>$val['group_id'],
				);
			}else{
				if(!$defaultVal){
					$defaultVal=$val['id'];
				}
				$groupList[$val['group_id']]=1;
				$newNodelList['group'][]=array(
						'id'=>$val['group_id'],
						'name'=>getFieldBy($val['group_id'], "id", "name", "group"),
						'sort'=>getFieldBy($val['group_id'], "id", "sorts", "group"),
				);
				$newNodelList['node'][]=array(
						'id'=>$val['id'],
						'name'=>$val['title'],
						'group_id'=>$val['group_id'],
				);
			}
		}
		$newNodelList['group']=array_merge(array_sort($newNodelList['group'],'sort','asc'));
		$this->assign("defaultVal",$defaultVal);
		$this->assign("nodelist",json_encode($newNodelList));
	}
	/**
	 * @Title: lookupdetailadd
	 * @Description: todo(模块更改时 更改条件)   
	 * @author 谢友志 
	 * @date 2015-7-3 下午4:44:41 
	 * @throws
	 */
	public function lookupdetailadd(){
		$scdmodel = D('SystemConfigDetail');
		$modelName=getFieldBy($_REQUEST['modelid'], "id", "name", "node");
		$detailList = $scdmodel->getDetail($modelName,false);
		if($_REQUEST['type']==1){
			$modelname=$_REQUEST['modelname']?$_REQUEST['modelname']:$modelName;
			$this->assign("modelname",$modelname);
			if($_REQUEST['jsonvo']){
				$vo = $_REQUEST['jsonvo'];
				if($_REQUEST['akey'] == 'more'){
					$vo['rules'] = $vo['rules']['more']&&$vo['rules']['more']!='null'?$vo['rules']['more']:'';
					$vo['showrules'] = $vo['showrules']['more']&&$vo['showrules']['more']!='null'?$vo['showrules']['more']:'';
					$vo['rulesinfo'] = $vo['rulesinfo']['more']&&$vo['rulesinfo']['more']!='null'?$vo['rulesinfo']['more']:'';
				}else{
					$vo['rules'] = $vo['rules']['panel']&&$vo['rules']['panel']!='null'?$vo['rules']['panel']:"";
					$vo['showrules'] = $vo['showrules']['panel']&&$vo['showrules']['panel']!='null'?$vo['showrules']['panel']:'';
					$vo['rulesinfo'] = $vo['rulesinfo']['panel']&&$vo['rulesinfo']['panel']!='null'?$vo['rulesinfo']['panel']:'';
				}
				$this->assign("vo",$vo);
			}			
			$this->assign("akey",$_REQUEST['akey']);
			$this->display();
		}else{
			$detailList['modelname']=$modelName;
			
			echo json_encode($detailList);
		}
	}
	/**
	 * @Title: lookupdetail
	 * @Description: todo(模块更改时更改字段集)   
	 * @author 谢友志 
	 * @date 2015-7-3 下午4:43:48 
	 * @throws
	 */
	public function lookupdetail(){
		$scdmodel = D('SystemConfigDetail');
		$modelName=getFieldBy($_REQUEST['modelid'], "id", "name", "node");
		$detailList = $scdmodel->getDetail($modelName,false);
		if($_REQUEST['type']==1){
			$modelname=$_REQUEST['modelname'];
			$this->assign("modelname",$modelname);
			$this->assign("vo",$_REQUEST['jsonvo']);
			$this->display();
		}else{
			//处理字段顺序(按选择的需要显示字段先后顺序排列)
			if($_REQUEST['jsonvo']){
				$vo = $_REQUEST['jsonvo'];
				$temp = array();
 				if($vo['allfields']&&$vo['allfields']!='false'&&$vo['allfields']!='null'){//新加数据库字段，按页面
					$detailList3 = $detailList;
					foreach($detailList as $key=>$val){
						$darr[] = $val['name'];
					}
					$detailList2 = array();					
					foreach($vo['allfields'] as $ck=>$cv){
					//添加放错功能，防止配置文件删除字段导致字段显示不一致
						if(!in_array($v,$darr)){
							unset($vo['allfields'][$ck]);
							continue;
						}
						foreach($detailList as $k=>$v){
							//判断 $detailL是索引还是关联数组
							if((int)$k == 0 && $k!='0' ){
								if($cv == $v['name']){
									$detailList2[$k] = $v;
								}
							}else{
								if($cv == $v['name']){
									$detailList2[$ck] = $v;
								}
							}
						}
					}
					//添加放错功能，防止配置文件添加字段导致字段显示不一致
					$detailList3 = $detailList;
					foreach($detailList2 as $key=>$val){
						foreach($detailList3 as $k=>$v){
							if($val['name'] == $v['name']){
								unset($detailList3[$k]);
							}
						}
					}
					$detailList=array_merge($detailList2,$detailList3);
				}else{ //旧代码，添加关联判断，防止索引数组array_merge字段重复
					foreach($vo['checkfield'] as $ck=>$cv){
						foreach($detailList as $k=>$v){
							//判断 $detailL是索引还是关联数组
							if((int)$k == 0 && $k != '0' ){
								if($cv == $v['name']){
									$temp[$k] = $v;
								}
							}
						}
					}
					$detailList = array_merge($temp,$detailList);
				}
				unset($temp);
			}	
			$detailList['modelname']=$modelName;
			echo json_encode($detailList);
		}
	}
	public function updatesub(){
		//添加一个存储字段allfields，用于存储字段顺序
		$_POST['allfields'] = serialize(array_keys($_POST['showwidth']));
		$showsort = '';
		if($_POST['showsort']){
			foreach($_POST['showsort'] as $k=>$v){
				$showsort .= $showsort?",".$k." ".$v:$k." ".$v;
			}
		}
		$_POST['showsort'] = $showsort;
		$model = M( "mis_system_panel_desing_sub" );		
		if($_POST['modelid']){
			$_POST['modelname']=getFieldBy($_POST['modelid'], "id", "name", "node");
		}	
		//处理条件字段
		//面板条件
		$data2 = $_POST;
		$_POST['map']= str_replace("&#39;", "'", html_entity_decode($data2['rules']['panel']));
		$_POST['showmap']=str_replace("&#39;", "'", html_entity_decode($data2['showrules']['panel']));
		$_POST['rulesinfo'] = $data2['rulesinfo']['panel'];
		//更多按钮条件
		$_POST['morerules']= str_replace("&#39;", "'", html_entity_decode($data2['rules']['more']));
		$_POST['moreshowrules']=str_replace("&#39;", "'", html_entity_decode($data2['showrules']['more']));
		$_POST['morerulesinfo'] = $data2['rulesinfo']['more'];
		//处理显示字段
		//$_POST['showtitle']=implode(',',$_POST['showtitle']);
		$showfield = array();
		$i = 0;
		foreach($_POST['checkfield'] as $key=>$val){
			$showfield[] = $key."|".$_POST['showwidth'][$key]."|".$i;
			$i++;
		}
		$_POST['showtitle'] = implode(",",$showfield);
		$name=$this->getActionName();
		if (false === $data = $model->create ()) {
			
			$this->error ( $model->getError () );
		}
		//生成节点
		$actionName=getFieldBy($_POST['id'],'id','reportaction','mis_system_panel_desing_sub');
		
		if($_REQUEST['type']==2){
			$nodedata["pid"]=$_POST["modelid"];
		}else{
			$nodedata["pid"]=getFieldBy($_POST["modelid"],"id",'pid','node');
		}
		$nodedata["group_id"]=$_POST['group_id'];
		$nodedata['remark']='面板修改的报表文件节点:'.$actionName."Action";
		$nodedata["title"]=$_POST['name'];
		$nodemodel = M("node");
		$nodelist = $nodemodel->where("name='{$actionName}'")->save($nodedata);
		if(false===$nodelist){
			$this->error("节点修改失败");
		}
		// 更新数据
		$list=$model->save ();
		//添加报表action文件修改功能
		if(false !== $list){
			$masid = getFieldBy($_POST['id'],"id","masid","mis_system_panel_desing_sub");
			$type = getFieldBy($masid,"id","type","mis_system_panel_desing_mas");
			
			if($type ==0){
				//生成Action文件
				$time = date('Y-m-d H:i:s');
				$uid = $_SESSION['loginUserName'];
				$path = dirname(__FILE__);//__ROOT__."/Admin/Lib/Action";
				$filename = $actionName.'Action.class.php';
				$file = $path.'/'.$filename;
				$file = str_replace("\\","/",$file);
				$masid = getFieldBy($_POST['id'],"id","masid","mis_system_panel_desing_sub"); 
				$content = <<<EOF
				<?php
					/**
					 * @Title: {$actionName}Action
					 * @Package package_name
					 * @Description: todo(报表)（改）";
					 * @author {$uid}
					 * @company 重庆特米洛科技有限公司";
					 * @copyright 本文件归属于重庆特米洛科技有限公司";
					 * @date {$time}
					 * @version V1.08
					*/
					class {$actionName}Action extends AutoPanelAction{
						function __construct(){
							parent::__construct();
							\$this->id='{$masid}';
						}
						public function setting(){
						}
						/**
						 * 显示当前面板内容
						 * @Title: showPanel
						 * @Description: todo(页面展示)
						 * @author {$uid}
						 * @date {$time}
						 * @throws
						 */
						public function showPanel(){
							\$submodel=M("mis_system_panel_desing_sub");
							\$sublist = \$submodel->where("id={$_POST['id']}")->find();
							\$this->assign("sublist",\$sublist);
							\$this->display("MisSystemPanelDesingMas:report");
						}
						public function index(){
							\$submodel=M("mis_system_panel_desing_sub");
							\$sublist = \$submodel->where("id={$_POST['id']}")->find();
							\$this->assign("sublist",\$sublist);
							\$this->display("MisSystemPanelDesingMas:reportindex");
						}
					}
				
EOF;
				if(!is_dir(dirname($file))) mk_dir(dirname($file),0777);
				if( false === file_put_contents( $file , $content )){
					$this->error ("Action文件生成失败!");
				}
			}
		}
		//echo $model->getlastsql();exit;
		if (false !== $list) {
			$this->success ( L('_SUCCESS_'));
		} else {
			$this->error ( L('_ERROR_') );
		}
	}
	private  function getTypeActionName(){
		$name = $this->genRandomString(3);
		$filename = "MisSystemPanel".$name.'Action.class.php';
		$file = dirname(__FILE__).'/'.$filename;
		if(file_exists($file)){
			$this->getTypeActionName();
		}
		return $name;
	}
	private function getUniqueReportFielname($length){
		$name = "MisSystemReport".$this->genRandomString($length);
		$filename = $name.'Action.class.php';
		$path = dirname(__FILE__);//__ROOT__."/Admin/Lib/Action";
		$file = $path.'/'.$filename;
		if(file_exists($file)){
			$this->getUniqueReportFielname();
		}
		return $name;
	}
	
	private  function createTypeActionFile($name,$id,$type){
		$filename = 'MisSystemPanel'.$name.'Action.class.php';
		$path = dirname(__FILE__);//__ROOT__."/Admin/Lib/Action";
		$file = $path.'/'.$filename;
		$file = str_replace("\\","/",$file);
		$time = date('Y-m-d H:i:s');
		$uid = $_SESSION['loginUserName'];
		if($type==1){ //面板
			$content = <<<EOF
			<?php
			/**
			 * @Title: MisSystemPanel{$name}Action
			 * @Package package_name
			 * @Description: todo(自定义小块面板（个人首页，公司首页）)";
			 * @author {$uid}
			 * @company 重庆特米洛科技有限公司";
			 * @copyright 本文件归属于重庆特米洛科技有限公司";
			 * @date {$time}
			 * @version V1.08
			*/
			class MisSystemPanel{$name}Action extends AutoPanelAction{
				function __construct(){
					parent::__construct();
					\$this->id={$id}; 
				}
				public function setting(){
				}
				/**
				 * 显示当前面板内容
				 * @Title: showPanel
				 * @Description: todo(页面展示)
				 * @author {$uid}
				 * @date {$time}
				 * @throws
				 */
				public function showPanel(){
					import ( '@.ORG.Browse' );
					\$submodel=M("mis_system_panel_desing_sub");
					\$sublist = \$submodel->where("masid={$id}")->select();
					\$scdmodel = D("SystemConfigDetail");
					\$map["status"]=1;
					foreach(\$sublist as \$key=>\$val){
						\$model = \$val["modelname"];
						\$fields = explode(",",\$val["showtitle"]);
						\$defaultwidth = (int)100/count(\$fields);
						\$temp = explode(",",\$val["showtitle"]);
							\$newfields = array();
						foreach(\$temp as \$tk=>\$tv){
							\$temparr = explode("|",\$tv);
							\$newfields[\$temparr[0]]['name'] = \$temparr[0];
							\$newfields[\$temparr[0]]['width'] = \$temparr[1];
							\$newfields[\$temparr[0]]['sort'] = \$temparr[2];									
								
						}
						sortArray(\$newfields , 'sort','asc','number');
						\$detailList = \$scdmodel->getDetail(\$model,false);
						\$sublist[\$key]['link'] = __APP__."/".\$model."/index";
						\$sublist[\$key]['rel'] = \$model;
						\$newd = array();
						foreach(\$newfields as \$k=>\$v){
							foreach(\$detailList as \$dk=>\$dv){
								if(\$v['name']==\$dv['name']){
									\$newd[\$k] = \$dv;
									\$newd[\$k]['shows'] =1;
									\$newd[\$k]['sortnum'] = \$v['sort'];
									if(strpos('px',\$v['width'])>0||strpos('PX',\$v['width'])>0||strpos('Px',\$v['width'])>0){
										\$newd[\$k]['widths'] = \$v['width'];
									}else{
										\$newd[\$k]['widths'] = \$v['width']?\$v['width']."%":\$defaultwidth."%";
									}
									
								}
							}
						}
						\$sublist[\$key]["detailList"] = \$newd;
						//具体数据1
						\$listmodel = D(\$model);
						\$val['num'] = \$val['num']?\$val['num']:5;
						\$time2 = time();
						\$part = array (
										'&quot;',
										'&#39;',
										'&lt;',
										'&gt;',
										'==' ,
										"\\\$time",
										"\\\$uid"
								);
						\$replacepart = array (
										'"',
										"'",
										'<',
										'>',
										'=' ,
										\$time2,
										\$_SESSION[C("USER_AUTH_KEY")]
								);
						//面板条件
						if(\$val['map']){
							\$val["map"] = str_replace(\$part,\$replacepart, \$val["map"] );
								// 把条件加入map 中
							\$map["_string"] =\$val["map"];
						}
						//更多按钮的条件
						if(\$val["morerules"]){
							\$val["moresmap"] = str_replace (\$part,\$replacepart,\$val["morerules"] );
						}						
						//验证浏览及权限 
						if( !isset(\$_SESSION['a']) ){
							\$map=D('User')->getAccessfilter(\$model,\$map);	
						}
						//获取当前模型数据权限 by renl 20150626
						\$broMap = Browse::getUserMap (\$model);
						if(\$broMap){
							if(\$map['_string']){
								\$map['_string'].=" and ".\$broMap;
							}else{
								\$map['_string']=\$broMap;
							}
						}
						if(\$val['showsort']){
							\$order = \$val['showsort'];
						}else{
							\$order = 'id desc';
						}
						\$list = \$listmodel->where(\$map)->order(\$order)->limit(\$val['num'])->select();
						\$sublist[\$key]["list"] = \$list;
						if( \$val["moresmap"]){
							\$sublist[\$key]["moresmap"] = base64_encode( \$val["moresmap"]);
						}else{
							\$sublist[\$key]["moresmap"] = '';
						}
						unset(\$map["_string"]);
					}
					\$this->assign("sublist",\$sublist);
					\$this->display("MisSystemPanelDesingMas:news");
				}
			}
		
EOF;
			
			
		}else if($type==2){ //新闻
			$content =<<<EOF
			
			<?php
			/**
			 * @Title: MisSystemPanel{$name}Action
			 * @Package package_name
			 * @Description: todo(自定义小块面板（个人首页，公司首页）)";
			 * @author {$uid}
			 * @company 重庆特米洛科技有限公司";
			 * @copyright 本文件归属于重庆特米洛科技有限公司";
			 * @date {$time}
			 * @version V1.08
			*/
			class MisSystemPanel{$name}Action extends AutoPanelAction{
				function __construct(){
					parent::__construct();
					\$this->id={$id}; 
				}
				public function setting(){
				}
				/**
				 * 显示当前面板内容
				 * @Title: showPanel
				 * @Description: todo(页面展示)
				 * @author {$uid}
				 * @date {$time}
				 * @throws
				 */
				public function showPanel(){
					\$submodel=M("mis_system_panel_desing_sub");
					\$sublist = \$submodel->where("masid={$id}")->select();
					\$scdmodel = D("SystemConfigDetail");
					\$map["status"]=1;
					foreach(\$sublist as \$key=>\$val){
					\$curl="http://news.baidu.com/ns?cl=2&rn=20&tn={\$val['keywordtype']}&word=";
					\$title=\$val['keyword'];
					\$endcurl=\$curl.urlencode(\$title); 
					\$html = file_get_contents(\$endcurl);
					\$star = strpos(\$html,'<div id="content_left">');
					\$end = strpos(\$html,'<div id="gotoPage">');
					\$html = substr(\$html,\$star,\$end-\$star);
					preg_match_all('#<h3(.*?)>(.*?)</h3>#is' , \$html , \$str );
					if(\$val['keywordtype']=="news"){
						preg_match_all('#<p class=\"c-author\">(.*?)</p>#is' , \$html , \$str1 );
					}else{
						preg_match_all('#<div class=\"c-title-author\">(.*?)<a(.*?)>(.*?)</a></div>#is' , \$html , \$str1 );
					}
					if(empty(\$val["num"])){
						\$num = 5;
					}else{
						\$num = \$val["num"] ;
					}
					\$i=1;
					foreach(\$str1[1] as \$tk => \$tv){
						if(\$i>\$num) break;
						\$time = explode("&nbsp;&nbsp;",\$tv);
						\$sublist[\$key]["time"][]=substr(strip_tags(\$time[1]),0,18);
									\$i++;
					}
					\$j=1;
					foreach(\$str[0] as \$k => \$v){
						if(\$j>\$num) break;
						preg_match_all('#href=\"(.*?)\"#' , \$v , \$url);
						preg_match_all('#<a(.*?)>(.*?)</a>#is' , \$v , \$text);
						\$listArr=array();
						\$listArr=array(
							'title'=>reset(\$text[2]),
							'url'=>reset(\$url[0]),
						);
						\$sublist[\$key]["list"][]=\$listArr;
						\$j++;
					}
					}
					\$this->assign("sublist",\$sublist);
					\$this->display("MisSystemPanelDesingMas:newscontent");
				}
			}
EOF;
		}else{ //报表
			$content = <<<EOF
			
<?php
/**
 * @Title: MisSystemPanel{$name}Action
 * @Package package_name
 * @Description: todo(自定义小块面板（个人首页，公司首页）)";
 * @author {$uid}
 * @company 重庆特米洛科技有限公司";
 * @copyright 本文件归属于重庆特米洛科技有限公司";
 * @date {$time}
 * @version V1.08
*/
class MisSystemPanel{$name}Action extends AutoPanelAction{
	function __construct(){
					parent::__construct();
					\$this->id={$id}; 
				}
	public function setting(){
	}

	/**
	 * 显示当前面板内容
	 * @Title: showPanel
	 * @Description: todo(页面展示)
	 * @author {$uid}
	 * @date {$time}
	 * @throws
	 */
	public function showPanel(){
		\$submodel=M("mis_system_panel_desing_sub");
		\$sublist = \$submodel->where("masid={$id}")->find();
		\$this->assign("sublist",\$sublist);
		\$this->display("MisSystemPanelDesingMas:report");
	}
	public function index(){
		\$this->showPanel();
	}
}
			
EOF;
		}
		
		if(!is_dir(dirname($file))) mk_dir(dirname($file),0777);
		if( false ===$num = file_put_contents( $file , $content )){
			echo $name."Action文件生成失败";exit;
		$this->error ("Action文件生成失败!");
		}
	
		
	}
	/**
	 * @Title: initPanleActionFile
	 * @Description: todo(初始化面板Action文件)   
	 * @author 谢友志 
	 * @date 2015-11-5 上午11:44:37 
	 * @throws
	 */
	public function initPanleActionFile(){
		$model=D($this->getActionName());
		$list = $model->where("status=1")->select();
		foreach($list as $key=>$val){
			$this->createTypeActionFile(ucfirst($val['name']),$val['id'],$val['type']);
		}
		echo "Action文件初始化完成";
	}
	/**
	 * @Title: role
	 * @Description: todo(面板禁权)   
	 * @author 谢友志 
	 * @date 2015-11-3 下午3:33:41 
	 * @throws
	 */
	public function role(){
		//面板记录
		$model=D($this->getActionName());
		$map['id'] = $_REQUEST['id'];
		$rs = $model->where($map)->find();
		$rs['paneltype'] = getSelectByName('panelDesignType',$rs['type']);
		$this->assign("rs",$rs);
		//授权对象
		if(empty($_GET['jump'])){
			$accessObjModel = D("MisSystemDataAccessCarrier");
			$treeList = $model->userAndRolegroupToTree("MisSystemPanelDesingMasRoleBOX",__URL__."/role",$rs['id']);
			$this->assign("treeList",json_encode($treeList));
		}else{
			$this->assign("objid",$_REQUEST['id']);
			$this->assign("objtype",$_REQUEST['type']);
		}
		//面板记录禁权情况
		if($_REQUEST['jump']){
			$this->display("MisSystemPanelDesingMas:roleview");
		}else{
			$this->display();
		}
		
	}
	/**
	 * @Title: getListOfPanel
	 * @Description: todo(ajax获取面板记录+对象禁权记录)   
	 * @author 谢友志 
	 * @date 2015-11-4 上午10:55:16 
	 * @throws
	 */
	public function getListOfPanel(){
		if(!empty($_POST['type'])||$_POST['type']==='0'){
			$model=D($this->getActionName());
			$map['type'] = $_POST['type'];
			$map['status'] = 1;
			$list = $model->where($map)->select();
			$rmodel = M("mis_system_panel_desing_role");
			$rmap['type'] = $_POST['type'];
			$rmap['objid'] = $_POST['objid'];
			$rmap['objtype'] = $_POST['objtype'];
			$rlist = $rmodel->where($rmap)->find();
			$arr = array();
			if($rlist){
				$arr = explode(",",$rlist['forbidroleids']);
			}
			foreach($list as $key=>$val){
				if(in_array($val['id'],$arr)){
					$list[$key]['checked'] = 'checked="checked"';
				}else{
					$list[$key]['checked'] = '';
				}
			}
			echo json_encode($list);
		}
	}
	/**
	 * @Title: roleinsert
	 * @Description: todo(禁权入库)   
	 * @author 谢友志 
	 * @date 2015-11-4 上午10:54:54 
	 * @throws
	 */
	public function roleinsert(){
		$model = M("mis_system_panel_desing_role");
		$_POST['forbidroleids'] = $_POST['forbidroleids']?join(",",$_POST['forbidroleids']):'';
		$map['type'] = $_POST['type'];
		$map['objid'] = $_POST['objid'];
		$map['objtype'] = $_POST['objtype'];
		$rs = $model->where($map)->find();	
		if(false===$data = $model->create()){
			$this->error();
		}
		if($rs){
			if(!$_POST['forbidroleids']){
				$ret = $model->where($map)->delete();
				if(false === $ret){
					$this->error("删除失败");
				}
			}else{
				$ret = $model->where($map)->save($data);
				if(false === $ret){
					$this->error("修改失败");
				}
			}			
		}else{
			$ret = $model->add();
			if(false === $ret){
				$this->error("添加失败");
			}
		}
		//已用户为对象写入缓存文件

		//生成缓存文件
		$thismodel = D($this->getActionName());
		$thismodel->panelRoleConfig();
		$this->success("保存成功");
	}
	
}












