<?php
/**
 * @Title: PublicExtend
 * @Package package_name
 * @Description: todo(public未知扩展函数)
 * @author everyone
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2011-4-7 下午5:13:20
 * @version V1.0
 */
class PublicExtendAction extends Action {
	
	/**
	 * @Title: nvigateTO 
	 * @Description:点击group组，组合2级和3级菜单  
	 * @author liminggang 
	 * @date 2014-10-13 下午8:22:48 
	 * @throws
	 */
	function nvigateTO(){
		//对菜单栏打开了导航的工具栏分组(一级菜单)在面板表(mis_system_panel)查询具体的数据并重组
		//左侧栏节点(二三级菜单)
		//第一步。获取group的id
		$groupid = $_REQUEST["groupid"];
		//获取节点
		$model = D("Public");
		$accessNode = $model->menuLeftTree($groupid);	
		$panel = A('MisSystemPanel');
		$_GET['nvigetTo']=true;
		$panel->lookupSysPanel($groupid);
		$this->assign("accessNode",$accessNode);
		$this->display();
	}
	//有点像是查询返回匹配数据集，返回JSON格式数据
	//输出到模板或者接口
	public function autocomplete(){
		$str = $_POST['query'];
		$val = $_POST['val'];
		$tab = $_POST['tab'];
		$key = $_POST['tabkey'] ? $_POST['tabkey'] : $_POST['key'];
		$dao = D($tab);
		$map[$val] = array('like','%'.$str.'%');
		$list = $dao->where($map)->field($key.','.$val)->select();
		if(is_array($list) && !empty($list)){
			foreach($list as $row){
				foreach($row as $k => $v){
					if($k == $val){
						$search[] = $v;
					}else{
						$realfield[] = $v;
					}
				}
			}
			$data = array(
					'query'=>$_POST[query],
					'suggestions'=>$search,
					'data'=>$realfield);
			echo json_encode($data);
		}else{
			echo '找不到记录';
		}
	}
	//根据列获取要查找的内容
	public function fifter(){
		$type = $_GET['type'];
		switch($type){
			case 'duty':
				$dao = D('Duty');
				$list = $dao->field('id,name')->select();
				$this->assign('list',$list);
				$this->assign('type', 'group');
				break;
			case 'role':
				$dao = D('Role');
				$list = $dao->field('id,name')->select();
				$this->assign('list',$list);
				$this->assign('type', 'group');
				break;
			case 'group':
				$dao = D('Group');
				$list = $dao->field('id,name')->select();
				$this->assign('list',$list);
				$this->assign('type', 'group');
				break;
			case 'time':
				$field = explode(',', $_GET['field']);
				$this->assign('filed1', $field[0]);
				$this->assign('filed2', $field[1]);
				$this->assign('type', $type);
				break;
			case 'count':
				$this->assign('type', $type);
				break;
			case 'status':
				$this->assign('type', $type);
				break;
		}
		$this->assign('action', str_replace('_', '/', $_GET['action']));
		$this->assign('field', $_GET['field']);
		$this->assign('showname', $_GET['showname']);
		$this->display();
	}
	//多条件查询中的添加查询条件
	public function new_fifter(){
		$type = $_GET['type'];
		switch($type){
			case 'duty':
				$dao = D('Duty');
				$list = $dao->field('id,name')->select();
				$data = "<select name='".$_GET['field']."'>";
				foreach($list as $key => $val){
					$data .= "<option value='".$val['id']."'>".$val['name']."</option>";
				}
				$data .= "</select>";
				break;
			case 'role':
				$dao = D('Role');
				$list = $dao->field('id,name')->select();
				$data = "<select name='".$_GET['field']."'>";
				foreach($list as $key => $val){
					$data .= "<option value='".$val['id']."'>".$val['name']."</option>";
				}
				$data .= "</select>";
				break;
			case 'group':
				$dao = D('Group');
				$list = $dao->field('id,name')->select();
				$data = "<select name='".$_GET['field']."'>";
				foreach($list as $key => $val){
					$data .= "<option value='".$val['id']."'>".$val['name']."</option>";
				}
				$data .= "</select>";
				break;
			case 'time':
				$field = explode(',', $_GET['field']);
				$data = "<input class='date textInput readonly' readonly='true' type='text' size='10' name='".$field[0]."' />
						<span class='limit' style='float:left;'>&nbsp;&nbsp;-&nbsp;&nbsp;</span>
						<input class='date textInput readonly' readonly='true' type='text' size='10' name='".$field[1]."' />";
				break;
			case 'count':
				$data = "<input class='textInput' type='text' size='5' name='".$_GET['field']."' />
						<label style='width:36px;'>规则</label>
						<select name='reg'>
						<option value='eq'>等于</option>
						<option value='egt'>大于等于</option>
						<option value='elt'>小于等于</option>
						</select>";
				break;
			case 'status':
				$data = "<select name='".$_GET['field']."'>
						<option value='1'>正常</option>
						<option value='0'>禁用</option>
						<option value='-1'>作废</option>
						</select>";
				break;
			default:
				$data = "<input class='textInput' type='text' size='25' name='".$_GET['field']."' />";
				break;
		}
		$unitDiv = "<div class='unit'><label field='".$_GET['field']."'>".$_GET['showname']."</label>";
		echo $unitDiv .= $data."<a href='#close' onclick='del_current_fifter(this);' class='close'>close</a></div>";
	}	
	
	//  查看系统信息
	public function main() {
		$info = array(
				'操作系统'=>PHP_OS,
				'运行环境'=>$_SERVER["SERVER_SOFTWARE"],
				'PHP运行方式'=>php_sapi_name(),
				//'ThinkPHP版本'=>THINK_VERSION.' [ <a href="http://thinkphp.cn" target="_blank">查看最新版本</a> ]',
				'上传附件限制'=>ini_get('upload_max_filesize'),
				'执行时间限制'=>ini_get('max_execution_time').'秒',
				'服务器时间'=>date("Y年n月j日 H:i:s"),
				'北京时间'=>gmdate("Y年n月j日 H:i:s",time()+8*3600),
				'服务器域名/IP'=>$_SERVER['SERVER_NAME'].' [ '.gethostbyname($_SERVER['SERVER_NAME']).' ]',
				'剩余空间'=>round((@disk_free_space(".")/(1024*1024)),2).'M',
				'register_globals'=>get_cfg_var("register_globals")=="1" ? "ON" : "OFF",
				'magic_quotes_gpc'=>(1===get_magic_quotes_gpc())?'YES':'NO',
				'magic_quotes_runtime'=>(1===get_magic_quotes_runtime())?'YES':'NO',
		);
		$this->assign('info',$info);
		$this->display();
	}
	
	/**
	 *
	 *删除图片 ajax请求
	 */
	public function pigdelete(){
		$imagename = $_POST['imagename'];
		$dir = UPLOAD_PATH;
		$dir = str_replace('Uploads','MisProductCode',$dir);
		$dir .= $imagename;
		$dir = iconv("UTF-8","GBK",$dir); //中文乱码问题
		if (file_exists($dir)){
			unlink($dir);
			exit('1');
		}
	}
	
	/*
	 * 获取维度信息
	* 作者：杨东 2011-12-21
	*/
	public function getDomain(){
		$map['status'] = '1';
		$list = getOptionList('Mis_inventory_domain',$map);
		$json = json_encode($list);
		echo $json;
	}
	/*
	 * 新增维度信息
	* 作者：杨东 2011-12-21
	*/
	public function new_domain(){
		//		$type = $_GET['type'];
		$data = "<input class='textInput' type='text' size='25' name='".$_GET['field']."' id='".$_GET['field']."' />";
		$unitDiv = "<div class='unit'><label field='".$_GET['field']."'>".$_GET['showname']."</label>";
		echo $unitDiv .= $data."<a href='#close' onclick='del_current_fifter(this);' class='close'>close</a></div>";
	}
	/*@wangcheng 短信回复内容掉回至数据库
	 * @parameter  string  $content  短信内容
	* @parameter  array   $telarr   电话号码（数组结构多电话）
	*/
	function  getReplyMsgByTel(){
		//	import ( '@.ORG.Snoopy' );
		//	$snoopy=new Snoopy();
		$settingmsginfo=C("SendPhoneMsg");
		$CorpID =$settingmsginfo['CorpID'];
		$Pwd	=$settingmsginfo['Pwd'];
		//$aryPara = array('CorpID' => $CorpID, 'Pwd'=> $Pwd);
		//$snoopy->submit("http://www.ht3g.com/htWS/linkWS.asmx/Get",$aryPara);
		//$r = $snoopy->results;
		//$r = $snoopy->_striptext($r);
	
		import('@.ORG.TelMsg.nusoap', '', $ext='.php');
		$client = new SoapClient('http://www.ht3g.com/htWS/linkWS.asmx?WSDL');
		//$content= iconv("UTF-8","GB2312",$content);
		$aryPara = array('CorpID' => $CorpID, 'Pwd'=> $Pwd);
		$re = $client->__call('Get',array('parameters'=> $aryPara));
		$r=$re->GetResult;
		//  		$r="||18723425975#284400同意#2013-07-10 17:33:55#";
		if($r){
			$allmsg = explode("||",$r);
			array_shift($allmsg);
			$replymodel = M("MessageVerify");
			$newreply=false;
			foreach( $allmsg as $k=>$v ){
				$data=array();
				$len = strlen($str);
				$text=substr($v,12,$len-21);
				$data["replytime"]=strtotime(substr($v,-20,19));
				$yesorno=substr($text,5,1);
				$data["replycontent"]=substr($text,6);
				$data['isreply']=1;
				$code=substr($text,0,4);
				$replytel=substr($v,0,11);
				$mapre["replytel"]=trim($replytel);
				$mapre["code"]=trim($code);
				$mapre["isreply"]=0;
				$vo = $replymodel->where($mapre)->find();
				if( $vo ){
					$re = $replymodel->where("id=".$vo['id'])->save($data);
					if( $re ){
						$mapdel["tablename"]=$vo["tablename"];
						$mapdel["tableid"]=$vo["tableid"];
						$mapdel["isreply"]=0;
						$replymodel->where($mapdel)->delete();
						if(trim($yesorno)==1){
							//$replycontro=A("MessageVerify");
							$auditstatus=$this->auditProcessBymsg($vo["tablename"],$vo["tableid"],$vo["replyuser"],$data["replycontent"]);
							if($auditstatus){
								$newreply=true;
							}
						}else{
							//$replycontro=A("MessageVerify");
							$auditstatus=$this->backProcessBymsg($vo["tablename"],$vo["tableid"],$vo["replyuser"],$data["replycontent"]);
							if($auditstatus){
								$newreply=true;
							}
						}
					}
				}
			}
			if($newreply){
				$this->transaction_model->commit();
			}else{
				$this->transaction_model->rollback();
			}
			exit;
		}
	}
	
	public function auditProcessBymsg($tablemodel,$id,$currentuserid,$replycontent="") {
		$_POST["backprocess"]="流程回退";
		$_POST["auditprocess"]="流程审核";
		$_POST["endprocess"]="流程结束";
			
		C('TOKEN_ON',false);
		$_SESSION[C('USER_AUTH_KEY')]=$currentuserid;
		$_POST['id']=$id;
		$_POST["audit_option_user"]="";
	
		$name=$tablemodel;
		$audit_option_user=$_POST["audit_option_user"];
		$pcmodel = D('ProcessConfig');
			
		$masid = $_POST['id'];
		$model2 = D ( $name );
		$vo = $model2->find($masid);
		$pcarr =  $pcmodel->getprocessinfo($name,$vo);
		//获取下级审核节点,赋值 curAuditUser 和 $tid
		$ostatus = explode(",",$vo['ostatus']);
		$alreadyauditnode = explode(",",$vo['alreadyauditnode']);
		$alluser = explode(";",$vo['auditUser']);
		$allnode = explode(",",$vo['allnode']);
	
		$i=0;
		$tid=-1;
		//查询流程信息
		$model_pr = M("process_relation");
		$map_pr["pid"] = $vo['ptmptid'];
		$process_info = $model_pr->where($map_pr)->order('sort asc')->getField("tid,userid,parallelid",true);
		$useri=0;
		foreach( $process_info as $k=>$v ){
			$process_info[$k]['userid']=$alluser[$useri];
			$useri++;
		}
			
		//根据 并行重新排列对应的节点
		$process_info_new=array();
		$process_info2=$process_info;
		foreach( $process_info as $k=>$v ){
			if($v['parallelid']>0){
				$arr=array();
				foreach($process_info2 as $k1=>$v1){
					if($v['parallelid']==$v1['parallelid']){
						unset($process_info2[$k1]);
						$arr[]=$k1;
					}
				}
				if($arr){
					$c=count($arr);
					if( $c>1 ){
						$process_info_new[]=$arr;
					}else{
						$process_info_new[]=$arr[0];
					}
				}
			}else{
				$process_info_new[]=$k;
			}
		}
		//print_r($process_info_new);
		$current_audit_node_id=$ostatus[0];
		$ostatus = array_reverse($ostatus);
		foreach($ostatus as $k=>$v ){
			$u=explode(",",$process_info[$v]['userid']);
			if(in_array($_SESSION[C('USER_AUTH_KEY')],$u)){//有该用户则审核到此步{
				$current_audit_node_id = $v;
				break;
			}
		}
			
		$process_info_new2=array_reverse($process_info_new);
		if($pcarr['crosslevel'] > 0 ){ //存在跨级
			foreach($process_info_new2 as $k=>$v){
				$out=false;
				if( is_array($v) ){
					foreach($v as $k1=>$v1){
						if( $v1==$current_audit_node_id ){
							unset($v[$k1]);
							$old_notyet_audit_node=array();
							$old_notyet_audit_user=array();
							foreach($v as $k2=>$v2){
								if(!in_array($v2,$alreadyauditnode) ){
									$u=explode(",",$process_info[$v2]['userid']);
									if(in_array($_SESSION[C('USER_AUTH_KEY')],$u)){
										$save_alreadyauditnode[]=$v2;
										continue;
									}
									$old_notyet_audit_node[]=$v2;
									$old_notyet_audit_user[]= $process_info[$v2]['userid'];
								}
							}
	
							if($alreadyauditnode){
								$_POST['alreadyauditnode'] =implode(",",$alreadyauditnode) . implode(",",$save_alreadyauditnode).",".$current_audit_node_id;
							}else{
								$_POST['alreadyauditnode'] = implode(",",$save_alreadyauditnode).",".$current_audit_node_id;
							}
							$_POST['alreadyauditnode'] = implode(",",$save_alreadyauditnode);
	
							if($old_notyet_audit_node ){
								if($old_notyet_audit_node){
									$tid=implode(",",$old_notyet_audit_node);
									$_POST["curAuditUser"]=implode(",",$old_notyet_audit_user);
									$_POST["curNodeUser"] =implode(",",$old_notyet_audit_user);
								}
							}
	
							if( isset($process_info_new2[$k-1]) ){
								if( $old_notyet_audit_node ){
									$corsslevel= $pcarr["crosslevel"];
									$j=0;
								}else{
									if(is_array($process_info_new2[$k-1])){//如果下一个节点是并行则要保存所有并行节点
										$tid=implode(",",$process_info_new2[$k-1]);
										$new_notyet_audit_user=array();
										foreach($v as $k2=>$v2){
											$new_notyet_audit_user[]=$process_info[$k-1]['userid'];
										}
										$_POST["curAuditUser"]=implode(",",$new_notyet_audit_user);
										$_POST["curNodeUser"] =implode(",",$new_notyet_audit_user);//保存下一级的节点用户
									}else{
										$tid = $process_info_new2[$k-1];
										$_POST["curAuditUser"]=$process_info[$k-1]['userid'];
										$_POST["curNodeUser"] =$process_info[$k-1]['userid'];//保存下一级的节点用户
									}
									$corsslevel= $pcarr["crosslevel"]+1;
									$j=1;
								}
								for($j;$j<$corsslevel;$j++){
									if(isset($process_info_new2[$k-$j-1])){//存在下级
										if(is_array($process_info_new2[$k-$j-1])){//如果下一个节点是并行则要保存所有并行节点
											$tid.=",".implode(",",$process_info_new2[$k-$j-1]);//下次可以审核的节点
											foreach($process_info_new2[$k-$j-1] as $k3=>$v3){//下次可以审核的用户id
												$_POST["curAuditUser"].=",".$process_info[$v3]['userid'];
											}
										}else{
											$tid.=",".$process_info_new2[$k-$j-1];//下次可以审核的节点
											$_POST["curAuditUser"] .=",".$process_info[$process_info_new2[$k-$j-1]]['userid'];//下次可以审核的用户id
										}
									}
								}
							}
							$out=true;
						}
					}
	
				}
				else{
					if( $v==$current_audit_node_id ){
						if( isset($process_info_new2[$k-1]) ){
							if(is_array($process_info_new2[$k-1])){//如果下一个节点是并行则要保存所有并行节点
								$tid=implode(",",$process_info_new2[$k-1]);
								$new_notyet_audit_user=array();
								foreach($process_info_new2[$k-1] as $k3=>$v3){//下次可以审核的用户id
									$new_notyet_audit_user[]=$process_info[$v3]['userid'];
	
								}
								$_POST["curAuditUser"] = implode(",",$new_notyet_audit_user);
								$_POST["curNodeUser"]  = implode(",",$new_notyet_audit_user);
							}else{
								$tid = $process_info_new2[$k-1];
								$_POST["curAuditUser"]=$process_info[$tid]['userid'];//下次可以审核的用户id
								$_POST["curNodeUser"] =$process_info[$tid]['userid'];
							}
	
							$corsslevel= $pcarr["crosslevel"];
							for($j=0;$j<$corsslevel;$j++){
								if(isset($process_info_new2[$k-$j-2])){
									$nowtid = $process_info_new2[$k-$j-2];
									if(is_array($nowtid)){//如果下一个节点是并行则要保存所有并行节点
										$tid.=",".implode($nowtid);
										foreach($nowtid as $k3=>$v3){//下次可以审核的用户id
											$_POST["curAuditUser"].=",".$process_info[$v3]['userid'];
										}
									}else{
										$tid.=",".$nowtid;
										$_POST["curAuditUser"] .=",".$process_info[$nowtid]['userid'];//下次可以审核的用户id
									}
								}
							}
						}
						$out=true;
					}
				}
				if($out) break;
			}
	
		}
		else{//不存在跨级
			foreach($process_info_new2 as $k=>$v){
				$out=false;
				if( is_array($v) ){
					foreach($v as $k1=>$v1){
						if( $v1==$current_audit_node_id ){
							unset($v[$k1]);
							$old_notyet_audit_node=array();
							$old_notyet_audit_user=array();
							foreach($v as $k2=>$v2){
								if(!in_array($v2,$alreadyauditnode) ){
									$u=explode(",",$process_info[$v2]['userid']);
									if(in_array($_SESSION[C('USER_AUTH_KEY')],$u)){
										$save_alreadyauditnode[]=$v2;
										continue;
									}
									$old_notyet_audit_node[]=$v2;
									$old_notyet_audit_user[]= $process_info[$v2]['userid'];
								}
							}
							if($alreadyauditnode){
								$_POST['alreadyauditnode'] =implode(",",$alreadyauditnode) . implode(",",$save_alreadyauditnode);
							}else{
								$_POST['alreadyauditnode'] = implode(",",$save_alreadyauditnode);
							}
	
							if($old_notyet_audit_node ){
								if($old_notyet_audit_node){
									$tid=implode(",",$old_notyet_audit_node);
									$_POST["curAuditUser"]=implode(",",$old_notyet_audit_user);
									$_POST["curNodeUser"] =implode(",",$old_notyet_audit_user);
								}
							}else{
								if( isset($process_info_new2[$k-1]) ){
									if(is_array($process_info_new2[$k-1])){//如果下一个节点是并行则要保存所有并行节点
										$tid=implode(",",$process_info_new2[$k-1]);
										$new_notyet_audit_user=array();
										foreach($v as $k2=>$v2){
											$new_notyet_audit_user[]=$process_info[$k-1]['userid'];
										}
										$_POST["curAuditUser"]=implode(",",$new_notyet_audit_user);
										$_POST["curNodeUser"] =implode(",",$new_notyet_audit_user);//保存下一级的节点用户
									}else{
										$tid = $process_info_new2[$k-1];
										$_POST["curAuditUser"]=$process_info[$k-1]['userid'];
										$_POST["curNodeUser"] =$process_info[$k-1]['userid'];//保存下一级的节点用户
									}
								}
							}
							$out=true;
						}
					}
	
				}
				else{
					if( $v==$current_audit_node_id ){
						if( isset($process_info_new2[$k-1]) ){
							if(is_array($process_info_new2[$k-1])){//如果下一个节点是并行则要保存所有并行节点
								$tid=implode(",",$process_info_new2[$k-1]);
								$new_notyet_audit_user=array();
								foreach($process_info_new2[$k-1] as $k3=>$v3){//下次可以审核的用户id
									$new_notyet_audit_user[]=$process_info[$v3]['userid'];
	
								}
								$_POST["curAuditUser"] = implode(",",$new_notyet_audit_user);
								$_POST["curNodeUser"]  = implode(",",$new_notyet_audit_user);
							}else{
								$tid = $process_info_new2[$k-1];
								$_POST["curAuditUser"]=$process_info[$tid]['userid'];//下次可以审核的用户id
								$_POST["curNodeUser"] =$process_info[$tid]['userid'];
							}
						}
						$out=true;
					}
				}
				if($out) break;
			}
		}
			
		//判断当前节点的下一个节点是否需要指定审核人
		if($tid!=-1){
			$model_pr = D("ProcessRelation");
			$next_tid =current( explode(",",$tid) );
			$map_nexttid["tid"] = $next_tid;
			$map_nexttid["pid"] = $vo['ptmptid'];
			$listnode = $model_pr->where($map_nexttid)->find();
			if($listnode["step"]==4){
				if( !$audit_option_user){
					return false;//$this->error("请选择下一个审核人");
				}else{
					foreach($allnode as $k=>$v){
						if($next_tid==$v){
							$alluser[$k]=$audit_option_user;
							$_POST['curNodeUser'] =$audit_option_user;
							if($_POST['curAuditUser']){
								$moreuser = strpos($_POST["curAuditUser"],",");
								if( $moreuser ){
									$ex=explode(",",$_POST['curAuditUser']);
									if($ex[0]==0){
										$ex[0]=$audit_option_user;
										$_POST['curAuditUser']=implode(",",$ex);
									}
								}
							}else{
								$_POST['curAuditUser'] =$audit_option_user;
							}
							break;
						}
					}
				}
			}
		}
	
		// 插入流程明细
		unset($_POST['id']);
		$model = D('ProcessInfoHistory');
		$_POST['dotype'] = $_POST['auditprocess'];
		$_POST['tablename']=$name;
		$_POST['tableid']=$masid;
		$_POST['doinfo']=$replycontent;
		$_POST['auditstatus'] =2;
		$_POST['ordercreateid'] = $vo["createid"];
		$_POST['orderno'] = $vo["orderno"];
		$_POST['ostatus'] = $current_audit_node_id;
		$_POST['pid'] = $vo['ptmptid'];
		if (false === $model->create ()) {
			return false;//$this->error ( $model->getError () );
		}
		$list=$model->add ();
		if( !$list ){
			return false;//$this->error(L ( '_ERROR_' ));
		}
		$_POST['auditUser'] = implode(";",$alluser);
		$_POST['ostatus'] = $tid;
		//保存已审核人id
		if( $vo['alreadyAuditUser'] ){
			$alreadyAuditUser = explode(",",$vo['alreadyAuditUser'].",".$_SESSION[C('USER_AUTH_KEY')]);
			$alreadyAuditUser = array_unique($alreadyAuditUser);
			$_POST['alreadyAuditUser']=implode(",",$alreadyAuditUser);
		}else{
			$_POST['alreadyAuditUser'] = $_SESSION[C('USER_AUTH_KEY')];
		}
		//判断是否终止于该审核节点 by wangcheng
		$tonextprocessnode = false;
		if($tid!=-1){
			$tonextprocessnode = $pcmodel->processJudgment( $vo ,$current_audit_node_id);
		}
		//end
			
		//如果流程结束插入相应的流程结束信息
		if ( $tid==-1 || $tonextprocessnode) {
			$_POST['dotype'] = $_POST['endprocess'];
			$_POST['doinfo'] = '';
			$_POST['tablename']=$name;
			$_POST['tableid']=$masid;
			$_POST['auditstatus'] =3;
			$_POST['isread']=0;
			$_POST['ordercreateid'] = $vo["createid"];
	
			if (false === $model->create ()) {
				return false;//$this->error ( $model->getError () );
			}
			$list=$model->add ();
			if( !$list ){
				return false;//$this->error(L ( '_ERROR_' ));
			}
			if (method_exists($this,"overProcess")) {
				$this->overProcess($masid);
			}
		}
	
		$_POST['id']=$masid;
		$_POST['auditState'] = 2;
		if ( $tid<0 || $tonextprocessnode) {
			$_POST['auditState'] = 3;
			$_POST['curAuditUser']='';
			$_POST["curNodeUser"]='';
		}
		//$this->update();
			
		//B('FilterString');
		$name=$tablemodel;
		$model = D ( $name );
		if (false === $model->create ()) {
			return false;//$this->error ( $model->getError () );
		}
		// 更新数据
		$list=$model->save ();
		if (false !== $list) {
			$module2=A($name);
			if (method_exists($module2,"_after_update")) {
				call_user_func(array(&$module2,"_after_update"),$list);
			}
			return true;//$this->success ( L('_SUCCESS_'));
		} else {
			return false;//$this->error ( L('_ERROR_') );
		}
			
	}
	
	//打回
	public function backProcessBymsg($tablemodel,$id,$currentuserid,$replycontent){
		$_POST["backprocess"]="流程回退";
		$_POST["auditprocess"]="流程审核";
		$_POST["endprocess"]="流程结束";
			
		C('TOKEN_ON',false);
		$_SESSION[C('USER_AUTH_KEY')]=$currentuserid;
		$_POST['id']=$id;
	
		// 插入流程明细
		$modelname =$tablemodel;
		$masid = $_POST['id'];
		$model2 = D ( $modelname );
		$vo = $model2->find($masid);
		unset($_POST['id']);
		$model = D('ProcessInfoHistory');
		// 		$_POST['doinfo'] = $_POST['remark'];
		$_POST['dotype'] = $_POST['backprocess'];
		$_POST['tablename']=$modelname;
		$_POST['tableid']=$masid;
		$_POST['doinfo']=$replycontent;
		$_POST['auditstatus'] =-1;
		$_POST['ordercreateid'] = $vo["createid"];
		$_POST['orderno'] = $vo["orderno"];
		$_POST['pid'] = $vo['ptmptid'];
		if (false === $model->create ()) {
			return false;//$this->error ( $model->getError () );
		}
		$list=$model->add ();
		$_POST['ostatus']=-1;
		$_POST['id']=$masid;
		if ($list!==false) { //保存成功
			$_POST['auditState']=-1;
			$_POST['isread']=0;
			//$_POST['auditUser']='';
			$_POST['curAuditUser']='';
			$_POST["curNodeUser"]='';
			if( $vo['alreadyAuditUser'] ){
				$alreadyAuditUser = explode(",",$vo['alreadyAuditUser'].",".$_SESSION[C('USER_AUTH_KEY')]);
				$alreadyAuditUser = array_unique($alreadyAuditUser);
				$_POST['alreadyAuditUser'] = implode(",",$alreadyAuditUser);
			}else{
				$_POST['alreadyAuditUser'] = $_SESSION[C('USER_AUTH_KEY')];
			}
			//$this->update();
			//B('FilterString');
			$name=$tablemodel;
			$model = D ( $name );
			if (false === $model->create ()) {
				return false;//$this->error ( $model->getError () );
			}
			// 更新数据
			$list=$model->save ();
			if (false !== $list) {
				$module2=A($name);
				if (method_exists($module2,"_after_update")) {
					call_user_func(array(&$module2,"_after_update"),$list);
				}
				return true;//$this->success ( L('_SUCCESS_'));
			} else {
				return false;//$this->error ( L('_ERROR_') );
			}
		} else {
			return false;//$this->error ( L('_ERROR_') );
		}
	}

	public function findpassword() {
		$this->assign('verificationcode',C("VERIFICATION_CODE"));
		$this->display("findpassword");
	}
	/**
	 *
	 * @Title: lookupgetvmsg
	 * @Description: todo(ajax请求验证用户名等信息 找回密码)
	 * @author renling
	 * @date 2014-5-12 上午11:47:54
	 * @throws
	 */
	public function lookupgetvmsg(){
		//值
		$account=$_POST['account'];
		$name=$_POST['name'];
		$val=$_POST['val'];
		$UserModel=D('User');
		$UserModel->checkFindPassword($account,$name,$val);
	}
	public function lookupresat(){
		//根据用户名查询用户信息
		$account=$_POST['account'];
		$UserModel=D('User');
		$UserVo=$UserModel->where("status=1 and account ='".$account."' or  zhname='".$account."'")->find();
		//随机生成6位数
		$rand=rand(100000,999999);
		$date['password']=pwdHash($rand);
		//修改该用户名密码
		$userResult=$UserModel->where("id=".$UserVo['id'])->save($date);
		$UserModel->commit();
		if(!$userResult){
			$this->error("密码修改失败,请联系系统管理员！");
		}
		$CommonAction=A("Common");
		if($_POST['resetValidate'] == 'mobile'){
			//第二，发送手机验证信息内容
			$content="您在特米洛企业信息化管理平台,重置密码为".$rand;
			//验证手机号码是否有重复的。
			$count=$UserModel->where('mobile = '.$UserVo['mobile']." and id neq ".$UserVo['id'])->count("*");
			if($count>1){
				$this->error("手机号码重复,请更换！");
				exit;
			}
			$result=$CommonAction->SendTelmMsg($content, $UserVo['mobile']);
			if($result){
				$_SESSION['msg']="手机".$UserVo['mobile'];
				$this->display("findpasswordnotice");
			}else{
				$this->error("操作失败！");
			}
		}else{
			//邮箱发送
			$title="[特米洛企业信息化管理平台]请查收您的密码";
			$content="亲爱的用户，您好！ 您在[特米洛企业信息化管理平台]的密码已重置为".$rand;
			$configEmailModel = D('MisSystemEmail');
			$vo['name'] = C("EMAIL_SERVERNAME");
			$vo['address'] ="brianl_yang";
			$vo['server'] = "163.com";
			$vo['email'] = C("EMAIL_SERVERADDRESS");
			$vo['pop3'] = "pop.163.com";
			$vo['smtp'] = "smtp.163.com";
			$vo['password'] =C("EMAIL_PASSWORD");
			$vo['pop3port']=110;
			$vo['smtpport']=25;
			$email=array($UserVo['email']);
			$result = $CommonAction->SendEmail($title, $content, $email, "", $vo, 1);
			if($result){
				$_SESSION['msg']="邮箱".$UserVo['email'];
				$this->display("findpasswordnotice");
			}else{
				$this->error("操作失败！");
			}
		}
	
	}
	
	/**
	 * @Title: lookupcomboxregulation
	 * @Description: todo(Ajax规则获取类型)
	 * @author libo
	 * @date 2014-3-17 下午4:36:34
	 * @throws
	 */
	public function lookupcomboxregulation(){
		//读取配置字段
		$scdmodel = D('SystemConfigDetail');
		$detailList = $scdmodel->getDetail($_POST['md']);
		$arr=array();
		foreach($detailList as $k=>$v){
			if($v['name']== $_POST['name']){
				$arr=$v;//筛选出当前数组
				break;
			}
		}
		//if(isset($arr['func'])){//是否有联查
		if(isset($arr['func'])&& $arr['func'][0][0]=="getFieldBy"){
			$tbmodel=D($arr['funcdata'][0][0][3]);
			$list=$tbmodel->field($arr['funcdata'][0][0][1].",".$arr['funcdata'][0][0][2])->where("status=1")->select();
			$arr2=array();
			foreach($list as $k=>$v){
				$arr2[$k][]=$v[$arr['funcdata'][0][0][1]];
				$arr2[$k][]=$v[$arr['funcdata'][0][0][2]];
			}
			echo json_encode($arr2);
		}else if($arr['func'][0][0]=="transTime"||$arr['func'][0][0]=="transtime"){
			$arr2=array();
			$arr2[0][]="day";
			$arr2[0][]="天";
			$arr2[1][]="month";
			$arr2[1][]="月";
			$arr2[2][]="year";
			$arr2[2][]="年";
			echo json_encode($arr2);
		}else{
			echo -1;
		}
	}
	
}
?>