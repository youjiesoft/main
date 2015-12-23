<?php
/**
 * @Title: MisUserEventsModel
 * @Package package_name
 * @Description: todo(日程记录)
 * @author renling
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-2-28 上午11:10:04
 * @version V1.0
 */
class MisUserEventsModel extends CommonModel{
	protected  $trueTableName = 'mis_user_events';
	public $_auto = array(
			
array('createtime','time',self::MODEL_INSERT,'function'),
			
	array('updatetime','time',self::MODEL_UPDATE,'function'),
			
	array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			
	array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			
	array('startdate','dateToTimeString',self::MODEL_BOTH,'callback'),
			
	array('enddate','dateToTimeString',self::MODEL_BOTH,'callback'),
			
	array('userid','getMemberId',self::MODEL_INSERT,'callback'),
		    
	array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
	
	);

	public $_validate=array(
			array('enddate','dataCompare',"结束日期晚于开始日期",self::VALUE_VAILIDATE,'callback',self::MODEL_BOTH,
					array('$_POST[startdate]')
			),
	);
	
	/**
	 * @Title: getMyEvents
	 * @Description: todo(我的日程管理，在我的桌面日历上面标记我的日程)
	 * @author liminggang
	 * @date 2013-12-30 下午3:19:48
	 * @throws
	 */
	public function getMyEvents(){
		//获取当前登录用户
		$userid=$_SESSION[C('USER_AUTH_KEY')];
		//获取当前登录人的日程
		//$model = D('MisUserEvents');
		//获取当前月份的日程
		$map['_string'] = 'FIND_IN_SET(  '.$userid.',personid ) or userid = '.$_SESSION[C('USER_AUTH_KEY')];
		$map['status'] = 1;
		$list=$this->where($map)->select();
		//第二部，分解日程，特别是一个日程中连续夸天数的。拆分开
		if($list){
			$num = count($list);
			foreach($list as $key=>$val){
				//转移掉反斜杠
				$list[$key]['details'] = preg_replace("|[".chr(1)."-".chr(31)."]+|",'', $val['details']);
				$list[$key]['text'] = preg_replace("|[".chr(1)."-".chr(31)."]+|",'', $val['text']);
				
				$a=strtotime(transTime($val['startdate']));
				$b=strtotime(transTime($val['enddate']));
				$x=($b-$a)/86400;
				if($x){
					for($i = 1;$i<=$x;$i++){
						$val['begintime'] =strtotime("+".$i." day", $a);
						$list[$num] = $val;
						$num++;
					}
				}
				$list[$key]['begintime'] = $a;
			}
		}
		$arr = array();
		//第三部、分解一天中多个日程。
		if($list){
			foreach($list as $k=>$v){
				$str = htmlspecialchars_decode($v['details'], ENT_QUOTES);//转码
				$str = trim(strip_tags(str_replace("&nbsp;", ' ', $str)));//过滤html
				$v['details'] = $str;
				if(!in_array($v['begintime'], array_keys($arr))){
					$arr[$v['begintime']][] = $v;
				}else{
					$arr[$v['begintime']][] = $v;
				}
			}
		}
		//第四部、判断日程属于那种类型，1,2,3
		$azz = array();
		foreach($arr as $k1=>$v1){
			$self = 1;
			foreach($v1 as $k2=>$v2){
				if($v2['userid'] == $userid){
					$self = 1;//自己发
					if(in_array($userid, explode(",", $v2['personid']))){
						$self = 3;
						break;
					}
				}else if(in_array($userid, explode(",", $v2['personid']) && $v2['userid'] != $userid)){
					$self = 2;//别人发的。
				}
				foreach($v2 as $k3=>$v3){
					$v1[$k2][$k3]  = is_string($v3)?   stripslashes($v3)  :  $v3;
				}
			}
			$azz[$k1][$self] = $v1;
		}
		return $azz;
	}
}
?>