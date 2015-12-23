<?php
/**
 * 
 * @Title: MisSystemDataRemindAction 
 * @Package package_name
 * @Description: todo(提醒中心) 
 * @author renling 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015年7月24日 下午2:19:07 
 * @version V1.0
 */
class MisSystemSysremindAction extends CommonAction { 
	public function index() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		if($_REQUEST['fieldtype']){
			$this->getBindSetTables($map);
		}
		if($_REQUEST['projectid']){
			$map['projectid'] = $_REQUEST['projectid'];
		}
		if($_REQUEST['projectworkid']){
			//$map['projectworkid'] = $_REQUEST['projectworkid'];
		}
		$name = $this->getActionName();
		if (! empty ( $name )) {
			$qx_name=$name;
			if(substr($name, -4)=="View"){
				$qx_name = substr($name,0, -4);
			}
			//验证浏览及权限
			if( !isset($_SESSION['a']) ){
				$map=D('User')->getAccessfilter($qx_name,$map);
			}
			//读取当前用户提醒
			$map['userid']=$_SESSION[C('USER_AUTH_KEY')];
			$map['isread']=$_REQUEST['type']?$_REQUEST['type']:0;
			$map['status']=1;
// 			if($map['isread']==0){
// 				$readcount=D("MisSystemDataRemindMasView")->where($map)->count();
// 				$this->assign("readcount",$readcount);
// 			}
			$this->_list ( $name, $map );
		}
		//begin
		$scdmodel = D('SystemConfigDetail');
		//读取列名称数据(按照规则，应该在index方法里面)
		$detailList = $scdmodel->getDetail($name,true,'','sortnum');
		if(file_exists(ROOT . '/Dynamicconf/Models/'.$name.'/form.inc.php')){
			$anameList = require ROOT . '/Dynamicconf/Models/'.$name.'/form.inc.php';
			if(!empty($detailList) && !empty($anameList)){
				foreach($detailList as $k => $v){
					$detailList[$k]["datatable"] = 'template_key=""';
					foreach($anameList as $kk => $vv){
						if($k==$kk){
							$detailList[$k]["datatable"] = $vv["datatable"];
						}
					}
				}
			}
		}
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		//扩展工具栏操作
		$toolbarextension = $scdmodel->getDetail($name,true,'toolbar','sortnum','shows',true);
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		//end
		if( intval($_POST['dwzloadhtml']) ){
			$this->display("dwzloadindex");exit;
		}
		//首页收件箱调用方法，为ajax调用
		if ($_GET['type'] == "ajaxcall") {
			$this->display ("ajax_index");exit;
		}
		if($_REQUEST['jump'] == "jump"){
			$this->display('indexview');exit;
		}
		$this->display ();
		return;
	}
	public function lookupsavesend(){
		$id=$_POST['id'];
		$model=D("mis_system_data_remind_sub");
		$data['isread']=1;
		$data['readtime']=time();
		$result=$model->where("id=".$id)->save($data);
		$model->commit();
		echo $result;
	}
	public function lookupgetRemindCount(){
		$map=array();
		$map['userid']=$_SESSION[C('USER_AUTH_KEY')];
		$map['isread']=0;
		$map['status']=1;
		$map['substatus']=1;
		$map['operation']=1;
		$readcount=D("MisSystemDataRemindMasView")->where($map)->count();
		echo $readcount;
// 		if($readcount){
// 			$html = $this->fetch("sysgmsgscheduleremind");
// 			$rehtml["html"]= $html;
// 			echo json_encode($rehtml); 
// 		} 
	}
	public function view(){
		$id=$_REQUEST['id'];
		$model=M('mis_system_sysremind');
		$list=$model->where("id={$id}")->find();
		$content=$this->fetch("MisAutoCbj:view");
		$this->display();
	}
}
?>