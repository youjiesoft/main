<?php
/**
 * @Title: MobileBaseModel
 * @Package 核心公共代码类
 * @Description: 请求类，取数据接口
 * @author liminggang
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d˾
 * @copyright Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d˾
 * @date 2014-2-08
 * @version V1.0
 */
class MobileBaseModel extends Model {
	private $transaction_model=NULL;//事务模型
	/**
	 * @Title: setToken
	 * @Description: todo(数据转换加密方法)
	 * @param 数据data $userInfo
	 * @return 返回处理后的字符串数据。
	 * @author liminggang
	 * @date 2016-1-23 下午2:53:32
	 * @throws
	 */
	protected function  setToken($userInfo){
		$token= serialize($userInfo);
		$token=str_replace('=','',base64_encode($token));//base64加密
		$token=strrev($token);//反转字符串
		return $token;
	}
	
	/**
	 * @Title: getToken
	 * @Description: todo(Token解码)
	 * @param 数据data $userInfo
	 * @return 返回处理后的字符串数据。
	 * @author liminggang
	 * @date 2016-1-23 下午2:53:32
	 * @throws
	 */
	protected function  getToken($token){
		$token=unserialize(base64_decode(strrev($token)));
		return $token;
	}	
	/**
	 * @Title: setToken
	 * @Description: todo(CURD操作公共方法)
	 * @param 模型名称 $modelname
	 * @param CURD操作类型 $opration 
	 * @param 数据data $data
	 * @param 条件 $condition
	 * @return 返回执行后的结构
	 * @author liminggang
	 * @date 2016-1-23 下午2:53:32
	 * @throws
	 */
	protected function CURD($modelname,$opration,$data,$condition=''){
		if(empty($modelname) && empty($opration))return false;
		$this->transaction_model=M();
		$this->transaction_model->startTrans();
		switch($opration){
			case 'add':
				$result=D($modelname)->add($data);
				break;
			case 'addAll':
				$result=D($modelname)->addAll($data);
				break;
			case 'save':
				if(empty($condition))return false;
				$result=D($modelname)->where($condition)->save($data);
				break;
			case 'delete':
				if(empty($condition))return false;
				$result=D($modelname)->where($condition)->delete();
				break;
			case 'select':
				if(empty($condition))return false;
				$result=D($modelname)->where($condition)->find();
				break;
			case 'query':
				$result=D($modelname)->query($data);
				break;
			case 'execute':
				$result=D($modelname)->execute($data);
				break;
		}
		if($result===false){
			$this->transaction_model->rollback();
		}else{
			$this->transaction_model->commit();
		}
		return $result;
	}
	
	/**
	 * @Title: swf_upload
	 * @Description: todo附件上传方法
	 * @param int 当前表单ID $insertid
	 * @param int $type  已经弃用此参数，为了减少修改。暂时没有删除。
	 * @param int 带明细的ID $subid 针对有明细数据时，也存在附件
	 * @param string 附件对应的控制器名称 $m
	 * @author liminggang
	 * @date 2014-10-10 下午7:40:06
	 * @throws
	 */
	function swf_upload($insertid,$subid=0,$m="",$userid,$projectid,$projectworkid){
		$save_file=$_POST['swf_upload_save_name'];
		$source_file=$_POST['swf_upload_source_name'];
		$attModel = D("MisAttachedRecord");
		//如果存在项目跟任务，判断是否需要归档77
		if($projectid && $projectworkid && $save_file){
			$MPFFDAO = M("mis_project_flow_form"); // 实例化User对象
			$isfile = $MPFFDAO->where("id=".$projectworkid)->getField('isfile');
		}
		//临时文件夹里面的文件转移到目标文件夹
		foreach($save_file as $k=>$v){
			if(is_array($v)){
				foreach ($v as $key=>$val){
					$fileinfo=pathinfo($val);
					$from = UPLOAD_PATH_TEMP.$val;//临时存放文件
					if( file_exists($from) ){
						$p=UPLOAD_PATH.$fileinfo['dirname'];// 目标文件夹
						if( !file_exists($p) ) $this->createFolders($p); //判断目标文件夹是否存在
						$to = UPLOAD_PATH.$val;
						rename($from,$to);
						//保存附件信息
						$data=array();
						//$data['type']=$type;
						//$data['orderid']=$insertid;
						$data['tablename'] = $m ? $m:$this->getActionName();
						$data['tableid']=$insertid;
						$data['subid']=$subid;
						$data['attached']= $val;
						$data['projectid']= $projectid;
						$data['projectworkid']= $projectworkid;
						$data['isfile']= $isfile;
						$data['fieldname']= $k;
						$data['upname']=$source_file[$k][$key];
						$data['createtime'] = time();
						$data['createid'] = $userid?$userid:0;
						$rel=$this->CURD('MisAttachedRecord','add',$data);
						if(!$rel){
							throw new AppException("附件上传失败，请联系管理员！");
						}
					}
				}
			}else{
				$fileinfo=pathinfo($v);
				$from = UPLOAD_PATH_TEMP.$v;//临时存放文件
				if( file_exists($from) ){
					$p=UPLOAD_PATH.$fileinfo['dirname'];// 目标文件夹
					if( !file_exists($p) ) $this->createFolders($p); //判断目标文件夹是否存在
					$to = UPLOAD_PATH.$v;
					rename($from,$to);
					//保存附件信息
					$data=array();
					//$data['type']=$type;
					//$data['orderid']=$insertid;
					$data['tablename'] = $m ? $m:$this->getActionName();
					$data['tableid']=$insertid;
					$data['subid']=$subid;
					$data['attached']= $v;
					$data['fieldname']= $_REQUEST['fieldname'];
					$data['upname']=$source_file[$k];
					$data['createtime'] = time();
					$data['createid'] = $userid?$userid:0;
					$data['projectid']= $projectid;
					$data['projectworkid']= $projectworkid;
					$data['isfile']= $isfile;
					$rel=$this->CURD('MisAttachedRecord','add',$data);
					if(!$rel){
						throw new AppException("附件上传失败，请联系管理员！");
					}
				}
			}
		}
	}
}