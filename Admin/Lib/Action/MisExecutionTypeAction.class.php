<?php
/**
 * @Title: MisExecutionTypeAction
 * @Package 基础配置-全局执行类型信息：功能类
 * @Description: TODO(全局执行类型信息的记录及维护)
 * @author yangxi
 * @company 重庆特米洛科技有限公司˾
 * @copyright 重庆特米洛科技有限公司˾
 * @date 2013-1-10 19:18:54
 * @version V1.0
 */
class MisExecutionTypeAction extends CommonAction{
/**
	 * @Title: _filter 
	 * @Description: todo(重写CommonAction的_filter方法，传递过滤参数后返回列表页面) 
	 * @return string  
	 * @author 杨希
	 * @date 2013-5-31 下午3:59:44 
	 * @throws
	 */
	public function _filter(&$map){
		if(empty($_REQUEST['status'])) {
			if ($_SESSION["a"] != 1)$map['status']=array("eq",1);
		}
	}
	
	/**
	 * @Title: getTree
	 * @Description: todo(生成左边树结构)
	 * @return json
	 * @author 杨希
	 * @date 2013-5-31 下午3:59:44
	 * @throws
	 */	
	public function getTree(){
		//读取订单类型配置文件
		$model=D('SystemTypeView');
		if(file_exists($model->GetFile())){
			require $model->GetFile();
		}
		$treemiso[]=array(
				'id'=>0,
				'pId'=>-1,
				'name'=>'状态分类',
				'rel'=>'misexecutiontype',
				'target'=>'ajax',
				'url'=>'__URL__/index/jump/1/type/0',
				'open'=>true
		);
		foreach ($aryType as $k => $v) {
			$treemiso[]= array(
					'id'=>$v['type'],
					'pId'=>0,
					'name'=>$v['typename'],
					'rel'=>'misexecutiontype',
					'target'=>'ajax',
					'url'=>'__URL__/index/jump/1/type/'.$v[type]
			);
		}
		return json_encode($treemiso);
	}

	/**
	 * @Title: index
	 * @Description: todo(重写CommonAction的index方法，展示列表)
	 * @return string
	 * @author 杨希
	 * @date 2013-5-31 下午3:59:44
	 * @throws
	 */
	public function index(){
		$this->assign('mistree',$this->getTree());
		$searchtype=array(array("id" =>"2","val"=>"模糊查找"),array("id" =>"1","val"=>"精确查找"));
		$this->assign('searchtypelist',$searchtype);
		$map = $this->_search();
		$type=$_REQUEST['type'];
		if($type){
			$map['typeid'] = $type;
			$this->assign('type',$type);
		}
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name = 'MisExecutionType';
		//动态配置部分。
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($name);
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		//searchby搜索扩展
		$searchby = $scdmodel->getDetail($name,true,'searchby');
		if ($searchby && $detailList) {
			$searchbylist=array();
			foreach( $detailList as $k=>$v ){
				if(isset($searchby[$v['name']])){
					$arr['id']= $searchby[$v['name']]['field'];
					$arr['val']= $v['showname'];
					array_push($searchbylist,$arr);
				}
			}
			$this->assign("searchbylist",$searchbylist);
		}
		//扩展工具栏操作
		$toolbarextension = $scdmodel->getDetail($name,true,'toolbar');
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		if( intval($_POST['dwzloadhtml']) ){$this->display("dwzloadindex");exit;}
		//查询数据部分
		if (! empty ( $name )) {
			$qx_name=$name;
			if(substr($name, -4)=="View"){
				$qx_name = substr($name,0, -4);
			}
			//验证浏览及权限
			if( !isset($_SESSION['a']) ){
				$map=D('User')->getAccessfilter($qx_name,$map);	
			}
			$this->_list ( $name, $map );
		}
		if ($_REQUEST['jump']) {
			$this->display('typeindex');
		} else {
			$this->display();
		}
	}

	/**
	 * @Title: _before_add
	 * @Description: todo(add页面打开前传递展示信息)
	 * @return string
	 * @author 杨希
	 * @date 2013-5-31 下午3:59:44
	 * @throws
	 */	
	public function _before_add()
	{
		$type=$_REQUEST['type'];
		$this->assign("type",$type);
		//订单号可写
		$scnmodel = D('SystemConfigNumber');
		$writable= $scnmodel->GetWritable('mis_execution_type');
		$this->assign("writable",$writable);
		$code = $scnmodel->GetRulesNO('mis_execution_type');
		$this->assign("code", $code);
		//读取订单类型配置文件
		$model=D('SystemTypeView');
		if(file_exists($model->GetFile())){
			require $model->GetFile();
		}
		$this->assign("list",$aryType);
	}

	/**
	 * @Title: _before_insert
	 * @Description: todo(插入方法insert前执行操作)
	 * @return string
	 * @author 杨希
	 * @date 2013-5-31 下午3:59:44
	 * @throws
	 */
	public function _before_insert(){
		$this->checkifexistcodeororder('mis_execution_type','code');
	}

	/**
	 * @Title: _before_edit
	 * @Description: todo(edit页面前传入数据)
	 * @return string
	 * @author 杨希
	 * @date 2013-5-31 下午3:59:44
	 * @throws
	 */
	public function _before_edit()
	{
		//获取到对应需要修改的单据ID
		$id=$_GET['id'];
		//实例化表对象
		$OrderTypes	=D("mis_order_types");
		//对数据采集
		$OrderTypesList=$OrderTypes->where("id='".$id."'")->find();
		//分派给模板
		$this->assign('OrderTypesList',$OrderTypesList);
		//读取订单类型配置文件
		$model=D('SystemTypeView');
		if(file_exists($model->GetFile())){
			require $model->GetFile();
		}
		$this->assign("typelist",$aryType);
		//编号可写
		$model1	=D('SystemConfigNumber');
		$writable= $model1->GetWritable('mis_execution_type');
		$this->assign("writable",$writable);
	}

	/**
	 * @Title: _before_update
	 * @Description: todo(更新方法update前执行操作)
	 * @return string
	 * @author 杨希
	 * @date 2013-5-31 下午3:59:44
	 * @throws
	 */	
	public function _before_update(){
		$this->checkifexistcodeororder('mis_execution_type','code',1);
	}
}
?>