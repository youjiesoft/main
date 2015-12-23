<?php
/**
 * @Title: BackupAction 
 * @Package package_name
 * @Description: todo(数据库备份) 
 * @author laicaixia
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-5-31 下午5:11:02 
 * @version V1.0
 */
class BackupAction extends CommonAction {
	private $temp_sql;  //临时SQL语句
	private $count; //sql文件数量
	private $zip; //zip对象
	private $time; //备份的时间
	private $dbname; //数据库名称
	private $folderdir; //备份文件夹
	private $zipfile; //最终生成的zip文件
	private $end; //文件分割末尾判断
	/**
	 * @Title: index 
	 * @Description: todo(进入首页)   
	 * @author laicaixia 
	 * @date 2013-5-31 下午5:14:16 
	 * @throws 
	*/  
	public function index(){
		$this->display();
	}
	/**
	 * @Title: start_backup 
	 * @Description: todo(开始备份)   
	 * @author laicaixia 
	 * @date 2013-5-31 下午5:14:16 
	 * @throws 
	*/  
	public function start_backup(){
		ignore_user_abort();
		import("@.ORG.Zip");
		$this->zip = new zipfile();
		$this->time = time();
		$this->dbname = C('DB_NAME');
		$this->folderdir = $this->getBackupDir();
		$this->makeDirExists();
		$this->end = '-- '.md5('- backup --*-**- END -**-*-- backup-'.$this->time);
		$tables = $_POST['tables'];
		$type = $_POST['type'];
		$folder = @iconv('UTF-8', 'gb2312', $_POST['folder']);
		$sizelimit = intval($_POST['sizelimit']);
		if(!is_numeric($sizelimit)){
			$sizelimit = 2048;  //KB
		}
		$sizelimit *= 1000;
		if(trim($folder) != ''){
			$this->folderdir = $this->folderdir.'/'.$folder;
			$folder = trim($_POST['folder']);
		}else{
			$folder = date("Ymd", $this->time);
			$this->folderdir = $this->folderdir.'/'.$folder;
		}
		$this->makeDirExists();
		$this->zipfile = $this->folderdir.'/'.$this->dbname.'_'.date("Y-m-d_H-i-s", $this->time).'.zip';
		$basename = basename($this->zipfile);
		if($tables == 'all'){
			$allTables = $this->get_all_tables();
			//print_r($allTables);
		}else{
			$allTables = explode(',', $tables);
		}
		if($type == 'all'){
			foreach($allTables as $tablename){
				$this->getFileHeader();
				$this->get_sql_struct($tablename);
				$this->get_sql_data($tablename);
				$sqlfile = $this->folderdir.'/'.$tablename.'.sql';
				$this->temp_sql .= $this->end;
				file_put_contents($sqlfile, $this->temp_sql);
				if(filesize($sqlfile) < $sizelimit){
					$this->makeFile($this->temp_sql, $tablename);
				}else{
					$this->count = 0;
					$this->splitSqlFile($sqlfile, $tablename, $sizelimit);
				}
				@unlink($sqlfile);
			}
		}else{
			$this->getFileHeader();
			foreach($allTables as $tablename){
				$this->get_sql_struct($tablename);
			}
			$this->makeFile($this->temp_sql, $this->dbname.'_struct');
		}
		$use_time = time() - $this->time;
		$log = $this->folderdir.'/'.C('BACKUP_LOG_FILE');
		$notic = date("Y-m-d H:i:s", $this->time);
		if(file_exists($this->zipfile)){
			$notic .= " -- 备份完成，备份文件名为{$basename}\r\n";
			$dao = D('Resume');
	    	$data['uid'] = $_SESSION[C('USER_AUTH_KEY')];
	    	$data['name'] = $basename;
	    	$data['remark'] = $_POST['remark'];
	    	$data['folder'] = $folder;
	    	$list=$dao->add($data);
	    	if($list!==false){
	    		$dao->commit();
	    		echo "花费{$use_time}秒备份完成，备份文件名为 <font color='red'>{$basename}</font>";
	    	}
	    	else{
	    		$dao->rollback();
	    		echo "数据插入失败!";
	    	}
		}else{
			$notic .= " -- 备份失败，请联系管理员\r\n";
			echo $notic;
			//echo "备份失败，请联系管理员";
		}
		$record = implode(',', $allTables);
		$this->makeFile($record, C('BACKUP_RECORD_FILE'));
    	$f_log = fopen($log, 'a');
    	fwrite($f_log, $notic);
    	fclose();
	}

	/**
	 * @Title: get_all_tables 
	 * @Description: todo(获取所有表信息) 
	 * @return multitype:unknown   
	 * @author laicaixia 
	 * @date 2013-5-31 下午5:15:06 
	 * @throws 
	*/  
	private function get_all_tables(){
		$this->dbname = C('DB_NAME');
		$listsql = "SHOW TABLES FROM `{$this->dbname}`";  //显示数据库的表
		//print_r($listsql);
		//exit;
		$Backup = M('Backup');
        $db_data = $Backup->query($listsql);
        $tables = array();
        foreach($db_data as $key=>$table_list){
        	 foreach($table_list as $key=>$value){
        	 	$tables[] = $value;
        	 }
        }
		return $tables;
	}

	/**
	 * @Title: show_tables 
	 * @Description: todo(显示所有表信息)   
	 * @author laicaixia 
	 * @date 2013-5-31 下午5:17:59 
	 * @throws 
	*/  
	public function show_tables(){
		echo json_encode($this->get_all_tables());
	}

	/**
	 * @Title: getBackupDir 
	 * @Description: todo(获取备份路径) 
	 * @return Ambigous <void, NULL, multitype:>  
	 * @author laicaixia 
	 * @date 2013-5-31 下午5:18:04 
	 * @throws 
	*/  
	private function getBackupDir(){
		return C('BACKUP_PATH');
	}

	/**
	 * @Title: makeDirExists 
	 * @Description: todo(新建路径)   
	 * @author laicaixia 
	 * @date 2013-5-31 下午5:18:07 
	 * @throws 
	*/  
	private function makeDirExists(){
		if(!is_dir($this->folderdir)){
			mkdir($this->folderdir, 0777);
		}
	}

	/**
	 * @Title: getFileHeader 
	 * @Description: todo(获取文件头)   
	 * @author laicaixia 
	 * @date 2013-5-31 下午5:18:33 
	 * @throws 
	*/  
	private function getFileHeader(){
		$this->temp_sql = "-- 主机: ".C('DB_HOST')." \n";
	    $this->temp_sql .= "-- 生成日期: ".date("Y 年 m 月 d 日 H:i:s", $this->time)." \n";
	    $this->temp_sql .= "-- 服务器版本: ".mysql_get_server_info()." \n";
	    $this->temp_sql .= "-- PHP 版本: ".phpversion()." \n\n";
	    $this->temp_sql .= "SET SQL_MODE=\"NO_AUTO_VALUE_ON_ZERO\";\n\n";
	}
 	/**
 	 * @Title: get_sql_struct 
 	 * @Description: todo(获取表结构) 
 	 * @param unknown_type $tablename  
 	 * @author laicaixia 
 	 * @date 2013-5-31 下午5:19:53 
 	 * @throws 
 	*/  
 	private function get_sql_struct($tablename){
		$this->temp_sql .= "--\n-- 表的结构 `$tablename`\n--\n\n";  //表结构提示
		$sql         = "SHOW CREATE TABLE `$tablename`";        //查询创建表时的语句
		//虚拟化一个模型对象
		$model=M();
		$table_creat = $model->query($sql);
		//$table_creat = mysql_fetch_row($table_creat);
		$table_creat = preg_replace("/,\n  CONSTRAINT([\s\S]*)\)\n\)/", "\n)", $table_creat[0]['Create Table']);   //去除外键关联
		$table_creat = str_replace("CREATE TABLE", "CREATE TABLE IF NOT EXISTS", $table_creat);
		$this->temp_sql .= $table_creat.";\n\n";           //返回表结构
 	}
	/**
	 * @Title: get_sql_data 
	 * @Description: todo(获取表数据) 
	 * @param unknown_type $tablename  
	 * @author laicaixia 
	 * @date 2013-5-31 下午5:20:03 
	 * @throws 
	*/  
	private function get_sql_data($tablename){
		$this->temp_sql  .= "--\n-- 转存表中的数据 `$tablename`\n--\n\n";
		//虚拟化一个模型对象
		$model=M($tablename);
		$table_data   = $model->query("SELECT * FROM `$tablename`");
		//$table_data   = mysql_query("SELECT * FROM `$tablename`");
        //获取表数据记录条数
		$count=$model->count();
		//$count        = mysql_num_rows($table_data);
		$is_have_data = $count !== 0 ;
		$field_info=$model->getDbFields();
		$field_num=count($field_info[_type]);
        foreach($field_info[_type] as $key=>$value){
        	$table_fields.="`".$key."`,";
        }
        $table_fields=substr($table_fields,0,-1);
		//防止一次性插入太多数据导致程序挂掉，每条insert语句最多插入200条记录
		if($count % 200 == 0){
			$count_insert = intval($count / 200);
		}else{
			$count_insert = intval($count / 200) + 1;
		}
		for($j=0;$j<$count_insert;$j++){
			$start = $j * 200;
			$insert_data   = $model->query("SELECT * FROM `$tablename` LIMIT $start,200");
			$arr_datas    = array();
			if($is_have_data)  $this->temp_sql  .= "INSERT INTO `$tablename`($table_fields) VALUES \n";  //生成SQL表数据
			foreach($insert_data as $key=>$out_data){                         //循环取出表数据
				$arr_fields = array();
				foreach($out_data as $key=>$value){
					$con = $value;
					$con = str_replace("'", "''", $con);
					$con = str_replace("\\", "\\\\", $con);
					$con = str_replace("\r\n", '\n', $con);
					$con = str_replace("\n", '\n', $con);
					$arr_fields[] = "'".$con."'"; //非数字类型的数据加上引号
				}
				$arr_datas[] = '('.implode(',',$arr_fields).')';
			}
			$this->temp_sql .= implode(",\n",$arr_datas);
			if($is_have_data) $this->temp_sql .= ";\n";
		}
		$this->temp_sql .= "\n\n";
		$this->temp_sql .= "-- --------------------------------------------------------\n\n";  //返回表数据
	}
	/**
	 * @Title: splitSqlFile 
	 * @Description: todo(这里用一句话描述这个方法的作用) 
	 * @param unknown_type $sqlfile
	 * @param unknown_type $tablename
	 * @param unknown_type $sizelimit  
	 * @author laicaixia 
	 * @date 2013-5-31 下午5:20:45 
	 * @throws 
	*/  
	private function splitSqlFile($sqlfile, $tablename, $sizelimit){
		$handle = fopen($sqlfile, 'r');
		while (!feof($handle)) {
		  	$file_content = fread($handle, $sizelimit);
		  	if(!preg_match("/".$this->end."$/", $file_content)){ //判断是否到文件结尾
			  	$rev_content = strrev($file_content);
				$new_content = preg_replace("/^([\s\S]*)\n/U", '', $rev_content);
				$new_content = strrev($new_content);
				$pos_n = strpos($rev_content, "\n");
				fseek($handle, - $pos_n, SEEK_CUR); //重置文件指针到上一次位置的行末
		  	}else{
				$new_content = $file_content;
		  	}
		  	$this->makeFile($new_content, $tablename, true);
		}
		fclose($handle);
	}
	/** 
	 * @Title: makeFile 
	 * @Description: todo(执行文件) 
	 * @param unknown_type $file_content
	 * @param unknown_type $filename
	 * @param unknown_type $isSplitFile  
	 * @author laicaixia 
	 * @date 2013-5-31 下午5:21:01 
	 * @throws 
	*/  
	private function makeFile($file_content, $filename, $isSplitFile = false){
		if($isSplitFile){
			$this->count++;
			$this->zip->addFile($file_content, $filename.'-'.$this->count.'.sql');
		}elseif($filename == C('BACKUP_RECORD_FILE')){
			$this->zip->addFile($file_content, $filename);
		}else{
			$this->zip->addFile($file_content, $filename.'.sql');
		}
		$fp = fopen($this->zipfile, 'w');
		@fwrite($fp, $this->zip->file());
		fclose($fp);
	}
	/**
	 * @Title: getLen 
	 * @Description: todo(获取字符串字节数 UTF8专用(没用)) 
	 * @param unknown_type $str
	 * @return number  
	 * @author laicaixia 
	 * @date 2013-5-31 下午5:22:33 
	 * @throws 
	*/  
	private function getLen($str){
		return strlen(mb_convert_encoding($str, "gb2312", "utf-8"));
		//return (strlen($str) + mb_strlen($str, 'UTF8')) / 2;
	}
}
?>