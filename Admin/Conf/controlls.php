<?php
/**
 * 组件结构配置文件
 */
return array(
	// 文本框 
	'text'		=>	array(
		'show'	=>	1,	//是否显示在工具列表中
		'title'	=>'短文本框',
		'iscreate'	=>	1 , //是否生成到数据库中字段
		'isview'	=>	1 , //是否生成到视图页面
		'weight'	=>	1, // 权重，用处：在字段生成时出现的默认排序位置。
		'isline' => false, // 一个标签是否占一行
		'isconfig'=>	1 , // 是否生成字段配置
		'listwidth'=>'',//列表宽度
		'html'	=>	'
		<div class="nbm_controll $isshow" data="text" $checkorder $isline >
		<label class="label_new"><i class="$copycontrollcls"></i> $title</label><input type="text" class="input_new"> $hidden
		$delTag
		</div>
		
		<div class="nbmshadow short-element">
	           <div class="icon_stort_lay">$statusHtml</div>
	        </div>
		',
		'property' => array(
				'subimporttableobj'	=>	array(
						'title'	=>	'固定显示-表',
						'type'	=>	'select',
						'id'	=>	'subimporttableobj',
						'name'	=>	'subimporttableobj',
						'data'	=>	'',
						'dataAdd'=>'getTables',
						'linkto' => 'subimporttablefieldobj', // 关联下拉框的ID值，作用为：当该对象cnahge时关系对象的值更新。被更新对象的值参数为当前change后的值
						'group'=>'c',
						'toggle'=>'d',
						'displayright'=>1,
						'isforeignoprate' => 1,
						'dbfield'=>'subimporttableobj',
				
				),'subimporttablefieldobj'	=>	array(
						'title'	=>	'固定显示-显示字段',
						'type'	=>	'select',
						'id'	=>	'subimporttablefieldobj',
						'name'	=>	'subimporttablefieldobj',
						'data'	=>	'',
						'dataAdd'=>'comboxgetTableField',
						'parentto' => 'subimporttableobj', //重新获取值时条件值来源对象
						'group'=>'c',
						'toggle'=>'d',
						'displayright'=>1,
						'isforeignoprate' => 1,
						'dbfield'=>'subimporttablefieldobj',
				
				),
				'defaultval'=>array(
						'title'	=>	'默认值',
						'type'	=>	'select',
						'id'	=>	'defaultval',
						'name'	=>	'defaultval',
						'data'	=>	'',
						'dataAdd'=>'',
						'parentto' => '', //重新获取值时条件值来源对象
						'dbfield'=>'defaultval',
						'displayright'=>1,
						'isforeignoprate' => 1,
						'data'	=>	'|请选择#C(\'USER_AUTH_KEY\')|用户id#user_shangji_id|用户上级id#user_txsj|调休时间#user_anquandengji_id|用户安全等级#companyid|公司id#user_job_id|岗位id#email|用户email#user_employeid|员工id#user_dep_id|当前部门id#user_duty_id|用户职级id#loginUserName|用户中文名称',
				),
				'defaultvaltext'=>array(
						'title'	=>	'固定默认值',
						'type'	=>	'text',
						'id'	=>	'defaultvaltext',
						'name'	=>	'defaultvaltext',
						'data'	=>	'',
						'dataAdd'=>'',
						'parentto' => '', //重新获取值时条件值来源对象
						'dbfield'=>'defaultvaltext',
						'displayright'=>1,
						'isforeignoprate' => 1,
				),
				'subimporttablefield2obj'	=>	array(
						'title'	=>	'固定显示-是否时时获取',
						'type'	=>	'checkbox',
						'id'	=>	'subimporttablefield2obj',
						'name'	=>	'subimporttablefield2obj',
						'data'	=>	'',
						'group'=>'c',
						'toggle'=>'d',
						'isforeignoprate' => 1,
						'displayright'=>1,
						'dbfield'=>'subimporttablefield2obj',
				
				),'conditions'	=>	array(
						'title'	=>	'固定显示-过滤条件',
						'type'	=>	'text',
						'id'	=>	'subimporttableobjcondition',
						'name'	=>	'subimporttableobjcondition',
						'group'=>'c',
						'toggle'=>'d',
						'displayright'=>1,
						'isforeignoprate' => 1,
						'dbfield'=>'lookupconditions',
				),
				'additionalconditions'	=>	array(//这个key不可修改，页面业务中已使用
				'title'	=>	'固定显示-附加条件',
				'type'	=>	'dialog',
				'id'	=>	'additionalconditions',
				'name'	=>	'additionalconditions',
				'dbfield'=>'additionalconditions',
				'dialogcontroll'=>'textadditionalconditions',// 对话框请求的php函数名
				'dialogtitle'=>'附加条件设置',
				'allowcontroll'	=>	'select,text,lookup',				//		允许加入到looup附加条件的组件。
				'notallowfiled'	=>	'#self#,id,orderno',								//		不允许使用的字段。如果是本字段，就书写为 #self# 其它字段为原字段名，多字段以逗号分隔。
				'displayright'=>1,
				'isforeignoprate' => 1,
			),
			'requiredfield'	=>	array( // 从检查类型中抽取出来的检查属性
					'title'	=>	'是否必填',
					'type'	=>	'checkbox',
					'default'=>'0',
					'id'	=>	'requiredfield',
					'name'	=>	'requiredfield',
					'dbfield'=>'isrequired',
			    'function'=>'changetagsortandrequired',
					'isforeignoprate' => 1,
					'displayright'=>1,
			),'checkfunc'	=>	array(
					'title'	=>	'检查类型',
					'type'	=>	'select',
					'default'=>'',
					'id'	=>	'checkfunc',
					'name'	=>	'checkfunc',
					'dbfield'=>'validatetype',
					'isforeignoprate' => 1,
					// select option(val|text ) # op # op
					'data'	=>	'|无需验证#eamil|邮箱#url|网址#number|数字#digits|整数#double|浮点数#lettersonly|字母',
					'displayright'=>1,
			),'calculate'	=>	array(//这个key不可修改，页面业务中已使用
				'title'	=>	'组件关联计算设置',
				'type'	=>	'dialog',
				'id'	=>	'calculate',
				'name'	=>	'calculate',
				'dbfield'=>'calculate',
				'isforeignoprate' => 1,
				'dialogcontroll'=>'calculatecontroll',
				'dialogtitle'=>'组件关联计算设置',
				'displayright'=>1,
			),
			'validdigit'	=>	array(//这个key不可修改，页面业务中已使用
					'title'	=>	'组件计算有效位数',
					'type'	=>	'text',
					'id'	=>	'validdigit',
					'name'	=>	'validdigit',
					'dbfield'=>'validdigit',
					'displayright'=>1,
					'default'=>'0',
					'isforeignoprate' => 1,
			),
			'org'	=>	array(
					'title'	=>	'ORG设置',
					'type'	=>	'select',
					'id'	=>	'org',
					'name'	=>	'org',
					'data'	=>	'',
					'dataAdd'=>'',
					'datafunc'=>'getLookupinfo',//获取数据的JS函数名
					'group'=>'o',
					'toggle'=>'p',
					'displayright'=>1,
					'dbfield'=>'org',
					'isforeignoprate' => 1,
			),
			/* 'dropback'	=>	array(
				'title'	=>	'下拉框返写设置',
				'type'	=>	'select',
				'id'	=>	'dropback',
				'name'	=>	'dropback',
				//'data'	=>	'',
				//'dataAdd'=>'getDropbackinfo',
				//'datafunc'=>'getDropbackinfo',//获取数据的JS函数名
				'displayright'=>1,
				'dbfield'=>'dropback',
				'isforeignoprate' => 1,
			), */
			'unitl'=>array( // 是存储
				'title'	=>	'基本单位',
				'type'	=>	'select',
				'default'=>'',
				'id'	=>	'unitl',
				'name'	=>	'unitl',
				'data'	=>	'',
				'displayright'=>1,
				'dataAdd'=>'getBaseUnit',
				'linkto' => 'unitls',
				'dbfield'=>'unit',
				'isforeignoprate' => 1,
			)
			,'unitls'=>array( // 是显示
				'title'	=>	'显示单位',
				'type'	=>	'select',
				'default'=>'',
				'id'	=>	'unitls',
				'name'	=>	'unitls',
				'data'	=>	'',
				'displayright'=>1,
				'dataAdd'=>'getSubUnit',
				'parentto' => 'unitl', //重新获取值时条件值来源对象
				'dbfield'=>'unitls',
				'isforeignoprate' => 1,
			)
			,'checkfortable'=>array(
				'title'	=>	'checkfor查询表',
				'type'	=>	'text',
				'id'	=>	'checkfortableid',
				'name'	=>	'checkfortablename',
				'dbfield'=>'checkfortablename',
				'displayright'=>0,
				'isforeignoprate' => 1,
			),'checkforshow'=>array(
				'title'	=>	'显示字段名',
				'type'	=>	'text',
				'id'	=>	'checkforshowid',
				'name'	=>	'checkforshowname',
				'dbfield'=>'checkforshowname',
				'displayright'=>0,
				'isforeignoprate' => 1,
			),'checkforbindd'=>array(
				'title'	=>	'隐藏域绑定字段名',
				'type'	=>	'text',
				'id'	=>	'checkforbindid',
				'name'	=>	'checkforbindname',
				'dbfield'=>'checkforbindname',
				'displayright'=>0,
				'isforeignoprate' => 1,
			),'checkformap'=>array(
				'title'	=>	'过滤条件',
				'type'	=>	'text',
				'id'	=>	'checkformap',
				'name'	=>	'checkformap',
				'dbfield'=>'checkformap',
				'displayright'=>0,
				'isforeignoprate' => 1,
			),'checkfororderby'=>array(
				'title'	=>	'排序条件',
				'type'	=>	'text',
				'id'	=>	'checkfororderby',
				'name'	=>	'checkfororderby',
				'dbfield'=>'checkfororderby',
				'displayright'=>0,
				'isforeignoprate' => 1,
			),'checkfororfields'=>array(
				'title'	=>	'checkfor弹出列表显示字段',
				'type'	=>	'text',
				'id'	=>	'checkfororfields',
				'name'	=>	'checkfororfields',
				'dbfield'=>'checkfororfields',
				'displayright'=>0,
				'isforeignoprate' => 1,
			),
			'checkforchoice'=>array(	// 前置lookup属性选择
				'title'	=>	'checkfor属性选择',
				'type'	=>	'select',
				'id'	=>	'checkforchoice',
				'name'	=>	'checkforchoice',
				'displayright'=>0,
				'group'=>'p',
				'toggle'=>'o',
				'dataAdd'=>'checkforconfig',
				'afterfunc'=>'checkforchoice', // 用户自定义后置函数，在右侧属性查看、当前标签默认事件触发时 被调用
				'rel'=>'checkfortable:query_table;checkforshow:show_fields;checkforbindd:hidden_field;checkformap:filter_condition;checkfororderby:sort_condition;checkfororfields:fields',	//当前checkfor属性key与前置属性key对应关系
				'dbfield'=>'checkforchoice',
				'isforeignoprate' => 1,
			)
			,'checkforiswrite'=>array(
				'title'	=>	'是否清除未找到内容',
				'type'	=>	'checkbox',
				'id'	=>	'checkforiswrite',
				'name'	=>	'checkforiswrite',
				'dbfield'=>'checkforiswrite',
				'displayright'=>0,
				'isforeignoprate' => 1,
			),'islock'	=>	array(
				'isforeignoprate' => 1,
				'title'	=>	'是否允许编辑',
				'type'	=>	'checkbox',
				'default'=>1,
				'id'	=>	'islock',
				'name'	=>	'islock',
				'displayright'=>1,
				'dbfield'=>'islock',
				'function'=>'changetagsortandlock',
				'isforeignoprate' => 1,
			),'tagevent' => array(
				'title'	=>	'事件绑定',
				'type'	=>	'select',
				'id'	=>	'tagevent',
				'name'	=>	'tagevent',
				'displayright'=>1,
				'dbfield'=>'tagevent',
				'isforeignoprate' => 1,
				'data'	=>	'|请选择#change|change()',
			),
// 				'dropback'	=>	array(
// 						'title'	=>	'下拉框返写设置',
// 						'type'	=>	'select',
// 						'id'	=>	'dropback',
// 						'name'	=>	'dropback',
// 						//'data'	=>	'',
// 						//'dataAdd'=>'',
// 						//'datafunc'=>'getLookupinfo',//获取数据的JS函数名
// 						'displayright'=>1,
// 						'dbfield'=>'dropback',
// 						'isforeignoprate' => 1,
// 				),
				'titlepercent'=>array(
				'title'	=>	'标题占比',
				'type'	=>	'select',
				'id'	=>	'titlepercent',
				'name'	=>	'titlepercent',
				'displayright'	=>	1,
				'dbfield'=>'titlepercent',
				'default'=>'1',
				'isforeignoprate' => 1,
				'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列',
				'function'=>'titlepercentchange',
			),'contentpercent'=>array(
				'title'	=>	'内容框占比',
				'type'	=>	'select',
				'id'	=>	'contentpercent',
				'name'	=>	'contentpercent',
				'displayright'	=>	1,
				'dbfield'=>'contentpercent',
				'default'=>'3',
				'isforeignoprate' => 1,
				'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
				'function'=>'contentpercentchange',
				'isforeignoprate' => 1,
			),'callback'	=>	array(//这个key不可修改，页面业务中已使用
				'title'	=>	'回调函数',
				'type'	=>	'text',
				'id'	=>	'callback',
				'name'	=>	'callback',
				'isforeignoprate' => 1,
				'displayright'=>1,
				'dbfield'=>'callback',
			)
		),
	),
		// 密码框
		'password'		=>	array(
				'show'	=>	1,	//是否显示在工具列表中
				'title'	=>'密码框',
				'iscreate'	=>	1 , //是否生成到数据库中字段
				'isview'	=>	1 , //是否生成到视图页面
				'weight'	=>	1, // 权重，用处：在字段生成时出现的默认排序位置。
				'isline' => false, // 一个标签是否占一行
				'isconfig'=>	1 , // 是否生成字段配置
				'listwidth'=>'',//列表宽度
				'html'	=>	'
		<div class="nbm_controll $isshow" data="password" $checkorder $isline >
		<label class="label_new"><i class="$copycontrollcls"></i> $title</label><input type="password" class="input_new"> $hidden
		$delTag
		</div>
		
		<div class="nbmshadow short-element">
	           <div class="icon_stort_lay">$statusHtml</div>
	        </div>
		',
				'property' => array(
						'requiredfield'	=>	array( // 从检查类型中抽取出来的检查属性
								'title'	=>	'是否必填',
								'type'	=>	'checkbox',
								'default'=>'0',
								'id'	=>	'requiredfield',
								'name'	=>	'requiredfield',
								'dbfield'=>'isrequired',
						    'function'=>'changetagsortandrequired',
								'isforeignoprate' => 1,
								'displayright'=>1,
						),'islock'	=>	array(
								'isforeignoprate' => 1,
								'title'	=>	'是否允许编辑',
								'type'	=>	'checkbox',
								'default'=>1,
								'id'	=>	'islock',
								'name'	=>	'islock',
								'displayright'=>1,
								'dbfield'=>'islock',
								'function'=>'changetagsortandlock',
								'isforeignoprate' => 1,
						),'titlepercent'=>array(
								'title'	=>	'标题占比',
								'type'	=>	'select',
								'id'	=>	'titlepercent',
								'name'	=>	'titlepercent',
								'displayright'	=>	1,
								'dbfield'=>'titlepercent',
								'default'=>'1',
								'isforeignoprate' => 1,
								'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列',
								'function'=>'titlepercentchange',
						),'contentpercent'=>array(
								'title'	=>	'内容框占比',
								'type'	=>	'select',
								'id'	=>	'contentpercent',
								'name'	=>	'contentpercent',
								'displayright'	=>	1,
								'dbfield'=>'contentpercent',
								'default'=>'3',
								'isforeignoprate' => 1,
								'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
								'function'=>'contentpercentchange',
								'isforeignoprate' => 1,
						)
		
				),
		),
	'tablabel'	=>	array(
		'show'	=>	1,
		'title'	=>	'标签',
		'iscreate'	=>	0 , //是否生成到数据库中字段
		'isview'	=>	0 , //是否生成到视图页面
		'isline' => false, // 一个标签是否占一行
		'isconfig'=>	0 , // 是否生成字段配置
		'listwidth'=>'',//列表宽度
		'html'	=>	'
			 <div class="nbm_controll $isshow" data="tablabel" $checkorder $isline>
				<label class="label_new"><i class="$copycontrollcls"></i> $title</label>				
  				<input type="text" class="input_new" value="内容来自外部设置">
  				$delTag $hidden 
			</div>
			<div class="nbmshadow">
	           <div class="icon_stort_lay">$statusHtml</div>
	        </div>
		',
		'view'=>' '
		,'property' => array(
			
			'requiredfield'	=>	array( // 从检查类型中抽取出来的检查属性
					'title'	=>	'是否必填',
					'type'	=>	'checkbox',
					'default'=>'0',
					'id'	=>	'requiredfield',
					'name'	=>	'requiredfield',
					'dbfield'=>'isrequired',
			    'function'=>'changetagsortandrequired',
					'isforeignoprate' => 1,
					'displayright'=>1,
			),'checkfunc'	=>	array(
					'title'	=>	'检查类型',
					'type'	=>	'select',
					'default'=>'',
					'id'	=>	'checkfunc',
					'name'	=>	'checkfunc',
					'dbfield'=>'validatetype',
					'isforeignoprate' => 1,
					// select option(val|text ) # op # op
					'data'	=>	'1|无需验证#eamil|邮箱#url|网址#number|整数#double|浮点数#lettersonly|字母',
					'displayright'=>1,
			),
			'org'	=>	array(
				'title'	=>	'ORG设置',
				'type'	=>	'select',
				'id'	=>	'org',
				'name'	=>	'org',
				'data'	=>	'',
				'dataAdd'=>'',
				'datafunc'=>'getLookupinfo',//获取数据的JS函数名
				'displayright'=>1,
				'isforeignoprate' => 1,
				'dbfield'=>'org',
			),'titlepercent'=>array(
					'title'	=>	'标题占比',
					'type'	=>	'select',
					'id'	=>	'titlepercent',
					'name'	=>	'titlepercent',
					'displayright'	=>	1,
					'dbfield'=>'titlepercent',
					'default'=>'1',
					'isforeignoprate' => 1,
					'function'=>'titlepercentchange',
					'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
			),'contentpercent'=>array(
					'title'	=>	'内容框占比',
					'type'	=>	'select',
					'id'	=>	'contentpercent',
					'name'	=>	'contentpercent',
					'displayright'	=>	1,
					'dbfield'=>'contentpercent',
					'isforeignoprate' => 1,
					'default'=>'3',
					'dbfield'=>'contentpercent',
					'function'=>'contentpercentchange',
					'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
			),'islock'	=>	array(
				'title'	=>	'是否允许编辑',
				'type'	=>	'checkbox',
				'default'=>1,
				'id'	=>	'islock',
				'name'	=>	'islock',
				'isforeignoprate' => 1,
				'displayright'=>0,
				'dbfield'=>'islock',
				'function'=>'changetagsortandlock',
			)
		)
	),
	'hiddens'	=>	array(
		'show'	=>	1,
		'title'	=>	'隐藏域组件',
		'iscreate'	=>	1 , //是否生成到数据库中字段
		'isview'	=>	1 , //是否生成到视图页面
		'isline' => false, // 一个标签是否占一行
		'isconfig'=>	0 , // 是否生成字段配置
		'listwidth'=>'',//列表宽度
		'html'	=>	'
		<div class="nbm_controll $isshow" data="hiddens" $checkorder $isline >
		<label class="label_new"><i class="$copycontrollcls"></i> $title</label><input type="text" class="input_new">
		<span class="icon-unlink">隐藏域</span>
		$hidden
		$delTag
		</div>
		
		<div class="nbmshadow short-element">
	        <div class="icon_stort_lay">$statusHtml</div>
	    </div>
		
		','property' => array(
			'requiredfield'	=>	array( // 从检查类型中抽取出来的检查属性
					'title'	=>	'是否必填',
					'type'	=>	'checkbox',
					'default'=>'0',
					'id'	=>	'requiredfield',
					'name'	=>	'requiredfield',
					'dbfield'=>'isrequired',
			    'function'=>'changetagsortandrequired',
					'isforeignoprate' => 1,
					'displayright'=>1,
			),'checkfunc'	=>	array(
					'title'	=>	'检查类型',
					'type'	=>	'select',
					'default'=>'',
					'id'	=>	'checkfunc',
					'name'	=>	'checkfunc',
					'dbfield'=>'validatetype',
					'isforeignoprate' => 1,
					// select option(val|text ) # op # op
					'data'	=>	'|无需验证#eamil|邮箱#url|网址#number|数字#digits|整数#double|浮点数#lettersonly|字母',
					'displayright'=>1,
			),
			'org'	=>	array(
				'title'	=>	'ORG设置',
				'type'	=>	'select',
				'id'	=>	'org',
				'name'	=>	'org',
				'data'	=>	'',
				'dataAdd'=>'',
				'datafunc'=>'getLookupinfo',//获取数据的JS函数名
				'displayright'=>1,
				'isforeignoprate' => 1,
				'dbfield'=>'org',
			),
			'isviewshow'	=>	array(
				'title'	=>	'是否在view页面显示',
				'type'	=>	'checkbox',
				'id'	=>	'isviewshow',
				'name'	=>	'isviewshow',
				'displayright'=>1,
				'isforeignoprate' => 1,
				'dbfield'=>'isviewshow',
			),'titlepercent'=>array(
				'title'	=>	'标题占比',
				'type'	=>	'select',
				'id'	=>	'titlepercent',
				'name'	=>	'titlepercent',
				'displayright'	=>	1,
				'dbfield'=>'titlepercent',
				'isforeignoprate' => 1,
				'default'=>'1',
				'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
					'function'=>'titlepercentchange',
			),'contentpercent'=>array(
				'title'	=>	'内容框占比',
				'type'	=>	'select',
				'id'	=>	'contentpercent',
				'name'	=>	'contentpercent',
				'displayright'	=>	1,
				'dbfield'=>'contentpercent',
				'default'=>'3',
				'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
				'function'=>'contentpercentchange',
				'isforeignoprate' => 1,
			),'islock'	=>	array(
				'title'	=>	'是否允许编辑',
				'type'	=>	'checkbox',
				'default'=>1,
				'id'	=>	'islock',
				'name'	=>	'islock',
				'displayright'=>0,
				'dbfield'=>'islock',
				'function'=>'changetagsortandlock',
				'isforeignoprate' => 1,
			),'callback'	=>	array(//这个key不可修改，页面业务中已使用
				'title'	=>	'回调函数',
				'type'	=>	'text',
				'id'	=>	'callback',
				'name'	=>	'callback',
				'isforeignoprate' => 1,
				'displayright'=>1,
				'dbfield'=>'callback',
			)
		)
	),
	// 下拉框
	'select'	=>	array(
		'show'	=>	1,
		'title'	=>	'下拉框',
		'iscreate'	=>1 ,
		'isview'	=>	1 , //是否生成到视图页面
		'weight'	=>	2, // 权重，用处：在字段生成时出现的默认排序位置。
		'isline' => false,
		'listwidth'=>'',//列表宽度
		'isconfig'=>	1 , // 是否生成字段配置
		'html'	=>	'<div class="nbm_controll $isshow" data="select" $checkorder $isline >
		<label class="label_new"><i class="$copycontrollcls"></i> $title</label><select class="select2 select_elm" disabled></select>
		$delTag $hidden 
		</div>
		<div class="nbmshadow short-element">
	        <div class="icon_stort_lay">$statusHtml</div>
	    </div>
		',
		'property' => array(
			/* 'dropbackkey'=>array(
					'title'	=>	'下拉返写标识',
					'type'	=>	'text',
					'default'=>'#self##id#',
					'id'	=>	'dropbackkey',
					'name'	=>	'dropbackkey',
					'dbfield'=>'dropbackkey',
					'displayright'=>1,
					'isforeignoprate' => 1,
			), */
			'requiredfield'	=>	array( // 从检查类型中抽取出来的检查属性
					'title'	=>	'是否必填',
					'type'	=>	'checkbox',
					'default'=>'0',
					'id'	=>	'requiredfield',
					'name'	=>	'requiredfield',
					'dbfield'=>'isrequired',
			    'function'=>'changetagsortandrequired',
					'displayright'=>1,
					'isforeignoprate' => 1,
			),'islock'	=>	array(
				'title'	=>	'是否允许编辑',
				'type'	=>	'checkbox',
				'default'=>1,
				'id'	=>	'islock',
				'name'	=>	'islock',
				'dbfield'=>'islock',
				'displayright'=>1,
				'function'=>'changetagsortandlock',
				'isforeignoprate' => 1,
			),'isshowresoult'=>array(
				'title'	=>	'是否直接显示内容',
				'type'	=>	'checkbox',
				'id'	=>	'isshowresoult',
				'name'	=>	'isshowresoult',
				'data'	=>	'',
				'dbfield'=>'isshowresoult',
				'displayright'=>0,
				'isforeignoprate' => 1,
			),'defaultcheckitem'=>array(
				'title'	=>	'默认选中项的值',
				'type'	=>	'text',
				'id'	=>	'defaultcheckitem',
				'name'	=>	'defaultcheckitem',
				'data'	=>	'',
				//'dataAdd'=>'getDefaultCheckItem',
				//'datafunc' => 'setDefaultCheckItem',
				//'customevent'=>'givedefaultitemdata',
				//'customfunc'=>'setDefaultCheckItem',
				//'parentto' => 'treedtable,showoption,subimporttableobj', //重新获取值时条件值来源对象
				//'parame' => '{"showoption":["showoption"],"subimporttableobj":[{"key":"subimporttableobj","require":1},{"key":"subimporttablefieldobj","require":1},{"key":"subimporttablefield2obj","require":1},{"key":"conditions","require":0}],"treedtable":[{"key":"treedtable","require":1},{"key":"treeshowfield","require":1},{"key":"treevaluefield","require":1},{"key":"conditions","require":0}]}', //重新获取值时条件值来源对象
				'dbfield'=>'defaultcheckitem',
				'displayright'=>1,
				'isforeignoprate' => 1,
			),'defaultval'=>array(
				'title'	=>	'首项值',
				'type'	=>	'text',
				'id'	=>	'defaultval',
				'name'	=>	'defaultval',
				'data'	=>	'',
				'dataAdd'=>'comboxgetTableField',
				'parentto' => 'treedtable', //重新获取值时条件值来源对象
				'dbfield'=>'defaultval',
				'displayright'=>1,
				'isforeignoprate' => 1,
			),'defaulttext'=>array(
				'title'	=>	'首项文本',
				'type'	=>	'text',
				'id'	=>	'defaulttext',
				'name'	=>	'defaulttext',
				'data'	=>	'',
				'dataAdd'=>'comboxgetTableField',
				'parentto' => 'treedtable', //重新获取值时条件值来源对象
				'dbfield'=>'defaulttext',
				'displayright'=>1,
				'isforeignoprate' => 1,
			),
// 				'dropbackkey'=>array(
// 						'title'	=>	'下拉返写标识',
// 						'type'	=>	'text',
// 						'default'=>'#self##id#',
// 						'id'	=>	'dropbackkey',
// 						'name'	=>	'dropbackkey',
// 						'dbfield'=>'dropbackkey',
// 						'displayright'=>0,
// 						'isforeignoprate' => 1,
// 				),
			'showoption'	=>	array(
				'title'	=>	'默认选项数据',
				'type'	=>	'select',
				'group'=>'c',
				'toggle'=>'d,e',
				'data'	=>	'',
				'dataAdd'=>'getOptions', // 数据来源地址
				'id'	=>	'showoption',
				'name'	=>	'showoption',
				'displayright'=>1,
				'dbfield'=>'showoption',
				'isforeignoprate' => 1,
			),'subimporttableobj'	=>	array(
				'title'	=>	'绑定数据表',
				'type'	=>	'select',
				'id'	=>	'subimporttableobj',
				'name'	=>	'subimporttableobj',
				'data'	=>	'',
				'group'=>'d',
				'toggle'=>'c,e',
				'dataAdd'=>'getTables',
				'linkto' => 'subimporttablefieldobj,subimporttablefield2obj', // 关联下拉框的ID值，作用为：当该对象cnahge时关系对象的值更新。被更新对象的值参数为当前change后的值
				'displayright'=>1,
				'dbfield'=>'subimporttableobj',
				'isforeignoprate' => 1,
			),'subimporttablefieldobj'	=>	array(
				'title'	=>	'绑定显示字段',
				'type'	=>	'select',
				'id'	=>	'subimporttablefieldobj',
				'name'	=>	'subimporttablefieldobj',
				'group'=>'d',
				'toggle'=>'c,e',
				'data'	=>	'',
				'dataAdd'=>'comboxgetTableField',
				'parentto' => 'subimporttableobj', //重新获取值时条件值来源对象
				'displayright'=>1,
				'dbfield'=>'subimporttablefieldobj',
				'isforeignoprate' => 1,
				
			),'subimporttablefield2obj'	=>	array(
				'title'	=>	'绑定值字段',
				'type'	=>	'select',
				'id'	=>	'subimporttablefield2obj',
				'name'	=>	'subimporttablefield2obj',
				'group'=>'d',
				'toggle'=>'c,e',
				'data'	=>	'',
				'dataAdd'=>'comboxgetTableField',
				'parentto' => 'subimporttableobj', //重新获取值时条件值来源对象
				'displayright'=>1,
				'dbfield'=>'subimporttablefield2obj',
				'isforeignoprate' => 1,
				
			),'conditions'	=>	array(
				'title'	=>	'过滤条件(绑定数据表时)',
				'type'	=>	'text',
				'id'	=>	'subimporttableobjcondition',
				'name'	=>	'subimporttableobjcondition',
				'toggleevent'=>'defaultcheckitem',
				'displayright'=>1,
				'dbfield'=>'lookupconditions',
				'isforeignoprate' => 1,
			),'org'	=>	array(
				'title'	=>	'ORG设置',
				'type'	=>	'select',
				'id'	=>	'org',
				'name'	=>	'org',
				'data'	=>	'',
				'dataAdd'=>'',
				'datafunc'=>'getLookupinfo',//获取数据的JS函数名
				'displayright'=>1,
				'dbfield'=>'org',
				'isforeignoprate' => 1,
			),'treedtable' => array(
				'title'	=>	'树形数据显示-绑定表名',
				'type'	=>	'select',
				'id'	=>	'treedtable',
				'name'	=>	'treedtable',
				'data'	=>	'',
				'group'=>'e',
				'toggle'=>'c,d',
				'dataAdd'=>'getTables',
				'linkto' => 'treeshowfield,treevaluefield,treeparentfield', // 关联下拉框的ID值，作用为：当该对象cnahge时关系对象的值更新。被更新对象的值参数为当前change后的值
				'toggleevent'=>'defaultcheckitem',
				'displayright'=>1,
				'dbfield'=>'treedtable',
				'isforeignoprate' => 1,
				
			),'treeshowfield'=>array(
				'title'	=>	'树形-Text',
				'type'	=>	'select',
				'id'	=>	'treeshowfield',
				'name'	=>	'treeshowfield',
				'group'=>'e',
				'toggle'=>'c,d',
				'data'	=>	'',
				'dataAdd'=>'comboxgetTableField',
				'parentto' => 'treedtable', //重新获取值时条件值来源对象
					'toggleevent'=>'defaultcheckitem',
				'displayright'=>1,
				'dbfield'=>'treeshowfield',
					'isforeignoprate' => 1,
			),'treevaluefield'=>array(
				'title'	=>	'树形-Value',
				'type'	=>	'select',
				'id'	=>	'treevaluefield',
				'name'	=>	'treevaluefield',
				'group'=>'e',
				'toggle'=>'c,d',
				'data'	=>	'',
				'dataAdd'=>'comboxgetTableField',
				'parentto' => 'treedtable', //重新获取值时条件值来源对象
					'toggleevent'=>'defaultcheckitem',
				'displayright'=>1,
				'dbfield'=>'treevaluefield',
					'isforeignoprate' => 1,
			),'treeparentfield'=>array(
				'title'	=>	'树形-父级字段',
				'type'	=>	'select',
				'id'	=>	'treeparentfield',
				'name'	=>	'treeparentfield',
				'group'=>'e',
				'toggle'=>'c,d',
				'data'	=>	'',
				'dataAdd'=>'comboxgetTableField',
				'parentto' => 'treedtable', //重新获取值时条件值来源对象
				'dbfield'=>'treeparentfield',
				'displayright'=>1,
				'isforeignoprate' => 1,
			),'isnextend'=>array(
				'title'	=>	'树形-是否末级操作',
				'type'	=>	'checkbox',
				'id'	=>	'isnextend',
				'name'	=>	'isnextend',
				'default'=>1,
				//'group'=>'e',
				//'toggle'=>'c,d',
				'dbfield'=>'isnextend',
				'displayright'=>1,
				'isforeignoprate' => 1,
			),'mulit'=>array(
				'title'	=>	'树形-是否多选',
				'type'	=>	'checkbox',
				'id'	=>	'mulit',
				'name'	=>	'mulit',
				'default'=>0,
				//'group'=>'e',
				//'toggle'=>'c,d',
				'data'	=>	'',
				'dbfield'=>'mulit',
				'displayright'=>1,
				'isforeignoprate' => 1,
			),'treeheight'=>array(
				'title'	=>	'树形-下拉高度',
				'type'	=>	'text',
				'id'	=>	'treeheight',
				'name'	=>	'treeheight',
				'default'=>150,
				//'group'=>'e',
				//'toggle'=>'c,d',
				'data'	=>	'',
				'dbfield'=>'treeheight',
				'displayright'=>1,
				'isforeignoprate' => 1,
			),'treewidth'=>array(
				'title'	=>	'树形-下拉宽度',
				'type'	=>	'text',
				'id'	=>	'treewidth',
				'name'	=>	'treewidth',
				'default'=>0,
				//'group'=>'e',
				//'toggle'=>'c,d',
				'data'	=>	'',
				'dbfield'=>'treewidth',
				'displayright'=>1,
				'isforeignoprate' => 1,
			),
			'isdialog'=>array(
				'title'	=>	'树形-是否对话框模式',
				'type'	=>	'checkbox',
				'id'	=>	'isdialog',
				'name'	=>	'isdialog',
				'default'=>0,
// 				'group'=>'e',
// 				'toggle'=>'c,d',
				'data'	=>	'',
				'dbfield'=>'isdialog',
				'displayright'=>1,
			),'iscontrollchange'	=>	array(//这个key不可修改，页面业务中已使用 
				'title'	=>	'组件关联',
				'type'	=>	'dialog',
				'id'	=>	'iscontrollchange',
				'name'	=>	'iscontrollchange',
				'dialogcontroll'=>'relationcontroll',
				'dialogtitle'=>'组件关联设置',
				'displayright'=>1,
				'isforeignoprate' => 1,
			),'tagevent' => array(
				'title'	=>	'事件绑定',
				'type'	=>	'select',
				'id'	=>	'tagevent',
				'name'	=>	'tagevent',
				'displayright'=>1,
				'dbfield'=>'tagevent',
				'data'	=>	'|请选择#change|change()#click|click()',
				'isforeignoprate' => 1,
				//'function'=>'test_wang', // 后
			),'titlepercent'=>array(
				'title'	=>	'标题占比',
				'type'	=>	'select',
				'id'	=>	'titlepercent',
				'name'	=>	'titlepercent',
				'displayright'	=>	1,
				'dbfield'=>'titlepercent',
				'default'=>'1',
				'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
				'function'=>'titlepercentchange',
				'isforeignoprate' => 1,
			),'contentpercent'=>array(
				'title'	=>	'内容框占比',
				'type'	=>	'select',
				'id'	=>	'contentpercent',
				'name'	=>	'contentpercent',
				'displayright'	=>	1,
				'dbfield'=>'contentpercent',
				'default'=>'3',
				'isforeignoprate' => 1,
				'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
					'function'=>'contentpercentchange',
				)
		),
	),
	// 复选框 
	'checkbox'	=>	array(
		'show'	=>	1,
		'title'	=>	'复选框',
		'iscreate'	=>	1 , //是否生成到数据库中字段
		'isview'	=>	1 , //是否生成到视图页面
		'isline' => true, // 一个标签是否占一行
		'weight'	=>	3, // 权重，用处：在字段生成时出现的默认排序位置。
		'isconfig'=>	1 , // 是否生成字段配置
		'listwidth'=>'',//列表宽度
		'html'	=>	'<div class="nbm_controll $isshow" data="checkbox" $checkorder $isline >
		<label class="label_new"><i class="$copycontrollcls"></i> $title</label>
			<div class="left tml-checkbox tml-w100">
			<label><input type="checkbox">复选按钮</label>
			</div>
			$hidden
		$delTag
		</div>
		<div class="nbmshadow select-element">
	        <div class="icon_stort_lay">$statusHtml</div>
	    </div>
		',
		'property' => array(
			'requiredfield'	=>	array( // 从检查类型中抽取出来的检查属性
					'title'	=>	'是否必填',
					'type'	=>	'checkbox',
					'default'=>'0',
					'id'	=>	'requiredfield',
					'name'	=>	'requiredfield',
					'dbfield'=>'isrequired',
			    'function'=>'changetagsortandrequired',
					'displayright'=>1,
					'isforeignoprate' => 1,
			),'checkfunc'	=>	array(
					'title'	=>	'检查类型',
					'type'	=>	'select',
					'default'=>'',
					'id'	=>	'checkfunc',
					'name'	=>	'checkfunc',
					'dbfield'=>'validatetype',
					// select option(val|text ) # op # op
					'data'	=>	'|无需验证#eamil|邮箱#url|网址#number|数字#digits|整数#double|浮点数#lettersonly|字母',
					'displayright'=>1,
					'isforeignoprate' => 1,
			),
			'showoption'	=>	array(
				'title'	=>	'默认选项数据',
				'type'	=>	'select',
				'data'	=>	'',
				'dataAdd'=>'getOptions', // 数据来源地址
				'id'	=>	'showoption',
				'name'	=>	'showoption',
				'group'=>'o',
				'toggle'=>'p',
				'displayright'=>1,
				'dbfield'=>'showoption',
					'isforeignoprate' => 1,
				
			),'subimporttableobj'	=>	array(
				'title'	=>	'绑定数据表',
				'type'	=>	'select',
				'id'	=>	'subimporttableobj',
				'name'	=>	'subimporttableobj',
				'data'	=>	'',
				'dataAdd'=>'getTables',
				'linkto' => 'subimporttablefieldobj,subimporttablefield2obj', // 关联下拉框的ID值，作用为：当该对象cnahge时关系对象的值更新。
				'group'=>'p',
				'toggle'=>'o',
				'displayright'=>1,
					'isforeignoprate' => 1,
				'dbfield'=>'subimporttableobj',
				
			),'subimporttablefieldobj'	=>	array(
				'title'	=>	'绑定显示字段',
				'type'	=>	'select',
				'id'	=>	'subimporttablefieldobj',
				'name'	=>	'subimporttablefieldobj',
				'data'	=>	'',
				'dataAdd'=>'comboxgetTableField',
				'parentto' => 'subimporttableobj', //重新获取值时条件值来源对象
				'group'=>'p',
				'toggle'=>'o',
				'displayright'=>1,
					'isforeignoprate' => 1,
				'dbfield'=>'subimporttablefieldobj',
				
			),'subimporttablefield2obj'	=>	array(
				'title'	=>	'绑定值字段',
				'type'	=>	'select',
				'id'	=>	'subimporttablefield2obj',
				'name'	=>	'subimporttablefield2obj',
				'data'	=>	'',
				'dataAdd'=>'comboxgetTableField',
				'parentto' => 'subimporttableobj',//重新获取值时条件值来源对象
				'group'=>'p',
				'toggle'=>'o',
				'displayright'=>1,
					'isforeignoprate' => 1,
				'dbfield'=>'subimporttablefield2obj',
			),'conditions'	=>	array(
				'title'	=>	'过滤条件(绑定数据表时)',
				'type'	=>	'text',
				'id'	=>	'subimporttableobjcondition',
				'name'	=>	'subimporttableobjcondition',
				'group'=>'p',
				'toggle'=>'o',
				'displayright'=>1,
					'isforeignoprate' => 1,
				'dbfield'=>'lookupconditions',
			),'islock'	=>	array(
				'title'	=>	'是否允许编辑',
				'type'	=>	'checkbox',
				'default'=>1,
				'id'	=>	'islock',
				'name'	=>	'islock',
				'displayright'=>1,
					'isforeignoprate' => 1,
				'function'=>'changetagsortandlock',
				'dbfield'=>'islock',
			),'iscontrollchange'	=>	array(//这个key不可修改，页面业务中已使用
				'title'	=>	'组件关联',
				'type'	=>	'dialog',
				'id'	=>	'iscontrollchange',
				'name'	=>	'iscontrollchange',
				'dialogcontroll'=>'relationcontroll',
				'dialogtitle'=>'组件关联设置',
					'isforeignoprate' => 1,
				'displayright'=>1,
			),'tagevent' => array(
				'title'	=>	'事件绑定',
				'type'	=>	'select',
				'id'	=>	'tagevent',
				'name'	=>	'tagevent',
				'displayright'=>1,
				'dbfield'=>'tagevent',
					'isforeignoprate' => 1,
				'data'	=>	'|请选择#change|change()#click|click()',
				//'function'=>'test_wang', // 后
			),'titlepercent'=>array(
				'title'	=>	'标题占比',
				'type'	=>	'select',
				'id'	=>	'titlepercent',
				'name'	=>	'titlepercent',
				'displayright'	=>	1,
				'dbfield'=>'titlepercent',
				'default'=>'1',
					'isforeignoprate' => 1,
				'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
					'function'=>'titlepercentchange',
			),'contentpercent'=>array(
				'title'	=>	'内容框占比',
				'type'	=>	'select',
				'id'	=>	'contentpercent',
				'name'	=>	'contentpercent',
				'displayright'	=>	1,
				'dbfield'=>'contentpercent',
				'default'=>'7',
					'isforeignoprate' => 1,
				'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
					'function'=>'contentpercentchange',
				)
		),
	),
	// 单选框 
	'radio'		=>	array(
		'show'	=>	1,
		'title'	=>	'单选框',
		'iscreate'	=>	1 , //是否生成到数据库中字段
		'isview'	=>	1 , //是否生成到视图页面
		'weight'	=>	4, // 权重，用处：在字段生成时出现的默认排序位置。
		'isline' => true, // 一个标签是否占一行
		'listwidth'=>'',//列表宽度
		'isconfig'=>	1 , // 是否生成字段配置
		'html'	=>	'<div class="nbm_controll $isshow" data="radio" $checkorder $isline > 
		<label calss="label_new"><i class="$copycontrollcls"></i>$title</label><div class="left tml-radio"><label><input type="radio" name="exampleRadio">这是一个单选按钮</label></div>
		$hidden
		$delTag
		</div>
		<div class="nbmshadow select-element">
	        <div class="icon_stort_lay">$statusHtml</div>
	    </div>
		',
		'property' => array(
			'requiredfield'	=>	array( // 从检查类型中抽取出来的检查属性
					'title'	=>	'是否必填',
					'type'	=>	'checkbox',
					'default'=>'0',
					'id'	=>	'requiredfield',
					'name'	=>	'requiredfield',
					'dbfield'=>'isrequired',
			    'function'=>'changetagsortandrequired',
					'isforeignoprate' => 1,
					'displayright'=>1,
			),'checkfunc'	=>	array(
					'title'	=>	'检查类型',
					'type'	=>	'select',
					'default'=>'',
					'id'	=>	'checkfunc',
					'name'	=>	'checkfunc',
					'dbfield'=>'validatetype',
					'isforeignoprate' => 1,
					// select option(val|text ) # op # op
					'data'	=>	'|无需验证#eamil|邮箱#url|网址#number|数字#digits|整数#double|浮点数#lettersonly|字母',
					'displayright'=>1,
			),
			'showoption'	=>	array(
				'title'	=>	'默认选项数据',
				'type'	=>	'select',
				'data'	=>	'',
				'dataAdd'=>'getOptions', // 数据来源地址
				'id'	=>	'showoption',
				'name'	=>	'showoption',
				'group'=>'d',
				'toggle'=>'c',
				'displayright'=>1,
					'isforeignoprate' => 1,
				'dbfield'=>'showoption',
				
			),'subimporttableobj'	=>	array(
				'title'	=>	'绑定数据表',
				'type'	=>	'select',
				'id'	=>	'subimporttableobj',
				'name'	=>	'subimporttableobj',
				'data'	=>	'',
				'dataAdd'=>'getTables',
				'linkto' => 'subimporttablefieldobj,subimporttablefield2obj', // 关联下拉框的ID值，作用为：当该对象cnahge时关系对象的值更新。被更新对象的值参数为当前change后的值
				'group'=>'c',
				'toggle'=>'d',
				'displayright'=>1,
					'isforeignoprate' => 1,
				'dbfield'=>'subimporttableobj',
				
			),'subimporttablefieldobj'	=>	array(
				'title'	=>	'绑定显示字段',
				'type'	=>	'select',
				'id'	=>	'subimporttablefieldobj',
				'name'	=>	'subimporttablefieldobj',
				'data'	=>	'',
				'dataAdd'=>'comboxgetTableField',
				'parentto' => 'subimporttableobj', //重新获取值时条件值来源对象
				'group'=>'c',
				'toggle'=>'d',
				'displayright'=>1,
					'isforeignoprate' => 1,
				'dbfield'=>'subimporttablefieldobj',
				
			),'subimporttablefield2obj'	=>	array(
				'title'	=>	'绑定值字段',
				'type'	=>	'select',
				'id'	=>	'subimporttablefield2obj',
				'name'	=>	'subimporttablefield2obj',
				'data'	=>	'',
				'dataAdd'=>'comboxgetTableField',
				'parentto' => 'subimporttableobj', //重新获取值时条件值来源对象
				'group'=>'c',
				'toggle'=>'d',
				'displayright'=>1,
					'isforeignoprate' => 1,
				'dbfield'=>'subimporttablefield2obj',
				
			),'conditions'	=>	array(
				'title'	=>	'过滤条件(绑定数据表时)',
				'type'	=>	'text',
				'id'	=>	'subimporttableobjcondition',
				'name'	=>	'subimporttableobjcondition',
				'group'=>'c',
				'toggle'=>'d',
				'displayright'=>1,
					'isforeignoprate' => 1,
				'dbfield'=>'subimporttableobjcondition',
			),'islock'	=>	array(
				'title'	=>	'是否允许编辑',
				'type'	=>	'checkbox',
				'default'=>1,
				'id'	=>	'islock',
				'name'	=>	'islock',
				'displayright'=>1,
					'isforeignoprate' => 1,
				'function'=>'changetagsortandlock',
				'dbfield'=>'islock',
			),'iscontrollchange'	=>	array(//这个key不可修改，页面业务中已使用
				'title'	=>	'组件关联',
				'type'	=>	'dialog',
				'id'	=>	'iscontrollchange',
				'name'	=>	'iscontrollchange',
				'dialogcontroll'=>'relationcontroll',
				'dialogtitle'=>'组件关联设置',
					'isforeignoprate' => 1,
				'displayright'=>1,
			),'tagevent' => array(
				'title'	=>	'事件绑定',
				'type'	=>	'select',
				'id'	=>	'tagevent',
				'name'	=>	'tagevent',
				'displayright'=>1,
				'dbfield'=>'tagevent',
					'isforeignoprate' => 1,
				'data'	=>	'|请选择#change|change()#click|click()',
				//'function'=>'test_wang', // 后
			),'titlepercent'=>array(
				'title'	=>	'标题占比',
				'type'	=>	'select',
				'id'	=>	'titlepercent',
				'name'	=>	'titlepercent',
				'displayright'	=>	1,
				'dbfield'=>'titlepercent',
					'isforeignoprate' => 1,
				'default'=>'1',
				'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
					'function'=>'titlepercentchange',
			),'contentpercent'=>array(
				'title'	=>	'内容框占比',
				'type'	=>	'select',
				'id'	=>	'contentpercent',
				'name'	=>	'contentpercent',
				'displayright'	=>	1,
					'isforeignoprate' => 1,
				'dbfield'=>'contentpercent',
				'default'=>'7',
				'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
					'function'=>'contentpercentchange',
				)
		),
	),
	// 文本域
	'textarea'	=>	array(
		'show'	=>	1,
		'title'	=>	'文本域',
		'iscreate'	=>	1 , //是否生成到数据库中字段
		'listwidth'=>'350',//列表宽度
		'isview'	=>	1 , //是否生成到视图页面
		'weight'	=>	5, // 权重，用处：在字段生成时出现的默认排序位置。
		'isline' => true, // 一个标签是否占一行
		'isconfig'=>	1 , // 是否生成字段配置
		'html'	=>	'<div class="nbm_controll $isshow" data="textarea" $checkorder $isline >
			$hidden
			$delTag
			<label class="label_new"><i class="$copycontrollcls"></i>$title</label><textarea class="text_area" rows="4"></textarea></div>
			<div class="nbmshadow texteara-element">
	           <div class="icon_stort_lay">$statusHtml</div>
	        </div>
		',
		'property' => array(
			'requiredfield'	=>	array( // 从检查类型中抽取出来的检查属性
					'title'	=>	'是否必填',
					'type'	=>	'checkbox',
					'default'=>'0',
					'id'	=>	'requiredfield',
					'name'	=>	'requiredfield',
					'dbfield'=>'isrequired',
			    'function'=>'changetagsortandrequired',
					'isforeignoprate' => 1,
					'displayright'=>1,
			),'org'	=>	array(
				'title'	=>	'ORG设置',
				'type'	=>	'select',
				'id'	=>	'org',
				'name'	=>	'org',
				'data'	=>	'',
				'dataAdd'=>'',
				'datafunc'=>'getLookupinfo',//获取数据的JS函数名
					'isforeignoprate' => 1,
				'displayright'=>1,
				'dbfield'=>'org',
			),'isrichbox'	=>	array( // 从检查类型中抽取出来的检查属性
					'title'	=>	'是否富文本框',
					'type'	=>	'checkbox',
					'default'=>'0',
					'id'	=>	'isrichbox',
					'name'	=>	'isrichbox',
					'dbfield'=>'isrichbox',
					'isforeignoprate' => 1,
					'displayright'=>1,
			),'checkfunc'	=>	array(
					'title'	=>	'检查类型',
					'type'	=>	'select',
					'default'=>'',
					'id'	=>	'checkfunc',
					'name'	=>	'checkfunc',
					'dbfield'=>'validatetype',
					// select option(val|text ) # op # op
					'data'	=>	'|无需验证#eamil|邮箱#url|网址#number|数字#digits|整数#double|浮点数#lettersonly|字母',
					'displayright'=>1,
					'isforeignoprate' => 1,
			),
			'defaultval'=>array(
					'title'	=>	'默认值',
					'type'	=>	'text',
					'id'	=>	'defaultval',
					'name'	=>	'defaultval',
					'data'	=>	'',
					'dataAdd'=>'',
					'parentto' => '', //重新获取值时条件值来源对象
					'dbfield'=>'defaultval',
					'displayright'=>1,
					'isforeignoprate' => 1,
			),
			'rows'	=>	array(
				'title'	=>	'行',
				'type'	=>	'text',
				'id'	=>	'rows',
				'name'	=>	'rows',
				'default'	=>	'4',
				'function'	=>	'changeTextareaRow',
				'displayright'=>1,
				'dbfield'=>'rows',
					'isforeignoprate' => 1,
			),
			'cols'	=>	array(
				'title'	=>	'列',
				'type'	=>	'text',
				'id'	=>	'cols',
				'name'	=>	'cols',
				'default'	=>	'100',
				'function'	=>	'changeTextareaCol',
				'displayright'=>1,
				'dbfield'=>'cols',
					'isforeignoprate' => 1,
			),'islock'	=>	array(
				'title'	=>	'是否允许编辑',
				'type'	=>	'checkbox',
				'default'=>1,
				'id'	=>	'islock',
				'name'	=>	'islock',
				'displayright'=>1,
				'dbfield'=>'islock',
				'function'=>'changetagsortandlock',
					'isforeignoprate' => 1,
			),'titlepercent'=>array(
				'title'	=>	'标题占比',
				'type'	=>	'select',
				'id'	=>	'titlepercent',
				'name'	=>	'titlepercent',
				'displayright'	=>	1,
				'dbfield'=>'titlepercent',
				'default'=>'1',
				'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
					'function'=>'titlepercentchange',
					'isforeignoprate' => 1,
			),'contentpercent'=>array(
				'title'	=>	'内容框占比',
				'type'	=>	'select',
				'id'	=>	'contentpercent',
				'name'	=>	'contentpercent',
				'displayright'	=>	1,
				'dbfield'=>'contentpercent',
				'default'=>'7',
					'isforeignoprate' => 1,
				'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
					'function'=>'contentpercentchange',
				)
		)
	),
	// 日期 
	'date'		=>	array(
		'show'	=>	1,
		'title'	=>	'日期组件',
		'iscreate'	=>	1 , //是否生成到数据库中字段
		'isview'	=>	1 , //是否生成到视图页面
		'listwidth'=>'',//列表宽度
		'weight'	=>	6, // 权重，用处：在字段生成时出现的默认排序位置。
		'isline' => false,
		'isconfig'=>	1 , // 是否生成字段配置
		'html'	=>	'
			<div class=" nbm_controll $isshow" data="date" $checkorder $isline >
                    <label class="label_new"><i class="$copycontrollcls"></i> $title</label>
                    <div class="tml-input-calendar">
                        <input type="text" class="input_new half_angle_input input_left">
                        <a class="icon_elm icon-calendar" href="#"></a>
                    </div>
                    $hidden
                    $delTag
                </div>
			<div class="nbmshadow short-element">
	           <div class="icon_stort_lay">$statusHtml</div>
	        </div>
		',
		'property' => array(
				'tabletype'	=>	array(
						'title'	=>	'字段数据类型',
						'type'	=>	'select',
						// 'default'=>'VARCHAR',
						'id'	=>	'tabletype',
						'name'	=>	'tabletype',
						'dbfield'=>'fieldtype',
						'data'	=>	'varchar|字符串(varchar)|30|text#int|整型(int)|10|text#text|文本型(text)|255|textarea#decimal|浮点型(decimal)|18,6|text#smallint|短整型(smallint)|8|text#tinyint|布尔型(tinyint)|1|text#date|日期型(date)|11|date',
						'default'=>':6',
						'displayright'=>1,
						'dbfield'=>'fieldtype',
						'isforeignoprate' => 1,
						'beforefunc'=>'setdefault',
						'checkvaluetovisable'=>'ids',
				),'requiredfield'	=>	array( // 从检查类型中抽取出来的检查属性
					'title'	=>	'是否必填',
					'type'	=>	'checkbox',
					'default'=>'0',
					'id'	=>	'requiredfield',
					'name'	=>	'requiredfield',
					'dbfield'=>'isrequired',
				    'function'=>'changetagsortandrequired',
						'isforeignoprate' => 1,
					'displayright'=>1,
			),'acquiretime'	=>	array( // 从检查类型中抽取出来的检查属性
					'title'	=>	'自动获取当前时间',
					'type'	=>	'checkbox',
					'default'=>'0',
					'id'	=>	'acquiretime',
					'name'	=>	'acquiretime',
					'dbfield'=>'acquiretime',
					'isforeignoprate' => 1,
					'displayright'=>1,
			),'editacquiretime'	=>	array( // 从检查类型中抽取出来的检查属性
					'title'	=>	'修改页面自动获取时间',
					'type'	=>	'checkbox',
					'default'=>'0',
					'id'	=>	'editacquiretime',
					'name'	=>	'editacquiretime',
					'isforeignoprate' => 1,
					'dbfield'=>'editacquiretime',
					'displayright'=>1,
			),
			'defaulttimechar'=>array(
					'title'	=>	'默认显示日期（先于自动获取）[数据不是日期格式自动失效]',
					'type'	=>	'text',
					'id'	=>	'defaulttimechar',
					'name'	=>	'defaulttimechar',
					'isforeignoprate' => 1,
					'data'	=>	'',
					'dbfield'=>'defaulttimechar',
					'displayright'=>1,
			),
			'isshowresoult'=>array(
				'title'	=>	'是否直接显示内容',
				'type'	=>	'checkbox',
				'id'	=>	'isshowresoult',
				'name'	=>	'isshowresoult',
					'isforeignoprate' => 1,
				'data'	=>	'',
				'dbfield'=>'isshowresoult',
				'displayright'=>0,
			),'checkfunc'	=>	array(
					'title'	=>	'检查类型',
					'type'	=>	'select',
					'default'=>'',
					'id'	=>	'checkfunc',
					'name'	=>	'checkfunc',
					'isforeignoprate' => 1,
					'dbfield'=>'validatetype',
					// select option(val|text ) # op # op
					'data'	=>	'|无需验证#eamil|邮箱#url|网址#number|数字#digits|整数#double|浮点数#lettersonly|字母',
					'displayright'=>1,
			),
			'org'	=>	array(
					'title'	=>	'ORG设置',
					'type'	=>	'select',
					'id'	=>	'org',
					'name'	=>	'org',
					'data'	=>	'',
					'isforeignoprate' => 1,
					'dataAdd'=>'',
					'datafunc'=>'getLookupinfo',//获取数据的JS函数名
					'displayright'=>1,
					'dbfield'=>'org',
			),
			'format'	=>	array(
				'title'	=>	'日期格式',
				'type'	=>	'select',
				'id'	=>	'format',
				'name'	=>	'format',
				'default'	=>	'yyyy-MM-dd@Y-m-d',
				'data'	=>	'yyyy-MM-dd@Y-m-d|年-月-日#yyyy-MM-dd HH:mm@Y-m-d H:i|年-月-日 时:分#HH:mm@H:i|时:分#yyyy-MM@Y-m|年-月#MM-dd@m-d|月-日#yyyy@Y|年#MM@m|月#dd@d|日',
				'displayright'=>1,
			    'function'=>'explainDateFormat',
				'isforeignoprate' => 1,
				'dbfield'=>'dateformat',
				
			),
		    'formatphp'	=>	array(
		        'title'	=>	'日期格式-php',
		        'type'	=>	'text',
		        'id'	=>	'formatphp',
		        'name'	=>	'formatphp',
		        'default'	=>	'Y-m-d',
		        'displayright'=>0,
		        'isforeignoprate' => 1,
		        'dbfield'=>'formatphp',
		    
		    ),
		    'formatjs'	=>	array(
		        'title'	=>	'日期格式-js',
		        'type'	=>	'text',
		        'id'	=>	'formatjs',
		        'name'	=>	'formatjs',
		        'default'	=>	'yyyy-MM-dd',
		        'displayright'=>0,
		        'isforeignoprate' => 1,
		        'dbfield'=>'formatjs',
		    
		    )
		    ,'islock'	=>	array(
				'title'	=>	'是否允许编辑',
				'type'	=>	'checkbox',
				'default'=>1,
				'id'	=>	'islock',
				'name'	=>	'islock',
				'displayright'=>1,
				'dbfield'=>'islock',
					'isforeignoprate' => 1,
				'function'=>'changetagsortandlock',
			),'calculate'	=>	array(//这个key不可修改，页面业务中已使用
				'title'	=>	'组件计算设置',
				'type'	=>	'dialog',
				'id'	=>	'calculate',
				'name'	=>	'calculate',
				'dbfield'=>'calculate',
					'isforeignoprate' => 1,
				'dialogcontroll'=>'calculatedate',
				'dialogtitle'=>'组件计算设置',
				'displayright'=>1,
			),'titlepercent'=>array(
				'title'	=>	'标题占比',
				'type'	=>	'select',
				'id'	=>	'titlepercent',
				'name'	=>	'titlepercent',
					'isforeignoprate' => 1,
				'displayright'	=>	1,
				'dbfield'=>'titlepercent',
				'default'=>'1',
				'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
					'function'=>'titlepercentchange',
			),'contentpercent'=>array(
				'title'	=>	'内容框占比',
				'type'	=>	'select',
				'id'	=>	'contentpercent',
				'name'	=>	'contentpercent',
				'displayright'	=>	1,
					'isforeignoprate' => 1,
				'dbfield'=>'contentpercent',
				'default'=>'3',
				'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
					'function'=>'contentpercentchange',
				),
				
		),
	),
	// 列表选择框
	/*'checkfor'	=>	array(
		'show'	=>	0,
		'title'	=>	'checkfor组件',
		'iscreate'	=>	1 , //是否生成到数据库中字段
		'isline' => true, // 一个标签是否占一行
		'html'	=>	'',
		'property' => array(
		),
	),*/
	// 查找带回 
	'lookup'	=>	array(
		'show'	=>	1,
		'title'	=>	'lookup组件',
		'iscreate'	=>	1 , //是否生成到数据库中字段
		'isview'	=>	1 , //是否生成到视图页面
		'weight'	=>	7, // 权重，用处：在字段生成时出现的默认排序位置。
		'listwidth'=>'',//列表宽度
		'isline' => false, // 一个标签是否占一行
		'isconfig'=>	1 , // 是否生成字段配置
		'html'	=>	'
		<div class="nbm_controll" data="lookup" $checkorder $isline >
			<label class="label_new"><i class="$copycontrollcls"></i> $title </label>
			<div class="tml-input-lookup">
			<input type="text" value="" class="input_new half_angle_input" />
			<a class="icon_elm mid_icon_elm icon-plus" href="javascript:void(0);"></a>
			<a title="清空信息" class="icon_elm icon-trash"  href="javascript:;"></a>
			</div>
			$hidden
            $delTag
		</div>
		<div class="nbmshadow short-element">
	        <div class="icon_stort_lay">$statusHtml</div>
	    </div>
		',
		'property' => array(
			'requiredfield'	=>	array( // 从检查类型中抽取出来的检查属性
					'title'	=>	'是否必填',
					'type'	=>	'checkbox',
					'default'=>'0',
					'id'	=>	'requiredfield',
					'name'	=>	'requiredfield',
					'dbfield'=>'isrequired',
			    'function'=>'changetagsortandrequired',
					'isforeignoprate' => 1,
					'displayright'=>1,
			),'checkfunc'	=>	array(
					'title'	=>	'检查类型',
					'type'	=>	'select',
					'default'=>'',
					'id'	=>	'checkfunc',
					'name'	=>	'checkfunc',
					'dbfield'=>'validatetype',
					// select option(val|text ) # op # op
					'data'	=>	'|无需验证#eamil|邮箱#url|网址#number|数字#digits|整数#double|浮点数#lettersonly|字母',
					'displayright'=>1,
					'isforeignoprate' => 1,
			),
				'iscontrollchange'	=>	array(//这个key不可修改，页面业务中已使用
						'title'	=>	'组件关联',
						'type'	=>	'dialog',
						'id'	=>	'iscontrollchange',
						'name'	=>	'iscontrollchange',
						'dialogcontroll'=>'relationcontroll',
						'dialogtitle'=>'组件关联设置',
						'dbfield'=>'iscontrollchange',
						'displayright'=>1,
						'isforeignoprate' => 1,
				),
				'iscreatesearch'=>array( //这个key不可修改，页面业务中已使用
						'title'=>'是否生成搜索',
						'type'=>'text',
						'id'=>'iscreatesearch',
						'name'=>'iscreatesearch',
						'dbfield'=>'iscreatesearch',
						'displayright'=>1,
						'isforeignoprate' => 1,
				),
			'filedback'	=>	array(//这个key不可修改，页面业务中已使用
				'title'	=>	'查找带回字段',
				'type'	=>	'text',
				'id'	=>	'filedback',
				'name'	=>	'filedback',
				'displayright'=>1,
				'dbfield'=>'lookupfiledback',
					'isforeignoprate' => 1,
			),
			'filedbackchina'	=>	array(//这个key不可修改，页面业务中已使用
				'title'	=>	'查找带回字段中文',
				'type'	=>	'text',
				'id'	=>	'filedbackchina',
				'name'	=>	'filedbackchina',
				'displayright'=>1,
				'dbfield'=>'lookupfiledbackchina',
					'isforeignoprate' => 1,
			),
			'lookupgroup'=>array( //这个key不可修改，页面业务中已使用
				'title'=>'lookup唯一标识',
				'type'=>'text',
				'id'=>'lookupgroup',
				'name'=>'lookupgroup',
				'default'=>'ORG#ids#',
				'beforefunc'=>'createORGName', // JS 生成ORG的唯一名称
				'displayright'=>1,
				'dbfield'=>'lookupgrouporg',
					'isforeignoprate' => 1,
			),
			'geturls'=>array(
				'title'=>'数据请求地址',
				'type'=>'text',
				'id'=>'geturls',
				'name'=>'geturls',
				'default'=>'lookupGeneral',
				'displayright'=>1,
				'dbfield'=>'lookupurls',
					'isforeignoprate' => 1,
			),
			'model'=>array(
				'title'=>'Model',
				'type'=>'text',
				'id'=>'model',
				'name'=>'model',
				'displayright'=>1,
				'dbfield'=>'lookupmodel',
					'isforeignoprate' => 1,
			),'lookuporg'=>array(
				'title'=>'指定当前org显示字段',
				'type'=>'text',
				'id'=>'lookuporg',
				'name'=>'lookuporg',
				'displayright'=>0,
				'dbfield'=>'lookupshoworg',
					'isforeignoprate' => 1,
			),'lookuporgval'=>array(
				'title'=>'指定当前org值字段',
				'type'=>'text',
				'id'=>'lookuporgval',
				'name'=>'lookuporgval',
				'displayright'=>1,
				'dbfield'=>'lookuporgval',
					'isforeignoprate' => 1,
			),
			'lookupconditions'=>array(
				'title'=>'过滤条件',
				'type'=>'text',
				'id'=>'conditions',
				'name'=>'conditions',
				'displayright'=>1,
					'isforeignoprate' => 1,
				'dbfield'=>'lookupconditions',
			),'org'	=>	array(
					'title'	=>	'外部ORG Value',
					'type'	=>	'select',
					'id'	=>	'org',
					'name'	=>	'org',
					'data'	=>	'',
					'dataAdd'=>'',
					'datafunc'=>'getLookupinfo',//获取数据的JS函数名
					'displayright'=>1,
					'isforeignoprate' => 1,
					'dbfield'=>'org',
			),
			'defaultval'=>array(
					'title'	=>	'默认值',
					'type'	=>	'select',
					'id'	=>	'defaultval',
					'name'	=>	'defaultval',
					'data'	=>	'',
					'dataAdd'=>'',
					'parentto' => '', //重新获取值时条件值来源对象
					'dbfield'=>'defaultval',
					'displayright'=>1,
					'isforeignoprate' => 1,
					'data'	=>	'|请选择#C(\'USER_AUTH_KEY\')|用户id#user_shangji_id|用户上级id#user_anquandengji_id|用户安全等级#user_txsj|调休时间#companyid|公司id#user_job_id|岗位id#email|用户email#user_employeid|员工id#user_dep_id|当前部门id#user_duty_id|用户职级id#loginUserName|用户中文名称',
			),

				'org1'	=>	array(
					'title'	=>	'外部ORG Text',
					'type'	=>	'select',
					'id'	=>	'org1',
					'name'	=>	'org1',
					'data'	=>	'',
					'dataAdd'=>'',
					'isforeignoprate' => 1,
					'datafunc'=>'getLookupinfo',//获取数据的JS函数名
					'displayright'=>1,
					'dbfield'=>'org1',
			),'islock'	=>	array(
				'title'	=>	'是否允许编辑',
				'type'	=>	'checkbox',
				'default'=>1,
				'id'	=>	'islock',
				'name'	=>	'islock',
				'displayright'=>1,
				'dbfield'=>'islock',
				'isforeignoprate' => 1,
				'function'=>'changetagsortandlock',
			),'viewname'=>array(
				'title'=>'视图名称',
				'type'=>'text',
				'id'=>'viewname',
				'name'=>'viewname',
				'displayright'=>1,
				'isforeignoprate' => 1,
				'dbfield'=>'viewname',
			),'viewtype'=>array(
				'title'=>'视图类型',
				'type'=>'text',
				'id'=>'viewtype',
				'name'=>'viewtype',
				'displayright'=>1,
				'isforeignoprate' => 1,
				'dbfield'=>'viewtype',
			),
			'lookupchoice'=>array(	// 前置lookup属性选择
				'title'	=>	'lookup属性选择',
				'type'	=>	'select',
				'id'	=>	'lookupchoice',
				'name'	=>	'lookupchoice',
				'beforefunc'=>'setDefaultChoices',
				'displayright'=>1,
				'isforeignoprate' => 1,
				'dataAdd'=>'lookuoconfig',
				'afterfunc'=>'lookupchoice', // 用户自定义后置函数，在右侧属性查看、当前标签默认事件触发时 被调用
				'rel'=>'filedback:fields;geturls:url;model:mode;models:mode;relation:val;methods:view;lookuporg:filed;lookuporgval:val;conditions:condition;viewname:viewname;viewtype:viewtype;filedbackchina:fieldbackchina;dialogwidth:dialogwidth;dialogheight:dialogheight',	//当前lookup属性key与前置属性key对应关系
				'dbfield'=>'lookupchoice',
			),'iscontrollchange'	=>	array(//这个key不可修改，页面业务中已使用
				'title'	=>	'组件关联',
				'type'	=>	'dialog',
				'id'	=>	'iscontrollchange',
				'name'	=>	'iscontrollchange',
				'dialogcontroll'=>'relationcontroll',
				'dialogtitle'=>'组件关联设置',
				'isforeignoprate' => 1,
				'displayright'=>1,
			),
				'additionalconditions'	=>	array(//这个key不可修改，页面业务中已使用
				'title'	=>	'附加条件',
				'type'	=>	'dialog',
				'id'	=>	'additionalconditions',
				'name'	=>	'additionalconditions',
				'dbfield'=>'additionalconditions',
				'dialogcontroll'=>'additionalconditions',// 对话框请求的php函数名
				'dialogtitle'=>'附加条件设置',
				'isforeignoprate' => 1,
				'allowcontroll'	=>	'select,text,lookup,hiddens',				//		允许加入到looup附加条件的组件。
				'notallowfiled'	=>	'#self#,id,orderno',								//		不允许使用的字段。如果是本字段，就书写为 #self# 其它字段为原字段名，多字段以逗号分隔。
				'displayright'=>1,
			),'ismuchchoice'	=>	array(
				'title'	=>	'是否多选',
				'type'	=>	'checkbox',
				'default'=>1,
				'id'	=>	'ismuchchoice',
				'name'	=>	'ismuchchoice',
				'displayright'=>1,
				'dbfield'=>'ismuchchoice',
				'isforeignoprate' => 1,
			),
				'dialogwidth'	=>	array(
				'title'	=>	'弹出窗宽度',
				'type'	=>	'text',
				'id'	=>	'dialogwidth',
				'name'	=>	'dialogwidth',
				'dbfield'=>'dialogwidth',
				'isforeignoprate' => 1,
				//'dialogcontroll'=>'dialogwidth',
				//'dialogtitle'=>'关联条件设置',
				'displayright'=>1,
			),
				'dialogheight'	=>	array(
				'title'	=>	'弹出窗高度',
				'type'	=>	'text',
				'id'	=>	'dialogheight',
				'name'	=>	'dialogheight',
				'dbfield'=>'dialogheight',
				'isforeignoprate' => 1,
				//'dialogcontroll'=>'dialogheight',
				//'dialogtitle'=>'关联条件设置',
				'displayright'=>1,
			),
			'titlepercent'=>array(
				'title'	=>	'标题占比',
				'type'	=>	'select',
				'id'	=>	'titlepercent',
				'name'	=>	'titlepercent',
				'displayright'	=>	1,
				'dbfield'=>'titlepercent',
				'default'=>'1',
				'isforeignoprate' => 1,
				'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
					'function'=>'titlepercentchange',
			),'contentpercent'=>array(
				'title'	=>	'内容框占比',
				'type'	=>	'select',
				'id'	=>	'contentpercent',
				'name'	=>	'contentpercent',
				'displayright'	=>	1,
				'isforeignoprate' => 1,
				'dbfield'=>'contentpercent',
				'default'=>'3',
				'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
					'function'=>'contentpercentchange',
				),
				'callback'	=>	array(//这个key不可修改，页面业务中已使用
				'title'	=>	'回调函数',
				'type'	=>	'text',
				'id'	=>	'callback',
				'name'	=>	'callback',
						'isforeignoprate' => 1,
				'displayright'=>1,
				'dbfield'=>'callback',
			),
		),
	),
	// 上传 
	'upload'	=>	array(
		'show'	=>	1,
		'title'	=>	'上传组件',
		'iscreate'	=>	1 , //是否生成到数据库中字段
		'isview'	=>	1 , //是否生成到视图页面
		'isline' => true, // 一个标签是否占一行
		'listwidth'=>'',//列表宽度
		'isconfig'=>	1 , // 是否生成字段配置
		'html'	=>	'
            <div class="nbm_controll $isshow" data="upload" $checkorder $isline >
			<label class="label_new"><i class="$copycontrollcls"></i>$title</label>
			<div id="nbmxkjuploads" class="uploadify" style="height: 25px; width: 104px;">
			<div id="nbmxkjuploads-button" class="uploadify-button " style="background-image: url(&quot;/system/Public/uploadify/upload.png&quot;); text-indent: -9999px; height: 25px; line-height: 25px; width: 104px;">
			<span class="uploadify-button-text">选择上传文件</span></div></div>
			$delTag
             $hidden
			</div>
			<div class="nbmshadow upload-element">
	           <div class="icon_stort_lay">$statusHtml</div>
	        </div>
		','property' => array(
			'uploadnum'	=>	array( // 从检查类型中抽取出来的检查属性
					'title'	=>	'上传个数',
					'type'	=>	'text',
					'default'=>'',
					'id'	=>	'uploadnum',
					'name'	=>	'uploadnum',
					'dbfield'=>'uploadnum',
					'displayright'=>1,
			),'uploadtype'	=>	array( // 从检查类型中抽取出来的检查属性
					'title'	=>	'上传限制类型',
					'type'	=>	'text',
					'id'	=>	'uploadtype',
					'name'	=>	'uploadtype',
					'dbfield'=>'uploadtype',
					'displayright'=>1,
			),'requiredfield'	=>	array( // 从检查类型中抽取出来的检查属性
					'title'	=>	'是否必填',
					'type'	=>	'checkbox',
					'default'=>'0',
					'id'	=>	'requiredfield',
					'name'	=>	'requiredfield',
					'dbfield'=>'isrequired',
			        'function'=>'changetagsortandrequired',
					'displayright'=>1,
			),'checkfunc'	=>	array(
					'title'	=>	'检查类型',
					'type'	=>	'select',
					'default'=>'',
					'id'	=>	'checkfunc',
					'name'	=>	'checkfunc',
					'dbfield'=>'validatetype',
					// select option(val|text ) # op # op
					'data'	=>	'|无需验证#eamil|邮箱#url|网址#number|数字#digits|整数#double|浮点数#lettersonly|字母',
					'displayright'=>1,
			),'islock'	=>	array(
				'title'	=>	'是否允许编辑',
				'type'	=>	'checkbox',
				'default'=>1,
				'id'	=>	'islock',
				'name'	=>	'islock',
				'displayright'=>1,
				'dbfield'=>'islock',
				'function'=>'changetagsortandlock',
			),'widthheight'	=>	array( // 从检查类型中抽取出来的检查属性
					'title'	=>	'图片宽高',
					'type'	=>	'text',
					'default'=>'',
					'id'	=>	'widthheight',
					'name'	=>	'widthheight',
					'dbfield'=>'widthheight',
					'displayright'=>1,
			),'titlepercent'=>array(
				'title'	=>	'标题占比',
				'type'	=>	'select',
				'id'	=>	'titlepercent',
				'name'	=>	'titlepercent',
				'displayright'	=>	1,
				'dbfield'=>'titlepercent',
				'default'=>'1',
				'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
					'function'=>'titlepercentchange',
			),'contentpercent'=>array(
				'title'	=>	'内容框占比',
				'type'	=>	'select',
				'id'	=>	'contentpercent',
				'name'	=>	'contentpercent',
				'displayright'	=>	1,
				'dbfield'=>'contentpercent',
				'default'=>'7',
				'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
					'function'=>'contentpercentchange',
				)
		)
	),
    // 图片上传组件
    'uploadpic'	=>	array(
        'show'	=>	1,
        'title'	=>	'图片上传组件',
        'iscreate'	=>	1 , //是否生成到数据库中字段
        'isview'	=>	1 , //是否生成到视图页面
        'isline' => true, // 一个标签是否占一行
        'listwidth'=>'',//列表宽度
        'isconfig'=>	1 , // 是否生成字段配置
        'html'	=>	'
            <div class="nbm_controll $isshow" data="uploadpic" $checkorder $isline >
			<label class="label_new"><i class="$copycontrollcls"></i>$title</label>
			<div id="nbmxkjuploads" class="uploadify" style="height: 25px; width: 104px;">
			<div id="nbmxkjuploads-button" class="uploadify-button " style="background-image: url(&quot;/system/Public/uploadify/upload.png&quot;); text-indent: -9999px; height: 25px; line-height: 25px; width: 104px;">
			<span class="uploadify-button-text">选择上传文件</span></div></div>
			$delTag
             $hidden
			</div>
			<div class="nbmshadow upload-element">
	           <div class="icon_stort_lay">$statusHtml</div>
	        </div>
		','property' => array(
    		    'uploadnum'	=>	array( // 从检查类型中抽取出来的检查属性
    		        'title'	=>	'上传个数',
    		        'type'	=>	'text',
    		        'default'=>'',
    		        'id'	=>	'uploadnum',
    		        'name'	=>	'uploadnum',
    		        'dbfield'=>'uploadnum',
    		        'displayright'=>1,
    		    ),'uploadtype'	=>	array( // 从检查类型中抽取出来的检查属性
    		        'title'	=>	'上传限制类型',
    		        'type'	=>	'text',
    		        'id'	=>	'uploadtype',
    		        'name'	=>	'uploadtype',
    		        'dbfield'=>'uploadtype',
    		        'default'=>'*.jpg;*.jpeg;*.gif;*.png;',
    		        'displayright'=>0,
    		    )
    		    ,'cropstart'	=>	array( // 图片剪裁
    		        'title'	=>	'装饰线开始',
    		        'type'	=>	'hr',
    		        'displayright'=>1,
    		    )
    		    ,'cropratiostart'	=>	array( // 图片剪裁
    		        'title'	=>	'等比操作',
    		        'type'	=>	'fieldsetstart',
    		        'displayright'=>1,
    		    ),'widthheight'	=>	array( // 从检查类型中抽取出来的检查属性
					'title'	=>	'图片宽高',
					'type'	=>	'text',
					'default'=>'',
					'id'	=>	'widthheight',
					'name'	=>	'widthheight',
					'dbfield'=>'widthheight',
					'displayright'=>1,
			) ,'cropratiomin'	=>	array(
    		        'title'	=>	'勾取最小值',
    		        'type'	=>	'text',
    		        'id'	=>	'cropratiomin',
    		        'name'	=>	'cropratiomin',
    		        'dbfield'=>'cropratiomin',
    		        'default'=>'10,10',
    		        'displayright'=>1,
    		    ),'cropratiomax'	=>	array(
    		        'title'	=>	'勾取最大值',
    		        'type'	=>	'text',
    		        'id'	=>	'cropratiomax',
    		        'name'	=>	'cropratiomax',
    		        'dbfield'=>'cropratiomax',
    		        'default'=>'400,400',
    		        'displayright'=>1,
    		    )
    		    ,'cropratioend'	=>	array( // 图片剪裁
    		        'title'	=>	'等比操作结束',
    		        'type'	=>	'fieldsetend',
    		        'displayright'=>1,
    		    )
    		    ,'cropend'	=>	array( // 图片剪裁
    		        'title'	=>	'装饰线开始结束',
    		        'type'	=>	'hr',
    		        'displayright'=>1,
    		    ),'cropdefaultcheck'	=>	array(
    		        'title'	=>	'默认选中区域',
    		        'type'	=>	'text',
    		        'id'	=>	'cropdefaultcheck',
    		        'name'	=>	'cropdefaultcheck',
    		        'dbfield'=>'cropdefaultcheck',
    		        'default'=>'10,10,400,400',
    		        'displayright'=>1,
    		    ),'cropfreed'	=>	array(
    		        'title'	=>	'自由选取',
    		        'type'	=>	'checkbox',
    		        'id'	=>	'cropfreed',
    		        'name'	=>	'cropfreed',
    		        'dbfield'=>'cropfreed',
    		        'displayright'=>1,
    		        'desc'=>'自由选择功能与最值范围允许关联操作。'
    		    ),'cropratio'	=>	array(
    		        'title'	=>	'等比操作',
    		        'type'	=>	'checkbox',
    		        'id'	=>	'cropratio',
    		        'name'	=>	'cropratio',
    		        'dbfield'=>'cropratio',
    		        'desc'=>'等比操作的区域为正方形，不可改变比例，可与最值范围关联操作',
    		        'displayright'=>1,
    		    )
		        ,'requiredfield'	=>	array( // 从检查类型中抽取出来的检查属性
    		        'title'	=>	'是否必填',
    		        'type'	=>	'checkbox',
    		        'default'=>'0',
    		        'id'	=>	'requiredfield',
    		        'name'	=>	'requiredfield',
    		        'dbfield'=>'isrequired',
    		        'function'=>'changetagsortandrequired',
    		        'displayright'=>1,
    		    ),'checkfunc'	=>	array(
    		        'title'	=>	'检查类型',
    		        'type'	=>	'select',
    		        'default'=>'',
    		        'id'	=>	'checkfunc',
    		        'name'	=>	'checkfunc',
    		        'dbfield'=>'validatetype',
    		        // select option(val|text ) # op # op
    		        'data'	=>	'|无需验证#eamil|邮箱#url|网址#number|数字#digits|整数#double|浮点数#lettersonly|字母',
    		        'displayright'=>0,
    		    ),'islock'	=>	array(
    		        'title'	=>	'是否允许编辑',
    		        'type'	=>	'checkbox',
    		        'default'=>1,
    		        'id'	=>	'islock',
    		        'name'	=>	'islock',
    		        'displayright'=>1,
    		        'dbfield'=>'islock',
    		        'function'=>'changetagsortandlock',
    		    ),'titlepercent'=>array(
    		        'title'	=>	'标题占比',
    		        'type'	=>	'select',
    		        'id'	=>	'titlepercent',
    		        'name'	=>	'titlepercent',
    		        'displayright'	=>	1,
    		        'dbfield'=>'titlepercent',
    		        'default'=>'1',
    		        'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
    		        'function'=>'titlepercentchange',
    		    ),'contentpercent'=>array(
    		        'title'	=>	'内容框占比',
    		        'type'	=>	'select',
    		        'id'	=>	'contentpercent',
    		        'name'	=>	'contentpercent',
    		        'displayright'	=>	1,
    		        'dbfield'=>'contentpercent',
    		        'default'=>'7',
    		        'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
    		        'function'=>'contentpercentchange',
    		    )
    		)
    ),
/*	// 长文本框 
	'longtext'	=>	'',
	//数据表格
	'datatable'	=>	'',

	// html 分隔符
	'hr'		=>	'',*/
	//作用域
	'fieldset'	=>	array(
		'show'	=>	1,
		'title'	=>	'作用域',
		'iscreate'	=>	0 , //是否生成到数据库中字段
		'isview'	=>	0 , //是否生成到视图页面
		'listwidth'=>'',//列表宽度
		'isline' => true, // 一个标签是否占一行
		'isconfig'=>	0 , // 是否生成字段配置
		'html'	=>	'
			<div class="$isshow nbm_controll " data="fieldset" $checkorder $isline >
			<fieldset class="side-catalog-anchor">
				<legend class="fieldset_legend_toggle side-catalog-text side-catalog-firstanchor">	
					<b><i class="$copycontrollcls"></i>$title</b>
					<div class="tml_style_line tml_sl4 tml_slb_blue"></div>
				</legend>		
			</fieldset>
			$delTag
             $hidden
		</div>
		<div class="nbmshadow">
	        <div class="icon_stort_lay">$statusHtml</div>
	    </div>',
			'view'=>'
			#endfieldset#
			<div class="fieldset_show_box #class#" original="#original#" category="#category#" style="#style#">
				<legend class="fieldset_legend_toggle side-catalog-text side-catalog-firstanchor">
					<a name="#fields#"></a>
					<b>#title#</b>
					<div class="tml_style_line tml_sl4 tml_slb_blue"></div>
				</legend>
			</div>
			<div class="fieldsetjs_show_box #class#">'
			,
			'property' => array(
				'requiredfield'	=>	array( // 从检查类型中抽取出来的检查属性
						'title'	=>	'是否必填',
						'type'	=>	'checkbox',
						'default'=>'0',
						'id'	=>	'requiredfield',
						'name'	=>	'requiredfield',
						'dbfield'=>'isrequired',
				        'function'=>'changetagsortandrequired',
						'displayright'=>1,
				),'checkfunc'	=>	array(
						'title'	=>	'检查类型',
						'type'	=>	'select',
						'default'=>'',
						'id'	=>	'checkfunc',
						'name'	=>	'checkfunc',
						'dbfield'=>'validatetype',
						// select option(val|text ) # op # op
						'data'	=>	'|无需验证#eamil|邮箱#url|网址#number|数字#digits|整数#double|浮点数#lettersonly|字母',
						'displayright'=>1,
				),
				'titlepercent'=>array(
					'title'	=>	'标题占比',
					'type'	=>	'select',
					'id'	=>	'titlepercent',
					'name'	=>	'titlepercent',
					'displayright'	=>	1,
					'dbfield'=>'titlepercent',
					'default'=>'8',
					'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
					'function'=>'titlepercentchange',
				),'contentpercent'=>array(
					'title'	=>	'内容框占比',
					'type'	=>	'select',
					'id'	=>	'contentpercent',
					'name'	=>	'contentpercent',
					'displayright'	=>	1,
					'dbfield'=>'contentpercent',
					'default'=>'0',
					'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
					'function'=>'contentpercentchange',
				),'islock'	=>	array(
				'title'	=>	'是否允许编辑',
				'type'	=>	'checkbox',
				'default'=>1,
				'id'	=>	'islock',
				'name'	=>	'islock',
				'displayright'=>1,
				'dbfield'=>'islock',
				'function'=>'changetagsortandlock',
			)
						
			),
		
	),
	'userselect'	=>	array(
		'show'	=>	0,
		'title'	=>	'人员选择器',
		'iscreate'	=>	1 , //是否生成到数据库中字段
		'isview'	=>	0 , //是否生成到视图页面
		'listwidth'=>'',//列表宽度
		'isline' => true, // 一个标签是否占一行
		'isconfig'=>	1 , // 是否生成字段配置
		'html'	=>	'
			<div class="tml-form-row $isshow nbm_controll" data="userselect" $checkorder $isline>
				<label class="label_new"><i class="$copycontrollcls"></i>$title</label>
					  	<input type="text" class=" select_per addresseeTextInput textInput enterIndex" style="float: left;">
	               <!-- checkFor带回来的值 -->
				<a height="800" width="800"  href="javascript:;" class="input-addon input-addon-addon input-addon-userplus">查找带回</a>&nbsp;
				<a title="清空接收用户" href="javascript:;" class="input-addon input-addon-recycle"></a>
				$delTag
				$hidden
			</div>
			<div class="nbmshadow select_per_element">
	           <div class="icon_stort_lay">$statusHtml</div>
	        </div>
		','property' => array(
				'requiredfield'	=>	array( // 从检查类型中抽取出来的检查属性
						'title'	=>	'是否必填',
						'type'	=>	'checkbox',
						'default'=>'0',
						'id'	=>	'requiredfield',
						'name'	=>	'requiredfield',
						'dbfield'=>'isrequired',
				    'function'=>'changetagsortandrequired',
						'displayright'=>1,
				),'checkfunc'	=>	array(
						'title'	=>	'检查类型',
						'type'	=>	'select',
						'default'=>'',
						'id'	=>	'checkfunc',
						'name'	=>	'checkfunc',
						'dbfield'=>'validatetype',
						// select option(val|text ) # op # op
						'data'	=>	'|无需验证#eamil|邮箱#url|网址#number|数字#digits|整数#double|浮点数#lettersonly|字母',
						'displayright'=>1,
				),
			'islock'	=>	array(
				'title'	=>	'是否允许编辑',
				'type'	=>	'checkbox',
				'default'=>1,
				'id'	=>	'islock',
				'name'	=>	'islock',
				'displayright'=>1,
				'dbfield'=>'islock',
				'function'=>'changetagsortandlock',
			),
			'ismult'	=>	array(
				'title'	=>	'是否多选',
				'type'	=>	'checkbox',
				'default'=>1,
				'id'	=>	'ismult',
				'name'	=>	'ismult',
				'displayright'=>1,
				'dbfield'=>'ismult',
			),'titlepercent'=>array(
				'title'	=>	'标题占比',
				'type'	=>	'select',
				'id'	=>	'titlepercent',
				'name'	=>	'titlepercent',
				'displayright'	=>	1,
				'dbfield'=>'titlepercent',
				'default'=>'1',
				'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
					'function'=>'titlepercentchange',
			),'contentpercent'=>array(
				'title'	=>	'内容框占比',
				'type'	=>	'select',
				'id'	=>	'contentpercent',
				'name'	=>	'contentpercent',
				'displayright'	=>	1,
				'dbfield'=>'contentpercent',
				'default'=>'7',
				'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
					'function'=>'contentpercentchange',
				)
		)
	),'areainfo'	=>	array(
		'show'	=>	1,
		'title'	=>	'地区信息',
		'isview'	=>	1 , //是否生成到视图页面
		'iscreate'	=>	1 , //是否生成到数据库中字段
		'isline' => true, // 一个标签是否占一行
		'listwidth'=>'',//列表宽度
		'isconfig'=>	1 , // 是否生成字段配置
		'html'	=>	'
		<div class="$isshow nbm_controll" data="areainfo" $checkorder $isline >
			<label class="label_new "><i class="$copycontrollcls"></i> 地址组件：</label>
			<div class="address_elm left">
				<select class="select2 address_level_elm left">
					<option value="">请选择省</option>
				</select>
				<select class="select2 address_level_elm left">
					<option value="">请选择市</option>
				</select>
				<select class="select2 address_level_elm left">
					<option value="">请选择区县</option>
				</select>
				<input class="address_four_level input_new left" placeholder="请输入详情地址" type="text"/>
				<a class="icon-map-marker address_icon_link left" href="#"></a>
				<input class="split_address input_new left" placeholder="地址显示：省、市、区县、详细地址" type="text" disabled />
			</div>
			$delTag $hidden 
		</div>
		<div class="nbmshadow adds_element">
	        <div class="icon_stort_lay">$statusHtml</div>
	    </div>
		','property' => array(
			'datashare'	=>	array(
					'title'	=>	'复用时数据共用',
					'type'	=>	'checkbox',
					'default'=>'0',
					'id'	=>	'datashare',
					'name'	=>	'datashare',
					'dbfield'=>'datashare',
					'displayright'=>1,
			),
			'length'	=>	array(
				'title'	=>	'字段长度',
				'type'	=>	'text',
				'default'=>'200',
				'id'	=>	'length',
				'name'	=>	'length',
				'dbfield'=>'tablelength',
				'displayright'=>1,
				'checkvaluetovisable'=>'ids',
				//'dbfield'=>'dblength',
			),
			'requiredfield'	=>	array( // 从检查类型中抽取出来的检查属性
					'title'	=>	'是否必填',
					'type'	=>	'checkbox',
					'default'=>'0',
					'id'	=>	'requiredfield',
					'name'	=>	'requiredfield',
					'dbfield'=>'isrequired',
			    'function'=>'changetagsortandrequired',
					'displayright'=>1,
			),'checkfunc'	=>	array(
					'title'	=>	'检查类型',
					'type'	=>	'select',
					'default'=>'',
					'id'	=>	'checkfunc',
					'name'	=>	'checkfunc',
					'dbfield'=>'validatetype',
					// select option(val|text ) # op # op
					'data'	=>	'|无需验证#eamil|邮箱#url|网址#number|数字#digits|整数#double|浮点数#lettersonly|字母',
					'displayright'=>1,
			),
			'islock'	=>	array(
				'title'	=>	'是否允许编辑',
				'type'	=>	'checkbox',
				'default'=>1,
				'id'	=>	'islock',
				'name'	=>	'islock',
				'displayright'=>1,
				'dbfield'=>'islock',
				'function'=>'changetagsortandlock',
			),'titlepercent'=>array(
				'title'	=>	'标题占比',
				'type'	=>	'select',
				'id'	=>	'titlepercent',
				'name'	=>	'titlepercent',
				'displayright'	=>	1,
				'dbfield'=>'titlepercent',
				'default'=>'1',
				'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
					'function'=>'titlepercentchange',
			),'contentpercent'=>array(
				'title'	=>	'内容框占比',
				'type'	=>	'select',
				'id'	=>	'contentpercent',
				'name'	=>	'contentpercent',
				'displayright'	=>	1,
				'dbfield'=>'contentpercent',
				'default'=>'7',
				'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
					'function'=>'contentpercentchange',
				)
		)
	),
	// 换行符
	'divider2'	=>	array(
		'show'	=>	1,
		'title'	=>	'换行符',
		'iscreate'	=>	0 , //是否生成到数据库中字段
		'isview'	=>	0 , //是否生成到视图页面
		'isline' => true, // 一个标签是否占一行
		'isconfig'=>	0 , // 是否生成字段配置
		'html'	=>	'
			<div class="tml_line_break nbm_controll $isshow" data="divider2" $checkorder $isline >
			<i class="$copycontrollcls"></i>换行符，PS：不在页面显示
			$delTag
			$hidden
			</div>
			<div class="nbmshadow">
	           <div class="icon_stort_lay">$statusHtml</div>
	        </div>
		',
		'view'=>' '
		,'property'	=>	array(
				'requiredfield'	=>	array( // 从检查类型中抽取出来的检查属性
						'title'	=>	'是否必填',
						'type'	=>	'checkbox',
						'default'=>'0',
						'id'	=>	'requiredfield',
						'name'	=>	'requiredfield',
						'dbfield'=>'isrequired',
				    'function'=>'changetagsortandrequired',
						'displayright'=>1,
				),'checkfunc'	=>	array(
						'title'	=>	'检查类型',
						'type'	=>	'select',
						'default'=>'',
						'id'	=>	'checkfunc',
						'name'	=>	'checkfunc',
						'dbfield'=>'validatetype',
						// select option(val|text ) # op # op
						'data'	=>	'|无需验证#eamil|邮箱#url|网址#number|数字#digits|整数#double|浮点数#lettersonly|字母',
						'displayright'=>1,
				),
				'titlepercent'=>array(
					'title'	=>	'标题占比',
					'type'	=>	'select',
					'id'	=>	'titlepercent',
					'name'	=>	'titlepercent',
					'displayright'	=>	0,
					'dbfield'=>'titlepercent',
					'default'=>'8',
					'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
					'function'=>'titlepercentchange',
				)
				,'contentpercent'=>array(
					'title'	=>	'内容框占比',
					'type'	=>	'select',
					'id'	=>	'contentpercent',
					'name'	=>	'contentpercent',
					'displayright'	=>	0,
					'dbfield'=>'contentpercent',
					'default'=>'0',
					'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
					'function'=>'contentpercentchange',
				),'islock'	=>	array(
				'title'	=>	'是否允许编辑',
				'type'	=>	'checkbox',
				'default'=>1,
				'id'	=>	'islock',
				'name'	=>	'islock',
				'displayright'=>0,
				'dbfield'=>'islock',
				'function'=>'changetagsortandlock',
			)
				
				),
	),
	// 固定文本
	'fiexdtext'	=>	array(
		'show'	=>	1,
		'title'	=>	'固定文本',
		'iscreate'	=>	0 , //是否生成到数据库中字段
		'isview'	=>	0 , //是否生成到视图页面
		'isline' => false, // 一个标签是否占一行
		'isconfig'=>	0 , // 是否生成字段配置
		'html'	=>	'
			<div class="nbm_controll $isshow" data="fiexdtext" $checkorder $isline>
  				<b><i class="$copycontrollcls"></i>$title</b>
  				$delTag $hidden 
			</div>
			<div class="nbmshadow">
	               <div class="icon_stort_lay">$statusHtml</div>
	        </div>
		',
			'view'=>'
			<div class="#class#" style="#style#">
				<span class="block #content_class#" style="#content_style#">
				#content#
				</span>
			
			
			</div>',
			'property'	=>	array(
			'requiredfield'	=>	array( // 从检查类型中抽取出来的检查属性
					'title'	=>	'是否必填',
					'type'	=>	'checkbox',
					'default'=>'0',
					'id'	=>	'requiredfield',
					'name'	=>	'requiredfield',
					'dbfield'=>'isrequired',
			    'function'=>'changetagsortandrequired',
					'displayright'=>1,
			),'checkfunc'	=>	array(
					'title'	=>	'检查类型',
					'type'	=>	'select',
					'default'=>'',
					'id'	=>	'checkfunc',
					'name'	=>	'checkfunc',
					'dbfield'=>'validatetype',
					// select option(val|text ) # op # op
					'data'	=>	'|无需验证#eamil|邮箱#url|网址#number|数字#digits|整数#double|浮点数#lettersonly|字母',
					'displayright'=>1,
			),
			'aligntype'	=>	array(//这个key不可修改，页面业务中已使用
				'title'	=>	'对齐方式',
				'type'	=>	'select',
				'data'	=>	'|默认#left|居左#center|居中#right|居右',
				'id'	=>	'aligntype',
				'name'	=>	'aligntype',
				'dbfield'=>'aligntype',
				'default'=>'left',
				'displayright'=>1,
			),
			'backgroundcolor'	=>	array(//这个key不可修改，页面业务中已使用
				'title'	=>	'背景颜色',
				'type'	=>	'select',
				'data'	=>	'|默认#fff|白色#ccc|灰色#f90|黄色',
				'id'	=>	'backgroundcolor',
				'name'	=>	'backgroundcolor',
				'dbfield'=>'backgroundcolor',
				'default'=>'fff',
				'displayright'=>1,
			),
			'fontcolor'	=>	array(//这个key不可修改，页面业务中已使用
				'title'	=>	'字体颜色',
				'type'	=>	'select',
				'data'	=>	'|默认#fff|白色#f90|黄色',
				'id'	=>	'fontcolor',
				'name'	=>	'fontcolor',
				'dbfield'=>'fontcolor',
				'default'=>'333',
				'displayright'=>1,
			),
			'fontsize'	=>	array(//这个key不可修改，页面业务中已使用
				'title'	=>	'字体大小',
				'type'	=>	'select',
				'data'	=>	'|默认#14|14号#18|18号#20|20号#22|22号#30|30号',
				'id'	=>	'fontsize',
				'name'	=>	'fontsize',
				'default'=>'14',
				'dbfield'=>'fontsize',
				'displayright'=>1,
			),
			'fontheight'	=>	array(//这个key不可修改，页面业务中已使用
				'title'	=>	'行高',
				'type'	=>	'select',
				'data'	=>	'|默认#14|14号#18|18号#20|20号#22|22号#30|30号',
				'id'	=>	'fontheight',
				'name'	=>	'fontheight',
				'default'=>'30',
				'dbfield'=>'fontheight',
				'displayright'=>1,
			),
			/*'contentheight'	=>	array(//这个key不可修改，页面业务中已使用
				'title'	=>	'文本区高度',
				'type'	=>	'select',
				'data'	=>	'|默认#14|14号#18|18号#20|20号#22|22号#30|30号',
				'id'	=>	'contentheight',
				'name'	=>	'contentheight',
				'dbfield'=>'contentheight',
				'default'=>'12',
				'displayright'=>1,
			),*/
			'fontweight'	=>	array(//这个key不可修改，页面业务中已使用
				'title'	=>	'字体粗细',
				'type'	=>	'select',
				'data'	=>	'|默认#400|400#500|500#600|600#700|700',
				'id'	=>	'fontweight',
				'name'	=>	'fontweight',
				'dbfield'=>'fontweight',
				'default'=>'400',
				'displayright'=>1,
			),'titlepercent'=>array(
				'title'	=>	'标题占比',
				'type'	=>	'select',
				'id'	=>	'titlepercent',
				'name'	=>	'titlepercent',
				'displayright'	=>	1,
				'dbfield'=>'titlepercent',
				'default'=>'4',
				'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
					'function'=>'titlepercentchange',
			),'contentpercent'=>array(
				'title'	=>	'内容框占比',
				'type'	=>	'select',
				'id'	=>	'contentpercent',
				'name'	=>	'contentpercent',
				'displayright'	=>	0,
				'dbfield'=>'contentpercent',
				'default'=>'0',
				'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
					'function'=>'contentpercentchange',
			),'islock'	=>	array(
				'title'	=>	'是否允许编辑',
				'type'	=>	'checkbox',
				'default'=>1,
				'id'	=>	'islock',
				'name'	=>	'islock',
				'displayright'=>0,
				'dbfield'=>'islock',
				'function'=>'changetagsortandlock',
			)
		)
	),
	// 副标题
	'subtitles'	=>	array(
		'show'	=>	0,
		'title'	=>	'副标题',
		'iscreate'	=>	0 , //是否生成到数据库中字段
		'isview'	=>	0 , //是否生成到视图页面
		'isline' => true, // 一个标签是否占一行
		'isconfig'=>	0 , // 是否生成字段配置
		'html'	=>	'
			<div class="nbm_controll $isshow" data="subtitles" $checkorder $isline >
				<div class="fieldset_legend_toggle side-catalog-text side-catalog-firstanchor">	
					<b><i class="$copycontrollcls"></i>$title</b>
					<div class="tml_style_line tml_sl4 tml_slb_blue"></div>
				</div>
			$delTag
			$hidden
			</div>
			<div class="nbmshadow">
	               <div class="icon_stort_lay">$statusHtml</div>
	        </div>
		','property' => array(
			'requiredfield'	=>	array( // 从检查类型中抽取出来的检查属性
					'title'	=>	'是否必填',
					'type'	=>	'checkbox',
					'default'=>'0',
					'id'	=>	'requiredfield',
					'name'	=>	'requiredfield',
					'dbfield'=>'isrequired',
			    'function'=>'changetagsortandrequired',
					'displayright'=>1,
			),'checkfunc'	=>	array(
					'title'	=>	'检查类型',
					'type'	=>	'select',
					'default'=>'',
					'id'	=>	'checkfunc',
					'name'	=>	'checkfunc',
					'dbfield'=>'validatetype',
					// select option(val|text ) # op # op
					'data'	=>	'|无需验证#eamil|邮箱#url|网址#number|数字#digits|整数#double|浮点数#lettersonly|字母',
					'displayright'=>1,
			),
//			'splider'=>array(
//				'type'=>'hr',
//				'displayright'=>1,
//			),
			'aligntype'	=>	array(//这个key不可修改，页面业务中已使用
				'title'	=>	'对齐方式',
				'type'	=>	'select',
				'data'	=>	'left|默认#center|居中#right|居右',
				'id'	=>	'aligntype',
				'name'	=>	'aligntype',
				'dbfield'=>'aligntype',
				'displayright'=>1,
			),
			'backgroundcolor'	=>	array(//这个key不可修改，页面业务中已使用
				'title'	=>	'背景颜色',
				'type'	=>	'select',
				'data'	=>	'fff|默认#ccc|灰色#f90|黄色',
				'id'	=>	'backgroundcolor',
				'name'	=>	'backgroundcolor',
				'dbfield'=>'backgroundcolor',
				'displayright'=>1,
			),
			'fontcolor'	=>	array(//这个key不可修改，页面业务中已使用
				'title'	=>	'字段颜色',
				'type'	=>	'select',
				'data'	=>	'ccc|默认#fff|白色#f90|黄色',
				'id'	=>	'fontcolor',
				'name'	=>	'fontcolor',
				'dbfield'=>'fontcolor',
				'displayright'=>1,
			),
			'fontsize'	=>	array(//这个key不可修改，页面业务中已使用
				'title'	=>	'字段大小',
				'type'	=>	'select',
				'data'	=>	'12|默认#14|14号#18|18号#20|20号#22|22号#30|30号',
				'id'	=>	'fontsize',
				'name'	=>	'fontsize',
				'dbfield'=>'fontsize',
				'displayright'=>1,
			),
			'fontheight'	=>	array(//这个key不可修改，页面业务中已使用
				'title'	=>	'行高',
				'type'	=>	'select',
				'data'	=>	'12|默认#14|14号#18|18号#20|20号#22|22号#30|30号',
				'id'	=>	'fontheight',
				'name'	=>	'fontheight',
				'dbfield'=>'fontheight',
				'displayright'=>1,
			),
			'contentheight'	=>	array(//这个key不可修改，页面业务中已使用
				'title'	=>	'文本区高度',
				'type'	=>	'select',
				'data'	=>	'12|默认#14|14号#18|18号#20|20号#22|22号#30|30号',
				'id'	=>	'contentheight',
				'name'	=>	'contentheight',
				'dbfield'=>'contentheight',
				'displayright'=>1,
			),
			'fontweight'	=>	array(//这个key不可修改，页面业务中已使用
				'title'	=>	'字段粗细',
				'type'	=>	'select',
				'data'	=>	'400|默认#500|500#600|600#700|700',
				'id'	=>	'fontweight',
				'name'	=>	'fontweight',
				'dbfield'=>'fontweight',
				'displayright'=>1,
			),'titlepercent'=>array(
				'title'	=>	'标题占比',
				'type'	=>	'select',
				'id'	=>	'titlepercent',
				'name'	=>	'titlepercent',
				'displayright'	=>	0,
				'dbfield'=>'titlepercent',
				'default'=>'8',
				'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
					'function'=>'titlepercentchange',
			),'contentpercent'=>array(
				'title'	=>	'内容框占比',
				'type'	=>	'select',
				'id'	=>	'contentpercent',
				'name'	=>	'contentpercent',
				'displayright'	=>	0,
				'dbfield'=>'contentpercent',
				'default'=>'0',
				'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
					'function'=>'contentpercentchange',
			),'islock'	=>	array(
				'title'	=>	'是否允许编辑',
				'type'	=>	'checkbox',
				'default'=>1,
				'id'	=>	'islock',
				'name'	=>	'islock',
				'displayright'=>0,
				'dbfield'=>'islock',
				'function'=>'changetagsortandlock',
			)
		)
	)
	,'datatable'	=>	array(
		'show'	=>	1,
		'title'	=>	'内嵌数据表格',
		'iscreate'	=>	1 , //是否生成到数据库中字段
		'isview'	=>	1 , //是否生成到视图页面
		'weight'	=>	8, // 权重，用处：在字段生成时出现的默认排序位置。
		'isline' => true, // 一个标签是否占一行
		'isconfig'=>	0 , // 是否生成字段配置
		'html'	=>	'
				<div class="nbm_controll $isshow" data="datatable" $checkorder $isline >
				<label class="label_new"><i class="$copycontrollcls"></i>$title</label>
				<table class="data-table tml-table-w table_into">
					<thead>
						<tr>
							<th>表头一</th>
							<th>表头二</th>
							<th>表头三</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><input type="text" value="" class="textInput"></td>
							<td><input type="text" class="textInput"></td>
							<td><input type="text" class="textInput"></td>
						</tr>
					</tbody>
				</table>
				$delTag
            	$hidden
			</div>
			<div class="nbmshadow table-element">
	           <div class="icon_stort_lay">$statusHtml</div>
	        </div>
		',
		'view'=>'
			<div class="#class#" style="#style#">
			#content#
			</div>',
		'property' => array(
			'requiredfield'	=>	array( // 从检查类型中抽取出来的检查属性
					'title'	=>	'是否必填',
					'type'	=>	'checkbox',
					'default'=>'0',
					'id'	=>	'requiredfield',
					'name'	=>	'requiredfield',
					'dbfield'=>'isrequired',
			    'function'=>'changetagsortandrequired',
					'displayright'=>1,
			),'checkfunc'	=>	array(
					'title'	=>	'检查类型',
					'type'	=>	'select',
					'default'=>'',
					'id'	=>	'checkfunc',
					'name'	=>	'checkfunc',
					'dbfield'=>'validatetype',
					// select option(val|text ) # op # op
					'data'	=>	'|无需验证#eamil|邮箱#url|网址#number|数字#digits|整数#double|浮点数#lettersonly|字母',
					'displayright'=>1,
			),
//			'splider'=>array(
//				'type'=>'hr',
//				'displayright'=>1,
//			),
			'fieldlist'	=>	array(//这个key不可修改，页面业务中已使用
				'title'	=>	'字段列表信息',
				'type'	=>	'dialog',
				'id'	=>	'fieldlist',
				'name'	=>	'fieldlist',
				'dbfield'=>'fieldlist',
				'isforeignoprate' => 1,
				'displayright'=>1,
			),'dtlookup'	=>	array(//这个key不可修改，页面业务中已使用
				'title'	=>	'内嵌表格设置',
				'type'	=>	'dialog',
				'id'	=>	'dtlookup',
				'name'	=>	'dtlookup',
				'dbfield'=>'fieldrelation',
				'dialogcontroll'=>'datatabledatasouceset',
				'isforeignoprate' => 1,
				'dialogtitle'=>'内嵌表格设置',
				'displayright'=>1,
			),'calculate'	=>	array(//这个key不可修改，页面业务中已使用
				'title'	=>	'关联计算设置',
				'type'	=>	'dialog',
				'id'	=>	'calculate',
				'name'	=>	'calculate',
				'dbfield'=>'calculate',
				'isforeignoprate' => 1,
				'dialogcontroll'=>'calculatedatatable',
				'dialogtitle'=>'关联计算设置',
				'displayright'=>1,
			),
			'iscreatepager'=>array( //这个key不可修改，页面业务中已使用
				'title'=>'是否分页',
				'type'=>'checkbox',
				'id'=>'iscreatepager',
				'name'=>'iscreatepager',
				'dbfield'=>'iscreatepager',
				'displayright'=>1,
			),
			'iscreatesearch'=>array( //这个key不可修改，页面业务中已使用
				'title'=>'是否生成搜索',
				'type'=>'checkbox',
				'id'=>'iscreatesearch',
				'name'=>'iscreatesearch',
				'dbfield'=>'iscreatesearch',
				'displayright'=>1,
			),'islock'	=>	array(
				'title'	=>	'是否允许编辑',
				'type'	=>	'checkbox',
				'default'=>1,
				'id'	=>	'islock',
				'name'	=>	'islock',
				'displayright'=>0,
				'isforeignoprate' => 1,
				'dbfield'=>'islock',
				'function'=>'changetagsortandlock',
			),'titlepercent'=>array(
				'title'	=>	'标题占比',
				'type'	=>	'select',
				'id'	=>	'titlepercent',
				'name'	=>	'titlepercent',
				'displayright'	=>	0,
				'dbfield'=>'titlepercent',
				'default'=>'8',
				'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
					'function'=>'titlepercentchange',
			),'contentpercent'=>array(
				'title'	=>	'内容框占比',
				'type'	=>	'select',
				'id'	=>	'contentpercent',
				'name'	=>	'contentpercent',
				'displayright'	=>	0,
				'dbfield'=>'contentpercent',
				'default'=>'0',
				'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
					'function'=>'contentpercentchange',
			),
			'org'	=>	array(
				'title'	=>	'ORG设置',
				'type'	=>	'text',
				'id'	=>	'org',
				'name'	=>	'org',
				'data'	=>	'',
				'displayright'=>1,
				'dbfield'=>'org',
			)
		)
	),
		'defcomponent'		=>	array(
				'show'	=>	1,	//是否显示在工具列表中
				'title'	=>'自定义组件',
				'iscreate'	=>	0 , //是否生成到数据库中字段
				'isview'	=>	1 , //是否生成到视图页面
				'weight'	=>	0, // 权重，用处：在字段生成时出现的默认排序位置。
				'isline' => true, // 一个标签是否占一行
				'isconfig'=>	0 , // 是否生成字段配置
				'listwidth'=>'',//列表宽度
				'html'	=>	'
			<div class="nbm_controll $isshow" data="defcomponent" $checkorder $isline>
  				<b><i class="$copycontrollcls"></i>$title</b>
				自定义组件，PS：显示内容自定义，不提供预览效果
  				$delTag $hidden 
			</div>
			<div class="nbmshadow">
	               <div class="icon_stort_lay">$statusHtml</div>
	        </div>
		',
			'view'=>'
			<div class="#class#" style="#style#">
				#component#
			</div>',
				'property' => array(
						'component'	=>	array(
								'title'	=>	'自定义组件内容',
								'type'	=>	'dialog',
								'id'	=>	'component',
								'name'	=>	'component',
								'dialogcontroll'=>'componentContentEdit',// 对话框请求的php函数名
								'displayright'=>1,
								'isforeignoprate' => 1,
								'dbfield'=>'component',
		
						),'titlepercent'=>array(
				'title'	=>	'标题占比',
				'type'	=>	'select',
				'id'	=>	'titlepercent',
				'name'	=>	'titlepercent',
				'displayright'	=>	1,
				'dbfield'=>'titlepercent',
				'default'=>'8',
				'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
					'function'=>'titlepercentchange',
			),'contentpercent'=>array(
				'title'	=>	'内容框占比',
				'type'	=>	'select',
				'id'	=>	'contentpercent',
				'name'	=>	'contentpercent',
				'displayright'	=>	1,
				'dbfield'=>'contentpercent',
				'default'=>'0',
				'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
					'function'=>'contentpercentchange',
			)
				),
		),
		// 图片上传编辑 
		'picedit'	=>	array(
				'show'	=>	1,
				'title'	=>	'图片上传编辑 ',
				'iscreate'	=>	1 , //是否生成到数据库中字段
				'isview'	=>	1 , //是否生成到视图页面
				'isline' => true, // 一个标签是否占一行
				'listwidth'=>'',//列表宽度
				'isconfig'=>	1 , // 是否生成字段配置
				'html'	=>	'
            <div class="nbm_controll $isshow" data="picedit" $checkorder $isline >
			<label class="label_new"><i class="$copycontrollcls"></i>$title</label>
			<div id="nbmxkjuploads" class="uploadify" style="height: 25px; width: 104px;">
			<div id="nbmxkjuploads-button" class="uploadify-button " style="background-image: url(&quot;/system/Public/uploadify/upload.png&quot;); text-indent: -9999px; height: 25px; line-height: 25px; width: 104px;">
			<span class="uploadify-button-text">选择上传文件</span></div></div>
			$delTag
             $hidden
			</div>
			<div class="nbmshadow upload-element">
	           <div class="icon_stort_lay">$statusHtml</div>
	        </div>
		','property' => array(
						'uploadnum'	=>	array( // 从检查类型中抽取出来的检查属性
								'title'	=>	'上传个数',
								'type'	=>	'text',
								'default'=>'',
								'id'	=>	'uploadnum',
								'name'	=>	'uploadnum',
								'dbfield'=>'uploadnum',
								'displayright'=>1,
						),'uploadtype'	=>	array( // 从检查类型中抽取出来的检查属性
								'title'	=>	'上传限制类型',
								'type'	=>	'text',
								'id'	=>	'uploadtype',
								'name'	=>	'uploadtype',
								'dbfield'=>'uploadtype',
								'default'=>'*.jpg;*.jpeg;*.gif;*.png;',
								'displayright'=>0,
						),'widthheight'	=>	array( // 从检查类型中抽取出来的检查属性
							'title'	=>	'图片宽高',
							'type'	=>	'text',
							'default'=>'',
							'id'	=>	'widthheight',
							'name'	=>	'widthheight',
							'dbfield'=>'widthheight',
							'displayright'=>1,
						),'requiredfield'	=>	array( // 从检查类型中抽取出来的检查属性
								'title'	=>	'是否必填',
								'type'	=>	'checkbox',
								'default'=>'0',
								'id'	=>	'requiredfield',
								'name'	=>	'requiredfield',
								'dbfield'=>'isrequired',
								'function'=>'changetagsortandrequired',
								'displayright'=>1,
						),'checkfunc'	=>	array(
								'title'	=>	'检查类型',
								'type'	=>	'select',
								'default'=>'',
								'id'	=>	'checkfunc',
								'name'	=>	'checkfunc',
								'dbfield'=>'validatetype',
								// select option(val|text ) # op # op
								'data'	=>	'|无需验证#eamil|邮箱#url|网址#number|数字#digits|整数#double|浮点数#lettersonly|字母',
								'displayright'=>0,
						),'islock'	=>	array(
								'title'	=>	'是否允许编辑',
								'type'	=>	'checkbox',
								'default'=>1,
								'id'	=>	'islock',
								'name'	=>	'islock',
								'displayright'=>1,
								'dbfield'=>'islock',
								'function'=>'changetagsortandlock',
						),'titlepercent'=>array(
								'title'	=>	'标题占比',
								'type'	=>	'select',
								'id'	=>	'titlepercent',
								'name'	=>	'titlepercent',
								'displayright'	=>	1,
								'dbfield'=>'titlepercent',
								'default'=>'1',
								'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
								'function'=>'titlepercentchange',
						),'contentpercent'=>array(
								'title'	=>	'内容框占比',
								'type'	=>	'select',
								'id'	=>	'contentpercent',
								'name'	=>	'contentpercent',
								'displayright'	=>	1,
								'dbfield'=>'contentpercent',
								'default'=>'7',
								'data'	=>	'|请选择#1|一列#2|二列#3|三列#4|四列#5|五列#6|六列#7|七列#8|八列',
								'function'=>'contentpercentchange',
						)
				)
		),
);