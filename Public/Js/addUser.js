function changephone(obj,$id){
	var $box=navTab.getCurrentPanel();
	var phone=$(obj).val();
	if(phone){
		if($id=="useremail"){
			$box.find("input[name='"+$id+"']").val(phone);
			$box.find("input[name='"+$id+"']").removeClass("error");
			$box.find("input[name='"+$id+"']").siblings(".error").remove();
			
		}else{
			$box.find("#"+$id).text(phone);
		}
	}
}
function changeUser(obj,$id){
	var $box=navTab.getCurrentPanel();
	var isuser=$(obj).attr("checked");
	if(isuser){
		$box.find("input[name='accountenglish']").addClass("required");
		$box.find("input[name='useremail']").addClass("required");
		$box.find("#"+$id).show();
	}else{
		$box.find("input[name='accountenglish']").removeClass("required error");
		$box.find("input[name='useremail']").removeClass("required error");
		$box.find("#"+$id).hide();
	}
}
function checkUser(obj,$name){
	var $box=navTab.getCurrentPanel();
	$box.find("#"+$name).show();
	var val=$(obj).val();
	if($(obj).attr("name") == "name"){
		$box.find("input[name='zhname']").val(val);
		$box.find("input[name='zhname']").removeClass("error");
	}
	if(!val || $(obj).hasClass("error")){
		 $box.find("input[name='"+$name+"']").addClass("error");
		 $box.find("#"+$name+"span").show();
		$box.find("#"+$name).attr("class","icon icon-remove tml-c-red");
		$box.find("#"+$name).text("请正确输入用户名");
		return false;
	}
	$.ajax({
		type : 'POST',
		url : TP_APP+"/MisHrBasicEmployee/lookupcheckuser",
		data : {
			val : val,
			name : $name,
		},
		cache : false,
		global : false,
		success : function(msg) {
		 if(msg !=0 ){
			 if($name=="zhname"){
				 $box.find("input[name='"+$name+"']").val(val+Number(parseInt(msg)+1));
					 $box.find("#"+$name).attr("class","icon icon-ok tml-c-green");
					 $box.find("#"+$name+"span").hide();
					$box.find("#"+$name).text("用户名可用");
			 }else{
				 $box.find("input[name='"+$name+"']").addClass("error");
				 $box.find("#"+$name+"span").show();
				$box.find("#"+$name).attr("class","icon icon-remove tml-c-red");
				$box.find("#"+$name).text("用户名已被注册！");
			 }
		 }else{
			 $box.find("#"+$name).attr("class","icon icon-ok tml-c-green");
			 $box.find("#"+$name+"span").hide();
			$box.find("#"+$name).text("用户名可用");
		 }
		}
	});
}
function hidediv(obj){
	 $box.find("#"+obj).hide();
}
function changeemail(obj){
	var $box=navTab.getCurrentPanel();
	$ischecked=$(obj).attr("checked");
	if($ischecked){
		$box.find("input[name='useremail']").removeClass("required");
		$box.find("input[name='useremail']").removeClass("error");
		$box.find("input[name='useremail']").siblings(".error").remove();
		$box.find("input[name='useremail']").removeAttr('onclick');
	}else{
		$box.find("input[name='useremail']").addClass("required");
		$box.find("input[name='useremail']").attr('onclick','changeemail(this)');
	}
}