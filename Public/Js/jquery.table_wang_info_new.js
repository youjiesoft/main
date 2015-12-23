var TABLEWNEW = function() {
	var table_wang_new = {
		st: {
			__id: 0,
			nextId : function ()
			{
				return this.prefx + this.__id++;
			},
			add_default_val:[],                          //新增行默认值     {"name":"曹操","sex":"男"}
			obj:null,                                    //表格对象
			ajax_get_data_url:"",                        //ajax获取数据地址
			mini_set_orderno:"",                          //设置编号,用于miniindex
			ajax_post_url:"",                            //ajax保存提交地址
			mini_add_url:"",                             //迷你新增地址
			mini_add_type:"",                            //迷你新增跳转方式
			tr_max:0,                                    //用于标识新增行的索引
			nbox:"",
			search_column:0,                             //搜索列索引
			isReLoad:false,                              //刷新组件标识
			re_id:0,                                     //刷新次数
			tableObj:null,                               //DT对象
			default_new_tr:0,                            //添加初始行
			maxShow:-1,                                  //最大显示列数
			tableId: '#into_table_new',
			table: '.into_table_new',
			table_type: 'view',                          //表格类型       view:查看    add:新增     edit:编辑
			prefx: 'into_table',
			addRow: 'add_col_btn',                       //添加行按钮
			delRow: 'into_table_new_trash_tr',           //删除行按钮
			add_col_input: 'add_col_input',              //添加行数量输入框
			max_add_row_number: 20,                      //最大一次性添加行数
			tj_input_class:"into_table_tj_input",        //统计样式
			table_th:[],                                 //表头th
			table_th_index:[],							 //表头th索引
			template_tr:[],                              //模板tr
			tongji:[],                                   //用于控件统计功能
			ordering:false,                              //是否排序
			searching:false,                             //是否搜索
			paging:true,                                 //是否分页
			displayLength: 10,                           //分页每页显示条数
			aaData:[],                                   //列表数据
			td_isReadonly:[],                            //是否可以编辑
			readonly:false,                              //控件是否只读
			is_stats:false,                              //控件是否统计
			show_save_btn:false,                         //是否显示单行保存按钮
			show_both_save_btn: false,                   //是否显示整体保存按钮
			max_height:true,                             //最大高度
			scrollX:"900px",                             //表格滚动条宽度
			scrollY:"300px",                             //表格最大高度
			fySelectHide:true,                           //是否显示分页下拉框
			bServerSide:false,                           //是否动态加载数据
			initTdWidth:true,                            //是否初始化td内下拉组件宽度
			isYMInfo:true,                                //是否左下角页码信息
			isReloadNavTab:false,                         //保存后是否刷新标签页
			isImportBtn:true,                             //是否显示导入按钮
			formModel:"",                                  //表单model
			datatableModel:"",                               //内嵌表model
			importUrl:"",
			isSelectChange:true
		},
		date_format:function(){
			Date.prototype.format =function(format)
			{
				var o = {
					"M+" : this.getMonth()+1, //month
					"d+" : this.getDate(), //day
					"h+" : this.getHours(), //hour
					"H+" : this.getHours(), //hour
					"m+" : this.getMinutes(), //minute
					"s+" : this.getSeconds(), //second
					"q+" : Math.floor((this.getMonth()+3)/3), //quarter
					"S" : this.getMilliseconds() //millisecond
				}
				if(/(y+)/.test(format)) format=format.replace(RegExp.$1,
				(this.getFullYear()+"").substr(4- RegExp.$1.length));
				for(var k in o)if(new RegExp("("+ k +")").test(format))
				format = format.replace(RegExp.$1,
				RegExp.$1.length==1? o[k] :
				("00"+ o[k]).substr((""+ o[k]).length));
				return format;
			}
		},
		ThType: function(obj,column,val) {
			
			var that = this;
			try{
				var template_data = obj.attr("template_data")?$.parseJSON(obj.attr("template_data")):"";
			}catch(e){
				console.log('组件配置属性错误'+e.message);
				return "";
			}
			
			var template_key = obj.attr("template_key");
			var template_name = obj.attr("template_name");
			var template_class = obj.attr("template_class");
			var is_tj = obj.attr("is_stats");
			var html = "";
			var tjClass = " "+that.st.tj_input_class;
			var is_readonly = that.st.readonly||obj.attr("is_readonly");
			if(template_class==null||template_class==undefined)template_class="";
			switch (template_key) {
				case "serial":
					if(template_name==undefined){
						template_name = "into_table_serial";
					}
					html = "<span class='serial_number "+template_class+"'>#index#</span><input type='hidden' value='#index#' name='"+template_name+"' />";
					break;
				case "calc":
					html = '<div class="list_group_lay"><input type="text" readonly="" value="" class="list_input calc"></div>';
					break;
				case "input":
					if(is_tj)
					{
						template_class+=tjClass;
					}
					var up_index = 0;
					if(template_data!=null && template_data.bindlookupname!=null){
						$(that.st.table).find("thead:eq(0) th[template_key]").each(function(i){
							var this_data = "";
							try{
								this_data = $(this).attr("template_data")?$.parseJSON($(this).attr("template_data")):"";
							}catch(e){
							}
							if(this_data!=""&&template_data.bindlookupname==this_data.lookupname){
								up_index = i;
							}
						});
						
						var lookupname = that.st.table.replace("#","")+template_data.bindlookupname+"#index#"+up_index.toString();
						var upclass = lookupname+"."+template_data.upclass;
						
						template_class+=" "+upclass;
					}
					if(val!=""){
						val = "value='"+val+"'";
					}
					var readonly = "";
					if(typeof(template_data.formula) != "undefined"){
						template_class+=" calc";
						readonly = "";
					}
					if(is_readonly){
						is_readonly = 'readonly="readonly"';
					}else{
						is_readonly = '';
					}
					html = "<div class='list_group_lay'><input "+is_readonly+" type='text' "+readonly+" "+val+" name='"+template_name+"' class='list_input "+template_class+"' /></div>";
					if((template_data!=null) && (typeof(template_data.unitl) != "undefined")){
						
						html = "<div class='list_group_lay'><div class='list_public_lay'><input "+is_readonly+" type='text' "+readonly+" class='list_public_input list_unit_input "+template_class+"' name='"+template_name+"' /><span title='"+template_data.unitlname+"' class='list_icon_elm_unit'>"+template_data.unitlname+"</span></div></div>";
					}
					break;
				case "date":
					var up_index = 0;
					var bindlookup = null;
					try{
						var bindlookup = obj.attr("bindlookup")?$.parseJSON(obj.attr("bindlookup")):"";
					}catch(e){
						
					}
					//lookupname:绑定的lookup名称,name:绑定的字段名称
					if(bindlookup!=null && bindlookup.lookupname!=null && bindlookup.name!=null){
						$(that.st.table).find("thead:eq(0) th[template_key]").each(function(i){
							var this_data = "";
							try{
								this_data = $(this).attr("template_data")?$.parseJSON($(this).attr("template_data")):"";
							}catch(e){
							}
							if(this_data!=""&&bindlookup.lookupname==this_data.lookupname){
								up_index = i;
							}
						});
						
						var lookupname = that.st.table.replace("#","")+bindlookup.lookupname+"#index#"+up_index.toString();
						var upclass = lookupname+"."+bindlookup.name;
						
						template_class+=" "+upclass;
					}
					
					html = '<div class="list_group_lay">';
					if(val!=""){
						that.date_format();
						var timeint = parseInt(val+"000");
						if(that.isNumber(timeint)){
							val = new Date(timeint).format(template_data.format);
						}
						val = "value='"+val+"'";
					}
					var dateClass = " Wdate js-wdate ";
					if(is_readonly){
						is_readonly = 'readonly="readonly"';
						dateClass = "";
					}else{
						is_readonly = '';
					}
					//dateFmt为json 注意引号要合标准
					html += "<input "+is_readonly+" type='text' "+val+" format='{\"dateFmt\":\""+template_data.format+"\"}' class='"+dateClass+" list_input "+template_class+"'  name='"+template_name+"' />";
					html += '</div>';
					break;
				case "uploadfile":
					if((template_data!=null) && (template_data.callback != "")){
						callback = "onUploadSuccess='"+template_data.callback+"'";
					}
					html = '<div class="list_group_lay">';
					if(!is_readonly){
						html += "<input id='swf_up"+column+"_#index#' type=\"file\" name='"+template_name+"' uploader=\"true\" auto=\"true\" multi='false' "+callback+" queueSizeLimit='1'  formData=\"{ uploadpath:\'"+template_data.uploadpath+"\'}\"/>"+ 
						"<span id='swf_up"+column+"_#index#-queue' class='info uploadify-queue'></span>";
					}
					html += '</div>';
					break;
				case "uploadfilenew": //url:__URL__/DT_uploadnew
					html = '<div class="list_group_lay">';
					if(!is_readonly){
						html += '<div class="js_privyIndex">';
						html += '<a onclick="DTopenFile(this)" rel_name="'+template_name+'" rel_url="'+template_data.url+'" rel_index="#index#" id="DT_upload_'+column+'#index#" href="javascript:;" style="padding:3px 10px;" class="tml_task_btn" title="附件管理">附件管理(<span class="attached_count">0</span>)</a>';
						html += '</div>';
					}
					html += '</div>';
					break;
				case "lookup":
					var callback = "";
					var bindstr = "";
					var bindstr_hidden = "";
					
					var lookupname = that.st.table.replace("#","")+template_data.lookupname+"#index#"+column.toString();
					var upclass = lookupname+"."+template_data.upclass;
					template_class+=" "+upclass;
					
					var up_index = 0;
					var hidden_class = "";
					if(template_data!=null && template_data.bindlookupname!=null && template_data.bindlookupname!=""){ //其它lookup绑定到此lookup
						$(that.st.table).find("thead:eq(0) th[template_key]").each(function(i){
							var this_data = "";
							try{
								this_data = $(this).attr("template_data")?$.parseJSON($(this).attr("template_data")):"";
							}catch(e){
							}
							if(this_data!=""&&template_data.bindlookupname==this_data.lookupname){
								up_index = i;
							}
						});
						
						var bindlookupname = that.st.table.replace("#","")+template_data.bindlookupname+"#index#"+up_index.toString();
						var bindupclass = bindlookupname+"."+template_data.lporder;
						hidden_class = " "+bindlookupname+"."+template_data["hidden_data"][0]["lporder"];
						template_class+=" "+bindupclass;
					}
					if(that.isArray(template_data["hidden_data"])){
						var hidden_upclass = lookupname+"."+template_data["hidden_data"][0]["upclass"];
					}
					
					if((template_data!=null) && (template_data.callback != "")&& template_data.bindlookupname!=null&& template_data.bindlookupname!=""){
						callback = "callback='"+template_data.callback+"'";
					}
					if((template_data!=null) && (template_data.lpkey != "")&& template_data.bindlookupname!=null&& template_data.bindlookupname!=""){
						bindstr += "lporder='"+template_data.lporder+"' ";
						bindstr += "lpkey='"+template_data.lpkey+"' ";
						bindstr += "lpself='"+template_data.upclass+"' ";
						bindstr += "lpfor='"+hidden_upclass+"' ";
					}
					
					if((template_data!=null) && (template_data["hidden_data"]!= null)&& template_data.bindlookupname!=null && template_data.bindlookupname!=""){
						bindstr_hidden += "lporder='"+template_data["hidden_data"][0]["lporder"]+"' ";
						bindstr_hidden += "lpkey='"+template_data.lpkey+"' ";
						bindstr_hidden += "lpself='"+template_data["hidden_data"][0]["upclass"]+"' ";
						bindstr_hidden += "lpfor='"+upclass+"' ";
					}
					
					html =  '<div class="list_group_lay"><div class="list_public_lay">';
					html += "<input type='text' "+bindstr+" readonly class='list_public_input list_lookup_input readonly "+template_class+"' "+callback+"/>";
					
					html += "<input type='hidden' "+bindstr_hidden+" name='"+template_data["hidden_data"][0]["name"]+"' "+callback+" class='"+hidden_upclass+hidden_class+" "+obj.attr("template_class")+" '/>";
					
					var lookup_add_a = ' autocomplete="off" yuanorg="'+template_data.lookupname+'" bindlookupname="'+lookupname+'" lookupgroup="'+lookupname+'" href="'+template_data.href+'" param="'+template_data.param+'" ';
					var lookup_clear_a = 'onclick="clearOrg(\''+lookupname+'\');"';
					if(is_readonly){
						lookup_add_a = '';
						lookup_clear_a = '';
					}
					html += '<a '+lookup_add_a+' class="list_icon_elm list_mid_icon_elm icon-plus"></a>';
					html += '<a '+lookup_clear_a+' href="javascript:void(0);" class="list_icon_elm icon-trash" title="清空信息"></a>';
					html += '</div></div>';
					break;
				case "select":
					var readonly = "";
					if(is_readonly){
						is_readonly = 'readonly';
					}else{
						is_readonly = '';
					}
					
					var up_index = 0;
					var bindlookup = null;
					try{
						var bindlookup = obj.attr("bindlookup")?$.parseJSON(obj.attr("bindlookup")):"";
					}catch(e){
						
					}
					//lookupname:绑定的lookup名称,name:绑定的字段名称
					if(bindlookup!=null && bindlookup.lookupname!=null && bindlookup.name!=null){
						$(that.st.table).find("thead:eq(0) th[template_key]").each(function(i){
							var this_data = "";
							try{
								this_data = $(this).attr("template_data")?$.parseJSON($(this).attr("template_data")):"";
							}catch(e){
							}
							if(this_data!=""&&bindlookup.lookupname==this_data.lookupname){
								up_index = i;
							}
						});
						
						var lookupname = that.st.table.replace("#","")+bindlookup.lookupname+"#index#"+up_index.toString();
						var upclass = lookupname+"."+bindlookup.name;
						
						template_class+=" "+upclass;
					}
					
					var bindGroup = obj.attr("bindGroup");
					if(typeof(bindGroup) == "undefined"){
						bindGroup = "";
					}else{
						bindGroup = "bindGroup bindGroup"+column;
					}
					html = '<div class="list_group_lay '+bindGroup+'">';
					html += '<select name="'+template_name+'" class=" '+is_readonly+' list_select2 '+template_class+'">';
					var template_html = obj.attr("template_html");
					if(typeof(template_html) != "undefined"){
						html+=template_html;
					}else{
						if(that.isArray(template_data))
						{
							html+='<option value="">请选择</option>'
							for(var i = 0;i<template_data.length;i++)
							{
								var disabled = "";
								if(typeof(template_data[i].disabled) != "undefined")disabled = 'disabled="disabled"';
								if(typeof(template_data[i].template_html) != "undefined")disabled = 'disabled="disabled"';
								if(val!="" && template_data[i].value==val){
									val = "selected";
								}
								html+='<option '+disabled+' '+val+' value="'+template_data[i].value+'">'+template_data[i].name+'</option>';
							}
						}
					}
					html+="</select>";
					html += '</div>';
					break;
				case "selecttree":
					var up_index = 0;
					var oprateType = 'default';
					if(that.st.table_type){
						oprateType = that.st.table_type;
					}
					var template_controll = obj.attr("template_controll");
					var is_readonly	= obj.attr('is_readonly')=='true'?true:false;
					
					try{
						var base64Obj = new Base64();
						var treeid = that.st.table.replace("#","")+template_controll+column+'#index#'+oprateType;
						if(typeof(template_data.treedata) == 'object'){
							var treeJson = JSON.stringify(template_data.treedata);
						}else{
							var treeJson = '';
						}
						treeJson = htmlencode( treeJson );
						if(typeof(template_data.treeconfig) == 'object'){
							var treeConfig = JSON.stringify(template_data.treeconfig);
						}else{
							var treeConfig = '';
						}
						treeConfig = htmlencode( treeConfig );
						var cwidth = parseInt(template_data.treewidth);
						var cheight = parseInt(template_data.treeheight);
						
						var datawidth = cwidth ? cwidth : 150;
						var dataheight = cheight ? cheight : 150;
						
						var bindlookup = null;
						try{
							var bindlookup = obj.attr("bindlookup")?$.parseJSON(obj.attr("bindlookup")):"";
						}catch(e){
							
						}
						//lookupname:绑定的lookup名称,name:绑定的字段名称
						if(bindlookup!=null && bindlookup.lookupname!=null && bindlookup.name!=null){
							$(that.st.table).find("thead:eq(0) th[template_key]").each(function(i){
								var this_data = "";
								try{
									this_data = $(this).attr("template_data")?$.parseJSON($(this).attr("template_data")):"";
								}catch(e){
								}
								if(this_data!=""&&bindlookup.lookupname==this_data.lookupname){
									up_index = i;
								}
							});
							
							var lookupname = that.st.table.replace("#","")+bindlookup.lookupname+"#index#"+up_index.toString();
							var upclass = lookupname+"."+bindlookup.name;
							
							template_class+=" "+upclass;
						}
					
						//var treeConfig = '{&quot;expandAll&quot;:false, &quot;checkEnable&quot;:true, &quot;chkStyle&quot;:&quot;radio&quot;, &quot;radioType&quot;:&quot;all&quot;, &quot;onClick&quot;:&quot;S_NodeClick&quot;, &quot;onCheck&quot;:&quot;S_NodeCheck&quot;}';
						// console.log(treeConfig.length);
						// treeConfig = htmlencode( treeConfig );
						
						var comboxtreeCls ="comboxtree notreadonly";
						if(is_readonly==true){ //控制只读属性 --xyz 2015-08-20
							comboxtreeCls = "";
						}
						html = '<div class="list_group_lay">'
							+'<div class="list_input">'
			         		+'<input type="text" data-search="true" data-comboxtype="dt" data-width="'
							+datawidth+'" data-tree="#'
							+treeid+'" data-height="'
							+dataheight+'" data-names="'
							+template_name+'" value="" size="18" class="'+comboxtreeCls+' readonly list_input'
							+template_class+'" readonly="readonly" style="">'
			           		+'<input type="hidden" value="" name="'+template_name+'">'
			            	+'<ul id="'+treeid+'"nodes='+treeJson +' attrs='+treeConfig+' class="ztree hide" ></ul>'
	              			+'</div>';
						html += '</div>';
					}catch(e){
						console.log(e.message);
						html ="<error>配置错误!{e.message}</error>";
					}
					break;
				case "action":
					if(!is_readonly){
						template_class =  that.st.delRow + " " + template_class;
						if(typeof(template_data.table) == "undefined"){
							template_data.table = "table";
						}
						html = '<button type="button" del_url="'+template_data.del_url+'" del_table="'+template_data.post_table+'" del_id="0" class="'+template_class+' into_table_btn itb_del" title="删除"><span class="icon-remove"></span></button><input type="hidden" name="datatable[#index#]['+template_data.table+'][id]" value="0" />';
						if((template_data!=null) && (template_data.post_url != "") && (template_data.post_table != "") && (that.st.table_type=="edit")){
							var style = "display:block;";
							if(!that.st.show_save_btn)style="display:none;";
							html += '<button style="'+style+'" type="button" title="保存" class="save_row_btn into_table_btn" rel_type="save" post_url="'+template_data.post_url+'" post_table="'+template_data.post_table+'" post_id="'+template_data.post_id+'" /><span class="icon-save"></span></button>';
						}
					}
					break;
				case "orderno":
					html = '<div class="list_group_lay">';
					html += "<input type='text' readonly='readonly' name='"+template_name+"' class='list_input autoOrderno "+template_class+"' /></div>"
					html += '</div>';
					break;
				default :
					html = val;
					break;
			}
				
				return html;
		},
		uploadify_init:function(obj){
			if($.fn.uploadify && $.fn.Huploadify){
				var $this=$("input[type=file]",obj);
				var options={
			    	swf:$this.attr("swf")||TP_PUBLIC+"/Js/uploadify/scripts/uploadify.swf",
			    	uploader:TP_PUBLIC+"/Js/uploadify/uploadify.php",
			    	buttonText:'选择上传附件',
			        fileDataName:$this.attr("name")||"file",
			        queueID:$this.attr("id")+"-queue",
			        auto:$this.attr("auto")=='true'?true:false,//true,
					multi:$this.attr("multi")=='false'?false:true,//true,
			        width:$this.attr("width")||105,
			        height:$this.attr("height")||32,
			        buttonImage:$this.attr("buttonImage")||TP_PUBLIC+"/Js/uploadify/img/upload.png",
			        fileTypeDesc:$this.attr("fileTypeDesc")||"*.txt;*.jpg;*.jpeg;*.gif;*.png;*.doc;*.xls;*.csv;*.zip;*.pdf;*.xlsx;*.ppt;*.docx;*.rar;",
			        fileTypeExts:$this.attr("fileTypeExts")||"*.txt;*.jpg;*.jpeg;*.gif;*.png;*.doc;*.xls;*.csv;*.zip;*.pdf;*.xlsx;*.ppt;*.docx;*.rar;",
			        uploadLimit:$this.attr("uploadLimit")||"5",
			        queueSizeLimit:$this.attr("queueSizeLimit")||"5",
			        fileSizeLimit:$this.attr("fileSizeLimit")||"100MB",
			        onUploadComplete:onUploadComplete,
			        onUploadSuccess:onUploadSuccess,
			        upload_save_name : $this.attr("upload_save_name")?$this.attr("upload_save_name"):"",
			        onUploadError: uploadifyError,
			        /*以下仅Huploadify适用*/
			        breakPoints:false, //断点续传
			        saveInfoLocal:false,
			        previewImg:false,//预览上传图片
			        previewLoadimg:'', //预览前的载入图标
			        dragDrop:false,
			        showUploadedSize:true,
			        removeTimeout:2000000000,//指定的时间内，删除进度条
			        removeCompleted:true
			    };
				if($this.attr("onUploadSuccess")){
					options.onUploadSuccess=DWZ.jsonEval($this.attr("onUploadSuccess"));
				}
				if($this.attr("onUploadComplete")){
					options.onUploadComplete=DWZ.jsonEval($this.attr("onUploadComplete"));
				}
				if($this.attr("scriptData")){
					options.scriptData=DWZ.jsonEval($this.attr("scriptData"));
				}
				if($this.attr("formData")){
				    var f=DWZ.jsonEval($this.attr("formData"));
				    if(f.uploadpath) f.uploadpath=TP_PUBLIC+"/Uploadstemp/"+f.uploadpath;
				    options.formData=f;
				}
				if (window.FileReader) {
			        var $up = $('<div id="'+ $this.attr('id') +'"></div>');
			        $up.insertBefore($this).Huploadify(options);
			        $this.remove();
			    } else {
			        $this.uploadify(options);
			    }
			}
		},
		getTjCol:function(){
			var that = this;
			var tj = that.st.tongji;
			var html = "<tfoot><tr class='into_table_tjtr'>";
			
			for(var i=0;i<tj.length;i++)
			{
				if(tj[i]["is_stats"])
				{
					var $th = $(that.st.table).find("th[template_key]").eq(i);
					var template_data = "";
					try{
						template_data = $th.attr("template_data")?$.parseJSON($th.attr("template_data")):"";
					}catch(e){
					}
//					if((template_data!=null) && (typeof(template_data.unitlname) != "undefined")){
//						html += "<td>" +
//						"<div class='list_group_lay into_table_tj'>" +
//						"<div class='list_public_lay'>" +
//						"<label class='list_unit_label'><span title='小计'>小计：</span></label>" +
//						"<input readonly='readonly' decimals="+tj[i]["decimals"]+"  class='textInput list_public_input list_unit_total enterIndex into_table_tj_"+i+"'>" +
//						"<span title='"+template_data.unitlname+"' class='list_icon_elm_unit'>"+template_data.unitlname+"</span>"+
//						"</div>" +
//						"<div class='clear'></div>" +
//						"</div>" +
//						"<div class='list_group_lay into_table_all_tj'>" +
//						"<div class='list_public_lay'>" +
//						"<label class='list_unit_label'><span title='总计'>总计：</span></label>" +
//						"<input readonly='readonly' decimals="+tj[i]["decimals"]+"  class='textInput list_public_input list_unit_total enterIndex into_table_all_tj_"+i+"'>" +
//						"<span title='"+template_data.unitlname+"' class='list_icon_elm_unit'>"+template_data.unitlname+"</span>"+
//						"</div>" +
//						"<div class='clear'></div>" +
//						"</div>" +
//						"</td>";
//					}else{
//						html += "<td>" +
//						"<div class='list_group_lay into_table_tj'>" +
//						"<div class='list_public_lay'>" +
//						"<label class='list_unit_label'><span title='小计'>小计：</span></label>" +
//						"<input readonly='readonly' decimals="+tj[i]["decimals"]+"  class='textInput list_public_input list_unit_total enterIndex into_table_tj_"+i+"'>" +
//						"</div>" +
//						"</div>" +
//						"<div class='list_group_lay into_table_all_tj'>" +
//						"<div class='list_public_lay'>" +
//						"<label class='label_new'><span title='总计'>总计：</span></label>" +
//						"<input readonly='readonly' decimals="+tj[i]["decimals"]+"  class='textInput list_public_input list_unit_total enterIndex into_table_all_tj_"+i+"'>" +
//						"</div>" +
//						"</div>" +
//						"</td>";
//					}
					var unitlname = "";
					if((template_data!=null) && (typeof(template_data.unitlname) != "undefined")){
						unitlname = template_data.unitlname;
					}
					html += "<td>" +
							"<div class='into_table_tj'>小计：<span class='into_table_tj_"+i+"' decimals='"+tj[i]["decimals"]+"'></span>"+unitlname+"<div class='clear'></div></div>" +
							"<div class='into_table_all_tj'>总计：<span class='into_table_all_tj_"+i+"' decimals='"+tj[i]["decimals"]+"'></span>"+unitlname+"<div class='clear'></div></div>" +
							"</td>";
				}
				else
				{
					html += "<td></td>";
				}
			}
			html += "</tr></tfoot>";
			console.log(html);
			return html;
		},
		getTj:function(this_num,index,decimals,bindhz) {
			var that = this;
			if(that.st.is_stats==false||that.st.tongji.length==0)return;
			var tb = that.st.table;
			var sum = parseFloat($(tb).find(".into_table_tj_"+index).html());
			var all_sum = parseFloat($(tb).find(".into_table_all_tj_"+index).html());
			if(!that.isNumber(this_num))
			{
				this_num = 0.00;
			}
			
			if(!that.isNumber(sum))
			{
				sum = 0.00;	
			}
			
			if(!that.isNumber(all_sum))
			{
				all_sum = 0.00;	
			}
			
			sum += this_num;
			all_sum += this_num;
			$(tb).find(".into_table_tj_"+index).html(parseFloat(sum.toFixed(decimals)));
			$(tb).find(".into_table_all_tj_"+index).html(parseFloat(all_sum.toFixed(decimals)));
			$(tb).parents("form").find("[name='"+bindhz+"']").val(parseFloat(all_sum.toFixed(decimals)));
		},
		getTjAll:function(index) {
			var that = this;
			var tj = that.st.tongji;
			var tb = that.st.table;
			if(that.st.is_stats==false||tj.length==0)return;
			for(var i=0;i<tj.length;i++)
			{
				if(tj[i]["is_stats"])
				{
					var this_num = parseFloat($(tb).find("tbody tr").eq(index).find("td").eq(i).find("input").val());
					
					if(!that.isNumber(this_num))
					{
						this_num = 0.00;
					}
					else
					{
						this_num = -this_num;	
					}
					var bindhz = $(that.st.table).find("th[template_key]").eq(i).attr("bindhz");
					that.getTj(this_num,i,tj[i]["decimals"],bindhz);
				}
			}
		},
		get_obj_name:function() { 
			var that = this;
			var new_tr = $(that.st.table).find("tbody tr");
			for(var i =0;i<new_tr.length;i++)
			{
				for(var j =0;j<$(that.st.table).find("tbody tr").eq(i).find("td").length;j++)
				{
					var type = $(that.st.table).find("th[template_key]").eq(j).attr("template_key");
					var template_name = $(that.st.table).find("th[template_key]").eq(j).attr("template_name");
					var obj_name = "";
					var obj = "";
					if(template_name)
					{
						obj_name = template_name;
					}
					else
					{
						obj_name = "into_table_"+type;
					}
					switch (type) {
						case "serial":
							type = "input";
							new_tr.eq(i).find("td").eq(j).find(".serial_number").html(i+1);
							new_tr.eq(i).find("td").eq(j).find(type).val(i+1);
							break;
						default :
							break;
					}
					if(obj_name!="")
					{
						new_tr.eq(i).find("td").eq(j).html().replace("#index#",i+1);
					}
				}
			}
		},
		js_strto_time:function(str_time){
		    var new_str = str_time.replace(/:/g,'-');
		    new_str = new_str.replace(/ /g,'-');
		    var arr = new_str.split("-");
		    var datum = new Date(Date.UTC(arr[0],arr[1]-1,arr[2],arr[3]-8,arr[4],arr[5]));
		    return datum.getTime()/1000;
		},
		rowCalc:function(inputObjArr,formula,stats_num,type,jindu){ //行组件计算     inputObjArr:需要计算的对象    formula:计算公式    stats_num:保留小数  type:公式类型 jindu:日期计算精度
			var that = this;
			var isEmpty = true;
			var calcType = parseInt(type);
			switch (calcType) {
				case 1: //普通加减乘除
					for(var i=0; i<inputObjArr.length; i++){
						var value = parseFloat(inputObjArr[i]["obj"].val());
						var index = inputObjArr[i]["index"];
						if(!that.isNumber(value)){
							value = 0;
						}
						if(value!=""){
							isEmpty = false;
						}
						
						formula = formula.replace(index,value);
					}
					var sum = 0.00;
					if(!that.isNumber(parseFloat(stats_num))){
						stats_num = 2;
					}
					try{
						sum = eval(formula).toFixed(stats_num);
						if(sum<=0 && isEmpty)sum="";
					}catch(e){
						sum = "";
					}
					break;
				case 2: //两日期差
					for(var i=0; i<inputObjArr.length; i++){
						var value = inputObjArr[i]["obj"].val();
						var index = inputObjArr[i]["index"];
						formula = formula.replace(index,"###"+value+"###");
					}
					var strs= new Array(); //定义一数组
					strs = formula.split("###-###"); //字符分割
					var s1 = new Date(strs[0].replace("###",""));
					var s2 = new Date(strs[1].replace("###",""));
					if(!jindu){
						jindu = "d";
					}
					try{
						sum = s1.DateDiff(jindu,s1,s2);
					}catch(e){
						sum = "";
					}
					break;
				case 3: //日期增减
					for(var i=0; i<inputObjArr.length; i++){
						var value = inputObjArr[i]["obj"].val();
						var index = inputObjArr[i]["index"];
						formula = formula.replace(index,"###"+value+"###");
					}
					var strs= new Array(); //定义一数组
					if(formula.indexOf("###-")!=-1){
						strs = formula.split("###-"); //字符分割
						var s1 = new Date(strs[0].replace("###",""));
						var s2 = -parseInt(strs[1].replace("###",""));
					}else{
						strs = formula.split("###+"); //字符分割
						var s1 = new Date(strs[0].replace("###",""));
						var s2 = parseInt(strs[1].replace("###",""));
					}
					if(!jindu){
						jindu = "d";
					}
					try{
						sum = s1.DateAdd(jindu,s2,s1);
						sum = sum.Format("YYYY-MM-DD");
					}catch(e){
						sum = "";
					}
					break;
				case 4: //字符连接
					for(var i=0; i<inputObjArr.length; i++){
						var value = inputObjArr[i]["obj"].val();
						var index = inputObjArr[i]["index"];
						formula = formula.replace(index,'"'+value+'"');
					}
					sum = eval(formula);
					break;
			}
			return sum;
		},
		get_add_obj_name:function(index,obj_new_tr,isInit) { //tr索引,新tr,是否初始化祖件
			var that = this;
			var i = index;
			var new_tr = obj_new_tr;
			if(index==-1){
				i = parseInt($.trim(new_tr.find(".serial_number").html()))-1;
			}
			if(new_tr.find(".serial_number").length)new_tr.find(".serial_number").html(new_tr.find(".serial_number").html().replace(/#index#/g,i+1));
			new_tr.html(new_tr.html().replace(/#index#/g,that.st.tr_max));
			var formulaArr = new Array(); //行公式数组
//			$(that.st.table).find("thead th[template_key='serial']").width(24);//序号列宽度
//			$(that.st.table).find("thead th[template_key='action']").width(100);//操作列宽度
			for(var j =0;j<new_tr.find("td").length;j++)
			{
				var template_data = "";
				try{
					template_data = $.parseJSON($(that.st.table).find("th[template_key]").eq(j).attr("template_data"));
				}catch(e){
				}
				var type = $(that.st.table).find("th[template_key]").eq(j).attr("template_key");
				if(that.st.initTdWidth){
					var td_width = $(that.st.table).find("thead th").eq(j).width()+36;
//					new_tr.find("td").eq(j).find('.list_group_lay').css("width",parseInt(td_width)+"px");
				}
				var is_hide = $(that.st.table).find("th[template_key]").eq(j).hasClass("hide");
				if(is_hide){
					new_tr.find("td").eq(j).addClass("hide");
				}
				var bindGroup = $(that.st.table).find("th[template_key]").eq(j).attr("bindGroup");
				switch (type) {
					case "input":
						var formulaType = 1;
						var formulaJindu = "d";
						try{
							formulaType = template_data.formulaType;
							formulaJindu = template_data.formulaJindu;
						}catch(e){
						}
						if((template_data!=null) && (typeof(template_data.formula) != "undefined") && template_data.formula!=null){
							try{
								formulaArr.push({obj:new_tr.find("td").eq(j).find("input.calc"),formula:template_data.formula,listObj:template_data.formula.match(/\[.\w+\]/g),stats_num:template_data.stats_num,type:formulaType,jindu:formulaJindu});
							}catch(e){
							}
						}
						if(new_tr.find("td").eq(j).find("input").attr("readonly") && !new_tr.find("td").eq(j).find("input").hasClass("readonly")){
							new_tr.find("td").eq(j).find("input").addClass("readonly");
						}
						break;
					case "uploadfile":
						if(isInit){
							if($.fn.uploadify)that.uploadify_init(new_tr.find("td").eq(j));
						}else{
							new_tr.find("td").eq(j).find(".list_group_lay").addClass("initUpload");
							var td_html = "";
							td_html += '<a class="btn btn-default btn-sm Huploadify-button" href="javascript:void(0)">选择上传附件</a>';
							new_tr.find("td").eq(j).find("input").hide();
							new_tr.find("td").eq(j).find(".list_group_lay>div:first").before(td_html);
						}
						break;
					case "orderno":
						if(template_data){
							var getUrl = template_data.url?template_data.url:"";
							var autoOrdernoObj = new_tr.find("td").eq(j).find("input.autoOrderno");
							$.ajax({
								url:getUrl,
								type:'post',
								success:function(data){
									autoOrdernoObj.val(data);
								}
							});
						}
						break;
					case 'selecttree':
						// 下拉树代码处理 add by nbmxkj 20150331 1119
				        var content = new_tr.find("td").eq(j);
				        if($.fn.comboxtree)$("input.comboxtree", content ).comboxtree();
				        // 下拉树 end
				        
						break;
					case "select":
						var this_select=new_tr.find("td").eq(j).find('select');
						if(isInit){
							if(that.st.initTdWidth){
								//this_select.css("width",parseInt(td_width*0.8)+"px");
							}
							if(new_tr.find("td").eq(j).find('.list_group_lay').css("display")!="none"&&!this_select.hasClass("select2")){
								if(this_select.hasClass("readonly") || this_select.attr("readonly")){
									this_select.select2("destroy").select2().attr("readonly",true);
								}else{
									this_select.select2("destroy").select2();
								}
							}
						}else{
							new_tr.find("td").eq(j).find(".list_group_lay").addClass("initSelect2");
							var select_text = this_select.find("option:selected").text();
							var td_html = "";
							td_html += '<div class="select2-container list_select2 enterIndex">';
							td_html += '<a tabindex="-1" class="select2-choice" href="javascript:void(0)">';
							td_html += '<span class="select2-chosen" id="select2-chosen-4">'+select_text+'</span>';
							td_html += '<abbr class="select2-search-choice-close"></abbr>';
							td_html += '<span role="presentation"class="select2-arrow"><b role="presentation"></b></span>';
							td_html += '</a>';
							td_html += '</div>';
							this_select.hide();
							this_select.before(td_html);
						}
						if(typeof(bindGroup) != "undefined" && !new_tr.find("td").eq(j).find('div.list_group_lay').hasClass("bindGroup")){
							new_tr.find("td").eq(j).find('div.list_group_lay').addClass("bindGroup bindGroup"+j);
						}
						this_select.change(function(){ //是和否分组
							var index = $(this).parents("tr").find("td").index($(this).parents("td"));
							var bindgroup = $(that.st.table).find("th[template_key]").eq(index).attr("bindGroup");
							var form = $(this).parents("form");
							$(this).parents(".list_group_lay").attr("groupValue",$(this).val());
							var groupValue = 0;
							$(that.st.table).find(".bindGroup"+index).each(function(){
								var val = $(this).attr("groupValue");
								var int = 0;
								if(val=="否"){
									int = 0;
								}else if(val=="是"){
									int = 1;
								}
								groupValue = groupValue || int;
							});
							if(groupValue){
								form.find("[name='"+bindgroup+"']").val("是").change();
							}else{
								form.find("[name='"+bindgroup+"']").val("否").change();
							}
						});
						if(that.st.isSelectChange){
							this_select.change();
						}
						break;
					case "lookup":
						$("textarea.datatabe_data_to_cell").die("change");
						$("textarea.datatabe_data_to_cell").live("change",function(){
							try{
								var val = $(this).val()?$.parseJSON($(this).val()):"";
							}catch(e){
								console.log(e.message);
								return "";
							}
							var td_index = $(this).parents("tr").find("td").index($(this).parents("td"));
							if(typeof(val) == 'object'){
								var arr = $.json2arr(val);
								that.lookupByAddRow(arr,td_index);
							}
						});

						if(isInit){
							new_tr.find("td").eq(j).find("a[lookupgroup]").mouseover(function(){
								var isInit = $(this).attr("isInit");
								if(isInit==undefined){
									$(this).attr("isInit",1);
									$(this).parents("td").initUI();
								}
							});
						}
						//附加条件
						if((template_data!=null) && (typeof(template_data.param) != "undefined") && (typeof(template_data.condition) != "undefined")){
							var newconditions = template_data.param.substr(template_data.param.indexOf("newconditions=")+14);
							if(newconditions!=undefined){
								new_tr.find("td").eq(j).find("a[lookupgroup]").mouseover(function(){
									// modify by nbmxkj@20150531 2045 属性不存在时置为空。
									var ajax_post_url = typeof(that.st.ajax_post_url)=='undefined' ? '' : that.st.ajax_post_url;
									var template_data = "";
									
									var tr = ajax_post_url==""?$(this).parents("form"):$(this).parents("tr");
									var index = $(this).parents("tr").find("td").index($(this).parents("td"));
									try{
										template_data = $.parseJSON($(that.st.table).find("th[template_key]").eq(index).attr("template_data"));
									}catch(e){
									}
									var list = new Array();
									var condition = template_data.condition;
									var param = template_data.param;
									var index1 = param.length;
									var index2 = template_data.param.indexOf("newconditions=")+14;
									if(index1!=index2){
										var newconditions = template_data.param.substr(template_data.param.indexOf("newconditions=")+14);
										list = newconditions.replace(/=\'.*?\'/g,"").split(" and ");
										var conditions = new Array();
										var conditions_str = "";
										if(condition != null){
											for(var key in list){
												var obj =  ajax_post_url==""?tr.find("[name='"+condition[list[key]]+"']"):tr.find("[name*='["+condition[list[key]]+"]']");
												var vals = obj.val();
												var this_obj_val = obj.val() ? obj.val():'';
												if(this_obj_val!=""){
													//升级lookup外部条件，以前是’=‘ 2015-10-23修改为 in 
													conditions.push(list[key]+" in ('"+this_obj_val+"')");
												}
											}
										}
										if(conditions.length>0){
											conditions_str = conditions.join(" and ");
										}
										param = param.replace(/newconditions=.*/,"newconditions="+conditions_str);
										$(this).attr("param",param);
									}
								});
							}
						}
						break;
					case "date":
						if(isInit){
							new_tr.find("td").eq(j).mouseover(function(){
								var isInit = $(this).find("input").attr("isInit");
								if(isInit==undefined){
									$(this).find("input").attr("isInit",1);
									$(this).find("input.js-wdate").on("click",function(){
										var $this = $(this);
										var format = $this.attr("format");
										var json = {el:$this[0]};
										if(format){
											var format = eval('('+format+')');
											json = $.extend(format, json);
										}
										// 扩展格式化2
										var data = $this.attr("data");
										if(data){
											var data = eval('('+data+')');
											json = $.extend(data, json);
										}
										WdatePicker(json);
									});
								}
							});
						}
						break;
					default :
						break;
				}
			}
			if(formulaArr.length){  //行计算绑定
				var calcObjArr = new Array();
				for(var i=0; i<formulaArr.length;i++){
					var arr = formulaArr[i]["listObj"];
					for(var j=0; j<arr.length;j++){
						calcObjArr.push(arr[j]);
					}
				}
				var n = {},calcObjArrNew=[]; //n为hash表，calcObjArrNew为新数组
				for(var i = 0; i < calcObjArr.length; i++) //遍历当前数组
				{
					if (!n[calcObjArr[i]]) //如果hash表中没有当前项
					{
						n[calcObjArr[i]] = true; //存入hash表
						calcObjArrNew.push(calcObjArr[i]); //把当前数组的当前项push到新数组里面
					}
				}
				for(var i = 0; i < calcObjArrNew.length; i++){
					new_tr.find("input[name*='"+calcObjArrNew[i]+"']").on('change keyup',function(){
						for(var i=0; i<formulaArr.length;i++){
							var this_obj = formulaArr[i]["obj"];
							var inputArrObj = new Array();
							for(var j=0;j<formulaArr[i]["listObj"].length;j++){
								inputArrObj.push({obj:new_tr.find("input[name*='"+formulaArr[i]["listObj"][j]+"']"),index:formulaArr[i]["listObj"][j]});
							}
							var sum = that.rowCalc(inputArrObj,formulaArr[i]["formula"],formulaArr[i]["stats_num"],formulaArr[i]["type"],formulaArr[i]["jindu"]);
							if( ! that.isNumber(parseFloat(sum))){
								sum = "";
							}
							this_obj.val(sum).change();
						}
					});
					new_tr.find("input[name*='"+calcObjArrNew[i]+"']").change();
				}
			}
			that.st.tr_max++;
			if(typeof baohoubind === 'function' && $(that.st.table).parents("#MisAutoDeb_edit,#MisAutoDeb_add").length!=0){
				//baohoubind(new_tr);
			}
			
			//费用报销审批表专用
			if($(that.st.table).parents("#MisAutoEzb_edit,#MisAutoEzb_add,#MisAutoEzb_auditEdit").length!=0){
				$("#MisAutoEzb_edit tbody,#MisAutoEzb_add tbody,#MisAutoEzb_auditEdit tbody").find(new_tr).find("[name*='[bumen]']").on("change",function(){
					var deptid = $(this).val();
					var obj = $(this).parents("tr").find("[name*='[yusuanxiangmu]']").siblings("a.icon-plus");
					var param = obj.attr("param");
					var index = param.indexOf("newconditions=")+14;
					obj.attr("param",param.substr(0,index)+"deptid in ('"+deptid+"')");
				});
				$("#MisAutoEzb_edit tbody,#MisAutoEzb_add tbody,#MisAutoEzb_auditEdit tbody").find(new_tr).find("[name*='[bumen]']").change();
			}
		},
		inArray:function (str, arr) {
			if(typeof str == 'string') {
				for(var i in arr) {
					if(arr[i] == str) {
						return true;
					}
				}
			}
			return false;
		},
		delRow:function(obj){
			var that = this;
			var this_obj_a = $(obj);
			if(this_obj_a.attr("del_id")!="0"){
				var del_id = $(obj).attr("del_id");
				var del_url = $(obj).attr("del_url");
				var del_table = $(obj).attr("del_table");
				alertMsg.confirm("确认移除该数据吗？", {
					okCall: function(){
						$.ajax({
							url:del_url,
							data:{'table':del_table,'id':del_id},
							type:'post',
							dataType:'json',
							success:function(data){
								if(data && data.status==1){
									var delIndex = $(that.st.table).find("tbody tr").index($(obj).parents("tr"));
									that.getTjAll(delIndex);
									that.st.tableObj.row(this_obj_a.parents("tr")).remove().draw(false);
									that.reloadSerial();
									$(that.st.table).find(".dataTables_scrollBody tbody tr:eq(0) .bindGroup select").change();//删除时执行绑定联动
									if( typeof dt_delrow_after === 'function' ){
										dt_delrow_after(obj);
									}
									if(that.st.mini_set_orderno){
										reschange();
									}
								}else{
									if(data){
										alertMsg.error(data.message);
									}else{
										alertMsg.error("删除失败");
									}
									
								}
							}
						});
					}
				});
			}else{
				var delIndex = $(that.st.table).find("tbody tr").index($(obj).parents("tr"));
				that.getTjAll(delIndex);
				that.st.tableObj.row($(obj).parents("tr")).remove().draw(false);
				that.reloadSerial();
				$(that.st.table).find(".dataTables_scrollBody tbody tr:eq(0) .bindGroup select").change();//删除时执行绑定联动
				if( typeof dt_delrow_after === 'function' ){
					dt_delrow_after(obj);
				}
			}
		},
		reloadSerial:function(){ //重置序号
			var that = this;
			var template_key = $(that.st.table).find("thead:eq(0) th:eq(0)").attr("template_key");
			if(template_key=="serial"){
				that.st.tableObj.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
					var html = "<span class='serial_number'>#index#</span><input type='hidden' name='into_table_serial' value='#index#' />";
					var index = i+1;
					$(that.st.tableObj.rows().nodes()).eq(i).find("td:eq(0)").html(html.replace(/#index#/g,index));
				});
			}
		},
		addRow:function(rows){
			var that = this;
			if(!that.isNumber(rows))
			{
				rows = 1;
			}
			if(rows>that.st.max_add_row_number)
			{
				alert("最多一次添加"+that.st.max_add_row_number+"行！");
				return;
			}
			that.st.template_tr = [];
			if(that.st.template_tr=="")
			{
				$(that.st.table).find("thead:eq(0) th").each(function(i){
					that.st.template_tr.push(that.ThType($(this),i,""));
				});
			}
			for(var i = 0;i<rows;i++){
				that.st.obj.fnAddData(that.st.template_tr);
				var rowsCount = 1;
				if(that.st.tableObj.row()[0]!=undefined){
					rowsCount = that.st.tableObj.row()[0].length;
				}
				var obj = $(that.st.tableObj.rows().nodes(rowsCount-1)).eq(rowsCount-1);
				that.get_add_obj_name(rowsCount-1,obj,true);
				obj.addClass("add_new");
				try{
					if(that.st.mini_set_orderno){
						if(that.st.mini_set_orderno["writable"]){
							obj.find("input[name*='[orderno]']").attr("readonly",false);
						}
						obj.addClass("is_not_save");
					}
				}catch(err){
					
				}
				try{
					if(that.st.add_default_val){ //如果有新增默认值
						for(var key in that.st.add_default_val) { 
							if(key!="" || key !=null || key != undefined){
								if(that.isObject(that.st.add_default_val[key]) && that.st.add_default_val[key]!=null && that.st.add_default_val[key]!=undefined){
									obj.find("[name*='["+key+"]']").val(that.st.add_default_val[key]["value"]);
									obj.find("[name*='["+key+"]']").siblings("input").val(that.st.add_default_val[key]["showname"]);
								}else{
									obj.find("[name*='["+key+"]']").val(that.st.add_default_val[key]);
								}
							}
					    }
					}
				}catch(err){
					console.log(err);
				}
			}
		},
		getTjColumnAll:function(index,data,type) {
			var that = this;
			var tj = that.st.tongji;
			var tb = that.st.table;
			if(that.st.is_stats==false||tj.length==0)return;
			
			var obj = $(data);
			var sum = 0.00;
			var tj_class = ".into_table_all_tj_"+index;
			if(type=="page"){
				obj = $(tb).find("tbody tr");
				tj_class = ".into_table_tj_"+index;
			}
			
			obj.each(function(){
				var this_obj = $(this).find("td").eq(index);
				var y_num = parseFloat($(this_obj).find("."+that.st.tj_input_class).attr("y_num"));
				if(!that.isNumber(y_num))
				{
					y_num = 0.00;
				}
				sum += y_num;
			});
			if(that.st.__id==1){ //初始化时第一次统计
				tb+="_wrapper";
			}
			$(tb).find(tj_class).html(parseFloat(sum.toFixed(tj[index]["decimals"])));
		},
		tjInit:function(data){
			var that = this;
			if(that.st.is_stats){
				$(that.st.table).find("."+that.st.tj_input_class).die();
				$(that.st.table).find("."+that.st.tj_input_class).live("change keyup",function(){
					var tj = that.st.tongji;
					var index = $(this).parents("tr").eq(0).find("td").index($(this).parents("td"));
					var bindhz = $(that.st.table).find("thead:eq(0) th[template_key]").eq(index).attr("bindhz");
					var this_num = 0;
					this_num = parseFloat($(this).val());
					
					var y_num = parseFloat($(this).attr("y_num"));
					if(!that.isNumber(this_num))
					{
						this_num = 0.00;	
					}
					$(this).attr("y_num",this_num);
					if(that.isNumber(y_num))
					{
						this_num -= y_num;	
					}
					that.getTj(this_num,index,tj[index]["decimals"],bindhz);
				});
				var page_fy = parseInt($(that.st.table).find(".dataTables_length select").val());
				
				var rows = parseInt(data.length);
				
				var page_count = 0;
				if(that.isNumber(page_fy)){
					page_count = rows/page_fy;
				}
				if(page_count<=1){
					if(that.st.__id==1){
						$(that.st.table+"_wrapper").find(".into_table_all_tj").hide();
					}else{
						$(that.st.table).find(".into_table_all_tj").hide();
					}
					
				}else{
					$(that.st.table).find(".into_table_all_tj").show();
				}
				for(var key in that.st.tongji){
					if((typeof(that.st.tongji[key]["is_stats"]) != "undefined")&&that.st.tongji[key]["is_stats"]){
						that.getTjColumnAll(key,data,"page");
						that.getTjColumnAll(key,data,"all");
					}
				}
			}
		},
		saveOk:function(this_obj){
			var that = this;
			var this_data = that.st.obj.$('input[name^="datatable["], select[name^="datatable["]');
			var is_return = false;
			this_data.each(function(){
				if($(this).hasClass("required")&&$(this).val()==""){
					is_return = true;
					alertMsg.error("提交数据不完整！");
				}
			});
			if(is_return){
				return false;
			}
			var post_data = this_data.serialize();
			if(post_data=="")return false;
			$.ajax({
				url:that.st.ajax_post_url,
				data:post_data,
				type:'post',
				dataType:'json',
				success:function(data){
					var re_data = data.data;
					if(data.status){
						if(re_data){
							for(var i=0;i<re_data.length;i++){
								$("input[name='datatable["+re_data[i]['index']+"]["+re_data[i]['table']+"][id]']").val(re_data[i]["id"]);
							}
						}
					}else{
						alertMsg.error(data.message);
					}
					if(that.st.isReloadNavTab){
						navTab.reload();
					}else{
						reschange();
					}
				}
			});
		},
		bothRowHtml:function(){
			var that = this;
			$(that.st.table).find(".top").append('<div class="save_btn_div"><span class="save_btn_span"><a href="javascript:;" class="both_save_btn tml-btn tml_look_btn tml_mp">保存</a></span></div>');
			$(that.st.table).find(".both_save_btn").live( 'click', function () {
				alertMsg.confirm("确定要保存?",{okCall:function(){
					that.saveOk(this);
				}});
			});
		},
		fnDestroy:function(){
			var that = this;
			that.st.obj.fnDestroy();
		},
		reload:function(){
			var that = this;
			that.fnDestroy();
			initTableWNEWOne(that.st.tableId);
		},
		addRowHtml:function(){
			var that = this;
			var html = "";
			
			if(that.st.mini_add_url=="" ){
				if(that.st.add_col_input){
					html = '<div class="add_btn_div"><span class="add_btn_span"><input type="text" class="'+that.st.add_col_input+'"><a name="'+that.st.tableId+'"></a><a href="javascript:;" class="tml-btn tml_look_btn tml_mp '+that.st.addRow+'">新增行</a></span></div>';
				}else{
					html = '<div class="add_btn_div"><span class="add_btn_span"><a name="'+that.st.tableId+'"></a><a href="javascript:;" class="tml-btn tml_look_btn tml_mp '+that.st.addRow+'">新增行</a></span></div>';
				}
			}else{
				var target = that.st.mini_add_type?that.st.mini_add_type:"navTab";
				html = '<div class="add_btn_div"><span class="add_btn_span"><a href="'+that.st.mini_add_url+'" rel="miniadd" width="980" height="480"    mask="true" target="'+target+'" class="tml-btn tml_look_btn tml_mp">新增</a></span></div>';
			}
			if(that.st.formModel!="" && that.st.datatableModel!="" && that.st.importUrl!=""){
				html += '<div class="add_btn_div"><span class="add_btn_span"><a mask="true" href="'+that.st.importUrl+'/model/'+that.st.formModel+'/datatable/'+that.st.datatableModel+'/tableID/'+that.st.tableId.replace("#","")+'" rel="neiqianbiaoimport" target="dialog" width="640" height="227" class="tml-btn tml_look_btn tml_mp">导入</a></span></div>';
			}
			$(that.st.table).find(".top").append(html);
		},
		isArray : function(v){ return Object.prototype.toString.apply(v) === '[object Array]';},
		isNumber : function(o) { return typeof o === 'number' && isFinite(o); },
		isObject : function isObject(o){return typeof(o)=="object";},
		init:function(){
			var $ = jQuery;
			var that = this;
			if(!that.st.isReLoad){
				if(that.st.__id==0){
					if(that.st.table_type!="add"){
						that.st.searching = true;
					}
					
					var table_data = "";
					try{
						table_data = $(that.st.table).attr("table_data")?$.parseJSON($(that.st.table).attr("table_data")):"";
					}catch(e){
					}
					if(table_data!=""){
						for(var k1 in table_data){
							for(var k2 in that.st){
								if(k1==k2){
									that.st[k2]=table_data[k1];
								}
							}
						}
					}
					
					var table_type = $(this.st.table).attr("table_type");
					if(table_type!=undefined){
						that.st.table_type=table_type;
					}
					var parent_div = $(that.st.table).parent();//上一级div容器
					var width = parseInt(parent_div.width()) * 1;
					var height = parent_div.height();
//					that.st.scrollX = width+"px";
					if(this.st.template_tr=="")
					{
						$(this.st.table).find("thead:eq(0) th").each(function(i){
							that.st.table_th.push($(this).html());
							that.st.template_tr.push(that.ThType($(this),i,""));
							var tjarr = {};
							tjarr.is_stats=$(this).attr("is_stats")?true:false;
							tjarr.decimals=that.isNumber(parseInt($(this).attr("stats_num")))?parseInt($(this).attr("stats_num")):2;
							that.st.tongji.push(tjarr);
						});
					}
					that.st.table_th_index = [];
					$(that.st.table).find("th[template_key]").each(function(o){
						if($(this).attr("is_stats")){
							that.st.is_stats = true;
						}
						that.st.table_th_index.push(o);
					});
					if(that.st.is_stats){
						$(that.st.table).append(that.getTjCol());
					}
					that.list();//初始化DataTable
					var time = new that.timerRecord();
					time.begin();
					if(that.st.tableObj && that.st.re_id==0){
						if(that.st.tableObj.row()[0]!=undefined){
							var rows_Count = that.st.tableObj.row()[0].length;
							for(var i=0;i<rows_Count;i++){
								that.get_add_obj_name(i,$(that.st.tableObj.rows().nodes(i)).eq(i),false);
							}
						}
					}
					time.end();
					
					if(!that.st.isYMInfo){
						$(that.st.table).find(".bottom .dataTables_info").hide();
					}
					
					if(that.st.table_type=="add" && $(that.st.table).find(".save_row_btn").length>0){
						$(that.st.table).find(".save_row_btn").each(function(){
							$(this).click();
						});
					}
					
//					if(typeof initUI === 'function')$(that.st.table).find("a[target=dialog],a[target=navTab]").initUI();//a标签事件绑定
					$(that.st.table).find("a[target=dialog]").die("click");
					$(that.st.table).find("a[target=dialog]").live("click",function(event){
						var $this=$(this);
						//灰色按钮不可点
						if($this.hasClass("disabled")){
							return false;
						}
						/** 杨东修改 单据明细新增时保存单头 开始*/
						var model = $this.attr("m");
						if(model != undefined){
							var m = $("#"+model+"docheadform")[0];
							return validateCallback(m, function(json){
								var refreshtabs = json.refreshtabs;
								if(json.statusCode==DWZ.statusCode.ok){
									if(json.navTabId){
										navTab.reloadFlag(json.navTabId);
									}else{
										if(json.refreshtabs&&json.refreshtabs.data!=null){
											var t = json.refreshtabs.data;
											var d=t;
										}else{
											var d="";
										}
										navTabPageBreak({realnavTab:true,refreshtabs:d},json.rel);
									}
									var masid = json.data;
									$this.attr("href",$this.attr("href")+masid);
									// 黎明刚  在这里加了一个参数 refershtabsStep 判断从何处过来的新增
									var urls = TP_APP + "/" + model + "/edit/id/"+json.data+"/refershtabsStep/1";
									$(".navTab-tab").find("li.selected").attr("url",urls);
									//$(".navTab-tab").find("li.selected").attr("tabid",model+"edit");
									navTab.reload(urls);
									var title=$this.attr("title")||$this.text();
									var rel=$this.attr("rel")||"_blank";
									var options={};
									var w=$this.attr("width");
									var h=$this.attr("height");
									if(w)options.width=w;
									if(h)options.height=h;
									options.max=eval($this.attr("max")||"false");
									options.mask=eval($this.attr("mask")||"false");
									options.maxable=eval($this.attr("maxable")||"true");
									options.minable=eval($this.attr("minable")||"true");
									options.fresh=eval($this.attr("fresh")||"true");
									options.resizable=eval($this.attr("resizable")||"true");
									options.drawable=eval($this.attr("drawable")||"true");
									options.close=eval($this.attr("close")||"");
									options.param=$this.attr("param")||"";
									var url=unescape($this.attr("href")).replaceTmById($(event.target).parents(".unitBox:first"));
									DWZ.debug(url);
									if(!url.isFinishedTm()){
									alertMsg.error($this.attr("warn")||DWZ.msg("alertSelectMsg"));
									return false;}
									$.pdialog.open(url,rel,title,options);
									return false;
								} else {
									DWZ.ajaxDone(json);
									return false;
								}
							});
						}
						/** 杨东修改 单据明细新增时保存单头 结束*/
						var title=$this.attr("title")||$this.text();
						var rel=$this.attr("rel")||"_blank";
						var options={};
						var w=$this.attr("width");
						var h=$this.attr("height");
						if(w)options.width=w;
						if(h)options.height=h;
						options.max=eval($this.attr("max")||"false");
						options.mask=eval($this.attr("mask")||"false");
						options.maxable=eval($this.attr("maxable")||"true");
						options.minable=eval($this.attr("minable")||"true");
						options.fresh=eval($this.attr("fresh")||"true");
						options.resizable=eval($this.attr("resizable")||"true");
						options.drawable=eval($this.attr("drawable")||"true");
						options.close=eval($this.attr("close")||"");
						options.param=$this.attr("param")||"";
						var url=unescape($this.attr("href")).replaceTmById($(event.target).parents(".unitBox:first"));
						DWZ.debug(url);
						if(!url.isFinishedTm()){
						alertMsg.error($this.attr("warn")||DWZ.msg("alertSelectMsg"));
						return false;}
						$.pdialog.open(url,rel,title,options);
						return false;
					});
				}
			}else{
				that.st.__id=0;
				that.st.isReLoad=false;
				that.st.table = that.st.tableId;
		        $(that.st.tableId).removeAttr("role");
		        $(that.st.tableId).find("tfoot").remove();
		        that.init();
			}
			
		},
		timerRecord: function(){  //计算方法运行时间
			   this.array=[];
			   this.begin=function(){
			    this.array.push(new Date());
			   }
			   this.end=function(){
			    if(this.array.length==0)throw 'begin()和end()要成对的调用';
			    var end=new Date();
			    var start=this.array.pop();
			    console.log("运行时间"+(end-start));
			    return end-start;
			   }
		},
		list: function() {
			var that = this;
			that.st.tableId = that.st.table;
			$(that.st.table).dataTable().fnDestroy();
			that.st.tableObj = $(that.st.table).DataTable({
						"autoWidth":false, //禁用自动列宽度计算
						"ordering":that.st.ordering,
						"searching":that.st.searching,
						"paging": that.st.paging,
						"scrollX":that.st.scrollX,
						"scrollY":that.st.scrollY,
						"displayLength":that.st.displayLength,
						"dom": '<"top"lf>rt<"bottom"ip><"clear">',
						"pagingType": "full_numbers",
						"language" : {
							"lengthMenu": "每页  _MENU_ 条记录",
							"ZeroRecords": "抱歉， 没有找到",
							"info": "当前显示 _START_ 到  _END_ 条   一共 _TOTAL_ 条记录",
							"infoEmpty": "",
							"infoFiltered": "(从 _MAX_ 条数据中检索)",
							"zeroRecords": "没有检索到数据",
							"search": "搜索",
							"processing": "正在加载数据...",  
							"paginate":{
							"first": "首页",
							"previous": "上一页",
							"next": "下一页",
							"last": "末页"
							},
						},
						"fnDrawCallback": function() {
							that.st.nextId();
							if(that.st.is_stats){
								var footObj = $(this).parents("div.dataTables_wrapper").find(".dataTables_scrollFoot");
								if(this.fnGetNodes().length>0){
									footObj.show();
									that.tjInit(this.fnGetNodes());
								}
								else{
									footObj.hide();
								}
							}
							if(that.st.show_both_save_btn){//显示保存按钮
								if(this.fnGetData().length>0){
									var id = new Array();
									$("[name*='[id]']").each(function(){
									    if($(this).val()=="0"){
									    	id.push($(this).val());
									    }
									});
									if(id.length>0){//有新增行时
										$(that.st.table).find('.both_save_btn').show();
									}else{
										$(that.st.table).find('.both_save_btn').hide();
									}
								}else{
									$(that.st.table).find('.both_save_btn').hide();
								}
							}else{
								$(that.st.table).find('.both_save_btn').hide();
							}
						},
						"fnInitComplete": function() {
							var this_obj = this;
							that.st.table+="_wrapper";
							that.st.tableObj = $(this_obj).DataTable();
							that.st.obj = this;
							var time = new that.timerRecord(); //计算方法运行时间
							var width = $(that.st.table).find(".dataTables_scrollHeadInner").width();
							$(that.st.table).find("[type='search']").blur();
							if(that.st.table_type=="view"){ //隐藏操作列
								$(that.st.table).find("thead:eq(0) th").each(function(i){
									var template_key = $(this).attr("template_key");
									if(template_key=="action"){
										that.st.obj.fnSetColumnVis(i,false);
									}
								});
								$(that.st.table).find("table,.dataTables_scrollHeadInner,.dataTables_scrollFootInner").width(width);
								that.st.__id = 1;
							}
							if(that.st.is_stats){
								var footObj = $(this).parents("div.dataTables_wrapper").find(".dataTables_scrollFoot");
								if(this.fnGetNodes().length>0){
									footObj.show();
									that.tjInit(this.fnGetNodes());
								}
								else{
									footObj.hide();
								}
							}
							if(that.st.maxShow>0){  //显示最大列数
								for(var k in that.st.template_tr){
									if(k>that.st.maxShow && k!=that.st.template_tr.length-1){
										var column = that.st.tableObj.column(k);
								        column.visible(false);
									}
								}
								if(that.st.template_tr.length>that.st.maxShow){
									that.st.tableObj.column(0).nodes().each( function (cell, i) {
										var html = '<a class="details-control"></a>';
										$(this).html(html);
									});
								}
								$(that.st.table).find('.dataTables_scrollBody tbody').on('click', 'a.details-control', function (e) {
									e.preventDefault();
							        var tr = $(this).closest('tr');
							        var row = that.st.tableObj.row( tr );
							 
							        if ( row.child.isShown() ) {
							            row.child.hide();
							            tr.removeClass('shown');
							        }
							        else {
							            row.child( format(row.data()) ).show();
							            tr.addClass('shown');
							        }
							    } );
								
								function format ( d ) {
									var html = '';
									for(var k in d){
										if(k>that.st.maxShow && k!=d.length-1){
											html += '<div class="max_column_shown">'+that.st.table_th[k]+':'+ d[k] +'</div>';
										}
									}
									return html;
								}
							}
							$(that.st.table).find("."+that.st.delRow).die();
							//删除行
							$(that.st.table).find("."+that.st.delRow).live("click", function(){
								that.delRow(this);
								return false;
							});
							
							//点击tr时添加选中行样式效果
							$(that.st.table).on( 'click', 'tr', function () {
					        	that.st.tableObj.$('tr.selected').removeClass('selected');
					            $(this).addClass('selected');
						    } );
							
							if(that.st.mini_add_url==""){
								$(that.st.table).find("."+that.st.addRow).die();
								//添加行
								$(that.st.table).find("."+that.st.addRow).live( 'click', function () {
									if(that.st.mini_set_orderno && $(that.st.table).find("tr.is_not_save").length){
										$(that.st.table).find(".both_save_btn").click();
										return false;
									}
									var rows = parseInt($(that.st.table).find("."+that.st.add_col_input).val());
									that.addRow(rows);
									that.st.tableObj.draw();
									//location.href = that.st.tableId;
									if(that.st.paging){
										var countYe = that.st.tableObj.rows().nodes().length/that.st.displayLength;
										countYe = Math.ceil(countYe);
										this_obj.fnPageChange(countYe-1);
										$(that.st.table).find('.dataTables_scrollBody').scrollLeft(0); //滚动到最左侧
									}else{
										//$(that.st.table).find('.dataTables_scrollBody').scrollTop($(that.st.table).find('.dataTables_scrollBody table').height());
									}
									$(that.st.table).find('.dataTables_scrollBody tr:last .list_group_lay input:first').focus();
								});
							}else{
								$(that.st.table).find("."+that.st.addRow).die();
								$(that.st.table).find("."+that.st.addRow).live( 'click',function(event){
									var $this=$(this);
									//灰色按钮不可点
									if($this.hasClass("disabled")){
										return false;
									}
									var title=$this.attr("title")||$this.text();
									var tabid=$this.attr("rel")||"_blank";
									var fresh=eval($this.attr("fresh")||"true");
									var external=eval($this.attr("external")||"false");
									var url=unescape($this.attr("href")).replaceTmById($(event.target).parents(".unitBox:first"));
									DWZ.debug(url);
									if(!url.isFinishedTm()){
									alertMsg.error($this.attr("warn")||DWZ.msg("alertSelectMsg"));
									return false;}
									navTab.openTab(tabid,url,{title:title,fresh:fresh,external:external});
									event.preventDefault();
								});
							}
							if(that.st.search_column){
								$(that.st.table).find('input[type="search"]').die();
								$(that.st.table).find('input[type="search"]').on('keyup click', function () {
									var value = $(this).val();
									$(that.st.tableId).DataTable().column(that.st.search_column).search(value,false,true).draw();
							    });
							}
							
							$(that.st.table).on( 'dblclick', 'tr', function () {
								var rel_type = $(this).find(".save_row_btn").attr("rel_type");
								if(rel_type=="edit"){
									$(this).find(".save_row_btn").click();
								}
								return false;
							});
							
							//双击tr时进行编辑
							$(that.st.table).on( 'dblclick', 'tr', function () {
								var rel_type = $(this).find(".save_row_btn").attr("rel_type");
								if(rel_type=="edit"){
									$(this).find(".save_row_btn").click();
								}
								return false;
							});
							
							//鼠标移上去的时候初始化下拉框组件 或 上传组件
							$(that.st.table).find(".initSelect2").live( 'mousemove', function () {
								$(this).removeClass("initSelect2");
								var selet_obj = $(this).find("select");
								selet_obj.show();
								$(this).find(".select2-container").remove();
								if(selet_obj.hasClass("readonly") || selet_obj.attr("readonly")){
									selet_obj.select2("destroy").select2().attr("readonly",true);
								}else{
									selet_obj.select2("destroy").select2();
								}
							});
							
							//鼠标移上去的时候初始化上传组件
							$(that.st.table).find(".initUpload").live( 'mousemove', function () {
								$(this).removeClass("initUpload");
								$(this).find(".Huploadify-button").remove();
								if($.fn.uploadify)that.uploadify_init($(this));
							});
							
							
							//保存或编辑行
							$(that.st.table).find(".save_row_btn").die();
							$(that.st.table).find(".save_row_btn").live( 'click', function() {
//								var time = new that.timerRecord(); //计算方法运行时间
//								time.begin();
								var this_btn = $(this);
								var rel_type = $(this).attr("rel_type");
								var delRowClass = "."+that.st.delRow;
								var all_obj = $(this).parents("tr").eq(0).find('input, select');
						        var post_obj = $(this).parents("tr").eq(0).find('input[name*="datatable"], select[name*="datatable"]');
						        var nRow = $(this).parents("tr")[0];
								if(rel_type=="save"){
							        var post_data = new Array();
							        var post_url = $(this).attr("post_url");
							        var post_id = $(this).attr("post_id");
							        var post_table = $(this).attr("post_table");
							        var post_mas_id = $(this).parents("form").find("input[name='masid']").val();
							        if(post_id==null || post_id=="undefined" || post_id==undefined){
							        	post_id = 0;
							        }
							        
							        post_obj.each(function(i){
							        	var name = $(this).attr("name");
							        	var value = $(this).val();
							        	post_data.push({"name":name,"value":value});
							        	if(!name.match(/\[id\]/g)){
								        	$(this).attr("name","#hide#"+name);  //为了整体保存时不接收值
								        	$(this).attr("disabled",true);
							        	}
							        });
							        
							        $(this).parents("tr").eq(0).find("td").each(function(i){
							        	var val = "";
							        	var obj = $(this).find('select').val();
							        	var div_obj = $(this).find(".list_group_lay");
							        	var html = "";
							        	if(obj!=undefined){
							        		val = $(this).find('select').find("option:selected").text();
							        		if($(this).find('select').val()==""){
							        			val = "";
							        		}
							        	}else{
							        		obj = $(this).find('input[class*="textInput"]');
							        		val = obj.val();
							        		if($(this).find('.input-addon-unit').attr("title")){
							        			val=obj.val()+$(this).find('.input-addon-unit').attr("title");
							        		}
							        	}
							        	if($.trim(div_obj.html())!=""){
							        		if(val==undefined)val="";
											html = "<span class='datatable_show_val'>";
											html += val;
											html += "</span>";
										}else{
											html = $(this).html();
										}
							        	div_obj.after(html);
						        		div_obj.hide();
							        });
							        $.ajax({
										url:post_url+'/post_table/'+post_table+'/post_id/'+post_id+'/post_mas_id/'+post_mas_id,
										data:post_data,
										type:'post',
										dataType:'json',
										success:function(data){
											var id = data.data;
											if(data.status){
												this_btn.attr('post_id',id);
												this_btn.parents("tr").eq(0).find("input[name*='[id]']").val(id);
												this_btn.parents("tr").eq(0).find(delRowClass).attr("del_id",id);
											}else{
												alertMsg.error(data.message);
											}
										}
									});
							        
							        $(this).parents("tr").eq(0).removeClass('selected');
							        $(this).attr("rel_type","edit");
							        $(this).attr("title","修改");
							        $(this).html('<span class="icon-pencil">');
								}else{
									post_obj.each(function(i){
							        	var name = $(this).attr("name");
						        		$(this).attr("name",name.replace(/#hide#/, ""));
							        	$(this).attr("disabled",false);
							        });
									
									//$(that.st.table).find("table").resize();//重置表格宽度
						            that.st.tableObj.$('tr.selected').removeClass('selected');
						            $(this).parents("tr").eq(0).addClass('selected');
									$(this).parents("tr").eq(0).find(".list_group_lay").show();
									$(this).parents("tr").eq(0).find(".datatable_show_val").remove();
									$(this).attr("rel_type","save");
									that.get_add_obj_name(-1,$(this).parents("tr"),true);
									$(this).attr("title","保存");
									$(this).html('<span class="icon-save">');
									$(this).parents("tr").find(".list_group_lay input[type='text']:visible:first").focus();
								}
//								var end = time.end();
						    });
							if(that.st.__id==1){
								if(that.st.table_type!="view"){
									that.get_obj_name(); //重写name									
								}
								if(that.st.fySelectHide)$(that.st.table).find(".dataTables_length label").hide();
								
								if(that.st.table_type!="list" && that.st.max_height){
									$(that.st.table).find(".dataTables_scrollBody").css("height","auto");
									$(that.st.table).find(".dataTables_scrollBody").css("max-height",that.st.scrollY);
								}
								
								var body_table = $(that.st.table).find(".dataTables_scrollBody table");
								var head_table = $(that.st.table).find(".dataTables_scrollHead table");
								
								if(that.st.addRow && that.st.table_type!="view"){
									that.addRowHtml();
								}
								if(that.st.show_both_save_btn && that.st.table_type!="view"){
									that.bothRowHtml();
								}
								
								$(that.st.table).find('[type="search"]').focus();
								if(that.st.table_type=="add"){
									$(that.st.table).find(".dataTables_filter").hide();
								}
								if(that.st.table_type=="add"){
									that.addRow(that.st.default_new_tr);
								}
								if(!that.st.show_save_btn){//隐藏单行保存按钮
									$(that.st.table).find('.save_row_btn').hide();
								}
								if(that.st.show_both_save_btn){//显示保存按钮
									if(this.fnGetData().length>0){
										var id = new Array();
										$("[name*='[id]']").each(function(){
										    if($(this).val()=="0"){
										    	id.push($(this).val());
										    }
										});
										if(id.length>0){//有新增行时
											$(that.st.table).find('.both_save_btn').show();
										}else{
											$(that.st.table).find('.both_save_btn').hide();
										}
									}else{
										$(that.st.table).find('.both_save_btn').hide();
									}
								}else{
									$(that.st.table).find('.both_save_btn').hide();
								}
								$(that.st.table).find('th').removeClass("sorting_asc");
								
								if(that.st.table_type=="view" || that.st.table_type=="list" || that.st.table_type=="edit"){
									//if( typeof initUI === 'function' )$(that.st.table).find("tbody").initUI();
								}
								var obj = $(that.st.table).parents(".into_table_lay").find('.dataTableBtn');
								if(obj){
									$(that.st.table).find(".top").append(obj); //把外部按钮移入表格组件
								}
							}
						}
			});
		},
		initList: function() {
			var that = this;
			that.st.tableId = that.st.table;
			that.st.searching = true;
			
			var table_data = "";
			try{
				table_data = $(that.st.table).attr("table_data")?$.parseJSON($(that.st.table).attr("table_data")):"";
			}catch(e){
			}
			if(table_data!=""){
				for(var k1 in table_data){
					for(var k2 in that.st){
						if(k1==k2){
							that.st[k2]=table_data[k1];
						}
					}
				}
			}
			
			var table_type = $(this.st.table).attr("table_type");
			if(table_type!=undefined){
				that.st.table_type=table_type;
			}
			var parent_div = $(that.st.table).parents("div").eq(0);//上一级div容器
			var width = parseInt(parent_div.width()) * 1;
			var height = parent_div.height();
			//that.st.scrollX = width+"px";
			if(this.st.template_tr=="")
			{
				$(this.st.table).find("thead:eq(0) th").each(function(i){
					that.st.table_th.push($(this).html());
					that.st.template_tr.push(that.ThType($(this),i,""));
					var tjarr = {};
					tjarr.is_stats=$(this).attr("is_stats")?true:false;
					tjarr.decimals=that.isNumber(parseInt($(this).attr("stats_num")))?parseInt($(this).attr("stats_num")):2;
					that.st.tongji.push(tjarr);
				});
			}
			that.st.table_th_index = [];
			$(that.st.table).find("th[template_key]").each(function(o){
				if($(this).attr("is_stats")){
					that.st.is_stats = true;
				}
				that.st.table_th_index.push(o);
			});
			if(that.st.is_stats){
				$(that.st.table).append(that.getTjCol());
			}
			$(that.st.table).dataTable().fnDestroy();
			that.st.tableObj = $(that.st.table).DataTable({
						"autoWidth":false, //禁用自动列宽度计算
						"ordering":that.st.ordering,
						"searching":that.st.searching,
						"paging": that.st.paging,
						"bProcessing": true,
						"bServerSide": that.st.bServerSide,
						"scrollY":that.st.scrollY,
						"scrollX":that.st.scrollX,
						"displayLength":that.st.displayLength,
						"dom": '<"top"lf>rt<"bottom"ip><"clear">',
						"pagingType": "full_numbers",
						"ajax": that.st.ajax_get_data_url,
						"language" : {
							"lengthMenu": "每页  _MENU_ 条记录",
							"ZeroRecords": "抱歉， 没有找到",
							"info": "当前显示 _START_ 到  _END_ 条   一共 _TOTAL_ 条记录",
							"infoEmpty": "",
							"infoFiltered": "(从 _MAX_ 条数据中检索)",
							"zeroRecords": "没有检索到数据",
							"search": "搜索",
							"processing": "",  
							"paginate":{
							"first": "首页",
							"previous": "上一页",
							"next": "下一页",
							"last": "末页"
							},
						},
						"fnDrawCallback": function() {
							that.st.nextId();
							if(that.st.is_stats){
								var footObj = $(this).parents("div.dataTables_wrapper").find(".dataTables_scrollFoot");
								if(this.fnGetNodes().length>0){
									footObj.show();
									that.tjInit(this.fnGetNodes());
								}
								else{
									footObj.hide();
								}
							}
							
							if(that.st.show_both_save_btn){//显示保存按钮
								if(this.fnGetData().length>0){
									var id = new Array();
									$("[name*='[id]']").each(function(){
									    if($(this).val()=="0"){
									    	id.push($(this).val());
									    }
									});
									if(id.length>0){//有新增行时
										$(that.st.table).find('.both_save_btn').show();
									}else{
										$(that.st.table).find('.both_save_btn').hide();
									}
								}else{
									$(that.st.table).find('.both_save_btn').hide();
								}
							}else{
								$(that.st.table).find('.both_save_btn').hide();
							}
							
							
						},
						"fnInitComplete": function() {
							var this_obj = this;
							that.st.obj = this;
							that.st.table+="_wrapper";
							that.st.tableObj = $(this_obj).DataTable();
							if( typeof initUI === 'function' )$(that.st.table).initUI();
							$(that.st.table).find('th').removeClass("sorting_asc");
							
							//点击tr时添加选中行样式效果
							$(that.st.table).on( 'click', 'tr', function () {
					        	that.st.tableObj.$('tr.selected').removeClass('selected');
					            $(this).addClass('selected');
						    } );
							
							if(that.st.maxShow>0){  //显示最大列数
								for(var k in that.st.template_tr){
									if(k>that.st.maxShow && k!=that.st.template_tr.length-1){
										var column = that.st.tableObj.column(k);
								        column.visible(false);
									}
								}
								if(that.st.template_tr.length>that.st.maxShow){
									that.st.tableObj.column(0).nodes().each( function (cell, i) {
										var html = '<a class="details-control"></a>';
										$(this).html(html);
									});
								}
								$(that.st.table).find('.dataTables_scrollBody tbody').on('click', 'a.details-control', function (e) {
									e.preventDefault();
							        var tr = $(this).closest('tr');
							        var row = that.st.tableObj.row( tr );
							 
							        if ( row.child.isShown() ) {
							            row.child.hide();
							            tr.removeClass('shown');
							        }
							        else {
							            row.child( format(row.data()) ).show();
							            tr.addClass('shown');
							        }
							    } );
								
								if(that.st.show_both_save_btn){//显示保存按钮
									if(this.fnGetData().length>0){
										var id = new Array();
										$("[name*='[id]']").each(function(){
										    if($(this).val()=="0"){
										    	id.push($(this).val());
										    }
										});
										if(id.length>0){//有新增行时
											$(that.st.table).find('.both_save_btn').show();
										}else{
											$(that.st.table).find('.both_save_btn').hide();
										}
									}else{
										$(that.st.table).find('.both_save_btn').hide();
									}
								}else{
									$(that.st.table).find('.both_save_btn').hide();
								}
								
								function format ( d ) {
									var html = '';
									for(var k in d){
										if(k>that.st.maxShow && k!=d.length-1){
											html += '<div class="max_column_shown">'+that.st.table_th[k]+':'+ d[k] +'</div>';
										}
									}
									return html;
								}
								
								var obj = $(that.st.table).parents(".into_table_lay").find('.dataTableBtn');
								if(obj){
									$(that.st.table).find(".top").append(obj); //把外部按钮移入表格组件
								}
							}
						}
			});
		},
		rowUpdate:function (html, nRow, index){
			this.st.obj.fnUpdate(html, nRow, index, false);
		},
		addRowNew:function (nRow){
			var that = this;
			$(that.st.table).find("thead:eq(0) th").each(function(i){
				var html = that.ThType($(this),i,nRow[i]);
				nRow[i] = html;
			});
			that.st.obj.fnAddData(nRow);
			
			that.reloadSerial();
			var row_index = that.st.tableObj.rows().nodes().length-1; //获取新增索引
			var row = that.st.obj.fnGetNodes(row_index);
			that.get_add_obj_name(-1,$(row),true);
		},
		empty:function(){ //清空表
			var that = this;
			that.st.obj?that.st.obj.fnClearTable():'';
		},
		reloadBodyHtml:function(html){
			var that = this;
			that.fnDestroy();
			$(that.st.table).find('tbody').html($html);
			return initTableWNEWOne(that.st.tableId)
		},
		dateDiff:function(endDate,  startDate){    //sDate1和sDate2是2006-12-18格式 
			var startTime = (new Date(startDate)).getTime();//传过来的开始时间转换为毫秒
			var endTime = (new Date(endDate)).getTime()
			if(startTime>endTime){
				return 0;
			}else{
				return parseInt(Math.abs(startTime - endTime ) / 1000 / 60 / 60 /24);
			}
		},
		add_day:function(sdate,n,type) {//日期增减天数
		    var d = new Date(sdate);
		    if (d == "Invalid Date") {
		        return "";
		    }
		    //当前日期的毫秒数 + 天数 * 一天的毫秒数
		    var da = d.getTime() + n * 24 * 60 * 60 * 1000;
		    if(type!="add"){
		    	da = d.getTime() - n * 24 * 60 * 60 * 1000;
		    }
		    var result = new Date(da);
		    var year = result.getFullYear();
		    var month = result.getMonth()+1;
		    var days = result.getDate();
		    if(month<10){
			    month = "0"+month;
            }
            if(days<10){
        	    days = "0"+days;
            }
            var dd = year+"-"+month+"-"+days;
 		    return dd;
		},
		ding_month:function(dtstr,val,type){//定月
			var today = new Date(dtstr);
		    if (today == "Invalid Date") {
		        return "";
		    }
		    var that = this;
		    var last_day = getMonthLastDay(val);
            var day = last_day.getDate();
            if(type=='first'){
            	day = "01";
            }
            var now_date = new Date();
            var month = today.getMonth()+1;
            var year = today.getFullYear();
            if(val<10){
                val = "0"+val;
            }
            var str = today.getFullYear()+"-"+val+"-"+day;
            var this_date = new Date(str);
            if((this_date-now_date)<0){
                str = (now_date.getFullYear()+1)+"-"+val+"-"+day;
            }
		    return str;
		},
		getLastDay:function(year,month) {        
            var new_year = year;    //取当前的年份         
            var new_month = month++;//取下一个月的第一天，方便计算（最后一天不固定）         
            if(month>12) {        
             new_month -=12;        //月份减         
             new_year++;            //年份增         
            }        
            var new_date = new Date(new_year,new_month,1);                //取当年当月中的第一天         
            return (new Date(new_date.getTime()-1000*60*60*24)).getDate();//获取当月最后一天日期         
		},
		lookupByAddRow:function(arr,td_index){//lookup内部多行带回
			var that = this;
			if(that.isArray(arr)){
				for(var i = 0;i<arr.length;i++){
					that.addRow();
					var row_index = that.st.tableObj.rows().nodes().length-1; //获取新增索引
					var row = that.st.obj.fnGetNodes(row_index);
					if(that.isArray(arr[i])){
						for(var k in arr[i]){
								var value = arr[i][k];
								var bindlookup = $(row).find("td").eq(td_index).find("a[lookupgroup]").attr("lookupgroup");
								$(row).find("[class*='"+bindlookup+"."+k+"']").val(value).change();
						}
						that.st.tableObj.draw();
						if(that.st.paging){
							var countYe = that.st.tableObj.rows().nodes().length/that.st.displayLength;
							countYe = Math.ceil(countYe);
							this_obj.fnPageChange(countYe-1);
							$(that.st.table).find('.dataTables_scrollBody').scrollLeft(0); //滚动到最左侧
						}
					}else{
						alert("数据格式错误");
					}
				}
			}else{
				alert("数据格式错误");
			}
		},
		lookupAddRow:function(arr){
			var that = this;
			if(that.isArray(arr)){
				if($(that.st.table).parents("#MisAutoDeb_edit").length || $(that.st.table).parents("#MisAutoDeb_add").length){//保后计划
					try{
						var dingbao_count = 0;//定保条数
						var dingbao_danweizhi = 0;//定保单位值
						var dingbao_date = $(that.st.table).parents("form").find("input[name='jiangetianshu']").val();//定保周期
						var sDate = $(that.st.table).parents("form").find("input[name='jihuayijuriqi']").val();//计划开始日期
						var eDate = $(that.st.table).parents("form").find("input[name='yinghuaikuanrijidaoq']").val();//计划结束日期
						for(var i = 0;i<arr.length;i++){
							for(var k in arr[i]){
								if(k=="baohounaxing" && arr[i][k]=="02" && arr[i]["zhouqitian"]=="01"){
									dingbao_danweizhi = parseInt(arr[i]["danweizhi"])?parseInt(arr[i]["danweizhi"]):0;
									break;
								}
							}
						}
						dingbao_count = parseInt(dingbao_date/dingbao_danweizhi) - 1;
						var newArr = new Array();
						var is_shoubao = false;//是否存在首保
						for(var i = 0;i<arr.length;i++){
							for(var k in arr[i]){
								var dingbao_arr = new Array();
								dingbao_arr["baohounaxing"] = arr[i]["baohounaxing"];
								dingbao_arr["zhouqitian"] = arr[i]["zhouqitian"];
								dingbao_arr["danweizhi"] = arr[i]["danweizhi"];
								dingbao_arr["shifugaidong"] = arr[i]["shifugaidong"];
								if(k=="baohounaxing" && arr[i][k]=="01" && arr[i]["zhouqitian"]=="01"){//首保 天
									dingbao_arr["jihuakaishiriqi"] = sDate;
									dingbao_arr["jihuawanchengriqi"] = that.add_day(sDate,parseInt(dingbao_arr["danweizhi"]),"add");
									newArr.push(dingbao_arr);
								}
								if(k=="baohounaxing" && arr[i][k]=="01" && arr[i]["zhouqitian"]=="02"){//首保 定月
									dingbao_arr["jihuakaishiriqi"] = that.ding_month(sDate,parseInt(dingbao_arr["danweizhi"]),'first');
									dingbao_arr["jihuawanchengriqi"] = that.ding_month(sDate,parseInt(dingbao_arr["danweizhi"]),'last');
									newArr.push(dingbao_arr);
								}
							}
						}
						if(newArr.length>0){
							is_shoubao = true;
						}
						var weibaoksDate = "";
						for(var i = 0;i<arr.length;i++){
							for(var k in arr[i]){
								if(k=="baohounaxing" && arr[i][k]=="02" && arr[i]["zhouqitian"]=="01"){//定保 天
									if(dingbao_count){
										for(var j=0; j<dingbao_count; j++){
											var dingbao_arr = new Array();
											dingbao_arr["baohounaxing"] = arr[i]["baohounaxing"];
											dingbao_arr["zhouqitian"] = arr[i]["zhouqitian"];
											dingbao_arr["danweizhi"] = arr[i]["danweizhi"];
											dingbao_arr["shifugaidong"] = arr[i]["shifugaidong"];
											if(j==0){
												if(newArr.length==0){
													dingbao_arr["jihuakaishiriqi"] = sDate;
													dingbao_arr["jihuawanchengriqi"] = that.add_day(dingbao_arr["jihuakaishiriqi"],parseInt(arr[i]["danweizhi"]),"add");
												}else{
													dingbao_arr["jihuakaishiriqi"] = that.add_day(newArr[0]["jihuawanchengriqi"],1,"add");
													dingbao_arr["jihuawanchengriqi"] = that.add_day(dingbao_arr["jihuakaishiriqi"],parseInt(arr[i]["danweizhi"]),"add");
												}
											}else{
												if(!is_shoubao){
													dingbao_arr["jihuakaishiriqi"] = that.add_day(newArr[j-1]["jihuawanchengriqi"],1,"add");
													dingbao_arr["jihuawanchengriqi"] = that.add_day(dingbao_arr["jihuakaishiriqi"],parseInt(arr[i]["danweizhi"]),"add");
												}else{
													dingbao_arr["jihuakaishiriqi"] = that.add_day(newArr[j]["jihuawanchengriqi"],1,"add");
													dingbao_arr["jihuawanchengriqi"] = that.add_day(dingbao_arr["jihuakaishiriqi"],parseInt(arr[i]["danweizhi"]),"add");
												}
											}
											weibaoksDate = dingbao_arr["jihuawanchengriqi"];
											newArr.push(dingbao_arr);
										}
									}
								}
								if(k=="baohounaxing" && arr[i][k]=="02" && arr[i]["zhouqitian"]=="02"){//定保 定月
									var dingbao_arr = new Array();
									dingbao_arr["baohounaxing"] = arr[i]["baohounaxing"];
									dingbao_arr["zhouqitian"] = arr[i]["zhouqitian"];
									dingbao_arr["danweizhi"] = arr[i]["danweizhi"];
									dingbao_arr["shifugaidong"] = arr[i]["shifugaidong"];
									dingbao_arr["jihuakaishiriqi"] = that.ding_month(sDate,parseInt(dingbao_arr["danweizhi"]),'first');
									dingbao_arr["jihuawanchengriqi"] = that.ding_month(sDate,parseInt(dingbao_arr["danweizhi"]),'last');
									newArr.push(dingbao_arr);
								}
							}
						}
						for(var i = 0;i<arr.length;i++){
							for(var k in arr[i]){
								if(k=="baohounaxing" && arr[i][k]=="03" && arr[i]["zhouqitian"]=="01"){//尾保 天
									var wbLength = $("#MisAutoDeb_add,#MisAutoDeb_edit").find("table tbody").eq(0).find("tr").length;
									if(wbLength){
										for(var j = 0;j<wbLength;j++){
											var dingbao_arr = new Array();
											dingbao_arr["baohounaxing"] = arr[i]["baohounaxing"];
											dingbao_arr["zhouqitian"] = arr[i]["zhouqitian"];
											dingbao_arr["danweizhi"] = arr[i]["danweizhi"];
											dingbao_arr["shifugaidong"] = arr[i]["shifugaidong"];
											dingbao_arr["jihuawanchengriqi"] = that.add_day($("#MisAutoDeb_add,#MisAutoDeb_edit").find("table tbody").eq(0).find("tr").eq(j).find("[name*='[yinghuaikuanrijidaoqiri]']").val(),parseInt(arr[i]["danweizhi"]),"jian");
											dingbao_arr["jihuakaishiriqi"] = that.add_day(dingbao_arr["jihuawanchengriqi"],30,"")
											newArr.push(dingbao_arr);
										}
									}
								}
								if(k=="baohounaxing" && arr[i][k]=="03" && arr[i]["zhouqitian"]=="02"){//尾保 定月
									var dingbao_arr = new Array();
									dingbao_arr["baohounaxing"] = arr[i]["baohounaxing"];
									dingbao_arr["zhouqitian"] = arr[i]["zhouqitian"];
									dingbao_arr["danweizhi"] = arr[i]["danweizhi"];
									dingbao_arr["shifugaidong"] = arr[i]["shifugaidong"];
									dingbao_arr["jihuakaishiriqi"] = that.ding_month(sDate,parseInt(dingbao_arr["danweizhi"]),'first');
									dingbao_arr["jihuawanchengriqi"] = that.ding_month(sDate,parseInt(dingbao_arr["danweizhi"]),'last');
									newArr.push(dingbao_arr);
								}
							}
						}
						if(newArr.length>0){
							arr = newArr;
						}
					}catch(err){
					}
				}
				for(var i = 0;i<arr.length;i++){
					that.addRow();
					var row_index = that.st.tableObj.rows().nodes().length-1; //获取新增索引
					var row = that.st.obj.fnGetNodes(row_index);
					if(that.isArray(arr[i])){
						for(var k in arr[i]){
							var index = $(row).find("td").index($(row).find("[name*='["+k+"]']").parents("td"));
							if(index>0 && arr[i][k]){
								var this_th = $(that.st.table).find("th[template_key]").eq(index)
								var type = this_th.attr("template_key");
								var this_value = arr[i][k];
								var template_data = "";
								try{
									template_data = this_th.attr("template_data")?$.parseJSON(this_th.attr("template_data")):"";
								}catch(e){
								}
								switch (type) {
									case "input":
										if((template_data!=null) && (typeof(template_data.unitl) != "undefined")){
											this_value = this_value;
										}
										break;
									case "date":
										if(this_value!="" && parseInt(this_value)>1000000){
											that.date_format();
											var timeint = parseInt(this_value+"000");
											if(that.isNumber(timeint)){
												this_value = new Date(timeint).format(template_data.format);
											}
										}
										break;
//									case "selecttree":
////										var content = $(row).find("td").eq(index);
////								        if($.fn.comboxtree)$("input.comboxtree", content ).comboxtree();
////								        var $input = $(row).find("[name*='["+k+"]']").siblings(".comboxtree");
////								        if($input.length){
////			                            	try{
////			                            		var ul_tree = $input.siblings("ul.ztree").attr("nodes");
////			                                	ul_tree = ul_tree?$.parseJSON(ul_tree):"";
////			                                	if(ul_tree.length>0){
////			                                		for(var i=0; i<ul_tree.length;i++){
////			                                			if(ul_tree[i]["key"]==$input.siblings("input:hidden").val()){
////			                                				$input.val(ul_tree[i]["name"]);
////			                                			}
////			                                		}
////			                                	}
////			                            	}catch(err){
////			                            	}
////								        }
//								        break;
									case "lookup":
										$(row).find("[name*='["+k+"]']").siblings("input.readonly").val(this_value).change();
										break;
									default :
										this_value = arr[i][k];
										break;
								}
								if($(row).find("[name*='["+k+"]']").length){
									$(row).find("[name*='["+k+"]']").val(this_value);
									try{
										$(row).find("[name*='["+k+"]']").trigger("change");
									}catch(e){
										console.log(e);
									}
								}
							}
						}
						that.st.tableObj.draw();
						if(that.st.paging){
							var countYe = that.st.tableObj.rows().nodes().length/that.st.displayLength;
							countYe = Math.ceil(countYe);
							this_obj.fnPageChange(countYe-1);
							$(that.st.table).find('.dataTables_scrollBody').scrollLeft(0); //滚动到最左侧
						}
					}else{
						alert("数据格式错误");
					}
				}
			}else{
				alert("数据格式错误");
			}
		}
	};
	return table_wang_new;
}
function timerRecord(num){  //计算方法运行时间
	   this.array=[];
	   this.begin=function(){
	    this.array.push(new Date());
	   }
	   this.end=function(){
	    if(this.array.length==0)throw 'begin()和end()要成对的调用';
	    var end=new Date();
	    var start=this.array.pop();
	    console.log(num+":"+(end-start));
	    return end-start;
	   }
}
function initTableWNEW() {
	$(".into_table_new").each(function(){
		var this_obj = this;
		var role = $(this_obj).attr("role");
		if(!role && this_obj.offsetHeight>0){
			var rand = Math.round(Math.random() * 10000000);
			var table_type = $(this_obj).attr("table_type");
			var id = $(this_obj).attr("id");
			var table = new TABLEWNEW();
			
			if(id == undefined){
				$(this_obj).attr("id","into_table_new"+rand);
				table.st.table="#into_table_new"+rand;
			}else{
				table.st.table="#"+id;
			}
			var parent_div = $(table.st.table).parent();//上一级div容器
			var width = parseInt(parent_div.width());
			if(table_type=="add"||table_type=="edit"){
				table.st.paging = false;
			}
			var ajax_get_data_url = $(this_obj).attr("ajax_get_data_url");
			var ajax_post_url = $(this_obj).attr("ajax_post_url");
			table.st.ajax_get_data_url=ajax_get_data_url;
			table.st.ajax_post_url=ajax_post_url;
			var time = new timerRecord("内嵌表初始化用时");
			time.begin();
			if(table_type=="list"){
				table.st.scrollX = (width-14)+"px";
				table.initList();
			}else{
				table.st.scrollX = (width-14)+"px";
				table.st.add_col_input = false;
				table.init();
			}
			time.end();
			var nbox = navTab.getCurrentPanel();
			nbox.find('.pageFormContent').eq(0).animate({scrollTop: '0px'}, 0);
		}
	});
}
function initTableWNEWOne(objId) {
	var role = $(objId).attr("role");
	var rand = Math.round(Math.random() * 10000000);
	var table_type = $(objId).attr("table_type");
	var id = $(objId).attr("id");
	var table = new TABLEWNEW();
	
	if(id == undefined){
		$(objId).attr("id","into_table_new"+rand);
		table.st.table="#into_table_new"+rand;
	}else{
		table.st.table="#"+id;
	}
	
	var parent_div = $(table.st.table).parent();//上一级div容器
	var width = parseInt(parent_div.width());
	
	if(table_type=="add"||table_type=="edit"){
		table.st.paging = false;
	}
	var ajax_get_data_url = $(objId).attr("ajax_get_data_url");
	var ajax_post_url = $(this).attr("ajax_post_url");
	table.st.ajax_get_data_url=ajax_get_data_url;
	table.st.ajax_post_url=ajax_post_url;
	if(table_type=="list"){
		table.initList();
		table.st.scrollX = (width-14)+"px";
	}else{
		table.st.scrollX = (width-14)+"px";
		table.st.add_col_input = false;
		table.init();
	}
	return table;
}