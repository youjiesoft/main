						
						<!-- 日期组件 -->
						<div class="#class#" original="#original#" category="#category#" style="#style#">
							<label class="label_new">
								{$fields["#fields#"]}:
							</label>
							<div  class="tml-input-calendar">
								<input type="text" name="#fields#" #paramArr# class="input_new half_angle_input Wdate {:'#islock#'?'js-wdate':''}  input_left #content_class#" format='{"dateFmt":"#formatjs#"}'   value="{$vo['#fields#']|transtime='#formatphp#'}"/>
								<a href="javascript:;" class="icon_elm icon-calendar {:'#islock#'?'js-inputCheckDate':''} "></a>
							</div>
							<div class="display_none {$classNodeSettingArr['#fields#']}">
								{$vo['#fields#']|transtime='#formatphp#'}
							</div>
						</div>
	