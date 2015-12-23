<?php
/**
 * @Title: file_name
 * @Package package_name
 * @Description: todo(动态展示)
 * @author Arrowing
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-6-1 下午12:01:16
 * @version V1.0
 */
class DynamicconfAction extends CommonAction {
	/**
	 * 查找动态配置组里边可用的节点
	 */
	private $firstDetail = array();
	/**
	 * (non-PHPdoc)
	 * @Description: todo(首页输出模板方法)
	 * @see CommonAction::index()
	*/
	public function index() {
		if (isset ($_SESSION[C('USER_AUTH_KEY')])) {
			$model = D('SystemConfigDetail');
			if ($_REQUEST['jump']) {
				$modelName = $_GET['model'];
				$list = $model->getDetail($modelName,false);
				$this->assign('model', $modelName);
				$name = $model->getTitleName($modelName);
				$sublist = $model->getSubDetail($modelName,false);
				if ($sublist) {
					$this->assign('sublist', $sublist);
				}
				//by arrowing
				$searchinc = $model->getDetail($modelName,false,'search');
				if($searchinc){
					$searchurl = __APP__.'/Search/edit/view/'.$modelName;
				}else{
					$searchurl = __APP__.'/Search/add/view/'.$modelName;
				}
				$this->assign('name', $name);
				$this->assign('list', $list);
				$this->assign('searchurl', $searchurl);
				$this->assign('searchinc', $searchinc);
				$this->display('confpanel');
			} else {
				//$this->getRoleTree();
				$models = D('SystemConfigNumber');
				$list = $models->getRoleTree('dynamicconfBox');
				$firstDetail=$models->firstDetail;
				$this->assign('returnarr',$list);
				$list = $model->getDetail($models->firstDetail['name'],false);
				$this->assign('list', $list);
				$sublist = $model->getSubDetail($models->firstDetail['name'],false);
				if ($sublist) {
					$this->assign('sublist', $sublist);
				}
				//by arrowing
				$searchinc = $model->getDetail($this->firstDetail['name'],false,'search');
				if($searchinc){
					$searchurl = __APP__.'/Search/edit/view/'.$models->firstDetail['name'];
				}else{
					$searchurl = __APP__.'/Search/add/view/'.$models->firstDetail['name'];
				}
				$this->assign('searchurl', $searchurl);
				$this->assign('searchinc', $searchinc);
				$this->assign('name', $models->firstDetail['title']);
				$this->assign('model', $models->firstDetail['name']);
				$this->assign('check', $models->firstDetail['check']);
				$this->display();
			}
		}
	}
	
	/**
	 * @Title: confpanel
	 * @Description: todo(进入动态面板并配置)
	 * @author 杨东
	 * @date 2013-6-1 下午12:02:36
	 * @throws
	 */
	public function confpanel() {
		$modelName = $_GET['model'];
		$model = D('SystemConfigDetail');
		$list = $model->getDetail($modelName,false);
		$this->assign('model', $modelName);
		$name = $model->getTitleName($modelName);
		$sublist = $model->getSubDetail($modelName,false);
		if ($sublist) {
			$this->assign('sublist', $sublist);
		}
		$this->assign('name', $name);
		$this->assign('list', $list);
		$this->display();
	}
	/**
	 * (non-PHPdoc)
	 * @Description: todo(构造树)
	 * @see CommonAction::getTree()
	 */

	/**
	 * (non-PHPdoc)
	 * @Description: todo(重写修改方法)
	 * @param $data
	 * @param $iscrossDomain 是否为跨类调用 动态表单调用此函数 jiangx 2014-02-24
	 * @see CommonAction::update()
	 */
	public function update($data = array(), $iscrossDomain = false) {
		//管理员设置存入公共设置
		$publistListincModel=M('mis_system_public_listinc');
		$modelName = $_POST['model'];
		$shows=$_REQUEST['shows'];
		foreach ($shows as $sk=>$sv){
			if($sv==0){
				unset($shows[$sk]);
			}
		}
		$showname=$_REQUEST['showname'];
		$widths=$_REQUEST['widths'];
		$sortnum=$_REQUEST['sortnum'];
		//查询是否存在数据中
		$publistMap=array();
		$publistMap['modelname']=$modelName;
		$publistListincList=$publistListincModel->where($publistMap)->select();
		foreach ($publistListincList as $pk=>$pv){
			$publistListincList[$pv['fieldname']]=$pv;
		}
		foreach ($showname as $shownamek=>$shownamev){
			$fieldname[]=$shownamek;
			//判断是否显示
			$fieldexist=array_key_exists($shownamek, $shows);
			if(empty($publistListincList[$shownamek])){
					//不存在添加
					$publistData=array();
					$publistData['modelname']=$modelName;
					$publistData['fieldname']=$shownamek;
					$publistData['widths']=$widths[$shownamek];
					$publistData['sortnum']=$sortnum[$shownamek];
					$publistData['shows']=$fieldexist?1:0;
					$publistList=$publistListincModel->add($publistData);
					if($publistList==false){
						$this->error ( L('_ERROR_') );
					}
			}else{
				//存在修改
				$publistData=array();
				$publistincMap=array();
				$publistincMap['modelname']=$modelName;
				$publistincMap['fieldname']=$shownamek;
				$publistData['widths']=$widths[$shownamek];
				$publistData['sortnum']=$sortnum[$shownamek];
				$publistData['shows']=$fieldexist?1:0;
				$publistList=$publistListincModel->where($publistincMap)->save($publistData);
				if($publistList==false){
					$this->error ( L('_ERROR_') );
				}
			}
		}
		//删除不存在的字段
		$publistDeleteMap['modelname']=$modelName;
		$publistDeleteMap['fieldname']=array('not in',$fieldname);
		$publistincList=$publistListincModel->where($publistDeleteMap)->delete();
		if($publistincList==false){
			$this->error ( L('_ERROR_') );
		}
		
		$model = D('SystemConfigDetail');
		if ($iscrossDomain) {
			$temporaryvar = $_POST; //把原$_POST赋值给一个临时变量  --- 不可删，用于动态表单调用
			$_POST = $data;
		}
		
		if($_POST['step'] == 'quickSearch'){
			$detailList = $model->getDetail($modelName,false);
			//修改动态表单配置记录
			//begin
			$mMDFMmodel = D("MisDynamicFormManage");
			$Form_id = $mMDFMmodel->where("nodename = '". $modelName."'")->getField("id");
			if ($Form_id) {
				$mMDFFmodel = D("MisDynamicFormField");
				$aMap = array();
				$aMap['formid'] = $Form_id;
				$MDFFlist = $mMDFFmodel->where($aMap)->getField("fieldname,id");
			}
			//end
			foreach ($detailList as $k1 => $v1) {
				$detailList[$k1]['searchField'] = $_POST['searchField'][$k1]?$_POST['searchField'][$k1]:"";
				$detailList[$k1]['table'] = $_POST['table'][$k1]?$_POST['table'][$k1]:"";
				$detailList[$k1]['field'] = $_POST['field'][$k1]?$_POST['field'][$k1]:"";
				$detailList[$k1]['conditions'] = $_POST['conditions'][$k1]?$_POST['conditions'][$k1]:"";
				$detailList[$k1]['type'] = $_POST['type'][$k1]?$_POST['type'][$k1]:"";
				$detailList[$k1]['issearch'] = $_POST['issearch'][$k1]?$_POST['issearch'][$k1]:0;
				$detailList[$k1]['isallsearch'] = $_POST['isallsearch'][$k1]?$_POST['isallsearch'][$k1]:0;
				$detailList[$k1]['searchsortnum'] = $_POST['searchsortnum'][$k1]?$_POST['searchsortnum'][$k1]:0;
				//保存动态表单配置记录
				//begin
				if ($v1['name'] == "orderno") {
					continue;
				}
				if ($_POST['pkeys'][$k1] && $Form_id && $MDFFlist) {
					if ($MDFFlist[$_POST['pkeys'][$k1]]) {
						$saveData = array();
						$saveData['linktable'] = $detailList[$k1]['table'];
						$saveData['linkfield'] = $detailList[$k1]['field'];
						$saveData['issearchlist'] = $detailList[$k1]['issearch'];
						$list = $mMDFFmodel->where("id = ". $MDFFlist[$_POST['pkeys'][$k1]])->save($saveData);
					}
				}
				//end
			}
			$model->setDetail($modelName, $detailList);
			if ($iscrossDomain) {
				$_POST = $temporaryvar; ////把原临时变量赋值给还原$_POST  --- 不可删，用于动态表单调用
				return true;
			} else {
				$this->success(L('_SUCCESS_'));
			}
			exit;
		} else if($_POST['step'] == 'subQuickSearch'){
			$detailList = $model->getSubDetail($modelName,false);
			foreach ($detailList as $k1 => $v1) {
				$detailList[$k1]['searchField'] = $_POST['searchField'][$k1]?$_POST['searchField'][$k1]:"";
				$detailList[$k1]['table'] = $_POST['table'][$k1]?$_POST['table'][$k1]:"";
				$detailList[$k1]['field'] = $_POST['field'][$k1]?$_POST['field'][$k1]:"";
				$detailList[$k1]['conditions'] = $_POST['conditions'][$k1]?$_POST['conditions'][$k1]:"";
				$detailList[$k1]['type'] = $_POST['type'][$k1]?$_POST['type'][$k1]:"";
				$detailList[$k1]['issearch'] = $_POST['issearch'][$k1]?$_POST['issearch'][$k1]:0;
				$detailList[$k1]['isallsearch'] = $_POST['isallsearch'][$k1]?$_POST['isallsearch'][$k1]:0;
				$detailList[$k1]['searchsortnum'] = $_POST['searchsortnum'][$k1]?$_POST['searchsortnum'][$k1]:0;
			}
			$model->setSubDetail($modelName, $detailList);
			$this->success(L('_SUCCESS_'));
			exit;
		}
		$detailList = $model->getDetail($modelName,false);
		foreach ($detailList as $k1 => $v1) {
			$detailList[$k1]['shows']=$detailList[$k1]['sorts']=0;
			$detailList[$k1]['showname']=$detailList[$k1]['widths']=$detailList[$k1]['models']='';
			$detailList[$k1]['status'] = $_POST['status'][$k1];
			$_POST['showname'][$k1] && $detailList[$k1]['showname']=$_POST['showname'][$k1];
			$_POST['helpvalue'][$k1] && $detailList[$k1]['helpvalue']=$_POST['helpvalue'][$k1];
			$_POST['shows'][$k1] 	&& $detailList[$k1]['shows']=1;
			$_POST['widths'][$k1] 	&& $detailList[$k1]['widths'] =$_POST['widths'][$k1];
			$_POST['sorts'][$k1] 	&& $detailList[$k1]['sorts']=1;
			$_POST['models'][$k1] 	&& $detailList[$k1]['models'] =$_POST['models'][$k1];
			$_POST['methods'][$k1] 	&& $detailList[$k1]['methods'] =$_POST['methods'][$k1];
			$_POST['relation'][$k1] 	&& $detailList[$k1]['relation'] =$_POST['relation'][$k1];
			//$_POST['issearch'][$k1] && $detailList[$k1]['issearch']=1;
			$_POST['isexport'][$k1] && $detailList[$k1]['isexport']=1;
			$detailList[$k1]['rules']=$_POST['rules'][$k1]?1:0;  //加入权限配置规则字段 by renl
			$detailList[$k1]['message']=$_POST['message'][$k1]?1:0;  //加入权限配置消息字段 by renl
			$_POST['sortnum'][$k1] 	&& $detailList[$k1]['sortnum'] =$_POST['sortnum'][$k1];
		}
		$model->setDetail($modelName, $detailList);
		$subdetailList = $model->getSubDetail($modelName,false);
		if ($subdetailList) {
			foreach ($subdetailList as $k1 => $v1) {
				$subdetailList[$k1]['shows']=$subdetailList[$k1]['sorts']=$detailList[$k1]['issearch']=0;
				$subdetailList[$k1]['showname']=$subdetailList[$k1]['widths']=$subdetailList[$k1]['models']='';
				$_POST['subshowname'][$k1] 	&& $subdetailList[$k1]['showname']=$_POST['subshowname'][$k1];
				$_POST['subshows'][$k1] 	&& $subdetailList[$k1]['shows']=1;
				$_POST['subwidths'][$k1] 	&& $subdetailList[$k1]['widths'] =$_POST['subwidths'][$k1];
				$_POST['subissearch'][$k1] 	&& $subdetailList[$k1]['issearch']=1;
				$_POST['subisexport'][$k1] 	&& $subdetailList[$k1]['isexport']=1;
				$_POST['subsorts'][$k1] 	&& $subdetailList[$k1]['sorts']=1;
				$_POST['submodels'][$k1] 	&& $subdetailList[$k1]['models'] =$_POST['submodels'][$k1];
				$_POST['submethods'][$k1] 	&& $subdetailList[$k1]['methods'] =$_POST['submethods'][$k1];
				$_POST['subsortnum'][$k1] 	&& $subdetailList[$k1]['sortnum'] =$_POST['subsortnum'][$k1];
				
				
				$_POST['isexport'][$k1] 	&& $subdetailList[$k1]['isexport'] =$_POST['isexport'][$k1];
				
				
			}
			$model->setSubDetail($modelName, $subdetailList);
		}
		$this->success(L('_SUCCESS_'));
	}
	/**
	 * @Title: linkshow
	 * @Description: todo(输出到模板,关联配置，专门配置common.php的扩展函数)
	 * @author liminggang
	 * @date 2013-6-1 下午1:46:32
	 * @throws
	 */
	public function linkshow() {
		$modelName = $_GET['setmodule'];
		$issub = intval($_GET['issub']);
		$key = $_REQUEST['key'];
		$step = intval($_POST['step']);
		$model = D('SystemConfigDetail');
		$detailList = $model->getDetail($modelName,false);
		if( $issub){
			$detailList = $model->getSubDetail($modelName,false);
		}
		if( $step==2 ){
			$modelName =  $_POST['setmodule'];
			$detailList = $model->getDetail($modelName,false);
			$issub=$_POST['issub'];
			if( $issub){
				$detailList = $model->getSubDetail($modelName,false);
			}
			if ($detailList) {
				$arr = $detailList[$key];
				unset($arr['extention_html_start']);
				unset($arr['extention_html_end']);
				unset($arr['func']);
				unset($arr['funcdata']);
				$extention_html_start=$_POST['htmlstartnewadd'];
				foreach($extention_html_start as $k=>$v){
					if($v[0]) $arr['extention_html_start'][$k]=$v[0];
				}
				$extention_html_end=$_POST['htmlendnewadd'];
				foreach($extention_html_end as $k=>$v){
					if($v[0]) $arr['extention_html_end'][$k]=$v[0];
				}
				$func=$_POST['funcnamenewadd'];
				foreach($func as $k=>$v){
					foreach($v as $k2=>$v2){
						if(!function_exists($v2)) $this->error("关联操作函数".$v2."不存在！");
						if($v2) $arr['func'][$k][$k2]=$v2;
					}
				}
				$funcdata=$_POST['funcdatanewadd'];
				foreach($funcdata as $k=>$v){
					foreach($v as $k2=>$v2){
						$dchar=explode(";",$v2);
						if(count($dchar)>10) $this->error("关联操作函数参数最多只能传递10个！");
						if($v2) $arr['funcdata'][$k][$k2]=$dchar;
					}
				}
				$detailList[$key]=$arr;
				if( $issub){
					$model->setSubDetail($modelName, $detailList);
				}else{
					$model->setDetail($modelName, $detailList);
				}
			}else{
				$this->error("模块不存在!");
			}
			$this->success(L('_SUCCESS_'));
		}
		$this->assign("key",$key);
		$this->assign("module",$modelName);
		$this->assign("issub",$issub);
		$i=$j=0;
		$arr=$detailList[$key];
		$oldarr=$a=array();
		foreach($arr['func'] as $k=>$v){
			$a['num']=$j;
			foreach($v as $k1=>$v1){
				if($v1){
					if($k1==0){
						$a['one']=1;
						if(count($v)>1) $a['haschildren']=1;
					}
					$i++;
					$a['func']=$v1;
					$a['k1']=$k1;
					$a['magrin']=($k1-1)*12;
					$a['inputsize']=30-$k1*2;
					$a['funcdata']=implode(";",$arr['funcdata'][$k][$k1]);
					$a['funcdata']=htmlspecialchars($a['funcdata']);
					if($arr['extention_html_start'][$k]) $a['htmlstart']=$arr['extention_html_start'][$k];
					if($arr['extention_html_end'][$k]) $a['htmlend']=$arr['extention_html_end'][$k];
					array_push($oldarr,$a);
				}
				$a=array();
				$a['num']=$j;
			}
			$j++;
		}

		$this->assign("list",$oldarr);
		$this->assign("i",$i);
		$this->display();
	}

	/**
	 * @Title: comboxgetTableField
	 * @Description: todo(根据表明获取字段)
	 * @param unknown_type $t
	 * @return multitype:multitype:string
	 * @author liminggang
	 * @date 2013-6-1 下午2:03:36
	 * @throws
	 */
	function comboxgetTableField( $t='' ){
		$model=M();
		if( $t=="" ){
			$table = $this->escapeStr($_POST['table']);
		}else{
			$table = $t;
		}
		$arr=array(array("","请选择对应表的显示字段"));
		if ($table!='') {
			$columns = $model->query("show columns from ".$table);
			$model2=M("INFORMATION_SCHEMA.COLUMNS","","",1);
			$columnstitle = $model2->where("table_name = '".$table."' AND TABLE_SCHEMA = '".C('DB_NAME')."'")->getField("COLUMN_NAME,COLUMN_COMMENT");
			foreach($columns as $k=>$v){
				$title=$v['Field'];
				if( $columnstitle[$v['Field']] ) $title=$columnstitle[$v['Field']];
				array_push($arr,array($v['Field'], $title));
			}
		}
		if( $t=="" ){
			echo json_encode($arr);
		}else{
			return $arr;
		}
	}

	/**
	 * @Title: quickSearch
	 * @Description: todo(快捷搜索配置)
	 * @author 杨东
	 * @date 2013-5-28 下午2:14:44
	 * @throws
	 */
	function quickSearch(){
		$model = D('SystemConfigDetail');
		$modelName = $_GET['model'];
		$list = $model->getDetail($modelName,false);
		$models = D($modelName);
		$tableName = $models->getTableName();
		foreach ($list as $k => $v) {
			if(strpos($v['searchField'], '.')){
				$searchField = explode(".", $v['searchField']);
				if (!$searchField[0]) {
					$list[$k]['searchField'] = $tableName.$v['searchField'];
				}
			} else {
				if(!$v['searchField']){
					$v['searchField'] = $v['name'];
				}
				$list[$k]['searchField'] = $tableName.".".$v['searchField'];
			}
		}
		$this->assign('list', $list);
		$this->assign('model', $modelName);
		$name = $model->getTitleName($modelName);
		$this->assign('name', $name);
		$this->display();
	}
	/**
	 * @Title: subQuickSearch
	 * @Description: todo(SUB快捷搜索配置)
	 * @author 杨东
	 * @date 2013-5-28 下午2:14:44
	 * @throws
	 */
	function subQuickSearch(){
		$model = D('SystemConfigDetail');
		$modelName = $_GET['model'];
		$list = $model->getSubDetail($modelName,false);
		$models = D($modelName);
		$tableName = $models->getTableName();
		$this->assign('list', $list);
		$this->assign('model', $modelName);
		$name = $model->getTitleName($modelName);
		$this->assign('name', $name);
		$this->display();
	}
	/**
	 * @Title: lookupForbidView
	 * @Description: todo(查看被禁止查看列的人员)
	 * @author liminggang
	 * @date 2013-12-10 上午11:57:34
	 * @throws
	 */
	public function lookupForbidView(){
		//判断是单头还是详情
		$issub = $_REQUEST['issub'];
		//获取配置文件的键值
		$key = $_REQUEST['key'];
		//获取配置文件名称
		$modelName = $_REQUEST['setmodule'];
		$model = D('SystemConfigDetail');
		$listLimitModel=D('MisSystemListLimit');
		$useridArr=$_POST['userid'];
		$roleidArr=$_POST['roleid'];
		if( $issub){
			$detailList = $model->getSubDetail($modelName,false);
		}else{
			$detailList = $model->getDetail($modelName,false);
		}
		if($_POST['isInsert']){
			//表示修改数据
			if($detailList){
				foreach($detailList as $k=>$v){
					if($k == $key){
						if(!empty($v["row_access"])){$arr =$v["row_access"];}else{$arr =array();}
						if(empty($arr)){
							//修改当前key的列授权内容
							if(isset($arr['deny'])){
								if(isset($arr['deny']['userid']) && $arr['deny']['userid']!=""){
									//删除数据库中禁止字段
									$denywhere['modelname']=$modelName;
									$denywhere['denyfields']=array('neq','');
									$listdenyLimitInfo=$listLimitModel->where($denywhere)->select();
									foreach ($listdenyLimitInfo as $listdkey=>$listdval){
										$denyfields=json_decode($listdval['denyfields'], true);
										foreach ($denyfields as $dfk=>$dfv){
											if($v['name']==$dfv[0]){
												unset($denyfields[$dfk]);
											}
										}
										sort($denyfields);
										if(!empty($denyfields)){
											$denydata['denyfields']=json_encode($denyfields);
											$denydata['updateid']=$_SESSION[C('USER_AUTH_KEY')];
											$denydata['updatetime']=time();
										}else{
											$denydata['denyfields']=null;
											$denydata['updateid']=$_SESSION[C('USER_AUTH_KEY')];
											$denydata['updatetime']=time();
										}
									
										$denylist=$listLimitModel->where('id='.$listdval['id'])->save($denydata);
									}	
									//end
									//释放配置的禁止用户
									unset($detailList[$k]['row_access']['deny']['userid']);
								}
								if(isset($arr['deny']['roleid']) && $arr['deny']['roleid']!=""){
									
									
									unset($detailList[$k]['row_access']['deny']['roleid']);
								}
							}
						}else{
							//修改当前key的列授权内容
							if(isset($arr['deny'])){
								if(isset($arr['deny']['userid']) && $arr['deny']['userid']!=""){
									$detailList[$k]['row_access']['deny']['userid'] = implode(",",$useridArr);
									
									//删除数据库中禁止字段
									$denywhere['modelname']=$modelName;
									$denywhere['denyfields']=array('neq','');
									$listdenyLimitInfo=$listLimitModel->where($denywhere)->select();
									foreach ($listdenyLimitInfo as $listdkey=>$listdval){
										 if(!in_array($listdval['userid'],$useridArr)){
											 $denyfields=json_decode($listdval['denyfields'], true);
											 foreach ($denyfields as $dfk=>$dfv){
												if($v['name']==$dfv[0]){
													unset($denyfields[$dfk]);
												}
											}
											sort($denyfields);
											if(!empty($denyfields)){
												$denydata['denyfields']=json_encode($denyfields);
												$denydata['updateid']=$_SESSION[C('USER_AUTH_KEY')];
												$denydata['updatetime']=time();
											}else{
												$denydata['denyfields']=null;
												$denydata['updateid']=$_SESSION[C('USER_AUTH_KEY')];
												$denydata['updatetime']=time();
											}  
											$denylist=$listLimitModel->where('id='.$listdval['id'])->save($denydata);
										} 
									}
									//end
								}
								if(isset($arr['deny']['roleid']) && $arr['deny']['roleid']!=""){
									$detailList[$k]['row_access']['deny']['roleid'] = implode(",",$roleidArr);
								}
							}
						}
					}
				}
				
				if( $issub){
					$model->setSubDetail($modelName, $detailList);
				}else{
					$model->setDetail($modelName, $detailList);
				}
				//删除缓存文件
				$p= C("DATA_CACHE_PATH")."Dynamicconf/Models/".$modelName;
				
				if(file_exists($p)){
					$FileUtil = new FileUtil();
					$boolean=$FileUtil->unlinkDir($p);
					if(!$boolean){
						$this->error("更新缓存文件失败");
					}
				}
				$this->success(L('_SUCCESS_'));exit;
			}
		}else{
			$this->assign('setmodule',$modelName);
			$this->assign('key',$key);
			$this->assign('issub',$issub);
			$userArr = array();
			if($detailList){
				foreach($detailList as $k=>$v){
					if($k==$key){
						$arr = $v['row_access']?$v['row_access']:array();
						//修改当前key的列授权内容
						if(isset($arr['deny'])){
							if(isset($arr['deny']['userid']) && $arr['deny']['userid']!=""){
								$userArr = explode(",",$arr['deny']['userid']);
							}
							if(isset($arr['deny']['roleid']) && $arr['deny']['roleid']!=""){
								$roleArr = explode(",",$arr['deny']['roleid']);
							}
							if(isset($arr['deny']['expertid']) && $arr['deny']['expertid']!=""){
								$expertArr = explode(",",$arr['deny']['expertid']);
							}
						}
						
						if(isset($arr['allow'])){
							if(isset($arr['allow']['userid']) && $arr['allow']['userid']!=""){
								$allowuserArr = explode(",",$arr['allow']['userid']);
							}
							if(isset($arr['allow']['roleid']) && $arr['allow']['roleid']!=""){
								$allowroleArr = explode(",",$arr['allow']['roleid']);
							}
							if(isset($arr['allow']['expertid']) && $arr['allow']['expertid']!=""){
								$allowexpertArr = explode(",",$arr['allow']['expertid']);
							}
						}
					}
				}
			}
			//查询用户
			$UserDao = D("User");
			$map['id'] = 0;
			if($userArr){
				$map['id'] = array(' in ',$userArr);
			}
			$map['status'] = 1;
			$userlist=$UserDao->where($map)->field('id,name')->select();
			$this->assign('userlist',$userlist);
			
			if($allowuserArr){
				$allowmap['id'] = array(' in ',$allowuserArr);
			}
			$allowmap['status'] = 1;
			$allowuserlist=$UserDao->where($allowmap)->field('id,name')->select();
			if(in_array(0,$userArr)){
				$this->assign('alluser','1');
			}
			$this->assign('allowuserlist',$allowuserlist);
			
			//查询角色
			$RoleDao = M("rolegroup");
			if(!empty($roleArr)){
				$rolemap['id'] = array(' in ',$roleArr);
				$rolemap['status'] = 1;
				$rolelist=$RoleDao->where($rolemap)->field('id,name')->select();
				$this->assign('rolelist',$rolelist);
			}
			if($allowroleArr){
				$roleallowmap['id'] = array(' in ',$allowroleArr);
			}
			$roleallowmap['status'] = 1;
			$allowrolelist=$RoleDao->where($roleallowmap)->field('id,name')->select();
			if(in_array(0,$roleArr)){
				$this->assign('allrole','1');
			}
			$this->assign('allowrolelist',$allowrolelist);
			
			//查询专家
			$ExpertDao = M("mis_expert_list");
			if(!empty($expertArr)){
				$expertmap['id'] = array(' in ',$expertArr);
				$expertmap['status'] = 1;
				$expertlist=$ExpertDao->where($expertmap)->field('id,name')->select();
				$this->assign('expertlist',$expertlist);
			}
			if($allowexpertArr){
				$expertallowmap['id'] = array(' in ',$allowexpertArr);
			}
			$expertallowmap['status'] = 1;
			$allowexpertlist=$ExpertDao->where($expertallowmap)->field('id,name')->select();
			if(in_array(0,$expertArr)){
				$this->assign('allexpert','1');
			}
			$this->assign('allowexpertlist',$allowexpertlist);
			$this->display();
		}
	}

	function lookupRowAccess(){
		$step = intval($_REQUEST['step']);
		if( $step==2 ){
			$listLimitModel=D('MisSystemListLimit');
			$model = D('SystemConfigDetail');
			$modelName = $_POST['setmodule'];
			$issub = $_POST['issub'];
			if( $issub){
				$detailList = $model->getSubDetail($modelName,false);
			}else{
				$detailList = $model->getDetail($modelName,false);
			}
			if ($detailList) {
				$allowarr = $_POST["allow"];//传回来的键值是key
				
				$clearallow = $_POST["clearallow"];//传回来的是key
				$cleardeny = $_POST["cleardeny"];//传回来的是key
				$userid = $_POST["userid"];
				$roleid = $_POST["roleid"]; 
				$expertid = $_POST["expertid"];
				$globalrow = $_POST["globalrow"]; //传回来的是key
				
				foreach( $detailList as $k=>$v ){
						if(!empty($v["row_access"])){$arr =$v["row_access"];}else{$arr =array();}
						 if(in_array($k,$clearallow) ){
							$arr["allow"]=array();
							//删除数据库中允许字段
							$allowwhere['modelname']=$modelName;
							$listallowLimitInfo=$listLimitModel->where($allowwhere)->select();
							foreach ($listallowLimitInfo as $listkey=>$listval){
								$denyfields=json_decode($listdval['denyfields'], true);
								$allowfields=json_decode($listval['allowfields'], true);
								foreach ($allowfields as $afk=>$afv){
									if($v['name']==$afv[0]){
										unset($allowfields[$afk]);
									}
								}
								sort($allowfields);
								if(!empty($allowfields)){
									$allowdata['allowfields']=json_encode($allowfields);
									$allowdata['updateid']=$_SESSION[C('USER_AUTH_KEY')];
									$allowdata['updatetime']=time();
								}elseif(empty($allowfields) && empty($denyfields)){
									$allowlist=$listLimitModel->where('id='.$listval['id'])->delete();
								}else{
									$allowdata['allowfields']=null;
									$allowdata['updateid']=$_SESSION[C('USER_AUTH_KEY')];
									$allowdata['updatetime']=time();
								}
								$allowlist=$listLimitModel->where('id='.$listval['id'])->save($allowdata);
							}
							
						}
						if(in_array($k,$cleardeny) ){
							$arr["deny"]=array();
							//删除数据库中禁止字段
							$denywhere['modelname']=$modelName;
							$listdenyLimitInfo=$listLimitModel->where($denywhere)->select();
							foreach ($listdenyLimitInfo as $listdkey=>$listdval){
								$denyfields=json_decode($listdval['denyfields'], true);
								$allowfields=json_decode($listval['allowfields'], true);
								foreach ($denyfields as $dfk=>$dfv){
									if($v['name']==$dfv[0]){
										unset($denyfields[$dfk]);
									}
								}
								sort($denyfields);
								if(!empty($denyfields)){
									$denydata['denyfields']=json_encode($denyfields);
									$denydata['updateid']=$_SESSION[C('USER_AUTH_KEY')];
									$denydata['updatetime']=time();
								}elseif(empty($allowfields) && empty($denyfields)){
									$allowlist=$listLimitModel->where('id='.$listval['id'])->delete();
								}else{
									$denydata['denyfields']=null;
									$denydata['updateid']=$_SESSION[C('USER_AUTH_KEY')];
									$denydata['updatetime']=time();
								}
								
								$denylist=$listLimitModel->where('id='.$listdval['id'])->save($denydata);
							}
							
						} 
							
						if($allowarr[$k]==1){//允许
									if( isset($arr["allow"]) ){
										//用户
										if($userid){
											if( isset($arr["allow"]["userid"]) && $arr["allow"]["userid"]!=""){
												$arr1 = explode(",",$arr["allow"]["userid"]);
												$arr2 = explode(",",$userid);
												$arr3 = array_merge($arr1,$arr2);
												$arr["allow"]["userid"] = implode(",",array_unique($arr3));
												//取得当前用户允许字段
												$Data['allow'][]=array($v['name'],$v['showname']);
												
											}else{
												$arr["allow"]["userid"]= $userid;
												//取得当前用户允许字段
												$Data['allow'][]=array($v['name'],$v['showname']);
											}
										}
										//角色
										if($roleid){
											if( isset($arr["allow"]["roleid"]) && $arr["allow"]["roleid"]!=""){
												$arr1 = explode(",",$arr["allow"]["roleid"]);
												$arr2 = explode(",",$roleid);
												$arr3 = array_merge($arr1,$arr2);
												$arr["allow"]["roleid"] = implode(",",array_unique($arr3));
												//取得当前角色允许字段
												$roleData['allow'][]=array($v['name'],$v['showname']);
											}else{
												$arr["allow"]["roleid"]= $roleid;
												//取得当前角色允许字段
												$roleData['allow'][]=array($v['name'],$v['showname']);
											}
										}
										//专家
										if($expertid){
											if( isset($arr["allow"]["expertid"]) && $arr["allow"]["expertid"]!=""){
												$arr1 = explode(",",$arr["allow"]["expertid"]);
												$arr2 = explode(",",$expertid);
												$arr3 = array_merge($arr1,$arr2);
												$arr["allow"]["expertid"] = implode(",",array_unique($arr3));
												//取得当前专家允许字段
												$expertData['allow'][]=array($v['name'],$v['showname']);
											}else{
												$arr["allow"]["expertid"]= $expertid;
												//取得当前角色专家字段
												$expertData['allow'][]=array($v['name'],$v['showname']);
											}
										}
									}else{
										$arr["allow"]=array();
										if($userid) $arr["allow"]["userid"]= $userid;
										if($roleid) $arr["allow"]["roleid"]= $roleid;
										if($expertid) $arr["allow"]["expertid"]= $expertid;
										//取得当前用户允许字段
										$Data['allow'][]=array($v['name'],$v['showname']);
										//取得当前角色允许字段
										$roleData['allow'][]=array($v['name'],$v['showname']);
										//取得当前角色专家字段
										$expertData['allow'][]=array($v['name'],$v['showname']);
									}
									
									if( in_array($k,$globalrow) ){
										if($userid!=null){
											$arr["deny"] = isset($arr["deny"]) ? $arr["deny"]:array();
											$arr["deny"]["userid"]= ($arr["deny"]["userid"]!="")?$arr["deny"]["userid"].",0":0;
											//取得除当前用户以外的其他用户禁止字段
											$allData[]=array($v['name'],$v['showname']);
										}
										if($roleid!=null){
											$arr["deny"] = isset($arr["deny"]) ? $arr["deny"]:array();
											$arr["deny"]["roleid"]= ($arr["deny"]["roleid"]!="")?$arr["deny"]["roleid"].",0":0;
											//取得除当前角色以外的其他角色禁止字段
											$roleallData[]=array($v['name'],$v['showname']);
										}
										if($expertid!=null){
											$arr["deny"] = isset($arr["deny"]) ? $arr["deny"]:array();
											$arr["deny"]["expertid"]= ($arr["deny"]["expertid"]!="")?$arr["deny"]["expertid"].",0":0;
											//取得除当前专家以外的其他专家禁止字段
											$expertallData[]=array($v['name'],$v['showname']);
										}
									}
										$detailList[$k]["row_access"]=$arr;
								
							
						}else{//禁止
									if( isset($arr["deny"]) ){
										//用户
										if($userid){
											if( isset($arr["deny"]["userid"]) && $arr["deny"]["userid"]!=""){
												$arr1 = explode(",",$arr["deny"]["userid"]);
												$arr2 = explode(",",$userid);
												$arr3 = array_merge($arr1,$arr2);
												$arr["deny"]["userid"] = implode(",",array_unique($arr3));
												//取得当前用户禁止字段
												$Data['deny'][]=array($v['name'],$v['showname']);
											}else{
												$arr["deny"]["userid"]= $userid;
												//取得当前用户禁止字段
												$Data['deny'][]=array($v['name'],$v['showname']);
											}
										}
										//角色
										if($roleid){
											if(isset($arr["deny"]["roleid"]) && $arr["deny"]["roleid"]){
												$arr1 = explode(",",$arr["deny"]["roleid"]);
												$arr2 = explode(",",$roleid);
												$arr3 = array_merge($arr1,$arr2);
												$arr["deny"]["roleid"] = implode(",",array_unique($arr3));
												//取得当前角色禁止字段
												$roleData['deny'][]=array($v['name'],$v['showname']);
											}else{
												$arr["deny"]["roleid"]= $roleid;
												//取得当前角色禁止字段
												$roleData['deny'][]=array($v['name'],$v['showname']);
											}
										}
										//专家
										if($expertid){
											if(isset($arr["deny"]["expertid"]) && $arr["deny"]["expertid"]){
												$arr1 = explode(",",$arr["deny"]["expertid"]);
												$arr2 = explode(",",$expertid);
												$arr3 = array_merge($arr1,$arr2);
												$arr["deny"]["expertid"] = implode(",",array_unique($arr3));
												//取得当前专家禁止字段
												$expertData['deny'][]=array($v['name'],$v['showname']);
											}else{
												$arr["deny"]["expertid"]= $expertid;
												//取得当前专家禁止字段
												$expertData['deny'][]=array($v['name'],$v['showname']);
											}
										}
									}else{
										$arr["deny"]=array();
										if($userid) $arr["deny"]["userid"]= $userid;
										if($roleid) $arr["deny"]["roleid"]= $roleid;
										if($expertid) $arr["deny"]["expertid"]= $expertid;
										//取得当前用户禁止字段
										$Data['deny'][]=array($v['name'],$v['showname']);
										//取得当前角色禁止字段
										$roleData['deny'][]=array($v['name'],$v['showname']);
										//取得当前专家禁止字段
										$expertData['deny'][]=array($v['name'],$v['showname']);
									}
										$detailList[$k]["row_access"]=$arr;
								
							
							
					}
				}
				
				//对除当前用户以外的所有用户禁止
				if(!empty($allData) && $userid!=null){
					$allwhere['modelname']=$modelName;
					$allwhere['tableid']=0;
					$allwhere['tablename']='user';
					$allInfo=$listLimitModel->where($allwhere)->find();
					if($allInfo!=null){
						//对已有数据进行修改
						$alluserdata['modelname']=$modelName;
						$alluserdata['denyfields']=json_encode($allData);
						$alluserdata['updateid']=$_SESSION[C('USER_AUTH_KEY')];
						$alluserdata['updatetime']=time();
						$alluserList=$listLimitModel->where('id='.$allInfo['id'])->save($alluserdata);
					}else{
						$alluserdata['modelname']=$modelName;
						$alluserdata['tablename']='user';
						$alluserdata['denyfields']=json_encode($allData);
						$alluserdata['createid']=$_SESSION[C('USER_AUTH_KEY')];
						$alluserdata['createtime']=time();
						$alluserdata['tableid']='0';
						$alluserList=$listLimitModel->add($alluserdata);
					}
				} 
				//角色
				if(!empty($roleallData) && $roleid!=null){
					$roleallwhere['modelname']=$modelName;
					$roleallwhere['tableid']=0;
					$roleallwhere['tablename']='rolegroup';
					$roleallInfo=$listLimitModel->where($roleallwhere)->find();
					if($roleallInfo!=null){
						//对已有数据进行修改
						$rolealluserdata['modelname']=$modelName;
						$rolealluserdata['denyfields']=json_encode($roleallData);
						$rolealluserdata['updateid']=$_SESSION[C('USER_AUTH_KEY')];
						$rolealluserdata['updatetime']=time();
						$rolealluserList=$listLimitModel->where('id='.$roleallInfo['id'])->save($rolealluserdata);
					}else{
						$rolealluserdata['modelname']=$modelName;
						$rolealluserdata['tablename']='rolegroup';
						$rolealluserdata['denyfields']=json_encode($roleallData);
						$rolealluserdata['createid']=$_SESSION[C('USER_AUTH_KEY')];
						$rolealluserdata['createtime']=time();
						$rolealluserdata['tableid']='0';
						$rolealluserList=$listLimitModel->add($rolealluserdata);
					}
				}
				//专家
				if(!empty($expertallData) && $expertid!=null){
					$expertallwhere['modelname']=$modelName;
					$expertallwhere['tableid']=0;
					$expertallwhere['tablename']='mis_expert_list';
					$expertallInfo=$listLimitModel->where($expertallwhere)->find();
					if($expertallInfo!=null){
						//对已有数据进行修改
						$expertalluserdata['modelname']=$modelName;
						$expertalluserdata['denyfields']=json_encode($expertallData);
						$expertalluserdata['updateid']=$_SESSION[C('USER_AUTH_KEY')];
						$expertalluserdata['updatetime']=time();
						$expertalluserList=$listLimitModel->where('id='.$expertallInfo['id'])->save($expertalluserdata);
					}else{
						$expertalluserdata['modelname']=$modelName;
						$expertalluserdata['tablename']='mis_expert_list';
						$expertalluserdata['denyfields']=json_encode($expertallData);
						$expertalluserdata['createid']=$_SESSION[C('USER_AUTH_KEY')];
						$expertalluserdata['createtime']=time();
						$expertalluserdata['tableid']='0';
						$expertalluserList=$listLimitModel->add($expertalluserdata);
					}
				}
				
				
				//对当前用户允许或禁止
				$userarr = explode(",",$userid);
				if($userid!=null){
					foreach ($userarr as $uk=>$uval){
						$userdata['tableid']=$uval;
						$userwhere['modelname']=$modelName;
						$userwhere['tableid']=$uval;
						$userwhere['tablename']='user';
						$userInfo=$listLimitModel->where($userwhere)->find();
						if($userInfo!=null){
							//对已有数据进行修改
							$userdata['modelname']=$modelName;
							$userdata['allowfields']=json_encode($Data['allow']);
							$userdata['denyfields']=json_encode($Data['deny']);
							$userdata['updateid']=$_SESSION[C('USER_AUTH_KEY')];
							$userdata['updatetime']=time();
							$userList=$listLimitModel->where('id='.$userInfo['id'])->save($userdata);
						}else{
							$userdata['modelname']=$modelName;
							$userdata['tablename']='user';
							$userdata['allowfields']=json_encode($Data['allow']);
							$userdata['denyfields']=json_encode($Data['deny']);
							$userdata['createid']=$_SESSION[C('USER_AUTH_KEY')];
							$userdata['createtime']=time();
							$userList=$listLimitModel->add($userdata);
						}
					}
				}
				
				//对当前角色允许或禁止
				$rolearr = explode(",",$roleid);
				if($roleid!=null){
					foreach ($rolearr as $rk=>$rval){
						$roledata['tableid']=$rval;
						$rolewhere['modelname']=$modelName;
						$rolewhere['tableid']=$rval;
						$rolewhere['tablename']='rolegroup';
						$roleInfo=$listLimitModel->where($rolewhere)->find();
						if($roleInfo!=null){
							//对已有数据进行修改
							$roledata['modelname']=$modelName;
							$roledata['allowfields']=json_encode($roleData['allow']);
							$roledata['denyfields']=json_encode($roleData['deny']);
							$roledata['updateid']=$_SESSION[C('USER_AUTH_KEY')];
							$roledata['updatetime']=time();
							$roleList=$listLimitModel->where('id='.$roleInfo['id'])->save($roledata);
						}else{
							$roledata['modelname']=$modelName;
							$roledata['tablename']='rolegroup';
							$roledata['allowfields']=json_encode($roleData['allow']);
							$roledata['denyfields']=json_encode($roleData['deny']);
							$roledata['createid']=$_SESSION[C('USER_AUTH_KEY')];
							$roledata['createtime']=time();
							$roleList=$listLimitModel->add($roledata);
						}
					}
				}
				
				//对当前专家允许或禁止
				$expertarr = explode(",",$expertid);
				if($expertid!=null){
					foreach ($expertarr as $ek=>$eval){
						$expertdata['tableid']=$eval;
						$expertwhere['modelname']=$modelName;
						$expertwhere['tableid']=$eval;
						$expertwhere['tablename']='mis_expert_list';
						$expertInfo=$listLimitModel->where($expertwhere)->find();
						if($expertInfo!=null){
							//对已有数据进行修改
							$expertdata['modelname']=$modelName;
							$expertdata['allowfields']=json_encode($expertData['allow']);
							$expertdata['denyfields']=json_encode($expertData['deny']);
							$expertdata['updateid']=$_SESSION[C('USER_AUTH_KEY')];
							$expertdata['updatetime']=time();
							$expertList=$listLimitModel->where('id='.$expertInfo['id'])->save($expertdata);
						}else{
							$expertdata['modelname']=$modelName;
							$expertdata['tablename']='mis_expert_list';
							$expertdata['allowfields']=json_encode($expertData['allow']);
							$expertdata['denyfields']=json_encode($expertData['deny']);
							$expertdata['createid']=$_SESSION[C('USER_AUTH_KEY')];
							$expertdata['createtime']=time();
							$expertList=$listLimitModel->add($expertdata);
						}
					}
				}
				
				
				if( $issub){
					$model->setSubDetail($modelName, $detailList);
				}else{
					$model->setDetail($modelName, $detailList);
				}
				//删除缓存文件
				$p= C("DATA_CACHE_PATH")."Dynamicconf/Models/".$modelName;

				if(file_exists($p)){
					$FileUtil = new FileUtil();
					$boolean=$FileUtil->unlinkDir($p);
					if(!$boolean){
						$this->error("更新缓存文件失败");
					}
				}
				$this->success(L('_SUCCESS_'));exit;
			}else{
				$this->error("模块不存在!");
			}
		}else{
			$model = D('SystemConfigDetail');
			$modelName = $_GET['setmodule'];
			$issub = $_GET['issub'];
			if( $issub){
				$list = $model->getSubDetail($modelName,false);
			}else{
				$list = $model->getDetail($modelName,false);
			}
			if($list){
				$modeluser = M("user");
				foreach($list as $k=>$v){
					if( isset($v["row_access"]) ){
						$access = $v["row_access"];
						if($access["deny"]){
							$map["id"]=array("in",$access["deny"]['userid']);
							$list[$k]["row_access"]["deny_username"]=implode(",",$modeluser->where($map)->getField("name",true));
						}
						if($access["allow"]){
							$map["id"]=array("in",$access["allow"]['userid']);
							$list[$k]["row_access"]["allow_username"] =implode(",",$modeluser->where($map)->getField("name",true));
						}
					}
				}
			}
			$this->assign('list', $list);
			$this->assign("module",$modelName);
			$this->assign("issub",$issub);
			$this->display();
		}
	}

	/**
	 * @Title: lookuprolegroup
	 * @Description: todo(获取角色，部门，职级内容方法)
	 * @author liminggang
	 * @date 2013-11-26 上午9:34:13
	 * @throws
	 */
	public function lookuprolegroup(){
		//查询获取角色
		$obj=$_REQUEST['obj'];
		$stepType=$_REQUEST['stepType'];
		$this->assign('obj',$obj);
		$objname=$_REQUEST['objname'];
		$this->assign('objname',$objname);
		$this->assign('stepType',$stepType);
		$map = array();
		$searchby = $_POST["searchby"];
		$keyword= $_POST["keyword"];
		if($keyword){
			$map[$searchby] = array('like','%'.$keyword.'%');
			$this->assign('keyword',$keyword);
			$this->assign('searchby',$searchby);
		}
		$searchby=array(
				array("id" =>"name","val"=>"按角色名称"),
		);
		$this->assign("searchbylist",$searchby);
	
		$this->_list("rolegroup", $map);
		$this->display();
	}
	
	/**
	 * @Title: lookupexpert
	 * @Description: todo(获取专家列表方法)
	 */
	public function lookupExpert(){
		//查询获取专家
		$map = array();
		$map['status']=1;
		$searchby = $_POST["searchby"];
		$keyword= $_POST["keyword"];
		if($keyword){
			$map[$searchby] = array('like','%'.$keyword.'%');
			$this->assign('keyword',$keyword);
			$this->assign('searchby',$searchby);
		}
		$searchby=array(
				array("id" =>"name","val"=>"按专家名称"),
		);
		$this->assign("searchbylist",$searchby);
	
		$this->_list("mis_expert_list", $map);
		$this->display();
	}
	/**
	 * (non-PHPdoc)
	 * @see CommonAction::edit()
	 * 配置文件新增
	 */
	public function	setListFromData(){
		$model = D('SystemConfigDetail');
		$modelName = $_GET['model'];
		$dataInfo = $model->getDetail($modelName,false,"data");
		$listinfo = $model->getDetail($modelName,false,"list");
		foreach($listinfo as $key => $val){
			//如果存在值
			if(array_key_exists($val['name'],$dataInfo)){
				$dataInfo[$val['name']]=$val;
				$dataInfo[$val['name']]['checked']=1;				
			}
		}
		//对dataInfo进行循环
		foreach($dataInfo as $key =>$val){
			if(!isset($val['checked'])){
				$dataInfo[$key]['check']=0;
				//如果name为null，自动补全
				$dataInfo[$key]['name']=$val['name'];
				//如果table为null，自动补全
				$dataInfo[$key]['table']=$val['tablename'];
				//如果filed为null,自动补全
				$dataInfo[$key]['field']=$val['name'];
				//如果searchField为null,自动补全
				$dataInfo[$key]['searchField']=$val['tablename'].".".$val['name'];
				//如果showname为null,自动补全
				$dataInfo[$key]['showname']=$val['comment'];
			}		
		}
// 		print_r($dataInfo);
// 		exit;
		$models = D($modelName);
		$this->assign('list', $dataInfo);
		$this->assign('model', $modelName);
		$name = $model->getTitleName($modelName);
		$this->assign('name', $name);
		$this->display();		
	}
	/**
	 * (non-PHPdoc)
	 * @see CommonAction::edit()
	 * 配置文件新增
	 */
	public function add(){
		$this->assign("md",$_REQUEST['md']);
		//查询 排序
		$model=D("Dynamicconf");
		if(file_exists($model->GetFile($_REQUEST['md']))){
			$aryRule = require $model->GetFile($_REQUEST['md']);
		}
		Krsort($aryRule);
		foreach ($aryRule as $k=>$v){
			$id=$k+1;
			break;
		}
		//$id 为已有字段排序 最大序号
		$this->assign("id",$id+1);
		$this->display();
	}

	/**
	 * (non-PHPdoc)
	 * @see CommonAction::edit()
	 * 配置文件编辑
	 */
	public function edit(){
		$this->assign("md",$_REQUEST['md']);
		//查询 排序
		$model=D("Dynamicconf");
		if(file_exists($model->GetFile($_REQUEST['md']))){
			//加载
			$aryRule = require $model->GetFile($_REQUEST['md']);
		}
		$this->assign("vo",$aryRule[$_REQUEST['key']]);//基本信息
		$this->assign("func",$aryRule[$_REQUEST['key']]['func']);//关联函数
		foreach ($aryRule[$_REQUEST['key']]['funcdata'] as $k=>$v){
			$funcdata[$k+1]=implode(";", $v[0]);
		}
		$this->assign("id",$_REQUEST['key']);
// 		dump($funcdata);
		$this->assign("funcdata",$funcdata);//函数参数
		$this->display();
	}
	/**
	 * (non-PHPdoc)
	 * @see CommonAction::edit()
	 * 配置文件新增保存
	 */
	public function insert() {
// 		dump($_REQUEST);die;
		$model=D("Dynamicconf");
		$val=$model->GetRules($_POST['name'],$_REQUEST['md']);
		if($val){
			$this->error("该英文字段已创建，创建失败");
			exit;
		}
		//组装数据
		$data=array(
				'name' => $_POST['name'],
				'showname' => $_POST['showname'],
				'shows' => $_POST['shows'],
				'widths' => $_POST['widths'],
				'sorts' => $_POST['sorts'],
				'models' => $_POST['models'],
				'sortname' => $_POST['sortname'],
				'sortnum' => $_POST['sortnum'],
				'issearch' => $_POST['issearch'],
				'searchField' => $_POST['searchField'],
				'table' => $_POST['table'],
				'field' => $_POST['field'],
				'conditions' => $_POST['conditions'],
				'type' => $_POST['type'],
				'isallsearch' => $_POST['isallsearch'],
				'searchsortnum' => $_POST['searchsortnum'],
				'status' => $_POST['status'],
				'helpvalue' => $_POST['helpvalue'],
		);
		//判断 有关联函数
		if($_POST['arr_func'][0]){
			foreach ($_POST['arr_func'] as $k=>$v){
				$data['func'][]=explode(",", $_POST['arr_func'][$k]);
				$data['funcdata'][]=array("0"=>explode(";",$_POST['arr_funcdata'][$k]));
			}
		}
		//加载配置文件
		if(file_exists($model->GetFile($_REQUEST['md']))){
			$aryRule = require $model->GetFile($_REQUEST['md']);
		}
		//头部变为主key值
		$aryRule[$_POST['name']]=$data;
		Ksort($aryRule);
// 		dump($aryRule);die;
		$model->SetRules($aryRule,$_REQUEST['md']);//写入文件
		$this->success ( L('_SUCCESS_') );
	}
	/**
	 * (non-PHPdoc)
	 * @see CommonAction::edit()
	 * 配置文件修改保存
	 */
	public function updatelist(){
		$model=D("Dynamicconf");
		if(file_exists($model->GetFile($_REQUEST['md']))){
			$aryRule = require $model->GetFile($_REQUEST['md']);
		}
         $data=array();
			$data['name']     = $_POST['name'];
			$data['showname'] = $_POST['showname'];
			$data['shows']    = $_POST['shows'];
			$data['widths']   = $_POST['widths'];
			$data['sorts']    = $_POST['sorts'];
			$data['models']   = $_POST['models'];
			$data['sortname'] = $_POST['sortname'];
			$data['sortnum']  = $_POST['sortnum'];
			$data['issearch'] = $_POST['issearch'];
			$data['searchField'] = $_POST['searchField'];
			$data['table']    = $_POST['table'];
			$data['field']    = $_POST['field'];
			$data['conditions'] = $_POST['conditions'];
			$data['type']     = $_POST['type'];
			$data['isallsearch'] = $_POST['isallsearch'];
			$data['searchsortnum'] = $_POST['searchsortnum'];
			$data['status']    = $_POST['status'];
			$data['helpvalue'] = $_POST['helpvalue'];
		foreach ($aryRule as  $key => $val) {
			if ($key == $_POST['id'] || $val['name'] == $_POST['id']) {
				unset($aryRule[$key]);
				$aryRule[$_POST['id']] = $data;
			
			}
		}
		//判断 有关联函数
		if($_POST['arr_func'][0]){
			foreach ($_POST['arr_func'] as $k=>$v){
				$aryRule[$_POST['name']]['func'][]=explode(",", $_POST['arr_func'][$k]);
				$aryRule[$_POST['name']]['funcdata'][]=array("0"=>explode(";",$_POST['arr_funcdata'][$k]));
			}
		}
		$model->SetRules($aryRule,$_REQUEST['md']);
		$this->success ( L('_SUCCESS_') );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CommonAction::edit()
	 * 配置文件修改保存
	 */
	public function updatelistFromData(){
		$model=D("Dynamicconf");
		if(file_exists($model->GetFile($_REQUEST['md']))){
			$aryRule = require $model->GetFile($_REQUEST['md']);
		}
		foreach ($aryRule as $key => $val) {
			if (array_key_exists($key,$_REQUEST['id']) && array_key_exists($val['name'],$_REQUEST['id'])){
				//去掉以前的数据信息
				unset($aryRule[$key]);
				//赋予最新的数据值
				$aryRule[$key] = array(
						'name'     => $_POST['name'][$val['name']],
						'showname' => $_POST['showname'][$val['name']],
						'shows'    => $_POST['shows'][$val['name']],
						'widths'   => $_POST['widths'][$val['name']],
						'sorts'    => $_POST['sorts'][$val['name']],
						'models'   => $_POST['models'][$val['name']],
						'sortname' => $_POST['sortname'][$val['name']],
						'sortnum'  => $_POST['sortnum'][$val['name']],
						'issearch' => $_POST['issearch'][$val['name']],
						'searchField' => $_POST['searchField'][$val['name']],
						'table'    => $_POST['table'][$val['name']],
						'field'    => $_POST['field'][$val['name']],
						'conditions' => $_POST['conditions'][$val['name']],
						'type'     => $_POST['type'][$val['name']],
						'isallsearch' => $_POST['isallsearch'][$val['name']],
						'searchsortnum' => $_POST['searchsortnum'][$val['name']],
						'status'    => $_POST['status'][$val['name']],
						'helpvalue' => $_POST['helpvalue'][$val['name']],
				);
				//去掉POST已赋值的数据信息
				unset($_REQUEST['id'][$val['name']]);
			}
		}
		
		foreach($_REQUEST['id'] as $key => $val){
			//赋予最新的数据值
			$aryRule[$key] = array(
					'name'     => $_POST['name'][$key],
					'showname' => $_POST['showname'][$key],
					'shows'    => $_POST['shows'][$key],
					'widths'   => $_POST['widths'][$key],
					'sorts'    => $_POST['sorts'][$key],
					'models'   => $_POST['models'][$key],
					'sortname' => $_POST['sortname'][$key],
					'sortnum'  => $_POST['sortnum'][$key],
					'issearch' => $_POST['issearch'][$key],
					'searchField' => $_POST['searchField'][$key],
					'table'    => $_POST['table'][$key],
					'field'    => $_POST['field'][$key],
					'conditions' => $_POST['conditions'][$key],
					'type'     => $_POST['type'][$key],
					'isallsearch' => $_POST['isallsearch'][$key],
					'searchsortnum' => $_POST['searchsortnum'][$key],
					'status'    => $_POST['status'][$key],
					'helpvalue' => $_POST['helpvalue'][$key],
			);
		}
		// 		//判断 有关联函数
		// 		if($_POST['arr_func'][0]){
		// 			foreach ($_POST['arr_func'] as $k=>$v){
		// 				$aryRule[$_POST['name']]['func'][]=explode(",", $_POST['arr_func'][$k]);
		// 				$aryRule[$_POST['name']]['funcdata'][]=array("0"=>explode(";",$_POST['arr_funcdata'][$k]));
		// 			}
		// 		}
		// 		print_r($aryRule);
		// 		exit;
// 		$model->SetRules($aryRule,$_REQUEST['md']);

		
		$autoformModel=D('Autoform');
		$dir = '/Models/';
		$listincpath = $dir.$_REQUEST['md'].'/list.inc.php';
		$autoformModel->setPath($listincpath);
		$autoformModel->SetListinc($aryRule,'1');
		
		$this->success ( L('_SUCCESS_') );
	}
	/**
	 * (non-PHPdoc)
	 * @see CommonAction::edit()
	 * 配置文件删除
	 */
	public function delete(){
// 		dump($_REQUEST);die;
		$model=D("Dynamicconf");
		if(file_exists($model->GetFile($_REQUEST['md']))){
			$aryRule = require $model->GetFile($_REQUEST['md']);
		}
		foreach ($aryRule as $key => $val) {
			if ($key == $_REQUEST['key']) {
				unset($aryRule[$_REQUEST['key']]);
			}
		}
		$model->SetRules($aryRule,$_REQUEST['md']);
		$this->success ( L('_SUCCESS_') );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CommonAction::edit()
	 * 配置文件生成
	 */	
	public function setDynamiccof($showret=false){
		
		$model=$_REQUEST['model'];
		$list=D("Dynamicconf")->setTableInfo($model);
 		$list=D("Dynamicconf")->setListInfo($model);
		$list=D("Dynamicconf")->setFormInfo($model);
		if(!$showret){		
			$this->success ( L('_SUCCESS_') );
		}
	} 
	
	
}
?>
