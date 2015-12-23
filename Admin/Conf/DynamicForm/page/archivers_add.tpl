<div class="pageContent">
	<form method="post" action="__URL__/insert/navTabId/__MODULE__" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone)">
		<div class="pageFormContent new_version_page_content" layoutH="58">
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
		</div>
		{:W('ShowAction')}
	</form>
</div>