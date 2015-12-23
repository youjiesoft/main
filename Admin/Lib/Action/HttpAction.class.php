<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(文件SocketIo操作) 
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-8-12 下午5:28:22 
 * @version V1.0
 */
class HttpAction extends Action{
	/**
	 * @Title: create 
	 * @Description: todo(使用IO流异步生成文件) 
	 * @param 文件夹路径 $dirname  当前文件的文件夹路径
	 * @param 文件路径 $resource_path  当前文件的物理路径
	 * @author liminggang 
	 * @date 2014-8-12 下午4:54:27 
	 * @throws
	 */
	function create($dirname,$resource_path){
		//对文件夹物理路径加密
		$dirname = str_replace("=","",base64_encode($dirname));
		//对文件物理路径加密
		$resource_path = str_replace("=","",base64_encode($resource_path));
		/*
		 * 开启socket通信   
		 * 参数1、HOST_NAME
		 * 参数2、端口
		 * 参数3、参数4、参数5、是socket通信自带的，请阅读PHP socket通信手册
		 */
		$fp = fsockopen($_SERVER['SERVER_NAME'],$_SERVER['SERVER_PORT'],&$errno,&$errstr,5);
		if(!$fp){
			//socket开启失败。
			$this->error("$errstr ($errno)");
		}
		//组装报头
		$out = "GET ".$_SERVER['SCRIPT_NAME']."/Http/createPDF/dirname/{$dirname}/resource_path/{$resource_path}  / HTTP/1.1\r\n";
	    $out .= "Host: ".$_SERVER['SERVER_NAME']."\r\n";
	    $out .= "Connection: Close\r\n\r\n";
	    
	    fwrite($fp, $out);
		fclose($fp);
	}
	function createPDF($dirname,$resource_path){
		$outfile = "";
		//接收参数    1、加密文件夹物理路径
		//接收参数    1、加密文件物理路径
		//使用PHPinfo方法。进行路径解析
		$info=pathinfo($resource_path);
		//组装一个文件标示，方便判断文件是否生成
		$lockFile = $info['dirname'].'/'.$info['filename'].'.lock';
		//验证标示文件是否存在，判断是否为后台自动生成中，避免第二次点击的时候在重新生成
		if(!file_exists($lockFile)){
			//生成标示文件
			file_put_contents($lockFile, 'true');
			//执行PDF文件生成
			import ( '@.ORG.OfficeOnline.OfficeOnlineView' );
			$OfficeOnlineView= new OfficeOnlineView();
			$outfile = $OfficeOnlineView->fileCreate($dirname, $resource_path, 'swf/pdf');
			//删除标示文件
			unlink($lockFile);
		}
		return $outfile;
	}
}