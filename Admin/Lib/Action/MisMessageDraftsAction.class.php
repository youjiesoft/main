<?php
/**
 * @Title: MisMessageDraftsAction
 * @Package package_name
 * @Description: todo(草稿箱功能)
 * @author liminggang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2012-12-20 上午10:16:55
 * @version V1.0
 */
class MisMessageDraftsAction extends CommonAction {

	public function _filter(&$map=array()){
		$map['status'] = 1;
		$map['commit'] = 0;
		$map['createid'] = $_SESSION [C ( 'USER_AUTH_KEY' )];
	}
	
	public function delete(){
		//删除指定记录
		$name="MisMessageUser";
		$model = D ($name);
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				//$list=$model->where ( $condition )->setField ( 'status', - 1 );
				$list = $model->where( $condition )->delete();//真删
				if ($list!==false) {
					
						$this->success ('删除成功！' );
				}
				else{
					$this->error ('删除失败！');
				}
				
			} else {
				$this->error ( '非法操作' );
			}
		}
	}
	
	public function edit(){
		$modelMM = A('MisMessage');
		$modelMM->searchuser();
		//找出要编辑数据
		$modelMMU = D("MisMessageUser");
		$id = $_REQUEST [$modelMMU->getPk ()];
		//取出邮件ID
		$mailID = $modelMMU->where('id='.$id)->getField('sendid');
		//取出邮件
		$modelMM = D('MisMessage');
		//分配到模板
		$userModel = D('User');
		$vo = $modelMM->getById ( $mailID );
		if ($vo['recipient']) {
			$recipientarr = explode(',', $vo['recipient']);
			$vo['recipient'] = array();
			$recipientnamearr = explode(',', $vo['recipientname']);
			$vo['recipientname'] = array();
			$map = array();
			$map['id'] = array('in',$recipientarr);
			$emailArr = $userModel->where($map)->getField('id, email');
			foreach ($recipientarr as $k=>$v){
				$vo['recipient'][$v] = $v;
				$vo['recipientname'][$v] = $recipientnamearr[$k];
				$vo['email'][$v] = $emailArr[$v];
			}
		}
		if ($vo['copytopeopleid']) {
			$copytopeopleidarr = explode(',', $vo['copytopeopleid']);
			$vo['copytopeopleid'] = array();
			$copytopeoplenamearr = explode(',', $vo['copytopeoplename']);
			$vo['copytopeoplename'] = array();
			//获取email地址
			$map = array();
			$map['id'] = array('in',$copytopeopleidarr);
			$emailCopyArr = $userModel->where($map)->getField('id, email');
			foreach ($copytopeopleidarr as $key => $val) {
				$vo['copytopeopleid'][$val] = $val;
				$vo['copytopeoplename'][$val] = $copytopeoplenamearr[$key];
				$vo['emailCopy'][$val] = $emailCopyArr[$val];
			}
		}
		$this->assign ( 'vo', $vo );
		//获取附件信息
		$this->getAttachedRecordList($mailID,true,true,array(' in', array('MisMessageDrafts','MisMessage','MisMessageInbox')));
		//查询所有的部门
		$this->display();
	}
	/**
	 * @Title: _before_update 
	 * @Description: todo(保存前置函数)   
	 * @author xiafengqin 
	 * @date 2013-8-14 上午9:57:57 
	 * @throws
	 */
	public function _before_update(){
		if ($_POST['recipient']) {
			$_POST['recipient'] = implode(',', $_POST['recipient']);
			$_POST['recipientname'] = implode(',', $_POST['recipientname']);
		}
		if (!$_POST['copytopeopleid']){
			$_POST['copytopeopleid'] = '';
			$_POST['copytopeoplename'] = '';
		}else {
			$_POST['copytopeopleid'] = implode(',',$_POST['copytopeopleid']);
			$_POST['copytopeoplename'] = implode(',',$_POST['copytopeoplename']);
		}
	}
	/*
	 * 编辑草
	 *
	 * */
	public function update() {
		$name="MisMessage";
		$model = D ( $name );
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		if (!$_POST['recipient']){
			$this->error ( L('收件人不能为空') );
		}
		$list=$model->save ();
		$MisMessageID = $_POST['id'];
		if (false !== $list) {
			if($_POST['commit']){
				$modelMMU = D('MisMessageUser');
				//获得收件人ID （多个收件人）
				$user = explode(',', $_POST['recipient']);  
				$copyuser = explode(',', $_POST['copytopeopleid']);
				$user = array_merge($user, $copyuser);
				$MisMessageID = $_POST['id'];
				unset($_POST['id']);
				foreach ($user as $k => $v) {
					$_POST['recipient'] = $v;
					$_POST['sendid'] = $MisMessageID;
					//用create对应一下
					if (false === $modelMMU->create ()) {
						$this->error ( $modelMMU->getError () );
					}
					$list1=$modelMMU->add ();
					if(!$list1){
						$this->error ("error");
					}
				}
				//修改MisMessageUser表中，草稿邮件为发出邮件
				$CMMU['sendid'] = $MisMessageID;
				$CMMU['recipient'] = -$_SESSION [C ( 'USER_AUTH_KEY' )];
				$resultMMU = $modelMMU->where($CMMU)->setField('commit',1);
				if(!$resultMMU){
					$this->error ("error");
				}
			}
			$this->swf_upload($MisMessageID, 64);
			$this->success ( L('_SUCCESS_'));
		} else {
			$this->error ( L('_ERROR_') );
		}
	}
	/**
	 *
	 * @Title: lookupmanage
	 * @Description: todo(用ztree形式查询出所有部门员工信息。)
	 * @author liminggang
	 * @throws
	 */
	public function lookupmanage(){
		$this->assign('ulId', $_REQUEST['ulId']);
		$model= M('mis_system_department');
		$deptlist = $model->where("status=1")->order("id asc")->select();
		$param['rel']="positiveBox";
		$param['url']="__URL__/lookupmanage/jump/1/deptid/#id#/parentid/#parentid#/ulId/".$_REQUEST['ulId'];
		$typeTree = $this->getTree($deptlist,$param);
		$this->assign('tree',$typeTree);
		$map = array();
		$searchby = str_replace("-", ".", $_POST["searchby"]);
		$keyword=$this->escapeChar($_POST["keyword"]);
		$searchtype = $_POST['searchtype'];
		if($_POST["keyword"]){
			$map[$searchby] = ($searchtype==2)  ? array('like','%'.$keyword.'%'):$keyword;
			$this->assign('keyword',$keyword);
			$searchby = str_replace(".", "-", $_POST["searchby"]);
			$this->assign('searchby',$searchby);
			$this->assign('searchtype',$searchtype);
		}
		$searchby=array(
				array("id" =>"user-name","val"=>"按员工姓名"),
				array("id" =>"orderno","val"=>"按员工编号"),
		);
		$searchtype=array(array("id" =>"2","val"=>"模糊查找"),
				array("id" =>"1","val"=>"精确查找"));
		$this->assign("searchbylist",$searchby);
		$this->assign("searchtypelist",$searchtype);
		$map['working']=1;
		$map['user.status']=1;
		$deptid		= $_REQUEST['deptid'];
		$parentid	= $_REQUEST['parentid'];
		if ($deptid && $parentid) {
			$deptlist =array_unique(array_filter (explode(",",$this->downAllChildren($deptlist,$deptid))));
			$map['user.dept_id'] = array(' in ',$deptlist);
		}

		$this->_list('MisHrPersonnelPersonInfoView',$map);
		$this->assign('deptid',$deptid);
		$this->assign('parentid',$parentid);
		if ($_REQUEST['jump']) {
			$this->display('lookupmanagelist');
		} else {
			$this->display("lookupmanage");
		}
	}
	public function insertInterEmail(){
		if ($_POST['email']) {
			$emailStr = implode(',', $_POST['email']);
		}else {
			$this->error ( '邮件地址不能为空！' );
		}
		if (!$_POST['content']) {
			$this->error ( '邮件内容不能为空！' );
		}
		if ($_POST['messageTypeNmae']) {
			$configEmailModel = D('MisSystemEmail');
			$map = array();
			$map['status'] = 1;
			$map['defaultemail'] = 1;
			$map['userid'] = $_SESSION[C('USER_AUTH_KEY')];
			$vo = $configEmailModel->where($map)->find();
		}
		$attachmentlistnew = array();
		foreach ($_POST['swf_upload_save_name'] as $key => $val) {
			$path = pathinfo($val);
			$oldpath = UPLOAD_PATH_TEMP . $_POST['swf_upload_save_name'][$key];
			$newpath = UPLOAD_PATH_TEMP . $path['dirname'] . '/' . $_POST['swf_upload_source_name'][$key];
			if(file_exists($oldpath)){
				$isrename = rename($oldpath,$newpath);
				if ($isrename) {
					$attachmentlistnew[] = $newpath;
				}
			}
		}
		$result = $this->SendEmail($_POST['title'], $_POST['content'], $emailStr, $attachmentlistnew, $vo, $_POST['messageTypeNmae']);
		if($result){
			$this->update();
			$this->success( "发送成功！");
		} else {
			$this->error( "发送失败，请联系管理员!");
		}
	}
}
?>