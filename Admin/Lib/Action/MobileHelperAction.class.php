<?php
/**
 * @Title: OAHelperAction
 * @Package OA助的请求数据
 * @Description: OA助手，请求类，取数据接口
 * @author eagle
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d˾
 * @copyright Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d˾
 * @date 2014-2-08
 * @version V1.0
 */
class MobileHelperAction extends MobileBaseAction {
	//保存类实例的静态成员变量
	//private $userid=NULL;//当前登录用户
	//private $account=NULL;
	//private $password=NULL;
	//保存类实例的静态成员变量
	private $userid=NULL;//当前登录用户ID
	private $username=NULL;//当前登录用户账号
	private $token=NULL;//当前令牌
	private $transaction_model=NULL;//事务模型
	private $notoken=array('register','login');
	
	public function __construct(){
		//适配转换
		if(isset($_REQUEST['username']))$_REQUEST['account']=$_REQUEST['account'];
		if(isset($_REQUEST['password']))$_REQUEST['pwd']=$_REQUEST['password'];
		//是否注册方法标示位
		$register=false;
		foreach($_REQUEST['_URL_'] as $key => $val ){
			if(in_array($val,$this->notoken)){
				$register=true;
			}
		}
		//直接跳出构造函数
		if($register){ return ;}
		//初步界定token是一个包含用户名，密码，用户ID的数组，经过base64加密后的字符串
		try{
			if($_REQUEST['token']){
				//对token做解析
				//1反向转换2base64解密3反序列化
				$token=unserialize(base64_decode(strrev($_REQUEST['token'])));
				//这里做了一下转换
				$_REQUEST['userid']=$token['userid'];
				$_REQUEST['account']=$token['account'];
				$_REQUEST["password"] = $_REQUEST['pwd']=$token['password'];
				//给当前变量赋值
				$this->userid = $token['userid'];
				$this->username = $token['username'];
				try{
					if((!empty($_REQUEST['account'])) && (!empty($_REQUEST['pwd']))){
						$userinfo = $this->checkLogin($_REQUEST['account'],$_REQUEST['pwd']);
						if(!$userinfo){
							$data=array();
							$code='1002';
							$msg='1002:账号或密码错误';
							return $this->getReturnData($data,'json',$code,$msg);
						}
					}else{
						throw new Exception('Division by zero.');
					}
				}catch(Exception $e){
					$data=array();
					$code='1003';
					$msg='1003:令牌错误';
					return $this->getReturnData($data,'json',$code,$msg);
				}
			}else{
				throw new Exception('Division by zero.');
			}
		}catch(Exception $e){
			$data=array();
			$code='1003';
			$msg='1003:令牌错误';
			return $this->getReturnData($data,'json',$code,$msg);
		}
	}
	/*
	 * 登录验证方法
	 * login页面使用
	 * 放弃以前通过网页版的登陆了方法
	 */
	public function login($returnType='json'){
		//获取账号
		$account = $_POST['account'];
		//获取登录密码
		$pwd = $_POST['pwd'];
		if(!empty($account) && !empty($pwd)){
			$userinfo = $this->checkLogin($account,md5($pwd));
			//登陆成功
			$data=array();
			if($userinfo){
				//首先赋值session账号内容
				$_SESSION [C ( 'USER_AUTH_KEY' )] = $userinfo['id'];
				$data['userid']=$userinfo['id'];
				$data['account']=$userinfo['account'];
				$data['password']=$userinfo['password'];
				$data['username'] = $userinfo['name'];
				$data['logintime']=time();
				//获取token
				$token=$this->setToken($data);
				$data['token']=$token;
				$code='1000';
				$msg='1000:登录成功';
			}else{
				$data=array();
				$code='1002';
				$msg='1002:密码错误';
			}
		}else{
			$data=array();
			$code='1003';
			$msg='1003:非法访问';
		}
		return $this->getReturnData($data,$returnType,$code,$msg);
	}
	/*
	 * 根据用户名和密码验证用户信息
	 * 结合login方法使用
	 */
	public function checkLogin($account,$pwd){
		//核验用户名与密码
		$map=array();
		$map['account']=$account;
		$map['password']=$pwd;
		$userD = D('User');
		$result=$userD->where($map)->find();
		if(!$result){
			$map=array();
			$map['name']=$account;
			$map['password']=$pwd;
			$result=$userD->where($map)->find();
		}
		return $result;
	}
	/*
	 * main页面使用
	 * 获取审批数量和邮件数量
	 */
	public function mainJson(){
		//获取审批数量
		$shenpicount = $this->getWillWorkCount();
		//获取邮件数量
		$mesgcount = $this->getMesgCount();
		//获取登录用户信息
		$arr = array('auditcount'=>count($shenpicount),'mesgcount'=>$mesgcount);
		return $this->getReturnData($arr,'json',1000,'获取成功');
		
	}
	
	/**
	 * @Title: getWillWorkCount
	 * @Description: todo(获取审批类型 数据以及类型审批的数量)
	 * @return Ambigous <array, unknown>  
	 * @author 黎明刚
	 * @date 2015年4月18日 下午3:52:09 
	 * @throws
	 */
	private function getWillWorkCount(){
		//查询手机端和pc端审核数据数量
		$MWMModel = D('MisWorkMonitoring');
		$map['dostatus'] = 0;
		$map['_string'] = 'FIND_IN_SET( '.$_REQUEST['userid'].', curAuditUser )';
		$volist = $MWMModel->where($map)->field('ismobile')->select();
		return $volist;
	}
	/**
	 * @Title: getMesgCount
	 * @Description: todo(获取信息总条数)
	 * @return Ambigous <array, unknown>
	 * @author liminggang
	 * @date 2015年4月21日 下午7:09:51
	 * @throws
	 */
	private function getMesgCount(){
		//新邮件数量
		$mesgcount = $this->getNewMsgList(1);
		//执行数量
		$zhixingcount = $this->getProjectExecting(1);
		//项目分配
		$fenpeicount = $this->getProjectAssign(1);
		$count = $fenpeicount+$zhixingcount+$mesgcount;
		return $count>99?'99+':$count;
	}
	/**
	 * @Title: getWillWorkCateGory
	 * @Description: todo(获取审批类型 数据以及 各类型审批的数量) 
	 * @return Ambigous <array, unknown>  
	 * @author 黎明 刚
	 * @date 2015年4月18日 下午3:52:09 
	 * @throws
	 */
	public function getWillWorkCateGory(){
		//查询手机端和pc端审核数据数量
		$volist = $this->getWillWorkCount();
		//手机端审批数量字段
		$ismobile = 0;
		//pc端审批数量字段
		$ispc = 0;
		foreach($volist as $key=>$val){
			if($val['ismobile']){
				++$ismobile;
			}else{
				++$ispc;
			}
		}
		$data = array(
			'ismobile'=>$ismobile,
			'ispc'=>$ispc,
		);
		return $this->getReturnData($data,"json",1000,"审批数据获取成功");
	}
	
	/**
	 * @Title: getWillWorksList
	 * @Description: todo(获取选中审批类型下面的审批数据)
	 * @author 黎明刚
	 * @date 2013-3-15 下午5:02:51
	 * @throws
	 */
	public function getWillWorksList($returnType='json'){
		/*
		 * 这里一共有2个参数
		 * 参数一 category 值分别为 0：PC端，1：手机端
		 * 参数二 dostatus 值分别为 0 待审批，1 全部，2 已审批 ，3已发待批 ，4已发办结
		 */
		//获取查询那种类型的数据。手机还是PC端    0 PC端，1手机端
		$category = $_REQUEST['category']?$_REQUEST['category']:0;
		//获取审批状态值，0 待审批，1 全部，2 已审批 ，3已发待批 ，4已发代办
		$dostatus = $_REQUEST['dostatus']?$_REQUEST['dostatus']:0;
		
		$MWMModel = D('MisWorkMonitoring');
		$user = M('user');
		//获取检索字段名称
		$searchval = $_REQUEST['searchval'];
		//定义一个分页条数
		$numPerPage=$_REQUEST['pagesize'];
		//获取分页条码数
		$pageNum = $_REQUEST['pagenum']?$_REQUEST['pagenum']:1;
		$firstNum = ($pageNum-1)*$numPerPage;
		$nextNum = $pageNum*$numPerPage;
		//判断dostatus类型集
		if($dostatus<=2){
			if($dostatus==0){//待我审批
				$map['dostatus'] = 0;	
			}
			if($dostatus == 2){//我已审批
				$map['dostatus'] = 1;
			}
			if($searchval){
				//检索条件
				$map['_string'] = "FIND_IN_SET(  '.$this->userid.', curAuditUser ) and (relationname like '%".$searchval."%' or orderno like '%".$searchval."%')";
			}else{
				$map['_string'] = 'FIND_IN_SET(  '.$this->userid.', curAuditUser )';
			}
		}
		if($dostatus > 2){
			if($dostatus == 3){//已发待批
				$map['dostatus'] = 0;
				$map['createid'] = $_SESSION[C('USER_AUTH_KEY')];
			}
			if($dostatus == 4){//已发办结
				$map['auditState'] = 3;
				$map['createid'] = $_SESSION[C('USER_AUTH_KEY')];
			}	
			if($searchval){
			//检索条件
			$map['_string'] = "(relationname like '%".$searchval."%' or orderno like '%".$searchval."%')";
			}
		}
		$map['ismobile'] = $category;
		$result = $MWMModel->where($map)->field('orderno,tablename,tableid,dostatus,ismobile,projectid,createid,createtime')->limit($firstNum . ',' . $nextNum)->order("createtime desc")->select();
		//echo $MWModel->getlastsql();
		if($result){
			//存在审批任务，查询项目名字
			$mis_auto_kimpuDao = M("mis_auto_kimpu");
			$projectlist = $mis_auto_kimpuDao->getField("id,name");
		}
		$returnData=array();
        foreach($result as $key=>$val){
        	//标记。
        	$returnData[$key]['category']   = $category;
        	$returnData[$key]['newdostatus']   = $dostatus;
        	$returnData[$key]['ddostatus']   = $val['dostatus'];
        	//获取待办事务名称
        	$returnData[$key]['title']     =getFieldBy($val['tablename'], 'name', 'title', 'node'); 
        	//获取待办事务名称
        	$returnData[$key]['orderno']   =$val['orderno'];
        	//获取对应表名
        	$returnData[$key]['tablename'] =$val['tablename'];  
        	//获取对应表ID
        	$returnData[$key]['tableid'] =$val['tableid'];
        	
        	$userinfo=$user->where("id =".$val['createid'])->field('name')->find();
        	//获取制单人名
        	$returnData[$key]['username'] = $userinfo['name'];
        	//获取制单时间
        	$returnData[$key]['createtime'] =transTime($val['createtime'],'Y-m-d H:i:s');
        	//获取当前单据状态
        	$returnData[$key]['dostatus'] =$val['dostatus']?"已审核":"未审核";
        	//获取项目名称
        	if($val['projectid']){
        		$returnData[$key]['projectname'] =$projectlist[$val['projectid']]?$projectlist[$val['projectid']]:"";
        	}else{
        		$returnData[$key]['projectname'] ="";
        	}
        }
		if($returnData){
			$data['list'] = $returnData;
			//返回分页配置
			if($pageNum){
				//获取总条数
				$allcount = count($result);
				$data['curpagenum']['curpagenum'] = $pageNum; //当前页面
				$data['pagenum']['pagenum'] = ceil($allcount/$numPerPage); // 进一法取整 总页面
			}
		}else{
			$data['list'] = '';
		}
        return $this->getReturnData($data,$returnType,1000,"审批数据获取成功");
	}
	
	/**
	 * @Title: getNewMsgList
	 * @Description: todo(获取新邮件数据信息) 
	 * @param number $choose 参数为真时，获取邮件条数，参数为假时获取邮件总条数数组
	 * @return number|Ambigous <array, unknown>  
	 * @author 黎明刚
	 * @date 2015年4月21日 下午4:58:35 
	 * @throws
	 */
	public function getNewMsgList($choose=0){
		if($choose == 1){
			$returnData = D("MisMessageInbox")->getMessages(0,0,$_REQUEST['userid']);
			return count($returnData);
		}else{
			//定义一个分页条数
			$numPerPage=$_REQUEST['pagesize'];
			//获取分页条码数
			$pageNum = $_REQUEST['pagenum']?$_REQUEST['pagenum']:1;
			
			$firstNum = ($pageNum-1)*$numPerPage;
			$nextNum = $pageNum*$numPerPage;
			//获取检索字段名称
			$searchval = $_REQUEST['searchval'];
			if($searchval){
				//检索条件
				$where['mis_message.title'] = array('like','%'.$searchval.'%');
				$where['_logic'] = 'or';
				$map['_complex'] = $where;
			}
			$returnData = D("MisMessageInbox")->getMessages($pageNum,0,$_REQUEST['userid'],$map);
			
			//查询系统用户信息，获取发件人
			$userModel = M("user");
			$userlist = $userModel->where("status = 1")->getField("id,name");
			//声明相关附件表
			$modelMAR = M('MisAttachedRecord');
			//现在重新拼装带URL的返回值数据
			foreach($returnData as $key => $val){
				if($val['createid'] == 0){
					$returnData[$key]['sendname'] = "系统";
				}else{
					if(in_array($val['createid'], array_keys($userlist))){
						$returnData[$key]['sendname'] = $userlist[$val['createid']];
					}else{
						$returnData[$key]['sendname'] ="未知";
					}
				}
				$messageType = "inboxself";
				if($val['messageType'] == 1){
					$messageType = "inboxsystem";
				}
				//$returnData[$key]['urldata']="MisMessage,index,id,".$returnData[$key]['id'].",messageType,".$messageType.";MisMessage;MisMessage";
				
				//处理时间戳
				if($val['createtime']){
					$returnData[$key]['createtime'] = date("Y年m月d日 H:i:s",$val['createtime']) ;
				}
				//处理logo
				if($returnData[$key]['sendname']){
					$returnData[$key]['logo'] = substr($returnData[$key]['sendname'],-3); 
				}
			}
			
			if($returnData){
				$data['list'] = $returnData;
				if($pageNum){
					$allcount = $this->getProjectExecting(1); //获取总条数
					$data['curpagenum']['curpagenum'] = $pageNum; //当前页面
					$data['pagenum']['pagenum'] = ceil($allcount/$numPerPage); // 进一法取整 总页面
				}
			}else{
				$data['list'] = -1;
			}
			return $this->getReturnData($data,'json',1000,'邮件获取成功');
		}
	}

	/**
	 * @Title: getProjectExecting
	 * @Description: todo(项目执行数据获取) 
	 * @param number $choose  
	 * @author liminggang 
	 * @date 2015年4月21日 下午7:21:58 
	 * @throws
	 */
	public function getProjectExecting($choose=0,$newuserid){
		//如果有传入ID则修改成传入的ID人
		if($newuserid){
			$_REQUEST['userid'] = $newuserid;
		}
		if($_REQUEST['userid']){
			//项目执行，（只是执行人）
			$userid = $_REQUEST['userid'];
			//获取有权限查看的角色
			$misProjectFlowResource = D('MisProjectFlowResource');
			$plist = $misProjectFlowResource->getMyProjectIdList($userid);
			if($plist){
				$map['id'] = array(" in ",array_keys($plist));
			}else{
				$map['id'] = 0;
			}
			//实例化项目数据模型
			$model = D("MisSalesMyProject");
			
			if($choose == 1){
				//查询项目执行的条数
				$count = $model->where($map)->count();
				return $count;
			}else if($choose == 2){
				//单独获取用户最新执行的四条项目
				//进行中的项目数据
				$map['type']=1;
				$list = $model->where($map)->limit(4)->order("createtime desc")->select();
				return $list;
			}else{
				//获取检索字段名称
				$searchval = $_REQUEST['searchval'];
				//定义一个分页条数
				$numPerPage=$_REQUEST['pagesize'];
				//获取分页条码数
				$pageNum = $_REQUEST['pagenum']?$_REQUEST['pagenum']:1;
				
				$firstNum = ($pageNum-1)*$numPerPage;
				$nextNum = $pageNum*$numPerPage;
				//进行中的项目数据
				$map['type']=1;
				//增添检索条件
				if($searchval){
					$where['name']  = array('like','%'.$searchval.'%');
					$where['orderno']  = array('like','%'.$searchval.'%');
					$where['customername'] = array('like','%'.$searchval.'%');
					$where['_logic'] = 'or';
					$map['_complex'] = $where;
				}
				$list = $model->where($map)->limit($firstNum . ',' . $nextNum)->order("createtime desc")->select();
				if($list){
					$data['list'] = $list;
					//返回分页配置
					if($pageNum){
						$allcount = $this->getProjectExecting(1); //获取总条数
						$data['curpagenum']['curpagenum'] = $pageNum; //当前页面
						$data['pagenum']['pagenum'] = ceil($allcount/$numPerPage); // 进一法取整 总页面
					}
				}else{
					$data['list'] = -1;
				}
				
				return $this->getReturnData($data,'json',1000,"获取数据成功");
			}
		}else{
			return $this->getReturnData(array(),'json',0,"请先登录");
		}
	}
/**
	 * @Title: getProjectAssign
	 * @Description: todo(项目任务分派条数) 
	 * @param number $choose  
	 * @author liminggang
	 * @date 2015年4月22日 下午2:15:09 
	 * @throws
	 */
	public function getProjectAssign($choose=0){
		if($_REQUEST['userid']){
			if($choose == 1){
				//查询项目执行的条数
				$MisWorkExecutingModel = D("MisWorkExecuting");
				$count = $MisWorkExecutingModel->getWorkExecutingNum(2);
				return $count;
			}else{
				//定义一个分页条数
				$numPerPage=$_REQUEST['pagesize'];
				//获取分页条码数
				$pageNum = $_REQUEST['pagenum']?$_REQUEST['pagenum']:1;
				$firstNum = ($pageNum-1)*$numPerPage;
				$nextNum = $pageNum*$numPerPage;
		
				//查询项目执行的具体数据信息
				$MisWorkExecutingModel = D("MisWorkExecuting");
				$MisWorkExecutingModel->_filter("MisSalesProjectAllocation",$map);
				//获取检索字段名称
				$searchval = $_REQUEST['searchval'];
				//检索条件
				if($searchval){
					$where['xiangmumingchen']  = array('like','%'.$searchval.'%');
					$where['xiangmubianma']  = array('like','%'.$searchval.'%');
					$where['kehumingchen']  = array('like','%'.$searchval.'%');
					$where['_logic'] = 'or';
					$map['_complex'] = $where;
				}
				//实例化项目数据模型
				$model = D("MisAutoQzu");
				$list = $model->where($map)->limit($firstNum . ',' . $nextNum)->order("createtime desc")->select();
				//重构数组
				foreach ($list as $key=>$val){
					$list[$key]['id'] =$list[$key]['projectid'];
					$list[$key]['orderno'] =$list[$key]['xiangmubianma'];
				}
				if($list){
					$data['list'] = $list;
				}else{	
					$data['list'] = -1;
				}
				//返回分页配置
				if($pageNum){
					$allcount = $this->getProjectExecting(1); //获取总条数
					$data['curpagenum']['curpagenum'] = $pageNum; //当前页面
					$data['pagenum']['pagenum'] = ceil($allcount/$numPerPage); // 进一法取整 总页面
				}
				return $this->getReturnData($data,'json',1000,"获取数据成功");
			}
		}else{
			return $this->getReturnData(array(),'json',0,"请先登录");
		}
	}
	/**
	 * @Title: getMsgType
	 * @Description: todo(获取信息类型) 
	 * @return Ambigous <array, unknown>  
	 * @author liminggang
	 * @date 2015年4月21日 下午7:09:51 
	 * @throws
	 */
	public function getMsgType(){
		$data['list'] = array(
				'0'=>array('name'=>'团队状态改变','value'=>1,'func'=>'','count'=>0),
				'1'=>array('name'=>'新邮件','value'=>2,'func'=>'getNewMsgList','count'=>$this->getNewMsgList(1)),
				'2'=>array('name'=>'执行','value'=>3,'func'=>'getProjectExecting','count'=>$this->getProjectExecting(1)),
				'3'=>array('name'=>'项目分配','value'=>5,'func'=>'getProjectAssign','count'=>$this->getProjectAssign(1))
		);
		return $this->getReturnData($data,'json',1000,"数据获取成功");
	}

	
	/**
	 * @Title: getMessageDetail
	 * @Description: todo(邮件信息读取，REQUEST传入mis_message_user的id)
	 * @param string $returnType  返回值类型，默认为json
	 * @author yangxi
	 * @date 2014-4-3 
	 * @throws
	 */
	public function getMessageDetail($returnType='json'){
		//对当前用户查看记录做标记
		$MMUModel=M("mis_message_user");
		$map['id']=$_REQUEST['id'];//mis_message_user的ID
		//改变状态为已读
		$readStatus=$MMUModel->where($map)->setField("readedStatus",1);
		//查看邮件详细信息
		$returnData =D("MisMessageInbox")->getMessagesDetail($_REQUEST['id']);
		if($returnData){
			//查询系统用户信息，获取发件人
			$userModel = M("user");
			$userlist = $userModel->where("status = 1")->getField("id,name");
			//现在重新拼装带URL的返回值数据
			if($returnData['createid'] == 0){
				$returnData['sendname'] = "系统";
			}else{
				if(in_array($returnData['createid'], array_keys($userlist))){
					$returnData['sendname'] = $userlist[$returnData['createid']];
				}else{
					$returnData['sendname'] ="未知";
				}
			}
		}
		
		if($returnData){
			$data['list'] = $returnData;
		}else{
			$data['list'] = -1;
		}
		//返回了mis_message_user表的id(邮件指向)，createid发件人,createtime发件时间，messageType(邮件类型),mis_message表的title（主题）
		return $this->getReturnData($data,$returnType,1000,"数据获取成功");		
	}
	
	/**
	 * @Title: getProjectStage
	 * @Description: todo(获取当前选中项目的阶段和节点信息)   
	 * @author liminggang
	 * @date 2015年4月23日 下午3:27:37 
	 * @throws
	 */
	public function getProjectStage(){
		//获取项目ID
		$projectid = $_REQUEST['projectid'];
		//查询业务阶段和业务节点
		if($projectid){
			$mis_project_flow_typeDao = M("mis_project_flow_type");
			$where = array();
			$where['projectid'] = $projectid;
			$where['outlinelevel'] = 2;
			$where['isshow'] = 0;  // 非动态阶段
			$jieduanlist = $mis_project_flow_typeDao->where($where)->field("id,name,complete")->order("sort asc")->select();
			$mis_project_flow_formDao = M("mis_project_flow_form");
			//查询业务节点
			$where = array();
			$where['projectid'] = $projectid;
			$where['outlinelevel'] = 3;
			$jiedianlist = $mis_project_flow_formDao->where($where)->field("id,name,orderno,category,projectid")->select();
			foreach($jieduanlist as $key=>$val){
				$jieduanvo = array();
				foreach ($jiedianlist as $k=>$v){
					if($val['id'] == $v['category']){
						$jieduanvo[] = $v;
					}
				}
				$jieduanlist[$key]['jiedian'][] = $jieduanvo;
			}
			
			if($jieduanlist){
				$data['list'] = $jieduanlist;
			}else{
				$data['list'] = -1;
			}
			return $this->getReturnData($data,'json',1000,"获取数据成功");
		}else{
			return $this->getReturnData($data,'json',0,"缺少固定参数，请检查");
		}
	}
	/**
	 * @Title: getProjectInfo
	 * @Description: todo(根据项目ID获取项目基础信息) 
	 * @return Ambigous <array, unknown>  
	 * @author liminggang
	 * @date 2015年4月23日 下午4:45:35 
	 * @throws
	 */
	public function getProjectInfo(){
		//获取项目ID
		$projectid = $_REQUEST['projectid'];
		if($projectid){
			$tableid = getFieldBy($projectid,'projectid','id','MisAutoPvb');
			//获取配置文件以及数据
			$model = D('SystemConfigDetail');
			//从动态配置获取到数据信息
			$result=$model->GetDynamicconfData("MisAutoPvb",$projectid,"message","projectid");
			
			//message信息拼装
			$message=array();
			foreach($result as $key => $val){
				//初始化一个字符串容器
				foreach($val as $subkey => $subval){
					$message[$key] = array(
							'name'=>$subkey,
							'value'=>$subval,
					);
				}
			}
			/*
			 * 封装内嵌表数据，进行页面url标记
			 */
			$neiqianArr = $this->getNeiQian("MisAutoPvb", $tableid);
			
		if($neiqianArr){
				$message = array_merge($message,$neiqianArr);
			}
			//封装套表数据，进行页面url标记
			$taobiaoArr = $this->getTaoBiao("MisAutoPvb", $tableid);
			if($taobiaoArr){
				$message = array_merge($message,$taobiaoArr);
			}
			$list = array('title'=>getFieldBy("MisAutoPvb", "name", "title", "node"),'data'=>$message);
			//对list重构数据顺序
			$this->getProjectListAss(&$list);
			if($list){
				$data['list'] = $list;
			}else{
				$data['list'] = -1;
			}
			return $this->getReturnData($data,'json',1000,"获取数据成功");
			
		}else{
			return $this->getReturnData($data,'json',0,"缺少固定参数，请检查");
		}
	}
	/**
	 * 验证当前模型是否存在动态建模？
	 */
	private function getNeiQian($name,$tableid){
		$neiqianArr = array();
		// 验证当前模型是否存在动态建模。
		$where = array ();
		$where ['actionname'] = $name;
		// 实例化mis_dynamic_form_manage
		$mis_dynamic_form_manageDao = M ( "mis_dynamic_form_manage" );
		$managevo = $mis_dynamic_form_manageDao->where ( $where )->find ();
		if ($managevo) {
		// --------------验证一、是否存在内嵌表格-----------------//
		$properywhere = array ();
		$properywhere ['formid'] = $managevo ['id'];
		$properywhere ['category'] = "datatable";
		$properywhere ['status'] = 1;
		// 实例化动态建模数据匹配表
		$mis_dynamic_form_properyModel = M ( "mis_dynamic_form_propery" );
		$neiqianlist = $mis_dynamic_form_properyModel->where ( $properywhere )->field ( "fieldname,title,datatablemodel,datatablename" )->select ();
			if ($neiqianlist) {
				foreach($neiqianlist as $key=>$val){
				//存在内嵌表，则给内嵌表标记成字段，然后加入url
					$newarr = array(
					'type'=>3,//类型为内嵌表
					'name'=>'内嵌表',
					'value'=>$val['title'],
					'func'=>'getNeiQainView',
							'zhumodelname'=>$name,
							'neiqianmodelname'=>$val['datatablemodel'],
							'masid'=>$tableid,
					);
					$neiqianArr[$val['fieldname']] = $newarr;
				}
			}
		}
		return $neiqianArr;
	}

	/*
	 * 获取套表绑定关系
	 */
	private function getTaoBiao($tablename,$tableid){
		$taobiaoArr = array();
		$mis_auto_bindDao = M("mis_auto_bind");
		//验证三、是否存在套表
		$where = array();
		$where['bindaname'] = $tablename;
		$where['status'] = 1;
		$where['typeid'] = 2;//套表类型
		// 查询符合条件的表单
		$bingdsetList=$mis_auto_bindDao->where($where)->select();
		if(count($bingdsetList)>0){
			//套表数据查询条件
			$map = array();
			$map['status'] = 1;
			$map['bindid'] = getFieldBy($tableid,'id','orderno',$tablename);
				
			foreach($bingdsetList as $bkey=>$bval){
				//实例化模型对象
				$inbindaModel = D($bval['inbindaname']);
				//显示节点名称
				$showname = $bval['inbindtitle'];
				//第一步、判断是表单还是列表  bindtype = 0  表单
				if($bval['bindtype']==0){
					//以主表的数据orderno查询组合的表单数据
					$taobiaotableid = $inbindaModel->where($map)->getField("id");
					//存在内嵌表，则给内嵌表标记成字段，然后加入url
					$newarr = array(
							'type'=>1,//类型为套表单数据
							'name'=>'套表',
							'value'=>$showname,
							'func'=>'getWillWorksDetail',
							'tablename'=>$bval['inbindaname'],
							'tableid'=>$taobiaotableid,
					);
					$taobiaoArr[$bval['inbindaname']] = $newarr;
				}else{
					//存在内嵌表，则给内嵌表标记成字段，然后加入url
					$newarr = array(
							'type'=>2,//类型为套表多数据
							'name'=>'套表',
							'value'=>$showname,
							'func'=>'getTaobiaoView',
							'tablename'=>$val['datatablemodel'],
							'bindid'=>$map['bindid'],
					);
					$taobiaoArr[$bval['inbindaname']] = $newarr;
				}
			}
		}
		return $taobiaoArr;
	}
	/**
	 * @Title: getProjectFormInfo
	 * @Description: todo(根据任务ID和模型名称获取当前任务对应的表单信息)
	 * @author 谢友志
	 * @date 2015年4月23日 下午6:24:30
	 * @throws
	 */
	public function getProjectFormInfo(){
		//获取任务ID值
		$projectworkid = $_REQUEST['projectworkid'];
		//获取模型名称
		$modelName = $_REQUEST['modelname'];
		if(!$projectworkid){
			return $this->getReturnData($data,'json',0,"缺少固定参数，请检查");
		}
		if(!$modelName){
			return $this->getReturnData($data,'json',0,"缺少固定参数，请检查");
		}
		$tableid = getFieldBy($projectworkid,'projectworkid','id',$modelName);
		//获取配置文件以及数据
		$model = D('SystemConfigDetail');
		//从动态配置获取到数据信息
		$result=$model->GetDynamicconfData($modelName,$projectworkid,"message","projectworkid");
		//message信息拼装
		$message=array();
		foreach($result as $key => $val){
			//初始化一个字符串容器
			foreach($val as $subkey => $subval){
				$message[$key] = array(
						'name'=>$subkey,
						'value'=>$subval,
						'field'=>$key,
				);
			}
		}
		/*
		 * 封装内嵌表数据，进行页面url标记
			*/
		$neiqianArr = $this->getNeiQian($modelName, $tableid);
		if($neiqianArr){
			$message = array_merge($message,$neiqianArr);
		}
		//封装套表数据，进行页面url标记
		$taobiaoArr = $this->getTaoBiao($modelName, $tableid);
		if($taobiaoArr){
			$message = array_merge($message,$taobiaoArr);
		}
		
		//查询有关附件
		$attarry=$this->getAttachedRecordList($modelName,$tableid,true,true,$modelName,0,false,false);
		$uploadPath="http://192.168.1.10/systemui/Public/Uploads/";
		$attarryInfo=array();
		
		foreach ($attarry as $ak=>$av){
			$att = array();
			foreach($av as $aak=>$aav){
				$att[] = array('filename'=>$aav['filename'],'attachurl'=>$uploadPath.$aav['attached']);
			}
			$attarryInfo[$ak]=$att;
		}
		
		//处理数据格式
		$arr = array();
		foreach ($message as $kk=>$vv){
			//过滤原始值
			if(strpos($kk,'_souce')){
				continue;
			}
			//不显示ID
			if($kk=='id'){
				$vv['field']="is__souce";
				$arr[] = $vv;
				continue;
			}
			//过滤附件的值 
			if(strpos($vv['name'],'附件')){
				$vv['value'] ='';
			}
			//如果附件存在则添加值
			if($attarryInfo){
				//存在附件信息，循环绑定到数据列表
				foreach($attarryInfo as $atkey=>$atval){
					if($kk == $atkey){
						$vv['value'] = $atval;
						$vv['field'] = "is_attached_field";
					}
				}
			}
			$arr[] = $vv;
		}
		
		$list = array('title'=>getFieldBy($modelName, "name", "title", "node"),'data'=>$arr);
		if($list){
			$data['list'] = $list;
		}else{
			$data['list'] = -1;
		}
		return $this->getReturnData($data,'json',1000,"获取数据成功");
	}
	/**
	 * @Title: getProjectWorkList
	 * @Description: todo(获取项目某节点下面的任务信息)
	 * @author 谢友志
	 * @date 2015年4月23日 下午5:44:34
	 * @throws
	 */
	public function getProjectWorkList(){
		//获取节点ID值
		$jiedianid = $_REQUEST['jiedianid'];
		//获取项目ID
		$projectid = $_REQUEST['projectid'];
		if(!$projectid){
			return $this->getReturnData($data,'json',0,"缺少固定参数，请检查");
		}
		if(!$jiedianid){
			return $this->getReturnData($data,'json',0,"缺少固定参数，请检查");
		}
		//根据项目和节点ID查询任务数组
		$mis_project_flow_formDao = M("mis_project_flow_form");
		$where = array();
		$where['projectid'] = $projectid;
		$where['outlinelevel'] = 4;
		$where['parentid'] = $jiedianid;
		$where['formtype'] = 2;
		$worklist = $mis_project_flow_formDao->where($where)->field("id,name,formobj,projectid,parentid")->select();
		//封装专门的返回数组
		if($worklist){
				$data['list'] = $worklist;
			}else{
				$data['list'] = -1;
		}
		return $this->getReturnData($data,'json',1000,"获取数据成功");
	}
	/**
	 * @Title: getProjectListAss
	 * @Description: todo(手机端项目查看排序修改)
	 * @author liuzhihong
	 * @date 2015年4月23日 下午5:44:34
	 * @throws
	 */
	public function getProjectListAss($list,$attarryInfo){
		if($list){
			$listdata = array();//用来存放替换后的list['data']
			$listdatasouce = array();//用来存放后缀名为_souce的
			//替换list['data']数组下标
			foreach ($list['data'] as $key=>$var){
				//查找到后缀名为_souce的 让他下标不变化
				if(strpos($key,'_souce')){
					$listdatasouce[$key] = $var;
				}else{
					$listdata[] =  $var;
				}
			}
			$list['data'] = $listdata;
			$list['data_souce'] = $listdatasouce;
		}
	}

	/**
	 * @Title: getProjectType
	 * @Description: todo(获取项目类型以及每个类型的总条数)
	 * @return Ambigous <array, unknown>
	 * @author liminggang
	 * @date 2015年4月23日 上午10:18:39
	 * @throws
	 */
	public function getProjectType(){
		//获取项目类型数据
		$mis_system_flow_typeDao = M("mis_system_flow_type");
		$where = array();
		$where['outlinelevel'] = 1;
		$where['status'] = 1;
		$typelist = $mis_system_flow_typeDao->where($where)->getField("orderno,name");
		//获取我所能看见的项目
		//查询项目执行的具体数据信息
		$MisWorkExecutingModel = D("MisWorkExecuting");
		$map = $MisWorkExecutingModel->_filter("MisSalesMyProject",$map);
		//进行中的项目数据
		$map['type']=1;
		//实例化项目数据模型
		$model = D("MisSalesMyProject");
		$list = $model->where($map)->field("id,name,typeid")->select();
		//定义存储数组
		$datas = array();
		foreach($typelist as $key=>$val){
			$count = 0;
			foreach($list as $kk=>$vv){
				if($key == $vv['typeid']){
					++$count;
				}
			}
			$datas[] = array(
					'orderno'=>$key,
					'name'=>$val,
					'count'=>$count,
			);
		}
		//封装专门的返回数组
		if($datas){
			$data['list'] = $datas;
		}else{
			$data['list'] = -1;
		}
		return $this->getReturnData($data,'json',1000,"获取数据成功");
	}
	/**
	 * @Title: getDoingProject
	 * @Description: todo(获取项目查看项目列表数据)
	 * @return Ambigous <array, unknown>
	 * @author liminggang
	 * @date 2015年4月23日 下午2:44:08
	 * @throws
	 */
	public function getDoingProject(){
		$typeid = $_REQUEST['typeid'];
		if($typeid){
			//定义一个分页条数
			$numPerPage=$_REQUEST['pagesize'];
			//获取分页条码数
			$pageNum = $_REQUEST['pagenum']?$_REQUEST['pagenum']:1;
				
			$firstNum = ($pageNum-1)*$numPerPage;
			$nextNum = $pageNum*$numPerPage;
				
			//查询项目执行的具体数据信息
			$MisWorkExecutingModel = D("MisWorkExecuting");
			$map = $MisWorkExecutingModel->_filter("MisSalesMyProject",$map);
			//进行中的项目数据
			$map['type']=1;
			$map['typeid'] = array('eq',$typeid);
			//获取检索字段名称
			$searchval = $_REQUEST['searchval'];
			//增添检索条件
			if($searchval){
				$where['name']  = array('like','%'.$searchval.'%');
				$where['orderno']  = array('like','%'.$searchval.'%');
				$where['customername'] = array('like','%'.$searchval.'%');
				$where['_logic'] = 'or';
				$map['_complex'] = $where;
			}
			//实例化项目数据模型
			$model = D("MisSalesMyProject");
			$list = $model->where($map)->field("id,orderno,name,typeid")->order("createtime desc")->limit($firstNum . ',' . $nextNum)->select();
			if($list){
				$data['list']= $list;
			}else{
				$data['list'] = -1;
			}
			//返回分页配置
			if($pageNum){
				//获取总条数
				$count = $model->where($map)->count();
				$allcount = $count;
				$data['curpagenum']['curpagenum'] = $pageNum; //当前页面
				$data['pagenum']['pagenum'] = ceil($allcount/$numPerPage); // 进一法取整 总页面
				
			}
			return $this->getReturnData($data,'json',1000,"获取数据成功");
				
		}else{
			return $this->getReturnData($data,'json',0,"请传入项目类型");
		}
	}
	//团队用户列表,类型 1、部门  2、项目 3、部门内人员列表
	public function getUsersList(){
		//获取该用户是否在组织架构里
		//D("mis_organizational_recode");
		$MisHrPersonnelUserDeptViewModel=D("MisHrPersonnelUserDeptView");
		//该架构里所有人员列表
		$type=$_REQUEST['type'];
		$UsersList = array();
		switch ($type){
			case '1'://部门（需传入部门id）
				//查询所有部门列表
				$deptlist=M("mis_system_department")->where("status=1")->select();
				//判断如果没有传入的部门 则查询自己所在的部门
				if($_REQUEST['deptid']){
					$deptid = $_REQUEST['deptid'];
				}else{
					$deptid = $this->getDeptidByUserid($_REQUEST['userid']);
				}
				//递归查询所有子级
				$deptlists =array_unique(array_filter (explode(",",$this->findAlldept($deptlist,$deptid))));
				$map=array();
				$map['deptid']=array("in",$deptlists);
				$map['status']=1;
				//获取检索字段名称
				$searchval = $_REQUEST['searchval'];
				//检索条件
				if($searchval){
					$where['mis_hr_personnel_person_info.name'] = array('like','%'.$searchval.'%');
					$where['mis_hr_personnel_person_info.phone'] = array('like','%'.$searchval.'%');
					$where['_logic'] = 'or';
					$map['_complex'] = $where;
				}
				$UsersList=$MisHrPersonnelUserDeptViewModel->where($map)->select();
				//->field("mis_hr_personnel_person_info.usersid,mis_hr_personnel_person_info.picture,mis_hr_personnel_person_info.sex,mis_hr_personnel_person_info.name,mis_hr_personnel_person_info.dutyname,mis_hr_personnel_person_info.phone,mis_hr_personnel_person_info.eventsid,mis_hr_personnel_person_info.worktype,mis_hr_personnel_person_info.complete")
				break;
			case '2'://项目（传入项目id）
				$projectUserList= M("mis_project_flow_resource")->where("status=1 and projectid={$_REQUEST['projectid']}")->group("alloterid,executorid")->getField("alloterid,executorid");
				$arr=array();
				if($projectUserList){
					$arr[]=implode(',', array_filter(array_values($projectUserList)));
					$arr[]=implode(',', array_filter(array_keys($projectUserList)));
				}
				//查询项目角色
				$MisProjectFlowManagerDao=M("mis_project_flow_manager");
				$flowUserList=$MisProjectFlowManagerDao->where("projectid={$_REQUEST['projectid']}")->group("userid")->getField("id,userid");
				$arr[]=implode(',', array_values($flowUserList));
				$userid=implode(',', $arr);
				$map=array();
				$map['userid']=array("in",$userid);
				$UsersList=$MisHrPersonnelUserDeptViewModel->where($map)->select();
				break;
			case '3'://部门内人员列表（需传入部门id）
				$map=array();
				$map['deptid']=$_REQUEST['deptid'];
				$map['status']=1;
				$UsersList=$MisHrPersonnelUserDeptViewModel->where($map)->select();
				break;
		}
		//通过外出人员表封装工作状态
		foreach($UsersList as $key=>$val){
			$newtime = time();
			//获取需要查询的用户ID
			$map = array();
			$where['xingming'] = $val['usersid'];
			$where['_string'] = 'FIND_IN_SET( '.$val['usersid'].', suixingrenyuan )';
			$where['_logic'] = 'OR';
			$map['_complex'] = $where;
			$waichuModel = M('mis_auto_jevyr'); 
			$map['kaishishijian'] = array('gt',$newtime);
			$map['jieshushijian'] = array('lt',$newtime);
			$gongzuozhuangtai = $waichuModel->where($map)->order('kaishishijian desc')->find();
			logs($waichuModel->getlastsql(),'sql');
			if($gongzuozhuangtai){
				$UsersList[$key]['leibie'] = $gongzuozhuangtai['leibie'];
			}else{
				$UsersList[$key]['leibie'] = '';
			}
		}
		$data = array();
		if($UsersList){
			$data['list'] = $UsersList;
		}else{
			$data['list'] = -1;
		}
		return $this->getReturnData($data,'json',1000,"获取数据成功");
	}
	//通过userid获取所在部门编号
	public function getDeptidByUserid($userid){
		$UserDeptDutymModel = M('user_dept_duty');
		$map['userid'] = $userid;
		$list = $UserDeptDutymModel->where($map)->getField("userid,deptid");
		if($list){
			$deptid = $list[$userid];
			return $deptid;
		}else{
			return false;
		}
		
	}
	/**
	 *
	 * @Title: findAlldept
	 * @Description: todo(查找当前节点下的子节点)
	 * @param array $deptlist 指定模型的所有数据
	 * @param int $deptid   选中的节点
	 * @param unknown_type $hasparentsid  是否组合选中的节点
	 * @return string   返回选中节点在内的所有子节点
	 * @author liminggang
	 * @throws
	 */
	function findAlldept($deptlist,$deptid,$hasparentsid=true){
		if($deptid){
			$arr="";
			if( $hasparentsid ) $arr =",".$deptid;
			foreach ($deptlist as $k=>$v){
				if($v['parentid']==$deptid ){
					unset($deptlist[$k]);
					$arr.=",".$v['id'].$this->findAlldept($deptlist,$v['id']);
				}
			}
			return $arr;
		}
	}
	//获取公司列表
	public function getCompanyList(){
		//实例化公司表
		$companyList=M("mis_system_company")->select();
		//遍历取公司的部门信息
		foreach($companyList as $key=>$val){
			$dptlist = $this->getDepartmentList($val['id']);
			$companyList[$key]['dptlist'] = $dptlist;
		}
		if($companyList){
			$data['list'] = $companyList;
		}else{
			$data['list'] = -1;
		}
		return $this->getReturnData($data,'json',1000,"获取数据成功");
	}
	//获取部门列表（某公司下的）
	public function getDepartmentList($companyid){
		//实例化公司表 (需传入公司id)
		$map['iscompany'] = '0';
		$map['status'] = '1';
		$map['companyid'] = $companyid;
		$departmentList=M("mis_system_department")->where($map)->select();
		return $departmentList;
	}
	//用户基本信息
	public function getUserInfo(){
		//如果存在参数，则用参数；如果不存在则用当前的userid
		$userid=$_REQUEST['chakanuserid']?$_REQUEST['chakanuserid']:'';
		if($userid){
			(int)$userid;
			//实例化用户视图
			$misHrPersonnelPersonInfo=D("MisHrPersonnelUserDeptView");
			$misHrPersonnelPersonInfoVo=$misHrPersonnelPersonInfo->where('user.id='.$userid)->find();
// 			//查询当前人员状态
// 			$MisUserEventsModel=M("mis_user_events");
// 			$nowtime= strtotime(transTime(time(),'Y-m-d')." 00:00");
// 			$endtime= strtotime(transTime(time(),'Y-m-d')." 23:59");
// 			$MisUserEventsVo=$MisUserEventsModel->where("userid={$userid} and startdate between {$nowtime} and {$endtime}")->find();
// 			$misHrPersonnelPersonInfoVo['worktype']=$MisUserEventsVo['worktype'];
// 			//$_SESSION['user_dep_id']
// 			$deptid = $this->getDeptidByUserid($userid);
// 			if($misHrPersonnelPersonInfoVo){
// 				//获取查询的部门经理
// 				$manager = "部门经理";
// 				if(file_exists(DConfig_PATH."/System/list.inc.php")){
// 					require DConfig_PATH."/System/list.inc.php";
// 					$manager = $aryRule['部门经理'];
// 				}
// 				//指定部门角色为“部门经理”，据此查询角色id
// 				$roleid = M ( "rolegroup" )->where ( "name='{$manager}' and status=1 and catgory=5" )->getField("id");
// 				$manageruserid = M ( "mis_organizational_recode" )->where ( "rolegroup_id={$roleid} and deptid={$deptid} and status=1" )->getField("userid");
// 				if($manageruserid==$userid){
// 					$misHrPersonnelPersonInfoVo['ismanager']=1;
// 				}else{
// 					$misHrPersonnelPersonInfoVo['ismanager']=0;
// 				}
// 			}
			//获取4条用户项目执行数据
			$xiangmuzhixing = $this->getProjectExecting(2,$userid);//传入用户
			$misHrPersonnelPersonInfoVo['xiangmuzhixing'] = $xiangmuzhixing;
			//获取外出日期
			$misHrPersonnelPersonInfoVo['waichuriqi'] = $this->getChucaiData($userid,"arr");
			$data['list'] = $misHrPersonnelPersonInfoVo;
			return $this->getReturnData($data,'json',1000,"获取数据成功");
		}else{
			return $this->illegalParameter();
		}
	}
	/**
	 *
	 * @Title: getChucaiData
	 * @Description: todo(查询用户的外出日期和类型)
	 * @param int userid(用户的ID)
	 * @param string returnType(返回类型)
	 * @author liuzhihong
	 * @return array $specialDateArray (所选用户的外出日期和类型)
	 * @throws
	 */
	public function getChucaiData($userid,$returnType="json"){
		//获取userid 
		$userid = $userid?$userid:$this->userid;
 		if($userid){
 		//获取需要查询的用户ID
 		$map['xingming'] = $userid;
 		$map['_string'] = 'FIND_IN_SET( '.$userid.', suixingrenyuan )';
 		$map['_logic'] = 'OR';
 		$waichuModel = M('mis_auto_jevyr');
 		$timearray = $waichuModel->where($map)->field("id,kaishishijian,jieshushijian,leibie")->order('kaishishijian desc')->select();
 		$specialDateArray = array();
 		//把里面所有的外出记录遍历成日期
 		$index = 0;
	 		foreach ($timearray as $key => $val){
	 			//先计算出时间戳差
	 			$shijiancha = $val['jieshushijian']-$val['kaishishijian'];
	 			//如果时间戳差加上开始时间不超过当天的话就直接记录为当天请假;
	 			if(date("Y-m-d",$val['kaishishijian'])==date("Y-m-d",$val['kaishishijian']+$shijiancha)){
	 				$specialDateArray['date'][$index]['date'] = date("Y-m-d",$val['kaishishijian']);
	 				$specialDateArray['leibie'][$index][date("Y-m-d",$val['kaishishijian'])] = $val['leibie'];
	 				$specialDateArray['id'][$index]['id'] = $val['id'];
	 				$index++;
	 			}else{//如果不等于 则超出一天时间 则把在内的所有日期添加进去
	 				$xiangchatianshu = ceil($shijiancha/86400);//计算出 相差天数 用进一法 取整
	 				for($i=0;$i<$xiangchatianshu;$i++){
	 					$specialDateArray['date'][$index]['date'] = date("Y-m-d",$val['kaishishijian']+$i*86400);
	 					$specialDateArray['leibie'][$index][date("Y-m-d",$val['kaishishijian']+$i*86400)] = $val['leibie'];
	 					$specialDateArray['id'][$index]['id'] = $val['id'];
	 					$index++;
	 				}
	 			}
	 		}
	 		if($returnType=="arr"){
	 			return $this->getReturnData($specialDateArray,$returnType,1000,"获取数据成功");
	 		}else{
	 			$data["list"] = $specialDateArray;
	 			return $this->getReturnData($data,$returnType,1000,"获取数据成功");
	 		}
	 	}
	}
	/**
	 *
	 * @Title: getChucaiView
	 * @Description: todo(查询用户当天的外出详细列表)
	 * @param int userid(用户的ID)
	 * @param string date(选中日期)
	 * @author liuzhihong
	 * @return array $specialDateArray (所选用户的相关信息列表)
	 * @throws
	 */
	public function getChucaiView(){
		//获取userid
		$userid = $this->userid;
		//获取时间
		$newdate = date("Y-m-d",$_REQUEST['date']/1000);
		//获取user的所有出行情况
		$dateDay = $this->getChucaiData($userid,"arr");
     	$indexId = array(); 
		foreach ($dateDay["date"] as $key=>$val){
			if($val['date'] == $newdate){
				//获取所有出行ID
				$indexId[] = $dateDay['id'][$key]['id'];	
         	}
		}
		//去除重复ID
		$indexId = array_unique($indexId);
		$map['id']=array("in",$indexId);
		$waichuModel = M('mis_auto_jevyr');
		//查询到当天的出行信息
		$dateviewarray = $waichuModel->where($map)->field("id,xingming,shiyou,suixingrenyuan")->select();
		//获取用户姓名
		$userModel = M('user');
		//遍历查找ID所对应的ID 封装到$dateviewarray里面
		foreach ($dateviewarray as $key =>$val){
			if($val['suixingrenyuan']){
				$val['xingming'] .= ','.$val['suixingrenyuan'];
			}
			$map['id']=array("in",$val['xingming']);
			$userarray = $userModel->where($map)->field("name")->select();
			$username ='';
			$index = 0;
			//拼接字符串 当 大于3人时不再添加
			foreach ($userarray as $k =>$v){
				if($index<3){
					$username = $username?$username.'、'.$v['name']:$v['name'];
				}
				$index++;
			}
			if($index>3){
				$username .= "等".$index."人";
			}else if($index==3){
				$username .= "等";
			}
			$dateviewarray[$key]['name']=$username;
		}
		if($dateviewarray){
			$data['list'] = $dateviewarray;
		}else{
			$data['list'] = -1;
		}
		return $this->getReturnData($data,'json',1000,"获取数据成功");
	}
	/**
	 *
	 * @Title: getChucaiDetail
	 * @Description: todo(查询用户当天的外出详细详细)
	 * @param int userid(外出表的ID)
	 * @author liuzhihong
	 * @return array $specialDateArray (所选用户的外出日期和类型)
	 * @throws
	 */
	public function getChucaiDetail(){
		//实例化外出人员Model
		$waichuModel = M('mis_auto_jevyr');
		$userModel = M('user');
		$id = $_REQUEST['waichuid'];
		if(!$id){
			return $this->getReturnData($data,'json',0,"缺少固定参数，请检查");
		}
		$map['id']=$id;
		$list = $waichuModel->field("id,shiyou,kaishishijian,jieshushijian,leibie,beizhu,xingming,suixingrenyuan")->where($map)->find();
		//遍历查找ID所对应的ID 封装到$dateviewarray里面
		if($list['suixingrenyuan']){
			$list['xingming'] .= ','.$list['suixingrenyuan'];
		}
		$where['id']=array("in",$list['xingming']);
		$userarray = $userModel->where($where)->field("name")->select();
		//拼接name字符串
		$username ='';
		foreach ($userarray as $k =>$v){
			$username = $username?$username.'、'.$v['name']:$v['name'];
		}
		//日期转时间戳
		$list['kaishishijian'] = date("Y-m-d H:i:s",$list['kaishishijian']);
		$list['jieshushijian'] = date("Y-m-d H:i:s",$list['jieshushijian']);
		$list['name']=$username;
		$data['list'] = $list;
	return $this->getReturnData($data,'json',1000,"获取数据成功");
	}
	/**
	 * @Title: getTaobiaoView
	 * @Description: todo(获取套表字段详情数据)
	 * @return Ambigous <array, unknown>
	 * @author liminggang
	 * @date 2015-7-24 上午9:46:41
	 * @throws
	 */
	public function getTaobiaoView(){
		//获取模型名称
		$tablename = $_REQUEST['tablename'];
		//获取内嵌表关联的masid
		$bindid = $_REQUEST['bindid'];
		// 实例化内嵌表
		$datatablename = D ( $tablename );
		// 获取list.inc.php配置文件内容的模型
		$scdmodel = D ( 'SystemConfigDetail' );
		// 获取内嵌表的配置文件
		$neiqdetailList = $scdmodel->getDetail ( $tablename, false);
		$where = array ();
		$where['status'] = 1;
		$where ['bindid'] = $bindid;
		//内嵌表的数据
		$innerTabelObjdatatable3Data = $datatablename->where ( $where )->select ();
		//获取内嵌表数据内容
		$neiqiandata= $this->getDetaileZhList($neiqdetailList,$innerTabelObjdatatable3Data);
		//记录下标位置
		$xiabiao = array();
		$neiqdetailData =array();
		$k = 0;
		//修改下标
		foreach ($neiqdetailList as $key => $val){
			if($k<4){
				$xiabiao[] = $key;
				$neiqdetailData[] = $val;
			}else{
				break;
			}
			$k++;
		}
		//组合th和td
		$list = array('datelist'=>$neiqdetailData,'volist'=>$neiqiandata,'tablename'=>$tablename,'xiabiao'=>$xiabiao);
		$data['list'] = $list;
		return $this->getReturnData($data,'json',1000,"详情获取成功");
	}
	/**
	 * @Title: getWillWorksDetail
	 * @Description: todo(获取当前选中的审批数据字段和数据信息)
	 * @author 杨希
	 * @date 2014-4-17 下午5:02:51
	 * @throws
	 */
	public function getWillWorksDetail($returnType='json'){
		/*
		 * 这里有2个参数
		 * 参数一：tablename  是当前模型名称（上面方法中数据中的tablename）
		 * 参数二：tableid  是当前模型名称（上面方法中数据中的tableid）
		 */
		//获取传递过来的模型名称
		$modelName	= $_REQUEST['tablename'];
		//获取传递过来的模型数据ID
		$tableid= $_REQUEST['tableid'];
	
		if(!$modelName){
			return $this->getReturnData(array(),'json',0,"模型名称必须");
		}
		if(!$tableid){
			return $this->getReturnData(array(),'json',0,"数据ID值必须");
		}
		//获取配置文件以及数据
		$model = D('SystemConfigDetail');
		//从动态配置获取到数据信息
		$result=$model->GetDynamicconfData($modelName,$tableid);
		//message信息拼装
		$message=array();
		foreach($result as $key => $val){
			//初始化一个字符串容器
			foreach($val as $subkey => $subval){
				$message[$key] = array(
						'name'=>$subkey,
						'value'=>$subval,
						'field'=>$key,
				);
			}
		}
		
		//查询是否存在审批历史一件
		$history = $this->getHistory($modelName, $tableid);
		if($history){
			$message = array_merge($message,$history);
		}
		/*
		 * 封装内嵌表数据，进行页面url标记
		 */
		$neiqianArr = $this->getNeiQian($modelName, $tableid);
		if($neiqianArr){
			$message = array_merge($message,$neiqianArr);
		}
		//封装套表数据，进行页面url标记
		$taobiaoArr = $this->getTaoBiao($modelName, $tableid);
		if($taobiaoArr){
			$message = array_merge($message,$taobiaoArr);
		}
		//查询有关附件
		$attarry=$this->getAttachedRecordList($modelName,$tableid,true,true,$modelName,0,false,false);
		$uploadPath="http://192.168.1.10/systemui/Public/Uploads/";
		$attarryInfo=array();
		foreach ($attarry as $ak=>$av){
			$att = array();
			foreach($av as $aak=>$aav){
				$att[] = array('filename'=>$aav['filename'],'attachurl'=>$uploadPath.$aav['attached']);
			}
			$attarryInfo[$ak]=$att;
		}
		logs($message,'message');
		//调用删除
		$this->getResultval($modelName,&$message);
		//处理数据格式
		$arr = array();
		foreach ($message as $kk=>$vv){ 
			if(strpos($kk,'_souce')){
			 	continue;
			}
			//不显示ID
			if($kk=='id'){
				$vv['field']="is__souce";
				$arr[] = $vv;
				continue;
			}
			//过滤附件的值
			if(strpos($vv['name'],'附件')){
				$vv['value'] ='';
			}
			//如果附件存在则添加值
			if($attarryInfo){
				//存在附件信息，循环绑定到数据列表
				foreach($attarryInfo as $atkey=>$atval){
					if($kk == $atkey){
						$vv['value'] = $atval;
						$vv['field'] = "is_attached_field";
					}
				}
			}
			$arr[] = $vv;
		}
		//unset($arr[])
		//初始化返回值
		$data['list']['data'] = $arr;
		$data['list']['title'] = getFieldBy($modelName, "name", "title", "node");
		$data['list']['category'] = $_REQUEST['category'];
		return $this->getReturnData($data,$returnType,1000,"详情获取成功");
	}
	/**
	 * @Title: getResultval
	 * @Description: todo(根据值的情况 过滤显示字段)
	 * @param string 模型名称 $tablename
	 * @param array 原始数据
	 * @return array 返回处理后的数据
	 * @author liuzhihong
	 * @date 2015-11-11 下午5:32:18
	 * @throws
	 */
// 	function getResultval($tablename,$message){
// 		//删除后缀为_souce主键
// 		foreach($message as $k=>$v){
// 			if(strpos($k,'_souce')){
// 				unset($message[$k]);
// 			}
// 		}
// 		//引入枚举的配置文件
// 		if(file_exists(DConfig_PATH."/System/selectlist.inc.php")){
// 			$list = require DConfig_PATH."/System/selectlist.inc.php";
// 		}
// 		if($tablename){
// 			//查询到要隐藏的KEY
// 			$model = M("mis_dynamic_controll_record");
// 			$result = $model->query( "SELECT fieldname,typeval,resultshowval,showoption FROM `mis_dynamic_controll_record` LEFT JOIN `mis_dynamic_form_propery`  ON mis_dynamic_controll_record.`properyid` = mis_dynamic_form_propery.`id` WHERE mis_dynamic_controll_record.formid =(SELECT id FROM `mis_dynamic_form_manage` WHERE actionname = '".$tablename."')" ); //将返回array()
// 			if($result){
// 				foreach ($result as $rk=>$rv){
// 					if($message[$rv['fieldname']]){
// 						//有枚举
// 						if($re['showoption']){
// 							if($list[$rv['fieldname']][$rv['fieldname']][$message[$rv['fieldname']][value]] ){
								
// 							}
// 						}else{
							
// 						}
// 					}
// 					$listarr=explode(",",$rv['resultshowval']);
// 					foreach($listarr as $lk=>$lv){
// 						if($lv){
// 							$message[$lv][]
// 						}
// 					}
// 				}
// 				//遍历删除
// 				foreach ($message as $ak=>$al){
// 					foreach ($result as $rk=>$rl){
// 						//如果是枚举值就用枚举值来对比
// 						if($rl['showoption']){
// 							if($list[$rl['showoption']]&$ak==$rl['fieldname']&$list[$rl['showoption']][$rl['showoption']][$rl['typeval']]){
// 								logs($list[$rl['showoption']][$rl['showoption']],'showoption');
// 								$listarr = array();
// 								$listarr=explode(",",$rl['resultshowval']);
// 								foreach($listarr as $lak=>$lal){
// 									if(!$lal&!$message[$lal]){
// 										//删除KEY
// 										unset($message[$lal]);
// 									}
// 								}
// 							}
// 						}else if($ak==$rl['fieldname']&$al['value']==$rl['typeval']){
// 							$listarr = array();
// 							$listarr=explode(",",$rl['resultshowval']);
// 							foreach($listarr as $lak=>$lal){
// 								if(!$lal&!$message[$lal]){
// 									//删除KEY
// 									unset($message[$lal]);
// 								}
// 							}
// 						}
// 					}
// 				}
// 		}	
// 		}
// 	}
	/**
	 * @Title: getHistory 
	 * @Description: todo(查询审批数据信息) 
	 * @param string 模型名称 $tablename
	 * @param int 模型数据ID $tableid
	 * @return array 返回处理后的审批记录信息数据    
	 * @author liminggang
	 * @date 2015-11-11 下午5:32:18 
	 * @throws
	 */
	function getHistory($tablename,$tableid){
		//查询是否存在审批历史一件
		$process_info_historyDao = M("process_info_history");
		$map = array();
// 		$map["ostatus"] = array("gt",0);
		$map["_string"] = "ostatus >0 || dotype = 2";
		$map["tableid"] =$tableid;
		$map["tablename"] =$tablename;
		$pihlist = $process_info_historyDao->where($map)->field("id,dotype,doinfo,userid,dotime,ostatus")->order('id desc')->select();
		//过滤回退以前的数据
		$ftrue = true;
		$judge = array();
		foreach ($pihlist as $k2 => $v2) {
			if ($v2['dotype'] == 2) {
				$judge[] = $v2;
				break;
			}
			if ($ftrue) {
				$judge[] = $v2;
			}
		}
		$message = array();
		//拼装历史意见
		if($judge){
			//查询审批节点名称
			$process_relationDao = M("process_relation_form");
			foreach($judge as $k=>$v){
				$map = array();
				$map['id'] = $v['ostatus'];
				if($v['dotype'] == 2){
					//获取审核节点名称
					$relationname = "启动节点";
				}else{
					//获取审核节点名称
					$relationname = $process_relationDao->where($map)->getField("name");
				}
				//获取审核人
				$username = getFieldBy($v['userid'], "id", "name", "user");
				$message["info_history".$v['id']] = array(
						'name'=> $username."({$relationname})",
						'value'=>$v['doinfo']."           ".transTime($v['dotime'],'Y-m-d H:i'),
				);
			}
		}
		return $message;
	}
	
	
	/**
	 * @Title: getDetaileZhList
	 * @Description: todo(列表行数组)
	 * @param linc配置文件 $detailList
	 * @param 二维数据 $volist
	 * @param 数据字段 $fieldname
	 * @param 数据显示名称 $showname
	 * @return 返回数组
	 * @author 黎明刚
	 * @date 2015年1月30日 下午4:04:59
	 * @throws
	 */
	public function getDetaileZhList($detailList,$volist){
		foreach($detailList as $k2=>$v2){
			if($v2['name'] =="id" || $v2['name'] =="action" || $v2['name'] =="auditState" || strpos(strtolower($v2['name']),"datatable")!==false || $v2['shows']==0){
				continue;
			}
			foreach($volist as $k=>$v){
				if(count($v2['func']) >0){
					$varchar = "";
					foreach($v2['func'] as $k3=>$v3){
						//开始html字符
						if(isset($v2['extention_html_start'][$k3])){
							$varchar = $v2['extention_html_start'][$k3];
						}
						//中间内容
						$varchar .= getConfigFunction($v[$v2['name']],$v3,$v2['funcdata'][$k3],$volist[$k]);
	
						if(isset($v2['extention_html_end'][$k3])){
							$varchar .= $v2['extention_html_end'][$k3];
						}
						//结束html字符
					}
					$volist[$k][$v2['name']] = $varchar;
				}
			}
		}
		return $volist;
	}
	/**
	 * @Title: getNeiQainView
	 * @Description: todo(内嵌表列表数据方法)
	 * @return Ambigous <array, unknown>
	 * @author liminggang
	 * @date 2015-7-24 上午9:45:47
	 * @throws
	 */
	public function getNeiQainView(){
		//获取住模型名称
		$zhumodelname = $_REQUEST['zhumodelname'];
		//获取内嵌表模型名称
		$neiqianmodelname = $_REQUEST['neiqianmodelname'];
		//获取内嵌表关联的masid
		$masid = $_REQUEST['masid'];
		// 实例化内嵌表
		$datatablename = D ( $neiqianmodelname );
		// 获取list.inc.php配置文件内容的模型
		$scdmodel = D ( 'SystemConfigDetail' );
		// 获取内嵌表的配置文件
		$neiqdetailList = $scdmodel->getEmbedDetail ( $zhumodelname, $neiqianmodelname);
		$where = array ();
		$where ['masid'] = $masid;
		//内嵌表的数据
		$innerTabelObjdatatable3Data = $datatablename->where ( $where )->select ();
		//获取内嵌表数据内容
		$neiqiandata= $this->getDetaileZhList($neiqdetailList,$innerTabelObjdatatable3Data);
		//记录下标位置
		$xiabiao = array();
		$neiqdetailData =array();
		$k = 0;
		//修改下标
		foreach ($neiqdetailList as $key => $val){
			if($k<4){
			$xiabiao[] = $key;
			$neiqdetailData[] = $val;
			}else{
				break;
			}
			$k++;
		}
		//组合th和td
		$list = array('datelist'=>$neiqdetailData,'volist'=>$neiqiandata,'zhumodelname'=>$zhumodelname,'tablename'=>$neiqianmodelname,'xiabiao'=>$xiabiao);
		$data['list'] = $list;
		
// 		print_r($data);
// 		exit;
		return $this->getReturnData($data,'json',1000,"详情获取成功");
	}
	/**
	 * @Title: getNeiQianDetail
	 * @Description: todo(内嵌表行数据详情信息)
	 * @param 返回的数据格式类型 $returnType
	 * @return Ambigous <array, unknown>
	 * @author liminggang
	 * @date 2015-7-24 上午9:45:23
	 * @throws
	 */
	public function getNeiQianDetail($returnType='json'){
		/*
			* 这里有3个参数
			*
			* 参数一：tablename  是当前模型名称（上面方法中数据中的tablename）
			* 参数二：tableid  是当前模型名称（上面方法中数据中的tableid）
			* 参数三：zhumodelname 是当前内嵌表的主模型(为了查找内嵌表的配置文件list.inc.php)
			*/
		//主模型名称
		$zhumodelname	= $_REQUEST['zhumodelname'];
		//获取传递过来的模型名称
		$modelName	= $_REQUEST['tablename'];
		//获取传递过来的模型数据ID
		$tableid	= $_REQUEST['tableid'];
	
		if(!$_REQUEST['tablename']){
			return $this->getReturnData(array(),'json',0,"模型名称必须");
		}
		if(!$_REQUEST['tableid']){
			return $this->getReturnData(array(),'json',0,"数据ID值必须");
		}
		if(!$zhumodelname){
			return $this->getReturnData(array(),'json',0,"主模型名称必须");
		}
		// 获取list.inc.php配置文件内容的模型
		$scdmodel = D ( 'SystemConfigDetail' );
		// 获取内嵌表的配置文件
		$detailList = $scdmodel->getEmbedDetail( $zhumodelname, $modelName);
	
		$map['id'] =$tableid;
		//查询数据库取得详细信息记录
		$list=D($modelName)->where($map)->find();
		//定义返回值
		$returnData=array();
		//首先循环动态配置的详细信息
		foreach($detailList as $detailListKey => $detailListVal){
			//根据动态配置某个标识过滤多余的数据
			if( $detailListVal["shows"]==1){
				//将动态配置名称与数据库返回记录里的字段主键做比较，存在时执行
				if(array_key_exists($detailListVal['name'],$list)){
					//存在函数时
					if($detailListVal['func']){
						foreach($detailListVal['func'] as $funcKey=>$funcVal){
							$returnData[$detailListVal['name']][$detailListVal['showname']]=getConfigFunction($list[$detailListVal['name']],$funcVal,$detailListVal['funcdata'][$funcKey],$list);
						}
					}else{
						$returnData[$detailListVal['name']][$detailListVal['showname']]=$list[$detailListVal['name']];
					}
				}else{
					//将动态配置名称与数据库返回记录里的字段主键做比较，不存在时，通过配置函数函数获取到值
					//存在函数时
					if($detailListVal['func']){
						foreach($detailListVal['func'] as $funcKey=>$funcVal){
							$returnData[$detailListVal['name']][$detailListVal['showname']]=getConfigFunction($list[$detailListVal['name']],$funcVal,$detailListVal['funcdata'][$funcKey],$list);
						}
					}else{
						$returnData[$detailListVal['name']][$detailListVal['showname']]=$list[$detailListVal['name']];
					}
				}
			}
		}
		//message信息拼装
		$message=array();
		$index = 0;
		foreach($returnData as $key => $val){
			//初始化一个字符串容器
			foreach($val as $subkey => $subval){
				$message[$index] = array(
						'name'=>$subkey,
						'value'=>$subval,
				);
				$index++;
			}
		}
		$returnData = array();
		//将值赋予返回数据
		$returnData['list']=$message;
// 		print_r($returnData);
// 		exit;
// 		logs($returnData,'list');
		return $this->getReturnData($returnData,$returnType,1000,"详情获取成功");
	}
	
	/**
	 * @Title: auditProcess
	 * @Description: todo(手机助手审核待办事务)
	 * @author 杨希
	 * @date 2014-4-17 下午5:02:51
	 * @throws
	 */
	public function auditProcess(){
		if(!$_REQUEST['tablename']){
			return $this->getReturnData(array(),'json',0,"模型名称必须");
		}
		if(!$_REQUEST['tableid']){
			return $this->getReturnData(array(),'json',0,"数据ID值必须");
		}
		C('TOKEN_ON',false);
		//拼装当前登陆用户的session
		$_SESSION[C('USER_AUTH_KEY')]=$this->userid;
		//初始化审核验证SESSION数据
		$_SESSION[strtolower($_REQUEST['tablename'])."_index"] == 1;
		$_POST['id']=$_REQUEST['tableid'];
		unset($_REQUEST['tableid']);
		$modelname=$_REQUEST['tablename'];
		//初始化打回必备POST值
		$_POST['doinfo']=$_REQUEST['doinfo'];
		// 获取当前表单ID
		$masid = $_POST ['id'];
		$userid = $_SESSION[C('USER_AUTH_KEY')];
		//获取流程审批节点信息
		$mis_work_monitoringDao = M("mis_work_monitoring");
		$where = array();
		$where['tablename'] = $modelname;
		$where['tableid'] = $masid;
		$where['dostatus'] = 0;//未处理
		$where['ismobile'] = 1; //手机端
		$where ['_string'] = 'FIND_IN_SET(  ' . $userid . ',curAuditUser )';
		$myworklist = $mis_work_monitoringDao->where($where)->count();
		//获取流程审批节点信息
		$process_relation_formDao = M("process_relation_form");
		$where = array();
		$where['tablename'] = $modelname;
		$where['tableid'] = $masid;
		$where['auditState'] = 0;//未处理
		$where['doing'] = 1;//进行中的节点
		$where['ismobile'] = 1;//手机端
		$where['flowtype'] = 2;//审批节点或者转子流程节点
		$newinfo = $process_relation_formDao->where($where)->count();
		/*
		 * 验证串行同时审核问题。这里要排除子流程情况，子流程时$infolist[0]['curAuditUser']为null
		*/
		if($myworklist==0 || $newinfo ==0){
			return $this->getReturnData(array(),'json',0,"单据已被审核，无需再审");
		}else{
			$m = M();
			$m->startTrans();
			$model= A( $modelname );
			$model->auditProcess(1);
			$m->commit();
			return $this->getReturnData(array(),'json',1000,"审核成功");
		}
	}
	/**
	 * @Title: backprocess
	 * @Description: todo(手机助手打回待办事务)
	 * @author 杨希
	 * @date 2014-4-17 下午5:02:51
	 * @throws
	 */
	public function backprocess(){
		if(!$_REQUEST['tablename']){
			return $this->getReturnData(array(),'json',0,"模型名称必须");
		}
		if(!$_REQUEST['tableid']){
			return $this->getReturnData(array(),'json',0,"数据ID值必须");
		}
		C('TOKEN_ON',false);
		$_SESSION[C('USER_AUTH_KEY')]=$this->userid;
		$_POST['id']=$_REQUEST['tableid'];
		unset($_REQUEST['tableid']);
		$modelname=$_REQUEST['tablename'];
	
		$model2=D($modelname);
		//获取当前表单ID
		$masid = $_POST ['id'];
		//获取当前登陆人
		$userid = $_SESSION[C('USER_AUTH_KEY')];
		//获取流程审批节点信息
		$mis_work_monitoringDao = M("mis_work_monitoring");
		$where = array();
		$where['tablename'] = $modelname;
		$where['tableid'] = $masid;
		$where['dostatus'] = 0;//未处理
		$where['ismobile'] = 1; //手机端
		$where ['_string'] = 'FIND_IN_SET(  ' . $userid . ',curAuditUser )';
		$myworklist = $mis_work_monitoringDao->where($where)->count();
		//获取流程审批节点信息
		$process_relation_formDao = M("process_relation_form");
		$where = array();
		$where['tablename'] = $modelname;
		$where['tableid'] = $masid;
		$where['auditState'] = 0;//未处理
		$where['doing'] = 1;//进行中的节点
		$where['ismobile'] = 1;//手机端
		$where['flowtype'] = 2;//审批节点
		$newinfo = $process_relation_formDao->where($where)->count();
		/*
		 * 验证串行同时审核问题。这里要排除子流程情况，子流程时$infolist[0]['curAuditUser']为null
		*/
		if($myworklist==0 || $newinfo==0){
			return $this->getReturnData(array(),'json',0,"单据已被其他同事退回，无需再审！");
		}else{
			//初始化打回必备POST值
			$_POST['backprocess']="流程回退";
			$_POST['doinfo']=$_REQUEST['doinfo'];
			$m = M();
			$m->startTrans();
			$model= A( $modelname );
			$model->backprocess(1);
			$m->commit();
			return $this->getReturnData(array(),'json',1000,"退回成功");
		}
	}
}