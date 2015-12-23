<?php
/**
 * @Title: MisNewsAction
 * @Package package_name
 * @Description: todo(公司新闻)
 * @author laicaixia
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-5-31 下午4:50:55
 * @version V1.0
 */
class MisMessagePhoneAction extends CommonAction{
	//put your code here
	/**
	* @Title: _filter
	* @Description: todo(检索)
	* @param unknown_type $map
	* @author laicaixia
	* @date 2013-5-31 下午4:51:38
	* @throws
	*/
	public function _filter(&$map){
		//管理员权限判断
		if ($_SESSION["a"] != 1)$map['status']=array("gt",-1);
		if($_REQUEST['flag'] != NULL){
			$map['flag']=$_REQUEST['flag'];
		}
		$this->assign("flag",$_REQUEST['flag']);
	}
	public function _before_index(){
		$this->getlefttree();
	}
	public function  _after_list($list){
		foreach($list as $k=>$v){
			$userArray = explode(',', $v['userid']);
			foreach($userArray as $kk=>$vv){
				$userArray[$kk] = '[<b style="color:#2698ab;">'.getFieldBy($vv,'id','name','user').'</b>]';
			}
			$list[$k]['userid'] =implode(",", $userArray);		 								//重新压回到，列表记录中去
		}
		//dump($list);
		/*
//处理，接收人电话号前加人名
		$phoneArray = explode(',', $getInfo['phone']);
		foreach($phoneArray as $key=>$value){
			$phoneArray[$key] = '<b style="color:#000;">['.getFieldBy($value,'mobile','name','user').']</b>'.$value;
		}
		$getInfo['phone'] =implode("&nbsp;&nbsp;", $phoneArray);		 								//重新压回到，列表记录中去
*/
	}
	/**
	 * @Title: common
	 * @Description: todo(公共函数)   
	 * @author 杨东 
	 * @date 2013-6-1 下午4:33:56 
	 * @throws 
	*/  
	private function common(){
		$mismodel=D("MisSystemDepartment");
		$mislist =$mismodel->where('status = 1')->field("id,name")->select();
		$this->assign("department_idlist",$mislist);
	}	
	 
	private function getlefttree(){
		$tree = array(); // 树初始化
		$tree[] = array(
				'id' => 1,
				'pId' => 0,
				'name' => '短消息记录',
				'title' => '短消息记录',
				'rel' => "MisMessagePhoneModel",
				'target' => 'ajax',
				'icon' => "",
				'url' => "__URL__/index/jump/jump",
				'open' => true
		);
		$treez[] = array(
				'id' => 2,
				'pId' =>1,
				'name' =>'草稿信息',
				'title' =>'草稿信息',
				'rel' => "MisMessagePhoneModel",
				'target' => 'ajax',
				'icon' => "",
				'url' => "__URL__/index/jump/jump/flag/0",
				'open' => true
		);
		$treez[] = array(
				'id' => 3,
				'pId' =>1,
				'name' =>'已发信息',
				'title' =>'已发信息',
				'rel' => "MisMessagePhoneModel",
				'target' => 'ajax',
				'icon' => "",
				'url' => "__URL__/index/jump/jump/flag/1",
				'open' => true
		);
		$tree=array_merge($tree,$treez);
		$this->assign("tree",json_encode($tree));
	}

	/**
	 * @Title: _before_add
	 * @Description: todo(进入新增)
	 * @author laicaixia
	 * @date 2013-5-31 下午4:51:46
	 * @throws
	 */
	public function _before_add(){
		//查询所有的部门,显示在选人列表
		$this->searchuser();
		$this->display();
	}
	/**
	 * @Title: _before_edit
	 * @Description: todo(进入修改)
	* @author laicaixia
	* @date 2013-5-31 下午4:51:56
	* @throws
	*/
	public function edit(){
		$map['id'] = $_REQUEST['id'];
		$name=$this->getActionName();
		$model = D ($name);
		$smsInfo = $model-> where($map)->find();
		$userArr=explode(",", $smsInfo['userid']);
		$this->assign('userArr',$userArr);
		$this->assign('vo',$smsInfo);
		//dump($smsInfo);
		$this->searchuser();
		if($smsInfo['flag']==1){
			$this->showMessage();
		}else{
			$this->display('edit');
		}
		
	}
	/**
	 * @Title: _before_edit
	 * @Description: todo(进入修改)
	* @author laicaixia
	* @date 2013-5-31 下午4:51:56
	* @throws
	*/
	public function _before_insert(){
// 		dump($_REQUEST);die;
		//接收人手机号码字符串；手机号码要去重，去空值
		$phoneArray = $_REQUEST['mobile'];
		$arrayTemp = array();
		foreach($phoneArray as $k=>$v){
			$arrayEach = explode(",", $v);
			$arrayTemp =array_merge($arrayTemp,$arrayEach);
		}
		$cutSame  = array_unique($arrayTemp);//去除重复值
		$cutEmpty = array_filter($cutSame);	//去除空值
		$phoneString = implode(",", $cutEmpty);	//转成“，”分隔字符串
		$userStr = implode(",",$_REQUEST['recipient']);
		//准备数据，在insert中去保存到数据库
		$_POST['flag'] 	= $_REQUEST['commit']; 	//是否直接发送了
		$_POST['content'] = $_REQUEST['content']; //短信息内容
		$_POST['phone'] = $phoneString; //接收人手机号码
		$_POST['userid'] = $userStr;
		//调用短信接口发送短信息
		//dump($cutEmpty);
		//dump($_POST['content'] );
		//die;
		if($_REQUEST['commit'] == 1){
			$mailContent = $_POST['content'];
			$smsResult = $this->SendTelmMsg($mailContent,$cutEmpty);
		}
		
	}
	/**
	 * @Title: insert
	 * @Description: todo(保存发送记录)
	* @author laicaixia
	* @date 2013-5-31 下午4:51:56
	* @throws
	*/
	public function insert() {
		//B('FilterString');
		$name=$this->getActionName();
		$model = D ($name);
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		//保存当前数据对象
		$list=$model->add ();
		if ($list!==false) {
			$mrdmodel = D('MisRuntimeData');
			$mrdmodel->setRuntimeCache($_POST,$name,'add');
			$module2=A($name);
			if (method_exists($module2,"_after_insert")) {
				call_user_func(array(&$module2,"_after_insert"),$list);
			}
			$this->success ( L('_SUCCESS_') ,'',$list);
			exit;
		} else {
			$this->error ( L('_ERROR_') );
		}
	}

	/**
	* @Title: update
	* @Description: todo(保存发送记录)
	* @author laicaixia
	* @date 2013-5-31 下午4:51:56
	* @throws
	*/
	public function _before_update() {
		//接收人手机号码字符串；手机号码要去重，去空值
		$phoneArray = $_REQUEST['mobile'];
		$arrayTemp = array();
		foreach($phoneArray as $k=>$v){
			$arrayEach = explode(",", $v);
			$arrayTemp =array_merge($arrayTemp,$arrayEach);
		}
		$cutSame  = array_unique($arrayTemp);//去除重复值
		$cutEmpty = array_filter($cutSame);	//去除空值
		$phoneString = implode(",", $cutEmpty);	//转成“，”分隔字符串
		$userStr = implode(",",$_REQUEST['recipient']);
		//准备数据，在insert中去保存到数据库
		$_POST['flag'] 	= $_REQUEST['commit']; 	//是否直接发送了
		$_POST['content'] = $_REQUEST['content']; 	//短信息内容
		$_POST['phone'] = $phoneString; //接收人手机号码
		$_POST['userid'] = $userStr;
		//调用短信接口发送短信息
		if($_REQUEST['commit'] == 1){
			$mailContent = $_POST['content'];
			$smsResult = $this->SendTelmMsg($mailContent,$cutEmpty);
		}
	}

	/**
	 * @Title: showMessage
	 * @Description: todo(查看发信息信内容)
	* @author laicaixia
	* @date 2013-5-31 下午4:51:56
	* @throws
	*/
	public function showMessage(){
		$map['id']=$_REQUEST['id'];
		$map['status']=array('gt',-1);
		
		$name=$this->getActionName();
		$model = D($name);
		$getInfo = $model->where($map)->find();
		//处理，接收人电话号前加人名
		$userArray = explode(',', $getInfo['userid']);
		foreach($userArray as $key=>$value){
			if(getFieldBy($value,'id','mobile','user')){
				$userArray[$key] = '<b style="color:#000;">['.getFieldBy($value,'id','name','user').']</b>'.getFieldBy($value,'id','mobile','user');
			}else{
				$userArray[$key] = '<b style="color:#000;">['.getFieldBy($value,'id','name','user').']</b>'."<span style='color:red;'>未设置手机号码，发送失败</span>";
			}
			
		}
		$getInfo['userid'] =implode("&nbsp;&nbsp;", $userArray);//重新压回到，列表记录中去
		$this->assign("vo",$getInfo);
		$this->display('showMessage');
	}
	
	/**
	 * @Title: searchuser
	 * @Description: todo(搜索用户)
	* @author laicaixia
	* @date 2013-5-31 下午4:51:56
	* @throws
	*/
	public function searchuser(){
		$mode1=D("User");
		$map = array();
		$map['status'] = array('GT',0);
		//是管理员的不显示出来
		$map['name'] = array('NEQ','管理员');
		$list=$mode1->field("id,name,dept_id,email,mobile")->where($map)->order('sort ASC')->select();						//user表中的人拿出来
		$returnarr = array();
		$dptmodel = D("MisSystemDepartment");//部门表
		$deptlist = $dptmodel->where("status=1")->order("id asc")->field('id,name,parentid')->select();						//部门数据拿出来
		foreach($deptlist as $k=>$v){
			$newv=array();
			$newv['id'] = -$v['id'];
			$newv['pId'] = -$v['parentid'] ? -$v['parentid']:0;
			$newv['title'] = $v['name']; //光标提示信息
			$newv['name'] = missubstr($v['name'],18,true); //结点名字，太多会被截取,针对于现在的ZTREE，宽度不能超过18个字符。
			if($v['parentid'] == 0){
				$newv['open'] = $v['open'] ? false : true;
			}
			$istrue = false;
			$userarr = array();								//名字id
			$usernamearr = array(); 						//名字
			$emailarr = array();							//邮箱
			// 构造用户
			foreach ($list as $k2 => $v2) {
				if($v2['dept_id'] == $v['id']){
					$newv2 = array();
					$userarr[] = $v2['id'];//将用户的名字和id分别存在数组中
					$usernamearr[] = $v2['name'];
					$emailarr[] = $v2['mobile'];
					
					$newv2['email'] = $v2['mobile'];  //不传邮件， 传电话号码过去
					$newv2['id'] = $v2['id'];
					$newv2['pId'] = -$v['id'];
					$newv2['title'] = $v2['name']; //光标提示信息
					$newv2['name'] = missubstr($v2['name'],20,true); //结点名字，太多会被截取,针对于现在的ZTREE，宽度不能超过18个字符。
					$newv2['icon'] = "__PUBLIC__/Images/icon/group.png";
					$newv2['open'] = true;
					array_push($returnarr,$newv2);
				}
			}
			$newv["userid"] = implode(",",$userarr);
			$newv["email"] = implode(",",$emailarr);
			$newv["username"] = implode(",",$usernamearr);//把取到名字和id转换成字符串，在传到前台。
			
			array_push($returnarr,$newv);
		}
		
		$this->assign('usertree',json_encode($returnarr));
		/*---------------------------------------------- split ---------------------------------------------- */
		//用户组的树
		$rolegroup = array();
	 	$rolegroupModel = D('Rolegroup');
		$rolegroupList = $rolegroupModel->where("status=1")->order("id asc")->field('id,name,pid')->select();//所有的组
		$rolegroup_userModel = M('rolegroup_user');
		$rolegroup_userList = $rolegroup_userModel->field("rolegroup_id,user_id")->order('rolegroup_id ASC')->select();
		foreach ($rolegroupList as $k => $v) {
			foreach ($rolegroup_userList as $k2 => $v2) {
				if($v["id"] == $v2["rolegroup_id"]){
					$rolegroupList[$k]["useridarr"][] = $v2["user_id"];
				}
			}
		}
		foreach($rolegroupList as $ke=>$va){
			$newRole=array();
			$newRole['id'] = -$va['id'];
			$newRole['pId'] = 0;
			$newRole['title'] = $va['name']; //光标提示信息
			$newRole['name'] = missubstr($va['name'],18,true); //结点名字，太多会被截取,针对于现在的ZTREE，宽度不能超过18个字符。
			$newRole['open'] = false;
			$istrue = false;
			$userarr = array();
			$usernamearr = array();
			$emailarr = array();
			foreach ($list as $k2 => $v2) {
				if(in_array($v2['id'],$va["useridarr"])){
					$istrue = true;
					$newv2 = array();
					$userarr[] = $v2['id'];
					$usernamearr[] = $v2['name'];
					$emailarr[] = $v2['mobile'];
					$newv2['email'] = $v2['mobile'];
					$newv2['id'] = $v2['id'];
					$newv2['pId'] = -$va['id'];
					$newv2['title'] = $v2['name']; //光标提示信息
					$newv2['name'] = missubstr($v2['name'],18,true); //结点名字，太多会被截取,针对于现在的ZTREE，宽度不能超过18个字符。
					$newv2['icon'] = "__PUBLIC__/Images/icon/group.png";
					$newv2['open'] = false;
					array_push($rolegroup,$newv2);
				}
			}
			if($istrue){
				$newRole["userid"] = implode(",",$userarr);
				$newRole["email"] = implode(",",$emailarr);
				$newRole["username"] = implode(",",$usernamearr);
				array_push($rolegroup,$newRole);
			}
		}
		$this->assign('rolegrouptree',json_encode($rolegroup));
		//角色的树
		$ProcessRole = array();
		$ProcessRoleModel = D('ProcessRole');
		$ProcessRoleList = $ProcessRoleModel->where("status=1")->order("sort asc")->field('id,name,deptid,userid')->select();//所有的角色组
		
		foreach($ProcessRoleList as $ke=>$va){
			$newRole=array();
			$newRole['id'] = -$va['id'];
			$newRole['pId'] = 0;
			$newRole['title'] = $va['name']; //光标提示信息
			$newRole['name'] = missubstr($va['name'],18,true); //结点名字，太多会被截取,针对于现在的ZTREE，宽度不能超过18个字符。
			$newRole['open'] = false;
			$istrue = false;
			$deptid = explode(",",$va['deptid']);
			$userid = explode(",",$va['userid']);
			$userarr = array();
			$usernamearr = array();
			$emailarr = array();
			foreach ($list as $k2 => $v2) {
				$istrue2 = false;
				if(in_array($v2['id'],$userid)){
					$istrue2 = true;
				}
				if($istrue2){
					$istrue = true;
					$newv2 = array();
					$userarr[] = $v2['id'];
					$usernamearr[] = $v2['name'];
					$emailarr[] = $v2['mobile'];
					$newv2['email'] = $v2['mobile'];
					$newv2['id'] = $v2['id'];
					$newv2['pId'] = -$va['id'];
					$newv2['title'] = $v2['name']; //光标提示信息
					$newv2['name'] = missubstr($v2['name'],18,true); //结点名字，太多会被截取,针对于现在的ZTREE，宽度不能超过18个字符。
					$newv2['icon'] = "__PUBLIC__/Images/icon/group.png";
					$newv2['open'] = false;
					array_push($ProcessRole,$newv2);
				}
			}
			if($istrue){
				$newRole["userid"] = implode(",",$userarr);
				$newRole["email"] = implode(",",$emailarr);
				$newRole["username"] = implode(",",$usernamearr);
				array_push($ProcessRole,$newRole);
			}
		}
		$this->assign('ProcessRoletree',json_encode($ProcessRole));
	}
}

?>
