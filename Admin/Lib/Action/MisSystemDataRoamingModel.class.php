<?php
class MisSystemDataRoamingModel extends CommonModel{
	protected $trueTableName = 'mis_system_data_roaming';

	
	public  function main($type,$sourcemodel,$pkValue,$sourcetype,$targetmodel){
		//漫游表验证是否要漫游
		switch($type){
			//普通漫游
			case 1:
				$dataRoamMas=$this->dataRoamMasCheck($isbindsettable=0,$sourcemodel,$pkValue,$sourcetype,$targetmodel);				
				break;
			//套表漫游
			case 2:
				$dataRoamMas=$this->dataRoamMasCheck($isbindsettable=1,$sourcemodel,$pkValue,$sourcetype,$targetmodel);
				break;
			//子流程漫游
			case 3:
				$dataRoamMas=$this->dataRoamMasCheck($isbindsettable=2,$sourcemodel,$pkValue,$sourcetype,$targetmodel);				
				break;

		}
		//print_R($dataRoamMas);
		//没有值，中断返回
		if($dataRoamMas===false)return;
		
		foreach($dataRoamMas as $roamMasKey => $roamMasVal){
			//首先通过主模型核验是否需要执行，不执行就直接中断，执行就赋值给主模型对象及通过它获取视图对象结果
			$sourceMainTable=D($roamMasVal['sourcemodel'])->gettablename();
			$canDataRoaming=$this->dataRoamSourceObject($sourceMainTable,$pkValue,$roamMasVal);
			//print_R($canDataRoaming);
			//如果返回false直接中断并继续返回false;
			if($canDataRoaming===false)continue;
			//获取来源数据并分流
			$mainObject=array();
			//数据源对象
			$tableObj=json_decode($roamMasVal['strelation'],true);
            //print_r($tableObj);
			//循环对象
			$cycleObj=$roamMasVal['cycle'];
			$cycleNum=1;//初始化循环次数
			foreach($tableObj as $key => $val){
				//目标对象类型 1为主表 2为内嵌表
				$targetObjType=$val['targettable']['tablecategory'];
				//目标对象表
				$targetObjTable=$val['targettable']['name'];
				//来源数据源类型 1为主表 2为内嵌表 3为视图
				$sourceObjType=$val['sourcetable']['tablecategory'];
				//来源数据源对应表
				$sourceObjTable=$val['sourcetable']['name'];
				switch($sourceObjType){
					//主表
					case 1:
						
						//主表与来源模型对比，如果相同
						if($sourceMainTable==$sourceObjTable){
						    $object=$canDataRoaming;
						}else{
							$object=$this->dataRoamSourceObject($sourceObjTable,$pkValue);
						}
						break;
				    //内嵌表
					case 2:
						$object=$this->dataRoamSublistObject($sourceObjTable,$pkValue);
						break;
					//视图
					case 3:
						//视图需要循环体，1视图本身多少条记录就循环多少次 2指定一个循环对象
						$object=$this->dataRoamViewObject($sourceObjTable,$roamMasVal['id'],$canDataRoaming);
						//$object="";
// 						$cycleNumView=count($object);
// 						//对数据进行循环赋值
// 						for($i=1;$i<$cycleNumView;$i++){
// 							$object[$i]=$object[0];
// 						}
						break;
				}
				//获取循环插入次数，这里是主表单生成单据次数
				if($cycleObj==$sourceObjTable){
					$cycleNum=count($object);
				}
				//print_r($object);
				//存在object值时
				if($object){
				$mainObject[$targetObjTable]['sourcetable']=$sourceObjTable;
				$mainObject[$targetObjTable]['data']=$object;
				$mainObject[$targetObjTable]['sourceobjtype']=$sourceObjType;
				$mainObject[$targetObjTable]['targetobjtype']=$targetObjType;
				}
			}
			//print_r($mainObject);
			//获取子表字段对应信息
			$roamSubVal=$this->dataRoamSubCheck($roamMasVal['id']);
			//$sourcetype=4;
			switch($sourcetype){
				//漫游生单，这个是不可能有循环的
				case 4:
					$result=$this->dataRoamOrderno($mainObject,$roamMasVal,$roamSubVal);
					return $result;
					break;
					//静默漫游
				default:
					//当为子流程漫游时获取projectworkid
					if($type==3){
						$getProjectWorkid=true;
					}
					//开始循环多次生成单据
					for($i=0;$i<$cycleNum;$i++){
					//数据源，漫游主表id
					unset($sqlArr);
					$sqlArr=array();
					$sqlArr=$this->dataRoam($mainObject,$roamMasVal,$roamSubVal,$getProjectWorkid);
					$result=$this->dataRoamSqlExecute($sqlArr,$roamMasVal['id'],$roamMasVal['sourcemodel'],$pkValue);
					}
					break;
			}			
	  }
	}
	/**
	 +----------------------------------------------------------
	 * 数据漫游检查
	 +----------------------------------------------------------
	 * @access dataRoamCheck
	 +----------------------------------------------------------
	 * @param string $do    1是点击触发  2是数据自动穿透（钩子）
	 +----------------------------------------------------------
	 * @param string $type  执行范围 （‘update,insert,delete’）
	 +----------------------------------------------------------
	 * @param string $do  当即执行标示
	 +----------------------------------------------------------
	 * @return false|integer
	 +----------------------------------------------------------
	 */	
	public function dataRoamMasCheck($isbindsettable,$sourcemodel,$pkValue,$sourcetype,$targetmodel){
		switch($sourcetype){
			case 4:
				//生单漫游
				$masModel=D("MisSystemDataRoamMas");
				//主表数据
				$map=array();
				$map['sourcemodel']=$sourcemodel;
				if($targetmodel){
				$map['targetmodel']=$targetmodel;
				}	
				$map['sourcetype']=$sourcetype;
				$map['isbindsettable']=$isbindsettable;
				$map['status']=1;
				
				$masResult=$masModel->where($map)->select();
				//print_r($masModel->getLastSql());
	        break;
	        //静默漫游
			default:
				//实例化对象
				$masModel=D("MisSystemDataRoamMas");
				$map=array();
				
				//$map['sourcemodel']=$model->getModelName();
				$map['sourcemodel']=$sourcemodel;
				if($targetmodel){
					$map['targetmodel']=$targetmodel;
				}
				$map['isbindsettable']=$isbindsettable;
				$map['status']=1;
				$map['_string'] = 'FIND_IN_SET('.$sourcetype.',sourcetype)';
				$masResult=$masModel->where($map)->select();
				//print_r($masModel->getLastSql());
			break;

		}
		//print_r($masResult);
		return $masResult;
	}
	public function dataRoamSubCheck($roamMasid){
		$map['masid']=$roamMasid;
		$map['status']=1;
		$subResult=M('mis_system_data_roam_sub')->where($map)->order('tsort desc')->select();
		//print_r(M('mis_system_data_roam_sub')->getLastSql());
		if($subResult===false)return false;
		//以targettable为key重新复制
		$object=array();
		$group="";//分组初始化
		$num=0;//key初始化
		foreach($subResult as $key => $val){
			if($val['targettable']!=$group){
				$group=$val['targettable'];
				$num=0;
				//开始新的分组
				$object[$group]['sourcetable']=$val['sourcetable'];
				//并将当前数据压入
				$object[$group]['data'][$num]=$val;
			}else{
				//直接将当前数据压入
				$object[$group]['data'][$num]=$val;
			}
			$num++;
		}
		return 	$object;
	}
	public function dataRoamObjectCount($tableObj,$pkValue){
		
	}	
	public function dataRoamSourceObject($tableObj,$pkValue,$roamMasVal){
		//获取数据源记录形成对象
		$map=array();
		$map['id']=$pkValue;
		$map['status']=1;
		$roamMasVal['rules']?$map['_string']=$roamMasVal['rules']:$map['_string']="1=1";
		//根据来源模型来做判断是否能执行数据漫游
 		$object=M($tableObj)->where($map)->select();
//      print_r($object);
// 		print_r(M($tableObj)->getLastSql()); 
		if(!$object){
			return false;		
		}else{
			return $object;
		}
	}
	//内嵌表对象
	public function dataRoamSublistObject($tableObj,$pkValue){
		//获取数据源记录形成对象
        $map="";
		$map['masid']=$pkValue;
		//内嵌表好像没有status字段呢
		//$map['status']=1;
		$object = M( $tableObj )->where($map)->select ();
		//print_r($object);
		//print_r(M($tableObj)->getLastSql());
		if(!$object){
			return false;
		}else{
			return $object;
		}
	}
	
	//内嵌表对象
	public function dataRoamSublistObjectCheck($tableObj,$pkValue){
		//获取数据源记录形成对象
		$map="";
		$map['masid']=$pkValue;
		//内嵌表好像没有status字段呢
		//$map['status']=1;
		$object = M( $tableObj )->where($map)->select ();
		//print_r($object);
		//print_r(M($tableObj)->getLastSql());
		if(!$object){
			return false;
		}else{
			return $object;
		}
	}
	//视图对象
	private function dataRoamViewObject($tableObj,$roamMasID,$sourceObject){
		//首先获取视图关联关系
		$whereArr="";
		$map=array();
		$map['masid']=$roamMasID;
		$map['viewtable']=$tableObj;
		$whereArr=M('mis_system_data_roam_relation_view')->where($map)->select();
		//print_r(M('mis_system_data_roam_relation_view')->getLastSql());
		$where="";
		//初始化whereArray条件
		//print_r($whereArr);
		//json数据转数组
		
		foreach($whereArr as $whereKey => $whereVal){
			//条件环节拼装
			$viewfield="";//字段
			$viewfieldValue="";//字段值
			//数据
			$viewfield=$whereVal['vfield'];
			//字段值：来源模型的元数据（注意：为了统一，这里取的是二维数组）,元数据是指向性的，只可能取下标为0的
			if(isset($sourceObject[0][$whereVal['sfield']])){
				$viewfieldValue= $sourceObject[0][$whereVal['sfield']];
			}else{
				$viewfieldValue="";
			}
			//更新值环节拼装,视图字段带了表名，不用加`符号
			$where.=$viewfield."='".$viewfieldValue."'";
		}
		//视图条件
		$viewWhere=D("MisSystemDataviewMas")->viewWhere($tableObj);
		if($viewWhere){
			if(empty($where)){
				$where.=$viewWhere;
			}else{
				$where.=" and ".$viewWhere;
			}
		}
		$map="";
		$map['_string']=$where;
		//$map['status']=1;
		$object = D ( $tableObj )->where($map)->select ();
		//print_r($object);

		//print_R( D ( $tableObj )->getLastSql());
		return $object;
		// 		if ($masVal ['sourcetable']) {
		// 			// 视图对象
		// 			$viewSign = substr ( $masVal ['sourcetable'], - 7 );
		// 			if ($viewSign == "_isview") {
		// 				$viewObj = substr_replace ( $masVal ['sourcetable'], "", - 7 );
		// 				$sourceTable = $viewObj;
		// 				//初始化where条件
		// 				$where="";
		// 				//初始化whereArray条件
		// 				$whereArr="";
		// 				//json数据转数组
		// 				$whereArr= json_decode($masVal['viewrelation'],true);
		// 				foreach($whereArr as $whereKey => $whereVal){
		// 					//条件环节拼装
		// 					$viewfield="";//字段
		// 					$viewfieldValue="";//字段值
		// 					//数据
		// 					$viewfield=$whereVal['tfield'];
		// 					//字段值：来源模型的元数据
		// 					if(isset($sourceObject[$whereVal['sfield']])){
		// 						$viewfieldValue= $sourceObject[$whereVal['sfield']];
		// 					}else{
		// 						$viewfieldValue="";
		// 					}
		// 					//更新值环节拼装,视图字段带了表名，不用加`符号
		// 					$where.=$viewfield."='".$viewfieldValue."'";
		// 				}
		// 				//视图条件
		// 				$viewWhere=D("MisSystemDataviewMas")->viewWhere($viewObj);
		// 				if($viewWhere){
		// 					if(empty($where)){
		// 						$where.=$viewWhere['condition'];
		// 					}else{
		// 						$where.=" and ".$viewWhere['condition'];
		// 					}
		// 				}
		// 				$map="";
		// 				$map['_string']=$where;
		// 				$map['status']=1;
		// 				$object = D ( $sourceTable )->where($map)->find ();
		// 				//print_R( D ( $sourceTable )->getLastSql());
		// 			} else {
		// 				//表选择，这里可以对内嵌表格做特殊处理
		// 				$sourceTable = $masVal ['sourcetable'];
		// 				$object = M ( $sourceTable )->where ( $map )->find ();
		// 			}
		// 		} else {
		// 			//直接获取数据值
		// 			$object = $sourceObject;
		// 		}
	}
	
	public  function dataRoamOrderno($mainObject,$roamMasVal,$roamSubVal){
		//抽离对象，将数据与表关系匹配分离,这里$roamSubVal与$mainObject都是key为targettable，data为
		$dataArr=array();//SQL记录初始化
		$num=0;//key初始化
		$objType=0;//初始化对象类型，分为1主表，2内嵌表
		foreach($roamSubVal as $targettable => $roamSubData){
			$sourcetable=$roamSubData['sourcetable'];
			$roamSub=$roamSubData['data'];
			//数据对象为多条时，进行数据循环
			if(count($mainObject[$targettable]['data'])>1){
				//这个时候再来做循环过滤
				foreach($mainObject[$targettable]['data'] as $key => $object){
					$dataArr[$targettable][$key]=self::dataRoamOrdernoCombox($object,&$roamMasVal,$roamSub,$sourcetable,$targettable);
					//$num++;//这里也继续循环计数
				}
			}else{
				//数据对象为一条时，直接赋值
				$dataArr[$targettable][0]=self::dataRoamOrdernoCombox($mainObject[$targettable]['data'][0],$roamMasVal,$roamSub,$sourcetable,$targettable);
				$num++;
			}				
		}
// 		print_r($dataArr);
		return $dataArr;
		/**数据处理实例-start**/
		foreach($dataArr as $targettable=>$data){
			//print_R($data);
			foreach($data as $k => $v){
				foreach($v as $k1 => $v1){
			    $vo[key($v1)] = reset($v1);			   
				}
			}
		}
// 		print_R($vo);
// 		exit;
		/**数据处理实例-end**/
	}
	/**
	 +----------------------------------------------------------
	 * 生成单据方法
	 +----------------------------------------------------------
	 * @access dataRoamOrderno
	 +----------------------------------------------------------
	 * @param string $do    1是点击触发  2是数据自动穿透（钩子）
	 +----------------------------------------------------------
	 * @param string $type  执行范围 （‘update,insert,delete’）
	 +----------------------------------------------------------
	 * @param string $do  当即执行标示
	 +----------------------------------------------------------
	 * @return false|integer
	 +----------------------------------------------------------
	 */
	static public  function dataRoamOrdernoCombox($object,$roamMasVal,$roamSubVal){
		$data=array();//初始化空数据
		$num=0;//初始化计数
		foreach($roamSubVal as $subKey => $subVal){
			//字段
			$field=$subVal['tfield'];
			//直接替换
			if($subVal['expdo']==1){
				//字段值
				if(isset($object[$subVal['sfield']])){
					$fieldValue= $object[$subVal['sfield']];
				}else{
					//暂定，后续调整
					$fieldValue=$object[$subVal['sfield']];
				}
			}
			//数据库函数,生单不支持
			if($subVal['expdo']==2){
				//字段值	为空					
					$fieldValue="";
			}
			//SQL语句
			if($subVal["expdo"]==3){
				$qSql="";//初始化查询语句
				if($subVal['sfield']){
					$subVal['sfield']="'".$object[$subVal['sfield']]."'";
					$qSql.= str_replace("###",$subVal['sfield'],$subVal["expremark"]);
				}
				$reValue =M('mis_system_data_roam_sub')->query($qSql);
				if($reValue){
					foreach($reValue as $resultkey=>$resultval){
						$resultval=array_values($resultval);
						$fieldValue=$resultval[0];
						break;
					}
				}
				else{
					$fieldValue = "";
				}
			}
			//人工设置
			if($subVal['expdo']==4){
				if(!empty($subVal['expremark'])){
					$fieldValue=$subVal['expremark'];
				}else{
					$fieldValue = "";
				}
			}
			$data[$num]=array($field=>$fieldValue);
			$num++;
		}
		return $data;
	}	
	/**
	 +----------------------------------------------------------
	 * 数据删除时还原前一状态
	 +----------------------------------------------------------
	 * @access dataRoamResume
	 +----------------------------------------------------------
	 * @param string $do    
	 +----------------------------------------------------------
	 * @param string $type  执行范围 （‘update,insert,delete’）
	 +----------------------------------------------------------
	 * @param string $do  当即执行标示
	 +----------------------------------------------------------
	 * @return false|integer
	 +----------------------------------------------------------
	 */
	public  function dataRoamResume($sourcemodel,$sourceid,$targetmodel){
		
	}
	/**
	 +----------------------------------------------------------
	 * 延时更新检查 返回false表示需要延时
	 * 否则返回实际写入的数值
	 +----------------------------------------------------------
	 * @access dataRoam
	 +----------------------------------------------------------
	 * @param string $sourcetype  1来源模型新增动作  2来源模型更新动作  3来源模型删除动作）
     +----------------------------------------------------------
	 * @param string $do    1是点击触发  2是数据自动穿透（钩子）
	 +----------------------------------------------------------
	 * @param string $dataromaDebug    1是数据漫游调试
	 +----------------------------------------------------------
	 * @param string $do    1是点击触发  2是数据自动穿透（钩子）
	 +----------------------------------------------------------
	 * @return false|integer
	 +----------------------------------------------------------
	 */
	 public  function dataRoam($mainObject,$roamMasVal,$roamSubVal,$getProjectWorkid){
	 	//抽离对象，将数据与表关系匹配分离,这里$roamSubVal与$mainObject都是key为targettable，data为
	 	$sqlArr=array();//SQL记录初始化
	 	$num=0;//key初始化
	 	$objType=0;//初始化对象类型，分为1主表，2内嵌表
		foreach($roamSubVal as $targettable => $roamSubData){
			$sourcetable=$roamSubData['sourcetable'];
			$roamSub=$roamSubData['data'];			
			//如果不存在来源数据，继续下一个循环
			if(!$mainObject[$targettable]['data']) continue;
			//$sourceObjType=$mainObject[$targettable]['sourceobjtype'];//来源对象类型
			$targetObjType=$mainObject[$targettable]['targetobjtype'];//目标对象类型
			//如果为主表时
			switch ($targetObjType) {
				case 1:
					//数据对象为主表时
						  $sqlArr[$num]=self::dataRoamSqlCombox($mainObject[$targettable]['data'][0],$roamMasVal,$roamSub,$sourcetable,$targettable,$sqlParam,$targetObjType,$getProjectWorkid);					 
						  switch ($roamMasVal ['targettype']) {
						  	//如果是插入的情况下，需要获取到最新插入的主表id并返回回来当masid插入
						  	case 1:
						  	$num= $num+1;
						  	$sqlArr[$num]['sql']="SELECT LAST_INSERT_ID() as returnval";
						  	$sqlArr[$num]['return']=true;
						  	$sqlArr[$num]['replace']=false;
						  	$sqlArr[$num]['type']='read';
						  	break;					  	
						  }
						  $num++;
			    break;
				case 2:
					//数据对象为内嵌表时
					//数据对象为多条时，进行数据循环
					foreach($mainObject[$targettable]['data'] as $key => $object){
						//对Sql执行进行替换
						unset($sqlParam);
						$sqlParam=array(
								'field'=>'masid',
						        'return'=>false,
								'replace'=>true
								);
						$sqlArr[$num]=self::dataRoamSqlCombox($object,$roamMasVal,$roamSub,$sourcetable,$targettable,$sqlParam,$targetObjType);
						$num++;//这里也继续循环计数
					}
				break;				
			}			
		}
		return $sqlArr;
	}
    	


	
	static private function dataRoamSqlCombox($object,$roamMasVal,$roamSubVal,$sourceTable,$targetTable,$sqlParam,$targetObjType,$getProjectWorkid=false){
			switch ($roamMasVal ['targettype']) {
				// 新增,这里都是压入到一整行
				case "1" :
					$sql = "";
					$sql = self::dataRoamInsert ( $object,$roamMasVal,$roamSubVal,$sourceTable,$targetTable,$sqlParam,$targetObjType,$getProjectWorkid);
					break;
					// 修改
				case "2" :
					$sql = "";
					$sql = self::dataRoamUpdate ( $object,$roamMasVal,$roamSubVal,$sourceTable,$targetTable,$sqlParam,$targetObjType,$getProjectWorkid);
					break;
					// 删除
				case "3" :
					$sql = "";
					$sql = self::dataRoamDelete ( $object,$roamMasVal,$roamSubVal,$sourceTable,$targetTable,$sqlParam,$targetObjType,$getProjectWorkid);
					break;
				case "0" :
					break;
			}
			return $sql;		
	}
	
	 private function dataRoamSqlExecute($sqlArr,$roamid,$sourcemodel,$pkValue,$do=1){
		if (! empty ( $sqlArr )) {
			//当即执行
			if($do){
				$data = array (); // 日志记录
				$sort =1;//计数器
				$sqlreturn="";//初始化返回值使用方式
				foreach ( $sqlArr as $key => $val ) {
					//第一步，判断是否需要替换返回值,如果sort大于1才执行，第一次不可能有返回值
					if($sort>1 && $val['replace']){
						$sql= str_replace("###",$sqlreturn,$val['sql']);
					}else{
						$sql= $val['sql'];
					}
					//第二步，确定操作方式用读或者写
					if($val['type']=='write'){
						$result = $this->execute ( $sql );
						
					}else{
						$result = $this->query ( $sql );
					}
					// 第三步，确定是否执行成功
					if ($result === false) {
						$data [$key] ['result'] = 0;
						//第四步，是否要重新给返回值赋值，这里因为本语句已经出错，可以强制给予一个特别值
						if($val['return']){
							$sqlreturn = 'error';
						}
					} else {
						$data [$key] ['result'] = 1;
						//第四步，是否要重新给返回值赋值？，赋值最新的返回结果,这里是2维数组，必须制定第一个数组的别名为returnval的字段
						if($val['return']){
							$sqlreturn = $result[0]['returnval'];
						}
					}
					
					$data [$key] ['createid'] = $_SESSION [C ( 'USER_AUTH_KEY' )];
					$data [$key] ['createtime'] = time ();
					$data [$key] ['companyid'] = $_SESSION ['companyid'];
					$data [$key] ['departmentid'] = $_SESSION ['user_dep_id'];
					$data [$key] ['sql'] = $sql;
					$data [$key] ['roamid'] = $roamid;
					$data [$key] ['sourcemodel'] = $sourcemodel;
					$data [$key] ['sourceid'] = $pkValue;
					$data [$key] ['sqlreturn'] = $val['return'];
					$data [$key] ['sqlreturnval'] = $sqlreturn;
					$data [$key] ['sqlreplace'] = $val['replace'];
					$data [$key] ['sqltype'] = $val['type'];
					$sort++;//增加计数
				}
				// 数据漫游调试
				$Debug = false;
				if ($Debug) {
					print_r ( $data );
					exit ();
				}
				// 写入日志
				$msdrModel = M ( "mis_system_data_roaming" );
				$msdrModel->addAll ( $data );
			} else {
				// 压入到队列执行
			}
		}		
	}
	/**
	 +----------------------------------------------------------
	 * 数据漫游时的数据插入 dataRoamInsert
	 +----------------------------------------------------------
	 * @access dataRoam
	 +----------------------------------------------------------
	 * @param mix data  写入标识
	 +----------------------------------------------------------
	 * @return string  sql
	 +----------------------------------------------------------
	 */
	
	static private function dataRoamInsert($object,$roamMasVal,$roamSubVal,$sourceTable,$targetTable,$sqlParam,$targetObjType,$getProjectWorkid){   

      	
		/****************新增时需要附加的数组**********************/
		$autoFiledArr=array();//附加数组初始化
		switch ($targetObjType){
			case 1:
				//主表
				//新增时要更新单号规则
				$scnModel=D("SystemConfigNumber");
				$orderno=$scnModel->GetRulesNO($targetTable,$roamMasVal['targettable']);
				$autoFiledArr=array('orderno'=>$orderno,'createtime'=>time(),'createid'=>$_SESSION[C('USER_AUTH_KEY')]);
				break;
			case 2:
				
				break;
		}
		/************数据是项目插入时,是否要提出来？**********/
		if($roamMasVal['sourcemodel']=='MisSalesMyProject'){
			$mis_project_flow_formDao = M("mis_project_flow_form");
			$projectmap['projectid'] = $object['id'];
			$projectmap['formobj'] = $roamMasVal['targetmodel'];
			$projectworkid = $mis_project_flow_formDao->where($projectmap)->getField("id");
			//print_R($mis_project_flow_formDao->getLastSql());
			$autoFiledArr['projectworkid']=$projectworkid;
		}else if($getProjectWorkid){
			$mis_project_flow_formDao = M("mis_project_flow_form");
			$projectmap['projectid'] = $object['projectid'];
			$projectmap['formobj'] = $roamMasVal['targetmodel'];
			$projectworkid = $mis_project_flow_formDao->where($projectmap)->getField("id");			
			//print_R($mis_project_flow_formDao->getLastSql());
            $autoFiledArr['projectworkid']=$projectworkid;			
		}
	    /************对sqlArr进行操作************************/
		  if($sqlParam['field']){
		  	$autoFiledArr[$sqlParam['field']]='###';
		  } 		
		/***************************************************/
		$sqlArr="";//销毁之前的SQL返回数组
		$sql="INSERT INTO " . $targetTable . " ( ";//初始化SQL语句前缀
		$field="";//字段
		$sqlField="";//SQL拼装
		$fieldValue="";//字段值
		$sqlFieldValue="";//SQL拼装
		$sort=1;//计数器
		//获取明细信息
		foreach($roamSubVal as $subKey => $subVal){
			//先判断是否在自动插入数组内，在的话进入下一个循环
			if(array_key_exists($subVal['tfield'],$autoFiledArr))continue;			
			//字段
			$field=$subVal['tfield'];
			//字段值
			//直接替换
			if($subVal['expdo']==1){	
				if(isset($object[$subVal['sfield']])){
					$fieldValue= "'".$object[$subVal['sfield']]."'";
				}else{
					$fieldValue= "'".self::dataRoamExchange($subVal['sfield'])."'";
				}
			}
			//SQL语句
			if($subVal["expdo"]==2){
				$fieldValue = $subVal["expremark"]."(".$fieldValue.")";
			}
			//SQL语句
			if($subVal["expdo"]==3){
				$qSql="";//初始化查询语句
				if($subVal['sfield']){
				   $subVal['sfield']="'".$object[$subVal['sfield']]."'";
					$qSql.= str_replace("###",$subVal['sfield'],$subVal["expremark"]);
				}
				$reValue = M('mis_system_data_roam_sub')->query($qSql);
				if($reValue){
					foreach($reValue as $resultkey=>$resultval){
						  $resultval=array_values($resultval);
						  $fieldValue="'".$resultval[0]."'";
						  break;
					}
				}
				else{
					$fieldValue = "''";
				}
				/*		
				$customSql = "''";
				foreach($subResult as $k => $v){
					$customSql = str_replace($v['sfield'],$object[$v['sfield']],$subVal["expremark"]);
				}
				if($customSql == ""){
					$fieldValue = "''";
				}else{
					$fieldWhere=" WHERE 1=1 ";	//条件
					//初始化whereArray条件
					$whereArr="";
					//json数据转数组
					
					$whereArr= json_decode($masVal['relation'],true);
					foreach($whereArr as $whereKey => $whereVal){
						foreach($whereVal as $k => $v){
							$fieldWhere.="AND b.".$v."= a.".$k." AND b.".$v."= '".$object[$k]."'";
						}
					}
					$qSql = "SELECT ".$customSql." AS value from $sourceTable as a,$targetTable as b ".$fieldWhere;
					$reValue = $subModel->query($qSql);
					if($reValue){
						$fieldValue = "'".$reValue[0]["value"]."'";
					}
					else{
						$fieldValue = "''";
					}
				}*/
			}
			//人工设置
			if($subVal['expdo']==4){
				if(!empty($subVal['expremark'])){
				   $fieldValue="'".$subVal['expremark']."'";
				}else{
				   $fieldValue = "''";
				}
			}
			/*****************************************/
			//这里开始匹配字段与字段值	
			//第一个数据,因为是插入不能有,号分隔
			if($sort==1){
					$sqlField.="`".$field."`";
					$sqlFieldValue.=" ".$fieldValue;							
			}else{
					$sqlField.=",`".$field."`";
					$sqlFieldValue.=",".$fieldValue;
			}
			$sort++;
		}
		//循环补充自动增补数组
		foreach($autoFiledArr as $autokey=> $autoval){
			if($sort==1){
				$sqlField.="`".$autokey."`";
				$sqlFieldValue.="'".$autoval."'";
			}else{
				$sqlField.=",`".$autokey."`";
				$sqlFieldValue.=",'".$autoval."'";				
			}
			$sort++;
		}		
		$sql.=$sqlField." ) VALUES ( ".$sqlFieldValue." )";
		//拼装sqlArr
		//执行前是否替换
		$sqlParam['replace']?$sqlArr['replace']=$sqlParam['replace']:$sqlArr['replace']=false;
		//执行后是否返回值
		$sqlParam['return']?$sqlArr['return']=$sqlParam['return']:$sqlArr['return']=false;
		//执行类型,写入操作
		$sqlArr['type']='write';
		$sqlArr['sql']=$sql;
		
		
		return $sqlArr;
	}
	/**
	 +----------------------------------------------------------
	 * 数据漫游时的数据更新 dataRoamUpdate
	 +----------------------------------------------------------
	 * @access dataRoam
	 +----------------------------------------------------------
	 * @param mix data  写入标识
	 +----------------------------------------------------------
	 * @return string  sql
	 +----------------------------------------------------------
	 */
	static private function dataRoamUpdate($object,$roamMasVal,$roamSubVal,$sourceTable,$targetTable,$targetObjType,$getProjectWorkid){
		//初始化SQL前缀
		$sql="UPDATE " . $targetTable . " SET ";
		$fieldWhere=" WHERE 1=1 ";	//条件
		foreach($roamSubVal as $subKey => $subVal){
			//获取明细信息
			$field="";//字段
			$fieldValue="";//字段值
			//数据
			$field=$subVal['tfield'];
			//直接替换
			if($subVal['expdo']==1){				
				//字段值
				if(isset($object[$subVal['sfield']])){
					$fieldValue= "'".$object[$subVal['sfield']]."'";
				}else{
					//暂定，后续调整
					$fieldValue="'".$object[$subVal['sfield']]."'";
				}
			}
			//数据库函数
			if($subVal['expdo']==2){
                //字段值
				if(isset($object[$subVal['sfield']])){
					$fieldValue= "getPinyin('".$object[$subVal['sfield']]."')";
				}else{
					$fieldValue=self::dataRoamExchange($subVal['sfield']);
				}
			}
			//SQL语句
			if($subVal["expdo"]==3){
			        $qSql="";//初始化查询语句
					if($subVal['sfield']){
					   $subVal['sfield']="'".$object[$subVal['sfield']]."'";
					    $qSql.= str_replace("###",$subVal['sfield'],$subVal["expremark"]);
					}
                    $reValue = M('mis_system_data_roam_sub')->query($qSql);
					if($reValue){
						foreach($reValue as $resultkey=>$resultval){
						      $resultval=array_values($resultval);
							  $fieldValue="'".$resultval[0]."'";
							  break;
						}
					}
					else{
						$fieldValue = "''";
					}
				/*$customSql = "''";
				foreach($subResult as $k => $v){
					$customSql = str_replace($v['sfield'],$object[$v['sfield']],$subVal["expremark"]);
				}
				if($customSql == ""){
					$fieldValue = "''";
				}else{
					$fieldSqlExecWhere=" WHERE 1=1 ";	//条件
					//初始化whereArray条件
					$sqlExecWhereArr="";
					//json数据转数组
						
					$sqlExecWhereArr= json_decode($masVal['relation'],true);
					foreach($sqlExecWhereArr as $whereKey => $whereVal){
						foreach($whereVal as $k => $v){
							$fieldSqlExecWhere.="AND b.".$v."= a.".$k." AND b.".$v."= '".$object[$k]."'";
						}
					}
					$qSql = "SELECT ".$customSql." AS value from $sourceTable as a,$targetTable as b ".$fieldWhere;
					$reValue = $subModel->query($qSql);
					if($reValue){
						$fieldValue = "'".$reValue[0]["value"]."'";
					}
					else{
						$fieldValue = "''";
					}
				}*/
			}
			//人工设置
			if($subVal['expdo']==4){
				if(!empty($subVal['expremark'])){
					$fieldValue="'".$subVal['expremark']."'";
				}else{
					$fieldValue = "''";
				}				
			}			
			if($subKey===0){
				//更新值环节拼装
				$sql.="`".$field."`=".$fieldValue;
			}else{
				$sql.=",`".$field."`=".$fieldValue;
			}
	
		}
	
		// 		$stooges = array(
		// 				       array('sname'=>'增补人数','sfield'=>'sumpeople','tname'=>'增补人数','tfield'=>'c'),
		// 				       array('sname'=>'创建人ID','sfield'=>'createtime','tname'=>'创建人ID','tfield'=>'d')
		// 		           );
	
		//$a=json_encode($stooges);
		//初始化whereArray条件
		$whereArr="";
		//json数据转数组
		$whereArr= json_decode($roamMasVal['relation'],true);
		foreach($whereArr as $whereKey => $whereVal){
			//条件环节拼装
			$field="";//字段
			$fieldValue="";//字段值
			//数据
			$field=$whereVal['tfield'];
			//字段值：来源模型的元数据
			if(isset($sourceObject[$whereVal['sfield']])){
				$fieldValue= $sourceObject[$whereVal['sfield']];
			}else{
				$fieldValue=self::dataRoamExchange($whereVal['sfield']);
			}
			//更新值环节拼装
			$fieldWhere.=" AND `".$field."`='".$fieldValue."'";
		}
		$sql.=$fieldWhere;
		//这里开始准备一个要还原的SQL语句
		$sql1=self::dataRoamResumeSql($targetTable,$roamSubVal,$fieldWhere);
		return $sql;
	}
	/**
	 +----------------------------------------------------------
	 * 数据漫游时的数据删除  dataRoamDelete
	 +----------------------------------------------------------
	 * @access dataRoam
	 +----------------------------------------------------------
	 * @param mix data  写入标识
	 +----------------------------------------------------------
	 * @return string  sql
	 +----------------------------------------------------------
	 */
	static private function dataRoamDelete($object,$roamMasVal,$roamSubVal,$sourceTable,$targetTable,$targetObjType,$getProjectWorkid){
		
		$map=array();
		//初始化SQL前缀
        $sql.="DELETE form  ".$targetTable." ";
		$fieldWhere=" WHERE 1=1 ";	//条件
		//初始化whereArray条件
		$whereArr="";
		//json数据转数组
		$whereArr= json_decode($masVal['relation'],true);
		foreach($whereArr as $whereKey => $whereVal){
			//条件环节拼装
			$field="";//字段
			$fieldValue="";//字段值
			//数据
			$field=$whereVal['tfield'];
			//字段值
			if(isset($sourceObject[$whereVal['sfield']])){
				$fieldValue= $sourceObject[$whereVal['sfield']];
			}else{
				$fieldValue=self::dataRoamExchange($whereVal['sfield']);
			}
			//更新值环节拼装
			$fieldWhere.=" AND `".$field."`='".$fieldValue."'";
		}
		$sql.=$fieldWhere;		
		return $sql;
	}
	/**
	 +----------------------------------------------------------
	 * 数据漫游时的目标数据存在时的SQL语句  dataRoamResumeSql
	 +----------------------------------------------------------
	 * @access dataRoam
	 +----------------------------------------------------------
	 * @param mix data  写入标识
	 +----------------------------------------------------------
	 * @return string  sql
	 +----------------------------------------------------------
	 */
	static private function dataRoamResumeSql($targetTable,$data,$where){
		$sql="Select ";
		$field="";
		foreach($data as $key => $val){
			$field.=$val['tfield'].",";
		}
		$sql.=substr($field,0,-1)." from ".$targetTable.$where;
	    $result=M($targetTable)->query($sql);
	    //print_r($sql);
	    $sqlResume="Update ".$targetTable." SET ";
	    $fieldVal="";
	    if($result){
		    foreach($result as $key => $val){
		    	foreach($val as $subkey=>$subval){
		    	  $fieldVal.=" `".$subkey."`='".$subval."',";
		    	}
		    }
	    }else{
	    	$sqlResume="";
	    	return $sqlResume;
	    }
	    $sqlResume.=substr($fieldVal,0,-1).$where;
	    //print_r($sqlResume);
		return $sqlResume;
	}
	/**
	 +----------------------------------------------------------
	 * 数据漫游时的数据转换项
	 * 暂时未用，数据级的基本用不上
	 +----------------------------------------------------------
	 * @access dataRoamExchange
	 +----------------------------------------------------------
	 * @param string $val  写入标识
	 +----------------------------------------------------------
	 * @return false|integer
	 +----------------------------------------------------------
	 */
	static private function dataRoamExchange($val){
		switch($val){
			case "createid"://创建人
				$result="1";
				//$aryval[$k]=getFieldBy($v,"id","name","LinkManInfomation");
				break;
			case "createtime"://创建人
				$result=strtotime(date("Y-m-d H:i:s"));
				break;
			case "createtime"://创建人
				$result=null;
				break;				
		}
		return $result;
	}	
	
	
}
?>
