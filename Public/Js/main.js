/**
 * @author arrowing
 * @email  likenovel@qq.com
 */

//add a row in list to edit
function newRow(url){
	var table = $('.list:visible');
	var ths = table.find('th');
	var len = ths.length;
	var trbg = table.find('tr').last().attr('class');
	if(trbg == ''){
		var str = "<tr class='trbg'>";
	}else{
		var str = "<tr>";
	}
	var type = '';
	var field = '';
	for(var i=0;i<len;i++){
		type = ths.eq(i).attr('type');
		field = ths.eq(i).attr('field');
	    if(field == 'id' || typeof(field) == 'undefined'){
	    	str += "<td>#</td>";
	    }else if(type == 'time'){       //time
			str += "<td><input type='text' name='"+ ths.eq(i).attr('rfield') +"[]' size='10' readonly='true' class='date textInput readonly'></td>";
	    }else if(type == 'status'){     //status
	    	str += "<td><select name='status[]'><option value='1'>启用</option><option value='0'>禁用</option></select></td>";
	    }else if(typeof(type) != 'undefined' && type != 'count'){   //group
			$.ajax({
				url : url + '/getGroup/type/' + type,
				async : false,
				type : "GET",
				dataType: "json",
				success : function(data){
					str += "<td><select name='"+ field +"[]'>";
					for(var j=0;j<data.length;j++){
						str += "<option value='"+ data[j].id +"'>"+ data[j].name +"</option>";
					}
					str += "</select></td>";
				}
			});
	    }else{
	    	str += "<td><input name='"+ field +"[]' type='text' class='textInput' /></td>";
	    }
	}
	str += "</tr>";
	table.append(str);
	if(typeof($('#bInsert').attr('id')) == 'undefined'){
		var form = document.createElement("form");
		form.id = 'bInsert';
		form.method = 'post';
		form.action = url + '/batchInsert';
		table.wrap(form);
	}
	//Re-render the date plug again
    if($.fn.datepicker){
    $('input.date').each(function(){
    var $this=$(this);
    var opts={};
    if($this.attr("format"))opts.pattern=$this.attr("format");
    if($this.attr("yearstart"))opts.yearstart=$this.attr("yearstart");
    if($this.attr("yearend"))opts.yearend=$this.attr("yearend");
    $this.datepicker(opts);});}
}

//submit the rows which are added dynamicly
function batchInsertSubmit(){
	if(typeof($('#bInsert').attr('id')) == 'undefined'){
		alertMsg.warn('请添加行！');
	}else{
		$('#bInsert').submit();
	}
}

//the ending of the son page load, make it run
function sonPageInit(){
	//all class autocomplete use autocomplete
	$('.autocomplete').each(function(){
    	$(this).autocomplete({serviceUrl : TP_APP + '/Public/autocomplete'});
  	});
}

//add item to fifter list result
function addFifterItem(){
  var ths = $('.list:visible').eq(0).find('th[field]');
  var thsLen = ths.length;

  var options = new Array();
  for(i=0;i<thsLen;i++){
    options[i] = ths.eq(i).attr('field');
  }

  var labels = $('#fifter .unit label');
  for(j=0;j<labels.length;j++){
    for(i=0;i<thsLen;i++){
      if(labels.eq(j).attr('field') == options[i]){
        delete options[i];
      }
    }
  }

  var newUnit = "<div id='selFifterItem' class='unit'><label>";
  newUnit += "<select onchange='getNewFifterItem(this);'><option value=''>请选择</option>";
  for(index in options){
    newUnit += "<option type='"+ ths.eq(index).attr('type') +"' value='"+ options[index] +"'>"+ ths.eq(index).text() +"</option>";
  }
  newUnit += "</select></label></div>";

  $('#fifter').append(newUnit);
}

//get new fifter item to search
function getNewFifterItem(select){
  var $opt = $(select).find('option:selected');
  var url = TP_APP + '/Public/new_fifter';
  var data = {field:$opt.val(), type:$opt.attr('type'), showname:$opt.text() };
  $.get(url, data, function(data){
    $('#selFifterItem').remove();
    $('#fifter').append(data);

    //Re-render the date plug again
    if($.fn.datepicker){
    $('input.date').each(function(){
    var $this=$(this);
    var opts={};
    if($this.attr("format"))opts.pattern=$this.attr("format");
    if($this.attr("yearstart"))opts.yearstart=$this.attr("yearstart");
    if($this.attr("yearend"))opts.yearend=$this.attr("yearend");
    $this.datepicker(opts);});}
  });
}

//delete current fifter item , make it out of search
function del_current_fifter(close){
  $close = $(close);
  $close.parent().remove();
}

//use ajax to set the dynamic config
function setConf(form){
  var fields = '';
  $("tr[name=fields]").each(function(i){
    var thefield = '';
    var fieldname = '';
    $(this).find('input').each(function(j){
      var $this = $(this);
      fieldname = $this.attr('name');
      if($this.attr('type') == 'checkbox'){
        thefield += $this.attr('checked') ? '1' : '';
      }else{
        thefield += $this.val();
      }
      if(j != 3){
        thefield += ',';
      }
    });
    fields += fieldname + '=' + thefield + '&';
  });

  fields = fields.substring(0, fields.lastIndexOf('&'));
  var $form=$(form);
  var $callback = navTabAjaxDone;
  if(!$.isFunction($callback))$callback = eval('('+callback+')');
  $.ajax({
    type:'POST',
    url:$form.attr('action'),
    data:fields,
    dataType:"json",
    cache:false,
    success:$callback,
    error:DWZ.ajaxError
  });
  return false;
}
//select the all checkbox in the same td line
function all_select(checkbox,tableid,tablecalss){
	$checkbox = $(checkbox);
	if(tablecalss=='table'){
		var index = $checkbox.parent().parent().index();
	}else{
		var index = $checkbox.parent().index();
	}
	var status = $checkbox.attr('checked');
	var ftrue = false;
    if(status == 'checked'){
    	ftrue = true;
    }
    if(tablecalss=='table'){
    	var $dot = $checkbox.parents(".grid");
    	$dot.find(".gridScroller .gridTbody tr").each(function(i){
    		$(this).children().eq(index).children().children().attr('checked',ftrue);
    	});
    }else{
    	$('#'+tableid+' tr').each(function(i){
			$(this).children().eq(index).children().attr('checked',ftrue);
    	});
    }
}

function all_selectquickSearch(checkbox,inuptClass){
  $checkbox = $(checkbox);
  var $box= $.pdialog.getCurrent()
  $box.find('input.'+inuptClass).attr('checked',$checkbox.is(":checked"));
}

//redirect to the dynamic config panel
function to_dynamic(atag){
  var table = $('#node').val();
  if(table == 'all'){
    alertMsg.warn('请选择要配置的节点！');
    $(atag).attr('rel','Dynamicconf');
    $(atag).attr('href',TP_ACTION);
    $(atag).attr('title','动态配置');
  }
  $(atag).attr('href', $(atag).attr('href') + '/table/' + table);
}

//add domain item
function addDomainItem(){
  var url = TP_APP + '/Public/getDomain';
  $.getJSON(url, function(data){
	  if(data!=null){
	  	var options = new Array();
	  	var fields = new Array();
		for(var i=0;i<data.length;i++){
			options[i] = data[i].name;
			fields[i] = data[i].referfield;
		}
		var labels = $('#fifter .unit label');
		for(j=0;j<labels.length;j++){
		    for(i=0;i<data.length;i++){
		      if(labels.eq(j).attr('field') == fields[i]){
		        delete options[i];
		        delete fields[i];
		      }
		    }
		  }
		  var newUnit = "<div id='selFifterItem' class='unit'><label>";
		  newUnit += "<select onchange='getNewDomainItem(this);'><option value=''>请选择</option>";
		  for(index in options){
		    newUnit += "<option value='"+ fields[index] +"'>"+ options[index] +"</option>";
		  }
		  newUnit += "</select></label></div>";
		  $('#fifter').append(newUnit);
	  }
	});
}
//get new fifter item to search
function getNewDomainItem(select){
  var $opt = $(select).find('option:selected');
  var url = TP_APP + '/Public/new_domain';
  var data = {field:$opt.val(), type:$opt.attr('type'), showname:$opt.text() };
  $.get(url, data, function(data){
    $('#selFifterItem').remove();
    $('#fifter').append(data);
  });
}

/**********************************************
2012-01-07增加如下代码段
数据备份模块 START
***********************************************/
//start to resume db
function start_to_resume(){
	var tables = '';
	var type = document.getElementsByName('resume_type');
	for(var i=0;i<type.length;i++){
		if($(type[i]).attr('checked')){
			type = type[i].value;
		}
	}

	if(type == 'part'){
		var selectTables = document.getElementsByName('table');
		var arrayTables = new Array();
		for(var i=0;i<selectTables.length;i++){
			if($(selectTables[i]).attr('checked'))
				arrayTables.push(selectTables[i].value);
		}
		tables = arrayTables.join(',');
	}

	var file = $('#file').val();

	$('#resume_notic').html('正在还原，请耐心等候 ...');
	$.post(TP_APP + '/Resume/start_resume', {tables : tables, type : type, file : file, folder : encodeURIComponent($('#folder').val())},
		function(notic){
			$('#resume_notic').html(notic);
		}
	);
}

//resume some tables that part of db
function resume_part(){
	$('#part_tables').fadeIn('fast');
}

//load the page to select table for resume
function showSelect(filename, folder){
	var url = TP_APP + '/Resume/show/filename/' + filename + '/folder/' + encodeURIComponent(folder);
	$.pdialog.open(url, 'resumeDB', '还原数据库', {minable:true,width:500,height:400,mask:true,resizable:false});
}

//start to backup db
function start_to_backup(){
	var tables = document.getElementsByName('backup_tables');
	var type = document.getElementsByName('backup_type');
	var remark = $('#remark').val();
	var folder = $('#folder').val();
	var sizelimit = $('#sizelimit').val();

	for(var i=0;i<tables.length;i++){
		if($(tables[i]).attr('checked')){
			tables = tables[i].value;
		}
	}
	for(var i=0;i<type.length;i++){
		if($(type[i]).attr('checked')){
			type = type[i].value;
		}
	}

	if(tables == 'part'){
		var selectTables = document.getElementsByName('table');
		var arrayTables = new Array();
		for(var i=0;i<selectTables.length;i++){
			if($(selectTables[i]).attr('checked'))
				arrayTables.push(selectTables[i].value);
		}
		tables = arrayTables.join(',');
	}

	$('#backup_notic').html('正在备份，请耐心等候 ...');
	$.post(TP_APP + '/Backup/start_backup', {tables : tables, type : type, remark : remark, folder : folder, sizelimit : sizelimit},
		function(notic){
			$('#backup_notic').html(notic);
		}
	);
}

//backup some tables that part of db
function backup_part(){
	$('#part_tables').fadeIn('fast');
	if($('#tables').html() == ''){
		$.post(TP_APP + '/Backup/show_tables',
			function(tables){ //show all tables to select
				tables = $.parseJSON(tables);
				var html_show = '';
				for(var i=0;i<tables.length;i++){
					html_show += "<label style='width:300px;'><input name='table' type='checkbox' value='"+ tables[i] +"' />" + tables[i] + "</label>";
				}
				$('#tables').html(html_show);
			}
		);
	}
}

//hide table for backup
function hide_tables(){
	$('#part_tables').fadeOut('fast');
}
/**********************************************
2012-01-07增加如下代码段  END
***********************************************/


/**********************************************
2012-01-30增加如下代码段
邮件功能模块 START
***********************************************/
//highlight the line from close button
function highlight(p){
	$(p).css('background', '#ccc');
}

//unhighlight the line from close button
function unhighlight(p){
	$(p).css('background', '#fff');
}

//show all user for selecting email addresses
function showAddress(){
	var url = TP_APP + '/Email/show';
	$.pdialog.open(url, 'showAddress', '选择用户邮址', {minable:true,width:500,height:400,mask:true,resizable:false});
}

//batch add email to send
function batchAddEmail(){
	if($('#notic').css('display') == 'none'){
		var emails = document.getElementsByName('one_email');
		var sel_emails = new Array();
		for(var i=0;i<emails.length;i++){
			if($(emails[i]).attr('checked')){
				sel_emails.push(emails[i].value);
			}
		}

		var addresses = $('#addresses').val();
		if(addresses!= ''){
			var count = 0;
			var html = '';
			var select_tmp = document.getElementsByName('select_tmp');
			for(var i=0;i<select_tmp.length;i++){
				if($(select_tmp[i]).attr('checked')){
					select_tmp = select_tmp[i];
					break;
				}
			}
			addresses = addresses.split(',');
			for(var j=0;j<sel_emails.length;j++){
				var this_email = sel_emails[j] + "#" + select_tmp.value;
				if(!isIn(this_email, addresses) && this_email.indexOf(':#') == -1){
					addresses.push(this_email);
					count++;
					html += "<p onmouseover='highlight(this);' onmouseout='unhighlight(this);'><label style='color:green;'>"+ select_tmp.title + " " + sel_emails[j] +"</label>";
					html += "<a href='#close' onclick='del_current_email(this);' class='close'>close</a></p>";
				}
			}
		}else{
			addresses = sel_emails;
		}
		var tmp_html = $('#all_address').html();
		$('#all_address').html(tmp_html + html);
		$('#notic').html('此次共添加了'+ count +'条不重复邮址').fadeIn().delay(1500).fadeOut();
		$('#addresses').val(addresses.join(','));
		$.pdialog.close('showAddress');
	}
}

//judge the string that is in the array
function isIn(str, arr){
	for(var i=0;i<arr.length;i++){
		if(str == arr[i]){
			return true;
		}
	}
	return false;
}

//add a email to send
function addEmail(){
	if($('#notic').css('display') == 'none'){
		var email = $.trim($('#address').val());
		if(isEmail(email)){
			var select_tmp = document.getElementsByName('select_tmp');
			for(var i=0;i<select_tmp.length;i++){
				if($(select_tmp[i]).attr('checked')){
					select_tmp = select_tmp[i];
					break;
				}
			}

			var addresses = $('#addresses').val();
			if(addresses!= ''){
				addresses = addresses.split(',');
				var len = addresses.length;
				for(var i=0;i<len;i++){
					if(email + "#" + select_tmp.value == addresses[i]){
						$('#notic').html('此模板下的Email地址已经存在。').fadeIn().delay(800).fadeOut();
						return;
					}
				}
			}else{
				addresses = [];
			}

			addresses.push(email + '#' + select_tmp.value);
			$('#addresses').val(addresses.join(','));

			var html = $('#all_address').html();
			html += "<p onmouseover='highlight(this);' onmouseout='unhighlight(this);'><label title='"+ email + '#' + select_tmp.value +"' style='color:green;'>"+ select_tmp.title + " " + email +"</label>";
			html += "<a href='#close' onclick='del_current_email(this);' class='close'>close</a></p>";
			$('#all_address').html(html);
		}else{
			$('#notic').html('这不是有效的Email地址。').fadeIn().delay(800).fadeOut();
		}
	}
}



//delete current email address
function del_current_email(close){
	$close = $(close);
	var this_p = $close.parent();
	this_p.remove();

	var addresses = $('#addresses').val().split(',');
	var len = addresses.length;
	var email = this_p.children('label:eq(0)').attr('title');

	for(var i=0;i<len;i++){
		if(email == addresses[i]){
			addresses.splice(i, 1);
			break;
		}
	}
	$('#addresses').val(addresses.join(','));
}

//a reg for checking to email
function isEmail(str){
       var reg = /^([\.a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/;
       return reg.test(str);
}
/**********************************************
2012-01-30增加如下代码段  END
***********************************************/
/**
 * @author arrowing
 * @remark 增加动态搜索功能JS
 * @time 2012-08-20
 */
/* ============= start ================= */
$(function(){
	$("td[field]").live('mousedown', function(e){
		if(e.which == 3){
			/* 判断是否是lookup的 */
			var navTabId = 'navTabFIFTER';
			var fifterDiv = 'fifterDiv';
			var fifterTd = 'fifterTd';
			var searchtype = '';
			if($(this).parents('#lookup').length > 0){
				navTabId += '2';
				fifterDiv += '2';
				fifterTd += '2';
				searchtype = '/searchtype/2';
			}

			var options = {};
			options[fifterTd] = function(t){
	            var field = t.attr('field');
	            var ename = t.parents('.grid').find('thead').attr('ename');
	            var url = TP_APP + '/Search/singlesearch/field/'+ field +'/model/' + ename + searchtype;
				$.pdialog.open(url, fifterDiv, '查找记录', {minable:false,maxable:false,width:570,height:150,mask:true,resizable:false,mask:true});
	        };

			$(this).contextMenu(navTabId, {
			    bindings: options,
			    ctrSub:function(t,m){
			      var mCur = m.find("[rel='"+ fifterTd +"']");
			    }
			});
		}
	});
});
// 异步获取选中的动态设置的配置
function confirmSearch(view){

	if(view == ''){
		return false;
	}else if($('#relate_tables').css('display') != 'none'){
		$('#relate_tables').css('display', 'none');
		$('#tables tbody').html('');
	}
	$.getJSON(TP_APP + '/Search/confirmSearch?view=' + view, function(showfields){
		if(showfields){
			var html = '', name, showname, findtype, findtable, findfield;

			for(var i=0,len=showfields.length;i<len;i++){
				name = showfields[i].name;
				showname = showfields[i].showname;
				findtype = showfields[i].findtype;
				findtable = showfields[i].findtable;
				findfield = showfields[i].findfield;

				if(typeof name == 'undefined'){
					name = '';
				}
				if(typeof showname == 'undefined'){
					showname = '';
				}
				if(typeof findtype == 'undefined'){
					findtype = '';
				}
				if(typeof findtable == 'undefined'){
					findtable = '';
				}
				if(typeof findfield == 'undefined'){
					findfield = '';
				}

				html += "<tr><td><input type='text' value='"+ name +"' class='required textInput' /></td>";
				html += "<td><input type='text' value='"+ showname +"' class='required textInput' /></td>";
				html += "<td><input style='width:50px;' type='text' value='"+ findtype +"' class='required textInput' /></td>";
				//html += "<td><input style='width:120px;' type='text' value='"+ findtable +"' class='required textInput' /></td>";
				//html += "<td><input type='text' value='"+ findfield +"' class='required textInput' /></td>";
				html += '<td><input style="width:120px;" type="text" class="required checkByInput" checkfor="Exnt_Tables" insert="TABLE_NAME" show="TABLE_NAME" value="'+findtable+'" callback="searchchangetable" /></td>';
				html += '<td><input name="search_tablesname" type="text" class="required checkByInput" map="array(\'table_name\'=>'+findtable+');" checkfor="Exnt_Table_column" insert="COLUMN_NAME" show="COLUMN_NAME" value="'+findfield+'"  /></td>';
				html += "<td><a href='#close' onclick='removeSearchField(this);' class='close'>close</a></td></tr>";
			}
			$('#search_table tbody').html(html);
			$('#search_error').hide();
			$('#search_fields').show();
		}else{
			$('#search_fields').hide();
			$('#search_error').show();
		}
	});
}

//添加查找字段
function addSearchField(){
	var tr = "<tr><td><input type='text' class='required textInput' /></td>";
	tr += "<td><input type='text' class='required textInput' /></td>";
	tr += "<td><input style='width:50px;' type='text' class='required textInput' /></td>";
	//tr += "<td><input style='width:120px;' type='text' class='required textInput' /></td>";
	//tr += "<td><input type='text' class='required textInput' /></td>";
	tr +='<td><input style="width:120px;" type="text" class="required checkByInput xytdinput" checkfor="Exnt_Tables" insert="TABLE_NAME" show="TABLE_NAME" value="" callback="searchchangetable" /></td>';
	tr += '<td><input name="search_tablesname" type="text" class="required checkByInput xytdinput" map="" checkfor="Exnt_Table_column" insert="COLUMN_NAME" show="COLUMN_NAME" /></td>';
	tr += "<td><a href='#close' onclick='removeSearchField(this);' class='close'>close</a></td></tr>";
	$('#search_table tbody').append(tr);
}

// 移除查找字段
function removeSearchField(btn){
	$(btn).parent().parent().remove();
}

// 确保有字段可以分配、所有文本框都有填写
function AllStarWrited(){
	if($('#search_fields tbody').html() == ''){
		alert('请确定第一步中有信息可以分配');
		return false;
	}

	var inputs = $('#search_fields input');
	for(var i=0,len=inputs.length;i<len;i++){
		if($.trim(inputs[i].value) == ''){
			alert('请填写第一步中所有带 * 的文本框');
			return false;
		}
	}
	return true;
}

// 确定分配，检查已有问题并生成相关html代码
function confirmTables(sortJson){
	if(!AllStarWrited()) return false;
	$("input[name=sortjson]").val('');

	var tables = [], times = [], relations = [],
		table, showfield, html = '', maintable, max = 1,
		trs = $('#search_table tbody tr'),
		aSortField = [];

	// 确定主要查询表及所有查询表
	for(var i=0,len=trs.length;i<len;i++){
		showfield = trs[i].cells[0].firstChild.value;
		aSortField.push({key:showfield, val: trs[i].cells[1].firstChild.value});
		table = trs[i].cells[3].firstChild.value;
		if(i == 0) maintable = table;

		if(!isIn(table, tables)){ //获取所有表名，isIn为本文件的一个函数
			tables.push(table);
			times.push(1);
			relations.push({table: table, show: showfield, select: trs[i].cells[4].firstChild.value});
		}else{   //获取所有表出现次数，以确定主表（出现次数最多）
			for(var h=0;h<tables.length;h++){
				if(table == tables[h]){
					times[h] = times[h] + 1;
					if(times[h] > max){
						max = times[h];
						maintable = tables[h];
					}
				}
			}
		}

		// 保存主要查询表在隐藏input中
		$('#maintable').val(maintable);
		$('#maintable2').html(maintable);
	}
	// 获取表的所有字段
	var allFields = tables.join(',');

	//添加排序，条件字段
	var sSortHtml = '';
	if(typeof sortJson == 'object'){
		for(var field in sortJson){
			if(isNaN(field)){
				sSortHtml += '<li><p style="display:none">'+field+':'+sortJson[sortJson[field]]+'</p><div><font>'+sortJson[sortJson[field]]+'</font></div></li>';
			}
		}
	}else{
		for(var i=0,len=aSortField.length;i<len;i++){
			sSortHtml += '<li><p style="display:none">'+aSortField[i].key+':'+aSortField[i].val+'</p><div><font>'+aSortField[i].val+'</font></div></li>';
		}
	}

	$('#searchList').html(sSortHtml);
	var saveOrder = function() {
	    var data = $("#searchList li").map(function() { return $(this).children().html(); }).get();
	    $("input[name=sortjson]").val(data.join(","));
	};
	$("#searchList, #searchList2").dragsort({ dragSelector: "div", dragBetween: true, dragEnd: saveOrder, placeHolderTemplate: "<li class='placeHolder'><div></div></li>" });
	if(sortJson) return;

	// 异步获取表字段并放入表结构中
	$.get(TP_APP + '/Search/getFields/tables/' + allFields, function(response){
		response = $.parseJSON(response);

		if(response.data !== 0){
			if(tables.length > 1){ // 如果有多个表
				var table_fields = response;

				// 生成第二步中的table
				var all_html = [], current_table, p, k, maintableIndex;

				// 所有表的字段html代码
				for(var u=0,ulen=table_fields.length;u<ulen;u++){
					current_table = table_fields[u];

					all_html[u] = "<select>";
					for(var f=0,flen=current_table.length;f<flen;f++){
						all_html[u] += "<option value='"+ current_table[f] +"' ";

						//自动关联
						if(relations[u].table == maintable){
							maintableIndex = u;
							//默认与主表后面的表关联 u+1
							if(relations[u+1] && relations[u+1].show == current_table[f]){
								all_html[u] += "selected";
							}
						}else if(current_table[f] == relations[u].select){
							all_html[u] += "selected";
						}
						all_html[u] += ">" + current_table[f] + "</option>";
					}
					all_html[u] += "</select>";

					if(u > 1){
						var showSelectIndex = u + table_fields.length;
						all_html[showSelectIndex] = all_html[maintableIndex].replace(/selected/, '').replace(new RegExp("(value='"+relations[u].show+"')"), '$1 selected');
					};
				}

				for(var i=0,len=tables.length;i<len;i++){
					if(tables[i] == maintable){
						k = i;
					}
					html += "<tr><td><input style='width:150px;' onclick='confirmMainTable(this);' type='button' value='"+ tables[i] +"'></td>";

					// 表字段的select
					html += "<td>" + all_html[i] + "</td>";

					// 相关表的select
					html += "<td><select onchange='changeReTable(this);' >";
					for(var j=0;j<len;j++){
						if(j != i){
							html += "<option value='"+ tables[j] +"'>"+ tables[j] +"</option>";
						}
					}
					html += "</select></td>";

					// 表字段的select
					// 第一个表默认取第二个表的字段，其他表默认取第一个表的字段
					p = (i > 1) ? i + tables.length :
						(i == 0) ? 1 : 0;
					html += "<td>" + all_html[p] + "</td></tr>";
				}

				$('#tables tbody').html(html);

				// 使主要查询表的数据不可用，不用提交
				var tr = $('#tables tbody tr').eq(k);
				tr.find('input').attr('disabled', 'disabled');
				tr.find('select').attr('disabled', 'disabled');

				$('#oneTable').css('display', 'none');
				$('#tables').css('display', '');
			}else{ // 如果只有一个表
				$('#oneTable').css('display', '');
				$('#tables').css('display', 'none');
			}

			$('#relate_tables').css('display', '');

		}else{// 改变错误字段、表的文本框背景
			navTabAjaxDone(response);
			if($('#relate_tables').css('display') != 'none'){
				$('#relate_tables').css('display', 'none');
				$('#tables tbody').html('');
			}
		}
	});
}

// 更改主要查询表并将其保存在隐藏的input中
function confirmMainTable(input){
	$('#relate_tables input').removeAttr('disabled');
	$('#relate_tables select').removeAttr('disabled');

	var tr = $(input).parent().parent();
	tr.find('input').attr('disabled', 'disabled');
	tr.find('select').attr('disabled', 'disabled');

	$("#maintable2").text(input.value);
	$("#maintable").val(input.value);
}

// 覆盖dwz内置的AJAX中的dwzPageBreak方法，注释中的为修改内容
// 确保搜索字段能够以hidden的input形式存在页面中
// 找不到 $('#search_form2') 不影响其他操作
function dwzPageBreak(options){
	var op = $.extend({ targetType:"navTab", rel:"", data:{pageNum:"", numPerPage:"", orderField:"", orderDirection:""}, callback:null}, options);
	var $parent = op.targetType == "dialog" ? $.pdialog.getCurrent() : navTab.getCurrentPanel();
	if (op.rel) {
		//start修复左右树结构  by wangcheng
		if(options.data.realnavTab && options.data.refreshtabs){
			$parent = navTab._getPanels().eq(navTab._currentIndex-1);
		}//end 
		var $box = $parent.find("#" + op.rel);
		// by 杨东 如果参数是ID刷新 则刷新根据rel值进行刷新
		if(options.data.refreshtabsbyid){
			$box = $("#" + op.rel);
		}
		var form = _getPagerForm($box, op.data);
		if (form) {

			/* change */
			if($('#search_form2').length > 0){
				var search_flag = "<div id='search_form2' style='display:none;'>" + $('#search_form2').html() + "</div>";
			}
			/* change */
			
			$box.loadUrl($(form).attr("action"), $(form).serializeArray(), function(){
					$box.find("[layoutH]").layoutH();

					/* change */
					if(search_flag != null){
						$(search_flag).appendTo($box.find('#pagerForm'));
					}
					/* change */
				}
			);
//			$box.ajaxUrl({
//				type:"POST", url:$(form).attr("action"), data: $(form).serializeArray(), callback:function(){
//					$box.find("[layoutH]").layoutH();
//					
//					/* change */
//					if(search_flag != null){
//						$(search_flag).appendTo($box.find('#pagerForm'));
//					}
//					/* change */
//				}
//			});
		}
	} else {
		var form = _getPagerForm($parent, op.data);
		var params = $(form).serializeArray();

		if (op.targetType == "dialog") {
			if (form) $.pdialog.reload($(form).attr("action"), {data: params, callback: op.callback});
		} else {
			if (form) navTab.reload($(form).attr("action"), {data: params, callback: op.callback});
		}
	}
}

// 改变相关表的选择
function changeReTable(sel){
	var table = $("#tables input[value='"+ sel.value +"']");
	var html = $(table).parent().next().html();
	var target = $(sel).parent().next();
	target.html(html);
	target.children().removeAttr('disabled');
}

// 保存搜索条目，即搜索模板
function searchSave(form,callback){

	var $form=$(form);
	if(!AllStarWrited() || !$form.valid()){
		return false;
	}else{
		// 生成字段信息并保存在隐藏的input中
		var trs = $('#search_table tbody tr');
		var fields = '', showfield, table, relatetables = '';

		for(var i=0,len=trs.length;i<len;i++){
			showfield = trs[i].cells[0].firstChild.value;
			table = trs[i].cells[3].firstChild.value;

			// 字段信息保存形式： 显示字段-字段名称-查找方式-表名-查找字段
			fields += showfield + "-" + trs[i].cells[1].firstChild.value;
			fields += "-" + trs[i].cells[2].firstChild.value;
			fields += "-" + table + "-" + trs[i].cells[4].firstChild.value + ";;";
		}
		$('#fields').val(fields);

		// 生成表关联信息并保存在隐藏的input中
		trs = $('#relate_tables tbody tr');
		for(var i=0,len=trs.length;i<len;i++){
			for(var j=0;j<4;j++){
				var input = trs[i].cells[j].firstChild;
				if($(input).attr('disabled') === 'disabled'){
					break;
				}
				relatetables += input.value;

				if(j < 3){ //表关联信息保存格式： 表名-字段名-表名-字段名
					relatetables += '-';
				}else{
					relatetables += ';';
				}
			}
		}
		$('#relatetables').val(relatetables);
	}

	// 保存搜索条目，即搜索模板
	var $callback=callback||DWZ.ajaxDone;
	if(!$.isFunction($callback))$callback=eval('('+callback+')');
	$.ajax({
		type:form.method||'POST',
		url:$form.attr("action"),
		data:$form.serializeArray(),
		dataType:"json",
		cache:false,
		success:function(response){
			var j = DWZ.jsonEval(response);
			if( j.checkfield!="" ){$form.find("input[name='"+j.checkfield+"']").val(j.data);}
			$callback(response);
		},
		error:DWZ.ajaxError
	});
	return false;
}

// 开始模糊搜索
function searchSubmit(form){
	var $box = navTab.getCurrentPanel();
	var $form = $box.find('#pagerForm:visible');
	var dialog = $('.dialogContent:visible');
	/* 判断 有dialog的情况 */
	if(dialog.length > 1){//1个dialog是搜索框，一个是返回数据的dialog
		dialog.each(function(i){
			var _this = $(this);
			if(_this.find('#search_form').length == 0){//不是搜索框
				$form = _this.find('#pagerForm:visible');//在里面再确认form
				$box = $form.parents("div.dialogContent:first");//确认返回数据的div
				return false;
			}
		});
	} else {
		/* 判断是否有多tabs */
		$box = $form.parent();//正常的
		//var tabIndex = $box.find('.tabsHeaderContent li.selected').index();
		//var $relid = $box.find('.navTab-tab li.selected').attr('tabid');
		/* $box中第一个为大的div显示层，第二个为tabs下的div显示层 */
		//$box =$box.find('.tabsContent div').eq(tabIndex);
		var $treeleft = $box.find('div.treeleft')
		if($treeleft.length > 0){
			$box = $box.find('div.treeleft').next();
		}
	}
	var search_form2 = $box.find('#search_form2');
	var datas = $('#search_form').serializeArray();
	var data = '';
	for(var i=0,len=datas.length;i<len;i++){
		data += "<input type='hidden' name='"+ datas[i].name +"' value='" + datas[i].value + "' />";
	}
	var search_flag = "<div id='search_form2' style='display:none;'>" + data + "</div>";

	// 将当前填写的条件放置于主要页面的form中
	if(search_form2.length > 0){
		search_form2.html(data);
	}else{
		$(search_flag).appendTo($form);
	}
	$box.ajaxUrl({
		type:"POST", url:$form.attr("action"), data: $form.serializeArray(), callback:function(){
			$box.find("[layoutH]").layoutH();
			// 保证搜索窗口关闭后，搜索条件仍在，可以翻页操作
			$(search_flag).appendTo($box.find('#pagerForm'));
		}
	}); 
	//直接关闭窗口  by renl
	$.pdialog.close($.pdialog.getCurrent());
	return false;
}

// 开始模糊搜索2
function searchSubmit2(form){
	var $form = $('.dialog:visible').last().prev().find('#pagerForm');
	var datas = $('#fifter2').parent().serializeArray();
	var data = '';

	for(var i=0,len=datas.length;i<len;i++){
		data += "<input type='hidden' name='"+ datas[i].name +"' value='" + datas[i].value + "' />";
	}
	var search_flag = "<div id='search_form3' style='display:none;'>" + data + "</div>";

	// 将当前填写的条件放置于主要页面的form中
	if($('#search_form3').length > 0){
		$('#search_form3').html(data);
	}else{
		$(search_flag).appendTo($form);
	}

	var $box = $form.parent();
	$box.ajaxUrl({
		type:"POST", url:$form.attr("action"), data: $form.serializeArray(), callback:function(){
			$box.find("[layoutH]").layoutH(); 
			// 保证搜索窗口关闭后，搜索条件仍在，可以翻页操作
			$(search_flag).appendTo($box.find('#pagerForm'));
		}
	});
	return false;
}
/* ============= end ================= */
/*==========2012年8月20日============= */
/*=======新增动态搜索功能代码段结束=== */
/* ============= end ================= */

/**
 * @author xyzhanjiang
*/
var xy = {
	// 首页弹出层定位
	"appPosition": function(obj, pxy){
		if(obj.length > 0){
			var objWidth = 0, objHeight = 0, objTop = 0, objLeft = 0, objRight = 0;
			var objStyle = obj[0].style;
			var wrapWidth = $('body').width();
	
			objLeft = pxy.left;
			objTop = pxy.top + 80;
	
			objWidth = obj.children('ul').children('li').length;
			if(objWidth > 5){
				objHeight = 192;
			} else {
				objHeight = 96;
			}
			objWidth = Math.min(objWidth * 108, 570);
			if(objWidth > wrapWidth){
				objWidth = wrapWidth;
			}
			if(objWidth + objLeft > wrapWidth){
				objLeft = objLeft - Math.min(objWidth - 108, objLeft);
			}
	
			objStyle.width = objWidth + 'px';
			objStyle.height = objHeight + 'px';
			objStyle.left = objLeft + 'px';
			objStyle.top = objTop + 'px';
		}
	},
	// 开始菜单定位
	"startMenuPosition": function(obj, pxy){
		if(obj.length > 0){
			var objHeight = 0, objTop = -1, unitH = 32;
			var objStyle = obj[0].style;
			var wrapHeight = $(window).height();

			objHeight = obj.children('li').length;
			objHeight = objHeight * unitH;

			if(objHeight + pxy.top > wrapHeight){
				objTop = objTop - Math.min(objHeight - unitH, Math.floor(pxy.top/unitH) * unitH);
				objStyle.top = objTop + 'px';
			}
		}
	},
	"changeTab": function(a, e){
		e = e || window.event;
		if(e.preventDefault){
			e.preventDefault();
		} else {
			e.returnValue = false;
		}
		var tabIndex = a.href.split('#')[1];
		var $wrap = $(a).parents('.xyTab');
		var $tabList = $wrap.find('.xyTabList');
		var $tabContent = $wrap.find('.xyTabContent');
		$tabList.filter('.xyTabListCu').removeClass('xyTabListCu').end().eq(tabIndex).addClass('xyTabListCu');
		$tabContent.filter('.xyTabContentCu').removeClass('xyTabContentCu').end().eq(tabIndex).addClass('xyTabContentCu');
	},
	"showNext": function(a){
		$(a).next().show();
	},
	"hideNext": function(a){
		$(a).next().hide();
	}
};
(function($){

	var scrollbarWidth = 0;

	// scrollbar
	function getScrollbarWidth() 
	{
		if (scrollbarWidth) return scrollbarWidth;
		var div = $('<div style="width:50px;height:50px;overflow:hidden;position:absolute;top:-200px;left:-200px;"><div style="height:100px;"></div></div>'); 
		$('body').append(div); 
		var w1 = $('div', div).innerWidth(); 
		div.css('overflow-y', 'auto'); 
		var w2 = $('div', div).innerWidth(); 
		$(div).remove(); 
		scrollbarWidth = (w1 - w2);
		return scrollbarWidth;
	}
	
	$.fn.tableScroll = function(options)
	{
		if (options == 'undo')
		{
			var container = $(this).parent().parent();
			container.find('.tablescroll_head thead').prependTo(this);
			container.find('.tablescroll_foot tfoot').appendTo(this);
			container.before(this);
			container.empty();
			return;
		}

		var settings = $.extend({},$.fn.tableScroll.defaults,options);

		settings.scrollbarWidth = getScrollbarWidth();

		this.each(function()
		{
			var flush = settings.flush;
			
			var tb = $(this).addClass('tablescroll_body');

			var wrapper = $('<div class="tablescroll_wrapper"></div>').insertBefore(tb).append(tb);

			// check for a predefined container
			if (!wrapper.parent('div').hasClass(settings.containerClass))
			{
				$('<div></div>').addClass(settings.containerClass).insertBefore(wrapper).append(wrapper);
			}

			var width = settings.width ? settings.width : tb.outerWidth();

			wrapper.css
			({
				'width': width+'px',
				'height': settings.height+'px',
				'overflow-y': 'auto',
				'overflow-x': 'hidden'
			});

			tb.css('width',width+'px');

			// with border difference
			var wrapper_width = wrapper.outerWidth();
			var diff = wrapper_width-width;

			// assume table will scroll
			wrapper.css({width:((width-diff)+settings.scrollbarWidth)+'px'});
			tb.css('width',(width-diff)+'px');

			if (tb.outerHeight() <= settings.height)
			{
				wrapper.css({height:'auto',width:(width-diff)+'px'});
				flush = false;
			}

			// using wrap does not put wrapper in the DOM right 
			// away making it unavailable for use during runtime
			// tb.wrap(wrapper);

			// possible speed enhancements
			var has_thead = $('thead',tb).length ? true : false ;
			var has_tfoot = $('tfoot',tb).length ? true : false ;
			var thead_tr_first = $('thead tr:first',tb);
			var tbody_tr_first = $('tbody tr:first',tb);
			var tfoot_tr_first = $('tfoot tr:first',tb);

			// remember width of last cell
			var w = 0;

			$('th, td',thead_tr_first).each(function(i)
			{
				w = $(this).width();

				$('th:eq('+i+'), td:eq('+i+')',thead_tr_first).css('width',w+'px');
				$('th:eq('+i+'), td:eq('+i+')',tbody_tr_first).css('width',w+'px');
				if (has_tfoot) $('th:eq('+i+'), td:eq('+i+')',tfoot_tr_first).css('width',w+'px');
			});

			if (has_thead) 
			{
				var tbh = $('<table class="tablescroll_head" cellspacing="0"></table>').insertBefore(wrapper).prepend($('thead',tb));
			}

			if (has_tfoot) 
			{
				var tbf = $('<table class="tablescroll_foot" cellspacing="0"></table>').insertAfter(wrapper).prepend($('tfoot',tb));
			}

			if (tbh != undefined)
			{
				tbh.css('width',width+'px');
				
				if (flush)
				{
					$('tr:first th:last, tr:first td:last',tbh).css('width',(w+settings.scrollbarWidth)+'px');
					tbh.css('width',wrapper.outerWidth() + 'px');
				}
			}

			if (tbf != undefined)
			{
				tbf.css('width',width+'px');

				if (flush)
				{
					$('tr:first th:last, tr:first td:last',tbf).css('width',(w+settings.scrollbarWidth)+'px');
					tbf.css('width',wrapper.outerWidth() + 'px');
				}
			}
		});

		return this;
	};

	// public
	$.fn.tableScroll.defaults =
	{
		flush: true, // makes the last thead and tbody column flush with the scrollbar
		width: null, // width of the table (head, body and foot), null defaults to the tables natural width
		height: 100, // height of the scrollable area
		containerClass: 'tablescroll' // the plugin wraps the table in a div with this css class
	};

/**
 * .selectButton
 *
 * Version: 1.0.1
 * Updated: 2013-09-03
 * Author: me
 *
 **/
	$.fn.selectButton = function(options) {
	  return this.each(function(){
		var $ = jQuery;
		var select = $(this);
		var multiselect = select.attr('multiple');
		select.hide();
	
		var buttonsHtml = $('<div class="selectButton"></div>');
		var selectIndex = 0;
		var addOptGroup = function(optGroup){
		  if (optGroup.attr('label')){
			buttonsHtml.append('<strong>' + optGroup.attr('label') + '</strong>');
		  }
		  var ulHtml =  $('<ul class="select-buttons">');
		  optGroup.children('option').each(function(){
			var liHtml = $('<li></li>');
			if(optGroup.children('option').length > 1){
				if(selectIndex == 0){
					liHtml.addClass('first');
				} else if(selectIndex == (optGroup.children('option').length - 1)){
					liHtml.addClass('last');
				}
			} else {
				liHtml.addClass('only');
			}
			if ($(this).attr('disabled') || select.attr('disabled')){
			  liHtml.addClass('disabled');
			  liHtml.append('<span>' + $(this).html() + '</span>');
			}else{
			  liHtml.append('<a href="#" data-select-index="' + selectIndex + '">' + $(this).html() + '</a>');
			}
	
			// Mark current selection as "selected"
			if((!options || !options.noDefault) && $(this).attr('selected')){
			  liHtml.children('a, span').addClass('selected');
			}
			ulHtml.append(liHtml);
			selectIndex++;
		  });
		  buttonsHtml.append(ulHtml);
		}
	
		var optGroups = select.children('optgroup');
		if (optGroups.length == 0) {
		  addOptGroup(select);
		}else{
		  optGroups.each(function(){
			addOptGroup($(this));
		  });
		}
	
		select.after(buttonsHtml);
	
		buttonsHtml.find('a').click(function(e){
		  e.preventDefault();
		  if($(this).hasClass('selected')){
			  return false;
		  }
		  var clickedOption = $(select.find('option')[$(this).attr('data-select-index')]);
		  if(multiselect){
			if(clickedOption.attr('selected')){
			  $(this).removeClass('selected');
			  clickedOption.removeAttr('selected');
			}else{
			  $(this).addClass('selected');
			  clickedOption.attr('selected', 'selected');
			}
		  }else{
			buttonsHtml.find('a, span').removeClass('selected');
			$(this).addClass('selected');
			clickedOption.siblings().removeAttr('selected').end().attr('selected', 'selected');
		  }
		  select.trigger('change');
		});
	  });
	};
})(jQuery);