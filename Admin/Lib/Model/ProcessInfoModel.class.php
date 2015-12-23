<?php
/**
 * @Title: ProcessInfoModel 
 * @Package package_name
 * @Description: (系统流程信息模型) 
 * @author 黎明刚
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015年2月10日 下午2:31:51 
 * @version V1.0
 */
class ProcessInfoModel extends CommonModel {
	protected $trueTableName = 'process_info';
	/*
	 * 数据自动填充
	 */
	public $_auto = array (
			array ('createid','getMemberId',self::MODEL_INSERT,'callback' ),
			array ('updateid','getMemberId',self::MODEL_UPDATE,'callback' ),
			array ('createtime','time',self::MODEL_INSERT,'function'),
			array ('updatetime','time',self::MODEL_UPDATE,'function'),
		    array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),

	);
	
	// 数据验证
	public $_validate = array (
			// 多字段组合验证
			array ('code','','数据有重复，请检查！',self::MUST_VALIDATE,'unique',	self::MODEL_BOTH ) 
	); 
	
	/**
	 * @Title: getProcessInfo
	 * @Description: 获取流程信息方法
	 * @param string 模型名称 $modelname        	
	 * @param array模型post数据 $data        	
	 * @return 返回流程审核人，节点信息
	 * @author liminggang
	 * @date 2014-9-29 下午4:11:31
	 * @throws
	 */
	public function getProcessInfo($modelname,$catgory = 1) {
		//第一步，根据modelname获取一个默认流程
		$map = array ();
		$map ['nodename'] = $modelname;
		$map ['status'] = 1;
		$map ['default'] = 1;//默认流程
		$map ['catgory'] = $catgory;//流程类型 1为固定流程，2为变更流程
		$InfoVo = $this->where ( $map )->find();
		if ($InfoVo) {
			return $InfoVo;
		} else {
			// 未查找到流程
			return false;
		}
	}
	/**
	 * @Title: getFlowAuitNode
	 * @Description: 审核节点过滤
	 * @param array $nodelist 所有审核节点数组
	 * @param $sign $project 默认为true,表示清理开始和判断节点。false则不清理
	 * @return array 返回清理过后的节点数组
	 * @author 黎明刚
	 * @date 2014年10月28日 上午10:57:40
	 * @throws
	 */
	public function getFlowAuitNode($nodelist,$data,$sign=1){
		foreach($nodelist as $rkey=>$rval){
			//是审批流程节点才进行清理。
			if($rval['tablename'] == "process_info"){
				if($rval['flowtype'] == 0){
					//unset掉开始节点
					if($sign){
						unset($nodelist[$rkey]);
					}
				}
				//获取条件，进行条件解析
				if($rval['flowtype'] == 1){
					//所有子级节点值
					$processall = array_filter(explode(";",$rval['processall']));
					$guizeCount = count($processall);
					//规则(sql型)
					//$rules = explode(",",$rval['processrules']);
					//规则(条件数组)
					$rulesinfo = explode(",", $rval['processrulesinfo']);
					$num = 0;
					if(count($rulesinfo)>0){
						foreach($rulesinfo as $key=>$val){
							//解密，反序列化
							$derulesinfo = unserialize ( base64_decode(base64_decode ( $val )));
							unset($derulesinfo['mapsql']);
							$bool = $this->getRuleInfo ( $derulesinfo, $data );
							if($bool){
								unset($processall[$key]);
								break;
							}
							++$num;
						}
					}
					//unset掉未满足条件的审批节点
					foreach($nodelist as $unkey=>$unval){
						foreach($processall as $alkey=>$alval){
							if(in_array($unval['flowid'], explode(",", $alval))){
								if(!$sign){
									unset($nodelist[$unkey]);
								}else{
									$nodelist[$unkey]['doing'] = 0; //关闭非执行节点。(存在动态变动的执行节点)
								}
							}
						}
					}
				}
			}
		}
		return $nodelist;
	}
	/**
	 * @Title: getUser
	 * @Description: todo(这里用一句话描述这个方法的作用) 
	 * @param unknown $bactchvo
	 * @param unknown $data
	 * @return string  
	 * @author 黎明刚
	 * @date 2015年2月10日 下午7:59:51 
	 * @throws
	 */
	public function getUser($bactchvo,$vo){
		$userStr = "";
		$map = array ();
		switch ($bactchvo ['userobjid']) {
			case 1 : // 后台用户
				$userStr = $bactchvo ['userobj'];
				break;
			case 2 : // 职级
				$map ['dutyid'] = array ( " in ", $bactchvo ['userobj'] );
				$userArr = M ( "user_dept_duty" )->where ( $map )->getField ( "id,userid" );
				$userStr = implode ( ",", $userArr );
				break;
			case 3 : // 角色
				$map ['rolegroup_id'] = array ( " in ", $bactchvo ['userobj'] );
				$userArr = M ( "rolegroup_user" )->where ( $map )->group("user_id")->getField ( "user_id",true);
				$userStr = implode ( ",", $userArr );
				break;
			case 4 : // 组织架构
				//获取当前数据部门ID
				$map ['deptid'] = $vo ['departmentid'];
				$map ['rolegroup_id'] = $bactchvo ['userobj'];
				$userStr = M ( "mis_organizational_recode" )->where ( $map )->getField ( "userid" );
				break;
			case 5: //按表单字段获取内容
				$userStr = $vo [$bactchvo ['userobj']];
				break;
			case 6://按照表单部门字段获取人员，，（注意，这里的部门数据必须是逗号分隔方式）
				$deptArr = explode(",",$vo [$bactchvo ['userobj']]);
				$userArr = $this->getDeptUserId($deptArr);
				$userStr = implode ( ",", $userArr );
				break;
		}
		return $userStr;
	}
	
	public function getFlowAuitUser($InfoVo,$data = array()){
		//表单流程相关
		$map = array();
		$map['pinfoid'] = $InfoVo ['id'];
		$map['tablename'] = "process_info";
		$map['status'] = 1;
		$ProcessRelationModel = D ( "ProcessRelation" );
		// 查询流程节点的批次信息
		$MisSystemUserBactchModel = D ( "MisSystemUserBactch" );
		// 获取流程节点数据
		$relationList = $ProcessRelationModel->where ( $map )->order("sort asc")->select ();
		if(count($relationList)>0){
			//获取当前流程表单字段
			//读取动态配制
// 			$scdmodel = D('SystemConfigDetail');
// 			$detailList = $scdmodel->getDetail($InfoVo['nodename'],false);
// 			require CONF_PATH . 'property.php';
// 			$prolist = array_merge($NBM_DEFAULTFIELD,$NBM_SYSTEM_FIELD_CONFIG,$NBM_AUDITFELD);
// 			$profieldarr = array_keys($prolist);
// 			foreach($detailList as $key=>$val){
// 				if(in_array($val['name'],$profieldarr)){
// 					unset($detailList[$key]);
// 				}
// 			}
			//获取模型字段数量(方便下方进行判断当前字段是否无需填写，直接审批)
// 			$fieldlenght = count($detailList);
			//如果是表单流程，需要对审核节点进行过滤
			$auditNodeList = $this->getFlowAuitNode($relationList,$data);
			//实例化用户 模型
			$UserModel = D("User");
			if(count($auditNodeList)>0){
				//组合流程数据
				$infoArr = array();
				foreach($auditNodeList as $key=>$val){
// 					$field = $val['filterwritsetempty'].",".$val['filterreadsetempty'];
// 					$fieldlng = array_unique(array_filter(explode(",", $field)));
					//查询知会人信息
					$infoperson = $UserModel->getSelectUserList("ProcessManage",$val ['id']);
					$informpersonid = "";
					if($infoperson){
						$informpersonid = implode(",", $infoperson['sysfields']);
					}
					$infoArr [$key] ['ismobile'] = 0;
// 					if(count($fieldlng) == $fieldlenght && $val['flowtype']!=3){
// 						$infoArr [$key] ['ismobile'] = $val['ismobile'];
// 					}
					$infoArr [$key] ['ismobile'] = $val['ismobile']?$val['ismobile']:0;
					$infoArr [$key] ['relationid'] = $val ['id'];
					$infoArr [$key] ['name'] = $val ['name'];
					$infoArr [$key] ['flowid'] = $val ['flowid'];
					$infoArr [$key] ['flowtype'] = $val ['flowtype'];
					$infoArr [$key] ['processto'] = $val ['processto'];
					$infoArr [$key] ['doing'] =  $val ['doing'];
					$infoArr [$key] ['isauto'] =  $val ['isauto'];//是否自动推单
					$infoArr [$key] ['istuihui'] =  $val ['istuihui'];//回退节点
					//条件
					$infoArr [$key] ['processrulesinfo'] = $val ['processrulesinfo'];
					
					$infoArr [$key] ['informpersonid'] = $informpersonid;
					$infoArr [$key] ['sort'] = $val ['sort'];
					$infoArr [$key] ['parallel'] = $val ['parallel'];
					$infoArr [$key] ['document'] = $val ['document'];
					// 子流程节点
					$infoArr [$key] ['isauditmodel'] = $val ['isauditmodel']?$val ['isauditmodel']:'';
					//来源模型
					$infoArr [$key] ['issourcemodel'] = $val ['issourcemodel']?$val ['issourcemodel']:'';
					// 审批节点
					$map = array ();
					$map ['tablename'] = 'process_relation';
					$map ['tableid'] = $val ['id'];
					$map ['status'] = 1;
					$bactchlist = $MisSystemUserBactchModel->where ( $map )->order ( "sort asc" )->select ();
					$userstr = "";
					$bacthname = "";
					$parallel = array();
					foreach ( $bactchlist as $k1 => $v1 ) {
						// 获取解析条件
						$rulesinfo = unserialize ( base64_decode(base64_decode ( $v1 ['rulesinfo'] )) );
						// 对数据和条件进行匹配，获取满足条件的审批批次
						$bool = $this->getRuleInfo ( $rulesinfo, $data );
						if ($bool) {
							// 找到了满足条件的审批人信息
							$newarr = $this->getUser ( $v1, $data );
							if ($newarr) {
								if($val['parallel'] == 2){
									$parallel[] = array(
											'bactch'=>$v1['id'],
											'bactchname'=>$v1['name'],
											'sort'=>$v1['sort'],
											'curAuditUser'=>$newarr,
									);
									/*
									 * 多批次并行，需要找完所有满足条件的批次
									 * 多批次情况，根据批次顺序进行获取，1 2 3   1 1 2 2 3 3  如果循序相同则表示批次中并行加入了串行
									 */
									if($userstr){
										$userstr = $userstr.",".$newarr;
										
									}else{
										$userstr = $newarr;
									}
								}else{
									// 找到用户了就跳出循环
									$userstr = $newarr;
									break;
								}
							}
						}
					}
					$infoArr [$key] ['relation_parallel'] = $parallel;
					$infoArr [$key] ['curAuditUser'] = $userstr;
					$infoArr [$key] ['auditUser'] = $userstr;
					$infoArr [$key] ['alreadyAuditUser'] = "";
				}
				return $infoArr;
			}else{
				return false;
			}
		}else{
			// 未查询到流程节点信息
			return false;
		}
	}
	
	/**
	 * @Title: getRuleInfo
	 * @Description: 根据当前规则条件和表单数据进行对比，返回boolean
	 * @param 规则数组 $rulesinfo 规则数组
	 * @param 单据数据 $data  表单数据或者项目数据
	 * @return boolean 验证当前规则是否满足，满足返回ture，不满足返回false
	 * @author 黎明刚
	 * @date 2014年10月28日 上午10:57:40
	 * @throws
	 */
	public function getRuleInfo($rulesinfo, $data) {
		// 定义一个标志。表示是否有满足条件批次
		$bool = true;
		// 循环批次信息
		foreach ( $rulesinfo as $rkey => $rval ) {
			$field = $rval [0] ['name'];
			// 判断控件属于什么类型
			switch ($rval [0] ['control']) {
				case 'text' :
					$symbol = $rval [0] ['symbol'];
					// 获取运算符
					$operator = getSelectByName ( $rval [0] ['widget'], $rval [0] ['symbol'] );
					// 获取目标值
					$goalVal = $rval [0] ['val'];
					// 获取对比值
					$fieldVal = $data [$field];
					// 将对比值，目标值，和运算符组合成PHP可执行的判断语句
					if ($symbol == 3) { // 包含（in）
					    // 在字符串中查找，是否包含对比值
						if (! stripos ( $goalVal, $fieldVal )) {
							$bool = false;
							continue;
						}
					} elseif ($symbol == 4) { // "not in"
						if (stripos ( $goalVal, $fieldVal )) {
							$bool = false;
							continue;
						}
					}else{
						$goalVal = $goalVal?$goalVal:0;
						$fieldVal = $fieldVal?$fieldVal:0;
						// 字符串链接
						$str =$goalVal. $operator . $fieldVal;
						$str= str_replace (array ('&quot;','&#39;','&lt;','&gt;'), array ('"', "'",'<','>'),$str);
						$boolean = eval ( " return $str;" );
						if (!$boolean) { // '!=','<','<=','>','>=',
							$bool = false;
							continue;
						}
					}
					break;
				case 'select' :
					$symbol = $rval [0] ['symbol'];
					// 获取运算符
					$operator = getSelectByName ( $rval [0] ['widget'], $rval [0] ['symbol'] );
					// 获取目标值
					$goalVal = $rval [0] ['val'];
					// 获取对比值
					$fieldVal = $data [$field];
					// 将对比值，目标值，和运算符组合成PHP可执行的判断语句
					if ($symbol == 3) { // "in"
					    // 在字符串中查找，是否包含对比值
						if (! in_array ( $fieldVal, $goalVal )) {
							$bool = false;
							continue;
						}
					} elseif ($symbol == 4) { // "not in"
						if (in_array ( $fieldVal, $goalVal )) {
							$bool = false;
							continue;
						}
					}else{
						$goalVal = $goalVal?$goalVal:0;
						$fieldVal = $fieldVal?$fieldVal:0;
						// 字符串链接
						$str =$goalVal. $operator . $fieldVal;
						$str= str_replace (array ('&quot;','&#39;','&lt;','&gt;'), array ('"', "'",'<','>'),$str);
						$boolean = eval ( " return $str;" );
						if (!$boolean) { // '!=','<','<=','>','>=',
							$bool = false;
							continue;
						}
					}
					break;
				case 'number' :
					// 获取对比值
					$fieldVal = $data [$field] ? $data [$field] : 0;
					// 获取区间值 区间开始值
					$goalVals = $rval [0] ['vals'];
					$symbols = $rval [0] ['symbols'];
					// 区间结束值
					$goalVale = $rval [0] ['vale'];
					$symbole = $rval [0] ['symbole'];
					
					if ($goalVals) {
						// 表示有值
						if ($symbols == 1) { // =
							if ($fieldVal !== $goalVals) {
								$bool = false;
								continue;
							}
						} else {
							// 获取运算符
							$operator = getSelectByName ( $rval [0] ['widget'], $symbols );
							// 字符串链接
							$str = $fieldVal . $operator . $goalVals;
							$str= str_replace (array ('&quot;','&#39;','&lt;','&gt;'), array ('"', "'",'<','>'),$str);
							$boolean = eval ( " return $str;" );
							if (! $boolean) { // '!=','<','<=','>','>=',
								$bool = false;
								continue;
							}
						}
					}
					if ($goalVale) {
						// 表示有值
						if ($symbole == 1) { // =
							if ($fieldVal !== $goalVals) {
								$bool = false;
								continue;
							}
						} else {
							// 获取运算符
							$operator = getSelectByName ( $rval [0] ['widget'], $symbole );
							// 字符串链接
							$str = $fieldVal . $operator . $goalVale;
							$str= str_replace (array ('&quot;','&#39;','&lt;','&gt;'), array ('"', "'",'<','>'),$str);
							//$str = str_replace ( "&#39;", "'", html_entity_decode ( $str ) );
							$boolean = eval ( " return $str;" );
							if (! $boolean) { // '!=','<','<=','>','>=',
								$bool = false;
								continue;
							}
						}
					}
					break;
				case 'time' :
					// 获取对比值
					$fieldVal = $data [$field] ? $data [$field] : 0;
					// 获取区间值
					$begintime = strtotime ( $rval [0] ['vals'] );
					$symbols = $rval [0] ['symbols'];
					
					$endtime = strtotime ( $rval [0] ['vale'] );
					$symbole = $rval [0] ['symbole'];
					
					if ($begintime) {
						// 表示有值
						if ($symbols == 1) { // =
							if ($fieldVal !== $begintime) {
								$bool = false;
								continue;
							}
						} else {
							// 获取运算符
							$operator = getSelectByName ( $rval [0] ['widget'], $symbols );
							// 字符串链接
							$str = $fieldVal . $operator . $begintime;
							$str= str_replace (array ('&quot;','&#39;','&lt;','&gt;'), array ('"', "'",'<','>'),$str);
							$boolean = eval ( " return $str;" );
							if (! $boolean) { // '!=','<','<=','>','>=',
								$bool = false;
								continue;
							}
						}
					}
					if ($endtime) {
						// 表示有值
						if ($symbole == 1) { // =
							if ($fieldVal !== $endtime) {
								$bool = false;
								continue;
							}
						} else {
							// 获取运算符
							$operator = getSelectByName ( $rval [0] ['widget'], $symbole );
							// 字符串链接
							$str = $fieldVal . $operator . $endtime;
							$str= str_replace (array ('&quot;','&#39;','&lt;','&gt;'), array ('"', "'",'<','>'),$str);
							$boolean = eval ( " return $str;" );
							if (! $boolean) { // '!=','<','<=','>','>=',
								$bool = false;
								continue;
							}
						}
					}
					break;
				default :
					break;
			}
		}
		return $bool;
	}
	
	
	
	public function getnode($pid = 0) {
		$model = D ( "ProcessTemplate" );
		$where = " 1=1 ";
		if ($pid) {
			$where .= " and ptmptid='" . $pid . "' ";
		}
		$list = $model->where ( $where )->order ( "sort ASC" )->select ();
		return $list;
	}
	/**
	 *根据部门id数组。获取部门经理后台用户ID
	 *
	 */
	public function getDeptUserId($deptarr = array()){
		$manageruserarr = array();
		//获取查询的部门经理
		$manager = "部门经理";
		if(file_exists(DConfig_PATH."/System/list.inc.php")){
			require DConfig_PATH."/System/list.inc.php";
			$manager = $aryRule['部门经理'];
		}
		//指定部门角色为“部门经理”，据此查询角色id
		$roleid = M ( "rolegroup" )->where ( "name='{$manager}' and status=1 and catgory=5" )->getField("id");
		if($roleid){
			$map['status'] = 1;
			$map['rolegroup_id'] = $roleid;
			if($deptarr){
				$map['deptid'] = array(' in ',$deptarr);
			}else{
				$map['deptid'] = $_SESSION['user_dep_id'];
			}
			$manageruserarr = M ( "mis_organizational_recode" )->where ( $map )->getField("userid",true);
		}
		return $manageruserarr;
	}
}
?>
