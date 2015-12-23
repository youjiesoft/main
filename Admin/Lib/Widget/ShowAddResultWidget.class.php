<?php

class ShowAddResultWidget extends Widget {
	public function render($data){
		$curid = rand(1000,999999).uniqid();
		$model = $data['model'];
		$multitype=$data['multitype'];//是否多行
		$akey=$data['akey'];//k值
		$vo = $data['data'];
		$cls = base64_encode('nbm').$curid;
		$showrules = $vo['showrules'];
// 		$tableinfo=$data['table'];
// 		$tableser=base64_encode(serialize($tableinfo));
		if($multitype){
			if($akey){
				$rulesname="rules[{$akey}]";
				$rulesinfoname="rulesinfo[{$akey}]";
				$showrulesname="showrules[{$akey}]";
				$endsql="endsql[{$akey}]";
			}else{
				$rulesname="rules[]";
				$rulesinfoname="rulesinfo[]";
				$showrulesname="showrules[]";
				$endsql="endsql[]";
			}
		}else{
			$rulesname="rules";
			$rulesinfoname="rulesinfo";
			$showrulesname="showrules";
			$endsql="endsql";
		}
		$inlinetable = '';
		if(!empty($data['inlinetable'])){
			$inlinetable = "inlinetable='{$data[inlinetable]}'";
		}
$html = <<<EOF
		<input type="hidden" name="order" value="{$cls}">
		<div style="display: inline-block;">
			<button class="condition_btn condition_add p_addresult{$cls} " akey="{$akey}"  type="button"  rel="{$model}_addresult"  order="{$cls}" modelname="{$model}" {$inlinetable} multitype="{$multitype}" listarr="{$vo['rulesinfo']}" onclick="openRule(this);"><span class="icon-plus"></span> 添加</button>
			<a class="condition_clear condition_btn" onclick="clearAllinforpresult('{$cls}');" href="javascript:;"><span class="icon-trash"></span> 清除</a>
		</div>
		<div class="{$cls}">
			<div class="condition_value adddt">{$showrules}</div>
	    	<input type="hidden" name="{$rulesname}" value="{$vo['rules']}">
	    	<input type="hidden" name="{$rulesinfoname}" value="{$vo['rulesinfo']}">
	    	<input type="hidden" name="{$showrulesname}" value="{$vo['showrules']}">
	    	<input type="hidden" name="{$endsql}" value="{$vo['endsql']}">
	    </div>
EOF;


		/*$html.= '<input type="hidden" name="order" value="'.$cls.'">';
		$html.= '<div class="'.$cls.'">';
		$html.= '	<div class="condition_value">'.base64_decode($vo['showrules']).'</div>';
	    $html.= '	<input type="hidden" name="rules" value="'.$vo['rules'].'">';
	    $html.= '	<input type="hidden" name="rulesinfo" value="'.$vo['rulesinfo'].'">';
	    $html.= '	<input type="hidden" name="showrules" value="'.$vo['showrules'].'">';
	    $html.= '</div>';
	    $html.= '<button class="condition_btn condition_add p_addresult'.$cls.' " type="button"  rel="'.$model.'_addresult"  order="'.$cls.'" modelname="'.$model.'" listarr="'.$vo['rulesinfo'].'" onclick="openRule(this);"><span class="icon-plus"></span> 添加</button>';
	    $html.= '<a class="condition_clear condition_btn" onclick="clearAllinforpresult(\''.$cls.'\');" href="javascript:;"><span class="icon-trash"></span> 清除</a>';*/
		return $html;
		
		/*$model = $data['model'];
		
		$html.= '<div class="'.$cls.'">'.$vo['showrules'];
	    $html.= '		<input type="hidden" name="rules" value="'.$vo['rules'].'">';
	    $html.= '		<input type="hidden" name="rulesinfo"  class="showrules'.$cls.'" value="'.$vo['rulesinfo'].'">';
	    $html.= '		<input type="hidden" name="showrules" value="'.$vo['showrules'].'">';
	    $html.= '		</div>';
	    $html.= '		<a class="tml-btn tml-btn-blue tml-mr5 p_addresult'.$cls.'"   atthref="__APP__/'.$model.'/lookupaddresult/order/'.$cls.'/nodename/'.$model.'"   href="javascript:;"  onclick="getAddresult(\''.$url.'\',this,\''.$cls.'\')" title="添加条件" >添加</a>';
	    $html.= '<a class="input-addon input-addon-recycle" onclick="clearAllinforpresult();" href="javascript:;"></a>';
		return $html;*/
	}
}