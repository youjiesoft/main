    <?php 	
	//$bo="[{'0':'100005','1':'2015-07-31 17:00','2':'BS00','3':'批准人名','4':'周庆红','5':'2015-07-31 17:00','6':'2015-07-31 17:00','7':'100005','8':'请假原因','9':'备注说明','10':'2015-07-31 17:00','11':'100005','12':'周庆红','13':'2015-07-31 17:00','14':1}]";
    //<script src="__PUBLIC__/Js/EsbApi/jquery.min.js"  type="text/javascript"></script>
	//<script src="__PUBLIC__/Js/EsbApi/esblink.api.js" type="text/javascript"></script> 
	class esblink{
		function main($url,$dbsource,$proname,$bo){
		header('Content-Type:text/html;Charset=utf-8');
		//$bo="[".json_encode((object)icon_to_utf8($bo))."]";curl里解析需要，因为中文会为null
		$bo="[".json_encode((object)$bo)."]";
		$serverurl="http://192.168.0.238:8088/smartESBProject/services/dbService/procedure/4";
		$serverjob=__APP__."/MisSystemEsblog/main";
	$echoJs = <<<EOF
	<script>
		    esblink();
			function esblink()
			{	
			var serverURL = "http://192.168.0.238:8088/smartESBProject/services/dbService/procedure/4";
			var serverjob="{$serverjob}"
			var dbsource = "{$dbsource}"; 
			var proname = "{$proname}"; 
			var BO= '{$bo}';		
				if(proname=="" || dbsource=="" || serverURL =="") {
				   alert("相关数据必须全部填写！");
				   return;
				}else {
					  var url = serverURL;
					  data = {"dbsource" : dbsource,   "procedureName" : proname,  "BO" : BO };
					  JSONP(url,data,function(rs){
					       
						   //document.write(JSON.stringify(rs));
						   \$.ajax({
								url:serverjob, //填入请求路径
								type:'POST',
								dataType:'json',
								data:{'returndata':JSON.stringify(rs),'serverurl':serverURL,'serverjob':serverjob,'proname':proname,'dbsource':dbsource,'param':BO},
								success:function(msg){
					                 console.log(msg);
								},
								error:function(msg){
					                console.log(msg);
								}
						   });
						   //alert(JSON.stringify(rs));
					  });
				}
			}
            //alert(test());		
	</script>
EOF;
		echo $echoJs;
		}
	}
	?>