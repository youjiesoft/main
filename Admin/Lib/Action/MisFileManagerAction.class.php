<?php
/**
 * @Title: file_name
 * @Package package_name
 * @Description: todo(文件柜控制器)
 * @author liminggang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-8-13 下午4:38:54
 * @version V1.0
 */
class MisFileManagerAction extends CommonAction{
	/**
	 * 构造树形节点  @changeby wangcheng
	 * @param array  $alldata  构造树的数据
	 * @param array  $param    传入数组参数，包含rel，url【url中参数传递用#name#形式，name 为字段名字】
	 * @param array  $returnarr  初始化s树形节点，可以传入数字，默认选中哪一行数据
	 * @return string
	 */
	function getTree($alldata=array(),$param=array(),$returnarr=array()){
		$selected = $_REQUEST["pid"] ? $_REQUEST["pid"]:1;
		foreach($alldata as $k=>$v){
			$newv=array();
			$matches=array();
			preg_match_all("|#+(.*)#|U", $param["url"], $matches);
			foreach($matches[1] as $k2=>$v2){
				if( isset($v[$v2]) ) $matches[1][$k2]=$v[$v2];
			}
			$url = str_replace($matches[0],$matches[1],$param['url']);
			$newv['id']=$v['id'];
			$newv['realid']=$v['realid'];
			$newv['pId']=$v['parentid']?$v['parentid']:0;
			$newv['type']='post';
			$newv['url']=$url;
			$newv['shareeduserid'] = $v['shareeduserid'];
			$newv['target']='ajax';
			$newv['rel']=$param['rel'];
			$newv['title']=$v['name']; //光标提示信息
			$newv['name']=missubstr($v['name'],18,true); //结点名字，太多会被截取,针对于现在的ZTREE，宽度不能超过18个字符。
			$newv['open']= $v["id"] ? false:true;
			$newv['isParent'] = true;
			if($v['realid']== $selected){
				$this->assign("selectedid",$v["id"]);
			}
			array_push($returnarr,$newv);
		}
		return $returnarr;
	}

	/**
	 * @Title: DefaultFilenameGen
	 * @Description: todo(获取转移后的文件或者文件夹名称)
	 * @param string $dst 文件路径
	 * @param int $step 一个标志，true 代表文件夹，false 代表文件
	 * @return string 返回一个转义后的文件或者文件夹名称
	 * @author liminggang
	 * @date 2013-8-20 下午2:49:35
	 * @throws
	 */
	function DefaultFilenameGen( $dst ,$step = 1) {
		$tmpdst = $dst;
		//文件夹
		if($step){
			while(true){
				$info   = pathinfo( $dst );
				$dir    = $info[ "dirname" ];
				$name   = $info[ "filename" ];
				$tmp = md5($name);	//用md5转义文件夹名称
				$rename = build_count_rand(1,5,1);
				$tmp = $rename[0];
				$tmpdst = $dir."/".$tmp;
				if( !file_exists( $tmpdst ) ) {
					break;
				}
			}
		}else{
			while( true ) {
				$info   = pathinfo( $dst );
				$ext    = $info[ "extension" ];
				$dir    = $info[ "dirname" ];
				$name   = $info[ "filename" ];
				$rename = build_count_rand(1,2,1);
				$tmp = time()."_".$rename[0].".".$ext; //用时间戳转义文件名称
				$tmpdst = $dir."/".$tmp;
				if( !file_exists( $tmpdst ) ) {
					break;
				}
			}
		}
		return $tmp;
	}

	/**
	 * @Title: generateTree
	 * @Description: todo(这里构造左侧树结构的数据)
	 * @author liminggang
	 * @date 2013-8-29 下午3:58:32
	 * @throws
	 */
	private function generateTree(){
		$name = $this->getActionName();
		$MisFileManagerModel = D($name);
		//构造树形顶级节点
		$arr[] = array("id"=>0,"pId"=>0,"realid"=>0,"category"=>0,"name"=>"文档树形结构","open"=>true,"title"=>"文档树形结构");
		//构造树形二级节点,即系统默认的文件夹
		$map=array();
		$map['type'] = 1;//文件夹
		$map['status'] = 1; //状态为正常
		$map['issystem'] = 1;//为系统文件
		$sytlist = $MisFileManagerModel->where($map)->select();
		$sytlist=array_merge($arr,$sytlist);
		$map=array();

		//查询我的文件夹
		$map['type'] = 1;
		$map['status'] = 1;
		$map['category'] = 1;
		$map['issystem'] = 0;
		$map['createid'] = $_SESSION[C('USER_AUTH_KEY')];
		$list = $MisFileManagerModel->where($map)->select();
		if($list){
			$sytlist=array_merge($sytlist,$list);
		}
		//查询单位 公文
		if( $_SESSION['a'] ){//如果是管理员不过滤
			unset($map);
			$map['type'] = 1;
			$map['status'] = 1;
			$map['category'] = array("not in",array(1,5));
			$map['issystem'] = 0;
			$list = $MisFileManagerModel->where($map)->select();
			if($list){
				$sytlist=array_merge($sytlist,$list);
			}
		}else{//找出对应 管理员 管理的所有文件
			unset($map);
			$map['type'] = 1;
			$map['status'] = 1;
			$map['issystem'] = 0;
			 
			//找出管理员对应文件id
			$alllist = $MisFileManagerModel->where($map)->field("id,parentid,userid")->select();
			$downfid="";
			$upid="";
			$inid=array();
			foreach($alllist as $k=>$v){
				if( $v["userid"]==$_SESSION[C('USER_AUTH_KEY')] ){
					$downfid.= $this->downAllChildren($alllist,$v["id"]);
					$upid.=$this->parentRecursion($v["id"]);
				}
			}
			if($downfid){
				$downfid = explode(",",$downfid);
				array_shift ($downfid);
			}

			if($upid){
				$upid = explode(",",$upid);
				array_shift ($upid);
			}
			if($downfid || $upid){//过滤管理员文件的id
				$inid=array_merge($upid,$downfid);
			}
			if($inid){
				$map=array();
				$map['id'] = array(' in ',$inid);
				$map['status'] = 1;
				$map['type'] = 1;
				$list = $MisFileManagerModel->where($map)->select();
				if($list){
					$sytlist=array_merge($sytlist,$list);
				}
			}
		}

		//查询共享(借阅)给当前用户的文件夹
		$map=array();
		//$map['userid'] = $_SESSION[C('USER_AUTH_KEY')];
		$map['userid'] = array("exp","=0 OR `userid`=".$_SESSION[C('USER_AUTH_KEY')]);
		$map['category'] = array("in",array(1,2,3,4));
		$map['status'] = 1;
		$MisFileManagerAccessModel = M("mis_file_manager_access");
		//共享文件夹ID
		$accessfidarr=$MisFileManagerAccessModel->where($map)->getField('fid',true);
		$accessdownfid=$accessupid="";

		if( $accessfidarr ){
			foreach($accessfidarr as $k=>$v){
				$accessdownfid.= $this->downAllChildren($alllist,$v);
				$accessupid.=$this->parentRecursion($v);
			}
			
			if($accessdownfid){
				$accessdownfid = explode(",",$accessdownfid);
				array_shift ($accessdownfid);
			}
			if($accessupid){
				$accessupid = explode(",",$accessupid);
				array_shift ($accessupid);
			}
			if($accessdownfid || $accessupid){
				$accessinid=array_merge($accessupid,$accessdownfid);
			}
			
			unset($map);
			$map['id'] = array(' in ',$accessinid);
			//$map['type'] = 1;
			//获得共享文件夹信息
			$arrlist=$MisFileManagerModel->where($map)->select();
			$ar=array(); //存储分享人员
			//取出分享人 
			foreach($arrlist as $k=>$v){
				if($v['category']==1){
					if( $v['createid'] == $_SESSION[C('USER_AUTH_KEY')] || ($v["type"] == 0 && $v["parentid"] != 1)){
						unset($arrlist[$k]);
						continue;//过滤自己共享给自己
					}
					if(!in_array($v['createid'],array_keys($ar))){
						$newname = M("user")->find( $v['createid'] );
						$depname = M("mis_system_department")->find( $newname["dept_id"] );
						$sharefilename = $depname["name"]."(".$newname["name"].")";
						
						$ar[$v['createid']] = array(
							'shareeduserid'=>$v['createid'],
							'name'=>$sharefilename,
							'parentid'=>5,//id=5是分享顶级节点
							'category'=>"1/uid/".$v['createid'],
							'share'=>1,
						);
					}
					if( $v["type"] == 0 && $v["parentid"] == 1 ){
						unset($arrlist[$k]);
						continue;
					}
					$arrlist[$k]['share'] = 1;
					$arrlist[$k]['realid'] = $v["id"];
				}
			}
			 
			$sytlist=array_merge($sytlist,$ar);
			if($arrlist){
				$sytlist = array_merge($sytlist,$arrlist);
			}
		}

		$fidarr=array();
		foreach($sytlist as $k=>$v){
			if( !in_array($v["id"],$fidarr) && $v["share"]!=1){
				$sytlist[$k]["realid"]=$v["id"];
				$sytlist[$k]["id"]=$k;
				$fidarr[]= $v["id"];
			}else{
				if($v["share"]==1){
					$sytlist[$k]["id"]=$k;
				}else{
					unset($sytlist[$k]);
				}
			}
		}
		//print_r($sytlist);
		$this->doTree($sytlist);
		$param['rel']="myfileManagerBox";
		$param['url']=__URL__."/index/jump/1/pid/#realid#/cid/#category#/share/#share#";
		$shareFile = $sytlist[5];
		$sytlist[5] =$sytlist[2];
		$sytlist[2] =$shareFile;
		$typeTree = $this->getTree($sytlist,$param);
		$this->assign('treearr',json_encode($typeTree));
		return $typeTree;
	}

	public function doTree( &$arr ){
		if($arr){
			foreach( $arr as $k=>$v ){
				if(!isset($v["realid"])){
					$arr[$k]["realid"]=$v["id"];
					$arr[$k]["id"]=$k;
				}

				if( isset($v['share']) ){
					$this->doShareId($arr,$k);
				}else{
					$this->doRealId($arr,$k);
				}
			}
		}
		//print_r($arr);
		return $arr;
	}
	function doShareId( &$arr ,&$kk){
		$arrnow=$arr[$kk];
		if($arrnow["parentid"]==1){
			foreach( $arr as $k2=>$v2 ){
				if($v2["shareeduserid"] && $v2["shareeduserid"]==$arrnow["createid"]){
					$arr[$kk]["parentid"]=$v2["id"];
					//$arr[$kk]=$arrnow;
					break;
				}
			}
		}else{
			foreach( $arr as $k2=>$v2 ){
				if( !isset($v2["shareeduserid"]) && isset($v2['share']) ){
					if($arrnow["parentid"]==$v2["realid"] ){
						$arr[$kk]["parentid"]=$v2["id"];
					}
				}
			}
		}
	}
	function doRealId( &$arr,$kk){
		$arrnow=$arr[$kk];
		foreach( $arr as $k2=>$v2 ){
			if(isset($v2["share"])) continue;
			if( $arrnow["parentid"]==$v2["realid"] ){
				$arr[$kk]["parentid"]=$v2["id"];
			}
		}
	}

	
	public function index(){
		//树结构
		$lefttree = $this->generateTree();
		$name = $this->getActionName();
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		$searching = 0;
		if(isset($map["_complex"])){
			$searching = 1;
		}
		//默认查询我的文档
		$pid = $_REQUEST['pid'];
		$share = intval($_REQUEST['share']) ? intval($_REQUEST['share']):0;
		$cid = intval($_REQUEST['cid']);
		$uid = intval($_REQUEST['uid']) ? intval($_REQUEST['uid']):"";

		$this->assign('pid',$pid);
		$this->assign('share',$share);
		$this->assign('cid',$cid);
		$this->assign('uid',$uid);
			
		if($cid==5){//if share type
			if($searching){
			}else{
				$maparr=array();
				//$maparr['userid'] = $_SESSION[C('USER_AUTH_KEY')];
				$maparr['userid'] = array("exp","=0 or `userid`=".$_SESSION[C('USER_AUTH_KEY')]);
				$maparr['status'] = 1;
				$maparr['category'] = 1;//过滤出来类型1的
				$MisFileManagerAccessModel = M("mis_file_manager_access");
				//共享文件夹ID
				$accessfidarr=$MisFileManagerAccessModel->where($maparr)->getField('fid',true);
				if( $accessfidarr ){
					$map['id'] = array(' in ',$accessfidarr);
					$model = D($name);
					$arrlist=$model->where($map)->select();
					$ar=array();
					$shareusertree =array();
					foreach($lefttree as $k=>$v){
						if($v['pId']==5){
							$shareusertree[$v["shareeduserid"]]=$v;
						}
					}
					//print_r($lefttree);
					foreach($arrlist as $k=>$v){
						if(!in_array($v['createid'],array_keys($ar)) && $v['createid']!=$_SESSION[C('USER_AUTH_KEY')]){
							$newname = M("user")->find( $v['createid'] );
							$depname = M("mis_system_department")->find( $newname["dept_id"] );
							$sharefilename = $depname["name"]."(".$newname["name"].")";
							//$lefttree
							$ar[$v['createid']] = $shareusertree[$v['createid']];
							$ar[$v['createid']]['name']=$sharefilename;
						}
					}
					
					$this->assign ( 'list', $ar );
				}
			}
		}
		else{
			if($share ==1){// click share folder
				if($searching){
					
				}else{
					if($uid){//by share userid
						$msmap=array();
						$msmap['userid'] = array("exp","=0 or userid=".$_SESSION[C('USER_AUTH_KEY')]);
						$msmap['status'] = 1;
						$MisFileManagerAccessModel = M("mis_file_manager_access");
						//共享文件夹ID
						$accessfidarr=$MisFileManagerAccessModel->where($msmap)->getField('fid,userid');
						//获取共享文件顶级id 
						$upfid="";
						$idarr = array_keys($accessfidarr);
						foreach($idarr as $k=>$v){
							$upfid.= $this->parentRecursion($v);
						}
						$idarr = explode(",",$upfid);
						array_shift ($idarr);
						$map['id'] = array(' in ',$idarr);
						 
						$map['createid'] = $uid;
						$map['parentid'] = 1;
						$map['category'] = $cid;
						$map['status'] = 1;
						$map['issystem'] = 0;
						$this->_list($name, $map);
					}else{//by file
						$msmap=array();
						$msmap['userid'] = array("exp","=0 or `userid`=".$_SESSION[C('USER_AUTH_KEY')]);
						$msmap['status'] = 1;
						$MisFileManagerAccessModel = M("mis_file_manager_access");
						//共享文件夹ID
						 
						$alllist = M("mis_file_manager")->field("id,parentid,userid")->select();
						$fidarr=$MisFileManagerAccessModel->where($msmap)->getField('fid',true);
						//获取共享文件顶级id 
						$upfid=$downfid="";
						foreach($fidarr as $k=>$v){
							$upfid.= $this->parentRecursion($v);
							$upfid.= $this->downAllChildren($alllist,$v);
						}
						$fidarr = explode(",",$upfid);
						array_shift ($fidarr);
						if($fidarr){
							array_unique($fidarr);
						}
						
						$map['id'] = array(' in ',$fidarr);
						$map['parentid'] = $pid;
						$map['category'] = $cid;
						$map['status'] = 1;
						$map['issystem'] = 0;
						$this->_list($name, $map);
					}
				}
			}
			else{
				if($searching){
					 
				}else{
					$map['parentid'] = $pid;
					$map['category'] = $cid;
					$map['status'] = 1;
					$map['issystem'] = 0;
					if(!in_array($cid,array(1,4))){
						if( !isset($_SESSION['a']) ){
							//找出管理员对应文件id
							$model =D($name);
							$alllist = $model->field("id,parentid,userid")->select();
							$downfid="";
							$upid="";
							$inid=$managerfid=array();
							foreach($alllist as $k=>$v){
								if( $v["userid"]==$_SESSION[C('USER_AUTH_KEY')] ){
									$downfid.= $this->downAllChildren($alllist,$v["id"]);
									$upid.=$this->parentRecursion($v["id"]);
								}
							}
							if($downfid){
								$managerfid = explode(",",$downfid);
								array_shift ($managerfid);
								$this->assign ( 'managerfid', $managerfid );
							}
	
							//找出对应授权了的文件id
							$mapaccess['userid'] = array("exp","=0 OR `userid`=".$_SESSION[C('USER_AUTH_KEY')]);
							$accessModel = M("mis_file_manager_access");
							$accessfidarr=$accessModel->where($mapaccess)->getField('fid',true);
							foreach($accessfidarr as $k=>$v){
								$downfid.= $this->downAllChildren($alllist,$v);
								$upid.=$this->parentRecursion($v);
							}
							if($downfid){
								$downfid = explode(",",$downfid);
								array_shift ($downfid);
							}
							if($upid){
								$upid = explode(",",$upid);
								array_shift ($upid);
							}
							if($downfid || $upid){//过滤管理员文件的id
								$inid=array_merge($upid,$downfid);
							}
							//过滤id条件
							$map["id"]=array("in",$inid);
							//$this->assign ( 'managerfid', $downfid );
							//获取父级的下载读取权限等
							$map['parentid'] = $pid;
						}
					}else if($cid==1){
						$model =D($name);
						$map1=array();
						$map1['createid'] = $_SESSION[C('USER_AUTH_KEY')];
						$alllist = $model->where($map1)->field("id,parentid,userid")->select();
						$managerfid=array();
						foreach($alllist as $k=>$v){
							if( $v["userid"]==$_SESSION[C('USER_AUTH_KEY')] ){
								$downfid.= $this->downAllChildren($alllist,$v["id"]);
							}
						}
						if($downfid){
							$managerfid = explode(",",$downfid);
							array_shift ($managerfid);
							$this->assign ( 'managerfid', $managerfid );
						}
						$map['createid'] = $_SESSION[C('USER_AUTH_KEY')]; //查询当前用户的文件夹
					}
					$this->_list($name, $map,'',false,"id");
				}
			} 
		}
		
		if($searching){
			$map['status'] = 1;
			$map['issystem'] = 0;
			$model =D($name);
			if( !isset($_SESSION['a']) ){
				//找出管理员对应文件id
				$alllist = $model->field("id,parentid,userid")->select();
				$downfid="";
				$upid="";
				$inid=$managerfid=array();
				foreach($alllist as $k=>$v){
					if( $v["userid"]==$_SESSION[C('USER_AUTH_KEY')] ){
						$downfid.= $this->downAllChildren($alllist,$v["id"]);
						$upid.=$this->parentRecursion($v["id"]);
					}
				}
				if($downfid){
					$managerfid = explode(",",$downfid);
					array_shift ($managerfid);
					$this->assign ( 'managerfid', $managerfid );
				}
				//找出对应授权了的文件id
				$mapaccess['userid'] = array("exp","=0 OR `userid`=".$_SESSION[C('USER_AUTH_KEY')]);
				$accessModel = M("mis_file_manager_access");
				$accessfidarr=$accessModel->where($mapaccess)->getField('fid',true);
				foreach($accessfidarr as $k=>$v){
					$downfid.= $this->downAllChildren($alllist,$v);
					$upid.=$this->parentRecursion($v);
				}
				if($downfid){
					$downfid = explode(",",$downfid);
					array_shift ($downfid);
				}
				if($upid){
					$upid = explode(",",$upid);
					array_shift ($upid);
				}
				if($downfid || $upid){//过滤管理员文件的id
					$inid=array_merge($upid,$downfid);
				}
				//找出对应自己的文件或文件夹 
				$myfilemap = array();
				$myfilemap['status'] = 1;
				$myfilemap['issystem'] = 0;
				$myfilemap['createid'] = $_SESSION[C('USER_AUTH_KEY')];
				$myfilemap['category'] =1 ;
				$myfileid = $model->where($myfilemap)->getField("id",true);
				if($myfileid){
					$inid = array_merge ($inid,$myfileid);
				}
				if($inid ){
					$inid = array_unique($inid);
					$map["id"]= array("in",$inid);
				} 
			}
			$this->_list($name, $map,'',false,"id");
		}
		
		$model = M($name);
		$vo = $model->find($pid);
		$vo['userid']=explode(",",$vo['userid']);
		$this->assign("vo",$vo);
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
		$ifread = $this->parentRecursionAccess( intval($_REQUEST['pid']),"ifread");
		$ifprint = $this->parentRecursionAccess( intval($_REQUEST['pid']),"ifprint");
		$ifexecute = $this->parentRecursionAccess( intval($_REQUEST['pid']),"ifexecute");
		
		$this->assign("ifread",$ifread);
		$this->assign("ifprint",$ifprint);
		$this->assign("ifexecute",$ifexecute);
		
		if ($_REQUEST['jump']) {
			$this->display('indexview');
		}else{
			$this->display();
		}
	}

	public function _after_list( &$voList ){
		$FileUtil = new FileUtil();
		$MisFileManagerAccessDao = M("mis_file_manager_access");
		$extensionArr = C('TRANSFORM_SWF');
		$extensionIMG = C('IMG_file');
		$extensionArr = array_merge($extensionArr, $extensionIMG);
		$extensionArr[] = 'pdf';
		foreach($voList as $key=>$val){
			$file = "MisFileManager/".$val['filepath'];
			$voList[$key]["codeurl"] =base64_encode($file);
			if($val["type"]!=2){
				if($val["type"]==0){
					$voList[$key]['size'] = byteFormat(filesize(UPLOAD_PATH.$file));
					$fileinfo = pathinfo($val['uploadname']);
					if (in_array(strtolower($fileinfo['extension']), $extensionArr)) {
						$voList[$key]['isplay'] = C("FILE_OFFICEONLINE");//是否显示在线查看
					}
					$voList[$key]['ext'] = "文件";
				}else{
					$voList[$key]['size']=byteFormat($FileUtil->dirsize(UPLOAD_PATH.$file));
					$voList[$key]['ext'] = "文件夹";
				}
			}else{
				if($val["stype"]==1) $voList[$key]['ext'] ="文章";
			}
			$voList[$key]["ifread"] = $this->parentRecursionAccess($voList[$key]['id'],"ifread");
			$voList[$key]["ifprint"] = $this->parentRecursionAccess($voList[$key]['id'],"ifprint");
			$voList[$key]["ifexecute"] = $this->parentRecursionAccess($voList[$key]['id'],"ifexecute");
			
			//echo $ifread."__".$ifread."__".$ifread."<br>";
			//$byuserall = $MisFileManagerAccessDao->where('fid ='.$val['id'].' and userid = 0')->find();
			//$byself = $MisFileManagerAccessDao->where('fid ='.$val['id'].' and userid = '.$_SESSION[C('USER_AUTH_KEY')])->find();
			// 
			//if($byuserall["ifprint"] || $byself["ifprint"] ) $voList[$key]["ifprint"] = 1;
			//if($byuserall["ifread"] || $byself["ifread"] ) $voList[$key]["ifread"] = 1;
			//if($byuserall["ifexecute"] || $byself["ifexecute"]) $voList[$key]["ifexecute"] = 1;
		}
		// print_R($voList);
		 
	}

	public function _before_add(){
		//获取当前选中的文件ID
		$pid = $_GET['pid'];
		$name = $this->getActionName();
		$model = M($name);
		$vo = $model->getById($pid);
		$this->assign("pvo",$vo);
	}

	
	public function checkFiledoer( $filename ,$isfile=false){
		$name = $this->getActionName();
		$MisFileManagerModel = D($name);
		$map['parentid'] = $pid = $_POST['pid'];
		$map['type'] = 1;
		if($isfile){
			$map['type'] = 0;
			$pathinfoname = pathinfo($filename);
		}
		
		$has=true;
		$i=1;
		$mapname=$newname=$filename;
		while( $has ){
			$map['name'] =$mapname;
			$count=$MisFileManagerModel->where($map)->count("*"); 
			if($count){
				if($isfile){
					$mapname = $pathinfoname["basename"]."_".$i.".".$pathinfoname["extension"];
				}else{
					$mapname = $filename."_".$i;
				}
				$i++;
			}else{
				$has=false;
			}
		}
		return $mapname;
	}
	
	public function insert(){
		//获取创建的文件名
		$filename = $_POST['filename'];
		$pid = $_POST['pid'];
		$projectid=$_POST['projectid'];
		$remark = $_POST['remark'];
		$orderno=$_POST['orderno'];
		$position=$_POST['position'];
		$page=$_POST['page'];
		//实例化对象
		$name = $this->getActionName();
		$MisFileManagerModel = D($name);

		//判断项目是否已经关联
		$MisFileManagerList=$MisFileManagerModel->where(array('projectid'=>$projectid))->select();
		if($MisFileManagerList){
			$this->error("关联项目已经存在");
		}
		//判断改目录文件夹下是否已经存在
		$filename=$this->checkFiledoer($filename);
		//查询父类信息
		$list=$MisFileManagerModel->where('id ='.$pid.' and status = 1')->find();
		//定义指定路径
		$path = $list['filepath']."/".$filename;
		//获得新的文件别名
		$newfilename=$this->DefaultFilenameGen($path);
		//如果是顶级节点下面建文件夹。则指定文件夹是那个用户的
// 		if($list['parentid'] == 0){
// 			//定义指定路径
// 			$newpath = $list['filepath']."/".$_SESSION[C('USER_AUTH_KEY')]."/".$newfilename;
// 		}else{
			$newpath = $list['filepath']."/".$newfilename;
// 		}
		$data=array('name' =>$filename,
				'category'=>$list['category'],
				'type'=>1,
				'remark' => $remark,
				'orderno' => $orderno,
				'position' => $position,
				'page' => $page,
				'uploadname'=>$newfilename,
				'parentid'=>$pid,
				'filepath'=>$newpath,
				'createtime' =>time(),
				'createid'=>$_SESSION[C('USER_AUTH_KEY')],
				'projectid'=>$projectid
		);
		$MisFileManagerModel->data($data);
		$result=$MisFileManagerModel->add();
		if(!$list['category']){
			$data = array('id' => $result, 'category' => 0);
			$MisFileManagerModel->data($data);
			$MisFileManagerModel->save();
		}
		if($result===false){
			$this->error("创建失败");
		}else{
			$path=UPLOAD_PATH."MisFileManager/".$newpath;
			$this->createFolders($path);
			$this->success("创建成功");
		}
	}
	
	/*添加  参数  项目id，项目阶段id，项目阶段所需附件id 返回 文档id*/
	
	public function lookupInsertProjectFile($project,$project_phase,$attachid,$sourcefilepath){
		$MisFileManagerModel = D("MisFileManager");
		//判断改目录文件夹下是否已经存在
		$map['uploadname'] = "project_".$project;
		$map['parentid'] = 4;
		$map['type'] = 1;
		$proinfo=$MisFileManagerModel->where($map)->find();
		//如果没有创建项目文件夹 则创建
		if(  !$proinfo ){
			//创建项目文件夹
			$project_name = M("mis_sales_project")->where("id=".$project)->getField("name");
			$newpath = "projectfile/project_".$project;
			$data=array('name' =>$project_name,'category'=> 4,'type'=>1,'uploadname'=>"project_".$project,'parentid'=>4,'filepath'=>$newpath,'createtime' =>time(),'createid'=>$_SESSION[C('USER_AUTH_KEY')]);
			$proinfo["id"]=$MisFileManagerModel->add($data);
		}
		//判断阶段目录文件夹是否已经存在
		$map['uploadname'] = "phase_".$project_phase;
		$map['parentid'] = $proinfo["id"];
		$map['type'] = 1;
		$phaseinfo=$MisFileManagerModel->where($map)->find();
		if(  !$phaseinfo ){
			//创建阶段文件夹
			$phase_name = M("mis_project_stage_info")->where("id=".$project_phase)->getField("name");
			$newpath = "projectfile/project_".$project."/phase_".$project_phase;
			$data=array('name' =>$phase_name,'category'=> 4,'type'=>1,'uploadname'=>"phase_".$project_phase,'parentid'=>$proinfo["id"],'filepath'=>$newpath,'createtime' =>time(),'createid'=>$_SESSION[C('USER_AUTH_KEY')]);
			$phaseinfo["id"]=$MisFileManagerModel->add($data);
		}
		
		//判断该项目阶段性文件夹是否已经存在
		$map['uploadname'] = "attach_".$attachid;
		$map['parentid'] = $phaseinfo["id"];
		$map['type'] = 1;
		$attachinfo=$MisFileManagerModel->where($map)->find();
		if(  !$attachinfo ){
			//创建附件对应文件夹
			$attach_name = M("mis_project_stage_files")->where("id=".$attachid)->getField("filename");
			$newpath = "projectfile/project_".$project."/phase_".$project_phase."/attach_".$attachid;
			$data=array('name' =>$attach_name,'category'=> 4,'type'=>1,'uploadname'=>"attach_".$attachid,'parentid'=> $phaseinfo["id"],'filepath'=>$newpath,'createtime' =>time(),'createid'=>$_SESSION[C('USER_AUTH_KEY')]);
			$attachinfo["id"]=$MisFileManagerModel->add($data);
			//创建文件夹
			$this->createFolders(UPLOAD_PATH."MisFileManager/".$newpath);
		}
		if( !isset($attachinfo["filepath"]) ){
			$path = $newpath;
		}else{
			$path = $attachinfo["filepath"];
		}
		$sourceinfo   = pathinfo( $sourcefilepath );
		$sourcename   = $sourceinfo[ "basename" ];
		$newpath = $path."/".$sourcename;
		//获得新的文件别名
		$newfilename=$this->DefaultFilenameGen($newpath,0);
		$newpath = $path."/".$newfilename;
		$data = array();
		$data=array('name' =>$sourcename,
				'category'=>4,
				'type'=>0,
				'uploadname'=>$newfilename,
				'parentid'=>$attachinfo["id"],
				'filepath'=>$newpath,
				'createtime' =>time(),
				'createid'=>$_SESSION[C('USER_AUTH_KEY')]
		);
		$FileUtil = new FileUtil();
		$boolean=$FileUtil->moveFile($sourcefilepath,UPLOAD_PATH."MisFileManager/".$newpath ,true);
		$result=$MisFileManagerModel->add($data);
		return $result;
	}

	public function _after_edit(&$vo){
		if($vo["type"]!=1){
			$info   = pathinfo( $vo['name'] );
			$arr = explode(".",$vo["name"]);
			//文件后缀名
			$vo['ext'] = end($arr);
			array_pop ($arr);
			//文件名称
			$vo['name'] = implode(".",$arr);
		}
	}

	/**
	 * (non-PHPdoc)
	 * @author liminggang
	 * @Description: todo(修改文件名称)
	 * @date 2013-8-16 下午2:04:55
	 * @see CommonAction::update()
	 */
	public function update(){
		//获取要修改的ID
		$id=$_POST['id'];
		//获取修改的名称
		$filename = $_POST['filename'];
		$remark = $_POST['remark'];
		$orderno=$_POST['orderno'];
		$position=$_POST['position'];
		$page=$_POST['page'];
		$projectid=$_POST['projectid'];
		//获取后缀
		$ext = $_POST['ext'];
		if($ext){
			$filename = $filename.".".$ext;
		}
		//实例化模型
		$name=$this->getActionName();
		$MisFileManagerModel = D ($name);
		$where['projectid']=$projectid;
		$where['id']=array('neq',$id);
		//判断项目是否已经关联
		$MisFileManagerList=$MisFileManagerModel->where($where)->select();
		if($MisFileManagerList){
			$this->error("关联项目已经存在");
		}
		$list=$MisFileManagerModel->where('id ='.$id.' and status = 1')->find();
		if($list){
			//如果是修改文件夹名称。则要判断是否重名
			$map['parentid'] = $list['parentid'];
			$map['type'] = $list['type'];
			$map['id'] = array("neq",$list["id"]);
			$map['name'] = $filename;
			$map['status'] = 1;
			$count=$MisFileManagerModel->where($map)->count('*');
			if($count >0 ){
				$this->error("文件存在重名，请重新输入");
			}
				
			//这里直接修改数据名称。不改变文件夹名称。文件夹名称还是原来一样被转义了的文件夹
			$data = array('id' => $id, 'remark' => $remark,'orderno' => $orderno,'position' => $position,'page' => $page,'projectid'=>$projectid,'name'=>$filename,'updateid' =>$_SESSION[C('USER_AUTH_KEY')],'updatetime'=>time(),);
			$MisFileManagerModel->data($data);
			$result=$MisFileManagerModel->save();
			if($result===false){
				$this->error ( "文件名称修改失败");				
			}else{				
				$this->success ( "文件名称修改成功");
			}
		}else{
			$this->error("文件可能被删除！请刷新操作");
		}
	}

	/**
	 * (non-PHPdoc)
	 * @author liminggang
	 * @Description: todo(删除文件夹)
	 * @date 2013-8-16 下午2:04:55
	 * @see CommonAction::delete()
	 */
	public function delete(){
		//删除指定记录
		$name=$this->getActionName();
		$MisFileManagerModel = D ($name);
		if (! empty ( $MisFileManagerModel )) {
			$pk = $MisFileManagerModel->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
				//$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				//先查询出对应的数据,在进行删除。
				$map['id'] = array(' in ',explode ( ',', $id ) );
				$map['status'] = 1;
				$list=$MisFileManagerModel->where($map)->select();
				//print_r($list);exit;
				$MisFileManageraccess = M("mis_file_manager_access");
				$result = 0;
				if($list){
					//递归出所有下级节点
					$FileUtil = new FileUtil();
					$fileidarray = array();
					foreach($list as $k=>$v){
						$filelist=$MisFileManagerModel->where("category = ".$v['category']." and status = 1")->select();
						$fileidarray=array_unique(array_filter (explode(",",$this->downAllChildren($filelist,$v['id']))));
						$condition['id'] = array(" in ",$fileidarray);
						//在进行删除功能 
						$result=$MisFileManagerModel->where ( $condition )->setField ( 'status', - 1 );
						if(!$result){
							$this->error("删除失败");
						}
						$accesscondition = array();
						$accesscondition['fid'] = array(" in ",$fileidarray);
						$result2 = $MisFileManageraccess->where($accesscondition)->delete();
						if(!$result2){
							$this->error("删除对应文件授权失败");
						}
					}
					$this->success ( L('_SUCCESS_') );
				}else{
					$this->error ( "此文件已经被删除，请刷新操作" );
				}
			} else {
				$this->error ( C('_ERROR_ACTION_') );
			}
		}
	}

	/**
	 * @Title: reCursion
	 * @Description: todo(递归循环，修改数据文件结构)
	 * @param int $id	移动文件ID
	 * @param int $parentid  移动到文件ID
	 * @author liminggang
	 * @date 2013-8-19 下午4:43:21
	 * @throws
	 */
	private function reCursion($id,$parentid){
		//第一步、修改数据库
		$name=$this->getActionName();
		$MisFileManagerModel = D ($name);
		$movetolist=$MisFileManagerModel->where('id = '.$parentid.' and status = 1')->find();
		//获取移动的文件数据
		$movelist=$MisFileManagerModel->where('id = '.$id.' and status = 1')->find();
		if($movelist){
			if($movetolist){
				//不是文件夹，则直接判断重名问题
				if($movelist['type'] == 0){
					//取得移动到的路劲
					//$movefilelist = explode("|",$movelist['filepath']);
					//获得移动文件名称
					//$movefilename = $movefilelist[count($movefilelist)-1];
					//$movefilename = $movelist['name'];
					//获取转义的文件名
					$mvoefileuploadname = $movelist['uploadname'];
				}else{
					//移动类型为文件夹，则判断是否有重名文件夹。
					//$movefilename = $movelist['name'] ;
					//获取转义的文件名
					$mvoefileuploadname = $movelist['uploadname'];
				}
				//组合一个新的移动路径
				$nowfilepath=$movetolist['filepath']."/".$mvoefileuploadname;
				if($movetolist['parentid'] == 0){
					//如果是顶级节点。则需要加上用户ID
					$nowfilepath=$movetolist['filepath']."/".$_SESSION[C('USER_AUTH_KEY')]."/".$mvoefileuploadname;
				}
				//判断新路径是否存在
				$map['filepath'] = array('like',$nowfilepath);
				$map['status'] = 1;
				$map['parentid'] = $parentid;
				$newlist=$MisFileManagerModel->where($map)->find();
				//文件
				if($movelist['type'] == 0){
					if($newlist){
						//存在同名文件。则直接报错。不能移动
						$this->error ( "移动文件存在同名。不能移动!" );
					}else{
						//不同命。则修改父节点ID
						$data = array(
								'id'=>$id,
								'parentid'=>$parentid,
								'type'=>0,
								'filepath'=>$nowfilepath,
								'category'=>$movetolist['category'],
								'updatetime'=>time(),
								'updateid'=>$_SESSION[C('USER_AUTH_KEY')],
						);
						$MisFileManagerModel->data($data);
						$result=$MisFileManagerModel->save();
						if(!$result){
							$this->error ( "文件移动失败!" );
						}
					}
				}else{
					if($newlist){
						//删除移动的文件夹
						$result=$MisFileManagerModel->where('id = '.$id)->delete();
						if(!$result){
							$this->error ( "文件移动失败!" );
						}
					}else{
						//不存在同名文件夹。则直接修改父类ID
						$data = array(
								'id'=>$id,
								'parentid' =>$parentid,
								'type'=>1,
								'filepath'=>$nowfilepath,
								'category'=>$movetolist['category'],
								'updatetime'=>time(),
								'updateid'=>$_SESSION[C('USER_AUTH_KEY')],
						);
						$MisFileManagerModel->data($data);
						$result=$MisFileManagerModel->save();
						if(!$result){
							$this->error ( "文件移动失败!" );
						}
					}
					//存在同名文件夹。则判断文件夹下面是否还有文件。
					$nextList=$MisFileManagerModel->where('parentid = '.$id.' and status = 1')->select();
					if($nextList){
						foreach($nextList as $k=>$v){
							$pid = $newlist['id']?$newlist['id']:$v['parentid'];
							$this->reCursion($v['id'], $pid);
						}
					}
				}
			}else{
				$this->error ( "移动到文件夹被删除，请重新选择移动到文件夹" );
			}
		}else{
			$this->error ( "此文件已被删除，请刷新操作" );
		}
	}

	/**
	 * @Title: move
	 * @Description: todo(移动文件)
	 * @author liminggang
	 * @date 2013-8-16 下午3:29:40
	 * @throws
	 */
	public function move(){
		//表示执行操作还是执行页面跳转
		$step=$_POST['step'];
		//当前选中要移动的文件ID
		$id=$_REQUEST['id'];
		$this->assign('id',$id);
		//实例化对象
		$name = $this->getActionName();
		
		if($step){
			$MisFileManagerModel = D($name);
			//需要移动到的文件夹ID
			$parentid=$_POST['parentid'];
			if($id==$parentid){
				$this->success ( "文件移动成功" );exit;
			}
			//第一步、修改数据库
			$movetolist=$MisFileManagerModel->where('id = '.$parentid.' and status = 1')->find();
			//获取移动的文件数据
			$map=array();
			$map['id'] = array(' in ',explode ( ',', $id ) );
			$map['status']=1;
			$movelist=$MisFileManagerModel->where( $map )->select();
			$FileUtil = new FileUtil();
			foreach($movelist as $k=>$onemove){
				//进行递归修改数据库文件结构
				$this->reCursion($onemove['id'], $parentid);
	
				//不是文件夹，则直接判断重名问题
				if($onemove['type'] == 0){
					//获得移动文件名称
					$movefilename = $onemove['uploadname'];
				}else{
					//移动类型为文件夹，则判断是否有重名文件夹。
					$movefilename = $onemove['uploadname'] ;
				}
				//组合一个新的移动路径
				$nowfilepath=$movetolist['filepath']."/".$movefilename;
				if($movetolist['parentid'] == 0){
					$nowfilepath=$movetolist['filepath']."/".$_SESSION[C('USER_AUTH_KEY')]."/".$movefilename;
				}
				$nowfilepath = UPLOAD_PATH."MisFileManager/".$nowfilepath;
				$oldfilepath =$onemove['filepath'];
				$oldfilepath = UPLOAD_PATH."MisFileManager/".$oldfilepath;
				//实例化文件操作对象
				
				if($onemove['type'] == 1){
					//移动的是文件夹
					$boolean=$FileUtil->moveDir($oldfilepath,$nowfilepath);
					if( !$boolean){
						$this->error ( "文件夹[".$onemove['name']."]移动失败" );
					}
				}else{
					//移动的是文件
					$boolean=$FileUtil->moveFile($oldfilepath,$nowfilepath,true);
					if( !$boolean){
						$this->error ( "文件[".$onemove['name']."]移动失败" );
					}
				}
			}
			$this->success ( "文件移动成功" );
		}
		else{
		    $model = D("MisFileManager");
		    $managerid=array(1);
		    $sytlist=array();
		   
		    $arr[] = array("id"=>0,"pId"=>-1,"realid"=>0,"category"=>0,"name"=>"文档树形结构","open"=>true,"title"=>"文档树形结构");
		    //系统默认的文件夹
		    $map=array();
		    $map['type'] = 1;//文件夹
		    $map['status'] = 1; //状态为正常
		    $map['issystem'] = 1;//为系统文件
		    $map['category'] = array("neq",5); 
		    $sytlist = $model->where($map)->select();
		    $sytlist=array_merge($arr,$sytlist);
		    $map=array();
		    
		    //查询我的文件夹
		    $map['type'] = 1;
		    $map['status'] = 1;
		    $map['category'] = 1;
		    $map['issystem'] = 0;
		    $map['createid'] = $_SESSION[C('USER_AUTH_KEY')];
		    $list = $model->where($map)->select();
		    if($list){
			$sytlist=array_merge($sytlist,$list);
			foreach($list as $k=>$v){
			    array_push($managerid,$v["id"]);
			}
		    }
		    
		    //查询单位 公文
		    if( $_SESSION['a'] ){//如果是管理员不过滤
			unset($map);
			$map['type'] = 1;
			$map['status'] = 1;
			$map['category'] = array("neq",1);
			$map['issystem'] = 0;
			$list = $model->where($map)->select();
			if($list){
			    $sytlist=array_merge($sytlist,$list);
			}
			$this->assign('admin',1);
		    }
		    else{//找出对应 管理员 管理的所有文件		
			unset($map);
			$map['type'] = 1;
			$map['status'] = 1;
			$map['issystem'] = 0;
			$alllist = $model->where($map)->field("id,parentid,userid")->select();
			$downfid="";
			$upid="";
			$inid=array();
			foreach($alllist as $k=>$v){
			    if( $v["userid"]==$_SESSION[C('USER_AUTH_KEY')] ){
				$downfid.= $this->downAllChildren($alllist,$v["id"]);
				$upid.=$this->parentRecursion($v["id"]);
			    }
			}
			if($downfid){
			    $downfid = explode(",",$downfid);
			    array_shift ($downfid);
			    $managerid= array_merge( $managerid,$downfid);
			}
			
			if($upid){
			    $upid = explode(",",$upid);
			    array_shift ($upid);
			}
			if($downfid || $upid){//过滤管理员文件的id
			    $inid=array_merge($upid,$downfid);
			}
			if($inid){
			    $map=array();
			    $map['id'] = array(' in ',$inid);
			    $map['status'] = 1;
			    $map['type'] = 1;
			    $list = $model->where($map)->select();
			    $sytlist=array_merge($sytlist,$list);
			}
			$this->assign('admin',0);
		    }
		     
		    $fidarr=array(); 
		    foreach($sytlist as $k=>$v){
			if( !in_array($v["id"],$fidarr) ){
			    $sytlist[$k]["realid"]=$v["id"];
			    $sytlist[$k]["id"]=$k;
			    $fidarr[]= $v["id"];
			}else{
			    unset($sytlist[$k]); 
			}
		    }
		    $typeTree = $this->doTree($sytlist);
		    $typeTree = $this->getTree($sytlist,$param);
		    //$this->assign('documentTree',$typeTree);
		    $this->assign('documentTree',json_encode($typeTree));
		    $managerid=array_unique($managerid);
		    $this->assign( 'managerid',json_encode($managerid) );
		    $this->display();
		}
	}
	/**
	 * @Title: uploade
	 * @Description: todo(上传方法)
	 * @author liminggang
	 * @date 2013-8-19 下午5:05:33
	 * @throws
	 */
	public function uploade(){
		//获得上传的父类ID
		$pid=$_REQUEST['pid'];
		$remark=$_POST['remark'];
		$orderno=$_POST['orderno'];
		$position=$_POST['position'];
		$page=$_POST['page'];
		$this->assign('pid',$pid);
		if($_POST['step']){
			//根据上传父类ID查询其他信息
			$name=$this->getActionName();
			$MisFileManagerModel = D ($name);
			$list=$MisFileManagerModel->where('id='.$pid.' and status = 1')->find();
			if(!$list){
				$this->error("获取目标文件路径失败");
			}
			$save_file=$_POST['swf_upload_save_name'];
			$source_file=$_POST['swf_upload_source_name'];
			//临时文件夹里面的文件转移到目标文件夹
			foreach($save_file as $k=>$v){
				if($v){
					foreach($v as $kkk => $vv){
						$filename=$this->checkFiledoer($source_file["swf_upload_save_name"][$kkk],true);
						$fileinfo=pathinfo($vv);
						$from = UPLOAD_PATH_TEMP.$vv;//临时存放文件
						if( file_exists($from) ){
							$p=UPLOAD_PATH."MisFileManager/".$list['filepath'];// 目标文件夹
							if( !file_exists($p) ) $this->createFolders($p); //判断目标文件夹是否存在
							$to = $p."/".$fileinfo['basename'];
							rename($from,$to);
							$data = array(
									'name'=>$filename,
									'category'=>$list['category'],
									'type'=>0,
									'remark' => $remark,
									'orderno' => $orderno,
									'position' => $position,
									'page' => $page,
									'userid'=>$_SESSION[C('USER_AUTH_KEY')],
									'uploadname'=>$fileinfo['basename'],
									'parentid'=>$pid,
									'filepath'=>$list['filepath']."/".$fileinfo['basename'],
									'createid'=>$_SESSION[C('USER_AUTH_KEY')],
									'createtime'=>time(),
							);
							
							//保存数据
							$MisFileManagerModel->data($data);
							$result=$MisFileManagerModel->add();
							if(!$result) {
								$this->error( "文件数据保存失败");
							}
	// 						else{
	// 							$filesArr = C ('TRANSFORM_SWF');
	// 							//文件路径
	// 							$dir_path = UPLOAD_PATH."MisFileManager/".$list['filepath'];
	// 							$file_path = UPLOAD_PATH."MisFileManager/" . $data['filepath'];
	// 							import ( '@.ORG.OfficeOnline.OfficeOnlineView' );
	// 							$OfficeOnlineView= new OfficeOnlineView();
	// 							$extension = strtolower($fileinfo['extension']);
	// 							if (in_array($extension, $filesArr)) {
	// 								//生成 PDF /swf文件
	// 								$OfficeOnlineView->fileCreate($dir_path, $file_path, 'swf/pdf');
	// 							}
	// 							if ('pdf' == $extension) {
	// 								//生成swf文件
	// 								$OfficeOnlineView->fileCreate($dir_path, $file_path, 'swf');
	// 							}
	// 							if(!$result){
	// 								$this->error("上传失败");
	// 							}
	// 						}
						}
					}
				}
			}
			$this->success("上传成功");
		}else{
			$this->display();
		}
	}

	//向上递归
	public function parentRecursion($parentid,$hasparentsid=true){
		$arr="";
		$MisFileManagerDao = M("mis_file_manager");
		//递归父节点
		$fatherlist=$MisFileManagerDao->where('id= '.$parentid.' and issystem=0')->field('id,parentid')->find();
			
		if( $fatherlist  ){
			$twolist=$MisFileManagerDao->where('id ='.$fatherlist['parentid'].' and issystem = 0')->field('id,parentid')->find();
			if( $hasparentsid )  $arr .=",".$fatherlist['id'];
			if($twolist){
				$arr.=",".$this->parentRecursion($fatherlist['parentid'],true);
			}
		}
		return $arr;
	}
	//向上递归权限
	public function parentRecursionAccess($parentid,$type="ifread"){
		$arr=0;
		$MisFileManagerDao = M("mis_file_manager");
		//递归父节点
		$fatherlist=$MisFileManagerDao->where('id= '.$parentid.' and issystem=0')->field('id,parentid,userid')->find();
		//if( $fatherlist['userid']==$_SESSION[C('USER_AUTH_KEY')] ){
		//	$arr = $parentid;//管理员
		//}else{
			if( $fatherlist  ){
				$MisFileManagerAccessDao = M("mis_file_manager_access");
				//父级授权
				$twolist=$MisFileManagerAccessDao->where('fid ='.$parentid.' and ( userid = 0 or userid = '.$_SESSION[C('USER_AUTH_KEY')].")")->sum($type);
				if($twolist>0){
					$arr= $parentid;
				}else{
					$arr = $this->parentRecursionAccess($fatherlist['parentid'],$type);
				}
			}
		//}
		return $arr;
	}
	//向下递归
	private function childRecursion($id){
		//实例化对象
		$MisFileManagerDao = M("mis_file_manager");
		//递归子节点
		$childlist=$MisFileManagerDao->where('parentid= '.$id)->field('id,type,parentid')->select();
		if($childlist){
			foreach($childlist as $key=>$val){
				if($val['type']==1){
					$childlst=$this->childRecursion($val['id']);
					if($childlst){
						$childlist=array_merge($childlist,$childlst);
					}
				}
			}
		}
		return $childlist;
	}

	/**
	 * @Title: lookupWriteAss
	 * @Description: todo(设置共享权限)
	 * @author liminggang
	 * @date 2013-8-23 上午11:58:52
	 * @throws
	 */
	public function lookupWriteAss(){
		$id=$_REQUEST['id'];//文件ID
		$ifread = $this->parentRecursionAccess( $id,"ifread");
		$ifprint = $this->parentRecursionAccess( $id,"ifprint");
		$ifexecute = $this->parentRecursionAccess( $id,"ifexecute");
		$this->assign("ifread",$ifread);
		$this->assign("ifprint",$ifprint);
		$this->assign("ifexecute",$ifexecute);
		
		$this->assign('id',$id);
		//实例化对象
		$MisFileManagerAccessModel = D("mis_file_manager_access");
		//标志是否为插入
		if($_POST['step']){
			$idarr = $_POST["idarr"];
			$userid	= $_POST['userid'];//获得用户ID
			$browse = $_POST['browse'];//获得浏览权限
			$ifread = $_POST['ifread'];//获得读取权限
			$ifprint = $_POST['ifprint']; //获得打印权限
			$ifexecute = $_POST['ifexecute']; //获取执行权限
			if($idarr){
				foreach($userid as $k=>$v){
					if(!in_array($v,$idarr)){
						unset($userid[$k]);
						unset($browse[$k]);
						unset($ifread[$k]);
						unset($ifprint[$k]);
						unset($ifexecute[$k]);
					}
				}
			}
				
			//这里需要递归查询所有父节点和子节点，排除系统节点
			$MisFileManagerDao = M("mis_file_manager");
			$lst=$MisFileManagerDao->find($id);
			$category=$lst["category"];

			//获得老用户
			$arrUserid=$_POST['arrUserid'];
			//获得原来的用户ID
			$useridlist =explode(",",$arrUserid);
			//比较2个传过来的userId 判断是否有数据变更
			$compareUserid = array_diff($userid,$useridlist);//返回的是新增的用户
			//查询原有绑定的权限
			$map['userid'] = array(' in ',$useridlist);
			$map['fid'] = $id;
			$map['status'] = 1;
			$list=$MisFileManagerAccessModel->where($map)->select();
			if($list){
				if($userid){
					$cannelUserid = array_diff($useridlist,$userid);//返回的是取消之前的用户
				}else{
					$cannelUserid = $useridlist;
				}
				$updateUserid = array_intersect($useridlist,$userid);
				foreach($list as $k=>$v){
					//判断查询出来的节点中，是否存在此人
					if( $v["createid"] !=$_SESSION[C('USER_AUTH_KEY')] ) continue;
					if( in_array($v['userid'],$updateUserid)  ){
						//存储已有的节点
						//$nodearr[$v['userid']][$v['fid']] = 1;
						//如果当前用户在提交过来的数据中。则是修改
						$data = array(
								'id'=>$v['id'],
								'fid'=>$v['fid'],
								'userid'=>$v['userid'],
								'browse'=>$browse[$v['userid']]? 1:0,
								'ifread'=>$ifread[$v['userid']]? 1:0,
								'ifprint'=>$ifprint[$v['userid']]? 1:0,
								'ifexecute'=>$ifexecute[$v['userid']]? 1:0,
								'updateid'=>$_SESSION[C('USER_AUTH_KEY')],
								'updatetime'=>time(),
						);
						$MisFileManagerAccessModel->data($data);
						$result=$MisFileManagerAccessModel->save();
						if(!$result){
							$this->error("共享设置失败");
						}
					}else if(in_array($v['userid'],$cannelUserid)){
						//如果提交来的数据不在原有的数据中。则需要删除原有数据
						$result=$MisFileManagerAccessModel->where("id=".$v["id"])->delete();
						if(!$result){
							$this->error("共享设置失败");
						}
					}
				}
			}
			 
			if($compareUserid){
				$data = array(); //保存插入所有数据
				foreach($compareUserid as $key=>$val){
					$data[] = array(
							'fid'=>$id,
							'category'=>$category,
							'userid'=>$val,
							'browse'=>$browse[$val]? 1:0,
							'ifread'=>$ifread[$val]? 1:0,
							'ifprint'=>$ifprint[$val]? 1:0,
							'ifexecute'=>$ifexecute[$val]? 1:0,
							'createid'=>$_SESSION[C('USER_AUTH_KEY')],
							'createtime'=>time(),
					);
				}
				$MisFileManagerAccessModel->data($data);
				$result=$MisFileManagerAccessModel->addAll($data);
				if(!$result){
					$this->error("共享设置失败");
				}
			}
			$this->success("共享设置成功");
		}else{
			$MisFileManagerDao = M("mis_file_manager");
			$vo=$MisFileManagerDao->find($id);
			$this->assign('vo',$vo);
			
			$list=$MisFileManagerAccessModel->where('fid ='.$id.' and status = 1')->select();
			$this->assign('list',$list);
			//这里将原有的共享人员分离出来
			$arrUserid ="";
			$alluser=array();
			foreach($list as $key=>$val){
				if($val['userid']==0){
					$alluser=$val;
				}
				$arrUserid .=$val['userid'].",";
			}
			if($arrUserid){
				$arrUserid = substr($arrUserid, 0, -1);
			}
			$this->assign('alluser',$alluser);
			$this->assign('arrUserid',$arrUserid);
			$this->display();
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
		$userid=$_SESSION[C('USER_AUTH_KEY')];
		$viewModel=D('MisHrPersonnelUserDeptView');
		$companyList=$viewModel->where('user.id='.$userid)->field('companyid')->select();
		foreach ($companyList as $comk=>$comv){
			$companyArr[]=$comv['companyid'];
		}
		//构造左侧部门树结构
		$model= M('mis_system_department');
		$departmentmap['status']=1;
		if(!empty($companyList) && $userid!=1){
			$departmentmap['companyid']=array('in',$companyArr);
		}
		$deptlist = $model->where($departmentmap)->order("id asc")->select();
		$param['rel']="misFileManagerGetDeptBox";
		$param['url']="__URL__/lookupmanage/jump/1/deptid/#id#/parentid/#parentid#/companyid/#companyid#";
		$common = A('Common');
		$typeTree = $common->getTree($deptlist,$param);
		//获得树结构json值
		$this->assign('tree',$typeTree);
		$map = array();
		$keyword	= $_POST['keyword'];
		$searchby = str_replace("-", ".", $_POST["searchby"]);
		if ($keyword) {
			if($searchby =="all"){
				$MisSystemDepartmentModel=D("MisSystemDepartment");
				$deptMap=array();
				$deptMap['name']=array('like','%'.$keyword.'%');
				$deptMap['status']=1;
				$MisSystemDepartmentList=$MisSystemDepartmentModel->where($deptMap)->getField("id,name");
				if($MisSystemDepartmentList){
					$where['deptid']=array("in",array_keys($MisSystemDepartmentList));
				}
				$MisSystemDutyModel=D("MisSystemDuty");
				$dutyMap=array();
				$dutyMap['name']=array('like','%'.$keyword.'%');
				$dutyMap['status']=1;
				$MisSystemDutyList=$MisSystemDutyModel->where($dutyMap)->getField("id,name");
				if($MisSystemDutyList){
					$where['dutyid']=array("in",array_keys($MisSystemDutyList));
				}
				$where['user.name']=array('like','%'.$keyword.'%');
				$where['_logic']='OR';
				$map['_complex'] = $where;
			}else{
				if($searchby=="mis_system_department.name"){
					$MisSystemDepartmentModel=D("MisSystemDepartment");
					$deptMap=array();
					$deptMap['name']=array('like','%'.$keyword.'%');
					$deptMap['status']=1;
					$MisSystemDepartmentList=$MisSystemDepartmentModel->where($deptMap)->getField("id,name");
					$map['deptid']=array("in",array_keys($MisSystemDepartmentList));
				}else if($searchby=="duty.name"){
					$MisSystemDutyModel=D("MisSystemDuty");
					$dutyMap=array();
					$dutyMap['name']=array('like','%'.$keyword.'%');
					$dutyMap['status']=1;
					$MisSystemDutyList=$MisSystemDutyModel->where($dutyMap)->getField("id,name");
					$map['dutyid']=array("in",array_keys($MisSystemDutyList));
				}else{
					$map[$searchby]=array('like','%'.$keyword.'%');
				}
			}
			$searchby = str_replace(".", "-", $_POST["searchby"]);
			if($searchby=='name'){
				$placeholder="搜索姓名";
			}
			if($searchby=='mis_system_department-name'){
				$placeholder="搜索部门";
			}
			if($searchby=='duty-name'){
				$placeholder="搜索职级";
			}
			if($searchby=='all'){
				$placeholder="搜索员工姓名,部门,职级";
			}
			$this->assign('placeholder', $placeholder);
			$this->assign('searchby', $searchby);
			$this->assign('keyword', $keyword);
		}
		$map['working']=1;	//在职状态
		$map['user.status']=1;	//后台用户正常状态
		$map['_string'] = "user.id<>'' or user.id<>0";
		$map['user_dept_duty.status']=1;
		if($_REQUEST['companyid']!=null){
			$map['companyid']=$_REQUEST['companyid'];
		}else{
			$map['companyid']=$deptlist[0]['companyid'];
		}
		$deptid		= $_REQUEST['deptid'];	//获得部门节点
		$parentid	= $_REQUEST['parentid'];	//获得父类节点
		$companyid	= $_REQUEST['companyid'];
		if ($deptid && $parentid) {
			$deptlist =array_unique(array_filter (explode(",",$this->downAllChildren($deptlist,$deptid))));
			$map['deptid'] = array(' in ',$deptlist);
		}
		$multi = intval($_REQUEST['multi']) ? 1:0;
		$common->_list('MisHrPersonnelUserDeptView',$map,'id',true);
		$this->assign('deptid',$deptid);
		$this->assign('parentid',$parentid);
		$this->assign('companyid',$companyid);
		$this->assign('multi',$multi);
		if ($_REQUEST['jump']) {
			$this->display('lookupmanagelist');
		} else {
			$this->display("lookupmanage");
		}
	}

	function lookupsetfileManager(){
		$name=$this->getActionName();
		$model = D ( $name );
		$id = $_REQUEST ["id"];
		$map['id']=$id;
		$vo = $model->where($map)->find();
		$step = $_POST["step"];
		if($vo["category"]==1 || $vo["category"]==5){//排除共享和我的文档以及记录和文件
			$this->error ( "不能对[".$vo["name"]."]设置管理员" );
		}
		if($step){
			$userid=$_POST["userid"];
			$result = $model->where($map)->setField("userid", $userid);
			if (false !== $result) {
				$this->success ( L('_SUCCESS_'));
			} else {
				$this->error ( L('_ERROR_') );
			}
		}else{
			$map=array();
			$map["status"]=1;
			$map["type"]=1;
			$map["issystem"]=0;
			$alllist = $model->where($map)->field("id,parentid,userid")->select();
			$downfid="";
			$upid="";
			$inid=array();
			foreach($alllist as $k=>$v){
				if( $v["userid"]==$_SESSION[C('USER_AUTH_KEY')] ){
					$downfid.= $this->downAllChildren($alllist,$v["id"],false);
				}
			}
			$allow=0;
			if($downfid){
				$downfid = explode(",",$downfid);
				array_shift ($downfid);
				$downfid = array_unique($downfid);
				if(in_array($id,$downfid)){
					$allow=1;
				}
				$this->assign("allow",$allow);
			}
			$this->assign( 'vo', $vo );
			$this->display ("setfileManager");
		}
	}
	
	/**
	 * @Title: lookupmanage
	 * @Description: todo(用ztree形式查询出所有部门员工信息。)
	 * @author liminggang
	 * @throws
	*/
	function lookupgetNewestFile( $num=10,$userid=0 ,$myself=false){
		if($userid===0){
			if($_SESSION[C('USER_AUTH_KEY')]){
			    $userid=$_SESSION[C('USER_AUTH_KEY')];
			}else{
				$this->error("非法访问!");
			}
		}
		$returnlist = array();
		if( $num >0 ){
			//1查询管理员所管理的文件id
			$model =M("MisFileManager");
			$alllist = $model->field("id,parentid,userid")->select();
			$downfid="";
			$upid="";
			$inid=$managerfid=array();
			foreach($alllist as $k=>$v){
				if( $v["userid"]== $userid){
					$downfid.= $this->downAllChildren($alllist,$v["id"]);
					$upid.=$this->parentRecursion($v["id"]);
				}
			}
			if($downfid){
				$managerfid = explode(",",$downfid);
				array_shift ($managerfid);
				$this->assign ( 'managerfid', $managerfid );
			}

			//2找出对应授权了的文件id
			$mapaccess['userid'] = array("exp","=0 OR `userid`=".$_SESSION[C('USER_AUTH_KEY')]);
			$accessModel = M("mis_file_manager_access");
			$accessfidarr=$accessModel->where($mapaccess)->getField('fid',true);
			foreach($accessfidarr as $k=>$v){
				$downfid.= $this->downAllChildren($alllist,$v);
				$upid.=$this->parentRecursion($v);
			}
			
			if($downfid){
				$downfid = explode(",",$downfid);
				array_shift ($downfid);
			}
			if($upid){
				$upid = explode(",",$upid);
				array_shift ($upid);
			}
			if($downfid || $upid){//过滤管理员文件的id
				$inid=array_merge($upid,$downfid);
			}
			//过滤id条件
			$map["type"]=array("neq",1);
			$map["id"]=array("in",$inid);
			$map["status"]=1;
			if($myself){
				$map["category"]=1;
				$map["createid"]=$userid;
			}
			$returnlist = $model->limit($num)->where($map)->order('id desc')->select();
			foreach($returnlist as $key=>$val){
				$returnlist[$key]["ifread"] = $this->parentRecursionAccess($returnlist[$key]['id'],"ifread");
				$returnlist[$key]["ifprint"] = $this->parentRecursionAccess($returnlist[$key]['id'],"ifprint");
				$returnlist[$key]["ifexecute"] = $this->parentRecursionAccess($returnlist[$key]['id'],"ifexecute");
			}
		}
		return $returnlist;
	}
}
?>