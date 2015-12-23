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

	/**
	 * 遍历指定目录，获取与指定文件比对信息相符的目录。
	 * @example <pre>$obj = new FileUtil();
	 * 	global $fileArr;
	 * $obj->findFileInDir(ROOT.'/Tpl',array('1.php','view.html'));
	 * $fileArr = array_flip($fileArr);
	 * var_dump($fileArr);
	 * @param string		$directory	检索目录
	 * @param string|array 	$ext		比对的文件名
	 */
	function findFileInDir ($directory, $ext) {
		global  $fileArr;
		global  $tempArr;
		if (is_dir($directory)) {
			$handle = opendir($directory);
			while ($file = readdir($handle)){
				$subdir = $directory . '/' .$file;
				if ($file != '.' && $file !='..' && is_dir($subdir) ){
					//if ($file != '.' && $file !='..' && is_dir($subdir)){
					self::findFileInDir($subdir,$ext);
				} else if( $file != '.' && $file != '..') {

					$fileInfo = pathinfo($subdir);
					$fileExt = $fileInfo['extension'];
					$fileFullName = $fileInfo['basename'];
					$tempArr[]=$fileInfo['dirname'];
					if(is_array($ext)){
							
						if(in_array($fileFullName, $ext)){
							$fileArr[]=$fileInfo['dirname'];
						}
					}else{

						if ($fileFullName == $ext){
							$fileArr[]=$fileInfo['dirname'];
						}
					}
				}
			}
			closedir($handle);
		}
	}

	/**
	 * 遍历指定目录，获取不包含指定文件的目录。
	 * @example <pre>$obj = new FileUtil();
	 * global $fileArr;
	 * $obj->findFileNotInDir(ROOT.'/Tpl',array('view.html','auditview.html'));
	 * var_dump($fileArr);//是这结果
	 * @param string		$directory	检索目录
	 * @param string|array 	$ext		比对的文件名
	 */
	function findFileNotInDir ($directory, $ext) {
		global  $fileArr;
		if (is_dir($directory)) {
			$handle = opendir($directory);
			while ($file = readdir($handle)){
				$subdir = $directory . '/' .$file;
				if ($file != '.' && $file !='..' && is_dir($subdir) ){
					//if ($file != '.' && $file !='..' && is_dir($subdir)){
					self::findFileNotInDir($subdir,$ext);
				} else if( $file != '.' && $file != '..') {
					$fileInfo = pathinfo($subdir);
					$fileExt = $fileInfo['extension'];
					$fileFullName = $fileInfo['basename'];
					if(is_array($ext)){
						$temp;
						foreach ($ext as $k=>$v){
							if ($fileFullName != $v){
								$temp[]=$fileInfo['dirname'];
							}
						}
						$ta[]=$temp;
						foreach ($ta as $k=>$v){
							if(count($v)== count($ext)){
								$fileArr[]=$v[0];
							}
						}
					}else{
						if ($fileFullName != $ext){
							$fileArr[]=$fileInfo['dirname'];
						}
					}
					$fileArr = array_flip(array_flip($fileArr));
				}
			}
			closedir($handle);
		}
	}


	/**
	 * 遍历指定目录，获取不包含指定文件的目录。
	 * @param string		$directory	检索目录
	 * @param string	 	$ext		检查文件名
	 * @param string		$souce		资源文件名
	 */
	function checkFileAndCreate($directory, $ext, $souce) {
		global  $fileArr;
		if (is_dir($directory)) {
			$handle = opendir($directory);
			while ($file = readdir($handle)){
				$subdir = $directory . '/' .$file;
				if ($file != '.' && $file !='..' && is_dir($subdir) ){
					//if ($file != '.' && $file !='..' && is_dir($subdir)){
					self::checkFileAndCreate($subdir,$ext , $souce);
				} else if( $file != '.' && $file != '..') {
					$msg ='';
					$fileInfo = pathinfo($subdir);
					$fileExt = $fileInfo['extension'];
					$fileFullName = $fileInfo['basename'];
					//当前文件夹下没有该文件
					if ($fileFullName != $ext){
						$fileArr[]=$fileInfo['dirname'];
						//error_log('目录：'.str_replace(ROOT, '', $fileInfo['dirname']).chr(16)."缺失文件：{$ext}".chr(16).Date('Y-m-d H:i:s', time()).chr(13).chr(10),3,ROOT.'/a.log');

						$msg .= "当前文件夹{$fileInfo['dirname']}中没有文件{$ext}";
						//检查当前文件夹下有不有资源文件
						$ret = $this->checFileInCurrentDir($directory , $souce);

						/////////////////////////////////文件复制////////////////////////////////
						if($ret){
							$orderIsRet = $this->checFileInCurrentDir($directory , $ext);
							if( !$orderIsRet ){
								$souceFile = $directory.'/'.$souce;
								$orderFile = $directory.'/'.$ext;
								$content = file_get_contents($souceFile);
								file_put_contents($orderFile, $content);
								//error_log(chr(16)."已从当前目录中获取[{$souce}]内容并生成[{$ext}]文件".chr(16).Date('Y-m-d H:i:s', time()).chr(13).chr(10),3,ROOT.'/a.log');
							}
							$msg .= "--<b style=\"color:green\">【successs】</b>--我要开始生成新文件了。{$ext}<br/>";
						}else {
							//							error_log(chr(16)."当前目录[{$directory}]中不存在:".$souce.' 文件 。'.chr(13).chr(10),3,ROOT.'/a.log');
							$msg .= "--<b style=\"color:red\">【Waring】</b>--没有资源生成文件{$$souce}<br/>";
						}
						/////////////////////////////////////////////////////////////////
						$msg .= Date('Y-m-d H:i:s',time()).'-----'.time();
					}
					//	var_dump($msg);
					error_log($msg.chr(13).chr(10),3,ROOT.'/a.log');

					//$fileArr = array_flip(array_flip($fileArr));


				}
			}
			closedir($handle);
		}
	}

	function checFileInCurrentDir($dir , $fileName){
		$ret = false;
		if(is_dir($dir)){
			$handle = opendir($dir);
			while ($file = readdir($handle)){
				$subdir = $dir.'/'.$file;

				if($file!='.' && $file!='..'){
					$fileInfo = pathinfo($subdir);
					if($fileName == $fileInfo['basename']){
						$ret= true;
						break;
					}
				}
			}
		}else{
			$ret = false;
		}
		return $ret;
	}

	/**
	 * 获取指定目录下的所有文件夹
	 * @param string $dir	地址
	 * @return array $dirArray('dir'=>array('dir1','dir2',.....))
	 */
	public function getDir($dir){
		$dirArr = array ();
		$dir = rtrim($dir, '//');
		if (is_dir($dir)) {
			$dirHandle = opendir($dir);
			while (false !== ($fileName = readdir($dirHandle))) {
				$subFile = $dir . DIRECTORY_SEPARATOR . $fileName;
				if (is_dir($subFile) && str_replace('.', '', $fileName) != '') {
					$dirArr[] = $subFile;
					$arr = $this->getDir($subFile);
					$dirArr = array_merge($dirArr, $arr['dir']);
				}
			}
			closedir($dirHandle);
		}
		return array (
			'dir' => $dirArr,
		);
	}
	
	/**
	 * 获取指定目录下的文件及文件夹
	 * @param string $dir	地址
	 * @return array $dirArray('dir'=>array('dir1','dir2',.....))
	 */
	public function getOneDir($dir){
		$dirArr = '';
		$dir = rtrim($dir, '//');
		if (is_dir($dir)) {
			$dirHandle = opendir($dir);
			while (false !== ($fileName = readdir($dirHandle))) {
				$subFile = $dir . DIRECTORY_SEPARATOR . $fileName;
				if (is_dir($subFile) && str_replace('.', '', $fileName) != '') {
					//$dirArr[$fileName] = $subFile;
					$dirArr[$fileName] = pathinfo($subFile);
				}
				if (is_file($subFile)) {
					// 将文件处理为， 文件名key，数组包含 , 后缀，路径，文件全称格式
					$fileArr[$fileName] = pathinfo($subFile);
				}
			}
			closedir($dirHandle);
		}
		$ret = '';
		if(is_array($dirArr)){
			$ret['dir']=$dirArr;
		}
		if(is_array($fileArr)){
			$ret['file']=$fileArr;
		}
		if(is_array($ret)){
			return array('dir' => $ret);
		}else{
			return '';
		}
	}
	
	
	
	/**
	 * 获取目录下的文件
	 * @param string	$dir	地址
	 * @return array	$fileArray('地址参数值'=>array('file1','file2'......))
	 */
	public function getFile($dir){
		$fileArr = array ();
		$dir = rtrim($dir, '//');
		if (is_dir($dir)) {
			$dirHandle = opendir($dir);
			while (false !== ($fileName = readdir($dirHandle))) {
				$subFile = $dir . DIRECTORY_SEPARATOR . $fileName;
				if (is_file($subFile)) {
					array_push($fileArr, $fileName);
				}
			}
			closedir($dirHandle);
		}
		return array($dir=>$fileArr);
	}

	/**
	 * 检查指定目录下的所有文件并生成缺失文件，若目标文件不存在则生成，其值来源与资源文件，若资源文件不生成。
	 * @example <pre>
	 * $obj = new FileUtil();
	 * $chekDir = ROOT.'/nbm';
	 * $obj->checkFileAndCreateAppend($chekDir , 'edit.html',ROOT.'/nbm/a.html','');
	 * @param string $dir	目录
	 * @param string $order	目标文件.
	 * @param string $souce	资源文件. 如果资源与目标文件不在同一路径下请传入物理地址.如：e:/a/b/c.txt
	 * @param string $log	日志存放路径及文件名.默认为当前项目ROOT 全局变量路径下,日志文件名为【recode.log】。
	 * @param string $createDir 生成文件存放路径，默认为当前目录。 如：e:/a/b/
	 * @param boolean forciblyBuild 当$createDir 路径下有检查文件时指定是否覆盖原文件内容。默认为 false,不覆盖
	 *
	 * 屈强@2014-07-05 	修改：修正递归目录时不找当前目录下的文件
	 * 屈强@2014-07-07 	修改：生成文件存在时是否覆盖 
	 *
	 */
	public function checkFileAndCreateAppend($dir , $order , $souce , $log='' ,$createDir='' ,$forciblyBuild=false){
	error_reporting(E_ALL | E_STRICT);
		if($log ==''){
			$log = ROOT.'/recode.log';
		}
		$souceInfo = pathinfo($souce);
		$soucePath = $souceInfo['dirname'];
		$souce = $souceInfo['basename'];

		$dirData['dir'][]=$dir;
		$dirData1 = self::getDir($dir);
		$dirData['dir'] = array_merge($dirData['dir'], $dirData1['dir']);
		
		//遍历文件夹下文件
		$file= array();
		foreach ($dirData['dir'] as $k=>$v){
			$fieTemp = self::getFile($v);
			$msg = '';
			$souceFilePath = '';
			if(!in_array($order, $fieTemp[$v])){
				$msg .=  '目录['.$v.']中没有文件 <'.$order.'>['.microtime().']';
				if($soucePath=='.'){
					if(!in_array($souce, $fieTemp[$v])){
						$msg .= '  ┠Waring┨没有资源生成文件'.$souce.'<br/>';
					}else{
						$souceFilePath = $v.'/'.$souce;
					}
				}else{
					if(!file_exists($soucePath.'/'.$souce)){
						$msg .= '  ┠Waring┨没有资源生成文件'.$souce.'<br/>';
					}else{
						$souceFilePath = $soucePath.'/'.$souce;
					}
				}
				if($souceFilePath){
					$content = file_get_contents($souceFilePath);
					//$content .= Date('Y-m-d H:i:s',time());
					if(is_dir($createDir)){
						$createDirCheck = self::getFile($createDir);
						$isFileExist = false;
						if(true == in_array($order ,$createDirCheck[$createDir])){
							$msg .= '  ┠Waring┨ 文件'.$createDir.' '.$order.'，已存在';
							$isFileExist = true;
						}
						//正常生成
						if(!$isFileExist){
							if(substr($createDir,-1)=='/'){
								$ret = file_put_contents($createDir.$order, $content);
							}else{
								$ret = file_put_contents($createDir.'/'.$order, $content);
							}
							
							if($ret){
								$msg .= '  ┨successs┠ 已生成新文件'.$order.'<br/>';
							}else{
								$msg .= '  ┴ERROR┴ 文件生成失败，请检查文件夹权限。'.$order.'<br/>';
							}
						}
						
						if($isFileExist && $forciblyBuild){
							$msg .= '，强制生成<br/>';
							if(substr($createDir,-1)=='/'){
								$ret = file_put_contents($createDir.$order, $content);
							}else{
								$ret = file_put_contents($createDir.'/'.$order, $content);
							}
							
							if($ret){
								$msg .= '  ┨successs┠ 已生成新文件'.$order.'<br/>';
							}else{
								$msg .= '  ┴ERROR┴ 文件生成失败，请检查文件夹权限。'.$order.'<br/>';
							}
						}else{
							$msg .= '，不予生成<br/>';
						}
					}else{
						$ret = file_put_contents($v.'/'.$order, $content);
						if($ret){
							$msg .= '  ┨successs┠ 已生成新文件'.$order.'<br/>';
						}else{
							$msg .= '  ┴ERROR┴ 文件生成失败，请检查文件夹权限。'.$order.'<br/>';
						}
					}
					
					
				}
				$msg.=Date('Y-m-d H:i:s',time());
				
				if(function_exists('logs')){
					logs($msg,'checkFile'.Date('Y-m-d',time()));
				}else{
					error_log($msg.chr(13).chr(10),3,$log);
				}
			}
		}
		$time=Date('Y-m-d H:i:s',time());
		return '日志位置：'.$log;
	}
	
	/**
	* 检查指定目录下文件是否存在，不存在则生成。
	* @param	string			$dir			检查目录。不递归子级目录
	* @param	string			$order			要检查的文件
	* @param	string|array 	$souce			资源文件
	* @param	string			$saveDir		生成文件保存地址
	* @param	boolean			$forciblyBuild	是否覆盖，默认false：不覆盖
	* @param	boolean|string	$logDir			日志记录状态。false:不记录。true:记录到项目根目录recode.log中，String:存放于指定位置。
	*/
	function checkFile($dir , $order ,$souce , $saveDir='' , $forciblyBuild=false , $logDir=false){
		//nbmxkj
		$msg='';
		$temp ;
		if(is_array($souce)){
			foreach($souce as $k=>$v){
				$souceInfo = pathinfo($v);
				$soucePath[] = $souceInfo['dirname'];
				$temp[] = $souceInfo['basename'];
			}
		}else{
			$souceInfo = pathinfo($souce);
			$soucePath = $souceInfo['dirname'];
			$temp = $souceInfo['basename'];
		}
		//unset($souce);
		$souce=$temp;
		$temp='';
		//unset($temp);
		if(is_dir($dir)){
			$souceFilePath='';
			$fileArr = self::getFile($dir);
			if(!in_array($order , $fileArr[$dir])){
				//目标文件不是当前目录中
				//检查资源文件，准备生成
				if(is_array($souce)){
					foreach($souce as $k=>$v){
						if(in_array($v , $fileArr[$dir])){
							$temp=$v;
							$souceFilePath=$dir.'/'.$v;
							break;
						}
					}
				}else{
					//$tmp=$fileArr[$dir];
					if(in_array($souce , $fileArr[$dir])){
						$temp = $souce;
						$souceFilePath=$dir.'/'.$souce;
					}
				}
				if($temp==''){
					//资源文件不存在，生成失败。
					$msg .=' ERROR:资源文件不存在，生成失败。';
				}else{
					$savepath = $dir;
					if($saveDir !='' && is_dir($saveDir)){
						$savepath =$saveDir;;
					}
					//waring:文件已存在
					$createDirCheck = self::getFile($savepath);
					$isFileExist = false;
					if(in_array($order , $createDirCheck[$savepath])){
						$isFileExist = true;
						$msg .=' waring:目录 '.$savepath.' 中已存在准备生成文件'.$order.' 。';
					}
					
					if(!$isFileExist){
						//正常生成
						$content = file_get_contents($souceFilePath);
						$ret = file_put_contents($savepath.'/'.$order, $content);
						if($ret){
							$msg .=' info:生成成功'.$savepath.'/'.$order;
						}else{
							$msg .=' Error:生成失败，请检查权限'.$savepath.'/'.$order;
						}
					}else{
						//覆盖生成
						if($forciblyBuild){
							$content = file_get_contents($souceFilePath);
							$ret = file_put_contents($savepath.'/'.$order, $content);
							if($ret){
								$msg .=' info:成功覆盖文件'.$savepath;
							}else{
								$msg .=' Error:生成失败，请检查权限'.$savepath;
							}
						}
					}
				}
			}else{
				//目标文件存在
				$msg .=' info:目标文件'.$order.'已存在于 '.$dir.' 目录中。';
			}
		}
		return $msg;
		
		if($logDir === false){
		}else{
			if(!is_dir($logDir)){
				$logDir = ROOT.'/recode.log';
			}
			$time=Date('Y-m-d H:i:s',time());
			error_log($msg.$time.chr(13).chr(10),3,$logDir);
		}
	}

	/**
	 * 获取指定文件的内容
	 * @param $file
	 */
	function getFileContent($file)
	{
		if (!$fp = fopen($file, "r")) {
			die("Cannot open file $file");
		}
		while ($text = fread($fp, 4096)) {
			$fileContent .= $text;
		}
		return $fileContent;
	}

	/**
	 * 替换文件中的内容 <p><B>读取文件的内容中每一项必须单独成行。</B></p>
	 * @example <pre>
	 * $dir = ROOT.'/nbm/m';
	 * import('@.ORG.FileUtil');
	 * $obj = new FileUtil();
	 * $search = 'find';
	 * $replace = 'this is the resoult ';
	 * $obj->replaceFileContent($dir, $search, $replace, $file=array('searchby.inc.php'));
	 * @param string 		$dir		文件所有目录
	 * @param string 		$search		检索内容
	 * @param string		$replace	替换内容
	 * @param string|array 	$file		搜索的文件
	 */
	public function replaceFileContent($dir , $search , $replace , $file=''){
		if($file){
			if(!is_array($file)){
				$temp[]=$file;
				unset($file);
				$file= $temp;
			}
		}
		$dirData = self::getDir($dir);
		$fileData= array();
		foreach ($dirData['dir'] as $k=>$v){
			$fileTemp = self::getFile($v);
			foreach ($fileTemp[$v] as $key=>$val){
				if($file){
					if(in_array($val, $file)){
						$fileData[]= $v.DIRECTORY_SEPARATOR.$val;
					}
				}else{
					$fileData[]= $v.DIRECTORY_SEPARATOR.$val;
				}
			}
		}
		foreach ($fileData as $k=>$v){
			$content = self::getFileContent($v);
			if(count($content)){
				echo '1';
				$match = '/([ \t]*)(\''.$search.'\'\s*=>\s*\'?)(.*?)(\'?)(\s*,\s*$)/m';
				$data = preg_replace($match,"\${1}\${2}{$replace}\${4}\${5}",$content);
				file_put_contents($v, $data);
			}else{
				exit('文件[ '.$v.' ]内容读取失败');
			}
		}
	}
}
?>