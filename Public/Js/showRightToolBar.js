$(function(){
	var box = navTab.getCurrentPanel();
	var bb = $("div.unitBox.page:visible").prev("div.unitBox.page").find("tr.selected");
	var rel = bb.attr('rel');
	alert(rel);
	if(rel==undefined){
		rel =  $("div.unitBox.page:visible").prev("div.unitBox.page").prev("div.unitBox.page").find("tr.selected").attr('rel');
	}
	var target = bb.attr('target');
	if(target==undefined){
		target = $("div.unitBox.page:visible").prev("div.unitBox.page").prev("div.unitBox.page").find("tr.selected").attr('target');
	}
	var tabid = $("ul.navTab-tab li.selected").attr("tabid");
	$("ul.show_right_top_toolbar",box).after("<div class='gridTbody'><div class='selected' rel='"+rel+"'></div></div>");
	var tool = $(".show_right_top_toolbar a",box);
	tool.each(function(){
		var str = $(this).attr("href");
		var replace = str.replace('{'+target+'}',rel);
		var replace = replace.replace('{'+target+'}',rel);
		var replace = replace.replace('{'+target+'}',rel);
		$(this).attr("href",replace);
	});
	 //var reg=/.*?edit$/;  
//     if(/.*?edit$/.test(tabid)){
//    	 $(".js-edit",box).closest('li').hide();
//     }else if(/.*?add$/.test(tabid)){
//    	 $(".js-add",box).closest('li').hide();
//    	 $(".js-edit",box).closest('li').hide();
//    	 $(".js-view",box).closest('li').hide();
//    	 $(".js-delete",box).closest('li').hide();
//    	 $(".tbundo",box).closest('li').hide();
//    	 $(".tbprint",box).closest('li').hide();
//    	 $(".js-printOut",box).closest('li').hide();
//    	 
//    	 $(".js-addremind",box).closest('li').hide();
//     }else if(/.*?view$/.test(tabid)){
//    	 $(".js-view",box).closest('li').hide();
//     }
	
})