<?php

/**
 * @Title: MisAutoYlsAction
 * @Package package_name
 * @Description: todo(动态表单_自动生成-路线管理)
 * @author 管理员
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015-06-10 11:57:36
 * @version V1.0
 */
class MisSystemMapTraceAction extends Action {

	public function index(){
		//用户账号
// 		$map['account'] = '15671376666';
// 		$mis_system_map_traceDao = M("mis_system_map_trace");
// 		$maplist = $mis_system_map_traceDao->where($map)->order("id asc")->field("latitude,longitude")->select();
// 		$json_maparr = json_encode($maplist);
// 		$this->assign("json_maparr",$json_maparr);
		
		$this->display();		
	}
	
	/**
	 * 检索单号
	 */
	public function lookupSearch(){
		//检索的单号
		$searchval = trim($_POST['searchval']);
		$MisAutoQecModel = D("MisAutoQec");
		$where = array();
		$where['orderno'] = array("like",'%'.$searchval.'%');
		$vo = $MisAutoQecModel->where($where)->find();
		if($vo){
			$volist[0] = $vo;
			$newarr = $this->getDateListData("MisAutoQec", $volist);
			$vo = $newarr[0];
			//查询到内容后。进行坐标数据查询
			$mis_system_map_traceDao = M("mis_system_map_trace");
			$map['orderno'] = $vo['id'];
			$map['latitude'] = array('gt',0);
			$map['longitude'] = array('gt',0);
			$maplist = $mis_system_map_traceDao->where($map)->order("id asc")->field("latitude,longitude")->group("latitude,longitude")->select();
			$level = 12;
			if($maplist){
				$vo['json_isnull'] = 1;
				//调用计算经纬度距离
				$last = count($maplist)-1;
				$distance = $this->getDistance($maplist[0]['latitude'],$maplist[0]['longitude'],$maplist[$last]['latitude'],$maplist[$last]['longitude']);
				$juli = abs($distance)/1000;
				if($juli<=1){
					$level = 17;
				}else if($juli>1 && $juli<=5){
					$level = 14;
				}else if($juli>5 && $juli<=20){
					$level = 11;
				}else if($juli>20 && $juli <=100){
					$level = 9;
				}else if($juli>100 && $juli <=300){
					$level = 7;
				}else{
					$level = 5;
				}
			}else{
				$vo['json_isnull'] = 0;
			}
			$vo['level'] = $level;
			$vo['distance'] = $distance;
			$vo['json_maparr'] = $maplist;
			$vo['isnull'] = 1;
		}else{
			$vo = array();
			$vo['isnull'] = 0;
		}
		echo json_encode($vo);
	}
	
	/** 
	* @desc 根据两点间的经纬度计算距离 
	* @param float $lat 纬度值 
	* @param float $lng 经度值 
	*/
	function getDistance($lat1, $lng1, $lat2, $lng2) { 
		$earthRadius = 6367000; //approximate radius of earth in meters 
		 
		/* 
		Convert these degrees to radians 
		to work with the formula 
		*/
		 
		$lat1 = ($lat1 * pi() ) / 180; 
		$lng1 = ($lng1 * pi() ) / 180; 
		 
		$lat2 = ($lat2 * pi() ) / 180; 
		$lng2 = ($lng2 * pi() ) / 180; 
		 
		/* 
		Using the 
		Haversine formula 
		 
		http://en.wikipedia.org/wiki/Haversine_formula 
		 
		calculate the distance 
		*/
		 
		$calcLongitude = $lng2 - $lng1; 
		$calcLatitude = $lat2 - $lat1; 
		$stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2); 
		$stepTwo = 2 * asin(min(1, sqrt($stepOne))); 
		$calculatedDistance = $earthRadius * $stepTwo; 
		 
		return round($calculatedDistance); 
	} 
	
	/**
	 * @Title: getDateListData
	 * @Description: todo(根据配置文件list.inc.php，转换数据格式)
	 * @param 模型名称 $modelname
	 * @param 数据二维数组 $volist
	 * @param 字段前缀名 $extend
	 * @author 黎明刚
	 * @return 返回转换后的二维数组
	 * @date 2015年1月13日 下午3:04:47
	 * @throws
	 */
	protected function getDateListData($modelname,$volist){
		//begin
		$scdmodel = D('SystemConfigDetail');
		//读取列名称数据(按照规则，应该在index方法里面)
		$detailList = $scdmodel->getDetail($modelname,false);
		foreach($volist as $key=>$val){
			foreach($detailList as $k1=>$v1){
				if($v1['name'] =="action" || $v1['name'] =="auditState" || strpos(strtolower($v1['name']),"datatable")!==false){
					continue;
				}
				if($val[$v1['name']]){
					if(count($v1['func']) >0){
						$varchar = "";
						foreach($v1['func'] as $k2=>$v2){
							//开始html字符
							if(isset($v1['extention_html_start'][$k2])){
								$varchar = $v1['extention_html_start'][$k2];
							}
							//中间内容
							$varchar .= getConfigFunction($val[$v1['name']],$v2,$v1['funcdata'][$k2],$val);
							if(isset($v1['extention_html_end'][$k2])){
								$varchar .= $v1['extention_html_end'][$k2];
							}
							//结束html字符
						}
						$volist[$key][$v1['name']] = $varchar;
					}
				}
			}
		}
		return $volist;
	}
}
?>
