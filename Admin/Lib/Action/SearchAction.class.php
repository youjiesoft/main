<?php
/**
 * @Title: file_name
 * @Package package_name
 * @Description: todo(动态搜索配置控制器)
 * @author arrowing
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-6-1 上午10:02:25
 * @version V1.0
 */
class SearchAction extends CommonAction {

	// 数据库表达式
	protected $comparison = array('eq'=>'=','neq'=>'!=','gt'=>'>','egt'=>'>=','lt'=>'<','elt'=>'<=','notlike'=>'NOT LIKE','like'=>'LIKE');

	// 主要查询表
	protected $maintable;

	//@var array
	//key:@var string模型名称
	//value:@ array 模型对应要过滤的表字段
	//如果不知道是表还是视图，请搜索下面信息打开调试
	//解析传来的参数where
	//echo $modelName;
	protected $fifterFields = array(
			//'NodeView'=>array('pid')
	);
	/**
	 * (non-PHPdoc)
	 * @Description: todo(重写父类的新增方法)
	 * @see CommonAction::add()
	 */
	public function add(){
		$this->assign('view', $_REQUEST['view']);
		$model = D('SystemConfigDetail');
		$title = $model->getTitleName($_REQUEST['view']);
		$this->assign('title',$title);
		//$this->assign('models', $this->getAllDynamic());
		$this->display();
	}
	/**
	 * (non-PHPdoc)
	 * @Description: todo(重写父类的编辑方法)
	 * @see CommonAction::edit()
	 */
	public function edit(){
		$view = $_REQUEST['view'];
		$model = D('SystemConfigDetail');
		$name = $model->getTitleName($_REQUEST['view']);
		$this->assign('name',$name);
		$model = D('Search');
		$dmap['dynamicname'] = $view;
		$dynamic = $model->where($dmap)->find();
		$maintable = $dynamic['maintable'];

		// 将所有字段信息放入数组
		$fields = array();
		$field = explode(';;', substr($dynamic['relatefields'], 0, -2));
		foreach($field as $v){
			$fields[] = explode('-', $v);
		}

		// 将所有相关表信息放入数组
		if($dynamic['relatetables'] != ''){
			$table = explode(';', substr($dynamic['relatetables'], 0, -1));
			$num = count($table);
		}else{
			$num = 0;
		}

		if($num >= 1){
			$tables = array();
			$html = '';
			foreach($table as $k => $v){
				// $v 例子 "表1-字段1-表2-字段2"
				$temp = explode('-', $v);
				$sql = "SHOW COLUMNS FROM `{$temp[0]}`";
				$thisfields = $model->query($sql);

				$tables[$k][0] = $temp[0]; // 表1
				$tables[$k][1] = $temp[1]; // 表1字段
				$tables[$k][2] = $temp[2]; // 表2
				$tables[$k][3] = $temp[3]; // 表2字段

				foreach($thisfields as $f){
					$tables[$k][4][] = $f['Field']; // 表1所有字段
				}
			}

			// 主表先列出并禁止使用
			$disabled = " disabled='disabled' ";
			$html .= "<tr>";
			$html .= "<td><input {$disabled} style='width:150px;' onclick='confirmMainTable(this);' type='button' value='{$maintable}'></td>";

			// 主查询表所有字段
			$sql = "SHOW COLUMNS FROM `{$maintable}`";
			$mainfields = $model->query($sql);

			$html .= "<td><select {$disabled}>";
			foreach($mainfields as $field){
				$html .= "<option value='{$field['Field']}'>{$field['Field']}</option>";
			}
			$html .= "</select></td>";

			// 相关表
			$html .= "<td><select {$disabled} onchange='changeReTable(this);'>";
			foreach($tables as $re){
				$html .= "<option value='{$re[0]}'>{$re[0]}</option>";
			}
			$html .= "</select></td>";

			// 相关表所有字段
			$html .= "<td><select {$disabled}>";
			foreach($tables[0][4] as $f4){ // $tables[0][4] 第一个相关表字段数组
				$html .= "<option value='{$f4}'>{$f4}</option>";
			}
			$html .= "</select></td>";

			$html .= "</tr>";

			// 循环列出其他表并使其按当前保存的数据显示
			foreach($tables as $val){
				$html .= "<tr>";

				// 构造值为表名的按钮
				$html .= "<td><input style='width:150px;' onclick='confirmMainTable(this);' type='button' value='{$val[0]}'>";

				// 构造第一个表的关联字段
				$html .= "<td><select>";

				// $val[4] 表对应的字段数组
				foreach($val[4] as $fld){
					$sel = '';
					if($val[1] == $fld) $sel = ' selected ';
					$html .= "<option {$sel} value='{$fld}'>{$fld}</option>";
				}
				$html .= "</select></td>";

				// 构造相关表
				$html .= "<td><select onchange='changeReTable(this);'>";

				$sel = '';
				if($val[2] == $maintable) $sel = ' selected ';
				$html .= "<option {$sel} value='{$maintable}'>{$maintable}</option>";

				foreach($tables as $val2){
					if($val[0] != $val2[0]){
						$sel = '';
						if($val2[0] == $val[2]) $sel = ' selected ';
						$html .= "<option {$sel} value='{$val2[0]}'>{$val2[0]}</option>";
					}
				}
				$html .= "</select></td>";

				// 构造相关表关联字段
				$html .= "<td><select>";
				$sql = "SHOW COLUMNS FROM `{$val[2]}`";

				$t2fields = $model->query($sql);
				foreach($t2fields as $fld2){
					$sel = '';
					if($val[3] == $fld2['Field']) $sel = ' selected ';
					$html .= "<option {$sel} value='{$fld2['Field']}'>{$fld2['Field']}</option>";
				}
				$html .= "</select></td>";

				$html .= "</tr>";
			}
			$this->assign('tablehtml', $html);
		}

		$this->assign('id', $id);
		$this->assign('ename', $dynamic['ename']);
		$this->assign('cname', $dynamic['cname']);
		$this->assign('dname', $dynamic['dynamicname']);
		$this->assign('maintable', $maintable);
		$this->assign('condition', $dynamic['condition']);
		$this->assign('sortjson', $dynamic['sortjson']);
		$this->assign('remark', $dynamic['remark']);

		$this->assign('models', $this->getAllDynamic());
		$this->assign('fields', $fields);
		$this->assign('num', $num);
		$this->display();
	}
	/**
	 * @Title: parseSortJson 
	 * @Description: todo(一个公用方法)   
	 * @author liminggang 
	 * @date 2013-6-1 上午10:15:59 
	 * @throws
	 */
	private function parseSortJson(){
		if(trim($_POST['sortjson'] != '')){
			$arraySortJson = explode(',', $_POST['sortjson']);
			$result = array();
			foreach($arraySortJson as $k => $sortField){
				$tmp = explode(':', $sortField);
				$result[] = '"'.$tmp[0].'":"'.$k.'", "'.$k.'": "'.$tmp[1].'"';
			}
			$_POST['sortjson'] = '{'.implode(',', $result).'}';
			//print_r($_POST['sortjson']);
		}
	}
	/**
	 * @Title: _before_update 
	 * @Description: todo(修改之前的操作方法)   
	 * @author liminggang 
	 * @date 2013-6-1 上午10:19:25 
	 * @throws
	 */
	public function _before_update(){
		$this->parseSortJson();
		$this->setSearchInc($_POST['id']);
	}
	/**
	 * @Title: insert 
	 * @Description: todo(保存搜索条目模板)   
	 * @author liminggang 
	 * @date 2013-6-1 上午11:00:37 
	 * @throws
	 */
	public function insert(){
		$model = D ('Search');
		$result = $model->where("ename = '{$_POST['ename']}'")->find();
		if($result){
			$this->error ( '第三步中的英文名已存在，请修改再保存' );
		}

		$this->parseSortJson();

		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}

		//保存当前数据对象
		$list=$model->add ();
		if ($list!==false) { //保存成功
			$this->setSearchInc($list);
			$this->success ( L('_SUCCESS_') ,'',$list);
		} else {
			$this->error ( L('_ERROR_') );
		}
	}
	
	/**
	 * @Title: setSearchInc
	 * @Description: todo(设置Search和toolbar配置文件) 
	 * @param ID $list  
	 * @author 杨东 
	 * @date 2013-6-7 上午11:17:26 
	 * @throws 
	*/  
	private function setSearchInc($list){
		$model = D('SystemConfigDetail');
		$modelName = $_POST['dynamicname'];
		if( $_POST['ename'] != ''){
			$model->setDetail($modelName,array('id'=>$list,'ename'=>$_POST['ename'],'cname'=>$_POST['cname']),'search');
			//配置toolbar
			$toolbars = $model->getDetail($modelName,false,'toolbar');
			if($toolbars == '') $toolbars = array();
			$html = '<a class="seaech" href="__APP__/Search/search/ename/'.$modelName.'/model/'.$modelName.'" rel="product_search" target="dialog" mask="true"><span>搜索</span></a>';
			$toollen = count($toolbars);
			if(isset($toolbars['search'])){
				$toolbars['search']['html'] = $html;
			}else{
				$toolbars['search'] = array(
					'ifcheck' => '0',
					'permisname' => '',//strtolower($modelName).'_search'
					'html' => $html,
					'shows' => '1',
					'sortnum' => $toollen + 1,
			);
				$model->setDetail($modelName,$toolbars,'toolbar');
			}
		} else {
			$model->setDetail($modelName,array('id'=>'','ename'=>'','cname'=>''),'search');
			$toolbars = $model->getDetail($modelName,false,'toolbar');
			if(isset($toolbars['search'])){
				unset($toolbars['search']);
			}
			$model->setDetail($modelName,$toolbars,'toolbar');
		}
	}
	/**
	 * @Title: getAllDynamic 
	 * @Description: todo(公共方法，返回模型) 
	 * @return unknown  
	 * @author liminggang 
	 * @date 2013-6-1 上午10:23:08 
	 * @throws
	 */
	private  function getAllDynamic(){
		$dynamic_models = DConfig_PATH . "/Models/";
		$keys = scandir($dynamic_models);

		// 所有文件夹的名称中去掉头两项，即 .(当前文件夹) ..(上一级)
		$keys = array_slice($keys, 2);
		$values = array();

		foreach($keys as $v){
			$model = D('SystemConfigDetail');

			// 获取动态配置对应的中文名称
			$name = $model->getTitleName($v);
			if($name == ''){
				$name = "- 暂无命名 - ({$v}) ";
			}
			$values[] = $name;
		}

		$models = array_combine($keys, $values);
		return $models;
	}
	/**
	 * @Title: confirmSearch 
	 * @Description: todo(列出要查询的动态配置相关数据)   
	 * @author liminggang 
	 * @date 2013-6-1 上午10:31:13 
	 * @throws
	 */
	public function confirmSearch(){
		$filename = "list.inc.php";
		$fields = @require(DConfig_PATH . "/Models/".$_GET['view']."/".$filename);
		$tablename = $this->toDbName($_GET['view']);

		// 动态配置中所有要显示的字段数组
		$showfields = array();

		// 过滤数组：不属于字段的动态配置列
		$filter = array('action');

		foreach($fields as $k => $v){
			if(in_array($v['name'], $filter)){
				continue;
			}

			if($v['shows'] == 1){ // 只要动态配置中列显示的
				if($v['findtype'] == '') $v['findtype'] = 'text';
				if($v['findtable'] == '') $v['findtable'] = $tablename;
				if($v['findfield'] == '') $v['findfield'] = $v['name'];

				$showfields[] = $v;
			}
		}

		echo json_encode($showfields);
	}
	/**
	 * @Title: search 
	 * @Description: todo(根据提交上来的ename参数生成对应的搜索模板)   
	 * @author liminggang 
	 * @date 2013-6-1 上午10:33:25 
	 * @throws
	 */
	public function search(){
		$modelName = $_GET['model'];
		$model = D ('Search');
		$search = $model->where("dynamicname='$modelName'")->find();

		$model = D('SystemConfigDetail');
		$searchs = $model->getDetail($modelName,false);
		$searchfielsd = array();

		foreach($searchs as $sf){
			if($sf['issearch'] == 1){
				$searchfielsd[] = $sf['name'];
			}
		}

		if($search){

			// 生成表单
			$fields = explode(';;', substr($search['relatefields'], 0, -2));
			if(trim($search['sortjson']) != ''){
				$sortjson = json_decode($search['sortjson'], true);
			}

			$arrsySortHtml = array();

			foreach($fields as $k => $v){
				$hide = '';
				$field = explode('-', $v);

				$showfield = $field[0];

				if(!in_array($showfield, $searchfielsd)){
					$hide = ' style="display:none;" ';
				}

				$showname = $field[1];
				$type = $field[2];
				$table = $field[3];
				$findfield = $field[4];

				$name = "{$table}-{$findfield}-{$showfield}-{$type}";

				if($sortjson){
					$sortIndex = $sortjson[$showfield];
				}else{
					$sortIndex = $k;
				}

				$arrsySortHtml[$sortIndex] = "<div {$hide} style='width:539px;' class='unit'><label>{$showname}：</label>".
						$this->baseTypeToHtml($type, $name, $table, $search['condition'])."</div>";
			}

			ksort($arrsySortHtml);
			$html = implode('', $arrsySortHtml);

			// 生成left_join代码
			$joins = array();
			if($search['relatetables']){
				$tables = explode(';', substr($search['relatetables'], 0, -1));

				foreach($tables as $k => $v){
					// $v 例子 "表1-表1字段-表2-表2字段"
					$m = explode('-', $v);
					$joins[] = urlencode(" `{$m[0]}` ON `{$m[0]}`.`{$m[1]}` = `{$m[2]}`.`{$m[3]}` ");
				}

				$join = implode('-', $joins);
			}
			$html .= "<input type='hidden' name='ename' value='{$ename}' />";
			$html .= "<input type='hidden' name='maintable' value='{$search['maintable']}' />";
			$html .= "<input type='hidden' name='join' value='{$join}' />";
			$html .= "<input type='hidden' name='condition' value='{$search['condition']}' />";
			$this->assign('searchhtml', $html);
		}else{
			$this->assign('error', '找不到英文名为 '.$ename.' 的搜索条目!');
		}
		$this->display();
	}
	/**
	 * (non-PHPdoc)
	 * @Description: todo(获取要查询的表所有字段)   
	 * @see CommonAction::getFields()
	 */
	public function getFields(){
		$tables = explode(',', urldecode($_GET['tables']));
		$model = new Model();
		$fields = array();
		$error = array(); //错误的表名数组

		foreach($tables as $k => $v){
			$sql = "SHOW COLUMNS FROM `{$v}`";
			$allFields = $model->query($sql);
			if($allFields === false){
				$error[] = $v;
				continue;
			}
			if(count($error) == 0){
				foreach($allFields as $f){
					$fields[$k][] = $f['Field'];
				}
			}
		}

		if(count($error) > 0){
			$this->error ( '存在错误的表名（'.implode(',', $error).'）， 请修改', '', 0 );
		}else{
			echo json_encode($fields);
		}
	}
	/**
	 * @Title: baseTypeToHtml 
	 * @Description: todo(根据查找类型，生成对应的查找html代码) 
	 * @param varchar $type 查找类型
	 * @param varchar $name 查找组成的字符串 "表名-查找字段-显示字段-查找类型
	 * @param varchar $table 表名
	 * @param varchar $condition 
	 * @param varchar $single 是否为单条件查找 是的话其他表单隐藏
	 * @return string  
	 * @author liminggang 
	 * @date 2013-6-1 上午10:42:16 
	 * @throws
	 */
	private function baseTypeToHtml($type, $name, $table, $condition, $single){
		$html = '';

		//单条件查找 不需要的都隐藏
		$hide = '';
		if($single === true){
			$hide = "display:none;";
		}

		if(strpos($type, ':')){
			$type_tmp = explode(':', $type);
			$type = $type_tmp[0].'2';
			$search_name = $type_tmp[1];
		}

		$search_field = 'name';
		if(strpos($type, '|')){
			$type_tmp = explode('|', $type);
			$type = $type_tmp[0];
			$search_field = $type_tmp[1];
		}

		switch($type){
			case 'text':
				$name = explode('|', $name);
				$html .= "<input style='{$hide}' type='text' maxlength='20' name='$name[0]' />";
				if($name[1] != ''){
					$moresearch = explode(',', $name[1]);
					foreach($moresearch as $type_v){
						$html .= "<input type='hidden' name='textmore-{$name[0]}[]' value='$type_v' />";
					}
				}
				break;
			case 'range':
				// 数组名称加[]
				$html .= "<input style='{$hide}' type='text' maxlength='5' name='{$name}[]' />";
				$html .= "<span style='{$hide}float:left;' >&nbsp;&nbsp;--&nbsp;&nbsp;</span><input style='{$hide}' style='float:right;' type='text' maxlength='5' name='{$name}[]'>";
				break;
			case 'time':
				// 数组名称加[]
				$html .= "<input style='{$hide}' type='text'  class='date textInput readonly valid' name='{$name}[]' />";
				$html .= "<label style='{$hide} width:20px;'>---</label><input style='{$hide}' type='text' style='float:left;'  class='date textInput readonly valid' name='{$name}[]' />";
				break;
			case 'group':
				// select 方式
				$selectname = explode('|', $name);
				$html .= "<div style='{$hide}float:left;' ><SELECT name='{$selectname[0]}' class='combox'>";
				$html .= "<option value='0'>请选择...</option>";
				$group = D($table);
				$map = 'status=1 ';
				if($condition){
					$condition = html_entity_decode($condition);
					$name = explode('-', $name);
					$conditions = explode(',', $condition);
					foreach($conditions as $c_v){
						if(strpos($c_v, $name[0]) !== false){
							$map .= ' AND '.$c_v;
						}
					}
				}

				$list = $group->where($map)->select();
				foreach($list as $k => $v){
					$html .= "<option value='{$v['id']}'>{$v[$search_field]}</option>";
				}
				$html .= "</SELECT></div>";
				break;
			case 'group2':
				// lookup 方式
				$name = explode(':', $name);
				$html .= "<input type='hidden'  name='{$name[0]}' />
				<input style='{$hide}' type='text' class='required textInput' readonly='readonly'  name='dwz.search.name' />
				<a style='{$hide}' class='btnLook' href='__URL__/lookup/search/{$search_name}' lookupgroup='search'>查找带回</a>";
				break;
			case 'checkfor':
				$checkname = explode('|', $name);
				// checkfor 方式
				if($type_tmp[6]){
					$map = "map=".$type_tmp[6];
				}
				$html .= "<input style='{$hide}' class='checkByInput textInput' {$map} show='{$type_tmp[2]}' insert='{$type_tmp[3]}' checkfor='{$type_tmp[1]}' limit='{$type_tmp[4]}' order='{$type_tmp[5]}' />
				<input type='hidden' name='{$checkname[0]}' />";
				break;
			case 'select':
				$name = explode('|', $name);
				$html .= "<div style='{$hide}float:left;' ><SELECT name='{$name[0]}' class='combox'>";
				$html .= "<option value=''>请选择...</option>";

				$select_config = require DConfig_PATH."/System/selectlist.inc.php";
				$select_config = $select_config[$search_field][$search_field];
				if($select_config){
					foreach($select_config as $select_k => $select_v){
						$html .= "<option value='{$select_k}'>{$select_v}</option>";
					}
				}
				$html .= "</SELECT></div>";
				break;
			case 'radio':
				$name = explode('|', $name);
				$select_config = require DConfig_PATH."/System/selectlist.inc.php";
				$select_config = $select_config[$search_field][$search_field];
				if($select_config){
					$html .= "<span style='{$hide}'>";
					$html .= "<input style='display:none;' type='radio' name='{$name[0]}' value='__empty__' checked='checked' />";
					foreach($select_config as $k => $v){
						$html .= "<input type='radio' name='$name[0]' value='$k' /> $v ";
					}
					$html .= "</span>";
				}
				break;
			case 'checkbox':
				$name = explode('|', $name);
				$select_config = require DConfig_PATH."/System/selectlist.inc.php";
				$select_config = $select_config[$search_field][$search_field];
				if($select_config){
					$html .= "<span style='{$hide}'>";
					$html .= "<input style='display:none;' type='checkbox' name='{$name[0]}[]' value='__empty__' checked='checked' />";
					foreach($select_config as $k => $v){
						$html .= "<input type='checkbox' name='{$name[0]}[]' value='$k' /> $v ";
					}
					$html .= "</span>";
				}
				break;
			case 'db':
				$name = explode('|', $name);
				$html .= "<input style='{$hide}' type='text' maxlength='20' name='$name[0]' />";
				$html .= "<input type='hidden' name='db-$name[0]' value='$search_field' />";
				$moresearch = explode(',', $search_field);
				foreach($moresearch as $type_v){
					$html .= "<input type='hidden' name='textmore-{$name[0]}[]' value='$type_v' />";
				}
				break;
		}
		return $html;
	}
	/**
	 * @Title: lookup 
	 * @Description: todo(输出到模板页面)   
	 * @author liminggang 
	 * @date 2013-6-1 上午10:43:26 
	 * @throws
	 */
	public function lookup(){
		$search = $_GET['search'];
		$s_model = D('Search');
		$search_item = $s_model->where("ename='{$search}'")->find();

		if($search_item){
			$dynamic = $search_item['dynamicname'];

			// 获取动态配置的表头，宽度
			$scdmodel = D('SystemConfigDetail');
			$detailList = $scdmodel->getDetail($dynamic);
			if ($detailList) {
				$this->assign ('lookup_list', $detailList );
			}

			if($_SESSION["a"] != 1){
				$map = ' status > -1 ';
			}else{
				$map = ' 1=1 ';
			}
			if($search_item['condition']){
				$table = $search_item['maintable'];
				$condition = html_entity_decode($search_item['condition']);
				$conditions = explode(',', $condition);
				foreach($conditions as $c_v){
					if(strpos($c_v, $table) !== false){
						$map .= ' AND '.$c_v;
					}
				}
			}

			// 获取相关数据列表
			$list = A('Common');
			$list->_list($dynamic, $map); // 根据动态配置名称查询

		}else{
			$this->assign('lookup_error', 1);
		}
		$this->assign('lookup_ename', $search);
		$this->display();
	}
	/**
	 * @Title: getFifterMsg 
	 * @Description: todo(单条件过滤页面生成输出模板)   
	 * @author liminggang 
	 * @date 2013-6-1 上午10:43:50 
	 * @throws
	 */
	public function getFifterMsg(){
		$field = $_GET['field'];
		$ename = $_GET['ename'];
		$isset = false; //是否有配置查找字段

		$showname = '';
		$html = '';
		$join = '';

		$model = D ('Search');
		$result = $model->where("dynamicname = '{$ename}'")->find();
		if($result){

			$tables = explode(';', substr($result['relatetables'], 0, -1));
			if($tables[0] != '' && count($tables) >= 1){
				$joins = array();
				foreach($tables as $k => $v2){
					// $v 例子 "表1-表1字段-表2-表2字段"
					$m = explode('-', $v2);
					$joins[] = urlencode(" `{$m[0]}` ON `{$m[0]}`.`{$m[1]}` = `{$m[2]}`.`{$m[3]}` ");
				}
				$join = implode('-', $joins);
			}

			$fields = explode(';;', substr($result['relatefields'], 0, -2));
			foreach($fields as $v){
				$f = explode('-', $v);

				// 构造查找的条件字段，放于隐藏文本框中
				// 表名-查找字段-显示字段-查找类型
				$name = $f[3]."-".$f[4]."-".$f[0]."-".$f[2];

				// $f[0] 为显示字段
				if($f[0] == $field){
					$showname = $f[1];
					$html .= "<label style='width:80px;'>{$showname}：</label>";
					$html .= $this->baseTypeToHtml($f[2], $name, $f[3], $result['condition']);
					$isset = true;
				}else{
					$html .= $this->baseTypeToHtml($f[2], $name, $f[3], $result['condition'], true);
				}
				$html .= "<input type='hidden' name='condition' value='{$result['condition']}' />";
			}

			if(!$isset){//假如没配置查找字段
				$html = '<span style="color:red;">该字段还未配置查找</span>';
			}

			$this->assign('html', $html);
			$this->assign('ename', $ename);
			$this->assign('join', $join);
			$this->assign('showname', $showname);
			$this->assign('maintable', $result['maintable']);
		}else{
			$this->assign('error', '找不到英文名为 '.$ename.' 的搜索条目!');
		}
		if($_GET['searchtype'] == 2){
			$this->assign('searchtype', 2);
		}else{
			$this->assign('searchtype', '');
		}
		$this->display();
	}
	/**
	 * @Title: addSpecialChar 
	 * @Description: todo(参考THINKPHP的DB类中的方法) 
	 * @param unknown_type $value
	 * @return string  
	 * @author arrowing 
	 * @date 2013-6-1 上午10:46:37 
	 * @throws
	 */
	private function addSpecialChar(&$value) {
		$value   =  trim($value);
		if( false !== strpos($value,' ') || false !== strpos($value,',') || false !== strpos($value,'*') ||  false !== strpos($value,'(') || false !== strpos($value,'.') || false !== strpos($value,'`')) {
			//如果包含* 或者 使用了sql方法 则不作处理
		}else{
			$value = '`'.$value.'`';
		}
		return $value;
	}
	/**
	 * @Title: parseValue 
	 * @Description: todo(判断传过来的值是什么) 
	 * @param unknown_type $value
	 * @return string  
	 * @author arrowing 
	 * @date 2013-6-1 上午10:48:18 
	 * @throws
	 */
	private function parseValue(&$value) {
		if(is_string($value)) {
			$value = '\''.mysql_escape_string($value).'\'';
		}elseif(isset($value[0]) && is_string($value[0]) && strtolower($value[0]) == 'exp'){
			$value   =  mysql_escape_string($value[1]);
		}elseif(is_null($value)){
			$value   =  'null';
		}
		return $value;
	}
	/**
	 * @Title: parseThinkWhere 
	 * @Description: todo() 
	 * @param unknown_type $key
	 * @param unknown_type $val
	 * @return string  
	 * @author arrowing 
	 * @date 2013-6-1 上午10:49:39 
	 * @throws
	 */
	private function parseThinkWhere($key,$val) {
		$whereStr   = '';
		switch($key) {
			case '_string':
				// 字符串模式查询条件
				$whereStr = $val;

				//判断是否为FIND_IN_SET
				if(strpos($whereStr, 'FIND_IN_SET') !== false){
					$whereStr = preg_replace("/,(\S*)\s*\)/i", ",`".$this->maintable."`.`\$1`)" , $whereStr);
				}

				break;
			case '_complex':
				// 复合查询条件
				$whereStr   = substr($this->parseWhere($val),6);
				break;
			case '_query':
				// 字符串模式查询条件
				parse_str($val,$where);
				if(array_key_exists('_logic',$where)) {
					$op   =  ' '.strtoupper($where['_logic']).' ';
					unset($where['_logic']);
				}else{
					$op   =  ' AND ';
				}
				$array   =  array();
				foreach ($where as $field=>$data)
					$array[] = $this->addSpecialChar($field).' = '.$this->parseValue($data);
				$whereStr   = implode($op,$array);
				break;
		}
		return $whereStr;
	}
	/**
	 * @Title: parseWhere 
	 * @Description: todo(这里用一句话描述这个方法的作用) 
	 * @param unknown_type $where
	 * @return string  
	 * @author liminggang 
	 * @date 2013-6-1 上午10:51:10 
	 * @throws
	 */
	private function parseWhere($where){
		$whereStr = '';
		if($where){
			if(is_string($where)){
				$whereStr = $where;
			}else{
				if(array_key_exists('_logic',$where)) {
					// 定义逻辑运算规则 例如 OR XOR AND NOT
					$operate    =   ' '.strtoupper($where['_logic']).' ';
					unset($where['_logic']);
				}else{
					// 默认进行 AND 运算
					$operate    =   ' AND ';
				}
				foreach ($where as $key=>$val){
					$whereStr .= "( ";
					if(0===strpos($key,'_')) {
						// 解析特殊条件表达式
						$whereStr   .= $this->parseThinkWhere($key,$val);
					}else{
						$key = $this->addSpecialChar($key);
						//添加主要查询表
						if(strpos($key, '.') === false){
							$key = "`".$this->maintable."`.{$key}";
						}

						if(is_array($val)) {
							if(is_string($val[0])) {
								if(preg_match('/^(EQ|NEQ|GT|EGT|LT|ELT|NOTLIKE|LIKE)$/i',$val[0])) { // 比较运算
									$whereStr .= $key.' '.$this->comparison[strtolower($val[0])].' '.$this->parseValue($val[1]);
								}elseif('exp'==strtolower($val[0])){ // 使用表达式
									$whereStr .= ' ('.$key.' '.$val[1].') ';
								}elseif(preg_match('/IN/i',$val[0])){ // IN 运算
									if(is_array($val[1])) {
										array_walk($val[1], array($this, 'parseValue'));
										$zone   =   implode(',',$val[1]);
									}else{
										$zone   =   $val[1];
									}
									$whereStr .= $key.' '.strtoupper($val[0]).' ('.$zone.')';
								}elseif(preg_match('/BETWEEN/i',$val[0])){ // BETWEEN运算
									$data = is_string($val[1])? explode(',',$val[1]):$val[1];
									$whereStr .=  ' ('.$key.' '.strtoupper($val[0]).' '.$this->parseValue($data[0]).' AND '.$this->parseValue($data[1]).' )';
								}else{
									throw_exception(L('_EXPRESS_ERROR_').':'.$val[0]);
								}
							}else {
								$count = count($val);
								if(in_array(strtoupper(trim($val[$count-1])),array('AND','OR','XOR'))) {
									$rule = strtoupper(trim($val[$count-1]));
									$count   =  $count -1;
								}else{
									$rule = 'AND';
								}
								for($i=0;$i<$count;$i++) {
									$data = is_array($val[$i])?$val[$i][1]:$val[$i];
									if('exp'==strtolower($val[$i][0])) {
										$whereStr .= '('.$key.' '.$data.') '.$rule.' ';
									}else{
										$op = is_array($val[$i])?$this->comparison[strtolower($val[$i][0])]:'=';
										$whereStr .= '('.$key.' '.$op.' '.$this->parseValue($data).') '.$rule.' ';
									}
								}
								$whereStr = substr($whereStr,0,-4);
							}
						}else {
							//对字符串类型字段采用模糊匹配
							if(C('DB_LIKE_FIELDS') && preg_match('/('.C('DB_LIKE_FIELDS').')/i',$key)) {
								$val  =  '%'.$val.'%';
								$whereStr .= $key." LIKE ".$this->parseValue($val);
							}else {
								$whereStr .= $key." = ".$this->parseValue($val);
							}
						}
					}
					$whereStr .= ' )'.$operate;
				}
				$whereStr = substr($whereStr,0,-strlen($operate));
			}
			return empty($whereStr)?'':' WHERE '.$whereStr;
		}
	}
	/**
	 * @Title: spellSql 
	 * @Description: todo(拼凑搜索的SQL语句) 
	 * @param unknown_type $argsMap
	 * @param unknown_type $modelName
	 * @param unknown_type $ename
	 * @return string  
	 * @author arrowing 
	 * @date 2013-6-1 上午10:51:27 
	 * @throws
	 */
	public function spellSql($argsMap,$modelName,$ename){
		$selects = array(); // 要查找的字段数组
		$where = array();   // 条件数组
		$joins = '';   // left join 字符串
		$this->maintable = $maintable = $_POST['maintable'];

		//接收传递来的参数：TP的where时的map参数


		// 特殊参数列表

		/* 产品类型及其子类型
		 * 去除，先用解析map方法
		if(isset($_REQUEST['typeid']) && !empty($_REQUEST['typeid'])){
		$productcode = A('MisProductCode');
		$typeid = $productcode->getTypeidTree(intval($_REQUEST['typeid']));
		$typeid = substr($typeid, 1);
		}
		// 特殊参数列表：产品类型及其子类型
		$equal_style = array('custmerid');
		// 特殊参数 产品类型及其子类型
		$in_style = array(
				//'typeid' => $typeid
		);
		*/

		foreach($_POST as $k => $v){

			// $k例子 "查询的表-查找字段-显示字段-搜索类型"
			if(substr_count($k, '-') == 3){
				$msg = explode('-', $k);
				$table_field = "`{$msg[0]}`.`{$msg[1]}`"; // 查询的表.查找字段

				// 有些搜索模板没有显示id，不过lookup必须要传id
				if($msg[2] == 'id'){
					$selects[1000] = " $table_field AS `{$msg[2]}` ";
				}else{
					$selects[] = " $table_field AS `{$msg[2]}` ";
				}

				if(!$selects[1000]){
					$selects[1000] = " `$maintable`.`id` AS `id` ";
				}

				switch($msg[3]){
					case 'text':
						if($v != ''){
							if(strpos($v, '..')){ // 范围搜索 从A到B
								$v = explode('..', $v);
								$v1 = intval($v[0]);
								$v2 = intval($v[1]);

								$where[] = " $table_field >= {$v1} ";
								$where[] = " $table_field <= {$v2} ";

							}elseif(strpos($v, ',')){ //范围搜索 在。。。里面
								function addSalash($val){
									return "'$val'";
								}
								$v = explode(',', $v);
								$v = array_map("addSalash", $v);
								$v = implode(',', $v);
								$where[] = " $table_field IN ({$v}) ";
							}else{ // 模糊搜索
								$v = str_replace('?', '_', $v);
								$v = str_replace('*', '%', $v);
								$sLike = " $table_field LIKE '{$v}' ";

								$textmore = $_POST['textmore-'.$k];
								$arrayTextmore = array();
								if(!empty($textmore) && count($textmore) > 0){
									foreach($textmore as $text_v){
										$arrayTextmore[] = " `{$msg[0]}`.`{$text_v}` LIKE '{$v}' ";
									}
									$arrayTextmore[] = $sLike;
									$where[] = " (".implode(' OR ', $arrayTextmore).") ";
								}else{
									$where[] = $sLike;
								}
							}
						}
						break;
					case 'range':
						if(is_numeric($v[0]) || is_numeric($v[1])){
							if(!is_numeric($v[1])){
								$where[] = " $table_field >= {$v[0]} ";
							}elseif(!is_numeric($v[0])){
								$where[] = " $table_field <= {$v[1]} ";
							}elseif($v[0] === $v[1]){
								$where[] = " $table_field = {$v[0]} ";
							}else{
								$where[] = " $table_field >= {$v[0]} ";
								$where[] = " $table_field <= {$v[1]} ";
							}
						}
						break;
					case 'time':
						if($v[0] != '' || $v[1] != ''){
							$start = strtotime($v[0]);

							// strtotime 取得当前日期的时间戳是从零点算起
							$end = strtotime($v[1]) + 3600*24 - 1;

							if($v[0] == ''){
								$where[] = " $table_field <= $end ";
							}elseif($v[1] == ''){
								$where[] = " $table_field >= $start ";
							}else{
								$where[] = " $table_field >= $start ";
								$where[] = " $table_field <= $end ";
							}
						}
						break;
					case 'checkfor':
					case 'group':
						if($v > 0 && $v != ''){
							$where[] = " $table_field = $v ";
						}
						break;
					case 'select':
						if($v != ''){
							$where[] = " $table_field = $v ";
						}
						break;
					case 'radio':
						if($v != '' && $v != '__empty__'){
							$where[] = " $table_field = $v ";
						}
						break;
					case 'checkbox':
						unset($v[0]);
						if(!empty($v) && count($v) > 0){
							$v = implode(',', $v);
							$where[] = " $table_field IN ({$v}) ";
						}
						break;
					case 'db':
						if($v != ''){
							$v = str_replace('?', '_', $v);
							$v = str_replace('*', '%', $v);
							$sLike = " `{$msg[0]}`.`{$_POST['db-'.$k]}` LIKE '{$v}'";

							$textmore = $_POST['textmore-'.$k];
							$arrayTextmore = array();
							if(!empty($textmore) && count($textmore) > 0){
								foreach($textmore as $text_v){
									$arrayTextmore[] = " `{$msg[0]}`.`{$text_v}` LIKE '{$v}' ";
								}
								$arrayTextmore[] = $sLike;
								$where[] = " (".implode(' OR ', $arrayTextmore).") ";
							}else{
								$where[] = $sLike;
							}
						}
						break;
				}
			}elseif($k == 'join' && $v != ''){ // left join 字符串
				$joins = urldecode(str_replace('-', ' LEFT JOIN ', $v));
				/*
				 * 去除，现用解析map方法确定传递的额外参数
				// 额外的参数 全等型
				}elseif(in_array($k, $equal_style) && $v != ''){ // 不是普通字段
				$where[] = " `{$maintable}`.`$k` = '{$v}' ";

				// 额外的参数 IN型
				}elseif(array_key_exists($k, $in_style) && $v != '' && $v != 0){ // 不是普通字段
				$where[] = " `{$maintable}`.`$k` IN ({$in_style[$k]}) ";
				*/
			}
		}

		// status条件 非管理员
		if ($_SESSION["a"] != 1){
			$where[] = " `{$maintable}`.`status` > 0 ";
		}
		//其他where条件
		if(trim($_POST['condition']) != ''){
			$otherCondition = explode(',', $_POST['condition']);
			$where = array_merge($where, $otherCondition);
		}

		//权限判断，必须存在createid字段
		$showsql = "SHOW COLUMNS FROM `{$maintable}`";
		$maintablefields = D('Model')->query($showsql);
		$havecreateid = false;

		foreach($maintablefields as $f){
			if($f == 'createid'){
				$havecreateid = true;
				break;
			}
		}
		//modelName是视图是做处理
		$isView=strpos($modelName,"View");
		if($isView!=false){
			//下面是针对视图模型的主表及关联表的分析
			$tablesArray=array();
			//          $viewModel=D("$modelName")->where("mis_product_code.id > 1");
			$viewModel=D("$modelName");
			//构建视图生成的查询语句
			$endsql=$viewModel->buildSql();
			$viewModelResult=$viewModel->viewFields;
			// 			print_r($viewModelResult);
			$viewTableArray=array();
			$viewRelTableArray=array();
			foreach($viewModelResult as $viewModelResultKey=>$viewModelResultValue){
				foreach($viewModelResultValue as $viewModelResultSubKey=>$viewModelResultSubValue)
				{
					//数组里只取key=_as 或者 key=_table 的value值，表名；找到后立即跳出本次循环
					if($viewModelResultSubKey=='_as' or $viewModelResultSubKey=='_table'){
						//特殊字符名前面加了`号,这里需要去除掉`符号
						$viewTableArray[]=str_replace('`','',$viewModelResultSubValue);
						break;
					}
				}
			}
			$viewMainTable=$viewTableArray[0];
			//排除表中第一个元素，我们规定视图中第一行必须是主表
			$viewRelTableArray=array_splice($viewTableArray, 1);
			//print_r($viewRelTableArray);
			//下面是针对搜索模板的主表及关联表的分析及拼装
			$SearchModel=M("search");
			$searchMap['ename']=$ename;
			$searchResult=$SearchModel->where($searchMap)->select();
			$searchMainTable=$searchResult[0]['maintable'];
			if(!empty($searchResult[0]['relatetables'])){
				$searchRelTableArray=array();//获取关联表，存入数组
				$leftJoinArray=array();//拼装关联表left join on语句
				$tmpResult=substr($searchResult[0]['relatetables'],0,-1);
				$tmpResult=explode(";",$tmpResult);
				foreach($tmpResult as $tmpResultKey=>$tmpResultValue){
					$tmpResultValue=explode("-",$tmpResultValue);
					//搜索配置的关联表名称数组
					$searchRelTableArray[]=$tmpResultValue[0];
					//拼装关联表left join on语句
					$leftJoinArray[$tmpResultValue[0]] = "LEFT JOIN {$tmpResultValue[0]} ON `{$tmpResultValue[0]}`.`{$tmpResultValue[1]}` = `{$tmpResultValue[2]}`.`{$tmpResultValue[3]}`"; // 查询的表.查找字段
				}
			}
			//          测试点：关键比较
			//  		echo $viewMainTable."<br/>";
			//  		echo $searchMainTable."<br/>";
			//如果视图主表与搜索配置主表相同时
			if($viewMainTable==$searchMainTable){
				$endsql0="";//获取对应拼装关联表语句
				//获得视图主表内没有，而搜索模板内有的表名
				$needArray=array_diff($searchRelTableArray,$viewRelTableArray);
				//测试点：差集效果
				//print_r($leftJoinArray);
				foreach($needArray as $needArrayKey=>$needArrayValue){
					foreach($leftJoinArray as $leftJoinArrayKey=>$leftJoinArrayValue){
						if($needArrayValue==$leftJoinArrayKey){
							$endsql0.=$leftJoinArrayValue;
						}
					}
				}
				//echo $endsql0;
				//判断是否存在where条件
				$tmpNum=0;//临时存数，初始化要清零
				$tmpNum=stripos($endsql,"where");
				if(!empty($tmpNum)){
					//获取where前面的字符串
					$endsql1=substr($endsql,1,$tmpNum-1);
					//echo $endsql1."<br/>";
					//去除关键字where后的条件
					$endsql2=" AND ".substr($endsql,$tmpNum+5,-1);

				}
				else{
					//获取where前面的字符串
					$endsql1=substr($endsql,1,$tmpNum-1);
					//去除关键字where后的条件
					$endsql2="";
				}
				//拼装后的视图查询语句viewSql
				$viewSql="";
				$viewSql.=$endsql1;
				$viewSql.=$endsql0;
				//echo $viewSql;
			}
			else{
				//视图主表与搜索配置主表不同时，抛出异常提示
				$this->error ( '搜索配置与页面不匹配，请联系管理员检查' );
			}
		}
		//视图处理结束
		//下面处理非视图对象
		else{
			//重新配置selects数组
			$arr1=array();//初始化查询结果别名数组
			$arr2=array();//初始化动态配置别名数组
			$arr3=array();//初始化arr1,arr2差集
			foreach($selects as $selKey=>$selValue)
			{
				//获取到查询结果别名,获取AS后面的字段
				$tmp="";//零时数组，初始化为空值
				$tmp=stristr($selValue,'AS');
				//echo $tmp."<br/>";
				//获取`符后面的长度
				$tmpNum=0;//临时存数，初始化为0
				$tmpNum=strpos($tmp,'`')+1;
				$tmp=substr($tmp,$tmpNum,-1);
				//去除掉`符号，保留最终输出查询结果别名数组
				$arr1[]=str_replace('`','',$tmp);
			}
			//print_r($arr1);
			$SCDModel = D('SystemConfigDetail');
			$detailList = $SCDModel->getDetail($modelName,$cache=false);
			foreach($detailList as $dtlKey=>$dtlValue)
			{
				//获取所有已存在的动态配置name名称
				$arr2[]=$dtlValue['name'];
			}
			//print_r($arr2);
			//比较出动态配置里有而搜索配置里没有的字段拼凑成数组
			$arr3=array_diff($arr2,$arr1);
			//print_r($arr3);
			//判断模型里面表字段
			$fields=M($modelName)->getDbFields();
			$arr4=array_intersect($arr3,$fields);
			//print_r($arr4);
			//拼凑动态配置里有，搜索配置里没有的字段
			foreach($arr4 as $fieldsKey=>$fieldsValue)
			{
				$tmp= " `$maintable`.`$fieldsValue` AS `$fieldsValue` ";
				array_push($selects,$tmp);
			}
			//测试点：得到最新拼凑的selects结果
			//print_r($selects);
		}
		// 开始拼凑SQL语句
		$sql = "SELECT ".implode(',', $selects);
		$sql.= " FROM `{$maintable}` ";
		if(trim($joins) != ''){
			$sql.= " LEFT JOIN {$joins} ";
		}

		//如果是视图，重新赋值拼接好的SQL语句
		if($isView!=false){
			$sql=$viewSql;
			//echo $sql;
		}
		//解析传来的参数where
		if(!empty($argsMap)){
			if(array_key_exists($modelName, $this->fifterFields)){
				foreach($this->fifterFields[$modelName] as $args_k){
					unset($argsMap[$args_k]);
				}
			}
		}
		$whereStr = $this->parseWhere($argsMap);
		if($whereStr){
			if(count($where) > 0){
				$sql.= $whereStr .' AND '. implode(' AND ', $where);
			}else{
				$sql.= $whereStr;
			}
		}else{
			if(count($where) > 0){
				$sql.= " WHERE ".implode(' AND ', $where);
			}
		}
		//如果是视图，重新赋值拼接好的SQL语句最后的where部分
		if($isView!=false){
			$sql.=$endsql2;
		}
		//测试点eagle
		//echo $sql;
		//echo "1111<br/>";
		return ($sql);
	}
	/**
	 * @Title: toDbName 
	 * @Description: todo(正则表达处理模型名字) 
	 * @param unknown_type $modelname  
	 * @author liminggang 
	 * @date 2013-6-1 上午10:58:25 
	 * @throws
	 */
	private function toDbName($modelname){
		// 		$len = strlen($modelname);
		// 		$modelname = str_replace($modelname[0], strtolower($modelname[0]), $modelname);

		// 		for($i=1;$i<$len;$i++){
		// 			$str = substr($modelname, $i, 1);
		// 			if($str == strtoupper($str)){
		// 				$modelname = substr($modelname, 0, $i).'_'.strtolower($str).substr($modelname, $i + 1);
		// 			}
		// 		}
		return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $modelname), "_"));
		//$modelname = D($modelname)->getTableName();
		//return $modelname;
	}
	
	/**
	 * @Title: advancedSearch
	 * @Description: todo(高级检索)
	 * @author 杨东
	 * @date 2013-6-7 下午1:27:32
	 * @throws
	 */
	public function advancedsearch(){
		$name = $_REQUEST['model'];
		$this->assign("model",$name);
		$model = D('SystemConfigDetail');//系统配置文件模型
		$detailList = $model->getDetail($name,false,'','searchsortnum');//获取searchby.inc.php文件内容
		$dmStep = $_REQUEST['dmStep']; //是否带有维度检索
		if($dmStep){
			// 维度
			$dmmap = array (
					'type' => '01',
					'status' => '1'
			);
			$dmlist = getOptionList('mis_inventory_domain',$dmmap);
			foreach ($dmlist as $k => $v) {
				if ($v['refertable']) {
					$searchField = 'dd'.$v['id'].'.'.$v['referfield'];
					$name = 'dd'.$v['id'].$v['referfield'];
				} else {
					$searchField = 'd'.$v['id'].'.content';
					$name = 'd'.$v['id'].'content';
				}
				$detailList[] = array(
						'name' => $name,
						'searchField' => $searchField,
						'showname' => $v['name'],
						'type' => 'text',
						'issearch' => 1,
						'isallsearch' => 1,
						'status' => 1
				);
			}
		}
		foreach ($detailList as $k => $v) {
			// 判断是否为快捷查询条件
			if($v['issearch'] != '1' || $v['status'] < 0){
				unset($detailList[$k]);
				continue;
			}
			// 获取当前查询对象的HTML值
			$detailList[$k]['html'] = $this->quickSearchToHtml($v['type'],'advanced'.$v['name'],$v['showname'],$v['table'],$v['field'],$v['conditions']);
		}
		$this->assign("detailList",$detailList);
		$this->display();
	}
	public function advancedsubsearch(){
		$name = $_REQUEST['model'];
		$dmStep = $_REQUEST['dmStep'];
		$model = D('SystemConfigDetail');//系统配置文件模型
		$detailList = $model->getSubDetail($name,false,'','searchsortnum');//获取searchby.inc.php文件内容
		if($_REQUEST['domain']){
			// 维度
			$dmmap = array (
					'type' => '01',
					'status' => '1'
			);
			$dmlist = getOptionList('mis_inventory_domain',$dmmap);
			foreach ($dmlist as $k => $v) {
				if ($v['refertable']) {
					$searchField = 'dd'.$v['id'].'.'.$v['referfield'];
					$name = 'dd'.$v['id'].$v['referfield'];
				} else {
					$searchField = 'd'.$v['id'].'.content';
					$name = 'd'.$v['id'].'content';
				}
				$detailList[] = array(
					'name' => $name,
					'searchField' => $searchField,
					'showname' => $v['name'],
					'type' => 'text',
					'issearch' => 1,
					'isallsearch' => 1,
					'status' => 1
				);
			}
		}
		foreach ($detailList as $k => $v) {
			// 判断是否为快捷查询条件
			if($v['issearch'] != '1' || $v['status'] < 0){
				unset($detailList[$k]);
				continue;
			}
			// 获取当前查询对象的HTML值
			$detailList[$k]['html'] = $this->quickSearchToHtml($v['type'],'advanced'.$v['name'],$v['showname'],$v['table'],$v['field'],$v['conditions']);
		}
		$this->assign("detailList",$detailList);
		$this->display();
	}
	/**
	 * @Title: singlesearch
	 * @Description: todo(单个列表检索)   
	 * @author 杨东 
	 * @date 2013-6-8 上午11:21:09 
	 * @throws 
	*/  
	public function singlesearch(){
		$name = $_REQUEST['model'];
		$model = D('SystemConfigDetail');//系统配置文件模型
		$detailList = $model->getDetail($name,false);//获取searchby.inc.php文件内容
		$singlesearch = array();// 单个检索
		foreach ($detailList as $k => $v) {
			if($v['name'] == $_REQUEST['field']){
				$showname = $v['showname'];
			}
			if($v['issearch'] != '1' || $v['status'] < 0){
				continue;
			}
			// 判断是否为快捷查询条件
			if($v['name'] == $_REQUEST['field']){
				$singlesearch = $v;
				// 获取当前查询对象的HTML值
				$singlesearch['html'] = $this->quickSearchToHtml($v['type'],'advanced'.$v['name'],$v['showname'],$v['table'],$v['field'],$v['conditions']);
			}
		}
		if($singlesearch){
			$this->assign("singlesearch",$singlesearch);
		} else {
			$this->assign('error', "&nbsp;&nbsp;&nbsp;".$showname.'没有配置成检索条件，请换个条件检索!');
		}
		$this->display();
	}

}
?>