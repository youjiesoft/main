<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(用户常用模型) 
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-8-29 下午3:23:54 
 * @version V1.0
 */
class UserOftenMenuModel extends CommonModel {
	//配置对应的数据表
	protected $trueTableName = 'mis_user_often_menu';
	//根据TP规则，自动填充
	public $_auto	=array(
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
		   array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),

	);
	
	//获取用户常用的面板
	public function getSysOftenList(){
		// 常用功能
		$umap['uid'] = $_SESSION[C('USER_AUTH_KEY')];
		$umap['status'] = 1;
		$oftenList = $this->where($umap)->order("createtime asc")->select();
		//获取用户权限
		$access = getAuthAccess();
		foreach ($oftenList as $k => $v) {
			$mdoelarr = explode('/', $v ['url']);
			if(!$mdoelarr[1]) $mdoelarr[1] = 'index';
			if (!isset ($access[strtoupper( APP_NAME )][strtoupper($mdoelarr[0])][strtoupper($mdoelarr[1])]) && !$_SESSION [C('ADMIN_AUTH_KEY')]) {
				unset($oftenList[$k]);
				continue;
			}
		}
		return $oftenList;
	}
	/**
	 * @Title: getUserMenu 
	 * @Description: todo(我的常用模块) 
	 * @param array 我的常用模块数组 $workoftenList  
	 * @author liminggang 
	 * @date 2014-9-1 下午2:03:06 
	 * @throws
	 */
	public function getUserMenu(){
		//获取系统模块
		$workoftenList = $this->getSysOftenList();
		//读取配置文件
		$defaultoftenlist=array();
		$oftenconfiglist=require DConfig_PATH."/System/oftenconfig.inc.php";
		foreach ($oftenconfiglist as $key=>$val){
			if($val['type']==1){//1为首页默认常用   2为工作中心常用
				$defaultoftenlist[]=$val;
			}
		}
		if($workoftenList){
			if(count($workoftenList)<7){
				//算出差数
				$count=7-(count($workoftenList));
				//去除重复数据
				foreach ($workoftenList as $wkey=>$wval){
					$relList[]=$wval['rel'];
				}
				$i=1;
				foreach ($defaultoftenlist as $dkey=>$dval){
					if(in_array($dval['rel'],$relList)){
						unset($defaultoftenlist[$dkey]);
					}else{
						if($i<=$count){
							$workoftenList[]=$dval;
						}
						$i++;
					}
				}
			}
		}else{
			//默认常用图标
			$workoftenList=$defaultoftenlist;
		}
		return $workoftenList;
	}
	public function oftenaddtree($group_id = 1,$m_list){
		$access = getAuthAccess();
		$umap['uid'] = $_SESSION[C('USER_AUTH_KEY')];
		$umap['status'] = 1;
		$oftenList = $this->where($umap)->getField('url',true);
		$node = D( "Node" );
		$map['status'] = 1;
		$map['showmenu'] = 1;
		$map['type'] = 3;
		$map['group_id'] = $group_id;
		$list = $node->where($map)->order('sort asc')->select();
		$tree = array();
		foreach ($list as $k => $v) {
			if($v['name']!="Index"){
				if(substr($v['name'], 0, 10)!="MisDynamic"){
					if(!in_array($v['name'], $m_list))  continue;
				}
				if (!isset ($access[strtoupper( APP_NAME )][strtoupper ($v ['name'])]) && !$_SESSION [C('ADMIN_AUTH_KEY')]) {
					continue;
				}
				if (in_array($v['name']."/index", $oftenList)) continue;
				$tree[] = $v;
			}
		}
// 		//查询功能盒子  --暂无发现用处
// 		$FunctionalBoxmodel=D("MisSystemFunctionalBox");
// 		$Functionalarr=$FunctionalBoxmodel->where("status=1 and groupid=".$group_id)->select();
// 		$this->assign("Functionalarr",$Functionalarr);
		return $tree;
	}
	// 	/**
	// 	 * @Title: userIndexOperate
	// 	 * @Description: todo(userIndex页面的操作调用函数)
	// 	 * @author 杨东
	// 	 * @date 2013-3-30 下午2:09:53
	// 	 * @throws
	// 	 */
	// 	private function userIndexOperate(){
	// 		if($_GET['type'] == 1) {
	// 			//调用生成首页常用功能
	// 			$uommodel = D("UserOftenMenu");
	// 			$workoftenList=$uommodel->getSysOftenList();
	
	// 			$oftenconfiglist=require DConfig_PATH."/System/oftenconfig.inc.php";
	// 			foreach ($oftenconfiglist as $key=>$val){
	// 				if($val['type']==1){//1为首页默认常用   2为工作中心常用
	// 					$defaultoftenlist[]=$val;
	// 				}
	// 			}
	// 			if($workoftenList){
	// 				if(count($workoftenList)<7){
	// 					//算出差数
	// 					$count=7-(count($workoftenList));
	// 					//去除重复数据
	// 					foreach ($workoftenList as $wkey=>$wval){
	// 						$relList[]=$wval['rel'];
	// 					}
	// 					$i=0;
	// 					foreach ($defaultoftenlist as $dkey=>$dval){
	// 						if(in_array($dval['rel'],$relList)){
	// 							unset($defaultoftenlist[$dkey]);
	// 						}else{
	// 							if($i<=$count){
	// 								$workoftenList[]=$dval;
	// 							}
	// 							$i++;
	// 						}
	// 					}
	// 				}
	// 			}else{
	// 				//默认常用图标
	// 				$workoftenList=$defaultoftenlist;
	// 			}
	// 			$this->assign('oftenList',$workoftenList);
	// 			//加载常用功能页面
	// 			$this->display('oftenindex');
	// 			exit;
	// 		} elseif ($_GET['type'] == 2) {
	// 			//弹出新增常用功能页面
	// 			$this->oftenadd();
	// 			exit;
	// 		} elseif ($_GET['type'] == 3) {
	// 			//删除常用功能
	// 			$uommodel = D("UserOftenMenu");
	// 			$uommap['id'] = $_POST['id'];
	// 			$uommodel->startTrans();
	// 			$res = $uommodel->where($uommap)->delete();
	// 			$uommodel->commit();
	// 			echo $res;
	// 			exit;
	// 		} elseif ($_GET['type'] == 4) {
	// 			
	// 		} elseif ($_GET['type'] == 5) {
	// 			//新增常用功能
	// 			$uommodel = D("UserOftenMenu");
	// 			//查询当前用户是否已添加6个常用
	// 			$umap['uid'] = $_SESSION[C('USER_AUTH_KEY')];
	// 			$umap['status'] = 1;
	// 			$workoftenList = $uommodel->where($umap)->order("createtime asc")->select();
	// 			if(count($workoftenList)>6){
	// 				$this->error("常用菜单已存在7个,请删除后再添加！");
	// 				exit;
	// 			}
	// 			//新增常用功能
	// 			$url = $_POST['url'];
	// 			$list = $uommodel->where("uid='".$_SESSION[C('USER_AUTH_KEY')]."' and url='".$_POST['url']."'")->count('*');
	// 			if($list){
	// 				$this->error("已经存在相同菜单！");
	// 				exit;
	// 			}
	// 			$nodeModel = D('Node');
	// 			$nodeArr = explode('/', $url);
	// 			$nodeVO = $nodeModel->where("name = '".$nodeArr[0]."'")->find();
	// 			if(!$_POST['rel'] || !$_POST['title'] || !$nodeVO['icon'] || !$url){
	// 				$this->error("常用菜单添加失败！");
	// 				exit;
	// 			}
	// 			$data['uid'] = $_SESSION[C('USER_AUTH_KEY')];
	// 			$data['rel'] = $_POST['rel'];
	// 			$data['title'] = $_POST['title'];
	// 			$data['url'] = $url;
	// 			$data['icon'] = $nodeVO['icon'];
	// 			$data['createtime'] = time();
	// 			$uommodel->startTrans();
	// 			$uommodel->data($data);
	// 			$res = $uommodel->add($data);
	// 			$uommodel->commit();
	// 			if ($res!==false) { //保存成功
	// 				$this->success ( L('_SUCCESS_'));
	// 			} else {
	// 				$this->error ( L('_ERROR_') );
	// 			}
	// 			exit;
	// 		}elseif ($_GET['type'] == 6) {//功能盒子添加常用
	// 			//新增常用功能
	// 			$uommodel = D("UserOftenMenu");
	// 			$url = $_POST['url'];
	// 			$list = $uommodel->where("uid='".$_SESSION[C('USER_AUTH_KEY')]."' and url='".$_POST['url']."'")->count('*');
	// 			if($list){
	// 				$this->error("已经存在相同菜单！");
	// 				exit;
	// 			}
	// 			$data['uid'] = $_SESSION[C('USER_AUTH_KEY')];
	// 			$data['rel'] = $_POST['rel'];
	// 			$data['title'] = $_POST['title'];
	// 			$data['url'] = $url;
	// 			$data['icon'] = $_POST['icon'];
	// 			$data['target'] = $_POST['target'];
	// 			$data['createtime'] = time();
	// 			$uommodel->startTrans();
	// 			$uommodel->data($data);
	// 			$res = $uommodel->add($data);
	// 			$uommodel->commit();
	// 			if ($res!==false) { //保存成功
	// 				$this->success ( L('_SUCCESS_'));
	// 			} else {
	// 				$this->error ( L('_ERROR_') );
	// 			}
	// 			exit;
	// 		}
	// 	}
}
?>