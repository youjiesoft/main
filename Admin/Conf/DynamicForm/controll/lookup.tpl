					
						<!-- 查找带回组件 -->
						<div class="#class#" original="#original#" category="#category#" style="#style#">
							<label class="label_new">
								{$fields["#fields#"]}:
							</label>
							{:W('Lookup',array('1',$vo,'#propertyid#','1','','#ganshe#','#ismuchchoice#'))}
							<div class="display_none {$classNodeSettingArr['#fields#']}">{:W('Lookup',array('1',$vo,'#propertyid#','1',1))}</div>
						</div>
						