<?php
/**
 * @Title: OAHelperAction
 * @Package OA助的请求数据
 * @Description: OA助手，请求类，取数据接口
 * @author eagle
 * @company 重庆特米洛科技有限公司˾
 * @copyright 重庆特米洛科技有限公司˾
 * @date 2014-2-08
 * @version V1.0
 */
class MobileBaseAction extends Action {
	protected $serviceRoot='http://112.74.23.43/zhsq';
	//事务模型
	protected $transaction_model=NULL;
	//无需认证的方法
	protected $notoken=array('getPolicyAdvice','getCommunityNews','getExpress','getLookAfter','getPartyBuilding','getRedCross','getStyleService','getSocialSecurityService','getComprehensiveManagementService','getFamilyPlanningService','getDisabledService','getCivilService','login','register','getVerificationCode','checkVerificationCode','resetPassword');
	
	function __construct(){
		//继承父类的构造函数
		parent::__construct();
		//是否注册方法标示位
		$register=false;
		//过滤掉无需登陆验证的方法
		foreach($_REQUEST['_URL_'] as $key => $val ){
			if(in_array($val,$this->notoken)){
				$register=true;
			}
		}
		//直接跳出构造函数
		if($register){ return ;}

		// ---- 调试 信息信息 ---- jiangxiaobo
		// try{
		// 	throw new Exception('xxxx');
		// }catch(Exception $e){
		// 	$token=unserialize(base64_decode(strrev($_REQUEST['token'])));
		// 	return $this->getReturnData($data,'json',$code,$token);
		// }
		// ---- 调试 信息信息 ---- jiangxiaobo
		
		try{
			if($_REQUEST['token']){
				//对token做解析
				//1反向转换2base64解密3反序列化
				$token=unserialize(base64_decode(strrev($_REQUEST['token'])));
				try{
					if((!empty($token['apptype'])) && (!empty($token['phone'])) && (!empty($token['password']))){
						//验证电话、密码和端口
						$MobileUserBaseModel = D("MobileUserBase");
						$MobileUserBaseModel->checkLogin($token['phone'],$token['password'],$token['apptype']);
					}else{
						throw new Exception('Division by zero.');
					}
				}catch(Exception $e){
					$data=array();
					$code='1003';
					$msg='1003:令牌错误';
					return $this->getReturnData($data,'json',$code,$msg);
				}
			}else{
				throw new Exception('Division by zero.');
			}
		}catch(Exception $e){
			$data=array();
			$code='1003';
			$msg='1003:令牌不存在';
			return $this->getReturnData($data,'json',$code,$msg);
		}
	}
	//此方法用来处理返回数据类型，为JSON  还是  array
	public  function getReturnData($returnData=array(),$returnType='json',$code='1001',$msg='系统内置错误'){
		if($returnType=='json'){
			$data = array(
					'code'=>$code,
					'data'=>$returnData,
					'msg'=>$msg,
			);
			echo json_encode($data);
			exit;
	
		}else if($returnType=='arr'){
			if($returnData){
				if(is_array($returnData)){
					$returnData=$returnData;
				}else{
					$returnData=(array)$returnData;
				}
				return $returnData;
			}
		}
	}
 
	//重复
	protected function  setToken($userInfo){
		$token= serialize($userInfo);
		$token=str_replace('=','',base64_encode($token));//base64加密
		$token=strrev($token);//反转字符串
		return $token;
	}
	//重复
	protected function CURD($modelname,$opration,$data,$condition=''){
		if(empty($modelname) && empty($opration))return false;
		$this->transaction_model=M();
		$this->transaction_model->startTrans();
		switch($opration){
			case 'add':
				//$result=M('mis_auto_ormeo_sub_guanzhusousuo')->add($data);
				
				$result=D($modelname)->add($data);
				break;
			case 'addAll':
				$result=D($modelname)->addAll($data);
				break;				
			case 'save':
				if(empty($condition))return false;
				$result=D($modelname)->where($condition)->save($data);
				//dump(D($modelname)->getlastsql());
				break;
			case 'delete':
				if(empty($condition))return false;
				$result=D($modelname)->where($condition)->delete();
				break;
			case 'select':
				if(empty($condition))return false;
				$result=D($modelname)->where($condition)->find();
				break;
			case 'query':
				$result=D($modelname)->query($data);
				break;
			case 'execute':
				$result=D($modelname)->execute($data);
				break;				
		}
		//$this->transaction_model->commit();
		//echo D($modelname)->getLastSql();
		logs(D($modelname)->getLastSql(),"sql");
		if($result===false){
			$this->transaction_model->rollback();
		}else{
			$this->transaction_model->commit();
		}
		return $result;
	}
	/**
	 * 创建目录 2012-12-17
	 * @author wangcheng
	 * @param string $path 创建目录路径
	 */
	public function createFolders($path) {
		if (!file_exists($path)) {
			$this->createFolders(dirname($path));
			mkdir($path, 0777);
		}
	}	
	
	/** php 接收流文件
	 * @param  String  $file 接收后保存的文件名
	 * @return boolean
	 */
	protected function receiveStreamFile($receiveFile){
		//获取文件流所有内容
		$pImg=$_FILES['upname'];
		if($pImg['error']==UPLOAD_ERR_OK){
			//$extName=strtolower(end(explode('.',$pImg['name'])));
			//第三步：数据流存储
			move_uploaded_file($pImg['tmp_name'],$receiveFile);
			return true;
		}else{
			return false;
		}
	}
	
	/** php 接收流文件
	 * @param  String  $file 接收后保存的文件名
	 * @return boolean
	 */
	protected function receiveStreamFileold($receiveFile){
		$streamData = isset($GLOBALS['HTTP_RAW_POST_DATA'])? $GLOBALS['HTTP_RAW_POST_DATA'] : '';
	
		if(empty($streamData)){
			$streamData = file_get_contents('php://input');
		}
	
		if($streamData!=''){
			$ret = file_put_contents($receiveFile, $streamData, true);
		}else{
			$ret = false;
		}
		return $ret;
	}
	/**
	 * @Title: getFormList
	 * @Description: todo(获取列表数据)
	 * @paramate $modelname  string   模型名称
	 * @paramate $fileds     string   显示的字段
	 * @return Ambigous <array, unknown>
	 * @author liminggang
	 * @date 2015年4月23日 下午2:44:08
	 * @throws
	 */
	protected function getFormList($modelname,$fileds='id,orderno',$seachFields,$condition,$attachedModel,$type=true){
		//定义一个分页条数
		$pageSize=intval($_REQUEST['pagesize'])?intval($_REQUEST['pagesize']):10;
		//获取分页条码数
		$pageNum = intval($_REQUEST['pagenum'])?intval($_REQUEST['pagenum']):1;
		
		//$map = array();
		//获取检索字段名称
		$searchval   = $_REQUEST['keyword'];
		//获取排序字段
		$orderby     = $_REQUEST['orderby']?$_REQUEST['orderby']:'id';
		//获取排序字段
		$orderbytype = $_REQUEST['orderbytype']?$_REQUEST['orderbytype']:'desc';
		//拼装order
		$order       =$orderby." ".$orderbytype;
		//检索条件
		if($searchval){
			if(!empty($seachFields)){
				$seachFields=str_replace(",", "|", $seachFields);
				$condition[$seachFields] = array('like','%'.$searchval.'%');
			}
		}
		///////////////////////////////////////////////////
		// 自定义多条件检索，条件间为且,
		//	keywords存在时模糊匹配失效
		//	by nbmxkj 20151008
		// 	示例：keywords/id,jiage,name/id/1-4/name/基本/jiage/23/
		//	条件解析为 where id between 1 and 4 and jiage=23 and name = '基本'
		///////////////////////////////////////////////////
		$searchList=$_REQUEST['keywords'];
		$conditionTemp = '';
		if($searchList){
			$tempSearchList = explode(',',$searchList);
			if(is_array($tempSearchList)){
				foreach($tempSearchList as $k=>$v){
				    if($_REQUEST[$v]){
    					$ret = preg_match_all('/\$(.*?)\$/',$_REQUEST[$v],$match);
    					//$check = strpos($_REQUEST[$v] , '-');
    					if(strpos($_REQUEST[$v] , '-')){
    						$tempKeyWord = explode('-',$_REQUEST[$v]);
    						if($tempKeyWord[0] && $tempKeyWord[1]){
    							$conditionTemp[$v] = array(between,array($tempKeyWord[0],$tempKeyWord[1]));
    						}
    					}else{
    						$conditionTemp[$v] = $_REQUEST[$v];
    					}
    					
    					if($ret){
    						$_REQUEST[$v] = preg_replace('/\$.*?\$/','',$_REQUEST[$v]);
    						switch($match[1][0]){
    							// 大于
    							case 'gt':
    								$conditionTemp[$v] = array('gt',$_REQUEST[$v]);
    								break;
    							// 小于
    							case 'lt':
    								$conditionTemp[$v] = array('lt',$_REQUEST[$v]);
    								break;
    							// 不等于
    							case 'neq':
    								$conditionTemp[$v] = array('neq',$_REQUEST[$v]);
    								break;
    							// 大于等于
    							case 'egt':
    								$conditionTemp[$v] = array('egt',$_REQUEST[$v]);
    								break;
    							// 小于等于
    							case 'elt':
    								$conditionTemp[$v] = array('elt',$_REQUEST[$v]);
    								break;
    							default:
    								$conditionTemp[$v] = $_REQUEST[$v];
    								break;
    						}
    					}
    				}
				}
			}
		}
		if(is_array($conditionTemp)){
			$condition = $conditionTemp;
		}
		////////////////////////////////////////
		// end
		////////////////////////////////////////
		//实例化项目数据模型
		$model = D($modelname);
		//查询总条数
		$count = $model->where ( $condition )->count ( '*' );
		
		
		//查询首页进入是否有数据
		 $site=$_REQUEST['site'];
		if($site==1 && $count==0){
			unset($condition[$seachFields]);
			$count = $model->where ( $condition )->count ( '*' );
		} 
		
		
		$totalPageNum = ($count  +  $pageSize  - 1) / $pageSize;
		$totalPageNum = (int)$totalPageNum;
		if($totalPageNum<$pageNum){
			$pageNum=$totalPageNum;
		}
		$firstNum = ($pageNum-1)*$pageSize;
		$nextNum = $pageNum*$pageSize;
		//$list = $model->where($condition)->field($fileds)->order($order)->limit($firstNum . ',' . $nextNum)->select();
		$list = $model->where($condition)->order($order)->limit($firstNum . ',' . $pageSize)->select();
		
		//查询有关附件
		$uploadPath=$this->serviceRoot."/Public/Uploads/";
		foreach ($list as $lk=>$lv){
			$attachModel=D('MisAttachedRecord');
			if(!empty($attachedModel)){
				$attarry=$this->getAttachedRecordList($attachModel,$lv['id'],true,true,$attachedModel,0,false,false);
			}else{
				$attarry=$this->getAttachedRecordList($modelname,$lv['id'],true,true,$modelname,0,false,false);
					
			}
			foreach ($attarry as $ak=>$av){
				$attarryInfo=array();
				foreach($av as $aak=>$aav){
					$attarryInfo[]=$uploadPath.$aav['attached'];
				}
				$list[$lk][$ak]=implode(',', $attarryInfo);
			}
			//查询相关地址
			$areaModel = M('MisAddressRecord');
			$areainfomap['tablename'] = $modelname;
			$areainfomap['tableid'] = $lv['id'] ;
			$areaArr = $areaModel->where($areainfomap)->select();
			foreach ($areaArr as $key=>$val){
				$list[$lk][$val['fieldname'].'_coordinatex']=$val['coordinatex']; 
				$list[$lk][$val['fieldname'].'_coordinatey']=$val['coordinatey'];
			}
		}
		
		//print_r($model->getLastSql());
		/*
		 *根据list.in.php转换数据格式
		 *@liminggang
		 */
		$volist = $this->getDateListData($modelname, $list);
		$data=array();
		$data['list']=$volist;
		$data['pagesize']=array("pagesize"=>$pageSize);
		$data['pagenum']=array("pagenum"=>$totalPageNum);
		$data['curpagenum']=array("curpagenum"=>$pageNum);
		$data['count']=array("count"=>$count);
		if($type){
			return $this->getReturnData($data,'json','1000',"获取数据成功");
		}else{
			return $data;
		}
		
	}
	
	
	/**
	 * @Title: getFormListAll
	 * @Description: todo(获取所有数据)
	 * @paramate $modelname  string   模型名称
	 * @paramate $fileds     string   显示的字段
	 * @return Ambigous <array, unknown>
	 * @author liminggang
	 * @date 2015年4月23日 下午2:44:08
	 * @throws
	 */
	protected function getFormListAll($modelname,$fileds='id,orderno',$seachFields,$condition,$type=true){
	
		//$map = array();
		//获取检索字段名称
		$searchval   = $_REQUEST['keyword'];
		//获取排序字段
		$orderby     = $_REQUEST['orderby']?$_REQUEST['orderby']:'id';
		//获取排序字段
		$orderbytype = $_REQUEST['orderbytype']?$_REQUEST['orderbytype']:'asc';
		//拼装order
		$order       =$orderby." ".$orderbytype;
		//检索条件
		if($searchval){
			if(!empty($seachFields)){
				$seachFields=str_replace(",", "|", $seachFields);
				$condition[$seachFields] = array('like','%'.$searchval.'%');
			}
		}
		//实例化项目数据模型
		$model = D($modelname);
		//查询总条数
		$count = $model->where ( $condition )->count ( '*' );
		//查询首页进入是否有数据
		 $site=$_REQUEST['site'];
		if($site==1 && $count==0){
			unset($condition[$seachFields]);
			$count = $model->where ( $condition )->count ( '*' );
		} 
		
		//$list = $model->where($condition)->field($fileds)->order($order)->limit($firstNum . ',' . $nextNum)->select();
		$list = $model->where($condition)->order($order)->select();
		//查询有关附件
		$uploadPath=$this->serviceRoot."/Public/Uploads/";
		foreach ($list as $lk=>$lv){
			$attachModel=D('MisAttachedRecord');
			$attarry=$this->getAttachedRecordList($modelname,$lv['id'],true,true,$modelname,0,false,false);
			foreach ($attarry as $ak=>$av){
				$attarryInfo=array();
				foreach($av as $aak=>$aav){
					$attarryInfo[]=$uploadPath.$aav['attached'];
				}
				$list[$lk][$ak]=implode(',', $attarryInfo);
			}
		}
// 		print_r($model->getLastSql()); 
		/*
		 *根据list.in.php转换数据格式
		 *@liminggang
		 */
		$volist = $this->getDateListData($modelname, $list);
		$data=array();
		$data['list']=$volist;
		if($type){
			return $this->getReturnData($data,'json','1000',"获取数据成功");
		}else{
			return $data;
		}
		
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
		$detailList = $scdmodel->getDetail($modelname,false,'','sortnum','status');
		foreach($volist as $key=>$val){
			foreach($detailList as $k1=>$v1){				
				if($v1['name'] =="action" || $v1['name'] =="auditState" || strpos(strtolower($v1['name']),"datatable")!==false){
					continue;
				}
				//下面这个if会过滤掉一批字段中不存在的转换目标值。这里取消这种判断
				//if($val[$v1['name']]){
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
						$volist[$key][$v1['name'].'_souce'] = $val[$v1['name']];
					}
				//}
			}
		}
		return $volist;
	}
	
	
	
	/**
	 * @Title: getFormInfo
	 * @Description: todo(根据模型名称跟当前ID获取当前表单信息)
	 * @author 谢友志
	 * @date 2015年4月23日 下午6:24:30
	 * $arrayType  1 带key value的二维数组  ；2 一维数组
	 * @throws
	 */
	protected function getFormInfo($modelname,$arrayType=2,$type=true){
		//获取表单ID值
		$id = $_REQUEST['id'];
		if(!$id){
			return $this->getReturnData($data,'json','1002',"缺少固定参数，请检查");
		}
		if(!$modelname){
			return $this->getReturnData($data,'json','1002',"缺少固定参数，请检查");
		}
		//获取配置文件以及数据
		$model 		= D('SystemConfigDetail');
		//从动态配置获取到数据信息
		$result=$model->GetDynamicconfData($modelname,$id,"status","id",true);
		//message信息拼装
		$message=array();
		foreach($result as $key => $val){
			//初始化一个字符串容器
			if($arrayType==1){
				foreach($val as $subkey => $subval){
					$message[$key] = array(
							'name'=>$subkey,
							'value'=>$subval,
					);
				}
			}else{
				foreach($val as $subkey => $subval){
					// 				$message[$key] = array(
					// 						'name'=>$subkey,
					// 						'value'=>$subval,
					// 				);
					$message[$key]=$subval;
				}
			}
		}
		
		
		//查询有关附件
		$attachModel=D('MisAttachedRecord');
		$attarry=$this->getAttachedRecordList($modelname,$message['id'],true,true,$modelname,0,false,false);
		$uploadPath=$this->serviceRoot."/Public/Uploads/";
		foreach ($attarry as $ak=>$av){
			$attarryInfo=array();
			foreach($av as $aak=>$aav){
				$attarryInfo[]=$uploadPath.$aav['attached'];
			}
			$message[$ak]=implode(',', $attarryInfo);
		}
		//查询相关地址
		$areaModel = M('MisAddressRecord');
		$areainfomap['tablename'] = $modelname;
		$areainfomap['tableid'] = $message['id'] ;
		$areaArr = $areaModel->where($areainfomap)->select();
		foreach ($areaArr as $key=>$val){
			$message[$val['fieldname'].'_coordinatex']=$val['coordinatex'];
			$message[$val['fieldname'].'_coordinatey']=$val['coordinatey'];
		}
		$data = array('title'=>getFieldBy($modelname, "name", "title", "node"),'data'=>$message);
		if($type){
			return $this->getReturnData($data,'json','1000',"获取数据成功");
		}else{
			return $data;
		}
		
	}
	

	
	/**
	 * 对array进行json编码
	 *
	 * @param mixed $array
	 * @return 返回 array 值的 JSON 形式
	 * 可以写成类方式调用如下： $json = new json (); $data = $json->json_encode ( $value );
	 */
   protected	function json_encode(&$array) {
		$this->do_urlencode ( $array );
		$str = json_encode ( $array );
		$str = urldecode ( $str );
		return $str;
	}
	
	/**
	 * 递归遍历var,对var进行url编码
	 *
	 * @param mixed $var
	 */
	protected function do_urlencode(&$var) {
		if (is_array ( $var )) {
			// 数组,继续遍历
			foreach ( $var as $key => &$value ) {
				$this->do_urlencode ( $value );
			}
		} else {
			// 非数组,进行url编码
			$var = urlencode ( $var );
		}
	}
	/**
	 * @Title: getBasicData
	 * @Description: todo(获取基础档案数据方法)
	 * @param string $key 基础数据key值  及对应数据库mis_system_selectlist的name值
	 * @return 返回基础数据array 格式为 array('1'=>'a','2'=>'b')  key为数据库存储值，val为页面显示值
	 * @author 黎明刚
	 * @date 2015年6月6日 下午10:47:17
	 * @throws
	 */
	protected function getBasicData($key){
		$list = require('./Dynamicconf/System/selectlist.inc.php');
		$list = $list[$key][$key];		
		$data=array();
		foreach($list as $key => $val){
			$data[]=$val;
		}
		return $data;
	}
	/**
	 * @Title: getMisSystemDataview
	 * @Description: todo(获取系统视图数据方法)
	 * @param $name 视图名称
	 * @param $name 视图名称
	 * @param $name 视图名称
	 * @param $name 视图名称
	 * @return 返回基础数据array 格式为 array('1'=>'a','2'=>'b')  key为数据库存储值，val为页面显示值
	 * @author 黎明刚
	 * @date 2015年6月6日 下午10:47:17
	 * @throws
	 */	
	protected function getMisSystemDataview($name,$map,$returnType='json',$arrayType=2){
		//视图信息
		$sysviewmap=array();
		$sysviewmap['name']=$name;
		$viewInfo=D("MisSystemDataviewMas")->where($sysviewmap)->find();
        if($viewInfo===false){
        	return $this->getReturnData($data,$returnType,'1002',"获取视图失败");
        }else{
        	$result = M()->where($map)->query($viewInfo['spellwheresql'],true);
        	//echo M()->getLastSql();
        	if($result===false){
        		return $this->getReturnData($data,$returnType,'1002',"查询视图失败");
        	}else{
        		//message信息拼装
        		$message=array();
        		foreach($result as $key => $val){
        			//初始化一个字符串容器
        			if($arrayType==1){
        				foreach($val as $subkey => $subval){
        					$message[$key] = array(
        							'name'=>$subkey,
        							'value'=>$subval,
        					);
        				}
        			}else{
        				foreach($val as $subkey => $subval){
        					// 				$message[$key] = array(
        					// 						'name'=>$subkey,
        					// 						'value'=>$subval,
        					// 				);
        					$message[$subkey]=$subval;
        				}
        			}
        		}
	        	$data = array('list'=>$message);
	        	return $this->getReturnData($data,$returnType,'1000',"获取数据成功");
        	}
        }
	}
	
	public  function smsTest(){
		$type=$_REQUEST['type']?$_REQUEST['type']:1;
		$phone='13983475645';//手机号码
		$verifcode=rand(1000,9999);		
		$content="创客荟APP本次验证码:".$verifcode.",将于60S内超期";
		//调用短信接口
		import('@.ORG.SmsHttp');
		$smsHttp= new SmsHttp;
		switch($type){
			case 1://短信操作
				$smsNotic=$smsHttp->getMessage($phone,$content,$httpType=1);
				print_R("调试类型".$type.":");
				print_R($phone.$content);
				print_R($smsNotic);
				break;
			case 2://获取上行短信  
				$smsNotic=$smsHttp->getMessage($phone,$content,$httpType=2);
				print_R("调试类型".$type.":");
				print_R($smsNotic);
				break;
			case 3://获取报告
				$smsNotic=$smsHttp->getMessage($phone,$content,$httpType=3);
				print_R("调试类型".$type.":");
				print_R($smsNotic);
				break;
			case 4://查询余额
				$smsNotic=$smsHttp->getMessage($phone,$content,$httpType=4);
				print_R("调试类型".$type.":");
				print_R($smsNotic);
				break;									
		}

	}
	
	public function getShareBase($channel,$QRCODE,$downloadUrl,$title,$content,$returnType='json'){
		if($channel){
			$data['QRCODE']=$QRCODE;
			$data['downloadUrl']=$downloadUrl;
			$data['title']=$title;
			$data['content']=$content;
			$code='1000';
			$msg='1000:分享成功';
			return $this->getReturnData($data,$returnType,$code,$msg);
		}else{
			$data=array();
			$code='1003';
			$msg='1003:非法操作';
			return $this->getReturnData($data,$returnType,$code,$msg);			
		}
	}
	
	public function getVersionBase($channel,$version,$build,$downloadUrl,$returnType='json'){
		if($channel){
			$data['version']=$version;
			$data['bulid']=$build;
			$data['downloadUrl']=$downloadUrl;
			$data['isUpdate']=1;
			$code='1000';
			$msg='1000:获取版本成功';
			return $this->getReturnData($data,$returnType,$code,$msg);
		}else{
			$data=array();
			$code='1003';
			$msg='1003:非法操作';
			return $this->getReturnData($data,$returnType,$code,$msg);
		}		
	}
	
	/**
	 +----------------------------------------------------------
	 * 外部邮件发送
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param string $subject 邮件主题
	 * @param string $body 邮件内容
	 * @param array  $addresses 发送地址,英文逗号分隔
	 * @param array  $attachments 附件列表
	 * @param array  $configemail 邮件发送服务配置
	 * @param int	 $type		  是否为系统 邮件，0表示为系统邮件，1表示为个人邮件
	 +----------------------------------------------------------
	 * @return  string
	 +----------------------------------------------------------
	 * @throws ThinkExecption
	 +----------------------------------------------------------
	 */
	public function SendEmail($subject, $body, $addresses = array(),$attachments = array(),$configemail=array(),$type=0){
		import("@.ORG.Mailer.PHPMailer");
		$mail = new PHPMailer();
		$mail->CharSet = "UTF-8";
		$mail->IsSMTP(); // telling the class to use SMTP
		//$mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
		// 1 = errors and messages
		// 2 = messages only
		$mail->SMTPAuth   = true;                  // enable SMTP authentication
		if ($configemail['loginaccform'] ==1){
			$SMTPaccountusername = $configemail['address'];
		}else {
			$SMTPaccountusername = $configemail['email'];
		}
		if($type){
			$mail->Host       = $configemail['smtp'];         // sets the SMTP server
			$mail->Port       = $configemail['smtpport'];        // set the SMTP port for the GMAIL server
			$mail->Username   = $SMTPaccountusername;    // SMTP account username
			$mail->Password   = $configemail['password'];     // SMTP account password
			$mail->SetFrom($configemail['email'],$configemail['name']);          //必填，发件人邮箱
			$mail->AddReplyTo($configemail['email'], $configemail['name']);//回复EMAIL(留空则为发件人EMAIL回复名称留空则为发件人名称)
		}else {
			$mail->Host       = C('SMTP_HOST');         // sets the SMTP server
			$mail->Port       = C('SMTP_PORT');        // set the SMTP port for the GMAIL server
			$mail->Username   = C('EMAIL_USERNAME');    // SMTP account username
			$mail->Password   = C('EMAIL_PASSWORD');     // SMTP account password
			$mail->SetFrom(C('EMAIL_SERVERADDRESS'),C('EMAIL_SERVERNAME'));          //必填，发件人邮箱
		}
		$mail->Subject    = $subject;
		$body = eregi_replace("[\]",'',$body);
		$mail->MsgHTML($body);
		//地址分割
		foreach($addresses as $key=>$value){
			if ($value !== ''){
				$mail->AddAddress($value,$configemail['name']);
			}else {//发内部邮件
				$_POST['messageType'] = 0;//messageType改为站内信
				if (method_exists($this,"_before_insert")) {
					call_user_func(array(&$this,"_before_insert"));
				}
				$this->insert();
				if (method_exists($this,"_after_insert")) {
					call_user_func(array(&$this,"_after_insert"),$list);
				}
			}
		}
		foreach($attachments as $attachment){
			$mail->AddAttachment($attachment);
		}
		if(!$mail->Send()) {
			return false;//"发送失败，请联系管理员";
		} else {
			return true;
		}
	}
	
/**
	 * @Title: getAttachedRecordList
	 * @Description: todo(获取附件信息查询方法)
	 * @param 单据ID $tableid 单据对应的ID
	 * @param 是否显示在线查看 $online 是否显示在线查看按钮，默认为true显示
	 * @param 是否显示归档 $archived 是否显示归档按钮，默认为true显示
	 * @param 单据的控制器名称 $tablename 单据对应的控制器名称
	 * @param 单据关联的详情ID $subid  单据关联的详情存在附件信息的时候查询详情附件信息需传入此ID
	 * @param 是否输出页面或者返回数组 $isassign   true 表示输出页面，false 表示返回福建信息数组  默认为 true
	 * @param 返回的数组是三维数组还是二维数组 $isfourorthree 默认为返回二维数组， false表示返回三维数组
	 * @return unknown
	 * @author liminggang
	 * @date 2014-7-30 下午2:06:32
	 * @throws
	 */
	public function getAttachedRecordList($modelname,$tableid,$online=true,$archived=true,$tablename='',$subid=0,$isassign=true,$isfourorthree=true){
		$armodel = D('MisAttachedRecord');
		$armap['tableid'] = $tableid;
		if($subid) $armap['subid'] = $subid;		
		$armap['status'] = 1;
		//添加一个查询的对象模型
		if ($tablename == '') {
			$armap['tablename'] = $modelname;
		} else {
			$armap['tablename'] = $tablename;
		}
		
		$attarry = $armodel->where($armap)->select();
		$filesArr = array('pdf','doc','docx','xls','xlsx','ppt','pptx','txt','jpg','jpeg','gif','png');
		foreach ($attarry as $key => $val) {
			$pathinfo = pathinfo($val['attached']);
			//获取除后缀的文件名称
			//王昭侠:2015年8月21日 $upname = missubstr($val['upname'],18,true).".".$pathinfo['extension'];
			$upname = $val['upname'];
			if (in_array(strtolower($pathinfo['extension']), $filesArr)) {
				//在线查看，必须是指定的文件类型，才能在线查看。
				$attarry[$key]['online'] = $online;  //在线查看按钮。
			}
			//URL传参。一定要将base64加密后生成的  ‘=’ 号替换掉
			$attarry[$key]['name'] = str_replace("=", '', base64_encode($val['attached']));
			//文件显示名称
			$attarry[$key]['filename'] = $upname;
			//文件下载名称
			$attarry[$key]['lookname'] = $val['upname'];
			//任何文件都可以归档
			$attarry[$key]['archived'] = $archived; //归档按钮
		}
		$uploadarry=array();
		foreach ($attarry as $akey=>$aval){
			$uploadarry[$aval['fieldname']][]=$aval;
		}
		if($isassign){
			$this->assign('attarry',$attarry);
			$this->assign('attcount',count($attarry));
			$this->assign('uploadarry',$uploadarry);
		} else {
			if($isfourorthree){
				return $attarry;
			}else{
				return $uploadarry;
			}
		}
	}
}
/**
 * 重写父类的用户自定义异常抽象类
 * @author liminggang
 * @date 2015年2月11日 下午2:25:31
 * @version V1.0
 */
class AppException extends Exception {
	public function __construct($message = null, $code = 0){
		if (!$message) {
			if(C("APP_EXCEPTION") == 1){
				$message = get_class($this) . " '{$this->message}' in {$this->file}({$this->line})";
			}else{
				$message = $this->message;
			}
		}
		if(!$code){
			$code = $this->code;
		}
		parent::__construct($message, $code);
	}

	public function __toString(){
		return get_class($this) . " '{$this->message}' in {$this->file}({$this->line})\n"
		. "{$this->getTraceAsString()}";
	}
}
/**
 * 服务器内部错误异常类
 * 
 * @author liminggang
 * @date 2015年2月11日 下午2:25:31
 * @version V1.0
 *         
 */
class AppServerException extends AppException {
	protected $message = "服务器内部错误";
	protected $code = "1001";
	public function __construct($message = null, $code = 0) {
		/**
		 * 服务器异常类 适用于所有服务器异常，信息不同时，则传入异常信息
		 * 
		 * @param 异常信息 $message
		 *        	为空时，采用默认的异常信息 【服务器内部错误】
		 * @param 异常编码 $code
		 *        	为空时，采用默认的编码 【1001】
		 * @author liminggang
		 *         @date 2015年2月11日 下午2:25:31
		 * @version V1.0
		 */
		parent::__construct ( $message, $code );
	}
}
/**
 * 针对所有空参数异常类
 * 
 * @author liminggang
 *         @date 2015年2月11日 下午2:25:31
 * @version V1.0
 *         
 */
class AppNullParamException extends AppException {
	// 异常信息
	protected $message = "缺少必要参数";
	// 异常编码
	protected $code = "1002";
	/**
	 * 空参数异常类 适用于所有空参数异常，信息不同时，则传入异常信息
	 * 
	 * @param 异常信息 $message
	 *        	不传入异常信息时，采用默认的异常信息 【缺少必要参数】
	 * @param 异常编码 $code
	 *        	不传入异常编码时，采用默认的编码 1002
	 * @author liminggang
	 *         @date 2015年2月11日 下午2:25:31
	 * @version V1.0
	 *         
	 */
	public function __construct($message = null, $code = 0) {
		parent::__construct ( $message, $code );
	}
}
/**
 * 非法访问异常类
 * 
 * @author liminggang
 *         @date 2015年2月11日 下午2:25:31
 * @version V1.0
 *         
 */
class AppFeifaFangWenException extends AppException {
	// 异常信息
	protected $message = "非法访问";
	// 异常编码
	protected $code = "1003";
	/**
	 * 非法访问异常类 适用于所有非法访问的异常，当异常信息不同时，可以直接传入异常信息
	 * 
	 * @param 异常信息 $message
	 *        	不传入异常信息时，采用默认的异常信息 【缺少必要参数】
	 * @param 异常编码 $code
	 *        	不传入异常编码时，采用默认的编码 1003
	 * @author liminggang
	 *         @date 2015年2月11日 下午2:25:31
	 * @version V1.0
	 *         
	 */
	public function __construct($message = null, $code = 0) {
		parent::__construct ( $message, $code );
	}
}
/**
 * 用户异常类
 * 
 * @author liminggang
 *         @date 2015年2月11日 下午2:25:31
 * @version V1.0
 *         
 */
class AppUserException extends AppException {
	// 异常信息
	protected $message = "用户登陆失败";
	// 异常编码
	protected $code = "1004";
	/**
	 * 用户异常类， 适用于所有用户异常，当异常信息不同时，可以直接传入异常信息
	 * 
	 * @param 异常信息 $message
	 *        	不传入异常信息时，采用默认的异常信息 【用户登陆失败】
	 * @param 异常编码 $code
	 *        	不传入异常编码时，采用默认的编码 1004
	 * @author liminggang
	 *         @date 2015年2月11日 下午2:25:31
	 * @version V1.0
	 *         
	 */
	public function __construct($message = null, $code = 0) {
		parent::__construct ( $message, $code );
	}
}
/**
 * 短信异常类
 * 
 * @author liminggang
 *         @date 2015年2月11日 下午2:25:31
 * @version V1.0
 *         
 */
class AppEmailException extends AppException {
	// 异常信息
	protected $message = "短信异常";
	// 异常编码
	protected $code = "1005";
	/**
	 * 用户异常类， 适用于所有用户异常，当异常信息不同时，可以直接传入异常信息
	 * 
	 * @param 异常信息 $message
	 *        	不传入异常信息时，采用默认的异常信息 【缺少必要参数】
	 * @param 异常编码 $code
	 *        	不传入异常编码时，采用默认的编码 1005
	 * @author liminggang
	 * @date 2015年2月11日 下午2:25:31
	 * @version V1.0
	 */
	public function __construct($message = null, $code = 0) {
		parent::__construct ( $message, $code );
	}
}