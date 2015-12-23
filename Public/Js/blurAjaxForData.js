$(function(){
	var $box= $.pdialog.getCurrent();
	var checkTimeout = false;
	//IE8浏览器 
	var isIE8 = $.browser.msie && $.browser.version <= '8.0';

	if($.browser.msie){
		if(isIE8){
			//如果在IE8浏览器。给对应的对象绑定一个keyup事件，触发后调用blurAjaxForData方法
			$('input.onblurInput').bind('keyup',blurAjaxForData);
		}else{
			//如果在IE6浏览器。给对应的对象绑定一个propertychange事件(只有IE6特有的事件)，触发后调用blurAjaxForData方法 
			$('input.onblurInput').bind('propertychange',blurAjaxForData);
		}
	}else{
		//其他浏览器绑定一个input事件，触发后调用blurAjaxForData方法 
		$('input.onblurInput').bind('input',blurAjaxForData);
	}
});
function blurAjaxForData(e){
	//当前对象 
	var _this = $(this); 
	var $form = this.form;
	var m = _this.attr("rel");
	//关闭浏览器自动记录输入的内容 
	_this.attr('autocomplete', 'off');
	
	var s= $(this).attr("name");
	//将文本框输入的内容赋值到隐藏域，方便提交到后台 
	$($form).attr("action",TP_APP + "/"+m+"/add");

	if(isIE8){
		//ie8浏览器下面 
		var curKey = e.keyCode; //获取键盘按钮值 
		if(curKey != 13 && curKey != 38 && curKey != 40){
			var prev = _this.prev(); //遍历同级所有节点 
			if(prev.attr('auto') == 1){
				if(e.type != 'dblclick' && prev.val() == _this.val()){
					return false;
				}else{
					prev.val(_this.val());
				}
			}
		}else{
			return false;
		}
	}
	
	if(checkTimeout){
		//取消延迟提交 
		clearTimeout(checkTimeout);
		checkTimeout = null;
	}
	
	checkTimeout = setTimeout(function(){
		//进行延迟提交 
		$form.onsubmit();
	}, 1000);
}
function divGlobalFalseSearch(form,rel){
	var $form = $(form);
	if (form[DWZ.pageInfo.pageNum])
		form[DWZ.pageInfo.pageNum].value = 1;
	if (rel) {
		var $box = $("#" + rel);
		$.ajax({
			type:'POST',
			url:$form.attr("action"),
			data : $form.serializeArray(),
			async:true,
			global:false,
			success:function(response){
				var json=DWZ.jsonEval(response);
				if(json.statusCode==DWZ.statusCode.timeout) {
					alertMsg.error(json.message||DWZ.msg("sessionTimout"),{okCall:function(){
					if($.pdialog)$.pdialog.checkTimeout();
					if(navTab)navTab.checkTimeout();
					DWZ.loadLogin();}});
				}
				if(json.statusCode==DWZ.statusCode.error){
					if(json.message)alertMsg.error(json.message);
				}else{
					if(json.statusCode==DWZ.statusCode.timeout){
						$box.html(json.message+'<div style="display:none;">'+response+'</div>').initUI();
					} else {
						$box.html(response).initUI();
					}
				}
				$box.find("[layoutH]").layoutH();
			},
			error:DWZ.ajaxError
		});
	}
	return false;
}