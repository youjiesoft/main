
{~$classNodeSettingArr =getModelClassByNodeSetting('#nodeName#','view')}
{~$formautosetting =setFormControllAutoCreate('#nodeName#' ,'view' , $main , 'update',$vo)}
<div class="page">
	<div class="pageContent">
		<div class="pageFormContent applecloth anchorsToolBarParen"
			<if condition="!$_REQUEST['main'] or $_REQUEST['main'] eq MODULE_NAME">
				<if condition="!$_REQUEST['bindaname']"> layoutH="40"</if>
			</if>
			>
			<div class="new_version_page ">
				<form id="#nodeName#_view"
					{$appendPageContentArr[5]} {$formautosetting[3]}  
					method="post" action="__APP__/#nodeName#/updateControll/navTabId/__MODULE__" 
					class="pageForm required-validate"
					onsubmit="return validateCallback(this, navTabAjaxDone)">
					<a class='xyz_anchornavi_top' name='#nodeName#_view_top'></a> 
					<input type="hidden" name="callbackType" value="closeCurrent" />
					{:W('FormMenu',array('#nodeName#' , 'view',$main))}
					<if condition="$_GET['viewtype'] neq 'view'&& !$_GET['main'] ">
					<div class="new_version_page_header pageFormContent ">
						<span class="left tml-ml20 ">#nodeTitle#</span>
						{:W('ShowRightToolBar',array('view','#nodeName#',$vo))}
						{:W('ShowAnchorNavi',array('#nodeName#', 'view',$main))}{$formautosetting[0]}
					</div>
					</if>
					<include file="contenthtml" />
					<div class="clear">
						<span style="display: none;" class="anchornaviforshow">#nodeName#_view</span><a
							class='xyz_anchornavi_buttom' name='#nodeName#_view_bottom'></a>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>