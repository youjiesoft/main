<?php
/**
 * @Title: MisSystemFunctionalBox
 * @Package package_name
 * @Description: todo(功能盒子)
 * @author libo
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-1-7 上午10:49:34
 * @version V1.0
 */
class MisSystemFunctionalBoxAction extends CommonAction{
	public function _before_edit(){
		//获取group
		$model=D("Group");
		$list=$model->where("status=1")->select();
		$this->assign('list',$list);
	}
	public function insert(){
		$data=array();
		$data['qlink']=getSelectByName('linkprefix',$_POST['qlink']);
		$data['userid']=$_SESSION[C('USER_AUTH_KEY')];
		$data['name']=$_POST['name'];
		$data['logo']=$_POST['logo'];
		$data['link']=$_POST['link'];
		$data['height']=$_POST['height'];
		$MisSystemFunctionalBoxModel=D('MisSystemFunctionalBox');
		$MisSystemFunctionalBoxResult=$MisSystemFunctionalBoxModel->add($data);
		$MisSystemFunctionalBoxModel->commit();
		if ($MisSystemFunctionalBoxResult!==false) { //保存成功
			$this->success ( L('_SUCCESS_'));
		} else {
			$this->error ( L('_ERROR_') );
		}
		exit;
	}
	public function update(){
		$data=array();
		$data['qlink']=getSelectByName('linkprefix',$_POST['qlink']);
		$data['userid']=$_SESSION[C('USER_AUTH_KEY')];
		$data['name']=$_POST['name'];
		$data['logo']=$_POST['logo'];
		$data['link']=$_POST['link'];
		$data['height']=$_POST['height'];
		$data['id']=$_POST['id'];
		$MisSystemFunctionalBoxModel=D('MisSystemFunctionalBox');
		$MisSystemFunctionalBoxResult=$MisSystemFunctionalBoxModel->save($data);
		$MisSystemFunctionalBoxModel->commit();
		if ($MisSystemFunctionalBoxResult!==false) { //保存成功
			$this->success ( L('_SUCCESS_'));
		} else {
			$this->error ( L('_ERROR_') );
		}
		exit;
	}
	/**
	 * @Title: lookupcomboxgetgroup
	 * @Description: todo(联动获取面板)
	 * @author libo
	 * @date 2014-1-7 下午1:33:06
	 * @throws
	 */
	public function lookupcomboxgetgroup(){
		$Model=D("Node");
		$groupid=$_POST['groupid'];
		if($_POST['whereobj'] == 1){
			$nodelist=$Model->where('type=1 and status=1 and group_id='.$groupid)->field('id,title')->select();
		}else if($_POST['whereobj'] == 2){
			$nodelist=$Model->where('type=3 and status=1 and pid='.$groupid)->field('id,title')->select();
		}
		$arr=array();
		foreach($nodelist as $k=>$v){
			$arr[$k][]=$v['id'];
			$arr[$k][]=$v['title'];
		}
		if($arr){
			echo json_encode($arr);
		} else {
			echo false;
		}
	}
	//获取logo
	public function lookuploadimg(){
		//添加成功后，临时文件夹里面的文件转移到目标文件夹
		$fileinfo=pathinfo($_POST['upload_name']);
		$from = UPLOAD_PATH_TEMP.$_POST['upload_name'];//临时存放文件
		if( file_exists($from) ){
			$p=UPLOAD_PATH.$fileinfo['dirname'];// 目标文件夹
			if( !file_exists($p) ) {
				$this->createFolders($p); //判断目标文件夹是否存在
			}
			$to= UPLOAD_PATH.$_POST['upload_name'];
			rename($from,$to);
			echo $_POST['upload_name'];
		}
	}
}


?>