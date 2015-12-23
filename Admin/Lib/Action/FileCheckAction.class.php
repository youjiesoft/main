<?php
/**
 * @Title: FileCheckAction 
 * @Package package_name
 * @Description: todo(文件检测) 
 * @author laicaixia
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-6-2 上午10:39:55 
 * @version V1.0 
*/ 
class FileCheckAction extends CommonAction {
    /**
     * @Title: checkaction 
     * @Description: todo(ACTION检测)   
     * @author laicaixia 
     * @date 2013-6-2 上午10:40:03 
     * @throws 
    */  
    public function checkaction(){
        $basedir=ROOT."/Lib/Action/"; //修改此行为需要检测的目录，点表示当前目录
        $auto=0; //是否自动移除发现的BOM信息。1为是，0为否。
        //以下不用改动
        $aryfile=array();
        if ($dh =opendir($basedir)) {
            while (($file = readdir($dh)) !== false) {
                if ($file!='.' && $file!='..' && !is_dir($basedir."/".$file)) {
                    $aryfile[]=array(
                        "bom"=>$this->checkBOM($basedir.$file),
                        "file"=>$file,
                        "edittime"=>filemtime($basedir.$file)
                        );
                }
            }
            closedir($dh);
        }
        $this->assign("file",$aryfile);
        $this->display();
    }
    /**
     * @Title: checkmodel 
     * @Description: todo(MODEL检测)   
     * @author laicaixia 
     * @date 2013-6-2 上午10:41:46 
     * @throws 
    */  
    public function checkmodel(){
        $basedir=ROOT."/Lib/Model/"; //修改此行为需要检测的目录，点表示当前目录
        $auto=0; //是否自动移除发现的BOM信息。1为是，0为否。
        //以下不用改动
        $aryfile=array();
        if ($dh = opendir($basedir)) {
            while (($file = readdir($dh)) !== false) {
                if ($file!='.' && $file!='..' && !is_dir($basedir."/".$file)) {
                    $aryfile[]=array(
                        "bom"=>$this->checkBOM($basedir.$file),
                        "file"=>$file,
                        "edittime"=>filemtime($basedir.$file)
                        );
                }
            }
            closedir($dh);
        }
        $this->assign("file",$aryfile);
        $this->display();
    }
	/**
	 * @Title: checkBOM 
	 * @Description: todo(检测BOM) 
	 * @param 文件名 $filename
	 * @return string  
	 * @author laicaixia 
	 * @date 2013-6-2 上午10:41:06 
	 * @throws 
	*/  
	private function checkBOM($filename) {
        global $auto;
        $contents=file_get_contents($filename);
        $charset[1]=substr($contents, 0, 1);
        $charset[2]=substr($contents, 1, 1);
        $charset[3]=substr($contents, 2, 1);
        if (ord($charset[1])==239 && ord($charset[2])==187 && ord($charset[3])==191) {
            if ($auto==1) {
            $rest=substr($contents, 3);
            $this->rewrite($filename, $rest);
            return "BOM found, automatically removed.";
            } else {
            return "存在";
            }
        }
        else return "不存在";
    }
    function rewrite ($filename, $data) {
        $filenum=fopen($filename,"w");
        flock($filenum,LOCK_EX);
        fwrite($filenum,$data);
        fclose($filenum);
    }
}
?>
