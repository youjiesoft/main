<?php
/**
 * @Title: MisDynamicFormCtrollAction
 * @Package package_name
 * @Description: todo(用于生成action)
 * @author quqiang
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2014-10-14 下午07:39:04
 * @version V1.0
 */
class MisDynamicFormCtrollAction extends MisDynamicFormBaseAction{

	/**
	 * @Title: createAction
	 * @Description: todo(生成Action代码)
	 * @param array $fieldData 组件属性列表
	 * @param string $cotrollname 控制器名称
	 * @param boolean $add 是否覆盖现有文件，默认为false:需要覆盖
	 * @param boolean $isaudit 是否带审批流，默认为false:不带审批流
	 * @param boolean $isinitExtendAction 是否初始化扩展Action代码 默认为空，不初始化【为组合表单的初化解绑准备】
	 * @author quqiang
	 * @date 2014-8-21 上午10:03:39
	 * @throws
	 */
	function createAction($fieldData , $cotrollname,$add=false ,$isaudit=false , $isinitExtendAction=false){
		$cotrollname=ucfirst($cotrollname);
		$actionPath =  LIB_PATH."Action/".$cotrollname."Action.class.php";
		$actionExtendPath = LIB_PATH."Action/".$cotrollname."ExtendAction.class.php";
		$isExist = (file_exists($actionPath));
		if( $isExist && $add){
			$this->error($actionPath."文件已存在!");
		}
		$extendphpcode .="<?php\r\n/**";
		$extendphpcode .="\r\n * @Title: {$cotrollname}Action";
		$extendphpcode .="\r\n * @Package package_name";
		$extendphpcode .= "\r\n * @Description: todo(动态表单_扩展类。本类为用户代码注入入口，系统一旦生成将不再重复生成。";
		$extendphpcode .= "\r * \t\t\t\t\t\t但当用户选为组合表单方案后会更新该文件，请做好备份)";
		$extendphpcode .="\r\n * @author ".$_SESSION['loginUserName'];
		$extendphpcode .="\r\n * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d";
		$extendphpcode .="\r\n * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d";
		$extendphpcode .="\r\n * @date ".date('Y-m-d H:i:s');
		$extendphpcode .="\r\n * @version V1.0";
		$extendphpcode .="\r\n*/";
		$extendphpcode .="\r\nclass ";

		$phpcode.="<?php\r\n/**";
		$phpcode.="\r\n * @Title: {$cotrollname}Action";
		$phpcode.="\r\n * @Package package_name";
		$phpcode.="\r\n * @Description: todo(动态表单_自动生成-".$this->nodeTitle.")";
		$phpcode.="\r\n * @author ".$_SESSION['loginUserName'];
		$phpcode.="\r\n * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d";
		$phpcode.="\r\n * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d";
		$phpcode.="\r\n * @date ".date('Y-m-d H:i:s');
		$phpcode.="\r\n * @version V1.0";
		$phpcode.="\r\n*/";
		$phpcode.="\r\nclass ";

		if( $isaudit ){
			$jcc="CommonAuditAction";
		}else{
			$jcc="CommonAction";
		}
		$extendsCC = $cotrollname."ExtendAction";
		$extendphpcode .=$cotrollname."ExtendAction extends ".$jcc." {\r\n";
		$phpcode.= $cotrollname."Action extends ".$extendsCC." {\r\n";
		$phpcode.="\tpublic function _filter(&\$map){";//start filter

		$extendphpcode .="\tpublic function _extend_filter(&\$map){";//start filter
  
		$phpcode.="\r\n";
		// \$map['allnode']=\$this->getActionName();
		$phpcode .= <<<COD
		\$fieldtype=\$_REQUEST['fieldtype'];
		\$relationmodelname=\$_REQUEST['bindaname'];
		//获取表单类型
		\$type=getFieldBy(\$relationmodelname, "bindaname", "typeid", "mis_auto_bind"); 		
		if(\$fieldtype){
			\$map[\$fieldtype]=\$_REQUEST[\$fieldtype];
			\$this->assign("fieldtype",\$fieldtype);
			\$this->assign("fieldtypeval",\$_REQUEST[\$fieldtype]);
		}else{
			//组从表单需加此条件过滤 
			if(\$type==1){
				if(\$relationmodelname){
					\$map['relationmodelname']=\$relationmodelname;	
				}
			}
		}
		if(\$type==1){
			// 为了兼容普通模式下的表单使用
			\$bindid = \$_REQUEST['bindid'];
			if(\$bindid){
				\$map['bindid']=\$bindid;
				\$this->assign("bindid",\$bindid);
			}
		}
		if (\$_SESSION["a"] != 1){
			\$map['status']=array("gt",-1);
		}
		\$this->_extend_filter(\$map);
COD;
		$phpcode .="\r\n\t}";

		$extendphpcode .="\r\n\t}";
		$extendphpcode .="\r\n\t/**";
		$extendphpcode .="\r\n\t * @Title: _extend_before_index";
		$extendphpcode .="\r\n\t * @Description: todo(扩展前置index函数)";
		$extendphpcode .="\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .="\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .="\r\n\t * @throws ";
		$extendphpcode .="\r\n\t*/";
		$extendphpcode .="\r\n\tfunction _extend_before_index() {";
		$extendphpcode .="\r\n\t}";

		$extendphpcode .="\r\n\t/**";
		$extendphpcode .="\r\n\t * @Title: _extend_before_edit";
		$extendphpcode .="\r\n\t * @Description: todo(扩展的前置编辑函数)";
		$extendphpcode .="\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .="\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .="\r\n\t * @throws ";
		$extendphpcode .="\r\n\t*/";
		$extendphpcode .="\r\n\tfunction _extend_before_edit(){";
		$extendphpcode.="\r\n\t}";

		$extendphpcode.="\r\n\t/**";
		$extendphpcode.="\r\n\t * @Title: _extend_before_insert";
		$extendphpcode.="\r\n\t * @Description: todo(扩展的前置添加函数)";
		$extendphpcode.="\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode.="\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode.="\r\n\t * @throws ";
		$extendphpcode.="\r\n\t*/";
		$extendphpcode.="\r\n\tfunction _extend_before_insert(){";
		$extendphpcode.="\r\n\t}";

		$extendphpcode .= "\r\n\t/**";
		$extendphpcode .= "\r\n\t * @Title: _extend_before_update";
		$extendphpcode .= "\r\n\t * @Description: todo(扩展前置修改函数)  ";
		$extendphpcode .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .= "\r\n\t * @throws";
		$extendphpcode .= "\r\n\t*/";
		$extendphpcode .= "\r\n\tfunction _extend_before_update(){";
		$extendphpcode .="\r\n\t}";

		$extendphpcode .="\r\n\t/**";
		$extendphpcode .="\r\n\t * @Title: _extend_after_edit";
		$extendphpcode .="\r\n\t * @Description: todo(扩展后置编辑函数)";
		$extendphpcode .="\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .="\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .="\r\n\t * @throws ";
		$extendphpcode .="\r\n\t*/";
		$extendphpcode .="\r\n\tfunction _extend_after_edit(\$vo){";
// 		$extendphpcode .="\r\n\t\tif(\$_GET['viewtype']=='view'){";  
// 		$extendphpcode .="\r\n\t\t\t\$this->assign( 'vo', \$vo );";
// 		$extendphpcode .="\r\n\t\t\t\$this->display('formview');";
// 		$extendphpcode .="\r\n\t\t\texit;";
// 		$extendphpcode .="\r\n\t\t}";
		$extendphpcode .="\r\n\t}";

		$extendphpcode.="\r\n\t/**";
		$extendphpcode.="\r\n\t * @Title: _extend_after_list";
		$extendphpcode.="\r\n\t * @Description: todo(扩展前置List)";
		$extendphpcode.="\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode.="\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode.="\r\n\t * @throws ";
		$extendphpcode.="\r\n\t*/";
		$extendphpcode.="\r\n\tfunction _extend_after_list(){";
		$extendphpcode .="\r\n\t}"; 

		$extendphpcode .= "\r\n\t/**";
		$extendphpcode .= "\r\n\t * @Title: _extend_after_insert";
		$extendphpcode .= "\r\n\t * @Description: todo(扩展后置insert函数)  ";
		$extendphpcode .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .= "\r\n\t * @throws";
		$extendphpcode .= "\r\n\t*/";
		$extendphpcode .= "\r\n\tfunction _extend_after_insert(\$id){";
		$extendphpcode .="\r\n\t}";

		$extendphpcode .= "\r\n\t/**";
		$extendphpcode .= "\r\n\t * @Title: _extend_before_add";
		$extendphpcode .= "\r\n\t * @Description: todo(扩展前置add函数)  ";
		$extendphpcode .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .= "\r\n\t * @throws";
		$extendphpcode .= "\r\n\t*/"; 
		$extendphpcode .= "\r\n\tfunction _extend_before_add(\$vo){"; 
		$extendphpcode .="\r\n\t\t\$this->getFormIndexLoad(\$vo);";
		$extendphpcode .="\r\n\t}";

		$extendphpcode .= "\r\n\t/**";
		$extendphpcode .= "\r\n\t * @Title: _extend_after_update";
		$extendphpcode .= "\r\n\t * @Description: todo(扩展后置update函数)  ";
		$extendphpcode .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .= "\r\n\t * @throws";
		$extendphpcode .= "\r\n\t*/";
		$extendphpcode .= "\r\n\tfunction _extend_after_update(){";
		$extendphpcode .="\r\n\t}";


		// beforindex
		$before_index='';
		$before_index .="\r\n\t/**";
		$before_index .="\r\n\t * @Title: _before_index";
		$before_index .="\r\n\t * @Description: todo(前置index函数)";
		$before_index .="\r\n\t * @author ".$_SESSION['loginUserName'];
		$before_index .="\r\n\t * @date ".date('Y-m-d H:i:s');
		$before_index .="\r\n\t * @throws ";
		$before_index .="\r\n\t*/";
		$before_index .="\r\n\tfunction _before_index() {";

		// 默认生成 前置函数
		$before_edit ='';
		$before_edit .="\r\n\t/**";
		$before_edit .="\r\n\t * @Title: _before_edit";
		$before_edit .="\r\n\t * @Description: todo(前置编辑函数)";
		$before_edit .="\r\n\t * @author ".$_SESSION['loginUserName'];
		$before_edit .="\r\n\t * @date ".date('Y-m-d H:i:s');
		$before_edit .="\r\n\t * @throws ";
		$before_edit .="\r\n\t*/";
		$before_edit .="\r\n\tfunction _before_edit(){";

		$before_insert='';
		$before_insert.="\r\n\t/**";
		$before_insert.="\r\n\t * @Title: _before_insert";
		$before_insert.="\r\n\t * @Description: todo(前置添加函数)";
		$before_insert.="\r\n\t * @author ".$_SESSION['loginUserName'];
		$before_insert.="\r\n\t * @date ".date('Y-m-d H:i:s');
		$before_insert.="\r\n\t * @throws ";
		$before_insert.="\r\n\t*/";
		$before_insert.="\r\n\tfunction _before_insert(){";
		$allNodeconfig = $fieldData['all'];
		if(in_array("orderno", array_keys($allNodeconfig)) || $this->isaudit ){
			//判断是否带审批流，必须验证编码是否重复
			$before_insert.="\r\n\t\t\$this->checkifexistcodeororder('{$this->tableName}','orderno',\$this->getActionName());";
		}
		$before_update = '';
		$before_update .= "\r\n\t/**";
		$before_update .= "\r\n\t * @Title: _before_update";
		$before_update .= "\r\n\t * @Description: todo(前置修改函数)  ";
		$before_update .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$before_update .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$before_update .= "\r\n\t * @throws";
		$before_update .= "\r\n\t*/";
		$before_update .= "\r\n\tfunction _before_update(){";
		if(in_array("orderno", array_keys($allNodeconfig)) || $this->isaudit ){
			//判断是否带审批流，必须验证编码是否重复
			$before_update.="\r\n\t\t\$this->checkifexistcodeororder('{$this->tableName}','orderno',\$this->getActionName(),1);";
		}

		// 重写父类编辑函数--为多表操作所做的修改 屈强@20141008 15:53
		$edit ='';
		$edit .="\r\n\t/**";
		$edit .="\r\n\t * @Title: edit";
		$edit .="\r\n\t * @Description: todo(重写父类编辑函数)";
		$edit .="\r\n\t * @author ".$_SESSION['loginUserName'];
		$edit .="\r\n\t * @date ".date('Y-m-d H:i:s');
		$edit .="\r\n\t * @throws ";
		$edit .="\r\n\t*/";
		$edit .="\r\n\tfunction edit(\$isdisplay=1){";

		$edit .="\r\n\t\t\$mainTab = '{$this->tableName}';";
		$edit .="\r\n\t\t//获取当前控制器名称";
		$edit .="\r\n\t\t\$name=\$this->getActionName();";
		$edit .="\r\n\t\t\$model = D(\"{$this->nodeName}View\");";
		$edit .="\r\n\t\t//获取当前主键";
		$edit .="\r\n\t\t\$map[\$mainTab.'.id']=\$_REQUEST['id'];";
		$edit .="\r\n\t\t\$vo = \$model->where(\$map)->find();";
		$edit .="\r\n\t\tif(!\$vo){";
		$edit .="\r\n\t\t\$this->getFormIndexLoad(\$vo);";
		$edit .="\r\n\t\t}";
		$edit .="\r\n\t\tif(method_exists ( \$this, '_filter' )) {";
		$edit .="\r\n\t\t\t\$this->_filter ( \$map );";
		$edit .="\r\n\t\t}";
		$edit .="\r\n\t\t//读取动态配制";
		$edit .="\r\n\t\t\$this->getSystemConfigDetail(\$name);";
		$edit .="\r\n\t\t//扩展工具栏操作";
		$edit .="\r\n\t\t\$scdmodel = D('SystemConfigDetail');";
		$edit .="\r\n\t\t// 上一条数据ID";
		$edit .="\r\n\t\t\$map['id'] = array(\"lt\",\$id);";
		$edit .="\r\n\t\t\$updataid = \$model->where(\$map)->order('id desc')->getField('id');";
		$edit .="\r\n\t\t\$this->assign(\"updataid\",\$updataid);";
		$edit .="\r\n\t\t// 下一条数据ID";
		$edit .="\r\n\t\t\$map['id'] = array(\"gt\",\$id);";
		$edit .="\r\n\t\t\$downdataid = \$model->where(\$map)->getField('id');";
		$edit .="\r\n\t\t\$this->assign(\"downdataid\",\$downdataid);";
		$edit .="\r\n\t\t//lookup带参数查询";
		$edit .="\r\n\t\t\$module=A(\$name);";
		$edit .="\r\n\t\tif (method_exists(\$module,\"_after_edit\")) {";
		$edit .="\r\n\t\t\tcall_user_func(array(\$module,\"_after_edit\"),\$vo);";
		$edit .="\r\n\t\t}";
		$edit .="\r\n\t\t\$this->assign( 'vo', \$vo );";
		$edit .="\r\n\t\tif(\$isdisplay)";
		$edit .="\r\n\t\t\$this->display ();";
		$edit.="\r\n\t}";
		//////////////////////////////////////////////////////////////////
		/*
		 * 不要重写父类中的edit函数
		 */
		$edit ='';
		/////////////////////////////////////////////////////////////////////
		// 默认生成后置函数
		$after_edit = '';
		$after_list ='';
		$after_insert ='';
		$before_add='';
		$after_update='';

		// 删除子表数据
		$delChildData='';
		$is_create_del_child = false;
		$is_create_opraete_child = false;
		$is_create_modify_child = false;
		$is_create_insert_child = false;
		//控制地址组件修改代码生成
		$isArea = false;
		// 实例化MODE
		$model_code = '';
		$is_include_model = false;

		$after_edit .="\r\n\t/**";
		$after_edit .="\r\n\t * @Title: _after_edit";
		$after_edit .="\r\n\t * @Description: todo(后置编辑函数)";
		$after_edit .="\r\n\t * @author ".$_SESSION['loginUserName'];
		$after_edit .="\r\n\t * @date ".date('Y-m-d H:i:s');
		$after_edit .="\r\n\t * @throws ";
		$after_edit .="\r\n\t*/";
		$after_edit .="\r\n\tfunction _after_edit(\$vo){";

		$after_list.="\r\n\t/**";
		$after_list.="\r\n\t * @Title: _after_list";
		$after_list.="\r\n\t * @Description: todo(前置List)";
		$after_list.="\r\n\t * @author ".$_SESSION['loginUserName'];
		$after_list.="\r\n\t * @date ".date('Y-m-d H:i:s');
		$after_list.="\r\n\t * @throws ";
		$after_list.="\r\n\t*/";
		$after_list.="\r\n\tfunction _after_list(){";

		$after_insert .= "\r\n\t/**";
		$after_insert .= "\r\n\t * @Title: _after_insert";
		$after_insert .= "\r\n\t * @Description: todo(后置insert函数)  ";
		$after_insert .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$after_insert .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$after_insert .= "\r\n\t * @throws";
		$after_insert .= "\r\n\t*/";
		$after_insert .= "\r\n\tfunction _after_insert(\$id){";

		$before_add .= "\r\n\t/**";
		$before_add .= "\r\n\t * @Title: _before_add";
		$before_add .= "\r\n\t * @Description: todo(前置add函数)  ";
		$before_add .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$before_add .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$before_add .= "\r\n\t * @throws";
		$before_add .= "\r\n\t*/";
		$before_add .= "\r\n\tfunction _before_add(){";


		$after_update .= "\r\n\t/**";
		$after_update .= "\r\n\t * @Title: _after_update";
		$after_update .= "\r\n\t * @Description: todo(后置update函数)  ";
		$after_update .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$after_update .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$after_update .= "\r\n\t * @throws";
		$after_update .= "\r\n\t*/";
		$after_update .= "\r\n\tfunction _after_update(){";

		/**
		 * 步骤：
		 * 1。先源数据 $filedData['visibility'] 中的项按表名['tablename']分组。
		 * 2。区分出主从表，主表的文件名用当前action名命名，从表以直接表名命名。
		 * 3。遍历分组后的数据，依次生成Model文件。
		 *
		 *		表数组，基础为二维.
		 * 	0=>主表信息
		 * 	1=>所有从表的信息
		 */
		/*	处理子表错误，子表不做数据添加 会导致修改 视图查询数据若用户只勾取部分表字段 引起的查询不出数据。
		 * 处理方式为：取出当前action的所有子表 都生成 添加与修改都 做处理
		 **/
		$curTabData = $this->getDataBaseConf();
		$curTabData = $curTabData['cur'];
		foreach ($curTabData['datebase'] as $key => $val){
			if(!$val['isprimay']){
				$after_update.="\r\n\t\t//添加子表数据处理--".$_SESSION['loginUserName'].'@'.date('Y-m-d H:i:s');
				$after_update.="\r\n\t\t\${$key}Mode = D('".createRealModelName($key)."');";
				$after_update.="\r\n\t\t\${$key}Data = \${$key}Mode->create();";
				$after_update.="\r\n\t\tunset(\${$key}Data['id']);";
				$after_update.="\r\n\t\tif(\${$key}Data){";
				$after_update.="\r\n\t\t\tif(\${$key}Mode->where('masid='.\${$key}Data['masid'])->find())";
				$after_update.="\r\n\t\t\t\t\${$key}Mode->where('masid='.\${$key}Data['masid'])->save(\${$key}Data);";
				$after_update.="\r\n\t\t\telse";
				$after_update.="\r\n\t\t\t\t\${$key}Mode->add(\${$key}Data);";
				$after_update.="\r\n\t\t}";

				$after_insert .="\r\n\t\t// 添加子表数据处理----".$_SESSION['loginUserName'].'@'.date('Y-m-d H:i:s');
				$after_insert .="\r\n\t\t\${$key}Mode = D('".createRealModelName($key)."');";
				$after_insert .="\r\n\t\t\${$key}Data = \${$key}Mode->create();";
				$after_insert .="\r\n\t\tunset(\${$key}Data['id']);";
				$after_insert .="\r\n\t\tif(\${$key}Data){";
				$after_insert .="\r\n\t\t\t\t\${$key}Data['masid']=\$id;";
				$after_insert .="\r\n\t\t\t\t\${$key}Mode->add(\${$key}Data);";
				$after_insert .="\r\n\t\t}";
			}
		}
		// 审批流
		if($isaudit){
		}
		foreach ($fieldData['visibility'] as $k=>$v){
			$property = $this->getProperty($v['catalog']);
			
			// 时间组件的自动获取当前系统时间功能
			if($v[$property['catalog']['name']] == 'date'){
				// 
				if($v[$property['defaulttimechar']['name']] && stringToTime($v[$property['defaulttimechar']['name']])){
					$before_add .= "\r\n\t\t\$vo['{$v[$property['fields']['name']]}']=stringToTime('{$v[$property['defaulttimechar']['name']]}');";
				}else{
					if($v[$property['acquiretime']['name']]){
						$before_add .= "\r\n\t\t\$vo['{$v[$property['fields']['name']]}']=time();";
					}
					if($v[$property['editacquiretime']['name']]){
						$after_edit .= "\r\n\t\t\$vo['{$v[$property['fields']['name']]}']=time();";
					}
				}
			}
			if(($v[$property['catalog']['name']]=='lookup'||$v[$property['catalog']['name']]=='text')&&$v[$property['defaultval']['name']]){
				//Lookup默认值
				$defaultval=str_replace ( array ( '&quot;', '&#39;', '&lt;','&gt;'), array ('"',"'",'<','>'), $v[$property['defaultval']['name']] );
				if($defaultval=="user_txsj"){
					$before_add .= "\r\n\t\t\$txsj=M('User')->where('id='.\$_SESSION[C('USER_AUTH_KEY')])->getField('txsj');";
					$before_add .= "\r\n\t\t\$vo['{$v[$property['fields']['name']]}']=\$txsj;";
				}else{
					$before_add .= "\r\n\t\t\$vo['{$v[$property['fields']['name']]}']=\$_SESSION[{$defaultval}];";
						
				}
				
			}
			// 文本框组件的默认显示数据功能 	by nbmxkj@20150408 1642
			if($v[$property['catalog']['name']] == 'text'){
				// 对现有字段  	subimporttableobj(数据来源表)  	subimporttablefieldobj(显示字段)		subimporttablefield2obj(值字段)
				// 做功用重定义		数据来源表									显示字段											是否在修改页面时也启用功能
				
				$conditions = $v[$property['conditions']['name']];
				$table = $v[$property['subimporttableobj']['name']];
				$showField = $v[$property['subimporttablefieldobj']['name']];
				$isEditShow = $v[$property['subimporttablefield2obj']['name']];
// 				throw new NullCreateOprateException($isEditShow);
				// 附加条件
				$textAppendCondtionConfigStr = $v[$property['additionalconditions']['name']];
				
				if( $table && $showField  ){
					$conditionsOperated ="'";
					if($conditions){
						$conditionsOperated .=$conditions;
					}
					$textAppendCondtionConfigArr = appendConditionConfigResolve($textAppendCondtionConfigStr);
					if($conditions && $textAppendCondtionConfigStr){
						$configResilvedArr = getAppendCondition($vo, $textAppendCondtionConfigArr);
						$conditionsOperated .=" and '.parameReplace(\"&#39;\",\"'\",getAppendConditionExceptEmpty(\$vo,".arr2string($textAppendCondtionConfigArr)."))";
					}else if(!$conditions && $textAppendCondtionConfigStr){
						$conditionsOperated .="'.parameReplace(\"&#39;\",\"'\",getAppendConditionExceptEmpty(\$vo,".arr2string($textAppendCondtionConfigArr)."))";
					}else{
						$conditionsOperated .="'";
					}
					$before_add .= "\r\n\t\t\$vo['{$v[$property['fields']['name']]}']=getFieldData('{$showField}','{$table}' , {$conditionsOperated});";
					// 修改页面自动生成控制
					if( $isEditShow ){
						$after_edit .= "\r\n\t\t\$vo['{$v[$property['fields']['name']]}']=getFieldData('{$showField}','{$table}' ,{$conditionsOperated});";
					}
				}
			}
			
			if($v[$property['catalog']['name']] == 'upload'){
				//存在上传组件
				$after_edit		.= "\r\n\t\t\$this->getAttachedRecordList(\$vo['id']);";
			}
			if($v[$property['catalog']['name']] == 'datatable'){  //存在datatable类型组件
				// 删除子表数据
				if(!$is_create_del_child){
					$delChildData.=$this->getDataTableAction();
					$is_create_del_child = true;
				}
				$after_edit .= $this->dataTableEditCode($v , $property);

				if(!$is_create_modify_child){
					$after_update .="\r\n\t\t// 内嵌表数据添加处理";
					$after_update .=$this->updateDatatableCode();
					$is_create_modify_child = true;
				}

				if(!$is_create_insert_child){
					$after_insert .=$this->insertDatatableCode();
					$is_create_insert_child = true;
				}
			}
			if($v[$property['catalog']['name']] == 'areainfo' && $isArea==false){
				//存在areainfo类型组件
				$after_edit .=$this->getAreaInfoCode();
				$isArea=true;
			}
		}

		/**************************************************************************************************************************************/
		/*		左侧树代码的处理 	20141114 1957																							  */
		/**************************************************************************************************************************************/
		$misdynamicform = M('mis_dynamic_form_manage');
		$formData = $misdynamicform->where("`actionname`='{$this->nodeName}'")->find();
		$temp = explode('#', $formData['tpl']);
		if($temp[0]=='noaudittpl' && $temp[1]=='ltrl'){
			$before_index .="\r\n\t\t//查询绑定数据源";
			$before_index .="\r\n\t\t\$this->getDateSoure();";
		}
		/**************************************************************************************************************************************/
		/*		左侧树代码的处理  end													*/
		/**************************************************************************************************************************************/

		$before_index .="\r\n\t\t\$this->_extend_before_index();";
		$before_index .="\r\n\t}";

		$before_edit.="\r\n\t\tif(\$_REQUEST['main'])";
		$before_edit.="\r\n\t\t\t\$this->assign(\"main\",\$_REQUEST['main']);";
		
		$before_edit.="\r\n\t\t\$this->_extend_before_edit();";
		$before_edit.="\r\n\t}";
		$before_insert.="\r\n\t\t\$this->_extend_before_insert();";
		$before_insert.="\r\n\t}";
		$before_update.="\r\n\t\t\$this->_extend_before_update();";
		$before_update.="\r\n\t}";

		$after_edit.="\r\n\t\t\$this->_extend_after_edit(\$vo);";
		$after_edit .="\r\n\t}";
		$after_list.="\r\n\t\t\$this->_extend_after_list();";
		$after_list .="\r\n\t}";
		$after_insert.="\r\n\t\t\$this->_extend_after_insert(\$id);";
		$after_insert .="\r\n\t}";
		
		$before_add.="\r\n\t\tif(\$_REQUEST['main'])";
		$before_add.="\r\n\t\t\t\$this->assign(\"main\",\$_REQUEST['main']);";
		$before_add.="\r\n\t\t\$this->_extend_before_add(\$vo);";
		$before_add .="\r\n\t}";
		
		$after_update.="\r\n\t\t\$this->_extend_after_update();";
		$after_update .="\r\n\t}";



		//$phpcode.=$before_edit.$before_insert.$before_update.$after_edit.$after_list.$after_insert.$after_add.$after_update;
		$phpcode.=$edit.$before_index.$before_edit.$before_insert.$before_update.$after_edit.$after_insert.$before_add.$after_update.$delChildData;
		$phpcode.="\r\n}\r\n?>";
		$extendphpcode.="\r\n}\r\n?>";
		if(!is_dir(dirname($actionPath))) mk_dir(dirname($actionPath),0777);
		if( false === file_put_contents( $actionPath , $phpcode )){
			$this->error ("Action文件生成失败!");
		}
		
		if($isinitExtendAction){
			if( false === file_put_contents( $actionExtendPath , $extendphpcode )){
				$this->error ("扩展Action文件生成失败!");
			}
		}else{
			
			
			if(false == (file_exists($actionExtendPath))){
				if( false === file_put_contents( $actionExtendPath , $extendphpcode )){
					$this->error ("扩展Action文件生成失败!");
				}
			}else{
				// 扩展文件已存在时的内部代码指定位置替换修改。
				// by nbmxkj@20150605 1711
				// 获取现有代码内容
				$actionExtendContent = file_get_contents($actionExtendPath);
				//var_dump($actionExtendContent);
				// 正则匹配关键字
				
				$baseExtendClass = 'CommonAction';
				if($this->isaudit){
					$baseExtendClass = 'CommonAuditAction';
				}
				// 替换基类
				$clearUpContent = preg_replace_callback('/(extends)(.*?)(\{)+/s' , function($vo) 	use($baseExtendClass){
						return $vo[1].' '.$baseExtendClass.' '.$vo[3];
					} , $actionExtendContent);
				//var_dump($clearUpContent);
				
				/* 
				替换其中的特殊代码问题
				获取该文件的创建间
				如果创建时间大于 2015-04-28 00:00:01 后的代码文件不进行特殊代码替换生成
				 */
				 // 创建时间
				$createTime = filectime($actionExtendPath);
				// 目标比较时间
				$orderTime = strtotime('2015-04-28 00:00:01');
				
				if( false !== $createTime ){ //&& $createTime < $orderTime ){
					// 为 _extend_before_add(扩展前置添加函数) 增加参数，及内容代码调用处理
					/*
					function _extend_before_add(){
						$this->getFormIndexLoad();
					}
					*/
					$extendBeforeAdd = '&$vo';
					$clearUpContent = preg_replace_callback('/(_extend_before_add\()(.*?)(\))+/s' , function($vo) use($extendBeforeAdd){
						return $vo[1].$extendBeforeAdd.$vo[3];
					} , $clearUpContent);
					$outerFuncParam = '$vo';
					$clearUpContent = preg_replace_callback('/(getFormIndexLoad\()(.*?)(\))+/s' , function($vo) use($outerFuncParam){
						return $vo[1].$outerFuncParam.$vo[3];
					} , $clearUpContent);
				}
				file_put_contents( $actionExtendPath , $clearUpContent );
				
			}
		}
		//return $phpcode;
	}
	/**
	 * @Title: createBinedAction
	 * @Description: todo(生成被绑定的Action扩展代码)
	 * @param string $cotrollname	Action名称
	 * @author quqiang
	 * @date 2014-11-13 下午05:19:48
	 * @throws
	 */
	function createBinedAction( $cotrollname,$type){
		$cotrollname=ucfirst($cotrollname);
		$actionExtendPath = LIB_PATH."Action/".$cotrollname."ExtendAction.class.php";
		$extendphpcode .="<?php\r\n/**";
		$extendphpcode .="\r\n * @Title: {$cotrollname}Action";
		$extendphpcode .="\r\n * @Package package_name";
		$extendphpcode .= "\r\n * @Description: todo(动态表单_扩展类。本类为用户代码注入入口，系统一旦生成将不再重复生成。";
		$extendphpcode .= "\r * \t\t\t\t\t\t但当用户选为组合表单方案后会更新该文件，请做好备份)";
		$extendphpcode .="\r\n * @author ".$_SESSION['loginUserName'];
		$extendphpcode .="\r\n * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d";
		$extendphpcode .="\r\n * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d";
		$extendphpcode .="\r\n * @date ".date('Y-m-d H:i:s');
		$extendphpcode .="\r\n * @version V1.0";
		$extendphpcode.="\r\n*/";
		$extendphpcode.="\r\nclass ";
		$jcc="CommonAction";
		$extendphpcode .=$cotrollname."ExtendAction extends ".$jcc." {\r\n";
		$extendphpcode .="\tpublic function _extend_filter(&\$map){";//start filter

		/**************************************************************************************************************************************/
		/*		组合表单处理代码 2014 11 13 17 09																								  */
		/**************************************************************************************************************************************/
		/*$misdynamicform = M('mis_dynamic_form_manage');
		 $formData = $misdynamicform->where("`actionname`='{$this->nodeName}'")->find();
		 $temp = explode('#', $formData['tpl']);
		 if($temp[0]=='zuhetpl'){
			$isinitExtendAction = true;
			}
			if($temp[0] == 'zuhetpl' && $temp[1]=='list'){
			$extendphpcode .="\r\n\t\t\$map['bindid']=\$_REQUEST['bindid'];";
			}*/
		/**************************************************************************************************************************************/
		/*		组合表单处理代码 end													*/
		/**************************************************************************************************************************************/

		$extendphpcode.="\r\n\t}";


		$extendphpcode .="\r\n\t/**";
		$extendphpcode .="\r\n\t * @Title: _extend_before_index";
		$extendphpcode .="\r\n\t * @Description: todo(扩展前置index函数)";
		$extendphpcode .="\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .="\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .="\r\n\t * @throws ";
		$extendphpcode .="\r\n\t*/";
		$extendphpcode .="\r\n\tfunction _extend_before_index() {";
		$extendphpcode .="\r\n\t}";


		$extendphpcode .="\r\n\t/**";
		$extendphpcode .="\r\n\t * @Title: _extend_before_edit";
		$extendphpcode .="\r\n\t * @Description: todo(扩展的前置编辑函数)";
		$extendphpcode .="\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .="\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .="\r\n\t * @throws ";
		$extendphpcode .="\r\n\t*/";
		$extendphpcode .="\r\n\tfunction _extend_before_edit(){";
		$extendphpcode .="\r\n\t\$name=\$this->getActionName();";
		$extendphpcode .="\r\n\tif(!\$_REQUEST['eid']){";
		if($type=='zuhetpl'){
			//组合表单关联关系为编号
			$extendphpcode .="\r\n\t\t\$id=getFieldBy(\$_REQUEST['bindid'], \"orderno\", \"id\", \$name);";
			$extendphpcode .="\r\n\t\t\$eid=\$_REQUEST['id']?\$_REQUEST['id']:\$id;";
		}else{
			//主从表单为bindid关联
			$extendphpcode .="\r\n\t\t\$eid=\$_REQUEST['bindid']?\$_REQUEST['bindid']:\$_REQUEST['id'];";
		}
		$extendphpcode .="\r\n\t\t\$this->getCileType();";
		$extendphpcode .="\r\n\t\t\$this->assign(\"eid\",\$eid);";
		$extendphpcode .="\r\n\t\t\$this->assign(\"ename\",\$name);";
		$extendphpcode .="\r\n\t\t\$this->display(\"editbasic\");exit;";
		$extendphpcode .="\r\n\t}";
		$extendphpcode .="\r\n\t\$this->getCileType();";
		$extendphpcode.="\r\n\t}";

		$extendphpcode.="\r\n\t/**";
		$extendphpcode.="\r\n\t* 获取当前Action下的关联子action";
		$extendphpcode.="\r\n\t*/";
		$extendphpcode.="\r\n\tprivate function getCileType(){";
		/*$extendphpcode.="\r\n\t\t\$name=\$this->getActionName();";
		 $extendphpcode.="\r\n\t\t\$MisAutoBindModel =M ( \"mis_auto_bind\" );";
		 $extendphpcode.="\r\n\t\t\$bindMap['status']=1;";
		 $extendphpcode.="\r\n\t\t\$bindMap['bindaname']=\$name;";
		 $extendphpcode.="\r\n\t\t\$MisAutoBindList=\$MisAutoBindModel->where(\$bindMap)->getField(\"id,inbindaname\");";
		 $extendphpcode.="\r\n\t\t\$this->assign(\"bindid\",\$_REQUEST['id']);";
		 $extendphpcode.="\r\n\t\t\$this->assign('MisSaleClientSTypeList',\$MisAutoBindList);";
		 */
		$extendphpcode.="\r\n\t\t\$name = \$this ->getActionName();";
		$extendphpcode.="\r\n\t\t\$MisAutoBindModel = M(\"mis_auto_bind\");";
		if($type=='zuhetpl'){
			$extendphpcode.="\r\n\t\t\$_REQUEST['bindid']=\$_REQUEST['bindid']?\$_REQUEST['bindid']:getFieldBy(\$_REQUEST['id'], \"id\", \"orderno\", \$name);";
			$extendphpcode.="\r\n\t\t// 查询符合条件的表单";
			$extendphpcode.="\r\n\t\t\$MisAutoBindVo=\$MisAutoBindModel->where(\"status=1 and bindaname='{\$name}'  and bindresult<>''\")->field(\"bindresult\")->find();";

			//$extendphpcode.="\r\n\t\t\$bindMap['bindval']=getFieldBy(\$_REQUEST['bindid'],\"id\",\$MisAutoBindVo['bindresult'],\$name);";
			$extendphpcode.="\r\n\t\t // 过滤掉可能的错误。";
			$extendphpcode.="\r\n\t\t\$bindCondition = getFieldBy(\$_REQUEST['bindid'],\"orderno\",\$MisAutoBindVo['bindresult'],\$name);";
			$extendphpcode.="\r\n\t\tif(isset(\$bindCondition)){";
			$extendphpcode.="\r\n\t\t\t\$bindMap['_string']=\"bindval={\$bindCondition} or bindval='all'\";";
			$extendphpcode.="\r\n\t\t}";


			$extendphpcode.="\r\n\t\t\$bindMap['status'] = 1;";
			$extendphpcode.="\r\n\t\t\$bindMap['bindaname'] = \$name;";
			$extendphpcode.="\r\n\t\t\$MisAutoBindList = \$MisAutoBindModel->where(\$bindMap)->getField(\"id,inbindaname,bindtype\");";
			//$extendphpcode .="\r\n\t\t\$_REQUEST['bindid']=\$_REQUEST['bindid']?\$_REQUEST['bindid']:\$_REQUEST['id'];";
		}else{
			$extendphpcode.="\r\n\t\t\$_REQUEST['bindid']=\$_REQUEST['bindid']?\$_REQUEST['bindid']:\$_REQUEST['id'];";
			//主从关系组合表
			$extendphpcode.="\r\n\t\t // 查询符合条件的表单";
			$extendphpcode.="\r\n\t\t\$MisAutoBindList=\$MisAutoBindModel->where(\"status=1 and bindaname='{\$name}'  and typeid=1\")->select();";
		}
		$extendphpcode.="\r\n\t\t\$map = array();";
		$extendphpcode.="\r\n\t\t\$map['status'] = 1;";
		$extendphpcode.="\r\n\t\t\$map['bindid'] = \$_REQUEST['bindid'];";
		$extendphpcode.="\r\n\t\tforeach(\$MisAutoBindList as \$key =>\$val) {";
		$extendphpcode.="\r\n\t\t\tif (\$val['bindtype'] == 0) {";
		$extendphpcode.="\r\n\t\t\t\t\$model = D(\$val['inbindaname']);";
		$extendphpcode.="\r\n\t\t\t\t\$bindList = \$model->where(\$map)->find();";
		$extendphpcode.="\r\n\t\t\t\t\$MisAutoBindList[\$key]['id'] = \$bindList['id'];";
		$extendphpcode.="\r\n\t\t\t\tif(!\$MisAutoBindList[\$key]['id']){";
		$extendphpcode.="\r\n\t\t\t\t\t\$date=array();";
		$extendphpcode.="\r\n\t\t\t\t\t\$date['bindid']=\$_REQUEST['bindid'];";
		$extendphpcode.="\r\n\t\t\t\t\t\$reuslt=\$model->add(\$date);";
		$extendphpcode.="\r\n\t\t\t\t\t\$model->commit();";
		$extendphpcode.="\r\n\t\t\t\t\t\$MisAutoBindList[\$key]['id'] =\$reuslt;";
		$extendphpcode.="\r\n\t\t\t\t\t\$reuslt=\"\";";
		$extendphpcode.="\r\n\t\t\t\t}";

		$extendphpcode.="\r\n\t\t\t}";
		$extendphpcode.="\r\n\t\t}";


		$extendphpcode.="\r\n\t\t\$this->assign(\"bindid\", \$_REQUEST['id']);";
		$extendphpcode.="\r\n\t\t\$this->assign('MisSaleClientSTypeList', \$MisAutoBindList);";


		$extendphpcode.="\r\n\t}";


		$extendphpcode.="\r\n\t/**";
		$extendphpcode.="\r\n\t * @Title: _extend_before_insert";
		$extendphpcode.="\r\n\t * @Description: todo(扩展的前置添加函数)";
		$extendphpcode.="\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode.="\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode.="\r\n\t * @throws ";
		$extendphpcode.="\r\n\t*/";
		$extendphpcode.="\r\n\tfunction _extend_before_insert(){";
		$extendphpcode.="\r\n\t}";


		$extendphpcode .= "\r\n\t/**";
		$extendphpcode .= "\r\n\t * @Title: _extend_before_update";
		$extendphpcode .= "\r\n\t * @Description: todo(扩展前置修改函数)  ";
		$extendphpcode .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .= "\r\n\t * @throws";
		$extendphpcode .= "\r\n\t*/";
		$extendphpcode .= "\r\n\tfunction _extend_before_update(){";
		$extendphpcode .="\r\n\t}";


		$extendphpcode .="\r\n\t/**";
		$extendphpcode .="\r\n\t * @Title: _extend_after_edit";
		$extendphpcode .="\r\n\t * @Description: todo(扩展后置编辑函数)";
		$extendphpcode .="\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .="\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .="\r\n\t * @throws ";
		$extendphpcode .="\r\n\t*/";
		$extendphpcode .="\r\n\tfunction _extend_after_edit(\$vo){";
		$extendphpcode .="\r\n\t}";


		$extendphpcode.="\r\n\t/**";
		$extendphpcode.="\r\n\t * @Title: _extend_after_list";
		$extendphpcode.="\r\n\t * @Description: todo(扩展前置List)";
		$extendphpcode.="\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode.="\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode.="\r\n\t * @throws ";
		$extendphpcode.="\r\n\t*/";
		$extendphpcode.="\r\n\tfunction _extend_after_list(){";
		$extendphpcode .="\r\n\t}";


		$extendphpcode .= "\r\n\t/**";
		$extendphpcode .= "\r\n\t * @Title: _extend_after_insert";
		$extendphpcode .= "\r\n\t * @Description: todo(扩展后置insert函数)  ";
		$extendphpcode .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .= "\r\n\t * @throws";
		$extendphpcode .= "\r\n\t*/";
		$extendphpcode .= "\r\n\tfunction _extend_after_insert(\$id){";
		$extendphpcode .= "\r\n\t\t// 默认往关联表中插入空数据";
		$extendphpcode .= "\r\n\t\t// 获取传送的id及主体id";
		$extendphpcode .= "\r\n\t\t\$name=\$this->getActionName();";
		//$extendphpcode .= "\r\n\t\t// 获取到当前action的名称";
		//$extendphpcode .= "\r\n\t\t\$misDynamicFormManageObj = M('mis_dynamic_form_manage');";
		//$extendphpcode .= "\r\n\t\t\$title = \$misDynamicFormManageObj->where(\"`actionname`='{\$name}'\")->field('actiontitle')->find();";
		//$extendphpcode .= "\r\n\t\t\$this->assign('actiontitle' , \$title['actiontitle']);";

		$extendphpcode .= "\r\n\t\t\$MisAutoBindModel =M ( \"mis_auto_bind\" );";
		$extendphpcode .= "\r\n\t\t\$bindMap['status']=1;";
		$extendphpcode .= "\r\n\t\t\$bindMap['bindaname']=\$name;";
		$extendphpcode .= "\r\n\t\t\$bindMap['bindtype']=0;";
		$extendphpcode .= "\r\n\t\t\$MisAutoBindList=\$MisAutoBindModel->where(\$bindMap)->getField(\"id,inbindaname\");";
		$extendphpcode .= "\r\n\t\tif(\$MisAutoBindList){";
		$extendphpcode .= "\r\n\t\t\$date = array ();";
		$extendphpcode .= "\r\n\t\t//获取数据漫游date";
		$extendphpcode .= "\r\n\t\t\$MisSystemDataRoamingModel=D('MisSystemDataRoaming');";
		$extendphpcode .= "\r\n\t\t\$bindid = getFieldBy(\$id, \"id\", \"orderno\", \$name);";
		
		
		$extendphpcode .= "\r\n\t\t\$bindList=array();";
		$extendphpcode .= "\r\n\t\t\tforeach (\$MisAutoBindList as \$key=>\$val){";
		$extendphpcode .= "\r\n\t\t\t\tif(in_array(\$val, array_keys(\$bindList))){";
		$extendphpcode .= "\r\n\t\t\t\t}else{";
		$extendphpcode .= "\r\n\t\t\t\t\$bindList[\$val]=1;";
		$extendphpcode .= "\r\n\t\t\t\t\$model = D ( \$val);";
		$extendphpcode .= "\r\n\t\t\t\t\$data=\$MisSystemDataRoamingModel->dataRoamOrderno(\$name,\$id,\$val);";
		$extendphpcode .= "\r\n\t\t\t\tforeach(\$data as \$k=>\$v){";
		$extendphpcode .= "\r\n\t\t\t\t\$date[key(\$v)] = reset(\$v);";
		$extendphpcode .= "\r\n\t\t\t\t}";
		$extendphpcode .= "\r\n\t\t\t\t\$date['bindid']=\$bindid;";
		$extendphpcode .= "\r\n\t\t\t\t\$result = \$model->add ( \$date );";
		
		
		
		$extendphpcode .= "\r\n\t\t\t\tif (! \$result) {";
		$extendphpcode .= "\r\n\t\t\t\t\t\$this->error ( \"数据插入失败,请联系管理员！\" );";
		$extendphpcode .= "\r\n\t\t\t\t}";
		$extendphpcode .= "\r\n\t\t\t}";
		$extendphpcode .= "\r\n\t\t}";
		$extendphpcode .= "\r\n\t}";
		$extendphpcode .= "\r\n\t\$this->success ( L ( '_SUCCESS_' ), '', array (";
		if($type=='zuhetpl'){
			$extendphpcode .= "\r\n\t\t\t'bindid' => \$_POST['orderno'],";
		}else{
			$extendphpcode .= "\r\n\t\t\t'bindid' => \$id,";
		}
		$extendphpcode .= "\r\n\t));";
		$extendphpcode .="\r\n\t}";



		$extendphpcode .= "\r\n\t/**";
		$extendphpcode .= "\r\n\t * @Title: _extend_before_add";
		$extendphpcode .= "\r\n\t * @Description: todo(扩展前置add函数)  ";
		$extendphpcode .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .= "\r\n\t * @throws";
		$extendphpcode .= "\r\n\t*/";
		$extendphpcode .= "\r\n\tfunction _extend_before_add(\$vo){";
		// 默认没得选项条件的情况

		$extendphpcode .= "\r\n\t\t\$name=\$this->getActionName();";
		$extendphpcode .= "\r\n\t\t\$MisAutoBindModel =M ( \"mis_auto_bind\" );";
		$extendphpcode .= "\r\n\t\t\$bindMap['status']=1;";
		$extendphpcode .= "\r\n\t\t\$bindMap['bindaname']=\$name;";
		$extendphpcode .= "\r\n\t\t// 获取到当前action的名称";
		$extendphpcode .= "\r\n\t\t\$misDynamicFormManageObj = M('mis_dynamic_form_manage');";
		$extendphpcode .= "\r\n\t\t\$title = \$misDynamicFormManageObj->where(\"`actionname`='{\$name}'\")->field('actiontitle')->find();";
		$extendphpcode .= "\r\n\t\t\$this->assign('actiontitle' , \$title['actiontitle']);";

		$extendphpcode .= "\r\n\t\t\$MisAutoBindList=\$MisAutoBindModel->where(\$bindMap)->field(\"id,inbindaname\")->select();";
		$extendphpcode .= "\r\n\t\t\$this->assign('MisAutoBindList',\$MisAutoBindList);"; 
		$extendphpcode .= "\r\n\t\t\$this->getFormIndexLoad(\$vo);";
		$extendphpcode .="\r\n\t}";



		$extendphpcode .= "\r\n\t/**";
		$extendphpcode .= "\r\n\t * @Title: _extend_after_update";
		$extendphpcode .= "\r\n\t * @Description: todo(扩展后置update函数)  ";
		$extendphpcode .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .= "\r\n\t * @throws";
		$extendphpcode .= "\r\n\t*/";
		$extendphpcode .= "\r\n\tfunction _extend_after_update(){";
		$extendphpcode .="\r\n\t}";


		$extendphpcode.="\r\n}\r\n?>";

		if( false === file_put_contents( $actionExtendPath , $extendphpcode )){
			$this->error ("扩展Action文件生成失败!");
		}
		//return $phpcode;
	}


	/**
	 * @Title: createBaseArchives
	 * @Description: todo(生成基础档案Action)
	 * @param boolean $isaudit	是否为审批
	 * @param boolean $isinitExtendAction  是否对扩展action做初始化处理
	 * @author quqiang
	 * @date 2014-11-18 下午07:09:06
	 * @throws
	 */
	function createBaseArchives($cotrollname , $isaudit=false , $isinitExtendAction=false){
		logs('我要生成acationqk::::'.$cotrollname);
		// 1.获取到当前Action的组件配置文件信息
		// 获取所有节点信息
		// $allNodeconfig = $model->getAllControllConfig(); // 物理文件获取方式
		$allNodeconfig = $this->getAllControllConfig();
		$fieldData['visibility'] = $allNodeconfig;
		$cotrollname=ucfirst($cotrollname);
		$actionPath =  LIB_PATH."Action/".$cotrollname."Action.class.php";
		$actionExtendPath = LIB_PATH."Action/".$cotrollname."ExtendAction.class.php";

		$extendphpcode .="<?php\r\n/**";
		$extendphpcode .="\r\n * @Title: {$cotrollname}Action";
		$extendphpcode .="\r\n * @Package package_name";
		$extendphpcode .= "\r\n * @Description: todo(动态表单_扩展类。本类为用户代码注入入口，系统一旦生成将不再重复生成。";
		$extendphpcode .= "\r * \t\t\t\t\t\t但当用户选为组合表单方案后会更新该文件，请做好备份)";
		$extendphpcode .="\r\n * @author ".$_SESSION['loginUserName'];
		$extendphpcode .="\r\n * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d";
		$extendphpcode .="\r\n * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d";
		$extendphpcode .="\r\n * @date ".date('Y-m-d H:i:s');
		$extendphpcode .="\r\n * @version V1.0";
		$extendphpcode .="\r\n*/";
		$extendphpcode .="\r\nclass ";

		$phpcode.="<?php\r\n/**";
		$phpcode.="\r\n * @Title: {$cotrollname}Action";
		$phpcode.="\r\n * @Package package_name";
		$phpcode.="\r\n * @Description: todo(动态表单_自动生成-".$this->nodeTitle.")";
		$phpcode.="\r\n * @author ".$_SESSION['loginUserName'];
		$phpcode.="\r\n * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d";
		$phpcode.="\r\n * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d";
		$phpcode.="\r\n * @date ".date('Y-m-d H:i:s');
		$phpcode.="\r\n * @version V1.0";
		$phpcode.="\r\n*/";
		$phpcode.="\r\nclass ";

		if( $isaudit ){
			$jcc="CommonAuditAction";
		}else{
			$jcc="CommonAction";
		}
		$extendsCC = $cotrollname."ExtendAction";
		$extendphpcode .=$cotrollname."ExtendAction extends ".$jcc." {\r\n";
		$phpcode.= $cotrollname."Action extends ".$extendsCC." {\r\n";
		$phpcode.="\tpublic function _filter(&\$map){";//start filter
		$phpcode.="\r\n\t\tif (\$_SESSION[\"a\"] != 1){\r\n\t\t\t\$map['status']=array(\"gt\",-1);";
		$phpcode.="\r\n\t\t}";
		$phpcode.="\r\n\t\t\$this->_extend_filter(\$map);";
		$phpcode .="\r\n\t}";
		//生成扩展文件的代码
		$extendphpcode .="\r\n\t/**";
		$extendphpcode .="\r\n\t * @Title: _extend_filter";
		$extendphpcode .="\r\n\t * @Description: todo(扩展前置index函数)";
		$extendphpcode .="\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .="\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .="\r\n\t * @throws ";
		$extendphpcode .="\r\n\t*/";
		$extendphpcode .="\r\n\tfunction _extend_filter() {";
		$extendphpcode .="\r\n\t}";

		$extendphpcode .="\r\n\t/**";
		$extendphpcode .="\r\n\t * @Title: _extend_before_index";
		$extendphpcode .="\r\n\t * @Description: todo(扩展前置index函数)";
		$extendphpcode .="\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .="\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .="\r\n\t * @throws ";
		$extendphpcode .="\r\n\t*/";
		$extendphpcode .="\r\n\tfunction _extend_before_index() {";
		$extendphpcode .="\r\n\t}";

		$extendphpcode .="\r\n\t/**";
		$extendphpcode .="\r\n\t * @Title: _extend_before_edit";
		$extendphpcode .="\r\n\t * @Description: todo(扩展的前置编辑函数)";
		$extendphpcode .="\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .="\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .="\r\n\t * @throws ";
		$extendphpcode .="\r\n\t*/";
		$extendphpcode .="\r\n\tfunction _extend_before_edit(){";
		$extendphpcode.="\r\n\t}";

		$extendphpcode.="\r\n\t/**";
		$extendphpcode.="\r\n\t * @Title: _extend_before_insert";
		$extendphpcode.="\r\n\t * @Description: todo(扩展的前置添加函数)";
		$extendphpcode.="\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode.="\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode.="\r\n\t * @throws ";
		$extendphpcode.="\r\n\t*/";
		$extendphpcode.="\r\n\tfunction _extend_before_insert(){";
		$extendphpcode.="\r\n\t}";

		$extendphpcode .= "\r\n\t/**";
		$extendphpcode .= "\r\n\t * @Title: _extend_before_update";
		$extendphpcode .= "\r\n\t * @Description: todo(扩展前置修改函数)  ";
		$extendphpcode .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .= "\r\n\t * @throws";
		$extendphpcode .= "\r\n\t*/";
		$extendphpcode .= "\r\n\tfunction _extend_before_update(){";
		$extendphpcode .="\r\n\t}";

		$extendphpcode .="\r\n\t/**";
		$extendphpcode .="\r\n\t * @Title: _extend_after_edit";
		$extendphpcode .="\r\n\t * @Description: todo(扩展后置编辑函数)";
		$extendphpcode .="\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .="\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .="\r\n\t * @throws ";
		$extendphpcode .="\r\n\t*/";
		$extendphpcode .="\r\n\tfunction _extend_after_edit(\$vo){";
		$extendphpcode .="\r\n\t\tif(\$_GET['viewtype']=='view'){";
		$extendphpcode .="\r\n\t\t\t\$this->display('formview');";
		$extendphpcode .="\r\n\t\t\texit;";
		$extendphpcode .="\r\n\t\t}";
		$extendphpcode .="\r\n\t}";

		$extendphpcode.="\r\n\t/**";
		$extendphpcode.="\r\n\t * @Title: _extend_after_list";
		$extendphpcode.="\r\n\t * @Description: todo(扩展前置List)";
		$extendphpcode.="\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode.="\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode.="\r\n\t * @throws ";
		$extendphpcode.="\r\n\t*/";
		$extendphpcode.="\r\n\tfunction _extend_after_list(){";
		$extendphpcode .="\r\n\t}";

		$extendphpcode .= "\r\n\t/**";
		$extendphpcode .= "\r\n\t * @Title: _extend_after_insert";
		$extendphpcode .= "\r\n\t * @Description: todo(扩展后置insert函数)  ";
		$extendphpcode .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .= "\r\n\t * @throws";
		$extendphpcode .= "\r\n\t*/";
		$extendphpcode .= "\r\n\tfunction _extend_after_insert(\$id){";
		$extendphpcode .="\r\n\t}";

		$extendphpcode .= "\r\n\t/**";
		$extendphpcode .= "\r\n\t * @Title: _extend_before_add";
		$extendphpcode .= "\r\n\t * @Description: todo(扩展前置add函数)  ";
		$extendphpcode .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .= "\r\n\t * @throws";
		$extendphpcode .= "\r\n\t*/";
		$extendphpcode .= "\r\n\tfunction _extend_before_add(\$vo){";  
		$extendphpcode .="\r\n\t\t\$this->getFormIndexLoad(\$vo);";
		$extendphpcode .="\r\n\t}";

		$extendphpcode .= "\r\n\t/**";
		$extendphpcode .= "\r\n\t * @Title: _extend_before_delete";
		$extendphpcode .= "\r\n\t * @Description: todo(扩展前置删除函数)  ";
		$extendphpcode .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .= "\r\n\t * @throws";
		$extendphpcode .= "\r\n\t*/";
		$extendphpcode .= "\r\n\tfunction _extend_before_delete(){";
		$extendphpcode .="\r\n\t}";


		$extendphpcode .= "\r\n\t/**";
		$extendphpcode .= "\r\n\t * @Title: _extend_after_update";
		$extendphpcode .= "\r\n\t * @Description: todo(扩展后置update函数)  ";
		$extendphpcode .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .= "\r\n\t * @throws";
		$extendphpcode .= "\r\n\t*/";
		$extendphpcode .= "\r\n\tfunction _extend_after_update(){";
		$extendphpcode .="\r\n\t}";


		// 重写父类index函数--为多表操作所做的修改 屈强@20141125 1712
		$index="";
		$index .="\r\n\t/**";
		$index .="\r\n\t * @Title: index";
		$index .="\r\n\t * @Description: todo(重写父类index函数)";
		$index .="\r\n\t * @author ".$_SESSION['loginUserName'];
		$index .="\r\n\t * @date ".date('Y-m-d H:i:s');
		$index .="\r\n\t * @throws ";
		$index .="\r\n\t*/";
		$index .="\r\n\tfunction index(){";
		$index.=<<<EOF
		
		\$name=\$this->getActionName();
		//读取配置文件字段列信息
		\$this->getSystemConfigDetail(\$name);
		\$model=D(\$name);
		//查询当前模型数据类型
		\$list=\$model->where('status = 1')->select();
		//封装数据类型结构树
		\$listnew = \$list;
		foreach(\$listnew as \$k=>\$v){
			\$listnew[\$k]['name'] = "(".\$v['orderno'].")".\$v['name'];
		}
		\$param['rel']="{$this->nodeName}view";
		\$param['url']="__URL__/index/jump/1/hy/#id#";
		\$treemiso[]=array(
				'id'=>0,
				'pId'=>0,
				'name'=>'{$this->nodeTitle}',
				'title'=>'{$this->nodeTitle}',
				'open'=>true,
				'isParent'=>true,
		);
		\$treearr = \$this->getTree(\$listnew,\$param,\$treemiso);
		\$this->assign("treearr",\$treearr);
		//获取数据ID
		\$hy = \$_REQUEST['hy'];
		//定义一个存储数据数组
		\$vo = array();
		if(\$hy){
			\$map['id'] = \$hy;
			\$vo=\$model->where(\$map)->find();
		}else{
			//判断是否有cookie默认 (作用于刷新)
			\$cookiecheck = Cookie::get("{$this->nodeName}view");
			Cookie::delete("{$this->nodeName}view");
			if(\$cookiecheck){
				\$hy = \$cookiecheck;
				\$map['id'] = \$cookiecheck;
				\$vo=\$model->where(\$map)->find();
			}else{
				if(\$list){
					//判断是否存在数据信息
					\$vo = \$list[0];
					\$hy = \$list[0]['id'];
				}
			}			
		}
		//数据删除的默认选中ID (关联到toolbar配置文件)
		\$_REQUEST['defaultid'] = \$hy?\$hy:0;
		\$this->assign("valid",\$hy);
		\$this->assign("vo",\$vo);
		//扩展工具栏操作
		\$scdmodel = D('SystemConfigDetail');
		\$toolbarextension = \$scdmodel->getDetail(\$name,true,'toolbar');
		if (\$toolbarextension) {
			\$this->assign ( 'toolbarextension', \$toolbarextension );
		}
		if(\$_REQUEST['jump'] == 1){
			\$this->display("indexview");
		}else{
			\$this->display();
		}
EOF;
		$index .="\r\n\t}";

		// beforindex
		$before_index='';
		$before_index .="\r\n\t/**";
		$before_index .="\r\n\t * @Title: _before_index";
		$before_index .="\r\n\t * @Description: todo(前置index函数)";
		$before_index .="\r\n\t * @author ".$_SESSION['loginUserName'];
		$before_index .="\r\n\t * @date ".date('Y-m-d H:i:s');
		$before_index .="\r\n\t * @throws ";
		$before_index .="\r\n\t*/";
		$before_index .="\r\n\tfunction _before_index() {";

		// 默认生成 前置函数
		$before_edit ='';
		$before_edit .="\r\n\t/**";
		$before_edit .="\r\n\t * @Title: _before_edit";
		$before_edit .="\r\n\t * @Description: todo(前置编辑函数)";
		$before_edit .="\r\n\t * @author ".$_SESSION['loginUserName'];
		$before_edit .="\r\n\t * @date ".date('Y-m-d H:i:s');
		$before_edit .="\r\n\t * @throws ";
		$before_edit .="\r\n\t*/";
		$before_edit .="\r\n\tfunction _before_edit(){";


		$before_insert='';
		$before_insert.="\r\n\t/**";
		$before_insert.="\r\n\t * @Title: _before_insert";
		$before_insert.="\r\n\t * @Description: todo(新增前置函数，验证新增数据是否满足条件)";
		$before_insert.="\r\n\t * @author ".$_SESSION['loginUserName'];
		$before_insert.="\r\n\t * @date ".date('Y-m-d H:i:s');
		$before_insert.="\r\n\t * @throws ";
		$before_insert.="\r\n\t*/";
		$before_insert.="\r\n\tfunction _before_insert(){";

		$before_update = '';
		$before_update .= "\r\n\t/**";
		$before_update .= "\r\n\t * @Title: _before_update";
		$before_update .= "\r\n\t * @Description: todo(修改前置函数，验证数据是否满足条件)  ";
		$before_update .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$before_update .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$before_update .= "\r\n\t * @throws";
		$before_update .= "\r\n\t*/";
		$before_update .= "\r\n\tfunction _before_update(){";

		$before_delete = '';
		$before_delete .= "\r\n\t/**";
		$before_delete .= "\r\n\t * @Title: _before_update";
		$before_delete .= "\r\n\t * @Description: todo(删除前置函数,验证数据是否满足条件)  ";
		$before_delete .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$before_delete .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$before_delete .= "\r\n\t * @throws";
		$before_delete .= "\r\n\t*/";
		$before_delete .= "\r\n\tfunction _before_delete(){";

		// 重写父类编辑函数--为多表操作所做的修改 屈强@20141008 15:53
		$edit ='';
		$edit .="\r\n\t/**";
		$edit .="\r\n\t * @Title: edit";
		$edit .="\r\n\t * @Description: todo(重写父类编辑函数)";
		$edit .="\r\n\t * @author ".$_SESSION['loginUserName'];
		$edit .="\r\n\t * @date ".date('Y-m-d H:i:s');
		$edit .="\r\n\t * @throws ";
		$edit .="\r\n\t*/";
		$edit .="\r\n\tfunction edit(\$isdisplay=1){";

		$edit .="\r\n\t\t\$mainTab = '{$this->tableName}';";
		$edit .="\r\n\t\t//获取当前控制器名称";
		$edit .="\r\n\t\t\$name=\$this->getActionName();";
		$edit .="\r\n\t\t\$model = D(\"{$this->nodeName}View\");";
		$edit .="\r\n\t\t//获取当前主键";
		$edit .="\r\n\t\t\$map[\$mainTab.'.id']=\$_REQUEST['id'];";
		$edit .="\r\n\t\t\$vo = \$model->where(\$map)->find();";
		$edit .="\r\n\t\tif(!\$vo){";
		$edit .="\r\n\t\t\$this->getFormIndexLoad(\$vo);";
		$edit .="\r\n\t\t}";
		//$edit .="\r\n\t\tif(empty(\$vo)){";
		//$edit .="\r\n\t\t\t\$this->display (\"Public:404\");";
		//$edit .="\r\n\t\t\texit;";
		//$edit .="\r\n\t\t}";
		$edit .="\r\n\t\tif (method_exists ( \$this, '_filter' )) {";
		$edit .="\r\n\t\t\t\$this->_filter ( \$map );";
		$edit .="\r\n\t\t}";
		$edit .="\r\n\t\t//读取动态配制";
		$edit .="\r\n\t\t\$this->getSystemConfigDetail(\$name);";
		$edit .="\r\n\t\t//扩展工具栏操作";
		$edit .="\r\n\t\t\$scdmodel = D('SystemConfigDetail');";
		$edit .="\r\n\t\t// 上一条数据ID";
		$edit .="\r\n\t\t\$map['id'] = array(\"lt\",\$id);";
		$edit .="\r\n\t\t\$updataid = \$model->where(\$map)->order('id desc')->getField('id');";
		$edit .="\r\n\t\t\$this->assign(\"updataid\",\$updataid);";
		$edit .="\r\n\t\t// 下一条数据ID";
		$edit .="\r\n\t\t\$map['id'] = array(\"gt\",\$id);";
		$edit .="\r\n\t\t\$downdataid = \$model->where(\$map)->getField('id');";
		$edit .="\r\n\t\t\$this->assign(\"downdataid\",\$downdataid);";
		$edit .="\r\n\t\t//lookup带参数查询";
		$edit .="\r\n\t\t\$module=A(\$name);";
		$edit .="\r\n\t\tif (method_exists(\$module,\"_after_edit\")) {";
		$edit .="\r\n\t\t\tcall_user_func(array(\$module,\"_after_edit\"),\$vo);";
		$edit .="\r\n\t\t}";
		$edit .="\r\n\t\t\$this->assign( 'vo', \$vo );";
		$edit .="\r\n\t\tif(\$isdisplay)";
		$edit .="\r\n\t\t\$this->display ();";
		$edit.="\r\n\t}";
		//////////////////////////////////////////////////////////////////
		/*
		 * 不要重写父类中的edit函数
		 */
		//$edit ='';
		/////////////////////////////////////////////////////////////////////
		// 默认生成后置函数
		$after_edit = '';
		$after_list ='';
		$after_insert ='';
		$before_add='';
		$after_update='';

		// 删除子表数据
		$delChildData='';
		$is_create_del_child = false;
		$is_create_opraete_child = false;
		$is_create_modify_child = false;
		$is_create_insert_child = false;
		//控制地址组件修改代码生成
		$isArea = false;
		// 实例化MODE
		$model_code = '';
		$is_include_model = false;

		$after_edit .="\r\n\t/**";
		$after_edit .="\r\n\t * @Title: _after_edit";
		$after_edit .="\r\n\t * @Description: todo(后置编辑函数)";
		$after_edit .="\r\n\t * @author ".$_SESSION['loginUserName'];
		$after_edit .="\r\n\t * @date ".date('Y-m-d H:i:s');
		$after_edit .="\r\n\t * @throws ";
		$after_edit .="\r\n\t*/";
		$after_edit .="\r\n\tfunction _after_edit(\$vo){";

		$after_list.="\r\n\t/**";
		$after_list.="\r\n\t * @Title: _after_list";
		$after_list.="\r\n\t * @Description: todo(前置List)";
		$after_list.="\r\n\t * @author ".$_SESSION['loginUserName'];
		$after_list.="\r\n\t * @date ".date('Y-m-d H:i:s');
		$after_list.="\r\n\t * @throws ";
		$after_list.="\r\n\t*/";
		$after_list.="\r\n\tfunction _after_list(){";

		$after_insert .= "\r\n\t/**";
		$after_insert .= "\r\n\t * @Title: _after_insert";
		$after_insert .= "\r\n\t * @Description: todo(后置insert函数)  ";
		$after_insert .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$after_insert .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$after_insert .= "\r\n\t * @throws";
		$after_insert .= "\r\n\t*/";
		$after_insert .= "\r\n\tfunction _after_insert(\$id){";

		$before_add .= "\r\n\t/**";
		$before_add .= "\r\n\t * @Title: _before_add";
		$before_add .= "\r\n\t * @Description: todo(前置add函数)  ";
		$before_add .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$before_add .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$before_add .= "\r\n\t * @throws";
		$before_add .= "\r\n\t*/";
		$before_add .= "\r\n\tfunction _before_add(){";



		$after_update .= "\r\n\t/**";
		$after_update .= "\r\n\t * @Title: _after_update";
		$after_update .= "\r\n\t * @Description: todo(后置update函数)  ";
		$after_update .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$after_update .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$after_update .= "\r\n\t * @throws";
		$after_update .= "\r\n\t*/";
		$after_update .= "\r\n\tfunction _after_update(){";

		/**
		 * 步骤：
		 * 1。先源数据 $filedData['visibility'] 中的项按表名['tablename']分组。
		 * 2。区分出主从表，主表的文件名用当前action名命名，从表以直接表名命名。
		 * 3。遍历分组后的数据，依次生成Model文件。
		 *
		 *		表数组，基础为二维.
		 * 	0=>主表信息
		 * 	1=>所有从表的信息
		 */
		/*	处理子表错误，子表不做数据添加 会导致修改 视图查询数据若用户只勾取部分表字段 引起的查询不出数据。
		 * 处理方式为：取出当前action的所有子表 都生成 添加与修改都 做处理
		 **/

		$curTabData = $this->getDataBaseConf();
		$curTabData = $curTabData['cur'];
		foreach ($curTabData['datebase'] as $key => $val){
			if(!$val['isprimay']){
				$after_update.="\r\n\t\t//添加子表数据处理--".$_SESSION['loginUserName'].'@'.date('Y-m-d H:i:s');
				$after_update.="\r\n\t\t\${$key}Mode = D('".createRealModelName($key)."');";
				$after_update.="\r\n\t\t\${$key}Data = \${$key}Mode->create();";
				$after_update.="\r\n\t\tunset(\${$key}Data['id']);";
				$after_update.="\r\n\t\tif(\${$key}Data){";
				$after_update.="\r\n\t\t\t\t\${$key}Mode->where('masid='.\${$key}Data['masid'])->save(\${$key}Data);";
				$after_update.="\r\n\t\t}";

				$after_insert .="\r\n\t\t// 添加子表数据处理----".$_SESSION['loginUserName'].'@'.date('Y-m-d H:i:s');
				$after_insert .="\r\n\t\t\${$key}Mode = D('".createRealModelName($key)."');";
				$after_insert .="\r\n\t\t\${$key}Data = \${$key}Mode->create();";
				$after_insert .="\r\n\t\tunset(\${$key}Data['id']);";
				$after_insert .="\r\n\t\tif(\${$key}Data){";
				$after_insert .="\r\n\t\t\t\t\${$key}Data['masid']=\$id;";
				$after_insert .="\r\n\t\t\t\t\${$key}Mode->add(\${$key}Data);";
				$after_insert .="\r\n\t\t}";
			}
		}
		// 审批流
		if($isaudit){
		}
		foreach ($fieldData['visibility'] as $k=>$v){
			$property = $this->getProperty($v['catalog']);
			if($v[$property['catalog']['name']] == 'upload'){
				$after_edit		.= "\r\n\t\t\$this->getAttachedRecordList(\$vo['id']);";

				$after_update 	.= "\r\n\t\t\$id=\$_REQUEST['id'];";
				$after_update 	.= "\r\n\t\t\$this->swf_upload(\$id);";

				$after_insert	.= "\r\n\t\t\$this->swf_upload(\$id);";
			}
// 			if($v[$property['catalog']['name']] == 'checkbox'){
// 				$after_edit .="\r\n\t\tif(\$vo['".$v[$property['fields']['name']]."'])\n\t\t\t\$vo[\"".$v[$property['fields']['name']]."\"]=explode(',',\$vo[\"".$v[$property['fields']['name']]."\"]);";
// 			}
			if($v[$property['catalog']['name']] == 'datatable'){
				// 删除子表数据
				if(!$is_create_del_child){
					$delChildData.=$this->getDataTableAction();
					$is_create_del_child = true;
				}
				//if(!$is_create_opraete_child){
// 				$after_edit .="\r\n\t\t// 内嵌表处理".$v[$property['fields']['name']];
// 				$after_edit .="\r\n\t\t\$innerTabelObj".$v[$property['fields']['name']]." = M('".$this->tableName.'_sub_'.$v[$property['fields']['name']]."');"; // 内嵌表名 生成规则： 主表名称_sub_组件名称
// 				$after_edit .="\r\n\t\t\$innerTabelObj".$v[$property['fields']['name']]."Data = \$innerTabelObj".$v[$property['fields']['name']]."->where('masid='.\$vo['id'])->select();";
// 				$after_edit .="\r\n\t\t\$this->assign(\"innerTabelObj".$v[$property['fields']['name']]."Data\",\$innerTabelObj".$v[$property['fields']['name']]."Data);";
				$after_edit .= $this->dataTableEditCode($v , $property);
				//$is_create_opraete_child = true;
				//}

				if(!$is_create_modify_child){
// 					$after_update .="\r\n\t\t// 内嵌表数据添加处理";
// 					$after_update .="\r\n\t\t\$datatablefiexname =\"{$this->tableName}_sub_\";";
// 					$after_update .="\r\n\t\t\$insertData = array();// 数据添加缓存集合";
// 					$after_update .="\r\n\t\t\$updateData = array();// 数据修改缓存集合";
// 					$after_update .="\r\n\t\tif(\$_POST['datatable']){";
// 					$after_update .="\r\n\t\t	foreach(\$_POST['datatable'] as \$key=>\$val){";
// 					$after_update .="\r\n\t\t		foreach(\$val as \$k=>\$v){";
// 					$after_update .="\r\n\t\t			if(\$v['id']){";
// 					$after_update .="\r\n\t\t				\$updateData[\$k][]=\$v;";
// 					$after_update .="\r\n\t\t			}else{";
// 					$after_update .="\r\n\t\t				\$insertData[\$k][]=\$v;";
// 					$after_update .="\r\n\t\t			}";
// 					$after_update .="\r\n\t\t		}";
// 					$after_update .="\r\n\t\t	}";
// 					$after_update .="\r\n\t\t}";
// 					$after_update .="\r\n\t\t//数据处理";
// 					$after_update .="\r\n\t\tif(\$insertData){";
// 					$after_update .="\r\n\t\t	foreach(\$insertData as \$k=>\$v){";
// 					$after_update .="\r\n\t\t		\$model = M(\$datatablefiexname.\$k);";
// 					$after_update .="\r\n\t\t		foreach(\$v as \$key=>\$val){";
// 					$after_update .="\r\n\t\t			\$val['masid'] = \$_POST['id'];";
// 					$after_update .="\r\n\t\t			\$model->add(\$val);";
// 					$after_update .="\r\n\t\t		}";
// 					$after_update .="\r\n\t\t	}";
// 					$after_update .="\r\n\t\t}";
// 					$after_update .="\r\n\t\tif(\$updateData){";
// 					$after_update .="\r\n\t\t	foreach(\$updateData as \$k=>\$v){";
// 					$after_update .="\r\n\t\t		\$model = M(\$datatablefiexname.\$k);";
// 					$after_update .="\r\n\t\t		foreach(\$v as \$key=>\$val){";
// 					$after_update .="\r\n\t\t			\$model->save(\$val);";
// 					$after_update .="\r\n\t\t		}";
// 					$after_update .="\r\n\t\t	}";
// 					$after_update .="\r\n\t\t}";
					$after_update .=$this->updateDatatableCode();
					$is_create_modify_child = true;
				}

				if(!$is_create_insert_child){
// 					$after_insert.="\r\n\t\t// 内嵌表数据添加处理";
// 					$after_insert.="\r\n\t\t\$datatablefiexname =\"{$this->tableName}_sub_\";";
// 					$after_insert.="\r\n\t\t\$insertData = array();// 数据添加缓存集合";
// 					$after_insert.="\r\n\t\tif(\$_POST['datatable']){";
// 					$after_insert.="\r\n\t\t	foreach(\$_POST['datatable'] as \$key=>\$val){";
// 					$after_insert.="\r\n\t\t		foreach(\$val as \$k=>\$v){";
// 					$after_insert.="\r\n\t\t			\$insertData[\$k][]=\$v;";
// 					$after_insert.="\r\n\t\t		}";
// 					$after_insert.="\r\n\t\t	}";
// 					$after_insert.="\r\n\t\t}";
// 					$after_insert.="\r\n\t\t//数据处理";
// 					$after_insert.="\r\n\t\tif(\$insertData){";
// 					$after_insert.="\r\n\t\t	foreach(\$insertData as \$k=>\$v){";
// 					$after_insert.="\r\n\t\t		\$model = M(\$datatablefiexname.\$k);";
// 					$after_insert.="\r\n\t\t		foreach(\$v as \$key=>\$val){";
// 					$after_insert.="\r\n\t\t			\$val['masid'] = \$id;";
// 					$after_insert.="\r\n\t\t			\$model->add(\$val);";
// 					$after_insert.="\r\n\t\t		}";
// 					$after_insert.="\r\n\t\t	}";
// 					$after_insert.="\r\n\t\t}";
					$after_insert .=$this->insertDatatableCode();
					$is_create_insert_child = true;
				}
			}

			if($v[$property['catalog']['name']] == 'areainfo'  && $isArea==false){
				$after_edit .=$this->getAreaInfoCode();
				$isArea==true;
			}
			if($v[$property['subimporttableobj']['name']] ){
				// 普通的绑定表
// 				$condition = '';
// 				if($v[$property['conditions']['name']]){
// 					$condition = "->where('status=1 and ".html_entity_decode($v[$property['conditions']['name']])."')";
// 				}else {
// 					$condition = "->where('status=1')";
// 				}

// 				$before_add.= "\r\n\t\t\$model=M(\"".$v[$property['subimporttableobj']['name']]."\");";
// 				$before_add.= "\r\n\t\t\$list{$v[$property['fields']['name']]} =\$model{$condition}->field(\"".$v[$property['subimporttablefieldobj']['name']].",".$v[$property['subimporttablefield2obj']['name']]."\")->select();";
// 				$before_add.="\r\n\t\t\$this->assign(\"".$v[$property['fields']['name']]."list\"".",\$list{$v[$property['fields']['name']]});";

// 				$after_edit .="\r\n\t\t\$model=M(\"{$v[$property['subimporttableobj']['name']]}\");";
// 				$after_edit .="\r\n\t\t\$list{$v[$property['fields']['name']]} =\$model{$condition}->field(\"".$v[$property['subimporttablefieldobj']['name']].",".$v[$property['subimporttablefield2obj']['name']]."\")->select();";
// 				$after_edit .="\r\n\t\t\$this->assign(\"".$v[$property['fields']['name']]."list\"".",\$list{$v[$property['fields']['name']]});";
			}elseif($v[$property['treedtable']['name']]){
				// 绑定的是只能选择最后一级选中效果
// 				$before_add.= "\r\n\t\t//添加-只能选择下拉框最后一级的特殊数据获取方式 @date:".Date('yyyy-MM-dd H:i:s');
// 				$before_add.= "\r\n\t\t\$model=D(\"MisSystemRecursion\");";
// 				$before_add.= "\r\n\t\t\$treeSelect{$v[$property['fields']['name']]}Data = \$model->modelShow('{$v[$property['treedtable']['name']]}' , array('key'=>'id','pkey'=>'{$v[$property['treeparentfield']['name']]}','fields'=>'{$v[$property['treevaluefield']['name']]},{$v[$property['treeshowfield']['name']]}') , 0 , 1);";
// 				$before_add.= "\r\n\t\t\$this->assign(\"treeSelect{$v[$property['fields']['name']]}Data\",\$treeSelect{$v[$property['fields']['name']]}Data);";

// 				$after_edit.= "\r\n\t\t//修改-只能选择下拉框最后一级的特殊数据获取方式 @date:".Date('yyyy-MM-dd H:i:s');
// 				$after_edit.= "\r\n\t\t\$model=D(\"MisSystemRecursion\");";
// 				$after_edit.= "\r\n\t\t\$treeSelect{$v[$property['fields']['name']]}Data = \$model->modelShow('{$v[$property['treedtable']['name']]}' , array('key'=>'id','pkey'=>'{$v[$property['treeparentfield']['name']]}','fields'=>'{$v[$property['treevaluefield']['name']]},{$v[$property['treeshowfield']['name']]}') , 0 , 1);";
// 				$after_edit.= "\r\n\t\t\$this->assign(\"treeSelect{$v[$property['fields']['name']]}Data\",\$treeSelect{$v[$property['fields']['name']]}Data);";
					
			}else{
				// 绑定的是selectlist数据源
				if($v[$property['showoption']['name']]){
// 					if(!$is_include_model){
// 						$model_code = "\r\n\t\t\$model=D('Selectlist');";
// 						$is_include_model = true;
// 					}
// 					$after_edit .=$model_code. "\r\n\t\t\$selectlis = \$model->GetRules('{$v['showoption']}');";
// 					$after_edit .="\r\n\t\t\$selectlist{$v[$property['fields']['name']]}=array();";
// 					$after_edit .="\r\n\t\tforeach(\$selectlis['{$v['showoption']}'] as \$k=>\$v){";
// 					$after_edit .="\r\n\t\t\t\$temp['key']=\$k;";
// 					$after_edit .="\r\n\t\t\t\$temp['val']=\$v;";
// 					$after_edit .="\r\n\t\t\t\$selectlist{$v[$property['fields']['name']]}[]=\$temp;";
// 					$after_edit .="\r\n\t\t}";
// 					$after_edit .="\r\n\t\t\$this->assign(\"selectlist{$v[$property['fields']['name']]}\",\$selectlist{$v[$property['fields']['name']]});";

// 					$before_add .=$model_code."\r\n\t\t\$selectlis = \$model->GetRules('{$v['showoption']}');";
// 					$before_add .="\r\n\t\t\$selectlist{$v[$property['fields']['name']]}=array();";
// 					$before_add .="\r\n\t\tforeach(\$selectlis['{$v['showoption']}'] as \$k=>\$v){";
// 					$before_add .="\r\n\t\t\t\$temp['key']=\$k;";
// 					$before_add .="\r\n\t\t\t\$temp['val']=\$v;";
// 					$before_add .="\r\n\t\t\t\$selectlist{$v[$property['fields']['name']]}[]=\$temp;";
// 					$before_add .="\r\n\t\t}";
// 					$before_add .="\r\n\t\t\$this->assign(\"selectlist{$v[$property['fields']['name']]}\",\$selectlist{$v[$property['fields']['name']]});";
				}
			}
		}

		$before_edit .="\r\n\t\t\$mainTab = '{$this->tableName}';";
		$before_edit .="\r\n\t\t//获取当前控制器名称";
		$before_edit .="\r\n\t\t\$name=\$this->getActionName();";
		$before_edit .="\r\n\t\t\$model = D(\"{$this->nodeName}View\");";
		$before_edit .="\r\n\t\t//获取当前主键";
		$before_edit .="\r\n\t\t\$map[\$mainTab.'.id']=\$_REQUEST['id'];";
		$before_edit .="\r\n\t\t\$vo = \$model->where(\$map)->find();";
		$datetiem = date('Y-m-d H:i:s');
		$before_insert .=<<<EOF
		
		// 插入之前，验证编码方案是否正确符合规定 @{$datetiem}
		\$name = \$this->getActionName();
		\$orderno = \$_POST['orderno'];
		\$MisSystemOrdernoDao = D("MisSystemOrderno");
		\$data = \$MisSystemOrdernoDao->validateOrderno(\$name,\$orderno);
		if(\$data['result']){
			\$_POST['parentid'] = \$data['parentid'];
			
		}else{
			\$this->error(\$data['altMsg']);
		}
EOF;
		$before_update .=<<<EOF
		
		//插入之前，验证编码方案是否正确符合规定 @{$datetiem}
		\$name = \$this->getActionName();
		\$orderno = \$_POST['orderno'];
		\$MisSystemOrdernoDao = D("MisSystemOrderno");
		\$data = \$MisSystemOrdernoDao->validateOrderno(\$name,\$orderno,\$_POST['id']);
		if(\$data['result']){
			\$_POST['parentid'] = \$data['parentid'];
		}else{
			\$this->error(\$data['altMsg']);
		}
EOF;
		$before_delete .=<<<EOF
		//删除之前，验证当前数据是否可删除 @{$datetiem}
		\$name = \$this->getActionName();
		\$map['parentid'] = \$_REQUEST['id'];
		\$map['status'] = 1;
		\$model = D(\$name);
		\$data = \$model->where(\$map)->select();
		if(\$data){
			\$this->error('该条数据下有子数据，不能删除');
		}
EOF;

		$before_edit .="\r\n\t\t\$this->assign( 'vo', \$vo );";

		$before_index .="\r\n\t\t\$this->_extend_before_index();";
		$before_index .="\r\n\t}";

		$before_edit.="\r\n\t\t\$this->_extend_before_edit();";
		$before_edit.="\r\n\t}";
		$before_insert.="\r\n\t\t\$this->_extend_before_insert();";
		$before_insert.="\r\n\t}";
		$before_update.="\r\n\t\t\$this->_extend_before_update();";
		$before_update.="\r\n\t}";

		$after_edit.="\r\n\t\t\$this->_extend_after_edit(\$vo);";
		$after_edit .="\r\n\t}";
		$after_list.="\r\n\t\t\$this->_extend_after_list();";
		$after_list .="\r\n\t}";
		//设置刷新的id值
		$after_insert .="\r\n\t\tCookie::set('{$this->nodeName}view',\$id);";
		$after_insert .="\r\n\t\t\$this->_extend_after_insert(\$id);";
		$after_insert .="\r\n\t}";
		$before_add.="\r\n\t\t\$this->_extend_before_add(\$vo);";
		$before_add .="\r\n\t}";

		//设置刷新的id值
		$after_update .="\r\n\t\tCookie::set('{$this->nodeName}view',\$_POST['id']);";
		$after_update .="\r\n\t\t\$this->_extend_after_update();";
		$after_update .="\r\n\t}";

		$before_delete .="\r\n\t\t\$this->_extend_before_delete();";
		$before_delete .="\r\n\t}";
		/*
		 $extendphpcode .="\r\n\tfunction miniindex(){";
		 $extendphpcode .="\r\n\t	\$this->assign(\"type\",\$_REQUEST['type']);";
		 $extendphpcode .="\r\n\t	\$this->assign(\"ecode\",\$_REQUEST['ecode']);";
		 $extendphpcode .="\r\n\t	parent::index();";
		 $extendphpcode .="\r\n\t	//\$this->display();";
		 $extendphpcode .="\r\n\t}";
		 */

		//$phpcode.=$before_edit.$before_insert.$before_update.$after_edit.$after_list.$after_insert.$after_add.$after_update;
		$phpcode.=$index.$edit.$before_index.$before_edit.$before_insert.$before_update.$before_delete.$after_edit.$after_insert.$before_add.$after_update.$delChildData;
		$phpcode.="\r\n}\r\n?>";
		$extendphpcode.="\r\n}\r\n?>";
		if(!is_dir(dirname($actionPath))) mk_dir(dirname($actionPath),0777);
		if( false === file_put_contents( $actionPath , $phpcode )){
			$this->error ("Action文件生成失败!");
		}
		if($isinitExtendAction){
			if( false === file_put_contents( $actionExtendPath , $extendphpcode )){
				$this->error ("扩展Action文件生成失败!");
			}
		}else{
			if(false == (file_exists($actionExtendPath))){
				if( false === file_put_contents( $actionExtendPath , $extendphpcode )){
					$this->error ("扩展Action文件生成失败!");
				}
			}
		}
	}

	/**
	 * @Title: createBaseArchives
	 * @Description: todo(生成基础档案Action)
	 * @param boolean $isaudit	是否为审批
	 * @param boolean $isinitExtendAction  是否对扩展action做初始化处理
	 * @author quqiang
	 * @date 2014-11-18 下午07:09:06
	 * @throws
	 */
	function createBaseArchivesLsit($cotrollname , $isaudit=false , $isinitExtendAction=false){
		logs('我要生成acationqk::::'.$cotrollname);
		// 1.获取到当前Action的组件配置文件信息
		$model = D('Autoform');
		$dir = '/autoformconfig/';
		$path = $dir.$this->nodeName.'.php';
		$model->setPath($path);

		// 获取所有节点信息
		//$allNodeconfig = $model->getAllControllConfig(); // 物理文件
		$allNodeconfig = $this->getAllControllConfig();

		$fieldData['visibility'] = $allNodeconfig;
		$cotrollname=ucfirst($cotrollname);
		$actionPath =  LIB_PATH."Action/".$cotrollname."Action.class.php";
		$actionExtendPath = LIB_PATH."Action/".$cotrollname."ExtendAction.class.php";

		$extendphpcode .="<?php\r\n/**";
		$extendphpcode .="\r\n * @Title: {$cotrollname}Action";
		$extendphpcode .="\r\n * @Package package_name";
		$extendphpcode .= "\r\n * @Description: todo(动态表单_扩展类。本类为用户代码注入入口，系统一旦生成将不再重复生成。";
		$extendphpcode .= "\r * \t\t\t\t\t\t但当用户选为组合表单方案后会更新该文件，请做好备份)";
		$extendphpcode .="\r\n * @author ".$_SESSION['loginUserName'];
		$extendphpcode .="\r\n * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d";
		$extendphpcode .="\r\n * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d";
		$extendphpcode .="\r\n * @date ".date('Y-m-d H:i:s');
		$extendphpcode .="\r\n * @version V1.0";
		$extendphpcode .="\r\n*/";
		$extendphpcode .="\r\nclass ";

		$phpcode.="<?php\r\n/**";
		$phpcode.="\r\n * @Title: {$cotrollname}Action";
		$phpcode.="\r\n * @Package package_name";
		$phpcode.="\r\n * @Description: todo(动态表单_自动生成-".$this->nodeTitle.")";
		$phpcode.="\r\n * @author ".$_SESSION['loginUserName'];
		$phpcode.="\r\n * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d";
		$phpcode.="\r\n * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d";
		$phpcode.="\r\n * @date ".date('Y-m-d H:i:s');
		$phpcode.="\r\n * @version V1.0";
		$phpcode.="\r\n*/";
		$phpcode.="\r\nclass ";

		if( $isaudit ){
			$jcc="CommonAuditAction";
		}else{
			$jcc="CommonAction";
		}
		$extendsCC = $cotrollname."ExtendAction";
		$extendphpcode .=$cotrollname."ExtendAction extends ".$jcc." {\r\n";
		$phpcode.= $cotrollname."Action extends ".$extendsCC." {\r\n";
		$phpcode.="\tpublic function _filter(&\$map){";//start filter
		$phpcode.="\r\n\t\tif (\$_SESSION[\"a\"] != 1){\r\n\t\t\t\$map['status']=array(\"gt\",-1);";
		$phpcode.="\r\n\t\t}";
		$phpcode.="\r\n\t\t\$this->_extend_filter(\$map);";
		$phpcode .="\r\n\t}";

		$extendphpcode .="\r\n\t/**";
		$extendphpcode .="\r\n\t * @Title: _extend_filter";
		$extendphpcode .="\r\n\t * @Description: todo(扩展前置index函数)";
		$extendphpcode .="\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .="\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .="\r\n\t * @throws ";
		$extendphpcode .="\r\n\t*/";
		$extendphpcode .="\r\n\tfunction _extend_filter() {";
		$extendphpcode .="\r\n\t}";

		$extendphpcode .="\r\n\t/**";
		$extendphpcode .="\r\n\t * @Title: _extend_before_index";
		$extendphpcode .="\r\n\t * @Description: todo(扩展前置index函数)";
		$extendphpcode .="\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .="\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .="\r\n\t * @throws ";
		$extendphpcode .="\r\n\t*/";
		$extendphpcode .="\r\n\tfunction _extend_before_index() {";
		$extendphpcode .="\r\n\t}";

		$extendphpcode .="\r\n\t/**";
		$extendphpcode .="\r\n\t * @Title: _extend_before_edit";
		$extendphpcode .="\r\n\t * @Description: todo(扩展的前置编辑函数)";
		$extendphpcode .="\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .="\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .="\r\n\t * @throws ";
		$extendphpcode .="\r\n\t*/";
		$extendphpcode .="\r\n\tfunction _extend_before_edit(){";
		$extendphpcode.="\r\n\t}";

		$extendphpcode.="\r\n\t/**";
		$extendphpcode.="\r\n\t * @Title: _extend_before_insert";
		$extendphpcode.="\r\n\t * @Description: todo(扩展的前置添加函数)";
		$extendphpcode.="\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode.="\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode.="\r\n\t * @throws ";
		$extendphpcode.="\r\n\t*/";
		$extendphpcode.="\r\n\tfunction _extend_before_insert(){";
		$extendphpcode.="\r\n\t}";

		$extendphpcode .= "\r\n\t/**";
		$extendphpcode .= "\r\n\t * @Title: _extend_before_update";
		$extendphpcode .= "\r\n\t * @Description: todo(扩展前置修改函数)  ";
		$extendphpcode .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .= "\r\n\t * @throws";
		$extendphpcode .= "\r\n\t*/";
		$extendphpcode .= "\r\n\tfunction _extend_before_update(){";
		$extendphpcode .="\r\n\t}";

		$extendphpcode .="\r\n\t/**";
		$extendphpcode .="\r\n\t * @Title: _extend_after_edit";
		$extendphpcode .="\r\n\t * @Description: todo(扩展后置编辑函数)";
		$extendphpcode .="\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .="\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .="\r\n\t * @throws ";
		$extendphpcode .="\r\n\t*/";
		$extendphpcode .="\r\n\tfunction _extend_after_edit(\$vo){";
		$extendphpcode .="\r\n\t\tif(\$_GET['viewtype']=='view'){";
		$extendphpcode .="\r\n\t\t\t\$this->display('formview');";
		$extendphpcode .="\r\n\t\t\texit;";
		$extendphpcode .="\r\n\t\t}";
		$extendphpcode .="\r\n\t}";

		$extendphpcode.="\r\n\t/**";
		$extendphpcode.="\r\n\t * @Title: _extend_after_list";
		$extendphpcode.="\r\n\t * @Description: todo(扩展前置List)";
		$extendphpcode.="\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode.="\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode.="\r\n\t * @throws ";
		$extendphpcode.="\r\n\t*/";
		$extendphpcode.="\r\n\tfunction _extend_after_list(){";
		$extendphpcode .="\r\n\t}";

		$extendphpcode .= "\r\n\t/**";
		$extendphpcode .= "\r\n\t * @Title: _extend_after_insert";
		$extendphpcode .= "\r\n\t * @Description: todo(扩展后置insert函数)  ";
		$extendphpcode .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .= "\r\n\t * @throws";
		$extendphpcode .= "\r\n\t*/";
		$extendphpcode .= "\r\n\tfunction _extend_after_insert(\$id){";
		$extendphpcode .="\r\n\t}";

		$extendphpcode .= "\r\n\t/**";
		$extendphpcode .= "\r\n\t * @Title: _extend_before_add";
		$extendphpcode .= "\r\n\t * @Description: todo(扩展前置add函数)  ";
		$extendphpcode .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .= "\r\n\t * @throws";
		$extendphpcode .= "\r\n\t*/";
		$extendphpcode .= "\r\n\tfunction _extend_before_add(\$vo){";
		$extendphpcode .= "\r\n\t\t\$this->getFormIndexLoad(\$vo);";
		$extendphpcode .= "\r\n\t}";

		$extendphpcode .= "\r\n\t/**";
		$extendphpcode .= "\r\n\t * @Title: _extend_before_delete";
		$extendphpcode .= "\r\n\t * @Description: todo(扩展前置删除函数)  ";
		$extendphpcode .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .= "\r\n\t * @throws";
		$extendphpcode .= "\r\n\t*/";
		$extendphpcode .= "\r\n\tfunction _extend_before_delete(){";
		$extendphpcode .="\r\n\t}";


		$extendphpcode .= "\r\n\t/**";
		$extendphpcode .= "\r\n\t * @Title: _extend_after_update";
		$extendphpcode .= "\r\n\t * @Description: todo(扩展后置update函数)  ";
		$extendphpcode .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .= "\r\n\t * @throws";
		$extendphpcode .= "\r\n\t*/";
		$extendphpcode .= "\r\n\tfunction _extend_after_update(){";
		$extendphpcode .="\r\n\t}";


		// 重写父类index函数--为多表操作所做的修改 屈强@20141125 1712
		$index="";
		$index .="\r\n\t/**";
		$index .="\r\n\t * @Title: index";
		$index .="\r\n\t * @Description: todo(重写父类index函数)";
		$index .="\r\n\t * @author ".$_SESSION['loginUserName'];
		$index .="\r\n\t * @date ".date('Y-m-d H:i:s');
		$index .="\r\n\t * @throws ";
		$index .="\r\n\t*/";
		$index .="\r\n\tfunction index(){";


		$index.=<<<EOF
		
		\$name=\$this->getActionName();
		\$listnewall =\$this->getDateSoure(\$name);
		if(!\$listnewall['list']){
		//不存在则读取当前表orderno当树形菜单
			\$model=D(\$name);
			\$listnewall['list']=\$model->where("status=1")->field("orderno,id,name,parentid")->select();
			\$listnewall['field']="id";
			\$listnewall['fielter']="id";
			
		}
		\$listnew=array();
		foreach(\$listnewall['list'] as \$k=>\$v){
			\$listnew[\$k]['name'] = "(".\$v['orderno'].")".\$v['name'];
			\$listnew[\$k][\$listnewall['fielter']]=\$v[\$listnewall['fielter']]?\$v[\$listnewall['fielter']]:\$v['id'];
			\$listnew[\$k]['parentid']=\$v['parentid'];
			\$listnew[\$k]['id']=\$v['id'];
		}
		\$param['rel']="{$this->nodeName}view";
		\$param['url']="__URL__/index/jump/1/hy/#".\$listnewall['fielter']."#";
		\$treemiso[]=array(
				'id'=>0,
				'pId'=>0,
				'url'=>'__URL__/index/jump/1',
				'target'=>'ajax',
				'rel'=>"{$this->nodeName}view",
				'name'=>'{$this->nodeTitle}',
				'title'=>'{$this->nodeTitle}',
				'open'=>true,
				'isParent'=>true,
		);
		\$treearr = \$this->getTree(\$listnew,\$param,\$treemiso);
		\$this->assign("treearr",\$treearr);
						
						
		//列表过滤器，生成查询Map对象
		\$map = \$this->_search ();				
		\$name=\$this->getActionName();				
		if(\$_REQUEST['hy']){
			//构造检索条件
			\$map[\$listnewall['field']] = \$_REQUEST['hy'];
			\$this->assign("hy",\$_REQUEST['hy']);
		}
		if (! empty ( \$name )) {
			\$qx_name=\$name;
			if(substr(\$name, -4)=="View"){
				\$qx_name = substr(\$name,0, -4);
			}
			//验证浏览及权限
			if( !isset(\$_SESSION['a']) ){
				\$map=D('User')->getAccessfilter(\$qx_name,\$map);
			}
			//列表页排序 ---开始-----2015-08-06 15:07 write by xyz
			if(\$_REQUEST['orderField']&&strpos(strtolower(\$_REQUEST['orderField']),' asc')===false&&strpos(strtolower(strpos(\$_REQUEST['orderField'])),' desc')===false){
				\$this->_list ( \$name, \$map);
			}else{
				\$sortsorder = '';
				\$sortsmap['modelname'] = \$name;
				\$sortsmap['sortsorder'] = array("gt",0);
				//管理员读公共设置
				if(\$_SESSION['a']){
					\$listincModel = M("mis_system_public_listinc");
					\$sortslist = \$listincModel->where(\$sortsmap)->order("sortsorder")->select();
				}else{
					//个人先读个人设置、没有再读公共设置
					\$sortsmap['userid'] = \$_SESSION [C ( 'USER_AUTH_KEY' )];
					\$listincModel = M("mis_system_private_listinc");
					\$sortslist = \$listincModel->where(\$sortsmap)->order("sortsorder")->select();
					if(empty(\$sortslist)){
						unset(\$sortsmap['userid']);
						\$listincModel = M("mis_system_public_listinc");
						\$sortslist = \$listincModel->where(\$sortsmap)->order("sortsorder")->select();
					}
				}
				//如果在设置里有相关数据、提取排序字段组合order by
				if(\$sortslist){
					foreach(\$sortslist as \$k=>\$v){
						\$sortsorder .= \$v['fieldname'].' '.\$v['sortstype'].',';
					}
					\$sortsorder = substr(\$sortsorder,0,-1);
				}
				//列表页排序 ---结束-----
				\$this->_list ( \$name, \$map,'', false,'','',\$sortsorder);
			}
				
				
		}
						
						
		\$scdmodel = D('SystemConfigDetail');
		//扩展工具栏操作
		\$toolbarextension = \$scdmodel->getDetail(\$name,true,'toolbar');
		if (\$toolbarextension) {
			\$this->assign ( 'toolbarextension', \$toolbarextension );
		}
		\$detailList = \$scdmodel->getDetail(\$name);
		if (\$detailList) {
			\$this->assign ( 'detailList', \$detailList );
		}		
		if(\$_REQUEST['jump'] == 1){
			\$this->display("indexview");
		}else{
			\$this->display();
		}
EOF;
		$index .="\r\n\t}";

		// beforindex
		$before_index='';
		$before_index .="\r\n\t/**";
		$before_index .="\r\n\t * @Title: _before_index";
		$before_index .="\r\n\t * @Description: todo(前置index函数)";
		$before_index .="\r\n\t * @author ".$_SESSION['loginUserName'];
		$before_index .="\r\n\t * @date ".date('Y-m-d H:i:s');
		$before_index .="\r\n\t * @throws ";
		$before_index .="\r\n\t*/";
		$before_index .="\r\n\tfunction _before_index() {";

		// 默认生成 前置函数
		$before_edit ='';
		$before_edit .="\r\n\t/**";
		$before_edit .="\r\n\t * @Title: _before_edit";
		$before_edit .="\r\n\t * @Description: todo(前置编辑函数)";
		$before_edit .="\r\n\t * @author ".$_SESSION['loginUserName'];
		$before_edit .="\r\n\t * @date ".date('Y-m-d H:i:s');
		$before_edit .="\r\n\t * @throws ";
		$before_edit .="\r\n\t*/";
		$before_edit .="\r\n\tfunction _before_edit(){";



		$before_insert='';
		$before_insert.="\r\n\t/**";
		$before_insert.="\r\n\t * @Title: _before_insert";
		$before_insert.="\r\n\t * @Description: todo(前置添加函数)";
		$before_insert.="\r\n\t * @author ".$_SESSION['loginUserName'];
		$before_insert.="\r\n\t * @date ".date('Y-m-d H:i:s');
		$before_insert.="\r\n\t * @throws ";
		$before_insert.="\r\n\t*/";
		$before_insert.="\r\n\tfunction _before_insert(){";

		$before_update = '';
		$before_update .= "\r\n\t/**";
		$before_update .= "\r\n\t * @Title: _before_update";
		$before_update .= "\r\n\t * @Description: todo(前置修改函数)  ";
		$before_update .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$before_update .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$before_update .= "\r\n\t * @throws";
		$before_update .= "\r\n\t*/";
		$before_update .= "\r\n\tfunction _before_update(){";

		$before_delete = '';
		$before_delete .= "\r\n\t/**";
		$before_delete .= "\r\n\t * @Title: _before_update";
		$before_delete .= "\r\n\t * @Description: todo(前置删除函数)  ";
		$before_delete .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$before_delete .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$before_delete .= "\r\n\t * @throws";
		$before_delete .= "\r\n\t*/";
		$before_delete .= "\r\n\tfunction _before_delete(){";

		// 重写父类编辑函数--为多表操作所做的修改 屈强@20141008 15:53
		$edit ='';
		$edit .="\r\n\t/**";
		$edit .="\r\n\t * @Title: edit";
		$edit .="\r\n\t * @Description: todo(重写父类编辑函数)";
		$edit .="\r\n\t * @author ".$_SESSION['loginUserName'];
		$edit .="\r\n\t * @date ".date('Y-m-d H:i:s');
		$edit .="\r\n\t * @throws ";
		$edit .="\r\n\t*/";
		$edit .="\r\n\tfunction edit(\$isdisplay=1){";

		$edit .="\r\n\t\t\$mainTab = '{$this->tableName}';";
		$edit .="\r\n\t\t//获取当前控制器名称";
		$edit .="\r\n\t\t\$name=\$this->getActionName();";
		$edit .="\r\n\t\t\$model = D(\"{$this->nodeName}View\");";
		$edit .="\r\n\t\t//获取当前主键";
		$edit .="\r\n\t\t\$map[\$mainTab.'.id']=\$_REQUEST['id'];";
		$edit .="\r\n\t\t\$vo = \$model->where(\$map)->find();";
		$edit .="\r\n\t\tif(!\$vo){";
		$edit .="\r\n\t\t\$this->getFormIndexLoad(\$vo);";
		$edit .="\r\n\t\t}";
// 		$edit .="\r\n\t\tif(empty(\$vo)){";
// 		$edit .="\r\n\t\t\t\$this->display (\"Public:404\");";
// 		$edit .="\r\n\t\t\texit;";
// 		$edit .="\r\n\t\t}";
		$edit .="\r\n\t\tif (method_exists ( \$this, '_filter' )) {";
		$edit .="\r\n\t\t\t\$this->_filter ( \$map );";
		$edit .="\r\n\t\t}";
		$edit .="\r\n\t\t//读取动态配制";
		$edit .="\r\n\t\t\$this->getSystemConfigDetail(\$name);";
		$edit .="\r\n\t\t//扩展工具栏操作";
		$edit .="\r\n\t\t\$scdmodel = D('SystemConfigDetail');";
		$edit .="\r\n\t\t// 上一条数据ID";
		$edit .="\r\n\t\t\$map['id'] = array(\"lt\",\$id);";
		$edit .="\r\n\t\t\$updataid = \$model->where(\$map)->order('id desc')->getField('id');";
		$edit .="\r\n\t\t\$this->assign(\"updataid\",\$updataid);";
		$edit .="\r\n\t\t// 下一条数据ID";
		$edit .="\r\n\t\t\$map['id'] = array(\"gt\",\$id);";
		$edit .="\r\n\t\t\$downdataid = \$model->where(\$map)->getField('id');";
		$edit .="\r\n\t\t\$this->assign(\"downdataid\",\$downdataid);";
		$edit .="\r\n\t\t//lookup带参数查询";
		$edit .="\r\n\t\t\$module=A(\$name);";
		$edit .="\r\n\t\tif (method_exists(\$module,\"_after_edit\")) {";
		$edit .="\r\n\t\t\tcall_user_func(array(\$module,\"_after_edit\"),\$vo);";
		$edit .="\r\n\t\t}";
		$edit .="\r\n\t\t\$this->assign( 'vo', \$vo );";
		$edit .="\r\n\t\tif(\$isdisplay)";
		$edit .="\r\n\t\t\$this->display ();";
		$edit.="\r\n\t}";
		//////////////////////////////////////////////////////////////////
		/*
		 * 不要重写父类中的edit函数
		 */
		$edit ='';
		/////////////////////////////////////////////////////////////////////
		// 默认生成后置函数
		$after_edit = '';
		$after_list ='';
		$after_insert ='';
		$before_add='';
		$after_update='';

		// 删除子表数据
		$delChildData='';
		$is_create_del_child = false;
		$is_create_opraete_child = false;
		$is_create_modify_child = false;
		$is_create_insert_child = false;
		//控制地址组件修改代码生成
		$isArea = false;
		
		// 实例化MODE
		$model_code = '';
		$is_include_model = false;

		$after_edit .="\r\n\t/**";
		$after_edit .="\r\n\t * @Title: _after_edit";
		$after_edit .="\r\n\t * @Description: todo(后置编辑函数)";
		$after_edit .="\r\n\t * @author ".$_SESSION['loginUserName'];
		$after_edit .="\r\n\t * @date ".date('Y-m-d H:i:s');
		$after_edit .="\r\n\t * @throws ";
		$after_edit .="\r\n\t*/";
		$after_edit .="\r\n\tfunction _after_edit(\$vo){";

		$after_list.="\r\n\t/**";
		$after_list.="\r\n\t * @Title: _after_list";
		$after_list.="\r\n\t * @Description: todo(前置List)";
		$after_list.="\r\n\t * @author ".$_SESSION['loginUserName'];
		$after_list.="\r\n\t * @date ".date('Y-m-d H:i:s');
		$after_list.="\r\n\t * @throws ";
		$after_list.="\r\n\t*/";
		$after_list.="\r\n\tfunction _after_list(){";

		$after_insert .= "\r\n\t/**";
		$after_insert .= "\r\n\t * @Title: _after_insert";
		$after_insert .= "\r\n\t * @Description: todo(后置insert函数)  ";
		$after_insert .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$after_insert .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$after_insert .= "\r\n\t * @throws";
		$after_insert .= "\r\n\t*/";
		$after_insert .= "\r\n\tfunction _after_insert(\$id){";

		$before_add .= "\r\n\t/**";
		$before_add .= "\r\n\t * @Title: _before_add";
		$before_add .= "\r\n\t * @Description: todo(前置add函数)  ";
		$before_add .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$before_add .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$before_add .= "\r\n\t * @throws";
		$before_add .= "\r\n\t*/";
		$before_add .= "\r\n\tfunction _before_add(){";



		$after_update .= "\r\n\t/**";
		$after_update .= "\r\n\t * @Title: _after_update";
		$after_update .= "\r\n\t * @Description: todo(后置update函数)  ";
		$after_update .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$after_update .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$after_update .= "\r\n\t * @throws";
		$after_update .= "\r\n\t*/";
		$after_update .= "\r\n\tfunction _after_update(){";

		/**
		 * 步骤：
		 * 1。先源数据 $filedData['visibility'] 中的项按表名['tablename']分组。
		 * 2。区分出主从表，主表的文件名用当前action名命名，从表以直接表名命名。
		 * 3。遍历分组后的数据，依次生成Model文件。
		 *
		 *		表数组，基础为二维.
		 * 	0=>主表信息
		 * 	1=>所有从表的信息
		 */
		/*	处理子表错误，子表不做数据添加 会导致修改 视图查询数据若用户只勾取部分表字段 引起的查询不出数据。
		 * 处理方式为：取出当前action的所有子表 都生成 添加与修改都 做处理
		 **/

		$curTabData = $this->getDataBaseConf();
		$curTabData = $curTabData['cur'];
		foreach ($curTabData['datebase'] as $key => $val){
			if(!$val['isprimay']){
				$after_update.="\r\n\t\t//添加子表数据处理--".$_SESSION['loginUserName'].'@'.date('Y-m-d H:i:s');
				$after_update.="\r\n\t\t\${$key}Mode = D('".createRealModelName($key)."');";
				$after_update.="\r\n\t\t\${$key}Data = \${$key}Mode->create();";
				$after_update.="\r\n\t\tunset(\${$key}Data['id']);";
				$after_update.="\r\n\t\tif(\${$key}Data){";
				$after_update.="\r\n\t\t\tif(\${$key}Mode->where('masid='.\${$key}Data['masid'])->find())";
				$after_update.="\r\n\t\t\t\t\${$key}Mode->where('masid='.\${$key}Data['masid'])->save(\${$key}Data);";
				$after_update.="\r\n\t\t\telse";
				$after_update.="\r\n\t\t\t\t\${$key}Mode->add(\${$key}Data);";
				$after_update.="\r\n\t\t}";
				
				
				$after_insert .="\r\n\t\t// 添加子表数据处理----".$_SESSION['loginUserName'].'@'.date('Y-m-d H:i:s');
				$after_insert .="\r\n\t\t\${$key}Mode = D('".createRealModelName($key)."');";
				$after_insert .="\r\n\t\t\${$key}Data = \${$key}Mode->create();";
				$after_insert .="\r\n\t\tunset(\${$key}Data['id']);";
				$after_insert .="\r\n\t\tif(\${$key}Data){";
				$after_insert .="\r\n\t\t\t\t\${$key}Data['masid']=\$id;";
				$after_insert .="\r\n\t\t\t\t\${$key}Mode->add(\${$key}Data);";
				$after_insert .="\r\n\t\t}";
			}
		}
		// 审批流
		if($isaudit){
		}
		foreach ($fieldData['visibility'] as $k=>$v){
			$property = $this->getProperty($v['catalog']);
			if($v[$property['catalog']['name']] == 'upload'){
				$after_edit		.= "\r\n\t\t\$this->getAttachedRecordList(\$vo['id']);";

				$after_update 	.= "\r\n\t\t\$id=\$_REQUEST['id'];";
				$after_update 	.= "\r\n\t\t\$this->swf_upload(\$id);";

				$after_insert	.= "\r\n\t\t\$this->swf_upload(\$id);";
			}
// 			if($v[$property['catalog']['name']] == 'checkbox'){
// 				$after_edit .="\r\n\t\tif(\$vo['".$v[$property['fields']['name']]."'])\n\t\t\t\$vo[\"".$v[$property['fields']['name']]."\"]=explode(',',\$vo[\"".$v[$property['fields']['name']]."\"]);";
// 			}
			if($v[$property['catalog']['name']] == 'datatable'){
				// 删除子表数据
				if(!$is_create_del_child){
					$delChildData.=$this->getDataTableAction();
					$is_create_del_child = true;
				}
				//if(!$is_create_opraete_child){
// 				$after_edit .="\r\n\t\t// 内嵌表处理".$v[$property['fields']['name']];
// 				$after_edit .="\r\n\t\t\$innerTabelObj".$v[$property['fields']['name']]." = M('".$this->tableName.'_sub_'.$v[$property['fields']['name']]."');"; // 内嵌表名 生成规则： 主表名称_sub_组件名称
// 				$after_edit .="\r\n\t\t\$innerTabelObj".$v[$property['fields']['name']]."Data = \$innerTabelObj".$v[$property['fields']['name']]."->where('masid='.\$vo['id'])->select();";
// 				$after_edit .="\r\n\t\t\$this->assign(\"innerTabelObj".$v[$property['fields']['name']]."Data\",\$innerTabelObj".$v[$property['fields']['name']]."Data);";
				$after_edit .= $this->dataTableEditCode($v , $property);
				//$is_create_opraete_child = true;
				//}

				if(!$is_create_modify_child){
// 					$after_update .="\r\n\t\t// 内嵌表数据添加处理";
					//$after_update .="\r\n\t\t\$datatablefiexname =\"{$this->tableName}_sub_\";";
// 					$after_update .="\r\n\t\t\$insertData = array();// 数据添加缓存集合";
// 					$after_update .="\r\n\t\t\$updateData = array();// 数据修改缓存集合";
// 					$after_update .="\r\n\t\tif(\$_POST['datatable']){";
// 					$after_update .="\r\n\t\t	foreach(\$_POST['datatable'] as \$key=>\$val){";
// 					$after_update .="\r\n\t\t		foreach(\$val as \$k=>\$v){";
// 					$after_update .="\r\n\t\t			if(\$v['id']){";
// 					$after_update .="\r\n\t\t				\$updateData[\$k][]=\$v;";
// 					$after_update .="\r\n\t\t			}else{";
// 					$after_update .="\r\n\t\t				\$insertData[\$k][]=\$v;";
// 					$after_update .="\r\n\t\t			}";
// 					$after_update .="\r\n\t\t		}";
// 					$after_update .="\r\n\t\t	}";
// 					$after_update .="\r\n\t\t}";
// 					$after_update .="\r\n\t\t//数据处理";
// 					$after_update .="\r\n\t\tif(\$insertData){";
// 					$after_update .="\r\n\t\t	foreach(\$insertData as \$k=>\$v){";
// 					$after_update .="\r\n\t\t		\$model = M(\$datatablefiexname.\$k);";
// 					$after_update .="\r\n\t\t		foreach(\$v as \$key=>\$val){";
// 					$after_update .="\r\n\t\t			\$val['masid'] = \$_POST['id'];";
// 					$after_update .="\r\n\t\t			\$model->add(\$val);";
// 					$after_update .="\r\n\t\t		}";
// 					$after_update .="\r\n\t\t	}";
// 					$after_update .="\r\n\t\t}";
// 					$after_update .="\r\n\t\tif(\$updateData){";
// 					$after_update .="\r\n\t\t	foreach(\$updateData as \$k=>\$v){";
// 					$after_update .="\r\n\t\t		\$model = M(\$datatablefiexname.\$k);";
// 					$after_update .="\r\n\t\t		foreach(\$v as \$key=>\$val){";
// 					$after_update .="\r\n\t\t			\$model->save(\$val);";
// 					$after_update .="\r\n\t\t		}";
// 					$after_update .="\r\n\t\t	}";
// 					$after_update .="\r\n\t\t}";
					$after_update .=$this->updateDatatableCode();
					$is_create_modify_child = true;
				}

				if(!$is_create_insert_child){
// 					$after_insert.="\r\n\t\t// 内嵌表数据添加处理";
					//$after_insert.="\r\n\t\t\$datatablefiexname =\"{$this->tableName}_sub_\";";
// 					$after_insert.="\r\n\t\t\$insertData = array();// 数据添加缓存集合";
// 					$after_insert.="\r\n\t\tif(\$_POST['datatable']){";
// 					$after_insert.="\r\n\t\t	foreach(\$_POST['datatable'] as \$key=>\$val){";
// 					$after_insert.="\r\n\t\t		foreach(\$val as \$k=>\$v){";
// 					$after_insert.="\r\n\t\t			\$insertData[\$k][]=\$v;";
// 					$after_insert.="\r\n\t\t		}";
// 					$after_insert.="\r\n\t\t	}";
// 					$after_insert.="\r\n\t\t}";
// 					$after_insert.="\r\n\t\t//数据处理";
// 					$after_insert.="\r\n\t\tif(\$insertData){";
// 					$after_insert.="\r\n\t\t	foreach(\$insertData as \$k=>\$v){";
// 					$after_insert.="\r\n\t\t		\$model = M(\$datatablefiexname.\$k);";
// 					$after_insert.="\r\n\t\t		foreach(\$v as \$key=>\$val){";
// 					$after_insert.="\r\n\t\t			\$val['masid'] = \$id;";
// 					$after_insert.="\r\n\t\t			\$model->add(\$val);";
// 					$after_insert.="\r\n\t\t		}";
// 					$after_insert.="\r\n\t\t	}";
// 					$after_insert.="\r\n\t\t}";

					$after_insert .=$this->insertDatatableCode();
					$is_create_insert_child = true;
				}
			}

			if($v[$property['catalog']['name']] == 'areainfo' && $isArea==false){
				$after_edit .=$this->getAreaInfoCode();
				$isArea==true;
			}
			if($v[$property['subimporttableobj']['name']] ){
				// 普通的绑定表
// 				$condition = '';
// 				if($v[$property['conditions']['name']]){
// 					$condition = "->where('status=1 and ".html_entity_decode($v[$property['conditions']['name']])."')";
// 				}else {
// 					$condition = "->where('status=1')";
// 				}

// 				$before_add.= "\r\n\t\t\$model=M(\"".$v[$property['subimporttableobj']['name']]."\");";
// 				$before_add.= "\r\n\t\t\$list{$v[$property['fields']['name']]} =\$model{$condition}->field(\"".$v[$property['subimporttablefieldobj']['name']].",".$v[$property['subimporttablefield2obj']['name']]."\")->select();";
// 				$before_add.="\r\n\t\t\$this->assign(\"".$v[$property['fields']['name']]."list\"".",\$list{$v[$property['fields']['name']]});";

// 				$after_edit .="\r\n\t\t\$model=M(\"{$v[$property['subimporttableobj']['name']]}\");";
// 				$after_edit .="\r\n\t\t\$list{$v[$property['fields']['name']]} =\$model{$condition}->field(\"".$v[$property['subimporttablefieldobj']['name']].",".$v[$property['subimporttablefield2obj']['name']]."\")->select();";
// 				$after_edit .="\r\n\t\t\$this->assign(\"".$v[$property['fields']['name']]."list\"".",\$list{$v[$property['fields']['name']]});";
			}elseif($v[$property['treedtable']['name']]){
				// 绑定的是只能选择最后一级选中效果
// 				$before_add.= "\r\n\t\t//添加-只能选择下拉框最后一级的特殊数据获取方式 @date:".Date('y-m-d H:i:s');
// 				$before_add.= "\r\n\t\t\$model=D(\"MisSystemRecursion\");";
// 				$before_add.= "\r\n\t\t\$treeSelect{$v[$property['fields']['name']]}Data = \$model->modelShow('{$v[$property['treedtable']['name']]}' , array('key'=>'id','pkey'=>'{$v[$property['treeparentfield']['name']]}','fields'=>'{$v[$property['treevaluefield']['name']]},{$v[$property['treeshowfield']['name']]}') , 0 , 1);";
// 				$before_add.= "\r\n\t\t\$this->assign(\"treeSelect{$v[$property['fields']['name']]}Data\",\$treeSelect{$v[$property['fields']['name']]}Data);";

// 				$after_edit.= "\r\n\t\t//修改-只能选择下拉框最后一级的特殊数据获取方式 @date:".Date('y-m-d H:i:s');
// 				$after_edit.= "\r\n\t\t\$model=D(\"MisSystemRecursion\");";
// 				$after_edit.= "\r\n\t\t\$treeSelect{$v[$property['fields']['name']]}Data = \$model->modelShow('{$v[$property['treedtable']['name']]}' , array('key'=>'id','pkey'=>'{$v[$property['treeparentfield']['name']]}','fields'=>'{$v[$property['treevaluefield']['name']]},{$v[$property['treeshowfield']['name']]}') , 0 , 1);";
// 				$after_edit.= "\r\n\t\t\$this->assign(\"treeSelect{$v[$property['fields']['name']]}Data\",\$treeSelect{$v[$property['fields']['name']]}Data);";
					
			}else{
				// 绑定的是selectlist数据源
				if($v[$property['showoption']['name']]){
// 					if(!$is_include_model){
// 						$model_code = "\r\n\t\t\$model=D('Selectlist');";
// 						$is_include_model = true;
// 					}
// 					$after_edit .=$model_code. "\r\n\t\t\$selectlis = \$model->GetRules('{$v['showoption']}');";
// 					$after_edit .="\r\n\t\t\$selectlist{$v[$property['fields']['name']]}=array();";
// 					$after_edit .="\r\n\t\tforeach(\$selectlis['{$v['showoption']}'] as \$k=>\$v){";
// 					$after_edit .="\r\n\t\t\t\$temp['key']=\$k;";
// 					$after_edit .="\r\n\t\t\t\$temp['val']=\$v;";
// 					$after_edit .="\r\n\t\t\t\$selectlist{$v[$property['fields']['name']]}[]=\$temp;";
// 					$after_edit .="\r\n\t\t}";
// 					$after_edit .="\r\n\t\t\$this->assign(\"selectlist{$v[$property['fields']['name']]}\",\$selectlist{$v[$property['fields']['name']]});";

// 					$before_add .=$model_code."\r\n\t\t\$selectlis = \$model->GetRules('{$v['showoption']}');";
// 					$before_add .="\r\n\t\t\$selectlist{$v[$property['fields']['name']]}=array();";
// 					$before_add .="\r\n\t\tforeach(\$selectlis['{$v['showoption']}'] as \$k=>\$v){";
// 					$before_add .="\r\n\t\t\t\$temp['key']=\$k;";
// 					$before_add .="\r\n\t\t\t\$temp['val']=\$v;";
// 					$before_add .="\r\n\t\t\t\$selectlist{$v[$property['fields']['name']]}[]=\$temp;";
// 					$before_add .="\r\n\t\t}";
// 					$before_add .="\r\n\t\t\$this->assign(\"selectlist{$v[$property['fields']['name']]}\",\$selectlist{$v[$property['fields']['name']]});";
				}
			}
		}

		$before_edit .="\r\n\t\t\$mainTab = '{$this->tableName}';";
		$before_edit .="\r\n\t\t//获取当前控制器名称";
		$before_edit .="\r\n\t\t\$name=\$this->getActionName();";
		$before_edit .="\r\n\t\t\$model = D(\"{$this->nodeName}View\");";
		$before_edit .="\r\n\t\t//获取当前主键";
		$before_edit .="\r\n\t\t\$map[\$mainTab.'.id']=\$_REQUEST['id'];";
		$before_edit .="\r\n\t\t\$vo = \$model->where(\$map)->find();";
		$datetiem = date('Y-m-d H:i:s');
		$before_insert .=<<<EOF
		
		// 插入之前，验证编码方案是否正确符合规定 @{$datetiem}
		\$name = \$this->getActionName();
		\$orderno = \$_POST['orderno'];
		\$MisSystemOrdernoDao = D("MisSystemOrderno");
		\$data = \$MisSystemOrdernoDao->validateOrderno(\$name,\$orderno);
		if(\$data['result']){
			\$_POST['parentid'] = \$data['parentid'];
			
		}else{
			\$this->error(\$data['altMsg']);
		}
EOF;
		$before_update .=<<<EOF
		
		// 修改之前，验证编码方案是否正确符合规定 @{$datetiem}
		\$name = \$this->getActionName();
		\$orderno = \$_POST['orderno'];
		\$MisSystemOrdernoDao = D("MisSystemOrderno");
		\$data = \$MisSystemOrdernoDao->validateOrderno(\$name,\$orderno,\$_POST['id']);
		if(\$data['result']){
			\$_POST['parentid'] = \$data['parentid'];
		}else{
			\$this->error(\$data['altMsg']);
		}
EOF;


		$before_edit .="\r\n\t\t\$this->assign( 'vo', \$vo );";

		$before_index .="\r\n\t\t\$this->_extend_before_index();";
		$before_index .="\r\n\t}";

		$before_edit.="\r\n\t\t\$this->_extend_before_edit();";
		$before_edit.="\r\n\t}";
		$before_insert.="\r\n\t\t\$this->_extend_before_insert();";
		$before_insert.="\r\n\t}";
		$before_update.="\r\n\t\t\$this->_extend_before_update();";
		$before_update.="\r\n\t}";

		$after_edit.="\r\n\t\t\$this->_extend_after_edit(\$vo);";
		$after_edit .="\r\n\t}";
		$after_list.="\r\n\t\t\$this->_extend_after_list();";
		$after_list .="\r\n\t}";
		$after_insert.="\r\n\t\t\$this->_extend_after_insert(\$id);";
		$after_insert .="\r\n\t}";
		$before_add.="\r\n\t\t\$this->_extend_before_add(\$vo);";
		$before_add .="\r\n\t}";
		$after_update.="\r\n\t\t\$this->_extend_after_update();";
		$after_update .="\r\n\t}";

		$before_delete .="\r\n\t\t\$this->_extend_before_delete();";
		$before_delete .="\r\n\t}";

		$phpcode.=$index.$edit.$before_index.$before_edit.$before_insert.$before_update.$before_delete.$after_edit.$after_insert.$before_add.$after_update.$delChildData;
		$phpcode.="\r\n}\r\n?>";
		$extendphpcode.="\r\n}\r\n?>";
		if(!is_dir(dirname($actionPath))) mk_dir(dirname($actionPath),0777);
		if( false === file_put_contents( $actionPath , $phpcode )){
			$this->error ("Action文件生成失败!");
		}
		if($isinitExtendAction){
			if( false === file_put_contents( $actionExtendPath , $extendphpcode )){
				$this->error ("扩展Action文件生成失败!");
			}
		}else{
			if(false == (file_exists($actionExtendPath))){
				if( false === file_put_contents( $actionExtendPath , $extendphpcode )){
					$this->error ("扩展Action文件生成失败!");
				}
			}
		}
	}

	/**
	 * @Title: creteCoverFormExtendAction
	 * @Description: todo(生成套表的扩展Action代码)
	 * @param string $cotrollname	Action名称
	 * @param boolean $isaudit		是否审批 default:false
	 * @param boolean $isinitExtendAction
	 * @author quqiang
	 * @date 2014-12-18 上午06:19:04
	 * @throws
	 */
	function creteCoverFormExtendAction($cotrollname , $isaudit =false, $isinitExtendAction=false){
		if(!$cotrollname){
			$this->error('扩展Action名为空:'.$cotrollname);
		}
		$extendphpcode .="<?php\r\n/**";
		$extendphpcode .="\r\n * @Title: {$cotrollname}Action";
		$extendphpcode .="\r\n * @Package package_name";
		$extendphpcode .= "\r\n * @Description: todo(动态表单_扩展类。本类为用户代码注入入口，系统一旦生成将不再重复生成。";
		$extendphpcode .= "\r * \t\t\t\t\t\t但当用户选为组合表单方案后会更新该文件，请做好备份)";
		$extendphpcode .="\r\n * @author ".$_SESSION['loginUserName'];
		$extendphpcode .="\r\n * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d";
		$extendphpcode .="\r\n * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d";
		$extendphpcode .="\r\n * @date ".date('Y-m-d H:i:s');
		$extendphpcode .="\r\n * @version V1.0";
		$extendphpcode .="\r\n*/";
		$extendphpcode .="\r\nclass ";
		if( $isaudit ){
			$jcc="CommonAuditAction";
		}else{
			$jcc="CommonAction";
		}
		$extendphpcode .=$cotrollname."ExtendAction extends ".$jcc." {\r\n";
		$extendphpcode .="\tpublic function _extend_filter(&\$map){";//start filter
		$extendphpcode .="\r\n\t}";
		$extendphpcode .="\r\n\t/**";
		$extendphpcode .="\r\n\t * @Title: _extend_before_index";
		$extendphpcode .="\r\n\t * @Description: todo(扩展前置index函数)";
		$extendphpcode .="\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .="\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .="\r\n\t * @throws ";
		$extendphpcode .="\r\n\t*/";
		$extendphpcode .="\r\n\tfunction _extend_before_index() {";
		$extendphpcode .="\r\n\t}";

		$extendphpcode .="\r\n\t/**";
		$extendphpcode .="\r\n\t * @Title: _extend_before_edit";
		$extendphpcode .="\r\n\t * @Description: todo(扩展的前置编辑函数)";
		$extendphpcode .="\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .="\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .="\r\n\t * @throws ";
		$extendphpcode .="\r\n\t*/";
		$extendphpcode .="\r\n\tfunction _extend_before_edit(){";
		$extendphpcode.="\r\n\t}";
		$extendphpcode.="\r\n\t/**";
		$extendphpcode.="\r\n\t * @Title: _extend_before_insert";
		$extendphpcode.="\r\n\t * @Description: todo(扩展的前置添加函数)";
		$extendphpcode.="\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode.="\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode.="\r\n\t * @throws ";
		$extendphpcode.="\r\n\t*/";
		$extendphpcode.="\r\n\tfunction _extend_before_insert(){";
		$extendphpcode.="\r\n\t}";
		$extendphpcode .= "\r\n\t/**";
		$extendphpcode .= "\r\n\t * @Title: _extend_before_update";
		$extendphpcode .= "\r\n\t * @Description: todo(扩展前置修改函数)  ";
		$extendphpcode .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .= "\r\n\t * @throws";
		$extendphpcode .= "\r\n\t*/";
		$extendphpcode .= "\r\n\tfunction _extend_before_update(){";
		$extendphpcode .="\r\n\t}";

		$extendphpcode .="\r\n\t/**";
		$extendphpcode .="\r\n\t * @Title: _extend_after_edit";
		$extendphpcode .="\r\n\t * @Description: todo(扩展后置编辑函数)";
		$extendphpcode .="\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .="\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .="\r\n\t * @throws ";
		$extendphpcode .="\r\n\t*/";
		$extendphpcode .="\r\n\tfunction _extend_after_edit(\$vo){";
		$extendphpcode .="\r\n\t\tif(\$_GET['viewtype']=='view'){";
		$extendphpcode .="\r\n\t\t\t\$this->display('formview');";
		$extendphpcode .="\r\n\t\t\texit;";
		$extendphpcode .="\r\n\t\t}";
		$extendphpcode .=<<<EOF
		
EOF;
		$extendphpcode .="\r\n\t}";

		$extendphpcode.="\r\n\t/**";
		$extendphpcode.="\r\n\t * @Title: _extend_after_list";
		$extendphpcode.="\r\n\t * @Description: todo(扩展前置List)";
		$extendphpcode.="\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode.="\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode.="\r\n\t * @throws ";
		$extendphpcode.="\r\n\t*/";
		$extendphpcode.="\r\n\tfunction _extend_after_list(){";
		$extendphpcode .="\r\n\t}";

		$extendphpcode .= "\r\n\t/**";
		$extendphpcode .= "\r\n\t * @Title: _extend_after_insert";
		$extendphpcode .= "\r\n\t * @Description: todo(扩展后置insert函数)  ";
		$extendphpcode .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .= "\r\n\t * @throws";
		$extendphpcode .= "\r\n\t*/";
		$extendphpcode .= "\r\n\tfunction _extend_after_insert(\$id){";
		$extendphpcode .= "\r\n\t\$this->success ( L ( '_SUCCESS_' ), '', array (";
		$extendphpcode .= "\r\n\t\t		'id' => \$id,";
		$extendphpcode .= "\r\n\t));";
		$extendphpcode .="\r\n\t}";

		$extendphpcode .= "\r\n\t/**";
		$extendphpcode .= "\r\n\t * @Title: _extend_before_add";
		$extendphpcode .= "\r\n\t * @Description: todo(扩展前置add函数)  ";
		$extendphpcode .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .= "\r\n\t * @throws";
		$extendphpcode .= "\r\n\t*/";
		$extendphpcode .= "\r\n\tfunction _extend_before_add(\$vo){";
		$extendphpcode .= "\r\n\t\t\$MisAutoBindSettableList=\$this->getBindSetTable();";
		$extendphpcode .= "\r\n\t\t\$this->assign(\"MisAutoBindSettableList\",\$MisAutoBindSettableList);"; 
		$extendphpcode .="\r\n\t\t\$this->getFormIndexLoad(\$vo);";
		$extendphpcode .="\r\n\t}";

		$extendphpcode .=<<<EOF

		private function getBindSetTable(){
			\$name=\$this->getActionName();
			//获取当前控制器中文名称
			\$actiontitle=getFieldBy(\$name,"actionname","actiontitle", "mis_dynamic_form_manage");
			\$this->assign("actiontitle",\$actiontitle);
			//查询绑定关系表
			\$MisAutoBindSettableModel=D("MisAutoBindSettable");
			\$map=array();
			\$map['status']=1;
			//查询当前模型绑定字段
			\$map['bindmodelname']=\$name;
			\$MisAutoBindSettableList=\$MisAutoBindSettableModel->where(\$map)->select();
			return \$MisAutoBindSettableList;
		}
EOF;
		$extendphpcode .= "\r\n\t/**";
		$extendphpcode .= "\r\n\t * @Title: _extend_after_update";
		$extendphpcode .= "\r\n\t * @Description: todo(扩展后置update函数)  ";
		$extendphpcode .= "\r\n\t * @author ".$_SESSION['loginUserName'];
		$extendphpcode .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$extendphpcode .= "\r\n\t * @throws";
		$extendphpcode .= "\r\n\t*/";
		$extendphpcode .= "\r\n\tfunction _extend_after_update(){";
		$extendphpcode .="\r\n\t}";

		$extendphpcode .="\r\n\tfunction miniindex(){";
		$extendphpcode .="\r\n\t\$this->getMiniIndexLoad();";
		$extendphpcode .="\r\n\t	parent::index();";
		$extendphpcode .="\r\n\t	//\$this->display();";
		$extendphpcode .="\r\n\t}"; 

		$extendphpcode.="\r\n}\r\n?>";
		
		$actionExtendPath = LIB_PATH."Action/".$cotrollname."ExtendAction.class.php";
		if(!is_writeable($actionExtendPath)){
			chmod($actionExtendPath,0755);
		}
		
		if( false === file_put_contents( $actionExtendPath , $extendphpcode )){
			logs('套表扩展Action生成失败。'.$actionExtendPath);
			$this->error ("扩展Action文件生成失败!");
		}
	}
	
	/**
	 * 
	 * @Title: updateDatatableCode
	 * @Description: todo(生成修改时的数据表格处理代码) 
	 * @return string  
	 * @author quqiang 
	 * @date 2015年1月11日 下午5:29:40 
	 * @throws
	 */
	private function updateDatatableCode(){
		$after_update =<<<EOF
		// 内嵌表数据添加处理
				
		\$datatablefiexname ="{$this->tableName}_sub_";
		\$insertData = array();// 数据添加缓存集合
		\$updateData = array();// 数据修改缓存集合
		if(\$_POST['datatable']){
			foreach(\$_POST['datatable'] as \$key=>\$val){
				foreach(\$val as \$k=>\$v){
					if(\$v['id'] || \$_REQUEST[\$datatablefiexname.\$k]){
						\$updateData[\$k][]=\$v;
					}else{
						\$insertData[\$k][]=\$v;
					}
				}
			}
		}
		//数据处理
		if(\$insertData){
			foreach(\$insertData as \$k=>\$v){
				\$nqname = createRealModelName(\$datatablefiexname.\$k);
				\$model = D(\$nqname);
				\$uploadfile = array();
				foreach(\$v as \$key=>\$val){
					if(C('TOKEN_NAME'))
						\$val[C('TOKEN_NAME')]= \$_POST[C('TOKEN_NAME')];
					\$val['masid'] =\$_POST["id"] ;
					\$val = \$model->create(\$val);
					\$insertId = \$model->add(\$val);
					/*
					 * _over_insert 方法，为静默插入生单。
					 */
					\$this->_over_insert(\$nqname, \$insertId);
					//处理内嵌表带附件信息数据
					foreach(\$val as \$kk => \$vv){
						if(is_array(\$vv)){
							\$uploadfile[\$kk.\$key.\$k]["file"] = \$vv;
							\$uploadfile[\$kk.\$key.\$k]["tableid"] = \$_POST["id"];
							\$uploadfile[\$kk.\$key.\$k]["subid"] = \$insertId;
							\$uploadfile[\$kk.\$key.\$k]["tablename"] = createRealModelName(\$datatablefiexname.\$k);
							\$uploadfile[\$kk.\$key.\$k]["fieldname"] = \$kk;
						}
					}
				}
				if(\$uploadfile){
					\$this->DT_swf_upload(\$uploadfile);
				}
			}
		}
		if(\$updateData){
			foreach(\$updateData as \$k=>\$v){
				\$nqname = createRealModelName(\$datatablefiexname.\$k);
				\$model = D(\$nqname);
				\$uploadfile = array();
				foreach(\$v as \$key=>\$val){
					if(C('TOKEN_NAME'))
						\$val[C('TOKEN_NAME')]= \$_POST[C('TOKEN_NAME')];
					\$val = \$model->create(\$val);
					\$model->save(\$val);
					/*
					 * _over_update 方法，为静默插入生单。(修改的原始数据未保存)
					 */
					\$this->_over_update(\$nqname,\$val['id'],2);
					//处理内嵌表带附件信息数据
					foreach(\$val as \$kk => \$vv){
						if(is_array(\$vv)){
							\$uploadfile[\$kk.\$key.\$k]["file"] = \$vv;
							\$uploadfile[\$kk.\$key.\$k]["tableid"] = \$_POST["id"];
							\$uploadfile[\$kk.\$key.\$k]["subid"] = \$val["id"];
							\$uploadfile[\$kk.\$key.\$k]["tablename"] = createRealModelName(\$datatablefiexname.\$k);
							\$uploadfile[\$kk.\$key.\$k]["fieldname"] = \$kk;
						}
					}
				}
				if(\$uploadfile){
					\$this->DT_swf_upload(\$uploadfile);
				}
			}
		}
EOF;
		return $after_update;
	}
	/**
	 * 
	 * @Title: insertDatatableCode
	 * @Description: todo(生成添加时的数据表格处理代码) 
	 * @return string  
	 * @author quqiang 
	 * @date 2015年1月11日 下午5:31:50 
	 * @throws
	 */
	private function insertDatatableCode(){
		$after_insert =<<<EOF
		
		// 内嵌表数据添加处理
				
		\$datatablefiexname ="{$this->tableName}_sub_";
		\$insertData = array();// 数据添加缓存集合
		if(\$_POST['datatable']){
			foreach(\$_POST['datatable'] as \$key=>\$val){
				foreach(\$val as \$k=>\$v){
					\$insertData[\$k][]=\$v;
				}
			}
		}
		//数据处理
		if(\$insertData){
			foreach(\$insertData as \$k=>\$v){
				\$nqname = createRealModelName(\$datatablefiexname.\$k);
				\$model = D(\$nqname);
				\$uploadfile = array();
				foreach(\$v as \$key=>\$val){
					if(C('TOKEN_NAME'))
						\$val[C('TOKEN_NAME')]= \$_POST[C('TOKEN_NAME')];
					\$val['masid'] =\$id ;
					\$val = \$model->create(\$val);
					\$insertId = \$model->add(\$val);
					/*
					 * _over_insert 方法，为静默插入生单。
					 */
					\$this->_over_insert(\$nqname, \$insertId);
					
					//处理内嵌表带附件信息数据
					foreach(\$val as \$kk => \$vv){
						if(is_array(\$vv)){
							\$uploadfile[\$kk.\$key.\$k]["file"] = \$vv;
							\$uploadfile[\$kk.\$key.\$k]["tableid"] = \$id;
							\$uploadfile[\$kk.\$key.\$k]["subid"] = \$insertId;
							\$uploadfile[\$kk.\$key.\$k]["tablename"] = createRealModelName(\$datatablefiexname.\$k);
							\$uploadfile[\$kk.\$key.\$k]["fieldname"] = \$kk;
						}
					}
				}
				if(\$uploadfile){
					\$this->DT_swf_upload(\$uploadfile);
				}
				\$a=\$model->getLastInsID();
				\$_REQUEST[\$datatablefiexname.\$k] = \$a;
			}
		}
EOF;
		return $after_insert;
	}
	/**
	 * 
	 * @Title: dataTableEditCode
	 * @Description: todo(数据表格的修改代码生成) 
	 * @param array $v	当前组件记录数据
	 * @param array $property  当前组件的配置文件内容
	 * @author quqiang 
	 * @date 2015年1月12日 上午11:14:39 
	 * @throws
	 */
	private function dataTableEditCode($v , $property){
		$after_edit ="\r\n\t\t// 内嵌表处理".$v[$property['fields']['name']];
		$after_edit .="\r\n\t\t\$innerTabelObj".$v[$property['fields']['name']]." = M('".$this->tableName.'_sub_'.$v[$property['fields']['name']]."');"; // 内嵌表名 生成规则： 主表名称_sub_组件名称
		$after_edit .="\r\n\t\t\$innerTabelObj".$v[$property['fields']['name']]."Data = \$innerTabelObj".$v[$property['fields']['name']]."->where('masid='.\$vo['id'])->select();";
		$after_edit .="\r\n\t\t//获取当前模型";
		$after_edit .="\r\n\t\t\$name = \$this->getActionName();";
		$after_edit .="\r\n\t\t\$nqname = createRealModelName('".$this->tableName.'_sub_'.$v[$property['fields']['name']]."');";
		$after_edit .="\r\n\t\t//调用内嵌表配置文件";
		$after_edit .="\r\n\t\t\$scdmodel = D('SystemConfigDetail');";
		$after_edit .="\r\n\t\t\$detailList = \$scdmodel->getEmbedDetail(\$name,\$nqname);";
		$after_edit .="\r\n\t\t//处理DECIMAL类型的 小数点后面无效的0";
		$after_edit .="\r\n\t\t\$innerTabelObj".$v[$property['fields']['name']]."Data = \$this->setInvalidZero(\$detailList,\$innerTabelObj".$v[$property['fields']['name']]."Data);";
		$after_edit .="\r\n\t\t\$this->assign(\"innerTabelObj".$v[$property['fields']['name']]."Data\",\$innerTabelObj".$v[$property['fields']['name']]."Data);";
		return $after_edit;
	}
	/**
	 *
	 * @Title: dataTableEditCode
	 * @Description: todo(数据表格的代码生成)
	 * @author quqiang
	 * @date 2015年1月12日 上午11:14:39
	 * @throws
	 */
	private function getDataTableAction(){
		$delChildData = "";
// 		$delChildData .= "\r\n\t/**";
// 		$delChildData .= "\r\n\t * @Title: delsubinfo";
// 		$delChildData .= "\r\n\t * @Description: todo(子表数据删除)  ";
// 		$delChildData .= "\r\n\t * @author ".$_SESSION['loginUserName'];
// 		$delChildData .= "\r\n\t * @date ".date('Y-m-d H:i:s');
// 		$delChildData .= "\r\n\t * @parame \$_POST['table'] 表名";
// 		$delChildData .= "\r\n\t * @parame \$_POST['id'] 数据ID值";
// 		$delChildData .= "\r\n\t * @throws";
// 		$delChildData .= "\r\n\t*/";
// 		$delChildData.="\r\n\tfunction delsubinfo(){";
// 		$delChildData.="\r\n\t	\$table = \$_POST['table'];";
// 		$delChildData.="\r\n\t	\$id = intval(\$_POST['id']);";
// 		$delChildData.="\r\n\t	if(\$table){";
// 		$delChildData.="\r\n\t		\$model = M(\$table);";
// 		$delChildData.="\r\n\t		\$map['id'] = array('eq',\$id);";
// 		$delChildData.="\r\n\t		\$model->where(\$map)->delete();";
// 		$delChildData.="\r\n\t		\$this->success('数据成功删除');";
// 		$delChildData.="\r\n\t	}";
// 		$delChildData.="\r\n\t}";
		$delChildData .= "\r\n\t/**";
		$delChildData .= "\r\n\t * @Title: onesave";
		$delChildData .= "\r\n\t * @Description: todo(子表单条数据保存)  ";
		$delChildData .= "\r\n\t * @author 王昭侠";
		$delChildData .= "\r\n\t * @date ".date('Y-m-d H:i:s');
		$delChildData .= "\r\n\t * @parame \$_POST['table'] 表名";
		$delChildData .= "\r\n\t * @parame \$_POST['id'] 数据ID值";
		$delChildData .= "\r\n\t * @parame \$_POST['masid'] 数据父表ID值";
		$delChildData .= "\r\n\t * @throws";
		$delChildData .= "\r\n\t*/";
		$delChildData.="\r\n\tfunction onesave(){";
		$delChildData.="\r\n\t	\$table = \$_REQUEST['post_table'];";
		$delChildData.="\r\n\t	\$id = intval(\$_REQUEST['post_id']);";
		$delChildData.="\r\n\t	\$masid = intval(\$_REQUEST['post_mas_id']);";
		$delChildData.="\r\n\t	if(\$table && !empty(\$_POST['datatable'])){";
		$delChildData.="\r\n\t		\$model = D(createRealModelName(\$table));";
		$delChildData.="\r\n\t		\$insertData = array();// 数据添加缓存集合";
		$delChildData.="\r\n\t		\$updateData = array();// 数据修改缓存集合";
		$delChildData.="\r\n\t	if(\$_POST['datatable']){";
		$delChildData.="\r\n\t		foreach(\$_POST['datatable'] as \$key=>\$val){";
		$delChildData.="\r\n\t			foreach(\$val as \$k=>\$v){";
		$delChildData.="\r\n\t				if(\$id){";
		$delChildData.="\r\n\t					\$updateData[\$k][]=\$v;";
		$delChildData.="\r\n\t				}else{";
		$delChildData.="\r\n\t					\$v[\"masid\"] = \$masid;";
		$delChildData.="\r\n\t					\$insertData[\$k][]=\$v;";
		$delChildData.="\r\n\t				}";
		$delChildData.="\r\n\t			}";
		$delChildData.="\r\n\t		}";
		$delChildData.="\r\n\t	}";
		$delChildData.="\r\n\t	//数据处理";
		$delChildData.="\r\n\t	if(\$insertData){";
		$delChildData.="\r\n\t		foreach(\$insertData as \$k=>\$v){";
		$delChildData.="\r\n\t			foreach(\$v as \$key=>\$val){";
		$delChildData.="\r\n\t				\$id=\$model->add(\$val);";
		$delChildData.="\r\n\t			}";
		$delChildData.="\r\n\t		}";
		$delChildData.="\r\n\t	}";
		$delChildData.="\r\n\t	if(\$updateData){";
		$delChildData.="\r\n\t		foreach(\$updateData as \$k=>\$v){";
		$delChildData.="\r\n\t			foreach(\$v as \$key=>\$val){";
		$delChildData.="\r\n\t				\$model->save(\$val);";
		$delChildData.="\r\n\t			}";
		$delChildData.="\r\n\t		}";
		$delChildData.="\r\n\t	}";
		$delChildData.="\r\n\t	\$this->success('保存成功',true,\$id);";
		$delChildData.="\r\n\t	}";
		$delChildData.="\r\n\t}";
		
		return $delChildData;
	}
	
	/**
	 * 
	 * @Title: dataTableEditCode
	 * @Description: todo(获取现 可能有的地区信息) 
	 * @author quqiang 
	 * @date 2015年1月12日 上午11:14:39 
	 * @throws
	 */
	private function getAreaInfoCode(){
		//存在areainfo类型组件
		$after_edit  ="\r\n\t\t// 获取现 可能有的地区信息";
		$after_edit .="\r\n\t\t\$areainfoModel = M('MisAddressRecord');";
		$after_edit .="\r\n\t\t\$arreamap['tablename'] = \$this->getActionName();";
		$after_edit .="\r\n\t\t\$arreamap['tableid'] = \$_REQUEST['id'] ;";
		$after_edit .="\r\n\t\t\$areaArr = \$areainfoModel->where(\$arreamap)->select();";
		$after_edit .="\r\n\t\t foreach (\$areaArr as \$key=>\$val){";
		$after_edit .="\r\n\t\t	\$areainfoarry[\$val['fieldname']][]=\$val;";
		$after_edit .="\r\n\t\t}";
		$after_edit .="\r\n\t\t\$this->assign('areainfoarry' , \$areainfoarry);";
		$after_edit = '';
		return $after_edit;
	}
}