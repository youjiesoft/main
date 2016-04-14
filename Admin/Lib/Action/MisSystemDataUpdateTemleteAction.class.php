<?php
class MisSystemDataUpdateTemleteAction extends MisDynamicFormManageAction{
	
	
	private $formid;
	function inits($action , $formid){
		try{
			$this->nodeName=$action;
			$this->formid=$formid;
			$mis_dynamic_form_indatatable = M ( "mis_dynamic_form_indatatable" );
			$misdynamicform = M ( 'mis_dynamic_form_manage' );
			// 重新设置当前action的真实表名
			$this->tableName = $this->getTrueTableName ();
			$nodeModel = D ( 'Node' );
			// 当前修改方式为不可靠的修改。
			$this->isaudit = $nodeModel->where ( "`name`='{$this->nodeName}'" )->getField ( "isprocess" );
			$formData = $misdynamicform->where ( "`actionname`='{$this->nodeName}'" )->find ();
			if(!$formData){
				throw new NullCreateOprateException("在动态表无该表单记录");
			}
			$this->formtype = $formData ['tpl'];
			$this->isrecord = $formData['isrecord'];
			$this->nodeTitle=$formData['actiontitle'];
		}catch(Exception $e){
			$this->error($e->getMessage());
		}
	}
	function curTplBuild(){
		$allControllConfig = $this->getfieldCategory ( $this->formid );
		// 获取当前节点的字段组件配置信息
		$curnodeData = $allControllConfig [$this->curnode];
		foreach ($curnodeData as $k=>$v){
			$v['islock']=1;			//
			$v['isrequired'] = 0;   //必填
			$v['isshow']=1;
			$v['requiredfield'] = 0; //添加必填判断
			$v['ganshe_datatable']=1; //添加内嵌表干涉页面判断
//			$v['catalog'] = $v['catalog']=='hiddens'?'text':$v['catalog'];
			switch ($v['catalog']){
				case 'lookup':
					$v['ganshe']=1;
					break;
				case 'datatable':
						if($v['fieldlist']){
							$temp = json_decode(html_entity_decode($v['fieldlist']),true);
							//print_r($temp);
							foreach($temp as $tk=>$tv){
								unset($temp[$tk]['isedit']);
							}
							$v['fieldlist'] = json_encode($temp);
						}
					break;
				case 'hiddens':
					$v['catalog'] = 'text';
					$v['defaultvaltext'] = '';
					break;
				default:
					break;
			}
// 			if($v['catalog'] == 'lookup'){
// 				$v['ganshe']=1;
// 			}elseif($v['catalog'] == 'datatable' && $v['fieldlist']){
// 				//print( $v['fieldlist']);
// 				$temp = json_decode(html_entity_decode($v['fieldlist']),true);
// 				//print_r($temp);
// 				foreach($temp as $tk=>$tv){
// 					unset($temp[$tk]['isedit']);
// 				}
// 				$v['fieldlist'] = json_encode($temp);
// 			}
			
			// 必需的 不能动
			$curnodeData[$k]=$v;
			
		}
		$content = $this->getPage( 'edit' , $curnodeData, 1 );
		return $content;
	}
	
	function index(){
		//header("Content-type: text/html; charset=utf-8");
		//$str = $this->fetch('Common:Nbm');
		//var_dump($str);
	}
	
	function test($node,$formid){
		$path = TMPL_PATH . C ( 'DEFAULT_THEME' ) . "/" . $node . "/misSystemDataUpdate.html";
		if(!is_file($path)){
			$this->inits($node,$formid);
			$content = $this->curTplBuild();
			$ret = file_put_contents($path,$content);
		}
 		return $path;
	}
}

