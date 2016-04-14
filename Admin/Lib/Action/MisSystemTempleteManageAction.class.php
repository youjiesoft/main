<?php
/**
 * @Title: MisSystemTempleteManageAction
 * @Package package_name
 * @Description: todo(动态表单_自动生成-模板管理)
 * @author 管理员
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015-01-27 10:48:48
 * @version V1.0
*/
class MisSystemTempleteManageAction extends MisSystemTempleteManageExtendAction {
	public function _filter(&$map){
	$fieldtype=$_REQUEST['fieldtype'];
	if($fieldtype){
		$map[$fieldtype]=$_REQUEST[$fieldtype];
		$this->assign("fieldtype",$fieldtype);
		$this->assign("fieldtypeval",$_REQUEST[$fieldtype]);
	}
		if ($_SESSION["a"] != 1){
			$map['status']=array("gt",-1);
		}
		$this->_extend_filter(&$map);
	}
	
	
	/**
	 * @Title: edit
	 * @Description: todo(重写父类编辑函数)
	 * @author 管理员
	 * @date 2015-01-27 10:48:48
	 * @throws 
	*/
	function edit($isdisplay=1){
		$mainTab = 'mis_system_templete_manage';
		//获取当前控制器名称
		$name=$this->getActionName();
		$model = D("MisSystemTempleteManageView");
		//获取当前主键
		$map[$mainTab.'.id']=$_REQUEST['id'];
		$vo = $model->where($map)->find();
		if(method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		//读取动态配制
		$this->getSystemConfigDetail($name);
		//扩展工具栏操作
		$scdmodel = D('SystemConfigDetail');
		// 上一条数据ID
		$map['id'] = array("lt",$id);
		$updataid = $model->where($map)->order('id desc')->getField('id');
		$this->assign("updataid",$updataid);
		// 下一条数据ID
		$map['id'] = array("gt",$id);
		$downdataid = $model->where($map)->getField('id');
		$this->assign("downdataid",$downdataid);
		//lookup带参数查询
		$module=A($name);
		if (method_exists($module,"_after_edit")) {
			call_user_func(array(&$module,"_after_edit"),&$vo);
		}
		$this->assign( 'vo', $vo );
		if($isdisplay)
		$this->display ();
	}
	public function index() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		$modelName = $_REQUEST['model'];
		$modelId   = $_REQUEST['modelid'];
		if($modelName){
			$map['modelname']=$modelName;
		}
		if($modelId){
			$map['modelid']=$modelId;
		}
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		if($_REQUEST['projectid']){
			$map['projectid'] = $_REQUEST['projectid'];
		}
		if($_REQUEST['projectworkid']){
			$map['projectworkid'] = $_REQUEST['projectworkid'];
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
			$this->_list ( $name, $map );
		}
		//begin
		$scdmodel = D('SystemConfigDetail');
		//读取列名称数据(按照规则，应该在index方法里面)
		$detailList = $scdmodel->getDetail($name);
		if(file_exists(ROOT . '/Dynamicconf/Models/'.$name.'/form.inc.php')){
			$anameList = require ROOT . '/Dynamicconf/Models/'.$name.'/form.inc.php';
			//dump($anameList);
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
		//dump($detailList);
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		//扩展工具栏操作
		$toolbarextension = $scdmodel->getDetail($name,true,'toolbar');
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		//end
	
		if( intval($_POST['dwzloadhtml']) ){
			$this->display("dwzloadindex");exit;
		}
		//首页收件箱调用方法，为ajax调用
		if ($_GET['type'] == "ajaxcall") {
			$this->display ("ajax_index");exit;
		}
		if($_REQUEST['jump'] == "jump"){
			$this->display('indexview');exit;
		}
		$this->display ();
		return;
	}
	
	/**
	 * @Title: _before_index
	 * @Description: todo(前置index函数)
	 * @author 管理员
	 * @date 2015-01-27 10:48:48
	 * @throws 
	*/
	function _before_index() {
		if($_REQUEST['jump']=='jump'){
			$modelName = $_REQUEST['model'];
			$modelId   = $_REQUEST['modelid'];
			$model = D($modelName);
			$tablename = $model->getTableName();
		}else{
			//$newAryRule = $aryRule;/*
			/* 建左边树
			 */
			$model = D('SystemConfigNumber');
			$list = $model->getRoleTree('MisSystemTempleteManageindexview');
			$ListInfo=json_decode($list,true);
			foreach ($ListInfo as $key=>$val){
				if($val['url']){
					$ListInfo[$key]['url']=$val['url'].'/modelid/'.$val['id'];
				}
			}
			$List=json_encode($ListInfo);
	 		$this->assign('returnarr',$List);
 		
			$firstDetail=$model->firstDetail;
			$this->assign('check',$firstDetail['check']);
			$modelName = $firstDetail['name'];
			$_REQUEST['model'] = $_REQUEST['model']?$_REQUEST['model']:$modelName;
			if($_REQUEST['model']){
				$check = getFieldBy($_REQUEST['model'], 'name', 'id', 'node');
				$modelId=$check;
				$this->assign('check',$check);
				$modelName = $_REQUEST['model'];
			}
			
			$modeltotable = D($firstDetail['name']);
			$tablename=$modeltotable->getTableName();
		}
		$this->assign('modelid',$modelId);
		$this->assign('modelname',$modelName);
		
		//查询绑定数据源
		$this->getDateSoure();
		$this->_extend_before_index();
		
	}
	/**
	 * @Title: _before_edit
	 * @Description: todo(前置编辑函数)
	 * @author 管理员
	 * @date 2015-01-27 10:48:48
	 * @throws 
	*/
	function _before_edit(){
		$modelName = $_REQUEST['modelname'];
		$modelId   = $_REQUEST['modelid'];
		$this->assign('modelid',$modelId);
		$this->assign('modelname',$modelName);
		$this->_extend_before_edit();
	}
	/**
	 * @Title: _before_insert
	 * @Description: todo(前置添加函数)
	 * @author 管理员
	 * @date 2015-01-27 10:48:48
	 * @throws 
	*/
	function _before_insert(){
		$modelName=$this->getActionName();
		$model=D($modelName);
		$map['modelname'] = $_REQUEST['modelname'];
		$map['modelid']   = $_REQUEST['modelid'];
		$zhuangtai=$_REQUEST['zhuangtai'];
		if($zhuangtai==1){
			$list=$model->where($map)->field('id')->select();
			if($list){
				foreach ($list as $k=>$val){
					$Id[]=$val['id'];
				}
			}
			$savemap['id']=array('in',$Id);
			$saveData['zhuangtai']=0;
			$saveList=$model->where($savemap)->save($saveData);
		}
		$_POST['showrules']=html_entity_decode($_POST['showrules']);
		$this->_extend_before_insert();
	}
	/**
	 * @Title: _before_update
	 * @Description: todo(前置修改函数)  
	 * @author 管理员
	 * @date 2015-01-27 10:48:48
	 * @throws
	*/
	function _before_update(){
		$modelName=$this->getActionName();
		$model=D($modelName);
		$map['modelname'] = $_REQUEST['modelname'];
		$map['modelid']   = $_REQUEST['modelid'];
		$zhuangtai=$_REQUEST['zhuangtai'];
		if($zhuangtai==1){
			$list=$model->where($map)->field('id')->select();
			if($list){
				foreach ($list as $k=>$val){
					$Id[]=$val['id'];
				}
			}
			$savemap['id']=array('in',$Id);
			$saveData['zhuangtai']=0;
			$saveList=$model->where($savemap)->save($saveData);
		}
		$_POST['showrules']=html_entity_decode($_POST['showrules']);
		$this->_extend_before_update();
	}
	/**
	 * @Title: _after_edit
	 * @Description: todo(后置编辑函数)
	 * @author 管理员
	 * @date 2015-01-27 10:48:48
	 * @throws 
	*/
	function _after_edit($vo){
		$this->getAttachedRecordList($vo['id']);
		$this->getAttachedRecordList($vo['id']);
		$this->_extend_after_edit($vo);
	}
	/**
	 * @Title: _after_insert
	 * @Description: todo(后置insert函数)  
	 * @author 管理员
	 * @date 2015-01-27 10:48:48
	 * @throws
	*/
	function _after_insert($id){
		$this->_extend_after_insert($id);
	}
	/**
	 * @Title: _before_add
	 * @Description: todo(前置add函数)  
	 * @author 管理员
	 * @date 2015-01-27 10:48:48
	 * @throws
	*/
	function _before_add(){
		$modelName = $_REQUEST['modelname'];
		$modelId   = $_REQUEST['modelid'];
		$this->assign('modelid',$modelId);
		$this->assign('modelname',$modelName);
		//获取节点名称
		$nodeModel=D('Node');
		$nodeMap['id']=$modelId;
		$nodelist=$nodeModel->where($nodeMap)->find();
		$this->assign('nodeList',$nodelist);
		//得到编号
		$model=D('MisSystemTempleteManage');
		$temMap['modelid']=$modelId;
		$temMap['modelname']=$modelName;
		$temOrderno=$model->where($temMap)->max('orderno');
		if($temOrderno==null){
			$orderno='00001';
		}else{
			$orderno=$temOrderno+1;
			$orderno=sprintf("%05d", $orderno);
		}
		$this->assign('orderno',$orderno);
		$this->_extend_before_add();
	}
	/**
	 * @Title: _after_update
	 * @Description: todo(后置update函数)  
	 * @author 管理员
	 * @date 2015-01-27 10:48:48
	 * @throws
	*/
	function _after_update(){
		$this->_extend_after_update();
	}

	function delsubinfo(){
		$table = $_POST['table'];
		$id = intval($_POST['id']);
		if($table){
			$model = M($table);
			$map['id'] = array('eq',$id);
			$model->where($map)->delete();
			$this->success('数据成功删除');
		}
	}
	/**
	 * 清除当前数据
	 */
	function lookupDelete(){
		$id = intval($_POST['id']);
		if($id){
			$model = M('mis_system_templete_label');
			$map['id'] = array('eq',$id);
			$list=$model->where($map)->delete();
			if($list){
				echo json_encode('1');
			}
		}
	}
	/**
	 * @Title: lable
	 * @Description: todo(标签管理)
	 * @author xiayq
	 * @date 2015-01-28 10:48:48
	 * @throws
	 */
	function label(){
		$name = $_REQUEST['modelname'];
		//查询是否是审批模板
		$NodeModel=D('Node');
		$nodeMap['isprocess']=1;
		$nodeMap['name']=$name;
		$nodeList=$NodeModel->where($nodeMap)->select();
		if($nodeList!=null && $nodeList!=false){
			//查询当前的审批节点
			$process_infoModel=M('process_info');
			$infoMap['nodename']=$name;
			$infoMap['default']=1;
			$infoMap['catgory']=1;
			$infoList=$process_infoModel->where($infoMap)->find();
			$process_relationModel=D('process_relation');
			$relMap['tablename']='process_info';
			$relMap['pinfoid']=$infoList['id'];
			$relMap['flowtype']=2;
			$relList=$process_relationModel->where($relMap)->field("id,name,flowid")->select();
			foreach ($relList as $rk=>$rv){
				$relList[$rk]['id'] = substr(md5($rv['id']),0,8);
				$history = array();
				$history[] = array(
						'id' =>substr(md5($rv['flowid']."HS_name"),0,8),
						'name' =>"节点名称【{$rv['name']}】",
				);
				$history[] = array(
						'id' =>substr(md5($rv['flowid']."HS_doinfo"),0,8),
						'name' =>"审核意见【{$rv['name']}】",
				);
				$history[] = array(
						'id' =>substr(md5($rv['flowid']."HS_dotime"),0,8),
						'name' =>"处理时间【{$rv['name']}】",
				);
				$history[] = array(
						'id' =>substr(md5($rv['flowid']."HS_userid"),0,8),
						'name' =>"处理人【{$rv['name']}】",
				);
				$relList[$rk]['biaoqian'] = $history;
			}
			$this->assign('relList',$relList);
		}
		$model = D($name);
		$list = $model->where()->find();
		$isedit=$_REQUEST['isedit'];
		if($isedit){
			$datatable=$_POST['datatable'];
			$modelname=$_REQUEST['modelname'];
			$modelid=$_REQUEST['modelid'];
			$exportData = $this->getReportData(array(),$name);
			if(!empty($datatable)){
				$model = D('MisSystemTempleteLabel');
				$insertData = array();// 数据添加缓存集合
				$updateData = array();// 数据修改缓存集合
				if($datatable){
					foreach($datatable as $key=>$val){
						foreach($val as $k=>$v){
							$v['modelname']=$modelname;
							$v['modelid']=$modelid;
							//去除空格
							$formula=trim($v['formula']);
							//判断是否有公式
							if($formula!=null){
								//替换
								if(!preg_match('/datatable/',$v['name'])){
									$v['formula']=str_replace('}',']',str_replace('${','[',$formula));
								}
								$id=$v['id'];
								if($id){
									$updateData[$k][]=$v;
								}else{
									$insertData[$k][]=$v;
								}
							}
						}
					}
				}
				//数据处理
				if($insertData){
					foreach($insertData as $k=>$v){
						foreach($v as $key=>$val){
							if($val['formula']!=null){
								$id=$model->add($val);
							}
						}
					}
				}
				if($updateData){
					foreach($updateData as $k=>$v){
						foreach($v as $key=>$val){
								$model->save($val);
							
						}
					}
				}
				$this->success('保存成功');
			}
			
		}else{
			$exportData = $this->getReportData(array(),$name,true);
			//查询添加的标签
			$map['modelname']=$_REQUEST['modelname'];
			$map['modelid']=$_REQUEST['modelid'];
			$labelModel=D('MisSystemTempleteLabel');
			$labelList=$labelModel->where($map)->select();
			foreach ($exportData as $exk=>$exVal){
				 foreach ($labelList as $lk=>$lval){
				 	foreach ($exVal['value'] as $exvKey=>$exvVal){
				 		if($exvVal['name']==$lval['name']){
				 			$exportData[$exk]['value'][$exvKey]['id']=$lval['id'];
				 			//unset($exportData[$exk]['value'][$exvKey]);
				 			unset($labelList[$lk]);
				 		}
				 	}
				} 
			}
			$exportKeyList=array_keys($exportData);
			$exportKey=$exportKeyList[0];
			foreach ($labelList as $labk=>$labval){
				//dump($labval['name']);
				$endExportData[$labval['name']]['name']=$labval['name'];
				$endExportData[$labval['name']]['showname']=$labval['showname'];
				$endExportData[$labval['name']]['formula']=$labval['formula'];
				$endExportData[$labval['name']]['id']=$labval['id'];
				$endExportData[$labval['name']]['colORrow']=$labval['colORrow'];
				$endExportData[$labval['name']]['is_datatable']=$labval['is_datatable'];
				//替换
				$labelList[$labk]['formula']=str_replace(']','}',str_replace('[','${',$labval['formula']));
			}
			//print_r($exportData);
			//查询id是否有值
			foreach ($exportData as $exk=>$exVal){
				foreach($exVal['value'] as $kk=>$vv){
					if($vv['id']==null || $vv['id']==''){
						$exportData[$exk]['value'][$kk]['id']=0;
					}
				}
			}
			$this->assign('endExportData',$endExportData);
			$this->assign('exportData',$exportData);
			$this->assign('modelname',$map['modelname']);
			$this->assign('modelid',$map['modelid']);
			$this->assign('labelList',$labelList);
			$this->display();
		}
	}
	
	/**
	 * @Title: lookupEditTable
	 * @Description: 编辑表格
	 * @author xiayq
	 * @date 2015-02-1 10:48:48
	 * @throws
	 */
	
	function lookupEditTable(){
		$isedit=$_REQUEST['isedit'];
		if($isedit){
			$id=$_REQUEST['id'];
			$relstr['showtype']=$_REQUEST['showtype'];
			$relstr['showtitle']=$_REQUEST['showtitle'];
			$datatable=$_REQUEST['datatable'];
			$relstr['datatable']=$datatable;
			//读取原有标签
			$modelname=$_REQUEST['modelname'];
			$modelid=$_REQUEST['modelid'];
			//内嵌表显示字段
			$showfield=json_encode($_REQUEST['showfield']);
			//字段宽度 by xyz 2016-2-22
			$fieldwidth = json_encode($_REQUEST['fieldwidth']);
			$ziti=$_REQUEST['ziti'];
			$zihao=$_REQUEST['zihao'];
			$hangjianju=$_REQUEST['hangjianju'];
			
			
			$exportData = $this->getReportData(array(),$modelname);
			//查询添加的标签
			$map['modelname']=$modelname;
			$map['modelid']=$modelid;
			$labelModel=D('MisSystemTempleteLabel');
			$labelList=$labelModel->where($map)->select();
			foreach ($exportData as $exk=>$exVal){
				foreach ($labelList as $lk=>$lval){
					if($exk==$lval['name']){
						unset($exportData[$exk]);
					}
				}
			}
			//将添加的标签放入原有标签中
			foreach ($labelList as $labk=>$labval){
				$exportData[$labval['name']]['name']=$labval['name'];
				$exportData[$labval['name']]['showname']=$labval['showname'];
				$exportData[$labval['name']]['formula']=$labval['formula'];
				$exportData[$labval['name']]['id']=$labval['id'];
			}
			//查询datatable里面名称是否重名
			foreach ($datatable as $dk=>$dval){
				//判断标签名称是否在原标签中
				$inArrayKey=array_key_exists($dval['name'],$exportData);
				//判断添加的标签是否重名
				$nameArr=array();
				foreach ($datatable as $dlk=>$dlval){
					if($dlk!=$dk){
						$nameArr[]=$dlval['name'];
					}
				}
				$inArray=in_array($dval['name'], $nameArr);
				if($inArrayKey || $inArray){
					$this->error($dval['name']."标签名称重名");
				}
			}
			$trArr['arrlist']='###'.json_encode($relstr).'###';
			$labelMap['id']=$id;
			$data['modelname']=$modelname;
			$data['modelid']=$modelid;
			$data['formula']=$trArr['arrlist'];
			$data['name']=$_REQUEST['name'];
			$data['showname']=$_REQUEST['showname'];
			$data['is_datatable']=1;
			$data['showfield']=$showfield;
			$data['fieldwidth']=$fieldwidth;
			$data['ziti']=$ziti;
			$data['zihao']=$zihao;
			$data['hangjianju']=$hangjianju;
			if($id){
				//修改
				$data['updatetime']=time();
				$data['updateid']=$_SESSION [C ( 'USER_AUTH_KEY' )];
				$list=$labelModel->where($labelMap)->save($data);
				$url=__URL__.'/lookupEditTable/showname/'.$_REQUEST['showname'].'/id/'.$id.'/name/'.$_REQUEST['name'].'/modelname/'.$modelname.'/modelid/'.$modelid;
				$jsondata['id']=$data['name'];
				$jsondata['url']=$url;
				$jsonArr=json_encode($jsondata);
				if($list){
					$this->success('保存成功','',$jsonArr);
				}else{
					$this->error('保存失败');
				}
			}else{
				//添加
				$data['createtime']=time();
				$data['createid']=$_SESSION [C ( 'USER_AUTH_KEY' )];
				$list=$labelModel->add($data);
				$url=__URL__.'/lookupEditTable/showname/'.$_REQUEST['showname'].'/id/'.$list.'/name/'.$_REQUEST['name'].'/modelname/'.$modelname.'/modelid/'.$modelid;
				$jsondata['id']=$data['name'];
				$jsondata['url']=$url;
				$jsonArr=json_encode($jsondata);
				if($list){
					$this->success('保存成功','',$jsonArr);
				}else{
					$this->error('保存失败');
				}
			}
		}else{
			$labmap['id']=$_REQUEST['id'];
			$modelname=$_REQUEST['modelname'];
			$modelid=$_REQUEST['modelid'];
			//查询编辑表格数据a
			$labModel=D('MisSystemTempleteLabel');
			$labInfo=$labModel->where($labmap)->find();
			//echo $labModel->getlastsql();
			$formula=$labInfo['formula'];
			
			//替换###
			$formula=str_replace('&#39;','\'',str_replace('&quot;','\"',str_replace('###', '', $formula)));
			$formula1=json_decode($formula,true);
			$tablelist=$formula1['datatable'];
			$showname=$_REQUEST['showname'];
			$name=$_REQUEST['name'];
			$showfield=$labInfo['showfield'];
			$formula1['ziti']=$labInfo['ziti'];
			$formula1['zihao']=$labInfo['zihao'];
			$formula1['hangjianju']=$labInfo['hangjianju'];
			$this->assign('showfield',$showfield);
			$this->assign('fieldwidth',$labInfo['fieldwidth']);
			$this->assign('id',$labmap['id']);
			$this->assign('modelname',$modelname);
			$this->assign('tablelist',$tablelist);
			$this->assign('modelid',$modelid);
			$this->assign('showname',$showname);
			$this->assign('name',$name);
			$this->assign('formula',$formula1);
			$this->display();
		}
		
	}
	
	
	/**
	 * @Title: lookupEditColORrow
	 * @Description: 编辑表格
	 * @author xiayq
	 * @date 2015-02-1 10:48:48
	 * @throws
	 */
	
	function lookupEditColORrow(){
		$isedit=$_REQUEST['isedit'];
		if($isedit){
			$id=$_REQUEST['id'];
			$relstr['showtype']=$_REQUEST['showtype'];
			//$relstr['showtitle']=$_REQUEST['showtitle'];
			$datatable=$_REQUEST['datatable'];
			$relstr['datatable']=$datatable;
			//读取原有标签
			$modelname=$_REQUEST['modelname'];
			$modelid=$_REQUEST['modelid'];
			$exportData = $this->getReportData(array(),$modelname);
			//查询添加的标签
			$map['modelname']=$modelname;
			$map['modelid']=$modelid;
			$labelModel=D('MisSystemTempleteLabel');
			$labelList=$labelModel->where($map)->select();
			foreach ($exportData as $exk=>$exVal){
				foreach ($labelList as $lk=>$lval){
					if($exk==$lval['name']){
						unset($exportData[$exk]);
					}
				}
			}
			//将添加的标签放入原有标签中
			foreach ($labelList as $labk=>$labval){
				$exportData[$labval['name']]['name']=$labval['name'];
				$exportData[$labval['name']]['showname']=$labval['showname'];
				$exportData[$labval['name']]['formula']=$labval['formula'];
				$exportData[$labval['name']]['id']=$labval['id'];
			}
			//查询datatable里面名称是否重名
			foreach ($datatable as $dk=>$dval){
				//判断标签名称是否在原标签中
				$inArrayKey=array_key_exists($dval['name'],$exportData);
				//判断添加的标签是否重名
				$nameArr=array();
				foreach ($datatable as $dlk=>$dlval){
					if($dlk!=$dk){
						$nameArr[]=$dlval['name'];
					}
				}
				$inArray=in_array($dval['name'], $nameArr);
				if($inArrayKey || $inArray){
					$this->error($dval['name']."标签名称重名");
				}
			}
			$trArr['arrlist']='###'.json_encode($relstr).'###';
			$labelMap['id']=$id;
			$data['modelname']=$modelname;
			$data['modelid']=$modelid;
			$data['colORrow']=1;
			$data['formula']=$trArr['arrlist'];
			$data['name']=$_REQUEST['name'];
			$data['showname']=$_REQUEST['showname'];
			if($id){
				//修改
				$data['updatetime']=time();
				$data['updateid']=$_SESSION [C ( 'USER_AUTH_KEY' )];
				$list=$labelModel->where($labelMap)->save($data);
				$url=__URL__.'/lookupEditColORrow/showname/'.$_REQUEST['showname'].'/id/'.$id.'/name/'.$_REQUEST['name'].'/modelname/'.$modelname.'/modelid/'.$modelid;
				$jsondata['id']=$data['name'];
				$jsondata['url']=$url;
				$jsonArr=json_encode($jsondata);
				if($list){
					$this->success('保存成功','',$jsonArr);
				}else{
					$this->error('保存失败');
				}
			}else{
				//添加
				$data['createtime']=time();
				$data['createid']=$_SESSION [C ( 'USER_AUTH_KEY' )];
				$list=$labelModel->add($data);
				$url=__URL__.'/lookupEditColORrow/showname/'.$_REQUEST['showname'].'/id/'.$list.'/name/'.$_REQUEST['name'].'/modelname/'.$modelname.'/modelid/'.$modelid;
				$jsondata['id']=$data['name'];
				$jsondata['url']=$url;
				$jsonArr=json_encode($jsondata);
				if($list){
					$this->success('保存成功','',$jsonArr);
				}else{
					$this->error('保存失败');
				}
			}
				
			/* $trArr['trname']=$_REQUEST['trname'];
			 $jsonArr=json_encode($trArr); */
			//$this->success('保存成功','',$jsonArr);
				
		}else{
			$labmap['id']=$_REQUEST['id'];
			$modelname=$_REQUEST['modelname'];
			$modelid=$_REQUEST['modelid'];
			//查询编辑表格数据a
			$labModel=D('MisSystemTempleteLabel');
			$labInfo=$labModel->where($labmap)->find();
			$formula=$labInfo['formula'];
			//替换###
			$formula=str_replace('&#39;','\'',str_replace('&quot;','\"',str_replace('###', '', $formula)));
			$formula1=json_decode($formula,true);
			$tablelist=$formula1['datatable'];
			$showname=$_REQUEST['showname'];
			$name=$_REQUEST['name'];
			$this->assign('id',$labmap['id']);
			$this->assign('modelname',$modelname);
			$this->assign('tablelist',$tablelist);
			$this->assign('modelid',$modelid);
			$this->assign('showname',$showname);
			$this->assign('name',$name);
			$this->assign('formula',$formula1);
			$this->display();
		}
	}
	
}
?>