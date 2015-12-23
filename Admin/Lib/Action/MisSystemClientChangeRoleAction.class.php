<?php
/**
 * @Title: MisSystemClientChangeRoleAction 
 * @Package package_name
 * @Description: todo(转授权) 
 * @author谢友志 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015-8-25 上午9:11:26 
 * @version V1.0
 */
class MisSystemClientChangeRoleAction extends CommonAction{
	public function _filter(&$map){
		if ($_SESSION["a"] != 1) $map['createid']=$_SESSION[C("USER_AUTH_KEY")];
		if($_REQUEST['id']) $map['userid'] = $_REQUEST['id'];
	}
	public function _before_index(){
		
		//获取用户列表
		$usermodel = M("user");
		$userlist = $usermodel->field("id,name,companyid")->where("status=1")->select();
		
		//组装左边树
		$param['rel']="MisSystemClientChangeRoleBox";
		$param['url']="__URL__/index/jump/jump/id/#id#";
		$treemiso[]=array(
				'id'=>0,
				'pId'=>0,
				'name'=>'用户',
				'title'=>'用户',
				'open'=>true,
				'isParent'=>true,
				'rel'=>"MisSystemClientChangeRoleBox",
				'url'=>"__URL__/index/jump/jump",
				'target'=>'ajax'
		);
		$treearr = $this->getTree($userlist,$param,$treemiso,false);
		$this->assign('treearr',$treearr);
	}
	public function _before_add(){
		//获取权限组、节点
		if($_REQUEST['userid']<=0){
			$this->error("请选择授权对象");
		}
		$model = D($this->getActionName());
		$accessnode = $model->getAname();
		$nodelist = array();
		foreach ($accessnode as $key=>$val){
			foreach($val as $key1=>$val1){
				foreach($val1['levelthree'] as $k=>$v){
					$nodelist[$v['name']] = $v['title'];
				}
			}
		}
		$this->assign("userid",$_REQUEST['userid']);
		if($_REQUEST['id']){
			$this->assign("id",$_REQUEST['id']);
			$vo = $model->where("id=".$_REQUEST['id'])->find();
			$idsarr = array();
			if($vo['content']){
				$idsarr = explode(',',$vo['content']);
			}
			$this->assign('vo',$vo);
		}
		$this->assign('nodelist',$nodelist);
	}
	public function _before_insert(){
		$model = D($this->getActionName());
		$_POST['fieldname'] = $_POST['fieldname']?$_POST['fieldname']:'id';
		$_POST['tablename'] = D($_POST['modelname'])->getTableName();
		$dmap['modelname'] = $_POST['modelname'];
		$dmap['createid'] = $_SESSION [C ( 'USER_AUTH_KEY' )];
		$dmap['userid'] = $_POST['userid'];
		if(!$_POST['id']){
			unset($_POST['id']);
			$rs = $model->where($dmap)->find();
			if ($rs){
				$_POST['id'] = $rs['id'];
				$this->update();
				exit;
			}
		}else{
			$dmap['id'] = array("neq",$_POST['id']);
			$rs = $model->where($dmap)->find();
			$this->update();
			exit;
		}
	}
	public function lookupGetDetailList($modelname='',$userid=''){
		$name=$modelname?$modelname:$_POST['modelname'];
		$userid=$userid?$userid:$_POST['userid'];
		$this->assign('modelname',$name);
		$this->assign('userid',$userid);
		//获取模型 权限内列表 ----开始-----
		$map = $this->_search ($name);
		$nameAction = A($name);
		if (method_exists ( $nameAction, '_filter' )) {
			$nameAction->_filter ( $map );
		}
		if($_REQUEST['fieldtype']){
			$this->getBindSetTables($map);
		}
		if($_REQUEST['projectid']){
			$map['projectid'] = $_REQUEST['projectid'];
		}
		if($_REQUEST['projectworkid']){
			//$map['projectworkid'] = $_REQUEST['projectworkid'];
		}
		
		if (! empty ( $name )) {
			$qx_name=$name;
			if(substr($name, -4)=="View"){
				$qx_name = substr($name,0, -4);
			}
			//验证浏览及权限
			if( !isset($_SESSION['a']) ){
				$map=D('User')->getAccessfilter($qx_name,$map);
			}
			//列表页排序 ---开始-----2015-08-06 15:07 write by xyz
				
			if($_REQUEST['orderField']&&strpos(strtolower($_REQUEST['orderField']),' asc')===false&&strpos(strtolower(strpos($_REQUEST['orderField'])),' desc')===false){
				$this->_list ( $name, $map);
			}else{
				$sortsorder = '';
				$sortsmap['modelname'] = $name;
				$sortsmap['sortsorder'] = array("gt",0);
				//管理员读公共设置
				if($_SESSION['a']){
					$listincModel = M("mis_system_public_listinc");
					$sortslist = $listincModel->where($sortsmap)->order("sortsorder")->select();
				}else{
					//个人先读个人设置、没有再读公共设置
					$sortsmap['userid'] = $_SESSION [C ( 'USER_AUTH_KEY' )];
					$listincModel = M("mis_system_private_listinc");
					$sortslist = $listincModel->where($sortsmap)->order("sortsorder")->select();
					if(empty($sortslist)){
						unset($sortsmap['userid']);
						$listincModel = M("mis_system_public_listinc");
						$sortslist = $listincModel->where($sortsmap)->order("sortsorder")->select();
					}
				}
				//如果在设置里有相关数据、提取排序字段组合order by
				if($sortslist){
					foreach($sortslist as $k=>$v){
						$sortsorder .= $v['fieldname'].' '.$v['sortstype'].',';
					}
					$sortsorder = substr($sortsorder,0,-1);
				}
				//列表页排序 ---结束-----
				$this->_list ( $name, $map,'', false,'','',$sortsorder);
				
			}
			
				
		}
		//获取模型权限内列表 ---结束----
		
		//获取模型list配置文件 ----开始----
		//begin
		$scdmodel = D('SystemConfigDetail');
		//读取列名称数据(按照规则，应该在index方法里面)
		$detailList = $scdmodel->getDetail($name,true,'','sortnum');
		if(file_exists(ROOT . '/Dynamicconf/Models/'.$name.'/form.inc.php')){
			$anameList = require ROOT . '/Dynamicconf/Models/'.$name.'/form.inc.php';
			if(!empty($detailList) && !empty($anameList)){
				foreach($detailList as $k => $v){
					$detailList[$k]["datatable"] = 'template_key=""';
					foreach($anameList as $kk => $vv){
						if($k==$kk){
							$detailList[$k]["datatable"] = $vv["datatable"];
						}
					}
				}
			}
		}
		//print_r($detailList);
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		//获取模型list配置文件 ----结束----
		
		//获取已经授权的数据
		$contentarr = array();
		if($_POST['content']){
			$contentarr = explode(",",$_POST['content']);
		}
		$this->assign('content',$content);
		$this->assign('contentarr',$contentarr);
		$this->assign('isall',$_POST['isall']);
		$this->assign('id',$_POST['id']);
		$this->display("detaillist");
	}
	function lookupgetAllList(){
		$name = $_POST['modelname'];
		$map = $this->_search ($name);
		$nameAction = A($name);
		if (method_exists ( $nameAction, '_filter' )) {
			$nameAction->_filter ( $map );
		}
		if($_REQUEST['fieldtype']){
			$this->getBindSetTables($map);
		}
		if($_REQUEST['projectid']){
			$map['projectid'] = $_REQUEST['projectid'];
		}
		if($_REQUEST['projectworkid']){
			//$map['projectworkid'] = $_REQUEST['projectworkid'];
		}
		$str = '';
		if (! empty ( $name )) {
			$qx_name=$name;
			if(substr($name, -4)=="View"){
				$qx_name = substr($name,0, -4);
			}
			//验证浏览及权限
			if( !isset($_SESSION['a']) ){
				$map=D('User')->getAccessfilter($qx_name,$map);
			}
			//列表页排序 ---开始-----2015-08-06 15:07 write by xyz
		
			if($_REQUEST['orderField']&&strpos(strtolower($_REQUEST['orderField']),' asc')===false&&strpos(strtolower(strpos($_REQUEST['orderField'])),' desc')===false){
				$str = $this->_list2 ( $name, $map);
			}else{
				$sortsorder = '';
				$sortsmap['modelname'] = $name;
				$sortsmap['sortsorder'] = array("gt",0);
				//管理员读公共设置
				if($_SESSION['a']){
					$listincModel = M("mis_system_public_listinc");
					$sortslist = $listincModel->where($sortsmap)->order("sortsorder")->select();
				}else{
					//个人先读个人设置、没有再读公共设置
					$sortsmap['userid'] = $_SESSION [C ( 'USER_AUTH_KEY' )];
					$listincModel = M("mis_system_private_listinc");
					$sortslist = $listincModel->where($sortsmap)->order("sortsorder")->select();
					if(empty($sortslist)){
						unset($sortsmap['userid']);
						$listincModel = M("mis_system_public_listinc");
						$sortslist = $listincModel->where($sortsmap)->order("sortsorder")->select();
					}
				}
				//如果在设置里有相关数据、提取排序字段组合order by
				if($sortslist){
					foreach($sortslist as $k=>$v){
						$sortsorder .= $v['fieldname'].' '.$v['sortstype'].',';
					}
					$sortsorder = substr($sortsorder,0,-1);
				}
				//列表页排序 ---结束-----
				$str = $this->_list2 ( $name, $map,'', false,'','',$sortsorder);
			}
		
		}
		exit($str) ;
	}
	public function _list2($name, $map, $sortBy = '', $asc = false,$group='',$echoSql='',$sortstr=''){
		import ( '@.ORG.Browse' );
		//map附加
		//提醒条件
		if($_REQUEST['remindMap']){
			$remindMap=base64_decode($_REQUEST['remindMap']);
			if($map['_string']){
				$map['_string'].=" and ".$remindMap;
			}else{
				$map['_string']=$remindMap;
			}
		}
		//权限验证条件
		if ($_SESSION ['a'] != 1&&$name==$this->getActionName()) {
			$broMap = Browse::getUserMap ( $this->getActionName() );
			//添加商机特殊数据权限过滤
			if($name=="MisSaleMyBusiness"){
				if(!$_POST['isbrows']){
					unset($broMap);
				}
			}
			if ($broMap) {
				if($map['_string']){
					$map['_string'] .= " and " . $broMap;
				}else{
					$map['_string']= $broMap;
				}
			}
		}
		if($_REQUEST['projectid'] && $_REQUEST['projectworkid']){
			$map['projectid']= $_REQUEST['projectid'];
		}
		$model = D($name);
		// 视图对象的排序
		$viewSign = substr ($name, -4 );
		if($viewSign=='View'){
			$viewTables=array_keys($model->viewFields);
			$viewTable=$viewTables[0];
			$viewOrderBy=$model->viewFields[$viewTable][0];
			$order = $viewTable.".".$viewOrderBy;
		}
		//排序字段 默认为主键名
		else if (isset ( $_REQUEST ['orderField'] ) && $_REQUEST ['orderField']) {
			$order = $_REQUEST ['orderField'];
			//$order="`" .$order."`" ;
		} else {
			$order = ! empty ( $sortBy ) ? $sortBy : $model->getPk ();
			//$order="`" .$order."`" ;
		}
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['orderDirection'] )) {
			$sort = $_REQUEST ['orderDirection'];
		} else {
			$sort = $asc ? 'asc' : 'desc';
		}
		//特殊处理 字符串排序参数 write by xyz
		if($sortstr){
			$order = $sortstr;
			$sort = '';
		}
		/* ***************** 修改 ***************** */
		if($_POST['search_flag'] == 1){
			$this->setAdvancedMap($map);
		}
		// '*'
		$count = $model->where ( $map )->count ( '*' );
		//trace($model->getLastSql());
		if($group){
			$count = $model->group($group)->where ( $map )->getField( 'id',true );
			$count = count($count);
		}
		if($echoSql=='count' && $_SESSION['a']==1){
			echo $model->getLastSql();
		}
		//传参开启调式 eagle
		/* ***************** 修改 ***************** */
		//不存在则遍历一遍重新拼装$map来处理视图类型数据
		$str = '';
		if ($count > 0) {
			import ( "@.ORG.Page" );
			//创建分页对象
			//分页查询数据
			
			if($group){
				$voList = $model->group($group)->where($map)->order( $order." ".$sort)->select();
			}else{
				$voList = $model->where($map)->order(  $order." ".$sort)->select();
			}
			
			foreach($voList as $k=>$v){
				$str .= $str?",".$v['id']:$v['id'];
			}
		}
		return $str;
	}
	
}