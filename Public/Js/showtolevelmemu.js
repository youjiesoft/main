$(function(){
	var $box=navTab.getCurrentPanel();
	$('.nbmaccordion' , $box).nbmaccordion();
    $('a.menu_tag').toggle(function(){
        $('div.nbmaccordionFloat').animate({width:"200px"},'slow');
    },function(){
       $('div.nbmaccordionFloat').animate({width:"0px"},'slow');
    });
   var title = $(".navTab-tab").find("li.selected a:first").attr("title");
   $("div.work_statement",$box).html(title);
});