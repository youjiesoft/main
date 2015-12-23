<?php
/**
 * 创建图标类组件
 * 莫小明
 * 2011-3-27
 */
class Chart {
	//xmlWriter对象变量定义
	private $xmlWriter = NULL;
	//根节点的属性定义
	private $graphAttribute = NULL;
	//系列(多个系列时,指定之)
	private $series = NULL;
	//x坐标
	private $x = NULL;
	//y坐标
	private $y = NULL;
	//根节点
	private static $GRAPH = "graph";
	//系列节点
	private static $CATEGORIES = "categories";
	//子系列
	private static $CATEGORY = "category";
	//data set
	private static $DATASET = "dataset";
	//data set 节点属性
	private static $SERIESNAME = "seriesName";
	private static $SET = "set";
	private static $NAME = "name";
	private static $LABEL = "label";
	private static $VALUE = "value";
	//-----------------------大系列
	//单系列(适用于：柱状图2d、柱状图3d、饼图2D、饼图3D、曲线图)
	public static  $SINGLE_SERIES = "SINGLE_SERIES";
	//多系列\混合系列\混合（x+y）\xyplot系列\滚动系列(适用于：柱状图2d、柱状图3d、饼图2D、饼图3D、曲线图)
	public static  $MULTI_SERIES_CHARTS = "MULTI_SERIES_CHARTS";
	/**
	 * 构造方法
	 *
	 */
	public function __construct(){
		$this->x = NULL;
		$this->y = NULL;
		$this->series = NULL;
		$this->graphAttribute = array();
		$this->xmlWriter = new XMLWriter();
	}
	//设置根节点属性值
	public function setGraphAttribute($graphAttribute){
		$this->graphAttribute = $graphAttribute;
	}
	//设置x
	public function setX($x){
		$this->x = $x;
	}
	//设置Y
	public function setY($y){
		$this->y = $y;
	}

	//设置系列
	public function setSeries($series){
		$this->series = $series;
	}
	/**
	 * 创建图表对象
	 */
	public  function builderChart($chart_type, $data){
		$this->setDocument($this->xmlWriter);
		//根节点
		$this->xmlWriter->startElement(Chart::$GRAPH);
		//节点信息设置
		if(isset($this->graphAttribute)){
			foreach($this->graphAttribute as $key=>$value){
				$this->xmlWriter->writeAttribute($key, $value);
			}
		}
		switch($chart_type){
			//单系列
			case   Chart::$SINGLE_SERIES :
				$this->create_set($data);
				break;
		  // 其他
			case   Chart::$MULTI_SERIES_CHARTS:
				$this->create_mult($data);
				break;
			default: break;
		}
		//结束根节点
		$this->xmlWriter->endDocument();
		$this->xmlWriter->flush();
		unset($this->xmlWriter);
	}
	/**
	 * 创建多系列dataset数据
	 * $data 数据对象
	 */
	private function create_mult($data){
		//1.取X
		if(!empty($this->x)){
			//----------------------------------------1 创建系列
			$this->xmlWriter->startElement(Chart::$CATEGORIES);
			//取得系列列表
			$seriesList = $this->getDataList($data, $this->series);
			asort($seriesList);
			//循环遍历创建
			foreach($seriesList as $value){
				$this->xmlWriter->startElement(Chart::$CATEGORY);
				$this->xmlWriter->writeAttribute(Chart::$NAME, $value);
				//$this->xmlWriter->writeAttribute(Chart::$LABEL, $value);
				$this->xmlWriter->endElement();
			}
			$this->xmlWriter->endElement();
			//----------------------------------------2 创建data set
			$dataSetList = $this->getDataList($data, $this->x);
			foreach($dataSetList as $dvalue){
				$this->xmlWriter->startElement(Chart::$DATASET);
				$this->xmlWriter->writeAttribute(Chart::$SERIESNAME, $dvalue);
				//循环设置set
				//循环遍历创建
				foreach($seriesList as $svalue){
					//创建set
					$this->create_mult_set($data,$svalue,$dvalue);
				}
				$this->xmlWriter->endElement();
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
			if($v[$this->series]==$svalue
					&& $v[$this->x]==$dvalue){
				$value = $value+$v[$this->y];
			}
		}
		//设置value的值
		$this->xmlWriter->startElement(Chart::$SET);
		$this->xmlWriter->writeAttribute(Chart::$VALUE,$value);
		$this->xmlWriter->endElement();
	}
	/**
	 * 创建单系列set数据
	 * $data 数据对象
	 */
	private function create_set($data){
		foreach ($data as $k => $v) {
			$this->xmlWriter->startElement(Chart::$SET);
			if(!empty($this->x)){
				$this->xmlWriter->writeAttribute(Chart::$NAME,$v[$this->x]);
			}
			if(!empty($this->y)){
				$this->xmlWriter->writeAttribute(Chart::$VALUE,$v[$this->y]);
			}
			$this->xmlWriter->endElement();
		}
	}
	/**
	 * 设置XML头信息
	 */
	private function setDocument($writer){
		$writer->openURI('php://output');
		$writer->startDocument('1.0','UTF-8');
		$writer->setIndent(4);
	}
}
?>