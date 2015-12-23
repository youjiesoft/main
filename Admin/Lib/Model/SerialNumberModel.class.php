<?php
/**
 * Description of SerialNumberModel
 *
 * @author Administrator
 */
class SerialNumberModel extends CommonModel{
    protected $autoCheckFields = false;
    //put your code here
    public function Set($data=array(),$key=""){
        $filename=  $this->Get();
        $this->writeover($filename,"\$Serialnumber =".$this->pw_var_export($data).";\n\$key=".$this->pw_var_export($key).";\n",true);
    }
    public  function Get(){
        return  DConfig_PATH."/System/Serialnumber.inc.php";
    }
    public function CheckFile(){
        import('@.ORG.tmlCrypt');
        $filename= $this->Get();
        $check=$system=array();
        if(file_exists($filename)){
            require($filename);
            $crypt= new tmlCrypt($key);
            $Serialnumber=$crypt->decrypt($Serialnumber);
            $check=unserialize($Serialnumber);
            
            if($Serialnumber=="0"){//校验序列号是否为空
                $system['length']=1;
            }
            if ($check['key']&&$key!=$check['key']) {//校验序列号是否一致
                $system['key']=1;
            }
            if ($check['version']&&C('Version')!=$check['version']) {//校验版本号是否一致
                $system['version']=1;
            }
            if ($check['pcode']&&C('pcode')!=$check['pcode']) {//校验产品编码是否一致
                $system['pcode']=1;
            }
            if ($check['fc']&&($check['fc']+60)<filectime($filename)) {//校验文件是否被篡改
                $system['fc']=1;
            }
            if ($check['software']&&C('software')!=$check['software']) {//校验软件形式
                $system['software']=1;
            }
            if ($check['expired']&&!$this->checktimelimit($check['expired'])) {//校验是否过期
                $system['expired']=1;
            }
            if ($check['online']&&!$this->Checkonline($check['online'])) {//校验并发数
                $system['online']=1;
            }
        }  else {
            $system['file']=1;
        }
        return $system;
    }
    /*
     *检查并发数
     */
    public  function Checkonline($check=""){
        //获取产品模块授权培
        $model=D("UserOnline");
        //获取并发数
        $online=$model->getOnLineCount();
        if($check<$online){
            return false;
        }
        else {
            return true;
        }
    }
    /*
     * 远程获取序列号
     */
    public function remote($key=""){
        $action = C ( 'System_update' );
        $this->Set(0);
        $filename=  $this->Get();
        $nodemodule=D("Node");
        $map['level']=array("neq",4);
        $map['type']=array("neq",4);
        $modulelist=$nodemodule->where($map)->getField("name",true);
        $modulelist = implode("##@@",$modulelist);
        $formvars=array(
            'key'=>$key,
            'version'=>C ( 'Version' ),
            'pcode'=>C ( 'pcode' ),
            'fc'=>  filectime($filename),
            'software'=>C ( 'Software' ),
            'localhostmodule'=> $modulelist
        );
        import('@.ORG.function_filesock', '', $ext='.php');
        $re = dfsockopen( $action,0,$formvars);
        if($re ){
            $this->Set($re,$key);
            return true;
        }else{
            return false;
        }
    }
    /*
     * 检查系统授权模块
     *
     */
    public function checkModule(){
//         import ( '@.ORG.tmlCrypt' );
//         $filename= $this->Get();
//         $check=$system=array();
//         if(file_exists($filename)){
//             require($filename);
//             $crypty= new tmlCrypt($key);
//             $Serialnumber=$crypty->decrypt($Serialnumber);
//             $check=unserialize($Serialnumber);
//             if($check['module']){
//                 $system=$check['module'];
//             }
//         }
        //排除密钥授权
    	$map=array();
		$map['status']=1;
		$map['level']=array("lt",4);
		$node=D('Node');
		$system="";
		$result=$node->where($map)->getField("name",true);
		$system = implode(",",$result);
        //这里来自动生成标识
        return $system;
    }
    /*
     * 获取使用时间
     * 默认15天授权
     */
    public function checktimelimit($check=''){
        if($check<time()){
            return false;
        }
        else {
            return true;
        }
    }
    /*
     * 获取公司信息
     *
     */
    public function getusername($check=""){

    }
    /*
     * 获取产品编号
     */
    public function getproductCode($key="",$Serialnumber=""){

    }
   /*
    * 获取产品使用方式
    */
    public function getusemethod($key="",$Serialnumber=""){

    }
    /*
     * 解码授权信息
     */
    public function Authorize(){
        import ( '@.ORG.tmlCrypt' );
        $filename= $this->Get();
        $system=array();
        if(file_exists($filename)){
            require_once($filename);
            $crypty= new tmlCrypt($key);
            $Serialnumber=$crypty->decrypt($Serialnumber);
            $system=unserialize($Serialnumber);
        }
        return $system;
    }
}
?>