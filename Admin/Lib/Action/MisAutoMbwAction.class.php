<?php
/**
 * @Title: MisAutoMbwAction
 * @Package package_name
 * @Description: todo(动态表单_自动生成-流程转授)
 * @author 汤文志
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2015-07-23 17:42:56
 * @version V1.0
*/
class MisAutoMbwAction extends MisAutoMbwExtendAction {
	public function _filter(&$map){
		$fieldtype=$_REQUEST['fieldtype'];
		if($fieldtype){
			$map[$fieldtype]=$_REQUEST[$fieldtype];
			$this->assign("fieldtype",$fieldtype);
			$this->assign("fieldtypeval",$_REQUEST[$fieldtype]);
		}else{
			$relationmodelname=$_REQUEST['bindaname'];
			if($relationmodelname){
				$map['relationmodelname']=$relationmodelname;	
			}
		}
		// 为了兼容普通模式下的表单使用
		$bindid = $_REQUEST['bindid'];
		if($bindid){
			$map['bindid']=$bindid;
			$this->assign("bindid",$bindid);
		}
		if ($_SESSION["a"] != 1){
			$map['status']=array("gt",-1);
		}
		$this->_extend_filter($map);
	}
	/**
	 * @Title: _before_index
	 * @Description: todo(前置index函数)
	 * @author 汤文志
	 * @date 2015-07-23 17:42:56
	 * @throws 
	*/
	function _before_index() {
		$this->_extend_before_index();
	}
	/**
	 * @Title: _before_edit
	 * @Description: todo(前置编辑函数)
	 * @author 汤文志
	 * @date 2015-07-23 17:42:56
	 * @throws 
	*/
	function _before_edit(){
		if($_REQUEST['main'])
			$this->assign("main",$_REQUEST['main']);
		$this->_extend_before_edit();
	}
	/**
	 * @Title: _before_insert
	 * @Description: todo(前置添加函数)
	 * @author 汤文志
	 * @date 2015-07-23 17:42:56
	 * @throws 
	*/
	function _before_insert(){
		$this->checkifexistcodeororder('mis_auto_guikc','orderno',$this->getActionName());
		$this->_extend_before_insert();
	}
	/**
	 * @Title: _before_update
	 * @Description: todo(前置修改函数)  
	 * @author 汤文志
	 * @date 2015-07-23 17:42:56
	 * @throws
	*/
	function _before_update(){
		$this->checkifexistcodeororder('mis_auto_guikc','orderno',$this->getActionName(),1);
		$this->_extend_before_update();
	}
	/**
	 * @Title: _after_edit
	 * @Description: todo(后置编辑函数)
	 * @author 汤文志
	 * @date 2015-07-23 17:42:56
	 * @throws 
	*/
	function _after_edit($vo){
		// 内嵌表处理datatable6
		$innerTabelObjdatatable6 = M('mis_auto_guikc_sub_datatable6');
		$innerTabelObjdatatable6Data = $innerTabelObjdatatable6->where('masid='.$vo['id'])->select();
		$this->assign("innerTabelObjdatatable6Data",$innerTabelObjdatatable6Data);
		$this->_extend_after_edit($vo);
	}
	/**
	 * @Title: _after_insert
	 * @Description: todo(后置insert函数)  
	 * @author 汤文志
	 * @date 2015-07-23 17:42:56
	 * @throws
	*/
	function _after_insert($id){		
		// 内嵌表数据添加处理
				
		$datatablefiexname ="mis_auto_guikc_sub_";
		$insertData = array();// 数据添加缓存集合
		if($_POST['datatable']){
			foreach($_POST['datatable'] as $key=>$val){
				foreach($val as $k=>$v){
					$insertData[$k][]=$v;
				}
			}
		}
		//数据处理
		if($insertData){
			foreach($insertData as $k=>$v){
				$nqname = createRealModelName($datatablefiexname.$k);
				$model = D($nqname);
				$uploadfile = array();
				foreach($v as $key=>$val){
					if(C('TOKEN_NAME'))
						$val[C('TOKEN_NAME')]= $_POST[C('TOKEN_NAME')];
					$val['masid'] =$id ;
					$val = $model->create($val);
					$insertId = $model->add($val);
					/*
					 * _over_insert 方法，为静默插入生单。
					 */
					$this->_over_insert($nqname, $insertId);
					
					//处理内嵌表带附件信息数据
					foreach($val as $kk => $vv){
						if(is_array($vv)){
							$uploadfile[$kk.$key.$k]["file"] = $vv;
							$uploadfile[$kk.$key.$k]["tableid"] = $id;
							$uploadfile[$kk.$key.$k]["subid"] = $insertId;
							$uploadfile[$kk.$key.$k]["tablename"] = createRealModelName($datatablefiexname.$k);
							$uploadfile[$kk.$key.$k]["fieldname"] = $kk;
						}
					}
				}
				if($uploadfile){
					$this->DT_swf_upload($uploadfile);
				}
				$a=$model->getLastInsID();
				$_REQUEST[$datatablefiexname.$k] = $a;
			}
		}
		$this->_extend_after_insert($id);
	}
	/**
	 * @Title: _before_add
	 * @Description: todo(前置add函数)  
	 * @author 汤文志
	 * @date 2015-07-23 17:42:56
	 * @throws
	*/
	function _before_add(){
		$vo['shengxiaoriqi']=time();
		if($_REQUEST['main'])
			$this->assign("main",$_REQUEST['main']);
		$this->_extend_before_add($vo);
	}
	/**
	 * @Title: _after_update
	 * @Description: todo(后置update函数)  
	 * @author 汤文志
	 * @date 2015-07-23 17:42:56
	 * @throws
	*/
	function _after_update(){
		// 内嵌表数据添加处理		// 内嵌表数据添加处理
				
		$datatablefiexname ="mis_auto_guikc_sub_";
		$insertData = array();// 数据添加缓存集合
		$updateData = array();// 数据修改缓存集合
		if($_POST['datatable']){
			foreach($_POST['datatable'] as $key=>$val){
				foreach($val as $k=>$v){
					if($v['id'] || $_REQUEST[$datatablefiexname.$k]){
						$updateData[$k][]=$v;
					}else{
						$insertData[$k][]=$v;
					}
				}
			}
		}
		//数据处理
		if($insertData){
			foreach($insertData as $k=>$v){
				$nqname = createRealModelName($datatablefiexname.$k);
				$model = D($nqname);
				$uploadfile = array();
				foreach($v as $key=>$val){
					if(C('TOKEN_NAME'))
						$val[C('TOKEN_NAME')]= $_POST[C('TOKEN_NAME')];
					$val['masid'] =$_POST["id"] ;
					$val = $model->create($val);
					$insertId = $model->add($val);
					/*
					 * _over_insert 方法，为静默插入生单。
					 */
					$this->_over_insert($nqname, $insertId);
					//处理内嵌表带附件信息数据
					foreach($val as $kk => $vv){
						if(is_array($vv)){
							$uploadfile[$kk.$key.$k]["file"] = $vv;
							$uploadfile[$kk.$key.$k]["tableid"] = $_POST["id"];
							$uploadfile[$kk.$key.$k]["subid"] = $insertId;
							$uploadfile[$kk.$key.$k]["tablename"] = createRealModelName($datatablefiexname.$k);
							$uploadfile[$kk.$key.$k]["fieldname"] = $kk;
						}
					}
				}
				if($uploadfile){
					$this->DT_swf_upload($uploadfile);
				}
			}
		}
		if($updateData){
			foreach($updateData as $k=>$v){
				$nqname = createRealModelName($datatablefiexname.$k);
				$model = D($nqname);
				$uploadfile = array();
				foreach($v as $key=>$val){
					if(C('TOKEN_NAME'))
						$val[C('TOKEN_NAME')]= $_POST[C('TOKEN_NAME')];
					$val = $model->create($val);
					$model->save($val);
					/*
					 * _over_update 方法，为静默插入生单。(修改的原始数据未保存)
					 */
					$this->_over_update($nqname,$val['id'],2);
					//处理内嵌表带附件信息数据
					foreach($val as $kk => $vv){
						if(is_array($vv)){
							$uploadfile[$kk.$key.$k]["file"] = $vv;
							$uploadfile[$kk.$key.$k]["tableid"] = $_POST["id"];
							$uploadfile[$kk.$key.$k]["subid"] = $val["id"];
							$uploadfile[$kk.$key.$k]["tablename"] = createRealModelName($datatablefiexname.$k);
							$uploadfile[$kk.$key.$k]["fieldname"] = $kk;
						}
					}
				}
				if($uploadfile){
					$this->DT_swf_upload($uploadfile);
				}
			}
		}
		$this->_extend_after_update();
	}
	/**
	 * @Title: onesave
	 * @Description: todo(子表单条数据保存)  
	 * @author 王昭侠
	 * @date 2015-07-23 17:42:56
	 * @parame $_POST['table'] 表名
	 * @parame $_POST['id'] 数据ID值
	 * @parame $_POST['masid'] 数据父表ID值
	 * @throws
	*/
	function onesave(){
		$table = $_REQUEST['post_table'];
		$id = intval($_REQUEST['post_id']);
		$masid = intval($_REQUEST['post_mas_id']);
		if($table && !empty($_POST['datatable'])){
			$model = D(createRealModelName($table));
			$insertData = array();// 数据添加缓存集合
			$updateData = array();// 数据修改缓存集合
		if($_POST['datatable']){
			foreach($_POST['datatable'] as $key=>$val){
				foreach($val as $k=>$v){
					if($id){
						$updateData[$k][]=$v;
					}else{
						$v["masid"] = $masid;
						$insertData[$k][]=$v;
					}
				}
			}
		}
		//数据处理
		if($insertData){
			foreach($insertData as $k=>$v){
				foreach($v as $key=>$val){
					$id=$model->add($val);
				}
			}
		}
		if($updateData){
			foreach($updateData as $k=>$v){
				foreach($v as $key=>$val){
					$model->save($val);
				}
			}
		}
		$this->success('保存成功',true,$id);
		}
	}
}
?>