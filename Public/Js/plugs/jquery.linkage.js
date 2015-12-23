/**
 * 下拉框多级联动。
 * @author 咏殇影@nbmxkj 20140529
 *	使用方法：
 *	var conf	=	new Object();
 *  conf.data	=	'JSON格式的数据';
 *	conf.tag	=	'JSON串中用每级数据的键名，如：key or 0,1具体以实际使用为准备。数据间以逗号分隔';
 *	conf.tex	=	'显示字段';
 *	conf.val	=	'值字段';
 *	conf.pid	=	'每一组数据的父ID，首项不需要，数据间以逗号分隔';
 *	conf.defaultVal	=	'默认显示数据ID，数据间以逗号分隔';
 *	$.linkage(conf);
 *	示例：
 *	$(function(){
 *		$.ajax({
 *			url:'/dwz_test/Data/index.php/Json/index',
 *			dataType:'json',
 *			success:function(msg){
 *				var conf	=	new Object();
 *				conf.ids	=	'.sel1,.sel2,.sel3,.sel4';//'#sel1,#sel2,#sel3,#sel4';
 *				conf.tag	=	'c,d,j,f';
 *				conf.pid	=	'cid,did,jid';
 *				conf.data	=	msg;
 *				conf.tex	=	'name';
 *				conf.val	=	'id';
 *				//conf.defaultVal='3,4,10';
 *				$.linkage(conf);
 *			}
 *		});
});
 */
(function($) {
    $.linkage = function(_settings) {
        var UNF = 'undefined',EMPTY='';
    	var c =  settings = jQuery.extend({
            ids: '',
            data: '',// 数据
            tag: '',//数据标识
            tex:'',//显示标识
            val:'',//值标识
            pid:'',//其父级标识字段
            defaultVal:''
        }, _settings);
    	if(c.tag == '' || c.ids == '' || c.pid=='' || c.tex=='' || c.val==''){
        	console.log('\u914d\u7f6e\u5931\u8d25\uff0c\u8bf7\u53c2\u7167\u8bf4\u660e\u914d\u7f6e\u3002');
        	return;
    	}
    	//切割 组合基础数据 
    	c.tag = c.tag.split(',');
    	c.ids = c.ids.split(',');
    	c.pid = c.pid.split(',');
    	if(typeof(c.defaultVal)!=UNF){
    		c.defaultVal = c.defaultVal.split(',')
    	}
    	//将现有数据源以个选项个数对应切割
    	var data= new Array();
       	if(typeof(settings.data)=='object'){
           	for(var i=0 ; i< c.ids.length ; i++){
				data.push(c.data[c.tag[i]]);
           	}
       	}else{
       		console.log('\u5f53\u524d\u6570\u636e\u6e90\u7c7b\u578b\u4e3a：'+typeof(c.data)+'\u3002\u7cfb\u7edf\u4e0d\u63a5\u53d7\u6b64\u7c7b\u6570\u636e\uff0c\u5f53\u524d\u4ec5\u63a5\u53d7JSON');
           	return;
       	}
       	
       	
       	
       	var curVal ='';
       	for(var i=0 ; i<c.ids.length ;i++){
           		//有默认值。
           		// 当前项的选项数据源为默认对应下标前一个的父级ID值。
           		//特殊处理首项，首项不存前一项。
           		if(c.defaultVal!=EMPTY){
	           		pids = c.defaultVal;
	  				creatItem(getFitData(i,pids[i-1]),i,c.defaultVal[i]);
           		}else{
               		var temp = getFitData(i,curVal);
               		if(typeof(temp[0])!=UNF){
                   		curVal = temp[0][c.val];
               		}
           			creatItem(temp , i , curVal);
           			//console.log('默认加载项时序号:'+i +'  父ID值：'+curVal);
           			
           		}
       	}
       	function getSub(val , data){
           	for(var i in data){
               	if(data[i] == val){
                   	return i;
               	}
           	}
       	}

        /**
        * 生成指定项的值
        * @parame data 数据
        * @parame i 生成项的ID集合下标
        * @parame selItemVal 默认选中项值
        */
       	function creatItem(data, i , selItemVal){
       		$(c.ids[i]).html('');
       		var op;
       		//console.log('开始生成新option'+i+'.当前数据源为：'+typeof(data[0]));
       		if(typeof(data[0])=='undefined'){
       			op = $("<option></option>")
               	op.text('\u6ca1\u6709\u6570\u636e');
       			op.val('0');
            	if(typeof(selItemVal)!='undefined' && selItemVal == v[c.val])
                	op.attr('selected',true);
               	$(c.ids[i]).append(op);
       		}else{
	           	$.each(data , function(k,v){
	               	op = $("<option></option>")
	               	op.text(v[c.tex]);
	               	op.val(v[c.val]);
	            	if(typeof(selItemVal)!=UNF && selItemVal == v[c.val])
	                	op.attr('selected',true);
	               	$(c.ids[i]).append(op);
	            });
	           	$(c.ids[i]).append(op).unbind('change');
	           	$(c.ids[i]).on('change',function(){
	           		changeItem($(this));
	            });
       		}
       	}
       	
       	/**
       	* 获取符合条件的数据集 
       	* @parame index 	项下标
       	* @parame curVal	父级Id值
       	**/
       	function getFitData(index,pid){
           	// 是不是首项，判断PID 值是否存在
           	//console.log('序号:'+index +'  父ID值：'+pid);
       		index = parseInt(index,10);
           	var temp = data[(index)];
           	if(typeof(temp) == UNF)
               	return;
           	var tdata = new Array();

           	if(pid==EMPTY){
           		tdata = temp;
           	}else{
           		pid = parseInt(pid,10);
	           	$.each(temp , function(k,v){
	           		if(pid ==v[c.pid[index-1]]){
	           			tdata.push(v);
	           		}
	            });
           	}
           	return tdata;
       	}
       	function changeItem(evt){
       		if(c.ids[0].indexOf('#')>-1){
       			var index = parseInt('#'+getSub($(evt).attr('id'),c.ids),10);
       		}else{
       			var index = parseInt(getSub('.'+$(evt).prop('class'),c.ids),10);
       		}
           //在重新选择时要将当项以后的项都触发一次值改变事件？
           var curVal = $(evt).val();
       		//console.log('事件区域:'+index +'  父ID值：'+curVal);
           	for(var i = index ; i<c.ids.length;i++){
           		if(i+1 < c.ids.length){
           			//console.log('事件区域循环:'+i);
               		//获取下一项
           			var t = getFitData(i+1 , curVal);
           			curVal = typeof(t[0])==UNF?99999999:t[0][c.val];
               		creatItem(t,i+1);
               	}
           	}
       	}
    };
})(jQuery);