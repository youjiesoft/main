							
						<!-- 文本框组件 [计量单位] -->
						<div class="#class#" original="#original#" category="#category#" style="#style#">
							<div class="tml-input-unit">
								<label class="label_new">{$fields["#fields#"]}:</label>
								<input  dropback=""  type="text" #paramArr# name="#fields#" class=" input_new half_angle_input #content_class#"
									 <if condition="$vo['#fields#'] neq ''">value="{$vo['#fields#']|unitExchange=###,#unitl#,#unitls#,2}"<else/>value="#defaultvaltext#"</if>>
								<span class="icon_elm icon_unit" title="#unitlsChar#">#unitlsChar#</span>
								</div>
								<div class="display_none {$classNodeSettingArr['#fields#']}">
									{$vo['#fields#']|unitExchange=###,#unitl#,#unitls#,2}
								</div>
						</div>