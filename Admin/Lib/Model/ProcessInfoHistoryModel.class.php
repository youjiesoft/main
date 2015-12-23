<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: 流程记录模型
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-9-12 下午2:40:37 
 * @version V1.0
 */
class ProcessInfoHistoryModel extends CommonModel {
        protected $trueTableName = 'process_info_history';

        public $_auto	=array(
			array('userid','getMemberId',self::MODEL_BOTH,'callback'),
        	array('ordercreateid','getMemberId',self::MODEL_BOTH,'callback'),
			array('dotime','time',self::MODEL_INSERT,'function'),
	        array('createid','getMemberId',self::MODEL_INSERT,'callback'),
	        array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
	        array('createtime','time',self::MODEL_INSERT,'function'),
	        array('updatetime','time',self::MODEL_UPDATE,'function'),
           array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),

        		
		);
        /**
         * @Title: addProcessInfoHistory
         * @Description: todo(表单流程历史记录) 
         * @param string $tablename 模型名称
         * @param int $tableid 单据ID
         * @param int $ostatus 流程当前审核节点
         * @return boolean  
         * @author 黎明刚
         * @date 2014年12月1日 下午8:29:09 
         * @throws
         */
        public function addProcessInfoHistory($tablename,$tableid,$ostatus =0){
        	$data = array();
        	//带有审批流。新增成功后，进行对流程历时记录进行插入
        	//查询当前这条记录
        	$list=D($tablename)->where("id = ".$tableid)->find();
        	if($list){
        		if($ostatus){
        			//获取流程审批节点信息
        			$process_relation_formDao = M("process_relation_form");
        			$where = array();
        			$where['id'] = $ostatus;
        			$document = $process_relation_formDao->where($where)->getField("document");
        		}
        		//1、从单据本身获取信息
	        	$data['tablename'] = $tablename;//单据对应表名称
	        	$data['tableid'] = $tableid;//单据对应表ID
	        	$data['orderno'] = $list['orderno'];//单据号
	        	$data['document']=$document?$document:0;
	        	
	        	$data['ostatus'] = $ostatus;//流程节点
	        	$data['ptmptid'] = $list['ptmptid'];//流程ID
	        	$data['ordercreateid'] = $list['createid']; //建单人
	        	
	        	//$data['auditstatus'] =  $list['auditstatus'];//前当审核状态
	        	
	        	$data['projectid'] = $list['projectid'];
	        	$data['projectworkid'] = $list['projectworkid'];
	        	//2、审批意见，传值获取	        	
	        	$data['dotype'] =  $_REQUEST['dotype'];//处理状态
	        	$data['dotime'] = time();//处理时间
	        	$data['doinfo'] =  $_REQUEST['doinfo'];//处理意见
	        	//3、自动获取
	        	$data['userid'] = $_SESSION[C('USER_AUTH_KEY')];//当前处理人	
				$data['createid'] = $_SESSION[C('USER_AUTH_KEY')];
				$data['createtime'] = time();
				$data['companyid'] =  $_SESSION['companyid'];
				$data['departmentid'] = $_SESSION['user_dep_id'];
				$result=$this->add($data);
				if($result===false){
					return false;
				}else{
					return true;
				}
        	}else{
			    return false;
        	}
        }
        public function addInfoHistory($list){
        	//带有审批流。新增成功后，进行对流程历时记录进行插入
        	$data = array();
        	$data['pid'] = $list['pid'];
        	$data['ostatus'] = $list['ostatus'];
        	$data['tablename'] = $list['tablename'];
        	$data['tableid'] = $list['tableid'];
        	$data['auditstatus'] =  $list['auditstatus'];
        	$data['dotype'] =  $list['dotype'];
        		
        	$data['userid'] = $_SESSION[C('USER_AUTH_KEY')];
        	$data['ordercreateid'] = $_SESSION[C('USER_AUTH_KEY')];
        	$data['dotime'] = time();
        	$data['createid'] = $_SESSION[C('USER_AUTH_KEY')];
        	$data['createtime'] =  time();
        	$result=$this->add($data);
        	return $result;
        }
        
        private function inProcess($data){
        	
        	 
        }
                
        private function overProcess($data){
        	//0、更新项目任务
        	//如果存在项目编号跟项目任务，而且审核状态为审核完毕
        	if ($data['projectid'] && $data['projectworkid'] ) {
        		$MSFFModel = D ( 'MisProjectFlowForm' );
        		$MSFFModel->setWorkComplete ( $data ['projectworkid'],$data['projectid'] );
        		// 这里不报错，如果报错的话会有很多影响，如果没更新状态成功，那需要他人为再去更新状态
        	}       	
        }
}
?>