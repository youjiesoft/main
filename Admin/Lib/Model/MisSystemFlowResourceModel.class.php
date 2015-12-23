<?php
/**
 * @Title: MisSystemFlowResourceModel 
 * @Package package_name 
 * @Description: 项目模板-资源获取页面
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-8-16 上午11:04:15 
 * @version V1.0
 */
class MisSystemFlowResourceModel extends CommonModel {
	protected $trueTableName = 'mis_system_flow_resource';

	
	/**
	 * @Title: intoProject
	 * @Description: todo(新建项目时将数据插入到新项目表结构中)
	 * @param checkArr,传入当前项目中，模板任务ID与项目任务id的比较结构
	 * @param projectid 项目ID
	 * @author yangxi
	 * @date 2014-10-21 上午11:00:00
	 * @throws
	 */
	public function  intoProject($checkArr,$projectid){
		//项目资源表
		$MPFRModel=D('MisProjectFlowResource');
		//print_R($checkArr);
		$data=array();//数据存储空数组
		$result=$this->select();
	    foreach($checkArr as $key => $val){
	        foreach($result as $subkey => $subval){
	        	//任务主KEY
	        	if($subval['id']==$val['systemformid']){	        		
	        		   $data[$key]=$subval;
	        		   $data[$key]['id']=$val['projectformid'];
	        		   $data[$key]['projectid']=$projectid;
	        	}
	        }
	    	
	    }
// 	    print_R($data);
// 	    exit;
	    //插入数据表
	    $MPFRModel->addAll($data);

	}
}
?>