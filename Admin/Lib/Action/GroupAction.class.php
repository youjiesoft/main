<?php
/**
 * @Title: GroupAction 
 * @Package package_name
 * @Description: todo(菜单分组) 
 * @author laicaixia
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-5-31 下午5:53:22 
 * @version V1.0
 */
class GroupAction extends CommonAction {
	/**
	 * @Title: _filter 
	 * @Description: todo(检索) 
	 * @param unknown_type $map  
	 * @author laicaixia 
	 * @date 2013-5-31 下午5:53:42 
	 * @throws 
	*/  
	public function _filter(&$map){
		if(!$_SESSION['a'])$map['status'] =1;
		if( !$_REQUEST ['orderField'] ) $_REQUEST ['orderField']="sorts";
		if( !$_REQUEST ['orderDirection'] ) $_REQUEST ['orderDirection']="asc";
	}
	/**
	 * @Title: sort 
	 * @Description: todo(未启用)   
	 * @author laicaixia 
	 * @date 2013-5-31 下午5:54:05 
	 * @throws 
	*/  
	private function sort(){
		$model = D("Group");
		$data['id'] =$_POST["id"];
		$data[$_POST["fields"]]=$_POST["value"];
		$list=$model->save($data);
		if (false !== $list) {
			$this->success ( L('_SUCCESS_'));
		} else {
			$this->error ( L('_ERROR_') );
		}
	}
	
	/**
	 * @Title: _before_add 
	 * @Description: todo(进入新增)   
	 * @author laicaixia 
	 * @date 2013-5-31 下午5:55:30 
	 * @throws 
	*/  
	public function _before_add(){
		//获取分组类型
		$module = M("mis_order_types");
		$map["status"]=1;
		$map["type"]=40;
		$typelist =$module->where($map)->select();
		$this->assign("typelist", $typelist);
	}
	
	/**
	 * @Title: _before_edit 
	 * @Description: todo(进入修改)   
	 * @author laicaixia 
	 * @date 2013-5-31 下午5:55:40 
	 * @throws 
	*/  
	public function _before_edit(){
		//获取分组类型
		$module = M("mis_order_types");
		$map["status"]=1;
		$map["type"]=40;
		$typelist =$module->where($map)->select();
		$this->assign("typelist", $typelist);
		$model2 =M("group_index_setting");
		$list = $model2->where("group_id=".$_REQUEST['id'])->select();
		$picareaarr=array();
		$bararr=array();
		foreach($list as $k=>$v){
			$v["links"]=htmlspecialchars($v["links"]);
			if($v["typeid"]){
				$bararr[]=$v;
			}else{
				$picareaarr[]=$v;
			}
		}
		//定义上传附件的展示清单
		$map["status"]  =1;
		$map["orderid"] =$_REQUEST["id"];
		$map["type"] =40;
		$m=M("mis_attached_record");
		$attarry=$m->where($map)->select();
		// print_r($m->getlastsql());die;
		$this->assign('attarry',$attarry);
		$this->assign("bararr", $bararr);
		$this->assign("picareaarr", $picareaarr);
	}
	
	/**
	 * @Title: _before_update 
	 * @Description: todo(执行修改)   
	 * @author laicaixia 
	 * @date 2013-5-31 下午5:55:53 
	 * @throws 
	*/  
	public function _before_update(){
		$groupid=$_POST["id"];
		R('Admin/Common/swf_upload',array($groupid,40));
		$this->settingGroupIndex($groupid);
	}
	
	/**
	 * @Title: _after_insert 
	 * @Description: todo(执行新增) 
	 * @param unknown_type $groupid  
	 * @author laicaixia 
	 * @date 2013-5-31 下午5:56:05 
	 * @throws 
	*/  
	public function _after_insert($groupid){
		R('Admin/Common/swf_upload',array($groupid,40));
		$this->settingGroupIndex($groupid);
	}
	
	/**
	 * @Title: settingGroupIndex 
	 * @Description: todo(这里用一句话描述这个方法的作用) 
	 * @param unknown_type $groupid  
	 * @author laicaixia 
	 * @date 2013-5-31 下午5:57:36 
	 * @throws 
	*/  
	private function settingGroupIndex($groupid){
		if($groupid){
			//保存图片热区链接
			$model =M("group_index_setting");
			$model->where("group_id=".$groupid)->delete();
			$area_title=$_POST["titlearr"];
			$area_title=$_POST["titlearr"];
			$area_windowsrel=$_POST["windowsrelarr"];
			$area_linkmodule=$_POST["modulesarr"];
			$area_picarea=$_POST["imgareaarr"];
			$data_imgarea=array();
			foreach($area_title as $k=>$v ){
				$data_imgarea[$k]["group_id"]=$groupid;
				$data_imgarea[$k]["title"]=$v;
				$data_imgarea[$k]["windowsrel"]=$area_windowsrel[$k];
				$data_imgarea[$k]["links"]=$area_linkmodule[$k];
				$data_imgarea[$k]["imgarea"]=$area_picarea[$k];
			}
			$model->addAll($data_imgarea);
			
			//保存图片模板信息链接
			$bar_type=$_POST["bartypearr"];
			$bar_links=$_POST["barlinkarr"];
			$bar_windowsrel=$_POST["barwindowsrelarr"];
			$bar_title=$_POST["bartitlearr"];
			$data_bar=array();
			foreach($bar_title as $k1=>$v1 ){
				$data_bar[$k1]["group_id"]=$groupid;
				$data_bar[$k1]["title"]=$v1;
				$data_bar[$k1]["windowsrel"]=$bar_windowsrel[$k1];
				$data_bar[$k1]["links"]=$bar_links[$k1];
				$data_bar[$k1]["typeid"]=$bar_type[$k1];
			}
			$model->addAll($data_bar);
			$this->swf_upload($groupid);
		}
	}
	
	/**
	 * @Title: swf_upload 
	 * @Description: todo(临时文件夹里面的文件转移到目标文件夹) 
	 * @param unknown_type $groupid  
	 * @author laicaixia 
	 * @date 2013-5-31 下午5:56:05 
	 * @throws 
	*/ 
	function swf_upload( $groupid ){
		$save_file=$_POST['swf_upload_save_name'];
		$source_file=$_POST['swf_upload_source_name'];
		foreach($save_file as $k=>$v){
			if($v){
				$fileinfo=pathinfo($v);
				$from = UPLOAD_PATH_TEMP.$v;//临时存放文件
				if( file_exists($from) ){
					$p=UPLOAD_PATH.$fileinfo['dirname'];// 目标文件夹
					if( !file_exists($p) ) $this->createFolders($p); //判断目标文件夹是否存在
					$to	= UPLOAD_PATH.$fileinfo["dirname"]."/".$groupid.".".$fileinfo["extension"];
					if( file_exists($to) ) unlink($to);
					rename($from,$to);
					$model=M("group");
					$savepath = $fileinfo["dirname"]."/".$groupid.".".$fileinfo["extension"];
					$model->where("id=".$groupid)->setField("imgpath",$savepath);
				}
			}
		}
	}
}
?>