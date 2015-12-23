							
						<!-- 隐藏域组件 -->
						<div class="#class#" original="#original#" category="#category#" style="display:none;#style#">
							<label class="label_new">{$fields["#fields#"]}:</label>
							<input type="hidden"  name="#fields#" class=" input_new  #content_class#" #callback# value="{$vo['#fields#']}">
							<div class="display_none {$classNodeSettingArr['#fields#']}">{$vo['#fields#']}</div>
						</div>	