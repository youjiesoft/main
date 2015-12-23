
<!-- 
基础档案模板： ajax 刷新模式 首页 
author:nbmxkj
time:2015-09-06 18
 -->
 <script>
$(document).ready(function(){
	var zNodes = {$treearr};
	var setting = {
			view: {
               selectedMulti: false,
               fontCss: getFontCss
			},
			data: {
				simpleData: {
					enable: true
				}
			}
		};
	$.fn.zTree.init($("##nodeName#tree"), setting, zNodes);
	var zTree = $.fn.zTree.getZTreeObj("#nodeName#tree");
});
</script>
<div class="pageContent">
	<div class="treeleft">
		<div class="close">
			<div class="clearfix p5">
				<div class="member_searcher">
					<input class="add_key left" id="#nodeName#SearchNodeKeys"  type="text" placeholder="请输入查找词" name=""/>
					<button class="btn_member icon-search right" onclick="SearchZTreeNode('#nodeName#tree','#nodeName#SearchNodeKeys')" type="button"></button>
				</div>
			</div>
			<ul id="#nodeName#tree" class="ztree" layoutH="100"></ul>
		</div>
	</div>
	<div id="#nodeName#view" class="unitBox treeright">
		<include file="indexview"  />
	</div>
</div>