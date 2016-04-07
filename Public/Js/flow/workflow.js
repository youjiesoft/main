var pr = {
    fnInit : function (obj, data, conditions,objId){
    	jsPlumb.reset();
    	//避免重复应用都需要修改的ID值。obj参数可以忽略。
    	var $curpanelobj=navTab.getCurrentPanel();
    	$curpanelobj.find(".processgraph .prcslist").attr("id",objId);
    	obj = objId;
    	//设定一个随机数
    	//var objId = "flow"+Math.random()*999;
    	var nodeClick = function(){};
    	var labelClass = 'aLabel';
    	var dropStop = function(event, ui){//这里做异步保存节点坐标信息
			//alert('拖动的节点ID为:'+event.target.id + '，left为:'+ui.position.left+ '，top为:'+ui.position.top)
			var value = ui.helper.find("input").val()?$.parseJSON(ui.helper.find("input").val()):"";
			value.setleft = ui.position.left;
			value.settop = ui.position.top;
			var json = JSON.stringify(value);
			ui.helper.find("input").val(json);
		};
		var labelFunction = function(e){
			var label = e.getElement();//获得label元素
			var lineIndex = $(label).index('.aLabel');//获取线的索引
			var sourceId = e.component.sourceId.slice(6);
			var targetId = e.component.targetId.slice(6);
			if(isNullorEmpty(conditions[sourceId+'-'+targetId])){
				$('#window'+ sourceId ).addClass('condition');
				return conditions[sourceId+'-'+targetId];
			}else{
				label.style.display = 'none';
				return '';
			}
		};
    	
        $('#'+obj).html('');
        var nLastPrcsId = 0;
        if( data.total > 0 ){
            jsPlumb.importDefaults({
                DragOptions : { cursor: 'pointer'},
                EndpointStyle : { fillStyle:'#225588' },
                Endpoint : [ "Dot", {radius:1} ],
                Connector:["Bezier", { curviness:63 } ],
                ConnectionOverlays : [
                    [ "Arrow", { location:1 } ],
                    [ "Label", {
                        location:0.5,
                        id:"label",
						label: labelFunction,
                        cssClass: labelClass
                    }]
                ],
                maxConnections:3,
                isTarget:true,
                PaintStyle:pr.connectorPaintStyle,
                Anchor : 'Continuous',
                ConnectorZIndex:5,
                HoverPaintStyle:pr.connectorHoverStyle
            });
            if( $.browser.msie && $.browser.version < '9.0' ){ //ie9以下，用VML画图
                jsPlumb.setRenderMode(jsPlumb.VML);
            } else { //其他浏览器用SVG
                jsPlumb.setRenderMode(jsPlumb.SVG);
            }
            //第一次循环，生成页面元素div
            var left = 0;
            var top = 0;
            $.each(data.list, function(i,row) {
            	if(row.setleft == 0){
            		row.setleft = (row.level-1)*200+60;
            	}
            	var settop_int = 100;
        		if(row.settop == 0){
        			for(var x=i-1;x>=1;x--){
        				if(data.list[x].level==row.level){
        					settop_int = data.list[x].settop+80;
        					break;
        				}
        			}
        			row.settop = settop_int;
        		}
                top = row.settop + 'px';
                left = row.setleft + 'px';
                var nodeDiv = document.createElement('div');
                var nodeId = "window" + row.prcsid + objId;
				var $nodeDiv = $(nodeDiv);
                if(row.flag==''){ //未接收
                    row.flag=1;
                }
                if(row.processcondition){
					$nodeDiv.attr('condition', row.processcondition);
				}
                var data_shape = "Rectangle";
                switch(row.choose){
                	case "1",1:
                		data_shape = "Diamond";
                }
                
                $nodeDiv
                .attr("id",nodeId)
                .attr("row",row)
                .css({"left":left,"top":top,"cursor":"move"})
                .attr("prcsto",row.processto)
                .attr("iscomplete",row.iscomplete)
                .attr("data-shape",data_shape)
                .attr("steps",row.prcsid)
                .addClass("wfwindow window"+row.flag)
                .addClass("window_choose_"+row.choose)
                .html('<span class="ep badge badge-inverse window_choose_prcsid'+row.choose+'">'
                + row.prcsid + '</span>&nbsp;<span class="window_choose_processname'+row.choose+'">' + row.processname +'</span>');
                if(row.runchild!='0'){
                    $(nodeDiv).attr('runchild',row.prcsid);
                    //子流程绑定双击事件
                    if(data.show === 0){
//                    	$(nodeDiv).bind('dblclick',function(){
//                            pr.fnViewChildflow(row);
//                      });
                    	$(nodeDiv).contextMenu("flowCharts", {
                    		bindings: {
                    			replaceFlows:function(t){
                    				if(row.prcsid == 0) return false;
                    				pr.fnReplaceFlows(row);
                    			},
                    			addChildChoose:function(t){
                    				pr.fnAddChildChoose(row);
                    			},
                    			addChildParallel:function(t){
                    				pr.fnAddChildParallel(row);
                    			},
                    			addChildFlows:function(t){
                    				pr.fnAddChildFlows(row);
                    			},
                    			addChildSonFlows:function (t){
                    				pr.fnAddChildSonFlows(row);
                    			},
                    			editChildFlows:function(t){
                    				pr.fnEditChildFlows(row);
                    			},
                    			linkFlows:function(t){
                    				pr.fnLinkFlows(row);
                    			},
                    			deleteFlows:function(t){
                    				if(row.prcsid == 0) return false;
                    				alertMsg.confirm("您将删除当前流程审核人及和它唯一关联的流程审核人?", {
                    					okCall : function(){
                    						pr.fnDeleteFlows(row);
                    					}
                    				});
                    				return false;
                    			},
                    			addNoOperationField:function(t){
                    				pr.fnAddNoOperationField(row);
                    			}
                    		},
                    		ctrSub:function(t,m){
                    			var mAdd=m.find("[rel='addChildFlows']");//新增下级节点
                    			var mPar=m.find("[rel='addChildParallel']");//新增并行节点
                    			var mChoose=m.find("[rel='addChildChoose']");//新增判定节点
                    			var mSon=m.find("[rel='addChildSonFlows']");//新增子流程节点
                    			var mEdit=m.find("[rel='editChildFlows']");//修改当前节点
                    			var mNoOpF = m.find("rel='addNoOperationField'");//不可操作字段控制
                    			var mDel=m.find("[rel='deleteFlows']");//删除审核节点
                    			var mLink=m.find("[rel='linkFlows']");//连接审核节点
                    			
                    			//下面2个类型暂未使用
                    			var mRep=m.find("[rel='replaceFlows']");
                    			var mReb=m.find("[rel='linkRebackFlows']");
                    			
                    			if(row.choose == 0){ //开始节点
                    				mDel.addClass("disabled");
                    				mDel.unbind("click");
                    			}
                    			if(row.choose == 1){ 
                    				//判定节点   判定节点下，不能在增加判定节点
                    				mChoose.addClass("disabled");
                    				mChoose.unbind("click");
                    				//只有判定节点才不存在表单字段控制
                    				mNoOpF.addClass("disabled");
                    				mNoOpF.unbind("click");
                    				//判定节点下不能添加并行节点
                    				mPar.addClass("disabled");
                    				mPar.unbind("click");
                    			}
                    			if(row.choose != 2 & row.choose != 3 & row.choose != 0){ 
                    				//非审批节点或者子流程节点 不允许进行节点修改
                    				mEdit.addClass("disabled");
                    				mEdit.unbind("click");
                    			}
                    			if(row.choose == 4){
                    				mPar.addClass("disabled");
                    				mPar.unbind("click");
                    				//不允许添加判定节点
                    				mChoose.addClass("disabled");
                    				mChoose.unbind("click");
                    				//并行节点下。不能添加子流程节点
                    				mSon.addClass("disabled");
                    				mSon.unbind("click");
                    			}
                    			if(isNullorEmpty(row.processto)){ //存在下级节点
                    				//不允许添加判定节点
                    				mChoose.addClass("disabled");
                    				mChoose.unbind("click");
                    				mPar.addClass("disabled");
                    				mPar.unbind("click");
                    				//不允许连接审核节点
                    				mLink.addClass("disabled");
                    				mLink.unbind("click");
                    				//if(row.choose!=1){ //存在下级节点且当前不是判定节点
                    					//mAdd.addClass("disabled");
                        				//mAdd.unbind("click");
                        				//mSon.addClass("disabled");
                        				//mSon.unbind("click");
                    				//}
                    			}
              			    }
                    	});
                    }
                }
				//点击绑定
				$nodeDiv.find('span').bind('click', nodeClick);
				var json = JSON.stringify(row);
				var input = '<input type="hidden" name="workflowVal[]" value=\''+json+'\'>';
				$nodeDiv.append(input);
                $("#"+obj).append(nodeDiv);
                //索引变量
                nLastPrcsId = row.prcsid;
            });
            //使之可拖动
            jsPlumb.draggable(jsPlumb.getSelector('#'+obj + " .wfwindow"), {
				 stop: dropStop
			});
            
            //连接关联的步骤
            $("#" + obj + " .wfwindow").each(function(i){
            	var $this_obj = $(this);
                var id = $(this).attr('steps');
                var nodeId = "window" + id + objId;
                if(id>1){
                    prePrcsId = id - 1;
                } else {
                    prePrcsId = 1 ;
                }
                
                if(data.type == 1){
                    var prcsto = $(this).attr('prcsto');
                    if(prcsto){
                    	var toArr = prcsto.split(",");
                        $.each(toArr,function(j,n){
                            if(n!='' && n!=0){
                            	var line = jsPlumb.connect({
                                	source:nodeId, 
                                	target:"window" + n + objId,
                            	});
                            	var isComplete = $("#window" + n + objId).attr('iscomplete');
                            	if(isComplete=="1" || isComplete==1){
                            		line.setPaintStyle({
                                        lineWidth:3,
                                        strokeStyle:"blue",
                                        joinstyle:"round"
                                    });
                            	}
                            }
                        });
                    }
                } else {
                	var line = jsPlumb.connect({
                    	source:"window"+prePrcsId,
                    	target:nodeId,
                    	anchors:[["Perimeter", { shape:$("#"+"window"+prePrcsId).attr("data-shape"), rotation:null}],
                    	         ["Perimeter", { shape:$("#"+nodeId).attr("data-shape"),rotation:null}]]
                    });
                    var isComplete = $("#"+nodeId).attr('iscomplete');
                	if(isComplete=="1" || isComplete==1){
                		line.setPaintStyle({
                            lineWidth:3,
                            strokeStyle:"blue",
                            joinstyle:"round"
                        });
                	}
                }
            });
        } // end if
    },
    // this is the paint style for the connecting lines..
    connectorPaintStyle : {
        lineWidth:3,
        strokeStyle:"#ec912a",
        joinstyle:"round"
    },
    // .. and this is the hover style.
    connectorHoverStyle : {
        lineWidth:3,
        strokeStyle:"#2e2aF8"
    },
    fnAddChildFlows:function(row){
    	var m = "ProcessManage";
    	var url = "lookupAddProcessRelation";//新增子审批节点
    	var options = {};
    	options.width = 800;
    	options.height = 485;
    	options.mask = true;
    	options.resizable = false;
    	options.maxable = false;
    	options.minable = false;
    	options.param = {row:row};
    	//options.param = {ids:row.ids,key:row.prcsid,choose:row.choose,modelname:row.modelname};
    	$.pdialog.open(TP_APP + '/'+m+'/'+url, "MisOaFlowsSelectFlowUser", "流程节点设计器", options);
    },
    fnAddChildSonFlows:function(row){
    	var m = "ProcessManage";
    	var url = "lookupAddChildSonFlows";//新增子流程节点
    	var options = {};
    	options.width = 800;
    	options.height = 485;
    	options.mask = true;
    	options.resizable = false;
    	options.maxable = false;
    	options.minable = false;
    	options.param = {row:row,step:1};
    	//options.param = {ids:row.ids,key:row.prcsid,choose:row.choose,modelname:row.modelname};
    	$.pdialog.open(TP_APP + '/'+m+'/'+url, "MisOaFlowsSelectFlowUser", "流程节点设计器", options);
    },
    fnEditChildFlows:function(row){
    	var m = "ProcessManage";
    	var width = 800,height = 485;
    	if(row.choose == 3){
    		var url = "lookupAddChildSonFlows";
    	}else if (row.choose == 0){
    		//开始节点，修改名称
    		var url = "lookupEditBeginFlows";
    		width = 500;
    		height = 250;
    	}else{
    		var url = "lookupEditProcessRelation";//修改当前节点
    	}
    	var options = {};
    	options.width = width;
    	options.height = height;
    	options.mask = true;
    	options.resizable = false;
    	options.maxable = false;
    	options.minable = false;
    	options.param = {row:row};
    	$.pdialog.open(TP_APP + '/'+m+'/'+url, "MisOaFlowsSelectFlowUser", "流程节点设计器", options);
    },
    fnAddChildParallel:function(row){
    	var m = "ProcessManage";
    	var url = "lookupAddChildChoose";//新增并行节点
    	var options = {};
    	options.width = 500;
    	options.height = 250;
    	options.mask = true;
    	options.resizable = false;
    	options.maxable = false;
    	options.minable = false;
    	options.param = {ids:row.ids,key:row.prcsid,modelname:row.modelname,choose:4,relation_name:'并行节点'};
    	$.pdialog.open(TP_APP + '/'+m+'/'+url, "lookupAddChildChoose", "并行节点", options);
    },
    fnAddChildChoose:function(row){
        //nothing to do
    	var m = "ProcessManage";
    	var url = "lookupAddChildChoose";//新增判定节点
    	var options = {};
    	options.width = 500;
    	options.height = 250;
    	options.mask = true;
    	options.resizable = false;
    	options.maxable = false;
    	options.minable = false;
    	options.param = {ids:row.ids,key:row.prcsid,modelname:row.modelname,choose:1,relation_name:'判定节点'};
    	$.pdialog.open(TP_APP + '/'+m+'/'+url, "lookupAddChildChoose", "判定节点", options);
    },
    fnAddNoOperationField:function(row){
    	var m = "ProcessManage";
    	var url = "lookupAddNoOperationField";//设置不可操作字段
    	var options = {};
    	options.width = 600;
    	options.height = 500;
    	options.mask = true;
    	options.resizable = false;
    	options.maxable = false;
    	options.minable = false;
    	options.param = {ids:row.ids,modelname:row.modelname,filterwritsetempty:row.filterwritsetempty,filterreadsetempty:row.filterreadsetempty};
    	$.pdialog.open(TP_APP + '/'+m+'/'+url, "lookupAddNoOperationField", "表单字段设置", options);
    },
    fnReplaceFlows:function(row){
    	//nothing to do
    	var m = "CommonFlows";
    	var url = "lookupSelectFlowUser/type/replace";// 默认为选择用户
    	var options = {};
    	options.width = 500;
    	options.height = 475;
    	options.mask = true;
    	options.resizable = false;
    	options.maxable = false;
    	options.minable = false;
    	options.param = {ids:row.ids,key:row.prcsid};
    	$.pdialog.open(TP_APP + '/'+m+'/'+url, "MisOaFlowsSelectFlowUser", "流程审核人选择器", options);
    },
    fnLinkFlows:function(row){
    	//nothing to do
    	var m = "ProcessManage";
    	var url = "lookupLinkFlowUser";// 默认为选择用户
    	var options = {};
    	options.width = 330;
    	options.height = 400;
    	options.mask = true;
    	options.resizable = false;
    	options.maxable = false;
    	options.minable = false;
    	options.param = {ids:row.ids,level:row.level};
    	$.pdialog.open(TP_APP + '/'+m+'/'+url, "MisOaFlowsLinkFlowsUser", "流程审核人连接器", options);
    },
    fnDeleteFlows:function(row){
    	$.ajax({
			type :'POST',
			url : TP_APP + '/ProcessManage/lookupDeleteFlowUser',
			data : {row:row},
			dataType : "json",
			cache : false,
			success : function(json) {
				DWZ.ajaxDone(json);
				if (json.statusCode == DWZ.statusCode.ok) {
					var flow_data = JSON.parse(json.data) || eval("(" + json.data + ")");
			    	var data = flow_data.data;
			    	var conditions = flow_data.condition;
			    	var connectkey = flow_data.connectkey;
					pr.fnInit("prcslist",data,conditions,connectkey);
				}
			},
			error : DWZ.ajaxError
		});
    }
};