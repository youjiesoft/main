<?php
/**
 * @Title: ShowOrdernoWidget
 * @Package package_name
 * @Description: todo(附件上传小主键)
 * @author liminggang
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2014-6-4 上午10:34:03
 * @version V1.0
 */
class ShowOrdernoWidget extends Widget{


	/*$data 参数必须包含以下内容
	 *1、当前是否带有审批流     必传入参数
	 *2、当前获取orderno的表名称  必传入参数
	 *3、 如果是修改页面，就必须封装   这个参数是除add页面不传入外，其他带有orderno查看的都要传入
	 *最终确定$data 的格式为  array('1','tablename',$vo['orderno'] , 
	 *		array(), // 响应式布局时的参数扩展
	 *	)
	 * @modify by nbmxkj 2014-11-13 1551 扩充第一个参数值的处理方式。2 为只返回orderno 编号值。
	 */
	public function render($data){
		$modulename = MODULE_NAME;// 当前类名
		//自动生成单号
		$scnmodel = D('SystemConfigNumber');
		//判断是修改页面，如果存在编码，只获取相关信息
		// 响应式布局 新增时必传个值，这里是处理。@nbmxkj 20141226 1833
		if($data[2]&& $data[2]!= ''){
			$ordernoInfo=$scnmodel->GetRules($data[1],$modulename);
			$ordernoInfo['orderno']=$data[2];
		}else{
		   $ordernoInfo = $scnmodel->getOrderno($data[1],$modulename);
		}
// 		print_r($ordernoInfo);
		$orderno =$ordernoInfo['orderno'];
		$ordernonum=$ordernoInfo['ordernonum'];
		//订单号是否可写
		$writable=$ordernoInfo['writable'];			
		//$writable= $scnmodel->GetWritable($data[1]);

		if($writable){
			$readonly = '';
		}else{			
			$readonly = 'readonly="readonly" ';
		}
		// 处理基础档案的orderno特殊代码
		$tableName = $data[1] ? $data[1] : 0;
		$isNeedBasisarchives = false;
		if($tableName){
			$sql="SELECT tpl FROM `mis_dynamic_form_manage` WHERE id=(".
					"SELECT formid FROM `mis_dynamic_database_mas` WHERE `tablename`='{$tableName}' LIMIT 1)";
			$obj = M();
			$tplTypeData = $obj->query($sql);
			if( is_array($tplTypeData) && $tplTypeData[0]['tpl'] ){
				if(strpos($tplTypeData[0]['tpl'] , 'basisarchivestpl')>-1){
					$isNeedBasisarchives = true;
				}
			}
		}
		$html = "";
		// 响应式布局页面用到的页面样式代码。@nbmxkj 20141226 1743
		$param = $data[3];
		// 加入orderno的组件隐藏功能 add by nbmxkj at 20150202 2122
		$display='';
		if($param['isshow'] === 0){
			$display='display:none';
		}
		switch ($data[0]) {
			case 1:
				$html .= '<div class="tml-form-col">';
				$html .= '	<label>编号:</label>';
				$html .= '	<input type="text" class="required" name="orderno" value="'.$orderno.'"'.$readonly.'/>';
				$html .= '	<input type="hidden"  name="ordernonum" value="'.$ordernonum.'"/>';
				$html .= '</div>';
				break;
			case 2:
				$html .=$orderno;
				break;
			case 3:
				$html .= '<div class="tml-form-col">';
				$html .= '	<label>编号:</label>';
				$html .= '	<input type="text" class="required" readonly=\"readonly\" name="orderno" value="'.$orderno.'"'.$readonly.'/>';
				$html .= '	<input type="hidden"  name="ordernonum" value="'.$ordernonum.'"/>';
				$html .= '</div>';
				break;
			case 4:
				// 响应式布局 编号可写@nbmxkj 20141226
				$contentcls = $param['contentcls'];
				$inputcls = $param['inputcls'];
				$title = $param['title']? $param['title']:'编号';
				if($isNeedBasisarchives){
					$html .= '<div class="'.$contentcls.'" style="'.$display.'">';
					$html .= '	<label class="label_new">'.$title.':</label>';
					$html .= '	<input type="text" '.$inputcls.' name="orderno" value="'.$orderno.'" />';
					$html .= '	<input type="hidden"  name="ordernonum" value="'.$ordernonum.'"/>';
					$html .= '</div>';
				}else{
					$html .= '<div class="'.$contentcls.'" style="'.$display.'">';
					$html .= '	<label class="label_new">'.$title.':</label>';
					$html .= '	<input type="text" '.$inputcls.' name="orderno" value="'.$orderno.'" '.$readonly.'/>';
					$html .= '	<input type="hidden"  name="ordernonum" value="'.$ordernonum.'"/>';
					$html .= '</div>';
				}
				break;
			case 5:
				// 响应式布局 编号不可写@nbmxkj 20141226
				$contentcls = $param['contentcls'];
				$inputcls = $param['inputcls'];
				$title = $param['title']? $param['title']:'编号';
				if($isNeedBasisarchives){
					$html .= '<div class="'.$contentcls.'" style="'.$display.'">';
					$html .= '	<label class="label_new">'.$title.':</label>';
					$html .= '	<input type="text" '.$inputcls.' name="orderno" value="'.$data[2].'" />';
					$html .= '	<input type="hidden"  name="ordernonum" value="'.$ordernonum.'"/>';
					$html .= '</div>';
				}else{
					$html .= '<div class="'.$contentcls.'" style="'.$display.'">';
					$html .= '	<label class="label_new">'.$title.':</label>';
					$html .= '	<input type="text" '.$inputcls.' name="orderno" value="'.$orderno.'" '.$readonly.'/>';
					$html .= '	<input type="hidden"  name="ordernonum" value="'.$ordernonum.'"/>';
					$html .= '</div>';
				}
				break;
			default:
				$html .='<span class="auto_code">';
				$html .='	<b>No. </b>';
				$html .='	<input type="text" name="orderno"  value="'.$orderno.'" '.$readonly.'/>';
				$html .= '	<input type="hidden"  name="ordernonum" value="'.$ordernonum.'"/>';
				$html .='	<span class="xyNextButtonDisable"></span>';
				$html .='</span>';
				break;
		}
		/*if($data[0]== 1){
			$html .= '<div class="tml-form-col">';
			$html .= '	<label>编号:</label>';
			$html .= '	<input type="text" name="orderno" value="'.$orderno.'"/>';
			$html .= '</div>';
		}else{
			$html .='<span class="auto_code">';
			$html .='	<b>No. </b>';
			$html .='	<input type="text" name="orderno" '.$class.' value="'.$orderno.'"/>';
			$html .='	<span class="xyNextButtonDisable"></span>';
			$html .='</span>';
		}*/
		return $html;
	}
}