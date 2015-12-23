<?php
//Version 1.0
/**
 * Description of MisAttachedRecordAction
 *
 * @author mashihe
 */
class MisAttachedRecordAction extends CommonAction{
	/**
	 * @Title: _filter 
	 * @Description: todo(构造检索条件) 
	 * @param HASHMAP $map  
	 * @author  
	 * @date 2013-5-31 下午4:01:22 
	 * @throws 
	*/ 
	public function _filter(&$map) {
		if ($_SESSION["a"] != 1)$map['status']=array("gt",-1);
	}
	/**
	 * @Title: search
	 * @Description: todo()   
	 * @author  
	 * @date 2013-5-31 下午4:21:10 
	 * @throws 
	*/ 
    public function search(){
    	$type = base64_decode($_REQUEST['type']);
    	$id= base64_decode($_REQUEST['orderid']);
    	if($type) $map['type']=$type;
    	if($id) $map['orderid']=$id;
        if($_POST['orderid']){
          $map['orderid']=$_POST['orderid'];
        }
        if($_POST['type']){
            $map['type']=$_POST['type'];
        }
        //是否显示操作
        if(isset($_REQUEST['view']) && $_REQUEST['view'] == 1){
        	$this->assign('view',1);
        }else{
        	$this->assign('view',0);
        }
        $map['status'] =array("gt",-1);
        $name="mis_attached_record";
		$this->_list($name,$map);
		$this->assign('orderid',$id);
		$this->assign('type',$type);
        $model1=D('SystemTypeView');
        include_once $model1->GetFile();
        $this->assign("aryType",$aryType);
        $this->display();
    }
	/**
	 * @Title: _empty 
	 * @Description: todo(判断页面是否存在函数)   
	 * @author  
	 * @date 2013-5-31 下午3:59:09 
	 * @throws 
	*/ 
    public function _empty(){
	   //空操作
	    $this->error("您访问的页面不存在！");
    }
	/**
	 * @Title: edit 
	 * @Description: todo(打开页面函数)   
	 * @author  
	 * @date 2013-5-31 下午3:59:09 
	 * @throws 
	*/ 
    public function edit(){
		$id=$_REQUEST['id'];
    	$model1=D('SystemTypeView');
        include_once $model1->GetFile();

    	$model=D("MisAttachedRecord");
		$list=$model->where('status = 1 and id='.$id)->find();
		$modelname="";
		$orderno="";
		foreach($aryType as $k1=>$v1){
        	if($list['type'] == $v1['type']){
        		$modelname=$v1['typename'];
				$name=$v1['modelname'];
				$model2=D($name);
        		$orderno=$model2->where('id='.$list['orderid'])->getField("orderno");
        	}
    	}
		$this->assign("modelname",$modelname);
		$this->assign("orderno",$orderno);
		$this->assign("vo",$list);
		$this->display();
	}
}
?>
