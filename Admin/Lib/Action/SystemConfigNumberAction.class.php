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
        if($_REQUEST['jump']){
        	$modelName = $_REQUEST['model'];
        	$getModel = D($modelName);
        	$tablename = $getModel->getTableName();
        	$newAryRule = $model->GetRules($tablename, $modelName);
        }else{
        	//$newAryRule = $aryRule;/*
         /* 建左边树
         */
// 	       $map2['isprojectwork'] = array('neq',1);
// 	      $list = $model->getRoleTree('SystemConfigNumberBox','',$map2);  
	       $list = $model->getRoleTree('SystemConfigNumberBox');
	       $firstDetail=$model->firstDetail;
	       $this->assign('check',$firstDetail['check']);     
	       $this->assign('returnarr',$list);
	       $modelName = $firstDetail['name'];
	       $modeltotable = D($firstDetail['name']);
	       $tablename=$modeltotable->getTableName();
	       $newAryRule = $model->GetRules($tablename, $modelName);
        }
	    $titlemodel = D('Node');
	    $title = $titlemodel->where("name='{$modelName}'")->getField('title');
	    $newAryRule['rulename'] = $title;	
	    //获取字段
	    $fieldList=$this->lookupcomboxgetTableField($modelName);
	    $tableList=$this->lookupgetTables();
	    if($newAryRule['fieldtable']){
		    $tabFieldList=$this->lookupcomboxgetTableField($newAryRule['fieldtable']);
	    }else{
	    	$tabFieldList=null;
	    }
	    $this->assign('tableList',$tableList);
	    $this->assign('fieldList',$fieldList);
	    $this->assign('tabFieldList',$tabFieldList);
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
        $status = (empty($_POST['status']) || is_null($_POST['status'])) ? 0 : $_POST['status'];
        $classify=$_POST['classify']?$_POST['classify']:0;
		$aryRule = array(
            'table'=>$_POST['id'],
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
			'numshow'    =>$_POST['numshow'],
			'numnew'    =>$_POST['numnew'],
			'suffix' =>$_POST['suffix'],
			'rule' =>$_POST['rule'],
			'preview' =>$_POST['preview'],
            'writable'=>$_POST['writable'],
            'status'=>$status,
            'modelname'=>$_POST['modelname'],
			'classify'=>$classify,
			'typefield'=>$_POST['typefield'],
			'fieldtable'=>$_POST['fieldtable'],
			'correlationfield'=>$_POST['correlationfield']
		);
		$orderno = "";
	    //进行规则实例
		$SystemConfigNumberModel = D("SystemConfigNumber");
		//前缀一
		if($aryRule['prefix1'])	$orderno.=$SystemConfigNumberModel->typeCheck($aryRule['prefix1'],$aryRule['prefix1_value'],$aryRule['prefix1_long']);
		//前缀二
		if($aryRule['prefix2'])	$orderno.=$SystemConfigNumberModel->typeCheck($aryRule['prefix2'],$aryRule['prefix2_value'],$aryRule['prefix2_long']);
		//前缀三
		if($aryRule['prefix3'])	$orderno.=$SystemConfigNumberModel->typeCheck($aryRule['prefix3'],$aryRule['prefix3_value'],$aryRule['prefix3_long']);
		//前缀四
		if($aryRule['prefix4'])	$orderno.=$SystemConfigNumberModel->typeCheck($aryRule['prefix4'],$aryRule['prefix4_value'],$aryRule['prefix4_long']);
		
	   $aryRule['oldrule'] = $orderno;
		
       $listInfo= $model->SetRules($aryRule);
       $aryRule['masid']=$listInfo;
       $classifyModel=M('mis_system_config_orderno_classify');
       if($classify==1){
      	 	//获取类型下的所有数据
       		$typeList=M($_POST['fieldtable'])->field($_POST['correlationfield'])->select();
       		foreach ($typeList as $k=>$v){
       			$aryRule['fieldval']=$v[$_POST['correlationfield']];
       			$condition = array('masid'=>array('eq', $aryRule['masid']), 'fieldval'=>array('eq', $aryRule['fieldval']));
       			$vo=$classifyModel->where($condition)->find();
       			if($vo){
       				unset($aryRule['numshow']);
       				unset($aryRule['numnew']);
       				if($vo['oldrule'] != $aryRule['oldrule']){
       					//进行验证 规则1-4是否有变化，如果变化。直接将流水号重置
       					$aryRule['numshow'] = 0;
       					$aryRule['numnew'] = 1;
       				}
       				$list=$classifyModel->where($condition)->data($aryRule)->save();
       			}else{
       				$list=$classifyModel->add($aryRule);
       			}
       		}
       }else{
       	$condition = array('masid'=>array('eq', $aryRule['masid']));
       	$classifyModel->where($condition)->delete();
       }
        $this->success ( L('_SUCCESS_') );
    }
    /**
     * @Title: lookupDatatableOrderNo
     * @Description: todo(datatable取单据编号)
     * @return string
     * @author yangxi
     * @date 2015-3-11 上午10：:3
     * @throws
     */    
    public function lookupDatatableOrderNo($actionName){
    	$modelName=$actionName;
    	$tableName=D($modelName)->getTableName();
    	$ordernoInfo=D('SystemConfigNumber')->getOrderno($tableName,$modelName); 
    	return $ordernoInfo;	  
    }
    public function tablefields(){
    	$promodel = M("mis_dynamic_form_propery");
    	$map['formid'] = getFieldBy($_POST['modelname'],'actionname','id','mis_dynamic_form_manage');    	
    	$map['status'] = array('eq',1);
    	$map['category'] = array('neq','datatable');
    	//$list = $promodel->field("id,fieldname,title")->where($map)->select();
    	$list=$this->lookupcomboxgetTableField($_POST['modelname']);
    	$this->assign("list",$list);
    	$this->assign("objindex",$_POST['objindex']);
    	$this->display();
    }
    /**
     * @Title: lookupgetTables
     * @Description: todo(获取当前所有数据表信息)
     * @author quqiang
     * @date 2014-8-18 下午4:54:35
     * @throws
     */
    public function lookupgetTables($iscur=true){
    	$model3=M();
    	$tables = $model3->query("SELECT `TABLE_NAME`,`TABLE_COMMENT` FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '".C('DB_NAME')."' ");//limit 1
    	$tables2=array();
    	foreach($tables as $k=>$v){
    		$k=$v['TABLE_NAME'];
    		$v=$v['TABLE_COMMENT'];
    		if($v==""){
    			$tables2[$k]=$k;
    		}else{
    			$v=explode("; InnoDB",$v);
    			$tables2[$k]=$v[0];
    			if( count(explode("InnoDB",$v[0])) >1 ){
    				$tables2[$k]=$k;
    			}
    		}
    	}
    	return $tables2;
    	/* if($iscur){
    		echo json_encode($tables2);
    	}else{
    		return $tables2;
    	} */
    }
     function lookupgetCorrelationField(){
     	$table=$_REQUEST['table'];
     	$fieldList=$this->lookupcomboxgetTableField('',$table);
     	echo json_encode($fieldList);
     }
    
}
?>