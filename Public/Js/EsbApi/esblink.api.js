//跨越支持设置
jQuery.support.cors = true;
//远程域
//var MONITOR_URL_PATH = "http://192.168.191.4:8080/server/services/";
//var MONITOR_URL_PATH = "http://192.168.1.36:8080/server/services/";
//var MONITOR_URL_PATH = "http://tmlsoft.kmras.com:8080/server/services/";
//var MONITOR_URL_PATH = "http://tmlsoft.kmras.com:8080/smartESBProject/services/";
//var MONITOR_URL_PATH = "http://192.168.0.237:8088/smartESBProject/services/";
//本地域
//var MONITOR_URL_PATH = "http://127.0.0.1:8080/server/services/";
//var MONITOR_URL_PATH = "http://localhost:8080/server/services/";
var MONITOR_URL_PATH = "http://192.168.0.238:8088/smartESBProject/services/";
//var MONITOR_URL_PATH = "http://192.168.10.25:8080/server/services/";

var SUCCESS = "success";

		
function JSONP(url,data,call)
{
	if(url.indexOf(MONITOR_URL_PATH)<0)
	{
		
	    url =  MONITOR_URL_PATH + url ;
	    //alert(url);
	} 
	$.ajax({
		url :	url, 
		type:	"get",
		jsonp:'jsonpcallback',
		data:	data,
		//跨域必须用jsonp   
		dataType : "jsonp",
	    cache: 	false,
	    error: function(XMLHttpRequest, textStatus, errorThrown)
	    {
           alert("请求状态"+XMLHttpRequest.status+",发送状态："+XMLHttpRequest.readyState+",返回状态"+textStatus);
       },
	   success: function(obj)
	   {	
    	 call(obj);
	   }
	});
}

//执行Post提交任务
function getJSONP(url,table,opration,data,where,call)
{
	if(data) {
		data = JSON.stringify(data);
    } 
	var params =  {'table' :table,'opration':opration ,'data':data,'where':where};
	
	//call post function
	JSONP(url,params,call);
	
}

//post提交
function doPost(url,data,call)
{
	$.ajax( {  
			url : MONITOR_URL_PATH+url,  
			type : 'POST',  
			data : data,  
			dataType : 'json',  
			contentType:'application/json',  
			async : true, //异步 
			success : function(rs) {  
				call(rs);
			},  
			error : function(err) {  
				alert(JSON.stringify(err));  
			} 
	
		});  
}

//执行get提交
function doGet(url,call)
{
	$.ajax( {  
			url : MONITOR_URL_PATH+url,  
			type : 'get',  
			data : null,  
			dataType : 'json',  
			contentType:'application/json',  
			async : true, //异步 
			success : function(rs) {  
				call(rs);
			},  
			error : function(err) {  
				alert(JSON.stringify(err));  
			} 
	
		});  
}

//执行Post提交任务
function getData(url,table,opration,data,where,call)
{
	if(data) {
		data = JSON.stringify(data);
    } 
	var params =  {'table' :table,'opration':opration ,'data':data,'where':where};
	
	//call post function
	doPost(url,params,call);
	
}

//form 转化为json
function formToJson(formObj){
    var o = {};
    var a = formObj.serializeArray();
    $.each(a, function() {

        if(this.value){
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [ o[this.name] ];
                }
                    o[this.name].push(this.value || null);
            } else {
                if($("[name='"+this.name+"']:checkbox",formObj).length){
                    o[this.name]=[this.value];
                }else{ 
                    o[this.name] = this.value || null;
                }
            }
        }		
    });
	//alert(o);
    return o;		
};


//取参数
$.extend(
{  
   getUrlVars: function() 
   {    
       var vars = [], hash;    
	   var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');    
	   for(var i = 0; i < hashes.length; i++)    
	   {     
	       hash = hashes[i].split('=');      
		   vars.push(hash[0]);      
		   vars[hash[0]] = hash[1];    
	   }    
	   return vars;  
   },  
   getUrlVar: function(name)
   {    
       return $.getUrlVars()[name];  
   }
});


//提供给终端访问的接口程序
var REST = 
{
   //调用数据库操作的服务接口	  
   dbservice : function (servernum , params ,where, call) 
   {
     var selectwhere = " service_num = "+servernum;	
	 
	 getJSONP('d2d/exe', 'esb_services','s', {0:'*'},selectwhere,function(serviceInfo)
	 {
	   formData =serviceInfo.rows[0] ;
	   //存在服务
	   if(formData)
	   {
	       var url = formData.service_url;
		   
		   var prs = formData.params;
		   prs = prs.replace('"{',"{").replace('}"',"}");
		    
		   var params_json =  eval('(' +prs+ ')'); 
	       params_json.data = params;
		   params_json.where = where;
		   JSONP(url,params_json,function(rs)
		   {
			  call(rs); 
		   });
	   }else {
		   alert('未发现服务！');   
	   }
    });
	 
	 
	 
}

}