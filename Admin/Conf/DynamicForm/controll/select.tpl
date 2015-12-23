					
						<!-- 下拉框组件 -->
						<div class="#class#" original="#original#" category="#category#" style="#style#">
							<label class="label_new">{$fields["#fields#"]}:</label>
							{:W('ShowSelect',
							array(
								$vo['#fields#'],
								array(
									'#content_class#',
									array(
										'type'=>('#showoption#'==''?'table':'selectlist'),
										array(
											'key'=>'#showoption#',
											'readonly'=>'#islock#',
											'targevent'=>'#tagevent#',
											'actionName'=>'#nodeName#',
											'names'=>'#fields#',
											'defaultcheckitem'=>'',
											'defaultval'=>'#defaultval#',
											'defaulttext'=>'#defaulttext#',
											'table'=>'#subimporttableobj#', 
											'id'=>'#subimporttablefield2obj#',
											'name'=>'#subimporttablefieldobj#' ,
											'conditions'=>'#subimporttableobjcondition#',
											'parentid'=>'',
											'mulit'=>'',
											'isnextend'=>'',
											'treeheight'=>'',
											'treewidth'=>'',
											'isedit'=>'#islock#',
											'showtype'=>'',
											'defaultcheckitem'=>'#defaultcheckitem#'
											)
										)
									)
								)
							)
							}
						</div>
						