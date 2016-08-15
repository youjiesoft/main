	
						<!-- 地图组件 -->
						<div class="#class#" original="#original#" category="#category#" style="#style#">
							<label class="label_new">{$fields["#fields#"]}:</label>
							{:W('ShowMap',
								array("0"=>$vo['#fields#'],
								"1"=>#fields#,
								"2"=>$fields[#fields#],
								"3"=>"",
								"5"=>"#content_class#")
							)}
							<div class="display_none {$classNodeSettingArr['#fields#']}">{$vo['#fields#']}</div>
						</div>