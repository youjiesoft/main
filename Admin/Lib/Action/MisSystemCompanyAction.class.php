<?php
/**
 * @Title: file_name
 * @Package package_name
 * @Description: todo(公司信息管理)
 * @author liminggang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-6-17 上午10:49:00
 * @version V1.0
 */
class MisSystemCompanyAction extends CommonAction{

	public function _filter(&$map){
		if ($_SESSION["a"] != 1)$map['status']=array("gt",-1);
		
		if($_REQUEST['parentid']){
			$parentid = $_REQUEST['parentid'];
			$this->assign("parentid",$parentid);
			//查询公司信息
			$MisSystemCompanyDao = M("mis_system_company");
			$where = array();
			$where['status'] = 1;
			$companylist=$MisSystemCompanyDao->where($where)->select();
			$this->assign("companylist",$companylist);
			$parentidArr = array_unique(array_filter (explode(",",$this->findAlldept($companylist,$parentid))));
			$map['id'] = array(" in ",$parentidArr);
		}
	}
	
	public function _before_index(){
		$model = D("MisSystemRecursion");
		$MisSystemCompanyDao = M("mis_system_company");
		$where = array();
		$where['status'] = 1;
		$companylist=$MisSystemCompanyDao->where($where)->select();
		$this->assign("companylist",$companylist);
		//构造结构树
		$param['url']= "__URL__/index/jump/jump/parentid/#id#/id/#id#";
		$param['rel']= "MisSystemCompanyZtree";
		$param['open']= "true";
		$param['isParent']="true";
		if($companylist){
			$companyztree = $this->getTree($companylist,$param);
		}
		//高亮默认选中节点
		$parentid = $_REQUEST['parentid'];
		if(empty($parentid)){
			$parentid = cookie::get("missystemcompanyid");
			cookie::delete("missystemcompanyid");
			if(empty($parentid)){
				$parentid = $companylist[0]['id'];
			}
		} 
		$this->assign('valid',$parentid);
		//赋值用于boolbar
		$this->assign('parentid',$parentid);
		$this->assign("companyztree",$companyztree);
	}
	 public function index(){
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
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
			$this->_list ( $name, $map,'orderno',1);
		}
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
		if($_REQUEST['jump'] == "jump"){
			$this->display('indexview');exit;
		}
		$this->display ();
		return;
	}

	
	
	
	//新增操作
	public function _before_add(){
		//获取上级公司
		$MisSystemCompanyDao = M("mis_system_company");
		$where = array();
		$where['status'] = 1;
		$companylist=$MisSystemCompanyDao->where($where)->getField("id,name");
		$this->assign("companyid",$_REQUEST['companyid']);
		$this->assign("companylist",$companylist);
	}
 	
	public function _before_insert(){
		$parentid = $_POST['parentid'];
		$date=array();
		$name = $this->getActionName();
		$orderno = $_POST['orderno'];
		$MisSystemOrdernoDao = D("MisSystemOrderno");
		$data = $MisSystemOrdernoDao->validateOrderno($name,$orderno,'',$_POST['companyid']);
		if($data['result']){
			$_POST['parentid'] = $data['parentid'];
		}else{
			$this->error($data['altMsg']);
		}
	}
	public function _after_insert($id){
		cookie::set('missystemcompanyid',$id);
		//存入dept表
		$MisSystemDepartmentModel=D('MisSystemDepartment');
		$date['name']=$_POST['name'];
		$date['orderno'] = $_POST['orderno'];
		$date['iscompany']=1;
		$date['companyid']=$id;
		$data['parentid'] = 0;
		if($_POST['parentid']==0){
			$date['isallcompany']=1;
		}
		$deptresult=$MisSystemDepartmentModel->add($date);
		if(!$deptresult){
			$this->error("组织结构添加失败,请联系系统管理员。");
		}
		M('mis_system_company')->where("id=".$id)->setField("deptid",$deptresult);
	}
	/**
	 * @Title: uploadimg
	 * @Description: todo(上传公司logo)
	 * @author renling
	 * @date 2013-6-27 下午4:19:43
	 * @throws
	 */
	public function uploadimg(){
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

	/**
	 * @Title: updateLogoPic
	 * @Description: todo(修改登陆LOGO)
	 * @author renling
	 * @date 2013-6-29 上午11:48:04
	 * @throws
	 */
	public function updateLogoPic(){
		//查询是否已修改公司登陆LOGO
		$CommonSettingModel=M('common_setting'); //配置表
		$picList=$CommonSettingModel->where(" skey='companyico'")->find(); //图片
		$nameList=$CommonSettingModel->where(" skey='companyname'")->find();//名称
		$enameList=$CommonSettingModel->where(" skey='companyename'")->find();//英文名称
		$lastList=$CommonSettingModel->where(" skey='companylast'")->find();//英文名称
		$this->assign("picList",$picList);
		$this->assign("nameList",$nameList);
		$this->assign("enameList",$enameList);
		$this->assign("lastList",$lastList);
		$updatepic=$_POST['updatepic'];
		if($updatepic){
			$pickey=$_POST['pickey'];
			$namekey=$_POST['namekey'];
			$enamekey=$_POST['enamekey'];
			$lastkey=$_POST['lastkey'];
			//修改公司登陆LOGO
			$saveMap['svalue']=$_POST['picture'];
			if($pickey){//修改
				$picresult=$CommonSettingModel->where("  skey='".$pickey."'")->save($saveMap);
			}else{ //添加
				$saveMap['skey']='companyico';
				$picresult=$CommonSettingModel->add($saveMap);
			}
			unset($saveMap);
			if(!$picresult)	{
				$this->error("操作失败,请联系系统管理员！");
			}
			$nameMap['svalue']=$_POST['companyname'];
			if($namekey){//修改
				$nameresult=$CommonSettingModel->where("  skey='".$namekey."'")->save($nameMap);
			}else{//添加
				$nameMap['skey']='companyname';
				$nameresult=$CommonSettingModel->add($nameMap);
			}
			unset($nameMap);
			if(!$nameresult){
				$this->error("操作失败,请联系系统管理员！");
			} 
			$enameMap['svalue']=$_POST['companyename'];
			if($enamekey){//修改
				$enameresult=$CommonSettingModel->where("  skey='".$enamekey."'")->save($enameMap);
			}else{//添加
				$enameMap['skey']='companyename';
				$enameresult=$CommonSettingModel->add($enameMap);
			}
			$lastMap['svalue']=$_POST['companylast'];
			if($lastkey){
				$lastresult=$CommonSettingModel->where("  skey='".$lastkey."'")->save($lastMap);
			}else{
				$lastMap['skey']='companylast';
				$lastresult=$CommonSettingModel->add($lastMap);
			}
			if(!$lastresult){
				$this->error("操作失败,请联系系统管理员！");
			}
			unset($lastMap);
			if(!$enameresult){
				$this->error("操作失败,请联系系统管理员！");
			}else{
				$this->success("操作成功！");
			}
		}
		$this->display();
	}
	//修改操作
	public function _before_edit(){
		//获取上级公司
		$MisSystemCompanyDao = M("mis_system_company");
		$where = array();
		$where['status'] = 1;
		$companylist=$MisSystemCompanyDao->where($where)->getField("id,name");
		$this->assign("companylist",$companylist);
		//判断当前公司下面是否已经生产部门
		$companyid = $_GET['id'];
		//判断是否存在部门了。
		$MisSystemDepartmentDao = M("mis_system_department");
		$where = array();
		$where['status'] = 1;
		$where['companyid'] = $companyid;
		$deptcount=$MisSystemDepartmentDao->where($where)->count();
		$depts = 0;
		if($deptcount){
			$depts =1;
		}
		$this->assign("depts",$depts);
	}
	public function _before_update(){
		$name = $this->getActionName();
		$orderno = $_POST['orderno'];
		$MisSystemOrdernoDao = D("MisSystemOrderno");
		$data = $MisSystemOrdernoDao->validateOrderno($name,$orderno,$_POST['id'],$_POST['companyid']);
		if($data['result']){
			$_POST['parentid'] = $data['parentid'];
		}else{
			$this->error($data['altMsg']);
		}
		if($_POST['oldorderno']!=$_POST['orderno']||$_POST['oldname']!=$_POST['name']){
			//更新编码后 上下结构改变
			$deptMap=array();
			$deptMap['companyid']=$_POST['id'];
			$deptMap['iscompany']=1;
			$saveDate=array();
			$saveDate['orderno']=$_POST['orderno'];
			$saveDate['name']=$_POST['name'];
			$result=M('mis_system_department')->where($deptMap)->save($saveDate);
			if(!$result){
				$this->error("修改部门信息失败！");
			}
		}
	}
	
	public function _before_delete(){ 
		//获取删除的公司ID
		$id = $_REQUEST['id'];
		//判断公司下面是否有子公司和部门
		$MisSystemCompanyDao = M("mis_system_company");
		$where = array();
		$where['status'] = 1;
		$where['parentid'] = $id;
		$companyNamw=$MisSystemCompanyDao->where($where)->getField("name");
		if($companyNamw){
			$this->error("当前公司下面存在".$companyNamw.",请先删除该公司");
		}
		//判断是否存在部门了。
		$MisSystemDepartmentDao = M("mis_system_department");
		$where = array();
		$where['status'] = 1;
		$where['companyid'] = $id;
		$where['_string']=" iscompany !=1";
		$deptcount=$MisSystemDepartmentDao->where($where)->count();
		if($deptcount){
			$this->error("当前公司已经生成了部门信息，请勿删除!");
		}
		//根据status判断是第一次删除还是第二次删除，首先执行删除部门表的公司数据
		$deptmap['companyid'] = $id;
		$deptmap['iscompany'] =1;
		$deptmap['status'] = $MisSystemDepartmentDao->where($deptmap)->getField('status');
		if($deptmap['status']==1){
			$MisSystemDepartmentDao->where ('companyid='.$id )->setField ( 'status', - 1 );
		}elseif($deptmap['status']=='-1'){
			if($_SESSION['a']){
				$MisSystemDepartmentDao->where ( $deptmap )->delete();
			}
		}
		
	}
}
?>