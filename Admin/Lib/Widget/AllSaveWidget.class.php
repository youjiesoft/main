<?php
/**
 * 一键保存
 * @Title: AllSaveWidget 
 * @Package package_name
 * @Description: todo(一键保存的功能代码块。) 
 * @author quqiang
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2015年2月28日 下午4:57:55 
 * @version V1.0
 */
class AllSaveWidget extends Widget{
	/**
	 * (non-PHPdoc)
	 * @see Widget::render()
	 */
	public function render($data){
		$selfactionname =  MODULE_NAME;
		$html = '';
		$main = $data[2];
		$actionname = $data[0];
		$oprateType = $data[1];
		$MainbindMap['bindaname'] = $actionname;
		$MainbindMap['pid'] = 0;
		$mainBindActionCondition = '';
		$MisAutoBindModel = D('MisAutoBind');
		$isMainAction = $MisAutoBindModel->where($MainbindMap)->count();
		$MisAutoBindSettableModel=D("MisAutoBindSettable");
		$MainbindsetMap=array();
		$MainbindsetMap['bindaname']= $actionname;
		$MainbindsetMap['pid'] = 0;
		$isMainbindsetAction = $MisAutoBindSettableModel->where($MainbindsetMap)->count();
		if(((($isMainbindsetAction||$isMainAction)&& !$main) || $_REQUEST['main'] == $selfactionname ) && ($oprateType=='add' || $oprateType=='edit')){
			if($oprateType=='add'){
				return '';
			}
			if($isMainAction||$isMainbindsetAction)$main = $actionname;
			switch ($oprateType){    
				case 'add':
					$oprate = 'insertControll';
					break;
				case 'edit':
					$oprate = 'updateControll';
					break;
				default:
					$oprate = 'insertControll';
			}
			
		$html = <<<EOF
		<ul class="right top_tool_bar" style="margin-left:-10px;">
			<li class="left">
				<a class="allSaveBtn" href="javascript:void(0);"><span class="icon-save"></span> 保存</a>
			</li>
		</ul>
<script>

	var box = navTab.getCurrentPanel();
	var errorCount=0;
	
	function initFormValid(){
		errorCount=0;
		var formObj = $("form.required-validate",box);
		//formObj.unbind();
		formObj.each(function(){
				//$(this).logs('自定义 表单初始化' );
				//$(this).unbind();
				$(this).validate({
					focusInvalid: true,
					focusCleanup: true, 
					errorElement: "span",
					ignore: ".ignore", 
					invalidHandler: function(form, validator) {
						//$(this).logs('自定义的 invalidHandler');
						var errors = validator.numberOfInvalids();
						errorCount += errors
						$(this).logs('自定义 错误个数：' + errors);
						if (errors) {
							//var message = DWZ.msg("validateFormError", [errors]);
							//alertMsg.error(message);
							//console.log(message+errors);
						}
					}
				});
			});
	}
	$(function(){
		//$("form.required-validate",box).validate();
		
		//initFormValid();
				
				
		$('a.allSaveBtn:last' , box).on('click' , function(){
			initFormValid();
			//errorCount=0;
			var formObj = $('form',box);
			formObj.each(function(){
				$(this).valid();
			});
				$(this).logs('自定义的表单验证'+errorCount);
				if(errorCount){
					var message = DWZ.msg("validateFormError", [errorCount]);
					alertMsg.error(message);
				}else{
					// 构造一个结束的表单。让程序知道，当前这个批次的操作完成了。
					var endForm = $('<form action="'+TP_APP+'/Common/{$oprate}/navTabId/{$main}/endform/1" method="post" onsubmit="return validateCallback(this, navTabAjaxDone)"></form>');
					//endForm.attr('action',TP_APP+'/Index/{$oprate}/navTabId/{$main}/');
					//endForm.attr('method','post');
					endForm.append($('<input type="hidden" name="__actionlistend__" value="end" />'));
					endForm.append($('<input type="hidden" name="callbackType" value="closeCurrent" />'));
					var main = $('#{$main}_{$oprateType}').find('input[name="__main__"]').clone();
					var actionlist = $('#{$main}_{$oprateType}').find('input[name="__actionnamelist__"]').clone();
					endForm.append(main);
					endForm.append(actionlist);
					//formObj.submit();
					formObj.each(function(){
					//		var ret = $(this).submit();
							console.log($(this));
					});
						
					var div = $('<div></div>');
					div.append(endForm);
						formObj.submit();
					console.log(div.html());
							console.log('开始休眠');
						setTimeout(function(){
							console.log('我在休眠啊。。结束了就提交完成。');
							endForm.submit();
						},2000)
							console.log('结束休眠，但业务还没开始呢');
					//return false;
				}
				return;
		});
	});
</script>
EOF;
		}
		return $html;
	}
}