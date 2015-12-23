//	var time = new timerRecord("ws初始化用时");
//	time.begin(); 
//	var wsServer = 'ws://61.186.153.133:8383/';
//	var websocket = new WebSocket(wsServer); 
//	websocket.onopen = function (evt) { 
//	console.log("Connected to WebSocket server.");
//	//websocket.send('xxx');
//	};  
//	websocket.onclose = function (evt) { 
//		console.log("Disconnected"); 
//	};  
//	websocket.onerror = function (evt, e) {
//		console.log('Error occured: ' + evt.data);
//	};
//	//$jwsdata=JSON.parse({$wsdata}); 
//	setTimeout(function(){
//	 //websocket.send(wsdata);
//	},30000);
//	  
//	 websocket.onmessage = function(e){ 
//	 //接收到html 输出至弹框
//	 if (e.data) {   
//			if( $("#alertMsgBoxTip").length >0 ){
//				var div = $("#alertMsgBoxTip").find(".msg");
//				div.empty().html(e.data).initUI();
//			}else{
//				datalist = 0;
//				alertMsgTip.info(e.data, {
//						okCall: function(){
//							$.ajax({
//								type: "POST",
//								url: TP_APP + "/Index/setDbhaSmsgType/nochangelogintime/1",
//								data:{datalist:datalist},
//								async:false,
//								success: function (succ){
//									//将取得时间数据放入数据库
//									var nexttime=$("#nexttime").val();
//									$.ajax({
//										type: "POST",
//										url: TP_APP + "/Index/lookupSetTime/nexttime/"+nexttime,
//										data:"",
//										dataType: "json",
//										async:false,
//										global: false,
//										success: function (data){
//											$.cookie('test', '123',{expires: 7, path: '/'});
//										},
//									});
//									
//									if (succ == '0') {
//										return false;
//									}
//								},
//								global: false
//							}); 
//						}
//					}); 
//			}
//		}
//	 };  
//  time.end(); 