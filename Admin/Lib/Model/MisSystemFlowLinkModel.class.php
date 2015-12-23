<?php
/**
 * @Title: MisSystemFlowLinkModel
 * @Package 项目模板-任务间链接：模型类
 * @Description: TODO(项目节点与任务)
 * @author yangxi
 * @company 重庆特米洛科技有限公司˾
 * @copyright 重庆特米洛科技有限公司˾
 * @date 2014-10-18 19:18:54
 * @version V1.0
 */
class MisSystemFlowLinkModel extends CommonModel {
	protected $trueTableName = 'mis_system_flow_link';
	public $_auto =array(
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
			
			array('begintime','dateToTimeString',self::MODEL_BOTH,'callback'),
			array('endtime','dateToTimeString',self::MODEL_BOTH,'callback'),
			array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
			
	);
	
	//关联数组记录
	private $info=array();
	//关联前置任务
	private $predecessorid="";
	//关联后置任务
	private $successorid="";

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
		$data=array();//数据存储空数组
		//项目LINK表
		$MPFLModel=D('MisProjectFlowLink');
		$result=$this->select();
		//多次循环比较后压数组
	    foreach($checkArr as $key => $val){
	        foreach($result as $subkey => $subval){
	        	//任务主KEY
	        	if($subval['id']==$val['systemformid']){
	        		//当前ID 
	        		$data[$key]['id']=$val['projectformid'];
	        		$data[$key]['type']=$subval['type'];
	        		$data[$key]['projectid']=$projectid;
	        	    //前置任务
	        	    if($subval['predecessorid']!==0){
	        	    	//重新去寻找
		        	    foreach($checkArr as $subkey1 => $subval1){
			        		if($subval['predecessorid']==$subval1['systemformid']){
			        			$data[$key]['predecessorid']=$subval1['projectformid'];
			        			break;
			        		}else{
			        			$data[$key]['predecessorid']=0;
			        		}
		        	    }
	        	    }else{
			        	$data[$key]['predecessorid']=0;
			        }
	        		//后置任务
	        	    if($subval['successorid']!==0){
	        	    	//重新去寻找
		        	    foreach($checkArr as $subkey2 => $subval2){
			        		if($subval['successorid']==$subval2['systemformid']){
			        			$data[$key]['successorid']=$subval2['projectformid'];
			        			break;
			        		}else{
			        			$data[$key]['successorid']=0;
			        		}
			        		
		        	    }
	        	    }else{
	        			$data[$key]['successorid']=0;
	        		}
	        	}
	        }
	    }
	    $bool = true;
	    //插入数据表
	    foreach($data as $k=>$v){
	    	$bool = $MPFLModel->add($v);
	    	if(!$bool){
	    		$bool = false;
	    		break;
	    	}
	    }
	    return $bool;
	    //$MPFLModel->addAll($data);
	    //echo $MPFLModel->getlastSql();
	}
	/**
	 * @Title: setLinkWork 
	 * @Description: todo(增加一行新任务时重新对任务关联结构做变动) 
	 * @param linkInfo当前任务节点信息，包含自身，前置节点的一维数组，
	 * linkInfo=array('id'=>'当前新增的任务节点'，'link'=>'被绑定的任务节点')
	 *  $type类型 分为增加前置节点1,增加后置节点2，修改3，删除4， 
	 * @author yangxi 
	 * @date 2014-10-21 上午11:00:00
	 * @throws
	 */
	function setLinkWork($linkid,$linkInfo,$type){
		switch ($type){
			case "1":
				//删除之前信息记录
				$map=array();
				$map['id']=$linkid;
				$this->where($map)->delete();
				//插入前置信息
				$data=array();
				$data['id']            = $linkid;
				$data['predecessorid'] = $linkInfo;
				$this->add($data);			
				break;
		}
/*****************************以下按标准任务处理，不实用，暂留存底******************************/		
// 		switch ($type){
// 			case "1":
// 			//首先获取前后置任务列表
// 	        $result=$this->getLinkWork($linkInfo['link']);
// 	        //对$linkInfo['pro']做专置为数组
// 	        //初始化一个空数组
// 	        $predecessoridArr=array();
// 	        $predecessoridArr=explode(',',$linkInfo['link']);
// 	        $data=array();
// 	        //执行事务
// 	        $Model->startTrans();
// 	        foreach ($predecessoridArr as $key => $val){
// 	        	//新数据的插入
// 	        	$data[$key]['id']=$linkInfo['id'];
// 	        	$data[$key]['predecessorid']=$val;
// 	        	//如果存在节点
// 	        	if($result){
// 		        	//如果有后置节点，替换后置节点
// 		        	foreach($result as $subkey=>$subval){
// 		        		if($subval[id]==$val){
// 		        			$data[$key]['successorid']=$subval['successorid'];	  
// 		        		}else{
// 		        			$data[$key]['successorid']=0;
// 		        		}
// 		        		//更新原节点的后置节点
// 		        		$map=array();
// 		        		$map['id']=$subval['id'];
// 		        		//重新设置successorid值
// 		        		$Model->where($map)->setField('successorid',$linkInfo['id']);
// 		        		//将前置节点为原节点值的节点更新为最新添加节点
// 		        		$map=array();
// 		        		$map['predecessorid']=$subval['id'];
// 		        		$Model->where($map)->setField('predecessorid',$linkInfo['id']);
// 		        	}
// 	        	}else{
// 	        	//如果不存在节点
// 	        		//前置节点数据的插入
// 	        		foreach ($predecessoridArr as $key=> $val){
// 		        		$dataPre[$key]['id']=$val;
// 		        		$dataPre[$key]['successorid']=$linkInfo['id'];	        		
// 	        		}
// 	        		$Model->addAll($dataPre);
// 	        	}
// 	        }	         
// 	        //插入数据
// 	        $Model->addAll($data);
// 	        //提交事务
// 	        $Model->commit();	        
//             break;
//             case "2":
//             	//首先获取前后置任务列表
//             	$result=$this->getLinkWork($linkInfo['link']);
//             	//对$linkInfo['pro']做专置为数组
//             	//初始化一个空数组
//             	$successoridArr=array();
//             	$successoridArr=explode(',',$linkInfo['link']);
//             	$data=array();
//             	//执行事务
//             	$Model->startTrans();
//             	foreach ($successoridArr as $key => $val){
//             		//如果存在节点
//             		if($result){
// 	            		//新数据的插入
// 	            		$data[$key]['id']=$linkInfo['id'];
// 	            		$data[$key]['successorid']=$val;
// 	            		//如果有后置节点，替换后置节点
// 	            		foreach($result as $subkey=>$subval){
// 	            			if($subval[id]==$val){
// 	            				$data[$key]['predecessorid']=$subval['predecessorid'];
// 	            			}else{
// 	            				$data[$key]['predecessorid']=0;
// 	            			}
// 	            			//更新原节点的后置节点
// 	            			$map=array();
// 	            			$map['id']=$subval['id'];
// 	            			//重新设置successorid值
// 	            			$Model->where($map)->setField('predecessorid',$linkInfo['id']);
// 	            			//将前置节点为原节点值的节点更新为最新添加节点
// 	            			$map=array();
// 	            			$map['successorid']=$subval['id'];
// 	            			$Model->where($map)->setField('successorid',$linkInfo['id']);
// 	            		}
//             		}else{
// 		        	    //如果存在节点
// 		        		//前置节点数据的插入
// 		        		foreach ($predecessoridArr as $key=> $val){
// 			        		$dataPre[$key]['id']=$val;
// 			        		$dataPre[$key]['predecessorid']=$linkInfo['id'];	        		
// 		        		}
// 		        		$Model->addAll($dataPre);
// 		        	}
//             	}
//             	//插入数据
//             	$Model->addAll($data);
//             	//提交事务
//             	$Model->commit();
//             	break;		
//             //修改只考虑前置任务  microsoft里面没有改变前置任务的功能，此处代码只做备用
// 			case "3":
// 				//首先获取前后置任务列表
// 				$result=$this->getLinkWork($linkInfo['id']);	
// 				//与当前数组做比较，获取差异数组
//                 //更新前置节点
// 		        $map=array();
// 		        $map['id']=$predecessorid;
// 		        //执行事务
// 		        $Model->startTrans();
// 		        //重新设置predecessorid值
// 		        $Model->where($map)->setField('predecessorid',$id);       		        
// 		        //更新后置节点
// 		        $map=array();
// 		        $map['predecessorid']=$predecessorid;
// 		        //重新设置predecessorid值
// 		        $Model->where($map)->setField('predecessorid',$id);
// 		        //提交事务
// 		        $Model->commit();	        				
// 			break;
// 			//删除某个节点，以下完全参照microsoft project管理
// 			case "4":				
// 				$map=array();
// 				$result=$this->getLinkWork($linkInfo['id']);
// 				//执行事务
// 				$Model->startTrans();
// 				if(count($result)>1){	
//                      foreach($result as $key =>$val){
// 				        //清空前置节点的后置节点值
//                      	$map=array();
//                      	$map['id']=$val['predecessorid'];
// 				        $Model->where($map)->setField('successorid',0);       		        
// 				        //清空后置节点的前置节点值
// 				        $map=array();
// 				        $map['id']=$val['successorid'];
// 				        $Model->where($map)->setField('predecessorid',0);
//                      }

// 				}else{
// 					//如果只有一条关联记录，就顺延
// 					foreach ($result as $key => $val){
// 				        //清空前置节点的后置节点值
//                      	$map=array();
//                      	$map['id']=$val['predecessorid'];
// 				        $Model->where($map)->setField('successorid',$val['successorid']);       		        
// 				        //清空后置节点的前置节点值
// 				        $map=array();
// 				        $map['id']=$val['successorid'];
// 				        $Model->where($map)->setField('predecessorid',$val['predecessorid']);
// 					}
// 				}
// 				//删除掉当前数组记录
// 				$map=array();
// 				$map['id']=$linkInfo['id'];
// 				$Model->where($map)->delete();
// 				//提交事务
// 				$Model->commit();
// 			break;
// 		} 
/*****************************以上按标准任务处理，不实用，暂留存底******************************/   
	}
	/**
	 * @Title: getLinkWork
	 * @Description: todo(获取任务的前置及后置任务)
	 * @param id 代表当前任务id
	 * @author yangxi
	 * @date 2014-10-21 上午11:00:00
	 * @throws
	 */
	function getLinkWork($id){
		$map=array();
		$map['mis_system_flow_link.id']=$id;
		$msflView=D('MisSystemFlowLinkView');
		$result=$msflView->where($map)->select();
		if( false!==$result){
			return $result;
		}		
	}
}
?>