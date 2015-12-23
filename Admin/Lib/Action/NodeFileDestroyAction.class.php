<?php
/**
 * @Title: NodeFileDestroyAction
 * @Package package_name
 * @Description: todo(销毁文件-动态表单成套文件销毁)
 * @author quqiang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-11-26 下午06:34:44
 * @version V1.0
 */
class NodeFileDestroyAction extends CommonAction{
	private  $nodeName="";//节点存储的控制器/模型名称
	/**
	 * @Title: _filter
	 * @Description: todo(检索)
	 * @param unknown_type $map
	 * @author laicaixia
	 * @date 2013-5-31 下午5:37:11
	 * @throws
	 */
	public function _filter(&$map){
		if(empty($_POST['search']) && !isset($map['pid']) ) {
			if($_REQUEST['group_id'] || $_REQUEST['level']){
				if($_REQUEST['group_id']) $map['group_id']=$_REQUEST['group_id'];
				if($_REQUEST['level']) $map['level']=$_REQUEST['level'];
			}else {
				$map['pid']	=0;
			}
		}
		//获取父节点ID
		$pid=$_REQUEST['pid']?$_REQUEST['pid']:0;
		//将父节点赋值到$_request中。方便操作按钮toolbar上面取值
		$_REQUEST['pid'] = $pid;

		$this->assign("pid",$pid);
		$node	=	M("Node");
		//TP方法。getById,根据ID查询当前模型的数据
		$node->getById($_REQUEST['pid']);
		$this->assign('type',$node->type); //取type值
		$nodelevel= (2 == $node->type)?  $node->level: $node->level+1;
		$this->assign('level',$nodelevel);
		if($pid){
			$map['pid'] = $pid;
		}
		//获取分组id
		$group_id = $_REQUEST['group_id']?$_REQUEST['group_id']:0;
		//将分组ID赋值到$_request中，方便操作按钮toolbar上面取值
		$_REQUEST['group_id'] = $group_id;
		$this->assign("group_id",$group_id);
		if ($group_id) {
			//查询出组
			$map['group_id'] = $group_id;
		}
	}

	public function index() {
		//获取左侧结构数据
		$nodeModel = D("Node");
		$groupNodelist = $nodeModel->getGroupNodeList();
		$this->assign('typeTree',json_encode($groupNodelist));
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();

		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		//查询模型
		$name = 'NodeView';
		if (! empty ( $name )) {
			$this->_list ( $name, $map ,"sort",'asc');
		}
		//配置列名称
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail("Node");
		if ($detailList) {
			$this->assign ( 'detailList', $detailList );
		}
		//扩展工具栏操作    配置操作按钮
		$toolbarextension = $scdmodel->getDetail("NodeFileDestroy",true,'toolbar');
		if ($toolbarextension) {
			$this->assign ( 'toolbarextension', $toolbarextension );
		}
		if ($_REQUEST['frame']) {
			$this->display ('indexlist');
		} else {
			$this->display ();
		}
	}
	/**
	 * @Title: deleteRoleOpeation,操作  代表4级
	 * @Description: todo(销毁指定模块的role及其关联的access，role_user，role_rolegroup相关数据)
	 * @param string $actionName
	 * @author quqiang
	 * @date 2014-11-26 下午06:36:02
	 * @throws
	 */
	private function deleteRoleOpeation($nodeid){
		$roleModel = M("role");
		$roleUserModel = M("role_user");
		$roleRolegroupModel = M("role_rolegroup");
		$accessModel = M("access");
		//获取权限组信息,对应
		$role_list = $roleModel->where('nodeid = ' . $nodeid)->select();
		if ($role_list) {
			//存在权限组
			$roleArr=array();
			foreach ($role_list as $val) {
				array_push($roleArr,$val['id']);
			}
			$roleList=implode(",",$roleArr);
			$map="";
			$map['role_id']=array('in',$roleList);
			//删除 access 数据
			$result = $accessModel->where($map)->delete();
			//删除 权限组与用户 数据
			$result = $roleUserModel->where($map)->delete();
			//删除 权限组与高级权限组  数据
			$result = $roleRolegroupModel->where($map)->delete();
			$map="";
			$map['id']=array('in',$roleList);
			//删除 权限组 数据
			$result = $roleModel->where($map)->delete();
		}
		//再次删除余留数据
		$accessModel->where('node_id = '. $_REQUEST['id'])->delete();
	}

	/**
	 * @Title: deleteRoleModel,模块  代表3级
	 * @Description: todo(销毁指定模块的role及其关联的access，role_user，role_rolegroup相关数据)
	 * @param string $actionName
	 * @author quqiang
	 * @date 2014-11-26 下午06:36:02
	 * @throws
	 */
	private function deleteRoleModel($nodepid){
		$roleModel = M("role");
		$roleUserModel = M("role_user");
		$roleRolegroupModel = M("role_rolegroup");
		$accessModel = M("access");
		//获取权限组信息,对应
		$role_list = $roleModel->where('nodepid = ' . $nodepid)->select();
		if ($role_list) {
			//存在权限组
			foreach ($role_list as $val) {
				//存在权限组
				$roleArr=array();
				foreach ($role_list as $val) {
					array_push($roleArr,$val['id']);
				}
				$roleList=implode(",",$roleArr);
				$map="";
				$map['role_id']=array('in',$roleList);
				//删除 access 数据
				$result = $accessModel->where($map)->delete();
				//删除 权限组与用户 数据
				$result = $roleUserModel->where($map)->delete();
				//删除 权限组与高级权限组  数据
				$result = $roleRolegroupModel->where($map)->delete();
				$map="";
				$map['id']=array('in',$roleList);
				//删除 权限组 数据
				$result = $roleModel->where($map)->delete();
			}
		}
		//再次删除余留数据
		$accessModel->where('pid = '. $nodepid)->delete();
	}

	/**
	 * @Title: deleteNode
	 * @Description: todo(删除node节点及其子节点信息，模块可以连操作一起删除，面板，空面板必须没有绑定模块才能删除)
	 * @param string $actionName
	 * @author quqiang
	 * @date 2014-11-26 下午06:36:02
	 * @throws
	 */
	private function deleteNode(){
		$nodeModel = M("node");
		//获取节点组信息
		$nodeInfo = $nodeModel->where('id = ' . $_REQUEST['id'])->find();
		if($nodeInfo){
			//模块及操作时，直接进行删除
			switch($nodeInfo['level']){
				//项目节点
				case "0":
					echo "清理失败，此级不允许删除！";
					exit;
					break;
					//面板
				case "1":
					$nodeList = $nodeModel->where('pid = ' . $_REQUEST['id'])->select();
					if(count($nodeList)>0){
						$this->error("清理失败，还有模块未被删除或移走！");
						exit;
					}else{
						//直接删除该node节点
						$result = $nodeModel->where('id = '. $_REQUEST['id'])->delete();
					}
					break;
					//空模块
				case "2":
					$nodeList = $nodeModel->where('pid = ' . $_REQUEST['id'])->select();
					if(count($nodeList)>0){
						$this->error("清理失败，还有模块未被删除或移走！");
						exit;
					}else{
						//直接删除该node节点
						$result = $nodeModel->where('id = '. $_REQUEST['id'])->delete();
					}
					break;
					//模块
				case "3":
					$this->nodeName=$nodeInfo['name'];
					//删除关联的权限信息
					$this->deleteRoleModel($_REQUEST['id']);
					//直接删除该node节点
					$result = $nodeModel->where('id = '. $_REQUEST['id'])->delete();
					//删除该节点的操作
					$result = $nodeModel->where('pid = '. $_REQUEST['id'])->delete();
					break;
					//操作
				case "4":
					//删除关联的权限信息
					$this->deleteRoleOpeation($_REQUEST['id']);
					//直接删除该node节点
					$result = $nodeModel->where('id = '. $_REQUEST['id'])->delete();
					break;
					 
			}
		}else{
			$this->error("清理失败，本节点已经被清理过！");
			exit;
		}
		 
	}


	public  function  delete(){

		$this->deleteNode();
		// 		$this->success("清理成功！");
		if(!empty($this->nodeName)){
			$this->destroy($this->nodeName);
		}
		$this->success("清理成功！");
	}

	/**
	 * @Title: destroy
	 * @Description: todo(销毁指定Action名称的所有文件)
	 * @param string $actionName
	 * @author quqiang
	 * @date 2014-11-26 下午06:36:02
	 * @throws
	 */
	protected function destroy($actionName){
		// 日志存储位置
		$logsName='destriylogs_'.date('Ymd');
		$tableName='';
		/**
		 * 动态表单删除文件流程：
		 * 1：销毁action、model、view、actionExtend、modelExtend、模板文件
		 * 2：销毁动态配置下的Models项、Sytem下的listInc
		 * 3：动态表单的组件配置文件，动态表单的字段记录信息(MisAutoAnameList.inc.php)，
		 * 4：清DB数据 mis_auto_primary_mas 表 mis_auto_primary_sub
		 * 5：表单(mis_dynamic_form_manage) 及 表单下的字段记录(mis_dynamic_form_field)
		 */

		// 检查Action是否存在
		$actionFileName = LIB_PATH."Action/".$actionName."Action.class.php";
		if(!file_exists($actionFileName)){
			logs("用户指定删除Action:{$actionName}不存在",$logsName);
		}
		// 得到当前action的表名。
		$curModel = D($actionName);
		// 主表名称
		$tableName = $curModel->getTableName();
		//
		logs($tableName , $logsName);
		// 得到主表在表单记录中的Id然后找到所有datatable

		/**
		 * 构建删除文件列表
		 * 1.Action , Model , View , ActionExtend , ModelExtend , Template
		 * 2.销毁动态配置
		*/

		/**
		 * 单文件删除列表
		 * @var unknown_type
		*/
		$destriyFileList =array();

		/**
		 * 文件夹删除列表
		 * @var unknown_type
		*/
		$destriyFolderList =array();
		// 1
		// action , actionExtend
		$destriyFileList[]=  LIB_PATH."Action/".$actionName."Action.class.php";
		$destriyFileList[]=  LIB_PATH."Action/".$actionName."ExtendAction.class.php";
		// Model , ModelExtend , View
		$destriyFileList[]=  LIB_PATH."Model/".$actionName."Model.class.php";
		$destriyFileList[]=  LIB_PATH."Model/".$actionName."ExtendModel.class.php";
		$destriyFileList[]=  LIB_PATH."Model/".$actionName."ViewModel.class.php";
		// 模板文件目录
		$destriyFolderList[] = TMPL_PATH.C('DEFAULT_THEME').'/'.$actionName;
		// 2
		// Models , 组件配置文件
		$dir = '/autoformconfig/';
		$destriyFolderList[] =C('DYNAMIC_PATH').'/Models/'.$actionName;
		$destriyFileList[] = C('DYNAMIC_PATH').'/autoformconfig/'.$actionName.'.php';
		/**
		 * 删除表记录
		 * 1.删除模板
		 * 1.删除组件表propery
		 * 2.删除sub表字段记录
		 * 3.删除mas表 
		 * 4.内嵌表格删除
		 * 
		 */
		$formid=getFieldBy($actionName, "actionname", "id", "mis_dynamic_form_manage");
		
		//删除mas表
		$deltemp="DELETE FROM mis_dynamic_form_template WHERE formid={$formid}";
		//删除组件记录表
		$delpropery="DELETE FROM mis_dynamic_form_propery WHERE formid={$formid}";
		//查找当前表单主表
		$tablename=getFieldBy($formid, "formid", "tablename", "mis_dynamic_database_mas","isprimary","1");
		//查询表是否被复用 
		$ischoise=getFieldBy($tablename, "tablename", "tablename", "mis_dynamic_database_mas","ischoise","1");
		//丢弃表SQL
		$droptable="";
		if(!$ischoise){
			//删除mas表记录
			$delMas="DELETE FROM mis_dynamic_database_mas WHERE formid={$formid}";
			//丢弃表
			$droptable="drop table ".$tablename;
			//删除sub表记录
			$delSub="DELETE FROM mis_dynamic_database_sub WHERE formid={$formid}";
		}
		// 4.删除数据表。
		$getDataTableSql="SELECT
		CONCAT(tablename, '_sub_', `FIELD`) AS datatablename
		FROM
		mis_dynamic_database_sub AS sub,
		(SELECT
		tablename
		FROM
		mis_dynamic_database_mas
		WHERE id IN
		(SELECT
		masid
		FROM
		mis_dynamic_database_sub
		WHERE formid = {$formid}
		AND category = 'datatable')) AS tablename
		WHERE sub.formid = {$formid}
		AND sub.category = 'datatable' ";
		/**
		 * 删除数据表，
		 *  先得到所有数据表格记录
		 * 1。删除字段记录表
		 * 2。删除内嵌表格的表
		 * 3. 删除表单记录
		 * 4. 删除当前使用表
		 */
		//
		
		$delFieldSql="DELETE FROM mis_dynamic_form_field WHERE formid ={$formid}";
		$delDataTableSql="";
		$delFormSql="DELETE FROM mis_dynamic_form_manage WHERE actionname='$actionName'";
		if($tableName){
			$delSql="DELETE FROM $tableName";
		}
		logs('单文件删除列表：'.arr2string($destriyFileList), $logsName);
		logs('文件件删夹除列表：'.arr2string($destriyFolderList), $logsName);
		logs('数据表-字段记录删除：'.$delFieldSql, $logsName);
		logs('数据表-内嵌表删除：'.$delDataTableSql, $logsName);
		logs('数据表-表单记录删除：'.$delFormSql, $logsName);
          
		
		
		
		/**
		 * 真实删除
		*/

		foreach ($destriyFileList as $k=>$v){
			$ret = unlink($v);
			logs("文件 $v 删除".($ret?'成功':'失败') , $logsName);
		}
		foreach ($destriyFolderList as $k=>$v){
			$ret = deldir($v);
			logs("文件夹 $v 删除".($ret?'成功':'失败') , $logsName);
		}

		$modelObj = M();
		$fieldDelRet = $modelObj->query($delFieldSql);
		logs("组件字段记录 $delFieldSql  删除  ".($fieldDelRet?'失败':'成功') , $logsName);
		$formDelRet = $modelObj->query($delFormSql);
		logs("表单记录  $delFormSql  删除 ".($formDelRet?'失败':'成功') , $logsName);
		if($delSql){
			$delRet = $modelObj->query($delSql);
			logs("表单记录  $delFormSql  删除 ".($delRet?'失败':'成功') , $logsName);
		}else{
			logs("表 $tableName  不存在" , $logsName);
		}
		// 删除数据表
		$dataTableList = $modelObj->query($getDataTableSql);
		logs("删除数据表SQL  $getDataTableSql " , $logsName);
		if($dataTableList){
			foreach ($dataTableList as $k=>$v){
				$delDataTableSql = "DROP TABLE {$v['datatablename']}";
				// 删除数据表
				$tempDelRet = $modelObj->query($delDataTableSql);
				logs("删除数据表  $delDataTableSql  删除 ".($tempDelRet?'失败':'成功') , $logsName);
			}
		}
		
		//删除模板
		$tempDelRet = $modelObj->query($deltemp);
		logs("删除模板  $deltemp  删除 ".($tempDelRet?'失败':'成功') , $logsName);
		//删除组件
		$properyDelRet = $modelObj->query($delpropery);
		logs("删除组件  $delpropery  删除 ".($properyDelRet?'失败':'成功') , $logsName);
		//删除sub表
		$subResult= $modelObj->query($delSub);
		logs("删除sub表  $delSub  删除 ".($subResult?'失败':'成功') , $logsName);
		//删除mas表记录
		$masResult= $modelObj->query($delMas);
		logs("删除mas表  $delMas  删除 ".($masResult?'失败':'成功') , $logsName);
		$modelObj->commit();
	}
}