<?php
/*
 * author : quqiang
 * date   : 2014-10-28
 * usage  : 地区信息组件
 */

class ShowAreaWidget extends Widget{
/**
 * 特殊说明：该组件不能使用到 dialog中。
 */
	private function getData(){
		return getDBData(0);
	}
	
	private function getGUID(){
		return intval(microtime(true)*1000);
	}
	/**
	 * @Title: render
	 * @Description: todo(地区信息选择组件)
	 * @param array $data 
	 * @return string $html  组件html
	 * @author quqiang
	 * @date 2014-10-28 下午3:9:12
	 * @throws
	 */
	public function render($data){
		$areadata = $this->getData();
		$gid = $this->getGUID();
		
		//默认城市重庆
		$default = '500000';
		//默认地址重庆
		$detail='重庆';
		//横坐标
		$coordinatex = "";
		//纵坐标
		$coordinatey = "";
		//字段名
		$fieldName=$data[1];
		//标题
		$fieldsTitle = $data[2];
		//样式
		$fieldsStyle=$data[3];
		
		// 必填验证
		$required = $data[5] ;
		$requiredStatus = $required ? true : false ;
		//tablename
		$tableName=$data[4];
		if($fieldName==''){
			return "字段名为空";
		}elseif($fieldsTitle==''){
			return "标题为空";
		}
		$curSouce = array();
		if($data[0]){
			foreach($data[0] as $k=>$v){
			 $curSouce['1']		= $v['ext1'];
			 $curSouce['2']		= $v['ext2'];
			 $curSouce['3']		= $v['ext3'];
			 $curSouce['4']		= $v['ext4'];
			 $curSouce['5']		= $v['ext5'];
			 $address =$v['address'];
			 $detail  = $v['detail'];
			 $coordinatex = $v['coordinatex'];
			 $coordinatey = $v['coordinatey'];
			}
			
			//$default = $sheng.','.$shi.','.$xian;
			$default="";
		}
		
		
		$option = "<option value=''>请选择</option>";
		foreach ($areadata as $k=>$v){
			if($default === $v['id']){
				//默认第一个为重庆
				$curSouce['1']		= $default;
			}
			$option .= '<option  '. ($default === $v['id'] ? 'selected' : '') .' value="'.$v['id'].'">'.$v['name'].'</option>';
		}
		// 清除空值
		$curSouce = array_filter($curSouce);
		$souceStr = '';
		if(count($curSouce)){
			$souceStr = json_encode($curSouce);
		}
		/**
		 * 定义：areainfo[当前字段名称]
		 * 												[fieldname]	//	当前使用的字段名
		 * 												[detail]		//	完整地址信息，包含下拉级联值及详细地址值
		 * 												[address]	// 详细地址
		 *												[data][]		//下拉级联所有数据 
		 */
		$html=<<<EOF
		
			 <div class="address_elm left">
			 	<input type="hidden" name="areainfotag" value="1" />
			 	<!-- 当前使用字段	-->
			 	<input type="hidden"  name="areainfo[{$fieldName}][fieldname]" value="{$fieldName}" />
			 	<!-- 表单使用的当前字段，用于存储最终结果值 -->
			 	<input type="hidden" id="address_detail_{$fieldName}"  class="address_detail" name="{$fieldName}" value="{$detail}" />
			 	
			 	<select data-souce='{$souceStr}' data-required='{$required}' cascade  class=" address_level_elm left select2 next  nbm1 {$required}" names="areainfo[{$fieldName}][data]">{$option}</select>
			 	<!-- 详细地址信息 -->
			 	<input class="address_four_level address input_new left" placeholder="详情地址，具体到街道\村庄" type="text" name="areainfo[{$fieldName}][address]" value="{$address}" />
			 	<input class="address_detail_coordinatex" name="areainfo[{$fieldName}][coordinatex]" id="address_detail_coordinatex_{$fieldName}" type="hidden" value="{$coordinatex}"/>
			 	<input class="address_detail_coordinatey" name="areainfo[{$fieldName}][coordinatey]" id="address_detail_coordinatey_{$fieldName}" type="hidden" value="{$coordinatey}"/>
			 	<!-- 地图标注 -->
			 	<a class="icon-map-marker tml_map_link" style="cursor:pointer" onclick="openMap('{$fieldName}');" mask="true" rel="lookupgetMapCoordinate"  title="地图"></a>
			 	<!-- 完整地址信息 -->
			 	<input name="areainfo[{$fieldName}][detail]" value="{$detail}" id="address_detail_address_{$fieldName}" class="area_push detail split_address input_new left {$required}"  placeholder="地址显示：省、市、区县、详细地址" type="text"  />
			 </div>
EOF;
		// 测试版的调用JS
		$htmls = <<<EOF
		<script>
	\$(function(){
		\$('select.address_level_elm').cascade({box:'div.address_elm'});
			\$('select.address_level_elm').cascade.afterchange=function(curobj , box){
				var str = '';
				$('select option:selected' , box).each(function(){
					var text = $(this).text();
					var reg = new RegExp("^县{1}$|^市辖区{1}|^请选择{1}$",'g');
					text = reg.test(text)?'':text;
					if(text){
						str += $(this).text();
					}
				});
				str+=$.trim($('input.address',box).val());
				$('input.address_detail ,input.detail ',box).val(str);
				$('input.address',box).keyup(function(){
					var str = '';
					$('select option:selected' , box).each(function(){
						var text = $(this).text();
						var reg = new RegExp("^县{1}$|^市辖区{1}|^请选择{1}$",'g');
						text = reg.test(text)?'':text;
						if(text){
							str += $(this).text();
						}
					});
					str+=$.trim($('input.address',box).val());
					$('input.address_detail ,input.detail ',box).val(str);
				});
			}
	});
		</script>
EOF;
		return $html;
	}
}