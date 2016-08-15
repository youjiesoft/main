<?php
/*
 * author : xiayq
 * date   : 2016-8-10
 * usage  : 地图定位组件
 */

class ShowMapWidget extends Widget{
/**
 * 特殊说明：该组件不能使用到 dialog中。
 */
	
	/**
	 * @Title: render
	 * @Description: todo(地图定位组件)
	 * @param array $data 
	 * @return string $html  组件html
	 * @author xiayq
	 * @date 2016-8-10 下午3:9:12
	 * @throws
	 */
	public function render($data){
		
		//默认地址重庆
		$detail='';
		//地址详细信息   地址|经度|纬度
		$address_detail=$data[0];
		//横坐标
		$coordinatex = "";
		//纵坐标
		$coordinatey = "";
		//字段名
		$fieldName=$data[1];
		//标题
		$fieldsTitle = $data[2];
		//样式
// 		$fieldsStyle=$data[3];
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
			$datadetail=explode('|', $data[0]);
			$detail  = $datadetail[0];
			$coordinatex = $datadetail[1];
			$coordinatey =$datadetail[2];
			$default="";
		}
		
		
		
		// 清除空值
		$curSouce = array_filter($curSouce);
		$souceStr = '';
		if(count($curSouce)){
			$souceStr = json_encode($curSouce);
		}
		/**
		 * 定义：mapinfo[当前字段名称]
		 * 												[fieldname]	//	当前使用的字段名
		 * 												[detail]		//	完整地址信息，包含下拉级联值及详细地址值
		 * 												[address]	// 详细地址
		 *												[data][]		//下拉级联所有数据 
		 */
		$html=<<<EOF
		
			 <div class="address_elm left">
			 	<input type="hidden" name="mapinfotag" value="1" />
			 	<!-- 当前使用字段	-->
			 	<input type="hidden"  name="mapinfo[{$fieldName}][fieldname]" value="{$fieldName}" />
			 	<!-- 表单使用的当前字段，用于存储最终结果值 -->
			 	<input type="hidden" id="address_detail_{$fieldName}" class="address_detail" name="{$fieldName}" value="{$address_detail}" />
			 	
			 	<!-- 详细地址信息 -->
			 	<input class="address_detail_coordinatex" name="mapinfo[{$fieldName}][coordinatex]" id="address_detail_coordinatex_{$fieldName}" type="hidden" value="{$coordinatex}"/>
			 	<input class="address_detail_coordinatey" name="mapinfo[{$fieldName}][coordinatey]" id="address_detail_coordinatey_{$fieldName}" type="hidden" value="{$coordinatey}"/>
			 	<!-- 地图标注 -->
			 	<a class="icon-map-marker tml_map_link" style="cursor:pointer" onclick="openMap('{$fieldName}');" mask="true" rel="lookupgetMapCoordinate"  title="地图"></a>
			 	<!-- 完整地址信息 -->
			 	<input name="mapinfo[{$fieldName}][detail]"  onBlur="changeAddress('{$fieldName}')" value="{$detail}" id="address_detail_address_{$fieldName}" style="width:calc(100% - 30px)" class="map_area area_push detail split_address input_new left {$required}"  placeholder="地址显示：省、市、区县、详细地址" type="text"  />
			 </div>
EOF;
		return $html;
	}
}