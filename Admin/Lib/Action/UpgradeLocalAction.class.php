<?php
//Version 1.0
import ( '@.ORG.FileUtil' );
/**
 * @Title: UpgradeLocalAction 
 * @Package package_name
 * @Description: todo(本地升级) 
 * @author laicaixia
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-6-2 上午10:35:19 
 * @version V1.0 
*/ 
class UpgradeLocalAction extends CommonAction {

	/**
	 * @Title: step_one 
	 * @Description: todo(第一版执行文件检查)   
	 * @author laicaixia 
	 * @date 2013-6-2 上午10:35:32 
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
	 * @date 2013-6-2 上午10:35:53 
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
	 * @Title: step_three 
	 * @Description: todo(解压文件包，覆盖及备份文件;执行SQL文件)   
	 * @author laicaixia 
	 * @date 2013-6-2 上午10:36:22 
	 * @throws 
	*/  
	public function step_three(){
		//目录
		$folderdir=C('UPGRADE_PATH');
		//生成目录名
		$folder=date('Y-m-d');
		//解压文件名
		$filename='Upgrade.zip';
		import ( '@.ORG.UploadFile' );
		$upload = new UploadFile(); // 实例化上传类
		$MaxSize=C('maxSize');      // 设置附件上传大小 3MB,此处可以配置更大
		$upload->maxSize =$MaxSize  ;
		$upload->allowExts = C('allexts'); // 上传类型设置

		//上传文件路径拼接
		$uploaddir=$folderdir."/".$folder."/";
		$upload->savePath = $uploaddir; // 上传目录
		$upload->dateFormat = C('dateFormat'); // 格式
		$upload->autoSub = false; // 是否开启子目录
		if(!$upload->upload('',$filename)) { // 上传错误提示错误信息,第2个参数为上传文件重命名
			$info=$this->error($upload->getErrorMsg());
		}else{ // 上传成功 获取上传文件信息
			$info = $upload->getUploadFileInfo();
		}
		echo "上传成功";
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
	 * @param 解压的文件路径 $UnzipUrl
	 * @param 解压后文件的路径 $UnzipPath  
	 * @author laicaixia 
	 * @date 2013-6-2 上午10:36:51 
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