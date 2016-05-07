<?php
use DataTables\Editor\Join;
//Version 1.0
// +----------------------------------------------------------------------
// | ThinkPHP
// +----------------------------------------------------------------------
// | Copyright (c) 2007 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

//公共函数

function tags($value = ''){
	return F('common', $value, './Lang/zh-cn/');
}

/*
* 自定义函数 操作列显示状态及按钮
*paramate $creatid创建人 $auditstatus 审核状态 $param传递参数 $modelname模型名称 $target标签模式$title标题$width宽度$height高度$mask
*/
function getOperateStatus($creatid,$auditstatus,$param="",$modelname="",$rel="",$target="navTab",$title="",$width="",$height="",$mask=true){
	$qx=false;
	$modelname= $modelname ? $modelname:MODULE_NAME;
	if( !isset($_SESSION['a']) ){
		if( $_SESSION[strtolower($modelname.'_update')]==1 ){//判断全部修改权限
			$qx = true;
		}else if( $_SESSION[strtolower($modelname.'_update')]==2 ){////判断部门及子部门权限
				
			$qx = in_array($creatid,$_SESSION['user_dep_all_child']) ? true:false;
		}else if($_SESSION[strtolower($modelname.'_update')]==3){//判断部门权限
			$qx = in_array($creatid,$_SESSION['user_dep_all_self']) ? true:false;
		}else if($_SESSION[strtolower($modelname.'_update')]==4){//判断个人权限
			$qx = ($creatid==$_SESSION[C('USER_AUTH_KEY')]) ? true:false;

		}
	}else{
		$qx=true;
	}
	if($auditstatus=="#auditState#"){
		$title.= $qx? "_编辑":"_查看";
		$class= $qx? "tml-icon tml-icon-pencil":"tml-icon-view";
		$text= getOperateUrl($param,$modelname,"edit",$class,$rel,$target,$title,$width,$height,$mask);
	} else{
		$defaultSelect = Cookie::get($_SESSION[C('USER_AUTH_KEY')].$modelname.'defaultSelect');
		$default = substr($defaultSelect, 0,1);
		if($default == 1){
			if( $auditstatus<=0 ){
				$title.=$qx? "_编辑":"_查看";
				$class= $qx? "tml-icon tml-icon-pencil":"tml-icon-view";
			} else {
				$title.="_查看";
				$class='tml-icon-view';
			}
			$text= getOperateUrl($param,$modelname,"edit",$class,$rel,$target,$title,$width,$height,$mask);
			if( $auditstatus==1 ){
				if($qx) {
					$text .= '&nbsp;<a class="icon icon-undo" title="撤回" href="__APP__/'.$modelname.'/lookupGetBackprocess/'.$param.'/navTabId/__MODULE__" target="ajaxTodo"></a>';
				}
			}
		} else if($default == 2){
			$title.="_审核";
			$class='tml-icon-signature';
			$text= getOperateUrl($param,$modelname,"auditEdit",$class,$rel,$target,$title,$width,$height,$mask);
		} else if($default == 3){
			$title.="_查看";
			$class='tml-icon-view';
			$text= getOperateUrl($param,$modelname,"edit",$class,$rel,$target,$title,$width,$height,$mask);
		}
	}
	return $text;
}

/*
 * 自定义函数 时间戳转标准Y-m-d函数
 * $value : 查找关键值
 * $key	: 目标表中的字段名
 * $fieldName : 被检索出来的目标表中的字段名
 * $table : 被检索的目标表名
 */
function transTime($time,$format = 'Y-m-d'){
	if (empty ( $time ) || $time == '' || $time == 0) {
		return '';
	}
	return date ($format, $time );
}

function untransTime ($time,$format = 'Y-m-d'){
	if (empty ( $time ) || $time == '' || $time == 0) {
		return '';
	}
	return strtotime($time);
}
/*
 *时间段
 */
function transTimeQuantum($begintime, $endime, $format = 'Y-m-d'){
	if (empty ( $begintime ) || $begintime == '' || $begintime == 0) {
		return '';
	}
	if (empty ( $endime ) || $endime == '' || $endime == 0) {
		return '';
	}
	return transTime ( $begintime, $format ) .' ~ ' . transTime ( $endime, $format );
}

/*
 * 极少使用
 * 转换函数，针对于无数据的时候，返回单引号
 */
function transition($fieldVal){
	if ($fieldVal) {
		return $fieldVal;
	}
	return '';
}

/*
 *转换逗号分隔的数字
 */
function toStringReplace($str,$format=','){
	if (empty ( $str ) || $str == '') {
		return '';
	}
	return str_replace($format,'',$str);
}
/**************************************************************/
//下面缓存没使用，可参考
// 缓存文件
function cmssavecache($name = '', $fields = '') {
	$Model = D ( $name );
	$list = $Model->select ();
	$data = array ();
	foreach ( $list as $key => $val ) {
		if (empty ( $fields )) {
			$data [$val [$Model->getPk ()]] = $val;
		} else {
			// 获取需要的字段
			if (is_string ( $fields )) {
				$fields = explode ( ',', $fields );
			}
			if (count ( $fields ) == 1) {
				$data [$val [$Model->getPk ()]] = $val [$fields [0]];
			} else {
				foreach ( $fields as $field ) {
					$data [$val [$Model->getPk ()]] [] = $val [$field];
				}
			}
		}
	}
	$savefile = cmsgetcache ( $name );
	// 所有参数统一为大写
	$content = "<?php\nreturn " . var_export ( array_change_key_case ( $data, CASE_UPPER ), true ) . ";\n?>";
	file_put_contents ( $savefile, $content );
}

function cmsgetcache($name = '') {
	return DATA_PATH . '~' . strtolower ( $name ) . '.php';
}
/**************************************************************/
//zhanghuihua@msn.com
function showStatus($status, $id, $callback="",$m="",$navTab=__MODULE__) {
	if( $m ){
		$url =strtolower($m);
		$url_a = __APP__.'/'.$m;
	}else{
		$url =strtolower(end(explode('/', __URL__)));
		$url_a = __URL__;
	}
	$info = "";
	switch ($status) {
		case 0 :
			if($_SESSION[$url.'_resume'] || $_SESSION['a'])
			$info = '<a href="'.$url_a.'/resume/id/' . $id . '/navTabId/'.$navTab.'" target="ajaxTodo" callback="'.$callback.'">恢复</a>';
			break;
		case 2 :
			if($_SESSION[$url.'_pass'] || $_SESSION['a'])
			$info = '<a href="'.$url_a.'/pass/id/' . $id . '/navTabId/'.$navTab.'" target="ajaxTodo" callback="'.$callback.'">批准</a>';
			break;
		case 1 :
			if($_SESSION[$url.'_forbid'] || $_SESSION['a'])
			$info = '<a href="'.$url_a.'/forbid/id/' . $id . '/navTabId/'.$navTab.'" target="ajaxTodo" callback="'.$callback.'">禁用</a>';
			break;
		case - 1 :
			if($_SESSION[$url.'_recycle'] || $_SESSION['a'])
			$info = '<a href="'.$url_a.'/recycle/id/' . $id . '/navTabId/'.$navTab.'" target="ajaxTodo" callback="'.$callback.'">还原</a>';
			break;
	}
	return $info;
}

function getStatus($status,$id,$callback="",$m="",$navTab=__MODULE__,$imageShow = true) {
	if(!isset($_SESSION['a'])) return '';
	if( $m ){
		$url =strtolower($m);
		$url_a = __APP__.'/'.$m;
	}else{
		$url =strtolower(end(explode('/', __URL__)));
		$url_a = __URL__;
	}
	switch ($status) {
		case 0 :
			$showText = '禁用';
			//$showImg = '<IMG SRC="' . WEB_PUBLIC_PATH . '/Images/locked.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="禁用">';
			if($_SESSION['a']!=1 || !$id){
				$showImg = '<a href="javascript:;" class="stateLocked" title="禁用">禁用</a>';
			}
			else{
				$showImg = '<a href="'.$url_a.'/resume/id/' . $id . '/navTabId/'.$navTab.'" target="ajaxTodo" callback="'.$callback.'"><span class="stateLocked" title="禁用">禁用</span></a>';
			}
			break;
		case 2 :
			$showText = '待审';
			//$showImg = '<IMG SRC="' . WEB_PUBLIC_PATH . '/Images/prected.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="待审">';
			if($_SESSION['a']!=1 || !$id){
				$showImg = '<a href="javascript:;" class="statePrected" title="待审">待审</a>';
			}
			else{
				$showImg = '<a  href="'.$url_a.'/pass/id/' . $id . '/navTabId/'.$navTab.'" target="ajaxTodo" callback="'.$callback.'"><span class="statePrected" title="待审">待审</span></a>';
			}
			break;
		case - 1 :
			$showText = '删除';
			//$showImg = '<IMG SRC="' . WEB_PUBLIC_PATH . '/Images/del.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="删除">';
			if($_SESSION['a']!=1 || !$id){
				$showImg = '<a href="javascript:;" class="stateDel" title="删除">删除</a>';
			}
			else{
				$showImg = '<a href="'.$url_a.'/recycle/id/' . $id . '/navTabId/'.$navTab.'" target="ajaxTodo" callback="'.$callback.'"><span class="stateDel" title="删除">删除</span></a>';
			}
			break;
		case -2 :
			$showText = '作废';
			if($_SESSION['a']!=1 || !$id){
				$showImg = '<a href="javascript:;" class="stateDel" title="作废">作废</a>';
			}
			else{
				$showImg = '<a href="'.$url_a.'/recycle/id/' . $id . '/navTabId/'.$navTab.'" target="ajaxTodo" callback="'.$callback.'"><span class="stateDel" title="作废">作废</span></a>';
			}
			break;
		case 1 :
		default :
			$showText = '正常';
			//$showImg = '<IMG SRC="' . WEB_PUBLIC_PATH . '/Images/ok.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="正常">';
			if($_SESSION['a']!=1 || !$id){
				$showImg = '<a href="javascript:;" class="stateOk" title="正常">正常</a>';
			}
			else{
				$showImg = '<a href="'.$url_a.'/forbid/id/' . $id . '/navTabId/'.$navTab.'" target="ajaxTodo" callback="'.$callback.'"><span class="stateOk" title="正常">正常</span></a>';
			}
			break;
	}
	return ($imageShow === true) ?  $showImg  : $showText;
}

/**
 * 极少使用MisHrPersonnelAppraisalInfo
 * 得到评估状态
 * @param unknown_type $linkcontent
 * @return string|unknown
 */
function getLinkcontent($linkcontent) {
	if ($linkcontent) {
		return "已评估";
	} else {
		return "未评估";
	}

}
/**
 * 极少使用MisHrPersonnelAddressBook
 * @Title: getLeavestatus，
 * @Description: todo(判断职位状态方法)
 * @param int $link
 * @return string
 * @author liminggang
 * @date 2013-3-14 上午9:53:53
 * @throws
 */
function getLeavestatus($link) {
	if ($link) {
		return "在职";
	} else {
		return "离职";
	}

}
function getDefaultStyle($style) {
	if (empty ( $style )) {
		return 'blue';
	} else {
		return $style;
	}

}
function IP($ip = '', $file = 'UTFWry.dat') {
	$_ip = array ();
	if (isset ( $_ip [$ip] )) {
		return $_ip [$ip];
	} else {
		import ( "ORG.Net.IpLocation" );
		$iplocation = new IpLocation ( $file );
		$location = $iplocation->getlocation ( $ip );
		$_ip [$ip] = $location ['country'] . $location ['area'];
	}
	return $_ip [$ip];
}

function getNodeName($id) {
	if (Session::is_set ( 'nodeNameList' )) {
		$name = Session::get ( 'nodeNameList' );
		return $name [$id];
	}
	$Group = D ( "Node" );
	$list = $Group->getField ( 'id,name' );
	$name = $list [$id];
	Session::set ( 'nodeNameList', $list );
	return $name;
}


function getNodeGroupName($id) {
	if (empty ( $id )) {
		return '未分组';
	}
	if (isset ( $_SESSION ['nodeGroupList'] )) {
		return $_SESSION ['nodeGroupList'] [$id];
	}
	$Group = D ( "Group" );
	$list = $Group->getField ( 'id,title' );
	$_SESSION ['nodeGroupList'] = $list;
	$name = $list [$id];
	return $name;
}

/**
 * @Title: showRelStatus
 * @Description: todo(状态栏刷新，指定rel刷新位置)
 * @param 状态 $status
 * @param 序号 $id
 * @param 模型 $m
 * @param 刷新ID $rel
 * @return string
 * @author 杨东
 * @date 2013-6-1 下午1:47:14
 * @throws
 *
 */
function showRelStatus($status, $id, $rel="", $m="") {
	if( $m ){
		$url =strtolower($m);
		$url_a = __APP__.'/'.$m;
	}else{
		$url =strtolower(end(explode('/', __URL__)));
		$url_a = __URL__;
	}
	switch ($status) {
		case 0 :
			if($_SESSION[$url.'_resume'] || $_SESSION['a'])
			$info = '<a href="'.$url_a.'/resume/id/' . $id . '/rel/'.$rel.'" target="ajaxTodo">恢复&nbsp;</a>';
			break;
		case 2 :
			if($_SESSION[$url.'_pass'] || $_SESSION['a'])
			$info = '<a href="'.$url_a.'/pass/id/' . $id . '/rel/'.$rel.'" target="ajaxTodo">批准&nbsp;</a>';
			break;
		case 1 :
			if($_SESSION[$url.'_forbid'] || $_SESSION['a'])
			$info = '<a href="'.$url_a.'/forbid/id/' . $id . '/rel/'.$rel.'" target="ajaxTodo">禁用&nbsp;</a>';
			break;
		case - 1 :
			if($_SESSION[$url.'_recycle'] || $_SESSION['a'])
			$info = '<a href="'.$url_a.'/recycle/id/' . $id . '/rel/'.$rel.'" target="ajaxTodo">还原&nbsp;</a>';
			break;
	}
	return $info;
}

function getGroupName($id) {
	if ($id == 0) {
		return '无上级组';
	}
	if ($list = F ( 'groupName' )) {
		return $list [$id];
	}
	$dao = D ( "Role" );
	$list = $dao->select ( array ('field' => 'id,name' ) );
	foreach ( $list as $vo ) {
		$nameList [$vo ['id']] = $vo ['name'];
	}
	$name = $nameList [$id];
	F ( 'groupName', $nameList );
	return $name;
}

//极少用到，供做参考
function sort_by($array, $keyname = null, $sortby = 'asc') {
	$myarray = $inarray = array ();
	# First store the keyvalues in a seperate array
	foreach ( $array as $i => $befree ) {
		$myarray [$i] = $array [$i] [$keyname];
	}
	# Sort the new array by
	switch ($sortby) {
		case 'asc' :
			# Sort an array and maintain index association...
			asort ( $myarray );
			break;
		case 'desc' :
		case 'arsort' :
			# Sort an array in reverse order and maintain index association
			arsort ( $myarray );
			break;
		case 'natcasesor' :
			# Sort an array using a case insensitive "natural order" algorithm
			natcasesort ( $myarray );
			break;
	}
	# Rebuild the old array
	foreach ( $myarray as $key => $befree ) {
		$inarray [] = $array [$key];
	}
	return $inarray;
}

function pwdHash($password, $type = 'md5') {
	return hash ( $type, $password );
}
/*
 * 自定义函数，振宇
 * 添加注，Eagle
 * $value : 查找关键值
 * $key		: 目标表中的字段名
 * $fieldName	: 被检索出来的目标表中的字段名
 * $table 		: 被检索的目标表名
 * $field1		: 第二个条件，表字段名，
 * $value1 		： 第二个条件的值
 * */
function getFieldBy($value,$key,$fieldName,$table,$field1='',$value1=''){
	// 传入字段与查询字段相等时 不做查询，直接返回传值@nbmxkj 20150608 2240
	if($key == $fieldName){
		return $value;
	}
	if(strpos($value, ',') && $value){
		$map='';
		if(strpos( $value , ',' )){
			$valueStr=explode(',', $value);
			$map = $key." in ('".join("','", $valueStr)."')";
		}else{
			$map = $key."='".$value."'";
		}
		return getFieldData($fieldName, $table, $map , true,100);
	}
	//判断参数是否存在。
	if($value && $key && $fieldName && $table){
		$model = D($table);
		//修改此处，加入一个1=1 的条件。方便特殊权限不控制到。
		$map[$key] = $value;
		if(is_array($value)){
			$map[$key] =array("in",$value);
		}
		if($field1){
			//$var = "and '.$field11.'='.$value1.'";
			$map[$field1] = $value1;
		}
		$map['_string'] = "1=1";
		// 	"'.$key.'='.$value.' and 1=1 '.$var.'"
		$data = $model->where($map)->getField($fieldName);
	} 
	return $data;
}

/**
 * ungetFieldBy
 * getFieldBy的反转函数
 * 
 */
function ungetFieldBy($value,$key,$fieldName,$table,$field1='',$value1=''){
	//判断参数是否存在。
	if($value && $key && $fieldName && $table){
		$model = D($table);
		//修改此处，加入一个1=1 的条件。方便特殊权限不控制到。
		$map[$fieldName] = $value;
		if(is_array($value)){
			$map[$fieldName] =array("in",$value);
		}
		if($value1){
			//$var = "and '.$field11.'='.$value1.'";
			$map[$value1] = $field1;
		}
		$map['_string'] = "1=1";
		// 	"'.$key.'='.$value.' and 1=1 '.$var.'"
		$data = $model->where($map)->getField($key);
	}
	return $data;
}


function getNodeTypeList(){
	$model = M('Node');
	$data = $model->select();
	return $data;
}
function isSelected($baseUnit,$currentUnit){
	$isSelect = '';
	if($baseUnit == $currentUnit){
		$isSelect = 'selected';
	}
	return $isSelect;
}
function getOptionList($tableName,$map =null){
	$model = D($tableName);
	$list = $model->where($map)->select();
	return $list;

}
function isDefault($target,$type=0){
	$word = '';
	if($type==0){
		if($target==1){
			$word = '是';
		}else{
			$word = '否';
		}
	}else{
		if($target==1){
			$word = '否';
		}else{
			$word = '是';
		}
	}
	return $word;
}

/**
 *
 * @copyright HentStudio (c)2008
 * @author  mashihe
 * @package Editplus4PHP
 * @version $Id: template.php $
 */
function deldir($dir)
{
	$dh=opendir($dir);
	while ($file=readdir($dh))
	{
		if($file!="." && $file!="..")
		{
			$fullpath=$dir."/".$file;
			if(!is_dir($fullpath))
			{
			 unlink($fullpath);
			}
			else
			{
			   deldir($fullpath);
			}
		}
	}
	closedir($dh);
	if(rmdir($dir))
	{
		return true;
	}
	else
	{
		return false;
	}
}

/*
 *
 * @use 创建多层目录 多用于上传图片时候做后台处理
 * @auther mashihe
 * @time 2011/9/24
 *
 */
function createFolder($path)
{
	if (!file_exists($path)){
		createFolder(dirname($path));
		mkdir($path, 0777);
	}
}



/*
 * 极少使用，需要改造
 *获取图片路径
 */
function getPic($path){
	$tem = array();
	$opath = str_replace('../Public', '__PUBLIC__', $path);
	if(!file_exists($path)){
		return false;
	}else{
		foreach (new DirectoryIterator($path) as $file) {
			if(!$file->isDot()){
				array_push($tem,$opath.'/'. $file->getFilename());
			}
		}
	}
	return print_r($tem);
	return array_slice($tem, 0,5);
}

/* ---
 * author:zhongyong
 * date:2011-12-30
 * remark:初步设计为数据库备份跟恢复时启用
 * update time 2012-1-7
 *  --- */

//判断文件是否存在
function fileIsEexists($filename, $folder){
	$folderdir = C('BACKUP_PATH');
	$folder = @iconv('UTF-8', 'gb2312', $folder);
	if(file_exists("$folderdir/$folder/$filename")){
		$result = $filename;
	}else{
		$result = "$folderdir/$folder/$filename";
	}
	return $result;
}

//根据文件存在与否显示相关操作
function resumeAction($filename, $status, $folder){
	$folderdir = C('BACKUP_PATH');
	$gbk_folder = @iconv('UTF-8', 'gb2312', $folder);

	if(file_exists("$folderdir/$gbk_folder/$filename") && $status == 1){
		$actions = "<a href='#' onclick=\"showSelect('".$filename."', '".$folder."');\">还原</a> <a href='".__APP__."/Resume/download/filename/$filename/folder/".urlencode($gbk_folder)."'>下载</a>";
	}else{
		$actions = '';
	}
	return $actions;
}

//极少使用MisExpertquestionListAction
//根据用户ID获取用户名

function getUserName($id){
	$user = D('User');
	$username = $user->where("id=$id")->getField('name');
	return $username;
}

/**
 * 极少使用ReportExcelModel
 * 根据审核状态获取审核状态名称
 * @param 审核状态 $auditState
 * @param 单据ID $id
 * @author 杨东
 * date:2012-01-14
 */
function getAuditState($auditState,$id,$ptmptid,$md) {
	$ConfigListModel= D('SystemConfigList');
	$auditStateLsit	= $ConfigListModel->GetValue('auditState');// 审核状态
	foreach ($auditStateLsit as $key => $value) {
		if ($value['id'] == $auditState) {
			if($id){
				$pimodel = M('process_info_form');
				$ptmptname = $pimodel->where('id='.$ptmptid)->getField('name');//流程名称
				if(!$ptmptname){
					$ptmptname = "流程查看";
				}
				if($md){
					return '<a href="__APP__/'.$md.'/seeProcessDetail/id/'.$id.'" target="dialog" height="450" width="580" mask="true" title="'.$ptmptname.'" rel="seeProcessDetail">
						<img src="'.$value['icon'].'" border="0" title="'.$value['name'].'" alt="'.$value['name'].'"></a>';
				}else{
					return '<a href="__URL__/seeProcessDetail/id/'.$id.'" target="dialog" height="450" width="580" mask="true" title="'.$ptmptname.'" rel="seeProcessDetail">
					<img src="'.$value['icon'].'" border="0" title="'.$value['name'].'" alt="'.$value['name'].'"></a>';
				}
			} else {
				return $value['name'];
			}
		}
	}
}


//极少使用 供作参考
function getSendMessagehtml($id="",$md=""){
	$mds = $md ? $md : MODULE_NAME;
	//先根据ID查询出需要发送短信的用户
	$model=D($mds);
	$map['id'] = $id;
	$map['auditState'] = array('exp','>= 1 and auditState <=2 '); //审核状态
	$list = $model->where($map)->field('curNodeUser')->find();
	if($list['curNodeUser']){
		return '<a class="" href="__URL__/lookupMessage/id/'.$id.'" mask="true" rel="__MODULE__email" title="短信发送" target="dialog">
				<span style="color:blue;">短信息</span>
				</a>';
	}else{
		return '停止发送';
	}
}

/*
 * 极少使用，仅供参考
 * 去除回车
 *
 *
 * @author  mashihe
 * date:2012-01-17
 * */
function mynl2br1($str,$str_replace='<br>') {
	$str=rawurlencode($str);//去除回车；
	$str=preg_replace("#(%0D%0A){1,}#is",$str_replace,$str);
	$str=str_replace("%0D", $str_replace, $str);
	$str=rawurldecode($str);
	return $str;
}
/*
 * 极少使用，仅供参考
 * 远程得到数据
 * */
function url_get_contents($url) {
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	//在需要用户检测的网页里需要增加下面两行
	//curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
	//curl_setopt($ch, CURLOPT_USERPWD, US_NAME.":".US_PWD);
	$contents = curl_exec($ch);
	curl_close($ch);
	return $contents;
	// 	$opts = array(
	// 			'http'=>array(
	// 					'method'=>”GET”,
	// 					'timeout'=>5,
	// 			)
	// 	);
	// 	$context = stream_context_create($opts);
	// 	$text=@file_get_contents($url,false,$context);
	// 	if ($text<>'') return $text;
	// 	$url_parsed = parse_url($url);
	// 	$host = @$url_parsed["host"];
	// 	$port = @$url_parsed["port"];if (empty($port)) $port = 80;
	// 	$path = @$url_parsed["path"];if (empty($path)) $path="/";
	// 	if (@$url_parsed["query"] != "") $path .= "?".$url_parsed["query"];
	// 	if (empty($host)) return "";

	// 	$out = "GET $path HTTP/1.1\r\nHost: $host\r\nUser-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)\r\n\r\nConnection: Close\r\n\r\n";
	// 	$fp = @fsockopen($host, $port, $errno, $errstr, 30);if (!$fp) return "";
	// 	fwrite($fp, $out);$text = "";
	// 	while (!feof($fp)) $text .= fgets($fp, 128);
	// 	fclose($fp);
	// 	return $text;
}

/*
 * 极少使用，供参考
 * 抓数据用MisNoticeModel
 * 得到官方动态
 * */
function get_tmlnews($file){
	$url="http://www.lnlandscape.com/news.asp";
	$s=url_get_contents($url);
	$s = mb_convert_encoding( $s, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5' );
	@file_put_contents($file,$s);
	if ($s=='') return '';
	else{
		$s=get_tag_data($s,'<table width="665"  border="0" align="center" cellpadding="0" cellspacing="0">','</table>');
	}
	@file_put_contents($file,$s);
	return $s;
}
//* 极少使用，供参考
//抓数据用MisNoticeModel
//获取指定标记中的内容
function get_tag_data($str, $start, $end){
	if ( $start == '' || $end == '' ){
		return;
	}
	$str = explode($start, $str);
	$str = explode($end, $str[1]);
	$str = str_replace('<tr align="center" valign="middle">', '', $str);
	$str = str_replace('<td width="7%" height="30" class="rr1"><img src="images/dot.gif" width="9" height="9" /> </td>', '', $str);
	$str = str_replace('<td width="93%" align="left" class="rr1">', '', $str);
	$str = str_replace('</td>', '', $str);
	$str = str_replace('</tr>', '', $str);
	return $str[0];
}
//获取基本单位
function getBaseUnit($baseunit=0,$type=0){
	if(!$baseunit){
		$where=" where isbase=1 ";
	}else{
		if($type){
			$where=" where id=".$baseunit;
		}else{
			$where=" where danweidaima='".$baseunit."'";			
		}
	}
	$sql = "SELECT id,danweidaima,danweimingchen  FROM mis_system_unit ".$where;
	$result=M("mis_system_unit")->query($sql);
	return $result;
}
//获取该基本单位可转换单位
function getSubUnit($baseunit){
	if($baseunit){
		$sql = "SELECT  msue.subunitid as subunitid,msue.exchange as exchange
                FROM mis_system_unit  AS msu
                LEFT JOIN mis_system_unit_exchange  AS msue
                ON msu.id=msue.baseunitid
                WHERE msu.isbase=1 AND msu.danweidaima = '".$baseunit."';" ;
		$result=M("mis_system_unit")->query($sql);
		$unitList=array();	
		if($result===false){
			return false;
		}else{
			$baseUnit=getBaseUnit($baseunit);
			$unitList[0]=$baseUnit[0];
			foreach($result as $key => $val){
				if(!$val['subunitid']){continue;}
				//这里带ID回去查
				$temp=getBaseUnit($val['subunitid'],1);
				$unitList[$key+1]=$temp[0];
			}
			return $unitList;
		}
	}else{
		return false;
	}
}
function  getUnitExchangeCache($baseunit,$subunit){
	
}


function unitInfo($unit){
	$msuModel= D("MisSystemUnit");
	//获取主单位格式类型，1整数 2浮点
	//$unitInfo = $msuModel->where("danweidaima='".$unit."'")->getField('danweimingchen');
	$unitInfo = $msuModel->where("danweidaima='".$unit."'")->find();
	if($unitInfo){
		return  $unitInfo;
	}	
}


//基本单位转换单位转换结果
function unitExchange($qty='', $baseunit,$subunit,$type=1){
	//如果$qty不是数字对象；直接返回null
	if(!is_numeric($qty)) return null;
	//判断基本单位和转换单位是否相同，如果相同就不用转换
	$msuModel= D("MisSystemUnit");
	//获取主单位格式类型，1整数 2浮点
	$unittype = $msuModel->where("danweidaima='".$baseunit."'")->getField('danweizhuangtai');
	$name = $msuModel->where("danweidaima='".$subunit."'")->getField('danweimingchen');
	
	if ($baseunit == $subunit) {
		if ($unittype) {
			//最大4位小数
			//$qty=round($qty,2);
			//去掉小数点后无效的零 @liminggang
			$qty = doubleval($qty);
			$qty = preg_replace('/(.¥d+)0+$/', '${1}', $qty.'');
		} else {
			$qty;
		}
		if($type==3){
			//特殊单位编码不拼装
			if($baseunit!='shu'){
				$qty.=$name;
			}
		}
		return $qty;
	} else {
		$msueModel= D("MisSystemUnitExchange");
		$map['status']=1;
		//获取主单位id
		$baseunitid = $msuModel->where("danweidaima='".$baseunit."'")->getField('id');	
		//获取转换单位id
		$subunitid = $msuModel->where("danweidaima='".$subunit."'")->getField('id');
		//获取转换参数
		//拼装查询，一次定位
		$map['baseunitid']=$baseunitid;
		$map['subunitid']=$subunitid;
		$exchange = $msueModel->where($map)->getField('exchange');	
		//echo    $msueModel->getLastSql();
		switch($type){
			//存储
			case 1:
				$qty = $exchange*$qty;
							
				break;
			//读取
			case 2:
				$qty = $qty/$exchange;

				break;
			//list页面
			case 3:
				$qty = $qty/$exchange;
				break;				
		}
		//单位类型为浮点计算
		if ($unittype) {
			//最大4位小数
			//$qty = round($qty,2);
			//去掉小数点后无效的零 @liminggang
			$qty = doubleval($qty);
			$qty = preg_replace('/(.¥d+)0+$/', '${1}', $qty.'');
		}
		if($type==2){
			$qty=(string)$qty;
		}
		if($type==3){
			//特殊单位编码不拼装
			if($baseunit!='shu'){
				$qty.=$name;
			}
		}
		return $qty;
	}	
}


/**
 +----------------------------------------------------------
 * 查找带回转换单位后的 格式化数量
 +----------------------------------------------------------
 * @author wangcheng
 * @date:2012-03-27
 * @param void $subunitid  	转换单位
 * @param void $qty  		转换单位数量
 * @param void $type        0大转小，其他值小转大
 +----------------------------------------------------------
 * @return void $exchangeqty    默认返回$qty
 +----------------------------------------------------------
 */
function getAfterExchangeFormatQty( $subunitid,$qty=0,$type=0,$prodid){
	return getDigits(getAfterExchangeQty($subunitid,$qty,$type),true,$prodid);
}
/**
 * @Title: getMajorYears
 * @Description: todo(计算专业工作年限->人事部分)
 * @param unknown_type $majoryear
 * @param unknown_type $indate
 * @param unknown_type $switch 算月还是算年
 * @return number
 * @method: {$vo['majoryears']|getMajorYears=$vo['indate']}
 * @author liminggang
 * @date 2013-5-23 上午10:36:43
 * @throws
 */
function getMajorYears($majoryear=0,$indate){
	//取输入的时间的摸。判定是否为整年
	$mod = $majoryear%12;
	//判断是否为整数
	if($mod){
		//有多余的月份存在,取出年份
		$yer=($majoryear-$mod)/12;
	}else{
		$yer=$majoryear/12;
	}
	//获取当前时间
	$nowTime=getdate();
	//获得工作年限
	if($indate){
		$indate=getdate($indate);
		$year=$nowTime['year']-$indate['year'];
		$mon=$nowTime['mon']+$mod-$indate['mon'];
		if($mon<0){
			$year=$year-1;
			$mon=$mon+12;
		}
		$countyear=$yer+$year;
		if($countyear){
			$count=$countyear."年".$mon."月";
		}else{
			$count=$mon."个月";
		}
		return $count;
	}
	return "";
}

/**
 +----------------------------------------------------------
 * 返回修改颜色
 +----------------------------------------------------------
 * @date:2012-04-01
 * @param void $time 	时间戳
 +----------------------------------------------------------
 * @return 返回16进制颜色
 +----------------------------------------------------------
 */

function getTimeColor($t){
	$color="000";
	if ( intval($t) ) {
		$t2 = time() - $t;
		if ( $t2 <= 120 ) {//2分钟
			$color= 'fe9d00';
		} else if ($t2 <= 240 && 120 < $t2) {//4分钟
			$color= 'd68500';
		} else if ($t2 <= 360 && 240 < $t2) {//6分钟
			$color= 'aa6900';
		} else if ($t2 <= 480 && 360 < $t2) {//8分钟
			$color= '835100';
		} else if ($t2 <= 600 && 480 < $t2) {//10分钟
			$color= '513200';
		}
	}
	return $color;
}

/**
 +----------------------------------------------------------
 * 返回警告颜色
 +----------------------------------------------------------
 * @author yangd
 * @date:2012-04-16
 * @param void $qty 	使用数量
 * @param void $doqty 	库存可用数量
 +----------------------------------------------------------
 * @return 返回16进制颜色(红色)
 +----------------------------------------------------------
 */
function getWarningColor($qty,$doqty){
	if ($doqty == 0) {
		return '#FF0000';
	} else if ($qty <= $doqty) {
		return 'no';
	} else {
		return '#FFC000';
	}
}


/**
 * @Title: getDigits
 * @Description: todo(获取小数位数 并格式化,普通计算小数位数)
 * @param 数量 $num
 * @param 格式化数据或者取小数位数 $returndigits
 * @param 单位ID $unitid
 * @param 产品ID $prodid
 * @return string|获取小数位数 并格式化/小数位数
 * @author 杨东
 * @date 2013-5-7 下午3:38:29
 * @throws
 */
function getDigits($num=0,$returndigits=true,$prodid,$unitid){
	$ConfigListModel = D('SystemConfigList');
	$digits = $ConfigListModel->GetValue('digits');// 获取小数位数
	if($returndigits){
		$formatDigits = true;
		if($unitid){
			$model = D('MisProductUnitexchange');
			//获取单位类型
			$unittype = $model->table('mis_product_unit as mis_product_unit,mis_product_unitexchange as mis_product_unitexchange')->where('mis_product_unit.id = mis_product_unitexchange.subunitid and mis_product_unitexchange.id='.$units)->getField('mis_product_unit.unittype as unittype');
			if ($unittype == false) {
				$formatDigits = false;
			}
		} else {
			if($prodid){
				$model = D('MisProductUnit');
				$unittype = $model->table('mis_product_unit as mis_product_unit,mis_product_code as mis_product_code')->where('mis_product_unit.id = mis_product_code.baseunitid and mis_product_code.id='.$prodid)->getField('mis_product_unit.unittype as unittype');
				if ($unittype == false) {
					$formatDigits = false;
				}
			}
		}
		if($formatDigits){
			$num = number_format($num,$digits);
		} else {
			$num = number_format($num);
		}
		return $num;
	}else{
		return $digits;
	}
}

/**
 +----------------------------------------------------------
 * 模板函数方法调用
 * +----------------------------------------------------------
 * @author wangcheng
 * @date:2012-06-08
 * @param string  $v1
 * @param array  $fun_nam  处理的方法 		ex：array('0'=>'trim',1'=>'getFieldBy')
 * @param array  $fun_arr 处理的方法对应的参数 	ex：array('0'=>array(param1,param2),1'=>array(param1,param2));
 * @param array  $extention_data 扩展参数 	ex：array('0'=>array(param1,param2),1'=>array(param1,param2));
 * @param bool  $out 	是否导出
 +----------------------------------------------------------
 * @return string  返回经过函数处理有的结果
 +----------------------------------------------------------
 */
function getConfigFunction($v1,$fun_name,$fun_arr,$extention_data,$out=false,$isdomain=0 ){
	$s=$v1;
	$fun=$fun_name;
	$farr=$fun_arr;
	$i=count($fun);
	if($i==0)return $v1;
	$d2=$d2_arr=array();
	foreach($extention_data as $k=>$v){
		$d2_arr[]="#".$k."#";
		$d2_arr_data[]=$v;
		$d2[$k]=$v;
	}
	foreach($fun as $p=>$pv){
		$i--;
		if($out && in_array($pv,array("missubstr"))) continue;
		$fun_name=$pv;
		if(function_exists($fun_name)){
			$fun_arr=$farr[$p];
			$c=count($fun_arr);
			foreach($fun_arr as $k=>$v){
				if($isdomain && substr($v,-9,-1)=="_domain_"){
					$v=str_replace("_domain_",$isdomain,$v);
				}
				if($v=="###"){
					$fun_arr[$k]=$s;
				}else if(in_array($v,$d2_arr)){
					$fun_arr[$k]=$d2[substr(substr($v, 1), 0,-1)];
				}else{
					$fun_arr[$k]=str_replace($d2_arr,$d2_arr_data,$v);
				}
			}
			$s=callfun($c,$fun_name,$fun_arr);
		}else{
			return "error";
		}
	}
	return $s;
}
/**
 +----------------------------------------------------------
 * 模板函数方法调用
 * +----------------------------------------------------------
 * @author wangcheng
 * @date:2012-06-08
 * @param string  $v1
 * @param array  $fun_nam  处理的方法 		ex：'getFieldBy'
 * @param array  $fun_arr 处理的方法对应的参数 	ex：param1
 +----------------------------------------------------------
 * @return string  返回经过函数处理有的结果
 +----------------------------------------------------------
 */

function callfun($c,$fun_name,$fun_arr){
	if($c==1){
		return call_user_func($fun_name,$fun_arr[0]);
	}else if($c==2){
		return call_user_func($fun_name,$fun_arr[0],$fun_arr[1]);
	}else if($c==3){
		return call_user_func($fun_name,$fun_arr[0],$fun_arr[1],$fun_arr[2]);
	}else if($c==4){
		return call_user_func($fun_name,$fun_arr[0],$fun_arr[1],$fun_arr[2],$fun_arr[3]);
	}else if($c==5){
		return call_user_func($fun_name,$fun_arr[0],$fun_arr[1],$fun_arr[2],$fun_arr[3],$fun_arr[4]);
	}else if($c==6){
		return call_user_func($fun_name,$fun_arr[0],$fun_arr[1],$fun_arr[2],$fun_arr[3],$fun_arr[4],$fun_arr[5]);
	}else if($c==7){
		return call_user_func($fun_name,$fun_arr[0],$fun_arr[1],$fun_arr[2],$fun_arr[3],$fun_arr[4],$fun_arr[5],$fun_arr[6]);
	}else if($c==8){
		return call_user_func($fun_name,$fun_arr[0],$fun_arr[1],$fun_arr[2],$fun_arr[3],$fun_arr[4],$fun_arr[5],$fun_arr[6],$fun_arr[7]);
	}else if($c==9){
		return call_user_func($fun_name,$fun_arr[0],$fun_arr[1],$fun_arr[2],$fun_arr[3],$fun_arr[4],$fun_arr[5],$fun_arr[6],$fun_arr[7],$fun_arr[8]);
	}else if($c==10){
		return call_user_func($fun_name,$fun_arr[0],$fun_arr[1],$fun_arr[2],$fun_arr[3],$fun_arr[4],$fun_arr[5],$fun_arr[6],$fun_arr[7],$fun_arr[8],$fun_arr[9]);
	}
}
/**
 * 方法:isdate()
 * 功能:判断日期格式是否正确
 * 参数:$str 日期字符串 $format日期格式
 * 返回:布儿值
 * by:mashihe
 */
function isdate($str,$format="Y-m-d"){
	$strArr = explode("-",$str);
	if(empty($strArr)){
		return false;
	}
	foreach($strArr as $val){
		if(strlen($val)<2){
			$val="0".$val;
		}
		$newArr[]=$val;
	}
	$str =implode("-",$newArr);
	$unixTime=strtotime($str);
	$checkDate= date($format,$unixTime);
	if($checkDate==$str)
	return true;
	else
	return false;
}

/**
 * @Title: getSeparatedValue
 * @Description: todo(获取多个逗号分隔id的对应的名称)
 * @param unknown_type 字段
 * @param unknown_type 对应表字段
 * @param unknown_type 获取名称
 * @param unknown_type 对应表
 * @param unknown_type 附加字段条件
 * @param unknown_type 附加字段条件值
 * @return string
 * @author renling
 * @date 2013-7-31 下午2:00:33
 * @throws
 */
function getSeparatedValue($value,$key,$fieldName,$table,$field1='',$value1=''){
	$model = D($table);
	if($field1){
		$map[$field1] = $value1;
	}
	if(strpos($value,',')){
		$map[$key] = array('in',$value);
		$data1 = $model->where($map)->getField($fieldName,true);
		$data = implode(',', $data1);
	}else {
		$map[$key] = $value;
		$data = $model->where($map)->getField($fieldName);
	}
	return $data;

}
/**
 * @Title: getunitCode
 * @Description: todo(获取产品单位状态)
 * @param 类型 $unittype
 * @return $vo
 * @author 杨东
 * @date 2013-6-3 下午5:35:13
 * @throws
 */
function getunitCode($unittype=0){
	$select_config = require DConfig_PATH."/System/selectlist.inc.php";
	$select_config = $select_config['unittype']['unittype'];
	foreach ($select_config as $k => $v) {
		if($unittype == $k){
			return $v;
		}
	}
}
/**
 * @Title: getunitTypeCode
 * @Description: todo(获取产品单位转换类型)
 * @param 类型 $type
 * @return $v
 * @author 杨东
 * @date 2013-6-3 下午5:36:19
 * @throws
 */
function getunitTypeCode($type=0){
	$select_config = require DConfig_PATH."/System/selectlist.inc.php";
	$select_config = $select_config['typeid']['typeid'];
	foreach ($select_config as $k => $v) {
		if($type == $k){
			return $v;
		}
	}
}
/*
 *
 */
function getobjname($typeid,$typename){
	$name="";
	if($typeid==1){
		$name=getFieldBy($typename,"id","name","MisSalesCustomer");
	}else if($typeid==2){
		$name=getFieldBy($typename,"id","name","MisPurchaseSupplier");
	}
	return $name;
}
/*
 *
 */
function getnextUrl($name,$par_val,$par='id',$action="index",$title="",$opentype="navTab" ,$navTab=__MODULE__){
	$url =strtolower(end(explode('/', __URL__)));
	$url_a = __URL__;
	$info = '<a rel="'.$navTab.'" title="'.$title.'" href="'.$url_a.'/'.$action.'/'.$par.'/' . $par_val . '/navTabId/'.$navTab.'" target="'.$opentype.'">'.$name.'</a>';
	return $info;
}
function getNodeSortIcon($name,$par_val,$par='id'){
	$url =strtolower(end(explode('/', __URL__)));
	$url_a = __URL__;
	$info='';
	if($name !=4 ){
		$info = '<a rel="'.$navTab.'" title="'.$title.'" href="'.$url_a.'/index/'.$par.'/' . $par_val . '/navTabId/'.$navTab.'" target="'.$opentype.'">'.$name.'</a>';
		$info = '<a class="up"  target="ajaxTodo" href="__URL__/setsort/frame/1/d/up/id/'.$par_val.'/rel/jbsxNodeBox"><img width="20" border="0" height="20" alt="正常" src="__PUBLIC__/Images/up.gif"></a>&nbsp;
			<a class="down"  target="ajaxTodo" href="__URL__/setsort/frame/1/d/down/id/'.$par_val.'/rel/jbsxNodeBox"><img width="20" border="0" height="20" alt="正常" src="__PUBLIC__/Images/down.gif"></a>';
	}
	return $info;
}

/**
 * @Title: getUrl
 * @Description: todo(这里用一句话描述这个方法的作用)
 * @param string  $name     链接名称
 * @param string  $par      定位对象
 * @param string  $par_val  定位对象值
 * @param string  $action   定位操作
 * @param string  $title    标签名称
 * @param string  $opentype 弹出样式
 * @param int     $width       宽度
 * @param int     $height      高度
 * @param boolean $mark      是否最小化
 * @return string
 * @author yangxi
 * @date 2013-6-18 下午6:33:03
 * @throws
 */

function getUrl($name,$object,$action="index",$par_val,$par='id',$title="默认页面",$opentype="navTab",$width=600,$height=400,$mask="true"){
	if($opentype=="navTab"){
		$info ='<a  rel="'.$object.'" title="'.$title.'" href="__APP__/'.$object.'/'.$action.'/'.$par.'/'. $par_val.'" target="'.$opentype.'" mask="'.$mask.'" width="'.$width.'" height="'.$height.'" >'.$name.'</a>';
	}
	if($opentype=="dialog"){
		$info ='<a  rel="'.$object.'" title="'.$title.'" href="__APP__/'.$object.'/'.$action.'/'.$par.'/'. $par_val.'" target="'.$opentype.'" mask="'.$mask.'" width="'.$width.'" height="'.$height.'" >'.$name.'</a>';
	}
	return $info;
}
/**
 * @Title: getBaseRole
 * @Description: todo(授权页面跳转，配置基础权限组)
 * @param int $objid      对象ID
 * @param string $title   传入名
 * @param int $type       类型默认为用户授权，1为高级组授权 2为面板模块授权
 * @param int $userid     多组织架构使用 为实现授权管理用户授权--2014-12-26 renlin
 * @return string
 * @author yangxi
 * @date 2013-6-18 上午9:24:11
 * @throws
 */
function getBaseRole( $objid=0 ,$title='授权',$type=0,$userid=0){
	$info="";
	if( $objid>0 || $userid>0){
		switch ($type){
			case 1:
				if(isset($_SESSION["rolegroup_roleaccess"])|| isset($_SESSION["a"])){
					$info = '<a rel="RolegroupAccess" title="'.$title.'" href="__APP__/Rolegroup/roleGroupAuthorizeC/rolegroupid/'.$objid.'" target="navTab">授权</a>';
				}
				break;
			case 2:
				if(isset($_SESSION["authorizeuser_roleaccess"])|| isset($_SESSION["a"])){
					$info = '<a rel="AuthorizeUserroleaccess" title="'.$title.'" href="__APP__/AuthorizeUser/roleAccess/userid/'.$objid.'" target="navTab">授权</a>';
				}
				break;
			default:
				if(isset($_SESSION["user_roleaccess"])|| isset($_SESSION["a"])){
					if(!intval($objid)){
						$objid=$userid;
					}
					$info = '<a rel="UserAccess" title="'.$title.'" href="__URL__/userAuthorizeB/userid/'.$objid.'" target="navTab">授权</a>';
				}
		}
	}
	return $info;
}

/**
 * getPublishReclaim 发布 /收回
 */

function getPublishReclaim($id,$fabuzhuangtai,$zhixingqingkuang,$moxingming,$fabupingtaiid,$type){
	if($fabuzhuangtai=='' || $fabuzhuangtai=='2' || $zhixingqingkuang=='5'){
		$title='发布';
		$info = '<a rel="__MODULE__add" title="'.$title.'" href="__APP__/MisAutoShb/add/biaoid/'.$id.'/moxingming/'.$moxingming.'" target="navTab">发布</a>';
	}else{
		$title='收回';
		$info = '<a rel="__MODULE__bac" title="'.$title.'" href="__APP__/MisAutoShb/edit/id/'.$fabupingtaiid.'/tuihui/1" target="navTab">收回</a>';
	}
	return $info;
}


function getDayBiaozhu($time,$type,$status , $fmt=''){
	$fmt = $fmt ? $fmt : 'Y-m-d';
	if(!empty($time) && $time!=0){
		$dc=$time-time();
		$days = 0;
		if($dc){
			// 每天小时数 * 分钟数 * 秒数
			$day = $dc/(24*60*60);
		}
		
		$time = transTime($time,$fmt);
		//var_dump($day);
		if($day<=2){
			$color="<span class='red' >".$time."</span>";
		}elseif( 2 < $day && $day<= 10){
			$color="<span class='green' >".$time."</span>";
		}else{
			$color=$time;
		}
		return $color;
		
	}
}

/**
 * getExecutiveCondition查看执行情况
 */
function getExecutiveCondition($id,$fabuzhuangtai,$zhixingqingkuang,$moxingming,$fabupingtaiid,$type){
	if($zhixingqingkuang==4){
		$title='处理';
		$info = '<a rel="__MODULE__add" title="'.$title.'" href="__APP__/MisAutoShb/audit/id/'.$fabupingtaiid.'" target="navTab">&nbsp;处理</a>';
	}elseif($zhixingqingkuang==null || $zhixingqingkuang==''){
		
	}else{
		$title='查看';
		$info = '<a rel="__MODULE__bac" title="'.$title.'" href="__APP__/MisAutoShb/view/id/'.$fabupingtaiid.'" target="navTab">&nbsp;查看</a>';
	}
	return $info;
}
/**
 * 个人预览权限（数字转文字）
 * 杨东
 * */
function getViewpermissions($key){
	$str = "";
	switch ($key) {
		case 0:
			$str = "全部";
			break;
		case 1:
			$str = "部门";
			break;
		case 2:
			$str = "个人";
			break;
		case 3:
			$str = "部门及子部门";
			break;
		default:
			$str = "全部";
			break;
	}
	return $str;
}

/**
 * @Desc 二维数组排序
 * @param $arr:二维数组
 * @param $keys:排序字段
 * @param $type:顺序：正序asc 倒序 desc
 * @return $new_array:排序完的二维数组
 * @author 杨东
 * */
function array_sort($arr,$keys,$type='asc'){
	$keysvalue = $new_array = array();
	foreach ($arr as $k=>$v){
		$keysvalue[$k] = $v[$keys];
	}
	if($type == 'asc'){
		asort($keysvalue);
	}else{
		arsort($keysvalue);
	}
	reset($keysvalue);
	foreach ($keysvalue as $k=>$v){
		$new_array[$k] = $arr[$k];
	}
	return $new_array;
}


/**
 *
 *物质获取图片路径，点击图标，弹出大图效果
 *@param $pcode  物资code
 *@author jiangx
 */
function getProductPictureUrl($pcode=""){
	if ($pcode) {
		$model = D("MisProductCode");
		$map['status'] = 1;
		$map['code'] = $pcode;
		$code = $model->where($map)->field('id,code,typeid')->find();
		$typemodel = D("MisProductType");
		$typecode = $typemodel->where("status = 1 and id=".$code['typeid'])->getField('code');
		$dir = UPLOAD_PATH;
		$dir = str_replace('Uploads','MisProductCode',$dir);
		$dir .= $typecode;
		if (false !== ($handle = opendir($dir))) {
			$i = 0;
			while (false !== $file = readdir($handle)) {
				if(preg_match('/^'.$pcode.'/',$file)){
					$arr = array(
							'name' => $file,
							'url'  => iconv("GBK","UTF-8","__PUBLIC__/MisProductCode/".$typecode.'/'.$file).'?'.time()
					);
					$imagelist[$i] = $arr;
					$i++;
				}
			}
		}
		closedir($handle);
		return getPictureHtml($imagelist,$pcode);
	}
}
/**
 *
 *获取图片路径，点击图标，弹出大图效果
 *@param $imgtype  1：mas图片 2：sub图片
 *@param $index 索引  $imgtype=1，$index 为masid;$imgtype=2，$index 为subid;
 *@param $type 附件配置文件对应的type
 *@author jiangx
 */
function getPictureUrl($imgtype = 1,$index="", $type = NULL){
	if ($index) {
		$imagelist = array();
		$map = array();
		//mas图片
		if ($imgtype == 1) {
			$map['orderid'] = $index;
		}
		//sub图片
		if ($imgtype == 2) {
			$map['subid'] = $index;
		}
		$map['status'] = 1;
		$map['type'] = $type;
		$mdoel = M("mis_attached_record");
		$attarry=$mdoel->where($map)->field('attached')->select();
		foreach ($attarry as $key => $val) {
			//获取后缀名
			$fileinfo = pathinfo($val['attached']);
			//后缀名转换成小写
			$postfix = strtolower($fileinfo['extension']);
			//匹配筛选图片格式
			if (preg_match('/^(png|jpg|bmp|gif)$/',$postfix)) {
				$arr = array(
						'url' => "__PUBLIC__/".$val['attached']
				);
				$imagelist[$key] = $arr;
			}
		}
		return getPictureHtml($imagelist,$index);
	}
}

/**
 * 图片数组组织成HTML
 * @param $imagelist 图片数组
 */
function getPictureHtml($imagelist,$pcode){
	$str="";
	if($imagelist){
		$str .= '<span class="proimage_span_icon" style="cursor:pointer;" onclick="proimageshow( this );"><img  width="16" height="16" title="点击查看" src="__PUBLIC__/Images/pic_img_icon.gif" /></span><span style="display:none;">';
		foreach($imagelist as $k=>$v){
			$str .= '<a class="pro_group_'.$pcode.'" href="'.$v["url"].'">image'.($k+1).'</a>';
		}
		$str .= "<span>";
	}else{
		$str .= '<span class="proimage_span_icon" style="cursor:pointer;" onclick="proimageshow( this );"><img  width="16" height="16" title="暂无图片" src="__PUBLIC__/Images/pic_noimg_icon.gif" /></span>';
	}
	return $str;
}


//极少使用，可以合并
function getWritableChname($num){
	switch ($num) {
		case 1:
			return "启用";
		default:
			return "禁用";
	}
}



/*动态配置表单时默认选项（select，checkbox）
 * @author wangcheng
 * @param string  $val  当前值   配置结构为   ###;1：XXX,2:XXX,3:XXX;
 * @param array	 $arr 所有值
 * @param array	 $type select,checkbox
 * @return excelfile $code
 */
function excelTplselected($val,$arr,$type="selected"){
	$arr=explode(",",$arr);
	if($type=="checkbox"){
		$str=array();
		$val=unserialize($val);
		foreach($arr as $k=>$v ){
			$a=explode(":",$v);
			if( count($a)>1 ){
				if( in_array($a[0],$val) ){
					array_push($str,$a[1]);
				}
			}else{
				if( in_array($v,$val) ){
					array_push($str,$v);
				}
			}
		}
		return implode(" ",$str);
	}else{
		$str="";
		foreach($arr as $k=>$v ){
			$a=explode(":",$v);
			if( count($a)>1 ){
				if( $val==$a[0] ){
					$str=$a[1];
					break;
				}
			}else{
				if( $val==$v ){
					$str=$v;
					break;
				}
			}
		}
		return $str;
	}
}
/**
 * @Title: excelTplidToname
 * @Description: todo(逗号分隔的ID，转换成逗号分隔的name)
 * @param array $val	逗号分隔的ID
 * @param string $md	查询的model
 * @param string $name 需要查询的字段，返回的字段
 * @return unknown
 * @author liminggang
 * @date 2013-9-23 下午4:34:52
 * @throws
 */
function excelTplidToname($val,$md,$name){
	$arr=explode(",",$val);
	$model = M($md);
	$map['id'] = array(' in ',$arr);
	$map['status'] = 1;
	$namelist=$model->where($map)->getField($name,true);
	$namelist=implode(",",$namelist);
	return $namelist;
}

/**
 * @Title: excelTplidTonameAppend
 * @Description: todo(逗号分隔的ID，转换成逗号分隔的name)
 * @param array $val	逗号分隔的值
 * @param string $key	值存关联的字段名
 * @param string $name 需要查询的字段，返回的字段
 * @param string $md	查询的model
 * @return string
 * @author nbmxkj
 * @date 2014-8-1 9:24
 * @throws
 */
function excelTplidTonameAppend($val,$key='id', $name='name',$md){
	$arr=explode(",",$val);
	if(!$md){
		return '数据转换函数错误，表名为空';
	}
	$model = M($md);
	$map[$key] = array(' in ',$arr);
	$map['status'] = 1;
	$namelist=$model->where($map)->getField($name,true);
	$namelist=implode(",",$namelist);
	return $namelist;
}

/**
 +----------------------------------------------------------
 * 模板函数方法调用
 * +----------------------------------------------------------
 * @author wangcheng
 * @date:2012-06-08
 * @param string   $param1 	显示名称
 * @param string   $param2  	部分url参数(包括操作,及其他参数)
 * @param string   $m	     	控制器
 * @param string   $a	     	操作
 * @param string   $rel	 	打开页面名称
 * @param string   $target	打开方式(ajax,navtab,dialog等)
 * @param string   $callback	返回函数
 * @param string   $width  打开窗口的宽度
 * @param string   $height  打开窗口的高度
 +----------------------------------------------------------
 * @return string  返回经过函数处理有的结果
 +----------------------------------------------------------
 */
function createUrl($param1,$param2="",$m="",$a="index",$rel="__MODULE__",$target="navTab",$title="",$callback="",$width="",$height=""){
	$width = $width ? "width='".$width."'":"";
	$height= $height? "height='".$height."'":"";
	if( $m ){
		$url =strtolower($m);
		$url_a = __APP__.'/'.$m.'/'.$a;
	}else{
		$url =strtolower(end(explode('/', __URL__)));
		$url_a = __URL__.'/'.$a;
	}
	if($callback) $callback='callback="'.$callback.'"';

	$str="";
	if($param1){
		$n=substr($a,0,6) == "lookup" ? 1:0;
		$n2=substr($a,0,6) == "combox" ? 1:0;
		// 		if($_SESSION[$url.'_'.$a] || $_SESSION['a'] || $n || $n2){
		$str= '<a href="'.$url_a.'/'.$param2.'" rel="'.$rel.'" title="'.$title.'" target="'.$target.'" '.$callback.' '.$width.' '.$height.'>'.$param1.'</a>';
		// 		}
	}
	return $str;
}

function getUserRoleView($userid,$param="",$m="",$rel="",$target="navTab",$title="",$width="",$height="",$mask=true){
	$title .= "-权限查看";
	$class = "iconView";
	$text= getOperateUrl($param,$m,"lookuproleView",$class,$rel,$target,$title,$width,$height,$mask);
	return $text;
}
/**
 * @Title: getOperateUrl
 * @Description: todo(这里用一句话描述这个方法的作用)
 * @param URL后面的参数 $param2
 * @param 控制器名称 $m  如果传入了控制器名称，则进入这个控制器进行操作。否则就模型当前调用方法的控制器
 * @param 控制器方法名称 $a 调用的方法名称
 * @param 当前URL的class名称 $class class代表有什么图标显示
 * @param 弹出的窗口的tabid $rel
 * @param 当前弹出窗口类型 $target dialog,navTab
 * @param 弹出窗口的title名称 $title
 * @param 宽度 $width
 * @param 高度 $height
 * @param 是否隐藏后面 $mask
 * @return string
 * @author liminggang
 * @date 2013-12-12 下午4:46:28
 * @throws
 */
function getOperateUrl($param2="",$m="",$a,$class="",$rel="",$target="",$title="",$width="",$height="",$mask=true){
	$rel= $rel ? $rel:__MODULE__;
	$target = $target ? $target:"navTab";
	$width = $width ? "width='".$width."'":"";
	$height= $height? "height='".$height."'":"";
	if( $m ){
		$url =strtolower($m);
		$url_a = __APP__.'/'.$m.'/'.$a;
	}else{
		$url =strtolower(end(explode('/', __URL__)));
		$url_a = __URL__.'/'.$a;
	}
	$str="";
	$n=substr($a,0,6) == "lookup" ? 1:0;
	$n2=substr($a,0,6) == "combox" ? 1:0;
	if($_SESSION[strtolower($url.'_'.$a)] || $_SESSION['a'] || $n || $n2){
		$str= '<a class="'.$class.'" '.$width.' '.$height.' href="'.$url_a.'/'.$param2.'" rel="'.$rel.$a.'" title="'.$title.'" mask="'.$mask.'" target="'.$target.'"></a>';
	}
	return $str;
}

// 随机生成一组字符串
function build_count_rand ($number,$length=4,$mode=1) {
	if($mode==1 && $length<strlen($number) ) {
		//不足以生成一定数量的不重复数字
		return false;
	}
	$rand   =  array();
	for($i=0; $i<$number; $i++) {
		$rand[] =   rand_string($length,$mode);
	}
	$unqiue = array_unique($rand);
	if(count($unqiue)==count($rand)) {
		return $rand;
	}
	$count   = count($rand)-count($unqiue);
	for($i=0; $i<$count*3; $i++) {
		$rand[] =   rand_string($length,$mode);
	}
	$rand = array_slice(array_unique ($rand),0,$number);
	return $rand;
}
/**
 +----------------------------------------------------------
 * 产生随机字串，可用来自动生成密码 默认长度6位 字母和数字混合
 +----------------------------------------------------------
 * @param string $len 长度
 * @param string $type 字串类型
 * 0 字母 1 数字 其它 混合
 * @param string $addChars 额外字符
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function rand_string($len=6,$type='',$addChars='') {
	$str ='';
	switch($type) {
		case 0:
			$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.$addChars;
			break;
		case 1:
			$chars= str_repeat('0123456789',3);
			break;
		case 2:
			$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ'.$addChars;
			break;
		case 3:
			$chars='abcdefghijklmnopqrstuvwxyz'.$addChars;
			break;
		case 4:
			$chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借".$addChars;
			break;
		default :
			// 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
			$chars='ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'.$addChars;
			break;
	}
	if($len>10 ) {//位数过长重复字符串一定次数
		$chars= $type==1? str_repeat($chars,$len) : str_repeat($chars,5);
	}
	if($type!=4) {
		$chars   =   str_shuffle($chars);
		$str     =   substr($chars,0,$len);
	}else{
		// 中文随机字
		for($i=0;$i<$len;$i++){
			$str.= msubstr($chars, floor(mt_rand(0,mb_strlen($chars,'utf-8')-1)),1);
		}
	}
	return $str;
}
/**
 +----------------------------------------------------------
 * 模板函数方法调用
 * +----------------------------------------------------------
 * @author wangcheng
 * @date:2012-12-05
 * @param int      $time 	1363456743 传入时间戳
 * @param string   $precision  	返回位数
 +----------------------------------------------------------
 * @return string  返回经过函数处理有的结果
 +----------------------------------------------------------
 */
function timeDiff( $time ,$precision=2) {
	if(empty($time)) return '';
	import ( "@.ORG.Date" );
	$date=new Date(intval($time));
	return $date->timeDiff(time(),$precision);
}

/**
 * 极少使用。只在知识库调用
 +----------------------------------------------------------
 * 模板函数方法调用
 * +----------------------------------------------------------
 * @author wangcheng
 * @date:2012-12-05
 * @param int      $levels 	传入1,2,3,4
 +----------------------------------------------------------
 * @return string  返回经过函数处理有的结果
 +----------------------------------------------------------
 */
function getKnowleageLevel($levels=0){
	switch ($levels){
		case 1:
			$levels = "A级";
			break;
		case 2:
			$levels = "B级";
			break;
		case 3:
			$levels = "C级";
			break;
		case 4:
			$levels = "D级";
			break;
		default:
			$levels = "未知级别";
	}
	return $levels;
}

/**
 * 中文字符串截取 2012-12-24
 * @author wangcheng
 * @param string $string 字符串
 * @param int $length  长度
 * @param bool	$append 是否加上"..."
 */
function missubstr($string,$length,$append = false,$appendcode="...")
{
	if(strlen($string) <= $length )
	{
		return $string;
	}
	else
	{
		$i = 0;
		while ($i < $length)
		{
			$stringTMP = substr($string,$i,1);
			if ( ord($stringTMP) >=224 )
			{
				$stringTMP = substr($string,$i,3);
				$i = $i + 3;
			}
			elseif( ord($stringTMP) >=192 )
			{
				$stringTMP = substr($string,$i,2);
				$i = $i + 2;
			}
			else
			{
				$i = $i + 1;
			}
			$stringLast[] = $stringTMP;
		}
		$stringLast = implode("",$stringLast);
		if($append)
		{
			$stringLast .= $appendcode;
		}
		return $stringLast;
	}
}
/**
 * @Title: showhtml
 * @Description: todo(动态配置td上面显示的其他效果。)
 * @param html $html
 * @param $role 权限 （判断是否有此权限）
 * @return string  返回一段处理过后的html代码。自动转换为当前效果。
 * @author wangcheng
 * @throws
 */
function  showhtml($html="", $role = false){
	if (!$_SESSION['a']) {
		if ($role !== false && (!isset($_SESSION[$role]))) {
			return '';
		}
	}

	return stripslashes($html);
}

/**
 * Utf-8、gb2312都支持的汉字截取函数
 * cut_str(字符串, 截取长度, 开始长度, 编码);
 * 编码默认为 utf-8
 * 开始长度默认为 0
 */
function cut_str($string, $sublen, $start = 0, $code = 'UTF-8') {
	if($code == 'UTF-8') {
		$pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
		preg_match_all($pa, $string, $t_string);
		if(count($t_string[0]) - $start > $sublen) return join('', array_slice($t_string[0], $start, $sublen))."...";
		return join('', array_slice($t_string[0], $start, $sublen));
	} else {
		$start = $start*2;
		$sublen = $sublen*2;
		$strlen = strlen($string);
		$tmpstr = '';
		for($i=0; $i< $strlen; $i++) {
			if($i>=$start && $i< ($start+$sublen)) {
				if(ord(substr($string, $i, 1))>129) {
					$tmpstr.= substr($string, $i, 2);
				} else {
					$tmpstr.= substr($string, $i, 1);
				}
			}
			if(ord(substr($string, $i, 1))>129) $i++;
		}
		if(strlen($tmpstr)< $strlen ) $tmpstr.= "...";
		return $tmpstr;
	}
}


/**
 * @Title: getExtension
 * @Description: todo(获取上传文件的后缀名，并加上相应的图片以便区分)
 * @param unknown_type $upname（mis_attached_record表里面的upname字段）
 * @return string
 * @author xiafengqin
 * @date 2013-7-8 下午2:58:42
 * @throws
 */
function getExtension($upname){
	$nameAry =  explode ( '.', $upname );
	$extension = strtolower( end($nameAry) );
	if ( in_array($extension,array("gif","png","jpg","bmp","jpeg")) ){
		return '<img src="__PUBLIC__/Images/icon/doc_img.png">&nbsp;'.$upname;
	}elseif ( in_array($extension,array("doc","docx" )) ){
		return '<img src="__PUBLIC__/Images/icon/doc_word.png">&nbsp;'.$upname;
	}elseif ( in_array($extension,array("xls","xlsx" )) ){
		return '<img src="__PUBLIC__/Images/icon/doc_excel.png">&nbsp;'.$upname;
	}elseif ($extension == 'pdf' ){
		return '<img src="__PUBLIC__/Images/icon/doc_pdf.png">&nbsp;'.$upname;
	}elseif ( in_array($extension,array("rar","zip" )) ){
		return '<img src="__PUBLIC__/Images/icon/doc_zip.png">&nbsp;'.$upname;
	}else {
		if( count($nameAry)==1 ){
			return '<img src="__PUBLIC__/Images/icon/folder_s.png">&nbsp;'.$upname;
		}
		return '<img src="__PUBLIC__/Images/icon/doc_other.png">&nbsp;'.$upname;
	}
}
/**
 * 极少使用   还原数据库用到，要改造
 * @Title: download
 * @Description: todo(附件上传记录的下载)
 * @param unknown_type $staus（只有他的状态为一的才能下载）
 * @param unknown_type $attached（路径）
 * @return string
 * @author xiafengqin
 * @date 2013-7-8 下午4:11:06
 * @throws
 */
function download($staus,$attached){
	if ($staus){
		return "<a class='iconDownload' href='__PUBLIC__/".$attached."' target='_blank'><span>下载</span></a>";
	}
}


//scan the function oprmaize
function xhprof_scan_func($func,$obj=""){
	C ( 'SHOW_RUN_TIME', false ); // 运行时间显示
	C ( 'SHOW_PAGE_TRACE', false );
	$this->display();
	//开始测试
	//C ( 'SHOW_RUN_TIME', true ); // 运行时间显示
	//C ( 'SHOW_PAGE_TRACE', true );
	//$this->display();

	//开始执行xhprof
	xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);

	$xhprof_on = true;

	////
	//此处为你的程序
	if($obj==""){
		$this->$func;
	}
	else{
		$obj=new $obj();
		$obj->getAllScheduleList();
	}
	// 		///

	if($xhprof_on){

		$xhprof_data = xhprof_disable();
		import('@.ORG.xhprof.xhprof_lib','', $ext='.php');
		import('@.ORG.xhprof.xhprof_runs','', $ext='.php');

		$xhprof_runs = new XHProfRuns_Default();

		$run_id = $xhprof_runs->save_run($xhprof_data, "eagle");//（eagle为命名空间，你可以随意取）

		//echo '<a href="http://localhost/xhprof_html/index.php?run='.$run_id.'&source=eagle" target="_blank">统计</a>';
	}
}

/**
 * 查测是否是图片 2013-08-17
 * @author eagle
 * @emal 66eagle@163.com
 * @param string $fileName 文件名字
 * @param int $showIcon 默认显示的文件名字
 * sample：模板中使用 {$att.attached|isPicture}
 */
function isPicture($fileName,$showIcon="Images/xyicon/-.png")
{
	$str = strtolower(end(explode(".",$fileName)));

	//获得上传文件的扩展名，然后再进行判断
	if($str=="jpg" or $str=="jpeg" or $str=="gif" or $str=="png")
	{
		return $fileName;
	}
	else{
		return $showIcon;
	}

}

/**
 * @Title: 生成二维码
 * @Description: todo(生成二维码)
 * @author  eagle
 * @date 2013-6-1 下午4:33:56
 * @throws
 * 模板调用方法:
 * {$vo.id|createQRCode=###,'www.laicaoyuan.com','Images/ico.png'}
 */
function createQRCode($sid,$url,$picPath="Images/logo_ico.png"){
	import("@.ORG.LogoQRmake");
	$picPath = PUBLIC_PATH.$picPath;
	$png = new LogoQRmake($url,$picPath,'120x120');
	$img=$png->images();
	return $img;
}
/**
 * @Title: getSelectByHtml
 * @Description: todo(获取select配置文件的数组)
 * @param select配置文件的主键 $key
 * @param 生成HTML的类型 $type
 * @param 默认选中项的key $selected
 * @param 踢出不需要的value $akickout 数组/字符串
 * @param 判断是否踢出 默认为踢出 否则为包含 $ifkickout boolean
 * @author 杨东
 * @date 2013-9-13 上午11:57:14
 * @throws
 */
function getSelectByHtml($key,$type = "select",$selected, $akickout="0",$ifkickout=true){
	$list = require('./Dynamicconf/System/selectlist.inc.php');
	$list = $list[$key][$key];
	$str = "";
	if($list){
		switch ($type) {
			case "select":
				foreach ($list as $k => $v) {
					if ($akickout != 0) {
						if (!is_array($akickout)) {
							$akickout = explode(',', $akickout);
						}
						if($ifkickout){
							if (in_array($k, $akickout)) {
								continue;
							}
						} else {
							if (!in_array($k, $akickout)) {
								continue;
							}
						}
					}
					$str .= '<option value="'.$k.'"';
					if(isset($selected) && $selected == $k) $str .= ' selected="selected" ';
					$str .= '>'.$v.'</option>';
				};
				break;
			case "array"://20130925 xiafengqin 把当前的key当成数组返回
				$str = $list;
				break;
		}
	}
	return $str;
}
/**
 * @Title: getSelectByName
 * @Description: todo(获取select配置文件中的值)
 * @param select配置文件的主键 $key
 * @param 对应的key值 $selected
 * @author 杨东
 * @date 2013-9-13 下午12:07:57
 * @throws
 */
function getSelectByName($key,$selected){
	$list = require('./Dynamicconf/System/selectlist.inc.php');
	$list = $list[$key][$key];
	$str = "";
	foreach ($list as $k => $v) {
		if($selected == $k) $str = $v;
	};
	return $str;
}
/**
 * 
 * @Title: getSelectlistByName
 * @Description: todo(获取selectlist文件中的指定值) 
 * @param string $selected	当前选中的值， 多个值以逗号分隔
 * @param string $key 		selecklist中的key
 * @author quqiang 
 * @date 2015年1月16日 下午3:37:40 
 * @throws
 */
function getSelectlistByName( $selected , $key){
	$list = require('./Dynamicconf/System/selectlist.inc.php');
	$list = $list[$key][$key];
	$str = "";
	$selected = explode(',',$selected);
	$str = '';
	foreach ($selected as $k=>$v){
		$str[] = $list[$v];
	};
	return is_array($str)?join(',',$str):'';
}
/**
 * @Title: getDatabaseByHtml
 * @Description: todo(通过传入表名返回html值)
 * @param 传入字符串表名 $sTable
 * @param 扩展参数 $param
 * @author 杨东
 * @date 2013-10-17 下午3:46:07
 * @throws
 */
function getDataBaseByHtml($sTable,$param){
	$mModel = M($sTable);
	$sSelected = $param['selected'] ? $param['selected']:null;//传入字符串选中值
	$sName = $param["name"] ? $param["name"]:"name";//传入字符串显示名称
	$sVal = $param["id"] ? $param["id"]:"id";//传入字符串数据库存入值
	$sType = $param["type"] ? $param["type"]:"select";//传入字符串返回类型
	$tagName	=	$param['names']?$param['names']:uniqid().'[]';//checkbox或者radio name属性
	$sConditions = $param["conditions"] ? $param["conditions"]:"";//传入字符串查询条件
	$sWhere = " status = 1 ";
	if($sConditions) $sWhere .= " AND ".$sConditions;
	$aList = $mModel->where($sWhere)->getField($sVal.','.$sName);
	$str = "";
	if($aList){
		switch ($sType) {
			case "select":
				foreach ($aList as $k => $v) {
					$str .= '<option value="'.$k.'"';
					if($sSelected != null && $sSelected == $k) $str .= ' selected="selected" ';
					$str .= '>'.$v.'</option>';
				};
				break;
			case "checkbox":
				foreach ($aList as $k => $v) {
					$str .='<div class="left tml-checkbox tml-w100">';
					$str .='<label><input name="'.$tagName.'" value="'.$k.'" ';
					if(in_array($k, $sSelected)) $str .= ' checked="checked" ';		
					$str .=	'type="checkbox">'.$v.'</label>';
					$str .='</div>';
				}
				break;
		}
	}
	return $str;
}
/**
 * @Title: getDataBaseLevelByHtml
 * @Description: todo(通过传入表名返回html值)
 * @param 传入字符串表名 $sTable
 * @param 扩展参数 $param
 * @author 杨东
 * @date 2013-10-17 下午3:46:07
 * @throws
 */
function getDataBaseLevelByHtml($sTable,$param){
	$mModel = M($sTable);
	$sSelected = $param['selected'] ? $param['selected']:null;//传入字符串选中值
	$sName = $param["name"] ? $param["name"]:"name";//传入字符串显示名称
	$sVal = $param["id"] ? $param["id"]:"id";//传入字符串数据库存入值
	$sType = $param["type"] ? $param["type"]:"select";//传入字符串返回类型
	$sConditions = $param["conditions"] ? $param["conditions"]:"";//传入字符串查询条件
	$sWhere = " status = 1 ";
	if($sConditions) $sWhere .= " AND ".$sConditions;
	// 	$model=D('MisSystemRecursion');
	//修复人事bug 改为new
	$model = new MisSystemRecursionModel();
	$aList=$model->modelShow($sTable,array('key'=>'id','pkey'=>'parentid','fields'=>"id,name",'conditions'=>$sWhere),0,1);
	$str = '';//"<option value=\"\">请选择</option>";
	if($aList){
		switch ($sType) {
			case "select":
				foreach ($aList as $k => $v) {
					$dis = '';
					if($v['nextEnd']==0){
						$dis = 'disabled="disabled"';
					}
					$lev = '';
					if($v['level']){
						$lev=' class="level'.$v['level'].'"';
					}
					$str .= '<option '.$dis.$lev.' value="'.$v['id'].'"';
					if($sSelected != null && $sSelected == $v['id']) $str .= ' selected="selected" ';
					$str .= '>'.$v['name'].'</option>';
				};
				break;
		}
	}
	return $str;
}


/**
 * @Title: 获取用户权限
 * @author qchlian
 * @date 2013-10-24
 * @param int $authId 用户id
 */
function getAuthAccess( $authId="" ){
	if($_SESSION[C('ADMIN_AUTH_KEY')]) return array();
	$authId = $authId ? $authId:$_SESSION[C('USER_AUTH_KEY')];
	$file =  DConfig_PATH."/AccessList/access_".$authId.".php";
	if( !file_exists($file) ){
		import ( '@.ORG.RBAC' );
		$accessList =RBAC::getAccessList($authId);
		if (!file_exists(DConfig_PATH."/AccessList")){
			createFolder(dirname(DConfig_PATH."/AccessList"));
			mkdir(DConfig_PATH."/AccessList", 0777);
		}
		RBAC::writeover($file,"return ".RBAC::pw_var_export($accessList).";\n",true);
		foreach($accessList as $k3 => $v3){
			foreach($accessList[$k3] as $k1 => $v1 ){
				foreach($accessList[$k3][$k1]  as $k => $v ){
					$p=explode("-",$v);
					$_SESSION[strtolower($k1.'_'.$k)] = $p[1];
				}
			}
		}
	}
	$access =require $file;
	return $access;
}
/**
 * @Title: getMoveNode
 * @Description: todo(移动节点的按钮)
 * @$id id
 * @$status status
 * @author jiangx
 * @date 2013-11-15 上午10:11:12
 * @throws
 */
function getMoveNode($id = "", $status = 0){
	$html = "";
	if ($status != 1 || $id == "") {
		return $html;
	}
	$mNodeMdoel = D("Node");
	$aMap = array();
	$aMap['id'] = $id;
	$mModeResult = $mNodeMdoel->where($aMap)->find();
	if (!$mModeResult) {
		return $html;
	}
	if ($mModeResult['level'] == 2 || $mModeResult['level'] == 3) {
		$html = '<a class="" target="dialog" mask="true" title="节点_移动" rel="Nodemove" href="__URL__/move/id/'.$id.'" height="400">移动</a>';
	}
	return $html;
}



/**
 * @Title: byteformat
 * @Description: todo(计算文件大小)
 * @$size 大小(B)
 * @author qchlian
 * @date 2013-12-27 下午14:16:42
 * return size
 */
function byteFormat($size, $dec=2) {
	$a = array("B", "KB", "MB", "GB", "TB", "PB");
	$pos = 0;
	while ($size >= 1024) {
		$size /= 1024;
		$pos++;
	}
	return round($size,$dec)." ".$a[$pos];
}

/**
 * @Title: getViewButtonStatus
 * @Description: todo(获得每个模块下面的按钮操作，并把样式(自定义)放在数组里面。在action的_after_list里面调用的。)
 * @param unknown_type $voList :_list里面的$voList.
 * @author xiafengqin
 * @date 2013-11-15 上午10:11:12
 * @throws
 */
function getViewButtonStatus(&$voList) {
	foreach ($voList as $key => $value) {
		$arr = array(
				'js-add'=>'js-add'
				);
				if($value['status'] >=0 ){
					if ($value['auditState'] == 0 || $value['auditState'] == -1){
						$arr['js-edit'] = 'js-edit';
					}
					if ($value['auditState'] == 1 ) {
						$arr['js-iconBack'] = 'js-iconBack';
						$arr['js-view'] = 'js-view';
					}
					if ( $value['auditState'] == 2) {
						$arr['js-view'] = 'js-view';
					}
					if ($value['auditState'] == 3){
						$arr['js-abb'] = 'js-abb';
						$arr['js-view'] = 'js-view';
						$arr['js-replenish'] = 'js-replenish';
						$arr['js-abolish'] = 'js-abolish';
					}
					if ($value['status'] == 1 && $value['auditState'] == '') {
						$arr['js-edit'] = 'js-edit';
					}
				}
				$voList[$key]['classarr'] = json_encode($arr);
	}
}
/**
 * @Title: getAgeByToCarIdForList
 * @Description: todo(通过身份证算出出生年月 年龄 显示在列表上)
 * @param unknown_type $idcard 身份证号
 * @param unknown_type $isYear如果有值 返回为出生年月  空则返回为年龄
 * @return string|number
 * @author renling
 * @date 2013-12-3 下午3:26:55
 * @throws
 */
function getAgeByToCarIdForList($idcard,$isYear=''){
	//获得身份证的出生年月日
	$year = substr($idcard,6, 4);
	$month = substr($idcard,10, 2);
	$day = intval(substr($idcard,12, 2));
	$birthday=$year."-".$month."-".$day;
	$date=strtotime($birthday);
	if($isYear){
		return $birthday;
	}else{
		//获得出生年月日的时间戳
		$today=strtotime('today');
		//获得今日的时间戳
		$diff=floor(($today-$date)/86400/365);
		//得到两个日期相差的大体年数
		//strtotime加上这个年数后得到那日的时间戳后与今日的时间戳相比
		$age=strtotime(substr($id,6,8).' +'.$diff.'years')>$today?($diff):$diff-1;
		return $age;
	}
}

/**
 * @Title: getCHStimeDifference
 * @Description: todo(获取显示中文的时间比)
 * @param 开始时间戳 $starttime
 * @param 结束时间戳不传表示当前时间 $endtime
 * @return string
 * @author 杨东
 * @date 2014-2-22 下午2:46:33
 * @throws
 */
function getCHStimeDifference($starttime,$endtime,$step) {
	// 判断结束时间是否传入，没有传入设置为当前时间
	if (empty($endtime)) {
		$endtime = time();
	}
	$resulttime = $endtime-$starttime;//取结束时间减去开始时间的差值
	$day = intval($resulttime/86400);//天
	$hour = intval(($resulttime-$day*86400)/3600);//小时
	$minute = intval(($resulttime-$day*86400-$hour*3600)/60);//分钟
	$seconds = intval($resulttime-$day*86400-$hour*3600-$minute*60);//秒
	if($step=="dh"){
		//天和小时
		return $day."天".$hour."小时";
	}else if($step =="H"){
		//总小时
		return $day*24+$hour;
	}else{
		return $day."天".$hour."小时".$minute."分钟".$seconds."秒";
	}
}
/**
 * @Title: getWorkMonitoringCurNodeUser
 * @Description: todo(工作监控中待办人员列表)
 * @param 待办人员ID组 $curNodeUser
 * @return Ambigous <string, 格式化后的待办人员中文>
 * @author 杨东
 * @date 2014-2-24 下午2:03:44
 * @throws
 */
function getWorkMonitoringCurNodeUser($curNodeUser){
	$mUser = D("User");
	$uMap['id'] = array("in",$curNodeUser);
	$ulist = $mUser->where($uMap)->getField("id,name");
	$uStr = "";// 初始化返回值
	foreach ($ulist as $k => $v) {
		if($uStr != "") $uStr.= "|";// 判断是否为第一个人
		$uStr .= '<a class="send" mask="true" width="530" height="300" rel="ADVICE" target="dialog"
				href="__APP__/SendMsg/index/userid/'.$k.'" title="发送消息给'.$v.'"></a>';
		if($k == $_SESSION[C('USER_AUTH_KEY')]){
			$uStr .= "<strong>".$v."</strong>";// 如果当前登录人在待办人员里面则加粗显示
		} else {
			$uStr .= $v;
		}
	}
	return $uStr;
}
/**
 *
 * @Title: getTaskulous
 * @Description: todo(查询我的待办事项 我的工作)
 * @return Ambigous <mixed, string, boolean, NULL, unknown>
 * @author renling
 * @date 2014-3-29 下午3:45:08
 * @throws
 */
function getTaskulous(){
	$model = D('MisWorkMonitoring');
	$map['dostatus'] = 0;
	$map['_string'] = 'FIND_IN_SET(  '.$_SESSION[C('USER_AUTH_KEY')].', curAuditUser )';
	$data = $model->where($map)->select();
	return $data;
}
/**
 * @Title: getGrayImg
 * @Description: todo(生成灰色图片 jgp,png,gif)
 * @$resimg 资源路径
 * @$grayimg 输出文件路径
 * @date 2013-12-31
 */
function getGrayImg($resimg, $grayimg) {
	$imageinfo = strtolower(pathinfo($resimg, PATHINFO_EXTENSION));
	switch ($imageinfo) {
		case 'png':
			$image = imagecreatefrompng($resimg);
			break;
		case 'gif':
			$image = imagecreatefromgif($resimg);
			break;
		default:
			$image = imagecreatefromjpeg($resimg);
			break;
	}
	if ($image && imagefilter($image, IMG_FILTER_GRAYSCALE)) {
		switch ($imageinfo) {
			case 'png':
				//这儿用输出jpg函数输出，$grayimg 文件后缀依旧为png
				imagejpeg($image,$grayimg,75);
				break;
			case 'gif':
				imagegif($image,$grayimg,75);
				break;
			default:
				imagejpeg($image,$grayimg,75);
				break;
		}
	} else {
		return false;
	}
	imagedestroy($image);
	return true;
}

/*
 * 极少使用 在system目录下的oftenconfig
 * 自定义函数
 * 添加注，Eagle
 * $value : 查找关键值
 * $key		: 目标表中的字段名
 * $fieldName	: 被检索出来的目标表中的字段名
 * $table 		: 被检索的目标表名
 * $field1		: 第二个条件，表字段名，
 * $value1 		： 第二个条件的值
 * $session 		： 开关，1后， 会把map条件用有一个给定值，去$session变量中去取回来
 * */
function getFieldByEagle($value,$key,$fieldName,$table,$field1='',$value1='',$session=''){
	$model = D($table);
	//修改此处，加入一个1=1 的条件。方便特殊权限不控制到。
	$map[$key] = $value;
	if(is_array($value)){
		$map[$key] =array("in",$value);
	}
	if($field1){
		//$var = "and '.$field11.'='.$value1.'";
		if($session){
			$map[$field1] = $_SESSION[$value1];
		}else{
			$map[$field1] = $value1;
		}
	}
	$map['_string'] = "1=1";
	// 	"'.$key.'='.$value.' and 1=1 '.$var.'"
	$data = $model->where($map)->getField($fieldName,true);
	if(count($data)<=1){
		$data=$data[0];
	}
	return $data;
}
/**
 * @Title: getFinaceDigits
 * @Description: todo(获取小数位数 并格式化,财务计算小数位数)
 * @param 数量 $num
 * @param 格式化数据或者取小数位数 $returndigits
 * @param 单位ID $unitid
 * @param 产品ID $prodid
 * @return string|获取小数位数 并格式化/小数位数
 * @author 杨东
 * @date 2013-5-7 下午3:38:29
 * @throws
 */
function getFinaceDigits($num=0,$returndigits=true,$prodid,$unitid){
	$ConfigListModel = D('SystemConfigList');
	$digits = $ConfigListModel->GetValue('finaceDigits');// 获取小数位数
	if($returndigits){
		$formatDigits = true;
		if($unitid){
			$model = D('MisProductUnitexchange');
			//获取单位类型
			$unittype = $model->table('mis_product_unit as mis_product_unit,mis_product_unitexchange as mis_product_unitexchange')->where('mis_product_unit.id = mis_product_unitexchange.subunitid and mis_product_unitexchange.id='.$units)->getField('mis_product_unit.unittype as unittype');
			if ($unittype == false) {
				$formatDigits = false;
			}
		} else {
			if($prodid){
				$model = D('MisProductUnit');
				$unittype = $model->table('mis_product_unit as mis_product_unit,mis_product_code as mis_product_code')->where('mis_product_unit.id = mis_product_code.baseunitid and mis_product_code.id='.$prodid)->getField('mis_product_unit.unittype as unittype');
				if ($unittype == false) {
					$formatDigits = false;
				}
			}
		}
		if($formatDigits){
			$num = number_format($num,$digits);
		} else {
			$num = number_format($num);
		}
		return $num;
	}else{
		return $digits;
	}
}


/**
 * @Title: getCommonSettingSkey
 * @Description: todo(通过key获取common_setting表中的value，确定开关状态)
 * @paramer $skey:开关主键
 * @author  yangxi
 * @date 2013-10-12 下午4:33:56
 * @throws
 */
function getCommonSettingSkey($skey=""){
	$re=false;
	if($skey){
		$model = M("common_setting");
		$skey = strtolower($skey);
		$re = $model->where($map)->getBySkey($skey);
		$re = trim($re["svalue"]) ? true:false;
	}
	return $re;
}
/**
 * 获取节点的父点ID
 * @param int $id
 */
function getNodeParent($id){
	$model = M('Node');
	$data = $model->where('id='.$id)->field('pid')->find();
	return $data['id'];
}
/**
 * 获取selelist配置中的数据列表
 * @param string	$val	当前数据
 * @param string	$arr	selectlist中的键名
 */
function getSelectlistValue($val,$arr){
	$model=D('Selectlist');
	$temp = $model->GetRules($arr);
	$data = $temp[$arr];
	$val = explode(',', $val);
	unset($temp);
	foreach ($val as $k=>$v){
		$temp[]=$data[$v];
	}
	return join(',', $temp);
}
/**
 * ungetSelectlistValue
 * @param getSelectlistValue反转函数
 * @param 
 */
function ungetSelectlistValue($val,$arr){
	$model=D('Selectlist');
	$temp = $model->GetRules($arr);
	$data = $temp[$arr];
	//键与值互换
	$data=array_flip($data);
	$val = explode(',', $val);
	unset($temp);
	foreach ($val as $k=>$v){
		$temp[]=$data[$v];
	}
	return join(',', $temp);
}
/**
 * 获取当前用户的权限设置
 * @Title: getCurrentUserDataRight
 * @Description: todo(这里用一句话描述这个方法的作用) 
 * @param 	int			$type	类型，1：表，2 ：枚举 ，可扩展，默认1。
 * @param 	string		$key		根据$type 值决定，1：名表，2：枚举key  
 * @param	string		$fieldName	要过滤的字段名
 * @param	boolean	$isFmt		是否处理数据格式，默认false , 不处理。处理格式后结果变为数组，不处理结果为string
 * @param	string 		$mapType		条件格式化类型，只对$ype =1 类型为表时才生效。默认空值，不做处理。1：string类型，及以 《字段 运算符 值》为格式，2：ThinkPHP map类型，$map[字段] = 值，或$map[字段]=array('运算规则',值集合字符串); 
 * @return string|Ambigous <string, multitype:>  
 * @author quqiang 
 * @date 2015年6月17日 下午3:37:53 
 * @throws
 */
function getCurrentUserDataRight($type=1 , $key , $fieldName='' , $isFmt=true ){
	$isFmt=true;
	if(!$key ){
		return '';
	}
	$curAction = MODULE_NAME;
	// 数据权限过滤
	import ( '@.ORG.Browse' );
	$filterRight = Browse::getUserMap('' , 1);
	$ret = '';
	switch ($type){
		case 1:
			if(!$fieldName ){
				return '';
			}
			// 表
			if($filterRight[$curAction] && $filterRight[$curAction][$key]){
				$ret[$fieldName] = $filterRight[$curAction][$key][$fieldName];
				if($isFmt){
					$retStr = preg_replace("/\'/", '', $filterRight[$curAction][$key][$fieldName]);
					$ret[$fieldName] = array_unique( explode(',', $retStr) );
				}
				
				$ret[$fieldName] = $fieldName." in ('".join("','", $ret[$fieldName]) ."')";
			}
			// 读取表配置的继承权限
			if($filterRight[$curAction] && $filterRight[$curAction]['extend'] ){
				
				if($ret[$fieldName]){
					$ret[$fieldName] .= ' and '.$filterRight[$curAction]['extend'][$key];
				}else{
					$ret[$fieldName] = $filterRight[$curAction]['extend'][$key];
				}
				
				if($isFmt){
					$retStr = preg_replace("/\'/", '', $ret[$fieldName] );
					$ret[$fieldName] =$retStr;
				}
			}
			break;
		case 2:
			// 枚举
			$fieldName = $fieldName ? $fieldName : $key;
			if($filterRight[$curAction] && $filterRight[$curAction]['selectlist']){
				$ret[$fieldName] = $filterRight[$curAction]['selectlist'][$key];
				if($isFmt){
					$retStr = preg_replace("/\'/", '', $filterRight[$curAction]['selectlist'][$key]);
					$ret[$fieldName] = array_unique( explode(',', $retStr) );
				}
			}
			break;
		default:
			break;
	}
	return $ret[$fieldName];
	
// 	array_unique()
}
/**
 * 获取组件的html结构
 * @param enum $datatype 数据类型，允许值为 table , selectlist
 * @param array $parame	参数列表
 * @author 屈强
 * @data 13:44 2014-09-05
 */
function getControllbyHtml($datatype , $parame){
	
	
    // 默认初始值
    $defaultSetValue='-999999999';
	$datatypeArr = array('table'=>'table','selectlist'=>'selectlist');
	$type = $datatypeArr[$datatype];
	if(!$type)return "<error>未知数据来源：{$datatype},当前允许类型：table,selectlist</error>";
	$html='';
	switch ($type){
		case "table":
			// 参数说明：
			/*
			 * $parame['table']			查询表名
			 * $parame['id']			真实值字段名
			 * $parame['name']			显示值字段名
			 * $parame['type']			生成组件类型
			 * $parame['readonly']		组件是否只读
			 * $parame['conditions']	过滤条件
			 * $parame['selected']		当前选中值
			 * $parame['names']			组件name属性
			 * $parame['showtype']			值返回方式。
			 */
			$table	=	$parame['table']?$parame['table']:'';				//查询表名
			$valF 	=	$parame['id']?$parame['id']:'id';					//真实值字段名
			$textF 	=	$parame['name']?$parame['name']:'name';				//显示值字段名
			$tagType	=	$parame['type']?$parame['type']:'select';		//生成组件类型
			$readonly	=	$parame['readonly']?$parame['readonly']:false;	//组件是否只读
			$condition	=	$parame['conditions']?$parame['conditions']:'';	//过滤条件
			$tagSelected	=	$parame['selected']!=''?$parame['selected']:'-999999999';	//当前选中值
			$tagName	=	$parame['names']?$parame['names']:uniqid().'[]';//组件name属性
			$targevent	=	$parame['targevent']?$parame['targevent']:'';
			$showtype	=	$parame['showtype']	?'yes':'no';		//值返回方式。当值为no时 返回的是html,当为yes时返回的是真实值。
			// 下拉树 参数
			$comboxtree =	$parame['comboxtree'] ? 'yes' : 'no';							//	是否为树形
			$parentid		=	$parame['parentid'] ? $parame['parentid'] : 0;			// 树形的父级字段
			$defaultval	=	$parame['defaultval'] ? $parame['defaultval'] : 0;		// 默认显示值
			$defaulttext	=	$parame['defaulttext'] ? $parame['defaulttext'] : 0;	// 默认显示文本
			$isnextend	=	$parame['isnextend'] ? 'yes' : 'no';
			
			$sWhere = " status = 1 ";
			$condition=str_replace (array ('&quot;','&#39;','&lt;','&gt;'), array ('"', "'",'<','>'),$condition);
			if($condition) $sWhere .= " AND ".$condition;
			if(!$table)return "<error>未知的数据表</error>";
			if($comboxtree == 'no' && !$parentid){
				$mModel = M($table);
				
				$filterSouce = getCurrentUserDataRight(1 , $table  ,$valF ,true);
				if($filterSouce){
					$dataFilterMaps =$filterSouce ;// key($filterSouce)." in ('".join("','", reset($filterSouce))."')";
					// 在需要时可以在此处加上过滤条件格式验证，防止异常。
					
					$sWhere.=' and '.$dataFilterMaps;
				}
				
				$aList = $mModel->where($sWhere)->field($valF.','.$textF)->select();
				
				$sqlRet =<<<EOF
				
				<script>
				console.log("下拉/单、复选 查询条件:{$mModel->getLastSql()}");
				</script>
EOF;
				//echo $sqlRet;
			}else{
				$aList = array('');
			}
// 			var_dump($showtype);
// 			var_dump($parentid);
// 			var_dump($comboxtree);

			if($aList){
				switch ($tagType){
					case "select":
						
						if($showtype=='yes'){
							if($comboxtree == 'yes' && $parentid){
								$model=D("MisSystemRecursion");
								$model->__construct();
								if($dataFilterMaps){
									$condition .= ($condition ? ' and ' : '').$dataFilterMaps;
								}
								$treeSelectselect6Data = $model->modelShow($table , array('key'=>'id','pkey'=>$parentid,'conditions'=>$condition,'fields'=>"$valF,$textF") , 0 , 1);
								$treeSelectDataTemp='';
								if($defaulttext){
									array_unshift($treeSelectselect6Data, array($id=>$defaultval ,$name=>$defaulttext,$parentid=>0,'nextEnd'=>1));
								}
								foreach ($treeSelectselect6Data as $key => $value) {
									// id":1, "pId":0, "name":"基本元素"
									$tem='';
									$tem['id']=$value['id'];
									$tem['key'] = $value[$valF];
									$tem['pId']=$value[$parentid];
									$tem['name']=$value[$textF];
									if($isnextend=='yes' && $value['nextEnd']==0){
										$tem['chkDisabled']=true;
									}
									$treeSelectDataTemp[]=$tem;
								}
								$html = json_encode($treeSelectDataTemp);
							}else{
								if($tagSelected){
									$tagSelected= explode(',', $tagSelected);
								}
								$ret = '';
								foreach ($aList as $k => $v) {
									if(in_array($v[$valF], $tagSelected)){
										$ret[]= $v[$textF];
									}
								}
								$html = join(',', $ret);
							}
						}else{
							if(count($aList)<=5000){
								foreach ($aList as $k => $v) {
									$html .= '<option value="'.$v[$valF].'"';
									if($tagSelected != null && $tagSelected === $v[$valF]) $html .= ' selected="selected" ';
									$html .= '>'.$v[$textF].'</option>';
								};
							}else{
								$html .="<option value=''>内容超过5000条，请换成lookup组件显示</option>";
							}
						}
						break;
					case "checkbox":
						$index =0;
						$readonlyStr = !$readonly?"disabled=\"disabled\" ":'';
						if($tagSelected){
							$tagSelected= explode(',', $tagSelected);
						}
						if($showtype=='yes'){
							$ret = '';
							foreach ($aList as $k => $v) {
								if($tagSelected != null && $tagSelected == $v[$valF]){
									$ret = $v[$textF];
									break;
								}
							}
							$html = $ret;
						}else{
							foreach ($aList as $k => $v) {
								$html .= '<input '.$targevent.' dropback="xialayuan.value" type="checkbox" '.$readonlyStr.' id="'.str_replace('=', '', base64_encode($tagName)).$index.'" name="'.$tagName.'" value="'.$v[$valF].'"';
								if(in_array($v[$valF], $tagSelected)) $html .= ' checked="checked" ';
								$html .= '/><label class="tmp_label"  for="'.str_replace('=', '', base64_encode($tagName)).$index.'">'.$v[$textF].'</label>';
								$index++;
							};
						}
						break;
					case "radio":
						$index =0;
						$readonlyStr = $readonly?"readonly=\"readonly\" ":'';
						if($showtype=='yes' ){
							$ret = '';
							foreach ($aList as $k => $v) {
								if($tagSelected != null && $tagSelected == $v[$valF]){
									$ret = $v[$textF];
									break;
								}
							}
							$html = $ret;
						}else{
							foreach ($aList as $k => $v) {
								$html .= '<input  '.$targevent.' dropback="xialayuan.value" type="radio" '.$readonlyStr.' id="'.str_replace('=', '', base64_encode($tagName)).$index.'" name="'.$tagName.'" value="'.$v[$valF].'"';
								if(isset($tagSelected) && $tagSelected == $v[$valF]) $html.=' checked="checked" ';
								$html .= '/><label class="tmp_label"  for="'.str_replace('=', '', base64_encode($tagName)).$index.'">'.$v[$textF].'</label>';
								$index++;
							};
						}
						break;
				}
			}
			break;
		case "selectlist":
			// 参数说明：
			/*
			 * $parame['key']			数据key值
			 * $parame['type']			生成组件类型
			 * $parame['readonly']		组件是否只读
			 * $parame['names']			组件name属性
			 * $parame['selected']		当前选中值
			 */
			$list = require('./Dynamicconf/System/selectlist.inc.php');
			$key = $parame['key']?$parame['key']:'';							//数据key值
			$tagType = $parame['type'] ? $parame['type'] : 'select';			// 组件类型
			//$tagSelected 	= $parame['selected'] ? $parame['selected']:null;		//当前选中值
			$tagSelected	=	$parame['selected']!=''?$parame['selected']:'-999999999';	//当前选中值
			$tagName	=	$parame['names']?$parame['names']:uniqid();			//组件name属性
			$readonly = $parame["readonly"] ? $parame["readonly"]:false;		//组件是否只读
			$targevent=$parame['targevent']?$parame['targevent']:'';
			$showtype	=	$parame['showtype']	?'yes':'no';		//值返回方式。当值为no时 返回的是html,当为yes时返回的是真实值。
			
			if(!$key)return "<error>未知的selectlist.inc key值</error>";
			$list = $list[$key][$key];
			
			$filterSouce = getCurrentUserDataRight(2 , $key  ,'' ,true);
			if($filterSouce){
				$copyList = $list;
				unset($list);
				// 将允许的枚举key提取出来
				foreach (reset($filterSouce) as $key=>$val){
					$list[$val] = $copyList[$val];
				}
			}
			if($list){
				switch ($tagType) {
					case "select":
						if($showtype=='yes' ){
							if($list[$tagSelected]){
								$html = $list[$tagSelected];
							}else{
								$html = $tagSelected==$defaultSetValue  ? '' : $tagSelected ;
							}
						}else{
							foreach ($list as $k => $v) {
								$html .= '<option value="'.$k.'"';
								if(isset($tagSelected) && $tagSelected == $k) $html .= ' selected="selected" ';
								$html .= '>'.$v.'</option>';
							};
						}
						break;
					case "checkbox":
						$names = $tagName?$tagName:uniqid().'[]';
						$index =0;
						if($tagSelected){
							$tagSelected= explode(',', $tagSelected);
						}
						if($showtype=='yes'  ){
							if($list[$tagSelected]){
								$html = $list[$tagSelected];
							}else{
								$html = $tagSelected;
							}
						}else{
							foreach ($list as $k => $v) {
								$readonlyStr = !$readonly?"disabled=\"disabled\" ":'';
								$html .= '<input type="checkbox"  '.$targevent.' '.$readonlyStr.' id="'.str_replace('=', '', base64_encode($names)).$index.'" name="'.$names.'" value="'.$k.'"';
								if(in_array($k, $tagSelected))$html .= ' checked="checked" ';
								$html .= '/><label class="tmp_label"  for="'.str_replace('=', '', base64_encode($names)).$index.'">'.$v.'</label>';
								$index++;
							};
						}
						break;
					case "radio":
						if($showtype=='yes'  ){
							if($list[$tagSelected]){
								$html = $list[$tagSelected];
							}else{
								$html = $tagSelected;
							}
						}else{
							$radionames = $tagName?$tagName:uniqid();
							$index =0;
							$readonlyStr = $readonly?"readonly=\"readonly\" ":'';
							foreach ($list as $k => $v) {
								$html .= '<input type="radio"   '.$targevent.' '.$readonlyStr.' id="'.str_replace('=', '', base64_encode($radionames)).$index.'" name="'.$radionames.'" value="'.$k.'"';
								if(isset($tagSelected) && $tagSelected == $k) $html .= ' checked="checked" ';
								$html .= '/><label class="tmp_label"  for="'.str_replace('=', '', base64_encode($radionames)).$index.'">'.$v.'</label>';
								$index++;
							};
						}
						break;
				}
			}
			break;
	}
	return $html;
}
function strToArr($str){
	$encode = mb_detect_encoding($str, array("ASCII",'UTF-8',"GB2312","GBK","BIG5"));
	if(strtolower($encode)!='utf-8') iconv($encode, 'utf-8', $str);
	$arr=array();
	$a = '';
	if ($str!='') {
		if(ord($str)>128){
			$a +=  3;
			array_push($arr,mb_substr($str, 0,3));
			$str = mb_substr($str, 3);
		}else{
			$a +=  1;
			array_push($arr,mb_substr($str, 0,1));
			$str = mb_substr($str, 1);
		}
		$arr = array_merge($arr,strToArr($str));
	}
	return $arr;
}
//将已转变为单字数组的字符串按需要的字符数截取
function reStrToArr($arr,$length,$default){
	$a ='';
	$b = '';
	$last = '';
	$c = '';
	foreach ($arr as $k=>$v){
		if($a<$length){
			if(ord($v)>128){
				$a += 2;
			}else{
				$a += 1;
			}
			$b .= $v;
			$last = $k;
		}
	}
	if($k > $last){
		$b ='';
		foreach ($arr as $key=>$val){
			if($c<$length-strlen($default)){
				if(ord($val)>128){
					$c += 2;
				}else{
					$c += 1;
				}
				$b .= $val;
			}
		}
		$b .= $default;
	}
	return $b;
}
//截取适合html固定长度字符串
function subStrsToHtml($str,$length,$default='...'){
	$ext = explode('.',$str);
	$extname = end($ext);
	$extcount = strlen($extname)+1;
	$newstr = reStrToArr(strToArr(substr($str,0,strrpos($str, '.'))),$length-$extcount,$default);
	return $newstr.'.'.$extname;
}
/**
 * @Title: listToTree
 * @Description: todo(将数组转换为树形数组)
 * @author nbmxkj
 * @date 2014-10-13 下午 7:07:37
 * @param array $list 要转换的数据集
 * @param string $pk	主键字段名称
 * @param string $pid 	父级id字段名
 * @param string $child	子级数据的key名
 * @param string $root level标记字段,标识从哪一级开始遍历
 */
function listToTree($list, $pk='id',$pid = 'pid',$child = '_child',$root=0)
{
	// 创建Tree
	$tree = array();
	if(is_array($list)) {
		// 创建基于主键的数组引用
		$refer = array();
		foreach ($list as $key => $data) {
			$refer[$data[$pk]] =& $list[$key];
		}
		foreach ($list as $key => $data) {
			// 判断是否存在parent
			$parentId = $data[$pid];
			if ($root == $parentId) {
				$tree[] =& $list[$key];
			}else{
				if (isset($refer[$parentId])) {
					$parent =& $refer[$parentId];
					$parent[$child][] =& $list[$key];
				}
			}
		}
	}
	return $tree;
}

/**
 * MODEL名处理回调函数
 * @param $data
 */
function modelCallBackFunc($data){
	return ucfirst($data[1]);
}

/**
 * 生成真实的MODEL名称
 * @param string $modename	表名
 * @author nbmxkj
 * @date 2014-10-15 22:57
 */
function createRealModelName($modename){
	$modename = preg_replace_callback('/_([a-zA-Z])/', 'modelCallBackFunc', $modename);
	return ucfirst($modename);
}

/**
 * 生成地区信息的分组件结构数据。带缓存功能、
 * @param string $modename	表名
 * @author nbmxkj
 * @date 2014-10-31 13:26
 */
function getAreaGroupData(){

	$catchdata = F('areainfoData');
	if(is_array($catchdata)){
		return $catchdata;
	}else{
		$obj =D('MisSystemRecursion');
		$obj->__construct();
		$obj->modelShow('MisSystemAreas' , array('key'=>'id','pkey'=>'parentid','fields'=>'id,name,parentid') , 0 , 1);
		$data = $obj->getLevelGroup();
		$temp=array();
		foreach ($data as $key=>$val){
			switch ($key){
				case 0:
					$temp['sheng'] = $val;
					
				case 1:
					$temp['shi'] = $val;
				case 2:
					$temp['xian'] = $val;
				case 3:
					$temp['zhen'] = $val;
				case 4:
					$temp['xiang'] = $val;
			}
		}
		// var_dump($a);
		//logs($temp,'cach');
		try {
			F('areainfoData' , $temp , DATA_PATH);
		}catch(Exception $e){
			echo '<pre>'.$e->__toString().'</pre>';
		}
		
		logs($temp,'cach');
		
		return $temp;

		/*
		 $areaObj = M('MisSystemAreas');
		 // 省级
		 $sheng = $areaObj->field('areaid,name,parentid')->where('parentid=0')->select();
		 $shengIDArr = array();
		 foreach ($sheng as $k=>$v){
		 array_push($shengIDArr, $v['areaid']);
		 }
		 // 市级
		 $shi = $areaObj->field('areaid,name,parentid')->where("parentid in(".join(',', $shengIDArr).")")->select();
		 $shiIDArr = array();
		 foreach ($shi as $k=>$v){
		 array_push($shiIDArr, $v['areaid']);
		 }
		 // 县级
		 $xian = $areaObj->field('areaid,name,parentid')->where("parentid in(".join(',', $shiIDArr).")")->select();
		 $data['sheng'] = $sheng;
		 $data['shi'] = $shi;
		 $data['xian'] = $xian;
		 F('areainfoData' , $data , DATA_PATH);
		 return $data;
		 */
		/*
		 $obj = new MisSystemRecursionModel();
		 $data = $obj->modelShow('MisSystemAreas' , array('key'=>'areaid','pkey'=>'parentid','fields'=>'areaid,name,parentid') , 0 , 1);
		 F('areainfoData' , $data , DATA_PATH);
		 return $data;*/
	}


	//$model1=D('MisSystemRecursion');
	/*$model1 = new MisSystemRecursionModel();
	 $model1->modelShow('MisSystemAreas' , array('key'=>'areaid','pkey'=>'parentid','fields'=>'areaid,name,parentid') , 0 , 1);
	 $data = $model->getLevelGroup();
	 return $data;
	 */
}
/**
 * @Title: createTags
 * @Description: todo(生成html标签)
 * @param array $data  标签构成数据 例如：$data=>array('title'=>'类型','id'=>'ckbType','name'=>'type','type'=>'checkbox','value'=>'类型的值')
 * @param boolean $defaultchk	是否默认生成选中效果
 * @param string	$checkval	选中项
 * @author quqiang
 * @date 2014-11-5 上午09:54:45
 * @throws
 */
function createTags($data , $defaultchk=true,$checkval=''){
	/**
	 * $data=>array('title'=>'','id'=>'','name'=>'','type'=>'')
	 */
	$html='';
	if(is_array($data)){
		switch ($data['type']) {
			case 'checkbox':
				$html="<input type=\"{$data['type']}\" name=\"{$data['name']}\" id=\"{$data['id']}\" class=\"tmp_label\" value=\"{$data['value']}\" /><label for=\"{$data['id']}\">{$data['title']}</label>";
				break;
			case 'radio':
				$checked='';
				if($checkval){
					if($checkval == $data['value']){
						$checked = $data['checked']?'checked=""':'';
					}
				}else{
					if($defaultchk){
						$checked = $data['checked']?'checked=""':'';
					}
				}
				$html="<input type=\"{$data['type']}\" class=\"required \" name=\"{$data['name']}\" id=\"{$data['id']}\" value=\"{$data['value']}\" {$checked} /><label class=\"tmp_label\" for=\"{$data['id']}\">{$data['title']}</label>";
				break;
			default:
				break;
		}
	}
	return $html;
}
/**
 * @Title: logs
 * @Description: todo(日志生成函数)
 * @param string $msg		日志内容
 * @param string $filename 	文件名 。默认为当前日期
 * @param string $basepath	路径。默认为Runtime/Logs
 * @param string	$class
 * @param string	$function
 * @param string	$method
 * @param string	$contenttype 日志内容格式，line : 行，quto:块
 * @author quqiang
 * @example logs('内容','文件名','地址',__CLASS__,__FUNCTION__,__METHOD__);
 * @date 2014-11-13 下午02:29:12
 * @throws
 */
function logs($msg='' , $filename='' , $basepath='' , $class ='' ,  $function='' , $method='' , $contenttype='line'){
	if(!$filename){
		$filename = Date('Y-m-d',time());
	}
	if(!$basepath || !is_dir($basepath)){
		// $basepath = LOG_PATH; // 默认路径 修改前
		$basepath  = ROOT . '/Dynamicconf/FormLogs';
	}
	mk_dir($basepath);
	//$traces = debug_backtrace();  
	//$msg .= arr2string($traces);
	if(is_array($msg)){
		//$msg = json_encode($msg);
		$msg = arr2string($msg);
	}
	$quto = '┏━━━━━━━━━━━━━━━━━━'.chr(13).chr(10).
		'┣	IP：'.getip().chr(13).chr(10).
		'┣	请求对象:'.MODULE_NAME.chr(13).chr(10).
		'┣	UID		:'.$_SESSION[C('USER_AUTH_KEY')].chr(13).chr(10).
		'┣	NAME	:'.$_SESSION['loginUserName'].chr(13).chr(10).
		'┣	CLASS	:'.$class.chr(13).chr(10).
		'┣	METHOD	:'.$function.chr(13).chr(10).
		'┣	FUNCTION:'.$method.chr(13).chr(10).
		'┣	Time	:'.Date('Y-m-d H:i:s', time()).chr(13).chr(10).
		'┣ Note: '.$msg.chr(13).chr(10).
		'┗━━━━━━━━━━━━━━━━━━'.chr(13).chr(10);
	
	$line = 'Time '.Date('Y-m-d H:i:s', time()).' : '.
		$msg.
		chr(13).chr(10);
	
	$content = '';
	switch ($contenttype){
		case 'quto':
			$content =$quto;
			break;
		case 'line':
			$content = $line;
			break;
		default:
			$content = $line;
			break;
	}
	error_log(
	$content
	,3,$basepath.'/'.$filename.'.log');
	//error_log('执行SQL:'.$oprateSql.chr(16).chr(13).chr(10).Date('Y-m-d H:i:s', time()).chr(13).chr(10),3,ROOT.'/sql_err.log');
}
function obj2arr($obj){
	$_arr = is_object($obj)? get_object_vars($obj) :$obj;
	foreach ($_arr as $key => $val){
		$val=(is_array($val)) || is_object($val) ? obj2arr($val) :$val;
		$arr[$key] = $val;
	}
	return $arr;

}

/*
 * 除法函数
 * $arg1 除数
 * $arg2 被除数
 */
function accDiv($arg1,$arg2){

	$arg=$arg1/$arg2;
	if(!is_float($arg)){
		$arg=0;
	}
	return $arg;
}
/**
 *
 * @Title: getHrInfo
 * @Description: todo(查询多组织相关信息)
 * @param unknown $uid 当前用户 或查询用户
 * @param string $fieldName 取得那个字段
 * @param string $field1 条件字段
 * @param string $value1  条件值
 * @param string $typeid 1为主岗 2为辅岗
 * @return unknown
 * @author renling
 * @date 2014年12月1日 上午9:33:45
 * @throws
 */
function getHrInfo($uid,$fieldName,$field1='',$value1='',$typeid=''){
	$MisHrPersonnelUserDeptViewModel = D("MisHrPersonnelUserDeptView");
	$companyid="";
	$UserDeptDutyModel=D("UserDeptDuty");
	//查询当前session['companyid']
	$iscomMap['companyid']=$_SESSION['companyid'];
	$iscomMap['typeid']=1;
	$iscomMap['userid']=$uid;
	$iscomMap['status']=1;
	$iscomResult=$UserDeptDutyModel->where($iscomMap)->find();
	if($iscomResult){
		//查询用户存在于当前session记录的公司
		$companyid=$_SESSION['companyid'];
	}else{
		//取出查询用户第一个公司的id
		//清除公司id
		unset($iscomMap['companyid']);
		$isonecom=$UserDeptDutyModel->where($iscomMap)->find();
		$companyid=$isonecom['companyid'];
			
	}
	$map['companyid'] = $companyid;
	if($field1){
		//$var = "and '.$field11.'='.$value1.'";
		$map[$field1] = $value1;
	}
	$map['usersid']=$uid?$uid:$_SESSION[C('USER_AUTH_KEY')];
	$map['typeid']=$typeid?$typeid:1;//存在typeid值取typeid 默认为主岗
	$map['_string'] = "1=1";
	// 	"'.$key.'='.$value.' and 1=1 '.$var.'"
	$temp = $MisHrPersonnelUserDeptViewModel->where($map)->find();
	if($fieldName){
		$data=$temp[$fieldName];
	}else{
		$data[]=$temp;
	}
	return $data;
}
/*
 * 员工性别判断   员工档案在使用
 * 作者：刘典品
 * */
function getS($s){
	$sex = '';
	if($s==1){
		$sex = '男';
	}else{
		$sex = '女';
	}
	return $sex;
}
/**
 * @Title: arr2string 
 * @Description: todo(数组转字符串) 
 * @param array 	$input	需要转换的数组
 * @param string 	$t		
 * @author quqiang	
 * @date 2014-12-8 下午03:30:01 
 * @throws
 */
function arr2string($input,$t = null){
	$output = '';
	if (is_array($input))
	{
		$output .= "array(";
		foreach ($input as $key => $value)
		{
			$output .= $t."".arr2string($key,$t."\t").'=>'.  arr2string($value,$t."");
			$output .= ",";
		}
		$output .= $t.')';
	} elseif (is_string($input)) {
		$output .= "'".str_replace(array("\\","'"),array("\\\\","\'"),$input)."'";
	} elseif (is_int($input) || is_double($input)) {
		$output .= "'".(string)$input."'";
	} elseif (is_bool($input)) {
		$output .= $input ? 'true' : 'false';
	} else {
		$output .= 'NULL';
	}

	return $output;
}
/**
 * @Title: getProjectWorkFormObj
 * @Description: todo(任务节点模板名称转换) 
 * @param 类型 $formobj
 * @param 对象值 $objid  
 * @author 黎明刚
 * @date 2015年1月19日 下午5:34:14 
 * @throws
 */
function getProjectWorkFormObj($formobj,$objid){
	$name = "";
	if($formobj == 1){
		//附件
		$mis_system_attached_template_masDao = M("mis_system_attached_template_mas");
		$map['id'] = $objid;
		$name = $mis_system_attached_template_masDa->where($map)->getField("name");
	}else{
		//模板
		$nodeDao = M("node");
		$map['name'] = $objid;
		$name = $nodeDao->where($map)->getField("title");
	}
	return $name;
}
/**
 * 
 * @Title: getFieldTypeDisabled
 * @Description: todo(建模页面禁用部分字段类型选项) 
 * @param unknown $filedtype
 * @param unknown $infieldtype  
 * @author renling 
 * @date 2015年1月23日 下午4:23:13 
 * @throws
 */
function getFieldTypeDisabled($filedtype,$infieldtype){
	$disabled="";
	$str='disabled=disabled';
	switch ($filedtype){
		case 'varchar':
			if($infieldtype=="int"){
				$disabled=$str;
			}else if($infieldtype=="decimal"){
				$disabled=$str;
			}else if($infieldtype=="smallint"){
				$disabled=$str;
			}else if($infieldtype=="tinyint"){
				$disabled=$str;
			}else if($infieldtype=="date"){
				$disabled=$str;
			}
			break;
		case 'decimal':
			if($infieldtype=="int"){
				$disabled=$str;
			}else if($infieldtype=="smallint"){
				$disabled=$str;
			}else if($infieldtype=="tinyint"){
				$disabled=$str;
			}else if($infieldtype=="date"){
				$disabled=$str;
			}
			break;
		case 'text':
			if($infieldtype=="int"){
				$disabled=$str;
			}else if($infieldtype=="smallint"){
				$disabled=$str;
			}else if($infieldtype=="tinyint"){
				$disabled=$str;
			}else if($infieldtype=="date"){
				$disabled=$str;
			}
			break;
		default:
			 $disabled='';	
			 break;
	} 
	return $disabled;
}

/**
 * @Title: getModelFilterByNodeSetting
 * @Description: todo(在model中获取配置项)   
 * @author quqiang 
 * @date 2015年1月28日 下午8:15:00 
 * @throws
 */
function getModelFilterByNodeSetting($modelname){
	// 测试效果
	$userid = $_SESSION[C('USER_AUTH_KEY')];
	$actionName = $_SESSION['nodeActionName'.$userid];
	//$_SESSION['nodeActionName'.$userid]="";
	$id = $_SESSION['nodeAuditId'.$userid];
	//$_SESSION['nodeAuditId'.$userid] = "";
	// 模板目录
	$tplPath = C('DEFAULT_THEME');
	$tplPath = $tplPath?$tplPath.'/':'';
	// 导入样式名控制组件权限
	if($id && $actionName == $modelname){
		$path = "Tpl/{$tplPath}{$actionName}/right/{$id}.php";
		if(file_exists($path)){
			$data = require $path;
		}else{
			$data = array();
		}
	}else{
		$data = array();
	}
	return $data;
}
/**
 * @Title: getModelCssByNodeSetting
 * @Description: todo(获取节点对应的样式文件。)   
 * @author quqiang 
 * @date 2015年1月28日 下午8:49:46 
 * @throws
 */
function getModelCssByNodeSetting($actionName , $pageType){
	// 测试效果
	$id = $_GET['node'];
	$defaultCsssName = 'default';
	$nodeCssName ='';
	// 导入样式名控制组件权限
	if($id){
		$nodeCssName=$id;
		$css = <<<EOF
		
<link href="__TMPL__{$actionName}/{$nodeCssName}.css" rel="stylesheet" />
EOF;
	}else{
		$css = <<<EOF

<link href="__TMPL__{$actionName}/{$defaultCsssName}.css" rel="stylesheet" />
EOF;
	}
	return $css;
}


/**
 * @Title: getModelCssByNodeSetting
 * @Description: todo(获取节点对应的样式文件。)
 * @author quqiang
 * @date 2015年1月28日 下午8:49:46
 * @throws
 */
function getModelClassByNodeSetting($actionName , $pageType){
	// 测试效果
	$id = $_REQUEST['node'];
	// 模板目录
	$tplPath = C('DEFAULT_THEME');
	$tplPath = $tplPath?$tplPath.'/':'';
	//$id = 'node';
	if($pageType=='view'){
		$id.='View';
	}
	// 导入样式名控制组件权限
	if($id){
		$path = "Tpl/{$tplPath}{$actionName}/right/{$id}Class.php";
		if(file_exists($path)){
			$data = require $path;
		}else{
			$data = array();
		}
	}else{
		$data = array();
	}
	return $data;
}
/**
 * 附加条件的解析生成。
 * @Title: getAppendCondition
 * @Description: todo(传入一个数据源或者通过$_REQUEST获取值。) 
 * @param array $vo  		指定数据源
 * @param array		需要组合的字段列表 二维数组，key为当前字段，value为条件上使用的字段名。
 * @author quqiang 
 * @date 2015年1月31日 下午9:43:24 
 * @throws
 */
function getAppendCondition($vo ,  $fieldlist){
	// 得到系统字段
	
	if(!is_array($systemField)){
		$systemField='';
		$dir = CONF_PATH;
		$controlls =require $dir . 'controlls.php';
		require $dir . 'property.php';
		foreach ($SYSTEM_FIELD as $k=>$v){
			$systemField[]=$k;
		}
	}
	// 真实数据
	$dataSouce = array();
	// 字段解析列表
	$fieldArr=array();
	
	if(is_array($vo)){
		$dataSouce = $vo;
	}else{
		$dataSouce = $_REQUEST;
	}
	
	if($fieldlist){
		if(is_array($fieldlist)){
			$fieldArr = $fieldlist;
		}else{
			$fieldArr = explode( ',' , $fieldlist );
		}
	}
	// 条件解析
	$ret='';
	if($fieldArr && $dataSouce){
		foreach ($fieldArr as $k=>$v){
			if(!in_array($v, $systemField)){
				$ret[] = $v.'=&#39;'.$dataSouce[$k].'&#39;';
				// $ret[] = $v."='".$dataSouce[$k]."'";
			}else{
				if($dataSouce[$k]){
					$ret[] = $v.'=&#39;'.$dataSouce[$k].'&#39;';
					// $ret[] = $v."='".$dataSouce[$k]."'";
				}
			}
		}
	}
	if($ret){
		$conditionStr = join(' and ', $ret);
		return $conditionStr;
	}
}



/**
 * 将字段列表的值解析成Sql条件，如果字段取不到值，则直接不作条件对待
 * @Title: getAppendConditionExceptEmpty
 * @Description: todo(这里用一句话描述这个方法的作用) 
 * @param array $vo
 * @param array $fieldlist  
 * @author quqiang 
 * @date 2015年4月29日 下午5:06:49 
 * @throws
 */
function getAppendConditionExceptEmpty($vo , $fieldlist){
	// 真实数据
	$dataSouce = array();
	// 字段解析列表
	$fieldArr=array();
	
	if(is_array($vo)){
		$dataSouce = $vo;
	}else{
		$dataSouce = $_REQUEST;
	}
	
	if($fieldlist){
		if(is_array($fieldlist)){
			$fieldArr = $fieldlist;
		}else{
			$fieldArr = explode( ',' , $fieldlist );
		}
	}
	// 条件解析
	$ret='';
	if($fieldArr && $dataSouce){
		foreach ($fieldArr as $k=>$v){
			if($dataSouce[$k]){
				$ret[] = $v.'=&#39;'.$dataSouce[$k].'&#39;';
			}
		}
	}
	if($ret){
		$conditionStr = join(' and ', $ret);
		return $conditionStr;
	}else{
		return '1=1';
	}
}
/**
 * 替换字段串中的值
 * @Title: parameReplace
 * @Description: todo(替换函数) 
 * @param string		$search	查找字段
 * @param string		$replace	替换字段
 * @param string		$subject	查找对象
 * @return mixed  
 * @author quqiang 
 * @date 2015年2月1日 下午1:49:34 
 * @throws
 */
function parameReplace($search , $replace , $subject){
	return str_replace($search, $replace, $subject);
}


/*
 * @author  王昭侠
* @date:2015-02-06 13:55:00
* @describe: 金额转大写
*/
function upperMoney($ns) {
	if($ns===NULL || $ns===""){
		return "";
	}
	if((float)$ns==0){
		return "零元";
	}
	static $cnums=array("零","壹","贰","叁","肆","伍","陆","柒","捌","玖"),
	$cnyunits=array("元","角","分"),
	$grees=array("拾","佰","仟","万","拾","佰","仟","亿");
	list($ns1,$ns2)=explode(".",$ns,2);
	$ns2=array_filter(array($ns2[1],$ns2[0]));
	$ret=array_merge($ns2,array(implode("",_cny_map_unit(str_split($ns1),$grees)),""));
	$ret=implode("",array_reverse(_cny_map_unit($ret,$cnyunits)));
	$str = str_replace(array_keys($cnums),$cnums,$ret);
	$x=explode(".",$ns);
	if(empty($x[1]) || (! empty($x[1]) &&  ((int)$x[1])==0)){
		$str =$str."整";
	}
	$str = str_replace("零零","零",$str);
	return $str;
}

function _cny_map_unit($list,$units) {
	$ul=count($units);
	$xs=array();
	foreach (array_reverse($list) as $x) {
		$l=count($xs);
		if ($x!="0" || !($l%4)) $n=($x=='0'?'':$x).($units[($l-1)%$ul]);
		else $n=is_numeric($xs[0][0])?$x:'';
		array_unshift($xs,$n);
	}
	return $xs;
}
/**
 * 
 * @Title: getBindTabsContent
 * @Description: todo(生成表单选项卡头部) 
 * @param unknown $actionname 模型名称
 * @param unknown $type  头部为1 尾部为2
 * @param unknown $vo 修改数据vo 
 * @param unknown $type	操作类型 add  edit view
 * @param unknown $isactiontabs 选项卡是否带自己
 * @return string  
 * @author renling 
 * @date 2015年2月12日 下午2:51:24 
 * @throws
 */
function getBindTabsContent($actionname,$vo,$type,$isactiontabs,$main){
	try{
		$main = empty($main)?$_GET['main']:$main;
		$randUniqueTag = getMillisecond();
	$list=array();	
	// 定义返回内容数据
	// [0] => '验证函数名'
	// [1] => '函数内容 ， 当前调用系统函数时为空'
	// [2] => '切换头'
	// [3] => '切换尾'
	$ret = array();
	//查询当前表单所属类型
	//组合、主从
	$MisAutoBindModel=D("MisAutoBind");
	//套表
	$MisAutoBindSettableModel=D("MisAutoBindSettable");
	$MisSystemDataRoamSubDao=M("mis_system_data_roam_sub");
	//数据漫游
	$MisSystemDataRoamingModel=D("MisSystemDataRoaming");
	$MisDynamicFormProperyModel=D("MisDynamicFormPropery");
	$MisProjectFlowFormModel=D("MisProjectFlowForm");
	$DynamicconfModel=D("Dynamicconf");
	$html='';
	$temp_total="";
	$funcitonname=$actionname."addnavTabDone";
	$mainBindActionCondition = '';
	$dataroamingCondition='';
	$inbindid=0;
	$nodeModel=D("Node");
	$nodeList=$nodeModel->where("status=1")->getField("name,title");
	//是否新增也带选项卡
	$isaddContent="";
	if($main){
		$mainBindActionCondition='/main/'.$main;
	}else{
		// 判断当前表单是否为关系统表单的入口
// 		$MainbindMap=array();
// 		$MainbindMap['bindaname'] = $actionname;
// 		$MainbindMap['pid'] = 0;
// 		$isMainAction = $MisAutoBindModel->where($MainbindMap)->count();
// 		$MainbindsetMap=array();
// 		$MainbindsetMap['bindaname']= $actionname;
// 		$MainbindsetMap['pid'] = 0;
// 		$isMainbindsetAction = $MisAutoBindSettableModel->where($MainbindsetMap)->count();
		$isMainAction=checkIsRelationForm($actionname,1);
		if($isMainAction){
			$mainBindActionCondition='/main/'.$actionname;
		}
	}
	//查看界面在选项卡上多个样式
	if($type=="view"){
		$temp_total="temp_total";
	}
	$actiontitle=$nodeList[$actionname];
	//getFieldBy($actionname, "name", "title", "node");
	//定义选项卡头部
	$content= <<<EOF
	 <div  id="tabsContent_{$randUniqueTag}"  class="tabs proTabNav nbm_relation_form_tabs_navi" eventtype="click" currentindex="0">
					<div class="tabsHeader proNavHeader">
						<div class="tabsHeaderContent proTabNavHeaderContent {$temp_total}">
							<ul>
EOF;
	//当前主表自己
	$actiontabs="<li class=\"selected\"><a href=\"javascript:;\"><span>{$actiontitle}</span></a></li>";
	//如果需带自己 追加至选项卡头部
	if($isactiontabs){
		$content.=$actiontabs;
	}
	$footer= <<<EOF
					</ul>
				</div>
			</div>
		<div  class="tabsContent tml-p0">
EOF;
	//查询当前表单是否为套表的主表
	if(getFieldBy($actionname, "bindaname", "inbindaname", "MisAutoBind","typeid",2)){
		//套表新增需要带选项卡
		$isaddContent=1;
		//拼装套表js
		$jshtml= <<<EOF
		 <script>
function {$funcitonname}(json) {
	DWZ.ajaxDone(json);
		if (json.statusCode == DWZ.statusCode.ok) {
			var id = json.data.id;
			var params = [{
				name : 'id',
				value : id
			}];
			if(id){
				navTab.closeCurrentTab();
				var tabids = "__MODULE__edit";
				var urls = "__URL__/edit/id/"+id;
				var titles = "{$actiontitle}";
				var postdata =params;
				navTab.openTab(tabids, urls, {title : titles,fresh : true});
			}
			return false;
		}
	}
</script>
EOF;
		
		//获取当前控制器中文名称
		$actiontitle=$nodeList[$actionname];
		//getFieldBy($actionname,"actionname","actiontitle", "mis_dynamic_form_manage");
		//查询绑定关系表
		$MisAutoBindSettableModel=D("MisAutoBindSettable");
		$map=array();
		$map['status']=1;
		//查询当前模型绑定字段
		$map['bindaname']=$actionname;
		$map['typeid']=2;
		logs($vo,"BindCommonArr");
		if(!$vo||$type=="add"){
			//新增强制排除id 否则漫游取值出错
			if($vo['id'])
				unset($vo['id']);
			logs("我是3895","BindCommon");
			//获取
			$flowMap=array();
			$flowMap['projectid']=$_REQUEST['projectid'];
			$flowMap['formobj']=$actionname;
			$MisProjectFlowFormList=$MisProjectFlowFormModel->where($flowMap)->find();
			if($MisProjectFlowFormList){
				//查询真实节点返回数据
				$soureModel=D($MisProjectFlowFormList['sourcemodel']);
				$soureMap=array();
				$soureMap['status']=1;
				$soureMap['projectid']=$_REQUEST['projectid']; 
				$soureVo=$soureModel->where($soureMap)->find();
				//根据去查询漫游 不是标准套表漫游
				$dataArr=$MisSystemDataRoamingModel->main(1,$MisProjectFlowFormList['sourcemodel'],$soureVo['id'],4,$actionname);
				foreach($dataArr as $targettable=>$data){
					foreach($data as $k => $v){
						foreach($v as $k1 => $v1){
							if(reset($v1)){
								$vo[key($v1)] = reset($v1);
							}
							
						}
					}
				}
				logs($dataArr,"databindArr");
				logs($vo,"databindArrVo");
				$vo['projectworkid']=$MisProjectFlowFormList['id'];
			}
			$updateBackup=setOldDataToCache($actionname,$vo);
			$updateBackupList=str_replace('=', '', base64_encode(serialize($updateBackup)));
			$dataroamingCondition='/dataromaing/'.$updateBackupList;
		}  
		logs("我是3923","BindCommon");
		$MisAutoBindSettableList=$MisAutoBindSettableModel->where($map)->order("inbindsort asc")->select();
		//查询满足条件的表单类型
		foreach ($MisAutoBindSettableList as $key=>$val){
			if($val['bindtype']==0){ 
				$model=D($val['inbindaname']);
				//获取真实表名
				$tablename=$model->getTableName();
				//$tname=$model->getTableName();
				$bindmap=array();
				$bindmap[$val['inbindval']]=$vo[$val['bindval']];
				$bindmap['status']=1;
				//当前如果需要复制数据
				if($val['iscopy']==1){
					$bindmap['iscopy']=1; //过滤数据 1为可复制
					$bindmap['_string']="`xiangmubianma`  IS NULL || xiangmubianma=''";//强制加复制时没有项目编码
					$resetVo=$model->where($bindmap)->order("id desc")->find();
					//查询复制数据源
					logs("进行查找数据==={$model->getLastSql()}","BindSourceResult");
						if($resetVo){
							$data=array();
							//加上数据漫游条件
							$MisSystemDataRoamSubList=$MisSystemDataRoamSubDao->where("masid=".$val['dataroamid']." and targettable='".$tablename."'")->select();
							if($MisSystemDataRoamSubList){
								foreach ($MisSystemDataRoamSubList as $dskey=>$dsval){
									if($vo[$dsval['sfield']]){
										$bindmap[$dsval['tfield']]=$vo[$dsval['sfield']];
										$data[$dsval['tfield']]=$vo[$dsval['sfield']];
									}
									if($dsval['expdo']==4 && $dsval['expremark']){
										$data[$dsval['tfield']]=$dsval['expremark'];
									}
								}
							}
							unset($bindmap['iscopy']);
							unset($bindmap['_string']);
							//查询绑定的附加条件
							$MisAtuoMap=array();
							$MisAtuoMap['bindaname']=$actionname;
							$MisAtuoMap['inbindaname']=$val['inbindaname'];
							$MisAutoBindSettableVo=$MisAutoBindSettableModel->where($MisAtuoMap)->find();
							$bindconlistArr=unserialize($MisAutoBindSettableVo['bindconlistArr']);
							foreach ($bindconlistArr as $bindkey=>$bindval){
								//如果有值才生成查询
								if($val){
									if($vo[$bindkey]){
										$bindmap[$bindval]=$vo[$bindkey];
									}
								}
							}
							$soureVo=$model->where($bindmap)->order("id desc")->find();
							logs("进行查找数据==={$model->getLastSql()}","BindResult");
							if(!$soureVo){
								$oldresultid=$resetVo['id'];
								//清除编号
								unset($resetVo['id']);
								unset($resetVo['orderno']);
								unset($_REQUEST['id']);
								if($data){
									$resetVo['orderno']=getTabsOrderno($tablename,$val['inbindaname']);
									$addate=array();
									 $alldata=array_merge($data,$_REQUEST);
									 $addate=array_merge($resetVo,$alldata);
									 //强制复制系统字段
									 $addate['companyid']=$_SESSION['companyid'];//获取当前公司id
									 $addate['createid']=$_SESSION[C('USER_AUTH_KEY')];//获取创建人id
									 $addate['createtime']=time();//获取创建时间
									 $addate['updateid']="";
									 $addate['updatetime']="";
									 $addate['operateid']=0;
									 $addate['iscopy']="";
									 $addate['allnode']=$actionname;
								}
								if($addate){
									$resultid=$model->add($addate); 
									$model->commit();
									logs("进行复制数据==={$model->getLastSql()}","BindCopy");
									if($resultid){
										//订单号是否可写
										//查询当前表单的组件列表 是否有内嵌表格
										$ProperyMap=array();
										$ProperyMap['formid']=$val['inbindformid'];
										$ProperyMap['status']=1;
										$ProperyMap['category']="datatable";
										$MisDynamicFormProperyList=$MisDynamicFormProperyModel->where($ProperyMap)->field("dbname,fieldname,id")->select();
										if($MisDynamicFormProperyList){
											foreach ($MisDynamicFormProperyList as $pkey=>$pval){
												//循环查找满足条件的数据表格
												$tablename=$pval['dbname']."_sub_".$pval['fieldname'];
												$tmodel=D($tablename);
												$tmodel->startTrans();
												$list=$tmodel->where("masid=".$oldresultid)->select();
												foreach ($list as $dkey=>$dval){
													$dval['masid']=$resultid;
													unset($dval['id']);
													$tmodel->add($dval); 
												}
												$tmodel->commit();
											}
										  }
										}
									} 
								$inbindid['id']=$resultid?$resultid:0;
							}
						else{
							$inbindid=$soureVo?$soureVo:0;
						}
					}
				}else{ 
					
					//插入漫游数据进行查询 如果目标数据为空不单独处理
					$dataArr=$MisSystemDataRoamingModel->main(2,$actionname,$vo['id'],4,$val['inbindaname'],'',$val['dataroamid']);
// 					//加上数据漫游条件
// 					$MisSystemDataRoamSubList=$MisSystemDataRoamSubDao->where("masid=".$val['dataroamid']." and targettable='".$tablename."'")->select();
// 					if($MisSystemDataRoamSubList){
// 						foreach ($MisSystemDataRoamSubList as $dskey=>$dsval){
// 							if($vo[$dsval['sfield']]){
// 								$bindmap[$dsval['tfield']]=$vo[$dsval['sfield']];
// 							}
// 						}
// 					}
					if(is_array(reset($dataArr))){
						foreach(reset($dataArr) as $targettable=>$data){
							foreach($data as $k => $v){
								if(reset($v)){
									$bindmap[key($v)]=reset($v);
								}
							}
						}
					}
					unset($bindmap['iscopy']);
					unset($bindmap['xiangmubianma']);
					//查询绑定的附加条件
// 					$MisAtuoMap=array();
// 					$MisAtuoMap['bindaname']=$actionname;
// 					$MisAtuoMap['inbindaname']=$val['inbindaname'];
// 					$MisAutoBindSettableVo=$MisAutoBindSettableModel->where($MisAtuoMap)->find();
					if($val['inbindmap']){
						$newconditions = str_replace ( array ( '&quot;', '&#39;', '&lt;','&gt;'), array ('"',"'",'<','>'
						), $val['inbindmap'] );
						$bindmap['_string']=$newconditions;
					}
					$bindconlistArr=unserialize($val['bindconlistArr']);
					//获取表单子表附加 
					foreach ($bindconlistArr as $bindkey=>$bindval){
						//如果有值才生成查询
						if($vo[$bindkey]){
							$bindmap[$bindval]=$vo[$bindkey];
						}
					}
					$inbindid=$model->where($bindmap)->field("id")->order("id desc")->find();
					logs($val['inbindaname']."==".$model->getlastsql(),"bindtabssql");
				}
				if($vo[$val['bindval']]){
					$MisAutoBindSettableList[$key]['inbindid']=$inbindid['id'];
				}
			}
		}
		if($MisAutoBindSettableList){
			//重新对数据进行排序
			$html.=$content;
		}
		if(!$vo['id']){
			$vo['id']=0;
		}
		//开始组合套表选项卡	
		foreach ($MisAutoBindSettableList as $mkey=>$mval){
			$inbindtitle=$nodeList[$mval['inbindaname']];
			//getFieldBy($mval['inbindaname'], "actionname", "actiontitle", "mis_dynamic_form_manage");
			if($mval['inbindtitle']){
				$inbindtitle=$mval['inbindtitle'];
			}
			$idTempValue = intval($mval['id']);
			$typid = intval($mval['bindtype']);
			if(($type=="edit"||$type=="add")&&$mval['formshowtype']!='1'){
				$html.="<li><a id=\"{$mval['inbindaname']}_{$randUniqueTag}\" ";
				$inbindval=$vo[$mval['bindval']]?$vo[$mval['bindval']]:"#";
					if($typid >=1){//miniindex
						$html.="href=\"__APP__/{$mval['inbindaname']}/miniindex/func/{$type}/bindtype/{$mval['bindtype']}/bindaname/{$mval['bindaname']}{$mainBindActionCondition}/fieldtype/{$mval['inbindval']}/{$mval['inbindval']}/{$inbindval}/bindrdid/{$vo['id']}{$dataroamingCondition}";
					}else{
						if($mval['inbindid']){ //修改表单
							$html.="href=\"__APP__/{$mval['inbindaname']}/edit{$mainBindActionCondition}/fieldtype/{$mval['inbindval']}/{$mval['inbindval']}/{$vo[$mval['bindval']]}/bindrdid/{$vo['id']}/bindaname/{$mval['bindaname']}/id/{$mval['inbindid']}";
						}else{//新增表单
							$html.="href=\"__APP__/{$mval['inbindaname']}/add{$mainBindActionCondition}/bindaname/{$mval['bindaname']}/fieldtype/{$mval['inbindval']}/bindrdid/{$vo['id']}/{$mval['inbindval']}/{$vo[$mval['bindval']]}{$dataroamingCondition}";
						}
					}
					$html.="\" rel=\"{$mval['inbindaname']}_edit\" class=\"j-ajax\"><span>".$inbindtitle."</span></a></li>";
				}else if($type=="view"||$mval['formshowtype']==1){
					$html.="<li><a id=\"{$mval['inbindaname']}_{$randUniqueTag}\" ";
					if($typid>=1){//miniindex
						$html.="href=\"__APP__/{$mval['inbindaname']}/miniindex/func/{$type}/bindrdid/{$vo['id']}/minitype/1/viewtype/view/bindtype/{$mval['bindtype']}/bindaname/{$mval['bindaname']}/fieldtype/{$mval['inbindval']}/{$mval['inbindval']}/{$vo[$mval['bindval']]}";
					}else{
						$html.="href=\"__APP__/{$mval['inbindaname']}/view/bindrdid/{$vo['id']}/bindaname/{$mval['bindaname']}/id/{$mval['inbindid']}";
					}
					$html.="\" rel=\"{$mval['bindaname']}{$mval['inbindaname']}_edit\" class=\"j-ajax\"><span>".$inbindtitle."</span></a></li>";
				}
// 				}else{ //新增页面
// 					$html.="<span class=\"not_select\">";
// 					$html.="<a href=\"javascript:;\" onclick=\"alertMsg.info('请先保存主表信息！')\">";
// 					$html.="<span>".getFieldBy($mval['inbindaname'], "actionname", "actiontitle", "mis_dynamic_form_manage")."</span></a>";
// 					$html.="</span>";
// 				}
			}
			$html.=$footer;
	}else if(getFieldBy($actionname, "bindaname", "inbindaname", "MisAutoBind")){
		//拼装套表js
		$jshtml= <<<EOF
		<script>
		function {$funcitonname}(json) {
			DWZ.ajaxDone(json);
			if (json.statusCode == DWZ.statusCode.ok) {
				var bindid = json.data.bindid;
				var id=json.data.id;
				var params = [{
					name : 'bindid',
					value : bindid
				},{
					name : 'id',
					value : id
				}];
				if(bindid){
					navTab.closeCurrentTab();
					var tabids = "__MODULE__edit";
					var urls = "__URL__/edit/bindid/"+bindid+"/id/"+id;
					var titles = "{$actiontitle}";
					var postdata =params;
					navTab.openTab(tabids, urls, {title : titles,fresh : true,data:postdata});
				}
				return false;
			}
		}
		</script>
EOF;
		$html.=$content;
		$iszctpl="";
		$udataroamid="";
		//判断是否是组从表单
		$iszctpl=getFieldBy($actionname, "bindaname", "inbindaname", "MisAutoBind","typeid",1);
		if(!$iszctpl&&!$main){
			$iszhtpl=3;
		}
		if($type=="edit" ||$type=="view"||$iszctpl||$iszhtpl){
			$MisAutoBindModel=D("MisAutoBind");
			$map=array();
			$map['status']=1;
			$isaddContent=$iszctpl;
			if(!$iszctpl){//组合表单
				//获取绑定数组源 
				$bindid=getFieldBy($vo['id'], "id", "orderno", $actionname);  
				// 查询符合条件的表单
				$MisAutoBindVo=$MisAutoBindModel->where("status=1 and bindaname='{$actionname}'  and bindresult<>'' and typeid=0")->field("bindresult")->find();
				// 过滤掉可能的错误。
				$bindCondition = getFieldBy($bindid,"orderno",$MisAutoBindVo['bindresult'],$actionname);  
				if(isset($bindCondition)){
					$bindMap['_string']="bindval={$bindCondition} or bindval='all'";
				}
				$bindMap['status'] = 1;
				$bindMap['bindaname'] = $actionname;
				$MisAutoBindSettableList = $MisAutoBindModel->where($bindMap)->order("inbindsort asc")->getField("id,inbindaname,bindtype,bindaname,dataroamid,inbindtitle,inbindsort,formshowtype,inbindmap,bindconlistArr");
			}else{
				$bindid=$vo['id'];
				// 查询符合条件的表单
				$MisAutoBindSettableList=$MisAutoBindModel->where("status=1 and bindaname='{$actionname}'  and typeid=1")->order("inbindsort asc")->select();
			}  
			$MisSystemDataRoamingModel=D("MisSystemDataRoaming");
			//开启事物
			$MisSystemDataRoamingModel->startTrans();
				foreach($MisAutoBindSettableList as $key =>$val) {
					$map = array();
					$map['status'] = 1;
					//组从表单需要此条件
					if($iszctpl){
						//只需组从表区分
						$map['bindid'] = $bindid;
						$map['relationmodelname'] = $actionname;
					}
					if ($val['bindtype'] == 0) {
						//保存漫游id
						$MisAutoBindSettableList[$key]['urdataroamid']=$val['dataroamid'];
						$model = D($val['inbindaname']);
						//插入漫游数据进行查询 如果目标数据为空不单独处理
						$dataArr=$MisSystemDataRoamingModel->main(2,$actionname,$vo['id'],4,$val['inbindaname'],'',$val['dataroamid']);
						logs($val['inbindaname'].$val['dataroamid'],"zuhebindadddater");
						logs($dataArr,"zuhebindaddArr");
						$date=array();
						if(is_array(reset($dataArr))){
							foreach(reset($dataArr) as $targettable=>$data){
								foreach($data as $k => $v){
									if(reset($v)){
										$date[key($v)] = reset($v);
										//$map[key($v)]=reset($v);
									}
								}
							}
						}
						if($val['inbindmap']){
							$newconditions = str_replace ( array ( '&quot;', '&#39;', '&lt;','&gt;'), array ('"',"'",'<','>'
							), $val['inbindmap'] );
							$map['_string']=$newconditions;
						}
						$bindconlistArr=unserialize($val['bindconlistArr']);
						//获取表单子表附加
						foreach ($bindconlistArr as $bindkey=>$bindval){
							//如果有值才生成查询
							if($vo[$bindkey]){
								$map[$bindval]=$vo[$bindkey];
							}
						}
						$bindList = $model->where($map)->order('id desc')->find(); 
						logs($val['inbindaname']."====".$model->getlastsql(),"zuhebindaddsql");
						$MisAutoBindSettableList[$key]['id'] = $bindList['id'];
						//不存在数据且页面也是修改则执行新增数据 只增对于套用修改
						if(!$MisAutoBindSettableList[$key]['id'] && $type=="edit"&&$MisAutoBindSettableList[$key]['formshowtype']==0){ 
							if(!$iszctpl){
								$date['bindid']=$bindid;
								$date['relationmodelname']=$actionname;
							} 
							unset($reuslt);
							$reuslt=$model->add($date); 
							//$model->commit();
							logs("=====".$reuslt."====".$model->getlastsql(),"bindtype2");
							if(!$reuslt){
								$lastID = $model->query('SELECT LAST_INSERT_ID() as returnval');
								$reuslt = $lastID[0]['returnval'];
							}
							$MisAutoBindSettableList[$key]['id'] =$reuslt;
						}else if(!$MisAutoBindSettableList[$key]['id'] && $type=="add"){
							//为新增页面赋初始值
							$MisAutoBindSettableList[$key]['id']=0;
						}
				}else{
					//保存漫游id
					$MisAutoBindSettableList[$key]['urdataroamid']=$val['dataroamid'];
					
				}
			}
			//提交事物
			$MisSystemDataRoamingModel->commit();
			foreach ($MisAutoBindSettableList as $makey=>$maval){ 
				$inbindtitle=getFieldBy($maval['inbindaname'], "actionname", "actiontitle", "mis_dynamic_form_manage");
				if($maval['inbindtitle']){
					$inbindtitle=$maval['inbindtitle'];
				}
				$idTempValue = intval($maval['id']) ?  intval($maval['id']) : 0 ; 
				$typid = intval($maval['bindtype']);
				$html.="<li><a id=\"{$maval['inbindaname']}_{$randUniqueTag}\" ";
				if($type=="edit" && $maval['formshowtype']!=1){ 
					if( $typid >=1){
						$html.="href=\"__APP__/{$maval['inbindaname']}/miniindex/func/{$type}/bindaname/{$actionname}/bindtype/{$maval['bindtype']}{$mainBindActionCondition}/bindrdid/{$vo['id']}/bindid/{$bindid}/urdataroamid/{$maval['urdataroamid']}\"";
					}else{
						unset($tempMap);
						unset($tempModel);
						unset($tempData);
						$tempMap['bindid'] = $bindid;
						$tempMap['relationmodelname'] = $actionname;
						$tempModel = D($maval['inbindaname']);
						$tempData =$tempModel->where($tempMap)->find();
						$html.="href=\"__APP__/{$maval['inbindaname']}/edit{$mainBindActionCondition}/bindid/{$bindid}/bindrdid/{$vo['id']}/urdataroamid/{$maval['urdataroamid']}/id/".$idTempValue."\"";
						
					}
				}else if($type=="view" || $maval['formshowtype']==1){
					if( $typid >=1){
						$html.="href=\"__APP__/{$maval['inbindaname']}/miniindex/viewtype/view/bindrdid/{$vo['id']}/minitype/1/bindaname/{$actionname}/bindtype/{$maval['bindtype']}{$mainBindActionCondition}/bindid/{$bindid}/urdataroamid/{$maval['urdataroamid']}\"";
					}else{
						$html.="href=\"__APP__/{$maval['inbindaname']}/view/viewtype/view/id/{$maval['id']}/bindrdid/{$vo['id']}{$mainBindActionCondition}/urdataroamid/{$maval['urdataroamid']}\"";
					}
				} 
				$html.=" rel=\"{$maval['bindaname']}{$maval['inbindaname']}_edit\" class=\"j-ajax\">";
				$html.="<span>".$inbindtitle."</span></a>";
				}
			}
			$html.=$footer;
		}else{
			$html='';
		}
// 		if($type!="view"&&!getFieldBy($actionname, "bindaname", "inbindaname", "MisAutoBind")){
// 			$foothtml="</div>";
// 		}
		if($type=="edit" ||$type=="view"||$isaddContent){
			foreach ($MisAutoBindSettableList as $fkey=>$fval){
				if($fval['inbindaname']){
					$inbindmodel=$fval['bindaname'].$fval['inbindaname']; // nnnnnnnnnn
				}else{
					$inbindmodel=$fval['bindaname'].$fval['inbindaname'];
				}
				$foothtml.="<div id=\"{$inbindmodel}_edit\"></div>";
			}
		}
		if($MisAutoBindSettableList){
			$foothtml.="</div></div>";
		}
		//页面原始按钮处理    
		$buttonHtml="";
		$endfuncitonname="return validateCallback(this, navTabAjaxDoneNoDateFlush)";
		if($html||$main){
			if((checkActionIsMain($actionname))&&$type=="add"){ 
				$buttonHtml=1;
				if($iszctpl||$iszhtpl){
					$endfuncitonname="return validateCallback(this, {$funcitonname})";
				}else{
					$endfuncitonname="return validateCallback(this, navTabAjaxDoneNoDateFlush)";
				}
			}else{
				//修改刷新函数
				$endfuncitonname="return validateCallback(this, navTabAjaxDoneNoFlush)";
				$jshtml="";
				$buttonHtml=1;
			}
		}
		if($_REQUEST['bindtype']==3){
			$endfuncitonname="return validateCallback(this, dialogAjaxDone)";
		}
		$reflushChildLevelTabs = '';
		if($type=="view"||$type=="edit"){
			if($_GET['viewtype']){
				$type = 'view';
			}else{
				$type = 'edit';
			}
			//查询该表的数据源字段
			$tvalFiled=getFieldBy($actionname, "bindaname", "bindresult", "mis_auto_bind","typeid","0");
			$tbtvalFiled=getFieldBy($actionname, "bindaname", "bindval", "mis_auto_bind","typeid","2");
				
			$reflushChildLevelTabs = <<<EOF
	<script>
	$(function(){
		$(".data_table_{$randUniqueTag} tbody tr").die("click");
		$(".data_table_{$randUniqueTag} td").die("click");
		$(".data_table_{$randUniqueTag} tbody tr").live("click",function(){
			if($(this).find(".list_group_lay").length==0){
				var tag = $(this).closest('tbody').attr('tabfor'); 
				console.log(tag);
				var controll = 'orderno';
				// 得到指定的td对象
				var ret = $(this).find('td.'+controll);//.data(controll);
				var bindrdid=$(this).find("input[name*='[id]']").val();
				// 得到值
				var tval = ret.attr('content');
				var controllFiled = '{$tvalFiled}'; 
				var tbtvalFiled='{$tbtvalFiled}';
				if(controllFiled){
					var retFiled = $(this).find('td.'+controllFiled);//.data(controll);
				}else{
					var retFiled = $(this).find('td.'+tbtvalFiled);//.data(controll);	
				}
				var tvalFiled = retFiled.attr('content');
				$.ajax({
					url:TP_APP+'/Common/getAutoFormTabsForAll',
					data:{'action':'{$actionname}','main':'{$main}','val':tval,'filedval':tvalFiled,'formtype':'{$type}','bindrdid':bindrdid},
					type:'post',
					dataType:'json',
					success:function(msg){ 
						if(isNullorEmpty(msg.data)){
						console.log(msg.data);
    						 if(isNullorEmpty(tag)){
							$(".appendcontent").show();
    							var obj = $('#tabsContent_'+tag);
    							var options={};
    							$.each(msg.data , function(k , v){
    								var temp = v.replace(/#tagitem#/,tag);
    								if(isNullorEmpty(obj)){
    									obj.html(temp);
    									console.log(temp);
    									obj.find('li:first').addClass('selected');
    									options.eventType=obj.attr("eventType")||"click";
    								}
    							});
    							obj.tabs(options);
    						}
                        }
					}
				});
			}
		});
		
		
	});
	</script>
EOF;
		}
		//logs($html);
		$paramFive = " tabfor=\"{$randUniqueTag}\" ";
		$list[0]=$endfuncitonname;//转换函数
		$list[1]=$jshtml; //js代码
		$list[2]=$html;//头部
		$list[3]=$foothtml;//底部
		$list[4]=$buttonHtml;//页面原始按钮
		$list[5] =$paramFive;// 唯一标识数据
		// 子级选项卡生成JS
		$list[6] = $reflushChildLevelTabs;
		$list[7] = $randUniqueTag;
	return $list;
	}catch (Exception $e){
		echo "选项卡异常！！".$e->__toString();
	}
}

/**
 * 检查当前表单是否为关系型表单
 * @Title: checkBindStatus
 * @Description: todo(这里用一句话描述这个方法的作用) 
 * @param unknown $action		当前action名称
 * @param unknown $oprateType  	操作方式，add/edit/view
 * @author quqiang 
 * @date 2015年2月27日 下午7:09:19 
 * @throws
 */
function checkBindStatus($action , $oprateType ){
	if(!$action){
		return true;
	}
	if($oprateType=='add' && !$_GET['main']){
		return true;
	}
	// 检查在	mis_auto_bind	表中是否存在记录
	$misAutoBindObj = D('mis_auto_bind');
	$bindRet = $misAutoBindObj->where("bindaname='{$action}' or inbindaname='{$action}' and bindtype=0")->count();
	// 检查在	mis_auto_bind_settable	表中是否存在记录
	$misAutoBindSettableObj = D('mis_auto_bind_settable');
	$bindSettableRet = $misAutoBindSettableObj->where("bindaname='{$action}' or inbindaname='{$action}' and bindtype in (2,3)")->count();
	if($bindRet || $bindSettableRet){
		return false;
	}else{
		return true;
	}
	
}
/**
 * 二维单项数组降维回调函数， * 只适用于array_reduce的回调 * 。
 * @Title: reduceFunc
 * @Description: todo(二维单项数组降维回调函数，只适用于array_reduce的回调。) 
 * @param string $v1 上一次的数据
 * @param array $v2  当前传入项
 * @author quqiang 
 * @date 2015年3月1日 下午2:40:00 
 * @throws
 */
function reduceFunc($v1 , $v2){
	 return $v1.','.$v2;
}

/**
 * 通用表单改版所需要函数。
 */
/**
 * 检查是否为入口
 * @Title: checkActionIsMain
 * @Description: todo(检查传入action是否为关系表单的主表,并返回入口表单的类型)
 * @param string $action	要检查的action名称
 * @param int | string	$type 表单类型值，默认为空不按表单类型检查
 * @author quqiang
 * @return boolean | array	boolean 表示非关系表单入口， array 表示是关系表单入口，并返回当前入口的表单类型
 * @date 2015年3月6日 上午10:40:28
 * @throws	NullDataExcetion
 * @example
 * $ret = checkActionIsMain(控制器名称，检查表单类型);
 */
function checkActionIsMain($action  , $type=''){
	if(empty($action)){
		throw new NullDataExcetion('检查是否为关系表单入口：action空值');
	}
	$model = D('MisAutoBind');
	$map['bindaname'] = $action;
	//$map['pid']	=	0;
	$map['status']	=	1;
	if( !empty($type) ){
		$map['typeid']	=	$type;
	}
	$ret = $model->where($map)->select();
// 	if( count($ret) >1 ){
// 		throw new NullDataExcetion('检查是否为关系表单入口：同一表单多次做为主入口存在，请检查数据：mis_auto_bind');
// 	}
	// 套表记录中来源表单为当前表，且地址栏参数main无值
	if( count($ret)&&!$_GET['main'] ){
		// 是入口文件
		$arr['ismain'] = true;
		$ret = reset($ret);
		if(!is_array($ret)){
			throw new NullDataExcetion('检查是否为关系表单入口：检查数据结构异常。');
		}
		$arr['formtype'] = $ret['typeid'];
		return $arr;
	}else{
		//
		return false;
	}
}

/**
 * 检查是否为关系型表单
 * @Title: checkIsRelationForm
 * @Description: todo(检查传入表单是否为关系表单)
 * @param string	$actionName	要检查的action名称
 * @return boolean	true：是关系表单 | false：不是关系表单
 * @param string	$oprateType		当前的数据操作方式。add|edit|auditEdit.......
 * @author quqiang
 * @date 2015年3月5日 下午6:01:15
 * @throws	NullDataExcetion
 */
function checkIsRelationForm($actionName , $oprateType){
	if( empty($actionName) ){
		throw new NullDataExcetion('检查是否为关系型表单：action空值');
	}
	if( empty($oprateType) ){
		throw new NullDataExcetion('检查是否为关系型表单：操作方式空值');
	}
	$model = M('mis_auto_bind');
	$sql = "SELECT count(*) as _count FROM mis_auto_bind WHERE bindaname ='{$actionName}' OR inbindaname='{$actionName}'";
	$data = $model->query($sql);
	$ret = false;
	if($data[0]['_count']){
		$ret = true;
	}
	return $ret;
}

/**
 * 检查表单类型
 * @Title: checkFormType
 * @Description: todo(检查传入action是否为审批流)
 * @param string $action	要检查的action名称
 * @author quqiang
 * @return int	1:审批，0:普通表单
 * @date 2015年5月4日 下午4:26:28
 * @throws	NullDataExcetion
 */
function checkFormType($action){
	if(empty($action)){
		throw new NullDataExcetion('检查表单类型：action空值');
	}
	$obj = M('mis_dynamic_form_manage');
	$map['actionname'] = $action;
	$data = $obj->where($map)->find();
	if(is_array($data)){
		return $data['isaudit'];
	}else{
		throw new NullDataExcetion("没能检索到指定表【{$action}】的详细信息!");
	}
}

/**
 * 获取组合表单的所有子项
 * @Title: getComForm
 * @Description: todo(这里用一句话描述这个方法的作用)
 * @param string	 $mainAction  主入口action名称
 * @param boolean	$isContentSelf	是否包含自己，默认false 不包含
 * @author quqiang
 * @date 2015年3月4日 下午1:54:01
 * @throws
 */
function getComForm($mainAction , $isContentSelf = false){
	$MisAutoBindModel=D("MisAutoBind");
	//获取主表formid
	$formid=getFieldBy($mainAction, "actionname", "id", "mis_dynamic_form_manage");
	$bindMap=array();
	$bindMap['bindaname']=$mainAction;
	$bindMap['typeid']=array("neq",2);
	$bindMap['pid']=0;
	$MisAutoBindVo=$MisAutoBindModel->where($bindMap)->find();
	if($MisAutoBindVo){
		//组合或组从主表
		$map=array();
		$map['level']=$formid;
		$map['typeid']=array("neq",2);
		$map['bindtype'] = 0;
		$map['STATUS'] = 1;
		// 是否包换自己
		if(!$isContentSelf){
			//$map['pid'] = array('neq' , '0');
		}
		$list=$MisAutoBindModel->where($map)->getField("id,inbindaname");
		if($isContentSelf){
			echo $MisAutoBindModel->getLastSql();
		}
		return $list;
	}else{
		return false;
	}
}

/**
 * 获取套表的所有子项，只获取直属关系，套表 调用 套表 不考虑、
 * @Title: getNestedForm
 * @Description: todo(这里用一句话描述这个方法的作用)
 * @param string	 $mainAction  主入口action名称
 * @param boolean	$isContentSelf	是否包含自己，默认false 不包含
 * @author quqiang
 * @date 2015年3月4日 下午1:55:23
 * @throws
 */
function getNestedForm($mainAction , $isContentSelf = false){
	$model = new Model();
	//数据库递归获取当前表单所有子级
	$data = $model->query("SELECT getBindChildList('{$mainAction}')");
	if(false !== $data){
		$ret = explode(',',  reset(reset($data)) );
		array_shift($ret);
		return $ret;
	}else{
		return false;
	}
	exit;
	//旧版获取套表所以子级
	$MisAutoBindModel=D("MisAutoBind");
	//获取主表formid
	$formid=getFieldBy($mainAction, "actionname", "id", "mis_dynamic_form_manage");
	//是否为套表主表
	if(getFieldBy($mainAction, "bindaname", "pid", "mis_auto_bind","typeid",2)==0){
		$map=array();
		$map['level']=$formid;
		$map['typeid']=2;
		// 是否包换自己
		if(!$isContentSelf){
			//$map['pid'] = array('neq' , '0');
		}
		$list=$MisAutoBindModel->where($map)->getField("id,inbindaname");
		if($isContentSelf){
			echo $MisAutoBindModel->getLastSql();
		}
		return $list;
	}else{
		return false;
	}
}

/**
 * 通用表单改版-模板文件调用功能函数。
 */
// 套表时
function setFormControllAutoCreteAppend($actionName , $oprateType , $main , $originalUrl,$vo , $conf){
	$retData = array();
	/**
	 * 参数定义
	 */
	//	当前action名称
	$selfactionname =  MODULE_NAME;
	// 一键保存按钮
	$allSaveBtn = '';
	// 通用表单产的隐藏域组件及值。
	$hiddens = '';
	// 隐藏域组件附属变量-子级表单临时存储数组
	$relationArr = array();
	// 隐藏域组件附属变量-所有构成表单临时存储
	$actionlist = '';
	// 重构后的url数据提交地址。
	$restructureUrl = $originalUrl;
	// form表单上的备用属性
	$formParame='';
	$funcitonname="navTabAjaxDone";
	
	try {

		// 检查当前表单是否是关系表单
		$isRelationForm = checkIsRelationForm($actionName, $oprateType);
		if( $isRelationForm ){
			/**
			 * 处理一键保存按钮的生成
			 * 一键保存出现依据：是关系型表单的主表
			 */
			// 检查当前表单是否为关系表单的主表
			$isRelationMainForm = checkActionIsMain($actionName);
			if( $isRelationMainForm != false && !$main){
				if(!$main)	$main = $actionName;
				// 一键保存结束表单数据统一提交地址
				switch ($oprateType){
					case 'add':
						$oprate = 'updateControll';
						$funcitonname=$actionName."addnavTabDone";
						break;
					case 'edit':
						$oprate = 'updateControll';
						$funcitonname="navTabAjaxDoneNoFlush";
						break;
					default:
						$oprate = 'updateControll';
				}
				$aaa = __URL__;
		
				// 附加按钮的生成
				$appendBtnHtml = '';
				if($oprateType == 'edit' || $oprateType == 'add' ){
					$formtype = checkFormType($actionName);
						
					if($formtype==1){
						// 生成启动流程
						if( is_array($vo) && $vo['operateid'] == 0 && $vo['id']){
							$appendBtnHtml = <<<EOF
			<li class="left">
<a title="启动流程" height="227" width="640" target="dialog" rel="MisAutoHxzaddremind" href="$aaa/enterToSubmitAudit/type/$oprateType/module/$actionName" mask="true" class="tml_look_btn  tml_mp js-addremind">
									<span class="icon-save"></span>启动流程</a>
				<a class="enterToSubmitAudit hide" href="javascript:void(0);"><span class="icon-save"></span>启动流程</a>
					<input type="hidden" value="1" name="__startprocessStatus__" disabled="disabled" enterToSubmitAudit="enterToSubmitAudit" />
			</li>
EOF;
						}elseif(1== $_GET['bgval']){
								
							$appendBtnHtml = <<<EOF
			<li class="left">
				<a class="enterToSubmitAudit" href="javascript:void(0);"><span class="icon-save"></span>变更启动流程</a>
					<input type="hidden" value="2" name="__startprocessStatus__" disabled="disabled" enterToSubmitAudit="enterToSubmitAudit" />
			</li>
EOF;
						}
					}else{
						// 生成确认提交
						if( is_array($vo)   ){
							if(1== $_GET['bgval']){
								$appendBtnHtml = <<<EOF
			<li class="left">
				<a class="enterToSubmit" href="javascript:void(0);"><span class="icon-save"></span>确认提交</a>
					<input type="hidden" value="2" name="__startprocessStatus__" disabled="disabled" enterToSubmit="enterToSubmit" />
					<input type="hidden" value="1" name="operateid" disabled="disabled" enterToSubmit="enterToSubmit" />
			</li>
EOF;
							}else{
								$appendBtnHtml = <<<EOF
			<li class="left">
				<a class="enterToSubmit" href="javascript:void(0);"><span class="icon-save"></span>确认提交</a>
					<input type="hidden" value="1" name="operateid" disabled="disabled" enterToSubmit="enterToSubmit" />
			</li>
EOF;
							}
						}
					}
						
				}
		
				if($oprateType == 'view'){
					// 生成变更按钮
					if( is_array($vo) && $vo['operateid'] == 1 ){
						$appendBtnHtml = <<<EOF
			<li class="left">
				<a href="__URL__/changeEdit/bgval/1/id/{$vo['id']}" target="navTab" class="js-Change" rel="{$actionName}edit"><span class="icon-save"></span>变更</a>
			</li>
EOF;
					}
					// 变更按钮 暂时不用在这里生成，使用toolbar中的按钮组
					$appendBtnHtml ='';
				}
				// 审批流表单时，表单回调函数修改。
		
		
				// 获取当前表单的类型
				$manageModel = M('mis_dynamic_form_manage');
				$formTypeMap['actionname'] = $actionName;
				$formTypeData = $manageModel->where($formTypeMap)->select();
		
				$formTypeData = reset($formTypeData);
				$isaudit = $formTypeData['isaudit'];
				if($isaudit && $oprateType != 'add'){
					$funcitonnameAudit = 'navTabAjaxDone';
				}else{
					$funcitonnameAudit = $funcitonname;
				}
		
				$taobiaoHtmlTemp = "";
		
				if(1== $_GET['bgval']){
					//当遇到变更的时候，将保存按钮关闭
					$taobiaoHtmlTemp = 'display:none;';
				}
				// 审批流	
				$auditCallBack =$conf['callback']['audit']?$conf['callback']['audit']:"return validateCallback(this, {$funcitonnameAudit})";
				// 普通表
				$commonCallBack =$conf['callback']['common']?$conf['callback']['common']:"return validateCallback(this, {$funcitonname})";
				// 保存,新增套表时的特殊回调
				$enterToSubmitCallBack =$conf['callback']['common']?$conf['callback']['common']:"return validateCallback(this, navTabAjaxDone)";

				$allSaveBtnTemp = <<<EOF
		<ul class="right top_tool_bar" style="margin-right:0px;">
			<li class="left" style="{$taobiaoHtmlTemp}">
                <a class="allSaveBtn" href="javascript:void(0);"><span class="icon-save"></span> 保存</a>
            </li>
			{$appendBtnHtml}
		</ul>
<script>
	var errorCount=-1;
	$(function(){
		// 1:普通表单。 2：审批流表单
		var formtype = 1;
	var box = navTab.getCurrentPanel();
	function initFormValid(){
		errorCount=0;
		var formObj = \$("form.required-validate",box);
		//formObj.unbind();
		formObj.each(function(){
				//$(this).logs('自定义 表单初始化' );
				//$(this).unbind();
				\$(this).validate({
					 debug:false,
					focusInvalid: true,
					focusCleanup: true,
					errorElement: "span",
					ignore: ".ignore",
					invalidHandler: function(form, validator) {
						//$(this).logs('自定义的 invalidHandler');
						var errors = validator.numberOfInvalids();
						errorCount += errors;
						$(this).logs('自定义 错误个数：' + errors);
						if (errors) {
							//var message = DWZ.msg("validateFormError", [errors]);
							//alertMsg.error(message);
							//console.log(message+errors);
						}
					}, submitHandler:function(form){
			            alert("submitted");
			        }
				});
			});
	}
		function checkStatus(obj){
			if( $(obj).hasClass('disabled') ){
				return false;
			}else{
				return true;
			}
		}
		function setStatus(obj){
			$(obj).addClass('disabled');
		}
		//$("form.required-validate",box).validate();
		
		//initFormValid();
		// 确认提交
		$('a.enterToSubmit:last' , box).on('click' , function(){
			formtype = 3;
			$('[enterToSubmit="enterToSubmit"]' , box).attr("disabled" , false);
			clickfunction(this);
		});
		// 启动流程
		$('a.enterToSubmitAudit:last' , box).on('click' , function(){
			formtype = 2;
			$('[enterToSubmitAudit="enterToSubmitAudit"]' , box).attr("disabled" , false);
			clickfunction(this);
		});
		// 保存
		$('a.allSaveBtn:last ' , box).on('click' , function(){
			formtype = 1;
			$('[enterToSubmit="enterToSubmit"]' , box).attr("disabled" , true);
			clickfunction(this);
		});
		function clickfunction(_this) {
		    initFormValid();
			var formObj = $('form[autoformsubmit="submit"]', box);
		    formObj.each(function() {
		        $(this).valid();
		    });
		
			errorCount = $(':input.error',box).length;
		    $(this).logs('自定义的表单验证' + errorCount);
		    if (errorCount > 0) {
		        var message = DWZ.msg("validateFormError", [errorCount]);
		        alertMsg.error(message);
		    } else if (errorCount == 0) {
		        var curobj = $(_this);
		        if (checkStatus(curobj) == false) {
		            return;
		        } else {
		            setStatus(curobj);
		        }
				var auditCallBack ='{$auditCallBack}';
				var commonCallBack ='{$commonCallBack}';
				var enterToSubmitCallBack ='{$enterToSubmitCallBack}';
		
		        // 构造一个结束的表单。让程序知道，当前这个批次的操作完成了。
		        var endForm = $('<form action="' + TP_APP + '/Common/{$oprate}/navTabId/{$main}/endform/1{$conf['urlparame']}" method="post" onsubmit="return validateCallback(this, {$funcitonnameAudit})"></form>');
		        //endForm.attr('action',TP_APP+'/Index/{$oprate}/navTabId/{$main}/');
		        //endForm.attr('method','post');
		        endForm.append($('<input type="hidden" name="__actionlistend__" value="end" />'));
		        
		        var settingHiddens = '{$conf['hiddens']}';
		        if(settingHiddens)
		        	endForm.append($(settingHiddens));
				switch(formtype){
					case 1:
						endForm.attr('onsubmit' , commonCallBack);
						//endForm.append($('<input type="hidden" name="callbackType" value="closeCurrent" />'));
						break;
					case 2:
						endForm.attr('onsubmit' , auditCallBack);
						//endForm.append($('<input type="hidden" name="callbackType" value="closeCurrent" />'));
						break;
					case 3:
						endForm.attr('onsubmit' , enterToSubmitCallBack);
						endForm.append($('<input type="hidden" name="callbackType" value="closeCurrent" />'));
						break;
					default:
						endForm.attr('onsubmit' , commonCallBack);
						break;
				}
				//console.warn(formtype);
				//console.warn(endForm.attr('onsubmit'));
				var div = $('<div></div>');
				div.append(endForm);
		        //console.log(div.html());
		
		        //endForm.append($('<input type="hidden" name="callbackType" value="closeCurrent" />'));
		        var main = $('#{$main}_{$oprateType}').find('input[name="__main__"]').clone();
		        var actionlist = $('#{$main}_{$oprateType}').find('input[name="__actionnamelist__"]').clone();
		        endForm.append(main);
		        endForm.append(actionlist);
		   
			   // 表有表单合并
				var recombinationName = '__apply__form__';
				var recombinationNameStart = '[';
				var recombinationNameEnd = ']';
				var formObjClone = formObj.clone(true);
		        formObjClone.each(function(i , v) {
					var curFormObj = $(this);
					curFormObj.find(':input[name]').each(function(key , item){
						var curName = $(item).attr('name');
		
						var reg = /\[.*\]/;
						var typecheck = reg.test(curName);
						if(typecheck){
							// 将现有标签name为多提交时替换格式
							var regName = /(\w+)(\[.*\])/;
							var reNameArr = curName.match(regName);
							if(typeof(reNameArr) == 'object'){
								curName = recombinationNameStart + reNameArr[1] + recombinationNameEnd + reNameArr[2];
							}
							$(item).attr('name' ,recombinationName
								+ recombinationNameStart
								+ i
								+ recombinationNameEnd
								+ curName
							);
						}else{
							$(item).attr('name' ,recombinationName
								+ recombinationNameStart
								+ i
								+ recombinationNameEnd
								+ recombinationNameStart
								+ curName
								+ recombinationNameEnd
							 );
						}
					endForm.append($(item));
			
					});
		        });
		        		console.log(endForm);
				$(endForm).submit();
        		//endForm.ajaxSubmit();
		    }
		}
	});
</script>
EOF;
		
			if($oprateType != 'view'){
					$allSaveBtn = $allSaveBtnTemp;
					}else{
					$allSaveBtn = <<<EOF
					<ul class="right top_tool_bar" style="margin-right:0px;">
					{$appendBtnHtml}
					</ul>
EOF;
					}
						
					$formtype = $isRelationMainForm['formtype'];
					// 往隐藏域列表中加入套表的值关联情况页面刷新，
					if(($oprateType == 'add' || $oprateType == 'edit' ) && $formtype==2){
					/*
							bindaname	套表主表action名称
							 bindval		套表主表关系产生字段
							  inbindaname	套表子表action名称
							 inbindval		套表子表关系接收字段
							 */
							 // 获取主表的第一级子表关联信息
							 $sql = "SELECT bindaname , bindval , inbindaname , inbindval FROM mis_auto_bind
							 WHERE typeid=2 AND  bindaname='{$main}'";
								$model = M('mis_auto_bind');
								$nestFormData = $model->query($sql);
								$json = '';
								if (is_array($nestFormData)){
								$temp='';
								foreach ($nestFormData as $k=>$v){
								// inbindval
								$temp[$v['bindval']][]=$v;
								}
								$json = json_encode($temp);
								}
									
								//$json = is_array($nestFormData) ? json_encode($nestFormData) : '';
								// 获取主表的套表关联信息。
								// style="display:none"
								$allSaveBtn .= <<<EOF
								<!--	套表一级子表关联数据动态修改	-->
								<script>
$(function(){
		var box = navTab.getCurrentPanel();
		var autoJson = '{$json}';
		var curAction = '{$actionName}';
		var curMain = '{$main}';
		var scriptTag = '{$scriptTag}';
		if(isNullorEmpty(autoJson)){
          autoJson = $.parseJSON(autoJson);
			$.each(autoJson , function(key , val){
				var obj = $('[name="'+key+'"]',box);
				if(typeof(obj) != undefined){
					//obj.eq(0).off('change');
					obj.eq(0).on('change' , function(){
						///////////////////
						var fieldName = key;
						var fieldValue = $(this).val();
						var tag = $(this).closest('form').attr('tabfor');
		                var mainid =  $(this).closest('form').find('input[name="id"]').val();
		                mainid = isNullorEmpty(mainid)?mainid:'';
						var mainprojectid =  $(this).closest('form').find('input[name="projectid"]').val();
				        projectid = isNullorEmpty(mainprojectid)?mainprojectid:'';
		                var parames = new Object();
		                parames.action=curAction;
		                parames.main=curMain;
		                parames.val=fieldValue;
		                parames.filedval=fieldName;
		                parames.bindrdid=mainid;
						if(fieldValue){
							$.ajax({
									url:TP_APP+'/Common/getAutoFormTabs',
									data:{'action':curAction,'main':curMain,'val':fieldValue,'filedval':fieldName,'projectid':projectid,'bindrdid':mainid},
									type:'post',
									dataType:'json',
									success:function(msg){
										if(isNullorEmpty(msg.data)){
											var obj = $('#tabsContent_'+tag);
				                            var header = obj.find('div.tabsHeader');
				                            $.each(msg.data , function(key , val){
				                                $.each(val , function(k , v){
				                                      // 改url地址
				                                      var tempId = k+'_'+tag;
				                                      var curOprateObj = $('#'+tempId, header);
				                                      curOprateObj.attr('href',v);
				                                      var index = curOprateObj.closest('li').index();
				console.log(tempId);
				                                      // 重新请求子表页面
		                                              $('.tabsContent',obj).children().eq(index).loadUrl(v,{},function(){});
						                        });
						                    });
										}
									}
								});
						}
					});
				}
			});
		}
});
			
			
</script>
EOF;
				}
			}
			// 隐藏域的生成
			if( $isRelationMainForm != false && !$_GET['main']){
				/**
				 * 主入口代码处理
				 */
				if(!$main)	$main = $actionName;
				$formtype = $isRelationMainForm['formtype'];
				if($formtype==2){
					$relationArr = getNestedForm($actionName );
					// 检查套表的构成项是否为关系型表单
					foreach ($relationArr as $key => $val){
						$tempData = getComForm($val );
						if( is_array( $tempData ) ) {
							$relationArr = array_merge($relationArr , $tempData);
						}
					}
				}else{
					$relationArr = getComForm($main );
				}
			
				if(is_array($relationArr)){
					$actionlist =  join(',',$relationArr);//array_reduce($relationArr , 'reduceFunc' , $main));
				}else{
					$actionlist = $main;
				}
				$relationArr['0'] = $main;
			}else{
				/**
				 * 当前为关系型表单的非入口
				 * 二级选项
				 * 生成隐藏域
				 */
				// mian参数有值，表示它是从主入口进来的
				// 那就以main的值查询其所有子级
				$relationArr =getNestedForm($main);
				if(!is_array($relationArr) || count($relationArr) ==0){
					$relationArr = getComForm($main);
				}else{
					// 检查套表的构成项是否为关系型表单
					foreach ($relationArr as $key => $val){
						$tempData = getComForm($val);
						if( is_array( $tempData ) ) {
							$relationArr = array_merge($relationArr , $tempData);
						}
					}
				}
				if(is_array($relationArr)){
					$actionlist = join(',',$relationArr);//array_reduce($relationArr , 'reduceFunc' , $main);
				}else{
					$actionlist = $main;
				}
				$relationArr['0'] = $main;
			}
			unset($temp);
			foreach ($relationArr as $k=>$v){
				$temp[]=$v;
			}
			unset($relationArr);
			$relationArr = $temp;
			if( (in_array( $actionName , $relationArr ) || $actionName == $main) && $oprateType!='view' ){
				$formParame = " autoformsubmit=\"submit\"";
			}
		
			/**
			 * 通用表单产的隐藏域组件及值html组装
			 */
			$hiddens = <<<EOF
			<input type="hidden" name="__actionnamelist__" value="{$actionlist}" />
			<input type="hidden" name="__main__" value="{$main}" />
			<input type="hidden" name="__selfaction__" value="{$selfactionname}" />
			<input type="hidden" name="__selfoprate__" value="{$oprateType}" />
EOF;
		
			/**
			 * 处理url重构值
			 */
			// 获取当前表单的类型
						
						 $manageModel = M('mis_dynamic_form_manage');
						 $formTypeMap['actionname'] = $actionName;
					 $formTypeData = $manageModel->where($formTypeMap)->select();
					 if( is_array($formTypeData) && count($formTypeData) > 1){
						throw new NullDataExcetion("动态重构表单的组件项：当前action{$actionName} 在manage表中存在多条记录，");
					 }
					 	if( is_array($formTypeData) && count($formTypeData) == 0){
					 	throw new NullDataExcetion("动态重构表单的组件项：当前action{$actionName} 在manage表中无记录，");
					 	}
					 	// 			if( $isRelationMainForm != false){
					 	// 			$formtype = $isRelationMainForm['formtype'];
					 	$formTypeData = reset($formTypeData);
					 	$isaudit = $formTypeData['isaudit'];
					 	switch ($oprateType){
					 	case 'add':
					 		if(!$_GET['main'] && $isRelationMainForm == false ){
								// 是关系表单但没有入口，也不是主表
									if($isaudit){
									// 审批 // 修改：关系型表单的非主表提交地址为默认、 nbmxkkj@20150527 2143
										//$restructureUrl = 'updateControll';
									}
									}else{
									if($formtype==0 && $isRelationMainForm != false){
									$restructureUrl = 'updateControll';
									}else{
									$restructureUrl = 'updateControll';
											if($isaudit){
											// 审批
												$restructureUrl = 'updateControll';
											}
									}
									}
									break;
					 		case 'edit':
					 			if(!$_GET['main'] && $isRelationMainForm == false ){
					 				// 是关系表单但没有入口，也不是主表
					 			}else{
					 				$restructureUrl = 'updateControll';
					 				if($isaudit){
					 					// 审批
					 					$restructureUrl = 'updateControll';
					 				}
					 			}
					 			break;
					 		case 'auditEdit':
					 			$restructureUrl = 'updateControll';
					 			if($isaudit){
					 				// 审批
					 				$restructureUrl = 'updateControll';
					 			}
					 			break;
					 		case 'view':
					 			$restructureUrl = 'updateControll';
					 			if($isaudit){
					 				// 审批
					 				$restructureUrl = 'updateControll';
					 			}
					 			break;
					 	}
					 	$selfactiontype = $isaudit ? 'audit':'common';
					 	$hiddens .= <<<EOF
		
			<input type="hidden" name="__selfactiontype__" value="{$selfactiontype}" />
EOF;
					 	// 			$restructureUrl
		}
		
	} catch (Exception $e) {
		echo '表配置：'.$e->__toString();
	}
	
	if($oprateType == 'add'){
		$hiddens .= <<<EOF
		<input type="hidden" name="bindid" value="{$_GET['bindid']}" />
		<input type="hidden" name="relationmodelname" value="{$_GET['bindaname']}" />
EOF;
	}
	// 数据组装
	$retData[0] = $allSaveBtn;		// 一键提交
	$retData[1] = $hiddens.$conf['hiddens'];		// 隐藏属性
	$retData[2] = $restructureUrl;	// 重构后的url
	$retData[3] = $formParame;		//	备用的表单属性
	$retData[4] = $conf['urlparame'];		//	地址栏参数附加
	$retData[5] = $conf['callback']['callback'];		// 非套表使用的回调
	return $retData;
}
/**
 * 动态重构表单的组件项
 * @Title: setFormControllAutoCreate
 * @Description: todo(动态重构表单的一键保存按钮，关系型表单的隐藏项，表单的提交地址。) 
 * @param string $actionName
 * @param string	$oprateType
 * @param string	$main
 * @param string	$originalUrl  
 * @author quqiang 
 * @date 2015年3月6日 下午2:27:23 
 * @throws
 */
function setFormControllAutoCreate($actionName , $oprateType , $main , $originalUrl , $vo){
	// 返回结果对象
	// 格式定义：		$retData[0] = 一键保存按钮
	// 				$retData[1] = 关系表单所需的隐藏项
	//				$retData[2] = 重构后的提交地址
	//				$retData[3] = 备用的表单属性
	/*
	 * 需要实现：
	 * 	1。套表与非套表时的操作按钮及使用到的JS代码
	 * 	2.表单提交需要的隐藏域，支持二开的添加
	 * 	3.提交地址问题
	 * 	4。form标签上的备用属性
	 */
	$retData = array();
	/**
	 * 参数定义
	 */
	//	当前action名称
	$selfactionname =  MODULE_NAME;
	// 一键保存按钮
	$allSaveBtn = '';
	// 通用表单产的隐藏域组件及值。
	$hiddens = '';
	// 		隐藏域组件附属变量-子级表单临时存储数组
	$relationArr = array();
	// 		隐藏域组件附属变量-所有构成表单临时存储
	$actionlist = '';
	// 重构后的url数据提交地址。
	$restructureUrl = $originalUrl;
	// form表单上的备用属性
	$formParame='';
	$funcitonname="navTabAjaxDone";
	try {
		// 检查当前表单是否是关系表单
		$isRelationForm = checkIsRelationForm($actionName, $oprateType);
		if( $isRelationForm ){
			/**
			 * 处理一键保存按钮的生成
			 * 一键保存出现依据：是关系型表单的主表
			 */
			// 检查当前表单是否为关系表单的主表
			$isRelationMainForm = checkActionIsMain($actionName);
			if( $isRelationMainForm != false && !$main){
				if(!$main)	$main = $actionName;
				// 一键保存结束表单数据统一提交地址
				switch ($oprateType){
					case 'add':
						$oprate = 'updateControll';
						$funcitonname=$actionName."addnavTabDone";
						break;
					case 'edit':
						$oprate = 'updateControll';
						$funcitonname="navTabAjaxDoneNoFlush";
						break;
					default:
						$oprate = 'updateControll';
				}
				$aaa = __URL__;
				
				// 附加按钮的生成
				$appendBtnHtml = '';
				if($oprateType == 'edit' || $oprateType == 'add' ){
					$formtype = checkFormType($actionName);
					
					if($formtype==1){
						// 生成启动流程
						if( is_array($vo) && $vo['operateid'] == 0 && $vo['id']){
							$appendBtnHtml = <<<EOF
			<li class="left">
<a title="启动流程" height="227" width="640" target="dialog" rel="MisAutoHxzaddremind" href="$aaa/enterToSubmitAudit/type/$oprateType/module/$actionName" mask="true" class="tml_look_btn  tml_mp js-addremind">
									<span class="icon-save"></span>启动流程</a>
				<a class="enterToSubmitAudit hide" href="javascript:void(0);"><span class="icon-save"></span>启动流程</a>
					<input type="hidden" value="1" name="__startprocessStatus__" disabled="disabled" enterToSubmitAudit="enterToSubmitAudit" />
			</li>
EOF;
						}elseif(1== $_GET['bgval']){
							
														$appendBtnHtml = <<<EOF
			<li class="left">
				<a class="enterToSubmitAudit" href="javascript:void(0);"><span class="icon-save"></span>变更启动流程</a>
					<input type="hidden" value="2" name="__startprocessStatus__" disabled="disabled" enterToSubmitAudit="enterToSubmitAudit" />
			</li>
EOF;
						}
					}else{
					// 生成确认提交
						if( is_array($vo)   ){
							if(1== $_GET['bgval']){
								$appendBtnHtml = <<<EOF
			<li class="left">
				<a class="enterToSubmit" href="javascript:void(0);"><span class="icon-save"></span>确认提交</a>
					<input type="hidden" value="2" name="__startprocessStatus__" disabled="disabled" enterToSubmit="enterToSubmit" />
					<input type="hidden" value="1" name="operateid" disabled="disabled" enterToSubmit="enterToSubmit" />
			</li>
EOF;
							}else{
								$appendBtnHtml = <<<EOF
			<li class="left">
				<a class="enterToSubmit" href="javascript:void(0);"><span class="icon-save"></span>确认提交</a>
					<input type="hidden" value="1" name="operateid" disabled="disabled" enterToSubmit="enterToSubmit" />
			</li>
EOF;
							}
						}
					}
					
				}
				
				if($oprateType == 'view'){
					// 生成变更按钮
					if( is_array($vo) && $vo['operateid'] == 1 ){
						$appendBtnHtml = <<<EOF
			<li class="left">
				<a href="__URL__/changeEdit/bgval/1/id/{$vo['id']}" target="navTab" class="js-Change" rel="{$actionName}edit"><span class="icon-save"></span>变更</a>
			</li>
EOF;
					}
					// 变更按钮 暂时不用在这里生成，使用toolbar中的按钮组
					$appendBtnHtml ='';
				}
                // 审批流表单时，表单回调函数修改。
                
                
                // 获取当前表单的类型
            $manageModel = M('mis_dynamic_form_manage');
            $formTypeMap['actionname'] = $actionName;
            $formTypeData = $manageModel->where($formTypeMap)->select();
            
            $formTypeData = reset($formTypeData);
            $isaudit = $formTypeData['isaudit'];
                if($isaudit && $oprateType != 'add'){
                    $funcitonnameAudit = 'navTabAjaxDone';
                }else{
                    $funcitonnameAudit = $funcitonname;
                }
				
                $taobiaoHtmlTemp = "";

				if(1== $_GET['bgval']){
                	//当遇到变更的时候，将保存按钮关闭
                	$taobiaoHtmlTemp = 'display:none;';
				}
			
				$allSaveBtnTemp = <<<EOF
		<ul class="right top_tool_bar" style="margin-right:0px;">
			<li class="left" style="{$taobiaoHtmlTemp}">
                <a class="allSaveBtn" href="javascript:void(0);"><span class="icon-save"></span> 保存</a>
            </li>
			{$appendBtnHtml}
		</ul>
<script>
	var errorCount=-1;
	$(function(){
		// 1:普通表单。 2：审批流表单
		var formtype = 1;
	var box = navTab.getCurrentPanel();
	function initFormValid(){
		errorCount=0;
		var formObj = \$("form.required-validate",box);
		//formObj.unbind();
		formObj.each(function(){
				//$(this).logs('自定义 表单初始化' );
				//$(this).unbind();
				\$(this).validate({
					 debug:false,
					focusInvalid: true,
					focusCleanup: true,
					errorElement: "span",
					ignore: ".ignore",
					invalidHandler: function(form, validator) {
						//$(this).logs('自定义的 invalidHandler');
						var errors = validator.numberOfInvalids();
						errorCount += errors;
						$(this).logs('自定义 错误个数：' + errors);
						if (errors) {
							//var message = DWZ.msg("validateFormError", [errors]);
							//alertMsg.error(message);
							//console.log(message+errors);
						}
					}, submitHandler:function(form){
			            alert("submitted"); 
			        } 
				});
			});
	}
		function checkStatus(obj){
			if( $(obj).hasClass('disabled') ){
				return false;
			}else{
				return true;
			}
		}
		function setStatus(obj){
			$(obj).addClass('disabled');
		}
		//$("form.required-validate",box).validate();
				
		//initFormValid();
		// 确认提交
		$('a.enterToSubmit:last' , box).on('click' , function(){
			formtype = 3;
			$('[enterToSubmit="enterToSubmit"]' , box).attr("disabled" , false);
			clickfunction(this);
		});
		// 启动流程
		$('a.enterToSubmitAudit:last' , box).on('click' , function(){
			formtype = 2;
			$('[enterToSubmitAudit="enterToSubmitAudit"]' , box).attr("disabled" , false);
			clickfunction(this);
		});
		// 保存
		$('a.allSaveBtn:last ' , box).on('click' , function(){
			formtype = 1;
			$('[enterToSubmit="enterToSubmit"]' , box).attr("disabled" , true);	
			clickfunction(this);
		});
		function clickfunction(_this) {
		    initFormValid();
			var formObj = $('form[autoformsubmit="submit"]', box);
		    formObj.each(function() {
		        $(this).valid();
		    });
			
			errorCount = $(':input.error',box).length;
		    $(this).logs('自定义的表单验证' + errorCount);
		    if (errorCount > 0) {
		        var message = DWZ.msg("validateFormError", [errorCount]);
		        alertMsg.error(message);
		    } else if (errorCount == 0) {
		        var curobj = $(_this);
		        if (checkStatus(curobj) == false) {
		            return;
		        } else {
		            setStatus(curobj);
		        }
				var auditCallBack ='return validateCallback(this, {$funcitonnameAudit})';
				var commonCallBack ='return validateCallback(this, {$funcitonname})';
				var enterToSubmitCallBack ='return validateCallback(this, navTabAjaxDone)';
				var otherCallBack = 'return iframeCallback(this, navTabAjaxDone)';
				
		        // 构造一个结束的表单。让程序知道，当前这个批次的操作完成了。
		        var endForm = $('<form action="' + TP_APP + '/Common/{$oprate}/navTabId/{$main}/endform/1" method="post" onsubmit="return validateCallback(this, {$funcitonnameAudit})"></form>');
		        //endForm.attr('action',TP_APP+'/Index/{$oprate}/navTabId/{$main}/');
		        //endForm.attr('method','post');
		        endForm.append($('<input type="hidden" name="__actionlistend__" value="end" />'));
				switch(formtype){
					case 1:
						endForm.attr('onsubmit' , commonCallBack);
						break;
					case 2:
						endForm.attr('onsubmit' , auditCallBack);
						//endForm.append($('<input type="hidden" name="callbackType" value="closeCurrent" />'));
						break;
					case 3:
						endForm.attr('onsubmit' , enterToSubmitCallBack);
						endForm.append($('<input type="hidden" name="callbackType" value="closeCurrent" />'));
						break;
					case 4:
						endForm.attr('onsubmit' , otherCallBack);
						break;
					default:
						endForm.attr('onsubmit' , commonCallBack);
						break;
				}
				//console.warn(formtype);
				//console.warn(endForm.attr('onsubmit'));
				var div = $('<div></div>');
				div.append(endForm);
		        //console.log(div.html());
				
		        //endForm.append($('<input type="hidden" name="callbackType" value="closeCurrent" />'));
		        var main = $('#{$main}_{$oprateType}').find('input[name="__main__"]').clone();
		        var actionlist = $('#{$main}_{$oprateType}').find('input[name="__actionnamelist__"]').clone();
		        endForm.append(main);
		        endForm.append(actionlist);
		       
			   // 表有表单合并
				var recombinationName = '__apply__form__';
				var recombinationNameStart = '[';
				var recombinationNameEnd = ']';
				var formObjClone = formObj.clone(true);
		        formObjClone.each(function(i , v) {
					var curFormObj = $(this);
					curFormObj.find(':input[name]').each(function(key , item){
						var curName = $(item).attr('name');
						
						var reg = /\[.*\]/;
						var typecheck = reg.test(curName);
						if(typecheck){
							// 将现有标签name为多提交时替换格式
							var regName = /(\w+)(\[.*\])/;
							var reNameArr = curName.match(regName);
							if(typeof(reNameArr) == 'object'){
								curName = recombinationNameStart + reNameArr[1] + recombinationNameEnd + reNameArr[2];
							}
							$(item).attr('name' ,recombinationName 
								+ recombinationNameStart
								+ i
								+ recombinationNameEnd
								+ curName
							);
						}else{
							$(item).attr('name' ,recombinationName 
								+ recombinationNameStart
								+ i
								+ recombinationNameEnd
								+ recombinationNameStart
								+ curName
								+ recombinationNameEnd
							 );
						}
					endForm.append($(item));
					
					});
		        });
				endForm.submit();
		    }
		}
	});
</script>
EOF;
		
			if($oprateType != 'view'){
				$allSaveBtn = $allSaveBtnTemp;
			}else{
				$allSaveBtn = <<<EOF
		<ul class="right top_tool_bar" style="margin-right:0px;">
			{$appendBtnHtml}
		</ul>
EOF;
			}
			
				$formtype = $isRelationMainForm['formtype'];
				// 往隐藏域列表中加入套表的值关联情况页面刷新，
				if(($oprateType == 'add' || $oprateType == 'edit' ) && $formtype==2){
					/*
					 bindaname	套表主表action名称
					 bindval		套表主表关系产生字段
					 inbindaname	套表子表action名称
					 inbindval		套表子表关系接收字段
					 */
					// 获取主表的第一级子表关联信息
					$sql = "SELECT bindaname , bindval , inbindaname , inbindval FROM mis_auto_bind 
					WHERE typeid=2 AND  bindaname='{$main}'";
					$model = M('mis_auto_bind');
					$nestFormData = $model->query($sql);
					$json = '';
					if (is_array($nestFormData)){
					    $temp='';
					    foreach ($nestFormData as $k=>$v){
					        // inbindval
					        $temp[$v['bindval']][]=$v;
					    }
					    $json = json_encode($temp);
					}
					
					//$json = is_array($nestFormData) ? json_encode($nestFormData) : '';
					// 获取主表的套表关联信息。
					// style="display:none"
					$allSaveBtn .= <<<EOF
<!--	套表一级子表关联数据动态修改	-->
				<script>
$(function(){
		var box = navTab.getCurrentPanel();
		var autoJson = '{$json}';
		var curAction = '{$actionName}';
		var curMain = '{$main}';
		var scriptTag = '{$scriptTag}';
		if(isNullorEmpty(autoJson)){
          autoJson = $.parseJSON(autoJson);
			$.each(autoJson , function(key , val){
				var obj = $('[name="'+key+'"]',box);
				if(typeof(obj) != undefined){
					//obj.eq(0).off('change');
					obj.eq(0).on('change' , function(){
						///////////////////
						var fieldName = key;
						var fieldValue = $(this).val();
						var tag = $(this).closest('form').attr('tabfor');
		                var mainid =  $(this).closest('form').find('input[name="id"]').val();
		                mainid = isNullorEmpty(mainid)?mainid:'';
						var mainprojectid =  $(this).closest('form').find('input[name="projectid"]').val();
				        projectid = isNullorEmpty(mainprojectid)?mainprojectid:'';
		                var parames = new Object();
		                parames.action=curAction;
		                parames.main=curMain;
		                parames.val=fieldValue;
		                parames.filedval=fieldName;
		                parames.bindrdid=mainid;
						if(fieldValue){
							$.ajax({
									url:TP_APP+'/Common/getAutoFormTabs',
									data:{'action':curAction,'main':curMain,'val':fieldValue,'filedval':fieldName,'projectid':projectid,'bindrdid':mainid},
									type:'post',
									dataType:'json',
									success:function(msg){
										if(isNullorEmpty(msg.data)){
											var obj = $('#tabsContent_'+tag);
				                            var header = obj.find('div.tabsHeader');
				                            $.each(msg.data , function(key , val){
				                                $.each(val , function(k , v){
				                                      // 改url地址
				                                      var tempId = k+'_'+tag;
				                                      var curOprateObj = $('#'+tempId, header);
				                                      curOprateObj.attr('href',v);
				                                      var index = curOprateObj.closest('li').index();
				console.log(tempId);
				                                      // 重新请求子表页面
		                                              $('.tabsContent',obj).children().eq(index).loadUrl(v,{},function(){});
						                        });
						                    });
										}
									}
								});
						}
					});
				}
			});
		}
});
					
					
</script>
EOF;
				}
			}
			// 隐藏域的生成
			if( $isRelationMainForm != false && !$_GET['main']){
				/**
				 * 主入口代码处理
				 */
				if(!$main)	$main = $actionName;
				$formtype = $isRelationMainForm['formtype'];
				if($formtype==2){
					$relationArr = getNestedForm($actionName );
					// 检查套表的构成项是否为关系型表单
					foreach ($relationArr as $key => $val){
						$tempData = getComForm($val );
						if( is_array( $tempData ) ) {
							$relationArr = array_merge($relationArr , $tempData);
						}
					}
				}else{
					$relationArr = getComForm($main );
				}
					
				if(is_array($relationArr)){
					$actionlist =  join(',',$relationArr);//array_reduce($relationArr , 'reduceFunc' , $main));
				}else{
					$actionlist = $main;
				}
				$relationArr['0'] = $main;
			}else{
				/**
				 * 当前为关系型表单的非入口
				 * 二级选项
				 * 生成隐藏域
				 */
				// mian参数有值，表示它是从主入口进来的
				// 那就以main的值查询其所有子级
				$relationArr =getNestedForm($main);
				if(!is_array($relationArr) || count($relationArr) ==0){
					$relationArr = getComForm($main);
				}else{
					// 检查套表的构成项是否为关系型表单
					foreach ($relationArr as $key => $val){
						$tempData = getComForm($val);
						if( is_array( $tempData ) ) {
							$relationArr = array_merge($relationArr , $tempData);
						}
					}
				}
				if(is_array($relationArr)){
					$actionlist = join(',',$relationArr);//array_reduce($relationArr , 'reduceFunc' , $main);
				}else{
					$actionlist = $main;
				}
				$relationArr['0'] = $main;
			}
			unset($temp);
			foreach ($relationArr as $k=>$v){
				$temp[]=$v;
			}
			unset($relationArr);
			$relationArr = $temp;
			if( (in_array( $actionName , $relationArr ) || $actionName == $main) && $oprateType!='view' ){
				$formParame = " autoformsubmit=\"submit\"";
			}
			
			/**
			 * 通用表单产的隐藏域组件及值html组装
			 */
			$hiddens = <<<EOF
			<input type="hidden" name="__actionnamelist__" value="{$actionlist}" />
			<input type="hidden" name="__main__" value="{$main}" />
			<input type="hidden" name="__selfaction__" value="{$selfactionname}" />
			<input type="hidden" name="__selfoprate__" value="{$oprateType}" />
EOF;
			
			/**
			 * 处理url重构值
			 */
			// 获取当前表单的类型
			
			$manageModel = M('mis_dynamic_form_manage');
			$formTypeMap['actionname'] = $actionName;
			$formTypeData = $manageModel->where($formTypeMap)->select();
			if( is_array($formTypeData) && count($formTypeData) > 1){
				throw new NullDataExcetion("动态重构表单的组件项：当前action{$actionName} 在manage表中存在多条记录，");
			}
			if( is_array($formTypeData) && count($formTypeData) == 0){
				throw new NullDataExcetion("动态重构表单的组件项：当前action{$actionName} 在manage表中无记录，");
			}
// 			if( $isRelationMainForm != false){
// 			$formtype = $isRelationMainForm['formtype'];
			$formTypeData = reset($formTypeData);
			$isaudit = $formTypeData['isaudit'];
			switch ($oprateType){
				case 'add':
					if(!$_GET['main'] && $isRelationMainForm == false ){
						// 是关系表单但没有入口，也不是主表
						if($isaudit){
							// 审批 // 修改：关系型表单的非主表提交地址为默认、 nbmxkkj@20150527 2143
							//$restructureUrl = 'updateControll';
						}
					}else{
						if($formtype==0 && $isRelationMainForm != false){
							$restructureUrl = 'updateControll';
						}else{
							$restructureUrl = 'updateControll';
							if($isaudit){
								// 审批
								$restructureUrl = 'updateControll';
							}
						}
					}
					break;
				case 'edit':
					if(!$_GET['main'] && $isRelationMainForm == false ){
						// 是关系表单但没有入口，也不是主表
					}else{
						$restructureUrl = 'updateControll';
						if($isaudit){
							// 审批
							$restructureUrl = 'updateControll';
						}
					}
					break;
				case 'auditEdit':
					$restructureUrl = 'updateControll';
					if($isaudit){
						// 审批
						$restructureUrl = 'updateControll';
					}
					break;
				case 'view':
					$restructureUrl = 'updateControll';
					if($isaudit){
						// 审批
						$restructureUrl = 'updateControll';
					}
					break;
			}
			$selfactiontype = $isaudit ? 'audit':'common';
			$hiddens .= <<<EOF
			
			<input type="hidden" name="__selfactiontype__" value="{$selfactiontype}" />
EOF;
// 			$restructureUrl
		}
	}catch (Exception $e){
		echo 'SSSSSSSSSSSSSSSSSSSSSSS'. $e->__toString();
	}
	
	if($oprateType == 'add'){
	$hiddens .= <<<EOF
		<input type="hidden" name="bindid" value="{$_GET['bindid']}" />
		<input type="hidden" name="relationmodelname" value="{$_GET['bindaname']}" />
EOF;
	}
// 	$retData['allSaveBtn'] = $allSaveBtn;		// 一键提交
// 	$retData['hidden'] = $hiddens;		// 隐藏属性
// 	$retData['url'] = $restructureUrl;	// 重构后的url
// 	$retData['parame'] = $formParame;		//	备用的表单属性
	
	$retData[0] = $allSaveBtn;		// 一键提交
	$retData[1] = $hiddens;		// 隐藏属性
	$retData[2] = $restructureUrl;	// 重构后的url
	$retData[3] = $formParame;		//	备用的表单属性
	
	return $retData;
}

/**
 * 获取附件数量
 * @Title: getDTCount
 * @Description: todo(这里用一句话描述这个方法的作用) 
 * @param unknown $tableid
 * @param unknown $tablename
 * @param unknown $subid
 * @param unknown $fieldname  
 * @author quqiang 
 * @date 2015年3月30日 上午10:03:54 
 * @throws
 */
function getDTCount($tableid , $tablename , $subid='' , $fieldname=''){
	$DTArr = A('Common')->getDTAttachedRecordList($tableid,$tablename,$subid,$fieldname,true,true,false);
	return count($DTArr);
}

/**
 * 添加和获取页面Trace记录
 * @param string $value 变量
 * @param string $label 标签
 * @param string $level 日志级别
 * @param boolean $record 是否记录日志
 * @return void
 */
function trace($value='[think]',$label='',$level='DEBUG',$record=false) {
	static $_trace =  array();
	$debugInfo = debug_backtrace();
	if('[think]' === $value){ // 获取trace信息
		return $_trace;
	}else{
		$info   =   ($label&&$label!="showline"?$label.' ： ':'').print_r($value,true);
		if($level=='DEBUG' && $label=="showline"){ //调试时显示行号和文件
			$info.=" (".$debugInfo[0]["file"]." 行号:".$debugInfo[0]["line"].")";
		}
		if(APP_DEBUG && 'ERR' == $level) {// 调试模式ERR抛出异常
			throw_exception($info);
		}
		if(!isset($_trace[$level])) {
			$_trace[$level] =   array();
		}
		$_trace[$level][]   = $info;
		if((defined('IS_AJAX') && IS_AJAX) || !C('SHOW_PAGE_TRACE')  || $record) {
			Log::record($info,$level,$record);
		}
	}
}

/**
 * @Title: getFieldData
 * @Description: todo(快速获取数据)
 * @param	string	$fieldName	检索字段名
 * @param	string	$table			被检索表名
 * @param	array|string	$map	检索条件
 * @param	boolean	$isline		多行数据时是否合并为一行显示，默认 false
 * @param	int			$limit		查询数据量，默认 1
 * @param	string		$character	合并数据时的分隔符，默认为英文逗号
 * @author quqiang
 * @date 2015年4月8日 下午5:27:15
 * @throws
 */
function getFieldData($fieldName , $table , $map , $isline=false , $limit=1 , $character=','){
	$ret ='';
	//判断参数是否存在。
	if($fieldName && $table ){
		$model = D($table);
		// 	"'.$key.'='.$value.' and 1=1 '.$var.'"
		if(empty($map)){
			$data = array();
			$data = $model->getField($fieldName , $limit);
		}else{
			$map = html_entity_decode($map);
			$data = $model->where($map)->getField($fieldName , $limit); 
		}
// 		echo $model->getLastSql();
		if($isline){
		$ret = is_array($data) ? join($character , $data) : $data;
		}else{
			$ret = $data;
		}
	}

	return $ret;
}

function getFieldsName($value,$fields,$fieldName , $table  , $isline=true , $limit=1000 , $character=','){
	if(!empty($value)){
		if(strpos( $value , ',' )){
			$valueStr=explode(',', $value);
			$map = $fields." in ('".join("','", $valueStr)."')";
		}else{
			$map = $fields."='".$value."'";
		}
		$ret ='';
		//判断参数是否存在。
		if($fieldName && $table ){
			$model = D($table);
			// 	"'.$key.'='.$value.' and 1=1 '.$var.'"
			if(empty($map)){
				$data = array();
				$data = $model->getField($fieldName , $limit);
			}else{
				$map = html_entity_decode($map);
				$data = $model->where($map)->getField($fieldName , $limit);
			}
			if($isline){
				$ret = is_array($data) ? join($character , $data) : $data;
			}else{
				$ret = $data;
			}
		}
	}else{
		return '';
	}
	return $ret;
}


function getTabsOrderno($tablename,$modelname){
	$scnmodel = D ( 'SystemConfigNumber' );
	//重新生成编号
	$ordernoInfo = $scnmodel->getOrderno($tablename,$modelname);
	//print_r($ordernoInfo);
	$condition=array();
	$condition['orderno']=$ordernoInfo['orderno'];
	$model=D($modelname);
	$list = $model->where($condition)->getField('id');
	if($list){
		return	getTabsOrderno($tablename,$modelname);
	}else{
		return $ordernoInfo['orderno'];
	}

}
/**
 * @Title: nvigateTO
 * @Description:临时表存储历史记录
 * @author liminggang
 * @date 2014-10-13 下午8:22:48
 * @paramate $model mix  $pkval int
 * @return $updateBackup(临时表关键查找信息);
 * @throws
 */
 function setOldDataToCache($modelobj,$vo){
	$updateTable=D($modelobj)->getTableName();
	$updateSave= base64_encode(serialize($vo));
	$randtable="databind_cache".mt_rand(1,1);//选择对应的内存表，减少并发压力
	$randnum=mt_rand(1000,9999);//随机数号；通过表名+随机数+操作人锁定唯一的记录
	$transeModel = new Model();
	$transeModel->startTrans();
		$creatable=M()->execute("CREATE TABLE if not exists `".$randtable."` (
					  `id` int(10) NOT NULL AUTO_INCREMENT,
					  `backupdata` varchar(10240) DEFAULT NULL,
				      `tablename` varchar(100) DEFAULT NULL,
					  `randnum` int(4) DEFAULT NULL,
					  `createid` int(10) DEFAULT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;");
		if($creatable===false){
			$transeModel->rollback();
			$msg = "创建缓存表失败！";
			throw new NullDataExcetion($msg);
		}
		$cratevo=M()->execute("INSERT INTO  ".$randtable." (`tablename`,`backupdata`,`randnum`,`createid`)VALUES('".$updateTable."','".$updateSave."','".$randnum."','".$_SESSION[C('USER_AUTH_KEY')]."');");
		if($cratevo===false){
			$transeModel->rollback();
			$msg = "插入缓存数据失败！";
			throw new NullDataExcetion($msg);
		}
		//echo M()->getLastSql();
		$updateBackup=array('randtable'=>$randtable,'tablename'=>$updateTable,'randnum'=>$randnum,'createid'=>$_SESSION[C('USER_AUTH_KEY')]);
		$transeModel->commit();
		//$this->success("操作成功", '', array('type' => 1, 'nodename' => $this->nodeName , 'tablename'=>$primaryname,'tpltype'=>$this->tpltype));
	return $updateBackup;
}

function getMillisecond() {
	list($s1, $s2) = explode(' ', microtime()); 
	return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 10000); 
}

/**
 * numToCh数字转中文数字
 * @param 
 * @param 
 * @return string
 */
function numToCh($num, $m = 2) {
	switch($m) {
		case 0:
			$CNum = array(
			array('零','壹','贰','叁','肆','伍','陆','柒','捌','玖'),
			array('','拾','佰','仟'),
			array('','萬','億','萬億')
			);
			break;
		case 1:
			$CNum = array(
			array('〇','一','二','三','四','五','六','七','八','九'),
			array('','','',''),
			array('','','','')
			);
			break;
		default:
			$CNum = array(
			array('零','一','二','三','四','五','六','七','八','九'),
			array('','十','百','千'),
			array('','万','亿','万亿')
			);
			break;
	}
	// $cNum = array('零','一','二','三','四','五','六','七','八','九');

	if (is_integer($num)) {
		$int = (string)$num;
	} else if (is_numeric($num)) {
		$num = explode('.', (string)floatval($num));
		$int = $num[0];
		$fl  = isset($num[1]) ? $num[1] : FALSE;
	}
	// 长度
	$len = strlen($int);
	// 中文
	$chinese = array();

	// 反转的数字
	$str = strrev($int);
	for($i = 0; $i<$len; $i+=4 ) {
		$s = array(0=>$str[$i], 1=>$str[$i+1], 2=>$str[$i+2], 3=>$str[$i+3]);
		$j = '';
		// 千位
		if ($s[3] !== '') {
			$s[3] = (int) $s[3];
			if ($s[3] !== 0) {
				$j .= $CNum[0][$s[3]].$CNum[1][3];
			} else {
				if ($s[2] != 0 || $s[1] != 0 || $s[0]!=0) {
					$j .= $CNum[0][0];
				}
			}
		}
		// 百位
		if ($s[2] !== '') {
			$s[2] = (int) $s[2];
			if ($s[2] !== 0) {
				$j .= $CNum[0][$s[2]].$CNum[1][2];
			} else {
				if ($s[3]!=0 && ($s[1] != 0 || $s[0]!=0) ) {
					$j .= $CNum[0][0];
				}
			}
		}
		// 十位
		if ($s[1] !== '') {
			$s[1] = (int) $s[1];
			if ($s[1] !== 0) {
				$j .= $CNum[0][$s[1]].$CNum[1][1];
			} else {
				if ($s[0]!=0 && $s[2] != 0) {
					$j .= $CNum[0][$s[1]];
				}
			}
		}
		// 个位
		if ($s[0] !== '') {
			$s[0] = (int) $s[0];
			if ($s[0] !== 0) {
				$j .= $CNum[0][$s[0]].$CNum[1][0];
			} else {
				// $j .= $CNum[0][0];
			}
		}
		$j.=$CNum[2][$i/4];
		array_unshift($chinese, $j);
	}
	$chs = implode('', $chinese);
	if ($fl) {
		$chs .= '点';
		for($i=0,$j=strlen($fl); $i<$j; $i++) {
			$t = (int)$fl[$i];
			$chs.= $str[0][$t];
		}
	}
	return $chs;
}

/**
 * numToCh数字转中文数字
 * @param
 * @param
 * @return string
 *
 */
function richtext2str($str,$length=0) {
	$str = strip_tags(htmlspecialchars_decode($str));
	if($length){
		missubstr($str,$length);
	}
	$str = str_replace("&nbsp", "", $str);
	return $str;
}
/**
 * unrichtext2str
 * @param richtext2str的转换函数
 * @return 
 */
function unrichtext2str($str){
	return $str;
} 
/**
 * 生成摘要，这里需要引入一个类
 * @param  string  $content='' 富文本内容源码
 * @param  integer $length=3000 摘要字数限制
 * @return [type]
 */
function generateDigest($content='',$length=3000){
		// 实例化Fl
		$flInstance = self::getInstance();
		// HTML词法分析
		$analyticResult = $flInstance->analytic_html($content);
	
		$result = '';
		$htmlTagStack = array();
	
		// 遍历词法分析的结果
		foreach ($analyticResult as $key => $item) {
			// 分析单个标签
			$tagAttr = $flInstance->analytic_html($item[0],2);
	
			// 开始标签，如：<p>、<div id="xx">
			if($item[1] == FL::HTML_TAG_START) {
				// 将不能自动闭合的标签压栈
				if(!$flInstance->analytic_html($tagAttr[1],4)) {
					$htmlTagStack[] = $tagAttr[1];
				}
			}
			// 结束标签
			elseif($item[1] == FL::HTML_TAG_END) {
				// 当前结束标签和栈顶的标签相同，则出栈该标签
				if($tagAttr[1] == $htmlTagStack[count($htmlTagStack) - 1]) {
					array_pop($htmlTagStack);
				}
			}
	
			// 拼接摘要
			$result .= $item[0];
	
			// 字数控制
			if(strlen($result) >= $length) {
				break;
			}
		}
	
		// 将没有闭合的标签，都闭合起来
		for ($i=count($htmlTagStack) - 1; $i >= 0 ; $i--) {
			$result .= ('</' . $htmlTagStack[$i] . '>');
		}
	
		// 生成最终摘要
		return $result;
}
/**
 * word_default_empty word标签空值时默认一个值
 * @param $str  原值
 * @param $default 默认值
 * @return string
 */
function wordEmpty($str,$default=""){
	if($str==="" || $str===NULL){
		return $default;
	}else{
		return $str;
	}
}
/**
 * 附加条件的数据解析。
 * @Title: appendConditionConfigResolve
 * @Description: todo(解析配置记录中的附加条件配置数据) 
 * @param array $data	数据记录中的附加条件配置
 * @return Ambigous <multitype:, string, array>  
 * @author quqiang 
 * @date 2015年4月27日 下午5:05:40 
 * @throws
 */
function appendConditionConfigResolve($data){
	$appendCondtionArr = '';
	if( !empty($data) ){
	$appendCondtion = unserialize(base64_decode($data) );
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
	}
	
	return $appendCondtionArr;
}

/**
 * 对二维数组进行排序
 * @Title: sortArray
 * @param $array
 * @param $keyid 排序的键值
 * @param $order 排序方式 'asc':升序 'desc':降序
 * @param $type  键值类型 'number':数字 'string':字符串
 * @example	$arr = array(array('id'=>1,'sort'=>2) , array('id'=>2,'sort'=>1));
 * 		sort_array($arr , 'sort','desc','number');
 * @author quqiang
 * @date 2015年5月12日 下午5:36:26
 * @throws
 */
function sortArray(&$array, $keyid, $order = 'asc', $type = 'number') {
	if (is_array($array)) {
		foreach($array as $val) {
			$order_arr[] = $val[$keyid];
		}
		$order = ($order == 'asc') ? SORT_ASC: SORT_DESC;
		$type = ($type == 'number') ? SORT_NUMERIC: SORT_STRING;
		array_multisort($order_arr, $order, $type, $array);
	}
}

/**
 * 
 * @Title: fileSuffix
 * @Description: todo(这里用一句话描述这个方法的作用) 
 * @param unknown $extension
 * @return string  
 * @author quqiang 
 * @date 2015年5月18日 下午3:23:49 
 * @throws
 */
function fileSuffix($extension , $ico=true){
	$extension = $extension ? $extension : 'folder';
	
	// 文件夹
	$arr['folder']=array(
			'cls'=>'icon-folder-close',
			'edit'=>'openFolder',
	);
	// 文本
	$arr['html']=array(
			'cls'=>'icon-file-alt',
			'edit'=>'editFile',
	);
	$arr['htm']=array(
			'cls'=>'icon-file-alt',
			'edit'=>'editFile',
	);
	$arr['js']=array(
			'cls'=>'icon-file-alt',
			'edit'=>'editFile',
	);
	$arr['css3']=array(
			'cls'=>'icon-file-alt',
			'edit'=>'editFile',
	);
	$arr['css']=array(
			'cls'=>'icon-file-alt',
			'edit'=>'editFile',
	);
	$arr['php']=array(
			'cls'=>'icon-file-alt',
			'edit'=>'editFile',
	);
	$arr['txt']=array(
			'cls'=>'icon-file-alt',
			'edit'=>'editFile',
	);
	$arr['log']=array(
			'cls'=>'icon-file-alt',
			'edit'=>'editFile',
	);
	// 图象
	$arr['png']=array(
			'cls'=>'icon-picture',
			'edit'=>'',
	);
	// 其它
	$arr['db']=array(
			'cls'=>'icon-file-alt',
			'edit'=>'',
	);
	$arr['md5']=array(
			'cls'=>'icon-file-alt',
			'edit'=>'',
	);
	if($ico){
		return $arr[$extension]['cls'].' '.$arr[$extension]['edit'];
	}else{
		return $arr[$extension]['edit'];
	}
	
}
/**
*	获取当前请求IP地址
*/
function getip(){
	$onlineip = '';
	if($_SERVER['HTTP_CLIENT_IP']){
		 $onlineip=$_SERVER['HTTP_CLIENT_IP'];
	}elseif($_SERVER['HTTP_X_FORWARDED_FOR']){
		 $onlineip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	}else{
		 $onlineip=$_SERVER['REMOTE_ADDR'];
	}
	return $onlineip;
}
/**
 * 对导入数据进行验证
 * @Title: sortArray
 * @param $type 验证类型
 * @param $value 验证值
 * @author 王昭侠
 * @date 2015年6月2日 上午10:00:00
 * @throws
 */
function importValidate($type, $value) {
	$bool = false;
	$str = '';
	if ( ! empty($value)) {
		switch ($validate){
			case "eamil":
				$str = "/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/";
				break;
			case "url";
				$str = "/^[a-zA-z]+:\/\/(\\w+(-\\w+)*)(\\.(\\w+(-\\w+)*))*(\\?\\S*)?$/";
				break;
			case "number";
				$str = "/^-?\d+$/";
				break;
			case "double";
				$str = "/^(-?\d+)(\.\d+)?$/";
				break;
			case "lettersonly";
				$str = "/^[A-Za-z]+$/";
				break;
		}
		$bool = preg_match($str,$value)?true:false;
	}
	return $bool;
}

/**
 * 密码从可见转为不可见
 * @Title: passwordChange
 * @Description: todo(这里用一句话描述这个方法的作用) 
 * @return string  
 * @author quqiang 
 * @date 2015年6月10日 下午7:17:00 
 * @throws
 */
function passwordChange($password){
	if($password){
		return '********';
	}else{
		return '';
	}
}

/**
 * 根据HTML代码获取word Xml格式内容
 *
 * @param string $html HTML内容
 */
function htmlToWordXml($html=""){
	$str = "";
	if( ! empty($html)){
		import('@.ORG.PHPWord', '', $ext='.php');
		import('@.ORG.HtmlToDocx');
		$pp = new HtmlToDocx();
		$template_word = $pp->save($html,UPLOAD_PATH_TEMP);
		$PHPWord = new PHPWord();
		$document = $PHPWord->loadTemplate($template_word);
		$str = $document->getStr();
		$xml = DOMDocument::loadXML($str, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
		$sectPr=$xml->getElementsByTagName("sectPr");
		$xml->getElementsByTagName("body")->item(0)->removeChild($sectPr->item(0));
		$str = $xml->saveXML();
		$startPos = strpos($str, "<w:body>")+8;
		$endPos = strpos($str, "</w:body>");
		$str = trim(substr($str, $startPos,$endPos-$startPos));
// 		for($i=0;$i<3;$i++){
// 			$endP = strrpos($str, "<w:p/>");
// 			$str = trim(substr($str, 0, $endP));
// 		}
// 		$str = trim(str_replace("<w:p/>", "", $str));//去掉换行符
		$document->unlinkFile();
		unlink($template_word);
	}
	return $str;
}

/**
 * @Title: getDBData
 * @Description:获取下级地址数据
 * @author quqiang
 * @date 2015-06-23 上午9:53:48
 * @param int $id	
 * @throws
 */
function getDBData($id=''){
	$showType = false;
	$_id = '';
	
	if($id === '' ){
		$_id = $_POST['parentid'];
		$showType= false;
	}else{
		$_id = $id;
		$showType= true;
	}
	if($_id === ''){
		return '';
	}
	$obj = M('mis_system_areas');
	$fields = 'id,name,parentid';
	$map['parentid'] = $_id;
	$data = $obj->where($map)->field($fields)->select();
	if($showType){
		return $data;
	}else{
		echo json_encode($data);
	}
}

/**
 * RGB转 十六进制
 * @param $rgb RGB颜色的字符串 如：rgb(255,255,255);
 * @return string 十六进制颜色值 如：#FFFFFF
 */
function RGBToHex($rgb){
	$regexp = "/^rgb\(([0-9]{0,3})\,\s*([0-9]{0,3})\,\s*([0-9]{0,3})\)/";
	$re = preg_match($regexp, $rgb, $match);
	$re = array_shift($match);
	$hexColor = "";
	$hex = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F');
	for ($i = 0; $i < 3; $i++) {
		$r = null;
		$c = $match[$i];
		$hexAr = array();

		while ($c > 16) {
			$r = $c % 16;

			$c = ($c / 16) >> 0;
			array_push($hexAr, $hex[$r]);

		}
		array_push($hexAr, $hex[$c]);

		$ret = array_reverse($hexAr);
		$item = implode('', $ret);
		$item = str_pad($item, 2, '0', STR_PAD_LEFT);
		$hexColor .= $item;
	}
	return $hexColor;
}

/**
 * 字符串转计算结果
 * @Title: strCalculate
 * @Description: todo(将字符串内计算标签中的内容做数学运算并返回结果)
 * @param string $str
 * @author quqiang
 * @date 2015年7月6日 下午4:59:40
 * @throws
 */
function strCalculate($str){
	// 非空验证
	if(empty($str)){
		return '';
	}
	// 非计算标签验证
	$reg = '/\<eval\>.*?\<\/eval\>/';
	$ret = preg_match($reg, $str);
	if($ret){
		$calculateReg = '/\<eval\>(.*?)\<\/eval\>/';
		$str = preg_replace_callback($calculateReg, function($vo){
			if($vo[1]){
				return eval("return $vo[1];");
			}else{
				return '';
			}
		}, $str);
	}
	return $str;
}

/**
 * 检查字符串内的特定值是否在被检查数组中
 * @Title: strReplaceCheck
 * @Description: todo(检查字符串内的特定值是否在被检查数组中,来源字符格式现只支持， =  、in 、>= and <= )
 * @param string $souceStr	来源字符，如： mis_auto_cbj.orderno='010101' ， mis_auto_cbj.id in('1','2','3','4','56','67'),  mis_auto_cbj.id >='2' and mis_auto_cbj.id <='10'  
 * @param string $checkArr	被检查数组 , 如： array('id'=>5 , 'orderno'=>'010101');
 * @author quqiang
 * @date 2015年7月13日 下午2:21:40
 * @example
 * <pre>
		 $souceStrEq="mis_auto_cbj.orderno ='010101'";
		$souceStrIn=" mis_auto_cbj.id in('1','2','3','4','56','67')";
		$souceStrAnd ="MIS_auto_cbj.id>='6' AND mis_auto_cbj.id<='10'";
		$checkArr = array('id'=>5 , 'orderno'=>'010101');
 		$ret = strReplaceCheck($souceStrEq , $checkArr);
 		$ret = strReplaceCheck($souceStrIn , $checkArr);
		$ret = strReplaceCheck($souceStrAnd , $checkArr);
		var_dump($ret);
		
 * </pre>
 * @throws
 */
function strReplaceCheck($souceStr , $checkArr){
	$checkedField = '';
	$checkedRet = false;
	// in(*) 格式
	$regIn='/in\\((.*?)\\)/i';
	
	// = 格式
	$regEq = "/[^>|<]='(.*?)'/i";
	
	// >= * and <= * 格式
	$regAnd = "/(.*)>='(.*)'\sand(.*)<='(.*)'/i";
	
	// 检查格式
	// eq , in , and 顺序检查 
	$ret = false;
	if(!$ret){
		// eq
		$ret = preg_match($regEq, $souceStr , $match , $flag);
		if($ret){
			// get field reg
			$fieldReg = '/[\.|\s](.*)[\s|=]/';
			preg_match($fieldReg, $souceStr , $matchField , $flag);
			if($matchField[1]){
				$checkedField = $matchField[1];
			}
			$match = $match[1];
			if($checkedField && $checkArr[$checkedField] ==$match ){
				$checkedRet = true;
			}
		}
	}
	if(!$ret){
		// in 
		$ret = preg_match($regIn, $souceStr , $match , $flag);
		if($ret){
			// get field reg
			$fieldReg = '/[\.](.*)\sin/';
			preg_match($fieldReg, $souceStr , $matchField , $flag);
			if($matchField[1]){
				$checkedField = $matchField[1];
			}
			$match = str_replace("'", "", $match[1] );
			
			if($checkedField && $checkArr[$checkedField]){
				$needCheckArr = explode(',', $match);
				if(in_array($checkArr[$checkedField], $needCheckArr)){
					$checkedRet = true;
				}
			}
		}
	}
	if(!$ret){
		// and
		$ret = preg_match($regAnd, $souceStr , $match , $flag);
		if($ret){
			// get field reg
			$tempStr = $souceStr;
			$fieldReg = "/[\.](.*?)(>|<)/i";
			preg_match($fieldReg, $souceStr , $matchField , $flag);
			if($matchField[1]){
				$checkedField = $matchField[1];
			}
			
			if($match[2] && $match[4]){
				$match=array($match[2] , $match[4]);
			}else {
				unset($match);
			}
			
			// 验证方式为 ： 变量 >= 值 and 变量 <= 值。
			if($checkedField && $match && $checkArr[$checkedField]){
				if($checkArr[$checkedField] >=$match[0] && $checkArr[$checkedField] <= $match[1]){
					$checkedRet = true;
				}
			}
		}
	}
	if(!$ret){
		return false;
	}
	return $checkedRet;
}

/**
 * 避免二次解码导致加号丢失
 * 
 * @author yige
 * @link http://yige.org
 * @param string $string 要解码的字符串
 * @return string
 */
function _urldecode($string) {
	if(preg_match('#%[0-9A-Z]{2}#isU', $string) > 0) {
		$string = urldecode($string);
	}
	
	return $string;
}

/**
 * 数据请求
 *
 * @author wzx
 * @param array $data 传输的数据
 * @param string $string 请求的地址
 * @return $result
 */
function tmlCurl($data=array(),$va_url=""){
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $va_url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_TIMEOUT, 1);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false );
	$result = curl_exec($curl);
	curl_close($curl);
	return $result;
}

/**
 * 写系统日志
 *
 * @author wzx
 * @param string $msg 日志内容
 */
function writeLog($msg){
	$logFile = ROOT."/systemLog.txt";
	$msg = date('Y-m-d H:i:s').' >>> '.$msg."\r\n";
	file_put_contents($logFile,$msg,FILE_APPEND);
}

/**
 * 将字符串时间转换为时间戳，如果字符格式错误转换失败
 * @Title: stringToTime
 * @Description: todo(这里用一句话描述这个方法的作用)
 * @param string $str	字符串时间
 * @return string|Ambigous <'', number>
 * @author quqiang
 * @date 2016年1月20日 下午5:00:11
 * @throws
 */
function stringToTime($str){
	if(!$str)
		return '';
	$reg = '/((\d{4}-\d{2}-\d{2})|(\d{2}:\d{2}:\d{2})|(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}))$/';
	$ret = preg_match($reg, $str , $match);
	return $ret ? strtotime($str) : '';
}
/**
 * 首页导航组件显示
 * @Title: getSesstionContion
 * @Description: todo($data【导航组件对应session名称】需要动态获取) 
 * @param unknown $id
 * @return boolean  
 * @author xyz
 * @date 2016年4月12日 下午2:46:32 
 * @throws
 */
function getSesstionContion($id){
	$username = C('USER_AUTH_KEY');
	$data = array(
			'59'=> $username,
			'60'=>'misautogrt_company',
			'63'=>'misautogrt_jiashicang',
			'66'=>'misautogrt_hangyezhixun',
			'67'=>'misautogrt_gongnenghezi',
	);
	if($_SESSION.a != 1 && !$_SESSION[$data[$id]]){
		return false;
	}
	return true;
}
?>