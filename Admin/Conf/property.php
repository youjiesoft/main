<?php
/**
 *  公用属性文件
 */
// 组件通用模板,分别适用到查看、新增、修改页面中
$NBM_TEMPLATE=array(
		'view'=>'
			<div class="#class#" original="#original#" category="#category#" style="#style#">
				<label class="label_new">
					{$fields["#fields#"]}:
				</label>
				<span class="input_new">#content#</span>
			</div>',
		'add'=>'
			<div class="#class#">
				<label class="label_new">
					{$fields["#fields#"]}:
				</label>
				<span class="input_new">#content#</span>
			</div>',
		'edit'=>'
			<div class="#class#">
				<label class="label_new">
					{$fields["#fields#"]}:
				</label>
				<span class="input_new">#content#</span>
			</div>'
);

		
$NBM_COMMON_PROPERTY = array(
	'id'	=>	array( // 不可删除属性
		'title'	=>	'组件类型',
		'type'	=>	'select',
		'id'	=>	'id',
		'name'	=>	'id',
		'dbfield'=>'id',
		'isforeignoprate' => 1,
		'displayright'=>0,
	),
	'isforeignfield'	=>	array( // 不可删除属性
		'title'	=>	'是否为引用字段',// 复用
		'type'	=>	'select',
		'id'	=>	'isforeignfield',
		'name'	=>	'isforeignfield',
		'dbfield'=>'isforeignfield',
		'isforeignoprate' => 1,
		'displayright'=>0,
	),
	'catalog'	=>	array( // 不可删除属性
		'title'	=>	'组件类型',
		'type'	=>	'select',
		'id'	=>	'catalog',
		'name'	=>	'catalog',
		'dbfield'=>'category',
		'displayright'=>1,
		'dataAdd'=>'tagcategorycontroll',
		'checkvaluetovisable'=>'ids',
		'changeTagCategory'=>'changeTagCategory',
	),
	'fields'	=>	array( // 不可删除属性
		'title'	=>	'字段名',
		'type'	=>	'text',
		'id'	=>	'fields',
		'name'	=>	'fields',
		'dbfield'=>'fieldname',
		'identity'	=> true,
		'displayright'=>1,
		'checkvaluetovisable'=>'ids',
	)
	,'sort'	=>	array(	// 不可删除属性
		'title'	=>	'排序',
		'type'	=>	'text',
		'id'	=>	'sort',
		'name'	=>	'sort',
		'dbfield'=>'sort',
		'displayright'=>0,
	)
	,'ids'	=>	array( // 不可删除属性
		'title'	=>	'当前项ID',
		'type'	=>	'text',
		'id'	=>	'ids',
		'name'	=>	'ids',
		'dbfield'=>'ids',
		'isforeignoprate' => 1,
		'displayright'=>'0',
	)
	,'title'	=>	array( // 不可删除属性
		'title'	=>	'标题',
		'type'	=>	'text',
		'default'=>'',
		'id'	=>	'title',
		'name'	=>	'title',
		'dbfield'=>'title',
		'isforeignoprate' => 1,
		'displayright'=>1,
		
	),'searchlist'	=>	array(
		'title'	=>	'是否检索',
		'type'	=>	'checkbox',
		'default'=>'0',
		'id'	=>	'searchlist',
		'name'	=>	'searchlist',
		'displayright'=>1,
		'dbfield'=>'isformsearch',
		
	),'allsearchlist'	=>	array(
		'title'	=>	'是否全局检索',
		'type'	=>	'checkbox',
		'default'=>'0',
		'id'	=>	'allsearchlist',
		'name'	=>	'allsearchlist',
		'displayright'=>1,
		'dbfield'=>'islistsearch',
		
	),'tabletype'	=>	array(
		'title'	=>	'字段数据类型',
		'type'	=>	'select',
		// 'default'=>'VARCHAR',
		'id'	=>	'tabletype',
		'name'	=>	'tabletype',
		'dbfield'=>'fieldtype',
		'data'	=>	'varchar|字符串(varchar)|30|text#int|整型(int)|10|text#text|文本型(text)|255|textarea#longtext|长文本型(longtext)|255|textarea#decimal|浮点型(decimal)|18,6|text#smallint|短整型(smallint)|8|text#tinyint|布尔型(tinyint)|1|text#date|日期型(date)|11|date',
		'default'=>':0',
		'displayright'=>1,
		'dbfield'=>'fieldtype',
		'beforefunc'=>'setdefault',
		'checkvaluetovisable'=>'ids',
	)
	 ,'tablename'	=>	array(
		'title'	=>	'插入到的表',
		'type'	=>	'select',
		'id'	=>	'tablename',
		'name'	=>	'tablename',
		'data'	=>	'',
		'dataAdd'=>'checkavailabletablecontroll',
	 	'dbfield'=>'dbname',
		'default'=>':0',
		'beforefunc'=>'setdefault',
	 	'isforeignoprate' => 1,
		'displayright'=>1,
		'checkvaluetovisable'=>'ids',
	)
	 /*,'defaultval'	=>	array(
		'title'	=>	'默认值',
		'type'	=>	'text',
		'id'	=>	'defaultval',
		'name'	=>	'defaultval',
		'dbfield'=>'fieldcheck',
		'displayright'=>1,
		
	)*/,'length'	=>	array(
		'title'	=>	'字段长度',
		'type'	=>	'text',
		'default'=>'11',
		'id'	=>	'length',
		'name'	=>	'length',
		'dbfield'=>'tablelength',
		'displayright'=>1,
		'checkvaluetovisable'=>'ids',
		//'dbfield'=>'dblength',
	),'isshow'	=>	array( //不可删除属性
		'title'	=>	'是否显示在页面',
		'type'	=>	'checkbox',
		'default'=>1,
		'id'	=>	'isshow',
		'name'	=>	'isshow',
		'displayright'=>1,
		'isforeignoprate' => 1,
		'dbfield'=>'isshow',
		'function'=>'changetagsortanddisplay', // 后
	),'models'	=>	array( //不可删除属性
		'title'	=>	'关联模板',
		'type'	=>	'text',
		'default'=>'',
		'id'	=>	'models',
		'name'	=>	'models',
		'displayright'=>1,
		'dbfield'=>'models',
	),
	'methods'	=>	array( //不可删除属性
			'title'	=>	'关联函数',
			'type'	=>	'text',
			'default'=>'view',
			'id'	=>	'methods',
			'name'	=>	'methods',
			'displayright'=>1,
			'dbfield'=>'methods',
	),
	'relation'	=>	array( //不可删除属性
			'title'	=>	'关联关系',
			'type'	=>	'text',
			'default'=>'orderno',
			'id'	=>	'relation',
			'name'	=>	'relation',
			'displayright'=>1,
			'dbfield'=>'relation',
	),
);

$NBM_DEFAUL_PROPERTYVAL=array(
	'text'=>array('isshow'=>1,'islock'=>1,'ids'=>'$index'),
	'select'=>array('isshow'=>1,'islock'=>1,),
	'checkbox'=>array('isshow'=>1,'islock'=>1,),
	'radio'=>array('isshow'=>1,'islock'=>1,),
	'textarea'=>array('rows'=>2,'cols'=>100,'islock'=>1,'isshow'=>1),
	'date'=>array('isshow'=>1,'islock'=>1,'format'=>'yyyy-MM-dd@Y-m-d'),
	'lookup'=>array('isshow'=>1,'islock'=>1,'lookupgroup'=>'org$index','lookupchoice'=>""),
	'lookupsuper'=>array('isshow'=>1,'islock'=>1,'lookupgroup'=>'org$index','lookupchoice'=>""),
);

// 类型配置项名称  => 简单配置文件中的key值
$NBM_REL_FILED=array(
	'catalog'=>'category',	
	'fields'=>'filed',
	'title'=>'title',
	'tabletype'=>'type',
	'length'=>'length',
	'sort'=>'sort',
	'tablename'=>'tablename'
);

/**
 *  模板生成类型
 *  
 *  数组中一维只是用来区分用的，不做取值。
 *  最终代码生成时的类型判断使用的是二维与三维。
 *  二维 的 value属性 做的生成方案 选择：现有 
 *  			basisarchivestpl：基础档案
 *  			audittpl：审批流
 *  			noaudittpl：普通表单
 * 	三维的value属性 做的是模板布局选择：现有
 * 				ltrl：左侧树右侧列表
 * 				lmrl：左侧菜单右侧列表
 * 				llrt：左侧列表右侧树
 * 				llrm：左侧列表右侧菜单
 * 				list：只有列表
 *  
 * @var array $NBM_TPL_TYPE
 */
$NBM_TPL_TYPE=array(
	// 基础档案
	'basisarchivestpl'=>array(
		// 基本的信息
		'0'=>array('typename'=>'basisarchivestpl','title'=>'基础档案','id'=>'nbm_base_archives_tpl' ,'name'=>'nbm_tpl_type','type'=>'radio' ,'value'=>'basisarchivestpl',/*'checked'=>'checked',*/
			'item'=>array(
				'0'=>array('typename'=>'basisarchivestpl','title'=>'基础档案-默认模板',	'id'=>'nbm_base_archives_tpl' , 'name'=>'nbm_tpl_type',	'type'=>'radio', 'value'=>'basisarchivestpl', /*'checked'=>'checked',*/
					//模板构成方式。
					'tpl'=>array(
						'0'=>array('title'=>'基础档案右侧为表单，新增按钮ajax刷新右侧表单页面', 'value'=>'ltrc', 'content'=>'<table><tr><td style="width:250px;">菜单</td><td>内容</td></tr></table>'),
						'1'=>array('title'=>'基础档案右侧为列表', 'value'=>'ltrl', 'content'=>'<table><tr><td style="width:250px;">菜单</td><td>列表</td></tr></table>'),
					)
				)/*不作基础档案审批流 ,'1'=>array('typename'=>'basisarchivestpl','title'=>'基础档案-审批流',	'id'=>'nbm_base_archives_audit_tpl' , 'name'=>'nbm_tpl_type',	'type'=>'radio', 'value'=>'basisarchivesaudittpl',
					//模板构成方式。
					'tpl'=>array(
						'0'=>array('title'=>'左侧菜右侧内容', 'value'=>'default', 'content'=>'<table><tr><td style="width:250px;">菜单</td><td>列表</td></tr></table>'),
					)
				) */
			)
		)
	),
	// 普通表单
	'commontpl'=>array(
		'0'=>array('typename'=>'commontpl','title'=>'普通表单',	'id'=>'nbm_common_tpl' , 'name'=>'nbm_tpl_type',	'type'=>'radio', 'value'=>'commontpl', /*'checked'=>'checked',*/
			// item 表示的项是其子项
			'item'=>array(
				'0'=>array('typename'=>'audittpl','title'=>'审批模板',	'id'=>'nbm_common_audit_tpl' , 'name'=>'nbm_tpl_type',	'type'=>'radio', 'value'=>'audittpl', /*'checked'=>'checked',*/
					'tpl'=>array(
							'0'=>array('title'=>'审批模板-默认模板','value'=>'lmrl', 'content'=>'<table class="table_check_view"><tr><td><span class="icon icon-reorder"></span>菜单</td><td><span class="icon icon-list-ol"></span>列表</td></tr></table>')
						)
				),
				'1'=>array('typename'=>'noaudittpl','title'=>'普通模板',	'id'=>'nbm_common_noaudit_tpl' , 'name'=>'nbm_tpl_type',	'type'=>'radio', 'value'=>'noaudittpl', /*'checked'=>'checked',*/
					'tpl'=>array(
							'0'=>array('title'=>'普通模板-左侧树右侧内容','value'=>'ltrl', 'content'=>
								'<table class="table_check_view"><tr><td><span class="icon icon-sitemap"></span>树</td><td><span class="icon icon-list-ol"></span>列表</td></tr></table>'
							),
							'2'=>array('title'=>'普通模板-普通列表页','value'=>'list', 'content'=>
								'<table class="table_check_view"><tr><td><span class="icon icon-list-ol"></span>列表</td></tr></table>'
							)
						)
				)
			)
		)
	),
	//'2'=>array()
);

/*
 * 系统默认字段配置
 * canuse 1 用户自定义数据输入载体
 * fieldcategory	字段所属类型  audit:审批流 ， default：系统默认字段，base：基础档案字段
 */
// 默认字段
$NBM_DEFAULTFIELD = array (
	'bindid'		=>array('type'=>'varchar' , 	'length'=>'100' , 	'default'=>'',	'isnull'=>'' , 		'desc'=>"COMMENT '绑定id'", 		'title'=>'绑定id',		'canuse'=>'0', 'fieldcategory'=>'default'),
	'orderno'		=>array('type'=>'varchar' , 'length'=>'100' , 	'default'=>'',	'isnull'=>'NULL' , 	'desc'=>"COMMENT '编号'", 		'title'=>'编号' , 		'canuse'=>'1', 'fieldcategory'=>'default'),
	'status'		=>array('type'=>'tinyint' , 'length'=>'1' , 	'default'=>'1',	'isnull'=>'' , 		'desc'=>"COMMENT '状态'", 		'title'=>'状态',			'canuse'=>'0', 'fieldcategory'=>'default'),
	'companyid'		=>array('type'=>'int' , 	'length'=>'10' , 	'default'=>'0',	'isnull'=>'NULL' , 	'desc'=>"COMMENT '公司ID'",		'title'=>'公司',		'canuse'=>'1', 'fieldcategory'=>'default'),
	'createid'		=>array('type'=>'int' , 	'length'=>'10' , 	'default'=>'0',	'isnull'=>'NULL' , 	'desc'=>"COMMENT '创建人ID'", 	'title'=>'创建人', 	'canuse'=>'1', 'fieldcategory'=>'default'),
	'createtime'	=>array('type'=>'int' , 	'length'=>'11' , 	'default'=>'',	'isnull'=>'NULL' , 	'desc'=>"COMMENT '创建时间'", 	'title'=>'创建时间',		'canuse'=>'1', 'fieldcategory'=>'default'),
	'updateid'		=>array('type'=>'int' , 	'length'=>'10' , 	'default'=>'',	'isnull'=>'NULL' ,	'desc'=>"COMMENT '修改人ID'", 	'title'=>'修改人', 	'canuse'=>'1', 'fieldcategory'=>'default'),
	'updatetime'	=>array('type'=>'int' , 	'length'=>'11' , 	'default'=>'',	'isnull'=>'NULL' ,	'desc'=>"COMMENT '修改时间'", 	'title'=>'修改时间',		'canuse'=>'1', 'fieldcategory'=>'default'),
	'operateid'		=>array('type'=>'int' , 	'length'=>'1' , 	'default'=>'0',	'isnull'=>'NOT NULL' , 'desc'=>"COMMENT '是否确认'", 'title'=>'是否确认',	'canuse'=>'1', 'fieldcategory'=>'default'),
	'departmentid'	=>array('type'=>'int' , 	'length'=>'10' , 	'default'=>'0',	'isnull'=>'NULL' , 	'desc'=>"COMMENT '部门ID'", 		'title'=>'部门', 		'canuse'=>'1', 'fieldcategory'=>'default'),
	'projectid'		=>array('type'=>'int' , 	'length'=>'10' , 	'default'=>'',	'isnull'=>'NULL' , 	'desc'=>"COMMENT '项目ID'", 		'title'=>'项目',	 	'canuse'=>'1', 'fieldcategory'=>'default'),
	'projectworkid'	=>array('type'=>'int' , 	'length'=>'10' , 	'default'=>'',	'isnull'=>'NULL' , 	'desc'=>"COMMENT '任务ID'", 		'title'=>'任务', 		'canuse'=>'1', 'fieldcategory'=>'default'),
	'sysdutyid'		=>array('type'=>'int' , 	'length'=>'10' , 	'default'=>'',	'isnull'=>'NULL' , 	'desc'=>"COMMENT '职级ID'", 		'title'=>'职级', 		'canuse'=>'1', 'fieldcategory'=>'default'),
	'relationmodelname'=>array('type'=>'varchar','length'=>'100',	'default'=>'',	'isnull'=>'NULL' , 	'desc'=>"COMMENT '关系型关联model'", 'title'=>'关系型关联模型名称','canuse'=>'0', 'fieldcategory'=>'default'),
	'auditUser'=>array('type'=>'varchar','length'=>'200',	'default'=>'',	'isnull'=>'NULL' , 	'desc'=>"COMMENT '提醒人组合'", 'title'=>'提醒人组合','canuse'=>'1', 'fieldcategory'=>'default')
		
); 
$NBM_SYSTEM_FIELD_CONFIG = array(
	// 是否确认
	
	'operateid' =>array('category'=>'select','showoption'=>'status' , 'isshowresoult'=>1),
	// 任务
	//'projectworkid'	=>array('category'=>'select','subimporttableobj'=>'mis_project_flow_form','subimporttablefieldobj'=>'name','subimporttablefield2obj'=>'id','subimporttableobjcondition'=>'' , 'isshowresoult'=>1),
	'projectworkid'	=>array('category'=>'text','subimporttableobjcondition'=>'' , 'isshowresoult'=>1),
	// 修改人
	'updateid'	=>array('category'=>'select','subimporttableobj'=>'user','subimporttablefieldobj'=>'name','subimporttablefield2obj'=>'id','subimporttableobjcondition'=>'' , 'isshowresoult'=>1),
	// 职级
	'sysdutyid'	=>array('category'=>'select','subimporttableobj'=>'mis_system_duty','subimporttablefieldobj'=>'name','subimporttablefield2obj'=>'id','subimporttableobjcondition'=>'' , 'isshowresoult'=>1),
	// 公司
	'companyid'	=>array('category'=>'select','subimporttableobj'=>'mis_system_company','subimporttablefieldobj'=>'name','subimporttablefield2obj'=>'id','subimporttableobjcondition'=>'' , 'isshowresoult'=>1),
	//	创建人
	'createid'	=>array('category'=>'select','subimporttableobj'=>'user','subimporttablefieldobj'=>'name','subimporttablefield2obj'=>'id','subimporttableobjcondition'=>'', 'isshowresoult'=>1),
	// 部门
	'departmentid'	=>array('category'=>'select','subimporttableobj'=>'mis_system_department','subimporttablefieldobj'=>'name','subimporttablefield2obj'=>'id','subimporttableobjcondition'=>'', 'isshowresoult'=>1),
	// 修改时间
	'updatetime'	=>array('category'=>'date', 'isshowresoult'=>1),
	// 创建时间
	'createtime'	=>array('category'=>'date', 'isshowresoult'=>1),
	//提示范围
	'auditUser'=>array('category'=>'lookup', 'lookupchoice'=>'2ba96fc5ab7bef0ac9a4d3f5d72bb1f0','lookupgrouporg'=>'ORGP#id#01','isshowresoult'=>0),
	// 当前审核状态
	'auditState'	=>array('category'=>'select','showoption'=>'auditStateVal', 'isshowresoult'=>1),
	
);

// 审批流字段
$NBM_AUDITFELD = array (
	'ptmptid'			=>array('type'=>'int' , 	'length'=>'10' , 	'default'=>'',	'isnull'=>'NULL' , 'desc'=>"COMMENT '固定流程ID'" , 'title'=>'固定流程ID',		'canuse'=>'0', 'fieldcategory'=>'audit'),
	'flowid'			=>array('type'=>'int' , 	'length'=>'10' , 	'default'=>'0',	'isnull'=>'NULL' , 'desc'=>"COMMENT '自定义流程ID'" , 'title'=>'自定义流程ID',	'canuse'=>'0', 'fieldcategory'=>'audit'),
	'ostatus'			=>array('type'=>'varchar' , 'length'=>'100' , 	'default'=>'',	'isnull'=>'NULL' , 'desc'=>"COMMENT '当前审核节点'"  , 'title'=>'当前审核节点',	'canuse'=>'0', 'fieldcategory'=>'audit'),
	'auditState'		=>array('type'=>'tinyint' , 'length'=>'1' , 	'default'=>'0',	'isnull'=>'NULL' , 'desc'=>"COMMENT '当前审核状态'" , 'title'=>'当前审核状态',	'canuse'=>'1', 'fieldcategory'=>'audit'),
	'curAuditUser'		=>array('type'=>'varchar' , 'length'=>'300' , 	'default'=>'',	'isnull'=>'NULL' , 'desc'=>"COMMENT '当前可审核人清单'" , 'title'=>'当前可审核人清单',	'canuse'=>'0', 'fieldcategory'=>'audit'),
	'curNodeUser'		=>array('type'=>'varchar' , 'length'=>'300' , 	'default'=>'',	'isnull'=>'NULL' , 'desc'=>"COMMENT '当前待审核人清单'", 'title'=>'当前待审核人清单',		'canuse'=>'0', 'fieldcategory'=>'audit'),
	'alreadyAuditUser'	=>array('type'=>'varchar' , 'length'=>'500' , 	'default'=>'',	'isnull'=>'NULL' , 'desc'=>"COMMENT '当前已审核人'", 'title'=>'当前已审核人',				'canuse'=>'0', 'fieldcategory'=>'audit'),
	'alreadyauditnode'	=>array('type'=>'int' , 'length'=>'11' , 	'default'=>'0',	'isnull'=>'NULL' , 'desc'=>"COMMENT '终审时间'", 'title'=>'终审时间' ,	'canuse'=>'0' , 'fieldcategory'=>'audit'),
	'allnode'			=>array('type'=>'varchar' , 'length'=>'100' ,	'default'=>'',	'isnull'=>'NULL' , 'desc'=>"COMMENT '所有流程节点'", 'title'=>'所有流程节点',			'canuse'=>'0', 'fieldcategory'=>'audit'),
	'informpersonid'	=>array('type'=>'varchar' , 'length'=>'500' , 	'default'=>'',	'isnull'=>'NULL' , 'desc'=>"COMMENT '节点知道会人'", 'title'=>'节点知道会人', 		'canuse'=>'0','fieldcategory'=>'audit'),
);
// 基本档案字段
$NBM_BASEARCHIVESFIELD =array(
	'parentid'			=>array('type'=>'int' , 	'length'=>'10' , 	'default'=>'0','isnull'=>'NULL' , 'desc'=>"COMMENT '父类ID'", 'title'=>'父类', 			'canuse'=>'0' ,'fieldcategory'=>'base'),
	'name'			=>array('type'=>'varchar' , 	'length'=>'50' , 	'default'=>'0','isnull'=>'NULL' , 'desc'=>"COMMENT '名称'", 'title'=>'名称', 				'canuse'=>'1' , 'fieldcategory'=>'base'),

);
// 数据库使用的关键字
$NBM_DBSYSTEMFIELD=array("add","all","alteranalyze","and","asasc","asensitive","beforebetween","bigint","binaryblob","both","bycall","cascade","casechange","char","charactercheck","collate","columncondition","connection","constraintcontinue","convert","createcross","current_date","current_timecurrent_timestamp","current_user","cursordatabase","databases","day_hourday_microsecond","day_minute","day_seconddec","decimal","declaredefault","delayed","deletedesc","describe","deterministicdistinct","distinctrow","divdouble","drop","dualeach","else","elseifenclosed","escaped","existsexit","explain","falsefetch","float","float4float8","for","forceforeign","from","fulltextgoto","grant","grouphaving","high_priority","hour_microsecondhour_minute","hour_second","ifignore","in","indexinfile","inner","inoutinsensitive","insert","intint1","int2","int3int4","int8","integerinterval","into","isiterate","join","keykeys","kill","labelleading","leave","leftlike","limit","linearlines","load","localtimelocaltimestamp","lock","longlongblob","longtext","looplow_priority","match","mediumblobmediumint","mediumtext","middleint","minute_microsecond","minute_second","mod","modifies","natural","not","no_write_to_binlog","null","numeric","on","optimize","option","optionally","or","order","out","outer","outfile","precision","primary","procedure","purge","raid0","range","read","reads","real","references","regexp","release","rename","repeat","replace","require","restrict","return","revoke","right","rlike","schema","schemas","second_microsecond","select","sensitive","separator","set","show","smallint","spatial","specific","sql","sqlexception","sqlstate","sqlwarning","sql_big_result","sql_calc_found_rows","sql_small_result","ssl","starting","straight_join","table","terminated","then","tinyblob","tinyint","tinytext","to","trailing","trigger","true","undo","union","unique","unlock","unsigned","update","usage","use","using","utc_date","utc_time","utc_timestamp","values","varbinary","varchar","varcharacter","varying","when","where","while","with","write","x509","xor","year_month","zerofill");
//系统预定义字段
$SYSTEM_FIELD=array("departmentid"=>'部门id', "sysdutyid"=>"职级id", "projectid"=>"项目id", "projectworkid"=>"任务id","companyid"=>"公司id");


