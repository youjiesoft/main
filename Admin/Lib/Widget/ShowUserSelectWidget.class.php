<?php
/*
 * author : quqiang
 * date   : 2014-10-28
 * usage  : 人员选择组件
 */
class ShowUserSelectWidget extends Widget{

	/**
	 * @Title: render
	 * @Description: todo(人员选择组件)
	 * @return string $html  组件html
	 * @author quqiang
	 * @date 2014-10-28 下午3:9:12
	 * @throws
	 */
	public function render($data){
		$html=<<<EOF
		<label>人员选择组件：</label>
		<ul class="addressee left checkCc" id="Mismessage_add_copyTo" style="width:720px;">
		<li class="addresseeText" style="z-index: 10;">
		
		<input 
			onclick="addressee.unselect(this);" 
			onfocus="addressee.clearVal(this);" 
			onkeydown="addressee.del(this, event);" 
			type="text" 
			checkfor="MisMessage" 
			callback="addresseeInput" 
			show="" 
			class="checkByInput addresseeTextInput textInput enterIndex" 
			inputname="copytopeopleid" 
			chainname="copytopeoplename" 
			emailname="emailCopy" 
			tabindex="1" 
		autocomplete="off">
		</li>
		</ul>
		
		<a class="input-addon input-addon-addon input-addon-userplus checkUser" 
			href="javascript:;" ulid="Mismessage_add_copyTo" 
			data="copytopeoplename,username,text;copytopeopleid,userid,hidden,1;emailCopy,email,hidden"
		>查找带回</a>&nbsp;
		
		<a class="input-addon input-addon-recycle" href="javascript:;" onclick="clearreceverMismessage('2','Mismessage_add_copyTo');" title="清空接收用户"></a>
							
EOF;
		return $html;
	}
}