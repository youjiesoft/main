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
	protected $serviceRoot='http://120.25.218.139';
	private $transaction_model=NULL;//事务模型
	
	function __construct(){
		parent::__construct();
		$this->serviceRoot='http://120.25.218.139';
	}
	/*
	 * 服务器地址返回接口；这个接口地址不能改变
	 * paramate $_REQUEST['type']  Formal正式环境  PreRelease 预发布环境  Test测试环境
	 * return 返回服务器地址
	*/
	public function serverLink($returnType='json'){
		$serverType=$_REQUEST['type']?$_REQUEST['type']:'Formal';
		$server=$this->serverLinkBase($serverType);
        if($server){
        	$data=$server;
        	$code='1000';
        	$msg='1000:返回成功';
        	return $this->getReturnData($data,$returnType,$code,$msg);        	
		}else{
			$server['allPath']=$this->serviceRoot.'/Admin/index.php?s=/MobileChuangke/';
			$server['prefixPath']=$this->serviceRoot.'/Admin/';
			$server['postfixPath']='index.php?s=/MobileChuangke/';
			$server['phone']='02367787216';
			$data=$server;
			$code='1003';
			$msg='1003:非法操作';
			return $this->getReturnData($data,$returnType,$code,$msg);
		}
	}	
	/*
	 * 服务器地址
	*/	
	private function serverLinkBase($serverType){
		switch($serverType){
			case 'Formal':
				$server['allPath']=$this->serviceRoot.'/Admin/index.php?s=/MobileChuangke/';
				$server['prefixPath']=$this->serviceRoot.'/Admin/';
				$server['postfixPath']='index.php?s=/MobileChuangke/';
				$server['phone']='02367787216';
			break;
			case 'PreRelease':
				$server['allPath']=$this->serviceRoot.'/Admin/index.php?s=/MobileChuangke/';
				$server['prefixPath']=$this->serviceRoot.'/Admin/';
				$server['postfixPath']='index.php?s=/MobileChuangke/';
				$server['phone']='02367787216';
			break;
			case 'Test':
				$server['allPath']=$this->serviceRoot.'/Admin/index.php?s=/MobileChuangke/';
				$server['prefixPath']=$this->serviceRoot.'/Admin/';
				$server['postfixPath']='index.php?s=/MobileChuangke/';
				$server['phone']='02367787216';
			break;						
		}
		return $server;
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
	protected  function illegalParameter(){
		return $this->getReturnData(array(),'json',0,'200:非法参数');
	}
	
	protected function  setToken($userInfo){
		$token= serialize($userInfo);
		$token=str_replace('=','',base64_encode($token));//base64加密
		$token=strrev($token);//反转字符串
		return $token;
	}
	
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
		logs(D($modelname)->getLastSql());
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
	protected function getFormList($modelname,$fileds='id,orderno',$seachFields,$condition,$attachedModel){
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
		//var_dump($model->getLastSql());exit;
		//dump($count);
		
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
	
		return $this->getReturnData($data,'json','1000',"获取数据成功");
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
	protected function getFormListAll($modelname,$fileds='id,orderno',$seachFields,$condition){
	
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
		return $this->getReturnData($data,'json','1000',"获取数据成功");
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
	protected function getFormInfo($modelname,$arrayType=2){
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
		return $this->getReturnData($data,'json','1000',"获取数据成功");
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
	
	public function login($returnType='json'){
		if(!empty($_REQUEST['account']) && !empty($_REQUEST['pwd'])){
			$userinfo=$this->checkLogin('arr');
			//登陆成功
			$data=array();
			if($userinfo){
				$data['userid']=$userinfo['id'];
				$data['account']=$userinfo['account'];
				$data['password']=$userinfo['pwd'];
				$data['verifcode']=$userinfo['verifcode'];
				$data['logintime']=time();
				//获取token
				$token=$this->setToken($data);
				$data['token']=$token;
				$code='1000';
				$msg='1000:登录成功';
			}else{
				$data=array();
				$code='1002';
				$msg='1002:密码错误';
			}
		}else{
			$data=array();
			$code='1003';
			$msg='1003:非法访问';
		}
		return $this->getReturnData($data,$returnType,$code,$msg);
	}
	
	
	public function checkLogin($returnType='json'){
		//核验用户名与密码
		$map=array();
		$map['account']=$_REQUEST['account'];
		$map['pwd']=$_REQUEST['pwd'];
		$result=D('MisAutoRor')->where($map)->find();
		//$result=$this->getMisSystemDataview('fpappsjView',$map);
		//echo D('MisAutoRor')->getLastSql();
		if($result){
			//检查情况下直接返回
			if($returnType=='check'){
				return;
			}else{
				$data=$result;
				$code='1000';
				$msg='用户验证成功';
			}
		}else{
			$data=array();
			$code='1003';
			$msg='1003:用户验证不符';
		}
		return $this->getReturnData($data,$returnType,$code,$msg);
	}
	
	/*
	 * register
	* 用户注册
	*/
	public function register($returnType='json'){
		if(!empty($_REQUEST['account']) && !empty($_REQUEST['verifcode']) && !empty($_REQUEST['pwd'])){
			//核验用户名与密码
			$map=array();
			$map['account']=$_REQUEST['account'];
			//$map['verifcode']=$_REQUEST['verifcode'];
			$userInfo=D('MisAutoRor')->where($map)->find();
			//echo D('MisAutoRor')->getLastSql();
			if($userInfo){
				if($userInfo['verifcode']==$_REQUEST['verifcode']){
					//修改类型
					$sign='save';
					//如果存在，则将状态改为1
					$map['id']=$userInfo['id'];
					$data['status']=1;
					$data['pwd']=$_REQUEST['pwd'];
					$result=$this->CURD('MisAutoRor','save',$data,$map);
				}else{
					$data=array();
					$code='1001';
					$msg='1001:验证码错误';
					return $this->getReturnData($data,$returnType,$code,$msg);
				}
	
			}else{
				//新增类型
				$sign='add';
				$data['account']=$_REQUEST['account'];
				$data['phone']=$_REQUEST['account'];
				$data['pwd']=$_REQUEST['pwd'];
				$result=$this->CURD('MisAutoRor','add',$data);
			}
			if($result===false){
				$data=array();
				$code='1001';
				$msg='1001:用户注册失败';
				 
			}else{
				//token需要id,account,password三个数据源来做数据处理
				switch($sign){
					case 'add':
						//只需要一个userid重新返回
						$returnData['userid']=$result;
						$returnData['account']=$_REQUEST['account'];
						$returnData['password']=$_REQUEST['pwd'];
						$returnData['logintime']=time();
						break;
					case 'save':
						//这里需要根据userinfo的数据获取id,account,password
						$returnData['userid']=$userInfo['id'];
						$returnData['account']=$userInfo['account'];
						$returnData['password']=$_REQUEST['pwd'];
						$returnData['logintime']=time();
						break;
				}
				//获取token
				$token=$this->setToken($returnData);
				$data['token']=$token;
				$code='1000';
				$msg='1000:登录成功';
			}
		}else{
			$data=array();
			$code='1003';
			$msg='1003:非法访问';
		}
		return $this->getReturnData($data,$returnType,$code,$msg);
	}
	
	private function  checkPhoneExist($phone,$type){
		$map=array();
		$map['phone']=$phone;
		$result=D('MisAutoRor')->where($map)->find();
		
		if($result){
			//新用户注册
			switch($type){
				case 1://新用户注册
					//状态已经为1时
					if($result['status']){
						return $this->getReturnData($data,'json','1010',"用户已经存在");
						exit;
					}else{
						//状态为0时
						return $result['id'];
					}
					break;
				case 2://找回密码
					return $result['id'];
					break;
			}
		}else{
			return false;
		}
	}
	/**
	 getVerificationCode 获取验证码
	 */
	public function getVerificationCode($returnType='json'){
		if(!empty($_REQUEST['phone']) && is_numeric($_REQUEST['phone'])){
			$verifcode=rand(1000,9999);
			//echo $verifcode;
			$phone=$_REQUEST['phone'];//手机号码
			$type=$_REQUEST['type'];//类型
			$content=$verifcode."(您获取的手机验证码),有效期为24小时，感谢您注册创客荟。如非本人操作，请忽略此短信。";
			$userid=$this->checkPhoneExist($phone,$type);
			if($userid){
				//存在手机号，回写验证码到数据库
				$map=array();
				$map['id']=$userid;
				$data['verifcode']=$verifcode;
				$result=$this->CURD('MisAutoRor','save',$data,$map);
			}else{
				//不存在手机号，插入到数据库
				$data['account']=$phone;
				$data['phone']=$phone;
				$data['verifcode']=$verifcode;
				$data['createtime']=time();
				$data['status']=0;
				$result=$this->CURD('MisAutoRor','add',$data);
			}
			//echo 123;
			if($result){
				//查询短信发送一个手机号短信间隔最短60s 一个小时不超过3条 ，一天不超过5条
				$phoneHisteryMpdel=D('MisSystemPhoneHistery');
				//首先查询是否发送了5条数据
				$dstarttime=strtotime(date('Y-m-d',time()));
				$dendtime=strtotime(date('Y-m-d',strtotime('+1 day')));
				$dphMap['createtime']=array(array('egt',$dstarttime),array('lt',$dendtime)) ;
				$dphMap['phone']=$phone;
				$dphList=$phoneHisteryMpdel->where($dphMap)->count();
				$phListInfo=$phoneHisteryMpdel->where($dphMap)->order('id desc ')->find();
				//查询一个小时内发送的数据
				$hendtime=$phListInfo['createtime'];
				$hstarttime=strtotime(date('Y-m-d H:i:s',strtotime('-1 hour',$hendtime)));
				$hphMap['createtime']=array(array('egt',$hstarttime),array('lt',$hendtime)) ;
				$hphMap['phone']=$phone;
				$hphList=$phoneHisteryMpdel->where($hphMap)->count();
				$stime=time()-$hendtime;
				if($dphList<5 &&  $hphList<3 && $stime>=60){
					//调用短信接口
					import('@.ORG.SmsHttp');
					$smsHttp= new SmsHttp;
					//短信操作
					$smsNotic=$smsHttp->getMessage($phone,$content,$httpType=1);
					//回写状态正常时
					$data=$result;
					$code='1000';
					$msg=$smsNotic."验证码为：".$verifcode;
					//添加号码发送短信记录
					$phdate['phone']=$phone;
					$phdate['createtime']=time();
					$phresult=$this->CURD('MisSystemPhoneHistery','add',$phdate);
				}else{
					$data=array();
					$code='1006';
					if($dphList>=5){
						$msg='1006:当天验证码获取超过5条';
					}elseif($hphList>=3){
						$msg='1006:一个小时内验证码获取超过3条';
					}elseif($stime<60){
						$msg='1006:上次获取验证码时间不足60s';
					}
						
				}
			}else{
				$data=array();
				$code='1006';
				$msg='1006:获取验证码失败';
			}
		}else{
			$data=array();
			$code='1007';
			$msg='1007:非法访问：未有手机号';
		}
		return $this->getReturnData($data,$returnType,$code,$msg);
	}
	
	public function checkVerificationCode($returnType='json'){
		if(empty($_REQUEST['phone']) && empty($_REQUEST['verifcode'])){
			$data=array();
			$code='1003';
			$msg='1003:非法访问';
			return $this->getReturnData($data,$returnType,$code,$msg);
		}
		//核验用户名与密码
		$map=array();
		$map['phone']=$_REQUEST['phone'];
		$map['verifcode']=$_REQUEST['verifcode'];
		$result=D('MisAutoRor')->where($map)->find();
		if($result){
			$data=$result;
			$code='1000';
			$msg='验证成功';
		}else{
			$data=array();
			$code='1005';
			$msg='1005:验证不符';
		}
		return $this->getReturnData($data,$returnType,$code,$msg);
	}
	public function setPassword($returnType='json'){
		if(empty($_REQUEST['account'])  && empty($_REQUEST['pwd'])){
			$data=array();
			$code='1003';
			$msg='1003:非法访问';
			return $this->getReturnData($data,$returnType,$code,$msg);
		}
		//核验用户名与密码
		$map=array();
		$map['account']=$_REQUEST['account'];
		$data['pwd']=$_REQUEST['pwd'];
		$result=$this->CURD('MisAutoRor','save',$data,$map);
		if($result){
			$data=$result;
			$code='1000';
			$msg='修改成功';
		}else{
			$data=array();
			$code='1006';
			$msg='1006:修改失败';
		}
		return $this->getReturnData($data,$returnType,$code,$msg);
	}
	
	public function resetPassword($returnType='json'){
		if(empty($_REQUEST['account']) && empty($_REQUEST['verifcode']) && empty($_REQUEST['pwd'])){
			$data=array();
			$code='1003';
			$msg='1003:非法访问';
			return $this->getReturnData($data,$returnType,$code,$msg);
		}
		//核验用户名与密码
		$map=array();
		$map['account']=$_REQUEST['account'];
		//$map['userid']=$_REQUEST['userid'];
		//忘记密码
		$map['verifcode']=$_REQUEST['verifcode'];
		$data['pwd']=$_REQUEST['pwd'];
		$data['status']=1;
		$result=$this->CURD('MisAutoRor','save',$data,$map);
		if($result){
			$data=$result;
			$code='1000';
			$msg='修改成功';
		}else{
			$data=array();
			$code='1006';
			$msg='1006:修改失败';
		}
		return $this->getReturnData($data,$returnType,$code,$msg);
	}
	
	
}