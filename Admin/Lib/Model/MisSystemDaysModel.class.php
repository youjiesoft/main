<?php

class MisSystemDaysModel extends CommonModel {
	protected $trueTableName = 'mis_system_days';
	/**
	 * @Title: getWorkDay
	 * @Description: todo(获取某一时间几个工作日后的日期返回)
	 * @param $start  起始时间
	 * @param $days   几个工作日
	 * @param $intoType     输入的日期格式 1时间戳  2.data格式 
	 * @param $worktype     是否工作日，默认为是1
	 * @param $today        计算当日，默认为1
	 * return 数组记录   
	 * @author yangxi
	 * @date 2014-10-24 上午11:00:00
	 * @throws
	 */
	public function getWorkDay($start,$days,$intoType=1,$worktype=1,$today=1){
		$dateInfo=array();
		if(!$start && !$days){
			return false;
		}else{
			//初始化查询条件
			$map1=array();//定位到开始日期
			$map2=array();//定位到结束日期
			//查询类型判断	
		   switch($intoType){
		   	case 1:
		   		//传入进来的时间戳。需要转换成当前日期
				$start = strtotime(date("Y/m/d",$start));
		   		$map1['unixdate']=$start;
		   	break;
		   	case 2:
		   		$map1['standdate']=$start;
		   	break;
		
		   }
		   switch($worktype){
		   	case 1:
		   		$map2['type']=1;
		   		break;
		   	case 2:
		   		break;
		   
		   }	
		   //获取当前日期的ID值				
			$daysList=$this->where($map1)->find();
            //一次日期扣减，一次数组扣减,因为要算当天，昨天跟明天，这里要+1；
            $today?$days=$days+1:$key=$days;
            $key=1;//设置初始值
            $key=$days-1;
            $map2['id']=array('EGT',$daysList['id']);
			$result=$this->where($map2)->limit($days)->select();	
			if($result!==false){
				$dateInfo['preday']=$result[$key-2];
				$dateInfo['day']=$result[$key-1];
				$dateInfo['nextday']=$result[$key];
				return $dateInfo;
			}else{
				return false;
			}
		}
	}	
	/**
	 * @Title: getWorkDayNum
	 * @Description: todo(获取两个日期间的工作日期差)
	 * @param $start  1、数据插入     2、数据展示
	 * @param $end        重排序 1为是，0为否
	 * @param $intoType     输入的日期格式 1时间戳  2.data格式 
	 * @param $worktype     是否工作日，默认为是1
	 * @param $intoType
	 * @author yangxi
	 * @date 2014-10-24 上午11:00:00
	 * @throws
	 */	
	public function getWorkDayNum($start,$end,$intoType=1,$worktype=1,$today=1){
		if(!$start && !$days){
			return false;
		}else{
			//初始化查询条件
			$map=array();//定位到开始日期
			//查询类型判断
			switch($intoType){
				case 1:
					//传入进来的时间戳。需要转换成当前日期
					$start = strtotime(date("Y/m/d",$start));
					$end = strtotime(date("Y/m/d",$end));
					$map['unixdate']  = array('between',array($start,$end));
				break;
				case 2:
					$map['standdate']  = array('between',array($start,$end));
				break;
		
			}
			switch($worktype){
				case 1:
					$map['type']=1;
					break;
				case 2:
					break;
					 
			}
			$result=$this->where($map)->count("id");
			//减去当日
			$today?$result:$result=$result-1;
				if($result!==false){
					return $result;
				}else{
					return false;
				}					
		}
	}
	
}
?>