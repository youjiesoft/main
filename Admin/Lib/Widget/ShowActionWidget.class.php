<?php
/*
 * author : zhongyong
 * date   : 2013-1-16
 * usage  : 获取审批流的审核意见信息
 */
import("@.ORG.RBAC");
class ShowActionWidget extends Widget{
	/*
	 * 权限验证机制
	* @auther  yangxi
	* @create time:20140516
	* @editor  yangxi
	* paramate  $key   当前操作级别
	* paramate  $data  当前表单信息
	*/
public function checkRBAC($key,$data){
// 	$name = MODULE_NAME;
// 	if (! empty ( $name )) $qx_name=$name;
	if( $key!=1 &&  $_SESSION[C('USER_AUTH_KEY')]!=1){
		if( $key==2 ){////判断公司权限
			//$this->filterSpecialCompetence($map);
			if($key==3){
				$show=in_array($creatid,$_SESSION['user_dep_all_child'])? 1:0;
			}
		}else if($key==3){//判断公司权限
			//$this->filterSpecialCompetence($map);
			if($_SESSION['user_dep_all_self']) {
				$show=in_array($creatid,$_SESSION['user_dep_all_self'])? 1:0;
			} 
		}else if($key==4){//判断个人权限

			if($data['createid']==$_SESSION[C('USER_AUTH_KEY')]){
				$show=1;
			}else{
				$show=0;
			}
		}
	}else{
		$show=1;
	}
	return $show;
}

/*
* 扩展插件：页面显示按钮
* @auther  yangxi
* @create time:20130113
* @editor  yangxi
* @update time:20130118
* paramate  $data
*/	
	public function render($data){
		require DConfig_PATH."/System/systembutton.inc.php";// 引入按钮配置文件
		//引用需要确认按钮的配置文件
		$confirmcmit = require DConfig_PATH."/System/confirmcmit.inc.php";
		
		$ActionName = ACTION_NAME;//操作方法
		if($ActionName == "lookupDataRoamPull"){
			$ActionName = "add";
		}
		$accessList = RBAC::getRecordAccessList();//当前类的所有权限组
		/**
		 * 开始 获取当前类的所有函数
		 */
		$modulename = MODULE_NAME;// 当前类名
		$class = $modulename."Action";
		$my_object = new $class();
		$class_methods = get_class_methods( get_class($my_object) );
		//开启反射
// 		$my_object = new ReflectionClass($class);
// 		$class_methods = $my_object->getMethods(ReflectionMethod::IS_PUBLIC);
// 		$class_methods=obj2arr($class_methods);
// 		dump($class_methods);
		/**
		 * 结束 获取当前类的所有函数
		 */
		/**
		 * 开始构造按钮显示
		 */
		// 第一步 获取公共按钮集合
		$generalbutton = $general[strtoupper($ActionName)];
		// 第二步 获取当前模型的特殊按钮集合
		$specialbutton = $special[$modulename][strtoupper($ActionName)];
		$buttonList = array();
		// 第三步 设置按钮组
		if(count($generalbutton)>0) $buttonList = array_merge($buttonList,$generalbutton);
		if(count($specialbutton)>0) $buttonList = array_merge($buttonList,$specialbutton);
		// 第四步 判断权限 构造HTML
		$html = '<div class="formBar">';
		$html .= '<ul>';
		foreach ($buttonList as $k => $v) {
			if(!in_array("auditEdit",$class_methods) && $k == "confirmcmit" && in_array($modulename,$confirmcmit) && !in_array($modulename, array('MisAutoMrt','MisAutoHxr','MisAutoAux','MisAutoTyl'))){
				//如果是带审批流的模板，将取消确认提交按钮
				if($_SESSION[strtolower($modulename."_".$ActionName)] != '' || $_SESSION[C('ADMIN_AUTH_KEY')] || strtoupper($ActionName) == 'AUDITEDIT'){
					$html .= $v['html'];
					continue;
				}
			}
			if(in_array("auditEdit",$class_methods) && strtoupper($ActionName) == 'AUDITEDIT'){
				//获取流程审批节点信息
				$process_relation_formDao = M("process_relation_form");
				$where = array();
				$where['tablename'] = $modulename;
				$where['tableid'] = $data['data']['id'];
				$where['auditState'] = 0;//未处理
				$where['doing'] = 1;//进行中的节点
				$where['flowtype'] = array("gt",1);//审批节点或者转子流程节点
				$newinfo = $process_relation_formDao->where($where)->order('sort asc')->select();
				$infolist = array_merge($newinfo);
				if($infolist[0]['flowtype'] == 3){
					//当前审核节点时子流程。则排除审核和打回按钮。保留生单按钮
					if($k == "auditProcess"){
						continue;
					}
				}else{
					if($k == "lookupAuditTuiProcess"){
						continue;
					}
				}
			}
			// 判断 当前按钮是否存在
			if(count($v) > 0)
			// 1、判断当前类里面有没有这个函数
			if(in_array($k, $class_methods)){
				// 2、判断返回值是不是空的，是空的就是没有'-'
				if($_SESSION[strtolower($modulename."_".$ActionName)] != '' || $_SESSION[C('ADMIN_AUTH_KEY')] || strtoupper($ActionName) == 'AUDITEDIT'){
					$show = 1;
// 					if($data){
// 						$key=substr($accessList[strtoupper($ActionName)],strpos($accessList[strtoupper($ActionName)],'-')+1);   //截取后面一段
// 						$show = $this->checkRBAC($key,$data['data']);
// 					}
					// 判断是不是有权限
					if($show) {
						// 判断是不是有多个按钮
						if($v['more']){
							// 多个按钮过滤
							foreach ($v['list'] as $k1 => $v1) {
								// 判断是否有规则存在
								if ($v1['rules']) {
									$a=$v1['rules'];
									$vals = $data['data'];
									// 判断是否有传值过来
									if($vals){
										$matches=array();
										preg_match_all('|#+(.*)#|U', $v1['rules'], $matches);
										foreach($matches[1] as $k2=>$v2){
											if(isset($vals[$v2])){
												$a = str_replace( $matches[0][$k2],$vals[$v2],$a );
											}
										}
									}
									eval("\$a =\"$a\";");
									if( $a ){
										$html .= $v1['html'];
									}
								} else {
									$html .= $v1['html'];
								}
							}
						} else {
							// 判断是否有规则存在
							if ($v['rules']) {
								$a = $v['rules'];
								$vals = $data['data'];
								// 判断是否有传值过来
								if($vals) {
									$matches=array();
									preg_match_all('|#+(.*)#|U', $v['rules'], $matches);
									foreach($matches[1] as $k2=>$v2){
										if(isset($vals[$v2])){
											$a = str_replace( $matches[0][$k2],$vals[$v2],$a );
										}
									}
								}
								eval("\$a =\"$a\";");
								if( $a ){
									$html .= $v['html'];
								}
							} else {
								$html .= $v['html'];
							}
						}
					}
				}
			}
		}
		//关闭按钮常用
		//$html .= '<li><button type="button" class="close tml_formBar_btn tml_formBar_btn_red">'.L("confirmcommit").'</button></li>';
		
		$html .='<div class="export_button" style="display:none;">
					<a class="js-printOut tml-btn tml_look_btn tml_mp" title="导出" rel_id="{sid_node}" export_url="__URL__/fileexport" onclick="fileexport(this)" href="javascript:;" ><span class="icon_lrp">导出11111</span><span class="icon-sort"></span></a>
					<div class="top_drop_lay export_operate __MODULE__">
						<a href="__URL__/lookupWordChoice/id/"   target="dialog"  width="720" height="500"  mask="true" class="tml-btn tml_look_btn tml_mp export_type">
							<span class="icon icon-share icon_lrp"></span><span>导出Word</span>
						</a>
						<a href="__URL__/export_pdf_one/id/" class="tml-btn tml_look_btn tml_mp export_type">
							<span class="icon icon-share icon_lrp"></span><span>导出Pdf</span>
						</a>
					</div>
				</div>';
		
		$html .= '</ul></div>';
		return $html;
	}
}