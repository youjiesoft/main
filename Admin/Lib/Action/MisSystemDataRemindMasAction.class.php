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
class MisSystemDataRemindMasAction extends CommonAction { 
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
		//获得查询的类型。0未读，1已读
		$type= $_REQUEST['type']?$_REQUEST['type']:0;
		$this->assign('type',$type);
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
			$map['isread']=$type;
			$map['status']=1;
			//$map['substatus']=1; //子表status作为有用是否终止提醒字段 by xyz
			$map['operation']=1;
// 			if($map['isread']==0){
// 				$readcount=D("MisSystemDataRemindMasView")->where($map)->count();
// 				$this->assign("readcount",$readcount);
// 			} 
			$group = $type?'':'pkey';
			$this->_list ( "MisSystemDataRemindMasView", $map ,'',false,$group);
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
		$this->assign("type",$_REQUEST['type']);
		//end
		if( intval($_POST['dwzloadhtml']) ){
			$this->display("dwzloadindex");exit;
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
	//	$result=$model->where("id=".$id)->save($data);
		
		//添加邮件已读状态
		$map['masid'] = getFieldBy($id,'id','masid','mis_system_data_remind_sub');	
		$map['userid'] = $_SESSION[C("USER_AUTH_KEY")];
		$map['isread'] = array("neq",1);			
		$emodel = M("mis_system_data_eamilremind_sub");
		$model=D("mis_system_data_remind_sub");
		$result=$model->where($map)->save($data);
		$elist = $emodel->where($map)->select();
		if($elist){
			$emodel->where($map)->save($data);
		}
		$model->commit();
		if($_POST['linkstatus']){
			echo $result;
		}else{
			exit;
		}
		
	}
	/**
	 * @Title: lookupchangestatusdll
	 * @Description: todo(批处理标注已读状态)   
	 * @author 谢友志 
	 * @date 2015-12-10 上午9:34:09 
	 * @throws
	 */
	public function lookupchangestatusdll(){
		$idarr = $_POST['ids'];
		if($idarr){
			$map['id'] = array("in",$idarr);
			$model=D("mis_system_data_remind_sub");
			$list = $model->where($map)->getField("masid,id");
			$masids = array_keys($list);
			
			$emap['masid'] = array("in",$masids);
			$emap['userid'] = $_SESSION[C("USER_AUTH_KEY")];
			$emap['isread'] = array("neq",1);
			$data['isread']=1;
			$data['readtime']=time();
			$result=$model->where($emap)->save($data);
			$model->commit();
			//添加邮件已读状态
			$emodel = M("mis_system_data_eamilremind_sub");
			$emodel->where($emap)->save($data);
			echo $result;
		}
		exit;
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
	/**
	 * @Title: lookupExchangeStatus
	 * @Description: todo(更改状态 status 1继续提醒 0终止提醒)   
	 * @author 谢友志 
	 * @date 2015-10-26 下午6:13:15 
	 * @throws
	 */
	public function lookupExchangeStatus(){
		$model = M("mis_system_data_remind_sub");
		$data['status'] = $_REQUEST['status']==1?0:1;
		$map['id'] = $_REQUEST['id'];
		$ret = $model->where($map)->save($data);
		if(false === $ret){
			$this->error();
		}else{
			$model->commit();
		}
		$this->index();
	}
}
?>