<?php
/**
 * 对现有文件进行修改修改
 * @Title: FieldEditAction 
 * @Package package_name
 * @Description: todo(文件修改) 
 * @author quqiang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015年4月27日 上午10:22:10 
 * @version V1.0
 */
class FieldEditAction extends CommonAction {
	function index() {
		// 处理编辑文件初始目录
		$dir = C ( 'FILE_EDIT_ROOT' ) ? C ( 'FILE_EDIT_ROOT' ) : './';
		$fileList = $this->getFolderInfo ( $dir );
		//var_dump($dir);
		//var_dump($fileList);
		
		$this->assign ( 'vo', $fileList );
		$this->display ();
	}
	
	function getFileList(){
		
		
	}
	
	/**
	 * 获取
	 * @Title: getFolderInfo
	 * @Description: todo(这里用一句话描述这个方法的作用)
	 * 
	 * @param unknown $path        	
	 * @author quqiang
	 *         @date 2015年5月18日 下午4:15:29
	 * @throws
	 *
	 */
	function getFolderInfo($path) {
		
		// 是否直接返回。true:内部调用，false:URL地址访问
		$realyReturn = true;
		if (! $path) {
			$path = $_POST ['path'];
			$realyReturn = false;
		}
		// 得到所有能够编辑的文件列表
		// 先读缴存，没有就直接遍历然后存入缓存
		// 处理编辑文件初始目录
		// 所有可以有编辑的文件夹
		$fileData = '';
		// 先找缓存
		$catchdata = F ( 'file_edit_root_dir' );
		if (is_array ( $fileData [$path] )) {
			$fileData = $fileData [$path];
		} else {
			import ( '@.ORG.FileUtil' );
			$fileObj = new FileUtil ();
			$ret = $fileObj->getOneDir ( $path );
			if (is_array ( $ret ) && $path) {
				if (is_array ( $catchdata )) {
					$catchdataTemp = array_merge ( $catchdata, array (
							$path => $ret ['dir']
					) );
				} else {
					$catchdataTemp =  array (
							$path => $ret ['dir']
					);
				}
				F ( 'file_edit_root_dir', $catchdataTemp );
			}
			
			$fileData = $ret;//['dir'];
		}
		
		$temp = reset ( $fileData );
		$dir = '';
		$file='';
		if($temp['dir']){
			$dir = $temp['dir'];
		}
		if($temp['file']){
			$file = $temp['file'];
		}
		
		if(is_array($dir) && is_array($file)){
			$fileList = array_merge ( $dir , $file );
		}elseif(!is_array($dir) && is_array($file)){
			$fileList = $file;
		}elseif(is_array($dir) && !is_array($file)){
			$fileList = $dir;
		}else{
			$fileList = '';
		}
// 		print_r($fileList);
		if ($realyReturn) {
			return $fileList;
		} else {
			//echo json_encode (  $fileList  );
			$this->assign ( 'vo', $fileList );
			$this->display ('FieldEdit:index');
			
		}
	}
	
	
	function editfile(){
		$path = $_POST['path'];
		if(!file_exists($path)){
			echo '文件不存在【'.$path.'】';
			exit;
		}
		$content = file_get_contents($path);
		$this->assign('path' , $path);
		$this->assign('vo' , $content);
		$this->display();
	}
	
	function savecode(){
		
		$content = $_POST['content'];
		$content = html_entity_decode($content, ENT_QUOTES);
		$path = $_POST['path'];
		if(!file_exists($path)){
			$this->error('文件不存在！【'.$path.'】');
			exit;
		}
		
		if($path){
			file_put_contents($path , $content);
			$this->success('文件修改成功','',$content);
		}else{
			$this->error('路径不存在！');
		}
	}
}
