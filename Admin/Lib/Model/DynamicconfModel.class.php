<?php
//Version 1.0
// 系统配置
class DynamicconfModel extends CommonModel {
    protected $autoCheckFields = false;
    protected $listSystemFields = array(
//     		"id"=>"",
//     		//"auditState"=>array('name' => 'auditState','showname' => '审核状态','shows' => '1','widths' => '','sorts' => '0','models' => '','sortname' => 'auditState','func' => array('0' => array('0' => 'getAuditState',),),'funcdata' => array('0' => array('0' => array('0' => '###','1' => '#id#','2' => '#ptmptid#',),),),'sortnum' =>""),
//     		"status"=>"",
//     		"companyid"=>"",
//     		"departmentid"=>"",
//     		"sysdutyid"=>"",
//     		"projectid"=>"",
//     		"projectworkid"=>""
    );
/**
	 * (non-PHPdoc)
	 * @Description: SetRules(重设置配置文件list.inc信息)
	 * @param $data   array 配置文件相关参数信息
	 * @param $modelname  对应model名称
	 * @author yangxi  2014-12-12
	 */
    public function SetRules($data=array(),$modelname,$typename="list"){
        $filename=  $this->GetFile($modelname,$typename);
        $str ='';
        $str="/**";
        $str.="\r\n * @Title: Config";
        $str.="\r\n * @Package package_name";
        $str.="\r\n * @Description: todo(动态表单_配置文件-".$typename.")";
        $str.="\r\n * @author ".$_SESSION['loginUserName'];
        $str.="\r\n * @company 重庆特米洛科技有限公司";
        $str.="\r\n * @copyright 本文件归属于重庆特米洛科技有限公司";
        $str.="\r\n * @date ".date('Y-m-d H:i:s');
        $str.="\r\n * @version V1.0";
        $str.="\r\n*/\n";
        /*if(PHP_OS != 'WINNT'){
         $filename=str_replace(DConfig_PATH, './Dynamicconf', $filename);
        }*/
        $pathinfo = pathinfo($filename);
        if (!is_dir( $pathinfo['dirname'] )) {
        	$this->make_dir( $pathinfo['dirname'],0755);
        }
        logs('开始生成listinc文件系统:'.PHP_OS.'路径:'.$filename);            
//         echo $filename;
//         exit;
        $this->writeover($filename,$str."return ".$this->pw_var_export($data).";\n",true);
    }
/**
	 * (non-PHPdoc)
	 * @Description: GetFile(获取配置文件list.inc信息，指向性的获取某个字段)
	 * @param $keyVal sting  字段主键
	 * @param $modelname  sting  模型名称
	 * @author yangxi  2014-12-12
	 */
    public function GetRules($keyVal = '',$modelname,$typename="") {
		$value = '';
		if (file_exists($this->GetFile($modelname))) {
			$aryRule = require $this->GetFile($modelname,$typename="");
			foreach ($aryRule as $key => $val) {
				if ($val['name']) {
					if ($val['name']==$keyVal) {
						$value = $val['name'];
					}
				}

			}
		}
		return $value;
	}
	/**
	 * (non-PHPdoc)
	 * @Description: GetFile(获取配置文件list.inc,data.inc.php,form.inc.php全部信息)
	 * @param $modelname  string  模型名称
	 * @param $modelname  string  模型名称
	 * @author yangxi  2014-12-12
	 */
	public  function GetFile($modelname,$typename=""){		
		$filename=$this->getDynamicFileName($typename);		
		$filepath=DConfig_PATH."/Models/".$modelname."/".$filename;
		return  $filepath;
	}
	
/**
	 * (non-PHPdoc)
	 * @Description: setTableInfo(设置配置文件data.inc信息,数据字典)
	 * @param $model  string 模型名称
	 * @author yangxi  2014-12-12
	 * @see CommonAction::update()
	 */
	public function setTableInfo($modelname){
		$columnsList=array();//初始化存储字段数组
// 		$modelname="MisHrPersonnelManagement";
		//如果是VIEW
		$sql="SELECT
              mis_system_dataview_sub.`field`,
			  mis_system_dataview_sub.`otherfield`
              FROM  node
              LEFT JOIN mis_system_dataview_mas
              ON node.`viewname` = mis_system_dataview_mas.`name`
              LEFT JOIN mis_system_dataview_sub
              ON mis_system_dataview_mas.id = mis_system_dataview_sub.`masid`
              WHERE mis_system_dataview_mas.`modelname` = '".$modelname."' ";
		
		$viewList=$this->query($sql);
		if(count($viewList)){
			//如果存在时，对视图进行查询
			//print_r($viewList);
			$tableName="";//初始化表名
			$filedName="";//初始化字段名
			$otherName =array();//初始化别名
			$columnsList = array();//最终结果
			foreach($viewList as  $key => $val){	
				$filelist = array();
				//$fieldInfo第一个元素为表名，第二个元素为字段名
				$fieldInfo=explode(".",$val['field']);
				if($fieldInfo[0]==$tableName){
					//持续拼装字段名
					$filedName.=",".$fieldInfo[1];
					if(isset($val['otherfield']) && $val['otherfield']){
						$otherName[$val['field']] = $val['otherfield'];
					}else{
						$otherName[$val['field']] = $fieldInfo[1];
					}
					//如果是最后一个元素执行最后一次SQL
					if($key+1==count($viewList)){						
						$filelist=$this->getTableInfo($tableName,$filedName);
						if($filelist){
							$otherList = array();
							foreach($filelist as $k=>$v){
								$fieldKey = $tableName.".".$k;
								if(!in_array($fieldKey,array_keys($otherName))){
									unset($filelist[$k]);
								}else{
									$otherList[$otherName[$fieldKey]] = $v;
								}
							}
							$columnsList = array_merge($columnsList,$otherList);
						}
					}					
				}else{
					//如果已经进入新表名，就开始之前的拼装查询
					if($tableName){
						$filelist=$this->getTableInfo($tableName,$filedName);
						if($filelist){
							$otherList = array();
							foreach($filelist as $k=>$v){
								$fieldKey = $tableName.".".$k;
								if(!in_array($fieldKey,array_keys($otherName))){
									unset($filelist[$k]);
								}else{
									$otherList[$otherName[$fieldKey]] = $v;
								}
							}
							$columnsList = array_merge($columnsList,$otherList);
						}
					}
					//并对表名，字段名重新赋值
					$tableName=$fieldInfo[0];
					$filedName=$fieldInfo[1];
					
					if(isset($val['otherfield']) && $val['otherfield']){
						$otherName[$val['field']] = $val['otherfield'];
					}else{
						$otherName[$val['field']] = $fieldInfo[1];
					}
				}
			}	
		}else{
			//通过modelname获取表名
			$tableName=D($modelname)->getTableName();
			$columnsList=$this->getTableInfo($tableName);
		}
		$tableInfo   =   array();
        if($columnsList) {
            foreach ($columnsList as $key => $val) {
                $tableInfo[$val['COLUMN_NAME']] = array(
                	'tablename'=> $val['TABLE_NAME'],
                    'name'     => $val['COLUMN_NAME'],
                    'type'     => $val['COLUMN_TYPE'],
                    'nullable' => $val['IS_NULLABLE'], // not null is empty, null is yes
                    'default'  => $val['COLUMN_DEFAULT'],
                    'primary'  => $val['COLUMN_KEY'],
                    'autoinc'  => $val['EXTRA'],
                	'comment'  => $val['COLUMN_COMMENT'],
                );
            }
        }
        $this->SetRules($tableInfo,$modelname,$typename="data");   

	}
	/**
	 * (non-PHPdoc)
	 * @Description: setFormField(通过数据库字典生成list相关信息)
	 * @param $listFieldVal  array list文件配置字段信息
	 * @author yangxi  2014-12-12
	 * @see CommonAction::update()
	 */
	public function setFormInfo($modelname){
		 $formList=array();//初始化存储表单元素数组
	     //获取form字段的组件信息
	     $formid=M('mis_dynamic_form_manage')->where("actionname= '".$modelname."'")->getfield('id');
	     if($formid){
	     	//$result=D("MisDynamicFormProperyAndSubView")->where("formid= '".$formid."'")->select();
	     	$sql="SELECT
                               mis_dynamic_form_propery.`id`                         AS `id`,
                               mis_dynamic_form_propery.`showoption`                 AS `showoption`,
                               mis_dynamic_form_propery.`subimporttableobj`          AS `subimporttableobj`,
                               mis_dynamic_form_propery.`subimporttablefieldobj`     AS `subimporttablefieldobj`,
                               mis_dynamic_form_propery.`subimporttablefield2obj`    AS `subimporttablefield2obj`,
                               mis_dynamic_form_propery.`subimporttableobjcondition` AS `subimporttableobjcondition`,
                               mis_dynamic_form_propery.`treedtable`                 AS `treedtable`,
	     			           mis_dynamic_form_propery.`dateformat`                 AS `dateformat`,
                               mis_dynamic_form_propery.`lookupfiledback`            AS `lookupfiledback`,
                               mis_dynamic_form_propery.`lookupgrouporg`             AS `lookupgrouporg`,
	     			  		   mis_dynamic_form_propery.`org`                        AS `org`,
                               mis_dynamic_form_propery.`org1`                       AS `org1`,
                               mis_dynamic_form_propery.`lookupurls`                 AS `lookupurls`,	     				     			
                               mis_dynamic_form_propery.`lookupmodel`                AS `lookupmodel`,
                               mis_dynamic_form_propery.`lookupshoworg`              AS `lookupshoworg`,
                               mis_dynamic_form_propery.`lookuporgval`               AS `lookuporgval`,	     				     			     			
                               mis_dynamic_form_propery.`lookupconditions`           AS `lookupconditions`,
                               mis_dynamic_form_propery.`lookupchoice`               AS `lookupchoice`,
                               mis_dynamic_form_propery.`viewname`                   AS `viewname`,
                               mis_dynamic_form_propery.`viewtype`                   AS `viewtype`,	
	     			           mis_dynamic_form_propery.`org`                        AS `org`,
	     				       mis_dynamic_form_propery.`unit`                       AS `unit`,	     				     				     			
	     			           mis_dynamic_form_propery.`unitls`                     AS `unitls`,
                               mis_dynamic_form_propery.`title`                      AS `title`,
                               mis_dynamic_form_propery.`category`                   AS `category`,
                               mis_dynamic_form_propery.`fieldname`                  AS `fieldname`,
                               mis_dynamic_form_propery.`ids`                        AS `ids`,
                               mis_dynamic_form_propery.`formid`                     AS `formid`,
                               mis_dynamic_form_propery.`tplid`                      AS `tplid`,
                               mis_dynamic_form_propery.`status`                     AS `status`,
	     					   mis_dynamic_form_propery.`islock`                     AS `islock`,
	     					   mis_dynamic_form_propery.`isrequired`                 AS `isrequired`,
	     			
	     			mis_dynamic_form_propery.`treedtable`						AS `treedtable`,
	     			mis_dynamic_form_propery.`treevaluefield`               AS `treevaluefield`,
	     			mis_dynamic_form_propery.`treeshowfield`               AS `treeshowfield`,
	     			mis_dynamic_form_propery.`treeparentfield`             AS `treeparentfield`,
	     			mis_dynamic_form_propery.`isnextend`            			AS `isnextend`,
	     			mis_dynamic_form_propery.`mulit`            				AS `mulit`,
	     			mis_dynamic_form_propery.`treeheight`            		AS `treeheight`,
	     			mis_dynamic_form_propery.`treewidth`            			AS `treewidth`,
	     			
	     					mis_dynamic_form_propery.`additionalconditions`								AS `addconditions`,
                               mis_dynamic_database_sub.`field`                      AS `field`,
                               mis_dynamic_database_sub.`formid`                     AS `subformid`
                             FROM mis_dynamic_form_propery mis_dynamic_form_propery
                               LEFT JOIN mis_dynamic_database_sub mis_dynamic_database_sub
                                 ON mis_dynamic_form_propery.`ids` = mis_dynamic_database_sub.`id`
                             WHERE mis_dynamic_form_propery.formid = ".$formid;
	     	//M('mis_dynamic_database_sub')->where("formid= '".$formid."'")->select();
	     	$formList=$this->query($sql);
// 	     	print_r($this->getLastSql());exit;
//             print_r($formList);
//             echo 123;
	        foreach($formList as $key => $val){
		        if(!empty($val['fieldname'])){
		        	$temp = array();
		        	
		        	//组件name，用来提交数据的
		        	$temp["template_name"]   = "datatable[#index#][table][".$val['fieldname']."]";
		        	$temp["template_data"] = "";
		        	$temp["template_class"] = $val["isrequired"]?"required":"";
		        	$temp["template_key"] = "";
		        	$temp["is_readonly"] = $val["islock"]?"":"true";
		        	
		        	switch ($val['category']){
		        		case "text":
		        			$temp["template_key"] = "input";
		        			$template_data = array();
		        			//获取ORG参数
		        			if(!empty($val['org'])){
		        				$org = explode(".",$val['org']);
		        				$template_data["bindlookupname"] = $org[0];
		        				$template_data["upclass"] = $org[1];
		        			}
		        			
		        			if(!empty($val['unitls'])){
		        				//存储的单位编码
		        				$template_data["unitl"] = $val['unitls'];
		        				//获取单位编码中文名
		        				$template_data["unitlname"] = getFieldBy($val['unitls'], 'danweidaima', 'danweimingchen', 'mis_system_unit');
		        			}
		        			$temp["template_data"] = json_encode($template_data);
		        			//列是否统计
		        			$temp["is_stats"]=false;
		        			//小数位数
		        			$temp["stats_num"]=2;
		        			break;
		        		case "select":
		        			//获取ORG参数
		        			if(!empty($val['org'])){
		        				$org = explode(".",$val['org']);
		        				$bindlookup = array("lookupname"=>$org[0],"name"=>$org[1]);
		        				$temp["bindlookup"] = json_encode($bindlookup);
		        			}
		        			/* 
		        			 * 下拉树配置	
		        			 * */
		        			if($val['treedtable'] && $val['treevaluefield'] && $val['treeshowfield'] && $val['treeparentfield'] ){
		        				$chkStyle="radio";
		        				// 单选，多选
		        				if($val["mulit"]==1){
		        					$chkStyle="checkbox";
		        				}
		        				$isnextend=$val['isnextend']==1?1:0;
		        				// 获取下拉树的显示数据
		        				$treeData = getControllbyHtml("table",array("type"=>"select","table"=>$val['treedtable'] ,"id"=>$val['treevaluefield'],"name"=>$val['treeshowfield'],"showtype"=>"1","comboxtree"=>"1","parentid"=>$val['treeparentfield'],"isnextend"=>$isnextend));
		        				$dataSouceArr = '{"treeconfig":'.
		        						'{"expandAll":false,'.
		        						'"checkEnable":true,'.
		        						'"chkStyle":"'.$chkStyle.'",'.
		        						'"radioType":"all",'.
		        						'"onClick":"S_NodeClick",'.
		        						'"onCheck":"S_NodeCheck"},'.
		        						'"treeheight":"'.$val['treeheight'].'",'.
		        						'"treewidth":"'.$val['treewidth'].'",'.
		        						'"treedata":'.$treeData.'}';
		        				
		        				$temp["template_data"] = $dataSouceArr;
		        				$temp["template_key"] = "selecttree";
		        			}else{
			        			$temp["template_key"] = "select";
			        			$temp["template_data"]="[";
			        			$abc=$this->getSelectSource($val);
			        			foreach($abc as $skey => $sval){
			        				$temp["template_data"].='{"value":"'.$skey.'","name":"'.$sval.'"},';
			        			}
			        			//去掉最后一个，
		 	        			$temp["template_data"]=$abc?substr($temp["template_data"],0,-1)."]":$temp["template_data"]."]";
			        			//数据格式例子
			        			//$temp["template_data"]   = '[{"value":1,"name":"第一人"},{"value":2,"name":"第二人"}]';
		        			}
		        			break;
		        		case "lookup":
		        			$temp["template_key"] = "lookup";
		        			$abc=$this->getLookupSource($val);
		        			//upclass:查找带回显示字段,格式 string class="textInput enterIndex readonly into_table_new9122317_wrapperorgfandanbaocuoshibianhao11.name" class里 org.name  
		        			//callback是回调函数  param是传递到lookupGeneral函数的参数，格式 string 例：param="field=orderno,pid,name,id&model=MisSaleProfession&newconditions="   
		        			//herf是请求地址   格式 string 例：href="/workarea/workstation/systemui/product/Admin/index.php/MisAutoDap/lookupGeneral"
		        			//hidden_data:隐藏的存入数据库字段的值 格式 ：[{"upclass":"","name":"datatable[#index#][datatable1][sinianji]"}]，其中upclass是查找带回存储字段，name是隐藏输入入框的name，[datatable1][sinianji]表名，字段名
		        			$param="";//初始化参数
		        			$param.="lookupchoice=".$val['lookupchoice'];//传Key值
		        			unset($lookuporg0);
		        			unset($lookuporg1);
		        			$lookuporg0 = explode('.',  $val['org'] );
		        			$lookuporg1 = explode('.',  $val['org1'] );
		        			$bindlookupname = $lookuporg1[0]?$lookuporg1[0]:$lookuporg0[0];
		        			$param .='';
// 		        			//lookup带回字段
// 		        			$param.="field=".$val['lookupfiledback'];
// 		        			//lookup的模型对象
// 		        			$param.="&model=".$val['lookupmodel'];
	                        //过滤条件
		        			
		        			//////////////////////////////////////////////////////////////////////////////////////////////
		        			unset($conditionConfigJson);
		        			unset($appendCondtion);
		        			unset($additionalconditions);
		        			unset($formFiledList);
		        			unset($sysFieldList);
		        			unset($sysFieldFmt);
		        			unset($appendCondtionArr);
		        			unset($appendCondtionStr);
		        			unset($conditions);
		        			unset($formFiledFmt);
		        			$additionalconditions = $val['addconditions'];
		        			if($additionalconditions){
		        				$appendCondtion = unserialize(base64_decode($additionalconditions) );
		        				// proexp 表单字段列表
		        				// sysexp	系统字段列表
		        				$formFiledList 	=	$appendCondtion['proexp'];
		        				$sysFieldList 		= 	$appendCondtion['sysexp'];
		        				// 						$formFiledFmt=array();
		        				// 						$sysFieldFmt = array();
		        				$sysFieldFmt = unserialize($sysFieldList) or array();
		        				// 获取真实的表单字段名。
		        				if($formFiledList){
		        					$formFiledListArr = unserialize($formFiledList) or array();
		        					if( is_array( $formFiledListArr ) && count( $formFiledListArr ) ){
		        						$formFiledKey = array_keys($formFiledListArr);
		        						$properModel = M('mis_dynamic_form_propery');
		        						$properMap['id'] = array('in',$formFiledKey);
		        						unset($fd);
		        						$fd = $properModel->where($properMap)->field('fieldname,id')->select();
		        						if($fd){
		        							foreach ($fd as $k=>$v){
		        								// 									$fieldArr[] = $v['fieldname'];
		        								if( $formFiledListArr[$v['id']] == -1){
		        									$formFiledFmt[$v['fieldname']] = $v['fieldname'];
		        								} else {
		        									$formFiledFmt[$v['fieldname']] = $formFiledListArr[$v['id']];
		        								}
		        							}
		        						}
		        					}
		        				}
		        				if(is_array($sysFieldFmt) && is_array($formFiledFmt))
		        					$appendCondtionArr = array_merge($sysFieldFmt , $formFiledFmt);
		        				elseif(is_array($sysFieldFmt) && !is_array($formFiledFmt) )
		        				$appendCondtionArr =$sysFieldFmt;
		        				elseif(!is_array($sysFieldFmt) && is_array($formFiledFmt) )
		        				$appendCondtionArr =$formFiledFmt;
		        				else
		        					$appendCondtionArr =array();
		        				// 将条件转换为可用的sql where语句
		        				$appendCondtionStr = arr2string($appendCondtionArr);
		        				logs($val['fieldname'].$appendCondtionStr , 'rinidaye');
		        				//$conditions = "{:getAppendCondition(\$vo ,$appendCondtionStr)}";
		        				$conditions = getAppendCondition($vo ,$appendCondtionArr );
		        				if(is_array($appendCondtionArr)){
		        					unset($tempData);
		        					foreach ($appendCondtionArr as $k=>$v){
		        						$tempData[$v] = $k;
		        					}
		        					$conditionConfigJson = ',"condition":'.json_encode($tempData);
		        				}
		        			}
		        			////////////////////////////////////////////////////////////////////
		        		    $param.="&newconditions=".$conditions;
		        		    $val['callback'] = "lookup_counter_check";
		        		    //herf路径
		        		    $herf=__APP__.'/'.$modelname.'/'.$val['lookupurls'];
		        			$temp["template_data"]   ='{"bindlookupname":"'.$bindlookupname.'","upclass":"'.$val['lookupshoworg'].'","lporder":"'.$lookuporg1[1].'","lpkey":"'.$val['lookupchoice'].'","callback":"'.$val['callback'].'","param":"'.$param.'","lookupname":"'.$val['lookupgrouporg'].'","href":"'.$herf.'","hidden_data":[{"upclass":"'.$val['lookuporgval'].'","lporder":"'.$lookuporg0[1].'","name":"datatable[#index#][table]['.$val['fieldname'].']"}]'.$conditionConfigJson.'}';
		        			break;
	        			case "date":
	        				$temp["template_key"] = "date";
	        				//日期格式
	        				if(empty($val['format'])){
	        					$val['dateformat']="yyyy-MM-dd";
	        				}else{
	        					$formatTemp=explode('@', $val['dateformat']);
	        					$val['dateformat']=$formatTemp[0];
	        				}
	        				$temp["template_data"]   ='{"format":"'.$val['dateformat'].'"}';       				
	        				break;
	        			case "upload":
	        				$temp["template_key"] = "uploadfilenew";
	        				$temp["template_data"]='{"url":"__URL__/DT_uploadnew"}';
	        				break;
		        		default:
		        		break;
		        	}
		        	$th_html=array();
		        	foreach($temp as $k => $v){
		        		$th_html[] = $k."='".$v."'";
		        	}
		        	$th_html = implode(" ",$th_html);
		        	$val['datatable']=$th_html;
		        	$list[$val["fieldname"]]=$val;
		        }
	        }	
	     }  
	   $this->SetRules($list,$modelname,$typename="form");
	}
		
	protected function getSelectSource($confVo){
		if($confVo){
			// 用户指定的枚举
			if($confVo['showoption']){
				//选择select配置文件
				$selectList = require ROOT . '/Dynamicconf/System/selectlist.inc.php';
				//取得配置的数据表
				$MisSaleClientTypeList=$selectList[$confVo['showoption']][$confVo['showoption']];
					
			}else{
				$skey="";
				$ekey="";
				// 用户指定的表数据来源
				if($confVo['subimporttableobj']){
					//非树形展示
					$MisSaleClientTypeDao=M($confVo['subimporttableobj']);
					$skey=$confVo['subimporttablefield2obj'];
					$ekey=$confVo['subimporttablefieldobj'];
				}else{
					// 树形展示
					$MisSaleClientTypeDao=M($confVo['treedtable']);
					$skey=$confVo['treevaluefield'];
					$ekey=$confVo['treeshowfield'];
				}
				if($tpltype=="basisarchivestpl#ltrl"||$tpltype=="basisarchivestpl#ltrc"){
					$MisSaleClientTypeList=$MisSaleClientTypeDao->where("status=1")->select();
				}else{
					$MisSaleClientTypeList=$MisSaleClientTypeDao->where("status=1")->getField($skey.",".$ekey);
				}
			}
			return $MisSaleClientTypeList;
		}else{
			return false;
		}
	}	

	protected function getLookupSource($confVo){
		if($confVo){
			// 用户指定的枚举
			if($confVo['lookupgrouporg']){
				
				return "grouporg";
			}else{
				// 用户指定的视图查找带回来源
				if($confVo['viewname']){
					return "view";
				}else{
					return false;
				}
				
			}		
		}else{
			return false;
		}
	}
	/**
	 * (non-PHPdoc)
	 * @Description: GetFile(获取配置文件list.inc信息)
	 * @param $model  string 模型名称
	 * @author yangxi  2014-12-12
	 * @see CommonAction::update()
	 */
	public function setListInfo($modelname){
		//list里的信息
		$listInfo=$this->GetFile($modelname);
		if(file_exists($listInfo)){
			$listInfo=(require $listInfo);
		}else{
			$listInfo=array();
		}
		//获取表数据信息
		$tableInfo=$this->GetFile($modelname,$typename="data");
		if(file_exists($tableInfo)){
            $tableInfo=(require $tableInfo);
		}else{
			$tableInfo=$this->setTableInfo($modelname);
			$tableInfo=(require $tableInfo);
		}		
		//如果存在list文件
		if(!empty($listInfo)){
			//获取listInfo的最大key值
			foreach($listInfo as $key=>$val){
				//如果table为null，自动补全
				if(!isset($val['table']) && empty($val['table'])){
					$listInfo[$val['name']]['table']=$tableInfo[$val['name']]['tablename'];
				}
				//如果filed为null,自动补全
				if(!isset($val['field']) && empty($val['field'])){
					$listInfo[$val['name']]['field']=$tableInfo[$val['name']]['name'];
				}
				//如果searchField为null,自动补全
				if(!isset($val['searchField']) && empty($val['searchField'])){
					$listInfo[$val['name']]['searchField']=$tableInfo[$val['name']]['tablename'].".".$tableInfo[$val['name']]['name'];
				}
				//如果showname为null,自动补全
				if(!isset($val['showname']) && empty($val['showname'])){
					$listInfo[$val['name']]['showname']=$tableInfo[$val['name']]['comment'];
				}
// 				已经增加的字段被unset掉
				unset($tableInfo[$val['name']]);
// 				生成新KEY
				if($key!==$val['name']){
					$newkey=$val['name'];
					$listInfo[$newkey]=$val;
					unset($listInfo[$key]);
				}
			}
		}
		//通过tableInfo来完善list信息
		foreach($tableInfo as $key =>$val){
			//如果存在则写入
			//先确定是否在要生成的系统字段内
			if(array_key_exists($val['name'],$this->listSystemFields)){
				//如果存在标准数组
				if(!empty($this->listSystemFields[$val['name']])){
					$listInfo[$val['name']]=$this->listSystemFields[$val['name']];
				}else{
					//如果name为null，自动补全
					$listInfo[$val['name']]['name']=$val['name'];
					//如果table为null，自动补全
					$listInfo[$val['name']]['table']=$val['tablename'];
					//如果filed为null,自动补全
					$listInfo[$val['name']]['field']=$val['name'];
					//如果searchField为null,自动补全
					$listInfo[$val['name']]['searchField']=$val['tablename'].".".$val['name'];
					//如果showname为null,自动补全
					$listInfo[$val['name']]['showname']=$val['comment'];
					//增加字段数据字典信息
					//$listInfo[$max]['fieldInfo']=$val;
					//启用状态为0
					$listInfo[$val['name']]['status']=0;
					}
				}
			//已经增加的字段被unset掉
			unset($tableInfo[$key]);
		}
		//$this->SetRules($listInfo,$modelname,$typename="list");
		$autoformModel=D('Autoform');
		$dir = DConfig_PATH.'/Models/';
		$listincpath = $dir.$modelname.'/list.inc.php';
		$autoformModel->setPath($listincpath);
		$autoformModel->SetListinc($listInfo);
	}
	
	

    /**
     * (non-PHPdoc)
     * @Description: GetFile(获取配置文件list.inc信息)
     * @param $tableName  string  表对象名称
     * @param $tableFiled  string 要查询的字段名称，多字段用,号分隔
     * @author yangxi  2014-12-12
     * @see CommonAction::update()
     */
    public function getTableInfo($tableName,$tableFiled=""){
    	$tableObj=M("INFORMATION_SCHEMA.COLUMNS","","",1);
    	$map['table_name']=$tableName;
    	$map['TABLE_SCHEMA']=C('DB_NAME');
    	$tableFiled?$map['COLUMN_NAME']=array('in',$tableFiled):"";
    	$columnsList = $tableObj->where($map)->getField("COLUMN_NAME,TABLE_NAME,COLUMN_KEY,EXTRA,IS_NULLABLE,DATA_TYPE,COLUMN_TYPE,COLUMN_DEFAULT,COLUMN_COMMENT");
//     	echo $tableObj->getLastSql()."<br/>";
//     	print_r($columnsList);
    
    	return $columnsList;
    }    
   
}
?>