//此方法用于报表另起窗口打开时jqGrid显示
function Merger(gridName, CellName, ConName) {
	// 得到显示到界面的id集合
	var mya = $("#" + gridName + "").getDataIDs();
	// 当前显示多少条
	var length = mya.length;
	for ( var i = 0; i < length; i++) {
		// 从上到下获取一条信息
		var before = $("#" + gridName + "").jqGrid('getRowData', mya[i]);
		// 定义合并行数
		var rowSpanTaxCount = 1;
		for (j = i + 1; j <= length; j++) {
			// 和上边的信息对比 如果值一样就合并行数+1 然后设置rowspan 让当前单元格隐藏
			var end = $("#" + gridName + "").jqGrid('getRowData', mya[j]);
			if (before[CellName] == end[CellName] && before[ConName] == end[ConName]) {
				rowSpanTaxCount++;
				$("#" + gridName + "").setCell(mya[j], CellName, '', {display : 'none'});
				$("#" + gridName + "").setCell(mya[i], CellName, '', '',{rowspan:rowSpanTaxCount});
			} else {
				rowSpanTaxCount = 1;
				break;
			}
		}
	}
}

// 报表检索按钮
function show_doSearch(divid, funcname, issearch) {
	var wh = "";
	$("#" + divid + " :input").each(function() {
		var name = $(this).attr('name');
		var val = $(this).val();
		if (val != "") {
			if (wh == "") {
				wh = "/" + name + "/" + val;
			} else {
				wh = wh + "/" + name + "/" + val;
			}
		}
	});
	if (issearch != 0 ) {
		if (wh == "") {
			alert('请填写检索条件');
			return false;
		}
	}
	var url = funcname + wh;
	window.open(url);
}
/**
 * +----------------------------------------------------------
 * 用于导出excel/pdf时url跳转
 * +----------------------------------------------------------
 * 
 * @author jiangx
 * @date:2013-05-7
 */
function lookupfunc_exportExcel(url, divname, reporttype, type, wh) {
	var sidx = $("#" + divname).jqGrid("getGridParam", "sortname");
	var sord = $("#" + divname).jqGrid("getGridParam", "sortorder");
	var rows = $("#" + divname).jqGrid("getGridParam", "rowNum");
	var page = $("#" + divname).jqGrid("getGridParam", "page");
	var localurl = url + "/ReportExcel/index/reporttype/" + reporttype + wh
			+ "?sidx=" + sidx + "&sord=" + sord + "&rows=" + rows + "&page="
			+ page + "&gettype=" + type;
	window.open(localurl);
}
/**
 * +----------------------------------------------------------
 * 用于导出excel/pdf时url跳转
 * +----------------------------------------------------------
 * 
 * @author jiangx
 * @date:2013-05-7
 */
function lookupfunc_exportExcelPdf(divname, reporttype, type) {
	var $box = navTab.getCurrentPanel();
	var sidx = $("#" + divname).jqGrid("getGridParam", "sortname");
	var sord = $("#" + divname).jqGrid("getGridParam", "sortorder");
	var rows = $("#" + divname).jqGrid("getGridParam", "rowNum");
	var page = $("#" + divname).jqGrid("getGridParam", "page");
	//开始时间(月份)
	var collhandledate = $box.find("input[name='collhandledate']").val();
	var overdate = $box.find("input[name='overdate']").val();
	//截止时间
	if(!overdate){
		overdate=-1;
	}
	var localurl =  TP_APP + "/ReportExcel/index/reporttype/" + reporttype
					+ "/collhandledate/" + collhandledate +"/overdate/"+ overdate +"?sidx=" + sidx + "&sord="
					+ sord + "&rows=" + rows + "&page=" + page + "&gettype=" + type;
	window.open(localurl);
}
/**
 * 
 * @param 打开地址
 * @param divname
 * @param 导出参数
 * @param type
 * @param 参数取值
 * @param name组合
 * @returns {Boolean}
 */
function lookupfunc_exportcontrolExcel(url, divname, reporttype, type, wh, colnamesdiv) {
	var $box = navTab.getCurrentPanel();
	var collhandledate = $box.find("input[name='" + wh + "']").val();
	if (collhandledate == '') {
		alert('请选择要汇总的月份');
		return false;
	}
	if ($box.find("#" + colnamesdiv).find("input[type='checkbox']:checked").length < 1) {
		alertMsg.error('请选择需要显示的字段！');
		return false;
	}
	var discolname = "";
	var showname = "";
	$box.find("#" + colnamesdiv).find("input[type='checkbox']:checked").each(
			function() {
				discolname += $(this).val() + ",";
				showname += $(this).attr("rel") + ",";
			});
	// 传入选中参数
	var sidx = $("#" + divname).jqGrid("getGridParam", "sortname");
	var sord = $("#" + divname).jqGrid("getGridParam", "sortorder");
	var rows = $("#" + divname).jqGrid("getGridParam", "rowNum");
	var page = $("#" + divname).jqGrid("getGridParam", "page");
	var localurl = url + "/ReportExcel/index/reporttype/" + reporttype
			+ "/collhandledate/" + collhandledate + "/discolname/" + discolname
			+ "?sidx=" + sidx + "&sord=" + sord + "&rows=" + rows + "&page="
			+ page + "&gettype=" + type;
	window.open(localurl);
}
/**
 * +----------------------------------------------------------
 * 用于检索重现载入jqgrid(仅限于月份检索报表)
 * +----------------------------------------------------------
 * 
 * @author renl
 * @date:2013-12-17
 */
function reportReloadGrid_doSearch($inputname, tableid, url, excelId, pdfId,
		reporttype, colnamesdiv, loadDiv) {
	// 此处可以添加对查询数据的合法验证
	var $box = navTab.getCurrentPanel();
	var discolname = "";
	var showname = "";
	var collhandledate = $box.find("input[name='" + $inputname + "']").val();
	var overdate = $box.find("input[name='overdate']").val();
	if (!overdate) {
		overdate = -1;
	}
	if (collhandledate == '') {
		alert('请选择要汇总的月份');
		return false;
	}
	$box.find("#" + tableid).jqGrid('setGridParam', {
		url : url,
		datatype : 'json',
		postData : {
			'collhandledate' : collhandledate,
			listtype : 1,
			'overdate' : overdate
		}, // 发送数据
		page : 1
	}).trigger("reloadGrid"); // 重新载入
}
// 报表颜色区分
function cellColor(gridName, CellName, type) {
	// 得到显示到界面的id集合
	var mya = $("#" + gridName + "").getDataIDs();
	// 当前显示多少条
	var length = mya.length;
	var colorArray = new Array();
	colorArray[1] = "#ecf1f2";
	colorArray[2] = "#ffffa4";
	var colorlist = new Array();
	colorlist[0] = 1;
	// task 用于任务颜色区分控制变量
	task = 1;
	if (type == 'task') {
		colorArray[1] = "ui-state-colorbad";
		colorArray[2] = "";
		for ( var i = 1; i <= length; i++) {
			var index = $("#" + CellName + mya[i - 1]).text();
			if (index == '0') {
				task = task == 1 ? 0 : 1;
			}
			if (task == 0) {
				colorlist[i - 1] = 1;
			} else {
				colorlist[i - 1] = 2;
			}
		}
	} else {
		for ( var i = 1; i < length; i++) {
			var index = $("#" + CellName + mya[i - 1]).attr("title");
			var val = $("#" + CellName + mya[i]).attr("title");
			if (val == index) {
				colorlist[i] = colorlist[i - 1];
			} else {
				if (colorlist[i - 1] == 1) {
					colorlist[i] = 2;
				} else if (colorlist[i - 1] == 2) {
					colorlist[i] = 1;
				}
			}
		}
	}
	for ( var i = 0; i < length; i++) {
		if (type == 'task') {
			$("#" + mya[i]).addClass(colorArray[colorlist[i]]);
		} else {
			$("#" + mya[i]).css("background", colorArray[colorlist[i]]);
		}

	}
}
// 报表字体颜色区分
function fontColor(gridName, CellName) {
	// 得到显示到界面的id集合
	var mya = $("#" + gridName + "").getDataIDs();
	// 当前显示多少条
	var length = mya.length;
	for ( var i = 0; i < length; i++) {
		var color = $("#" + CellName + mya[i]).attr("title");
		$("#" + mya[i]).css("color", color);
	}
}

// 不进行弹出的报表检索按钮
function reportJqGridDoSearch($this, jqgrid, url) {
	// 此处可以添加对查询数据的合法验证
	var form = $this.form;
	var data = $(form).serializeArray();
	var sidx = $("#" + jqgrid).jqGrid("getGridParam", "sortname");
	var sord = $("#" + jqgrid).jqGrid("getGridParam", "sortorder");
	var rows = $("#" + jqgrid).jqGrid("getGridParam", "rowNum");
	var page = $("#" + jqgrid).jqGrid("getGridParam", "page");
	data = $.extend({sidx:sidx,sord:sord,rows:rows,page:page},data);
	$("#" + jqgrid).jqGrid('setGridParam', {
		url : url,
		datatype : 'json',
		postData : data // 发送数据
	}).trigger("reloadGrid"); // 重新载入
}
//进行弹出的报表导出按钮
function exportJqGridDoSearch($this, jqgrid, url, exportType) {
	// 此处可以添加对查询数据的合法验证
	var form = $this.form;
	var sidx = $("#" + jqgrid).jqGrid("getGridParam", "sortname");
	var sord = $("#" + jqgrid).jqGrid("getGridParam", "sortorder");
	var rows = $("#" + jqgrid).jqGrid("getGridParam", "rowNum");
	var page = $("#" + jqgrid).jqGrid("getGridParam", "page");
	$(form).attr("action",url);
	$(form).find("input[name='sidx']").val(sidx);
	$(form).find("input[name='sord']").val(sord);
	$(form).find("input[name='rows']").val(rows);
	$(form).find("input[name='page']").val(page);
	$(form).find("input[name='exportType']").val(exportType);
	form.submit();
}