<?php
/**
 * @Title: ShowSelectWidget
 * @Package package_name
 * @Description: todo(下拉框组件)
 * @author liminggang
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2015-1-22 上午10:34:03
 * @version V1.0
 */

class ShowSelectDTWidget extends Widget{
	/**
	 * (non-PHPdoc)
	 * @see Widget::render()
	 * @param Array $data	参数集合
	 * 		$data[0]	当前显示数据，修改时使用
	 * 		$data[1]	用户参数集合
	 * $data[1]=>array(
	 * 		[0]=>'样式参数'
	 * 		[1]=>'组件配置参数'
	 * );
	 * $param[0] = 'col require '; 	// 样式参数
	 * $param[1] = array( 		// 组件配置参数
	 * 		'type'=>'table|selectlist',数据来源方式：table|selectlist
	 * 		$parame
	 * );
	 *
	 *
	 * 公用的属性
	 * $parame['readonly']		组件是否只读
	 * $parame['targevent']		绑定的事件
	 * $parame['names']			组件name属性
	 * $parame['defaultcheckitem']		默认选中项的值，只有在add时有效
	 * $parame['defaultval']			外部传入首选项的值
	 * $parame['defaulttext']			外部传入首选项的显示文本
	 *
	 * // 表查询私有属性
	 *   $parame['table']			查询表名
	 *   $parame['id']			真实值字段名
	 *   $parame['name']			显示值字段名
	 *   $parame['conditions']	过滤条件
	 *
	 *   	//当数据为树形查找的需要的参数
	 *   $parame['parentid']		上一级ID，当这个有值时表示下拉框为树形下拉。
	 *   $parame['mulit']          是否多选 true false   false：单选 , true:多选
	 *   $parame['isnextend']		是否只能操作最下一级数据 true|false。
	 *
	 *   // selectlist 查询私有属性
	 *   $parame['key']			数据key值
	 *
	 * {W:ShowSelect(array('1' , $param ))}
	 */
public function render($data){
		$param = $data[1];
		$conf = $param[1];
		$parame=$conf[0];
		if(!is_array($param) || !is_array($conf)){
			return "<error>组件配置信息缺失！</error>";
		}
		$datatype = $conf['type'];
		$datatypeArr = array('table'=>'table','selectlist'=>'selectlist');
		$type = $datatypeArr[$datatype];
		if(!$type)return "<error>未知数据来源：{$datatype},当前允许类型:".join(',',$datatypeArr)."</error>";

		//样式
		$classStyle=$param[0];
		//是否只读
		$readonly= intval($parame['readonly']);
		$readonly=$readonly==1? true : false ;
		//组件name属性
		$fieldName=$parame['names'];
		$fieldNameAppend=$parame['namesappend'];
		//组件事件
		$targevent=$parame['targevent'];
		//action名称
		$actionName=$parame['actionName'];
		//默认选中项的值，只有在add时有效
		$defaultcheckitem=$parame['defaultcheckitem'];
		//外部传入首选项的值
		$defaultval=$parame['defaultval'];
		//外部传入首选项的显示文本
		$defaulttext=$parame['defaulttext'];
		//查询表名
		$table=$parame['table'];
		//真实值字段名
		$id=$parame['id'];
		//显示值字段名
		$name=$parame['name'];
		//过滤条件
		$conditions = $parame['conditions'];
		$conditions = html_entity_decode($conditions);
		//父级id
		$parentid=$parame['parentid'];
		//是否多选 true false
		$mulit=$parame['mulit'];
		//是否末级操作
		$isnextend=$parame['isnextend'];
		//数据key值
		$key=$parame['key'];
		//是否编辑
		$isedit=$parame['isedit'];
		//	树形-下拉高度
		$treeheight = $parame['treeheight'];
		if(empty($treeheight) || $treeheight==0){
			$treeheight = 150;
		}
		// 树形-下拉宽度
		$treewidth = $parame['treewidth'] ? $parame['treewidth'] : 150;
		$treewidthParame = '';
		if(!empty($treewidth) || $treewidth!=0){
			$treewidthParame = 'data-width="'.$treewidth.'"';
		}
		$dtCtP = 'data-comboxtype="dt"';
		
		// 是否为直接转出只读内容
		$showtype = $parame['showtype'];
		if($targevent){
			$tagEventSytr = 'on'.ucwords($targevent).'="'.$actionName.'_'.$fieldName.'_'.$targevent.'(this)"';
		}
		if($readonly){
			$readonlyStr='readonly="readonly"';
		}
		if($mulit==1){
			$mulitType='checkbox';
		}else{
			$mulitType='radio';
		}
		if($defaulttext){
			$optionStr='<option value="'.$defaultval.'">'.$defaulttext.'</option>';
		}
		$ztreeId="ztree_{$actionName}_{$fieldNameAppend}";
		if($isedit){
			$ztreeId="ztree_{$actionName}_{$fieldNameAppend}_edit";
		}
		//$ztreeId = str_replace('=', '',base64_encode($ztreeId));
		if($showtype){
			if($conf['type']=='selectlist'){
				$html = '<span class="input_new">'.getControllbyHtml('selectlist',array('type'=>'select','key'=>$key,'conditions'=>$conditions,'selected'=>$data[0] ,'showtype'=>$showtype)).'</span>';
			}else{
				$html = '<span class="input_new">'.getControllbyHtml('table',array('type'=>'select','table'=>$table,'id'=>$id,'name'=>$name,'conditions'=>$conditions,'selected'=>$data[0],'showtype'=>$showtype)).'</span>';
			}
			return $html;
		}
		if($conf['type']=='selectlist'){
			if($key){
				$html=<<<EOF
			<select {$tagEventSytr} {$readonlyStr}  name="{$fieldName}"  class="   {$classStyle} select2 select_elm ">
			{$optionStr}
EOF;
			$html .=getControllbyHtml('selectlist',array('type'=>'select','key'=>$key,'conditions'=>$conditions,'selected'=>$data[0]));
			$html .= <<<EOF

			</select>
EOF;
			}
		}else{
			if($parentid){
				//添加-只能选择下拉框最后一级的特殊数据获取方式 @date:15-01-17 16:38:41
				$model=D("MisSystemRecursion");
				$model->__construct();
				$treeSelectselect6Data = $model->modelShow($table , array('key'=>'id','pkey'=>$parentid,'conditions'=>$conditions,'fields'=>"$id,$name") , 0 , 1);
				$treeSelectselect6DataTemp='';
				if($defaulttext){
					array_unshift($treeSelectselect6Data, array($id=>$defaultval ,$name=>$defaulttext,$parentid=>0,'nextEnd'=>1));
				}
				foreach ($treeSelectselect6Data as $key => $value) {
					// id":1, "pId":0, "name":"基本元素"
					$tem='';
					$tem['id']=$value['id'];
					$tem['key'] = $value[$id];
					$tem['pId']=$value[$parentid];
					$tem['name']=$value[$name];
					if($isnextend=='1' && $value['nextEnd']==0){
						$tem['chkDisabled']=true;
					}
					if($isedit){
						$dataArr=explode(',', $data[0]);
						if(in_array($value[$id], $dataArr)){
							$tem['checked']=true;
						}
					}
					//查询显示数据
					$ztreeModel=M($table);
					$ztreemap[$id]=array('in',$dataArr);
					$ztreeList=$ztreeModel->where($ztreemap)->select();
					$ztreeName=array();
					foreach ($ztreeList as $zk=>$zval){
						$ztreeName[]=$zval[$name];
					}
					$nameArr=implode('，', $ztreeName);
					$treeSelectselect6DataTemp[]=$tem;
				}
				//print_r($treeSelectselect6DataTemp);
				// 当前没得传显示数据，有首项显示数据
				if($isedit=='' && $defaulttext){
					$nameVal=$defaulttext;
					$IdVal=$defaultval;
				}else{
					if($data[0]==$defaultval){
						$nameVal=$defaulttext;
						$IdVal=$defaultval;
					}else{
						$nameVal=$nameArr;
						$IdVal=$data[0];
					}
				}
				$conboxtreeCls = 'comboxtree';
				if($readonly){
					$conboxtreeCls='';
				}else{
					$conboxtreeCls='comboxtree notreadonly';
				}
				$treeData=json_encode($treeSelectselect6DataTemp);
				$dataNames = str_replace('#hide#','',$fieldName);
				$html=<<<EOF
				<div class="list_input">
              <input type="text" readonly="readonly"  class="{$conboxtreeCls} {$classStyle} " size="18" value="{$nameVal}" data-names="{$dataNames}" data-height="{$treeheight}" data-tree="#{$ztreeId}" {$treewidthParame} {$dtCtP} data-search="true" />
              <input type="hidden" name="{$fieldName}" value="{$IdVal}"/>
              <ul id="{$ztreeId}" class="ztree hide" attrs = '{"expandAll":false, "checkEnable":true, "chkStyle":"{$mulitType}", "radioType":"all", "onClick":"S_NodeClick", "onCheck":"S_NodeCheck"}' nodes='{$treeData}'></ul>
              		</div>
EOF;
			}else{
				$html=<<<EOF
			<select  {$tagEventSytr} {$readonlyStr}  name="{$fieldName}"  class="{$classStyle} select2 select_elm">
				{$optionStr}
EOF;
				$html .=getControllbyHtml('table',array('type'=>'select','table'=>$table,'id'=>$id,'name'=>$name,'conditions'=>$conditions,'selected'=>$data[0]));
				$html .= <<<EOF

			</select>
EOF;
			}
		}
		return $html;
	}
}