<?php
/**
 * @Title: MobileFileBaseAction
 * @Package 文件接口类
 * @Description: 请求类，取数据接口
 * @author gml
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d˾
 * @copyright Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d˾
 * @date 2014-2-08
 * @version V1.0
 */
class MobileFileBaseAction extends MobileBaseAction {
	private $rootPath = "";//初始位置
	private $targetPath = "/Public/Uploads";//文件目录地址
		/** php 接收流文件
	 * @param count 文件总数
	 * @param fieldInfo 文件对应 参数集
	 * @param
	 * @return data 成功或失败信息
	 */
	private function mkDirs($dir){//递归创建文件夹位置
		if(!is_dir($dir)){
			if(!$this->mkDirs(dirname($dir))){
				return false;
			}
			if(!mkdir($dir,0777)){
				return false;
			}
		}
		return true;
	}
	//构造文件夹的路径返回文件夹位置
	//param string $modelname action名
	//return dirUrl 文件地址
	private function getDirUrl($modelname){
		
		//准备时间
		$newtime = time();
// 		$newlist["Y"] = date("Y",$newtime);
// 		$newlist["m"] = date("m",$newtime);
// 		$newlist["d"] = date("d",$newtime);
		$dirUrl = "/".$modelname."/".date("Y",$newtime)."/".date("m",$newtime)."/".date("d",$newtime)."/".date("Y",$newtime).date("m",$newtime)."/";
		return $dirUrl;
	}
	/**
	 * @Title: upLoadFileOf
	 * @Description: todo(获取上传的文件 并把相关信息存入数据库)
	 * @author 	
	 * @date 
	 * @throws
	 */
	public function upLoadFileOf(){
		//设置文件初始位置
		$this->rootPath = dirname(dirname(dirname(dirname(__FILE__))));
		if(!empty($_REQUEST["trueName"])){//传入真实姓名 则把信息添加进去
			$where["id"] = $_REQUEST["id"];
			$data["zhenshixingming"] = $_REQUEST["trueName"];
			$data["shenfenzhenghao"] = $_REQUEST["trueNum"];
			$data["shenhezhuangtai"] = 1;
			//修改状态 添加信息
			$returnId=$this->CURD($_REQUEST["modelname"],"save",$data,$where);
		}
		//准备存储文件路径
		//if (!file_exists($this->rootPath.$this->targetPath.$newtime)){ mkdir ($this->rootPath.$this->targetPath.$newtime."/11111"); echo "创建文件夹".$this->rootPath.$this->targetPath."成功";} else {echo "需创建的文件夹".$this->rootPath.$this->targetPath."已经存在";}
// 		if(!file_exists($this->rootPath.$this->targetPath.$newlist["Y"]."/".$newlist["m"]."/".$newlist["d"]."/")){
// 			echo "没有文件夹";
// 			if($this->mkDirs($this->rootPath.$this->targetPath.$newlist["Y"]."/".$newlist["m"]."/".$newlist["d"]."/")){
// 				echo "创建成功";	
// 			}else{
// 				echo "创建失败";
// 			}
// 		}else{
// 			echo "已有文件夹";
// 		}
		//准备返回信息
		$data = array();
		//获取转入文件相关信息
		if(!empty($_REQUEST["upLoadInfo"])){
			$this->transaction_model=M();
			$info = json_decode($_REQUEST["upLoadInfo"],true);//获取传入文件相关信息
			foreach ($info as $key => $file){
				if(!empty($_FILES[$file['fieldName']])){
					if($file['fieldName']=="touxiang"){//头像是唯一存在的东西 唯一存在的都确保在这个判断里面
						$this->deleteFile($file['tableName'],$file['tableId'],$file['fieldName']);
					}
					if($file['tableName']=="MisAutoHbz"){//如果是这张表 则需要修改表里面的内容
						$this->upDanju($file['tableId'],$_REQUEST["orderno"],$_REQUEST["huozhu"]);
					}
					$dirUrl = $this->getDirUrl($file['tableName']);//文件夹路径
					$FileName = time().".jpg";//文件名
					$dirUrlFile = $this->rootPath.$this->targetPath.$dirUrl.$FileName;//文件路径
					$upFilesInfo = $_FILES[$file['fieldName']];//获取上传文件相关信息
					//判断文件路径是否存在
					if(!file_exists($this->rootPath.$this->targetPath.$dirUrl)){
						//如果不存在则添加目标路径文件
						$this->mkDirs($this->rootPath.$this->targetPath.$dirUrl);
					}
					//移动文件位置
					if(move_uploaded_file($upFilesInfo['tmp_name'],$dirUrlFile)){
						
						$adddata = array();
						$adddata["attached"] = $dirUrl.$FileName;
						$adddata["upname"] = $upFilesInfo['name'];
						$adddata["tablename"] = $file['tableName'];
						$adddata["tableid"] = $file['tableId'];
						$adddata["fieldname"] = $file['fieldName'];
						//改用CURD方法
						$this->transaction_model->startTrans();
						$returnId=$this->CURD("MisAttachedRecord",'add',$adddata);
						//$model = M("mis_attached_record");
						//$returnId = $model->add($adddata);
						if($returnId){
							$this->transaction_model->commit();
							$data[$file['fieldName']]['code'] = '1';
							$data[$file['fieldName']]['data'] = '1';
							$data[$file['fieldName']]['days'] = '1';
						}else{
							$this->transaction_model->rollback();
						}
					}else{
						$data[$file['fieldName']]['code'] = '1';
						$data[$file['fieldName']]['data'] = '0';
						$data[$file['fieldName']]['days'] = '0';
						//echo "There was an error uploading the file, please try again!" . $_FILES['uploadedfile']['error'];
					}
					//准备文件存储路径 
// 					if(!file_exists($this->rootPath.$this->targetPath.$newlist["Y"]."/")){mkdir();}
// 					if(!file_exists($this->rootPath.$this->targetPath.$newlist["Y"]."/".$newlist["m"]."/")){mkdir();}
					//if(!file_exists($this->rootPath.$this->targetPath.$newlist["Y"]."/".$newlist["m"]."/".$newlist["d"]."/")){mkdir($this->rootPath.$this->targetPath.$newlist["Y"]."/".$newlist["m"]."/".$newlist["d"]."/");}
					$upFilesInfo = $_FILES[$file['index']];//获取上传文件相关信息
// 					if(move_uploaded_file($upFilesInfo['tmp_name'],$this->rootPath.$this->targetPath.$filename)){
// 						$model = M("mis_attached_record");
// 						$adddata = array();
// 						$adddata["attached"] = $dirUrl.$FileName;
// 						$adddata["upname"] = $upFilesInfo['name'];
// 						$adddata["tablename"] = $file['tableName'];
// 						$adddata["tableid"] = $file['tableId'];
// 						$adddata["fieldname"] = $file['tableName'];
// 						$returnId = $model->add($data);
// 						if($returnId){
// 							$data[$file['index']]['code'] = '1';
// 							$data[$file['index']]['data'] = '1';
// 							$data[$file['index']]['days'] = '1';
// 						}
// 					}else{
// 						$data[$file['index']]['code'] = '1';
// 						$data[$file['index']]['data'] = '1';
// 						$data[$file['index']]['days'] = '0';
// 						//echo "There was an error uploading the file, please try again!" . $_FILES['uploadedfile']['error'];
// 					}
				}
			}
		}else{
			$data[$file['fieldName']]['code'] = '未得到文件相关信息';
			$data[$file['fieldName']]['data'] = '0';
			$data[$file['fieldName']]['days'] = '0';
		}
		
		if($data){
			echo json_encode($data);
		}else{
			echo json_encode($data);
		}
	}
	/**
	 * @Title: deleteFile
	 * @Description: todo(删除原有的文件 确保文件唯一)
	 * @author
	 * @date
	 * @throws
	 */
	function deleteFile($tablename,$tableid,$fieldname){
		//$model = M("mis_attached_record");
		$where["tablename"] =$tablename;
		$where["tableid"] =$tableid;
		$where["fieldname"] =$fieldname;
		//$model -> where($where)->delete();
		//删除多余文件
		$returnId=$this->CURD("MisAttachedRecord","delete","",$where);
	}
	/**
	 * 
	 * @Title: upDanju
	 * @Description: todo(上传单据时修改上传单据状态 并加入推送 让货主确认) 
	 * @param unknown $tableid
	 * @return unknown  
	 * @author 刘智宏 
	 * @date 2016年3月23日 下午3:38:39 
	 * @throws
	 */
	function upDanju($tableid,$orderno,$huozhu){
		//加入自动确认
		$MisAutoGhk = D("MisAutoGhk");
		$map = array();
		$map["moxing"] = "MisAutoCpl";//主城配送模型
		$peizhi = $MisAutoGhk->where($map)->field("peizhitianshu")->find();//获取配置
		$tianshu = $peizhi["peizhitianshu"];//获取配置天数
		$huodanid = getFieldBy($tableid, "id", "peisongdanhao", "MisAutoHbz");//获取货单id
		$MisAutoQgl = D("MisAutoQgl");
		$data = array();
		$data["suoshumoxing"] = "mis_auto_evuyl";
		$data["dengdaitianshu"] = time() + $tianshu*86400;//自动完成时间
		$data["shifuqueren"] = 0;
		$data["danhao"] = $huodanid;
		$data["wanchengziduan"] = "zuizhongqueren";
		$data["wanchengziduanzhuang"] = "1";
		$data["createtime"] = time();//加入时间
		$result=$this->CURD("MisAutoQgl",'add',$data);
		//加入推送
		$MobileApicloudBase = D("MobileApicloudBase");
		$MobileApicloudBase->getTuisong("您的订单".$orderno."已送达并上传单据请确认",$huozhu,"1","type=1&winname=frame2/hyxq_window&huodanid=".$tableid);// 内容     货主ID   推送端1货主0司机
		$map = array();
		$map['id']=$tableid;
		$data = array();
		$data['danjushifushangchuan']=1;
		$result = $this->CURD('MisAutoHbz','save',$data,$map);
		return $result;
	}
}