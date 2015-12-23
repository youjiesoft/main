/**
 * 标签附加新增插件。仅配合DWZ 的ComBox一起使用，默认为添加 行业类型。
 * 注意：
 * 		插件需要jquery.form.js插件支持，在模板文件数据提交前确保【form】插件已引入。
 * @author 咏殇影@nbmxkj 20140604
 * 使用：
 * 定义标签
 * 	<select class="combox additem" 
 * 		aurls="页面请求地址" 
 * 		atitle="新增"
 * 		laywidth="700">
 * 	<option>请选择</option>
 *	</select>
 * 调用
 * $('.additem').additem();
 * 标签属性说明
 * 		aurls：dialog弹窗显示内容地址。为空时默认地址为【/system/Admin/index.php/MisSalesCustomer/lookupAddSelectValue/model/MisSalesCustomerIndustry】
 * 		atitle：dialog弹窗显示标题
 * 		laywidth：dialog弹窗宽度
 * 		layheight：dialog弹窗高度
 */
/*
 修改示例
业务处理：/Lib/Action/CommonAction.class.php
	函数 lookupInsertSelectValue() 修改其成功回返值，带上当前插入数据的信息，返回格式为{id:'',name:'',.....},id为下拉框value,name为下拉框text,不是对应数据库中字段。
	代码修改如下：
	$pk = $model->getPk();
	//保存当前数据对象
	$list=$model->add();
	if ($list!==false) {
		$data = $model->where("{$pk}={$list}")->select();
		$this->success ( L('_SUCCESS_') ,'',$data);
		exit;
	} else {
		$this->error ( L('_ERROR_') );
	}
	
模板： /Tpl/default/Public/lookupAddSelectValue.html
<script>
$(function(){
	$('#myForm').submit(function(data){
		return false;
	});
});
function newsubmit(obj,aid){
	obj = $(obj);
	obj.submit(function(data){
		return false;
	});
	if(obj.valid()){
		obj.ajaxSubmit(function(data){
			if('string'==typeof(data)){
				data=$.parseJSON(data);
			}
			if(!data.status){alertMsg.error(data.message)return;}
			$.pdialog.closeCurrent();
			if('object'==typeof(data.data)){
				var cont = $("#"+aid).prev();
				var selObj = cont.find("select");
				var showObj = cont.find("a:first");
				var divCombox = cont.find("div:first");
				selObj.children().attr('selected',false);
				var optObj = $('#op_'+divCombox.attr('id'));
				optObj.html(optObj.html().replace('class="selected"',''));
				$.each(data.data , function(i,v){
					var opt = $('<option></option>');
					opt.val(v.id).text(v.name).attr('selected',true);
					selObj.append(opt);
					var li = '<li><a value="'+v.id+'" class="selected" href="#">'+v.name+'</a></li>';
					optObj.append(li);
					showObj.html(v.name);
				});
				optObj.find('li a').click(function(){
					var $this=$(this);
					$this.parent().parent().find(".selected").removeClass("selected");
					$this.addClass("selected");
					showObj.text($this.text());
					var $input=$("select",cont);
					if($input.val()!=$this.attr("value")){
						$("select",cont).val($this.attr("value")).trigger("change");
					}
				});
			}
		});
	}
}
</script>
<div class="pageContent">
	<form method="post" action="__URL__/lookupInsertSelectValue" class="pageForm required-validate" id="myForm" onsubmit="return newsubmit(this,'{$_REQUEST['aid']}')">
		<input type="hidden" name="model" value="{$model}">
		<div class="pageFormContent" layoutH="56">
			<include file="$tplName" />
		</div>
		<div class="formBar">
			<ul>
				<li><div class="buttonActive"><div class="buttonContent">
				<button type="submit">{$Think.lang.save}</button>
				</div></div></li>
			</ul>
		</div>
	</form>
</div>
 
 */

(function($){
	$.fn.additem=function(options){
		var timestamp =(new Date()).valueOf();
		var $this = $(this);
		var defaults = {
				show:function(id, url , atitle , w,h){
					console.log(w,h);
					var options = {};
					options.width = parseInt(w,10)?w:500;
					options.height = parseInt(h,10)?h:380;
					options.mask = true;
					options.resizable = false;
					options.maxable = false;
					options.minable = false;
					$.pdialog.open(url,id,atitle,options);
				}
		};
		var opts = $.extend(defaults, options);
		$.each($this,function(i,v){
			var id='nbm_panel_'+$.rand();
			var opts = $.extend(defaults, options);
			var item = $('<a></a>');
			item.attr('id',id).html('+').addClass('btnQuickAdd');
			$(v).after(item);
			item.click(function(){
				var $thisSelect =item.prev().find("select");
				var urls = $thisSelect.attr('aurls')?$thisSelect.attr('aurls'):'/system/Admin/index.php/MisSalesCustomer/lookupAddSelectValue/model/MisSalesCustomerIndustry';
				var atitle = $thisSelect.attr('atitle')? $thisSelect.attr('atitle'):'添加新项';
				var lw = $thisSelect.attr('laywidth')? $thisSelect.attr('laywidth'):'';
				var lh = $thisSelect.attr('layheight')? $thisSelect.attr('layheight'):'';
				console.log(urls,atitle);
				var url = urls+'/accesstype/plugs/aid/'+id;
				opts.show(id , url , atitle,lw,lh);
			});
		});
		
	}
$.extend({
	rand:function(){
		return Math.round(Math.random()*10000000);
	}
});
})(jQuery);