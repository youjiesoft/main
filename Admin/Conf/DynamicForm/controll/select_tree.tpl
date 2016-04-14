					
						<!-- 下拉树组件 -->
						<div class="#class#" original="#original#" category="#category#" style="#style#">
							<label class="label_new">{$fields["#fields#"]}:</label>
							{:W('ShowSelect',
							array(
								$vo['#fields#'],
								array(
									'#content_class#',
									array(
										'type'=>'table',
										array(
											'key'=>'#showoption#',
											'readonly'=>'0',
											'targevent'=>'#tagevent#',
											'actionName'=>'#nodeName#',
											'names'=>'#fields#',
											'defaultcheckitem'=>'',
											'defaultval'=>'#defaultval#',
											'defaulttext'=>'#defaulttext#',
											'table'=>'#treedtable#', 
											'id'=>'#treevaluefield#',
											'name'=>'#treeshowfield#',
											'conditions'=>'#subimporttableobjcondition#',
											'parentid'=>'#treeparentfield#',
											'mulit'=>'#mulit#',
											'isnextend'=>'#isnextend#',
											'treeheight'=>'#treeheight#',
											'treewidth'=>'#treewidth#',
											'isedit'=>'#islock#',
											'showtype'=>'',
											'treedialog'=>'#isdialog#',
											'defaultcheckitem'=>'#defaultcheckitem#'
											)
										)
									)
								)
							)
							}
							<div class="display_none {$classNodeSettingArr['#fields#']}">
								{:getControllbyHtml('table',array('type'=>'select','table'=>'#treedtable#','id'=>'#treevaluefield#','name'=>'#treeshowfield#','conditions'=>'#subimporttableobjcondition#','selected'=>$vo['#fields#'],'showtype'=>'1'))}
							</div>
						</div>