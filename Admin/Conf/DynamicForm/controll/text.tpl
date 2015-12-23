							
						<!-- 文本框组件 -->
						<div class="#class#" original="#original#" category="#category#" style="#style#">
							<label class="label_new">{$fields["#fields#"]}:</label>
							<input  dropback="#dropback#" #paramArr# type="text"  name="#fields#" #callback# class=" input_new  #content_class#" <if condition="$vo['#fields#']">value="{$vo['#fields#']}"<else/>value="#defaultvaltext#"</if>  >
							<div class="display_none {$classNodeSettingArr['#fields#']}">{$vo['#fields#']}</div>
						</div>