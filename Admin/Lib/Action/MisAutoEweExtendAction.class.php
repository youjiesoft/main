<?php
/**
 * @Title: MisAutoEweAction
 * @Package package_name
 * @Description: todo(动态表单_扩展类。本类为用户代码注入入口，系统一旦生成将不再重复生成。 * 						但当用户选为组合表单方案后会更新该文件，请做好备份)
 * @author 管理员
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2015-07-20 14:55:22
 * @version V1.0
*/
class MisAutoEweExtendAction extends CommonAction {
	private $unit = array();
	public function _extend_filter(&$map){
		
		$_REQUEST['type']=$_REQUEST['type']?$_REQUEST['type']:0;
		$_REQUEST['danjumingchen']=$_REQUEST['danjumingchen']?$_REQUEST['danjumingchen']:0;
		if($_REQUEST['jump']){
			if($_REQUEST['type']){
				$map['type'] = $_REQUEST['type'];
			}
			if($_REQUEST['danjumingchen']){
				$map['danjumingchen'] = $_REQUEST['danjumingchen'];
			}
		}
	}
	/**
	 * @Title: _extend_before_index
	 * @Description: todo(扩展前置index函数)
	 * @author 管理员
	 * @date 2015-07-20 14:55:22
	 * @throws 
	*/
	function _extend_before_index() {
		//查询绑定数据源
		$this->getDateSoure();
		$model = D($this->getActionName());
		$maslist = $model->where("status=1")->group("type,danjumingchen")->select();
		$newlist = $maslist;
		
		//$check = $_REQUEST['id'];
		foreach($newlist as $key=>$val){
			$newlist[$key]['parentid'] = -$val['type'];
			$newlist[$key]['name'] = $val['danjumingchenchina'];
			$newlist[$key]['title'] = $val['danjumingchenchina'];
		}
		//组装左边树
		$param['rel']="MisAutoEweBox";
		$param['url']="__URL__/index/jump/jump/type/#type#/danjumingchen/#danjumingchen#/id/#id#";
		$treemiso[]=array(
				'id'=>-1,
				'pId'=>0,
				'name'=>'单据提醒',
				'title'=>'单据提醒',
				'type'=>'post',
				'open'=>false,
				'isParent'=>true,
				'rel'=>"MisAutoEweBox",
				'url'=>"__URL__/index/jump/jump/type/1",
				'target'=>'ajax'
		);
		$treemiso[]=array(
				'id'=>-2,
				'pId'=>0,
				'name'=>'定时任务',
				'title'=>'定时任务',
				'type'=>'post',
				'open'=>false,
				'isParent'=>false,
				'rel'=>"MisAutoEweBox",
				'url'=>"__URL__/index/jump/jump/type/2",
				'target'=>'ajax'
		);
		$treearr = $this->getTree($newlist,$param,$treemiso,false);
		//print_r(json_decode($treearr,true));
		$this->assign("check",$check);
		$this->assign("requestval",$_REQUEST);
		//print_r($_REQUEST);
		$this->assign("returnarr",$treearr);
		
	}
	
	/**
	 * @Title: _extend_before_edit
	 * @Description: todo(扩展的前置编辑函数)
	 * @author 管理员
	 * @date 2015-07-20 14:55:22
	 * @throws 
	*/
	function _extend_before_edit(){
	}
	/**
	 * @Title: _extend_before_insert
	 * @Description: todo(扩展的前置添加函数)
	 * @author 管理员
	 * @date 2015-07-20 14:55:22
	 * @throws 
	*/
	function _extend_before_insert(){
		//对提醒对象数据进行解析
		$_POST['userid'] = implode(",",$_POST['recipient']);
		$userinfo = array();
		foreach($_POST['recipient'] as $k=>$v){
			$usertemp['name'] = $v;
			$usertemp['title'] = $_POST['recipientname'][$k];
			$userinfo[] = $usertemp;
		}
		$_POST['userinfo'] = json_encode($userinfo);
		//触发动作
		$_POST['operation'] = implode(",",$_POST['operation']);
		//条件
		$_POST['tixingtiaojian'] = $_POST['tixingtiaojian']?str_replace("&#39;", "'", html_entity_decode($_POST['tixingtiaojian'])):'';
		//内容
		$_POST['tixingnarong'] = $_POST['tixingnarong']?str_replace("&#39;", "'", html_entity_decode($_POST['tixingnarong'])):'';
		//通知类型
		$_POST['noticetype'] = implode(",",$_POST['noticetype']);
		//单据名称
		$_POST['danjumingchen'] = getFieldBy($_POST['danjumingchen'],"id","name","node");
		$_POST['danjumingchenchina'] = getFieldBy($_POST['danjumingchen'],"name","title","node");
		//print_r($_POST);exit;
		//通知字段
		$_POST['checkfields'] = implode(",",$_POST['checkfield']);
		//单据表单
		$tables = $this->tablesOfModel($_POST['danjumingchen']);
		$_POST['danjutablechina'] = $tables[$_POST['danjutable']];
		
	}
	/**
	 * @Title: _extend_before_update
	 * @Description: todo(扩展前置修改函数)  
	 * @author 管理员
	 * @date 2015-07-20 14:55:22
	 * @throws
	*/
	function _extend_before_update(){
		//对提醒对象数据进行解析
		$_POST['userid'] = implode(",",$_POST['recipient']);
		$userinfo = array();
		foreach($_POST['recipient'] as $k=>$v){
			$usertemp['name'] = $v;
			$usertemp['title'] = $_POST['recipientname'][$k];
			$userinfo[] = $usertemp;
		}
		$_POST['userinfo'] = json_encode($userinfo);
		//触发动作
		$_POST['operation'] = implode(",",$_POST['operation']);
		//条件
		$_POST['tixingtiaojian'] = $_POST['tixingtiaojian']?str_replace("&#39;", "'", html_entity_decode($_POST['tixingtiaojian'])):'';
		//内容
		$_POST['tixingnarong'] = $_POST['tixingnarong']?str_replace("&#39;", "'", html_entity_decode($_POST['tixingnarong'])):'';
		//通知类型
		$_POST['noticetype'] = implode(",",$_POST['noticetype']);
		//通知字段
		$_POST['checkfields'] = implode(",",$_POST['checkfield']);
		//单据名称
		//$_POST['danjumingchen'] = getFieldBy($_POST['danjumingchen'],"id","name","node");
		//$_POST['danjumingchenchina'] = getFieldBy($_POST['danjumingchen'],"id","title","node");
		//print_r($_POST);exit;
	}
	/**
	 * @Title: _extend_after_edit
	 * @Description: todo(扩展后置编辑函数)
	 * @author 管理员
	 * @date 2015-07-20 14:55:22
	 * @throws 
	*/
	function _extend_after_edit(&$vo){		
			//对提醒对象数据进行解析
			
			$vo['userinfo'] = json_decode($vo['userinfo'],true);
			
			//触发动作
			$vo['operation'] = explode(",",$vo['operation']);
			//条件
			//内容
			//通知类型
			$vo['noticetype'] = explode(",",$vo['noticetype']);
			$fields = explode(",",$vo["checkfields"]);
			$fieldsarr = $this->ziduanduixuang($vo['danjumingchen'],$fields);
			$this->assign('fieldsarr',$fieldsarr);
			//单据名称
			//$vo['danjumingchen'] = getFieldBy($vo["danjumingchen"],"name","id","node");
			$tables = $this->tablesOfModel($vo['danjumingchen']);
			$table = $tables[$vo['danjutable']];
			$this->assign('table',$table);
			
	}
	/**
	 * @Title: _extend_after_list
	 * @Description: todo(扩展前置List)
	 * @author 管理员
	 * @date 2015-07-20 14:55:22
	 * @throws 
	*/
	function _extend_after_list(){
	}
	/**
	 * @Title: _extend_after_insert
	 * @Description: todo(扩展后置insert函数)  
	 * @author 管理员
	 * @date 2015-07-20 14:55:22
	 * @throws
	*/
	function _extend_after_insert($id){
		$taskdir = str_replace('Admin','systask/Admin' , getcwd());
		$model = D($this->getActionName());
		$map['status'] = 1;
		$map['type'] = 2;
		$list = $model->where($map)->select();
		$str = '';
		$huanjin = strtoupper(PHP_OS);
		$path1 = '/www/wdlinux/php/bin/php';
		$mode = '-f';
		$path2 = $taskdir.'/index.php /Index/index/masid';
		foreach($list as $key=>$val){
			$intercycle = '';
			switch ($val['intercycleulit']){
				case 'm':
					$intercycle = '*/'.$val["intercycle"].' * * * *';
					break;
				case 'h':
					$intercycle = '0 */'.$val["intercycle"].' * * *';
					break;
				case 'd':
					$intercycle = '0 0 */'.$val["intercycle"].' * *';
					break;
				case 'mon':
					$intercycle = '0 0 1 */'.$val["intercycle"].' *';
					break;
				case 'w':
					$intercycle = '0 0 * * * /'.$val["intercycle"];
					break;
			}
			$str .= $intercycle.' '.$path1.' '.$mode.' '.$path2.'/'.$val['id'].'  >> /temp/task5.log'.chr(13).chr(10);
		}
		$path3 = dirname(ROOT).'/crons';
		if(!is_dir($path3)){
			mkdir($path3,0757);
		}
		$file = $path3."/perminit.cron";
		if($huanjin == 'LINUX'){
			file_put_contents($file,$str);
			$this->regtask($path3);
		}elseif($huanjin == 'WINNT'){
			file_put_contents($file,$str);
		}
	}
	//$taskdir = str_replace('Admin','systask/Admin' , getcwd());
	//LINUX */1 * * * * D:/www/wdlinux/php/bin/php -f /www/web/default/systask /Admin/index.php /Index/index/masid/5  >> /temp/task5.log 
	//WIDOW ..???
	/**
	 * @Title: _extend_before_add
	 * @Description: todo(扩展前置add函数)  
	 * @author 管理员
	 * @date 2015-07-20 14:55:22
	 * @throws
	*/
	function _extend_before_add($vo){
		//
		
		//$model = D('SystemConfigNumber');
		//$returnarr = $model->getRoleTree('LookupobjBox','__URL__/index',array(),false,'ajax');
		//$nodelist = json_decode($returnarr,true);
		$models = D($this->getActionName());
		$nodelist = $models->getNodeArr();	
		
		$this->assign("nodelist",$nodelist);
		if(!$_REQUEST['type']){
			$this->error("请先选择任务类型！");
		}
		$this->assign("type",$_REQUEST['type']);
		if($_REQUEST['danjumingchen']){
			$this->assign("modelid",getFieldBy($_REQUEST['danjumingchen'],"name","id","node"));
			$this->assign("danjumingchen",$_REQUEST['danjumingchen']);
		}
		//print_r($nodelist);
		$this->getFormIndexLoad($vo);
	}
	/**
	 * @Title: _extend_after_update
	 * @Description: todo(扩展后置update函数)  
	 * @author 管理员
	 * @date 2015-07-20 14:55:22
	 * @throws
	*/
	function _extend_after_update(){
		$taskdir = str_replace('Admin','systask/Admin' , getcwd());
		$model = D($this->getActionName());
		$map['status'] = 1;
		$map['type'] = 2;
		$list = $model->where($map)->select();
		$str = '';
		$huanjin = strtoupper(PHP_OS);
		$path1 = '/www/wdlinux/php/bin/php';
		$mode = '-f';
		$path2 = $taskdir.'/index.php /Index/index/masid';
		foreach($list as $key=>$val){
			$intercycle = '';
			switch ($val['intercycleulit']){
				case 'm':
					$intercycle = '*/'.$val["intercycle"].' * * * *';
					break;
				case 'h':
					$intercycle = '0 */'.$val["intercycle"].' * * *';
					break;
				case 'd':
					$intercycle = '0 0 */'.$val["intercycle"].' * *';
					break;
				case 'mon':
					$intercycle = '0 0 1 */'.$val["intercycle"].' *';
					break;
				case 'w':
					$intercycle = '0 0 * * * /'.$val["intercycle"];
					break;
			}
			$str .= $intercycle.' '.$path1.' '.$mode.' '.$path2.'/'.$val['id'].'  >> /temp/task5.log '.chr(13).chr(10);
		}
		$path3 = dirname(ROOT).'/crons';
		if(!is_dir($path3)){
			mkdir($path3,757);
		}
		$file = $path3."/perminit.cron";
		if($huanjin == 'LINUX'){
			file_put_contents($file,$str);
			$this->regtask($path3);
		}elseif($huanjin == 'WINNT'){
			file_put_contents($file,$str);
		}
	}
	/**
	 * @Title: msginfo
	 * @Description: todo(ajax处理条件插件) 
	 * @param unknown_type $inputname  
	 * @author 谢友志 
	 * @date 2015-7-24 上午9:59:17 
	 * @throws
	 */
	public function msginfo($inputname){
		$inputname = $inputname?$inputname:$_REQUEST['inputname'];
		$model = getFieldBy($_POST['modelid'],'id','name','node');
		$table = $_POST['table'];
		$tables = $this->lookuptablelist($model);
		$tablearr = array_keys($tables);
		//print_r($tablearr);
		echo W('ShowSqlResult', array('inputname'=>$inputname,'model'=>$model,'table'=>array($model=>array($table))) );
	}
	/**
	 * @Title: ziduanduixuang
	 * @Description: todo(通知字段) 
	 * @param unknown_type $modelname
	 * @param unknown_type $checkfieldarr
	 * @return Ambigous <string, unknown>  
	 * @author 谢友志 
	 * @date 2015-9-10 上午10:29:02 
	 * @throws
	 */
	public function ziduanduixuang($modelname='',$checkfieldarr=array()){
		$modelname = $modelname?$modelname:getFieldBy($_POST['modelid'],'id','name','node');
		$tablename = $tablename?$tablename:$_POST['table'];
		if($modelname == 'MisSalesMyProject') $modelname = 'MisAutoPvb';
		$newfields = array();
		$syscModel = D("SystemConfigDetail");
		$sysclist = $syscModel->getDetail($modelname ,'','','','');
		//print_r($sysclist);
		foreach($sysclist as $key=>$val){
			if(strtolower($val['table']) == 'user'){
				$newfields[$key]['title'] = $val['showname'];
				$newfields[$key]['name'] = $val['name'];
				//if($tablename== $oldtablename && in_array($key,$checkfieldarr)){
				if(in_array($val['name'],$checkfieldarr)){
					$newfields[$key]['checked'] = 'checked="checked"';
				}else{
					$newfields[$key]['checked'] = '';
				}
			}
			
		}
		
// 		$dycModel = D("Dynamicconf");
// 		$fields = $dycModel->getTableInfo($tablename);
// 		$newfields = array();
// 		foreach($fields as $key=>$val){
// 			$newfields[$key]['title'] = $val['COLUMN_COMMENT']?$val['COLUMN_COMMENT']:$key;
// 			$newfields[$key]['name'] = $key;
// 			//if($tablename== $oldtablename && in_array($key,$checkfieldarr)){
// 			if(in_array($key,$checkfieldarr)){
// 				$newfields[$key]['checked'] = 'checked="checked"';
// 			}else{
// 				$newfields[$key]['checked'] = '';
// 			}
// 		}
		if($_POST['table']){
			echo json_encode($newfields);
		}else{
			return $newfields;
		}
		
	}
	/**
	 *
	 * @Title: lookuptablelist
	 * @Description: todo(查找模型所有表)//改为只要主表
	 * @author renling
	 * @date 2014年12月29日 下午4:29:12
	 * @throws
	 */
	private function lookuptablelist($modelName){
		$formid=getFieldBy($modelName, "actionname", "id", "mis_dynamic_form_manage");
		$MisDynamicDatabaseMasModel=M("mis_dynamic_database_mas");
		//只要主表 and isprimary=1
		//$MisDynamicDatabaseMasList=$MisDynamicDatabaseMasModel->where("status=1 and isprimary=1 and formid=".$formid)->getField("tablename,tabletitle");
		$tablename = D($modelName)->getTableName();
		$MisDynamicDatabaseMasList[$tablename] = $tablename;
		
// 		//查询是否存在内嵌表
// 		$sql="SELECT CONCAT(m.tablename,'_sub_',p.fieldname) AS datatablename ,IF( p.title <> '' OR p.title <> NULL , p.title , p.fieldname) AS title   FROM `mis_dynamic_form_propery` AS p LEFT JOIN mis_dynamic_database_mas AS m ON p.formid=m.formid  WHERE p.formid={$formid} AND p.category = 'datatable' AND m.`isprimary` = 1";
// 		$MisDatabaseList=$MisDynamicDatabaseMasModel->query($sql);
		
		
// 		if($MisDatabaseList){
// 			//存在内嵌表
// 			foreach ($MisDatabaseList as $dkey=>$dval){
// 				$MisDynamicDatabaseMasList[$dval['datatablename']]=$dval['title'];
// 			}
// 		}
		
		
		return $MisDynamicDatabaseMasList;
	}
	/**
	 * @Title: tablesOfModel
	 * @Description: todo(查找模型所有表过渡页面)   
	 * @author 谢友志 
	 * @date 2015-7-24 上午9:59:50 
	 * @throws
	 */
	public function tablesOfModel($model){
		$model = $model?$model:getFieldBy($_POST['modelid'],'id','name','node');
		$tables = $this->lookuptablelist($model);
		if($_POST['modelid']){
			echo json_encode($tables);
		}else{
			return $tables;
		}
		
	}

	function regtask($path){
		// 注册定时任务
		$ret = exec('crontab -r');
		// 在注册任务前先将当前用户现有任务清空
		if($path){
			$cmddir=$path.'/perminit.cron';
		}else{
			$dir= getcwd();
			$cmddir=$dir.'/crons/perminit.cron';
		}
		
		
		$ret = exec("sudo crontab $cmddir", $arr , $flag);
		$msg = array('status'=>0,'msg'=>$arr);
		if($ret){
			$msg['status']=1;
		}
		return $msg;
	}
}
?>