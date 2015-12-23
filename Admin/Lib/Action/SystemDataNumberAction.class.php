<?php
/**
 * @Title:系统编码规则表
 * @Package 基类
 * @Description: 对系统内所有单据给予一个唯一编码并进行描述
 * @author 马世河
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2012年1月16日
 * @version V1.0
 */
class SystemConfigNumberAction extends CommonAction {
/**
	 * @Title: index 
	 * @Description: todo(重写CommonAction的index方法，展示列表) 
	 * @return string  
	 * @author 杨希
	 * @date 2013-5-31 下午3:59:44 
	 * @throws
	 */
    public function index(){
    	//动态配置列表项字段  包括：1、是否显示；2、是否排序；3、列宽度
		$scdmodel = D('SystemConfigDetail');
		$modelname = $this->getActionName();
		$detailList = $scdmodel->getDetail($modelname);
    	if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		//扩展工具栏操作
		$toolbarextension = $scdmodel->getDetail($modelname,true,'toolbar');
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
        $model=D('SystemConfigNumber');
        if(file_exists($model->GetFile())){
           require $model->GetFile();
        }
        $newAryRule=array();
        if($_REQUEST['jump']){
        	$modelName = $_REQUEST['model'];
        	$model = D($modelName);
        	$tablename = $model->getTableName();
        	foreach($aryRule as $k=>$v){
        		if($k==$tablename){
        			$newAryRule = $v;
        		}
        	}
        }else{
        	//$newAryRule = $aryRule;/*
         /* 建左边树
         */
	       $model = D('SystemConfigNumber');
	       $map2['isprojectwork'] = array('neq',1);
	       $list = $model->getRoleTree('SystemConfigNumberBox','',$map2);  
	       $firstDetail=$model->firstDetail;
	       $this->assign('check',$firstDetail['check']);     
	       $this->assign('returnarr',$list);
	       $modelName = $firstDetail['name'];
	       $modeltotable = D($firstDetail['name']);
	       $tablename=$modeltotable->getTableName();
	       foreach($aryRule as $k=>$v){
	       	if($k==$tablename){
	       		$newAryRule = $v;
	       	}
	       }   
        }
	    $titlemodel = D('Node');
	    $title = $titlemodel->where("name='{$modelName}'")->getField('title');
	    $newAryRule['rulename'] = $title;	
        $this->assign('id',$tablename);
        $this->assign('modelname',$modelName);
        $this->assign("vo",$newAryRule);
        if($_REQUEST['jump']){
        	$this->display('indexview');
        }else{
        	$this->display();
        }        
    }

    /**
     * @Title: update
     * @Description: todo(重写CommonAction的update方法，更新)
     * @return string
     * @author 马世河
     * @date 2013-5-31 下午3:59:44
     * @throws
     */    
    public function update() {
        $model=D('SystemConfigNumber');
        $id = $_POST['id'];
        if(file_exists($model->GetFile())){
            require $model->GetFile();
        }

				$aryRule[$id] = array(
		            'rulename'=>$_POST['rulename'],
		            'prefix1'=>$_POST['prefix1'],
					'prefix1_value'=>$_POST['prefix1_value'],
					'prefix1_long'=>$_POST['prefix1_long'],
		            'prefix2'=>$_POST['prefix2'],
					'prefix2_value'=>$_POST['prefix2_value'],
					'prefix2_long'=>$_POST['prefix2_long'],
		            'prefix3'=>$_POST['prefix3'],
					'prefix3_value'=>$_POST['prefix3_value'],
					'prefix3_long'=>$_POST['prefix3_long'],
					'prefix4'=>$_POST['prefix4'],
					'prefix4_value'=>$_POST['prefix4_value'],
					'prefix4_long'=>$_POST['prefix4_long'],						
		            'num'    =>$_POST['num'],
					'suffix' =>$_POST['suffix'],
					'rule' =>$_POST['rule'],
					'preview' =>$_POST['preview'],
		            'writable'=>$_POST['writable'],
		            'status'=>$_POST['status'],	           
		        );

        $model->SetRules($aryRule);
        $this->success ( L('_SUCCESS_') );
    }
}
?>