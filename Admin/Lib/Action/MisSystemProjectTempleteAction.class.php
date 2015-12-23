<?php
/**
 * @Title: MisSystemProjectTempleteAction
 * @Package package_name
 * @Description: todo(项目模板管理)
 * @author 管理员
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015-02-13 10:48:48
 * @version V1.0
*/
class MisSystemProjectTempleteAction extends MisSystemProjectTempleteExtendAction {
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
	
	private function getType(){
		//获取流程类型 组合成树结构
		$MisSystemFlowTypeDao = M("mis_system_flow_type");
		$where['status'] = 1;
		$typelist=$MisSystemFlowTypeDao->where($where)->select();
		//配置树形结构的url请求地址，和div刷新区域
		$paeam['url'] = "__URL__/index/jump/jump/pid/#id#/tid/#parentid#";
		$paeam['rel'] = "MisSystemProjectTemplete_left";
		//生成树形结构的json值
		$json_arr=$this->getTree($typelist,$paeam);
		$this->assign('json_arr',$json_arr);
		return $typelist;
	}
	/**
	 * @Title: edit
	 * @Description: todo(重写父类编辑函数)
	 * @author 管理员
	 * @date 2015-01-27 10:48:48
	 * @throws 
	*/
	public function index(){
			//获取流程类型
			$typelist=$this->getType();
			
			$name = $this->getActionName();
			
			//列表过滤器，生成查询Map对象
			$map = $this->_search ($name);
			
			$pid = $_REQUEST['pid'] = $_REQUEST['pid']?$_REQUEST['pid']:$typelist[0]['id'];
			$this->assign("pid",$pid);
			if($pid){
				$categoryarr = $this->downAllChildren($typelist,$pid);
				$map['category'] = array(' in ',$categoryarr);
			}
			
			if($_REQUEST['tid'] == 0){
				$tid = $pid;
			}else{
				$tid=$_REQUEST['tid']?$_REQUEST['tid']:$typelist[0]['id'];
			}
			$this->assign("tid",$tid);
			
			if (method_exists ( $this, '_filter' )) {
				$this->_filter ( $map );
			}
			
			if (! empty ( $name )) {
				$this->_list ( $name, $map );
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
				$this->display('indexview');
			}else{
				$this->display();
			}
		}

	/**
	 * @Title: _before_edit
	 * @Description: todo(前置编辑函数)
	 * @author 管理员
	 * @date 2015-01-27 10:48:48
	 * @throws 
	*/
	function _before_edit(){
		//获取流程类型 组合成树结构
		$MisSystemFlowTypeDao = M("mis_system_flow_type");
		$where1['status'] = 1;
		$where1['outlinelevel'] = 1;
		$ty['a']=$MisSystemFlowTypeDao->where($where1)->field("id,name")->select();
		$where = array();
		$where['status'] = 1;
		$where['outlinelevel'] = 2;
		$ty['b']=$MisSystemFlowTypeDao->where($where)->field("id,name,parentid")->select();
		$this->assign("typelist",json_encode($ty));
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
		if($_POST['zhuangtai']){
			$_POST['zhuangtai'];
		}else{
			$_POST['zhuangtai']=0;
		}
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
		if($_POST['zhuangtai']){
			$_POST['zhuangtai'];
		}else{
			$_POST['zhuangtai']=0;
		}
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
		//获取流程类型 组合成树结构
		$MisSystemFlowTypeDao = M("mis_system_flow_type");
		$where['status'] = 1;
		$where['outlinelevel'] = 1;
		$ty['a']=$MisSystemFlowTypeDao->where($where)->field("id,name")->select();
		$where = array();
		$where['status'] = 1;
		$where['outlinelevel'] = 2;
		$ty['b']=$MisSystemFlowTypeDao->where($where)->field("id,name,parentid")->select();
		$this->assign("typelist",json_encode($ty));
		/* //得到编号
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
		$this->assign('orderno',$orderno); */
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
			$model = M('mis_system_project_templete_label');
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
		$isedit=$_REQUEST['isedit'];
		if($isedit){
			$datatable=$_POST['datatable'];
			$templeteid=$_REQUEST['templeteid'];
			$supcategory=$_REQUEST['supcategory'];
			$category=$_REQUEST['category'];
			$hyid=$_REQUEST['hyid']?$_REQUEST['hyid']:null;
			$cylid=$_REQUEST['cylid']?$_REQUEST['cylid']:null;
			$custtypeid=$_REQUEST['custtypeid']?$_REQUEST['custtypeid']:null;
			if(!empty($datatable)){
				$model = D('MisSystemProjectTempleteLabel');
				$insertData = array();// 数据添加缓存集合
				$updateData = array();// 数据修改缓存集合
				if($datatable){
					foreach($datatable as $key=>$val){
						foreach($val as $k=>$v){
							$v['supcategory']=$supcategory;
							$v['category']=$category;
							$v['hyid']=$hyid;
							$v['cylid']=$cylid;
							$v['custtypeid']=$custtypeid;
							$v['templeteid']=$templeteid;
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
			$projectTemModel=D('MisSystemProjectTemplete');
			$projectTemMap['id']=$_REQUEST['id'];
			$this->assign('templeteid',$_REQUEST['id']);
			$projectTemList=$projectTemModel->where($projectTemMap)->find();
			$supcategory=$projectTemList['supcategory'];
			$category=$projectTemList['category'];
			$hyid=$projectTemList['hyid']?$projectTemList['hyid']:0;
			$cylid=$projectTemList['cylid']?$projectTemList['cylid']:0;
			$custtypeid=$projectTemList['custtypeid']?$projectTemList['custtypeid']:0;
			$exportData=$this->lookupGetExportData($supcategory,$category,$hyid,$cylid,$custtypeid);
			//查询添加的标签
// 			$map['supcategory']=$supcategory;
// 			$map['category']=$category;
// 			$map['hyid']=$hyid;
// 			$map['cylid']=$cylid;
// 			$map['custtypeid']=$custtypeid;
			$map['templeteid']=$_REQUEST['id'];
			$labelModel=D('MisSystemProjectTempleteLabel');
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
				$endExportData[$labval['name']]['name']=$labval['name'];
				$endExportData[$labval['name']]['showname']=$labval['showname'];
				$endExportData[$labval['name']]['formula']=$labval['formula'];
				$endExportData[$labval['name']]['id']=$labval['id'];
				$endExportData[$labval['name']]['colORrow']=$labval['colORrow'];
				$endExportData[$labval['name']]['is_datatable']=$labval['is_datatable'];
				//替换
				$labelList[$labk]['formula']=str_replace(']','}',str_replace('[','${',$labval['formula']));
			}
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
			$this->assign('supcategory',$supcategory);
			$this->assign('category',$category);
			$this->assign('hyid',$hyid);
			$this->assign('cylid',$cylid);
			$this->assign('custtypeid',$custtypeid);
			$this->assign('labelList',$labelList);
			$this->display();
		}
	}
	
	function lookupGetExportData($supcategory,$category,$hyid,$cylid,$custtypeid){
		//先判断是否存在总报告信息
		$mis_project_flow_formDao =M("mis_system_flow_form");
		//定义报告数组
		$totalArr=array();
		$where = array();
		$where['supcategory'] = $supcategory;
		$where['category'] = $category;
		$where['outlinelevel'] = 4;
		if($category){
			$where['category'] =$category;
			$where['smallreport'] = 1;
			$totalList = $mis_project_flow_formDao->where($where)->field("id,name,formtype,formobj,datatype,smallsort,custtypeid,hyid,cylid")->order("smallsort asc")->select();
		
		}else{
			$where['totalreport'] = 1;
			$totalList = $mis_project_flow_formDao->where($where)->field("id,name,formtype,formobj,datatype,totalsort")->order("totalsort asc")->select();
		}
		$data = array();
		foreach($totalList as $key=>$val){
			if ($val ["custtypeid"] && $custtypeid && $custtypeid != $val ["custtypeid"]) {
				unset ( $totalList [$key] );
				continue;
			}
			if ($val ["hyid"] && $hyid && $hyid != $val ["hyid"]) {
				unset ( $totalList [$key] );
				continue;
			}
			if ($val ["cylid"] && $cylid && $cylid != $val ["cylid"]) {
				unset ( $totalList [$key] );
				continue;
			}
			//表单才会存在内嵌表
			if($val['formtype'] == 2){
				//获取主模型名称
				$name=$val['formobj'];
				//根据项目ID和任务ID查询对应模型数据
				$where = array ();
				$where ['projectworkid'] = $val['id'];
				$modelname = D($name);
				//1、判断是列表还是表单
				if($val['datatype']){
					//列表类型任务
					$volist = $modelname->where ( $where )->select ();
					$totalArr = $this->getNeiQianVal($volist,$name,true);
				}else{
					$vo = $modelname->where ( $where )->find ();
					$totalArr = $this->getReportData($vo,$name,true);
				}
				if($totalArr){
					$data = array_merge($data,$totalArr);
				}
			}
		}
		return $data;
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
			//内嵌表显示字段
			$showfield=json_encode($_REQUEST['showfield']);
			$ziti=$_REQUEST['ziti'];
			$zihao=$_REQUEST['zihao'];
			$hangjianju=$_REQUEST['hangjianju'];
			//读取原有标签
			$supcategory=$_REQUEST['supcategory'];
			$category=$_REQUEST['category'];
			$hyid=$_REQUEST['hyid'];
			$cylid=$_REQUEST['cylid'];
			$custtypeid=$_REQUEST['custtypeid'];
			$exportData = $this->lookupGetExportData($supcategory,$category,$hyid,$cylid,$custtypeid);
			//查询添加的标签
			$map['supcategory']=$supcategory;
			$map['category']=$category;
			$map['hyid']=$hyid;
			$map['cylid']=$cylid;
			$map['custtypeid']=$custtypeid;
			$labelModel=D('MisSystemProjectTempleteLabel');
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
			$data['supcategory']=$supcategory;
			$data['category']=$category;
			$data['hyid']=$hyid;
			$data['cylid']=$cylid;
			$data['custtypeid']=$custtypeid;
			$data['formula']=$trArr['arrlist'];
			$data['name']=$_REQUEST['name'];
			$data['showname']=$_REQUEST['showname'];
			$data['is_datatable']=1;
			$data['showfield']=$showfield;
			$data['ziti']=$ziti;
			$data['zihao']=$zihao;
			$data['hangjianju']=$hangjianju;
			if($id){
				//修改
				$data['updatetime']=time();
				$data['updateid']=$_SESSION [C ( 'USER_AUTH_KEY' )];
				$list=$labelModel->where($labelMap)->save($data);
				$url=__URL__.'/lookupEditTable/showname/'.$_REQUEST['showname'].'/id/'.$id.'/name/'.$_REQUEST['name'].'/supcategory/'.$supcategory.'/category/'.$category.'/custtypeid/'.$custtypeid.'/hyid/'.$hyid.'/cylid/'.$cylid;
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
				$url=__URL__.'/lookupEditTable/showname/'.$_REQUEST['showname'].'/id/'.$list.'/name/'.$_REQUEST['name'].'/supcategory/'.$supcategory.'/category/'.$category.'/custtypeid/'.$custtypeid.'/hyid/'.$hyid.'/cylid/'.$cylid;
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
			$supcategory=$_REQUEST['supcategory'];
			$category=$_REQUEST['category'];
			$hyid=$_REQUEST['hyid'];
			$cylid=$_REQUEST['cylid'];
			$custtypeid=$_REQUEST['custtypeid'];
			//查询编辑表格数据a
			$labModel=D('MisSystemProjectTempleteLabel');
			$labInfo=$labModel->where($labmap)->find();
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
			$this->assign('id',$labmap['id']);
			$this->assign('supcategory',$supcategory);
			$this->assign('category',$category);
			$this->assign('hyid',$hyid);
			$this->assign('cylid',$cylid);
			$this->assign('custtypeid',$custtypeid);
			$this->assign('tablelist',$tablelist);
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
			$supcategory=$_REQUEST['supcategory'];
			$category=$_REQUEST['category'];
			$hyid=$_REQUEST['hyid'];
			$cylid=$_REQUEST['cylid'];
			$custtypeid=$_REQUEST['custtypeid'];
			$exportData = $this->lookupGetExportData($supcategory,$category,$hyid,$cylid,$custtypeid);
			//查询添加的标签
			$map['supcategory']=$supcategory;
			$map['category']=$category;
			$map['hyid']=$hyid;
			$map['cylid']=$cylid;
			$map['custtypeid']=$custtypeid;
			$labelModel=D('MisSystemProjectTempleteLabel');
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
			$data['supcategory']=$supcategory;
			$data['category']=$category;
			$data['hyid']=$hyid;
			$data['cylid']=$cylid;
			$data['custtypeid']=$custtypeid;
			$data['colORrow']=1;
			$data['formula']=$trArr['arrlist'];
			$data['name']=$_REQUEST['name'];
			$data['showname']=$_REQUEST['showname'];
			if($id){
				//修改
				$data['updatetime']=time();
				$data['updateid']=$_SESSION [C ( 'USER_AUTH_KEY' )];
				$list=$labelModel->where($labelMap)->save($data);
				$url=__URL__.'/lookupEditColORrow/showname/'.$_REQUEST['showname'].'/id/'.$id.'/name/'.$_REQUEST['name'].'/supcategory/'.$supcategory.'/category/'.$category.'/custtypeid/'.$custtypeid.'/hyid/'.$hyid.'/cylid/'.$cylid;
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
				$url=__URL__.'/lookupEditColORrow/showname/'.$_REQUEST['showname'].'/id/'.$list.'/name/'.$_REQUEST['name'].'/supcategory/'.$supcategory.'/category/'.$category.'/custtypeid/'.$custtypeid.'/hyid/'.$hyid.'/cylid/'.$cylid;
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
			$supcategory=$_REQUEST['supcategory'];
			$category=$_REQUEST['category'];
			$hyid=$_REQUEST['hyid'];
			$cylid=$_REQUEST['cylid'];
			$custtypeid=$_REQUEST['custtypeid'];
			//查询编辑表格数据a
			$labModel=D('MisSystemProjectTempleteLabel');
			$labInfo=$labModel->where($labmap)->find();
			$formula=$labInfo['formula'];
			//替换###
			$formula=str_replace('&#39;','\'',str_replace('&quot;','\"',str_replace('###', '', $formula)));
			$formula1=json_decode($formula,true);
			$tablelist=$formula1['datatable'];
			$showname=$_REQUEST['showname'];
			$name=$_REQUEST['name'];
			$this->assign('id',$labmap['id']);
			$this->assign('supcategory',$supcategory);
			$this->assign('category',$category);
			$this->assign('hyid',$hyid);
			$this->assign('cylid',$cylid);
			$this->assign('custtypeid',$custtypeid);
			$this->assign('tablelist',$tablelist);
			$this->assign('showname',$showname);
			$this->assign('name',$name);
			$this->assign('formula',$formula1);
			$this->display();
		}
	}
	
}
?>