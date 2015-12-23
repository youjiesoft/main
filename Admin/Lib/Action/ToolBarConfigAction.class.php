<?php
class ToolBarConfigAction extends CommonAction{
	
	public function index(){
		$name = $this->getActionName();
		if (isset ($_SESSION[C('USER_AUTH_KEY')])) {
			//初始化生成左边树，默认打开节点，跳转不再刷新树
			$model = D('SystemConfigDetail');
			if($_REQUEST['jump']){
				$modelName = $_REQUEST['model'];
			}else{
				$models = D('SystemConfigNumber');
				$returnarr = $models->getRoleTree('ToolBarConfigBox');//左边树
				$firstDetail = $models->firstDetail;
				$this->assign('returnarr',$returnarr);
				if($_REQUEST['model']){
					$check = getFieldBy($_REQUEST['model'], 'name', 'id', 'node');
					$this->assign('check',$check);
					$modelName = $_REQUEST['model'];
				}else{
					$this->assign('check',$firstDetail['check']);
					$modelName = $firstDetail['name'];
				}
			}
			//赋值给toolbar调用
			$_REQUEST['modelname'] = $modelName;
			$this->assign('modelname',$modelName);
			$scdmodel = D('SystemConfigDetail');
			//读取列名称数据(按照规则，应该在index方法里面)
			$detailList = $scdmodel->getDetail($name);
			if ($detailList) {
				$this->assign ( 'detailList', $detailList );
			}
			// 		//扩展工具栏操作
			$toolbarextension = $scdmodel->getDetail($name,true,'toolbar');
			if ($toolbarextension) {
				$this->assign ( 'toolbarextension', $toolbarextension );
			}
			// 选中模型的toolbar配置集
			$scdmodel = D('SystemConfigDetail');
			$toollist = $scdmodel->getDetail($modelName,false,'toolbar');
			$list = $this->htmlaspilt($toollist);
			
			//dump($toollist);
			$this->assign('list',$list);
			if ($_REQUEST['jump']){
				$this->display("indexview");
			}else{
				$this->display();
			}
			
		}
	}
	public function add(){
		//获取
		$name = $_REQUEST['modelname'];
		$this->assign('modelname',$name);
		//获取除本按钮外的所有按钮(页面)
		$scnmodel = D("SystemConfigDetail");
		$scntoorlist = $scnmodel->getDetail($name,false,'toolbar');
		$bottunlist = array();
		foreach($scntoorlist as $k=>$v){
			$bottunlist[$k]['name'] = substr($k,3);
			$newtitle = $v['html'];
			$preg = '/<\/span>(.*?)<\/span>/';
			preg_match($preg,$newtitle,$match);
			$bottunlist[$k]['title'] = $match[1];
		}
		$this->assign('bottunlist',$bottunlist);
		
		//获取按钮样式
		$model = D("Selectlist");
		$list = require $model->GetFile();
		$this->assign('stylecss',$list['dialogstyle']['dialogstyle']);
		$this->display();
	}
	public function insert(){
		$data = $_POST;
		$key = $data['jskey'];
		//----开始--取出toolbar需要的元素---
		$newdata['ifcheck']=$data['ifcheck'];
		$newdata['permisname']=$data['permisname'];
		if($data['extendurl']) $newdata['extendurl']=$data['extendurl'];
		$newdata['html']=html_entity_decode($data['html']);
		$newdata['shows']=$data['shows'];
		$newdata['sortnum']=$data['sortnum'];
		$newdata['rules']=$data['rule'];
		if($data['ismore']) $newdata['ismore']=$data['ismore'];
		if($data['rules']) {
			$newdata['disabledrules']=str_replace ( "&#39;", "'", html_entity_decode ($_POST ['rules']));	
			$newdata['rulesinfo'] = $data['rulesinfo'];
			$newdata['showrules'] = str_replace ( "&#39;", "'", html_entity_decode ( $_POST ['showrules'] ) );
			$newdata['disabledmap'] = $this->addresultchange($data['rulesinfo']);
		}
		
		//----结束--取出toolbar需要的元素---
		//判断是否存在扩展toolbar 存在则写入 不存在 就看toolbar是否存在 存在写入 不存在看是否有相应的文件夹，没有则创建 ，再创建文件写入数据
		$path = DConfig_PATH . "/Models";
		$file1 = $path.'/'.$data['modelname'].'/toolbar.extension.inc.php';
		$file2 = $path.'/'.$data['modelname'].'/toolbar.extensionExtend.inc.php';
		$listall = require $file1;
		$model = D($this->getActionName());
		if(is_file($file2)){
			$list = require $file2;
			$list[$key]=$newdata;
			$model->writeoveronly($file2,$list);
		}elseif(is_file($file1)){
			$list = require $file1;
			$list[$key]=$newdata;
			$model->writeoveronly($file1,$list);
		}else{
			$list[$key]=$newdata;
			if (!is_dir( $path.'/'.$data['modelname'] )) {
				$this->make_dir( $path.'/'.$data['modelname'],0777);
			}
			$model->writeoveronly($file1,$list);
		}
		//入数据库
		// 1.将修改的数据入库
		$dbdata = $newdata;
		$dbdata['modelname'] = $data['modelname'];
		$dbdata['jskey'] = $key;
		$dbmap['modelname'] = $data['modelname'];
		$dbmap['jskey'] = $key;
		$rs = $model->where($dbmap)->find();
		if($rs){
			$this->error("该model已有同名的按钮标识");
		}else{
			$ret = $model->add($dbdata);
			//echo $model->getlastsql();
		}
		if(false===$ret){
			$this->error();
		}
		//2.保持数据完整，保证该model下的数据都在数据库
		foreach($listall as $key=>$val){
			if($key==$id){
				continue;
			}else{
				$temp = $val;
				$temp['jskey'] = $key;
				$temp['modelname'] = $data['modelname'];
				$tmap['jskey'] = $key;
				$tmap['modelname'] = $data['modelname'];
				$rs = $model->where($tmap)->find();
				if(!$rs){
					$ret = $model->add($temp);
					if(false===$ret){
						$this->error();
					}
				}
		
			}
				
		}
		
		
		
		$this->success('操作成功！');
	}
	
	public function edit(){
		//获取选中记录的数据
		$modelname = $_GET['modelname'];
		$this->assign('modelname',$modelname);
		$scdmodel = D('SystemConfigDetail');		
		$toollist = $scdmodel->getDetail($modelname,false,'toolbar');
		$model = D($this->getActionName());
		
		$list = $this->htmlaspilt($toollist);
		$vo = array();
		foreach($list as $k=>$v){
			if($k==$_GET['id']){
				$vo=$v;
			}
		}
		$dbmap['modelname'] = $modelname;
		$dbmap['jskey'] = $_GET['id'];
		$dblist = $model->where($dbmap)->find();
		//echo $model->getlastsql();
		if($dblist){
				$dblist['rule'] = $dblist['rules'];
				$dblist['rules'] = $dblist['disabledrules'];
				$dblist['html'] = str_replace(array('__URL__','__APP__','__MODULE__'),array('--URL--','--APP--','--MODULE--'),$dblist['html']);
				$vo = $dblist;
		}
		$this->assign('id',$_GET['id']);
		$this->assign('vo',$vo);
		$this->display();
	}
	
	public function update(){
		$data = $_POST;	
		$key = $data['id'];
		//----开始--取出toolbar需要的元素---
		$newdata['ifcheck']=$data['ifcheck'];
		$newdata['permisname']=$data['permisname'];
		if($data['extendurl']) $newdata['extendurl']=$data['extendurl'];
		$newdata['html']=html_entity_decode($data['html']);
		$newdata['shows']=$data['shows'];
		$newdata['sortnum']=$data['sortnum'];
		$newdata['rules']=$data['rule']; 
		if($data['ismore']) $newdata['ismore']=$data['ismore'];
		if($data['rules']) {
			$newdata['disabledrules']=str_replace ( "&#39;", "'", html_entity_decode ($_POST ['rules']));
			$newdata['rulesinfo'] = $data['rulesinfo'];
			$newdata['showrules'] = str_replace ( "&#39;", "'", html_entity_decode ( $_POST ['showrules'] ) );
			$newdata['disabledmap'] = $this->addresultchange($data['rulesinfo']);
		}else{
			$newdata['disabledrules']='';
			$newdata['rulesinfo'] = '';
			$newdata['showrules'] = '';
			$newdata['disabledmap'] = '';
		}
		//----结束--取出toolbar需要的元素---
		//重组数据 区分开始extension还是extensionExtend的数据
		$path = DConfig_PATH . "/Models";
		$file1 = $path.'/'.$data['modelname'].'/toolbar.extension.inc.php';
		$file2 = $path.'/'.$data['modelname'].'/toolbar.extensionExtend.inc.php';
		$listall=require $file1;
		$listextend = array();//extensionExtend.inc.php数据
		if(is_file($file2)){
			$listextend = require $file2;
		}		
		$listextension = $listall; //extension.inc.php 数据
		foreach($listextend as $k=>$v){
			foreach($listextension as $k2=>$v2){
				if($k==$k2) unset($listextension[$k2]);
			}
		}
		//区分页面 在对应页面修改
		$model = D($this->getActionName());
		if(in_array($key,array_keys($listextend))){
			//unset($listextend[$_POST['key']]);
			$listextend[$key] = $newdata;
			$model->writeoveronly($file2,$listextend);
		}else{
			//unset($listextension[$_POST['key']]);
			$listextension[$key] = $newdata;
			if(is_file($file2)){
				$model->writeovertwo($file1,$listextension);
			}else{
				$model->writeoveronly($file1,$listextension);
			}
			
		}
		//入数据库
		// 1.将修改的数据入库
		$dbdata = $newdata;
		$dbdata['modelname'] = $data['modelname'];
		$dbdata['jskey'] = $key;
		$dbmap['modelname'] = $data['modelname'];
		$dbmap['jskey'] = $key;
		
		$rs = $model->where($dbmap)->find();
		if($rs){
		$ret = $model->where($dbmap)->save($dbdata);
		}else{
			$ret = $model->add($dbdata);
		}
		//print_r($dbdata);
		//echo $model->getlastsql();
		if(false===$ret){
			$this->error();
		}
		//2.保持数据完整，保证该model下的数据都在数据库
		foreach($listall as $key=>$val){
			if($key==$id){
				continue;
			}else{
				$temp = $val;
				$temp['jskey'] = $key;
				$temp['modelname'] = $data['modelname'];
				$tmap['jskey'] = $key;
				$tmap['modelname'] = $data['modelname'];
				$rs = $model->where($tmap)->find();
				if(!$rs){
					$ret = $model->add($temp);
					if(false===$ret){
						$this->error();
					}
				}
				
			}
			
		}
		$this->success('操作成功！');
	}
	public function delete(){
		
		$data = $_REQUEST;
		$key = $data['id'];
		//重组数据 区分开始extension还是extensionExtend的数据
		$path = DConfig_PATH . "/Models";
		$file1 = $path.'/'.$data['modelname'].'/toolbar.extension.inc.php';
		$file2 = $path.'/'.$data['modelname'].'/toolbar.extensionExtend.inc.php';
		$listall=require $file1;
		$listextend = array();//extensionExtend.inc.php数据
		if(is_file($file2)){
			$listextend = require $file2;
		}
		$listextension = $listall; //extension.inc.php 数据
		foreach($listextend as $k=>$v){
			foreach($listextension as $k2=>$v2){
				if($k==$k2) unset($listextension[$k2]);
			}
		}
		//区分页面 删除
		$model = D($this->getActionName());
		if(in_array($key,array_keys($listextend))){
			unset($listextend[$key]);
			$model->writeoveronly($file2,$listextend);
		}else{
			unset($listextension[$key]);
			$model->writeovertwo($file1,$listextension);
		}
		//数据库删除
		$tmap['jskey'] = $key;
		$tmap['modelname'] = $data['modelname'];
		$model->where($tmap)->delete();
		$this->success('操作成功！');
	}
	/**
	 * @Title: htmlaspilt
	 * @Description: todo(拆分toolbar的元素) 
	 * @param  htmlaspilt  需要拆分的toolbar数组
	 * @author 谢友志 
	 * @date 2014-12-10 下午9:18:33 
	 * @throws
	 */
	public function htmlaspilt($toollist){
		$list = array();
		foreach($toollist as $k=>$v){
			$toollist[$k]['html'] = str_replace(array('__URL__','__APP__','__MODULE__'),array('--URL--','--APP--','--MODULE--'),$v['html']);
// 			$new = array();
// 			//ischeck 是否检查
// 			$new['ischeck']=$v['ischeck']?'是':'否';
// 			//session检查
// 			$new['permisname']=$v['permisname'];
// 			//是否显示
// 			$new['shows']=$v['shows']?'是':'否';
// 			//按钮排序
// 			$new['sortnum']=$v['sortnum'];
// 			//规则
// 			$new['rules']=$v['rules'];
// 			//扩展url
// 			$new['extendurl']=substr($v['extendurl'],1,-1);
// 			//更多
// 			$new['ismore'] = $v['ismore'];
// 			//开始 对html元素拆分
// 			//href 基础url
// 			$preg1 = '/.*?href="(.*?)(\/#.*)?"/';
// 			preg_match($preg1,$v['html'],$href1);
// 			//title 标签名称
// 			$preg2 = '/.*?title="(.*?)"/';
// 			preg_match($preg2,$v['html'],$title);
			//按钮名称
			$preg3 = '/<\/span>(.*?)<\/span>/';
			preg_match($preg3,$v['html'],$name);
			$toollist[$k]['name'] = $name[1];
// 			//target
// 			$preg4 = '/.*?target="(.*?)"/';
// 			preg_match($preg4,$v['html'],$dorn);
// 			//当为dialog是会多2个参数 高度和遮挡层
// 			if($dorn[1]=='dialog'){
// 				//高度
// 				$preg6 = '/height="(\d*)"/';
// 				preg_match($preg6,$v['html'],$height);
// 				$new['height']=$height[1];
// 				//遮挡层
// 				$preg7 = '/mask="(.*?)"/';
// 				preg_match($preg7,$v['html'],$mask);
// 				$new['mask']=$mask[1];
// 			}
// 			//rel
// 			$preg5 = '/.*?rel="(.*?)"/';
// 			preg_match($preg5,$v['html'],$rel);
// 			$new['dialogornavtab']=$dorn[1];
// 			if($new['dialogornavtab']=='dialog'){
// 				$preg6 = '/.*?hight="(.*?)"/';
// 				preg_match($preg5,$v['html'],$rel);
// 			}
// 			//样式stylecss
// 			$preg8 = '/<span\s*class="(.*?)"\s*>/';
// 			preg_match($preg8,$v['html'],$style);
// 			$stylestr = trim(str_replace(array('icon_lrp','icon '),array('',''),$style[1]));
// 			$new['stylecss']=$stylestr;
// 			$new['title']=$title[1];
// 			$new['href']=str_replace(array('__URL__','__APP__','__MODULE__'),array('--URL--','--APP--','--MODULE--'),$href1[1]);
// 			$new['name']=$name[1];
// 			$new['rel'] = str_replace(array('__URL__','__APP__','__MODULE__'),array('--URL--','--APP--','--MODULE--'),$rel[1]);
// 			//拆分结束
			$new['css']=substr(trim($k),3);
			$list[$new['css']]=$new;
		}
		return $toollist;
	}
	public function buttonToPage(){
		//获取model
		$name = $_REQUEST['modelname'];
		$this->assign('modelname',$name);
		//获取该model的toolbar信息		
		$scnmodel = D("SystemConfigDetail");
		$scntoorlist = $scnmodel->getDetail($name,false,'toolbar');
		//分解toolbar配置文件信息
		$info = $this->htmlaspilt($scntoorlist);
		foreach($info as $k=>$v){
			$preg4 = '/.*?target="(.*?)"/';
			preg_match($preg4,$v['html'],$dorn);
			$preg5 = '/.*?rel="(.*?)"/';
			preg_match($preg5,$v['html'],$rel);
			$info[$k]['dialogornavtab'] = $dorn[1];
			$info[$k]['rel'] = $rel[1];
		}
		//获取model拥有的页面
		$page = array();
		foreach($info as $k=>$v){
			if($v["rel"]&&in_array($v["dialogornavtab"],array("navTab","navtab"))){
				$page[$k]=$v['name'];
			}			
		}
		foreach($scntoorlist as $k=>$v){
			if($v['rightnotshow']){
				$notshow[$k] = explode(',',$v['rightnotshow']);
			}
		}
		$this->assign('notshow',$notshow);
		$this->assign('page',$page);
		$this->assign('info',$info);
		$this->display();
	}
	public function buttonToPageupdate(){
		$data = $_POST;
		$name = $_REQUEST['modelname'];
		$this->assign('modelname',$name);		
		//获取该model的toolbar信息		
		$scnmodel = D("SystemConfigDetail");
		$scntoorlist = $scnmodel->getDetail($name,false,'toolbar');
		//分解toolbar配置文件信息
		$info = $this->htmlaspilt($scntoorlist);
		foreach($info as $k=>$v){
			$preg4 = '/.*?target="(.*?)"/';
			preg_match($preg4,$v['html'],$dorn);
			$preg5 = '/.*?rel="(.*?)"/';
			preg_match($preg5,$v['html'],$rel);
			$info[$k]['dialogornavtab'] = $dorn[1];
			$info[$k]['rel'] = $rel[1];
		}
		//所有的页面
		$button = array();
		foreach($info as $k=>$v){
			if($v["rel"]&&in_array($v["dialogornavtab"],array("navTab","navtab"))){
				$button[]=substr($k,3);
			}
		}
		//需要显示的页面
		$right = $_POST["rightnotshow"];
		foreach($right as $key=>$val){
			foreach($val as $k=>$v){
				$right[$key][$k] = substr($v,3);
			}
			
		}
		//重构toolbar配置
		foreach($scntoorlist as $k=>$v){
			$scntoorlist[$k]["rightnotshow"]=implode(',',$button);
			$key = $k;
			foreach($right as $k1=>$v1){
				if($key==$k1){
					$temp = $button;
					foreach($v1 as $k2=>$v2){
						foreach($temp as $k3=>$v3){
							if($v2==$v3){
								unset($temp[$k3]);
							}
						}
					}
					$scntoorlist[$k]['rightnotshow'] = implode(',',$temp);
				}
				
			}
		}
		//重组数据 区分开始extension还是extensionExtend的数据
		$path = DConfig_PATH . "/Models";
		$file1 = $path.'/'.$data['modelname'].'/toolbar.extension.inc.php';
		$file2 = $path.'/'.$data['modelname'].'/toolbar.extensionExtend.inc.php';
		$listall=require $file1;
		$listextend = array();//extensionExtend.inc.php数据
		if(is_file($file2)){
			$listextend = require $file2;
		}
		$listextension = $listall; //extension.inc.php 数据
		foreach($listextend as $k=>$v){
			foreach($listextension as $k2=>$v2){
				if($k==$k2) unset($listextension[$k2]);
			}
		}
		//区分页面 在对应页面修改
		foreach($listextension as $k=>$v){
			$temp = $scntoorlist;
			foreach($temp as $k1=>$v1){
				if($k=$k1){
					$listextension[$k] = $temp[$k];
				}
			}
		}
		foreach($listextend as $k=>$v){
			$temp = $scntoorlist;
			foreach($temp as $k1=>$v1){
				if($k=$k1){
					$listextend[$k] = $temp[$k];
				}
			}
		}
		$model = D($this->getActionName());
		if(in_array($_POST['key'],array_keys($listextend))){
			unset($listextend[$_POST['key']]);
			$listextend[$key] = $newdata;
			$model->writeoveronly($file2,$listextend);
		}else{
			unset($listextension[$_POST['key']]);
			$listextension[$key] = $newdata;
			$model->writeovertwo($file1,$listextension);
		}
		//入数据库
		foreach($scntoorlist as $key=>$val){
			$val['modelname']= $data['modelname'];
			$val['jskey']= $key;
			$tmap['modelname']= $data['modelname'];
			$tmap['jskey']= $key;
			$rs = $model->where($tmap)->find();
			if($rs){
				$ret = $model->where($tmap)->save($val);
			}else{
				$ret = $model->add($val);
			}
			if(false===$ret){
				$this->error();
			}
		}
		$this->success('操作成功！');
		
	}
	/**
	 * @Title: addresultchange
	 * @Description: todo(解析rulesinfo生成一个toolbar配置rules格式的字符串) 
	 * @param unknown_type $rulesinfo
	 * @return unknown  
	 * @author 谢友志 
	 * @date 2015-6-16 上午9:17:17 
	 * @throws
	 */
	public function addresultchange($rulesinfo){
		$rulesarr = unserialize(base64_decode(base64_decode($rulesinfo)));
		$textchange = array(	
			1 => '==',	
			2 => '!=',	
			3 => 'in',	
			4 => 'notin',	
			5 => '>',	
			6 => '>=',	
			7 => '<',	
			8 => '<=',
		);
		$numchange = array(
				'1'=>'==',
				'2'=>'!=',
				'3'=>'>',
				'4'=>'>=',
				'5'=>'<',
				'6'=>'<=',
				);
		$str = '';
		$strarr = array();
		foreach($rulesarr as $key=>$val){
			if($val[0]['control']=='number'){
				
				if($val[0]['vale']){
				$strarr[$key] = '#'.$val[0]['name'].'#'.$numchange[$val[0]['symbols']].$val[0]['vals']."&&#".$val[0]['name'].'#'.$numchange[$val[0]['symbole']].$val[0]['vale'];
				}else{
					$strarr[$key] = '#'.$val[0]['name'].'#'.$numchange[$val[0]['symbole']].$val[0]['vals'];
				}
			}elseif($val[0]['control']=='select'){
					$strarr[$key] = '';
				if($val[0]['symbol']==3){
					foreach($val[0]['val'] as $k=>$v){
						$strarr[$key] .= $strarr[$key]?'||#'.$val[0]['name'].'#'.$textchange[1].$v:'#'.$val[0]['name'].'#'.$textchange[1].$v;
					}
				}else if($val[0]['symbol']==4){
					foreach($val[0]['val'] as $k=>$v){
						$strarr[$key] .= $strarr[$key]?'&&#'.$val[0]['name'].'#'.$textchange[2].$v:'#'.$val[0]['name'].'#'.$textchange[2].$v;
					}
				}else{
					$strarr[$key] = '#'.$val[0]['name'].'#'.$textchange[$val[0]['symbol']].$val[0]['val'];
				}
				
			}else{
				$strarr[$key] = '#'.$val[0]['name'].'#'.$textchange[$val[0]['symbol']].$val[0]['val'];
			}
				
		}
		$str = implode("&&",$strarr);
		return $str;
	}
}

























