/**
 * tags标签插件。
 * @author 咏殇影@nbmxkj 20140604
 * 使用：
 * 定义标签
 * <ul><li><input type="text" class="textInput"
 * 		checkfor="MisMessage" fileds="" inputName="recipient"  
 * 		chainname="recipientname"  emailName="email" />
 *</li></ul>
 * 
 * 	var obj = '.textInput';
 * 	$(obj).tagsinput();
 * 参数说明：checkfor:数据查找类型【程序逻辑使用】
 * 			fileds：指定弹出层显示字段，默认显示系统自定义
 * 			inputName：标识字段
 * 			chainname：标识字段
 * 			emailName：标识字段
 */
/*
 
 示例：
 html页面部分：
 <script>
function clearreceverMismessage(n , p){
	var l = $('#'+p+' li:last');
	$('#'+p+' li:not(:last)').remove();
}
</script>
 <div class="tml-form-row">
	<label>收件人</label>
	<div class="tml-input-append2">
		<ul class="addressee left checkTo" id="Mismessage_add" style="width:80%;">
		<li class="addresseeText">
			<input type="text" style="float: left;" 
			class="checkByInput addresseeTextInput textInput"  
			checkfor="MisMessage" 
			inputName="recipient" 
			chainname="recipientname" 
			emailName="email"
			/>
		</li>
		</ul>
		<a class="input-addon input-addon-addon input-addon-userplus checkUser" href="javascript:;" ulid="Mismessage_add" data="recipientname,username,text;recipient,userid,hidden,1;email,email,hidden">查找带回</a>
		<a href="javascript:void(0)" class="input-addon input-addon-recycle" title="清空接收用户" onclick="clearreceverMismessage('1','Mismessage_add');">清除</a>
		
	</div>
</div>
 
 程序模板：Tpl/default/CheckFor/check.html 修改后内容见下方：
 <!-- 模板开始  -->
 <div id="checkfor">
<style>
#checkfor{border: 1px solid #95B8E7;position: absolute;background: #fff;text-align: center;z-index: 9999 !important;}
#checkfor .current{	background: #fbec88;}
#checkfor td{	border-right: 1px dotted #ccc;	border-bottom: 1px dotted #ccc;	cursor: pointer;	min-width:70px;	padding:4px 2px;}
#checkfor thead{	background: #efefef;}
</style>
<empty name="notfound">
	<div>
		<table border="0" cellspacing="0" id="checkForTable">
			<thead>
				<tr>
				<volist name="fields" id="n" key="k">
					<td <eq name="n" value="0">style="display:none;"</eq>>{$n}</td>
				</volist>
				</tr>
			</thead>
			<tbody>
				<volist name="checklist" id="l" key="k">
					<tr <eq name="k" value="1">class="current"</eq>>
						<volist name="fields" id="f" key="k2">
							<td class="{$key}" <eq name="f" value="0">style="display:none;"</eq>>{$l[$key]}</td>
						</volist>
					</tr>
				</volist>
			</tbody>
		</table>
	</div>
<else/>
{$notfound}
</empty>
</div>
  <!-- 模板结束  -->
 */

;(function($){
	$.fn.tagsinput=function(){
		var tagsId = 'checkfor';
		//给每个对象绑定事件
		$.each($(this),function(){
			var _this = $(this);
			_this.attr('autocomplete', 'off');
			//////////////////
			var iswrite = _this.attr('iswrite');
			var insert = _this.attr('insert')==''?'id':_this.attr('insert');
			var show = _this.attr('show')==''?'name':_this.attr('show');
			///////////////
			var callback = _this.attr('callback');
			
			
			var real = _this.siblings('input[type="hidden"]');
			var tr = _this.find('tbody tr.current');
			_this.parent().css('zIndex', '10');

			_this.bind('keyup',function(e){
				$.log('绑定事件地方')
				if(e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40 && e.keyCode != 13 && e.keyCode!=27)
					getData(_this.val() , _this);
				/*if(_this.val()==' '){
					clear();
					//_this.val('');
				}*/
			});
			
			if ($.browser.mozilla) {
				_this.bind('keypress', function(e){bindValue(e,_this);}).log('the event is binds');
			} else {
				_this.bind('keydown', function(e){bindValue(e,_this);});
			}
			
		});
		
		$('body').bind('click',function(){clear();});//非正常情况下关闭显示数据
		
		//
		// checkfro 属性：					【只需传】
		// iswrite：输入结果查询为空时时否保存
		// checkfor：提交到的action名称		【只需传】
		// show:显示字段			【只需传】
		// litmit:查询条数			【只需传】
		// order:排序方式			【只需传】
		// map: 查询条件			【只需传】
		// fields:数据检索字段		【只需传】
		// other:其它条件
		// m:MODEL					【只需传】
		// appendurl:附件			【只需传】
		// callback:回调函数		
		// insert:被抓取数据的字段
		
					
		function bindValue(e,obj){
			_this= obj || _this;
			e.stopPropagation();
			var key = (e.keyCode) || (e.which) || (e.charCode);//兼容IE(e.keyCode)和Firefox(e.which)
			var tr = $('#checkfor tbody tr.current');
			switch(key){
				case 38://上
					if(tr.prev().length){
					tr.removeClass('current');
					tr.prev().addClass('current');
					autoScroll(tr.prev() , this);
					}
					break;
				case 40://下
					if(tr.next().length){
						tr.removeClass('current');
						tr.next().addClass('current');
						autoScroll(tr.next() , this);
						}
					break;
				case 13://回车
					e.preventDefault();
					tr.log('this is the enter get value').trigger('click');
					break;
				case 27://ESC
					clear();
					break;
				case 8://删除
					//var d = new Date();
					//console.log('我点了删除键'+d.toString()+'++'+_this.val());
					if(_this.val()==''){
						//删光了文本框中的值。就移除已成tag的项 
						//找出当前对象的父节点Ul下的li 不为最后一个子节点
						var temp = _this.closest('ul').find('li:not(.addresseeText)');
						temp.last('li').remove();
						clear();
					}
					
					break;
				case 27://ESC关闭
					clear();
					break;
			}
		
			
		}
		function getData(val , _this){
			var checkfor = _this.attr('checkfor');
			var limit = _this.attr('litmit');
			var order = _this.attr('order');
			var map = _this.attr('map');
			var fields = _this.attr('fields');
			var other = _this.attr('other');
			var m = _this.attr("m");	// 当前模型
			var appendurl = _this.attr('appendurl');
			var url = TP_APP + '/CheckFor/check';
			var callback = _this.attr('callback');
			var iswrite = _this.attr('iswrite')==''?false:true;
			
			// 如果存在附件URL 这加入url中
			if(appendurl){
				url = url+"/"+appendurl+'/accesstype/plugs';
			}
			$.ajax({
				type: "POST",
				url: url,
				data: {m:m,other:other,fields: fields,map: map,order: order, limit: limit, checkfor: checkfor, con: val},
				async:true,
				success:function(msg){
					var con = $(msg);
					$('#'+tagsId).remove();
					
					$('body').append(con).log('append the eval html');
					var scrollHeight = 18;
					var pos = _this.offset();
					var w = $(window);
					var c = $('#'+tagsId);

					var cheight = c.outerHeight(true)+scrollHeight;
					var cwidth = c.outerWidth(true);

					var overflow_width = pos.left + cwidth - w.width();   // > 0 溢出宽度
					var overflow_height = pos.top + cheight - w.height(); // > 0 溢出高度

					var left = overflow_width > 0 ? pos.left - cwidth + _this.outerWidth() : pos.left;
					var top = overflow_height > 0 ? pos.top - cheight : pos.top + _this.outerHeight();
					
					con.css({left: left, top: top});
					if($('table',con).height()+scrollHeight>200){
						con.css({overflow:'scroll',height:233});
					}
					$.log('自动清除功能:'+$('#'+tagsId+' table' , con).length+'__iswrite:'+iswrite);
					if(iswrite == true){
						if($('#'+tagsId+' table').length==0){
							_this.val('');
						}
					}
					
					
					$.log(typeof(callback)+'__'+callback);
					if(callback != '' && callback != undefined ){
						_this.one('callback', function(event, data){
							window[callback](data,_this);
						});
					}
					
					$('tbody tr').mouseover(function(){
						tr = $(this);
						tr.addClass('current').siblings('.current').removeClass('current');
					});

					$('tbody > tr').click(function(){
						
						setRealData(true , _this);
						clear();
					});
					
					
						
				},
				global:false
			});
		}
		
		/**
		* 设置所有相关的值
		* @param isConfirm 
		*/
		function setRealData(isConfirm  , obj){
			_this = obj || _this;
			
			var iswrite = _this.attr('iswrite')==''?false:true;
			var insert = _this.attr('insert')==''?'id':_this.attr('insert');
			var show = _this.attr('show')==''?'name':_this.attr('show');
			var other = _this.attr('other');
			var callback = _this.attr('callback');
			var tr = $('#'+tagsId+' tbody tr.current');
			var realdata = tr.children('.'+insert).text();
			var val = tr.children('.'+show).text();
			var real = _this.siblings('input[type="hidden"]');
			_this.prev('[auto="1"]').val(val);//for IE8
			_this.val(val).focus().log('set val:'+val);
			real.val(realdata).log('resout '+realdata);

			/* 假如选择值的同时有回调函数，则执行 */
			if(isConfirm){
				other && setOther();
				if(callback){
					$.log(callback);
					var tds = tr.children();
					var count = tds.length;
					var td, id, data;
					var callbackData = {show: val, insert: realdata, theInput: _this, callbacktype:'checkfor'};

					for(var i=0;i<count;i++){
						td = $(tds[i]);
						id = td.attr('class');
						data = td.text();
						callbackData[id] = data;
						
						if(count == i+1){
							evalCallback(callbackData);
						}
					}
				}
			}
		}

		function evalCallback(data){
			$.log('common on'+data);
			try{
				$.log('go to func');
				_this.trigger('callback', [data]);
				_this.unbind('callback');
			}catch(e){
				$.log(e.toString());
			}
		}


		
		function clear(){
			$('#'+tagsId).remove();
			//_this.val('');
		}
		function autoScroll(tr , obj){
			tagsObj= $('#'+tagsId);
			var scrollTop = tagsObj.scrollTop();
			var top = tr.position().top;
			var hei = tagsObj.outerHeight(true);
			if(top+27 > hei){
				tagsObj.scrollTop(scrollTop + 54);
			}else if(top-27 < 0){
				tagsObj.scrollTop(scrollTop - 27);
			}
		}
	}
})(jQuery);