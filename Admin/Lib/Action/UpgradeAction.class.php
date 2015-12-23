<?php
//Version 1.0
/*
 * Created on 2012-5-18
*
* To change the template for this generated file go to
* Window - Preferences - PHPeclipse - PHP - Code Templates
*/
import ( '@.ORG.FileUtil' );
/**
 * @Title: UpgradeAction 
 * @Package package_name
 * @Description: todo(版本升级) 
 * @author laicaixia
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-6-2 上午10:29:28 
 * @version V1.0 
*/ 
class UpgradeAction extends CommonAction {

	/**
	 * @Title: step_one 
	 * @Description: todo(第一版执行文件检查)   
	 * @author laicaixia 
	 * @date 2013-6-2 上午10:29:42 
	 * @throws 
	*/  
	public function step_one(){
		$fileutil= new FileUtil;
		$filedir=$fileutil->file_list_all(APP_PATH);
		//print_r($a);

		$filedirlist=$this->getResult($filedir);
		foreach($filedirlist as $key => $val ){
			$arr[]=explode("|",$val);
		}
		$this->assign("filedir",$arr);
		$this->display();
	}
	/**
	 * @Title: getResult 
	 * @Description: todo(获取$arr循环递归出来的排列结果) 
	 * @param 数组 $arr
	 * @return multitype:|Ambigous <multitype:, multitype:unknown >  
	 * @author laicaixia 
	 * @date 2013-6-2 上午10:30:04 
	 * @throws 
	*/  
	private function getResult($arr) {
		$new_arr=array();
		if(!is_array($arr)){
			return $new_arr;
		}
		foreach($arr  as $key => $value){
			if(is_array($value)){
				$callback_value=$this->getResult($value);
				$new_arr = array_merge($new_arr,$callback_value);
			}else{
				$new_arr[] = $value;
			}
		}
		return $new_arr;
	}

	/**
	 * @Title: Upload 
	 * @Description: todo(上传)   
	 * @author laicaixia 
	 * @date 2013-6-2 上午10:30:40 
	 * @throws 
	*/  
	public function Upload(){
		import ( '@.ORG.UploadFile' );
		$upload = new UploadFile(); // 实例化上传类
		$MaxSize=C('maxSize');      // 设置附件上传大小 3MB,此处可以配置更大
		$upload->maxSize =$MaxSize  ;
		$upload->allowExts = C('allexts'); // 上传类型设置
		$folderdir=C('UPGRADE_PATH');
		$upload->savePath = $folderdir; // 上传目录
		$upload->subType = C('subType'); // 子目录格式
		$upload->dateFormat = C('dateFormat'); // 格式
		$upload->autoSub = C('autoSub'); // 是否开启子目录
		if(!$upload->upload()) { // 上传错误提示错误信息
			$this->error($upload->getErrorMsg());
		}else{ // 上传成功 获取上传文件信息
			$info = $upload->getUploadFileInfo();
		}
		echo "上传成功";
	}

	/**
	 * @Title: Download 
	 * @Description: todo(下载)   
	 * @author laicaixia 
	 * @date 2013-6-2 上午10:30:49 
	 * @throws 
	*/  
	public function Download(){
		$path=!empty($_REQUEST['path'])?$_REQUEST['path']:"";
		@set_time_limit(C('PAGE_LIFE_TIME'));//设置该页面最久执行时间为300秒
		import("@.ORG.RunTime");
		import("@.ORG.HttpDownload");
		$postfix=end(explode(".",$path));
		/*	if($path==$postfix or ($postfix!="zip" or $postfix!="Zip" or $postfix!="ZIP")){
		 echo "下载程序非ZIP文件，请检查下载文件！";
		exit;
		}*/
		$runtime= new RunTime;
		$runtime->start();

		#下载文件
		$file = new HttpDownload(); # 实例化类
		//$file->OpenUrl("http://www.ti.com.cn/cn/lit/an/rust020/rust020.pdf"); # 远程文件地址
		$file->OpenUrl($path);
		//$file->OpenUrl("http://dl_dir.qq.com/qqfile/qq/QQ2010/QQ2010Beta3.exe"); # 远程文件地址
		//重命名下载文件
		$filename="Upgrade.zip";
		//$folder=date('Y-m-d');
		//$folderdir=C('UPGRADE_PATH');
		$aimDir=C('UPGRADE_PATH')."/".date('Y-m-d');
		$file->SaveToBin($filename,$aimDir); // 保存路径及文件名
		$file->Close(); # 释放资源
		$runtime->stop();
		echo "总下载时间：".$runtime->spent();
	}

	/**
	 * @Title: step_three 
	 * @Description: todo(第三步：解压文件包，覆盖及备份文件;执行SQL文件)   
	 * @author laicaixia 
	 * @date 2013-6-2 上午10:30:59 
	 * @throws 
	*/  
	function step_three(){
		//目录
		$folderdir=C('UPGRADE_PATH');
		//生成目录名
		$folder=date('Y-m-d');
		//解压文件名
		$filename='Upgrade.zip';
		//被解压文件路径
		$UnzipUrl=$folderdir.'/'.$folder.'/'.$filename;
		//解压后存储路径
		$UnzipPath=$folderdir.'/'.$folder.'/'."root";
		//1.解压
		$this->UnZip($UnzipUrl,$UnzipPath);
		//拷贝文件路径
		$CopyPath=$UnzipPath;
		//被拷贝文件路径
		$AimPath="../";
		//备份文件路径
		$BackupPath=$folderdir.'/'.$folder.'/'."backup";
		//SQL语句路径
		$SqlUrl=$UnzipPath.'/'."UpgradeSql.sql";
		$doUpgrade=A("DoUpgrade");
		$doUpgrade->doUpgrade($CopyPath,$AimPath,$BackupPath,$SqlUrl);
	}
	/**
	 * @Title: UnZip 
	 * @Description: todo(解压文件) 
	 * @param 要解压的文件路径 $UnzipUrl
	 * @param 解压后文件的路径 $UnzipPath  
	 * @author laicaixia 
	 * @date 2013-6-2 上午10:31:21 
	 * @throws 
	*/  
	private function UnZip($UnzipUrl,$UnzipPath){
		import("@.ORG.PHPZip");
		$archive   = new PHPZip();
		$zipfile   = $UnzipUrl;
		$savepath  = $UnzipPath;
		//$zipfile   = $unzipfile;
		//$savepath  = $unziptarget;
		$array     = $archive->GetZipInnerFilesInfo($zipfile);
		$filecount = 0;
		$dircount  = 0;
		$failfiles = array();
		set_time_limit(C('PAGE_LIFE_TIME'));   //修改为不限制超时时间(默认为30秒)

		for($i=0; $i<count($array); $i++) {
			if($array[$i][folder] == 0){
				if($archive->unZip($zipfile, $savepath, $i) > 0){
					$filecount++;
				}else{
					$failfiles[] = $array[$i][filename];
				}
			}else{
				$dircount++;
			}
		}
		printf("文件夹:%d&nbsp;&nbsp;&nbsp;&nbsp;解压文件:%d&nbsp;&nbsp;&nbsp;&nbsp;失败:%d<br>\r\n", $dircount, $filecount, count($failfiles));
		if(count($failfiles) > 0){
			foreach($failfiles as $file){
				printf("&middot;%s<br>\r\n", $file);
			}
		}
	}


}
?>
