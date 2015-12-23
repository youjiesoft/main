<?php
/**
 * @Title: ShowRightToolBarWidget 
 * @Package package_name
 * @Description: todo(将toolbar放在右侧) 
 * @author谢友志 
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2014-12-25 上午11:46:59 
 * @version V1.0
 */
class ShowRightToolBarWidget extends Widget{
	public $pagetype=array(
				'add'=>1,  //新增页面
				'edit'=>2, //修改页面
				'view'=>3, //查看页面
			);
	 /**
	  * @Title: render
	  * @Description: todo(功能按钮左上方显示) 
	  * @param array $data  模板传递参数 array('pagetype','modelname',$vo) 
	  * 			$data[0] 页面类型      $data[1] 当前模型名称  $vo 列表页面选择的一条记录
	  * @return string  
	  * @author 谢友志 
	  * @date 2015-3-5 上午10:06:17 
	  * @throws
	  */
	public function render($data){
		//获取当前模型名称
		$name = MODULE_NAME;
		//获取toolbar数据
		$scdmodel = D('SystemConfigDetail');
		$toolbarextensiontoright = $scdmodel->getDetail($name,false,'toolbar');
		$html = '';
//		$html='<script src="__PUBLIC__/Js/showRightToolBar.js" type="text/javascript"></script>';
//		$html .= '<div class="inside_pages_btn_group">';
		$html .='<ul class="right top_tool_bar show_right_top_toolbar">';
		$toolkey = array_keys($toolbarextensiontoright);
		
		//先保留原有的撤回按钮值，然后清除
		$js_iconBack = $toolbarextensiontoright["js-iconBack"];
		//清除撤回按钮
		unset($toolbarextensiontoright["js-iconBack"]);
		
		//是审批时 对 修改 单据撤回 根据条件处理
		if($data[2]){
			//默认关闭所有打印按钮
			unset($toolbarextensiontoright["js-printOut"]);
			
			if($data[2]['auditState']!=0&&$data[2]['auditState']!=-1){
				unset($toolbarextensiontoright["js-delete"]);
				unset($toolbarextensiontoright["js-edit"]);
			}
			if($data[2]['operateid']==1){
				unset($toolbarextensiontoright["js-edit"]);
				unset($toolbarextensiontoright["js-delete"]);
			}
			if($data[2]['auditState']!=3 && $data[2]['operateid'] != 1){
				unset($toolbarextensiontoright["js-Change"]);
			}
		}
		//去掉本身
		unset($toolbarextensiontoright["js-".$data[0]]);
		
		//视图时必须有打印 导出
		if($data[0]=='view'){
			unset($toolbarextensiontoright['js-delete']);
			if($data[2]['auditState']==3 || $data[2]['operateid'] == 1){
				$toolbarextensiontoright['js-Change'] = array(
						'ifcheck' => '1',
						'rules' => '#auditState#==3 || #operateid#==1',
						'permisname' => strtolower($name).'_changeedit',
						'html' => '<a class="js-Change icon tml-btn tml_look_btn tml_mp" href="__URL__/changeEdit/bgval/1/id/{sid_node}" rel="__MODULE__edit" target="navTab"   title="变更"><span><span class="icon icon-eye-open icon_lrp"></span>变更</span></a>',
						'shows' => '1',
						'sortnum' => '6',
				);
				
			}
			if($name=='MisAutoAgr'){
				$projectid=$data[2]["projectid"];
				$onlineUrl1 = "PageOffice://|http://".$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']."/MisSalesMyProject/export_word_one/modelname/MisSalesMyProject/export/swf/id/'.$projectid.'|width=1200px;height=800px;|";
// 				$onlineUrl1 = "http://".$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']."/MisSalesMyProject/export_word_one/modelname/MisSalesMyProject/export/swf/id/".$projectid;
				$toolbarextensiontoright['js-exportswf'] = array(
						'ifcheck' => '1',
						'permisname' => strtolower($name).'_exportswf',
						'html' => '<a class="js-Change icon tml-btn tml_look_btn tml_mp" href="'.$onlineUrl1.'"  title="在线查看"><span><span class="icon icon-eye-open icon_lrp"></span>在线查看</span></a>',
						'shows' => '1',
						'sortnum' => '7',
				
				);
			}else{
				$onlineUrl2 = "PageOffice://|http://".$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']."/$name/export_word_one/modelname/$name/export/swf/id/{sid_node}|width=1200px;height=800px;|";
// 				$onlineUrl2 = "http://".$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']."/$name/export_word_one/modelname/$name/export/swf/id/{sid_node}";
				$toolbarextensiontoright['js-exportswf'] = array(
						'ifcheck' => '0',
						'permisname' => strtolower($name).'_exportswf',
						'html' => '<a class="js-Change icon tml-btn tml_look_btn tml_mp" href="'.$onlineUrl2.'" title="在线查看"><span><span class="icon icon-eye-open icon_lrp"></span>在线查看</span></a>',
						'shows' => '1',
						'sortnum' => '7',
				
				);
			}
			if(!$data[2]["id"]){
				$toolbarextensiontoright = array();
			}
		}elseif($data[0]=='add'){ 
			$toolbarextensiontoright = array(); 
		}elseif($data[0]=='edit'){
			unset($toolbarextensiontoright['js-Change']);
		}
		$this->setToolBorInVolist($data[2],$toolbarextensiontoright);
		/*
		 * 上面 setToolBorInVolist方法处理的按钮 不能放在此代码后面
		 * 
		 * 重新组合表单内部的单据撤回按钮。。 此单据撤回按钮和列表上的撤回按钮功能不一样
		 * @liminggang
		 */
		if($data[0]=='view' && $data[2]['auditState']>0){
			//获取当前模型名称
			$tablename = $data[1];
			$tableid = $data[2]['id'];
			//带有审批流标记字段
			$auditStatus = $data[2]['auditState'];
			if($tableid && $tablename && $auditStatus >0){
				//调用处理撤回按钮方法
				$js_iconBack = $this->setToolBorBack($data[2], $tablename, $tableid, $js_iconBack);
			}
			//追加单据撤回按钮
			$toolbarextensiontoright['js-iconBack'] = $js_iconBack;
		}
		foreach($toolbarextensiontoright as $k=>$v){
			//过滤配制不显示的toolbar
			$file = DConfig_PATH . '/System/unsettoolbar.php';
			if(file_exists($file)){
				//引入按钮过滤配制文件
				$unsettoolbar = require $file;
				if(in_array($k, $unsettoolbar)){
					continue;
				}
			}
			/*
			 * 只要是存在了项目编码的数据，那么一律过滤掉新增按钮(因为直接新增无法带回项目相关的生单数据源)
			 * @黎明刚
			 */
			if($data[2]['projectid'] && $k=="js-add"){
				continue;
			}
			//新增时去需要掉带id的html的项
			if($data[0]=='add'){
				if(strpos($v['html'],"{sid_node}")>0){
					continue;
				}
			}
			
			//改按钮不下列页面显示							
			$rightnotshow = array();
			if($v['rightnotshow']){
				$rightnotshow = explode(',',$v['rightnotshow']);
			}
			MOTHED_NAME;
			//构造li 2个条件，排除不显示页面和权限不允许
			if(!in_array($this->pagetype[$data[0]],$rightnotshow)&&!in_array($data[0],$rightnotshow)){
				if($_SESSION['a']==1||empty($v['ifcheck'])||($v['ifcheck']&&!empty($v['permisname']) and $_SESSION[$v['permisname']])){
					$research = array('tml-btn','tml_look_btn','tml_mp','icon_lrp','icon ');
					$replace = array('','','','','');
					$v['html'] = str_replace($research,$replace,$v['html']);
					$v['html']=str_replace('{sid_node}',$data[2]['id'],$v['html']);
					$pregsearch = '/<span>\s*(<span\s+class=.*?>)\s*<\/span>/';
					$pregplace = '${1}</span><span class="inside_pages_btn_word">';
					$v['html'] = preg_replace($pregsearch,$pregplace,$v['html']);
					if($k == 'js-delete'){
						$v['html'] = str_replace('<a ','<a callback="ajaxTodoForRightToolbar" ',$v['html']);
					}
					$html.="<li class='left'>".$v['html']."</li>";
				}
			}
		}
		//当前表单是关系型表单不生成预览
		$BindMap=array();
		$BindMap['_string']="bindaname='{$name}' or inbindaname='{$name}'";
		$MisAutoBindModel=D("MisAutoBind");
		$bindList=$MisAutoBindModel->where($BindMap)->find();
		if(($data[0]=='add'||$data[0]=='edit')&&!$bindList){ // ||$data[0]=='view'
			
			$actionName = $data[1];
			$actionOprate = $data[0];
			$html .=<<<EOF
			<li class="left">
				<a href="#" id="{$actionName}_{$actionOprate}_nbm_preview_btn" class="nbm_preview_btn">
					<span class="tool_bar_icon icon-eye-open"></span>
					预览
				</a>
			</li>
EOF;
			$html .= <<<EOF
	<script>
		$(function(){
			//var box = navTab.getCurrentPanel();
			console.log($('#{$actionName}_{$actionOprate}_nbm_preview_btn'));
			$('#{$actionName}_{$actionOprate}_nbm_preview_btn').die('click');
			$('#{$actionName}_{$actionOprate}_nbm_preview_btn').live('click' , function(){
			var ajax_option={
				url:"__URL__/view/preview/1",//默认是form action
				success:function(data){
					console.log(data);
				}
			}
			//$('#{$actionName}_{$actionOprate}' , box).ajaxSubmit(ajax_option);
				$('#{$actionName}_{$actionOprate} input:disabled').attr("disabled",false);
				var formObj = $('#{$actionName}_{$actionOprate}').formSerialize();
				//console.log($('#{$actionName}_{$actionOprate}'),formObj);
				if(formObj){
					var postdata = formObj;
					var urls = "__URL__/view/preview/1";
					var rel="__MODULE__preview";
					var titles = '预览';
					navTab.openTab(rel, urls, {title : titles,fresh : true,data:postdata});
					
					/*
					var formObj = $('form.required-validate' , box).formSerialize();
				 	var tabids = "__MODULE__view";
					var urls = "__URL__/view/preview/1";
					var titles = "{$actiontitle}";
					var options = {};
					options.param = formObj;
					options.mask = "true";
					options.height = 559;
					options.width = 1100;
					var rel="__MODULE__preview";
					$.pdialog.open(urls,rel, "预览",options);
					*/
				}
			});
					
		});
	</script>
EOF;
		}
//		$html .= '<li><a href="javascript:navTab.closeCurrentTab();" class=""><span class="icon-remove"></span><span class="inside_pages_btn_word">关闭</span></a></li>'; 
		$html.='</ul>';
		$html .="<div class='gridTbody'><div class='selected' rel='{$data[2]['id']}'></div></div>";
//		$html.="</div>";
		return $html;
	}
	
	/**
	 * @Title: setToolBorBack 
	 * @Description: todo(处理撤回按钮) 
	 * @param 当前表单数据 $data
	 * @param 当前模型 $tablename
	 * @param 当前表单ID $tableid
	 * @param 撤回按钮html $js_iconBack  
	 * @author liminggang
	 * @date 2015-8-28 下午3:39:55 
	 * @throws
	 */
	protected function setToolBorBack($data,$tablename,$tableid,$js_iconBack){
		//判断单据是否已经是终审了。终审了的单据禁止撤回
		$bool = true;
		if($data['auditState'] != 3){
			//实例化表单流程表
			$process_relation_formDao = M("process_relation_form");
			/*
			 * 模型名称必须， 表单id必须，流程执行标记字段必须
			 * 开始进行撤回按钮的限制
			 * 获取当前登录人， 已经当前单据已经走到那个节点了。
			 */
			$relmap = array();
			$relmap['tablename'] = $tablename;
			$relmap['tableid'] = $tableid;
			$relmap['doing'] = 1;
			$relmap['flowtype'] = array("gt",1);//审批节点或者转子流程节点
			$newinfo = $process_relation_formDao->where($relmap)->field("id,flowid,parallel,flowtype,processto,curAuditUser,alreadyAuditUser,auditUser,auditState,catgory")->order('sort asc')->select();
			if($newinfo){
				//实例化并串表
				$process_relation_parallel = M("process_relation_parallel");
				$bool = false;//定义是否可以操作变量
				//存储已经审核过的流程数据
				$alreadyAuditNode = array();
				//获取当前登录人
				$userid = $_SESSION[C('USER_AUTH_KEY')];
				$i = 0;
				//循环遍历审批节点数据
				foreach($newinfo as $key=>$val){
					if($val['catgory'] == 1){
						//变更流程
						if($val['auditState'] == 0){
							//表示是当前审核节点, 那么判断是否存在已审核了
							$alreadyAuditUser = array_filter(explode(",", $val['alreadyAuditUser']));
							$d = true;
							//存在审核人 或者是子流程而且已经转子任务了
							if($alreadyAuditUser || ($val['isaudittableid'] && $val['flowtype']==3)){
								//存储正在审核的并行或者并串混搭节点
								$alreadyAuditNode[] = $val;
								$d = false;
							}
							if($d && $i==0)$alreadyAuditNode = array();
							break;
						}
						//存储审核过的
						$alreadyAuditNode[] = $val;
						++$i;
					}else{
						//普通流程
						if($val['auditState'] == 0){
							//表示是当前审核节点, 那么判断是否存在已审核了
							$alreadyAuditUser = array_filter(explode(",", $val['alreadyAuditUser']));
							//存在审核人 或者是子流程而且已经转子任务了
							if($alreadyAuditUser || ($val['isaudittableid'] && $val['flowtype']==3)){
								//存储正在审核的并行或者并串混搭节点
								$alreadyAuditNode[] = $val;
							}
							break;
						}
						//存储审核过的
						$alreadyAuditNode[] = $val;
					}
				}
				//进行已审核和正在审核的节点进行解析
				if($alreadyAuditNode){
					//寻找最新的一个数据
					$c = count($alreadyAuditNode)-1;
					//取出最新一个审批节点
					$oldalreadyAuditNode = $alreadyAuditNode[$c];
					//获取已审核人员
					$oldalreadyAuditUser =explode(",", $oldalreadyAuditNode['alreadyAuditUser']);
					//对最新的审批节点进行解析 ，判断是那种审批节点 0串行，1并行，2并串混搭
					if($oldalreadyAuditNode['parallel'] == 2){
						//查询并串混搭走向 。可以撤回的节点
						$where = array();
						$where['tablename'] = $tablename;
						$where['tableid'] = $tableid;
						$where['relation_formid'] = $oldalreadyAuditNode['id'];
						$where['auditState'] = 2;//已审核完成的节点
						$where['parentid'] = 0;//先找顶级
						$parallist =$process_relation_parallel->where($where)->order("sort asc")->select();
						if($parallist){
							//先将按钮禁止
							$bool = true;
							foreach($parallist as $ke=>$va){
								$data = $this->digui($va);
								//进行解析串行混搭的批次
								if(in_array($userid, explode(",", $data['curAuditUser']))){
									//将单据撤回按钮可操作
									$bool = false;
									break;
								}
							}
						}else{
							//将单据撤回按钮禁用，不可操作
							$bool = true;
						}
					}else if($oldalreadyAuditUser['flowtype'] == 3){
						if($oldalreadyAuditUser['auditState']!=1){
							//目前暂不支持混搭情况
							$bool = true;
						}
					}else{
						//存在多个人审核。
						if(!in_array($userid, $oldalreadyAuditUser)){
							//将单据撤回按钮禁用，不可操作
							$bool = true;
						}
					}
				}else{
					//启动后的节点撤回，直接进行打回到新建状态
					if($data['createid'] != $userid && $_SESSION['a'] != 1){
						$bool = true;
					}
				}
			}else{
				//将单据撤回按钮禁用，不可操作
				$bool = true;
			}
		}
		
		//判断撤回按钮是否可操作
		if($bool){
			//将单据撤回按钮禁用，不可操作
			preg_match("/<a.*?class=[\'\"](.*?)[\'\"]/",$js_iconBack['html'],$m);
			$m2 = $m[1]." disabled";
			$js_iconBack['html'] = str_replace($m[1],$m2 ,$js_iconBack['html']);
		}else{
			$js_iconBack['html'] = '<a class="js-iconBack tbundo" href="__URL__/lookupGetBackprocess/id/{sid_node}/navTabId/__MODULE__auditView" callback="" warn="请选择节点" target="ajaxTodo" title="您确定要撤回单据吗?"><span><span class="icon icon-external-link icon_lrp"></span>单据撤回</span></a>';
		}
		//取消撤回按钮的权限验证
		$js_iconBack['ifcheck'] = 0;
		return $js_iconBack;
	}
	/**
	 * @Title: digui 
	 * @Description: todo(递归查询批次信息数据) 
	 * @param array 批次数据 $data
	 * @return 返回数据  
	 * @author liminggang
	 * @date 2015-9-17 下午3:37:08 
	 * @throws
	 */
	protected function digui($data){
		//实例化并串表
		$process_relation_parallel = M("process_relation_parallel");
		$where = array();
		$where['parentid'] = $data['id'];
		$where['auditState'] = 2;
		//查询是否下级也已经完成了
		$diguidata = $process_relation_parallel->where($where)->order("sort asc")->find();
		if($diguidata){
			return $this->digui($diguidata);
		}else{
			return $data;
		}
	}
	
	/**
	 * @Title: setToolBorInVolist
	 * @Description: todo(重构volist,加入按钮控制)
	 * @param 列表数据 $voList
	 * @author 杨东
	 * @date 2014-5-26 上午10:59:06
	 * @throws
	 */
	protected function setToolBorInVolist($voList,&$toolbarextensiontoright){
		// 		$str = "";
		if($toolbarextensiontoright){
			// 构造按钮控制
		foreach ($toolbarextensiontoright as $k => $v) {
				$temp = 0;
				$temp2 = 1;
				//封装单据个人，部门，部门及子部门，全部。等全线问题
					$bool = true; //默认当前按钮可执行
					if($v['ifcheck'] && $k!='js-add' && $k!='js-printOut'){
						//表示验证session
						if( !isset($_SESSION['a']) ){
							if( $_SESSION[$v['permisname']]!=1 ){
								if( $_SESSION[$v['permisname']]==2 ){////判断公司权限
									if($voList['companyid']!=$_SESSION['companyid']){
										$bool = false;
									}
								}else if($_SESSION[$v['permisname']]==3){//判断部门权限
									if($voList['departmentid']!=$_SESSION['user_dep_id']){
										$bool = false;
									}
								}else if($_SESSION[$v['permisname']]==4){//判断个人权限
									if($voList['createid'] != $_SESSION[C('USER_AUTH_KEY')]||$voList['companyid']!=$_SESSION['companyid']){
										$bool = false;
									}
								}
							}
						}
					}
					if($bool){
						if ($v['rules']) {
							// 判断是否有传值过来
							$matches=array();
							preg_match_all('|#+(.*)#|U', $v['rules'], $matches);
							$a = $v['rules'];
							foreach($matches[1] as $k2=>$v2){
								if(isset($voList[$v2])){
									$a = str_replace($matches[0][$k2],$voList[$v2],$a);
								} else {
									$a = str_replace($matches[0][$k2],$v2,$a);
								}
							}
							//警告：这里一定不能修改成 if($a) eval("\$a = \"$a\";"); ，这样会导致所有的按钮不受数据控制。
							@eval("\$a =".$a.";");
							if( $a ){
								$temp =1;
							}
						} else {
							$temp =1;
						}
						if($temp ==1){
							if($v['disabledmap']){
								// 判断是否有传值过来
								$matches=array();
								preg_match_all('|#+(.*)#|U', $v['disabledmap'], $matches);
								$a = $v['disabledmap'];
								foreach($matches[1] as $k2=>$v2){
									if(isset($voList[$v2])){
										$a = str_replace($matches[0][$k2],$voList[$v2],$a);
									} else {
										$a = str_replace($matches[0][$k2],$v2,$a);
									}
								}
								//警告：这里一定不能修改成 if($a) eval("\$a = \"$a\";"); ，这样会导致所有的按钮不受数据控制。
								@eval("\$a =".$a.";");
								if( $a ){
									unset($classarr[$k]);
									$temp2 = 0;
								}
							}
						}
	
					}
					
				//}
				$temp = $temp2?$temp:$temp2;
				if(empty($temp)){
					preg_match("/<a.*?class=[\'\"](.*?)[\'\"]/",$v['html'],$m);
					$m2 = $m[1]." disabled";
					$toolbarextensiontoright[$k]['html'] = str_replace($m[1],$m2 ,$v['html']);	
				}
			}
		}
	}
}