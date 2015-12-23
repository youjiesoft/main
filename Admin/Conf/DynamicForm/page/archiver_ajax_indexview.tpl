
<!-- 
基础档案模板： ajax 刷新模式 查看、编辑页
author:nbmxkj
time:2015-09-06 18
 -->
{~$classNodeSettingArr = getModelClassByNodeSetting('#nodeName#','add')}		
<div class="pageContent" layoutH="53">
	<div class="panelBar">
		<ul class="toolBar">
			<volist name="toolbarextension" id="toolb">
			<if condition="$_SESSION.a eq 1 or $toolb['ifcheck'] eq 0 or ($toolb['ifcheck'] eq 1 and !empty($toolb['permisname']) and $_SESSION[$toolb['permisname']])">
				<li>{$toolb['html']}</li>
			</if>
			</volist>
		</ul>
	</div>
	<div class="pageFormContent new_basis_archives_page">
		<div class="new_version_page_content">
			<if condition="$vo">
			<form method="post" action="__URL__/update/navTabId/__MODULE__" class="pageForm required-validate" onsubmit="return validateCallback(this, navTabAjaxDone)">
				<input type="hidden" name="id" value="{$vo.id}"/>
				<check name="orderno">
				<div class="#class#">
					<label class="label_new">{$fields["#fields#"]}:</label>
					<input type="text"  name="#fields#" class="required input_new" maxlength="40" value="{$vo['#fields#']}">
				</div>
				<else/>
				<div class="col_1_3 form_group_lay">
					<label class="label_new">编号:</label>
					<input type="text" name="orderno" class="required input_new " maxlength="40" value="{$vo['orderno']}" />
				</div>
				</check>
				<check name="name">
				<div class="#class#">
					<label class="label_new">{$fields["#fields#"]}:</label>
					<input type="text"  name="#fields#" class="required input_new" maxlength="40" value="{$vo['#fields#']}">
				</div>
				<else/>
				<div class="col_1_3 form_group_lay">
					<label class="label_new">名称:</label>
					<input type="text" name="name" class="required input_new " maxlength="40" value="{$vo['name']}" />
				</div>
				</check>
				#controll#
				<div class="clear"></div>
				{:W('MisSystemOrderno',array(1))}
				<div class="formBar">
					<ul>
						<li><button type="submit" class="tml_formBar_btn tml_formBar_btn_blue">{$Think.lang.save}</button></li>
					</ul>
				</div>
			</form>
			<else/>
			{:W('MisSystemOrderno')}
			</if>
		</div>
	</div>
</div>