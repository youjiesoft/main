<?php
/**
 * 页面设计基类
 * @Title: DesingFormBaseAction 
 * @Package package_name
 * @Description: todo(生成基础功能) 
 * @author quqiang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015年5月12日 下午4:35:03 
 * @version V1.0
 */
class DesingFormBaseAction extends CommonAction {
	/**
	 * 通用\默认属性
	 *
	 * @var array
	 */
	protected $default;
	/**
	 * 组件详细列表配置，包含组件配置与属性
	 *
	 * @var array
	 */
	protected $controls;
	/**
	 * 组件整合的完整配置信息,只有属性，没有组件配置
	 *
	 * @var array
	 */
	protected $controlsConfig;
	
	/**
	 * 新增时使用到的唯一标识属性名称
	 *
	 * @var string
	 */
	protected $tagIndentity = 'checkfor';
	/**
	 * 属性存储标签类型
	 *
	 * @var sring hidden
	 */
	protected $propertyTagType = 'hidden'; // hidden
	
	/**
	 * 构造函数，父类
	 * @Title: __construct
	 * @Description: todo(基础构造函数)
	 *
	 * @author quqiang
	 *         @date 2015年5月12日 下午4:37:55
	 * @throws
	 *
	 */
	public function __construct() {
		parent::__construct ();
		// 加载基础配置信息
		$dir = CONF_PATH;
		$configDir = $dir . 'desing.php';
		if (! file_exists ( $configDir )) {
			$msg = '文件 ' . $configDir . ' 不存在!';
			throw new NullDataExcetion ( $msg );
		}
		require $configDir;
		$this->default = $desing_default;
		$this->controls = $desing_controll;
		// 合并组件配置元素
		if ($this->controls && $this->default) {
			unset ( $temp );
			foreach ( $this->controls as $k => $v ) {
				$array = array_merge ( $this->default, $v ['property'] );
				sortArray ( $array, 'sort' );
				$temp [$k] = $array;
			}
			$this->controlsConfig = $temp;
		}
		
		$this->assign ( 'config', $this->controlsConfig );
		$this->assign ( 'control', $this->controls );
		$this->assign ( 'tagIndentity', $this->tagIndentity );
		$this->assign ( 'propertyTagType', $this->propertyTagType );
	}
	
	/*
	 * ******************************************************************************************
	 * 功能函数区
	 * ******************************************************************************************
	 */
	/**
	 * 构建组件列表源数据
	 * @Title: getControlList
	 * @Description: todo(这里用一句话描述这个方法的作用)
	 *
	 * @author quqiang
	 *         @date 2015年5月12日 下午4:46:33
	 * @throws NullDataExcetion
	 * @deprecated 简单处理
	 */
	protected function getControlList() {
		if (! is_array ( $this->controlsConfig )) {
			throw new NullDataExcetion ( '组件配置缺失!' );
		}
		unset ( $temp );
		foreach ( $this->controlsConfig as $k => $v ) {
			if (1 == $this->controls [$k] ['show']) {
				$temp [$k] = array (
						'title' => $this->controls [$k] ['title'],
						'html' => $this->controls [$k] ['html'],
						'config' => array (
								'cls' => str_replace ( '#width#', $v ['width'] ['value'], $v ['controllcls'] ['value'] ),
								'title' => $v ['title'] ['value'],
								'category' => $v ['category'] ['value'] 
						) 
				);
			}
		}
		return $temp;
	}
	/**
	 * 数据处理
	 * @Title: parse
	 * @Description: todo(将数据处理为DB或表单)
	 *
	 * @param array $data        	
	 * @param boolean $todb
	 *        	true:将post数据转换为DB数据，false:将Db数据转为post数据
	 * @author quqiang
	 *         @date 2015年5月13日 上午11:49:16
	 * @throws
	 *
	 */
	protected function parse($data, $todb = true, $needToken = false) {
		$souceData = $data;
		if (empty ( $data )) {
			$souceData = $_POST;
		}
		if (! is_array ( $souceData )) {
			$msg = '数据整理失败，来源为空';
			throw new NullDataExcetion ( $msg );
		}
		$category = $this->default ['category'] ['key'];
		$show = 'key';
		$value = 'field';
		if (! $todb) {
			$category = $this->default ['category'] ['field'];
			$show = 'field';
			$value = 'key';
		}
		unset ( $temp );
		foreach ( $this->controlsConfig [$souceData [$category]] as $k => $v ) {
			$temp [$v [$value]] = $souceData [$v [$show]];
		}
		if ($needToken) {
			if (C ( 'TOKEN_NAME' )) {
				if (! $_POST [C ( 'TOKEN_NAME' )])
					$_POST [C ( 'TOKEN_NAME' )] = $_SESSION [C ( 'TOKEN_NAME' )];
				$temp [C ( 'TOKEN_NAME' )] = $_POST [C ( 'TOKEN_NAME' )];
			}
		}
		return $temp;
	}
	
	/**
	 * 将数据库数据转换为页面组件属性
	 * @Title: createEditConttrol
	 * @Description: todo(将数据库数据转换为页面组件属性)
	 *
	 * @param array $data        	
	 * @return string|array
	 * @author quqiang
	 *         @date 2015年5月13日 下午3:12:55
	 * @throws
	 *
	 */
	protected function createEditConttrol($data , $isShowPage=false) {
		if (is_array ( $data )) {
			unset ( $temp );
			unset ( $souceData );
			foreach ( $data as $key => $val ) {
				$souceData[$val['id']] = $val;
				$eachControlData = self::parse ( $val, false );
				// 取得组件类型
				$category = $eachControlData [$this->default ['category'] ['key']];
				$group = $this->controls [$category] ['group'];
				// 取得显示样式
				$group = $this->controls [$category] ['group'];
				if($isShowPage){
					$temp [$group] [] = $this->createSingleShowControl ( $val );
				}else{
					$temp [$group] [] = $this->createSingleConttrol ( $val );
				}
			}
			
			unset ( $ret );
			foreach ( $temp as $k => $v ) {
				$ret [$k] = join ( '', $v );
			}
			return $ret;
		} else {
			return '';
		}
	}
	
	/**
	 * 将数据库数据转换为页面组件属性
	 * @Title: createEditConttrol
	 * @Description: todo(将数据库数据转换为页面组件属性)
	 *
	 * @param array $data        	
	 * @return string|array
	 * @author quqiang
	 *         @date 2015年5月13日 下午3:12:55
	 * @throws
	 *
	 */
	protected function createSingleConttrol($data) {
		if (is_array ( $data )) {
			$tempHtml = '';
			// 组件使用皮肤
			$cls = '';
			// 可见内容
			$show = '';
			// 配置项的模板内容
			$tpl = '';
			$eachControlData = self::parse ( $data, false );
			// 取得组件类型
			$category = $eachControlData [$this->default ['category'] ['key']];
			
			$group = $this->controls [$category] ['group'];
			// 取得显示样式
			/**
			 * 取得显示样式
			 * 1：取出显示样式默认模板
			 * 2：取得宽度值，从DB或配置中
			 * 3：替换得到最终值
			 */
			// 1:
			$cls = $this->controlsConfig [$category] ['controllcls'] ['value'];
			// 2:
			$width = $eachControlData [$this->controlsConfig [$category] ['width'] ['key']] ? $eachControlData [$this->controlsConfig [$category] ['width'] ['key']] : $this->controlsConfig [$category] ['width'] ['value'];
			// 3:
			$cls = parameReplace ( '#width#', $width, $cls );
			
			$tpl = $this->controls [$category] ['html'];
			// 解析可见内容
			// $show = self::parseTitle ( $eachControlData [$this->controlsConfig [$category] ['title'] ['key']], $tpl );
			// $show = self::parseContent ( $eachControlData [$this->default ['title'] ['key']], $show );
			
			$show = parameReplace ( '$title', $eachControlData [$this->controlsConfig [$category] ['title'] ['key']], $tpl );
			$show = parameReplace ( '$content', '数据展示区', $show );
			$show = parameReplace ( '__URL__', __URL__, $show );
			// 转译点位符
			$show = preg_replace_callback ( '/\#+(\w+)\#/', function ($vo) use($eachControlData) {
				$ret = '';
				if ($vo [1]) {
					$ret = $eachControlData [$vo [1]];
				}
				return $ret;
			}, $show );
			
			foreach ( $this->controlsConfig [$category] as $k => $v ) {
				$names = $v ['name'];
				$values = $eachControlData [$v ['key']];
				/*
				 * 处理name规则生成,
				 * 1:字段名，[~key~]
				 * 2:多行标识 [#primary#]
				 */
				$names = preg_replace_callback ( '/\~+(\w+)\~/', function ($vo) use($v) {
					if ($vo [1]) {
						return $v [$vo [1]];
					}
				}, $names );
				$names = preg_replace_callback ( '/\#+(\w+)\#/', function ($vo) use($eachControlData) {
					$ret = '';
					if ($vo [1]) {
						$ret = $eachControlData [$vo [1]];
					}
					return $ret;
				}, $names );
				
				$tempHtml .= '<input type="' . $this->propertyTagType . '" name="' . $names . '" value="' . $values . '" />';
			}
			if ($tempHtml) {
				// $tag
				$tag = $this->tagIndentity . '=' . $eachControlData [$this->default ['primary'] ['key']];
				$show = parameReplace ( '$cls', $cls, $show );
				$show = parameReplace ( '$hidden', $tempHtml, $show );
				$show = parameReplace ( '$tag', $tag, $show );
			}
			return $show;
		} else {
			return '';
		}
	}
	
	/**
	 * 生成查看页面组件
	 * @Title: createSingleShowControl
	 * @Description: todo(这里用一句话描述这个方法的作用) 
	 * @param array $data	数据库中存储属性值
	 * @return string	|mixed|string	最终组件html代码  
	 * @author quqiang 
	 * @date 2015年5月15日 上午11:45:32 
	 * @throws
	 */
	protected function createSingleShowControl($data) {
		if (is_array ( $data )) {
			$tempHtml = '';
			// 组件使用皮肤
			$cls = '';
			// 可见内容
			$show = '';
			// 配置项的模板内容
			$tpl = '';
			$eachControlData = self::parse ( $data, false );
			// 取得组件类型
			$category = $eachControlData [$this->default ['category'] ['key']];
			
			// 取得显示样式
			/**
			 * 取得显示样式
			 * 1：取出显示样式默认模板
			 * 2：取得宽度值，从DB或配置中
			 * 3：替换得到最终值
			 */
			// 1:
			$cls = $this->controlsConfig [$category] ['controllcls'] ['value'];
			// 2:
			$width = $eachControlData [$this->controlsConfig [$category] ['width'] ['key']] ? $eachControlData [$this->controlsConfig [$category] ['width'] ['key']] : $this->controlsConfig [$category] ['width'] ['value'];
			// 3:
			$cls = parameReplace ( '#width#', $width, $cls );
			
			$tpl = $this->controls [$category] ['showhtml'];
			$id = $eachControlData[$this->controlsConfig [$category] ['primary']['key']];
			// 获取显示数据配置项，
			$dataSouceObj = M('mis_system_design_setting');
			if(!$id){
				$msg = '属性ID未知';
				throw new NullDataExcetion($msg);
			}
			
			$settingSql = "SELECT 
  datasouce.`actionname`,
  datasouce.`functionname` 
FROM
  `mis_system_design_setting` AS setting 
  LEFT JOIN `mis_system_desing_datasource` AS datasouce 
    ON setting.`datasouceid` = datasouce.`id` 
WHERE setting.`masid` = {$id} ";
			
			$dataSouceData = $dataSouceObj->query($settingSql);
			
			$dataSouceHtml='';
			foreach ($dataSouceData as $key=>$val){
				$dataSouceHtml .='{:A(\''.$val['actionname'].'\')->'.$val['functionname'].'()}';
			}
			$dataSouceHtml = $dataSouceHtml ? $dataSouceHtml : '没有配置显示数据';
			// 解析可见内容
			$show = parameReplace ( '$title', $eachControlData [$this->controlsConfig [$category] ['title'] ['key']], $tpl );
			$show = parameReplace ( '$content',$dataSouceHtml, $show );
			$show = parameReplace ( '__URL__', __URL__, $show );
			$eachControlData['showdisplay'] = '{:A(\''.$val['actionname'].'\')->getConfig()}';
			// 转译点位符
			$show = preg_replace_callback ( '/\#+(\w+)\#/', function ($vo) use($eachControlData) {
				$ret = '';
				if ($vo [1]) {
					$ret = $eachControlData [$vo [1]];
				}
				return $ret;
			}, $show );
				// $tag
				$tag = $this->tagIndentity . '=' . $eachControlData [$this->default ['primary'] ['key']];
				$show = parameReplace ( '$cls', $cls, $show );
				$show = parameReplace ( '$tag', $tag, $show );
			return $show;
		} else {
			return '';
		}
	}
	/**
	 * 生成属性编辑
	 * @Title: createPropertyEdit
	 * @Description: todo(这里用一句话描述这个方法的作用)
	 *
	 * @param array $data        	
	 * @throws NullDataExcetion
	 * @author quqiang
	 *         @date 2015年5月13日 下午9:03:33
	 * @throws
	 *
	 */
	protected function createPropertyEdit($data) {
		$msid = $data['masid'];
		if (! is_array ( $data )) {
			$msg = '数据异常，无法生成属性编辑菜单！';
			throw new NullDataExcetion ( $msg );
		}
		// 数据转换
		$eachControlData = self::parse ( $data, false );
		// 属性编辑最终结果
		$html = '';
		// 取得组件类型
		$category = $eachControlData [$this->default ['category'] ['key']];
		$display = 'display:block;';
		foreach ( $this->controlsConfig [$category] as $k => $v ) {
			if (! $v ['show']) {
				$display = 'display:none;';
			} else {
				$display = 'display:block;';
			}
			$names = $v ['name'];
			$values = $eachControlData [$v ['key']];
			$type = $v ['type'];
			$title = $v ['title'];
			/*
			 * 处理name规则生成,
			 * 1:字段名，[~key~]
			 * 2:多行标识 [#primary#]
			 */
			// 不需要生成批量数据功能。
			$names = str_replace ( '[#primary#]', '', $names );
			$names = preg_replace_callback ( '/\~+(\w+)\~/', function ($vo) use($v) {
				if ($vo [1]) {
					return $v [$vo [1]];
				}
			}, $names );
			
			$peditControl = '';
			switch ($type) {
				case 'text' :
					$peditControl = "<input class=\"input_new\" type=\"text\" value=\"{$values}\" name=\"{$names}\">";
					break;
				case 'select' :
					unset ( $op );
					$op = '';
					
					$staticData = $v ['data'];
					$autoData = $v ['dataurl'];
					$op = $staticData;
					if ($staticData && $autoData) {
						$op = $staticData;
					} elseif (! $staticData && $autoData) {
						$op = call_user_func ( array (
								$this,
								$autoData
						) ,
								$msid
						);
					}
					
					$ophtml = self::getOption ( $op, $values );
					$peditControl = <<<EOF
					<select class="select2 select_elm" name="{$names}">
							{$ophtml}
					</select>
EOF;
					break;
				case 'radio' :
					break;
				case 'checkbox' :
					break;
				case 'dialog' :
					$dialogUrl = $v['data'];
					$peditControl = '<a href="'.$dialogUrl.'" rel="DesingForm"  target="dialog">编辑</a>';
					break;
				default :
					break;
			}
			
			
			$tempHtml .=<<<EOF
			
			<div class="col_1_3 form_group_lay" style="{$display}">
                <label class="label_new">{$title}:</label>
                {$peditControl}
            </div>
EOF;
			
			$tempHtml = preg_replace_callback ( '/\#+(\w+)\#/', function ($vo) use($eachControlData) {
				$ret = '';
				if ($vo [1]) {
					$ret = $eachControlData [$vo [1]];
				}
				return $ret;
			}, $tempHtml );
			
		}
		return $tempHtml;
	}
	
	/**
	 * 生成选项
	 * @Title: getOption
	 * @Description: todo(这里用一句话描述这个方法的作用)
	 *
	 * @param unknown $data        	
	 * @param unknown $selected        	
	 * @param string $type        	
	 * @author quqiang
	 *         @date 2015年5月13日 下午9:15:39
	 * @throws
	 *
	 */
	private function getOption($data, $selected, $type = 'select') {
		$data = explode ( '|', $data );
		$html = '';
		foreach ( $data as $k => $v ) {
			$item = explode ( '#', $v );
			$checked = '';
			if ($selected == $item [0]) {
				$checked = 'selected';
			}
			$html .= '<option value="' . $item [0] . '" ' . $checked . '>' . $item [1] . '</option>';
		}
		return $html;
	}
	function navData($msid) {
		$obj = D ( 'MisSystemDesignProperty' );
		$map ['category'] = 'navigation';
		$map ['masid'] = $msid;
		$data = $obj->where ( $map )->order ( 'sort asc' )->select ();
		if ($data) {
			unset ( $ret );
			foreach ( $data as $k => $v ) {
				$ret [] = $v ['id'] . '#' . $v ['title'];
			}
			array_unshift ( $ret, '#请选择' );
			return join ( '|', $ret );
		}
		return '';
	}
}