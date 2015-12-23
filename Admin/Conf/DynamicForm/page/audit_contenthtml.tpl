<div class="new_version_page_content">
	#controll#
	<div class="clear"></div>
	{~$appendPageContentArr = getBindTabsContent('#nodeName#',$vo,'view')}
	{$appendPageContentArr[2]}
	{$appendPageContentArr[3]}
	
	
	<div class="showFormFlow">
	{:W('ShowFormFlowView',array_merge($vo,array('ShowActionName'=>'#nodeName#')))}</div>
	{:W('ShowNotifyView',$vo)}
	{:W('ShowAction')}
</div>