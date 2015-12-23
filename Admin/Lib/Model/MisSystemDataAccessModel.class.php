<?php
//Version 1.0
/**
 * @Title: MisSystemDataAccessModel 
 * @Package package_name
 * @Description: todo(数据权限控制) 
 * @author谢友志 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015-6-1 下午2:59:13 
 * @version V1.0
 */
class MisSystemDataAccessModel extends CommonModel{
    protected $trueTableName = 'mis_system_data_access_mas';

    public $_auto	=array(
    		array('createid','getMemberId',self::MODEL_INSERT,'callback'),
    		array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
    		array('createtime','time',self::MODEL_INSERT,'function'),
    		array('updatetime','time',self::MODEL_UPDATE,'function'),
    		array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
    		array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
    		array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),    		
    );
    public function getCompanyOne(){
    	$companyid=$this->where("status=1")->getField("id");
    	return $companyid;
    }
  	/**
  	 * @Title: fieldConfigToList
  	* @Description: todo(查询字段配置信息后查询配置对应的数据)
     * @param $id string 数据id（mis_system_data_access_mas）
  	 * @return multitype:  
  	 * @author 谢友志 
  	 * @date 2015-6-6 下午5:47:15 
  	 * @throws
  	 */
    public function fieldConfigToList($id){
    	//读取权限控制字段信息（父记录）
    	$masmodel = M("mis_system_data_access_mas");
    	$maslist = $masmodel->where("id={$id}")->find();
    	//将有用的信息带入页面
    	$list = array();
    	//查询该字段配置
    
    	$dymodel = M("mis_dynamic_form_manage");
    	$promodel = M("mis_dynamic_form_propery");
    	$datamodel = M("mis_dynamic_form_datatable");
    	//字段可能为内嵌表字段、主表字段，分开处理
    	$listsource = '';//字段配置来源（数据表或配置文件）
    	$sourcesave = '';//用于存储的来源字段
    	$sourceshow ='';//用于显示的来源字段
    	$sourcetype = 2; //来源类型(来源类型 1配置文件 2数据表)
    	if($maslist['type'] == 2){ //内嵌表字段
    		//查询该字段配置
    		$dmap['formid'] = $maslist['formid'];
    		$dmap['propertyid'] = $maslist['propertyid'];
    		$dmap['fieldname'] = $maslist['field'];
    		$datars = $datamodel->where($dmap)->find();
    		$config = unserialize(base64_decode($datars['config']));
    		
    		//print_r($config);
    		//获取对应数据
    		
    		switch ($config['type']){
    			case "lookup":
    				$LookupModel = D("LookupObj");
    				$lpdetails = $LookupModel->GetLookupDetail($config['parame'][0]);
    				$lpmodelname = $lpdetails['mode'];
    				$lpmodel = D($lpmodelname);
    				$list = $lpmodel->getField("{$lpdetails['val']},{$lpdetails['filed']}");
    				$listsource = $lpmodel->getTableName();
    				$sourcesave = $lpdetails['val'];
    				$sourceshow = $lpdetails['filed'];
    				$sourcetype = 2; 
    				break;
    					
    			case "select":
    				//对应数据表
    				if($config['datasouce']==1){
    					$smodel = M($config['datasouceparame'][0]);
    					$list = $smodel->getField("{$config['datasouceparame'][2]},{$config['datasouceparame'][1]}");
    					$listsource = $config['datasouceparame'][0];
	    				$sourcesave = $config['datasouceparame'][2];
	    				$sourceshow = $config['datasouceparame'][1];
	    				$sourcetype = 2; 
    				}
    				//对应配置文件
    				else{
    					$dmodel = D("Selectlist");
    					$ddetails = require $dmodel->getFile();
    					$list = $ddetails[$config['datasouceparame'][0]][$config['datasouceparame'][0]];
    					$listsource = 'selectlist.inc.php';
	    				$sourcesave = $config['datasouceparame'][0];
	    				$sourceshow = '';
	    				$sourcetype = 1; 
    				}
    				break;
    					
    			default:
    				break;
    		}
    		//print_r($list);
    	}else{//主表字段
    		$pmap['formid'] = $maslist['formid'];
    		$pmap['id'] = $maslist['propertyid'];
    		$pmap['fieldname'] = $maslist['field'];
    		$prolist = $promodel->where($pmap)->find();
    		//print_r($prolist);
    		if($prolist['category']=='lookup'){
    			$LookupModel = D("LookupObj");
    			$lpdetails = $LookupModel->GetLookupDetail($prolist['lookupchoice']);
    			//print_r($lpdetails);
    			$lpmodelname = $lpdetails['mode'];
    			if($lpdetails['viewname']){
    				$viewmodel = D("MisSystemDataviewMasView");
    				$viewlist = $viewmodel->where("mis_system_dataview_mas.status=1 and mis_system_dataview_sub.status=1 and mis_system_dataview_mas.name='".$lpdetails['viewname']."'")->select();
    				//ECHO $viewmodel->getlastsql();
    				foreach($viewlist as $viewk=>$viewv){
    					if($viewv['otherfield'] == $lpdetails['filed']){
    						$vshowfieldarr = explode(".",$viewv['field']);
    						$vshowtable = $vshowfieldarr[0];
    						$vshowfield = $vshowfieldarr[1];
    					}
    					if($viewv['otherfield'] == $lpdetails['val']){
    						$vsavefieldarr = explode(".",$viewv['field']);
    						$vsavetable = $vsavefieldarr[0];
    						$vsavefield = $vsavefieldarr[1];
    					}
    				}
    				//if($vshowtable == $vsavetable){
    					$lpmodel = M($vsavetable);
    					$list = $lpmodel->getField("{$vsavefield},{$vshowfield}");
    					$listsource = $lpmodel->getTableName();
    					$sourcesave = $vsavefield;
    					$sourceshow = $vshowfield;
//     				}else{
//     					$lpmodel = D($lpmodelname);
//     					$list = $lpmodel->getField("{$lpdetails['val']},{$lpdetails['filed']}");
//     					$listsource = $lpmodel->getTableName();
//     					$sourcesave = $lpdetails['val'];
//     				}
    			}else{
    				$lpmodel = D($lpmodelname);
    				$list = $lpmodel->getField("{$lpdetails['val']},{$lpdetails['filed']}");
    				$listsource = $lpmodel->getTableName();
    				$sourcesave = $lpdetails['val'];
    				$sourceshow = $lpdetails['filed'];
    			}    			
    			$sourcetype = 2;
    		}else{
    			if($prolist['showoption']){
    				//对应配置文件
    				$secmodel = D("Selectlist");
    				$sellist = require $secmodel->GetFile();    				
    				$list = $sellist[$prolist['showoption']][$prolist['showoption']];
    				$listsource = 'selectlist.inc.php';
    				$sourcesave = $prolist['showoption'];
    				$sourceshow = '';
    				$sourcetype = 1;
    			}else if($prolist['subimporttableobj']){
    				//对应数据表
    				$seltable = $prolist['subimporttableobj'];
    				$selmodel = D($seltable);
    				$list = $selmodel->getField("{$prolist['subimporttablefield2obj']},{$prolist['subimporttablefieldobj']}");
    				$listsource = $seltable;
    				$sourcesave = $prolist['subimporttablefield2obj'];
    				$sourceshow = $prolist['subimporttablefieldobj'];
    				$sourcetype = 2;
    			}else if($prolist['treedtable']){
    				//树形下拉
    				$seltable = $prolist['treedtable'];
    				$selmodel = D($seltable);
    				$list = $selmodel->getField("{$prolist['treevaluefield']},{$prolist['treeshowfield']},{$prolist['treeparentfield']},id");
    				$listsource = $seltable;
    				$sourcesave = $prolist['treevaluefield'];
    				$sourceshow = $prolist['treeshowfield'];
    				$sourcetype = 2;
    				$treetype=$prolist['treeparentfield'];
    			}
    		}
    	}
    	$newlist['list'] = $list;
    	$newlist['listsource'] = $listsource;
    	$newlist['sourcesave'] = $sourcesave;
    	$newlist['sourceshow'] = $sourceshow;
    	$newlist['sourcetype'] = $sourcetype;
    	$newlist['treetype'] = $treetype;
    	return $newlist;
    }
    
}
?>