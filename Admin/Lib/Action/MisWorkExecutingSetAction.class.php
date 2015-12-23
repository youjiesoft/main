<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(工作中心 工作执行设置) 
 * @author libo 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-6-26 下午2:25:31 
 * @version V1.0
 */
class MisWorkExecutingSetAction extends CommonAction{
	public function _filter(&$map){
		if( !isset($_SESSION['a']) ){
			$map['status']=1;
		}
	}
	public function _before_insert(){
		//查询当前model名是否以创建
		$ExecutingSetModel=D("MisWorkExecutingSet");
		$re=$ExecutingSetModel->where("status=1 and name='".$_REQUEST['name']."'")->getfield("id");
		if($re){
			$this->error("该模型已创建工作执行入口,请不要重复创建");
			exit;
		}
		$this->setexecutingfile();
	}
	public function _before_update(){
		//查询当前model名是否以创建
		$ExecutingSetModel=D("MisWorkExecutingSet");
		$re=$ExecutingSetModel->where("status=1 and name='".$_REQUEST['name']."' and id !=".$_REQUEST['id'])->getfield("id");
		if($re){
			$this->error("该模型已创建工作执行入口,请不要重复创建");
			exit;
		}
		$this->setexecutingfile();
	}
	/**
	 * @Title: setexecutingfile 
	 * @Description: todo(生成处理页面)   
	 * @author libo 
	 * @date 2014-7-7 上午9:49:40 
	 * @throws
	 */
	public function setexecutingfile(){
		import('@.ORG.FileUtil');//引入文件
		$obj = new FileUtil();//实例化对象
		$chekDir = ROOT.'/Tpl/default/'.$_REQUEST['name'];//设置找查目录
		$createDir=ROOT.'/Tpl/default/MisWorkExecuting';
		//返回日志记录位置
		$a=$obj->checkFile($chekDir,$_REQUEST['name'].'.html',array('auditview.html','view.html','edit.html'),$createDir,false,true);
	}
}

?>