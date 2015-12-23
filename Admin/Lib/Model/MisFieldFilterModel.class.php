<?php

/**
 * 表单过滤器生成
 * @Title: MisFieldFilterAction 
 * @Package package_name
 * @Description: todo(生成表单的过滤器及前端css文件。) 
 * @author quqiang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015年1月29日 上午10:54:18 
 * @version V1.0
 */
class MisFieldFilterModel extends CommonModel {
	protected $trueTableName = '';
	// 节点名称
	private $nodeName = '';
	// 控制器名称
	private $actionName = '';
	// 模板名称
	private $tplName = '';
	// 模板ID
	private $formid = '';
	
	// 格式化后的可用数据
	private $dataSouce = array ();
	
	/**
	 * 原始数据格式化为组件属性数据。
	 * @Title: format
	 * @Description: todo(将原始数据格式化为以组件为主的属性数据)
	 *
	 * @param array $data
	 *        	原始数据
	 * @author quqiang
	 *         @date 2015年1月29日 上午11:02:37
	 * @example $data = array('ActionName','nodeName|nodeID','tplName'
	 *          array(
	 *          array('name',true,false),//	$arr[0]：字段名	$arr[1]:写入权限	$arr[2]：读取权限
	 *          array('age',true,true),
	 *          array('sex',false,false),
	 *          )
	 *          );
	 *          $fmtData = format($data);
	 *          权限值 为 true时 表示不做过滤操作，没有读取权限包含没有写入权限
	 *          tplName:可为空，表示使用index模板。做后续可能的扩展用。
	 * @throws Exception 需要异常处理
	 */
	private function format($data) {
		if (! is_array ( $data )) {
			throw new Exception ( "没有可用数据用于解析！" );
			exit ();
		}
		$actionName = $data [0];
		$nodeName = $data [1];
		$tplName = $data [2] ? $data [2] : 'index';
		
		if (! $actionName) {
			throw new Exception ( '传入Action名称为空' );
			exit ();
		}
		$this->actionName = $actionName;
		
		if (! $nodeName) {
			throw new Exception ( '传入节点标识为空' );
			exit ();
		}
		$this->nodeName = $nodeName;
		
		if (! is_dir ( TMPL_PATH . C ( 'DEFAULT_THEME' ) . '/' . $actionName )) {
			throw new Exception ( "文件路径 " . TMPL_PATH . C ( 'DEFAULT_THEME' ) . '/' . $actionName . " 不存在!" );
			exit ();
		}
		$formid = getFieldBy ( $actionName, "actionname", "id", "mis_dynamic_form_manage" );
		if (! $formid) {
			throw new Exception ( "传入表单Action[{$actionName}]不存在于表单记录表中，请查证！" );
			exit ();
		}
		$this->formid = $formid;
		
		$dataSouce = $data [3];
		// if (! is_array ( $dataSouce )) {
		// throw new Exception ( "过滤条件为空！" );
		// return false;
		// }
		$this->dataSouce = $dataSouce;
	}
	/**
	 * 生成过滤器代码
	 * @Title: modelFilter
	 * @Description: todo(将用户传入的数据生成过滤器需要的相关文件，包括 model的_filter数组 ， 过滤样式 )
	 *
	 * @param array $data        	
	 * @author quqiang
	 *         @date 2015年1月29日 下午3:46:23
	 * @throws
	 *
	 */
	public function createFilter($data) {
		$this->format ( $data );
		
		// 生成 model _filter 数组
		// 定义_filter 数组存放位置
		$filterSavePath = TMPL_PATH . C ( 'DEFAULT_THEME' ) . '/' . $this->actionName . '/right';
		// 获取组件信息
		$filterFiledKeysArr = array_keys ( $this->dataSouce );
		$model = M ( 'mis_dynamic_form_propery' );
		$map ['formid'] = array (
				'eq',
				$this->formid 
		);
		$map ['fieldname'] = array (
				'in',
				join ( ',', $filterFiledKeysArr ) 
		);
		$dataProperty = $model->where ( $map )->select ();
		$fieldArr = '';
		foreach ( $dataProperty as $key => $val ) {
			$fieldArr [$val ['fieldname']] = $val;
		}
		
		// 解析数据库操作权限，生成
		createFolder ( $filterSavePath );
		// 遍历权限源文件，构建所需文件内容
		// model 操作权限
		$modelRight = '';
		// 样式，通过befor属性设置组件的权限
		$cssRight = '';
		// 样式类名的控制权限，控制的是页面上的一个现有div
		$classRight = '';
		// 样式类名控制显示权限， 只用于view页面
		$classViewRight = '';
		// 临时的不可查看图标内容
		$nbmcss = '\266F\266F\266F\266F\266F\266F';
		foreach ( $this->dataSouce as $key => $val ) {
			$tempModelRight = array ();
			$tempCssRight = '';
			$tempClassRight = '';
			$tempClassViewRight='';
// 			echo '--' . $key;
// 			var_dump ( $val [0] );
			// 写入权限不为空
			if ($val [0] == true) {
				
				$tempModelRight [0] = 'filterWritSetEmpty';
				unset($tempData);
				$tempData = $this->getControllPropertyToRight('view',$fieldArr [$key]);
				$tempClassRight .= $tempData[0];
				$tempClassViewRight .= $tempData[1];
// 				$tempClassRight .= $fieldArr [$key] ['category'] . '_cantwrite ';
				$tempCssRight = <<<EOF
				
EOF;
				}else{
					$tempModelRight[0]='';
					$tempClassRight .= '';
					$tempClassViewRight .= '';
				}
				// 查看权限不为空
				if($val[1]==true){
					$tempModelRight[1]='filterReadSetEmpty';
					//$tempClassRight .= $fieldArr[$key]['category'].'_cantview ';
					unset($tempData);
					$tempData = $this->getControllPropertyToRight('write',$fieldArr [$key]);
					$tempClassRight .= $tempData[0];
					$tempClassViewRight .= $tempData[1];
					$tempCssRight=<<<EOF
					
EOF;
				$tempCssRight = iconv ( "UTF-8", "UTF-8//IGNORE", $tempCssRight );
			} else {
				$tempModelRight [1] = '';
				$tempClassRight .= '';
				$tempClassViewRight .= '';
			}
			$modelRight [$key] = $tempModelRight;
			$cssRight [] = $tempCssRight;
			$classRight [$key] = $tempClassRight;
			$classViewRight [$key] = $tempClassViewRight;
		
		}
		
		// 生成数据库操作权限。
		$this->writeover ( $filterSavePath . '/' . $this->nodeName . '.php', 'return ' . $this->pw_var_export ( $modelRight ), true );
		// 通过类名控制字段读写权限
		$this->writeover ( $filterSavePath . '/' . $this->nodeName . 'Class.php', 'return ' . $this->pw_var_export ( $classRight ), true );
		// 生成view页面的查看权限。
		$this->writeover ( $filterSavePath . '/' . $this->nodeName . 'ViewClass.php', 'return ' . $this->pw_var_export ( $classViewRight ), true );
		
		// 生成页面权限控制权限， 样式名称
		// 生成权限样式控制
		// 统一样式
		$allTitleCss = '.field_' . join ( ', .field_', $filterFiledKeysArr );
		$allTitleCss .= <<<EOF
{
	position: relative;
}
EOF;
		// 生成样式 add 页面
		$this->writeover ( TMPL_PATH . C ( 'DEFAULT_THEME' ) . '/' . $this->actionName . '/add.css', $allTitleCss . chr ( 13 ) . chr ( 10 ) . join ( chr ( 13 ) . chr ( 10 ), $cssRight ), false );
		// 生成样式 edit 页面
		$this->writeover ( TMPL_PATH . C ( 'DEFAULT_THEME' ) . '/' . $this->actionName . '/edit.css', $allTitleCss . chr ( 13 ) . chr ( 10 ) . join ( chr ( 13 ) . chr ( 10 ), $cssRight ), false );
		// 生成样式 view 页面
		$this->writeover ( TMPL_PATH . C ( 'DEFAULT_THEME' ) . '/' . $this->actionName . '/view.css', $allTitleCss . chr ( 13 ) . chr ( 10 ) . join ( chr ( 13 ) . chr ( 10 ), $cssRight ), false );
	}
	
	/**
	 * 按组件属性得到权限 类名
	 * @Title: getControllPropertyToRight
	 * @Description: todo(将组件的属性翻译为对应权限的页面样式 class 名称 ,
	 *  查看页面的样式 在返回值中的 系第一项， 只有在 type == view 才有效)
	 * 
	 * @param string $type        	
	 * @param unknown $property        	
	 * @author quqiang
	 *         @date 2015年2月2日 下午2:08:07
	 * @throws
	 *
	 */
	private function getControllPropertyToRight($type = 'write', $property) {
		$separator =' ';
		$middle_char = 'not_edit_';
		$viewCls='not_view ';
		if($type == 'write'){
			$middle_char = 'not_see_';
			$viewCls='';
		}
		$cls = '';
		switch ($property ['category']) {
			case 'text' :
				// 文本框
				$cls=$middle_char.'base'.$separator;
				if($property['unit'] && $property['unitls']){
					$cls=$middle_char.'one'.$separator;
				}
				break;
			case 'tablabel' :
				// 标签
				$cls=$middle_char.'base'.$separator;
				break;
			case 'hiddens' :
				// 隐藏域组件
				break;
			case 'select' :
				// 下拉框
				$cls=$middle_char.'select2'.$separator;
				if($property['treeparentfield']){
					$cls=$middle_char.'one'.$separator;
				}
				break;
			case 'checkbox' :
				// 复选框
				$cls=$middle_char.'check'.$separator;
				break;
			case 'radio' :
				// 单选框
				$cls=$middle_char.'check'.$separator;
				break;
			case 'textarea' :
				// 文本域
				$cls=$middle_char.'base '.$separator;
				break;
			case 'date' :
				// 日期组件
				$cls=$middle_char.'one'.$separator;
				break;
			case 'lookup' :
				// lookup组件
				$cls=$titlecss.$middle_char.'two'.$separator;
				break;
			case 'lookupsuper' :
				// lookupSuper组件
				$cls=$middle_char.'lookupsuper'.$separator;
				break;
			case 'upload' :
				// 上传组件
				$cls=$middle_char.'up'.$separator;
				break;
			case 'fieldset' :
				//作用域
				break;
			case 'userselect' :
				// 人员选择器
				$cls=$middle_char.'two'.$separator;
				break;
			case 'areainfo' :
				// 地区信息
				$cls=$middle_char.'address'.$separator;
				break;
			case 'fiexdtext' :
				// 固定文本
				$cls=$middle_char.'fiexdtext'.$separator;
				break;
			case 'subtitles' :
				// 副标题
				$cls=$middle_char.'fiexdtext'.$separator;
				break;
			case 'datatable' :
				// 内嵌数据表格 $titlecss为有无标题附加css类（遮挡层）
				$titlecss = $property['title']?'header_datatable':'';
				$cls=$titlecss.$separator.$middle_char.'datatable'.$separator;
				break;
		}
		$ret[0] = $cls;
		$ret[1] = $viewCls;
		return $ret;
	}
}
