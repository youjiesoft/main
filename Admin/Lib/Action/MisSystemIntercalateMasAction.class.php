<?php
 /**
  * 
  * @Title: MisSystemIntercalateMasAction 
  * @Package package_name
  * @Description: todo(管理员面板设置) 
  * @author renling 
  * @company 重庆特米洛科技有限公司
  * @copyright 本文件归属于重庆特米洛科技有限公司
  * @date 2015年5月21日 下午3:05:11 
  * @version V1.0
  */
class MisSystemIntercalateMasAction extends CommonAction{
	public function _before_index(){
	  	//左边树
	  	if(!$_REQUEST['masid']){
	  		$name = $this->getActionName();
	  		$model=D($name);
	  		$map['status'] = 1;
	  		$maslist = $model->where($map)->select();
	  		//组装左边树
	  		$param['rel']="MisSystemIntercalateMasBox";
	  		$param['url']="__URL__/index/jump/jump/masid/#id#";
	  		$treemiso[]=array(
	  				'id'=>0,
	  				'pId'=>0,
	  				'name'=>'分类设计',
	  				'title'=>'分类设计',
	  				'open'=>true,
	  				'isParent'=>true,
	  				'rel'=>"MisSystemIntercalateMasBox",
	  				'url'=>"__URL__/index/jump/jump",
	  				'target'=>'ajax'
	  		);
	  		$treearr = $this->getTree($maslist,$param,$treemiso,false);
	  		$this->assign("treearr",$treearr);
	  	}else if($_REQUEST['masid']){
	  		//获取数据
	  		$map['masid'] = $_REQUEST['masid'];
	  		$submodel = M("mis_system_intercalate_sub");
	  		$sublist = $submodel->where($map)->select();
	  		$this->assign('list',$sublist);
	  		//获取配置文件
	  		$detailList = require ROOT . '/Dynamicconf/Models/MisSystemIntercalateMas/sublist.inc.php';
	  		if ($detailList) {
	  			$this->assign ( 'detailList', $detailList );
	  		}
	  		//获取toolbar配置文件
	  		$toolbarextension = require  ROOT . '/Dynamicconf/Models/MisSystemIntercalateMas/subtoolbar.extension.inc.php';
	  		//扩展工具栏操作
	  		foreach ($toolbarextension as $k => $v) {
	  			$extendurl = $v["extendurl"];	  			
  				if($extendurl){
  					eval("\$extendurl =".$extendurl.";");
  					$toolbarextension[$k]['html'] = str_replace( "#extendurl#",$extendurl,$v['html'] );
  				}
	  		}
	  		if ($toolbarextension) {
	  			$this->assign ( 'toolbarextension', $toolbarextension );
	  		}
	  		$this->display("subindexview");
	  	}
	}
	public function _before_add(){
		if($_REQUEST['masid']){
			$this->display("addsub");
			exit;
		}		
	}
	public function _before_edit(){
		if($_REQUEST['masid']){
			$model=M("mis_system_intercalate_sub");
			$id=$_REQUEST['id'];
			$vo=$model->where("id=".$id)->find();
			$this->assign("vo",$vo);
			$this->display("editsub");
			exit;
		}
	}
	public function insertsub(){
		$model=M("mis_system_intercalate_sub");
		if($_POST['modelid']){
			$_POST['modelname']=getFieldBy($_POST['modelid'], "id", "name", "node");
		}
		$data = $model->create ();
		if (false === $data) {
				$this->error ( $model->getError () );
		}
		//保存当前数据对象
		try {
			$list=$model->add($data);
			//echo $model->getlastsql();
		}catch (Exception $e){
			$this->error($e->__toString());
		}
		$msg = $model->getlastsql().arr2string($data);
		if(false===$list){
			$this->error($msg );
		}else{
			$this->success("操作成功！");
		}
	}
	
	public function updatesub(){
		$model = M( "mis_system_intercalate_sub" );		
		$name=$this->getActionName();
		if (false ===  $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$list=$model->save ();
		if (false !== $list) {
			$this->success ( L('_SUCCESS_'));
		} else {
			$this->error ( L('_ERROR_') );
		}
	}
	public function _before_delete(){
		if($_REQUEST['type']){
			//删除sub表
			$result=M("mis_system_intercalate_sub")->where("id=".$_REQUEST['id'])->delete();
			if($result===false){
				$this->error("操作失败！");
			}else{
				$this->success("操作成功！");
			}
			exit;
		}
	}

}