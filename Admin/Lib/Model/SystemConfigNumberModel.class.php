<?php
//Version 1.0
// 系统配置
class SystemConfigNumberModel extends CommonModel {
	protected $trueTableName = 'mis_system_config_orderno';
    protected $autoCheckFields = false;
   	public $firstDetail='';

    public function SetRules($data=array()){
    	if(!$data['modelname']){
    		throw new NBM('模型名不存在');
    	}
    	if(!$data['table']){
    		throw new NBM('表名不存在');
    	}
		$condition = array('modelname'=>array('eq', $data['modelname']), 'table'=>array('eq', $data['table']));
		$vo=$this->where($condition)->find();
		if($vo){
			if($vo['oldrule'] != $data['oldrule']){
				//进行验证 规则1-4是否有变化，如果变化。直接将流水号重置
				$data['numshow'] = 0;
				$data['numnew'] = 1;
			}
		    $list=$this->where($condition)->data($data)->save();
		    return $vo['id'];
		}else{
		    $list=$this->add($data);
		    return $list;
		}

//         $filename=  $this->GetFile();
//         $this->writeover($filename,"\$aryRule = ".$this->pw_var_export($data).";\n",true);
    }

    public function GetRules($tableName = '', $modelName = '',$fieldval) {
    	if(!empty($tableName)){
    		if(!empty($fieldval)){
    			$condition = array('table'=>array('eq', $tableName),'fieldval'=>array('eq',$fieldval));
    			if(!empty($modelName))	$condition['modelname'] = array('eq', $modelName);
    			$classifyModel=M('mis_system_config_orderno_classify');
    			if($ruleData =$classifyModel ->where($condition)->find()){
    				//如果设置为不自动生成，默认可写
    				if(!$ruleData['status'])$ruleData['writable']=1;
    				$returnData = $ruleData;
    			}else{
    				//如果不存在。直接返回六位流水号
    				$returnData['status'] = 1;
    				$returnData['num'] = 6;
    				$returnData['numshow'] = 0;
    				$returnData['notexist'] = true;
    			}
    		}else{
	    		$condition = array('table'=>array('eq', $tableName));
	    		if(!empty($modelName))	$condition['modelname'] = array('eq', $modelName);
				$ruleData = $this->where($condition)->find();
				//echo $this->getLastSql();
	    		if($ruleData){
	    			//如果设置为不自动生成，默认可写
	    			if(!$ruleData['status'])$ruleData['writable']=1;
	    			$returnData = $ruleData;
	    		}else{
	    			//如果不存在。直接返回六位流水号
	    			$returnData['status'] = 1;
	    			$returnData['num'] = 6;
	    			$returnData['numshow'] = 0;
					$returnData['notexist'] = true;
	    		}
    		}
    		//echo $this->getLastSql();
    	}else{
    		$returnData = array();
    	}
    	
    	return $returnData;
	}

	public function GetRulesNO($tableName="", $modelName='', $max){
		$RulesNO=$num='';
		if(!empty($tableName)){
			$ruleData = $this->GetRules($tableName, $modelName);
			
			if($ruleData['status']){
				//进行解析规则
				//$rule = explode("/",$ruleData['rule']);
				//前缀一
				if($ruleData['prefix1'])	$RulesNO.=$this->typeCheck($ruleData['prefix1'],$ruleData['prefix1_value'],$ruleData['prefix1_long']);
				//前缀二
				if($ruleData['prefix2'])	$RulesNO.=$this->typeCheck($ruleData['prefix2'],$ruleData['prefix2_value'],$ruleData['prefix2_long']);
				//前缀三
				if($ruleData['prefix3'])	$RulesNO.=$this->typeCheck($ruleData['prefix3'],$ruleData['prefix3_value'],$ruleData['prefix3_long']);
				//前缀四
				if($ruleData['prefix4'])	$RulesNO.=$this->typeCheck($ruleData['prefix4'],$ruleData['prefix4_value'],$ruleData['prefix4_long']);
				
				//流水号
				if($ruleData['num']){
					if(isset($max)){
						$RulesNO.=sprintf("%0".$ruleData['num']."d", $max);
					}else{
						$realModelName = empty($modelName) ? $ruleData['modelname'] : $modelName;
						$RulesNO.=sprintf("%0".$ruleData['num']."d", M($tableName)->max('id')+1);
					}
				}
				//后缀
				if($ruleData['suffix']){
					$RulesNO.=$ruleData['suffix'];
				}
				return $RulesNO;
			}
		}
	}

	
	public function getOrderno($tableName="", $modelName='',$max=0,$fieldval=null){
		if(!empty($tableName)){
			$oldorderno = "";
			$RulesInfo=array();//编号返回值
			$orderno='';//初始化流水号，订单编号
			$ruleData = $this->GetRules($tableName, $modelName,$fieldval);
			if($ruleData['status']){
				//前缀一
				if($ruleData['prefix1'])	$orderno.=$this->typeCheck($ruleData['prefix1'],$ruleData['prefix1_value'],$ruleData['prefix1_long']);
				//前缀二
				if($ruleData['prefix2'])	$orderno.=$this->typeCheck($ruleData['prefix2'],$ruleData['prefix2_value'],$ruleData['prefix2_long']);
				//前缀三
				if($ruleData['prefix3'])	$orderno.=$this->typeCheck($ruleData['prefix3'],$ruleData['prefix3_value'],$ruleData['prefix3_long']);
				//前缀四
				if($ruleData['prefix4'])	$orderno.=$this->typeCheck($ruleData['prefix4'],$ruleData['prefix4_value'],$ruleData['prefix4_long']);
				$oldorderno = $orderno;
				//判断：如果前缀发生了变化。则将流水号重置
				if($ruleData['oldrule'] != $orderno  && $tableName!='mis_auto_fuhhu'){
					$ruleData['numshow'] = 0;
					$ruleData['numnew'] = 1;
				}
				
				//流水号
				if($ruleData['num']){
					//先确定当前编号
					if(empty($ruleData['numshow'])){
						$ruleData['numshow']=0;
					}
					//获取最大编号
					else{
						$max=$ruleData['numshow']>$max?$ruleData['numshow']:$max;
					}
					
					//再判断
					$max=$max+1;
				
					$max=$ruleData['numnew']>$max?$ruleData['numnew']:$max;//流水号

					$orderno.=sprintf("%0".$ruleData['num']."d", $max);

				}
				//后缀
				if($ruleData['suffix']){
					$orderno.=$ruleData['suffix'];
				}
				//递归查询编码是否重复
				$map=array();
				$map['orderno']=$orderno;
				$list = M($tableName)->where($map)->getField('id');
				if($list){
					return $this->getOrderno($tableName, $modelName,$max,$fieldval);
				}else{
					//更新表
				    $transeModel = new Model();
					$transeModel->startTrans();
					//数据是否存在，不存在新增，存在修改
					if($ruleData['notexist']){
							if($fieldval){
								$model=M('mis_system_config_orderno_classify');
								//新增
								$cldata['masid']=$result;
								$cldata['fieldval']=$fieldval;
								$cldata['table']=$tableName;
								$cldata['modelname']=$modelName;
								$cldata['status']=$ruleData['status'];
								$cldata['num']=$ruleData['num'];
								$cldata['numnew']=$max;
								$cldata['oldrule']=$oldorderno;
								$result = $model->add($cldata);
							}else{
								//新增
								$data['table']=$tableName;
								$data['modelname']=$modelName;
								$data['status']=$ruleData['status'];
								$data['num']=$ruleData['num'];
								$data['numnew']=$max;
								$cldata['oldrule']=$oldorderno;
								$result = $this->add($data);
							}
				    }else{
				    	if($fieldval){
				    		$model=M('mis_system_config_orderno_classify');
				    		//修改
					    	$map=array();
					    	$map['table']=$tableName;
					    	$map['modelname']=$modelName;
					    	$map['fieldval']=$fieldval;
					    	$data = array('numnew'=>$max);
					    	//判断：如果前缀发生了变化。则将流水号重置
					    	 if($ruleData['oldrule'] != $oldorderno && $tableName!='mis_auto_fuhhu'){
					    		$data['numshow'] = 0;
					    		$data['numnew'] = 1;
					    		$data['oldrule'] = $oldorderno;
					    	} 
					    	$result = $model->where($map)->setField($data);	
				    	}else{
				    		//修改
					    	$map=array();
					    	$map['table']=$tableName;
					    	$map['modelname']=$modelName;
					    	$data['numnew'] =$max;
					    	
					    	//判断：如果前缀发生了变化。则将流水号重置
					    	if($ruleData['oldrule'] != $oldorderno){
					    		$data['numshow'] = 0;
					    		$data['numnew'] = 1;
					    		$data['oldrule'] = $oldorderno;
					    	}
					    	
					    	$result = $this->where($map)->setField($data);	
				    	}			    	
				    }
			        if($result===false){
			        	$transeModel->rollback();
			        }else{
			        	$this->startTrans();
			        }			
					//返回数据信息
					$RulesInfo['orderno']=$orderno;
					$RulesInfo['writable']=$ruleData['writable']==1?true:false;
					$RulesInfo['status']=$ruleData['status'];
				}
			}else{
				//编码不存在时，直接赋值为空
				$RulesInfo['orderno']='';
				$RulesInfo['writable']=true;
				$RulesInfo['status']=0;				
			}
			return $RulesInfo;
		}
	}

		
	public  function typeCheck($obj,$value,$long){
		if($obj){
			switch($obj){
				case 'year':
					$temp=date('Y') ;
					return $temp;
				break;
				case 'moth':
					$temp=date('m') ;
					return $temp;					
				break;
				case 'day':
					$temp=date('d') ;
					return $temp;					
				break;	
				case 'creatid':
					$temp=$_SESSION['USER_AUTH_KEY'];
					return $temp;					
				break;	
				case 'companyid':
					$temp=$_SESSION['companyid'];
					return $temp;					
				break;	
				case 'departmentid':
					$temp=$_SESSION['departmentid'];
					return $temp;					
				break;	
				case 'myset':
					$temp=$value;
					return $temp;					
				break;																					
			}
		}else{
			
			return false;
		}
		
	}


    
    public function GetWritable($tablename=""){
       if(file_exists($this->GetFile())){
            require $this->GetFile();
            foreach ($aryRule as $key=>$val){
                if($key==$tablename){
                    $writable=$val['writable'];
                    if ($val['status']!='1') {
						$writable='1';//单号规则禁用时，可写状态
					}
                }
            }
        }
       return $writable;
    }

    public  function GetFile(){
        return  DConfig_PATH."/System/list.inc.php";
    }
    /**
     * @Title: getRoleTree
     * @Description: todo(菜单树)
     * @param rel str 连接（html标签里的id值）
     * @param url str 显示的页面
     * @param map2 array 额外条件
     * @author 杨东
     * @date 2013-5-28 上午10:41:15
     * @throws
     */
    public function getRoleTree($rel,$url='__URL__/index',$map2=array(),$modelname=false,$target='ajax',$title=''){
    	if(empty($url)) $url='__URL__/index';
    	// 组
    	$model=M("group");
    	$list = $model->where("status=1")->order("sorts asc")->select();
    	//获取系统授权
    	$model_syspwd = D('SerialNumber');
    	$modules_sys = $model_syspwd->checkModule();
    	$m_list = explode(",",$modules_sys);
    	// 查询菜单节点
    	$map['status'] = 1;
    	$map['showmenu'] = 1;
    	$map['type'] = array('lt',4);
    	$node = M("Node");
    	//$map['name'] = array('not in',$filter);
    	$data = $node->where($map)->order('sort asc')->field("id,pid,name,level,isprojectwork,type,group_id,title,toolshow")->select();
   		if($map2){
   			$map['level']=array('eq',3);
   			$map=array_merge($map,$map2);
   			$data2 = $node->where($map)->field("id,pid,name,level,isprojectwork,type,group_id,title,toolshow")->select();
   			$cond = $data2;
   			$top=array();
   			foreach ($data as $k=>$v){
   				foreach ($data2 as $k1=>$v1){
   					if ($v['id']==$v1['pid'])
   						$top[]=$v['id'];
   				}
   			}
   			$top = array_unique($top);
   			foreach($data as $k=>$v){
   				if(in_array($v['id'],$top)){
   					$data2[]=$v;
   				}
   			}
   			$data = $data2;
   		}
    	
    	// 获取授权节点
    	$access = getAuthAccess();
    	$returnarr = array();
    	// 第一个循环构造分组节点
    	foreach ($list as $k2 => $v2) {
    		$newv1 = array();
    		$newv1['id'] = -$v2['id'];
    		$newv1['pId'] = 0;
    		$newv1['title'] = $v2['name']; //光标提示信息
    		$newv1['name'] = missubstr($v2['name'],20,true); //结点名字，太多会被截取
    		$newv1['open'] = 'false';
    		$returnarr2 = array();
    		// 第二个循环构造组分类节点
    		foreach($data as $k => $v){
    			//if($v['isproject']==1) unset($data[$k]);
    			$newv2 = array();
    			// 过来权限
    			if(substr($v['name'], 0, 10)!="MisDynamic"){
    				if(!in_array($v['name'], $m_list))  continue;
    			}
    			if (!isset ($access[strtoupper( APP_NAME )][strtoupper ($v ['name'])]) && !$_SESSION [C('ADMIN_AUTH_KEY')]) {
    				continue;
    			}
    			if($v['name']!='Public' && $v['name']!='Index') {
    				if ($v['type'] == 1 && $v['group_id'] == $v2['id']) {
    					$newv2['id'] = $v['id'];
    					$newv2['pId'] = -$v2['id'];
    					$newv2['title'] = $v['title']; //光标提示信息
    					$newv2['name'] = missubstr($v['title'],20,true); //结点名字，太多会被截取
    					$newv2['open'] = 'false';
    					$returnarr3 = array();
    					// 判断当前节点是否显示1显示0不显示
    					if($v["toolshow"]==1 ){
    						// 第三个循环判断模块节点
    						foreach($data as $k3 => $v3){
    							// 过来权限
    							if(substr($v3['name'], 0, 10)!="MisDynamic"){
    								if(!in_array($v3['name'], $m_list))  continue;
    							}
    							if (!isset ($access[strtoupper( APP_NAME )][strtoupper ($v3 ['name'])]) && !$_SESSION [C('ADMIN_AUTH_KEY')]) {
    								continue;
    							}
    							$newv3 = array();
    							if ($v3['level'] == 3 && $v3['pid'] == $v['id']) {
    								if (!$this->firstDetail) {
    									$this->firstDetail['name'] = $v3['name'];
    									$this->firstDetail['title'] = $v3['title'];
    									$this->firstDetail['check'] = $v3['id'];
    								}
    								$newv3['id'] = $v3['id'];
    								$newv3['pId'] = $v['id'];
    								$newv3['title'] =  $target=='ajax'?$v3['title']:$title; //光标提示信息
    								$newv3['name'] = missubstr($v3['title'],20,true); //结点名字，太多会被截取
    								$newv3['url'] =$target=='ajax'? $url."/jump/jump/model/".$v3['name']:$url."/model/".$v3['name'];//"__URL__/index/jump/1/model/".$v3[name];
    								$newv3['target']=$target=='ajax'?'ajax':'navTab';
    								$newv3['rel']=$rel;//"SystemConfigNumberBox";    									
    								$newv3['open'] = 'false';
    								$returnarr3[] = $newv3;
    							}
    						}
    						if($returnarr3){
    							$returnarr3[] = $newv2;
    						}
    					} else {
    						
    						// 第三个循环判断模块节点
    						foreach($data as $k3 => $v3){
    							if(substr($v3['name'], 0, 10)!="MisDynamic"){
    								if(!in_array($v3['name'], $m_list))  continue;
    							}
    							if (!isset ($access[strtoupper( APP_NAME )][strtoupper ($v3 ['name'])]) && !$_SESSION [C('ADMIN_AUTH_KEY')]) {
    								continue;
    							}
    							$newv3 = array();

    							if($v3['name']===$modelname){
    								$this->firstDetail['name'] = $v3['name'];
    								$this->firstDetail['title'] = $v3['title'];
    								$this->firstDetail['check'] = $v3['id'];
    							}
    							if ($v3['level'] == 3 && $v3['pid'] == $v['id']) {
    								if (!$this->firstDetail) {
    									$this->firstDetail['name'] = $v3['name'];
    									$this->firstDetail['title'] = $v3['title'];
    									$this->firstDetail['check'] = $v3['id'];
    								}
    								$newv3['id'] = $v3['id'];
    								$newv3['pId'] = -$v2['id'];
    								$newv3['title'] = $target=='ajax'?$v3['title']:$title; //光标提示信息
    								$newv3['name'] = missubstr($v3['title'],20,true); //结点名字，太多会被截取
    								$newv3['url'] = $target=='ajax'? $url."/jump/jump/model/".$v3['name']:$url."/model/".$v3['name'];//"__URL__/index/jump/1/model/".$v3[name];
    								$newv3['target']=$target=='ajax'?'ajax':'navTab';
    								$newv3['rel']=$rel;//"SystemConfigNumberBox";
    								$newv3['open'] = 'false';
    								$returnarr3[] = $newv3;
    							}
    						}
    					}
    					if($returnarr3){
    						$returnarr2 = array_merge($returnarr2,$returnarr3);
    					}
    				}
    			}
    		}
    		if($returnarr2){
    			$returnarr[] = $newv1;
    			$returnarr = array_merge($returnarr,$returnarr2);
    		}
    	}
    	return json_encode($returnarr);
    }
    function getRoleTreeCache($rel,$url='__URL__/index',$map2=array (),$modelname=false,$target='ajax',$title='',$modelN,$type=''){
    	$cModel=D("MisRuntimeData");
    	$list = $cModel->getRuntimeCache($modelN,$type);
    	if(!$list){
    		$list = $this->getRoleTree($rel,$url='__URL__/index',$map2=array(),$modelname=false,$target='ajax',$title='');
    		$cModel->setRuntimeCache($list,$modelN,$type);
    	}
    	return $list;
    	
    }
}
?>