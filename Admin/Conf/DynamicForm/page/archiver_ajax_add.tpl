
<!-- 
基础档案模板： ajax 刷新模式 新增 
author:nbmxkj
time:2015-09-06 18
 -->
<div class="pageContent" layoutH="48">
	<form method="post" action="__URL__/insert/navTabId/__MODULE__" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
		<div class="pageFormContent new_basis_archives_page">
			<div class="new_version_page_content">
				<check name="orderno">
				<div class="#class#">
					<label class="label_new">{$fields["#fields#"]}:</label>
					<input type="text"  name="#fields#" class="required input_new" maxlength="40" value="{$vo['#fields#']}">
				</div>
				<else/>
				<div class="col_1_3 form_group_lay">
					<label class="label_new">编号:</label>
					<input type="text" name="orderno" class="required input_new " maxlength="40" value="" />
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
					<input type="text" name="name" class="required input_new " maxlength="40" value="" />
				</div>
				</check>
				#controll#
				<div class="clear"></div>
				{:W('MisSystemOrderno',array(1))} 
				{:W('ShowAction')}
			</div>
		</div>
	</form>
</div>