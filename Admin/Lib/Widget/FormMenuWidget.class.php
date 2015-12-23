<?php
/**
 * 一键保存-辅助功能组件表单构成项
 * @Title: AllSaveWidget 
 * @Package package_name
 * @Description: todo(一键保存--辅助功能组件表单构成项，记录其所有子级表单，) 
 * @author quqiang
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2015年2月28日 下午4:57:55 
 * @version V1.0
 */
class FormMenuWidget extends Widget{
	
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
	protected function getComForm($mainAction , $isContentSelf = false){
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
	protected function getNestedForm($mainAction , $isContentSelf = false){
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
	 * (non-PHPdoc)
	 * @see Widget::render()
	 */
	public function render($data){
		// 组件所在action名称
		$selfActionName =  MODULE_NAME;
		$html = '';
		$main = $data[2];  // 关系型表单的主入口表单action名称
		$actionname = $data[0]; // 当前action名称
		$oprateType = $data[1]; // 当前的数据操作方式，add | edit | view
		
		// 检查当前表单是为关系表单的入口。
		// 1.组合表单，2.套表表单
		// 套表可以嵌套套表，可以嵌套组合表。
		// 组合表不对现操作。
		// 检查顺序为：
		// 1:检查是否为 套表主入口
		// 2:检查是否为 组合表主入口
		// 1 2 都为false 则为普通表单
		
		// 检查1 为真时，获取其所有子级表单
		//			并检查子级表单是否为组合表单，如果是 则调用组合子表的下级获取
		//检查2 为真是，获取其所有子级表单
		$relactionArr=array();
		$actionlist='';
		// main 参数为空，可能情况有主入口及不是关系表单
		if(empty($main) && !empty($actionname)){
			$relactionArr = $this->getNestedForm($actionname);
			if(!is_array($relactionArr)){
				$comFormData = $this->getComForm($actionname);
				$actionlist =  array_reduce($comFormData , 'reduceFunc' , $actionname);
				//var_dump($actionlist);
			}else{
				// 检查套表的构成项是否为关系型表单
				foreach ($relactionArr as $key => $val){
					$tempData = $this->getComForm($val);
					if( is_array( $tempData ) ) {
						$relactionArr = array_merge($relactionArr , $tempData);
					}
				}
				$actionlist =  array_reduce($relactionArr , 'reduceFunc' , $actionname);
			}
			$main = $actionname;
		}else{
			// mian参数有值，表示它是从主入口进来的
			// 那就以main的值查询其所有子级
			$relactionArr = $this->getNestedForm($main);
			if(!is_array($relactionArr)){
				$comFormData = $this->getComForm($main);
				$actionlist =  array_reduce($comFormData , 'reduceFunc' , $main);
			}else{
				// 检查套表的构成项是否为关系型表单
				foreach ($relactionArr as $key => $val){
					$tempData = $this->getComForm($val);
					if( is_array( $tempData ) ) {
						$relactionArr = array_merge($relactionArr , $tempData);
					}
				}
				$actionlist =  array_reduce($relactionArr , 'reduceFunc' , $main);
			}
		}
		if( $actionlist && ($oprateType == 'add' || $oprateType == 'edit' ) ){
			$html = <<<EOF
		<input type="hidden" name="__actionnamelist__" value="{$actionlist}" />
		<input type="hidden" name="__main__" value="{$main}" />
EOF;
		}
		$html.='<input type="hidden" name="__selfaction__" value="'.$selfActionName.'" />';
		return $html;
		exit;
		$MainbindMap['bindaname'] = $actionname;
		$MainbindMap['pid'] = 0;
		$mainBindActionCondition = '';
		$MisAutoBindModel = D('MisAutoBind');
		
		$MisAutoBindSettableModel=D("MisAutoBindSettable");
		$MainbindsetMap['bindmodelname']= $actionname;
		$MainbindsetMap['pid'] = 0;
		
		$isMainAction = $MisAutoBindModel->where($MainbindMap)->count();
		$isMainbindsetAction = $MisAutoBindSettableModel->where($MainbindsetMap)->count();
		
		if( ( $main || $isMainAction || $isMainbindsetAction) && ($oprateType == 'add' || $oprateType == 'edit' ) ){
			if($isMainAction || $isMainbindsetAction )$main = $actionname;
			// 获取当前入口下的所有子表单及其本身
			// 如果套表下有记录，那么类型优先做套表处理
			if($isMainbindsetAction){
				$sql="SELECT bindmodelname FROM `mis_auto_bind_settable` WHERE LEVEL = (SELECT id FROM `mis_dynamic_form_manage` WHERE actionname = '{$main}') AND bindtype=1 AND pid <> 0";
				$formData = $MisAutoBindModel->query($sql);
			}elseif($isMainAction){
				// 这是组合关系型表单
				$sql="SELECT inbindaname FROM `mis_auto_bind` WHERE LEVEL = (SELECT id FROM `mis_dynamic_form_manage` WHERE actionname = '{$main}') AND bindtype=0 AND STATUS=1";
				$formData = $MisAutoBindModel->query($sql);
// 				if($formData){
// 					$formData = array_reduce($formData , 'reduceFunc' , $main);
// 					//array_unshift($data , $main);
// 				}
			}else{
				return '';
			}
			
// 			$sql="SELECT inbindaname FROM `mis_auto_bind` WHERE LEVEL = (SELECT id FROM `mis_dynamic_form_manage` WHERE actionname = '{$main}') AND bindtype=0 AND STATUS=1";
// 			$formData = $MisAutoBindModel->query($sql);
// 			if($formData){
// 				$formData = array_reduce($formData , 'reduceFunc' , $main);
// 				//array_unshift($data , $main);
// 			}
// 			if(!count($formData)){
// 				$sql="SELECT bindmodelname FROM `mis_auto_bind_settable` WHERE LEVEL = (SELECT id FROM `mis_dynamic_form_manage` WHERE actionname = '{$main}') AND bindtype=1 AND pid <> 0";
// 				$formData = $MisAutoBindModel->query($sql);
// 			}
			if($formData){
				$formData = array_reduce($formData , 'reduceFunc' , $main);
				//array_unshift($data , $main);
			}
			if(!count($formData)){
				return '没有干系';
			}
			$html = <<<EOF
		<input type="hidden" name="__actionnamelist__" value="{$formData}" />
		<input type="hidden" name="__main__" value="{$main}" />
EOF;
		}else{
			$html = <<<EOF
		<input type="hidden" name="__actionnamelist__" value="" />
		<input type="hidden" name="__main__" value="" />
EOF;
		}
		$html.='<input type="hidden" name="__selfaction__" value="'.$selfActionName.'" />';
		return $html;
	}
}