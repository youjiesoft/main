//右下侧导航
$(function(){
	var anchorbox = navTab.getCurrentPanel();
	//modelname
	var modelname = $(".anchornaviforshow",anchorbox).eq(0).text();
	var top22 = $(".xyz_anchornavi_top:first",anchorbox).attr("name");
	var buttom22 = $(".xyz_anchornavi_buttom:last",anchorbox).attr("name");
	var lastmodelname = $(".anchornaviforshow:last",anchorbox).text();
	//作用域
	var fieldset = $("div.fieldset_show_box:visible",anchorbox);
	var	relation = anchorbox.find("div.nbm_relation_form_tabs_navi");
	var html = '<li><a href="javascript:void(0);" onclick="go_button();"><span class="icon-arrow-down"></span><span class="inside_pages_btn_word">底部</span></a></li>';

	if(fieldset){			
		$.each(fieldset,function(){
			var zname = $(this).find("legend a").attr("name");
			var id = modelname+"_"+$(this).find("legend a").attr("name");
			$(this).find("legend a").attr("name",id);
			var text = $.trim($(this).find("b").text());
			var iduplode = zname;//id.split("_")[2];
			var cssname = 'icon-list-ul';
			if(iduplode == 'zhihuirenyuan'){
				cssname = 'icon-comments-alt';
			}else if(iduplode == 'liuchengtu'){
				cssname = 'icon-legal';
			}else if(iduplode == 'upload'){
				cssname = 'icon-paperclip';
			}
			if(id&&text){
				html += '<li> <a href="#'+id+'">';
				html +=     '<span class="'+cssname+'"></span>';
				html +=     '<span class="inside_pages_btn_word" title="'+text+'">'+text+'</span>';
				html += '</a>';
				html += '</li>';
			}
		});		
	}
	//选项卡
	if(relation.length>0){
		var relationli = anchorbox.find("div.nbm_relation_form_tabs_navi .tabsHeaderContent:visible").eq(0).find("ul:first>li:first");
		if($("a.nbm_relation_form_tabs_navi"+modelname,anchorbox).size()<1){
			relationli.find("a span").after("<a name='nbm_relation_form_tabs_navi"+modelname+"' class='nbm_relation_form_tabs_navi"+modelname+"'></a>");
		}
		
		var relationval = relationli.find("a span").text();
		html += '<li> <a href="#nbm_relation_form_tabs_navi'+modelname+'">';
		html +=     '<span class="icon-paperclip"></span>';
		html +=     '<span class="inside_pages_btn_word" title="'+relationval+'">'+relationval+'</span>';
		html += '</a>';
		html += '</li>';
	}
	//表格 在view页面特殊处理
	var i=1;
	$(".into_table_title:visible",anchorbox).each(function(){
		var name = modelname+"_"+i;
		$(this).append("<a name="+name+"></a>");
		var text = $(this).text();
		if(text){
			html += '<li> <a href="#'+name+'">';
			html +=     '<span class="icon-table"></span>';
			html +=     '<span class="inside_pages_btn_word" title="'+text+'">'+text+'</span>';
			html += '</a>';
			html += '</li>';
			i++;
		}
	})
	html += '<li><a href="javascript:void(0);" class="go_top" onclick="go_top();"><span class="icon-arrow-up"></span><span class="inside_pages_btn_word">顶部</span></a></li>';
	var new_group = anchorbox.find("div.inside_pages_btn_group:last").clone();
	anchorbox.find("div.inside_pages_btn_group:not(':last')").remove();
	anchorbox.append(new_group);
//	anchorbox.find("div.inside_pages_btn_group").not(:first).remove();
	anchorbox.find("ul.pages_btn_group").html(html);
//	var nbox = navTab.getCurrentPanel();
//	window.location.hash = nbox.find(".go_top").attr("href");
});
function go_top(){
	var nbox = navTab.getCurrentPanel();
	nbox.find('.pageFormContent').eq(0).animate({scrollTop: '0px'}, 800);
}	

function go_button(){
	var nbox = navTab.getCurrentPanel();
	nbox.find('.pageFormContent').eq(0).animate({scrollTop: nbox.find('.new_version_page').height()+'px'}, 800);//平滑滚动到底部

}

