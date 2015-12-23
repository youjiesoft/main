<?php
/**
 * 创建Highcharts图标类组件
 * 杨东
 * 2014-2-17
 */
class Highcharts {
	//结果对象变量定义
	private $results = NULL;
	//系列(多个系列时,指定之)
	private $series = NULL;
	//单系列(标题)
	private $title = NULL;
	//x坐标
	private $x = NULL;
	private $xType = NULL;
	//X值
	private $categories = NULL;
	//y坐标
	private $y = NULL;
	private $yType = NULL;

	//extraArr参数,继续扩展额外参数,数组，名称与查询名称一致,最多支持8个元素，可以扩展
	private $extraArr = array();
	//extra参数,单个且指定键值为extra
	private $extraTypeArr = NULL;
	//单系列(适用于：柱状图、饼图、曲线图等)
	public static $SINGLE_SERIES = "SINGLE_SERIES";
	//多系列\混合系列\混合（x+y）\xyplot系列\滚动系列(适用于：柱状图、饼图、曲线图等)
	public static $MULTI_SERIES_CHARTS = "MULTI_SERIES_CHARTS";
	//额外参数图
	public static $EXTRA_SINGLE_SERIES = "EXTRA_SINGLE_SERIES";
	/**
	 * 构造方法
	 */
	public function __construct(){
		$this->x = NULL;
		$this->y = NULL;
		$this->series = NULL;
		$this->title = NULL;
		$this->categories = array();
		$this->results = array();
	}
	//设置x
	public function setX($x){
		$this->x = $x;
	}
	//设置x
	public function setXType($xtype){
		$this->xType = $xtype;
	}
	//设置Y
	public function setY($y){
		$this->y = $y;
	}
	//设置Y类型属性
	public function setYType($ytype){
		$this->yType = $ytype;
	}
	//设置ExtraArr,设置额外参数
	public function setExtraArr($extraArr){		
		$extraArr=array_unique($extraArr);
		//排除数组中名称与y或Y重复的参数名称
		foreach( $extraArr as $key=>$val){
			if($val=="y"  || $val=="x"){
				unset( $extraArr[$key] );
			}
		}
		//数组重排序
		$tempArr=array();
		while ($value = current($extraArr))
		{
			$tempArr[] = $value;
			next($extraArr);
		}
		$this->extraArr = $tempArr;
	}
	//设置ExtraArr,设置额外参数
	public function setExtraTypeArr($extraTypeArr){
		$this->extraTypeArr=$extraTypeArr;
	}
	//设置系列
	public function setSeries($series){
		$this->series = $series;
	}
	//设置单系列标题
	public function setTitle($title){
		$this->title = $title;
	}
	/**
	 * @Title: getCategories
	 * @Description: todo(获取X标题组) 
	 * @param 判断 $true 默认为true，判断是否获取格式为JSON的标题组
	 * @return string|X标题组
	 * @author 杨东 
	 * @date 2014-2-18 上午10:02:21 
	 * @throws 
	*/  
	public function getCategories($true = true){
		if($true){
			return json_encode($this->categories);
		} else {
			return $this->categories;
		}
	}
	/**
	 * @Title: getResults
	 * @Description: todo(获取结果数据集) 
	 * @param 判断 $true 默认为true，判断是否获取格式为JSON的结果数据集
	 * @return string|结果数据集
	 * @author 杨东 
	 * @date 2014-2-18 上午10:04:43 
	 * @throws 
	*/  
	public function getResults($true = true){
		if($true){
			return json_encode($this->results);
		} else {
			return $this->results;
		}
	}
	/**
	 * 创建图表对象
	 */
	public function builderChart($chart_type, $data){
		switch($chart_type){
			//单系列
			case Highcharts::$SINGLE_SERIES :
				$this->create_set($data);
				break;
		  	// 其他
			case Highcharts::$MULTI_SERIES_CHARTS:
				$this->create_mult($data);
				break;
				//额外参数
			case  Highcharts::$EXTRA_SINGLE_SERIES;
				$this->create_extra($data);
				break;
			default: break;
		}
	}
	/**
	 * 创建多系列dataset数据
	 * $data 数据对象
	 */
	private function create_mult($data){
		//1.取X
		if(!empty($this->x)){
			//----------------------------------------1 创建系列
			//取得系列列表
			$seriesList = $this->getDataList($data, $this->series);
			asort($seriesList);
			//循环遍历创建
			//----------------------------------------2 创建data set
			$dataSetList = $this->getDataList($data, $this->x);
			// 循环系列
			foreach($seriesList as $svalue){
				$series = array("name" => $svalue);
				// 循环X坐标
				foreach($dataSetList as $dvalue){
					if(!in_array($dvalue, $this->categories['categories'])){
						$this->categories['categories'][] = $dvalue;
					}
					//创建set
					$series['data'][] = $this->create_mult_set($data,$svalue,$dvalue);
				}
				$this->results[] = $series;
			}
			
		}
	}
	
	/**
	 * 增加额外变量数据
	 * $data 数据对象
	 */
	private function create_extra($data){
		//1.取y轴与附加参数extra
		if(!empty($this->y) || !empty($this->extraArr)){
			$tampArr=array();//初始化临时存储数组
			foreach($data as $key=> $val){
				foreach($val as $subkey =>$subval){
	                switch ($subkey){
	                	//获取X轴，此处没对X轴进行分组，如果有分组需求，可以对这个数组进行扩展，排除重复数据
	                	case $this->x:
                			 $this->categories['categories'][] = $this->typeConvert($subval,$this->xType);	                 
	                	break;
	                	case $this->y:
                             $tampArr['y']=$this->typeConvert($subval,$this->yType);
	                    break;
	                    case $this->extraArr[0]:
	                    	$tampArr[$this->extraArr[0]]=$this->typeConvert($subval,$this->extraTypeArr[0]);
	                    break;
	                    case $this->extraArr[1]:
	                    	$tampArr[$this->extraArr[1]]=$this->typeConvert($subval,$this->extraTypeArr[1]);
	                    break;
	                    case $this->extraArr[2]:
	                    	$tampArr[$this->extraArr[2]]=$this->typeConvert($subval,$this->extraTypeArr[2]);
	                    break;
	                    case $this->extraArr[3]:
	                    	$tampArr[$this->extraArr[3]]=$this->typeConvert($subval,$this->extraTypeArr[3]);
	                    break;
	                    case $this->extraArr[4]:
	                    	$tampArr[$this->extraArr[4]]=$this->typeConvert($subval,$this->extraTypeArr[4]);
	                    break;
	                    case $this->extraArr[5]:
	                    	$tampArr[$this->extraArr[5]]=$this->typeConvert($subval,$this->extraTypeArr[5]);
	                    break;
	                    case $this->extraArr[6]:
	                    	$tampArr[$this->extraArr[6]]=$this->typeConvert($subval,$this->extraTypeArr[6]);
	                    break;
	                    case $this->extraArr[7]:
	                    	$tampArr[$this->extraArr[7]]=$this->typeConvert($subval,$this->extraTypeArr[7]);
	                    break;
			            default: break;
	               }
				}
				$this->results[]=$tampArr;
			} 
		}
	}
	//取得某关键的数据列表(唯一)
	private function getDataList($data,$key){
		$list = array();
		//循环获取
		foreach ($data as $k => $v) {
			//判断是否已经存在
			if(!in_array($v[$key],$list)){
				array_push($list,$v[$key]);
			}
		}
		return $list;
	}
	//创建多系列的set节点数据
	private function create_mult_set($data,$svalue,$dvalue){
		$value = 0 ;
		foreach ($data as $k => $v) {
			//判断是否已经存在
			if($v[$this->series]==$svalue && $v[$this->x]==$dvalue){
				$value = $value + intval($v[$this->y]);
			}
		}
		return $value;
	}
	/**
	 * 创建单系列set数据
	 * $data 数据对象
	 */
	private function create_set($data){
		if(!empty($this->x)){
			$series = array();
			$series = array("name" => $this->title);
			foreach ($data as $k => $v) {
				$this->categories['categories'][] = $v[$this->x];
				if(!empty($this->y)){
					$series['data'][] = array($v[$this->x],intval($v[$this->y]));
				}
			}
			$this->results[] = $series;
		} else if(!empty($this->series)){
			$this->categories['categories'][] = $this->title;
			foreach ($data as $k => $v) {
				$series = array();
				$series["name"] = $v[$this->series];
				if(!empty($this->y)){
					$series['data'][] = intval($v[$this->y]);
				}
				$this->results[] = $series;
			}
		}
	}
	function tags($value = ''){
		return F('common', $value, './Lang/zh-cn/');
	}
	
	//paramate source:数据源
	//paramate param  数据类型处理
	function typeConvert($source,$param=null){
		if(!empty($source) && !empty($param)){
		    $type=null;//初始化类型
		    $tempArr=null;//初始化承载值
			$returnData=null;//初始化返回值
			if(is_string($param)){
				$tempArr=array();
				$tempArr=explode("|",$param);
				$type=$tempArr['0'];
			}else if(is_array($param)){
				$type=$param['type'];
			}
			switch ($type){
				case 'time':
					$returnData=transTime($source);
					break;
				case 'bool':
					$returnData=(boolean)$source;
					break;
				case 'int':
					$returnData=(int)$source;
					break;
				case 'float':
					//还可以扩展参数
					$returnData=round(floatval($source),2);
					break;
				case 'string':
					$returnData=(string)$source;
					break;
				case 'array':
					$returnData=(array)$source;
					break;
				case 'object':
					$returnData=(object)$source;
					break;
				case 'null':
					$returnData=NULL;
					break;
				default:
					$returnData=$source;
					break;
			}
			return 	$returnData;
		}else{
			return  $source;
		}
	}
}
?>