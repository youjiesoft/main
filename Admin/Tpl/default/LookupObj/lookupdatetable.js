/**
 * @Title: Config
 * @Package package_name
 * @Description: todo(动态表单_组件配置文件-生成添加页面专用JS)
 * @author 管理员
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-12-30 16:58:25
 * @version V1.0
*/

function openDatetable(obj){
	$this = $(obj);
	//获取需要的参数信息
	//1、获取模型名称
	var formid =$("input[name='formid']").val();
	//获取默认值
	var listarr = $this.attr("listarr");
	
	var title="配置内嵌表格";
	var rel=$this.attr("rel")||"_blank";
	var options={};
	options.width="1200";
	options.height="580";
	options.mask=true;
	options.max=eval($this.attr("max")||"false");
	options.maxable=eval($this.attr("maxable")||"true");
	options.minable=eval($this.attr("minable")||"true");
	options.fresh=eval($this.attr("fresh")||"true");
	options.resizable=eval($this.attr("resizable")||"true");
	options.drawable=eval($this.attr("drawable")||"true");
	options.close=eval($this.attr("close")||"");
	options.param  = {formid:formid};
	var url = TP_APP+"/LookupObj/lookupdatetable";
	DWZ.debug(url);
	$.pdialog.open(url,rel,title,options);
}
/*function clearAllinforpresult(obj){
	var $box=navTab.getCurrentPanel();
	$box.find("div.adddt").html("");
	$box.find("div.adddt").attr("proid","");
	$box.find("div.adddt").attr("fieldback","");
}*/
