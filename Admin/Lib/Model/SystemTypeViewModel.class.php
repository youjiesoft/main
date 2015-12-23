<?php
//Version 1.0
class SystemTypeViewModel extends CommonModel {

 	public function SetType($data=array()){
        $filename=  $this->GetFile();
        $this->writeover($filename,"\$aryType = ".$this->pw_var_export($data).";\n",true);
    }

    public function GetTypelistValue(){
        $value = '';
        if(file_exists($this->GetFile())){
            require $this->GetFile();
            $value = $aryType;
        }
        return $value;
    }
    public  function GetFile(){
        return  DConfig_PATH."/System/typelist.inc.php";
    }
}
?>