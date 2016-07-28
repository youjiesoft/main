<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: 系统配置读写类
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-9-12 上午10:35:20 
 * @version V1.0
 */
class SystemConfigDetailModel extends CommonModel {
	protected $autoCheckFields = false;
	
	/**
	 * @Title: setDetail
	 * @Description: todo(获取动态配置的明细返回值)
	 * @param  $modelname      string 模型对象名称
	 * @param  $data           array 数据设置
	 * @param  $typename       string 匹配类型,只支持boolean型的
	 * @return $returnData array  返回数组
	 * @author 杨希
	 * @date 2014-4-15 上午10:00:00
	 * @throws
	 */	
	public function setDetail($modelname,$data=array(),$typename=""){
        $filename=$this->getDynamicFileName($typename);
         //配置文件路径
		$filepath=DConfig_PATH . "/Models/".$modelname;
		if (!is_dir( $filepath )) {
			$this->make_dir( $filepath ,0755);
		}
		if($filename =="list.inc.php"){
			$this->writeover($filepath."/".$filename,"return ".$this->pw_var_export($data).";\n",true);
		}
		//针对具体人员配置到缓存中去
		$filename_user = $filename.$_SESSION[C('USER_AUTH_KEY')].".php";		
		$cachepath= C("DATA_CACHE_PATH")."Dynamicconf/Models/".$modelname;
		$cachedata=array();
		foreach($data as $k=>$v){
			if($v['shows']==1){
				$cachedata[]=$v;
			}
		}
		//是否存在配置文件
		if( file_exists( $filepath."/".$filename ) ){
			//是否存在cache缓存，存在则删除里面的数据,不存在则创建文件夹
			if(file_exists($cachepath)){
				$obj_dir = new Dir;
				$obj_dir->del($cachepath);
			}else{
			    $isdir=$this->make_dir($cachepath);
			}
		}
		$this->writeover($cachepath."/".$filename_user,"return ".$this->pw_var_export($cachedata).";\n",true);
	}
// 	public  function file_put_listinc($input,$t = null){
// 		$output = '';
// 		if (is_array($input))
// 		{
// 			$output .= "array(\r\n";
// 			foreach ($input as $key => $value)
// 			{
// 				$output .= $t."\t".$this->file_put_listinc($value['name'],$t."\t").' => '.  $this->file_put_listinc($value,$t."\t");
// 				$output .= ",\r\n";
// 			}
// 			$output .= $t.')';
// 		} elseif (is_string($input)) {
// 			$output .= "'".str_replace(array("\\","'"),array("\\\\","\'"),$input)."'";
// 		} elseif (is_int($input) || is_double($input)) {
// 			$output .= "'".(string)$input."'";
// 		} elseif (is_bool($input)) {
// 			$output .= $input ? 'true' : 'false';
// 		} else {
// 			$output .= 'NULL';
// 		}
	
// 		return $output;
// 	}
	
	/**
	 * @Title: setSubDetail
	 * @Description: todo(获取动态配置的明细返回值)
	 * @param  $modelname      string 模型对象名称
	 * @param  $data          array 数据设置
	 * @author 杨希
	 * @date 2014-4-15 上午10:00:00
	 * @throws
	 */
	public function setSubDetail($modelname,$data=array()){
		$filename="sublist.inc.php";
		//文件路径
		$filepath=DConfig_PATH . "/Models/".$modelname;
		if (!is_dir( $filepath )) {
			$this->make_dir($filepath,0777);
		}
		$this->writeover($filepath."/".$filename,"return ".$this->pw_var_export($data).";\n",true);
		//针对具体人员配置到缓存中去
		$filename_user = $filename.$_SESSION[C('USER_AUTH_KEY')].".php";
		$cachepath= C("DATA_CACHE_PATH")."Dynamicconf/Models/".$modelname;
		$cachedata=array();
		foreach($data as $k=>$v){
			if($v['shows']==1){
				$cachedata[]=$v;
			}
		}
		//是否存在配置文件
		if( file_exists( $filepath."/".$filename ) ){
			//是否存在cache缓存，存在则删除里面的数据
			if(file_exists($cachepath)){
				$obj_dir = new Dir;
				$obj_dir->del($cachepath);
			}else{
			    $isdir=$this->make_dir($cachepath);
			}
		}
		$this->writeover($cachepath."/".$filename_user,"return ".$this->pw_var_export($cachedata).";\n",true);
	}
	
	/**
	 * 
	 * @param importDataGetDetail 导入获取list信息
	 *@param  $modelname 模型名
	 * @param unknown_type $type 类型 1:list.inc 2:datatable.inc(内嵌表的inc)
	 * @param unknown_type $typename list.inc.php(默认) view：MisAutoXzhheSubDatatable9.inc.php
	 * @param unknown_type $listCongfigControl
	 * @param unknown_type $ismore
	 * @return number
	 */
	public function importDataGetDetail($modelname = '',$type='1',$typename='',$listCongfigControl="shows",$ismore=false){
		if($type=='1'){
			$listInfo = require $this->GetDetailFile($modelname,false,$typename,$listCongfigControl);
		}else{
			$listInfo= require $this->GetDetailFile($modelname,false,$typename='SubDatatable',$listCongfigControl,$type);
		}
		sortArray($listInfo,'sortnum','asc');
		$listInfo1=array();
		$listInfo2=array();
		foreach ($listInfo as $lk=>$lv){
			if($lv['shows']==1){
				$listInfo1[]=$lv;
			}else{
				$listInfo2[]=$lv;
			}
		}
		$listInfo=array_merge($listInfo1,$listInfo2);
		foreach ($listInfo as $infok=>$infov){
			if($infov['name']!='id' && $infov['fieldcategory']!='datatable'){
				$list[$infok]['name']=$infov['name'];
				$list[$infok]['showname']=$infov['showname'];
				$list[$infok]['transform']=$infov['transform'];//是否转换 0否 1转换
				$list[$infok]['unique']=$infov['unique'];//是否唯一  0否 1唯一
				$list[$infok]['validate']=$infov['validate'];//验证类型
				if($infov['required']=='1'){
					$required=1;
				}else{
					$required=0;
				}
				$list[$infok]['required']=$required;//是否必填   0否 1必填
				$list[$infok]['unfunc']=$infov['unfunc']; //转换函数  
				$list[$infok]['unfuncdata']=$infov['unfuncdata']; //转换数据
				
			}
		}
		return $list;
	}
	
	/**
	 * @Title: getDetail
	 * @Description: todo(获取动态配置的返回值)
	 * @param  $modelname      string 模型对象名称
	 * @param  $cache          boolean 是否调用缓存
	 * @param  $typename       string 匹配类型,只支持boolean型的
	 * @param $ismore bool 是否添加更多按钮
	 * @return $returnData array  返回数组
	 * @author 杨希
	 * @date 2014-4-15 上午10:00:00
	 * @throws
	 */
     public function getDetail($modelname = '',$cache=true,$typename='',$sort='sortnum',$listCongfigControl="shows",$ismore=false) {
		if ($modelname) {
			/*
			 * 第一步，验证文件是否存在，如果不存在一样返回空数组
			 * typename参数如果为空，则默认获取list.inc.php文件 ，其他参数请参照getDynamicFileName方法内的参数说明
			 */
			if (file_exists($this->GetDetailFile($modelname,false,$typename,$listCongfigControl))) {
				if($_SESSION['a'] || $_REQUEST["detailadll"] || $cache==false){
					$list = require $this->GetDetailFile($modelname,$cache,$typename,$listCongfigControl);
				} else {
					if($typename == "toolbar"){
						$list = require $this->GetDetailFile($modelname,$cache,$typename,$listCongfigControl);
					}else{
						// 第一步取个人缓存的值,判断如果没有再取公共的值
						$mMisRuntimeData = D("MisRuntimeData");
						$runtimeData = $mMisRuntimeData->getRuntimeCache($modelname,'detailList');
						if(empty($runtimeData)){
							// 取公共的值
							$list = require $this->GetDetailFile($modelname,$cache,$typename,$listCongfigControl);
						} else {
							// 取个人缓存的值 并过滤掉不显示的数据
							$list = array();
							foreach ($runtimeData as $k => $v) {
								//if($v['shows']==1){
									$list[]=$v;
								//}
							}
						}
					}
				}
				$list = array_sort($list, $sort);
				foreach ($list as $k => $v) {
					if($typename == "toolbar"){
						//过滤配制不显示的toolbar
						$file = DConfig_PATH . '/System/unsettoolbar.php';
						if(file_exists($file)){
							//引入按钮过滤配制文件
							$unsettoolbar = require $file;
							if(in_array($k, $unsettoolbar)){
								unset($list[$k]);
								continue;
							}
						}
						/*
						 * 此处进行判断。如果是项目查看过来的。过滤掉新增按钮
						 */
						if($_REQUEST['projectid'] && $_REQUEST['projectworkid'] && $k == "js-add"){
							unset($list[$k]);
							continue;
						}
						$extendurl = $v["extendurl"];
						if($extendurl){
							@eval("\$extendurl =".$extendurl.";");
							$list[$k]['html'] = str_replace( "#extendurl#",$extendurl,$list[$k]['html'] );
						}
					}else{
// 						if(($v['name'] == 'action' || $v['name'] == 'status')&& !$_SESSION['a']){
						if($v['name'] == 'status' && !$_SESSION['a']){
							unset($list[$k]);
						}
					}
				}
				if($ismore){
					//将ismore属性不为空的元素提出来做成更多按钮
					$listextend = array();
					foreach($list as $key=>$val){
						if($val['ismore']){
							if($_SESSION['a'] == 1 || $val['ifcheck'] == 0 || ($val['ifcheck'] == 1 && !empty($val['permisname']) && $_SESSION[$val['permisname']])){
								$listextend[$key]=$val;
							}
							unset($list[$key]);
						}
					}
					if(count($listextend)>0){
						$ismorearr['ifcheck'] = 0;
						$ismorearr['html'] = '<a class="js-ismore ismore tml-btn tml_look_btn tml_mp" title="更多"  onclick="ismore(this)" href="javascript:;" ><span class="icon_lrp">更多</span><span class="icon-sort"></span></a>';
						$ismorearr['html'] .= '<div class="top_drop_lay more_ismore">';
						foreach($listextend as $k=>$v){
							$ismorearr['html'] .=$v['html'];
						}
						$ismorearr['html'] .='<div>';
						$list['js-ismore'] = $ismorearr;
					}
				}
// 				dump($list['js-delete']);
				return $list;
			} else {
				return '';
			}
		} else {
			return '';
		}
	}
	/**
	 * @Title: getEmbedDetail
	 * @Description: todo(获取内嵌表格配置文件方法，暂时用此方法。) 
	 * @param 模型名称 $modelname 放置内嵌表格配置文件的模型名称
	 * @param 内嵌表配置文件名 $filename  
	 * @author 黎明刚
	 * @date 2015年1月13日 下午4:34:59 
	 * @throws
	 */
	public function getEmbedDetail($modelname='',$filename=''){
		$newlist = array();
		if(file_exists(DConfig_PATH . "/Models/".$modelname."/".$filename.".inc.php")){
			$list = require DConfig_PATH . "/Models/".$modelname."/".$filename.".inc.php";
			$sort = array();
			//处理内嵌表顺序问题
			foreach ($list as $key=>$val){
				if($val['shows']){
					$newlist[$key]=$val;
					$sort[] = $val['sortnum'];
				}
			}
			array_multisort($sort, SORT_ASC, $newlist);
		}
		return $newlist;
	}
	
	/**
	 * @Title: getSubDetail
	 * @Description: todo(获取动态配置的明细返回值)
	 * @param  $modelname      string 模型对象名称
	 * @param  $cache          boolean 是否调用缓存 
	 * @param  $typename       string 匹配类型,只支持boolean型的
	 * @return $returnData array  返回数组
	 * @author 杨希
	 * @date 2014-4-15 上午10:00:00
	 * @throws
	 */
	public function getSubDetail($modelname = '',$cache=true,$typename='',$sort='sortnum') {
		if ($modelname) {
			if (file_exists($this->GetSubDetailFile($modelname,false,$typename))) {
				$list = require $this->GetSubDetailFile($modelname,$cache,$typename);
				if($_SESSION['a'] || $_REQUEST["detailadll"]){
					$list = require $this->GetSubDetailFile($modelname,$cache,$typename);
				} else {
					// 第一步取个人缓存的值,判断如果没有再取公共的值
					$mMisRuntimeData = D("MisRuntimeData");
					$runtimeData = $mMisRuntimeData->getRuntimeCache($modelname,'subDetailList');
					if(empty($runtimeData)){
						// 取公共的值
						$list = require $this->GetSubDetailFile($modelname,$cache,$typename);
					} else {
						// 取个人缓存的值 并过滤掉不显示的数据
						$list = array();
						foreach ($runtimeData as $k => $v) {
							if($v['shows']==1){
								$list[]=$v;
							}
						}
					}
				}
				$list = array_sort($list, $sort);
				return $list;
			} else {
				return '';
			}
		} else {
			return '';
		}
	}
	/**
	 * @Title: getTitleName
	 * @Description: todo(获取动态配置的文件名称)
	 * @param  $modelname      string 模型对象名称
	 * @return $title          string 返回node节点的名称
	 * @author 杨希
	 * @date 2014-4-15 上午10:00:00
	 * @throws
	 */
	public function getTitleName($modelname = '') {
		$title = '';
		if ($modelname) {
			$model = D('Node');
			$title = $model->where('name = '."'$modelname'")->getField('title');
		}
		return $title;
	}
	/**
	 * @Title: getDCFilter
	 * @Description: todo(获取动态配置的文件返回值)
	 * @param  $modelname      string 模型对象名称
	 * @param  $cache          boolean 是否调用缓存
	 * @param  $typename       string 匹配类型,只支持boolean型的
	 * @return $returnData array  返回数组
	 * @author 杨希
	 * @date 2014-4-15 上午10:00:00
	 * @throws
	 */
	public function getDCFilter(){
		$fileUrl = DConfig_PATH . "/System/dynamicConfigFilter.inc.php";
		if (file_exists($fileUrl)) {
			$list = require $fileUrl;
			return $list;
		} else {
			return '';
		}
	}
	/**
	 * @Title: GetDetailFile
	 * @Description: todo(获取动态配置的文件返回值)
	 * @param  $modelname      string 模型对象名称
	 * @param  $cache          boolean 是否调用缓存
	 * @param  $typename       string 匹配类型,只支持boolean型的
	 * @return $returnData array  返回数组
	 * @author 杨希
	 * @date 2014-4-15 上午10:00:00
	 * @throws
	 */
	public function GetDetailFile($modelname,$cache,$typename='',$listCongfigControl="shows",$subdatatableName) {
		/*
		 * typename参数如果为空，则默认获取list.inc.php文件 ，其他参数请参照getDynamicFileName方法内的参数说明
		 */
		//如果后4位为视图
		if(substr($typename,-4)==='View'){
        	$filename=$this->getDynamicFileName('view',$typename);
		}elseif($typename=='SubDatatable'){
			$filename=$this->getDynamicFileName('view',$subdatatableName);
		}else{
			$filename=$this->getDynamicFileName($typename);
		}
        //定义个人文件的名称
		$filename_user = $filename.$_SESSION[C('USER_AUTH_KEY')].".php";
		
		if( !$cache ) return DConfig_PATH . "/Models/".$modelname."/".$filename;
		//定义个人文件缓存地址
		$p= C("DATA_CACHE_PATH")."Dynamicconf/Models/".$modelname;
		if(!file_exists($p."/".$filename) && file_exists( DConfig_PATH . "/Models/".$modelname."/".$filename )){

			$isdir=$this->make_dir($p,0777);
			$filearr =require DConfig_PATH . "/Models/".$modelname."/".$filename;
			if($typename == 'toolbar'){
				$temp = $this->getdetailconfigofdb($modelname);
				if($temp){
					
					$filearr = $temp;
				}
			}
			$cachedata=array();
			if($filename == "list.inc.php"){
				//角色禁止
				$userid=$_SESSION[C('USER_AUTH_KEY')];
				//查询用户角色
				$userModel=D('User');
				$userVo=$userModel->where('id='.$userid)->find();
				//查询角色
				$RolegroupUserDao = M("rolegroup_user");
				$usergrouplist=$RolegroupUserDao->where('user_id = '.$userVo['id'])->select();
				
				//读取个人是否设置如果有则读取个人设置 如果不存在个人设置则读取公共设置
				$PrivateListincModel=M('mis_system_private_listinc');
				$PrivateListincMap['modelname']=$modelname;
				$PrivateListincMap['userid']=$_SESSION[C('USER_AUTH_KEY')];
				$PrivateListincList=$PrivateListincModel->where($PrivateListincMap)->select();
				foreach ($PrivateListincList as $pk=>$pv){
					$PrivateListincList[$pv['fieldname']]=$pv;
				}
				if(!empty($PrivateListincList)){
					//存在则替换list里面的shows、whdths、sortnum
					foreach ($filearr as $fk=>$fv){
						$isexisit=array_key_exists($fv['name'], $PrivateListincList);
						if($isexisit){
							$filearr[$fk]['shows']=$PrivateListincList[$fk]['shows'];
							$filearr[$fk]['widths']=$PrivateListincList[$fk]['widths']?$PrivateListincList[$fv['name']]['widths']:null;
							$filearr[$fk]['sortnum']=$PrivateListincList[$fk]['sortnum'];
						}
					}
				}else{
					//读取公共模块设置
					$PublicListincModel=M('mis_system_public_listinc');
					$PublicListincMap['modelname']=$modelname;
					$PublicListincList=$PublicListincModel->where($PublicListincMap)->select();
					foreach ($PublicListincList as $pk=>$pv){
						$PublicListincList[$pv['fieldname']]=$pv;
					}
					if(!empty($PublicListincList)){
						//存在则替换list里面的shows、whdths、sortnum
						foreach ($filearr as $fk=>$fv){
							$isexisit=array_key_exists($fv['name'], $PublicListincList);
							if($isexisit){
								$filearr[$fk]['shows']=$PublicListincList[$fk]['shows'];
								$filearr[$fk]['widths']=$PublicListincList[$fk]['widths']?$PublicListincList[$fv['name']]['widths']:null;
								$filearr[$fk]['sortnum']=$PublicListincList[$fk]['sortnum'];
							}
						}
					}
					
				}
				
				foreach($filearr as $k=>$v){
					if( $v[$listCongfigControl]==1){
						$arr = $v['row_access'] ?$v['row_access']:array();
						//用户禁止
						if( $arr && isset( $arr["deny"]["userid"]) && $arr["deny"]["userid"]!=""){
							$denyuser = explode(",",$arr["deny"]["userid"]);
							$alluser = isset($arr["allow"]["userid"])?$arr["allow"]["userid"]:array();
							$allowuser =  explode(",",$alluser);
							if( in_array($_SESSION[C('USER_AUTH_KEY')],$denyuser) ){
								continue;
							}else{
								if( $denyuser && in_array(0,$denyuser) && !in_array($_SESSION[C('USER_AUTH_KEY')],$allowuser)){
									continue;
								}else{
// 									//角色禁止
									if(!$userVo['attachrole'] && !$userVo['role_id']){
										$attachrole=array();
										foreach ($usergrouplist as $key=>$val){
											if($key == 0){
												$attachrole[] = $val['rolegroup_id'];
											}else{
												$attachrole[] = $val['rolegroup_id'];
											}
										}
									}else{
										//获取辅助角色
										$attachrole = array_filter(array_diff(explode(',',$userVo['attachrole']),array($userVo['role_id'])));
									}
									if( $arr && isset( $arr["deny"]["roleid"]) && $arr["deny"]["roleid"]!=""){
										$denyrole = explode(",",$arr["deny"]["roleid"]);
										$allrole = isset($arr["allow"]["roleid"])?$arr["allow"]["roleid"]:array();
										$allowrole =  explode(",",$allrole);
										$valueArr=array_intersect($attachrole,$denyrole);
										$AllowValueArr=array_intersect($attachrole,$allowrole);
										if(!empty($valueArr)){
											continue;
										}else{
											if( $denyrole && in_array(0,$denyrole) && empty($AllowValueArr)){
												continue;
											}
										}
										$cachedata[$k]=$v;
									}else{
										$cachedata[$k]=$v;
									}
								}
							}
						}else{
							//角色禁止
							if(!$userVo['attachrole'] && !$userVo['role_id']){
								$attachrole=array();
								foreach ($usergrouplist as $key=>$val){
									if($key == 0){
										$attachrole[] = $val['rolegroup_id'];
									}else{
										$attachrole[] = $val['rolegroup_id'];
									}
								}
							}else{
								//获取辅助角色
								$attachrole = array_filter(array_diff(explode(',',$userVo['attachrole']),array($userVo['role_id'])));
							}
							$arr1 = $v['row_access'] ?$v['row_access']:array();
							if( $arr && isset( $arr["deny"]["roleid"]) && $arr["deny"]["roleid"]!=""){
								$denyrole = explode(",",$arr["deny"]["roleid"]);
								$allrole = isset($arr["allow"]["roleid"])?$arr["allow"]["roleid"]:array();
								$allowrole =  explode(",",$allrole);
								$valueArr=array_intersect($attachrole,$denyrole);
								$AllowValueArr=array_intersect($attachrole,$allowrole);
								if(!empty($valueArr)){
									continue;
								}else{
									if( $denyrole && in_array(0,$denyrole) && empty($AllowValueArr)){
										continue;
									}
								}
								$cachedata[$k]=$v;
							}else{
								$cachedata[$k]=$v;
							}
							//end
						}
					}
				}
			}else{
				foreach($filearr as $k=>$v){
					if( $v[$listCongfigControl]==1){
						$cachedata[$k]=$v;
					}
				}
			}
			$this->writeover($p."/".$filename_user,"return ".$this->pw_var_export($cachedata).";\n",true);
		}
		return $p."/".$filename_user;
	}
	/**
	 * @Title: GetSubDetailFile
	 * @Description: todo(获取动态配置的明细文件返回值)
	 * @param  $modelname      string 模型对象名称
	 * @param  $cache          boolean 是否调用缓存 
	 * @param  $typename       string 匹配类型,只支持boolean型的
	 * @return $returnData array  返回数组
	 * @author 杨希
	 * @date 2014-4-15 上午10:00:00
	 * @throws
	 */
	public function GetSubDetailFile($modelname,$cache,$typename='') {
		$filename="sublist.inc.php";
		if( $typename=="toolbar" ){
			$filename="subtoolbar.extension.inc.php";
		}else if( $typename=="link" ){
			$filename="sublink.extension.inc.php";
		}else if( $typename=="searchby" ){
			$filename="subsearchby.inc.php";
		}
		$filename_user = $filename.$_SESSION[C('USER_AUTH_KEY')].".php";
		if( !$cache ) return DConfig_PATH . "/Models/".$modelname."/".$filename;
		$p= C("DATA_CACHE_PATH")."Dynamicconf/Models/".$modelname;
		if(!file_exists($p."/".$filename_user) && file_exists( DConfig_PATH . "/Models/".$modelname."/".$filename )){
			$isdir=$this->make_dir($p);
			$filearr =require DConfig_PATH . "/Models/".$modelname."/".$filename;
			$cachedata=array();
			foreach($filearr as $k=>$v){
				if( $v['shows']==1){
					$arr = $v['row_access'] ?$v['row_access']:array();
					//用户禁止
					if( $arr && isset( $arr["deny"]["userid"]) && $arr["deny"]["userid"]!=""){
						$denyuser = explode(",",$arr["deny"]["userid"]);
						$alluser = isset($arr["allow"]["userid"])?$arr["allow"]["userid"]:array();
						$allowuser =  explode(",",$alluser);
						if( in_array($_SESSION[C('USER_AUTH_KEY')],$denyuser) ){
							continue;
						}else{
							if( in_array(0,$denyuser) && !in_array($_SESSION[C('USER_AUTH_KEY')],$allowuser)){
								continue;
							}
						}
						$cachedata[$k]=$v;
					}else{
						$cachedata[$k]=$v;
					}
					
				}
			}
			$this->writeover($p."/".$filename_user,"return ".$this->pw_var_export($cachedata).";\n",true);
		}
		return $p."/".$filename_user;
	}

	/**
	 * @Title: GetDynamicconfData
	 * @Description: todo(获取动态配置的单行返回值)
	 * @param  $model      string 模型对象名称
	 * @param  $id         int    模型对象的记录（表主键）
	 * @param  $mapType    string 匹配类型,只支持boolean型的
	 * @return $returnData array  返回数组
	 * @author 杨希
	 * @date 2014-4-15 上午10:00:00
	 * @throws
	 */
	public function GetDynamicconfData($modelname,$id,$mapType="message",$field="id",$isallshow=false){
		$model= D($modelname);
		$detailList = $this->getDetail($modelname,false);
		$map[$field] =$id;
		$map['status']=1;
		//查询数据库取得详细信息记录
		$list=$model->where($map)->find();
		//定义返回值
		$returnData=array();
		//首先循环动态配置的详细信息
		foreach($detailList as $detailListKey => $detailListVal){
			if($isallshow){
				if($detailListVal['name']!='action'){
					if(array_key_exists($detailListVal['name'],$list)){
						//存在函数时
						if($detailListVal['func']){
							foreach($detailListVal['func'] as $funcKey=>$funcVal){
								$returnData[$detailListVal['name']][$detailListVal['showname']]=getConfigFunction($list[$detailListVal['name']],$funcVal,$detailListVal['funcdata'][$funcKey],$list);
								if($funcVal[0]=='richtext2str'){
									$returnData[$detailListVal['name'].'_souce'][$detailListVal['showname'].'_souce']=htmlspecialchars_decode($list[$detailListVal['name']]);
								}else{
									$returnData[$detailListVal['name'].'_souce'][$detailListVal['showname'].'_souce']=$list[$detailListVal['name']];
								}
								
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
							if($funcVal[0]=='richtext2str'){
									$returnData[$detailListVal['name'].'_souce'][$detailListVal['showname'].'_souce']=htmlspecialchars_decode($list[$detailListVal['name']]);
							}else{
									$returnData[$detailListVal['name'].'_souce'][$detailListVal['showname'].'_souce']=$list[$detailListVal['name']];
								}
							}
						}else{
							$returnData[$detailListVal['name']][$detailListVal['showname']]=$list[$detailListVal['name']];
						}
					}
				}
				
			}else{
				//根据动态配置某个标识过滤多余的数据
				if( $detailListVal[$mapType]==1){
					//将动态配置名称与数据库返回记录里的字段主键做比较，存在时执行
					
					if(array_key_exists($detailListVal['name'],$list)){
						//存在函数时
						if($detailListVal['func']){
							foreach($detailListVal['func'] as $funcKey=>$funcVal){
								$returnData[$detailListVal['name']][$detailListVal['showname']]=getConfigFunction($list[$detailListVal['name']],$funcVal,$detailListVal['funcdata'][$funcKey],$list);
							if($$funcVal[0]='richtext2str'){
									$returnData[$detailListVal['name'].'_souce'][$detailListVal['showname'].'_souce']=htmlspecialchars_decode($list[$detailListVal['name']]);
							}else{
									$returnData[$detailListVal['name'].'_souce'][$detailListVal['showname'].'_souce']=$list[$detailListVal['name']];
								}
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
							if($funcVal[0]=='richtext2str'){
									$returnData[$detailListVal['name'].'_souce'][$detailListVal['showname'].'_souce']=htmlspecialchars_decode($list[$detailListVal['name']]);
							}else{
									$returnData[$detailListVal['name'].'_souce'][$detailListVal['showname'].'_souce']=$list[$detailListVal['name']];
								}
							}
						}else{
							    $returnData[$detailListVal['name']][$detailListVal['showname']]=$list[$detailListVal['name']];
						}
					}
				}
			}
		}
		
		return $returnData;
	}
	
	public function updatFile($modelname){
		$url =  DConfig_PATH . "/Models/".$modelname;
		if (!is_dir($url)) {
			mkdir($url);
		}
	}
	/**
	 * @Title: getdetailconfigofdb
	 * @Description: todo(获取数据库里的toolbar配置数据) 
	 * @param unknown_type $modelname
	 * @return multitype:unknown   
	 * @author 谢友志 
	 * @date 2015-6-15 下午6:11:07 
	 * @throws
	 */
	public function getdetailconfigofdb($modelname){
		$model = M("mis_system_toolbar_config");
		$map['modelname'] = $modelname;
		$list = $model->where($map)->select();
		$newlist = array();
		foreach($list as $key=>$val){
			unset($val['id']);
			unset($val['modelname']);
			$tkey = $val['jskey'];			
			unset($val['jskey']);
			$newlist[$tkey] = $val;
		}
		return $newlist;
	}
}
?>