<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(selectlist配置文件维护) 
 * @author libo 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-7-28 下午1:47:28 
 * @version V1.0
 */
class LookupAction extends CommonAction{
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
		$model=D('Lookup');
		if(file_exists($model->GetFile())){
			$selectlist = require $model->GetFile();
		}
		foreach ($selectlist as $k=>$v){
			$selectlist[$k]['name']=$k;
			$selectlist[$k]['data']=implode(",", $v['data']);
		}
		$this->assign("list",$selectlist);
		$this->display();
	}
	/**
	 * (non-PHPdoc)新增类型
	 * @see CommonAction::insert()
	 */
	public function insert() {
//	dump($_POST);die;
		$model=D('Lookup');
		$name = $_POST['name'];
		$vo = $model->GetRules($name);
		if($vo){
			$this->error("输入的名称有重名，请重新输入！");
		} else {
			$arr=array();
			
			if($_POST['arr_key']){
				foreach ($_POST['arr_key'] as $k=>$v){
					$arr[$v]= $_POST['arr_val'][$k];
				}
			}
			$data=array(
					'title'=>$_POST['title'],
					'fields' => $_POST['fields'],
					'url'=>$_POST['url'],
					'mode' => $_POST['mode'], // 屈强@2014-08-07 新增标题项
					'filed'=> $_POST['filed'],
					'val'=> $_POST['val'],
					'condition'=> $_POST['condition'],
					'status' => $_POST['status'],
					'level' => 15, // 屈强@2014-08-07 新增当前项权限
			);
			if(file_exists($model->GetFile())){
				$selectlist = require $model->GetFile();
				$a = array();
				foreach($selectlist as $k=>$v){	
					if(intval($k)){
					array_push($a,$k);
					}				
				}
				$b = max($a)+1;
			}
			$selectlist[$b]=$data;
			$model->SetRules($selectlist);
			$this->success("操作成功");
		}
	}
	/**
	 * (non-PHPdoc)编辑
	 * @see CommonAction::edit()
	 */
	public function edit(){
		$model=D('Lookup');
		if(file_exists($model->GetFile())){
			$selectlist = require $model->GetFile();
		}
		$this->assign("name",$_REQUEST['id']);
		$this->assign("vo",$selectlist[$_REQUEST['id']]);
		$this->assign("selectlistname",$selectlist[$_REQUEST['id']]['data']);
//		dump($selectlist[$_REQUEST['id']]);die;
		$this->display();
	}
	public function view(){
		$this->edit();
	}
	public function update() {
		$model=D('Lookup');
		$id = $_POST['id'];
		if(file_exists($model->GetFile())){
			$selectlist = require $model->GetFile();
		}
		$arr=array();
		if($_POST['arr_key']){
			foreach ($_POST['arr_key'] as $k=>$v){
				$arr[$v]= $_POST['arr_val'][$k];
			}
		}
		$data=array(
				'title'=>$_POST['title'],
				'fields' => $_POST['fields'],
				'url'=>$_POST['url'],
				'mode' => $_POST['mode'], // 屈强@2014-08-07 新增标题项
				'filed'=> $_POST['filed'],
				'val'=> $_POST['val'],
				'condition'=> $_POST['condition'],
				'status' => $_POST['status'],
		);
		/* 屈强@2014-08-06 修正修改数据时当前记录丢失
		foreach ($selectlist as $key => $val) {
			if ($key == $id) {
				$selectlist[$id] = array(
						'remark'=>$_POST['remark'],
						'status'=>$_POST['status'],
						$_POST['name']=>$arr,
				);
				$selectlist[$_POST['name']] = $selectlist[$id];
				unset($selectlist[$id]);
			}
		}*/
		// 屈强@2014-08-06 给当前对象新设定值
		$selectlist[$id] =  $data;
		$model->SetRules($selectlist);
		$this->success("操作成功");
	}
}

?>