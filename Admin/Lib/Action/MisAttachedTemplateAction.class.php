<?php
/**
 * @Title: MisAttachedTemplateAction 
 * @Package package_name 
 * @Description: todo(附件模板管理控制器) 
 * @author wangzhaoxia 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-11-05 下午5:00:36 
 * @version V1.0
 */
class MisAttachedTemplateAction extends CommonAction {
	/**
	 * @Title: _before_add
	 * @Description: 打开新增页面之前，处理一些需要的数据信息
	 * @author 黎明刚
	 * @date 2014年11月21日 下午2:30:07 
	 * @throws
	 */
	public function _before_add() {
		$type_list = array ();
		//获取上传格式限制
		$allexts = C ( "allexts" );
		$type_list [] = array (
				"value" => - 1,
				"name" => "全部" 
		);
		foreach ( $allexts as $k => $v ) {
			$type_list [] = array (
					"value" => $k,
					"name" => $v 
			);
		}
		$type_list1=json_encode($type_list);
		$this->assign ( 'allexts', $type_list1 );
		
	}
	function insert() {
// 		dump($_POST);exit;
		$name = $this->getActionName ();
		$model = D ( $name );
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 保存当前数据对象
		$list = $model->add ();
		if ($list !== false) {
			// 上传附件信息
			//实例化附件设计模板
			$submodel = M ( "mis_system_attached_template_sub" );
			$subname = $_POST['subname'];//资料名称
			$save_file=$_POST['swf_upload_save_name'];
			$source_file=$_POST['swf_upload_source_name'];
			
			foreach($subname as $key=>$val){
				$data = array();
				$data['masid']= $list;
				$data['name']= $val;
				$data['type']= $_POST['subtype'][$key];
				$data['remark']= $_POST['subremark'][$key];
				$data['datum']= $_POST['subdatum'][$key];
				$data['createid']= $_POST['subremark'][$key];
				$data ['createid'] = $_SESSION [C ( 'USER_AUTH_KEY' )];
				$data ['createtime'] = time ();
				
				$subid = $submodel->add($data);
				if($subid){
					if ($save_file[$key]) {
						//存在附件信息
						unset($_POST ['swf_upload_save_name']);
						unset($_POST ['swf_upload_source_name']);
						$_POST ['swf_upload_save_name'] = $save_file[$key];
						$_POST ['swf_upload_source_name'] = $source_file[$key];
						$this->swf_upload ( $list,$subid, 'MisAttachedTemplate' );
					}
				}else{
					$this->error ( "附件模板设计失败，请联系管理员" );
				}
			}
			$this->success ( L ( '_SUCCESS_' ), '', $list );
		} else {
			$this->error ( L ( '_ERROR_' ) );
		}
	}
	public function _after_edit($vo){ 
		$type_list = array ();
		$allexts = C ( "allexts" );
		$type_list [] = array (
				"value" => - 1,
				"name" => "全部"
		);
		foreach ( $allexts as $k => $v ) {
			$type_list [] = array (
					"value" => $k,
					"name" => $v
			);
		}
		$type_list1=json_encode($type_list);
		$this->assign ( 'allexts', $type_list);
		$this->assign ( 'allexts1', $type_list1);
		//第一步，sub数据查询
		$submodel = M ( "mis_system_attached_template_sub" );
		$map['masid'] = $vo['id'];
		$sublist = $submodel->where($map)->select();
		$subidArr = array();
		//附件信息查询
		foreach($sublist as $key=>$val){
			array_push($subidArr, $val['id']);
			//获取附件信息
			$recolist = $this->getAttachedRecordList($vo['id'],true,true,"MisAttachedTemplate",$val['id'],false);
			$sublist[$key]['record'] = $recolist;
		}
		$this->assign("mas_vo",$vo);
		$this->assign("sublist",$sublist);
		$this->assign("suvidArr",implode(",", $subidArr));
	}
	public function additemview(){
		$name = $this->getActionName ();
		$model = D ( $name );
		$id = $_REQUEST ["id"];
		$map ["id"] = $id;
		$vo = $model->where ( $map )->find ();
		$type_list = array ();
		$allexts = C ( "allexts" );
		$type_list [] = array (
				"value" => - 1,
				"name" => "全部"
		);
		foreach ( $allexts as $k => $v ) {
			$type_list [] = array (
					"value" => $k,
					"name" => $v
			);
		}
		$type_list1=json_encode($type_list);
		$this->assign ( 'allexts', $type_list);
		$this->assign ( 'allexts1', $type_list1);
		//第一步，sub数据查询
		$submodel = M ( "mis_system_attached_template_sub" );
		$map = array();
		$map['masid'] = $id;
		$sublist = $submodel->where($map)->select();
		$subidArr = array();
		//附件信息查询
		foreach($sublist as $key=>$val){
			array_push($subidArr, $val['id']);
			//获取附件信息
			$recolist = $this->getAttachedRecordList($vo['id'],true,true,"MisAttachedTemplate",$val['id'],false);
			$sublist[$key]['record'] = $recolist;
		}
		$this->assign("mas_vo",$vo);
		$this->assign("sublist",$sublist);
		$this->assign("suvidArr",implode(",", $subidArr));
		$this->display();
	}
	
	public function update(){
		$name=$this->getActionName();
		$model = D ( $name );
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$list=$model->save ();
		if (false !== $list) {
			//实例化附件设计模板
			$submodel = M ( "mis_system_attached_template_sub" );
			$subname = $_POST['subname'];//资料名称
			$save_file=$_POST['swf_upload_save_name'];
			$source_file=$_POST['swf_upload_source_name'];
			$subidArr = explode(",", $_POST['suvidArr']);
			$inArr=array();
			foreach($subname as $key=>$val){
				if(in_array($_POST['subid'][$key], $subidArr) && $_POST['subid'][$key]!=null){
					array_push($inArr, $_POST['subid'][$key]);
					//原始数据修改
					$data = array();
					$data['id']= $_POST['subid'][$key];
					$data['name']= $val;
					$data['type']= $_POST['subtype'][$key];
					$data['remark']= $_POST['subremark'][$key];
					$data['datum']= $_POST['subdatum'][$key];
					$data['createid']= $_POST['subremark'][$key];
					$data ['createid'] = $_SESSION [C ( 'USER_AUTH_KEY' )];
					$data ['createtime'] = time ();
					$result = $submodel->save($data);
					if($result===false){
						$this->error ( "附件模板设计失败，请联系管理员" );
					}else{
						if ($save_file[$key]) {
							//存在附件信息
							unset($_POST ['swf_upload_save_name']);
							unset($_POST ['swf_upload_source_name']);
							$_POST ['swf_upload_save_name'] = $save_file[$key];
							$_POST ['swf_upload_source_name'] = $source_file[$key];
							$this->swf_upload ( $_POST['id'], $_POST['subid'][$key], 'MisAttachedTemplate' );
						}						
					}
				}else{
					//新数据新增
					$data = array();
					$data['masid']= $_POST['id'];
					$data['name']= $val;
					$data['type']= $_POST['subtype'][$key];
					$data['remark']= $_POST['subremark'][$key];
					$data['datum']= $_POST['subdatum'][$key];
					$data['createid']= $_POST['subremark'][$key];
					$data ['createid'] = $_SESSION [C ( 'USER_AUTH_KEY' )];
					$data ['createtime'] = time ();
					$subid = $submodel->add($data);
					if($subid){
						if ($save_file[$key]) {
							//存在附件信息
							unset($_POST ['swf_upload_save_name']);
							unset($_POST ['swf_upload_source_name']);
							$_POST ['swf_upload_save_name'] = $save_file[$key];
							$_POST ['swf_upload_source_name'] = $source_file[$key];
							$this->swf_upload ( $_POST['id'], $subid, 'MisAttachedTemplate' );
						}
					}else{
						$this->error ( "附件模板设计失败，请联系管理员" );
					}
				}
			}
			//排除相同的，获取新的数组
			$newArr = array_diff($subidArr, $inArr);
			if($newArr){
				$map = array();
				$map['id'] = array(' in ',$newArr);
				$result = $submodel->where($map)->delete();
				if(!$result){
					$this->error ( "附件模板设计失败，请联系管理员" );
				}
			}
			$this->success ( L('_SUCCESS_'));
		} else {
			$this->error ( L('_ERROR_') );
		}
	}
}
?>