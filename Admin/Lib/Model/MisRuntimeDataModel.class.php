<?php
//Version 1.0
// 写入快速缓存
class MisRuntimeDataModel extends CommonModel {

	public function getRuntimeCache($modelname,$type){
		$uid = $_SESSION[C('USER_AUTH_KEY')];
        $data = F($uid.'_'.$modelname.'_'.$type);
        return $data;
    }

    public function setRuntimeCache($data,$modelname,$type){
    	$uid = $_SESSION[C('USER_AUTH_KEY')];
		$path = F($uid.'_'.$modelname.'_'.$type,$data,DATA_PATH);
    }
}
?>