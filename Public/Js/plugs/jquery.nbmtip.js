
/**
 * 提示信息插件。
 * @author 咏殇影@nbmxkj 20140530
 * 		适用范围：所有支持hover事件的html标签
 * 参数：
 * 		title	: 提示信息内容
 * 		order	：<可选>设定该值后title参数无效，显示内容为本参数对应的标签内容。[#ID]或[.class]
 * 示例：
 * $('a').nbmtip({title:'我是提示内容'});
 * or
 * $('a').nbmtip({order:'#calendar-tips'});
 * or
 * $('a').nbmtip({order:'.calendar-tips'});
 * 
 */
(function($) {
	$.fn.nbmtip = function(options) {
		
		/*
		console.log('浏览器时下窗口可视区域高度'+$(window).height()); //浏览器时下窗口可视区域高度
		console.log('浏览器时下窗口文档的高度'+$(document).height()); //浏览器时下窗口文档的高度
		console.log('浏览器时下窗口文档body的高度'+$(document.body).height());//浏览器时下窗口文档body的高度
		console.log('浏览器时下窗口文档body的总高度 包括border padding margin'+$(document.body).outerHeight(true));//浏览器时下窗口文档body的总高度 包括border padding margin
		console.log('浏览器时下窗口可视区域宽度'+$(window).width()); //浏览器时下窗口可视区域宽度
		console.log('浏览器时下窗口文档对于象宽度'+$(document).width());//浏览器时下窗口文档对于象宽度
		console.log('浏览器时下窗口文档body的高度'+$(document.body).width());//浏览器时下窗口文档body的高度
		console.log('浏览器时下窗口文档body的总宽度 包括border padding margin'+$(document.body).outerWidth(true));//浏览器时下窗口文档body的总宽度 包括border padding margin
		 
		console.log('获取滚动条到顶部的垂直高度'+$(document).scrollTop()); //获取滚动条到顶部的垂直高度
		console.log('获取滚动条到左边的垂直宽度'+$(document).scrollLeft()); //获取滚动条到左边的垂直宽度
		
		*/
		var defaults = {
			title : '',// 显示内容
			order:'',//指定标签 ID 或 Class
		};
		// 配置项
		var opts = $.extend(defaults, options)
		var content;
		$(this).hover(function() {
			create(this);
		}, function() {
			if(typeof(content)!='undefined'){
				content.hide();
			}
		});
		// 功能函数
		function create(obj) {
			var selWidth = $(obj).width();
			var offset = $(obj).offset();
			var right = offset.left + $(obj).width();
			var left = offset.left;
			var top = offset.top;
			var height = $(obj).height();
			var width = $(obj).width();
			
			var total_width = $(document).width();//$(obj).parent().width();// 总宽度
			var total_heitht = $(document).height();//$(obj).parent().height();// 总高度
			
			var cdheight = $(window).height();// 浏览器可视高度
			var scrollTop = $(document).scrollTop();
			if(opts.order!=''){
				content = $(opts.order);
				content.css( {
					left : offset.left,
					top : offset.top + $(obj).height(),
					position : 'fixed',
				});
			}else{
				if(typeof(content) == 'undefined'){
					content = $('<div></div>');
					content.text(opts.title)
					$(obj).after(content);
				}
				content.css( {
					background: 'none repeat scroll 0 0 #FFFFFF',
					border: '1px solid #298CCE',
					overflow:'hidden',
					width : 300,
					height : $(obj).height()*2 ,
					left : offset.left,
					top : offset.top + $(obj).height(),
					position : 'fixed',
					display:'none'
				});
			}
			content.css({
				'z-index':99999
			});
			var c_width = content.width();
			var c_height = content.height();
			
			if (total_width < (c_width + left)) {
				//console.log('进入total_width < (c_width + left)');
				content.css( {
					left : left - c_width +  width
				});
			}else{
				//console.log('默认定义对当前对象X轴平行:'+left);
				content.css( {
					left : left
				});
			}
			//console.log('total_heitht:'+total_heitht +'c_height + top'+(c_height + top));
			//总高度 	小于 	当前标签距枯顶 	+	显示标签高度
			if (total_heitht < (c_height + top)) {
				//console.log('1111111');
				content.css( {
					//top : (total_heitht - c_height - height)
					top : top-c_height-3
				});
			}else{
				//console.log('22222 浏览器可风高：'+cdheight);
				var curScorllTop = top  - scrollTop;//当前距可视区顶
				//当前距可视区顶高度与自高大于可视区高，标签显示在左上
				if((curScorllTop +height+c_height) >=cdheight){
					//console.log('3333333');
					content.css( {
						top : (top - c_height-3)
					});
				}else{
					//console.log('44444_____TOP:'+(top+height+3));
					content.css( {
						top : top+height+3
					});
				}
			}
			content.show();
		}
	};
})(jQuery);
