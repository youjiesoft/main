<?php
/**
 * 页面设计配置文件
 * @Title: asd 
 * @Package package_name
 * @Description: todo(页面设计的相关配置信息，用以规范化属性操作。) 
 * @author quqiang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015年5月12日 下午3:59:07 
 * @version V1.0
 */

/**
 * 页面设计可用组件列表
 */
$desing_controll=array(
		'panel'=>array(
				'title'			=>	'显示面板',
				'show'		=>	1,
				'group'		=>	'content',
				
				'html'		=>	'<li class="#direction# $cls" $tag style="height:#height#px;"><div class="title"><span>$title</span><div class="toolbar_cell"><a mask="true" class="design_tool_btn design_tool_edit" href="__URL__/editproperty/id/#primary#"    target="dialog"><i class="icon icon-pencil" ></i>修改</a><a class="design_tool_btn design_tool_delete"href="#" onclick="delpanel(#primary#,this)" ><i class="icon icon-trash"></i>删除</a></div></div><div class="design_edit_content">$content</div>$hidden</li>',
				
				'showhtml'=>	'<li class="#direction# $cls" title="$title" style="height:#height#px;#showdisplay#"><div class="title"><span>$title</span></div><div class="desing_content">$content</div></li>',
				
				
				
				'property'	=>array(
						// 组件类型，重写
						'category'		=>	array(
								'key'		=>	'category',	// 数组key
								'name'	=>	'~key~[#primary#]', // post数据中的name
								'field'	=>	'category',	//	数据库字段
								'title'		=>	'类型',	//	显示标题字符
								'show'	=>	'0',		// 是否启用
								'value'	=>	'panel',	// 默认值
								'type'	=>	'text' , // 属性设置时使用的组件
								'sort'		=>	2,	//	排序
						),
						// 组件默认使用样式
						'controllcls'		=>	array(
								'key'		=>	'controllcls',	// 数组key
								'name'	=>	'~key~[#primary#]', // post数据中的name
								'field'	=>	'controllcls',	//	数据库字段
								'title'		=>	'皮肤',	//	显示标题字符
								'show'	=>	'0',		// 是否启用
								'value'	=>	'panel_group_lay panel_#width#',	// 默认值
								'type'	=>	'text' , // 属性设置时使用的组件
								'sort'		=>	1,	//	排序
								//'from'	=>	'width', //	数据来源对象的key
						),
						//	组件使用宽度，需要与controllcls配合使用
						'width'		=>	array(
								'key'		=>	'width',	// 数组key
								'name'	=>	'~key~[#primary#]', // post数据中的name
								'field'	=>	'width',	//	数据库字段
								'title'		=>	'宽度',	//	显示标题字符
								'show'	=>	'1',		// 是否启用
								'value'	=>	'2',	// 默认值
								'type'	=>	'select' , // 属性设置时使用的组件
								'data'	=>	'1#比例1|2#比例2|3#比例3|4#比例4|5#比例5|6#比例6|7#比例7|8#比例8|9#比例9|10#比例10|11#比例11|12#比例12', // 数据来源
								'sort'		=>	3,	//	排序
						),
						//	组件使用宽度，需要与controllcls配合使用
						'height'		=>	array(
								'key'		=>	'height',	// 数组key
								'name'	=>	'~key~[#primary#]', // post数据中的name
								'field'	=>	'height',	//	数据库字段
								'title'		=>	'高度',	//	显示标题字符
								'show'	=>	'1',		// 是否启用
								'value'	=>	'230',	// 默认值
								'type'	=>	'text' , // 属性设置时使用的组件
								'data'	=>	'', // 数据来源
								'sort'		=>	3,	//	排序
						),
						// 组件默认中文标题
						'title'		=>	array(
							'key'		=>	'title',	// 数组key
							'name'	=>	'~key~[#primary#]', // post数据中的name
							'field'	=>	'title',	//	数据库字段
							'title'		=>	'标题',	//	显示标题字符
							'show'	=>	'1',		// 是否启用
							'value'	=>	'面板组件#index#',	// 默认值
							'type'	=>	'text' , // 属性设置时使用的组件
							'sort'		=>	2,	//	排序
						),
						// 组件所属导航
						'nav'		=>	array(
								'key'		=>	'nav',	// 数组key
								'name'	=>	'~key~[#primary#]', // post数据中的name
								'field'	=>	'nav',	//	数据库字段
								'title'		=>	'所属导航',	//	显示标题字符
								'show'	=>	'1',		// 是否启用
								'value'	=>	'',	// 默认值
								'type'	=>	'select' , // 属性设置时使用的组件
								'dataurl'	=>	'navData', // 数据来源
								'sort'		=>	2,	//	排序
						),
						// 数据源
						'datasouce'		=>	array(
								'key'		=>	'datasouce',	// 数组key
								'name'	=>	'~key~[#primary#]', // post数据中的name
								'field'	=>	'datasouce',	//	数据库字段
								'title'		=>	'数据源',	//	显示标题字符
								'show'	=>	'1',		// 是否启用
								'value'	=>	'',	// 默认值
								'type'	=>	'dialog' , // 属性设置时使用的组件
								'data'	=>	'__URL__/datasouce/id/#primary#', // 数据来源
								'sort'		=>	2,	//	排序
						),
						// 方向
						'direction'		=>	array(
								'key'		=>	'direction',	// 数组key
								'name'	=>	'~key~[#primary#]', // post数据中的name
								'field'	=>	'direction',	//	数据库字段
								'title'		=>	'方向',	//	显示标题字符
								'show'	=>	'1',		// 是否启用
								'value'	=>	'2',	// 默认值
								'type'	=>	'select' , // 属性设置时使用的组件
								'data'	=>	'left#左浮动|right#右浮动', // 数据来源
								'sort'		=>	3,	//	排序
						),
				),
		),
		'navigation'=>array(
				'title'			=>	'导航',
				'show'		=>	0,
				'group'		=>	'nav',
				'html'		=>	'<li $tag>
				<a mask="true" class="design_edit" href="__URL__/editproperty/id/#primary#" target="dialog">修改</a>
				<a class="design_delete" onclick="delpanel(#primary#,this)"  href="#">删除</a>
				<a href="__URL__/desing/id/#masid#/nav/#primary#" rel="DesingFormedit" target="navTab" title="点击后显示当前选项的面板">
				<span class="ui-select #icon#"></span></a><span>$title</span>$hidden</li>',
				
				'showhtml'	=>	'<li><a class="" href="#url#" onclick="navToPanel(#primary#);"><span class="ui-select #icon#"></span></a><span>$title</span></li>',
				'property'	=>array(
						// 组件类型，重写
						'category'		=>	array(
								'key'		=>	'category',	// 数组key
								'name'	=>	'~key~[#primary#]', // post数据中的name
								'field'	=>	'category',	//	数据库字段
								'title'		=>	'类型',	//	显示标题字符
								'show'	=>	'0',		// 是否启用
								'value'	=>	'navigation',	// 默认值
								'type'	=>	'text' , // 属性设置时使用的组件
								'sort'		=>	2,	//	排序
						),
						// 组件默认中文标题
						'title'		=>	array(
								'key'		=>	'title',	// 数组key
								'name'	=>	'~key~[#primary#]', // post数据中的name
								'field'	=>	'title',	//	数据库字段
								'title'		=>	'标题',	//	显示标题字符
								'show'	=>	'1',		// 是否启用
								'value'	=>	'导航组件#index#',	// 默认值
								'type'	=>	'text' , // 属性设置时使用的组件
								'sort'		=>	2,	//	排序
						),
						// 组件默认使用样式
						'controllcls'		=>	array(
								'key'		=>	'controllcls',	// 数组key
								'name'	=>	'~key~[#primary#]', // post数据中的name
								'field'	=>	'controllcls',	//	数据库字段
								'title'		=>	'皮肤',	//	显示标题字符
								'show'	=>	'0',		// 是否启用
								'value'	=>	'ui-select #icon#',	// 默认值
								'type'	=>	'text' , // 属性设置时使用的组件
								'sort'		=>	1,	//	排序
								//'from'	=>	'icon', //	数据来源对象的key
						),
						// 组件默认使用样式
						'icon'		=>	array(
								'key'		=>	'icon',	// 数组key
								'name'	=>	'~key~[#primary#]', // post数据中的name
								'field'	=>	'icon',	//	数据库字段
								'title'		=>	'图标',	//	显示标题字符
								'show'	=>	'1',		// 是否启用
								'value'	=>	'my-destop',	// 默认值
								'type'	=>	'select' , // 属性设置时使用的组件
								'data'	=>	'my-destop#图标1|my-app#图标2|my-box#图标3|my-folder#图标4', // 数据来源
								'sort'		=>	1,	//	排序
						),
				),
		),
);

/**
 * 属性默认值
 */
$desing_default=array(
		// 组件默认使用样式
		'primary'		=>	array(
				'key'		=>	'primary',	// 数组key
				'name'	=>	'~key~[#primary#]', // post数据中的name
				'field'	=>	'id',	//	数据库字段
				'title'		=>	'ID',	//	显示标题字符
				'show'	=>	'1',		// 是否启用
				'value'	=>	'',	// 默认值
				'type'	=>	'text' , // 属性设置时使用的组件
				'sort'		=>	0,	//	排序
		),
		// 组件类型
		'category'		=>	array(
				'key'		=>	'category',	// 数组key
				'name'	=>	'~key~[#primary#]', // post数据中的name
				'field'	=>	'category',	//	数据库字段
				'title'		=>	'类型',	//	显示标题字符
				'show'	=>	'0',		// 是否启用
				'sort'		=>	2,	//	排序
		),
		//	排序
		'sort'		=>	array(
				'key'		=>	'sort',	// 数组key
				'name'	=>	'~key~[#primary#]', // post数据中的name
				'field'	=>	'sort',	//	数据库字段
				'title'		=>	'排序',	//	显示标题字符
				'show'	=>	'1',		// 是否启用
				'value'	=>	'',	// 默认值
				'type'	=>	'text' , // 属性设置时使用的组件
				'data'	=>	'', // 数据来源
				'sort'		=>	4,	//	排序
		),
		//	所属父级
		'masid'		=>	array(
				'key'		=>	'masid',	// 数组key
				'name'	=>	'~key~[#primary#]', // post数据中的name
				'field'	=>	'masid',	//	数据库字段
				'title'		=>	'所属父级',	//	显示标题字符
				'show'	=>	'0',		// 是否启用
				'value'	=>	'',	// 默认值
				'type'	=>	'text' , // 属性设置时使用的组件
				'data'	=>	'', // 数据来源
				'sort'		=>	5,	//	排序
		),
);
/**
 * 属性数据来源，
 * 1：直接配置，data		可分隔数组
 * 2：代码获取，dataurl // action地址
 */
?>