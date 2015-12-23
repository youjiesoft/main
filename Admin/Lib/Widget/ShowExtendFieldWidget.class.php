<?php

/**
 * @Title: ShowExtendFieldWidget
 * @Package package_name
 * @Description: todo(扩展字段类)
 * @data array 2个元素 0=>数据（产品）id(新增为空) 1=>模型名（对应数据所在表名）
 * @author liminggang
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2014-6-4 上午10:34:03
 * @version V1.0
 */
class ShowExtendFieldWidget extends Widget{
	
	
	public function render($data){
		if($data){
			$where['tableid'] = $data['id'];
		}else{
			$where['tableid'] = 0;
		}
		$where["modelname"] = MODULE_NAME;// 当前类名
		$model = M("mis_typeform_data");
		$dataArr=array();
		$expand_property = $model->where($where)->field('id,extendid,content')->select();
		
		foreach($expand_property as $key=>$val){
			$dataArr[$val['extendid']]['id']=$val['id'];
			$dataArr[$val['extendid']]['content']=$val['content'];
		}
		$CommonModel = D("Common");
		$expand_property= $CommonModel->get_expand_property($where,$dataArr);
		
		$html = "";
		$html .= '<fieldset class="side-catalog-anchor">';
		$html .= '<legend class="fieldset_legend_toggle side-catalog-text ">';
		$html .= '<b>四、产品扩展属性</b>';
		$html .= '</legend>';
		foreach($expand_property as $key=>$val){
			$html.=$val;
		}
		$html .= '</fieldset>';
		return $html;
	}
}

?>