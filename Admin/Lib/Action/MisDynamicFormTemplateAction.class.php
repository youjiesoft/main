<?php
/**
 * 模板生成升级
 * @Title: MisDynamicFormTemplateAction 
 * @Package package_name
 * @Description: todo(动态建模-生成模板页面-功能升级) 
 * @author quqiang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015年8月20日 上午11:04:00 
 * @version V1.0
 */
class MisDynamicFormTemplateAction extends MisDynamicFormModelAction {
	
	/**
	 * @Title: createTemplate
	 * @Description: todo(生成模板物理文件)
	 *
	 * @param array $fieldData
	 * @param string $filedPathName
	 *        	存储文件夹名称
	 * @param boolean $isaudit
	 *        	是否带审批流，默认为false:不带审批流
	 * @author quqiang
	 *         @date 2014-8-21 上午10:11:59
	 * @throws
	 *
	 */
	protected function createTemplate($fieldData, $filedPathName, $isaudit = false) {
// 		import('@.ORG.dynamicForm.dynamicForm');
		$misdynamicform = M ( 'mis_dynamic_form_manage' );
		$formData = $misdynamicform->where ( "`actionname`='{$this->nodeName}'" )->find ();
		// $formid = getFieldBy($this->nodeName, "actionname", "id", "mis_dynamic_form_manage");
		$formid = $formData ['id'];
		
		
		// 扩展JS的导入路径
		$jsPath = '';
		$allControllConfig = $this->getfieldCategory ( $formid );
		// 获取当前节点的字段组件配置信息
		$curnodeData = $allControllConfig [$this->curnode];
		logs ( '组件属性：' . $this->pw_var_export ( $curnodeData ) );
		$f = ucfirst ( $filedPathName );
		$jsPath = $f;
		$addjs = $this->getJS ( $jsPath );
		$addjs .= $this->getJS ( $jsPath, 'addExtend' );
		
		$viewjs=$this->getJS ( $jsPath,'view' );
		$viewjs.=$this->getJS ( $jsPath, 'viewExtend' );
		
		$editjs = $this->getJS ( $jsPath, 'edit' );
		$editjs .= $this->getJS ( $jsPath, 'editExtend' );
		
		// 复选框样式
		$styles = "";
		// miniindex.html
		$mini_index = $styles . $this->getPage ('miniindex' );
		$file = TMPL_PATH . C ( 'DEFAULT_THEME' ) . "/" . $f . "/miniindex.html";
		$this->createFile($mini_index, $file);
		
		// 生成公共页面 dwzloadindex.html
		$dynamic_dwzloadindex = $this->getPage (  'dwzloadindex' );
		$file = TMPL_PATH . C ( 'DEFAULT_THEME' ) . "/" . $f . "/dwzloadindex.html";
		$this->createFile($dynamic_dwzloadindex, $file);
		
		
		
		if ($isaudit) {
			/**
			 * ***************************
			 * 	审批流表单 7个
			 * ***************************
			 */
			// add.html
			$dynamic_add = $addjs.$styles . $this->getPage ('audit_add' , $curnodeData, 0);
			$file = TMPL_PATH . C ( 'DEFAULT_THEME' ) . "/" . $f . "/add.html";
			$this->createFile($dynamic_add, $file);
			
			// edit.html
			$dynamic_edit = $editjs.$styles . $this->getPage ('audit_edit', $curnodeData, 1 );
			$file = TMPL_PATH . C ( 'DEFAULT_THEME' ) . "/" . $f . "/edit.html";
			$this->createFile($dynamic_edit, $file);
			
			// indexview.html
			$dynamic_indeview = $this->getPage ( 'audit_indexview' );
			$file = TMPL_PATH . C ( 'DEFAULT_THEME' ) . "/" . $f . "/indexview.html";
			$this->createFile($dynamic_indeview, $file);
			
			// view.html
			$dynamic_view = $viewjs.$styles . $this->getPage ( 'audit_view' );
			$file = TMPL_PATH . C ( 'DEFAULT_THEME' ) . "/" . $f . "/view.html";
			$this->createFile($dynamic_view, $file);
			
			// auditIndex.html
			$dynamic_auditindex = $this->getPage ( 'audit_auditIndex' );
			$file = TMPL_PATH . C ( 'DEFAULT_THEME' ) . "/" . $f . "/auditIndex.html";
			$this->createFile($dynamic_auditindex, $file);
			
			// auditView.html
			$dynamic_auditView = $viewjs.$styles . $this->getPage ( 'audit_auditView');
			$file = TMPL_PATH . C ( 'DEFAULT_THEME' ) . "/" . $f . "/auditView.html";
			$this->createFile($dynamic_auditView, $file);
			
			// auditEdit.html
			$dynamic_auditEdit = $editjs.$styles . $this->getPage ('audit_auditEdit', $curnodeData, 1 );
			$file = TMPL_PATH . C ( 'DEFAULT_THEME' ) . "/" . $f . "/auditEdit.html";
			$this->createFile($dynamic_auditEdit, $file);
			
			// 生成公用查看主体内容
			$dwzContentHtml = $this->getPage ('audit_contenthtml' ,  $curnodeData , 2 );
			$file = TMPL_PATH . C ( 'DEFAULT_THEME' ) . "/" . $f . "/contenthtml.html";
			$this->createFile($dwzContentHtml, $file);
			
		}else{
			/**
			 * ************************
			 * 非审核 4个
			 * ***********************
			 */
		    try {
		    	//查询当前表单有没有左侧菜单配置
		    	$isdatasouce=getFieldBy($formid, "formid", "isdatasouce","mis_dynamic_database_sub", "isdatasouce","1");
    			// index.html
    			if($isdatasouce){
    				$dynamic_index = $styles . $this->getPage ( 'index_tree' );
    				$file = TMPL_PATH . C ( 'DEFAULT_THEME' ) . "/" . $f . "/index.html";
    				$this->createFile($dynamic_index, $file);
    			}else{
    				$dynamic_index = $styles . $this->getPage ( 'index' );
    				$file = TMPL_PATH . C ( 'DEFAULT_THEME' ) . "/" . $f . "/index.html";
    				$this->createFile($dynamic_index, $file);
    			}
    				
    			// indexview.html
    			$indexview = $styles . $this->getPage ( 'indexview' );
    			$file = TMPL_PATH . C ( 'DEFAULT_THEME' ) . "/" . $f . "/indexview.html";
    			$this->createFile($indexview, $file);
    			// add.html
    			$dynamic_add = $addjs.$styles . $this->getPage ( 'add' , $curnodeData , 0);
    			$file = TMPL_PATH . C ( 'DEFAULT_THEME' ) . "/" . $f . "/add.html";
    			$this->createFile($dynamic_add, $file);
    			// edit.html
    			$dynamic_edit = $editjs.$styles . $this->getPage ( 'edit' , $curnodeData, 1 );
    			$file = TMPL_PATH . C ( 'DEFAULT_THEME' ) . "/" . $f . "/edit.html";
    			$this->createFile($dynamic_edit, $file);
    			// view.html
    			$dynamic_view = $viewjs.$styles . $this->getPage ( 'view' , $curnodeData, 2);
    			$file = TMPL_PATH . C ( 'DEFAULT_THEME' ) . "/" . $f . "/view.html";
    			$this->createFile($dynamic_view, $file);
				// 生成公用查看主体内容
				$dwzContentHtml = $styles . $this->getPage ( 'contenthtml' , $curnodeData, 2);
    			$file = TMPL_PATH . C ( 'DEFAULT_THEME' ) . "/" . $f . "/contenthtml.html";
    			$this->createFile($dwzContentHtml, $file);
				
		
		    }catch (Exception $e){
		        $this->error($e->getMessage());
		    }
		}
	}
	
	/**
	 * 模板变量替换
	 * @Title: templateReplace
	 * @Description: todo(唯一模板替换处理单元) 
	 * @param string $replaceArr	被替换内容,key为查找对象，val为替换内容
	 * @param string $template  	替换对象
	 * @author quqiang 
	 * @date 2015年8月23日 下午5:35:34 
	 * @throws
	 */
	protected function templateReplace($replaceArr , $template){
		$search		=	array();	// 查找对象集合
		$replace	=	array(); //	替换对象集合
		// 系统预制替换变量
		array_push($search, '#nodeName#');
		array_push($replace, $this->nodeName);
		
		array_push($search, '#tableName#');
		array_push($replace, $this->tableName);
		
   		array_push($search, '#nodeTitle#');
		array_push($replace, $this->nodeTitle);
		if(is_array($replaceArr) && count($replaceArr)){
			foreach ($replaceArr as $key=>$val){
				if(preg_match('#(.*?)#', $key)){
					array_push($search,$key);
					array_push($replace, $val);
				}
			}
		}
		
		if($template){
			$template = str_replace ( $search, $replace, $template );
		}
		return $template;
	}

	/**
	 * 获取页面内容
	 * @Title: getPage
	 * @Description: todo(获取要生成页面的页面内容信息) 
	 * @param	string		$pageCategory		页面分类，确定调用哪一种类型的页面基础模板
	 * @param	array			$fieldsinfo				组件配置信息	
	 * @param 	boolean	$pageOperate		表单操作类型，0：新增，1：修改  ,2：查看
	 * @author quqiang 
	 * @date 2015年8月23日 下午6:12:18 
	 * @throws
	 */
	protected function getPage($pageCategory ,$fieldsinfo , $pageOperate){
	    $template = $this->getPageConf ( $pageCategory );
	    $defaultControlTplArr;
	    if(empty($template)){
	        $msg = "指定模板  {$pageCategory} 不存在";
	        throw new NullDataExcetion($msg);
	    }
    	$ret = preg_match_all('/<check name="([\s\S]*?)">([\s\S]*?)<\/check>/', $template , $defaultControllTag);
    	if($ret && is_array($defaultControllTag)){
    	    // [0] 原始数据
    	    // [1] 字段名
    	    // [2] 逻辑判断输出
    	    
    	    foreach ($defaultControllTag[1] as $k=>$v){
    	        // 判断是否出现指定字段
    	        $logicArr = explode('<else/>', $defaultControllTag[2][$k]);
    	        if($fieldsinfo[$v]){
    	            // 需要将模板传入组件生成函数，以接收用户参数
    	            // 用户指定字段时，模板标记设定<else/>标签前为逻辑判断真输出内容
    	            $replacement = $logicArr[0];
    	            $pattern='/<check name="'.$v.'">([\s\S]*?)<\/check>/';
    	            $template = preg_replace($pattern, '',  $template);
    	            $defaultControlTplArr[$v]=$replacement;
    	        }else{
    	            // 用户没有指定字段时，如果模板没有设定<else/>标签则不做输出，
    	            // 将结果重新写入当前页面模板
    	            $replacement = $logicArr[1];
    	            $pattern='/<check name="'.$v.'">([\s\S]*?)<\/check>/';
    	            $template = preg_replace($pattern, $replacement,  $template);
//     	            preg_replace_callback($pattern, function ($vo){
//     	                var_dump($vo);
//     	            }, $template);
    	        }
    	    }
    	}
		$searchArr=array();
		if(is_array($fieldsinfo)){
			// 组件解析变量，
			$controll	 = '';
			// 需要生成组件
			if($pageOperate == 2){
				foreach ( $fieldsinfo as $k => $data ) {
					$controll .= $this->cellForView ( $data, 'view' );
				}
			}else{
				foreach ( $fieldsinfo as $k => $data ) {
					$controll .= $this->cellForOprate ( $data  ,'add', $defaultControlTplArr[$k]);
				}
			}
			// 处理 fieldset的开始结束问题
			$ret = preg_match('/#endfieldset#/', $controll);
			if($ret){
				$controll.='#endfieldset#';
			}
			$controll = preg_replace('/#endfieldset#/','', $controll , 1);
			$controll = str_replace('#endfieldset#', '</div>', $controll);
			
			$searchArr['#controll#']=$controll;
		}
		
		$html = $this->templateReplace ( $searchArr, $template );
		
		return $html;
	}

	/**
	 * 组件生成函数,查看页面专用
	 * @Title: cellForView
	 * @Description: todo(按配置文件中的组件模板生成组件最终效果)
	 * @param array $data	组件属性数据
	 * @param string $type 页面类型，按此类型找配置文件中的显示模板
	 * @author quqiang
	 * @date 2015年8月11日 下午12:44:10
	 * @throws
	 *
	 */
	private function cellForView($data, $type = 'add') {
		if (! is_array ( $data )) {
			$msg = '组件配置数据为空！！';
			throw new NullDataExcetion ( $msg );
		}
		// 返回变量
		$html = '';
		// 获取 当前组件的组件类型，
		$category = $data [$this->publicProperty ['catalog'] ['name']];
		// 显示状态
		$isshow = $data [$this->publicProperty ['isshow'] ['name']];
			
		// 获取当前组件的全部配置属性
		$property = $this->getProperty ( $category );
		// 获取显示模板
		$template = $this->controlConfig [$category] [$type];
		if (empty ( $template )) {
			$msg = "{$category}没有定义 {$type} 模板！";
			throw new NullDataExcetion ( $msg );
		}
		if ($this->nodeName) {
			$path = DConfig_PATH . "/Models/" . $this->nodeName . "/list.inc.php";
			$detailList = require $path;
			$this->datalist = $detailList;
		}
	
		/**
		 * 变量定义：
		 * 定义在组件生成使用到的变量名并赋初值空.
		 */
	
		$class = ""; // 主体布局样式归结
		$style = ""; // 主体布局行内样式
		$title = ""; // 标题内容
		$title_class = ""; // 标题类名
		$title_style = ""; // 标题行内样式
		$content = ""; // 显示内容
		$content_class = ""; // 显示内容容器类名
		$content_style = ""; // 显示内容行内样式
	
		$original = ""; // 数据格式转换前的值，真实值
		//$category = ""; // 组件类型
		$changeFunc = ""; // 格式转换函数
	
		$content = ""; // 显示数据
		$conditions = ""; // 获取数据时的过滤条件
	
		// 布局样式，包含： 必填、org、格式验证
		$contentCls = "col_" . $data [$property ['titlepercent'] ['name']] . "_" . $data [$property ['contentpercent'] ['name']] . " form_group_lay field_" . $data [$property ['fields'] ['name']];
		// 标题
		$title = $data [$property ['title'] ['name']];
		// 真实字段名
		$fields = $data [$property ['fields'] ['name']];
	
		// 属性合并
		$class = $contentCls;
		if(!$isshow){
			$style = "display:none;";
		}
		$original = "{\$vo['{$fields}']}";
		switch ($category) {
			case 'text' :
	
				// 单位转换
				$unitl = $data [$property ['unitl'] ['name']];
				$unitls = $data [$property ['unitls'] ['name']];
				if ($unitl && $unitls) {
					$changeFunc = "|unitExchange=###,{$unitl},{$unitls},3";
				}
				// 显示数据
				$content = "{\$vo['{$fields}']{$changeFunc}}";
				break;
			case 'password' :
				$content = "{:str_repeat('*', strlen(\$vo['{$fields}']))}";
				break;
			case 'tablabel' :
	
				// 该标签没有值可从DB中获取。
				$content = "";
				break;
			case 'hiddens' :
				$func = "";
				foreach ( $detailList as $dkey => $dval ) {
					if ($dval ['name'] == $data [$property ['fields'] ['name']]) {
						if ($dval ['func']) {
							$func .= $dval ['func'] [0] [0];
							if ($dval ['func'] [0] [0] = "getFieldBy") {
								$func .= "=";
							}
						}
						if ($dval ['funcdata']) {
							$func .= "'" . $dval ['funcdata'] [0] [0] [1];
							if ($dval ['funcdata'] [0] [0] [2]) {
								$func .= "','" . $dval ['funcdata'] [0] [0] [2] . "','" . $dval ['funcdata'] [0] [0] [3];
							}
						}
					}
				}
				if ($func) {
					$changeFunc = '|' . $func;
				}
				// 显示数据
				$content = "{\$vo['{$fields}']{$changeFunc}}";
				break;
			case 'select' :
				$conditions = '';
				$showtype = '1';
				$type = '';
				// 数据来源方式：table|selectlist
				if ($data [$property ['showoption'] ['name']]) {
					$type = 'selectlist';
					$key = $data [$property ['showoption'] ['name']];
				} else {
					if ($data [$property ['subimporttableobj'] ['name']] || $data [$property ['treedtable'] ['name']]) {
						$conditions = $data [$property ['conditions'] ['name']];
						$type = 'table';
						if ($data [$property ['subimporttableobj'] ['name']]) {
							// 查询表名
							$tableName = $data [$property ['subimporttableobj'] ['name']];
							// 值字段名
							$tableVal = $data [$property ['subimporttablefield2obj'] ['name']];
							// 显示字段名
							$tableText = $data [$property ['subimporttablefieldobj'] ['name']];
						} elseif ($data [$property ['treedtable'] ['name']]) {
							// 查询表名
							$tableName = $data [$property ['treedtable'] ['name']];
							// 值字段名
							$tableVal = $data [$property ['treevaluefield'] ['name']];
							// 显示字段名
							$tableText = $data [$property ['treeshowfield'] ['name']];
								
							$parentid = $data [$property ['treeparentfield'] ['name']];
							// 是否末级操作
							$mulit = $data [$property ['mulit'] ['name']];
							// 是否多选
							$isnextend = $data [$property ['isnextend'] ['name']];
							// 树形-下拉高度
							$treeheight = $data [$property ['treeheight'] ['name']];
							// 树形-下拉宽度
							$treewidth = $data [$property ['treewidth'] ['name']];
							// 树形-是否对话框模式
							$treedialog = $data [$property ['isdialog'] ['name']] ? true : false;
						}
					}
				}
				if ($type) {
					$func = '';
					switch ($type) {
						case 'selectlist' :
							$func = "getControllbyHtml('selectlist',array('type'=>'select','key'=>'$key','conditions'=>'$conditions','selected'=>\$vo[$fields] ,'showtype'=>'$showtype'))";
							break;
						case 'table' :
							$func = "getControllbyHtml('table',array('type'=>'select','table'=>'$tableName','id'=>'$tableVal','name'=>'$tableText','conditions'=>'$conditions','selected'=>\$vo[$fields],'showtype'=>'$showtype'))";
							break;
					}
				}
				// 显示数据
				if($func){
					$content = "{:{$func}}";
				}else{
					$content="";
				}
				break;
			case 'checkbox' :
	
				if ($data [$property ['subimporttableobj'] ['name']]) {
					$changeFunc = "|excelTplidTonameAppend='{$data[$property['subimporttablefield2obj']['name']]}','{$data[$property['subimporttablefieldobj']['name']]}','{$data[$property['subimporttableobj']['name']]}'";
				} else {
					$changeFunc = "|getSelectlistByName='{$data[$property['showoption']['name']]}'";
				}
				// 显示数据
				$content = "{\$vo['{$fields}']{$changeFunc}}";
				break;
			case 'radio' :
				if ($data [$property ['subimporttableobj'] ['name']]) {
					$changeFunc = "|getFieldBy='{$data[$property['subimporttablefield2obj']['name']]}','{$data[$property['subimporttablefieldobj']['name']]}','{$data[$property['subimporttableobj']['name']]}'}";
				} else {
					$changeFunc = "|getSelectlistValue='{$data[$property['showoption']['name']]}'";
				}
				// 显示数据
				$content = "{\$vo['{$fields}']{$changeFunc}}";
				break;
			case 'textarea' :
				if ($data [$property ['isrichbox'] ['name']]) {
					//$changeFunc = "|richtext2str";
				}
				$changeFunc='|htmlspecialchars_decode';
				// 显示数据
				$content = "{\$vo['{$fields}']{$changeFunc}}";
				break;
			case 'date' :
				$format = '';
				$format1 = '';
				$formatSouce = '';
				if ($data [$property ['format'] ['name']]) {
					$formatSouce = $data [$property ['format'] ['name']];
				} else {
					$formatSouce = $property ['format'] ['default'];
				}
				if ($formatSouce) {
					$temp = '';
					$temp = explode ( '@', $formatSouce );
					$format = "format=\"{dateFmt:'" . $temp [0] . "'}\"";
					if ($temp [1]) {
						$format1 = "='{$temp[1]}'";
					}
				}
				$changeFunc = "|transtime{$format1}";
				// 显示数据
				$content = "{\$vo['{$fields}']{$changeFunc}}";
				break;
			case 'lookup' :
				$content = "{:W('Lookup',array('1',\$vo,'{$data[$property['id']['name']]}','','false'))}";
				break;
			case 'lookupsuper' :
	
				// 不实现
				break;
			case 'upload' :
	
				$content = $this->getControl ( $data, true, true );
	
				// 				// 数据字段名
				// 				$filedName = $data [$property ['fields'] ['name']];
				// 				// 组件标题
				// 				$filedTitle = $data [$property ['title'] ['name']];
				// 				// 上传数量
				// 				$uploadNum = $data [$property ['uploadnum'] ['name']];
				// 				// 上传类型
				// 				$uploadType = $data [$property ['uploadtype'] ['name']];
				// 				$param = 'array("0"=>$uploadarry["' . $filedName . '"],"1"=>' . $filedName . ',"2"=>$fields[' . $filedName . '],"3"=>"' . $uploadNum . '","4"=>"' . $uploadType . '")';
				// 				// 显示数据
				// 				$content = "{:W('ShowUploadView',{$param})}";
				break;
			case 'fieldset' :
				break;
			case 'userselect' :
				$content=$this->getControl ( $data, true, true );
				// 不知道怎么做
				break;
			case 'areainfo' :
				// 显示数据
				$content = "{\$vo['{$fields}']{$changeFunc}}";
				break;
			case 'divider2' :
				// 不做
				break;
			case 'fiexdtext' :
				$fiexdtextstyle = '';
				if($data[$property['aligntype']['name']]){
					$fiexdtextstyle .="text-align:".$data[$property['aligntype']['name']].";";
				}else{
					$fiexdtextstyle .="text-align:{$property['aligntype']['default']};";
				}
				if($data[$property['backgroundcolor']['name']]){
					$fiexdtextstyle .="background:#".$data[$property['backgroundcolor']['name']].";";
				}else{
					$fiexdtextstyle .="background:#{$property['backgroundcolor']['default']};";
				}
				if($data[$property['fontcolor']['name']]){
					$fiexdtextstyle .="color:#".$data[$property['fontcolor']['name']].";";
				}else{
					$fiexdtextstyle .="color:#{$property['fontcolor']['default']};";
				}
				if($fiexdtextstyle[$property['fontsize']['name']]){
					$fiexdtextstyle .="font-size:".$data[$property['fontsize']['name']]."px;";
				}else{
					$fiexdtextstyle .="font-size:{$property['fontsize']['default']}px;";
				}
				if($data[$property['fontheight']['name']]){
					$fiexdtextstyle .="line-height:".$data[$property['fontheight']['name']]."px;";
				}else{
					$fiexdtextstyle .="line-height:{$property['fontheight']['default']}px;";
				}
				if($controllProperty[$property['fontweight']['name']]){
					$fiexdtextstyle .="font-weight:".$controllProperty[$property['fontweight']['name']].";";
				}else{
					$fiexdtextstyle .="font-weight:{$property['fontweight']['default']};";
				}
				$content = $data[$property['title']['name']];
				$content_style = $fiexdtextstyle;
				break;
			case 'subtitles' :
				$content=$this->getControl ( $data, true, true );
				break;
			case 'datatable' :
				$content='<div class="pos_relative">'.$this->getControl ( $data, true, true ).'</div>';
				break;
		}
		$searchArr=array(
				'#class#'=>$class,
				'#style#'=>$style,
				'#title#'=>$title,
				'#title_class#'=>$title_class,
				'#title_style#'=>$title_style,
				'#content#'=>$content,
				'#content_class#'=>$content_class,
				'#content_style#'=>$content_style,
				'#original#'=>$original,
				'#category#'=>$category,
				'#fields#'=>$fields
		);
		$html = $this->templateReplace($searchArr , $template);
		return $html;
	}
	
	/**
	 * 组件生成函数,新增、修改页面专用
	 * @Title: cellForOprate
	 * @Description: todo(o(按配置文件中的组件模板生成组件最终效果) 
	 * @param array $data	组件属性数据
	 * @param string $type 页面类型，按此类型找配置文件中的显示模板 
	 * @param string $inTemplate 特殊情况下的重置组件模板结构
	 * @author quqiang 
	 * @date 2015年8月23日 下午7:37:13 
	 * @throws
	 */
	private function cellForOprate($data , $type='add' , $inTemplate){
		if (! is_array ( $data )) {
			$msg = '组件配置数据为空！！';
			throw new NullDataExcetion ( $msg );
		}
		// 返回变量
		$html = '';
		// 获取 当前组件的组件类型，
		$category = $data [$this->publicProperty ['catalog'] ['name']];
		// 显示状态
		$isshow = $data [$this->publicProperty ['isshow'] ['name']];
		// 获取当前组件的全部配置属性
		$property = $this->getProperty ( $category );
		// 获取显示模板
		//$template = $this->controlConfig [$category] [$type];
		$template = $this->getControllConf($category);
		$template = $inTemplate ?$inTemplate :$template;
		if (empty ( $template )) {
			$msg = "组件 {$category} 没有定义模板！";
			throw new NullDataExcetion ( $msg );
		}
		if ($this->nodeName) {
			$path = DConfig_PATH . "/Models/" . $this->nodeName . "/list.inc.php";
			$detailList = require $path;
			$this->datalist = $detailList;
		}
		$searchArr=array();
		/**
		 * 变量定义：
		 * 定义在组件生成使用到的变量名并赋初值空.
		 */
	
		$class = ""; // 主体布局样式归结
		$style = ""; // 主体布局行内样式
		$title = ""; // 标题内容
		$title_class = ""; // 标题类名
		$title_style = ""; // 标题行内样式
		$content = ""; // 显示内容
		$content_class = ""; // 显示内容容器类名,必填、org、格式验证
		$content_style = ""; // 显示内容行内样式
	
		$original = ""; // 数据格式转换前的值，真实值
		//$category = ""; // 组件类型
		$changeFunc = ""; // 格式转换函数
	
		$unitlsChar = ""; // 单位转换时的显示单位
		
		$content = ""; // 显示数据
		$conditions = ""; // 获取数据时的过滤条件
		$propertyid = $data[$property['id']['name']];//通用属性，配置ID
		
		//$format = ''; // 日期组件特殊属性
		$paramArr=''; // 参数集合，组件不同参数集合构成与写法也存在差异.
		
		// 布局样式
		$contentCls = "col_" . $data [$property ['titlepercent'] ['name']] . "_" . $data [$property ['contentpercent'] ['name']] . " form_group_lay field_" . $data [$property ['fields'] ['name']];
		// 标题
		$title = $data [$property ['title'] ['name']];
		// 真实字段名
		$fields = $data [$property ['fields'] ['name']];
		
		if($data[$property['requiredfield'] ['name']]){
		    $content_class_arr[]="required";
		}
		if($data [$property ['org'] ['name']]){
		    $content_class_arr[]=$data [$property ['org'] ['name']];
		}
		if($data [$property ['checkfunc'] ['name']]){
		    $content_class_arr[]=$data [$property ['checkfunc'] ['name']];
		}
		
		$content_class=implode(' ' , $content_class_arr);
		// 属性合并
		$class = $contentCls;
		if(!$isshow){
			$style = "display:none;";
		}
		$original = "{\$vo['{$fields}']}";
		if($data [$property ['islock'] ['name']] == '0' ){
			$paramArr="readonly=\"readonly\"";
		}
		// 将组件全属性按属性名称封装到替换数据中
		foreach ($data as $k=>$v){
// 		    var_dump($k);
		    if($k==$property ['conditions'] ['name']){
		        $cs=array('&lt;','&gt;');
		        $cr=array('<','>');
		        $v = str_replace($cs ,$cr , $v);
		    }
			$searchArr["#{$k}#"] = $v;
		}
		switch ($category) {
			case 'text' :
			
				if($data[$property ['callback'] ['name']]){
					$searchArr["#callback#"] = 'callback="'.$data[$property ['callback'] ['name']].'"';
				}
				// 显示数据
				//$content=$this->getControl ( $data, true, false );
				if($data[$property ['unitls'] ['name']] && $data[$property ['unitl'] ['name']]){
					$template = $this->getControllConf($category.'_unit');
					$unitlsChar = $this->untils[$data[$property ['unitls'] ['name']]];
				}
				break;
			case 'password' :
				$content = "{:str_repeat('*', strlen(\$vo['{$fields}']))}";
				break;
			case 'tablabel' :
				// 该标签没有值可从DB中获取。
				$content = "";
				break;
			case 'hiddens' :
				if($data[$property ['callback'] ['name']]){
					$searchArr["#callback#"] = 'callback="'.$data[$property ['callback'] ['name']].'"';
				}
				break;
			case 'select' :
				// 显示数据
				//$content=$this->getControl ( $data, true, false );
			    if($data[$property ['treedtable'] ['name']] && $data[$property ['treeshowfield'] ['name']] && $data[$property ['treevaluefield'] ['name']]&& $data[$property ['treeparentfield'] ['name']]){
			        $template = $this->getControllConf($category.'_tree');
			    }
			    
				break;
			case 'checkbox' :
				// 显示数据
				$content=$this->getControl ( $data, true, false );
				break;
			case 'radio' :
				// 显示数据
				$content=$this->getControl ( $data, true, false );
				break;
			case 'textarea' :
			    // 显示数据
				//$content=$this->getControl ( $data, true, false );
				
				if($data[$property ['isrichbox'] ['name']]){
				   $content_class .= " ueditor";
				}
				break;
			case 'date' :
// 				$format = '';
// 				$format1 = '';
// 				$formatSouce = '';
// 				if ($data [$property ['format'] ['name']]) {
// 					$formatSouce = $data [$property ['format'] ['name']];
// 				} else {
// 					$formatSouce = $property ['format'] ['default'];
// 				}
// 				if ($formatSouce) {
// 					$temp = '';
// 					$temp = explode ( '@', $formatSouce );
// 					$format = "{dateFmt:'" . $temp [0] . "'}";
// 					if ($temp [1]) {
// 						$format1 = "='{$temp[1]}'";
// 					}
// 				}
// 				$changeFunc = "|transtime{$format1}";
				// 显示数据
				//$content=$this->getControl ( $data, true, false );
				break;
			case 'lookup' :
				$content = "{:W('Lookup',array('1',\$vo,'{$data[$property['id']['name']]}','','false'))}";
				break;
			case 'lookupsuper' :
	
				// 不实现
				break;
			case 'upload' :
				//$content = $this->getControl ( $data, true, false );
				break;
			case 'uploadpic' :
// 			    // 等比勾取最小值
// 			    $data[$property ['cropratiomin'] ['name']]
// 			    // 等比勾取最大值
// 			    $data[$property ['cropratiomax'] ['name']]
// 			    // 默认选中区域
// 			    $data[$property ['cropdefaultcheck'] ['name']]
// 			    // 自由选取
// 			    $data[$property ['cropfreed'] ['name']] 
			    unset($temp);
			    $cropfreed =  $data[$property ['cropfreed'] ['name']]; // 是否允许调整大小
			    $cropdefaultcheck =  $data[$property ['cropdefaultcheck'] ['name']];// 初始化时默认圈中区域
			    $cropratiomin =  $data[$property ['cropratiomin'] ['name']];// 勾取最小值
			    $cropratiomax =  $data[$property ['cropratiomax'] ['name']];// 勾取最大值
			    $cropratio =  $data[$property ['cropratio'] ['name']];// 等比操作
			    $temp['aspectRatio']=$cropratio; // 是否等比操作
			    $temp['allowResize']=$cropfreed; // 是否允许调整大小
			    if($cropdefaultcheck){
			        $cropdefaultcheckArr = explode(',', $cropdefaultcheck);
			        if(count($cropdefaultcheckArr) == 4){
			            $temp['setSelect']=$cropdefaultcheckArr; // 初始化时默认圈中区域
			        }
			    }
			    if($cropratiomin){
			        $cropratiominArr = explode(',', $cropratiomin);
			        if(count($cropratiominArr) == 2){
			            $temp['minSize']=$cropratiominArr; // 勾取最小值
			        }
			    }
			    if($cropratiomax){
			        $cropratiomaxArr = explode(',', $cropratiomax);
			        if(count($cropratiomaxArr) == 2){
			            $temp['maxSize']=$cropratiomaxArr; // 勾取最大值
			        }
			    }
			    $paramArr=json_encode($temp);
			    break;
			case 'fieldset' :
				break;
			case 'userselect' :
				$content=$this->getControl ( $data, true, false );
				// 不知道怎么做
				break;
			case 'areainfo' :
				// 显示数据
				$content=$this->getControl ( $data, true, false );
				break;
			case 'divider2' :
				// 不做
				// 显示数据
				//$content=$this->getControl ( $data, true, false );
				break;
			case 'fiexdtext' :
				$fiexdtextstyle = '';
				if($data[$property['aligntype']['name']]){
					$fiexdtextstyle .="text-align:".$data[$property['aligntype']['name']].";";
				}else{
					$fiexdtextstyle .="text-align:{$property['aligntype']['default']};";
				}
				if($data[$property['backgroundcolor']['name']]){
					$fiexdtextstyle .="background:#".$data[$property['backgroundcolor']['name']].";";
				}else{
					$fiexdtextstyle .="background:#{$property['backgroundcolor']['default']};";
				}
				if($data[$property['fontcolor']['name']]){
					$fiexdtextstyle .="color:#".$data[$property['fontcolor']['name']].";";
				}else{
					$fiexdtextstyle .="color:#{$property['fontcolor']['default']};";
				}
				if($fiexdtextstyle[$property['fontsize']['name']]){
					$fiexdtextstyle .="font-size:".$data[$property['fontsize']['name']]."px;";
				}else{
					$fiexdtextstyle .="font-size:{$property['fontsize']['default']}px;";
				}
				if($data[$property['fontheight']['name']]){
					$fiexdtextstyle .="line-height:".$data[$property['fontheight']['name']]."px;";
				}else{
					$fiexdtextstyle .="line-height:{$property['fontheight']['default']}px;";
				}
				if($controllProperty[$property['fontweight']['name']]){
					$fiexdtextstyle .="font-weight:".$controllProperty[$property['fontweight']['name']].";";
				}else{
					$fiexdtextstyle .="font-weight:{$property['fontweight']['default']};";
				}
				$content = $data[$property['title']['name']];
				$content_style = $fiexdtextstyle;
				break;
			case 'subtitles' :
				$content=$this->getControl ( $data, true, true );
				break;
			case 'datatable' :
				$content='<div class="pos_relative">'.$this->getControl ( $data, true, false ).'</div>';
				break;
		}
		$searchArr['#class#']=$class;
		$searchArr['#style#']=$style;
		$searchArr['#title#']=$title;
		$searchArr['#title_class#']=$title_class;
		$searchArr['#title_style#']=$title_style;
		$searchArr['#content#']=$content;
		$searchArr['#content_class#']=$content_class;
		$searchArr['#content_style#']=$content_style;
		$searchArr['#original#']=$original;
		$searchArr['#category#']=$category;
		$searchArr['#fields#']=$fields;
		$searchArr['#propertyid#']=$propertyid;
		//$searchArr["#format#"]=$format;
		$searchArr["#unitlsChar#"]=$unitlsChar;
		$searchArr["#paramArr#"]=$paramArr;
		
		$html = $this->templateReplace($searchArr , $template);
		return $html;
	}
	/**
	 * @Title: getControl
	 * @Description: todo(将组件属性生成组件实际html代码)
	 *
	 * @param array $controllProperty
	 *        	组件信息
	 * @param boolean $isedit
	 *        	是否是编辑，默认flase:新增
	 * @param boolean $isreturnvalue
	 *        	是否直接显示值
	 * @param boolean $special
	 * @author quqiang
	 *         @date 2014-8-21 上午10:12:43
	 * @throws
	 *
	 */
	private function getControl($controllProperty, $isedit = false, $isreturnvalue = false, $special = true) {
		// 重新取得当前文件的list配置文件
		if ($this->nodeName) {
			$path = DConfig_PATH . "/Models/" . $this->nodeName . "/list.inc.php";
			$detailList = require $path;
			$this->datalist = $detailList;
		}
		// print_r($this->datalist);
		// if($_POST['nodename']=='MisAutoPvb'){
		// print_r($detailList);
		// }
		if (! is_array ( $controllProperty )) {
			return '';
		}
		$property = $this->getProperty ( $controllProperty ['catalog'] );
		if ($controllProperty [$property ['checkfunc'] ['name']]) {
			$chtml = $controllProperty [$property ['checkfunc'] ['name']];
		}
		$readonly = 0;
		$readonlyStr = '';
		$islockHtml = '<div class="display_none {$classNodeSettingArr[\'' . $controllProperty [$property ['fields'] ['name']] . '\']}">$viewval</div>';
		if (! $controllProperty [$property ['islock'] ['name']]) {
			$chtml .= " readonly";
			$readonly = 1;
			$readonlyStr = "readonly=\"readonly\" ";
		}
		$html = '';
		if ($controllProperty ['catalog'] == 'tablabel') {
			$prefTag = "\t\t\t<label class=\"label_new\">" . $controllProperty [$property ['title'] ['name']] . "</label>\r\n\t\t\t\t\t\t\t\t";
		} else {
			$prefTag = "\t\t\t<label class=\"label_new\">{\$fields[\"" . $controllProperty [$property ['fields'] ['name']] . "\"]}:</label>\r\n\t\t\t\t\t\t\t\t";
		}
		$org = '';
		if ($controllProperty [$property ['org'] ['name']]) {
			$org = $controllProperty [$property ['org'] ['name']];
		}
		$required = ''; // 必填验证
		if ($controllProperty [$property ['requiredfield'] ['name']]) {
			$required = 'required';
		}
		$dropback = "dropback=\"{$controllProperty [$property ['dropback'] ['name']]}\"";
		$chtml = $required . ' ' . $chtml;
		// 读取当前list.inc.php文件
		switch ($controllProperty ['catalog']) {
			case 'text' :
				// 绑定事件
				$tagEvent = $controllProperty [$property ['tagevent'] ['name']]; // 获取事件名
				$calculate = $controllProperty [$property ['calculate'] ['name']]; // 获取事件名
				$tagEventSytr = '';
				if ($tagEvent) {
					$tagEventSytr = 'on' . ucwords ( $tagEvent ) . '="' . $this->nodeName . '_' . $controllProperty [$property ['fields'] ['name']] . '_' . $tagEvent . '(this);"';
				}
				$tagEventSytr = $dropback.' '.$tagEventSytr;
				$valStr = '';
				$unitChangeStr = '';
				if ($isedit) {
					$unitl = $controllProperty [$property ['unitl'] ['name']];
					$unitls = $controllProperty [$property ['unitls'] ['name']];
					if ($unitl && $unitls) {
						$unitChangeStr = "|unitExchange=###,{$unitl},{$unitls},2";
					}
					$valStr = "{\$vo['{$controllProperty[$property['fields']['name']]}']{$unitChangeStr}}";
				}
	
				if (! $isreturnvalue) {
					$unitlsStr = '';
					$unitlsPanelStart = '';
					$unitlsPanelEnd = '';
					$unitlsCls = '';
					$uFStr = '';
					$defaultCls = 'input_new ';
					if ($controllProperty [$property ['unitls'] ['name']]) {
						$unitlsPanelStart = "<div class=\"tml-input-unit\">";
						$unitlsPanelEnd = '</div>';
						$unitlsStr = "<span class=\"icon_elm icon_unit\" title=\"{$this->untils[$controllProperty[$property['unitls']['name']]]}\">{$this->untils[$controllProperty[$property['unitls']['name']]]}</span>";
						// $unitlsCls=' unitlpase ';
						$defaultCls .= ' half_angle_input';
						// $uFStr=" unitl=\"{$controllProperty[$property['unitls']['name']]}\" ";
					}
					$unitlsCls .= $defaultCls;
						
					// checkfor 属性设置
					// checkfor 当 $controllProperty[$property['checkfortable']['name']] 指定了表名，
					// $controllProperty[$property['checkforshow']['name']] 显示字段
					// $controllProperty[$property['checkforbindd']['name']] 绑定真实值
					// 单换算功能 不能与checkfor功能同用，原因为：checkfor会生成hidden标签，单们换算也会生成hidden无法赋值。
					/*
					 * if($controllProperty[$property['unitls']['name']]){
					 * $html="\r\n\t\t\t\t\t{$prefTag}<input $tagEventSytr type=\"text\" {$readonlyStr} unitl=\"{$controllProperty[$property['unitls']['name']]}\" name=\"".
					 * $controllProperty[$property['fields']['name']]."\" class=\"input_new {$org} {$unitlsCls} unitlpase {$chtml}\" value=\"".($isedit?"{\$vo['".
					 * $controllProperty[$property['fields']['name']]."']}":'')."\">";
					 * }else{ 单位与checkfor 不共用
					 */
					if ($controllProperty [$property ['checkfortable'] ['name']]) {
						$insert = $controllProperty [$property ['checkforbindd'] ['name']] ? htmlspecialchars ( "insert=\"" . $controllProperty [$property ['checkforbindd'] ['name']] . "\"" ) : ''; // 隐藏域绑定字段名
						$show = $controllProperty [$property ['checkforshow'] ['name']] ? htmlspecialchars ( "show=\"" . $controllProperty [$property ['checkforshow'] ['name']] . "\"" ) : ''; // 显示字段名
						$table = $controllProperty [$property ['checkfortable'] ['name']] ? htmlspecialchars ( "checkfor=\"" . $controllProperty [$property ['checkfortable'] ['name']] . "\"" ) : ''; // checkfor查询表
						$orderby = $controllProperty [$property ['checkfororderby'] ['name']] ? htmlspecialchars ( "order=\"" . $controllProperty [$property ['checkfororderby'] ['name']] . "\"" ) : ''; // 排序条件
						$iswrite = $controllProperty [$property ['checkforiswrite'] ['name']] ? htmlspecialchars ( "iswrite=\"" . $controllProperty [$property ['checkforiswrite'] ['name']] . "\"" ) : ''; // 是否清除未找到内容
						$fileds = $controllProperty [$property ['checkfororfields'] ['name']] ? htmlspecialchars ( "fields=\"array(" . $controllProperty [$property ['checkfororfields'] ['name']] . ");\"" ) : ''; // 查看字段
						$map = $controllProperty [$property ['checkformap'] ['name']] ? htmlspecialchars ( "newconditions=\"" . $controllProperty [$property ['checkformap'] ['name']] . "\"" ) : ''; // 过滤条件
						$org = '';
						// 时当前文本框 没得name属性，
						if ($controllProperty [$property ['checkforbindd'] ['name']]) {
							$iswrite = '';
							$hiden = "<input type=\"hidden\" {$uFStr} name=\"" . $controllProperty [$property ['fields'] ['name']] . "\" value=\"" . ($isedit ? "{\$vo['" . $controllProperty [$property ['fields'] ['name']] . "']}" : '') . "\">";
								
							$viewval = $isedit ? "{\$vo['" . $controllProperty [$property ['fields'] ['name']] . "']|getFieldBy='{$controllProperty[$property['checkforbindd']['name']]}','{$controllProperty[$property['checkforshow']['name']]}','{$controllProperty[$property['checkfortable']['name']]}'}" : '';
							$html = "\r\n\t\t\t\t\t{$prefTag}{$hiden}\r\n<input $tagEventSytr type=\"text\" {$readonlyStr}  class=\"{$org} input_new {$unitlsCls} checkByInput {$chtml}\" \r\n" . "{$table} \r\n" . "{$insert} \r\n" . "{$show} \r\n" . "{$map} \r\n" . "{$orderby} \r\n" . "{$fileds} \r\n" . "{$iswrite} autocomplete=\"on\" value=\"" . $viewval . "\">"; // id:{$controllProperty[$property['checkforbindd']['name']]}
							$islockHtml = parameReplace ( '$viewval', $viewval, $islockHtml );
						} else {
							$viewval = $valStr;
							$html = "\r\n\t\t\t\t\t{$prefTag}<input {$uFStr} $tagEventSytr type=\"text\" {$readonlyStr} name=\"" . $controllProperty [$property ['fields'] ['name']] . "\" class=\"{$org} {$unitlsCls} input_new {$chtml}\" value=\"" . $viewval . "\">";
							$islockHtml = parameReplace ( '$viewval', $viewval, $islockHtml );
						}
					} else {
						$viewval = $valStr;
						$html = "\r\n\t\t\t\t\t{$prefTag}<input {$uFStr} $tagEventSytr type=\"text\" {$readonlyStr} name=\"" . $controllProperty [$property ['fields'] ['name']] . "\" class=\"{$org} {$unitlsCls} input_new {$chtml}\" value=\"" . $valStr . "\">";
						$islockHtml = parameReplace ( '$viewval', $viewval, $islockHtml );
					}
					// }
				} else {
					/* 查看页面单位转换 */
					$unitChangeStr = '';
					$unitl = $controllProperty [$property ['unitl'] ['name']];
					$unitls = $controllProperty [$property ['unitls'] ['name']];
					if ($unitl && $unitls) {
						$unitChangeStr = "|unitExchange=###,{$unitl},{$unitls},3";
					}
					$valStr = "{\$vo['{$controllProperty[$property['fields']['name']]}']{$unitChangeStr}}";
					/**
					 * **************
					 */
					if ($controllProperty [$property ['checkfortable'] ['name']]) {
						if ($controllProperty [$property ['checkforbindd'] ['name']]) {
							// id : {$controllProperty[$property['checkforbindd']['name']]}
							$viewval = '';
							$html = "{\$vo['" . $controllProperty [$property ['fields'] ['name']] . "']|getFieldBy='{$controllProperty[$property['checkforbindd']['name']]}','{$controllProperty[$property['checkforshow']['name']]}','{$controllProperty[$property['checkfortable']['name']]}'}";
						} else {
							$viewval = '';
							$html = $valStr; // "{\$vo['".$controllProperty[$property['fields']['name']]."']}";
						}
					} else {
						$viewval = '';
						$html = $valStr; // "{\$vo['".$controllProperty[$property['fields']['name']]."']}";
					}
					// $viewval= '';
					// $islockHtml='';
				}
				$islockHtml = parameReplace ( '$viewval', $viewval, $islockHtml );
				$html = $unitlsPanelStart . htmlspecialchars_decode ( $html ) . $unitlsStr . $unitlsPanelEnd . $islockHtml;
				break;
			case 'password' :
				if (! $isreturnvalue) {
					$viewval = $isedit ? "{\$vo['" . $controllProperty [$property ['fields'] ['name']] . "']}" : '';
					$html = "<input type=\"password\" class=\" input_new\" name=\"" . $controllProperty [$property ['fields'] ['name']] . "\" value=\"" . $viewval . "\" />";
					$html = $prefTag . $html;
				} else {
					$viewval = '********';
					$html = $viewval;
				}
				$islockHtml = parameReplace ( '$viewval', $viewval, $islockHtml );
				$html .= $islockHtml;
				break;
			case 'hiddens' :
				if (! $isreturnvalue) {
					$viewval = $isedit ? "{\$vo['" . $controllProperty [$property ['fields'] ['name']] . "']}" : '';
					$html = "<input type=\"hidden\" class=\"{$org} input_new\" name=\"" . $controllProperty [$property ['fields'] ['name']] . "\" value=\"" . $viewval . "\" />";
				} else {
					$func = "";
					foreach ( $detailList as $dkey => $dval ) {
						if ($dval ['name'] == $controllProperty [$property ['fields'] ['name']]) {
							if ($dval ['func']) {
								$func .= $dval ['func'] [0] [0];
								if ($dval ['func'] [0] [0] = "getFieldBy") {
									$func .= "=";
								}
							}
							if ($dval ['funcdata']) {
								$func .= "'" . $dval ['funcdata'] [0] [0] [1];
								if ($dval ['funcdata'] [0] [0] [2]) {
									$func .= "','" . $dval ['funcdata'] [0] [0] [2] . "','" . $dval ['funcdata'] [0] [0] [3] . "'}";
								} else {
									$func .= "'}";
								}
							}
						}
					}
					if ($func) {
						$viewval = '';
						$html = "{\$vo['" . $controllProperty [$property ['fields'] ['name']] . "']|" . $func;
					} else {
						$viewval = '';
						$html = "{\$vo['" . $controllProperty [$property ['fields'] ['name']] . "']}";
					}
					// $viewval = '';
					// $islockHtml='';
				}
				$islockHtml = parameReplace ( '$viewval', $viewval, $islockHtml );
				$html .= $islockHtml;
				break;
			case 'tablabel' :
	
				// $html="\r\n\t\t\t\t\t{$prefTag}\r\n<input class=\"{$org} nbm_tablabel\" value=\"\" type=\"text\" readonly=\"readonly\" disabled=\"disabled\" />";
				// $html="\r\n\t\t\t\t\t{$prefTag}\r\n<input class=\"{$org} nbm_tablabel input_new \" value=\"\" type=\"text\" readonly=\"readonly\" disabled=\"disabled\" />";
				$html = "\r\n\t\t\t\t\t{$prefTag}\r\n<input class=\"{$org}  input_new nbm_tablabel\" value=\"\" type=\"text\" readonly=\"readonly\" disabled=\"disabled\" />";
				if ($isreturnvalue) {
					// $viewval='';
					// $islockHtml = '';
				}
				$viewval = '';
				$islockHtml = parameReplace ( '$viewval', $viewval, $islockHtml );
				$html .= $islockHtml;
				break;
			case 'subtitles' :
				$html = "\r\n\t\t\t\t\t";
	
				$style = "style=\"";
				if ($controllProperty [$property ['aligntype'] ['name']]) {
					$style .= "text-align:" . $controllProperty [$property ['aligntype'] ['name']] . ";";
				} else {
					$style .= "text-align:{$property['aligntype']['default']};";
				}
				if ($controllProperty [$property ['backgroundcolor'] ['name']]) {
					$style .= "background:#" . $controllProperty [$property ['backgroundcolor'] ['name']] . ";";
				} else {
					$style .= "background:#{$property['backgroundcolor']['default']};";
				}
				if ($controllProperty [$property ['fontcolor'] ['name']]) {
					$style .= "color:#" . $controllProperty [$property ['fontcolor'] ['name']] . ";";
				} else {
					$style .= "color:#{$property['fontcolor']['default']};";
				}
				if ($controllProperty [$property ['fontsize'] ['name']]) {
					$style .= "font-size:" . $controllProperty [$property ['fontsize'] ['name']] . "px;";
				} else {
					$style .= "font-size:{$property['fontsize']['default']}px;";
				}
				if ($controllProperty [$property ['fontheight'] ['name']]) {
					$style .= "line-height:" . $controllProperty [$property ['fontheight'] ['name']] . "px;";
				} else {
					$style .= "line-height:{$property['fontheight']['default']}px;";
				}
				if ($controllProperty [$property ['fontweight'] ['name']]) {
					$style .= "font-weight:" . $controllProperty [$property ['fontweight'] ['name']] . ";";
				} else {
					$style .= "font-weight:{$property['fontweight']['default']};";
				}
				$style .= "\"";
				$viewval = $controllProperty [$property ['title'] ['name']];
				$html .= "<div class=\"fieldset_legend_toggle side-catalog-text side-catalog-firstanchor\" {$style}>" . "{$viewval}" . "</div>";
				if ($isreturnvalue) {
					// $viewval='';
					// $islockHtml = '';
				}
				$islockHtml = parameReplace ( '$viewval', $viewval, $islockHtml );
				$html .= $islockHtml;
				break;
			case 'fiexdtext' :
				$html = "\r\n\t\t\t\t\t";
				$style = "style=\"";
				if ($controllProperty [$property ['aligntype'] ['name']]) {
					$style .= "text-align:" . $controllProperty [$property ['aligntype'] ['name']] . ";";
				} else {
					$style .= "text-align:{$property['aligntype']['default']};";
				}
				if ($controllProperty [$property ['backgroundcolor'] ['name']]) {
					$style .= "background:#" . $controllProperty [$property ['backgroundcolor'] ['name']] . ";";
				} else {
					$style .= "background:#{$property['backgroundcolor']['default']};";
				}
				if ($controllProperty [$property ['fontcolor'] ['name']]) {
					$style .= "color:#" . $controllProperty [$property ['fontcolor'] ['name']] . ";";
				} else {
					$style .= "color:#{$property['fontcolor']['default']};";
				}
				if ($controllProperty [$property ['fontsize'] ['name']]) {
					$style .= "font-size:" . $controllProperty [$property ['fontsize'] ['name']] . "px;";
				} else {
					$style .= "font-size:{$property['fontsize']['default']}px;";
				}
				if ($controllProperty [$property ['fontheight'] ['name']]) {
					$style .= "line-height:" . $controllProperty [$property ['fontheight'] ['name']] . "px;";
				} else {
					$style .= "line-height:{$property['fontheight']['default']}px;";
				}
				/*
				 * 高度影响 页面显示效果
				 * if($controllProperty[$property['contentheight']['name']]){
				 * $style .="height:".$controllProperty[$property['contentheight']['name']]."px;";
				 * }else{
				 * $style .="height:12px;";
				 * }
				 */
				if ($controllProperty [$property ['fontweight'] ['name']]) {
					$style .= "font-weight:" . $controllProperty [$property ['fontweight'] ['name']] . ";";
				} else {
					$style .= "font-weight:{$property['fontweight']['default']};";
				}
				// $style .="background:#".$controllProperty[$property['backgroundcolor']['name']]?$controllProperty[$property['backgroundcolor']['name']]:"fff".";";
				// $style .="color:#".$controllProperty[$property['fontcolor']['name']]?$controllProperty[$property['fontcolor']['name']]:'333'.";";
				// $style .="font-size:".$controllProperty[$property['fontsize']['name']]?$controllProperty[$property['fontsize']['name']]:'12' ."px;";
				// $style .="line-height:".$controllProperty[$property['fontheight']['name']]?$controllProperty[$property['fontheight']['name']]:'12'."px;";
				// $style .="height:".$controllProperty[$property['contentheight']['name']]?$controllProperty[$property['contentheight']['name']]:'12'."px;";
				// $style .="font-weight:".$controllProperty[$property['fontweight']['name']]?$controllProperty[$property['fontweight']['name']]:"400".";\"";
				// "border:1px solid #d8d8d8;".
				$style .= "\"";
				// tml-form-text
				$viewval = $controllProperty [$property ['title'] ['name']];
				$html .= "<span class=\"block\" {$style}>{$viewval}</span>";
				if ($isreturnvalue) {
					// $viewval='';
					// $islockHtml = '';
				}
				$islockHtml = parameReplace ( '$viewval', $viewval, $islockHtml );
				$html .= $islockHtml;
				break;
			case 'select' :
	
				// 是否只读
				$readonly = $controllProperty [$property ['islock'] ['name']];
				// 绑定的事件
				$targevent = $controllProperty [$property ['tagevent'] ['name']];
				// 数据字段名
				$filedName = $controllProperty [$property ['fields'] ['name']];
				// 组件标题
				$filedTitle = $controllProperty [$property ['title'] ['name']];
				// 默认选中项的值
				$defaultcheckitem = $controllProperty [$property ['defaultcheckitem'] ['name']];
				// 外部传入首选项的值
				$defaultval = $controllProperty [$property ['defaultval'] ['name']];
				// 外部传入首选项的显示文本
				$defaulttext = $controllProperty [$property ['defaulttext'] ['name']];
				// Action名称
				$ActionName = $this->nodeName;
				// 直接显示内容的控制阀
				$isshowresoult = $controllProperty [$property ['isshowresoult'] ['name']];
				// 下拉返回唯一标识
				$dropbackkey = $controllProperty [$property ['dropbackkey'] ['name']];
	
				$conditions = '';
				// 数据来源方式：table|selectlist
				if ($controllProperty [$property ['showoption'] ['name']]) {
					$type = 'selectlist';
					$key = $controllProperty [$property ['showoption'] ['name']];
				} else {
					if ($controllProperty [$property ['subimporttableobj'] ['name']] || $controllProperty [$property ['treedtable'] ['name']]) {
						$conditions = $controllProperty [$property ['conditions'] ['name']];
						$type = 'table';
						if ($controllProperty [$property ['subimporttableobj'] ['name']]) {
							// 查询表名
							$tableName = $controllProperty [$property ['subimporttableobj'] ['name']];
							// 值字段名
							$tableVal = $controllProperty [$property ['subimporttablefield2obj'] ['name']];
							// 显示字段名
							$tableText = $controllProperty [$property ['subimporttablefieldobj'] ['name']];
						} elseif ($controllProperty [$property ['treedtable'] ['name']]) {
							// 查询表名
							$tableName = $controllProperty [$property ['treedtable'] ['name']];
							// 值字段名
							$tableVal = $controllProperty [$property ['treevaluefield'] ['name']];
							// 显示字段名
							$tableText = $controllProperty [$property ['treeshowfield'] ['name']];
								
							$parentid = $controllProperty [$property ['treeparentfield'] ['name']];
							// 是否末级操作
							$mulit = $controllProperty [$property ['mulit'] ['name']];
							// 是否多选
							$isnextend = $controllProperty [$property ['isnextend'] ['name']];
							// 树形-下拉高度
							$treeheight = $controllProperty [$property ['treeheight'] ['name']];
							// 树形-下拉宽度
							$treewidth = $controllProperty [$property ['treewidth'] ['name']];
							// 树形-是否对话框模式
							$treedialog = $controllProperty [$property ['isdialog'] ['name']] ? true : false;
						}
					}
				}
				// 组件外样式
				$content_cls = "col_" . $controllProperty [$property ['titlepercent'] ['name']] . "_" . $controllProperty [$property ['contentpercent'] ['name']] . " form_group_lay field_" . $controllProperty [$property ['fields'] ['name']];
				$paramArr = '';
				if ($isedit) {
					$paramArr .= "array(\$vo['{$controllProperty[$property['fields']['name']]}']";
				} else {
					$paramArr .= "array(''";
				}
	
				// 拼接class 属性
				$cls = $chtml . ' ' . $org;
				// 拼接组件配置参数
				$subassembly = '';
				$subassembly .= "array('readonly'=>'{$readonly}','targevent'=>'{$targevent}','actionName'=>'{$ActionName}','names'=>'{$filedName}','defaultcheckitem'=>'{$defaultcheckitem}','defaultval'=>'{$defaultval}','defaulttext'=>'{$defaulttext}','table'=>'{$tableName}', 'id'=>'{$tableVal}','name'=>'{$tableText}','conditions'=>'{$conditions}','parentid'=>'{$parentid}','mulit'=>'{$mulit}','isnextend'=>'{$isnextend}','treeheight'=>'{$treeheight}','treewidth'=>'{$treewidth}','treedialog'=>'{$treedialog}','key'=>'{$key}','isedit'=>'{$isedit}','showtype'=>'{$isshowresoult}','dropbackkey'=>'$dropbackkey')";
				$param = '';
				$param .= "{$paramArr},array('{$cls}',array('type'=>'{$type}',{$subassembly})))";
				if (! $isreturnvalue) {
					$viewval = '';
					$html = "\r\n\t\t\t\t\t{$prefTag}";
					if ($isedit) {
						if ($readonly) {
							$html .= "\r\n\t\t\t\t\t\t\t\t{:W('ShowSelect',{$param})}";
						} else {
							$html .= "\r\n\t\t\t\t\t\t\t\t{:W('ShowSelect',{$param})}";
						}
					} else {
						$html .= "\r\n\t\t\t\t\t\t\t\t{:W('ShowSelect',{$param})}";
					}
						
					if ($controllProperty [$property ['subimporttableobj'] ['name']]) {
						$viewval = "{\$vo['" . $controllProperty [$property ['fields'] ['name']] . "']|getFieldBy='" . "{$controllProperty[$property['subimporttablefield2obj']['name']]}'" . ",'{$controllProperty[$property['subimporttablefieldobj']['name']]}'," . "'{$controllProperty[$property['subimporttableobj']['name']]}'}";
						// 普通表数据
						// $html=$viewval;
					} elseif ($controllProperty [$property ['treedtable'] ['name']]) {
						$viewval = "{\$vo['" . $controllProperty [$property ['fields'] ['name']] . "']|getFieldBy='" . "{$controllProperty[$property['treevaluefield']['name']]}'" . ",'{$controllProperty[$property['treeshowfield']['name']]}'," . "'{$controllProperty[$property['treedtable']['name']]}'}";
						// 树形数据
						// $html=$viewval;
					} else {
						$viewval = "{\$vo['{$controllProperty[$property['fields']['name']]}']|getSelectlistValue='{$controllProperty[$property['showoption']['name']]}'}";
						// selectlist数据
						// $html=$viewval;
					}
				} else {
					if ($controllProperty [$property ['subimporttableobj'] ['name']]) {
						$viewval = "{\$vo['" . $controllProperty [$property ['fields'] ['name']] . "']|getFieldBy='" . "{$controllProperty[$property['subimporttablefield2obj']['name']]}'" . ",'{$controllProperty[$property['subimporttablefieldobj']['name']]}'," . "'{$controllProperty[$property['subimporttableobj']['name']]}'}";
						// 普通表数据
						$html = $viewval;
					} elseif ($controllProperty [$property ['treedtable'] ['name']]) {
						$viewval = "{\$vo['" . $controllProperty [$property ['fields'] ['name']] . "']|getFieldBy='" . "{$controllProperty[$property['treevaluefield']['name']]}'" . ",'{$controllProperty[$property['treeshowfield']['name']]}'," . "'{$controllProperty[$property['treedtable']['name']]}'}";
						// 树形数据
						$html = $viewval;
					} else {
						$viewval = "{\$vo['{$controllProperty[$property['fields']['name']]}']|getSelectlistValue='{$controllProperty[$property['showoption']['name']]}'}";
						// selectlist数据
						$html = $viewval;
					}
					// $viewval='';
					// $islockHtml = '';
				}
	
				$islockHtml = parameReplace ( '$viewval', $viewval, $islockHtml );
				$html .= $islockHtml;
				break;
			case 'checkbox' :
				$checked = "";
				$tagEvent = $controllProperty [$property ['tagevent'] ['name']]; // 获取事件名
				$conditions = '';
				$tagEventSytr = '';
				if ($tagEvent) {
					$tagEventSytr = 'on' . ucwords ( $tagEvent ) . '="' . $this->nodeName . '_' . $controllProperty [$property ['fields'] ['name']] . '_' . $tagEvent . '(this)"';
				}
				$html = "\r\n\t\t\t\t\t{$prefTag}";
				if (! $isreturnvalue) {
					$viewval = '';
					if ($controllProperty [$property ['subimporttableobj'] ['name']]) {
						$conditions = $controllProperty [$property ['conditions'] ['name']];
	
						$html .= "{:getControllbyHtml('table',array('type'=>'checkbox','names'=>'{$controllProperty[$property['fields']['name']]}[]','targevent'=>'{$tagEventSytr}','table'=>'{$controllProperty[$property['subimporttableobj']['name']]}','id'=>'{$controllProperty[$property['subimporttablefield2obj']['name']]}','name'=>'{$controllProperty[$property['subimporttablefieldobj']['name']]}','conditions'=>'{$conditions}','selected'=>\$vo['{$controllProperty[$property['fields']['name']]}']))}";
					} else {
						$html .= "{:getControllbyHtml('selectlist',array('type'=>'checkbox','names'=>'{$controllProperty[$property['fields']['name']]}[]','targevent'=>'{$tagEventSytr}','key'=>'{$controllProperty[$property['showoption']['name']]}','selected'=>\$vo['{$controllProperty[$property['fields']['name']]}']))}";
					}
				} else {
					if ($controllProperty [$property ['subimporttableobj'] ['name']]) {
						$viewval = "{\$vo['" . $controllProperty [$property ['fields'] ['name']] . "']|excelTplidTonameAppend=" . "'{$controllProperty[$property['subimporttablefield2obj']['name']]}'," . "'{$controllProperty[$property['subimporttablefieldobj']['name']]}'" . ",'{$controllProperty[$property['subimporttableobj']['name']]}'}";
						$html = $viewval;
					} else {
						$viewval = "{\$vo['{$controllProperty[$property['fields']['name']]}']|getSelectlistByName='{$controllProperty[$property['showoption']['name']]}'}";
						$html = $viewval;
					}
					$viewval = '';
					$islockHtml = '';
				}
				$islockHtml = parameReplace ( '$viewval', $viewval, $islockHtml );
				$html .= $islockHtml;
				break;
			case 'radio' :
				$checked = "";
				$conditions = '';
				$tagEvent = $controllProperty [$property ['tagevent'] ['name']]; // 获取事件名
				$tagEventSytr = '';
				if ($tagEvent) {
					$tagEventSytr = 'on' . ucwords ( $tagEvent ) . '="' . $this->nodeName . '_' . $controllProperty [$property ['fields'] ['name']] . '_' . $tagEvent . '(this)"';
				}
				if (! $isreturnvalue) {
					$html = "\r\n\t\t\t\t\t{$prefTag}";
					if ($controllProperty [$property ['subimporttableobj'] ['name']]) {
						$conditions = $controllProperty [$property ['conditions'] ['name']];
						$html .= "{:getControllbyHtml('table',array('type'=>'radio','targevent'=>'{$tagEventSytr}','names'=>'{$controllProperty[$property['fields']['name']]}','table'=>'{$controllProperty[$property['subimporttableobj']['name']]}','id'=>'{$controllProperty[$property['subimporttablefield2obj']['name']]}','name'=>'{$controllProperty[$property['subimporttablefieldobj']['name']]}','conditions'=>'{$conditions}','selected'=>\$vo['{$controllProperty[$property['fields']['name']]}']))}";
					} else {
						$html .= "{:getControllbyHtml('selectlist',array('type'=>'radio','targevent'=>'{$tagEventSytr}','names'=>'{$controllProperty[$property['fields']['name']]}','key'=>'{$controllProperty[$property['showoption']['name']]}','selected'=>\$vo['{$controllProperty[$property['fields']['name']]}']))}";
					}
				} else {
					if ($controllProperty [$property ['subimporttableobj'] ['name']]) {
						$viewval = "{\$vo['" . $controllProperty [$property ['fields'] ['name']] . "']|getFieldBy='" . "{$controllProperty[$property['subimporttablefield2obj']['name']]}'" . ",'{$controllProperty[$property['subimporttablefieldobj']['name']]}'," . "'{$controllProperty[$property['subimporttableobj']['name']]}'}";
	
						$html = $viewval;
					} else {
						$viewval = "{\$vo['{$controllProperty[$property['fields']['name']]}']|getSelectlistValue='{$controllProperty[$property['showoption']['name']]}'}";
						$html = $viewval;
					}
					// $viewval='';
					// $islockHtml = '';
				}
				$islockHtml = parameReplace ( '$viewval', $viewval, $islockHtml );
				$html .= $islockHtml;
				break;
			case 'textarea' :
				$html = '';
				$richBoxCls = '';
				if ($controllProperty [$property ['isrichbox'] ['name']]) {
					$richBoxCls = 'ueditor';
				}
	
				if (! $isreturnvalue) {
					$rows = 4;
					$cols = 100;
					if ($controllProperty [$property ['rows'] ['name']])
						$rows = $controllProperty [$property ['rows'] ['name']];
					if ($controllProperty [$property ['cols'] ['name']])
						$cols = $controllProperty [$property ['cols'] ['name']];
					$viewval = $isedit ? "{\$vo['" . $controllProperty [$property ['fields'] ['name']] . "']}" : '';
					$val = "";
					if ($viewval) {
						$textareahtml = "<if condition=\"\$vo['" . $controllProperty [$property ['fields'] ['name']] . "']\">{\$vo['" . $controllProperty [$property ['fields'] ['name']] . "']}<else/>" . $controllProperty [$property ['defaultval'] ['name']] . "</if>";
					} else {
						$textareahtml = "";
					}
					$html = "\r\n\t\t\t\t\t{$prefTag}<textarea  {$readonlyStr} cols=\"{$cols}\" rows=\"{$rows}\" class=\"{$chtml} text_area {$richBoxCls} {$org}\" name=\"" . $controllProperty [$property ['fields'] ['name']] . "\">" . $textareahtml . "</textarea>"; // .$islockHtml;
				} else {
					if ($controllProperty [$property ['isrichbox'] ['name']]) {
						$viewval = "{\$vo['{$controllProperty[$property['fields']['name']]}']|richtext2str}";
					} else {
						$viewval = "{\$vo['{$controllProperty[$property['fields']['name']]}']}";
					}
					$html = $viewval;
					// $islockHtml = '';
					// $viewval = '';
				}
				$islockHtml = parameReplace ( '$viewval', $viewval, $islockHtml );
				if ($controllProperty [$property ['isrichbox'] ['name']]) {
					$islockHtml='';
				}
				$html .= $islockHtml;
	
				break;
			case 'date' :
				$style = "";
				$format = '';
				$format1 = '';
				$formatSouce = '';
				if ($controllProperty [$property ['format'] ['name']]) {
					$formatSouce = $controllProperty [$property ['format'] ['name']];
				} else {
					$formatSouce = $property ['format'] ['default'];
				}
				if ($formatSouce) {
					$temp = '';
					$temp = explode ( '@', $formatSouce );
					$format = "format=\"{dateFmt:'" . $temp [0] . "'}\"";
					if ($temp [1]) {
						$format1 = "='{$temp[1]}'";
					}
				}
				if (! $isreturnvalue) {
					// 直接显示内容的控制阀
					$isshowresoult = $controllProperty [$property ['isshowresoult'] ['name']];
					$dateControllCls = '';
					$dateJsEventTag = ' Wdate js-wdate';
					$a = 'js-inputCheckDate';
					// 只读
					if (! $controllProperty [$property ['islock'] ['name']]) {
						$a = '';
						$dateJsEventTag = '';
						$dateControllCls = $chtml;
					}
					if ($isedit) {
						$viewval = "{\$vo['" . $controllProperty [$property ['fields'] ['name']] . "']|transtime{$format1}}";
	
						$html = "\r\n\t\t\t\t\t{$prefTag}";
	
						if ($isshowresoult) {
							$html .= "\r\n\t\t\t\t\t<span class=\"input_new\">{$viewval}</span>";
						} else {
							$html .= "\r\n\t\t\t\t\t<div  class=\"tml-input-calendar\">\r\n\t\t\t\t\t<input type=\"text\" name=\"" . $controllProperty [$property ['fields'] ['name']] . "\" class=\"{$required} {$org} input_new half_angle_input {$dateJsEventTag} input_left {$dateControllCls}\" {$format}  {$readonlyStr} value=\"{$viewval}\"/>";
							$html .= "\r\n\t\t\t\t\t<a href=\"javascript:;\" class=\"icon_elm icon-calendar {$a} \"></a>";
							$html .= "\r\n\t\t\t\t</div>";
						}
					} else {
						$viewval = '';
						$html = "\r\n\t\t\t\t\t{$prefTag}";
						if ($isshowresoult) {
							$html .= "\r\n\t\t\t\t\t<span class=\"input_new\">{$viewval}</span>";
						} else {
							$html .= "\r\n\t\t\t\t\t<div  class=\"tml-input-calendar\">\r\n\t\t\t\t\t<input type=\"text\" name=\"" . $controllProperty [$property ['fields'] ['name']] . "\" class=\"{$required} {$org} input_new half_angle_input {$dateJsEventTag} input_left {$dateControllCls}\" {$format} {$readonlyStr} value=\"{$viewval}\"/>";
							$html .= "\r\n\t\t\t\t\t<a href=\"javascript:;\" class=\"icon_elm icon-calendar {$a} \"></a>";
						}
						$html .= "\r\n\t\t\t\t</div>";
					}
				} else {
					$viewval = "{\$vo['" . $controllProperty [$property ['fields'] ['name']] . "']|transtime{$format1}}";
					$html = $viewval;
					// $islockHtml = '';
					// $viewval= '';
				}
	
				$islockHtml = parameReplace ( '$viewval', $viewval, $islockHtml );
				$html .= $islockHtml;
				break;
			case 'checkfor' :
				break;
			case 'lookup' :
				$html = '';
				if (! $isreturnvalue) {
					$lookupchoice = $controllProperty [$property ['lookupchoice'] ['name']];
					$lookupObj = D ( 'LookupObj' );
					$lookupDetail = $lookupObj->GetLookupDetail ( $lookupchoice );
					$model = $lookupDetail ['mode'];
						
					$orgchar = $lookupDetail ['filed'] ? $lookupDetail ['filed'] : 'name';
					$orgval = $lookupDetail ['val'] ? $lookupDetail ['val'] : 'id';
						
					if (1 == $lookupDetail ['viewtype'] && $orgval != $orgchar) {
						$configObj = D ( 'MisSystemDataviewMasView' );
						$viewConfig = $configObj->getViewConf ( $lookupDetail ['viewname'] );
						$searchField = $viewConfig [$orgval] ['searchField'];
						$orderModelArr = explode ( '.', $searchField );
						if ($orderModelArr [0] && $searchField) {
							$tempModel = D ( $orderModelArr [0] );
						}
					} else {
						$tempModel = D ( $model );
					}
					// 当lookup配置中的存储值与显示一致时不需要做值转换。nbmxkj@20150608 1610
					$viewval = $isedit ? $orgval == $orgchar ? "{\$vo['" . $controllProperty [$property ['fields'] ['name']] . "']}" : "{\$vo['" . $controllProperty [$property ['fields'] ['name']] . "']|getFieldBy='{$orgval}','{$orgchar}','{$tempModel->getTableName()}'}" : '';
					$html = "\r\n\t\t\t\t\t\t{$prefTag}";
				} else {
					$func = "";
					// 处理子表及主表的lookup值转换 , modify by nbmxkj at 20150127 20
					// *****注意：func 与 funcdata 数组项一定要个数对应。****
					foreach ( $this->datalist as $dkey => $dval ) {
						if ($dval ['name'] == $controllProperty [$property ['fields'] ['name']]) {
							$count = count ( $dval ['func'] [0] );
							if ($dval ['func']) {
								$func .= $dval ['func'] [0] [$count - 1];
								// if($dval['func'][0][0]="getFieldBy"){
								$func .= "=";
								// }
							}
							if ($dval ['funcdata']) {
								$func .= "'" . $dval ['funcdata'] [0] [$count - 1] [1];
								if ($dval ['funcdata'] [0] [$count - 1] [1]) {
									$func .= "','" . $dval ['funcdata'] [0] [$count - 1] [2] . "','" . $dval ['funcdata'] [0] [$count - 1] [3] . "'}";
								} else {
									$func .= "'}";
								}
							}
						}
					}
					if ($func) {
						$viewval = "{\$vo['" . $controllProperty [$property ['fields'] ['name']] . "']|" . $func;
					} else {
						$viewval = "{\$vo['" . $controllProperty [$property ['fields'] ['name']] . "']}";
					}
				}
				$html .= "\r\n\t\t\t\t\t\t\t\t{:W('Lookup',array('1',\$vo,'{$controllProperty[$property['id']['name']]}','{$isedit}','{$isreturnvalue}'))}";
				$islockHtml = parameReplace ( '$viewval', $viewval, $islockHtml );
				$html .= "\r\n\t\t\t\t\t\t\t\t" . $islockHtml;
				break;
			case 'lookupsuper' :
				if (! $isreturnvalue) {
					// 获取lookup key值 动态加载lookup配置
					$lookupchoice = $controllProperty [$property ['lookupchoice'] ['name']];
					$lookupObj = D ( 'LookupObj' );
					$lookupDetail = $lookupObj->GetLookupDetail ( $lookupchoice );
					$model = $lookupDetail ['mode'];
					$urls = $lookupDetail ['url'];
					$lookupgroup = $controllProperty [$property ['lookupgroup'] ['name']];
					$orgchar = $lookupDetail ['filed'] ? $lookupDetail ['filed'] : 'name';
					$orgval = $lookupDetail ['val'] ? $lookupDetail ['val'] : 'id';
					$orgLookuoVal = $controllProperty [$property ['org1'] ['name']]; // 显示绑定
					$orgLookupText = $controllProperty [$property ['org'] ['name']]; // 值绑定
					$title = "{\$fields[\"" . $controllProperty [$property ['fields'] ['name']] . "\"]}";
					$view = '';
					if ($lookupDetail ['viewname']) {
						$view = '&viewname=' . $lookupDetail ['viewname'];
					}
					if ($lookupDetail ['dt']) {
						$dtcon = '&lookuptodatatable=2';
					}
					$conditions = '';
					$checkformodel = $lookupDetail ['checkformodel'];
					// checkfor的org显示字段
					$checkfororgchar = $detailList ['field'] ? $detailList ['field'] : $orgchar;
					// lookup的org值字段
						
					// lookup的值反写
					$textCounterCheck = ''; // 显示文本的反写 text
					$valCounterCheck = ''; // 值的反写 value
					// lporder 从外部lookup中取值字段
					// lpkey 当前lookup的key
					// lpfor 往哪个东西里写值
					// lpself 当前项的字段名称
					if ($orgLookuoVal) {
						unset ( $temp );
						$temp = explode ( '.', $orgLookuoVal );
						$valCounterCheck = "callback=\"lookup_counter_check\" lpkey=\"{$lookupchoice}\" lpfor=\"{$lookupgroup}.{$orgchar}\" lpself=\"{$orgval}\" lporder=\"{$temp[1]}\"";
					}
					if ($orgLookupText) {
						unset ( $temp );
						$temp = explode ( '.', $orgLookupText );
						$textCounterCheck = "callback=\"lookup_counter_check\" lpkey=\"{$lookupchoice}\" lpfor=\"{$lookupgroup}.{$orgval}\" lpself=\"{$orgchar}\" lporder=\"{$temp[1]}\"";
					}
						
					// checkfor的列表显示字段
					$checkfororgshowfield = $detailList ['checkfororgshowfield'] ? $detailList ['checkfororgshowfield'] : '';
					// $urls = $controllProperty[$property['geturls']['name']];
					// $lookupgroup = $controllProperty[$property['lookupgroup']['name']];
					// $filedback = $controllProperty[$property['filedback']['name']];
					// $model = $controllProperty[$property['model']['name']];
					// $checkformodel = $controllProperty[$property['checkformodel']['name']];
					// $orgchar = $controllProperty[$property['lookuporg']['name']]?$controllProperty[$property['lookuporg']['name']]:'name';
					// $orgval = $controllProperty[$property['lookuporgval']['name']]?$controllProperty[$property['lookuporgval']['name']]:'id';
					// checkfor的org显示字段
					// $checkfororgchar = $controllProperty[$property['checkfororg']['name']]?$controllProperty[$property['checkfororg']['name']]:$orgchar;
					// checkfororfields
					// checkfor显示字段
					// $checkfororgshowfield = $controllProperty[$property['checkfororgshowfield']['name']]?$controllProperty[$property['checkfororgshowfield']['name']]:'';
					// $conditions = $controllProperty[$property['conditions']['name']]; // auditState,3;status,1;ischange,0
					// $title = "{\$fields[\"".$controllProperty[$property['fields']['name']]."\"]}";
					$val = '';
					$tempModel = D ( $model );
					$html = "\r\n\t\t\t\t\t\t{$prefTag}<div class=\"tml-input-lookupSuper\">";
					if ($readonly) {
						// checkfor 文本框 $checkfororgchar
						$html .= "\r\n\t\t\t\t\t\t<input type=\"text\" class=\"{$lookupgroup}.{$checkfororgchar} {$chtml} lookupSuper_input input_new half_angle_input\" autocomplete=\"off\" readonly=\"readonly\"  value=\"" . ($isedit ? "{\$vo['" . $controllProperty [$property ['fields'] ['name']] . "']|getFieldBy='{$orgval}','{$orgchar}','{$tempModel->getTableName()}'}" : '') . "\" />";
					} else {
						// $html .="\r\n\t\t\t\t\t\t<input type=\"text\" class=\"{$lookupgroup}.{$checkfororgchar} {$chtml} \" autocomplete=\"off\" value=\"".($isedit?"{\$vo['".$controllProperty[$property['fields']['name']]."']|getFieldBy='{$orgval}','{$orgchar}','{$tempModel->getTableName()}'}":'')."\" />";
	
						if ($checkfororgshowfield) {
							$fileds = "array(" . $checkfororgshowfield . ");";
						} else {
							$fileds = '';
						}
						$html .= "\r\n\t\t\t\t\t\t<input type=\"text\" " . "autocomplete=\"off\" " . "checkfor=\"{$checkformodel}\" " . "insert=\"{$orgval}\" " . "show=\"{$checkfororgchar}\" " . "name=\"{$checkfororgchar}\" " . "newconditions=\"{$conditions}\" " . "fields=\"{$fileds}\" " . "other " .
								// "value=\"".($isedit?"{\$vo['".$controllProperty[$property['fields']['name']]."']|getFieldBy='{$orgval}','{$orgchar}','{$tempModel->getTableName()}'}":'')."\" ".
						"value=\"" . ($isedit ? "{\$vo['" . $controllProperty [$property ['fields'] ['name']] . "']|getFieldBy='id','{$orgchar}','{$tempModel->getTableName()}'}" : '') . "\" " .
						// "callback=\"getPactCheckVal\" ". // 回调处理函数 ， 目前是有错误。
						"class=\"{$lookupgroup}.{$checkfororgchar} checkByInput lookupSuper_input input_new half_angle_input {$chtml}\">";
					}
					// checkfor 文本框的隐藏域
					$html .= "\r\n\t\t\t\t\t\t<input type=\"hidden\" class=\"{$lookupgroup}.{$orgval}\" name=\"" . $controllProperty [$property ['fields'] ['name']] . "\" value=\"{\$vo['" . $controllProperty [$property ['fields'] ['name']] . "']}\" />";
						
					if ($readonly) { // 只读情况
						$viewval = $isedit ? "{\$vo['" . $controllProperty [$property ['fields'] ['name']] . "']|getFieldBy='{$orgval}','{$orgchar}','{$tempModel->getTableName()}'}" : '';
						// lookup 触发按钮
						$html .= "\r\n\t\t\t\t\t\t<a class=\"icon_elm mid_icon_elm icon-plus\"></a>";
						// looup 默认显示框
						$html .= "<input type=\"text\" readonly=\"readonly\" class=\"{$lookupgroup}.{$orgchar} input_new square_input readonly valid\"	check_key=\"othername\" name=\"{$lookupgroup}{$orgchar}\" value=\"" . $viewval . "\">";
						// 清空 lookup 按钮
						$html .= "\r\n\t\t\t\t\t\t<a title=\"清空信息\" class=\"icon_elm icon-trash\"  href=\"javascript:void(0);\"></a>";
					} else {
						$viewval = $isedit ? "{\$vo['" . $controllProperty [$property ['fields'] ['name']] . "']|getFieldBy='{$orgval}','{$orgchar}','{$tempModel->getTableName()}'}" : '';
						// lookup 触发按钮
						$html .= "\r\n\t\t\t\t\t\t<a class=\"icon_elm mid_icon_elm icon-plus\" param=\"lookupchioce={$lookupchioce}&newconditions={$conditions}{$view}{$dtcon}\" href=\"__URL__/{$urls}\" lookupGroup=\"{$lookupgroup}\" ></a>"; // $title
						// looup 默认显示框
						$html .= "<input type=\"text\" readonly=\"readonly\" class=\"{$lookupgroup}.{$orgchar} textInput input_new square_input readonly valid\"	check_key=\"{$orgchar}\" name=\"{$lookupgroup}{$orgchar}\" value=\"" . $viewval . "\">";
						// 清空 lookup 按钮
						// $html .="\r\n\t\t\t\t\t\t<a title=\"清空信息\" class=\"input-addon input-addon-recycle\" href=\"javascript:void(0);\" onclick=\"clearInput('{$checkfororgchar},{$controllProperty[$property['fields']['name']]},{$lookupgroup}{$orgchar}');\"></a>";
						$html .= "\r\n\t\t\t\t\t\t<a title=\"清空信息\" class=\"icon_elm icon-trash\"  href=\"javascript:void(0);\" onclick=\"clearOrg('{$lookupgroup}');\"></a>";
					}
						
					$html .= "\r\n\t\t\t\t\t\t</div>";
				} else {
					// $html="{\$vo['".$controllProperty[$property['fields']['name']]."']}";
					$func = "";
					// 处理子表及主表的lookup值转换 , modify by nbmxkj at 20150127 20
					// *****注意：func 与 funcdata 数组项一定要个数对应。****
					foreach ( $this->datalist as $dkey => $dval ) {
						if ($dval ['name'] == $controllProperty [$property ['fields'] ['name']]) {
							$count = count ( $dval ['func'] [0] );
							if ($dval ['func']) {
								$func .= $dval ['func'] [0] [$count - 1];
								// if($dval['func'][0][0]="getFieldBy"){
								$func .= "=";
								// }
							}
							if ($dval ['funcdata']) {
								$func .= "'" . $dval ['funcdata'] [0] [$count - 1] [1];
								if ($dval ['funcdata'] [0] [$count - 1] [1]) {
									$func .= "','" . $dval ['funcdata'] [0] [$count - 1] [2] . "','" . $dval ['funcdata'] [0] [$count - 1] [3] . "'}";
								} else {
									$func .= "'}";
								}
							}
						}
					}
					if ($func) {
	
						$viewval = "{\$vo['" . $controllProperty [$property ['fields'] ['name']] . "']|" . $func;
						$html = $viewval;
					} else {
						$viewval = "{\$vo['" . $controllProperty [$property ['fields'] ['name']] . "']}";
						$html = $viewval;
					}
					// $viewval='';
					// $islockHtml = '';
				}
				$islockHtml = parameReplace ( '$viewval', $viewval, $islockHtml );
				$html .= $islockHtml;
				break;
			case 'upload' :
				// 数据字段名
				$filedName = $controllProperty [$property ['fields'] ['name']];
				// 组件标题
				$filedTitle = $controllProperty [$property ['title'] ['name']];
				// 上传数量
				$uploadNum = $controllProperty [$property ['uploadnum'] ['name']];
				// 上传类型
				$uploadType = $controllProperty [$property ['uploadtype'] ['name']];
	
				$param = 'array("0"=>$uploadarry["' . $filedName . '"],"1"=>' . $filedName . ',"2"=>$fields[' . $filedName . '],"3"=>"' . $uploadNum . '","4"=>"' . $uploadType . '")';
				$viewval = '';
				if (! $isreturnvalue) {
					if ($isedit) {
						if ($readonly) {
							$html .= "\r\n\t\t\t\t\t\t\t\t{:W('ShowUpload',{$param})}";
						} else {
							$html .= "\r\n\t\t\t\t\t\t\t\t{:W('ShowUpload',{$param})}";
						}
					} else {
						$html .= "\r\n\t\t\t\t\t\t\t\t{:W('ShowUpload',{$param})}";
					}
				} else {
					$html .= "\r\n\t\t\t\t\t\t\t\t{:W('ShowUploadView',{$param})}";
					// $islockHtml = '';
				}
				$islockHtml = parameReplace ( '$viewval', $viewval, $islockHtml );
				$html .= $islockHtml;
				break;
			case 'userselect' :
				$viewval = '';
				if (! $isreturnvalue) {
					if ($isedit) {
						if ($readonly) {
							$html .= "\r\n\t\t\t\t\t\t\t\t{:W('ShowUserSelect',\$attarry)}" . $islockHtml;
						} else {
							$html .= "\r\n\t\t\t\t\t\t\t\t{:W('ShowUserSelect',\$attarry)}" . $islockHtml;
						}
					} else {
						$html .= "\r\n\t\t\t\t\t\t\t\t{:W('ShowUserSelect')}";
					}
				} else {
					// 视图模式
					// $islockHtml = '';
				}
				$islockHtml = parameReplace ( '$viewval', $viewval, $islockHtml );
				$html .= $islockHtml;
				break;
			case 'areainfo' :
				$viewval = '';
				// 数据字段名
				$filedName = $controllProperty [$property ['fields'] ['name']];
				// 组件标题
				$filedTitle = $controllProperty [$property ['title'] ['name']];
				// 组件外样式
				$content_cls = "col_" . $controllProperty [$property ['titlepercent'] ['name']] . "_" . $controllProperty [$property ['contentpercent'] ['name']] . " form_group_lay field_" . $controllProperty [$property ['fields'] ['name']];
				$param = 'array("0"=>$areainfoarry["' . $filedName . '"],"1"=>' . $filedName . ',"2"=>$fields[' . $filedName . '],"3"=>"' . $content_cls . '","5"=>"' . $required . '")';
	
				$viewval = "{\$vo['" . $controllProperty [$property ['fields'] ['name']] . "']}";
				if (! $isreturnvalue) {
					if ($isedit) {
						if ($readonly) {
							$html .= "\r\n\t\t\t\t\t\t\t\t{:W('ShowArea',{$param})}" . $islockHtml;
						} else {
							$html .= "\r\n\t\t\t\t\t\t\t\t{:W('ShowArea',{$param})}" . $islockHtml;
						}
					} else {
						$html .= "\r\n\t\t\t\t\t\t\t\t{:W('ShowArea',{$param})}";
					}
				} else {
					$html = $viewval;
					// $viewval='';
					// $islockHtml = '';
				}
				$islockHtml = parameReplace ( '$viewval', $viewval, $islockHtml );
				$html .= $islockHtml;
				break;
			case 'datatable' :
	
				$viewval = '';
				$titles = <<<EOF
	
				<if condition="\$fields['{$controllProperty[$property['fields']['name']]}']">
				<div class="into_table_title">{\$fields["{$controllProperty[$property['fields']['name']]}"]}</div>
				</if>
EOF;
					/* 获取字段信息 */
				$filedTag = $property ['fieldlist'] ['name']; // 获取配置信息中的标识名称
				// 获取所有节点
				$configPath = $controllProperty;
				$controlName = $controlTag = $controllProperty [$property ['fields'] ['name']]; // 组件唯一标识 datatable8,......
				$tableName = $this->tableName . '_sub_' . $controlTag;
				$namses = "datatable[#index#][{$controlName}]";
				if ($configPath) {
				$innerTableFieldInfo = $configPath [$filedTag]; // 获取到内嵌表字段设置信息
				}
				$innerTableFieldInfoObj = json_decode ( htmlspecialchars_decode ( $innerTableFieldInfo ), true ); // 得到字段信息json对象数据
				usort ( $innerTableFieldInfoObj, function ($a, $b) {
				$al = $a ['fieldsort'];
				$bl = $b ['fieldsort'];
				if ($al == $bl)
					return 0;
					return ($al < $bl) ? - 1 : 1;
				} );
	
				$innerTableFieldInfoObj = json_decode ( json_encode ( $innerTableFieldInfoObj ) );
	
				// 修改及展示 页面组件列表
				$showandeditfieldlist = '';
	
				$filedList = "";
				$filedList .= "<th style='width:40px;' template_key=\"serial\" >";
				$filedList .= "				序号";
				$filedList .= "			</th>";
				$count = 0;
				$nameKey = '';
	
					foreach ( $innerTableFieldInfoObj as $key => $val ) {
					if ($val->fieldshow == 1) {
	
						// 0 允许编辑， 1：不可编辑
						$isDatatableEdit = intval ( $val->isedit ) ? false : true;
							$editStatusConfig = '';
							if (! $isDatatableEdit) {
							$editStatusConfig = 'is_readonly=true';
						}
						$dt_readonly = "";
						$class_readonly = "";
						$int_readonly = 1;
						if ($editStatusConfig) { // 内嵌表控件不可编辑
							$dt_readonly = "readonly='readonly'";
							$class_readonly = "readonly";
							$int_readonly = 0;
							}
							$nameKey = "template_controll={$val->fieldname}";
						switch ($val->fieldshowtype) {
							case 'text':
							//查询公式
							$misDynamicFormDatatableModel=M('mis_dynamic_form_datatable');
								$datatablemap['id']=$val->fieldid;
								$datatableList=$misDynamicFormDatatableModel->where($datatablemap)->find();
								$validdigit=$datatableList['validdigit']?$datatableList['validdigit']:2;
								$fieldname=$datatableList['fieldname'];
								$calculate=$datatableList['calculate'];
								$subassembly=$datatableList['subassembly'];
								$subassemblyArr=explode(',', $subassembly);
											$formulaType = $datatableList['type'];
													$formulaJindu = $datatableList['jindu'];
													//替换id
													$inputString="";
															$FormulArr = array();
															foreach ($subassemblyArr as $sk=>$sval){
															$propertymap['id']=$sval;
															$properyList=$misDynamicFormDatatableModel->where($propertymap)->find();
															$name=$properyList['fieldname'];
															$FormulArr[$sval]=$name;
															}
															// @todo
															$calculateString = preg_replace_callback ( '/(\[(.*?)\])+/', function ($callbackParame) use($FormulArr) {
																	return '[' . $FormulArr [$callbackParame [2]] . ']';
																	},$calculate);
	
																	$config = '';
																	$orgConfigVal = '';
																			$dataConfig = '';
																			if ($val->fieldshowtypeconfig) {
																			$config = unserialize ( (base64_decode ( $val->fieldshowtypeconfig )) );
																			}
																			$curCls = '';
																			if (is_array ( $config )) {
																				// 将组件属性转换为html代码
																						$dataConfig = $config ['parame']; // 获取select的配置项
																							
									if ($config ['required']) {
										$curCls .= ' required ';
																						}
																								if ($config ['checktype']) {
																								$curCls .= $config ['checktype'];
																								}
																								}
	
																								$orgSet = '';
																								if (is_array ( $dataConfig )) {
																								$orgConfigVal = $dataConfig [0];
																										if ($orgConfigVal) {
																										$orgSet = $orgConfigVal;
							}
							}
	
							$untilsCls = '';
	
							$unit = $config ['baseuntils']; // 基础单位
							$untils = $config ['untils']; // 显示单位
	
								$tempData = '';
								$template_data = '';
								$orgshowfield = '';
								logs ( 'org set ::' . $orgSet );
								if ($orgSet) {
																				$orgshowfield = str_replace ( 'org', "org{\$key}", $orgSet );
																						$temp = explode ( '.', $orgSet );
																						if (is_array ( $temp )) {
																						$template_data ['bindlookupname'] = $temp [0];
																						$template_data ['upclass'] = $temp [1];
																						}
																						}
																						if ($untils && $unit) {
																						$template_data ['unitl'] = $config ['untils'];
																						$template_data ['unitlname'] = urlencode ( iconv ( "UTF-8", "UTF-8//IGNORE", $config ['untilschar'] ) );
							}
	
							// 添加统计功能
							// is_stats="true" 统计控制
							//
							$stats_num = 'stats_num=\"2\"'; // 统计时的小数有效位数
							$is_stats = ''; // 统计状态
							$edit_count_str = ''; // 编辑时统计数据
							$edit_count_cls = ''; // 统计的标识样式
							$y_num = "";
									if ($datatableList ['calculate']) {
									$edit_calculate_cls = ' calc '; // 计算样式
										
									$template_data ['formula'] = $calculateString;
									$template_data ['stats_num'] = $validdigit;
									$template_data['formulaType'] = $formulaType;
									$template_data['formulaJindu'] = $formulaJindu;
								}
	
											if ($val->fieldcount == 1) {
											$is_stats = ' is_stats="true" ';
											$edit_count_str = " y_num=\"{\$item[{$val->fieldname}]|unitExchange=###,$unit,$untils,2}\"";
											$edit_count_cls = ' into_table_tj_input ';
											$stats_num = $datatableList['stats_num']?"stats_num=\"{$datatableList['stats_num']}\"":$stats_num;//"stats_num=\"{$config['stats_num']}\""; // 指定有效位数。
											if ($unit && $untils) {
											$y_num = "y_num='{\$item[{$val->fieldname}]|unitExchange=###,$unit,$untils,2}'";
											} else {
											$y_num = "y_num='{\$item[{$val->fieldname}]}'";
											}
								}
	
								if (is_array ( $template_data )) {
									$tempData = "template_data='" . _urldecode ( json_encode ( $template_data ) ) . "'";
								}
								if ($untils && $unit) {
									$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t<td>";
									$showandeditfieldlist .= <<<EOF
			
									<div class="list_group_lay" style="display: none;">
										<div class="list_public_lay">
											<input type="text" $y_num $dt_readonly class="$class_readonly $orgshowfield {$edit_calculate_cls}  {$edit_count_cls} {$curCls} list_unit_input list_public_input" {$edit_count_str} disabled="disabled" name="#hide#datatable[#index#][{$controlTag}][{$val->fieldname}]" value="{\$item[{$val->fieldname}]|unitExchange=###,$unit,$untils,2}">
											<span class="list_icon_elm_unit" title="{$config['untilschar']}">{$config['untilschar']}
											</span>
										</div>
									</div>
									<span class="datatable_show_val">{\$item[{$val->fieldname}]|unitExchange=###,$unit,$untils,3}</span>
EOF;
										$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t</td>";
											} else {
											$showandeditfieldlist .= <<<EOF
											<td>
											<div class="list_group_lay" style="display: none;">
											<input type="text" $dt_readonly $y_num class="list_input $class_readonly {$edit_calculate_cls}  {$untilsCls} {$orgshowfield} {$edit_count_cls} {$curCls}" disabled="disabled" name="#hide#datatable[#index#][{$controlTag}][{$val->fieldname}]" {$untils} value="{\$item[{$val->fieldname}]}" />
												</div>
												<span class="datatable_show_val">{\$item[{$val->fieldname}]}</span>
												</td>
EOF;
												}
	
												// 统计绑定表头文本框字段
												$bindhz = "";
												if ($val->countassignment && $val->fieldcount) {
												$bindhz = "bindhz=\"{$val->countassignment}\"";
							}
							$filedList .= "\r\n<th {$bindhz} style=\"width:{$val->colwidth}px;min-width:{$val->colwidth}px;min-width:{$val->colwidth}px;\" template_key=\"input\" template_name=\"{$namses}[{$val->fieldname}]\" {$tempData} {$stats_num}";
							$filedList .= "\r\n{$is_stats} {$editStatusConfig}	template_class=\"$untilsCls $curCls\" {$nameKey}>"; // unitlpase
							$filedList .= "\r\n{$val->fieldtitle}";
							$filedList .= "\r\n</th>";
							// logs($filedList);
							break;
							case 'select' :
							unset ( $template_data );
								/*
								* template_key="select"
								* template_data='[{"value":1,"name":"第一人"},{"value":2,"name":"第二人"}]'
								* template_class="code"
									*/
									$config = '';
									if ($val->fieldshowtypeconfig) {
									$config = unserialize ( (base64_decode ( $val->fieldshowtypeconfig )) );
								}
								$curCls = '';
									if (is_array ( $config )) {
									// 将组件属性转换为html代码
									$lookupConfig = $config ['datasouceparame']; // 获取select的配置项
											$lookuptolookupconfig = $config ['lookto'];
											$dateConfig = $config ['parame']; // 获取select的配置项
												
											if ($config ['required']) {
											$curCls .= ' required ';
											}
													if ($config ['checktype']) {
														$curCls .= $config ['checktype'];
											}
							}
	
							// 下拉组件org效果
								$orgSet = '';
								if (is_array ( $dateConfig )) {
								$orgConfigVal = $dateConfig ['org'];
								if ($orgConfigVal) {
								$orgSet = $orgConfigVal;
								}
							}
	
							if ($orgSet) {
							$orgshowfield = str_replace ( 'org', "org{\$key}", $orgSet );
							$temp = explode ( '.', $orgSet );
							if (is_array ( $temp )) {
							$template_data ['bindlookupname'] = $temp [0];
								$template_data ['upclass'] = $temp [1];
							}
							}
	
							$dateOrgConfig = '';
									// 对现有DB数据编辑时的 org 属性解析。
									$orgshowfield = '';
								if ($orgSet) {
										$orgshowfield = str_replace ( 'org', "org{\$key}", $orgSet );
								$temp = explode ( '.', $orgSet );
										if (is_array ( $temp )) {
										$template_data ['lookupname'] = $temp [0];
										$template_data ['name'] = $temp [1];
						}
						$dateOrgConfig = 'bindlookup=\'' . json_encode ( $template_data ) . '\'';
						}
	
						$dataSouceArr = '';
						$getControllbyHtmlStr = "";
							if ($lookuptolookupconfig [0]) {
							// 如果有lookup带回
							$l = array ();
							foreach ( $innerTableFieldInfoObj as $k => $v ) {
							if ($k == $lookuptolookupconfig [0]) {
							$l = unserialize ( (base64_decode ( $v->fieldshowtypeconfig )) );
							}
							}
							$lookmodel = D ( "LookupObj" );
								$lookuplist = $lookmodel->GetLookupDetail ( $l ['parame'] [0] );
								// $lookupParame['lpkey'] = reset($lookupConfig);//$l['parame'][0];
								$lookupParame ['lporder'] = $l [$lookuptolookupconfig [1]];
						// $lookupobjmodel = D("lookupObj");
						// $c = reset($lookupConfig);
						// $lookuplist = $lookupobjmodel->GetLookupDetail($c);
						// $lookupParame['lpfor'] = $lookuplist['val'];
						// $lookupParame['lpself'] = $lookuplist['field'];
						// lporder 从外部lookup中取值字段
						// lpkey 当前lookup的key ?
						// lpfor 往哪个东西里写值
						// lpself 当前项的字段名称
							}
								$template_key = "select";
	
								if ($config ['datasouce'] == 0) {
								// 数据源为 select list
								$selectInc = $lookupConfig [0];
									// lookup时
									if ($lookupParame ['lporder']) {
									$selectInc = $lookupParame ['lporder'];
									}
									$getControllbyHtmlStr = '{:getControllbyHtml("selectlist",array("type"=>"select","key"=>"' . $selectInc . '"))}';
										if ($selectInc) {
										$selectlistData = $this->getSelectList ( $selectInc );
											if (is_array ( $selectlistData [$selectInc] )) {
											foreach ( $selectlistData [$selectInc] as $selk => $selv ) {
													$temp ['value'] = urlencode ( iconv ( "UTF-8", "UTF-8//IGNORE", $selk ) );
															$temp ['name'] = urlencode ( iconv ( "UTF-8", "UTF-8//IGNORE", $selv ) );
															$dataSouceArr [] = $temp;
											}
										}
										$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t<td><div style='display: none;' class='list_group_lay'>";
											$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t\t<select class=\"list_select2 $class_readonly {$orgshowfield} $curCls \" data-placeholder=\"没有可用数据\"";
											$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t\t name=\"#hide#datatable[#index#][{$controlTag}][{$val->fieldname}]\">";
													$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t\t{:getControllbyHtml('selectlist',array('type'=>'select','names'=>\"datatable[\$key][{$controlTag}][{$val->fieldname}]\",'key'=>'{$selectInc}','selected'=>\$item['{$val->fieldname}']))}";
													$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t\t</select></div><span class='datatable_show_val'>{:getControllbyHtml('selectlist',array('type'=>'select','names'=>\"datatable[\$key][{$controlTag}][{$val->fieldname}]\",'key'=>'{$selectInc}','selected'=>\$item['{$val->fieldname}'],'showtype'=>1))}</span>";
															$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t</td>";
															}
															} else {
															// 数据源为 table
															if ($lookupConfig [0] && $lookupConfig [1] && $lookupConfig [2]) {
															if ($lookupConfig ['treeparentfield']) {
											$template_key = "selecttree";
															// 设置为树形结构
											$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t<td>";
																	$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t\t<div style='display: none;' class='list_group_lay'>";
																	$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t\t{:W('ShowSelectDT',array(\$item['{$val->fieldname}'], array(' $curCls ',array('type'=>'table',array('readonly'=>'1','targevent'=>'','actionName'=>'{$this->nodeName}','names'=>\"#hide#datatable[#index#][{$controlTag}][{$val->fieldname}]\",'defaultcheckitem'=>'','defaultval'=>'','defaulttext'=>'','table'=>'{$lookupConfig[0]}', 'id'=>'{$lookupConfig[2]}','name'=>'{$lookupConfig[1]}','conditions'=>'','parentid'=>'{$lookupConfig['treeparentfield']}','mulit'=>'{$lookupConfig['mulit']}','isnextend'=>'{$lookupConfig['isnextend']}','treeheight'=>'{$lookupConfig['treeheight']}','treewidth'=>'{$lookupConfig['treewidth']}','key'=>'','isedit'=>'{$int_readonly}','showtype'=>'0','namesappend'=>'{$val->fieldname}#index#')))))}";
																	$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t\t</div><span class='datatable_show_val'>{:getControllbyHtml('table',array('type'=>'select','names'=>\"datatable[\$key][{$controlTag}][{$val->fieldname}]\",'table'=>{$lookupConfig[0]},'id'=>'{$lookupConfig[2]}','name'=>'{$lookupConfig[1]}','selected'=>\$item['{$val->fieldname}'],'showtype'=>1))}</span>";
																	$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t</td>";
																	$chkStyle = "radio";
																	if ($lookupConfig ["mulit"] == 1) {
																	$chkStyle = "checkbox";
															}
											$isnextend = $lookupConfig ['isnextend'] == 1 ? 1 : 0;
														$dataSouceArr = '{"treeconfig":' . '{"expandAll":false,' . '"checkEnable":true,' . '"chkStyle":"' . $chkStyle . '",' . '"radioType":"all",' . '"onClick":"S_NodeClick",' . '"onCheck":"S_NodeCheck"},' . '"treeheight":"' . $lookupConfig ['treeheight'] . '",' . '"treewidth":"' . $lookupConfig ['treewidth'] . '",' . '"treedata":<?php ' . 'echo getControllbyHtml(' . '"table",' . 'array("type"=>"select",' . '"table"=>"' . $lookupConfig [0] . '",' . '"id"=>"' . $lookupConfig [2] . '",' . '"name"=>"' . $lookupConfig [1] . '",' . '"showtype"=>"1",' . '"comboxtree"=>"1",' . '"parentid"=>"' . $lookupConfig ['treeparentfield'] . '",' . '"isnextend"=>' . $isnextend . ')); ' . '?>}';
																	} else {
																	$obj = M ( $lookupConfig [0] );
											$data = $obj->field ( "{$lookupConfig[1]} , $lookupConfig[2]" )->select ();
														$getControllbyHtmlStr = '{:getControllbyHtml("table",array("type"=>"select","table"=>"' . $lookupConfig [0] . '","id"=>"' . $lookupConfig [2] . '","name"=>"' . $lookupConfig [1] . '"))}';
														if ($data) {
														foreach ( $data as $dk => $dv ) {
																$temp ['value'] = urlencode ( iconv ( "UTF-8", "UTF-8//IGNORE", $dv [$lookupConfig [2]] ) );
														$temp ['name'] = urlencode ( iconv ( "UTF-8", "UTF-8//IGNORE", $dv [$lookupConfig [1]] ) );
														$dataSouceArr [] = $temp;
																	}
																	}
																	$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t<td>";
																	$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t\t<div style='display: none;' class='list_group_lay'><select class=\"list_select2 $class_readonly {$orgshowfield} $curCls \" data-placeholder=\"没有可用数据\"";
																	$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t\t name=\"#hide#datatable[#index#][{$controlTag}][{$val->fieldname}]\">";
																	$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t\t{:getControllbyHtml('table',array('type'=>'select','names'=>\"datatable[\$key][{$controlTag}][{$val->fieldname}]\",'table'=>'{$lookupConfig[0]}','id'=>'{$lookupConfig[2]}','name'=>'{$lookupConfig[1]}','selected'=>\$item['{$val->fieldname}']))}";
																	$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t\t</select></div><span class='datatable_show_val'>{:getControllbyHtml('table',array('type'=>'select','names'=>\"datatable[\$key][{$controlTag}][{$val->fieldname}]\",'table'=>'{$lookupConfig[0]}','id'=>'{$lookupConfig[2]}','name'=>'{$lookupConfig[1]}','selected'=>\$item['{$val->fieldname}'],'showtype'=>1))}</span>";
																	$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t</td>";
																	}
																	}
																	}
																	if (empty ( $getControllbyHtmlStr ) && is_array ( $dataSouceArr )) {
																	$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t<td>";
																	$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t\t<div style='display: none;' class='list_group_lay'><select class=\"list_select2 $class_readonly {$orgshowfield} $curCls \" data-placeholder=\"没有可用数据\"";
																	$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t\t name=\"#hide#datatable[#index#][{$controlTag}][{$val->fieldname}]\">";
																	$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t\t{:getControllbyHtml('table',array('type'=>'select','names'=>\"datatable[\$key][{$controlTag}][{$val->fieldname}]\",'table'=>'{$lookupConfig[0]}','id'=>'{$lookupConfig[2]}','name'=>'{$lookupConfig[1]}','selected'=>\$item['{$val->fieldname}']))}";
																	$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t\t</select></div><span class='datatable_show_val'>{:getControllbyHtml('table',array('type'=>'select','names'=>\"datatable[\$key][{$controlTag}][{$val->fieldname}]\",'table'=>'{$lookupConfig[0]}','id'=>'{$lookupConfig[2]}','name'=>'{$lookupConfig[1]}','selected'=>\$item['{$val->fieldname}'],'showtype'=>1))}</span>";
																	$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t</td>";
																	}
																	$template_data = $dataSouceArr;
																	if (is_array ( $dataSouceArr )) {
																	$template_data = urldecode ( json_encode ( $dataSouceArr ) );
							}
							// select绑定表头下拉框字段
							$bindGroup = "";
							if ($config ["selectassignment"]) {
							$bindGroup = "bindGroup=\"{$config['selectassignment']}\"";
							}
							$filedList .= "\r\n			<th {$bindGroup} style=\"width:{$val->colwidth}px;min-width:{$val->colwidth}px;min-width:{$val->colwidth}px;\" template_key='{$template_key}' template_name=\"{$namses}[{$val->fieldname}]\"  {$dateOrgConfig}";
							$filedList .= "\r\n			template_data='{$template_data}' {$editStatusConfig}";
							$filedList .= "\r\n			template_html='{$getControllbyHtmlStr}'";
								$filedList .= "\r\n			template_class=\" $curCls \"  {$nameKey}>";
								$filedList .= "\r\n				{$val->fieldtitle}";
								$filedList .= "\r\n			</th>";
	
									break;
									case 'date' :
									$config = '';
									if ($val->fieldshowtypeconfig) {
											$config = unserialize ( (base64_decode ( $val->fieldshowtypeconfig )) );
								}
													$curCls = '';
													if (is_array ( $config )) {
													// 将组件属性转换为html代码
													$dateConfig = $config ['parame']; // 获取select的配置项
														
													if ($config ['required']) {
															$curCls .= ' required ';
													}
													if ($config ['checktype']) {
													$curCls .= $config ['checktype'];
													}
													}
													$format = $dateConfig ['0'] ? $dateConfig ['0'] : 'yyyy-MM-dd';
													$format1='';
													switch($format){
													case 'yyyy-MM-dd':
													$format1 = "='Y-m-d'";
														break;
														case 'yyyy-MM-dd HH:mm':
														$format1 = "='Y-m-d H:i'";
														break;
														case 'HH:mm':
														$format1 = "='H:i'";
														break;
														default:
														break;
							}
							// 日期组件org效果
	
							$orgSet = '';
							if (is_array ( $dateConfig )) {
									$orgConfigVal = $dateConfig ['org'];
									if ($orgConfigVal) {
									$orgSet = $orgConfigVal;
									}
									}
	
								$dateOrgConfig = '';
									// 对现有DB数据编辑时的 org 属性解析。
									$orgshowfield = '';
									if ($orgSet) {
									unset ( $template_data );
										$orgshowfield = str_replace ( 'org', "org{\$key}", $orgSet );
										$temp = explode ( '.', $orgSet );
										if (is_array ( $temp )) {
										$template_data ['lookupname'] = $temp [0];
										$template_data ['name'] = $temp [1];
										}
										$dateOrgConfig = 'bindlookup=' . json_encode ( $template_data );
										}
										//
	
										$filedList .= "\r\n			<th style=\"width:{$val->colwidth}px;min-width:{$val->colwidth}px;min-width:{$val->colwidth}px;\" template_key=\"date\" template_name=\"{$namses}[{$val->fieldname}]\" {$dateOrgConfig} ";
												$filedList .= "\r\n			template_data='{\"format\":\"{$format}\"}' {$editStatusConfig}";
												$filedList .= "\r\n			template_class=\" $curCls \"  {$nameKey}>";
												$filedList .= "\r\n				{$val->fieldtitle}";
												$filedList .= "\r\n			</th>";
												$showandeditfieldlist .= <<<EOF
	
												<td>
												<div class="list_group_lay" style='display: none;'>
												<input type="text" {$dt_readonly} disabled="disabled" name="#hide#datatable[#index#][{$controlTag}][{$val->fieldname}]" class="Wdate js-wdate list_input {$orgshowfield} {$class_readonly}  $curCls " format="{dateFmt:'{$format}'}" value="{\$item.{$val->fieldname}|transTime{$format1}}">
												</div>
												<span class='datatable_show_val'>{\$item.{$val->fieldname}|transTime{$format1}}</span>
												</td>
EOF;
												break;
												case 'uploadfilenew' :
												$config = '';
												if ($val->fieldshowtypeconfig) {
												$config = unserialize ( (base64_decode ( $val->fieldshowtypeconfig )) );
							}
	
							$filedList .= "\r\n			<th style=\"width:{$val->colwidth}px;min-width:{$val->colwidth}px;min-width:{$val->colwidth}px;\" template_key=\"uploadfilenew\" template_name=\"{$namses}[{$val->fieldname}]\" ";
							$filedList .= "\r\n			template_data='{\"url\":\"__URL__/DT_uploadnew\"}' {$editStatusConfig} ";
							$filedList .= "\r\n			template_class=\"\"  {$nameKey}>";
							$filedList .= "\r\n				{$val->fieldtitle}";
							$filedList .= "\r\n			</th>";
							$uploadFileType = '';
							if ($isreturnvalue || $editStatusConfig) {
							$uploadFileType = 'rel_type="view"';
							}
							$fileattlist = $this->getDTAttachedRecordList ( $tableid, $tablename = '', $subid = 0, $fieldname );
							$tableModelname = createRealModelName ( $tableName );
							$showandeditfieldlist .= <<<EOF
							<td>
							<div class="list_group_lay">
							<div class="js_privyIndex">
							<a
							title="附件管理"
							class="tml_task_btn"
							style="padding:3px 10px;"
							href="javascript:;"
							id="DT_upload_{$val->fieldname}#index#"
							rel_index="#index#"
							rel_url="__URL__/DT_uploadnew"
							rel_subid="{\$item['id']}"
							rel_fieldname = "{$val->fieldname}"
							rel_tablename = "{$tableModelname}"
							rel_tableid = "{\$vo['id']}"
											{$uploadFileType}
											rel_name="datatable[#index#][{$controlTag}][{$val->fieldname}]"
												onclick="DTopenFile(this)">
														附件管理(<span class="attached_count">{\$item.id|getDTCount=\$vo['id'],'{$tableModelname}',###,'{$val->fieldname}'}</span>)
														</a></div></div>
														</td>
EOF;
														break;
														case 'lookup' :
														$lookupParame = '';
														$config = '';
														$parme = '';
														$lookupurl = '';
														$callbackFileds = '';
														$modelName = '';
														$conditionsStr = '';
														$showFiled = '';
														$valField = '';
															$lookupConfig = '';
															$orgval = '';
															$appendCondtionStr = '';
															$appendCondtionArr = array ();
															if ($val->fieldshowtypeconfig) {
																	$config = unserialize ( (base64_decode ( $val->fieldshowtypeconfig )) );
												}
														$curCls = '';
														if (is_array ( $config )) {
														// 将组件属性转换为html代码
														$lookupConfig = $config ['parame']; // 获取lookup等特殊组件的配置项
															$lookuptolookupconfig = $config ['ltol'];
																
															if ($config ['required']) {
															$curCls .= ' required ';
														}
														if ($config ['checktype']) {
										$curCls .= $config ['checktype'];
														}
														}
										$dataTableEventControll = $config ['fieldname'];
										if ($lookupConfig [0]) {
										// 用户配置了lookup前置对象
										// 获取指定项的lookup配置内容
										$lookupConfigData = $this->lookuoconfig ( false, true );
										$lookupConfigData = $lookupConfigData [reset ( $lookupConfig )];
										// fields 带回字段列表
												// url 请求地址
												// mode 处理Model
														// filed 显示字段
														// val 值字段
														// condition 过滤条件
														$lookupurl = '__URL__/' . $lookupConfigData ['url']; // 请求地址
																$callbackFileds = "{$lookupConfigData['fields']}"; // 带回字段列表
																$modelName = "{$lookupConfigData['mode']}"; // 处理Model
																$lookupConfigData ['filed'] = $lookupConfigData ['filed'] ? $lookupConfigData ['filed'] : 'name';
																$showFiled = "{$lookupConfigData['filed']}"; // 显示字段
																$lookupConfigData ['val'] = $lookupConfigData ['val'] ? $lookupConfigData ['val'] : "id";
																$valField = "{$lookupConfigData['val']}"; // 值字段
																		
																	if (1 == $lookupConfigData ['viewtype']) {
																	$configObj = D ( 'MisSystemDataviewMasView' );
															$viewConfig = $configObj->getViewConf ( $lookupConfigData ['viewname'] );
	
															$searchField = $viewConfig [$valField] ['searchField'];
															$orderModelArr = explode ( '.', $searchField );
															if ($orderModelArr [0] && $searchField) {
															$tempModel = D ( $orderModelArr [0] );
														}
														} else {
														$tempModel = D ( $lookupConfigData ['mode'] );
														}
														$modelName = $tempModel->getTableName ();
															// throw new NullDataExcetion(var_dump($modelName));
															$conditionsStr = ''; // $lookupConfigData['condition'] ? preg_replace("/'/", '&#39;', $lookupConfigData['condition']).' and ' : '';
	
															// $parme[]="field={$lookupConfigData['fields']}";
															// $parme[]="model={$lookupConfigData['mode']}";
															// //////////////////////////////////////////////////////////////////////////////////////
															// lookup附加条件处理
															$appendCondtion = '';
															$sysFieldFmt = '';
																	$sysFieldList = '';
																	$formFiledList = '';
																	$fd = '';
																	$formFiledListArr = '';
																	$formFiledFmt = '';
																	$appendCondtionArr = '';
																			$appendCondtionStr = '';
																			// iscontrollchange
																			// {:getAppendCondition($vo,'wenbenkuang,createtime')}
																			$appendCondtion = $config ['dateconditions']; // 附加条件设置信息
																			logs ( $dataTableEventControll . '::::' . arr2string ( $appendCondtion ), 'fffffffffffffffffffff' );
																			if ($appendCondtion) {
																			$appendCondtion = unserialize ( base64_decode ( $appendCondtion ) );
																			// proexp 表单字段列表
										// sysexp 系统字段列表
											$formFiledList = $appendCondtion ['proexp'];
											$sysFieldList = $appendCondtion ['sysexp'];
											// $formFiledFmt=array();
											// $sysFieldFmt = array();
											$sysFieldFmt = unserialize ( $sysFieldList ) or array ();
											// 获取真实的表单字段名。
												if ($formFiledList) {
												$formFiledListArr = unserialize ( $formFiledList ) or array ();
													if (is_array ( $formFiledListArr ) && count ( $formFiledListArr )) {
																							$formFiledKey = array_keys ( $formFiledListArr );
																							$properModel = M ( 'mis_dynamic_form_propery' );
																							$properMap ['id'] = array (
																									'in',
																									$formFiledKey
																							);
																							$fd = $properModel->where ( $properMap )->field ( 'fieldname,id' )->select ();
																							if ($fd) {
																							foreach ( $fd as $k => $v ) {
																							// $fieldArr[] = $v['fieldname'];
																							if ($formFiledListArr [$v ['id']] == - 1) {
																							$formFiledFmt [$v ['fieldname']] = $v ['fieldname'];
																							} else {
																							$formFiledFmt [$v ['fieldname']] = $formFiledListArr [$v ['id']];
																							}
																	}
														}
														}
														}
																if (is_array ( $sysFieldFmt ) && is_array ( $formFiledFmt ))
																$appendCondtionArr = array_merge ( $sysFieldFmt, $formFiledFmt );
																elseif (is_array ( $sysFieldFmt ) && ! is_array ( $formFiledFmt ))
																$appendCondtionArr = $sysFieldFmt;
																elseif (! is_array ( $sysFieldFmt ) && is_array ( $formFiledFmt ))
																	$appendCondtionArr = $formFiledFmt;
																	else
																		$appendCondtionArr = array ();
																$appendCondtionStr = arr2string ( $appendCondtionArr );
																$appendCondtionStr = "<?php echo getAppendCondition(\$vo,{$appendCondtionStr}) ?>";
							}
								
							$parme [] = "lookupchoice=" . reset ( $lookupConfig ) . '&type=dt';
							$parme [] = "newconditions={$conditionsStr}{$appendCondtionStr}";
								
							// getAppendCondition
								
							if ($lookuptolookupconfig [0]) {
							// 如果有lookup带回lookup
							$ll = array ();
									// lporder 从外部lookup中取值字段
									$lookupParame1 ['lporder'] = "ORG" . $lookuptolookupconfig [0];
									// lpkey 当前lookup的key
									$lookupParame1 ['lpkey'] = reset ( $lookupConfig );
	
							// lporder 从外部lookup中取值字段
							// lpkey 当前lookup的key ?
							// lpfor 往哪个东西里写值
							// lpself 当前项的字段名称
							}
							}
	
							$orgval = $lookupConfig [1]; // ? $lookupConfig[1] : 'org_'.$val->fieldname.rand(1, 100);
							$onEditParame = '';
							$paramStr = '';
							if (is_array ( $parme )) {
							$paramStr = join ( '&', $parme );
							$onEditParame = join ( '&', $parme );
							}
	
							// 拼接lookup参数
							$lookupParame ['upclass'] = $showFiled;
									$lookupParame ['callback'] = 'lookupDataToCell';
									$lookupParame ['param'] = $paramStr;
									$lookupParame ['condition'] = array_flip ( $appendCondtionArr ); // $appendCondtionArr;//
									$lookupParame ['lookupname'] = $orgval;
									$lookupParame ['href'] = $lookupurl;
									$lookupParame ['hidden_data'] = array (
									array (
									'upclass' => $valField,
									'name' => "{$namses}[{$val->fieldname}]"
									)
								); // 回写 lookup中指定的值字段名。当前字段名。
									if ($lookuptolookupconfig [0]) {
									$lookupParame ['lporder'] = $lookupParame1 ['lporder'];
									$lookupParame ['lpkey'] = $lookupParame1 ['lpkey'];
									$lookupParame ['hidden_data'] = array (
									array (
									'upclass' => $valField,
											'name' => "{$namses}[{$val->fieldname}]",
									"lporder" => $lookupParame1 ['lporder']
											)
							);
							}
							$filedList .= "\r\n			<th style=\"width:{$val->colwidth}px;min-width:{$val->colwidth}px;min-width:{$val->colwidth}px;\" template_key=\"lookup\" \r\n ";
							$filedList .= "\r\n 			template_name=\"\" \r\n ";
								$filedList .= "\r\n		template_data='" . urldecode ( json_encode ( $lookupParame ) ) . "' {$editStatusConfig} \r\n";
								$filedList .= "\r\n			template_class=\" $curCls \"  {$nameKey}>";
								$filedList .= "\r\n				{$val->fieldtitle}";
								$filedList .= "\r\n			</th>";
	
								// org 没设定哦。
								$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t<td>";
								$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t\t<div class='list_group_lay' style='display: none;'><div class=\"list_public_lay\">";
								$lookupvaluechange = '';
								if ($valField && $showFiled && $modelName) {
								$lookupvaluechange = "{\$item.{$val->fieldname}|getFieldBy='{$valField}','{$showFiled}','{$modelName}'}";
								} else {
								$lookupvaluechange = "{\$item.{$val->fieldname}}";
								}
								if ($editStatusConfig) {
									$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t\t<input type=\"text\" callback=\"\" class=\"list_public_input list_lookup_input readonly org{\$key}{$val->fieldname}.{$showFiled} \" readonly=\"readonly\" value=\"{$lookupvaluechange}\">";
									$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t\t<input type=\"hidden\" class=\"org{\$key}{$val->fieldname}.{$valField} $curCls\" disabled=\"disabled\" name=\"#hide#datatable[#index#][{$controlTag}][{$val->fieldname}]\" value=\"{\$item.{$val->fieldname}}\">";
										$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t\t<a class=\"list_icon_elm list_mid_icon_elm icon-plus\"></a>";
										$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t\t<a title=\"清空信息\" class=\"list_icon_elm icon-trash\" href=\"javascript:void(0);\"></a>";
										$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t\t</div></div>";
										} else {
										$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t\t<input type=\"text\" callback=\"\" class=\"list_public_input list_lookup_input readonly org{\$key}{$val->fieldname}.{$showFiled} \" readonly=\"readonly\" value=\"{$lookupvaluechange}\">";
										$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t\t<input type=\"hidden\" class=\"org{\$key}{$val->fieldname}.{$valField} $curCls\" disabled=\"disabled\" name=\"#hide#datatable[#index#][{$controlTag}][{$val->fieldname}]\" value=\"{\$item.{$val->fieldname}}\">";
										$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t\t<a class=\"list_icon_elm list_mid_icon_elm icon-plus\" param=\"{$onEditParame}\" href=\"{$lookupurl}\" lookupgroup=\"org{\$key}{$val->fieldname}\" autocomplete=\"off\"></a>";
										$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t\t<a title=\"清空信息\" class=\"list_icon_elm icon-trash\" href=\"javascript:void(0);\" onclick=\"clearOrg('org{\$key}{$val->fieldname}');\"></a>";
										$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t\t</div></div>";
						}
	
						$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t\t<span class='datatable_show_val'>{$lookupvaluechange}</span>";
								$showandeditfieldlist .= "\r\n\t\t\t\t\t\t\t\t\t\t</td>";
								break;
						}
						$count ++;
					}
				}
				// logs($count);
				// table 组件的操作类型设置@20141211 nbmxkj
				$tableoprate = '';
				$container = '';
				$containerend = '';
				$oprateItem = '';
	
				$dataTableModelName = createRealModelName ( $tableName );
				$tableOprateTitle = <<<EOF
				<th style='width:90px;' template_data='{"table":"{$controlName}","post_table":"{$tableName}","post_url":"__URL__/onesave","del_url":"__URL__/delsubinfo/delmodel/{$dataTableModelName}"}' template_key="action">操作</th>
EOF;
	
				$tableOprateContent = <<<EOF
	
				<td {$oprateItem}>
						<input type="hidden" name="datatable[#index#][{$controlTag}][id]" value="{\$item.id}" />
						<button title="删除" type="button" class="into_table_new_trash_tr into_table_btn itb_del" del_url="__URL__/delsubinfo/delmodel/{$dataTableModelName}" del_id="{\$item.id}" del_table="{$tableName}"  nbmonclick="del_sub_info('$this->nodeName','{$tableName}',{\$item.id},this)">
						<span class="icon-remove"></span></button>
						<button post_id="{\$item.id}" post_url="__URL__/onesave" post_id="{\$item.id}" post_table="{$tableName}" rel_type="edit" class="save_row_btn" type="button" title="编辑"><span class="icon-pencil"></span></button>
					</td>
EOF;
				if ($isreturnvalue) {
					$tableoprate = 'view';
					$tableOprateContent = '';
					$tableOprateTitle = '';
					// $container='<div class="temp_cell_right">';
					// $containerend='</div>';
					$oprateItem = 'style="display:none"';
				} else {
					if ($isedit) {
						$tableoprate = 'edit';
					} else {
						$tableoprate = 'add';
					}
				}
				$datatableID = 'dt_' . $controlName . rand_string () . '_' . $tableoprate;
				$orgTag = $controllProperty [$property ['org'] ['name']]; // 获取数据表格的数据带回绑定标识
				$dataTableCallBackFunc = 'nbm_datatable_callback';
				$datatableLookupInsertDataTag = '';
				if ($orgTag) {
			
					// 取得组件记录的ID值。
					$propertyid = $controllProperty [$property ['id'] ['name']];
					// 从这个表中得到对应关系 。 `mis_dynamic_form_indatatable`
					// datafieldname：当前这个数据表格的字段名。
					// datainfieldname:这个是 带回数据表格中取值的字段名。
					// 允许组合方式：{datafieldname：datainfieldname},
					// datafieldname: a, b , c ,d
					// datainfieldname： e , f ,g ,h
					// {a:e},{b:e},{c:e}
			
					// 在需要回绑表时将数据的key带回数据的表名)查询出来。即当前表关联信息中记录的表
			
					$relationData = $controllProperty [$property ['dtlookup'] ['name']];
					$relationData = unserialize ( base64_decode ( $relationData ) );
					$relationDataTableName = key ( $relationData );
						$relation = reset ( $relationData );
						$relation = $relation ['list'];
						$relation = json_encode ( $relation );
							
						// dtkey: 从哪个表中的得数据。
						// dtid:当前这个数据表格的ID值
						// dtrelation:字段对应关系
							
						$datatableLookupInsertDataTag = <<<EOF
							
						<input style="display:none;" type="text" value="datatableinsert" class="{$orgTag}.datatable_data" dtrelation='{$relation}' dtkey="{$relationDataTableName}" dtid="{$datatableID}" callback="{$dataTableCallBackFunc}" />
EOF;
												}
												$temp = <<<EOF
	
												{$container}
												<div class="into_table_lay">
												{$datatableLookupInsertDataTag}
												{$titles}
												<table class="into_table_new nbm_data_table" table_data='{"formModel":"{$this->nodeName}","datatableModel":"{$dataTableModelName}","importUrl":"__APP__/MisImportExcel/misimportexceladd"}' id="{$datatableID}" table_type="{$tableoprate}">
												<thead>
												<tr class="thead_tr">
												{$filedList}
												{$tableOprateTitle}
												</tr>
												</thead>
												<tbody cellpadding="0" cellspacing="1">
EOF;
												/**
												* *************现有数据的修改展示*****************************
												*/
												$temp .= <<<EOF
	
												{~\$key=0;}
												<volist name="innerTabelObj{$controlTag}Data" id="item">
												<tr>
												<td>
												<span class="serial_number">
												{\$key+1}
												</span>
												</td>
												<!-- 现有数据 -->
												{$showandeditfieldlist}
												{$tableOprateContent}
												</tr>
												{~\$key++}
												</volist>
EOF;
												/**
					 * *****************现有数据呈现end*************************
					 */
	
					$temp .= "	</tbody>";
					/*
					 * $temp .="	<tfoot>";
					 * $temp .="		<tr>";
					 * $temp .="			<td colspan=\"".($count+2)."\">";
					 * $temp .="				<span class=\"right\">";
					 * $temp .="					<input class=\"add_col_input\" type=\"text\" />";
					 * $temp .="					<a class=\"add_col_btn\" href=\"#\">";
					 * $temp .="						新增行";
					 * $temp .="					</a>";
					 * $temp .="				</span>";
					 * $temp .="			</td>";
					 * $temp .="		</tr>";
					 * $temp .="	</tfoot>";
					 */
					$temp .= "</table></div>{$containerend}";
	
					$html = $temp;
	
					$islockHtml = parameReplace ( '$viewval', $viewval, $islockHtml );
					$html .= $islockHtml;
					break;
				default :
					$html = '';
			}
			return $html;
		}
}


