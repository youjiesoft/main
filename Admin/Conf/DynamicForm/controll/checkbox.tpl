						
						<!-- 复选框组件 -->
						<div class="#class#" original="#original#" category="#category#" style="#style#">
							<label class="label_new">{$fields["#fields#"]}:</label>
							{:getControllbyHtml('#showoption#'==''?'table':'selectlist' , 
								array(
									'type'=>'checkbox',
									'key'=>'#showoption#',
									'table'=>'#subimporttableobj#',
									'id'=>'#subimporttablefield2obj#',
									'name'=>'#subimporttablefieldobj#' ,
									'selected'=>$vo['#fields#'],
									'names'=>'#fields#[]' ,
									'readonly'=>'#islock#',
									'showtype'=>'',
									'conditions'=>'#subimporttableobjcondition#'
								)
							)}
							<div class="display_none {$classNodeSettingArr['#fields#']}">{$vo['#fields#']}</div>
						</div>