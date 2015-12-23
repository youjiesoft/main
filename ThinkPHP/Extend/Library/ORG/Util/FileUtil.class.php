<?php
/**
 * 操纵文件类
 *
 * 例子：
 * FileUtil::createDir('a/1/2/3');                    测试建立文件夹 建一个a/1/2/3文件夹
 * FileUtil::createFile('b/1/2/3');                    测试建立文件        在b/1/2/文件夹下面建一个3文件
 * FileUtil::createFile('b/1/2/3.exe');             测试建立文件        在b/1/2/文件夹下面建一个3.exe文件
 * FileUtil::copyDir('b','d/e');                    测试复制文件夹 建立一个d/e文件夹，把b文件夹下的内容复制进去
 * FileUtil::copyFile('b/1/2/3.exe','b/b/3.exe'); 测试复制文件        建立一个b/b文件夹，并把b/1/2文件夹中的3.exe文件复制进去
 * FileUtil::moveDir('a/','b/c');                 测试移动文件夹 建立一个b/c文件夹,并把a文件夹下的内容移动进去，并删除a文件夹
 * FileUtil::moveFile('b/1/2/3.exe','b/d/3.exe'); 测试移动文件        建立一个b/d文件夹，并把b/1/2中的3.exe移动进去
 * FileUtil::unlinkFile('b/d/3.exe');             测试删除文件        删除b/d/3.exe文件
 * FileUtil::unlinkDir('d');                      测试删除文件夹 删除d文件夹
 */
class FileUtil extends Think{
	/**
	 * 文件读取,写入权限检查
	 * @param string $filename
	 * */
	function ReadableFile($filename){
		if(file_exists($filename)==false){
			$filestatus=-1;//不存在
		}else if (is_writable($filename) and is_readable($filename)) {
			$filestatus=3;//可读写
		} else if (is_writable($filename)) {
			$filestatus=2;//可写
		} else if (is_readable($filename)) {
			$filestatus=1;//可读
		} else {$filestatus=0;//不可读写}

		return $filestatus;
		}
	}

	/**
	 * 测试目录的可写性
	 * @param string $dir
	 * @return int  1 可写  0 不可写
	 * 小佳(www.phpcina.cn)  整理 于 2006-06-26
	 */
	function WriteableDir($dir) {
		/**
		 * $dir如果不是目录将创建一个可读写的目录
		 */
		if(!is_dir($dir)) {
			@mkdir($dir, 0777);
		}
		if(is_dir($dir)) {  //如果目录已存在
			if($fp = @fopen("$dir/test.test", 'w')) {    //创建一个名为test.test的文件来测试
				@fclose($fp);             //关闭文件流
				@unlink("$dir/test.test");    //删除测试文件
				$writeable = 1;            //能创建则说明可读写，返回值为 1
			} else {
				$writeable = 0;          //不能创建，返回值为 0
			}
		}
		return $writeable;              //返回值
	}
	/**
	 * 遍历目录,找出目录下某个后缀名的所有文件
	 * */
	function viewDir ($directory, $ext) {
		if (is_dir($directory)) {
			$handle = opendir($directory);
			while ($file = readdir($handle)){
				$subdir = $directory . '/' .$file;
				if ($file != '.' && $file !='..' && is_dir($subdir)){
					viewDir($subdir,$ext);
				} else if( $file != '.' && $file != '..') {
					$fileInfo = pathinfo($subdir);
					$fileExt = $fileInfo['extension'];
					if ($fileExt == $ext){
						echo $directory.'/'.$file.'<br />';
					}
				}
			}
			closedir($handle);
		}
	}
	/*  已停用
	 *   递归获取指定路径下的所有文件或匹配指定正则的文件（不包括“.”和“..”），结果以数组形式返回
	*   @param  string  $dir
	*   @param  string  $type
	*   @return array

	function file_list_all($path,$type=true){
	if ($handle = opendir($path)) {
	while (false !== ($file = readdir($handle))) {
	//if ($file != "." && $file != "..") {
	if(strpos($file,".")===false && strpos($file,"Backup")===false){
	if (is_dir($path."/".$file)) {
	echo $path.": ".$file."<br>";//去掉此行显示的是所有的非目录文件
	}
	$this->file_list_all($path."/".$file);
	} else if($type==false){
	echo $path.": ".$file."<br>";
	}
	}
	}
	}
	*/

	/*  2012-07-20
	 *   递归获取指定路径下的所有文件或匹配指定正则的文件（不包括“.”和“..”），结果以数组形式返回
	*   @param  string  $dir
	*   @param  boolen  $type  过滤类型
	*   @param  boolen  $level 打开层级显示
	*   @return array
	*/
	function file_list_all($path,$type=true,$level=false){
		$filearr=array();
		if ($handle = opendir($path)) {
			while (false !== ($file = readdir($handle))) {
				if(strpos($file,".")===false && strpos($file,"Backup")===false){

					//过滤掉不想显示的文件夹
					if (is_dir($path."/".$file)) {
						$writeable=$this->WriteableDir($path."/".$file);//目录是否可读写
						$filearr[]=array(
								//"fileurl"=>$path."/".$file,
								//"writeable"=>$writeable
								$path."/".$file."|".$writeable,
						);
						/* $filestring.=$path.": ".$file."-----".$writeable;*/
						// echo $path.": ".$file."-----".$a."<br>";//去掉此行显示的是所有的非目录文件
					}
					if($level==true){
						$retArr=$this->file_list_all($path."/".$file);
					}
					if(!empty($retArr))
					{
						$filearr[]=$retArr;
					}
				} else if($type==false){
					echo $path.": ".$file."<br>";
				}
			}
		}
		return $filearr;
	}
	/*
	 *   递归获取指定路径下的所有文件或匹配指定正则的文件（不包括“.”和“..”），结果以数组形式返回
	*   此处不可用，$this调用有问题
	*   @param  string  $dir
	*   @param  string  [$pattern]
	*   @return array
	*/
	function file_list($dir,$pattern="")
	{
		// 	$dir=ROOT;
		$arr=array();
		$dir_handle=opendir($dir);
		if($dir_handle !== false)
		{
			// 这里必须严格比较，因为返回的文件名可能是“0”
			while(($file=readdir($dir_handle))!==false)
			{
				//if($file==='.' || $file==='..')
				if(strpos($file, '.')||strpos($file, '..'))
				{
					continue;
				}
				//$tmp=realpath($dir.'/'.$file);
				$tmp=$dir.'/'.$file;
				//             echo $tmp."<br/>";
				if(is_dir($tmp))
				{
					$retArr=$this->file_list($tmp,$pattern);
					if(!empty($retArr))
					{
						$arr[]=$retArr;
					}
				}
				else
				{
					if($pattern==="" || preg_match($pattern,$tmp))
					{
						$arr[]=$tmp;
					}
				}
			}
			closedir($dir_handle);
		}
		return $arr;
	}
	/**
	 * 递归遍历目录下的所有文件，此处不可用，$this调用有问题
	 * */
	function get_filetree($path){
		$path="../Admin";
		$tree = array();
		foreach(glob($path.'/*') as $single){
			if(is_dir($single)){
				$tree = array_merge($tree,$this->get_filetree($single));
			}
			else{
				$tree[] = $single;
				print_r($tree);
			}
		}
		return $tree;
	}

	/*
	 * 文件夹大小
	* @param  string  $dir
	*/
	function dirsize($dirname){
		$dirsize=0;
		$dir=opendir($dirname);
		while($filename=readdir($dir)){
			$file=$dirname."/".$filename;
			if($filename!="."&&$filename!=".."){
				if(is_dir($file)){
					$dirsize+=$this->dirsize($file);//递归完成
				}else{
					$dirsize+=filesize($file);
				}
			}
		}
		closedir($dir);
		return $dirsize;
	}
	/**
	 * 建立文件夹
	 *
	 * @param string $aimUrl
	 * @return viod
	 */
	function createDir($aimUrl,$num = 0775) {
		$boolean = false;
		$aimUrl = str_replace('', '/', $aimUrl);
		$aimDir = '';
		$arr = explode('/', $aimUrl);
		foreach ($arr as $str) {
			$aimDir .= $str . '/';
			if (!file_exists($aimDir)) {
				$boolean=mkdir($aimDir,$num);
			}
		}
		return $boolean;
	}
	/**
	 * 建立文件
	 *
	 * @param string $aimUrl
	 * @param boolean $overWrite 该参数控制是否覆盖原文件
	 * @return boolean
	 */
	function createFile($aimUrl, $overWrite = false) {
		if (file_exists($aimUrl) && $overWrite == false) {
			return false;
		} elseif (file_exists($aimUrl) && $overWrite == true) {
			FileUtil::unlinkFile($aimUrl);
		}
		$aimDir = dirname($aimUrl);
		FileUtil::createDir($aimDir);
		touch($aimUrl);
		return true;
	}
	/**
	 * 移动文件夹
	 *
	 * @param string $oldDir
	 * @param string $aimDir
	 * @param boolean $overWrite 该参数控制是否覆盖原文件
	 * @return boolean
	 */
	function moveDir($oldDir, $aimDir, $overWrite = false) {
		$aimDir = str_replace('', '/', $aimDir);
		$aimDir = substr($aimDir, -1) == '/' ? $aimDir : $aimDir . '/';
		$oldDir = str_replace('', '/', $oldDir);
		$oldDir = substr($oldDir, -1) == '/' ? $oldDir : $oldDir . '/';
		if (!is_dir($oldDir)) {
			return false;
		}
		if (!file_exists($aimDir)) {
			FileUtil::createDir($aimDir);
		}
		@$dirHandle = opendir($oldDir);
		if (!$dirHandle) {
			return false;
		}
		while(false !== ($file = readdir($dirHandle))) {
			if ($file == '.' || $file == '..') {
				continue;
			}
			if (!is_dir($oldDir.$file)) {
				FileUtil::moveFile($oldDir . $file, $aimDir . $file, $overWrite);
			} else {
				FileUtil::moveDir($oldDir . $file, $aimDir . $file, $overWrite);
			}
		}
		closedir($dirHandle);
		return rmdir($oldDir);
	}
	/**
	 * 移动文件
	 *
	 * @param string $fileUrl
	 * @param string $aimUrl
	 * @param boolean $overWrite 该参数控制是否覆盖原文件
	 * @return boolean
	 */
	function moveFile($fileUrl, $aimUrl, $overWrite = false) {
		if (!file_exists($fileUrl)) {
			return false;
		}
		if (file_exists($aimUrl) && $overWrite = false) {
			return false;
		} elseif (file_exists($aimUrl) && $overWrite = true) {
			FileUtil::unlinkFile($aimUrl);
		}
		$aimDir = dirname($aimUrl);
		FileUtil::createDir($aimDir);
		$boolean=rename($fileUrl, $aimUrl);
		return $boolean;
	}
	/**
	 * 删除文件夹
	 *
	 * @param string $aimDir
	 * @return boolean
	 */
	function unlinkDir($aimDir) {
		$aimDir = str_replace('', '/', $aimDir);
		$aimDir = substr($aimDir, -1) == '/' ? $aimDir : $aimDir.'/';
		if (!is_dir($aimDir)) {
			return false;
		}
		$dirHandle = opendir($aimDir);
		while(false !== ($file = readdir($dirHandle))) {
			if ($file == '.' || $file == '..') {
				continue;
			}
			if (!is_dir($aimDir.$file)) {
				FileUtil::unlinkFile($aimDir . $file);
			} else {
				FileUtil::unlinkDir($aimDir . $file);
			}
		}
		closedir($dirHandle);
		return rmdir($aimDir);
	}
	/**
	 * 删除文件
	 *
	 * @param string $aimUrl
	 * @return boolean
	 */
	function unlinkFile($aimUrl) {
		if (file_exists($aimUrl)) {
			unlink($aimUrl);
			return true;
		} else {
			return false;
		}
	}
	/**
	 * 复制文件夹
	 *
	 * @param string $oldDir
	 * @param string $aimDir
	 * @param boolean $overWrite 该参数控制是否覆盖原文件
	 * @param boolean $useBackup 该参数控制是否备份，只在$overWrite为TRUE时生效
	 * @param string  $backupDir 该参数控制是否备份，只在$useBackup为TRUE时生效
	 * @return boolean
	 */
	function copyDir($oldDir, $aimDir, $overWrite = false,$useBackup=false,$backupDir) {
		$aimDir = str_replace('', '/', $aimDir);
		$aimDir = substr($aimDir, -1) == '/' ? $aimDir : $aimDir.'/';
		$oldDir = str_replace('', '/', $oldDir);
		$oldDir = substr($oldDir, -1) == '/' ? $oldDir : $oldDir.'/';
		$backupDir = str_replace('', '/', $backupDir);
		$backupDir = substr($backupDir, -1) == '/' ? $backupDir : $backupDir.'/';
		if (!is_dir($oldDir)) {
			return false;
		}
		if($useBackup==true && !file_exists($backupDir) && file_exists($aimDir)){
			FileUtil::createDir($backupDir);
		}
		if (!file_exists($aimDir)) {
			FileUtil::createDir($aimDir);
		}
		$dirHandle = opendir($oldDir);
		while(false !== ($file = readdir($dirHandle))) {
			if ($file == '.' || $file == '..') {
				continue;
			}
			if (!is_dir($oldDir . $file)) {
				if($useBackup==true){
					FileUtil::copyFile($oldDir . $file, $aimDir . $file, $overWrite,$useBackup,$backupDir. $file);
				}

			} else {
				FileUtil::copyDir($oldDir . $file, $aimDir . $file, $overWrite,$useBackup,$backupDir. $file);
			}
		}
		return closedir($dirHandle);
	}
	
	function copyDirIncludeChildren($oldDir, $aimDir, $overWrite = false,$useBackup=false,$backupDir) {
		$aimDir = str_replace('', '/', $aimDir);
		$aimDir = substr($aimDir, -1) == '/' ? $aimDir : $aimDir.'/';
		$oldDir = str_replace('', '/', $oldDir);
		$oldDir = substr($oldDir, -1) == '/' ? $oldDir : $oldDir.'/';
		$backupDir = str_replace('', '/', $backupDir);
		$backupDir = substr($backupDir, -1) == '/' ? $backupDir : $backupDir.'/';
		if (!is_dir($oldDir)) {
			return false;
		}
		if($useBackup==true && !file_exists($backupDir) && file_exists($aimDir)){
			FileUtil::createDir($backupDir);
		}
		if (!file_exists($aimDir)) {
			FileUtil::createDir($aimDir);
		}
		$dirHandle = opendir($oldDir);
		while(false !== ($file = readdir($dirHandle))) {
			if ($file == '.' || $file == '..') {
				continue;
			}
			if (!is_dir($oldDir . $file)) {
				FileUtil::copyFile($oldDir . $file, $aimDir . $file, $overWrite,$useBackup,$backupDir. $file);
			} else {
				FileUtil::copyDir($oldDir . $file, $aimDir . $file, $overWrite,$useBackup,$backupDir. $file);
			}
		}
		return closedir($dirHandle);
	}
	/**
	 * 复制文件
	 *
	 * @param string $fileUrl
	 * @param string $aimUrl
	 * @param boolean $overWrite 该参数控制是否覆盖原文件
	 * @param boolean $useBackup 该参数控制是否备份，只在$overWrite为TRUE时生效
	 * @param string  $backupUrl 该参数控制备份文件对象，只在$useBackup为TRUE时生效
	 * @return boolean
	 */
	function copyFile($fileUrl, $aimUrl, $overWrite = false,$useBackup=false,$backupUrl) {
		if (!file_exists($fileUrl)) {
			return false;
		}
		if (file_exists($aimUrl) && $overWrite == false) {
			return false;
		} else if (file_exists($aimUrl) && $overWrite == true) {

			if($useBackup==true && file_exists($backupUrl)){
				return false;
			}
			else{
				FileUtil::moveFile($aimUrl, $backupUrl);
			}
			FileUtil::unlinkFile($aimUrl);
		}
		$aimDir = dirname($aimUrl);
		FileUtil::createDir($aimDir);
		copy($fileUrl, $aimUrl);
		return true;
	}
}
?>