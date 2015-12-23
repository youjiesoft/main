<?php
/**
 * php异步请求操作类型
 * @Title: AsynchronousrequestAction 
 * @Package package_name
 * @Description: todo(用一句话描述该类的作用) 
 * @author quqiang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015年7月11日 上午11:31:37 
 * @version V1.0
 */
class AsynchronousrequestAction extends Action {
	/**
	 * 开启异步启用
	 * @Title: start
	 * @Description: todo(这里用一句话描述这个方法的作用)
	 *
	 * @param string $parame
	 *        	地址栏参数，必须。默认请求只包含站点域名
	 * @author quqiang
	 *         @date 2015年7月11日 上午11:40:51
	 * @throws
	 *
	 */
	function start($parame) {
		if (! $parame) {
			$parame = 'startclear';
		}
		// 用户参数格式处理
		// $parame = (strpos($parame , '/')===false || strpos($parame , '/') !==0 ) ? '/'.$parame : $parame;
		/*
		 * 开启socket通信
		 * 参数1、HOST_NAME
		 * 参数2、端口
		 * 参数3、参数4、参数5、是socket通信自带的，请阅读PHP socket通信手册
		 */
		$fp = fsockopen ( $_SERVER ['SERVER_NAME'], $_SERVER ['SERVER_PORT'], &$errno, &$errstr, 5 );
		if (! $fp) {
			// socket开启失败。
			$this->error ( "$errstr ($errno)" );
		}
		// 组装报头
		// $out = "GET ".$_SERVER['SCRIPT_NAME']."{$parame} / HTTP/1.1\r\n";
		$out = "GET " . $_SERVER ['SCRIPT_NAME'] . "/Asynchronousrequest/{$parame}  / HTTP/1.1\r\n";
		$out .= "Host: " . $_SERVER ['SERVER_NAME'] . "\r\n";
		$out .= "Connection: Close\r\n\r\n";
		fwrite ( $fp, $out );
		fclose ( $fp );
	}
	
	/**
	 * 收集删除数据
	 * @Title: collecttodata
	 * @Description: todo(收集删除数据)
	 *
	 * @author quqiang
	 *         @date 2015年7月14日 下午2:46:42
	 * @throws
	 *
	 */
	function collecttodata() {
		$basepath = ROOT.'/Dynamicconf/truncate';
		deldir($basepath);
		mk_dir($basepath);
		$obj = M ( 'mis_dynamic_form_manage' );
		$map ['isrecord'] = 0;
		$data = $obj->where ( $map )->select ();
		
		/**
		 * 动态表单删除文件数据收集流程：
		 * 1：action、model、view、actionExtend、modelExtend、模板文件
		 * 2：销毁动态配置下的Models项、Sytem下的listInc
		 * 3：获取所有需要清除的表，主表、子表、内嵌表
		 */
		
		/**
		 * 单文件删除列表
		 *
		 * @var unknown_type
		 */
		$destriyFileList = array ();
		
		/**
		 * 文件夹删除列表
		 *
		 * @var unknown_type
		 */
		$destriyFolderList = array ();
		/**
		 * 要删除的action名称，可以用于node节点删除
		 */
		$actionList = array();
		/**
		 * 要删除的Model名称
		 */
		$modelList=array();
		
		foreach ( $data as $k => $v ) {
			$actionName = $v ['actionname'];
			if($actionName){
				// 1
				// action , actionExtend
				$destriyFileList [] = LIB_PATH . "Action/" . $actionName . "Action.class.php";
				$destriyFileList [] = LIB_PATH . "Action/" . $actionName . "ExtendAction.class.php";
				// Model , ModelExtend , View
				$destriyFileList [] = LIB_PATH . "Model/" . $actionName . "Model.class.php";
				$destriyFileList [] = LIB_PATH . "Model/" . $actionName . "ExtendModel.class.php";
				$destriyFileList [] = LIB_PATH . "Model/" . $actionName . "ViewModel.class.php";
				// 模板文件目录
				$destriyFolderList [] = TMPL_PATH . C ( 'DEFAULT_THEME' ) . '/' . $actionName;
				// 2
				// Models , 组件配置文件
				$dir = '/autoformconfig/';
				$destriyFolderList [] = C ( 'DYNAMIC_PATH' ) . '/Models/' . $actionName;
				$destriyFileList [] = C ( 'DYNAMIC_PATH' ) . '/autoformconfig/' . $actionName . '.php';
				// 要移除的action列表
				$actionList[] = $actionName;
			}
		}
		$modelList = $actionList;
		$model = M();
		// 3
		// 内嵌表名
		$dtNameSql = "SELECT
				CONCAT(mas.`tablename` , '_sub_' , pro.`fieldname` ) AS dtname
			FROM
			  mis_dynamic_form_manage AS ma 
			  LEFT JOIN 
			  `mis_dynamic_database_mas` AS mas
			  ON
			  ma.`id` = mas.`formid`
			  LEFT JOIN mis_dynamic_form_propery AS pro 
			    ON ma.`id` = pro.`formid` 
			WHERE ma.`isrecord` = 0 
			  AND pro.`category` = 'datatable' 
			  ";
		// 实际使用表，按子表、非复用表、复用表 顺序排序
		$needDelteSql="SELECT 
			  mas.tablename
			FROM
			  `mis_dynamic_form_manage` AS ma 
			  LEFT JOIN `mis_dynamic_database_mas` AS mas 
			    ON ma.`id` = mas.`formid` 
			WHERE ma.`isrecord` = 0 
			  AND mas.`tablename` IS NOT NULL 
			ORDER BY mas.`isprimary` ASC,
			  mas.`ischoise` ASC ";
		$needDeleteDatatableData = $model->query($dtNameSql);
		$needDeleteTableData = $model->query($needDelteSql);
		
		
		// 获取当前库中的所有表
		$sql="SHOW TABLES;";
		$allTable = $model->query($sql);
		unset($temp);
		foreach ($allTable as $k=>$v){
			$temp[]=reset($v);
		}
		$allTable=$temp;
		
		
		// 处理数据表删除列表数据
		$needDeleteDatatableDataTemp = $needDeleteDatatableData;
		$needDeleteDatatableData = '';
		foreach ($needDeleteDatatableDataTemp as $k=>$v){
			if(in_array($v['dtname'], $allTable)){
				// 只记录在库的内嵌表
				$destriyFileList [] = LIB_PATH . "Model/" .createRealModelName($v['dtname']) . "Model.class.php";
				$needDeleteDatatableData[]=$v['dtname'];
			}
		}
		
		$needDeleteTableDataTemp = $needDeleteTableData;
		$needDeleteTableData = '';
		foreach ($needDeleteTableDataTemp as $k=>$v){
			if(in_array($v['tablename'], $allTable)){
				// 只记录在库的业务表
				$needDeleteTableData[]=$v['tablename'];
			}
		}
		$needDeleteTableData = array_unique($needDeleteTableData);
		$needDeleteDatatableData = array_unique($needDeleteDatatableData);
		
		file_put_contents($basepath.'/file.log',  join(',', $destriyFileList));
		file_put_contents($basepath.'/folder.log',  join(',', $destriyFolderList));
		file_put_contents($basepath.'/dt.log', join(',', $needDeleteDatatableData));
		file_put_contents($basepath.'/table.log',  join(',', $needDeleteTableData));
		file_put_contents($basepath.'/action.log',  join(chr(13).chr(10), $actionList));
	}
	
	
	function clear(){
		set_time_limit(0);
		$basepath = ROOT.'/Dynamicconf/truncate';
		$clearLogPath = 'clear_dynamic_'.date('Y-m-d' , time());
		// 单个文件
		$file = file_get_contents($basepath.'/file.log');
		// 文件夹
		$folder = file_get_contents($basepath.'/folder.log');
		// 数据表格
		$datatable = file_get_contents($basepath.'/dt.log');
		// 动态建模生成表
		$table = file_get_contents($basepath.'/table.log');
		if($file){
			// 开始删除文件
			foreach (explode(',', $file) as $k=>$v){
				$ret = unlink($v);
				$ret = $ret===false ? '失败':'成功';
				logs('文件删除 ：'.$v .$ret, $clearLogPath , '' , __CLASS__ ,  __FUNCTION__ , __METHOD__);
// 				sleep(1);
				// 百万分之一秒
				//usleep(10000);
				
			}
		}
		if($folder){
			// 开始删除文件夹
			foreach (explode(',', $folder) as $k=>$v){
				$ret = deldir($v);
				$ret = $ret===false ? '失败':'成功';
				logs('删除文件夹 ：'.$v.$ret , $clearLogPath , '' , __CLASS__ ,  __FUNCTION__ , __METHOD__);
			}
		}
		$model = new Model();
		$model->startTrans();
		
		if($datatable){
			// 删除数据表格
			$dropSql="DROP TABLE $datatable";
			$ret = $model->query($dropSql);
			logs('删除表状态 ：'.$ret===false ? '失败':'成功' , $clearLogPath , '' , __CLASS__ ,  __FUNCTION__ , __METHOD__);
			logs('删除表 ：'.$model->getLastSql() , $clearLogPath , '' , __CLASS__ ,  __FUNCTION__ , __METHOD__);
		}
		if($table){
			// 动态建模生成表
			// 删除数据表格
			$dropSql="DROP TABLE $table";
			$ret = $model->query($dropSql);
			logs('删除表状态 ：'.$ret===false ? '失败':'成功' , $clearLogPath , '' , __CLASS__ ,  __FUNCTION__ , __METHOD__);
			logs('删除表 ：'.$model->getLastSql() , $clearLogPath , '' , __CLASS__ ,  __FUNCTION__ , __METHOD__);
		}
		
		// 清空动态建模核心表
		$truncateCoreTable[] = "TRUNCATE TABLE `mis_dynamic_calculate`";
		$truncateCoreTable[] = "TRUNCATE TABLE `mis_dynamic_controll_record`";
		$truncateCoreTable[] = "TRUNCATE TABLE `mis_dynamic_database_mas`";
		$truncateCoreTable[] = "TRUNCATE TABLE `mis_dynamic_database_sub`";
		$truncateCoreTable[] = "TRUNCATE TABLE `mis_dynamic_form_datatable`";
		$truncateCoreTable[] = "TRUNCATE TABLE `mis_dynamic_form_datatable_oprate`";
		$truncateCoreTable[] = "TRUNCATE TABLE `mis_dynamic_form_field`";
		$truncateCoreTable[] = "TRUNCATE TABLE `mis_dynamic_form_indatatable`";
		$truncateCoreTable[] = "TRUNCATE TABLE `mis_dynamic_form_manage`";
		$truncateCoreTable[] = "TRUNCATE TABLE `mis_dynamic_form_propery`";
		$truncateCoreTable[] = "TRUNCATE TABLE `mis_dynamic_form_record`";
		$truncateCoreTable[] = "TRUNCATE TABLE `mis_dynamic_form_template`";
		
		foreach ($truncateCoreTable as $k=>$v){
			$model->execute($v);
			logs('删除表 ：'.$model->getLastSql() , $clearLogPath , '' , __CLASS__ ,  __FUNCTION__ , __METHOD__);
		}
		$model->commit();
		deldir($basepath);
	}
	function startclear() {
		$logsName = 'destriylogs_' . date ( 'Ymd', time () );
		$obj = M ( 'mis_dynamic_form_manage' );
		$map ['isrecord'] = 0;
		// $nodeDel = A('NodeFileDestroy');
		$data = $obj->where ( $map )->select ();
		if (is_array ( $data )) {
			foreach ( $data as $key => $val ) {
				// $nodeDel->destroy($val['actionname']);
				$this->dda ( $val ['actionname'] );
			}
		}
		// 生成结束文件
		logs ( '异步删除结束', $logsName . 'end' );
	}
	public function dda($actionName, $logsName) {
		$logsName = 'destriylogs_' . date ( 'Ymd', time () );
		logs ( "===清除表单 '{$actionName}'========" );
		
		if (! $actionName) {
			$msg = 'Action名称为空';
			throw new NullDataExcetion ( $msg );
		}
		
		// 日志存储位置
		$logsName = $logsName ? $logsName : 'destriylogs_' . date ( 'Ymd' );
		$tableName = '';
		/**
		 * 动态表单删除文件流程：
		 * 1：销毁action、model、view、actionExtend、modelExtend、模板文件
		 * 2：销毁动态配置下的Models项、Sytem下的listInc
		 * 3：动态表单的组件配置文件，动态表单的字段记录信息(MisAutoAnameList.inc.php)，
		 * 4：清DB数据 mis_auto_primary_mas 表 mis_auto_primary_sub
		 * 5：表单(mis_dynamic_form_manage) 及 表单下的字段记录(mis_dynamic_form_field)
		 */
		
		// 检查Action是否存在
		$actionFileName = LIB_PATH . "Action/" . $actionName . "Action.class.php";
		if (! file_exists ( $actionFileName )) {
			logs ( "用户指定删除Action:{$actionName}不存在", $logsName );
		}
		// 得到当前action的表名。
		$curModel = D ( $actionName );
		// 主表名称
		$tableName = $curModel->getTableName ();
		//
		logs ( $tableName, $logsName );
		// 得到主表在表单记录中的Id然后找到所有datatable
		
		/**
		 * 构建删除文件列表
		 * 1.Action , Model , View , ActionExtend , ModelExtend , Template
		 * 2.销毁动态配置
		 */
		
		/**
		 * 单文件删除列表
		 *
		 * @var unknown_type
		 */
		$destriyFileList = array ();
		
		/**
		 * 文件夹删除列表
		 *
		 * @var unknown_type
		 */
		$destriyFolderList = array ();
		// 1
		// action , actionExtend
		$destriyFileList [] = LIB_PATH . "Action/" . $actionName . "Action.class.php";
		$destriyFileList [] = LIB_PATH . "Action/" . $actionName . "ExtendAction.class.php";
		// Model , ModelExtend , View
		$destriyFileList [] = LIB_PATH . "Model/" . $actionName . "Model.class.php";
		$destriyFileList [] = LIB_PATH . "Model/" . $actionName . "ExtendModel.class.php";
		$destriyFileList [] = LIB_PATH . "Model/" . $actionName . "ViewModel.class.php";
		// 模板文件目录
		$destriyFolderList [] = TMPL_PATH . C ( 'DEFAULT_THEME' ) . '/' . $actionName;
		// 2
		// Models , 组件配置文件
		$dir = '/autoformconfig/';
		$destriyFolderList [] = C ( 'DYNAMIC_PATH' ) . '/Models/' . $actionName;
		$destriyFileList [] = C ( 'DYNAMIC_PATH' ) . '/autoformconfig/' . $actionName . '.php';
		/**
		 * 删除表记录
		 * 1.删除模板
		 * 1.删除组件表propery
		 * 2.删除sub表字段记录
		 * 3.删除mas表
		 * 4.内嵌表格删除
		 */
		$formid = getFieldBy ( $actionName, "actionname", "id", "mis_dynamic_form_manage" );
		
		// 删除mas表
		$deltemp = "DELETE FROM mis_dynamic_form_template WHERE formid={$formid}";
		// 删除组件记录表
		$delpropery = "DELETE FROM mis_dynamic_form_propery WHERE formid={$formid}";
		// 查找当前表单主表
		$tablename = getFieldBy ( $formid, "formid", "tablename", "mis_dynamic_database_mas", "isprimary", "1" );
		// 查询表是否被复用
		$ischoise = getFieldBy ( $tablename, "tablename", "tablename", "mis_dynamic_database_mas", "ischoise", "1" );
		// 丢弃表SQL
		$droptable = "";
		if (! $ischoise) {
			// 删除mas表记录
			$delMas = "DELETE FROM mis_dynamic_database_mas WHERE formid={$formid}";
			// 丢弃表
			$droptable = "drop table " . $tablename;
			// 删除sub表记录
			$delSub = "DELETE FROM mis_dynamic_database_sub WHERE formid={$formid}";
		}
		// 4.删除数据表。
		$getDataTableSql = "SELECT
		CONCAT(tablename, '_sub_', `FIELD`) AS datatablename
		FROM
		mis_dynamic_database_sub AS sub,
		(SELECT
		tablename
		FROM
		mis_dynamic_database_mas
		WHERE id IN
		(SELECT
		masid
		FROM
		mis_dynamic_database_sub
		WHERE formid = {$formid}
		AND category = 'datatable')) AS tablename
		WHERE sub.formid = {$formid}
		AND sub.category = 'datatable' ";
		/**
		 * 删除数据表，
		 * 先得到所有数据表格记录
		 * 1。删除字段记录表
		 * 2。删除内嵌表格的表
		 * 3. 删除表单记录
		 * 4. 删除当前使用表
		 */
		//
		
		$delFieldSql = "DELETE FROM mis_dynamic_form_field WHERE formid ={$formid}";
		$delDataTableSql = "";
		$delFormSql = "DELETE FROM mis_dynamic_form_manage WHERE actionname='$actionName'";
		if ($tableName) {
			$delSql = "DELETE FROM $tableName";
		}
		logs ( '单文件删除列表：' . arr2string ( $destriyFileList ), $logsName );
		logs ( '文件件删夹除列表：' . arr2string ( $destriyFolderList ), $logsName );
		logs ( '数据表-字段记录删除：' . $delFieldSql, $logsName );
		logs ( '数据表-内嵌表删除：' . $delDataTableSql, $logsName );
		logs ( '数据表-表单记录删除：' . $delFormSql, $logsName );
		
		/**
		 * 真实删除
		 */
		
		foreach ( $destriyFileList as $k => $v ) {
			$ret = unlink ( $v );
			logs ( "文件 $v 删除" . ($ret ? '成功' : '失败'), $logsName );
		}
		foreach ( $destriyFolderList as $k => $v ) {
			$ret = deldir ( $v );
			logs ( "文件夹 $v 删除" . ($ret ? '成功' : '失败'), $logsName );
		}
		
		$modelObj = M ();
		$fieldDelRet = $modelObj->query ( $delFieldSql );
		logs ( "组件字段记录 $delFieldSql  删除  " . ($fieldDelRet ? '失败' : '成功'), $logsName );
		$formDelRet = $modelObj->query ( $delFormSql );
		logs ( "表单记录  $delFormSql  删除 " . ($formDelRet ? '失败' : '成功'), $logsName );
		if ($delSql) {
			$delRet = $modelObj->query ( $delSql );
			logs ( "表单记录  $delFormSql  删除 " . ($delRet ? '失败' : '成功'), $logsName );
		} else {
			logs ( "表 $tableName  不存在", $logsName );
		}
		// 删除数据表
		$dataTableList = $modelObj->query ( $getDataTableSql );
		logs ( "删除数据表SQL  $getDataTableSql ", $logsName );
		if ($dataTableList) {
			foreach ( $dataTableList as $k => $v ) {
				$delDataTableSql = "DROP TABLE {$v['datatablename']}";
				// 删除数据表
				$tempDelRet = $modelObj->query ( $delDataTableSql );
				logs ( "删除数据表  $delDataTableSql  删除 " . ($tempDelRet ? '失败' : '成功'), $logsName );
			}
		}
		
		// 删除模板
		$tempDelRet = $modelObj->query ( $deltemp );
		logs ( "删除模板  $deltemp  删除 " . ($tempDelRet ? '失败' : '成功'), $logsName );
		// 删除组件
		$properyDelRet = $modelObj->query ( $delpropery );
		logs ( "删除组件  $delpropery  删除 " . ($properyDelRet ? '失败' : '成功'), $logsName );
		// 删除sub表
		$subResult = $modelObj->query ( $delSub );
		logs ( "删除sub表  $delSub  删除 " . ($subResult ? '失败' : '成功'), $logsName );
		// 删除mas表记录
		$masResult = $modelObj->query ( $delMas );
		logs ( "删除mas表  $delMas  删除 " . ($masResult ? '失败' : '成功'), $logsName );
		$modelObj->commit ();
	}
	public function cmdclear(){
		
		// 命令路径
		$cmddir  = ROOT.'/cmd/clear.bat';
		
		exec("$cmddir",$out,$status);
		//exec("$cmddir $filedir",$out , $status);
		//exec("$cmddir $folderdir",$out,$status);
		var_dump($out);
		var_dump($status);
	}
}