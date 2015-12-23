<?php
/**
 * 下载远程文件类支持断点续传
 */
class HttpDownload extends Think{
	private $m_url = "";
 	private $m_urlpath = "";
 	private $m_scheme = "http";
 	private $m_host = "";
 	private $m_port = "80";
 	private $m_user = "";
 	private $m_pass = "";
 	private $m_path = "/";
 	private $m_query = "";
 	private $m_fp = "";
 	private $m_error = "";
	private $m_httphead = "" ;
	private $m_html = "";
	/**
	 * 初始化
	 */
	public function PrivateInit($url){
		$urls = "";
		$urls = @parse_url($url);
		$this->m_url = $url;
		if(is_array($urls)) {
			$this->m_host = $urls["host"];
			if(!empty($urls["scheme"])) $this->m_scheme = $urls["scheme"];
			if(!empty($urls["user"])) $this->m_user = $urls["user"];
		    if(!empty($urls["pass"])) $this->m_pass = $urls["pass"];
		    if(!empty($urls["port"])) $this->m_port = $urls["port"];
		    if(!empty($urls["path"])) $this->m_path = $urls["path"];
		    $this->m_urlpath = $this->m_path;
			if(!empty($urls["query"])) {
     			$this->m_query = $urls["query"];
     			$this->m_urlpath .= "?".$this->m_query;
     		}
  		}
	}
	/**
	* 打开指定网址
	*/
	function OpenUrl($url) {
		#重设各参数
		$this->m_url = "";
		$this->m_urlpath = "";
		$this->m_scheme = "http";
		$this->m_host = "";
		$this->m_port = "80";
		$this->m_user = "";
		$this->m_pass = "";
		$this->m_path = "/";
		$this->m_query = "";
		$this->m_error = "";
		$this->m_httphead = "" ;
		$this->m_html = "";
		$this->Close();
		#初始化系统
		$this->PrivateInit($url);
		$this->PrivateStartSession();
	}
	/**
	* 获得某操作错误的原因
	*/
	public function printError() {
		echo "错误信息：".$this->m_error;
		echo "具体返回头：<br>";
		foreach($this->m_httphead as $k=>$v) {
			echo "$k => $v <br>\r\n";
		}
	}
	/**
	* 判别用Get方法发送的头的应答结果是否正确
	*/
	public function IsGetOK() {
		if( ereg("^2",$this->GetHead("http-state")) ) {
			return true;
		} else {
			$this->m_error .= $this->GetHead("http-state")." - ".$this->GetHead("http-describe")."<br>";
			return false;
		}
	}
	/**
	* 看看返回的网页是否是text类型
	*/
	public function IsText() {
		if (ereg("^2",$this->GetHead("http-state")) && eregi("^text",$this->GetHead("content-type"))) {
			return true;
		} else {
			$this->m_error .= "内容为非文本类型<br>";
			return false;
		}
	}
	/**
	* 判断返回的网页是否是特定的类型
	*/
	public function IsContentType($ctype) {
		if (ereg("^2",$this->GetHead("http-state")) && $this->GetHead("content-type") == strtolower($ctype)) {
			return true;
		} else {
			$this->m_error .= "类型不对 ".$this->GetHead("content-type")."<br>";
			return false;
		}
	}
	/**
	* 用 HTTP 协议下载文件
	* $filename保存文件名
	* $aimDir保存路径
	*/
	public function SaveToBin($filename,$aimUrl,$type=true) {
	    if (!$this->IsGetOK()) return false;
		if (@feof($this->m_fp)) {
			$this->m_error = "连接已经关闭！";
			return false;
		}
        if($type==true){
		echo '  <div>
		        <table border="1" width="300">
				<tr><td width="100">文件大小</td><td width="200"><div id="filesize">未知长度</div></td></tr>
				<tr><td>已经下载</td><td><div id="downloaded">0</div></td></tr>
				<tr><td>完成进度</td><td><div id="progressbar" style="float:left;width:1px;text-align:center;color:#FFFFFF;background-color:#0066CC"></div><div id="progressText" style=" float:left">0%</div></td></tr>
				</table>
				</div>
				<script type="text/javascript">
				//文件长度
				var filesize=0;
				function $(obj) {return document.getElementById(obj);}
				//设置文件长度
				function setFileSize(fsize) {
					filesize=fsize;
					$("filesize").innerHTML=fsize;
				}
				//设置已经下载的,并计算百分比
				function setDownloaded(fsize) {
					$("downloaded").innerHTML=fsize;
					if(filesize>0) {
						var percent=Math.round(fsize*100/filesize);
						$("progressbar").style.width=(percent+"%");
						if(percent>0) {
							$("progressbar").innerHTML=percent+"%";
							$("progressText").innerHTML="";
						} else {
							$("progressText").innerHTML=percent+"%";
						}
					}
				}
				</script>
				';
		}
	    //获取文件大小
	    //$oldfile=fopen ($this->m_url, "rb");
        $filesize = -1;
        $headers = get_headers($this->m_url, 1);
        if ((!array_key_exists("Content-Length", $headers))) $filesize=0;
        $filesize = $headers["Content-Length"];
        //不是所有的文件都会先返回大小的，有些动态页面不先返回总大小，这样就无法计算进度了
        if ($filesize != -1) echo "<script>setFileSize($filesize);</script>";//在前台显示文件大小

       $aimUrl = str_replace('', '/', $aimUrl);
       $aimDir = '';
       $arr = explode('/', $aimUrl);
       foreach ($arr as $str) {
         $aimDir .= $str . '/';
         if (!file_exists($aimDir)) {
            mkdir($aimDir);
         }
       }
	    /*//默认路径转换
		$folderdir = @iconv('UTF-8', 'gb2312', $folderdir);
		$folder = @iconv('UTF-8', 'gb2312', $folder);
		$file = @iconv('UTF-8', 'gb2312', $file);
		$destination_folderdir=$folderdir;
        if(!is_dir($destination_folderdir)) //判断根目录是否存在
        mkdir($destination_folderdir,0777); //若无则创建，并给与777权限 windows忽略
        $destination_folder=$folderdir.'/'.$folder;
        if(!is_dir($destination_folder)) //判断目录是否存在
        mkdir($destination_folder,0777); //若无则创建，并给与777权限 windows忽略
		$path= $folderdir.'/'.$folder.'/'.$file;*/
		$path=$aimUrl.'/'.$filename;
		$fp = fopen($path,"w") or die("写入文件 $path 失败！");
        //下载长度
        $downlen=0;
		while (!feof($this->m_fp)) {
			$data=fread($this->m_fp, 1024 * 128 );//默认获取128K
            $downlen+=strlen($data);//累计已经下载的字节数
			fwrite($fp, $data, 1024 * 128 );
            echo "<script>setDownloaded($downlen);</script>";//在前台显示已经下载文件大小
            //print str_repeat(" ",4096);
			//刷新缓存
			ob_flush();
            flush();
          }
		@fclose($fp);
		@fclose($this->m_fp);
		return true;
	}
	/**
	* 保存网页内容为 Text 文件
	*/
	public function SaveToText($savefilename) {
		if ($this->IsText()) {
			$this->SaveBinFile($savefilename);
		} else {
			return "";
		}
	}
	/**
	* 用 HTTP 协议获得一个网页的内容
	*/
	public function GetHtml() {
		if (!$this->IsText()) return "";
		if ($this->m_html!="") return $this->m_html;
		if (!$this->m_fp||@feof($this->m_fp)) return "";
		while(!feof($this->m_fp)) {
			$this->m_html .= fgets($this->m_fp,256);
		}
		@fclose($this->m_fp);
		return $this->m_html;
	}
	/**
	* 开始 HTTP 会话
	*/
	public function PrivateStartSession() {
		if (!$this->PrivateOpenHost()) {
			$this->m_error .= "打开远程主机出错!";
			return false;
		}
		if ($this->GetHead("http-edition")=="HTTP/1.1") {
			$httpv = "HTTP/1.1";
		} else {
			$httpv = "HTTP/1.0";
		}
		fputs($this->m_fp,"GET ".$this->m_urlpath." $httpv\r\n");
		fputs($this->m_fp,"Host: ".$this->m_host."\r\n");
		fputs($this->m_fp,"Accept: */*\r\n");
		fputs($this->m_fp,"User-Agent: Mozilla/4.0+(compatible;+MSIE+6.0;+Windows+NT+5.2)\r\n");
		#HTTP1.1协议必须指定文档结束后关闭链接,否则读取文档时无法使用feof判断结束
		if ($httpv=="HTTP/1.1") {
			fputs($this->m_fp,"Connection: Close\r\n\r\n");
		} else {
			fputs($this->m_fp,"\r\n");
		}
		$httpstas = fgets($this->m_fp,256);
		$httpstas = split(" ",$httpstas);
		$this->m_httphead["http-edition"] = trim($httpstas[0]);
		$this->m_httphead["http-state"] = trim($httpstas[1]);
		$this->m_httphead["http-describe"] = "";
		for ($i=2;$i<count($httpstas);$i++) {
			$this->m_httphead["http-describe"] .= " ".trim($httpstas[$i]);
		}
		while (!feof($this->m_fp)) {
			$line = str_replace("\"","",trim(fgets($this->m_fp,256)));
			if($line == "") break;
			if (ereg(":",$line)) {
				$lines = split(":",$line);
				$this->m_httphead[strtolower(trim($lines[0]))] = trim($lines[1]);
			}
		}
	}
	/**
	* 获得一个Http头的值
	*/
	public function GetHead($headname) {
		$headname = strtolower($headname);
		if (isset($this->m_httphead[$headname])) {
			return $this->m_httphead[$headname];
		} else {
			return "";
		}
	}
	/**
	* 打开连接
	*/
	public function PrivateOpenHost() {
		if ($this->m_host=="") return false;
		$this->m_fp = @fsockopen($this->m_host, $this->m_port, &$errno, &$errstr,10);
		if (!$this->m_fp){
			$this->m_error = $errstr;
			return false;
		} else {
			return true;
		}
	}
	/**
	* 关闭连接
	*/
	public function Close(){
		@fclose($this->m_fp);
	}
}
?>