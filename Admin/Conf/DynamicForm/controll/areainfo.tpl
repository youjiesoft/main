	
						<!-- 地址组件 -->
						<div class="#class#" original="#original#" category="#category#" style="#style#">
							<label class="label_new">{$fields["#fields#"]}:</label>
							{:W('ShowArea',
								array("0"=>$areainfoarry["#fields#"],
								"1"=>#fields#,
								"2"=>$fields[#fields#],
								"3"=>"",
								"5"=>"")
							)}
							<div class="display_none {$classNodeSettingArr['#fields#']}">{$vo['#fields#']}</div>
						</div>