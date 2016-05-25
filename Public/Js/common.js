/**
 * 函数 验证码生成
 * @author  wangcheng
 * date:2012-04-10
 */
function fleshVerify() {
	// 重载验证码
	$('#verifyImg').attr("src",
			TP_APP + "/Public/verify/" + new Date().getTime());
}
/**
 *将字符转换为数组 
 */
String.prototype.toArray=function(){
    return this? isNaN(this) ? this:'' : '';
}

/**
 *	转换为26进制
 * @param strin config 用于替换显示26进制的基本字符，长度必须为26否则无效 
 */
String.prototype.toLetter26=function(config){
    var col = parseInt(this);
     if(col > 0 ){
         var str     = "zabcdefghigklmnopqrstuvwxy";
         if(config && config.length == 26){
             str = config;
         }
        var arr = str.toArray();
        var col_str = "";
        do
        {
            col_tmp  = col % 26;
            col      = col_tmp == 0 ? parseInt(col/26) - 1 : parseInt(col / 26);
            col_str  = arr[col_tmp]+col_str;
        }while( col );
        return  col_str;
    }
    return '';
}

/*
function dialogAjaxMenu(json) {
	dialogAjaxDone(json);
	if (json.statusCode == DWZ.statusCode.ok) {
		$("#sidebar").loadUrl(TP_APP + "/Public/menu/pid/" + json.menuid);
	}
}
function navTabAjaxMenu(json) {
	navTabAjaxDone(json);
	if (json.statusCode == DWZ.statusCode.ok) {
		$("#sidebar").loadUrl(TP_APP + "/Public/menu/pid/" + json.menuid);
	}
}

function dialogAjaxMiwh(json) {
	dialogAjaxDone(json);
	var $url = $(this).attr("url").split("/");
	$refreshUrl = $url[$url.length - 2];
	var $tabsChange = $url[$url.length - 1];
	if (json.statusCode == DWZ.statusCode.ok) {
		$("#" + $tabsChange).loadUrl(TP_APP + "/" + $refreshUrl);
	}
}
function navTabAjaxMiwh(json) {
	navTabAjaxDone(json);
	var $url = $(this).attr("url").split("/");
	$refreshUrl = $url[$url.length - 2];
	var $tabsChange = $url[$url.length - 1];
	if (json.statusCode == DWZ.statusCode.ok) {
		$("#" + $tabsChange).loadUrl(TP_APP + "/" + $refreshUrl);
	}
}*/




/**
 * @Title:BusinesscallFunction
 * @Description: todo(商机定时更新是否超期)
 * @author  xiayq
 * @return  boolean
 * date:2015-04-20
 */

//var timeTask=setInterval(function(){
//    var date=new Date();
//    var h=date.getHours();
//    var m=date.getMinutes();
//    var s=date.getSeconds();
//    if(h==15&&m==41&&s==0){
//    	BusinesscallFunction();
//                                                                  
//    }
//},1000);
var timeTask = setInterval(BusinesscallFunction,7200000);
function BusinesscallFunction(){
	$.ajax({
		type: "POST",
		url: TP_APP + "/MisSaleBusiness/lookupSelectTime",
		data:"",
		dataType: "json",
		async:false,
		global: false,
		success: function (data){
			console.log(data);
		},
	});
}


/**
 * @Title:ShbCallFunction
 * @Description: todo(发布任务是否超期)
 * @author  xiayq
 * @return  boolean
 * date:2015-04-20
 */

var shbtimeTask = setInterval(ShbCallFunction,7200000);
function ShbCallFunction(){
	$.ajax({
		type: "POST",
		url: TP_APP + "/MisAutoShb/lookupSelectTime",
		data:"",
		dataType: "json",
		async:false,
		global: false,
		success: function (data){
			console.log(data);
		},
	});
}



/**
 * @Title:userAlert
 * @Description: todo( 及时提醒-暂时用ajax 请求方式)
 * @author  wangcheng
 * @return  boolean
 * date:2013-06-10
 */

var useralert = setInterval(function(){
selectTime();
userRemind();
},10000000000000);
function selectTime(){
	//读取数据库时间
	$.ajax({
		type: "POST",
		url: TP_APP + "/Index/lookupSelectTime",
		data:"",
		dataType: "json",
		async:false,
		global: false,
		success: function (data){
			var d = new Date().getTime();
			var d2 = d.toString().substring(0,10);
			var d1 = data;   
			if(d1<d2){    
				userAlert(); 
			}
		},
	});
}
function changelookupSetTime(){
	//存入时间弹框确定方法
	//取得延时时间
	nexttime=$("#nexttime").val(); 
	$.ajax({
		type: "POST",
		url: TP_APP + "/Index/lookupSetTime",
		data:{'nexttime':nexttime},
		dataType: "json",
		async:false,
		global: false,
		success: function (data){
			 alertMsgTip.close();
		},
	}); 
}

function userRemind(){  
	$.ajax({
		type: "POST",
		url: TP_APP + "/MisSystemDataRemindMas/lookupgetRemindCount",
		data:"",
		dataType: "json",
		async:true,
		timeout: 8000,
			global: false,
		success: function (d){   
			if(d){
				$(".remindaspan").show();
			}else{
				$(".remindaspan").hide(); 
			}
		}

	});
}
 
function userAlert(){
	var html="";
	var datalist = "";
	$.ajax({
		type: "POST",
		url: TP_APP + "/Index/getAllScheduleList/nochangelogintime/1",
		data:"",
		dataType: "json",
		async:true,
		timeout: 8000,
		success: function (d){
			if (!d) {
				return false;
			}
			html=d.html;
			datalist = d.datalist;
			if (html) {
				if( $("#alertMsgBoxTip").length >0 ){
					var div = $("#alertMsgBoxTip").find(".msg");
					div.empty().html(html).initUI();
				}else{
					alertMsgTip.info(html, {
						okCall: function(){
							$.ajax({
								type: "POST",
								url: TP_APP + "/Index/setDbhaSmsgType/nochangelogintime/1",
								data:{datalist:datalist},
								async:false,
								success: function (succ){
									//将取得时间数据放入数据库
									var nexttime=$("#nexttime").val();
									$.ajax({
										type: "POST",
										url: TP_APP + "/Index/lookupSetTime/nexttime/"+nexttime,
										data:"",
										dataType: "json",
										async:false,
										global: false,
										success: function (data){
											$.cookie('test', '123',{expires: 7, path: '/'});
										},
									});
									
									if (succ == '0') {
										return false;
									}
								},
								global: false
							});
							
							
						}
					});
				}
			}
		},
		global: false
	});
}

function ConfirmCommit(form, m) {
	var $form = $(form);
	//如果是确认保存则将确认保存字段修改为1.
	var html = "<input type='hidden' name='operateid' value='1'/>";
	$(html).appendTo($form);
	return validateCallback($form, navTabAjaxDone);
	//$form.submit();
}
/**
 * @Title: StartProcess
 * @Description: todo(启动流程)
 * @author liminggang
 * @parameter form  string  表单
 * @parameter m     string  模型
 * @return    JSION、boolean
 * @date 2012-2-26 下午2:13:26
 * @throws
 */

function StartProcess(form, m) {
	var $form = $(form);
	//为当前form表单定制一个存储当前url的标志
	startprocessurlid=$form.attr("id")||("form_"+Math.round(Math.random()*10000000));
	$form.attr("id",startprocessurlid);
	//获取当前请求的action值
	var s = $form.attr("action");
	var html = "<input type='hidden' name='refreshtabs[StartProcessUrl]' value='"+s+"'/>";
	$(html).appendTo($form);
	//改变当前action的url
	$form.attr("action", TP_APP + "/" + m + "/startprocess/navTabId/" + m);
	//return validateCallback($form, startProcessRefreshTabs);
	
	var thisForm = $(this.form);
	$form.find("div.js-auditDiv").remove();
	$.ajax({
		type: "POST",   
		url: TP_APP+"/Public/auditStartDiv",
		data: {},
		async:true,
		success: function(data){
			var con = $(data);
			con.initUI();
			$form.append(con);
		},
		global: false
	});
	
	
	
	
}

//变更流程
function changeRecord(form,m){
	var $form = $(form);
	//为当前form表单定制一个存储当前url的标志
	startprocessurlid=$form.attr("id")||("form_"+Math.round(Math.random()*10000000));
	$form.attr("id",startprocessurlid);
	//获取当前请求的action值
	var s = $form.attr("action");
	var html = "<input type='hidden' name='refreshtabs[StartProcessUrl]' value='"+s+"'/>";
	$(html).appendTo($form);
	//改变当前action的url
	$form.attr("action", TP_APP + "/" + m + "/lookupUpdateProcess/navTabId/" + m);
	return validateCallback($form, startProcessRefreshTabs);
}


// 启动流程判断
function startProcessRefreshTabs(json) {
	DWZ.ajaxDone(json);
	var refreshtabs = json.refreshtabs;
	if(json.statusCode==DWZ.statusCode.ok){
		if(json.navTabId){ navTab.reloadFlag(json.navTabId);}
		if("closeCurrent"==json.callbackType){
			setTimeout(function(){navTab.closeCurrentTab(json.navTabId);},100);}
    }else{
		if(refreshtabs&&refreshtabs.StartProcessUrl!=null){
			//启动失败，还原actino的url内容
			$("#"+startprocessurlid).attr("action", refreshtabs.StartProcessUrl);
		}
    }
}

function exportBysearch(form,url,url2,pagetype){
	var t = (pagetype===undefined) ? "navTab":pagetype;
	var currId=new Date().getTime();
	var form_s = $('<form action="'+url+'" method="post" id="'+currId+'"></form>');
	var $form = $(form);
	var $parent = t == "dialog" ? $.pdialog.getCurrent() : navTab.getCurrentPanel();
	var l=$form.attr("rel");
	if (l!="") {
		var $byrel_form = $parent.find("#" +l);
		if ($byrel_form) {
			var s1= $byrel_form.find(":input").clone();
			form_s.append(s1);
		}
	}
	var s= $form.find(":input").clone();
	form_s.append(s);
	form_s.append('<input name="export_bysearch" value="1">');
	form_s.appendTo("body");
	form_s.css('display','none');
	//form_s.submit();
	
	var options = {};
	options.param={formid:currId};
	options.mask = "true";
	$.pdialog.open(url2,"_blank", "请选择导出字段",options);
	return false;
}

function exportBysearchOut(form,formid){
	var $form = $(form);
	var s= $form.find(":input").clone();
	$("#"+formid).append(s);
	$("#"+formid).submit();
	return false;
}

// dialog启动流程
function StartProcess_dialog(form, m) {
	var $form = $(form);
	//navTab.reloadFlag(m);
	var opendialog=false;
	var postparam={};
	var data = $form.serializeArray();
	$.ajax({
		type:'POST',
		url:TP_APP + '/' + m + '/sureProcess',
		data:data,
		dataType:"json",
		async:false,
		cache:false,
		global:false,
		success:function(respon){
			var json=DWZ.jsonEval(respon);
			if (json.statusCode == DWZ.statusCode.ok) {
				postparam=json.data;
				opendialog = true;
			}
		},
		error:DWZ.ajaxError
	});
	if(opendialog){
		var role_duty="";
		if(postparam.role) role_duty="/role/"+postparam.role;
		if(postparam.duty) role_duty+="/duty/"+postparam.duty;
		if( role_duty ){
			var h=$(form).find("#auditselectoption").attr("href");
			$(form).find("#auditselectoption").attr("href",h+role_duty).click();
		}else{
			$(form).find("#auditselectoption").click();	
		}
	}else{
		$form.attr("action", TP_APP + "/" + m + "/startprocess/navTabId/" + m);
		return validateCallback($form, dialogAjaxDone);
	}
	
}

function auditOpion(arg, obj){
	var $form =$(obj).parent("form")
	var m= arg.modulename_auditoption;
	var t= arg.audittype;
	$form.attr("action", TP_APP + "/" + m + "/startprocess/navTabId/" + m);
	if(t=="dialog"){
		validateCallback($form, dialogAjaxDone);
	}else{
		return validateCallback($form, startProcessRefreshTabs);
	}
}

function auditOpionPorcess(arg, obj){
	var $form =$(obj).parent("form")
	var t= arg.audittype;
	if(t=="dialog"){
		return validateCallback($form, dialogAjaxDone);
	}else{
		return validateCallback($form, refreshtabsAudit);
	}
}


// dialog启动流程
function auditProcess(form, m,t) {
	var t = (undefined === t) ? 'navTab' : 'dialog';
	var $form = $(form);
	var opendialog=false;
	var postparam={};
	var data = $form.serializeArray();
	$.ajax({
		type:'POST',
		url:TP_APP + '/' + m + '/sureProcess/auditprocessing/1',
		data:data,
		dataType:"json",
		async:false,
		cache:false,
		global:false,
		success:function(respon){
			var json=DWZ.jsonEval(respon);
			if (json.statusCode == DWZ.statusCode.ok) {
				postparam=json.data;
				opendialog = true;
			}
		},
		error:DWZ.ajaxError
	});
	if(opendialog){
		var role_duty="";
		if(postparam.role) role_duty="/role/"+postparam.role;
		if(postparam.duty) role_duty+="/duty/"+postparam.duty;
		if( role_duty ){
			var h=$(form).find("#auditselectoption").attr("href");
			$(form).find("#auditselectoption").attr("href",h+role_duty).click();
		}else{
			$(form).find("#auditselectoption").click();	
		}
		return false;
	}else{
		return validateCallback($form, refreshtabsAudit);
	}
	
}

/*
 * 中标 、二次回标、此标作废  提交控制器js
 * author: liminggang
 * data: 2013-4-10
 * form: 当前form表单，
 * m   ：传入的模型
 * step : 1 表示中标，2表示二次回标，3表示此表作废
 */
function BackAuditView(form, m, step, msg) {
	if(msg == undefined){
		msg = "您确定要操作当前信息吗？";
	}
	alertMsg.confirm(msg, {
		okCall: function(){
			var $form = $(form);
			$form.attr("action", TP_APP + "/" + m + "/update/step/"+step+"/navTabId/" + m);
			return validateCallback($form, refreshtabsAudit);
		}
	});
}
// 流程回退
function BackProcess(form, m) {
	var $form = $(form);
	$form.attr("action", TP_APP + "/" + m + "/backprocess/navTabId/" + m);
	return validateCallback($form, navTabAjaxDone);
}


// 审核界面刷新
function refreshtabsAudit(json) {
	var refreshtabs = json.refreshtabs;
	var types = refreshtabs.type;
	if (types == 'dialog') {
		dialogAjaxDone(json);
	} else {
		navTabAjaxDone(json);
	}
	if (json.statusCode == DWZ.statusCode.ok) {
		if (types == 'navtab' || types == 'navTab'){ navTab.closeCurrentTab();}
		dwzPageBreak({targetType:"navTab",rel:"MisWorkExecutingbox",data:{realnavTab:false}});
	}
}
// 刷新tabs
function refreshtabs(json) {
	var refreshtabs = json.refreshtabs;
	var types = refreshtabs.type;
	if (types == 'dialog') {
		dialogAjaxDone(json);
	} else {
		navTabAjaxDone(json);
	}
	if (json.statusCode == DWZ.statusCode.ok) {
		if (types == 'navtab' || types == 'navTab'){ navTab.closeCurrentTab();}
		var tabids = refreshtabs.tabid;
		var urls = refreshtabs.url;
		var titles = refreshtabs.title;
		var postdata =DWZ.jsonEval(refreshtabs.data);
		if( json.data ) postdata.jsondata = json.data;
		navTab.openTab(tabids, urls, {title : titles,fresh : true,data:postdata});
	}
}
/**
 * 修复此处错误
 * 如果model自动验证失败，就会关闭当前表单
 * 让用户已经填写好的数据消失。这是最痛苦的事情
 */
function refreshtabs_afteradd(json) {
	DWZ.ajaxDone(json);
	var refreshtabs = json.refreshtabs;
	var types = refreshtabs.type;
	if (json.statusCode == DWZ.statusCode.ok) {
		if (types == 'dialog') {
			dialogAjaxDone(json);
			$.pdialog.closeCurrent();
		} else {
			navTabAjaxDone(json);
			navTab.closeCurrentTab();
		}
		var tabids = refreshtabs.tabid;
		var urls = refreshtabs.url+json.data;
		var titles = refreshtabs.title;
		var postdata =DWZ.jsonEval(refreshtabs.data);
		navTab.openTab(tabids, urls, {title : titles,fresh : true,data:postdata});
	}
}
function refreshtabs_navTabafteradd(json) {
	var refreshtabs = json.refreshtabs;
	navTabAjaxDone(json);
	if (json.statusCode == DWZ.statusCode.ok) {
		var urls = refreshtabs.url+json.data;
		navTab.reload(urls);
		$(".navTab-tab").find("li.selected").attr("url",urls);
	}
}
function button_refresh(json,d){
	DWZ.ajaxDone(json);
	if (json.statusCode == DWZ.statusCode.ok) {
		var d=DWZ.jsonEval(d);
		var tabids = d.tabid;
		var urls = d.url;
		var titles = d.title;
		var postdata =DWZ.jsonEval(d.data);
		if( json.data ) postdata.jsondata = json.data;
		navTab.openTab(tabids, urls, {title : titles,fresh : true,data:postdata});
	}
}
/**
 * 打开tab的公用方法。 为了方便Bi用。
 */
function openNavTab(tabid,url,title){
	navTab.openTab(tabid, url, {title : title,fresh : true});
}
/**
 * 生成图形报表
 * @param swfSource SWF路径
 * @param chartId 图形报表所在div的ID
 * @param chartDataUrl 图形报表所请求的URL
 * @param w 图形报表的宽度
 * @param h 图形报表的高度
 */
function chart(swfSource,chartId,chartDataUrl,w,h) {
    var chart = new FusionCharts(swfSource, 'myChartId', w, h);
    chart.setDataURL(chartDataUrl);
    chart.render(chartId);
}
/**
 * 类型修改时操作
 * 杨东
 */ 
function onchangeType($this){
	// 当前对象
	var $ref =$($this);
	// 当前模型
	var $model = $ref.attr('model');
	// 当前选中的值
	var $val = $ref.val();
	// 当前类型的名字
	var $field = $ref.attr('name');
	// 当前SUB模型
	var $submodel = $ref.attr('submodel');
	// 当前对象ID
	var $masid = $ref.attr('masid');
	var mapkey = $ref.attr('mapkey');
	if(mapkey=="undefined" || mapkey==""){
		mapkey="masid";
	}
	// 是否判断明细
	var $judgeSub = true;
	if($ref.attr('judgeSub')){
		$judgeSub = $ref.attr('judgeSub');
	}
	$.ajax({
		type:'POST',
		url:TP_APP+"/Common/onchangeType",
		cache:false,
		data:{model:$model,val:$val,field:$field,submodel:$submodel,masid:$masid,mapkey:mapkey,judgeSub:$judgeSub},
		success:function(results){
			if(!results) return;
			if(results > 0) {
				alertMsg.error('当前订单已经存在明细，不允许修改订单类型！');
				var html = $ref.parents("div.combox:first").next().html();
				var $refCombox = $ref.parent().parent();
				$ref.html(html).insertAfter($refCombox);
				$refCombox.remove();
				$ref.combox();
			} else {
				return false;
			}
		},
		error:DWZ.ajaxError
	});
};
// add by wangcheng
function ondblclick_navTab(obj, tabid, url,title) {
	var $this = $(obj,tabid);
	//url = url.replace("/edit", "/view");
	// 选中行 by杨东 修改为双击针对有checkbox的TR
	if($this.find("input[type='checkbox']:first").length > 0){
		if(!$this.find("input[type='checkbox']:first").is(':disabled')){
			$this.find("input[type='checkbox']:first").attr("checked","checked");
		};
		$this.addClass("selected");
		$this.addClass("checkedbox");
		$this.siblings().removeClass("selected");
		$this.siblings().removeClass("checkedbox");
		$this.siblings().find("input[type='checkbox']:first").removeAttr("checked");
	}
	
	//var t = obj.attr("title");
	var t = $this.attr("title") || $this.text();
	title=t|| title;
	navTab.openTab(tabid, url, {
				title : title,
				fresh : true,
				data : {}
			});
}

function ondblclick_dialog(obj, op, url,t) {
	var $this = $(obj, op);
	// 选中行 by杨东 修改为双击针对有checkbox的TR
	if($this.find("input[type='checkbox']:first").length > 0){
		if(!$this.find("input[type='checkbox']:first").is(':disabled')){
			$this.find("input[type='checkbox']:first").attr("checked","checked");
		};
		$this.addClass("selected");
		$this.addClass("checkedbox");
		$this.siblings().removeClass("selected");
		$this.siblings().removeClass("checkedbox");
		$this.siblings().find("input[type='checkbox']:first").removeAttr("checked");
	}
	
	var rel = $this.attr("drel") || "_blank";
	var title = $this.attr("title") || $this.text();
	title = t || title;
	//url = url.replace("/edit", "/view");
	var url = unescape(url);
	var options = {};
	var w = $this.attr("dwidth");
	var h = $this.attr("dheight");
	if (w)
		options.width = w;
	if (h)
		options.height = h;
	options.max = eval($this.attr("max") || "false");
	options.mask = eval($this.attr("mask") || "true");
	options.maxable = eval($this.attr("maxable") || "true");
	options.minable = eval($this.attr("minable") || "true");
	options.fresh = eval($this.attr("fresh") || "true");
	options.resizable = eval($this.attr("resizable") || "true");
	options.drawable = eval($this.attr("drawable") || "true");
	options.close = eval($this.attr("close") || "");
	$.pdialog.open(url, rel, title, options);
}
function FormatAmount(amount){
	str.replace(amount,",")
}

/*
 * 局部刷新， 指哪刷哪
 * rel: 刷新部位的DIV的ID
 * url： 载入的URL地址
 * data：传入数据
 * callback： 返回数据方法
 * author: eagle
 */
function loadUrlTo_rel(rel,url,data,callback){
	var url = unescape(url);
	var options = {};
	var $rel = $("#"+rel);
	$rel.loadUrl(url,options,function(){
		$rel.find("[layoutH]").layoutH();
	});
}
/*
 * 局部刷新， 指哪刷哪
 * rel: 刷新部位的DIV的ID
 * url： 载入的URL地址
 * data：传入数据
 * callback： 返回数据方法
 * author: eagle
 */
function divAjaxDone(json){
	DWZ.ajaxDone(json);	
	if (json.statusCode == DWZ.statusCode.ok){
		var url=json.forwardUrl;
		$("#"+json.rel).loadUrl(url,'','');
		$("#"+json.rel).find("[layoutH]").layoutH();
		if ("closeCurrent" == json.callbackType) {
			$.pdialog.closeCurrent();
		}
	}
}

function reloadDialogWin(json){
	DWZ.ajaxDone(json);
	if (json.statusCode == DWZ.statusCode.ok){
	
		var whereTableName=json.navTabId;
		
		var id=json.data; //ID的值 

		var url="__APP__/PersonnelPersonInfo/"+whereTableName+"/id/"+id;

		$("#"+whereTableName).loadUrl(url,'','');
				
		$("#"+whereTableName).find("[layoutH]").layoutH();
		
		if ("closeCurrent" == json.callbackType) {
			$.pdialog.closeCurrent();
		}
	
	}
}

// 清理浏览器内存,只对IE起效，FF不需要
if ($.browser.msie) {
	window.setInterval("CollectGarbage();", 10000);
}
// setTimeout("getuserindx()",1000);
function getuserindx() {
	// 加载个性化定制页面

	var ajaxbg = $("#background,#progressBar");
	$(document).ajaxStart(function() {
				ajaxbg.hide();
			});

	$("#s_modules").loadUrl(TP_APP + "/Index/lookupuserindex");
	$(document).ajaxStart(function() {
				ajaxbg.show();
			}).ajaxStop(function() {
				ajaxbg.hide();
			});
}
// 格式化数字
function FormatNumber(element, sourceVal, maxVal, decimal) {
	maxVal = (undefined === maxVal) ? '' : maxVal;
	sourceVal = (undefined === sourceVal) ? '' : sourceVal;
	var obj = $(element);
	var re = /^[0-9]+.?[0-9]*$/;
	var F_ThisValue = obj.val();
	F_ThisValue = Number(F_ThisValue.replace(/,/g, ""));
	var F_NowValue = FormatN(F_ThisValue, decimal);
	// 判断最大值是否存在
	var F_maxVal = Number(maxVal.replace(/,/g, ""));
	sourceVal = Number(sourceVal.replace(/,/g, ""));
	if (maxVal.length != 0 && re.test(maxVal)) {
		if (F_ThisValue < 0 || !re.test(F_ThisValue) || F_ThisValue > F_maxVal) {
			if(F_ThisValue > F_maxVal){
				obj.val(FormatN(F_maxVal, decimal));
			}else{
				obj.val(FormatN(sourceVal, decimal));
			}
		} else {
			obj.val(F_NowValue);
		}
	} else {
		if (F_ThisValue < 0 || !re.test(F_ThisValue)) {
			obj.val(FormatN(sourceVal, decimal));
		} else {
			obj.val(F_NowValue);
		}
	}
}
// 格式化数字  不能小于minVal验证
function FormatMinVerifyNumber(element, sourceVal, minVal, decimal) {
	minVal = (undefined === minVal) ? '' : minVal;
	sourceVal = (undefined === sourceVal) ? '' : sourceVal;
	var obj = $(element);
	var re = /^[0-9]+.?[0-9]*$/;
	var F_ThisValue = obj.val();
	F_ThisValue = Number(F_ThisValue.replace(/,/g, ""));
	var F_NowValue = FormatN(F_ThisValue, decimal);
	// 判断最大值是否存在
	var F_minVal = Number(minVal.replace(/,/g, ""));
	sourceVal = Number(sourceVal.replace(/,/g, ""));
	if (minVal.length != 0 && re.test(minVal)) {
		if (F_ThisValue < 0 || !re.test(F_ThisValue) || F_ThisValue < F_minVal) {
			if(F_ThisValue < F_minVal){
				obj.val(FormatN(F_minVal, decimal));
			}else{
				obj.val(FormatN(sourceVal, decimal));
			}
		} else {
			obj.val(F_NowValue);
		}
	} else {
		if (F_ThisValue < 0 || !re.test(F_ThisValue)) {
			obj.val(FormatN(sourceVal, decimal));
		} else {
			obj.val(F_NowValue);
		}
	}
}

function FormatN(num, decimal, dec, spe) {
	decimal = (undefined === decimal) ? TP_DECIMAL : decimal;
	decimal = parseInt(decimal);
	dec = (undefined === dec) ? '.' : dec;
	spe = (undefined === spe) ? ',' : spe;
	if (isNaN(num) && num != "") {
		var r = _pad('', decimal);
		return "0" + dec + r;
	}
	num = parseFloat(num) + '';
	var length, tmp, left, right;
	length = num.length;
	tmp = num.split('.', 2);
	left = tmp[0];
	left = _split(left, 3, spe);
	right = (undefined === tmp[1]) ? '' : tmp[1];
	right = _pad(right, decimal);
	if (0 == right.length) {
		num = left;
	} else {
		num = left + dec + right;
	}
	return num;
	function _split(str, len, spe) {
		var l = str.length;
		var tmp = new Array();
		if (l > len) {
			var b = l % len;
			var ts = str.substr(0, b);
			tmp.push(ts);
			while (b < l) {
				var ts = str.substr(b, len);
				tmp.push(ts);
				b += len;
			}
			str = tmp.join(spe);
		}
		var a = str.substr(0, spe.length);
		if (a == spe) {
			str = str.substr(spe.length);
		}
		return str;
	}
	function _pad(str, len) {
		var l = str.length;
		if (l < len) {
			for (var i = 0; i < (len - l); i++) {
				str += '0';
			}
		} else {
			str = str.substr(0, len);
		}
		return str;
	}
}
var Wi = [ 7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2, 1 ];// 加权因子  
var ValideCode = [ 1, 0, 10, 9, 8, 7, 6, 5, 4, 3, 2 ];// 身份证验证位值.10代表X   

$(function() {
	$(".TablePress .keyBoard").live("keyup", function(evt) {
		var num = $(this).attr("rel");
		var tabIndex = parseInt($(this).attr("tabindex"));
		var d = tabIndex % 100;
		switch (evt.which) {
			case 38 : // 上
				tabIndex -= 100;
				break;
			case 40 : // 下
				tabIndex += 100;
				break;
			case 37 : // 左
				d--;
				if (d > 0) {
					tabIndex--;
				} else {
					tabIndex -= (100 - num + 1);
				}
				break;
			case 39 : // 右
				d++;
				if (d <= num) {
					tabIndex++;
				} else {
					tabIndex += (100 - num + 1);
				}
				break;
			default :
				return;
		}
		if (tabIndex > 0) {
			$(".keyBoard[tabindex=" + tabIndex + "]").trigger("focus")
					.trigger("select");
		}
	});
});
function getfields() {
	var table = $("#maptable").val();
	$.ajax({
				url : TP_APP + "/BiDataMap/getfields/table/" + table,// 通过Ajax取数据的目标页面
				type : 'post',// 方法，还可以是"post"
				success : function(locals)// 成功后执行的语句，这里是一个函数，“locals”是返回的数据
				{
					$("#mapfield").empty();
					$("#mapfield").append(locals);
				}
			});
}
// 列表中图片显示
function proimageshow(obj) {
	var aobj = $(obj).next("span").children("a:first");
	if ($(aobj).hasClass("cboxElement")) {
		$(aobj).click();
		return false;
	}
	var aclass = $(aobj).attr("class");
	$("." + aclass).colorbox({
				rel : aclass,
				maxWidth:700,
				maxHeight:500,
				slideshow : true
			});
	$("." + aclass).click();
}
function dateToStr(datetime) {
	var year = datetime.getFullYear();
	var month = datetime.getMonth() + 1;// js从0开始取
	var date = datetime.getDate();
	var hour = datetime.getHours();
	var minutes = datetime.getMinutes();
	var second = datetime.getSeconds();

	if (month < 10) {
		month = "0" + month;
	}
	if (date < 10) {
		date = "0" + date;
	}
	if (hour < 10) {
		hour = "0" + hour;
	}
	if (minutes < 10) {
		minutes = "0" + minutes;
	}
	if (second < 10) {
		second = "0" + second;
	}
	var time = year + "-" + month + "-" + date;
	return time;
}
function table_tdedit(obj, url) {
	var tdIns = $(obj);
	var fields = tdIns.attr('name');
	var id = tdIns.parents("tr").attr("rel");
	if (tdIns.parents("td").find("input").length > 0) {
		return false;
	}
	var inputIns = $("<input type='text' name='field'/>"); // 需要插入的输入框代码
	var text = $.trim($(obj).html());
	inputIns.width(tdIns.parent("div").width());// 设置input与td宽度一致
	inputIns.val($.trim(tdIns.html()));// 将本来单元格td内容copy到插入的文本框input中
	tdIns.html("");// 删除原来单元格td内容

	inputIns.appendTo(tdIns).focus().select();// 将需要插入的输入框代码插入dom节点中
	// 特殊处理金额部分
	inputIns.click(function() {
				return false;
			});
	inputIns.blur(function() {
				var inputText = $(this).val();
				$.post(url, {
							"id" : id,
							"fields" : fields,
							"value" : inputText
						}, function(response) {
							var json = DWZ.jsonEval(response);
							if (json.statusCode == DWZ.statusCode.ok) {
								tdIns.html(inputText);
							} else {
								alertMsg.error(json.message);
								tdIns.html(text);
							}
						});
			});
	// 处理Enter和Esc事件
	inputIns.keyup(function(event) {
				var keycode = event.which;
				var inputText = $(this).val();
				if (keycode == 13) {
					// enter事件
					$.post(url, {
								"id" : id,
								"fields" : fields,
								"value" : inputText
							}, function(response) {
								var json = DWZ.jsonEval(response);
								if (json.statusCode == DWZ.statusCode.ok) {
									tdIns.html(inputText);
								} else {
									alertMsg.error(json.message);
									tdIns.html(text);
								}
							});
				}
				if (keycode == 27) {
					tdIns.html(text);
				}// esc事件
			});
}

function updateinvoicetype(obj,url,ids,params){
	var ajaxbg=$("#background,#progressBar");
	$(document).ajaxStart(function(){ajaxbg.hide();});
	var val=$(obj).val();
  	$(obj).parents("form").find("#"+ids).val("");
	var arr=[{name:params, value:val}];
  	$.ajax({type:'POST',dataType:"json",url:url,cache:false,data:arr,
	success:function(json){if (json.statusCode != DWZ.statusCode.ok){error:DWZ.ajaxError;}},
	error:DWZ.ajaxError});
	$(document).ajaxStart(function(){ajaxbg.show();}).ajaxStop(function(){ajaxbg.hide();});
}

/**
 * @param str   运算动作，比如：2*5/4-3
 * @param decimal   保留小数点位数,这里默认保留3位小数，进行四舍五入。
 * @returns  返回运算的结果，这里未进行逗号分隔处理，逗号分隔处理在页面上进行。
 */
function comboxMathematicalOperation(str,decimal){
	var text= '0';
	var re=0;
	decimal = (undefined === decimal) ? TP_DECIMAL : decimal;//保留小数位数
	s = (undefined === str) ? 0 : str;//保留小数位数
	if(s==0) return FormatN(0);
	text = eval(s);
	rel = text.toFixed(decimal);
	return rel;
}
/**
 * 绑定选择人员控制
 */
var checkUser = null;
/**
 * ajax请求user数据
 */
function ajaxForUserData(){
	//e.stopPropagation();//阻止冒泡
	checkUser = $(this);
	// 设置加载中显示层位置
	var m = checkUser.attr("m");// 当前模型
	
	
	// 没有传入 当前模型 则默认为commonAction
	if(!m){
		m = "Common";
	}
	var ulid = checkUser.attr("ulid");//填入的UL的id
	var data = checkUser.attr("data");//参数
	var sysUser=checkUser.attr("sysUser");//系统用户组
	var groupData = checkUser.data('group');	// 组选择
	var isuser = checkUser.attr("checktype");
	var url = "lookupSelectUser";// 默认为选择用户
	var appendurl = checkUser.attr("appendurl");// 附加URL
	// 判断选择是人还是用户
	if(isuser && isuser == "person"){
		url = "lookupSelectPerson";
	}else if(isuser && isuser == "deptuser"){
		url = "lookupSelectDpwtuser";
	}else if(isuser && isuser == 'xyzperson'){
		url = "lookupSelectXyzPerson";
	}else if(isuser && isuser == 'expertPerson'){
		url = "lookupSelectExpertPerson";
	}else if(isuser && isuser == 'notifyPerson'){
		url = "lookupSelectNotifyPerson";
	}else if(isuser && isuser == 'rolegroupperson'){
		url = "lookupRoleGroupPerson";
	}
	// 如果存在附件URL 这加入url中
	if(appendurl){
		url = url+"/"+appendurl;
	}
	var options = {};
	options.param = {ulid:ulid,data:data,sysUser:sysUser,groupData:groupData};
	options.width = 600;
	options.height = 570;
	options.mask = true;
	options.resizable = false;
	options.maxable = false;
	options.minable = false;
	$.pdialog.open(TP_APP + '/'+m+'/'+url, "MisCheckUserObj", "人员选择器", options);
}
$(function() {
	DWZ.init(TP_PUBLIC + "/dwz2/dwz.frag.xml", {
				loginUrl : TP_APP + "/Public/login_dialog",
				loginTitle : "登录",
				debug : false,
				statusCode : {
					ok : 1,
					error : 0,
					timeout : 301
				},
				callback : function() {
					initEnv();
					$("#themeList").theme({
								themeBase : TP_PUBLIC + "/dwz2/themes"
							});
				}
			});
	$.fn.calculator.hide = function(calc) {
		calc.fadeOut(500);
	};

	$('#calc').calculator({
				movable : true,
				resizable : true,
				width : 160,
				defaultOpen : false
			});
	$('#showCalc').click(function() {
				$('#calc').show();
			});

	//browseNavigate();

	window.onresize = function() {
		$("#spanFirst").unbind("click");
		$("#spanLast").unbind("click");
		//browseNavigate();
	}
    // 导航菜单控制显示数量及其滚动效果
	function browseNavigate() {
        /*
         * 计算各种css效果的属性值
         */
		var showCount = 10;
		var $outDiv = $("#www");
		var $muenDiv = $("#page-wrap");
		var logoWidth = parseInt($("#header a").css("width"));
		var windowWidth = document.documentElement.offsetWidth;
		var muenWidth = windowWidth - 2 * logoWidth - 50;
		var liWidth = parseInt($(".menu_now").css("width"));
		showCount = parseInt(muenWidth / liWidth);
		$muenDiv.css({"width" : liWidth * showCount + "px"});
		$outDiv.css({"width":(liWidth * showCount + 50) + "px"});
		var pwidth = $("#www").parent().css("width");
		var outLeft = (pwidth - $outDiv.css("width")) / 2;
		$outDiv.css("left",outLeft);


		var $first = $("#spanFirst").removeClass("first_img1")
				.removeClass("first_img2").addClass("first_img1");
		var $last = $("#spanLast").removeClass("first_img3")
				.removeClass("first_img4").addClass("first_img3");
		var i = 0;
		var $list = $("#page-wrap > ul > li");
		var curli = 0;
		var count = $list.size();
		$list.each(function() {
					curli = i++;
					$(this).show();
					if (i > showCount) {
						$(this).hide();
					}
				});
		if (showCount < count) {
			curli = showCount - 1;
			$last.removeClass("first_img3").addClass("first_img4");
		}
        //前移动
		$first.bind("click", function() {
					if (curli - (showCount - 1) > 0) {
                        $outDiv.addClass("mystyle");
                        $("#page-wrap > ul > li:eq(" + curli + ")")
                                .hide("slow");
                        $("#page-wrap > ul > li:eq(" + (curli - showCount)
                                + ")").show("slow",function(){
                                    $outDiv.removeClass("mystyle");
                                });
						$last.removeClass("first_img3").addClass("first_img4");
						curli--;
					}
					if (curli - (showCount - 1) == 0) {
						$first.removeClass("first_img2").addClass("first_img1");
					}
				});
        //后移动
		$last.bind("click", function() {
					if (curli < count - 1) {
                        $outDiv.addClass("mystyle");
                        $("#page-wrap > ul > li:eq("
                                + (curli - (showCount - 1)) + ")").hide("slow");
                        $("#page-wrap > ul > li:eq(" + (curli + 1) + ")")
                                .show("slow",function(){
                                    $outDiv.removeClass("mystyle");
                                });
						$first.removeClass("first_img1").addClass("first_img2");
						curli++;
					}
					if (curli == count - 1) {
						$last.removeClass("first_img4").addClass("first_img3");
					}
				});
	}
});

function choose_userbydeptid(url,target){
	if(target=="dialog"){
		var $box= $.pdialog.getCurrent()
	}else{
		var $box=navTab.getCurrentPanel();
	}
	$("select.dep_obj",$box).change(function(){
		var t=$(this).val();
		var $ref =$("select.user_obj",$box);
		$.ajax({
			type:'POST',dataType:"json",url:url,cache:false,
			data:{dep_id:t},
			success:function(json){
			if(!json)return;
			var html='';
			$.each(json,function(i){
			if(json[i]&&json[i].length>1){
			html+='<option value="'+json[i][0]+'">'+json[i][1]+'</option>';}});
			var $refCombox=$ref.parents("div.combox:first");
			$ref.html(html).insertAfter($refCombox);
			$refCombox.remove();
			$ref.trigger("change").combox();},
			error:DWZ.ajaxError
		});
	});
}
function mis_swf_upload_del(json,d){
	var ids=json.data;
	$("#"+d).find("div.uploadify-queue-item").each(function(){
		var r=$(this).find("a.dellink").attr("rel");
		if(r==ids){
			$(this).fadeOut(250,function(){ $(this).remove(); });
		}
	});
}

//add by arrowing 2012-12-22
var checkforDivIsOut = true;
var checkforInput = null;
$(function(){



	/*
	clearCheckFor();
	var checkTimeout = false;
	var isIE8 = $.browser.msie && $.browser.version <= '9.0';

	if($.browser.msie){
		if(isIE8){//for IE8
			$('.checkByInput').live('keyup',ajaxForData);
		}else{
			$('.checkByInput').live('propertychange',ajaxForData);
		}
	}else{
		$('.checkByInput').live('input',ajaxForData);
	}

	$('#checkfor').live('mouseleave', function(){checkforDivIsOut = true;});
	$('#checkfor').live('mouseenter', function(){checkforDivIsOut = false;});
	$('#checkfor').live('blur', function(){checkforDivIsOut = false;});

	//$('.checkByInput').live('dblclick', ajaxForData);
	$('.checkByInput').live('focus', function(){
		checkforInput = $(this);
	});
	function clearCheckFor(){
		var checkfor = $('#checkfor');
		if(checkfor.length > 0){
			checkfor.remove();
		}
	}
*/


	function ajaxForData(e){
		var _this = $(this);
		_this.attr('autocomplete', 'off');

		if(e.type != 'dblclick' && _this.val() == ''){//清空文本行为不查询  不是双击的时候不查询
			$('#checkfor').remove();
			_this.next().val('');

			clearTimeout(checkTimeout);
			checkTimeout = null;
			return false;
		}

		if(isIE8){//for IE8
			var curKey = e.keyCode;
			if(curKey != 13 && curKey != 38 && curKey != 40){
				var prev = _this.prev();
				if(prev.attr('auto') == 1){//是否存在比较文本值的对象
					if(e.type != 'dblclick' && prev.val() == _this.val()){//值相同时不查询,除非双击
						return false;
					}else{
						prev.val(_this.val());//确保比较值的跟进，此次已确定查询
					}
				}else{//建立比较值的对象，放置于文本框之前
					$('<input type="hidden" auto="1" type="text" />').insertBefore(_this);
				}
			}else{//选择list数据时不查询
				return false;
			}
		}

		if(checkTimeout){
			clearTimeout(checkTimeout);
			checkTimeout = null;
		}

		checkTimeout = setTimeout(function(){

			var checkfor = _this.attr('checkfor');
			var limit = _this.attr('litmit');
			var order = _this.attr('order');
			var map = _this.attr('map');
			var newconditions=_this.attr('newconditions');
			var fields = _this.attr('fields');
			var other = _this.attr('other');
			var m = _this.attr("m");	// 当前模型
			var appendurl = _this.attr('appendurl');
			var url = TP_APP + '/CheckFor/check';
			// 如果存在附件URL 这加入url中
			if(appendurl){
				url = url+"/"+appendurl;
			}
			//var ajaxbg=$("#background,#progressBar");$(document).ajaxStart(function(){ajaxbg.hide();});
			$.ajax({
				type: "POST",
				url: url,
				data: {m:m,other:other,fields: fields,map: map,newconditions:newconditions,order: order, limit: limit, checkfor: checkfor, con: _this.val()},
				async:true,
				success: function(data){
					clearCheckFor();
					var con = $(data);
					$('body').append(con);

					var pos = _this.offset();
					var w = $(window);
					var c = $('#checkfor');

					var cheight = c.outerHeight(true);
					var cwidth = c.outerWidth(true);

					var overflow_width = pos.left + cwidth - w.width();   // > 0 溢出宽度
					var overflow_height = pos.top + cheight - w.height(); // > 0 溢出高度

					var left = overflow_width > 0 ? pos.left - cwidth + _this.outerWidth() : pos.left;
					var top = overflow_height > 0 ? pos.top - cheight : pos.top + _this.outerHeight();

					con.css({left: left, top: top});
				},
				global: false
			});
			/*$.post(TP_APP + '/CheckFor/check', {other:other,fields: fields,map: map, order: order, limit: limit, checkfor: checkfor, con: _this.val()}, function(data){
				clearCheckFor();
				var con = $(data);
				$('body').append(con);

				var pos = _this.offset();
				var w = $(window);
				var c = $('#checkfor');

				var cheight = c.outerHeight(true);
				var cwidth = c.outerWidth(true);

				var overflow_width = pos.left + cwidth - w.width();   // > 0 溢出宽度
				var overflow_height = pos.top + cheight - w.height(); // > 0 溢出高度

				var left = overflow_width > 0 ? pos.left - cwidth + _this.outerWidth() : pos.left;
				var top = overflow_height > 0 ? pos.top - cheight : pos.top + _this.outerHeight();

				con.css({left: left, top: top});
			});//$(document).ajaxStart(function(){ajaxbg.show();}).ajaxStop(function(){ajaxbg.hide();});*/		
		}, 1000);

	}

});

/**
 *	查找带回，可编辑处理方法，保持前后台两个框的id,name同步对应
 *	$("[name='org3.id']").val(inputHiddenText);  这里的org3.id对应的是表单中的隐含的文本框
 * 	eagle
 **/
//保持两个文本框中对应的值，同步，在找查代回中的应用。
jQuery.eagleKeepInputSameCopyValue = function() {

	var b_name=$("[name='org3.orgName']").val();

	$("[name='tempValue']").val(b_name); //中转文本框


};

jQuery.eagleKeepInputSameValue = function() {

	//获取文本框值
	var a_id=$("[name='org3.id']").val();

	var b_name=$("[name='org3.orgName']").val();

	var c_name=$("[name='tempValue']").val();

	//变成数组
	var a_id_array=a_id.split(",");

	var b_name_array= b_name.split(",");

	var c_name_array= c_name.split(",");

	/*
	var a=new Array('1','2','3','4','5');
	var b=new Array('a','b','c','d','e');
	var c=new Array('a','b','c','e');*/

	var e_id_array=new Array(); //存新的值

	for(i=0; i< b_name_array.length;i++)
	{
		for(j=0;j<c_name_array.length;j++)
		{
			if(c_name_array[j]==b_name_array[i])
			{
				e_id_array[i]=a_id_array[j];
			}

		}

	}

	//所新ID值付给 隐藏的文本框
	 var inputHiddenText=e_id_array.join(",");
	 $("[name='org3.id']").val(inputHiddenText);

};

/**
 *查询条件，要求只选中一个，或在没有传参数时，把所有的class='onlyOneSelected'的值设定位空
 *调用：  onchange, onclick  = javascript:$.onlyOneSelected('可以为空/你要清空对象的ＩＤ')
 * eagle
 **/
jQuery.onlyOneSelected = function(args) {

	if(args=="")
	{
		$("[class='onlyOneSelected']").val(' ');
	}
	else
	{
		$("[name='"+args+"']").val(' ');
	}

};

/**
 *修改页面双层嵌套的。如：物料入库eidt中。详情保存成功后，只刷新选中的navTab-tab。
 * author: liminggang
 * submit_refreshsub   保存和保存并关闭调用的同一个JS   
 * @param	form	当前表单
 * @param	modelname 需要操作的模型
 * @param	name	需要判断是否为0的字段
 * @param	stp 	stp==1:表示保存并关闭。step为其他时，则是不关闭
 * @param	key		这个条件是单选保存的时候起作用。
 * refreshsub_edit_sub_part_close  保存并关闭方法
 * refreshsub_edit_sub_part_unclose  保存不关闭方法
 * date: 2013-2-28  
 */ 
function submit_refreshsub(form,modelname,name,stp,key){
    var $form = $(form);
    var n=0;
    $("input[name='"+name+"[]']").each(function(){
        var s=$(this).val();
        s = Number(s.replace(/,/g,""));
        if(s > 0){
            n++;
        }
    });
    if(n == 0){
        alertMsg.warn('数量全部为0！')
        return false;
    }
    var rel = $(".navTab-tab").find("li.selected").attr("tabid");
    
    if(key == undefined){
    	if(stp==1){
    		$form.attr("action",TP_APP+"/"+modelname+"/insert/navTabId/"+rel+"/unclose/0");
            return validateCallback($form, dialogAjaxDone);
	        //$form.attr("action",TP_APP+"/"+modelname+"/insert/unclose/0");
	        //return validateCallback($form, refreshsub_edit_sub_part_close);
	    }else{
	    	 $form.attr("action",TP_APP+"/"+modelname+"/insert/navTabId/"+rel+"/unclose/1");
	         return validateCallback($form, dialogunclose);
	        //$form.attr("action",TP_APP+"/"+modelname+"/insert/unclose/1");
	        //return validateCallback($form, refreshsub_edit_sub_part_unclose);
	    }
    }else{
    	if(stp==1){
    		$form.attr("action",TP_APP+"/"+modelname+"/insert/navTabId/"+rel+"/unclose/0");
            return validateCallback($form, dialogAjaxDone);
	        //$form.attr("action",TP_APP+"/"+modelname+"/insert/unclose/0");
	        //return validateCallback($form, refreshsub_edit_sub_part_close);
	    }else{
	    	$form.attr("action",TP_APP+"/"+modelname+"/insert/navTabId/"+rel+"/unclose/1/key/"+key);
	        return validateCallback($form, dialogunclose);
	        //$form.attr("action",TP_APP+"/"+modelname+"/insert/unclose/1/key/"+key);
	        //return validateCallback($form, refreshsub_edit_sub_part_unclose);
	    }
    }
};
function refreshsub_edit_sub_part_close(json){
    dialogAjaxDone(json);
    var refreshtab=json.refreshtabs;
    var model=refreshtab.tab;
    var masid=refreshtab.masid;
    var h=$("#"+model+"edit_sub_part_info").height();
    $("#"+model+"edit_sub_part").loadUrl(TP_APP+"/"+model+"/edit/id/"+masid,{"refreshsub":1,"height":h},function(){
    	$("#"+model+"edit_sub_part").find("[layoutH]").layoutH();});
    
}
function refreshsub_edit_sub_part_unclose(json){
    dialogunclose(json);
    var refreshtab=json.refreshtabs;
    var model=refreshtab.tab;
    var masid=refreshtab.masid;
    var h=$("#"+model+"edit_sub_part_info").height();
    $("#"+model+"edit_sub_part").loadUrl(TP_APP+"/"+model+"/edit/id/"+masid,{"refreshsub":1,"height":h},function(){
    	$("#"+model+"edit_sub_part").find("[layoutH]").layoutH();});
    if( refreshtab.reshsub_rel && refreshtab.reshsub_title ){
	var rel=refreshtab.reshsub_rel;
	var url=refreshtab.reshsub_url;
	var title=refreshtab.reshsub_title;
	$.pdialog.open(url, rel, title, {});
    }
}
//end

function apptrhtmlmas(obj){
	//获取选中元素值
	var $box=navTab.getCurrentPanel();
	var html='<tr>\
		<td><input name="arr_nd[]" class="gangwei_d xytdinput" type="text" readonly="readonly"/></td>\
		<td><input name="arr_content[]" class="gangwei_d xytdinput" type="text" /></td>\
		<td><input type="text" class="gangwei_d checkByInput xytdinput" checkfor="MisProductUnit" insert="id" show="name"/><input type="hidden" name="arr_unitid[]"/></td>\
		<td><input name="arr_qty[]" class="gangwei_d xytdinput arr_qty" type="text" value="0.00" onblur="onblurplush(this);"/></td>\
		<td><input name="arr_price[]" class="gangwei_d xytdinput arr_price"  type="text" value="0.00" onblur="onblurplush(this);"/></td>\
		<td><input name="arr_amount[]" class="gangwei_d xytdinput arr_amount" readonly="readonly" type="text" value="0.00" onblur="onblurplush({$key+1});"/></td>\
		<td><input type="hidden" class="gangwei_d xytdinput arr_audamount" name="arr_audamount[]" /><input type="hidden" class="gangwei_d xytdinput arr_audprice" name="arr_audprice[]" />\
		<input name="arr_remark[]" class="gangwei_d xytdinput" type="text"/></td>\
        <td><a href="javascript:;" onclick="deletetrhtml(this);"><strong>移除</strong></a></td>\
      </tr>';
	$("#"+obj).append(html).initUI();
	//重新计算ND
	$box.find("input[name='arr_nd[]']").each(function(i){
	    	 $(this).val(i+1);
	 });
}
function deletetrhtml(obj){
	//获取选中元素值
	var $box=navTab.getCurrentPanel();
    $(obj).parent().parent().remove();
     //重新计算金额
	var amount=FormatN(0);
	var amountstr="0";
	 $box.find("input[name='arr_amount[]']").each(function(i){
		 var val=$(this).val();
		 val = Number(val.replace(/,/g,""));
		 var arr_amount=parseFloat(val);
		 if(arr_amount>0){
			 //amount=comboxMathematicalOperation(amount,arr_amount,0);
			amountstr+="+"+arr_amount;
		}
	 });
	amount=comboxMathematicalOperation(amountstr);
	//本次需支付金额
	 $("input[name='apamount']").val(FormatN(amount)); 
	//获取阶段支付比例  
	 var phasepayratio=$("input[name='phasepayratio']").val();
	var ratioamount=comboxMathematicalOperation(amount+"*"+phasepayratio+"/100");
	 $("input[name='ratioapamount']").val(FormatN(ratioamount));
}
function onblurplush(obj){
	//获取选中元素值
	var $box=navTab.getCurrentPanel();
	var arr_qty=$(obj).parents("tr").find("input.arr_qty").val();
	var qty = arr_qty.replace(/,/g, "");
	var arr_price=$(obj).parents("tr").find("input.arr_price").val();
	var price=arr_price.replace(/,/g, "");
	qty=parseFloat(qty);
	price=parseFloat(price);
	$(obj).parents("tr").find("input.arr_qty").val(FormatN(qty));
	$(obj).parents("tr").find("input.arr_price").val(FormatN(price));
	$(obj).parents("tr").find("input.arr_audprice").val(FormatN(price));
	//var amount=comboxMathematicalOperation(qty,price,2);
	var amount=comboxMathematicalOperation(qty+"*"+price);
	$(obj).parents("tr").find("input.arr_amount").val(FormatN(amount));
	$(obj).parents("tr").find("input.arr_audamount").val(FormatN(amount));
	//重新计算金额
	var amount=FormatN(0);
	var amountstr="0";
	 $box.find("input[name='arr_amount[]']").each(function(i){
		 var val=$(this).val();
		 val = Number(val.replace(/,/g,""));
		 var arr_amount=parseFloat(val);
		 if(arr_amount){
			//amount=comboxMathematicalOperation(amount,arr_amount,0);
			amountstr+="+"+arr_amount;
		 }
	 });
	 amount=comboxMathematicalOperation(amountstr);
	 //本次需支付金额
	 $("input[name='apamount']").val(FormatN(amount)); 
	//获取阶段支付比例  
	 var phasepayratio=$("input[name='phasepayratio']").val();
	 //var ratioamount=comboxMathematicalOperation(amount,phasepayratio/100,2);
	  var ratioamount=comboxMathematicalOperation(amount+"*"+phasepayratio+"/100");
	 $("input[name='ratioapamount']").val(FormatN(ratioamount));
}
//end

/**
 * 明细新增时对当前行不进行新增，选择是删除和取消函数
 * author: 杨东
 * $this：当前对象
 * $v：删除和取消标记（1：表示取消，其他：表示删除）
 * 注意：1、加入当前函数时需对作用input添加class:del_name,如：class="number keyBoard del_name"
 * 	2、需添加没有name的隐藏input，如：<input class="number keyBoard del_name2" size="15" type="text" style="display: none;"/>
 *  3、新加入的input长度应该和作用input长度样式一致
 * date: 2013-3-6  
 */ 
function deleteOnc($this,$v){
	$obj = $($this).closest('tr');//当前对象的当前行
	var del_name = $obj.find('input').filter(".del_name");//作用input
	var del_name2 = $obj.find('input').filter(".del_name2");//隐藏input
	if($v == 1) {
		// 将作用input显示，隐藏input隐藏
		$obj.css("background",'');
		var val = del_name2.val();
		del_name.val(val);
		del_name[0].style.display = 'block';
		del_name2.hide();
		$($this).hide();
		$($this).prev()[0].style.display = 'block';
	} else {
		// 将作用input隐藏，隐藏input显示
		$obj.css("background",'#ccc');
		var val = del_name.val();
		del_name2.val(val);
		del_name2[0].style.display = 'block';
		del_name.val('0');
		del_name.hide();
		$($this).hide();
		$($this).next()[0].style.display = 'block';
	}
}
/**
 * 搜索关键字并着色
 * author: 杨东
 * $this：当前对象
 * $div：需要检索的div
 * date: 2013-4-10  
 */ 
function textSearchKeys($div,$this){
	var $obj = $($this).prev();
	$("#"+$div).textSearch($obj.val());
}
/**
 * 流程角色管理处点选部门特效
 * author: 杨东
 * $this：当前对象
 * $div：写入数据的div
 * date: 2013-4-10  
 */ 
function deptChecked($div,$this){
	var $obj = $($this).next();
	if($($this).attr("checked")){
		$($this).attr("checked","checked");
		if($("#"+$div).text()){
			$("#"+$div).append(",");
			$("#"+$div).append($obj.text());
		} else {
			$("#"+$div).append($obj.text());
		}
	} else {
		$($this).removeAttr("checked");
		var val = $("#"+$div).text().split(",");
		var arr = [];
		for(var i=0;i<val.length;i++){
			if(val[i] !== $obj.text()){
				arr.push(val[i]);
			}
		}
		$("#"+$div).text(arr.join(','));
	}
}
/**
 * 流程角色管理处点选用户特效
 * author: 杨东
 * $this：当前对象
 * $div：写入数据的div
 * $all:全选
 * $checkdiv:check所在的DIV
 * date: 2013-4-10  
 */ 
function userChecked($div,$this,$all,$checkdiv){
	if($all){
		var name = $($this).attr("group");
		var $checkboxLi = $("#"+$checkdiv).find(":checkbox[name='"+name+"']");
		$checkboxLi.each(function(){
			var $obj = $(this).parent().next().next().next();
			if(!$(this).attr("checked")){
				if($("#"+$div).text()){
					$("#"+$div).append(",");
					$("#"+$div).append($obj.text());
				} else {
					$("#"+$div).append($obj.text());
				}
			} else {
				$("#"+$div).text("");
			}
		});
	} else {
		var $obj = $($this).parent().next().next().next();
		if($($this).attr("checked")){
			$($this).attr("checked","checked");
			if($("#"+$div).text()){
				$("#"+$div).append(",");
				$("#"+$div).append($obj.text());
			} else {
				$("#"+$div).append($obj.text());
			}
		} else {
			$($this).removeAttr("checked");
			var val = $("#"+$div).text().split(",");
			var arr = [];
			for(var i=0;i<val.length;i++){
				if(val[i] !== $obj.text()){
					arr.push(val[i]);
				}
			}
			$("#"+$div).text(arr.join(','));
		}
	}
}
/*
 * 作用：底部工具栏
 * arrowing 2013-1-17
 */
$(function(){
	var mainpanel = $('#mainpanel');
	var chatpanel = mainpanel.children('#chatpanel');
	var alertpanel = mainpanel.children('#alertpanel');
	var chatAnode = chatpanel.children('.feed');
	var alertAnode = alertpanel.children('.alerts');
	var chatSubpanel = chatpanel.children('.subpanel');
	var alertSubpanel = alertpanel.children('.subpanel');

	$('body').bind('click', function(e){
		mainpanel.find('.subpanel').hide();
		chatAnode.removeClass('active');
		alertAnode.removeClass('active');
	});


	chatpanel.bind('click', function(e){
		e.stopPropagation();
		e.preventDefault();
		var show = chatSubpanel.css('display');
		chatSubpanel.css('display', (show == 'none' ? 'block' : 'none'));
		alertAnode.removeClass('active');
		alertSubpanel.hide();
		chatAnode.toggleClass('active');
	});

	alertpanel.bind('click', function(e){
		e.stopPropagation();
		e.preventDefault();
		var show = alertSubpanel.css('display');
		alertSubpanel.css('display', (show == 'none' ? 'block' : 'none'));
		chatAnode.removeClass('active');
		chatSubpanel.hide();
		alertAnode.toggleClass('active');

		if(show == 'none'){
			$.get(TP_APP + '/MisTodo/lookupshow', function(data){
				$('#MisTodoShow').html(data.html);
				alertpanel.find("em").html(data.daiban);
				$('#MisTodoShow a').bind('click', function(e){
					var __this = $(this);
					navTab.openTab(__this.attr('tabid'), __this.attr('taburl'), {title : __this.attr('tabname'), fresh : true, data: null});
				});
			},'json');
		}
	});

	chatSubpanel.bind('click', function(e){
		e.stopPropagation();
	});

	alertSubpanel.bind('click', function(e){
		e.stopPropagation();
	});
});
/**
 *  工作便签添加、修改
 * @param obj 当前节点(this)对象
 */
function saveNotes($this){
	var $val = $($this).val();
	var $input = $($this).next();
	if($val == $input.val()){
		return false;
	} else {
		$.ajax({
			url : TP_APP+"/MisSystemPanelMethod/misUserNote/type/note",// 通过Ajax取数据的目标页面
			type : 'post',// 方法，还可以是"post"
			data:{note:$val},
			async:true,
			global: false,
			success : function(res){
				$input.val($val);
			}// 成功后执行的语句，这里是一个函数，“locals”是返回的数据
		});
	}
}
/**
 * SUB界面视图切换函数
 * @param $divid 刷新DIV的ID
 * @param $model 存在$method的model
 * @param $masid 单据头ID
 * @param $method 方法
 * @param $auditstatus 审核状态
 * @author 杨东
 */
function subViewChange($model,$masid,$method,$auditstatus){
	var $divid = $model;
	if($auditstatus == '2'){
		$divid = $divid+'auditEdit';
	} else if($auditstatus == '3'){
		$divid = $divid+'auditView';
	} else {
		$divid = $divid+'edit';
	}
	$divid = $divid+'_sub_part';
	var h=$("#"+$divid).height();
	$("#"+$divid).loadUrl(TP_APP+"/"+$model+"/"+$method+"/id/"+$masid,{"refreshsub":1,"height":h,"auditstatus":$auditstatus,"divid":$divid},function(){
		$("#"+$divid).find("[layoutH]").layoutH();});
}
/**
 * 单击TR选中Name为$inputname的Checkbox
 * @param $this 当前TR对象
 * @param $inputname Checkbox的name
 * @author 杨东
 */
function onTrClickCheckbox($this,$inputname){
	$($this).find("input[type='checkbox']").click(function(e){
//	    阻止冒泡,避免行点击事件中,直接选择选框无效
		e.stopPropagation();
	});
	var trdate = $($this).data('tool');//列上面的操作按钮
	if(trdate){
		var toollength = $($this).closest('.pageContent').find('.toolBar').find('a').filter(':visible').addClass('disabled');//toolBar上面的操作按钮
		for(var b=toollength in trdate){
			toollength.filter('.' + b ).removeClass('disabled');
		}
	}
	var $thischeckbox = $($this).find("input[name='"+$inputname+"']");
	$thischeckbox.attr('checked',!$thischeckbox.is(":checked"));
}
/**
 * ztree专用着色
 * @param treeId 树ID
 * @param treeNode 树
 * @returns
 * @author 杨东
 */
function getFontCss(treeId, treeNode) {
    return (!!treeNode.highlight) ? {color:"#A60000", "font-weight":"bold"} : {color:"#333", "font-weight":"normal"};
}
var _searchZtreeKeyWord = null;// 检索ztree的关键字
var _searchZtreeSelectedkey = 0;// 检索ztree的第几个关键字，默认为0
var _searchZtreeTime = null; //检索ztree关键字的保存时间
/**
 * ztree专用检索(单棵树)
 * @param treeId 树DIV的ID
 * @param inputId 检索框的ID
 * @author 杨东
 */
function SearchZTreeNode(treeId,inputId) {
    var value = $.trim($("#"+inputId).val());// 取得关键字
    if (value === "") return; // 如果关键字为空则直接返回
    // 判断当前树和检索关键字是否相同
    if(_searchZtreeKeyWord === treeId+value) {
    	_searchZtreeSelectedkey = _searchZtreeSelectedkey+1;//设置下一个检索位置
    } else {
    	// 不相同的时候
    	_searchZtreeSelectedkey = 0;// 设置从第一个检索位置开始
    	_searchZtreeKeyWord = treeId+value;// 设置判断条件 检索ztree的关键字
    }
    clearSetZtreeSearch();// 延迟执行函数
    var zTree = $.fn.zTree.getZTreeObj(treeId);// 获取当前树对象
    var zTreeNodes = zTree.getNodes();// 获取所有树节点
    var nodeList = zTree.transformToArray(zTreeNodes);// 将树节点转换为数组
    // 将所有树节点高亮去掉
	for( var i=0, l=nodeList.length; i<l; i++) {
		nodeList[i].highlight = false;
		zTree.updateNode(nodeList[i]);
	}
	// 通过关键字查询树节点
	var nodeList = zTree.getNodesByParamFuzzy("title", value);
	var nodepinyinList = zTree.getNodesByParamFuzzy("pinyin", value);
	if(nodepinyinList.length>0) nodeList = $.extend({}, nodeList,nodepinyinList);
	var selectnode = null;// 初始化选中节点
	// 判断节点是否存在 不存在则 返回第一个节点 如果第一个节点不存在 则为空
	if(nodeList[_searchZtreeSelectedkey]){
		selectnode = nodeList[_searchZtreeSelectedkey];
	} else if(nodeList[0]){
		selectnode = nodeList[0];
	}
	if(selectnode!=null){
		zTree.expandNode(nodeList, true, false);
	}
	// 高亮节点 及展开父节点
	for( var i=0, l=nodeList.length; i<l; i++) {
		nodeList[i].highlight = true;
		zTree.updateNode(nodeList[i]);
	}
	// 选中节点
	zTree.selectNode(selectnode,false);
}
// 清空ztree检索配置信息
function clearSetZtreeSearch(){
	if(_searchZtreeTime) clearTimeout(_searchZtreeTime);// 清空延迟执行
	// 延迟5秒清空ztree检索配置信息
	_searchZtreeTime = setTimeout(function(){
		_searchZtreeKeyWord = null;//清空检索ztree的关键字
		_searchZtreeSelectedkey = 0;//重置检索ztree的第几个关键字
	},5000);
}
/**
 * 删除产品图片
 * @param obj 当前对象
 */
function productcodepigdelete(obj){
	var name = $(obj).attr("rel");
	$.ajax({
		type: 'POST',
		url : TP_APP+"/Public/pigdelete",
		data: {imagename:name},
		success:function(succ){
			if(succ == '1') {
				$(obj).parent().parent().css("display","none");
			}
		}
	});
}
/**
 * 快捷检索切换条件
 * @param $this 当前对象
 * @param $model 当前模型
 * @author 杨东
 */
function quickSearchChange($this,$model){
	$changeDiv = $($this).parents("tr");
	$changeDiv.find("div."+$model+"quickSearch").hide();
	$val = $($this).val();
	$changeDiv.find("div."+$model+$val).show();
}
/**
 * 上一条单据/下一条单据刷新专用
 * @param m 当前model
 * @param f 当前函数
 * @param $id 当前修改页面ID
 * @author 杨东
 */
function changeTheDocNum(m,f,$id,issave){
	if(issave){
		alertMsg.confirm("您的新建单据还未保存，是否要离开本页面！", {
			okCall: function(){
				navTab.reload(TP_APP + "/" + m + "/"+f+"/id/"+$id);
				$(".navTab-tab").find("li.selected").attr("url",TP_APP + "/" + m + "/"+f+"/id/"+$id);
			}
		});
	} else {
		navTab.reload(TP_APP + "/" + m + "/"+f+"/id/"+$id);
		$(".navTab-tab").find("li.selected").attr("url",TP_APP + "/" + m + "/"+f+"/id/"+$id);
	}
}
/**
 * 保存并新增单据
 * @param form 当前表单
 * @param m 当前model
 * @author 杨东
 */
function saveAndAdd(form,m,isedit){
    var isedit = isedit==undefined ? 0:1;
    var $box = navTab.getCurrentPanel();
    if(isedit) $box.find("input[name='callbackType']").val("");
    return validateCallback(form, function(json){
	navTabAjaxDone(json);
	if( $box.find("#after_save_then_addnew").length ){
	   $box.find("#after_save_then_addnew").click();
	}
	if(isedit==1) setTimeout(function(){navTab.closeTab( m+"edit" );},100);
    });
}


/**
 * 保存并新增单据
 * @param form 当前表单
 * @param m 当前model
 * @author 杨东
 */
function saveAndEdit(form,m){
    var $box = navTab.getCurrentPanel();
    return validateCallback(form, function(json){
    	DWZ.ajaxDone(json);
    	if (json.statusCode == DWZ.statusCode.ok) {
    		navTabAjaxDone(json);
    		if( $box.find("#after_save_then_edit").length ){
    			var id=json.data;
    			var url= $box.find("#after_save_then_edit").attr("href");
    		   $box.find("#after_save_then_edit").attr("href",url+"/id/"+id).click();
    		}
    		setTimeout(function(){navTab.closeTab( m+"add" );},100);
    	}
    });
}

/**
 * 在查看页面进行新增
 * @param m tabid 
 */
function audiViewAndAdd(m){
	 var $box = navTab.getCurrentPanel();
	 if( $box.find("#after_audiView_then_addnew").length ){
		   $box.find("#after_audiView_then_addnew").click();
	 }
	 navTab.closeTab( m+"edit" );
}


/**
 * 新增TREE节点
 * @param $this 当前对象
 * @param $treeId 当前tree对象ID
 * @author 杨东
 */
function addNodes($this,$treeId,$field){
	$this = $($this);
	var zTree = $.fn.zTree.getZTreeObj($treeId),
	nodes = zTree.getSelectedNodes(),
	treeNode = nodes[0];
	var id=treeNode.id;
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
	if($field != undefined){
		$field = $field+"/"+id;
	} else {
		$field = '';
	}
	var url=$this.attr("url")+$field;
	if(!url.isFinishedTm()){
		alertMsg.error($this.attr("warn")||DWZ.msg("alertSelectMsg"));
		return false;
	}
	$.pdialog.open(url,rel,title,options);
}
/**
 * 修改TREE节点
 * @param $this 当前对象
 * @param $treeId 当前tree对象ID
 * @author 杨东
 */
function editNodes($this,$treeId){
	$this = $($this);
	var zTree = $.fn.zTree.getZTreeObj($treeId),
	nodes = zTree.getSelectedNodes(),
	treeNode = nodes[0];
	var id=treeNode.id;
	if(id==0){
		alertMsg.error('顶级节点不允许修改');
		return false;
	}
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
	var url=$this.attr("url")+id;
	DWZ.debug(url);
	if(!url.isFinishedTm()){
		alertMsg.error($this.attr("warn")||DWZ.msg("alertSelectMsg"));
		return false;
	}
	$.pdialog.open(url,rel,title,options);
}
/**
 * 保存TREE节点并保存数据
 * @param $form 当前表单
 * @param $inputName 当前表单中和tree的名字
 * @param $treeId 当前tree对象ID
 * @author 杨东
 */
function refreshTreeEdit($form,$inputName,$treeId) {
	$this = $($form);
	var $input = $inputName.split(",");
	if($input[0] == undefined){
		return false;
	} 
	if($input.length == 1){
		$name = $this.find("input[name='"+$input[0]+"']").val();
	} else if($input.length == 2){
		$name = $this.find("input[name='"+$input[0]+"']").val() + $this.find("input[name='"+$input[1]+"']").val();
	} else if($input.length == 3){
		if($input[2] == 1){
			$name = "["+$this.find("input[name='"+$input[0]+"']").val() + "]" + $this.find("input[name='"+$input[1]+"']").val();
		}
	}
	return validateCallback($form, function(json){
		dialogAjaxDone(json);
		if (json.statusCode == DWZ.statusCode.ok) {
			var id = json.data;
			var zTree = $.fn.zTree.getZTreeObj($treeId);
		    var nodes = zTree.getSelectedNodes();
		    nodes[0].name=$name;
		    zTree.updateNode(nodes[0]);
		}
	});
}
/**
 * 新增插入TREE节点并保存数据
 * @param $form 当前表单
 * @param $inputName 当前表单中和tree的名字
 * @param $treeId 当前tree对象ID
 * @param $rel 当前tree对象节点刷新rel
 * @param $url 当前tree对象节点刷新url
 * @author 杨东
 */
function refreshTreeAdd($form,$inputName,$treeId,$rel,$url,$pid) {
	$this = $($form);
	var $input = $inputName.split(",");
	if($input[0] == undefined){
		return false;
	} 
	if($input.length == 1){
		$name = $this.find("input[name='"+$input[0]+"']").val();
	} else if($input.length == 2){
		$name = $this.find("input[name='"+$input[0]+"']").val() + $this.find("input[name='"+$input[1]+"']").val();
	} else if($input.length == 3){
		if($input[2] == 1){
			$name = "["+$this.find("input[name='"+$input[0]+"']").val() + "]" + $this.find("input[name='"+$input[1]+"']").val();
		}
	}
	return validateCallback($form, function(json){
		dialogAjaxDone(json);
		if (json.statusCode == DWZ.statusCode.ok) {
			var id = json.data;
			var zTree = $.fn.zTree.getZTreeObj($treeId);
			nodes = zTree.getSelectedNodes();
		    treeNode = nodes[0];
		    var $pids = treeNode.id;
		    if($pid != undefined){
		    	$pids = $pid;
		    	nodes = zTree.getNodesByParam("id", $pid, null);
		    	treeNode = nodes[0];
		    }
		    var node = {id:id, pId:$pids, name:$name, title:$name, target:'ajax',rel:$rel,url:$url+id};
		    treeNode = zTree.addNodes(treeNode, node);
		    $("#"+$treeId).initUI();
		}
	});
}
/**
 * 新增插入TREE节点并保存数据
 * @param $treeId 当前tree对象ID
 * @param $url 删除ACTION的链接
 * @param $rel 当前tree对象节点刷新rel
 * @param $noparent 判断是否选中根节点
 * @author 杨东
 */
function delNodes($treeId,$url,$rel,$noparent){
	var zTree = $.fn.zTree.getZTreeObj($treeId);//当前数对象
	var nodes = zTree.getSelectedNodes();// 当前选中的节点
	var treeNode = nodes[0];//节点数据
	if(treeNode == undefined){
		alertMsg.error('请选中节点');
		return false;
	}
	if(treeNode.id==0){
		alertMsg.error('顶级节点不允许删除');
		return false;
	}
	alertMsg.confirm("您确定要删除当前节点吗？", {
		okCall: function(){
			$.ajax({
				type:'POST',
				dataType:"json",
				url:TP_APP+$url+treeNode.id,
				cache:false,
				success:function(json){
					DWZ.ajaxDone(json);
					if (json.statusCode != DWZ.statusCode.ok){
						error:DWZ.ajaxError;
					} else {
						if($noparent){
							// 获取当前节点的父节点的所有子节点
							var childrens = zTree.getNodesByParam("null", null , treeNode.getParentNode());
							if(childrens.length>0){
								zTree.selectNode(childrens[0]);//选中第一个子节点
								$("#"+$rel).loadUrl(childrens[0].url,{},function(){
							    	$("#"+$rel).find("[layoutH]").layoutH();});//刷新第一个子节点的数据
							} else {
								zTree.selectNode(treeNode.getParentNode());//选中父节点
							}
						} else {
							zTree.selectNode(treeNode.getParentNode());//选中父节点
							if(treeNode.getParentNode().url){
								$("#"+$rel).loadUrl(treeNode.getParentNode().url,{},function(){
							    	$("#"+$rel).find("[layoutH]").layoutH();});//刷新父节点链接
							}
						}
						zTree.removeNode(treeNode);
					}
				},
				error:DWZ.ajaxError
			});
		}
	});
}
function lookupCallbackSetCookie(json,$this){
	$name = $($this).attr('name');
	json['setCookie'] = $name;
	$m = $($this).attr('m');
	$.ajax({
		type:'POST',
		dataType:"json",
		url:TP_APP+"/"+$m+"/lookupCallbackSetCookie",
		cache:false,
		data:json,
		success:function(json){
			if (json.statusCode != DWZ.statusCode.ok){
				error:DWZ.ajaxError;
			}
		},
		error:DWZ.ajaxError}
	);
}
/**
 * 清除当前navTab中的某些input文本框
 * @param $inputName 需要清除的input文本框的名称
 * @author 杨东
 */
function clearInput($inputName){
	var $box = navTab.getCurrentPanel();
	var $input = $inputName.split(",");
	for (var i=0; i<$input.length; i++) {
		$box.find("input[name='"+$input[i]+"']").val('');
	}
}
/**
 * 清除当前Dialog中的某些input文本框
 * @param $inputName 需要清除的input文本框的名称
 * @author jiangx
 */
function clearInputInDialog($this,$inputName){
	$this = $($this);
	var $box = $this.closest("div.dialog");
	var $input = $inputName.split(",");
	for (var i=0; i<$input.length; i++) {
		$box.find("input[name='"+$input[i]+"']").val('');
	}
}

/**
 * 发件箱类似人员添加函数
 * @param $ulid UL的ID
 * @param $inputName 添加的input名称
 * @param $id input的值
 * @param $name 显示的名称
 * @param string $otherulid 第二个UL的ID modifier xiafengqin date 2013-08-13 应用于两个人员添加框 可扩展为多个
 * @param string $inputemail，$email email地址 xiafengqin date 2013-08-19 应用于外部邮件的发送
 * @author 杨东
 */
function appendUserHtml($ulid, $inputName, $id, $name, $chainname, $otherulid, $email, $inputemail){
	var isTrue = true;
	$("#"+$ulid).find("input").each(function(){
		if($(this).val() == $id) {
			isTrue = false;
		}
	});
	if($otherulid && isTrue){
		$("#"+$otherulid).find("input").each(function(){
			if($(this).val() == $id) {
				isTrue = false;
			}
		});
	}
	var $str = "";
	if(isTrue){
		$str += "<li>";
		$str += "<input type='hidden' name='"+$inputName+"[]' value='"+$id+"'/>";
		$str += "<span><input type='hidden' name='"+$chainname+"[]' value='"+$name+"'>"+$name+"</span>";
		$str += "<input type='hidden' name='"+$inputemail+"[]' value='"+$email+"'>";
		$str += "<a class='delAddressee' title='删除' onclick='this.parentNode.parentNode.removeChild(this.parentNode);' href='javascript:;'>x</a>";
		$str += "</li>";
	}
	return $str;
}

//mispurchaseapplymas add and edit action about sub
function itemDetailmispurchaseapplymas( $tr ){
	$tr.find('input.qty').blur(function(){
		var $obj=$(this).parents("tr");
		updateapplysubblur($obj,1);
	});
	$tr.find('input.unitprice').blur(function(){
		var $obj=$(this).parents("tr");
		updateapplysubblur($obj,1);
	});
	$tr.find('input.taxunitprice').blur(function(){
		var $obj=$(this).parents("tr");
		updateapplysubblur($obj,0);
	});
	$tr.find('select.taxid').change(function(){
		var $obj=$(this).parents("tr");
		updateapplysubblur($obj,1);
	});
}
function updateapplysubblur($box,step){
	//获得税金率
	var taxval=$box.find("select.taxid option:checked").attr("key");
	//数量 
	var qty =$box.find("input.qty").val();
	//不含税单价
	var unitprice =$box.find("input.unitprice").val();
	//含税单价
	var taxunitprice =$box.find("input.taxunitprice").val();
	//格式化数据 
	qty =Number(qty.replace(/,/g,""));
	unitprice =Number(unitprice.replace(/,/g,""));
	taxunitprice =Number(taxunitprice.replace(/,/g,""));
	taxval =Number(taxval);
	$box.find("input.qty").val(FormatN(qty));
	if(step==1){
		//不含税单价计算含税单价
		var taxunitprice=comboxMathematicalOperation(unitprice+'*'+(taxval+100)/100);
		$box.find("input.unitprice").val(FormatN(unitprice));  //赋值不含税单价 
		$box.find("input.taxunitprice").val(FormatN(taxunitprice));  //赋值含税单价
	}else{
		//含税单价计算不要含税单价 
		var unitprice=comboxMathematicalOperation(taxunitprice+'/'+(taxval+100)/100);
		$box.find("input.unitprice").val(FormatN(unitprice));  //赋值不含税单价 
		$box.find("input.taxunitprice").val(FormatN(taxunitprice));  //赋值含税单价 
	}
	$box.find("input.unitpricetotal").val(FormatN(unitprice*qty));  //赋值含税单价
	$box.find("input.taxunitpricetotal").val(FormatN(taxunitprice*qty));  //赋值含税单价
}
// 新增产品快捷查询 绑定事件
function blurAjaxForData(e){
	//当前对象 
	var _this = $(this); 
	var $form = this.form;
	//关闭浏览器自动记录输入的内容 
	_this.attr('autocomplete', 'off');
	if(isIE8){
		//ie8浏览器下面 
		var curKey = e.keyCode; //获取键盘按钮值 
		if(curKey != 13 && curKey != 38 && curKey != 40){
			var prev = _this.prev(); //遍历同级所有节点 
			if(prev.attr('auto') == 1){
				if(e.type != 'dblclick' && prev.val() == _this.val()){
					return false;
				}else{
					prev.val(_this.val());
				}
			}
		}else{
			return false;
		}
	}
	if(checkBlurProductTimeout){
		//取消延迟提交 
		clearTimeout(checkBlurProductTimeout);
		checkBlurProductTimeout = null;
	}
	checkBlurProductTimeout = setTimeout(function(){
		//进行延迟提交 
		$form.onsubmit();
	}, 1000);
}
//divSearch演变而来，主要解决检索时不出现“加载中”DIV层
function divGlobalFalseSearch(form,rel){
	var $form = $(form);
	if (form[DWZ.pageInfo.pageNum])
		form[DWZ.pageInfo.pageNum].value = 1;
	if (rel) {
		var $box = $("#" + rel);
		$.ajax({
			type:'POST',
			url:$form.attr("action"),
			data : $form.serializeArray(),
			async:true,
			global:false,
			success:function(response){
				var json=DWZ.jsonEval(response);
				if(json.statusCode==DWZ.statusCode.timeout) {
					alertMsg.error(json.message||DWZ.msg("sessionTimout"),{okCall:function(){
					if($.pdialog)$.pdialog.checkTimeout();
					if(navTab)navTab.checkTimeout();
					DWZ.loadLogin();}});
				}
				if(json.statusCode==DWZ.statusCode.error){
					if(json.message)alertMsg.error(json.message);
				}else{
					if(json.statusCode==DWZ.statusCode.timeout){
						$box.html(json.message+'<div style="display:none;">'+response+'</div>').initUI();
					} else {
						$box.html(response).initUI();
					}
				}
				$box.find("[layoutH]").layoutH();
			},
			error:DWZ.ajaxError
		});
	}
	return false;
}
/**
 * 快速度信息物料JS
 * @param form	当前表单
 * @param rel	指定刷新区域的ID
 * @param $masid 当前数据的单头ID
 * @param $md 需要查询的模型
 * @author liminggang
 * @data 2013-09-7
 */
function onSubMitProduct(form,rel){
	//当前对象 
	var $form = $(form);
	var $referurl = $(form).attr("action");
	if (form[DWZ.pageInfo.pageNum]){
		form[DWZ.pageInfo.pageNum].value = 1;
	}
	//判断物料编码是否为空
	var prodcode=$(form).find("input[name='code']").val();
	if(prodcode == ''){
		alertMsg.error("请填写物料编码!");
		return false
	}
	//判断物料名称是否为空
	var prodname=$(form).find("input[name='prodname']").val();
	if(prodname == ''){
		alertMsg.error("请填写物料名称!");
		return false
	}
	//判断物料名称是否为空
	var prodsize=$(form).find("input[name='prodsize']").val();
	if(prodsize == ''){
		alertMsg.error("请填写物料规格!");
		return false
	}
	//判断单位
	var baseunitid=$(form).find("input[name='baseunitid']").val();
	if(baseunitid == ''){
		alertMsg.error("请选择物料单位!");
		return false
	}
	var $url = TP_APP + "/Common/lookupInsertProduct";
	if (rel) {
		var $box = $("#" + rel);
		$.ajax({
			type:'POST',
			url:$url,
			data : $form.serializeArray(),
			async:true,
			global:false,
			success:function(response){
				var json=DWZ.jsonEval(response);
				if (json.statusCode == DWZ.statusCode.ok) {
					$box.loadUrl($referurl,$form.serializeArray(),function(){
					$box.find("[layoutH]").layoutH();});
				} else {
					alertMsg.confirm(json.message + '! 是否自动生成？',{
						okCall:function(){
							$.ajax({
								type:'POST',
								url: TP_APP + "/MisProductCode/lookupisgetProductCode",
								data : {typeid:$(form).find("input[name='type']").val()},
								success:function(response){
									$(form).find("input[name='code']").val(response);
								}
							});
						}
					});
				}
			},
			error:DWZ.ajaxError
		});
	}
}
// 删除表格的第一个TR对象
function removeTR($this){
	$this = $($this);
	$this.parents("tr:first").remove();
}
//点击审核弹出页面
function openAuditDialog(){
	var options = {};
		options.width = 400;
		options.height = 250;
		options.mask = true;
	$.pdialog.open(TP_APP+"/Public/auditDialog", "auditDialog", "审核意见",options);
}
function openAuditJqDialog(){
	var options = {};
	options.width = 600;
	options.height = 300;
	options.mask = true;
	$.pdialog.open(TP_APP+"/Public/auditJqDialog/jq/1", "auditJqDialog", "审核意见",options);
}
function openStartProcessDialog(){
	var options = {};
	options.width = 600;
	options.height = 300;
	options.mask = true;
	$.pdialog.open(TP_APP+"/Public/startProcessDialog", "startProcessDialog", "审核意见",options);
}
// 点击打回弹出业务 传入当前model名字
function openBackDialog(model){
	var options = {};
		options.width = 400;
		options.height = 250;
		options.mask = true;
	$.pdialog.open(TP_APP+"/Public/backDialog/model/"+model, "backDialog", "打回意见",options);
}
/**
 * 作用：修改页面的删除操作
 * author:xiafengqin
 * @param form  整个表单，一般传this
 * @param id 当前那条数据的id
 * @param model 当前的model
 * @param types 是dialog还是navTab
 */
function deleteRecord(form,id,model,types){
	var $form = $(form.form);
	alertMsg.confirm('确定要删除该数据？',{
		okCall:function(){
			$form.attr("action", TP_APP + "/" + model + "/delete/navTabId/" + model);
			$form.submit();
//				if(types == 'dialog'){
//					$.pdialog.closeCurrent();
//					return validateCallback($form,dialogAjaxDone);
//				}
//				if(types == 'navTab'){
//					navTab.closeCurrentTab();
//					return validateCallback($form,navTabAjaxDone);
//				}
		}
	})
	
}
function ifCheckedTrByQty(obj,istr){
    if(istr==0){
	    var v=$(obj).val();
	    var $tr = $(obj).parents("tr");
    }else{
	    var v=$(obj).find("input.checkqty").val();
	    var $tr = $(obj);
    }
    var re = /^[0-9]+.?[0-9]*$/;
    v = Number(v.replace(/,/g, ""));
    var $ftdfisrtcheckbox = $tr.find("input[type='checkbox']:first");
    if( v<=0 || !re.test(v) ) {
	    $ftdfisrtcheckbox.removeAttr("checked");
	    $tr.removeClass("selected");
	    $tr.removeClass("checkedbox");
    }else{
	    $ftdfisrtcheckbox.attr("checked",true);
	    $tr.addClass("selected");
	    $tr.addClass("checkedbox");
    }
}
//选择带回回调函数 jiangx 2013-11-14
function getselectoption(args, obj,checkfor){
	var prefix = '';
	if ($(obj).attr('prefix')) {
		prefix = $(obj).attr('prefix');
	}
	var hiddenfield = $(obj).attr('hiddenfield').split(',');
	var hiddenid = $(obj).attr('hiddenid');
	var showname = $(obj).attr('showname');
	var shows = $(obj).val().split(',');
	
	var idarr = args.id.split(',');
	var lenth = shows.length;
	var strHtml="";
	var ulid;
	if (checkfor) {
		ulid = $(obj).parents('ul');
	} else {
		ulid = $(obj).prev();
	}
	for (i=0;i< lenth;i++) {
		var isTrue = true;
		$(ulid).find("input[name='"+prefix+hiddenid+"[]']").each(function(){
			if($(this).val() == idarr[i]) {
				isTrue = false;
			}
		});
		if(isTrue){
			strHtml += "<li>";
			strHtml += "<input type='hidden' name='"+prefix+hiddenid+"[]' value='"+idarr[i]+"'/>";
			for (j=0;j<hiddenfield.length;j++) {
				
				if (args[hiddenfield[j]] != undefined ) {
					var str = args[hiddenfield[j]];
					alert(str);
					var strarr = str.split(',');
					strHtml += "<input type='hidden' name='"+prefix+hiddenfield[j]+"[]' value='"+ strarr[i] +"'/>";
				}
			}
			strHtml += "<span><input type='hidden' name='"+prefix+showname+"[]' value='"+shows[i]+"'>"+shows[i]+"</span>";
			
			strHtml += "<a class='delAddressee' title='删除' onclick='this.parentNode.parentNode.removeChild(this.parentNode);' href='javascript:;'>x</a>";
			strHtml += "</li>";
			$(obj).val("");
			$(obj).focus();
		} else {
			$(obj).val("");
		}
	}
	$(ulid).children().last().before(strHtml);
	
}
function getselectoptionBycheckfor(obj,json) {
	getselectoption(json, obj, true);
}
function changeInventoryMoveObj( obj ){
	var $obj = $(obj);
	var t= $obj.val();
	if(t==1){
		$obj.parents("form").find("label.movein").show();
		$obj.parents("form").find("label.moveout").hide();
	}else{
		$obj.parents("form").find("label.movein").hide();
		$obj.parents("form").find("label.moveout").show();
	}
}
//邮件数量及时刷新
function refreshEmailCount(){
	$.ajax({
        type:'POST',
        url:TP_APP+"/MisMessageInbox/lookupemailcount",
        async:true,
        global:false,
        dataType: "json",
        success:function(res){
        	$("span.js-systememail").text(res.countSystemMessage);
        	$("span.js-selfemail").text(res.countInboxMessage);
        	$("span.js-countemail").text(res.countsum);
        	if(res.countsum > 0) {
        		$("span.js-countemail").parent().show();
        	} else {
        		$("span.js-countemail").parent().hide();
        	}
        	if(res.countInboxMessage > 0){
        		$("img.js-selfimg").show();
        	} else {
        		$("img.js-selfimg").hide();
        	}
        	if(res.countSystemMessage > 0){
        		$("img.js-systemimg").show();
        	} else {
        		$("img.js-systemimg").hide();
        	}
		}
    });
}
function openNewWindowsDisplayFile(obj){
	var url= $(obj).attr("rel"); 
	$(obj).popupWindow({
		windowURL: url,
		centerScreen: 1,
		scrollbars: 0,
		width:960,
		resizable:1,
		scrollbars:1
	});
}
//监听键盘的退格键和del键  用于选人
var addressee = {
    select: function(obj){
        $(obj).toggleClass('selected');
    },
    selectToggle: function(ev){
    	var parentLi = $(ev.target).closest('li');
        if(!parentLi.is('.addresseeText')){
            addressee.select(parentLi[0]);
        }
    },
    unselect: function(obj){
        $(obj).parent().siblings().filter('.selected').removeClass('selected');
    },
    del: function(obj, e){
        if(obj.value == ''){
			var key = 0;
			if(e.which){
				key = e.which;
			} else if(e.keyCode) {
				key = e.keyCode;
			}
            if(key == 8 || key == 46){
                if(!$(obj).parent().siblings().is('.selected')){
                    addressee.select($(obj).parent().prev());
                } else {
                  $(obj).parent().siblings().filter('.selected').remove();
                }
            }
        }
    },
    clearVal:function(obj){
    	$(obj).val("");
    }
};
 //checkFor的callback函数  用于选人时使用的checkFor的回调函数
function addresseeInput(json,obj){
	var $ulId = obj.parents('ul');
    var $inputName = obj.attr('inputName'); //id在页面input框的name
    var $chainname = obj.attr('chainname'); //name在页面input框的name
    var $emailName = obj.attr('emailName'); //email在页面input框的name
    var isTrue = true;
    var $id = json.id;
    var $name = json.name;
    var $email = json.email;
    $ulId.find("input[name='"+$inputName+"[]']").each(function(){
        if($(this).val() == $id) {
            isTrue = false;
        }
    });
    var $str = "";
    if(isTrue){
        $str += "<li>";
        $str += "<input type='hidden' name='"+$inputName+"[]' value='"+$id+"'/>";
        $str += "<span><input type='hidden' name='"+$chainname+"[]' value='"+$name+"'>"+$name+"</span>";
		if ($emailName !== undefined) {
			$str += "<input type='hidden' name='"+$emailName+"[]' value='"+$email+"'>";
		}
        $str += "<a class='delAddressee' title='删除' onclick='this.parentNode.parentNode.removeChild(this.parentNode);' href='javascript:;'>x</a>";
        $str += "</li>";
        obj.val("");
        obj.focus();
        obj.parent().before($str);
    }else {
        obj.val("");
    }
}
/**
 * 通用人员选择 --单个选择
 * @param id	人员id
 * @param name	人员名称
 * @param ULid 作用域ul节点的id属性值
 * @param index 标识页面上的第几个作用域ul，可不用，但作用域ULid必须为 "xxxxx_1"形式
 * @author jiangx
 * @data 2014-02-12
 */
function Common_SingleSelectPerson(id, name, ULid, index){
	if (!index) {
		index = ULid.split('-');
		if (index[1]) {
			index = parseInt(index[1]) - 1;
		} else {
			index = 0;
		}
	}
	//实例化input标签的name属性值 第一个作用域Ul中，第二个作用域Ul中，目前只初始化了两个作用域的id，name两个属性值
	//如果需要其他属性，可添加
	var options = [{'id':'leadid','name':'leadname'},{'id':'staffid','name':'staffname'}];
	var $strHtml = appendUserHtml(ULid,options[index]['id'],id,name,options[index]['name']);
	if ($strHtml) {
		$("#"+ULid).children().last().before($strHtml);
	}
    $("#"+ULid).find('input').focus();
};
/**
 * 产品浮动框，弹出产品库存数量信息
 */
$(function(){
	$(".js-mousekeyup").live("mouseover",ab_mouseover);
	$(".js-mousekeyup").live("mouseout",plugin_mouseout);
	
	function ab_mouseover(e){
		var _this = $(this);
		//var xOffset = 78;var yOffset = -48;var t = 0;
		//$("#tipsview").css("top",(e.pageY - xOffset) + "px").css("left",(e.pageX + yOffset+26) + "px").append(content).fadeIn("slow").append("<img style='position: absolute;z-index:1000;' src='"+img+"' class='tipsview'  id='tipsview2' />");
		$(_this).siblings(".js-whidQty").show();
	}
	function plugin_mouseout(){
		var _this = $(this);
		$(_this).siblings(".js-whidQty").hide();
	}
})
/**
 * 通用人员选择 --多个选择
 * @param mateStr	匹配标识
 * @param ULid	作用域ul节点的id属性值
 * @param index 标识页面上的第几个作用域ul
 * @author jiangx
 * @data 2014-02-12
 */
function Common_MultiSelectPerson(mateStr,ULid, index){
 	var id;
 	var name; 
 	$("."+mateStr).find("input[type='checkbox']").each(function(){
 		if($(this).attr("checked")=='checked'){
 			id = $(this).attr("userid");
 			name = $(this).attr("username");
 			Common_SingleSelectPerson(id, name, ULid, index);
 		}
 	});
}
/**
 * 作用：用于合同页面快捷新增开票申请单（仅合同页面使用）
 * author:renling
 * @param $id  当前数据表ID
 * @param $objmodelname  当前模型名称
 * @param $attid  当前模型对应的附件ID
 */
function quickinvoice($id,$objmodelname,$attid){
	navTab.openTab("MisBusinessInvoiceManageradd",TP_APP+"/MisBusinessInvoiceManager/add/objmodelname/"+$objmodelname+"/attid/"+$attid+"/aid/"+$id,{title : "开票申请_新增"});
}
//打开填写审核意见DIV
function openAuditDiv(){
	var thisForm = $(this.form);
	thisForm.find("div.js-auditDiv").remove();
	$.ajax({
		type: "POST",
		url: TP_APP+"/Public/auditDiv",
		data: {},
		async:true,
		success: function(data){
			var con = $(data);
			con.initUI();
			thisForm.append(con);
			//thisForm.find("textarea.js-focustextarea").focus();
		},
		global: false
	});
}
/***
 * 将时间戳日期转换成正常的日期格式
 * authoer libo
 * date 2014-3-9
 */
function transTime(ob){
	var mydate = new Date(parseInt(ob) * 1000);
	var time = '';
	time += mydate.getFullYear() + '-';   //返回年份
	time += mydate.getMonth()+1 + '-';    //返回月份，因为返回值是0开始，表示1月，所以做+1处理
	time += mydate.getDate();
	return time;
}
// 关闭审核或者打回DIV
function closeAuditDiv(){
	var thisForm = $(this.form);
	thisForm.find("div.js-auditDiv").remove();
}
// 打开填写打回意见DIV
function openBackDiv(){
	var thisForm = $(this.form);
	var model = $(this).attr("m");
	thisForm.find("div.js-auditDiv").remove();
	$.ajax({
		type: "POST",
		url: TP_APP+"/Public/backDiv",
		data: {},
		async:true,
		success: function(data){
			var con = $(data);
			con.initUI();
			thisForm.append(con);
			//thisForm.find("textarea.js-focustextarea").focus();
			thisForm.find("button.submitBackDiv").attr("m",model);
		},
		global: false
	});
}
/**
 * 表格合并行	杨东 2014-3-20
 * table_id：表格的ID
 * table_colnum：需要合并的列
 * contrast_colnum：合并列时需要对比的列 没有时可以不用传参
 */
function table_rowspan(table_id,table_colnum,contrast_colnum){
	var table_firsttd = "";//对应的第一个合并内容
	var table_SpanNum = 0;//合并数量
	// 判断有没有对比对象
	if(contrast_colnum){
		var contrast_firsttd = "";//保存合并时的对比对象
		var table_contrast = $("#"+table_id + " tr td:nth-child(" + contrast_colnum + ")");// 获取所有的对比td对象
	}
	// 获取所有的合并TD对象
	$("#"+table_id + " tr td:nth-child(" + table_colnum + ")").each(function(i) {
		if(i==0){
			// 第一个默认合并对象
            table_firsttd = $(this);// 设置合并对象
            if(contrast_colnum) contrast_firsttd = $(table_contrast[i]);//设置合并对比对象
            table_SpanNum = 1;// 合并数量初始化
        }else{
        	// 合并对象判断
        	if(contrast_colnum){
        		if(table_firsttd.text() == $(this).text() && contrast_firsttd.text() == $(table_contrast[i]).text()){
        			table_SpanNum++;//合并值累加
                    $(this).hide(); //remove();
                    table_firsttd.attr("rowSpan",table_SpanNum);// 设置合并值
        		} else {
        			 table_firsttd = $(this);// 设置合并对象
                     contrast_firsttd = $(table_contrast[i]);//设置合并对比对象
                     table_SpanNum = 1;// 合并数量初始化
        		}
        	} else {
        		if(table_firsttd.text() == $(this).text()){
        			table_SpanNum++;//合并值累加
                    $(this).hide(); //remove();
                    table_firsttd.attr("rowSpan",table_SpanNum);// 设置合并值
        		} else {
                    table_firsttd = $(this);// 设置合并对象
                    table_SpanNum = 1;// 合并数量初始化
                }
        	}
        }
	});
}
/***
 * 将表单页面打印输出到插件lodop上
 * authoer libo
 * date 2014-3-27
 */
function tbPrint(sid,md){
	$.ajax({
		type : 'GET',
		url : TP_APP + '/' + md + '/exportsSample',
		cache : false,
		data : {
			id : sid,
			isprint : 1
		},
		success : function(json) {
			var re=DWZ.jsonEval(json);
			var printurl="file:///"+re;
			LODOP=getLodop();
			//LODOP.PRINT_INIT("打印控件功能演示_Lodop功能_按网址打印");
			LODOP.ADD_PRINT_URL(30,20,746,"100%",printurl);
			LODOP.SET_PRINT_STYLEA(0,"HOrient",3);
			LODOP.SET_PRINT_STYLEA(0,"VOrient",3);
//			LODOP.SET_SHOW_MODE("MESSAGE_GETING_URL",""); //该语句隐藏进度条或修改提示信息
//			LODOP.SET_SHOW_MODE("MESSAGE_PARSING_URL","");//该语句隐藏进度条或修改提示信息
			LODOP.PREVIEW();
		}
	});
}
// begin 柔性流程函数 杨东
// 流程选择对象
var addflowsobj = null;
/**
 * 查看柔性流程
 * @param obj 当前按钮对象
 */
function showFlows(obj,m){
	var val = $(obj).closest("div.tml-form-col").find("select").val();
	if(val == 0) {
		alertMsg.error("请先选择流程！");
		return false;
	}
	// 设置流程选择对象
	addflowsobj = $(obj).prev();
	var m = m||"CommonFlows";
	var options = {};
	options.param = {id:val};
	options.width = 930;
	options.height = 580;
	options.mask = true;
	options.resizable = false;
	options.maxable = false;
	options.minable = false;
	$.pdialog.open(TP_APP + '/' + m +'/lookupShowFlows', "ShowFlows", "流程查看", options);
}
/**
 * 新增柔性流程
 * @param obj 当前按钮对象
 */
function addFlows(obj,m){
	addflowsobj = $(obj);
	var m = m||"CommonFlows";
	var options = {};
	options.width = 930;
	options.height = 580;
	options.mask = true;
	options.resizable = false;
	options.maxable = false;
	options.minable = false;
	$.pdialog.open(TP_APP + '/' + m +'/lookupAddFlows', "addFlows", "流程新增", options);
}
/**
 * 提交柔性流程 初次提交时出发
 * @param obj 当前按钮对象
 * @param m 当前提交流程的model名次
 * @param url 扩展URL参数
 * @returns {Boolean}
 */
function commitFlows(obj,m,url) {
	var $form = $(obj.form);
	var m = m?m:"CommonFlows";
	var url = url?url:"";
	if (!$form.valid()) {
		return false;
	}
	var $callback = navTabAjaxDone;
	$.ajax({
		type : 'POST',
		url : TP_APP + "/" + m + "/commitFlows"+url,
		data : $form.serializeArray(),
		dataType : "json",
		cache : false,
		success : function(response) {
			var j = DWZ.jsonEval(response);
			if (j.checkfield != "") {
				$form.find("input[name='" + j.checkfield + "']").val(j.data);
			}
			$callback(response);
		},
		error : DWZ.ajaxError
	});
	return false;
}
/**
 * 刷新流程选择的combox对象
 * @param selected 选中对象
 */
function refreshSelect(selected){
	$.ajax({
		type: "POST",
		url: TP_APP + '/CommonFlows/lookupRefreshSelect',
		success: function(data){
			var con = $(data);
			con.children("option").each(function(){
				var v = $(this).val();
				if(v == selected){
					$(this).attr("selected","selected");
				}
			});
			addflowsobj.prev().remove();
			addflowsobj.before(con);
			addflowsobj.prev().combox();
		}
	});
}
//end 柔性流程函数 杨东
// 获取当前时间对象在本年的第几周
function getIso8601Week(date) {
	var time;
	var checkDate = new Date(date.getTime());
	// Find Thursday of this week starting on Monday
	checkDate.setDate(checkDate.getDate() + 4 - (checkDate.getDay() || 7));
	time = checkDate.getTime();
	checkDate.setMonth(0); // Compare with Jan 1
	checkDate.setDate(1);
	return Math.floor(Math.round((time - checkDate) / 86400000) / 7) + 1;
}
/**
 * 设置input的回车绑定
 * obj当前对象 type 当前表单类型 dialog or navTab
 */
function setInputKeydown(obj,type){
	var $box = type == "dialog" ? $.pdialog.getCurrent() : navTab.getCurrentPanel();
	var $inp = $(".pageFormContent input:text",$box);
	obj.find("input:text").unbind('keydown');
	obj.find("input:text").bind('keydown',function(e){
		//获得键盘按键值
		var ev = document.all ? window.event : e;
		//判断是否点击的是回车
		if(ev.keyCode==13) {
			e.preventDefault();
			var nxtIdx = $inp.index(this) + 1;
	        $('.pageFormContent :input:text:eq(' + nxtIdx + ')',$box).focus();
	        return false;
		}
	});
}


/**
 * 动态调用其它函数
 * @parame fn 被调用函数名
 * @parame args 参数列表 是一个数组
 * @author nbmxkj
 * @date 2014-09-16 15:53
 * @example
 * 		nbm_doCallback(eval(函数名),[参数集); 
 */
function nbm_doCallback(fn,args){
	if(typeof(fn)=='string')
		fn = eval(fn);
	try{
    fn.apply(this, args);
	}catch(e){
		$(this).logs(e.message);
	}
}

/**
 * 为左侧栏目导航点击后修改选中状态
 * @author nbmxkj
 * @date 2014-10-09 16:22
 */
function nbm_getfouce(){
    var box = navTab.getCurrentPanel();
	if(typeof(type)!='undefined' && type > 0){
		 $('ul.bar_nav li[rel="'+type+'"]' , box).addClass('active');
	}
    $('ul.bar_nav li' , box).click(function(){
        $('ul.bar_nav li' , box).removeClass('active');
        $(this).addClass('active');
    });
}


function nbmaccordion_getfouce(){
	var box = navTab.getCurrentPanel();
	if(typeof(type)!='undefined' && type > 0){
		 $('div.nbmaccordion ul li[rel="'+type+'"]' , box).addClass('active');
	}
    $('div.nbmaccordion ul li' , box).click(function(){
        $('div.nbmaccordion ul li' , box).removeClass('active');
        $(this).addClass('active');
    });
}


/**
 * 人员多选显示处理回调函数
 * @param data	
 * @param ids	操作的唯一ID值
 * @param condition	用户设置的显示的条件 
 * @returns
 */
function setSelectMultipleUer(data , ids , condition){
	var box = $.pdialog.getCurrent();
	$('#'+ids +' li').not(':last').remove();
	
    $.each(data , function(i , v){
        var liObj = $('<li>');
        $.each(v,function(index , val){
        	var consdStr = condition[i][index];
        	if(consdStr){
        		var condArr = consdStr.split(',');
        	}
            var hid = $('<input type="hidden">');
            hid.attr('name',condArr[0]+'[]');
            hid.val(val);
            liObj.append(hid);
            if(condArr[2]=='text'){
                var spanObj = $('<span>');
                spanObj.html(val);
                liObj.append(spanObj);
            }
        });
        liObj.append('<a href="javascript:;" onclick="this.parentNode.parentNode.removeChild(this.parentNode);" title="删除" class="delAddressee">x</a>');
        $('#'+ids +' li.addresseeText:first').before(liObj);
    });
}
/**
 * 清除指定的所有关联ORG属性值
 * @param org ORG名称
 * @returns
 */
function clearOrg(org){
	var box = navTab.getCurrentPanel();
	$('[class*="'+org+'."]' , box).each(function(){
		$(this).val('');
	});
}
/**
 * 清除指定的所有关联ORG属性值
 * @param org ORG名称
 * @returns
 */
function clearOrgDialog(org){
	var box1 = $.pdialog.getCurrent();
	$('*[class^="'+org+'."]' , box1).each(function(){
		$(this).val('');
	});
}

function auditTuiProcess(obj,model){
	//获取当前对象
	var $this = $(obj);
	var $box = navTab.getCurrentPanel();
	var $id = $box.find("input[name='id']").val();
	//用ajax验证是否数据已发生变化。
	var url=TP_APP+"/"+model+"/lookupAuditTuiProcess";
	$.ajax({
		type:'POST',dataType:"json",url:url,cache:false,
		data:{id:$id},
		global: false,
		success:function(response){
			var json=DWZ.jsonEval(response);
			if(json.json_Msg == 0){
				alertMsg.info(json.json_info);
				return false;
			}else{
				var target = json.target;
				var title=json.title;
				var tabid=json.rel;
				var url=unescape(json.url);
				DWZ.debug(url);
				if(!url.isFinishedTm()){
				alertMsg.error($this.attr("warn")||DWZ.msg("alertSelectMsg"));
				return false;}
				
				if(target=="navTab"){
					navTab.openTab(tabid,url,{title:title});
				}else{
					var options={};
					var w=$this.attr("width");
					var h=$this.attr("height");
					options.width="730";
					options.height="450";
					options.mask="true";
					$.pdialog.open(url,tabid,title,options);
				}
			}
		},
		error:DWZ.ajaxError
	});
}

/**
 * 项目任务管理中，验证执行的JS方法
 * @param obj 对象
 * @param num 值为 1，2  1表示执行，2表示查看
 */
function ValidateRedirect(obj,num,step){
	//获取当前对象
	var $this = $(obj);
	var $data = $this.data("js");
	//用ajax验证是否数据已发生变化。
	var $val= $(obj).val();
	if(step==1){
		//多数据
		var url=TP_APP+"/MisSalesMyProject/lookupValidateRedirectList";
	}else{
		var url=TP_APP+"/MisSalesMyProject/lookupValidateRedirect";
	}
	$.ajax({
		type:'POST',dataType:"json",url:url,cache:false,
		data:{val:$data,num:num},
		global: false,
		success:function(response){
			var json=DWZ.jsonEval(response);
			if(json.json_Msg == 0){
				alertMsg.info("请先完成前置任务");
				return false;
			}else{
				var target = json.target;
				var title=json.title;
				var tabid=json.rel;
				var url=unescape(json.url);
				DWZ.debug(url);
				if(!url.isFinishedTm()){
				alertMsg.error($this.attr("warn")||DWZ.msg("alertSelectMsg"));
				return false;}
				
				if(target=="navTab"){
					navTab.openTab(tabid,url,{title:title});
				}else{
					var options={};
					var w=$this.attr("width");
					var h=$this.attr("height");
					options.width="730";
					options.height="450";
					options.mask="true";
					$.pdialog.open(url,tabid,title,options);
				}
			}
		},
		error:DWZ.ajaxError
	});
}

function printOut(obj,type){
	type = 0;
	var print_url = $(obj).attr("print_url");
	var $box=navTab.getCurrentPanel();
	var id = $box.find(".gridTbody .selected").attr("rel");
	if($(obj).attr("rel_id")!=undefined && $(obj).attr("rel_id")!="" && $(obj).attr("rel_id")!="{sid_node}"){
		id = $(obj).attr("rel_id");
	}
	
	if(!isNaN(parseInt(id))){
		$.post(print_url, { id: id},
		    function(data){
		        var LODOP=getLodop(document.getElementById('LODOP_OB'),document.getElementById('LODOP_EM'));
		    	LODOP.PRINT_INIT("打印控件功能演示_Lodop功能_全页排除按钮");
		    	var url = data;
		    	if(url=="0"){
		    		alertMsg.error("打印模板不存在！");
		    		return;
		    	}
		    	LODOP.ADD_PRINT_URL(30,20,746,"100%",url);
				LODOP.SET_PRINT_STYLEA(0,"HOrient",3);
				LODOP.SET_PRINT_STYLEA(0,"VOrient",3);
		    	if(type==0){
		    		LODOP.PREVIEW(); 
		    	}
		    	else{
		    		if (LODOP.PRINTA())
		    			alert("已发出实际打印命令！");
		    		else
		    			alert("放弃打印！"); 
		    	}
			}
	    );
	}else{
		alertMsg.error("请选择信息！");
	}
};

//文件导出
function fileexport(obj){
	var $box=$(obj).parents("form").eq(0);
	var url = $(obj).attr("export_url");
	var this_model = $box.attr("id");
	var index = this_model.indexOf("_");
	this_model = this_model.substr(0,index);
	//获取rel 即记录id
	var id = $box.find("tr.selected").attr("rel");
	if($(obj).attr("rel_id")!=undefined && $(obj).attr("rel_id")!="" && $(obj).attr("rel_id")!="{sid_node}"){
		id = $(obj).attr("rel_id");
	}
	if(!isNaN(parseInt(id))){
		var export_operate = $(".export_operate."+this_model);
		$box.append(export_operate);
		export_operate.toggle();
		var left = $(obj).offset().left;
		var top = $(obj).offset().top;	
		export_operate.offset({left:left,top:top+$(obj).height()+3});
		export_operate.mouseleave(function(){
			$(this).hide();
		});
		export_operate.find("a").each(function(i){
			var href = $(this).attr("href");
			var index = href.lastIndexOf("/");
			href = href.substr(0,index+1);
			$(this).attr("href",href+id);
		});
	}else{
		alertMsg.error("请选择信息！");
	}
}
/**
 * 更多按钮
 */
function ismore(obj){
	var $box=navTab.getCurrentPanel();
	//获取rel 即记录id	
		$box.append($box.find(".more_ismore"));
		$box.find(".more_ismore").toggle();
		var left = $(obj).offset().left;
		var top = $(obj).offset().top;
		$box.find(".more_ismore").offset({left:left,top:top+$(obj).height()+3});
		$box.find(".more_ismore").mouseleave(function(){
			$(".more_ismore").hide();
		});
	
}
/**
 * 生成单回调函数（lookup带回数据表格回调函数）
 * @param JSON		json 	jsono源数据对象
 * @param Object	obj		当前数据操作对象
 */
function nbm_datatable_callback(json , obj){
	try{
		console.log(json);
		var dtkey = $(obj).attr('dtkey');
		var dtiid = $(obj).attr('dtid');
		var relation = $(obj).attr('dtrelation');
		var dtdata = json['datatable_data'][dtkey];
		console.log(dtdata);
		//能够从数据源中找到关键数据，表示需要插入到当前对应的数据表格中
		if(typeof(dtdata)=='object' && isNullorEmpty(relation)){
			// 将对应关系转换为json
			//relation = $.parseJSON(relation);
			
			//console.log(dtdata);
			//var ret = toArr(dtdata);
			//console.log('beegin reset datatabl data');
			//console.log(ret);
			var arr =$.json2arr(dtdata , relation);
			console.log(arr);
			var datatablelookup1 = initTableWNEWOne("#"+dtiid);
			datatablelookup1.empty();
			datatablelookup1.lookupAddRow(arr);
		}
	}catch(e){
		console.log(e||e.message);
	}
}
/**
 * lookup反写回调函数（lookup被lookup写入值时使用）
 * @param JSON		json 	jsono源数据对象
 * @param Object	obj		当前数据操作对象
 */
function lookup_counter_check(json , obj){
	var lpkey = $(obj).attr('lpkey'); // lookup配置key
	var lpfor = $(obj).attr('lpfor'); // 被反写的对象
	var lpself = $(obj).attr('lpself'); // 当前项的取值对象
	var lporder = $(obj).attr('lporder'); // 外部带回数据取值key
	var c = $(obj).closest('div'); // 取得当前lookup组件的容器
	if(lpkey && lpself && lporder && json[lporder] != null){
		try{
			$.ajax({
				url:TP_URL+'/lookupCounterCheck',
				type:'post',
				data:{'looukupkey':lpkey,'key':lpself,'val':json[lporder]},
				dataType:'json',
				success:function(msg){
					if(msg.code==1){
						if( typeof(msg.data) =='string' ){
							//$.bringBack();
							var obj = $('[class*="'+lpfor+'"]');
							obj.val(msg.data).change();
						}else{
							$.bringBack(msg.data);
						}
					}
				}
			});
		}catch(e){
			console.log(e||e.message);
		}
	}
}

/**
 * 数据表格中的lookup带回数据转换为单数据行
 * @param JSON		json 	jsono源数据对象
 * @param Object	obj		当前数据操作对象
 */
function lookupDataToCell(json , obj){
	console.info('数据表格中的lookup带回数据转换为单数据行');
	console.info(json);
}

//---------------------------------------------------  
// 判断闰年  
//---------------------------------------------------  
Date.prototype.isLeapYear = function()   
{   
    return (0==this.getYear()%4&&((this.getYear()%100!=0)||(this.getYear()%400==0)));   
}   
  
//---------------------------------------------------  
// 日期格式化  
// 格式 YYYY/yyyy/YY/yy 表示年份  
// MM/M 月份  
// W/w 星期  
// dd/DD/d/D 日期  
// hh/HH/h/H 时间  
// mm/m 分钟  
// ss/SS/s/S 秒  
//---------------------------------------------------  
Date.prototype.Format = function(formatStr)   
{   
    var str = formatStr;   
    var Week = ['日','一','二','三','四','五','六'];  
  
    str=str.replace(/yyyy|YYYY/,this.getFullYear());   
    str=str.replace(/yy|YY/,(this.getYear() % 100)>9?(this.getYear() % 100).toString():'0' + (this.getYear() % 100));   
  
    str=str.replace(/MM/,(this.getMonth()+1)>9?(this.getMonth()+1).toString():'0' + (this.getMonth()+1));   
    str=str.replace(/M/g,(this.getMonth()+1));   
  
    str=str.replace(/w|W/g,Week[this.getDay()]);   
  
    str=str.replace(/dd|DD/,this.getDate()>9?this.getDate().toString():'0' + this.getDate());   
    str=str.replace(/d|D/g,this.getDate());   
  
    str=str.replace(/hh|HH/,this.getHours()>9?this.getHours().toString():'0' + this.getHours());   
    str=str.replace(/h|H/g,this.getHours());   
    str=str.replace(/mm/,this.getMinutes()>9?this.getMinutes().toString():'0' + this.getMinutes());   
    str=str.replace(/m/g,this.getMinutes());   
  
    str=str.replace(/ss|SS/,this.getSeconds()>9?this.getSeconds().toString():'0' + this.getSeconds());   
    str=str.replace(/s|S/g,this.getSeconds());   
  
    return str;   
}   
  
//+---------------------------------------------------  
//| 求两个时间的天数差 日期格式为 YYYY-MM-dd   
//+---------------------------------------------------  
function daysBetween(DateOne,DateTwo)  
{   
    var OneMonth = DateOne.substring(5,DateOne.lastIndexOf ('-'));  
    var OneDay = DateOne.substring(DateOne.length,DateOne.lastIndexOf ('-')+1);  
    var OneYear = DateOne.substring(0,DateOne.indexOf ('-'));  
  
    var TwoMonth = DateTwo.substring(5,DateTwo.lastIndexOf ('-'));  
    var TwoDay = DateTwo.substring(DateTwo.length,DateTwo.lastIndexOf ('-')+1);  
    var TwoYear = DateTwo.substring(0,DateTwo.indexOf ('-'));  
  
    var cha=((Date.parse(OneMonth+'/'+OneDay+'/'+OneYear)- Date.parse(TwoMonth+'/'+TwoDay+'/'+TwoYear))/86400000);   
    return Math.abs(cha);  
}  
  
  
//+---------------------------------------------------  
//| 日期计算  
//+---------------------------------------------------  
Date.prototype.DateAdd = function(strInterval, Number,getInterval) { 
	if(getInterval){
		var dtTmp =getInterval;  
	}else{
		var dtTmp =this;
	}
	console.log(getInterval);
    switch (strInterval) {   
       case 's' :return new Date(Date.parse(dtTmp) + (1000 * Number));  
       // case 's' :return new Date(dtTmp.getFullYear(), dtTmp.getMonth(), dtTmp.getDate(), dtTmp.getHours(), dtTmp.getMinutes(), ((dtTmp.getSeconds())+1000 * Number));
       case 'i' :return new Date(Date.parse(dtTmp) + (60000 * Number));  
       //  case 'i' :return new Date(dtTmp.getFullYear(), dtTmp.getMonth(), dtTmp.getDate(), dtTmp.getHours(), ((dtTmp.getMinutes())+60000 * Number), dtTmp.getSeconds());
      case 'h' :return new Date(Date.parse(dtTmp) + (3600000 * Number));  
      //   case 'h' :return new Date(dtTmp.getFullYear(), dtTmp.getMonth(), dtTmp.getDate(), ((dtTmp.getHours())+3600000 * Number), dtTmp.getMinutes(), dtTmp.getSeconds());
        case 'd' :return new Date(Date.parse(dtTmp) + (86400000 * Number)); 
        //  case 'd' :return new Date(dtTmp.getFullYear(), dtTmp.getMonth(), ((dtTmp.getDate())+86400000 * Number), dtTmp.getHours(), dtTmp.getMinutes(), dtTmp.getSeconds());  
        case 'w' :return new Date(Date.parse(dtTmp) + ((86400000 * 7) * Number));  
        case 'q' :return new Date(dtTmp.getFullYear(), (dtTmp.getMonth()) + Number*3, dtTmp.getDate(), dtTmp.getHours(), dtTmp.getMinutes(), dtTmp.getSeconds());  
        case 'm' :return new Date(dtTmp.getFullYear(), (dtTmp.getMonth()) + Number, dtTmp.getDate(), dtTmp.getHours(), dtTmp.getMinutes(), dtTmp.getSeconds());  
        case 'y' :return new Date((dtTmp.getFullYear() + Number), dtTmp.getMonth(), dtTmp.getDate(), dtTmp.getHours(), dtTmp.getMinutes(), dtTmp.getSeconds());  
    }  
}  
  
//+---------------------------------------------------  
//| 比较日期差 dtEnd 格式为日期型或者 有效日期格式字符串  
//+---------------------------------------------------  
Date.prototype.DateDiff = function(strInterval, dtEnd , dtStart) {
	if(!dtStart){
		dtStart = this;  
	}
	var jindu=100;
    if (typeof dtEnd == 'string' )//如果是字符串转换为日期型  
    {   
        dtEnd = StringToDate(dtEnd);  
    }
	if (typeof dtStart == 'string' )//如果是字符串转换为日期型  
    {   
        dtStart = StringToDate(dtStart);  
    } 
	
    switch (strInterval) {   
        case 's' :return parseInt(parseFloat((dtEnd - dtStart) / 1000)*jindu)/jindu;  
        case 'i' :return  parseInt(parseFloat((dtEnd - dtStart) / 60000)*jindu)/jindu;  
        case 'h' :return  parseInt(parseFloat((dtEnd - dtStart) / 3600000)*jindu)/jindu;  
        case 'd' :return  parseInt(parseFloat((dtEnd - dtStart) / 86400000)*jindu)/jindu;  
        case 'w' :return  parseInt(parseFloat((dtEnd - dtStart) / (86400000 * 7))*jindu)/jindu;  
        case 'm' :return (dtEnd.getMonth()+1)+((dtEnd.getFullYear()-dtStart.getFullYear())*12) - (dtStart.getMonth()+1);  
        case 'y' :return dtEnd.getFullYear() - dtStart.getFullYear();  
    }  
}  
  
//+---------------------------------------------------  
//| 日期输出字符串，重载了系统的toString方法  
//+---------------------------------------------------  
Date.prototype.toString = function(showWeek)  
{   
    var myDate= this;  
    var str = myDate.toLocaleDateString();  
    if (showWeek)  
    {   
        var Week = ['日','一','二','三','四','五','六'];  
        str += ' 星期' + Week[myDate.getDay()];  
    }  
    return str;  
}  
  
//+---------------------------------------------------  
//| 日期合法性验证  
//| 格式为：YYYY-MM-DD或YYYY/MM/DD  
//+---------------------------------------------------  
function IsValidDate(DateStr)   
{   
    var sDate=DateStr.replace(/(^\s+|\s+$)/g,''); //去两边空格;   
    if(sDate=='') return true;   
    //如果格式满足YYYY-(/)MM-(/)DD或YYYY-(/)M-(/)DD或YYYY-(/)M-(/)D或YYYY-(/)MM-(/)D就替换为''   
    //数据库中，合法日期可以是:YYYY-MM/DD(2003-3/21),数据库会自动转换为YYYY-MM-DD格式   
    var s = sDate.replace(/[\d]{ 4,4 }[\-/]{ 1 }[\d]{ 1,2 }[\-/]{ 1 }[\d]{ 1,2 }/g,'');   
    if (s=='') //说明格式满足YYYY-MM-DD或YYYY-M-DD或YYYY-M-D或YYYY-MM-D   
    {   
        var t=new Date(sDate.replace(/\-/g,'/'));   
        var ar = sDate.split(/[-/:]/);   
        if(ar[0] != t.getYear() || ar[1] != t.getMonth()+1 || ar[2] != t.getDate())   
        {   
            //alert('错误的日期格式！格式为：YYYY-MM-DD或YYYY/MM/DD。注意闰年。');   
            return false;   
        }   
    }   
    else   
    {   
        //alert('错误的日期格式！格式为：YYYY-MM-DD或YYYY/MM/DD。注意闰年。');   
        return false;   
    }   
    return true;   
}   
  
//+---------------------------------------------------  
//| 日期时间检查  
//| 格式为：YYYY-MM-DD HH:MM:SS  
//+---------------------------------------------------  
function CheckDateTime(str)  
{   
    var reg = /^(\d+)-(\d{ 1,2 })-(\d{ 1,2 }) (\d{ 1,2 }):(\d{ 1,2 }):(\d{ 1,2 })$/;   
    var r = str.match(reg);   
    if(r==null)return false;   
    r[2]=r[2]-1;   
    var d= new Date(r[1],r[2],r[3],r[4],r[5],r[6]);   
    if(d.getFullYear()!=r[1])return false;   
    if(d.getMonth()!=r[2])return false;   
    if(d.getDate()!=r[3])return false;   
    if(d.getHours()!=r[4])return false;   
    if(d.getMinutes()!=r[5])return false;   
    if(d.getSeconds()!=r[6])return false;   
    return true;   
}   
  
//+---------------------------------------------------  
//| 把日期分割成数组  
//+---------------------------------------------------  
Date.prototype.toArray = function()  
{   
    var myDate = this;  
    var myArray = Array();  
    myArray[0] = myDate.getFullYear();  
    myArray[1] = myDate.getMonth();  
    myArray[2] = myDate.getDate();  
    myArray[3] = myDate.getHours();  
    myArray[4] = myDate.getMinutes();  
    myArray[5] = myDate.getSeconds();  
    return myArray;  
}  
  
//+---------------------------------------------------  
//| 取得日期数据信息  
//| 参数 interval 表示数据类型  
//| y 年 m月 d日 w星期 ww周 h时 n分 s秒  
//+---------------------------------------------------  
Date.prototype.DatePart = function(interval)  
{   
    var myDate = this;  
    var partStr='';  
    var Week = ['日','一','二','三','四','五','六'];  
    switch (interval)  
    {   
        case 'y' :partStr = myDate.getFullYear();break;  
        case 'm' :partStr = myDate.getMonth()+1;break;  
        case 'd' :partStr = myDate.getDate();break;  
        case 'w' :partStr = Week[myDate.getDay()];break;  
        case 'ww' :partStr = myDate.WeekNumOfYear();break;  
        case 'h' :partStr = myDate.getHours();break;  
        case 'i' :partStr = myDate.getMinutes();break;  
        case 's' :partStr = myDate.getSeconds();break;  
    }  
    return partStr;  
}  
  
//+---------------------------------------------------  
//| 取得当前日期所在月的最大天数  
//+---------------------------------------------------  
Date.prototype.MaxDayOfDate = function()  
{   
    var myDate = this;  
    var ary = myDate.toArray();  
    var date1 = (new Date(ary[0],ary[1]+1,1));  
    var date2 = date1.dateAdd(1,'m',1);  
    var result = dateDiff(date1.Format('yyyy-MM-dd'),date2.Format('yyyy-MM-dd'));  
    return result;  
}  
  

  
//+---------------------------------------------------  
//| 字符串转成日期类型   
//| 格式 MM/dd/YYYY MM-dd-YYYY YYYY/MM/dd YYYY-MM-dd  
//+---------------------------------------------------  
function StringToDate(DateStr)  
{   
  
	//var DateStr ='2012-08-12 23:13:15';
	DateStr = DateStr.replace(/-/g,"/");
	var date = new Date(DateStr );
	return date;
	
    var converted = Date.parse(DateStr);  
    var myDate = new Date(converted);  
    if (isNaN(myDate))  
    {   
        //var delimCahar = DateStr.indexOf('/')!=-1?'/':'-';  
        var arys= DateStr.split('-');  
        myDate = new Date(arys[0],--arys[1],arys[2]);  
    }  
    return myDate;  
}
/**
 * 全部替换
 */
String.prototype.replaceAll = function (str1,str2){
  var str    = this;  
  var result   = str.replace(eval("/"+str1+"/gi"),str2);
  return result;
}

/**
 * 将日期字段串转换为date对象。
 * @parame string dateStr 日期字符串。 格式求为： YYYY-MM-dd or YYYY-MM-dd H:i:s
 * @parame string fmtjs		当前显示日期格式。为空时返回当前时间
 */
 function toDate(dateStr,fmtjs){
	 if(typeof fmtjs == 'undefined' || !fmtjs ){
		 console.warn('没有传入显示格式，将返回当前时间对象');
		 return new Date();
	 }
	 // 所有格式参数
	var fmtAll=Array('yyyy','MM','dd','HH','mm','ss');
	var regall=/[\-/ .:]/;
	// 当前显示数据格式分解
	var curfmt = fmtjs.split(regall);
	// 将显示数据处理为通用格式数据
	var curvalOprate=new Array();
	var curvalOprate =dateStr.split(regall);
	// 当前用户时间数据按格式分割后数组， 年，月，日，时，分，秒
	// 按最终用户传入显示格式为准
	// ['yyyy':'2016','MM':'01','dd':'31','HH':'15','mm':'38','ss':'01']
	var ret=new Array();
	for (i=0;i<curvalOprate.length ;i++ )
	{
		ret[curfmt[i]]=curvalOprate[i];
	} 
	// 实例化当前时间，并将用户指定时间数据替换掉实例化的时间对应值。
	// 返回最后结果。
	// 当前时间，按标准格式分解
	var curDateObj = new Date();
	var curDateTimeStr = curDateObj.Format('yyyy-MM-dd HH:mm:ss');
	var curDateTimeArr = curDateTimeStr.split(regall);
	var souceTime=new Array();
	for(var i in curDateTimeArr){
		souceTime[fmtAll[i]] = curDateTimeArr[i];
	}
	for(var i in ret){
		souceTime[i]=ret[i];
	}
	var fmt= new Array();
	fmt.push(souceTime['yyyy']);// 年
	fmt.push(parseInt(souceTime['MM'])-1);// 月
	fmt.push(souceTime['dd']);// 日
	fmt.push(souceTime['HH']);// 时
	fmt.push(souceTime['mm']);// 分
	fmt.push(souceTime['ss']);// 秒
	var data =  new Date(parseInt(fmt[0]),parseInt(fmt[1]),parseInt(fmt[2]),parseInt(fmt[3]),parseInt(fmt[4]),parseInt(fmt[5]));
	return data;
	
	
	return new Date();
	
	var reg = /[\-/ .:]/;
	var fmt = dateStr.split(reg);
	var count = fmt.length;
	if( count < 6 ){
		for( var i = 0; i< 6-count;i++){
			fmt.push('01');
		}
	}
//	console.log(fmt);
	fmt[1] = parseInt(fmt[1])-1;
	var data =  new Date(parseInt(fmt[0]),parseInt(fmt[1]),parseInt(fmt[2]),parseInt(fmt[3]),parseInt(fmt[4]),parseInt(fmt[5]));
	return data;
}

//生成trace导航
function set_trace_html(child,rand){
	var $html = $("body .think_page_trace_open_parent");
	if($html.length==0){
		$html = $('<div class="think_page_trace_open_parent"><div><button class="clear_both_trace_btn"><span class="icon-trash"></span> 清除全部</button></div></div>')
		$("body").append($html);
	}
	$html.find("div:eq(0)").before(child);
	var child_list = $html.find("div[rel]");
	child_list.each(function(){
		var this_rand = $(this).attr("rel");
		if($("#think_page_trace"+this_rand).length==0){
			$(this).remove();
		}
	});
	
	$("body").append($("#think_page_trace"+rand));
	
	$("button.clear_both_trace_btn").die("click");
	$("button.clear_both_trace_btn").live("click",function(){
		$("body .think_page_trace_open_parent").remove();
		$(".think_page_trace").remove();
	});
	
	$("img.think_page_trace_close_all").die("click");
	$("img.think_page_trace_close_all").live("click",function(){
		var this_rand = $(this).attr("rel");
		$(".think_page_trace_open_parent #think_page_trace_open"+this_rand).remove();
	});
}

//取某月最后一天
function getMonthLastDay(month){
    var current=new Date();
    var currentMonth=month-1;
    var nextMonth=++currentMonth;

    var nextMonthDayOne =new Date(current.getFullYear(),nextMonth,1);

    var minusDate=1000*60*60*24;

    return new Date(nextMonthDayOne.getTime()-minusDate);
} 

//验证日期有效
function checkDate(date){
    return (new Date(date).getDate()==date.substring(date.length-2));
}

//保后计划
function baohoubind(new_tr){
	$("#MisAutoDeb_edit tbody,#MisAutoDeb_add tbody").find(new_tr).find("td:eq(3) .list_group_lay input,td:eq(2) .list_group_lay select,td:eq(1) .list_group_lay select").on("change",function(){
	    var val = parseInt($(this).parents("tr").find("td:eq(3) input").val());
	    var jhDate = $("[name='jihuayijuriqi']").val();
		var str = "";
		if($(this).parents("tr").find("td:eq(1) select").val()=="03"){
			var find_obj = $(this).parents("tbody").find("tr");
			find_obj.each(function(){
				if($(this).find("td:eq(1) select").val()=="01"){
					jhDate = $(this).find("td:eq(4) input").val();
				}
			});
        }
	    if(jhDate!=undefined&&jhDate!="" && !isNaN(val)){
	    	jhDate = jhDate.replace(/-/g,'/');
		    var today = new Date(jhDate);
		   
		if($(this).parents("tr").find("td:eq(2) select").val()=="01"){
            today.setDate(today.getDate() + val);
            var year = today.getFullYear();
            var month = today.getMonth()+1;
            var day = today.getDate();
            if(month<10){
              month = "0"+month;
            }
            if(day<10){
              day = "0"+day;
            }
            var str = year + "-" + month + "-" + day;
          }
          if($(this).parents("tr").find("td:eq(2) select").val()=="02"){
            var last_day = getMonthLastDay(val);
            var day = last_day.getDate();
            var now_date = new Date();
            var month = today.getMonth()+1;
            var year = today.getFullYear();
            if(val<10){
              val = "0"+val;
            }
            var str = today.getFullYear()+"-"+val+"-"+day;
            var this_date = new Date(str);
            if(this_date-now_date<0){
              str = (today.getFullYear()+1)+"-"+val+"-"+day;
            }

          }
		    if(!checkDate(str)){
		    	str = "";
		    }
	    }
	    $(this).parents("tr").find("td:eq(4) input,td:eq(5) input").val(str);
	});
	$("#MisAutoDeb_edit tbody,#MisAutoDeb_add tbody").find(new_tr).find("td:eq(3) .list_group_lay input,td:eq(2) .list_group_lay select,td:eq(1) .list_group_lay select").change();
}

//新内嵌表上传组件
function DTopenFile(obj){
	$this = $(obj);
	var title=$this.attr("title")||$this.text();
	var rel=$this.attr("rel")||"_blank";
	var id="#"+$this.parents("table").attr("id")+" #"+$this.attr("id");
	var url=$this.attr("rel_url");
	var options={};
	var attached = new Array();
	var rel_subid = $this.attr("rel_subid");
	var rel_tableid = $this.attr("rel_tableid");
	var rel_tablename = $this.attr("rel_tablename");
	var rel_fieldname = $this.attr("rel_fieldname");
	var rel_index = $this.attr("rel_index");
	var rel_name = $this.attr("rel_name");
	var rel_type = $this.attr("rel_type")?$this.attr("rel_type"):"";
	$this.siblings("div").each(function(i){
		var obj = {};
		var strs= new Array(); //定义一数组
		strs=$(this).find("input").val().split("###"); 
		var filename = strs[1];
		var index1=filename.lastIndexOf(".");  
		var index2=filename.length;
		obj.filename = strs[1];
		obj.filename_0 = filename.substring(0,index1);
		obj.ext = filename.substring(index1,index2);
		obj.url = strs[0];
		obj.rand = $(this).attr("rel");
		attached.push(obj);
	});
	options.width="800";
	options.height="500";
	options.mask=true;
	options.max=false;
	options.maxable=false;
	options.minable=false;
	options.fresh=false;
	options.resizable=false;
	options.drawable=false;
	options.close=eval($this.attr("close")||"");
	options.param  = {id:id,list:attached,rel_type:rel_type,rel_subid:rel_subid,rel_index:rel_index,rel_tableid:rel_tableid,rel_name:rel_name,rel_tablename:rel_tablename,rel_fieldname:rel_fieldname};
	$.pdialog.open(url,rel,title,options);
}

/**
*	字符html编码
*	@parame string s 需要被html编码的字符
*	@return string result 编码后的结果
*	@authoer nbmxkj
*	@date	2015-03-23 20:17
*/
function htmlencode(s){
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(s));
    return div.innerHTML;
}
/**
*	字符html解码
*	@parame string s 需要被html解码的字符
*	@return string result 解码后的结果
*	@authoer nbmxkj
*	@date	2015-03-23 20:17
*/
function htmldecode(s){
    var div = document.createElement('div');
    div.innerHTML = s;
    return div.innerText || div.textContent;
}
//lookup升级版width自适应 取消
//面板滚动
$(function() {
	/*var lookupTotalWidth = $("div.select_top_lay").width; //获取弹框的总宽度
	var leftTreeWidth = $("div.stl_left").width; //获取左边树的宽度
	$("div.stl_right").css("width",lookupTotalWidth - leftTreeWidth); //计算出右边列表的宽度
*/

})

/**
 * 文本域的初始化 
 */
function initTextarea(obj , value){
	var _this = $(obj);
	if("TEXTAREA" == _this[0].tagName){
		_this.html(value);
	}
	
	if( _this.hasClass('ueditor')){
		var umeditorid = _this.attr("id");
		if(umeditorid){
			UE.getEditor(umeditorid).setContent('', false);
			UE.getEditor(umeditorid).execCommand('insertHtml',value);
		}
	}
	/*
	if( _this.hasClass('ueditor')){
		var umeditorid = _this.attr("id");
		console.log(umeditorid);
		if(umeditorid){
			UE.getEditor(umeditorid).destroy();
			UE.getEditor(umeditorid);
			//ueObj.destroy();
			var editor = new UE.ui.Editor();
			editor.render(umeditorid);
			//加一个监听时间
			editor.addListener("keyup",function(type,event){
				var content = editor.getContent();
				_this.html(content);
			});
		}
	}
	*/
}

function qdschangeeventfunc(obj , curbox ){
	var box = navTab.getCurrentPanel();
	// 取二级， 取三级
	var firstLevel = curbox.find('select[clevel="2"]').find("option:selected").text();
	//san级
	var secendLevel = curbox.find('select[clevel="3"]').find("option:selected").text();
	// si级
	var thirdLevel = curbox.find('select[clevel="4"]').find("option:selected").text();
	if(firstLevel=='市辖区' || firstLevel=='县'){
		firstname = '';
	}else{
		firstname = firstLevel;
	}
	/*var firstshiname = secendLevel.replace(reg , ' ');
	var firstxianname = '';
	if(secendLevel!='' && thirdLevel!=''){
		if(thirdLevel=='市辖区'){
			firstxianname = thirdLevel;
		}else{
			firstxianname = thirdLevel.replace(reg , '');
		}
	}*/
	var  appshifadian=firstname+secendLevel+thirdLevel;
	$('input[name="suoshudiqu"]',box).val(appshifadian).change();
	
}


function tiechangeeventfunc(obj , curbox ){
	var box = navTab.getCurrentPanel();
	var reg = new RegExp('[市|区|县]','g');
	
	
	// 取二级， 取三级
	
	// 二级
	var secendLevel = curbox.find('select[clevel="2"]').find("option:selected").text();
	// 三级
	var thirdLevel = curbox.find('select[clevel="3"]').find("option:selected").text();
	var firstshiname = secendLevel.replace(reg , '-');
	var firstxianname = '';
	if(secendLevel!='' && thirdLevel!=''){
		if(thirdLevel=='市辖区'){
			firstxianname = thirdLevel;
		}else{
			firstxianname = thirdLevel.replace(reg , '');
		}
	}
	var  appshifadian=firstshiname+thirdLevel;
	$('input[name="appshifadian"]',box).val(appshifadian).change();
	
}

function mudichangeeventfunc(obj , curbox ){
	var box = navTab.getCurrentPanel();
	var reg = new RegExp('[市|区|县]','g');
	// 取二级， 取三级
	// 二级
	var secendLevel = curbox.find('select[clevel="2"]').find("option:selected").text();
	// 三级
	var thirdLevel = curbox.find('select[clevel="3"]').find("option:selected").text();
	var firstshiname = secendLevel.replace(reg , '-');
	var firstxianname = '';
	if(secendLevel!='' && thirdLevel!=''){
		if(thirdLevel=='市辖区'){
			firstxianname = thirdLevel;
		}else{
			firstxianname = thirdLevel.replace(reg , '');
		}
	}
	var  appshifadian=firstshiname+thirdLevel;
	$('input[name="appmudedi"]',box).val(appshifadian).change();
	
}

function mychangeeventfunc(i , v ){
	var box = navTab.getCurrentPanel();
	var val = $(i).val();
	var level = $(i).attr('clevel');
	var order = $('div.address_elm:last' , box);
	var obj = order.find('select[clevel="'+level+'"]');
	obj.find('option').removeAttr('selected');
	obj.val(val);
	obj.find('option[value="'+val+'"]').attr('selected' , true);
	obj.change();
	$('input[name="areainfo[zhucedizhi][address]"]',v).keyup(function(){
		$('input[name="areainfo[address][address]"]',box).val($(this).val());
		$('input[name="areainfo[address][address]"]',box).keyup();
	});
	$('input[name="areainfo[zhucedizhi][detail]"]',v).keyup(function(){
		$('input[name="areainfo[address][detail]"]',box).val($(this).val());
		$('input[name="areainfo[address][detail]"]',box).keyup();
	});
}
function cbjchangeeventfunc(i , v){
	var box = navTab.getCurrentPanel();
	var val = $(i).val();
	var level = $(i).attr('clevel');
	var order = $('div.address_elm:last' , box);
	var obj = order.find('select[clevel="'+level+'"]');
	obj.find('option').removeAttr('selected');
	obj.val(val);
	obj.find('option[value="'+val+'"]').attr('selected' , true);
	obj.change();
	$('input[name="areainfo[areainfo33][address]"]',v).keyup(function(){
		$('input[name="areainfo[areainfo34][address]"]',box).val($(this).val());
		$('input[name="areainfo[areainfo34][address]"]',box).keyup();
	});
	$('input[name="areainfo[areainfo33][detail]"]',v).keyup(function(){
		$('input[name="areainfo[areainfo34][detail]"]',box).val($(this).val());
		$('input[name="areainfo[areainfo34][detail]"]',box).keyup();
	});
}
function openMap(obj){
	//获取地址
	var address = $("#address_detail_address").val();
	//获取横坐标
	var xmap = $("#address_detail_coordinatex").val();
	//获取纵坐标
	var ymap = $("#address_detail_coordinatey").val();
	$this = $(obj);
	var title=$this.attr("title")||$this.text();
	var rel=$this.attr("rel")||"_blank";
	var options={};
	options.width =  950;
	options.height = 530;
	options.mask = true;
	options.resizable = true;
	options.maxable = true;
	options.minable = false;
	options.param={xmap:xmap,ymap:ymap,address:address};
	var url=unescape(TP_APP+'/Common/lookupgetMapCoordinate');
	$.pdialog.open(url,rel,title,options);
}
/**
 * 将每个动态隐藏的操作项，移除eror错误类。
 * 将必填类移到属性上并从class中移除
 * @param obj
 */
function setReq(obj){
  $.each(obj , function(){
     $(this).removeClass('error');
    if($(this).hasClass('required')){
      $(this).removeClass('required');
      $(this).attr('req','required');
    }
  }); 
}
/**
 * 每个动态隐藏项将必填属性转换为class
 * @param obj
 */
function setClsReq(obj){
  $.each(obj , function(){
    if($(this).attr('req')){
      $(this).addClass($(this).attr('req'));
    }
  }); 
}