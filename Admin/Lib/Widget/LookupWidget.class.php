<?php
/**
 * LookUp组件动态化插件。
 * @Title: LookupWidget 
 * @Package package_name
 * @Description: todo(将LookUp组件动态化，方便维护升级。) 
 * @author quqiang
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2015年3月19日 上午11:33:26 
 * @version V1.0
 */
class LookupWidget extends Widget{
	/**
	 * $data[0]		$controllType		组件类型 lookup , lookupsuper
	 * $data[1]		$vo						页面vo数据
	 * $data[2]		$controllProperty	当前组件的属性ID或属性数组
	 * $data[3]		$isedit					是否为修改操作页面
	 * $data[4]		$isreturnvalue		是否为view操作页面
	 * $data[5]		$ganshe				干涉页面 状态归0
	 * @see Widget::render()
	 */
	public function render($data){
		/**			数据收集			*/
		unset($vo);
		$controllType		=	$data[0];	//组件类型 lookup , lookupsuper
		$vo					=	$data[1];	//页面vo数据
		$controllProperty	=	$data[2];	//当前组件的属性ID或属性数组
		$isedit				=	$data[3];	//是否为修改操作页面
		$isreturnvalue		=	$data[4];	//是否为view操作页面
		$ganshe				=	$data[5];	//干涉页面 状态归0
		/**			数据处理			*/
		$dir = CONF_PATH;
		$controlls =require $dir . 'controlls.php';
		require $dir . 'property.php';
		$privateProperty = array();
		foreach ($controlls as $key => $val) {
			if ($val['property']) {
				$privateProperty[$key] = array_merge($NBM_COMMON_PROPERTY, $val['property']);
			}else{
				$privateProperty[$key] = $NBM_COMMON_PROPERTY;
			}
		}
		$property = $privateProperty['lookup'];
		switch ($controllType){
			case 1:
				$controllType = 'lookup';
				break;
			case 2:
				$controllType = 'lookupsuper';
			default:
				$controllType = 'lookup';
				break;
		}
		if(!is_array($controllProperty)){
			$obj = M('mis_dynamic_form_propery');
			$controllProperty = $obj->where('id='.$controllProperty)->find();
			if($ganshe == '1'){
				$controllProperty[$property['islock']['dbfield']] = 1;
				$controllProperty[$property['requiredfield']['dbfield']] = 1;
				$controllProperty[$property['isshow']['dbfield']] = 0;
			}
		}
		
		if(!is_array($controllProperty)){
			return  '<error>Lookup组件配置错误/没有属性<error>';
		}
		/**		开始lookup属性解析			*/
		if(!$isreturnvalue){
			$chtml = '';
			if( $controllProperty[$property['checkfunc']['dbfield']] ){
				$chtml=$controllProperty[$property['checkfunc']['dbfield']];
			}
			$required = ''; // 必填验证
			if($controllProperty[$property['requiredfield']['dbfield']]&&$ganshe != '1'){
				$required = 'required';
			}
			$chtml = $required.'nbm '. $chtml;
			
			//获取lookup key值 动态加载lookup配置
			$lookupchoice = $controllProperty[$property['lookupchoice']['dbfield']];
			$lookupObj = D('LookupObj');
			$lookupDetail = $lookupObj->GetLookupDetail($lookupchoice);
			//var_dump($lookupDetail['condition']);
			$defaultLookupCondition = $lookupDetail['condition']; // lookup配置时生成的过滤条件。
			$model = $lookupDetail['mode'];
			$urls = $lookupDetail['url'];
			$lookupgroup = $controllProperty[$property['lookupgroup']['dbfield']];
			//视图特殊处理
			if($lookupDetail["viewname"]){	   			
				$viewname = $lookupDetail["viewname"];//视图名称
				$viewsavefield =  $lookupDetail["val"];	//视图显示字段
				$viewshowfield	= $lookupDetail["filed"];	//视图存储字段
				$dataViewModel = M("mis_system_dataview_mas");
				$viewsql = "SELECT s.field FROM mis_system_dataview_mas AS m LEFT JOIN mis_system_dataview_sub AS s ON m.id=s.masid WHERE m.status=1 and s.status=1 and m.`name`='{$viewname}' AND s.otherfield=";
				//存储字段
				$savefieldviewsql 			= $viewsql."'{$viewsavefield}' limit 1";
				$savefieldviewfieldarr 		= $dataViewModel->query($savefieldviewsql);
				$savefieldviewfieldexplode	= explode(".",$savefieldviewfieldarr[0]['field']);
				$savefieldviewtable 		= $savefieldviewfieldexplode[0]; //存储字段所在的真实表名
				$orgval 					= $savefieldviewfieldexplode[1]; //存储字段真实字段名
				
				//显示字段
				$showfieldviewsql 			= $viewsql."'{$viewshowfield}' limit 1";
				$showfieldviewfieldarr 		= $dataViewModel->query($showfieldviewsql);
				$showfieldviewfieldexplode	= explode(".",$showfieldviewfieldarr[0]['field']);
				$showfieldviewtable 		= $showfieldviewfieldexplode[0]; //显示字段所在的真实表名
				$orgchar 					= $showfieldviewfieldexplode[1]; //显示字段真实字段名
			}else{				
				$orgval = $lookupDetail['val']?$lookupDetail['val']:'id';
				$orgchar =$lookupDetail['filed']?$lookupDetail['filed']:'name';
			}
			$orgLookuoVal = $controllProperty[$property['org']['dbfield']]; // 值 value
			$orgLookupText = $controllProperty[$property['org1']['dbfield']]; // 显示内容 text
			$readonly	=	$controllProperty[$property['islock']['dbfield']]==1?false:true;
			
			$view = '';
			if($lookupDetail['viewname']){
				$view = '&viewname='.$lookupDetail['viewname'];
			}
			if($lookupDetail['dt']){
				$dtcon = '&lookuptodatatable=2';
			}
			// lookup的值反写
			$textCounterCheck=''; // 显示文本的反写  text
			$valCounterCheck=''; // 值的反写				value
			// lporder	从外部lookup中取值字段
			// lpkey		当前lookup的key
			// lpfor		往哪个东西里写值
			// lpself		当前项的字段名称
			if($orgLookuoVal){
				unset($temp);
				$temp = explode('.', $orgLookuoVal);
				$valCounterCheck = "callback=\"lookup_counter_check\" lpkey=\"{$lookupchoice}\" lpfor=\"{$lookupgroup}.{$orgchar}\" lpself=\"{$orgval}\" lporder=\"{$temp[1]}\"";
			}
			if($orgLookupText){
				unset($temp);
				$temp = explode('.', $orgLookupText);
				$textCounterCheck = "callback=\"lookup_counter_check\" lpkey=\"{$lookupchoice}\" lpfor=\"{$lookupgroup}.{$orgval}\" lpself=\"{$orgchar}\" lporder=\"{$temp[1]}\"";
			}
				
			// lookup附加条件处理
			$appendCondtionArr=array();
			$appendCondtion = $controllProperty[$property['additionalconditions']['name']]; //  附加条件设置信息
			if($appendCondtion){
				$appendCondtion = unserialize(base64_decode($appendCondtion) );
				// proexp 表单字段列表
				// sysexp	系统字段列表
				$formFiledList 	=	$appendCondtion['proexp'];
				$sysFieldList 		= 	$appendCondtion['sysexp'];
				// 						$formFiledFmt=array();
				// 						$sysFieldFmt = array();
				$sysFieldFmt = unserialize($sysFieldList) or array();
				// 获取真实的表单字段名。
				if($formFiledList){
					$formFiledListArr = unserialize($formFiledList) or array();
					if( is_array( $formFiledListArr ) && count( $formFiledListArr ) ){
						$formFiledKey = array_keys($formFiledListArr);
						$properModel = M('mis_dynamic_form_propery');
						$properMap['id'] = array('in',$formFiledKey);
						$fd = $properModel->where($properMap)->field('fieldname,id')->select();
						if($fd){
							foreach ($fd as $k=>$v){
								// 									$fieldArr[] = $v['fieldname'];
								if( $formFiledListArr[$v['id']] == -1){
									$formFiledFmt[$v['fieldname']] = $v['fieldname'];
								} else {
									$formFiledFmt[$v['fieldname']] = $formFiledListArr[$v['id']];
									//$formFiledFmt[$formFiledListArr[$v['id']]] = $v['fieldname'];
								}
							}
						}
					}
				}
				if(is_array($sysFieldFmt) && is_array($formFiledFmt))
					$appendCondtionArr = array_merge($sysFieldFmt , $formFiledFmt);
				elseif(is_array($sysFieldFmt) && !is_array($formFiledFmt) )
				$appendCondtionArr =$sysFieldFmt;
				elseif(!is_array($sysFieldFmt) && is_array($formFiledFmt) )
				$appendCondtionArr =$formFiledFmt;
				else
					$appendCondtionArr =array();
				$conditions = getAppendCondition($vo , $formFiledFmt);
				
				$systemconditionsTemp = getAppendCondition($vo , $sysFieldFmt);
// 				if($defaultLookupCondition){
// 					$systemconditions = $defaultLookupCondition;
// 					if($systemconditionsTemp){
// 						$systemconditions .= ' and '.$systemconditionsTemp;
// 					}
// 				}else{
// 					$systemconditions = $systemconditionsTemp;
// 				}
				$systemconditions = $systemconditionsTemp;
				
			}
			
			$val='';
			$tempModel =D($model);
			$temptable = '';
			//视图时取字段对应的真实字段所在真实表名
			if($lookupDetail["viewname"]){
				$temptable = $savefieldviewtable;
			}else{
				$temptable = $tempModel->getTableName();
			}
			$map = '';
			$value=$vo[$controllProperty[$property['fields']['dbfield']]];
			if($value){
				if(strpos( $value , ',' )){
					$valueStr=explode(',', $value);
					$map = $orgval." in ('".join("','", $valueStr)."')";
				}else{
					$map = $orgval."='".$value."'";
				}
			}
			//$viewval = $isedit ?(getFieldBy($value , $orgval , $orgchar , $tempModel->getTableName())):'';

// 			if(!$map){
// 				$viewval='';
// 			}else{ 
// 				$viewval = $isedit ?(getFieldData($orgchar , $temptable , $map , true , 1000)):'';
// 			}
			//多做了个分支，主要是应付视图里储存字段不唯一的情况
			/**
			 *当储存字段（以orderno=06为例）查询结果有n条记录，显示字段（以type为例）可以和储存字段保证一致性（当orderno=06时，查出的type全部都是“请假”）
			 */			
			if(!$map){
				$viewval='';
			}else{
				if(strpos( $value , ',' )){
					$viewval = $isedit ?(getFieldData($orgchar , $temptable , $map , true , 1000)):'';
				}else{
					$viewval = $isedit ?(getFieldBy($value , $orgval , $orgchar , $temptable)):''; //
				}
			
			}
			$html ="<div class=\"tml-input-lookup\">";
			
				$paramsEcho = arr2string($map);
				$controllPropertyEcho = '字段名：'.$controllProperty[$property['fields']['dbfield']].'__表名：'.$tempModel->getTableName();
				
// 				$html.=<<<EOF
				
// 				<script>
// 				console.log("lookup查询条件:{$controllPropertyEcho}--{$orgchar}--查询条件:{$paramsEcho} 修改状态：{$isedit}");
// 				</script>
// EOF;

				$readonlyCls='active_lookup';
				if($readonly){
					$readonlyCls='';
				}

			$html .="<input type=\"text\"  {$textCounterCheck} class=\"{$lookupgroup}.{$orgchar} {$chtml} input_new half_angle_input {$orgLookupText} {$readonlyCls} \" autocomplete=\"off\" readonly=\"readonly\"  value=\"".$viewval."\" />";
			$html .="<input type=\"hidden\" {$valCounterCheck}  class=\"{$required} {$lookupgroup}.{$orgval} {$orgLookuoVal}\" name=\"".$controllProperty[$property['fields']['dbfield']]."\" value=\"".$vo[$controllProperty[$property['fields']['dbfield']]]."\"  />";
			if($readonly){ // 只读情况
				// lookup 触发按钮
				$html .="<a class=\"icon_elm mid_icon_elm icon-plus\"></a>"; // $title
				// 清空 lookup 按钮
				$html .="<a title=\"清空信息-只读\" class=\"icon_elm icon-trash\"  href=\"javascript:void(0);\"></a>";
			}else{
				$formFiledFmt = array_flip($formFiledFmt);
				$conditionOprateString = "condition=\"\"";
				if(is_array($formFiledFmt)){
					$conditionOprateString = "condition=".json_encode($formFiledFmt);
				}
				
				// lookup 触发按钮
				$html .="<a class=\"icon_elm mid_icon_elm icon-plus\" syscondition=\"{$systemconditions}\"  newconditions=\"{$conditions}\"  {$conditionOprateString}  param=\"lookupchoice={$lookupchoice}&newconditions={$view}{$dtcon}\" href=\"__URL__/{$urls}\" lookupGroup=\"{$lookupgroup}\" ></a>";//$title
				// 清空 lookup 按钮
				$html .="<a title=\"清空信息-可读写\" class=\"icon_elm icon-trash\"  href=\"javascript:void(0);\" onclick=\"clearOrg('{$lookupgroup}');\"></a>";
			}
			$html .="</div>";
		}else{
			$func="";
			$parmFunc = array();
			// 处理子表及主表的lookup值转换 , modify by nbmxkj at 20150127 20
			// *****注意：func 与 funcdata 数组项一定要个数对应。****
			$scdmodel = D('SystemConfigDetail');
			$detailList = $scdmodel->getDetail(MODULE_NAME,'','','','status'); 
			$dval = $detailList[$controllProperty[$property['fields']['dbfield']]];
			if(is_array($dval)){
				if(count($dval['func']) >0){
					$varchar = "";
					foreach($dval['func'] as $k3=>$v3){
						//开始html字符
						if(isset($dval['extention_html_start'][$k3])){
							$varchar = $dval['extention_html_start'][$k3];
						}
						//中间内容
						$varchar .= getConfigFunction($vo[$dval['name']],$v3,$dval['funcdata'][$k3],$vo);
				
						if(isset($dval['extention_html_end'][$k3])){
							$varchar .= $dval['extention_html_end'][$k3];
						}
						//结束html字符
					}
					$html = $varchar;
				}else{
					$viewval=$vo[$controllProperty[$property['fields']['dbfield']]];
					$html = $viewval;
				}
			}
		}
		return $html;
	}
}