<?php
/**
 * @Title: ResumeAction 
 * @Package package_name
 * @Description: todo(数据还原) 
 * @author laicaixia
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-5-31 下午5:25:22 
 * @version V1.0
 */
class ResumeAction extends CommonAction {
	var $handle;  //打开文件操作指针
    /**
     * @Title: _filter 
     * @Description: todo(检索) 
     * @param unknown_type $map  
     * @author laicaixia 
     * @date 2013-5-31 下午5:25:42 
     * @throws 
    */  
    public function _filter(&$map){
		 if ($_SESSION["a"] != 1)$map['status']=array("gt",-1);
    }
    /**
     * @Title: _filter 
     * @Description: todo(下载) 
     * @param unknown_type $map  
     * @author laicaixia 
     * @date 2013-5-31 下午5:25:42 
     * @throws 
    */  
    public function download(){
    	$filename = $_GET['filename'];
        $folder = urldecode($_GET['folder']);
        $folderdir = R("Backup/getBackupDir");
        header("Content-Type: application/force-download");
        header("Content-Disposition: attachment; filename=$filename");
    	readfile($folderdir.'/'.$folder.'/'.$filename);
  	}
	/**
	 * @Title: start_resume 
	 * @Description: todo(开始还原)   
	 * @author laicaixia 
	 * @date 2013-5-31 下午5:26:27 
	 * @throws 
	*/  
	public function start_resume(){
		//实例化数据对象
		$Resume = M('Resume');
		//回滚级别判断
		if(C('RESUME_MODEL')=='Database'){
		//启用数据库事务
		$Resume->query("SET AUTOCOMMIT=0");
        $Resume->query("START TRANSACTION");
		}
        //确定开始执行时间
		$start_time = time();
		//获取还原文件对象
		$file = $_POST['file'];
		$folder = urldecode($_POST['folder']);
		$gbk_folder = @iconv('UTF-8', 'gb2312', $folder);
		$folderdir = R("Backup/getBackupDir");
		$log = $folderdir.'/'.$gbk_folder.'/'.C('BACKUP_LOG_FILE');
		if(file_exists($folderdir.'/'.$gbk_folder.'/'.$file)){
			$type = $_POST['type'];
			$tables = $_POST['tables'];
			$tables = explode(',', $tables);
			$resumeobj = array();
			$temp_sql = '';
			import("@.ORG.Zip");
			$unzip = new SimpleUnzip($folderdir.'/'.$gbk_folder.'/'.$file);
			$unzip->SimpleUnzip();
			$file_count = $unzip->Count();
			if($type == 'all'){
				$tables = array();
				for($i=0;$i<$file_count;$i++){
					$filename = $unzip->GetName($i);
					if(preg_match("/([\w]+)([0-9\-]*)\.sql/", $filename, $out)){
						$tablename = $out[1];
						if($tablename == C('DB_NAME').'_struct'){
							break;
						}
						if($tablename != C('BACKUP_TABLE') && $tablename != C('UPGRADE_TABLE')){
							$tables[] = $tablename;
						}
					}else{
						continue;
					}
				}
			}
			$tables = array_unique($tables);
			$set_file = array(); //分割文件的集合数组
			for($i=0;$i<$file_count;$i++){
				$filename = $unzip->GetName($i);
				if(preg_match("/(\w+)([0-9\-]*)\.sql/", $filename, $out)){
					$tablename = $out[1];
				}else{
					continue;
				}
				if(in_array($tablename, $tables)){
					$temp_sql = $unzip->GetData($i);
					if(preg_match("/(\w+)-([0-9]+)\.sql/", $filename, $out)){
						$set_file[$tablename][$out[2]] = $temp_sql;
					}else{
						$temp_file = $folderdir.'/'.$gbk_folder.'/'.$filename;
						$int = file_put_contents($temp_file, $temp_sql);
						$this->resumeOneTable($temp_file, $log, $tablename, $file);
					}
				}
			}
			foreach($set_file as $tablename => $table_part_sql){
				$tablesql = implode(' ', $table_part_sql);
				$temp_file = $folderdir.'/'.$gbk_folder.'/'.$tablename.'.sql';
				file_put_contents($temp_file, $tablesql);
				$this->resumeOneTable($temp_file, $log, $tablename, $file);
			}
			//回滚级别判断
			if(C('RESUME_MODEL')=='Database'){
			//全部成功后提交事务
			$Resume->query("COMMIT");
			}
			$success = date("Y-m-d H:i:s")." -- 还原成功，所采用的还原文件是{$file}\r\n";
			$f_log = fopen($log, 'a');
	    	fwrite($f_log, $success);
	    	fclose();
			$use_time = time() - $start_time;
			echo "花费{$use_time}秒完成此次还原";
		}else{
			echo "<br/>"."是否准备还原,确认执行请按F5刷新后选择记录还原！谨慎操作！";
		}
	}
  /**
	 * @Title: getNextSql 
	 * @Description: todo(显示) 
	 * @return string|boolean  
	 * @author laicaixia 
	 * @date 2013-5-31 下午5:27:02 
	 * @throws 
	*/
  	public function show(){
  		$filename = $_GET['filename'];
    	$folder = urldecode($_GET['folder']);
    	$gbk_folder = @iconv('UTF-8', 'gb2312', $folder);
    	$folderdir = R("Backup/getBackupDir");
    	$file = $folderdir.'/'.$gbk_folder.'/'.$filename;
	    if(file_exists($file)){
			import("@.ORG.Zip");
			$unzip = new SimpleUnzip($file);
			$unzip->SimpleUnzip();
			$dbname = C('DB_NAME');
			$tables = '';
			$file_count = $unzip->Count();
			for($i=0;$i<$file_count;$i++){
				if($unzip->GetName($i) == C('BACKUP_RECORD_FILE')){
					$tables = $unzip->GetData($i);
					break;
				}
			}
			$tables = explode(',', $tables);
			$html_show = '';
			foreach($tables as $table){
				if($table != C('BACKUP_TABLE') &&  $table != C('UPGRADE_TABLE')){
					$html_show .= "<label style='width:300px;'><input name='table' type='checkbox' value='$table' />$table</label>";
				}
			}
			$this->assign('html_show', $html_show);
			$this->assign('file', $filename);
			$this->assign('folder', $folder);
	    }
	    //还原模式显示
		if(C('RESUME_MODEL')=='Database'){
			$modelInfo="当前还原模式为：数据库级<br/>还原失败所有数据均不会被还原！";
		}
		else if(C('RESUME_MODEL')=='Table'){
			$modelInfo="当前还原模式为：数据表级<br/>还原失败只有出错表数据不还原！";
		}
		$this->assign('modelInfo', $modelInfo);
  		$this->display();
  	}
	/**
	 * @Title: getNextSql 
	 * @Description: todo(从文件中逐条取sql) 
	 * @return string|boolean  
	 * @author laicaixia 
	 * @date 2013-5-31 下午5:27:02 
	 * @throws 
	*/  
      private function getNextSql(){
	    $sql="";
	    while ($line = @fgets($this->handle, 40960)) {
	        $line = trim($line);
	        //以下三句在高版本php中不需要，在部分低版本中也许需要修改
	        //$line = str_replace("\\","\",$line);
	        //$line = str_replace("'","'",$line);
	        //$line = str_replace("\r\n",chr(13).chr(10),$line);
	        //$line = stripcslashes($line);
	        if (strlen($line)>1) {
	            if ($line[0]=="-" && $line[1]=="-") {
	                continue;
	            }
	        }
	        $sql.=$line.chr(13).chr(10);
	        if (strlen($line)>0){
	            if ($line[strlen($line)-1]==";"){
	                break;
	            }
	        }
	    }
	    if(trim($sql) != '')
	    	return trim($sql);
	    else
	    	return false;
	}
	/**
	 * @Title: resumeOneTable 
	 * @Description: todo(单表还原) 
	 * @param unknown_type $temp_file
	 * @param unknown_type $log
	 * @param unknown_type $tablename
	 * @param unknown_type $file  
	 * @author laicaixia 
	 * @date 2013-5-31 下午5:27:55 
	 * @throws 
	*/  
	public function resumeOneTable($temp_file, $log, $tablename, $file){
		  //实例化数据对象
		  $Resume = M('Resume');
		  //回滚级别判断
		  if(C('RESUME_MODEL')=='Table'){
           $Resume->query("SET AUTOCOMMIT=0");
           $Resume->query("START TRANSACTION");
		  }
		/* 事务 */
		/*
		mysql_query("SET AUTOCOMMIT=0");
		mysql_query("START TRANSACTION");
		$delete = "DELETE FROM `$tablename`";
		*/
		while($Resume->query("DELETE FROM `$tablename`") === false){
			$error = mysql_error();
			preg_match("/`".C('DB_NAME')."`\.`(.*)`[\s\S]+CONSTRAINT `(.*)` FOREIGN/", $error, $out);
			$key_table = $out[1];
			$key_name = $out[2];
			//mysql_query("ALTER TABLE `$key_table` DROP FOREIGN KEY ".$key_name);
			$Resume->query("ALTER TABLE `$key_table` DROP FOREIGN KEY ".$key_name);
		}
		$this->handle = fopen($temp_file, 'r');
		while($sql = $this->getNextSql()){
			if(substr($sql, 0, 6) == 'CREATE'){
				continue;
			}elseif($Resume->query($sql) === false){
				$msg = date("Y-m-d H:i:s")." -- 还原失败，所采用的还原文件是{$file}，在还原表{$tablename}时出错，具体信息如下：\r\n";
				$msg .= mysql_errno()." : ".mysql_error()."\r\n";
				$f_log = fopen($log, 'a');
		    	fwrite($f_log, $msg);
		    	fclose();
		    	//遇到失败时即刻回滚
				$Resume->query("ROLLBACK");
				if(C('RESUME_MODEL')=='Database'){
					$modelInfo="当前还原模式为：数据库级；还原失败导致所有数据均未被还原！";
				}
				else if(C('RESUME_MODEL')=='Table'){
					$modelInfo="当前还原模式为：数据表级；还原失败导致出错表数据未被还原！";
				}
				$info="还原失败，原因已写入日志，请联系管理员查看!<br/>".$modelInfo;
				echo $info;
				@unlink($temp_file);
				exit;
			}
		}
		//回滚级别判断
		if(C('RESUME_MODEL')=='Table'){
 		$Resume->query("COMMIT");
		}
		fclose($this->handle);
		@unlink($temp_file);
	}
}
?>