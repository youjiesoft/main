/**
 * @Title: Config
 * @Package package_name
 * @Description: todo(动态表单_组件配置文件-生成添加页面专用JS)
 * @author 汤文志
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015-07-24 16:29:15
 * @version V1.0
*/
$(function(){
			var box = navTab.getCurrentPanel();

	$("[name='zhuanshoufanwei']" , box).on('change' ,function(){
		MisAutoMbw_zhuanshoufanwei_change(this);
	});
	$("[name='zhuanshoufanwei']" , box).change()		
});		

function MisAutoMbw_zhuanshoufanwei_change(obj){  var box = navTab.getCurrentPanel();
	var val=$(obj).val();
	if(val=='全部'){
		var hidObj = $(".field_datatable6", box);
		if(typeof(hidObj) != 'undefined'){
			hidObj.hide();
			hidObj.find(':input').attr('disabled',true);
		}
		if(typeof(showObj) != 'undefined'){
			showObj.show();
			showObj.find(':input').attr('disabled',false);
		}
	}
	if(val=='部分'){
		if(typeof(hidObj) != 'undefined'){
			hidObj.hide();
			hidObj.find(':input').attr('disabled',true);
		}
		var showObj =$(".field_datatable6", box);
		if(typeof(showObj) != 'undefined'){
			showObj.show();
			showObj.find(':input').attr('disabled',false);
		}
	}
};