<?php
/**
 * @Title: MisSystemOrdernoWidget 
 * @Package package_name
 * @Description: 编码方案配置
 * @author 黎明刚 
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2014年10月22日 下午5:51:40 
 * @version V1.0
 */
class MisSystemOrdernoWidget extends Widget{
	
	public function render($data){
		$tablename = MODULE_NAME;// 当前类名
		$where = array();
		$where['tablename'] = $tablename;
		//查询当前tablename是否存在编码方案
		$MisSystemOrdernoModel = D("MisSystemOrderno");
		$vo = $MisSystemOrdernoModel->where($where)->find();
		
		$nodetitle = getFieldBy($tablename, "name", "title", "node");
		if($data){
			$title=$nodetitle."编码方案！";
		}else{
			$title ="暂无".$nodetitle."，请新增".$nodetitle."！";
		}
		$html ="";
		if($vo){
			$xstr = "*********";
			$newstr = "";
			for ($i = 1; $i <= $vo['maxlevels']; $i++){
				$num = $vo[$MisSystemOrdernoModel->arr[$i]];
				$result = substr ($xstr, 0,$num);
				$newstr .=$result." ";
			}
			$html .='<div class="tips_add_info">';
			$html .='   <p><span><span class="icon-comments"></span><span> 提示：</span></span>'.$title.'</p>';
			$html .='   <p>编码方案：<span>'.$newstr.'</span></p>';
			$html .='   <p>最大级数：<span>'.$vo['maxlevels'].'</span></p>';
			$html .='   <p>最大长度：<span>'.$vo['maxlenght'].'</span></p>';
			$html .='	<p>单级最大长度：<span>'.$vo['singlelenght'].'</span></p>';
			$html .='</div>';
		}else{
			$html .='<div class="tips_add_info">';
			$html .='   <p><span><span class="icon-comments"></span><span> 提示：</span></span>'.$title.'</p>';
			$html .='   <p>编码方案：<span>无</span></p>';
			$html .='   <p>最大级数：<span>0</span></p>';
			$html .='   <p>最大长度：<span>0</span></p>';
			$html .='	<p>单级最大长度：<span>0</span></p>';
			$html .='</div>';
		}
		
		
		
		
		
		
		return $html;
	}
}
?>