function clearAllinforpresult($order){
	var ht = '';
	//移除添加按钮中list
	if($order == 'processcondition_batch'){
		//这一块是在流程管理中使用。切勿修改  liminggang
		ht = '<input type="text" name="processcondition_batch" readonly="readonly" class="required textInput node_name_input" placeholder="必填，添加分子条件"/>';
	}
	$("."+$order).html(ht);
	var atthref=$(".p_addresult"+$order).attr('atthref');
	$(".p_addresult"+$order).attr('href',atthref);
	$(".p_addresult"+$order).attr("listarr","");
}
function clearAllsqlpresult($order){ 
	var atthref=$(".p_addresultsql"+$order).attr('listArr',''); 
	$(".sqlcondition_value"+$order).val("");
}
function openRule(obj){
	$this = $(obj);
	//获取需要的参数信息
	//1、获取模型名称
	var modelname =$this.attr("modelname");
	//扩展 本来是做的模型规则，扩展为模型本表、模型单个内嵌表、视图规则  --xyz--2015-5-26
	var inlinetable = $this.attr("inlinetable");
	//2、获取class的唯一名称标志，方便后面用JS赋值
	var order =$this.attr("order");
	//获取默认值
	var listarr = $this.attr("listarr");
	var multitype = $this.attr("multitype");
	var akey = $this.attr("akey");
	var title=$this.attr("title")||$this.text();
	var rel=$this.attr("rel")||"_blank";
	var options={};
	options.width="800";
	options.height="580";
	options.mask=true;
	options.max=eval($this.attr("max")||"false");
	options.maxable=eval($this.attr("maxable")||"true");
	options.minable=eval($this.attr("minable")||"true");
	options.fresh=eval($this.attr("fresh")||"true");
	options.resizable=eval($this.attr("resizable")||"true");
	options.drawable=eval($this.attr("drawable")||"true");
	options.close=eval($this.attr("close")||"");
	options.param  = {nodename:modelname,order:order,listarr:listarr,multitype:multitype,inlinetable:inlinetable,akey:akey};
	var url = TP_APP+"/"+modelname+"/lookupaddresult";
	DWZ.debug(url);
	$.pdialog.open(url,rel,title,options);
}
//sql设计器打开页面
function openSqlRule(obj){
	$this = $(obj);
	//获取需要的参数信息
	//1、获取模型名称
	var modelname =$this.attr("modelname"); 
	//获取默认值
	var tableArr = $this.attr("tableArr"); 
	var order=$this.attr("order");
	var listArr=$this.attr("listArr");
	var inputname=$this.attr("inputname"); 
	var title=$this.attr("title")||$this.text();
	var rel=$this.attr("rel")||"_blank";
	var options={};
	options.width="800";
	options.height="580";
	options.mask=true;
	options.max=eval($this.attr("max")||"false");
	options.maxable=eval($this.attr("maxable")||"true");
	options.minable=eval($this.attr("minable")||"true");
	options.fresh=eval($this.attr("fresh")||"true");
	options.resizable=eval($this.attr("resizable")||"true");
	options.drawable=eval($this.attr("drawable")||"true");
	options.close=eval($this.attr("close")||"");
	options.param  = {nodename:modelname,order:order,tableArr:tableArr,listArr:listArr,inputname:inputname};
	var url = TP_APP+"/"+modelname+"/lookupaddsqlresult";
	DWZ.debug(url);
	$.pdialog.open(url,rel,title,options);
}
/**
 * 表单流程模拟提交post数据获取流程JS,在showFormFlowWidget中使用
 * @param obj
 */
function showFormFlow(obj){
	var formObj = $(obj).closest('form');
	var curUrl = formObj.attr('action');
	var url = $(".showFormFlow a.js-actionUrl",formObj).attr("rel");
	formObj.attr('action' , url);
	formObj.ajaxSubmit(function(data) {
		if ('string' == typeof(data)) {
			var html = $(data).initUI();
			$(".showFormFlow",formObj).html(html);
		}
		formObj.attr('action' , curUrl);
	});
}