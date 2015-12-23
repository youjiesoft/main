<?php
class ShowSqlResultWidget extends Widget {
	public function render($data){
		//得到表集合
		//print_r($data);
		$tableinfo=$data['table'];
		$modelrel=$data['model'];
		$inputname=$data['inputname'];
		$content = $data['content'];
		$tableser=base64_encode(serialize($tableinfo));
		$curid = rand(10000,999999).uniqid();
		$cls = base64_encode('nbm').$curid;
// 		//循环获取表字段
// 		$list=array();
// 		$model=D("Dynamicconf");
// 		foreach ($tableinfo as $tkey=>$tval){
//   			$resultlist=$model->getTableInfo($tval);
//   			if($resultlist){
//   				$list[]=$resultlist;
//   			}
// 		}
				$html = <<<EOF
		<div style="display: inline-block;">
			<button class="condition_btn condition_add  p_addresultsql{$cls}  " inputname="{$inputname}" type="button" 
			 rel="{$modelrel}_addresult"   modelname="{$modelrel}" listArr="{$content}" tableArr="{$tableser}" order="{$cls}"  onclick="openSqlRule(this);"><span class="icon-plus"></span> 添加</button>
			<a class="condition_clear condition_btn" onclick="clearAllsqlpresult('{$cls}');" href="javascript:;"><span class="icon-trash"></span> 清除</a>
		</div>
				<div class="{$cls}">
			<input type="hidden" class="sqlcondition_value{$cls} adddt" name="{$inputname}" value="{$content}">
	    </div>
EOF;
 
	return $html;	
		
		
	}
}