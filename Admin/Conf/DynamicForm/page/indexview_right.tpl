
<!-- 
审批流模板：首页查看模式
author:nbmxkj
time:2015-09-06 18
 -->
 <div class="pageContent">
  	<form id="pagerForm" action="__URL__/index/type/1" method="post">
 		<input type="hidden" name="pageNum" value="1"/>
 		<input type="hidden" name="orderField" value="{$order}" />
 		<input type="hidden" name="orderDirection" value="{$sort}"/>
 		{:getTreeParam()}
	</form>
 		<div class="panelBar">
 			<ul class="toolBar">
				<volist name="toolbarextension" id="toolb">
					<if condition="$_SESSION.a eq 1 or $toolb['ifcheck'] eq 0 or ($toolb['ifcheck'] eq 1 and !empty($toolb['permisname']) and $_SESSION[$toolb['permisname']])">
						<li>{$toolb['html']}</li>
					</if>
				</volist>
			</ul>
			<form rel="pagerForm" onsubmit="return   divSearch(this, '__MODULE__indexview')" action="__URL__/index/type/1" method="post">
			<input type="hidden" name="jump" value="jump"/>	
			<div class="searchBar">
				<table class="searchContent">
					<tr>
					<include file="Public:quickSearchConditionForAudit" />
					</tr>
				</table>
			</div>
			</form>
	</div>
	<table class="table" width="100%" layoutH="146">
		<thead ename="{$ename}">
			<tr>
				<th width="26">序号</th>
				<volist id="vo" name="detailList">
					<if condition="$vo[shows] eq 1"><th <if condition="$vo[widths]">width="{$vo[widths]}"</if><if condition="$vo[sorts]"> rel="__MODULE__indexview" orderField="{$vo[sortname]}" class="{$sort}"</if>>{$vo[showname]}</th></if>	<!--类型-->
				</volist>
			</tr>
		</thead>
		<tbody>
			<include file="dwzloadindex" />
		</tbody>
	</table>
	<div class="panelBar panelPageBar">
		<div class="pages">
			<span>共{$totalCount}条</span>
		</div>
		<div class="pagination" rel="__MODULE__indexview" targetType="navTab" totalCount="{$totalCount}" numPerPage="{$numPerPage}" pageNumShown="10" currentPage="{$currentPage}"></div>
	</div>
</div>