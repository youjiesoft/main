<?php
/**
 * @Title: MisAutoEweModel
 * @Package package_name
 * @Description: todo(动态表单_自动生成-单据提醒)
 * @author 管理员
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2015-07-20 14:55:22
 * @version V1.0
*/
class MisAutoEweModel extends MisAutoEweExtendModel {
	protected $trueTableName = 'mis_auto_oyljp';		
	// 字段权限过滤
	protected $_filter = array();
	
	public $_auto =array(
		array("createid","getMemberId",self::MODEL_INSERT,"callback"),
		array("updateid","getMemberId",self::MODEL_UPDATE,"callback"),
		array("createtime","time",self::MODEL_INSERT,"function"),
		array("updatetime","time",self::MODEL_UPDATE,"function"),
		array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
		array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
		array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
		array('allnode','getActionName',self::MODEL_INSERT,'callback'),
	);
	//定时抓取
	public function settask(){
    	//调用短信接口
    	import('@.ORG.SmsHttp');
    	import ( '@.ORG.Browse' );
    	$smsHttp= new SmsHttp;
    	$model=D("MisAutoEwe");
    	$map['type']=2;
    	// 		$userid=11;//$_SESSION[C('USER_AUTH_KEY')]
    	// 		$map['_string']="find_in_set('".$userid."',userid)";
    	$map['status']=1;
    	$list=$model->where($map)->select();
    	if($list){
    		$userModel=D("User");
    		//循环list 查询满足条件的单据
    		foreach ($list as $key=>$val){
    			$modelname=$val['danjumingchen'];
    			$modelNameChinese=getFieldBy($modelname, "name", "title", "node");
    			$isprocess=getFieldBy($modelname, "name", "isprocess", "node");
    			$url=$isprocess==1?"auditView":"view";
    			$valmodel=M($val['danjutable']);
    			$newmap=array();
    			if($val['tixingtiaojian']){
    				$newmap['_string']=$val['tixingtiaojian'];
    			}else{
    				$newmap['_string']="1=1";
    			}
    			//验证浏览及权限
    			if( !isset($_SESSION['a']) ){
    				$newmap=$userModel->getAccessfilter($modelname,$newmap);
    			}
//     			//获取当前模型数据权限 by renl 20150626
//     			$broMap = Browse::getUserMap ($modelname);
//     			if($broMap){
//     				$newmap['_string'].=" and ".$broMap;
//     			}
    			$newmap['status']=1;
    			$resultlist=$valmodel->where($newmap)->select();
    			logs("我是查询==".$valmodel->getLastSql(),"dataremind");
    			if($resultlist){
    				//替换提醒信息
    				$msginfo=$val['tixingnarong'];
    				foreach ($resultlist as $rkey=>$rval){
    					foreach ($rval as $ekey=>$eval){
    						if(strpos($msginfo, $val['danjutable'].".".$ekey)!== false){
    							if(!$eval){
    								$ieval=0;
    							}else{
    								if(is_string($eval)){
    									$ieval="'".$eval."'";
    								}
    								if(is_numeric($eval)){
    									$ieval=$eval;
    								}
    							}
    							$msginfo=str_replace($val['danjutable'].".".$ekey,$ieval,$msginfo);
    						}
    					}
    					$MasData=array();
    					$MasData['modelname']=$val['danjumingchen'];
    					//提示信息
    					$ninfostr=strCalculate($msginfo);
    					$MasData['msginfo']=$ninfostr;
    					$MasData['pkey']=$rval['id'];
    					$MasData['porderno']=$rval['orderno'];
    					//定時任務
    					$MasData['type']=2;
    					$MasData['createtime']=time();
    					$MasData['createid']=$_SESSION[C('USER_AUTH_KEY')];
    					$masResultid=M("mis_system_data_remind_mas")->add($MasData);
    					M("mis_system_data_remind_mas")->commit();
    					$useridList=explode(',', $val['userid']);
    					$subModel=M("mis_system_data_remind_sub");
    					$noticeArr=explode(',', $val['noticetype']);
    					if(in_array('1', $noticeArr)){
    	
    					}
    					if(in_array('2', $noticeArr)){
    						//发送邮件
    						$messageTitle =$modelNameChinese.' 单据号为  '.$rval['orderno'].'请关注！';
    						//message信息拼装
    						$messageContent="";
    						$messageContent.='
					<p></p>
					<span></span>
					<div style="width:98%;">
					<p class="font_darkblue">您好！</p>
					<p>&nbsp;&nbsp;&nbsp;&nbsp;'. $modelNameChinese.' 单据号为  <a  target="navTab" rel="'.$modelName.'"  title="' . $modelNameChinese . '_查看"  href="__APP__/'.$modelname.'/'.$url.'/id/'.$rval['id'].'">'.$rval['orderno'].'</a></p>
							'.$msginfo.'
													</div>';
    	
    						$this->pushMessage(explode(',',$val['userid']), $messageTitle, $messageContent);
    					}
    					if(in_array('3', $noticeArr)){
	    					//发送邮件
	    						$messageTitle ="【".$modelNameChinese.'】 单据号为  '.$rval['orderno'].'请关注！';
	    						//message信息拼装
	    						$messageContent="";
	    						$messageContent.='
						<p></p>
						<span></span>
						<div style="width:98%;">
						<p class="font_darkblue">您好！</p>
						<p>&nbsp;&nbsp;&nbsp;&nbsp;【'. $modelNameChinese.'】 单据号为  <a  target="navTab" rel="'.$modelName.'"  title="' . $modelNameChinese . '_查看"  href="__APP__/'.$modelname.'/'.$url.'/id/'.$rval['id'].'">'.$rval['orderno'].'</a></p>
								'.$msginfo.'
														</div>';
    					}
    					if($useridList){
    						$recodesqldata=array();
	    					foreach ($useridList as $ukey=>$uval){
	    						if(in_array(1, $noticeArr)){
	    							//弹窗
	    							$recodesqldata[]="('{$masResultid}','{$uval}','1','".time()."','".time()."','1','{$_SESSION[C('USER_AUTH_KEY')]}')";
	    						}
	    						if(in_array(2, $noticeArr)){
	    							//邮件
	    							$recodesqldata[]="('{$masResultid}','{$uval}','2','".time()."','".time()."','1','{$_SESSION[C('USER_AUTH_KEY')]}')";
	    							
	    						}
	    						if(in_array(3, $noticeArr)){
	    							$mobile=getFieldBy($uval, "id", "mobile", "user");
	    							//短信操作
									if($mobile){
										$smsNotic=$smsHttp->getMessage($mobile,$messageContent,$httpType=1);
									}
	    							//手机
	    							$recodesqldata[]="('{$masResultid}','{$uval}','3','".time()."','".time()."','1','{$_SESSION[C('USER_AUTH_KEY')]}')";
	    						}
	    					}
	    					//插入sub表
	    					$insertsql = "INSERT INTO mis_system_data_remind_sub ";
	    					$insertsql .= " (masid,userid,operation,createtime,sendtime,issend,createid) ";
	    					$insertsql .= " VALUES ";
	    					$insertsql .= implode(',',$recodesqldata);
	    					$subModel->startTrans();
	    					$subModel->execute($insertsql);
	    					$subModel->commit();
    					}
    				}
    			}
    		}
    	}
    }
	public function main($modelname,$operation,$pkey,$targetname){
		$modelnewname=$targetname?$targetname:$modelname;
		//判断当前模型 当前表是否有提醒
		$map=array();
		//查詢當前模型是操作类型
		$map['operation']=array("like",'%'.$operation.'%');
		$map['danjumingchen']=$modelnewname;
		$map['status']=1;
		$model=D($modelname);
		$tablename=$model->getTableName();
		$map['danjutable']=$tablename;
	  	$remindList=$this->where($map)->select();
	  	logs("我是进入提醒".$this->getlastsql(),"dataremindselect");
		if($remindList){
			$subModel=D("mis_system_data_remind_sub");
			$masModel=D("mis_system_data_remind_mas");
			$MisSystemDataRemindMasView=D("MisSystemDataRemindMasView");
			$commAction=A("Common");
			$modelNameChinese=getFieldBy($modelname, "name", "title", "node");
			//查询当前模型是否带审批
			$isaudit=getFieldBy($modelname, "name", "isprocess", "node");
			$url=$isaudit==0?"view":"auditView";
			//替换类容如果包含字段，需要查看字段配置信息是否有转换函数 by xyz
			$scdmodel = D('SystemConfigDetail');
			$detailList = $scdmodel->getDetail($modelname,false);
			$detailListNew = array();
			foreach($detailList as $dk=>$dv){
				$detailListNew[$dv['name']] = $dv;
			}
			
			foreach ($remindList as $mkey=>$mval){
				$newmap=array();
				//判断是否满足此条数据控制
				if($mval['tixingtiaojian']){
					$defaultval=str_replace ( array ( '$time', '$uid'), array (time(),$_SESSION [C ( 'USER_AUTH_KEY' )]), $mval['tixingtiaojian'] );
					$newmap['_string']=$defaultval;
				}else{
					$newmap['_string']="1=1";
				}
				$newmap['id']=$pkey;
				$execulist=$model->where($newmap)->find();
				logs("我是满足条件".$model->getlastsql(),"dataremindinsql");
				//当前数据满足提醒
				if(!$execulist){
					continue;
				}
// 				$tn = $mval['tixingnarong'];
// 				$tn = "<eval>xyz_aa</eval>asdfasd<eval>xyz_bb</eval>";
// 				$n = preg_match_all("/<eval>(.*?)<\/eval>/",$tn,$match);
// 				if($n){
// 					foreach($match as $mk=>$mv){
// 						$fieldname = end(explode(".",$mv[1]));
// 						if($detailListNew[$fieldname]['func']){
// 							 getConfigFunction($v2,$detailListNew[$fieldname]['func'][0],$detailListNew[$fieldname]['funcdata'][0],$v1);
// 						}
// 					}
// 				}  
				
				$msgval=str_replace ( array ( '$time', '$uid'), array (transTime(time()),$_SESSION['loginUserName']), $mval['tixingnarong'] );
				//替换提醒信息
				$msginfo=$msgval;
				foreach ($execulist as $ekey=>$eval){
					if(strpos($msginfo, $tablename.".".$ekey)!== false){
						if(count($detailListNew[$ekey]['func'])>0){
							//函数循环层
							foreach($detailListNew[$ekey]['func'] as $fk=>$fv){
								$eval = getConfigFunction($eval,$fv,$detailListNew[$ekey]['funcdata'][$fk],$execulist);
							}
						}
						if(!$eval){
							$ieval=0;
						}else{
							if(is_string($eval)){
								$ieval="'".$eval."'";
							}
							if(is_numeric($eval)){
								$ieval=$eval;
							}
						}
						$msginfo=str_replace($tablename.".".$ekey,$ieval,$msginfo);
					}
				}
				 //获取当前是否有字段拼装用户
				 if($mval['checkfields']){
				 	$fieldList=explode(',', $mval['checkfields']);
				 	$fuserArr=array();
				 	foreach ($fieldList as $fkey=>$fval){
				 		if($_POST[$fval]){
				 			$fuserArr[$_POST[$fval]]=$_POST[$fval];
				 		}
				 	}
				 }
				 if($fuserArr){
				 	//提示信息
				 	$useridList=explode(',', $mval['userid']);
				 	foreach ($useridList as $uskey=>$usval){
				 		$fuserArr[$usval]=$usval;
				 	}
				 }
				 if($fuserArr){
				 	//提示用户
				 	$useridList=$fuserArr;
				 }else{
				 	//提示用户
				 	if($mval['userid']){
				 		$useridList=explode(',', $mval['userid']);
				 	}
				 }
				 //查询屏蔽此条信息的用户
				 $viewmap=array();
				 $viewmap['pkey']=$pkey;
				 $viewmap['modelname']=$modelname;
				 //下次不再提醒标示
				 $viewmap['substatus']=0;
				 foreach ($useridList as $puserkey=>$puserval){
				 	 $viewmap['userid']=$puserval;
				 	 $result=$MisSystemDataRemindMasView->where($viewmap)->find();
				 	 if($result){
				 		 unset($useridList[$puserkey]);
				 	 }
				 }
				 logs($useridList,"datareminduser");
				 if($useridList){
					 $MasData=array();
					 $MasData['modelname']=$modelname;
					 //提示信息
					 $ninfostr=strCalculate($msginfo);
					 $MasData['msginfo']=$ninfostr;
					 $MasData['pkey']=$pkey;
					 $MasData['porderno']=$_POST['orderno'];
					 //单据提醒
					 $MasData['type']=1;
					 $MasData['createtime']=time();
					 $MasData['createid']=$_SESSION[C('USER_AUTH_KEY')];
					 $masResultid=$masModel->add($MasData);
					$noticeArr=explode(',', $mval['noticetype']);
					if(in_array('2', $noticeArr)){ 
						//发送邮件
						$messageTitle =$modelNameChinese.' 单据号为  '.$_POST['orderno'].'请关注！';
						//message信息拼装
						$messageContent="";
						$messageContent.='
					<p></p>
					<span></span>
					<div style="width:98%;">
					<p class="font_darkblue">您好！</p>
					<p>&nbsp;&nbsp;&nbsp;&nbsp;'. $modelNameChinese.' 单据号为  <a  target="navTab" rel="'.$modelName.'"  title="' . $modelNameChinese . '_查看"  href="__APP__/'.$modelname.'/'.$url.'/id/'.$pkey.'">'.$_POST['orderno'].'</a></p>
							'.$msginfo.'
													</div>';
					
						$commAction->pushMessage($useridList, $messageTitle, $messageContent);
					}
	// 				if(in_array('3', $noticeArr)){ 
	// 					//发送手机短信
	// 					$commAction->SendTelmMsg($msginfo,$mval['userid']);
	// 				}
					if($useridList){ 
						$recodesqldata=array();
						$eamilsqldata=array();
						foreach ($useridList as $ukey=>$uval){
							if(in_array(1, $noticeArr)){
								//弹窗
								$recodesqldata[]="('{$masResultid}','{$uval}','1','".time()."','".time()."','1','{$_SESSION[C('USER_AUTH_KEY')]}')";
							}
							if(in_array(2, $noticeArr)){
								//邮件
								$eamilsqldata[]="('{$masResultid}','{$uval}','2','".time()."','".time()."','1','{$_SESSION[C('USER_AUTH_KEY')]}')";
							
							}
							if(in_array(3, $noticeArr)){
								//手机
							$recodesqldata[]="('{$masResultid}','{$uval}','3','".time()."','".time()."','1','{$_SESSION[C('USER_AUTH_KEY')]}')"; 							}
						}
							
						//弹窗插入sub表
    					$insertsql = "INSERT INTO mis_system_data_remind_sub ";
    					$insertsql .= " (masid,userid,operation,createtime,sendtime,issend,createid) ";
    					$insertsql .= " VALUES ";
    					$insertsql .= implode(',',$recodesqldata); 
    					//邮件插入已读记录表
    					if($eamilsqldata){
	    					$insertsql .= "INSERT INTO mis_system_data_eamilremind_sub ";
	    					$insertsql .= " (masid,userid,operation,createtime,sendtime,issend,createid) ";
	    					$insertsql .= " VALUES ";
	    					$insertsql .= implode(',',$eamilsqldata);
    					}
    					$subModel->startTrans();
    					$subModel->execute($insertsql);
    					
						logs("我是拼装sub".$insertsql,"dataremindsubsql");
					}
				}
			}
		}
	}
/**
	 * @Title: userAndRolegroupToTree
	 * @Description: todo(包含人员和角色的左侧树数组)
	 * @param unknown_type $rel
	 * @author 谢友志
	 * @date 2015-6-8 下午1:36:57
	 * @throws
	 */
	public function level3ofnode(){
		$model = M();
	   	$sql = "select a.id,a.`name`,a.`level`,a.title,b.id as pid,b.name as pname,b.title as ptitle,g.`name` as gtitle,g.id as group_id from node as a left join node as b ON a.pid=b.id left join `group` as g ON a.group_id=g.id where a.`level`=3 and a.`status`=1 and b.`status`=1 and g.`status`=1 order by g.id,b.id,a.id ";
	   	$list = $model->query($sql);
	   	return $list;
	}
	
	public function nodetree(){
		$nodelist = $this->level3ofnode();
		$newnodelist = array();
		$list = array();
		foreach($nodelist as $key=>$val){
			
			//组
			$newnodelist[-$val['group_id']]['id'] = -$val['group_id'];
			$newnodelist[-$val['group_id']]['pId']=-1001;
			$newnodelist[-$val['group_id']]['type']='post';
			$newnodelist[-$val['group_id']]['url']=__URL__."/index/jump/jump/type/1/id/".$v['id'];
			$newnodelist[-$val['group_id']]['target']='ajax';
			$newnodelist[-$val['group_id']]['rel']=$rel;
			$newnodelist[-$val['group_id']]['title']=$val['gtitle']; //光标提示信息
			$newnodelist[-$val['group_id']]['name']=$val['gtitle'];; //结点名字，太多会被截取,针对于现在的ZTREE，宽度不能超过18个字符。
			$newnodelist[-$val['group_id']]['open']=false;
			$newnodelist[-$val['group_id']]['isParent'] = true;
			
			//2级节点
			$newnodelist[$val['pid']]['id']=$val['pid'];
			$newnodelist[$val['pid']]['pId']=-$val['group_id'];
			$newnodelist[$val['pid']]['type']='post';
			$newnodelist[$val['pid']]['url']=__URL__."/index/jump/jump/type/1/id/".$v['id'];
			$newnodelist[$val['pid']]['target']='ajax';
			$newnodelist[$val['pid']]['rel']=$rel;
			$newnodelist[$val['pid']]['title']= $val['ptitle']; //光标提示信息
			$newnodelist[$val['pid']]['name']= $val['ptitle']; //结点名字，太多会被截取,针对于现在的ZTREE，宽度不能超过18个字符。
			$newnodelist[$val['pid']]['open']=false;
			$newnodelist[$val['pid']]['isParent'] = true;
			//3级节点
			$newnodelist[$val['id']]['id']=$val['id'];
			$newnodelist[$val['id']]['pId']=$val['pid'];
			$newnodelist[$val['id']]['type']='post';
			$newnodelist[$val['id']]['url']=__URL__."/index/jump/jump/type/1/id/".$v['id'];
			$newnodelist[$val['id']]['target']='ajax';
			$newnodelist[$val['id']]['rel']=$rel;
			$newnodelist[$val['id']]['title']=$val['title']; //光标提示信息
			$newnodelist[$val['id']]['name']=$val['title']; //结点名字，太多会被截取,针对于现在的ZTREE，宽度不能超过18个字符。
			$newnodelist[$val['id']]['open']=false;
			$newnodelist[$val['id']]['isParent'] = false;
		}
		$newlist = array();
		foreach($newnodelist as $k=>$v){
			$newlist[] = $v;
		}
		return $newlist;
	}
}
?>