
{~$classNodeSettingArr =getModelClassByNodeSetting('#nodeName#','edit')}
{~$appendPageContentArr =getBindTabsContent('#nodeName#',$vo,'edit','',$main)}
{~$formautosetting = setFormControllAutoCreate('#nodeName#' ,'edit' ,$main , 'update',$vo)}
{$appendPageContentArr[1]}
<div class="page">
	<div class="pageContent">
		<div class="pageFormContent applecloth anchorsToolBarParen"
			<if condition="!$_REQUEST['main'] or $_REQUEST['main'] eq MODULE_NAME">
			<if condition="!$_REQUEST['bindaname']"> layoutH="40"</if> </if>
			>
			<div class="new_version_page ">
				<form id="#nodeName#_edit"
					{$appendPageContentArr[5]} {$formautosetting[3]}  method="post" action="__APP__/#nodeName#/{$formautosetting[2]}/navTabId/__MODULE__" class="pageForm required-validate"	 onsubmit="{$appendPageContentArr[0]}">
					<a class='xyz_anchornavi_top' name='#nodeName#_edit_top'></a> 
					<input type="hidden" name="callbackType" value="closeCurrent" />
					{:W('HiddenInput',$vo)} 
					<input type="hidden" name="id" value="{$vo['id']}" />
					<input type="hidden" name="masid" value="{$vo['id']}" /> 
					{$formautosetting[1]}
					<if condition="$_GET['viewtype'] neq 'view'&& !$_GET['main'] ">
					<div class="new_version_page_header pageFormContent ">
						<span class="left tml-ml20 ">#nodeTitle#</span>
						{:W('ShowRightToolBar',array('edit','#nodeName#',$vo))}
						{:W('ShowAnchorNavi',array('#nodeName#', 'edit',$main))}
						{$formautosetting[0]}
					</div>
					</if>
					<div class="new_version_page_content">
						<check name="orderno">
						{:W('ShowOrderno',array(4,'#tableName#',$vo['orderno'],array('contentcls'=>'#class#',	'inputcls'=>'class="input_new "','title'=>$fields["orderno"],'isshow'=>#isshow#)))}
						<else/>
						{:W('ShowOrderno',array(4,'#tableName#',$vo['orderno'],array('contentcls'=>'col_1_3 form_group_lay field_orderno',	'inputcls'=>'class="input_new "','title'=>$fields["orderno"],'isshow'=>1)))}
						</check>	
						#controll#
						<if condition="!$appendPageContentArr[4]">{:W('ShowAction',array('data'=>$vo))}</if>
					</div>
				</form>
				{$appendPageContentArr[2]}
				{$appendPageContentArr[3]}
			</div>
		</div>
	</div>
</div>