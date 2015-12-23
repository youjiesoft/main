<?php
/**
 * @Title: MisHrInvitereFormAction 
 * @Package 人事管理-人员招聘 
 * @Description: todo(人员招聘管理) 
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-3-27 上午11:21:33 
 * @version V1.0
 */
class MisHrInvitereFormAction extends CommonAction {
	/** @Title: _filter
	 * @Description: (构造检索条件) 
	 * @author  
	 * @date 2013-5-31 下午3:59:44 
	 * @throws 
	*/
	public function _filter(&$map){
		if ($_SESSION["a"] != 1)$map['status']=array("gt",-1);
	}
	/* (non-PHPdoc) 显示列表
	 * @see CommonAction::index()								
	*/
	function index(){
		//渠道
		$model=M("mis_hr_typeinfo");
		$listQD =$model->where("status=1 and typeid=2 and pid=41")->field("id,name")->select();
		$QDroot = Array(
				'0'=> Array('id'=>'0','name'=>'招聘渠道','parentid'=>'0','findKeyType'=>'mqd')
			);
			
		$newQD = array_merge($QDroot, $listQD);
		//添加爷节点标记
		foreach( $newQD as $k=>$v){
			if(!isset($v['parentid'])){
				$newQD[$k]['parentid']= 0;
				$newQD[$k]['findKeyType']= "qd"; 				//渠道
			}
		}
		
		//招骋方式
		$model=M("mis_hr_typeinfo");
		$listFS =$model->where("status=1 and typeid=2 and pid=100")->field("id,name")->select();
		$FSroot = Array(
				'1000'=> Array('id'=>'1000','name'=>'招聘方式','parentid'=>'1000','findKeyType'=>'mfs')
			);
			
		$newFS = array_merge($FSroot, $listFS);

		//添加爷节点标记
		foreach( $newFS as $k=>$v){
			if(!isset($v['parentid'])){
				$newFS[$k]['parentid']= 1000;
				$newFS[$k]['findKeyType']= "fs"; 				//方式
			}
		}
// 		dump($newFS);
		//合并两个招骋方式的数据	
		$twoArray = array_merge($newFS, $newQD);

		$typeTreeFS = $this->getZtreelist($twoArray);

		$this->assign('typetree',$typeTreeFS);

		//搜索开始
		$map=$this->_search();
			
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
			

		//动态配置显示字段
		$name=$this->getActionName();
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($name);
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		//扩展工具栏操作
		$toolbarextension = $scdmodel->getDetail($name,true,'toolbar');
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}

		//筛选不同分类的结果显示出来
		$findKeyType = $_REQUEST['findKeyType'];
		$findKeyTypeValue = $_REQUEST['typeid'];
		switch($findKeyType){
			case "fs":
				$map['introducemethod'] = $findKeyTypeValue;
				break;
			case "qd":
				$map['inviteresources'] = $findKeyTypeValue;
				break;
				/*
			case "mqd":
				$map['introducemethod'] = $findKeyTypeValue;
				break;
			case "mfs":
				$map['introducemethod'] = $findKeyTypeValue;
				break;
				*/
		}
		//保留条件
		$this->assign('findKeyType',$findKeyType);
		
		//去_list方法中把数据取出来
		if (! empty ( $name )) {
			$qx_name=$name;
			if(substr($name, -4)=="View"){
				$qx_name = substr($name,0, -4);
			}

			//验证浏览及权限
			if( !isset($_SESSION['a']) ){
				$map=D('User')->getAccessfilter($qx_name,$map);	
			}
			$this->_list ($name, $map); //,'','','',1);
		}
		if( intval($_POST['dwzloadhtml']) ){
			$this->display("dwzloadindex");exit;
		}
		//jump=1 
		if ($_REQUEST['jump'] ) {

			$this->display('right');

		} else {
			$this->display();
		}
	}
	public function _after_list(&$voList){
		foreach ($voList as $k=>$v){
			$name=getSelectByName("isinjob", $v['isinjob']);
			if($v['isinjob']==0){
				$voList[$k]['isinjob']="<span style='color:red;'>".$name."</span>";
			}else if($v['isinjob']==1){
				$voList[$k]['isinjob']=$name;
			}
		}
	}
	/**
	 * @Title: _before_edit
	 * @Description: todo(打开修改页面前置函数)   
	 * @author  
	 * @date 2013-5-31 下午4:11:26 
	 * @throws 
	*/  
	public function _before_edit(){
		$this->common();
	}
	/**
	 * @Title: _before_add
	 * @Description: todo(打开页面前置函数)
	 * @author 
	 * @throws
	 */
	public function _before_add(){
		$this->common();
		$this->assign("time",time());
	}
	/**
	 * @Title: common 
	 * @Description: todo(公共调用函数)   
	 * @author  
	 * @date 2013-5-31 下午4:07:47 
	 * @throws 
	*/ 
	private function common(){
		//渠道
		$model=M("mis_hr_typeinfo");
		$listQD =$model->where("status=1 and typeid=2 and pid=41")->field("id,name")->select();
		$this->assign("inviteresourceslist",$listQD);
		//学历
		$model=M("mis_hr_typeinfo");
		$listedu =$model->where("status=1 and typeid=2 and pid=44")->field("id,name")->select();
		$this->assign("educationlist",$listedu);
		//招骋方式
		$model=M("mis_hr_typeinfo");
		$listFS =$model->where("status=1 and typeid=2 and pid=100")->field("id,name")->select();
		$this->assign("introducemethodlist",$listFS);
		
	}
	
	/**
	 * 构树形
	 * @param array  $alldata  所有部门信息
	 * @param int $pid  部门节点ID
	 * @param int $i  节点等级
	 * @return string
	 */
	private function getZtreelist($alldata){
		$returnarr=array();
		foreach($alldata as $k=>$v){
			$newv=array();
			$newv['id']=$v['id'];
			$newv['pId']=$v['parentid'];
			$newv['name']='-'.$v['name'];
			$newv['type']='post';
			if($v['parentid']==0 || $v['parentid']==1000  ){
				$newv['url']='__URL__/index/jump/1/typeid/'.$v['id'].'/findKeyType/'.$v['findKeyType'];
			}else{
				$newv['url']='__URL__/index/jump/1/findKeyType/'.$v['findKeyType'];
			}
			$newv['target']='ajax';
			$newv['rel']='mishrinvitereformmodel';
			$newv['open']='true';
			array_push($returnarr,$newv);
		}
		return json_encode($returnarr);
	}
}
?>