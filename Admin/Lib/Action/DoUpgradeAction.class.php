<?php
class DoUpgradeAction extends CommonAction {
public function doUpgrade($CopyPath,$AimPath,$BackupPath,$SqlUrl){
//2.文件拷贝
$this->updateFile($CopyPath,$AimPath,$BackupPath);
//3.SQL更新
$this->updateSql($SqlUrl);
}

/*
 * 文件更新及备份
 */
function updateFile($CopyPath,$AimPath,$BackupPath){
  FileUtil::copyDir($CopyPath,$AimPath,$overWrite = true,$useBackup= true ,$BackupPath);
  //删除特殊文件
  FileUtil::unlinkFile("../UpgradeSql.sql");
  FileUtil::unlinkFile("../SqlLog.txt");
}

function updateSql($SqlUrl){
	if(!file_exists($SqlUrl)){
		return false;
	}
	else{
	   //SQL更新日志,拼接路径与SQL更新文件相同，名称固定为$SqlLog.txt
	   $log=substr($SqlUrl,0,strrpos($SqlUrl,'/'));
       $log = substr($log, -1) == '/' ? $log : $log . '/';
	   $log.="SqlLog.txt";
	   FileUtil::createFile($log);
	   $this->sqlQuery($SqlUrl,$log);
	}
}

//SQL文件读取执行
function  sqlQuery($SqlUrl,$log){
	$Upgrade = M();
    $Upgrade->query("SET AUTOCOMMIT=0");
    $Upgrade->query("START TRANSACTION");
    $Upgrade->query("SET NAMES gbk");
	$handle = fopen($SqlUrl, "r");

    while($sql = $this->getNextSql($handle)){
     if($Upgrade->query($sql) === false){
       $msg = date("Y-m-d H:i:s")." -- 升级失败，数据库更新有冲突，具体信息如下：\r\n";
       $msg .= mysql_errno()." : ".mysql_error()."\r\n";
	   $f_log = fopen($log, 'a');
       fwrite($f_log, $msg);
       fclose();
	   $Upgrade->query("ROLLBACK");
	   echo "升级失败，原因已写入日志，请联系管理员查看";
	   exit;
     }
	}
    $Upgrade->query("COMMIT");
	fclose($handle);
	return true;
}

//从文件中逐条取sql
function getNextSql($handle)
{
   if ($handle) {
    while (!feof($handle)) {
        $buffer = fgets($handle, 4096);
        $buffer=trim($buffer);
        if (strlen($buffer)>1) {
	            if ($buffer[0]=="-" && $buffer[1]=="-") {
	                continue;
	            }
	        }
	        $sql.=$buffer.chr(13).chr(10);
	        if (strlen($buffer)>0){
	            if ($buffer[strlen($buffer)-1]==";"){
	                break;
	            }
	        }
    }
	if(trim($sql) != ''){
	  return trim($sql);
	}
	else{
	   return false;
	}
  }
}

}
?>
