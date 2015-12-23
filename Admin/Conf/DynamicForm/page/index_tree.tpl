
<!-- 
普通表单：列表左侧带菜单
author:nbmxkj
time:2015-09-06 18
 -->
<script>
	 $(function(){
	 //为左侧栏目导航点击后修改选中状态@nbmxkj 20141009 1629
		 nbm_getfouce();
	 });
</script>
<div class="pageContent">
{:W("ShowToLevelMenu")}
	<div class="treeleft">
		<div class="collapse" layoutH="0">
			<div class="toggleCollapse">
				<div></div>
			</div>
		</div>
	<div class="close" layoutH="40"  >
		<div class="work_statement">#nodeTitle#</div>
			<div class="edit_work_lay"><a class="edit_work_btn" href="__URL__/add"  target="navtab" title="新增">新增 <span class="icon-pencil"></span></a></div>
				<div class="tml_divider"></div>
					<div class="tml_bar_nav">
						<ul class="bar_nav">
							<li class="active"><a href="__URL__/index/jump/jump" target="ajax" rel="#nodeName#indexview">全部#nodeTitle#</a></li>
							<volist name='MisSaleClientTypeList' id='MisSaleClientTypeVo'>
							<li><a href="__URL__/index/fieldtype/{$suroce}/{$suroce}/{$key}/jump/jump" target="ajax" rel="#nodeName#indexview">{$MisSaleClientTypeVo}</a></li>
							</volist>
						</ul>
					</div>
		<!--<ul id="misSalesCustomerTree" class="ztree" layoutH="43"></ul>-->
				</div>
			</div>
			<div id="#nodeName#indexview" class="unitBox treeright">
				<include file="indexview" />
			</div>
		</div>