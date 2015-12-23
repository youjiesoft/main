<?php
class MisSystemFlowFormModel extends CommonModel {
	protected $trueTableName = 'mis_system_flow_form';
	
	public $_auto =array(
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),			
			array('begintime','dateToTimeString',self::MODEL_BOTH,'callback'),
			array('endtime','dateToTimeString',self::MODEL_BOTH,'callback'),
			array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
				
	);
	
	private $info=array();//初始化插入数据
	private $infoTopNode=array();//顶级ID数组
	private $infoTop=array();//顶级
	private $infoSubNode=array();//下级ID数组
	private $infoSub=array();//下级
	private $infoNew=array();//新数组
	private $checkArr=array();//校验数组
	private $formCheckArr=array();//校验数组插入
	private $num=0;//重置数据标识
	private $projectid=0;//被复制的项目ID
	private $startDate =0;//开始日期
	private $linkArr =array();//链接关系
	private $parentidArr=array();//父类ID数组
	private $fenpaiArr = array(); //分派数组
	function workDays($start,$end){
		//日期计算
		$MSDModel = D("MisSystemDays");
		$dateInfo=$MSDModel->getWorkDay($start,$end);
		return $dateInfo;
	}
	//深度优先遍历
	//@data          带邻接表的二维数组结构
	//@paramate num  最新排序
	function DFS($data,$num,$vo){
		//最新排序
		$this->num=$num+1;
		foreach($data as $key =>$val){
			if($val['outlinelevel']==3){
				$this->infoTop[]=$val;
				$this->infoTopNode[]=$val['id'];
				$this->parentidArr[]=$val['parentid'];
			}else{
				$this->infoSub[]=$val;
				$this->infoSubNode[]=$val['id'];
				$this->parentidArr[]=$val['parentid'];
			}
		}
		$this->parentidArr=array_unique($this->parentidArr);
        $test=array();
  		foreach($this->infoSubNode as $key => $val){
  			$test[$key]['id']=$val;
  			foreach($this->linkArr as $subkey => $subval){	
  				if($val==$subval['id']){  				   
  				   $test[$key]['predecessorid']=$subval['predecessorid'].",".$test[$key]['predecessorid'];
  				   $test[$key]['successorid']=$subval['successorid'].",".$test[$key]['successorid'];
  				   $test[$key]['type']=$subval['type'].",".$test[$key]['type'];
  				}else{
  					if(empty($test[$key]['predecessorid'])){
  						$test[$key]['predecessorid']=0;
  					}
  				    if(empty($test[$key]['successorid'])){
  						$test[$key]['successorid']=0;
  					}
  					if(empty($test[$key]['type'])){
  						$test[$key]['type']=0;
  					}
  				}
  			}
  		}
  		
		foreach($this->infoTopNode as $key => $val){
			//创建校验数组			
			$this->checkArr[$this->infoTop[$key]['id']]=$this->num;
			//校验数组存入
			$this->formCheckArr[$this->num]['systemformid']=$this->infoTop[$key]['id'];
			$this->formCheckArr[$this->num]['projectformid']=$this->num;
			$this->formCheckArr[$this->num]['projectid']=$this->projectid;
			//将systemformid存入projectform表中
			$this->infoTop[$key]['systemformid']=$this->infoTop[$key]['id'];
			//重新对ID赋值
			$this->infoTop[$key]['id']=$this->num;
			//将项目ID赋值
			$this->infoTop[$key]['projectid']=$this->projectid;
			//开始时间
			$this->infoTop[$key]['begintime']=$this->startDate;
			$dateInfo=array();
			//明细开始时间跟节点开始时间一致
			$subStartDate=$this->startDate;
			$dateInfo=$this->workDays($this->startDate,$this->infoTop[$key]['days']);
			//print_R($dateInfo);
			$this->infoTop[$key]['endtime']=$dateInfo['day']['unixdate'];
			//加入公司ID
			$this->infoTop[$key]['companyid']=$vo['companyid'];
			$this->startDate=$dateInfo['nextday']['unixdate'];
			//如果为根节点，直接压入新数组
			array_push($this->infoNew,$this->infoTop[$key]);
			//进入子节点递归方法
            $this->digui($val,$subStartDate,4,$vo);
            $this->num++;
		}	
	}
	//@paramate val      父ID
	//@paramate startDay 开始日期	
	//@paramate startDay 
	function  digui($val,$subStartDate,$level,$vo){
		if($level>10){
			return true ;
		}
		//开始循环子类节点
		foreach($this->infoSubNode as $subkey=>$subval){			
			//如果某个子节点的父ID与当前查询节点ID相同
			if($this->infoSub[$subkey]['parentid']==$val){
				//如果在校验数组内存在，则获取校验数组最新值
				if(array_key_exists($this->infoSub[$subkey]['parentid'],$this->checkArr)){
					//明细开始时间
					$this->infoSub[$subkey]['begintime']=$subStartDate;
				    $dateInfo=$this->workDays($subStartDate,$this->infoSub[$subkey]['days']);
				    $this->infoSub[$subkey]['endtime']=$dateInfo['day']['unixdate'];
				    $this->infoSub[$subkey]['parentid']=$this->checkArr[$this->infoSub[$subkey]['parentid']];
	                $this->num++;
	                //校验数组
	                $this->checkArr[$this->infoSub[$subkey]['id']]=$this->num;
	                //校验数组存入
	                $this->formCheckArr[$this->num]['systemformid']=$this->infoSub[$subkey]['id'];
	                $this->formCheckArr[$this->num]['projectformid']=$this->num;
	                $this->formCheckArr[$this->num]['projectid']=$this->projectid;
	                $this->formCheckArr[$this->num]['suoshujuese'] = $this->infoSub[$subkey]['xiangmujuese'];
	                
	                //是否存在于父id序列中，存在为1，不存在为0
	                if(in_array($this->infoSub[$subkey]['id'],$this->parentidArr)){
	              	  $sign=1;
	                }else{
	              	  $sign=0;
	                }
	                //获取分派人。
	                if($this->infoSub[$subkey]['xiangmujuese']){
	                	//项目组
	                	$prolineorderno = explode(",", $vo['prolineorderno']);
	                	//项目角色
	                	$suoshujiaose = explode(",", $vo['suoshujiaose']);
	                	//归属人员
	                	$deptjinli = explode(",", $vo['deptjinli']);
	                	//是否自动分派
	                	$shifuzidongfenpei = explode(",", $vo['shifuzidongfenpei']);
	                	//实例化项目组
	                	$MisAutoAhmDao = D("MisAutoAhm");
	                	//执行人员
	                	//$jianyirenyuan = explode(",", $vo['jianyirenyuan']);
	                	//项目组合角色对比后的值
	                	$projectTeam = "";
		              	 foreach($prolineorderno as $k=>$v){
		              	 	//项目所属角色对比
		              	 	if($suoshujiaose[$k] == $this->infoSub[$subkey]['xiangmujuese']){
		              	 		$projectTeam = $v;
		              	 		//分派人
		              	 		$this->formCheckArr [$this->num] ['alloterid'] = $deptjinli[$k];
		              	 		//获取执行人
		              	 		$where = array();
		              	 		$where['_string'] = "jiaoseshuxing='执行人' and status = 1 and orderno <> '{$v}' and orderno like '{$v}%'";
		              	 		$fenpeiorderno = $MisAutoAhmDao->where($where)->getField("orderno");
		              	 		//执行人
		              	 		$this->formCheckArr [$this->num] ['executorid'] = 0;
		              	 		if($fenpeiorderno){
		              	 			foreach($prolineorderno as $kk=>$vv){
		              	 				if($vv == $fenpeiorderno){
		              	 					$this->formCheckArr [$this->num] ['executorid'] = $deptjinli[$kk];
		              	 					break;
		              	 				}
		              	 			}
		              	 		}
		              	 		//是否自动分派
		              	 		$this->formCheckArr [$this->num] ['shifuzidongfenpei'] = $shifuzidongfenpei[$k]?$shifuzidongfenpei[$k]:"否";
		            		}
		            	}
		            }
		            //项目角色
		            $xiangmujuese = $this->infoSub[$subkey]['xiangmujuese'];
		            $fenpai= array(
		            		'xiangmuzu'=>$projectTeam,
		            		'ziyuanhao'=>$this->num,
		            		'renwumingchen'=>$this->infoSub[$subkey]['name'],
		            		'suoshujieduan'=>$this->infoSub[$subkey]['category'],
		            		'xiangmujiaose'=>$xiangmujuese,
		            		'guanjianrenwu'=>$this->infoSub[$subkey]['critical']?"是":"否",
		            		'gongzuori'=>$this->infoSub[$subkey]['days'],
		            		'zhixingren'=>$this->formCheckArr [$this->num] ['executorid'],
		            		'fenpeiren'=>$this->formCheckArr [$this->num] ['alloterid'],
		            		'shifuzidongfenpei'=>$this->formCheckArr [$this->num] ['shifuzidongfenpei'],
		            );
		            //进行自动分派数据拼装
		            if(in_array($xiangmujuese, array_keys($this->fenpaiArr))){
		            	array_push($this->fenpaiArr[$xiangmujuese], $fenpai);
		            }else{
		            	$this->fenpaiArr[$xiangmujuese][] = $fenpai;
		            }
	              //将systemformid存入projectform表中
	              $this->infoSub[$subkey]['systemformid']=$this->infoSub[$subkey]['id'];
	              //数组ID新赋值
	              $this->infoSub[$subkey]['id']=$this->num;
	              //将项目ID赋值
	              $this->infoSub[$subkey]['projectid']=$this->projectid;
	              //将公司赋值
	              $this->infoSub[$subkey]['companyid']=$vo['companyid'];
	              //排除掉已经验证过的数据
	              unset($this->infoSubNode[$subkey]);
	              array_push($this->infoNew,$this->infoSub[$subkey]);
	              if($sign==0){ continue; }	             
                  //更新明细开始时间
			  	  $subStartDate=$dateInfo['nextday']['unixdate'];
	              //继续向下递归是否存在
	              //$this->digui($subval,$subStartDate,$level+1);		            		             
				}
			}else{
				continue;
			}
		}
	}
	
	/*
	 * 
	 * 只传递flow_type即可插入全部数据到任务中；
	 * 特别规定flow_type里的节点为固定值，不能随意更改；节点与任务全部在下属信息内获取
	 * 
	 * 
	 * */
	function intoProject($data){
		//根据编号获取业务类型的ID
		$mis_system_flow_typeDao = M("mis_system_flow_type");
		$typeid = $mis_system_flow_typeDao->where("outlinelevel = 1 and orderno = '".$data['typeid']."'")->getField("id");
		//根据主体编号获取主体ID
		$MisSaleClientTypeDao = D("MisSaleClientType");
		$ztid = $MisSaleClientTypeDao->where("orderno='".$data['zt']."'")->getField("id");
		//根据行业编号获取行业ID
		$MisSaleProfessionDao = D("MisSaleProfession");
		$hyid = $MisSaleProfessionDao->where("orderno='".$data['hy']."'")->getField("id");
		//根据主体获取主题编号
		$MisSaleIndustryDao = D("MisSaleIndustry");
		$cylid = $MisSaleIndustryDao->where("orderno='".$data['cyl']."'")->getField("id");
		//实例化要插入的表对象
		$this->projectid=$data['projectid'];
		//实例化项目任务数据表
		$MPFFModel=D('MisProjectFlowForm');
		//校验表
		$MSFFCModel=D('MisSystemFlowFormcheck');
		//项目模板LINK表
		$MSFLModel=D('MisSystemFlowLink');
		//项目模板LINK表视图
		$MSFLVModel=D('MisSystemFlowLinkView');
		//项目模板资源表
		$MSFRModel=D('MisProjectFlowResource');
		$this->linkArr=$MSFLVModel->field('id,predecessorid,successorid,type')->where("supcategory=".$typeid)->select();
        //获取当前表查询结果集
		$where = array ();
		$where ['mis_system_flow_work.supcategory'] = $typeid;//业务类型
		$where ['mis_system_flow_work.status'] = 1;
		$where ['mis_system_flow_type.status'] = 1;
		$where ['mis_system_flow_work.id'] = array('gt',0);
		$where ['mis_system_flow_work.outlinelevel'] = 4;
		$where['_string'] = "((mis_system_flow_work.custtypeid = 0 or mis_system_flow_work.custtypeid is null ) AND (mis_system_flow_work.hyid= 0 OR mis_system_flow_work.hyid is null) AND (mis_system_flow_work.cylid= 0 OR mis_system_flow_work.cylid is null))
							 OR	(mis_system_flow_work.custtypeid = '".$ztid."' AND (mis_system_flow_work.hyid= 0 OR mis_system_flow_work.hyid is null) AND (mis_system_flow_work.cylid= 0 OR mis_system_flow_work.cylid is null))
							 OR (mis_system_flow_work.hyid= '".$hyid."' AND (mis_system_flow_work.custtypeid = 0 or mis_system_flow_work.custtypeid  is null ) AND (mis_system_flow_work.cylid= 0 OR mis_system_flow_work.cylid is null))
							 OR (mis_system_flow_work.cylid= '".$cylid."' AND (mis_system_flow_work.custtypeid = 0 or mis_system_flow_work.custtypeid  is null ) AND (mis_system_flow_work.hyid= 0 OR mis_system_flow_work.hyid is null))
							 OR (mis_system_flow_work.custtypeid = '".$ztid."' AND mis_system_flow_work.hyid= '".$hyid."' AND (mis_system_flow_work.cylid= 0 OR mis_system_flow_work.cylid is null))
   							 OR (mis_system_flow_work.custtypeid = '".$ztid."' AND mis_system_flow_work.cylid= '".$cylid."' AND (mis_system_flow_work.hyid= 0 OR mis_system_flow_work.hyid is null))
							 OR (mis_system_flow_work.hyid= '".$hyid."' AND mis_system_flow_work.cylid= '".$cylid."' AND (mis_system_flow_work.custtypeid = 0 or mis_system_flow_work.custtypeid  is null ))
							 OR (mis_system_flow_work.custtypeid = '".$ztid."' AND mis_system_flow_work.hyid= '".$hyid."' AND mis_system_flow_work.cylid= '".$cylid."')
   							";
		
		$MisSystemFlowFormModel = D ( "MisSystemFlowForm" );
		//查询4级
		$worklist = $MisSystemFlowFormModel->join ("mis_system_flow_form AS mis_system_flow_work ON mis_system_flow_work.parentid = mis_system_flow_form.id" )->join("
				mis_system_flow_type as mis_system_flow_type ON mis_system_flow_type.id = mis_system_flow_work.category ")
			->field("mis_system_flow_work.id as id,mis_system_flow_work.name as name,mis_system_flow_work.supcategory as supcategory,mis_system_flow_work.category as category,
				mis_system_flow_work.begintime as begintime,mis_system_flow_work.endtime as endtime,mis_system_flow_work.createid as createid,mis_system_flow_work.createtime as createtime,
				mis_system_flow_work.status as status,mis_system_flow_work.companyid as companyid,mis_system_flow_work.departmentid as departmentid ,
				mis_system_flow_work.parentid as parentid,mis_system_flow_work.summary as summary,mis_system_flow_work.issubtask as issubtask,mis_system_flow_work.days as days,
				mis_system_flow_work.outlinenumber as outlinenumber,mis_system_flow_work.outlinelevel as outlinelevel,mis_system_flow_work.readyonly as readyonly,mis_system_flow_work.percentcomplete as percentcomplete,
				mis_system_flow_work.notes as notes,mis_system_flow_work.constrainttype as constrainttype,mis_system_flow_work.hyperlink as hyperlink,
				mis_system_flow_work.hyperlinkurl as hyperlinkurl,mis_system_flow_work.classname as classname,mis_system_flow_work.critical as critical,mis_system_flow_work.sourcemodel as sourcemodel,
				mis_system_flow_work.complete as complete,mis_system_flow_work.orderno as orderno,mis_system_flow_work.sort as sort,mis_system_flow_work.defaultdo as defaultdo,
				mis_system_flow_work.formtype as formtype,mis_system_flow_work.formobj as formobj,mis_system_flow_work.formobjid as formobjid,mis_system_flow_work.custtypeid as custtypeid,
				mis_system_flow_work.hyid as hyid,mis_system_flow_work.cylid as cylid,mis_system_flow_work.isopen as isopen,mis_system_flow_work.readtaskrole as readtaskrole,mis_system_flow_work.todeptid as todeptid,
				mis_system_flow_work.datatype as datatype,mis_system_flow_work.datatype as datatype,mis_system_flow_work.totalreport as totalreport,
				mis_system_flow_work.totalsort as totalsort,mis_system_flow_work.smallreport as smallreport,mis_system_flow_work.smallsort as smallsort,
				mis_system_flow_work.rules as rules,mis_system_flow_work.rulesinfo as rulesinfo ,mis_system_flow_work.dycon as dycon,mis_system_flow_work.showrules as showrules,
				mis_system_flow_work.xiangmujuese as xiangmujuese,mis_system_flow_work.suoshubumen as suoshubumen,mis_system_flow_work.isfile as isfile,mis_system_flow_work.guidangleixing as guidangleixing,
				mis_system_flow_work.ziliaomingcheng as ziliaomingcheng,mis_system_flow_work.disabledobj as disabledobj,mis_system_flow_work.dostatus as dostatus
			")->where ( $where )->order ( "mis_system_flow_type.sort,mis_system_flow_type.id ,mis_system_flow_form.sort,mis_system_flow_form.id ,mis_system_flow_work.sort,mis_system_flow_work.id" )->select ();
		
		if(count($worklist)== 0){
			$msg = "当前业务类型下面没有可执行的任务！";
 			throw new NullDataExcetion($msg);
 			return false;
 			exit;
		}
		//查询3级
		$where = array ();
		$where ['mis_system_flow_work.supcategory'] = $typeid;
		$where ['mis_system_flow_work.outlinelevel'] = 3;
		$where ['mis_system_flow_work.status'] = 1;
		$where ['mis_system_flow_type.status'] = 1;
		$where ['mis_system_flow_type.id'] = array('gt',0);
		$MisSystemFlowTypeModel = D("MisSystemFlowType");
		$flowlist = $MisSystemFlowTypeModel->join ("mis_system_flow_form AS mis_system_flow_work ON mis_system_flow_work.category = mis_system_flow_type.id" )
		->field("mis_system_flow_work.id as id,mis_system_flow_work.name as name,mis_system_flow_work.supcategory as supcategory,mis_system_flow_work.category as category,
				mis_system_flow_work.begintime as begintime,mis_system_flow_work.endtime as endtime,mis_system_flow_work.createid as createid,mis_system_flow_work.createtime as createtime,
				mis_system_flow_work.status as status,mis_system_flow_work.companyid as companyid,mis_system_flow_work.departmentid as departmentid ,
				mis_system_flow_work.parentid as parentid,mis_system_flow_work.summary as summary,mis_system_flow_work.issubtask as issubtask,mis_system_flow_work.days as days,
				mis_system_flow_work.outlinenumber as outlinenumber,mis_system_flow_work.outlinelevel as outlinelevel,mis_system_flow_work.readyonly as readyonly,mis_system_flow_work.percentcomplete as percentcomplete,
				mis_system_flow_work.notes as notes,mis_system_flow_work.constrainttype as constrainttype,mis_system_flow_work.hyperlink as hyperlink,
				mis_system_flow_work.hyperlinkurl as hyperlinkurl,mis_system_flow_work.classname as classname,mis_system_flow_work.critical as critical,mis_system_flow_work.sourcemodel as sourcemodel,
				mis_system_flow_work.complete as complete,mis_system_flow_work.orderno as orderno,mis_system_flow_work.sort as sort,mis_system_flow_work.defaultdo as defaultdo,
				mis_system_flow_work.formtype as formtype,mis_system_flow_work.formobj as formobj,mis_system_flow_work.formobjid as formobjid,mis_system_flow_work.custtypeid as custtypeid,
				mis_system_flow_work.hyid as hyid,mis_system_flow_work.cylid as cylid,mis_system_flow_work.isopen as isopen,mis_system_flow_work.readtaskrole as readtaskrole,mis_system_flow_work.todeptid as todeptid,
				mis_system_flow_work.datatype as datatype,mis_system_flow_work.datatype as datatype,mis_system_flow_work.totalreport as totalreport,
				mis_system_flow_work.totalsort as totalsort,mis_system_flow_work.smallreport as smallreport,mis_system_flow_work.smallsort as smallsort, 
				mis_system_flow_work.rules as rules,mis_system_flow_work.rulesinfo as rulesinfo ,mis_system_flow_work.dycon as dycon,mis_system_flow_work.showrules as showrules,
				mis_system_flow_work.xiangmujuese as xiangmujuese,mis_system_flow_work.suoshubumen as suoshubumen,mis_system_flow_work.isfile as isfile,mis_system_flow_work.guidangleixing as guidangleixing,
				mis_system_flow_work.ziliaomingcheng as ziliaomingcheng,mis_system_flow_work.disabledobj as disabledobj,mis_system_flow_work.dostatus as dostatus
			")->where ( $where )->order ( "mis_system_flow_type.sort,mis_system_flow_type.id,mis_system_flow_work.sort,mis_system_flow_work.id asc" )->select ();
		$result = array_merge($flowlist,$worklist);
		$this->info=$result;
		//获取开始时间
		$this->startDate?$this->startDate=time():$this->startDate=time();
		//获取要插入表最新的ID
		$maxId=$MPFFModel->max('id');
		if(empty($maxId)){
			$maxId=0;
		}
		$this->DFS($result,$maxId,$data);
		//数据插入项目
 		$MPFFbool = $MPFFModel->addAll($this->infoNew);
 		if(!$MPFFbool){
 			$msg = "实例化项目任务数据添加失败！".$MPFFModel->getDBError();
 			throw new NullDataExcetion($msg);
 			return false;
 			exit;
 		}
 		$checkData = array();
 		foreach($this->formCheckArr as $checkey=>$cheval){
 			unset($cheval['executorid']);
 			unset($cheval['alloterid']);
 			unset($cheval['shifuzidongfenpei']);
 			unset($cheval['suoshujuese']);
 			$checkData[] = $cheval;
 		}
 		//数据插入核验表
 		$MSFFCbool = $MSFFCModel->addAll($checkData);
 		if(!$MSFFCbool){
 			$msg = "实例化项目校验数据添加失败！".$MSFFCModel->getDBError();
 			throw new NullDataExcetion($msg);
 			return false;
 			exit;
 		}
 		//数据插入项目link表
		$MSFLbool = $MSFLModel->intoProject($this->formCheckArr,$this->projectid);
		if(!$MSFLbool){
			$msg = "实例化项目模板LINK表添加失败！".$MSFLModel->getDBError();
			throw new NullDataExcetion($msg);
			return false;
			exit;
		}
		//数据插入项目资源表
		$MSFbool = $MSFRModel->intoProject($this->formCheckArr,$data);
		if(!$MSFbool){
			$msg = "实例化项目资源表添加失败！".$MSFRModel->getDBError();
			throw new NullDataExcetion($msg);
			return false;
			exit;
		}
		$fenpaibool = $MSFRModel->intoFenPaiData($this->fenpaiArr,$data);
		if(!$fenpaibool){
			$msg = "项目分派数据添加失败！".$MSFRModel->getDBError();
			throw new NullDataExcetion($msg);
			return false;
			exit;
		}
		return true;
	}
	

	
	/**
	 * @Title: getFormList
	 * @Description: 根据节点ID，获取当前节点下面的任务清单数据
	 * @param int $formid 节点ID
	 * @return 返回节点下面的任务清单数组
	 * @author 黎明刚
	 * @date 2014年10月20日 上午11:06:46
	 * @throws
	 */
	public function getFormList($formid){
		//状态正常
		$where['status'] = 1;
		//类型为任务
		$where['outlinelevel'] = 4;
		//父类型为节点
		$where['parentid'] = $formid;
		$formlist = $this->where($where)->select();
	
		$scdmodel = D('SystemConfigDetail');
	
		foreach($formlist as $key=>$val){
			//获取title名称
			$nodename = getFieldBy($val['formobj'], 'name', 'title', 'node');
			if($val['formtype'] == 2){
				//清单 ，获取清单的控制器名称，为了获取toobar按钮。
				$toolbarextension = $scdmodel->getDetail($val['formobj'],false,'toolbar');
				if ($toolbarextension['js-add']) {
					$string = $toolbarextension['js-add']['html'];
					$str = 'navTab';
					//判断是navTab还是dialog类型
					$keynumber= strpos($string,$str);
					//$formlist[$key]['url'] = __APP__."/".$val['formobj']."/add";
					$urltitle = "dialog";
					if($keynumber){
						$urltitle = "navTab";
					}
					$formlist[$key]['urltarget'] = $urltitle;
					$formlist[$key]['urltitle'] = $nodename;
				}
			}else if($val['formtype'] ==1){
				//附件
				//$formlist[$key]['url'] = __APP__."/".$val['formobj']."/add";
				$formlist[$key]['urltarget'] = "dialog";
				$formlist[$key]['urltitle'] = $nodename;
			}
		}
		return $formlist;
	}
	
	/*
	 *
	* 获取当前任务的属性
	* @paramate
	* @paramate
	* */	
	protected function getFormAttribute(){
		//$MSFLModel=M('mis_system_flow_link');
		$result=$this->where($map)->find();
		if($result===false){
			//查询错误
			return false;
		}else{
			//有返回值
			return $result;
		}
	}
	
	
	
}
?>
