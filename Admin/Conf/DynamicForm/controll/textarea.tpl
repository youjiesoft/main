						
						<!-- 文本域组件 -->
						<div class="#class#" original="#original#" category="#category#" style="#style#">
							<label class="label_new">{$fields["#fields#"]}:</label>
							<textarea   cols="#cols#" rows="#rows#" #paramArr# class="text_area #content_class#" name="#fields#"><if condition="$vo['#fields#']">{$vo['#fields#']}<else/>#defaultval#</if></textarea>
							<div class="display_none {$classNodeSettingArr['#fields#']}">{$vo['#fields#']}</div>
						</div>
